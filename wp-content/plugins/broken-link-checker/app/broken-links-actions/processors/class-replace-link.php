<?php
/**
 * Executes the `Replace` action on Broken Links.
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
class Replace_Link extends Base {

	public function execute( string $content = '', string $link = '', string $new_link = '' ) {
		if ( empty( $this->get_target_tags() ) ) {
			return $content;
		}

		$link = trim( $link, '\'"' );

		foreach ( $this->get_target_tags() as $tag_name => $tag_atts ) {
			if ( ! empty( $tag_atts ) ) {
				foreach ( $tag_atts as $tag_att ) {
					//  /<a\s[^>]*href=["'](.*?)["']>(.*)/gm      : 231 steps, 0,1 ms
					//  /<a\s[^>]*href=["'](.*?)["']>(.*)<\/a>/gm : 255 steps, 0,1 ms
					// $regexp = "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/{$tag_name}>";
					$regexp  = "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)[^>]*>(.*)<\/{$tag_name}>";
					$content = preg_replace_callback(
						"/$regexp/siU",
						function ( $match ) use ( $link, $new_link, $tag_att ) {
							$old_link = untrailingslashit( trim( $match[2], '\'"' ) );

							if ( $old_link === $link ) {
								// Let's not use str_replace, so we avoid replacing url in tag content or other tag atts.
								// We have stripped trailing slashes, but we use `/?` in regex as link might have or might not have trailing slash.
								return preg_replace( "#({$tag_att}=[\"|\'])" . $old_link . '/?(["|\'])#i', '\1' . $new_link . '\2', $match[0], 1 );
							} else {
								return $match[0];
							}
						},
						$content
					);
				}
			}
		}

		return $content;

		/*
		if ( ! class_exists( '\blcUtility' ) ) {
			include_once BLC_DIRECTORY_LEGACY . '/includes/utility-class.php';
		}

		foreach ( $this->get_target_tags() as $tag_name => $tag_att ) {
			// Use the same method that Local BLC uses.
			// We're setting `selfclosing` (3rd param) to `null` so that can be determined by method.
			$content_links = \blcUtility::extract_tags( $content, $tag_name, null, true );


			if ( ! empty( $content_links ) ) {
				$offset = 0;

				foreach ( $content_links as $content_link ) {
					$content = substr_replace( $content, $new_link, $content_link['offset'] + $offset, strlen( $content_link['full_tag'] ) );

					//Update the replacement offset
					$offset += ( strlen( $new_link ) - strlen( $content_link['full_tag'] ) );
				}
			}
		}
		*/
	}

	public function get_target_tags() {

		return apply_filters(
			'wpmudev_blc_replace_target_tags',
			array(
				'a' => array( 'href' ),
				//'img'    => array( 'src', 'srcset ),
				//'iframe' => 'src'
			)
		);
	}

}
