<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DisplayType\Controller as ParentController;
use Imagely\NGG\DynamicThumbnails\Manager as ThumbnailsManager;

use Imagely\NGG\DataTypes\DisplayedGallery;
use Imagely\NGG\Display\{StaticAssets, View};
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Router;

class Thumbnails extends ParentController {

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

		$router = Router::get_instance();

		$show = $router->get_parameter( 'show' );
		$pid  = $router->get_parameter( 'pid' );

		if ( ! empty( $pid ) && isset( $displayed_gallery->display_settings['use_imagebrowser_effect'] ) && intval( $displayed_gallery->display_settings['use_imagebrowser_effect'] ) ) {
			$show = NGG_BASIC_IMAGEBROWSER;
		}

		// Are we to display a different display type?
		if ( ! empty( $show ) && $show !== NGG_BASIC_THUMBNAILS ) {
			$params = (array) $displayed_gallery->get_entity();
			unset( $params['id'] );
			unset( $params['ID'] );

			$ds = $params['display_settings'];

			if ( ( ! empty( $ds['show_slideshow_link'] ) || ! empty( $ds['show_thumbnail_link'] ) || ! empty( $ds['use_imagebrowser_effect'] ) ) ) {
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

		$storage   = StorageManager::get_instance();
		$dynthumbs = ThumbnailsManager::get_instance();

		$display_settings = $displayed_gallery->display_settings;
		$gallery_id       = $displayed_gallery->id();

		if ( ! $display_settings['disable_pagination'] ) {
			$current_page = (int) $router->get_parameter( 'nggpage', $gallery_id, 1 );
		} else {
			$current_page = 1;
		}

		$offset = $display_settings['images_per_page'] * ( $current_page - 1 );
		$total  = $displayed_gallery->get_entity_count();

		// Get the images to be displayed.
		if ( $display_settings['images_per_page'] > 0 && $display_settings['show_all_in_lightbox'] ) {
			// the "Add Hidden Images" feature works by loading ALL images and then marking the ones not on this page
			// as hidden (style="display: none").
			$images = $displayed_gallery->get_included_entities();
			$i      = 0;
			foreach ( $images as &$image ) {
				if ( $i < $display_settings['images_per_page'] * ( $current_page - 1 ) ) {
					$image->hidden = true;
				} elseif ( $i >= $display_settings['images_per_page'] * ( $current_page ) ) {
					$image->hidden = true;
				}
				++$i;
			}
		} else {
			// just display the images for this page, as normal.
			$images = $displayed_gallery->get_included_entities( $display_settings['images_per_page'], $offset );
		}

		// Are there images to display?.
		if ( $images ) {
			// Create pagination.
			if ( $display_settings['images_per_page'] && ! $display_settings['disable_pagination'] ) {
				$pagination_result = $this->create_pagination(
					$current_page,
					$total,
					$display_settings['images_per_page'],
					urldecode( $router->get_parameter( 'ajax_pagination_referrer' ) ?: '' )
				);
				$app               = $router->get_routed_app();
				$app->remove_parameter( 'ajax_pagination_referrer' );
				$pagination_prev = $pagination_result['prev'];
				$pagination_next = $pagination_result['next'];
				$pagination      = $pagination_result['output'];
			} else {
				list($pagination_prev, $pagination_next, $pagination) = [ null, null, null ];
			}

			$thumbnail_size_name = 'thumbnail';

			if ( $display_settings['override_thumbnail_settings'] ) {
				if ( $dynthumbs != null ) {
					$dyn_params = [
						'width'  => $display_settings['thumbnail_width'],
						'height' => $display_settings['thumbnail_height'],
					];

					if ( $display_settings['thumbnail_quality'] ) {
						$dyn_params['quality'] = $display_settings['thumbnail_quality'];
					}

					if ( $display_settings['thumbnail_crop'] ) {
						$dyn_params['crop'] = true;
					}

					if ( $display_settings['thumbnail_watermark'] ) {
						$dyn_params['watermark'] = true;
					}

					$thumbnail_size_name = $dynthumbs->get_size_name( $dyn_params );
				}
			}

			// Generate a slideshow link.
			$slideshow_link = '';
			if ( $display_settings['show_slideshow_link'] ) {
				// origin_url is necessary for ajax operations. slideshow_link_origin will NOT always exist.
				$origin_url     = $router->get_parameter( 'ajax_pagination_referrer' );
				$slideshow_link = $this->get_url_for_alternate_display_type(
					$displayed_gallery,
					NGG_BASIC_SLIDESHOW,
					$origin_url
				);
			}

			// This setting 1) points all images to an imagebrowser display & 2) disables the lightbox effect.
			if ( $display_settings['use_imagebrowser_effect'] ) {
				if ( ! empty( $displayed_gallery->display_settings['original_display_type'] )
				&& ! empty( $_SERVER['NGG_ORIG_REQUEST_URI'] ) ) {
					$origin_url = $_SERVER['NGG_ORIG_REQUEST_URI'];
				}

				$app = $router->get_routed_app();
				$url = ( ! empty( $origin_url ) ? $origin_url : $app->get_routed_url() );
				$url = $app->remove_parameter( $url, null, 'image' );
				$url = $this->set_param_for( $url, 'image', '%STUB%' );

				$effect_code = "class='use_imagebrowser_effect' data-imagebrowser-url='{$url}'";
			} else {
				$effect_code = $this->get_effect_code( $displayed_gallery );
			}

			// The render functions require different processing.
			if ( ! empty( $display_settings['template'] ) && $display_settings['template'] != 'default' ) {
				$params = $this->prepare_legacy_parameters(
					$images,
					$displayed_gallery,
					[
						'next'           => ( empty( $pagination_next ) ) ? false : $pagination_next,
						'prev'           => ( empty( $pagination_prev ) ) ? false : $pagination_prev,
						'pagination'     => $pagination,
						'slideshow_link' => $slideshow_link,
						'effect_code'    => $effect_code,
					]
				);
				return $this->legacy_render( $display_settings['template'], $params, $return, 'gallery' );
			} else {
				$params = $display_settings;

				// Additional values for the carousel display view.
				if ( ! empty( $router->get_parameter( 'pid' ) ) ) {
					foreach ( $images as $image ) {
						if ( $image->image_slug === $router->get_parameter( 'pid' ) ) {
							$params['current_image'] = $image;
						}
					}
					if ( isset( $pagination_result ) ) {
						$params['pagination_prev'] = $pagination_result['prev'];
						$params['pagination_next'] = $pagination_result['next'];
					}
				}
				if ( empty( $params['current_image'] ) ) {
					$params['current_image'] = reset( $images );
				}

				$params['storage']              = $storage;
				$params['images']               = $images;
				$params['displayed_gallery_id'] = $gallery_id;
				$params['current_page']         = $current_page;
				$params['effect_code']          = $effect_code;
				$params['pagination']           = $pagination;
				$params['thumbnail_size_name']  = $thumbnail_size_name;
				$params['slideshow_link']       = $slideshow_link;

				$params = $this->prepare_display_parameters( $displayed_gallery, $params );

				$view = new View(
					'Thumbnails/index',
					$params,
					'photocrati-nextgen_basic_gallery#thumbnails/index'
				);

				return $view->render( $return );
			}
		} elseif ( $display_settings['display_no_images_error'] ) {
			$view = new View(
				'GalleryDisplay/NoImagesFound',
				[],
				'photocrati-nextgen_gallery_display#no_images_found'
			);

			return $view->render( $return );
		}

		return '';
	}

	/**
	 * @param DisplayedGallery $displayed_gallery
	 */
	public function enqueue_frontend_resources( $displayed_gallery ) {
		parent::enqueue_frontend_resources( $displayed_gallery );
		$this->enqueue_pagination_resources();

		\wp_enqueue_style(
			'nextgen_basic_thumbnails_style',
			StaticAssets::get_url( 'Thumbnails/nextgen_basic_thumbnails.css', 'photocrati-nextgen_basic_gallery#thumbnails/nextgen_basic_thumbnails.css' ),
			[],
			NGG_SCRIPT_VERSION
		);

		\wp_enqueue_script(
			'nextgen_basic_thumbnails_script',
			StaticAssets::get_url( 'Thumbnails/nextgen_basic_thumbnails.js', 'photocrati-nextgen_basic_gallery#thumbnails/nextgen_basic_thumbnails.js' ),
			[],
			NGG_SCRIPT_VERSION
		);

		if ( $displayed_gallery->display_settings['ajax_pagination'] ) {
			\wp_enqueue_script(
				'nextgen-basic-thumbnails-ajax-pagination',
				StaticAssets::get_url( 'Thumbnails/ajax_pagination.js', 'photocrati-nextgen_basic_gallery#thumbnails/ajax_pagination.js' ),
				[],
				NGG_SCRIPT_VERSION
			);
		}
	}

	/**
	 * Allows the above imagebrowser-url to return as image/23 instead of image--23
	 *
	 * @param string      $url
	 * @param string      $key
	 * @param mixed       $value
	 * @param null|string $id
	 * @param bool        $use_prefix
	 * @return string
	 */
	public function set_param_for( $url, $key, $value, $id = null, $use_prefix = false ) {
		$app    = Router::get_instance()->get_routed_app();
		$retval = $app->set_parameter( $key, $value, $id, $use_prefix, $url );

		while ( preg_match( '#(image)--([^/]+)#', $retval, $matches ) ) {
			$retval = str_replace( $matches[0], $matches[1] . '/' . $matches[2], $retval );
		}

		return $retval;
	}

	public function get_preview_image_url() {
		return StaticAssets::get_url( 'Thumbnails/thumb_preview.jpg' );
	}

	public function get_default_settings() {
		$settings = Settings::get_instance();

		$default_template = isset( $entity->settings['template'] ) ? 'default' : 'default-view.php';

		return \apply_filters(
			'ngg_thumbnails_default_settings',
			[
				'display_view'                => $default_template,
				'images_per_page'             => $settings->get( 'galImages' ),
				'number_of_columns'           => $settings->get( 'galColumns' ),
				'thumbnail_width'             => $settings->get( 'thumbwidth' ),
				'thumbnail_height'            => $settings->get( 'thumbheight' ),
				'show_all_in_lightbox'        => $settings->get( 'galHiddenImg' ),
				'ajax_pagination'             => $settings->get( 'galAjaxNav' ),
				'use_imagebrowser_effect'     => $settings->get( 'galImgBrowser' ),
				'template'                    => '',
				'display_no_images_error'     => 1,
				'disable_pagination'          => 0,

				// Alternative view support.
				'show_slideshow_link'         => $settings->get( 'galShowSlide' ) ? 1 : 0,
				'slideshow_link_text'         => $settings->get( 'galTextSlide' ),

				// override thumbnail settings.
				'override_thumbnail_settings' => 0,
				'thumbnail_quality'           => '100',
				'thumbnail_crop'              => 1,
				'thumbnail_watermark'         => 0,

				// Part of the pro-modules.
				'ngg_triggers_display'        => 'never',
			]
		);
	}

	public function get_template_directory_name(): string {
		return 'Thumbnails';
	}

	public function install( $reset = false ) {
		$this->install_display_type(
			NGG_BASIC_THUMBNAILS,
			[
				'title'          => __( 'NextGEN Basic Thumbnails', 'nggallery' ),
				'entity_types'   => [ 'image' ],
				'default_source' => 'galleries',
				'view_order'     => NGG_DISPLAY_PRIORITY_BASE,
				'settings'       => $this->get_default_settings(),
				'aliases'        => [
					'basic_thumbnail',
					'basic_thumbnails',
					'nextgen_basic_thumbnails',
					'photocrati-nextgen_basic_thumbnails',
				],
			],
			$reset
		);
	}
}
