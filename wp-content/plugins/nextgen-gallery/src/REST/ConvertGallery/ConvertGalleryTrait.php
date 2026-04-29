<?php
/**
 * Convert Gallery Trait - Core conversion logic for WP Gallery to NextGEN Gallery.
 *
 * @package Imagely\NGG\REST\ConvertGallery
 * @since 3.x
 */

namespace Imagely\NGG\REST\ConvertGallery;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DataTypes\Gallery;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert Gallery Trait.
 */
trait ConvertGalleryTrait {

	/**
	 * Create an Imagely (NextGEN) Gallery using WordPress Gallery data.
	 *
	 * @param array $passed_data WP gallery passed data array.
	 * @return array Result with gallery_id or error.
	 */
	public function create_imagely_gallery_from_wp_gallery( $passed_data ) {
		$post_id       = $passed_data['post_id'] ?? null;
		$columns       = $passed_data['columns'] ?? 3;
		$size_slug     = $passed_data['size_slug'] ?? 'thumbnail';
		$link_target   = $passed_data['link_target'] ?? '';
		$images        = $passed_data['images'] ?? [];
		$block_content = $passed_data['block_content'] ?? '';

		// Generate gallery title.
		$date_now      = wp_date( 'Y-m-d H:i:s' );
		$gallery_title = sprintf( 'Converted-%s', $date_now );

		if ( ! empty( $post_id ) ) {
			// Save the block content to post meta using a unique meta key for backup.
			$date_prefix = wp_date( 'Ymd_His' );
			$meta_key    = 'wp_gallery_block_bkp_' . wp_rand( 1000, 9999 ) . '_' . $date_prefix;
			update_post_meta( $post_id, $meta_key, $block_content );

			// Get the post title.
			$post_title = get_the_title( $post_id );

			if ( ! empty( $post_title ) ) {
				// Truncate title to 15-20 characters (using mb_* functions for multibyte support).
				$truncated_title = mb_strlen( $post_title ) > 20 ? mb_substr( $post_title, 0, 20 ) : $post_title;

				// Generate a unique gallery title.
				$gallery_title = sprintf(
					'%s-%d-Converted-%s',
					$truncated_title,
					$post_id,
					$date_now
				);
			}
		}

		// Create the NextGEN Gallery.
		$gallery_mapper = GalleryMapper::get_instance();
		$gallery        = $gallery_mapper->create( [ 'title' => $gallery_title ] );

		if ( ! $gallery->save() ) {
			return [
				'error' => __(
					'There was a problem creating the gallery. Please try again.',
					'nggallery'
				),
			];
		}

		$gallery_id = $gallery->id();

		// Import images from WordPress Media Library.
		$storage      = StorageManager::get_instance();
		$image_mapper = ImageMapper::get_instance();
		$image_ids    = [];
		$errors       = [];

		// Raise memory limit for image processing.
		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			wp_raise_memory_limit( 'image' );
		}

		foreach ( $images as $image_data ) {
			$attachment_id = $image_data['id'] ?? 0;

			if ( empty( $attachment_id ) ) {
				continue;
			}

			try {
				$abspath = get_attached_file( $attachment_id );

				if ( ! $abspath || ! file_exists( $abspath ) ) {
					$errors[] = sprintf(
						// translators: %d is the attachment ID.
						__( 'Could not find file for attachment ID %d', 'nggallery' ),
						$attachment_id
					);
					continue;
				}

				$file_data = file_get_contents( $abspath );

				if ( empty( $file_data ) ) {
					$errors[] = sprintf(
						// translators: %d is the attachment ID.
						__( 'Could not read file for attachment ID %d', 'nggallery' ),
						$attachment_id
					);
					continue;
				}

				$file_name  = \Imagely\NGG\Display\I18N::mb_basename( $abspath );
				$attachment = get_post( $attachment_id );
				$ngg_image  = $storage->upload_image( $gallery_id, $file_name, $file_data );

				if ( $ngg_image ) {
					// Import metadata from WordPress attachment.
					$ngg_image = $image_mapper->find( $ngg_image );

					// Use the alt text from the passed data, WordPress attachment, or title as fallback.
					// Priority: 1) Provided alt text, 2) WP attachment caption, 3) WP attachment alt meta, 4) Title as last resort.
					if ( ! empty( $image_data['alt'] ) ) {
						$ngg_image->alttext = $image_data['alt'];
					} elseif ( $attachment instanceof \WP_Post && ! empty( $attachment->post_excerpt ) ) {
						$ngg_image->alttext = $attachment->post_excerpt;
					} else {
						$attachment_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
						if ( $attachment_alt ) {
							$ngg_image->alttext = $attachment_alt;
						} elseif ( ! empty( $image_data['title'] ) ) {
							// Only use title as fallback if no alt text is available.
							$ngg_image->alttext = $image_data['title'];
						}
					}
					// Use description from WordPress attachment.
					if ( $attachment instanceof \WP_Post && ! empty( $attachment->post_content ) ) {
						$ngg_image->description = $attachment->post_content;
					}

					// Apply filters and save.
					$ngg_image = apply_filters( 'ngg_wp_gallery_converted_image', $ngg_image, $attachment, $image_data );
					$image_mapper->save( $ngg_image );
					$image_ids[] = $ngg_image->{$ngg_image->id_field};
				} else {
					$errors[] = sprintf(
						// translators: %s is the filename.
						__( 'Failed to import image: %s', 'nggallery' ),
						$file_name
					);
				}
			} catch ( \RuntimeException $ex ) {
				$errors[] = $ex->getMessage();
			} catch ( \Exception $ex ) {
				$errors[] = sprintf(
					// translators: %d is the attachment ID.
					__( 'Unexpected error importing attachment ID %d', 'nggallery' ),
					$attachment_id
				);
			}
		}

		// Set the first image as preview if we imported any.
		if ( ! empty( $image_ids ) ) {
			$gallery->previewpic = $image_ids[0];
			$gallery_mapper->save( $gallery );
		}

		// If no images were imported, delete the gallery and return error.
		if ( empty( $image_ids ) ) {
			$gallery_mapper->destroy( $gallery_id );
			return [
				'error' => __(
					'No images could be imported. The gallery was not created.',
					'nggallery'
				),
			];
		}

		$response_data = [
			'gallery_id'  => $gallery_id,
			'title'       => $gallery_title,
			'columns'     => $columns,
			'image_count' => count( $image_ids ),
			'image_ids'   => $image_ids,
			'errors'      => $errors,
			'message'     => __( 'Converted successfully. Don\'t forget to save your changes!', 'nggallery' ),
		];

		return $response_data;
	}

	/**
	 * Process gallery shortcodes in post content.
	 *
	 * @param string $updated_content Reference to content being updated.
	 * @param int    $post_id         Post ID.
	 * @param bool   $needs_update    Reference to flag indicating if update is needed.
	 * @return \WP_REST_Response|void
	 */
	public function process_gallery_shortcodes( &$updated_content, $post_id, &$needs_update ) {
		if ( ! has_shortcode( $updated_content, 'gallery' ) ) {
			return;
		}

		preg_match_all( '/\[gallery(.*?)\]/', $updated_content, $matches, PREG_SET_ORDER );

		foreach ( $matches as $shortcode ) {
			$shortcode_string = $shortcode[0];
			$shortcode_attrs  = shortcode_parse_atts( $shortcode[1] );

			// Extract gallery attributes.
			$ids        = isset( $shortcode_attrs['ids'] ) ? explode( ',', $shortcode_attrs['ids'] ) : [];
			$include    = isset( $shortcode_attrs['include'] ) ? explode( ',', $shortcode_attrs['include'] ) : [];
			$exclude    = isset( $shortcode_attrs['exclude'] ) ? explode( ',', $shortcode_attrs['exclude'] ) : [];
			$columns    = isset( $shortcode_attrs['columns'] ) ? absint( $shortcode_attrs['columns'] ) : 3;
			$size_slug  = isset( $shortcode_attrs['size'] ) ? sanitize_text_field( $shortcode_attrs['size'] ) : 'thumbnail';
			$gallery_id = isset( $shortcode_attrs['id'] ) ? absint( $shortcode_attrs['id'] ) : 0;

			// Fetch attachments for the shortcode.
			if ( empty( $ids ) ) {
				$query_args = [
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'post_parent'    => $gallery_id ? $gallery_id : $post_id,
				];

				if ( ! empty( $include ) ) {
					$query_args['post__in'] = array_map( 'absint', $include );
				}

				if ( ! empty( $exclude ) ) {
					$query_args['post__not_in'] = array_map( 'absint', $exclude );
				}

				$ids = get_posts( $query_args );
			}

			// Create gallery images array.
			$images = [];
			foreach ( $ids as $id ) {
				$image_id    = absint( $id );
				$image_url   = wp_get_attachment_url( $image_id );
				$image_title = get_the_title( $image_id );
				$image_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

				if ( $image_url ) {
					$images[] = [
						'id'    => $image_id,
						'url'   => $image_url,
						'title' => $image_title,
						'alt'   => $image_alt,
					];
				}
			}

			if ( ! empty( $images ) ) {
				$passed_data = [
					'post_id'       => $post_id,
					'columns'       => $columns,
					'size_slug'     => $size_slug,
					'link_target'   => '_self',
					'images'        => $images,
					'block_content' => $shortcode_string,
				];

				$created_result = $this->create_imagely_gallery_from_wp_gallery( $passed_data );

				if ( isset( $created_result['error'] ) && ! empty( $created_result['error'] ) ) {
					return new \WP_REST_Response( [ 'message' => $created_result['error'] ], 400 );
				} else {
					$imagely_shortcode = "[imagely id=\"{$created_result['gallery_id']}\"]";
					$updated_content   = str_replace( $shortcode_string, $imagely_shortcode, $updated_content );
					$needs_update      = true;
				}
			}
		}
	}

	/**
	 * Process gallery blocks in post content.
	 *
	 * @param array $blocks       Reference to parsed blocks array.
	 * @param int   $post_id      Post ID.
	 * @param bool  $needs_update Reference to flag indicating if update is needed.
	 * @return void
	 */
	public function process_gallery_blocks( &$blocks, $post_id, &$needs_update ) {
		foreach ( $blocks as &$block ) {
			// If the block is a gallery block.
			if ( 'core/gallery' === $block['blockName'] ) {
				// Extract the attributes.
				$columns       = $block['attrs']['columns'] ?? 3;
				$size_slug     = $block['attrs']['sizeSlug'] ?? 'thumbnail';
				$link_target   = $block['attrs']['linkTo'] ?? '';
				$block_content = serialize_block( $block );

				$images = [];

				// Check if there are inner blocks (Gutenberg gallery structure).
				if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
					foreach ( $block['innerBlocks'] as $inner_block ) {
						// If the inner block is an image block.
						if ( 'core/image' === $inner_block['blockName'] ) {
							$image_id = isset( $inner_block['attrs']['id'] ) ? absint( $inner_block['attrs']['id'] ) : 0;
							if ( $image_id ) {
								$image_url   = wp_get_attachment_url( $image_id );
								$image_title = get_the_title( $image_id );

								// Use the `alt` from the HTML if not available in attributes.
								$image_alt = '';
								if ( ! empty( $inner_block['innerHTML'] ) ) {
									$doc                          = new \DOMDocument( '1.0', 'UTF-8' );
									$previous_use_internal_errors = libxml_use_internal_errors( true );
									// Prepend UTF-8 encoding declaration to prevent character corruption.
									$loaded = $doc->loadHTML( '<?xml encoding="UTF-8">' . $inner_block['innerHTML'] );
									libxml_clear_errors();
									libxml_use_internal_errors( $previous_use_internal_errors );

									if ( $loaded ) {
										$img_tag = $doc->getElementsByTagName( 'img' )->item( 0 );
										if ( $img_tag && $img_tag->hasAttribute( 'alt' ) ) {
											$image_alt = $img_tag->getAttribute( 'alt' );
										}
									}
								}

								// Fallback: If `alt` is not in HTML, try `_wp_attachment_image_alt` meta.
								if ( ! $image_alt ) {
									$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
								}

								$images[] = [
									'id'    => $image_id,
									'url'   => $image_url,
									'title' => $image_title,
									'alt'   => $image_alt,
								];
							}
						}
					}
				}

				if ( empty( $images ) || ! is_array( $images ) ) {
					// Recursively process inner blocks even if this gallery has no images.
					if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
						$this->process_gallery_blocks( $block['innerBlocks'], $post_id, $needs_update );
					}
					continue;
				}

				$passed_data = [
					'post_id'       => $post_id,
					'columns'       => $columns,
					'size_slug'     => $size_slug,
					'link_target'   => $link_target,
					'images'        => $images,
					'block_content' => $block_content,
				];

				$created_result = $this->create_imagely_gallery_from_wp_gallery( $passed_data );

				if ( isset( $created_result['error'] ) && ! empty( $created_result['error'] ) ) {
					// Continue processing other blocks even if one fails.
					continue;
				} else {
					// Replace the core/gallery block with imagely/main-block.
					$block['blockName']    = 'imagely/main-block';
					$block['attrs']        = [
						'content' => "[imagely id=\"{$created_result['gallery_id']}\"]",
					];
					$block['innerBlocks']  = [];
					$block['innerHTML']    = "[imagely id=\"{$created_result['gallery_id']}\"]";
					$block['innerContent'] = [
						"[imagely id=\"{$created_result['gallery_id']}\"]",
					];

					$needs_update = true;
				}
			}

			// Recursively process inner blocks.
			if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$this->process_gallery_blocks( $block['innerBlocks'], $post_id, $needs_update );
			}
		}
	}

	/**
	 * Check if the current user can edit the post.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public function can_edit_post( $post_id ) {
		return current_user_can( 'edit_post', $post_id );
	}
}
