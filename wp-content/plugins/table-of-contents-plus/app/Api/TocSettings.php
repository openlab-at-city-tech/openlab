<?php
namespace AIOSEO\TableOfContents\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads and writes the table of contents settings.
 *
 * The frontend engine still stores its settings in the legacy 'toc-options'
 * option, so this controller bridges the Vue settings UI to that store and
 * mirrors the sanitization the legacy admin page used.
 *
 * Follow-up (tech debt): migrate these user settings onto the modern Options
 * framework the other AIOSEO plugins use. That store persists on activation and
 * version-tracks reliably, which would remove the existing-install detection
 * gymnastics in {@see \AIOSEO\TableOfContents\Main\Updates::migrateAppearanceDefaults()}.
 *
 * @since 1.0.0
 */
class TocSettings {
	/**
	 * Returns the current settings merged with the defaults, plus the data the UI needs.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response The response.
	 */
	public static function getSettings() {
		$engine = aioseoTableOfContents()->standalone->frontend;

		return new \WP_REST_Response( [
			'success'   => true,
			'settings'  => $engine->get_options(),
			'postTypes' => self::getPostTypes()
		], 200 );
	}

	/**
	 * Saves the settings to the legacy 'toc-options' option.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST request.
	 * @return \WP_REST_Response          The response.
	 */
	public static function saveSettings( $request ) {
		$engine   = aioseoTableOfContents()->standalone->frontend;
		$defaults = $engine->get_defaults();
		$body     = $request->get_json_params();

		if ( empty( $body ) || ! is_array( $body ) ) {
			return new \WP_REST_Response( [ 'success' => false ], 400 );
		}

		$string = function ( $key ) use ( $body, $defaults ) {
			return isset( $body[ $key ] ) ? trim( sanitize_text_field( wp_unslash( $body[ $key ] ) ) ) : $defaults[ $key ];
		};
		$bool = function ( $key ) use ( $body ) {
			return ! empty( $body[ $key ] );
		};
		$int = function ( $key ) use ( $body, $defaults ) {
			return isset( $body[ $key ] ) ? intval( $body[ $key ] ) : $defaults[ $key ];
		};
		$color = function ( $key, $default ) use ( $engine, $body ) {
			return ! empty( $body[ $key ] ) ? $engine->hex_value( trim( wp_unslash( $body[ $key ] ) ), $default ) : $default;
		};
		$enum = function ( $key, $allowed ) use ( $string, $defaults ) {
			$value = $string( $key );

			return in_array( $value, $allowed, true ) ? $value : $defaults[ $key ];
		};

		$restrictPath = $string( 'restrict_path' );
		if ( $restrictPath && 0 !== strpos( $restrictPath, '/' ) ) {
			// The restrict path must start with a forward slash, otherwise clear it.
			$restrictPath = '';
		}

		$sanitized = [
			'fragment_prefix'                    => $string( 'fragment_prefix' ),
			'position'                           => $int( 'position' ),
			'start'                              => $int( 'start' ),
			'show_heading_text'                  => $bool( 'show_heading_text' ),
			'heading_text'                       => $string( 'heading_text' ),
			'heading_alignment'                  => $enum( 'heading_alignment', [ 'left', 'center', 'right' ] ),
			'auto_insert_post_types'             => self::sanitizeStringArray( $body, 'auto_insert_post_types' ),
			'show_heirarchy'                     => $bool( 'show_heirarchy' ),
			'ordered_list'                       => $bool( 'ordered_list' ),
			'numbering_style'                    => $enum( 'numbering_style', [ 'hierarchical', 'decimal', 'upper-roman', 'lower-roman', 'upper-alpha', 'lower-alpha' ] ),
			'smooth_scroll'                      => $bool( 'smooth_scroll' ),
			'smooth_scroll_offset'               => $int( 'smooth_scroll_offset' ),
			'visibility'                         => $bool( 'visibility' ),
			'visibility_show'                    => $string( 'visibility_show' ),
			'visibility_hide'                    => $string( 'visibility_hide' ),
			'toggle_style'                       => $enum( 'toggle_style', [ 'plus_minus', 'caret', 'brackets' ] ),
			'visibility_hide_by_default'         => $bool( 'visibility_hide_by_default' ),
			'width'                              => $string( 'width' ),
			'width_custom'                       => isset( $body['width_custom'] ) ? floatval( $body['width_custom'] ) : $defaults['width_custom'],
			'width_custom_units'                 => $string( 'width_custom_units' ),
			'wrapping'                           => $int( 'wrapping' ),
			'font_size'                          => isset( $body['font_size'] ) ? floatval( $body['font_size'] ) : $defaults['font_size'],
			'font_size_units'                    => $string( 'font_size_units' ),
			'theme'                              => $int( 'theme' ),
			'custom_background_colour'           => $color( 'custom_background_colour', TOC_DEFAULT_BACKGROUND_COLOUR ),
			'custom_border_colour'               => $color( 'custom_border_colour', TOC_DEFAULT_BORDER_COLOUR ),
			'custom_title_colour'                => $color( 'custom_title_colour', TOC_DEFAULT_TITLE_COLOUR ),
			'custom_links_colour'                => $color( 'custom_links_colour', TOC_DEFAULT_LINKS_COLOUR ),
			'custom_links_hover_colour'          => $color( 'custom_links_hover_colour', TOC_DEFAULT_LINKS_HOVER_COLOUR ),
			'custom_links_visited_colour'        => $color( 'custom_links_visited_colour', TOC_DEFAULT_LINKS_VISITED_COLOUR ),
			'lowercase'                          => $bool( 'lowercase' ),
			'hyphenate'                          => $bool( 'hyphenate' ),
			'bullet_spacing'                     => $bool( 'bullet_spacing' ),
			'include_homepage'                   => $bool( 'include_homepage' ),
			'exclude_css'                        => $bool( 'exclude_css' ),
			'heading_levels'                     => self::sanitizeIntArray( $body, 'heading_levels' ),
			'exclude'                            => $string( 'exclude' ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			'restrict_path'                      => $restrictPath,
			'css_container_class'                => $string( 'css_container_class' ),
			'sitemap_show_page_listing'          => $bool( 'sitemap_show_page_listing' ),
			'sitemap_show_category_listing'      => $bool( 'sitemap_show_category_listing' ),
			'sitemap_heading_type'               => $int( 'sitemap_heading_type' ),
			'sitemap_pages'                      => $string( 'sitemap_pages' ),
			'sitemap_categories'                 => $string( 'sitemap_categories' ),
			'show_toc_in_widget_only'            => $bool( 'show_toc_in_widget_only' ),
			'show_toc_in_widget_only_post_types' => self::sanitizeStringArray( $body, 'show_toc_in_widget_only_post_types' ),
			'rest_toc_output'                    => $bool( 'rest_toc_output' )
		];

		$options = array_merge( $engine->get_options(), $sanitized );
		update_option( 'toc-options', $options );
		$engine->set_option( $options );

		return new \WP_REST_Response( [
			'success'  => true,
			'settings' => $options
		], 200 );
	}

	/**
	 * Sanitizes an array of strings from the request body.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $body The request body.
	 * @param  string $key  The key to sanitize.
	 * @return array        The sanitized array.
	 */
	private static function sanitizeStringArray( $body, $key ) {
		if ( empty( $body[ $key ] ) || ! is_array( $body[ $key ] ) ) {
			return [];
		}

		return array_values( array_map( 'sanitize_text_field', array_map( 'wp_unslash', $body[ $key ] ) ) );
	}

	/**
	 * Sanitizes an array of integers from the request body.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $body The request body.
	 * @param  string $key  The key to sanitize.
	 * @return array        The sanitized array.
	 */
	private static function sanitizeIntArray( $body, $key ) {
		if ( empty( $body[ $key ] ) || ! is_array( $body[ $key ] ) ) {
			return [];
		}

		return array_values( array_map( 'intval', $body[ $key ] ) );
	}

	/**
	 * Returns the public post types the table of contents can be enabled for.
	 *
	 * @since 1.0.0
	 *
	 * @return array The post types as { name, label, icon } objects.
	 */
	public static function getPostTypes() {
		$exclude      = [ 'attachment', 'revision', 'nav_menu_item', 'safecss' ];
		$builtinIcons = [
			'post' => 'dashicons-admin-post',
			'page' => 'dashicons-admin-page'
		];

		$postTypes = [];
		foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $postType ) {
			if ( in_array( $postType->name, $exclude, true ) ) {
				continue;
			}

			if ( isset( $builtinIcons[ $postType->name ] ) ) {
				$icon = $builtinIcons[ $postType->name ];
			} elseif ( is_string( $postType->menu_icon ) && 0 === strpos( $postType->menu_icon, 'dashicons-' ) ) {
				$icon = $postType->menu_icon;
			} else {
				$icon = 'dashicons-admin-post';
			}

			$postTypes[] = [
				'name'  => $postType->name,
				'label' => $postType->labels->name,
				'icon'  => $icon
			];
		}

		return $postTypes;
	}
}