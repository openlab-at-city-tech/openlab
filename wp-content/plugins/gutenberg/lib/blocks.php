<?php
/**
 * Block and style registration functions.
 *
 * @package gutenberg
 */

/**
 * Substitutes the implementation of a core-registered block type, if exists,
 * with the built result from the plugin.
 */
function gutenberg_reregister_core_block_types() {
	// Blocks directory may not exist if working from a fresh clone.
	$blocks_dirs = array(
		__DIR__ . '/../build/block-library/blocks/' => array(
			'block_folders' => array(
				'audio',
				'button',
				'buttons',
				'freeform',
				'code',
				'column',
				'columns',
				'comments-query-loop',
				'cover',
				'gallery',
				'group',
				'heading',
				'html',
				'home-link',
				'image',
				'list',
				'media-text',
				'missing',
				'more',
				'navigation-area',
				'navigation-link',
				'navigation-submenu',
				'nextpage',
				'pattern',
				'paragraph',
				'preformatted',
				'pullquote',
				'quote',
				'separator',
				'social-links',
				'spacer',
				'table',
				// 'table-of-contents',
				'text-columns',
				'verse',
				'video',
				'embed',
			),
			'block_names'   => array(
				'archives.php'                  => 'core/archives',
				'block.php'                     => 'core/block',
				'calendar.php'                  => 'core/calendar',
				'categories.php'                => 'core/categories',
				'comment-author-avatar.php'     => 'core/comment-author-avatar',
				'comment-author-name.php'       => 'core/comment-author-name',
				'comment-content.php'           => 'core/comment-content',
				'comment-date.php'              => 'core/comment-date',
				'comment-edit-link.php'         => 'core/comment-edit-link',
				'comment-reply-link.php'        => 'core/comment-reply-link',
				'comment-template.php'          => 'core/comment-template',
				'file.php'                      => 'core/file',
				'home-link.php'                 => 'core/home-link',
				'image.php'                     => 'core/image',
				'gallery.php'                   => 'core/gallery',
				'latest-comments.php'           => 'core/latest-comments',
				'latest-posts.php'              => 'core/latest-posts',
				'loginout.php'                  => 'core/loginout',
				'navigation.php'                => 'core/navigation',
				'navigation-area.php'           => 'core/navigation-area',
				'navigation-link.php'           => 'core/navigation-link',
				'navigation-submenu.php'        => 'core/navigation-submenu',
				'page-list.php'                 => 'core/page-list',
				'pattern.php'                   => 'core/pattern',
				'post-author.php'               => 'core/post-author',
				'post-comment.php'              => 'core/post-comment',
				'post-comments.php'             => 'core/post-comments',
				'post-comments-count.php'       => 'core/post-comments-count',
				'post-comments-form.php'        => 'core/post-comments-form',
				'post-comments-link.php'        => 'core/post-comments-link',
				'post-content.php'              => 'core/post-content',
				'post-date.php'                 => 'core/post-date',
				'post-excerpt.php'              => 'core/post-excerpt',
				'post-featured-image.php'       => 'core/post-featured-image',
				'post-navigation-link.php'      => 'core/post-navigation-link',
				'post-terms.php'                => 'core/post-terms',
				'post-title.php'                => 'core/post-title',
				'query.php'                     => 'core/query',
				'post-template.php'             => 'core/post-template',
				'query-pagination.php'          => 'core/query-pagination',
				'query-pagination-next.php'     => 'core/query-pagination-next',
				'query-pagination-numbers.php'  => 'core/query-pagination-numbers',
				'query-pagination-previous.php' => 'core/query-pagination-previous',
				'query-title.php'               => 'core/query-title',
				'rss.php'                       => 'core/rss',
				'search.php'                    => 'core/search',
				'shortcode.php'                 => 'core/shortcode',
				'social-link.php'               => 'core/social-link',
				'site-logo.php'                 => 'core/site-logo',
				'site-tagline.php'              => 'core/site-tagline',
				'site-title.php'                => 'core/site-title',
				// 'table-of-contents.php'        => 'core/table-of-contents',
				'tag-cloud.php'                 => 'core/tag-cloud',
				'template-part.php'             => 'core/template-part',
				'term-description.php'          => 'core/term-description',
			),
		),
		__DIR__ . '/../build/edit-widgets/blocks/'  => array(
			'block_folders' => array(
				'widget-area',
			),
			'block_names'   => array(),
		),
		__DIR__ . '/../build/widgets/blocks/'       => array(
			'block_folders' => array(
				'legacy-widget',
				'widget-group',
			),
			'block_names'   => array(
				'legacy-widget.php' => 'core/legacy-widget',
				'widget-group.php'  => 'core/widget-group',
			),
		),
	);
	foreach ( $blocks_dirs as $blocks_dir => $details ) {
		$block_folders = $details['block_folders'];
		$block_names   = $details['block_names'];

		$registry = WP_Block_Type_Registry::get_instance();

		foreach ( $block_folders as $folder_name ) {
			$block_json_file = $blocks_dir . $folder_name . '/block.json';

			// Ideally, all paths to block metadata files should be listed in
			// WordPress core. In this place we should rather use filter
			// to replace paths with overrides defined by the plugin.
			$metadata = json_decode( file_get_contents( $block_json_file ), true );
			if ( ! is_array( $metadata ) || ! $metadata['name'] ) {
				return false;
			}

			if ( $registry->is_registered( $metadata['name'] ) ) {
				$registry->unregister( $metadata['name'] );
			}

			gutenberg_register_core_block_assets( $folder_name );
			register_block_type_from_metadata( $block_json_file );
		}

		foreach ( $block_names as $file => $sub_block_names ) {
			if ( ! file_exists( $blocks_dir . $file ) ) {
				return;
			}

			$sub_block_names_normalized = is_string( $sub_block_names ) ? array( $sub_block_names ) : $sub_block_names;
			foreach ( $sub_block_names_normalized as $block_name ) {
				if ( $registry->is_registered( $block_name ) ) {
					$registry->unregister( $block_name );
				}
				gutenberg_register_core_block_assets( $block_name );
			}

			require_once $blocks_dir . $file;
		}
	}
}

add_action( 'init', 'gutenberg_reregister_core_block_types' );

/**
 * Registers block styles for a core block.
 *
 * @param string $block_name The block-name.
 *
 * @return void
 */
function gutenberg_register_core_block_assets( $block_name ) {
	if ( ! wp_should_load_separate_core_block_assets() ) {
		return;
	}

	$block_name = str_replace( 'core/', '', $block_name );

	// When in production, use the plugin's version as the default asset version;
	// else (for development or test) default to use the current time.
	$default_version = defined( 'GUTENBERG_VERSION' ) && ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? GUTENBERG_VERSION : time();

	$style_path        = "build/block-library/blocks/$block_name/style.css";
	$editor_style_path = "build/block-library/blocks/$block_name/style-editor.css";

	if ( file_exists( gutenberg_dir_path() . $style_path ) ) {
		wp_deregister_style( "wp-block-{$block_name}" );
		wp_register_style(
			"wp-block-{$block_name}",
			gutenberg_url( $style_path ),
			array(),
			$default_version
		);
		wp_style_add_data( "wp-block-{$block_name}", 'rtl', 'replace' );

		// Add a reference to the stylesheet's path to allow calculations for inlining styles in `wp_head`.
		wp_style_add_data( "wp-block-{$block_name}", 'path', gutenberg_dir_path() . $style_path );
	} else {
		wp_register_style( "wp-block-{$block_name}", false );
	}

	// If the current theme supports wp-block-styles, dequeue the full stylesheet
	// and instead attach each block's theme-styles to their block styles stylesheet.
	if ( current_theme_supports( 'wp-block-styles' ) ) {

		// Dequeue the full stylesheet.
		// Make sure this only runs once, it doesn't need to run for every block.
		static $stylesheet_removed;
		if ( ! $stylesheet_removed ) {
			add_action(
				'wp_enqueue_scripts',
				function() {
					wp_dequeue_style( 'wp-block-library-theme' );
				}
			);
			$stylesheet_removed = true;
		}

		// Get the path to the block's stylesheet.
		$theme_style_path = is_rtl()
			? "build/block-library/blocks/$block_name/theme-rtl.css"
			: "build/block-library/blocks/$block_name/theme.css";

		// If the file exists, enqueue it.
		if ( file_exists( gutenberg_dir_path() . $theme_style_path ) ) {

			if ( file_exists( gutenberg_dir_path() . $style_path ) ) {
				// If there is a main stylesheet for this block, append the theme styles to main styles.
				wp_add_inline_style(
					"wp-block-{$block_name}",
					file_get_contents( gutenberg_dir_path() . $theme_style_path )
				);
			} else {
				// If there is no main stylesheet for this block, register theme style.
				wp_register_style(
					"wp-block-{$block_name}",
					gutenberg_url( $theme_style_path ),
					array(),
					$default_version
				);
				wp_style_add_data( "wp-block-{$block_name}", 'path', gutenberg_dir_path() . $theme_style_path );
			}
		}
	}

	if ( file_exists( gutenberg_dir_path() . $editor_style_path ) ) {
		wp_deregister_style( "wp-block-{$block_name}-editor" );
		wp_register_style(
			"wp-block-{$block_name}-editor",
			gutenberg_url( $editor_style_path ),
			array(),
			$default_version
		);
		wp_style_add_data( "wp-block-{$block_name}-editor", 'rtl', 'replace' );
	} else {
		wp_register_style( "wp-block-{$block_name}-editor", false );
	}
}

/**
 * Change the way styles get loaded depending on their size.
 *
 * Optimizes performance and sustainability of styles by inlining smaller stylesheets.
 *
 * @todo Remove this function when the minimum supported version is WordPress 5.8.
 *
 * @return void
 */
function gutenberg_maybe_inline_styles() {

	// Early exit if the "wp_maybe_inline_styles" function exists.
	if ( function_exists( 'wp_maybe_inline_styles' ) ) {
		return;
	}

	$total_inline_limit = 20000;
	/**
	 * The maximum size of inlined styles in bytes.
	 *
	 * @param int $total_inline_limit The file-size threshold, in bytes. Defaults to 20000.
	 * @return int                    The file-size threshold, in bytes.
	 */
	$total_inline_limit = apply_filters( 'styles_inline_size_limit', $total_inline_limit );

	global $wp_styles;
	$styles = array();

	// Build an array of styles that have a path defined.
	foreach ( $wp_styles->queue as $handle ) {
		if ( wp_styles()->get_data( $handle, 'path' ) && file_exists( $wp_styles->registered[ $handle ]->extra['path'] ) ) {
			$styles[] = array(
				'handle' => $handle,
				'src'    => $wp_styles->registered[ $handle ]->src,
				'path'   => $wp_styles->registered[ $handle ]->extra['path'],
				'size'   => filesize( $wp_styles->registered[ $handle ]->extra['path'] ),
			);
		}
	}

	if ( ! empty( $styles ) ) {
		// Reorder styles array based on size.
		usort(
			$styles,
			function( $a, $b ) {
				return ( $a['size'] <= $b['size'] ) ? -1 : 1;
			}
		);

		/**
		 * The total inlined size.
		 *
		 * On each iteration of the loop, if a style gets added inline the value of this var increases
		 * to reflect the total size of inlined styles.
		 */
		$total_inline_size = 0;

		// Loop styles.
		foreach ( $styles as $style ) {

			// Size check. Since styles are ordered by size, we can break the loop.
			if ( $total_inline_size + $style['size'] > $total_inline_limit ) {
				break;
			}

			// Get the styles if we don't already have them.
			$style['css'] = file_get_contents( $style['path'] );

			// Check if the style contains relative URLs that need to be modified.
			// URLs relative to the stylesheet's path should be converted to relative to the site's root.
			$style['css'] = _wp_normalize_relative_css_links( $style['css'], $style['src'] );

			// Set `src` to `false` and add styles inline.
			$wp_styles->registered[ $style['handle'] ]->src = false;
			if ( empty( $wp_styles->registered[ $style['handle'] ]->extra['after'] ) ) {
				$wp_styles->registered[ $style['handle'] ]->extra['after'] = array();
			}
			array_unshift( $wp_styles->registered[ $style['handle'] ]->extra['after'], $style['css'] );

			// Add the styles size to the $total_inline_size var.
			$total_inline_size += (int) $style['size'];
		}
	}
}
// Run for styles enqueued in <head>.
add_action( 'wp_head', 'gutenberg_maybe_inline_styles', 1 );
// Run for late-loaded styles in the footer.
add_action( 'wp_footer', 'gutenberg_maybe_inline_styles', 1 );

if ( ! function_exists( '_wp_normalize_relative_css_links' ) ) {
	/**
	 * Make URLs relative to the WordPress installation.
	 *
	 * @since 5.8.2
	 *
	 * @param string $css            The CSS to make URLs relative to the WordPress installation.
	 * @param string $stylesheet_url The URL to the stylesheet.
	 *
	 * @return string The CSS with URLs made relative to the WordPress installation.
	 */
	function _wp_normalize_relative_css_links( $css, $stylesheet_url ) {
		$has_src_results = preg_match_all( '#url\s*\(\s*[\'"]?\s*([^\'"\)]+)#', $css, $src_results );
		if ( $has_src_results ) {
			// Loop through the URLs to find relative ones.
			foreach ( $src_results[1] as $src_index => $src_result ) {
				// Skip if this is an absolute URL.
				if ( 0 === strpos( $src_result, 'http' ) || 0 === strpos( $src_result, '//' ) ) {
					continue;
				}

				// Build the absolute URL.
				$absolute_url = dirname( $stylesheet_url ) . '/' . $src_result;
				$absolute_url = str_replace( '/./', '/', $absolute_url );
				// Convert to URL related to the site root.
				$relative_url = wp_make_link_relative( $absolute_url );

				// Replace the URL in the CSS.
				$css = str_replace(
					$src_results[0][ $src_index ],
					str_replace( $src_result, $relative_url, $src_results[0][ $src_index ] ),
					$css
				);
			}
		}

		return $css;
	}
}

/**
 * Complements the implementation of block type `core/social-icon`, whether it
 * be provided by core or the plugin, with derived block types for each
 * "service" (WordPress, Twitter, etc.) supported by Social Links.
 *
 * This ensures backwards compatibility for any users running the Gutenberg
 * plugin who have used Social Links prior to their conversion to block
 * variations.
 *
 * This shim is INTENTIONALLY left out of core, as Social Links haven't yet
 * landed there.
 *
 * @see https://github.com/WordPress/gutenberg/pull/19887
 */
function gutenberg_register_legacy_social_link_blocks() {
	$services = array(
		'amazon',
		'bandcamp',
		'behance',
		'chain',
		'codepen',
		'deviantart',
		'dribbble',
		'dropbox',
		'etsy',
		'facebook',
		'feed',
		'fivehundredpx',
		'flickr',
		'foursquare',
		'goodreads',
		'google',
		'github',
		'instagram',
		'lastfm',
		'linkedin',
		'mail',
		'mastodon',
		'meetup',
		'medium',
		'pinterest',
		'pocket',
		'reddit',
		'skype',
		'snapchat',
		'soundcloud',
		'spotify',
		'tumblr',
		'twitch',
		'twitter',
		'vimeo',
		'vk',
		'wordpress',
		'yelp',
		'youtube',
	);

	foreach ( $services as $service ) {
		register_block_type(
			'core/social-link-' . $service,
			array(
				'category'        => 'widgets',
				'attributes'      => array(
					'url'     => array(
						'type' => 'string',
					),
					'service' => array(
						'type'    => 'string',
						'default' => $service,
					),
					'label'   => array(
						'type' => 'string',
					),
				),
				'render_callback' => 'gutenberg_render_block_core_social_link',
			)
		);
	}
}

add_action( 'init', 'gutenberg_register_legacy_social_link_blocks' );

/**
 * Filters the default block categories array to add a new one for themes.
 *
 * This can be removed when plugin support requires WordPress 5.8.0+.
 *
 * @see https://core.trac.wordpress.org/ticket/52883
 *
 * @param array[] $categories The list of default block categories.
 *
 * @return array[] Filtered block categories.
 */
function gutenberg_register_theme_block_category( $categories ) {
	foreach ( $categories as $category ) {
		// Skip when the category is already set in WordPress core.
		if (
			isset( $category['slug'] ) &&
			'theme' === $category['slug']
		) {
			return $categories;
		}
	}

	$categories[] = array(
		'slug'  => 'theme',
		'title' => _x( 'Theme', 'block category', 'gutenberg' ),
		'icon'  => null,
	);
	return $categories;
}
// This can be removed when plugin support requires WordPress 5.8.0+.
if ( ! function_exists( 'get_default_block_categories' ) ) {
	add_filter( 'block_categories', 'gutenberg_register_theme_block_category' );
}

/**
 * Checks whether the current block type supports the feature requested.
 *
 * @param WP_Block_Type $block_type Block type to check for support.
 * @param array         $feature    Path of the feature to check support for.
 * @param mixed         $default    Fallback value for feature support, defaults to false.
 *
 * @return boolean                  Whether or not the feature is supported.
 */
function gutenberg_block_has_support( $block_type, $feature, $default = false ) {
	$block_support = $default;
	if ( $block_type && property_exists( $block_type, 'supports' ) ) {
		$block_support = _wp_array_get( $block_type->supports, $feature, $default );
	}

	return true === $block_support || is_array( $block_support );
}

/**
 * Updates the shape of supports for declaring fontSize and lineHeight.
 *
 * @param array $metadata Metadata for registering a block type.
 * @return array          Metadata for registering a block type with the supports shape updated.
 */
function gutenberg_migrate_old_typography_shape( $metadata ) {
	// Temporarily disable migrations from core blocks to avoid warnings on versions older than 5.8.
	if ( isset( $metadata['supports'] ) && false === strpos( $metadata['file'], '/wp-includes/blocks/' ) ) {
		$typography_keys = array(
			'__experimentalFontFamily',
			'__experimentalFontStyle',
			'__experimentalFontWeight',
			'__experimentalLetterSpacing',
			'__experimentalTextDecoration',
			'__experimentalTextTransform',
			'fontSize',
			'lineHeight',
		);
		foreach ( $typography_keys as $typography_key ) {
			$support_for_key = _wp_array_get( $metadata['supports'], array( $typography_key ), null );
			if ( null !== $support_for_key ) {
				trigger_error(
					/* translators: %1$s: Block type, %2$s: typography supports key e.g: fontSize, lineHeight etc... */
					sprintf( __( 'Block %1$s is declaring %2$s support on block.json under supports.%2$s. %2$s support is now declared under supports.typography.%2$s.', 'gutenberg' ), $metadata['name'], $typography_key ),
					headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
				);
				_wp_array_set( $metadata['supports'], array( 'typography', $typography_key ), $support_for_key );
				unset( $metadata['supports'][ $typography_key ] );
			}
		}
	}
	return $metadata;
}

if ( ! function_exists( 'wp_migrate_old_typography_shape' ) ) {
	add_filter( 'block_type_metadata', 'gutenberg_migrate_old_typography_shape' );
}

if ( ! function_exists( 'wp_enqueue_block_style' ) ) {
	/**
	 * Enqueue a stylesheet for a specific block.
	 *
	 * If the theme has opted-in to separate-styles loading,
	 * then the stylesheet will be enqueued on-render,
	 * otherwise when the block inits.
	 *
	 * @param string $block_name The block-name, including namespace.
	 * @param array  $args       An array of arguments [handle,src,deps,ver,media].
	 *
	 * @return void
	 */
	function wp_enqueue_block_style( $block_name, $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'handle' => '',
				'src'    => '',
				'deps'   => array(),
				'ver'    => false,
				'media'  => 'all',
			)
		);

		/**
		 * Callback function to register and enqueue styles.
		 *
		 * @param string $content When the callback is used for the render_block filter,
		 *                        the content needs to be returned so the function parameter
		 *                        is to ensure the content exists.
		 *
		 * @return string
		 */
		$callback = function( $content ) use ( $args ) {
			// Register the stylesheet.
			if ( ! empty( $args['src'] ) ) {
				wp_register_style( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );
			}

			// Add `path` data if provided.
			if ( isset( $args['path'] ) ) {
				wp_style_add_data( $args['handle'], 'path', $args['path'] );

				// Get the RTL file path.
				$rtl_file_path = str_replace( '.css', '-rtl.css', $args['path'] );

				// Add RTL stylesheet.
				if ( file_exists( $rtl_file_path ) ) {
					wp_style_add_data( $args['hanle'], 'rtl', 'replace' );

					if ( is_rtl() ) {
						wp_style_add_data( $args['handle'], 'path', $rtl_file_path );
					}
				}
			}

			// Enqueue the stylesheet.
			wp_enqueue_style( $args['handle'] );

			return $content;
		};

		$hook = did_action( 'wp_enqueue_scripts' ) ? 'wp_footer' : 'wp_enqueue_scripts';
		if ( wp_should_load_separate_core_block_assets() ) {
			$hook = "render_block_$block_name";
		}

		// Enqueue assets in the frontend.
		add_filter( $hook, $callback );

		// Enqueue assets in the editor.
		add_action( 'enqueue_block_assets', $callback );
	}
}

/**
 * Allow multiple block styles.
 *
 * @param array $metadata Metadata for registering a block type.
 *
 * @return array
 */
function gutenberg_multiple_block_styles( $metadata ) {
	foreach ( array( 'style', 'editorStyle' ) as $key ) {
		if ( ! empty( $metadata[ $key ] ) && is_array( $metadata[ $key ] ) ) {
			$default_style = array_shift( $metadata[ $key ] );
			foreach ( $metadata[ $key ] as $handle ) {
				$args = array( 'handle' => $handle );
				if ( 0 === strpos( $handle, 'file:' ) && isset( $metadata['file'] ) ) {
					$style_path = remove_block_asset_path_prefix( $handle );
					$args       = array(
						'handle' => sanitize_key( "{$metadata['name']}-{$style_path}" ),
						'src'    => plugins_url( $style_path, $metadata['file'] ),
					);
				}

				wp_enqueue_block_style( $metadata['name'], $args );
			}

			// Only return the 1st item in the array.
			$metadata[ $key ] = $default_style;
		}
	}
	return $metadata;
}
add_filter( 'block_type_metadata', 'gutenberg_multiple_block_styles' );
