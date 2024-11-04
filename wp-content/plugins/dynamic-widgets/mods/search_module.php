<?php
/**
 * Search Module
 *
 * @version $Id: search_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Search extends DWModule {
		public static $option = array( 'search' => 'Search page' );
		protected static $question = 'Show widget on the search page?';
	}
?>