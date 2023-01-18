<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

defined( 'ABSPATH' ) || exit;

class Control_Type_Icons extends CT_Base {

	public function start_writing_conf( $file_handler, $conf ) {

		$ret = '';

		if ( ! empty( $conf->description ) ) {
			$ret .= "\t\t\t\t" . '\'description\' =>  esc_html( \'' . esc_html( $conf->description ) . '\' ),' . PHP_EOL;
		}

		if ( ! empty( $conf->separator ) ) {
			$ret .= "\t\t\t\t" . '\'separator\' => \'' . esc_html( $conf->separator ) . '\' ,' . PHP_EOL;
		}

		if ( ! empty( $conf->classes ) ) {
			$ret .= "\t\t\t\t" . '\'classes\' => \'' . esc_html( $conf->classes ) . '\' ,' . PHP_EOL;
		}

		if ( isset( $conf->show_label ) ) {
			$ret .= "\t\t\t\t" . '\'show_label\' => ' . ( $conf->show_label == 1 ? 'true' : 'false' ) . ' ,' . PHP_EOL;
		}

		if ( isset( $conf->label_block ) ) {
			$ret .= "\t\t\t\t" . '\'label_block\' => ' . ( $conf->label_block == 1 ? 'true' : 'false' ) . ' ,' . PHP_EOL;
		}

		if ( ! empty( $conf->skin ) ) {
			$ret .= "\t\t\t\t" . '\'skin\' => \'' . esc_html( $conf->skin ) . '\' ,' . PHP_EOL;
		}

		if ( ! empty( $conf->fa4compatibility ) ) {
			$ret .= "\t\t\t\t" . '\'fa4compatibility\' => \'' . esc_html( $conf->fa4compatibility ) . '\' ,' . PHP_EOL;
		}

		if ( ! empty( $conf->default ) ) {

			$ret .= "\t\t\t\t" . '\'default\' => array(' . PHP_EOL;
			$ret .= "\t\t\t\t\t" . '\'value\' => \'' . esc_html( $conf->default->value ) . '\',' . PHP_EOL;
			$ret .= "\t\t\t\t\t" . '\'library\' => \'' . esc_html( $conf->default->library ) . '\',' . PHP_EOL;
			$ret .= "\t\t\t\t" . ')' . PHP_EOL;

		}

		return $ret;
	}
}
