<?php

namespace LottaFramework\Customizer;

use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\CZ;

/**
 * Abstract customizer control
 */
abstract class Control {

	/**
	 * Control/Setting id
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * @var array
	 */
	protected $params = [];

	/**
	 * @var array
	 */
	protected $options = [];

	/**
	 * @param string $id
	 */
	public function __construct( string $id ) {
		$this->id = $id;
	}

	/**
	 * Get control type
	 *
	 * @return string
	 */
	abstract public function getType(): string;

	/**
	 * Get control sanitize callback
	 *
	 * @return mixed
	 */
	abstract public function getSanitize();

	/**
	 * Get sub controls path
	 *
	 * @return array
	 */
	public function getSubControlsPath(): array {
		return [];
	}

	/**
	 * Add or change control param
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function setParam( $key, $value ) {
		$this->params[ $key ] = $value;

		return $this;
	}

	/**
	 * Add or change an option
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function setOption( $key, $value ) {
		$this->options[ $key ] = $value;

		return $this;
	}

	/**
	 * Alias for set label param
	 *
	 * @param $label
	 *
	 * @return $this
	 */
	public function setLabel( $label ) {
		return $this->setParam( 'label', apply_filters( $this->id . '_label', $label ) );
	}

	/**
	 * Alias for set description param
	 *
	 * @param $desc
	 *
	 * @return $this
	 */
	public function setDescription( $desc ) {
		return $this->setParam( 'description', apply_filters( $this->id . '_desc', $desc ) );
	}

	/**
	 * Alias for set section id
	 *
	 * @param $id
	 *
	 * @return $this
	 */
	public function setSection( $id ) {
		return $this->setParam( 'section', apply_filters( $this->id . '_section_id', $id ) );
	}

	/**
	 * Alias for set default value param
	 *
	 * @param $default
	 *
	 * @return $this
	 */
	public function setDefaultValue( $default ) {
		return $this->setParam( 'default', apply_filters( $this->id . '_default_value', $default ) );
	}

	/**
	 * Set transport to postMessage
	 *
	 * @return $this
	 */
	public function postMessage() {
		return $this->setParam( 'transport', 'postMessage' );
	}

	/**
	 * Enable responsive value
	 *
	 * @return $this
	 */
	public function enableResponsive() {
		return $this->setOption( 'responsive', true );
	}

	/**
	 * Get is responsive or not
	 *
	 * @return false|mixed
	 */
	public function isResponsive() {
		return $this->options['responsive'] ?? false;
	}

	/**
	 * Show control as inline
	 *
	 * @return $this
	 */
	public function displayNone() {
		return $this->setOption( 'design', 'none' );
	}

	/**
	 * Show control as inline
	 *
	 * @return $this
	 */
	public function displayInline() {
		return $this->setOption( 'design', 'inline' );
	}

	/**
	 * Show control as block
	 *
	 * @return $this
	 */
	public function displayBlock() {
		return $this->setOption( 'design', 'block' );
	}

	/**
	 * Show control as raw control
	 *
	 * @return $this
	 *
	 * @since v2.0.15
	 */
	public function displayRaw() {
		return $this->setOption( 'design', 'raw' );
	}

	/**
	 * Show control label
	 *
	 * @return $this
	 */
	public function showLabel() {
		return $this->setOption( 'hideLabel', false );
	}

	/**
	 * Show control label
	 *
	 * @return $this
	 */
	public function hideLabel() {
		return $this->setOption( 'hideLabel', true );
	}

	/**
	 * Generate async script
	 *
	 * @param $script
	 *
	 * @return $this
	 */
	public function async( $script ) {

		$this->postMessage();

		CZ::addAsync( $this->id, $script );

		return $this;
	}

	/**
	 * @param $selector
	 * @param $css
	 *
	 * @return $this
	 */
	public function asyncCss( $selector, $css ) {

		if ( is_array( $selector ) ) {
			$selector = implode( ',', $selector );
		}

		$css = AsyncCss::encode( $css );

		return $this->async( AsyncCss::dynamic( $this->id, "{'$selector': $css}" ) );
	}

	/**
	 * @param $selector
	 *
	 * @return $this
	 */
	public function asyncText( $selector ) {

		if ( is_array( $selector ) ) {
			$selector = implode( ',', $selector );
		}

		return $this->async( "document.querySelector('{$selector}').innerText = value" );
	}

	/**
	 * @param $selector
	 *
	 * @return $this
	 */
	public function asyncHtml( $selector ) {

		if ( is_array( $selector ) ) {
			$selector = implode( ',', $selector );
		}

		return $this->async( "document.querySelector('{$selector}').innerHTML = value" );
	}

	/**
	 * Bind selective refresh
	 *
	 * @param $id
	 *
	 * @return $this
	 */
	public function bindSelectiveRefresh( $id ) {
		if ( ! $id ) {
			return $this;
		}

		$this->postMessage();

		CZ::bindSelectiveRefresh( $id, $this->id );

		return $this;
	}

	/**
	 * Enable selective refresh
	 *
	 * @param string $selector
	 * @param $callback
	 * @param $args
	 *
	 * @return $this
	 */
	public function selectiveRefresh( $selector = null, $callback = null, $args = [] ) {
		if ( ! $selector || ! $callback ) {
			return $this;
		}

		$this->postMessage();

		return $this->setParam( 'selective_refresh', array_merge( $args, [
			'selector'        => $selector,
			'render_callback' => $callback,
		] ) );
	}

	/**
	 * Convert control to array args
	 *
	 * @return array
	 */
	public function toArray(): array {
		$sanitize_callback = $this->getSanitize();

		$args = array_merge( $this->params, [
			'id'       => $this->id,
			'type'     => $this->getType(),
			'options'  => $this->options,
			'controls' => $this->getSubControlsPath(),
		] );

		$args['sanitize_callback'] = is_array( $sanitize_callback ) ? function ( $input ) use ( $args, $sanitize_callback ) {
			if ( $this->isResponsive() ) {
				return Sanitizes::responsive_sanitize( $sanitize_callback, $input, $args );
			}

			return call_user_func( $sanitize_callback, $input, $args );
		} : $sanitize_callback;

		return $args;
	}
}
