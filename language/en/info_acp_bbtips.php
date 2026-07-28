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
	'ACP_CAT_BBTIPS'                     => 'bbTips',
	'ACP_BBTIPS_SETTINGS'                => 'bbTips settings',
	'ACP_BBTIPS_SAVED'                   => 'bbTips settings saved.',

	'ACP_BBTIPS_PROVIDERS'               => 'Game providers',
	'ACP_BBTIPS_PROVIDER_WOW'            => 'World of Warcraft tooltips',
	'ACP_BBTIPS_PROVIDER_WOW_EXPLAIN'    => 'Enable the World of Warcraft tooltip BBCodes (item, spell, quest, achievement, and so on).',
	'ACP_BBTIPS_PROVIDER_DIABLO4'        => 'Diablo 4 tooltips',
	'ACP_BBTIPS_PROVIDER_DIABLO4_EXPLAIN' => 'Enable the Diablo 4 tooltip BBCodes ([d4item], [d4skill]).',

	'ACP_BBTIPS_TAGS'                    => 'BBCode tags',
	'ACP_BBTIPS_TAG_ITEM'                => 'Item tooltips ([item])',
	'ACP_BBTIPS_TAG_ITEM_EXPLAIN'        => 'Example: [item]50468[/item]. Optional attributes: domain, ench, gems, pcs.',
	'ACP_BBTIPS_TAG_ITEMICO'             => 'Item icon tooltips ([itemico])',
	'ACP_BBTIPS_TAG_ITEMICO_EXPLAIN'     => 'Renders the item icon. Example: [itemico size=medium]50468[/itemico].',
	'ACP_BBTIPS_TAG_SPELL'               => 'Spell tooltips ([spell])',
	'ACP_BBTIPS_TAG_SPELL_EXPLAIN'       => 'Example: [spell]17[/spell].',
	'ACP_BBTIPS_TAG_QUEST'               => 'Quest tooltips ([quest])',
	'ACP_BBTIPS_TAG_QUEST_EXPLAIN'       => 'Example: [quest]12345[/quest].',
	'ACP_BBTIPS_TAG_CRAFT'               => 'Craft/recipe tooltips ([craft])',
	'ACP_BBTIPS_TAG_CRAFT_EXPLAIN'       => 'Recipe/craftable tooltip. Example: [craft]46351[/craft].',
	'ACP_BBTIPS_TAG_ACHIEVEMENT'         => 'Achievement tooltips ([achievement])',
	'ACP_BBTIPS_TAG_ACHIEVEMENT_EXPLAIN' => 'Example: [achievement]892[/achievement].',
	'ACP_BBTIPS_TAG_ITEMSET'             => 'Item set tooltips ([itemset])',
	'ACP_BBTIPS_TAG_ITEMSET_EXPLAIN'     => 'Example: [itemset]861[/itemset].',
	'ACP_BBTIPS_TAG_NPC'                 => 'NPC tooltips ([npc])',
	'ACP_BBTIPS_TAG_NPC_EXPLAIN'         => 'Example: [npc]11502[/npc].',
	'ACP_BBTIPS_TAG_D4ITEM'              => 'Diablo 4 item tooltips ([d4item])',
	'ACP_BBTIPS_TAG_D4ITEM_EXPLAIN'      => 'Example: [d4item]444429[/d4item].',
	'ACP_BBTIPS_TAG_D4SKILL'             => 'Diablo 4 skill tooltips ([d4skill])',
	'ACP_BBTIPS_TAG_D4SKILL_EXPLAIN'     => 'Example: [d4skill]1[/d4skill].',

	'ACP_BBTIPS_BEHAVIOR'                => 'Behavior',
	'ACP_BBTIPS_WOW_DOMAIN'              => 'Default WoW domain',
	'ACP_BBTIPS_WOW_DOMAIN_EXPLAIN'      => 'Applied when a tag omits its own domain. Blank = retail. Examples: classic, ptr, de, ru, de.classic.',
	'ACP_BBTIPS_SCOPE'                   => 'Load tooltip script on',
	'ACP_BBTIPS_SCOPE_EXPLAIN'           => 'Where the WowHead tooltip script is loaded. “All board pages” also covers app.php pages such as bbGuild.',
	'ACP_BBTIPS_SCOPE_ALL'               => 'All board pages',
	'ACP_BBTIPS_SCOPE_POSTS'             => 'Forum posts only',
	'ACP_BBTIPS_RUNTIME_ENABLED'         => 'Enable WowHead tooltip script (third-party)',
	'ACP_BBTIPS_RUNTIME_ENABLED_EXPLAIN' => 'Loads WowHead’s third-party tooltips.js. Disable for a GDPR-friendly board; links still work as plain WowHead links.',

	'ACP_BBTIPS_DISPLAY'                 => 'Link display',
	'ACP_BBTIPS_COLOR_LINKS'             => 'Color tooltip links by item quality',
	'ACP_BBTIPS_COLOR_LINKS_EXPLAIN'     => 'Color each tooltip link by the item’s quality.',
	'ACP_BBTIPS_ICONIZE_LINKS'           => 'Show icon next to tooltip links',
	'ACP_BBTIPS_ICONIZE_LINKS_EXPLAIN'   => 'Show a small icon next to each tooltip link.',
	'ACP_BBTIPS_RENAME_LINKS'            => 'Replace link text with the live item/spell name',
	'ACP_BBTIPS_RENAME_LINKS_EXPLAIN'    => 'Replace the numeric ID in the link text with the live entity name.',

	'LOG_BBTIPS_SETTINGS'                => '<strong>bbTips settings updated</strong>',
]);
