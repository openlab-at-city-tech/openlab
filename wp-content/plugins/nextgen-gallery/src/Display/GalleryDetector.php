<?php

namespace Imagely\NGG\Display;

/**
 * Detects whether the current frontend page contains any NextGEN Gallery content.
 *
 * Used to gate asset loading — zero assets should load on pages with no gallery.
 * Detection runs once per request and caches the result.
 *
 * Handles:
 * - Shortcodes ([ngg], [ngg_images], [nggallery] and any registered aliases)
 * - Gutenberg blocks (imagely/main-block, imagely/nextgen-gallery)
 * - Widget areas (Gallery and Slideshow widgets)
 * - Block-based widget areas (WP 5.8+) and FSE template parts (wp_block, wp_template_part)
 * - Page builder postmeta (Elementor, Beaver Builder, Divi)
 * - Legacy NGG 1.x "Attach to Post" placeholder (nextgen-attach_to_post / ngg_displayed_gallery)
 *
 * False positives are acceptable. False negatives (missing a real gallery) are not.
 *
 * Apply the `nextgen_load_assets_on_page` filter to override detection for edge cases
 * (e.g., galleries inserted via JavaScript after page load, or custom template tags).
 *
 * Third-party integrations (page builders, custom renderers) can hook `ngg_page_has_gallery`
 * to return true for a given post when they know a gallery is present.
 */
class GalleryDetector {

	/**
	 * Cached detection result for the current request.
	 *
	 * @var bool|null
	 */
	private static $result = null;

	/**
	 * Returns the legacy NGG 1.x "Attach to Post" placeholder needles — the slug
	 * (as an `<img>` src fragment) and the marker class NGG 1.x wrote onto the
	 * placeholder. Shared by check_content(), check_block_templates(), and
	 * check_term_description() so the list only needs updating in one place.
	 *
	 * @return string[]
	 */
	private static function attach_to_post_needles(): array {
		return [
			defined( 'NGG_ATTACH_TO_POST_SLUG' ) ? NGG_ATTACH_TO_POST_SLUG : 'nextgen-attach_to_post',
			'ngg_displayed_gallery',
		];
	}

	/**
	 * Returns true if the current page contains any NextGEN Gallery content.
	 * Result is cached for the lifetime of the request.
	 */
	public static function has_gallery(): bool {
		if ( null !== self::$result ) {
			return self::$result;
		}
		self::$result = (bool) apply_filters( 'nextgen_load_assets_on_page', self::detect() );
		return self::$result;
	}

	/**
	 * Resets the cached result. Used in tests or when content changes after detection ran.
	 */
	public static function reset() {
		self::$result = null;
	}

	// ── Detection methods ─────────────────────────────────────────────────────

	private static function detect(): bool {
		// Skip REST requests — no front-end rendering.
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if ( isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( $_SERVER['REQUEST_URI'], 'wp-json' ) ) {
			return false;
		}

		// Skip admin pages.
		if ( is_admin() ) {
			return false;
		}

		return self::check_content() || self::check_widgets() || self::check_block_templates() || self::check_term_description();
	}

	/**
	 * Checks if any NGG shortcode or Gutenberg block appears in the current query's posts.
	 * Single pass over posts for shortcode regex, block detection, page builder postmeta,
	 * and the `ngg_page_has_gallery` filter for third-party integrations.
	 */
	private static function check_content(): bool {
		global $wp_query;

		if ( empty( $wp_query->posts ) || ! is_array( $wp_query->posts ) ) {
			return false;
		}

		$shortcode_manager = Shortcodes::get_instance();
		$shortcodes        = array_keys( $shortcode_manager->get_shortcodes() );

		$pattern = null;
		if ( ! empty( $shortcodes ) ) {
			$tags    = array_map(
				function ( $tag ) {
					return preg_quote( $tag, '/' );
				},
				$shortcodes
			);
			$pattern = '/\[(' . implode( '|', $tags ) . ')[\s\/\]]/';
		}

		$ngg_blocks = [
			'imagely/main-block',
			'imagely/nextgen-gallery',
		];

		// Page builder postmeta keys that may contain serialized shortcodes or layout data.
		$builder_meta_keys = [ '_elementor_data', 'fl_builder_data', '_et_pb_use_builder' ];

		foreach ( $wp_query->posts as $post ) {
			// Allow page builder integrations and custom renderers to signal a gallery is present.
			if ( apply_filters( 'ngg_page_has_gallery', false, $post ) ) {
				return true;
			}

			if ( empty( $post->post_content ) ) {
				continue;
			}

			if ( $pattern && preg_match( $pattern, $post->post_content ) ) {
				return true;
			}

			// Legacy NGG 1.x "Attach to Post" placeholder — no bracket shortcode present.
			foreach ( self::attach_to_post_needles() as $needle ) {
				if ( false !== strpos( $post->post_content, $needle ) ) {
					return true;
				}
			}

			foreach ( $ngg_blocks as $block_name ) {
				if ( has_block( $block_name, $post ) ) {
					return true;
				}
			}

			// Check page builder postmeta for embedded NGG shortcodes (Elementor, Beaver Builder, Divi).
			if ( $pattern ) {
				foreach ( $builder_meta_keys as $meta_key ) {
					$meta_value = get_post_meta( $post->ID, $meta_key, true );
					if ( $meta_value && is_string( $meta_value ) && preg_match( $pattern, $meta_value ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Checks if any NextGEN widget is active in any classic sidebar.
	 * We check for registered NGG widget ID bases in the active widgets option.
	 * This is a conservative check — if a widget area exists with an NGG widget,
	 * we load assets regardless of whether that sidebar renders on this page.
	 */
	private static function check_widgets(): bool {
		$sidebars = get_option( 'sidebars_widgets', [] );

		if ( empty( $sidebars ) || ! is_array( $sidebars ) ) {
			return false;
		}

		// NGG widget ID bases (as registered in Widget/Gallery.php and Widget/Slideshow.php).
		$ngg_widget_bases = [
			'ngg-images', // Gallery widget
			'slideshow',  // Slideshow widget
		];

		foreach ( $sidebars as $sidebar => $widgets ) {
			if ( 'wp_inactive_widgets' === $sidebar ) {
				continue;
			}
			if ( ! is_array( $widgets ) ) {
				continue;
			}
			foreach ( $widgets as $widget_id ) {
				foreach ( $ngg_widget_bases as $base ) {
					if ( 0 === strpos( (string) $widget_id, $base ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Checks block-based widget areas (WP 5.8+) and FSE template parts for NGG content.
	 * wp_block posts back block widget areas; wp_template_part posts back FSE headers/footers.
	 * An NGG block or shortcode in a global template part would render on every page.
	 *
	 * Result is stored in the WP object cache so Redis/Memcached provides cross-request
	 * relief — the queries only hit the DB once until the cache is cleared.
	 */
	private static function check_block_templates(): bool {
		// '[ngg' covers [ngg], [ngg_images], [nggallery] and any registered alias starting with [ngg.
		$ngg_patterns = array_merge(
			[
				'imagely/main-block',
				'imagely/nextgen-gallery',
				'[ngg',
			],
			self::attach_to_post_needles()
		);

		// Cache key includes a hash of the pattern list so a stale cached result from
		// before a needle-list change (like this PR's) can't survive on Redis/Memcached
		// sites, where there is no per-request cache reset to fall back on.
		$cache_key   = 'ngg_gallery_in_block_templates_' . md5( implode( '|', $ngg_patterns ) );
		$cache_group = 'ngg_gallery_detector';
		$cached      = wp_cache_get( $cache_key, $cache_group );
		if ( false !== $cached ) {
			return (bool) $cached;
		}

		$found = false;
		foreach ( [ 'wp_block', 'wp_template_part' ] as $post_type ) {
			if ( $found ) {
				break;
			}
			$posts = get_posts(
				[
					'post_type'              => $post_type,
					'posts_per_page'         => -1,
					'post_status'            => 'publish',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				]
			);

			foreach ( $posts as $post ) {
				if ( empty( $post->post_content ) ) {
					continue;
				}
				foreach ( $ngg_patterns as $needle ) {
					if ( false !== strpos( $post->post_content, $needle ) ) {
						$found = true;
						break 2;
					}
				}
			}
		}

		wp_cache_set( $cache_key, $found ? 1 : 0, $cache_group );
		return $found;
	}

	/**
	 * Checks if the current taxonomy/category/tag archive has an NGG shortcode or block
	 * in the term description. Covers the WooCommerce product-category pattern (issue #74)
	 * where galleries are placed in term descriptions rather than post content.
	 */
	private static function check_term_description(): bool {
		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return false;
		}

		$queried_object = get_queried_object();
		if ( ! $queried_object instanceof \WP_Term || empty( $queried_object->description ) ) {
			return false;
		}

		$description = $queried_object->description;

		if ( false !== strpos( $description, '[ngg' ) ) {
			return true;
		}

		$needles = array_merge(
			[ 'imagely/main-block', 'imagely/nextgen-gallery' ],
			self::attach_to_post_needles()
		);

		foreach ( $needles as $needle ) {
			if ( false !== strpos( $description, $needle ) ) {
				return true;
			}
		}

		return false;
	}
}
