<?php

namespace Imagely\NGG\DisplayType;

class LegacyTemplateLocator {

	static $instance = null;

	/**
	 * @return LegacyTemplateLocator
	 */
	static function get_instance() {
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
	 * @param string $dir Directory
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
		if ( is_array( $prefix ) ) {
			$str            = implode( '|', $prefix );
			$regex_iterator = new \RegexIterator( $iterator, "/({$str})-.+\\.php$/i", \RecursiveRegexIterator::GET_MATCH );
		} elseif ( is_string( $prefix ) ) {
			$regex_iterator = new \RegexIterator( $iterator, "#(.*)[/\\\\]{$prefix}\\-?.*\\.php$#i", \RecursiveRegexIterator::GET_MATCH );
		} else {
			$regex_iterator = new \RegexIterator( $iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH );
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

		// Find the abspath of the template to render.
		if ( ! @file_exists( $custom_template ) ) {
			foreach ( $this->get_template_directories() as $dir ) {
				if ( $template_abspath ) {
					break;
				}
				$filename = implode( DIRECTORY_SEPARATOR, [ rtrim( $dir, '/\\' ), $custom_template ] );
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
					if ( @file_exists( $filename ) ) {
						$template_abspath = $filename;
					}
				}
			}
		} elseif ( ! preg_match( '#\.\.[/\\\]#', $custom_template ) ) {
			// An absolute path was already given.
			$template_abspath = $custom_template;
		}

		return $template_abspath;
	}
}
