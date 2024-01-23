<?php

namespace Imagely\NGG\Settings;

class Installer {

	private $global_settings = [];
	private $local_settings  = [];

	public $blog_settings = null;
	public $site_settings = null;

	public function __construct() {
		$existing_options = \get_option( 'ngg_options', [] );

		$this->blog_settings = Settings::get_instance();
		$this->site_settings = GlobalSettings::get_instance();

		$this->global_settings = \apply_filters(
			'ngg_default_global_settings',
			[
				'gallerypath'          => implode( DIRECTORY_SEPARATOR, [ 'wp-content', 'uploads', 'sites', '%BLOG_ID%', 'nggallery' ] ) . DIRECTORY_SEPARATOR,
				'wpmuRoles'            => false,
				'wpmuImportFolder'     => false,
				'wpmuZipUpload'        => false,
				'wpmuQuotaCheck'       => false,
				'maximum_entity_count' => 500,
				'router_param_slug'    => 'nggallery',
			]
		);

		$this->local_settings = \apply_filters(
			'ngg_default_settings',
			[
				'gallerypath'                       => 'wp-content' . DIRECTORY_SEPARATOR . 'gallery' . DIRECTORY_SEPARATOR,
				'deleteImg'                         => true,        // delete Images.
				'usePermalinks'                     => false,       // use permalinks for parameters.
				'permalinkSlug'                     => 'nggallery', // the default slug for permalinks.
				'graphicLibrary'                    => 'gd',        // default graphic library.
				'useMediaRSS'                       => false,       // activate the global Media RSS file.
				'galleries_in_feeds'                => false,   // enables rendered gallery output in RSS & Atom feeds.

			// Tags / categories.
				'activateTags'                      => 0,      // append related images.
				'appendType'                        => 'tags', // look for category or tags.
				'maxImages'                         => 7,      // number of images to show.
				'relatedHeading'                    => '<h3>' . \__( 'Related Images', 'nggallery' ) . ':</h3>', // subheading for related images.

			// Thumbnail Settings.
				'thumbwidth'                        => 240,  // Thumb Width.
				'thumbheight'                       => 160,  // Thumb height.
				'thumbfix'                          => true, // Fix the dimension.
				'thumbquality'                      => 100,  // Thumb Quality.

			// Image Settings.
				'imgWidth'                          => 1800, // Image Width.
				'imgHeight'                         => 1200, // Image height.
				'imgQuality'                        => 100,  // Image Quality.
				'imgBackup'                         => true, // Create a backup.
				'imgAutoResize'                     => true, // Resize after upload.

			// Gallery Settings.
				'galImages'                         => '24',  // Number of images per page.
				'galPagedGalleries'                 => 0,     // Number of galleries per page (in a album).
				'galColumns'                        => 0,     // Number of columns for the gallery.
				'galShowSlide'                      => false, // Show slideshow.
				'galTextSlide'                      => \__( 'View Slideshow', 'nggallery' ), // Text for slideshow.
				'galTextGallery'                    => \__( 'View Thumbnails', 'nggallery' ), // Text for gallery.
				'galShowOrder'                      => 'gallery',   // Show order.
				'galSort'                           => 'sortorder', // Sort order.
				'galSortDir'                        => 'ASC',       // Sort direction.
				'galNoPages'                        => true,        // use no subpages for gallery.
				'galImgBrowser'                     => 0, // Thumbnails feature: show ImageBrowser in place of lightbox.
				'galHiddenImg'                      => 0, // For paged galleries we can hide image.
				'galAjaxNav'                        => 1, // Thumbnails feature: use ajax pagination.

			// Thumbnail Effect.
				'thumbEffect'                       => 'simplelightbox',
				'thumbCode'                         => 'class="ngg-simplelightbox" rel="%GALLERY_NAME%"',
				'thumbEffectContext'                => 'nextgen_images',

				// Watermark settings.
				'watermark_automatically_at_upload' => 0,
				'wmPos'                             => 'midCenter',             // Position.
				'wmXpos'                            => 15,                      // X Pos.
				'wmYpos'                            => 5,                       // Y Pos.
				'wmType'                            => 'text',                  // Type : 'image' / 'text'.
				'wmPath'                            => '',                      // Path to image.
				'wmFont'                            => 'arial.ttf',             // Font type.
				'wmSize'                            => 30,                      // Font Size.
				'wmText'                            => \get_option( 'blogname' ), // Text.
				'wmColor'                           => 'ffffff',                // Font Color.
				'wmOpaque'                          => '33',                    // Font Opaque.

			// Image Rotator settings.
				'slideFX'                           => 'fade',
				'irWidth'                           => 750,
				'irHeight'                          => 500,
				'irRotatetime'                      => 5,

				// Misc.
				'dynamic_image_filename_separator_use_dash' => ! isset( $existing_options['gallerypath'] ),

				// It is known that WPEngine disables 'order by rand()' by default, but exposes it as an option to users.
				'use_alternate_random_method'       => ( function_exists( 'is_wpe' ) && \is_wpe() ),

				// Prevent conflicts with other plugins that enqueue fontawesome.
				'disable_fontawesome'               => false,

				// Prevent the /ngg_tag/ page from being enabled.
				'disable_ngg_tags_page'             => false,

				// Duration of caching of 'random' widgets image IDs.
				'random_widget_cache_ttl'           => 30,

				// Path for the dynamic thumbnails' generator/controller.
				'dynamic_thumbnail_slug'            => 'nextgen-image',

				// Path for the dynamic stylesheets controller.
				'dynamic_stylesheet_slug'           => 'nextgen-dcss',

				// Router internal configuration.
				'router_param_separator'            => '--',
				'router_param_prefix'               => '',
				'router_param_slug'                 => 'nggallery',

				// Legacy POPE compatibility & former option handlers.
				'frame_event_cookie_name'           => 'X-Frame-Events',
				'mvc_template_dir'                  => '/templates',
				'mvc_template_dirname'              => '/templates',
				'mvc_static_dirname'                => '/static',
				'mvc_static_dir'                    => '/static',
				'jquery_ui_theme'                   => 'jquery-ui-nextgen',
				'jquery_ui_theme_version'           => '1.8',
			]
		);

		if ( \is_multisite() ) {
			if ( $options = \get_site_option( 'ngg_options' ) ) {
				$gallerypath = $options['gallerypath'];
			} else {
				$gallerypath = $this->global_settings['gallerypath'];
			}

			$this->local_settings['gallerypath'] = $this->gallerypath_replace( $gallerypath );
		}
	}

	public function install_global_settings( $reset = false ) {
		foreach ( $this->global_settings as $key => $value ) {
			if ( $reset ) {
				$this->site_settings->set( $key, null );
			}
			$this->site_settings->set_default_value( $key, $value );
		}
	}

	public function install_local_settings( $reset = false ) {
		foreach ( $this->local_settings as $key => $value ) {
			if ( $reset ) {
				$this->blog_settings->set( $key, null );
			}
			$this->blog_settings->set_default_value( $key, $value );
		}

		if ( is_multisite() ) {
			// If this is already network activated we just need to use the existing setting
			// Note: attempting to use Imagely\NGG\Settings\GlobalSettings here may result in an infinite loop,
			// so get_site_option() is used to check.
			if ( $options = \get_site_option( 'ngg_options' ) ) {
				$gallerypath = $options['gallerypath'];
			} else {
				$gallerypath = $this->global_settings['gallerypath'];
			}

			$gallerypath = $this->gallerypath_replace( $gallerypath );

			// a gallerypath setting has already been set, so we explicitly set a default AND set a new value.
			$this->blog_settings->set_default_value( 'gallerypath', $gallerypath );
			if ( $reset ) {
				$this->blog_settings->set( 'gallerypath', $gallerypath );
			}
		}
	}

	public function install( $reset = false ) {
		$this->install_global_settings( $reset );
		$this->install_local_settings( $reset );
	}

	public function get_global_defaults() {
		return $this->global_settings;
	}

	public function get_local_defaults() {
		return $this->local_settings;
	}

	public function gallerypath_replace( $gallerypath ) {
		$gallerypath = str_replace( '%BLOG_NAME%', \get_bloginfo( 'name' ), $gallerypath );
		$gallerypath = str_replace( '%BLOG_ID%', \get_current_blog_id(), $gallerypath );
		$gallerypath = str_replace( '%SITE_ID%', \get_current_blog_id(), $gallerypath );

		return $gallerypath;
	}
}
