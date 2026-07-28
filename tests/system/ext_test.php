<?php
namespace avathar\bbtips\tests\system;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\ext;

class ext_test extends TestCase
{
	public function test_version_constant_is_set(): void
	{
		$this->assertNotEmpty(ext::BBTIPS_VERSION);
		$this->assertSame('8.1.0', ext::MIN_PHP_VERSION);
	}
}
