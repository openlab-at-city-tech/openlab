<?php
/**
 * Kadence\FSE\Block_Theme_Cache class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Keeps core's cached block theme flag in step with the mode.
 *
 * @since 1.6.0
 */
final class Block_Theme_Cache {

	/**
	 * Full Site Editing mode.
	 *
	 * @since 1.6.0
	 *
	 * @var Mode
	 */
	private Mode $mode;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Mode $mode Full Site Editing mode.
	 */
	public function __construct( Mode $mode ) {
		$this->mode = $mode;
	}

	/**
	 * Drops a cached block theme flag that does not match the mode.
	 *
	 * Core builds the active theme object before functions.php loads, so with the
	 * mode on the flag is cached as false until it is recomputed. Turning the mode
	 * off recomputes it while the filter is still registered, so the next request
	 * can find a stale true when theme data is cached persistently.
	 *
	 * @since 1.6.0
	 */
	public function refresh() {
		if ( wp_get_theme()->is_block_theme() !== $this->mode->is_enabled() ) {
			$this->clear();
		}
	}

	/**
	 * Deletes the cached theme data that holds the block theme flag.
	 *
	 * Only the theme entry is deleted, so the next wp_get_theme() recomputes the
	 * flag without clearing the pattern cache that WP_Theme::cache_delete() also clears.
	 *
	 * @since 1.6.0
	 */
	public function clear() {
		$theme  = wp_get_theme();
		$themes = [ $theme ];

		if ( $theme->parent() ) {
			$themes[] = $theme->parent();
		}

		foreach ( $themes as $item ) {
			wp_cache_delete( 'theme-' . md5( $item->get_theme_root() . '/' . $item->get_stylesheet() ), 'themes' );
		}
	}
}
