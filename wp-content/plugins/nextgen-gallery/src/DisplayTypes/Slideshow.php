<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DisplayType\Controller as ParentController;

use Imagely\NGG\DataTypes\DisplayedGallery;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Display\View;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Router;

/**
 * @implements \Imagely\NGG\DisplayType\ControllerAbstract
 */
class Slideshow extends ParentController {

	/**
	 * @param DisplayedGallery $displayed_gallery
	 * @return DisplayedGallery
	 */
	public function get_alternative_displayed_gallery( $displayed_gallery ) {
		// Prevent recursive checks for further alternates causing additional modifications to the settings array.
		$id = $displayed_gallery->id();
		if ( ! empty( self::$alternate_displayed_galleries[ $id ] ) ) {
			return self::$alternate_displayed_galleries[ $id ];
		}

		$show = Router::get_instance()->get_parameter( 'show' );

		// Are we to display a different display type?
		if ( ! empty( $show ) && $show !== NGG_BASIC_SLIDESHOW ) {
			$params = (array) $displayed_gallery->get_entity();
			unset( $params['id'] );
			unset( $params['ID'] );

			$ds = $params['display_settings'];

			if ( ( ! empty( $ds['show_slideshow_link'] ) || ! empty( $ds['show_thumbnail_link'] ) ) ) {
				return $this->set_alternative_displayed_gallery( $params, $displayed_gallery, $show );
			}
		}

		return $displayed_gallery;
	}

	/**
	 * @param DisplayedGallery $displayed_gallery
	 * @param bool             $return (optional)
	 * @return string
	 */
	public function index_action( $displayed_gallery, $return = false ) {
		$router = Router::get_instance();

		// We now hide option for triggers on this display type. This ensures they do not show based on past settings.
		$displayed_gallery->display_settings['ngg_triggers_display'] = 'never';

		// Get the images to be displayed.
		$current_page = (int) $router->get_parameter( 'nggpage', 1 );

		if ( ( $images = $displayed_gallery->get_included_entities() ) ) {
			// Get the gallery storage component.
			$storage = StorageManager::get_instance();

			// Create parameter list for the view.
			$params                         = $displayed_gallery->display_settings;
			$params['storage']              = $storage;
			$params['images']               = $images;
			$params['displayed_gallery_id'] = $displayed_gallery->id();
			$params['current_page']         = $current_page;
			$params['effect_code']          = $this->get_effect_code( $displayed_gallery );
			$params['anchor']               = 'ngg-slideshow-' . $displayed_gallery->id() . '-' . rand( 1, getrandmax() ) . $current_page;
			$params['placeholder']          = StaticAssets::get_url( 'Slideshow/placeholder.gif', 'photocrati-nextgen_basic_gallery#slideshow/placeholder.gif' );

			// This was not set correctly in previous versions.
			if ( empty( $params['cycle_effect'] ) ) {
				$params['cycle_effect'] = 'fade';
			}

			// Are we to generate a thumbnail link?
			if ( $displayed_gallery->display_settings['show_thumbnail_link'] ) {
				$params['thumbnail_link'] = $this->get_url_for_alternate_display_type( $displayed_gallery, NGG_BASIC_THUMBNAILS );
			}

			$params = $this->prepare_display_parameters( $displayed_gallery, $params );

			$view = new View(
				'Slideshow/index',
				$params,
				'photocrati-nextgen_basic_gallery#slideshow/index'
			);
		} else {
			// No images found.
			$view = new View(
				'GalleryDisplay/NoImagesFound',
				[],
				'photocrati-nextgen_gallery_display#no_images_found'
			);

		}

		return $view->render( $return );
	}

	/**
	 * @param DisplayedGallery $displayed_gallery
	 */
	public function enqueue_frontend_resources( $displayed_gallery ) {
		parent::enqueue_frontend_resources( $displayed_gallery );

		wp_enqueue_style(
			'ngg_basic_slideshow_style',
			StaticAssets::get_url( 'Slideshow/ngg_basic_slideshow.css', 'photocrati-nextgen_basic_gallery#slideshow/ngg_basic_slideshow.css' ),
			[],
			NGG_SCRIPT_VERSION
		);

		wp_enqueue_style(
			'ngg_slick_slideshow_style',
			StaticAssets::get_url( 'Slideshow/slick/slick.css', 'photocrati-nextgen_basic_gallery#slideshow/slick/slick.css' ),
			[],
			NGG_SCRIPT_VERSION
		);

		wp_enqueue_style(
			'ngg_slick_slideshow_theme',
			StaticAssets::get_url( 'Slideshow/slick/slick-theme.css', 'photocrati-nextgen_basic_gallery#slideshow/slick/slick-theme.css' ),
			[],
			NGG_SCRIPT_VERSION
		);

		wp_register_script(
			'ngg_slick',
			StaticAssets::get_url( 'Slideshow/slick/slick-1.8.0-modded.js', 'photocrati-nextgen_basic_gallery#slideshow/slick/slick-1.8.0-modded.js' ),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION
		);

		wp_enqueue_script(
			'ngg_basic_slideshow_script',
			StaticAssets::get_url( 'Slideshow/ngg_basic_slideshow.js', 'photocrati-nextgen_basic_gallery#slideshow/ngg_basic_slideshow.js' ),
			[ 'ngg_slick' ],
			NGG_SCRIPT_VERSION,
			true
		);
	}

	public function get_preview_image_url() {
		return StaticAssets::get_url( 'Slideshow/slideshow_preview.jpg' );
	}

	public function get_template_directory_name(): string {
		return 'Slideshow';
	}

	public function get_default_settings() {
		$settings = Settings::get_instance();
		return \apply_filters(
			'ngg_slideshow_default_settings',
			[
				'gallery_width'        => $settings->get( 'irWidth' ),
				'gallery_height'       => $settings->get( 'irHeight' ),
				'show_thumbnail_link'  => $settings->get( 'galShowSlide' ) ? 1 : 0,
				'thumbnail_link_text'  => $settings->get( 'galTextGallery' ),
				'template'             => '',
				'display_view'         => 'default',
				'autoplay'             => 1,
				'pauseonhover'         => 1,
				'arrows'               => 0,
				'interval'             => 3000,
				'transition_speed'     => 300,
				'transition_style'     => 'fade',
				'ngg_triggers_display' => 'never',
			]
		);
	}

	public function install( $reset = false ) {
		$this->install_display_type(
			NGG_BASIC_SLIDESHOW,
			[
				'title'          => __( 'NextGEN Basic Slideshow', 'nggallery' ),
				'entity_types'   => [ 'image' ],
				'default_source' => 'galleries',
				'view_order'     => NGG_DISPLAY_PRIORITY_BASE + 10,
				'aliases'        => [
					'basic_slideshow',
					'nextgen_basic_slideshow',
					'photocrati-nextgen_basic_slideshow',
				],
				'settings'       => $this->get_default_settings(),
			],
			$reset
		);
	}
}
