<?php
/**
 * Block template loader functions.
 *
 * @package gutenberg
 */

/**
 * Adds necessary filters to use 'wp_template' posts instead of theme template files.
 */
function gutenberg_add_template_loader_filters() {
	if ( ! gutenberg_supports_block_templates() ) {
		return;
	}

	foreach ( gutenberg_get_template_type_slugs() as $template_type ) {
		if ( 'embed' === $template_type ) { // Skip 'embed' for now because it is not a regular template type.
			continue;
		}
		add_filter( str_replace( '-', '', $template_type ) . '_template', 'gutenberg_override_query_template', 20, 3 );
	}

	// Request to resolve a template.
	if ( isset( $_GET['_wp-find-template'] ) ) {
		add_filter( 'pre_get_posts', 'gutenberg_resolve_template_for_new_post' );
	}
}

add_action( 'wp_loaded', 'gutenberg_add_template_loader_filters' );

/**
 * Filters into the "{$type}_template" hooks to redirect them to the Full Site Editing template canvas.
 *
 * Internally, this communicates the block content that needs to be used by the template canvas through a global variable.
 *
 * @param string $template  Path to the template. See locate_template().
 * @param string $type      Sanitized filename without extension.
 * @param array  $templates A list of template candidates, in descending order of priority.
 * @return string The path to the Full Site Editing template canvas file.
 */
function gutenberg_override_query_template( $template, $type, array $templates ) {
	global $_wp_current_template_content;

	if ( $template ) {
		// locate_template() has found a PHP template at the path specified by $template.
		// That means that we have a fallback candidate if we cannot find a block template
		// with higher specificity.
		// Thus, before looking for matching block themes, we shorten our list of candidate
		// templates accordingly.

		// Locate the index of $template (without the theme directory path) in $templates.
		$relative_template_path = str_replace(
			array( get_stylesheet_directory() . '/', get_template_directory() . '/' ),
			'',
			$template
		);
		$index                  = array_search( $relative_template_path, $templates, true );

		// If the template hiearchy algorithm has successfully located a PHP template file,
		// we will only consider block templates with higher or equal specificity.
		$templates = array_slice( $templates, 0, $index + 1 );
	}

	$block_template = gutenberg_resolve_template( $type, $templates );

	if ( $block_template ) {
		if ( empty( $block_template->content ) && is_user_logged_in() ) {
			$_wp_current_template_content =
			sprintf(
				/* translators: %s: Template title */
				__( 'Empty template: %s', 'gutenberg' ),
				$block_template->title
			);
		} elseif ( ! empty( $block_template->content ) ) {
			$_wp_current_template_content = $block_template->content;
		}
		if ( isset( $_GET['_wp-find-template'] ) ) {
			wp_send_json_success( $block_template );
		}
	} else {
		if ( $template ) {
			return $template;
		}

		if ( 'index' === $type ) {
			if ( isset( $_GET['_wp-find-template'] ) ) {
				wp_send_json_error( array( 'message' => __( 'No matching template found.', 'gutenberg' ) ) );
			}
		} else {
			return false; // So that the template loader keeps looking for templates.
		}
	}

	// Add hooks for template canvas.
	// Add viewport meta tag.
	add_action( 'wp_head', 'gutenberg_viewport_meta_tag', 0 );

	// Render title tag with content, regardless of whether theme has title-tag support.
	remove_action( 'wp_head', '_wp_render_title_tag', 1 );    // Remove conditional title tag rendering...
	add_action( 'wp_head', 'gutenberg_render_title_tag', 1 ); // ...and make it unconditional.

	// This file will be included instead of the theme's template file.
	return gutenberg_dir_path() . 'lib/template-canvas.php';
}

/**
 * Return the correct 'wp_template' to render for the request template type.
 *
 * Accepts an optional $template_hierarchy argument as a hint.
 *
 * @param string   $template_type      The current template type.
 * @param string[] $template_hierarchy (optional) The current template hierarchy, ordered by priority.
 * @return null|array {
 *  @type WP_Post|null template_post A template post object, or null if none could be found.
 *  @type int[] A list of template parts IDs for the template.
 * }
 */
function gutenberg_resolve_template( $template_type, $template_hierarchy ) {
	if ( ! $template_type ) {
		return null;
	}

	if ( empty( $template_hierarchy ) ) {
		$template_hierarchy = array( $template_type );
	}

	$slugs = array_map(
		'gutenberg_strip_php_suffix',
		$template_hierarchy
	);

	// Find all potential templates 'wp_template' post matching the hierarchy.
	$query     = array(
		'theme'    => wp_get_theme()->get_stylesheet(),
		'slug__in' => $slugs,
	);
	$templates = gutenberg_get_block_templates( $query );

	// Order these templates per slug priority.
	// Build map of template slugs to their priority in the current hierarchy.
	$slug_priorities = array_flip( $slugs );

	usort(
		$templates,
		function ( $template_a, $template_b ) use ( $slug_priorities ) {
			return $slug_priorities[ $template_a->slug ] - $slug_priorities[ $template_b->slug ];
		}
	);

	return count( $templates ) ? $templates[0] : null;
}

/**
 * Displays title tag with content, regardless of whether theme has title-tag support.
 *
 * @see _wp_render_title_tag()
 */
function gutenberg_render_title_tag() {
	echo '<title>' . wp_get_document_title() . '</title>' . "\n";
}

/**
 * Returns the markup for the current template.
 */
function gutenberg_get_the_template_html() {
	global $_wp_current_template_content;
	global $wp_embed;

	if ( ! $_wp_current_template_content ) {
		if ( is_user_logged_in() ) {
			return '<h1>' . esc_html__( 'No matching template found', 'gutenberg' ) . '</h1>';
		}
		return;
	}

	$content = $wp_embed->run_shortcode( $_wp_current_template_content );
	$content = $wp_embed->autoembed( $content );
	$content = do_blocks( $content );
	$content = wptexturize( $content );
	if ( function_exists( 'wp_filter_content_tags' ) ) {
		$content = wp_filter_content_tags( $content );
	} else {
		$content = wp_make_content_images_responsive( $content );
	}
	$content = str_replace( ']]>', ']]&gt;', $content );

	// Wrap block template in .wp-site-blocks to allow for specific descendant styles
	// (e.g. `.wp-site-blocks > *`).
	return '<div class="wp-site-blocks">' . $content . '</div>';
}

/**
 * Renders the markup for the current template.
 */
function gutenberg_render_the_template() {
	echo gutenberg_get_the_template_html(); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Renders a 'viewport' meta tag.
 *
 * This is hooked into {@see 'wp_head'} to decouple its output from the default template canvas.
 */
function gutenberg_viewport_meta_tag() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Strips .php suffix from template file names.
 *
 * @access private
 *
 * @param string $template_file Template file name.
 * @return string Template file name without extension.
 */
function gutenberg_strip_php_suffix( $template_file ) {
	return preg_replace( '/\.(php|html)$/', '', $template_file );
}

/**
 * Removes post details from block context when rendering a block template.
 *
 * @param array $context Default context.
 *
 * @return array Filtered context.
 */
function gutenberg_template_render_without_post_block_context( $context ) {
	/*
	 * When loading a template or template part directly and not through a page
	 * that resolves it, the top-level post ID and type context get set to that
	 * of the template part. Templates are just the structure of a site, and
	 * they should not be available as post context because blocks like Post
	 * Content would recurse infinitely.
	 */
	if ( isset( $context['postType'] ) &&
			( 'wp_template' === $context['postType'] || 'wp_template_part' === $context['postType'] ) ) {
		unset( $context['postId'] );
		unset( $context['postType'] );
	}

	return $context;
}
add_filter( 'render_block_context', 'gutenberg_template_render_without_post_block_context' );


/**
 * Sets the current WP_Query to return auto-draft posts.
 *
 * The auto-draft status indicates a new post, so allow the the WP_Query instance to
 * return an auto-draft post for template resolution when editing a new post.
 *
 * @param WP_Query $wp_query Current WP_Query instance, passed by reference.
 * @return void
 */
function gutenberg_resolve_template_for_new_post( $wp_query ) {
	remove_filter( 'pre_get_posts', 'gutenberg_resolve_template_for_new_post' );

	// Pages.
	$page_id = isset( $wp_query->query['page_id'] ) ? $wp_query->query['page_id'] : null;

	// Posts, including custom post types.
	$p = isset( $wp_query->query['p'] ) ? $wp_query->query['p'] : null;

	$post_id = $page_id ? $page_id : $p;
	$post    = get_post( $post_id );

	if (
		$post &&
		'auto-draft' === $post->post_status &&
		current_user_can( 'edit_post', $post->ID )
	) {
		$wp_query->set( 'post_status', 'auto-draft' );
	}
}
