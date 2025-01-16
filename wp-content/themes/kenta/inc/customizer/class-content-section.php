<?php
/**
 * Content customizer section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Typography;
use LottaFramework\Customizer\Section as CustomizeSection;
use LottaFramework\Facades\AsyncCss;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Content_Section' ) ) {

	class Kenta_Content_Section extends CustomizeSection {

		use Kenta_Button_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new Section( 'kenta_content_colors' ) )
					->setLabel( __( 'Colors', 'kenta' ) )
					->setControls( $this->getColorsControls() )
				,
				( new Section( 'kenta_content_typography' ) )
					->setLabel( __( 'Typography', 'kenta' ) )
					->setControls( $this->getTypographyControls() )
				,
				( new Section( 'kenta_content_buttons' ) )
					->setLabel( __( 'Buttons', 'kenta' ) )
					->setControls( $this->getButtonStyleControls( 'kenta_content_buttons_', [
						'min-height'           => '42px',
						'button-selector'      => [
							'.kenta-article-content .wp-block-button',
							'.kenta-article-content button'
						],
						'button-css-selective' => 'kenta-global-selective-css',
						'preset'               => 'solid',
						'preset-options'       => [
							'ghost'   => __( 'Ghost', 'kenta' ),
							'outline' => __( 'Outline', 'kenta' ),
							'solid'   => __( 'Solid', 'kenta' ),
							'invert'  => __( 'Invert', 'kenta' ),
							'primary' => __( 'Primary', 'kenta' ),
							'accent'  => __( 'Accent', 'kenta' ),
							'custom'  => __( 'Custom (Premium)', 'kenta' ),
						],
					] ) )
				,
				( new Section( 'kenta_content_forms' ) )
					->setLabel( __( 'Forms', 'kenta' ) )
					->setControls( $this->getFormControls() )
				,
			];
		}

		/**
		 * @return array
		 */
		protected function getColorsControls() {
			$colors = apply_filters( 'kenta_content_color_options', [
				'base_color'     => [
					'label'  => __( 'Paragraph Color', 'kenta' ),
					'colors' => [
						'initial' => 'var(--kenta-accent-active)',
					],
					'maps'   => [
						'initial' => '--kenta-content-base-color',
					],
				],
				'drop_cap_color' => [
					'label'  => __( 'Drop Cap Color', 'kenta' ),
					'colors' => [
						'initial' => 'var(--kenta-accent-color)',
					],
					'maps'   => [
						'initial' => '--kenta-content-drop-cap-color',
					],
				],
				'links_color'    => [
					'label'  => __( 'Links Color', 'kenta' ),
					'colors' => [
						'initial' => 'var(--kenta-primary-color)',
						'hover'   => 'var(--kenta-primary-active)',
					],
					'maps'   => [
						'initial' => '--kenta-link-initial-color',
						'hover'   => '--kenta-link-hover-color',
					],
				],
				'headings_color' => [
					'label'  => __( 'All Headings Color (H1 - H6)', 'kenta' ),
					'colors' => [
						'initial' => 'var(--kenta-accent-color)',
					],
					'maps'   => [
						'initial' => '--kenta-headings-color',
					],
				],
			] );


			$controls = [];

			foreach ( $colors as $item => $color ) {
				$picker = new ColorPicker( 'kenta_content_' . $item );
				$picker->setLabel( $color['label'] );
				$picker->asyncColors( '.kenta-body', $color['maps'] );

				foreach ( $color['colors'] as $id => $value ) {
					$picker->addColor( $id, ucfirst( $id ), $value );
				}

				$controls[] = $picker;
			}

			if ( ! KENTA_CMP_PRO_ACTIVE ) {
				$controls[] = ( kenta_upsell_info_control( __( 'More options in %sPro Version%s', 'kenta' ) ) )
					->showBackground();
			}

			return $controls;
		}

		/**
		 * @return array
		 */
		protected function getTypographyControls() {

			$fonts = apply_filters( 'kenta_content_typography_options', [
				'base_typography'     => [
					'label'    => __( 'Content', 'kenta' ),
					'selector' => '.kenta-article-content',
					'default'  => [
						'family'     => 'inherit',
						'fontSize'   => '1rem',
						'variant'    => '400',
						'lineHeight' => '1.75'
					],
				],
				'drop_cap_typography' => [
					'label'    => __( 'Drop Cap', 'kenta' ),
					'selector' => '.kenta-article-content .has-drop-cap::first-letter',
					'default'  => [
						'family'        => 'serif',
						'fontSize'      => '5rem',
						'variant'       => '700',
						'lineHeight'    => '1',
						'textTransform' => 'uppercase'
					],
				],
			] );

			$controls = [
				kenta_docs_control(
					__( '%sRead Documentation%s', 'kenta' ), 'https://kentatheme.com/docs/kenta-theme/general-theme-options/typography/'
				),
				( new Typography( 'kenta_site_global_typography' ) )
					->setLabel( __( 'Global Typography', 'kenta' ) )
					->setDescription( __( 'This option will affects the entire site', 'kenta' ) )
					->asyncCss( [ ':root', '.kenta-site-wrap' ], AsyncCss::typography() )
					->setDefaultValue( [
						'family'     => 'sans',
						'fontSize'   => '16px',
						'variant'    => '400',
						'lineHeight' => '1.5',
					] )
				,
				( new Separator() ),
			];

			foreach ( $fonts as $item => $font ) {
				$controls[] = ( new Typography( 'kenta_content_' . $item ) )
					->setLabel( $font['label'] )
					->asyncCss( $font['selector'], AsyncCss::typography() )
					->setDefaultValue( $font['default'] );
			}

			if ( ! KENTA_CMP_PRO_ACTIVE ) {
				$controls[] = ( kenta_upsell_info_control( __( 'More options in %sPro Version%s', 'kenta' ) ) )
					->showBackground();
			}

			return $controls;
		}

		/**
		 * @return array
		 */
		protected function getFormControls() {
			$selectors = '.kenta-form, form';

			return [
				( new Radio( 'kenta_content_form_style' ) )
					->setLabel( __( 'Style', 'kenta' ) )
					->setDefaultValue( 'classic' )
					->buttonsGroupView()
					->setChoices( [
						'classic' => __( 'Classic', 'kenta' ),
						'modern'  => __( 'Modern', 'kenta' ),
					] )
				,
				( new Typography( 'kenta_content_form_typography' ) )
					->setLabel( __( 'Typography', 'kenta' ) )
					->asyncCss( $selectors, AsyncCss::typography() )
					->setDefaultValue( [
						'family'     => 'inherit',
						'fontSize'   => '0.85rem',
						'variant'    => '400',
						'lineHeight' => '2'
					] )
				,
				( new ColorPicker( 'kenta_content_form_color' ) )
					->setLabel( __( 'Controls Color', 'kenta' ) )
					->enableAlpha()
					->asyncColors( $selectors, [
						'background' => '--kenta-form-background-color',
						'border'     => '--kenta-form-border-color',
						'active'     => '--kenta-form-active-color',
					] )
					->addColor( 'background', __( 'Background', 'kenta' ), 'var(--kenta-base-color)' )
					->addColor( 'border', __( 'Border', 'kenta' ), 'var(--kenta-base-300)' )
					->addColor( 'active', __( 'Active', 'kenta' ), 'var(--kenta-primary-color)' )
				,
			];
		}
	}

}
