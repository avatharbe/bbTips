<?php
namespace avathar\bbtips\tests\provider;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\provider\provider_registry;
use avathar\bbtips\provider\tooltip_provider_interface;

class provider_registry_test extends TestCase
{
	private function fake(string $id): tooltip_provider_interface
	{
		$p = $this->createMock(tooltip_provider_interface::class);
		$p->method('get_id')->willReturn($id);
		return $p;
	}

	public function test_indexes_providers_by_id(): void
	{
		$reg = new provider_registry([$this->fake('wow'), $this->fake('diablo4')]);
		$this->assertSame('wow', $reg->get('wow')->get_id());
		$this->assertNull($reg->get('gw2'));
		$this->assertSame(['wow', 'diablo4'], array_keys($reg->all()));
	}

	public function test_enabled_filters_by_id_list(): void
	{
		$reg = new provider_registry([$this->fake('wow'), $this->fake('diablo4')]);
		$this->assertSame(['wow'], array_keys($reg->enabled(['wow'])));
	}
}
