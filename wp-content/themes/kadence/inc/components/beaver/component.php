<?php
/**
 * Kadence\Beaver\Component class
 *
 * @package kadence
 */

namespace Kadence\Beaver;

use Kadence\Component_Interface;
use FLBuilderModel;

/**
 * Class for adding Woocommerce plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string {
		return 'beaver';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'kadence_entry_content_class', [ $this, 'filter_content_entry_class' ] );
	}
	/**
	 * Filters the content entry class for beaver builder so css doesn't conflict.
	 *
	 * @param string $class the entry container class.
	 */
	public function filter_content_entry_class( $class ) {
		if ( FLBuilderModel::is_builder_enabled() ) {
			$class = 'entry-content';
		}
		return $class;
	}
}
