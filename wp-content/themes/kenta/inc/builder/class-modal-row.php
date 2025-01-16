<?php
/**
 * Modal row
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\BoxShadow;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\GenericBuilder\Row;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Modal_Row' ) ) {

	class Kenta_Modal_Row extends Row {
		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts() {
			// Add dynamic css for row
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) {
				$fixed = Css::colors( CZ::get( 'kenta_canvas_close_button_color' ), [
					'initial' => '--kenta-modal-action-initial',
					'hover'   => '--kenta-modal-action-hover',
				] );

				if ( CZ::get( 'kenta_canvas_modal_type' ) === 'drawer' ) {
					$fixed['width'] = CZ::get( 'kenta_canvas_drawer_width' );

					$fixed[ ( CZ::get( 'kenta_canvas_drawer_placement' ) === 'left' ) ? 'margin-right' : 'margin-left' ] = 'auto';
				}

				$css['.kenta-off-canvas .kenta-modal-inner'] = $fixed;

				$css['.kenta-off-canvas .kenta-modal-inner'] = array_merge(
					Css::shadow( CZ::get( 'kenta_canvas_modal_shadow' ) ),
					Css::background( CZ::get( 'kenta_canvas_modal_background' ) )
					, $fixed
				);

				$css['.kenta-off-canvas'] = Css::background( CZ::get( 'kenta_canvas_modal_mask' ) );

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function beforeRow() {
			$behaviour = 'toggle';

			if ( CZ::get( 'kenta_canvas_modal_type' ) === 'drawer' ) {
				$behaviour = 'drawer-' . CZ::get( 'kenta_canvas_drawer_placement' );
			}

			$attrs = [
				'id'                    => 'kenta-off-canvas-modal',
				'class'                 => 'kenta-off-canvas kenta-modal',
				'data-toggle-behaviour' => $behaviour,
			];

			$inner_attrs = [
				'class' => 'kenta-modal-inner'
			];

			if ( is_customize_preview() ) {
				$inner_attrs['data-shortcut']          = 'border';
				$inner_attrs['data-shortcut-location'] = 'kenta_header:' . $this->id;
			}

			?>
        <div <?php Utils::print_attribute_string( $attrs ); ?>>
        <div <?php Utils::print_attribute_string( $inner_attrs ); ?>>
                <div class="kenta-modal-actions">
                    <button id="kenta-close-off-canvas-modal"
                            class="kenta-close-modal"
                            data-toggle-target="#kenta-off-canvas-modal"
                            type="button"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="kenta-modal-content" data-redirect-focus="#kenta-close-off-canvas-modal">
			<?php
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterRow() {
			echo '</div></div></div>';
		}

		protected function getRowControls() {
			return [
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), $this->getContentControls() )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getStyleControls() )
			];
		}

		protected function getStyleControls() {
			return [
				( new ColorPicker( 'kenta_canvas_close_button_color' ) )
					->setLabel( __( 'Close Button Color', 'kenta' ) )
					->addColor( 'initial', __( 'Initial', 'kenta' ), 'var(--kenta-accent-color)' )
					->addColor( 'hover', __( 'Hover', 'kenta' ), 'var(--kenta-primary-color)' )
				,
				( new Separator() ),
				( new Background( 'kenta_canvas_modal_background' ) )
					->setLabel( __( 'Modal Background', 'kenta' ) )
					->setDefaultValue( [
						'type'  => 'color',
						'color' => 'var(--kenta-base-color)',
					] )
				,
				( new Condition() )
					->setCondition( [ 'kenta_canvas_modal_type' => 'drawer' ] )
					->setControls( [
						( new Background( 'kenta_canvas_modal_mask' ) )
							->setLabel( __( 'Modal Mask', 'kenta' ) )
							->setDefaultValue( [
								'type'  => 'color',
								'color' => 'rgba(0, 0, 0, 0)',
							] )
						,
						( new BoxShadow( 'kenta_canvas_modal_shadow' ) )
							->setLabel( __( 'Modal Shadow', 'kenta' ) )
							->setDefaultShadow(
								'rgba(44, 62, 80, 0.35)',
								'0px', '0px',
								'70px', '0px', true
							)
						,
					] )
				,
			];
		}

		protected function getContentControls() {
			return [
				( new Radio( 'kenta_canvas_modal_type' ) )
					->setLabel( __( 'Modal Type', 'kenta' ) )
					->setDefaultValue( 'drawer' )
					->buttonsGroupView()
					->setChoices( [
						'modal'  => __( 'Modal', 'kenta' ),
						'drawer' => __( 'Drawer', 'kenta' ),
					] )
				,
				( new Condition() )
					->setCondition( [ 'kenta_canvas_modal_type' => 'drawer' ] )
					->setControls( [
						( new Radio( 'kenta_canvas_drawer_placement' ) )
							->setLabel( __( 'Drawer Placement', 'kenta' ) )
							->setDefaultValue( 'right' )
							->buttonsGroupView()
							->setChoices( [
								'left'  => __( 'Left', 'kenta' ),
								'right' => __( 'Right', 'kenta' ),
							] )
						,
						( new Separator() ),
						( new Slider( 'kenta_canvas_drawer_width' ) )
							->setLabel( __( 'Drawer Width', 'kenta' ) )
							->enableResponsive()
							->setDefaultValue( [
								'desktop' => '500px',
								'tablet'  => '65vw',
								'mobile'  => '90vw',
							] )
							->setOption( 'units', Utils::units_config( [
								[ 'unit' => 'px', 'min' => 0, 'max' => 1000 ],
							] ) )
						,
					] )
				,
			];
		}
	}
}
