<?php
/**
 * Header builder row
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\Border;
use LottaFramework\Customizer\Controls\BoxShadow;
use LottaFramework\Customizer\Controls\Info;
use LottaFramework\Customizer\Controls\MultiSelect;
use LottaFramework\Customizer\Controls\Number;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\GenericBuilder\Row;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Header_Row' ) ) {

	class Kenta_Header_Row extends Row {

		use Kenta_Global_Color_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$visibility = CZ::get( $this->getRowControlKey( 'visibility' ) );

				$css[".kenta-header-row-{$this->id}"] = array_merge(
					[
						'z-index' => CZ::get( $this->getRowControlKey( 'z_index' ) ),
						'display' => [
							'desktop' => ( isset( $visibility['desktop'] ) && $visibility['desktop'] === 'yes' ) ? 'block' : 'none',
							'tablet'  => ( isset( $visibility['tablet'] ) && $visibility['tablet'] === 'yes' ) ? 'block' : 'none',
							'mobile'  => ( isset( $visibility['mobile'] ) && $visibility['mobile'] === 'yes' ) ? 'block' : 'none',
						],
					],
					Css::background( CZ::get( $this->getRowControlKey( 'background' ) ) ),
					Css::shadow( CZ::get( $this->getRowControlKey( 'shadow' ) ) ),
					Css::border( CZ::get( $this->getRowControlKey( 'border_top' ) ), 'border-top' ),
					Css::border( CZ::get( $this->getRowControlKey( 'border_bottom' ) ), 'border-bottom' ),
					Css::background( CZ::get( $this->getRowControlKey( 'background' ) ) ),
					Css::border( CZ::get( $this->getRowControlKey( 'border_top' ) ), 'border-top' ),
					Css::colors( CZ::get( $this->getRowControlKey( 'primary_color' ) ), [
						'default' => '--kenta-primary-color',
						'active'  => '--kenta-primary-active',
					] ),
					Css::colors( CZ::get( $this->getRowControlKey( 'accent_color' ) ), [
						'default' => '--kenta-accent-color',
						'active'  => '--kenta-accent-active',
					] ),
					Css::colors( CZ::get( $this->getRowControlKey( 'base_color' ) ), [
						'default' => '--kenta-base-color',
						'100'     => '--kenta-base-100',
						'200'     => '--kenta-base-200',
						'300'     => '--kenta-base-300',
					] )
				);

				$css[".kenta-header-row-{$this->id} .container"] = [
					'min-height' => CZ::get( $this->getRowControlKey( 'min_height' ) )
				];

				return apply_filters( 'kenta_header_row_css', $css, $this->id );
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function shouldRender() {
			return CZ::checked( $this->getRowControlKey( 'render_empty' ) ) || $this->builder->hasContent( $this->id );
		}

		/**
		 * {@inheritDoc}
		 */
		public function beforeRow() {
			do_action( 'kenta_start_header_row', $this->id );
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterRow() {
			do_action( 'kenta_after_header_row', $this->id );
		}

		/**
		 * @param $key
		 *
		 * @return string
		 */
		protected function getRowControlKey( $key ) {
			return 'kenta_header_' . $this->id . '_row_' . $key;
		}

		/**
		 * {@inheritDoc}
		 *
		 * @return array
		 */
		protected function getRowControls() {
			$general_controls = [
				( new Slider( $this->getRowControlKey( 'min_height' ) ) )
					->setLabel( __( 'Min Height', 'kenta' ) )
					->setDefaultValue( $this->getRowControlDefault( 'min_height', '80px' ) )
					->asyncCss( ".kenta-header-row-{$this->id} .container", [ 'min-height' => 'value' ] )
					->setDefaultUnit( 'px' )
					->enableResponsive()
					->setMin( 20 )
					->setMax( 800 )
				,
				( new Number( $this->getRowControlKey( 'z_index' ) ) )
					->setLabel( __( 'Z Index', 'kenta' ) )
					->setMin( - 99999 )
					->setMax( 99999 )
					->setDefaultUnit( false )
					->setDefaultValue( $this->getRowControlDefault( 'z_index', 9 ) )
				,
				( new Toggle( $this->getRowControlKey( 'render_empty' ) ) )
					->setLabel( __( 'Render Empty Row', 'kenta' ) )
					->closeByDefault()
					->selectiveRefresh( '.kenta-site-header', 'kenta_header_render' )
				,
				( new MultiSelect( $this->getRowControlKey( 'visibility' ) ) )
					->setLabel( __( 'Visibility', 'kenta' ) )
					->buttonsGroupView()
					->setChoices( [
						'desktop' => kenta_image( 'desktop' ),
						'tablet'  => kenta_image( 'tablet' ),
						'mobile'  => kenta_image( 'mobile' )
					] )
					->asyncCss( ".kenta-header-row-{$this->id}", [
						'display' => [
							'desktop' => AsyncCss::unescape( AsyncCss::valueMapper( [
								'yes' => 'block',
								'no'  => 'none'
							], "value['desktop']" ) ),
							'tablet'  => AsyncCss::unescape( AsyncCss::valueMapper( [
								'yes' => 'block',
								'no'  => 'none'
							], "value['tablet']" ) ),
							'mobile'  => AsyncCss::unescape( AsyncCss::valueMapper( [
								'yes' => 'block',
								'no'  => 'none'
							], "value['mobile']" ) ),
						]
					] )
					->setDefaultValue( [
						'desktop' => 'yes',
						'tablet'  => 'yes',
						'mobile'  => 'yes',
					] )
				,
			];

			$style_controls = array_merge(
				[ ( new Info() )->hideBackground()->setInfo( __( 'Override Global Colors', 'kenta' ) ) ],
				$this->getGlobalColorControls( $this->getRowControlKey( '' ), ".kenta-header-row-{$this->id}" ),
				[
					( new Separator() ),
					( new Border( $this->getRowControlKey( 'border_top' ) ) )
						->setLabel( __( 'Top Border', 'kenta' ) )
						->asyncCss( ".kenta-header-row-{$this->id}", AsyncCss::border( 'border-top' ) )
						->enableResponsive()
						->displayBlock()
						->setDefaultBorder(
							...$this->getRowControlDefault( 'border_top', [
							1,
							'none',
							'var(--kenta-base-300)'
						] ) )
					,
					( new Border( $this->getRowControlKey( 'border_bottom' ) ) )
						->setLabel( __( 'Bottom Border', 'kenta' ) )
						->asyncCss( ".kenta-header-row-{$this->id}", AsyncCss::border( 'border-bottom' ) )
						->enableResponsive()
						->displayBlock()
						->setDefaultBorder(
							...$this->getRowControlDefault( 'border_bottom', [
							1,
							'none',
							'var(--kenta-base-300)'
						] ) )
					,
					( new BoxShadow( $this->getRowControlKey( 'shadow' ) ) )
						->setLabel( __( 'Box Shadow', 'kenta' ) )
						->asyncCss( ".kenta-header-row-{$this->id}", AsyncCss::shadow() )
						->enableResponsive()
						->displayBlock()
						->setDefaultShadow(
							...$this->getRowControlDefault( 'shadow', [
								'rgba(44, 62, 80, 0.05)',
								'0px',
								'10px',
								'10px',
								'0px',
								false
							]
						) )
					,
					( new Background( $this->getRowControlKey( 'background' ) ) )
						->setLabel( __( 'Background', 'kenta' ) )
						->enableResponsive()
						->asyncCss( ".kenta-header-row-{$this->id}", AsyncCss::background() )
						->setDefaultValue( $this->getRowControlDefault( 'background', [
							'type'  => 'color',
							'color' => 'var(--kenta-base-color)'
						] ) )
				]
			);

			return [
				( new Tabs() )
					->setActiveTab( 'general' )
					->addTab( 'general', __( 'General', 'kenta' ), apply_filters(
						'kenta_header_row_general_controls', $general_controls, $this->getRowControlKey( '' ), $this->id
					) )
					->addTab( 'style', __( 'Style', 'kenta' ), apply_filters(
						'kenta_header_row_style_controls', $style_controls, $this->getRowControlKey( '' ), $this->id
					) )
			];
		}
	}
}
