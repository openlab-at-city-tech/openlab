<?php
/**
 * Header builder column
 *
 * @package Kenta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Header_Column' ) ) {

	class Kenta_Header_Column extends Kenta_Builder_Column {
		/**
		 * @return false
		 */
		protected function isResponsive() {
			return false;
		}

		/**
		 * @return array
		 */
		protected function getDefaultSettings() {
			return [
				'align-items' => 'center',
				'exclude'     => [
					'align-items',
				],
			];
		}
	}
}
