<?php
/**
 * @copyright (c) 2026 avathar.be
 * @license GNU General Public License, version 2 (GPL-2.0)
 */
namespace avathar\bbtips\provider;

class wow_provider extends abstract_wowhead_provider
{
	/** bbcode tag => wowhead entity type */
	const TYPES = [
		'item' => 'item', 'itemico' => 'item', 'spell' => 'spell', 'quest' => 'quest',
		'craft' => 'spell', 'achievement' => 'achievement', 'itemset' => 'item-set', 'npc' => 'npc',
	];

	/** @var \phpbb\config\config */
	protected $config;

	public function __construct(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	public function get_id(): string
	{
		return 'wow';
	}

	public function get_game_name(): string
	{
		return 'World of Warcraft';
	}

	public function build_link(string $type, $id, array $opts = []): string
	{
		if (!ctype_digit((string) $id))
		{
			return '';
		}
		$wtype  = self::TYPES[$type] ?? $type;
		$href   = 'https://www.wowhead.com/' . $wtype . '=' . $id;
		$domain = $opts['domain'] ?? (string) $this->config['bbtips_wow_domain'];
		$data   = [
			$wtype  => $id,
			'domain' => $domain,
			'ench'   => $opts['ench'] ?? '',
			'gems'   => is_array($opts['gems'] ?? null) ? implode(':', $opts['gems']) : ($opts['gems'] ?? ''),
			'pcs'    => $opts['pcs'] ?? '',
		];
		return $this->wowhead_anchor($href, $data, (string) ($opts['text'] ?? $id));
	}

	public function get_tags(): array
	{
		$tags = [];
		foreach (array_keys(self::TYPES) as $bbcode)
		{
			$tags[] = $this->tag_spec($bbcode, self::TYPES[$bbcode], $bbcode === 'itemico');
		}
		return $tags;
	}

	/**
	 * Build an s9e addCustom() usage+template pair for one tag.
	 * Attributes are optional; empty ones are omitted from data-wowhead via XSL.
	 */
	private function tag_spec(string $bbcode, string $wtype, bool $icon): array
	{
		$B = strtoupper($bbcode);
		// Board-wide default WoW domain (applied when a tag omits domain=).
		$default = (string) $this->config['bbtips_wow_domain'];
		// Usage: numeric content + optional named attrs.
		// gems/pcs accept colon-separated numeric lists (e.g. 40133:40132); a REGEXP token permits ':'.
		$usage = "[$B domain={SIMPLETEXT1;optional} ench={UINT;optional}"
			. " gems={REGEXP=/^\\d+(:\\d+)*\$/;optional} pcs={REGEXP=/^\\d+(:\\d+)*\$/;optional}"
			. ($icon ? " size={SIMPLETEXT2;optional}" : "")
			. "]{UINT2}[/$B]";
		// Domain emission: per-tag @domain wins; else fall back to the configured board default (if any).
		if ($default !== '')
		{
			$domain_xsl = "<xsl:choose>"
				. "<xsl:when test=\"@domain\">&amp;domain=<xsl:value-of select=\"@domain\"/></xsl:when>"
				. "<xsl:otherwise>&amp;domain=" . htmlspecialchars($default, ENT_QUOTES) . "</xsl:otherwise>"
				. "</xsl:choose>";
		}
		else
		{
			$domain_xsl = "<xsl:if test=\"@domain\">&amp;domain=<xsl:value-of select=\"@domain\"/></xsl:if>";
		}
		// Template: href + data-wowhead assembled with xsl conditionals.
		$data = "$wtype=<xsl:value-of select=\"@content\"/>"
			. $domain_xsl
			. "<xsl:if test=\"@ench\">&amp;ench=<xsl:value-of select=\"@ench\"/></xsl:if>"
			. "<xsl:if test=\"@gems\">&amp;gems=<xsl:value-of select=\"@gems\"/></xsl:if>"
			. "<xsl:if test=\"@pcs\">&amp;pcs=<xsl:value-of select=\"@pcs\"/></xsl:if>";
		$icon_attr = $icon ? "<xsl:if test=\"@size\"><xsl:attribute name=\"data-wh-icon-size\"><xsl:value-of select=\"@size\"/></xsl:attribute></xsl:if>" : "";
		$template = "<a href=\"https://www.wowhead.com/$wtype={@content}\">"
			. "<xsl:attribute name=\"data-wowhead\">$data</xsl:attribute>$icon_attr"
			. "<xsl:apply-templates/></a>";
		return ['bbcode' => $bbcode, 'usage' => $usage, 'template' => $template, 'help' => 'BBTIPS_HELP_' . $B];
	}

	public function get_primary_tags(): array
	{
		return ['item', 'spell'];
	}
}
