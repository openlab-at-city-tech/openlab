<?php

define('NGG_DISPLAY_SETTINGS_SLUG', 'ngg_display_settings');
define('NGG_DISPLAY_PRIORITY_BASE', 10000);
define('NGG_DISPLAY_PRIORITY_STEP', 2000);
if (!defined('NGG_RENDERING_CACHE_TTL')) define('NGG_RENDERING_CACHE_TTL', PHOTOCRATI_CACHE_TTL);
if (!defined('NGG_DISPLAYED_GALLERY_CACHE_TTL')) define('NGG_DISPLAYED_GALLERY_CACHE_TTL', PHOTOCRATI_CACHE_TTL);
if (!defined('NGG_RENDERING_CACHE_ENABLED')) define('NGG_RENDERING_CACHE_ENABLED', PHOTOCRATI_CACHE);
if (!defined('NGG_SHOW_DISPLAYED_GALLERY_ERRORS')) define('NGG_SHOW_DISPLAYED_GALLERY_ERRORS', NGG_DEBUG);

class M_Gallery_Display extends C_Base_Module
{
    public static $enqueued_displayed_gallery_ids = [];

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
			'photocrati-nextgen_gallery_display',
			'Gallery Display',
			'Provides the ability to display gallery of images',
			'3.3.21',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
		);

		C_Photocrati_Installer::add_handler($this->module_id, 'C_Display_Type_Installer');
	}

	/**
	 * Register utilities required for this module
	 */
	function _register_utilities()
	{
        // This utility provides a controller to render the settings form for a display type, or render the front-end of a display type
        $this->get_registry()->add_utility('I_Display_Type_Controller', 'C_Display_Type_Controller');

        // This utility provides the capabilities of rendering a display type
        $this->get_registry()->add_utility('I_Displayed_Gallery_Renderer', 'C_Displayed_Gallery_Renderer');

		// This utility provides a datamapper for Display Types
		$this->get_registry()->add_utility('I_Display_Type_Mapper', 'C_Display_Type_Mapper');

		// This utility provides a datamapper for Displayed Galleries. A displayed gallery is the association between
        // some entities (images or galleries) and a display type
		$this->get_registry()->add_utility('I_Displayed_Gallery_Mapper', 'C_Displayed_Gallery_Mapper') ;
	}

	/**
	 * Registers adapters required for this module
	 */
	function _register_adapters()
	{
		// Provides factory methods for creating display type and displayed gallery instances
		$this->get_registry()->add_adapter(
			'I_Component_Factory', 'A_Gallery_Display_Factory'
		);

        if (is_admin())
        {
            $this->get_registry()->add_adapter('I_Page_Manager', 'A_Display_Settings_Page');
            $this->get_registry()->add_adapter('I_NextGen_Admin_Page', 'A_Display_Settings_Controller', NGG_DISPLAY_SETTINGS_SLUG);
        }

        $this->get_registry()->add_adapter('I_MVC_View', 'A_Gallery_Display_View');
        $this->get_registry()->add_adapter('I_MVC_View', 'A_Displayed_Gallery_Trigger_Element');
        $this->get_registry()->add_adapter('I_Display_Type_Controller', 'A_Displayed_Gallery_Trigger_Resources');
	}

	/**
	 * Registers hooks for the WordPress framework
	 */
	function _register_hooks()
	{
        C_NextGen_Shortcode_Manager::add('ngg', [$this, 'display_images']);
        C_NextGen_Shortcode_Manager::add('ngg_images', [$this, 'display_images']);
        add_filter('the_content', array($this, '_render_related_images'));

        add_action('init', array(&$this, 'register_resources'), 12);
        add_action('admin_bar_menu', array(&$this, 'add_admin_bar_menu'), 100);

        // Add hook to delete displayed galleries when removed from a post
        add_action('pre_post_update', array(&$this, 'locate_stale_displayed_galleries'));
        add_action('before_delete_post', array(&$this, 'locate_stale_displayed_galleries'));
        add_action('post_updated',	array(&$this, 'cleanup_displayed_galleries'));
        add_action('after_delete_post', array(&$this, 'cleanup_displayed_galleries'));

        add_action('wp_print_styles', array($this, 'fix_nextgen_custom_css_order'), PHP_INT_MAX-1);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_resources']);
	}

	function enqueue_frontend_resources()
    {
        if ((defined('NGG_SKIP_LOAD_SCRIPTS') && NGG_SKIP_LOAD_SCRIPTS) || $this->is_rest_request())
            return;

        // Find our content and process it
        global $wp_query;

        // It's possible for the posts attribute to be empty or unset
        if (!isset($wp_query->posts) || !is_array($wp_query->posts))
            return;

        $posts = $wp_query->posts;
        foreach ($posts as $post) {
            if (empty($post->post_content))
                continue;

            self::enqueue_frontent_resources_for_content($post->post_content);
        }
    }

    /**
     * Most content will come from the WP query / global $posts but it's also sometimes necessary to enqueue resources
     * based on the results of an output filter
     * @param string $content
     */
    public static function enqueue_frontent_resources_for_content($content = '')
    {
        $manager = C_NextGen_Shortcode_Manager::get_instance();
        $pattern = $manager->get_shortcode_regex();
        $ngg_shortcodes = $manager->get_shortcodes();
        $ngg_shortcodes_keys = array_keys($ngg_shortcodes);

        // Determine which shortcodes to look for; 'ngg' is the default but there are legacy aliases
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $shortcode) {
            $this_shortcode_name = $shortcode[2];
            if (!in_array($this_shortcode_name, $ngg_shortcodes_keys))
                continue;

            $params = shortcode_parse_atts(trim($shortcode[0], '[]'));
            if (in_array($params[0], $ngg_shortcodes_keys)) // Don't pass 0 => 'ngg' as a parameter, it's just part of the shortcode itself
                unset($params[0]);

            // And do the enqueueing process
            $renderer = C_Displayed_Gallery_Renderer::get_instance();

            // This is necessary for legacy shortcode compatibility
            if (is_callable($ngg_shortcodes[$this_shortcode_name]['transformer']))
                $params = call_user_func($ngg_shortcodes[$this_shortcode_name]['transformer'], $params);

            $displayed_gallery = $renderer->params_to_displayed_gallery($params);

            if (did_action('wp_enqueue_scripts') == 1
                && !C_Photocrati_Resource_Manager::addons_version_check()
                && in_array($displayed_gallery->display_type, ['photocrati-nextgen_pro_horizontal_filmstrip', 'photocrati-nextgen_pro_slideshow']))
                continue;

            $controller = C_Display_Type_Controller::get_instance($displayed_gallery->display_type);

            if (!$displayed_gallery || empty($params))
                continue;

            self::enqueue_frontend_resources_for_displayed_gallery($displayed_gallery, $controller);
        }
    }

    /**
     * @param C_Displayed_Gallery $displayed_gallery
     * @param C_Display_Type_Controller $controller
     */
    public static function enqueue_frontend_resources_for_alternate_displayed_gallery($displayed_gallery, $controller)
    {
        // Allow basic thumbnails "use imagebrowser effect" feature to seamlessly change between display types as well
        // as for album display types to show galleries
        $alternate_displayed_gallery = $controller->get_alternate_displayed_gallery($displayed_gallery);
        if ($alternate_displayed_gallery === $displayed_gallery)
            return;

        $alternate_controller = C_Display_Type_Controller::get_instance($alternate_displayed_gallery->display_type);
        self::enqueue_frontend_resources_for_displayed_gallery($alternate_displayed_gallery, $alternate_controller);
    }

    /**
     * @param C_Displayed_Gallery $displayed_gallery
     * @param C_Display_Type_Controller $controller
     */
    public static function enqueue_frontend_resources_for_displayed_gallery($displayed_gallery, $controller)
    {
        if (is_null($displayed_gallery->id()))
            $displayed_gallery->id(md5(json_encode($displayed_gallery->get_entity())));

        self::$enqueued_displayed_gallery_ids[] = $displayed_gallery->id();

        $controller->enqueue_frontend_resources($displayed_gallery);
        self::enqueue_frontend_resources_for_alternate_displayed_gallery($displayed_gallery, $controller);
    }

    function is_rest_request()
    {
        return defined('REST_REQUEST') || strpos($_SERVER['REQUEST_URI'], 'wp-json') !== FALSE;
    }

    /**
     * This moves the NextGen custom CSS to the last of the queue
     */
    function fix_nextgen_custom_css_order()
    {
        global $wp_styles;
        if (in_array('nggallery', $wp_styles->queue))
        {
            foreach ($wp_styles->queue as $ndx => $style) {
                if ($style == 'nggallery')
                {
                    unset($wp_styles->queue[$ndx]);
                    $wp_styles->queue[] = 'nggallery';
                    break;
                }
            }
        }
    }

    /**
     * Locates the ids of displayed galleries that have been
     * removed from the post, and flags then for cleanup (deletion)
     * @global array $displayed_galleries_to_cleanup
     * @param int $post_id
     */
    function locate_stale_displayed_galleries($post_id)
    {
        global $displayed_galleries_to_cleanup;
        $displayed_galleries_to_cleanup	= array();
        $post							= get_post($post_id);
        $gallery_preview_url			= C_NextGen_Settings::get_instance()->get('gallery_preview_url');
        $preview_url = preg_quote($gallery_preview_url, '#');
        if (preg_match_all("#{$preview_url}/id--(\d+)#", html_entity_decode($post->post_content), $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $preview_url = preg_quote($match[0], '/');
                // The post was edited, and the displayed gallery placeholder was removed
                if (isset($_REQUEST['post_content']) && (!preg_match("/{$preview_url}/", $_POST['post_content']))) {
                    $displayed_galleries_to_cleanup[] = intval($match[1]);
                }
                // The post was deleted
                elseif (!isset($_REQUEST['action'])) {
                    $displayed_galleries_to_cleanup[] = intval($match[1]);
                }
            }
        }
    }

    /**
     * Deletes any displayed galleries that are no longer associated with a post/page
     *
     * @global array $displayed_galleries_to_cleanup
     * @param int $post_id
     */
    function cleanup_displayed_galleries($post_id)
    {
	    if (!apply_filters('ngg_cleanup_displayed_galleries', true, $post_id))
		    return;

        global $displayed_galleries_to_cleanup;
        $mapper = C_Displayed_Gallery_Mapper::get_instance();
        foreach ($displayed_galleries_to_cleanup as $id) {
	        $mapper->destroy($id);
        }
    }

    static function enqueue_fontawesome()
    {
        // The official plugin is active, we don't need to do anything outside the wp-admin
        if (defined('FONT_AWESOME_OFFICIAL_LOADED') && !is_admin())
            return;

        $settings = C_NextGen_Settings::get_instance();
        if ($settings->get('disable_fontawesome'))
            return;

        wp_register_script(
            'fontawesome_v4_shim',
            C_Router::get_instance()->get_static_url('photocrati-nextgen_gallery_display#fontawesome/js/v4-shims.min.js'),
            [],
            '5.3.1'
        );
        if (!wp_script_is('fontawesome', 'registered'))
        {
            add_filter('script_loader_tag', 'M_Gallery_Display::fix_fontawesome_script_tag', 10, 2);
            wp_enqueue_script(
                'fontawesome',
                C_Router::get_instance()->get_static_url('photocrati-nextgen_gallery_display#fontawesome/js/all.min.js'),
                ['fontawesome_v4_shim'],
                '5.3.1'
            );
        }

        if (!wp_style_is('fontawesome', 'registered'))
        {
            wp_enqueue_style(
                'fontawesome_v4_shim_style',
                C_Router::get_instance()->get_static_url('photocrati-nextgen_gallery_display#fontawesome/css/v4-shims.min.css')
            );
            wp_enqueue_style(
                'fontawesome',
                C_Router::get_instance()->get_static_url('photocrati-nextgen_gallery_display#fontawesome/css/all.min.css')
            );
        }

        wp_enqueue_script('fontawesome_v4_shim');
        wp_enqueue_script('fontawesome');
    }

    /**
     * WP doesn't allow an easy way to set the defer, crossorign, or integrity attributes on our <script>
     *
     * @param string $tag
     * @param string $handle
     * @return string
     */
    static function fix_fontawesome_script_tag($tag, $handle)
    {
        if ('fontawesome' !== $handle)
            return $tag;

        return str_replace(' src', ' defer data-auto-replace-svg="false" data-keep-original-source="false" data-search-pseudo-elements src', $tag);
    }

  static function _render_related_string($sluglist=array(), $maxImages=NULL, $type=NULL)
  {
      $settings = C_NextGen_Settings::get_instance();
      if (is_null($type)) $type = $settings->appendType;
	  if (is_null($maxImages)) $maxImages = $settings->maxImages;

	  if (!$sluglist) {
		  switch ($type) {
			  case 'tags':
				  if (function_exists('get_the_tags'))
				  {
					  $taglist = get_the_tags();
					  if (is_array($taglist)) {
						  foreach ($taglist as $tag) {
							  $sluglist[] = $tag->slug;
						  }
					  }
				  }
				  break;
			  case 'category':
				  $catlist = get_the_category();
				  if (is_array($catlist))
				  {
					  foreach ($catlist as $cat) {
						  $sluglist[] = $cat->category_nicename;
					  }
				  }
				  break;
		  }
	  }

      $taglist = implode(',', $sluglist);

      if ($taglist === 'uncategorized' || empty($taglist))
          return '';

      $renderer = C_Displayed_Gallery_Renderer::get_instance();
      $view     = C_Component_Factory::get_instance()->create('mvc_view', '');
      $retval = $renderer->display_images(array(
          'source' => 'tags',
          'container_ids' => $taglist,
          'display_type' => NGG_BASIC_THUMBNAILS,
          'images_per_page' => $maxImages,
          'maximum_entity_count' => $maxImages,
          'template' => $view->get_template_abspath('photocrati-nextgen_gallery_display#related'),
          'show_all_in_lightbox' => FALSE,
          'show_slideshow_link' => FALSE,
          'disable_pagination' => TRUE,
          'display_no_images_error' => FALSE
      ));

      if ($retval) wp_enqueue_style('nextgen_gallery_related_images');

      return apply_filters('ngg_show_related_gallery_content', $retval, $taglist);
  }

	function _render_related_images($content)
	{
    $settings = C_NextGen_Settings::get_instance();
      
		if ($settings->get('activateTags')) {
			$related = self::_render_related_string();
			
			if ($related != null) {
		    $heading = $settings->relatedHeading;
				$content .= $heading . $related;
			}
		}
		
		return $content;
	}

    /**
     * Adds menu item to the admin bar
     */
    function add_admin_bar_menu()
    {
        global $wp_admin_bar;

        if ( current_user_can('NextGEN Change options') ) {
            $wp_admin_bar->add_menu(array(
                'parent' => 'ngg-menu',
                'id' => 'ngg-menu-display_settings',
                'title' => __('Gallery Settings', 'nggallery'),
                'href' => admin_url('admin.php?page=ngg_display_settings')
            ));
        }
    }

    /**
     * Registers our static settings resources so the ATP module can find them later
     */
    function register_resources()
    {
		// Register custom post types for compatibility
        $types = array(
			'displayed_gallery'		=>	'NextGEN Gallery - Displayed Gallery',
			'display_type'			=>	'NextGEN Gallery - Display Type',
			'gal_display_source'	=>	'NextGEN Gallery - Displayed Gallery Source'
		);
		foreach ($types as $type => $label) {
			register_post_type($type, array(
				'label'		=>	$label,
				'publicly_queryable'	=>	FALSE,
				'exclude_from_search'	=>	TRUE,
			));
		}
		$router = C_Router::get_instance();

        wp_register_script(
            'nextgen_gallery_display_settings',
            $router->get_static_url('photocrati-nextgen_gallery_display#nextgen_gallery_display_settings.js'),
            array('jquery-ui-accordion', 'jquery-ui-tooltip'),
	        NGG_SCRIPT_VERSION
        );

        wp_register_style(
            'nextgen_gallery_display_settings',
            $router->get_static_url('photocrati-nextgen_gallery_display#nextgen_gallery_display_settings.css'),
	        array(),
	        NGG_SCRIPT_VERSION
        );

        wp_register_script(
            'shave.js',
            $router->get_static_url('photocrati-nextgen_gallery_display#shave.js'),
            [],
            NGG_SCRIPT_VERSION
        );

        wp_register_style(
            'nextgen_gallery_related_images',
            $router->get_static_url('photocrati-nextgen_gallery_display#nextgen_gallery_related_images.css'),
            array(),
            NGG_SCRIPT_VERSION
        );
        wp_register_script(
            'ngg_common',
            $router->get_static_url('photocrati-nextgen_gallery_display#common.js'),
            array('jquery', 'photocrati_ajax'),
            NGG_SCRIPT_VERSION,
            TRUE
        );
        wp_register_style(
            'ngg_trigger_buttons',
            $router->get_static_url('photocrati-nextgen_gallery_display#trigger_buttons.css'),
            array(),
            NGG_SCRIPT_VERSION
        );

        wp_register_script(
            'ngg_waitforimages',
            $router->get_static_url('photocrati-nextgen_gallery_display#jquery.waitforimages-2.4.0-modded.js'),
            array('jquery'),
            NGG_SCRIPT_VERSION
        );
    }

	/**
	 * Adds the display settings page to wp-admin
	 */
	function add_display_settings_page()
	{
		add_submenu_page(
			NGGFOLDER,
			__('NextGEN Gallery & Album Settings', 'nggallery'),
			__('Gallery Settings', 'nggallery'),
			'NextGEN Change options',
			NGG_DISPLAY_SETTINGS_SLUG,
			array(&$this->controller, 'index_action')
		);
	}

	/**
	 * Provides the [display_images] shortcode
	 * @param array $params
	 * @param string $inner_content
	 * @return string
	 */
	function display_images($params, $inner_content=NULL)
	{
		$renderer = C_Displayed_Gallery_Renderer::get_instance();
		return $renderer->display_images($params, $inner_content);
	}

    /**
     * Gets a value from the parameter array, and if not available, uses the default value
     *
     * @param string $name
     * @param mixed $default
     * @param array $params
     * @return mixed
     */
    function _get_param($name, $default, $params)
    {
        return (isset($params[$name])) ? $params[$name] : $default;
    }

    function get_type_list()
    {
        return array(
            'A_Display_Settings_Controller'         => 'adapter.display_settings_controller.php',
            'A_Display_Settings_Page'               => 'adapter.display_settings_page.php',
            'A_Displayed_Gallery_Trigger_Element'   => 'adapter.displayed_gallery_trigger_element.php',
            'A_Displayed_Gallery_Trigger_Resources' => 'adapter.displayed_gallery_trigger_resources.php',
            'A_Gallery_Display_Factory'             => 'adapter.gallery_display_factory.php',
            'A_Gallery_Display_View'                => 'adapter.gallery_display_view.php',
            'C_Display_Type'                        => 'class.display_type.php',
            'C_Display_Type_Controller'             => 'class.display_type_controller.php',
            'C_Display_Type_Installer'              => 'class.gallery_display_installer.php',
            'C_Display_Type_Mapper'                 => 'class.display_type_mapper.php',
            'C_Displayed_Gallery'                   => 'class.displayed_gallery.php',
            'C_Displayed_Gallery_Mapper'            => 'class.displayed_gallery_mapper.php',
            'C_Displayed_Gallery_Renderer'          => 'class.displayed_gallery_renderer.php',
            'C_Displayed_Gallery_Source_Manager'    => 'class.displayed_gallery_source_manager.php',
            'C_Displayed_Gallery_Trigger'           => 'class.displayed_gallery_trigger.php',
            'C_Displayed_Gallery_Trigger_Manager'   => 'class.displayed_gallery_trigger_manager.php',
            'Mixin_Display_Type_Form'               => 'mixin.display_type_form.php',
            'Hook_Propagate_Thumbnail_Dimensions_To_Settings' => 'hook.propagate_thumbnail_dimensions_to_settings.php'
        );
    }

    /**
     * Gets a list of directories in which display type template might be stored
     *
     * @param C_Display_Type $display_type
     * @return array
     */
    static function get_display_type_view_dirs($display_type)
    {
        if (!is_object($display_type)) $display_type = C_Display_Type_Mapper::get_instance()->find_by_name($display_type);

        /* Create array of directories to scan */
        $dirs = array(
            'default' => C_Component_Registry::get_instance()->get_module_dir($display_type->name) . DIRECTORY_SEPARATOR . 'templates',
            'custom' => WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'ngg' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $display_type->name . DIRECTORY_SEPARATOR . 'templates',
        );

        /* Apply filters so third party devs can add directories for their templates */
        $dirs = apply_filters('ngg_display_type_template_dirs', $dirs, $display_type);
        $dirs = apply_filters('ngg_' . $display_type->name . '_template_dirs', $dirs, $display_type);
        foreach ($display_type->aliases as $alias) {
          $dirs = apply_filters("ngg_{$alias}_template_dirs", $dirs, $display_type);
        }

        return $dirs;
    }

}

class C_Display_Type_Installer
{
	function get_registry()
	{
		return C_Component_Registry::get_instance();
    }
    
    function delete_duplicates($name)
    {
        $mapper				= C_Display_Type_Mapper::get_instance();
        $results =          $mapper->find_all(array('name = %s', $name));
        if (count($results) > 0) {
            $kept = array_pop($results); // the last should be the latest
            foreach ($results as $display_type) {
                $mapper->destroy($display_type);
            }
        }
        $mapper->flush_query_cache();
    }

	/**
	 * Installs a display type
	 * @param string $name
	 * @param array $properties
	 */
	function install_display_type($name, $properties=array())
	{
        $this->delete_duplicates($name);

		// Try to find the existing entity. If it doesn't exist, we'll create
		$fs					= C_Fs::get_instance();
        $mapper				= C_Display_Type_Mapper::get_instance();
        $display_type		= $mapper->find_by_name($name);
        $mapper->flush_query_cache();
		if (!$display_type)	$display_type = new stdClass;

		// Update the properties of the display type
		$properties['name'] = $name;
		$properties['installed_at_version'] = NGG_PLUGIN_VERSION;
		foreach ($properties as $key=>$val) {
			$display_type->$key = $val;
		}

		// Save the entity
        $retval = $mapper->save($display_type);
		return $retval;
	}

	/**
	 * Deletes all displayed galleries
	 */
	function uninstall_displayed_galleries()
	{
		$mapper = C_Displayed_Gallery_Mapper::get_instance();
		$mapper->delete()->run_query();
	}

	/**
	 * Uninstalls all display types
	 */
	function uninstall_display_types()
	{
		$mapper = C_Display_Type_Mapper::get_instance();
		$mapper->delete()->run_query();
	}

	/**
	 * Installs displayed gallery sources
	 * @param bool $reset (optional) Unused
	 */
	function install($reset=FALSE)
	{
        // Note: NGG Display types are registered in other modules

        // Force Pro display types to register themselves
        if (class_exists('C_NextGen_Pro_Installer')) {
            $pro_installer = new C_NextGen_Pro_Installer();
            $pro_installer->install_display_types();
        } elseif (class_exists('C_NextGen_Plus_Installer')) {
            $plus_installer = new C_NextGen_Plus_Installer();
            $plus_installer->install_display_types();
        }
	}

	/**
	 * Uninstalls this module
	 * @param bool $hard (optional) Unused
	 */
	function uninstall($hard = FALSE)
	{
		C_Photocrati_Transient_Manager::flush();

		$this->uninstall_display_types();

		// TODO temporary Don't remove galleries on uninstall
		//if ($hard) $this->uninstall_displayed_galleries();
	}
}

/**
 * Show related images for a post/page. Ngglegacy function
 * @param $taglist
 * @param int $maxImages (optional) Default = 0
 * @return string
 */
function nggShowRelatedGallery($taglist, $maxImages = 0)
{
	return M_Gallery_Display::_render_related_string($taglist, $maxImages, $type=NULL);
}

function nggShowRelatedImages($type=NULL, $maxImages=0)
{
	return M_Gallery_Display::_render_related_string(NULL, $maxImages, $type);
}

function the_related_images($type = 'tags', $maxNumbers = 7)
{
	echo nggShowRelatedImages($type, $maxNumbers);
}


new M_Gallery_Display();