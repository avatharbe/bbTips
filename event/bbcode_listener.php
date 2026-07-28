<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\event;

use avathar\bbtips\provider\provider_registry;
use avathar\bbtips\service\tag_gate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class bbcode_listener implements EventSubscriberInterface
{
	/** @var provider_registry */
	protected $registry;

	/** @var tag_gate */
	protected $gate;

	public function __construct(provider_registry $registry, tag_gate $gate)
	{
		$this->registry = $registry;
		$this->gate = $gate;
	}

	static public function getSubscribedEvents()
	{
		return ['core.text_formatter_s9e_configure_after' => 'register_bbcodes'];
	}

	public function register_bbcodes($event)
	{
		$configurator = $event['configurator'];

		foreach ($this->registry->all() as $id => $provider)
		{
			if (!$this->gate->provider_enabled($id))
			{
				continue;
			}

			foreach ($provider->get_tags() as $tag)
			{
				if (!$this->gate->tag_enabled($tag['bbcode']))
				{
					continue;
				}

				if (isset($configurator->tags[strtoupper($tag['bbcode'])]))
				{
					continue;
				}

				try
				{
					$configurator->BBCodes->addCustom($tag['usage'], $tag['template']);
				}
				catch (\Exception $e)
				{
					// A malformed spec must never break post rendering board-wide.
				}
			}
		}
	}
}
