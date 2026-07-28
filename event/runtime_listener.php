<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\event;

use avathar\bbtips\provider\provider_registry;
use phpbb\config\config;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class runtime_listener implements EventSubscriberInterface
{
	protected $registry;
	protected $config;
	protected $template;
	protected $user;

	public function __construct(provider_registry $registry, config $config, template $template, user $user)
	{
		$this->registry = $registry;
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return ['core.page_header' => 'inject_runtime'];
	}

	public function inject_runtime($event)
	{
		if (!(int) $this->config['bbtips_runtime_enabled'] || !$this->in_scope())
		{
			return;
		}

		$srcs = [];
		$cfg  = [];
		foreach ($this->registry->enabled($this->enabled_provider_ids()) as $provider)
		{
			$asset = $provider->get_runtime_asset();
			$srcs[$asset['url']] = true;             // de-dupe by URL
			$cfg = array_merge($cfg, $asset['config']);
		}
		if (empty($srcs))
		{
			return;
		}

		// Apply admin display overrides.
		$cfg['colorLinks']   = (bool) (int) $this->config['bbtips_color_links'];
		$cfg['iconizeLinks'] = (bool) (int) $this->config['bbtips_iconize_links'];
		$cfg['renameLinks']  = (bool) (int) $this->config['bbtips_rename_links'];

		$this->template->assign_vars([
			'S_BBTIPS_RUNTIME'       => true,
			'BBTIPS_WHTOOLTIPS_JSON' => json_encode($cfg),
		]);
		foreach (array_keys($srcs) as $src)
		{
			$this->template->assign_block_vars('bbtips_runtime_srcs', ['SRC' => $src]);
		}
	}

	private function in_scope(): bool
	{
		if ($this->config['bbtips_scope'] === 'all')
		{
			return true;
		}
		$page = $this->user->page['page_name'] ?? '';
		return in_array($page, ['viewtopic.php', 'posting.php'], true);
	}

	private function enabled_provider_ids(): array
	{
		$ids = [];
		if ((int) $this->config['bbtips_provider_wow'])
		{
			$ids[] = 'wow';
		}
		if ((int) $this->config['bbtips_provider_diablo4'])
		{
			$ids[] = 'diablo4';
		}
		return $ids;
	}
}
