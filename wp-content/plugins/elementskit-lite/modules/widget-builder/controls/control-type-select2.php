<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

defined( 'ABSPATH' ) || exit;

class Control_Type_Select2 extends CT_Base {


	public function start_writing_conf( $file_handler, $conf ) {

		$ret = '';

		if ( ! empty( $conf->description ) ) {
			$ret .= "\t\t\t\t" . '\'description\' =>  esc_html( \'' . esc_html( $conf->description ) . '\' ),' . PHP_EOL;
		}

		if ( ! empty( $conf->default ) ) {

			if ( gettype( $conf->default ) == 'string' ) {
				$ret .= "\t\t\t\t" . '\'default\' =>  esc_html( \'' . esc_html( $conf->default ) . '\' ),' . PHP_EOL;
			} else {
				$ret     .= "\t\t\t\t" . '\'default\' => array(' . PHP_EOL;
				$defaults = (array) $conf->default;
				foreach ( $defaults as $key ) {
					$ret .= "\t\t\t\t\t" . '\'' . esc_html( $key ) . '\',' . PHP_EOL;
				}
				$ret .= "\t\t\t\t" . '),' . PHP_EOL;
			}
		}

		if ( ! empty( $conf->separator ) ) {
			$ret .= "\t\t\t\t" . '\'separator\' => \'' . esc_html( $conf->separator ) . '\' ,' . PHP_EOL;
		}

		if ( isset( $conf->multiple ) ) {
			$ret .= "\t\t\t\t" . '\'multiple\' => ' . ( $conf->multiple == 1 ? 'true' : 'false' ) . ' ,' . PHP_EOL;
		}

		if ( isset( $conf->show_label ) ) {
			$ret .= "\t\t\t\t" . '\'show_label\' => ' . ( $conf->show_label == 1 ? 'true' : 'false' ) . ' ,' . PHP_EOL;
		}

		if ( isset( $conf->label_block ) ) {
			$ret .= "\t\t\t\t" . '\'label_block\' => ' . ( $conf->label_block == 1 ? 'true' : 'false' ) . ' ,' . PHP_EOL;
		}

		$ret .= "\t\t\t\t" . '\'options\' => array(' . PHP_EOL;

		if ( ! empty( $conf->options ) ) {

			$realArray = (array) $conf->options;

			foreach ( $realArray as $val => $label ) {

				$ret .= "\t\t\t\t\t" . '\'' . esc_html( $val ) . '\' => esc_html( \'' . esc_html( $label ) . '\' ),' . PHP_EOL;
			}
		}

		$ret .= "\t\t\t\t" . '),' . PHP_EOL;

		if ( ! empty( $conf->selectors ) ) {

			$selectors = (array) $conf->selectors;
			$ret      .= "\t\t\t\t" . '\'selectors\' => array(' . PHP_EOL;

			foreach ( $selectors as $selectorName => $selectorValue ) {
				$selectorProperty = str_replace( ',', ', {{WRAPPER}} ', $selectorName );
				$ret             .= "\t\t\t\t\t" . '\'{{WRAPPER}} ' . $selectorProperty . '\' => \'' . esc_html( $selectorValue ) . '\',' . PHP_EOL;
			}

			$ret .= "\t\t\t\t" . '),' . PHP_EOL;
		}

		if ( ! empty( $conf->classes ) ) {
			$ret .= "\t\t\t\t" . '\'classes\' => \'' . esc_html( $conf->classes ) . '\',' . PHP_EOL;
		}

		return $ret;
	}
}
