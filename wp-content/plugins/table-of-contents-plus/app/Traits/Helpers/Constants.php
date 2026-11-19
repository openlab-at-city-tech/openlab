<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains constant specific helper methods.
 *
 * @since 1.0.0
 */
trait Constants {
	/**
	 * Returns the plugin menu icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string The icon as a string.
	 */
	public function icon() {
		return '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16" cy="6" r="2.8" stroke="white" stroke-width="2.4"/><path d="M16 8.8V26.6M10.4 12.2H21.6" stroke="white" stroke-width="2.6" stroke-linecap="round"/><path d="M7 16.4H4.3a11.7 11.7 0 0 0 23.4 0H25" stroke="white" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded
	}
}