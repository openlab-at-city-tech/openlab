<?php
/**
 * Regex processor for the Unlink action.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.2.4
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Unlink
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Unlink;

use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Abstract_Regex_Processor;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Unlink_Regex_Processor
 *
 * Uses preg_match_all to locate broken link tags and returns an array of
 * (old_markup => inner_text) replacement pairs compatible with the
 * `wpmudev_blc_unlink_replacements` filter.
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Unlink
 */
class Unlink_Regex_Processor extends Abstract_Regex_Processor {

	/**
	 * Uses a stricter pattern with a backreference (\\1) so that the opening and
	 * closing quote characters around the attribute value must match.
	 *
	 * @param string $tag_name The name of the HTML tag to process.
	 * @param string $tag_att  The attribute of the HTML tag to process.
	 * @return string The regex pattern for matching the specified tag and attribute.
	 */
	protected function get_pattern( string $tag_name, string $tag_att ): string {
		return "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/{$tag_name}>";
	}

	/**
	 * Scans $content for matching tags and returns a (search => replacement) array.
	 *
	 * @param string $content Source content to process.
	 * @param string $link The broken link URL.
	 * @param string $new_link Unused for unlink.
	 * @param array  $tags An array of target tags and attributes to process.
	 * @return array Array of (old_markup => replacement_str) pairs for str_replace.
	 */
	public function process( string $content, string $link, string $new_link, array $tags ): array {
		$replacements = array();

		foreach ( $tags as $tag_name => $tag_atts ) {
			foreach ( $tag_atts as $tag_att ) {
				$regexp = $this->get_pattern( $tag_name, $tag_att );

				if ( preg_match_all( "/$regexp/siU", $content, $matches ) ) {
					if ( ! empty( $matches[0] ) ) {
						foreach ( $matches[0] as $key => $markup ) {
							$old_link = untrailingslashit( trim( $matches[2][ $key ], '\'"' ) );

							if ( $this->link_processor->links_match( $link, $old_link ) ) {
								$replacements[ $markup ] = $matches[3][ $key ];
							}
						}
					}
				}
			}
		}

		return $replacements;
	}
}
