<?php
namespace avathar\bbtips\tests\service;

use PHPUnit\Framework\TestCase;
use avathar\bbtips\service\toolbar_builder;
use avathar\bbtips\service\tag_gate;
use avathar\bbtips\provider\provider_registry;

class toolbar_builder_test extends TestCase
{
	/** Minimal fake provider implementing the interface surface the builder uses. */
	private function provider(string $id, string $game, array $bbcodes, array $primary): \avathar\bbtips\provider\tooltip_provider_interface
	{
		return new class($id, $game, $bbcodes, $primary) implements \avathar\bbtips\provider\tooltip_provider_interface {
			public function __construct(private string $id, private string $game, private array $bbcodes, private array $primary) {}
			public function get_id(): string { return $this->id; }
			public function get_game_name(): string { return $this->game; }
			public function get_tags(): array {
				return array_map(fn($b) => ['bbcode'=>$b,'usage'=>'','template'=>'','help'=>'BBTIPS_HELP_'.strtoupper($b)], $this->bbcodes);
			}
			public function get_primary_tags(): array { return $this->primary; }
			public function build_link(string $type, $id, array $opts = []): string { return ''; }
			public function get_runtime_asset(): array { return ['url'=>'','config'=>[]]; }
		};
	}

	private function lang(): \phpbb\language\language
	{
		$l = $this->createMock(\phpbb\language\language::class);
		$l->method('lang')->willReturnCallback(fn($k) => 'HELP:' . $k);
		return $l;
	}

	private function build(array $providers, array $config): array
	{
		$registry = new provider_registry($providers);
		$gate = new tag_gate(new \phpbb\config\config($config));
		return (new toolbar_builder($registry, $gate, $this->lang()))->build();
	}

	public function test_headline_uses_primary_tags_rest_grouped(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item','itemico','spell','quest'], ['item','spell']);
		$out = $this->build([$wow], [
			'bbtips_provider_wow' => '1', 'bbtips_provider_diablo4' => '0',
		]);

		$this->assertTrue($out['has_any']);
		$this->assertSame(['item','spell'], array_column($out['headline'], 'bbcode'));
		$this->assertSame('[item]', $out['headline'][0]['open']);
		$this->assertSame('[/item]', $out['headline'][0]['close']);
		$this->assertSame('HELP:BBTIPS_HELP_ITEM', $out['headline'][0]['help']);

		$wowGroup = $out['groups'][0];
		$this->assertSame('World of Warcraft', $wowGroup['game']);
		$this->assertSame(['itemico','quest'], array_column($wowGroup['options'], 'bbcode'));
	}

	public function test_disabled_tags_excluded(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item','spell'], ['item','spell']);
		$out = $this->build([$wow], [
			'bbtips_provider_wow' => '1', 'bbtips_provider_diablo4' => '0', 'bbtips_tag_spell' => '0',
		]);
		$this->assertSame(['item'], array_column($out['headline'], 'bbcode'));
		$this->assertSame([], $out['groups'][0]['options'] ?? []);
	}

	public function test_fallback_when_no_primary_enabled(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item','spell','quest','npc'], ['item','spell']);
		$out = $this->build([$wow], [
			'bbtips_provider_wow' => '1', 'bbtips_provider_diablo4' => '0',
			'bbtips_tag_item' => '0', 'bbtips_tag_spell' => '0',
		]);
		$this->assertSame(['quest','npc'], array_column($out['headline'], 'bbcode'));
	}

	public function test_disabled_provider_absent_and_diablo_only(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item','spell'], ['item','spell']);
		$d4  = $this->provider('diablo4', 'Diablo 4', ['d4item','d4skill'], ['d4item','d4skill']);
		$out = $this->build([$wow, $d4], [
			'bbtips_provider_wow' => '0', 'bbtips_provider_diablo4' => '1',
		]);
		$this->assertSame(['d4item','d4skill'], array_column($out['headline'], 'bbcode'));
		$this->assertSame([], array_column($out['groups'], 'game'));
	}

	public function test_has_any_false_when_nothing_enabled(): void
	{
		$wow = $this->provider('wow', 'World of Warcraft', ['item'], ['item']);
		$out = $this->build([$wow], ['bbtips_provider_wow' => '0', 'bbtips_provider_diablo4' => '0']);
		$this->assertFalse($out['has_any']);
		$this->assertSame([], $out['headline']);
	}
}
