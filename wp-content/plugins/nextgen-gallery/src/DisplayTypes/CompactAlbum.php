<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DisplayTypes\Albums\SharedController;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Settings\Settings;

/**
 * Compact Album display type class.
 *
 * Handles the display and configuration of compact album layouts.
 */
class CompactAlbum extends SharedController {

	/**
	 * Constructor.
	 *
	 * Initializes the compact album display type with template settings.
	 */
	public function __construct() {
		$this->legacy_template = 'photocrati-nextgen_basic_album#compact';
		$this->template        = 'CompactAlbum/compact';
	}

	/**
	 * Gets the URL for the preview image of this display type.
	 *
	 * @return string The URL to the preview image.
	 */
	public function get_preview_image_url() {
		return StaticAssets::get_url( 'CompactAlbum/compact_preview.jpg' );
	}

	/**
	 * Gets the default settings for the compact album display type.
	 *
	 * @return array The default settings array.
	 */
	public function get_default_settings() {
		$settings         = Settings::get_instance();
		$default_template = isset( $entity->settings['template'] ) ? 'default' : 'default-view.php';

		return \apply_filters(
			'ngg_compact_album_default_settings',
			[
				'disable_pagination'          => 0,
				'display_view'                => $default_template,
				'enable_breadcrumbs'          => 1,
				'enable_descriptions'         => 0,
				'galleries_per_page'          => $settings->get( 'galPagedGalleries' ),
				'gallery_display_template'    => '',
				'gallery_display_type'        => NGG_BASIC_THUMBNAILS,
				'ngg_triggers_display'        => 'never',
				'open_gallery_in_lightbox'    => 0,
				'override_thumbnail_settings' => 1,
				'template'                    => '',
				'thumbnail_crop'              => 1,
				'thumbnail_height'            => 160,
				'thumbnail_quality'           => $settings->get( 'thumbquality' ),
				'thumbnail_watermark'         => 0,
				'thumbnail_width'             => 240,
			]
		);
	}

	/**
	 * Gets the template directory name for this display type.
	 *
	 * @return string The template directory name.
	 */
	public function get_template_directory_name(): string {
		return 'CompactAlbum';
	}

	/**
	 * Installs the compact album display type.
	 *
	 * @param bool $reset Whether to reset existing installation. Default false.
	 */
	public function install( $reset = false ) {
		$this->install_display_type(
			NGG_BASIC_COMPACT_ALBUM,
			[
				'title'          => __( 'NextGEN Basic Compact Album', 'nggallery' ),
				'entity_types'   => [ 'album', 'gallery' ],
				'default_source' => 'albums',
				'view_order'     => NGG_DISPLAY_PRIORITY_BASE + 200,
				'aliases'        => [
					'compact_album',
					'basic_album_compact',
					'basic_compact_album',
					'nextgen_basic_album',
					'photocrati-nextgen_basic_compact_album',
				],
				'settings'       => $this->get_default_settings(),
			],
			$reset
		);
	}
}
