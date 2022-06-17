<?php

namespace ElementsKit_Lite\Core;

use ElementsKit_Lite\Libs\Framework\Classes\Onboard_Status;

defined( 'ABSPATH' ) || exit;

class Activation_Actions {

	private $key     = 'elementskit-lite__plugin_activated';
	private $has_key = false;

	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		$this->process_key();

		if ( $this->has_key === false ) {
			return;
		}

		// call activation job classes or methods here.
		$this->flush_rewrite_rules();
		$this->redirect_to_onboard();
	}

	private function process_key() {
		if ( ! empty( get_option( $this->key ) ) ) {
			$this->has_key = true;
			delete_option( $this->key );
		}
	}

	private function flush_rewrite_rules() {
		// all CPTs must be declared completely before flushing rewrite rules. otherwise, it won't work as expected.
		flush_rewrite_rules();
	}

	private function redirect_to_onboard() {
		// Onboard_Status::redirect_onboard();
	}

}
