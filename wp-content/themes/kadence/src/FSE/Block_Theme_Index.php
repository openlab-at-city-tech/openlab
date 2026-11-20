<?php
/**
 * Kadence\FSE\Block_Theme_Index class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Points core's block theme check at fse/site-templates/index.html.
 *
 * Core treats a theme as a block theme when templates/index.html exists. Only
 * this theme's path is redirected, which also covers child themes.
 *
 * @since 1.6.0
 */
final class Block_Theme_Index {

	/**
	 * Redirects the templates/index.html lookup for this theme.
	 *
	 * @since 1.6.0
	 *
	 * @param string $path The resolved file path.
	 * @param string $file The requested file, relative to the theme root.
	 * @return string
	 */
	public function filter_path( $path, $file ): string {
		if ( 'templates/index.html' !== $file || ! str_starts_with( $path, get_template_directory() . '/' ) ) {
			return $path;
		}

		return get_template_directory() . '/fse/site-templates/index.html';
	}
}
