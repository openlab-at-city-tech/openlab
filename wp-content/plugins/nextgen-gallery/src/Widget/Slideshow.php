<?php

namespace Imagely\NGG\Widget;

use Imagely\NGG\DisplayTypes\Slideshow as SlideshowController;

use Imagely\NGG\DataTypes\DisplayedGallery;
use Imagely\NGG\Display\DisplayManager;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Display\StaticPopeAssets;
use Imagely\NGG\Display\View;
use Imagely\NGG\DisplayedGallery\Renderer;

class Slideshow extends \WP_Widget {

	protected static $displayed_gallery_ids = [];

	public function __construct() {
		$widget_ops = [
			'classname'   => 'widget_slideshow',
			'description' => \__( 'Show a NextGEN Gallery Slideshow', 'nggallery' ),
		];

		parent::__construct( 'slideshow', \__( 'NextGEN Slideshow', 'nggallery' ), $widget_ops );

		// Determine what widgets will exist in the future, create their displayed galleries, enqueue their resources,
		// and cache the resulting displayed gallery for later rendering to avoid the ID changing due to misc attributes
		// in $args being different now and at render time ($args is sidebar information that is not relevant).
		\add_action(
			'wp_enqueue_scripts',
			function () {

				// Prevent enqueueing resources if the widget is not in use.
				if ( ! is_active_widget( false, false, 'slideshow', true ) ) {
					return;
				}

				global $wp_registered_sidebars;

				$sidebars = \wp_get_sidebars_widgets();
				$options  = $this->get_settings();

				foreach ( $sidebars as $sidebar_name => $sidebar ) {
					if ( 'wp_inactive_widgets' === $sidebar_name || ! $sidebar ) {
						continue;
					}
					foreach ( $sidebar as $widget ) {
						if ( \strpos( $widget, 'slideshow-', 0 ) !== 0 ) {
							continue;
						}
						$id = \str_replace( 'slideshow-', '', $widget );

						if ( isset( $options[ $id ] ) ) {
							$sidebar_data              = $wp_registered_sidebars[ $sidebar_name ];
							$sidebar_data['widget_id'] = $widget;

							// These are normally replaced at display time but we're building our cache before then.
							$sidebar_data['before_widget'] = \str_replace( '%1$s', $widget, $sidebar_data['before_widget'] );
							$sidebar_data['before_widget'] = \str_replace( '%2$s', 'widget_slideshow', $sidebar_data['before_widget'] );
							$sidebar_data['widget_name']   = \__( 'NextGEN Slideshow', 'nggallery' );

							$displayed_gallery = $this->get_displayed_gallery( $sidebar_data, $options[ $id ] );

							self::$displayed_gallery_ids[ $widget ] = $displayed_gallery;

							$controller = new SlideshowController();
							DisplayManager::enqueue_frontend_resources_for_displayed_gallery( $displayed_gallery, $controller );
						}
					}
				}

				\wp_enqueue_style(
					'nextgen_widgets_style',
					StaticAssets::get_url( 'Widget/display.css', 'photocrati-widget#widgets.css' ),
					[],
					NGG_SCRIPT_VERSION
				);
				\wp_enqueue_style(
					'nextgen_basic_slideshow_style',
					StaticPopeAssets::get_url( 'Slideshow/ngg_basic_slideshow.css', 'photocrati-nextgen_basic_gallery#slideshow/ngg_basic_slideshow.css' ),
					[],
					NGG_SCRIPT_VERSION
				);
			},
			11
		);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 * @return DisplayedGallery $displayed_gallery
	 */
	public function get_displayed_gallery( $args, $instance ) {
		if ( empty( $instance['limit'] ) ) {
			$instance['limit'] = 10;
		}

		$params = [
			'container_ids'           => $instance['galleryid'],
			'display_type'            => 'photocrati-nextgen_basic_slideshow',
			'gallery_width'           => $instance['width'],
			'gallery_height'          => $instance['height'],
			'source'                  => 'galleries',
			'slug'                    => 'widget-' . $args['widget_id'],
			'entity_types'            => [ 'image' ],
			'show_thumbnail_link'     => false,
			'show_slideshow_link'     => false,
			'use_imagebrowser_effect' => false, // just to be safe.
			'ngg_triggers_display'    => 'never',
		];

		if ( 0 === $instance['galleryid'] ) {
			$params['source']               = 'random_images';
			$params['maximum_entity_count'] = $instance['limit'];
			unset( $params['container_ids'] );
		}

		$displayed_gallery = Renderer::get_instance()->params_to_displayed_gallery( $params );
		if ( is_null( $displayed_gallery->id() ) ) {
			$displayed_gallery->id( \md5( \json_encode( $displayed_gallery->get_entity() ) ) );
		}

		return $displayed_gallery;
	}

	/**
	 * @param array $instance
	 */
	public function form( $instance ) {
		global $wpdb;

		// Default settings.
		$instance = \wp_parse_args(
			(array) $instance,
			[
				'galleryid' => '0',
				'height'    => '120',
				'title'     => 'Slideshow',
				'width'     => '160',
				'limit'     => '10',
			]
		);

		$view = new View(
			'Widget/Form/Slideshow',
			[
				'self'     => $this,
				'instance' => $instance,
				'title'    => \esc_attr( $instance['title'] ),
				'height'   => \esc_attr( $instance['height'] ),
				'width'    => \esc_attr( $instance['width'] ),
				'limit'    => \esc_attr( $instance['limit'] ),
				'tables'   => $wpdb->get_results( "SELECT * FROM {$wpdb->nggallery} ORDER BY 'name' ASC" ),
			],
			'photocrati-widget#form_slideshow'
		);

		return $view->render();
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$nh = $new_instance['height'];
		$nw = $new_instance['width'];
		if ( empty( $nh ) || 0 === (int) $nh ) {
			$new_instance['height'] = 120;
		}
		if ( empty( $nw ) || 0 === (int) $nw ) {
			$new_instance['width'] = 160;
		}
		if ( empty( $new_instance['limit'] ) ) {
			$new_instance['limit'] = 10;
		}

		$instance              = $old_instance;
		$instance['title']     = \strip_tags( $new_instance['title'] );
		$instance['galleryid'] = (int) $new_instance['galleryid'];
		$instance['height']    = (int) $new_instance['height'];
		$instance['width']     = (int) $new_instance['width'];
		$instance['limit']     = (int) $new_instance['limit'];

		return $instance;
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// these are handled by extract() but I want to silence my IDE warnings that these vars don't exist.
		$before_widget = null;
		$before_title  = null;
		$after_widget  = null;
		$after_title   = null;
		$widget_id     = null;

		\extract( $args );

		$title = \apply_filters(
			'widget_title',
			empty( $instance['title'] ) ? \__( 'Slideshow', 'nggallery' ) : $instance['title'],
			$instance,
			$this->id_base
		);

		$view = new View(
			'Widget/Display/Slideshow',
			[
				'self'          => $this,
				'instance'      => $instance,
				'title'         => $title,
				'out'           => $this->render_slideshow( $args, $instance ),
				'before_widget' => $before_widget,
				'before_title'  => $before_title,
				'after_widget'  => $after_widget,
				'after_title'   => $after_title,
				'widget_id'     => $widget_id,
			],
			'photocrati-widget#display_slideshow'
		);

		$view->render();
	}

	/**
	 * @param $args
	 * @param $instance
	 * @return string
	 */
	public function render_slideshow( $args, $instance ) {
		// This displayed gallery is created dynamically at runtime.
		if ( empty( self::$displayed_gallery_ids[ $args['widget_id'] ] ) ) {
			$displayed_gallery                                       = $this->get_displayed_gallery( $args, $instance );
			self::$displayed_gallery_ids[ $displayed_gallery->id() ] = $displayed_gallery;
		} else {
			// The displayed gallery was created during the action wp_enqueue_resources and was cached to avoid ID conflicts.
			$displayed_gallery = self::$displayed_gallery_ids[ $args['widget_id'] ];
		}

		return \apply_filters(
			'ngg_show_slideshow_widget_content',
			Renderer::get_instance()->display_images( $displayed_gallery ),
			$instance['galleryid'],
			$instance['width'],
			$instance['height']
		);
	}
}
