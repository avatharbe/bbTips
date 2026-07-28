<?php
namespace avathar\bbtips\tests\service;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\service\linker;
use avathar\bbtips\provider\provider_registry;
use avathar\bbtips\provider\wow_provider;

class linker_test extends TestCase
{
	public function test_for_and_wow_delegate_to_provider(): void
	{
		$config = $this->createMock(\phpbb\config\config::class);
		$config->method('offsetGet')->willReturn('');
		$config->method('offsetExists')->willReturn(true);
		$reg = new provider_registry([new wow_provider($config)]);
		$linker = new linker($reg);
		$this->assertStringContainsString('item=50468', $linker->wow()->build_link('item', 50468));
		$this->assertSame('wow', $linker->for('wow')->get_id());
		$this->assertNull($linker->for('gw2'));
	}
}
