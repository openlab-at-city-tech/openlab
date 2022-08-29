<?php
namespace ElementsKit_Lite\Modules\ElementsKit_Icon_Pack;

defined( 'ABSPATH' ) || exit;

class Init {

	public static function get_url() {
		return \ElementsKit_Lite::module_url() . 'elementskit-icon-pack/';
	}

	public static function get_dir() {
		return \ElementsKit_Lite::module_dir() . 'elementskit-icon-pack/';
	}

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

		add_filter( 'elementor/icons_manager/additional_tabs', array( $this, 'register_icon_pack_to_elementor' ) );
	}

	public function enqueue_frontend() {
		wp_enqueue_style( 'elementor-icons-ekiticons', self::get_url() . 'assets/css/ekiticons.css', \ElementsKit_Lite::version() );
	}

	public function register_icon_pack_to_elementor( $font ) {
		$font_new['ekiticons'] = array(
			'name'          => 'ekiticons',
			'label'         => esc_html__( 'ElementsKit Icon Pack', 'elementskit-lite' ),
			'url'           => self::get_url() . 'assets/css/ekiticons.css',
			'prefix'        => 'icon-',
			'displayPrefix' => 'icon',
			'labelIcon'     => 'icon icon-ekit',
			'ver'           => \ElementsKit_Lite::version(),
			'fetchJson'     => self::get_url() . 'assets/js/ekiticons.json',
			'native'        => true,
		);
		return array_merge( $font, $font_new );
	}
}
