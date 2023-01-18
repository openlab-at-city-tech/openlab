<?php
namespace ElementsKit_Lite\Modules\ElementsKit_Icon_Pack;

defined( 'ABSPATH' ) || exit;

class Icons {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

		add_filter( 'elementor/icons_manager/additional_tabs', array( $this, 'add_font' ) );
	}

	public function enqueue_frontend() {
		wp_enqueue_style( 'elementor-icons-ekiticons', Init::get_url() . 'assets/css/ekiticons.css', \ElementsKit_Lite::version() );
	}

	public function add_font( $font ) {
		$font_new['ekiticons'] = array(
			'name'          => 'ekiticons',
			'label'         => esc_html__( 'ElementsKit Icon Pack', 'elementskit-lite' ),
			'url'           => Init::get_url() . 'assets/css/ekiticons.css',
			'prefix'        => 'icon-',
			'displayPrefix' => 'icon',
			'labelIcon'     => 'icon icon-ekit',
			'ver'           => \ElementsKit_Lite::version(),
			'fetchJson'     => Init::get_url() . 'assets/js/ekiticons.json',
			'native'        => true,
		);
		return array_merge( $font, $font_new );
	}
}
