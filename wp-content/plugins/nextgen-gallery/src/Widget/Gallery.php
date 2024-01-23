<?php

namespace Imagely\NGG\Widget;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;

use Imagely\NGG\DisplayTypes\Thumbnails;
use Imagely\NGG\Display\DisplayManager;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Display\StaticPopeAssets;
use Imagely\NGG\Display\View;
use Imagely\NGG\DisplayedGallery\Renderer;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Transient;

class Gallery extends \WP_Widget {

	protected static $displayed_gallery_ids = [];

	public function __construct() {
		$widget_ops = [
			'classname'   => 'ngg_images',
			'description' => \__( 'Add recent or random images from the galleries', 'nggallery' ),
		];

		parent::__construct( 'ngg-images', \__( 'NextGEN Widget', 'nggallery' ), $widget_ops );

		// Determine what widgets will exist in the future, create their displayed galleries, enqueue their resources,
		// and cache the resulting displayed gallery for later rendering to avoid the ID changing due to misc attributes
		// in $args being different now and at render time ($args is sidebar information that is not relevant).
		\add_action(
			'wp_enqueue_scripts',
			function () {

				// Prevent enqueueing resources if the widget is not in use.
				if ( ! is_active_widget( false, false, 'ngg-images', true ) ) {
					return;
				}

				global $wp_registered_sidebars;

				$sidebars = \wp_get_sidebars_widgets();
				$options  = $this->get_settings();

				foreach ( $sidebars as $sidebar_name => $sidebar ) {
					if ( $sidebar_name === 'wp_inactive_widgets' || ! $sidebar ) {
						continue;
					}
					foreach ( $sidebar as $widget ) {
						if ( \strpos( $widget, 'ngg-images-', 0 ) !== 0 ) {
							continue;
						}
						$id = \str_replace( 'ngg-images-', '', $widget );
						if ( isset( $options[ $id ] ) ) {
							$sidebar_data              = $wp_registered_sidebars[ $sidebar_name ];
							$sidebar_data['widget_id'] = $widget;

							// These are normally replaced at display time but we're building our cache before then.
							$sidebar_data['before_widget'] = \str_replace( '%1$s', $widget, $sidebar_data['before_widget'] );
							$sidebar_data['before_widget'] = \str_replace( '%2$s', 'ngg_images', $sidebar_data['before_widget'] );
							$sidebar_data['widget_name']   = \__( 'NextGEN Widget', 'nggallery' );

							$displayed_gallery = $this->get_displayed_gallery( $sidebar_data, $options[ $id ] );

							self::$displayed_gallery_ids[ $widget ] = $displayed_gallery;

							$controller = new Thumbnails();
							DisplayManager::enqueue_frontend_resources_for_displayed_gallery( $displayed_gallery, $controller );
						}
					}
				}

				// Now enqueue the basic styling for the display itself.
				\wp_enqueue_style(
					'nextgen_widgets_style',
					StaticAssets::get_url( 'Widget/display.css', 'photocrati-widget#widgets.css' ),
					[],
					NGG_SCRIPT_VERSION
				);

				\wp_enqueue_style(
					'nextgen_basic_thumbnails_style',
					StaticPopeAssets::get_url( 'Thumbnails/nextgen_basic_thumbnails.css', 'photocrati-nextgen_basic_gallery#thumbnails/nextgen_basic_thumbnails.css' ),
					[],
					NGG_SCRIPT_VERSION
				);
			},
			11
		);
		// It is important that this run at priority 11 or higher so that M_Gallery_Display->enqueue_frontend_resources() has run first.
	}

	/**
	 * @return array
	 */
	public function get_defaults() {
		return [
			'exclude'  => 'all',
			'height'   => '75',
			'items'    => '4',
			'list'     => '',
			'show'     => 'thumbnail',
			'title'    => 'Gallery',
			'type'     => 'recent',
			'webslice' => true,
			'width'    => '100',
		];
	}

	/**
	 * @param array $instance
	 */
	public function form( $instance ) {
		// defaults.
		$instance = \wp_parse_args( (array) $instance, $this->get_defaults() );

		$view = new View(
			'Widget/Form/Gallery',
			[
				'self'     => $this,
				'instance' => $instance,
				'title'    => \esc_attr( $instance['title'] ),
				'items'    => \intval( $instance['items'] ),
				'height'   => \esc_attr( $instance['height'] ),
				'width'    => \esc_attr( $instance['width'] ),
			],
			'photocrati-widget#form_gallery'
		);

		return $view->render();
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// do not allow 0 or less.
		if ( (int) $new_instance['items'] <= 0 ) {
			$new_instance['items'] = 4;
		}

		// for clarity: empty the list if we're showing every gallery anyway.
		if ( $new_instance['exclude'] == 'all' ) {
			$new_instance['list'] = '';
		}

		// remove gallery ids that do not exist.
		if ( \in_array( $new_instance['exclude'], [ 'denied', 'allow' ] ) ) {
			// do search.
			$mapper = GalleryMapper::get_instance();
			$ids    = \explode( ',', $new_instance['list'] );
			foreach ( $ids as $ndx => $id ) {
				if ( ! $mapper->find( $id ) ) {
					unset( $ids[ $ndx ] );
				}
			}
			$new_instance['list'] = \implode( ',', $ids );
		}

		// reset to show all galleries IF there are no valid galleries in the list.
		if ( $new_instance['exclude'] !== 'all' && empty( $new_instance['list'] ) ) {
			$new_instance['exclude'] = 'all';
		}

		$instance['title']    = \strip_tags( $new_instance['title'] );
		$instance['items']    = (int) $new_instance['items'];
		$instance['type']     = $new_instance['type'];
		$instance['show']     = $new_instance['show'];
		$instance['width']    = (int) $new_instance['width'];
		$instance['height']   = (int) $new_instance['height'];
		$instance['exclude']  = $new_instance['exclude'];
		$instance['list']     = $new_instance['list'];
		$instance['webslice'] = (bool) $new_instance['webslice'];

		return $instance;
	}

	/**
	 * @param array $args
	 * @param array $instance
	 * @return \Imagely\NGG\DataTypes\DisplayedGallery $displayed_gallery
	 */
	public function get_displayed_gallery( $args, $instance ) {
		$settings = Settings::get_instance();

		// these are handled by extract() but I want to silence my IDE warnings that these vars don't exist.
		$before_widget = null;
		$before_title  = null;
		$after_widget  = null;
		$after_title   = null;
		$widget_id     = null;
		\extract( $args );

		$title = \apply_filters( 'widget_title', empty( $instance['title'] ) ? '&nbsp;' : $instance['title'], $instance, $this->id_base );

		// Used later.
		$renderer = Renderer::get_instance();

		$view = new View( 'Widget/Display/Gallery', [], 'photocrati-widget#display_gallery' );

		// IE8 webslice support if needed.
		if ( ! empty( $instance['webslice'] ) ) {
			$before_widget .= '<div class="hslice" id="ngg-webslice">';
			$before_title   = \str_replace( 'class="', 'class="entry-title ', $before_title );
			$after_widget   = '</div>' . $after_widget;
		}

		$source   = ( $instance['type'] == 'random' ? 'random_images' : 'recent' );
		$template = ! empty( $instance['template'] ) ? $instance['template'] : $view->find_template_abspath( 'Widget/Display/Gallery', 'photocrati-widget#display_gallery' );

		$params = [
			'slug'                         => 'widget-' . $args['widget_id'],
			'source'                       => $source,
			'display_type'                 => NGG_BASIC_THUMBNAILS,
			'images_per_page'              => $instance['items'],
			'maximum_entity_count'         => $instance['items'],
			'template'                     => $template,
			'image_type'                   => $instance['show'] == 'original' ? 'full' : 'thumb',
			'show_all_in_lightbox'         => false,
			'show_slideshow_link'          => false,
			'show_thumbnail_link'          => false,
			'use_imagebrowser_effect'      => false,
			'disable_pagination'           => true,
			'image_width'                  => $instance['width'],
			'image_height'                 => $instance['height'],
			'ngg_triggers_display'         => 'never',
			'widget_setting_title'         => $title,
			'widget_setting_before_widget' => $before_widget,
			'widget_setting_before_title'  => $before_title,
			'widget_setting_after_widget'  => $after_widget,
			'widget_setting_after_title'   => $after_title,
			'widget_setting_width'         => $instance['width'],
			'widget_setting_height'        => $instance['height'],
			'widget_setting_show_setting'  => $instance['show'],
			'widget_setting_widget_id'     => $widget_id,
		];

		switch ( $instance['exclude'] ) {
			case 'all':
				break;
			case 'denied':
				$mapper      = GalleryMapper::get_instance();
				$gallery_ids = [];
				$list        = \explode( ',', $instance['list'] );
				foreach ( $mapper->find_all() as $gallery ) {
					if ( ! \in_array( $gallery->{$gallery->id_field}, $list ) ) {
						$gallery_ids[] = $gallery->{$gallery->id_field};
					}
				}
				$params['container_ids'] = \implode( ',', $gallery_ids );
				break;
			case 'allow':
				$params['container_ids'] = $instance['list'];
				break;
		}

		// "Random" galleries are a bit resource intensive when querying the database and widgets are generally
		// going to be on every page a site may serve. Because the displayed gallery renderer does *NOT* cache the
		// HTML of random galleries the following is a bit of a workaround: for random widgets we create a displayed
		// gallery object and then cache the results of get_entities() so that, for at least as long as
		// NGG_RENDERING_CACHE_TTL seconds, widgets will be temporarily cached.
		if ( \in_array( $params['source'], [ 'random', 'random_images' ] )
		&& (float) $settings->get( 'random_widget_cache_ttl' ) > 0 ) {
			$displayed_gallery = $renderer->params_to_displayed_gallery( $params );
			if ( \is_null( $displayed_gallery->id() ) ) {
				$displayed_gallery->id( \md5( \json_encode( $displayed_gallery->get_entity() ) ) );
			}

			$cache_group  = 'random_widget_gallery_ids';
			$cache_params = [ $displayed_gallery->get_entity() ];
			$transientM   = Transient::get_instance();
			$key          = $transientM->generate_key( $cache_group, $cache_params );
			$ids          = $transientM->get( $key, false );

			if ( ! empty( $ids ) ) {
				$params['image_ids'] = $ids;
			} else {
				$ids = [];
				foreach ( $displayed_gallery->get_entities( $instance['items'], false, true ) as $item ) {
					$ids[] = $item->{$item->id_field};
				}
				$params['image_ids'] = \implode( ',', $ids );
				$transientM->set( $key, $params['image_ids'], ( (float) $settings->random_widget_cache_ttl * 60 ) );
			}

			$params['source'] = 'images';
			unset( $params['container_ids'] );
		}

		$final_displayed_gallery = $renderer->params_to_displayed_gallery( $params );
		if ( is_null( $final_displayed_gallery->id() ) ) {
			$final_displayed_gallery->id( \md5( \json_encode( $final_displayed_gallery->get_entity() ) ) );
		}

		return $final_displayed_gallery;
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// This displayed gallery is created dynamically at runtime.
		if ( empty( self::$displayed_gallery_ids[ $args['widget_id'] ] ) ) {
			$displayed_gallery                                       = $this->get_displayed_gallery( $args, $instance );
			self::$displayed_gallery_ids[ $displayed_gallery->id() ] = $displayed_gallery;
		} else {
			// The displayed gallery was created during the action wp_enqueue_resources and was cached to avoid ID conflicts.
			$displayed_gallery = self::$displayed_gallery_ids[ $args['widget_id'] ];
		}

		print Renderer::get_instance()->display_images( $displayed_gallery );
	}
}
