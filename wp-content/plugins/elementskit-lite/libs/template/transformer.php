<?php
namespace ElementsKit_Lite\Libs\Template;

defined( 'ABSPATH' ) || exit;

class Transformer {

	private $prefix;

	public function render( $str, $prefix ) {
		$str          = trim( $str );
		$this->prefix = $prefix;

		$fn_length = explode( '(', $str );
		if ( count( $fn_length ) == 2 ) {
			$method = $fn_length[0]; // backward support
			
			if ( method_exists( $this, $method ) ) {
				return $this->$method( rtrim( $fn_length[1], ')' ) );
			}
			return $str;
		}

		return $this->variable( $str );
	}

	private function variable( $str ) {
		$str_var_set = explode( '.', $str );
		$array_parts = '';
		foreach ( $str_var_set as $i => $var ) {
			$array_parts .= '["' . ( $i > 0 ? '' : $this->prefix ) . $var . '"]';
		}

		return '<?php echo isset($settings' . $array_parts . ') ? $settings' . $array_parts . ' : ""; ?>';
	}

	private function icon( $str ) {
		return '<?php Icons_Manager::render_icon($settings["' . $this->prefix . trim( $str ) . '"]); ?>';
	}
}
