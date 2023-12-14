<?php
/*
 * MetaSlider. Slideshow plugin for WordPress.
 *
 * Plugin Name: MetaSlider
 * Plugin URI:  https://www.metaslider.com
 * Description: MetaSlider gives you the power to create a beautiful slideshow, carousel, or gallery on your WordPress site.
 * Version:     3.60.1
 * Author:      MetaSlider
 * Author URI:  https://www.metaslider.com
 * License:     GPL-2.0+
 * Copyright:   2023 - MetaSlider LLC
 *
 * Text Domain: ml-slider
 * Domain Path: /languages
 */

if (! defined('ABSPATH')) {
    die('No direct access.');
}

if (! class_exists('MetaSliderPlugin')) {
    if (! defined('METASLIDER_COMPOSER_AUTOLOAD_LOADED')) {
        if (is_readable(__DIR__ . '/vendor/autoload.php')) {
            require_once __DIR__ . '/vendor/autoload.php';
        }

        define('METASLIDER_COMPOSER_AUTOLOAD_LOADED', true);
    }

    /**
     * Register the plugin.
     *
     * Display the administration panel, insert JavaScript etc.
     */
    class MetaSliderPlugin
    {
        const DEFAULT_CAPABILITY_EDIT_SLIDES = 'edit_others_posts';

        /**
         * MetaSlider version number
         *
         * @var string
         */
        public $version = '3.60.1';

        /**
         * Pro installed version number
         *
         * @var string
         */
        public $installed_pro_version = '';

        /**
         * Specific Slider
         *
         * @var MetaSlider
         */
        public $slider = null;

        /**
         * Instance object
         *
         * @var object
         * @see get_instance()
         */
        protected static $instance = null;

        /**
         * @var MetaSlider_Admin_Pages
         */
        public $admin = null;

        /**
         * @var \MetaSlider_Themes|object|null
         */
        public $themes;

        /**
         * @var \MetaSlider_Api|object|null
         */
        public $api;

        /**
         * @var \MetaSlider_Gutenberg
         */
        public $gutenberg;

        /**
         * Constructor
         */
        public function __construct()
        {
        }

        /**
         * Used to access the instance
         *
         * @return object - class instance
         */
        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Setup
         */
        public function setup()
        {
            define('METASLIDER_PATH', plugin_dir_path(__FILE__));

            if (! class_exists('MetaSlider_Settings')) {
                require_once METASLIDER_PATH . 'admin/support/Settings.php';
            }

            require_once(METASLIDER_PATH . 'admin/lib/helpers.php');

            $this->define_constants();
            spl_autoload_register(array($this, 'autoload'));
            $this->setup_actions();
            $this->setup_filters();
            $this->setup_shortcode();
            $this->check_dependencies();

            // Load in slideshow admin relates classes.
            $this->register_slide_types();
            if (is_admin()) {
                $this->admin = new MetaSlider_Admin_Pages($this);
            }

            // Load in slideshow related classes
            require_once(METASLIDER_PATH . 'admin/slideshows/bootstrap.php');
            $this->themes = MetaSlider_Themes::get_instance();

            // Default to WP (4.4) REST API but backup with admin ajax
            require_once(METASLIDER_PATH . 'admin/routes/api.php');
            $this->api = MetaSlider_Api::get_instance();
            $this->api->setup();
            $this->api->register_admin_ajax_hooks();
            if (class_exists('WP_REST_Controller')) {
                new MetaSlider_REST_Controller();
            }

            if (function_exists('register_block_type')) {
                $this->gutenberg = new MetaSlider_Gutenberg($this);
            }

            $capability = apply_filters('metaslider_capability', self::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (is_admin() && current_user_can($capability)) {
                $analytics = new MetaSlider_Analytics();
                $analytics->load();
            }

            // require_once(METASLIDER_PATH . 'admin/lib/temporary.php');
            // require_once(METASLIDER_PATH . 'admin/lib/callout.php');
        }

        /**
         * Define MetaSlider constants
         */
        private function define_constants()
        {
            if (! defined('METASLIDER_VERSION')) {
                $assets_version = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? uniqid() : $this->version;

                define('METASLIDER_VERSION', $this->version);
                define('METASLIDER_ASSETS_VERSION', $assets_version);
                define('METASLIDER_BASE_URL', plugin_dir_url(metaslider_plugin_is_installed('ml-slider')));
                define('METASLIDER_ASSETS_URL', METASLIDER_BASE_URL . 'assets/');
                define('METASLIDER_ADMIN_URL', METASLIDER_BASE_URL . 'admin/');
                define('METASLIDER_ADMIN_ASSETS_URL', METASLIDER_ADMIN_URL . 'assets/');

                define('METASLIDER_THEMES_PATH', METASLIDER_PATH . 'themes/');
                define('METASLIDER_THEMES_URL', METASLIDER_BASE_URL . 'themes/');
            }
        }

        /**
         * All MetaSlider classes
         */
        private function plugin_classes()
        {
            return array(
                'metaslider' => METASLIDER_PATH . 'inc/slider/metaslider.class.php',
                'metacoinslider' => METASLIDER_PATH . 'inc/slider/metaslider.coin.class.php',
                'metaflexslider' => METASLIDER_PATH . 'inc/slider/metaslider.flex.class.php',
                'metanivoslider' => METASLIDER_PATH . 'inc/slider/metaslider.nivo.class.php',
                'metaresponsiveslider' => METASLIDER_PATH . 'inc/slider/metaslider.responsive.class.php',
                'metaslide' => METASLIDER_PATH . 'inc/slide/metaslide.class.php',
                'metaimageslide' => METASLIDER_PATH . 'inc/slide/metaslide.image.class.php',
                'metasliderimagehelper' => METASLIDER_PATH . 'inc/metaslider.imagehelper.class.php',
                'metaslidersystemcheck' => METASLIDER_PATH . 'inc/metaslider.systemcheck.class.php',
                'metaslider_widget' => METASLIDER_PATH . 'inc/metaslider.widget.class.php',
                'simple_html_dom' => METASLIDER_PATH . 'inc/simple_html_dom.php',
                'metaslider_notices' => METASLIDER_PATH . 'admin/Notices.php',
                'metaslider_admin_pages' => METASLIDER_PATH . 'admin/Pages.php',
                'metaslider_admin_table' => METASLIDER_PATH . 'admin/Table.php',
                'metaslider_slideshows' => METASLIDER_PATH . 'admin/Slideshows/Slideshows.php',
                'metaslider_settings' => METASLIDER_PATH . 'admin/Slideshows/Settings.php',
                'metaslider_slide' => METASLIDER_PATH . 'admin/Slideshows/slides/Slide.php',
                'metaslider_themes' => METASLIDER_PATH . 'admin/Slideshows/Themes.php',
                'metaslider_image' => METASLIDER_PATH . 'admin/Slideshows/Image.php',
                'metaslider_gutenberg' => METASLIDER_PATH . 'admin/Gutenberg.php',
                'metaslider_analytics' => METASLIDER_PATH . 'admin/support/Analytics.php'
            );
        }

        /**
         * Display a warning on the plugins page if a dependancy
         * is missing or a conflict might exist.
         *
         * @return void
         */
        public function check_dependencies()
        {
            // MetaSlider pro is active but pre 2.13.0 (2.13.0 includes its own notice system)
            $slug = metaslider_plugin_is_installed('ml-slider-pro');
            if (is_plugin_active($slug)) {
                $pro_data = get_file_data(trailingslashit(WP_PLUGIN_DIR) . $slug, array('Version' => 'Version'));
                $this->installed_pro_version = $pro_data['Version'];
                if ($this->installed_pro_version && version_compare($this->installed_pro_version, '2.13.0', '<')) {
                    add_action('admin_notices', array($this, 'show_pro_is_outdated'), 10, 3);
                }
            }
        }

        /**
         * The warning message that is displayed
         *
         * @return void
         */
        public function show_pro_is_outdated()
        {
            global $pagenow;
            $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
            if ('update-core.php' !== $pagenow && 'plugins.php' !== $pagenow && 'metaslider' !== $page) {
                return;
            } ?>
            <div class='notice notice-error metaslider-pro-outdated-notice'>
                <p>
                    <?php
                    printf(
                        esc_html__(
                            'MetaSlider Pro is installed but is out of date. You may update it %shere%s. Learn more about this notice %shere%s',
                            'ml-slider'
                        ),
                        '<a href="' . esc_url(self_admin_url('plugins.php')) . '">',
                        '</a>',
                        '<a target="_blank" href="https://www.metaslider.com/pro-is-installed-but-out-of-date">',
                        '</a>'
                    );
                    ?>
                </p>
            </div>
            <?php
        }

        /**
         * Autoload MetaSlider classes to reduce memory consumption
         *
         * @param string $class Class name
         */
        public function autoload($class)
        {
            $classes = $this->plugin_classes();

            $class_name = strtolower($class);

            if (isset($classes[$class_name]) && is_readable($classes[$class_name])) {
                require_once($classes[$class_name]);
            }
        }


        /**
         * Register the [metaslider] shortcode.
         */
        private function setup_shortcode()
        {
            add_shortcode('metaslider', array($this, 'register_shortcode'));
            add_shortcode('ml-slider', array($this, 'register_shortcode')); // backwards compatibility
        }


        /**
         * Hook MetaSlider into WordPress
         */
        private function setup_actions()
        {
            add_action('admin_menu', array($this, 'register_admin_pages'), 9553);
            add_action('admin_bar_menu', array($this, 'add_edit_links'), 100);
            add_action('init', array($this, 'register_post_types'));
            add_action('init', array($this, 'register_taxonomy'));
            add_action('init', array($this, 'load_plugin_textdomain'));
            add_action('init', array($this, 'redirect_on_activate'));
            add_action('admin_footer', array($this, 'admin_footer'), 11);
            add_action('admin_footer', array($this, 'quickstart_params'), 11);
            add_action('widgets_init', array($this, 'register_metaslider_widget'));
            
            add_action('admin_post_metaslider_switch_view', array($this, 'switch_view'));
            add_action('admin_post_metaslider_delete_slide', array($this, 'delete_slide'));
            add_action('admin_post_metaslider_delete_slider', array($this, 'delete_slider'));
            add_action('admin_post_metaslider_create_slider', array($this, 'create_slider'));

            add_action('media_upload_vimeo', array($this, 'upgrade_to_pro_tab_vimeo'));
            add_action('media_upload_youtube', array($this, 'upgrade_to_pro_tab_youtube'));
            add_action('media_upload_post_feed', array($this, 'upgrade_to_pro_tab_post_feed'));
            add_action('media_upload_layer', array($this, 'upgrade_to_pro_tab_layer'));
            add_action('media_upload_external_url', array($this, 'upgrade_to_pro_tab_external_url'));
            add_action('media_upload_local_video', array($this, 'upgrade_to_pro_tab_local_video'));
            add_action('media_upload_external_video', array($this, 'upgrade_to_pro_tab_external_video'));

            // TODO: Refactor to Slide class object
            add_action('wp_ajax_delete_slide', array($this, 'ajax_delete_slide'));
            add_action('wp_ajax_undelete_slide', array($this, 'ajax_undelete_slide'));
            add_action('wp_ajax_permanent_delete_slide', array($this, 'ajax_permanent_delete_slide'));
            add_action('wp_ajax_quickstart_upload', array($this, 'ajax_quickstart_upload'));
            add_action('wp_ajax_quickstart_slideshow', array($this, 'ajax_quickstart_slideshow'));

            // Set date showing the first activation and redirect
            if (! get_option('ms_was_installed_on')) {
                update_option('ms_was_installed_on', time());
            }

            // New install (for legacy library)
            if (get_option('metaslider_new_user') == false) {
                add_option('metaslider_new_user', 'new');
            }
        }

        public function redirect_on_activate()
        {
          if (get_option('metaslider_activate')) {
              delete_option('metaslider_activate');
              if(!isset($_GET['activate-multi'])) {
                  wp_redirect(admin_url("admin.php?page=metaslider-start"));
                  exit;
              }
          }
        }


        /**
         * Hook MetaSlider into WordPress
         */
        private function setup_filters()
        {
            add_filter('media_upload_tabs', array($this, 'custom_media_upload_tab_name'), 998);
            add_filter('media_view_strings', array($this, 'custom_media_uploader_tabs'), 5);
            add_action('media_buttons', array($this, 'insert_metaslider_button'));
            add_filter("plugin_row_meta", array($this, 'get_extra_meta_links'), 10, 4);
            add_action('admin_head', array($this, 'add_star_styles'));

            // html5 compatibility for stylesheets enqueued within <body>
            add_filter('style_loader_tag', array($this, 'add_property_attribute_to_stylesheet_links'), 11, 2);
        }

        /**
         * Register MetaSlider widget
         */
        public function register_metaslider_widget()
        {
            register_widget('MetaSlider_Widget');
        }


        /**
         * Register ML Slider post type
         */
        public function register_post_types()
        {
            $show_ui = false;

            $capability = apply_filters('metaslider_capability', self::DEFAULT_CAPABILITY_EDIT_SLIDES);

            if (is_admin() && current_user_can($capability) && (isset($_GET['show_ui']) || defined(
                        "METASLIDER_DEBUG"
                    ) && METASLIDER_DEBUG)) {
                $show_ui = true;
            }

            register_post_type(
                'ml-slider',
                array(
                    'query_var' => false,
                    'rewrite' => false,
                    'public' => false,
                    'exclude_from_search' => true,
                    'publicly_queryable' => false,
                    'show_in_nav_menus' => false,
                    'show_ui' => $show_ui,
                    'labels' => array(
                        'name' => 'MetaSlider'
                    )
                )
            );

            register_post_type(
                'ml-slide',
                array(
                    'query_var' => false,
                    'rewrite' => false,
                    'public' => false,
                    'exclude_from_search' => true,
                    'publicly_queryable' => false,
                    'show_in_nav_menus' => false,
                    'show_ui' => $show_ui,
                    'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
                    'labels' => array(
                        'name' => 'Meta Slides'
                    )
                )
            );
        }


        /**
         * Register taxonomy to store slider => slides relationship
         */
        public function register_taxonomy()
        {
            $show_ui = false;

            $capability = apply_filters('metaslider_capability', self::DEFAULT_CAPABILITY_EDIT_SLIDES);

            if (is_admin() && current_user_can($capability) && (isset($_GET['show_ui']) || defined(
                        "METASLIDER_DEBUG"
                    ) && METASLIDER_DEBUG)) {
                $show_ui = true;
            }

            register_taxonomy(
                'ml-slider',
                array('attachment', 'ml-slide'),
                array(
                    'hierarchical' => true,
                    'public' => false,
                    'query_var' => false,
                    'rewrite' => false,
                    'show_ui' => $show_ui,
                    'label' => "Slider"
                )
            );
        }


        /**
         * Register our slide types
         */
        private function register_slide_types()
        {
            $image = new MetaImageSlide();
        }

        /**
         * Add the menu pages
         */
        public function register_admin_pages()
        {
            $title = metaslider_pro_is_active() ? 'MetaSlider Pro' : 'MetaSlider';

            $this->admin->add_page($title, 'metaslider');
            $this->admin->add_page(__('Home', 'ml-slider'), 'metaslider', 'metaslider');
            $this->admin->add_page(__('Quick Start', 'ml-slider'), 'metaslider-start', 'metaslider');
            $this->admin->add_page(__('Settings & Help', 'ml-slider'), 'metaslider-settings', 'metaslider');

            if (metaslider_user_sees_upgrade_page()) {
                $this->admin->add_page(__('Upgrade to Pro', 'ml-slider'), 'upgrade-metaslider', 'metaslider');
            }
        }

        /**
         * Add edit slideshow links to admin toolbar
         * 
         * @since 3.40
         */
        public function add_edit_links($admin_bar){
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $global_settings = get_option( 'metaslider_global_settings' );
            if (isset($global_settings['adminBar']) && false === $global_settings['adminBar']) {
                return;
            }

            $target = '_blank';
            if (is_admin()) {
                $target = '_self';
            }

            $logo = '<div id="metaslider-main-menu-icon" class="ab-item svg" style="background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyBmaWxsPSIjZmZmIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMjU1LjggMjU1LjgiIHN0eWxlPSJmaWxsOiNmZmYiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnPjxwYXRoIGQ9Ik0xMjcuOSwwQzU3LjMsMCwwLDU3LjMsMCwxMjcuOWMwLDcwLjYsNTcuMywxMjcuOSwxMjcuOSwxMjcuOWM3MC42LDAsMTI3LjktNTcuMywxMjcuOS0xMjcuOUMyNTUuOCw1Ny4zLDE5OC41LDAsMTI3LjksMHogTTE2LjQsMTc3LjFsOTIuNS0xMTcuNUwxMjQuMiw3OWwtNzcuMyw5OC4xSDE2LjR6IE0xNzAuNSwxNzcuMWwtMzguOS00OS40bDE1LjUtMTkuNmw1NC40LDY5SDE3MC41eiBNMjA4LjUsMTc3LjFMMTQ2LjksOTkgbC02MS42LDc4LjJoLTMxbDkyLjUtMTE3LjVsOTIuNSwxMTcuNUgyMDguNXoiLz48L2c+PC9zdmc+Cg==);"></div>';

            $admin_bar->add_menu( array(
                'id'    => 'ms-main-menu',
                'title' => $logo .' MetaSlider',
                'href'  => '#',
                'meta'  => array(
                    'title' => __('MetaSlider'),            
                ),
            ));
            $admin_bar->add_menu( array(
                'id'    => 'all-slideshows-list',
                'parent' => 'ms-main-menu',
                'title' => __( 'All Slideshows', 'ml-slider' ),
                'href'  => admin_url('admin.php?page=metaslider'),
                'meta'  => array(
                    'title' => __( 'All Slideshows', 'ml-slider' ),
                    'target' => $target,
                    'class' => 'ms_admin_menu_item'
                ),
            ));
            $admin_bar->add_menu( array(
                'id'    => 'create-slideshow-link',
                'parent' => 'ms-main-menu',
                'title' => __( 'Create Slideshow', 'ml-slider' ),
                'href'  => esc_url(wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider"), "metaslider_create_slider")),
                'meta'  => array(
                    'title' => __( 'Create Slideshow', 'ml-slider' ),
                    'target' => $target,
                    'class' => 'ms_admin_menu_item'
                ),
            ));
        }

        /**
         * Shortcode used to display slideshow
         *
         * @param array $atts attributes for short code
         * @return string HTML output of the shortcode
         */
        public function register_shortcode($atts)
        {
            $atts = shortcode_atts(
                [
                    'id' => false,
                    'title' => false,
                    'restrict_to' => false,
                    'theme' => null
                ],
                $atts,
                'metaslider'
            );


            // If no id and no title, exit here
            if (! $atts['id'] && ! $atts['title']) {
                return false;
            }

            // If there is a title, get the id from the title
            $id = $atts['id'];
            if ($atts['title']) {
                global $wpdb;

                // Run a custom query because get_page_by_title() includes "trash" posts
                // Also, be sure just to get 1 post, in case they have multiple
                $id = $wpdb->get_var(
                    $wpdb->prepare(
                        "
                            SELECT ID
                            FROM $wpdb->posts
                            WHERE post_title = %s
                            AND post_type = 'ml-slider'
                            AND post_status = 'publish'
                            LIMIT 1
                        ",
                        $atts['title']
                    )
                );

                // If no posts were returned, $id will be NULL
                if (is_null($id) || ! (bool)$atts['title']) {
                    return false;
                }
            }

            // handle [metaslider id=123 restrict_to=home]
            if ('home' === $atts['restrict_to'] && ! is_front_page()) {
                return false;
            }
            if ($atts['restrict_to'] && 'home' !== $atts['restrict_to'] && ! is_page($atts['restrict_to'])) {
                return false;
            }

            // Attempt to get the ml-slider post object
            $slider = get_post($id);
            // check the slideshow is published and the ID is correct
            if (! $slider || 'publish' !== $slider->post_status || 'ml-slider' !== $slider->post_type) {
                return "<!-- MetaSlider {$atts['id']} not found -->";
            }

            // Set up the slideshow and load the slideshow theme
            $this->set_slider($id, $atts);
            MetaSlider_Themes::get_instance()->load_theme($id, $atts['theme']);
            $this->slider->enqueue_scripts();
            return $this->slider->render_public_slides();
        }

        /**
         * Initialise translations
         */
        public function load_plugin_textdomain()
        {
            // First, unload textdomain - Based on https://core.trac.wordpress.org/ticket/34213#comment:26
            unload_textdomain('ml-slider');

            // Call the core translations from plugins languages/ folder
            if (file_exists(METASLIDER_PATH . 'languages/' . 'ml-slider' . '-' . get_locale() . '.mo')) {
                load_textdomain(
                    'ml-slider',
                    METASLIDER_PATH . 'languages/' . 'ml-slider' . '-' . get_locale() . '.mo'
                );
            }

            if (function_exists('wp_set_script_translations')) {
                wp_set_script_translations(
                    'metaslider-admin-script',
                    'ml-slider',
                    METASLIDER_PATH . 'languages'
                );
            }
        }

        /**
         * Check our WordPress installation is compatible with MetaSlider
         *
         * @param int $slideshow_id The current slideshow ID
         */
        public function do_system_check($slideshow_id)
        {
            $systemCheck = new MetaSliderSystemCheck($slideshow_id);
            $systemCheck->check();
        }

        /**
         * Update the tab options in the media manager
         *
         * @param array $strings Array of settings for custom media tabs
         * @return array
         */
        public function custom_media_uploader_tabs($strings)
        {
            // update strings
            if ((isset($_GET['page']) && $_GET['page'] == 'metaslider')) {
                $strings['insertMediaTitle'] = __("Image", "ml-slider");
                $strings['insertIntoPost'] = __("Add to slideshow", "ml-slider");

                // remove options
                $strings_to_remove = array(
                    'createVideoPlaylistTitle',
                    'createGalleryTitle',
                    'insertFromUrlTitle',
                    'createPlaylistTitle'
                );

                foreach ($strings_to_remove as $string) {
                    if (isset($strings[$string])) {
                        unset($strings[$string]);
                    }
                }
            }

            return $strings;
        }


        /**
         * Add extra tabs to the default wordpress Media Manager iframe
         *
         * @param array $tabs existing media manager tabs]
         * @return array
         */
        public function custom_media_upload_tab_name($tabs)
        {
            $metaslider_tabs = array('post_feed', 'layer', 'youtube', 'vimeo', 'external_url', 'local_video', 'external_video');

            // restrict our tab changes to the MetaSlider plugin page
            if ((isset($_GET['page']) && $_GET['page'] == 'metaslider') || (isset($_GET['tab']) && in_array(
                        $_GET['tab'],
                        $metaslider_tabs
                    ))) {
                $newtabs = array();

                if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
                    $newtabs = array(
                        'post_feed' => __("Post Feed", "ml-slider"),
                        'vimeo' => __("Vimeo", "ml-slider"),
                        'youtube' => __("YouTube", "ml-slider"),
                        'layer' => __("Layer Slide", "ml-slider"),
                        'external_url' => __("External URL", "ml-slider"),
                        'local_video' => __("Local Video", "ml-slider"),
                        'external_video' => __("External Video", "ml-slider"),
                    );
                }

                if (isset($tabs['nextgen'])) {
                    unset($tabs['nextgen']);
                }


                if (is_array($tabs)) {
                    return array_merge($tabs, $newtabs);
                } else {
                    return $newtabs;
                }
            }

            return $tabs;
        }

        /**
         * Set the current slider
         *
         * @param int $id ID for slider
         * @param array $shortcode_settings Settings for slider
         */
        public function set_slider($id, $shortcode_settings = array())
        {
            $type = 'flex';

            if (isset($shortcode_settings['type'])) {
                $type = $shortcode_settings['type'];
            } elseif ($settings = get_post_meta($id, 'ml-slider_settings', true)) {
                if (is_array($settings) && isset($settings['type'])) {
                    $type = $settings['type'];
                }
            }

            if (! in_array($type, array('flex', 'coin', 'nivo', 'responsive'))) {
                $type = 'flex';
            }

            $this->slider = $this->load_slider($type, $id, $shortcode_settings);
        }


        /**
         * Create a new slider based on the sliders type setting
         *
         * @param string $type Type of slide
         * @param int $id ID of slide
         * @param string $shortcode_settings Shortcode settings
         * @return array
         */
        private function load_slider($type, $id, $shortcode_settings)
        {
            switch ($type) {
                case ('coin'):
                    return new MetaCoinSlider($id, $shortcode_settings);
                case ('flex'):
                    return new MetaFlexSlider($id, $shortcode_settings);
                case ('nivo'):
                    return new MetaNivoSlider($id, $shortcode_settings);
                case ('responsive'):
                    return new MetaResponsiveSlider($id, $shortcode_settings);
                default:
                    return new MetaFlexSlider($id, $shortcode_settings);
            }
        }

        /**
         * Update the slider
         *
         * @return string a JSON string with success or failure (and errors)
         * @deprecated 3.13.0 use the API
         *
         */
        public function update_slider()
        {
            return $this->api->save_slideshow(stripslashes_deep($_REQUEST));
        }

        /**
         * Delete a slide via ajax.
         *
         * @return string Returns the status of the request
         */
        public function ajax_undelete_slide()
        {
            if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(
                    sanitize_key($_REQUEST['_wpnonce']),
                    'metaslider_undelete_slide'
                )) {
                wp_send_json_error(array(
                    'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
                ), 401);
            }

            $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                wp_send_json_error(
                    [
                        'message' => __('Access denied', 'ml-slider')
                    ],
                    403
                );
            }

            if (! isset($_POST['slide_id']) || ! isset($_POST['slider_id'])) {
                wp_send_json_error(
                    [
                        'message' => __('Bad request', 'ml-slider'),
                    ],
                    400
                );
            }

            $result = $this->undelete_slide(absint($_POST['slide_id']), absint($_POST['slider_id']));

            if (is_wp_error($result)) {
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ), 409);
            }

            wp_send_json_success(array(
                'message' => __('The slide was successfully restored', 'ml-slider'),
            ), 200);
        }

        /**
         * Undeletes a slide.
         *
         * @param int $slide_id The ID of the slide
         * @param int $slideshow_id The ID of the slideshow
         * @return mixed
         */
        public function undelete_slide($slide_id, $slideshow_id)
        {
            if ('ml-slide' === get_post_type($slide_id)) {
                // Touch the slideshow post type to update the modified date
                wp_update_post(array('ID' => $slideshow_id));

                return wp_update_post(array(
                    'ID' => $slide_id,
                    'post_status' => 'publish'
                ),
                    new WP_Error(
                        'update_failed',
                        __('The attempt to restore the slide failed.', 'ml-slider'),
                        array('status' => 409)
                    ));
            }

            /*
            * Legacy: This removes the relationship between the slider and slide
            * This restores the relationship between a slide and slider.
            * If using a newer version, this relationship is never lost on delete.
            * NB: this covers when a 'ml-slide' was an attachment with meta
            */

            // Get the slider's term and apply it to the slide.
            $term = get_term_by('name', $slideshow_id, 'ml-slider');
            return wp_set_object_terms($slide_id, $term->term_id, 'ml-slider');
        }

        /**
         * Delete a slide via ajax.
         */
        public function ajax_delete_slide()
        {
            if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(
                    sanitize_key($_REQUEST['_wpnonce']),
                    'metaslider_delete_slide'
                )) {
                wp_send_json_error(array(
                    'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
                ), 401);
            }

            $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                wp_send_json_error(
                    [
                        'message' => __('Access denied', 'ml-slider')
                    ],
                    403
                );
            }

            if (! isset($_POST['slide_id']) || ! isset($_POST['slider_id'])) {
                wp_send_json_error(
                    [
                        'message' => __('Bad request', 'ml-slider'),
                    ],
                    400
                );
            }

            $result = $this->delete_slide(absint($_POST['slide_id']), absint($_POST['slider_id']));

            if (is_wp_error($result)) {
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ), 409);
            }

            wp_send_json_success(array(
                'message' => __('The slide was successfully trashed', 'ml-slider'),
            ), 200);
        }

        /**
         * Delete a slide by either trashing it or for
         * legacy reasons removing the taxonomy relationship.
         *
         * @param int $slide_id The ID of the slide
         * @param int $slideshow_id The ID of the slideshow
         * @return mixed Will return the terms or WP_Error
         */
        public function delete_slide($slide_id, $slideshow_id)
        {
            if ('ml-slide' === get_post_type($slide_id)) {
                // Touch the slideshow post type to update the modified date
                wp_update_post(array('ID' => $slideshow_id));

                return wp_update_post(array(
                    'ID' => $slide_id,
                    'post_status' => 'trash'
                ), new WP_Error('update_failed', 'The attempt to delete the slide failed.', array('status' => 409)));
            }

            /*
            * Legacy: This removes the relationship between the slider and slide
            * A slider with ID 216 might have a term_id of 7
            * A slide with ID 217 could have a term_taxonomy_id of 7
            * Multiple slides would have this term_taxonomy_id of 7
            * NB: this covers when a 'ml-slide' was an attachment with meta
            */

            // This returns the term_taxonomy_id (7 from example)
            $current_terms = wp_get_object_terms($slide_id, 'ml-slider', array('fields' => 'ids'));

            // This returns the term object, named after the slider ID
            // The $term->term_id would be 7 in the example above
            // It also includes the count of slides attached to the slider
            $term = get_term_by('name', $slideshow_id, 'ml-slider');

            // I'm not sure why this is here. It seems this is only useful if
            // a slide was attached to multiple sliders. A slide should only
            // have one $current_terms (7 above)
            $new_terms = array();
            foreach ($current_terms as $current_term) {
                if ($current_term != $term->term_id) {
                    $new_terms[] = absint($current_term);
                }
            }

            // This only works becasue $new_terms is an empty array,
            // which deletes the relationship. I'm leaving the loop above
            // in case it's here for some legacy reason I'm unaware of.
            return wp_set_object_terms($slide_id, $new_terms, 'ml-slider');
        }


        /**
         * Delete a slider (send it to trash)
         *
         * @return string - the json response from the API
         * @deprecated 3.11.0 use the API
         *
         */
        public function delete_slider()
        {
            return $this->api->delete_slideshow($_REQUEST);
        }


        /**
         * Permanently delete a slide via ajax.
         */
        public function ajax_permanent_delete_slide()
        {
            if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(
                    sanitize_key($_REQUEST['_wpnonce']),
                    'metaslider_permanent_delete_slide'
                )) {
                wp_send_json_error(array(
                    'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
                ), 401);
            }

            $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                wp_send_json_error(
                    [
                        'message' => __('Access denied', 'ml-slider')
                    ],
                    403
                );
            }

            if (! isset($_POST['slide_id'])) {
                wp_send_json_error(
                    [
                        'message' => __('Bad request', 'ml-slider'),
                    ],
                    400
                );
            }

            $result = $this->permanent_delete_slide(absint($_POST['slide_id']));

            if (is_wp_error($result)) {
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ), 409);
            }

            wp_send_json_success(array(
                'message' => __('The slide was permanently deleted', 'ml-slider'),
            ), 200);
        }

        /**
         * Delete a slide by either trashing it or for
         * legacy reasons removing the taxonomy relationship.
         *
         * @param int $slide_id The ID of the slide
         * @param int $slideshow_id The ID of the slideshow
         * @return mixed Will return the terms or WP_Error
         */
        public function permanent_delete_slide($slide_id)
        {
            if ('ml-slide' === get_post_type($slide_id)) {
                return wp_delete_post($slide_id, true);
            }
        }

        /**
         * Switch view
         *
         * @return null
         */
        public function switch_view()
        {
            global $user_ID;

            if (! isset($_GET['view'])) {
                return;
            }

            $view = sanitize_key($_GET['view']);

            $allowed_views = array('tabs', 'dropdown');

            if (! in_array($view, $allowed_views)) {
                return;
            }

            delete_user_meta($user_ID, "metaslider_view");

            if ($view == 'dropdown') {
                add_user_meta($user_ID, "metaslider_view", "dropdown");
            }

            wp_redirect(admin_url("admin.php?page=metaslider"));
            exit;
        }

        /**
         * Create a new slider
         */
        public function create_slider()
        {
            check_admin_referer('metaslider_create_slider');
            $capability = apply_filters('metaslider_capability', self::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                return;
            }

            $id = MetaSlider_Slideshows::create();

            if (isset($_GET['metaslider_add_sample_slides'])) {
                $slideInstance = new MetaSlider_Slideshows();
                $sampleType = sanitize_key($_GET['metaslider_add_sample_slides']);
                if ($sampleType == 'carousel') {
                    $settings = array(
                        'type' => 'flex',
                        'printCss' => 'on',
                        'printJs' => 'on',
                        'width' => 1200,
                        'height' => 600,
                        'center' => 'on',
                        'carouselMode' => 'on',
                        'autoPlay' => 'on',
                        'fullWidth' => 'on',
                        'noConflict' => 'on'
                    );
                    $sampleId = $slideInstance->save($id, $settings);
                } elseif ($sampleType == 'withcaption') {
                    $settings = array(
                        'type' => 'flex',
                        'printCss' => 'on',
                        'printJs' => 'on',
                        'width' => 400,
                        'height' => 400,
                        'center' => 'on',
                        'carouselMode' => 'on',
                        'autoPlay' => 'on',
                        'fullWidth' => 'on',
                        'noConflict' => 'on'
                    );
                    $sampleId = $slideInstance->save($id, $settings);

                    $themeInstance = new MetaSlider_Themes();
                    $outlineTheme = $themeInstance->get_theme_object($id,'outline');
                    $setTheme = $themeInstance->set($id, $outlineTheme);
                } else {
                    $sampleId = $id;
                    $sampleType = "";
                }

                wp_redirect(esc_url_raw(admin_url("admin.php?page=metaslider&id={$sampleId}&metaslider_add_sample_slides={$sampleType}")));
                exit;
            } else {
                wp_redirect(esc_url_raw(admin_url("admin.php?page=metaslider&id={$id}")));
                exit;
            }          
        }

        /**
         * Find a single slider ID. For example, last edited, or first published.
         *
         * @param string $orderby field to order.
         * @param string $order direction (ASC or DESC).
         * @return int slider ID.
         */
        private function find_slider($orderby, $order)
        {
            $args = array(
                'force_no_custom_order' => true,
                'post_type' => 'ml-slider',
                'num_posts' => 1,
                'post_status' => 'publish',
                'suppress_filters' => 1, // wpml, ignore language filter
                'orderby' => $orderby,
                'order' => $order
            );

            $the_query = new WP_Query($args);

            while ($the_query->have_posts()) {
                $the_query->the_post();
                return $the_query->post->ID;
            }

            wp_reset_query();

            return false;
        }


        /**
         * Get sliders. Returns a nicely formatted array of currently
         * published sliders.
         *
         * @param string $sort_key Specified sort key
         * @return array all published sliders
         */
        public function all_meta_sliders($sort_key = 'date')
        {
            $sliders = array();

            // list the tabs
            $args = array(
                'post_type' => 'ml-slider',
                'post_status' => 'publish',
                'orderby' => $sort_key,
                'suppress_filters' => 1, // wpml, ignore language filter
                'order' => 'ASC',
                'posts_per_page' => -1
            );

            $args = apply_filters('metaslider_all_meta_sliders_args', $args);

            // WP_Query causes issues with other plugins using admin_footer to insert scripts
            // use get_posts instead
            $all_sliders = get_posts($args);

            foreach ($all_sliders as $slideshow) {
                $active = $this->slider && ($this->slider->id == $slideshow->ID) ? true : false;

                $sliders[] = array(
                    'active' => $active,
                    'title' => $slideshow->post_title,
                    'id' => $slideshow->ID
                );
            }

            return $sliders;
        }

        /**
         * Compare array values
         *
         * @param array $elem1 The first element to comapre
         * @param array $elem2 The second element to comapr
         * @return int
         */
        private function compare_elems($elem1, $elem2)
        {
            if ($elem1['priority'] == $elem2['priority']) {
                return 0;
            }
            return $elem1['priority'] > $elem2['priority'] ? 1 : -1;
        }


        /**
         * Building setting rows
         *
         * @param array $settings array of fields to render
         * @return string
         */
        public function build_settings_rows($settings)
        {
            // order the fields by priority
            uasort($settings, array($this, "compare_elems"));
            $output = "";

            // loop through the array and build the settings HTML
            foreach ($settings as $id => $row) {
                $helptext       = isset($row['helptext']) ? htmlentities2($row['helptext']) : '';
                $dependencies   = '';
                
                if ( isset( $row['dependencies'] ) ) {
                    $json = json_encode( $row['dependencies'] );
                    $dependencies = htmlspecialchars( $json, ENT_QUOTES, 'UTF-8' );
                    $dependencies = ' data-dependencies="' . $dependencies . '"';
                }

                $after = '';
                if (isset($row['after'])) {
                    $after = '<span class="">' . $row['after'] . '</span>';
                }

                switch ($row['type']) {
                    // checkbox input type
                    case 'checkbox':
                        $output .= '<tr class="' . esc_attr(
                                $row["type"]
                            ) . '"><td class="tipsy-tooltip" title="' . esc_attr($helptext) . '">' . esc_html(
                                $row["label"]
                            ) . '</td><td>';
                        $output .= '<div class="ms-switch-button">
                            <label>
                                <input type="checkbox" id="" name="settings[' . esc_attr($id) . ']" ' . esc_attr(
                                    $row["checked"]
                                ) . ' class="' . esc_attr($row["class"]) . '"' . 
                                $dependencies . '/>
                                <span></span>
                            </label>
                        </div>';
                        $output .= $after;
                        $output .= '</td></tr>';
                        break;

                    // navigation row
                    case 'radio':
                        $navigation_row = '<tr class="' . esc_attr(
                                $row["type"]
                            ) . '"><td class="tipsy-tooltip" title="' . esc_attr($helptext) . '">' . esc_html(
                                $row["label"]
                            ) . '</td><td><ul>';
                        foreach ($row['options'] as $option_name => $option_value) {
                            $checked = checked($option_name, $row['value'], false);
                            $class = isset($option_value['class']) ? $option_value['class'] : "";
                            $navigation_row .= '<li><label><input type="radio" name="settings[' . esc_attr(
                                    $id
                                ) . ']" value="' . esc_attr(
                                    $option_name
                                ) . '" ' . $checked . ' class="radio ' . esc_attr($class) . '"/>' . esc_html(
                                    $option_value["label"]
                                ) . '</label></li>';
                        }
                        $navigation_row .= '</ul>';
                        $navigation_row .= $after;
                        $navigation_row .= '</td></tr>';
                        $output .= apply_filters('metaslider_navigation_options', $navigation_row, $this->slider);
                        break;

                    // header row
                    case 'highlight':

                        if ( isset( $row["topspacing"] ) && $row["topspacing"] ) {
                            $output .= '<tr class="empty-row-spacing">
                                <td colspan="2"></td>
                            </tr>';
                        }

                        $output .= '<tr class="' . esc_attr(
                            $row["type"]
                        ) . '"><td colspan="2">' . esc_html(
                                $row["value"]
                            ) . $after . '</td></tr>';
                        $output .= '<tr class="empty-row-spacing">
                            <td colspan="2"></td>
                        </tr>';
                        break;

                    // slideshow select row
                    case 'slider-lib':
                        $output .= '<tr class="' . esc_attr($row['type']) . '"><td colspan="2" class="slider-lib-row">';
                        foreach ($row['options'] as $option_name => $option_value) {
                            $checked = checked($option_name, $row['value'], false);
                            $output .= '<input class="select-slider" id="' . esc_attr(
                                    $option_name
                                ) . '" rel="' . esc_attr(
                                    $option_name
                                ) . '" type="radio" name="settings[type]" value="' . esc_attr(
                                    $option_name
                                ) . '" ' . $checked . '  />'
                                . '<label tabindex="0" for="' . esc_attr($option_name) . '">' . esc_html(
                                    $option_value['label']
                                ) . '</label>';
                        }
                        $output .= $after;
                        $output .= '</td></tr>';
                        $output .= '</table><table class="ms-settings-table">';
                        break;

                    // number input type
                    case 'number':
                        $output .= '<tr class="' . esc_attr(
                                $row["type"]
                            ) . '"><td class="tipsy-tooltip" title="' . esc_attr($helptext) . '">' . esc_html(
                                $row["label"]
                            ) . '</td><td class="flex items-center justify-start"><input class="option ' . esc_attr(
                                $row["class"]
                            ) . ' ' . esc_attr($id) . ' w-20" type="number" min="' . esc_attr(
                                $row["min"]
                            ) . '" max="' . esc_attr($row["max"]) . '" step="' . esc_attr(
                                $row["step"]
                            ) . '" name="settings[' . esc_attr($id) . ']" value="' . esc_attr(
                                $row["value"]
                            ) . '" /><span class="text-base ml-1 rtl:ml-0 rtl:mr-1">' . '</span>';
                        $output .= $after;
                        $output .= '</td></tr>';
                        break;

                    // select drop down
                    case 'select':
                    case 'navigation':
                        $output .= '<tr class="' . esc_attr(
                                $row["type"]
                            ) . '"><td class="tipsy-tooltip" title="' . esc_attr($helptext) . '">' . esc_html(
                                $row["label"]
                            ) . '</td><td><select class="option ' . esc_attr($row["class"]) . ' ' . esc_attr(
                                $id
                            ) . ' width w-40" name="settings[' . esc_attr($id) . ']"' . 
                                $dependencies . '>';
                        foreach ($row['options'] as $option_name => $option_value) {
                            $selected = selected($option_name, $row['value'], false);
                            $disabled = isset( $option_value['addon_required'] ) && $option_value['addon_required'] 
                                        ? ' disabled="disabled"' : '';
                            $output .= '<option class="' . (
                                    isset( $option_value['class'] ) ? esc_attr( $option_value['class'] ) : '' 
                                ) . '" value="' . esc_attr(
                                    $option_name
                                ) . '" ' . $selected . $disabled . '>' . esc_html($option_value['label']) . '</option>';
                        }
                        $output .= '</select>';
                        $output .= $after;
                        $output .= '</td></tr>';
                        break;

                    // theme drop down
                    case 'theme':
                        $output .= '<tr class="' . esc_attr(
                                $row["type"]
                            ) . '"><td class="tipsy-tooltip" title="' . esc_attr($helptext) . '">' . esc_html(
                                $row["label"]
                            ) . '</td><td><select class="option ' . esc_attr($row["class"]) . ' ' . esc_attr(
                                $id
                            ) . '" name="settings[' . esc_attr($id) . ']">';
                        $themes = "";
                        foreach ($row['options'] as $option_name => $option_value) {
                            $selected = selected($option_name, $row['value'], false);
                            $themes .= '<option class="' . esc_attr($option_value["class"]) . '" value="' . esc_attr(
                                    $option_name
                                ) . '" ' . $selected . '>' . esc_html($option_value["label"]) . '</option>';
                        }
                        $output .= apply_filters(
                            'metaslider_get_available_themes',
                            $themes,
                            $this->slider->get_setting('theme')
                        );
                        $output .= '</select>';
                        $output .= $after;
                        $output .= '</td></tr>';
                        break;

                    // text input type
                    case 'text':
                        $output .= '<tr class="' . esc_attr(
                                $row["type"]
                            ) . '"><td class="tipsy-tooltip" title="' . esc_attr($helptext) . '">' . esc_html(
                                $row["label"]
                            ) . '</td><td class="flex items-center justify-start"><input class="option ' . esc_attr(
                                $row["class"]
                            ) . ' ' . esc_attr(
                                $id
                            ) . ' width w-40" type="text" autocomplete="off" data-lpignore="true" name="settings[' . esc_attr(
                                $id
                            ) . ']" value="' . esc_attr($row["value"]) . '" />';
                        $output .= $after;
                        $output .= '</td></tr>';
                        break;

                    // text input type
                    case 'textarea':
                        $output .= '<tr class="' . esc_attr(
                                $row["type"]
                            ) . '"><td class="tipsy-tooltip" title="' . esc_attr(
                                $helptext
                            ) . '" colspan="2">' . esc_html(
                                $row["label"]
                            ) . '</td></tr><tr><td colspan="2"><textarea class="option ' . esc_attr(
                                $row["class"]
                            ) . ' ' . esc_attr($id) . '" name="settings[' . esc_attr($id) . ']" />' . esc_html(
                                $row["value"]
                            ) . '</textarea>';
                        $output .= $after;
                        $output .= '</td></tr>';
                        break;

                    // text input type
                    case 'title':
                        $output .= '<tr class="' . esc_attr(
                                $row["type"]
                            ) . '"><td class="tipsy-tooltip" title="' . esc_attr($helptext) . '">' . esc_html(
                                $row["label"]
                            ) . '</td><td><input class="option ' . esc_attr($row["class"]) . ' ' . esc_attr(
                                $id
                            ) . '" type="text" autocomplete="off" data-lpignore="true" name="' . esc_attr(
                                $id
                            ) . '" value="' . esc_attr($row["value"]) . '" />';
                        $output .= $after;
                        $output .= '</td></tr>';
                        break;
                }
            }
            return $output;
        }


        /**
         * Return an indexed array of all easing options
         *
         * @return array
         */
        private function get_easing_options()
        {
            $options = array(
                'linear',
                'swing',
                'jswing',
                'easeInQuad',
                'easeOutQuad',
                'easeInOutQuad',
                'easeInCubic',
                'easeOutCubic',
                'easeInOutCubic',
                'easeInQuart',
                'easeOutQuart',
                'easeInOutQuart',
                'easeInQuint',
                'easeOutQuint',
                'easeInOutQuint',
                'easeInSine',
                'easeOutSine',
                'easeInOutSine',
                'easeInExpo',
                'easeOutExpo',
                'easeInOutExpo',
                'easeInCirc',
                'easeOutCirc',
                'easeInOutCirc',
                'easeInElastic',
                'easeOutElastic',
                'easeInOutElastic',
                'easeInBack',
                'easeOutBack',
                'easeInOutBack',
                'easeInBounce',
                'easeOutBounce',
                'easeInOutBounce'
            );
            $output = array();

            foreach ($options as $option) {
                $output[$option] = array(
                    'label' => ucfirst(preg_replace('/(\w+)([A-Z])/U', '\\1 \\2', $option)),
                    'class' => ''
                );
            }

            return $output;
        }

        /**
         * Return the users saved view preference.
         */
        public function get_view()
        {
            global $user_ID;

            if (get_user_meta($user_ID, "metaslider_view", true)) {
                return get_user_meta($user_ID, "metaslider_view", true);
            }

            return 'tabs';
        }

        public function get_global_settings() {
            if (is_multisite() && $settings = get_site_option('metaslider_global_settings')) {
                return $settings;
            }
    
            if ($settings = get_option('metaslider_global_settings')) {
                return $settings;
            }
        }

        /**
         * Render the admin page (tabs, slides, settings)
         */
        public function render_admin_page()
        {
            // Default to the most recently modified slider
            $slider_id = $this->find_slider('modified', 'DESC');

            // If the id parameter exists, verify and use that.
            if (isset($_REQUEST['id']) && $id = (int)$_REQUEST['id']) {
                if (in_array(get_post_status($id), array('publish', 'inherit'))) {
                    $slider_id = $id;
                }
            }

            // "Set the slider"
            // TODO figure out what this does and if it can be better stated
            // Perhaps maybe "apply_settings()" or something.
            if ($slider_id) {
                $this->set_slider($slider_id);
            }

            $slider_id = $this->slider ? $this->slider->id : 0;

            $this->do_system_check($slider_id);

            if (metaslider_user_is_ready_for_notices()) {
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $this->notices->do_notice(false, 'header', true);
            }

            // Get the current theme if set
            $theme = metaslider_themes::get_instance()->get_current_theme($slider_id);
            if (is_wp_error($theme)) {
                $theme = false;
            }
            if (is_array($theme)) {
                unset($theme['images']);
            }
            $theme_id = isset($theme['folder']) ? $theme['folder'] : false; ?>
            <div id="metaslider-ui" class="metaslider metaslider-ui block min-h-screen p-0 pb-24 bg-gray-lightest">
                <?php
                $slider_settings = get_post_meta($slider_id, 'ml-slider_settings', true);
                $tour_position = get_option('metaslider_tour_cancelled_on');

                if (! class_exists('MetaSlider_Analytics')) {
                    require_once(METASLIDER_PATH . 'admin/support/Analytics.php');
                }
                $analytics = new MetaSlider_Analytics(); ?>

                <metaslider
                        :id='<?php
                        echo esc_attr($slider_id); ?>'
                        v-bind:settings='<?php
                        echo esc_attr(json_encode($slider_settings)); ?>'
                        tour-status="<?php
                        echo $tour_position ? esc_attr($tour_position) : false ?>"
                        show-opt-in="<?php
                        echo esc_attr(
                            ! $analytics::siteIsOptin() && ! get_user_option(
                                'metaslider_analytics_onboarding_status'
                            ) ? 1 : ''
                        ); ?>"
                        inline-template>
                <span>
                <form @submit.prevent="" @keydown.enter.prevent="" autocomplete="off" id="ms-form-settings"
                      accept-charset="UTF-8" action="<?php
                echo esc_url(admin_url('admin-post.php')); ?>" method="post" :class="{ 'respect-ie11': isIE11 }">

                    <?php
                    include METASLIDER_PATH . "admin/views/pages/parts/toolbar.php"; ?>

                    <div class="container px-6">
                    <?php

                    // If there is no slideshow, show the start page and close out
                    if (! $this->slider) {
                        include METASLIDER_PATH . "admin/views/pages/start.php";
                        echo '</form><metaslider-utility-modal></metaslider-utility-modal></span></metaslider>';
                        return;
                    } ?>
                    <div v-if="current && current.hasOwnProperty('id')" id='poststuff'
                         class="metaslider-inner wp-clearfix">
                         <?php
                            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'permanent') {
                                echo '<div class="updated below-h2" id="message"><p>' . esc_html( 'Slide permanently deleted.', 'ml-slider'). '</p></div>';
                            }
                            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'restore') {
                                echo '<div class="updated below-h2" id="message"><p>' . esc_html( 'Slide restored.', 'ml-slider'). '</p></div>';
                            }
                         ?>
                        <div class="my-6">
                            <?php
                            if (metaslider_viewing_trashed_slides($this->slider->id)) {
                                // If they are on the trash page, show them
                                ?>
                                <h1 class="-ml-4 h-16 pl-4 pr-12 text-2xl font-light rounded-none shadow-none bg-transparent border-0 border-l-4 border-transparent hover:border-gray-light hover:bg-white focus:bg-white focus:shadow-sm focus:border-gray-light rtl:border-l-0 rtl:border-r-0 rtl:ml-0 rtl:-mr-4 rtl:pl-12 rtl:pr-4"
                                    style="line-height: 2.7em;">
                                    <?php 
                                    printf(
                                        '%s (%s)',
                                        esc_html__( get_the_title( $this->slider->id ) ),
                                        esc_html__( 'Trashed Slides', 'ml-slider' )
                                    ); 
                                    ?>
                                </h1>
                                <?php
                            } else { ?>
                                <metaslider-title></metaslider-title>
                            <?php
                            } ?>
                        </div>

                        <div id='post-body' class='ms-edit-slideshow -mx-4 metabox-holder<?php
                        echo $this->slider && metaslider_viewing_trashed_slides( $this->slider->id )
                            ? ' ms-edit-slideshow--trashed-slides' : '';
                        ?>'>

                            <metaslider-slide-viewer inline-template>

                                <div class="left mx-4 mb-4">

                                    <?php
                                    do_action("metaslider_admin_table_before", $this->slider->id); ?>

                                    <table id="metaslider-slides-list"
                                           class="widefat sortable metaslider-slides-container table-fixed">
                                        <tbody>
                                            <?php
                                            $this->slider->render_admin_slides(); ?>
                                        </tbody>
                                    </table>

                                    <?php
                                    do_action("metaslider_admin_table_after", $this->slider->id); ?>

                                </div>

                            </metaslider-slide-viewer>

                                <div class='right mx-4'>
                                <?php
                                if (metaslider_viewing_trashed_slides($this->slider->id)) {
                                    // Show a notice explaining the trash?>
                                    <div class="ms-postbox trashed-notice">
                                        <div class="notice-info">
                                            <p><?php
                                            printf(
                                                esc_html__(
                                                    'You are viewing slides that have been trashed, which will be automatically deleted in %s days.',
                                                    'ml-slider'
                                                ),
                                                esc_html(EMPTY_TRASH_DAYS)
                                            ); ?></p>
                                            <p class="mb-0">
                                                <a href="<?php echo esc_url(
                                                        admin_url( "admin.php?page=metaslider&id={$this->slider->id}" )
                                                    ) ?>" class="button button-secondary">
                                                    <?php esc_html_e( 'Return to Published Slides', 'ml-slider' ) ?>
                                                </a>
                                            </p>
                                            <?php
                                            // TODO this is a temp fix to avoid a compatability check in pro
                                            echo "<input type='checkbox' style='display:none;' checked class='select-slider' rel='flex'></inpu>"; ?>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    $global_settings = $this->get_global_settings();
                                    $new_install = get_option('metaslider_new_user');
                            ?>
                                    <metaslider-settings-viewer inline-template>
                                        <div>
                                            <div id="metaslider_configuration">
                                                <?php
                                                if (
                                                    (isset($global_settings['legacy']) && true == $global_settings['legacy']) ||
                                                    (isset($new_install)  && 'new' == $new_install)
                                                ) {
                                                    if($this->slider->get_setting('type') == 'flex') {
                                                        include METASLIDER_PATH . "admin/views/pages/parts/slider-settings.php";
                                                    } else {
                                                        include METASLIDER_PATH . "admin/views/pages/parts/slider-settings-legacy.php";
                                                    }
                                                } else {
                                                    include METASLIDER_PATH . "admin/views/pages/parts/slider-settings-legacy.php";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </metaslider-settings-viewer>
                                    <?php
                                    $url = wp_nonce_url(
                                        admin_url(
                                            "admin-post.php?action=metaslider_delete_slider&amp;slider_id={$this->slider->id}"
                                        ),
                                        "metaslider_delete_slider"
                                    ); ?>

                                    <div class="ms-delete-save">
                                        <a @click.prevent="deleteSlideshow()" class='ms-delete-slideshow' href='<?php
                                        echo esc_url($url) ?>'><?php
                                            esc_html_e('Move slideshow to trash', 'ml-slider'); ?></a>
                                    </div>
                                    </div>
                                <?php
                                } ?>
                        </div>
                    </div>
                </div>
            </form>
            <metaslider-preview
                    slideshow-id="<?php
                    echo esc_attr($this->slider->id); ?>"
                    theme-identifier="<?php
                    echo esc_attr($theme_id); ?>"
                    :showButton="false"></metaslider-preview>
            <metaslider-import-module></metaslider-import-module>
            <metaslider-utility-modal></metaslider-utility-modal>
            <?php
            do_action('metaslider_add_external_components'); ?>
            </span>
                </metaslider>
            </div>
            <?php
        }

        /**
         * Append the 'Add Slideshow' button to selected admin pages (classic editor)
         * Uses the media_buttons filter
         */
        public function insert_metaslider_button()
        {
            $capability = apply_filters('metaslider_capability', self::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                return;
            }

            global $pagenow;
            if (! in_array($pagenow, array('post.php', 'page.php', 'post-new.php', 'post-edit.php'))) {
                return;
            }

            printf(
                '
                <a href="#TB_inline?&inlineId=choose-meta-slider" class="thickbox button" title="%s">
                    <span class="wp-media-buttons-icon"
                        style="background:url(%s/metaslider/matchalabs.png);background-repeat:no-repeat;background-position:left bottom;position: relative;top:-2px;right:-2px"></span>%s</a>',
                esc_html__("Select slideshow to insert into post", "ml-slider"),
                esc_url(METASLIDER_ASSETS_URL),
                esc_html__("Add slideshow", "ml-slider")
            );
        }

        /**
         * Append the 'Choose MetaSlider' thickbox content to the bottom of selected admin pages
         */
        public function admin_footer()
        {
            global $pagenow;

            // Only run in post/page creation and edit screens
            if (in_array($pagenow, array('post.php', 'page.php', 'post-new.php', 'post-edit.php'))) {
                $sliders = $this->all_meta_sliders('title'); ?>

                <script>
                    jQuery(document).ready(function () {
                        jQuery('#insertMetaSlider').on('click', function () {
                            var id = jQuery('#metaslider-select option:selected').val();
                            window.send_to_editor('[metaslider id=' + id + ']');
                            tb_remove();
                        })
                    });
                </script>

                <div id="choose-meta-slider" style="display: none;">
                    <div class="wrap">
                        <?php
                        if (count($sliders)) {
                            echo "<h3 style='margin-bottom: 20px;'>" . esc_html_x(
                                    "Insert MetaSlider",
                                    'Keep the plugin name "MetaSlider" when possible',
                                    "ml-slider"
                                ) . "</h3>";
                            echo "<select id='metaslider-select'>";
                            echo "<option disabled=disabled>" . esc_html__(
                                    "Choose slideshow",
                                    "ml-slider"
                                ) . "</option>";
                            foreach ($sliders as $slider) {
                                echo '<option value="' . esc_attr($slider['id']) . '">' . esc_html(
                                        $slider['title']
                                    ) . '</option>';
                            }
                            echo "</select>";
                            echo '<button class="button primary" id="insertMetaSlider">' . esc_html__(
                                    "Insert slideshow",
                                    "ml-slider"
                                ) . '</button>';
                        } else {
                            esc_html_e("No slideshows found", "ml-slider");
                        } ?>
                    </div>
                </div>

                <?php
            }
        }


        /**
         * Return the MetaSlider pro upgrade iFrame
         */
        public function upgrade_to_pro_tab_layer()
        {
            if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
                return wp_iframe(array($this, 'upgrade_to_pro_iframe_layer'));
            }
        }

        /**
         * Media Manager iframe HTML - vimeo
         */
        public function upgrade_to_pro_iframe_layer()
        {
            $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
            $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-layer&amp;utm_campaign=pro';
            $this->upgrade_to_pro_iframe(
                array(
                    '<div class="left"><img src="' . esc_url(METASLIDER_ADMIN_URL . 'images/upgrade/layers.png') . '" alt="" /></div>',
                    "<div><h2>" . esc_html__(
                        'Create slides by adding text, videos and more to your images',
                        'ml-slider'
                    ) . "</h2>",
                    "<p>" . esc_html__(
                        'With Layer slides, you can create beautiful, stylish layers of different elements.',
                        'ml-slider'
                    ) . "</p>",
                    "<p>" . esc_html__(
                        'You start with an image and then can add text, video, colors, animations, more images, and even shortcodes.',
                        'ml-slider'
                    ) . "</p>",
                    '<a class="probutton button button-primary button-hero" href="' . esc_url(
                        $link
                    ) . '" target="_blank">' . esc_html__(
                        'Find out more about MetaSlider Pro',
                        'ml-slider'
                    ) . "<span class='dashicons dashicons-external'></span></a>",
                    "</div>"
                )
            );
        }

        /**
         * Return the MetaSlider pro upgrade iFrame for Vimeo
         */
        public function upgrade_to_pro_tab_vimeo()
        {
            if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
                return wp_iframe(array($this, 'upgrade_to_pro_iframe_vimeo'));
            }
        }

        /**
         * Media Manager iframe HTML - vimeo
         */
        public function upgrade_to_pro_iframe_vimeo()
        {
            $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
            $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-vimeo&amp;utm_campaign=pro';
            $this->upgrade_to_pro_iframe(
                array(
                    '<div class="left"><img src="' . esc_url(METASLIDER_ADMIN_URL . 'images/upgrade/vimeo.png') . '" alt="" /></div>',
                    "<div><h2>" . esc_html__(
                        'Create slideshows with your Vimeo videos',
                        'ml-slider'
                    ) . "</h2>",
                    "<p>" . esc_html__(
                        'With Vimeo slides, you can build beautiful slideshows with your Vimeo videos.',
                        'ml-slider'
                    ) . "</p>",
                    "<p>" . esc_html__(
                        'Vimeo slides will display your videos with auto play, mute, the ability to hide controls, and much more.',
                        'ml-slider'
                    ) . "</p>",
                    "<a class='probutton button button-primary button-hero' href='{$link}' target='_blank'>" . esc_html__(
                        'Find out more about MetaSlider Pro',
                        'ml-slider'
                    ) . "<span class='dashicons dashicons-external'></span></a>",
                    "</div>"
                )
            );
        }

        /**
         * Return the MetaSlider pro upgrade iFrame
         */
        public function upgrade_to_pro_tab_youtube()
        {
            if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
                return wp_iframe(array($this, 'upgrade_to_pro_iframe_youtube'));
            }
        }

        /**
         * Media Manager iframe HTML - youtube
         */
        public function upgrade_to_pro_iframe_youtube()
        {
            $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
            $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-youtube&amp;utm_campaign=pro';
            $this->upgrade_to_pro_iframe(
                array(
                    '<div class="left"><img src="' . esc_url(METASLIDER_ADMIN_URL . 'images/upgrade/youtube.png') . '" alt="" /></div>',
                    "<div><h2>" . esc_html__(
                        'Create slideshows with your YouTube videos',
                        'ml-slider'
                    ) . "</h2>",
                    "<p>" . esc_html__(
                        'With YouTube slides, you can build beautiful slideshows with your YouTube videos.',
                        'ml-slider'
                    ) . "</p>",
                    "<p>" . esc_html__(
                        'YouTube slides will display your videos with auto play, mute, lazy load, the ability to hide controls, and much more.',
                        'ml-slider'
                    ) . "</p>",
                    "<a class='probutton button button-primary button-hero' href='{$link}' target='_blank'>" . esc_html__(
                        'Find out more about MetaSlider Pro',
                        'ml-slider'
                    ) . "<span class='dashicons dashicons-external'></span></a>",
                    "</div>"
                )
            );
        }

        /**
         * Return the MetaSlider pro upgrade iFrame
         */
        public function upgrade_to_pro_tab_post_feed()
        {
            if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
                return wp_iframe(array($this, 'upgrade_to_pro_iframe_post_feed'));
            }
        }

        /**
         * Media Manager iframe HTML - post_feed
         */
        public function upgrade_to_pro_iframe_post_feed()
        {
            $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
            $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-feed&amp;utm_campaign=pro';
            $this->upgrade_to_pro_iframe(
                array(
                    '<div class="left"><img src="' . esc_url(METASLIDER_ADMIN_URL . 'images/upgrade/post-feed.png') . '" alt="" /></div>',
                    "<div><h2>" . esc_html__(
                        'Create slideshows with your posts',
                        'ml-slider'
                    ) . "</h2>",
                    "<p>" . esc_html__(
                        'With Post Feed slides, you can build slideshows with your latest posts, events, or WooCommerce products.',
                        'ml-slider'
                    ) . "</p>",
                    "<p>" . esc_html__(
                        'Post Feed slides will automatically display your posts with images, text, custom fields, and much more.',
                        'ml-slider'
                    ) . "</p>",
                    '<a class="probutton button button-primary button-hero" href="' . esc_url(
                        $link
                    ) . '" target="_blank">' . esc_html__(
                        "Find out more about MetaSlider Pro",
                        "ml-slider"
                    ) . '<span class="dashicons dashicons-external"></span></a>',
                    "</div>"
                )
            );
        }

        /**
         * Return the MetaSlider pro upgrade iFrame
         */
        public function upgrade_to_pro_tab_external_url()
        {
            if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
                return wp_iframe(array($this, 'upgrade_to_pro_iframe_external_url'));
            }
        }

        /**
         * Media Manager iframe HTML - External URL
         */
        public function upgrade_to_pro_iframe_external_url()
        {
            $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
            $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-external&amp;utm_campaign=pro';
            $this->upgrade_to_pro_iframe(
                array(
                    '<div class="left"><img src="' . esc_url(METASLIDER_ADMIN_URL . 'images/upgrade/external-url.png') . '" alt="" /></div>',
                    "<div ><h2>" . esc_html__(
                        'Create slideshows with images stored outside of WordPress',
                        'ml-slider'
                    ) . "</h2>",
                    "<p>" . esc_html__(
                        'With External URL slides, you can load images directly from non-WordPress sources such as CDNs or image hosts.',
                        'ml-slider'
                    ) . "</p>",
                    "<p>" . esc_html__(
                        'External URL slides give you all the power of MetaSlider, using images stored anywhere you want.',
                        'ml-slider'
                    ) . "</p>",
                    '<a class="probutton button button-primary button-hero" href="' . esc_url(
                        $link
                    ) . '" target="_blank">' . esc_html__(
                        "Find out more about MetaSlider Pro",
                        "ml-slider"
                    ) . '<span class="dashicons dashicons-external"></span></a>',
                    "</div>"
                )
            );
        }

        /**
         * Return the MetaSlider pro upgrade iFrame
         */
        public function upgrade_to_pro_tab_local_video()
        {
            if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
                return wp_iframe(array($this, 'upgrade_to_pro_iframe_local_video'));
            }
        }

        /**
         * Media Manager iframe HTML - Local video
         */
        public function upgrade_to_pro_iframe_local_video()
        {
            $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
            $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-video&amp;utm_campaign=pro';
            $this->upgrade_to_pro_iframe(
                array(
                    '<div class="left"><img src="' . esc_url(METASLIDER_ADMIN_URL . 'images/upgrade/local-video.png') . '" alt="" /></div>',
                    "<div ><h2>" . esc_html__(
                        'Create slideshows with videos in your media library',
                        'ml-slider'
                    ) . "</h2>",
                    "<p>" . esc_html__(
                        'With Local Video slides, you can build beautiful slideshows with videos in your WordPress media library.',
                        'ml-slider'
                    ) . "</p>",
                    "<p>" . esc_html__(
                        'Local Video slides will display your MP4, WebM, and MOV videos with cover images, auto play, mute, lazy load, the ability to hide controls, and much more.',
                        'ml-slider'
                    ) . "</p>",
                    '<a class="probutton button button-primary button-hero" href="' . esc_url(
                        $link
                    ) . '" target="_blank">' . esc_html__(
                        "Find out more about MetaSlider Pro",
                        "ml-slider"
                    ) . '<span class="dashicons dashicons-external"></span></a>',
                    "</div>"
                )
            );
        }

         /**
         * Return the MetaSlider pro upgrade iFrame
         */
        public function upgrade_to_pro_tab_external_video()
        {
            if (function_exists('is_plugin_active') && ! is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
                return wp_iframe(array($this, 'upgrade_to_pro_iframe_external_video'));
            }
        }

        /**
         * Media Manager iframe HTML - External video
         */
        public function upgrade_to_pro_iframe_external_video()
        {
            $link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
            $link .= '?utm_source=lite&amp;utm_medium=more-slide-types-external-video&amp;utm_campaign=pro';
            $this->upgrade_to_pro_iframe(
                array(
                    '<div class="left"><img src="' . esc_url(METASLIDER_ADMIN_URL . 'images/upgrade/external-video.png') . '" alt="" /></div>',
                    "<div ><h2>" . esc_html__(
                        'Create slideshows with videos stored outside of WordPress',
                        'ml-slider'
                    ) . "</h2>",
                    "<p>" . esc_html__(
                        'With External Video slides, you can add videos directly from non-WordPress sources.',
                        'ml-slider'
                    ) . "</p>",
                    "<p>" . esc_html__(
                        'External Video Slides will display your MP4, WebM, and MOV videos. Features include text captions, cover images, auto play, mute, lazy load, the ability to hide controls, and much more.',
                        'ml-slider'
                    ) . "</p>",
                    '<a class="probutton button button-primary button-hero" href="' . esc_url(
                        $link
                    ) . '" target="_blank">' . esc_html__(
                        "Find out more about MetaSlider Pro",
                        "ml-slider"
                    ) . '<span class="dashicons dashicons-external"></span></a>',
                    "</div>"
                )
            );
        }

        /**
         * Upgrade to pro Iframe - Render
         *
         * @param array $content The HTML to render
         */
        public function upgrade_to_pro_iframe($content)
        {
            echo "<div class='ms-panel-container-ad'>";
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo implode("", $content);
            echo "</div>";
        }

        /**
         * Adds extra links to the plugin activation page
         *
         * @param array $meta Extra meta links
         * @param string $file Specific file to compare against the base plugin
         * @param string $data Data for the meat links
         * @param string $status Staus of the meta links
         * @return array          Return the meta links array
         */
        public function get_extra_meta_links($meta, $file, $data, $status)
        {
            if (plugin_basename(__FILE__) == $file) {
                $plugin_page = admin_url('admin.php?page=metaslider');
                $nonce = wp_create_nonce('metaslider_request');
                if (metaslider_pro_is_installed()) {
                    $meta[] = "<a href='https://www.metaslider.com/support/' target='_blank'>" . esc_html__(
                            'Premium Support',
                            'ml-slider'
                        ) . "</a>";
                } else {
                    $upgrade_link = apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade/');
                    $meta[] = '<a href="' . esc_url($upgrade_link) . '" target="_blank">' . esc_html__(
                            'MetaSlider Pro',
                            'ml-slider'
                        ) . "</a>";
                    $meta[] = "<a href='https://wordpress.org/support/plugin/ml-slider/' target='_blank'>" . esc_html__(
                            'Support',
                            'ml-slider'
                        ) . "</a>";
                }
                $meta[] = "<a href='https://www.metaslider.com/documentation/' target='_blank'>" . esc_html__(
                        'Documentation',
                        'ml-slider'
                    ) . "</a>";
                $meta[] = "<a href='https://wordpress.org/support/plugin/ml-slider/reviews#new-post' target='_blank' title='" . esc_attr__(
                        'Leave a review',
                        'ml-slider'
                    ) . "'><i class='ml-stars'><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg></i></a>";
            }
            return $meta;
        }

        /**
         * Adds styles to admin head to allow for stars animation and coloring
         */
        public function add_star_styles()
        {
            if (metaslider_user_is_on_admin_page('plugins.php')) { ?>
                <style>
                    .ml-stars {
                        display: inline-block;
                        color: #ffb900;
                        position: relative;
                        top: 3px
                    }

                    .ml-stars svg {
                        fill: #ffb900
                    }

                    .ml-stars svg:hover {
                        fill: #ffb900
                    }

                    .ml-stars svg:hover ~ svg {
                        fill: none
                    }
                </style>
            <?php
            }
        }

        /**
         * Add a 'property=stylesheet' attribute to the MetaSlider CSS links for HTML5 validation
         *
         * @param string $tag Specifies tag
         * @param string $handle Checks for the handle to add property to
         * @return string
         * @since 3.3.4
         */
        public function add_property_attribute_to_stylesheet_links($tag, $handle)
        {
            if (strpos($handle, 'metaslider') !== false && strpos($tag, "property='") === false) {
                // we're only filtering tags with metaslider in the handle, and links which don't already have a property attribute
                $tag = str_replace("/>", "property='stylesheet' />", $tag);
            }

            return $tag;
        }


        public function quickstart_params(){ 
            if (isset($_REQUEST['page']) && 'metaslider-start' == $_REQUEST['page']) {
                $plupload_init = array(
                  'runtimes'            => 'html5,silverlight,flash,html4',
                  'browse_button'       => 'plupload-browse-button',
                  'container'           => 'plupload-upload-ui',
                  'drop_element'        => 'drag-drop-area',
                  'file_data_name'      => 'async-upload',            
                  'multiple_queues'     => true,
                  'max_file_size'       => wp_max_upload_size().'b',
                  'url'                 => admin_url('admin-ajax.php'),
                  'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
                  'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                  'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
                  'multipart'           => true,
                  'urlstream_upload'    => true,
                  'multipart_params'    => array(
                    '_wpnonce' =>  wp_create_nonce('metaslider_quickstart_upload'),
                    'action'      => 'quickstart_upload'
                  ),
                );
          ?>  
           <script type="text/javascript">
            jQuery(document).ready(function($){
                var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);
                uploader.bind('Init', function(up){
                  var uploaddiv = $('#plupload-upload-ui');
                  if(up.features.dragdrop){
                    uploaddiv.addClass('drag-drop');
                      $('#drag-drop-area')
                        .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                        .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });
                  }else{
                    uploaddiv.removeClass('drag-drop');
                    $('#drag-drop-area').unbind('.wp-uploader');
                  }
                });
                uploader.init();
                uploader.bind('FilesAdded', function(up, files){
                  var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
                  plupload.each(files, function(file){
                    if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
                     $("#quickstart-status").html("Error");
                    }else{
                        $("#media-items").append('<div class="media-item child-of-0 open"><div class="media-item-wrapper"><div class="attachment-details"><div class="filename new"><span class="media-list-title"><strong>'+file.name+'</strong></span><span class="media-list-subtitle"></span></div></div><div class="attachment-tools"><span class="media-item-copy-container copy-to-clipboard-container edit-attachment"></span><b id="'+file.id+'"><div class="progress"><div class="percent">100%</div><div class="bar" style="width: 200px;"></div></div></b></div></div></div>');
                    }
                  })
                  up.refresh();
                  up.start();
                });
                uploader.bind('FileUploaded', function(up, file, response) {
                    $("#"+file.id).text("Upload Complete");
                    $("#"+file.id).attr("data-slide", response.response);
                });
                uploader.bind("UploadComplete", function (up, files, response) {
                    $("#media-items").append('<div class="updated below-h2" id="message"><p>Creating slideshow.</p></div>');
                    var file_details = [];
                    $.each(files, function(key, value) {
                        file_details.push($("#"+value.id).data("slide"));
                    });
                    var data = {
                        action: 'quickstart_slideshow',
                        images: file_details,
                        _wpnonce: metaslider.quickstart_slideshow_nonce
                    };
                    $.ajax({
                        url: metaslider.ajaxurl,
                        data: data,
                        type: 'POST',
                        success: function (response) {
                            window.location.href = '<?php echo esc_url_raw(admin_url("admin.php?page=metaslider&id=")); ?>' + response
                        }
                    })
                });
             });   
           </script>
           <?php
            }
        }

        public function ajax_quickstart_slideshow() {
            if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(
                sanitize_key($_REQUEST['_wpnonce']),
                'metaslider_quickstart_slideshow'
            )) {
                wp_send_json_error(array(
                    'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
                ), 401);
            }

            $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                wp_send_json_error(
                    [
                        'message' => __('Access denied', 'ml-slider')
                    ],
                    403
                );
            }

            if (!isset($_POST['images'])) {
                wp_send_json_error(
                    [
                        'message' => __('Bad request', 'ml-slider'),
                    ],
                    400
                );
            }

            $images = $_POST['images'];
            $slideshow_id = MetaSlider_Slideshows::create();
            $slide = new MetaImageSlide();
            foreach ($images as $image) {
                $data = array('id' => (int)$image, 'type' => 'image');
                $slide->add_slide($slideshow_id, $data);
            }

            wp_send_json($slideshow_id);
            wp_die();
        }

        public function ajax_quickstart_upload(){
            if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(
                sanitize_key($_REQUEST['_wpnonce']),
                'metaslider_quickstart_upload'
            )) {
                wp_send_json_error(array(
                    'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
                ), 401);
            }

            $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                wp_send_json_error(
                    [
                        'message' => __('Access denied', 'ml-slider')
                    ],
                    403
                );
            }
            
            if (!isset($_FILES['async-upload'])) {
                wp_send_json_error(
                    [
                        'message' => __('Bad request', 'ml-slider'),
                    ],
                    400
                );
            }
            $file = $_FILES['async-upload'];
            $wp_upload_dir = wp_upload_dir();
            $uploaded = wp_handle_upload($file, array('test_form'=> false, 'action' => 'quickstart_upload'));
            $filename = $uploaded['url'];
            $filetype = wp_check_filetype(basename($filename), null);
            $attachment = array(
                'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content'   => '',
                'post_excerpt'   => '',
                'post_status'    => 'publish'
            );
            $attach_id = wp_insert_attachment($attachment, $filename);
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            $update_data = wp_update_attachment_metadata($attach_id, $attach_data);

            $settings = MetaSlider_Slideshow_Settings::defaults();
            $image_cropper = new MetaSliderImageHelper(
                $attach_id,
                $settings['width'],
                $settings['height'],
                isset($settings['smartCrop']) ? $settings['smartCrop'] : 'false'
            );
            $newurl = $image_cropper->get_image_url();
    
            if (is_wp_error($attach_id)) {
                wp_send_json_error(array(
                    'message' => $attach_id->get_error_message()
                ), 409);
            }

            if (is_wp_error($attach_data)) {
                wp_send_json_error(array(
                    'message' => $attach_data->get_error_message()
                ), 409);
            }

            if (is_wp_error($update_data)) {
                wp_send_json_error(array(
                    'message' => $update_data->get_error_message()
                ), 409);
            }

            wp_send_json($attach_id);
            wp_die(); 
        }
    }

    if (! class_exists('MetaSlider_Settings')) {
        require_once __DIR__ . '/admin/support/Settings.php';
    }

    // Load in MetaGallery unless the user wants to use the dev version
    if (! defined('METAGALLERY_ENABLE_DEV')) {
        define('METAGALLERY_ENABLE_DEV', false);
    }

    $global_settings = get_option( 'metaslider_global_settings' );

    if ( file_exists(__DIR__ . '/metagallery/metagallery.php') 
        && ! METAGALLERY_ENABLE_DEV 
        && isset( $global_settings['gallery'] )
        && (bool) $global_settings['gallery']
    ) {
        if (! defined('METAGALLERY_TEXTDOMAIN')) {
            define('METAGALLERY_SIDELOAD_FROM', 'metaslider');
            define('METAGALLERY_TEXTDOMAIN', 'ml-slider');
            define('METAGALLERY_PATH', plugin_dir_path(__FILE__) . 'metagallery/');
            define('METAGALLERY_BASE_URL', plugin_dir_url(__FILE__) . 'metagallery/');
            require plugin_dir_path(__FILE__) . 'metagallery/metagallery.php';
        }
    }
}

add_action('plugins_loaded', array(MetaSliderPlugin::get_instance(), 'setup'), 10);

function metaslider_activate() {
    add_option( 'metaslider_activate', true );
}
register_activation_hook( __FILE__, 'metaslider_activate' );
