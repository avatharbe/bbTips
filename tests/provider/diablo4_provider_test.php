<?php
namespace avathar\bbtips\tests\provider;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\provider\diablo4_provider;

class diablo4_provider_test extends TestCase
{
	private diablo4_provider $p;
	protected function setUp(): void { $this->p = new diablo4_provider(); }

	public function test_id(): void { $this->assertSame('diablo4', $this->p->get_id()); }

	public function test_item_link(): void
	{
		$html = $this->p->build_link('item', 444429);
		$this->assertStringContainsString('/diablo-4/item/444429', $html);
	}

	public function test_shares_wowhead_runtime(): void
	{
		$this->assertSame('https://wow.zamimg.com/js/tooltips.js', $this->p->get_runtime_asset()['url']);
	}

	public function test_tags(): void
	{
		$this->assertSame(['d4item','d4skill'], array_column($this->p->get_tags(), 'bbcode'));
	}

	public function test_skill_link_via_bbcode_tag(): void
	{
		$this->assertStringContainsString('/diablo-4/skill/7', $this->p->build_link('d4skill', 7));
	}

	public function test_skill_link_via_segment(): void
	{
		$this->assertStringContainsString('/diablo-4/skill/7', $this->p->build_link('skill', 7));
	}

	public function test_tags_carry_help_keys(): void
	{
		foreach ((new \avathar\bbtips\provider\diablo4_provider())->get_tags() as $tag)
		{
			$this->assertSame('BBTIPS_HELP_' . strtoupper($tag['bbcode']), $tag['help']);
		}
	}

	public function test_primary_tags(): void
	{
		$this->assertSame(['d4item', 'd4skill'], (new \avathar\bbtips\provider\diablo4_provider())->get_primary_tags());
	}
}
