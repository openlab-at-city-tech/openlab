<?php
/**
 * Attachment Module
 *
 * @version $Id: attachment_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Attachment extends DWModule {
		public static $option = array( 'attachment'	=> 'Attachments' );
		protected static $question = 'Show widget on attachment pages?';
	}
?>