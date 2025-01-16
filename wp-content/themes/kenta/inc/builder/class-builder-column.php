<?php
/**
 * Generic builder column
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Placeholder;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Spacing;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\PageBuilder\Container;
use LottaFramework\Facades\Css;
use LottaFramework\Utils;

if ( ! class_exists( 'Kenta_Builder_Column' ) ) {

	class Kenta_Builder_Column extends Container {

		/**
		 * @return bool
		 */
		protected function isResponsive() {
			return true;
		}

		/**
		 * @return array
		 */
		protected function getDefaultSettings() {
			return [];
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts( $id, $data ) {
			$settings = $data['settings'] ?? [];

			// Add builder column dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) use ( $id, $settings ) {
				$css[".$id"] = array_merge(
					[
						'width'                        => $this->get( 'width', $settings ),
						'flex-direction'               => $this->get( 'direction', $settings ),
						'justify-content'              => $this->get( 'justify-content', $settings ),
						'align-items'                  => $this->get( 'align-items', $settings ),
						'--kenta-builder-elements-gap' => $this->get( 'elements-gap', $settings ),
					],
					Css::dimensions( $this->get( 'padding', $settings ), 'padding' )
				);

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function start( $id, $data, $location = '' ) {
			$index    = $data['index'] ?? 0;
			$device   = $data['device'] ?? 0;
			$settings = $data['settings'] ?? [];
			$dir      = $this->get( 'direction', $settings );
			if ( ! is_array( $dir ) ) {
				$dir = [ 'desktop' => $dir, 'tablet' => $dir, 'mobile' => $dir ];
			}

			$this->add_render_attribute( $id, 'class', Utils::clsx( [
				'kenta-builder-column',
				'kenta-builder-column-' . $index,
				'kenta-builder-column-' . $device,
				'kenta-builder-column-desktop-dir-' . $dir['desktop'] ?? 'row',
				'kenta-builder-column-tablet-dir-' . $dir['tablet'] ?? 'row',
				'kenta-builder-column-mobile-dir-' . $dir['mobile'] ?? 'row',
				$id
			],
				[ 'kenta-scroll-reveal' => $this->checked( 'scroll-reveal', $settings ) ],
				$data['css'] ?? []
			) );

			if ( is_customize_preview() ) {
				$this->add_render_attribute( $id, 'data-shortcut-inner', 'true' );
				$this->add_render_attribute( $id, 'data-shortcut', 'dashed-border' );
				$this->add_render_attribute( $id, 'data-shortcut-location', $location );
			}
			echo '<div ' . $this->render_attribute_string( $id ) . '>';
		}

		/**
		 * {@inheritDoc}
		 */
		public function end( $id, $data ) {
			echo '</div>';
		}

		public function getControls() {
			$defaults = wp_parse_args( $this->getDefaultSettings(), [
				'width'           => '100%',
				'scroll-reveal'   => 'no',
				'elements-gap'    => '12px',
				'direction'       => 'row',
				'justify-content' => 'flex-start',
				'align-items'     => 'flex-start',
				'exclude'         => [],
				'padding'         => [
					'top'    => '0px',
					'right'  => '0px',
					'bottom' => '0px',
					'left'   => '0px',
					'linked' => true,
				],
			] );

			$exclude = $defaults['exclude'];

			$controls = [
				( new Slider( 'width' ) )
					->setLabel( __( 'Width', 'kenta' ) )
					->setOption( 'responsive', $this->isResponsive() )
					->setMin( 0 )
					->setMax( 100 )
					->setDefaultUnit( '%' )
					->setDefaultValue( $defaults['width'] )
				,
				( new Separator() ),
				( new Slider( 'elements-gap' ) )
					->setLabel( __( 'Elements Gap', 'kenta' ) )
					->setOption( 'responsive', $this->isResponsive() )
					->setMin( 0 )
					->setMax( 100 )
					->setDefaultUnit( 'px' )
					->setDefaultValue( $defaults['elements-gap'] )
				,
				( new Separator() ),
				( new Toggle( 'scroll-reveal' ) )
					->setLabel( __( 'Enable Scroll Reveal', 'kenta' ) )
					->setDefaultValue( $defaults['scroll-reveal'] )
				,
				( new Separator() )
			];

			if ( in_array( 'direction', $exclude ) ) {
				$controls[] = ( new Placeholder( 'direction' ) )->setDefaultValue( $defaults['direction'] );
			} else {
				$controls[] = ( new Radio( 'direction' ) )
					->setLabel( __( 'Direction', 'kenta' ) )
					->setOption( 'responsive', $this->isResponsive() )
					->setDefaultValue( $defaults['direction'] )
					->buttonsGroupView()
					->setChoices( [
						'row'    => __( 'Row', 'kenta' ),
						'column' => __( 'Column', 'kenta' ),
					] );
			}

			if ( in_array( 'justify-content', $exclude ) ) {
				$controls[] = ( new Placeholder( 'justify-content' ) )->setDefaultValue( $defaults['justify-content'] );
			} else {
				$controls[] = ( new Radio( 'justify-content' ) )
					->setLabel( __( 'Justify Content', 'kenta' ) )
					->setOption( 'responsive', $this->isResponsive() )
					->setDefaultValue( $defaults['justify-content'] )
					->buttonsGroupView()
					->setChoices( [
						'flex-start' => __( 'Start', 'kenta' ),
						'center'     => __( 'Center', 'kenta' ),
						'flex-end'   => __( 'End', 'kenta' ),
					] );
			}

			if ( in_array( 'align-items', $exclude ) ) {
				$controls[] = ( new Placeholder( 'align-items' ) )->setDefaultValue( $defaults['align-items'] );
			} else {
				$controls[] = ( new Radio( 'align-items' ) )
					->setLabel( __( 'Align Items', 'kenta' ) )
					->setOption( 'responsive', $this->isResponsive() )
					->setDefaultValue( $defaults['align-items'] )
					->buttonsGroupView()
					->setChoices( [
						'flex-start' => __( 'Start', 'kenta' ),
						'center'     => __( 'Center', 'kenta' ),
						'flex-end'   => __( 'End', 'kenta' ),
					] );
			}

			if ( in_array( 'padding', $exclude ) ) {
				$controls[] = ( new Placeholder( 'padding' ) )->setDefaultValue( $defaults['padding'] );
			} else {
				$controls[] = ( new Spacing( 'padding' ) )
					->setLabel( __( 'Padding', 'kenta' ) )
//					->asyncCss( '', AsyncCss::dimensions( 'padding' ) )
					->setDefaultValue( $defaults['padding'] );
			}

			return $controls;
		}
	}

}
