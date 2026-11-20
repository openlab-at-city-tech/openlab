<?php
/**
 * Kadence\FSE\Global_Stylesheet class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Loads the FSE build of the global stylesheet.
 *
 * In it the base button rule gives Kadence Blocks' Fill buttons no colors. Kadence Blocks
 * draws them at the specificity of core's Global Styles rule for the block and before it, so
 * the colors set for the block in the Site Editor reach the button, Tablet and Mobile included.
 * Classic mode keeps the original build.
 *
 * @since 1.6.0
 */
final class Global_Stylesheet {

	/**
	 * Points the global stylesheet to its FSE build.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, array<string, mixed>|string> $css_files The theme's CSS files, as handle => data or file.
	 *
	 * @return array<string, array<string, mixed>|string> The CSS files.
	 */
	public function filter_css_files( array $css_files ): array {
		if ( isset( $css_files['kadence-global'] ) && is_array( $css_files['kadence-global'] ) ) {
			$css_files['kadence-global']['file'] = 'global-fse.min.css';
		}

		return $css_files;
	}
}
