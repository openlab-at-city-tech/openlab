<?php
/**
 * Theme component interface.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

interface Component_Interface {

	/**
	 * Component initialization.
	 *
	 * Usually contains hooks to integrate with WordPress
	 * and/or loads additional sub-components.
	 */
	public static function init();

}
