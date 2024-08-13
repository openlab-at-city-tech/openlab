<?php

namespace Imagely\NGG\DisplayType;

use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;

use Imagely\NGG\DataTypes\{DisplayType, DisplayedGallery, LegacyImage, LegacyImageCollection};
use Imagely\NGG\Display\{DisplayManager, LightboxManager, StaticAssets};
use Imagely\NGG\DisplayedGallery\{Renderer, TriggerManager};
use Imagely\NGG\Util\{Filesystem, Router, Transient};

class Controller {

	public $run_once = false;

	public static $alternate_displayed_galleries = [];

	/**
	 * @deprecated This method is only used by NextGEN Pro
	 * @param DisplayedGallery $displayed_gallery
	 * @return bool
	 */
	public function enqueue_displayed_gallery_trigger_buttons_resources( $displayed_gallery = false ) {
		$retval = false;

		DisplayManager::enqueue_fontawesome();

		if ( ! $this->run_once
		&& ! empty( $displayed_gallery )
		&& ! empty( $displayed_gallery->display_settings['ngg_triggers_display'] )
		&& $displayed_gallery->display_settings['ngg_triggers_display'] !== 'never' ) {
			$pro_active = false;
			if ( defined( 'NGG_PRO_PLUGIN_VERSION' ) ) {
				$pro_active = 'NGG_PRO_PLUGIN_VERSION';
			}
			if ( defined( 'NEXTGEN_GALLERY_PRO_VERSION' ) ) {
				$pro_active = 'NEXTGEN_GALLERY_PRO_VERSION';
			}
			if ( ! empty( $pro_active ) ) {
				$pro_active = constant( $pro_active );
			}
			if ( ! is_admin() && ( empty( $pro_active ) || version_compare( $pro_active, '1.0.11' ) >= 0 ) ) {
				\wp_enqueue_style( 'fontawesome' );
				$retval         = true;
				$this->run_once = true;
			}
		}

		return $retval;
	}

	public function is_cachable() {
		return true;
	}

	public function enqueue_pagination_resources() {
		wp_enqueue_style(
			'nextgen_pagination_style',
			StaticAssets::get_url( 'GalleryDisplay/pagination_style.css', 'photocrati-nextgen_pagination#style.css' ),
			[],
			NGG_SCRIPT_VERSION
		);
	}

	public function enqueue_frontend_resources( $displayed_gallery ) {
		// This script provides common JavaScript among all display types.
		\wp_enqueue_script( 'ngg_common' );
		\wp_add_inline_script(
			'ngg_common',
			'
            var nggLastTimeoutVal = 1000;

            var nggRetryFailedImage = function(img) {
                setTimeout(function(){
                    img.src = img.src;
                }, nggLastTimeoutVal);

                nggLastTimeoutVal += 500;
            }'
		);

		// Add "galleries = {};".
		DisplayManager::add_script_data(
			'ngg_common',
			'galleries',
			new \stdClass(),
			true,
			false
		);

		// Add "galleries.gallery_1 = {};"..
		DisplayManager::add_script_data(
			'ngg_common',
			'galleries.gallery_' . $displayed_gallery->id(),
			(array) $displayed_gallery->get_entity(),
			false
		);

		DisplayManager::add_script_data(
			'ngg_common',
			'galleries.gallery_' . $displayed_gallery->id() . '.wordpress_page_root',
			get_permalink(),
			false
		);

		// Enqueue trigger button resources.
		TriggerManager::get_instance()->enqueue_resources( $displayed_gallery );

		// Enqueue the selected lightbox.
		LightboxManager::get_instance()->enqueue();

		$this->enqueue_displayed_gallery_trigger_buttons_resources( $displayed_gallery );

		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			\C_Display_Type_Controller::get_instance()->enqueue_frontend_resources( $displayed_gallery, false );
		}

		\do_action( 'ngg_display_type_controller_enqueue_frontend_resources', $displayed_gallery );
	}

	public function get_template_directory_name(): string {
		return '';
	}

	/**
	 * Allows the admin forms that display available templates to limit the selection to one directory.
	 */
	public function get_template_directory_abspath(): string {
		return path_join( NGG_PLUGIN_DIR, 'templates' . DIRECTORY_SEPARATOR . $this->get_template_directory_name() );
	}

	public function get_preview_image_url() {
		return '';
	}

	/**
	 * Ensures that the minimum configuration of parameters are sent to a view
	 *
	 * @param DisplayedGallery $displayed_gallery
	 * @param null|array       $params
	 * @return array|null
	 */
	public function prepare_display_parameters( $displayed_gallery, $params = null ) {
		if ( $params == null ) {
			$params = [];
		}

		$params['display_type_rendering'] = true;
		$params['displayed_gallery']      = $displayed_gallery;

		return $params;
	}

	/**
	 * Renders the frontend display of the display type
	 *
	 * @param bool $return (optional)
	 * @return string
	 */
	public function index_action( $displayed_gallery, $return = false ) {
		return '';
	}

	/**
	 * This effectively busts the standard template rendering cache
	 *
	 * @param DisplayedGallery $displayed_gallery
	 * @return string Rendered HTML
	 */
	public function cache_action( $displayed_gallery ) {
		return '';
	}

	/**
	 * Returns the effect "effect code" used to inject lightbox attributes into image anchor elements
	 *
	 * @return string
	 */
	public function get_effect_code( $displayed_gallery, $legacy_compat = true ) {
		global $post;
		$retval   = '';
		$lightbox = LightboxManager::get_instance()->get_selected();

		if ( 'arifancybox' === $lightbox->name ) {
			return apply_filters( 'ngg_effect_code', $lightbox->code, $displayed_gallery );
		}

		if ( $lightbox->is_supported( $displayed_gallery ) ) {
			$retval = $lightbox->code;
			$retval = str_replace( '%GALLERY_ID%', $displayed_gallery->id(), $retval );
			$retval = str_replace( '%GALLERY_NAME%', $displayed_gallery->id(), $retval );

			if ( $post && isset( $post->ID ) && $post->ID ) {
				$retval = str_replace( '%PAGE_ID%', $post->ID, $retval );
			}
		}

		if ( $legacy_compat && \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			$retval = \C_Display_Type_Controller::get_instance()->get_effect_code( $displayed_gallery );
		}

		return apply_filters( 'ngg_effect_code', $retval, $displayed_gallery );
	}

	/**
	 * Returns the longest and widest dimensions from a list of entities. Only used by Pro Film.
	 *
	 * @param $entities
	 * @param $named_size
	 * @param bool       $style_images Unused
	 * @deprecated This should be moved into the Pro Film controller and removed when POPE-compat level 1 is reached
	 * @return array
	 */
	public function get_entity_statistics( $entities, $named_size, $style_images = false ) {
		$longest      = $widest = 0;
		$storage      = StorageManager::get_instance();
		$image_mapper = ImageMapper::get_instance();

		foreach ( $entities as $entity ) {
			$image = null;
			if ( isset( $entity->pid ) ) {
				$image = $entity;
			} elseif ( isset( $entity->previewpic ) ) {
				$image = $image_mapper->find( $entity->previewpic );
			}

			// Once we have the image, get its dimensions.
			if ( $image ) {
				$dimensions = $storage->get_image_dimensions( $image, $named_size );
				if ( $dimensions['width'] > $widest ) {
					$widest = $dimensions['width'];
				}
				if ( $dimensions['height'] > $longest ) {
					$longest = $dimensions['height'];
				}
			}
		}

		return [
			'entities' => $entities,
			'longest'  => $longest,
			'widest'   => $widest,
		];
	}

	/**
	 * Finds the absolute path of template given file name and list of possible directories
	 *
	 * @param string $template
	 * @param array  $params
	 * @return string $template
	 */
	public function get_display_type_view_abspath( $template, $params ) {
		// Identify display type and display_type_view.
		$displayed_gallery = $params['displayed_gallery'];
		$display_type_name = $params['displayed_gallery']->display_type;
		$display_settings  = $displayed_gallery->display_settings;
		$display_type_view = null;
		if ( isset( $display_settings['display_type_view'] ) ) {
			$display_type_view = $display_settings['display_type_view'];
		}
		if ( isset( $display_settings['display_view'] ) ) {
			$display_type_view = $display_settings['display_view'];
		}

		if ( $display_type_view && $display_type_view != 'default' ) {
			/*
			 * A display type view or display template value looks like this:
			 *
			 * "default"
			 * "imagebrowser-dark-template.php" ("default" category is implicit)
			 * "custom/customized-template.php" ("custom" category is explicit)
			 *
			 * Templates can be found in multiple directories, and each directory is given
			 * a key, which is used to distinguish it's "category".
			 */

			$fs = Filesystem::get_instance();

			/* Fetch array of template directories */
			$dirs = DisplayManager::get_display_type_view_dirs( $display_type_name );

			// Add the missing "default" category name prefix to the template to make it more consistent to evaluate.
			if ( strpos( $display_type_view, DIRECTORY_SEPARATOR ) === false ) {
				$display_type_view = join( DIRECTORY_SEPARATOR, [ 'default', $display_type_view ] );
			}

			foreach ( $dirs as $category => $dir ) {
				$category = preg_quote( $category . DIRECTORY_SEPARATOR );
				if ( preg_match( "#^{$category}(.*)$#", $display_type_view, $match ) ) {
					$display_type_view = $match[1];
					$template_abspath  = $fs->join_paths( $dir, $display_type_view );
					if ( @file_exists( $template_abspath ) ) {
						$template = $template_abspath;
						break;
					}
				}
			}
		}

		// Return template. If no match is found, returns the original template.
		return $template;
	}

	/**
	 * The basic thumbnails and slideshow have options to display galleries with the other display type, and albums
	 * of course display child of an entirely different kind. Implementing this method allows displays to alter
	 * the displayed gallery passed to their index_action() method.
	 *
	 * @param DisplayedGallery $displayed_gallery
	 * @return DisplayedGallery
	 */
	public function get_alternative_displayed_gallery( $displayed_gallery ) {
		return $displayed_gallery;
	}

	public function set_alternative_displayed_gallery(
		array $params,
		DisplayedGallery $displayed_gallery,
		string $new_display_type
	): DisplayedGallery {
		// Render the new display type.
		$renderer                        = Renderer::get_instance();
		$params['original_display_type'] = $displayed_gallery->display_type;
		$params['original_settings']     = $displayed_gallery->display_settings;
		$params['display_type']          = $new_display_type;
		$params['display_settings']      = [];

		$id = $displayed_gallery->id();

		$alt_displayed_gallery = $renderer->params_to_displayed_gallery( $params );
		if ( is_null( $alt_displayed_gallery->id() ) ) {
			$alt_displayed_gallery->id( md5( json_encode( $alt_displayed_gallery->get_entity() ) ) );
		}
		self::$alternate_displayed_galleries[ $id ] = $alt_displayed_gallery;

		return self::$alternate_displayed_galleries[ $id ];
	}

	/**
	 * Renders legacy NextGen templates
	 *
	 * @param string $template_name File name
	 * @param array  $vars (optional) Specially formatted array of parameters
	 * @param bool   $return (optional)
	 * @param string $prefix (optional)
	 * @return string
	 */
	public function legacy_render( $template_name, $vars = [], $return = false, $prefix = null ) {
		$retval           = '[Not a valid template]';
		$template_locator = LegacyTemplateLocator::get_instance();

		// search first for files with their prefix.
		$template_abspath = $template_locator->find( $prefix . '-' . $template_name );
		if ( ! $template_abspath ) {
			$template_abspath = $template_locator->find( $template_name );
		}

		if ( $template_abspath ) {
			// render the template.
			extract( $vars );
			if ( $return ) {
				ob_start();
			}

			include $template_abspath;

			if ( $return ) {
				$retval = ob_get_contents();
				ob_end_clean();
			}
		}

		return $retval;
	}

	/**
	 * Returns the parameter objects necessary for legacy template rendering using legacy_render()
	 *
	 * @param array            $images
	 * @param DisplayedGallery $displayed_gallery
	 * @param array            $params
	 *
	 * @return array
	 */
	public function prepare_legacy_parameters( $images, $displayed_gallery, $params = [] ) {
		// setup.
		$image_map   = ImageMapper::get_instance();
		$gallery_map = GalleryMapper::get_instance();
		$image_key   = $image_map->get_primary_key_column();
		$gallery_id  = $displayed_gallery->id();

		$pid = Router::get_instance()->get_routed_app()->get_parameter( 'pid' );

		// because picture_list implements ArrayAccess any array-specific actions must be taken on
		// $picture_list->container or they won't do anything.
		$picture_list = new LegacyImageCollection();
		$current_pid  = null;

		// begin processing.
		$current_page = ( @\get_the_ID() == false ) ? 0 : @\get_the_ID();

		// determine what the "current image" is; used mostly for carousel.
		if ( ! is_numeric( $pid ) && ! empty( $pid ) ) {
			$picture = $image_map->find_first( [ 'image_slug = %s', $pid ] );
			$pid     = $picture->$image_key;
		}

		// create our new wrappers.
		foreach ( $images as &$image ) {
			if ( $image && isset( $params['effect_code'] ) ) {
				if ( is_object( $image ) ) {
					$image->thumbcode = $params['effect_code'];
				} elseif ( is_array( $image ) ) {
					$image['thumbcode'] = $params['effect_code'];
				}
			}

			$new_image = new LegacyImage( $image, $displayed_gallery );
			if ( $pid == $new_image->$image_key ) {
				$current_pid = $new_image;
			}
			$picture_list[] = $new_image;
		}

		reset( $picture_list->container );

		// assign current_pid.
		$current_pid = ( is_null( $current_pid ) ) ? current( $picture_list->container ) : $current_pid;

		foreach ( $picture_list as &$image ) {
			if ( isset( $image->hidden ) && $image->hidden ) {
				$tmp          = $displayed_gallery->display_settings['number_of_columns'];
				$image->style = ( $tmp > 0 ) ? 'style="width:' . floor( 100 / $tmp ) . '%;display: none;"' : 'style="display: none;"';
			}
		}

		// find our gallery to build the new one on.
		$orig_gallery = $gallery_map->find( current( $picture_list->container )->galleryid );

		// create the 'gallery' object.
		$gallery                    = new \stdclass();
		$gallery->ID                = $displayed_gallery->id();
		$gallery->name              = stripslashes( $orig_gallery->name );
		$gallery->title             = stripslashes( $orig_gallery->title );
		$gallery->description       = \html_entity_decode( \stripslashes( $orig_gallery->galdesc ) );
		$gallery->pageid            = $orig_gallery->pageid;
		$gallery->anchor            = 'ngg-gallery-' . $gallery_id . '-' . $current_page;
		$gallery->displayed_gallery = &$displayed_gallery;
		$gallery->columns           = @intval( $displayed_gallery->display_settings['number_of_columns'] );
		$gallery->imagewidth        = ( $gallery->columns > 0 ) ? 'style="width:' . floor( 100 / $gallery->columns ) . '%;"' : '';

		if ( ! empty( $displayed_gallery->display_settings['show_slideshow_link'] ) ) {
			$gallery->show_slideshow      = true;
			$gallery->slideshow_link      = $params['slideshow_link'];
			$gallery->slideshow_link_text = $displayed_gallery->display_settings['slideshow_link_text'];
		} else {
			$gallery->show_slideshow = false;
		}

		$gallery = apply_filters( 'ngg_gallery_object', $gallery, 4 );

		// build our array of things to return.
		$return = [ 'gallery' => $gallery ];

		// single_image is an internally added flag.
		if ( ! empty( $params['single_image'] ) ) {
			$return['image'] = $picture_list[0];
		} else {
			$return['current'] = $current_pid;
			$return['images']  = $picture_list->container;
		}

		// this is expected to always exist.
		if ( ! empty( $params['pagination'] ) ) {
			$return['pagination'] = $params['pagination'];
		} else {
			$return['pagination'] = null;
		}

		if ( ! empty( $params['next'] ) ) {
			$return['next'] = $params['next'];
		} else {
			$return['next'] = false;
		}

		if ( ! empty( $params['prev'] ) ) {
			$return['prev'] = $params['prev'];
		} else {
			$return['prev'] = false;
		}

		return $return;
	}

	/**
	 * Returns an url to view the displayed gallery using an alternate display type
	 *
	 * @param DisplayedGallery $displayed_gallery
	 * @param string           $display_type
	 * @return string
	 */
	public function get_url_for_alternate_display_type( $displayed_gallery, $display_type, $origin_url = false ) {
		$app = Router::get_instance()->get_routed_app();

		if ( ! $origin_url
		&& ! empty( $displayed_gallery->display_settings['original_display_type'] )
		&& ! empty( $_SERVER['NGG_ORIG_REQUEST_URI'] ) ) {
			$origin_url = $_SERVER['NGG_ORIG_REQUEST_URI'];
		}

		$url = ( $origin_url ?: $app->get_app_url( false, true ) );

		$url = $app->remove_parameter( 'show', $displayed_gallery->id(), $url );
		$url = $app->set_parameter( 'show', $display_type, $displayed_gallery->id(), false, $url );

		return $url;
	}

	/**
	 * Returns a formatted HTML string of a pagination widget
	 *
	 * @param mixed       $selected_page
	 * @param int         $number_of_entities
	 * @param int         $entities_per_page
	 * @param string|null $current_url (optional)
	 * @return array Of data holding prev & next url locations and a formatted HTML string
	 */
	public function create_pagination( $selected_page, $number_of_entities, $entities_per_page = 0, $current_url = null ) {
		$router = Router::get_instance();
		$app    = $router->get_routed_app();

		$prev_symbol = \apply_filters( 'ngg_prev_symbol', '&#9668;' );
		$next_symbol = \apply_filters( 'ngg_next_symbol', '&#9658;' );

		if ( empty( $current_url ) ) {
			$current_url = $app->get_app_url( false, true );

			if ( \is_archive() ) {
				$id = \get_the_ID();

				if ( $id == null ) {
					global $post;
					$id = $post ? $post->ID : null;
				}

				if ( $id != null && \in_the_loop() ) {
					$current_url = \get_permalink( $id );
				}
			}
		}

		// Early exit.
		$return = [
			'prev'   => '',
			'next'   => '',
			'output' => "<div class='ngg-clear'></div>",
		];

		if ( $entities_per_page <= 0 || $number_of_entities <= 0 ) {
			return $return;
		}

		// Construct array of page urls.
		$ending_ellipsis = $starting_ellipsis = false;
		$number_of_pages = ceil( $number_of_entities / $entities_per_page );
		$pages           = [];

		for ( $i = 1; $i <= $number_of_pages; $i++ ) {

			if ( $selected_page === $i ) {
				$pages['current'] = "<span class='current'>{$i}</span>";
			} else {
				$link        = esc_attr( $app->set_parameter( 'nggpage', $i, null, false, $current_url ) );
				$pages[ $i ] = "<a class='page-numbers' data-pageid='{$i}' href='{$link}'>{$i}</a>";

			}
		}

		$after = $this->array_slice_from( 'current', $pages );
		if ( count( $after ) > 3 ) {
			$after = array_merge(
				$this->array_take_from_start( 2, $after ),
				[ "<span class='ellipsis'>...</span>" ],
				$this->array_take_from_end( 1, $after )
			);
		}

		$before = $this->array_slice_to( 'current', $pages );
		if ( count( $before ) > 3 ) {
			$before = array_merge(
				$this->array_take_from_start( 1, $before ),
				[ "<span class='ellipsis'>...</span>" ],
				$this->array_take_from_end( 2, $before )
			);
			array_pop( $before );
		}

		$pages = array_merge( $before, $after );

		if ( $pages && count( $pages ) > 1 ) {
			// Next page.
			if ( $selected_page + 1 <= $number_of_pages ) {
				$next_page = $selected_page + 1;
				$link      = $return['next'] = $app->set_parameter( 'nggpage', $next_page, null, false, $current_url );
				$pages[]   = "<a class='prev' href='{$link}' data-pageid={$next_page}>{$next_symbol}</a>";
			}

			// Prev page.
			if ( $selected_page - 1 > 0 ) {
				$prev_page = $selected_page - 1;
				$link      = $return['next'] = $app->set_parameter( 'nggpage', $prev_page, null, false, $current_url );
				array_unshift( $pages, "<a class='next' href='{$link}' data-pageid={$prev_page}>{$prev_symbol}</a>" );
			}

			$return['output'] = "<div class='ngg-navigation'>" . implode( "\n", $pages ) . '</div>';
		}

		return $return;
	}

	/**
	 * This is necessary for the SinglePicture display type.
	 *
	 * @return false
	 */
	public function is_hidden_from_igw() {
		return false;
	}

	public function array_slice_from( $find_key, $arr ) {
		$retval = [];
		reset( $arr );
		foreach ( $arr as $key => $value ) {
			if ( $key == $find_key || $retval ) {
				$retval[ $key ] = $value;
			}
		}
		reset( $arr );

		return $retval;
	}

	public function array_slice_to( $find_key, $arr ) {
		$retval = [];
		reset( $arr );
		foreach ( $arr as $key => $value ) {
			$retval[ $key ] = $value;
			if ( $key == $find_key ) {
				break;
			}
		}
		reset( $arr );

		return $retval;
	}

	public function array_take_from_start( $number, $arr ) {
		$retval = [];
		foreach ( $arr as $key => $value ) {
			if ( count( $retval ) < $number ) {
				$retval[ $key ] = $value;
			} else {
				break;
			}
		}
		return $retval;
	}

	public function array_take_from_end( $number, $arr ) {
		return array_reverse( $this->array_take_from_start( $number, array_reverse( $arr ) ) );
	}

	/*  The following methods manage the installation and removal of display types */

	/**
	 * @param $name
	 */
	public function delete_duplicates( $name ) {
		$mapper  = DisplayTypeMapper::get_instance();
		$results = $mapper->find_all( [ 'name = %s', $name ] );
		if ( count( $results ) > 0 ) {
			array_pop( $results ); // the last should be the latest.
			foreach ( $results as $display_type ) {
				$mapper->destroy( $display_type );
			}
		}
		$mapper->flush_query_cache();
	}

	/**
	 * Method for installing a display type.
	 *
	 * @param string $name Display type name.
	 * @param array  $properties Display type properties.
	 * @param bool   $reset True: revert to default setting.
	 * @return bool|int
	 */
	public function install_display_type( string $name, array $properties = [], bool $reset = false ) {
		$this->delete_duplicates( $name );

		// Try to find the existing entity. If it doesn't exist, we'll create.
		$mapper       = DisplayTypeMapper::get_instance();
		$display_type = $mapper->find_by_name( $name );
		$mapper->flush_query_cache();
		if ( ! $display_type ) {
			$display_type = new DisplayType();
		}

		// Update the properties of the display type.
		$properties['name'] = $name;
		$changed            = false;
		foreach ( $properties as $key => $val ) {
			if ( ! isset( $display_type->$key ) || empty( $display_type->$key ) || is_null( $display_type->$key ) || $reset ) {
				$display_type->$key = $val;
				$changed            = true;
			}
		}

		// Save the entity.
		if ( $changed ) {
			return $mapper->save( $display_type );
		}

		return false;
	}

	/**
	 * Uninstalls all display types
	 */
	public function uninstall_display_types() {
		$mapper = DisplayTypeMapper::get_instance();
		$mapper->delete()->run_query();
	}

	/**
	 * @param bool $reset (optional) Unused
	 */
	public function install( $reset = false ) {
	}

	/**
	 * @param bool $hard (optional) Unused
	 */
	public function uninstall( $hard = false ) {
		Transient::flush();
		$this->uninstall_display_types();
	}

	public function get_default_settings() {
		return [];
	}
}
