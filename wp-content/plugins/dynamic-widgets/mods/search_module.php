<?php
/**
 * Search Module
 *
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Search extends DWModule {
		public static $option = array( 'search' => 'Search page' );
		protected static $question = 'Show widget on the search page?';
	}
?>