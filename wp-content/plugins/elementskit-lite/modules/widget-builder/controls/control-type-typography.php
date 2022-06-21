<?php

namespace ElementsKit_Lite\Modules\Widget_Builder\Controls;

defined( 'ABSPATH' ) || exit;

class Control_Type_Typography extends CT_Base {


	public function start_writing_conf( $file_handler, $conf ) {

		$ret = '';

		if ( ! empty( $conf->label ) ) {
			$ret .= "\t\t\t\t" . '\'label\' => esc_html( \'' . esc_html( $conf->label ) . '\', \'elementskit\' ),' . PHP_EOL;
		}

		if ( ! empty( $conf->selector ) ) {
			$selectorProperty = str_replace( ',', ', {{WRAPPER}} ', esc_html( $conf->selector ) );
			$ret             .= "\t\t\t\t" . '\'selector\' => \'{{WRAPPER}} ' . $selectorProperty . '\',' . PHP_EOL;
		}

		if ( isset( $conf->show_label ) || isset( $conf->label_block ) || ! empty( $conf->separator ) || ! empty( $conf->classes ) ) :
			$ret .= "\t\t\t\t'fields_options' => [";
			$ret .= "\n\t\t\t\t\t'typography' => [";

			if ( isset( $conf->show_label ) ) {
				$ret .= "\n\t\t\t\t\t\t'show_label' => " . ( $conf->show_label == 1 ? 'true' : 'false' ) . ',';
			}

			if ( isset( $conf->label_block ) ) {
				$ret .= "\n\t\t\t\t\t\t'label_block' => " . ( $conf->label_block == 1 ? 'true' : 'false' ) . ',';
			}

			if ( ! empty( $conf->separator ) ) {
				$ret .= "\n\t\t\t\t\t\t'separator' => '" . esc_html( $conf->separator ) . "',";
			}

			if ( ! empty( $conf->classes ) ) {
				$ret .= "\n\t\t\t\t\t\t'classes' => '" . esc_html( $conf->classes ) . "',";
			}

			$ret .= "\n\t\t\t\t\t],";
			$ret .= "\n\t\t\t\t],\n";
		 endif;

		return $ret;
	}
}
