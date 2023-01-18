<?php

class Tribe__Events__Deactivation extends Tribe__Abstract_Deactivation {

	/**
	 * Set a flag to indicate that the plugin has been deactivated
	 * and needs to be reinitialized if it is reactivated
	 */
	private function set_flags() {
		// Ensure the class is loaded before using it, we're in shutdown context and common autoloader might be unset.
		require_once __DIR__ . '/Updater.php';
		$updater = new Tribe__Events__Updater( Tribe__Main::VERSION );

		// Reset uses cached values, which means the sub-sites will be overwritten by the main site cache.
		if ( $this->network && ! is_main_site() ) {
			return;
		}

		$updater->reset();
	}

	/**
	 * Remove event-related capabilities
	 */
	private function clear_capabilities() {
		// Ensure the class is loaded before using it, we're in shutdown context and common autoloader might be unset.
		require_once __DIR__ . '/Capabilities.php';
		$capabilities = new Tribe__Events__Capabilities();
		$capabilities->remove_all_caps();
	}


	/**
	 * The deactivation routine for a single blog
	 */
	protected function blog_deactivate() {
		$this->set_flags();
		$this->clear_capabilities();
		$this->flush_rewrite_rules();
		/**
		 * Fires an action during the-events-calendar deactivation
		 */
		do_action( 'tribe_events_blog_deactivate' );
	}

	/**
	 * An abridged version that is less DB intensive.
	 *
	 * @see wp_is_large_network() and the 'wp_is_large_network' filter
	 */
	protected function short_blog_deactivate() {
		$this->set_flags();
	}
}
