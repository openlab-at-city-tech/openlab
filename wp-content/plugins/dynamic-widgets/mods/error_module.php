<?php
/**
 * Error Module
 *
 * @version $Id: error_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_E404 extends DWModule {
		public static $option = array( 'e404' => 'Error Page' );
		protected static $question = 'Show widget on the error page?';
	}
?>