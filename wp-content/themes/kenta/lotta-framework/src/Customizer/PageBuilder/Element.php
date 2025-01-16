<?php

namespace LottaFramework\Customizer\PageBuilder;

use LottaFramework\Customizer\GenericBuilder\Element as GenericElement;
use LottaFramework\Customizer\Traits\ContainerControl;

abstract class Element extends GenericElement {

	use ContainerControl;

	/**
	 * Controls
	 *
	 * @var array
	 */
	protected $controls = [];

	/**
	 * @var null
	 */
	protected $icon = null;

	/**
	 * Override construct function
	 *
	 * @param $id
	 * @param $label
	 * @param array $defaults
	 */
	public function __construct( $id, $label, $defaults = [] ) {
		parent::__construct( $id, $id, $label, $defaults );

		$this->controls = $this->parseControls( $this->getControls(), true );
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function enqueue_frontend_scripts( $id = null, $data = [] ) {
		// Should implement in real element
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function enqueue_admin_scripts( $id = null, $data = [] ) {
		// Should implement in real element
	}

	/**
	 * @return array
	 */
	public function getControlsArg() {
		return $this->controls;
	}

	/**
	 * @param $icon
	 *
	 * @return $this
	 */
	public function setIcon( $icon ) {
		$this->icon = $icon;

		return $this;
	}

	public function getIcon() {
		return $this->icon;
	}

	/**
	 * Get slug for setting
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function getSlug( $key = '' ) {
		return $key === '' ? $this->slug : $key;
	}
}