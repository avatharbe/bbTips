<?php
/**
 * bbTips Extension
 * @copyright (c) 2026 avathar.be
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */
namespace avathar\bbtips;

use phpbb\extension\base;

class ext extends base
{
	const BBTIPS_VERSION    = '2.0.0-rc3';
	const MIN_PHP_VERSION   = '8.1.0';
	const MIN_PHPBB_VERSION = '3.3.11';

	public function is_enableable()
	{
		$errors = [];
		$user = $this->container->get('user');
		$user->add_lang_ext('avathar/bbtips', 'info_ext');

		if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<'))
		{
			$errors[] = $user->lang('BBTIPS_PHP_VERSION_FAIL', self::MIN_PHP_VERSION, PHP_VERSION);
		}
		if (phpbb_version_compare(PHPBB_VERSION, self::MIN_PHPBB_VERSION, '<'))
		{
			$errors[] = $user->lang('BBTIPS_PHPBB_VERSION_FAIL', self::MIN_PHPBB_VERSION, PHPBB_VERSION);
		}
		return empty($errors) ? true : $errors;
	}
}
