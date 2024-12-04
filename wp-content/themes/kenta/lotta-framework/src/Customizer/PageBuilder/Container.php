<?php

namespace LottaFramework\Customizer\PageBuilder;

use LottaFramework\Customizer\Traits\ContainerControl;
use LottaFramework\Customizer\Traits\Renderable;

abstract class Container {

	use Renderable;
	use ContainerControl;

	/**
	 * @var null
	 */
	protected static $_instances = [];

	/**
	 * Controls
	 *
	 * @var array
	 */
	protected $controls = [];

	/**
	 * keep constructor is protected
	 */
	protected function __construct() {
		$this->controls = $this->parseControls( $this->getControls(), true );
	}

	/**
	 * Get instance
	 */
	public static function instance() {
		if ( ! isset( self::$_instances[ static::class ] ) ) {
			self::$_instances[ static::class ] = new static();
		}

		return self::$_instances[ static::class ];
	}

	/**
	 * After register hook
	 */
	public function after_register( $id, $data ) {
		// Should implement in real element
	}

	/**
	 * @return mixed
	 */
	abstract public function enqueue_frontend_scripts( $id, $data );

	/**
	 * @param $id
	 * @param $data
	 * @param string $location
	 *
	 * @return mixed
	 */
	abstract public function start( $id, $data, $location = '' );

	/**
	 * @param $id
	 * @param $data
	 *
	 * @return mixed
	 */
	abstract public function end( $id, $data );

	/**
	 * @return array
	 */
	abstract public function getControls();

	/**
	 * @return array
	 */
	public function getControlsArg() {
		return $this->controls;
	}
}