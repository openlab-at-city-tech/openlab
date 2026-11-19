<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains methods related to multisites.
 *
 * @since 1.0.0
 */
trait WpMultisite {
	/**
	 * Returns the current site.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Site|Object A WP_Site instance of the current site or an object representing the same.
	 */
	public function getSite() {
		if ( is_multisite() ) {
			return get_site();
		}

		return (object) [
			'domain' => $this->getSiteDomain(),
			'path'   => $this->getHomePath()
		];
	}

	/**
	 * Returns the network ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int The integer of the blog/site id.
	 */
	public function getNetworkId() {
		if ( is_multisite() ) {
			return get_network()->site_id;
		}

		return get_current_blog_id();
	}

	/**
	 * Wrapper for switch_to_blog especially for non-multisite setups.
	 *
	 * @since 1.0.0
	 *
	 * @param  int  $blogId The blog ID to switch to.
	 * @return bool         True in all cases.
	 */
	public function switchToBlog( $blogId ) {
		if ( ! is_multisite() ) {
			return true;
		}

		return switch_to_blog( $blogId );
	}

	/**
	 * Wrapper for restore_current_blog especially for non-multisite setups.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether we're already on the current blog or not in a multisite environment.
	 */
	public function restoreCurrentBlog() {
		if ( ! is_multisite() ) {
			return false;
		}

		return restore_current_blog();
	}
}