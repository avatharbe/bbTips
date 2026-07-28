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
	'BBTIPS_PHP_VERSION_FAIL'   => 'bbTips benötigt PHP %1$s oder neuer. Sie verwenden %2$s.',
	'BBTIPS_PHPBB_VERSION_FAIL' => 'bbTips benötigt phpBB %1$s oder neuer. Sie verwenden %2$s.',
]);
