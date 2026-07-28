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
	'BBTIPS_TOOLBAR_LABEL'	=> 'Infobulles de jeu',
	'BBTIPS_TOOLBAR_MORE'	=> 'Plus d’infobulles…',
	'BBTIPS_TOOLBAR_ARIA'	=> 'Insérer une balise d’infobulle de jeu',
	'BBTIPS_TOOLBAR_HINT'	=> 'Infobulles de jeu : [item] [spell] [quest] et plus — utilisez les boutons Infobulles de jeu ci-dessus.',

	'BBTIPS_HELP_ITEM'			=> 'Infobulle d’objet WoW — accepte domain, ench, gems',
	'BBTIPS_HELP_ITEMICO'		=> 'Objet WoW en icône — accepte un attribut size',
	'BBTIPS_HELP_SPELL'			=> 'Infobulle de sort / capacité WoW',
	'BBTIPS_HELP_QUEST'			=> 'Infobulle de quête WoW',
	'BBTIPS_HELP_CRAFT'			=> 'Infobulle de recette WoW',
	'BBTIPS_HELP_ACHIEVEMENT'	=> 'Infobulle de haut fait WoW',
	'BBTIPS_HELP_ITEMSET'		=> 'Infobulle d’ensemble d’objets WoW — accepte un attribut pcs',
	'BBTIPS_HELP_NPC'			=> 'Infobulle de PNJ WoW',
	'BBTIPS_HELP_D4ITEM'		=> 'Infobulle d’objet Diablo 4',
	'BBTIPS_HELP_D4SKILL'		=> 'Infobulle de compétence Diablo 4',
));
