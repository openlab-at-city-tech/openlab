<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains helper methods for other plugins/themes.
 *
 * @since 1.0.0
 */
trait ThirdParty {
	/**
	 * Checks whether WooCommerce is active.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean Whether WooCommerce is active.
	 */
	public function isWooCommerceActive() {
		return class_exists( 'woocommerce' );
	}

	/**
	 * Checks whether the queried object is the WooCommerce shop page.
	 *
	 * @since 1.0.0
	 *
	 * @param  int  $id The post ID to check against (optional).
	 * @return bool     Whether the current page is the WooCommerce shop page.
	 */
	public function isWooCommerceShopPage( $id = 0 ) {
		if ( ! $this->isWooCommerceActive() ) {
			return false;
		}

		if ( ! is_admin() && ! aioseoTableOfContents()->helpers->isAjaxCronRestRequest() && function_exists( 'is_shop' ) ) {
			return is_shop();
		}

		$id = ! $id && ! empty( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : (int) $id; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return $id && wc_get_page_id( 'shop' ) === $id;
	}
}