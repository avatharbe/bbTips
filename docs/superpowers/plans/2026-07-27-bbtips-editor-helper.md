# bbTips posting-editor insertion helper — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a posting-toolbar helper that lets users insert bbTips BBCode tags without typing brackets, and advertises the tags in the editor.

**Architecture:** A pure editor-convenience layer. Tag *parsing* is untouched (still s9e at `core.text_formatter_s9e_configure_after`). A shared enablement gate feeds both the existing `bbcode_listener` and a new `toolbar_builder`; a new posting listener assigns the builder's output to the template on `core.posting_modify_bbcode_status`; two `styles/all` template-event files render headline buttons + a "more ▾" dropdown (with a live help span) + a hint line under "BBCode is ON".

**Tech Stack:** PHP 8.1+, phpBB 3.3 extension (Symfony DI + event listeners, Twig templates), s9e/TextFormatter (unchanged), PHPUnit 9.

## Global Constraints

- PHP >= 8.1.0; phpBB >= 3.3.0 (copy from `composer.json`).
- Coding standards: phpBB sniffs — tabs only, Allman/BSD braces, type-hint spacing. `php -l` is NOT sufficient; CI runs phpcs.
- All files chmod 644, dirs 755 (EPV "Check executable files").
- No new `phpbb_config` keys and **no migration changes** — helper visibility reuses existing `bbtips_provider_*` / `bbtips_tag_*` config.
- **Never** write to `phpbb_bbcodes` (ACP BBCode table) — it collides with the s9e runtime tags and silently strips their conditional-attribute logic.
- Insertion is skeleton-only (`[item]│[/item]`); no attribute wizard.
- One set of `styles/all` template files must cover prosilver + pbwow3 + pbtech + we_clearblue.
- Ship strings for all four bundled locales: `en`, `de`, `fr`, `nl` (English authoritative).
- Work in the **working copy** at `/Users/Andreas/Sites/avathar/forum/ext/avathar/bbtips`. This directory is **not** a git repo — do not run `git` here.

### Version control (project workflow)

The working copy is not under git. The bbTips git repo is a separate dir; edit + test in the working copy, then rsync into the repo and commit there. Each task's "Commit" step is a checkpoint — run this procedure (only when the user has authorised committing; otherwise implement/test and leave committing to them):

```bash
# sync working copy -> git repo (never copy .git)
rsync -a --delete --exclude='.git' \
  /Users/Andreas/Sites/avathar/forum/ext/avathar/bbtips/ \
  /Users/Andreas/development/PHP/phpbb33_extensions/avathar/bbtips/
cd /Users/Andreas/development/PHP/phpbb33_extensions/avathar/bbtips
git add <files listed in the task's Commit step>
git commit -m "<message from the task's Commit step>"
```

The `git add … && git commit …` snippet inside each task states the files and message; run it in the **repo dir** after the rsync above, not in the working copy.

### Local test runner (verified working)

The phpBB test framework is absent locally, so `phpunit.xml.dist` (which points at
`../../../tests/bootstrap.php`) cannot run here. For the pure-unit tests in this plan, use
the standalone runner below — it was verified against the existing suite (5/5 pass). CI runs
the full framework suite normally; do not change `phpunit.xml.dist`.

- PHPUnit phar: `/private/tmp/claude-501/-Users-Andreas-Sites-avathar-forum-ext-avathar/bdf341cd-61bc-46d2-b3e1-c559e8dba005/scratchpad/phpunit9.phar`
- Local bootstrap: `/private/tmp/claude-501/-Users-Andreas-Sites-avathar-forum-ext-avathar/bdf341cd-61bc-46d2-b3e1-c559e8dba005/scratchpad/local_bootstrap.php`
  (autoloads `avathar\bbtips\` from the working copy and `phpbb\` from `forum/phpbb/`; silences `E_DEPRECATED`.)

Run commands in this plan use these shell variables (set once per shell):

```bash
cd /Users/Andreas/Sites/avathar/forum/ext/avathar/bbtips
SP=/private/tmp/claude-501/-Users-Andreas-Sites-avathar-forum-ext-avathar/bdf341cd-61bc-46d2-b3e1-c559e8dba005/scratchpad
PU="php $SP/phpunit9.phar --bootstrap $SP/local_bootstrap.php"
```

---

## File structure

**New files**
- `service/tag_gate.php` — shared enablement checks (provider ids + per-tag toggle) used by both listeners/builders.
- `service/toolbar_builder.php` — turns enabled providers/tags into headline buttons + grouped dropdown options, help text resolved.
- `event/posting_listener.php` — subscribes to `core.posting_modify_bbcode_status`; loads lang; assigns template vars.
- `styles/all/template/event/posting_buttons_after.html` — headline buttons + dropdown + help span + JS.
- `styles/all/template/event/posting_editor_bbcode_status_after.html` — hint line.
- `language/en/bbtips.php`, `language/de/bbtips.php`, `language/fr/bbtips.php`, `language/nl/bbtips.php` — front-end strings.
- `tests/service/tag_gate_test.php`, `tests/service/toolbar_builder_test.php`.

**Modified files**
- `provider/tooltip_provider_interface.php` — add `get_primary_tags(): array`; document `help` key in `get_tags()`.
- `provider/abstract_wowhead_provider.php` — default `get_primary_tags(): array { return []; }`.
- `provider/wow_provider.php` — add `help` to each tag; override `get_primary_tags()`.
- `provider/diablo4_provider.php` — add `help` to each tag; override `get_primary_tags()`.
- `event/bbcode_listener.php` — use `tag_gate` instead of private enablement methods.
- `config/services.yml` — register `tag_gate`, `toolbar_builder`, `posting_listener`.

---

## Task 1: Shared enablement gate (`tag_gate`)

Extract the enablement logic currently private in `bbcode_listener` into a reusable service so the toolbar and the bbcode registration can never drift.

**Files:**
- Create: `service/tag_gate.php`
- Test: `tests/service/tag_gate_test.php`
- Modify (Task 5 wires the listener; here only create + test the service)

**Interfaces:**
- Consumes: `phpbb\config\config`.
- Produces:
  - `class avathar\bbtips\service\tag_gate`
  - `__construct(\phpbb\config\config $config)`
  - `provider_enabled(string $id): bool` — reads `bbtips_provider_<id>` (int-cast truthy).
  - `tag_enabled(string $bbcode): bool` — `bbtips_tag_<bbcode>`; **absent key => enabled** (matches current `bbcode_listener` behaviour).

- [ ] **Step 1: Write the failing test**

```php
<?php
namespace avathar\bbtips\tests\service;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\service\tag_gate;

class tag_gate_test extends TestCase
{
	private function config(array $values): \phpbb\config\config
	{
		// phpbb\config\config implements ArrayAccess over a plain array.
		return new \phpbb\config\config($values);
	}

	public function test_provider_enabled_reads_provider_key(): void
	{
		$gate = new tag_gate($this->config(['bbtips_provider_wow' => '1', 'bbtips_provider_diablo4' => '0']));
		$this->assertTrue($gate->provider_enabled('wow'));
		$this->assertFalse($gate->provider_enabled('diablo4'));
	}

	public function test_tag_enabled_defaults_true_when_key_absent(): void
	{
		$gate = new tag_gate($this->config([]));
		$this->assertTrue($gate->tag_enabled('item'));
	}

	public function test_tag_enabled_false_when_key_zero(): void
	{
		$gate = new tag_gate($this->config(['bbtips_tag_item' => '0']));
		$this->assertFalse($gate->tag_enabled('item'));
	}
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `$PU tests/service/tag_gate_test.php`
Expected: FAIL — `Class "avathar\bbtips\service\tag_gate" does not exist`.

- [ ] **Step 3: Write minimal implementation**

```php
<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\service;

use phpbb\config\config;

/**
 * Single source of truth for which providers and tags are enabled.
 * Shared by the s9e bbcode registration and the posting-toolbar helper.
 */
class tag_gate
{
	/** @var config */
	protected $config;

	public function __construct(config $config)
	{
		$this->config = $config;
	}

	public function provider_enabled(string $id): bool
	{
		return (bool) (int) $this->config['bbtips_provider_' . $id];
	}

	public function tag_enabled(string $bbcode): bool
	{
		$key = 'bbtips_tag_' . $bbcode;

		return !isset($this->config[$key]) || (int) $this->config[$key] === 1;
	}
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `$PU tests/service/tag_gate_test.php`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add service/tag_gate.php tests/service/tag_gate_test.php
git commit -m "feat(bbtips): add shared tag_gate enablement service"
```

---

## Task 2: Provider help lines + primary tags

Give each tag a help-line language key and let each provider declare its ordered "primary" tags (for headline buttons). Existing `bbcode`/`usage`/`template` keys are unchanged, so `bbcode_listener` keeps working.

**Files:**
- Modify: `provider/tooltip_provider_interface.php`
- Modify: `provider/abstract_wowhead_provider.php`
- Modify: `provider/wow_provider.php`
- Modify: `provider/diablo4_provider.php`
- Test: `tests/provider/wow_provider_test.php` (extend), `tests/provider/diablo4_provider_test.php` (extend)

**Interfaces:**
- Consumes: nothing new.
- Produces:
  - `tooltip_provider_interface::get_primary_tags(): array` — ordered list of bbcode keys (e.g. `['item','spell']`).
  - Each `get_tags()` entry now also has `help` => language key string, format `BBTIPS_HELP_<UPPER_BBCODE>`.
  - `abstract_wowhead_provider::get_primary_tags(): array { return []; }` (default).
  - `wow_provider::get_primary_tags()` => `['item', 'spell']`.
  - `diablo4_provider::get_primary_tags()` => `['d4item', 'd4skill']`.

- [ ] **Step 1: Write the failing tests**

Add to `tests/provider/wow_provider_test.php`:

```php
	public function test_tags_carry_help_keys(): void
	{
		foreach ($this->p->get_tags() as $tag)
		{
			$this->assertArrayHasKey('help', $tag);
			$this->assertSame('BBTIPS_HELP_' . strtoupper($tag['bbcode']), $tag['help']);
		}
	}

	public function test_primary_tags_are_item_and_spell(): void
	{
		$this->assertSame(['item', 'spell'], $this->p->get_primary_tags());
	}
```

Add to `tests/provider/diablo4_provider_test.php`:

```php
	public function test_tags_carry_help_keys(): void
	{
		foreach ((new \avathar\bbtips\provider\diablo4_provider())->get_tags() as $tag)
		{
			$this->assertSame('BBTIPS_HELP_' . strtoupper($tag['bbcode']), $tag['help']);
		}
	}

	public function test_primary_tags(): void
	{
		$this->assertSame(['d4item', 'd4skill'], (new \avathar\bbtips\provider\diablo4_provider())->get_primary_tags());
	}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `$PU tests/provider/wow_provider_test.php tests/provider/diablo4_provider_test.php`
Expected: FAIL — missing `help` key / undefined method `get_primary_tags`.

- [ ] **Step 3: Implement**

In `provider/tooltip_provider_interface.php`, update the `get_tags()` docblock return type and add the method:

```php
	/**
	 * BBCode tag specs for s9e addCustom().
	 * @return array<int, array{bbcode:string, usage:string, template:string, help:string}>
	 */
	public function get_tags(): array;

	/**
	 * Ordered list of this provider's bbcode keys to surface as headline
	 * toolbar buttons, most important first. Empty = no headline preference.
	 * @return string[]
	 */
	public function get_primary_tags(): array;
```

In `provider/abstract_wowhead_provider.php`, add the default (place after `get_runtime_asset()`):

```php
	public function get_primary_tags(): array
	{
		return [];
	}
```

In `provider/wow_provider.php`:
- In `tag_spec()`, add the help key to the returned array. Change the final `return` to:

```php
		return ['bbcode' => $bbcode, 'usage' => $usage, 'template' => $template, 'help' => 'BBTIPS_HELP_' . $B];
```

  (`$B` is already `strtoupper($bbcode)` in that method.)
- Add the override:

```php
	public function get_primary_tags(): array
	{
		return ['item', 'spell'];
	}
```

In `provider/diablo4_provider.php`, update `get_tags()` to include help, and add the override:

```php
	public function get_tags(): array
	{
		$tags = [];
		foreach (self::TYPES as $bbcode => $seg)
		{
			$B = strtoupper($bbcode);
			$usage = "[$B]{UINT}[/$B]";
			$template = "<a href=\"https://www.wowhead.com/diablo-4/$seg/{@content}\"><xsl:apply-templates/></a>";
			$tags[] = ['bbcode' => $bbcode, 'usage' => $usage, 'template' => $template, 'help' => 'BBTIPS_HELP_' . $B];
		}
		return $tags;
	}

	public function get_primary_tags(): array
	{
		return ['d4item', 'd4skill'];
	}
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `$PU tests/provider/wow_provider_test.php tests/provider/diablo4_provider_test.php`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add provider/ tests/provider/
git commit -m "feat(bbtips): add help-line keys and primary-tag lists to providers"
```

---

## Task 3: Front-end language files

Add the strings the toolbar and hint line need, in all four locales.

**Files:**
- Create: `language/en/bbtips.php`, `language/de/bbtips.php`, `language/fr/bbtips.php`, `language/nl/bbtips.php`

**Interfaces:**
- Produces (keys, used by Tasks 4/5 template + builder):
  `BBTIPS_TOOLBAR_LABEL`, `BBTIPS_TOOLBAR_MORE`, `BBTIPS_TOOLBAR_ARIA`, `BBTIPS_TOOLBAR_HINT`,
  and `BBTIPS_HELP_ITEM`, `BBTIPS_HELP_ITEMICO`, `BBTIPS_HELP_SPELL`, `BBTIPS_HELP_QUEST`,
  `BBTIPS_HELP_CRAFT`, `BBTIPS_HELP_ACHIEVEMENT`, `BBTIPS_HELP_ITEMSET`, `BBTIPS_HELP_NPC`,
  `BBTIPS_HELP_D4ITEM`, `BBTIPS_HELP_D4SKILL`.

This task has no unit test (language files are excluded from coverage and are static data). Verify by `php -l` and by the builder test in Task 4, which resolves these keys.

- [ ] **Step 1: Create `language/en/bbtips.php`**

```php
<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'BBTIPS_TOOLBAR_LABEL'	=> 'Game tooltips',
	'BBTIPS_TOOLBAR_MORE'	=> 'More tooltips…',
	'BBTIPS_TOOLBAR_ARIA'	=> 'Insert a game tooltip tag',
	'BBTIPS_TOOLBAR_HINT'	=> 'Game tooltips: [item] [spell] [quest] and more — use the Game tooltips buttons above.',

	'BBTIPS_HELP_ITEM'			=> 'WoW item tooltip link — accepts domain, ench, gems',
	'BBTIPS_HELP_ITEMICO'		=> 'WoW item shown as an icon — accepts a size attribute',
	'BBTIPS_HELP_SPELL'			=> 'WoW spell / ability tooltip link',
	'BBTIPS_HELP_QUEST'			=> 'WoW quest tooltip link',
	'BBTIPS_HELP_CRAFT'			=> 'WoW crafting recipe tooltip link',
	'BBTIPS_HELP_ACHIEVEMENT'	=> 'WoW achievement tooltip link',
	'BBTIPS_HELP_ITEMSET'		=> 'WoW item-set tooltip link — accepts a pcs attribute',
	'BBTIPS_HELP_NPC'			=> 'WoW NPC tooltip link',
	'BBTIPS_HELP_D4ITEM'		=> 'Diablo 4 item tooltip link',
	'BBTIPS_HELP_D4SKILL'		=> 'Diablo 4 skill tooltip link',
));
```

- [ ] **Step 2: Create the de/fr/nl files**

Same structure and keys; translate the values. Use these translations:

`language/de/bbtips.php` values:
- `BBTIPS_TOOLBAR_LABEL` => `'Spiel-Tooltips'`
- `BBTIPS_TOOLBAR_MORE` => `'Weitere Tooltips…'`
- `BBTIPS_TOOLBAR_ARIA` => `'Einen Spiel-Tooltip-Tag einfügen'`
- `BBTIPS_TOOLBAR_HINT` => `'Spiel-Tooltips: [item] [spell] [quest] und mehr — nutze die Spiel-Tooltip-Schaltflächen oben.'`
- `BBTIPS_HELP_ITEM` => `'WoW-Gegenstand-Tooltip — akzeptiert domain, ench, gems'`
- `BBTIPS_HELP_ITEMICO` => `'WoW-Gegenstand als Symbol — akzeptiert ein size-Attribut'`
- `BBTIPS_HELP_SPELL` => `'WoW-Zauber-/Fähigkeits-Tooltip'`
- `BBTIPS_HELP_QUEST` => `'WoW-Quest-Tooltip'`
- `BBTIPS_HELP_CRAFT` => `'WoW-Rezept-Tooltip'`
- `BBTIPS_HELP_ACHIEVEMENT` => `'WoW-Erfolg-Tooltip'`
- `BBTIPS_HELP_ITEMSET` => `'WoW-Gegenstandsset-Tooltip — akzeptiert ein pcs-Attribut'`
- `BBTIPS_HELP_NPC` => `'WoW-NPC-Tooltip'`
- `BBTIPS_HELP_D4ITEM` => `'Diablo-4-Gegenstand-Tooltip'`
- `BBTIPS_HELP_D4SKILL` => `'Diablo-4-Fähigkeit-Tooltip'`

`language/fr/bbtips.php` values:
- `BBTIPS_TOOLBAR_LABEL` => `'Infobulles de jeu'`
- `BBTIPS_TOOLBAR_MORE` => `'Plus d’infobulles…'`
- `BBTIPS_TOOLBAR_ARIA` => `'Insérer une balise d’infobulle de jeu'`
- `BBTIPS_TOOLBAR_HINT` => `'Infobulles de jeu : [item] [spell] [quest] et plus — utilisez les boutons Infobulles de jeu ci-dessus.'`
- `BBTIPS_HELP_ITEM` => `'Infobulle d’objet WoW — accepte domain, ench, gems'`
- `BBTIPS_HELP_ITEMICO` => `'Objet WoW en icône — accepte un attribut size'`
- `BBTIPS_HELP_SPELL` => `'Infobulle de sort / capacité WoW'`
- `BBTIPS_HELP_QUEST` => `'Infobulle de quête WoW'`
- `BBTIPS_HELP_CRAFT` => `'Infobulle de recette WoW'`
- `BBTIPS_HELP_ACHIEVEMENT` => `'Infobulle de haut fait WoW'`
- `BBTIPS_HELP_ITEMSET` => `'Infobulle d’ensemble d’objets WoW — accepte un attribut pcs'`
- `BBTIPS_HELP_NPC` => `'Infobulle de PNJ WoW'`
- `BBTIPS_HELP_D4ITEM` => `'Infobulle d’objet Diablo 4'`
- `BBTIPS_HELP_D4SKILL` => `'Infobulle de compétence Diablo 4'`

`language/nl/bbtips.php` values:
- `BBTIPS_TOOLBAR_LABEL` => `'Spel-tooltips'`
- `BBTIPS_TOOLBAR_MORE` => `'Meer tooltips…'`
- `BBTIPS_TOOLBAR_ARIA` => `'Een spel-tooltiptag invoegen'`
- `BBTIPS_TOOLBAR_HINT` => `'Spel-tooltips: [item] [spell] [quest] en meer — gebruik de Spel-tooltipknoppen hierboven.'`
- `BBTIPS_HELP_ITEM` => `'WoW-voorwerp-tooltip — accepteert domain, ench, gems'`
- `BBTIPS_HELP_ITEMICO` => `'WoW-voorwerp als pictogram — accepteert een size-attribuut'`
- `BBTIPS_HELP_SPELL` => `'WoW-spreuk-/vaardigheid-tooltip'`
- `BBTIPS_HELP_QUEST` => `'WoW-quest-tooltip'`
- `BBTIPS_HELP_CRAFT` => `'WoW-recepttooltip'`
- `BBTIPS_HELP_ACHIEVEMENT` => `'WoW-prestatie-tooltip'`
- `BBTIPS_HELP_ITEMSET` => `'WoW-voorwerpset-tooltip — accepteert een pcs-attribuut'`
- `BBTIPS_HELP_NPC` => `'WoW-NPC-tooltip'`
- `BBTIPS_HELP_D4ITEM` => `'Diablo 4-voorwerp-tooltip'`
- `BBTIPS_HELP_D4SKILL` => `'Diablo 4-vaardigheid-tooltip'`

- [ ] **Step 3: Lint all four**

Run: `for f in language/{en,de,fr,nl}/bbtips.php; do php -l "$f"; done`
Expected: `No syntax errors detected` for each.

- [ ] **Step 4: Commit**

```bash
git add language/en/bbtips.php language/de/bbtips.php language/fr/bbtips.php language/nl/bbtips.php
git commit -m "feat(bbtips): add front-end language strings for editor helper"
```

---

## Task 4: Toolbar builder

Turn enabled providers/tags into the exact data the template renders: headline buttons and game-grouped dropdown options, with help text resolved to strings.

**Files:**
- Create: `service/toolbar_builder.php`
- Test: `tests/service/toolbar_builder_test.php`

**Interfaces:**
- Consumes: `avathar\bbtips\provider\provider_registry`, `avathar\bbtips\service\tag_gate` (Task 1), `phpbb\language\language`, providers' `get_tags()` + `get_primary_tags()` + `get_game_name()` (Task 2).
- Produces:
  - `class avathar\bbtips\service\toolbar_builder`
  - `__construct(provider_registry $registry, tag_gate $gate, \phpbb\language\language $language)`
  - `build(): array` returning:
    ```php
    [
      'headline' => [ ['bbcode'=>'item','open'=>'[item]','close'=>'[/item]','help'=>'<resolved>'], ... ], // ≤2
      'groups'   => [ ['game'=>'World of Warcraft','options'=>[ ['bbcode'=>..,'open'=>..,'close'=>..,'help'=>..], ... ]], ... ],
      'has_any'  => bool, // true if at least one enabled tag exists
    ]
    ```
  - Rules: only enabled providers × enabled tags; headline = first ≤2 enabled tags taken from providers' `get_primary_tags()` in provider order; if none, first ≤2 enabled tags overall. Headline tags are **excluded** from `groups`. `open`/`close` are `"[$bbcode]"` / `"[/$bbcode]"`. `help` is `language->lang($helpKey)`.

- [ ] **Step 1: Write the failing test**

```php
<?php
namespace avathar\bbtips\tests\service;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\service\toolbar_builder;
use avathar\bbtips\service\tag_gate;
use avathar\bbtips\provider\provider_registry;

class toolbar_builder_test extends TestCase
{
	/** Minimal fake provider implementing the interface surface the builder uses. */
	private function provider(string $id, string $game, array $bbcodes, array $primary): \avathar\bbtips\provider\tooltip_provider_interface
	{
		return new class($id, $game, $bbcodes, $primary) implements \avathar\bbtips\provider\tooltip_provider_interface {
			public function __construct(private string $id, private string $game, private array $bbcodes, private array $primary) {}
			public function get_id(): string { return $this->id; }
			public function get_game_name(): string { return $this->game; }
			public function get_tags(): array {
				return array_map(fn($b) => ['bbcode'=>$b,'usage'=>'','template'=>'','help'=>'BBTIPS_HELP_'.strtoupper($b)], $this->bbcodes);
			}
			public function get_primary_tags(): array { return $this->primary; }
			public function build_link(string $type, $id, array $opts = []): string { return ''; }
			public function get_runtime_asset(): array { return ['url'=>'','config'=>[]]; }
		};
	}

	private function lang(): \phpbb\language\language
	{
		$l = $this->createMock(\phpbb\language\language::class);
		$l->method('lang')->willReturnCallback(fn($k) => 'HELP:' . $k); // echo key back, prefixed
		return $l;
	}

	private function build(array $providers, array $config): array
	{
		$registry = new provider_registry($providers);
		$gate = new tag_gate(new \phpbb\config\config($config));
		return (new toolbar_builder($registry, $gate, $this->lang()))->build();
	}

	public function test_headline_uses_primary_tags_rest_grouped(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item','itemico','spell','quest'], ['item','spell']);
		$out = $this->build([$wow], [
			'bbtips_provider_wow' => '1', 'bbtips_provider_diablo4' => '0',
		]);

		$this->assertTrue($out['has_any']);
		$this->assertSame(['item','spell'], array_column($out['headline'], 'bbcode'));
		$this->assertSame('[item]', $out['headline'][0]['open']);
		$this->assertSame('[/item]', $out['headline'][0]['close']);
		$this->assertSame('HELP:BBTIPS_HELP_ITEM', $out['headline'][0]['help']);

		// itemico + quest remain, item/spell excluded from the group
		$wowGroup = $out['groups'][0];
		$this->assertSame('World of Warcraft', $wowGroup['game']);
		$this->assertSame(['itemico','quest'], array_column($wowGroup['options'], 'bbcode'));
	}

	public function test_disabled_tags_excluded(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item','spell'], ['item','spell']);
		$out = $this->build([$wow], [
			'bbtips_provider_wow' => '1', 'bbtips_provider_diablo4' => '0', 'bbtips_tag_spell' => '0',
		]);
		$this->assertSame(['item'], array_column($out['headline'], 'bbcode'));
		$this->assertSame([], $out['groups'][0]['options'] ?? []); // nothing left over
	}

	public function test_fallback_when_no_primary_enabled(): void
	{
		// primary tags both disabled -> promote first enabled non-primary tags
		$wow = $this->provider('wow', 'World of Warcraft', ['item','spell','quest','npc'], ['item','spell']);
		$out = $this->build([$wow], [
			'bbtips_provider_wow' => '1', 'bbtips_provider_diablo4' => '0',
			'bbtips_tag_item' => '0', 'bbtips_tag_spell' => '0',
		]);
		$this->assertSame(['quest','npc'], array_column($out['headline'], 'bbcode'));
	}

	public function test_disabled_provider_absent_and_diablo_only(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item','spell'], ['item','spell']);
		$d4  = $this->provider('diablo4', 'Diablo 4', ['d4item','d4skill'], ['d4item','d4skill']);
		$out = $this->build([$wow, $d4], [
			'bbtips_provider_wow' => '0', 'bbtips_provider_diablo4' => '1',
		]);
		$this->assertSame(['d4item','d4skill'], array_column($out['headline'], 'bbcode'));
		$this->assertSame(['Diablo 4'], array_column($out['groups'], 'game'));
	}

	public function test_has_any_false_when_nothing_enabled(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item'], ['item']);
		$out = $this->build([$wow], ['bbtips_provider_wow' => '0', 'bbtips_provider_diablo4' => '0']);
		$this->assertFalse($out['has_any']);
		$this->assertSame([], $out['headline']);
	}
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `$PU tests/service/toolbar_builder_test.php`
Expected: FAIL — `Class "avathar\bbtips\service\toolbar_builder" does not exist`.

- [ ] **Step 3: Implement**

```php
<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\service;

use avathar\bbtips\provider\provider_registry;
use phpbb\language\language;

/**
 * Builds the posting-toolbar data (headline buttons + grouped dropdown options)
 * from the enabled providers and tags. Parsing is unaffected — this is UI only.
 */
class toolbar_builder
{
	/** Maximum number of always-visible headline buttons. */
	const MAX_HEADLINE = 2;

	/** @var provider_registry */
	protected $registry;

	/** @var tag_gate */
	protected $gate;

	/** @var language */
	protected $language;

	public function __construct(provider_registry $registry, tag_gate $gate, language $language)
	{
		$this->registry = $registry;
		$this->gate = $gate;
		$this->language = $language;
	}

	/**
	 * @return array{headline:array, groups:array, has_any:bool}
	 */
	public function build(): array
	{
		// Ordered map of enabled tags: bbcode => ['bbcode','open','close','help','game','primary_rank'|null]
		$enabled = [];
		$primary_order = [];

		foreach ($this->registry->all() as $id => $provider)
		{
			if (!$this->gate->provider_enabled($id))
			{
				continue;
			}

			$primary = $provider->get_primary_tags();
			$game = $provider->get_game_name();

			foreach ($provider->get_tags() as $tag)
			{
				$bbcode = $tag['bbcode'];
				if (!$this->gate->tag_enabled($bbcode))
				{
					continue;
				}

				$enabled[$bbcode] = [
					'bbcode' => $bbcode,
					'open'   => '[' . $bbcode . ']',
					'close'  => '[/' . $bbcode . ']',
					'help'   => $this->language->lang($tag['help']),
					'game'   => $game,
				];
			}

			// Record provider-declared primary tags that survived the tag gate, in order.
			foreach ($primary as $bbcode)
			{
				if (isset($enabled[$bbcode]) && !in_array($bbcode, $primary_order, true))
				{
					$primary_order[] = $bbcode;
				}
			}
		}

		if (empty($enabled))
		{
			return ['headline' => [], 'groups' => [], 'has_any' => false];
		}

		// Headline: first MAX_HEADLINE primary tags; fall back to first enabled tags.
		$headline_keys = array_slice($primary_order, 0, self::MAX_HEADLINE);
		if (empty($headline_keys))
		{
			$headline_keys = array_slice(array_keys($enabled), 0, self::MAX_HEADLINE);
		}
		$headline_set = array_flip($headline_keys);

		$headline = [];
		foreach ($headline_keys as $bbcode)
		{
			$row = $enabled[$bbcode];
			unset($row['game']);
			$headline[] = $row;
		}

		// Groups: everything not promoted to headline, grouped by game in first-seen order.
		$groups = [];
		$index = [];
		foreach ($enabled as $bbcode => $row)
		{
			if (isset($headline_set[$bbcode]))
			{
				continue;
			}
			$game = $row['game'];
			if (!isset($index[$game]))
			{
				$index[$game] = count($groups);
				$groups[] = ['game' => $game, 'options' => []];
			}
			unset($row['game']);
			$groups[$index[$game]]['options'][] = $row;
		}

		return ['headline' => $headline, 'groups' => $groups, 'has_any' => true];
	}
}
```

Note on the `test_disabled_tags_excluded` case: when the only leftover would be empty, the WoW group still gets created with an empty `options` only if a non-headline enabled tag exists; with `['item','spell']` and spell disabled, `item` is headline and nothing remains, so `groups` is `[]` and `$out['groups'][0]['options'] ?? []` yields `[]`. Implementation above produces `groups => []` in that case — the test's null-coalesce handles it.

- [ ] **Step 4: Run test to verify it passes**

Run: `$PU tests/service/toolbar_builder_test.php`
Expected: PASS (5 tests).

- [ ] **Step 5: Run the whole local suite for regressions**

Run: `$PU tests/service tests/provider`
Expected: PASS (all).

- [ ] **Step 6: Commit**

```bash
git add service/toolbar_builder.php tests/service/toolbar_builder_test.php
git commit -m "feat(bbtips): add toolbar_builder for editor helper data"
```

---

## Task 5: Posting listener + service wiring + bbcode_listener refactor

Wire everything into phpBB: register the services, refactor `bbcode_listener` onto the shared gate, and add the posting listener that assigns template data.

**Files:**
- Modify: `config/services.yml`
- Modify: `event/bbcode_listener.php`
- Create: `event/posting_listener.php`

**Interfaces:**
- Consumes: `toolbar_builder` (Task 4), `tag_gate` (Task 1), `phpbb\template\template`, `phpbb\language\language`.
- Produces: template vars for Task 6 —
  - `S_BBTIPS_TOOLBAR` (bool = `has_any`)
  - block `bbtips_headline`: `{bbtips_headline.OPEN}`, `.CLOSE`, `.HELP`, `.TAG`
  - block `bbtips_group`: `{bbtips_group.GAME}`, nested block `bbtips_group.opt`: `.OPEN`, `.CLOSE`, `.HELP`, `.TAG`
  - lang keys added via `add_lang('bbtips', 'avathar/bbtips')` so `{L_BBTIPS_*}` resolve.

- [ ] **Step 1: Refactor `bbcode_listener` to use `tag_gate`**

Replace its `$config` usage and private methods. New file body:

```php
<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\event;

use avathar\bbtips\provider\provider_registry;
use avathar\bbtips\service\tag_gate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class bbcode_listener implements EventSubscriberInterface
{
	/** @var provider_registry */
	protected $registry;

	/** @var tag_gate */
	protected $gate;

	public function __construct(provider_registry $registry, tag_gate $gate)
	{
		$this->registry = $registry;
		$this->gate = $gate;
	}

	static public function getSubscribedEvents()
	{
		return ['core.text_formatter_s9e_configure_after' => 'register_bbcodes'];
	}

	public function register_bbcodes($event)
	{
		$configurator = $event['configurator'];

		foreach ($this->registry->all() as $id => $provider)
		{
			if (!$this->gate->provider_enabled($id))
			{
				continue;
			}

			foreach ($provider->get_tags() as $tag)
			{
				if (!$this->gate->tag_enabled($tag['bbcode']))
				{
					continue;
				}

				if (isset($configurator->tags[strtoupper($tag['bbcode'])]))
				{
					continue;
				}

				try
				{
					$configurator->BBCodes->addCustom($tag['usage'], $tag['template']);
				}
				catch (\Exception $e)
				{
					// A malformed spec must never break post rendering board-wide.
				}
			}
		}
	}
}
```

- [ ] **Step 2: Create `event/posting_listener.php`**

```php
<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\event;

use avathar\bbtips\service\toolbar_builder;
use phpbb\language\language;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Injects the bbTips insertion helper into the posting editor toolbar.
 * Fires only on the posting page (core.posting_modify_bbcode_status), so it
 * never touches other pages. UI only — parsing is handled elsewhere.
 */
class posting_listener implements EventSubscriberInterface
{
	/** @var toolbar_builder */
	protected $builder;

	/** @var template */
	protected $template;

	/** @var language */
	protected $language;

	public function __construct(toolbar_builder $builder, template $template, language $language)
	{
		$this->builder = $builder;
		$this->template = $template;
		$this->language = $language;
	}

	static public function getSubscribedEvents()
	{
		return ['core.posting_modify_bbcode_status' => 'inject_toolbar'];
	}

	public function inject_toolbar($event)
	{
		// Only meaningful when BBCode is allowed in this forum.
		if (empty($event['bbcode_status']))
		{
			return;
		}

		$data = $this->builder->build();
		if (!$data['has_any'])
		{
			return;
		}

		$this->language->add_lang('bbtips', 'avathar/bbtips');

		$this->template->assign_var('S_BBTIPS_TOOLBAR', true);

		foreach ($data['headline'] as $btn)
		{
			$this->template->assign_block_vars('bbtips_headline', [
				'TAG'   => $btn['bbcode'],
				'OPEN'  => $btn['open'],
				'CLOSE' => $btn['close'],
				'HELP'  => $btn['help'],
			]);
		}

		foreach ($data['groups'] as $group)
		{
			$this->template->assign_block_vars('bbtips_group', [
				'GAME' => $group['game'],
			]);

			foreach ($group['options'] as $opt)
			{
				$this->template->assign_block_vars('bbtips_group.opt', [
					'TAG'   => $opt['bbcode'],
					'OPEN'  => $opt['open'],
					'CLOSE' => $opt['close'],
					'HELP'  => $opt['help'],
				]);
			}
		}
	}
}
```

- [ ] **Step 3: Update `config/services.yml`**

Replace the `avathar.bbtips.bbcode_listener` block and add three services. The full services list becomes (append/replace as shown):

```yaml
    avathar.bbtips.tag_gate:
        class: avathar\bbtips\service\tag_gate
        arguments:
            - '@config'

    avathar.bbtips.toolbar_builder:
        class: avathar\bbtips\service\toolbar_builder
        arguments:
            - '@avathar.bbtips.provider_registry'
            - '@avathar.bbtips.tag_gate'
            - '@language'

    avathar.bbtips.bbcode_listener:
        class: avathar\bbtips\event\bbcode_listener
        arguments:
            - '@avathar.bbtips.provider_registry'
            - '@avathar.bbtips.tag_gate'
        tags:
            - { name: event.listener }

    avathar.bbtips.posting_listener:
        class: avathar\bbtips\event\posting_listener
        arguments:
            - '@avathar.bbtips.toolbar_builder'
            - '@template'
            - '@language'
        tags:
            - { name: event.listener }
```

(Leave the existing `provider.*`, `provider_collection`, `provider_registry`, `linker`, and `runtime_listener` blocks as they are.)

- [ ] **Step 4: Lint the new/edited PHP**

Run: `php -l event/posting_listener.php && php -l event/bbcode_listener.php`
Expected: `No syntax errors detected` for both.

- [ ] **Step 5: Run the local unit suite (no regressions)**

Run: `$PU tests/service tests/provider`
Expected: PASS. (bbcode_listener has no unit test; its behaviour is covered by the integration check in Task 7.)

- [ ] **Step 6: Commit**

```bash
git add config/services.yml event/bbcode_listener.php event/posting_listener.php
git commit -m "feat(bbtips): wire tag_gate, toolbar_builder and posting_listener"
```

---

## Task 6: Template-event files (toolbar + hint)

Render the toolbar controls and the hint line. `styles/all` covers every style.

**Files:**
- Create: `styles/all/template/event/posting_buttons_after.html`
- Create: `styles/all/template/event/posting_editor_bbcode_status_after.html`

**Interfaces:**
- Consumes: template vars from Task 5 (`S_BBTIPS_TOOLBAR`, `bbtips_headline`, `bbtips_group[.opt]`, `L_BBTIPS_*`).
- Produces: DOM + JS calling phpBB's global `bbfontstyle(open, close)` (defined in `assets/javascript/editor.js`, loaded with the posting form).

- [ ] **Step 1: Create `styles/all/template/event/posting_buttons_after.html`**

```html
<!-- IF S_BBTIPS_TOOLBAR -->
	<!-- BEGIN bbtips_headline -->
		<button type="button" class="button button-secondary bbtips-tag bbtips-tag-{bbtips_headline.TAG}" name="bbtips_{bbtips_headline.TAG}" value="{bbtips_headline.TAG}" onclick="bbfontstyle('{bbtips_headline.OPEN}', '{bbtips_headline.CLOSE}'); return false;" title="{bbtips_headline.HELP}">
			{bbtips_headline.TAG}
		</button>
	<!-- END bbtips_headline -->

	<select class="bbtips-more" onchange="bbtips_insert(this);" aria-label="{L_BBTIPS_TOOLBAR_ARIA}" title="{L_BBTIPS_TOOLBAR_LABEL}">
		<option value="">{L_BBTIPS_TOOLBAR_MORE}</option>
		<!-- BEGIN bbtips_group -->
			<optgroup label="{bbtips_group.GAME}">
				<!-- BEGIN opt -->
					<option value="{bbtips_group.opt.TAG}" data-open="{bbtips_group.opt.OPEN}" data-close="{bbtips_group.opt.CLOSE}" data-help="{bbtips_group.opt.HELP}">{bbtips_group.opt.TAG}</option>
				<!-- END opt -->
			</optgroup>
		<!-- END bbtips_group -->
	</select>
	<span class="bbtips-help" id="bbtips-help" aria-live="polite"></span>

	<script>
	// Defined once per page; inserts the selected tag skeleton and shows its help line.
	if (typeof window.bbtips_insert === 'undefined') {
		window.bbtips_insert = function (sel) {
			var o = sel.options[sel.selectedIndex];
			if (o && o.getAttribute('data-open')) {
				bbfontstyle(o.getAttribute('data-open'), o.getAttribute('data-close'));
				var h = document.getElementById('bbtips-help');
				if (h) { h.textContent = o.getAttribute('data-help') || ''; }
			}
			sel.selectedIndex = 0;
		};
	}
	</script>
<!-- ENDIF -->
```

- [ ] **Step 2: Create `styles/all/template/event/posting_editor_bbcode_status_after.html`**

```html
<!-- IF S_BBTIPS_TOOLBAR -->
	<br /><span class="bbtips-hint">{L_BBTIPS_TOOLBAR_HINT}</span>
<!-- ENDIF -->
```

- [ ] **Step 3: Verify file permissions (EPV)**

Run: `chmod 644 styles/all/template/event/posting_buttons_after.html styles/all/template/event/posting_editor_bbcode_status_after.html && ls -l styles/all/template/event/`
Expected: both new files `-rw-r--r--`.

- [ ] **Step 4: Commit**

```bash
git add styles/all/template/event/posting_buttons_after.html styles/all/template/event/posting_editor_bbcode_status_after.html
git commit -m "feat(bbtips): render editor helper toolbar and hint line"
```

---

## Task 7: End-to-end verification in the real forum

Confirm the helper actually renders and inserts, and that parsing still works, using the live phpBB install. (Manual — no phpBB test framework locally.)

**Files:** none (verification only).

- [ ] **Step 1: Purge the cache so new services/templates load**

Run: `find /Users/Andreas/Sites/avathar/forum/cache/production -maxdepth 1 -type f -delete 2>/dev/null; find /Users/Andreas/Sites/avathar/forum/cache/production -maxdepth 1 -name 'twig' -type d -exec rm -rf {} + 2>/dev/null; echo purged`
(Or use ACP → Purge the cache.)

- [ ] **Step 2: Confirm parsing is still intact (regression guard for the bbcode_listener refactor)**

Run: `php /private/tmp/claude-501/-Users-Andreas-Sites-avathar-forum-ext-avathar/bdf341cd-61bc-46d2-b3e1-c559e8dba005/scratchpad/phpbb_repro.php`
Expected: both `[item]50468[/item]` and `[item domain=classic ench=3825 gems=40133]50468[/item]` still produce `<a … data-wowhead="…">` output (same as before the change).

- [ ] **Step 3: Load the posting page in a browser**

Open a "Post a reply" / "New topic" page in a bbcode-enabled forum. Confirm:
- `[item]` and `[spell]` buttons appear in the toolbar,
- a "Game tooltips" dropdown lists the remaining tags grouped by "World of Warcraft" / "Diablo 4",
- the hint line shows under "BBCode is ON".

- [ ] **Step 4: Exercise insertion**

- Click `[item]` → editor inserts `[item][/item]` with the caret between the tags.
- Pick `quest` from the dropdown → inserts `[quest][/quest]` and the help span shows the quest help line; the select resets to "More tooltips…".

- [ ] **Step 5: Verify across styles**

Repeat Step 3 briefly under pbwow3 and pbtech (the actively-used styles) to confirm the toolbar wraps cleanly and the controls are usable. Note any layout issue for a follow-up CSS pass (out of scope for this plan unless broken).

- [ ] **Step 6: Toggle check**

In ACP → bbTips settings, disable the `[spell]` tag; reload the posting page; confirm `[spell]` no longer appears as a headline button (and `[item]` remains). Re-enable it afterward.

- [ ] **Step 7: Update docs**

Add a short "Inserting tags from the editor" subsection to `README.md` (after the BBCode tags table) describing the toolbar buttons + dropdown, and a `CHANGELOG.md` entry under a new unreleased heading. Commit:

```bash
git add README.md CHANGELOG.md
git commit -m "docs(bbtips): document editor insertion helper"
```

---

## Self-review notes (author)

- **Spec coverage:** convenience layer/no-ACP-rows (Global Constraints + Task 5 refactor keeps s9e path); help line third-part (Task 2 + 3); three discoverability levers — headline buttons + grouped dropdown + live help (Tasks 4/6), hint line (Tasks 5/6); primary-tag rule incl. fallbacks + D4-only (Task 4 tests); shared enablement gate (Task 1, consumed by Tasks 4 & 5); front-end lang in en/de/fr/nl (Task 3); config gating / no migration (Task 5 `bbcode_status` guard, no migration touched); GDPR-independent (listener never reads `bbtips_runtime_enabled`); unit + manual tests (Tasks 1/2/4 + Task 7). All spec sections map to a task.
- **Type consistency:** `tag_gate::provider_enabled/tag_enabled`, `toolbar_builder::build()` return shape, and the template block/var names (`S_BBTIPS_TOOLBAR`, `bbtips_headline`, `bbtips_group.opt`, `L_BBTIPS_*`) are used identically across Tasks 4/5/6. Help key format `BBTIPS_HELP_<UPPER>` is consistent between Task 2 (producer) and Task 3 (definitions).
- **No placeholders:** every code step contains full content; translations are given verbatim rather than "translate appropriately".
