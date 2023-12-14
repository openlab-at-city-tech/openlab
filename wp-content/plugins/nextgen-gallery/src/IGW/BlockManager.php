<?php

namespace Imagely\NGG\IGW;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Util\URL;

/**
 * Handles the NextGEN block and post thumbnail
 */
class BlockManager {

	protected static $instance = null;

	/**
	 * @return BlockManager
	 */
	static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new BlockManager();
		}
		return self::$instance;
	}

	public function register_hooks() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_post_thumbnails' ], 1 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor' ] );

		// Adds NextGEN thumbnail support to all posts with 'thumbnail' support by adding a field for posts/pages to
		// set the ngg_post_thumbnail via REST API.
		add_action(
			'init',
			function () {
				array_map(
					function ( $post_type ) {
						add_post_type_support( $post_type, 'custom-fields' );
						register_meta(
							$post_type,
							'ngg_post_thumbnail',
							[
								'type'         => 'integer',
								'single'       => true,
								'show_in_rest' => true,
							]
						);

						add_action( 'rest_insert_' . $post_type, [ $this, 'set_or_remove_ngg_post_thumbnail' ], PHP_INT_MAX - 1, 2 );
					},
					get_post_types_by_support( 'thumbnail' )
				);
			},
			11
		);
	}

	public function enqueue_block_editor() {
		\wp_enqueue_script(
			'nextgen-block-js',
			StaticAssets::get_url( 'IGW/Block/build/block.min.js', 'photocrati-nextgen_block#build/block.min.js' ),
			[ 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-compose' ],
			NGG_SCRIPT_VERSION,
			true
		);

		\wp_localize_script(
			'nextgen-block-js',
			'add_ngg_gallery_block_i18n',
			[
				'edit'   => \__( 'Edit', 'nggallery' ),
				'delete' => \__( 'Delete', 'nggallery' ),
				'create' => \__( 'Add NextGEN Gallery', 'nggallery' ),
				'h3'     => \__( 'NextGEN Gallery', 'nggallery' ),
				'nonce'  => \wp_create_nonce( 'ngg_attach_to_post_iframe' ),
			]
		);

		\wp_enqueue_style(
			'nextgen-block-css',
			StaticAssets::get_url( 'IGW/Block/editor.css', 'photocrati-nextgen_block#editor.css' ),
			[ 'wp-edit-blocks' ],
			NGG_SCRIPT_VERSION
		);
	}

	public function set_or_remove_ngg_post_thumbnail( $post, $request ) {
		$json   = @json_decode( $request->get_body() );
		$target = null;

		if ( ! is_object( $json ) ) {
			return;
		}

		// WordPress 5.3 changed how the featured-image metadata was submitted to the server.
		if ( isset( $json->meta ) && property_exists( $json->meta, 'ngg_post_thumbnail' ) ) {
			$target = $json->meta;
		} elseif ( property_exists( $json, 'ngg_post_thumbnail' ) ) {
			$target = $json;
		}

		if ( ! $target ) {
			return;
		}

		$storage = StorageManager::get_instance();

		// Was the post thumbnail removed?
		if ( ! $target->ngg_post_thumbnail ) {
			\delete_post_thumbnail( $post->ID );
			$storage->delete_from_media_library( $target->ngg_post_thumbnail );
		} else {
			// Was it added?
			$storage->set_post_thumbnail( $post->ID, $target->ngg_post_thumbnail );
		}
	}

	public function enqueue_post_thumbnails() {
		\add_thickbox();

		\wp_enqueue_script(
			'ngg-post-thumbnails',
			StaticAssets::get_url( 'IGW/Block/build/post-thumbnail.min.js', 'photocrati-nextgen_block#build/post-thumbnail.min.js' ),
			[ 'lodash', 'wp-element', 'wp-data', 'wp-editor', 'wp-components', 'wp-i18n', 'photocrati_ajax' ],
			NGG_SCRIPT_VERSION
		);

		$nonce = \wp_create_nonce( 'ngg_set_post_thumbnails' );

		\wp_localize_script(
			'ngg-post-thumbnails',
			'ngg_featured_image',
			[
				'modal_url' => \admin_url( "/media-upload.php?nonce={$nonce}&post_id=%post_id%&type=image&tab=nextgen&from=block-editor&TB_iframe=true" ),
			]
		);

		// Nonce verification is not necessary: this injects some extra CSS on the add/edit page/post page.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( preg_match( '/media-upload\.php/', $_SERVER['REQUEST_URI'] ) && 'nextgen' === $_GET['tab'] ) {
			\wp_add_inline_style( 'wp-admin', '#media-upload-header {display: none; }' );
			if ( isset( $_GET['from'] ) && 'block-editor' === sanitize_text_field( wp_unslash( $_GET['from'] ) ) ) {
				\add_action( 'admin_enqueue_scripts', [ $this, 'media_upload_footer' ] );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	public function media_upload_footer() {
		\wp_add_inline_script(
			'image-edit',
			'window.NGGSetAsThumbnail = top.set_ngg_post_thumbnail'
		);
	}
}
