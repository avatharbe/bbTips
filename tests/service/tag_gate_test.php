<?php
namespace avathar\bbtips\tests\service;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\service\tag_gate;

class tag_gate_test extends TestCase
{
	private function config(array $values): \phpbb\config\config
	{
		// phpbb\config\config implements ArrayAccess over a plain array.
		return new \phpbb\config\config($values);
	}

	public function test_provider_enabled_reads_provider_key(): void
	{
		$gate = new tag_gate($this->config(['bbtips_provider_wow' => '1', 'bbtips_provider_diablo4' => '0']));
		$this->assertTrue($gate->provider_enabled('wow'));
		$this->assertFalse($gate->provider_enabled('diablo4'));
	}

	public function test_tag_enabled_defaults_true_when_key_absent(): void
	{
		$gate = new tag_gate($this->config([]));
		$this->assertTrue($gate->tag_enabled('item'));
	}

	public function test_tag_enabled_false_when_key_zero(): void
	{
		$gate = new tag_gate($this->config(['bbtips_tag_item' => '0']));
		$this->assertFalse($gate->tag_enabled('item'));
	}
}
