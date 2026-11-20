<?php

final class Shortcodes_Ultimate_Admin_About extends Shortcodes_Ultimate_Admin {

	public function add_menu_pages() {

		/**
		 * Submenu: About
		 * admin.php?page=shortcodes-ultimate
		 */
		$this->add_submenu_page(
			rtrim( $this->plugin_prefix, '-_' ),
			__( 'About', 'shortcodes-ultimate' ),
			__( 'About', 'shortcodes-ultimate' ),
			$this->get_capability(),
			rtrim( $this->plugin_prefix, '-_' ),
			array( $this, 'the_menu_page' )
		);

	}

	public function the_menu_page() {
		$this->the_template( 'admin/partials/pages/about' );
	}

	public function enqueue_scripts() {

		if ( ! $this->is_component_page() ) {
			return;
		}

		if ( ! su_fs()->can_use_premium_code() && ! su_has_active_addons() ) {
			wp_enqueue_script( 'plugin-install' );
			add_thickbox();
		}

		wp_enqueue_script(
			'shortcodes-ultimate-admin-about',
			plugins_url( 'js/about/index.js', __FILE__ ),
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . 'js/about/index.js' ),
			true
		);

		wp_enqueue_style(
			'shortcodes-ultimate-admin',
			plugins_url( 'css/admin.css', __FILE__ ),
			false,
			filemtime( plugin_dir_path( __FILE__ ) . 'css/admin.css' )
		);

	}

	public function plugin_action_links( $links ) {

		array_unshift(
			$links,
			sprintf(
				'<a href="%s">%s</a>',
				esc_attr( $this->get_component_url() ),
				esc_html( __( 'About', 'shortcodes-ultimate' ) )
			)
		);

		return $links;

	}

}
