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
	'BBTIPS_TOOLBAR_LABEL'	=> 'Spel-tooltips',
	'BBTIPS_TOOLBAR_MORE'	=> 'Meer tooltips…',
	'BBTIPS_TOOLBAR_ARIA'	=> 'Een spel-tooltiptag invoegen',
	'BBTIPS_TOOLBAR_HINT'	=> 'Spel-tooltips: [item] [spell] [quest] en meer — gebruik de Spel-tooltipknoppen hierboven.',

	'BBTIPS_HELP_ITEM'			=> 'WoW-voorwerp-tooltip — accepteert domain, ench, gems',
	'BBTIPS_HELP_ITEMICO'		=> 'WoW-voorwerp als pictogram — accepteert een size-attribuut',
	'BBTIPS_HELP_SPELL'			=> 'WoW-spreuk-/vaardigheid-tooltip',
	'BBTIPS_HELP_QUEST'			=> 'WoW-quest-tooltip',
	'BBTIPS_HELP_CRAFT'			=> 'WoW-recepttooltip',
	'BBTIPS_HELP_ACHIEVEMENT'	=> 'WoW-prestatie-tooltip',
	'BBTIPS_HELP_ITEMSET'		=> 'WoW-voorwerpset-tooltip — accepteert een pcs-attribuut',
	'BBTIPS_HELP_NPC'			=> 'WoW-NPC-tooltip',
	'BBTIPS_HELP_D4ITEM'		=> 'Diablo 4-voorwerp-tooltip',
	'BBTIPS_HELP_D4SKILL'		=> 'Diablo 4-vaardigheid-tooltip',
));
