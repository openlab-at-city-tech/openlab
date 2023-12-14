<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DisplayType\Controller;

/**
 * This class provides a lazy-loading wrapper to the legacy "nggImage" class for use in legacy style templates
 */
class LegacyImage {

	public $_cache;         // cache of retrieved values.
	public $_settings;      // I_Settings_Manager cache.
	public $_storage;       // I_Gallery_Storage cache.
	public $_galleries;     // cache of Imagely\NGG\DataTypes\Mapper (plural).
	public $_orig_image;    // original provided image.
	public $_orig_image_id; // original image ID.
	public $_cache_overrides; // allow for forcing variable values.
	public $_legacy = false;
	public $_displayed_gallery; // cached object.

	/**
	 * Constructor. Converts the image class into an array and fills from defaults any missing values
	 *
	 * @param object $image Individual result from displayed_gallery->get_entities()
	 * @param object $displayed_gallery Displayed gallery -- MAY BE NULL
	 * @param bool   $legacy Whether the image source is from NextGen Legacy or NextGen
	 * @return void
	 */
	public function __construct( $image, $displayed_gallery = null, $legacy = false ) {
		// for clarity.
		if ( $displayed_gallery && isset( $displayed_gallery->display_settings['number_of_columns'] ) ) {
			$columns = $displayed_gallery->display_settings['number_of_columns'];
		} else {
			$columns = 0;
		}

		// Public variables.
		$defaults = [
			'errmsg'      => '',    // Error message to display, if any.
			'error'       => false, // Error state.
			'imageURL'    => '',    // URL Path to the image.
			'thumbURL'    => '',    // URL Path to the thumbnail.
			'imagePath'   => '',    // Server Path to the image.
			'thumbPath'   => '',    // Server Path to the thumbnail.
			'href'        => '',    // A href link code.

			// Mostly constant.
			'thumbPrefix' => 'thumbs_',  // FolderPrefix to the thumbnail.
			'thumbFolder' => '/thumbs/', // Foldername to the thumbnail.

			// Image Data.
			'galleryid'   => 0,  // Gallery ID.
			'pid'         => 0,  // Image ID.
			'filename'    => '', // Image filename.
			'description' => '', // Image description.
			'alttext'     => '', // Image alttext.
			'imagedate'   => '', // Image date/time.
			'exclude'     => '', // Image exclude.
			'thumbcode'   => '', // Image effect code.

			// Gallery Data.
			'name'        => '', // Gallery name.
			'path'        => '', // Gallery path.
			'title'       => '', // Gallery title.
			'pageid'      => 0, // Gallery page ID.
			'previewpic'  => 0,  // Gallery preview pic.

			'style'       => ( $columns > 0 ) ? 'style="width:' . floor( 100 / $columns ) . '%;"' : '',
			'hidden'      => false,
			'permalink'   => '',
			'tags'        => '',
		];

		// convert the image to an array and apply the defaults.
		$this->_orig_image = $image;
		$image             = (array) $image;
		foreach ( $defaults as $key => $val ) {
			if ( ! isset( $image[ $key ] ) ) {
				$image[ $key ] = $val;
			}
		}

		// cache the results.
		ksort( $image );
		$id_field                 = ( ! empty( $image['id_field'] ) ? $image['id_field'] : 'pid' );
		$this->_cache             = (array) apply_filters( 'ngg_image_object', (object) $image, $image[ $id_field ] );
		$this->_orig_image_id     = $image[ $id_field ];
		$this->_legacy            = $legacy;
		$this->_displayed_gallery = $displayed_gallery;
	}

	public function __set( $name, $value ) {
		$this->_cache[ $name ] = $value;
	}

	public function __isset( $name ) {
		return isset( $this->_cache[ $name ] );
	}

	public function __unset( $name ) {
		unset( $this->_cache[ $name ] );
	}

	/**
	 * Lazy-loader for image variables.
	 *
	 * @param string $name Parameter name.
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( isset( $this->_cache_overrides[ $name ] ) ) {
			return $this->_cache_overrides[ $name ];
		}

		// at the bottom we default to returning $this->_cache[$name].
		switch ( $name ) {
			case 'alttext':
				$this->_cache['alttext'] = ( empty( $this->_cache['alttext'] ) ) ? ' ' : html_entity_decode( stripslashes( $this->_cache['alttext'] ) );
				return $this->_cache['alttext'];

			case 'author':
				$gallery                = $this->get_legacy_gallery( $this->__get( 'galleryid' ) );
				$this->_cache['author'] = $gallery->name;
				return $this->_cache['author'];

			case 'caption':
				$caption = html_entity_decode( stripslashes( $this->__get( 'description' ) ) );
				if ( empty( $caption ) ) {
					$caption = '&nbsp;';
				}
				$this->_cache['caption'] = $caption;
				return $this->_cache['caption'];

			case 'description':
				$this->_cache['description'] = ( empty( $this->_cache['description'] ) ) ? ' ' : html_entity_decode( stripslashes( $this->_cache['description'] ) );
				return $this->_cache['description'];

			case 'galdesc':
				$gallery                 = $this->get_legacy_gallery( $this->__get( 'galleryid' ) );
				$this->_cache['galdesc'] = $gallery->name;
				return $this->_cache['galdesc'];

			case 'gid':
				$gallery             = $this->get_legacy_gallery( $this->__get( 'galleryid' ) );
				$this->_cache['gid'] = $gallery->{$gallery->id_field};
				return $this->_cache['gid'];

			case 'href':
				return $this->__get( 'imageHTML' );

			case 'id':
				return $this->_orig_image_id;

			case 'imageHTML':
				$tmp                       = '<a href="' . $this->__get( 'imageURL' ) . '" title="'
					. htmlspecialchars( stripslashes( $this->__get( 'description' ) ) )
					. '" ' . $this->get_thumbcode( $this->__get( 'name' ) ) . '>' . '<img alt="' . $this->__get( 'alttext' )
					. '" src="' . $this->__get( 'imageURL' ) . '"/>' . '</a>';
				$this->_cache['href']      = $tmp;
				$this->_cache['imageHTML'] = $tmp;
				return $this->_cache['imageHTML'];

			case 'imagePath':
				$storage                   = $this->get_storage();
				$this->_cache['imagePath'] = $storage->get_image_abspath( $this->_orig_image, 'full' );
				return $this->_cache['imagePath'];

			case 'imageURL':
				$storage                  = $this->get_storage();
				$this->_cache['imageURL'] = $storage->get_image_url( $this->_orig_image, 'full' );
				return $this->_cache['imageURL'];

			case 'linktitle':
				$this->_cache['linktitle'] = htmlspecialchars( stripslashes( $this->__get( 'description' ) ) );
				return $this->_cache['linktitle'];

			case 'name':
				$gallery              = $this->get_legacy_gallery( $this->__get( 'galleryid' ) );
				$this->_cache['name'] = $gallery->name;
				return $this->_cache['name'];

			case 'pageid':
				$gallery                = $this->get_legacy_gallery( $this->__get( 'galleryid' ) );
				$this->_cache['pageid'] = $gallery->name;
				return $this->_cache['pageid'];

			case 'path':
				$gallery              = $this->get_legacy_gallery( $this->__get( 'galleryid' ) );
				$this->_cache['path'] = $gallery->name;
				return $this->_cache['path'];

			case 'permalink':
				$this->_cache['permalink'] = $this->__get( 'imageURL' );
				return $this->_cache['permalink'];

			case 'pid':
				return $this->_orig_image_id;

			case 'id_field':
				$this->_cache['id_field'] = ( ! empty( $this->_orig_image->id_field ) ? $this->_orig_image->id_field : 'pid' );
				return $this->_cache['id_field'];

			case 'pidlink':
				$application             = \Imagely\NGG\Util\Router::get_instance()->get_routed_app();
				$this->_cache['pidlink'] = $application->set_parameter_value(
					'pid',
					$this->__get( 'image_slug' ),
					null,
					false,
					$application->get_routed_url( true )
				);
				return $this->_cache['pidlink'];

			case 'previewpic':
				$gallery                    = $this->get_legacy_gallery( $this->__get( 'galleryid' ) );
				$this->_cache['previewpic'] = $gallery->name;
				return $this->_cache['previewpic'];

			case 'size':
				$w = 0;
				$h = 0;

				if ( $this->_displayed_gallery && isset( $this->_displayed_gallery->display_settings ) ) {
					$ds = $this->_displayed_gallery->display_settings;
					if ( isset( $ds['override_thumbnail_settings'] ) && $ds['override_thumbnail_settings'] ) {
						$w = $ds['thumbnail_width'];
						$h = $ds['thumbnail_height'];
					}
				}
				if ( ! $w || ! $h ) {
					if ( is_string( $this->_orig_image->meta_data ) ) {
						$this->_orig_image = \Imagely\NGG\Util\Serializable::unserialize(
							$this->_orig_image->meta_data
						);
					}
					if ( ! isset( $this->_orig_image->meta_data['thumbnail'] ) ) {
						$storage = $this->get_storage();
						$storage->generate_thumbnail( $this->_orig_image );
					}
					$w = $this->_orig_image->meta_data['thumbnail']['width'];
					$h = $this->_orig_image->meta_data['thumbnail']['height'];
				}

				return "width='{$w}' height='{$h}'";

			case 'slug':
				$gallery              = $this->get_legacy_gallery( $this->__get( 'galleryid' ) );
				$this->_cache['slug'] = $gallery->name;
				return $this->_cache['slug'];

			case 'tags':
				$this->_cache['tags'] = wp_get_object_terms( $this->__get( 'id' ), 'ngg_tag', 'fields=all' );
				return $this->_cache['tags'];

			case 'thumbHTML':
				$tmp                       = '<a href="' . $this->__get( 'imageURL' ) . '" title="'
					. htmlspecialchars( stripslashes( $this->__get( 'description' ) ) )
					. '" ' . $this->get_thumbcode( $this->__get( 'name' ) ) . '>' . '<img alt="' . $this->__get( 'alttext' )
					. '" src="' . $this->thumbURL . '"/>' . '</a>';
				$this->_cache['href']      = $tmp;
				$this->_cache['thumbHTML'] = $tmp;
				return $this->_cache['thumbHTML'];

			case 'thumbPath':
				$storage                   = $this->get_storage();
				$this->_cache['thumbPath'] = $storage->get_image_abspath( $this->_orig_image, 'thumbnail' );
				return $this->_cache['thumbPath'];

			case 'thumbnailURL':
				$storage             = $this->get_storage();
				$thumbnail_size_name = 'thumbnail';
				if ( $this->_displayed_gallery && isset( $this->_displayed_gallery->display_settings ) ) {
					$ds = $this->_displayed_gallery->display_settings;
					if ( isset( $ds['override_thumbnail_settings'] ) && $ds['override_thumbnail_settings'] ) {
						$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();

						$dyn_params = [
							'width'  => $ds['thumbnail_width'],
							'height' => $ds['thumbnail_height'],
						];
						if ( $ds['thumbnail_quality'] ) {
							$dyn_params['quality'] = $ds['thumbnail_quality'];
						}
						if ( $ds['thumbnail_crop'] ) {
							$dyn_params['crop'] = true;
						}
						if ( $ds['thumbnail_watermark'] ) {
							$dyn_params['watermark'] = true;
						}
						$thumbnail_size_name = $dynthumbs->get_size_name( $dyn_params );
					}
				}

				$this->_cache['thumbnailURL'] = $storage->get_image_url( $this->_orig_image, $thumbnail_size_name );
				return $this->_cache['thumbnailURL'];

			case 'thumbcode':
				if ( $this->_displayed_gallery
				&& isset( $this->_displayed_gallery->display_settings )
				&& isset( $this->_displayed_gallery->display_settings['use_imagebrowser_effect'] )
				&& $this->_displayed_gallery->display_settings['use_imagebrowser_effect']
				&& ! empty( $this->_orig_image->thumbcode ) ) {
					$this->_cache['thumbcode'] = $this->_orig_image->thumbcode;
				} else {
					$this->_cache['thumbcode'] = $this->get_thumbcode( $this->__get( 'name' ) );
				}
				return $this->_cache['thumbcode'];

			case 'thumbURL':
				return $this->__get( 'thumbnailURL' );

			case 'title':
				$this->_cache['title'] = stripslashes( $this->__get( 'name' ) );
				return $this->_cache['title'];

			case 'url':
				$storage             = $this->get_storage();
				$this->_cache['url'] = $storage->get_image_url( $this->_orig_image, 'full' );
				return $this->_cache['url'];

			default:
				return $this->_cache[ $name ];
		}
	}

	// called on initial nggLegacy image at construction. not sure what to do with it now.
	public function construct_ngg_Image( $gallery ) {
		do_action_ref_array( 'ngg_get_image', [ &$this ] );
		unset( $this->tags );
	}

	/**
	 * Retrieves and caches an I_Settings_Manager instance
	 *
	 * @return mixed
	 */
	public function get_settings() {
		if ( is_null( $this->_settings ) ) {
			$this->_settings = \Imagely\NGG\Settings\Settings::get_instance();
		}
		return $this->_settings;
	}

	/**
	 * Retrieves and caches an I_Gallery_Storage instance
	 *
	 * @return mixed
	 */
	public function get_storage() {
		if ( is_null( $this->_storage ) ) {
			$this->_storage = \Imagely\NGG\DataStorage\Manager::get_instance();
		}
		return $this->_storage;
	}

	/**
	 * Retrieves gallery mapper instance
	 *
	 * @param int $gallery_id Gallery ID
	 * @return Imagely\NGG\DataTypes\Gallery|null
	 */
	public function get_gallery( $gallery_id ) {
		if ( isset( $this->container ) && method_exists( $this->container, 'get_gallery' ) ) {
			return $this->container->get_gallery( $gallery_id );
		}

		return \Imagely\NGG\DataMappers\Gallery::get_instance()->find( $gallery_id );
	}

	/**
	 * Retrieves gallery mapper instance
	 *
	 * @param int $gallery_id Gallery ID
	 * @return Imagely\NGG\DataTypes\Gallery|null
	 */
	public function get_legacy_gallery( $gallery_id ) {
		return \Imagely\NGG\DataMappers\Gallery::get_instance()->find( $gallery_id );
	}

	/**
	 * Get the thumbnail code (to add effects on thumbnail click)
	 *
	 * Applies the filter 'ngg_get_thumbcode'
	 *
	 * @param string $gallery_name (optional) Default = ''
	 * @return string
	 */
	public function get_thumbcode( $gallery_name = '' ) {
		if ( empty( $this->_displayed_gallery ) ) {
			$effect_code = \Imagely\NGG\Settings\Settings::get_instance()->thumbCode;
			$effect_code = str_replace( '%GALLERY_ID%', $gallery_name, $effect_code );
			$effect_code = str_replace( '%GALLERY_NAME%', $gallery_name, $effect_code );
			$retval      = $effect_code;
		} else {
			$controller = new Controller();
			$retval     = $controller->get_effect_code( $this->_displayed_gallery );

			// This setting requires that we disable the effect code.
			$ds = $this->_displayed_gallery->display_settings;
			if ( isset( $ds['use_imagebrowser_effect'] ) && $ds['use_imagebrowser_effect'] ) {
				$retval = '';
			}
		}

		$retval = apply_filters( 'ngg_get_thumbcode', $retval, $this );

		// ensure some additional data- fields are added; provides Pro-Lightbox compatibility.
		$retval .= ' data-image-id="' . $this->__get( 'id' ) . '"';
		$retval .= ' data-src="' . $this->__get( 'imageURL' ) . '"';
		$retval .= ' data-thumbnail="' . $this->__get( 'thumbnailURL' ) . '"';
		$retval .= ' data-title="' . esc_attr( $this->__get( 'alttext' ) ) . '"';
		$retval .= ' data-description="' . esc_attr( $this->__get( 'description' ) ) . '"';

		$this->_cache['thumbcode'] = $retval;
		return $retval;
	}

	/**
	 * For compatibility support
	 *
	 * @return mixed
	 */
	public function get_href_link() {
		return $this->__get( 'imageHTML' );
	}

	/**
	 * For compatibility support
	 *
	 * @return mixed
	 */
	public function get_href_thumb_link() {
		return $this->__get( 'thumbHTML' );
	}

	/**
	 * Function exists for legacy support but has been gutted to not do anything
	 *
	 * @param string|int $width (optional) Default = ''
	 * @param string|int $height (optional) Default = ''
	 * @param string     $mode could be watermark | web20 | crop
	 * @return bool|string The url for the image or false if failed
	 */
	public function cached_singlepic_file( $width = '', $height = '', $mode = '' ) {
		$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
		$storage   = $this->get_storage();

		// determine what to do with 'mode'.
		$display_reflection = false;
		$display_watermark  = false;

		if ( ! is_array( $mode ) ) {
			$mode = explode( ',', $mode );
		}
		if ( in_array( 'web20', $mode ) ) {
			$display_reflection = true;
		}
		if ( in_array( 'watermark', $mode ) ) {
			$display_watermark = true;
		}

		// and go for it.
		$params = [
			'width'      => $width,
			'height'     => $height,
			'watermark'  => $display_watermark,
			'reflection' => $display_reflection,
		];

		return $storage->get_image_url( (object) $this->_cache, $dynthumbs->get_size_name( $params ) );
	}

	/**
	 * Get the tags associated to this image
	 */
	public function get_tags() {
		return $this->__get( 'tags' );
	}

	/**
	 * Get the permalink to the image
	 *
	 * TODO: Get a permalink to a page presenting the image
	 */
	public function get_permalink() {
		return $this->__get( 'permalink' );
	}

	/**
	 * Returns the _cache array; used by nggImage
	 *
	 * @return array
	 */
	public function _get_image() {
		return $this->_cache;
	}
}
