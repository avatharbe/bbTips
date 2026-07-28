<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\service;

use phpbb\config\config;

/**
 * Single source of truth for which providers and tags are enabled.
 * Shared by the s9e bbcode registration and the posting-toolbar helper.
 */
class tag_gate
{
	/** @var config */
	protected $config;

	public function __construct(config $config)
	{
		$this->config = $config;
	}

	public function provider_enabled(string $id): bool
	{
		return (bool) (int) $this->config['bbtips_provider_' . $id];
	}

	public function tag_enabled(string $bbcode): bool
	{
		$key = 'bbtips_tag_' . $bbcode;

		return !isset($this->config[$key]) || (int) $this->config[$key] === 1;
	}
}
