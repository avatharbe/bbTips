# Changelog

## Unreleased

  - [NEW] Posting-editor insertion helper — a "Game tooltips" toolbar control (always-visible `[item]`/`[spell]` buttons plus a game-grouped "More tooltips…" dropdown with a live per-tag help line) and a hint line under "BBCode is ON", so users can insert tooltip tags without typing brackets. Tag parsing is unchanged; visibility follows the existing per-provider/per-tag toggles.
  - [NEW] Shared `tag_gate` enablement service and `toolbar_builder` — the per-provider/per-tag toggles now drive both s9e BBCode registration and the editor helper from a single source of truth.
  - [FIX] README: the attributed `[item]` example used `domain=classic` with a Wrath-era item/enchant/gem that 404s in WowHead's classic database, so the tooltip never resolved; corrected to `domain=wotlk`.

## 2.0.0-rc2 — ACP settings page fix

  - [FIX] ACP settings page rendered as bare HTML with no ACP layout/CSS — the template was missing the required `overall_header.html`/`overall_footer.html` includes for extension ACP module pages.
  - [CHG] Rewrote the settings form to canonical phpBB ACP markup (`fieldset`/`dl`-`dt`-`dd`, radio Yes/No toggles with `class="radio"`, `submit-buttons`), matching core ACP styling.
  - [NEW] Per-setting explain text on the left of each option (with usage examples for every BBCode tag), translated across en/de/fr/nl.

## 2.0.0-rc1 — ground-up phpBB 3.3 rewrite

bbTips 2.0 is a complete rewrite of the extension as a standalone phpBB 3.3 extension (`avathar/bbtips`), replacing the phpBB 3.0-era scrape-and-cache MOD with a provider-based architecture and live third-party tooltips.

  - [NEW] Provider framework (`tooltip_provider_interface`, `provider_registry`, `abstract_wowhead_provider`) — each game is a self-contained provider exposing its BBCode tag specs and a `build_link()` contract for other extensions (e.g. bbGuild) to build tooltip links programmatically.
  - [NEW] World of Warcraft provider — `[item]`, `[itemico]`, `[spell]`, `[quest]`, `[craft]`, `[achievement]`, `[itemset]`, `[npc]` tags, with optional `domain`, `ench`, `gems`, and `pcs` attributes, plus a `size` attribute on `[itemico]`.
  - [NEW] Diablo 4 provider — `[d4item]` and `[d4skill]` tags linking into the Diablo 4 database.
  - [NEW] WowHead `tooltips.js` runtime loader (`runtime_listener`) — injects the official WowHead tooltip script on demand instead of scraping/caching tooltip HTML server-side like the legacy MOD; scope is configurable (all pages vs. forum posts only) and can be disabled outright for GDPR/third-party-script reasons.
  - [NEW] s9e `text_formatter` BBCode registration (`bbcode_listener`) — tags are registered directly against the s9e configurator via `addCustom()`, with per-tag and per-provider ACP toggles and a defensive `try/catch` so a malformed spec can never break board-wide post rendering.
  - [NEW] ACP settings page (`ACP -> bbTips -> bbTips settings`) — provider toggles, per-tag toggles, default WoW domain, tooltip-script scope, GDPR opt-out, and link-display options (color by quality, icon, live rename).
  - [NEW] Install migration seeding all config defaults and registering the ACP category/module.
  - [NEW] `linker` service exposing `build_link()` to other extensions (e.g. bbGuild) without depending on bbTips' internal BBCode wiring.
  - [BC] Numeric entity IDs are now required for every tag; the old MOD's name-based tags no longer render (see README).
  - [NEW] German, French, and Dutch translations for extension info and ACP language files (English fallback where not yet fully localized).
