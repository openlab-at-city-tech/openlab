<?php
/**
 * Footer builder column
 *
 * @package Kenta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Footer_Column' ) ) {

	class Kenta_Footer_Column extends Kenta_Builder_Column {
		/**
		 * @return false
		 */
		protected function isResponsive() {
			return true;
		}

		/**
		 * @return array
		 */
		protected function getDefaultSettings() {
			return [
				'direction' => 'column',
			];
		}
	}
}
