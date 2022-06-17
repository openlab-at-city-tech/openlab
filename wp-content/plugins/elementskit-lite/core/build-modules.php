<?php
namespace ElementsKit_Lite\Core;

use ElementsKit_Lite\Libs\Framework\Attr;

defined( 'ABSPATH' ) || exit;

/**
 * Module registrar.
 *
 * Call assosiated classes of every modules.
 *
 * @since 1.0.0
 * @access public
 */
class Build_Modules {

	private $modules;

	use \ElementsKit_Lite\Traits\Singleton;

	/**
	 * Hold the module list.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */

	public function __construct() {
		$this->modules = \ElementsKit_Lite\Config\Module_List::instance()->get_list( 'active' );

		foreach ( $this->modules as $module_slug => $module ) {
			if ( isset( $module['path'] ) ) {
				include_once $module['path'] . 'init.php';
			}

			// make the class name and call it.
			$class_name = (
				isset( $module['base_class_name'] )
				? $module['base_class_name']
				: '\ElementsKit_Lite\Modules\\' . \ElementsKit_Lite\Utils::make_classname( $module_slug ) . '\Init'
			);
			if ( class_exists( $class_name ) ) {
				new $class_name();
			}
		}
	}
}
