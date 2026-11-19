<?php
/**
 * Attachment Module
 *
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Attachment extends DWModule {
		public static $option = array( 'attachment'	=> 'Attachments' );
		protected static $question = 'Show widget on attachment pages?';
	}
?>