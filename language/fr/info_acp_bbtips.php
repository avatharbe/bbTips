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
	'ACP_BBTIPS_SETTINGS'            => 'Paramètres bbTips',
	'ACP_BBTIPS_SAVED'               => 'Paramètres bbTips enregistrés.',

	'ACP_BBTIPS_PROVIDERS'           => 'Fournisseurs de jeu',
	'ACP_BBTIPS_PROVIDER_WOW'        => 'Infobulles World of Warcraft',
	'ACP_BBTIPS_PROVIDER_DIABLO4'    => 'Infobulles Diablo 4',

	'ACP_BBTIPS_TAGS'                => 'Balises BBCode',
	'ACP_BBTIPS_TAG_ITEM'            => 'Infobulles d\'objet ([item])',
	'ACP_BBTIPS_TAG_ITEMICO'         => 'Infobulles d\'icône d\'objet ([itemico])',
	'ACP_BBTIPS_TAG_SPELL'           => 'Infobulles de sort ([spell])',
	'ACP_BBTIPS_TAG_QUEST'           => 'Infobulles de quête ([quest])',
	'ACP_BBTIPS_TAG_CRAFT'           => 'Infobulles d\'artisanat/recette ([craft])',
	'ACP_BBTIPS_TAG_ACHIEVEMENT'     => 'Infobulles de haut fait ([achievement])',
	'ACP_BBTIPS_TAG_ITEMSET'         => 'Infobulles de set d\'objets ([itemset])',
	'ACP_BBTIPS_TAG_NPC'             => 'Infobulles de PNJ ([npc])',
	'ACP_BBTIPS_TAG_D4ITEM'          => 'Infobulles d\'objet Diablo 4 ([d4item])',
	'ACP_BBTIPS_TAG_D4SKILL'         => 'Infobulles de compétence Diablo 4 ([d4skill])',

	'ACP_BBTIPS_BEHAVIOR'            => 'Comportement',
	'ACP_BBTIPS_WOW_DOMAIN'          => 'Domaine WoW par défaut',
	'ACP_BBTIPS_WOW_DOMAIN_EXPLAIN'  => 'Vide = retail. Exemples : classic, ptr, de, ru, de.classic.',
	'ACP_BBTIPS_SCOPE'               => 'Charger le script d\'infobulles sur',
	'ACP_BBTIPS_SCOPE_ALL'           => 'Toutes les pages du forum',
	'ACP_BBTIPS_SCOPE_POSTS'         => 'Messages du forum uniquement',
	'ACP_BBTIPS_RUNTIME_ENABLED'     => 'Activer le script d\'infobulles WowHead (tiers)',

	'ACP_BBTIPS_DISPLAY'             => 'Affichage des liens',
	'ACP_BBTIPS_COLOR_LINKS'         => 'Colorer les liens d\'infobulle selon la qualité de l\'objet',
	'ACP_BBTIPS_ICONIZE_LINKS'       => 'Afficher une icône à côté des liens d\'infobulle',
	'ACP_BBTIPS_RENAME_LINKS'        => 'Remplacer le texte du lien par le nom actuel de l\'objet/du sort',

	'LOG_BBTIPS_SETTINGS'            => '<strong>Paramètres bbTips mis à jour</strong>',
]);

$lang = array_merge($lang, [
	'ACP_BBTIPS_PROVIDER_WOW_EXPLAIN'     => 'Active les BBCodes d’infobulle World of Warcraft (item, spell, quest, achievement, etc.).',
	'ACP_BBTIPS_PROVIDER_DIABLO4_EXPLAIN' => 'Active les BBCodes d’infobulle Diablo 4 ([d4item], [d4skill]).',
	'ACP_BBTIPS_TAG_ITEM_EXPLAIN'         => 'Exemple : [item]50468[/item]. Attributs facultatifs : domain, ench, gems, pcs.',
	'ACP_BBTIPS_TAG_ITEMICO_EXPLAIN'      => 'Affiche l’icône de l’objet. Exemple : [itemico size=medium]50468[/itemico].',
	'ACP_BBTIPS_TAG_SPELL_EXPLAIN'        => 'Exemple : [spell]17[/spell].',
	'ACP_BBTIPS_TAG_QUEST_EXPLAIN'        => 'Exemple : [quest]12345[/quest].',
	'ACP_BBTIPS_TAG_CRAFT_EXPLAIN'        => 'Infobulle de recette/fabrication. Exemple : [craft]46351[/craft].',
	'ACP_BBTIPS_TAG_ACHIEVEMENT_EXPLAIN'  => 'Exemple : [achievement]892[/achievement].',
	'ACP_BBTIPS_TAG_ITEMSET_EXPLAIN'      => 'Exemple : [itemset]861[/itemset].',
	'ACP_BBTIPS_TAG_NPC_EXPLAIN'          => 'Exemple : [npc]11502[/npc].',
	'ACP_BBTIPS_TAG_D4ITEM_EXPLAIN'       => 'Exemple : [d4item]444429[/d4item].',
	'ACP_BBTIPS_TAG_D4SKILL_EXPLAIN'      => 'Exemple : [d4skill]1[/d4skill].',
	'ACP_BBTIPS_SCOPE_EXPLAIN'            => 'Emplacement où le script d’infobulle WowHead est chargé. « Toutes les pages du forum » inclut aussi les pages app.php comme bbGuild.',
	'ACP_BBTIPS_RUNTIME_ENABLED_EXPLAIN'  => 'Charge le script tiers tooltips.js de WowHead. Désactivez-le pour un forum conforme au RGPD ; les liens fonctionnent toujours comme de simples liens WowHead.',
	'ACP_BBTIPS_COLOR_LINKS_EXPLAIN'      => 'Colore chaque lien d’infobulle selon la qualité de l’objet.',
	'ACP_BBTIPS_ICONIZE_LINKS_EXPLAIN'    => 'Affiche une petite icône à côté de chaque lien d’infobulle.',
	'ACP_BBTIPS_RENAME_LINKS_EXPLAIN'     => 'Remplace l’identifiant numérique dans le texte du lien par le nom réel.',
]);
