<?php
/**
 * Kadence\Polylang\Component class
 *
 * @package kadence
 */

namespace Kadence\Polylang;

use Kadence\Component_Interface;
use function add_action;


/**
 * Class for adding Polylang plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string {
		return 'polylang';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'add_styles' ], 60 );
	}
	/**
	 * Add some css styles for Restrict Content Pro
	 */
	public function add_styles() {
		wp_enqueue_style( 'kadence-polylang', get_theme_file_uri( '/assets/css/polylang.min.css' ), [], KADENCE_VERSION );
	}
}
