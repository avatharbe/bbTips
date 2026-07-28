<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\service;

use avathar\bbtips\provider\provider_registry;
use phpbb\language\language;

/**
 * Builds the posting-toolbar data (headline buttons + grouped dropdown options)
 * from the enabled providers and tags. Parsing is unaffected — this is UI only.
 */
class toolbar_builder
{
	/** Maximum number of always-visible headline buttons. */
	const MAX_HEADLINE = 2;

	/** @var provider_registry */
	protected $registry;

	/** @var tag_gate */
	protected $gate;

	/** @var language */
	protected $language;

	public function __construct(provider_registry $registry, tag_gate $gate, language $language)
	{
		$this->registry = $registry;
		$this->gate = $gate;
		$this->language = $language;
	}

	/**
	 * @return array{headline:array, groups:array, has_any:bool}
	 */
	public function build(): array
	{
		// Ordered map of enabled tags: bbcode => ['bbcode','open','close','help','game']
		$enabled = [];
		$primary_order = [];

		foreach ($this->registry->all() as $id => $provider)
		{
			if (!$this->gate->provider_enabled($id))
			{
				continue;
			}

			$primary = $provider->get_primary_tags();
			$game = $provider->get_game_name();

			foreach ($provider->get_tags() as $tag)
			{
				$bbcode = $tag['bbcode'];
				if (!$this->gate->tag_enabled($bbcode))
				{
					continue;
				}

				$enabled[$bbcode] = [
					'bbcode' => $bbcode,
					'open'   => '[' . $bbcode . ']',
					'close'  => '[/' . $bbcode . ']',
					'help'   => $this->language->lang($tag['help']),
					'game'   => $game,
				];
			}

			foreach ($primary as $bbcode)
			{
				if (isset($enabled[$bbcode]) && !in_array($bbcode, $primary_order, true))
				{
					$primary_order[] = $bbcode;
				}
			}
		}

		if (empty($enabled))
		{
			return ['headline' => [], 'groups' => [], 'has_any' => false];
		}

		$headline_keys = array_slice($primary_order, 0, self::MAX_HEADLINE);
		if (empty($headline_keys))
		{
			$headline_keys = array_slice(array_keys($enabled), 0, self::MAX_HEADLINE);
		}
		$headline_set = array_flip($headline_keys);

		$headline = [];
		foreach ($headline_keys as $bbcode)
		{
			$row = $enabled[$bbcode];
			unset($row['game']);
			$headline[] = $row;
		}

		$groups = [];
		$index = [];
		foreach ($enabled as $bbcode => $row)
		{
			if (isset($headline_set[$bbcode]))
			{
				continue;
			}
			$game = $row['game'];
			if (!isset($index[$game]))
			{
				$index[$game] = count($groups);
				$groups[] = ['game' => $game, 'options' => []];
			}
			unset($row['game']);
			$groups[$index[$game]]['options'][] = $row;
		}

		return ['headline' => $headline, 'groups' => $groups, 'has_any' => true];
	}
}
