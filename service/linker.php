<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\service;

use avathar\bbtips\provider\provider_registry;

class linker
{
	/** @var provider_registry */
	protected $registry;

	public function __construct(provider_registry $registry)
	{
		$this->registry = $registry;
	}

	public function for(string $id): ?\avathar\bbtips\provider\tooltip_provider_interface
	{
		return $this->registry->get($id);
	}

	public function wow(): ?\avathar\bbtips\provider\tooltip_provider_interface
	{
		return $this->registry->get('wow');
	}
}
