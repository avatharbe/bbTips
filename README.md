# bbTips

**Current version:** 2.0.0-rc1

[![Tests](https://github.com/avatharbe/bbTips/actions/workflows/tests.yml/badge.svg)](https://github.com/avatharbe/bbTips/actions/workflows/tests.yml)

bbTips brings WowHead-style hover tooltips to phpBB 3.3 as a set of BBCodes, letting players drop in-line item, spell, and NPC links for World of Warcraft and Diablo 4 that expand into rich, live-updated tooltips when hovered — the same experience the old phpBB 3.0 MOD offered, rebuilt ground-up as a standalone extension.

**bbTips 2.0 requires numeric entity IDs; name-based tags from the old MOD no longer render.** Look up the numeric ID on [wowhead.com](https://www.wowhead.com) (WoW) or the Diablo 4 database (D4) and use that inside the tag — see the examples below.

> **GDPR note:** bbTips loads WowHead's third-party `tooltips.js` to render tooltips in the browser. This is a third-party script call to wowhead.com and may be subject to your jurisdiction's data protection requirements. It can be disabled entirely via the ACP "Enable WowHead tooltip script (third-party)" toggle, in which case the BBCode tags still render as plain links to wowhead.com/the Diablo 4 database, but no third-party script is loaded.

## Requirements

- phpBB >= 3.3.0
- PHP >= 8.1.0

## Installation

1. Copy the `bbtips` folder to `/ext/avathar/bbtips/`.
2. Navigate in the ACP to `Customise -> Manage extensions`.
3. Look for `bbTips` under Disabled Extensions and click `Enable`.

## Uninstall

1. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
2. Find `bbTips` under Enabled Extensions and click `Disable`.
3. To permanently uninstall, click `Delete Data` and then delete the `/ext/avathar/bbtips` folder.

## BBCode tags

All tags take a **numeric entity ID** as their content. World of Warcraft tags additionally accept optional attributes for domain, enchant, gems, and set pieces; the `[itemico]` tag also accepts a `size` attribute.

### World of Warcraft

| Tag             | Example                          | Renders                                   |
| --------------- | --------------------------------- | ------------------------------------------ |
| `[item]`        | `[item]50468[/item]`              | Item tooltip link                          |
| `[itemico]`     | `[itemico size=medium]50468[/itemico]` | Item tooltip as an icon (optional `size`: small / medium / large) |
| `[spell]`       | `[spell]17[/spell]`               | Spell/ability tooltip link                 |
| `[quest]`       | `[quest]26385[/quest]`            | Quest tooltip link                         |
| `[craft]`       | `[craft]2477[/craft]`             | Crafting recipe tooltip link (spell-backed) |
| `[achievement]` | `[achievement]6[/achievement]`    | Achievement tooltip link                   |
| `[itemset]`     | `[itemset]861[/itemset]`          | Item set tooltip link                      |
| `[npc]`         | `[npc]15550[/npc]`                | NPC tooltip link                           |

WoW tags also accept optional attributes to fine-tune the tooltip, e.g. a specific game domain, an enchant, socketed gems, or a set-piece count:

```
[item domain=wotlk ench=3825 gems=40133]50468[/item]
```

- `domain` — overrides the board-wide default WoW domain for this tag only. Examples: `classic`, `ptr`, `de`, `ru`, `de.classic`.
- `ench` — enchant spell ID applied to the item tooltip.
- `gems` — one or more gem item IDs (colon-separated when more than one, e.g. `gems=40133:40132`).
- `pcs` — number of set pieces equipped, for set-bonus display on `[itemset]` style tooltips.

### Diablo 4

| Tag         | Example                     | Renders                    |
| ----------- | ---------------------------- | --------------------------- |
| `[d4item]`  | `[d4item]444429[/d4item]`   | Diablo 4 item tooltip link  |
| `[d4skill]` | `[d4skill]12345[/d4skill]`  | Diablo 4 skill tooltip link |

## Inserting tags from the editor

You don't have to type the brackets by hand. When you write a post, bbTips adds a **Game tooltips** helper to the editor toolbar:

- **`[item]` and `[spell]` buttons** — click to insert an empty tag skeleton with the cursor placed between the tags; type the numeric ID and (optionally) attributes.
- **A "More tooltips…" dropdown** — lists the remaining enabled tags grouped by game (World of Warcraft / Diablo 4). Selecting one inserts its skeleton and shows a short description of the tag.
- **A hint line** under the "BBCode is ON" status, so the tags are discoverable.

Which buttons and dropdown entries appear depends on the provider and per-tag toggles in `ACP -> bbTips -> bbTips settings`. The helper only inserts the BBCode text — it works even when the third-party tooltip script is disabled.

## ACP settings

`ACP -> bbTips -> bbTips settings` exposes:

- **Game providers** — enable/disable the WoW and Diablo 4 tag providers as a whole. Disabling a provider stops its BBCode tags from being registered at all.
- **BBCode tags** — per-tag on/off toggles (`[item]`, `[itemico]`, `[spell]`, `[quest]`, `[craft]`, `[achievement]`, `[itemset]`, `[npc]`, `[d4item]`, `[d4skill]`), so a board can enable only the tags it wants.
- **Behavior**
  - **Default WoW domain** — the WowHead domain used when a `[item]`-family tag doesn't specify its own `domain` attribute. Blank = retail (`www.wowhead.com`). Examples: `classic`, `ptr`, `de`, `ru`, `de.classic`.
  - **Load tooltip script on** — restrict `tooltips.js` loading to forum posts/topics only, or load it on all board pages.
  - **Enable WowHead tooltip script (third-party)** — the GDPR opt-out switch described above.
- **Link display** — color tooltip links by item quality, show a small icon next to tooltip links, and/or replace the link text with the live item/spell name fetched by WowHead's script.

## License

[GNU General Public License v2](http://opensource.org/licenses/gpl-2.0.php)

## Links

- [Issue Tracker](https://github.com/avatharbe/bbTips/issues)
