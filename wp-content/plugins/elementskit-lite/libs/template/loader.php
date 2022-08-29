<?php
namespace ElementsKit_Lite\Libs\Template;

defined( 'ABSPATH' ) || exit;

require 'transformer.php';

class Loader {

	private $transformer;
	
	function __construct() {
		$this->transformer = new Transformer();
	}

	public function replace_tags( $string, $prefix, $force_lower = false ) {
		return preg_replace_callback(
			'/\\{\\{([^{}]+)\}\\}/',
			function( $matches ) use ( $force_lower, $prefix ) {

				return $this->transformer->render( $matches[1], $prefix );
			},
			$string
		);
	}

	private function tag_list() {
		return array();
	}




	/**
	 * Get the instance.
	 */
	private static $instance = null;
	
	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
