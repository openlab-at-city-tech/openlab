<?php

namespace Imagely\NGG\IGW;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Settings\GlobalSettings;

class EventPublisher {

	protected static $instance = null;

	protected $setting_name = null;

	public function __construct() {
		$this->setting_name = Settings::get_instance()->get( 'frame_event_cookie_name' );
	}

	public function register_hooks() {
		add_action( 'init', [ $this, 'register_script' ] );
		add_filter( 'ngg_admin_script_handles', [ $this, 'add_script_to_ngg_pages' ] );
		add_action( 'ngg_enqueue_frame_event_publisher_script', [ $this, 'enqueue_script' ] );

		// Elementor's editor.php runs `new \WP_Scripts()` which requires we register scripts on both init and this
		// action if we want the attach-to-post code to function (which relies on frame_event_publisher).
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_script' ] );

		// Emit frame communication events.
		if ( $this->does_request_require_frame_communication() ) {
			add_action( 'ngg_created_new_gallery', [ $this, 'new_gallery_event' ] );
			add_action( 'ngg_after_new_images_added', [ $this, 'images_added_event' ] );
			add_action( 'ngg_page_event', [ $this, 'nextgen_page_event' ] );
			add_action( 'ngg_manage_tags', [ $this, 'manage_tags_event' ] );
		}
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new EventPublisher();
		}
		return self::$instance;
	}

	/**
	 * Encodes data for a setting
	 *
	 * @param array $data
	 * @return string
	 */
	protected function encode( $data ) {
		return \rawurlencode( \json_encode( $data ) );
	}

	/**
	 * Decodes data from a setting
	 *
	 * @param string $data
	 * @return array
	 */
	protected function decode( $data ) {
		return (array) \json_decode( \rawurldecode( $data ) );
	}

	/**
	 * Adds a setting to the frame events
	 *
	 * @param array $data
	 * @return array
	 */
	public function add_event( $data ) {
		$id              = \md5( \serialize( $data ) );
		$data['context'] = 'attach_to_post';

		$write_cookie = true;
		if ( \defined( 'XMLRPC_REQUEST' ) ) {
			$write_cookie = XMLRPC_REQUEST == false;
		}

		if ( $write_cookie ) {
			\setrawcookie( $this->setting_name . '_' . $id, $this->encode( $data ), \time() + 10800, '/', \parse_url( \site_url(), PHP_URL_HOST ) );
		}

		return $data;
	}

	/* TODO: Determine if this is necessary and remove it */
	public function add_script_to_ngg_pages( $scripts ) {
		$scripts['frame_event_publisher'] = NGG_SCRIPT_VERSION;
		return $scripts;
	}

	public function enqueue_script() {
		wp_enqueue_script( 'frame_event_publisher' );
		wp_localize_script(
			'frame_event_publisher',
			'frame_event_publisher_domain',
			[ parse_url( site_url(), PHP_URL_HOST ) ]
		);
	}

	public function register_script() {
		wp_register_script(
			'frame_event_publisher',
			StaticAssets::get_url( 'IGW/frame_event_publisher.js', 'photocrati-frame_communication#frame_event_publisher.js' ),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION
		);
	}

	public function does_request_require_frame_communication(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ( strpos( $_SERVER['REQUEST_URI'], 'attach_to_post' ) !== false or ( isset( $_SERVER['HTTP_REFERER'] ) && strpos( $_SERVER['HTTP_REFERER'], 'attach_to_post' ) !== false ) or array_key_exists( 'attach_to_post', $_REQUEST ) );
	}

	/**
	 * Notify frames that a new gallery has been created
	 *
	 * @param int $gallery_id
	 */
	public function new_gallery_event( $gallery_id ) {
		$gallery = GalleryMapper::get_instance()->find( $gallery_id );
		if ( $gallery ) {
			$this->add_event(
				[
					'event'         => 'new_gallery',
					'gallery_id'    => intval( $gallery_id ),
					'gallery_title' => $gallery->title,
				]
			);
		}
	}

	/**
	 * Notifies a frame that images have been added to a gallery
	 *
	 * @param int   $gallery_id
	 * @param array $image_ids
	 */
	public function images_added_event( $gallery_id, $image_ids = [] ) {
		$this->add_event(
			[
				'event'      => 'images_added',
				'gallery_id' => intval( $gallery_id ),
			]
		);
	}

	/**
	 * Notifies a frame that an action has been performed on a particular NextGEN page
	 *
	 * @param array $event
	 */
	public function nextgen_page_event( $event ) {
		$this->add_event( $event );
	}

	/**
	 * Notifies a frame that the tags have changed
	 *
	 * @param array $tags
	 */
	public function manage_tags_event( $tags = [] ) {
		$this->add_event(
			[
				'event' => 'manage_tags',
				'tags'  => $tags,
			]
		);
	}
}
