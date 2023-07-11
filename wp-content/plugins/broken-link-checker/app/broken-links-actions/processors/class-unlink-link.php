<?php
/**
 * Executes the `Unlink` action on Broken Links.
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
 * Class Scan_Data
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors
 */
class Unlink_Link extends Base {

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
								$old_link    = untrailingslashit( trim( $matches[2][ $key ], '\'"' ) );
								$links_match = false;

								/**
								 * If link doesn't have Host part, it is internal link. All internal links are stored as relative urls in Queue.
								 * If link does have the Host part, then it's an external link.
								 * For internal links we need to test 3 cases:
								 * 1. Relative urls (that is how it is stored in Queue)
								 * 2. Absolute url.
								 * 3. Absolute url without scheme ( //site.com )
								 * Engine treats all those types as full urls
								 */
								if ( empty( wp_parse_url( $link, PHP_URL_HOST ) ) ) {
									// Check Relative, Absolute and Absolute without host urls for $old_link.
									$site_url           = site_url();
									$scheme             = wp_parse_url( $site_url, PHP_URL_SCHEME ) . ':';
									$absolute_link      = $site_url . $link;
									$semi_absolute_link = str_replace( $scheme, '', $absolute_link );
									$links_match        = strcasecmp( trailingslashit( $link ), trailingslashit( $old_link ) ) == 0 ||
									                      strcasecmp( trailingslashit( $absolute_link ), trailingslashit( $old_link ) ) == 0 ||
									                      strcasecmp( trailingslashit( $semi_absolute_link ), trailingslashit( $old_link ) ) == 0;
								} else {
									// Check $old_link normally.
									$links_match = strcasecmp( trailingslashit( $old_link ), trailingslashit( $link ) ) == 0;
								}

								if ( $links_match ) {
									$replacements[ $markup ] = $matches[3][ $key ];
								}
							}
						}
					}
				}
			}
		}

		return empty( $replacements ) ? $content : str_replace( array_keys( $replacements ), array_values( $replacements ), $content );
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
