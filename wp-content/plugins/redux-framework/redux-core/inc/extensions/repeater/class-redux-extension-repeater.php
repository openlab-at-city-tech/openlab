<?php
/**
 * Redux Repeater Extension Class
 *
 * @package Redux
 * @author  Dovy Paukstys & Kevin Provance <kevin.provance@gmail.com>
 * @class   Redux_Extension_Repeater
 *
 * @version 4.3.13
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'Redux_Extension_Repeater' ) ) {


	/**
	 * Class Redux_Extension_Repeater
	 */
	class Redux_Extension_Repeater extends Redux_Extension_Abstract {

		/**
		 * Extension version.
		 *
		 * @var string
		 */
		public static $version = '4.3.13';

		/**
		 * Extension friendly name.
		 *
		 * @var string
		 */
		public $extension_name = 'Repeater';

		/**
		 * Class Constructor. Defines the args for the extensions class
		 *
		 * @since       1.0.0
		 * @access      public
		 *
		 * @param       object $redux Parent settings.
		 *
		 * @return      void
		 */
		public function __construct( $redux ) {
			parent::__construct( $redux, __FILE__ );

			$this->add_field( 'repeater' );
		}
	}
}

class_alias( 'Redux_Extension_Repeater', 'ReduxFramework_Extension_repeater' );
