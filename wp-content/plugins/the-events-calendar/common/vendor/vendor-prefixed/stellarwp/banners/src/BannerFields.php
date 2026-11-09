<?php declare(strict_types=1);

namespace TEC\Common\Nexcess\Banners;

/**
 * What a banner says and shows. Pass translated copy from the consuming plugin.
 */
final class BannerFields {

	/**
	 * The message, in white.
	 */
	public string $heading = '';

	/**
	 * Follows the heading in blue, on a line of its own where the banner is narrow.
	 */
	public string $body = '';

	/**
	 * The button renders only when both ctaLabel and ctaUrl are set. It opens in a new tab.
	 */
	public string $ctaLabel = '';

	public string $ctaUrl = '';

	/**
	 * Screen-reader label of the dismiss control.
	 */
	public string $dismissLabel = 'Dismiss this banner.';

}
