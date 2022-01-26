<?php
defined('ABSPATH') || die;


/**
 * Main class of Gutenberg Advanced
 */
if(!class_exists('AdvancedGutenbergMain')) {
    class AdvancedGutenbergMain
    {
        /**
         * Default role access
         *
         * @var array   Array of default role access
         */
        public static $default_roles_access = array('administrator', 'editor', 'author', 'contributor', 'subscriber');

        /**
         * Default active all blocks
         *
         * @var string  All blocks
         */
        public static $default_active_blocks = 'all';

        /**
         * Default custom styles
         *
         * @var array   Default custom styles for first install
         */
        public static $default_custom_styles = array(
            0 => array(
                'id' => 1,
                'title' => 'Blue message',
                'name' => 'blue-message',
                'identifyColor' => '#3399ff',
                'css' => 'background: none repeat scroll 0 0 #3399ff;
    color: #ffffff;
    text-shadow: none;
    font-size: 16px;
    line-height: 24px;
    padding: 10px;'
            ),
            1 => array(
                'id' => 2,
                'title' => 'Green message',
                'name' => 'green-message',
                'identifyColor' => '#8cc14c',
                'css' => 'background: none repeat scroll 0 0 #8cc14c;
    color: #ffffff;
    text-shadow: none;
    font-size: 16px;
    line-height: 24px;
    padding: 10px;'
            ),
            2 => array(
                'id' => 3,
                'title' => 'Orange message',
                'name' => 'orange-message',
                'identifyColor' => '#faa732',
                'css' => 'background: none repeat scroll 0 0 #faa732;
    color: #ffffff;
    text-shadow: none;
    font-size: 16px;
    line-height: 24px;
    padding: 10px;'
            ),
            3 => array(
                'id' => 4,
                'title' => 'Red message',
                'name' => 'red-message',
                'identifyColor' => '#da4d31',
                'css' => 'background: none repeat scroll 0 0 #da4d31;
    color: #ffffff;
    text-shadow: none;
    font-size: 16px;
    line-height: 24px;
    padding: 10px;'
            ),
            4 => array(
                'id' => 5,
                'title' => 'Grey message',
                'name' => 'grey-message',
                'identifyColor' => '#53555c',
                'css' => 'background: none repeat scroll 0 0 #53555c;
    color: #ffffff;
    text-shadow: none;
    font-size: 16px;
    line-height: 24px;
    padding: 10px;'
            ),
            5 => array(
                'id' => 6,
                'title' => 'Left block',
                'name' => 'left-block',
                'identifyColor' => '#ff00ff',
                'css' => 'background: none repeat scroll 0 0px, radial-gradient(ellipse at center center, #ffffff 0%, #f2f2f2 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
    color: #8b8e97;
    padding: 10px;
    margin: 10px;
    float: left;'
            ),
            6 => array(
                'id' => 7,
                'title' => 'Right block',
                'name' => 'right-block',
                'identifyColor' => '#00ddff',
                'css' => 'background: none repeat scroll 0 0px, radial-gradient(ellipse at center center, #ffffff 0%, #f2f2f2 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
    color: #8b8e97;
    padding: 10px;
    margin: 10px;
    float: right;'
            ),
            7 => array(
                'id' => 8,
                'title' => 'Blockquotes',
                'name' => 'blockquotes',
                'identifyColor' => '#cccccc',
                'css' => 'background: none;
    border-left: 5px solid #f1f1f1;
    color: #8B8E97;
    font-size: 16px;
    font-style: italic;
    line-height: 22px;
    padding-left: 15px;
    padding: 10px;
    width: 60%;
    float: left;'
            )
        );

        /**
         * Store original editor settings value, before we modify it to allow/hide blocks based on user roles
         *
         * @var string Original settings
         */
        protected static $original_block_editor_settings;

        /**
         * AdvancedGutenbergMain constructor.
         */
        public function __construct()
        {
            global $wp_version;

            add_action('init', array($this, 'registerPostMeta'));
            add_action('admin_init', array($this, 'registerStylesScripts'));
            add_action('wp_enqueue_scripts', array($this, 'registerStylesScriptsFrontend'));
            add_action('enqueue_block_assets', array($this, 'addEditorAndFrontendStyles'), 9999);
            add_action('plugins_loaded', array($this, 'advgbBlockLoader'));
            add_action('rest_api_init', array($this, 'registerRestAPI'));
            add_action('admin_print_scripts', array($this, 'disableAllAdminNotices')); // Disable all admin notice for page belong to plugin
            add_action('wp_login_failed', array($this, 'handleLoginFailed'));
            add_filter('safe_style_css', array($this, 'addAllowedInlineStyles'), 10, 1);
            add_filter('wp_kses_allowed_html', array($this, 'addAllowedTags'), 1);

            // Front-end ajax
            add_action('wp_ajax_advgb_contact_form_save', array($this, 'saveContactFormData'));
            add_action('wp_ajax_nopriv_advgb_contact_form_save', array($this, 'saveContactFormData'));
            add_action('wp_ajax_advgb_newsletter_save', array($this, 'saveNewsletterData'));
            add_action('wp_ajax_nopriv_advgb_newsletter_save', array($this, 'saveNewsletterData'));
            add_action('wp_ajax_advgb_lores_validate', array($this, 'validateLoresForm'));
            add_action('wp_ajax_nopriv_advgb_lores_validate', array($this, 'validateLoresForm'));

            if (is_admin()) {
                add_action('admin_init', array($this, 'registerAdvgbProfile'));
                add_action('admin_footer', array($this, 'initBlocksList'));
                add_action('admin_menu', array($this, 'registerMainMenu'));
                add_action('admin_menu', array($this, 'registerBlockConfigPage'));
                add_action('load-toplevel_page_advgb_main', array($this, 'saveAdvgbData'));
                add_action('enqueue_block_editor_assets', array($this, 'addEditorAssets'), 9999);
                add_filter('mce_external_plugins', array($this, 'addTinyMceExternal'));
                add_filter('mce_buttons_2', array($this, 'addTinyMceButtons'));
                add_filter('admin_body_class', array($this, 'setAdvgEditorBodyClassses'));

                if($wp_version >= 5.8) {
                    add_action('admin_enqueue_scripts', array($this, 'addEditorAssetsWidgets'), 9999);
                    add_filter('block_editor_settings_all', array($this, 'replaceEditorSettings'), 9999);

                    if( $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                        add_filter('block_categories_all', array($this, 'addAdvBlocksCategory'));
                    }
                } else {
                    add_filter('block_editor_settings', array($this, 'replaceEditorSettings'), 9999);

                    if( $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                        add_filter('block_categories', array($this, 'addAdvBlocksCategory'));
                    }
                }

                // Ajax
                add_action('wp_ajax_advgb_update_blocks_list', array($this, 'updateBlocksList'));
                add_action('wp_ajax_advgb_get_users', array($this, 'getUsers'));
                add_action('wp_ajax_advgb_custom_styles_ajax', array($this, 'customStylesAjax'));
                add_action('wp_ajax_advgb_delete_profiles', array($this, 'deleteProfiles'));
                add_action('wp_ajax_advgb_block_config_save', array($this, 'saveBlockConfig'));
            } else {
                // Front-end
                add_filter('render_block_data', array($this, 'contentPreRender'));
                add_filter('the_content', array($this, 'addFrontendContentAssets'), 9);

                if($wp_version >= 5.8) {
                    add_filter('widget_block_content', array($this, 'addFrontendWidgetAssets'), 9);
                }
            }
        }

        /**
         * Disable all admin notices in our page
         *
         * @return void
         */
        public function disableAllAdminNotices()
        {
            global $wp_filter;
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action, nonce is not required
            if ((!empty($_GET['page']) && in_array($_GET['page'], array('advgb_main')))) {
                if (is_user_admin()) {
                    if (isset($wp_filter['user_admin_notices'])) {
                        unset($wp_filter['user_admin_notices']);
                    }
                } elseif (isset($wp_filter['admin_notices'])) {
                    unset($wp_filter['admin_notices']);
                }
                if (isset($wp_filter['all_admin_notices'])) {
                    unset($wp_filter['all_admin_notices']);
                }
            }
        }

        /**
         * Add blocks category
         *
         * @param array $categories List of current blocks categories
         *
         * @return array New array include our category
         */
        public function addAdvBlocksCategory($categories)
        {
            return in_array(
                    'advgb-category', wp_list_pluck( $categories, 'slug' ), true
                ) ? $categories : array_merge(
                    array(
                        array(
                            'slug' => 'advgb-category',
                            'title' => __('PublishPress Blocks', 'advanced-gutenberg'),
                        ),
                    ),
                    $categories
            );
        }

        /**
         * Add inline styles allowed for user without unfiltered_html capability
         *
         * @param array $styles List of current allowed styles
         *
         * @return array New list of allowed styles
         */
        public function addAllowedInlineStyles($styles)
        {
            return array_merge(
                $styles,
                array('justify-content', 'align-items', 'border-radius')
            );
        }

        /**
         * Add allowed tags for user without unfiltered_html capability
         *
         * @param array $tags List of current allowed tags
         *
         * @return array New list of allowed tags
         */
        public function addAllowedTags($tags)
        {
            $tags['svg'] = array(
                'width'                 => true,
                'height'                => true,
                'viewbox'               => true,
                'xmlns'                 => true,
                'fill'                  => true,
                'styles'                => true,
                'preserveAspectRatio'   => true,
            );
            $tags['g'] = array(
                'fill'          => true,
                'fill-rule'     => true,
                'fill-opacity'  => true,
                'stroke'        => true,
                'stroke-width'  => true,
            );
            $tags['path'] = array(
                'd'             => true,
                'fill'          => true,
                'fill-opacity'  => true,
            );

            return $tags;
        }

        /**
         * Replaces if needed editor settings to allow/hide blocks
         *
         * @param array $settings Editor settings
         *
         * @return array
         */
        public function replaceEditorSettings($settings)
        {
            self::$original_block_editor_settings = $settings;

            $advgb_blocks_vars = array();

            if( $this->settingIsEnabled( 'enable_block_access' ) ) {
                $advgb_blocks_vars['blocks'] = $this->getUserBlocksForGutenberg();
            }

            // No Block Access defined for this role, so we define empty arrays
            if( !isset( $advgb_blocks_vars['blocks']['active_blocks'] ) && empty( $advgb_blocks_vars['blocks']['active_blocks'] ) ) {
                $advgb_blocks_vars['blocks']['active_blocks']   = array();
                $advgb_blocks_vars['blocks']['inactive_blocks'] = array();
            }

            if ( is_array($settings['allowedBlockTypes']) ) {
                // Remove blocks from the list that are not allowed
                // Note that we do not add missing blocks, because another plugin may have used the hook to remove some of them
                foreach ($settings['allowedBlockTypes'] as $key => $type) {
                    if (in_array($type, $advgb_blocks_vars['blocks']['inactive_blocks'])) {
                        unset($settings['allowedBlockTypes'][$key]);
                    }
                }
            } elseif ($settings['allowedBlockTypes'] === true) {
                // All was allowed, only return what the profile allows

                if ( count($advgb_blocks_vars['blocks']['active_blocks']) || count($advgb_blocks_vars['blocks']['inactive_blocks']) ) {
                    $settings['allowedBlockTypes'] = $advgb_blocks_vars['blocks']['active_blocks'];
                }
            }

            $current_screen = get_current_screen();
            if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()
                && (!defined('GUTENBERG_VERSION') || (defined('GUTENBERG_VERSION') && version_compare(GUTENBERG_VERSION, '5.3.0', '>=')))) {
                // WP 5 and Gutenberg 5.3.0 fires enqueue_block_editor_assets before block_editor_settings, Gutenberg plugin do the contrary
                // Gutenberg WP5 core feature is used and we are in the block editor page, we must enqueue our assets after retrieving editor settings
                $this->addEditorAssets(true);
            }

            return $settings;
        }

        /**
         * Enqueue styles and scripts for gutenberg
         *
         * @param boolean $force_loading Should force loading assets
         *
         * @return void
         */
        public function addEditorAssets($force_loading = false)
        {
            $current_screen = get_current_screen();
            if (!$force_loading && method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()
                && (!defined('GUTENBERG_VERSION') || (defined('GUTENBERG_VERSION') && version_compare(GUTENBERG_VERSION, '5.3.0', '>=')))) {
                // This function will be called manually in the block_editor_settings filter
                // WP 5 and Gutenberg 5.3.0 fires enqueue_block_editor_assets before block_editor_settings, Gutenberg plugin do the contrary
                return;
            }

            $this->enqueueEditorAssets();
            $this->advgbBlocksVariables();
        }

        /**
         * Enqueue styles and scripts for gutenberg in widgets.php
         *
         * @param int $hook Hook suffix for the current admin page
         *
         * @return void
         */
        public function addEditorAssetsWidgets($hook)
        {
            if ( 'widgets.php' != $hook ) {
                return;
            }

            $this->enqueueEditorAssets();
            $this->advgbBlocksVariables(false);
            $this->advgbDisableBlocksWidgetAreas();
        }

        /**
         * Enqueue styles and scripts for gutenberg
         *
         * @return void
         */
        public function enqueueEditorAssets()
        {
            if( $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                wp_enqueue_script(
                    'advgb_blocks',
                    plugins_url('assets/blocks/blocks.js', dirname(__FILE__)),
                    array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-data', 'wp-editor', 'wp-plugins', 'wp-compose' ),
                    ADVANCED_GUTENBERG_VERSION,
                    true
                );
            }

            $currentScreen = get_current_screen();

            // Don't load custom-styles.js in widgets.php and Theme Customizer > Widgets
            if(
                $this->settingIsEnabled( 'enable_custom_styles' )
                && $currentScreen->id !== 'widgets'
                && is_customize_preview() === false
            ) {
                wp_enqueue_script(
                    'advgb_custom_styles_script',
                    plugins_url('assets/blocks/custom-styles.js', dirname(__FILE__)),
                    array( 'wp-blocks' ),
                    ADVANCED_GUTENBERG_VERSION,
                    true
                );
            }

            // Don't load post-sidebar.js in widgets.php and Theme Customizer > Widgets
            if( $currentScreen->id !== 'widgets' && is_customize_preview() === false ) {
                wp_enqueue_script(
                    'advgb_post_sidebar',
                    plugins_url('assets/blocks/post-sidebar.js', dirname(__FILE__)),
                    array( 'wp-blocks' ),
                    ADVANCED_GUTENBERG_VERSION,
                    true
                );
            }

            // Pro
            if( defined( 'ADVANCED_GUTENBERG_PRO' ) && $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_enqueue_scripts_editor' ) ) {
                    PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_enqueue_scripts_editor();
                }
            }

            // Include needed JS libraries
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('slick_js');
            wp_enqueue_script('advgb_masonry_js');

            // Include needed CSS styles
            wp_enqueue_style('material_icon_font');
            wp_enqueue_style('material_icon_font_custom');
            wp_enqueue_style('slick_style');
            wp_enqueue_style('slick_theme_style');
        }

        /**
         * Output blocks data and settings as javascript objects (advgb_blocks_var, advgbBlocks and advgbDefaultConfig)
         *
         * @param boolean $post When the blocks load in post edit screen
         *
         * @return void
         */
        public function advgbBlocksVariables($post = true)
        {
            $advgb_blocks_vars = array();

            if( $this->settingIsEnabled( 'enable_block_access' ) ) {
                $advgb_blocks_vars['blocks'] = $this->getUserBlocksForGutenberg();
            }

            // No Block Access defined for this role, so we define empty arrays
            if( !isset( $advgb_blocks_vars['blocks']['active_blocks'] ) && empty( $advgb_blocks_vars['blocks']['active_blocks'] ) ) {
                $advgb_blocks_vars['blocks']['active_blocks']   = array();
                $advgb_blocks_vars['blocks']['inactive_blocks'] = array();
            }

            global $post;
            if ($post) {
                $advgb_blocks_vars['post_id'] = $post->ID;
                $advgb_blocks_vars['post_type'] = $post->post_type;
            }

            $advgb_blocks_vars['original_settings'] = self::$original_block_editor_settings;
            $advgb_blocks_vars['ajaxurl'] = admin_url('admin-ajax.php');
            $advgb_blocks_vars['nonce'] = wp_create_nonce('advgb_update_blocks_list');
            wp_localize_script('wp-blocks', 'advgb_blocks_vars', $advgb_blocks_vars);

            // Set variable needed by blocks editor
            $avatarHolder           = plugins_url('assets/blocks/testimonial/avatar-placeholder.png', ADVANCED_GUTENBERG_PLUGIN);
            $default_thumb          = plugins_url('assets/blocks/recent-posts/recent-post-default.png', ADVANCED_GUTENBERG_PLUGIN);
            $image_holder           = plugins_url('assets/blocks/advimage/imageholder.svg', ADVANCED_GUTENBERG_PLUGIN);
            $login_logo             = plugins_url('assets/blocks/login-form/login.svg', ADVANCED_GUTENBERG_PLUGIN);
            $reg_logo               = plugins_url('assets/blocks/login-form/reg.svg', ADVANCED_GUTENBERG_PLUGIN);
            $saved_settings         = get_option('advgb_settings');
            $custom_styles_data     = get_option('advgb_custom_styles');
            $recaptcha_config       = get_option('advgb_recaptcha_config');
            $recaptcha_config       = $recaptcha_config !== false ? $recaptcha_config : array('recaptcha_enable' => 0);
            $blocks_icon_color      = isset($saved_settings['blocks_icon_color']) ? $saved_settings['blocks_icon_color'] : '#5952de';
            $rp_default_thumb       = isset($saved_settings['rp_default_thumb']) ? $saved_settings['rp_default_thumb'] : array('url' => $default_thumb, 'id' => 0);
            $icons                  = array();
            $icons['material']      = file_get_contents(plugin_dir_path(__DIR__) . 'assets/css/fonts/codepoints.json');
            $icons['material']      = json_decode($icons['material'], true);
            $enable_advgb_blocks    = !isset($saved_settings['enable_advgb_blocks']) || $saved_settings['enable_advgb_blocks'] ? 1 : 0;

            global $wp_version;
            $blocks_widget_support = ( $wp_version >= 5.8 ) ? 1 : 0;

            wp_localize_script('wp-blocks', 'advgbBlocks', array(
                'color' => $blocks_icon_color,
                'post_thumb' => $rp_default_thumb['url'],
                'default_thumb' => $default_thumb,
                'image_holder' => $image_holder,
                'avatarHolder' => $avatarHolder,
                'login_logo' => $login_logo,
                'reg_logo' => $reg_logo,
                'home_url' => home_url(),
                'config_url' => admin_url('admin.php?page=advgb_main'),
                'customStyles' => !$custom_styles_data ? array() : $custom_styles_data,
                'captchaEnabled' => $recaptcha_config['recaptcha_enable'],
                'pluginUrl' => plugins_url('', ADVANCED_GUTENBERG_PLUGIN),
                'iconList' => $icons,
                'registerEnabled' => get_option('users_can_register'),
                'blocks_widget_support' => $blocks_widget_support,
                'enable_advgb_blocks' => $enable_advgb_blocks,
            ));

            // Setup default config data for blocks
            $blocks_config_saved = get_option('advgb_blocks_default_config');
            $blocks_config_saved = $blocks_config_saved !== false ? $blocks_config_saved : array();
            wp_localize_script('wp-blocks', 'advgbDefaultConfig', $blocks_config_saved);
        }

        /**
         * Enqueue styles for gutenberg editor and front-end
         *
         * @return mixed
         */
        public function addEditorAndFrontendStyles()
        {
            // Load custom styles in the <head>
            if( $this->settingIsEnabled( 'enable_custom_styles' ) ) {
                add_action('wp_head', array($this, 'loadCustomStylesFrontend'));
                add_action('admin_head', array($this, 'loadCustomStylesAdmin'));
            }

            add_action('admin_head', array($this, 'setBlocksSpacingAdmin'));

            wp_enqueue_style('dashicons');

            if ( is_admin() && $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                wp_enqueue_style(
                    'advgb_blocks_styles',
                    plugins_url('assets/css/blocks.css', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_enqueue_style(
                    'advgb_recent_posts_styles',
                    plugins_url('assets/css/recent-posts.css', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );

                // Pro
                if(defined('ADVANCED_GUTENBERG_PRO')) {
                    if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_enqueue_main_styles' ) ) {
                        PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_enqueue_main_styles();
                    }
                }
            }

            wp_enqueue_style(
                'advgb_editor_styles',
                plugins_url('assets/css/editor.css', dirname(__FILE__)),
                array(),
                ADVANCED_GUTENBERG_VERSION
            );

            if (!function_exists('advgbAddScriptAttributes')) {
                /**
                 * Add attributes to script tag
                 *
                 * @param string $tag    Script tag
                 * @param string $handle Handle name
                 *
                 * @return mixed
                 */
                function advgbAddScriptAttributes($tag, $handle)
                {
                    if ('advgb_map_api' === $handle) {
                        return str_replace(' src', ' defer src', $tag);
                    } elseif ('advgb_recaptcha_js' === $handle) {
                        return str_replace(' src', ' async defer src', $tag);
                    }
                    return $tag;
                }
            }
            add_filter('script_loader_tag', 'advgbAddScriptAttributes', 10, 2);

            if (is_admin()) {
                $this->loadGoogleMapApi();
                $this->loadRecaptchaApi();
            }
        }

        /**
         * Update the blocks list for first time install
         *
         * @return void
         */
        public function initBlocksList()
        {
            if (get_option('advgb_blocks_list') === false
                || (defined('GUTENBERG_VERSION') && version_compare(get_option('advgb_gutenberg_version'), GUTENBERG_VERSION, '<'))) {
                $advgb_nonce = wp_create_nonce('advgb_update_blocks_list');
                wp_enqueue_script('wp-blocks');
                wp_enqueue_script('wp-element');
                wp_enqueue_script('wp-data');
                wp_enqueue_script('wp-components');
                wp_enqueue_script('wp-block-library');
                wp_enqueue_script('wp-editor');
                do_action('enqueue_block_editor_assets');
                wp_enqueue_script(
                    'advgb_update_list',
                    plugins_url('assets/js/update-block-list.js', dirname(__FILE__)),
                    array('jquery'),
                    ADVANCED_GUTENBERG_VERSION
                );

                $blockCategories = array();
                if (function_exists('gutenberg_get_block_categories')) {
                    $blockCategories = gutenberg_get_block_categories(get_post());
                } elseif (function_exists('get_block_categories')) {
                    $blockCategories = get_block_categories(get_post());
                }

                wp_add_inline_script(
                    'wp-blocks',
                    sprintf('wp.blocks.setCategories( %s );', wp_json_encode($blockCategories)),
                    'after'
                );

                wp_localize_script('advgb_update_list', 'updateListNonce', array('nonce' => $advgb_nonce));
            }
        }

        /**
         * Unregister Table of Contents block in Widgets page
         *
         * @return void
         */
        public function advgbDisableBlocksWidgetAreas()
        {
            if( $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                wp_add_inline_script(
                    'advgb_blocks',
                    'wp.domReady( function () {
                        if ( wp.data.select("core/blocks").getBlockType( "advgb/summary" ) ) {
                            wp.blocks.unregisterBlockType( "advgb/summary" );
                        }
                    } );'
                );
            }
        }

        /**
         * Load block that required server side render
         *
         * @return void
         */
        public function advgbBlockLoader()
        {
            // Block Content Display
            require_once(plugin_dir_path(dirname(__FILE__)) . 'assets/blocks/recent-posts/block.php');
        }

        /**
         * Register REST API
         *
         * @return void
         */
        public function registerRestAPI()
        {
            // Add author info
            register_rest_field(
                'post',
                'author_meta',
                array(
                    'get_callback' => array($this, 'getAuthorInfo'),
                    'update_callback' => null,
                    'schema' => null,
                )
            );

            // Add post featured img
            register_rest_field(
                'post',
                'featured_img',
                array(
                    'get_callback' => array($this, 'getFeaturedImg'),
                    'update_callback' => null,
                    'schema' => null,
                )
            );

            // Register router to get data for Woo Products block
            if (class_exists('WC_REST_Products_Controller')) {
                include_once(plugin_dir_path(dirname(__FILE__)) . 'assets/blocks/woo-products/controller.php');
                $controller = new AdvgbProductsController();
                $controller->register_routes();
            }
        }

        /**
         * Get post author info for REST API
         *
         * @param array $object API Object
         *
         * @return mixed
         */
        public function getAuthorInfo($object)
        {
            // Get the author name
            $author['display_name'] = get_the_author_meta('display_name', $object['author']);

            // Get the author link
            $author['author_link'] = get_author_posts_url($object['author']);

            // Return the author data
            return $author;
        }

        /**
         * Get featured image link for REST API
         *
         * @param array $object API Object
         *
         * @return mixed
         */
        public function getFeaturedImg($object)
        {
            $featured_img = wp_get_attachment_image_src(
                $object['featured_media'],
                'medium',
                false
            );

            if($featured_img) {
                return $featured_img[0];
            }
        }

        /**
         * Ajax to update blocks list
         *
         * @return mixed
         */
        public function updateBlocksList()
        {
            if (!current_user_can('activate_plugins')) {
                wp_send_json('', 400);
            }

            if (!wp_verify_nonce($_POST['nonce'], 'advgb_update_blocks_list')
                && !wp_verify_nonce($_POST['nonce'], 'advgb_nonce')
            ) {
                wp_send_json('', 400);
            }

            /**
             * Remove slashes on svg icon
             *
             * @param array $block Block to remove slashes
             *
             * @return mixed
             */
            function removeSlashes(array $block)
            {
                $block['icon'] = htmlentities(stripslashes($block['icon']), ENT_QUOTES);
                return $block;
            }

            if (is_array($_POST['blocksList'])) {
                $blocksList  = array_map('removeSlashes', $_POST['blocksList']);
            } else {
                $blocksList  = json_decode(stripslashes($_POST['blocksList']));
            }

            $savedBlocksList = get_option('advgb_blocks_list');
            if (!is_array($savedBlocksList)) {
                $savedBlocksList = array();
            }

            $blocksListName = array();
            $savedBlocksListName = array();

            // Blocks coming from Block Access admin screen form
            foreach ($blocksList as &$block) {
                // Convert object to array
                $block = (array)$block;
                $blocksListName[] = $block['name'];
            }

            // Blocks saved in advgb_blocks_list option
            foreach ($savedBlocksList as $block) {
                // Convert object to array
                $block = (array)$block;
                $savedBlocksListName[] = $block['name'];
            }

            // Check if we have new blocks installed
            $newBlocks = array_diff($blocksListName, $savedBlocksListName);
            if (count($newBlocks)) {
                update_option('advgb_blocks_list', $blocksList);
            }

            // Check that advgb_blocks_user_roles is up to date - The result of this check is not saved
            $advgb_blocks_user_roles            = get_option( 'advgb_blocks_user_roles');
            $advgb_blocks_user_roles_updated    = array();

            foreach ( $advgb_blocks_user_roles as $role => $blocks ) {
                if (is_array($blocks) && is_array($blocks['active_blocks']) && is_array($blocks['inactive_blocks'])) {
                    $allAccessBlocks = array_merge($blocks['active_blocks'], $blocks['inactive_blocks']);

                    $newAllowedBlocks = array_diff($blocksListName, $allAccessBlocks);
                    $newAllowedBlocks = array_unique($newAllowedBlocks);

                    if ($newAllowedBlocks) {
                        $advgb_blocks_user_roles_updated[$role]['active_blocks'] = array_merge($blocks['active_blocks'], $newAllowedBlocks);
                        $advgb_blocks_user_roles_updated[$role]['inactive_blocks'] = $blocks['inactive_blocks'];
                    }
                }
            }

            /* We don't actually need to save the new blocks that are not detected by Access Blocks
            if ($newAllowedBlocks) {
                update_option( 'advgb_blocks_user_roles', $advgb_blocks_user_roles_updated );
            }*/

            if ((defined('GUTENBERG_VERSION')
                && version_compare(get_option('advgb_gutenberg_version'), GUTENBERG_VERSION, '<'))
            ) {
                update_option('advgb_gutenberg_version', GUTENBERG_VERSION);
            }


            wp_send_json(array(
                'blocks_list' => $blocksList
            ), 200);
        }

        /**
         * Ajax to get list of users
         *
         * @return boolean,void     Return false if failure, echo json on success
         */
        public function getUsers()
        {
            // Check users permissions
            if (! current_user_can('create_advgb_profiles')) {
                wp_send_json(__('No permission!', 'advanced-gutenberg'), 403);
                return false;
            }

            // phpcs:disable WordPress.Security.NonceVerification.Recommended -- View request, no action
            $usersearch     = isset($_REQUEST['search']) ? wp_unslash(trim($_REQUEST['search'])) : '';
            $role           = isset($_REQUEST['role']) ? $_REQUEST['role'] : '';
            $users_per_page = 20;
            $pagenum        = 1;
            if ($role === 'none') {
                $args_all = array(
                    'include' => wp_get_users_with_no_role(),
                    'search'  => $usersearch,
                    'fields'  => 'all_with_meta'
                );
            } else {
                $args_all = array(
                    'role'   => $role,
                    'search' => $usersearch,
                    'fields' => 'all_with_meta'
                );
            }
            if ($args_all['search'] !== '') {
                $args_all['search'] = '*' . $args_all['search'] . '*';
            }

            $total_users = count(get_users($args_all));
            $total_pages = (int)ceil($total_users / $users_per_page);
            if (isset($_REQUEST['paged'])) {
                if ($_REQUEST['paged'] === 'first') {
                    $pagenum = 1;
                } elseif ($_REQUEST['paged'] === 'last') {
                    $pagenum = (int)$total_pages;
                } else {
                    $pagenum = (int)$_REQUEST['paged'];
                }
            }
            // phpcs:enable
            $paged = max(1, $pagenum);

            if ($role === 'none') {
                $args = array(
                    'number'  => $users_per_page,
                    'offset'  => ($paged - 1) * $users_per_page,
                    'include' => wp_get_users_with_no_role(),
                    'search'  => $usersearch,
                    'fields'  => 'all_with_meta'
                );
            } else {
                $args = array(
                    'number' => $users_per_page,
                    'offset' => ($paged - 1) * $users_per_page,
                    'role'   => $role,
                    'search' => $usersearch,
                    'fields' => 'all_with_meta'
                );
            }

            if ($args['search'] !== '') {
                $args['search'] = '*' . $args['search'] . '*';
            }

            // Query the user IDs for this page
            $wp_user_search = get_users($args);

            $users_list = '';
            $pages_list = '';

            if (count($wp_user_search)) {
                foreach ($wp_user_search as $userid => $user_object) {
                    $users_list .= '<tr>';
                    $users_list .= '<td class="select-box">';
                    $users_list .= '<input class="ju-checkbox" type="checkbox" name="advgb-users[]" value="' . $userid . '" />';
                    $users_list .= '</td>';
                    $users_list .= '<td class="name column-name">';
                    $users_list .= '<span>' . $user_object->display_name . '</span>';
                    $users_list .= '</td>';
                    $users_list .= '<td class="username column-username">';
                    $users_list .= '<strong>' . $user_object->user_login . '</strong>';
                    $users_list .= '</td>';
                    $users_list .= '<td class="email column-email">' . $user_object->user_email . '</td>';

                    $role_list = array();
                    global $wp_roles;
                    foreach ($user_object->roles as $role) {
                        if (isset($wp_roles->role_names[ $role ])) {
                            $role_list[ $role ] = translate_user_role($wp_roles->role_names[ $role ]);
                        }
                    }

                    if (empty($role_list)) {
                        $role_list['none'] = _x('None', 'no user roles', 'advanced-gutenberg');
                    }
                    $roles_list = implode(', ', $role_list);

                    $users_list .= '<td class="role column-role">' . $roles_list . '</td>';
                    $users_list .= '</tr>';
                }
            } else {
                $users_list .= '<tr><td colspan="5"> ';
                $users_list .= __('No users found.', 'advanced-gutenberg');
                $users_list .= '</td></tr>';
            }

            $doneLeft = false;
            $doneRight = false;
            $skipLeft = false;
            $skipRight = false;
            if ($total_pages > 1) {
                for ($i = 1; $i <= $total_pages; $i ++) {
                    if ($i < $pagenum - 2) {
                        $skipLeft = true;
                    } elseif ($i > $pagenum + 2) {
                        $skipRight = true;
                    } else {
                        $skipLeft  = false;
                        $skipRight = false;
                    }
                    if ($i === 1) {
                        if ($pagenum === 1) {
                            $pages_list .= '<i class="dashicons dashicons-controls-skipback" id="first-page"></i>';
                        } else {
                            $pages_list .= '<a class="dashicons dashicons-controls-skipback" id="first-page" ';
                            $pages_list .= 'title="' . __('First page', 'advanced-gutenberg') . '"></a>';
                        }
                    }
                    if (! $skipLeft && ! $skipRight) {
                        if ($i === $pagenum) {
                            $pages_list .= '<strong>' . $i . '</strong>';
                        } else {
                            $pages_list .= '<a class="switch-page">' . $i . '</a>';
                        }
                    } elseif ($skipLeft) {
                        if (! $doneLeft) {
                            $pages_list .= '<span>...</span>';
                            $doneLeft   = true;
                        }
                    } elseif ($skipRight) {
                        if (! $doneRight) {
                            $pages_list .= '<span>...</span>';
                            $doneRight  = true;
                        }
                    }

                    if ($i === $total_pages) {
                        if ($pagenum === $total_pages) {
                            $pages_list .= '<i class="dashicons dashicons-controls-skipforward" id="last-page"></i>';
                        } else {
                            $pages_list .= '<a class="dashicons dashicons-controls-skipforward" id="last-page" ';
                            $pages_list .= 'title="' . __('Last page', 'advanced-gutenberg') . '"></a>';
                        }
                    }
                }
            }
            $response = array('users_list' => $users_list, 'pages_list' => $pages_list);
            wp_send_json($response);
        }

        /**
         * Ajax for custom styles
         *
         * @return boolean,void     Return false if failure, echo json on success
         */
        public function customStylesAjax()
        {
            // Check users permissions
            if (!current_user_can('activate_plugins')) {
                wp_send_json(__('No permission!', 'advanced-gutenberg'), 403);
                return false;
            }
            $regex = '/^[a-zA-Z0-9_\-]+$/';
            $regexWithSpaces = '/^[\p{L}\p{N}_\- ]+$/u';

            if (!wp_verify_nonce($_POST['nonce'], 'advgb_settings_nonce')) {
                wp_send_json(__('Invalid nonce token!', 'advanced-gutenberg'), 400);
            }

            $check_exist = get_option('advgb_custom_styles');
            if ($check_exist === false) {
                update_option('advgb_custom_styles', $this::$default_custom_styles);
            }

            $custom_style_data = get_option('advgb_custom_styles');
            $task = isset($_POST['task']) ? $_POST['task'] : '';
            if ($task === '') {
                return false;
            } elseif ($task === 'new') {
                $new_style_id = end($custom_style_data);
                $new_style_id = $new_style_id['id'] + 1;
                $new_style_array = array(
                    'id' => $new_style_id,
                    'title' => __('New class', 'advanced-gutenberg'),
                    'name' => __('new-class', 'advanced-gutenberg'),
                    'css' => '',
                    'identifyColor' => '#000000'
                );
                array_push($custom_style_data, $new_style_array);
                update_option('advgb_custom_styles', $custom_style_data);
                wp_send_json($new_style_array);
            } elseif ($task === 'delete') {
                $custom_style_data_delete = get_option('advgb_custom_styles');
                $style_id = (int)$_POST['id'];
                $new_style_deleted_array = array();
                $done = false;
                foreach ($custom_style_data_delete as $data) {
                    if ($data['id'] === $style_id) {
                        $done = true;
                        continue;
                    }
                    array_push($new_style_deleted_array, $data);
                }
                update_option('advgb_custom_styles', $new_style_deleted_array);
                if ($done) {
                    wp_send_json(array('id' => $style_id), 200);
                }
            } elseif ($task === 'copy') {
                $data_saved = get_option('advgb_custom_styles');
                $style_id = (int)$_POST['id'];
                $new_style_copied_array = get_option('advgb_custom_styles');
                $copied_styles = array();
                $new_id = end($new_style_copied_array);
                foreach ($data_saved as $data) {
                    if ($data['id'] === $style_id) {
                        $copied_styles = array(
                            'id' => $new_id['id'] + 1,
                            'title' => sanitize_text_field($data['title']),
                            'name' => sanitize_text_field($data['name']),
                            'css' => $data['css'],
                            'identifyColor' => $data['identifyColor'],
                        );

                        array_push($new_style_copied_array, $copied_styles);
                    }
                }
                update_option('advgb_custom_styles', $new_style_copied_array);
                wp_send_json($copied_styles);
            } elseif ($task === 'preview') {
                $style_id = (int)$_POST['id'];
                $data_saved = get_option('advgb_custom_styles');
                $get_style_array = array();
                foreach ($data_saved as $data) {
                    if ($data['id'] === $style_id) {
                        foreach ($data as $key => $value) {
                            $data[$key] = esc_html($value);
                        }
                        $get_style_array = $data;
                    }
                }
                if (!empty($get_style_array)) {
                    wp_send_json($get_style_array);
                } else {
                    wp_send_json(false, 404);
                }
            } elseif ($task === 'style_save') {
                $style_id = (int)$_POST['id'];
                $new_classname = sanitize_text_field($_POST['name']);
                $new_identify_color = sanitize_text_field($_POST['mycolor']);
                $new_css = $_POST['mycss'];
                // Validate new name
                if (!preg_match($regex, $new_classname)) {
                    wp_send_json('Invalid characters, please enter another!', 403);
                    return false;
                }
                $data_saved = get_option('advgb_custom_styles');
                $new_data_array = array();
                foreach ($data_saved as $data) {
                    if ($data['id'] === $style_id) {
                        $data['name'] = $new_classname;
                        $data['css'] = $new_css;
                        $data['identifyColor'] = $new_identify_color;
                    }
                    array_push($new_data_array, $data);
                }
                update_option('advgb_custom_styles', $new_data_array);
            } elseif ($task === 'edit') {
                $new_title = sanitize_text_field($_POST['title']);
                $style_id = (int)$_POST['id'];
                if (!preg_match($regexWithSpaces, $new_title)) {
                    wp_send_json('Invalid characters, please enter another!', 403);
                    return false;
                }
                $data_saved = get_option('advgb_custom_styles');
                $new_data_array = array();
                foreach ($data_saved as $data) {
                    if ($data['id'] === $style_id) {
                        $data['title'] = $new_title;
                    }
                    array_push($new_data_array, $data);
                }
                update_option('advgb_custom_styles', $new_data_array);
                wp_send_json(array('title' => $new_title), 200);
            } else {
                wp_send_json(null, 404);
            }
        }

        /**
         * Ajax for delete profiles
         *
         * @return boolean,void     Return false if failure, echo json on success
         */
        public function deleteProfiles()
        {
            // Check users permissions
            if (!current_user_can('delete_pages')) {
                wp_send_json(__('No permission!', 'advanced-gutenberg'), 403);
                return false;
            }

            if (!wp_verify_nonce($_POST['pNonce'], 'advgb_profiles_nonce')) {
                wp_send_json(__('Fail to verify nonce!', 'advanced-gutenberg'), 400);
                return false;
            };

            $profiles_to_delete = $_POST['ids'];
            if (count($profiles_to_delete)) {
                $deleted = array();
                foreach ($profiles_to_delete as $profile) {
                    if (wp_delete_post($profile)) {
                        array_push($deleted, $profile);
                    }
                }

                wp_send_json(array('deleted' => $deleted), 200);
            }
        }

        /**
         * Ajax for saving block default config
         *
         * @return boolean,void     Return false if failure, echo json on success
         */
        public function saveBlockConfig()
        {
            // Check users permissions
            if (!current_user_can('activate_plugins')) {
                wp_send_json(__('No permission!', 'advanced-gutenberg'), 403);
                return false;
            }

            if (!wp_verify_nonce($_POST['nonce'], 'advgb_block_config_nonce')) {
                wp_send_json(__('Invalid nonce token!', 'advanced-gutenberg'), 400);
            }

            $blocks_config_saved = get_option('advgb_blocks_default_config');
            if ($blocks_config_saved === false) {
                $blocks_config_saved = array();
            }

            $blockType = sanitize_text_field($_POST['blockType']);
            $settings = $_POST['settings'];

            foreach ($settings as $key => $setting) {
                foreach ($setting as $k => $option) {
                    $option = sanitize_text_field($option);
                    if (is_numeric($option)) {
                        if ($k !== 'lat' && $k !== 'lng') {
                            $option = floatval($option);
                        }
                    }

                    $settings[$key][$k] = $option;
                }
            }

            // Modify settings for social links blocks config
            if ($blockType === 'advgb-social-links') {
                $items = array();
                $settings[$blockType]['items'] = array();

                foreach ($settings[$blockType] as $k => $option) {
                    if (strpos($k, '.')) {
                        $item = explode('.', $k);
                        $items[$item[0]][$item[1]] = $option;
                    }
                }

                foreach ($items as $item) {
                    array_push($settings[$blockType]['items'], $item);
                }
            }

            $blocks_config_saved[$blockType] = $settings[$blockType];

            update_option('advgb_blocks_default_config', $blocks_config_saved);
            wp_send_json(true, 200);
        }

        /**
         * Ajax for saving contact form data
         *
         * @return boolean,void     Return false if failure, echo json on success
         */
        public function saveContactFormData()
        {
            // phpcs:disable -- WordPress.Security.NonceVerification.Recommended - frontend form, no nonce
            if (!isset($_POST['action'])) {
                wp_send_json(__('Bad Request!', 'advanced-gutenberg'), 400);
                return false;
            }

            if (isset($_POST['captcha'])) {
                $recaptcha_config  = get_option('advgb_recaptcha_config');
                if (!isset($recaptcha_config['recaptcha_secret_key']) || !isset($recaptcha_config['recaptcha_site_key'])) {
                    wp_send_json(__('Server error. Try again later!', 'advanced-gutenberg'), 500);
                }

                $captcha = $_POST['captcha'];
                $secret_key = $recaptcha_config['recaptcha_secret_key'];
                $verify = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha}");

                if (!is_array($verify) || !isset($verify['body'])) {
                    wp_send_json(__('Cannot validate captcha', 'advanced-gutenberg'), 400);
                }

                $verified = json_decode($verify['body']);
                if (!$verified->success) {
                    wp_send_json(__('Captcha validation error', 'advanced-gutenberg'), 400);
                }
            }

            $contacts_saved = get_option('advgb_contacts_saved');
            if (!$contacts_saved) $contacts_saved = array();

            $contact_data = array(
                'date'  => sanitize_text_field($_POST['submit_date']),
                'name'  => sanitize_text_field($_POST['contact_name']),
                'email' => sanitize_email($_POST['contact_email']),
                'msg'   => sanitize_textarea_field($_POST['contact_msg']),
            );

            array_push($contacts_saved, $contact_data);

            $saved = update_option('advgb_contacts_saved', $contacts_saved);
            if ($saved) {
                $saved_settings = get_option('advgb_email_sender');
                $website_title  = get_option('blogname');
                $admin_email    = get_option('admin_email');
                $sender_name    = isset($saved_settings['contact_form_sender_name']) && $saved_settings['contact_form_sender_name'] ? $saved_settings['contact_form_sender_name'] : $website_title;
                $sender_email   = isset($saved_settings['contact_form_sender_email']) && $saved_settings['contact_form_sender_email'] ? $saved_settings['contact_form_sender_email'] : $admin_email;
                $email_title    = isset($saved_settings['contact_form_email_title']) && $saved_settings['contact_form_email_title'] ? $saved_settings['contact_form_email_title'] : __('Website Contact', 'advanced-gutenberg');
                $email_receiver = isset($saved_settings['contact_form_email_receiver']) && $saved_settings['contact_form_email_receiver'] ? $saved_settings['contact_form_email_receiver'] : $admin_email;
                $email_header[] = 'Content-Type: text/html; charset=UTF-8';
                $email_header[] = 'From: '.$sender_name.' <'.$sender_email.'>';
                $msg = '<html><body>';
                $msg .= '<h2>'. __('You have received a contact from your website.', 'advanced-gutenberg') .'</h2>';
                $msg .= '<ul>';
                $msg .= '<li><strong>'. __('Name', 'advanced-gutenberg') .': </strong>'. $contact_data['name'] .'</li>';
                $msg .= '<li><strong>'. __('Email', 'advanced-gutenberg') .': </strong>'. $contact_data['email'] .'</li>';
                $msg .= '<li><strong>'. __('Date', 'advanced-gutenberg') .': </strong>'. $contact_data['date'] .'</li>';
                $msg .= '<li><strong>'. __('Message', 'advanced-gutenberg') .': </strong>'. $contact_data['msg'] .'</li>';
                $msg .= '</ul>';
                $msg .= '</body></html>';

                wp_mail($email_receiver, $email_title, $msg, $email_header);
                wp_send_json($contact_data, 200);
            } else {
                wp_send_json(__('Error while sending form. Try again!', 'advanced-gutenberg'), 500);
            }
            // phpcs:enable
        }

        /**
         * Ajax for saving newsletter form data
         *
         * @return boolean,void     Return false if failure, echo json on success
         */
        public function saveNewsletterData()
        {
            // phpcs:disable -- WordPress.Security.NonceVerification.Recommended - frontend form, no nonce
            if (!isset($_POST['action'])) {
                wp_send_json(__('Bad Request!', 'advanced-gutenberg'), 400);
                return false;
            }

            if (isset($_POST['captcha'])) {
                $recaptcha_config  = get_option('advgb_recaptcha_config');
                if (!isset($recaptcha_config['recaptcha_secret_key']) || !isset($recaptcha_config['recaptcha_site_key'])) {
                    wp_send_json(__('Server error. Try again later!', 'advanced-gutenberg'), 500);
                }

                $captcha = $_POST['captcha'];
                $secret_key = $recaptcha_config['recaptcha_secret_key'];
                $verify = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha}");

                if (!is_array($verify) || !isset($verify['body'])) {
                    wp_send_json(__('Cannot validate captcha', 'advanced-gutenberg'), 400);
                }

                $verified = json_decode($verify['body']);
                if (!$verified->success) {
                    wp_send_json(__('Captcha validation error', 'advanced-gutenberg'), 400);
                }
            }

            $newsletter_saved = get_option('advgb_newsletter_saved');
            if (!$newsletter_saved) $newsletter_saved = array();

            $newsletter_data = array(
                'date'  => sanitize_text_field($_POST['submit_date']),
                'fname' => sanitize_text_field($_POST['f_name']),
                'lname' => sanitize_text_field($_POST['l_name']),
                'email' => sanitize_email($_POST['email']),
            );

            array_push($newsletter_saved, $newsletter_data);

            update_option('advgb_newsletter_saved', $newsletter_saved);
            wp_send_json($newsletter_data, 200);
            // phpcs:enable
        }

        /**
         * Ajax for validating login/register form captcha
         *
         * @return boolean,void     Return false if failure, echo json on success
         */
        public function validateLoresForm()
        {
            // phpcs:disable -- WordPress.Security.NonceVerification.Recommended - frontend form, no nonce
            if (!isset($_POST['action'])) {
                wp_send_json(__('Bad Request!', 'advanced-gutenberg'), 400);
                return false;
            }

            if (isset($_POST['captcha'])) {
                $recaptcha_config  = get_option('advgb_recaptcha_config');
                if (!isset($recaptcha_config['recaptcha_secret_key']) || !isset($recaptcha_config['recaptcha_site_key'])) {
                    wp_send_json(__('Server error. Try again later!', 'advanced-gutenberg'), 500);
                }

                $captcha = $_POST['captcha'];
                $secret_key = $recaptcha_config['recaptcha_secret_key'];
                $verify = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha}");

                if (!is_array($verify) || !isset($verify['body'])) {
                    wp_send_json(__('Cannot validate captcha', 'advanced-gutenberg'), 400);
                }

                $verified = json_decode($verify['body']);
                if (!$verified->success) {
                    wp_send_json(__('Captcha validation error', 'advanced-gutenberg'), 400);
                }

                wp_send_json(__('Captcha validated', 'advanced-gutenberg'), 200);
            }

            wp_send_json(__('Captcha is empty', 'advanced-gutenberg'), 400);
            // phpcs:enable
        }

        /**
         * Handle failed login from our login form and redirect to that login page
         *
         * @return void
         */
        public function handleLoginFailed()
        {
            $from_advgb = isset($_POST['advgb_login_form']); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- redirect
            if (!empty($_SERVER['HTTP_REFERER']) && $from_advgb) {
                $redirect = add_query_arg('login', 'failed', $_SERVER['HTTP_REFERER']);
                wp_safe_redirect($redirect);
                exit;
            }
        }

        /**
         * Register back-end styles and script for later use
         *
         * @return void
         */
        public function registerStylesScripts()
        {
            if (!wp_doing_ajax()) {
                // Register CSS
                wp_register_style(
                    'ju_framework_styles',
                    plugins_url('assets/css/style.css', dirname(__FILE__))
                );
                wp_register_style(
                    'advgb_main_style',
                    plugins_url('assets/css/main.css', dirname(__FILE__))
                );
                wp_register_style(
                    'advgb_profile_style',
                    plugins_url('assets/css/profile.css', dirname(__FILE__))
                );
                wp_register_style(
                    'advgb_settings_style',
                    plugins_url('assets/css/settings.css', dirname(__FILE__))
                );
                wp_register_style(
                    'advgb_qtip_style',
                    plugins_url('assets/css/jquery.qtip.css', dirname(__FILE__))
                );
                wp_register_style(
                    'advgb_quirk',
                    plugins_url('assets/css/quirk.css', dirname(__FILE__))
                );
                wp_register_style(
                    'codemirror_css',
                    plugins_url('assets/js/codemirror/lib/codemirror.css', dirname(__FILE__))
                );
                wp_register_style(
                    'codemirror_hint_style',
                    plugins_url('assets/js/codemirror/addon/hint/show-hint.css', dirname(__FILE__))
                );
                wp_register_style(
                    'minicolors_css',
                    plugins_url('assets/css/jquery.minicolors.css', dirname(__FILE__))
                );
                wp_register_style(
                    'waves_styles',
                    plugins_url('assets/css/waves.min.css', dirname(__FILE__))
                );
                wp_register_style(
                    'material_icon_font',
                    plugins_url('assets/css/fonts/material-icons.min.css', dirname(__FILE__))
                );
                wp_register_style(
                    'material_icon_font_custom',
                    plugins_url('assets/css/fonts/material-icons-custom.min.css', dirname(__FILE__))
                );
                wp_register_style(
                    'slick_style',
                    plugins_url('assets/css/slick.css', dirname(__FILE__))
                );
                wp_register_style(
                    'slick_theme_style',
                    plugins_url('assets/css/slick-theme.css', dirname(__FILE__))
                );

                // Free
                if(!defined('ADVANCED_GUTENBERG_PRO')) {
                  wp_enqueue_style(
                      'advgb_top_notice',
                      plugins_url('assets/css/top-notice.css', dirname(__FILE__)),
                      array(),
                      ADVANCED_GUTENBERG_VERSION
                  );
                  wp_enqueue_style(
                      'advgb_pro_admin_popup',
                      plugins_url('assets/css/pro-popup.css', dirname(__FILE__)),
                      array(),
                      ADVANCED_GUTENBERG_VERSION
                  );
                  wp_enqueue_script(
                      'advgb_top_notice_js',
                      plugins_url('assets/js/top-notice.js', dirname(__FILE__)),
                      array(),
                      ADVANCED_GUTENBERG_VERSION
                  );
                }

                // Register JS
                wp_register_script(
                    'advgb_main_js',
                    plugins_url('assets/js/main.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'advgb_update_list',
                    plugins_url('assets/js/update-block-list.js', dirname(__FILE__)),
                    array('jquery'),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'advgb_profile_js',
                    plugins_url('assets/js/profile.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'advgb_block_access_js',
                    plugins_url('assets/js/block-access.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'advgb_settings_js',
                    plugins_url('assets/js/settings.js', dirname(__FILE__)),
                    array('wp-i18n'),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'advgb_custom_styles_js',
                    plugins_url('assets/js/custom-styles.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'velocity_js',
                    plugins_url('assets/js/velocity.min.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'waves_js',
                    plugins_url('assets/js/waves.min.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'tabs_js',
                    plugins_url('assets/js/tabs.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'qtip_js',
                    plugins_url('assets/js/jquery.qtip.min.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'advgb_codemirror_js',
                    plugins_url('assets/js/codemirror/lib/codemirror.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'codemirror_hint',
                    plugins_url('assets/js/codemirror/addon/hint/show-hint.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'codemirror_mode_css',
                    plugins_url('assets/js/codemirror/mode/css/css.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'codemirror_hint_css',
                    plugins_url('assets/js/codemirror/addon/hint/css-hint.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'less_js',
                    plugins_url('assets/js/less.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'minicolors_js',
                    plugins_url('assets/js/jquery.minicolors.min.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'slick_js',
                    plugins_url('assets/js/slick.min.js', dirname(__FILE__)),
                    array('jquery'),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_script(
                    'advgb_masonry_js',
                    plugins_url('assets/js/isotope.pkgd.min.js', dirname(__FILE__)),
                    array('jquery'),
                    ADVANCED_GUTENBERG_VERSION
                );

                /*/ Pro
                if(defined('ADVANCED_GUTENBERG_PRO')) {
                    if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_register_scripts_admin' ) ) {
                        PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_register_scripts_admin();
                    }
                }*/

                $saved_settings = get_option('advgb_settings');
                if (isset($saved_settings['editor_width']) && $saved_settings['editor_width']) {
                    wp_add_inline_style(
                        'dashicons',
                        'body:not(.advgb-editor-width-default) #editor .edit-post-visual-editor__post-title-wrapper > .wp-block,body:not(.advgb-editor-width-default) #editor .block-editor-writing-flow > .block-editor-block-list__layout > .wp-block{max-width:' . $saved_settings['editor_width'] . '%;width:' . $saved_settings['editor_width'] . '%;margin-left:auto;margin-right:auto}'
                    );
                }
            }
        }

        /**
         * Register front-end styles and script for later use
         *
         * @return void
         */
        public function registerStylesScriptsFrontend()
        {
            if( $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                wp_register_script(
                    'advgb_blocks_frontend_scripts',
                    plugins_url('assets/blocks/frontend.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );

                // Pro
                if( defined( 'ADVANCED_GUTENBERG_PRO' ) ) {
                    if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_register_scripts_frontend' ) ) {
                        PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_register_scripts_frontend();
                    }
                }
            }

            wp_register_style(
                'colorbox_style',
                plugins_url('assets/css/colorbox.css', dirname(__FILE__)),
                array(),
                ADVANCED_GUTENBERG_VERSION
            );
            wp_register_style(
                'slick_style',
                plugins_url('assets/css/slick.css', dirname(__FILE__))
            );
            wp_register_style(
                'slick_theme_style',
                plugins_url('assets/css/slick-theme.css', dirname(__FILE__))
            );
            wp_register_style(
                'material_icon_font',
                plugins_url('assets/css/fonts/material-icons.min.css', dirname(__FILE__))
            );
            wp_register_style(
                'material_icon_font_custom',
                plugins_url('assets/css/fonts/material-icons-custom.min.css', dirname(__FILE__))
            );

            if( $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                wp_register_style(
                    'advgb_columns_styles',
                    plugins_url('assets/css/columns.css', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_register_style(
                    'advgb_recent_posts_styles',
                    plugins_url('assets/css/recent-posts.css', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
            }

            wp_register_script(
                'colorbox_js',
                plugins_url('assets/js/jquery.colorbox.min.js', dirname(__FILE__)),
                array('jquery'),
                ADVANCED_GUTENBERG_VERSION
            );
            wp_register_script(
                'slick_js',
                plugins_url('assets/js/slick.min.js', dirname(__FILE__)),
                array('jquery'),
                ADVANCED_GUTENBERG_VERSION
            );
            wp_register_script(
                'advgb_masonry_js',
                plugins_url('assets/js/isotope.pkgd.min.js', dirname(__FILE__)),
                array('jquery'),
                ADVANCED_GUTENBERG_VERSION
            );

            $saved_settings = get_option('advgb_settings');
            if (isset($saved_settings['enable_blocks_spacing']) && $saved_settings['enable_blocks_spacing']) {
                $blocks_spacing = isset($saved_settings['blocks_spacing']) ? $saved_settings['blocks_spacing'] : 0;

                wp_add_inline_style(
                    'dashicons',
                    '.entry-content > * {margin-bottom: ' . $blocks_spacing . 'px !important}'
                );
            }
        }

        /**
         * Register main menu
         *
         * @return void
         */
        public function registerMainMenu()
        {
            if (empty($GLOBALS['admin_page_hooks']['advgb_main'])) {
                add_menu_page(
                    __('Blocks', 'advanced-gutenberg'),
                    __('Blocks', 'advanced-gutenberg'),
                    'manage_options',
                    'advgb_main',
                    array($this, 'advgbMainView'),
                    'dashicons-layout'
                );
            }
        }

        /**
         * Load main view
         *
         * @return void
         */
        public function advgbMainView()
        {
            wp_enqueue_style('roboto_font', 'https://fonts.googleapis.com/css?family=Roboto');
            wp_enqueue_style('material_icon_font');
            wp_enqueue_style('material_icon_font_custom');
            wp_enqueue_style('advgb_quirk');
            wp_enqueue_style('waves_styles');
            wp_enqueue_style('ju_framework_styles');
            wp_enqueue_style('advgb_main_style');

            wp_enqueue_script('waves_js');
            wp_enqueue_script('velocity_js');
            wp_enqueue_script('tabs_js');
            wp_enqueue_script('advgb_main_js');

            $this->loadView('main-view');
        }

        /**
         * Register meta data to use on editor sidebar
         *
         * @return void
         */
        public function registerPostMeta()
        {
            register_post_meta(
                '',
                'advgb_blocks_editor_width',
                array(
                    'show_in_rest'  => true,
                    'single'        => true,
                    'type'          => 'string',
                )
            );

            register_post_meta(
                '',
                'advgb_blocks_columns_visual_guide',
                array(
                    'show_in_rest'  => true,
                    'single'        => true,
                    'type'          => 'string',
                )
            );
        }

        /**
         * Convert Editor Width value into a string
         *
         * @param int $value Editor width number
         *
         * @return string
         */
        public function getAdvgbEditorWidth( $value ) {

            $result = '';

            switch($value) {
                default:
                    $result = 'default';
                    break;
                case '75':
                    $result = 'large';
                    break;
                case '95':
                    $result = 'full';
                    break;
            }

            return $result;
        }

        /**
         * Get Global Columns Visual Guide
         *
         * @param int $value Columns Visual Guide check
         *
         * @return string
         */
        public function getAdvgbColsVisualGuideGlobal( $value ) {
            return ( isset($value) && ($value == 0) ) ? 'disable' : 'enable';
        }

        /**
         * Set body classes for Editor width and Columns visual guide
         *
         * @param string $classes CSS class from body
         */
        public function setAdvgEditorBodyClassses( $classes ) {

            $saved_settings = get_option('advgb_settings');

            if ('post' === get_current_screen()->base) {
                $saved_settings     = get_option('advgb_settings');
                global $post;
                $editorWidth        = get_post_meta($post->ID, 'advgb_blocks_editor_width', true);
                $editorColsVG       = get_post_meta($post->ID, 'advgb_blocks_columns_visual_guide', true);
                $editorWidthGlobal  = (
                        isset($saved_settings['editor_width'])
                        && !empty($saved_settings['editor_width'])
                    )
                    ? $this->getAdvgbEditorWidth( $saved_settings['editor_width'] )
                    : 'default';
                $editorColsVGGlobal = $this->getAdvgbColsVisualGuideGlobal( $saved_settings['enable_columns_visual_guide'] );

                // Editor width
                if(isset($editorWidth) && !empty($editorWidth)) {
                    // Editor width - Post meta
                    $classes .= ' advgb-editor-width-' . $editorWidth . ' ';
                } else {
                    // Editor width - Global configuration
                    $classes .= ' advgb-editor-width-' . $editorWidthGlobal . ' ';
                }

                // Columns visual guide
                if(isset($editorColsVG) && !empty($editorColsVG)) {
                    // Columns visual guide - Post meta
                    $classes .= ' advgb-editor-col-guide-' . $editorColsVG . ' ';
                }  else {
                    // Columns visual guide - Global configuration
                    $classes .= ' advgb-editor-col-guide-' . $editorColsVGGlobal . ' ';
                }

                // Global settings as javascript variables
                wp_localize_script(
                    'wp-blocks',
                    'advg_settings',
                    [
                        'editor_width_global'                   => $editorWidthGlobal,
                        'enable_columns_visual_guide_global'    => $editorColsVGGlobal,
                    ]
                );

                return $classes;
            } elseif ( 'widgets' === get_current_screen()->id ) {
                // Use global Columns Visual Guide in Appearance > Widgets
                $editorColsVGGlobal = $this->getAdvgbColsVisualGuideGlobal( $saved_settings['enable_columns_visual_guide'] );
                $classes .= ' advgb-editor-col-guide-' . $editorColsVGGlobal;
            }
            return $classes;
        }

        /**
         * Register profiles custom post type
         *
         * @return void
         */
        public function registerAdvgbProfile()
        {
            $labels = array(
                'name'               => __('PublishPress Blocks Profiles', 'advanced-gutenberg'),  // Profile title
                'singular_name'      => __('PublishPress Blocks Profiles', 'advanced-gutenberg'),
                'add_new'            => __('New Profile', 'advanced-gutenberg'),                  // New profile menu title
                'add_new_item'       => __('Add New Profile', 'advanced-gutenberg'),              // New profile title
                'edit_item'          => __('Edit Profile', 'advanced-gutenberg'),                 // Edit profile title
                'all_items'          => __('Profiles', 'advanced-gutenberg'),                     // All profiles menu title
                'view_item'          => __('View Profile', 'advanced-gutenberg'),
                'search_items'       => __('Search Profiles', 'advanced-gutenberg'),              // Search button title
                'not_found'          => __('No profiles found', 'advanced-gutenberg'),
                'not_found_in_trash' => __('No profiles found in trash', 'advanced-gutenberg'),
                'parent_item_colon'  => '',
                'menu_name'          => __('Profiles', 'advanced-gutenberg')
            );
            register_post_type('advgb_profiles', array(
                'labels'       => $labels,
                'public'       => false,
                'show_ui'      => false,
                'supports'     => array('title', 'author'),
                'capabilities' => array(
                    'edit_posts'          => 'edit_advgb_profiles',
                    'edit_others_posts'   => 'edit_others_advgb_profiles',
                    'publish_posts'       => 'publish_advgb_profiles',
                    'read'                => 'read_advgb_profile',
                    'read_private_posts'  => 'read_private_advgb_profiles',
                    'delete_posts'        => 'delete_advgb_profiles',
                    'delete_others_posts' => 'delete_others_advgb_profiles',
                    'create_posts'        => 'create_advgb_profiles',
                ),
                'map_meta_cap' => true
            ));
        }

        /**
         * Save data function
         *
         * @return boolean True on success, False on failure
         */
        public function saveAdvgbData()
        {
            if( isset($_POST['advgb_block_access_save']) ) {
                $this->saveAdvgbBlockAccess();
            }  elseif (isset($_POST['save_settings']) || isset($_POST['save_custom_styles'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- we check nonce below
                $this->saveSettings();
            } elseif (isset($_POST['save_email_config'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- we check nonce below
                $this->saveEmailSettings();
            } elseif (isset($_POST['save_recaptcha_config'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- we check nonce below
                $this->saveCaptchaSettings();
            } elseif (isset($_POST['block_data_export'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- we check nonce below
                $this->downloadBlockFormData();
            }

            return false;
        }

        /**
         * Save config settings
         *
         * @return boolean True on success, False on failure
         */
        public function saveSettings()
        {
            if (isset($_POST['save_settings'])) {
                if (!wp_verify_nonce($_POST['advgb_settings_nonce_field'], 'advgb_settings_nonce')) {
                    return false;
                }

                $save_config = array();
                if (isset($_POST['gallery_lightbox'])) {
                    $save_config['gallery_lightbox'] = 1;
                } else {
                    $save_config['gallery_lightbox'] = 0;
                }

                if (isset($_POST['enable_blocks_spacing'])) {
                    $save_config['enable_blocks_spacing'] = 1;
                } else {
                    $save_config['enable_blocks_spacing'] = 0;
                }

                if (isset($_POST['disable_wpautop'])) {
                    $save_config['disable_wpautop'] = 1;
                } else {
                    $save_config['disable_wpautop'] = 0;
                }

                if (isset($_POST['enable_columns_visual_guide'])) {
                    $save_config['enable_columns_visual_guide'] = 1;
                } else {
                    $save_config['enable_columns_visual_guide'] = 0;
                }

                if (isset($_POST['enable_block_access'])) {
                    $save_config['enable_block_access'] = 1;
                } else {
                    $save_config['enable_block_access'] = 0;
                }

                if (isset($_POST['enable_custom_styles'])) {
                    $save_config['enable_custom_styles'] = 1;
                } else {
                    $save_config['enable_custom_styles'] = 0;
                }

                if (isset($_POST['enable_advgb_blocks'])) {
                    $save_config['enable_advgb_blocks'] = 1;
                } else {
                    $save_config['enable_advgb_blocks'] = 0;
                }

                // Pro
                if(defined('ADVANCED_GUTENBERG_PRO')) {
                    if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_setting_set_value' ) ) {
                        // Display branding
                        $save_config['enable_pp_branding'] = PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_setting_set_value( 'enable_pp_branding' );
                    }
                }

                $save_config['gallery_lightbox_caption'] = $_POST['gallery_lightbox_caption'];
                $save_config['google_api_key'] = $_POST['google_api_key'];
                $save_config['blocks_spacing'] = $_POST['blocks_spacing'];
                $save_config['blocks_icon_color'] = $_POST['blocks_icon_color'];
                $save_config['editor_width'] = $_POST['editor_width'];
                $save_config['rp_default_thumb'] = array(
                    'url' => $_POST['post_default_thumb'],
                    'id'  => $_POST['post_default_thumb_id']
                );

                update_option('advgb_settings', $save_config);

                if (isset($_REQUEST['_wp_http_referer'])) {
                    wp_safe_redirect(admin_url('admin.php?page=advgb_main&save_settings=success'));
                    exit;
                }
            }

            if (isset($_POST['save_custom_styles'])) {
                if (!wp_verify_nonce($_POST['advgb_cstyles_nonce_field'], 'advgb_cstyles_nonce')) {
                    return false;
                }

                if (isset($_REQUEST['_wp_http_referer'])) {
                    wp_safe_redirect(admin_url('admin.php?page=advgb_main&save_styles=success'));
                    exit;
                }
            }

            return true;
        }

        /**
         * Load Custom Styles in <head> in frontend
         *
         * @return void
         */
        public function loadCustomStylesFrontend() {
            $post_content = get_the_content();
            $this->getCustomStylesContent($post_content);

            // @TODO Check for Custom styles in use in Widgets
        }

        /**
         * Get Custom Styles in use in Contennt
         *
         * @return string
         */
        public function getCustomStylesContent($content) {

            $custom_styles = get_option('advgb_custom_styles');

            if(is_array($custom_styles)) {

                $css = '';

                foreach ($custom_styles as $styles) {

                    // @TODO Check if the class is in use in the post and widgets
                    //if (strpos($content, $styles['name']) !== false) {
                        $css .= '.' . $styles['name'] . " {\n";
                        $css .= $styles['css'] . "\n} \n";
                    //}
                }

                if( !empty($css) ) {
                    echo '<style type="text/css">' . $css . '</style>';
                }
            }
        }

        /**
         * Load Custom Styles in <head> in admin
         *
         * @return void
         */
        public function loadCustomStylesAdmin() {

            $custom_styles = get_option('advgb_custom_styles');

            if(is_array($custom_styles)) {

                $content = '';
                foreach ($custom_styles as $styles) {
                    $content .= '.block-editor-writing-flow .' .$styles['name'] . " {\n";
                    $content .= $styles['css'] . "\n} \n";
                }

                echo '<style type="text/css">' . $content . '</style>';

            }
        }

        /**
         * Set blocks spacing in editor
         *
         * @return void
         */
        public function setBlocksSpacingAdmin() {
            $saved_settings = get_option('advgb_settings');

            if (isset($saved_settings['enable_blocks_spacing']) && $saved_settings['enable_blocks_spacing']) {
                $blocks_spacing = isset($saved_settings['blocks_spacing']) ? $saved_settings['blocks_spacing'] : 0;
                echo '<style type="text/css">.editor-styles-wrapper [data-block] { margin-bottom: ' . $blocks_spacing . 'px !important; }</style>';
            }
        }

        /**
         * Save block access by user role
         *
         * @return void
         */
        public function saveAdvgbBlockAccess() {

            // Check nonce field exist
            if ( !isset( $_POST['advgb_nonce_field'] ) ) {
                return false;
            }
            // Verify nonce
            if ( !wp_verify_nonce( $_POST['advgb_nonce_field'], 'advgb_nonce' ) ) {
                return false;
            }

            if ( !current_user_can( 'administrator' ) ) {
                return false;
            }

            $user_role         = $_POST['user_role'];
            $blocks            = $_POST['blocks'];
            $active_blocks     = array();
            $inactive_blocks   = array();

            if ( isset( $blocks ) && !empty( $blocks ) && isset( $user_role ) && !empty( $user_role ) ) {

                /* Blocks saved in advgb_blocks_list but not listed in Block Access page
                 * due their categories are not detected
                 */
                $blocks_list_undetected = ( !empty( $_POST['blocks_list_undetected'] ) ? $_POST['blocks_list_undetected'] : '' );

                // Get all the blocks we can manage (which category is detected)
                $blocks_list = $_POST['blocks_list'];

                if( $blocks_list_undetected && is_array( $blocks_list_undetected ) ) {
                    // Merge active blocks with the ones we can't manage
                    $active_blocks = array_merge( $blocks, $blocks_list_undetected );
                } else {
                    $active_blocks = $blocks;
                }

                // Inactive blocks
                $inactive_blocks = array_unique( array_values( array_diff( $blocks_list, $active_blocks ) ) );

                // Define active and inactive blocks
                $block_access_by_role                                  = get_option( 'advgb_blocks_user_roles');
                $block_access_by_role[$user_role]['active_blocks']     = isset( $active_blocks ) ? $active_blocks : '';
                $block_access_by_role[$user_role]['inactive_blocks']   = isset( $inactive_blocks ) ? $inactive_blocks : '';

                update_option( 'advgb_blocks_user_roles', $block_access_by_role );

                wp_safe_redirect( admin_url( 'admin.php?page=advgb_main&view=block-access&user_role=' . $user_role . '&save_access=success' ) );
            } else {
                wp_safe_redirect( admin_url( 'admin.php?page=advgb_main&view=block-access&user_role=' . $user_role . '&save_access=error' ) );
            }
        }

        /**
         * Download block form data
         *
         * @return mixed
         */
        private function downloadBlockFormData()
        {
            // Verify nonce
            if (!wp_verify_nonce($_POST['advgb_export_data_nonce_field'], 'advgb_export_data_nonce')) {
                return false;
            }

            $postValue = $_POST['block_data_export'];
            $postValue = explode('.', $postValue);
            $dataExport = $postValue[0];
            $dataType = $postValue[1];
            $data = '';

            if ($dataExport === 'contact_form') {
                $dataSaved = get_option('advgb_contacts_saved');
                if (!$dataSaved) {
                    return false;
                }

                switch ($dataType) {
                    case 'csv':
                        $data .= '"#","Date","Name","Email","Message"' . PHP_EOL;
                        $tab = ',';
                        $int = 1;
                        foreach ($dataSaved as $dataVal) {
                            $data .= '"'.$int.'"'.$tab;
                            $data .= '"'.$dataVal['date'].'"'.$tab;
                            $data .= '"'.$dataVal['name'].'"'.$tab;
                            $data .= '"'.$dataVal['email'].'"'.$tab;
                            $data .= '"'.$dataVal['msg'].'"';
                            $data .= PHP_EOL;
                            $int++;
                        }
                        $data = trim($data);

                        header('Content-Type: text/csv; charset=utf-8');
                        header('Content-Disposition: attachment; filename=advgb_contact_form-'.date('m-d-Y').'.csv');
                        header('Pragma: no-cache');
                        header('Expires: 0');

                        echo $data; // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
                        exit;
                    case 'json':
                        header('Content-Type: application/json; charset=utf-8');
                        header('Content-Disposition: attachment; filename=advgb_contact_form-'.date('m-d-Y').'.json');
                        header('Pragma: no-cache');
                        header('Expires: 0');

                        echo json_encode($dataSaved);
                        exit;
                }
            } elseif ($dataExport === 'newsletter') {
                $dataSaved = get_option('advgb_newsletter_saved');
                if (!$dataSaved) {
                    return false;
                }

                switch ($dataType) {
                    case 'csv':
                        $data .= '"#","Date","First Name","Last Name","Email",' . PHP_EOL;
                        $tab = ',';
                        $int = 1;
                        foreach ($dataSaved as $dataVal) {
                            $data .= '"'.$int.'"'.$tab;
                            $data .= '"'.$dataVal['date'].'"'.$tab;
                            $data .= '"'.$dataVal['fname'].'"'.$tab;
                            $data .= '"'.$dataVal['lname'].'"'.$tab;
                            $data .= '"'.$dataVal['email'].'"';
                            $data .= PHP_EOL;
                            $int++;
                        }
                        $data = trim($data);

                        header('Content-Type: text/csv; charset=utf-8');
                        header('Content-Disposition: attachment; filename=advgb_newsletter-'.date('m-d-Y').'.csv');
                        header('Pragma: no-cache');
                        header('Expires: 0');

                        echo $data; // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
                        exit;
                    case 'json':
                        header('Content-Type: application/json; charset=utf-8');
                        header('Content-Disposition: attachment; filename=advgb_newsletter-'.date('m-d-Y').'.json');
                        header('Pragma: no-cache');
                        header('Expires: 0');

                        echo json_encode($dataSaved);
                        exit;
                }
            }

            return false;
        }

        /**
         * Save forms and email settings
         *
         * @return mixed
         */
        private function saveEmailSettings()
        {
            if (!isset($_POST['advgb_email_config_nonce_field'])) {
                return false;
            }

            if (!wp_verify_nonce($_POST['advgb_email_config_nonce_field'], 'advgb_email_config_nonce')) {
                return false;
            }

            $save_config = array();
            $save_config['contact_form_sender_name'] = $_POST['contact_form_sender_name'];
            $save_config['contact_form_sender_email'] = $_POST['contact_form_sender_email'];
            $save_config['contact_form_email_title'] = $_POST['contact_form_email_title'];
            $save_config['contact_form_email_receiver'] = $_POST['contact_form_email_receiver'];

            update_option('advgb_email_sender', $save_config);

            if (isset($_REQUEST['_wp_http_referer'])) {
                wp_safe_redirect(admin_url('admin.php?page=advgb_main&save_settings=success'));
                exit;
            }
        }

        /**
         * Save captcha settings
         *
         * @return mixed
         */
        private function saveCaptchaSettings()
        {
            if (!isset($_POST['advgb_captcha_nonce_field'])) {
                return false;
            }

            if (!wp_verify_nonce($_POST['advgb_captcha_nonce_field'], 'advgb_captcha_nonce')) {
                return false;
            }

            $save_config = array();
            if (isset($_POST['recaptcha_enable'])) {
                $save_config['recaptcha_enable'] = 1;
            } else {
                $save_config['recaptcha_enable'] = 0;
            }
            $save_config['recaptcha_site_key'] = $_POST['recaptcha_site_key'];
            $save_config['recaptcha_secret_key'] = $_POST['recaptcha_secret_key'];
            $save_config['recaptcha_language'] = $_POST['recaptcha_language'];
            $save_config['recaptcha_theme'] = $_POST['recaptcha_theme'];

            update_option('advgb_recaptcha_config', $save_config);

            if (isset($_REQUEST['_wp_http_referer'])) {
                wp_safe_redirect(admin_url('admin.php?page=advgb_main&save_settings=success'));
                exit;
            }
        }

        /**
         * Retrieve the active and inactive blocks for users regard to PublishPress Blocks access
         *
         * @return array
         */
        public function getUserBlocksForGutenberg()
        {
            // Get user info
            $current_user      = wp_get_current_user();
            //$current_user_id   = $current_user->ID;
            $current_user_role = $current_user->roles[0];

            // All saved blocks (even the ones not detected by Block Access)
            $all_blocks = get_option( 'advgb_blocks_list' );

            // Get the array from advgb_blocks_user_roles option that match current user role
            if( get_option('advgb_blocks_user_roles') ) {
                $advgb_blocks_user_roles = !empty( get_option('advgb_blocks_user_roles') ) ? get_option( 'advgb_blocks_user_roles' ) : [];
                $advgb_blocks_user_roles = array_key_exists( $current_user_role, $advgb_blocks_user_roles ) ? (array)$advgb_blocks_user_roles[$current_user_role] : [];

                // Include the blocks stored in advgb_blocks_list option but not detected by Block Access
                //$advgb_blocks_user_roles = array_key_exists( $current_user_role, $advgb_blocks_user_roles ) ? (array)$advgb_blocks_user_roles[$current_user_role] : [];

                return $advgb_blocks_user_roles;
            }

            // If advgb_blocks_user_roles option doesn't exists, then allow all blocks
            if (!is_array($all_blocks)) {
                $all_blocks = array();
            } else {
                foreach ($all_blocks as $block_key => $block_value) {
                    $all_blocks[$block_key] = $all_blocks[$block_key]['name'];
                }
            }
            return array(
                'active_blocks' => $all_blocks,
                'inactive_blocks' => array()
            );
        }

        /**
         * Register block config page
         *
         * @return void
         */
        public function registerBlockConfigPage()
        {
            $advgb_block = array(
                'accordions', 'button', 'image', 'list',
                'table', 'video', 'contact-form', 'container',
                'count-up','images-slider', 'map', 'newsletter',
                'recent-posts', 'social-links', 'summary', 'adv-tabs',
                'testimonial', 'woo-products', 'columns', 'column',
                'login-form', 'search-bar', 'icon', 'infobox'
            );

            foreach ($advgb_block as $block) {
                add_submenu_page(
                    'admin.php?',
                    __('Block Config', 'advanced-gutenberg'),
                    __('Block Config', 'advanced-gutenberg'),
                    'manage_options',
                    'advgb-' . $block,
                    array($this, 'loadBlockConfigView')
                );
            }
        }

        /**
         * Function to get and load the block config view
         *
         * @param string $block View to load
         *
         * @return void
         */
        public function loadBlockConfigView($block = '')
        {
            if (!$block) {
                $block = $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- view only
            }

            wp_enqueue_style('roboto_font', 'https://fonts.googleapis.com/css?family=Roboto');
            wp_enqueue_style(
                'minicolors_css',
                plugins_url('assets/css/jquery.minicolors.css', ADVANCED_GUTENBERG_PLUGIN)
            );
            wp_enqueue_style(
                'ju_framework_styles',
                plugins_url('assets/css/style.css', ADVANCED_GUTENBERG_PLUGIN)
            );
            wp_enqueue_style(
                'block_config_css',
                plugins_url('assets/css/block-config.css', ADVANCED_GUTENBERG_PLUGIN)
            );
            $loadingImage = plugins_url('assets/images/loading.gif', ADVANCED_GUTENBERG_PLUGIN);
            wp_add_inline_style('block_config_css', '#advgb-loading-screen-image {background-image: url('. $loadingImage .')}');

            wp_enqueue_script(
                'minicolors_js',
                plugins_url('assets/js/jquery.minicolors.min.js', ADVANCED_GUTENBERG_PLUGIN),
                array(),
                ADVANCED_GUTENBERG_VERSION
            );
            wp_enqueue_script(
                'block_config_js',
                plugins_url('assets/js/block-config.js', ADVANCED_GUTENBERG_PLUGIN),
                array('jquery'),
                ADVANCED_GUTENBERG_VERSION
            );

            $blocks_settings_list = array(
                'advgb-accordions' => array(
                    array(
                        'label'    => __('Accordion Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Bottom spacing', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'marginBottom',
                                'min'   => 0,
                                'max'   => 50,
                            ),
                            array(
                                'title' => __('Initial Collapsed', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'collapsedAll',
                            ),
                        )
                    ),
                    array(
                        'label'    => __('Header Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'headerBgColor',
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'headerTextColor'
                            ),
                            array(
                                'title'   => __('Header Icon', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'headerIcon',
                                'options' => array(
                                    array(
                                        'label' => __('Plus', 'advanced-gutenberg'),
                                        'value' => 'plus',
                                    ),
                                    array(
                                        'label' => __('Plus Circle', 'advanced-gutenberg'),
                                        'value' => 'plusCircle',
                                    ),
                                    array(
                                        'label' => __('Plus Circle Outline', 'advanced-gutenberg'),
                                        'value' => 'plusCircleOutline',
                                    ),
                                    array(
                                        'label' => __('Plus Square Outline', 'advanced-gutenberg'),
                                        'value' => 'plusBox',
                                    ),
                                    array(
                                        'label' => __('Unfold Arrow', 'advanced-gutenberg'),
                                        'value' => 'unfold',
                                    ),
                                    array(
                                        'label' => __('Horizontal Dots', 'advanced-gutenberg'),
                                        'value' => 'threeDots',
                                    ),
                                    array(
                                        'label' => __('Arrow Down', 'advanced-gutenberg'),
                                        'value' => 'arrowDown',
                                    ),
                                )
                            ),
                            array(
                                'title' => __('Header Icon Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'headerIconColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Body Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'bodyBgColor',
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'bodyTextColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Border Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Border Style', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'borderStyle',
                                'options' => array(
                                    array(
                                        'label' => __('Solid', 'advanced-gutenberg'),
                                        'value' => 'solid',
                                    ),
                                    array(
                                        'label' => __('Dashed', 'advanced-gutenberg'),
                                        'value' => 'dashed',
                                    ),
                                    array(
                                        'label' => __('Dotted', 'advanced-gutenberg'),
                                        'value' => 'dotted',
                                    ),
                                )
                            ),
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'borderColor',
                            ),
                            array(
                                'title' => __('Border Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderWidth',
                                'min'   => 1,
                                'max'   => 10,
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderRadius',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                ),
                'advgb-button' => array(
                    array(
                        'label'    => __('Text and Color', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Text Size', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textSize',
                                'min'   => 10,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'textColor'
                            ),
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'bgColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Border Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Border Style', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'borderStyle',
                                'options' => array(
                                    array(
                                        'label' => __('Solid', 'advanced-gutenberg'),
                                        'value' => 'solid',
                                    ),
                                    array(
                                        'label' => __('Dashed', 'advanced-gutenberg'),
                                        'value' => 'dashed',
                                    ),
                                    array(
                                        'label' => __('Dotted', 'advanced-gutenberg'),
                                        'value' => 'dotted',
                                    ),
                                )
                            ),
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'borderColor',
                            ),
                            array(
                                'title' => __('Border Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderWidth',
                                'min'   => 1,
                                'max'   => 10,
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderRadius',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Margin Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Margin Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'marginTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'marginRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'marginBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'marginLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Padding Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Padding Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'paddingTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'paddingRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'paddingBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'paddingLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Hover Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'hoverTextColor'
                            ),
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'hoverBgColor',
                            ),
                            array(
                                'title' => __('Shadow Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'hoverShadowColor',
                            ),
                            array(
                                'title' => __('Shadow H Offset', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'hoverShadowH',
                                'min'   => -50,
                                'max'   => 50,
                            ),
                            array(
                                'title' => __('Shadow V Offset', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'hoverShadowV',
                                'min'   => -50,
                                'max'   => 50,
                            ),
                            array(
                                'title' => __('Shadow Blur', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'hoverShadowBlur',
                                'min'   => 0,
                                'max'   => 50,
                            ),
                            array(
                                'title' => __('Shadow Spread', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'hoverShadowSpread',
                                'min'   => 0,
                                'max'   => 50,
                            ),
                            array(
                                'title' => __('Transition Speed', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'transitionSpeed',
                                'min'   => 0,
                                'max'   => 3,
                            ),
                        ),
                    ),
                ),
                'advgb-image' => array(
                    array(
                        'label' =>  __('Click action', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Action on click', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'openOnClick',
                                'options' => array(
                                    array(
                                        'label' => __('None', 'advanced-gutenberg'),
                                        'value' => 'none',
                                    ),
                                    array(
                                        'label' => __('Open image in a lightbox', 'advanced-gutenberg'),
                                        'value' => 'lightbox',
                                    ),
                                    array(
                                        'label' => __('Open custom URL', 'advanced-gutenberg'),
                                        'value' => 'url',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Open link in a new tab', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'linkInNewTab',
                            ),
                        )
                    ),
                    array(
                        'label'    => __('Image Size', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Full width', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'fullWidth',
                            ),
                            array(
                                'title' => __('Height', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'height',
                                'min'   => 100,
                                'max'   => 1000,
                            ),
                            array(
                                'title' => __('Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'width',
                                'min'   => 200,
                                'max'   => 1300,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Color', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Title Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'titleColor',
                            ),
                            array(
                                'title' => __('Subtitle Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'subtitleColor',
                            ),
                            array(
                                'title' => __('Overlay Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'overlayColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Text Alignment', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Vertical Alignment', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'vAlign',
                                'options' => array(
                                    array(
                                        'label' => __('Top', 'advanced-gutenberg'),
                                        'value' => 'flex-start',
                                    ),
                                    array(
                                        'label' => __('Center', 'advanced-gutenberg'),
                                        'value' => 'center',
                                    ),
                                    array(
                                        'label' => __('Bottom', 'advanced-gutenberg'),
                                        'value' => 'flex-end',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Horizontal Alignment', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'hAlign',
                                'options' => array(
                                    array(
                                        'label' => __('Left', 'advanced-gutenberg'),
                                        'value' => 'flex-start',
                                    ),
                                    array(
                                        'label' => __('Center', 'advanced-gutenberg'),
                                        'value' => 'center',
                                    ),
                                    array(
                                        'label' => __('Right', 'advanced-gutenberg'),
                                        'value' => 'flex-end',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'advgb-list' => array(
                    array(
                        'label'    => __('Text Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Text Size', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'fontSize',
                                'min'   => 10,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Icon Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Icon style', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'icon',
                                'options' => array(
                                    array(
                                        'label' => __('None', 'advanced-gutenberg'),
                                        'value' => '',
                                    ),
                                    array(
                                        'label' => __('Pushpin', 'advanced-gutenberg'),
                                        'value' => 'admin-post',
                                    ),
                                    array(
                                        'label' => __('Configuration', 'advanced-gutenberg'),
                                        'value' => 'admin-generic',
                                    ),
                                    array(
                                        'label' => __('Flag', 'advanced-gutenberg'),
                                        'value' => 'flag',
                                    ),
                                    array(
                                        'label' => __('Star', 'advanced-gutenberg'),
                                        'value' => 'star-filled',
                                    ),
                                    array(
                                        'label' => __('Checkmark', 'advanced-gutenberg'),
                                        'value' => 'yes',
                                    ),
                                    array(
                                        'label' => __('Minus', 'advanced-gutenberg'),
                                        'value' => 'minus',
                                    ),
                                    array(
                                        'label' => __('Plus', 'advanced-gutenberg'),
                                        'value' => 'plus',
                                    ),
                                    array(
                                        'label' => __('Play', 'advanced-gutenberg'),
                                        'value' => 'controls-play',
                                    ),
                                    array(
                                        'label' => __('Arrow Right', 'advanced-gutenberg'),
                                        'value' => 'arrow-right-alt',
                                    ),
                                    array(
                                        'label' => __('X Cross', 'advanced-gutenberg'),
                                        'value' => 'dismiss',
                                    ),
                                    array(
                                        'label' => __('Warning', 'advanced-gutenberg'),
                                        'value' => 'warning',
                                    ),
                                    array(
                                        'label' => __('Help', 'advanced-gutenberg'),
                                        'value' => 'editor-help',
                                    ),
                                    array(
                                        'label' => __('Info', 'advanced-gutenberg'),
                                        'value' => 'info',
                                    ),
                                    array(
                                        'label' => __('Circle', 'advanced-gutenberg'),
                                        'value' => 'marker',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Icon color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'iconColor',
                            ),
                            array(
                                'title' => __('Icon Size', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconSize',
                                'min'   => 10,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Line Height', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'lineHeight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'margin',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'padding',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                ),
                'advgb-table' => array(
                    array(
                        'label'    => __('Table Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Max width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'maxWidth',
                                'min'   => 0,
                                'max'   => 1999,
                            ),
                        ),
                    ),
                ),
                'advgb-video' => array(
                    array(
                        'label'    => __('Video Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Open video in lightbox', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'openInLightbox',
                            ),
                            array(
                                'title' => __('Full width', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'videoFullWidth'
                            ),
                            array(
                                'title' => __('Video Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'videoWidth',
                                'min'   => 100,
                                'max'   => 1000,
                            ),
                            array(
                                'title' => __('Video Height', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'videoHeight',
                                'min'   => 300,
                                'max'   => 7000,
                            ),
                            array(
                                'title' => __('Overlay color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'overlayColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Play Button Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Button Icon', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'playButtonIcon',
                                'options' => array(
                                    array(
                                        'label' => __('Normal', 'advanced-gutenberg'),
                                        'value' => 'normal',
                                    ),
                                    array(
                                        'label' => __('Filled Circle', 'advanced-gutenberg'),
                                        'value' => 'circleFill',
                                    ),
                                    array(
                                        'label' => __('Outline Circle', 'advanced-gutenberg'),
                                        'value' => 'circleOutline',
                                    ),
                                    array(
                                        'label' => __('Video Camera', 'advanced-gutenberg'),
                                        'value' => 'videoCam',
                                    ),
                                    array(
                                        'label' => __('Filled Square', 'advanced-gutenberg'),
                                        'value' => 'squareCurved',
                                    ),
                                    array(
                                        'label' => __('Star Sticker', 'advanced-gutenberg'),
                                        'value' => 'starSticker',
                                    ),
                                )
                            ),
                            array(
                                'title' => __('Button Size', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'playButtonSize',
                                'min'   => 40,
                                'max'   => 200,
                            ),
                            array(
                                'title' => __('Button Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'playButtonColor',
                            ),
                        ),
                    ),
                ),
                'advgb-count-up' => array(
                    array(
                        'label'    => __('General Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Number of columns', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'columns',
                                'min'   => 1,
                                'max'   => 3,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Color Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Header Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'headerTextColor',
                            ),
                            array(
                                'title' => __('Count Up Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'countUpNumberColor',
                            ),
                            array(
                                'title' => __('Description Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'descTextColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Count Up Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Count Up Number Size', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'countUpNumberSize',
                                'min'   => 10,
                                'max'   => 100,
                            ),
                        ),
                    ),
                ),
                'advgb-map' => array(
                    array(
                        'label'    => __('Location Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'type'  => 'hidden',
                                'name'  => 'useLatLng',
                                'value' => 1,
                            ),
                            array(
                                'title' => __('Latitude', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'lat',
                                'min'   => 0,
                                'max'   => 999,
                            ),
                            array(
                                'title' => __('Longitude', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'lng',
                                'min'   => 0,
                                'max'   => 999,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Map Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Zoom Level', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'zoom',
                                'min'   => 0,
                                'max'   => 25,
                            ),
                            array(
                                'title' => __('Height', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'height',
                                'min'   => 300,
                                'max'   => 1000,
                            ),
                            array(
                                'title' => __('Map style', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'mapStyle',
                                'options' => array(
                                    array('label' => __('Silver', 'advanced-gutenberg'), 'value' => 'silver'),
                                    array('label' => __('Retro', 'advanced-gutenberg'), 'value' => 'retro'),
                                    array('label' => __('Dark', 'advanced-gutenberg'), 'value' => 'dark'),
                                    array('label' => __('Night', 'advanced-gutenberg'), 'value' => 'night'),
                                    array('label' => __('Aubergine', 'advanced-gutenberg'), 'value' => 'aubergine'),
                                    array('label' => __('Custom', 'advanced-gutenberg'), 'value' => 'custom'),
                                )
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Marker Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Marker Title', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'markerTitle',
                            ),
                            array(
                                'title' => __('Marker Description', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'markerDesc',
                            ),
                        ),
                    ),
                ),
                'advgb-social-links' => array(
                    array(
                        'label'    => __('Icon 1 Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Icon', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'icon1.icon',
                                'options' => array(
                                    array('label' => __('Blogger', 'advanced-gutenberg'), 'value' => 'blogger'),
                                    array('label' => __('Facebook', 'advanced-gutenberg'), 'value' => 'facebook'),
                                    array('label' => __('Flickr', 'advanced-gutenberg'), 'value' => 'flickr'),
                                    array('label' => __('Google Plus', 'advanced-gutenberg'), 'value' => 'google'),
                                    array('label' => __('Instagram', 'advanced-gutenberg'), 'value' => 'instagram'),
                                    array('label' => __('LinkedIn', 'advanced-gutenberg'), 'value' => 'linkedin'),
                                    array('label' => __('Email', 'advanced-gutenberg'), 'value' => 'mail'),
                                    array('label' => __('Picasa', 'advanced-gutenberg'), 'value' => 'picasa'),
                                    array('label' => __('Pinterest', 'advanced-gutenberg'), 'value' => 'pinterest'),
                                    array('label' => __('Reddit', 'advanced-gutenberg'), 'value' => 'reddit'),
                                    array('label' => __('Skype', 'advanced-gutenberg'), 'value' => 'skype'),
                                    array('label' => __('Sound Cloud', 'advanced-gutenberg'), 'value' => 'soundcloud'),
                                    array('label' => __('Tumblr', 'advanced-gutenberg'), 'value' => 'tumblr'),
                                    array('label' => __('Twitter', 'advanced-gutenberg'), 'value' => 'twitter'),
                                    array('label' => __('Vimeo', 'advanced-gutenberg'), 'value' => 'vimeo'),
                                    array('label' => __('Youtube', 'advanced-gutenberg'), 'value' => 'youtube'),
                                )
                            ),
                            array(
                                'title' => __('Icon Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'icon1.iconColor',
                            ),
                            array(
                                'title' => __('Icon Link', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'icon1.link',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Icon 2 Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Icon', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'icon2.icon',
                                'options' => array(
                                    array('label' => __('Blogger', 'advanced-gutenberg'), 'value' => 'blogger'),
                                    array('label' => __('Facebook', 'advanced-gutenberg'), 'value' => 'facebook'),
                                    array('label' => __('Flickr', 'advanced-gutenberg'), 'value' => 'flickr'),
                                    array('label' => __('Google Plus', 'advanced-gutenberg'), 'value' => 'google'),
                                    array('label' => __('Instagram', 'advanced-gutenberg'), 'value' => 'instagram'),
                                    array('label' => __('LinkedIn', 'advanced-gutenberg'), 'value' => 'linkedin'),
                                    array('label' => __('Email', 'advanced-gutenberg'), 'value' => 'mail'),
                                    array('label' => __('Picasa', 'advanced-gutenberg'), 'value' => 'picasa'),
                                    array('label' => __('Pinterest', 'advanced-gutenberg'), 'value' => 'pinterest'),
                                    array('label' => __('Reddit', 'advanced-gutenberg'), 'value' => 'reddit'),
                                    array('label' => __('Skype', 'advanced-gutenberg'), 'value' => 'skype'),
                                    array('label' => __('Sound Cloud', 'advanced-gutenberg'), 'value' => 'soundcloud'),
                                    array('label' => __('Tumblr', 'advanced-gutenberg'), 'value' => 'tumblr'),
                                    array('label' => __('Twitter', 'advanced-gutenberg'), 'value' => 'twitter'),
                                    array('label' => __('Vimeo', 'advanced-gutenberg'), 'value' => 'vimeo'),
                                    array('label' => __('Youtube', 'advanced-gutenberg'), 'value' => 'youtube'),
                                )
                            ),
                            array(
                                'title' => __('Icon Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'icon2.iconColor',
                            ),
                            array(
                                'title' => __('Icon Link', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'icon2.link',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Icon 3 Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Icon', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'icon3.icon',
                                'options' => array(
                                    array('label' => __('Blogger', 'advanced-gutenberg'), 'value' => 'blogger'),
                                    array('label' => __('Facebook', 'advanced-gutenberg'), 'value' => 'facebook'),
                                    array('label' => __('Flickr', 'advanced-gutenberg'), 'value' => 'flickr'),
                                    array('label' => __('Google Plus', 'advanced-gutenberg'), 'value' => 'google'),
                                    array('label' => __('Instagram', 'advanced-gutenberg'), 'value' => 'instagram'),
                                    array('label' => __('LinkedIn', 'advanced-gutenberg'), 'value' => 'linkedin'),
                                    array('label' => __('Email', 'advanced-gutenberg'), 'value' => 'mail'),
                                    array('label' => __('Picasa', 'advanced-gutenberg'), 'value' => 'picasa'),
                                    array('label' => __('Pinterest', 'advanced-gutenberg'), 'value' => 'pinterest'),
                                    array('label' => __('Reddit', 'advanced-gutenberg'), 'value' => 'reddit'),
                                    array('label' => __('Skype', 'advanced-gutenberg'), 'value' => 'skype'),
                                    array('label' => __('Sound Cloud', 'advanced-gutenberg'), 'value' => 'soundcloud'),
                                    array('label' => __('Tumblr', 'advanced-gutenberg'), 'value' => 'tumblr'),
                                    array('label' => __('Twitter', 'advanced-gutenberg'), 'value' => 'twitter'),
                                    array('label' => __('Vimeo', 'advanced-gutenberg'), 'value' => 'vimeo'),
                                    array('label' => __('Youtube', 'advanced-gutenberg'), 'value' => 'youtube'),
                                )
                            ),
                            array(
                                'title' => __('Icon Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'icon3.iconColor',
                            ),
                            array(
                                'title' => __('Icon Link', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'icon3.link',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('General Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Icon Alignment', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'align',
                                'options' => array(
                                    array('label' => __('Left', 'advanced-gutenberg'), 'value' => 'left'),
                                    array('label' => __('Center', 'advanced-gutenberg'), 'value' => 'center'),
                                    array('label' => __('Right', 'advanced-gutenberg'), 'value' => 'right'),
                                )
                            ),
                            array(
                                'title' => __('Icon Size', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconSize',
                                'min'   => 20,
                                'max'   => 60,
                            ),
                            array(
                                'title' => __('Icon Space', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconSpace',
                                'min'   => 0,
                                'max'   => 30,
                            ),
                        ),
                    ),
                ),
                'advgb-summary' => array(
                    array(
                        'label'    => __('General Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Load minimized', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'loadMinimized',
                            ),
                            array(
                                'title' => __('Table of Contents header title', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'headerTitle',
                            ),
                            array(
                                'title' => __('Anchor color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'anchorColor',
                            ),
                            array(
                                'title' => __('Table of Contents Alignment', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'align',
                                'options' => array(
                                    array('label' => __('Left', 'advanced-gutenberg'), 'value' => 'left'),
                                    array('label' => __('Center', 'advanced-gutenberg'), 'value' => 'center'),
                                    array('label' => __('Right', 'advanced-gutenberg'), 'value' => 'right'),
                                )
                            ),
                        ),
                    ),
                ),
                'advgb-adv-tabs' => array(
                    array(
                        'label'    => __('Tab Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Tabs Style', 'advanced-gutenberg'),
                                'type' => 'select',
                                'name' => 'tabsStyle',
                                'options' => array(
                                    array(
                                        'label' => __('Horizontal', 'advanced-gutenberg'),
                                        'value' => 'horz',
                                    ),
                                    array(
                                        'label' => __('Vertical', 'advanced-gutenberg'),
                                        'value' => 'vert',
                                    ),
                                )
                            ),
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'headerBgColor',
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'headerTextColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Active Tab Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'activeTabBgColor',
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'activeTabTextColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Body Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'bodyBgColor',
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'bodyTextColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Border Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Border Style', 'advanced-gutenberg'),
                                'type'  => 'select',
                                'name'  => 'borderStyle',
                                'options' => array(
                                    array('label' => __('Solid', 'advanced-gutenberg'), 'value' => 'solid'),
                                    array('label' => __('Dashed', 'advanced-gutenberg'), 'value' => 'dashed'),
                                    array('label' => __('Dotted', 'advanced-gutenberg'), 'value' => 'dotted'),
                                )
                            ),
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'borderColor',
                            ),
                            array(
                                'title' => __('Border Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderWidth',
                                'min'   => 1,
                                'max'   => 10,
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderRadius',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                ),
                'advgb-testimonial' => array(
                    array(
                        'label'    => __('General Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Columns', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'columns',
                                'min'   => 1,
                                'max'   => 3,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Avatar Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'avatarColor',
                            ),
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'avatarBorderColor',
                            ),
                            array(
                                'title' => __('Border Radius (%)', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'avatarBorderRadius',
                                'min'   => 0,
                                'max'   => 50,
                            ),
                            array(
                                'title' => __('Border Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'avatarBorderWidth',
                                'min'   => 0,
                                'max'   => 5,
                            ),
                            array(
                                'title' => __('Avatar Size', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'avatarSize',
                                'min'   => 50,
                                'max'   => 130,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Text Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Name Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'nameColor',
                            ),
                            array(
                                'title' => __('Position Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'positionColor',
                            ),
                            array(
                                'title' => __('Description Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'descColor',
                            ),
                        ),
                    ),
                ),
                'advgb-woo-products' => array(
                    array(
                        'label'    => __('Layout Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Columns', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'columns',
                                'min'   => 1,
                                'max'   => 4,
                            ),
                            array(
                                'title' => __('Number of Products', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'numberOfProducts',
                                'min'   => 1,
                                'max'   => 48,
                            ),
                        ),
                    ),
                ),
                'advgb-contact-form' => array(
                    array(
                        'label'    => __('Text Label', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Name placeholder', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'nameLabel',
                            ),
                            array(
                                'title' => __('Email placeholder', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'emailLabel',
                            ),
                            array(
                                'title' => __('Message placeholder', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'msgLabel',
                            ),
                            array(
                                'title' => __('Submit label', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'submitLabel',
                            ),
                            array(
                                'title' => __('Success text', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'successLabel',
                            ),
                            array(
                                'title' => __('Error text', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'alertLabel',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Input Color', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'bgColor',
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'textColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Border Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'borderColor',
                            ),
                            array(
                                'title'   => __('Border Style', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'borderStyle',
                                'options' => array(
                                    array(
                                        'label' => __('Solid', 'advanced-gutenberg'),
                                        'value' => 'solid',
                                    ),
                                    array(
                                        'label' => __('Dashed', 'advanced-gutenberg'),
                                        'value' => 'dashed',
                                    ),
                                    array(
                                        'label' => __('Dotted', 'advanced-gutenberg'),
                                        'value' => 'dotted',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderRadius',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Submit Button Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Border and Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'submitColor',
                            ),
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'submitBgColor',
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'submitRadius',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title'   => __('Button Position', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'submitPosition',
                                'options' => array(
                                    array(
                                        'label' => __('Center', 'advanced-gutenberg'),
                                        'value' => 'center',
                                    ),
                                    array(
                                        'label' => __('Left', 'advanced-gutenberg'),
                                        'value' => 'left',
                                    ),
                                    array(
                                        'label' => __('Right', 'advanced-gutenberg'),
                                        'value' => 'right',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'advgb-newsletter' => array(
                    array(
                        'label'    => __('Form Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Form style', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'formStyle',
                                'options' => array(
                                    array(
                                        'label' => __('Normal', 'advanced-gutenberg'),
                                        'value' => 'default',
                                    ),
                                    array(
                                        'label' => __('Alternative', 'advanced-gutenberg'),
                                        'value' => 'alt',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Form width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'formWidth',
                                'min'   => 200,
                                'max'   => 1000,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Text Label', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('First Name placeholder', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'fnameLabel',
                            ),
                            array(
                                'title' => __('Last Name placeholder', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'lnameLabel',
                            ),
                            array(
                                'title' => __('Email placeholder', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'emailLabel',
                            ),
                            array(
                                'title' => __('Submit label', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'submitLabel',
                            ),
                            array(
                                'title' => __('Success text', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'successLabel',
                            ),
                            array(
                                'title' => __('Error text', 'advanced-gutenberg'),
                                'type'  => 'text',
                                'name'  => 'alertLabel',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Input Color', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'bgColor',
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'textColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Border Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'borderColor',
                            ),
                            array(
                                'title'   => __('Border Style', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'borderStyle',
                                'options' => array(
                                    array(
                                        'label' => __('Solid', 'advanced-gutenberg'),
                                        'value' => 'solid',
                                    ),
                                    array(
                                        'label' => __('Dashed', 'advanced-gutenberg'),
                                        'value' => 'dashed',
                                    ),
                                    array(
                                        'label' => __('Dotted', 'advanced-gutenberg'),
                                        'value' => 'dotted',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderRadius',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Submit Button Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Border and Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'submitColor',
                            ),
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'submitBgColor',
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'submitRadius',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                ),
                'advgb-images-slider' => array(
                    array(
                        'label'    => __('Images Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Action on click', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'actionOnClick',
                                'options' => array(
                                    array(
                                        'label' => __('Open image in lightbox', 'advanced-gutenberg'),
                                        'value' => 'lightbox',
                                    ),
                                    array(
                                        'label' => __('Open custom link', 'advanced-gutenberg'),
                                        'value' => 'link',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Full width', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'fullWidth',
                            ),
                            array(
                                'title' => __('Auto Height', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'autoHeight',
                            ),
                            array(
                                'title' => __('Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'width',
                                'min'   => 200,
                                'max'   => 1300,
                            ),
                            array(
                                'title' => __('Height', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'height',
                                'min'   => 100,
                                'max'   => 1000,
                            ),
                            array(
                                'title' => __('Always show overlay', 'advanced-gutenberg'),
                                'type'  => 'checkbox',
                                'name'  => 'alwaysShowOverlay',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Color Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Hover Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'hoverColor',
                            ),
                            array(
                                'title' => __('Title Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'titleColor',
                            ),
                            array(
                                'title' => __('Text Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'textColor',
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Text Alignment', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Vertical Alignment', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'vAlign',
                                'options' => array(
                                    array(
                                        'label' => __('Top', 'advanced-gutenberg'),
                                        'value' => 'flex-start',
                                    ),
                                    array(
                                        'label' => __('Center', 'advanced-gutenberg'),
                                        'value' => 'center',
                                    ),
                                    array(
                                        'label' => __('Bottom', 'advanced-gutenberg'),
                                        'value' => 'flex-end',
                                    ),
                                ),
                            ),
                            array(
                                'title'   => __('Horizontal Alignment', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'hAlign',
                                'options' => array(
                                    array(
                                        'label' => __('Left', 'advanced-gutenberg'),
                                        'value' => 'flex-start',
                                    ),
                                    array(
                                        'label' => __('Center', 'advanced-gutenberg'),
                                        'value' => 'center',
                                    ),
                                    array(
                                        'label' => __('Right', 'advanced-gutenberg'),
                                        'value' => 'flex-end',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'advgb-columns' => array(
                    array(
                        'label'    => __('Columns Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Space between column', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'gutter',
                                'options' => array(
                                    array(
                                        'label' => __('None', 'advanced-gutenberg'),
                                        'value' => 0,
                                    ),
                                    array(
                                        'label' => __('10px', 'advanced-gutenberg'),
                                        'value' => 10,
                                    ),
                                    array(
                                        'label' => __('20px', 'advanced-gutenberg'),
                                        'value' => 20,
                                    ),
                                    array(
                                        'label' => __('30px', 'advanced-gutenberg'),
                                        'value' => 30,
                                    ),
                                    array(
                                        'label' => __('40px', 'advanced-gutenberg'),
                                        'value' => 40,
                                    ),
                                    array(
                                        'label' => __('50px', 'advanced-gutenberg'),
                                        'value' => 50,
                                    ),
                                    array(
                                        'label' => __('70px', 'advanced-gutenberg'),
                                        'value' => 70,
                                    ),
                                    array(
                                        'label' => __('90px', 'advanced-gutenberg'),
                                        'value' => 90,
                                    ),
                                ),
                            ),
                            array(
                                'title'   => __('Vertical space when collapsed', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'collapsedGutter',
                                'options' => array(
                                    array(
                                        'label' => __('None', 'advanced-gutenberg'),
                                        'value' => 0,
                                    ),
                                    array(
                                        'label' => __('10px', 'advanced-gutenberg'),
                                        'value' => 10,
                                    ),
                                    array(
                                        'label' => __('20px', 'advanced-gutenberg'),
                                        'value' => 20,
                                    ),
                                    array(
                                        'label' => __('30px', 'advanced-gutenberg'),
                                        'value' => 30,
                                    ),
                                    array(
                                        'label' => __('40px', 'advanced-gutenberg'),
                                        'value' => 40,
                                    ),
                                    array(
                                        'label' => __('50px', 'advanced-gutenberg'),
                                        'value' => 50,
                                    ),
                                    array(
                                        'label' => __('70px', 'advanced-gutenberg'),
                                        'value' => 70,
                                    ),
                                    array(
                                        'label' => __('90px', 'advanced-gutenberg'),
                                        'value' => 90,
                                    ),
                                ),
                            ),
                            array(
                                'title'   => __('Collapsed order RTL', 'advanced-gutenberg'),
                                'type'    => 'checkbox',
                                'name'    => 'collapsedRtl',
                            )
                        ),
                    ),
                    array(
                        'label'    => __('Row Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Vertical Align', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'vAlign',
                                'options' => array(
                                    array(
                                        'label' => __('Top', 'advanced-gutenberg'),
                                        'value' => 'top',
                                    ),
                                    array(
                                        'label' => __('Middle', 'advanced-gutenberg'),
                                        'value' => 'middle',
                                    ),
                                    array(
                                        'label' => __('Bottom', 'advanced-gutenberg'),
                                        'value' => 'bottom',
                                    ),
                                    array(
                                        'label' => __('Full Height', 'advanced-gutenberg'),
                                        'value' => 'full',
                                    ),
                                ),
                            ),
                            array(
                                'title'   => __('Columns Wrapped', 'advanced-gutenberg'),
                                'type'    => 'checkbox',
                                'name'    => 'columnsWrapped',
                            ),
                            array(
                                'title'   => __('Wrapper Tag', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'wrapperTag',
                                'options' => array(
                                    array(
                                        'label' => __('Div', 'advanced-gutenberg'),
                                        'value' => 'div',
                                    ),
                                    array(
                                        'label' => __('Header', 'advanced-gutenberg'),
                                        'value' => 'header',
                                    ),
                                    array(
                                        'label' => __('Section', 'advanced-gutenberg'),
                                        'value' => 'section',
                                    ),
                                    array(
                                        'label' => __('Main', 'advanced-gutenberg'),
                                        'value' => 'main',
                                    ),
                                    array(
                                        'label' => __('Article', 'advanced-gutenberg'),
                                        'value' => 'article',
                                    ),
                                    array(
                                        'label' => __('Aside', 'advanced-gutenberg'),
                                        'value' => 'aside',
                                    ),
                                    array(
                                        'label' => __('Footer', 'advanced-gutenberg'),
                                        'value' => 'footer',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Content Max Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'contentMaxWidth',
                                'min'   => 0,
                                'max'   => 2000,
                            ),
                            array(
                                'title' => __('Content Min Height', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'contentMinHeight',
                                'min'   => 0,
                                'max'   => 2000,
                            ),
                        ),
                    ),
                ),
                'advgb-column' => array(
                    array(
                        'label'    => __('Border Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Border style', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'borderStyle',
                                'options' => array(
                                    array(
                                        'label' => __('None', 'advanced-gutenberg'),
                                        'value' => 'none',
                                    ),
                                    array(
                                        'label' => __('Solid', 'advanced-gutenberg'),
                                        'value' => 'solid',
                                    ),
                                    array(
                                        'label' => __('Dotted', 'advanced-gutenberg'),
                                        'value' => 'dotted',
                                    ),
                                    array(
                                        'label' => __('Dashed', 'advanced-gutenberg'),
                                        'value' => 'dashed',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'borderColor',
                            ),
                            array(
                                'title' => __('Border Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderWidth',
                                'min'   => 0,
                                'max'   => 20,
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'borderRadius',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Column Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title'   => __('Text alignment', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'textAlign',
                                'options' => array(
                                    array(
                                        'label' => __('Left', 'advanced-gutenberg'),
                                        'value' => 'left',
                                    ),
                                    array(
                                        'label' => __('Center', 'advanced-gutenberg'),
                                        'value' => 'center',
                                    ),
                                    array(
                                        'label' => __('Right', 'advanced-gutenberg'),
                                        'value' => 'right',
                                    ),
                                ),
                            ),
                        ),
                    )
                ),
                'advgb-icon' => array(
                    array(
                        'label'    => __('General Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Number of icons', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'numberItem',
                                'min'   => 1,
                                'max'   => 10,
                            ),
                        ),
                    ),

                ),
                'advgb-infobox' => array(
                    array(
                        'label'    => __('Container Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'containerBackground',
                            ),
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'containerBorderBackground',
                            ),
                            array(
                                'title' => __('Border Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'containerBorderWidth',
                                'min'   => 0,
                                'max'   => 40,
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'containerBorderRadius',
                                'min'   => 0,
                                'max'   => 200,
                            ),
                            array(
                                'title' => __('Padding Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'containerPaddingTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'containerPaddingBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'containerPaddingLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'containerPaddingRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Icon Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Icon Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'iconColor',
                            ),
                            array(
                                'title' => __('Icon Size', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconSize',
                                'min'   => 1,
                                'max'   => 200,
                            ),
                            array(
                                'title' => __('Background Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'iconBackground',
                            ),
                            array(
                                'title' => __('Border Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'iconBorderBackground',
                            ),
                            array(
                                'title' => __('Border Width', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconBorderWidth',
                                'min'   => 0,
                                'max'   => 40,
                            ),
                            array(
                                'title' => __('Border Radius', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconBorderRadius',
                                'min'   => 0,
                                'max'   => 200,
                            ),
                            array(
                                'title' => __('Padding Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconPaddingTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconPaddingBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconPaddingLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconPaddingRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconMarginTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconMarginBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconMarginLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'iconMarginRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        ),
                    ),
                    array(
                        'label'    => __('Title Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Title Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'titleColor',
                            ),
                            array(
                                'title' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titleSize',
                                'min'   => 1,
                                'max'   => 200,
                            ),
                            array(
                                'title' => __('Line Height (px)', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titleLineHeight',
                                'min'   => 1,
                                'max'   => 200,
                            ),
                            array(
                                'title'   => __('HTML Tag', 'advanced-gutenberg'),
                                'type'    => 'select',
                                'name'    => 'titleHtmlTag',
                                'options' => array(
                                    array(
                                        'label' => __('H1', 'advanced-gutenberg'),
                                        'value' => 'h1',
                                    ),
                                    array(
                                        'label' => __('H2', 'advanced-gutenberg'),
                                        'value' => 'h2',
                                    ),
                                    array(
                                        'label' => __('H3', 'advanced-gutenberg'),
                                        'value' => 'h3',
                                    ),
                                    array(
                                        'label' => __('H4', 'advanced-gutenberg'),
                                        'value' => 'h4',
                                    ),
                                    array(
                                        'label' => __('H5', 'advanced-gutenberg'),
                                        'value' => 'h5',
                                    ),
                                    array(
                                        'label' => __('H6', 'advanced-gutenberg'),
                                        'value' => 'h6',
                                    ),
                                ),
                            ),
                            array(
                                'title' => __('Padding Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titlePaddingTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titlePaddingBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titlePaddingLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titlePaddingRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titleMarginTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titleMarginBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titleMarginLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'titleMarginRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        )
                    ),
                    array(
                        'label'    => __('Text Settings', 'advanced-gutenberg'),
                        'settings' => array(
                            array(
                                'title' => __('Color', 'advanced-gutenberg'),
                                'type'  => 'color',
                                'name'  => 'textColor',
                            ),
                            array(
                                'title' => __('Font Size (px)', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textSize',
                                'min'   => 1,
                                'max'   => 200,
                            ),
                            array(
                                'title' => __('Line Height (px)', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textLineHeight',
                                'min'   => 1,
                                'max'   => 200,
                            ),
                            array(
                                'title' => __('Padding Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textPaddingTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textPaddingBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textPaddingLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Padding Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textPaddingRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Top', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textMarginTop',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Bottom', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textMarginBottom',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Left', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textMarginLeft',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                            array(
                                'title' => __('Margin Right', 'advanced-gutenberg'),
                                'type'  => 'number',
                                'name'  => 'textMarginRight',
                                'min'   => 0,
                                'max'   => 100,
                            ),
                        )
                    ),
                ),
            );

            $advgb_blocks_default_config = get_option('advgb_blocks_default_config');
            $current_block = $block;

            if (!isset($blocks_settings_list[$current_block])) {
                wp_die(esc_html(__('Default configuration for this block is not available.', 'advanced-gutenberg')));
                return;
            }

            $current_block_settings = $blocks_settings_list[$current_block];
            $current_block_settings_value = array();

            if ($advgb_blocks_default_config !== false) {
                if (isset($advgb_blocks_default_config[$current_block])) {
                    $current_block_settings_value = $advgb_blocks_default_config[$current_block];
                }
            }

            require_once(plugin_dir_path(__FILE__) . 'view/advanced-gutenberg-block-config.php');
        }

        /**
         * Function to get and load the view
         *
         * @param string $view View to load
         *
         * @return void
         */
        public function loadView($view)
        {
            include_once(plugin_dir_path(__FILE__) . 'view/advanced-gutenberg-' . $view . '.php');
        }

        /**
         * Function to load assets for post/page on front-end before gutenberg rendering
         *
         * @param array $block Block data
         *
         * @return array       New block data
         */
        public function contentPreRender($block)
        {
            // Search for needed blocks then add styles to it
            $style = $this->addBlocksStyles($block);
            array_push($block['innerContent'], $style);

            return $block;
        }

        /**
		 * Recursive loop to find nested blocks and load their blocks's CSS and media files
		 *
		 * @since   2.6.1
		 * @param   object  $block      Nested block
		 * @param   string  $style_html CSS Styles
		 * @param   integer $level      Nested block level
		 * @return  string              CSS Styles
		 */
		public function advgb_getNestedBlocksStyles($block, $level = 2, &$style_html = array()){

			if(isset($block['innerBlocks'])){
				foreach($block['innerBlocks'] as $key => $inner_block){

					// Get styles
					$new_style_html = $this->advgb_SetStylesForBlocks($inner_block['attrs'], $inner_block['blockName']);

					// Add the styles to the array
					$style_html[] = $new_style_html;
					//echo str_repeat("--", $level) . $inner_block['blockName'] . ' [ ' . $level . ' ]<br>';

					self::advgb_getNestedBlocksStyles($inner_block, $level + 1, $style_html);
				}
			}

			$final_styles = $style_html;
			if( ! is_string( $final_styles ) ) {
				// Convert array to string
				$final_styles = implode( '', array_unique( $style_html ) );
			}

			//echo '<code>' . $final_styles . '</code>'; // This output is correct!

			return $final_styles;
		}

        /**
         * Check to disable autop used to prevent unwanted paragraphs to blocks
         *
         * @param string $filter_name filter name; 'the_content' or 'widget_block_content'
         *
         * @return void
         */
        public function checkToDisableWpautop($filter_name)
        {
            $saved_settings = false;
            if (has_filter($filter_name, 'wpautop')) {
                if (!$saved_settings) {
                    $saved_settings = get_option('advgb_settings');
                }

                if (!empty($saved_settings['disable_wpautop'])) {
                    remove_filter($filter_name, 'wpautop');
                }
            }
        }

        /**
         * Load assets for post/page on front-end after gutenberg rendering
         *
         * @param string $content Post content
         *
         * @return string
         */
        public function addFrontendContentAssets($content)
        {
            $this->checkToDisableWpautop('the_content');
            $this->setFrontendAssets($content);
            $content = $this->groupStylesTag($content);

            return $content;
        }

        /**
         * Load assets for widgets on front-end after gutenberg rendering
         *
         * @param string $text Widget content
         *
         * @return string
         */
        public function addFrontendWidgetAssets($text)
        {
            $this->checkToDisableWpautop('widget_block_content');
            $this->setFrontendAssets($text);
            $text = $this->groupStylesTag($text, false);

            return $text;
        }

        /**
         * Set frontend assets and styles for posts
         *
         * @param string $content Post content
         *
         * @return string
         */
        public function setFrontendAssets($content)
        {
            wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');

            // Preview in Customizer > Widgets
            if( isset($_GET['customize_theme']) ) {

                wp_enqueue_style('colorbox_style');
                wp_enqueue_style('slick_style');
                wp_enqueue_style('slick_theme_style');

                wp_enqueue_script('colorbox_js');
                wp_enqueue_script('slick_js');
                wp_enqueue_script('advgb_masonry_js');

                wp_enqueue_script(
                    'advgb_widgets_customizer_js',
                    plugins_url('assets/js/widgets-customizer.js', dirname(__FILE__)),
                    array(
                        'colorbox_js',
                        'slick_js',
                        'advgb_masonry_js'
                    ),
                    ADVANCED_GUTENBERG_VERSION
                );

                // Pro
                if( defined( 'ADVANCED_GUTENBERG_PRO' ) && $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                    if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_widgets_customizer_frontend' ) ) {
                        PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_widgets_customizer_frontend();
                    }
                }

                // Patch for Twenty Twenty-One
                $this->fixCssGridFooterWidgets();
            }

            if (strpos($content, 'wp-block-gallery') !== false) {
                $saved_settings = get_option('advgb_settings');

                if ($saved_settings['gallery_lightbox']) {
                    wp_enqueue_style('colorbox_style');
                    wp_enqueue_script('colorbox_js');

                    wp_enqueue_script(
                        'gallery_lightbox_js',
                        plugins_url('assets/js/gallery.colorbox.init.js', dirname(__FILE__)),
                        array(),
                        ADVANCED_GUTENBERG_VERSION
                    );

                    wp_localize_script('gallery_lightbox_js', 'advgb', array(
                        'imageCaption' => $saved_settings['gallery_lightbox_caption']
                    ));
                }
            }

            if (strpos($content, 'advgb-toc-header') !== false) {
                wp_enqueue_script(
                    'summary_minimized',
                    plugins_url('assets/blocks/summary/summaryMinimized.js', dirname(__FILE__)),
                    array('jquery')
                );
            }

            if (strpos($content, 'wp-block-advgb-count-up') !== false) {
                $content = preg_replace_callback(
                    '@<div[^>]*?advgb\-count\-up\-columns.*?(</p></div>)@s',
                    array($this, 'decodeHtmlEntity'),
                    $content
                );
            }

            if (strpos($content, 'advgb-image-block') !== false) {
                if (strpos($content, 'advgb-lightbox') !== false) {
                    wp_enqueue_style('colorbox_style');
                    wp_enqueue_script('colorbox_js');

                    wp_enqueue_script(
                        'advgbImageLightbox_js',
                        plugins_url('assets/blocks/advimage/lightbox.js', dirname(__FILE__)),
                        array(),
                        ADVANCED_GUTENBERG_VERSION
                    );
                }
            }

            if (strpos($content, 'advgb-video-lightbox') !== false) {
                wp_enqueue_style('colorbox_style');
                wp_enqueue_script('colorbox_js');

                wp_enqueue_script(
                    'advgbVideoLightbox_js',
                    plugins_url('assets/blocks/advvideo/lightbox.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
            }

            if (strpos($content, 'advgb-map-block') !== false) {
                wp_enqueue_script(
                    'advgb_gmap_js',
                    plugins_url('assets/blocks/map/frontend.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                $this->loadGoogleMapApi();
            }

            if (strpos($content, 'advgb-accordion-block') !== false) {
                wp_enqueue_script('jquery-ui-accordion');
                wp_add_inline_script('jquery-ui-accordion', 'jQuery(document).ready(function($){
                    $(".advgb-accordion-block").parent().each(function() {
                        $(this).accordion({
                            header: "> div > .advgb-accordion-header",
                            heightStyle: "content",
                            collapsible: true,
                            active: $(this).find(".advgb-accordion-block:first").data("collapsed") ? false : 0,
                        });
                    });
                });');
            }

            if (strpos($content, 'advgb-accordion-wrapper') !== false) {
                wp_enqueue_script('jquery-ui-accordion');
                wp_add_inline_script('jquery-ui-accordion', 'jQuery(document).ready(function($){
                    $(".advgb-accordion-wrapper").each(function() {
                        $(this).accordion({
                            header: "> div > .advgb-accordion-header",
                            heightStyle: "content",
                            collapsible: true,
                            active: $(this).data("collapsed") ? false : 0,
                        });
                    });
                });');
                $content = preg_replace_callback(
                    '@<div[^>]*?advgb\-accordion\-wrapper.*?(</div></div>.?</div>)@s',
                    array($this, 'decodeHtmlEntity'),
                    $content
                );
            }

            if (strpos($content, 'advgb-tabs-block') !== false) {
                wp_enqueue_script('jquery-ui-tabs');
                wp_add_inline_script('jquery-ui-tabs', 'jQuery(document).ready(function($){
                     $(".advgb-tab a:not(.ui-tabs-anchor)").unbind("click");
                     $(".advgb-tabs-block").tabs();
                 });');
            }

            if (strpos($content, 'advgb-tabs-wrapper') !== false) {
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script(
                    'advgb_tabs_js',
                    plugins_url('assets/blocks/advtabs/frontend.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );
                // Fix broken tags: &lt;strong> and &lt;em>
                $content = preg_replace_callback(
                    array(
                        '@<ul class="advgb-tabs-panel">(.*?)&lt;(?:strong|em)>(.*?)&lt;/(?:strong|em)>(.*?)</ul>@s',
                        '@<div class="advgb-tab-body-header">(.*?)&lt;(?:strong|em)>(.*?)&lt;/(?:strong|em)>(.*?)</div>@s'
                    ),
                    array($this, 'decodeHtmlEntity'),
                    $content
                );
            }

            if (strpos($content, 'advgb-recent-posts-block') !== false) {
                wp_enqueue_style('advgb_recent_posts_styles');
            }

            if (strpos($content, 'advgb-recent-posts-block slider-view') !== false) {
                wp_enqueue_style('slick_style');
                wp_enqueue_style('slick_theme_style');
                wp_enqueue_script('slick_js');
                wp_add_inline_script('slick_js', 'jQuery(document).ready(function($){
                    $(".advgb-recent-posts-block.slider-view").find(".advgb-recent-posts:not(.slick-initialized)").each(function() {
                        $(this).slick({
                            dots: true,
                            adaptiveHeight: true,
                        });
                        $(this).slick("slickSetOption", "autoplay", $(this).parent().hasClass("slider-autoplay"), true);
                    });
                });');

                // Patch for Twenty Twenty-One
                $this->fixCssGridFooterWidgets();
            }

            if (strpos($content, 'advgb-recent-posts-block masonry-view') !== false) {
                wp_enqueue_script('advgb_masonry_js');
                wp_add_inline_script('advgb_masonry_js', 'document.addEventListener("DOMContentLoaded", function(){
                    (function($) {
                        $(\'.masonry-view .advgb-recent-posts\').isotope({
                            itemSelector: \'.advgb-recent-post\',
                            percentPosition: true
                        });
                        $(window).on(\'load resize\', function(){
                            $(\'.masonry-view .advgb-recent-posts\').isotope();
                        });
                    })(jQuery);
                });');
            }

            if (strpos($content, 'advgb-woo-products slider-view') !== false) {
                wp_enqueue_style('slick_style');
                wp_enqueue_style('slick_theme_style');
                wp_enqueue_script('slick_js');
                wp_add_inline_script('slick_js', 'jQuery(document).ready(function($){
                    $(".advgb-woo-products.slider-view").on("init", function(event){
                      $(this).find("div.woocommerce, ul.products").removeClass("columns-1 columns-2 columns-3 columns-4");
                    });
                    $(".advgb-woo-products.slider-view .products:not(.slick-initialized)").slick({
                        dots: true,
                        adaptiveHeight: true,
                    })
                });');

                // Patch for Twenty Twenty-One
                $this->fixCssGridFooterWidgets();
            }

            if (strpos($content, 'advgb-images-slider-block') !== false) {
                wp_enqueue_style('slick_style');
                wp_enqueue_style('slick_theme_style');
                wp_enqueue_script('slick_js');

                wp_add_inline_script('slick_js', 'jQuery(document).ready(function($){
                    $(".advgb-images-slider-block .advgb-images-slider:not(.slick-initialized)").slick({
                        dots: true,
                        adaptiveHeight: true,
                    })
                });');

                if (strpos($content, 'advgb-images-slider-lightbox') !== false) {
                    wp_enqueue_style('colorbox_style');
                    wp_enqueue_script('colorbox_js');

                    wp_enqueue_script(
                        'advgbImageSliderLightbox_js',
                        plugins_url('assets/blocks/images-slider/lightbox.js', dirname(__FILE__)),
                        array(),
                        ADVANCED_GUTENBERG_VERSION
                    );
                }

                // Patch for Twenty Twenty-One
                $this->fixCssGridFooterWidgets();
            }

            if (strpos($content, 'advgb-contact-form') !== false) {
                wp_enqueue_script(
                    'advgbContactForm_js',
                    plugins_url('assets/blocks/contact-form/frontend.js', dirname(__FILE__)),
                    array('jquery'),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_localize_script('advgbContactForm_js', 'advgbContactForm', array('ajax_url' => admin_url('admin-ajax.php')));
                $this->loadRecaptchaApi();
            }

            if (strpos($content, 'advgb-newsletter') !== false) {
                wp_enqueue_script(
                    'advgbNewsletter_js',
                    plugins_url('assets/blocks/newsletter/frontend.js', dirname(__FILE__)),
                    array('jquery', 'wp-i18n'),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_localize_script('advgbNewsletter_js', 'advgbNewsletter', array('ajax_url' => admin_url('admin-ajax.php')));
                $this->loadRecaptchaApi();
            }

            if (strpos($content, 'advgb-testimonial slider-view') !== false) {
                wp_enqueue_style('slick_style');
                wp_enqueue_style('slick_theme_style');
                wp_enqueue_script('slick_js');
                wp_enqueue_script(
                    'advgb_testimonial_frontend',
                    plugins_url('assets/blocks/testimonial/frontend.js', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );

                // Patch for Twenty Twenty-One
                $this->fixCssGridFooterWidgets();
            }

            if (strpos($content, 'advgb-testimonial') !== false) {
                $content = preg_replace_callback(
                    '@<div[^>]*?advgb\-testimonial.*?(</p></div></div>)@s',
                    array($this, 'decodeHtmlEntity'),
                    $content
                );
            }

            if (strpos($content, 'advgb-columns') !== false) {
                wp_enqueue_style('advgb_columns_styles');
            }

            if (strpos($content, 'advgb-lores-form-wrapper') !== false) {
                wp_enqueue_script('jquery-effects-slide');
                wp_enqueue_script(
                    'advgb_lores_js',
                    plugins_url('assets/blocks/login-form/frontend.js', dirname(__FILE__)),
                    array('jquery'),
                    ADVANCED_GUTENBERG_VERSION
                );
                wp_localize_script('advgb_lores_js', 'advgbLoresForm', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'login_url' => wp_login_url(),
                    'register_url' => wp_registration_url(),
                    'lostpwd_url' => wp_lostpassword_url(),
                    'home_url' => home_url(),
                    'admin_url' => admin_url(),
                    'register_enabled' => get_option('users_can_register'),
                    'unregistrable_notice' => __('User registration is currently not allowed.', 'advanced-gutenberg'),
                    'captcha_empty_warning' => __('Captcha must be checked!', 'advanced-gutenberg'),
                    'login_failed_notice' => __('Username or password is incorrect!', 'advanced-gutenberg'),
                ));
                $this->loadRecaptchaApi();
            }

            // Pro
            if( defined( 'ADVANCED_GUTENBERG_PRO' ) && $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_enqueue_scripts_frontend' ) ) {
                    PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_enqueue_scripts_frontend($content);
                }
            }

            return $content;
        }

        /**
         * Add styles for Adv Button and Adv List
         *
         * @param array $block Block data
         *
         * @return string HTML style
         */
        public function addBlocksStyles($block)
        {
            $style_html = '';
            $blockName = $block['blockName'];
            $blockAttrs = $block['attrs'];

            if ($blockName) {
                // Get styles for parent blocks
                $style_html .= $this->advgb_SetStylesForBlocks($blockAttrs, $blockName);

                // Parse styles for nested blocks in WP 5.5+
                global $wp_version;
                if($wp_version >= 5.5) {

                    // Check blocks in 2nd level and beyond
                    $style_html .= $this->advgb_getNestedBlocksStyles($block);
                }
            }

            $style_html = $style_html ? '<style type="text/css" class="advgb-blocks-styles-renderer">' . $style_html . '</style>' : '';

            return preg_replace('/\s\s+/', '', $style_html);
        }

        /**
         * Find all style tag and append them in the end of content
         *
         * @param string $content Content to be prettify
         * @param boolean $is_post if the content is false, is a widget
         *
         * @return string          New prettified string
         */
        public function groupStylesTag($content, $is_post = true)
        {
            // Group styles tag in the end of the content
            preg_match_all('/(<style.*?>)(.*?)(<\/style>)/mis', $content, $styles_html);
            $styles_tag = '';
            if (count($styles_html[0])) {
                foreach ($styles_html[0] as $key => $style_html) {
                    $content = str_replace($style_html, '', $content);
                    $styles_tag .= $styles_html[2][$key];
                }
            }

            if ($styles_tag) {
                $content .= '<style class="advgb-styles-renderer' . ( $is_post === false ? '-widget' : '' ) . '">'.$styles_tag.'</style>';
            }

            return $content;
        }

        /**
         * Convert html entity to real character
         *
         * @param string $match Matched string
         *
         * @return mixed
         */
        public function decodeHtmlEntity($match)
        {
            return str_replace('&lt;', '<', $match[0]);
        }

        /**
         * Function to load external plugins for tinyMCE
         *
         * @param array $plgs External plugins
         *
         * @return array
         */
        public function addTinyMceExternal(array $plgs)
        {
            $plgs['customstyles'] = plugin_dir_url(dirname(__FILE__)) . 'assets/blocks/customstyles/plugin.js';

            return $plgs;
        }

        /**
         * Function to add buttons for tinyMCE toolbars
         *
         * @param array $buttons TinyMCE buttons
         *
         * @return array
         */
        public function addTinyMceButtons(array $buttons)
        {
            array_push($buttons, 'customstyles');

            return $buttons;
        }

        /**
         * Render html for block config settings fields
         *
         * @param array $fieldset Array of field to render
         * @param array $data     Data will load as value to field
         *
         * @return boolean        Echo html content
         */
        public function renderBlockConfigFields($fieldset, $data)
        {
            $html = '';
            foreach ($fieldset as $category) {
                $html .= '<div class="block-config-category">';
                $html .= '<h3 class="block-config-category-title">' . $category['label'] . '</h3>';
                $html .= '<ul class="block-config-settings clearfix">';

                foreach ($category['settings'] as $setting) {
                    $settingValue = $this->setConfigValue($data, $setting['name']);

                    if ($setting['type'] === 'hidden') {
                        $html .= '<input type="hidden" class="block-config-input" name="'. $setting['name'] .'" value="'. $setting['value'] .'" />';
                        continue;
                    }

                    $html .= '<li class="ju-settings-option full-width block-config-option clearfix">';
                    $html .= '<label for="setting-'. $setting['name'] .'" class="ju-setting-label">' . $setting['title'] . '</label>';
                    $html .= '<div class="block-config-input-wrapper">';

                    switch ($setting['type']) {
                        case 'text':
                        case 'number':
                            $html .= '<input type="' . $setting['type'] . '" class="ju-input block-config-input" id="setting-'. $setting['name'] .'" name="' . $setting['name'] . '" ';
                            if ($setting['type'] === 'number' && (isset($setting['min']) || isset($setting['max']))) {
                                $html .= ' min="' . $setting['min'] . '" max="' . $setting['max'] . '" ';
                            }
                            $html .= ' value="'. $settingValue .'" />';
                            break;
                        case 'color':
                            $html .= '<input type="text" class="minicolors minicolors-input ju-input block-config-input" id="setting-'. $setting['name'] .'" name="' . $setting['name'] . '" value="'. $settingValue .'" />';
                            break;
                        case 'select':
                            $html .= '<select class="block-config-input ju-select" id="setting-'. $setting['name'] .'" name="' . $setting['name'] . '">';
                            $html .= '<option value="">'. __('Default', 'advanced-gutenberg') .'</option>';

                            foreach ($setting['options'] as $option) {
                                $selected = $option['value'] === $settingValue ? 'selected' : '';
                                $html .= '<option value="' . $option['value'] . '" ' . $selected . '>' . $option['label'] . '</option>';
                            }

                            $html .= '</select>';
                            break;
                        case 'checkbox':
                            $checked = (int)$settingValue === 1 ? 'checked' : '';
                            $html .= '<div class="ju-switch-button">';
                            $html .= '<label class="switch">';
                            $html .=    '<input type="checkbox" value="1" class="block-config-input" id="setting-'. $setting['name'] .'" name="' . $setting['name'] . '" ' . $checked . '/>';
                            $html .=    '<span class="slider"></span>';
                            $html .= '</label>';
                            $html .= '</div>';
                            break;
                        default:
                            $html .= '<div>' . __('Type field not defined', 'advanced-gutenberg') . '</div>';
                            break;
                    }

                    $html .= '</div>';
                    $html .= '</li>';
                }

                $html .= '</ul>';
                $html .= '</div>';
            }

            echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped
            return true;
        }

        /**
         * Load reCaptcha v2 scripts
         *
         * @return void
         */
        public function loadRecaptchaApi() {
            $recaptcha_config = get_option('advgb_recaptcha_config');
            if (!is_admin() && isset($recaptcha_config['recaptcha_enable']) && $recaptcha_config['recaptcha_enable']) {
                $lang = $recaptcha_config['recaptcha_language'] ? '&hl='.$recaptcha_config['recaptcha_language'] : '';
                wp_enqueue_script(
                    'advgb_recaptcha_js',
                    'https://www.google.com/recaptcha/api.js?onload=advgbRecaptchaInit&render=explicit' . $lang
                );

                if (isset($recaptcha_config['recaptcha_site_key']) && $recaptcha_config['recaptcha_site_key']) {
                    wp_enqueue_script(
                        'advgb_recaptcha_init_js',
                        plugins_url('assets/js/recaptcha.js', dirname(__FILE__))
                    );

                    wp_localize_script('advgb_recaptcha_init_js', 'advgbGRC', array(
                        'site_key' => $recaptcha_config['recaptcha_site_key'],
                        'theme' => $recaptcha_config['recaptcha_theme'],
                    ));
                }
            }
        }

        /**
         * Load Google Maps script
         *
         * @return void
         */
        public function loadGoogleMapApi() {
            $saved_settings = get_option('advgb_settings');
            if (isset($saved_settings['google_api_key']) && !empty($saved_settings['google_api_key'])) {
                wp_enqueue_script(
                    'advgb_map_api',
                    'https://maps.googleapis.com/maps/api/js?key='. $saved_settings['google_api_key']
                );
            }
        }

        /**
         * Add CSS patch when using Twenty Twenty-One in footer with Slick slideshow
         * https://core.trac.wordpress.org/ticket/53649
         * https://github.com/kenwheeler/slick/issues/3415
         *
         * @return void
         */
        public function fixCssGridFooterWidgets() {
            $ctheme = wp_get_theme();
            if ( 'Twenty Twenty-One' == $ctheme->name || 'Twenty Twenty-One' == $ctheme->parent_theme ) {
                wp_add_inline_style(
                    'advgb_blocks_styles',
                    '@media only screen and (min-width: 652px) {
                        .widget-area {
                          grid-template-columns: repeat(2, minmax( 0, 1fr ));
                        }
                      }
                      @media only screen and (min-width: 1024px) {
                        .widget-area {
                          grid-template-columns: repeat(3, minmax( 0, 1fr ));
                        }
                      }
                  }'
                );
            }
        }

        /**
         * Check if a setting is enabled
         *
         * @param string $setting The setting from advgb_settings option field
         *
         * @return boolean
         */
        public function settingIsEnabled( $setting ) {
            $saved_settings = get_option('advgb_settings');
            if( !isset($saved_settings[$setting]) || $saved_settings[$setting] ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Set value to config field
         *
         * @param array  $valueList  Array of values to set
         * @param string $valueToGet Value field to set
         * @param mixed  $default    Default value if value field from list not set
         *
         * @return string           Value after check
         */
        private function setConfigValue($valueList, $valueToGet, $default = '')
        {
            $valueReturn = $default;
            if (gettype($valueList) === 'array') {
                if (isset($valueList[$valueToGet])) {
                    $valueReturn = $valueList[$valueToGet];
                }
            }

            return $valueReturn;
        }

        /**
         * Dynamically load the blocks's CSS and media files
         *
         * @since   2.4.2
         * @param   $blockName
         * @param   $blockAttrs
         * @return  string      CSS Styles
         */
        public function advgb_SetStylesForBlocks($blockAttrs, $blockName)
        {
            $availableBlocks = array(
                'advgb/adv-tabs',
                'advgb/adv-tab',
                'advgb/columns',
                'advgb/column',
                'advgb/image',
                'advgb/icon',
                'advgb/accordions',
                'advgb/accordion-item',
                'advgb/table',
                'advgb/infobox',
                'advgb/button',
                'advgb/login-form',
                'advgb/count-up',
                'advgb/summary',
                'advgb/contact-form',
                'advgb/images-slider',
                'advgb/map',
                'advgb/newsletter',
                'advgb/testimonial',
                'advgb/list',
                'advgb/video',
                'advgb/recent-posts',
                'advgb/search-bar',
                'advgb/social-links',
                'advgb/woo-products',
                'advgb/tabs'
            );

            // Pro
            if(defined('ADVANCED_GUTENBERG_PRO')) {
                if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_get_blocks' ) ) {
                    $availableProBlocks = PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_get_blocks();
                    foreach ($availableProBlocks as $availableProBlock) {
                        array_push(
                            $availableBlocks,
                            $availableProBlock
                        );
                    }
            	}
            }

            // Load common CSS
            if ( in_array( $blockName, $availableBlocks ) && $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
                wp_enqueue_style(
                    'advgb_blocks_styles',
                    plugins_url('assets/css/blocks.css', dirname(__FILE__)),
                    array(),
                    ADVANCED_GUTENBERG_VERSION
                );

                // Pro
                if(defined('ADVANCED_GUTENBERG_PRO')) {
                    if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_enqueue_main_styles' ) ) {
                        PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_enqueue_main_styles();
                    }
                }
            }

            $html_style = '';

            switch($blockName) {

                case 'advgb/list':
                    $html_style = $this->advgb_AdvancedListStyles($blockAttrs);
                    break;

                case 'advgb/button':
                    $html_style = $this->advgb_AdvancedButtonStyles($blockAttrs);
                    break;

                case 'advgb/column':
                case 'advgb/columns':
                    $html_style = $this->advgb_AdvancedColumnsStyles($blockAttrs, $blockName);
                    break;

                case 'advgb/login-form':
                    $html_style = $this->advgb_AdvancedLoginRegisterStyles($blockAttrs);
                    break;

                case 'advgb/search-bar':
                    $html_style = $this->advgb_AdvancedSearchBarStyles($blockAttrs);
                    break;

                case 'advgb/image':
                    $html_style = $this->advgb_AdvancedImageStyles($blockAttrs);
                    break;

                case 'advgb/testimonial':
                    $html_style = $this->advgb_AdvancedTestimonialStyles($blockAttrs);
                    break;

                case 'advgb/adv-tabs':
                    $html_style = $this->advgb_AdvancedTabsStyles($blockAttrs);
                    break;

                case 'advgb/icon':
                    $html_style = $this->advgb_AdvancedIconStyles($blockAttrs);
                    break;

                case 'advgb/infobox':
                    $html_style = $this->advgb_AdvancedInfoBoxStyles($blockAttrs);
                    break;

                case 'advgb/count-up':
                    $html_style = $this->advgb_AdvancedCountUpStyles($blockAttrs);
                    break;

                default:
                    // Nothing to do here
                    break;
            }

            // Pro
            if(defined('ADVANCED_GUTENBERG_PRO')) {
                if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_set_styles_for_blocks' ) ) {
                    $html_style .= PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_set_styles_for_blocks($blockAttrs, $blockName);
                }
            }

            return $html_style;
        }

        /**
         * Styles for Adv. Lists Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedListStyles($blockAttrs)
        {
            $block_class    = $blockAttrs['id'];
            $font_size      = isset($blockAttrs['fontSize']) ? intval($blockAttrs['fontSize']) : 16;
            $icon_size      = isset($blockAttrs['iconSize']) ? intval($blockAttrs['iconSize']) : 16;
            $icon_color     = isset($blockAttrs['iconColor']) ? $blockAttrs['iconColor'] : '#000';
            $margin         = isset($blockAttrs['margin']) ? intval($blockAttrs['margin']) : 2;
            $padding        = isset($blockAttrs['padding']) ? intval($blockAttrs['padding']) : 2;
            $line_height    = isset($blockAttrs['lineHeight']) ? intval($blockAttrs['lineHeight']) : 18;

            $style_html  = '.wp-block-advgb-list ul.' . $block_class . ' > li{';
            $style_html .= 'font-size:'.$font_size.'px;';
            $style_html .= '}';
            if (isset($blockAttrs['icon']) && !empty($blockAttrs['icon'])) {
                $style_html .= '.wp-block-advgb-list ul.' . $block_class . ' > li{';
                $style_html .= 'padding-left:'.($icon_size + $padding).'px;margin-left:0;';
                $style_html .= '}';
                $style_html .= '.wp-block-advgb-list ul.' . $block_class . ' > li:before{';
                $style_html .= 'font-size:'.$icon_size.'px;';
                $style_html .= 'color:'.$icon_color.';';
                $style_html .= 'line-height:'.$line_height.'px;';
                $style_html .= 'margin:'.$margin.'px;';
                $style_html .= 'padding:'.$padding.'px;';
                $style_html .= 'margin-left:-'.($icon_size + $padding + $margin).'px';
                $style_html .= '}';
            }

            return $style_html;
        }

        /**
         * Styles for Adv. Button Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedButtonStyles($blockAttrs)
        {
            $block_class    = $blockAttrs['id'];
            $font_size      = isset($blockAttrs['textSize']) ? intval($blockAttrs['textSize']) : 18;
            $color          = isset($blockAttrs['textColor']) ? $blockAttrs['textColor'] : '#fff';
            $bg_color       = isset($blockAttrs['bgColor']) ? $blockAttrs['bgColor'] : '#2196f3';
            $mg_top         = isset($blockAttrs['marginTop']) ? intval($blockAttrs['marginTop']) : 0;
            $mg_right       = isset($blockAttrs['marginRight']) ? intval($blockAttrs['marginRight']) : 0;
            $mg_bottom      = isset($blockAttrs['marginBottom']) ? intval($blockAttrs['marginBottom']) : 0;
            $mg_left        = isset($blockAttrs['marginLeft']) ? intval($blockAttrs['marginLeft']) : 0;
            $pd_top         = isset($blockAttrs['paddingTop']) ? intval($blockAttrs['paddingTop']) : 10;
            $pd_right       = isset($blockAttrs['paddingRight']) ? intval($blockAttrs['paddingRight']) : 30;
            $pd_bottom      = isset($blockAttrs['paddingBottom']) ? intval($blockAttrs['paddingBottom']) : 10;
            $pd_left        = isset($blockAttrs['paddingLeft']) ? intval($blockAttrs['paddingLeft']) : 30;
            $border_width   = isset($blockAttrs['borderWidth']) ? intval($blockAttrs['borderWidth']) : 1;
            $border_color   = isset($blockAttrs['borderColor']) ? $blockAttrs['borderColor'] : '';
            $border_style   = isset($blockAttrs['borderStyle']) ? $blockAttrs['borderStyle'] : 'none';
            $border_radius  = isset($blockAttrs['borderRadius']) ? intval($blockAttrs['borderRadius']) : 50;
            $hover_t_color  = isset($blockAttrs['hoverTextColor']) ? $blockAttrs['hoverTextColor'] : '';
            $hover_bg_color = isset($blockAttrs['hoverBgColor']) ? $blockAttrs['hoverBgColor'] : '';
            $hover_sh_color = isset($blockAttrs['hoverShadowColor']) ? $blockAttrs['hoverShadowColor'] : '#ccc';
            $hover_sh_h     = isset($blockAttrs['hoverShadowH']) ? intval($blockAttrs['hoverShadowH']) : 1;
            $hover_sh_v     = isset($blockAttrs['hoverShadowV']) ? intval($blockAttrs['hoverShadowV']) : 1;
            $hover_sh_blur  = isset($blockAttrs['hoverShadowBlur']) ? intval($blockAttrs['hoverShadowBlur']) : 12;
            $hover_sh_sprd  = isset($blockAttrs['hoverShadowSpread']) ? intval($blockAttrs['hoverShadowSpread']) : 0;
            $hover_opacity  = isset($blockAttrs['hoverOpacity']) ? intval($blockAttrs['hoverOpacity'])/100 : 1;
            $transition_spd = isset($blockAttrs['transitionSpeed']) ? floatval($blockAttrs['transitionSpeed'])/1000 : 0.2;

            $style_html  = '.'. $block_class . '{';
            $style_html .= 'font-size:'.$font_size.'px;';
            $style_html .= 'color:'.$color.' !important;';
            $style_html .= 'background-color:'.$bg_color.' !important;';
            $style_html .= 'margin:'.$mg_top.'px '.$mg_right.'px '.$mg_bottom.'px '.$mg_left.'px !important;';
            $style_html .= 'padding:'.$pd_top.'px '.$pd_right.'px '.$pd_bottom.'px '.$pd_left.'px;';
            $style_html .= 'border-width:'.$border_width.'px !important;';
            $style_html .= 'border-color:'.$border_color.' !important;';
            $style_html .= 'border-style:'.$border_style. ($border_style === 'none' ? '' : ' !important') .';';
            $style_html .= 'border-radius:'.$border_radius.'px !important;';
            $style_html .= '}';

            $style_html .= '.'. $block_class . ':hover{';
            $style_html .= 'color:'.$hover_t_color.' !important;';
            $style_html .= 'background-color:'.$hover_bg_color.' !important;';
            $style_html .= 'box-shadow:'.$hover_sh_h.'px '.$hover_sh_v.'px '.$hover_sh_blur.'px '.$hover_sh_sprd.'px '.$hover_sh_color.';';
            $style_html .= 'opacity:'.$hover_opacity.';';
            $style_html .= 'transition:all '.$transition_spd.'s ease;';
            $style_html .= '}';

            return $style_html;
        }

        /**
         * Styles for Adv. Columns Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @param   $blockName  The block name
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedColumnsStyles($blockAttrs, $blockName)
        {
            $colID      = $blockAttrs['colId'];
            $marginUnit = 'px';
            $paddingUnit = 'px';
            if ($blockName === 'advgb/column') {
                $colID = $colID . '>.advgb-column-inner';
            }

            if (isset($blockAttrs['marginUnit'])) {
                $marginUnit = $blockAttrs['marginUnit'];
            }

            if (isset($blockAttrs['paddingUnit'])) {
                $paddingUnit = $blockAttrs['paddingUnit'];
            }

            $style_html  = '#'. $colID . '{';
            $style_html .= isset($blockAttrs['textAlign']) ? 'text-align:'.$blockAttrs['textAlign'].';' : '';
            $style_html .= isset($blockAttrs['marginTop']) ? 'margin-top:'.$blockAttrs['marginTop'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginRight']) ? 'margin-right:'.$blockAttrs['marginRight'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginBottom']) ? 'margin-bottom:'.$blockAttrs['marginBottom'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginLeft']) ? 'margin-left:'.$blockAttrs['marginLeft'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingTop']) ? 'padding-top:'.$blockAttrs['paddingTop'].$paddingUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingRight']) ? 'padding-right:'.$blockAttrs['paddingRight'].$paddingUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingBottom']) ? 'padding-bottom:'.$blockAttrs['paddingBottom'].$paddingUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingLeft']) ? 'padding-left:'.$blockAttrs['paddingLeft'].$paddingUnit.';' : '';
            $style_html .= '}';

            if ($blockName === 'advgb/column') {
                $childColID = $blockAttrs['colId'];
                $childColWidth = $blockAttrs['width'];
                if ($childColWidth !== 0) {
                    $style_html .= '#' . $childColID . '{width: ' . $childColWidth . '%;}';
                }
            }

            // Styles for tablet
            $style_html .= '@media screen and (max-width: 1023px) {';
            $style_html .=  '#'. $colID . '{';
            $style_html .= isset($blockAttrs['marginTopT']) ? 'margin-top:'.$blockAttrs['marginTopT'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginRightT']) ? 'margin-right:'.$blockAttrs['marginRightT'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginBottomT']) ? 'margin-bottom:'.$blockAttrs['marginBottomT'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginLeftT']) ? 'margin-left:'.$blockAttrs['marginLeftT'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingTopT']) ? 'padding-top:'.$blockAttrs['paddingTopT'].$paddingUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingRightT']) ? 'padding-right:'.$blockAttrs['paddingRightT'].$paddingUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingBottomT']) ? 'padding-bottom:'.$blockAttrs['paddingBottomT'].$paddingUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingLeftT']) ? 'padding-left:'.$blockAttrs['paddingLeftT'].$paddingUnit.';' : '';
            $style_html .=  '}';
            $style_html .= '}';

            // Styles for mobile
            $style_html .= '@media screen and (max-width: 767px) {';
            $style_html .=  '#'. $colID . '{';
            $style_html .= isset($blockAttrs['textAlignM']) ? 'text-align:'.$blockAttrs['textAlignM'].';' : '';
            $style_html .= isset($blockAttrs['marginTopM']) ? 'margin-top:'.$blockAttrs['marginTopM'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginRightM']) ? 'margin-right:'.$blockAttrs['marginRightM'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginBottomM']) ? 'margin-bottom:'.$blockAttrs['marginBottomM'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['marginLeftM']) ? 'margin-left:'.$blockAttrs['marginLeftM'].$marginUnit.';' : '';
            $style_html .= isset($blockAttrs['paddingTopM']) ? 'padding-top:'.$blockAttrs['paddingTopM'].'px;' : '';
            $style_html .= isset($blockAttrs['paddingRightM']) ? 'padding-right:'.$blockAttrs['paddingRightM'].'px;' : '';
            $style_html .= isset($blockAttrs['paddingBottomM']) ? 'padding-bottom:'.$blockAttrs['paddingBottomM'].'px;' : '';
            $style_html .= isset($blockAttrs['paddingLeftM']) ? 'padding-left:'.$blockAttrs['paddingLeftM'].'px;' : '';
            $style_html .=  '}';
            $style_html .= '}';

            return $style_html;
        }

        /**
         * Styles for Login / Register Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedLoginRegisterStyles($blockAttrs)
        {
            $block_class    = $blockAttrs['submitButtonId'];
            $hover_t_color  = isset($blockAttrs['submitHoverColor']) ? $blockAttrs['submitHoverColor'] : '';
            $hover_bg_color = isset($blockAttrs['submitHoverBgColor']) ? $blockAttrs['submitHoverBgColor'] : '';
            $hover_sh_color = isset($blockAttrs['submitHoverShadow']) ? $blockAttrs['submitHoverShadow'] : '#ccc';
            $hover_sh_h     = isset($blockAttrs['submitHoverShadowH']) ? intval($blockAttrs['submitHoverShadowH']) : 1;
            $hover_sh_v     = isset($blockAttrs['submitHoverShadowV']) ? intval($blockAttrs['submitHoverShadowV']) : 1;
            $hover_sh_blur  = isset($blockAttrs['submitHoverShadowBlur']) ? intval($blockAttrs['submitHoverShadowBlur']) : 12;
            $hover_sh_sprd  = isset($blockAttrs['submitHoverShadowSpread']) ? intval($blockAttrs['submitHoverShadowSpread']) : 0;
            $hover_opacity  = isset($blockAttrs['submitHoverOpacity']) ? intval($blockAttrs['submitHoverOpacity'])/100 : 1;
            $transition_spd = isset($blockAttrs['submitHoverTranSpeed']) ? floatval($blockAttrs['submitHoverTranSpeed'])/1000 : 0.2;

            $style_html  = '.'. $block_class . ':hover{';
            $style_html .= 'color:'.$hover_t_color.' !important;';
            $style_html .= 'background-color:'.$hover_bg_color.' !important;';
            $style_html .= 'box-shadow:'.$hover_sh_h.'px '.$hover_sh_v.'px '.$hover_sh_blur.'px '.$hover_sh_sprd.'px '.$hover_sh_color.' !important;';
            $style_html .= 'opacity:'.$hover_opacity.';';
            $style_html .= 'transition:all '.$transition_spd.'s ease;';
            $style_html .= '}';

            return $style_html;
        }

        /**
         * Styles for Search Bar Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedSearchBarStyles($blockAttrs)
        {
            $block_class    = $blockAttrs['searchBtnId'];
            $hover_t_color  = isset($blockAttrs['searchBtnHoverColor']) ? $blockAttrs['searchBtnHoverColor'] : '';
            $hover_bg_color = isset($blockAttrs['searchBtnHoverBgColor']) ? $blockAttrs['searchBtnHoverBgColor'] : '';
            $hover_sh_color = isset($blockAttrs['searchBtnHoverShadow']) ? $blockAttrs['searchBtnHoverShadow'] : '#ccc';
            $hover_sh_h     = isset($blockAttrs['searchBtnHoverShadowH']) ? intval($blockAttrs['searchBtnHoverShadowH']) : 1;
            $hover_sh_v     = isset($blockAttrs['searchBtnHoverShadowV']) ? intval($blockAttrs['searchBtnHoverShadowV']) : 1;
            $hover_sh_blur  = isset($blockAttrs['searchBtnHoverShadowBlur']) ? intval($blockAttrs['searchBtnHoverShadowBlur']) : 12;
            $hover_sh_sprd  = isset($blockAttrs['searchBtnHoverShadowSpread']) ? intval($blockAttrs['searchBtnHoverShadowSpread']) : 0;
            $hover_opacity  = isset($blockAttrs['searchBtnHoverOpacity']) ? intval($blockAttrs['searchBtnHoverOpacity'])/100 : 1;
            $transition_spd = isset($blockAttrs['searchBtnHoverTranSpeed']) ? floatval($blockAttrs['searchBtnHoverTranSpeed'])/1000 : 0.2;

            $style_html  = '.'. $block_class . ':hover{';
            $style_html .= 'color:'.$hover_t_color.' !important;';
            $style_html .= 'background-color:'.$hover_bg_color.' !important;';
            $style_html .= 'box-shadow:'.$hover_sh_h.'px '.$hover_sh_v.'px '.$hover_sh_blur.'px '.$hover_sh_sprd.'px '.$hover_sh_color.' !important;';
            $style_html .= 'opacity:'.$hover_opacity.';';
            $style_html .= 'transition:all '.$transition_spd.'s ease;';
            $style_html .= '}';

            return $style_html;
        }

        /**
         * Styles for Adv. Image Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedImageStyles($blockAttrs)
        {
            if (array_key_exists('blockIDX', $blockAttrs)) {
                $block_class     = $blockAttrs['blockIDX'];
                $default_opacity = isset($blockAttrs['defaultOpacity']) ? $blockAttrs['defaultOpacity'] : 40;
                $hover_opacity   = isset($blockAttrs['overlayOpacity']) ? $blockAttrs['overlayOpacity'] : 20;

                $style_html  = '.' . $block_class . '.advgb-image-block .advgb-image-overlay{';
                $style_html .= 'opacity:' . ($default_opacity / 100) . ' !important;';
                $style_html .= '}';

                $style_html .= '.' . $block_class . '.advgb-image-block:hover .advgb-image-overlay{';
                $style_html .= 'opacity:' . ($hover_opacity / 100) . ' !important;';
                $style_html .= '}';
            }

            return $style_html;
        }

        /**
         * Styles for Adv. Testimonial Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedTestimonialStyles($blockAttrs)
        {
            if (array_key_exists('pid', $blockAttrs)) {
                $block_id   = $blockAttrs['pid'];
                $dots_color = isset($blockAttrs['sliderDotsColor']) ? $blockAttrs['sliderDotsColor'] : '#000';

                $style_html  = '#' . $block_id . ' .slick-dots li button:before{';
                $style_html .= 'color:' . $dots_color . ' !important;';
                $style_html .= '}';
            }

            return $style_html;
        }

        /**
         * Styles for Adv. Tabs Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedTabsStyles($blockAttrs)
        {
            $block_class    = isset($blockAttrs['pid']) ? $blockAttrs['pid'] : 'wp-block-advgb-adv-tabs';
            $active_tab_bg_color  = isset($blockAttrs['activeTabBgColor']) ? $blockAttrs['activeTabBgColor'] : '#5954d6';
            $active_tab_text_color  = isset($blockAttrs['activeTabTextColor']) ? $blockAttrs['activeTabTextColor'] : '#fff';

            $style_html  = '.'. $block_class . ' ul.advgb-tabs-panel li.advgb-tab.advgb-tab-active {';
            $style_html .= 'background-color:'.$active_tab_bg_color.' !important;';
            $style_html .= 'color:'.$active_tab_text_color.' !important;';
            $style_html .= '}';

            $style_html .= '#'. $block_class . ' .advgb-tab-body-header.header-active, .'. $block_class . ' .advgb-tab-body-header.header-active{';
            $style_html .= 'background-color:'.$active_tab_bg_color.' !important;';
            $style_html .= 'color:'.$active_tab_text_color.' !important;';
            $style_html .= '}';

            return $style_html;
        }

        /**
         * Styles for Adv. Icon Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedIconStyles($blockAttrs)
        {
            wp_enqueue_style('material_icon_font');
            $block_id = $blockAttrs['blockIDX'];
            $i = 0;
            $default_items = array();
            $item = array(
                'icon' => 'info',
                'iconType' => 'material',
                'size' => 120,
                'color' => '#111111',
                'style' => 'default',
                'bgColor' => '',
                'borderColor' => '#111',
                'borderSize' => 2,
                'borderRadius' => 0,
                'paddingTop' => 20,
                'paddingBottom' => 20,
                'paddingLeft' => 20,
                'paddingRight' => 20,
                'marginTop' => 0,
                'marginBottom' => 0,
                'marginLeft' => 0,
                'marginRight' => 0,
                'paddingUnit' => 'px',
                'marginUnit' => 'px',
                'link' => '',
                'linkTarget' => '_self',
                'title' => ''
            );
            while ($i < 10) {
                array_push($default_items, $item);
                $i++;
            }
            $items = !isset($blockAttrs['items']) ? $default_items : $blockAttrs['items'];
            $text_align = !isset($blockAttrs['tAlign']) ? 'center' : $blockAttrs['tAlign'];

            $style_html = '#' . $block_id . ' .advgb-icons, .' . $block_id . ' .advgb-icons {';
            $style_html .= 'text-align: ' . $text_align . ';';
            $style_html .= '}';

            foreach ($items as $k => $item) {
                $style_html .= '#' . $block_id . ' .advgb-item-' . $k . ' .advgb-icon, .' . $block_id . ' .advgb-item-' . $k . ' .advgb-icon {';
                $style_html .= 'display: flex;';
                $style_html .= 'align-items:center;';

                $style_html .= 'margin-top: ' . $item['marginTop'] . $item['marginUnit'] . ';';
                $style_html .= 'margin-bottom: ' . $item['marginBottom'] . $item['marginUnit'] . ';';
                $style_html .= 'margin-left: ' . $item['marginLeft'] . $item['marginUnit'] . ';';
                $style_html .= 'margin-right: ' . $item['marginRight'] . $item['marginUnit'] . ';';

                $style_html .= $item['style'] === 'default' ? 'padding-top: 0;' : 'padding-top: ' . $item['paddingTop'] . $item['paddingUnit'] . ';';
                $style_html .= $item['style'] === 'default' ? 'padding-bottom: 0;' : 'padding-bottom: ' . $item['paddingBottom'] . $item['paddingUnit'] . ';';
                $style_html .= $item['style'] === 'default' ? 'padding-left: 0;' : 'padding-left: ' . $item['paddingLeft'] . $item['paddingUnit'] . ';';
                $style_html .= $item['style'] === 'default' ? 'padding-right: 0;' : 'padding-right: ' . $item['paddingRight'] . $item['paddingUnit'] . ';';

                $style_html .= $item['style'] === 'default' ? 'border-width: 0;' : 'border-width: ' . $item['borderSize'] . 'px;';
                $style_html .= 'border-style: solid;';
                $style_html .= 'border-color: ' . $item['borderColor'] . ';';
                $style_html .= 'border-radius: ' . $item['borderRadius'] . '%;';

                $style_html .= isset($item['bgColor']) ? 'background-color: ' . $item['bgColor'] . ';' : 'background-color: transparent;';

                $style_html .= '}';

                $style_html .= '#' . $block_id . ' .advgb-item-' . $k . ' .advgb-icon > i, .' . $block_id . ' .advgb-item-' . $k . ' .advgb-icon > i{';
                $style_html .= 'font-size: ' . $item['size'] . 'px;';
                $style_html .= 'color: ' . $item['color'] . ';';
                $style_html .= '}';
            }

            return $style_html;
        }

        /**
         * Styles for Info Box Block
         *
         * @since    2.4.2
         * @param   $blockAttrs The block attributes
         * @return  string      Inline CSS
         */
        public function advgb_AdvancedInfoBoxStyles($blockAttrs)
        {
            wp_enqueue_style('material_icon_font');
            $block_id = $blockAttrs['blockIDX'];

            $container_bg = isset($blockAttrs['containerBackground']) ? $blockAttrs['containerBackground'] : '#f5f5f5';

            $container_padding_unit = isset($blockAttrs['containerPaddingUnit']) ? $blockAttrs['containerPaddingUnit'] : 'px';
            $container_padding = '';
            $container_padding .= isset($blockAttrs['containerPaddingTop']) ? $blockAttrs['containerPaddingTop'] . $container_padding_unit . ' ' : '20' . $container_padding_unit . ' ';
            $container_padding .= isset($blockAttrs['containerPaddingRight']) ? $blockAttrs['containerPaddingRight'] . $container_padding_unit . ' ' : '20' . $container_padding_unit . ' ';
            $container_padding .= isset($blockAttrs['containerPaddingBottom']) ? $blockAttrs['containerPaddingBottom'] . $container_padding_unit . ' ' : '20' . $container_padding_unit . ' ';
            $container_padding .= isset($blockAttrs['containerPaddingLeft']) ? $blockAttrs['containerPaddingLeft'] . $container_padding_unit : '20' . $container_padding_unit;

            $container_border = '';
            $container_border .= isset($blockAttrs['containerBorderWidth']) ? $blockAttrs['containerBorderWidth'] . 'px ' : '0px ';
            $container_border .= 'solid ';
            $container_border .= isset($blockAttrs['containerBorderBackground']) ? $blockAttrs['containerBorderBackground'] : '#e8e8e8 ';

            $container_border_radius = '';
            $container_border_radius .= isset($blockAttrs['containerBorderRadius']) ? $blockAttrs['containerBorderRadius'] : 0;

            $style_html  = '#' . $block_id . ', .' . $block_id . ' {';
            $style_html .= 'background-color: ' . $container_bg . ';';
            $style_html .= 'padding: ' . $container_padding . ';';
            $style_html .= 'border: ' . $container_border . ';';
            $style_html .= 'border-radius: ' . $container_border_radius . 'px;';
            $style_html .= '}'; //end container css

            $icon_background_color = isset($blockAttrs['iconBackground']) ? $blockAttrs['iconBackground'] : '#f5f5f5';

            $icon_padding = '';
            $icon_padding_unit = isset($blockAttrs['iconPaddingUnit']) ? $blockAttrs['iconPaddingUnit'] : 'px';
            $icon_padding .= isset($blockAttrs['iconPaddingTop']) ? $blockAttrs['iconPaddingTop'] . $icon_padding_unit . ' ' : '0 ';
            $icon_padding .= isset($blockAttrs['iconPaddingRight']) ? $blockAttrs['iconPaddingRight'] . $icon_padding_unit . ' ' : '0 ';
            $icon_padding .= isset($blockAttrs['iconPaddingBottom']) ? $blockAttrs['iconPaddingBottom'] . $icon_padding_unit . ' ' : '0 ';
            $icon_padding .= isset($blockAttrs['iconPaddingLeft']) ? $blockAttrs['iconPaddingLeft'] . $icon_padding_unit : '0';
            if ($icon_padding === '0 0 0 0') {
                $icon_padding = 0;
            }

            $icon_margin = '';
            $icon_margin_unit = isset($blockAttrs['iconMarginUnit']) ? $blockAttrs['iconMarginUnit'] : 'px';
            $icon_margin .= isset($blockAttrs['iconMarginTop']) ? $blockAttrs['iconMarginTop'] . $icon_margin_unit . ' ' : '0 ';
            $icon_margin .= isset($blockAttrs['iconMarginRight']) ? $blockAttrs['iconMarginRight'] . $icon_margin_unit . ' ' : '0 ';
            $icon_margin .= isset($blockAttrs['iconMarginBottom']) ? $blockAttrs['iconMarginBottom'] . $icon_margin_unit . ' ' : '0 ';
            $icon_margin .= isset($blockAttrs['iconMarginLeft']) ? $blockAttrs['iconMarginLeft'] . $icon_margin_unit : '0';
            if ($icon_margin === '0 0 0 0') {
                $icon_margin = 0;
            }

            $icon_border = '';
            $icon_border .= isset($blockAttrs['iconBorderWidth']) ? $blockAttrs['iconBorderWidth'] . 'px ' : '0px ';
            $icon_border .= 'solid ';
            $icon_border .= isset($blockAttrs['iconBorderBackground']) ? $blockAttrs['iconBorderBackground'] : '#e8e8e8 ';

            $icon_border_radius = '';
            $icon_border_radius .= isset($blockAttrs['iconBorderRadius']) ? $blockAttrs['iconBorderRadius'] : 0;

            $style_html .= '#' . $block_id . ' .advgb-infobox-icon-container, .' . $block_id . ' .advgb-infobox-icon-container {';
            $style_html .= 'background-color: ' . $icon_background_color . ';';
            $style_html .= 'padding: ' . $icon_padding . ';';
            $style_html .= 'margin: ' . $icon_margin . ';';
            $style_html .= 'border: ' . $icon_border . ';';
            $style_html .= 'border-radius: ' . $icon_border_radius . 'px;';
            $style_html .= '}';

            $icon_color = isset($blockAttrs['iconColor']) ? $blockAttrs['iconColor'] : '#333';
            $icon_size = isset($blockAttrs['iconSize']) ? $blockAttrs['iconSize'] : '70';
            $icon_size_unit = isset($blockAttrs['iconSizeUnit']) ? $blockAttrs['iconSizeUnit'] : 'px';
            $style_html .= '#' . $block_id . ' .advgb-infobox-icon-container i, .' . $block_id . ' .advgb-infobox-icon-container i {';
            $style_html .= 'color: ' . $icon_color . ';';
            $style_html .= 'font-size: ' . $icon_size . $icon_size_unit . ';';
            $style_html .= 'display: block;';
            $style_html .= '}'; //end icon style

            $title_color = isset($blockAttrs['titleColor']) ? $blockAttrs['titleColor'] : '#333';

            $title_padding = '';
            $title_padding_unit = isset($blockAttrs['titlePaddingUnit']) ? $blockAttrs['titlePaddingUnit'] : 'px';
            $title_padding .= isset($blockAttrs['titlePaddingTop']) ? $blockAttrs['titlePaddingTop'] . $title_padding_unit . ' ' : '0 ';
            $title_padding .= isset($blockAttrs['titlePaddingRight']) ? $blockAttrs['titlePaddingRight'] . $title_padding_unit . ' ' : '0 ';
            $title_padding .= isset($blockAttrs['titlePaddingBottom']) ? $blockAttrs['titlePaddingBottom'] . $title_padding_unit . ' ' : '0 ';
            $title_padding .= isset($blockAttrs['titlePaddingLeft']) ? $blockAttrs['titlePaddingLeft'] . $title_padding_unit : '0';
            if ($title_padding === '0 0 0 0') {
                $title_padding = 0;
            }

            $title_margin = '';
            $title_margin_unit = isset($blockAttrs['titleMarginUnit']) ? $blockAttrs['titleMarginUnit'] : 'px';
            $title_margin .= isset($blockAttrs['titleMarginTop']) ? $blockAttrs['titleMarginTop'] . $title_margin_unit . ' ' : '5' . $title_margin_unit . ' ';
            $title_margin .= isset($blockAttrs['titleMarginRight']) ? $blockAttrs['titleMarginRight'] . $title_margin_unit . ' ' : '0 ';
            $title_margin .= isset($blockAttrs['titleMarginBottom']) ? $blockAttrs['titleMarginBottom'] . $title_margin_unit . ' ' : '10' . $title_margin_unit . ' ';
            $title_margin .= isset($blockAttrs['titleMarginLeft']) ? $blockAttrs['titleMarginLeft'] . $title_margin_unit : '0';
            if ($title_margin === '0 0 0 0') {
                $title_margin = 0;
            }

            $title_size_unit = isset($blockAttrs['titleSizeUnit']) ? $blockAttrs['titleSizeUnit'] : 'px';
            $title_lh_unit = isset($blockAttrs['titleLineHeightUnit']) ? $blockAttrs['titleLineHeightUnit'] : 'px';
            $style_html .= '#' . $block_id . ' .advgb-infobox-textcontent .advgb-infobox-title, .' . $block_id . ' .advgb-infobox-textcontent .advgb-infobox-title {';
            $style_html .= 'color: ' . $title_color . ';';
            $style_html .= 'padding: ' . $title_padding . ';';
            $style_html .= 'margin: ' . $title_margin . ';';
            if (isset($blockAttrs['titleSize'])) {
                $style_html .= 'font-size: ' . $blockAttrs['titleSize'] . $title_size_unit . ';';
            }
            if (isset($blockAttrs['titleLineHeight'])) {
                $style_html .= 'line-height: ' . $blockAttrs['titleLineHeight'] . $title_lh_unit . ';';
            }
            $style_html .= 'white-space: pre-wrap;';
            $style_html .= '}'; //end title style

            $text_color = isset($blockAttrs['textColor']) ? $blockAttrs['textColor'] : '#333';

            $text_padding = '';
            $text_padding_unit = isset($blockAttrs['textPaddingUnit']) ? $blockAttrs['textPaddingUnit'] : 'px';
            $text_padding .= isset($blockAttrs['textPaddingTop']) ? $blockAttrs['textPaddingTop'] . $text_padding_unit . ' ' : '0 ';
            $text_padding .= isset($blockAttrs['textPaddingRight']) ? $blockAttrs['textPaddingRight'] . $text_padding_unit . ' ' : '0 ';
            $text_padding .= isset($blockAttrs['textPaddingBottom']) ? $blockAttrs['textPaddingBottom'] . $text_padding_unit . ' ' : '0 ';
            $text_padding .= isset($blockAttrs['textPaddingLeft']) ? $blockAttrs['textPaddingLeft'] . $text_padding_unit : '0';
            if ($text_padding === '0 0 0 0') {
                $text_padding = 0;
            }

            $text_margin = '';
            $text_margin_unit = isset($blockAttrs['textMarginUnit']) ? $blockAttrs['textMarginUnit'] : 'px';
            $text_margin .= isset($blockAttrs['textMarginTop']) ? $blockAttrs['textMarginTop'] . $text_margin_unit . ' ' : '0 ';
            $text_margin .= isset($blockAttrs['textMarginRight']) ? $blockAttrs['textMarginRight'] . $text_margin_unit . ' ' : '0 ';
            $text_margin .= isset($blockAttrs['textMarginBottom']) ? $blockAttrs['textMarginBottom'] . $text_margin_unit . ' ' : '0 ';
            $text_margin .= isset($blockAttrs['textMarginLeft']) ? $blockAttrs['textMarginLeft'] . $text_margin_unit : '0';
            if ($text_margin === '0 0 0 0') {
                $text_margin = 0;
            }

            $text_size_unit = isset($blockAttrs['textSizeUnit']) ? $blockAttrs['textSizeUnit'] : 'px';
            $text_lh_unit = isset($blockAttrs['textLineHeightUnit']) ? $blockAttrs['textLineHeightUnit'] : 'px';
            $style_html .= '#' . $block_id . ' .advgb-infobox-textcontent .advgb-infobox-text, .' . $block_id . ' .advgb-infobox-textcontent .advgb-infobox-text {';
            $style_html .= 'color: ' . $text_color . ';';
            $style_html .= 'padding: ' . $text_padding . ';';
            $style_html .= 'margin: ' . $text_margin . ';';
            if (isset($blockAttrs['textSize'])) {
                $style_html .= 'font-size: ' . $blockAttrs['textSize'] . $text_size_unit . ';';
            }
            if (isset($blockAttrs['textLineHeight'])) {
                $style_html .= 'line-height: ' . $blockAttrs['textLineHeight'] . $text_lh_unit . ';';
            }
            $style_html .= 'white-space: pre-wrap;';
            $style_html .= '}'; //end text style

            return $style_html;
        }

        /**
         * Styles for Count Up Block
         *
         * @since    2.4.2
         * @return  string      empty
         */
        public function advgb_AdvancedCountUpStyles()
        {
            wp_enqueue_script('advgb_blocks_frontend_scripts');

            $style_html = '';

            return $style_html;
        }
    }
}
