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
	$lang = [];
}

$lang = array_merge($lang, [
	'ACP_CAT_BBTIPS'                => 'bbTips',
	'ACP_BBTIPS_SETTINGS'            => 'bbTips-Einstellungen',
	'ACP_BBTIPS_SAVED'               => 'bbTips-Einstellungen gespeichert.',

	'ACP_BBTIPS_PROVIDERS'           => 'Spiel-Anbieter',
	'ACP_BBTIPS_PROVIDER_WOW'        => 'World of Warcraft-Tooltips',
	'ACP_BBTIPS_PROVIDER_DIABLO4'    => 'Diablo-4-Tooltips',

	'ACP_BBTIPS_TAGS'                => 'BBCode-Tags',
	'ACP_BBTIPS_TAG_ITEM'            => 'Item-Tooltips ([item])',
	'ACP_BBTIPS_TAG_ITEMICO'         => 'Item-Icon-Tooltips ([itemico])',
	'ACP_BBTIPS_TAG_SPELL'           => 'Zauber-Tooltips ([spell])',
	'ACP_BBTIPS_TAG_QUEST'           => 'Quest-Tooltips ([quest])',
	'ACP_BBTIPS_TAG_CRAFT'           => 'Handwerks-/Rezept-Tooltips ([craft])',
	'ACP_BBTIPS_TAG_ACHIEVEMENT'     => 'Erfolgs-Tooltips ([achievement])',
	'ACP_BBTIPS_TAG_ITEMSET'         => 'Set-Tooltips ([itemset])',
	'ACP_BBTIPS_TAG_NPC'             => 'NPC-Tooltips ([npc])',
	'ACP_BBTIPS_TAG_D4ITEM'          => 'Diablo-4-Item-Tooltips ([d4item])',
	'ACP_BBTIPS_TAG_D4SKILL'         => 'Diablo-4-Fertigkeits-Tooltips ([d4skill])',

	'ACP_BBTIPS_BEHAVIOR'            => 'Verhalten',
	'ACP_BBTIPS_WOW_DOMAIN'          => 'Standard-WoW-Domain',
	'ACP_BBTIPS_WOW_DOMAIN_EXPLAIN'  => 'Leer = Retail. Beispiele: classic, ptr, de, ru, de.classic.',
	'ACP_BBTIPS_SCOPE'               => 'Tooltip-Skript laden auf',
	'ACP_BBTIPS_SCOPE_ALL'           => 'Allen Forumsseiten',
	'ACP_BBTIPS_SCOPE_POSTS'         => 'Nur Forenbeiträgen',
	'ACP_BBTIPS_RUNTIME_ENABLED'     => 'WowHead-Tooltip-Skript aktivieren (Drittanbieter)',

	'ACP_BBTIPS_DISPLAY'             => 'Link-Anzeige',
	'ACP_BBTIPS_COLOR_LINKS'         => 'Tooltip-Links nach Item-Qualität einfärben',
	'ACP_BBTIPS_ICONIZE_LINKS'       => 'Symbol neben Tooltip-Links anzeigen',
	'ACP_BBTIPS_RENAME_LINKS'        => 'Linktext durch den aktuellen Item-/Zaubernamen ersetzen',

	'LOG_BBTIPS_SETTINGS'            => '<strong>bbTips-Einstellungen aktualisiert</strong>',
]);

$lang = array_merge($lang, [
	'ACP_BBTIPS_PROVIDER_WOW_EXPLAIN'     => 'Aktiviert die World-of-Warcraft-Tooltip-BBCodes (item, spell, quest, achievement usw.).',
	'ACP_BBTIPS_PROVIDER_DIABLO4_EXPLAIN' => 'Aktiviert die Diablo-4-Tooltip-BBCodes ([d4item], [d4skill]).',
	'ACP_BBTIPS_TAG_ITEM_EXPLAIN'         => 'Beispiel: [item]50468[/item]. Optionale Attribute: domain, ench, gems, pcs.',
	'ACP_BBTIPS_TAG_ITEMICO_EXPLAIN'      => 'Zeigt das Item-Symbol. Beispiel: [itemico size=medium]50468[/itemico].',
	'ACP_BBTIPS_TAG_SPELL_EXPLAIN'        => 'Beispiel: [spell]17[/spell].',
	'ACP_BBTIPS_TAG_QUEST_EXPLAIN'        => 'Beispiel: [quest]12345[/quest].',
	'ACP_BBTIPS_TAG_CRAFT_EXPLAIN'        => 'Rezept-/Herstellungs-Tooltip. Beispiel: [craft]46351[/craft].',
	'ACP_BBTIPS_TAG_ACHIEVEMENT_EXPLAIN'  => 'Beispiel: [achievement]892[/achievement].',
	'ACP_BBTIPS_TAG_ITEMSET_EXPLAIN'      => 'Beispiel: [itemset]861[/itemset].',
	'ACP_BBTIPS_TAG_NPC_EXPLAIN'          => 'Beispiel: [npc]11502[/npc].',
	'ACP_BBTIPS_TAG_D4ITEM_EXPLAIN'       => 'Beispiel: [d4item]444429[/d4item].',
	'ACP_BBTIPS_TAG_D4SKILL_EXPLAIN'      => 'Beispiel: [d4skill]1[/d4skill].',
	'ACP_BBTIPS_SCOPE_EXPLAIN'            => 'Legt fest, wo das WowHead-Tooltip-Skript geladen wird. „Alle Forumsseiten“ umfasst auch app.php-Seiten wie bbGuild.',
	'ACP_BBTIPS_RUNTIME_ENABLED_EXPLAIN'  => 'Lädt das Drittanbieter-Skript tooltips.js von WowHead. Für einen DSGVO-freundlichen Betrieb deaktivieren; Links funktionieren weiterhin als einfache WowHead-Links.',
	'ACP_BBTIPS_COLOR_LINKS_EXPLAIN'      => 'Färbt jeden Tooltip-Link nach der Item-Qualität.',
	'ACP_BBTIPS_ICONIZE_LINKS_EXPLAIN'    => 'Zeigt ein kleines Symbol neben jedem Tooltip-Link.',
	'ACP_BBTIPS_RENAME_LINKS_EXPLAIN'     => 'Ersetzt die numerische ID im Linktext durch den aktuellen Namen.',
]);
