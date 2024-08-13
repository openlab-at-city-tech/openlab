<?php
namespace Imagely\NGG\Util;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\Display\Shortcodes as ShortcodesManager;
use Imagely\NGG\DisplayedGallery\Renderer as DisplayedGalleryRenderer;

use Imagely\NGG\Display\Shortcodes;
use Imagely\NGG\DisplayedGallery\Renderer;
use Imagely\NGG\IGW\ATPManager;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\URL;

class ThirdPartyCompatibility {

	protected $wpseo_images = [];

	protected static $instance = null;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new ThirdPartyCompatibility();
		}
		return self::$instance;
	}

	public function __construct() {
		// The following constants were renamed for 2.0.41 and are kept here for legacy compatibility.
		$changed_constants = [
			'NEXTGEN_ADD_GALLERY_SLUG'                     => 'NGG_ADD_GALLERY_SLUG',
			'NEXTGEN_BASIC_SINGLEPIC_MODULE_NAME'          => 'NGG_BASIC_SINGLEPIC',
			'NEXTGEN_BASIC_TAG_CLOUD_MODULE_NAME'          => 'NGG_BASIC_TAGCLOUD',
			'NEXTGEN_DISPLAY_PRIORITY_BASE'                => 'NGG_DISPLAY_PRIORITY_BASE',
			'NEXTGEN_DISPLAY_PRIORITY_STEP'                => 'NGG_DISPLAY_PRIORITY_STEP',
			'NEXTGEN_DISPLAY_SETTINGS_SLUG'                => 'NGG_DISPLAY_SETTINGS_SLUG',
			'NEXTGEN_GALLERY_ATTACH_TO_POST_SLUG'          => 'NGG_ATTACH_TO_POST_SLUG',
			'NEXTGEN_GALLERY_BASIC_SLIDESHOW'              => 'NGG_BASIC_SLIDESHOW',
			'NEXTGEN_GALLERY_BASIC_THUMBNAILS'             => 'NGG_BASIC_THUMBNAILS',
			'NEXTGEN_GALLERY_CHANGE_OPTIONS_CAP'           => 'NGG_CHANGE_OPTIONS_CAP',
			'NEXTGEN_GALLERY_I18N_DOMAIN'                  => 'NGG_I18N_DOMAIN',
			'NEXTGEN_GALLERY_IMPORT_ROOT'                  => 'NGG_IMPORT_ROOT',
			'NEXTGEN_GALLERY_MODULE_DIR'                   => 'NGG_MODULE_DIR',
			'NEXTGEN_GALLERY_NEXTGEN_BASIC_COMPACT_ALBUM'  => 'NGG_BASIC_COMPACT_ALBUM',
			'NEXTGEN_GALLERY_NEXTGEN_BASIC_EXTENDED_ALBUM' => 'NGG_BASIC_EXTENDED_ALBUM',
			'NEXTGEN_GALLERY_NEXTGEN_BASIC_IMAGEBROWSER'   => 'NGG_BASIC_IMAGEBROWSER',
			'NEXTGEN_GALLERY_NGGLEGACY_MOD_DIR'            => 'NGG_LEGACY_MOD_DIR',
			'NEXTGEN_GALLERY_NGGLEGACY_MOD_URL'            => 'NGG_LEGACY_MOD_URL',
			'NEXTGEN_GALLERY_PLUGIN'                       => 'NGG_PLUGIN',
			'NEXTGEN_GALLERY_PLUGIN_BASENAME'              => 'NGG_PLUGIN_BASENAME',
			'NEXTGEN_GALLERY_PLUGIN_DIR'                   => 'NGG_PLUGIN_DIR',
			'NEXTGEN_GALLERY_PLUGIN_STARTED_AT'            => 'NGG_PLUGIN_STARTED_AT',
			'NEXTGEN_GALLERY_PLUGIN_VERSION'               => 'NGG_PLUGIN_VERSION',
			'NEXTGEN_GALLERY_PRODUCT_DIR'                  => 'NGG_PRODUCT_DIR',
			'NEXTGEN_GALLERY_PROTECT_IMAGE_MOD_STATIC_URL' => 'NGG_PROTUCT_IMAGE_MOD_STATIC_URL',
			'NEXTGEN_GALLERY_PROTECT_IMAGE_MOD_URL'        => 'NGG_PROTECT_IMAGE_MOD_URL',
			'NEXTGEN_GALLERY_TESTS_DIR'                    => 'NGG_TESTS_DIR',
			'NEXTGEN_LIGHTBOX_ADVANCED_OPTIONS_SLUG'       => 'NGG_LIGHTBOX_ADVANCED_OPTIONS_SLUG',
			'NEXTGEN_LIGHTBOX_OPTIONS_SLUG'                => 'NGG_LIGHTBOX_OPTIONS_SLUG',
			'NEXTGEN_OTHER_OPTIONS_SLUG'                   => 'NGG_OTHER_OPTIONS_SLUG',
		];

		foreach ( $changed_constants as $old => $new ) {
			if ( defined( $new ) && ! defined( $old ) ) {
				define( $old, constant( $new ) );
			}
		}
	}

	public function register_hooks() {
		\add_action( 'init', [ $this, 'colorbox' ], PHP_INT_MAX );
		\add_action( 'init', [ $this, 'flattr' ], PHP_INT_MAX );
		\add_action( 'wp', [ $this, 'bjlazyload' ], PHP_INT_MAX );

		\add_action( 'admin_init', [ $this, 'excellent_themes_admin' ], -10 );

		\add_action( 'plugins_loaded', [ $this, 'wpml' ], PHP_INT_MAX );
		\add_action( 'plugins_loaded', [ $this, 'wpml_translation_management' ], PHP_INT_MAX );
		\add_filter( 'wpml_is_redirected', [ $this, 'wpml_is_redirected' ], -10, 3 );

		\add_filter( 'headway_gzip', [ $this, 'headway_gzip' ], ( PHP_INT_MAX - 1 ) );
		\add_filter( 'ckeditor_external_plugins', [ $this, 'ckeditor_plugins' ], 11 );
		\add_filter( 'the_content', [ $this, 'check_weaverii' ], -( PHP_INT_MAX - 2 ) );
		\add_action( 'wp', [ $this, 'check_for_jquery_lightbox' ] );
		\add_filter( 'get_the_excerpt', [ $this, 'disable_galleries_in_excerpts' ], 1 );
		\add_filter( 'get_the_excerpt', [ $this, 'enable_galleries_in_excerpts' ], PHP_INT_MAX - 1 );
		\add_action( 'debug_bar_enqueue_scripts', [ $this, 'no_debug_bar' ] );
		\add_filter( 'ngg_atp_show_display_type', [ $this, 'atp_check_pro_albums' ], 10, 2 );
		\add_filter( 'wpseo_sitemap_urlimages', [ $this, 'add_wpseo_xml_sitemap_images' ], 10, 2 );
		\add_filter( 'ngg_pre_delete_unused_term_id', [ $this, 'dont_auto_purge_wpml_terms' ] );
		\add_filter( 'rank_math/sitemap/urlimages', [ $this, 'add_rankmath_seo_images' ], 10, 2 );

		// Nimble Builder needs special consideration because of our shortcode manager's use of placeholders.
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_nimble_builder_frontend_resources' ] );
		\add_filter( 'ngg_shortcode_placeholder', [ $this, 'nimble_builder_shortcodes' ], 10, 4 );

		\add_filter(
			'wp_sweep_excluded_taxonomies',
			function ( $taxonomies ) {
				$taxonomies[] = 'ngg_tag';
				return $taxonomies;
			}
		);

		if ( $this->is_ngg_page() ) {
			\add_action( 'admin_enqueue_scripts', [ $this, 'dequeue_spider_calendar_resources' ] );
		}

		// Like WPML, BuddyPress is incompatible with our routing hacks.
		if ( function_exists( 'buddypress' ) ) {
			Router::$use_canonical_redirect = false;
			Router::$use_old_slugs          = false;
		}

		// WPML fix.
		if ( in_array( 'SitePress', get_declared_classes(), true ) ) {
			Router::$use_canonical_redirect = true;
			Router::$use_old_slugs          = false;
			\add_action( 'template_redirect', [ $this, 'fix_wpml_canonical_redirect' ], 1 );
		}

		add_action( 'the_post', [ $this, 'fix_page_parameter' ] );
	}

	/**
	 * Adds NextGEN images to RankMath when generating page & post sitemaps
	 *
	 * @param array $images
	 * @param int   $post_ID
	 * @return array
	 */
	public function add_rankmath_seo_images( $images, $post_ID ) {
		$post = get_post( $post_ID );
		preg_match_all(
			'/' . get_shortcode_regex() . '/',
			$post->post_content,
			$matches,
			PREG_SET_ORDER
		);

		$renderer   = DisplayedGalleryRenderer::get_instance();
		$storage    = StorageManager::get_instance();
		$shortcodes = ShortcodesManager::get_instance()->get_shortcodes();
		$retval     = [];

		foreach ( $matches as $match ) {
			// Only process our shortcodes.
			if ( in_array( $match[2], $shortcodes ) ) {
				continue;
			}
			$params = shortcode_parse_atts( trim( $match[0], '[]' ) );
			if ( in_array( $params[0], $shortcodes ) ) {
				unset( $params[0] );
			}

			$displayed_gallery = $renderer->params_to_displayed_gallery( $params );

			// There's no displayed gallery, so no reason to continue.
			if ( ! $displayed_gallery ) {
				continue;
			}

			foreach ( $displayed_gallery->get_entities() as $entity ) {
				// Do not start following albums' into their descent into madness.
				if ( isset( $entity->galdesc ) ) {
					continue;
				}
				$retval[] = [ 'src' => $storage->get_image_url( $entity ) ];
			}
		}

		return array_merge( $images, $retval );
	}

	/**
	 * This code was originally added to correct a bug in Pro 1.0.10 and was meant to be temporary. However now the
	 * albums pagination relies on this to function correctly, and fixing it properly would require more time than its worth.
	 *
	 * TODO: Remove this once the router and wordpress_routing modules are removed.
	 */
	public function fix_page_parameter() {
		global $post;

		if ( $post and isset( $post->post_content ) and ( strpos( $post->post_content, '<!--nextpage-->' ) === false ) and ( strpos( $_SERVER['REQUEST_URI'], '/page/' ) !== false ) ) {
			if ( preg_match( '#/page/(\\d+)#', $_SERVER['REQUEST_URI'], $match ) ) {
				$_REQUEST['page'] = $match[1];
			}
		}
	}

	/**
	 * @param string $placeholder
	 * @param string $shortcode
	 * @param array  $params
	 * @param string $inner_content
	 * @return string
	 */
	public function nimble_builder_shortcodes( $placeholder, $shortcode, $params, $inner_content ) {
		if ( ! defined( 'NIMBLE_PLUGIN_FILE' ) ) {
			return $placeholder;
		}

		// Invoke our gallery rendering now.
		if ( \doing_filter( 'the_nimble_tinymce_module_content' ) ) {
			$placeholder = Shortcodes::get_instance()->render_shortcode( $shortcode, $params, $inner_content );
		}

		return $placeholder;
	}

	/**
	 * @param array $collection
	 */
	public function nimble_find_content( $collection ) {
		if ( ! is_array( $collection ) ) {
			return;
		}

		foreach ( $collection as $item ) {
			if ( isset( $item['value'] ) && ! empty( $item['value']['text_content'] ) ) {
				\Imagely\NGG\Display\DisplayManager::enqueue_frontend_resources_for_content( $item['value']['text_content'] );
			}

			if ( ! empty( $item['collection'] ) ) {
				$this->nimble_find_content( $item['collection'] );
			}
		}
	}

	public function enqueue_nimble_builder_frontend_resources() {
		if ( ! defined( 'NIMBLE_PLUGIN_FILE' ) ) {
			return;
		}

		if ( ! function_exists( '\Nimble\skp_get_skope_id' )
		|| ! function_exists( '\Nimble\sek_get_skoped_seks' )
		|| ! function_exists( '\Nimble\sek_sniff_and_decode_richtext' ) ) {
			return;
		}

		// Bail now if called before skope_id is set (before @hook 'wp').
		$skope_id = \Nimble\skp_get_skope_id();
		if ( empty( $skope_id ) || '_skope_not_set_' === $skope_id ) {
			return;
		}

		$global_sections = \Nimble\sek_get_skoped_seks( NIMBLE_GLOBAL_SKOPE_ID );
		$local_sections  = \Nimble\sek_get_skoped_seks( $skope_id );
		$raw_content     = \Nimble\sek_sniff_and_decode_richtext(
			[
				'local_sections'  => $local_sections,
				'global_sections' => $global_sections,
			]
		);
		foreach ( $raw_content['local_sections'] as $section ) {
			$this->nimble_find_content( $section );
		}
		foreach ( $raw_content['global_sections'] as $section ) {
			$this->nimble_find_content( $section );
		}
	}

	public function is_ngg_page(): bool {
		return ( \is_admin() && isset( $_REQUEST['page'] ) && false !== strpos( $_REQUEST['page'], 'ngg' ) );
	}

	public function dequeue_spider_calendar_resources() {
		\remove_filter( 'admin_head', 'spide_ShowTinyMCE' );
	}

	/**
	 * Filter support for WordPress SEO
	 *
	 * @param array $images Provided by WPSEO Filter
	 * @param int   $post_id ID Provided by WPSEO Filter
	 * @return array $image List of a displayed galleries entities
	 */
	public function add_wpseo_xml_sitemap_images( $images, $post_id ) {
		$this->wpseo_images = $images;

		$post = \get_post( $post_id );

		// Assign our own shortcode handle.
		\remove_all_shortcodes();
		Shortcodes::add( 'ngg', [ $this, 'wpseo_shortcode_handler' ] );
		Shortcodes::add( 'ngg_images', [ $this, 'wpseo_shortcode_handler' ] );
		\do_shortcode( $post->post_content );

		return $this->wpseo_images;
	}

	/**
	 * Processes ngg_images shortcode when WordPress SEO is building sitemaps. Adds images belonging to a
	 * displayed gallery to $this->wpseo_images for the assigned filter method to return.
	 *
	 * @param array $params Array of shortcode parameters
	 * @param null  $inner_content
	 */
	public function wpseo_shortcode_handler( $params, $inner_content = null ) {
		$renderer          = Renderer::get_instance();
		$displayed_gallery = $renderer->params_to_displayed_gallery( $params );

		if ( $displayed_gallery ) {
			$gallery_storage = StorageManager::get_instance();
			$settings        = Settings::get_instance();
			$source          = $displayed_gallery->get_source();
			if ( in_array( 'image', $source->returns ) ) {
				foreach ( $displayed_gallery->get_entities() as $image ) {
					$named_image_size     = $settings->get( 'imgAutoResize' ) ? 'full' : 'thumb';
					$sitemap_image        = [
						'src'   => $gallery_storage->get_image_url( $image, $named_image_size ),
						'alt'   => $image->alttext,
						'title' => $image->description ? $image->description : $image->alttext,
					];
					$this->wpseo_images[] = $sitemap_image;
				}
			}
		}
	}

	/**
	 * This style causes problems with Excellent Themes admin settings
	 */
	public function excellent_themes_admin() {
		if ( \is_admin() && ( isset( $_GET['page'] ) && 0 == strpos( $_GET['page'], 'et_' ) ) ) {
			\wp_deregister_style( 'ngg-jquery-ui' );
		}
	}

	public function atp_check_pro_albums( $available, $display_type ) {
		if ( ! defined( 'NGG_PRO_ALBUMS' ) ) {
			return $available;
		}

		if ( in_array( $display_type->name, [ NGG_PRO_LIST_ALBUM, NGG_PRO_GRID_ALBUM ] )
		&& in_array( 'C_Component_Registry', get_declared_classes(), true )
		&& \C_Component_Registry::get_instance()->is_module_loaded( NGG_PRO_ALBUMS ) ) {
			$available = true;
		}

		return $available;
	}

	public function no_debug_bar() {
		if ( ATPManager::is_atp_url() ) {
			\wp_dequeue_script( 'debug-bar-console' );
		}
	}

	// A lot of routing issues start occuring with WordPress SEO when the routing system is
	// initialized by the excerpt, and then again from the post content.
	public function disable_galleries_in_excerpts( $excerpt ) {
		if ( in_array( 'WPSEO_OpenGraph', get_declared_classes(), true ) ) {
			ATPManager::$substitute_placeholders = false;
		}

		return $excerpt;
	}

	public function enable_galleries_in_excerpts( $excerpt ) {
		if ( in_array( 'WPSEO_OpenGraph', get_declared_classes(), true ) ) {
			ATPManager::$substitute_placeholders = true;
		}

		return $excerpt;
	}

	public function fix_wpml_canonical_redirect() {
		Router::$use_canonical_redirect = false;
		Router::$use_old_slugs          = false;
	}

	/**
	 * NGG automatically purges unused terms when managing a gallery, but this also ensnares WPML translations
	 *
	 * @param $term_id
	 * @return bool
	 */
	public function dont_auto_purge_wpml_terms( $term_id ) {
		$args               = [
			'element_id'   => $term_id,
			'element_type' => 'ngg_tag',
		];
		$term_language_code = \apply_filters( 'wpml_element_language_code', null, $args );

		if ( ! empty( $term_language_code ) ) {
			return false;
		} else {
			return $term_id;
		}
	}

	/**
	 * Prevent WPML's parse_query() from conflicting with NGG's pagination & router module controlled endpoints
	 *
	 * @param string    $redirect What WPML is send to wp_safe_redirect()
	 * @param int       $post_id
	 * @param \WP_Query $q
	 * @return bool|string FALSE prevents a redirect from occurring
	 */
	public function wpml_is_redirected( $redirect, $post_id, $q ) {
		$router = Router::get_instance();
		if ( ! $router->serve_request() && $router->has_parameter_segments() ) {
			return false;
		} else {
			return $redirect;
		}
	}

	/**
	 * CKEditor features a custom NextGEN shortcode generator that unfortunately relies on parts of the NextGEN
	 * 1.9x API that has been deprecated in NextGEN 2.0
	 *
	 * @param $plugins
	 * @return mixed
	 */
	public function ckeditor_plugins( $plugins ) {
		if ( ! in_array( 'add_ckeditor_button', get_declared_classes(), true ) ) {
			return $plugins;
		}

		if ( ! empty( $plugins['nextgen'] ) ) {
			unset( $plugins['nextgen'] );
		}

		return $plugins;
	}

	public function check_for_jquery_lightbox() {
		// Fix for jQuery Lightbox: http://wordpress.org/plugins/wp-jquery-lightbox/
		// jQuery Lightbox tries to modify the content of a post, but it does so before we modify
		// the content, and therefore it's modifications have no effect on our galleries.
		if ( function_exists( 'jqlb_autoexpand_rel_wlightbox' ) ) {
			$settings = Settings::get_instance();

			// First, we make it appear that NGG has no lightbox effect enabled. That way we don't any lightbox resources.
			$settings->delete( 'thumbEffect' );

			// We would normally just let the third-party plugin do it's thing, but it's regex doesn't
			// seem to work on our <a> tags (perhaps because they span multiple of lines or have data attributes)
			// So instead, we just do what the third-party plugin wants - add the rel attribute.
			$settings->set( 'thumbCode', "rel='lightbox[%POST_ID%]'" );
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
	public function check_weaverii( $content ) {
		if ( function_exists( 'weaverii_show_posts_shortcode' ) ) {
			Shortcodes::get_instance()->activate_all();
		}

		return $content;
	}

	/**
	 * WPML assigns an action to 'init' that *may* enqueue some admin-side JS. This JS relies on some inline JS
	 * to be injected that isn't present in ATP so for ATP requests ONLY we disable their action that enqueues
	 * their JS files.
	 */
	public function wpml() {
		if ( ! in_array( 'SitePress', get_declared_classes(), true ) ) {
			return;
		}

		if ( ! ATPManager::is_atp_url() ) {
			return;
		}

		global $wp_filter;

		if ( empty( $wp_filter['init'][2] ) && empty( $wp_filter['after_setup_theme'][1] ) ) {
			return;
		}

		foreach ( $wp_filter['init'][2] as $id => $filter ) {
			if ( ! strpos( $id, 'js_load' ) ) {
				continue;
			}

			$object = $filter['function'][0];

			if ( is_object( $object ) && get_class( $object ) != 'SitePress' ) {
				continue;
			}

			\remove_action( 'init', [ $object, 'js_load' ], 2 );
		}

		foreach ( $wp_filter['after_setup_theme'][1] as $id => $filter ) {
			if ( $id !== 'wpml_installer_instance_delegator' ) {
				continue;
			}

			\remove_action( 'after_setup_theme', 'wpml_installer_instance_delegator', 1 );
		}
	}

	/**
	 * WPML Translation Management has a similar problem to plain ol' WPML
	 */
	public function wpml_translation_management() {
		if ( ! in_array( 'WPML_Translation_Management', get_declared_classes(), true ) ) {
			return;
		}

		if ( ! ATPManager::is_atp_url() ) {
			return;
		}

		global $wp_filter;

		if ( empty( $wp_filter['init'][10] ) ) {
			return;
		}

		foreach ( $wp_filter['init'][10] as $id => $filter ) {
			if ( ! strpos( $id, 'init' ) ) {
				continue;
			}

			$object = $filter['function'][0];

			if ( is_object( $object ) && get_class( $object ) != 'WPML_Translation_Management' ) {
				continue;
			}

			\remove_action( 'init', [ $object, 'init' ], 10 );
		}
	}

	/**
	 * Headway themes offer gzip compression, but it causes problems with NextGEN output. Disable that feature while
	 * NextGEN is active.
	 *
	 * @param $option
	 * @return bool
	 */
	public function headway_gzip( $option ) {
		if ( ! in_array( 'HeadwayOption', get_declared_classes(), true ) ) {
			return $option;
		}

		return false;
	}

	/**
	 * Colorbox fires a filter (pri=100) to add class attributes to images via a the_content filter. We fire our
	 * shortcodes at PHP_INT_MAX-1 to avoid encoding issues with some themes. Here we move the Colorbox filters
	 * priority to PHP_INT_MAX so that they run after our shortcode text has been replaced with rendered galleries.
	 */
	public function colorbox() {
		if ( ! in_array( 'JQueryColorboxFrontend', get_declared_classes(), true ) ) {
			return;
		}

		global $wp_filter;

		if ( empty( $wp_filter['the_content'][100] ) ) {
			return;
		}

		foreach ( $wp_filter['the_content'][100] as $id => $filter ) {
			if ( ! strpos( $id, 'addColorboxGroupIdToImages' ) ) {
				continue;
			}

			$object = $filter['function'][0];

			if ( is_object( $object ) && get_class( $object ) != 'JQueryColorboxFrontend' ) {
				continue;
			}

			\remove_filter( 'the_content', [ $object, 'addColorboxGroupIdToImages' ], 100 );
			\remove_filter( 'the_excerpt', [ $object, 'addColorboxGroupIdToImages' ], 100 );
			\add_filter( 'the_content', [ $object, 'addColorboxGroupIdToImages' ], PHP_INT_MAX );
			\add_filter( 'the_excerpt', [ $object, 'addColorboxGroupIdToImages' ], PHP_INT_MAX );
			break;
		}
	}

	/**
	 * Flattr fires a filter (pri=32767) on "the_content" that recurses. This causes problems,
	 * see https://core.trac.wordpress.org/ticket/17817 for more information. Moving their filter to PHP_INT_MAX
	 * is enough for us though
	 */
	public function flattr() {
		if ( ! in_array( 'Flattr', get_declared_classes(), true ) ) {
			return;
		}

		global $wp_filter;

		$level = 32767;

		if ( empty( $wp_filter['the_content'][ $level ] ) ) {
			return;
		}

		foreach ( $wp_filter['the_content'][ $level ] as $id => $filter ) {
			if ( ! strpos( $id, 'injectIntoTheContent' ) ) {
				continue;
			}

			$object = $filter['function'][0];

			if ( is_object( $object ) && get_class( $object ) != 'Flattr' ) {
				continue;
			}

			\remove_filter( 'the_content', [ $object, 'injectIntoTheContent' ], $level );
			\add_filter( 'the_content', [ $object, 'injectIntoTheContent' ], PHP_INT_MAX );
			break;
		}
	}

	/**
	 * For the same reasons as Colorbox we move BJ-Lazy-load's filter() method to a later priority so it can access
	 * our rendered galleries.
	 */
	public function bjlazyload() {
		if ( ! in_array( 'BJLL', get_declared_classes(), true ) ) {
			return;
		}

		global $wp_filter;

		if ( empty( $wp_filter['the_content'][200] ) ) {
			return;
		}

		foreach ( $wp_filter['the_content'][200] as $id => $filter ) {
			if ( ! strpos( $id, 'filter' ) ) {
				continue;
			}

			$object = $filter['function'][0];

			if ( is_object( $object ) && get_class( $object ) != 'BJLL' ) {
				continue;
			}

			\remove_filter( 'the_content', [ $object, 'filter' ], 200 );
			\add_filter( 'the_content', [ $object, 'filter' ], PHP_INT_MAX );
			break;
		}

		\add_filter( 'the_content', [ $this, 'bjlazyload_filter' ], PHP_INT_MAX - 1 );
	}

	/**
	 * BJ-Lazy-load's regex is lazy and doesn't handle multiline search or instances where <img is immediately followed
	 * by a newline. The following regex replaces newlines and strips unnecessary space. We fire this filter
	 * before BJ-Lazy-Load's to make our galleries compatible with its expectations.
	 *
	 * @param string $content
	 * @return string
	 */
	public function bjlazyload_filter( $content ) {
		return trim( preg_replace( '/\\s\\s+/', ' ', $content ) );
	}
}
