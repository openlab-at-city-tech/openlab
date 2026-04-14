<?php

namespace FileBird\Blocks;

defined( 'ABSPATH' ) || exit;

class DocumentLibraryShortcode {
	private $pageSuffix;
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'adminMenu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
		add_shortcode( 'fbdl', array( $this, 'add_shortcode' ) );
	}

	public function adminMenu() {
		$this->pageSuffix = add_menu_page(
			__( 'Document Library', 'filebird-dl' ),
			__( 'Document Library', 'filebird-dl' ),
			'manage_options',
			'fbdl-document-library-shortcode',
			array( $this, 'pageCallback' ),
			'dashicons-media-document'
		);
	}

	public function add_shortcode( $attrs ) {
		if( isset( $attrs['setting'] ) ) {
			$defaultSettings = array(
				'request' => array(
					'pagination'     => array(
						'current' => 1,
						'limit'   => 5,
					),
					'search'         => '',
					'orderBy'        => 'post_title',
					'orderType'      => 'DESC',
					'selectedFolder' => '',
				),
				'title'   => 'Title',
				'column'  => 3,
				'layout'  => 'grid',
			);
	
			extract(
				shortcode_atts(
					array(
						'setting' => wp_json_encode( $defaultSettings ),
					),
					$attrs
				)
			);
	
			$params = json_decode( $setting, true );
		}
		if( ! isset( $attrs['setting'] ) || is_null( $params ) ) {
			$attrs = shortcode_atts(
				array(
					'column' => 3,
					'title' => 'Title',
					'layout' => 'grid',
					'current' => 1,
					'limit' => 5,
					'orderby' => 'post_title',
					'order' => 'DESC',
					'selected_folder' => '',
					'search' => '',
				), $attrs, 'fbdl'
			);
			$params = [
				'request' => [
					'pagination' => [
						'current' => (int)$attrs['current'],
						'limit' => (int)$attrs['limit'],
					],
					'search' => $attrs['search'],
					'orderBy' => $attrs['orderby'],
					'orderType' => $attrs['order'],
					'selectedFolder' => $attrs['selected_folder'],
				],
				'title' => $attrs['title'],
				'column' => $attrs['column'],
				'layout' => $attrs['layout'],
			];
		}
		
		$params['request']['selectedFolder'] = implode( ',', array_map(function( $id ){ 
			return Helpers::encrypt( $id );
		 }, explode(',', $params['request']['selectedFolder'])) );
		$params['request']['selectedFolder'] = explode( ',', $params['request']['selectedFolder'] );

		$html  = '<div id="filebird-document-library">';
		$html .= '<div class="njt-fbdl" data-json="';
		$html .= esc_attr( wp_json_encode( $params ) );
		$html .= '"></div></div>';

		// do_action( 'fbdl_enqueue_frontend' );

		return $html;
	}

	public function pageCallback() {
		echo '<div id="fbdl-shortcode"></div>';
	}

	public function enqueueScripts( $hook_suffix ) {
		if ( $hook_suffix === $this->pageSuffix ) {
			do_action( 'fbdl_enqueue_frontend' );

			wp_register_script( 'fbdl-shortcode', FBV_DL_URL . 'blocks/dist/shortcode/shortcode.js', array( 'wp-components', 'wp-element' ), FBV_DL_VERSION, true );
			wp_enqueue_script( 'fbdl-shortcode' );

			wp_set_script_translations( 'fbdl-shortcode', 'filebird-dl', FBV_DL_DIR . '/languages/' );

			wp_enqueue_style( 'fbdl-shortcode', FBV_DL_URL . 'blocks/dist/shortcode/shortcode.css', array( 'wp-components' ), FBV_DL_VERSION );
		}
	}
};

new DocumentLibraryShortcode();
