<?php
/**
 * DOMDocument-based content processor for the Unlink action.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.4.9
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Unlink
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Unlink;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Abstract_Content_Processor;

/**
 * WordPress unlink processor.
 *
 * Handles unlink operations for content processed by Broken Link Checker.
 */
class Unlink_DOM_Processor extends Abstract_Content_Processor {

	/**
	 * Rewrites matching hrefs using DOMDocument.
	 *
	 * @param string $content  Source content to process.
	 * @param string $link     The broken link URL.
	 * @param string $new_link The new link URL to replace the broken link with.
	 * @param array  $tags     An array of target tags and attributes to process.
	 * @return array List of (old_markup => replacement_str) pairs for str_replace.
	 */
	public function process( string $content, string $link, string $new_link, array $tags ) {
		if ( empty( $content ) ) {
			return array();
		}

		$dom          = new \DOMDocument();
		$replacements = array();

		libxml_use_internal_errors( true );

		$dom->loadHTML( $content );

		foreach ( $tags as $tag_name => $tag_atts ) {
			foreach ( $tag_atts as $tag_att ) {
				foreach ( $dom->getElementsByTagName( $tag_name ) as $dom_link ) {
					$search_markup   = '';
					$replacement_str = '';
					$link_text       = '';
					$old_link        = $dom_link->getAttribute( $tag_att );

					$link     = untrailingslashit( trim( $link, '\'"' ) );
					$old_link = untrailingslashit( trim( $old_link, '\'"' ) );

					if ( $this->link_processor->links_match( $link, $old_link ) ) {
						$search_markup = $dom->saveHTML( $dom_link );

						/*
						 * nodeValue hands back the anchor text with entities already decoded, so text
						 * that merely looked like markup in the source ("&lt;img ...&gt;") would be
						 * written back into post_content as live markup. Serialise it through a text
						 * node so it returns escaped.
						 */
						$link_text       = $dom_link->nodeValue; //phpcs:ignore -- Using camelCase to match the DOMDocument property name.
						$replacement_str = $dom->saveHTML( $dom->createTextNode( $link_text ) );

						// If already processed this exact markup, skip to avoid redundant callbacks and replacements.
						if ( ! empty( $replacements[ $search_markup ] ) ) {
							continue;
						}

						$special_actions = $this->link_processor->content_special_actions( $content );

						if ( ! empty( $special_actions ) ) {
							$replacement_str = $search_markup;
							// Loop through any special actions and apply their callbacks to the replacement string.
							foreach ( $special_actions as $special_case_key => $callback ) {
								if ( is_callable( $callback ) ) {
									$replacement_str = call_user_func(
										$callback,
										$replacement_str,
									);
								}
							}

							$replacements[ $search_markup ] = $replacement_str;

							continue;
						}

						// If the 'unlink_wrap' filter is enabled, wrap the inner text in a <span>
						// with a class and data attribute for the original URL.
						// This allows styling of unlinked items and preserves the original URL in a data attribute.
						if ( apply_filters( 'wpmudev_blc_link_action_unlink_wrap', false, $link, $new_link ) ) {

							$replacement_el = $dom->createElement( 'span' );
							$replacement_el->appendChild( $dom->createTextNode( $link_text ) );
							$replacement_el->setAttribute( 'class', apply_filters( 'wpmudev_blc_link_action_unlink_wrap_class', 'blc_unlinked', $link, $new_link ) );
							$replacement_el->setAttribute( 'data-blc-orig-url', $link );
							$replacement_str = $dom->saveHTML( $replacement_el );
						}

						$replacements[ $search_markup ] = $replacement_str;
					}
				}
			}
		}

		return $replacements;
	}
}
