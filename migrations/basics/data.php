<?php
/**
 * bbTips Extension — install migration
 *
 * Seeds config defaults (provider toggles, per-tag toggles, behavior/display
 * flags) and registers the ACP category + module.
 *
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace avathar\bbtips\migrations\basics;

class data extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['bbtips_scope']);
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v33x\v3311'];
	}

	public function update_data()
	{
		return [
			// providers
			['config.add', ['bbtips_provider_wow', 1]],
			['config.add', ['bbtips_provider_diablo4', 1]],
			// tags (default on)
			['config.add', ['bbtips_tag_item', 1]],
			['config.add', ['bbtips_tag_itemico', 1]],
			['config.add', ['bbtips_tag_spell', 1]],
			['config.add', ['bbtips_tag_quest', 1]],
			['config.add', ['bbtips_tag_craft', 1]],
			['config.add', ['bbtips_tag_achievement', 1]],
			['config.add', ['bbtips_tag_itemset', 1]],
			['config.add', ['bbtips_tag_npc', 1]],
			['config.add', ['bbtips_tag_d4item', 1]],
			['config.add', ['bbtips_tag_d4skill', 1]],
			// behavior
			['config.add', ['bbtips_wow_domain', '']],
			['config.add', ['bbtips_scope', 'all']],
			['config.add', ['bbtips_runtime_enabled', 1]],
			['config.add', ['bbtips_color_links', 1]],
			['config.add', ['bbtips_iconize_links', 1]],
			['config.add', ['bbtips_rename_links', 1]],
			// ACP
			['module.add', ['acp', 'ACP_CAT_DOT_MODS', 'ACP_CAT_BBTIPS']],
			['module.add', ['acp', 'ACP_CAT_BBTIPS', [
				'module_basename' => '\avathar\bbtips\acp\main_module',
				'modes'           => ['settings'],
			]]],
		];
	}
}
