<?php
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}
$lang = array_merge($lang, [
	'BBTIPS_PHP_VERSION_FAIL'   => 'bbTips vereist PHP %1$s of nieuwer. U gebruikt %2$s.',
	'BBTIPS_PHPBB_VERSION_FAIL' => 'bbTips vereist phpBB %1$s of nieuwer. U gebruikt %2$s.',
]);
