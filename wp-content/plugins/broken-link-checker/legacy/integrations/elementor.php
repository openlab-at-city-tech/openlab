<?php
/**
 * Elementor Page Builder Integration
 *
 * @package Broken_Link_Checker
 */

if ( ! class_exists( 'blcElementor' ) ) {

	/**
	 * Elementor Page Builder Integration
	 */
	class blcElementor {

		/**
		 * Constructor
		 */
		protected function __construct() {
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
		 * Update the the links on elementor pagebuilder
		 *
		 * @param string $old_url  Old URL to be replaced.
		 * @param string $new_url  New URL to replace with.
		 * @param int    $post_id  Post ID of whose content to update.
		 */
		public function update_blc_links( $old_url, $new_url, $post_id ) {
			global $wpdb;

			// Escape the URLs for use in JSON.
			$escaped_old_url = trim( wp_json_encode( $old_url ), '"' );
			$escaped_new_url = trim( wp_json_encode( $new_url ), '"' );

			//phpcs:ignore -- Ignore Cache. 
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->postmeta} 
					SET `meta_value` = REPLACE(`meta_value`, %s, %s) 
					WHERE `meta_key` = '_elementor_data' 
					AND `post_id` = %d 
					AND `meta_value` LIKE %s",
					$escaped_old_url,
					$escaped_new_url,
					$post_id,
					'%' . $wpdb->esc_like( $escaped_old_url ) . '%'
				)
			);
			wp_cache_delete( $post_id, 'post_meta' );
		}
	}
}
