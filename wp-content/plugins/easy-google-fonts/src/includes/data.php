<?php
/**
 * Data Structure Functionality
 *
 * Registers the posttype to represent the data
 * structure of the saved font controls and contains
 * any CRUD logic.
 *
 * @package easy-google-fonts
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace EGF\Data;

/**
 * Register Posttype
 *
 * Registers a new post type to hold any custom
 * font controls created.
 *
 * @since 2.0.0
 */
function register_post_type_for_font_controls() {
	register_post_type(
		'tt_font_control',
		[
			'labels'                => [
				'name'          => __( 'Font Controls', 'easy-google-fonts' ),
				'singular_name' => __( 'Font Control', 'easy-google-fonts' ),
			],
			'public'                => false,
			'hierarchical'          => false,
			'rewrite'               => false,
			'delete_with_user'      => false,
			'query_var'             => false,
			'show_in_rest'          => true,
			'rest_base'             => 'easy-google-fonts',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'              => [
				'custom-fields',
				'title',
			],
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_post_type_for_font_controls' );

/**
 * Register Meta
 *
 * Register metadata to hold properties for
 * each font control.
 *
 * @since 2.0.0
 */
function register_metadata_for_font_controls() {
	register_meta(
		'post',
		'force_styles',
		[
			'object_subtype'    => 'tt_font_control',
			'type'              => 'boolean',
			'description'       => __( 'If enabled each style will have the !important CSS rule applied to it.', 'easy-google-fonts' ),
			'single'            => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'show_in_rest'      => true,
		]
	);

	register_meta(
		'post',
		'control_selectors',
		[
			'object_subtype' => 'tt_font_control',
			'type'           => 'array',
			'description'    => __( "All of the CSS selectors that this font control's styles should be applied to.", 'easy-google-fonts' ),
			'single'         => true,
			'show_in_rest'   => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type' => 'string',
					],
				],
			],
		]
	);

	register_meta(
		'post',
		'control_description',
		[
			'object_subtype'    => 'tt_font_control',
			'type'              => 'string',
			'description'       => __( 'Description of the font control, displayed in the customizer.', 'easy-google-fonts' ),
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
		]
	);

	register_meta(
		'post',
		'min_screen_amount',
		[
			'object_subtype'    => 'tt_font_control',
			'type'              => 'number',
			'description'       => __( 'The minimum screen size media query amount (without units).', 'easy-google-fonts' ),
			'single'            => true,
			'sanitize_callback' => false,
			'show_in_rest'      => true,
		]
	);

	register_meta(
		'post',
		'min_screen_unit',
		[
			'object_subtype'    => 'tt_font_control',
			'type'              => 'string',
			'description'       => __( 'The unit type used for the minimum screen size.', 'easy-google-fonts' ),
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
		]
	);

	register_meta(
		'post',
		'max_screen_amount',
		[
			'object_subtype'    => 'tt_font_control',
			'type'              => 'number',
			'description'       => __( 'The minimum screen size media query amount (without units).', 'easy-google-fonts' ),
			'single'            => true,
			'sanitize_callback' => false,
			'show_in_rest'      => true,
		]
	);

	register_meta(
		'post',
		'max_screen_unit',
		[
			'object_subtype'    => 'tt_font_control',
			'type'              => 'string',
			'description'       => __( 'The unit type used for the minimum screen size.', 'easy-google-fonts' ),
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_metadata_for_font_controls' );

/**
 * Get Font Control ID
 *
 * Gets the unique identifier for
 * each font control.
 *
 * @param int $post_id ID of a 'tt_font_control' post.
 *
 * @return string Custom font control id.
 */
function get_font_control_id( $post_id ) {
	return apply_filters( 'egf_font_control_id', "egf-font-control-{$post_id}", $post_id );
}

/**
 * Get Font Control Description
 *
 * Gets the description text that will be
 * displayed in the customizer interface.
 *
 * @param int $post_id ID of a 'tt_font_control' post.
 */
function get_font_control_description( $post_id ) {
	return apply_filters(
		'egf_font_control_description',
		get_post_meta( $post_id, 'control_description', true ),
		$post_id
	);
}

/**
 * Get Font Control Force Styles
 *
 * Determines if the force styles flag has
 * been enabled for this font control.
 *
 * @param int $post_id ID of a 'tt_font_control' post.
 */
function get_font_control_force_styles( $post_id ) {
	$force_styles = get_post_meta( $post_id, 'force_styles', true );
	return rest_sanitize_boolean(
		apply_filters(
			'egf_font_control_force_styles',
			$force_styles,
			$post_id
		)
	);
}

/**
 * Get Font Control Selectors
 *
 * Gets the css selectors that will be
 * controlled by the font control id.
 *
 * @param int $post_id ID of a 'tt_font_control' post.
 */
function get_font_control_selectors( $post_id ) {
	$selectors = get_post_meta( $post_id, 'control_selectors', true );
	return empty( $selectors ) ? [] : apply_filters(
		'egf_font_control_selectors',
		$selectors,
		$post_id
	);
}

/**
 * Get Font Control Min Screen
 *
 * Gets the min media query that will be
 * controlled by the font control id.
 *
 * @param int $post_id ID of a 'tt_font_control' post.
 */
function get_font_control_min_screen( $post_id ) {
	$amount = get_post_meta( $post_id, 'min_screen_amount', true );
	$unit   = get_post_meta( $post_id, 'min_screen_unit', true );

	return apply_filters(
		'egf_font_control_min_screen',
		[
			'amount' => empty( $amount ) ? '' : $amount,
			'unit'   => empty( $unit ) ? '' : $unit,
		],
		$post_id
	);
}

/**
 * Get Font Control Max Screen
 *
 * Gets the max media query that will be
 * controlled by the font control id.
 *
 * @param int $post_id ID of a 'tt_font_control' post.
 */
function get_font_control_max_screen( $post_id ) {
	$amount = get_post_meta( $post_id, 'max_screen_amount', true );
	$unit   = get_post_meta( $post_id, 'max_screen_unit', true );

	return apply_filters(
		'egf_font_control_max_screen',
		[
			'amount' => empty( $amount ) ? '' : $amount,
			'unit'   => empty( $unit ) ? 'px' : $unit,
		],
		$post_id
	);
}

/**
 * Delete All Font Controls
 *
 * @return boolean true after all font controls have been deleted.
 */
function delete_all_font_controls() {
	$all_font_controls = new \WP_Query(
		[
			'post_type'      => 'tt_font_control',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		]
	);

	foreach ( $all_font_controls->posts as $font_control_id ) {
		wp_delete_post( $font_control_id, true );
	}

	if ( ! empty( $all_font_controls->posts ) ) {
		wp_reset_postdata();
	}

	return true;
}
