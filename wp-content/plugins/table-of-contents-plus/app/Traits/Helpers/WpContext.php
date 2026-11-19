<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains all context related helper methods.
 * This includes methods to check the context of the current request, but also get WP objects.
 *
 * @since 1.0.0
 */
trait WpContext {
	/**
	 * Checks whether we're on the given screen.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $screenName The screen name.
	 * @return boolean             Whether we're on the given screen.
	 */
	public function isScreenBase( $screenName ) {
		$screen = $this->getCurrentScreen();
		if ( ! $screen || ! isset( $screen->base ) ) {
			return false;
		}

		return $screen->base === $screenName;
	}

	/**
	 * Gets current admin screen
	 *
	 * @since 1.0.0
	 *
	 * @return false|\WP_Screen|null
	 */
	public function getCurrentScreen() {
		if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		return get_current_screen();
	}
}