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
	'ACP_BBTIPS_SETTINGS'            => 'bbTips-instellingen',
	'ACP_BBTIPS_SAVED'               => 'bbTips-instellingen opgeslagen.',

	'ACP_BBTIPS_PROVIDERS'           => 'Spelproviders',
	'ACP_BBTIPS_PROVIDER_WOW'        => 'World of Warcraft-tooltips',
	'ACP_BBTIPS_PROVIDER_DIABLO4'    => 'Diablo 4-tooltips',

	'ACP_BBTIPS_TAGS'                => 'BBCode-tags',
	'ACP_BBTIPS_TAG_ITEM'            => 'Item-tooltips ([item])',
	'ACP_BBTIPS_TAG_ITEMICO'         => 'Item-icoon-tooltips ([itemico])',
	'ACP_BBTIPS_TAG_SPELL'           => 'Spreuk-tooltips ([spell])',
	'ACP_BBTIPS_TAG_QUEST'           => 'Quest-tooltips ([quest])',
	'ACP_BBTIPS_TAG_CRAFT'           => 'Ambachts-/recept-tooltips ([craft])',
	'ACP_BBTIPS_TAG_ACHIEVEMENT'     => 'Prestatie-tooltips ([achievement])',
	'ACP_BBTIPS_TAG_ITEMSET'         => 'Itemset-tooltips ([itemset])',
	'ACP_BBTIPS_TAG_NPC'             => 'NPC-tooltips ([npc])',
	'ACP_BBTIPS_TAG_D4ITEM'          => 'Diablo 4-item-tooltips ([d4item])',
	'ACP_BBTIPS_TAG_D4SKILL'         => 'Diablo 4-vaardigheids-tooltips ([d4skill])',

	'ACP_BBTIPS_BEHAVIOR'            => 'Gedrag',
	'ACP_BBTIPS_WOW_DOMAIN'          => 'Standaard WoW-domein',
	'ACP_BBTIPS_WOW_DOMAIN_EXPLAIN'  => 'Leeg = retail. Voorbeelden: classic, ptr, de, ru, de.classic.',
	'ACP_BBTIPS_SCOPE'               => 'Tooltip-script laden op',
	'ACP_BBTIPS_SCOPE_ALL'           => 'Alle forumpagina\'s',
	'ACP_BBTIPS_SCOPE_POSTS'         => 'Alleen forumberichten',
	'ACP_BBTIPS_RUNTIME_ENABLED'     => 'WowHead-tooltipscript inschakelen (derde partij)',

	'ACP_BBTIPS_DISPLAY'             => 'Linkweergave',
	'ACP_BBTIPS_COLOR_LINKS'         => 'Tooltip-links kleuren op itemkwaliteit',
	'ACP_BBTIPS_ICONIZE_LINKS'       => 'Icoon tonen naast tooltip-links',
	'ACP_BBTIPS_RENAME_LINKS'        => 'Linktekst vervangen door de actuele item-/spreuknaam',

	'LOG_BBTIPS_SETTINGS'            => '<strong>bbTips-instellingen bijgewerkt</strong>',
]);

$lang = array_merge($lang, [
	'ACP_BBTIPS_PROVIDER_WOW_EXPLAIN'     => 'Schakelt de World of Warcraft-tooltip-BBCodes in (item, spell, quest, achievement, enzovoort).',
	'ACP_BBTIPS_PROVIDER_DIABLO4_EXPLAIN' => 'Schakelt de Diablo 4-tooltip-BBCodes in ([d4item], [d4skill]).',
	'ACP_BBTIPS_TAG_ITEM_EXPLAIN'         => 'Voorbeeld: [item]50468[/item]. Optionele attributen: domain, ench, gems, pcs.',
	'ACP_BBTIPS_TAG_ITEMICO_EXPLAIN'      => 'Toont het item-icoon. Voorbeeld: [itemico size=medium]50468[/itemico].',
	'ACP_BBTIPS_TAG_SPELL_EXPLAIN'        => 'Voorbeeld: [spell]17[/spell].',
	'ACP_BBTIPS_TAG_QUEST_EXPLAIN'        => 'Voorbeeld: [quest]12345[/quest].',
	'ACP_BBTIPS_TAG_CRAFT_EXPLAIN'        => 'Recept-/maak-tooltip. Voorbeeld: [craft]46351[/craft].',
	'ACP_BBTIPS_TAG_ACHIEVEMENT_EXPLAIN'  => 'Voorbeeld: [achievement]892[/achievement].',
	'ACP_BBTIPS_TAG_ITEMSET_EXPLAIN'      => 'Voorbeeld: [itemset]861[/itemset].',
	'ACP_BBTIPS_TAG_NPC_EXPLAIN'          => 'Voorbeeld: [npc]11502[/npc].',
	'ACP_BBTIPS_TAG_D4ITEM_EXPLAIN'       => 'Voorbeeld: [d4item]444429[/d4item].',
	'ACP_BBTIPS_TAG_D4SKILL_EXPLAIN'      => 'Voorbeeld: [d4skill]1[/d4skill].',
	'ACP_BBTIPS_SCOPE_EXPLAIN'            => 'Waar het WowHead-tooltipscript wordt geladen. „Alle forumpagina’s” omvat ook app.php-pagina’s zoals bbGuild.',
	'ACP_BBTIPS_RUNTIME_ENABLED_EXPLAIN'  => 'Laadt WowHead’s externe tooltips.js. Uitschakelen voor een AVG-vriendelijk forum; links werken nog als gewone WowHead-links.',
	'ACP_BBTIPS_COLOR_LINKS_EXPLAIN'      => 'Kleurt elke tooltip-link op itemkwaliteit.',
	'ACP_BBTIPS_ICONIZE_LINKS_EXPLAIN'    => 'Toont een klein icoon naast elke tooltip-link.',
	'ACP_BBTIPS_RENAME_LINKS_EXPLAIN'     => 'Vervangt de numerieke ID in de linktekst door de actuele naam.',
]);
