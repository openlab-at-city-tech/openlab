<?php

namespace Imagely\NGG\IGW;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Util\URL;

/**
 * Handles the NextGEN block and post thumbnail
 */
class BlockManager {

	/**
	 * Instance cache.
	 *
	 * @var BlockManager|null
	 */
	protected static $instance = null;

	/**
	 * Gets the BlockManager instance.
	 *
	 * @return BlockManager
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new BlockManager();
		}
		return self::$instance;
	}

	/**
	 * Register the hooks for the BlockManager.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_post_thumbnails' ], 1 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_gallery_conversion' ], 10 );
		add_action( 'enqueue_block_assets', [ $this, 'ngg_enqueue_block_assets' ] );

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

	/**
	 * Register blocks from block.json metadata.
	 *
	 * @return void
	 */
	public function register_blocks() {
		// Register editor styles (shared by blocks)
		wp_register_style(
			'imagely-nextgen-gallery-editor-style',
			StaticAssets::get_url( 'IGW/Block/editor.css', 'photocrati-nextgen_block#editor.css' ),
			[ 'wp-edit-blocks' ],
			NGG_SCRIPT_VERSION
		);

		// Add imagelyApp global data needed by adminApp components
		// Use centralized static method from App class
		$imagely_app_data = \Imagely\NGG\Admin\App::get_imagely_app_data();

		// Register unified media block (Gallery + Album)
		$media_block_asset_file = NGG_PLUGIN_DIR . '/static/IGW/Block/build/block-imagely-block.asset.php';
		$media_block_asset      = file_exists( $media_block_asset_file ) ? include $media_block_asset_file : [
			'dependencies' => [],
			'version'      => NGG_SCRIPT_VERSION,
		];

		\wp_register_script(
			'imagely-main-block-simple-editor-script',
			StaticAssets::get_url( 'IGW/Block/build/block-imagely-block.min.js', 'photocrati-nextgen_block#build/block-imagely-block.min.js' ),
			$media_block_asset['dependencies'],
			$media_block_asset['version'],
			true
		);

		// Register the adminApp styles (shared by unified block)
		wp_register_style(
			'imagely-nextgen-block-styles',
			StaticAssets::get_url( 'IGW/Block/build/block-imagely-block.min.css', 'photocrati-nextgen_block#build/block-imagely-block.min.css' ),
			[],
			$media_block_asset['version']
		);

		// Localize script with imagelyApp data
		wp_localize_script( 'imagely-main-block-simple-editor-script', 'imagelyApp', $imagely_app_data );

		// Load translations for the block script
		wp_set_script_translations(
			'imagely-main-block-simple-editor-script',
			'nggallery',
			NGG_PLUGIN_DIR . 'static/I18N'
		);

		// Add inline script for i18n
		\wp_add_inline_script(
			'imagely-main-block-simple-editor-script',
			'window.add_ngg_gallery_block_i18n = ' . wp_json_encode(
				[
					'edit'      => \__( 'Edit', 'nggallery' ),
					'delete'    => \__( 'Delete', 'nggallery' ),
					'create'    => \__( 'Add Imagely', 'nggallery' ),
					'h3'        => \__( 'Imagely', 'nggallery' ),
					'nonce'     => \wp_create_nonce( 'ngg_attach_to_post_iframe' ),
					'restNonce' => \wp_create_nonce( 'wp_rest' ),
					'adminUrl'  => \admin_url( 'admin.php' ),
				]
			) . ';',
			'before'
		);

		// Read block.json and register block
		$media_block_json_file                   = NGG_PLUGIN_DIR . '/static/IGW/Block/build/block-imagely-block.block.json';
		$media_metadata                          = file_exists( $media_block_json_file ) ? json_decode( file_get_contents( $media_block_json_file ), true ) : [];
		$media_metadata['editor_script_handles'] = [ 'imagely-main-block-simple-editor-script' ];
		$media_metadata['editor_style_handles']  = [ 'imagely-nextgen-gallery-editor-style', 'imagely-nextgen-block-styles' ];

		register_block_type( 'imagely/main-block', $media_metadata );

		// Register legacy block only for existing installations
		if ( $this->is_existing_installation() ) {
			$legacy_block_asset_file = NGG_PLUGIN_DIR . '/static/IGW/Block/build/block.asset.php';
			$legacy_block_asset      = file_exists( $legacy_block_asset_file ) ? include $legacy_block_asset_file : [
				'dependencies' => [],
				'version'      => NGG_SCRIPT_VERSION,
			];

			\wp_register_script(
				'imagely-nextgen-gallery-legacy-editor-script',
				StaticAssets::get_url( 'IGW/Block/build/block.min.js', 'photocrati-nextgen_block#build/block.min.js' ),
				$legacy_block_asset['dependencies'],
				$legacy_block_asset['version'],
				true
			);

			// Localize script for legacy block
			\wp_add_inline_script(
				'imagely-nextgen-gallery-legacy-editor-script',
				'window.add_ngg_gallery_block_i18n = ' . wp_json_encode(
					[
						'edit'      => \__( 'Edit', 'nggallery' ),
						'delete'    => \__( 'Delete', 'nggallery' ),
						'create'    => \__( 'Add NextGEN Gallery', 'nggallery' ),
						'h3'        => \__( 'NextGEN Gallery', 'nggallery' ),
						'nonce'     => \wp_create_nonce( 'ngg_attach_to_post_iframe' ),
						'restNonce' => \wp_create_nonce( 'wp_rest' ),
						'adminUrl'  => \admin_url( 'admin.php' ),
					]
				) . ';',
				'before'
			);

			$legacy_block_json_file                   = NGG_PLUGIN_DIR . '/static/IGW/Block/build/block.block.json';
			$legacy_metadata                          = file_exists( $legacy_block_json_file ) ? json_decode( file_get_contents( $legacy_block_json_file ), true ) : [];
			$legacy_metadata['editor_script_handles'] = [ 'imagely-nextgen-gallery-legacy-editor-script' ];
			$legacy_metadata['editor_style_handles']  = [ 'imagely-nextgen-gallery-editor-style' ];

			register_block_type( 'imagely/nextgen-gallery', $legacy_metadata );
		}
	}

	/**
	 * Check if this is an existing installation (not fresh).
	 *
	 * This method reads the 'ngg_installation_type' setting which is:
	 * - Set to 'fresh' by default for new installations
	 * - Set to 'existing' by the AddGalleryDates migration when it migrates data from existing galleries/albums
	 *
	 * @return bool True if existing installation, false if fresh install.
	 */
	private function is_existing_installation() {
		$settings          = \Imagely\NGG\Settings\Settings::get_instance();
		$installation_type = $settings->get( 'ngg_installation_type', 'fresh' );
		return 'existing' === $installation_type;
	}

	/**
	 * Enqueue the block assets.
	 *
	 * This method is now primarily a placeholder. Block registration is handled
	 * in register_blocks() via block.json metadata. Legacy block conditionally
	 * registered only for existing installations.
	 *
	 * @return void
	 */
	public function ngg_enqueue_block_assets() {
		// All block registration now handled in register_blocks() method
		// This method is kept for backward compatibility and potential future enqueue hooks
	}

	/**
	 * Set or remove the ngg_post_thumbnail for a post.
	 *
	 * @param \WP_Post         $post The post object.
	 * @param \WP_REST_Request $request The REST request object.
	 * @return void
	 */
	public function set_or_remove_ngg_post_thumbnail( $post, $request ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
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

		if ( 0 === $target->ngg_post_thumbnail ) { // Thumbnail ID is zero, skip deleting.
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

	/**
	 * Enqueue the gallery conversion script.
	 *
	 * This script extends the WordPress Gallery block with a "Convert to Imagely" button.
	 *
	 * @return void
	 */
	public function enqueue_gallery_conversion() {
		$asset_file = NGG_PLUGIN_DIR . '/static/IGW/Block/build/gallery-conversion.asset.php';
		$asset      = file_exists( $asset_file ) ? include $asset_file : [
			'dependencies' => [],
			'version'      => NGG_SCRIPT_VERSION,
		];

		\wp_enqueue_script(
			'imagely-gallery-conversion',
			StaticAssets::get_url( 'IGW/Block/build/gallery-conversion.min.js', 'photocrati-nextgen_block#build/gallery-conversion.min.js' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		// Localize script with conversion data.
		\wp_localize_script(
			'imagely-gallery-conversion',
			'imagelyConvertData',
			[
				'convertApiUrl'       => '/imagely/v1/convert-gallery/single',
				'convertRestNonce'    => \wp_create_nonce( 'wp_rest' ),
				'postId'              => \get_the_ID(),
				'panelTitle'          => \__( 'Imagely Gallery', 'nggallery' ),
				'contentHeading'      => \__( 'Convert to Imagely', 'nggallery' ),
				'contentDescription'  => \__( 'Convert this WordPress Gallery to an Imagely Gallery for more features and customization options.', 'nggallery' ),
				'contentButtonText'   => \__( 'Convert to Imagely Gallery', 'nggallery' ),
				'convertLoading'      => \__( 'Converting...', 'nggallery' ),
				'confirmationMessage' => \__( 'Are you sure you want to convert this WordPress Gallery to an Imagely Gallery? This action cannot be undone.', 'nggallery' ),
				'invalidBlockMessage' => \__( 'Invalid block. Please select a WordPress Gallery block.', 'nggallery' ),
				'noImagesMessage'     => \__( 'No images found in the gallery. Please add images before converting.', 'nggallery' ),
				'errorMessage'        => \__( 'An error occurred while converting the gallery.', 'nggallery' ),
			]
		);

		// Load translations for the gallery conversion script.
		\wp_set_script_translations(
			'imagely-gallery-conversion',
			'nggallery',
			NGG_PLUGIN_DIR . 'static/I18N'
		);
	}

	/**
	 * Enqueue the post thumbnails.
	 *
	 * @return void
	 */
	public function enqueue_post_thumbnails() {
		// Load new TypeScript build from adminApp
		$asset_file = NGG_PLUGIN_DIR . '/static/IGW/Block/build/post-thumbnail.asset.php';
		$asset      = file_exists( $asset_file ) ? include $asset_file : [
			'dependencies' => [],
			'version'      => NGG_SCRIPT_VERSION,
		];

		\wp_enqueue_script(
			'ngg-post-thumbnails',
			StaticAssets::get_url( 'IGW/Block/build/post-thumbnail.min.js', 'photocrati-nextgen_block#build/post-thumbnail.min.js' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		// Localize with REST URLs for the new component-based picker
		\wp_localize_script(
			'ngg-post-thumbnails',
			'nggFeaturedImage',
			[
				'restUrl'      => \rest_url( 'ngg/v1/admin/attach_to_post/' ),
				'imageRestUrl' => \rest_url( 'ngg/v1/admin/block/image/' ),
				'restNonce'    => \wp_create_nonce( 'wp_rest' ),
			]
		);

		// Load translations for the post-thumbnail script
		\wp_set_script_translations(
			'ngg-post-thumbnails',
			'nggallery',
			NGG_PLUGIN_DIR . 'static/I18N'
		);
	}
}
