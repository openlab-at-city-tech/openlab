<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\ContainerControl;
use LottaFramework\Customizer\GenericBuilder\Element;
use LottaFramework\Customizer\GenericBuilder\Row;
use LottaFramework\Customizer\PageBuilder\Container;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

class Builder extends ContainerControl {

	/**
	 * @var array
	 */
	protected $elements = [];

	/**
	 * @var array
	 */
	protected $rows = [];

	/**
	 * @var Container
	 */
	protected $column;

	/**
	 * Customizer preview location
	 *
	 * @var string
	 */
	protected $location = '';

	/**
	 * Save already performed actions
	 *
	 * @var array
	 */
	protected $performed_actions = [];

	/**
	 * @param $id
	 * @param array $params
	 */
	public function __construct( $id ) {
		parent::__construct( $id );

		$this->setDefaultValue( [] );

		// Enqueue elements admin scripts
		add_action( 'customize_register', function () {
			foreach ( $this->elements as $element ) {
				$element->enqueue_admin_scripts();
			}
		} );

		Utils::app()->add_action( 'after_register_' . $this->id, function () {
			$this->do( 'after_register' );
		} );

		// Enqueue elements frontend scripts
		add_action( 'wp_enqueue_scripts', function () {
			$this->do( 'enqueue_frontend_scripts' );
		} );
	}

	/**
	 * @param $action
	 */
	public function do( $action ) {
		if ( isset( $this->performed_actions[ $action ] ) ) {
			return;
		}

		// Enqueue all elements & rows style under customize preview
		if ( is_customize_preview() ) {
			foreach ( $this->rows as $row ) {
				$row->{$action}();
			}
			foreach ( $this->elements as $element ) {
				$element->{$action}();
			}
		}

		// Only enqueue used elements & rows
		$settings          = CZ::get( $this->id );
		$enqueued_elements = [];

		foreach ( $settings as $ri => $row ) {

			if ( ! is_customize_preview() && $this->shouldRenderRow( $ri ) && isset( $this->rows[ $ri ] ) ) {
				$this->rows[ $ri ]->{$action}();
			}

			if ( ! $this->isResponsiveBuilder() ) {
				$row = [ 'all' => $row ];
			}

			foreach ( $row as $device => $col ) {

				$columns = $col['columns'] ?? [];

				foreach ( $columns as $ci => $column ) {

					if ( ! is_customize_preview() ) {
						foreach ( ( $column['elements'] ?? [] ) as $element ) {
							if ( isset( $this->elements[ $element ] ) && ! in_array( $element, $enqueued_elements ) ) {
								$enqueued_elements[] = $element;
								$this->elements[ $element ]->{$action}();
							}
						}
					}

					$this->column->{$action}( $this->getColId( $ri, $ci, $device ), $column );
				}
			}
		}

		$this->performed_actions[ $action ] = true;
	}

	/**
	 * Should render a row
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function shouldRenderRow( $id ) {
		return $this->rows[ $id ]->shouldRender();
	}

	/**
	 * Get is responsive or not
	 *
	 * @return false|mixed
	 */
	public function isResponsiveBuilder() {
		return $this->options['responsive_builder'] ?? false;
	}

	/**
	 * @param string $row
	 * @param string $col
	 * @param string $device
	 *
	 * @return string
	 */
	protected function getColId( $row, $col, $device ) {
		return $this->id . '_col_' . $row . '_' . $col . '_' . $device;
	}

	/**
	 * Get sub controls path
	 *
	 * @return array
	 */
	public function getSubControlsPath(): array {
		return [
			'elements.[].controls' => true,
			'rows.[].controls'     => true,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return [ $this, 'sanitizeCallback' ];
	}

	/**
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public function sanitizeCallback( $input, $args ) {
		if ( ! is_array( $input ) ) {
			return [];
		}

		$desktopElements = [];
		$mobileElements  = [];

		foreach ( $args['options']['elements'] as $el => $data ) {
			$device = $data['device'] ?? null;

			if ( $device === null || $device === 'desktop' ) {
				$desktopElements[] = $el;
			}

			if ( $device === null || $device === 'mobile' ) {
				$mobileElements[] = $el;
			}
		}

		$rows       = $args['options']['rows'];
		$responsive = isset( $args['options']['responsive_builder'] ) && ! ! $args['options']['responsive_builder'];

		$sanitize_builder_row_data = function ( $data, $maxColumns, $device = 'desktop' ) use ( &$mobileElements, &$desktopElements ) {
			$columns    = $data['columns'] ?? [];
			$newColumns = [];

			foreach ( $columns as $i => $column ) {
				if ( $i >= $maxColumns ) {
					continue;
				}

				$newColumn = [
					'settings' => $this->sanitizeSettings( $this->column, $column['settings'] ?? [] )
				];
				$elements  = $column['elements'] ?? [];

				if ( $device === 'mobile' ) {
					$newColumn['elements'] = array_intersect( $elements, $mobileElements );
					$mobileElements        = array_diff( $mobileElements, $elements );
				}

				if ( $device === 'desktop' ) {
					$newColumn['elements'] = array_intersect( $elements, $desktopElements );
					$desktopElements       = array_diff( $desktopElements, $elements );
				}

				$newColumns[] = $newColumn;
			}

			return [
				'columns' => $newColumns,
			];
		};

		$result = [];

		foreach ( $input as $ri => $data ) {
			if ( ! isset( $rows[ $ri ] ) ) {
				continue;
			}

			$maxColumns = $rows[ $ri ]['maxColumns'] ?? 1;

			if ( $responsive ) {
				$result[ $ri ] = [
					'desktop' => $sanitize_builder_row_data( $data['desktop'] ?? [], $maxColumns, 'desktop' ),
					'mobile'  => $sanitize_builder_row_data( $data['mobile'] ?? [], $maxColumns, 'mobile' ),
				];
			} else {
				$result[ $ri ] = $sanitize_builder_row_data( $data, $maxColumns );
			}
		}

		return $result;
	}

	/**
	 * Enable responsive value
	 *
	 * @return $this
	 */
	public function enableResponsive() {
		return $this->setOption( 'responsive_builder', true );
	}

	/**
	 * @param $column
	 *
	 * @return $this
	 */
	public function setColumn( $column ) {
		$this->column = $column;

		$this->setOption( 'column', [
			'defaults' => $this->column->getDefaults(),
			'controls' => $this->column->getControlsArg(),
		] );

		return $this;
	}

	/**
	 * Add builder elements
	 *
	 * @param Element|null $element
	 *
	 * @return Builder
	 */
	public function addElement( $element ) {
		if ( ! $element instanceof Element ) {
			return $this;
		}

		$element->setBuilder( $this );

		$this->elements[ $element->getId() ] = $element;

		$elements                      = $this->options['elements'] ?? [];
		$elements[ $element->getId() ] = [
			'label'    => $element->getLabel(),
			'device'   => $element->getDevice(),
			'controls' => $this->parseControls( $element->getControls() ),
		];

		return $this->setOption( 'elements', $elements );
	}

	/**
	 * Add a row
	 *
	 * @param Row $row
	 *
	 * @return $this
	 */
	public function addRow( $row ) {
		if ( ! $row instanceof Row ) {
			return $this;
		}

		$row->setBuilder( $this );

		$this->rows[ $row->getId() ] = $row;

		$rows    = $this->options['rows'] ?? [];
		$default = $this->params['default'] ?? [];

		$rows[ $row->getId() ] = [
			'label'      => $row->getLabel(),
			'device'     => $row->getDevice(),
			'type'       => $row->getType(),
			'maxColumns' => $row->getMaxColumns(),
			'controls'   => $this->parseControls( $row->getControls() )
		];

		$default[ $row->getId() ] = $row->getDefault();

		return $this->setDefaultValue( $default )->setOption( 'rows', $rows );
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'lotta-builder';
	}

	/**
	 * @param $row
	 *
	 * @return bool
	 */
	public function hasContent( $row ) {

		$settings = CZ::get( $this->id );
		if ( ! isset( $settings[ $row ] ) ) {
			return false;
		}

		$settings = $settings[ $row ];
		if ( ! $this->isResponsiveBuilder() ) {
			$settings = [ 'all' => $settings ];
		}

		foreach ( $settings as $data ) {
			$columns = $data['columns'] ?? [];
			foreach ( $columns as $column ) {
				$elements = $column['elements'] ?? [];

				// Check renderable elements
				foreach ( $elements as $item ) {
					if ( isset( $this->elements[ $item ] ) && $this->elements[ $item ]->shouldRender() ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * @param $row
	 *
	 * @return array
	 */
	public function getColumns( $row ) {
		$settings = CZ::get( $this->id );
		if ( ! isset( $settings[ $row ] ) ) {
			return [];
		}
		$settings = $settings[ $row ];
		if ( ! $this->isResponsiveBuilder() ) {
			$settings = [ 'all' => $settings ];
		}

		$columns = [];

		foreach ( $settings as $device => $data ) {
			$columns[ $device ] = count( $data['columns'] ?? [] );
		}

		return $columns;
	}

	/**
	 * Set preview location
	 *
	 * @param $location
	 *
	 * @return $this
	 */
	public function setPreviewLocation( $location ) {
		$this->location = $location;

		return $this;
	}

	/**
	 * Get preview location
	 *
	 * @return string
	 */
	public function getPreviewLocation() {
		return $this->location;
	}

	/**
	 * Render a builder row
	 *
	 * @param $rowID
	 * @param null $filter
	 */
	public function render( $rowID, $filter = null ) {
		$settings = CZ::get( $this->id );
		if ( ! isset( $settings[ $rowID ] ) || ! isset( $this->rows[ $rowID ] ) ) {
			return;
		}

		$row      = $this->rows[ $rowID ];
		$settings = $settings[ $rowID ];

		if ( ! $this->isResponsiveBuilder() ) {
			$settings = [ 'all' => $settings ];
		}

		// Row start
		$row->beforeRow();

		foreach ( $settings as $device => $data ) {

			$row->beforeRowDevice( $device, $data );

			$columns = $data['columns'] ?? [];

			foreach ( $columns as $i => $column ) {

				$elements = $column['elements'] ?? [];

				// empty column
				if ( empty( $elements ) ) {
					continue;
				}

				$css = [];
				if ( $filter !== null ) {
					$css = call_user_func( $filter, $css, [
						'device'   => $device,
						'column'   => $i,
						'columns'  => $columns,
						'elements' => $elements
					] );
				}

				// Column start
				$this->column->start(
					$this->getColId( $rowID, $i, $device ),
					array_merge( $column, [
						'css'    => $css,
						'device' => $device,
						'index'  => $i,
					] ),
					$this->location . ":{$rowID}-{$device}-{$i}"
				);

				// Elements
				foreach ( $elements as $item ) {
					if ( isset( $this->elements[ $item ] ) && $this->elements[ $item ]->shouldRender() ) {
						$this->elements[ $item ]->build();
					}
				}

				// Column end
				$this->column->end( $this->getColId( $rowID, $i, $device ), $column );
			}

			$row->afterRowDevice( $device, $data );
		}

		// End row
		$row->afterRow();
	}
}
