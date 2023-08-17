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
					// Dom is preferred method, but it will "fix" any broken/corrupt markup which in some cases the broken markup might be intentional. Therefor by default we use regex.
					if ( apply_filters( 'wpmudev_blc_link_action_use_dom', false, 'replace', $link ) ) {
						$dom  = new \DOMDocument();
						$html = '';

						libxml_use_internal_errors( true );

						$dom->loadHTML( $content );

						foreach ( $dom->getElementsByTagName( 'a' ) as $dom_link ) {
							$old_link = $dom_link->getAttribute( "href" );

							if ( strcasecmp( trailingslashit( $old_link ), trailingslashit( $link ) ) == 0 ) {
								$dom_link->setAttribute( 'href', $new_link );
							}
						}

						$dom->saveHtml();

						foreach ( $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes as $element ) {
							$html .= $dom->saveXML( $element, LIBXML_NOEMPTYTAG );
						}

						if ( ! empty( $html ) ) {
							$content = $html;
						}
					} else {
						// Regex is default way, even if not preferred. The reason we use regex by default is because, DOM will fix any invalid markup which might not be desired.
						//  /<a\s[^>]*href=["'](.*?)["']>(.*)/gm      : 231 steps, 0,1 ms
						//  /<a\s[^>]*href=["'](.*?)["']>(.*)<\/a>/gm : 255 steps, 0,1 ms
						// $regexp = "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/{$tag_name}>";
						$regexp  = "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)[^>]*>(.*)<\/{$tag_name}>";
						$content = preg_replace_callback(
							"/$regexp/siU",
							function ( $match ) use ( $link, $new_link, $tag_att ) {
								$old_link    = untrailingslashit( trim( $match[2], '\'"' ) );
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

								//if ( $old_link === $link ) {
								if ( $links_match ) {
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
