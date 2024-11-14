<?php
	/**
	 * @version $Id: fimage_module.php 1474291 2016-08-14 20:35:12Z qurl $
	 * @copyright 2015 Jacco Drabbe
	 **/

	class DW_Fimage extends DWModule {
		public static $option = array( 'fimage' => 'Featured image' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget when there is a featured image?';
	}
?>
