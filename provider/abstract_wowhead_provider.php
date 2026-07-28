<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\provider;

abstract class abstract_wowhead_provider implements tooltip_provider_interface
{
	const RUNTIME_URL = 'https://wow.zamimg.com/js/tooltips.js';

	public function get_runtime_asset(): array
	{
		return [
			'url'    => self::RUNTIME_URL,
			'config' => ['colorLinks' => true, 'iconizeLinks' => true, 'renameLinks' => true],
		];
	}

	public function get_primary_tags(): array
	{
		return [];
	}

	/**
	 * Build an <a href data-wowhead="..."> anchor.
	 * @param string $href full URL
	 * @param array  $data ordered data-wowhead params; empty values dropped
	 * @param string $text visible text
	 */
	protected function wowhead_anchor(string $href, array $data, string $text): string
	{
		$pairs = [];
		foreach ($data as $k => $v)
		{
			if ($v === '' || $v === null)
			{
				continue;
			}
			$pairs[] = $k . '=' . $v;
		}
		$attr = implode('&', $pairs);
		return '<a href="' . htmlspecialchars($href, ENT_QUOTES) . '"'
			. ' data-wowhead="' . htmlspecialchars($attr, ENT_QUOTES) . '">'
			. htmlspecialchars($text, ENT_QUOTES) . '</a>';
	}
}
