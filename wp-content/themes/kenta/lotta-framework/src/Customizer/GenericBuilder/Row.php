<?php

namespace LottaFramework\Customizer\GenericBuilder;

use LottaFramework\Customizer\Controls\Builder;

class Row {

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * Row control's default value
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * @var string|null
	 */
	protected $device;

	/**
	 * @var string
	 */
	protected $type = 'row';

	/**
	 * @var array
	 */
	protected $default = [];

	/**
	 * @var array
	 */
	protected $controls = [];

	/**
	 * @var int
	 */
	protected $maxColumns = 2;

	/**
	 * @var null|Builder
	 */
	protected $builder = null;

	/**
	 * @param $id
	 * @param $label
	 * @param array $defaults
	 */
	public function __construct( $id, $label, $defaults = [] ) {
		$this->id       = $id;
		$this->label    = $label;
		$this->defaults = $defaults;

		$this->setControls( $this->getRowControls() );
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
	 * @param null $device
	 *
	 * @return array|int|mixed
	 */
	public function getColumns( $device = null ) {
		if ( ! $this->builder ) {
			return $device ? 1 : [];
		}

		$columns = $this->builder->getColumns( $this->id );

		return $device ? ( $columns[ $device ] ?? 1 ) : $columns;
	}

	/**
	 * @param $key
	 * @param null $fallback
	 *
	 * @return mixed|null
	 */
	protected function getRowControlDefault( $key, $fallback = null ) {
		if ( isset( $this->defaults[ $key ] ) ) {
			return $this->defaults[ $key ];
		}

		return $fallback;
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
		// Should implement in real row
	}

	/**
	 * @return array
	 */
	protected function getRowControls() {
		return [];
	}

	/**
	 * Before row hook
	 */
	public function beforeRow() {
		// Should implement in real row
	}

	/**
	 * Before row hook for different device & settings
	 *
	 * @param $device
	 * @param $settings
	 */
	public function beforeRowDevice( $device, $settings ) {
		// Should implement in real row
	}

	/**
	 * After row hook
	 */
	public function afterRow() {
		// Should implement in real row
	}

	/**
	 * After row hook for different device & settings
	 *
	 * @param $device
	 * @param $settings
	 */
	public function afterRowDevice( $device, $settings ) {
		// Should implement in real row
	}

	/**
	 * @param $elements
	 * @param array $settings
	 * @param null $device
	 *
	 * @return $this
	 */
	public function addColumn( $elements, $settings = [], $device = null ) {

		if ( $device === 'all' ) {
			$this->addDesktopColumn( $elements, $settings );
			$this->addMobileColumn( $elements, $settings );

			return $this;
		}

		if ( ! is_array( $this->default ) ) {
			$this->default = [];
		}

		if ( ! $device ) {
			$columns   = $this->default['columns'] ?? [];
			$columns[] = [
				'elements' => $elements,
				'settings' => $settings,
			];

			$this->default = [ 'columns' => $columns ];

			return $this;
		}

		if ( ! isset( $this->default['desktop'] ) ) {
			$this->default['desktop'] = [];
		}
		if ( ! isset( $this->default['mobile'] ) ) {
			$this->default['mobile'] = [];
		}

		$value = $this->default[ $device ] ?? [];

		$columns = $value['columns'] ?? [];
		if ( count( $columns ) >= $this->maxColumns ) {
			return $this;
		}

		$columns[] = [
			'elements' => $elements,
			'settings' => $settings,
		];

		$this->default[ $device ] = [ 'columns' => $columns ];

		return $this;
	}

	/**
	 * @param $elements
	 * @param array $settings
	 *
	 * @return $this
	 */
	public function addMobileColumn( $elements, $settings = [] ) {
		return $this->addColumn( $elements, $settings, 'mobile' );
	}

	/**
	 * @param $elements
	 * @param array $settings
	 *
	 * @return $this
	 */
	public function addDesktopColumn( $elements, $settings = [] ) {
		return $this->addColumn( $elements, $settings, 'desktop' );
	}

	/**
	 * Set max columns a row owen
	 *
	 * @param $columns
	 *
	 * @return $this
	 */
	public function setMaxColumns( $columns ) {
		$this->maxColumns = $columns;

		return $this;
	}

	/**
	 * Change row to off-canvas
	 *
	 * @return $this
	 */
	public function isOffCanvas() {
		$this->type = 'off-canvas';

		return $this;
	}

	/**
	 * Set controls for row
	 *
	 * @param $controls
	 *
	 * @return $this
	 */
	public function setControls( $controls ) {
		$this->controls = $controls;

		return $this;
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

	public function getId() {
		return $this->id;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getDevice() {
		return $this->device;
	}

	public function getType() {
		return $this->type;
	}

	public function getMaxColumns() {
		return $this->maxColumns;
	}

	public function getControls() {
		return $this->controls;
	}

	public function getDefault() {
		return $this->default;
	}

	/**
	 * {@inheritDoc}
	 */
	public function shouldRender() {
		return $this->builder->hasContent( $this->id );
	}
}