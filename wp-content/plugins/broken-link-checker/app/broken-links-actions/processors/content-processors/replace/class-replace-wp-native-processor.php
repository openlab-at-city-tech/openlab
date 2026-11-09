<?php
/**
 * WP_HTML_Tag_Processor-based content processor for the Replace action.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.2.4
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Replace
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Replace;

use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Abstract_Content_Processor;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Replace_Wp_Native_Processor
 *
 * Uses WordPress's native WP_HTML_Tag_Processor (available since WP 6.2) to
 * replace broken link hrefs in-place without loading a full DOMDocument.
 *
 * Returns the (possibly modified) content string on success, or null when
 * WP_HTML_Tag_Processor is not available so the caller can fall back to DOM.
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Replace
 */
class Replace_Wp_Native_Processor extends Abstract_Content_Processor {

	/**
	 * Checks whether the WP native processor is available in the current environment.
	 *
	 * @return bool
	 */
	public function is_available(): bool {
		return class_exists( '\WP_HTML_Tag_Processor' );
	}

	/**
	 * Rewrites matching hrefs using WP_HTML_Tag_Processor.
	 *
	 * @param string $content  Source content to process.
	 * @param string $link     The broken link URL.
	 * @param string $new_link The new link URL to replace the broken link with.
	 * @param array  $tags     An array of target tags and attributes to process.
	 * @return string|null Modified content string, or null when unavailable.
	 */
	public function process( string $content, string $link, string $new_link, array $tags ) {
		if ( ! $this->is_available() ) {
			return null;
		}

		foreach ( $tags as $tag_name => $tag_atts ) {
			foreach ( $tag_atts as $tag_att ) {
				$processor = new \WP_HTML_Tag_Processor( $content );

				while ( $processor->next_tag( array( 'tag_name' => $tag_name ) ) ) {
					$old_link = untrailingslashit( trim( $processor->get_attribute( $tag_att ), '\'"' ) );

					if ( $this->link_processor->links_match( $link, $old_link ) ) {
						$processor->set_attribute( $tag_att, $new_link );

						if ( apply_filters( 'wpmudev_blc_link_action_edit_wrap', false, $link, $new_link ) ) {
							$existing_class = $processor->get_attribute( 'class' );
							$processor->set_attribute(
								'class',
								apply_filters(
									'wpmudev_blc_link_action_unlink_wrap_class',
									empty( $existing_class ) ? 'blc_edited' : "{$existing_class} blc_edited",
									$link,
									$new_link
								)
							);
							$processor->set_attribute( 'data-blc-orig-url', $link );
						}

						$content = $processor->get_updated_html();
					}
				}
			}
		}

		return $content;
	}
}
