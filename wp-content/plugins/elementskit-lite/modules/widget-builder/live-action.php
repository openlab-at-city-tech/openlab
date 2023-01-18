<?php
namespace ElementsKit_Lite\Modules\Widget_Builder;

defined( 'ABSPATH' ) || exit;


class Live_Action {
	private $id;

	public function __construct() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are taking the post id from the URL. The page only can access admin. So nonce verification is not required.
		$this->id = ( ! isset( $_GET['post'] ) ? 0 : intval( wp_unslash( $_GET['post'] ) ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We are checking the page action. The page only can access admin. So nonce verification is not required.
		if ( $this->id == 0 || ! isset( $_GET['action'] ) || $_GET['action'] != 'elementor' ) {
			return;
		}

		if ( get_post_type( $this->id ) != 'elementskit_widget' ) {
			return;
		}

		add_action( 'init', array( $this, 'reset' ) );
	}

	public function reset() {
		update_post_meta( $this->id, '_wp_page_template', 'elementor_canvas' );

		update_post_meta(
			$this->id,
			'_elementor_data', 
			'[{"id":"e3a6ad6","elType":"section","settings":[],"elements":[{"id":"77605d8","elType":"column","settings":{"_column_size":100,"_inline_size":null},"elements":[{"id":"0d8eeb3","elType":"widget","settings":{},"elements":[],"widgetType":"ekit_wb_' . $this->id . '"}],"isInner":false}],"isInner":false}]'
		);
	}
}
