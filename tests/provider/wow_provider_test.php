<?php
namespace avathar\bbtips\tests\provider;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\provider\wow_provider;

class wow_provider_test extends TestCase
{
	private wow_provider $p;
	protected function setUp(): void
	{
		$config = $this->createMock(\phpbb\config\config::class);
		$config->method('offsetGet')->willReturn('');
		$config->method('offsetExists')->willReturn(true);
		$this->p = new wow_provider($config);
	}

	public function test_id_and_name(): void
	{
		$this->assertSame('wow', $this->p->get_id());
		$this->assertStringContainsString('Warcraft', $this->p->get_game_name());
	}

	public function test_item_link_minimal(): void
	{
		$html = $this->p->build_link('item', 50468);
		$this->assertStringContainsString('href="https://www.wowhead.com/item=50468"', $html);
		$this->assertStringContainsString('data-wowhead="item=50468"', $html);
	}

	public function test_item_link_with_opts_uses_amp_encoded_params(): void
	{
		$html = $this->p->build_link('item', 50468, ['domain' => 'classic', 'ench' => 3825, 'gems' => '40133']);
		$this->assertStringContainsString('data-wowhead="item=50468&amp;domain=classic&amp;ench=3825&amp;gems=40133"', $html);
	}

	public function test_build_link_rejects_non_numeric_id(): void
	{
		$this->assertSame('', $this->p->build_link('item', 'Ardent Guard'));
	}

	public function test_get_tags_covers_wow_set(): void
	{
		$bbcodes = array_column($this->p->get_tags(), 'bbcode');
		$this->assertSame(['item','itemico','spell','quest','craft','achievement','itemset','npc'], $bbcodes);
	}

	public function test_tags_carry_help_keys(): void
	{
		foreach ($this->p->get_tags() as $tag)
		{
			$this->assertArrayHasKey('help', $tag);
			$this->assertSame('BBTIPS_HELP_' . strtoupper($tag['bbcode']), $tag['help']);
		}
	}

	public function test_primary_tags_are_item_and_spell(): void
	{
		$this->assertSame(['item', 'spell'], $this->p->get_primary_tags());
	}
}
