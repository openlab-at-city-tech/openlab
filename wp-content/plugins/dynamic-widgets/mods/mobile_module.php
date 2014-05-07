<?php
/**
 * Mobile Module
 *
 * @version $Id$
 * @copyright 2014 Jacco Drabbe
 */

	class DW_Mobile extends DWModule {
		public static $option = array( 'mobile' => 'Mobile device' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget when viewed by a mobile device?';
	}
?>