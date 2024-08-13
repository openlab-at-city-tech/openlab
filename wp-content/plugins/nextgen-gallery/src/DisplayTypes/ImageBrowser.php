<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DataStorage\MetaData as MetaDataStorage;
use Imagely\NGG\DisplayType\Controller as ParentController;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Display\View;
use Imagely\NGG\Util\Router;
use Imagely\NGG\Util\URL;

class ImageBrowser extends ParentController {

	/**
	 * @return string
	 */
	public function index_action( $displayed_gallery, $return = false ) {
		// Force the trigger icon display off, regardless of past settings.
		$displayed_gallery->display_settings['ngg_triggers_display'] = 'never';

		$picture_list = [];

		foreach ( $displayed_gallery->get_included_entities() as $image ) {
			$picture_list[ $image->{$image->id_field} ] = $image;
		}

		if ( ! $picture_list ) {
			$view = new View( 'GalleryDisplay/NoImagesFound', [], 'photocrati-nextgen_gallery_display#no_images_found' );
			return $view->render( $return );
		}

		$settings    = $displayed_gallery->display_settings;
		$storage     = StorageManager::get_instance();
		$imap        = ImageMapper::get_instance();
		$router      = Router::get_instance();
		$application = $router->get_routed_app();

		// the pid may be a slug so we must track it & the slug target's database id.
		$pid         = $router->get_parameter( 'pid' );
		$numeric_pid = null;

		// makes the upcoming which-image-am-I loop easier.
		$picture_array = [];
		foreach ( $picture_list as $picture ) {
			$picture_array[] = $picture->{$imap->get_primary_key_column()};
		}

		// Determine which image in the list we need to display.
		if ( ! empty( $pid ) ) {
			if ( is_numeric( $pid ) && ! empty( $picture_list[ $pid ] ) ) {
				$numeric_pid = intval( $pid );
			} else {
				// in the case it's a slug we need to search for the pid.
				foreach ( $picture_list as $key => $picture ) {
					if ( $picture->image_slug == $pid || strtoupper( $picture->image_slug ) === strtoupper( urlencode( $pid ) ) ) {
						$numeric_pid = $key;
						break;
					}
				}
			}
		} else {
			reset( $picture_array );
			$numeric_pid = current( $picture_array );
		}

		// get ids to the next and previous images.
		$total = count( $picture_array );
		$key   = array_search( $numeric_pid, $picture_array );
		if ( ! $key ) {
			$numeric_pid = reset( $picture_array );
			$key         = key( $picture_array );
		}

		// for "viewing image #13 of $total".
		$picture_list_pos = $key + 1;

		// our image to display
		// TODO: Remove the use of LegacyImage type.
		$picture = new \Imagely\NGG\DataTypes\LegacyImage( $imap->find( $numeric_pid ), $displayed_gallery, true );
		$picture = apply_filters( 'ngg_image_object', $picture, $numeric_pid );

		// determine URI to the next & previous images.
		$back_pid = ( $key >= 1 ) ? $picture_array[ $key - 1 ] : end( $picture_array );

		// 'show' is set when using the imagebrowser as an alternate view to a thumbnail or slideshow for which the
		// basic-gallery module will rewrite the show parameter into existence as long as 'image' is set. We remove
		// 'show' here so navigation appears fluid.
		$current_url = $application->get_routed_url();
		if ( $router->get_parameter( 'ajax_pagination_referrer' ) ) {
			$current_url = $router->get_parameter( 'ajax_pagination_referrer' );
		}

		$prev_image_link = $application->set_parameter_value(
			'pid',
			$picture_list[ $back_pid ]->image_slug,
			null,
			false,
			$current_url
		);
		$prev_image_link = ( $application->remove_parameter( 'show', $displayed_gallery->id(), $prev_image_link ) );

		$next_pid        = ( $key < ( $total - 1 ) ) ? $picture_array[ $key + 1 ] : reset( $picture_array );
		$next_image_link = $application->set_parameter_value(
			'pid',
			$picture_list[ $next_pid ]->image_slug,
			null,
			false,
			$current_url
		);
		$next_image_link = ( $application->remove_parameter( 'show', $displayed_gallery->id(), $next_image_link ) );

		// css class.
		$anchor = 'ngg-imagebrowser-' . $displayed_gallery->id() . '-' . ( \get_the_ID() === false ? 0 : \get_the_ID() );

		// try to read EXIF data, but fallback to the db presets.
		$meta = new MetaDataStorage( $picture );
		$meta->sanitize();
		$meta_results         = [
			'exif' => $meta->get_EXIF(),
			'iptc' => $meta->get_IPTC(),
			'xmp'  => $meta->get_XMP(),
			'db'   => $meta->get_saved_meta(),
		];
		$meta_results['exif'] = ( $meta_results['exif'] === false ) ? $meta_results['db'] : $meta_results['exif'];

		// Disable triggers IF we're rendering inside an ajax-pagination request; var set in common.js.
		// Nonce verification is not necessary here.
		//
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['ajax_referrer'] ) ) {
			$displayed_gallery->display_settings['ngg_triggers_display'] = 'never';
		}

		if ( ! empty( $settings['template'] ) && $settings['template'] != 'default' ) {
			$picture->href_link           = $picture->get_href_link();
			$picture->previous_image_link = $prev_image_link;
			$picture->previous_pid        = $back_pid;
			$picture->next_image_link     = $next_image_link;
			$picture->next_pid            = $next_pid;
			$picture->number              = $picture_list_pos;
			$picture->total               = $total;
			$picture->anchor              = $anchor;

			return $this->legacy_render(
				$settings['template'],
				[
					'image'             => $picture,
					'meta'              => $meta,
					'exif'              => $meta_results['exif'],
					'iptc'              => $meta_results['iptc'],
					'xmp'               => $meta_results['xmp'],
					'db'                => $meta_results['db'],
					'displayed_gallery' => $displayed_gallery,
				],
				true,
				'imagebrowser'
			);
		} else {
			$params                 = $settings;
			$params['anchor']       = $anchor;
			$params['image']        = $picture;
			$params['storage']      = $storage;
			$params['previous_pid'] = $back_pid;
			$params['next_pid']     = $next_pid;
			$params['number']       = $picture_list_pos;
			$params['total']        = $total;

			$params['previous_image_link'] = $prev_image_link;
			$params['next_image_link']     = $next_image_link;
			$params['effect_code']         = $this->get_effect_code( $displayed_gallery );

			$params = $this->prepare_display_parameters( $displayed_gallery, $params );

			$view = new View(
				'ImageBrowser/default-view',
				$params,
				'photocrati-nextgen_basic_imagebrowser#nextgen_basic_imagebrowser'
			);

			return $view->render( $return );
		}

		return '';
	}

	public function enqueue_frontend_resources( $displayed_gallery ) {
		parent::enqueue_frontend_resources( $displayed_gallery );

		wp_enqueue_style(
			'nextgen_basic_imagebrowser_style',
			StaticAssets::get_url( 'ImageBrowser/style.css', 'photocrati-nextgen_basic_imagebrowser#style.css' ),
			[],
			NGG_SCRIPT_VERSION
		);
		wp_enqueue_script(
			'nextgen_basic_imagebrowser_script',
			StaticAssets::get_url( 'ImageBrowser/imagebrowser.js', 'photocrati-nextgen_basic_imagebrowser#imagebrowser.js' ),
			[ 'ngg_common' ],
			NGG_SCRIPT_VERSION,
			true
		);
	}

	public function get_preview_image_url() {
		return StaticAssets::get_url( 'ImageBrowser/preview.jpg' );
	}

	public function get_default_settings() {
		return \apply_filters(
			'ngg_image_browser_default_settings',
			[
				'display_view'         => 'default-view.php',
				'template'             => '',
				'ajax_pagination'      => '1',

				'ngg_triggers_display' => 'never',
			]
		);
	}

	public function get_template_directory_name(): string {
		return 'ImageBrowser';
	}

	public function install( $reset = false ) {
		$this->install_display_type(
			NGG_BASIC_IMAGEBROWSER,
			[
				'title'          => __( 'NextGEN Basic ImageBrowser', 'nggallery' ),
				'entity_types'   => [ 'image' ],
				'default_source' => 'galleries',
				'view_order'     => NGG_DISPLAY_PRIORITY_BASE + 20,
				'aliases'        => [
					'imagebrowser',
					'basic_imagebrowser',
					'nextgen_basic_imagebrowser',
					'photocrati-nextgen_basic_imagebrowser',
				],
				'settings'       => $this->get_default_settings(),
			],
			$reset
		);
	}
}
