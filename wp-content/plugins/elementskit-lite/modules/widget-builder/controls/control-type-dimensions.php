<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

defined( 'ABSPATH' ) || exit;

class Control_Type_Dimensions extends CT_Base {

	public function start_writing_conf( $file_handler, $conf ) {
		$ret = '';

		if ( ! empty( $conf->description ) ) {
			$ret .= "\t\t\t\t" . '\'description\' =>  esc_html( \'' . esc_html( $conf->description ) . '\' ),' . PHP_EOL;
		}

		if ( ! empty( $conf->placeholder ) ) {
			$ret .= "\t\t\t\t" . '\'placeholder\' =>  esc_html( \'' . esc_html( $conf->placeholder ) . '\' ),' . PHP_EOL;
		}

		if ( ! empty( $conf->allowed_dimensions ) ) {
			$allowed_dimensions = (array) $conf->allowed_dimensions;
			$ret               .= "\t\t\t\t" . '\'allowed_dimensions\' => array(' . PHP_EOL;

			foreach ( $allowed_dimensions as $key => $value ) {
				$ret .= "\t\t\t\t\t" . '\'' . esc_html( $value ) . '\',' . PHP_EOL;
			}

			$ret .= "\t\t\t\t" . '),' . PHP_EOL;
		}

		if ( isset( $conf->show_label ) ) {
			$ret .= "\t\t\t\t" . '\'show_label\' => ' . ( $conf->show_label == 1 ? 'true' : 'false' ) . ',' . PHP_EOL;
		}

		if ( isset( $conf->label_block ) ) {
			$ret .= "\t\t\t\t" . '\'label_block\' => ' . ( $conf->label_block == 1 ? 'true' : 'false' ) . ',' . PHP_EOL;
		}

		if ( ! empty( $conf->separator ) ) {
			$ret .= "\t\t\t\t" . '\'separator\' => \'' . esc_html( $conf->separator ) . '\',' . PHP_EOL;
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

		if ( ! empty( $conf->classes ) ) {
			$ret .= "\t\t\t\t" . '\'classes\' => \'' . esc_html( $conf->classes ) . '\',' . PHP_EOL;
		}

		return $ret;
	}
}
