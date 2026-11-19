<?php
/**
 * REST routes for the Privacy section of Edit Gallery.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\REST\DataMappers\GalleryREST;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * GET and POST imagely/v1/galleries/{id}/privacy, and POST .../privacy/recreate-page, gated like
 * every other gallery edit.
 */
class PrivacyREST {

	/**
	 * Registers the routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		$id_arg = [
			'id' => [
				'required'          => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			],
		];

		register_rest_route(
			'imagely/v1',
			'/galleries/(?P<id>\d+)/privacy',
			[
				[
					'methods'             => 'GET',
					'callback'            => [ self::class, 'get_privacy' ],
					'permission_callback' => [ GalleryREST::class, 'check_edit_permission' ],
					'args'                => $id_arg,
				],
				[
					'methods'             => 'POST',
					'callback'            => [ self::class, 'save_privacy' ],
					'permission_callback' => [ GalleryREST::class, 'check_edit_permission' ],
					'args'                => array_merge(
						$id_arg,
						[
							'is_private'        => [
								'required' => true,
								'type'     => 'boolean',
							],
							'password'          => [
								'type' => 'string',
							],
							'generate_password' => [
								'type'    => 'boolean',
								'default' => false,
							],
						]
					),
				],
			]
		);

		register_rest_route(
			'imagely/v1',
			'/galleries/(?P<id>\d+)/privacy/recreate-page',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'recreate_page' ],
				'permission_callback' => [ GalleryREST::class, 'check_edit_permission' ],
				'args'                => array_merge(
					$id_arg,
					[
						'password' => [
							'type' => 'string',
						],
					]
				),
			]
		);
	}

	/**
	 * GET: the privacy settings of a gallery.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function get_privacy( WP_REST_Request $request ) {
		$gallery = self::find_gallery( $request );

		if ( is_wp_error( $gallery ) ) {
			return $gallery;
		}

		return new WP_REST_Response( self::prepare( $gallery ), 200 );
	}

	/**
	 * POST: turns Private on or off, and sets the page password.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function save_privacy( WP_REST_Request $request ) {
		$gallery = self::find_gallery( $request );

		if ( is_wp_error( $gallery ) ) {
			return $gallery;
		}

		$gid        = (int) $gallery->{$gallery->id_field};
		$is_private = (bool) $request->get_param( 'is_private' );
		$password   = $request->has_param( 'password' ) ? (string) $request->get_param( 'password' ) : null;

		if ( null === $password && $request->get_param( 'generate_password' ) ) {
			$password = ClientPage::generate_password();
		}

		/**
		 * Filters the page password a privacy save sets (Pro sets a random one in link-only mode).
		 *
		 * @param string|null     $password Password from the request; null keeps the current one.
		 * @param object          $gallery  Gallery entity.
		 * @param WP_REST_Request $request  Request.
		 */
		$password = apply_filters( 'ngg_private_gallery_save_password', $password, $gallery, $request );

		if ( $is_private ) {
			$result = Registry::is_private( $gid ) && ClientPage::get_live_page( $gallery )
				? ( null === $password ? true : ClientPage::set_password( $gallery, $password ) )
				: ClientPage::make_private( $gallery, $password );
		} else {
			$result = ClientPage::make_public( $gallery );
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/**
		 * Fires after a privacy save, for the paid settings. Return a WP_Error from the filter to fail.
		 *
		 * @param true|WP_Error   $result  Result so far.
		 * @param object          $gallery Gallery entity, re-read.
		 * @param WP_REST_Request $request Request.
		 */
		$result = apply_filters( 'ngg_private_gallery_saved', true, GalleryMapper::get_instance()->find( $gid ), $request );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new WP_REST_Response( self::prepare( GalleryMapper::get_instance()->find( $gid ) ), 200 );
	}

	/**
	 * POST: creates a new client page when the old one is gone.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function recreate_page( WP_REST_Request $request ) {
		$gallery = self::find_gallery( $request );

		if ( is_wp_error( $gallery ) ) {
			return $gallery;
		}

		$password = $request->has_param( 'password' ) ? (string) $request->get_param( 'password' ) : null;
		$result   = ClientPage::recreate_page( $gallery, $password );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$gid = (int) $gallery->{$gallery->id_field};

		/** This filter is documented in save_privacy(). */
		$result = apply_filters( 'ngg_private_gallery_saved', true, GalleryMapper::get_instance()->find( $gid ), $request );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new WP_REST_Response( self::prepare( GalleryMapper::get_instance()->find( $gid ) ), 200 );
	}

	/**
	 * The privacy data of a gallery, for its managers only.
	 *
	 * @param object $gallery Gallery entity.
	 * @return array
	 */
	public static function prepare( $gallery ) {
		$page        = ClientPage::get_live_page( $gallery );
		$gid         = (int) $gallery->{$gallery->id_field};
		$is_set_up   = Registry::is_private( $gid );
		$can_edit    = $page ? current_user_can( 'edit_post', $page->ID ) : current_user_can( 'publish_pages' );
		$other_count = 0;

		if ( $page ) {
			$other_count = count( array_diff( Registry::galleries_for_page( $page->ID ), [ $gid ] ) );
		}

		$data = [
			'gid'                   => $gid,
			'is_private'            => ! empty( $gallery->is_private ),
			'status'                => ClientPage::get_status( $gallery ),
			'page_id'               => $page ? (int) $page->ID : 0,
			'page_title'            => $page ? (string) $page->post_title : '',
			'page_status'           => $page ? (string) $page->post_status : '',
			'page_url'              => $page ? (string) get_permalink( $page ) : '',
			'page_edit_url'         => $page ? (string) get_edit_post_link( $page->ID, 'raw' ) : '',
			'page_is_client_page'   => $page && ClientPage::page_has_mark( $page->ID, $gid ),
			'page_is_front_page'    => $page && 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === (int) $page->ID,
			'other_private_on_page' => $other_count,
			// Read from the page every time; the page is the source of truth.
			'password'              => $page && $can_edit ? (string) $page->post_password : '',
			'client_url'            => $is_set_up ? ClientPage::get_client_url( $gallery ) : '',
			'can_edit_page'         => (bool) $can_edit,
			'access_count'          => isset( $gallery->access_count ) ? (int) $gallery->access_count : 0,
			'last_access_at'        => ! empty( $gallery->last_access_at ) ? (string) $gallery->last_access_at : '',
			// Stored in GMT; shown in the site's timezone and date format.
			'last_access_display'   => ! empty( $gallery->last_access_at )
				? (string) wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $gallery->last_access_at . ' UTC' ) )
				: '',
			'paid'                  => false,
		];

		/**
		 * Filters the privacy data sent to the Privacy section (Pro adds the paid settings).
		 *
		 * @param array  $data    Privacy data.
		 * @param object $gallery Gallery entity.
		 */
		return (array) apply_filters( 'ngg_private_gallery_rest_data', $data, $gallery );
	}

	/**
	 * The gallery a request names.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object|WP_Error
	 */
	private static function find_gallery( WP_REST_Request $request ) {
		$gallery = GalleryMapper::get_instance()->find( (int) $request->get_param( 'id' ) );

		if ( ! $gallery ) {
			return new WP_Error( 'gallery_not_found', __( 'Gallery not found', 'nggallery' ), [ 'status' => 404 ] );
		}

		return $gallery;
	}
}
