<?php

namespace LottaFramework\Customizer\GenericBuilder;

use LottaFramework\Customizer\Controls\Builder;
use LottaFramework\Customizer\Traits\Renderable;

abstract class Element {

	use Renderable;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string|null
	 */
	protected $device;

	/**
	 * Controls default value
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * @var null|Builder
	 */
	protected $builder = null;

	/**
	 * @param $id
	 * @param $slug
	 * @param $label
	 * @param array $defaults
	 */
	public function __construct( $id, $slug, $label, $defaults = [] ) {
		$this->id       = $id;
		$this->label    = $label;
		$this->slug     = $slug;
		$this->defaults = $defaults;
	}

	/**
	 * @param Builder $builder
	 *
	 * @return $this
	 */
	public function setBuilder( $builder ) {
		$this->builder = $builder;

		return $this;
	}

	/**
	 * @return Builder|null
	 */
	public function getBuilder() {
		return $this->builder;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * Get slug for setting
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function getSlug( $key = '' ) {
		return $this->slug . ( ( $key === '' ) ? '' : '_' . $key );
	}

	/**
	 * Get default setting value
	 *
	 * @param $key
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public function getDefaultSetting( $key, $default = null ) {
		if ( isset( $this->defaults[ $key ] ) ) {
			return $this->defaults[ $key ];
		}

		return $default;
	}

	/**
	 * Enable only when desktop
	 *
	 * @return $this
	 */
	public function desktopOnly() {
		$this->device = 'desktop';

		return $this;
	}

	/**
	 * Enable only when mobile
	 *
	 * @return $this
	 */
	public function mobileOnly() {
		$this->device = 'mobile';

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getDevice() {
		return $this->device;
	}

	/**
	 * After register hook
	 */
	public function after_register() {
		// Should implement in real element
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function enqueue_frontend_scripts() {
		// Should implement in real element
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function enqueue_admin_scripts() {
		// Should implement in real element
	}

	/**
	 * Get controls for element
	 *
	 * @return mixed
	 */
	abstract public function getControls();

	/**
	 * Render element
	 *
	 * @param array $attrs
	 *
	 * @return mixed
	 */
	abstract public function render( $attrs = [] );

	/**
	 * Seletive refresh args
	 *
	 * @return array
	 */
	protected function selectiveRefresh() {
		return [
			".{$this->slug}",
			[ $this, 'build' ],
			[ 'container_inclusive' => true, 'fallback_refresh' => true ]
		];
	}

	/**
	 * Should render this element
	 *
	 * @return bool
	 */
	public function shouldRender() {
		return true;
	}

	/**
	 * Build element
	 */
	public function build() {
		$attrs = [
			'data-builder-element' => $this->id,
		];

		if ( is_customize_preview() && $this->builder ) {
			$location = $this->builder->getPreviewLocation();
			if ( $location !== '' ) {
				$attrs['data-shortcut']          = 'arrow';
				$attrs['data-shortcut-location'] = $location . ':' . $this->id;
			}
		}

		$this->render( $attrs );
	}
}
