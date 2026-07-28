<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\provider;

class diablo4_provider extends abstract_wowhead_provider
{
	/** bbcode tag => diablo-4 path segment */
	const TYPES = ['d4item' => 'item', 'd4skill' => 'skill'];

	public function get_id(): string
	{
		return 'diablo4';
	}

	public function get_game_name(): string
	{
		return 'Diablo 4';
	}

	public function build_link(string $type, $id, array $opts = []): string
	{
		if (!ctype_digit((string) $id))
		{
			return '';
		}
		// Accept either a bbcode-tag key (d4item/d4skill) or a raw segment value (item/skill); default to 'item'.
		$seg  = in_array($type, self::TYPES, true) ? $type : (self::TYPES[$type] ?? 'item');
		$href = 'https://www.wowhead.com/diablo-4/' . $seg . '/' . $id;
		// D4 tooltips are decorated from the href; no data-wowhead needed.
		return '<a href="' . htmlspecialchars($href, ENT_QUOTES) . '">'
			. htmlspecialchars((string) ($opts['text'] ?? $id), ENT_QUOTES) . '</a>';
	}

	public function get_tags(): array
	{
		$tags = [];
		foreach (self::TYPES as $bbcode => $seg)
		{
			$B = strtoupper($bbcode);
			$usage = "[$B]{UINT}[/$B]";
			$template = "<a href=\"https://www.wowhead.com/diablo-4/$seg/{@content}\"><xsl:apply-templates/></a>";
			$tags[] = ['bbcode' => $bbcode, 'usage' => $usage, 'template' => $template, 'help' => 'BBTIPS_HELP_' . $B];
		}
		return $tags;
	}

	public function get_primary_tags(): array
	{
		return ['d4item', 'd4skill'];
	}
}
