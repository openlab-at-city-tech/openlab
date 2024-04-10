<?php
namespace SydneyPro\Elementor;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Template_Library_Manager {

	protected static $source = null;

	public static function init() {
		add_action( 'elementor/editor/footer', [ __CLASS__, 'print_template_views' ] );
		add_action( 'elementor/ajax/register_actions', [ __CLASS__, 'register_ajax_actions' ] );
		add_action( 'elementor/preview/enqueue_styles', [ __CLASS__, 'enqueue_preview_styles' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'editor_scripts' ] );
	}

	public static function print_template_views() {
		require get_template_directory() . '/inc/integrations/elementor/library/templates.php';
	}

	public static function enqueue_preview_styles() {
		wp_enqueue_style( 'sydney-template-preview-style', get_template_directory_uri() . '/inc/integrations/elementor/library/template-preview.css', '1.0.0' );

	}

	public static function editor_scripts() {
        wp_enqueue_script( 'sydney-template-library-script', get_template_directory_uri() . '/inc/integrations/elementor/library/template-library.min.js', [ 'elementor-editor', 'jquery-hover-intent' ], '1.0.0', true );
		wp_enqueue_style( 'sydney-template-library-style', get_template_directory_uri() . '/inc/integrations/elementor/library/template-library.min.css', '1.0.0' );

		$localized_data = [
            'sydneyProWidgets' => [],
			'isProActive' => false,
			'i18n' => [
				'templatesEmptyTitle' => esc_html__( 'No Templates Found', 'sydney' ),
				'templatesEmptyMessage' => esc_html__( 'Try different category or sync for new templates.', 'sydney' ),
				'templatesNoResultsTitle' => esc_html__( 'No Results Found', 'sydney' ),
				'templatesNoResultsMessage' => esc_html__( 'Please make sure your search is spelled correctly or try a different word.', 'sydney' ),
			]
	
        ];

		wp_localize_script( 'sydney-template-library-script', 'sydneyEditor', $localized_data );
	}

	/**
	 * Undocumented function
	 *
	 * @return Template_Library_Source
	 */
	public static function get_source() {
		if ( is_null( self::$source ) ) {
			self::$source = new Template_Library_Source();
		}

		return self::$source;
	}

	public static function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'sydney_get_template_library_data', function( $data ) {
			if ( ! current_user_can( 'edit_posts' ) ) {
				throw new \Exception( 'Access Denied' );
			}

			if ( ! empty( $data['editor_post_id'] ) ) {
				$editor_post_id = absint( $data['editor_post_id'] );

				if ( ! get_post( $editor_post_id ) ) {
					throw new \Exception( __( 'Post not found.', 'sydney' ) );
				}

				\Elementor\Plugin::instance()->db->switch_to_post( $editor_post_id );
			}

			$result = self::get_library_data( $data );

			return $result;
		} );

		$ajax->register_ajax_action( 'sydney_get_template_item_data', function( $data ) {
			if ( ! current_user_can( 'edit_posts' ) ) {
				throw new \Exception( 'Access Denied' );
			}

			if ( ! empty( $data['editor_post_id'] ) ) {
				$editor_post_id = absint( $data['editor_post_id'] );

				if ( ! get_post( $editor_post_id ) ) {
					throw new \Exception( __( 'Post not found', 'sydney' ) );
				}

				\Elementor\Plugin::instance()->db->switch_to_post( $editor_post_id );
			}

			if ( empty( $data['template_id'] ) ) {
				throw new \Exception( __( 'Template id missing', 'sydney' ) );
			}

			$result = self::get_template_data( $data );

			return $result;
		} );
	}

	public static function get_template_data( array $args ) {
		$source = self::get_source();
		$data = $source->get_data( $args );
		return $data;
	}

	public static function get_library_data( array $args ) {
		$source = self::get_source();

		if ( ! empty( $args['sync'] ) ) {
			Template_Library_Source::get_library_data( true );
		}

		return [
			'templates' => $source->get_items(),
			'category' => $source->get_categories(),
			'type_category' => $source->get_type_category(),
		];
	}
}

Template_Library_Manager::init();
