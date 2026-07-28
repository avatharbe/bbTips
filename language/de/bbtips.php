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
	'BBTIPS_TOOLBAR_LABEL'	=> 'Spiel-Tooltips',
	'BBTIPS_TOOLBAR_MORE'	=> 'Weitere Tooltips…',
	'BBTIPS_TOOLBAR_ARIA'	=> 'Einen Spiel-Tooltip-Tag einfügen',
	'BBTIPS_TOOLBAR_HINT'	=> 'Spiel-Tooltips: [item] [spell] [quest] und mehr — nutze die Spiel-Tooltip-Schaltflächen oben.',

	'BBTIPS_HELP_ITEM'			=> 'WoW-Gegenstand-Tooltip — akzeptiert domain, ench, gems',
	'BBTIPS_HELP_ITEMICO'		=> 'WoW-Gegenstand als Symbol — akzeptiert ein size-Attribut',
	'BBTIPS_HELP_SPELL'			=> 'WoW-Zauber-/Fähigkeits-Tooltip',
	'BBTIPS_HELP_QUEST'			=> 'WoW-Quest-Tooltip',
	'BBTIPS_HELP_CRAFT'			=> 'WoW-Rezept-Tooltip',
	'BBTIPS_HELP_ACHIEVEMENT'	=> 'WoW-Erfolg-Tooltip',
	'BBTIPS_HELP_ITEMSET'		=> 'WoW-Gegenstandsset-Tooltip — akzeptiert ein pcs-Attribut',
	'BBTIPS_HELP_NPC'			=> 'WoW-NPC-Tooltip',
	'BBTIPS_HELP_D4ITEM'		=> 'Diablo-4-Gegenstand-Tooltip',
	'BBTIPS_HELP_D4SKILL'		=> 'Diablo-4-Fähigkeit-Tooltip',
));
