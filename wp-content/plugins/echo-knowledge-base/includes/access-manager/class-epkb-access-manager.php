<?php
/**
 * Not applicable: Methods that add code that Access Manager needs within core KB to run
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Access_Manager {

	/**
	 *
	 *   DO NOT CHANGE THESE FUNCTIONS UNLESS ACCESS MANAGER CHANGED
	 *
	 */

	public static function add_action_display_category_notices( $taxonomy ) {
	}

	public static function hide_menu_access_control() {
		return false;
	}

	public static function show_debug_user_access() {
		return [];
	}

	public static function display_debug_data() {
		return '';
	}

	public static function get_logs() {
		return [];
	}

	public static function reset_logs() {
	}

	public static function get_kb_id( $current_id ) {
		return $current_id;
	}

	public static function get_capability_type( $kb_id ) {
		return 'post';
	}

	public static function get_categories_capabilities( $capability_type ) {
		return [];
	}

	public static function get_tags_capabilities( $capability_type ) {
		return [];
	}

	public static function get_cpt_capabilities( $capability_type ) {
		return [];
	}

	public static function add_admin_body_class() {
	}

	public static function get_count( $articles ) {
		return count( $articles );
	}

	public static function filter_seq_data( $kb_config, &$category_seq_data, &$articles_seq_data ) {
	}

	public static function setup_data() {
	}

	public static function get_kb_id2() {
		return EPKB_KB_Config_DB::DEFAULT_KB_ID;
	}

	public static function filter_result( $result ) {
		return $result;
	}

	public static function search_limit() {
		return 20;
	}

	public static function limit_result( $result ) {
		return $result;
	}

	public static function get_seq_data( $kb_id ) {
		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		return [ 'articles_seq_data' => $articles_seq_data, 'category_seq_data' => $category_seq_data ];
	}

	// protect KB Main Page - only KB Manager and administrator can see even empty KB Main Page
	public static function get_seq_data2( $kb_id, &$category_seq_data, &$articles_seq_data) {
		return true;
	}

	public static function get_seq_data3( $kb_id, &$category_seq_data, &$articles_seq_data ) {
		return true;
	}

	public static function get_seq_data4( $kb_id, &$category_seq_data, &$articles_seq_data ) {
		return true;
	}

	public static function get_seq_data5( $kb_id, $articles_seq_data ) {
		return $articles_seq_data;
	}

	public static function get_seq_data6( $kb_id, &$category_seq_data, &$articles_seq_data, $article_id ) {
		return true;
	}

	public static function get_seq_data7( $kb_id, &$category_seq_data, &$articles_seq_data, $category_empty_msg ) {
		return [ 'initial_posts_per_page' => 1, 'initial_paged' => 1 ];
	}

	public static function check_access( $kb_id ) {
		return [];
	}

	public static function report_on_error() {
		return false;
	}

	public static function plugin_name() {
		return 'echo-knowledge-base/echo-knowledge-base.php';
	}

	public static function show_error( $field_spec, $input_value, $result ) {
		EPKB_Logging::add_log( 'Please change the value of ' . $field_spec['label'] . ' field. Current value: "' . $input_value . '" - ' . $result->get_error_message() . ', code: ' . $result->get_error_message(), $result );
	}

	public static function limit_query() {
		return false;
	}

	public static function delete_access_data() {
	}

	public static function multi_site() {
		global $wpdb;
		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			epkb_get_instance()->kb_config_obj->reset_cache();
			epkb_activate_plugin_do();
			restore_current_blog();
		}
	}

	public static function menu_items( $post_type_name='' ) {
	}

	/**
	 * Do not log anything if not in the back-end or not logged in as an admin
	 *
	 * @return bool
	 */
	public static function can_log_message( $report_on_error ) {

		// we cannot log too early
		if ( ! function_exists('wp_get_current_user') ) {
			return false;
		}

		// sometimes we expect errors
		if ( ! $report_on_error ) {
			return false;
		}

		$is_debug_on = get_transient( EPKB_Debug_Controller::EPKB_DEBUG );

		return  ! empty($is_debug_on) && current_user_can( 'manage_options' );
	}

	public static function plugin_setup() {
		return get_option( 'epkb_version' );
	}

	public static function finish_plugin_setup() {
	}

	public static function is_context_check( $context ) {
		return null;
	}

	public static function get_manager_capability( $contexts ) {
		return '';
	}

	public static function get_group_role( $role ) {
		return '';
	}

	public static function groups_capability() {
		return '';
	}

	public static function is_context_continue( $context ) {
		return false;
	}

	/**
	 * Get options list for Access Control settings
	 *
	 * @param false $include_author
	 *
	 * @return array
	 */
	public static function get_access_control_options( $kb_config, $include_author=false ) {

		$access_control_ptions = [];

		if ( $include_author ) {
			$access_control_ptions[EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY] = self::get_admins_distinct_box() . self::get_editors_distinct_box() . self::get_authors_distinct_box() . self::get_users_with_capability_distinct_box( EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY );
		}

		$access_control_ptions[EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY] = self::get_admins_distinct_box() . self::get_editors_distinct_box() . self::get_users_with_capability_distinct_box( EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY );
		$access_control_ptions[EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY]     = self::get_admins_distinct_box();

		return $access_control_ptions;
	}

	private static function get_admins_distinct_box() {
		return sprintf( esc_html__( '%sAdmins%s', 'echo-knowledge-base' ), '<span class="epkb-admin__distinct-box epkb-admin__distinct-box--high">', '</span>' );
	}

	private static function get_users_with_capability_distinct_box( $capability ) {
		return sprintf( esc_html__( '%susers with "%s" capability%s', 'echo-knowledge-base' ), '<span class="epkb-admin__distinct-box epkb-admin__distinct-box--lowest">', $capability, '</span>' );
	}

	public static function is_ui_access_loop( $context ) {
		return false;
	}

	private static function get_editors_distinct_box() {
		return sprintf( esc_html__( '%sEditors%s', 'echo-knowledge-base' ), '<span class="epkb-admin__distinct-box epkb-admin__distinct-box--middle">', '</span>' );
	}

	private static function get_authors_distinct_box() {
		return sprintf( esc_html__( '%sAuthors%s', 'echo-knowledge-base' ), '<span class="epkb-admin__distinct-box epkb-admin__distinct-box--low">', '</span>' );
	}
}