<?php
/**
 * Convert Gallery REST Class - REST API endpoints for WP Gallery to NextGEN Gallery conversion.
 *
 * @package Imagely\NGG\REST\ConvertGallery
 * @since 3.x
 */

namespace Imagely\NGG\REST\ConvertGallery;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Imagely\NGG\Util\Security;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert Gallery REST Class.
 */
class ConvertGalleryREST {
	use ConvertGalleryTrait;

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		$instance = new self();

		// REST route for converting single WordPress gallery to NextGEN Gallery.
		register_rest_route(
			'imagely/v1',
			'/convert-gallery/single',
			[
				'methods'             => 'POST',
				'callback'            => [ $instance, 'convert_single_gallery' ],
				'permission_callback' => [ $instance, 'verify_single_convert_permission' ],
				'args'                => [
					'post_id'      => [
						'required'          => false,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'columns'      => [
						'type'              => 'integer',
						'default'           => 3,
						'sanitize_callback' => 'absint',
					],
					'sizeSlug'     => [
						'type'              => 'string',
						'default'           => 'thumbnail',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'linkTarget'   => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'images'       => [
						'type'     => 'array',
						'required' => true,
						'items'    => [
							'type'       => 'object',
							'properties' => [
								'id'    => [ 'type' => 'integer' ],
								'url'   => [ 'type' => 'string' ],
								'title' => [ 'type' => 'string' ],
								'alt'   => [ 'type' => 'string' ],
							],
						],
					],
					'blockContent' => [
						'type'              => 'string',
						'sanitize_callback' => 'wp_kses_post',
					],
				],
			]
		);

		// REST route for starting bulk conversion.
		register_rest_route(
			'imagely/v1',
			'/convert-gallery/bulk-start',
			[
				'methods'             => 'POST',
				'callback'            => [ $instance, 'start_bulk_conversion' ],
				'permission_callback' => [ $instance, 'verify_bulk_convert_permission' ],
				'args'                => [
					'selected_posttype' => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// REST route for processing items during bulk conversion.
		register_rest_route(
			'imagely/v1',
			'/convert-gallery/bulk-process',
			[
				'methods'             => 'POST',
				'callback'            => [ $instance, 'process_bulk_item' ],
				'permission_callback' => [ $instance, 'verify_process_permission' ],
				'args'                => [
					'post_id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// REST route for getting available post types for conversion.
		register_rest_route(
			'imagely/v1',
			'/convert-gallery/post-types',
			[
				'methods'             => 'GET',
				'callback'            => [ $instance, 'get_post_types' ],
				'permission_callback' => [ $instance, 'verify_bulk_convert_permission' ],
			]
		);
	}

	/**
	 * Permission callback for single gallery conversion.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function verify_single_convert_permission( $request ) {
		// Must have upload images capability at minimum.
		if ( ! Security::is_allowed( 'NextGEN Upload images' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to create galleries.', 'nggallery' ),
				[ 'status' => 403 ]
			);
		}

		// Get post ID from request if provided.
		$post_id = absint( $request->get_param( 'post_id' ) );

		// If post_id is provided, check if user can edit the post.
		if ( $post_id > 0 && ! $this->can_edit_post( $post_id ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to edit this post.', 'nggallery' ),
				[ 'status' => 403 ]
			);
		}

		return true;
	}

	/**
	 * Permission callback for bulk gallery conversion.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function verify_bulk_convert_permission( $request = null ) {
		// Check bulk conversion capability.
		$capability = apply_filters( 'imagely_convert_bulk_galleries_cap', 'manage_options' );
		if ( ! current_user_can( $capability ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to access this feature.', 'nggallery' ),
				[ 'status' => 403 ]
			);
		}

		return true;
	}

	/**
	 * Permission callback for processing gallery items.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function verify_process_permission( $request ) {
		// Must have NextGEN upload images capability (same as single conversion).
		if ( ! Security::is_allowed( 'NextGEN Upload images' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to create galleries.', 'nggallery' ),
				[ 'status' => 403 ]
			);
		}

		// Get post ID from request.
		$post_id = absint( $request->get_param( 'post_id' ) );

		if ( $post_id <= 0 ) {
			return new WP_Error(
				'rest_invalid_param',
				__( 'A valid post ID is required.', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Get the post.
		$post = get_post( $post_id );

		if ( ! $post ) {
			return new WP_Error(
				'rest_post_not_found',
				__( 'Post not found.', 'nggallery' ),
				[ 'status' => 404 ]
			);
		}

		// Check if user can edit the post.
		if ( ! $this->can_edit_post( $post_id ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to edit this post.', 'nggallery' ),
				[ 'status' => 403 ]
			);
		}

		return true;
	}

	/**
	 * Convert single WordPress Gallery to NextGEN Gallery.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function convert_single_gallery( $request ) {
		$post_id       = absint( $request->get_param( 'post_id' ) );
		$columns       = absint( $request->get_param( 'columns' ) );
		$size_slug     = sanitize_text_field( $request->get_param( 'sizeSlug' ) );
		$link_target   = sanitize_text_field( $request->get_param( 'linkTarget' ) );
		$images        = $request->get_param( 'images' );
		$block_content = $request->get_param( 'blockContent' );

		// Sanitize images array.
		$sanitized_images = array_map(
			function ( $image ) {
				return [
					'id'    => isset( $image['id'] ) ? absint( $image['id'] ) : 0,
					'url'   => isset( $image['url'] ) ? esc_url_raw( $image['url'] ) : '',
					'title' => isset( $image['title'] ) ? sanitize_text_field( $image['title'] ) : '',
					'alt'   => isset( $image['alt'] ) ? sanitize_text_field( $image['alt'] ) : '',
				];
			},
			is_array( $images ) ? $images : []
		);

		// Check that required parameters are provided and valid.
		if ( empty( $sanitized_images ) || ! is_array( $sanitized_images ) ) {
			return new WP_REST_Response(
				[
					'message' => __( 'No images provided. Please add at least one image to continue.', 'nggallery' ),
				],
				400
			);
		}

		// Check if each image in the array has the required fields.
		foreach ( $sanitized_images as $image ) {
			if ( empty( $image['id'] ) ) {
				return new WP_REST_Response(
					[
						'message' => __( 'Each image must have an ID. Please check your images and try again.', 'nggallery' ),
					],
					400
				);
			}
		}

		$passed_data = [
			'post_id'       => $post_id,
			'columns'       => $columns,
			'size_slug'     => $size_slug,
			'link_target'   => $link_target,
			'images'        => $sanitized_images,
			'block_content' => $block_content,
		];

		$created_result = $this->create_imagely_gallery_from_wp_gallery( $passed_data );

		if ( isset( $created_result['error'] ) && ! empty( $created_result['error'] ) ) {
			return new WP_REST_Response(
				[
					'message' => $created_result['error'],
				],
				400
			);
		} else {
			return new WP_REST_Response( $created_result, 200 );
		}
	}

	/**
	 * Start the bulk conversion process by finding all posts with WP galleries.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function start_bulk_conversion( $request ) {
		$selected_posttype = sanitize_text_field( $request->get_param( 'selected_posttype' ) );

		if ( empty( $selected_posttype ) ) {
			return new WP_REST_Response(
				[ 'message' => __( 'A post type is required for conversion. Please make a selection.', 'nggallery' ) ],
				400
			);
		}

		if ( ! post_type_exists( $selected_posttype ) ) {
			return new WP_REST_Response(
				[ 'message' => __( 'Post type not recognized. Please check your selection and try again.', 'nggallery' ) ],
				404
			);
		}

		// Check if the current user can edit the selected post type.
		$post_type_object = get_post_type_object( $selected_posttype );
		if ( ! $post_type_object || ! isset( $post_type_object->cap, $post_type_object->cap->edit_posts ) ) {
			return new WP_REST_Response(
				[ 'message' => __( 'Unable to verify permissions for the selected post type.', 'nggallery' ) ],
				403
			);
		}

		if ( ! current_user_can( $post_type_object->cap->edit_posts ) ) {
			return new WP_REST_Response(
				[
					'message' => sprintf(
						// translators: %s is the post type singular name.
						__( 'You do not have permission to edit %s item(s).', 'nggallery' ),
						$post_type_object->labels->singular_name
					),
				],
				403
			);
		}

		// Get all posts of the given post type.
		$args  = [
			'post_type'   => $selected_posttype,
			'post_status' => 'any',
			'numberposts' => -1,
		];
		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			return new WP_REST_Response(
				[ 'message' => __( 'No items found for the selected post type. Please try selecting a different option.', 'nggallery' ) ],
				400
			);
		}

		// Find posts with WordPress galleries.
		$found_posts = [];
		foreach ( $posts as $post ) {
			if ( has_block( 'gallery', $post->post_content ) || has_shortcode( $post->post_content, 'gallery' ) ) {
				$found_posts[] = $post->ID;
			}
		}

		if ( empty( $found_posts ) ) {
			return new WP_REST_Response(
				[ 'message' => __( 'No WordPress galleries were found in the selected post type.', 'nggallery' ) ],
				400
			);
		}

		// Filter to only posts the current user can edit.
		$filtered_posts = array_values(
			array_filter(
				$found_posts,
				function ( $post_id ) {
					return $this->can_edit_post( $post_id );
				}
			)
		);

		if ( empty( $filtered_posts ) ) {
			return new WP_REST_Response(
				[ 'message' => __( 'You do not have permission to edit any of the found posts.', 'nggallery' ) ],
				403
			);
		}

		return new WP_REST_Response( [ 'posts' => $filtered_posts ], 200 );
	}

	/**
	 * Process a single post during bulk conversion.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function process_bulk_item( $request ) {
		$post_id = absint( $request->get_param( 'post_id' ) );

		if ( $post_id <= 0 ) {
			return new WP_REST_Response(
				[ 'message' => __( 'A valid post ID is required.', 'nggallery' ) ],
				400
			);
		}

		// Get the post.
		$post = get_post( $post_id );

		if ( ! $post ) {
			return new WP_REST_Response(
				[ 'message' => __( 'Post not found.', 'nggallery' ) ],
				404
			);
		}

		$updated_content = $post->post_content; // Start with the current content.
		$needs_update    = false;

		// Process gallery shortcodes.
		$shortcode_result = $this->process_gallery_shortcodes( $updated_content, $post_id, $needs_update );

		// Check if shortcode processing returned an error.
		if ( $shortcode_result instanceof WP_REST_Response ) {
			return $shortcode_result;
		}

		// Parse and process gallery blocks.
		$blocks = parse_blocks( $updated_content );
		$this->process_gallery_blocks( $blocks, $post_id, $needs_update );

		// Update post content if changes were made.
		if ( $needs_update ) {
			// If blocks were processed, serialize back to post content.
			$updated_content = serialize_blocks( $blocks );

			wp_update_post(
				[
					'ID'           => $post_id,
					'post_content' => $updated_content,
				]
			);

			return new WP_REST_Response(
				[ 'success' => __( 'Galleries converted to NextGEN Galleries.', 'nggallery' ) ],
				200
			);
		} else {
			return new WP_REST_Response(
				[
					'message'  => __( 'This post contains gallery content that is not compatible with the conversion process.', 'nggallery' ),
					'edit_url' => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
				],
				400
			);
		}
	}

	/**
	 * Get available post types for conversion.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_post_types( $request ) {
		// Exclusions for keywords, exact names, and labels.
		$exclusions = [
			'keywords'    => [ 'ngg', 'nextgen', 'photocrati', 'imagely', 'elementor' ],
			'exact_names' => [ 'e-landing-page', 'ngg_gallery', 'ngg_album', 'ngg_pictures' ],
			'labels'      => [ 'NextGEN', 'Gallery' ],
		];

		// Retrieve all custom post types.
		$post_types = get_post_types( [ '_builtin' => false ], 'objects' );

		// Filter post types based on exclusions.
		$filtered_post_types = array_filter(
			$post_types,
			function ( $post_type ) use ( $exclusions ) {
				// Exclude based on keywords in the post type name.
				foreach ( $exclusions['keywords'] as $keyword ) {
					if ( strpos( $post_type->name, $keyword ) !== false ) {
						return false;
					}
				}

				// Exclude based on exact post type names.
				if ( in_array( $post_type->name, $exclusions['exact_names'], true ) ) {
					return false;
				}

				// Exclude based on labels.
				foreach ( $exclusions['labels'] as $label ) {
					if ( strpos( $post_type->label, $label ) !== false ) {
						return false;
					}
				}

				return true; // Include post type if it passes all checks.
			}
		);

		// Allow users to modify the post types list.
		$filtered_post_types = apply_filters( 'imagely_convert_post_types', $filtered_post_types );

		// Build response array.
		$post_types_list = [
			[
				'value' => 'post',
				'label' => __( 'Posts', 'nggallery' ),
			],
			[
				'value' => 'page',
				'label' => __( 'Pages', 'nggallery' ),
			],
		];

		foreach ( $filtered_post_types as $post_type ) {
			$post_types_list[] = [
				'value' => esc_html( $post_type->name ),
				'label' => esc_html( $post_type->label ),
			];
		}

		return new WP_REST_Response( $post_types_list, 200 );
	}
}
