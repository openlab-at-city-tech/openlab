<?php 
namespace ElementsKit_Lite\Libs\Pro_Label;

defined( 'ABSPATH' ) || exit;

class Init {
	use Admin_Notice;

	public function __construct() {
		add_action( 'current_screen', array( $this, 'hook_current_screen' ) );
	}

	public function hook_current_screen( $screen ) {
		if ( ! in_array( $screen->id, array( 'nav-menus', 'toplevel_page_elementskit', 'edit-elementskit_template', 'dashboard' ) ) ) {
			return;
		}

		$activation_stamp = get_option( 'elementskit_lite_activation_stamp' );
		if ( date( 'd', ( time() - $activation_stamp ) ) > 10 ) {
			add_action( 'admin_head', array( $this, 'show_go_pro_notice' ) );
		}

		add_action( 'admin_footer', array( $this, 'footer_alert_box' ) );
	}
}
