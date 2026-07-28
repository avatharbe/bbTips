<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\acp;

class main_info
{
	public function module()
	{
		return [
			'filename' => '\avathar\bbtips\acp\main_module',
			'title'    => 'ACP_CAT_BBTIPS',
			'version'  => '2.0.0',
			'modes'    => [
				'settings' => [
					'title' => 'ACP_BBTIPS_SETTINGS',
					'auth'  => 'ext_avathar/bbtips && acl_a_board',
					'cat'   => ['ACP_CAT_BBTIPS'],
				],
			],
		];
	}
}
