<?php

/*** GENERIC NON-KB functions  ***/

/**
 * When page is added/updated, check if it contains KB main page shortcode. If it does, add the page to KB config.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function epkb_add_main_page_if_required( $post_id, $post ) {

	// ignore autosave/revision which is not article submission; same with ajax and bulk edit
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) || empty( $post->post_status ) ) {
		return;
	}

	// ignore posts, block templates and articles; only interested in pages that were deleted, or published, or restored (can be restored to draft or to publish)
	if ( in_array( $post->post_type, ['post', 'wp_template'] ) || EPKB_KB_Handler::is_kb_post_type( $post->post_type ) || ! in_array( $post->post_status, array( 'trash', 'draft', 'publish', 'private' ) ) ) {
		return;
	}

	// return if this page does not have KB shortcode, KB layout block, or error occurred; we remove old pages elsewhere
	$kb_id = EPKB_KB_Handler::get_kb_id_from_kb_main_page( $post );
	if ( empty( $kb_id ) ) {
		return;
	}

	// get KB main pages
	$kb_main_pages = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'kb_main_pages' );
	if ( ! is_array( $kb_main_pages ) ) {
		return;
	}

	// if the page is not relevant then remove it
	if ( in_array( $post->post_status, array( 'inherit', 'trash' ) ) ) {
		if ( ! isset( $kb_main_pages[$post_id] ) ) {
			return;
		}
		unset( $kb_main_pages[$post_id] );
		epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'kb_main_pages', $kb_main_pages );
		return;
	}

	// don't update if the page is stored with the same title
	if ( in_array( $post_id, array_keys( $kb_main_pages ) ) && $kb_main_pages[$post_id] == $post->post_title ) {
		return;
	}

	$kb_main_pages[$post_id] = $post->post_title;

	// sanitize and save configuration in the database.
	$result = epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'kb_main_pages', $kb_main_pages );
	if ( is_wp_error( $result ) ) {
		return;
	}
}
add_action( 'save_post', 'epkb_add_main_page_if_required', 10, 2 );

/**
 * If user changed slug for page with KB shortcode or KB layout block, and we do not have matching Article Common Path then let user know.
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function epkb_does_path_for_articles_need_update( $post_id, $post ) {
	
	// ignore autosave/revision which is not article submission; same with ajax and bulk edit
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return;
	}

	// ignore posts and articles and non-published pages
	if ( $post->post_type == 'post' || EPKB_KB_Handler::is_kb_post_type( $post->post_type ) || ! in_array( $post->post_status, array( 'publish', 'private' ) ) ) {
		return;
	}

	// check if we are changing any of the KB Main Pages or their parents
	$kb_config = array();
	$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
	foreach ( $all_kb_configs as $one_kb_config ) {

		$kb_main_pages = empty( $one_kb_config['kb_main_pages'] ) ? array() : $one_kb_config['kb_main_pages'];

		// is saved page KB Main Page?
		if ( isset( $kb_main_pages[$post_id] ) ) {
			$kb_config = $one_kb_config;
			break;
		}

		foreach( $kb_main_pages as $kb_main_page_id => $title ) {
			$ancestors = get_post_ancestors( $post );
			if ( in_array( $kb_main_page_id, $ancestors ) ) {
				$kb_config = $one_kb_config;
				break 2;
			}
		}
	}

	// this page is not KB Main Page or its parent
	if ( empty( $kb_config['kb_main_pages'] ) ) {
		return;
	}

	$notice_id = 'epkb_changed_slug_' . $kb_config['id'];

	EPKB_Admin_Notices::remove_ongoing_notice( $notice_id );

	// get slugs for all KB Main Pages
	$kb_main_page_slugs = array();
	foreach( $kb_config['kb_main_pages'] as $kb_main_page_id => $title ) {

		$slug = EPKB_Core_Utilities::get_main_page_slug( $kb_main_page_id );
		if ( empty( $slug ) ) {
			continue;
		}

		$kb_main_page_slugs[$kb_main_page_id] = $slug;
	}

	if ( empty( $kb_main_page_slugs ) ) {
		return;
	}

	// check if the Article Common Path does not match anymore any of the KB Main Page paths
	if ( in_array( $kb_config['kb_articles_common_path'], $kb_main_page_slugs ) ) {
		return;
	}

	EPKB_Admin_Notices::remove_dismissed_ongoing_notice( $notice_id );
	EPKB_Admin_Notices::add_ongoing_notice( 'warning', $notice_id,
			__( 'We detected that your KB Main Page slug has changed. Please update slug for your articles for KB', 'echo-knowledge-base' ) . ' #' . $kb_config['id'] .
	        ' <a class="epkb-admin__step-cta-box__link" data-target="kb-url" href="' . esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $kb_config['id'] . '&page=epkb-kb-configuration#kb-url' ) ) . '">' .
	        esc_html__( 'Update Slug here', 'echo-knowledge-base' ) . '</a> ' );
	
}
add_action( 'save_post', 'epkb_does_path_for_articles_need_update', 15, 2 );  // needs to run AFTER epkb_add_main_page_if_required()

/**
 * If user deleted page then let them know if the page has active KB shortcode or KB layout block
 * @param $post_id
 */
function epkb_add_delete_kb_page_warning( $post_id ) {

	$post = get_post( $post_id );
	if ( empty( $post ) || empty( $post->post_status ) ) {
		return;
	}

	// only interested in pages
	if ( $post->post_type == 'post' || EPKB_KB_Handler::is_kb_post_type( $post->post_type ) || ! in_array( $post->post_status, array( 'inherit', 'trash', 'publish', 'private' ) ) ) {
		return;
	}

	$kb_id = EPKB_KB_Handler::get_kb_id_from_kb_main_page( $post );
	if ( empty( $kb_id ) ) {
		return;
	}

	$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
	if ( empty( $all_kb_configs ) ) {
		return;
	}

	$kb_articles_common_path = '';
	$kb_status = '';
	foreach ( $all_kb_configs as $kb_config ) {
		if ( $kb_config['id'] == $kb_id ) {
			$kb_articles_common_path = $kb_config['kb_articles_common_path'];
			$kb_status = $kb_config['status'];
			break;
		}
	}

	// do not show message for KBs that were archived or deleted
	if ( empty( $kb_articles_common_path ) || $kb_status != EPKB_KB_Config_Specs::PUBLISHED ) {
		return;
	}
	
	$main_page_slug = EPKB_Core_Utilities::get_main_page_slug( $post_id );
	
	if ( $kb_articles_common_path == $main_page_slug ) {
		EPKB_Admin_Notices::add_one_time_notice( 'warning', sprintf( esc_html__( 'We detected that you deleted KB Main Page "%s". If you did this by accident you can restore here: ', 'echo-knowledge-base' ), $post->post_title ) .
		                                                    ' <a href="' . esc_url( admin_url( 'edit.php?post_status=trash&post_type=page' ) ) . '">' . esc_html__( 'Restore', 'echo-knowledge-base' ) . '</a> ' );
	}
}
add_action( 'wp_trash_post', 'epkb_add_delete_kb_page_warning', 15, 2 ); 

// Add "KB Page" to the page's list 
function epkb_add_post_state( $post_states, $post ) {

	if ( empty( $post->post_type ) || empty( $post->ID ) || $post->post_type != 'page' ) {
		return $post_states;
	}

	// start with default KB
	$kb_config = get_option( EPKB_KB_Config_DB::KB_CONFIG_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID );
	if ( empty( $kb_config ) || is_wp_error( $kb_config ) || empty( $kb_config['kb_main_pages'] ) || ! is_array( $kb_config['kb_main_pages'] ) ) {
		return $post_states;
	}

	if ( in_array( $post->ID, array_keys( $kb_config['kb_main_pages'] ) ) ) {
		$post_states[] = __( 'Knowledge Base', 'echo-knowledge-base' ) . ' #1';
	}

	return $post_states;
}
add_filter( 'display_post_states', 'epkb_add_post_state', 10, 2 );

/**
 * Show notice about CREL
 */
function epkb_crel_notice() {

	// Show CREL Notice for only admin
	if ( function_exists('wp_get_current_user') && ! current_user_can('administrator') ) {
		return;
	}

	EPKB_Admin_Notices::remove_ongoing_notice( 'epkb_crel_notice' );

	// check only on KB pages only
	$is_kb_request = EPKB_KB_Handler::is_kb_request();
	if ( ! $is_kb_request ) {
		return;
	}

	if ( ! EPKB_Site_Builders::is_elementor_enabled() || EPKB_Utilities::is_creative_addons_widgets_enabled() ) {
		return;
	}

	// wait a week before showing the notice
	if ( get_transient( '_epkb_crel_notice' ) ) {
		return;
	}
	if ( ! get_transient( '_epkb_crel_notice2' ) ) {
		set_transient( '_epkb_crel_notice', true, WEEK_IN_SECONDS );
		set_transient( '_epkb_crel_notice2', true, MONTH_IN_SECONDS );
		return;
	}

	$link = '<a href="' . esc_url( 'https://wordpress.org/plugins/creative-addons-for-elementor/' ) . '" target="_blank">' . esc_html__( 'Free Download', 'echo-knowledge-base' ) . '</a>';
	$message = esc_html__( 'Hey, did you know that makers of the Echo Knowledge Page plugin developed free Elementor widgets to help you create amazing documentation? See more details about our Creative Addons here: ' , 'echo-knowledge-base' ) . ' ' . $link;

	EPKB_Admin_Notices::add_ongoing_notice( 'large-info',  'epkb_crel_notice',
		$message,
		__( 'Did you know?' , 'echo-knowledge-base' ),
		'<i class="epkbfa epkbfa-exclamation-triangle"></i>' );

}
add_action( 'admin_init', 'epkb_crel_notice' );

/**
 * Show notices about site builders
 */
new EPKB_Site_Builders();

/**
 * On Categories Page add link to order categories and articles
 */
function epkb_categories_sorting_link() {

	$kb_id = EPKB_KB_Handler::get_current_kb_id();
	if ( empty( $kb_id ) ) {
		return;
	}

	$tax_name = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
	add_action( 'after-' . $tax_name . '-table', 'epkb_categories_sorting_link_add' );
}
add_action( 'admin_init', 'epkb_categories_sorting_link' );

function epkb_categories_sorting_link_add() {

	$current_kb_id = EPKB_KB_Handler::get_current_kb_id();
	if ( empty( $current_kb_id ) ) {
		return;
	}

	if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_order_articles_write' ) ) {
		return;
	} ?>

	<a id="epkb-admin__categories_sorting_link"
	   href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . $current_kb_id . '&page=epkb-kb-configuration#settings__order-articles' ) ); ?>"
	   style="display: none;" target="_blank"><i class="epkbfa epkbfa-sort-amount-asc"></i>
	<span><?php esc_html_e( 'Order Categories for the KB Main Page', 'echo-knowledge-base' ); ?></span></a> <?php
}
