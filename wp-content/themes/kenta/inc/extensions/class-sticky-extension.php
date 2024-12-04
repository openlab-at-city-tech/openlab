<?php

use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\CZ;

if ( ! class_exists( 'Kenta_Sticky_Extension' ) ) {

	class Kenta_Sticky_Extension {

		public function __construct() {
			add_filter( 'kenta_header_builder_controls', [ $this, 'injectControls' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

			add_filter( 'kenta_filter_dynamic_css', [ $this, 'inlineCss' ] );

			add_action( 'kenta_before_header_row_render', [ $this, 'beforeRowRender' ], 0 );
			add_action( 'kenta_after_header_row_render', [ $this, 'afterRowRender' ] );
		}

		public function enqueue_scripts() {
			if ( ! CZ::checked( 'kenta_sticky_header' ) ) {
				return;
			}

			wp_enqueue_script(
				'hc-sticky',
				get_template_directory_uri() . '/dist/vendor/hc-sticky/hc-sticky.min.js',
				[],
				KENTA_VERSION
			);
		}

		public function inlineCss( $css ) {
			if ( CZ::checked( 'kenta_sticky_header' ) ) {
				$css['.kenta-is-sticky'] = array_merge(
					\LottaFramework\Facades\Css::border( CZ::get( 'kenta_sticky_header_border_top' ), 'border-top' ),
					\LottaFramework\Facades\Css::border( CZ::get( 'kenta_sticky_header_border_bottom' ), 'border-bottom' ),
					\LottaFramework\Facades\Css::shadow( CZ::get( 'kenta_sticky_header_shadow' ) )
				);
			}

			return $css;
		}

		public function injectControls( $controls ) {
			$controls[] = ( new Section( 'kenta_sticky_header' ) )
				->setLabel( __( 'Sticky Header', 'kenta' ) )
				->enableSwitch( false )
				->keepMarginBelow()
				->setControls( [
					( new \LottaFramework\Customizer\Controls\Select( 'kenta_sticky_header_rows' ) )
						->setLabel( __( 'Sticky Rows', 'kenta' ) )
						->setDefaultValue( 'all' )
						->setChoices( [
							'all'                => __( 'All Rows', 'kenta' ),
							'top-row'            => __( 'Top Row Only', 'kenta' ),
							'primary-row'        => __( 'Primary Row Only', 'kenta' ),
							'bottom-row'         => __( 'Bottom Row Only', 'kenta' ),
							'top-primary-row'    => __( 'Top & Primary Row', 'kenta' ),
							'primary-bottom-row' => __( 'Primary & Bottom Row', 'kenta' ),
						] )
					,
					( new \LottaFramework\Customizer\Controls\Separator() ),
					( new \LottaFramework\Customizer\Controls\Border( 'kenta_sticky_header_border_top' ) )
						->setLabel( __( 'Top Border', 'kenta' ) )
						->asyncCss( ".kenta-is-sticky", AsyncCss::border( 'border-top' ) )
						->enableResponsive()
						->displayBlock()
						->setDefaultBorder( 1, 'none', 'var(--kenta-base-300)' )
					,
					( new \LottaFramework\Customizer\Controls\Border( 'kenta_sticky_header_border_bottom' ) )
						->setLabel( __( 'Bottom Border', 'kenta' ) )
						->asyncCss( ".kenta-is-sticky", AsyncCss::border( 'border-bottom' ) )
						->enableResponsive()
						->displayBlock()
						->setDefaultBorder( 1, 'none', 'var(--kenta-base-300)' )
					,
					( new \LottaFramework\Customizer\Controls\BoxShadow( 'kenta_sticky_header_shadow' ) )
						->setLabel( __( 'Box Shadow', 'kenta' ) )
						->asyncCss( ".kenta-is-sticky", AsyncCss::shadow() )
						->enableResponsive()
						->displayBlock()
						->setDefaultShadow(
							'rgba(44, 62, 80, 0.05)',
							'0px',
							'10px',
							'10px',
							'0px',
							false
						)
					,
				] );

			return $controls;
		}

		public function beforeRowRender( $id ) {

			if ( $id === 'modal' || ! CZ::checked( 'kenta_sticky_header' ) ) {
				return;
			}

			$sticky_rows = CZ::get( 'kenta_sticky_header_rows' );

			if ( $id === 'top_bar' && ! in_array( $sticky_rows, [ 'all', 'top-row', 'top-primary-row' ] ) ) {
				return;
			}
			if ( $id === 'primary_navbar' && ! in_array( $sticky_rows, [ 'primary-row', 'primary-bottom-row' ] ) ) {
				return;
			}
			if ( $id === 'bottom_row' && $sticky_rows !== 'bottom-row' ) {
				return;
			}

			$attrs = array(
				'class' => 'kenta-sticky',
			);

			echo '<div ' . \LottaFramework\Utils::render_attribute_string( $attrs ) . '>';
		}

		public function afterRowRender( $id ) {

			if ( $id === 'modal' || ! CZ::checked( 'kenta_sticky_header' ) ) {
				return;
			}

			$sticky_rows = CZ::get( 'kenta_sticky_header_rows' );

			if ( $id === 'top_bar' && $sticky_rows !== 'top-row' ) {
				return;
			}
			if ( $id === 'primary_navbar' && ! in_array( $sticky_rows, [ 'primary-row', 'top-primary-row' ] ) ) {
				return;
			}
			if ( $id === 'bottom_row' && ! in_array( $sticky_rows, [ 'all', 'bottom-row' ] ) ) {
				return;
			}

			echo '</div>';
		}
	}
}
new Kenta_Sticky_Extension();
