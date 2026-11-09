<?php
/**
 * Regex processor for the Replace action.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.2.4
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Replace;

use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Abstract_Regex_Processor;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Replace_Regex_Processor
 *
 * Uses preg_replace_callback to locate and rewrite broken link hrefs in-place.
 * Returns the modified content string.
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Replace
 */
class Replace_Regex_Processor extends Abstract_Regex_Processor {

	/**
	 * Scans $content with a regex and rewrites any matching href to $new_link.
	 *
	 * @param string $content Source content to process.
	 * @param string $link    The broken link URL.
	 * @param string $new_link The new link URL to replace the broken link with.
	 * @param array  $tags    An array of target tags and attributes to process.
	 * @return string The modified content string.
	 */
	public function process( string $content, string $link, string $new_link, array $tags ): string {
		foreach ( $tags as $tag_name => $tag_atts ) {
			foreach ( $tag_atts as $tag_att ) {
				$regexp = $this->get_pattern( $tag_name, $tag_att );

				$content = preg_replace_callback(
					"/$regexp/siU",
					function ( $found ) use ( $link, $new_link, $tag_att ) {
						$old_link = untrailingslashit( trim( $found[2], '\'"' ) );

						if ( $this->link_processor->links_match( $link, $old_link ) ) {
							/*
							 * The old link is quoted before it goes into the pattern - a URL
							 * carrying ( ) + or ? would otherwise be read as regex syntax - and
							 * the new one is put in through a callback, so neither a $ in the URL
							 * nor a backreference sequence is interpreted. esc_url() encodes the
							 * quote characters, so the value cannot close the attribute it sits in.
							 */
							return preg_replace_callback(
								'#(' . preg_quote( $tag_att, '#' ) . '=["\'])' . preg_quote( $old_link, '#' ) . '/?(["\'])#i',
								static function ( $parts ) use ( $new_link ) {
									return $parts[1] . esc_url( $new_link ) . $parts[2];
								},
								$found[0],
								1
							);
						}

						return $found[0];
					},
					$content
				);
			}
		}

		return $content;
	}
}
