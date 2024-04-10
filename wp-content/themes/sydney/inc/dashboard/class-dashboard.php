<?php
/**
 *
 * Dashboard
 * @package Dashboard
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Dashboard class.
 */
class Sydney_Dashboard
{

    /**
     * The settings of page.
     *
     * @var array $settings The settings.
     */
    public $settings = array();

    /**
     * Constructor.
     */
    public function __construct()
    {

        if (defined('SYDNEY_AWL_ACTIVE')) {
            return;
        }

        if( ! is_admin() ) {
            return;
        }

        if( $this->is_themes_page() ) {
            add_action('init', array($this, 'set_settings'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        }

        if( $this->is_sydney_dashboard_page() ) {
            add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );

            if( defined( 'SYDNEY_PRO_VERSION' ) ) {
                add_action( 'admin_footer', 'sydney_templates_display_conditions_script_template' );
            }
        }

        add_filter('woocommerce_enable_setup_wizard', '__return_false');


        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_notices', array($this, 'html_notice'));
        
        add_action('wp_ajax_sydney_notifications_read', array($this, 'ajax_notifications_read'));

        add_action('wp_ajax_sydney_plugin', array($this, 'ajax_plugin'));
        add_action('wp_ajax_sydney_dismissed_handler', array($this, 'ajax_dismissed_handler'));

        add_action( 'wp_ajax_sydney_module_activation_handler', array( $this, 'ajax_module_activation_handler' ) );
        add_action( 'wp_ajax_sydney_module_activation_all_handler', array( $this, 'ajax_module_activation_all_handler' ) );
        add_action( 'wp_ajax_sydney_template_builder_data', array( $this, 'ajax_template_builder_data' ) );
        add_action( 'wp_ajax_insert_template_part_callback', array( $this, 'insert_template_part_callback' ) );
        add_action( 'wp_ajax_edit_template_part_callback', array( $this, 'edit_template_part_callback' ) );

        add_action('switch_theme', array($this, 'reset_notices'));
        add_action('after_switch_theme', array($this, 'reset_notices'));
    }

    /**
     * Check if is the themes.php page
     * 
     */
    public function is_themes_page() {
        global $pagenow;
        return $pagenow === 'themes.php';
    }

    /**
     * Check if is the theme dashboard page
     * 
     */
    public function is_sydney_dashboard_page() {
        global $pagenow;
        return $pagenow === 'themes.php' && ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'sydney-dashboard' );
    }

    /**
     * Settings
     *
     * @param array $settings The settings.
     */
    public function set_settings()
    {
        $this->settings = apply_filters('sydney_dashboard_settings', array());
    }

    /**
     * Add menu page
     */
    public function add_menu_page()
    {

        add_submenu_page('themes.php', esc_html__('Theme Dashboard', 'sydney'), esc_html__('Theme Dashboard', 'sydney'), 'manage_options', isset( $this->settings['menu_slug'] ) ? $this->settings['menu_slug'] : 'sydney-dashboard', array($this, 'html_dashboard'), 1); // phpcs:ignore WPThemeReview.PluginTerritory.NoAddAdminPages.add_menu_pages_add_submenu_page
    }

    /**
     * This function will register scripts and styles for admin dashboard.
     *
     * @param string $page Current page.
     */
    public function admin_enqueue_scripts($hook)
    {

        wp_enqueue_style('sydney-dashboard', get_template_directory_uri() . '/inc/dashboard/assets/css/sydney-dashboard.min.css', array(), '20230525');

        if (is_rtl()) {
            wp_enqueue_style('sydney-dashboard-rtl', get_template_directory_uri() . '/inc/dashboard/assets/css/sydney-dashboard-rtl.min.css', array(), '20230525');
        }

        wp_enqueue_script('sydney-dashboard', get_template_directory_uri() . '/inc/dashboard/assets/js/sydney-dashboard.min.js', array('jquery', 'wp-util', 'jquery-ui-sortable' ), '20230525', true);

        wp_enqueue_script( 'sydney-select2-js', get_template_directory_uri() . '/inc/customizer/controls/typography/select2.full.min.js', array( 'jquery' ), '4.0.13', true );
		wp_enqueue_style( 'sydney-select2-css', get_template_directory_uri() . '/inc/customizer/controls/typography/select2.min.css', array(), '4.0.13', 'all' );

        wp_localize_script('sydney-dashboard', 'sydney_dashboard', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'nonce-bt-dashboard' ),
            'i18n' => array(
                'activate' => esc_html__('Activate', 'sydney'),
                'deactivate' => esc_html__('Deactivate', 'sydney'),
                'installing' => esc_html__('Installing...', 'sydney'),
                'activating' => esc_html__('Activating...', 'sydney'),
                'deactivating' => esc_html__('Deactivating...', 'sydney'),
                'loading' => esc_html__('Loading...', 'sydney'),
                'saving' => esc_html__('Saving...', 'sydney'),
                'saved' => esc_html__('Saved!', 'sydney'),
                'unsaved_changes' => esc_html__('You have unsaved changes.', 'sydney'),
                'save' => esc_html__('Save', 'sydney'),
                'redirecting' => esc_html__('Redirecting...', 'sydney'),
                'activated' => esc_html__('Activated', 'sydney'),
                'deactivated' => esc_html__('Deactivated', 'sydney'),
                'failed_message' => esc_html__('Something went wrong, contact support.', 'sydney'),
            ),
        ));
    }

    /**
     * Get plugin status.
     *
     * @param string $plugin_path Plugin path.
     */
    public function get_plugin_status($plugin_path)
    {

        if (!current_user_can('install_plugins')) {
            return;
        }

        if (!function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
        }

        if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin_path)) {
            return 'not_installed';
        } elseif (in_array($plugin_path, (array) get_option('active_plugins', array()), true) || is_plugin_active_for_network($plugin_path)) {
            return 'active';
        } else {
            return 'inactive';
        }

    }

    /**
     * Get plugin data.
     *
     * @param string $plugin_path Plugin path.
     */
    public function get_plugin_data($plugin_path)
    {

        if (!current_user_can('install_plugins')) {
            return;
        }

        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
        }

        return get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);

    }

    /**
     * Install a plugin.
     *
     * @param string $plugin_slug Plugin slug.
     */
    public function install_plugin($plugin_slug)
    {

        if (!current_user_can('install_plugins')) {
            return;
        }

        if (!function_exists('plugins_api')) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
        }
        if (!class_exists('WP_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
        }

        if (false === filter_var($plugin_slug, FILTER_VALIDATE_URL)) {
            $api = plugins_api(
                'plugin_information',
                array(
                    'slug' => $plugin_slug,
                    'fields' => array(
                        'short_description' => false,
                        'sections' => false,
                        'requires' => false,
                        'rating' => false,
                        'ratings' => false,
                        'downloaded' => false,
                        'last_updated' => false,
                        'added' => false,
                        'tags' => false,
                        'compatibility' => false,
                        'homepage' => false,
                        'donate_link' => false,
                    ),
                )
            );

            $download_link = $api->download_link;
        } else {
            $download_link = $plugin_slug;
        }

        // Use AJAX upgrader skin instead of plugin installer skin.
        // ref: function wp_ajax_install_plugin().
        $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());

        $install = $upgrader->install($download_link);

        if (false === $install) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Activate a plugin.
     *
     * @param string $plugin_path Plugin path.
     */
    public function activate_plugin($plugin_path)
    {

        if (!current_user_can('install_plugins')) {
            return false;
        }

        if (!function_exists('activate_plugin')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
        }

        $activate = activate_plugin($plugin_path, '', false, true);

        if (is_wp_error($activate)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Deactivate a plugin.
     *
     * @param string $plugin_path Plugin path.
     */
    public function deactivate_plugin($plugin_path)
    {

        if (!current_user_can('install_plugins')) {
            return false;
        }

        if (!function_exists('deactivate_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
        }

        $deactivate = deactivate_plugins($plugin_path);

        if (is_wp_error($deactivate)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Ajax notifications.
     */
    public function ajax_notifications_read() {
        check_ajax_referer( 'nonce-bt-dashboard', 'nonce' );

        $latest_notification_date = ( isset( $_POST[ 'latest_notification_date' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'latest_notification_date' ] ) ) : false;
        update_user_meta( get_current_user_id(), 'sydney_dashboard_notifications_latest_read', $latest_notification_date );

        wp_send_json_success();
    }

     /**
     * Admin Footer Text
     */
    public function admin_footer_text() {
        $text = sprintf(
			/* translators: %s: https://wordpress.org/ */
			__( 'Thank you for creating your website with <a href="%s" class="sydney-dashboard-footer-link" target="_blank">Sydney</a>.', 'sydney' ),
			'https://athemes.com/theme/sydney/'
		);

        return $text;
    }

    /**
     * Check if the latest notification is read
     */
    public function latest_notification_is_read() {
        if( ! isset( $this->settings[ 'notifications' ] ) || empty( $this->settings[ 'notifications' ] ) ) {
            return false;
        }
        
        $user_id                     = get_current_user_id();
        $user_read_meta              = get_user_meta( $user_id, 'sydney_dashboard_notifications_latest_read', true );

        $last_notification_date      = strtotime( is_string( $this->settings[ 'notifications' ][0]->post_date ) ? $this->settings[ 'notifications' ][0]->post_date : '' );
        $last_notification_date_ondb = $user_read_meta ? strtotime( $user_read_meta ) : false;

        if( ! $last_notification_date_ondb ) {
            return false;
        }

        if( $last_notification_date > $last_notification_date_ondb ) {
            return false;
        }

        return true;
    }

    /**
     * Ajax plugin.
     */
    public function ajax_plugin() {
        check_ajax_referer( 'nonce-bt-dashboard', 'nonce' );

        $plugin_type = (isset($_POST['type'])) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';
        $plugin_slug = (isset($_POST['slug'])) ? sanitize_text_field(wp_unslash($_POST['slug'])) : '';
        $plugin_path = (isset($_POST['path'])) ? sanitize_text_field(wp_unslash($_POST['path'])) : '';

        if ( ! current_user_can('install_plugins') || empty($plugin_slug) || empty($plugin_type) ) {
            wp_send_json_error( esc_html__( 'Insufficient permissions to install the plugin.', 'sydney' ) );
        }

        if ($plugin_type === 'install' || $plugin_type === 'activate') {

            if ('not_installed' === $this->get_plugin_status($plugin_path)) {

                $this->install_plugin($plugin_slug);
                $this->activate_plugin($plugin_path);

            } else if ('inactive' === $this->get_plugin_status($plugin_path)) {

                $this->activate_plugin($plugin_path);

            }

            if ('active' === $this->get_plugin_status($plugin_path)) {
                wp_send_json_success();
            }

        } else if ($plugin_type === 'deactivate') {

            $this->deactivate_plugin($plugin_path);

            if ('inactive' === $this->get_plugin_status($plugin_path)) {
                wp_send_json_success();
            }

        }

        wp_send_json_error(esc_html__('Failed to initialize or activate importer plugin.', 'sydney'));
    }

    /**
     * Dismissed handler
     */
    public function ajax_dismissed_handler() {
        check_ajax_referer( 'nonce-bt-dashboard', 'nonce' );

        if (isset($_POST['notice'])) {
            set_transient(sanitize_text_field(wp_unslash($_POST['notice'])), true, 0);
            wp_send_json_success();
        }

        wp_send_json_error();

    }

    /**
     * Purified from the database information about notification.
     */
    public function reset_notices() {
        delete_transient(sprintf('%s_hero_notice', get_template()));
    }

    /**
     * Activate/Deactivate Module Ajax
     */
    public function ajax_module_activation_handler() {
        check_ajax_referer( 'nonce-bt-dashboard', 'nonce' );

        if( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        $module   = ( isset( $_POST[ 'module' ] ) ) ? sanitize_text_field( wp_unslash( $_POST['module'] ) ) : '';
        $activate = ( isset( $_POST[ 'activate' ] ) ) ? sanitize_text_field( wp_unslash( $_POST['activate'] ) ) : '';

        // Convert string to boolean
        $activate = ( $activate === 'true' ) ? true : false;

        if ( empty( $module ) ) {
            wp_send_json_error();
        }

        $modules = get_option( 'sydney-modules', array() );
        $modules[ $module ] = $activate;

        update_option( 'sydney-modules', $modules );

        wp_send_json_success();
    }

    /**
     * Activate/Deactivate All Modules Ajax
     */
    public function ajax_module_activation_all_handler() {
        check_ajax_referer( 'nonce-bt-dashboard', 'nonce' );

        if( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        $activate = ( isset( $_POST[ 'activate' ] ) ) ? sanitize_text_field( wp_unslash( $_POST['activate'] ) ) : '';

        // Convert string to boolean
        $activate = ( $activate === 'true' ) ? true : false;

        // Get a list with all modules id's
        $all_modules_ids = sydney_get_modules_ids();

        // Get current modules active/disabled list
        $current_modules = get_option( 'sydney-modules', array() );

        $modules = array();
        foreach( $all_modules_ids as $module_id ) {

            // Skip some modules
            if( in_array( $module_id, array( 'hf-builder', 'schema-markup', 'adobe-typekit' ) ) ) {
                $modules[ $module_id ] = $current_modules[ $module_id ];
            } else {
                $modules[ $module_id ] = $activate;
            }

        }

        // Update modules option
        update_option( 'sydney-modules', $modules );

        wp_send_json_success();
    }

    /**
     * Templates builder
     */
    function ajax_template_builder_data() {
        check_ajax_referer('nonce-bt-dashboard', 'nonce');

        if( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }
    
        $data = $_POST[ 'data' ];

        $data = stripslashes_deep($data);
    
        update_option('sydney_template_builder_data', $data);
    
        wp_send_json_success($data);
    }    

	/**
	 * Get option text
	 */
	function get_option_text( $value ) {

		switch ( $value['condition'] ) {

			case 'post-id':
			case 'page-id':
			case 'product-id':
			case 'cpt-post-id':
				return get_the_title( $value['id'] );
			break;

			case 'tag-id':
			case 'category-id':
			
        $term = get_term( $value['id'] );

        if ( ! empty( $term ) ) {
					return $term->name;
        }

			break;

			case 'cpt-term-id':
			
        $term = get_term( $value['id'] );
        
        if ( ! empty( $term ) ) {
					return $term->name;
        }

			break;

			case 'cpt-taxonomy-id':
			
        $taxonomy = get_taxonomy( $value['id'] );
        
        if ( ! empty( $taxonomy ) ) {
					return $taxonomy->label;
        }

			break;

			case 'author':
			case 'author-id':
				return get_the_author_meta( 'display_name', $value['id'] );
			break;

		}

		// user-roles
		if ( substr( $value['condition'], 0, 10 ) === 'user_role_' ) {
			$user_rules = get_editable_roles();
			if ( ! empty( $user_rules[ $value['id'] ] ) ) {
				return $user_rules[ $value['id'] ]['name'];
			}
		}

		return $value['id'];

	}    

    /**
     * Insert a new template
     */
    function insert_template_part_callback() {

        check_ajax_referer('nonce-bt-dashboard', 'nonce');

		if ( ! isset( $_POST['key'] ) ) {
			wp_send_json_error();
		}

		$post_name      = sanitize_text_field( wp_unslash( $_POST['key'] ) ) . '-' . sanitize_text_field( wp_unslash( $_POST['part_type'] ) );
        $page_builder   = sanitize_text_field( wp_unslash( $_POST['page_builder'] ) );

		$post_title = '';
		$args       = array(
			'post_type'              => 'athemes_hf',
			'name'                   => $post_name,
			'post_status'            => 'publish',
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'posts_per_page'         => 1,
		);

		$post = get_posts( $args );

		if ( empty( $post ) ) {

			$key            = sanitize_text_field( wp_unslash( $_POST['key'] ) );

			$post_title     = 'Sydney Template Part - ' . str_replace( 'sydney-template-', '', $key ) . '-' . sanitize_text_field( wp_unslash( $_POST['part_type'] ) );

			$params = array(
				'post_content' => '',
				'post_type'    => 'athemes_hf',
				'post_title'   => $post_title,
				'post_name'    => $post_name,
				'post_status'  => 'publish',
			);

            if( $page_builder == 'elementor' ) {
                $params['meta_input'] = array(
                    '_elementor_edit_mode' => 'builder',
                    '_wp_page_template'    => 'elementor_canvas',
                );
            }

			$post_id = wp_insert_post( $params );

		} else { // edit post.
			$post_id    = $post[0]->ID;
			$post_title = $post[0]->post_title;
		}

        $action = $page_builder == 'elementor' ? 'elementor' : 'edit';

		$edit_url = get_admin_url() . 'post.php?post=' . $post_id . '&action=' . $action;

		$result = array(
			'url'   => $edit_url,
			'id'    => $post_id,
			'title' => $post_title,
		);

		wp_send_json_success( $result );
	}    

    /**
     * Edit template
     */
    function edit_template_part_callback() {
            
        check_ajax_referer('nonce-bt-dashboard', 'nonce');

        if ( ! isset( $_POST['key'] ) ) {
            wp_send_json_error();
        }

        $post_id = sanitize_text_field( wp_unslash( $_POST['key'] ) );

        $post = get_post( $post_id );

        if ( empty( $post ) ) {
            wp_send_json_error();
        }

        $action = 'edit';

        if( class_exists( 'Elementor\Plugin' ) && Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor() ) {
            $action = 'elementor';
        }

        $edit_url = get_admin_url() . 'post.php?post=' . $post_id . '&action=' . $action;

        $result = array(
            'url'   => $edit_url,
            'id'    => $post_id,
            'title' => $post->post_title,
        );

        wp_send_json_success( $result );
    }

    /**
     * Get athemes templates CPT
     */
    function get_template_parts() {
        $args = array(
            'numberposts' 	=> -1,
            'post_type'   	=> 'athemes_hf',
        );	

        $posts = get_posts( $args );

        $parts = array();

        if ( ! empty( $posts ) ) {
            foreach ( $posts as $post ) {
                $parts[ $post->ID ] = $post->post_title;
            }
        }

        return $parts;
    }

    /**
     * Existing parts select
     */
    function existing_parts_select( $parts = array() ) {

        $html = '<div class="existing-parts-wrapper">';
        
        if ( empty( $parts ) ) {
            $html .= '<div>' . esc_html__( 'No templates found.', 'sydney' ) . '</div>';
        } else {
            $html .= '<select class="existing-parts-select">';
            $html .= '<option value="">' . esc_html__( 'Select existing', 'sydney' ) . '</option>';
    
            foreach ( $parts as $id => $title ) {
    
                $page_builder = 'editor';
                if ( class_exists( 'Elementor\Plugin' ) && Elementor\Plugin::$instance->documents->get( $id )->is_built_with_elementor() ) {
                    $page_builder = 'elementor';
                }
                
                $html .= '<option data-page-builder="' . $page_builder . '" value="' . $id . '">' . $title . '</option>';
            }
    
            $html .= '</select>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * HTML Dashboard
     */
    public function html_dashboard() {
        $user_id             = get_current_user_id();
        $current_user        = wp_get_current_user();
        $notification_read   = $this->latest_notification_is_read();
        $notifications_count = 1;

        $theme_slug     = get_template();
        $theme_version  = wp_get_theme()->get( 'Version' );

		?>
      	<div class="sydney-dashboard sydney-dashboard-wrap">
			<div class="sydney-dashboard-top-bar">
				<a href="<?php echo esc_url($this->settings['upgrade_pro']); ?>" class="sydney-dashboard-top-bar-logo" target="_blank">
					<svg width="96" height="24" viewBox="0 0 96 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M23.4693 1.32313L8.45381 14.3107L0.67962 4.82163L23.4693 1.32313Z" fill="#335EEA"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M23.2942 1.17329L8.23868 14.112L16.0129 23.601L23.2942 1.17329Z" fill="#BECCF9"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M54.4276 12.8764C54.4276 10.7582 52.94 9.55047 51.2709 9.55047C49.6019 9.55047 48.8399 10.8325 48.8399 10.8325V4.53369H46.6629V18.5807H48.8399V12.7835C48.8399 12.7835 49.4205 11.6315 50.5453 11.6315C51.4886 11.6315 52.2506 12.1703 52.2506 13.4338V18.5807H54.4276V12.8764ZM39.9463 18.5807V7.6924H36.4449V5.57421H45.6247V7.6924H42.1233V18.5807H39.9463ZM36.604 12.8392C36.604 10.8325 35.1527 9.55047 32.6854 9.55047C30.8894 9.55047 29.2929 10.3494 29.2929 10.3494L30.0004 12.1889C30.0004 12.1889 31.3248 11.5386 32.5766 11.5386C33.3385 11.5386 34.427 11.9102 34.427 13.0622V13.5639C34.427 13.5639 33.6107 12.9321 32.0323 12.9321C30.1637 12.9321 28.658 14.1585 28.658 15.8493C28.658 17.7259 30.1456 18.8036 31.7602 18.8036C33.7014 18.8036 34.5903 17.4658 34.5903 17.4658V18.5807H36.604V12.8392ZM34.427 15.9236C34.427 15.9236 33.7376 16.9456 32.4314 16.9456C31.7602 16.9456 30.8713 16.7412 30.8713 15.8121C30.8713 14.8645 31.7965 14.5672 32.4677 14.5672C33.6469 14.5672 34.427 15.0132 34.427 15.0132V15.9236ZM59.7836 9.55047C62.142 9.55047 64.1195 11.4271 64.1195 14.1399C64.1195 14.3071 64.1195 14.6416 64.1013 14.976H57.6791C57.8424 15.7564 58.7314 16.7598 60.092 16.7598C61.5978 16.7598 62.4504 15.8679 62.4504 15.8679L63.5389 17.5401C63.5389 17.5401 62.1783 18.8036 60.092 18.8036C57.4796 18.8036 55.4659 16.7598 55.4659 14.177C55.4659 11.5943 57.2982 9.55047 59.7836 9.55047ZM61.9425 13.3595H57.6792C57.7517 12.5791 58.3867 11.5758 59.7836 11.5758C61.2168 11.5758 61.9062 12.5977 61.9425 13.3595ZM72.3963 11.0926C72.3987 11.0875 73.1253 9.55047 75.1357 9.55047C76.8773 9.55047 78.1472 10.7954 78.1472 12.9136V18.5807H75.9702V13.4896C75.9702 12.2818 75.5167 11.6315 74.4282 11.6315C73.2852 11.6315 72.741 12.7649 72.741 12.7649V18.5807H70.564V13.4896C70.564 12.2818 70.1104 11.6315 69.0219 11.6315C67.879 11.6315 67.3347 12.7649 67.3347 12.7649V18.5807H65.1577V9.77343H67.1896V11.0555C67.1896 11.0555 67.9697 9.55047 69.7294 9.55047C71.7947 9.55047 72.3946 11.0884 72.3963 11.0926ZM87.8391 14.1399C87.8391 11.4271 85.8616 9.55047 83.5032 9.55047C81.0178 9.55047 79.1855 11.5943 79.1855 14.177C79.1855 16.7598 81.1992 18.8036 83.8116 18.8036C85.8979 18.8036 87.2585 17.5401 87.2585 17.5401L86.17 15.8679C86.17 15.8679 85.3174 16.7598 83.8116 16.7598C82.451 16.7598 81.562 15.7564 81.3988 14.976H87.8209C87.8391 14.6416 87.8391 14.3071 87.8391 14.1399ZM81.3988 13.3595H85.6621C85.6258 12.5977 84.9364 11.5758 83.5032 11.5758C82.1063 11.5758 81.4713 12.5791 81.3988 13.3595ZM89.5486 15.5892L88.3331 17.2057C88.3331 17.2057 89.6937 18.8036 92.2154 18.8036C94.4106 18.8036 95.9708 17.5959 95.9708 16.0909C95.9708 14.2699 94.7553 13.6939 93.0499 13.3223C91.5986 13.0065 91.0181 12.8764 91.0181 12.3376C91.0181 11.7987 91.7619 11.5014 92.5783 11.5014C93.7393 11.5014 94.719 12.2632 94.719 12.2632L95.8075 10.6281C95.8075 10.6281 94.5194 9.55047 92.5783 9.55047C90.2198 9.55047 88.8773 10.9254 88.8773 12.3004C88.8773 13.9727 90.365 14.7159 92.0703 15.0875C93.3765 15.3662 93.7756 15.4777 93.7756 16.0537C93.7756 16.5925 93.0318 16.8527 92.1429 16.8527C90.6915 16.8527 89.5486 15.5892 89.5486 15.5892Z" fill="#101517"/>
					</svg>
				</a>
				<div class="sydney-dashboard-top-bar-infos">
					<div class="sydney-dashboard-top-bar-info-item">
						<div class="sydney-dashboard-theme-version">
							<strong><?php echo esc_html( $theme_version ); ?></strong>
						</div>
					</div>
                    <div class="sydney-dashboard-top-bar-info-item">
						<a href="#" class="sydney-dashboard-theme-notifications<?php echo ( $notification_read ) ? ' read' : ''; ?>" title="<?php echo esc_attr__( 'Theme News', 'sydney' ); ?>">
                            <span class="sydney-dashboard-notifications-count"><?php echo absint( $notifications_count ); ?></span>
                            <svg width="13" height="11" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.86194 0.131242C8.75503 0.0584347 8.63276 0.0143876 8.50589 0.0029728C8.37902 -0.00844195 8.25143 0.0131252 8.13433 0.0657785L4.29726 1.65655C4.20642 1.69547 4.10927 1.71548 4.01119 1.71547H1.55473C1.34856 1.71547 1.15083 1.80168 1.00505 1.95514C0.859264 2.1086 0.777363 2.31674 0.777363 2.53377V2.59923H0V4.56315H0.777363V4.64825C0.782235 4.86185 0.866281 5.06498 1.01154 5.21422C1.1568 5.36346 1.35175 5.44697 1.55473 5.44691L2.48756 7.52866C2.55073 7.66885 2.65017 7.78744 2.77448 7.87081C2.89878 7.95418 3.04291 7.99896 3.1903 8H3.58209C3.78718 7.99827 3.98331 7.9113 4.12775 7.75802C4.27219 7.60475 4.35324 7.3976 4.35323 7.1817V5.52547L8.13433 7.11624C8.22733 7.1552 8.32652 7.17519 8.42662 7.17515C8.58191 7.17252 8.73314 7.12249 8.86194 7.03114C8.96423 6.95843 9.0486 6.86114 9.10808 6.7473C9.16755 6.63347 9.20043 6.50636 9.20398 6.3765V0.80552C9.20341 0.672312 9.17196 0.541263 9.11235 0.423757C9.05274 0.30625 8.96678 0.205839 8.86194 0.131242ZM3.57587 2.53377V4.64825H1.55473V2.53377H3.57587ZM3.57587 7.1817H3.18408L2.41915 5.44691H3.57587V7.1817ZM4.58333 4.74645C4.5095 4.70672 4.4325 4.67387 4.35323 4.64825V2.48794C4.43174 2.47089 4.50872 2.4468 4.58333 2.41593L8.42662 0.80552V6.35686L4.58333 4.74645ZM9.22264 2.76289V4.39949C9.42881 4.39949 9.62653 4.31327 9.77232 4.15981C9.9181 4.00635 10 3.79821 10 3.58119C10 3.36416 9.9181 3.15602 9.77232 3.00256C9.62653 2.8491 9.42881 2.76289 9.22264 2.76289Z" fill="#1E1E1E"/>
                            </svg>
						</a>
					</div>
					<div class="sydney-dashboard-top-bar-info-item">
                        <?php $link = $this->settings[ 'has_pro' ] ? $this->settings['website_link'] : $this->settings['upgrade_pro']; ?>

						<a href="<?php echo esc_url( $link ); ?>" class="sydney-dashboard-theme-website" target="_blank">
							<?php echo esc_html__( 'Website', 'sydney' ); ?>
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M13.6 2.40002H7.20002L8.00002 4.00002H11.264L6.39202 8.88002L7.52002 10.008L12 5.53602V8.00002L13.6 8.80002V2.40002ZM9.60002 9.60002V12H4.00002V6.40002H7.20002L8.80002 4.80002H2.40002V13.6H11.2V8.00002L9.60002 9.60002Z" fill="#2271b1"/>
							</svg>
						</a>
					</div>
				</div>
			</div>

            <?php require get_template_directory() . '/inc/dashboard/html-notifications-sidebar.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound ?>
        
			<div class="sydney-dashboard-container" data-theme="<?php echo esc_attr( $theme_slug ); ?>">
                <?php require get_template_directory() . '/inc/dashboard/html-hero.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound ?>

				<div class="sydney-dashboard-row bt-p-relative bt-zindex-2">
					<div class="sydney-dashboard-column">
						<?php require get_template_directory() . '/inc/dashboard/html-tabs-nav-items.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound ?>
					</div>
				</div>
				<div class="sydney-dashboard-row">
					<div class="sydney-dashboard-column">
						<?php 
						$section = ( isset( $_GET['tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';

						foreach( $this->settings[ 'tabs' ] as $tab_id => $tab_title ) : 
							$tab_active = (($tab && $tab === $tab_id) || (!$section && $tab_id === 'home')) ? ' active' : '';

							?>	
                            <div class="sydney-dashboard-tab-content-wrapper" data-tab-wrapper-id="main">					
                                <div class="sydney-dashboard-tab-content<?php echo esc_attr( $tab_active ); ?>" data-tab-content-id="<?php echo esc_attr( $tab_id ); ?>">
                                    <?php require get_template_directory() . '/inc/dashboard/html-'. $tab_id .'.php'; ?>
                                </div>
                            </div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
      	</div>
    <?php
	}

    /**
     * HTML Notice
     */
    public function html_notice()
    {

        global $pagenow;

        $screen = get_current_screen();

        if ('themes.php' === $pagenow && 'themes' === $screen->base) {

            $transient = sprintf('%s_hero_notice', get_template());

            if (!get_transient($transient)) {
                ?>
          <div class="sydney-dashboard sydney-dashboard-notice">
            <div class="sydney-dashboard-dismissable dashicons dashicons-dismiss" data-notice="<?php echo esc_attr($transient); ?>"></div>
            <?php require get_template_directory() . '/inc/dashboard/html-hero.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound ?>
          </div>
        <?php
}

        }

    }

}

new Sydney_Dashboard();
