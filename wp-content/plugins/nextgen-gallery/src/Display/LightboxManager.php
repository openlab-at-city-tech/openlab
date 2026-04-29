<?php

namespace Imagely\NGG\Display;

use Imagely\NGG\Settings\Settings;
use Imagely\NGG\DataTypes\Lightbox;

/**
 * Manager for handling lightbox integrations.
 */
class LightboxManager {

	/**
	 * Array of registered lightboxes.
	 *
	 * @var array
	 */
	private $lightboxes = [];

	/**
	 * Whether default lightboxes have been registered.
	 *
	 * @var bool
	 */
	private $has_registered_default_lightboxes = false;

	/**
	 * LightboxManager instance.
	 *
	 * @var LightboxManager
	 */
	private static $_instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return LightboxManager
	 */
	public static function get_instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new LightboxManager();
		}
		return self::$_instance;
	}

	/**
	 * Register default lightbox options.
	 */
	public function register_defaults() {
		$settings = Settings::get_instance();

		// No lightbox at all.
		$none        = new Lightbox( 'none' );
		$none->title = \__( 'None', 'nggallery' );
		$this->register( 'none', $none );

		$simplelightbox          = new Lightbox( 'simplelightbox' );
		$simplelightbox->title   = \__( 'Simplelightbox', 'nggallery' );
		$simplelightbox->code    = 'class="ngg-simplelightbox" rel="%GALLERY_NAME%"';
		$simplelightbox->styles  = [
			[ 'Lightbox/simplelightbox/simple-lightbox.css', 'photocrati-lightbox#simplelightbox/simple-lightbox.css' ],
			[ 'Lightbox/nextgen_tiktok.css', 'photocrati-lightbox#nextgen_tiktok.css' ],
		];
		$simplelightbox->scripts = [
			[ 'Lightbox/simplelightbox/simple-lightbox.js', 'photocrati-lightbox#simplelightbox/simple-lightbox.js' ],
			[ 'Lightbox/nextgen_tiktok_helper.js', 'photocrati-lightbox#nextgen_tiktok_helper.js' ],
			[ 'Lightbox/simplelightbox/nextgen_simple_lightbox_init.js', 'photocrati-lightbox#simplelightbox/nextgen_simple_lightbox_init.js' ],
		];
		$this->register( 'simplelightbox', $simplelightbox );

		$fancybox          = new Lightbox( 'fancybox' );
		$fancybox->title   = \__( 'Fancybox', 'nggallery' );
		$fancybox->code    = 'class="ngg-fancybox" rel="%GALLERY_NAME%"';
		$fancybox->styles  = [
			[ 'Lightbox/fancybox/jquery.fancybox-1.3.4.css', 'photocrati-lightbox#fancybox/jquery.fancybox-1.3.4.css' ],
			[ 'Lightbox/nextgen_tiktok.css', 'photocrati-lightbox#nextgen_tiktok.css' ],
		];
		$fancybox->scripts = [
			[ 'Lightbox/fancybox/jquery.easing-1.3.pack.js', 'photocrati-lightbox#fancybox/jquery.easing-1.3.pack.js' ],
			[ 'Lightbox/fancybox/jquery.fancybox-1.3.4.pack.js', 'photocrati-lightbox#fancybox/jquery.fancybox-1.3.4.pack.js' ],
			[ 'Lightbox/nextgen_tiktok_helper.js', 'photocrati-lightbox#nextgen_tiktok_helper.js' ],
			[ 'Lightbox/fancybox/nextgen_fancybox_init.js', 'photocrati-lightbox#fancybox/nextgen_fancybox_init.js' ],
		];
		$this->register( 'fancybox', $fancybox );

		$shutter          = new Lightbox( 'shutter' );
		$shutter->title   = \__( 'Shutter', 'nggallery' );
		$shutter->code    = 'class="shutterset_%GALLERY_NAME%"';
		$shutter->styles  = [
			[ 'Lightbox/shutter/shutter.css', 'photocrati-lightbox#shutter/shutter.css' ],
			[ 'Lightbox/nextgen_tiktok.css', 'photocrati-lightbox#nextgen_tiktok.css' ],
		];
		$shutter->scripts = [
			[ 'Lightbox/shutter/shutter.js', 'photocrati-lightbox#shutter/shutter.js' ],
			[ 'Lightbox/nextgen_tiktok_helper.js', 'photocrati-lightbox#nextgen_tiktok_helper.js' ],
			[ 'Lightbox/shutter/nextgen_shutter.js', 'photocrati-lightbox#shutter/nextgen_shutter.js' ],
		];
		$shutter->values  = [
			'nextgen_shutter_i18n' => [
				'msgLoading' => \__( 'L O A D I N G', 'nggallery' ),
				'msgClose'   => \__( 'Click to Close', 'nggallery' ),
			],
		];
		$this->register( 'shutter', $shutter );

		$shutter2          = new Lightbox( 'shutter2' );
		$shutter2->title   = \__( 'Shutter Reloaded', 'nggallery' );
		$shutter2->code    = 'class="shutterset_%GALLERY_NAME%"';
		$shutter2->styles  = [
			[ 'Lightbox/shutter_reloaded/shutter.css', 'photocrati-lightbox#shutter_reloaded/shutter.css' ],
			[ 'Lightbox/nextgen_tiktok.css', 'photocrati-lightbox#nextgen_tiktok.css' ],
		];
		$shutter2->scripts = [
			[ 'Lightbox/shutter_reloaded/shutter.js', 'photocrati-lightbox#shutter_reloaded/shutter.js' ],
			[ 'Lightbox/nextgen_tiktok_helper.js', 'photocrati-lightbox#nextgen_tiktok_helper.js' ],
			[ 'Lightbox/shutter_reloaded/nextgen_shutter_reloaded.js', 'photocrati-lightbox#shutter_reloaded/nextgen_shutter_reloaded.js' ],
		];
		$shutter2->values  = [
			'nextgen_shutter2_i18n' => [
				\__( 'Previous', 'nggallery' ),
				\__( 'Next', 'nggallery' ),
				\__( 'Close', 'nggallery' ),
				\__( 'Full Size', 'nggallery' ),
				\__( 'Fit to Screen', 'nggallery' ),
				\__( 'Image', 'nggallery' ),
				\__( 'of', 'nggallery' ),
				\__( 'Loading...', 'nggallery' ),
			],
		];
		$this->register( 'shutter2', $shutter2 );

		$thickbox          = new Lightbox( 'thickbox' );
		$thickbox->title   = \__( 'Thickbox', 'nggallery' );
		$thickbox->code    = "class='thickbox' rel='%GALLERY_NAME%'";
		$thickbox->styles  = [
			'wordpress#thickbox',
			[ 'Lightbox/nextgen_tiktok.css', 'photocrati-lightbox#nextgen_tiktok.css' ],
		];
		$thickbox->scripts = [
			[ 'Lightbox/nextgen_tiktok_helper.js', 'photocrati-lightbox#nextgen_tiktok_helper.js' ],
			[ 'Lightbox/thickbox/nextgen_thickbox_init.js', 'photocrati-lightbox#thickbox/nextgen_thickbox_init.js' ],
			[ 'Lightbox/thickbox/thickbox.js', 'photocrati-lightbox#thickbox/thickbox.js' ],
		];
		$thickbox->values  = [
			'nextgen_thickbox_i18n' => [
				'next'      => \__( 'Next &gt;', 'nggallery' ),
				'prev'      => \__( '&lt; Prev', 'nggallery' ),
				'image'     => \__( 'Image', 'nggallery' ),
				'of'        => \__( 'of', 'nggallery' ),
				'close'     => \__( 'Close', 'nggallery' ),
				'noiframes' => \__( 'This feature requires inline frames. You have iframes disabled or your browser does not support them.', 'nggallery' ),
			],
		];
		$this->register( 'thickbox', $thickbox );

		\wp_register_style(
			'ngg_tiktok_lightbox_style',
			StaticAssets::get_url( 'Lightbox/nextgen_tiktok.css', 'photocrati-lightbox#nextgen_tiktok.css' ),
			[],
			NGG_SCRIPT_VERSION
		);
		\wp_register_script(
			'ngg_tiktok_lightbox_helper',
			StaticAssets::get_url( 'Lightbox/nextgen_tiktok_helper.js', 'photocrati-lightbox#nextgen_tiktok_helper.js' ),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION,
			true
		);

		// Allow third parties to integrate.
		do_action( 'ngg_registered_default_lightboxes' );

		// Custom lightbox.
		$custom        = new Lightbox( 'custom' );
		$custom->title = \__( 'Custom', 'nggallery' );
		$custom->code  = $settings->get( 'thumbEffectCode', [] );

		// Get user-defined styles and scripts.
		$user_styles  = $settings->get( 'thumbEffectStyles', [] );
		$user_scripts = $settings->get( 'thumbEffectScripts', [] );

		// Ensure styles and scripts are arrays.
		if ( ! is_array( $user_styles ) ) {
			$user_styles = ! empty( $user_styles ) ? [ $user_styles ] : [];
		}
		if ( ! is_array( $user_scripts ) ) {
			$user_scripts = ! empty( $user_scripts ) ? [ $user_scripts ] : [];
		}

		// Include TikTok helper styles and scripts for custom lightbox (same as other lightboxes).
		$custom->styles  = array_merge(
			$user_styles,
			[
				[ 'Lightbox/nextgen_tiktok.css', 'photocrati-lightbox#nextgen_tiktok.css' ],
			]
		);
		$custom->scripts = array_merge(
			$user_scripts,
			[
				[ 'Lightbox/nextgen_tiktok_helper.js', 'photocrati-lightbox#nextgen_tiktok_helper.js' ],
				[ 'Lightbox/nextgen_custom_lightbox_tiktok_init.js', 'photocrati-lightbox#nextgen_custom_lightbox_tiktok_init.js' ],
			]
		);

		$this->register( 'custom_lightbox', $custom );

		$this->has_registered_default_lightboxes = true;
	}

	/**
	 * Register a lightbox.
	 *
	 * @param string   $name The lightbox name.
	 * @param Lightbox $lightbox The lightbox object.
	 * @return void
	 */
	public function register( $name, $lightbox ) {
		$lightbox->name            = $name;
		$this->lightboxes[ $name ] = $lightbox;
	}

	/**
	 * Deregister a lightbox.
	 *
	 * @param string $name The lightbox name.
	 * @return void
	 */
	public function deregister( $name ) {
		unset( $this->lightboxes[ $name ] );
	}

	/**
	 * Get a lightbox by name.
	 *
	 * @param string $name The lightbox name.
	 * @return Lightbox|void
	 */
	public function get( $name ) {
		if ( ! $this->has_registered_default_lightboxes ) {
			$this->register_defaults();
		}

		if ( isset( $this->lightboxes[ $name ] ) ) {
			return $this->lightboxes[ $name ];
		}
	}

	/**
	 * Returns which lightbox effect has been chosen
	 *
	 * Highslide and jQuery.Lightbox were removed in 2.0.73 due to licensing. If a user has selected either of those
	 * options we silently make their selection fallback to Fancybox.
	 *
	 * @return Lightbox
	 */
	public function get_selected() {
		$settings     = Settings::get_instance();
		$thumb_effect = $settings->get( 'thumbEffect' );

		// These have been removed from NextGEN; if they were previously selected update them to Fancybox.
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		if ( in_array( $thumb_effect, [ 'highslide', 'lightbox' ] ) ) {
			$settings->set( 'thumbEffect', 'fancybox' );
		}

		// Fallback to SimpleLightbox in case of failure.
		if ( ! $this->is_registered( $thumb_effect ) || empty( $thumb_effect ) ) {
			$settings->set( 'thumbEffect', 'simplelightbox' );
		}

		return $this->get( $settings->get( 'thumbEffect' ) );
	}

	/**
	 * Get all registered lightboxes.
	 *
	 * @return array
	 */
	public function get_all() {
		if ( ! $this->has_registered_default_lightboxes ) {
			$this->register_defaults();
		}

		return array_values( $this->lightboxes );
	}

	/**
	 * Check if a lightbox is registered.
	 *
	 * @param string $name The lightbox name.
	 * @return bool
	 */
	public function is_registered( $name ) {
		return ! is_null( $this->get( $name ) );
	}

	/**
	 * Maybe enqueue lightbox resources based on context.
	 */
	public function maybe_enqueue() {
		$settings             = Settings::get_instance();
		$thumb_effect_context = $settings->get( 'thumbEffectContext', '' );

		if ( 'nextgen_images' !== $thumb_effect_context ) {
			$this->enqueue();
		}
	}

	/**
	 * Enqueue lightbox resources.
	 *
	 * @param string $lightbox The lightbox name to enqueue.
	 * @return void
	 */
	public function enqueue( $lightbox = null ) {
		$settings             = Settings::get_instance();
		$thumb_effect_context = $settings->get( 'thumbEffectContext', '' );

		// If no lightbox has been provided, get the selected lightbox.
		if ( ! $lightbox ) {
			$lightbox = $this->get_selected();
		} else {
			$lightbox = $this->get( $lightbox );
		}

		if ( ! wp_script_is( 'ngg_lightbox_context' ) ) {
			wp_enqueue_script(
				'ngg_lightbox_context',
				StaticAssets::get_url( 'Lightbox/lightbox_context.js', 'photocrati-lightbox#lightbox_context.js' ),
				[ 'ngg_common', 'photocrati_ajax' ],
				NGG_SCRIPT_VERSION,
				true
			);
		}

		// TODO: move this into a shutter-reloaded 'value'.
		DisplayManager::add_script_data(
			'ngg_common',
			'nextgen_lightbox_settings',
			[
				'static_path' => trailingslashit( NGG_PLUGIN_URI ) . 'static/Lightbox/{placeholder}',
				'context'     => $thumb_effect_context,
			],
			true,
			true
		);

		// Enqueue lightbox resources, only if we have a configured lightbox.
		if ( $lightbox ) {
			// Add lightbox script data.
			if ( isset( $lightbox->values ) ) {
				foreach ( $lightbox->values as $name => $value ) {
					if ( empty( $value ) ) {
						continue;
					}
					DisplayManager::add_script_data(
						'ngg_lightbox_context',
						$name,
						$value
					);
				}
			}

			// Enqueue stylesheets.
			$styles_count = count( $lightbox->styles );
			for ( $i = 0; $i < $styles_count; $i++ ) {
				$legacy_src = '';
				$src        = $lightbox->styles[ $i ];
				if ( is_array( $src ) ) {
					list($src, $legacy_src) = $src;
				}
				if ( empty( $legacy_src ) ) {
					$legacy_src = $src;
					$src        = '';
				}
				if ( 0 === strpos( $src, 'wordpress#' ) ) {
					$parts = explode( 'wordpress#', $src );
					wp_enqueue_style( array_pop( $parts ) );
				} elseif ( 0 === strpos( $legacy_src, 'wordpress#' ) ) {
					$parts = explode( 'wordpress#', $legacy_src );
					wp_enqueue_style( array_pop( $parts ) );
				} elseif ( ! empty( $src ) || ! empty( $legacy_src ) ) {
					wp_enqueue_style( $lightbox->name . "-{$i}", $this->_handle_url( $src, $legacy_src ), [], NGG_SCRIPT_VERSION );
				}
			}

			// Enqueue scripts.
			$scripts_count = count( $lightbox->scripts );
			for ( $i = 0; $i < $scripts_count; $i++ ) {
				$legacy_src = '';
				$src        = $lightbox->scripts[ $i ];
				$handle     = $lightbox->name . "-{$i}";
				if ( is_array( $src ) ) {
					list($src, $legacy_src) = $src;
				}
				if ( empty( $legacy_src ) ) {
					$legacy_src = $src;
					$src        = '';
				}
				if ( 0 === strpos( $src, 'wordpress#' ) ) {
					$parts = explode( 'wordpress#', $src );
					wp_enqueue_script( array_pop( $parts ) );
				} elseif ( 0 === strpos( $legacy_src, 'wordpress#' ) ) {
					$parts = explode( 'wordpress#', $legacy_src );
					wp_enqueue_script( array_pop( $parts ) );
				} elseif ( ! empty( $src ) || ! empty( $legacy_src ) ) {
					wp_enqueue_script( $handle, $this->_handle_url( $src, $legacy_src ), [ 'ngg_lightbox_context' ], NGG_SCRIPT_VERSION, true );
				}
			}
		}
	}

	/**
	 * Parses certain paths through get_static_url
	 *
	 * @param string $url The URL to handle.
	 * @param string $legacy_module_id The legacy module ID.
	 * @return string Resulting URL
	 */
	public static function _handle_url( $url, $legacy_module_id = '' ) {
		if ( 0 !== strpos( $url, '/' )
		&& 0 !== strpos( $url, 'wordpress#' )
		&& 0 !== strpos( $url, 'http://' )
		&& 0 !== strpos( $url, 'https://' ) ) {
			if ( ! empty( $legacy_module_id ) && empty( $url ) ) {
				$url = StaticPopeAssets::get_url( $legacy_module_id );
			} else {
				$url = StaticAssets::get_url( $url, $legacy_module_id );
			}
		} elseif ( strpos( $url, '/' ) === 0 ) {
			$url = home_url( $url );
		}

		return $url;
	}

	/**
	 * Deregister all lightboxes.
	 */
	public function deregister_all() {
		$this->lightboxes                        = [];
		$this->has_registered_default_lightboxes = false;
	}

	/**
	 * Add script data.
	 *
	 * @TODO Remove this when Pro no longer requires it
	 * @deprecated
	 * @param string $handle The script handle.
	 * @param string $object_name The object name.
	 * @param mixed  $object_value The object value.
	 * @param bool   $define Whether to define the object.
	 * @param bool   $override Whether to override existing data.
	 * @return bool
	 */
	public function _add_script_data( $handle, $object_name, $object_value, $define = true, $override = false ) {
		return DisplayManager::add_script_data( $handle, $object_name, $object_value, $define, $override );
	}
}
