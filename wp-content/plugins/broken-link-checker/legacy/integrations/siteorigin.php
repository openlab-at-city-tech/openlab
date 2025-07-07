<?php
/**
 * SiteOrigin Page Builder Integration
 *
 * @package Broken_Link_Checker
 */

if ( ! class_exists( 'blcSiteOrigin' ) ) {

	/**
	 * SiteOrigin Page Builder Integration
	 */
	class blcSiteOrigin {

		/**
		 * Constructor
		 */
		protected function __construct() {

			add_filter( 'blc-parser-html-link-content', array( $this, 'decode_html_widget' ) );
			add_filter( 'blc_parser_html_link_pre_content', array( $this, 'decode_html_widget' ) );
			add_filter( 'blc_parser_html_link_post_content', array( $this, 'encode_back_html_widget' ) );
		}

		/**
		 * Instance obtaining method.
		 *
		 * @return static Called class instance.
		 */
		public static function instance() {
			static $instance = null;
			if ( null === $instance ) {
				$instance = new static();
			}

			return $instance;
		}

		/**
		 * Decode the HTML widget content
		 *
		 * @param string $content Content to be decoded.
		 * @return string Decoded content.
		 */
		public function decode_html_widget( $content ) {
			// Match the full widget and extract value.
			$pattern = '/(\[siteorigin_widget class="WP_Widget_Custom_HTML"]<input type="hidden" value=")(.*?)(" \/\>\[\/siteorigin_widget\])/s';

			$content = preg_replace_callback(
				$pattern,
				function ( $matches ) {
					$prefix        = $matches[1];
					$encoded_value = $matches[2];
					$suffix        = $matches[3];

					// Replace encoded anchor with decoded anchor.
					if ( preg_match_all( '/&lt;a.*href.*&gt;.*a&gt;/s', $encoded_value, $anchor_matches ) ) {
						foreach ( $anchor_matches[0] as $encoded_anchor ) {
							$partially_decoded = html_entity_decode( $encoded_anchor, ENT_QUOTES | ENT_HTML5 );
							$decoded_anchor    = stripslashes( $partially_decoded );
							$encoded_value     = str_replace( $encoded_anchor, $decoded_anchor, $encoded_value );
						}
					}

					return $prefix . $encoded_value . $suffix;
				},
				$content
			);

			return $content;
		}

		/**
		 * Encode the HTML widget content back
		 *
		 * @param string $content Modified Post content.
		 * @return string Encoded content.
		 */
		public function encode_back_html_widget( $content ) {
			$pattern = '/(\[siteorigin_widget class="WP_Widget_Custom_HTML"]<input type="hidden" value=")(.*?)(" \/\>\[\/siteorigin_widget\])/s';

			$content = preg_replace_callback(
				$pattern,
				function ( $matches ) {
					$prefix                  = $matches[1];
					$decoded_value_container = $matches[2];
					$suffix                  = $matches[3];

					// Replace normal HTML anchors with encoded anchors.
					$encoded_value = preg_replace_callback(
						'/<a[^>]*?href[^>]*?>.*?<\/a>/s',
						function ( $anchor_matches ) {
							$encoded_anchor = htmlspecialchars( $anchor_matches[0], ENT_QUOTES, 'UTF-8' );
							$encoded_anchor = str_replace( array( '//', '&quot;', '/a&gt' ), array( '\/\/', '\&quot;', '\/a&gt' ), $encoded_anchor );

							return $encoded_anchor;
						},
						$decoded_value_container
					);

					return $prefix . $encoded_value . $suffix;
				},
				$content
			);

			return $content;
		}

		/**
		 * Update the links in SiteOrigin PageBuilder panels_data meta.
		 *
		 * @param string $old_url Old URL to be replaced.
		 * @param string $new_url New URL to replace with.
		 * @param int    $post_id Post ID of the content to update.
		 */
		public function update_blc_links( $old_url, $new_url, $post_id ) {
			$panels_data = get_post_meta( $post_id, 'panels_data', true );

			if ( empty( $panels_data ) || ! is_array( $panels_data ) ) {
				return;
			}

			// Recursively replace URLs in nested arrays. Direct update to Db issues with siteorigin.
			$recursive_replace = function ( $data ) use ( &$recursive_replace, $old_url, $new_url ) {
				if ( is_array( $data ) ) {
					foreach ( $data as $key => $value ) {
						$data[ $key ] = $recursive_replace( $value );
					}
				} elseif ( is_string( $data ) ) {
					$data = str_replace( $old_url, $new_url, $data );
				}
				return $data;
			};

			$updated_panels_data = $recursive_replace( $panels_data );
			update_post_meta( $post_id, 'panels_data', $updated_panels_data );
		}
	}
}
