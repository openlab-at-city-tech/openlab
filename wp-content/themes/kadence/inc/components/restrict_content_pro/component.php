<?php
/**
 * Kadence\Restrict_Content_Pro\Component class
 *
 * @package kadence
 */

namespace Kadence\Restrict_Content_Pro;

use Kadence\Component_Interface;
use function add_action;


/**
 * Class for adding Restrict Content Pro plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string {
		return 'restrict_content_pro';
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
		wp_enqueue_style( 'kadence-rcp', get_theme_file_uri( '/assets/css/rcp.min.css' ), [], KADENCE_VERSION );
	}
}
