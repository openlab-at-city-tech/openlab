<?php
class M_Third_Party_Compat extends C_Base_Module
{
    protected $wpseo_images = array();

    function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
    {
        parent::define(
            'photocrati-third_party_compat',
            'Third Party Compatibility',
            "Adds Third party compatibility hacks, adjustments, and modifications",
            '3.1.11.1',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );

        // the following constants were renamed for 2.0.41; keep them declared for compatibility sake until
        // other parties can update themselves.
        $changed_constants = array(
            'NEXTGEN_ADD_GALLERY_SLUG'                     => 'NGG_ADD_GALLERY_SLUG',
            'NEXTGEN_BASIC_SINGLEPIC_MODULE_NAME'          => 'NGG_BASIC_SINGLEPIC',
            'NEXTGEN_BASIC_TAG_CLOUD_MODULE_NAME'          => 'NGG_BASIC_TAGCLOUD',
            'NEXTGEN_DISPLAY_PRIORITY_BASE'                => 'NGG_DISPLAY_PRIORITY_BASE',
            'NEXTGEN_DISPLAY_PRIORITY_STEP'                => 'NGG_DISPLAY_PRIORITY_STEP',
            'NEXTGEN_DISPLAY_SETTINGS_SLUG'                => 'NGG_DISPLAY_SETTINGS_SLUG',
            'NEXTGEN_FS_ACCESS_SLUG'                       => 'NGG_FS_ACCESS_SLUG',
            'NEXTGEN_GALLERY_ATTACH_TO_POST_SLUG'          => 'NGG_ATTACH_TO_POST_SLUG',
            'NEXTGEN_GALLERY_BASIC_SLIDESHOW'              => 'NGG_BASIC_SLIDESHOW',
            'NEXTGEN_GALLERY_BASIC_THUMBNAILS'             => 'NGG_BASIC_THUMBNAILS',
            'NEXTGEN_GALLERY_CHANGE_OPTIONS_CAP'           => 'NGG_CHANGE_OPTIONS_CAP',
            'NEXTGEN_GALLERY_I18N_DOMAIN'                  => 'NGG_I18N_DOMAIN',
            'NEXTGEN_GALLERY_IMPORT_ROOT'                  => 'NGG_IMPORT_ROOT',
            'NEXTGEN_GALLERY_MODULE_DIR'                   => 'NGG_MODULE_DIR',
            'NEXTGEN_GALLERY_MODULE_URL'                   => 'NGG_MODULE_URL',
            'NEXTGEN_GALLERY_NEXTGEN_BASIC_COMPACT_ALBUM'  => 'NGG_BASIC_COMPACT_ALBUM',
            'NEXTGEN_GALLERY_NEXTGEN_BASIC_EXTENDED_ALBUM' => 'NGG_BASIC_EXTENDED_ALBUM',
            'NEXTGEN_GALLERY_NEXTGEN_BASIC_IMAGEBROWSER'   => 'NGG_BASIC_IMAGEBROWSER',
            'NEXTGEN_GALLERY_NGGLEGACY_MOD_DIR'            => 'NGG_LEGACY_MOD_DIR',
            'NEXTGEN_GALLERY_NGGLEGACY_MOD_URL'            => 'NGG_LEGACY_MOD_URL',
            'NEXTGEN_GALLERY_PLUGIN'                       => 'NGG_PLUGIN',
            'NEXTGEN_GALLERY_PLUGIN_BASENAME'              => 'NGG_PLUGIN_BASENAME',
            'NEXTGEN_GALLERY_PLUGIN_DIR'                   => 'NGG_PLUGIN_DIR',
            'NEXTGEN_GALLERY_PLUGIN_STARTED_AT'            => 'NGG_PLUGIN_STARTED_AT',
            'NEXTGEN_GALLERY_PLUGIN_URL'                   => 'NGG_PLUGIN_URL',
            'NEXTGEN_GALLERY_PLUGIN_VERSION'               => 'NGG_PLUGIN_VERSION',
            'NEXTGEN_GALLERY_PRODUCT_DIR'                  => 'NGG_PRODUCT_DIR',
            'NEXTGEN_GALLERY_PRODUCT_URL'                  => 'NGG_PRODUCT_URL',
            'NEXTGEN_GALLERY_PROTECT_IMAGE_MOD_STATIC_URL' => 'NGG_PROTUCT_IMAGE_MOD_STATIC_URL',
            'NEXTGEN_GALLERY_PROTECT_IMAGE_MOD_URL'        => 'NGG_PROTECT_IMAGE_MOD_URL',
            'NEXTGEN_GALLERY_TESTS_DIR'                    => 'NGG_TESTS_DIR',
            'NEXTGEN_LIGHTBOX_ADVANCED_OPTIONS_SLUG'       => 'NGG_LIGHTBOX_ADVANCED_OPTIONS_SLUG',
            'NEXTGEN_LIGHTBOX_OPTIONS_SLUG'                => 'NGG_LIGHTBOX_OPTIONS_SLUG',
            'NEXTGEN_OTHER_OPTIONS_SLUG'                   => 'NGG_OTHER_OPTIONS_SLUG'
        );
        foreach ($changed_constants as $old => $new) {
            if (defined($new) && !defined($old)) {
                define($old, constant($new));
            }
        }

        // Resolve problems with zlib compression: https://core.trac.wordpress.org/ticket/18525
        if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION == 4) {
            @ini_set('zlib.output_compression', 'Off');
        }

        // Detect 'Adminer' and whether the user is viewing its loader.php
        if (class_exists('AdminerForWP') && function_exists('adminer_object'))
        {
            if (!defined('NGG_DISABLE_RESOURCE_MANAGER'))
                define('NGG_DISABLE_RESOURCE_MANAGER', TRUE);
        }

        // Cornerstone's page builder requires a 'clean slate' of css/js that our resource manager interefers with
        if (class_exists('Cornerstone'))
        {
            if (!defined('NGG_DISABLE_FILTER_THE_CONTENT')) define('NGG_DISABLE_FILTER_THE_CONTENT', TRUE);
            if (!defined('NGG_DISABLE_RESOURCE_MANAGER'))   define('NGG_DISABLE_RESOURCE_MANAGER', TRUE);
        }

        // Genesis Tabs creates a new query / do_shortcode loop which requires these be set
        if (class_exists('Genesis_Tabs'))
        {
            if (!defined('NGG_DISABLE_FILTER_THE_CONTENT')) define('NGG_DISABLE_FILTER_THE_CONTENT', TRUE);
            if (!defined('NGG_DISABLE_RESOURCE_MANAGER'))   define('NGG_DISABLE_RESOURCE_MANAGER', TRUE);
        }

        // Elementor's graphical builder is broken by our resource manager
        if (defined('ELEMENTOR_VERSION'))
        {
            if (!defined('NGG_DISABLE_RESOURCE_MANAGER')) define('NGG_DISABLE_RESOURCE_MANAGER', TRUE);
        }
    }

    function _register_adapters()
    {
        $this->get_registry()->add_adapter(
            'I_Display_Type_Controller', 'A_Non_Cachable_Pro_Film_Controller', 'photocrati-nextgen_pro_film'
        );
    }

    function _register_hooks()
    {
        add_action('init', array($this, 'divi'),       10);
        add_action('init', array($this, 'colorbox'),   PHP_INT_MAX);
        add_action('init', array($this, 'flattr'),     PHP_INT_MAX);
        add_action('wp',   array($this, 'bjlazyload'), PHP_INT_MAX);

        add_action('admin_init', array($this, 'excellent_themes_admin'), -10);

        add_action('plugins_loaded',     array($this, 'wpml'), PHP_INT_MAX);
        add_action('plugins_loaded',     array($this, 'wpml_translation_management'), PHP_INT_MAX);
        add_filter('wpml_is_redirected', array($this, 'wpml_is_redirected'), -10, 3);

        add_filter('headway_gzip', array(&$this, 'headway_gzip'), (PHP_INT_MAX - 1));
        add_filter('ckeditor_external_plugins', array(&$this, 'ckeditor_plugins'), 11);
        add_filter('bp_do_redirect_canonical', array(&$this, 'fix_buddypress_routing'));
        add_filter('the_content', array($this, 'check_weaverii'), -(PHP_INT_MAX-2));
        add_action('wp', array($this, 'check_for_jquery_lightbox'));
        add_filter('get_the_excerpt', array($this, 'disable_galleries_in_excerpts'), 1);
        add_filter('get_the_excerpt', array($this, 'enable_galleries_in_excerpts'), PHP_INT_MAX-1);
	    add_action('debug_bar_enqueue_scripts', array($this, 'no_debug_bar'));
        add_filter('ngg_non_minified_modules', array($this, 'dont_minify_nextgen_pro_cssjs'));
        add_filter('ngg_atp_show_display_type', array($this, 'atp_check_pro_albums'), 10, 2);
        add_filter('run_ngg_resource_manager', array($this, 'run_ngg_resource_manager'));
        add_filter('wpseo_sitemap_urlimages', array($this, 'add_wpseo_xml_sitemap_images'), 10, 2);
        add_filter('ngg_pre_delete_unused_term_id', array($this, 'dont_auto_purge_wpml_terms'));

        if ($this->is_ngg_page()) add_action('admin_enqueue_scripts', array(&$this, 'dequeue_spider_calendar_resources'));

        // WPML fix
        if (class_exists('SitePress')) {
            M_WordPress_Routing::$_use_canonical_redirect = FALSE;
            M_WordPress_Routing::$_use_old_slugs = FALSE;
            add_action('template_redirect', array(&$this, 'fix_wpml_canonical_redirect'), 1);
        }

        // TODO: Only needed for NGG Pro 1.0.10 and lower
        add_action('the_post', array(&$this, 'add_ngg_pro_page_parameter'));
    }

    public function divi()
    {
        // Divi / Divi Booster loads its own Iris JS under the 'iris' ID, but because NextGen has already
        // registered the default /wp-admin/js/iris.js we effectively break their admin color selector
        if (function_exists('et_divi_load_unminified_scripts') && !empty($_GET['et_fb']))
            wp_deregister_script('iris');
    }

    function is_ngg_page()
    {
        return (is_admin() && isset($_REQUEST['page']) && strpos($_REQUEST['page'], 'ngg') !== FALSE);
    }

    function dequeue_spider_calendar_resources()
    {
        remove_filter('admin_head', 'spide_ShowTinyMCE');
    }

    /**
     * Filter support for WordPress SEO
     *
     * @param array $images Provided by WPSEO Filter
     * @param int $post_id ID Provided by WPSEO Filter
     * @return array $image List of a displayed galleries entities
     */
    function add_wpseo_xml_sitemap_images($images, $post_id)
    {
        $this->wpseo_images = $images;

        $post = get_post($post_id);

        // Assign our own shortcode handler; ngglegacy and ATP do this same routine for their own
        // legacy and preview image placeholders.
        remove_all_shortcodes();
        C_NextGen_Shortcode_Manager::add('ngg',        array($this, 'wpseo_shortcode_handler'));
        C_NextGen_Shortcode_Manager::add('ngg_images', array($this, 'wpseo_shortcode_handler'));
        do_shortcode($post->post_content);

        return $this->wpseo_images;
    }

    /**
     * Processes ngg_images shortcode when WordPress SEO is building sitemaps. Adds images belonging to a
     * displayed gallery to $this->wpseo_images for the assigned filter method to return.
     *
     * @param array $params Array of shortcode parameters
     * @param null $inner_content
     */
    function wpseo_shortcode_handler($params, $inner_content = NULL)
    {
        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        $displayed_gallery = $renderer->params_to_displayed_gallery($params);

        if ($displayed_gallery)
        {
            $gallery_storage = C_Gallery_Storage::get_instance();
            $settings		 = C_NextGen_Settings::get_instance();
            $source          = $displayed_gallery->get_source();
            if (in_array('image', $source->returns))
            {
                foreach ($displayed_gallery->get_entities() as $image) {
                    $named_image_size = $settings->imgAutoResize ? 'full' : 'thumb';
                    $sitemap_image = array(
                        'src'	=>	$gallery_storage->get_image_url($image, $named_image_size),
                        'alt'	=>	$image->alttext,
                        'title'	=>	$image->description ? $image->description: $image->alttext
                    );
                    $this->wpseo_images[] = $sitemap_image;
                }
            }
        }
    }

    /**
     * Some other plugins output content and die(); this causes problems with our resource manager which uses output buffering
     *
     * @param bool $valid_request
     * @return bool
     */
    function run_ngg_resource_manager($valid_request = TRUE)
    {
        // WP-Post-To-PDF-Enhanced
        if (class_exists('wpptopdfenh') && !empty($_GET['format']))
            $valid_request = FALSE;

        // WP-Photo-Seller download
        if (class_exists('WPS') && isset($_REQUEST['wps_file_dl']) && $_REQUEST['wps_file_dl'] == '1')
            $valid_request = FALSE;

        // Multiverso Advanced File Sharing download
        if (function_exists('mv_install') && isset($_GET['upf']) && isset($_GET['id']))
            $valid_request = FALSE;

        // WooCommerce downloads
        if (class_exists('WC_Download_Handler') && isset($_GET['download_file']) && isset($_GET['order']) && isset($_GET['email']))
            $valid_request = FALSE;

        // WP-E-Commerce
        if (isset($_GET['wpsc_download_id']) || (function_exists('wpsc_download_file') && isset($_GET['downloadid'])))
            $valid_request = FALSE;

        // Easy Digital Downloads
        if (function_exists('edd_process_download') && (isset($_GET['download_id']) || isset($_GET['download'])))
            $valid_request = FALSE;

        return $valid_request;
    }

    /**
     * This style causes problems with Excellent Themes admin settings
     */
    function excellent_themes_admin()
    {
        if (is_admin() && (!empty($_GET['page']) && strpos($_GET['page'], 'et_') == 0))
            wp_deregister_style('ngg-jquery-ui');
    }

    function atp_check_pro_albums($available, $display_type)
    {
        if (!defined('NGG_PRO_ALBUMS'))
            return $available;

        if (in_array($display_type->name, array(NGG_PRO_LIST_ALBUM, NGG_PRO_GRID_ALBUM))
        &&  $this->get_registry()->is_module_loaded(NGG_PRO_ALBUMS))
            $available = TRUE;

        return $available;
    }

    function no_debug_bar()
	{
		if (M_Attach_To_Post::is_atp_url()) {
			wp_dequeue_script('debug-bar-console');
		}
	}

    // A lot of routing issues start occuring with WordPress SEO when the routing system is
    // initialized by the excerpt, and then again from the post content.
    function disable_galleries_in_excerpts($excerpt)
    {
        if (class_exists('WPSEO_OpenGraph')) {
            M_Attach_To_Post::$substitute_placeholders = FALSE;
        }

        return $excerpt;
    }

    function enable_galleries_in_excerpts($excerpt)
    {
        if (class_exists('WPSEO_OpenGraph')) {
            M_Attach_To_Post::$substitute_placeholders = TRUE;
        }

        return $excerpt;
    }

    function fix_buddypress_routing()
    {
        M_WordPress_Routing::$_use_canonical_redirect = FALSE;

        return FALSE;
    }

    function fix_wpml_canonical_redirect()
    {
        M_WordPress_Routing::$_use_canonical_redirect = FALSE;
        M_WordPress_Routing::$_use_old_slugs = FALSE;
    }

    /**
     * NGG automatically purges unused terms when managing a gallery, but this also ensnares WPML translations
     * @param $term_id
     * @return bool
     */
    public function dont_auto_purge_wpml_terms($term_id)
    {
        $args = array('element_id' => $term_id,
                      'element_type' => 'ngg_tag');
        $term_language_code = apply_filters('wpml_element_language_code', null, $args);

        if (!empty($term_language_code))
            return FALSE;
        else
            return $term_id;
    }

    /**
     * Prevent WPML's parse_query() from conflicting with NGG's pagination & router module controlled endpoints
     *
     * @param string $redirect What WPML is send to wp_safe_redirect()
     * @param int $post_id
     * @param WP_Query $q
     * @return bool|string FALSE prevents a redirect from occurring
     */
    public function wpml_is_redirected($redirect, $post_id, $q)
    {
        $router = C_Router::get_instance();
        if (!$router->serve_request() && $router->has_parameter_segments())
            return false;
        else
            return $redirect;
    }

    /**
     * CKEditor features a custom NextGEN shortcode generator that unfortunately relies on parts of the NextGEN
     * 1.9x API that has been deprecated in NextGEN 2.0
     *
     * @param $plugins
     * @return mixed
     */
    function ckeditor_plugins($plugins)
    {
        if (!class_exists('add_ckeditor_button'))
            return $plugins;

        if (!empty($plugins['nextgen']))
            unset($plugins['nextgen']);

        return $plugins;
    }

    function check_for_jquery_lightbox()
    {
        // Fix for jQuery Lightbox: http://wordpress.org/plugins/wp-jquery-lightbox/
        // jQuery Lightbox tries to modify the content of a post, but it does so before we modify
        // the content, and therefore it's modifications have no effect on our galleries
        if (function_exists('jqlb_autoexpand_rel_wlightbox')) {
            $settings = C_NextGen_Settings::get_instance();

            // First, we make it appear that NGG has no lightbox effect enabled. That way
            // we don't any lightbox resources
            unset($settings->thumbEffect);

            // We would normally just let the third-party plugin do it's thing, but it's regex doesn't
            // seem to work on our <a> tags (perhaps because they span multiple of lines or have data attributes)
            // So instead, we just do what the third-party plugin wants - add the rel attribute
            $settings->thumbCode="rel='lightbox[%POST_ID%]'";
        }
    }

    /**
     * Weaver II's 'weaver_show_posts' shortcode creates a new wp-query, causing a second round of 'the_content'
     * filters to apply. This checks for WeaverII and enables all NextGEN shortcodes that would otherwise be left
     * disabled by our shortcode manager. See https://core.trac.wordpress.org/ticket/17817 for more.
     *
     * @param string $content
     * @return string $content
     */
    function check_weaverii($content)
    {
        if (function_exists('weaverii_show_posts_shortcode'))
            C_NextGen_Shortcode_Manager::get_instance()->activate_all();

        return $content;
    }

    /**
     * WPML assigns an action to 'init' that *may* enqueue some admin-side JS. This JS relies on some inline JS
     * to be injected that isn't present in ATP so for ATP requests ONLY we disable their action that enqueues
     * their JS files.
     */
    function wpml()
    {
        if (!class_exists('SitePress'))
            return;

        if (!M_Attach_To_Post::is_atp_url())
            return;

        global $wp_filter;

        if (empty($wp_filter['init'][2]) && empty($wp_filter['after_setup_theme'][1]))
            return;

        foreach ($wp_filter['init'][2] as $id => $filter) {
            if (!strpos($id, 'js_load'))
                continue;

            $object = $filter['function'][0];

            if (is_object($object) && get_class($object) != 'SitePress')
                continue;

            remove_action('init', array($object, 'js_load'), 2);
        }

        foreach ($wp_filter['after_setup_theme'][1] as $id => $filter) {
            if ($id !== 'wpml_installer_instance_delegator')
                continue;

            remove_action('after_setup_theme', 'wpml_installer_instance_delegator', 1);
        }
    }

    /**
     * WPML Translation Management has a similar problem to plain ol' WPML
     */
    function wpml_translation_management()
    {
        if (!class_exists('WPML_Translation_Management'))
            return;

        if (!M_Attach_To_Post::is_atp_url())
            return;

        global $wp_filter;

        if (empty($wp_filter['init'][10]))
            return;

        foreach ($wp_filter['init'][10] as $id => $filter) {
            if (!strpos($id, 'init'))
                continue;

            $object = $filter['function'][0];

            if (is_object($object) && get_class($object) != 'WPML_Translation_Management')
                continue;

            remove_action('init', array($object, 'init'), 10);
        }
    }

    /**
     * NGG Pro 1.0.10 relies on the 'page' parameter for pagination, but that conflicts with
     * WordPress Post Pagination (<!-- nextpage -->). This was fixed in 1.0.11, so this code is
     * for backwards compatibility
     * TODO: This can be removed in a later release
     */
    function add_ngg_pro_page_parameter()
    {
        global $post;

        if ($post AND !is_array($post->content) AND (strpos($post->content, "<!--nextpage-->") === FALSE) AND (strpos($_SERVER['REQUEST_URI'], '/page/') !== FALSE)) {
            if (preg_match("#/page/(\\d+)#", $_SERVER['REQUEST_URI'], $match)) {
                $_REQUEST['page'] = $match[1];
            }
        }
    }

    /**
     * Headway themes offer gzip compression, but it causes problems with NextGEN output. Disable that feature while
     * NextGEN is active.
     *
     * @param $option
     * @return bool
     */
    function headway_gzip($option)
    {
        if (!class_exists('HeadwayOption'))
            return $option;

        return FALSE;
    }

    /**
     * Colorbox fires a filter (pri=100) to add class attributes to images via a the_content filter. We fire our
     * shortcodes at PHP_INT_MAX-1 to avoid encoding issues with some themes. Here we move the Colorbox filters
     * priority to PHP_INT_MAX so that they run after our shortcode text has been replaced with rendered galleries.
     */
    function colorbox()
    {
        if (!class_exists('JQueryColorboxFrontend'))
            return;

        global $wp_filter;

        if (empty($wp_filter['the_content'][100]))
            return;

        foreach ($wp_filter['the_content'][100] as $id => $filter) {
            if (!strpos($id, 'addColorboxGroupIdToImages'))
                continue;

            $object = $filter['function'][0];

            if (is_object($object) && get_class($object) != 'JQueryColorboxFrontend')
                continue;

            remove_filter('the_content', array($object, 'addColorboxGroupIdToImages'), 100);
            remove_filter('the_excerpt', array($object, 'addColorboxGroupIdToImages'), 100);
            add_filter('the_content', array($object, 'addColorboxGroupIdToImages'), PHP_INT_MAX);
            add_filter('the_excerpt', array($object, 'addColorboxGroupIdToImages'), PHP_INT_MAX);
            break;
        }
    }

    /**
     * Flattr fires a filter (pri=32767) on "the_content" that recurses. This causes problems,
     * see https://core.trac.wordpress.org/ticket/17817 for more information. Moving their filter to PHP_INT_MAX
     * is enough for us though
     */
    function flattr()
    {
        if (!class_exists('Flattr'))
            return;

        global $wp_filter;

        $level = 32767;

        if (empty($wp_filter['the_content'][$level]))
            return;

        foreach ($wp_filter['the_content'][$level] as $id => $filter) {
            if (!strpos($id, 'injectIntoTheContent'))
                continue;

            $object = $filter['function'][0];

            if (is_object($object) && get_class($object) != 'Flattr')
                continue;

            remove_filter('the_content', array($object, 'injectIntoTheContent'), $level);
            add_filter('the_content', array($object, 'injectIntoTheContent'), PHP_INT_MAX);
            break;
        }
    }

    /**
     * For the same reasons as Colorbox we move BJ-Lazy-load's filter() method to a later priority so it can access
     * our rendered galleries.
     */
    function bjlazyload()
    {
        if (!class_exists('BJLL'))
            return;

        global $wp_filter;

        if (empty($wp_filter['the_content'][200]))
            return;

        foreach ($wp_filter['the_content'][200] as $id => $filter) {
            if (!strpos($id, 'filter'))
                continue;

            $object = $filter['function'][0];

            if (is_object($object) && get_class($object) != 'BJLL')
                continue;

            remove_filter('the_content', array($object, 'filter'), 200);
            add_filter('the_content', array($object, 'filter'), PHP_INT_MAX);
            break;
        }

        add_filter('the_content', array($this, 'bjlazyload_filter'), PHP_INT_MAX-1);
    }

    /**
     * BJ-Lazy-load's regex is lazy and doesn't handle multiline search or instances where <img is immediately followed
     * by a newline. The following regex replaces newlines and strips unnecessary space. We fire this filter
     * before BJ-Lazy-Load's to make our galleries compatible with its expectations.
     *
     * @param string $content
     * @return string
     */
    function bjlazyload_filter($content)
    {
        return trim(preg_replace("/\\s\\s+/", " ", $content));
    }

    /**
     * NextGen 2.0.67.20 introduced CSS/JS minification; do not apply this to NextGen Pro yet
     *
     * @param $modules_to_not_minify
     * @return array
     */
    function dont_minify_nextgen_pro_cssjs($modules_to_not_minify)
    {

	    // TODO: once Pro 2.1.30 is widely circulated, we don't need to use
	    // the installer. We can use the component registry to fetch the product
	    // and call the product's get_modules_to_load() function
	    $installer = new C_NextGen_Product_Installer;

        if (defined('NGG_PRO_PLUGIN_VERSION') && class_exists('P_Photocrati_NextGen_Pro'))
	        $modules_to_not_minify += $installer->get_modules_to_load_for('photocrati-nextgen-pro');
        else if (defined('NGG_PLUS_PLUGIN_VERSION') && class_exists('P_Photocrati_NextGen_Plus'))
	        $modules_to_not_minify += $installer->get_modules_to_load_for('photocrati-nextgen-plus');

        return $modules_to_not_minify;
    }

    function get_type_list()
    {
        return array(
            'A_Non_Cachable_Pro_Film_Controller'    => 'adapter.non_cachable_pro_film_controller.php'
        );
    }
}

new M_Third_Party_Compat();
