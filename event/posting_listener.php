<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\event;

use avathar\bbtips\service\toolbar_builder;
use phpbb\language\language;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Injects the bbTips insertion helper into the posting editor toolbar.
 * Fires only on the posting page (core.posting_modify_bbcode_status), so it
 * never touches other pages. UI only — parsing is handled elsewhere.
 */
class posting_listener implements EventSubscriberInterface
{
	/** @var toolbar_builder */
	protected $builder;

	/** @var template */
	protected $template;

	/** @var language */
	protected $language;

	public function __construct(toolbar_builder $builder, template $template, language $language)
	{
		$this->builder = $builder;
		$this->template = $template;
		$this->language = $language;
	}

	static public function getSubscribedEvents()
	{
		return ['core.posting_modify_bbcode_status' => 'inject_toolbar'];
	}

	public function inject_toolbar($event)
	{
		// Only meaningful when BBCode is allowed in this forum.
		if (empty($event['bbcode_status']))
		{
			return;
		}

		$this->language->add_lang('bbtips', 'avathar/bbtips');

		$data = $this->builder->build();
		if (!$data['has_any'])
		{
			return;
		}

		$this->template->assign_var('S_BBTIPS_TOOLBAR', true);

		foreach ($data['headline'] as $btn)
		{
			$this->template->assign_block_vars('bbtips_headline', [
				'TAG'   => $btn['bbcode'],
				'OPEN'  => $btn['open'],
				'CLOSE' => $btn['close'],
				'HELP'  => $btn['help'],
			]);
		}

		foreach ($data['groups'] as $group)
		{
			$this->template->assign_block_vars('bbtips_group', [
				'GAME' => $group['game'],
			]);

			foreach ($group['options'] as $opt)
			{
				$this->template->assign_block_vars('bbtips_group.opt', [
					'TAG'   => $opt['bbcode'],
					'OPEN'  => $opt['open'],
					'CLOSE' => $opt['close'],
					'HELP'  => $opt['help'],
				]);
			}
		}
	}
}
