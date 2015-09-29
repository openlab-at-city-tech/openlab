<?php
/**
 * dynwid_class.php - Dynamic Widgets Classes loader (PHP5)
 *
 * @version $Id: dynwid_class.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");
 
	$dh = opendir(DW_CLASSES);
	while ( ($file = readdir($dh)) !== FALSE ) {
		if ( $file != '.' && $file != '..' && substr(strrchr($file, '_'), 1) == 'class.php' ) {
				include_once(DW_CLASSES . $file);
		}
	}
?>