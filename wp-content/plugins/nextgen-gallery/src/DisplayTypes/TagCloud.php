<?php

namespace Imagely\NGG\DisplayTypes;

use Imagely\NGG\DisplayType\Controller as ParentController;

use Imagely\NGG\DataTypes\DisplayedGallery;
use Imagely\NGG\Display\{StaticAssets, View};
use Imagely\NGG\DisplayedGallery\Renderer;
use Imagely\NGG\Util\Router;

class TagCloud extends ParentController {

	/**
	 * @param DisplayedGallery $displayed_gallery
	 *
	 * @return DisplayedGallery
	 */
	public function get_alternative_displayed_gallery( $displayed_gallery ) {
		// Prevent recursive checks for further alternates causing additional modifications to the settings array.
		$id = $displayed_gallery->id();
		if ( ! empty( self::$alternate_displayed_galleries[ $id ] ) ) {
			return self::$alternate_displayed_galleries[ $id ];
		}

		$router = Router::get_instance();

		$tag = \urldecode( $router->get_parameter( 'gallerytag', null, '' ) );

		// The display setting 'display_type' has been removed to 'gallery_display_type'.
		$display_settings = $displayed_gallery->display_settings;
		if ( isset( $display_settings['display_type'] ) ) {
			$display_settings['gallery_display_type'] = $display_settings['display_type'];
			unset( $display_settings['display_type'] );
		}

		// we're looking at a tag, so show images w/that tag as a thumbnail gallery.
		if ( ! \is_home() && ! empty( $tag ) ) {
			$params = [
				'source'                => 'tags',
				'container_ids'         => [ esc_attr( $tag ) ],
				'display_type'          => $displayed_gallery->display_settings['gallery_display_type'],
				'original_display_type' => $displayed_gallery->display_type,
				'original_settings'     => $displayed_gallery->display_settings,
			];

			$renderer                    = Renderer::get_instance();
			$alternate_displayed_gallery = $renderer->params_to_displayed_gallery( $params );
			if ( is_null( $alternate_displayed_gallery->id() ) ) {
				$alternate_displayed_gallery->id( md5( json_encode( $alternate_displayed_gallery->get_entity() ) ) );
			}
			self::$alternate_displayed_galleries[ $id ] = $alternate_displayed_gallery;
			return $alternate_displayed_gallery;
		}

		return $displayed_gallery;
	}

	/**
	 * @param DisplayedGallery $displayed_gallery
	 * @param bool             $return (optional)
	 *
	 * @return string
	 */
	public function index_action( $displayed_gallery, $return = false ) {
		$router = Router::get_instance();

		// we're looking at a tag, so show images w/that tag as a thumbnail gallery.
		if ( ! \is_home() && ! empty( $router->get_parameter( 'gallerytag' ) ) ) {
			$displayed_gallery = $this->get_alternative_displayed_gallery( $displayed_gallery );
			return Renderer::get_instance()->display_images( $displayed_gallery );
		}

		$application      = $router->get_routed_app();
		$display_settings = $displayed_gallery->display_settings;
		$defaults         = [
			'exclude'  => '',
			'format'   => 'list',
			'include'  => $displayed_gallery->get_term_ids_for_tags(),
			'largest'  => 22,
			'link'     => 'view',
			'number'   => $display_settings['number'],
			'order'    => 'ASC',
			'orderby'  => 'name',
			'smallest' => 8,
			'taxonomy' => 'ngg_tag',
			'unit'     => 'pt',
		];
		$args             = \wp_parse_args( '', $defaults );

		// Always query top tags.
		$tags = \get_terms(
			$args['taxonomy'],
			array_merge(
				$args,
				[
					'orderby' => 'count',
					'order'   => 'DESC',
				]
			)
		);

		foreach ( $tags as $key => $tag ) {
			$tags[ $key ]->link = $application->set_parameter(
				'gallerytag',
				$tag->slug,
				null,
				false,
				$application->get_routed_url( true )
			);
			$tags[ $key ]->id   = $tag->term_id;
		}

		$params                         = $display_settings;
		$params['inner_content']        = $displayed_gallery->inner_content;
		$params['tagcloud']             = \wp_generate_tag_cloud( $tags, $args );
		$params['displayed_gallery_id'] = $displayed_gallery->id();

		$params = $this->prepare_display_parameters( $displayed_gallery, $params );

		$view = new View(
			'TagCloud/nextgen_basic_tagcloud',
			$params,
			'photocrati-nextgen_basic_tagcloud#nextgen_basic_tagcloud'
		);

		return $view->render( $return );
	}

	/**
	 * Enqueues all static resources required by this display type
	 *
	 * @param DisplayedGallery $displayed_gallery
	 */
	public function enqueue_frontend_resources( $displayed_gallery ) {
		parent::enqueue_frontend_resources( $displayed_gallery );

		wp_enqueue_style(
			'photocrati-nextgen_basic_tagcloud-style',
			StaticAssets::get_url( 'TagCloud/nextgen_basic_tagcloud.css', 'photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic.css' ),
			[],
			NGG_SCRIPT_VERSION
		);
	}

	public function get_preview_image_url() {
		return StaticAssets::get_url( 'TagCloud/preview.gif' );
	}

	public function get_template_directory_name(): string {
		return 'TagCloud';
	}

	public function get_default_settings() {
		return \apply_filters(
			'ngg_tag_cloud_default_settings',
			[
				'gallery_display_type' => NGG_BASIC_THUMBNAILS,
				'ngg_triggers_display' => 'never',
				'number'               => 45,
			]
		);
	}

	public function install( $reset = false ) {
		$this->install_display_type(
			NGG_BASIC_TAGCLOUD,
			[
				'title'          => __( 'NextGEN Basic TagCloud', 'nggallery' ),
				'entity_types'   => [ 'image' ],
				'default_source' => 'tags',
				'view_order'     => NGG_DISPLAY_PRIORITY_BASE + 100,
				'aliases'        => [
					'tagcloud',
					'basic_tagcloud',
					'nextgen_basic_tagcloud',
					'photocrati-nextgen_basic_tagcloud',
				],
				'settings'       => $this->get_default_settings(),
			],
			$reset
		);
	}
}
