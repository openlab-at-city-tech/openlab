<?php
/**
 * Provides common functions
 *
 * @package BadgeOS
 * @subpackage Credly
 * @author LearningTimes, LLC
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 * @link https://credly.com
 */

/**
 * Class badgeos_utilities
 */
class badgeos_utilities {

	/**
	 * self instance.
	 *
	 * @var self.
	 */
	private static $instance = null;

	/**
	 * Contructor
	 */
	public function __construct() {

	}

	/**
	 * Check if badgeos is network wide active.
	 *
	 * @return bool
	 */
	public function is_network_wide_active() {

		if ( is_multisite() && ! is_main_site() ) {
			return true;
		}

		return false;
	}

	/**
	 * Helper function to get an option value.
	 *
	 * @param string $option_name option name.
	 *
	 * @return mixed
	 */
	public static function get_option( $option_name ) {

		$settings       = array();
		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() ) {
			$settings = get_site_option( $option_name );
		} else {
			$settings = get_option( $option_name );
		}

		return $settings;
	}

	/**
	 * Helper function to get an option value.
	 *
	 * @param string $option_name option name.
	 * @param any    $value value.
	 *
	 * @return mixed
	 */
	public static function update_option( $option_name, $value = false ) {

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() ) {
			return update_site_option( $option_name, $value );
		} else {
			return update_option( $option_name, $value );
		}
	}

	/**
	 * Helper function to delete an option.
	 *
	 * @param string $option_name option name.
	 *
	 * @return mixed
	 */
	public static function delete_option( $option_name ) {

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() ) {
			return delete_site_option( $option_name, $value );
		} else {
			return delete_option( $option_name );
		}
	}

	/**
	 * Helper function to get an user meta.
	 *
	 * @param int    $user_id user ID.
	 * @param string $meta_key meta key.
	 * @param any    $single single.
	 *
	 * @return mixed
	 */
	public static function get_user_meta( $user_id, $meta_key = '', $single = true ) {

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() ) {
			return get_user_option( $meta_key, $user_id );
		} else {
			return get_user_meta( $user_id, $meta_key, $single );
		}

	}

	/**
	 * Helper function to update an user meta.
	 *
	 * @param int    $user_id user ID.
	 * @param string $meta_key meta key.
	 * @param any    $meta_value meta value.
	 * @param any    $prev_value previous value.
	 *
	 * @return mixed
	 */
	public static function update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() ) {
			return update_user_option( $user_id, $meta_key, $meta_value, true );
		} else {
			return update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
		}

	}

	/**
	 * Helper function to delete an user meta.
	 *
	 * @param int    $user_id user ID.
	 * @param string $meta_key meta key.
	 * @param any    $meta_value meta value.
	 *
	 * @return bool
	 */
	public static function delete_user_meta( $user_id, $meta_key, $meta_value = '' ) {

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() ) {
			return delete_user_option( $user_id, $meta_key, true );
		} else {
			return delete_user_meta( $user_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Helper function to get a post meta.
	 *
	 * @param int    $post_id post ID.
	 * @param string $meta_key meta key.
	 * @param bool   $single single.
	 *
	 * @return mixed
	 */
	public static function get_post_meta( $post_id, $meta_key = '', $single = true ) {

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() ) {

			// Switch to main site.
			switch_to_blog( get_current_blog_id() );

			// Get the post meta.
			$value = get_post_meta( $post_id, $meta_key, $single );
			// Restore current site.
			restore_current_blog();

			return $value;

		} else {
			$value = get_post_meta( $post_id, $meta_key, $single );
			return $value;
		}
	}

	/**
	 * Helper function to update a post meta.
	 *
	 * @param int    $post_id post ID.
	 * @param string $meta_key meta key.
	 * @param any    $meta_value meta value.
	 * @param any    $prev_value previous value.
	 *
	 * @return integer|bool
	 */
	public static function update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() && ! is_main_site() ) {

			// Switch to main site.
			switch_to_blog( get_current_blog_id() );
			$result = update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );

			// Restore current site.
			restore_current_blog();

			return $result;

		} else {
			return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
		}
	}

	/**
	 * Helper function to delete a post meta.
	 *
	 * @param int    $post_id post ID.
	 * @param string $meta_key meta key.
	 * @param any    $meta_value meta value.
	 *
	 * @return bool
	 */
	public static function del_post_meta( $post_id, $meta_key, $meta_value = '' ) {

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() && ! is_main_site() ) {

			// Switch to main site.
			switch_to_blog( get_main_site_id() );

			$result = delete_post_meta( $post_id, $meta_key, $meta_value );
			// Restore current site.
			restore_current_blog();

			return $result;

		} else {
			$result = delete_post_meta( $post_id, $meta_key, $meta_value );
			return $result;
		}

	}

	/**
	 * Helper function to get a post.
	 *
	 * @param int $post_id post ID.
	 *
	 * @return false|WP_Post
	 */
	public static function badgeos_get_post( $post_id ) {

		// if we got a post object, then return their field.
		if ( $post_id instanceof WP_Post ) {
			return $post_id;
		}

		global $wpdb;

		self::$instance = new self();
		if ( self::$instance->is_network_wide_active() && ! is_main_site() ) {

			$post = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT * FROM ' . $wpdb->prefix . 'posts WHERE ID = %d',
					absint( $post_id )
				)
			);

			return $post;

		} else {
			return get_post( $post_id );
		}
	}

	/**
	 * Helper function to get a post type.
	 *
	 * @param int $post_id post ID.
	 *
	 * @return false|WP_Post
	 */
	public static function get_post_type( $post_id ) {

		// if we got a post object, then return their field.
		if ( is_multisite() ) {
			switch_to_blog( get_current_blog_id() );
		}

		$result = get_post_type( $post_id );

		// Restore current site.
		if ( is_multisite() ) {
			restore_current_blog();
		}

		return $result;
	}
}
