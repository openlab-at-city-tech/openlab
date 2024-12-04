<?php
/**
 * Header builder instance
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Header_Builder' ) ) {

	class Kenta_Header_Builder {

		/**
		 * @var null
		 */
		protected static $_instance = null;

		/**
		 * @var Builder|null
		 */
		protected $_builder = null;

		/**
		 * Construct builder
		 */
		protected function __construct() {
			$this->_builder = ( new Builder( 'kenta_header_builder' ) )
				->setLabel( __( 'Header Elements', 'kenta' ) )
				->showLabel()
				->bindSelectiveRefresh( 'kenta-header-selective-css' )
				->selectiveRefresh( '.kenta-site-header', 'kenta_header_render' )
				->enableResponsive()
				->setColumn( Kenta_Header_Column::instance() );

			$this->_builder
				->addElement( new Kenta_Logo_Element( 'logo', 'kenta_header_el_logo', __( 'Logo', 'kenta' ), [
					'transparent-logo' => true,
				] ) )
				->addElement( new Kenta_Search_Element( 'search', 'kenta_header_el_search', __( 'Search', 'kenta' ) ) )
				// Buttons
				->addElement( new Kenta_Button_Element( 'button-1', 'kenta_header_el_button_1', __( 'Button #1', 'kenta' ) ) )
				->addElement( new Kenta_Button_Element( 'button-2', 'kenta_header_el_button_2', __( 'Button #2', 'kenta' ) ) )
				// Menus
				->addElement( new Kenta_Menu_Element( 'menu-1', 'kenta_header_el_menu_1', __( 'Menu #1', 'kenta' ), [
					'selective-refresh' => 'kenta-header-selective-css',
				] ) )
				->addElement( new Kenta_Menu_Element( 'menu-2', 'kenta_header_el_menu_2', __( 'Menu #2', 'kenta' ), [
					'selective-refresh' => 'kenta-header-selective-css',
				] ) )
				->addElement( new Kenta_Collapsable_Menu_Element(
					'collapsable-menu',
					'kenta_header_el_collapsable_menu',
					__( 'Collapsable Menu', 'kenta' )
				) )
				// Trigger
				->addElement( new Kenta_Trigger_Element( 'trigger', 'kenta_header_el_trigger', __( 'Trigger', 'kenta' ) ) )
				// Breadcrumbs
				->addElement( new Kenta_Breadcrumbs_Element( 'breadcrumbs', 'kenta_header_el_breadcrumbs', __( 'Breadcrumbs', 'kenta' ) ) )
				->addElement( new Kenta_Socials_Element( 'socials', 'kenta_header_el_socials', __( 'Socials', 'kenta' ) ) )
				->addElement( new Kenta_Theme_Switch_Element( 'theme-switch', 'kenta_header_el_theme_switch', __( 'Theme Switch', 'kenta' ) ) );

			// WooCommerce Elements
			if ( KENTA_WOOCOMMERCE_ACTIVE ) {
				$this->_builder->addElement( new Kenta_Cart_Element( 'cart', 'kenta_header_el_cart', __( 'Cart', 'kenta' ) ) );
			}

			// add rows
			$this->_builder
				->addRow( $this->getModalRow() )
				->addRow( $this->getTopBarRow() )
				->addRow( $this->getPrimaryRow() )
				->addRow( $this->getBottomRow() );

			do_action( 'kenta_header_builder_initialized', $this->_builder );
		}

		protected function getModalRow() {
			$data = apply_filters( 'kenta_modal_row_default_value', [
				'desktop' => [
					[
						'elements' => [ 'collapsable-menu' ],
						'settings' => [ 'direction' => 'column', ],
					]
				],
				'mobile'  => [
					[
						'elements' => [ 'collapsable-menu' ],
						'settings' => [
							'direction'    => 'column',
							'elements-gap' => '24px',
						],
					]
				],
			] );

			$row = ( new Kenta_Modal_Row( 'modal', __( 'Modal Area', 'kenta' ) ) )->isOffCanvas();

			foreach ( $data['desktop'] as $column ) {
				$row->addDesktopColumn( $column['elements'], $column['settings'] );
			}

			foreach ( $data['mobile'] as $column ) {
				$row->addMobileColumn( $column['elements'], $column['settings'] );
			}

			return $row;
		}

		protected function getTopBarRow() {

			$data = apply_filters( 'kenta_header_top_row_default_value', [
				'desktop' => [
					[
						'elements' => [],
						'settings' => [ 'width' => '50%' ]
					],
					[
						'elements' => [],
						'settings' => [ 'width' => '50%' ]
					],
				],
				'mobile'  => [
					[
						'elements' => [],
						'settings' => [ 'width' => '50%' ]
					],
					[
						'elements' => [],
						'settings' => [ 'width' => '50%' ]
					],
				],
			] );

			$row = ( new Kenta_Header_Row( 'top_bar', __( 'Top Bar', 'kenta' ), [
				'min_height' => '40px',
				'z_index'    => 100,
				'background' => [
					'type'  => 'color',
					'color' => 'var(--kenta-base-color)'
				],
			] ) );

			$row->setMaxColumns( apply_filters( 'kenta_header_top_row_max_columns', 3 ) );

			foreach ( $data['desktop'] as $column ) {
				$row->addDesktopColumn( $column['elements'], $column['settings'] );
			}

			foreach ( $data['mobile'] as $column ) {
				$row->addMobileColumn( $column['elements'], $column['settings'] );
			}

			return $row;
		}

		protected function getPrimaryRow() {
			$data = apply_filters( 'kenta_header_primary_row_default_value', [
				'desktop' => [
					[
						'elements' => [ 'logo' ],
						'settings' => [ 'width' => '30%' ]
					],
					[
						'elements' => [ 'menu-1', 'socials', 'theme-switch', 'search' ],
						'settings' => [ 'width' => '70%', 'justify-content' => 'flex-end' ]
					],
				],
				'mobile'  => [
					[
						'elements' => [ 'logo' ],
						'settings' => [ 'width' => '70%', ]
					],
					[
						'elements' => [ 'socials', 'theme-switch', 'search', 'trigger' ],
						'settings' => [ 'width' => '30%', 'justify-content' => 'flex-end' ]
					],
				],
			] );

			$row = ( new Kenta_Header_Row( 'primary_navbar', __( 'Primary Navbar', 'kenta' ), [
				'min_height' => '80px',
				'z_index'    => 99,
				'background' => [
					'type'  => 'color',
					'color' => 'var(--kenta-base-color)'
				],
			] ) );

			$row->setMaxColumns( apply_filters( 'kenta_header_primary_row_max_columns', 3 ) );

			foreach ( $data['desktop'] as $column ) {
				$row->addDesktopColumn( $column['elements'], $column['settings'] );
			}

			foreach ( $data['mobile'] as $column ) {
				$row->addMobileColumn( $column['elements'], $column['settings'] );
			}

			return $row;
		}

		protected function getBottomRow() {

			$data = apply_filters( 'kenta_header_bottom_row_default_value', [
				'desktop' => [
					[
						'elements' => [],
						'settings' => [ 'width' => '50%' ]
					],
					[
						'elements' => [],
						'settings' => [ 'width' => '50%' ]
					],
				],
				'mobile'  => [
					[
						'elements' => [],
						'settings' => [ 'width' => '50%', ]
					],
					[
						'elements' => [],
						'settings' => [ 'width' => '50%' ]
					],
				],
			] );

			$row = ( new Kenta_Header_Row( 'bottom_row', __( 'Bottom Row', 'kenta' ), [
				'z_index' => 98,
			] ) );

			$row->setMaxColumns( apply_filters( 'kenta_header_bottom_row_max_columns', 3 ) );

			foreach ( $data['desktop'] as $column ) {
				$row->addDesktopColumn( $column['elements'], $column['settings'] );
			}

			foreach ( $data['mobile'] as $column ) {
				$row->addMobileColumn( $column['elements'], $column['settings'] );
			}

			return $row;
		}

		/**
		 * Get header builder
		 *
		 * @return Kenta_Header_Builder|null
		 */
		public static function instance() {
			if ( self::$_instance === null ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Magic static calls
		 *
		 * @param $method
		 * @param $args
		 *
		 * @return mixed
		 */
		public static function __callStatic( $method, $args ) {
			$builder = self::instance()->builder();

			if ( method_exists( $builder, $method ) ) {
				return $builder->$method( ...$args );
			}

			return null;
		}

		/**
		 * @return Builder|null
		 */
		public function builder() {
			return $this->_builder;
		}
	}
}

