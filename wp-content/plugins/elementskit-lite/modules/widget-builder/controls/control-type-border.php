<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

defined( 'ABSPATH' ) || exit;

class Control_Type_Border extends CT_Base {

	public function start_writing_conf( $file_handler, $conf ) {

		$ret = '';
	
		$ret .= "\t\t\t\t'fields_options' => [";
		$ret .= "\n\t\t\t\t\t'border' => [";
		$ret .= "\n\t\t\t\t\t\t'label' => '" . esc_html( $conf->label ) . "',";
		
		if ( ! empty( $conf->description ) ) {
			$ret .= "\n\t\t\t\t\t\t'description' => '" . esc_html( $conf->description ) . "',";
		}

		if ( isset( $conf->show_label ) ) {
			$ret .= "\n\t\t\t\t\t\t'show_label' => " . ( $conf->show_label == 1 ? 'true' : 'false' ) . ',';
		}

		if ( isset( $conf->label_block ) ) {
			$ret .= "\n\t\t\t\t\t\t'label_block' => " . ( $conf->label_block == 1 ? 'true' : 'false' ) . ',';
		}

		$ret .= "\n\t\t\t\t\t],";
		$ret .= "\n\t\t\t\t],\n";

		if ( ! empty( $conf->separator ) ) {
			$ret .= "\t\t\t\t" . '\'separator\' => \'' . esc_html( $conf->separator ) . '\' ,' . PHP_EOL;
		}

		if ( ! empty( $conf->selector ) ) {
			$selectorProperty = str_replace( ',', ', {{WRAPPER}} ', esc_html( $conf->selector ) );
			$ret             .= "\t\t\t\t" . '\'selector\' => \'{{WRAPPER}} ' . $selectorProperty . '\' ,' . PHP_EOL;
		}

		return $ret;
	}
}
