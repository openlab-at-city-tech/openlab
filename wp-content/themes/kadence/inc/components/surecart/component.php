<?php
/**
 * Kadence\Surecart\Component class
 *
 * @package kadence
 */

namespace Kadence\Surecart;

use Kadence\Component_Interface;


/**
 * Class for adding Tankmath plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'surecart';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ), 60 );
	}
	/**
	 * Add some css styles for Restrict Content Pro
	 */
	public function add_styles() {
		wp_enqueue_style( 'kadence-surecart', get_theme_file_uri( '/assets/css/surecart.min.css' ), array(), KADENCE_VERSION );
	}
}
