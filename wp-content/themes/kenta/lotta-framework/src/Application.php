<?php

namespace LottaFramework;

use LottaFramework\Container\Container;

class Application extends Container {

	const VERSION = '2.1.1';

	/**
	 * Application id
	 *
	 * @var mixed|string
	 */
	protected $_id;

	/**
	 * @var string
	 */
	protected $_uri = '';

	/**
	 * @param string $id
	 * @param string $uri
	 */
	public function __construct( string $id, string $uri ) {
		$this->_id  = $id;
		$this->_uri = $uri;

		static::setInstance( $this );
	}

	/**
	 * @return mixed|string
	 */
	public function id() {
		return $this->_id;
	}

	/**
	 * @return mixed|string
	 */
	public function uri() {
		return $this->_uri;
	}

	/**
	 * 'do_action' wrapper that prefixes the hook name with id
	 *
	 * @param $hook_name
	 * @param ...$args
	 */
	public function do_action( $hook_name, ...$args ) {
		do_action( $this->uniqid( $hook_name ), ...$args );
	}

	/**
	 * Get prefixed id
	 *
	 * @param string $id
	 * @param string $sep
	 *
	 * @return string
	 */
	public function uniqid( string $id, string $sep = '_' ) {
		if ( empty( $this->_id ) ) {
			return $id;
		}

		return $this->_id . $sep . $id;
	}

	/**
	 * 'add_action' wrapper that prefixes the hook name with id
	 *
	 * @param $hook_name
	 * @param $callback
	 * @param int $priority
	 * @param int $accepted_args
	 */
	public function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		add_action( $this->uniqid( $hook_name ), $callback, $priority, $accepted_args );
	}

	/**
	 * 'apply_filters' wrapper that prefixes the hook name with id
	 *
	 * @param $hook_name
	 * @param $value
	 */
	public function apply_filters( $hook_name, $value ) {
		apply_filters( $this->uniqid( $hook_name ), $value );
	}

	/**
	 * 'add_filter' wrapper that prefixes the hook name with id
	 *
	 * @param $hook_name
	 * @param $callback
	 * @param mixed ...$args
	 */
	public function add_filter( $hook_name, $callback, ...$args ) {
		add_filter( $this->uniqid( $hook_name ), $callback, ...$args );
	}

	/**
	 * Add supported features
	 *
	 * @param $feature
	 *
	 * @return mixed
	 */
	public function support( $feature ) {
		return $this->instance( "features.{$feature}", true );
	}

	/**
	 * Check if a featured enabled or not
	 *
	 * @param $feature
	 *
	 * @return bool
	 */
	public function isSupport( $feature ) {
		return $this->has( "features.{$feature}" );
	}
}
