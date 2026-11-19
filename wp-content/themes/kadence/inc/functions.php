<?php
/**
 * The `kadence()` function.
 *
 * @package kadence
 */

namespace Kadence;

use Kadence\Template_Tags;
use Kadence\Theme;
use KadenceWP\KadenceShopKit\App;
use KadenceWP\KadenceShopKit\Features\Feature_Repository;

use function get_template_directory;

/**
 * Provides access to all available template tags of the theme.
 *
 * When called for the first time, the function will initialize the theme.
 *
 * @return Template_Tags Template tags instance exposing template tag methods.
 */
function kadence(): Template_Tags {
	static $theme = null;

	if ( null === $theme ) {
		$theme = Theme::instance();
	}

	return $theme->template_tags();
}
/**
 * Whether Full Site Editing mode is on for this site.
 *
 * True when the site turned the mode on and runs WordPress 7.1 or later. Each call reads the
 * setting, so the result changes in the request that saves it, while wp_is_block_theme() and the
 * hooks the theme adds for the mode switch on the next request. Check this rather than
 * wp_is_block_theme(), which is also true for a child theme with its own templates/index.html,
 * and call it each time rather than keeping the result.
 *
 * The kadence_fse_mode option behind it is internal, so don't read or write it directly. The mode
 * changes from Appearance > Kadence or the kadence/v1/fse-mode REST route, which fire
 * `kadence_fse_mode_enabled` or `kadence_fse_mode_disabled`. Hook those to react to a switch.
 *
 * The theme defines this when its functions.php loads, after plugins, so call it from
 * `after_setup_theme` or later, guarded with `function_exists( 'Kadence\is_fse_mode' )`.
 *
 * @since 1.6.0
 *
 * @return bool True when the mode is on.
 */
function is_fse_mode(): bool {
	return Theme::instance()->container()->get( FSE\Mode::class )->is_enabled();
}
/**
 * Checks if a Kadence Shop Kit feature is enabled.
 *
 * @param string $feature_id The feature ID to check.
 * @return bool True if the feature is enabled, false otherwise.
 */
function is_shopkit_feature_enabled( string $feature_id ): bool {
	if ( ! class_exists( App::class ) ) {
		return false;
	}

	/** @var Feature_Repository $repository */
	$repository = App::container()->get( Feature_Repository::class );

	return $repository->has( $feature_id ) && $repository->is_enabled( $feature_id );
}

// Load the CSS class.
require get_template_directory() . '/inc/class-kadence-css.php';
// Load the Local Font class.
require get_template_directory() . '/inc/class-local-gfonts.php';

// Load the Customizer class.
require get_template_directory() . '/inc/customizer/class-theme-customizer.php';

// Load Settings Page Class.
require get_template_directory() . '/inc/dashboard/class-theme-dashboard.php';

// Load the Meta class.
require get_template_directory() . '/inc/meta/class-theme-meta.php';

// Load the template functions.
require get_template_directory() . '/inc/template-functions/header-functions.php';
require get_template_directory() . '/inc/template-functions/title-functions.php';
require get_template_directory() . '/inc/template-functions/single-functions.php';
require get_template_directory() . '/inc/template-functions/footer-functions.php';
require get_template_directory() . '/inc/template-functions/archive-functions.php';

// Load the template hooks.
require get_template_directory() . '/inc/template-hooks.php';
