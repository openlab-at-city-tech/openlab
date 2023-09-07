<?php

/**
* The admin-specific functionality of the plugin.
*
* @author     Ben Roberts
*/
class BP_Toolkit_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $bp_toolkit    The ID of this plugin.
     */
    private  $bp_toolkit ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $bp_toolkit       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     *
     *@since    1.0.0
     */
    public function __construct( $bp_toolkit, $version )
    {
        $this->bp_toolkit = $bp_toolkit;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        global  $pagenow ;
        global  $post_type ;
        
        if ( 'admin.php' === $pagenow && strpos( $_GET['page'], "bp-toolkit" ) !== false || 'edit-tags.php' === $pagenow && 'report-type' === $_GET['taxonomy'] || $post_type === 'report' ) {
            wp_enqueue_style(
                $this->bp_toolkit,
                plugin_dir_url( __FILE__ ) . 'assets/css/bp-toolkit-admin.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                'tipso',
                plugin_dir_url( __FILE__ ) . 'assets/css/tipso.min.css',
                array(),
                $this->version,
                'all'
            );
        }
    
    }
    
    /**
     * Show Version 3.0 notice.
     *
     * @since    3.0.0
     */
    public function subscriber_check_activation_notice()
    {
        
        if ( get_transient( 'bptk-admin-notice-activation' ) ) {
            ?>
			<div class="notice notice-success is-dismissible">
				<p>Welcome to Version 3 of Block, Suspend, Report for BuddyPress!</p>
				<p>We have heaps of <a href="<?php 
            echo  BP_TOOLKIT_SUPPORT ;
            ?>">new documentation</a> to guide you through the new features.</p>
				<p>As we now use the BuddyPress native emails service, please take a moment to update your email settings under the Report > Emails tab.</p>
			</div>
			<?php 
            delete_transient( 'bptk-admin-notice-activation' );
        }
    
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        global  $pagenow ;
        global  $post_type ;
        
        if ( 'admin.php' === $pagenow && strpos( $_GET['page'], "bp-toolkit" ) !== false || 'edit-tags.php' === $pagenow && 'report-type' === $_GET['taxonomy'] || $post_type === 'report' ) {
            wp_enqueue_script(
                $this->bp_toolkit,
                plugin_dir_url( __FILE__ ) . '/assets/js/bp-toolkit-admin.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            wp_enqueue_script(
                'font-awesome',
                'https://use.fontawesome.com/cf8dc4a043.js',
                array(),
                '3',
                true
            );
            wp_enqueue_script(
                'tipso',
                plugin_dir_url( __FILE__ ) . '/assets/js/tipso.min.js',
                array( 'jquery' ),
                $this->version,
                false
            );
        }
    
    }
    
    /**
     * Set up the admin page menu link.
     *
     * @since    1.0.1
     */
    public function add_admin_menu()
    {
        $args = array(
            'post_type'   => 'report',
            'numberposts' => -1,
            'meta_query'  => array( array(
            'key'     => 'is_read',
            'value'   => '0',
            'compare' => '=',
        ) ),
        );
        $unread_reports = count( get_posts( $args ) );
        $menu_title = ( $unread_reports ? sprintf( 'BSR <span class="awaiting-mod bptk-unread-count">%d</span>', $unread_reports ) : 'BSR' );
        add_menu_page(
            __( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ),
            $menu_title,
            'manage_options',
            'bp-toolkit',
            array( $this, 'render_page' ),
            'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="-10 0 100 100"><title>logo2</title><path fill="rgba(240,246,252,.6)" d="M79.5,81.4a22.65,22.65,0,0,0-.6,5.3,21.07,21.07,0,0,0,36,14.9,21.41,21.41,0,0,0,5.5-20.5C113.6,84,95.5,90,79.5,81.4Z" transform="translate(-61.3 -49.97)"/><path fill="rgba(240,246,252,.6)" d="M130.2,63.5c-1.5-.1-3-.1-4.5-.3a28.82,28.82,0,0,1-15.4-7.1c-1.9-1.6-3.7-3.2-5.7-4.7-2.7-2-5.1-1.9-7.7.3-1.3,1.1-2.5,2.3-3.7,3.5a26.78,26.78,0,0,1-17.4,8.1l-4.4.3c-.7,0-.9.3-.8,1a7.84,7.84,0,0,0,.5,1.4c.7,1.3,1.4,2.5,2.1,3.7.8,1.5,1.6,3.1,2.3,4.7a18,18,0,0,1,.6,2.2c.2,0,.4-.1.6-.1,1.7-.4,3.5-.8,5.3-1.1a89.51,89.51,0,0,1,13.7-1.3c4.2-.1,8.4-.1,12.6.1,3.3.2,6.7.7,10,1.2l1.6.3c1,.2,2,.5,3,.8a9.3,9.3,0,0,1,2.2-4.7L129,67c.6-.8,1.3-1.6,1.8-2.4S131.1,63.6,130.2,63.5Zm-25.6,5.6c-1,.8-2.2,1.5-3.3,2.3a1.1,1.1,0,0,1-.8,0,13.88,13.88,0,0,1-4.1-3.1,3.39,3.39,0,0,1-1-2.7V60.5h11.1V65A4.77,4.77,0,0,1,104.6,69.1Z" transform="translate(-61.3 -49.97)"/><path fill="rgba(240,246,252,.6)" d="M119.7,77.4c-16.8-3.4-32.8-1.2-39.5,0-2.1.4-3.4.7-3.4.7.9.6,1.8,1.1,2.6,1.6,16,8.6,34.1,2.5,40.9-.3a22.15,22.15,0,0,0,2.7-1.3C122,77.9,120.9,77.6,119.7,77.4Z" transform="translate(-61.3 -49.97)"/><path fill="rgba(240,246,252,.6)" d="M138.5,130.1a47.36,47.36,0,0,0-.8-6,52.6,52.6,0,0,0-1.5-6,31.9,31.9,0,0,0-2.4-5.4,22.15,22.15,0,0,0-3.4-4.5,13.85,13.85,0,0,0-4.7-2.9,17.39,17.39,0,0,0-6.1-1.1,6.85,6.85,0,0,0-2.3,1.2c-1.2.8-2.6,1.7-4.1,2.6a21.71,21.71,0,0,1-5.9,2.6,23.25,23.25,0,0,1-7.3,1.2,20.34,20.34,0,0,1-7.3-1.2,30.33,30.33,0,0,1-5.9-2.6c-1.5-1-2.9-1.9-4.1-2.6a6.85,6.85,0,0,0-2.3-1.2,17,17,0,0,0-6.1,1.1,13.85,13.85,0,0,0-4.7,2.9,17.73,17.73,0,0,0-3.4,4.5,26.14,26.14,0,0,0-2.4,5.4,43.79,43.79,0,0,0-1.5,6c-.4,2.1-.6,4.1-.8,6s-.2,3.7-.2,5.7c0,4.4,1.3,7.9,4,10.4S71.5,150,76,150h48a15.21,15.21,0,0,0,10.7-3.8c2.7-2.5,4-6,4-10.4C138.7,133.8,138.6,131.9,138.5,130.1Zm-20.6-2.5a3.18,3.18,0,0,1-1.5,2.2,32.12,32.12,0,0,1-3,2.1.85.85,0,0,1-.7,0,12.49,12.49,0,0,1-3.7-2.8,3.41,3.41,0,0,1-.9-2.4v-4.5h9.8C117.9,124.1,118,125.9,117.9,127.6Z" transform="translate(-61.3 -49.97)"/></svg>' ),
            25
        );
        add_submenu_page(
            'bp-toolkit',
            __( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ),
            __( 'Dashboard', 'bp-toolkit' ),
            'manage_options',
            'bp-toolkit',
            array( $this, 'render_page' )
        );
        
        if ( class_exists( 'BuddyPress' ) ) {
            add_submenu_page(
                'bp-toolkit',
                __( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ),
                __( 'Block', 'bp-toolkit' ),
                'manage_options',
                'bp-toolkit-block',
                array( $this, 'render_page' )
            );
            add_submenu_page(
                'bp-toolkit',
                __( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ),
                __( 'Suspend', 'bp-toolkit' ),
                'manage_options',
                'bp-toolkit-suspend',
                array( $this, 'render_page' )
            );
            add_submenu_page(
                'bp-toolkit',
                __( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ),
                __( 'Report', 'bp-toolkit' ),
                'manage_options',
                'bp-toolkit-report',
                array( $this, 'render_page' )
            );
        }
    
    }
    
    /**
     * Register the Report CPT and a Report Type taxonomy.
     *
     * @since    2.0.0
     */
    public function setup_report_post_type()
    {
        $labelsTax = array(
            'name'               => _x( 'Report types', 'taxonomy general name', 'bp-toolkit' ),
            'singular_name'      => _x( 'Report type', 'taxonomy singular name', 'bp-toolkit' ),
            'search_items'       => __( 'Search Report types', 'bp-toolkit' ),
            'all_items'          => __( 'All Report types', 'bp-toolkit' ),
            'edit_item'          => __( 'Edit Report type', 'bp-toolkit' ),
            'update_item'        => __( 'Update Report type', 'bp-toolkit' ),
            'add_new_item'       => __( 'Add New Report type', 'bp-toolkit' ),
            'new_item_name'      => __( 'New Report type Name', 'bp-toolkit' ),
            'not_found'          => __( 'No Report types found', 'bp-toolkit' ),
            'not_found_in_trash' => __( 'No Reports types found in Trash', 'bp-toolkit' ),
            'menu_name'          => __( 'Report types', 'bp-toolkit' ),
        );
        register_taxonomy( 'report-type', array( 'report' ), array(
            'hierarchical'      => true,
            'labels'            => $labelsTax,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'show_admin_column' => true,
            'meta_box_cb'       => array( $this, 'render_report_types_metabox' ),
        ) );
        $labels = array(
            'name'                  => _x( 'Reports', 'Post Type General Name', 'bp-toolkit' ),
            'singular_name'         => _x( 'Report', 'Post Type Singular Name', 'bp-toolkit' ),
            'menu_name'             => __( 'Report', 'bp-toolkit' ),
            'name_admin_bar'        => __( 'Report', 'bp-toolkit' ),
            'archives'              => __( 'Item Archives', 'bp-toolkit' ),
            'attributes'            => __( 'Item Attributes', 'bp-toolkit' ),
            'parent_item_colon'     => __( 'Parent Item:', 'bp-toolkit' ),
            'all_items'             => __( 'All Reports', 'bp-toolkit' ),
            'add_new_item'          => __( 'Add New Report', 'bp-toolkit' ),
            'add_new'               => __( 'Add New Report', 'bp-toolkit' ),
            'new_item'              => __( 'New Report', 'bp-toolkit' ),
            'edit_item'             => __( 'Edit Report', 'bp-toolkit' ),
            'update_item'           => __( 'Update Report', 'bp-toolkit' ),
            'view_item'             => __( 'View Item', 'bp-toolkit' ),
            'view_items'            => __( 'View Items', 'bp-toolkit' ),
            'search_items'          => __( 'Search Item', 'bp-toolkit' ),
            'not_found'             => __( 'No Reports found', 'bp-toolkit' ),
            'not_found_in_trash'    => __( 'No Reports found in Trash', 'bp-toolkit' ),
            'featured_image'        => __( 'Featured Image', 'bp-toolkit' ),
            'set_featured_image'    => __( 'Set featured image', 'bp-toolkit' ),
            'remove_featured_image' => __( 'Remove featured image', 'bp-toolkit' ),
            'use_featured_image'    => __( 'Use as featured image', 'bp-toolkit' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'bp-toolkit' ),
            'items_list'            => __( 'Items list', 'bp-toolkit' ),
            'items_list_navigation' => __( 'Items list navigation', 'bp-toolkit' ),
            'filter_items_list'     => __( 'Filter items list', 'bp-toolkit' ),
        );
        $pq = false;
        $args = array(
            'label'                => __( 'Report', 'bp-toolkit' ),
            'description'          => __( '', 'bp-toolkit' ),
            'labels'               => $labels,
            'supports'             => array( 'title' ),
            'taxonomies'           => array( 'report-types' ),
            'hierarchical'         => false,
            'public'               => false,
            'show_ui'              => true,
            'show_in_menu'         => 'bp-toolkit',
            'menu_position'        => 1,
            'menu_icon'            => '',
            'show_in_admin_bar'    => true,
            'show_in_nav_menus'    => true,
            'can_export'           => false,
            'has_archive'          => 'reports',
            'publicly_queryable'   => $pq,
            'show_in_rest'         => false,
            'rest_base'            => 'Reports',
            'capability_type'      => 'post',
            'map_meta_cap'         => true,
            'register_meta_box_cb' => array( $this, 'Report_meta_box_init' ),
        );
        register_post_type( 'report', $args );
    }
    
    /**
     * Add report type capabilities and roles.
     *
     * @since		3.1.0
     *
     */
    public function add_report_caps()
    {
        // Give admins suspension rights
        $role_object = get_role( 'administrator' );
        $role_object->add_cap( 'suspend_users' );
    }
    
    /**
     * Add report type capabilities and roles, via activation. Can be used as a backup method to reinstall role and caps.
     *
     * @since		3.1.3
     *
     */
    public function force_add_report_caps()
    {
    }
    
    /**
     * Keep our menu highlighted when on report-type taxonomy page.
     *
     * @param		string		$parent_file The parent menu item.
     *
     * @return		string		$parent_file The parent menu item.
     *
     * @since		2.0.0
     */
    public function prefix_highlight_taxonomy_parent_menu( $parent_file )
    {
        if ( get_current_screen()->taxonomy == 'report-type' ) {
            $parent_file = 'bp-toolkit';
        }
        return $parent_file;
    }
    
    /**
     * Initialise the report CPT metaboxes.
     *
     * @since    2.0.0
     */
    public function report_meta_box_init()
    {
        remove_meta_box( 'categoriesdiv', 'report', 'side' );
        remove_meta_box( 'commentsdiv', 'report', 'normal' );
        remove_meta_box( 'commentstatusdiv', 'report', 'normal' );
        add_meta_box(
            'report-details',
            __( 'Report Form', 'bp-Toolkit' ),
            array( $this, 'report_cpt_meta_box_content' ),
            'report',
            'normal'
        );
        $currentScreen = get_current_screen();
        if ( $currentScreen->action !== "add" ) {
            add_meta_box(
                'report-moderation',
                __( 'Report Moderation', 'bp-Toolkit' ),
                array( $this, 'report_cpt_meta_box_moderation' ),
                'report',
                'side'
            );
        }
    }
    
    /**
     * Render report CPT main meta box content.
     *
     * @param WP_Post $post The post object.
     *
     *@since    2.0.0
     *
     */
    public function report_cpt_meta_box_content( $post )
    {
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'report_cpt-main_meta_box', 'report_cpt-main_meta_box_nonce' );
        require_once plugin_dir_path( __FILE__ ) . 'partials/report-cpt-main-metabox.php';
    }
    
    /**
     * Render report CPT moderation meta box content.
     *
     * @param WP_Post $post The post object.
     *
     *@since    3.0.0
     *
     */
    public function report_cpt_meta_box_moderation( $post )
    {
        
        if ( $post->_bptk_admin_created == 1 ) {
            echo  'Reports created by administrators relate to members, rather than individual items. Therefore moderation is not available.' ;
            return;
        } elseif ( !metadata_exists( 'post', $post->ID, '_bptk_item_id' ) ) {
            echo  'Moderation is not available for reports made prior to Version 3' ;
            return;
        }
        
        // See if user is suspended, to set our Suspend User ajax button
        $status = get_user_meta( $post->_bptk_member_reported, 'bptk_suspend', true );
        
        if ( $status == 0 || empty($status) ) {
            $is_suspended = false;
        } else {
            $is_suspended = true;
        }
        
        // See if the item this report refers to is moderated, to set our Moderate ajax button
        $option = 'bptk_moderated_' . $post->_bptk_activity_type . '_list';
        $exists = get_option( $option );
        
        if ( $exists && in_array( $post->_bptk_item_id, $exists ) ) {
            $moderated = true;
        } else {
            $moderated = false;
        }
        
        echo  '<div class="report-moderation-metabox">' ;
        $count = bptk_reports_per_item( get_post_meta( $post->ID, '_bptk_item_id', true ) );
        $count_ord = bptk_ordinal( $count );
        /* Create Nonce */
        $moderation_metabox_nonce = wp_create_nonce( 'moderation_metabox_nonce' );
        ?>

		<p><?php 
        printf(
            /* translators: %s: Number of times the item was reported */
            __( 'This is the %s time this item has been reported.', 'bp-toolkit' ),
            $count_ord
        );
        ?></p>



		<fieldset class="bptk-field-wrap">

			<input id="bptk-moderation-metabox-nonce" type="hidden" name="moderation_metabox_nonce" value="<?php 
        echo  $moderation_metabox_nonce ;
        ?>">

			<div class="button <?php 
        echo  ( $post->is_upheld == '1' ? 'bptk-report-upheld' : '' ) ;
        ?>" id="bptk-toggle-uphold" type="submit" data-id="<?php 
        echo  $post->ID ;
        ?>">&nbsp;<div class="bptk-ajax-loader"> </div> </div>

			<div class="button <?php 
        echo  ( $is_suspended ? 'bptk-member-suspended' : '' ) ;
        ?>" id="bptk-toggle-suspension" type="submit" data-id="<?php 
        echo  $post->_bptk_member_reported ;
        ?>">&nbsp;<div class="bptk-ajax-loader"> </div> </div>

			<?php 
        
        if ( $post->_bptk_activity_type != 'member' ) {
            ?>
				<div class="button <?php 
            echo  ( $moderated ? 'bptk-item-moderated' : '' ) ;
            ?>" id="bptk-toggle-moderation" type="submit" data-id="<?php 
            echo  $post->_bptk_item_id ;
            ?>" data-activity="<?php 
            echo  $post->_bptk_activity_type ;
            ?>"  data-post="<?php 
            echo  $post->ID ;
            ?>">&nbsp;<div class="bptk-ajax-loader"> </div> </div>
			<?php 
        }
        
        ?>

		</fieldset>



		<?php 
        echo  '</div>' ;
    }
    
    /**
     * Toggle upheld status via Ajax.
     *
     * @since   3.0.0
     *
     */
    public function toggle_uphold()
    {
        check_ajax_referer( 'moderation_metabox_nonce', 'nonce' );
        
        if ( $_POST['status'] == "true" ) {
            bptk_set_upheld( $_POST['id'] );
        } else {
            if ( $_POST['status'] == "false" ) {
                bptk_remove_upheld( $_POST['id'] );
            }
        }
        
        wp_die();
    }
    
    /**
     * Toggle suspension status via Ajax.
     *
     * @since   3.0.0
     *
     */
    public function toggle_suspension()
    {
        check_ajax_referer( 'moderation_metabox_nonce', 'nonce' );
        
        if ( $_POST['status'] == "true" ) {
            bptk_suspend_member( $_POST['id'] );
        } else {
            if ( $_POST['status'] == "false" ) {
                bptk_unsuspend_member( $_POST['id'] );
            }
        }
        
        wp_die();
    }
    
    /**
     * Toggle moderation status via Ajax.
     *
     * @since   3.0.0
     *
     */
    public function toggle_moderation()
    {
        check_ajax_referer( 'moderation_metabox_nonce', 'nonce' );
        
        if ( $_POST['status'] == "true" ) {
            bptk_moderate_activity( $_POST['id'], $_POST['activity'], $_POST['post'] );
        } else {
            if ( $_POST['status'] == "false" ) {
                bptk_unmoderate_activity( $_POST['id'], $_POST['activity'], $_POST['post'] );
            }
        }
        
        wp_die();
    }
    
    /**
     * Quick Moderate via Ajax.
     *
     * @since   3.0.0
     *
     */
    public function quick_moderate()
    {
        check_ajax_referer( 'quick_moderate_nonce', 'nonce' );
        if ( !isset( $_POST['item_id'] ) || !isset( $_POST['item_id'] ) ) {
            return;
        }
        
        if ( in_array( $_POST['item_id'], bptk_get_moderated_list( $_POST['activity'] ) ) ) {
            bptk_unmoderate_activity( $_POST['item_id'], $_POST['activity'] );
            echo  'We found a moderated activity with that item ID, and have unmoderated it.' ;
        } else {
            bptk_moderate_activity( $_POST['item_id'], $_POST['activity'] );
            echo  'We found an activity with that item ID, and have moderated it.' ;
        }
        
        wp_die();
    }
    
    /**
     * Save a report made by an administrator.
     *
     * @param int $post_id The post.
     *
     *@since    2.0.0
     *
     */
    public function save_report( $post_id, $post, $update )
    {
        // Checks save status
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $_POST['report_cpt-main_meta_box_nonce'] ) && wp_verify_nonce( $_POST['report_cpt-main_meta_box_nonce'], 'report_cpt-main_meta_box' ) ? 'true' : 'false' );
        // Exits script depending on save status
        if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
            return;
        }
        /* OK, it's safe for us to save the data now. */
        // Sanitize the user input.
        global  $wpdb ;
        update_post_meta( $post_id, 'is_upheld', 1 );
        if ( isset( $_POST['_bptk_member_reported'] ) ) {
            update_post_meta( $post_id, '_bptk_member_reported', sanitize_text_field( $_POST['_bptk_member_reported'] ) );
        }
        if ( isset( $_POST['_bptk_reported_by'] ) ) {
            update_post_meta( $post_id, '_bptk_reported_by', sanitize_text_field( $_POST['_bptk_reported_by'] ) );
        }
        if ( isset( $_POST['_bptk_activity_type'] ) ) {
            update_post_meta( $post_id, '_bptk_activity_type', sanitize_text_field( $_POST['_bptk_activity_type'] ) );
        }
        if ( isset( $_POST['bptk_report_substantiated'] ) ) {
            update_post_meta( $post_id, 'bptk_report_substantiated', sanitize_text_field( $_POST['bptk_report_substantiated'] ) );
        }
        if ( isset( $_POST['_bptk_admin_created'] ) ) {
            update_post_meta( $post_id, '_bptk_admin_created', 1 );
        }
    }
    
    /**
     * Render our custom header - allows admin notices to render underneath it.
     *
     * @since    2.0.0
     */
    public function add_custom_header()
    {
        $string = 'https://wordpress.org/support/plugin/bp-toolkit/';
        $suffix = '';
        
        if ( $this->is_bptk_page() ) {
            ?>
			<div class="bptk-admin-header">
				<div class="bptk-title">
					<a href="<?php 
            echo  BP_TOOLKIT_HOMEPAGE ;
            ?>"><img src="<?php 
            echo  plugin_dir_url( __FILE__ ) . '/assets/images/logo.jpg' ;
            ?>" alt=""> <h1><?php 
            _e( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' );
            echo  $suffix ;
            ?></h1></a>
				</div>
				<div class="bptk-meta">
					<span class="bptk-version"><?php 
            echo  'v' . BP_TOOLKIT_VERSION ;
            ?></span>
					<a target="_blank" class="button" href="<?php 
            echo  BP_TOOLKIT_SUPPORT ;
            ?>"><?php 
            _e( 'Documentation', 'bp-toolkit' );
            ?></a>
					<a target="_blank" class="button button-primary" href="<?php 
            echo  $string ;
            ?>"><?php 
            _e( 'Get Support', 'bp-toolkit' );
            ?></a>
				</div>
				<h2 class="bptk-notices-trigger"></h2>
			</div>
			<?php 
        } else {
            return;
        }
    
    }
    
    /**
     * Checks if we are on a bp-toolkit page.
     *
     * @return		boolean Is this a bp-toolkit page?
     *@since    2.0.0
     *
     */
    public function is_bptk_page()
    {
        global  $pagenow ;
        global  $post_type ;
        if ( 'admin.php' === $pagenow && ($_GET['page'] === 'bp-toolkit-pricing' || $_GET['page'] === 'bp-toolkit-contact') ) {
            return false;
        }
        
        if ( 'admin.php' === $pagenow && strpos( $_GET['page'], "bp-toolkit" ) !== false || 'edit-tags.php' === $pagenow && 'report-type' === $_GET['taxonomy'] || 'edit.php' === $pagenow && $post_type === 'report' || 'post-new.php' === $pagenow && $post_type === 'report' ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    /**
     * Initializes blank slate content if a list table is empty.
     *
     * @since 2.0.0
     */
    public function blank_slate()
    {
        $blank_slate = new BPTK_Blank_Slate();
        $blank_slate->init();
    }
    
    /**
     * Render non-CPT pages.
     *
     * @return mixed
     *@since    2.0.0
     *
     */
    public function render_page()
    {
        ?>

		<div class="wrap bptk_admin"><?php 
        global  $pagenow ;
        global  $post_type ;
        
        if ( 'admin.php' === $pagenow && $_GET['page'] === 'bp-toolkit' ) {
            require_once plugin_dir_path( __FILE__ ) . 'partials/dashboard.php';
        } elseif ( 'admin.php' === $pagenow && $_GET['page'] === 'bp-toolkit-block' ) {
            require_once plugin_dir_path( __FILE__ ) . 'partials/block.php';
        } elseif ( 'admin.php' === $pagenow && $_GET['page'] === 'bp-toolkit-suspend' ) {
            require_once plugin_dir_path( __FILE__ ) . 'partials/suspend.php';
        } elseif ( 'admin.php' === $pagenow && $_GET['page'] === 'bp-toolkit-report' ) {
            require_once plugin_dir_path( __FILE__ ) . '/class-bp-toolkit-report-settings.php';
            $report_settings = new BPTK_Report_Settings();
            $report_settings->create_page();
        }
        
        echo  '</div>' ;
    }
    
    /**
     * Render non-CPT pages.
     *
     * @return mixed
     *@since    2.0.0
     *
     */
    public function render_moderation_queue()
    {
        require_once plugin_dir_path( __FILE__ ) . '/class-bp-toolkit-moderation-queue.php';
        $moderation_queue = new BPTK_Moderation_Queue_List_Table();
        $moderation_queue->prepare_items();
        ?>
            <div class="wrap bptk_admin">
                <div id="icon-users" class="icon32"></div>
                <h2><?php 
        esc_html_e( 'Moderation Queue', 'bp-toolkit' );
        ?></h2>
                <form method="get">
                    <input type="hidden" name="page" value="bp-toolkit-moderation-queue" />
                    <?php 
        $moderation_queue->display();
        ?>
                </form>
            </div>
        <?php 
    }
    
    /**
     * Render the metabox that shows the different report types.
     *
     * @param WP_Post $post Post object.
     *
     *@since    2.0.0
     *
     */
    public function render_report_types_metabox( $post )
    {
        $box = array(
            'args' => array(
            'taxonomy' => 'report-type',
        ),
        );
        $defaults = array(
            'taxonomy' => 'category',
        );
        
        if ( !isset( $box['args'] ) || !is_array( $box['args'] ) ) {
            $args = array();
        } else {
            $args = $box['args'];
        }
        
        $r = wp_parse_args( $args, $defaults );
        $tax_name = esc_attr( $r['taxonomy'] );
        $taxonomy = get_taxonomy( $r['taxonomy'] );
        ?>
			<div id="taxonomy-<?php 
        echo  $tax_name ;
        ?>" class="categorydiv">
				<ul id="<?php 
        echo  $tax_name ;
        ?>-tabs" class="category-tabs">
					<li class="tabs"><a href="#<?php 
        echo  $tax_name ;
        ?>-all"><?php 
        echo  $taxonomy->labels->all_items ;
        ?></a></li>
					<li class="hide-if-no-js"><a href="#<?php 
        echo  $tax_name ;
        ?>-pop"><?php 
        echo  esc_html( $taxonomy->labels->most_used ) ;
        ?></a></li>
				</ul>

				<div id="<?php 
        echo  $tax_name ;
        ?>-pop" class="tabs-panel" style="display: none;">
					<ul id="<?php 
        echo  $tax_name ;
        ?>checklist-pop" class="categorychecklist form-no-clear" >
						<?php 
        $popular_ids = wp_popular_terms_checklist( $tax_name );
        ?>
					</ul>
				</div>

				<div id="<?php 
        echo  $tax_name ;
        ?>-all" class="tabs-panel">
					<?php 
        $name = ( $tax_name == 'category' ? 'post_category' : 'tax_input[' . $tax_name . ']' );
        echo  "<input type='hidden' name='{$name}[]' value='0' />" ;
        // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
        ?>
					<ul id="<?php 
        echo  $tax_name ;
        ?>checklist" data-wp-lists="list:<?php 
        echo  $tax_name ;
        ?>" class="categorychecklist form-no-clear">
						<?php 
        wp_terms_checklist( $post->ID, array(
            'taxonomy'     => $tax_name,
            'popular_cats' => $popular_ids,
        ) );
        ?>
					</ul>
				</div>
				<h4><a href="<?php 
        echo  bptk_fs()->get_upgrade_url() ;
        ?>"><?php 
        _e( 'Upgrade now', 'bp-toolkit' );
        ?></a> <?php 
        _e( ' to the Professional Edition to create your own report types, or to edit the default types above.', 'bp-toolkit' );
        ?></h4>
			</div>
			<?php 
    }
    
    /**
     * Set up the Block settings section and fields.
     *
     * @since    1.0.1
     */
    public function create_block_settings()
    {
        add_settings_section(
            'block_section',
            '',
            '',
            'block_section'
        );
        // Add fields below
        register_setting( 'block_section', 'block_section', array( $this, 'bptk_validate' ) );
    }
    
    /**
     * .
     *
     * @since 2.0.0
     */
    public function rebuild_blocks_cb()
    {
        $nonce = wp_create_nonce( 'bptk-rebuild-blocks-nonce' );
        ?>

		<fieldset class="bptk-field-wrap">
			<span class="bptk-field-label"><?php 
        _e( 'Rebuild Blocks', 'bp-toolkit' );
        ?></span>
			<legend class="screen-reader-text"><?php 
        _e( 'Rebuild Blocks', 'bp-toolkit' );
        ?></legend>
			<button id="bptk-rebuild-blocks" class="button-primary" data-nonce="<?php 
        echo  $nonce ;
        ?>"><?php 
        _e( 'Rebuild', 'bp-toolkit' );
        ?></button>
			<span class="bptk-field-description"><?php 
        _e( 'Use of this button should be unnecessary.', 'bp-toolkit' );
        ?></span>
			<pre id="bptk-rebuild-blocks-debug"></pre>
		</fieldset>

		<?php 
    }
    
    /**
     * .
     *
     * @since 2.0.0
     */
    public function rebuild_blocks()
    {
        if ( wp_verify_nonce( $_POST['nonce'], 'bptk-rebuild-blocks-nonce' ) ) {
            // Check to see if not done previously, otherwise rebuild blocks database
            
            if ( get_option( 'bptk_blocks_already_rebuilt' ) == false ) {
                $users = get_users();
                foreach ( $users as $user ) {
                    $block_list = get_user_meta( $user->ID, 'bptk_block', true );
                    if ( $block_list ) {
                        // echo "\r\n";
                        // echo $user->display_name . ' is blocking the following:';
                        // echo "\r\n";
                        // print_r($block_list);
                        foreach ( $block_list as $key => $blocked_user ) {
                            $list = get_user_meta( $blocked_user, 'bptk_blocked_by', true );
                            
                            if ( $list ) {
                                // echo "\r\n";
                                // echo 'Blocked user already has a list of people he has been blocked by:';
                                // echo "\r\n";
                                // print_r($list);
                                $key = array_search( $user->ID, $list );
                                // echo "\r\n" .  $key;
                                
                                if ( $key === false ) {
                                    // echo 'We searched the list and could not find ' . $user->display_name . '. Adding...';
                                    $list[] = $user->ID;
                                    update_user_meta( $blocked_user, 'bptk_blocked_by', $list );
                                    // echo "\r\n";
                                    // echo 'Blocked user list updated:';
                                    // echo "\r\n";
                                    // print_r($list);
                                } else {
                                    // echo "\r\n" . $user->ID . ' is already in the array above, right? Moving on...';
                                }
                            
                            } else {
                                $list = array();
                                $list[] = $user->ID;
                                update_user_meta( $blocked_user, 'bptk_blocked_by', $list );
                                // echo "\r\n";
                                // echo 'Blocked user was not blocked by anyone previously. List created:';
                                // echo "\r\n";
                                // print_r($list);
                            }
                        
                        }
                    }
                }
                update_option( 'bptk_blocks_already_rebuilt', 1 );
            }
        
        }
        wp_die();
    }
    
    /**
     * Set up the Suspend settings section and fields.
     *
     * @since    1.0.1
     */
    public function create_suspend_settings()
    {
        add_settings_section(
            'suspend_section',
            '',
            '',
            'suspend_section'
        );
        add_settings_field(
            'bptk_suspend_login_message',
            // Our field ID
            '',
            // Our field title
            array( $this, 'bptk_suspend_login_message_cb' ),
            // Our field callback
            'suspend_section',
            // The page our field is going on
            'suspend_section'
        );
        register_setting( 'suspend_section', 'suspend_section', array( $this, 'bptk_validate' ) );
    }
    
    /**
     * Set up the Styling settings section and fields.
     *
     * @since    1.0.1
     */
    public function create_styling_settings()
    {
        add_settings_section(
            'styling_section',
            '',
            '',
            'styling_section'
        );
        // Add fields below
        add_settings_field(
            'bptk_custom_css',
            // Our field ID
            '',
            // Our field title
            array( $this, 'bptk_custom_css_cb' ),
            // Our field callback
            'styling_section',
            // The page our field is going on
            'styling_section'
        );
        register_setting( 'styling_section', 'styling_section', array( $this, 'bptk_validate_styling' ) );
    }
    
    /**
     * Specify the Suspend login message.
     *
     * @since 2.0.0
     */
    public function bptk_suspend_login_message_cb()
    {
        $options = get_option( 'suspend_section' );
        $arg = ( isset( $options['bptk_suspend_login_message'] ) ? $options['bptk_suspend_login_message'] : '' );
        ?>

		<fieldset class="bptk-field-wrap">
			<span class="bptk-field-label"><?php 
        _e( 'Login Error Message', 'bp-toolkit' );
        ?></span>
			<legend class="screen-reader-text"><?php 
        _e( 'Login Error Message', 'bp-toolkit' );
        ?></legend>
			<input type="text" id="bptk_suspend_login_message" class="bptk-text_medium" name="suspend_section[bptk_suspend_login_message]" value="<?php 
        echo  $arg ;
        ?>" />
			<span class="bptk-field-description"><?php 
        _e( 'Enter the message to display to suspended users when they attempt to log in.', 'bp-toolkit' );
        ?></span>
		</fieldset>

		<?php 
    }
    
    /**
     * Render the custom CSS box
     *
     * @since 1.0.1
     */
    public function bptk_custom_css_cb()
    {
        $options = get_option( 'styling_section' );
        $arg = ( isset( $options['bptk_custom_css'] ) ? $options['bptk_custom_css'] : '' );
        echo  '<textarea id="bptk_custom_css" name="styling_section[bptk_custom_css]" rows="13">' . $arg . '</textarea>' ;
    }
    
    /**
     * Validate our code. Keep everyone safe and happy. Doesn't strip slashes so unicode can be used in CSS
     *
     * @since 1.0.1
     */
    public function bptk_validate_styling( $input )
    {
        $output = array();
        // return map_deep( $inputArray, 'upgm_sanitise');
        $output1 = $this->strip_tags_deep( $input );
        // Return the array processing any additional functions filtered by this action
        return apply_filters( 'bptk_validate_styling', $output1, $input );
    }
    
    /**
     * Strip tags from string or array
     *
     * @param  mixed  array or string to strip
     *
     * @return mixed  stripped value
     *@since 2.0
     *
     */
    public function strip_tags_deep( $value )
    {
        
        if ( is_array( $value ) ) {
            foreach ( $value as $key => $val ) {
                $value[$key] = $this->strip_tags_deep( $val );
            }
        } elseif ( is_string( $value ) ) {
            $value = strip_tags( $value );
        }
        
        return $value;
    }
    
    /**
     * Validate our code. Keep everyone safe and happy.
     *
     * @since 1.0.1
     */
    public function bptk_validate( $input )
    {
        $output = array();
        // return map_deep( $inputArray, 'upgm_sanitise');
        $output = stripslashes_deep( $input );
        $output1 = $this->strip_tags_deep( $output );
        // Return the array processing any additional functions filtered by this action
        return apply_filters( 'bptk_validate', $output1, $input );
    }
    
    /**
     * Add rating links to the admin dashboard
     *
     * @param string		$footer_text The existing footer text
     *
     * @return 	string
     * @since  	2.0.0
     *
     * @global 	string $pagenow
     * @global 	string $post_type
     *
     */
    public function admin_rate_us( $footer_text )
    {
        global  $pagenow ;
        global  $post_type ;
        // $parameterPage = isset($_GET['page']) ? $_GET['page'] : '';
        
        if ( 'admin.php' === $pagenow && strpos( $_GET['page'], "bp-toolkit" ) !== false || 'edit-tags.php' === $pagenow && 'report-type' === $_GET['taxonomy'] || 'edit.php' === $pagenow && $post_type === 'report' || 'post-new.php' === $pagenow && $post_type === 'report' ) {
            $rate_text = sprintf(
                /* translators: %s: Link to 5 star rating */
                __( 'If you like <strong>Block, Suspend, Report for BuddyPress</strong> please leave us a %s rating. It takes a minute and helps a lot. Thanks in advance!', 'bp-toolkit' ),
                '<a href="https://wordpress.org/support/view/plugin-reviews/bp-toolkit?filter=5#postform" target="_blank" class="bp-toolkit-rating-link" style="text-decoration:none;" data-rated="' . esc_attr__( 'Thanks :)', 'bp-toolkit' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
            );
            return $rate_text;
        } else {
            return $footer_text;
        }
    
    }
    
    /**
     * Handle admin notices.
     *
     * @since    2.1.0
     *
     */
    public function handle_admin_notices()
    {
        if ( isset( $_REQUEST['mark_read'] ) ) {
            // depending on how many posts were changed, make the message different
            printf( '<div id="message" class="updated notice is-dismissible"><p>' . _n( '%s report has been marked as read.', '%s reports have been marked as read.', intval( $_REQUEST['mark_read'] ) ) . '</p></div>', intval( $_REQUEST['mark_read'] ) );
        }
        if ( isset( $_REQUEST['mark_unread'] ) ) {
            // depending on how many posts were changed, make the message different
            printf( '<div id="message" class="updated notice is-dismissible"><p>' . _n( '%s report has been marked as unread.', '%s reports have been marked as unread.', intval( $_REQUEST['mark_unread'] ) ) . '</p></div>', intval( $_REQUEST['mark_unread'] ) );
        }
    }
    
    /**
     * Set up the BPTK Dashboard Widget.
     *
     * @since 3.0.0
     *
     */
    public function add_dashboard_widgets()
    {
        wp_add_dashboard_widget( 'bptk_dashboard_widget', esc_html__( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ), array( &$this, 'bptk_dashboard_widget_function' ) );
        global  $wp_meta_boxes ;
        // Get the regular dashboard widgets array
        // (which has our new widget already but at the end).
        $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
        // Backup and delete our new dashboard widget from the end of the array.
        $example_widget_backup = array(
            'bptk_dashboard_widget' => $normal_dashboard['bptk_dashboard_widget'],
        );
        unset( $normal_dashboard['bptk_dashboard_widget'] );
        // Merge the two arrays together so our widget is at the beginning.
        $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );
        // Save the sorted array back into the original metaboxes.
        $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
        // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
    }
    
    /**
     * Populate our Dashboard Widget.
     *
     * @since 3.0.0
     *
     */
    public function bptk_dashboard_widget_function()
    {
        ?>

		<style media="screen">
		#bptk_dashboard_widget .bptk-column {
			width: 46%;
		}
		#bptk_dashboard_widget .bptk-column .bptk-latest-news-date {
			display: none;
		}
		#bptk_dashboard_widget table {
			border-top: 1px solid #ECECEC;
			margin-top: 6px;
			padding-top: 6px;
		}
		#bptk_dashboard_widget td {
			padding: 3px 0;
		}
		#bptk_dashboard_widget td  ul{
			margin: 0px;
		}
		#bptk_dashboard_widget .b {
			padding-right: 5px;
			text-align: right;
			width: 1%;
		}
		#bptk_dashboard_widget .bptk-meta-container {
			border-top: 1px solid #eee;
			clear: both;
			margin: 12px -12px -12px;
			padding: 0 12px;
		}
		#bptk_dashboard_widget .bptk-meta-links {
			list-style: none;
			margin: 0;
			padding: 0;
		}
		#bptk_dashboard_widget .bptk-meta-links li {
			display: inline-block;
			margin: 0;
			position: relative;
		}
		#bptk_dashboard_widget .bptk-meta-links li:after {
			background: #ddd;
			content: ' ';
			height: 14px;
			position: absolute;
			right: -1px;
			top: 13px;
			width: 1px;
		}
		#bptk_dashboard_widget .bptk-meta-links li:first-child:after, #bptk_dashboard_widget .bptk-meta-links li:last-child:after {
			display: none;
		}
		#bptk_dashboard_widget .bptk-meta-links a {
			display: block;
			padding: 10px 8px;
		}

		/*------------------------------------*\
		#CLEARFIX
		\*------------------------------------*/
		.clearfix:after {
			content: "";
			display: table;
			clear: both;
		}
		</style>

		<div class="clearfix">

			<div class="bptk-column alignleft">
				<h4 class="sub"><?php 
        esc_html_e( 'Reports Status', 'bp-toolkit' );
        ?></h4>

				<table>
					<tbody>
						<?php 
        $unread = bptk_get_unread_reports();
        
        if ( $unread ) {
            ?>
							<tr>
								<td><a href="<?php 
            echo  esc_url( admin_url( 'edit.php?post_type=report' ) ) ;
            ?>">
									<?php 
            printf( _n(
                'You have %s unread report waiting',
                'You have %s unread reports waiting',
                count( $unread ),
                'bp-toolkit'
            ), number_format_i18n( count( $unread ) ) );
            ?>
								</a>
							</td>
						</tr>
						<?php 
        } else {
            echo  '<td>' . __( 'You have no reports requiring your attention', 'bp-toolkit' ) . '</td>' ;
        }
        
        ?>
				</tbody>
			</table>
		</div>

		<div class="bptk-column alignright">
			<h4 class="sub"><?php 
        esc_html_e( 'Latest News', 'bp-toolkit' );
        ?></h4>

			<table>
				<tbody>
					<tr><td>
						<?php 
        $this->latest_news();
        ?>
					</td></tr>
				</tbody>
			</table>
		</div>

	</div>

	<div class="bptk-meta-container">
		<ul class="bptk-meta-links">
			<li><b><?php 
        esc_html_e( 'Useful Links:', 'bp-toolkit' );
        ?></b></li>
			<li><a href="<?php 
        echo  BP_TOOLKIT_HOMEPAGE ;
        ?>"><?php 
        esc_html_e( 'Homepage', 'bp-toolkit' );
        ?></a></li>
			<?php 
        ?>
				<li>
					<a href="<?php 
        echo  esc_url( 'https://wordpress.org/support/plugin/bp-toolkit/' ) ;
        ?>"><?php 
        esc_html_e( 'Free Support', 'bp-toolkit' );
        ?></a>
				</li>
				<li>
					<?php 
        printf( __( '<a href="%s">Upgrade</a>', 'bp-toolkit' ), bptk_fs()->get_upgrade_url() );
        ?>
				</li>
			<?php 
        ?>
		</ul>
	</div>
	<?php 
    }
    
    /**
     * Fetch latest RSS feed entries.
     *
     * @return mixed
     *@since    2.0.0
     *
     */
    public function latest_news()
    {
        // Get RSS Feed(s)
        include_once ABSPATH . WPINC . '/feed.php';
        // Get a SimplePie feed object from the specified feed source.
        $rss = fetch_feed( 'https://www.bouncingsprout.com/tag/bsr,plugins/feed/ ' );
        $maxitems = 0;
        
        if ( !is_wp_error( $rss ) ) {
            // Checks that the object is created correctly
            // Figure out how many total items there are, but limit it to 5.
            $maxitems = $rss->get_item_quantity( 3 );
            // Build an array of all the items, starting with element 0 (first element).
            $rss_items = $rss->get_items( 0, $maxitems );
        }
        
        ?>

		<ul>
			<?php 
        
        if ( $maxitems == 0 ) {
            ?>
				<li><?php 
            _e( 'No news found.', 'bp-toolkit' );
            ?></li>
			<?php 
        } else {
            ?>
				<?php 
            // Loop through each feed item and display each item as a hyperlink.
            ?>
				<?php 
            foreach ( $rss_items as $item ) {
                ?>
					<li>
						<a href="<?php 
                echo  esc_url( $item->get_permalink() ) ;
                ?>"
							title="<?php 
                printf( __( 'Posted %s', 'bp-toolkit' ), $item->get_date( get_option( 'date_format' ) ) );
                ?>">
							<?php 
                echo  esc_html( $item->get_title() ) ;
                ?>
						</a>
						<span class="bptk-latest-news-date"><?php 
                echo  esc_html( $item->get_date( get_option( 'date_format' ) ) ) ;
                ?></span>
					</li>
				<?php 
            }
            ?>
			<?php 
        }
        
        ?>
		</ul>
		<?php 
    }

}