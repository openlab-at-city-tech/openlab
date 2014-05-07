<?php
/**
 * Day Module
 *
 * @version $Id: day_module.php 580343 2012-08-01 17:34:38Z qurl $
 * @copyright 2012 Jacco Drabbe
 */

	class DW_Day extends DWModule {
		protected static $info = 'Beware of double rules!';
		protected static $except = 'Except the days';
		public static $option = array( 'day'	=> 'Days' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget on every day?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			$days = array();
			$daysec = 86400;
			$start = mktime(0, 0, 1, 7, 29, 2012);	// We take a random Sunday - Go figure when I added this functionality
			$end = $start + (8 * $daysec);

			for ( $i = $start; $i < $end; $i += $daysec ) {
				$days[date('N', $i)] = date('l', $i);
			}

			self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $days);
		}
	}
?>