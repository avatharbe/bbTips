<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\provider;

class provider_registry
{
	/** @var tooltip_provider_interface[] indexed by id */
	private $providers = [];

	/** @param iterable $providers tagged bbtips.tooltip_provider services */
	public function __construct(iterable $providers)
	{
		foreach ($providers as $provider)
		{
			$this->providers[$provider->get_id()] = $provider;
		}
	}

	public function get(string $id): ?tooltip_provider_interface
	{
		return $this->providers[$id] ?? null;
	}

	/** @return tooltip_provider_interface[] id => provider */
	public function all(): array
	{
		return $this->providers;
	}

	/**
	 * @param array $enabled_ids provider ids enabled in config
	 * @return tooltip_provider_interface[] id => provider
	 */
	public function enabled(array $enabled_ids): array
	{
		return array_intersect_key($this->providers, array_flip($enabled_ids));
	}
}
