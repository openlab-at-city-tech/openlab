<?php

namespace Imagely\NGG\DisplayType;

use Imagely\NGG\DisplayTypes\Taxonomy;
use Imagely\NGG\Display\Shortcodes;
use Imagely\NGG\Settings\Settings;

class Manager {

	public static function register() {
		ControllerFactory::register_controller(
			'photocrati-nextgen_basic_imagebrowser',
			'\Imagely\NGG\DisplayTypes\ImageBrowser',
			[ // Aliases.
				'imagebrowser',
				'basic_imagebrowser',
				'nextgen_basic_imagebrowser',
			]
		);

		ControllerFactory::register_controller(
			'photocrati-nextgen_basic_singlepic',
			'\Imagely\NGG\DisplayTypes\SinglePicture',
			[
				'singlepicture',
				'singlepic',
				'basic_singlepic',
				'nextgen_basic_singlepic',
			]
		);

		ControllerFactory::register_controller(
			'photocrati-nextgen_basic_tagcloud',
			'\Imagely\NGG\DisplayTypes\TagCloud',
			[
				'tagcloud',
				'basic_tagcloud',
				'nextgen_basic_tagcloud',
			]
		);

		ControllerFactory::register_controller(
			'photocrati-nextgen_basic_thumbnails',
			'\Imagely\NGG\DisplayTypes\Thumbnails',
			[
				'basic_thumbnails',
				'nextgen_basic_thumbnails',
			]
		);

		ControllerFactory::register_controller(
			'photocrati-nextgen_basic_slideshow',
			'\Imagely\NGG\DisplayTypes\Slideshow',
			[
				'basic_slideshow',
				'nextgen_basic_slideshow',
			]
		);

		ControllerFactory::register_controller(
			'photocrati-nextgen_basic_compact_album',
			'\Imagely\NGG\DisplayTypes\CompactAlbum',
			[
				'compact_album',
				'basic_album_compact',
				'basic_compact_album',
				'nextgen_basic_album',
			]
		);

		ControllerFactory::register_controller(
			'photocrati-nextgen_basic_extended_album',
			'\Imagely\NGG\DisplayTypes\ExtendedAlbum',
			[
				'extended_album',
				'basic_extended_album',
				'nextgen_basic_extended_album',
			]
		);

		$self = new Manager();
		if ( ! defined( 'NGG_DISABLE_LEGACY_SHORTCODES' ) || ! NGG_DISABLE_LEGACY_SHORTCODES ) {
			Shortcodes::add( 'imagebrowser', null, [ $self, 'render_legacy_imagebrowser_shortcode' ] );
			Shortcodes::add( 'nggimagebrowser', null, [ $self, 'render_legacy_imagebrowser_shortcode' ] );
			Shortcodes::add( 'singlepic', null, [ $self, 'render_legacy_single_picture_shortcode' ] );
			Shortcodes::add( 'nggsinglepic', null, [ $self, 'render_legacy_single_picture_shortcode' ] );
			Shortcodes::add( 'tagcloud', null, [ $self, 'render_legacy_tag_cloud_shortcode' ] );
			Shortcodes::add( 'nggtagcloud', null, [ $self, 'render_legacy_tag_cloud_shortcode' ] );
			Shortcodes::add( 'random', null, [ $self, 'render_legacy_random_images' ] );
			Shortcodes::add( 'recent', null, [ $self, 'render_legacy_recent_images' ] );
			Shortcodes::add( 'thumb', null, [ $self, 'render_legacy_thumb_shortcode' ] );
			Shortcodes::add( 'slideshow', null, [ $self, 'render_legacy_slideshow' ] );
			Shortcodes::add( 'nggallery', null, [ $self, 'render_legacy_nggallery' ] );
			Shortcodes::add( 'nggtags', null, [ $self, 'render_legacy_based_on_tags' ] );
			Shortcodes::add( 'nggslideshow', null, [ $self, 'render_legacy_slideshow' ] );
			Shortcodes::add( 'nggrandom', null, [ $self, 'render_legacy_random_images' ] );
			Shortcodes::add( 'nggrecent', null, [ $self, 'render_legacy_recent_images' ] );
			Shortcodes::add( 'nggthumb', null, [ $self, 'render_legacy_thumb_shortcode' ] );
			Shortcodes::add( 'album', null, [ $self, 'render_legacy_album_shortcode' ] );
			Shortcodes::add( 'nggalbum', null, [ $self, 'render_legacy_album_shortcode' ] );

			if ( ! Settings::get_instance()->get( 'disable_ngg_tags_page', false ) ) {
				\add_filter( 'the_posts', [ Taxonomy::get_instance(), 'detect_ngg_tag' ], -10, 2 );
			}
		}

		\do_action( 'ngg_register_display_types' );

		\add_action( 'ngg_routes', [ $self, 'define_routes' ] );
	}

	public function define_routes( $router ) {
		$slug = '/' . Settings::get_instance()->get( 'router_param_slug', 'nggallery' );

		// ImageBrowser.
		$router->rewrite( "{*}{$slug}{*}/image/{\\w}", "{1}{$slug}{2}/pid--{3}" );

		// TagCloud.
		$router->rewrite( "{*}{$slug}{*}/tags/{\\w}{*}", "{1}{$slug}{2}/gallerytag--{3}{4}" );

		// Thumbnails, Slideshow.
		$router->rewrite( "{*}{$slug}{*}/image/{*}", "{1}{$slug}{2}/pid--{3}" );
		$router->rewrite( "{*}{$slug}{*}/slideshow/{*}", "{1}{$slug}{2}/show--" . NGG_BASIC_SLIDESHOW . '/{3}' );
		$router->rewrite( "{*}{$slug}{*}/thumbnails/{*}", "{1}{$slug}{2}/show--" . NGG_BASIC_THUMBNAILS . '/{3}' );
		$router->rewrite( "{*}{$slug}{*}/show--slide/{*}", "{1}{$slug}{2}/show--" . NGG_BASIC_SLIDESHOW . '/{3}' );
		$router->rewrite( "{*}{$slug}{*}/show--gallery/{*}", "{1}{$slug}{2}/show--" . NGG_BASIC_THUMBNAILS . '/{3}' );
		$router->rewrite( "{*}{$slug}{*}/page/{\\d}{*}", "{1}{$slug}{2}/nggpage--{3}{4}" );
	}

	/**
	 * Gets a value from the parameter array, and if not available, uses the default value
	 *
	 * @param string $name
	 * @param mixed  $default
	 * @param array  $params
	 * @return mixed
	 */
	public function get_param( $name, $default, $params ) {
		return ( isset( $params[ $name ] ) ) ? $params[ $name ] : $default;
	}

	public function render_legacy_album_shortcode( $params ) {
		$params['source']        = $this->get_param( 'source', 'albums', $params );
		$params['container_ids'] = $this->get_param( 'id', null, $params );
		$params['display_type']  = $this->get_param( 'display_type', NGG_BASIC_COMPACT_ALBUM, $params );

		unset( $params['id'] );
		return $params;
	}

	public function render_legacy_imagebrowser_shortcode( $params ) {
		$params['gallery_ids']  = $this->get_param( 'id', null, $params );
		$params['source']       = $this->get_param( 'source', 'galleries', $params );
		$params['display_type'] = $this->get_param( 'display_type', NGG_BASIC_IMAGEBROWSER, $params );

		unset( $params['id'] );
		return $params;
	}

	public function render_legacy_single_picture_shortcode( $params ) {
		$params['display_type'] = $this->get_param( 'display_type', NGG_BASIC_SINGLEPIC, $params );
		$params['image_ids']    = $this->get_param( 'id', null, $params );
		unset( $params['id'] );
		return $params;
	}

	public function render_legacy_tag_cloud_shortcode( $params ) {
		$params['tagcloud']     = $this->get_param( 'tagcloud', 'yes', $params );
		$params['source']       = $this->get_param( 'source', 'tags', $params );
		$params['display_type'] = $this->get_param( 'display_type', NGG_BASIC_TAGCLOUD, $params );
		return $params;
	}

	public function render_legacy_nggallery( $params ) {
		$params['gallery_ids']  = $this->get_param( 'id', null, $params );
		$params['display_type'] = $this->get_param( 'display_type', NGG_BASIC_THUMBNAILS, $params );
		if ( isset( $params['images'] ) ) {
			$params['images_per_page'] = $this->get_param( 'images', null, $params );
		}
		unset( $params['id'] );
		unset( $params['images'] );
		return $params;
	}

	public function render_legacy_based_on_tags( $params ) {
		$params['tag_ids']      = $this->get_param( 'gallery', $this->get_param( 'album', [], $params ), $params );
		$params['source']       = $this->get_param( 'source', 'tags', $params );
		$params['display_type'] = $this->get_param( 'display_type', NGG_BASIC_THUMBNAILS, $params );
		unset( $params['gallery'] );
		return $params;
	}

	public function render_legacy_random_images( $params ) {
		$params['source']             = $this->get_param( 'source', 'random', $params );
		$params['images_per_page']    = $this->get_param( 'max', null, $params );
		$params['disable_pagination'] = $this->get_param( 'disable_pagination', true, $params );
		$params['display_type']       = $this->get_param( 'display_type', NGG_BASIC_THUMBNAILS, $params );

		// Inside if because DisplayedGallery->get_entities() doesn't handle NULL container_ids correctly.
		if ( isset( $params['id'] ) ) {
			$params['container_ids'] = $this->get_param( 'id', null, $params );
		}

		unset( $params['max'] );
		unset( $params['id'] );
		return $params;
	}

	public function render_legacy_recent_images( $params ) {
		$params['source']             = $this->get_param( 'source', 'recent', $params );
		$params['images_per_page']    = $this->get_param( 'max', null, $params );
		$params['disable_pagination'] = $this->get_param( 'disable_pagination', true, $params );
		$params['display_type']       = $this->get_param( 'display_type', NGG_BASIC_THUMBNAILS, $params );

		if ( isset( $params['id'] ) ) {
			$params['container_ids'] = $this->get_param( 'id', null, $params );
		}

		unset( $params['max'] );
		unset( $params['id'] );
		return $params;
	}

	public function render_legacy_thumb_shortcode( $params ) {
		$params['entity_ids']   = $this->get_param( 'id', null, $params );
		$params['source']       = $this->get_param( 'source', 'galleries', $params );
		$params['display_type'] = $this->get_param( 'display_type', NGG_BASIC_THUMBNAILS, $params );
		unset( $params['id'] );
		return $params;
	}

	public function render_legacy_slideshow( $params ) {
		$params['gallery_ids']    = $this->get_param( 'id', null, $params );
		$params['display_type']   = $this->get_param( 'display_type', NGG_BASIC_SLIDESHOW, $params );
		$params['gallery_width']  = $this->get_param( 'w', null, $params );
		$params['gallery_height'] = $this->get_param( 'h', null, $params );
		unset( $params['id'], $params['w'], $params['h'] );
		return $params;
	}
}
