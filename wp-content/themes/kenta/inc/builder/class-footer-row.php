<?php
/**
 * Footer builder row
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\Border;
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
use LottaFramework\Utils;

if ( ! class_exists( 'Kenta_Footer_Row' ) ) {

	class Kenta_Footer_Row extends Row {

		use Kenta_Global_Color_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add dynamic css for row
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$visibility = CZ::get( $this->getRowControlKey( 'visibility' ) );

				$css[".kenta-footer-row-{$this->id}"] = array_merge(
					[
						'z-index'        => CZ::get( $this->getRowControlKey( 'z_index' ) ),
						'padding-top'    => CZ::get( $this->getRowControlKey( 'vt_spacing' ) ),
						'padding-bottom' => CZ::get( $this->getRowControlKey( 'vt_spacing' ) ),
						'display'        => [
							'desktop' => ( isset( $visibility['desktop'] ) && $visibility['desktop'] === 'yes' ) ? 'block' : 'none',
							'tablet'  => ( isset( $visibility['tablet'] ) && $visibility['tablet'] === 'yes' ) ? 'block' : 'none',
							'mobile'  => ( isset( $visibility['mobile'] ) && $visibility['mobile'] === 'yes' ) ? 'block' : 'none',
						],
					],
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

				return $css;
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
		public function beforeRowDevice( $device, $settings ) {
			$attrs = [
				'class'    => 'kenta-footer-row kenta-footer-row-' . $this->id,
				'data-row' => $this->id,
			];

			if ( is_customize_preview() ) {
				$attrs['data-shortcut']          = 'border';
				$attrs['data-shortcut-location'] = 'kenta_footer:' . $this->id;
			}

			echo '<div ' . Utils::render_attribute_string( $attrs ) . '>';
			echo '<div class="kenta-max-w-wide has-global-padding container mx-auto px-gutter flex flex-wrap">';
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterRowDevice( $device, $settings ) {
			echo '</div></div>';
		}

		/**
		 * @param $key
		 *
		 * @return string
		 */
		protected function getRowControlKey( $key ) {
			return 'kenta_footer_' . $this->id . '_row_' . $key;
		}

		/**
		 * {@inheritDoc}
		 */
		protected function getRowControls() {
			return [
				( new Tabs() )
					->setActiveTab( 'general' )
					->addTab( 'general', __( 'General', 'kenta' ), [
						( new Slider( $this->getRowControlKey( 'vt_spacing' ) ) )
							->setLabel( __( 'Vertical Spacing', 'kenta' ) )
							->asyncCss( ".kenta-footer-row-{$this->id}", [
								'padding-top'    => 'value',
								'padding-bottom' => 'value',
							] )
							->enableResponsive()
							->setMin( 0 )
							->setMax( 100 )
							->setDefaultUnit( 'px' )
							->setDefaultValue( '24px' )
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
							->asyncCss( ".kenta-footer-row-{$this->id}", [
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
					] )
					->addTab( 'style', __( 'Style', 'kenta' ), array_merge(
						[ ( new Info() )->hideBackground()->setInfo( __( 'Override Global Colors', 'kenta' ) ) ],
						$this->getGlobalColorControls( $this->getRowControlKey( '' ), ".kenta-footer-row-{$this->id}" ),
						[
							( new Separator() ),
							( new Border( $this->getRowControlKey( 'border_top' ) ) )
								->setLabel( __( 'Top Border', 'kenta' ) )
								->asyncCss( ".kenta-footer-row-{$this->id}", AsyncCss::border( 'border-top' ) )
								->enableResponsive()
								->displayBlock()
								->setDefaultBorder(
									...$this->getRowControlDefault( 'border_top', [
									1,
									'none',
									'var(--kenta-base-300)'
								] )
								)
							,
							( new Separator() ),
							( new Background( $this->getRowControlKey( 'background' ) ) )
								->setLabel( __( 'Background', 'kenta' ) )
								->asyncCss( ".kenta-footer-row-{$this->id}", AsyncCss::background() )
								->enableResponsive()
								->setDefaultValue( [
									'type'  => 'color',
									'color' => 'var(--kenta-base-color)'
								] )
							,
						]
					) )
			];
		}
	}
}