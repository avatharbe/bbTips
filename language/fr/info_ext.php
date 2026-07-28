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
	'BBTIPS_PHP_VERSION_FAIL'   => 'bbTips nécessite PHP %1$s ou supérieur. Vous utilisez %2$s.',
	'BBTIPS_PHPBB_VERSION_FAIL' => 'bbTips nécessite phpBB %1$s ou supérieur. Vous utilisez %2$s.',
]);
