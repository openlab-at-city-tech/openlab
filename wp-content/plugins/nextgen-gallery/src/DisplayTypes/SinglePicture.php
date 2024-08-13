<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DisplayType\Controller as ParentController;
use Imagely\NGG\DynamicThumbnails\Manager as DynThumbs;

use Imagely\NGG\DataTypes\DisplayedGallery;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Display\View;

class SinglePicture extends ParentController {

	/**
	 * @param DisplayedGallery $displayed_gallery
	 * @param bool             $return (optional)
	 * @return string
	 */
	public function index_action( $displayed_gallery, $return = false ) {
		$storage          = StorageManager::get_instance();
		$dynthumbs        = DynThumbs::get_instance();
		$display_settings = $displayed_gallery->display_settings;

		// use this over get_included_entities() so we can display images marked 'excluded'.
		$displayed_gallery->skip_excluding_globally_excluded_images = true;

		$entities = $displayed_gallery->get_entities( 1, false, false, 'included' );
		$image    = array_shift( $entities );

		if ( ! $image ) {
			$view = new View( 'GalleryDisplay/NoImagesFound', [], 'photocrati-nextgen_gallery_display#no_images_found' );
			return $view->render( $return );
		}

		switch ( $display_settings['float'] ) {
			case 'left':
				$display_settings['float'] = 'ngg-left';
				break;
			case 'right':
				$display_settings['float'] = 'ngg-right';
				break;
			case 'center':
				$display_settings['float'] = 'ngg-center';
				break;
			default:
				$display_settings['float'] = '';
				break;
		}

		$params = [];

		if ( ! empty( $display_settings['link'] ) ) {
			$target      = $display_settings['link_target'];
			$effect_code = '';
		} else {
			$display_settings['link'] = $storage->get_image_url( $image, 'full', true );
			$target                   = '_self';
			$effect_code              = $this->get_effect_code( $displayed_gallery );
		}
		$params['target'] = $target;

		// mode is a legacy parameter.
		if ( ! is_array( $display_settings['mode'] ) ) {
			$display_settings['mode'] = explode( ',', $display_settings['mode'] );
		}
		if ( in_array( 'web20', $display_settings['mode'] ) ) {
			$display_settings['display_reflection'] = true;
		}
		if ( in_array( 'watermark', $display_settings['mode'] ) ) {
			$display_settings['display_watermark'] = true;
		}

		if ( isset( $display_settings['w'] ) ) {
			$display_settings['width'] = $display_settings['w'];
		} elseif ( isset( $display_settings['h'] ) ) {
			unset( $display_settings['width'] );
		}

		if ( isset( $display_settings['h'] ) ) {
			$display_settings['height'] = $display_settings['h'];
		} elseif ( isset( $display_settings['w'] ) ) {
			unset( $display_settings['height'] );
		}

		// legacy assumed no width/height meant full size unlike generate_thumbnail: force a full resolution.
		if ( ! isset( $display_settings['width'] ) && ! isset( $display_settings['height'] ) ) {
			$display_settings['width'] = $image->meta_data['width'];
		}

		if ( isset( $display_settings['width'] ) ) {
				$params['width'] = $display_settings['width'];
		}

		if ( isset( $display_settings['height'] ) ) {
			$params['height'] = $display_settings['height'];
		}

		$params['quality']    = $display_settings['quality'];
		$params['crop']       = $display_settings['crop'];
		$params['watermark']  = $display_settings['display_watermark'];
		$params['reflection'] = $display_settings['display_reflection'];

		$size = $dynthumbs->get_size_name( $params );

		$thumbnail_url = $storage->get_image_url( $image, $size );

		if ( ! empty( $display_settings['template'] ) && $display_settings['template'] != 'default' ) {
			$params = $this->prepare_legacy_parameters( [ $image ], $displayed_gallery, [ 'single_image' => true ] );

			// the wrapper is a lazy-loader that calculates variables when requested. We here override those to always
			// return the same precalculated settings provided.
			$params['image']->container[0]->_cache_overrides['caption']      = $displayed_gallery->inner_content;
			$params['image']->container[0]->_cache_overrides['classname']    = 'ngg-singlepic ' . $display_settings['float'];
			$params['image']->container[0]->_cache_overrides['imageURL']     = $display_settings['link'];
			$params['image']->container[0]->_cache_overrides['thumbnailURL'] = $thumbnail_url;
			$params['target'] = $target;

			// if a link is present we temporarily must filter out the effect code.
			if ( empty( $effect_code ) ) {
				add_filter( 'ngg_get_thumbcode', [ $this, 'strip_thumbcode' ], 10 );
			}

			$retval = $this->legacy_render( $display_settings['template'], $params, $return, 'singlepic' );

			if ( empty( $effect_code ) ) {
				remove_filter( 'ngg_get_thumbcode', [ $this, 'strip_thumbcode' ], 10 );
			}

			return $retval;
		} else {
			$params                  = $display_settings;
			$params['storage']       = &$storage;
			$params['image']         = &$image;
			$params['effect_code']   = $effect_code;
			$params['inner_content'] = $displayed_gallery->inner_content;
			$params['settings']      = $display_settings;
			$params['thumbnail_url'] = $thumbnail_url;
			$params['target']        = $target;

			$params = $this->prepare_display_parameters( $displayed_gallery, $params );

			$view = new View(
				'SinglePicture/nextgen_basic_singlepic',
				$params,
				'photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic'
			);

			return $view->render( $return );
		}
	}

	/**
	 * The IGW popup requires substantial changes to include this display type.
	 *
	 * @return true
	 */
	public function is_hidden_from_igw() {
		return true;
	}

	/**
	 * Intentionally disable the application of the effect code
	 *
	 * @param string $thumbcode Unused
	 * @return string
	 */
	public function strip_thumbcode( $thumbcode ) {
		return '';
	}

	/**
	 * Enqueues all static resources required by this display type
	 *
	 * @param DisplayedGallery $displayed_gallery
	 */
	public function enqueue_frontend_resources( $displayed_gallery ) {
		parent::enqueue_frontend_resources( $displayed_gallery );

		wp_enqueue_style(
			'nextgen_basic_singlepic_style',
			StaticAssets::get_url( 'SinglePicture/nextgen_basic_singlepic.css', 'photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic.css' ),
			[],
			NGG_SCRIPT_VERSION
		);
	}

	public function get_preview_image_url() {
		return StaticAssets::get_url( 'SinglePicture/preview.gif' );
	}

	public function get_template_directory_name(): string {
		return 'SinglePicture';
	}

	public function get_default_settings() {
		return \apply_filters(
			'ngg_single_picture_default_settings',
			[
				'crop'               => 0,
				'display_reflection' => 0,
				'display_watermark'  => 0,
				'float'              => '',
				'height'             => '',
				'link'               => '',
				'mode'               => '',
				'quality'            => 100,
				'width'              => '',
				'link_target'        => '_blank',
				'template'           => '',

				'ngg_triggers_display',
				'never',
			]
		);
	}

	public function install( $reset = false ) {
		$this->install_display_type(
			NGG_BASIC_SINGLEPIC,
			[
				'title'           => __( 'NextGEN Basic SinglePic', 'nggallery' ),
				'entity_types'    => [ 'image' ],
				'default_source'  => 'galleries',
				'view_order'      => NGG_DISPLAY_PRIORITY_BASE + 60,
				'hidden_from_ui'  => true, // TODO remove this, use hidden_from_igw instead.
				'hidden_from_igw' => true,
				'aliases'         => [
					'singlepic',
					'singlepicture',
					'basic_singlepic',
					'nextgen_basic_singlepic',
					'photocrati-nextgen_basic_singlepic',
				],
				'settings'        => $this->get_default_settings(),
			],
			$reset
		);
	}
}
