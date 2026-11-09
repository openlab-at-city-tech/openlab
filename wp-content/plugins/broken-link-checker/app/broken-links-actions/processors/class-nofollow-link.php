<?php
/**
 * Executes the `Nofollow` action on Broken Links.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class Nofollow_Link
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors
 */
class Nofollow_Link extends Base {

	/**
	 * Modifies the content by adding `nofollow` to the target links.
	 * Returns the modified content string.
	 *
	 * @param string $content The content to process.
	 * @param string $link The link to be modified.
	 * @param string $new_link The new link to replace the old link with (Replace action only).
	 * @return string The modified content with `nofollow` added to the target links.
	 */
	public function execute( string $content = '', string $link = '', string $new_link = '' ) {
		if ( empty( $this->get_target_tags() ) ) {
			return $content;
		}

		$link         = untrailingslashit( trim( $link, '\'"' ) );
		$replacements = array();

		foreach ( $this->get_target_tags() as $tag_name => $tag_atts ) {
			if ( ! empty( $tag_atts ) ) {
				foreach ( $tag_atts as $tag_att ) {
					$regexp = "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/{$tag_name}>";

					if ( preg_match_all( "/$regexp/siU", $content, $matches ) ) {

						if ( ! empty( $matches[0] ) ) {
							foreach ( $matches[0] as $key => $markup ) {
								$content_link = untrailingslashit( trim( $matches[2][ $key ], '\'"' ) );

								if ( $content_link === $link ) {
									$replacements[ $markup ] = $this->add_nofollow( $markup );
								}
							}
						}
					}
				}
			}
		}

		return empty( $replacements ) ? $content : str_replace( array_keys( $replacements ), array_values( $replacements ), $content );
	}

	public function add_nofollow( string $input = '' ) {
		$dom = new \DOMDocument;

		$dom->loadHTML( $input );

		$anchors = $dom->getElementsByTagName( 'a' );

		foreach ( $anchors as $anchor ) {
			$rel = array();

			if ( $anchor->hasAttribute( 'rel' ) && ( $rel_arr = $anchor->getAttribute( 'rel' ) ) !== '' ) {
				$rel = preg_split( '/\s+/', trim( $rel_arr ) );
			}

			if ( in_array( 'nofollow', $rel ) ) {
				continue;
			}

			$rel[] = 'nofollow';
			$anchor->setAttribute( 'rel', implode( ' ', $rel ) );
		}

		$dom->saveHTML();

		$html = '';

		foreach ( $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes as $element ) {
			$html .= $dom->saveXML( $element, LIBXML_NOEMPTYTAG );
		}

		return $html;
	}

	public function get_target_tags() {

		return apply_filters(
			'wpmudev_blc_replace_target_tags',
			array(
				'a' => array( 'href' ),
			)
		);
	}

}
