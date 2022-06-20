<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Class Control_Type_Slider
 *
 * @see https://developers.elementor.com/elementor-controls/slider-control/
 *
 * @package ElementsKit_Lite\Modules\Widget_Builder\Controls
 */
class Control_Type_Slider extends CT_Base {

	public function start_writing_conf( $file_handler, $conf ) {

		$ret = '';

		if ( ! empty( $conf->description ) ) {
			$ret .= "\t\t\t\t" . '\'description\' =>  esc_html( \'' . esc_html( $conf->description ) . '\' ),' . PHP_EOL;
		}

		if ( ! empty( $conf->separator ) ) {
			$ret .= "\t\t\t\t" . '\'separator\' => \'' . esc_html( $conf->separator ) . '\',' . PHP_EOL;
		}

		if ( ! empty( $conf->classes ) ) {
			$ret .= "\t\t\t\t" . '\'classes\' => \'' . esc_html( $conf->classes ) . '\',' . PHP_EOL;
		}

		if ( isset( $conf->show_label ) ) {
			$ret .= "\t\t\t\t" . '\'show_label\' => ' . ( $conf->show_label == 1 ? 'true' : 'false' ) . ',' . PHP_EOL;
		}

		if ( isset( $conf->label_block ) ) {
			$ret .= "\t\t\t\t" . '\'label_block\' => ' . ( $conf->label_block == 1 ? 'true' : 'false' ) . ',' . PHP_EOL;
		}

		if ( ! empty( $conf->default ) ) {

			$ret .= "\t\t\t\t" . '\'default\' => array(' . PHP_EOL;
			$ret .= "\t\t\t\t\t" . '\'unit\' => \'' . esc_html( $conf->default->unit ) . '\',' . PHP_EOL;
			$ret .= "\t\t\t\t\t" . '\'size\' => \'' . esc_html( $conf->default->size ) . '\',' . PHP_EOL;
			$ret .= "\t\t\t\t" . '),' . PHP_EOL;

		}

		if ( ! empty( $conf->size_units ) ) {

			$ret .= "\t\t\t\t" . '\'size_units\' => array(' . PHP_EOL;

			foreach ( $conf->size_units as $size ) {

				$ret .= "\t\t\t\t\t" . '\'' . esc_html( $size ) . '\',' . PHP_EOL;
			}

			$ret .= "\t\t\t\t" . '),' . PHP_EOL;
		}

		if ( ! empty( $conf->range ) ) {

			$ret .= "\t\t\t\t" . '\'range\' => array(' . PHP_EOL;

			$realArray = (array) $conf->range;

			foreach ( $realArray as $val => $label ) {

				$ret .= "\t\t\t\t\t" . '\'' . $val . '\' => array(' . PHP_EOL;
				$ret .= "\t\t\t\t\t\t" . '\'min\' => \'' . floatval( $label->min ) . '\',' . PHP_EOL;
				$ret .= "\t\t\t\t\t\t" . '\'max\' => \'' . floatval( $label->max ) . '\',' . PHP_EOL;
				$ret .= "\t\t\t\t\t\t" . '\'step\' => \'' . esc_html( $label->step ) . '\',' . PHP_EOL;
				$ret .= "\t\t\t\t\t" . '),' . PHP_EOL;
			}

			$ret .= "\t\t\t\t" . ')' . PHP_EOL;
		}

		if ( ! empty( $conf->selectors ) ) {

			$selectors = (array) $conf->selectors;
			$ret      .= "\t\t\t\t" . '\'selectors\' => array(' . PHP_EOL;

			foreach ( $selectors as $selectorName => $selectorValue ) {
				$selectorProperty = str_replace( ',', ', {{WRAPPER}} ', $selectorName );
				$ret             .= "\t\t\t\t\t" . '\'{{WRAPPER}} ' . $selectorProperty . '\' => \'' . esc_html( $selectorValue ) . '\',' . PHP_EOL;
			}

			$ret .= "\t\t\t\t" . '),' . PHP_EOL;
		}

		return $ret;
	}
}
