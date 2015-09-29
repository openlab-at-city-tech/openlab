<?php
/**
 * Device Module
 *
 * @version $Id$
 * @copyright 2014 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Device extends DWModule {
		protected static $except = 'Except for:';
		public static $option = array( 'device' => 'Device' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget when viewed by all devices?';
		protected static $type = 'complex';

		public static function admin() {
			$DW = &$GLOBALS['DW'];

			parent::admin();

		  $list = array( 'desktop' => __('Desktop'), 'mobile' => __('Mobile') );
		  self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
		}
	}
?>