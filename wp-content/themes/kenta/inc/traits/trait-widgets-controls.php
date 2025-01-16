<?php
/**
 * Widgets trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\CallToAction;
use LottaFramework\Customizer\Controls\ImageRadio;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\Css;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Widgets_Controls' ) ) {

	/**
	 * Widgets controls
	 */
	trait Kenta_Widgets_Controls {

		/**
		 * Get Widgets Control
		 */
		public function getWidgetsControls( $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'css-selective-refresh' => '',
				'async-selector'        => '',
				'scroll-reveal'         => 'yes',
				'customize-location'    => 'sidebar-widgets-' . $this->getSlug(),
				'sidebar-style'         => 'style-1',
				'widgets-style'         => 'plain',
				'widgets-spacing'       => '24px',
				'title-tag'             => 'h3',
				'title-style'           => 'style-1',
				'content-align'         => 'left',
				'list-icon'             => 'yes',
				'link-underline'        => 'no',
				'title-typography'      => [
					'family'        => 'inherit',
					'fontSize'      => '0.875rem',
					'variant'       => '600',
					'lineHeight'    => '1.5em',
					'textTransform' => 'uppercase',
				],
				'title-color'           => 'var(--kenta-accent-active)',
				'title-indicator'       => 'var(--kenta-primary-color)',
				'content-typography'    => [
					'family'     => 'inherit',
					'fontSize'   => '0.875rem',
					'variant'    => '400',
					'lineHeight' => '1.5em'
				],
				'text-color'            => 'var(--kenta-accent-color)',
				'link-initial'          => 'var(--kenta-primary-color)',
				'link-hover'            => 'var(--kenta-primary-active)',
				'widgets-background'    => 'var(--kenta-base-color)',
				'widgets-border'        => [ 1, 'none', 'var(--kenta-base-200)' ],
				'widgets-shadow'        => [
					'rgba(44, 62, 80, 0.15)',
					'0px',
					'15px',
					'18px',
					'-15px',
				],
				'widgets-shadow-enable' => false,
				'widgets-padding'       => [
					'top'    => '24px',
					'right'  => '24px',
					'bottom' => '24px',
					'left'   => '24px',
					'linked' => true
				],
				'widgets-radius'        => [
					'top'    => '0px',
					'bottom' => '0px',
					'left'   => '0px',
					'right'  => '0px',
					'linked' => true,
				],
			] );

			return [
				( new CallToAction() )
					->setLabel( __( 'Edit Widgets', 'kenta' ) )
					->displayAsButton()
					->expandCustomize( $defaults['customize-location'] )
				,
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), $this->getContentControls( $defaults ) )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getStyleControls( $defaults ) )
				,
			];
		}

		protected function getContentControls( $defaults ) {

			$controls = [
				( new ImageRadio( $this->getSlug( 'sidebar-style' ) ) )
					->setLabel( __( 'Sidebar Style', 'kenta' ) )
					->setDefaultValue( $defaults['sidebar-style'] )
					->setColumns( 2 )
					->setChoices( [
						'style-1' => [
							'title' => __( 'Style 1', 'kenta' ),
							'src'   => kenta_image_url( 'sidebar-style-1.png' ),
						],
						'style-2' => [
							'title' => __( 'Style 2', 'kenta' ),
							'src'   => kenta_image_url( 'sidebar-style-2.png' ),
						]
					] )
				,
				( new Separator() ),
				( new Select( $this->getSlug( 'title-tag' ) ) )
					->setLabel( __( 'Widget Title Tag', 'kenta' ) )
					->setDefaultValue( $defaults['title-tag'] )
					->setChoices( [
						'h1'   => 'H1',
						'h2'   => 'H2',
						'h3'   => 'H3',
						'h4'   => 'H4',
						'h5'   => 'H5',
						'h6'   => 'H6',
						'span' => 'Span',
					] )
				,
				( new Select( $this->getSlug( 'title-style' ) ) )
					->setLabel( __( 'Widget Title Style', 'kenta' ) )
					->setDefaultValue( $defaults['title-style'] )
					->setChoices( [
						'plain'   => __( 'Plain', 'kenta' ),
						'style-1' => __( 'Style 1', 'kenta' ),
						'style-2' => __( 'Style 2', 'kenta' ),
					] )
				,
				( new Separator() ),
				( new Slider( $this->getSlug( 'widgets-spacing' ) ) )
					->setLabel( __( 'Widgets Spacing', 'kenta' ) )
					->asyncCss( $defaults['async-selector'], [
						'--kenta-widgets-spacing' => 'value'
					] )
					->enableResponsive()
					->setMin( 0 )
					->setMax( 100 )
					->setDefaultValue( $defaults['widgets-spacing'] )
					->setDefaultUnit( 'px' )
				,
				( new Toggle( $this->getSlug( 'scroll-reveal' ) ) )
					->setLabel( __( 'Enable Scroll Reveal', 'kenta' ) )
					->setDefaultValue( $defaults['scroll-reveal'] )
				,
			];

			return $controls;
		}

		protected function getStyleControls( $defaults ) {

			$selector = $defaults['async-selector'];

			$controls = [
				( new Toggle( $this->getSlug( 'list-icon' ) ) )
					->setLabel( __( 'List Icon', 'kenta' ) )
					->asyncCss( "$selector .kenta-widget ul li", [
						'--fa-display'     => AsyncCss::unescape( AsyncCss::valueMapper( [
							'yes' => 'inline-block',
							'no'  => 'none'
						] ) ),
						'--widget-list-pl' => AsyncCss::unescape( AsyncCss::valueMapper( [
							'yes' => '1.4rem',
							'no'  => '0'
						] ) ),
					] )
					->setDefaultValue( $defaults['list-icon'] )
				,
				( new Toggle( $this->getSlug( 'link-underline' ) ) )
					->setLabel( __( 'Link Underline', 'kenta' ) )
					->asyncCss( "$selector a", [
						'text-decoration' => AsyncCss::unescape( AsyncCss::valueMapper( [
							'yes' => 'underline',
							'no'  => 'none'
						] ) )
					] )
					->setDefaultValue( $defaults['link-underline'] )
				,
				( new Separator() ),
				( new Select( $this->getSlug( 'widgets-style' ) ) )
					->setLabel( __( 'Widgets Card Style', 'kenta' ) )
					->setDefaultValue( $defaults['widgets-style'] )
					->bindSelectiveRefresh( $defaults['css-selective-refresh'] )
					->setChoices( kenta_card_style_preset_options() )
				,
			];

			return apply_filters( 'kenta_widgets_style_controls', $controls, $this->getSlug() . '_', $defaults );
		}

		/**
		 * {@inheritDoc}
		 */
		public function enqueue_frontend_scripts( $id = null, $data = [] ) {
			$id = $id ?? $this->slug;

			$options  = $this->getOptions();
			$settings = $data['settings'] ?? [];

			// Add widgets area dynamic css
			add_filter( 'kenta_filter_dynamic_css', function ( $css ) use ( $id, $options, $settings ) {

				$sidebar_style        = $options->get( $this->getSlug( 'sidebar-style' ), $settings );
				$widgets_style_preset = $options->get( $this->getSlug( 'widgets-style' ), $settings );

				$widgets_css = $widgets_style_preset === 'custom' ? array_merge(
					Css::background( $options->get( $this->getSlug( 'widgets-background' ), $settings ) ),
					Css::border( $options->get( $this->getSlug( 'widgets-border' ), $settings ) ),
					Css::shadow( $options->get( $this->getSlug( 'widgets-shadow' ), $settings ) )
				) : kenta_card_preset_style( $widgets_style_preset );

				$widgets_css = array_merge(
					$widgets_css,
					Css::dimensions( $options->get( $this->getSlug( 'widgets-padding' ), $settings ), 'padding' ),
					Css::dimensions( $options->get( $this->getSlug( 'widgets-radius' ), $settings ), 'border-radius' )
				);

				if ( $sidebar_style === 'style-1' ) {
					$css[".$id .kenta-widget"] = $widgets_css;
				}

				// list icon style
				if ( ! $options->checked( $this->getSlug( 'list-icon' ), $settings ) ) {
					$css[".$id .kenta-widget ul li"] = [
						'--fa-display'     => 'none',
						'--widget-list-pl' => '0',
					];
				}

				$css[".$id"] = array_merge(
					$sidebar_style === 'style-2' ? $widgets_css : [],
					Css::typography( $options->get( $this->getSlug( 'content-typography' ), $settings ) ),
					Css::colors( $options->get( $this->getSlug( 'content-color' ), $settings ), [
						'text'    => '--kenta-widgets-text-color',
						'initial' => '--kenta-widgets-link-initial',
						'hover'   => '--kenta-widgets-link-hover',
					] ),
					[
						'width'                   => '100%',
						'--kenta-widgets-spacing' => $options->get( $this->getSlug( 'widgets-spacing' ), $settings ),
					]
				);

				$css[".$id .widget-title"] = array_merge(
					Css::typography( $options->get( $this->getSlug( 'title-typography' ), $settings ) ),
					Css::colors( $options->get( $this->getSlug( 'title-color' ), $settings ), [
						'initial'   => 'color',
						'indicator' => '--kenta-heading-indicator',
					] )
				);

				return $css;
			} );
		}

		/**
		 * {@inheritDoc}
		 */
		public function render( $attrs = [] ) {
			$this->beforeRender( $attrs );
			?>
            <div <?php $this->print_attribute_string( $this->getAttrId( $attrs ) ); ?>>
				<?php dynamic_sidebar( $this->getSidebarId( $attrs ) ); ?>
            </div>
			<?php
		}
	}

}
