<?php
/**
 * Class: ECS_Admin
 *
 * This controller class is used to build the admin page
 * output. It includes the necessary views contained in 
 * the views/admin-page directory.
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
if ( ! class_exists( 'ECS_Admin' ) ) :
	class ECS_Admin {
		/**
		 * Instance of this class.
		 * 
		 * @var      object
		 * @since    1.0
		 *
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 * 
		 * @var      string
		 * @since    1.0
		 *
		 */
		protected $plugin_screen_hook_suffix = 'easy-custom-sidebars';

		/**
		 * Constructor Function
		 * 
		 * Initialize the class and register all
		 * actions and filters.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		function __construct() {

			$this->plugin_slug = 'easy-custom-sidebars';

			// Bail if the current user doesn't 
			// have the admin capabilities.
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return;
			}

			$this->register_actions();		
			$this->register_filters();
		}

		/**
		 * Return an instance of this class.
		 * 
		 * @return    object    A single instance of this class.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * 
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function register_actions() {
			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			// Add the options page and menu item.
			add_action( 'admin_head', array( $this, 'admin_head_styles' ) );
			add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		}
		
		/**
		 * Register Custom Filters
		 *
		 * Add any custom filters in this function.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function register_filters() {
			// Add an action link pointing to the options page.
			$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
			add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		}

		/**
		 * Enqueue Admin Styles
		 * 
		 * Register and enqueue admin-specific stylesheets.
		 *
		 * @return    null    Return early if no settings page is registered.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function enqueue_admin_styles() {
			
			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();

			// Load styles only on the admin page
			if ( $this->plugin_screen_hook_suffix == $screen->id ) {

				wp_enqueue_style( 'wp-color-picker' );

				wp_deregister_style( $this->plugin_slug .'-admin-styles' );
				wp_register_style( 
					$this->plugin_slug .'-admin-styles', 
					Easy_Custom_Sidebars::get_css_url() . '/admin/admin.css', 
					array(), 
					Easy_Custom_Sidebars::VERSION
				);
				wp_enqueue_style( $this->plugin_slug .'-admin-styles' );
			}
		}

		/**
		 * Enqueue Admin Scripts
		 * 
		 * Register and enqueue admin-specific JavaScript.
		 *
		 * @return    null    Return early if no settings page is registered.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function enqueue_admin_scripts() {
			
			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();

			// Load styles only on the admin page
			if ( $this->plugin_screen_hook_suffix == $screen->id ) {
				
				// Load jQuery and jQuery UI
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'utils' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-effects-core' );
				wp_enqueue_script( 'jquery-effects-fade' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'jquery-ui-autocomplete' );
				wp_enqueue_script( 'jquery-ui-position' );
				wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				wp_enqueue_script( 'jquery-ui-draggable' );
				wp_enqueue_script( 'jquery-ui-droppable' );

				// Load PostBox
				wp_enqueue_script( 'postbox' );
				
				if ( wp_is_mobile() ) {
					wp_enqueue_script( 'jquery-touch-punch' );
				}

				// Load admin page js
				wp_deregister_script( $this->plugin_slug . '-admin-script' );
				wp_register_script( 
					$this->plugin_slug . '-admin-script', 
					Easy_Custom_Sidebars::get_js_url() . '/admin/admin.js',  
					array( 'jquery','jquery-ui-core', 'jquery-ui-widget' ), 
					Easy_Custom_Sidebars::VERSION 
				);
				wp_enqueue_script( $this->plugin_slug . '-admin-script' );	

				// Load metabox accordion plugin
				wp_deregister_script( $this->plugin_slug . '-accordion-sidebar' );
				wp_register_script( 
					$this->plugin_slug . '-accordion-sidebar', 
					Easy_Custom_Sidebars::get_js_url() . '/admin/metabox-accordion.js',  
					array( 'jquery' ), 
					Easy_Custom_Sidebars::VERSION 
				);
				wp_enqueue_script( $this->plugin_slug . '-accordion-sidebar' );				
				
				// Load in js l10n for javascript translations
				wp_localize_script( $this->plugin_slug . '-admin-script', 'sidebarsL10n', $this->getL10n() );

			}
		}

		/**
		 * Get L10n Translation Object
		 *
		 * This array is enqueues as a javascript object on
		 * the admin page. This allows the plugin to remain
		 * fully translatable.
		 * 
		 * @return array $l10n - Array of strings to be used as a js translation object
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function getL10n() {
			$l10n = array(
				'activateSidebar'            => '&mdash; ' . __( 'Select a Sidebar', 'easy-custom-sidebars' ) . ' &mdash;',
				'addButtonText'              => __( 'Add to Sidebar', 'easy-custom-sidebars' ),
				'ajax_url'                   => admin_url( 'admin-ajax.php' ),
				'confirmation'               => __( 'This page is asking you to confirm that you want to leave - data you have entered may not be saved.', 'easy-custom-sidebars' ),
				'deleteAllWarning'           => __( "Warning! You are about to permanently delete all sidebars. 'Cancel' to stop, 'OK' to delete.", 'easy-custom-sidebars' ),
				'deleteWarning'              => __( "You are about to permanently delete this sidebar. 'Cancel' to stop, 'OK' to delete.", 'easy-custom-sidebars' ),
				'deactivateSidebar'          => '&mdash; ' . __( 'Deactivate Sidebar', 'easy-custom-sidebars' ) . '&mdash; ',
				'leavePage'                  => __( 'Leave Page', 'easy-custom-sidebars' ) ,
				'stayOnPage'                 => __( 'Stay on Page', 'easy-custom-sidebars' ) ,
				'noResultsFound'             => __( 'No Results Found.', 'easy-custom-sidebars' ),
				'oneThemeLocationNoSidebars' => __( 'No Sidebars', 'easy-custom-sidebars')
			);
			return $l10n;
		}
		
		/**
		 * Edit Admin Styles
		 *
		 * Used to show/hide certain options in the admin
		 * area, and change the appearence of certain ui
		 * elements globally.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function admin_head_styles() {
			?>
			<style type="text/css">
			</style>
			<?php
		}

		/**
		 * Add Admin Menu 
		 * 
		 * Register the administration menu for this plugin 
		 * into the WordPress Dashboard menu.
		 *
		 * @link http://codex.wordpress.org/Administration_Menus	Administration Menus
		 * @link http://codex.wordpress.org/Roles_and_Capabilities 	Roles and Capabilities
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function add_plugin_admin_menu() {

			/**
			 * Add a settings page for this plugin to the Settings menu.
			 *
			 * NOTE: Alternative menu locations are available via WordPress 
			 * administration menu functions.
			 *
			 * {@link http://codex.wordpress.org/Administration_Menus} 		Administration Menus
			 * {@link http://codex.wordpress.org/Roles_and_Capabilities}	Roles and Capabilities
			 *    
			 */
			$this->plugin_screen_hook_suffix = add_theme_page(
				__( 'Theme Sidebars', 'easy-custom-sidebars' ),
				__( 'Theme Sidebars', 'easy-custom-sidebars' ),
				'edit_theme_options',
				$this->plugin_slug,
				array( $this, 'display_plugin_admin_page' )
			);


			/**
			 * Set up the custom sidebar metaboxes. Requires
			 * WordPress Nav Menu functionality.
			 * 
			 */
			$this->setup_metaboxes();

			/**
			 * Use the retrieved $this->plugin_screen_hook_suffix to hook the function that enqueues our 
			 * contextual help tabs. This hook invokes the function only on our plugin administration screen,
			 * see: http://codex.wordpress.org/Administration_Menus#Page_Hook_Suffix
			 */
			add_action( 'load-' . $this->plugin_screen_hook_suffix, array( $this, 'add_help_tabs' ) );
			add_action( 'load-' . $this->plugin_screen_hook_suffix, array( $this, 'add_screen_option' ) );
		}


		public function display_plugin_admin_page() {
			$controller = ECS_Admin_Controller::get_instance();
			$controller->render();
		}

		/**
		 * Get Screen Tab Options
		 *
		 * This function has been created in order to give developers
		 * a hook by which to add their own screen options.
		 *
		 * Custom Actions:
		 *     -  ecs_add_screen_options
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function add_screen_option() {
			
			// Bail if hook not defined
			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();

			if ( $this->plugin_screen_hook_suffix == $screen->id ) {
				// Developers: Add Options Below
				do_action( 'ecs_add_screen_options' );
			}
		}

		/**
		 * Add Help Tabs To The Admin Page
		 *
		 * Adds contextual help tabs to the custom themes sidebar page.
		 * This function is attached to an action that ensures that the
		 * help tabs are only displayed on the custom sidebar page.
		 *
		 * @link    http://codex.wordpress.org/Function_Reference/get_current_screen      get_current_screen()
		 * @link    http://codex.wordpress.org/Function_Reference/add_help_tab            add_help_tab()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function add_help_tabs() {
			
			// Bail if hook not defined
			if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
				return;
			}

			$screen = get_current_screen();

			// Check that this is the easy custom sidebars admin page
			if ( $this->plugin_screen_hook_suffix == $screen->id ) {
				
				// Add overview tab
				$screen->add_help_tab( array(
					'id'      => 'overview',
					'title'   => __( 'Overview', 'easy-custom-sidebars' ),
					'content' => $this->get_overview_tab_content(),
				) );

				/**
				 * Hook into this action to add more tabs to the admin page
				 * or remove any tabs defined above, if you wish to change
				 * the tab content based on the theme that is activated.
				 */
				do_action( 'cps-help-tabs', $screen );

				$screen->set_help_sidebar(
					'<p><strong>' . __( 'For more information:', 'easy-custom-sidebars' ) . '</strong></p>' .
					'<p><a href="http://codex.wordpress.org/Function_Reference/register_sidebar" target="_blank">' . __( 'Documentation on Registering Sidebars', 'easy-custom-sidebars' ) . '</a></p>' .
					'<p><a href="http://wordpress.org/support/" target="_blank">' . __('Support Forums') . '</a></p>'
				);	
			}
		}

		/**
		 * Get Overview Tab Content
		 *
		 * Gets the html contect to be used in the 'Overview'
		 * tab as a string.
		 *
		 * Custom Filters:
		 *     - ecs_overview_tab_content
		 *
		 * 
		 * @return string $content 	- Tab Content
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_overview_tab_content() {

			$content  = '<p>' . __( 'This screen is used for managing your custom sidebars. It provides a way to replace the default widget areas that have been registed with your theme. If your theme does not natively support widget areas you can learn about adding this support by following the documentation link to the side.', 'easy-custom-sidebars' ) . '</p>';
			$content .= '<p>' . __( 'From this screen you can:' ) . '</p>';
			$content .= '<ul><li>' . __( 'Create, edit, and delete custom sidebars', 'easy-custom-sidebars' ) . '</li>';
			$content .= '<li>' . __( 'Choose which widget area you would like to replace', 'easy-custom-sidebars' ) . '</li>';
			$content .= '<li>' . __( 'Add, organize, and modify pages/posts etc that belong to a custom sidebar', 'easy-custom-sidebars' ) . '</li></ul>';

			return apply_filters( 'ecs_overview_tab_content', $content );
		}

		/**
		 * Setup Sidebar Metaboxes
		 * 
		 * Creates a new array item in the global $wp_meta_boxes and
		 * then modify this data so that it is ready for the admin
		 * page.
		 *
		 * @uses global $wp_meta_boxes
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */	
		public function setup_metaboxes() {
			global $wp_meta_boxes;

			$this->setup_post_type_meta_boxes();
			$this->setup_category_posts_boxes();
			$this->setup_taxonomy_meta_boxes();
			$this->setup_author_meta_box();
			$this->setup_template_meta_box();
		}

		/**
		 * Retrieve All Post Type Metaboxes
		 *
		 * Gets all posttypes that are currently registered
		 * with the currently active WordPress theme and 
		 * registers metaboxes for each posttype for use on
		 * the Admin Page.
		 *
		 * Custom Filters:
		 *     - ecs_sidebar_meta_box_object
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post_types 	get_post_types()
		 * @link 	http://codex.wordpress.org/Function_Reference/apply_filters 	apply_filters()
		 * @link 	http://codex.wordpress.org/Function_Reference/add_meta_box 		add_meta_box()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function setup_post_type_meta_boxes() {
			$post_types      = get_post_types( array( 'show_in_nav_menus' => true ), 'object' );
			$admin_page_name = $this->plugin_screen_hook_suffix;

			// Bail if there are no registered posttypes
			if ( ! $post_types ) {
				return;
			}

			// Add metabox for each posttype
			foreach ( $post_types as $post_type ) {
				$post_type = apply_filters( 'ecs_sidebar_meta_box_object', $post_type );
				if ( $post_type ) {
					$id = $post_type->name;
					add_meta_box( 
						"master-add-{$id}", 
						$post_type->labels->name, 
						array( $this, 'render_post_type_meta_box' ), 
						$admin_page_name, 
						'side', 
						'default', 
						$post_type 
					);
				}
			}
		}

		/**
		 * Displays a Metabox for a Post Type Sidebar item.
		 *
		 * This function outputs the sidebar checklist metabox
		 * that is used on the admin page.
		 * 
		 * @link 	http://codex.wordpress.org/Function_Reference/apply_filters 				apply_filters()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post_type_object			get_post_type_object()
		 * @link 	http://codex.wordpress.org/Function_Reference/paginate_links				paginate_links()
		 * @link 	http://codex.wordpress.org/Function_Reference/add_query_arg					add_query_arg()
		 * @link 	http://codex.wordpress.org/Function_Reference/is_post_type_hierarchical		is_post_type_hierarchical()
		 * @link 	http://codex.wordpress.org/Function_Reference/esc_attr						esc_attr()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post						get_post()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_posts						get_posts()
		 * @link 	http://codex.wordpress.org/Function_Reference/submit_button					submit_button()
		 * @link 	http://codex.wordpress.org/Function_Reference/is_wp_error					is_wp_error()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_option					get_option()
		 *
		 * @uses  class ECS_Walker_Checklist
		 * 
		 * @global $_nav_menu_placeholder
		 * @global $nav_menu_selected_id
		 * @param string $object Not used.
		 * @param string $post_type The post type object.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function render_post_type_meta_box( $object, $post_type ) {
			
			global $_nav_menu_placeholder;
			global $nav_menu_selected_id;

			$post_type_name = $post_type['args']->name;

			// paginate browsing for large numbers of post objects
			$per_page = apply_filters( 'ecs_post_type_meta_box_per_page', 50 );
			$pagenum  = isset( $_REQUEST[$post_type_name . '-tab'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
			$offset   = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;

			$args = array(
				'offset'                 => $offset,
				'order'                  => 'ASC',
				'orderby'                => 'title',
				'posts_per_page'         => $per_page,
				'post_type'              => $post_type_name,
				'suppress_filters'       => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false
			);

			if ( isset( $post_type['args']->_default_query ) )
				$args = array_merge($args, (array) $post_type['args']->_default_query );

			// @todo transient caching of these results with proper invalidation on updating of a post of this type
			$get_posts = new WP_Query;
			$posts     = $get_posts->query( $args );

			if ( ! $get_posts->post_count ) {
				echo '<p>' . __( 'No items.', 'easy-custom-sidebars' ) . '</p>';
				return;
			}

			$post_type_object = get_post_type_object( $post_type_name );
			$num_pages        = $get_posts->max_num_pages;

			$page_links = paginate_links( array(
				'base' => add_query_arg(
					array(
						$post_type_name . '-tab' => 'all',
						'paged'                  => '%#%',
						'item-type'              => 'post_type',
						'item-object'            => $post_type_name,
					)
				),
				'format'    => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total'     => $num_pages,
				'current'   => $pagenum
			));

			if ( !$posts )
				$error = '<li id="error">'. $post_type['args']->labels->not_found .'</li>';

			$db_fields = false;
			if ( is_post_type_hierarchical( $post_type_name ) ) {
				$db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );
			}

			$walker = new ECS_Walker_Checklist( $db_fields );

			$current_tab = 'most-recent';
			if ( isset( $_REQUEST[$post_type_name . '-tab'] ) && in_array( $_REQUEST[$post_type_name . '-tab'], array('all', 'search') ) ) {
				$current_tab = $_REQUEST[$post_type_name . '-tab'];
			}

			if ( ! empty( $_REQUEST['quick-search-posttype-' . $post_type_name] ) ) {
				$current_tab = 'search';
			}

			$removed_args = array(
				'action',
				'customlink-tab',
				'edit-menu-item',
				'menu-item',
				'page-tab',
				'_wpnonce',
			);

			?>
			<div id="posttype-<?php echo $post_type_name; ?>" class="posttypediv">
				<ul id="posttype-<?php echo $post_type_name; ?>-tabs" class="posttype-tabs add-menu-item-tabs">
					<li <?php echo ( 'most-recent' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($post_type_name . '-tab', 'most-recent', remove_query_arg($removed_args))); ?>#tabs-panel-posttype-<?php echo $post_type_name; ?>-most-recent"><?php _e('Most Recent'); ?></a></li>
					<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($post_type_name . '-tab', 'all', remove_query_arg($removed_args))); ?>#<?php echo $post_type_name; ?>-all"><?php _e('View All'); ?></a></li>
					<li <?php echo ( 'search' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($post_type_name . '-tab', 'search', remove_query_arg($removed_args))); ?>#tabs-panel-posttype-<?php echo $post_type_name; ?>-search"><?php _e('Search'); ?></a></li>
				</ul>

				<div id="tabs-panel-posttype-<?php echo $post_type_name; ?>-most-recent" class="tabs-panel <?php
					echo ( 'most-recent' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>">
					<ul id="<?php echo $post_type_name; ?>checklist-most-recent" class="categorychecklist form-no-clear">
						
						<!-- All Post Type Checkbox -->
						<li>
							<label class="menu-item-title">
								<input type="checkbox" value="1" name="menu-item[-1111][menu-item-object-id]" class="menu-item-checkbox">
								<strong><?php echo sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type_object->labels->name ); ?></strong>
							</label>
							<input class="menu-item-db-id" type="hidden" value="0" name="menu-item[-1111][menu-item-db-id]">
							<input class="menu-item-object" type="hidden" value="<?php echo $post_type_object->name; ?>" name="menu-item[-1111][menu-item-object]">
							<input class="menu-item-parent-id" type="hidden" value="0" name="menu-item[-1111][menu-item-parent-id]">
							<input class="menu-item-type" type="hidden" value="post_type_all" name="menu-item[-1111][menu-item-type]">
							<input class="menu-item-title" type="hidden" value="<?php echo sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type_object->labels->name ); ?>" name="menu-item[-1111][menu-item-title]">
						</li>

						<!-- Posttype Archive Checkbox -->
						<?php if ( 'post' != $post_type_object->name && 'page' != $post_type_object->name ) : ?>
							<li>
								<label class="menu-item-title">
									<input type="checkbox" value="1" name="menu-item[-1112][menu-item-object-id]" class="menu-item-checkbox">
									<strong><?php echo sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_object->labels->singular_name ); ?></strong>

								</label>
								<input class="menu-item-db-id" type="hidden" value="0" name="menu-item[-1112][menu-item-db-id]">
								<input class="menu-item-object" type="hidden" value="<?php echo $post_type_object->name; ?>" name="menu-item[-1112][menu-item-object]">
								<input class="menu-item-parent-id" type="hidden" value="0" name="menu-item[-1112][menu-item-parent-id]">
								<input class="menu-item-type" type="hidden" value="post_type_archive" name="menu-item[-1112][menu-item-type]">
								<input class="menu-item-title" type="hidden" value="<?php echo sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_object->labels->singular_name ); ?>" name="menu-item[-1112][menu-item-title]">					
							</li>
						<?php endif; ?>

						<?php
						$recent_args = array_merge( $args, array( 'orderby' => 'post_date', 'order' => 'DESC', 'posts_per_page' => 15 ) );
						$most_recent = $get_posts->query( $recent_args );
						$args['walker'] = $walker;
						echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $most_recent), 0, (object) $args );
						?>
					</ul>
				</div><!-- /.tabs-panel -->

				<div class="tabs-panel <?php
					echo ( 'search' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>" id="tabs-panel-posttype-<?php echo $post_type_name; ?>-search">
					<?php
					if ( isset( $_REQUEST['quick-search-posttype-' . $post_type_name] ) ) {
						$searched = esc_attr( $_REQUEST['quick-search-posttype-' . $post_type_name] );
						$search_results = get_posts( array( 's' => $searched, 'post_type' => $post_type_name, 'fields' => 'all', 'order' => 'DESC', ) );
					} else {
						$searched = '';
						$search_results = array();
					}
					?>
					<p class="quick-search-wrap">
						<input type="search" class="quick-search" title="<?php esc_attr_e('Search'); ?>" value="<?php echo $searched; ?>" name="quick-search-posttype-<?php echo $post_type_name; ?>" placeholder="<?php _e( 'Search', 'easy-custom-sidebars' ) ?>" />
						<span class="spinner"></span>
						<?php submit_button( __( 'Search' ), 'button-small quick-search-submit button-secondary hide-if-js', 'submit', false, array( 'id' => 'submit-quick-search-posttype-' . $post_type_name ) ); ?>
					</p>

					<ul id="<?php echo $post_type_name; ?>-search-checklist" data-wp-lists="list:<?php echo $post_type_name?>" class="categorychecklist form-no-clear">
					<?php if ( ! empty( $search_results ) && ! is_wp_error( $search_results ) ) : ?>
						<?php
						$args['walker'] = $walker;
						echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $search_results), 0, (object) $args );
						?>
					<?php elseif ( is_wp_error( $search_results ) ) : ?>
						<li><?php echo $search_results->get_error_message(); ?></li>
					<?php elseif ( ! empty( $searched ) ) : ?>
						<li><?php _e('No results found.'); ?></li>
					<?php endif; ?>
					</ul>
				</div><!-- /.tabs-panel -->

				<div id="<?php echo $post_type_name; ?>-all" class="tabs-panel tabs-panel-view-all <?php
					echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>">
					<?php if ( ! empty( $page_links ) ) : ?>
						<div class="add-menu-item-pagelinks">
							<?php echo $page_links; ?>
						</div>
					<?php endif; ?>
					<ul id="<?php echo $post_type_name; ?>checklist" data-wp-lists="list:<?php echo $post_type_name?>" class="categorychecklist form-no-clear">
						
						<!-- All Post Type Checkbox -->
						<li>
							<label class="menu-item-title">
								<input type="checkbox" value="1" name="menu-item[-1111][menu-item-object-id]" class="menu-item-checkbox">
								<strong><?php echo sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type_object->labels->name ); ?></strong>
							</label>
							<input class="menu-item-db-id" type="hidden" value="0" name="menu-item[-1111][menu-item-db-id]">
							<input class="menu-item-object" type="hidden" value="<?php echo $post_type_object->name; ?>" name="menu-item[-1111][menu-item-object]">
							<input class="menu-item-parent-id" type="hidden" value="0" name="menu-item[-1111][menu-item-parent-id]">
							<input class="menu-item-type" type="hidden" value="post_type_all" name="menu-item[-1111][menu-item-type]">
							<input class="menu-item-title" type="hidden" value="<?php echo sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type_object->labels->name ); ?>" name="menu-item[-1111][menu-item-title]">
						</li>

						<!-- Posttype Archive Checkbox -->
						<?php if ( 'post' != $post_type_object->name && 'page' != $post_type_object->name ) : ?>
							<li>
								<label class="menu-item-title">
									<input type="checkbox" value="1" name="menu-item[-1112][menu-item-object-id]" class="menu-item-checkbox">
									<strong><?php echo sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_object->labels->singular_name ); ?></strong>

								</label>
								<input class="menu-item-db-id" type="hidden" value="0" name="menu-item[-1112][menu-item-db-id]">
								<input class="menu-item-object" type="hidden" value="<?php echo $post_type_object->name; ?>" name="menu-item[-1112][menu-item-object]">
								<input class="menu-item-parent-id" type="hidden" value="0" name="menu-item[-1112][menu-item-parent-id]">
								<input class="menu-item-type" type="hidden" value="post_type_archive" name="menu-item[-1112][menu-item-type]">
								<input class="menu-item-title" type="hidden" value="<?php echo sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_object->labels->singular_name ); ?>" name="menu-item[-1112][menu-item-title]">					
							</li>
						<?php endif; ?>
						
						<?php
						$args['walker'] = $walker;

						// if we're dealing with pages, let's put a checkbox for the front page at the top of the list
						if ( 'page' == $post_type_name ) {
							$front_page = 'page' == get_option('show_on_front') ? (int) get_option( 'page_on_front' ) : 0;
							if ( ! empty( $front_page ) ) {
								$front_page_obj = get_post( $front_page );
								$front_page_obj->front_or_home = true;
								array_unshift( $posts, $front_page_obj );
							} else {
								$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
								array_unshift( $posts, (object) array(
									'front_or_home' => true,
									'ID'            => 0,
									'object_id'     => $_nav_menu_placeholder,
									'post_content'  => '',
									'post_excerpt'  => '',
									'post_parent'   => '',
									'post_title'    => _x('Home', 'nav menu home label'),
									'post_type'     => 'nav_menu_item',
									'type'          => 'custom',
									'url'           => home_url('/'),
								) );
							}
						}

						$posts = apply_filters( 'ecs_sidebar_items_'.$post_type_name, $posts, $args, $post_type );
						$checkbox_items = walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $posts), 0, (object) $args );

						if ( 'all' == $current_tab && ! empty( $_REQUEST['selectall'] ) ) {
							$checkbox_items = preg_replace('/(type=(.)checkbox(\2))/', '$1 checked=$2checked$2', $checkbox_items);

						}

						echo $checkbox_items;
						?>
					</ul>
					<?php if ( ! empty( $page_links ) ) : ?>
						<div class="add-menu-item-pagelinks">
							<?php echo $page_links; ?>
						</div>
					<?php endif; ?>
				</div><!-- /.tabs-panel -->

				<p class="button-controls">
					<span class="list-controls">
						<a href="<?php
							echo esc_url(add_query_arg(
								array(
									$post_type_name . '-tab' => 'all',
									'selectall' => 1,
								),
								remove_query_arg($removed_args)
							));
						?>#posttype-<?php echo $post_type_name; ?>" class="select-all"><?php _e( 'Select All', 'easy-custom-sidebars' ); ?></a>
					</span>

					<span class="add-to-menu">
						<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Sidebar', 'easy-custom-sidebars' ); ?>" name="add-post-type-menu-item" id="submit-posttype-<?php echo $post_type_name; ?>" />
						<span class="spinner"></span>
					</span>
				</p>
			</div><!-- /.posttypediv -->
			<?php
		}

		/**
		 * Register Category Posts Metabox
		 *
		 * Registers the custom metabox that is added in order
		 * to cater for the posts with category feature.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/add_meta_box 		add_meta_box()
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function setup_category_posts_boxes() {
			add_meta_box(
				'master-add-category-posts-metabox',
				__( 'All Posts In Category', 'easy-custom-sidebars' ),
				array( $this, 'render_category_post_meta_box' ),
				$this->plugin_screen_hook_suffix,
				'side',
				'default'
			);
		}

		/**
		 * Display Category Posts Metabox
		 *
		 * This function contains the output for the category 
		 * posts metabox on the admin page of this plugin.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/get_terms 						get_terms()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_taxonomy						get_taxonomy()
		 * @link 	http://codex.wordpress.org/Function_Reference/is_wp_error						is_wp_error()
		 * @link 	http://codex.wordpress.org/Function_Reference/wp_count_terms					wp_count_terms()
		 * @link 	http://codex.wordpress.org/Function_Reference/esc_url							esc_url()
		 * @link 	http://codex.wordpress.org/Function_Reference/esc_attr_e						esc_attr_e()
		 * @link 	http://codex.wordpress.org/Function_Reference/admin_url							admin_url()
		 * @link 	http://codex.wordpress.org/Function_Reference/add_query_arg						add_query_arg()
		 * @link 	http://codex.wordpress.org/Function_Reference/paginate_links					paginate_links()
		 * @link 	http://codex.wordpress.org/Function_Reference/is_taxonomy_hierarchical			is_taxonomy_hierarchical()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function render_category_post_meta_box() {
			global $nav_menu_selected_id;
			$taxonomy_name = 'category';

			// Paginate browsing for large numbers of objects
			$per_page = apply_filters( 'ecs_category_post_meta_box_per_page', 50 );

			// Check if any variables have been passed in the URL
			$pagenum  = isset( $_REQUEST['custom-item-type'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
			$offset   = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;

			// Define query args for pagination items 
			$args = array(
				'child_of'     => 0,
				'exclude'      => '',
				'hide_empty'   => false,
				'hierarchical' => 1,
				'include'      => '',
				'number'       => $per_page,
				'offset'       => $offset,
				'order'        => 'ASC',
				'orderby'      => 'name',
				'pad_counts'   => false,
			);

			// Get taxonomy terms and object
			$terms        = get_terms( $taxonomy_name, $args );
			$taxonomy_obj = get_taxonomy( $taxonomy_name );

			// Display feedback message if there are no categories
			if ( ! $terms || is_wp_error($terms) ) {
				echo '<p>' . __( 'No items.', 'easy-custom-sidebars' ) . '</p>';
				return;
			}

			// Determine number of pages
			$num_pages = ceil( wp_count_terms( $taxonomy_name , array_merge( $args, array('number' => '', 'offset' => '') ) ) / $per_page );

			// Define admin page url
			$admin_url  = esc_url( 
							add_query_arg( 
								array( 
									'page' => 'easy-custom-sidebars' 
								), 
								admin_url( 'themes.php' ) 
							) 
						);

			// Generate pagination
			$page_links = paginate_links( array(
				'base' => add_query_arg(
					array(
						$taxonomy_name . '-tab' => 'all',
						'paged'                 => '%#%',
						'item-type'             => 'taxonomy',
						'item-object'           => $taxonomy_name,
						'custom-item-type'		=> 'category_posts',
					), $admin_url
				),
				'format'    => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total'     => $num_pages,
				'current'   => $pagenum
			));

			$db_fields = false;
			if ( is_taxonomy_hierarchical( $taxonomy_name ) ) {
				$db_fields = array( 
					'parent' => 'parent', 
					'id'     => 'term_id', 
				);
			}

			// Define our own custom walker
			$walker = new ECS_Walker_Checklist( $db_fields );

			// Determine the current tab to use
			$current_tab = 'most-used';
			if ( isset( $_REQUEST[$taxonomy_name . '-tab'] ) && in_array( $_REQUEST[$taxonomy_name . '-tab'], array('all', 'most-used', 'search') ) ) {
				$current_tab = $_REQUEST[$taxonomy_name . '-tab'];
			}

			if ( ! empty( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] ) ) {
				$current_tab = 'search';
			}

			$removed_args = array(
				'action',
				'customlink-tab',
				'edit-menu-item',
				'menu-item',
				'page-tab',
				'_wpnonce',
			);
			?>
			<div id="taxonomy-<?php echo $taxonomy_name; ?>-custom-category" class="taxonomydiv">
				
				<!-- Tab Panel Tabs -->
				<ul id="taxonomy-<?php echo $taxonomy_name; ?>-tabs-custom-category" class="taxonomy-tabs add-menu-item-tabs">
					<li <?php echo ( 'most-used' == $current_tab ? ' class="tabs"' : '' ); ?>>
						<a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($taxonomy_name . '-tab', 'most-used', remove_query_arg($removed_args))); ?>#tabs-panel-<?php echo $taxonomy_name; ?>-pop-custom-category">
							<?php _e('Most Used'); ?>
						</a>
					</li>
					<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>>
						<a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($taxonomy_name . '-tab', 'all', remove_query_arg($removed_args))); ?>#tabs-panel-<?php echo $taxonomy_name; ?>-all-custom-category">
							<?php _e('View All'); ?>
						</a>
					</li>
					<li <?php echo ( 'search' == $current_tab ? ' class="tabs"' : '' ); ?>>
						<a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($taxonomy_name . '-tab', 'search', remove_query_arg($removed_args))); ?>#tabs-panel-search-taxonomy-<?php echo $taxonomy_name; ?>-custom-category">
							<?php _e('Search'); ?>
						</a>
					</li>
				</ul>

				<!-- Tab Panels -->
				<div id="tabs-panel-<?php echo $taxonomy_name; ?>-pop-custom-category" class="tabs-panel <?php
					echo ( 'most-used' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>">
					<ul id="<?php echo $taxonomy_name; ?>checklist-pop" class="categorychecklist form-no-clear" >
						<?php
							
							// Get popular terms
							$popular_terms = get_terms( 
												$taxonomy_name, 
												array( 
													'orderby'      => 'count', 
													'order'        => 'DESC', 
													'number'       => 10, 
													'hierarchical' => false,
												)
											);
							
							// Use the custom walker
							$args['walker'] = $walker;

							// Set custom array index to indicate a custom data type
							$args['custom_item_type'] = 'category_posts';

							// Output menu markup
							echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $popular_terms), 0, (object) $args );
						?>
					</ul>
				</div><!-- /.tabs-panel -->

				<div id="tabs-panel-<?php echo $taxonomy_name; ?>-all-custom-category" class="tabs-panel tabs-panel-view-all <?php
					echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>">
					<?php if ( ! empty( $page_links ) ) : ?>
						<div class="add-menu-item-pagelinks">
							<?php echo $page_links; ?>
						</div>
					<?php endif; ?>
					<ul id="<?php echo $taxonomy_name; ?>checklist" data-wp-lists="list:<?php echo $taxonomy_name?>" class="categorychecklist form-no-clear">
						<?php
							// Use the custom walker
							$args['walker'] = $walker;

							// Set custom array index to indicate a custom data type
							$args['custom_item_type'] = 'category_posts';

							echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $terms), 0, (object) $args );
						?>
					</ul>
					<?php if ( ! empty( $page_links ) ) : ?>
						<div class="add-menu-item-pagelinks">
							<?php echo $page_links; ?>
						</div>
					<?php endif; ?>
				</div><!-- /.tabs-panel -->

				<div class="tabs-panel <?php
					echo ( 'search' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>" id="tabs-panel-search-taxonomy-<?php echo $taxonomy_name; ?>-custom-category">
					<?php
					if ( isset( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] ) ) {
						$searched = esc_attr( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] );
						$search_results = get_terms( $taxonomy_name, array( 'name__like' => $searched, 'fields' => 'all', 'orderby' => 'count', 'order' => 'DESC', 'hierarchical' => false ) );
					} else {
						$searched = '';
						$search_results = array();
					}
					?>
					<p class="quick-search-wrap">
						<input type="search" class="quick-search" title="<?php esc_attr_e('Search'); ?>" value="<?php echo $searched; ?>" name="quick-search-taxonomy-<?php echo $taxonomy_name; ?>-custom-category" placeholder="<?php _e( 'Search', 'easy-custom-sidebars' ) ?>" />
						<span class="spinner"></span>
						<?php submit_button( __( 'Search' ), 'button-small quick-search-submit button-secondary hide-if-js', 'submit', false, array( 'id' => 'submit-quick-search-taxonomy-' . $taxonomy_name ) ); ?>
					</p>

					<ul id="<?php echo $taxonomy_name; ?>-search-checklist" data-wp-lists="list:<?php echo $taxonomy_name?>" class="categorychecklist form-no-clear">
					<?php if ( ! empty( $search_results ) && ! is_wp_error( $search_results ) ) : ?>
						<?php
							// Use the custom walker
							$args['walker'] = $walker;

							// Set custom array index to indicate a custom data type
							$args['custom_item_type'] = 'category_posts';
							
							echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $search_results), 0, (object) $args );
						?>
					<?php elseif ( is_wp_error( $search_results ) ) : ?>
						<li><?php echo $search_results->get_error_message(); ?></li>
					<?php elseif ( ! empty( $searched ) ) : ?>
						<li><?php _e('No results found.'); ?></li>
					<?php endif; ?>
					</ul>
				</div><!-- /.tabs-panel -->

				<p class="button-controls">
					<span class="list-controls">
						<a href="<?php
							echo esc_url(add_query_arg(
								array(
									$taxonomy_name . '-tab' => 'all',
									'selectall' => 1,
								),
								remove_query_arg($removed_args)
							));
						?>#taxonomy-<?php echo $taxonomy_name; ?>-custom-category" class="select-all"><?php _e( 'Select All', 'easy-custom-sidebars' ); ?></a>
					</span>

					<span class="add-to-menu">
						<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Sidebar', 'easy-custom-sidebars' ); ?>" name="add-taxonomy-menu-item" id="submit-taxonomy-<?php echo $taxonomy_name; ?>-custom-category" />
						<span class="spinner"></span>
					</span>
				</p>
			</div><!-- /.taxonomydiv -->
			<?php
		}

		/**
		 * Retrieve All Taxonomy Metaboxes
		 *
		 * Gets all taxonomies that are currently registered
		 * with the currently active WordPress theme and 
		 * registers metaboxes for each taxonomy for use on
		 * the Sidebar Admin Page.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/get_taxonomies 	get_taxonomies()
		 * @link 	http://codex.wordpress.org/Function_Reference/apply_filters 	apply_filters()
		 * @link 	http://codex.wordpress.org/Function_Reference/add_meta_box 		add_meta_box()
		 *
		 * @uses 	render_taxonomy_meta_box() 	- Defined in this class
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function setup_taxonomy_meta_boxes() {
			$taxonomies      = get_taxonomies( array( 'show_in_nav_menus' => true ), 'object' );
			$admin_page_name = $this->plugin_screen_hook_suffix;

			if ( ! $taxonomies ) {
				return;
			}

			foreach ( $taxonomies as $tax ) {
				$tax = apply_filters( 'ecs_sidebar_meta_box_object', $tax );
				if ( $tax ) {
					$id = $tax->name;
					add_meta_box( 
						"master-add-{$id}", 
						$tax->labels->name, 
						array( $this, 'render_taxonomy_meta_box' ),
						$admin_page_name, 
						'side', 
						'default',
						$tax 
					);
				}
			}
		}

		/**
		 * Displays a metabox for a taxonomy menu item.
		 *
		 * This function outputs the sidebar checklist metabox
		 * that is used on the admin page.
		 * 
		 * @link 	http://codex.wordpress.org/Function_Reference/apply_filters 				apply_filters()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post_type_object			get_post_type_object()
		 * @link 	http://codex.wordpress.org/Function_Reference/paginate_links				paginate_links()
		 * @link 	http://codex.wordpress.org/Function_Reference/add_query_arg					add_query_arg()
		 * @link 	http://codex.wordpress.org/Function_Reference/is_post_type_hierarchical		is_post_type_hierarchical()
		 * @link 	http://codex.wordpress.org/Function_Reference/esc_attr						esc_attr()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_terms						get_terms()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_taxonomy					get_taxonomy()
		 * @link 	http://codex.wordpress.org/Function_Reference/submit_button					submit_button()
		 * @link 	http://codex.wordpress.org/Function_Reference/is_wp_error					is_wp_error()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_option					get_option()
		 * @link 	http://codex.wordpress.org/Function_Reference/wp_count_terms					wp_count_terms()
		 *
		 * @global $nav_menu_selected_id
		 * 
		 * @uses  class ECS_Walker_Checklist 	- Defined in includes/admin/walker/class-ecs-walker-checklist.php
		 *
		 * @param string $object Not used.
		 * @param string $taxonomy The taxonomy object.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function render_taxonomy_meta_box( $object, $taxonomy ) {
			global $nav_menu_selected_id;
			$taxonomy_name = $taxonomy['args']->name;

			// paginate browsing for large numbers of objects
			$per_page = apply_filters( 'ecs_taxonomy_meta_box_per_page', 50 );
			$pagenum  = isset( $_REQUEST[$taxonomy_name . '-tab'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
			$offset   = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;

			$args = array(
				'child_of'     => 0,
				'exclude'      => '',
				'hide_empty'   => false,
				'hierarchical' => 1,
				'include'      => '',
				'number'       => $per_page,
				'offset'       => $offset,
				'order'        => 'ASC',
				'orderby'      => 'name',
				'pad_counts'   => false,
			);

			$terms = get_terms( $taxonomy_name, $args );
			$taxonomy_obj = get_taxonomy( $taxonomy_name );

			if ( ! $terms || is_wp_error($terms) ) {
				echo '<p>' . __( 'No items.' ) . '</p>';
				return;
			}

			$num_pages = ceil( wp_count_terms( $taxonomy_name , array_merge( $args, array('number' => '', 'offset' => '') ) ) / $per_page );

			$admin_url  = esc_url( 
							add_query_arg( 
								array( 
									'page' => $this->plugin_slug
								), 
								admin_url( 'themes.php' ) 
							) 
						);

			$page_links = paginate_links( array(
				'base' => add_query_arg(
					array(
						$taxonomy_name . '-tab' => 'all',
						'paged'                 => '%#%',
						'item-type'             => 'taxonomy',
						'item-object'           => $taxonomy_name
					), $admin_url
				),
				'format'    => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total'     => $num_pages,
				'current'   => $pagenum
			));

			$db_fields = false;
			if ( is_taxonomy_hierarchical( $taxonomy_name ) ) {
				$db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
			}

			$walker = new ECS_Walker_Checklist( $db_fields );

			$current_tab = 'most-used';
			if ( isset( $_REQUEST[$taxonomy_name . '-tab'] ) && in_array( $_REQUEST[$taxonomy_name . '-tab'], array('all', 'most-used', 'search') ) ) {
				$current_tab = $_REQUEST[$taxonomy_name . '-tab'];
			}

			if ( ! empty( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] ) ) {
				$current_tab = 'search';
			}

			$removed_args = array(
				'action',
				'customlink-tab',
				'edit-menu-item',
				'menu-item',
				'page-tab',
				'_wpnonce',
			);

			?>
			<div id="taxonomy-<?php echo $taxonomy_name; ?>" class="taxonomydiv">
				<ul id="taxonomy-<?php echo $taxonomy_name; ?>-tabs" class="taxonomy-tabs add-menu-item-tabs">
					<li <?php echo ( 'most-used' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($taxonomy_name . '-tab', 'most-used', remove_query_arg($removed_args))); ?>#tabs-panel-<?php echo $taxonomy_name; ?>-pop"><?php _e('Most Used'); ?></a></li>
					<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($taxonomy_name . '-tab', 'all', remove_query_arg($removed_args))); ?>#tabs-panel-<?php echo $taxonomy_name; ?>-all"><?php _e('View All'); ?></a></li>
					<li <?php echo ( 'search' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="nav-tab-link" href="<?php if ( $nav_menu_selected_id ) echo esc_url(add_query_arg($taxonomy_name . '-tab', 'search', remove_query_arg($removed_args))); ?>#tabs-panel-search-taxonomy-<?php echo $taxonomy_name; ?>"><?php _e('Search'); ?></a></li>
				</ul>

				<div id="tabs-panel-<?php echo $taxonomy_name; ?>-pop" class="tabs-panel <?php
					echo ( 'most-used' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>">
					<ul id="<?php echo $taxonomy_name; ?>checklist-pop" class="categorychecklist form-no-clear" >
						<!-- All Taxonomies Checkbox -->
						<li>
							<label class="menu-item-title">
								<input type="checkbox" value="1" name="menu-item[-1111][menu-item-object-id]" class="menu-item-checkbox">
								<strong><?php echo sprintf( __( 'All %s', 'easy-custom-sidebars' ), $taxonomy_obj->labels->name ); ?></strong>
							</label>
							<input class="menu-item-db-id" type="hidden" value="0" name="menu-item[-1111][menu-item-db-id]">
							<input class="menu-item-object" type="hidden" value="<?php echo $taxonomy_obj->name; ?>" name="menu-item[-1111][menu-item-object]">
							<input class="menu-item-parent-id" type="hidden" value="0" name="menu-item[-1111][menu-item-parent-id]">
							<input class="menu-item-type" type="hidden" value="taxonomy_all" name="menu-item[-1111][menu-item-type]">
							<input class="menu-item-title" type="hidden" value="<?php echo sprintf( __( 'All %s', 'easy-custom-sidebars' ), $taxonomy_obj->labels->name ); ?>" name="menu-item[-1111][menu-item-title]">
						</li>

						<?php
						$popular_terms = get_terms( $taxonomy_name, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
						$args['walker'] = $walker;
						echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $popular_terms), 0, (object) $args );
						?>
					</ul>
				</div><!-- /.tabs-panel -->

				<div id="tabs-panel-<?php echo $taxonomy_name; ?>-all" class="tabs-panel tabs-panel-view-all <?php
					echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>">
					<?php if ( ! empty( $page_links ) ) : ?>
						<div class="add-menu-item-pagelinks">
							<?php echo $page_links; ?>
						</div>
					<?php endif; ?>
					<ul id="<?php echo $taxonomy_name; ?>checklist" data-wp-lists="list:<?php echo $taxonomy_name?>" class="categorychecklist form-no-clear">
						<!-- All Taxonomies Checkbox -->
						<li>
							<label class="menu-item-title">
								<input type="checkbox" value="1" name="menu-item[-1111][menu-item-object-id]" class="menu-item-checkbox">
								<strong><?php echo sprintf( __( 'All %s', 'easy-custom-sidebars' ), $taxonomy_obj->labels->name ); ?></strong>
							</label>
							<input class="menu-item-db-id" type="hidden" value="0" name="menu-item[-1111][menu-item-db-id]">
							<input class="menu-item-object" type="hidden" value="<?php echo $taxonomy_obj->name; ?>" name="menu-item[-1111][menu-item-object]">
							<input class="menu-item-parent-id" type="hidden" value="0" name="menu-item[-1111][menu-item-parent-id]">
							<input class="menu-item-type" type="hidden" value="taxonomy_all" name="menu-item[-1111][menu-item-type]">
							<input class="menu-item-title" type="hidden" value="<?php echo sprintf( __( 'All %s', 'easy-custom-sidebars' ), $taxonomy_obj->labels->name ); ?>">
						</li>
						<?php
						$args['walker'] = $walker;
						echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $terms), 0, (object) $args );
						?>
					</ul>
					<?php if ( ! empty( $page_links ) ) : ?>
						<div class="add-menu-item-pagelinks">
							<?php echo $page_links; ?>
						</div>
					<?php endif; ?>
				</div><!-- /.tabs-panel -->

				<div class="tabs-panel <?php
					echo ( 'search' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>" id="tabs-panel-search-taxonomy-<?php echo $taxonomy_name; ?>">
					<?php
					if ( isset( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] ) ) {
						$searched = esc_attr( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] );
						$search_results = get_terms( $taxonomy_name, array( 'name__like' => $searched, 'fields' => 'all', 'orderby' => 'count', 'order' => 'DESC', 'hierarchical' => false ) );
					} else {
						$searched = '';
						$search_results = array();
					}
					?>
					<p class="quick-search-wrap">
						<input type="search" class="quick-search" title="<?php esc_attr_e('Search'); ?>" value="<?php echo $searched; ?>" name="quick-search-taxonomy-<?php echo $taxonomy_name; ?>" placeholder="<?php _e( 'Search', 'easy-custom-sidebars' ) ?>" />
						<span class="spinner"></span>
						<?php submit_button( __( 'Search' ), 'button-small quick-search-submit button-secondary hide-if-js', 'submit', false, array( 'id' => 'submit-quick-search-taxonomy-' . $taxonomy_name ) ); ?>
					</p>

					<ul id="<?php echo $taxonomy_name; ?>-search-checklist" data-wp-lists="list:<?php echo $taxonomy_name?>" class="categorychecklist form-no-clear">
					<?php if ( ! empty( $search_results ) && ! is_wp_error( $search_results ) ) : ?>
						<?php
						$args['walker'] = $walker;
						echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $search_results), 0, (object) $args );
						?>
					<?php elseif ( is_wp_error( $search_results ) ) : ?>
						<li><?php echo $search_results->get_error_message(); ?></li>
					<?php elseif ( ! empty( $searched ) ) : ?>
						<li><?php _e('No results found.'); ?></li>
					<?php endif; ?>
					</ul>
				</div><!-- /.tabs-panel -->

				<p class="button-controls">
					<span class="list-controls">
						<a href="<?php
							echo esc_url(add_query_arg(
								array(
									$taxonomy_name . '-tab' => 'all',
									'selectall' => 1,
								),
								remove_query_arg($removed_args)
							));
						?>#taxonomy-<?php echo $taxonomy_name; ?>" class="select-all"><?php _e( 'Select All', 'easy-custom-sidebars' ); ?></a>
					</span>

					<span class="add-to-menu">
						<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Sidebar', 'easy-custom-sidebars' ); ?>" name="add-taxonomy-menu-item" id="submit-taxonomy-<?php echo $taxonomy_name; ?>" />
						<span class="spinner"></span>
					</span>
				</p>
			</div><!-- /.taxonomydiv -->
			<?php
		}

		/**
		 * Register Template Author Metabox
		 *
		 * Registers the custom metabox that is added in order
		 * to cater for WordPress author archive templates.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/add_meta_box 		add_meta_box()
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function setup_author_meta_box() {
			add_meta_box(
				'master-add-author-archive-metabox',
				__( 'Author Archives', 'easy-custom-sidebars' ),
				array( $this, 'render_author_meta_box' ),
				$this->plugin_screen_hook_suffix,
				'side',
				'default'
			);
		}

		/**
		 * Render Author Archive Metabox
		 *
		 * This function outputs the sidebar checklist metabox
		 * that is used in the Author Archive metabox on the 
		 * admin page.
		 * 
		 * @link 	http://codex.wordpress.org/Function_Reference/get_users 				get_users()
		 * @link 	http://codex.wordpress.org/Function_Reference/esc_url					esc_url()
		 * @link 	http://codex.wordpress.org/Function_Reference/admin_url					admin_url()
		 * @link 	http://codex.wordpress.org/Function_Reference/add_query_arg				add_query_arg()
		 *
		 * @global $nav_menu_selected_id
		 * 
		 * @uses  class ECS_Walker_Checklist 	- Defined in includes/admin/walker/class-ecs-walker-checklist.php
		 *
		 * @param string $object Not used.
		 * @param string $taxonomy The taxonomy object.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function render_author_meta_box() {
			global $nav_menu_selected_id;
			

			// Paginate browsing for large numbers of objects
			$per_page = apply_filters( 'ecs_author_meta_box_per_page', 50 );

			// Check if any variables have been passed in the URL
			$pagenum  = isset( $_REQUEST['custom-item-type'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
			$offset   = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;

			// Define query args for author query 
			$roles = array( 'Administrator', 'Editor', 'Author' ); 
			
			$all_authors = array();

			// Get all users that have author privaleges 
			foreach ( $roles as $role ) {
				$args = array(
					'role'        => $role,
					'orderby'     => 'display_name',
					'order'       => 'ASC',
					'offset'      => '',
					'search'      => '',
					'number'      => '',
					'count_total' => false,
					'fields'      => 'all',
					'who'         => ''
				);

				$authors = get_users( $args );

				if ( is_array( $authors ) ) {
					$all_authors = array_merge( $all_authors, $authors );
				}
			}
			
			// Display feedback message if there are no categories
			if ( ! $all_authors || is_null( $all_authors ) ) {
				echo '<p>' . __( 'No items.', 'easy-custom-sidebars' ) . '</p>';
				return;
			}

			// Determine number of pages
			$num_pages = count( $all_authors ) / $per_page;

			// Define admin page url
			$admin_url  = esc_url( 
							add_query_arg( 
								array( 
									'page' => 'easy-custom-sidebars' 
								), 
								admin_url( 'themes.php' ) 
							) 
						);

			// Generate pagination
			$page_links = paginate_links( array(
				'base' => add_query_arg(
					array(
						'author-tab' => 'all',
						'paged'                 => '%#%',
						'item-type'             => 'author_archive',
						'item-object'           => 'author_archive',
						'custom-item-type'		=> 'author_archive',
					), $admin_url
				),
				'format'    => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total'     => $num_pages,
				'current'   => $pagenum
			) );

			$db_fields = false;

			// Define our own custom walker
			$walker = new ECS_Walker_Checklist( $db_fields );

			// Determine the current tab to use
			$current_tab = 'all';
			if ( isset( $_REQUEST['author-tab'] ) && in_array( $_REQUEST['author-tab'], array('all', 'most-used', 'search') ) ) {
				$current_tab = $_REQUEST['author-tab'];
			}

			if ( ! empty( $_REQUEST['quick-search-author-custom-author'] ) ) {
				$current_tab = 'search';
			}

			$removed_args = array(
				'action',
				'customlink-tab',
				'edit-menu-item',
				'menu-item',
				'page-tab',
				'_wpnonce',
			);

			?>
			<div id="master-author-archive" class="taxonomydiv">
				
				<!-- Tab Panel Tabs -->
				<ul id="author-archive-tabs" class="author-tabs add-menu-item-tabs">
					<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>>
						<a href="#tabs-panel-master-author-archive-all" class="nav-tab-link">
							<?php _e('View All'); ?>
						</a>
					</li>
					<li <?php echo ( 'search' == $current_tab ? ' class="tabs"' : '' ); ?>>
						<a href="#tabs-panel-master-author-archive-search" class="nav-tab-link">
							<?php _e('Search'); ?>
						</a>
					</li>
				</ul>
				
				<!-- Tab Panel All -->
				<div id="tabs-panel-master-author-archive-all" class="tabs-panel tabs-panel-view-all <?php
					echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>">
					<?php if ( ! empty( $page_links ) ) : ?>
						<div class="add-menu-item-pagelinks">
							<?php echo $page_links; ?>
						</div>
					<?php endif; ?>
					<ul id="author-checklist" data-wp-lists="list:author" class="categorychecklist form-no-clear">
						<?php
							// Use the custom walker
							$args['walker'] = $walker;

							// Set custom array index to indicate a custom data type
							$args['custom_item_type'] = 'author_archive';	

							$db_id = -1222;

							$author_count = 0;
							$total_count  = 0;
						?>
						<?php foreach ( $all_authors as $author ) : ?>
							<?php if ( $offset == $author_count && $total_count < $per_page ) : ?>
								<li>
									<label class="menu-item-title">
										<input type="checkbox" value ="1" name="menu-item[<?php echo $db_id; ?>][menu-item-object-id]" class="menu-item-checkbox"> <?php echo $author->display_name; ?>
									</label>
									<input class="menu-item-db-id" type="hidden" value="0" name="menu-item[<?php echo $db_id; ?>][menu-item-db-id]">
									<input class="menu-item-object" type="hidden" value="<?php echo $author->ID; ?>" name="menu-item[<?php echo $db_id; ?>][menu-item-object]">
									<input class="menu-item-parent-id" type="hidden" value="0" name="menu-item[<?php echo $db_id; ?>][menu-item-parent-id]">
									<input class="menu-item-type" type="hidden" value="author_archive" name="menu-item[<?php echo $db_id; ?>][menu-item-type]">
									<input class="menu-item-title" type="hidden" value="<?php echo $author->display_name; ?>" name="menu-item[<?php echo $db_id; ?>][menu-item-title]">
									<input class="menu-item-url" type="hidden" value="<?php echo $author->user_url; ?>" name="menu-item[<?php echo $db_id; ?>][menu-item-url]">
								</li>
								<?php $total_count++; ?>
							<?php else : ?>
								<?php $author_count++; ?>
							<?php endif; ?>
						<?php $db_id++; endforeach; ?>

					</ul>
					<?php if ( ! empty( $page_links ) ) : ?>
						<div class="add-menu-item-pagelinks">
							<?php echo $page_links; ?>
						</div>
					<?php endif; ?>
				</div><!-- /.tabs-panel -->
				
				<!-- Tab Panel Search -->
				<div class="tabs-panel <?php
					echo ( 'search' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
				?>" id="tabs-panel-master-author-archive-search">
					<?php 
						$searched = '';
						$search_results = array();
					 ?>

					<p class="quick-search-wrap">
						<input type="search" class="quick-search" title="<?php esc_attr_e('Search'); ?>" value="<?php echo $searched; ?>" name="quick-search-author-archive" placeholder="<?php _e( 'Search', 'easy-custom-sidebars' ) ?>" />
						<span class="spinner"></span>
						<?php submit_button( __( 'Search' ), 'button-small quick-search-submit button-secondary hide-if-js', 'submit', false, array( 'id' => 'submit-quick-search-author-archive' ) ); ?>
					</p>
					<ul id="author-archive-search-checklist" data-wp-lists="list:author-archive" class="categorychecklist form-no-clear">
					</ul>

				</div><!-- /.tabs-panel -->

				<p class="button-controls">
					<span class="list-controls">
						<a href="#" class="select-all"><?php _e( 'Select All', 'easy-custom-sidebars' ); ?></a>
					</span>

					<span class="add-to-menu">
						<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Sidebar', 'easy-custom-sidebars' ); ?>" name="add-author-archive-item" id="submit-master-author-archive" />
						<span class="spinner"></span>
					</span>
				</p>
			</div>
			<?php
		}	

		/**
		 * Register Template Hierachy Metabox
		 *
		 * Registers the custom metabox that is added in order
		 * to cater for WordPress templates that are not either
		 * a post type or taxonomy.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/add_meta_box 		add_meta_box()
		 *
		 * @uses 	master_sidebar_item_post_type_meta_box() 	defined in includes/theme-sidebar-admin-page-functions.php
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function setup_template_meta_box() {
			add_meta_box(
				'master-add-page-heirachy-metabox',
				__( 'Template Hierarchy', 'easy-custom-sidebars' ),
				array( $this, 'render_template_meta_box' ),
				$this->plugin_screen_hook_suffix,
				'side',
				'default'
			);
		}

		/**
		 * Render Template Hierachy Metabox
		 *
		 * This function generates and outputs the required html
		 * markup required for the Template Hierarchy metabox that
		 * is displayed on the Admin Page.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/current_user_can 		current_user_can()
		 * @link 	http://codex.wordpress.org/Function_Reference/wp_get_theme 			wp_get_theme()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_page_templates 	get_page_templates()
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function render_template_meta_box() {
			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) )
				wp_die( -1 );

			// Create array to hold template hierachy items
			$template_items   = array();
			$checklist_output = '';
			$db_id = -1111;

			// Add 404 page
			$template_items[] = array(
				'menu-item-db-id'     => $db_id,
				'menu-item-title'     => __( '404 - Page Not Found', 'easy-custom-sidebars' ),
				'menu-item-object'    => '404',
				'menu-item-parent-id' => $db_id,
				'menu-item-type'      => 'template_hierarchy',
				'menu-item-url'       => '#'	
			);
			$db_id++;

			// Add author archive
			$template_items[] = array(
				'menu-item-db-id'     => $db_id,
				'menu-item-title'     => __( 'Author Archive', 'easy-custom-sidebars' ),
				'menu-item-object'    => 'author_archive_all',
				'menu-item-parent-id' => $db_id,
				'menu-item-type'      => 'template_hierarchy',
				'menu-item-url'       => '#'
			);
			$db_id++;
			
			// Add index page
			$template_items[] = array(
				'menu-item-db-id'     => $db_id,
				'menu-item-title'     => __( 'Blog Index Page', 'easy-custom-sidebars' ),
				'menu-item-object'    => 'index_page',
				'menu-item-parent-id' => $db_id,
				'menu-item-type'      => 'template_hierarchy',
				'menu-item-url'       => '#'
			);
			$db_id++;

			// Add date archive
			$template_items[] = array(
				'menu-item-db-id'     => $db_id,
				'menu-item-title'     => __( 'Date Archive', 'easy-custom-sidebars' ),
				'menu-item-object'    => 'date_archive',
				'menu-item-parent-id' => $db_id,
				'menu-item-type'      => 'template_hierarchy',
				'menu-item-url'       => '#'
			);
			$db_id++;

			// Add page templates 
			$page_templates = wp_get_theme()->get_page_templates();

			foreach ( $page_templates as $template_name => $template_filename ) {
				//echo "$template_name ($template_filename)<br />";
				$template_items[] = array(
					'menu-item-db-id'     => $db_id,
					'menu-item-title'     => sprintf( __( 'Page Template: %s', 'easy-custom-sidebars' ), $template_filename ),
					'menu-item-object'    => 'page-template-'. $template_name,
					'menu-item-parent-id' => $db_id,
					'menu-item-type'      => 'template_hierarchy',
					'menu-item-url'       => '#'				
				);
				$db_id++;
			}

			// Add index page
			$template_items[] = array(
				'menu-item-db-id'     => $db_id,
				'menu-item-title'     => __( 'Search Results', 'easy-custom-sidebars' ),
				'menu-item-object'    => 'search_results',
				'menu-item-parent-id' => $db_id,
				'menu-item-type'      => 'template_hierarchy',
				'menu-item-url'       => '#'
			);
			$db_id++;

			?>
			<div id="master-page-hierachy" class="posttypediv">
				
				<!-- Tabs -->
				<ul id="template-hierarchy-tabs" class="posttype-tabs add-menu-item-tabs">
					<li class="tabs"><a href="#tabs-panel-post_tag-all" class="nav-tab-link"><?php _e( 'View All', 'easy-custom-sidebars' ); ?></a></li>
				</ul><!-- END #template-hierarchy-tabs -->

				<!-- Panels -->
				<div id="master-page-hierachy" class="tabs-panel tabs-panel-view-all tabs-panel-active">
					
					<ul class="categorychecklist form-no-clear" data-wp-lists="list:testimonials" id="testimonialschecklist">
						<!-- All Post Type Checkbox -->
						<?php foreach ( $template_items as $template_item ) : ?>
							<li>
								<label class="menu-item-title">
									<input type="checkbox" value ="1" name="menu-item[<?php echo $template_item['menu-item-db-id']; ?>][menu-item-object-id]" class="menu-item-checkbox"> <?php echo $template_item['menu-item-title']; ?>
								</label>
								<input class="menu-item-db-id" type="hidden" value="0" name="menu-item[<?php echo $template_item['menu-item-db-id']; ?>][menu-item-db-id]">
								<input class="menu-item-object" type="hidden" value="<?php echo $template_item['menu-item-object']; ?>" name="menu-item[<?php echo $template_item['menu-item-db-id']; ?>][menu-item-object]">
								<input class="menu-item-parent-id" type="hidden" value="0" name="menu-item[<?php echo $template_item['menu-item-db-id']; ?>][menu-item-parent-id]">
								<input class="menu-item-type" type="hidden" value="<?php echo $template_item['menu-item-type']; ?>" name="menu-item[<?php echo $template_item['menu-item-db-id']; ?>][menu-item-type]">
								<input class="menu-item-title" type="hidden" value="<?php echo $template_item['menu-item-title']; ?>" name="menu-item[<?php echo $template_item['menu-item-db-id']; ?>][menu-item-title]">
								<input class="menu-item-url" type="hidden" value="<?php echo $template_item['menu-item-url']; ?>" name="menu-item[<?php echo $template_item['menu-item-db-id']; ?>][menu-item-url]">
							</li>
						<?php endforeach; ?>
					</ul>
				</div><!-- END .tabs-panel -->

				<p class="button-controls">
					<span class="list-controls">
						<a href="#" class="select-all"><?php _e( 'Select All', 'easy-custom-sidebars' ); ?></a>
					</span>

					<span class="add-to-menu">
						<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Sidebar', 'easy-custom-sidebars' ); ?>" name="add-page-heirachy-item" id="master-submit-page-hierachy" />
						<span class="spinner"></span>
					</span>
				</p>

			</div><!-- END #template-hierarchy -->
			<?php
		}

		/**
		 * Get HTML Sidebar Attachment Data Output
		 *
		 * This function is responsible for generating and 
		 * outputting the html list item markup on the admin
		 * page which is used to display existing attachments.
		 * This funciton requires nav-menu.php functions.  
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/current_user_can 		current_user_can()
		 * @link 	http://codex.wordpress.org/Function_Reference/wp_die 				wp_die()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post 				get_post()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post_meta 		get_post_meta()
		 * @link 	http://codex.wordpress.org/Function_Reference/apply_filters 		apply_filters()
		 *
		 * @uses master_get_sidebar_instance() 	defined in includes/theme-sidebar-functions.php
		 * @uses master_sidebar_author_quick_search()
		 * 
		 * @since  1.0
		 * @version 1.0.9
		 * 
		 */
		public function get_sidebar_attachment_markup( $sidebar_id ) {
			
			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );			
			}

			/**
			 * Include Nav Menu File
			 * 
			 * As this function uses some of the utility functions 
			 * contained in this file.
			 * 
			 */
			require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

			$data = ECS_Posttype::get_instance();

			/**
			 * Variables to store menu output
			 * @var mixed
			 */
			$sidebar             = $data->get_sidebar_instance( $sidebar_id );
			$sidebar_attachments = array();
			$sidebar_data        = array();
			$item_ids            = array();
			$menu_items          = array();
			$output              = '';

			/**
			 * Generate Output
			 * 
			 * Build valid output if the sidebar
			 * queried exists.
			 * 
			 */
			if ( $sidebar ) {
				// Get sidebar attachment data
				$sidebar_attachments = get_post_meta( $sidebar->ID, 'sidebar_attachments', true );

				foreach ( $sidebar_attachments as $menu_item => $menu_item_data ) {

					if ( ! empty( $menu_item_data['menu-item-type'] ) && ! empty( $menu_item_data['menu-item-object-id'] ) ) {
						
						// Get type of item
						$menu_item_type = $menu_item_data['menu-item-type'];
						
						// Initialise type, id and data
						switch ( $menu_item_type ) {
							
							case 'post_type':
								$item_ids[] = array( 
									'type' => 'post_type', 
									'id'   => $menu_item_data['menu-item-object-id'],
									'data' => $menu_item_data,
								);
								break;

							case 'post_type_all':
								$item_ids[] = array( 
									'type' => 'post_type_all', 
									'id'   => $menu_item_data['menu-item-object-id'],
									'data' => $menu_item_data,
								);
								break;

							case 'post_type_archive':
								$item_ids[] = array( 
									'type' => 'post_type_archive', 
									'id'   => $menu_item_data['menu-item-object-id'],
									'data' => $menu_item_data,
								);
								break;
		
							case 'taxonomy':
								$item_ids[] = array( 
									'type' => 'taxonomy', 
									'id'   => $menu_item_data['menu-item-object-id'],
									'data' => $menu_item_data,
								);
								break;

							case 'taxonomy_all':
								$item_ids[] = array( 
									'type' => 'taxonomy_all', 
									'id'   => $menu_item_data['menu-item-object-id'],
									'data' => $menu_item_data,
								);
								break;
							
							case 'category_posts':
								$item_ids[] = array( 
									'type' => 'category_posts',
									'id'   => $menu_item_data['menu-item-object-id'],
									'data' => $menu_item_data,
								);
								break;

							case 'author_archive':
								$item_ids[] = array( 
									'type' => 'author_archive',
									'id'   => $menu_item_data['menu-item-object'],
									'data' => $menu_item_data,
								);
								break;

							case 'template_hierarchy':
								$item_ids[] = array( 
									'type' => 'template_hierarchy',
									'id'   => $menu_item_data['menu-item-object'],
									'data' => $menu_item_data,
								);
								break;
						}

						$sidebar_data[] = $menu_item_data;
					}
				}

				// Die if error
				if ( is_wp_error( $item_ids ) ) {
					wp_die();
				}

				/**
				 * Generate Menu Items
				 *
				 * Determine the type of object we are working with
				 * and build an object that contains its properties.
				 * By building the object manually we avoid a trip
				 * to the database which results in a huge performance
				 * boost and a minimal database footprint.
				 * 
				 */
				foreach ( (array) $item_ids as $menu_item_id ) {

					switch ( $menu_item_id['type'] ) {
						
						case 'post_type':
							$current_post  = get_post( $menu_item_id['id'] );
							$post_type_obj = get_post_type_object( $current_post->post_type );

							
							// Manually build object (huge performance boost)
							if ( $post_type_obj ) {
								$menu_item                   = new stdClass();
								$menu_item                   = $current_post;
								$menu_item->ID               = $current_post->ID;
								$menu_item->db_id            = $current_post->ID;
								$menu_item->object           = $post_type_obj->name;
								$menu_item->type_label       = $post_type_obj->labels->singular_name;
								$menu_item->object_id        = $current_post->ID;
								$menu_item->menu_item_parent = 0;
								$menu_item->type             = 'post_type';
								$menu_item->post_title       = $current_post->post_title;
								$menu_item->label            = $current_post->post_title;
								$menu_item->url              = get_permalink( $current_post->ID );
								$menu_item->post_status      = 'publish';

								// Add to menu items
								$menu_items[]    = $menu_item;
							}
							break;

						case 'post_type_all':
							$post_type_obj = get_post_type_object( $menu_item_id['data']['menu-item-object'] );
		
							// Manually build object (huge performance boost)
							if ( $post_type_obj ) {
								$menu_item                   = new stdClass();
								$menu_item->ID               = $menu_item_id['id'];
								$menu_item->db_id            = $menu_item_id['data']['menu-item-db-id'];
								$menu_item->object           = $post_type_obj->name;
								$menu_item->type_label       = $post_type_obj->labels->name;
								$menu_item->object_id        = $menu_item_id['data']['menu-item-object-id'];
								$menu_item->menu_item_parent = $menu_item_id['data']['menu-item-object'];
								$menu_item->menu_order       = 0;
								$menu_item->type             = 'post_type_all';
								$menu_item->title            = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type_obj->labels->name );
								$menu_item->label            = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type_obj->labels->name );
								$menu_item->url              = esc_url( add_query_arg( array( 'post_type' => $post_type_obj->name ), admin_url( 'edit.php' ) ) );
								$menu_item->post_status      = 'publish';

								// Add to menu items
								$menu_items[]    = $menu_item;
							}
							break;

					case 'post_type_archive':
						$post_type_obj = get_post_type_object( $menu_item_id['data']['menu-item-object'] );

						// Manually build object (huge performance boost)
						if ( $post_type_obj ) {
							$menu_item                   = new stdClass();
							$menu_item->ID               = $menu_item_id['id'];
							$menu_item->db_id            = $menu_item_id['data']['menu-item-db-id'];
							$menu_item->object           = $post_type_obj->name;
							$menu_item->type_label       = sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_obj->labels->name );
							$menu_item->object_id        = $menu_item_id['data']['menu-item-object-id'];
							$menu_item->menu_item_parent = $menu_item_id['data']['menu-item-object'];
							$menu_item->menu_order       = 0;
							$menu_item->type             = 'post_type_archive';
							$menu_item->title            = sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_obj->labels->singular_name );
							$menu_item->label            = sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_obj->labels->singular_name );
							$menu_item->url              = esc_url( add_query_arg( array( 'post_type' => $post_type_obj->name ), admin_url( 'edit.php' ) ) );
							$menu_item->post_status      = 'publish';

							// Add to menu items
							$menu_items[]    = $menu_item;
						}
						break;	

						case 'taxonomy':
							$tax_obj  = get_taxonomy( $menu_item_id['data']['menu-item-object'] );
							$tax_term = get_term_by( 'id', $menu_item_id['data']['menu-item-object-id'], $tax_obj->name );

							if ( $tax_obj && $tax_term ) {
								$menu_item                   = new stdClass();
								$menu_item->ID               = $menu_item_id['id'];
								$menu_item->db_id            = $menu_item_id['id'];
								$menu_item->object           = $tax_obj->name;
								$menu_item->type_label       = $tax_obj->labels->singular_name;
								$menu_item->object_id        = $menu_item_id['id'];
								$menu_item->menu_order       = 0;
								$menu_item->menu_item_parent = 0;
								$menu_item->type             = 'taxonomy';
								$menu_item->title            = $tax_term->name;
								$menu_item->label            = $tax_term->name;
								$menu_item->url              = get_term_link( (int) $menu_item_id['id'], $tax_obj->name );
								$menu_item->post_status      = 'publish';

								// Add to menu items
								$menu_items[]    = $menu_item;
							}
							break;

						case 'taxonomy_all':

							$tax_obj  = get_taxonomy( $menu_item_id['data']['menu-item-object'] );

							if ( $tax_obj ) {
								$menu_item                   = new stdClass();
								$menu_item->ID               = $menu_item_id['id'];
								$menu_item->db_id            = $menu_item_id['id'];
								$menu_item->object           = $tax_obj->name;
								$menu_item->type_label       = $tax_obj->labels->name;
								$menu_item->object_id        = $menu_item_id['data']['menu-item-object-id'];
								$menu_item->menu_order       = 0;
								$menu_item->menu_item_parent = $menu_item_id['data']['menu-item-object'];
								$menu_item->type             = 'taxonomy_all';
								$menu_item->title            = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $tax_obj->labels->name );
								$menu_item->label            = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $tax_obj->labels->name );
								$menu_item->url              = esc_url( add_query_arg( array( 'taxonomy' => $tax_obj->name ), admin_url( 'edit-tags.php' ) ) );
								$menu_item->post_status      = 'publish';

								// Add to menu items
								$menu_items[]    = $menu_item;							
							}

							break;

						case 'category_posts':
							$tax_obj  = get_taxonomy( $menu_item_id['data']['menu-item-object'] );
							$tax_term = get_term_by( 'id', $menu_item_id['data']['menu-item-object-id'], $tax_obj->name );
							$category = get_category( $menu_item_id['data']['menu-item-object-id'] );

							if ( $tax_obj && $tax_term && $category ) {
								$menu_item                   = new stdClass();
								$menu_item->ID               = $menu_item_id['id'];
								$menu_item->db_id            = $menu_item_id['id'];
								$menu_item->object           = $tax_obj->name;
								$menu_item->type_label       = $tax_obj->labels->singular_name;
								$menu_item->object_id        = $menu_item_id['id'];
								$menu_item->menu_order       = 0;
								$menu_item->menu_item_parent = 0;
								$menu_item->type             = 'category_posts';
								$menu_item->title            = get_term_field( 'name', $menu_item_id['data']['menu-item-object-id'], $menu_item_id['data']['menu-item-object'], 'raw' );
								$menu_item->label            = get_term_field( 'name', $menu_item_id['data']['menu-item-object-id'], $menu_item_id['data']['menu-item-object'], 'raw' );
								$menu_item->url              = esc_url( add_query_arg( array( 'category_name' => $category->slug ), admin_url( 'edit.php' ) ) );
								$menu_item->post_status      = 'publish';

								// Add to menu items
								$menu_items[]    = $menu_item;							
							}
							break;

						case 'author_archive':

							$user = get_user_by( 'id', $menu_item_id['data']['menu-item-object-id'] );

							if ( $user ) {
								$menu_item                   = new stdClass();
								$menu_item->ID               = $menu_item_id['data']['menu-item-object-id'];
								$menu_item->db_id            = $menu_item_id['data']['menu-item-object-id'];
								$menu_item->object           = 'user';
								$menu_item->type_label       = __( 'Author Archive', 'easy-custom-sidebars' );
								$menu_item->object_id        = $menu_item_id['data']['menu-item-object-id'];
								$menu_item->menu_order       = 0;
								$menu_item->menu_item_parent = 0;
								$menu_item->type             = 'author_archive';
								$menu_item->title            = $user->display_name;
								$menu_item->label            = $user->display_name;
								$menu_item->url              = esc_url( get_author_posts_url( $menu_item_id['id'] ) );
								$menu_item->post_status      = 'publish';

								// Add to menu items
								$menu_items[]    = $menu_item;
							}
							break;

						case 'template_hierarchy':

							// List of available templates
							$templates = array( 
								'404'                => __( '404 - Page Not Found', 'easy-custom-sidebars' ),
								'author_archive_all' => __( 'Author Archive', 'easy-custom-sidebars' ),
								'index_page'         => __( 'Blog Index Page', 'easy-custom-sidebars' ),
								'date_archive'       => __( 'Date Archive', 'easy-custom-sidebars' ),
								'search_results'     => __( 'Search Results', 'easy-custom-sidebars' ),
							);

							// Add page templates 
							$page_templates = wp_get_theme()->get_page_templates();
							
							foreach ( $page_templates as $template_name => $template_filename ) {
								$templates[ 'page-template-' . $template_name ] = sprintf( __( 'Page Template: %s', 'easy-custom-sidebars' ), $template_filename );
							}

							// Only add item if the template exists
							if ( array_key_exists( $menu_item_id['data']['menu-item-object'], $templates ) ) {

								$key = $menu_item_id['data']['menu-item-object'];
								
								$menu_item                   = new stdClass();
								$menu_item->ID               = $menu_item_id['data']['menu-item-db-id'];
								$menu_item->db_id            = $menu_item_id['data']['menu-item-db-id'];
								$menu_item->object           = $menu_item_id['data']['menu-item-object'];
								$menu_item->type_label       = __( 'Template', 'easy-custom-sidebars' );
								$menu_item->object_id        = $menu_item_id['id'];
								$menu_item->menu_order       = 0;
								$menu_item->menu_item_parent = 0;
								$menu_item->type             = 'template_hierarchy';
								$menu_item->title            = $templates[ $key ];
								$menu_item->label            = $templates[ $key ];
								$menu_item->url              = esc_url( add_query_arg( array( 'post_type' => 'page' ), admin_url( 'edit.php' ) ) );
								$menu_item->post_status      = 'publish';

								// Add to menu items
								$menu_items[]    = $menu_item;
							}							
							break;
					}
				}					

				// Define Walker
				$walker_class_name = apply_filters( 'ecs_edit_sidebar_walker', 'ECS_Walker_Edit', $menu_items );

				// Die if walker doesn't exist
				if ( ! class_exists( $walker_class_name ) ) {
					wp_die();
				}

				if ( ! empty( $menu_items ) ) {

					$args = array(
						'after'       => '',
						'before'      => '',
						'link_after'  => '',
						'link_before' => '',
						'pending'     => true,
						'walker'      => new $walker_class_name,
					);

					$output .= walk_nav_menu_tree( $menu_items, 0, (object) $args );
				}	
			}
			
			// Return the output
			return $output;
		}

		/**
		 * [quick_search description]
		 * @param  array  $request [description]
		 * @return [type]          [description]
		 */
		public function quick_search( $request = array() ) {
			$args            = array();
			$type            = isset( $request['type'] ) ? $request['type'] : '';
			$object_type     = isset( $request['object_type'] ) ? $request['object_type'] : '';
			$query           = isset( $request['q'] ) ? $request['q'] : '';
			$response_format = isset( $request['response-format'] ) && in_array( $request['response-format'], array( 'json', 'markup' ) ) ? $request['response-format'] : 'json';

			/**
			 * Change $type if it is a custom category metabox 
			 * that shows the All Posts In Category items.
			 */
			if ( 'quick-search-taxonomy-category-custom-category' == $type ) {
				$type = 'quick-search-taxonomy-category';
			}

			if ( 'markup' == $response_format ) {
				$args['walker'] = new ECS_Walker_Checklist;
			}

			if ( 'get-post-item' == $type ) {
				if ( post_type_exists( $object_type ) ) {
					if ( isset( $request['ID'] ) ) {
						$object_id = (int) $request['ID'];
						if ( 'markup' == $response_format ) {

							echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', array( get_post( $object_id ) ) ), 0, (object) $args );
						} elseif ( 'json' == $response_format ) {
							$post_obj = get_post( $object_id );
							echo json_encode(
								array(
									'ID'         => $object_id,
									'post_title' => get_the_title( $object_id ),
									'post_type'  => get_post_type( $object_id )
								)
							);
							echo "\n";
						}
					}
				} elseif ( taxonomy_exists( $object_type ) ) {
					if ( isset( $request['ID'] ) ) {
						$object_id = (int) $request['ID'];
						if ( 'markup' == $response_format ) {
							echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', array( get_term( $object_id, $object_type ) ) ), 0, (object) $args );
						} elseif ( 'json' == $response_format ) {
							$post_obj = get_term( $object_id, $object_type );
							echo json_encode(
								array(
									'ID'         => $object_id,
									'post_title' => $post_obj->name,
									'post_type'  => $object_type
								)
							);
							echo "\n";
						}
					}

				}

			} elseif ( preg_match('/quick-search-(posttype|taxonomy)-([a-zA-Z_-]*\b)/', $type, $matches) ) {
				if ( 'posttype' == $matches[1] && get_post_type_object( $matches[2] ) ) {
					query_posts(array(
						'posts_per_page' => 10,
						'post_type'      => $matches[2],
						's'              => $query
					));
					if ( ! have_posts() )
						return;
					while ( have_posts() ) {
						the_post();
						if ( 'markup' == $response_format ) {
							$var_by_ref = get_the_ID();

							echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', array( get_post( $var_by_ref ) ) ), 0, (object) $args );
						} elseif ( 'json' == $response_format ) {
							echo json_encode(
								array(
									'ID'         => get_the_ID(),
									'post_title' => get_the_title(),
									'post_type'  => get_post_type()
								)
							);
							echo "\n";
						}
					}
				} elseif ( 'taxonomy' == $matches[1] ) {
					$terms = get_terms( $matches[2], array(
						'name__like' => $query,
						'number'     => 10
					));
					if ( empty( $terms ) || is_wp_error( $terms ) )
						return;
					foreach( (array) $terms as $term ) {
						if ( 'markup' == $response_format ) {

							/**
							 * Change Object Type Before Output
							 * 
							 * Checks if the search result is for the 'All Posts In Category'
							 * metabox and adds an argument to the $args array before the
							 * walker outputs the items.
							 */
							if ( isset( $request['type'] ) && 'quick-search-taxonomy-category-custom-category' == $request['type'] ) {
								$args['custom_item_type'] = 'category_posts';
							} 

							// Walk through the results and echo back to the client
							echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', array( $term ) ), 0, (object) $args );

						} elseif ( 'json' == $response_format ) {
							echo json_encode(
								array(
									'ID'         => $term->term_id,
									'post_title' => $term->name,
									'post_type'  => $matches[2]
								)
							);
							echo "\n";
						}
					}
				}
			} elseif ('quick-search-author-archive' == $type ) {
				$this->author_quick_search( $request );
			}			
		}

		/**
		 * Genereate Quick Search Response
		 *
		 * Takes the user input as an array parameter and generates
		 * a list of posts/taxonomies based on the result.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/post_type_exists 			post_type_exists()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post 					get_post()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post_type 			get_post_type()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_the_title 			get_the_title()
		 * @link 	http://codex.wordpress.org/Function_Reference/taxonomy_exists 			taxonomy_exists()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_term 					get_term()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_terms					get_terms()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post_type_object		get_post_type_object()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_the_ID 				get_the_ID()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_the_title				get_the_title()
		 * @link 	http://codex.wordpress.org/Function_Reference/have_posts				have_posts()
		 * @link 	http://codex.wordpress.org/Function_Reference/the_post					the_post()
		 * 
		 * @uses 	class Master_Walker_Sidebar_Checklist 	defined in includes/classes/class-master-walker-sidebar-edit.php
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function author_quick_search( $request = array() ) {
			if ( ! empty( $request ) && isset( $request['q'] ) ) {

				// Define query args for author query 
				$roles = array( 'Administrator', 'Editor', 'Author' ); 
				$db_id = -9999;

				// Get all users that have author priviledges
				foreach ( $roles as $role ) {

					$search_query = $request['q'];

					$args = array(
						'search_columns' => array( 'ID', 'user_login', 'user_nicename', 'user_email' ),
						'role'           => $role,
					);
			
					// The Query
					$user_query = new WP_User_Query( $args );

					// Output search results
					if ( ! empty( $user_query->results ) ) {
						foreach ( $user_query->results as $user ) {
							if ( false !== stripos( $user->data->display_name, $search_query ) ) {
								?>
								<li>
									<label class="menu-item-title">
										<input type="checkbox" value ="1" name="menu-item[<?php echo $db_id; ?>][menu-item-object-id]" class="menu-item-checkbox"> <?php echo $user->data->display_name; ?>
									</label>
									<input class="menu-item-db-id"      type="hidden" value="0"                                        name="menu-item[<?php echo $db_id; ?>][menu-item-db-id]">
									<input class="menu-item-object"     type="hidden" value="<?php echo $user->data->ID; ?>"           name="menu-item[<?php echo $db_id; ?>][menu-item-object]">
									<input class="menu-item-parent-id"  type="hidden" value="0"                                        name="menu-item[<?php echo $db_id; ?>][menu-item-parent-id]">
									<input class="menu-item-type"       type="hidden" value="author_archive"                           name="menu-item[<?php echo $db_id; ?>][menu-item-type]">
									<input class="menu-item-title"      type="hidden" value="<?php echo $user->data->display_name; ?>" name="menu-item[<?php echo $db_id; ?>][menu-item-title]">
									<input class="menu-item-url"        type="hidden" value="<?php echo $user->data->user_url; ?>"     name="menu-item[<?php echo $db_id; ?>][menu-item-url]">
								</li>						
								<?php
							}

							$db_id++;
						}
					}
				}
			} // endif
		}
	}
endif;
