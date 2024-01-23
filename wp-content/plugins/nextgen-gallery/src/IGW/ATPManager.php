<?php

namespace Imagely\NGG\IGW;

use Imagely\NGG\DataMappers\DisplayedGallery as DisplayedGalleryMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

use Imagely\NGG\Display\{DisplayManager, StaticAssets, View};
use Imagely\NGG\DisplayedGallery\Renderer;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\{Security, URL};

class ATPManager {

	public $attach_to_post_tinymce_plugin = 'NextGEN_AttachToPost';

	public static $substitute_placeholders = true;

	protected static $instance = null;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new ATPManager();
		}
		return self::$instance;
	}

	public static function is_atp_url() {
		return strpos( strtolower( $_SERVER['REQUEST_URI'] ), NGG_ATTACH_TO_POST_SLUG ) !== false;
	}

	public function register_hooks() {
		add_filter( 'wpseo_opengraph_image', [ $this, 'hide_preview_image_from_yoast' ] );
		add_filter( 'wpseo_twitter_image', [ $this, 'hide_preview_image_from_yoast' ] );
		add_filter( 'wpseo_sitemap_urlimages', [ $this, 'remove_preview_images_from_yoast_sitemap' ], null, 2 );

		// Admin-only hooks.
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_static_resources' ], -100 );

			// Elementor's editor.php runs `new \WP_Scripts()` which requires we enqueue scripts on both
			// admin_enqueue_scripts and this action if we want our resources to be used with the page builder.
			add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_static_resources' ] );

			add_action( 'media_buttons', [ $this, 'add_media_button' ], 15 );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_tinymce_resources' ] );
			add_filter( 'mce_buttons', [ $this, 'add_attach_to_post_button' ] );
			add_filter( 'mce_external_plugins', [ $this, 'add_attach_to_post_tinymce_plugin' ] );
			add_filter( 'wp_mce_translation', [ $this, 'add_attach_to_post_tinymce_i18n' ] );
			add_action( 'admin_print_scripts', [ $this, 'print_tinymce_placeholder_template' ] );
			add_action( 'admin_init', [ $this, 'route_insert_gallery_window' ] );
		}

		// Add hook to substitute displayed gallery placeholders.
		if ( ! is_admin() ) {
			add_filter( 'the_content', [ $this, 'substitute_placeholder_imgs' ], PHP_INT_MAX, 1 );
		}
	}

	/**
	 * Route the IGW requests using wp-admin
	 */
	function route_insert_gallery_window() {
		if ( isset( $_REQUEST[ NGG_ATTACH_TO_POST_SLUG ] )
			&& isset( $_REQUEST['nonce'] )
			&& \wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'ngg_attach_to_post_iframe' ) ) {
			print ( new Controller() )->index_action();
			exit();
		}
	}

	/**
	 * Renders the underscore template used by TinyMCE for IGW placeholders
	 */
	public function print_tinymce_placeholder_template() {
		$view     = new View( 'AttachToPost/tinymce_placeholder', [], 'photocrati-attach_to_post#tinymce_placeholder' );
		$template = $view->find_template_abspath( 'AttachToPost/tinymce_placeholder', 'photocrati-attach_to_post#tinymce_placeholder' );
		readfile( $template );
	}

	/**
	 * Prevents ATP preview image placeholders from being used as opengraph / twitter metadata
	 *
	 * @param string $image
	 * @return null|string
	 */
	public function hide_preview_image_from_yoast( $image ) {
		if ( strpos( $image, NGG_ATTACH_TO_POST_SLUG ) !== false ) {
			return null;
		}
		return $image;
	}

	/**
	 * Removes IGW preview/placeholder images from Yoast's sitemap
	 *
	 * @param $images
	 * @param $post_id
	 * @return array
	 */
	public function remove_preview_images_from_yoast_sitemap( $images, $post_id ) {
		$retval = [];

		foreach ( $images as $image ) {
			if ( strpos( $image['src'], NGG_ATTACH_TO_POST_SLUG ) === false ) {
				$retval[] = $image;
			} else {
				// Lookup images for the displayed gallery.
				if ( preg_match( '/(\d+)$/', $image['src'], $match ) ) {
					$displayed_gallery_id = $match[1];
					$mapper               = DisplayedGalleryMapper::get_instance();
					$displayed_gallery    = $mapper->find( $displayed_gallery_id, true );
					if ( $displayed_gallery ) {
						$gallery_storage = StorageManager::get_instance();
						$settings        = Settings::get_instance();
						$source_obj      = $displayed_gallery->get_source();
						if ( in_array( 'image', $source_obj->returns ) ) {
							foreach ( $displayed_gallery->get_entities() as $image ) {
								$named_image_size = $settings->get( 'imgAutoResize' ) ? 'full' : 'thumb';

								$sitemap_image = [
									'src'   => $gallery_storage->get_image_url( $image, $named_image_size ),
									'alt'   => $image->alttext,
									'title' => $image->description ? $image->description : $image->alttext,
								];
								$retval[]      = $sitemap_image;
							}
						}
					}
				}
			}
		}

		return $retval;
	}

	/**
	 * In 2.0.66.X and earlier, ATP placeholder images used a different url than what 2.0.69 uses and must be converted.
	 *
	 * @param string $content
	 * @return string
	 */
	public function fix_preview_images( $content ) {
		$content = str_replace(
			site_url( '/' . NGG_ATTACH_TO_POST_SLUG . '/preview/id--' ),
			admin_url( '/?' . NGG_ATTACH_TO_POST_SLUG . '=' . NGG_ATTACH_TO_POST_SLUG . '/preview/id--' ),
			$content
		);

		$content = str_replace(
			site_url( '/index.php/' . NGG_ATTACH_TO_POST_SLUG . '/preview/id--' ),
			admin_url( '/?' . NGG_ATTACH_TO_POST_SLUG . '=' . NGG_ATTACH_TO_POST_SLUG . '/preview/id--' ),
			$content
		);

		return $content;
	}

	public function add_media_button() {
		$search = [
			Security::is_allowed( 'NextGEN Attach Interface' ),
			Security::is_allowed( 'NextGEN Use TinyMCE' ),
		];

		if ( in_array( false, $search ) ) {
			return;
		}

		$button_url = StaticAssets::get_url( 'AttachToPost/igw_button.png', 'photocrati-attach_to_post#igw_button.png' );
		$label      = \__( 'Add Gallery', 'nggallery' );
		$igw_url    = admin_url( '/?' . NGG_ATTACH_TO_POST_SLUG . '=1' );
		$igw_url   .= '&nonce=' . \wp_create_nonce( 'ngg_attach_to_post_iframe' );
		$igw_url   .= '&KeepThis=true&TB_iframe=true&height=600&width=1000';

		printf( '<a href="%s" data-editor="content" class="button ngg-add-gallery thickbox" id="ngg-media-button" class="button" ><img src="%s" style="padding:0 4px 0 0; margin-left: -2px; margin-top:-3px; max-width: 20px;">%s</a>', $igw_url, $button_url, $label );
	}

	/**
	 * Substitutes the gallery placeholder content with the gallery type frontend
	 * view, returns a list of static resources that need to be loaded
	 *
	 * @param string $content
	 */
	public function substitute_placeholder_imgs( $content ) {
		$content = $this->fix_preview_images( $content );

		// To match ATP entries we compare the stored url against a generic path; entries MUST have a gallery ID.
		if ( preg_match_all( '#<img.*http(s)?://(.*)?' . NGG_ATTACH_TO_POST_SLUG . '(=|/)preview(/|&|&amp;)id(=|--)(\\d+).*?>#mi', $content, $matches, PREG_SET_ORDER ) ) {
			$mapper = DisplayedGalleryMapper::get_instance();
			foreach ( $matches as $match ) {
				// Find the displayed gallery.
				$displayed_gallery_id = $match[6];
				$displayed_gallery    = $mapper->find( $displayed_gallery_id, true );

				// Get the content for the displayed gallery.
				$retval = '<p>' . _( 'Invalid Displayed Gallery' ) . '</p>';
				if ( $displayed_gallery ) {
					$retval   = '';
					$renderer = Renderer::get_instance();
					if ( defined( 'NGG_SHOW_DISPLAYED_GALLERY_ERRORS' ) && NGG_SHOW_DISPLAYED_GALLERY_ERRORS && ! $displayed_gallery->is_valid() ) {
						$retval .= var_export( $displayed_gallery->validation(), true );
					}
					if ( self::$substitute_placeholders ) {
						$retval .= $renderer->render( $displayed_gallery, true );
					}
				}

				$content = str_replace( $match[0], $retval, $content );
			}
		}

		return $content;
	}

	/**
	 * Enqueues static resources required by the Attach-To-Post interface
	 */
	public function enqueue_static_resources() {
		// Enqueue resources needed at post/page level.
		if ( $this->is_new_or_edit_post_screen() ) {
			\wp_enqueue_script( 'nextgen_admin_js' );
			\wp_enqueue_style( 'nextgen_admin_css' );
			\wp_enqueue_script( 'frame_event_publisher' );

			DisplayManager::enqueue_fontawesome();

			\wp_register_script(
				'Base64',
				StaticAssets::get_url( 'AttachToPost/base64.js', 'photocrati-attach_to_post#base64.js' ),
				[],
				NGG_SCRIPT_VERSION
			);

			\wp_enqueue_style(
				'ngg_attach_to_post_dialog',
				StaticAssets::get_url( 'AttachToPost/attach_to_post_dialog.css', 'photocrati-attach_to_post#attach_to_post_dialog.css' ),
				[ 'gritter' ],
				NGG_SCRIPT_VERSION
			);

			\wp_enqueue_script(
				'ngg-igw',
				StaticAssets::get_url( 'AttachToPost/igw.js', 'photocrati-attach_to_post#igw.js' ),
				[ 'jquery', 'Base64', 'gritter' ],
				NGG_SCRIPT_VERSION
			);
			\wp_localize_script(
				'ngg-igw',
				'ngg_igw_i18n',
				[
					'nextgen_gallery' => \__( 'NextGEN Gallery', 'nggallery' ),
					'edit'            => \__( 'Edit', 'nggallery' ),
					'remove'          => \__( 'Delete', 'nggallery' ),
				]
			);

			// Nonce verification is not necessary here: we are only enqueueing resources for the IGW iframe children.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		} elseif ( isset( $_REQUEST['attach_to_post'] )
					|| isset( $_REQUEST['nextgen-attach_to_post'] )
					|| ( isset( $_REQUEST['page'] ) && false !== strpos( $_REQUEST['page'], 'nggallery' ) ) ) {
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			\wp_enqueue_script(
				'iframely',
				StaticAssets::get_url( 'AttachToPost/iframely.js', 'photocrati-attach_to_post#iframely.js' ),
				[],
				NGG_SCRIPT_VERSION
			);
			\wp_enqueue_style(
				'iframely',
				StaticAssets::get_url( 'AttachToPost/iframely.css', 'photocrati-attach_to_post#iframely.css' ),
				[],
				NGG_SCRIPT_VERSION
			);
			\wp_enqueue_style( 'nextgen_addgallery_page' );
			\wp_enqueue_style( 'ngg_marketing_blocks_style' );
			\wp_enqueue_style( 'uppy' );
			\wp_enqueue_script( 'nextgen_admin_js' );
			\wp_enqueue_style( 'nextgen_admin_css' );
		}
	}

	public function is_new_or_edit_post_screen() {
		return preg_match( '/\/wp-admin\/(post|post-new)\.php$/', $_SERVER['SCRIPT_NAME'] );
	}

	public function can_use_tinymce() {
		$checks = [
			Security::is_allowed( 'NextGEN Attach Interface' ),
			Security::is_allowed( 'NextGEN Use TinyMCE' ),
			\get_user_option( 'rich_editing' ) == 'true',
		];
		return ! in_array( false, $checks );
	}

	/**
	 * Enqueues resources needed by the TinyMCE editor
	 */
	public function enqueue_tinymce_resources() {
		if ( $this->is_new_or_edit_post_screen() ) {
			\add_editor_style( 'https://fonts.googleapis.com/css?family=Lato' );
			\add_editor_style( StaticAssets::get_url( 'AttachToPost/ngg_attach_to_post_tinymce_plugin.css', 'photocrati-attach_to_post#ngg_attach_to_post_tinymce_plugin.css' ) );
			\wp_enqueue_script( 'photocrati_ajax' );

			\wp_localize_script(
				'media-editor',
				'igw',
				[
					'url' => \admin_url( '/?' . NGG_ATTACH_TO_POST_SLUG . '=1' ),
				]
			);

			\wp_localize_script(
				'photocrati_ajax',
				'ngg_tinymce_plugin',
				[
					'url'   => add_query_arg(
						'ver',
						NGG_SCRIPT_VERSION,
						StaticAssets::get_url( 'AttachToPost/ngg_attach_to_post_tinymce_plugin.js', 'photocrati-attach_to_post#ngg_attach_to_post_tinymce_plugin.js' )
					),
					'i18n'  => [
						'button_label' => \__( 'Add NextGEN Gallery', 'nggallery' ),
					],
					'name'  => $this->attach_to_post_tinymce_plugin,
					'nonce' => \wp_create_nonce( 'ngg_attach_to_post_iframe' ),
				]
			);
		}
	}

	/**
	 * Adds a TinyMCE button for the Attach To Post plugin
	 *
	 * @param array $buttons
	 * @return array
	 */
	public function add_attach_to_post_button( $buttons ) {
		if ( $this->can_use_tinymce() ) {
			array_push(
				$buttons,
				'separator',
				$this->attach_to_post_tinymce_plugin
			);
		}

		return $buttons;
	}

	/**
	 * Adds the Attach To Post TinyMCE plugin
	 *
	 * @param array $plugins
	 * @return array
	 * @uses mce_external_plugins filter
	 */
	public function add_attach_to_post_tinymce_plugin( $plugins ) {
		if ( $this->can_use_tinymce() ) {
			$plugins[ $this->attach_to_post_tinymce_plugin ] = \add_query_arg(
				'ver',
				NGG_SCRIPT_VERSION,
				StaticAssets::get_url( 'AttachToPost/ngg_attach_to_post_tinymce_plugin.js', 'photocrati-attach_to_post#ngg_attach_to_post_tinymce_plugin.js' )
			);
		}

		return $plugins;
	}

	/**
	 * Adds the Attach To Post TinyMCE i18n strings
	 *
	 * @param $mce_translation
	 * @return mixed
	 */
	public function add_attach_to_post_tinymce_i18n( $mce_translation ) {
		$mce_translation['ngg_attach_to_post.title'] = \__( 'Attach NextGEN Gallery to Post', 'nggallery' );
		return $mce_translation;
	}
}
