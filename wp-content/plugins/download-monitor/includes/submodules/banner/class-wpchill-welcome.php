<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPChill_Welcome' ) ) {

	class WPChill_Welcome {

		private static $instance;

		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPChill_Welcome ) ) {
				self::$instance = new WPChill_Welcome();
			}

			return self::$instance;
		}
	}
}
