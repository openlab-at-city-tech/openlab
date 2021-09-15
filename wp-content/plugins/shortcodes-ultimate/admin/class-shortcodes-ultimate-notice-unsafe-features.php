<?php

final class Shortcodes_Ultimate_Notice_Unsafe_Features extends Shortcodes_Ultimate_Notice {

	public function display_notice() {

		if ( ! get_option( 'su_option_unsafe_features_auto_off' ) ) {
			return;
		}

		if ( $this->is_dismissed() ) {
			return;
		}

		if ( ! $this->current_user_can_view() ) {
			return;
		}

		// phpcs:disable
		$is_plugin_page =
			isset( $_GET['page'] ) &&
			in_array(
				$_GET['page'],
				array( 'shortcodes-ultimate', 'shortcodes-ultimate-settings' ),
				true
			);
		// phpcs:enable

		if ( 'dashboard' !== $this->get_current_screen_id() && ! $is_plugin_page ) {
			return;
		}

		$this->include_template();

	}

}
