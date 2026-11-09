<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Contracts;

use WP_Error;

/**
 * Strategy interface for enabling, disabling, and checking
 * the active state of a Feature.
 *
 * Each Strategy instance is bound to a single Feature at construction time.
 * The Strategy_Factory creates the right Strategy for each Feature.
 *
 * @since 1.0.0
 */
interface Strategy {

	/**
	 * Enables the feature.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function enable();

	/**
	 * Disables the feature.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function disable();

	/**
	 * Updates the feature to the latest available version.
	 *
	 * Returns a WP_Error if the feature is not installed or active,
	 * if no update is available, or if the update fails.
	 *
	 * @since 1.0.0
	 *
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function update();

	/**
	 * Checks whether the feature is currently active.
	 *
	 * Implementations should check live state rather than a cached flag.
	 * If the live state differs from any stored flag, the stored flag
	 * should be updated to match (self-healing).
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the feature is currently active.
	 */
	public function is_active(): bool;
}
