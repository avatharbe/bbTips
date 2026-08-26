# bbTips posting-editor insertion helper — design

**Date:** 2026-07-27
**Status:** Approved for planning
**Extension:** `avathar/bbtips`

## Problem

bbTips tags (`[item]`, `[spell]`, `[quest]`, …) must currently be typed by hand into a
post, brackets and all. Two problems follow:

1. **Discoverability** — nothing in the posting UI signals that these tags exist. A user
   who does not already know about bbTips will never discover that `[spell]17[/spell]`
   renders a live tooltip.
2. **Friction / correctness** — hand-typing brackets (and optional attributes) is
   error-prone. The attributes in particular are easy to get wrong (see the README
   `domain=classic` example that paired a Wrath item with the vanilla domain and so never
   resolved).

This feature adds a posting-toolbar insertion helper so users can insert the tags without
typing brackets, and so the tags are visibly advertised in the editor.

## Principle: convenience layer only, parsing untouched

Tag **parsing stays exactly where it is** — registered at runtime through s9e in
`bbcode_listener` on `core.text_formatter_s9e_configure_after`. This is what provides the
optional `domain` / `ench` / `gems` / `pcs` logic and the board-default-domain fallback.

**We deliberately do NOT create rows in the `phpbb_bbcodes` (ACP custom BBCode) table.**
phpBB registers ACP-table BBCodes into the s9e configurator at
`phpbb/textformatter/s9e/factory.php:298` — *before* the
`core.text_formatter_s9e_configure_after` event (line 375) where bbTips registers its rich
tags. `bbcode_listener` guards with
`if (isset($configurator->tags[strtoupper($tag['bbcode'])])) { continue; }`, so an
ACP-table `item` would win and bbTips would skip its own registration — silently dropping
the conditional-attribute XSL logic board-wide. The ACP HTML-replacement format (single
static template with token substitution) cannot express that logic anyway, so the ACP
table is strictly less capable and must not be the source of truth.

The helper therefore mirrors phpBB's ACP three-part BBCode model, but keeps all three parts
owned by the extension:

| ACP part          | bbTips home                                   | Status              |
| ----------------- | --------------------------------------------- | ------------------- |
| usage             | provider `tag_spec()` s9e usage string        | exists              |
| HTML replacement  | provider `tag_spec()` s9e template (XSL)      | exists              |
| **help line**     | **new: per-tag help language key**            | **added here**      |

## Goals

- Insert any enabled bbTips tag from the posting toolbar without typing brackets.
- Make the feature's existence visible at rest in the editor.
- Zero change to parsing behaviour and no new parsing source of truth.
- Work across the actively-used styles (prosilver, pbwow3, pbtech, we_clearblue) from a
  single set of `styles/all` files.

## Non-goals

- No attribute wizard. Inserted tags are **skeletons only** (`[item]│[/item]` with the
  caret between the tags); the user types the numeric ID and any attributes. (Confirmed
  decision — the guided-dialog option was declined.)
- No `phpbb_bbcodes` rows / ACP-table integration.
- No new ACP on/off toggle for the helper — the existing per-provider
  (`bbtips_provider_*`) and per-tag (`bbtips_tag_*`) switches already gate what appears.
- No override of the core BBCode-guide controller (`faq.php?mode=bbcode`); it exposes no
  clean "append a section" hook, and the status-line hint delivers the same value.

## Discoverability — three combined levers

1. **Headline buttons.** Up to two "primary" enabled tags render as always-visible labeled
   toolbar buttons (default `[item]`, `[spell]`). Their at-rest visibility is what
   advertises the feature — text alone is skimmed past.
2. **"more ▾" dropdown.** All remaining enabled tags, grouped by game via `optgroup`
   (`get_game_name()`), with a **live help-text span** that updates to the selected tag's
   help line on `change`. (Native `<option title>` tooltips were rejected as unreliable on
   mobile / some browsers; a DOM help span renders identically everywhere and is
   screen-reader accessible.)
3. **Hint line** injected on `posting_editor_bbcode_status_after`, directly under phpBB's
   "BBCode is ON" status — the canonical spot where users already read about available
   BBCode. Example: *"Game tooltips: `[item]` `[spell]` `[quest]` … — pick from the
   toolbar above."*

### Primary-tag selection rule (robust to config)

Each provider declares an ordered `primary` list of its own tag keys
(WoW: `['item', 'spell']`; Diablo 4: `['d4item', 'd4skill']`). The toolbar builder:

1. Collects enabled tags only (enabled providers × enabled `bbtips_tag_*`), preserving
   provider order.
2. Walks each enabled provider's `primary` list and promotes the first **≤ 2 total**
   still-enabled primary tags to headline buttons.
3. If step 2 yields nothing (e.g. `[item]`/`[spell]` both disabled, or WoW disabled and
   only Diablo 4 enabled), promotes the first enabled tag(s) instead, capped at 2.
4. Everything not promoted goes into the "more ▾" dropdown.

This guarantees sensible headline buttons for any provider/tag combination, including a
Diablo-4-only board.

## Components

1. **Provider tag spec gains a help line.**
   `tooltip_provider_interface` / each `get_tags()` entry gains a `help` field holding a
   **language key** (e.g. `BBTIPS_HELP_ITEM`). Providers also expose an ordered `primary`
   tag-key list. `wow_provider` and `diablo4_provider` fill both in. The existing
   `bbcode`/`usage`/`template` fields are unchanged, so `bbcode_listener` keeps working
   as-is.

2. **New front-end language file** `language/{en,de,fr,nl}/bbtips.php`.
   Holds: the dropdown label (`BBTIPS_TOOLBAR_LABEL`, e.g. "Game tooltips"), the "more"
   label, the per-tag help strings (`BBTIPS_HELP_*`), an aria label for the select, and
   the hint-line text (`BBTIPS_TOOLBAR_HINT`). English is authoritative; de/fr/nl mirror
   the four locales bbTips already ships.

3. **New posting listener** `event/posting_listener.php` (`EventSubscriberInterface`).
   Subscribes to a posting-scoped event so it fires only where the editor renders. It:
   - reuses the *same* enablement filtering as `bbcode_listener` (factor the shared
     provider-id + `tag_enabled()` logic so the two listeners can't drift),
   - builds the headline/overflow split via the primary-tag rule,
   - loads the `bbtips` language file,
   - assigns template vars (labels, hint) and `assign_block_vars` for headline buttons and
     for dropdown options (each option carrying its `open`, `close`, `help` values).

4. **New template-event files** under `styles/all/template/event/`:
   - `posting_buttons_after.html` (hooks `posting_editor_buttons_after`) — renders the
     headline buttons, the "more ▾" `<select>` with `optgroup`s, and the help `<span>`.
   - `posting_editor_bbcode_status_after.html` — renders the hint line.
   `styles/all` means one set of files covers every installed style.

5. **Client JS** (inline in the template-event file, guarded to define once):
   - headline button: `onclick="bbfontstyle('[item]', '[/item]')"` (phpBB's helper places
     the caret between the tags and wraps any current selection),
   - dropdown `onchange`: read the selected `<option>`'s `data-open` / `data-close` /
     `data-help`, call `bbfontstyle(open, close)`, write `data-help` into the help span,
     then reset the select to index 0.
   No separate JS help map is needed — each option carries its own data.

### Rendered markup (illustrative)

```html
<!-- headline buttons -->
<button type="button" class="button button-secondary bbtips-tag"
        onclick="bbfontstyle('[item]','[/item]');" title="{help}">item</button>
<button type="button" class="button button-secondary bbtips-tag"
        onclick="bbfontstyle('[spell]','[/spell]');" title="{help}">spell</button>

<!-- overflow dropdown + live help -->
<select id="bbtips-more" class="bbtips-more" onchange="bbtips_insert(this);"
        aria-label="{L_BBTIPS_ARIA}">
  <option value="">{L_BBTIPS_MORE}…</option>
  <optgroup label="World of Warcraft">
    <option data-open="[quest]" data-close="[/quest]"
            data-help="…">quest</option>
    …
  </optgroup>
  <optgroup label="Diablo 4">
    <option data-open="[d4item]" data-close="[/d4item]" data-help="…">d4item</option>
    …
  </optgroup>
</select>
<span id="bbtips-help" class="bbtips-help" aria-live="polite"></span>
```

## Data flow

posting page render → `posting_listener` fires → filters enabled providers/tags → applies
primary-tag rule → assigns headline + overflow block vars + labels/hint → template events
render toolbar controls and hint line → user clicks a button or picks from the dropdown →
`bbfontstyle()` inserts the skeleton into the textarea → (dropdown) help span updates.
Parsing on submit is unchanged (s9e).

## Config gating

- Helper visibility follows existing config: a tag appears only when its provider
  (`bbtips_provider_*`) **and** its tag toggle (`bbtips_tag_*`) are on.
- The helper is **independent of the GDPR runtime toggle** (`bbtips_runtime_enabled`): it
  only inserts BBCode text, so disabling the third-party tooltip script must not hide the
  editor helper.
- No new config keys, so **no migration change** is required.

## Internationalisation

New keys added to `language/{en,de,fr,nl}/bbtips.php`:
`BBTIPS_TOOLBAR_LABEL`, `BBTIPS_TOOLBAR_MORE`, `BBTIPS_TOOLBAR_HINT`, `BBTIPS_TOOLBAR_ARIA`,
and one `BBTIPS_HELP_<TAG>` per tag (`ITEM`, `ITEMICO`, `SPELL`, `QUEST`, `CRAFT`,
`ACHIEVEMENT`, `ITEMSET`, `NPC`, `D4ITEM`, `D4SKILL`). English authoritative; other three
locales translated.

## Testing

- **Unit** — a `toolbar_builder` (or equivalent) test following existing
  `wow_provider_test` / `provider_registry_test` patterns:
  - returns only enabled tags for a given config,
  - headline/overflow split honours the primary-tag rule, including the fallbacks
    (primaries disabled → first enabled promoted; Diablo-4-only board),
  - each entry carries the right `open`/`close`/`help` values and provider grouping.
- **Functional/manual** — load a real posting page; confirm headline buttons insert
  skeletons with the caret positioned, the dropdown inserts + updates the help span, and
  the hint line renders under "BBCode is ON". Verify across prosilver + pbwow3/pbtech.

## File manifest

- `provider/tooltip_provider_interface.php` — add `help` per tag + `primary` list (edit)
- `provider/wow_provider.php` — populate help keys + `primary` (edit)
- `provider/diablo4_provider.php` — populate help keys + `primary` (edit)
- `event/bbcode_listener.php` — extract shared enablement filtering (edit/refactor)
- `event/posting_listener.php` — new toolbar listener (new)
- `service/…` toolbar builder + shared filter helper (new; exact placement decided in plan)
- `config/services.yml` — register `posting_listener` (+ builder) (edit)
- `language/{en,de,fr,nl}/bbtips.php` — new front-end strings (new)
- `styles/all/template/event/posting_buttons_after.html` (new)
- `styles/all/template/event/posting_editor_bbcode_status_after.html` (new)
- `tests/…` — toolbar builder unit test (new)

## Risks / open items

- **Exact posting-scoped event** for `posting_listener` (e.g.
  `core.posting_modify_template_vars`) to be pinned in the implementation plan; it must
  cover both the full editor and quick reply, and must not fire board-wide.
- **Toolbar layout on narrow/mobile** — headline buttons + "more ▾" must wrap cleanly on
  pbwow3/pbtech; verify during functional testing.
- **`bbfontstyle` availability** — confirm the helper is present on every page that renders
  the toolbar (it ships with phpBB's `editor.js`, loaded with the posting form).

## Out of scope (possible follow-ups)

- Attribute wizard / guided dialog for `domain`/`ench`/`gems`/`pcs`.
- A bbTips section in the BBCode guide page (would require overriding the core controller).
- Per-board customisation of which tags are "headline".
