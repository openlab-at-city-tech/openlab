<?php
/**
 * Shared validation for the gallery storage path (relative to the galleries document root).
 *
 * @package Imagely\NGG\Settings
 */

namespace Imagely\NGG\Settings;

use Imagely\NGG\Util\Filesystem;
use WP_Error;

/**
 * Validates user-provided gallerypath strings for saves and REST updates.
 */
class GalleryPathValidation {

	/**
	 * Validates that a gallerypath is a safe relative path under the galleries root.
	 *
	 * @param mixed $gallerypath Raw value (typically string from form or JSON).
	 * @return true|\WP_Error True if the path is a safe relative path under the galleries root.
	 */
	public static function validate_relative_under_galleries_root( $gallerypath ) {
		if ( ! is_string( $gallerypath ) ) {
			return new WP_Error(
				'invalid_gallerypath',
				__( 'Gallery path must be a string.', 'nggallery' )
			);
		}
		$trimmed = trim( $gallerypath );
		if ( '' === $trimmed ) {
			return new WP_Error(
				'invalid_gallerypath',
				__( 'Gallery path must be a non-empty relative path.', 'nggallery' )
			);
		}

		// SECURITY: refuse stream-wrapper syntax ("://") before path_join()/
		// path_is_absolute() touch the value. A "phar://" path would make the
		// filesystem call open the named archive, which unserialises its metadata
		// object (object injection). A gallery path is always a plain path relative
		// to the galleries root, so a wrapper is never valid here.
		if ( false !== strpos( str_replace( '\\', '/', $trimmed ), '://' ) ) {
			return new WP_Error(
				'invalid_gallerypath',
				__( 'Gallery path may not contain stream-wrapper syntax.', 'nggallery' )
			);
		}

		$fs       = Filesystem::get_instance();
		$root     = wp_normalize_path( $fs->get_document_root( 'galleries' ) );
		$relative = $fs->add_trailing_slash( $trimmed );
		$sections = explode( '/', str_replace( '\\', '/', trim( $relative, '/\\' ) ) );
		// Reject both '..' (parent traversal) and '.' (self reference). A lone '.' resolves the path
		// back to the galleries root, which would widen every downstream boundary to the whole install.
		if ( in_array( '..', $sections, true ) || in_array( '.', $sections, true ) ) {
			return new WP_Error(
				'invalid_gallerypath',
				__( "Gallery paths may not use '.' or '..' to reference the current or parent directories.", 'nggallery' )
			);
		}

		$gallery_abspath = wp_normalize_path( path_join( $root, $relative ) );
		$root_normalized = wp_normalize_path( $root );
		$root_prefixed   = trailingslashit( $root_normalized );
		// PHP 7.4: avoid str_starts_with() (PHP 8+).
		$under_root = ( $gallery_abspath === $root_normalized )
			|| (
				strlen( $gallery_abspath ) >= strlen( $root_prefixed )
				&& 0 === strpos( $gallery_abspath, $root_prefixed )
			);
		if ( ! $under_root ) {
			return new WP_Error(
				'invalid_gallerypath',
				sprintf(
					/* translators: %s: galleries root directory path */
					__( 'Gallery path must be located under %s', 'nggallery' ),
					$root_normalized
				)
			);
		}

		// On multisite, "under the galleries root" still admits another subsite's uploads tree:
		// a main-site administrator could set `uploads/sites/3` here, and a gallery titled `2026`
		// then has `uploads/sites/3/2026` derived for it server-side, so deleting that gallery
		// removes subsite 3's media. Refused at the writer as well as on the deletion side, which
		// is where DataStorage\Manager::path_targets_another_site() is also consulted.
		if ( \Imagely\NGG\DataStorage\Manager::get_instance()->path_targets_another_site( $gallery_abspath ) ) {
			return new WP_Error(
				'invalid_gallerypath',
				__( 'Gallery path may not point inside another site\'s uploads directory.', 'nggallery' )
			);
		}

		return true;
	}
}
