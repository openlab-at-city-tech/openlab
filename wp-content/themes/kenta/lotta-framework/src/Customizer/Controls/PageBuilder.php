<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\ContainerControl;
use LottaFramework\Customizer\PageBuilder\Container;
use LottaFramework\Customizer\PageBuilder\Element;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

class PageBuilder extends ContainerControl {

	/**
	 * @var array
	 */
	protected $elements = [];

	/**
	 * @var Container
	 */
	protected $row;

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
	 * @param $id
	 * @param $row
	 * @param $column
	 * @param array $params
	 */
	public function __construct( $id, $row, $column ) {
		$this->row    = $row;
		$this->column = $column;

		$this->setOption( 'row', [
			'defaults' => $this->row->getDefaults(),
			'controls' => $this->row->getControlsArg(),
		] );
		$this->setOption( 'column', [
			'defaults' => $this->column->getDefaults(),
			'controls' => $this->column->getControlsArg(),
		] );

		parent::__construct( $id );

		$this->setDefaultValue( [] );

		Utils::app()->add_action( 'after_register_' . $this->id, function () {
			$this->do( 'after_register' );
		} );
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'lotta-page-builder';
	}

	/**
	 * @return string[]
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

		$result = [];

		foreach ( $input as $row ) {
			$columns = [];

			foreach ( ( $row['columns'] ?? [] ) as $column ) {
				$elements = [];

				foreach ( ( $column['elements'] ?? [] ) as $element ) {
					$id = $element['id'] ?? '';
					if ( ! isset( $this->elements[ $id ] ) ) {
						continue;
					}

					$elements[] = [
						'id'       => $id,
						'settings' => $this->sanitizeSettings( $this->elements[ $id ], $element['settings'] )
					];
				}

				$columns[] = [
					'settings' => $this->sanitizeSettings( $this->column, $column['settings'] ?? [] ),
					'elements' => $elements,
				];
			}

			$result[] = [
				'settings' => $this->sanitizeSettings( $this->row, $row['settings'] ?? [] ),
				'columns'  => $columns,
			];
		}

		return $result;
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
	 * Add builder elements
	 *
	 * @param Element|null $element
	 *
	 * @return PageBuilder
	 */
	public function addElement( $element ) {
		if ( ! $element instanceof Element ) {
			return $this;
		}

		$this->elements[ $element->getId() ] = $element;

		$elements                      = $this->options['elements'] ?? [];
		$elements[ $element->getId() ] = [
			'label'    => $element->getLabel(),
			'icon'     => $element->getIcon(),
			'device'   => $element->getDevice(),
			'defaults' => $element->getDefaults(),
			'controls' => $element->getControlsArg(),
		];

		return $this->setOption( 'elements', $elements );
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function enqueue_frontend_scripts() {
		$this->do( 'enqueue_frontend_scripts' );
	}

	/**
	 * @param $action
	 */
	protected function do( $action ) {

		$settings = CZ::get( $this->id );
		if ( ! is_array( $settings ) ) {
			return;
		}

		foreach ( $settings as $ri => $row ) {
			$this->row->{$action}( $this->getRowId( $ri ), $row );

			$columns = $row['columns'] ?? [];
			foreach ( $columns as $ci => $column ) {
				$elements = $column['elements'] ?? [];
				foreach ( $elements as $ei => $data ) {
					$id = $data['id'] ?? '';
					if ( isset( $this->elements[ $id ] ) ) {
						$this->elements[ $id ]->{$action}( $this->getElId( $ri, $ci, $ei ), $data );
					}
				}

				$this->column->{$action}( $this->getColId( $ri, $ci ), $column );
			}
		}
	}

	/**
	 * Render a builder row
	 */
	public function render() {
		$settings = CZ::get( $this->id );

		if ( ! is_array( $settings ) ) {
			return;
		}

		foreach ( $settings as $ri => $row ) {
			$columns = $row['columns'] ?? [];

			$this->row->start( $this->getRowId( $ri ), $row, $this->location . ":row-{$ri}" );

			foreach ( $columns as $ci => $column ) {
				$elements = $column['elements'] ?? [];

				$this->column->start( $this->getColId( $ri, $ci ), $column, $this->location . ":col-{$ri}-{$ci}" );

				foreach ( $elements as $ei => $data ) {
					$id = $data['id'] ?? '';
					if ( isset( $this->elements[ $id ] ) ) {
						$this->elements[ $id ]->render( [
							'id'       => $this->getElId( $ri, $ci, $ei ),
							'location' => $this->location . ":element-{$ri}-{$ci}-{$ei}",
							'settings' => $data['settings'] ?? [],
						] );
					}
				}

				$this->column->end( $this->getColId( $ri, $ci ), $column );
			}

			$this->row->end( $this->getRowId( $ri ), $row );
		}
	}

	/**
	 * @param $row
	 *
	 * @return string
	 */
	protected function getRowId( $row ) {
		return $this->id . '_row_' . $row;
	}

	/**
	 * @param $row
	 * @param $col
	 *
	 * @return string
	 */
	protected function getColId( $row, $col ) {
		return $this->id . '_col_' . $row . '_' . $col;
	}

	/**
	 * @param $row
	 * @param $col
	 * @param $el
	 *
	 * @return string
	 */
	protected function getElId( $row, $col, $el ) {
		return $this->id . '_el_' . $row . '_' . $col . '_' . $el;
	}
}