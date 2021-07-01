<?php
/**
 * Server-side rendering of the `core/template-part` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/template-part` block on the server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The render.
 */
function gutenberg_render_block_core_template_part( $attributes ) {
	static $seen_ids = array();

	$template_part_id = null;
	$content          = null;
	$area             = WP_TEMPLATE_PART_AREA_UNCATEGORIZED;

	if (
		isset( $attributes['slug'] ) &&
		isset( $attributes['theme'] ) &&
		wp_get_theme()->get_stylesheet() === $attributes['theme']
	) {
		$template_part_id    = $attributes['theme'] . '//' . $attributes['slug'];
		$template_part_query = new WP_Query(
			array(
				'post_type'      => 'wp_template_part',
				'post_status'    => 'publish',
				'post_name__in'  => array( $attributes['slug'] ),
				'tax_query'      => array(
					array(
						'taxonomy' => 'wp_theme',
						'field'    => 'slug',
						'terms'    => $attributes['theme'],
					),
				),
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		$template_part_post  = $template_part_query->have_posts() ? $template_part_query->next_post() : null;
		if ( $template_part_post ) {
			// A published post might already exist if this template part was customized elsewhere
			// or if it's part of a customized template.
			$content    = $template_part_post->post_content;
			$area_terms = get_the_terms( $template_part_post, 'wp_template_part_area' );
			if ( ! is_wp_error( $area_terms ) && false !== $area_terms ) {
				$area = $area_terms[0]->name;
			}
		} else {
			// Else, if the template part was provided by the active theme,
			// render the corresponding file content.
			$template_part_file_path = get_stylesheet_directory() . '/block-template-parts/' . $attributes['slug'] . '.html';
			if ( 0 === validate_file( $attributes['slug'] ) && file_exists( $template_part_file_path ) ) {
				$content = _gutenberg_inject_theme_attribute_in_content( file_get_contents( $template_part_file_path ) );
			}
		}
	}

	if ( is_null( $content ) && is_user_logged_in() ) {
		if ( ! isset( $attributes['slug'] ) ) {
			// If there is no slug this is a placeholder and we dont want to return any message.
			return;
		}
		return sprintf(
			/* translators: %s: Template part slug. */
			__( 'Template part has been deleted or is unavailable: %s' ),
			$attributes['slug']
		);
	}

	if ( isset( $seen_ids[ $template_part_id ] ) ) {
		if ( ! is_admin() ) {
			trigger_error(
				sprintf(
					// translators: %s are the block attributes.
					__( 'Could not render Template Part block with the attributes: <code>%s</code>. Block cannot be rendered inside itself.' ),
					wp_json_encode( $attributes )
				),
				E_USER_WARNING
			);
		}

		// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
		// is set in `wp_debug_mode()`.
		$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
			defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;
		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__( '[block rendering halted]' ) :
			'';
	}

	// Run through the actions that are typically taken on the_content.
	$seen_ids[ $template_part_id ] = true;
	$content                       = do_blocks( $content );
	unset( $seen_ids[ $template_part_id ] );
	$content = wptexturize( $content );
	$content = convert_smilies( $content );
	$content = shortcode_unautop( $content );
	if ( function_exists( 'wp_filter_content_tags' ) ) {
		$content = wp_filter_content_tags( $content );
	} else {
		$content = wp_make_content_images_responsive( $content );
	}
	$content = do_shortcode( $content );

	if ( empty( $attributes['tagName'] ) ) {
		$defined_areas = gutenberg_get_allowed_template_part_areas();
		$area_tag      = 'div';
		foreach ( $defined_areas as $defined_area ) {
			if ( $defined_area['area'] === $area && isset( $defined_area['area_tag'] ) ) {
				$area_tag = $defined_area['area_tag'];
			}
		}
		$html_tag = $area_tag;
	} else {
		$html_tag = esc_attr( $attributes['tagName'] );
	}
	$wrapper_attributes = get_block_wrapper_attributes();

	return "<$html_tag $wrapper_attributes>" . str_replace( ']]>', ']]&gt;', $content ) . "</$html_tag>";
}

/**
 * Returns an array of variation objects for the template part block.
 *
 * @return array Array containing the block variation objects.
 */
function gutenberg_build_template_part_block_variations() {
	$variations    = array();
	$defined_areas = gutenberg_get_allowed_template_part_areas();
	foreach ( $defined_areas as $area ) {
		if ( 'uncategorized' !== $area['area'] ) {
			$variations[] = array(
				'name'        => $area['area'],
				'title'       => $area['label'],
				'description' => $area['description'],
				'attributes'  => array(
					'area' => $area['area'],
				),
				'scope'       => array( 'inserter' ),
				'icon'        => $area['icon'],
			);
		}
	}
	return $variations;
}

/**
 * Registers the `core/template-part` block on the server.
 */
function gutenberg_register_block_core_template_part() {
	register_block_type_from_metadata(
		__DIR__ . '/template-part',
		array(
			'render_callback' => 'gutenberg_render_block_core_template_part',
			'variations'      => gutenberg_build_template_part_block_variations(),
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_template_part', 20 );
