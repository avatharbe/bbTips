<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\provider;

interface tooltip_provider_interface
{
	/** Unique provider id, e.g. 'wow', 'diablo4'. */
	public function get_id(): string;

	/** Human-readable game name for the ACP. */
	public function get_game_name(): string;

	/**
	 * BBCode tag specs for s9e addCustom().
	 * @return array<int, array{bbcode:string, usage:string, template:string, help:string}>
	 */
	public function get_tags(): array;

	/**
	 * Ordered list of this provider's bbcode keys to surface as headline
	 * toolbar buttons, most important first. Empty = no headline preference.
	 * @return string[]
	 */
	public function get_primary_tags(): array;

	/** Build an anchor for the given entity. */
	public function build_link(string $type, $id, array $opts = []): string;

	/**
	 * Client runtime asset needed by this provider.
	 * @return array{url:string, config:array}
	 */
	public function get_runtime_asset(): array;
}
