<?php
/**
 * Ranks Meta Boxes
 *
 * @package Badgeos
 * @subpackage Ranks
 * @author LearningTimes
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 * @link https://credly.com/
 */

/**
 * Register custom meta box for Badgeos ranks type.
 */
function badgeos_ranks_type_metaboxes() {

	/**
	 * Start with an underscore to hide fields from custom fields list.
	 */
	$prefix = '_badgeos_';

	/**
	 * Setup our $post_id, if available.
	 */
	$post_id = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : 0;

	/**
	 * New Achievement Types.
	 */
	$settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
	$cmb_obj  = new_cmb2_box(
		array(
			'id'           => 'rankstypedata',
			'title'        => esc_html__( 'Ranks Data', 'badgeos' ),
			'object_types' => array( trim( $settings['ranks_main_post_type'] ) ), // Post type.
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left.
		)
	);
	$cmb_obj->add_field(
		array(
			'name' => esc_html__( 'Plural Name', 'badgeos' ),
			'desc' => esc_html__( 'The plural name for this rank (Title is singular name).', 'badgeos' ),
			'id'   => $prefix . 'plural_name',
			'type' => 'text_medium',
		)
	);
	$cmb_obj->add_field(
		array(
			'name' => esc_html__( 'Show in Menu?', 'badgeos' ),
			'desc' => ' ' . esc_html__( 'Yes, show this achievement in the badgeos menu.', 'badgeos' ),
			'id'   => $prefix . 'show_in_menu',
			'type' => 'checkbox',
		)
	);

}
add_action( 'cmb2_admin_init', 'badgeos_ranks_type_metaboxes' );

/**
 * If plural name is empty then add post title in plural name.
 *
 * @param string $updated updated value.
 * @param string $action action.
 * @param string $cmb cmb.
 */
function badgeos_rank_plural_name_save_meta( $updated, $action, $cmb ) {
	$value      = $cmb->value;
	$object_id  = $cmb->object_id;
	$page_title = get_the_title( $object_id );
	if ( empty( $value ) && ! empty( $page_title ) && trim( strtolower( $page_title ) ) !== 'auto draft' ) {
		update_post_meta( $object_id, '_badgeos_plural_name', $page_title );
	}

}
add_action( 'cmb2_save_field__badgeos_plural_name', 'badgeos_rank_plural_name_save_meta', 10, 3 );

/**
 * If plural name is empty then add post title in plural name.
 *
 * @param string $updated updated value.
 * @param string $action action.
 * @param string $cmb cmb.
 */
function badgeos_point_plural_name_save_meta( $updated, $action, $cmb ) {
	$value      = $cmb->value;
	$object_id  = $cmb->object_id;
	$page_title = get_the_title( $object_id );
	if ( empty( $value ) && ! empty( $page_title ) && trim( strtolower( $page_title ) ) !== 'auto draft' ) {
		badgeos_utilities::update_post_meta( $object_id, '_point_plural_name', $page_title );
	}
}
add_action( 'cmb2_save_field__point_plural_name', 'badgeos_point_plural_name_save_meta', 10, 3 );

/**
 * Register custom meta box for badgeos WP ranks type Data.
 */
function badgeos_ranks_type_data_metaboxes() {

	global $post;

	/**
	 * Start with an underscore to hide fields from custom fields list.
	 */
	$prefix = '_ranks_';

	$ranks_types = badgeos_get_rank_types_slugs();

	/**
	 * Setup our $post_id, if available
	 */
	$post_id = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : 0;

	$cmb_obj = new_cmb2_box(
		array(
			'id'           => 'bos_ranks_sub_type_data',
			'title'        => esc_html__( 'Ranks Data', 'badgeos' ),
			'object_types' => $ranks_types,
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		)
	);
	$cmb_obj->add_field(
		array(
			'name' => esc_html__( 'Congratulations Text', 'badgeos' ),
			'desc' => esc_html__( 'Displayed after rank is earned.', 'badgeos' ),
			'id'   => $prefix . 'congratulations_text',
			'type' => 'hidden',
		)
	);
	$cmb_obj->add_field(
		array(
			'name' => esc_html__( 'Points Awarded', 'badgeos' ),
			'desc' => ' ' . esc_html__( 'Points awarded for earning this rank (optional). Leave empty if no points are awarded.', 'badgeos' ),
			'id'   => $prefix . 'points',
			'type' => 'credit_field',
		)
	);

	$cmb_obj->add_field(
		array(
			'name'    => esc_html__( 'Allow reach with points?', 'badgeos' ),
			'id'      => $prefix . 'unlock_with_points',
			'type'    => 'radio_inline',
			'options' => array(
				'Yes' => esc_html__( 'Yes', 'cmb2' ),
				'No'  => esc_html__( 'No', 'cmb2' ),
			),
			'default' => 'Yes',
		)
	);
	$cmb_obj->add_field(
		array(
			'name' => esc_html__( 'Points to Unlock', 'badgeos' ),
			'desc' => ' ' . esc_html__( 'Points required for earning this achievement.', 'badgeos' ),
			'id'   => $prefix . 'points_to_unlock',
			'type' => 'credit_field',
		)
	);
}
add_action( 'cmb2_admin_init', 'badgeos_ranks_type_data_metaboxes' );

/**
 * Register custom meta box for badgeos ranks type Detail.
 */
function badgeos_ranks_type_detail_metaboxes() {

	global $post, $wpdb;

	$prefix      = '_ranks_';
	$ranks_types = badgeos_get_rank_types_slugs();

	$post_id  = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : 0;
	$priority = 1;

	if ( intval( $post_id ) > 0 ) {
		$priority = get_post_field( 'menu_order', $post_id );
	}

	$cmb_obj = new_cmb2_box(
		array(
			'id'           => 'bos_ranks_sub_type_detail',
			'title'        => esc_html__( 'Rank Detail', 'badgeos' ),
			'object_types' => $ranks_types,
			'context'      => 'side',
			'priority'     => 'default',
			'show_names'   => true,
		)
	);

	$cmb_obj->add_field(
		array(
			'name'       => esc_html__( 'Priority', 'badgeos' ),
			'desc'       => esc_html__( 'The rank priority defines the order an user can achieve ranks. User will need to get lower priority ranks before get this one.', 'badgeos' ),
			'id'         => 'menu_order',
			'default'    => $priority,
			'type'       => 'text',
			'attributes' => array(
				'type'    => 'number',
				'pattern' => '\d*',
			),
		)
	);
}
add_action( 'cmb2_admin_init', 'badgeos_ranks_type_detail_metaboxes' );
