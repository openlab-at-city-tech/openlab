<?php

namespace Imagely\NGG\DisplayType;

/**
 * Legacy Template Locator
 *
 * Helps locate legacy template files for backward compatibility.
 */
class LegacyTemplateLocator {

	/**
	 * Singleton instance
	 *
	 * @var LegacyTemplateLocator|null
	 */
	public static $instance = null;

	/**
	 * Gets the singleton instance
	 *
	 * @return LegacyTemplateLocator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new LegacyTemplateLocator();
		}
		return self::$instance;
	}

	/**
	 * Returns an array of template storing directories
	 *
	 * @return array Template storing directories
	 */
	public function get_template_directories() {
		return apply_filters(
			'ngg_legacy_template_directories',
			[
				'Child Theme'       => get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'nggallery' . DIRECTORY_SEPARATOR,
				'Parent Theme'      => get_template_directory() . DIRECTORY_SEPARATOR . 'nggallery' . DIRECTORY_SEPARATOR,
				'NextGEN Legacy'    => NGGALLERY_ABSPATH . 'view' . DIRECTORY_SEPARATOR,
				'NextGEN Overrides' => implode(
					DIRECTORY_SEPARATOR,
					[
						WP_CONTENT_DIR,
						'ngg',
						'legacy',
						'templates',
					]
				),
			]
		);
	}

	/**
	 * Returns an array of all available template files
	 *
	 * @param bool|string|array $prefix Optional prefix filter.
	 * @return array All available template files
	 */
	public function find_all( $prefix = false ) {
		$files = [];
		foreach ( $this->get_template_directories() as $label => $dir ) {
			$tmp = $this->get_templates_from_dir( $dir, $prefix );
			if ( ! $tmp ) {
				continue;
			}
			$files[ $label ] = $tmp;
		}

		return $files;
	}

	/**
	 * Recursively scans $dir for files ending in .php
	 *
	 * @param string            $dir Directory to scan.
	 * @param bool|string|array $prefix Optional prefix filter.
	 * @return array All php files in $dir
	 */
	public function get_templates_from_dir( $dir, $prefix = false ) {
		if ( ! is_dir( $dir ) ) {
			return [];
		}

		$dir      = new \RecursiveDirectoryIterator( $dir );
		$iterator = new \RecursiveIteratorIterator( $dir );

		// convert single-item arrays to string.
		if ( is_array( $prefix ) && count( $prefix ) <= 1 ) {
			$prefix = end( $prefix );
		}

		// we can filter results by allowing a set of prefixes, one prefix, or by showing all available files.
		// Note: Legacy templates use lowercase naming (e.g., gallery.php, gallery-caption.php).
		// The regex is case-SENSITIVE to avoid matching View-based templates like Gallery.php.
		if ( is_array( $prefix ) ) {
			$str            = implode( '|', $prefix );
			$regex_iterator = new \RegexIterator( $iterator, "/({$str})-.+\\.php$/", \RecursiveRegexIterator::GET_MATCH );
		} elseif ( is_string( $prefix ) ) {
			$regex_iterator = new \RegexIterator( $iterator, "#(.*)[/\\\\]{$prefix}\\-?.*\\.php$#", \RecursiveRegexIterator::GET_MATCH );
		} else {
			$regex_iterator = new \RegexIterator( $iterator, '/^.+\.php$/', \RecursiveRegexIterator::GET_MATCH );
		}

		$files = [];
		foreach ( $regex_iterator as $filename ) {
			$files[] = reset( $filename );
		}

		return $files;
	}

	/**
	 * Find a particular template by name
	 *
	 * @param string $template_name
	 * @return string
	 */
	public function find( $template_name ) {
		$template_abspath = false;

		// Legacy templates may be an absolute path to a file that was moved in NextGEN 3.50. Here we remap the legacy
		// path to the current one.
		if ( false !== strpos( $template_name, 'nextgen-gallery/products/photocrati_nextgen/modules/ngglegacy' ) ) {
			$template_name = str_replace(
				'nextgen-gallery/products/photocrati_nextgen/modules/ngglegacy',
				'nextgen-gallery/src/Legacy',
				$template_name
			);
		}

		// hook into the render feature to allow other plugins to include templates.
		$custom_template = apply_filters( 'ngg_render_template', false, $template_name );

		if ( $custom_template === false ) {
			$custom_template = $template_name;
		}

		// Ensure we have a PHP extension.
		if ( strpos( $custom_template, '.php' ) === false ) {
			$custom_template .= '.php';
		}

		// SECURITY: Check for directory traversal patterns in ALL cases BEFORE processing.
		// This prevents LFI attacks via shortcode template parameters like "../../../../../../poc".
		// Normalize slashes first to catch mixed separator bypass attempts.
		$normalized_for_check = str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $custom_template );
		if ( preg_match( '#\.\.' . preg_quote( DIRECTORY_SEPARATOR, '#' ) . '#', $normalized_for_check ) ) {
			// Directory traversal attempt detected - do not load this template.
			return false;
		}

		// Get allowed template directories once for reuse.
		$template_dirs = $this->get_template_directories();

		// Find the abspath of the template to render.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! @file_exists( $custom_template ) ) {
			// Template doesn't exist as an absolute path, search through registered directories.
			foreach ( $template_dirs as $dir ) {
				if ( $template_abspath ) {
					break;
				}
				$filename = implode( DIRECTORY_SEPARATOR, [ rtrim( $dir, '/\\' ), $custom_template ] );
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( @file_exists( $filename ) ) {
					$template_abspath = $filename;
				} elseif ( strpos( $custom_template, '-template' ) === false ) {
					$filename = implode(
						DIRECTORY_SEPARATOR,
						[
							rtrim( $dir, '/\\' ),
							str_replace( '.php', '', $custom_template ) . '-template.php',
						]
					);
					// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					if ( @file_exists( $filename ) ) {
						$template_abspath = $filename;
					}
				}
			}
		} else {
			// An absolute path was given. Normalize before security checks to prevent bypass via mixed separators.
			$normalized_template = str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $custom_template );

			// Check for directory traversal patterns AFTER normalization to catch all bypass attempts.
			if ( preg_match( '#\.\.' . preg_quote( DIRECTORY_SEPARATOR, '#' ) . '#', $normalized_template ) ) {
				// Directory traversal attempt detected - do not load this template.
				return false;
			}

			// Check if it's within an allowed template directory.
			foreach ( $template_dirs as $dir ) {
				$normalized_dir = rtrim( str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $dir ), DIRECTORY_SEPARATOR );

				if ( strpos( $normalized_template, $normalized_dir ) === 0 ) {
					// This template is within an allowed directory.
					$template_abspath = $custom_template;
					break;
				}
			}

			if ( ! $template_abspath ) {
				/*
				 * Historically, NextGEN Gallery allowed absolute paths here so that templates could be loaded from
				 * arbitrary locations on disk. This created a local file inclusion vulnerability via the `template`
				 * parameter on shortcodes.
				 *
				 * For security reasons we no longer load templates using arbitrary absolute paths. Site owners should
				 * instead move custom templates into their theme or child theme `nggallery` directory and reference them
				 * by file name (without a full path).
				 */
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						// Translators: %s is the absolute path that was provided.
						'Using an absolute path for a NextGEN Gallery legacy template (%s) is deprecated and no longer supported for security reasons. Please move this template file into your active theme or child theme "nggallery" directory and reference it by file name instead.',
						esc_html( $custom_template )
					),
					'3.59.13'
				);
				// Intentionally do not set $template_abspath for absolute paths outside allowed directories.
			}
		}

		return $template_abspath;
	}
}
