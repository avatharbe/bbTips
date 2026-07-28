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
