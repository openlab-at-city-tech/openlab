<?php
/**
 * Soliloquy Blocks Class.
 *
 * @since 2.7.7
 * @package SoliloquyWP
 * @author SoliloquyWP Team <support@soliloquywp.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Soliloquy Blocks Class.
 *
 * @since 2.7.7
 */
class Soliloquy_Blocks {

	/**
	 * Holds Class Instance.
	 *
	 * @var Soliloquy_Blocks
	 */
	public static $instance = null;

	/**
	 * Holds Main instance.
	 *
	 * @var Soliloquy
	 */
	public $base = null;

	/**
	 * Class Constructor.
	 *
	 * @since 2.7.7
	 */
	public function __construct() {
		$this->base = Soliloquy_Lite::get_instance();
		add_action( 'init', [ $this, 'soliloquy_slider_dynamic_block_init' ] );
	}

	/**
	 * Init.
	 *
	 * @since 2.7.7
	 *
	 * @return void
	 */
	public function soliloquy_slider_dynamic_block_init() {
		register_block_type( SOLILOQUY_DIR . '/blocks/soliloquy' );
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 2.7.7
	 *
	 * @return object The Soliloquy_Common_Admin object.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Blocks ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

$soliloquy_blocks = Soliloquy_Blocks::get_instance();
