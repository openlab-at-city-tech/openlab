<?php

/**
* The admin-specific functionality of the plugin.
*
* @author     Ben Roberts <me@therealbenroberts.com>
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
     * @since    1.0.0
     * @param      string    $bp_toolkit       The name of this plugin.
     * @param      string    $version    The version of this plugin.
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
            wp_register_style(
                'select2css',
                '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css',
                false,
                '1.0',
                'all'
            );
            wp_enqueue_style( 'select2css' );
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
            wp_register_script(
                'select2',
                '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js',
                array( 'jquery' ),
                '1.0',
                true
            );
            wp_enqueue_script( 'select2' );
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
        }
    
    }
    
    /**
     * Set up the admin page menu link.
     *
     * @since    1.0.1
     */
    public function add_admin_menu()
    {
        add_menu_page(
            __( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ),
            __( 'BSR', 'bp-toolkit' ),
            'manage_options',
            'bp-toolkit',
            array( $this, 'render_page' ),
            'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="-10 0 100 100"><title>logo2</title><path fill="pink" d="M79.5,81.4a22.65,22.65,0,0,0-.6,5.3,21.07,21.07,0,0,0,36,14.9,21.41,21.41,0,0,0,5.5-20.5C113.6,84,95.5,90,79.5,81.4Z" transform="translate(-61.3 -49.97)"/><path fill="pink" d="M130.2,63.5c-1.5-.1-3-.1-4.5-.3a28.82,28.82,0,0,1-15.4-7.1c-1.9-1.6-3.7-3.2-5.7-4.7-2.7-2-5.1-1.9-7.7.3-1.3,1.1-2.5,2.3-3.7,3.5a26.78,26.78,0,0,1-17.4,8.1l-4.4.3c-.7,0-.9.3-.8,1a7.84,7.84,0,0,0,.5,1.4c.7,1.3,1.4,2.5,2.1,3.7.8,1.5,1.6,3.1,2.3,4.7a18,18,0,0,1,.6,2.2c.2,0,.4-.1.6-.1,1.7-.4,3.5-.8,5.3-1.1a89.51,89.51,0,0,1,13.7-1.3c4.2-.1,8.4-.1,12.6.1,3.3.2,6.7.7,10,1.2l1.6.3c1,.2,2,.5,3,.8a9.3,9.3,0,0,1,2.2-4.7L129,67c.6-.8,1.3-1.6,1.8-2.4S131.1,63.6,130.2,63.5Zm-25.6,5.6c-1,.8-2.2,1.5-3.3,2.3a1.1,1.1,0,0,1-.8,0,13.88,13.88,0,0,1-4.1-3.1,3.39,3.39,0,0,1-1-2.7V60.5h11.1V65A4.77,4.77,0,0,1,104.6,69.1Z" transform="translate(-61.3 -49.97)"/><path fill="pink" d="M119.7,77.4c-16.8-3.4-32.8-1.2-39.5,0-2.1.4-3.4.7-3.4.7.9.6,1.8,1.1,2.6,1.6,16,8.6,34.1,2.5,40.9-.3a22.15,22.15,0,0,0,2.7-1.3C122,77.9,120.9,77.6,119.7,77.4Z" transform="translate(-61.3 -49.97)"/><path fill="pink" d="M138.5,130.1a47.36,47.36,0,0,0-.8-6,52.6,52.6,0,0,0-1.5-6,31.9,31.9,0,0,0-2.4-5.4,22.15,22.15,0,0,0-3.4-4.5,13.85,13.85,0,0,0-4.7-2.9,17.39,17.39,0,0,0-6.1-1.1,6.85,6.85,0,0,0-2.3,1.2c-1.2.8-2.6,1.7-4.1,2.6a21.71,21.71,0,0,1-5.9,2.6,23.25,23.25,0,0,1-7.3,1.2,20.34,20.34,0,0,1-7.3-1.2,30.33,30.33,0,0,1-5.9-2.6c-1.5-1-2.9-1.9-4.1-2.6a6.85,6.85,0,0,0-2.3-1.2,17,17,0,0,0-6.1,1.1,13.85,13.85,0,0,0-4.7,2.9,17.73,17.73,0,0,0-3.4,4.5,26.14,26.14,0,0,0-2.4,5.4,43.79,43.79,0,0,0-1.5,6c-.4,2.1-.6,4.1-.8,6s-.2,3.7-.2,5.7c0,4.4,1.3,7.9,4,10.4S71.5,150,76,150h48a15.21,15.21,0,0,0,10.7-3.8c2.7-2.5,4-6,4-10.4C138.7,133.8,138.6,131.9,138.5,130.1Zm-20.6-2.5a3.18,3.18,0,0,1-1.5,2.2,32.12,32.12,0,0,1-3,2.1.85.85,0,0,1-.7,0,12.49,12.49,0,0,1-3.7-2.8,3.41,3.41,0,0,1-.9-2.4v-4.5h9.8C117.9,124.1,118,125.9,117.9,127.6Z" transform="translate(-61.3 -49.97)"/></svg>' )
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
            'insert_into_item'      => __( 'Insert into item', 'bp-toolkit' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'bp-toolkit' ),
            'items_list'            => __( 'Items list', 'bp-toolkit' ),
            'items_list_navigation' => __( 'Items list navigation', 'bp-toolkit' ),
            'filter_items_list'     => __( 'Filter items list', 'bp-toolkit' ),
        );
        $args = array(
            'label'                => __( 'Report', 'bp-toolkit' ),
            'description'          => __( '', 'bp-toolkit' ),
            'labels'               => $labels,
            'supports'             => false,
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
            'has_archive'          => false,
            'exclude_from_search'  => true,
            'publicly_queryable'   => true,
            'show_in_rest'         => false,
            'rest_base'            => 'Reports',
            'capability_type'      => 'post',
            'capabilities'         => array(
            ( bptk_fs()->is__premium_only() ? '' : 'create_posts' ) => 'do_not_allow',
        ),
            'map_meta_cap'         => true,
            'register_meta_box_cb' => array( $this, 'Report_meta_box_init' ),
        );
        register_post_type( 'report', $args );
    }
    
    /**
     * Keep our menu highlighted when on report-type taxonomy page.
     *
     * @param		string		$parent_file The parent menu item.
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
        add_meta_box(
            'report-details',
            __( 'Report Form', 'bp-Toolkit' ),
            array( $this, 'report_cpt_meta_box_content' ),
            'report',
            'normal'
        );
    }
    
    /**
     * Render report CPT main meta box content.
     *
     * @since    2.0.0
     *
     * @param WP_Post $post The post object.
     */
    public function report_cpt_meta_box_content( $post )
    {
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'report_cpt-main_meta_box', 'report_cpt-main_meta_box_nonce' );
        require_once plugin_dir_path( __FILE__ ) . 'partials/report-cpt-main-metabox.php';
    }
    
    /**
     * Save a report made by an administrator.
     *
     * @since    2.0.0
     *
     * @param int $post_id The post.
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
        $is_new = $post->post_date === $post->post_modified;
        if ( !$is_new ) {
            return;
        }
        /* OK, it's safe for us to save the data now. */
        // Sanitize the user input.
        
        if ( isset( $_POST['report_cpt-main_meta_box_nonce'] ) ) {
            global  $wpdb ;
            // Update the meta field if admin created.
            
            if ( !empty($_POST['_bptk_member_reported']) ) {
                update_post_meta( $post_id, '_bptk_member_reported', intval( $_POST['_bptk_member_reported'] ) );
            } else {
                if ( '' !== get_post_meta( $post_id, '_bptk_member_reported', true ) ) {
                    delete_post_meta( $post_id, '_bptk_member_reported' );
                }
            }
            
            
            if ( !empty($_POST['_bptk_reported_by']) ) {
                update_post_meta( $post_id, '_bptk_reported_by', intval( $_POST['_bptk_reported_by'] ) );
            } else {
                if ( '' !== get_post_meta( $post_id, '_bptk_reported_by', true ) ) {
                    delete_post_meta( $post_id, '_bptk_reported_by' );
                }
            }
            
            
            if ( !empty($_POST['_bptk_activity_type']) ) {
                update_post_meta( $post_id, '_bptk_activity_type', sanitize_text_field( $_POST['_bptk_activity_type'] ) );
            } else {
                if ( '' !== get_post_meta( $post_id, '_bptk_activity_type', true ) ) {
                    delete_post_meta( $post_id, '_bptk_activity_type' );
                }
            }
            
            $title = 'Admin Created Report';
            $where = array(
                'ID' => $post_id,
            );
            $wpdb->update( $wpdb->posts, array(
                'post_title' => $title,
            ), $where );
        }
    
    }
    
    /**
     * Render our custom header - allows admin notices to render underneath it.
     *
     * @since    2.0.0
     */
    public function add_custom_header()
    {
        
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
            ?></h1></a>
				</div>
				<div class="bptk-meta">
					<span class="bptk-version"><?php 
            echo  'v' . BP_TOOLKIT_VERSION ;
            ?></span>
					<a target="_blank" class="button" href="<?php 
            echo  BP_TOOLKIT_SUPPORT . 'bsr/' ;
            ?>"><?php 
            _e( 'Documentation', 'bp-toolkit' );
            ?></a>
					<a target="_blank" class="button button-primary" href="<?php 
            echo  ( bptk_fs()->is__premium_only() ? bptk_fs()->contact_url() : 'https://wordpress.org/support/plugin/bp-toolkit/' ) ;
            ?>"><?php 
            _e( 'Get Support', 'bp-toolkit' );
            ?></a><h2></h2>
				</div>
			</div>
			<?php 
        } else {
            return;
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
     * Checks if we are on a bp-toolkit page.
     *
     * @since    2.0.0
     *
     * @return		boolean Is this a bp-toolkit page?
     */
    public function is_bptk_page()
    {
        global  $pagenow ;
        global  $post_type ;
        
        if ( 'admin.php' === $pagenow && strpos( $_GET['page'], "bp-toolkit" ) !== false || 'edit-tags.php' === $pagenow && 'report-type' === $_GET['taxonomy'] || 'edit.php' === $pagenow && $post_type === 'report' || 'post-new.php' === $pagenow && $post_type === 'report' ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    /**
     * Fetch latest RSS feed entries.
     *
     * @since    2.0.0
     *
     * @return mixed
     */
    public function latest_news()
    {
        // Get RSS Feed(s)
        include_once ABSPATH . WPINC . '/feed.php';
        // Get a SimplePie feed object from the specified feed source.
        $rss = fetch_feed( 'https://www.therealbenroberts.com/tag/bsr,plugins/feed/ ' );
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
						<?php 
                echo  esc_html( $item->get_date( get_option( 'date_format' ) ) ) ;
                ?>
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
    
    /**
     * Fetch featured RSS feed entries.
     *
     * @since    2.0.2
     *
     * @return mixed
     */
    public function featured_news()
    {
        // Get RSS Feed(s)
        include_once ABSPATH . WPINC . '/feed.php';
        // Get a SimplePie feed object from the specified feed source.
        $rss = fetch_feed( 'https://www.therealbenroberts.com/tag/bsr-featured/feed/ ' );
        $maxitems = 0;
        
        if ( !is_wp_error( $rss ) ) {
            // Checks that the object is created correctly
            // Figure out how many total items there are, but limit it to 5.
            $maxitems = $rss->get_item_quantity( 1 );
            // Build an array of all the items, starting with element 0 (first element).
            $rss_items = $rss->get_items( 0, $maxitems );
        }
        
        
        if ( $maxitems == 0 ) {
            return;
        } else {
            foreach ( $rss_items as $item ) {
                $styles = ( substr( $item->get_description(), 0, 2 ) == substr( wp_strip_all_tags( $item->get_content() ), 0, 2 ) ? '' : wp_kses_post( $item->get_description() ) );
                ?>
				<div class="bptk-featured-news bptk-box" style="<?php 
                echo  $styles ;
                ?>">
					<div class="bptk-box-inner">
						<span class="bptk-featured-news-title"><?php 
                echo  wp_kses_post( $item->get_title() ) ;
                ?></span><span class="bptk-featured-news-content"><?php 
                echo  wp_kses_post( $item->get_content() ) ;
                ?></span>
					</div>
				</div>
			<?php 
            }
        }
    
    }
    
    /**wp_kses_post($item->get_description())
     * Render non-CPT pages.
     *
     * @since    2.0.0
     *
     * @return mixed
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
            require_once plugin_dir_path( __FILE__ ) . 'partials/report.php';
        }
        
        echo  '</div>' ;
    }
    
    /**
     * Render the metabox that shows the different report types.
     *
     * @since    2.0.0
     *
     * @param WP_Post $post Post object.
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
     * Build our report post type table.
     *
     * @since    2.0.0
     *
     * @param 		$columns The columns passed to us from the filter.
     */
    public function set_report_columns( $columns )
    {
        $columns = array(
            'cb'          => '<input type="checkbox" />',
            'title'       => __( 'Report Summary', 'bp-toolkit' ),
            'content'     => __( 'Details', 'bp-toolkit' ),
            'type'        => __( 'Report Type', 'bp-toolkit' ),
            'reported_by' => __( 'Reported By', 'bp-toolkit' ),
            'date'        => __( 'Date', 'bp-toolkit' ),
        );
        return $columns;
    }
    
    /**
     * Set our report post type table custom columns.
     *
     * @since    2.0.0
     *
     * @param 		$column_name The custom column passed to us from the filter.
     */
    public function add_report_columns( $column_name, $post_id )
    {
        switch ( $column_name ) {
            case 'content':
                echo  get_the_excerpt( $post_id ) ;
                break;
            case 'type':
                echo  strip_tags( get_the_term_list(
                    $post_id,
                    'report-type',
                    '',
                    ', '
                ) ) ;
                break;
            case 'reported_by':
                $user = get_user_by( 'ID', get_post_meta( $post_id, '_bptk_reported_by', true ) );
                echo  $user->display_name ;
                break;
        }
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
     * Set up the Report settings section and fields.
     *
     * @since    1.0.1
     */
    public function create_report_settings()
    {
        add_settings_section(
            'report_section',
            '',
            '',
            'report_section'
        );
        // Add fields below
        add_settings_field(
            'bptk_report_activity_types',
            // Our field ID
            '',
            // Our field title
            array( $this, 'report_activity_types_cb' ),
            // Our field callback
            'report_section',
            // The page our field is going on
            'report_section'
        );
        add_settings_field(
            'bptk_report_toggle_emails',
            // Our field ID
            '',
            // Our field title
            array( $this, 'report_toggle_emails_cb' ),
            // Our field callback
            'report_section',
            // The page our field is going on
            'report_section'
        );
        add_settings_field(
            'bptk_report_emails',
            // Our field ID
            '',
            // Our field title
            array( $this, 'report_emails_recipients_cb' ),
            // Our field callback
            'report_section',
            // The page our field is going on
            'report_section'
        );
        register_setting( 'report_section', 'report_section', array( $this, 'bptk_validate' ) );
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
     * Render the report email toggle input
     *
     * @since 2.0
     */
    public function report_toggle_emails_cb()
    {
        $options = get_option( 'report_section' );
        $arg = ( !isset( $options['bptk_report_toggle_emails'] ) ? '0' : '1' );
        ?>

		<fieldset class="bptk-field-wrap" style="border: none;">
			<span class="bptk-field-label"><?php 
        _e( 'Enable Report Notifications', 'bp-toolkit' );
        ?></span>
			<legend class="screen-reader-text"><?php 
        _e( 'Enable Report Notifications', 'bp-toolkit' );
        ?></legend>
			<input type="checkbox" id="bptk_report_toggle_emails" name="report_section[bptk_report_toggle_emails]" value="1" <?php 
        checked( $arg, 1 );
        ?> />
			<span class="bptk-field-description"><?php 
        _e( 'Sends an email when a new report is received.', 'bp-toolkit' );
        ?></span>
		</fieldset>

		<?php 
    }
    
    /**
     * Render the Report recipient email input
     *
     * @since 1.0.1
     */
    public function report_emails_recipients_cb()
    {
        $options = get_option( 'report_section' );
        $arg = ( isset( $options['bptk_report_emails'] ) ? $options['bptk_report_emails'] : '' );
        ?>

		<fieldset class="bptk-field-wrap">
			<span class="bptk-field-label"><?php 
        _e( 'Report Notification Recipients', 'bp-toolkit' );
        ?></span>
			<legend class="screen-reader-text"><?php 
        _e( 'Report Notification Recipients', 'bp-toolkit' );
        ?></legend>
			<input type="email" multiple id="bptk_report_emails" class="bptk-text_medium" name="report_section[bptk_report_emails]" placeholder="one@example.com,two@example.com" size="40" value="<?php 
        echo  $arg ;
        ?>">
			<span class="bptk-field-description"><?php 
        _e( 'Add each email that you want to send the notification to, separated by a comma. Or, leave blank to use the default administrator email.', 'bp-toolkit' );
        ?></span>
		</fieldset>

		<?php 
    }
    
    /**
     * Render the report activity types checkboxes.
     *
     * @since 2.0
     */
    public function report_activity_types_cb()
    {
        $options = get_option( 'report_section' );
        $arg = ( isset( $options['bptk_report_activity_types'] ) ? (array) $options['bptk_report_activity_types'] : [] );
        ?>

		<fieldset class="bptk-field-wrap">
			<span class="bptk-field-label"><?php 
        _e( 'Select Reportable Content', 'bp-toolkit' );
        ?></span>
			<legend class="screen-reader-text"><?php 
        _e( 'Select Reportable Content', 'bp-toolkit' );
        ?></legend>
			<ul class="bptk-radios">
				<li><label><input type="checkbox" id="bptk_report_activity_types" name="report_section[bptk_report_activity_types][]" value="members" <?php 
        checked( in_array( 'members', $arg ), 1 );
        ?> /><?php 
        _e( ' Members', 'bp-toolkit' );
        ?>  </label></li>
				<?php 
        ?>
			</ul> <?php 
        echo  '<span class="bptk-field-description">' . sprintf( __( 'In the free edition, only members themselves can be reported. Please <a href="%s">upgrade now</a> to the Pro Edition to report specific activities, including comments, private messages, activity updates and groups.', 'bp-toolkit' ), esc_url( bptk_fs()->get_upgrade_url() ) ) . '</span>' ;
        ?>
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
     * Strip tags from string or array
     *
     * @since 2.0
     *
     * @param  mixed  array or string to strip
     * @return mixed  stripped value
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
     * Add rating links to the admin dashboard
     *
     * @since  	2.0.0
     *
     * @param string		$footer_text The existing footer text
     * @return 	string
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

}