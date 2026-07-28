<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\acp;

class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $config, $request, $template, $user, $phpbb_log;

		$user->add_lang_ext('avathar/bbtips', 'info_acp_bbtips');
		$this->tpl_name = 'acp_bbtips_settings';
		$this->page_title = $user->lang('ACP_BBTIPS_SETTINGS');
		add_form_key('bbtips_settings');

		$keys_bool = [
			'bbtips_provider_wow', 'bbtips_provider_diablo4', 'bbtips_runtime_enabled',
			'bbtips_color_links', 'bbtips_iconize_links', 'bbtips_rename_links',
			'bbtips_tag_item', 'bbtips_tag_itemico', 'bbtips_tag_spell', 'bbtips_tag_quest',
			'bbtips_tag_craft', 'bbtips_tag_achievement', 'bbtips_tag_itemset', 'bbtips_tag_npc',
			'bbtips_tag_d4item', 'bbtips_tag_d4skill',
		];

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('bbtips_settings'))
			{
				trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}
			foreach ($keys_bool as $k)
			{
				$config->set($k, $request->variable($k, 0));
			}
			$config->set('bbtips_wow_domain', $request->variable('bbtips_wow_domain', ''));
			$config->set('bbtips_scope', $request->variable('bbtips_scope', 'all'));
			// Tag toggles and the default domain are baked into the s9e configuration at
			// text_formatter.cache build time, so invalidate that cache to apply changes on save.
			global $phpbb_container;
			$phpbb_container->get('text_formatter.cache')->invalidate();
			$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_BBTIPS_SETTINGS', false, []);
			trigger_error($user->lang('ACP_BBTIPS_SAVED') . adm_back_link($this->u_action));
		}

		$tpl = [
			'U_ACTION'            => $this->u_action,
			'BBTIPS_WOW_DOMAIN'   => $config['bbtips_wow_domain'],
			'S_SCOPE_ALL'         => $config['bbtips_scope'] === 'all',
			'S_SCOPE_POSTS'       => $config['bbtips_scope'] === 'posts',
		];
		foreach ($keys_bool as $k)
		{
			$tpl['S_' . strtoupper($k)] = (bool) (int) $config[$k];
		}
		$template->assign_vars($tpl);

		// Per-tag toggles are rendered via a template loop.
		$tag_bbcodes = ['item', 'itemico', 'spell', 'quest', 'craft', 'achievement', 'itemset', 'npc', 'd4item', 'd4skill'];
		foreach ($tag_bbcodes as $t)
		{
			$key = 'bbtips_tag_' . $t;
			$template->assign_block_vars('tag', [
				'KEY'     => $key,
				'LABEL'   => $user->lang('ACP_BBTIPS_TAG_' . strtoupper($t)),
				'EXPLAIN' => $user->lang('ACP_BBTIPS_TAG_' . strtoupper($t) . '_EXPLAIN'),
				'ENABLED' => (bool) (int) $config[$key],
			]);
		}
	}
}
