<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DisplayTypes\Albums\SharedController;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Settings\Settings;

class ExtendedAlbum extends SharedController {

	public function __construct() {
		$this->legacy_template = 'photocrati-nextgen_basic_album#extended';
		$this->template        = 'ExtendedAlbum/extended';
	}

	public function get_preview_image_url() {
		return StaticAssets::get_url( 'ExtendedAlbum/extended_preview.jpg' );
	}

	public function get_default_settings() {
		$settings         = Settings::get_instance();
		$default_template = isset( $entity->settings['template'] ) ? 'default' : 'default-view.php';

		return \apply_filters(
			'ngg_extended_album_default_settings',
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
				'thumbnail_height'            => 200,
				'thumbnail_quality'           => $settings->get( 'thumbquality' ),
				'thumbnail_watermark'         => 0,
				'thumbnail_width'             => 300,
			]
		);
	}

	public function get_template_directory_name(): string {
		return 'ExtendedAlbum';
	}

	public function install( $reset = false ) {
		$this->install_display_type(
			NGG_BASIC_EXTENDED_ALBUM,
			[
				'title'          => __( 'NextGEN Basic Extended Album', 'nggallery' ),
				'entity_types'   => [ 'album', 'gallery' ],
				'default_source' => 'albums',
				'view_order'     => NGG_DISPLAY_PRIORITY_BASE + 210,
				'aliases'        => [
					'extended_album',
					'basic_extended_album',
					'nextgen_basic_extended_album',
					'photocrati-nextgen_basic_extended_album',
				],
				'settings'       => $this->get_default_settings(),
			],
			$reset
		);
	}
}
