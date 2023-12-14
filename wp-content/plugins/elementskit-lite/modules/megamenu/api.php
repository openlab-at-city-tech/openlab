<?php 
namespace ElementsKit_Lite;

use ElementsKit_Lite\Libs\Framework\Attr;
use ElementsKit_Lite\Modules\Megamenu\Init;

defined( 'ABSPATH' ) || exit;

class Megamenu_Api extends Core\Handler_Api {

	public function config() {
		$this->prefix = 'megamenu';
	}

	public function get_save_menuitem_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$menu_item_id       = $this->request['settings']['menu_id'];
		$menu_item_settings = wp_json_encode( $this->request['settings'], JSON_UNESCAPED_UNICODE );
		update_post_meta( $menu_item_id, Init::$menuitem_settings_key, $menu_item_settings );

		return array(
			'saved'   => 1,
			'message' => esc_html__( 'Saved', 'elementskit-lite' ),
		);
	}

	public function get_get_menuitem_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$menu_item_id = $this->request['menu_id'];

		$data = get_post_meta( $menu_item_id, Init::$menuitem_settings_key, true );
		return (array) json_decode( $data );
	}

	public function get_megamenu_content() {
		$menu_item_id = intval($this->request['id']);

		if (!get_post_status ($menu_item_id) || post_password_required($menu_item_id)) {
			return;
		}

		$elementor = \Elementor\Plugin::instance();
		$output   = $elementor->frontend->get_builder_content_for_display($menu_item_id);

		return $output;
	}
}
new Megamenu_Api();
