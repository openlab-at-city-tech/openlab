<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Control_Type_Input for \Elementor\Controls_Manager::TEXT
 * check below link for argument list
 *
 *
 * @see https://developers.elementor.com/elementor-controls/text-control/
 *
 * @package Elementor
 */
class Control_Type_Input extends CT_Base {

	public function start_writing_conf( $file_handler, $conf ) {

		$ret = '';

		if ( ! empty( $conf->placeholder ) ) {
			$ret .= "\t\t\t\t" . '\'placeholder\' =>  esc_html( \'' . esc_html( $conf->placeholder ) . '\' ),' . PHP_EOL;
		}

		if ( ! empty( $conf->description ) ) {
			$ret .= "\t\t\t\t" . '\'description\' =>  esc_html( \'' . esc_html( $conf->description ) . '\' ),' . PHP_EOL;
		}

		if ( ! empty( $conf->default ) ) {
			$ret .= "\t\t\t\t" . '\'default\' =>  esc_html( \'' . esc_html( $conf->default ) . '\' ),' . PHP_EOL;
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

		if ( ! empty( $conf->input_type ) ) {
			$ret .= "\t\t\t\t" . '\'input_type\' => \'' . esc_html( $conf->input_type ) . '\',' . PHP_EOL;
		}

		return $ret;
	}
}
