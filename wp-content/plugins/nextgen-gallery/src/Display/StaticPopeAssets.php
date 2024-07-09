<?php

namespace Imagely\NGG\Display;

use Imagely\NGG\DataMapper\WPModel;
use Imagely\NGG\Settings\Settings;

class StaticPopeAssets extends StaticAssets {

	/**
	 * @param $filename
	 * @param false|string $legacy_module_id
	 * @return string
	 */
	public static function get_computed_abspath( $filename, $legacy_module_id = false ) {
		if ( strpos( $filename, '#' ) !== false ) {
			$parts = explode( '#', $filename );
			if ( \count( $parts ) === 2 ) {
				$filename         = $parts[1];
				$legacy_module_id = $parts[0];
			} else {
				$filename = $parts[0];
			}
		} elseif ( strpos( $legacy_module_id, '#' ) !== false ) {
			$parts = explode( '#', $legacy_module_id );
			if ( \count( $parts ) === 2 ) {
				$legacy_module_id = $parts[0];
			}
		}

		$filename = self::trim_preceding_slash( $filename );

		$static_dir = self::trim_preceding_slash( Settings::get_instance()->get( 'mvc_static_dir', '/static' ) );

		$override_dir = \wp_normalize_path( self::get_override_dir( $legacy_module_id ) );
		$override     = \path_join( $override_dir, $filename );
		if ( @\stream_resolve_include_path( $override ) ) {
			return $override;
		}

		// Find the POPE modules root.
		$module_dir = \C_NextGEN_Bootstrap::get_legacy_module_directory( $legacy_module_id );
		if ( ! empty( $module_dir ) ) { // To avoid PHP deprecated warnings.
			$module_dir = \wp_normalize_path( $module_dir );
		}

		// In case NextGen is in a symlink we make $mod_dir relative to the NGG parent root and then rebuild it
		// using WP_PLUGIN_DIR; without this NGG-in-symlink creates URL that reference the file abspath.
		if ( \is_link( \path_join( WP_PLUGIN_DIR, \basename( NGG_PLUGIN_DIR ) ) ) ) {
			$module_dir = \ltrim( \str_replace( \dirname( NGG_PLUGIN_DIR ), '', $module_dir ), DIRECTORY_SEPARATOR );
			$module_dir = \path_join( WP_PLUGIN_DIR, $module_dir );
		}

		if ( ! empty( $module_dir ) ) { // To avoid PHP deprecated warnings.
			$retval = \path_join(
				\path_join( $module_dir, $static_dir ),
				$filename
			);
		} else {
			$retval = '';
		}

		if ( ! is_null( $retval ) ) {
			// Adjust for windows paths.
			return \wp_normalize_path( $retval );
		} else {
			return $retval;
		}
	}

	/**
	 * @param string|null $module_id
	 * @return string $dir
	 */
	public static function get_override_dir( $module_id = null ) {
		$root = \trailingslashit( \path_join( WP_CONTENT_DIR, 'ngg' ) );
		if ( ! @\file_exists( $root ) && \is_writable( \trailingslashit( WP_CONTENT_DIR ) ) ) {
			\wp_mkdir_p( $root );
		}

		$modules = \trailingslashit( \path_join( $root, 'modules' ) );

		if ( ! @\file_exists( $modules ) && \is_writable( $root ) ) {
			\wp_mkdir_p( $modules );
		}

		if ( $module_id ) {
			$module_dir = \trailingslashit( \path_join( $modules, $module_id ) );
			if ( ! @\file_exists( $module_dir ) && \is_writable( $modules ) ) {
				\wp_mkdir_p( $module_dir );
			}

			$static_dir = \trailingslashit( \path_join( $module_dir, 'static' ) );
			if ( ! @\file_exists( $static_dir ) && \is_writable( $module_dir ) ) {
				\wp_mkdir_p( $static_dir );
			}

			return $static_dir;
		}

		return $modules;
	}

	/**
	 * @param string $str
	 * @return string
	 */
	public static function trim_preceding_slash( $str ) {
		return \preg_replace( '#^/{1,2}#', '', $str, 1 );
	}
}
