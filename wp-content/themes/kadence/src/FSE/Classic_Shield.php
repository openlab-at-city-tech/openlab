<?php
/**
 * Kadence\FSE\Classic_Shield class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_Block_Template;
use WP_Block_Templates_Registry;

/**
 * Keeps saved block templates from replacing the classic templates once the mode is off.
 *
 * Classic Kadence supports block templates, so core would still serve a single or
 * page template saved in the Site Editor in place of the theme's PHP files. Classic
 * sites use saved templates only as custom templates, so those keep rendering.
 *
 * @since 1.6.0
 */
final class Classic_Shield {

	/**
	 * Leaves saved templates that replace a classic template out of a template query.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Block_Template[] $query_result  Templates found so far.
	 * @param array               $query         The query arguments.
	 * @param string              $template_type wp_template or wp_template_part.
	 * @return WP_Block_Template[]
	 */
	public function hide_templates( $query_result, $query, $template_type ) {
		// Template parts are left alone, and a lookup by post ID is not picking a template to render.
		if ( 'wp_template' !== $template_type || isset( $query['wp_id'] ) ) {
			return $query_result;
		}

		// Reindexed, since some callers read the first result directly.
		return array_values( array_filter( $query_result, [ $this, 'renders_in_classic' ] ) );
	}

	/**
	 * Whether a template renders on a classic site.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $template A template from a query.
	 * @return bool
	 */
	private function renders_in_classic( $template ): bool {
		// Theme files and registered templates render on classic sites too.
		if ( ! $template instanceof WP_Block_Template || empty( $template->wp_id ) ) {
			return true;
		}

		// So do saved copies of them while the original still exists, since core skips the original once a copy does.
		if ( $template->has_theme_file || $this->copies_registered_template( $template ) ) {
			return true;
		}

		// A saved copy of one of the fse/ files, which only exist in the mode.
		if ( 'theme' === $template->origin ) {
			return false;
		}

		return $template->is_custom;
	}

	/**
	 * Whether a template is a saved copy of a plugin template that is still registered.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Block_Template $template A saved template.
	 * @return bool
	 */
	private function copies_registered_template( WP_Block_Template $template ): bool {
		// Plugins register templates with core from WordPress 6.7.
		if ( 'plugin' !== $template->origin || ! class_exists( 'WP_Block_Templates_Registry' ) ) {
			return false;
		}

		return null !== WP_Block_Templates_Registry::get_instance()->get_by_slug( $template->slug );
	}
}
