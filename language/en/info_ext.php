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
	'BBTIPS_PHP_VERSION_FAIL'   => 'bbTips requires PHP %1$s or newer. You are running %2$s.',
	'BBTIPS_PHPBB_VERSION_FAIL' => 'bbTips requires phpBB %1$s or newer. You are running %2$s.',
]);
