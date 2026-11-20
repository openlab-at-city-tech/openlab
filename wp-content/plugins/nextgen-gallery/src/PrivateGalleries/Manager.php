<?php
/**
 * Registers the private galleries feature.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

/**
 * Private galleries: a gallery shown only on its password protected client page.
 */
class Manager {

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		Visibility::register_hooks();
		Gate::register_hooks();
		PageExclusions::register_hooks();
		CachePurge::register_hooks();
	}

	/**
	 * Whether a displayed gallery shows a private gallery.
	 *
	 * @param object $displayed_gallery Displayed gallery.
	 * @return bool
	 */
	public static function shows_private_gallery( $displayed_gallery ) {
		if ( empty( Registry::get_all() ) ) {
			return false;
		}

		$source = isset( $displayed_gallery->source ) ? (string) $displayed_gallery->source : '';

		if ( in_array( $source, [ 'galleries', 'gallery' ], true ) ) {
			foreach ( (array) $displayed_gallery->container_ids as $id ) {
				if ( is_numeric( $id ) && Registry::is_private( $id ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
