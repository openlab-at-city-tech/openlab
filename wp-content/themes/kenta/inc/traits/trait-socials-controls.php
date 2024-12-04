<?php
/**
 * Socials trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\CallToAction;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Toggle;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Socials_Controls' ) ) {
	/**
	 * Socials controls
	 */
	trait Kenta_Socials_Controls {
		/**
		 * @param string $id
		 *
		 * @return string
		 */
		protected function getSocialControlId( $id ) {
			return $id;
		}

		/**
		 * @return array
		 */
		protected function getSocialControls( $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'render-callback'      => [],
				'selector'             => '',
				'new-tab'              => 'yes',
				'no-follow'            => 'yes',
				'icon-size'            => '16px',
				'icon-spacing'         => '16px',
				'icons-color-type'     => 'custom',
				'icons-shape'          => 'none',
				'icons-fill-type'      => 'solid',
				'icons-color-initial'  => 'var(--kenta-accent-color)',
				'icons-color-hover'    => 'var(--kenta-primary-color)',
				'icons-bg-initial'     => 'var(--kenta-base-200)',
				'icons-bg-hover'       => 'var(--kenta-primary-color)',
				'icons-border-initial' => 'var(--kenta-base-200)',
				'icons-border-hover'   => 'var(--kenta-primary-color)',
				'icons-box-spacing'    => [
					'top'    => '0px',
					'right'  => '0px',
					'bottom' => '0px',
					'left'   => '0px',
					'linked' => true,
				],
			] );

			return [
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), $this->getSocialContentControls( $defaults ) )
					->addTab( 'style', __( 'Style', 'kenta' ), $this->getSocialStyleControls( $defaults ) )
			];
		}

		/**
		 * @param array $defaults
		 *
		 * @return array
		 */
		protected function getSocialContentControls( $defaults = [] ) {

			$render_callback = $defaults['render-callback'];

			$controls = [
				( new CallToAction() )
					->setLabel( __( 'Edit Social Network Accounts', 'kenta' ) )
					->displayAsButton()
					->expandCustomize( 'kenta_global:kenta_global_socials' )
				,
				( new Separator() ),
				( new Toggle( $this->getSocialControlId( 'open_new_tab' ) ) )
					->setLabel( __( 'Open In New Tab', 'kenta' ) )
					->selectiveRefresh( ...$render_callback )
					->setDefaultValue( $defaults['new-tab'] )
				,
				( new Toggle( $this->getSocialControlId( 'no_follow' ) ) )
					->setLabel( __( 'No Follow', 'kenta' ) )
					->selectiveRefresh( ...$render_callback )
					->setDefaultValue( $defaults['no-follow'] )
				,
				( new Separator() ),
				( new Slider( $this->getSocialControlId( 'icons_size' ) ) )
					->setLabel( __( 'Icons Size', 'kenta' ) )
					->enableResponsive()
					->asyncCss( $defaults['selector'], [ '--kenta-social-icons-size' => 'value' ] )
					->setMin( 5 )
					->setMax( 50 )
					->setDefaultUnit( 'px' )
					->setDefaultValue( $defaults['icon-size'] )
				,
				( new Slider( $this->getSocialControlId( 'icons_spacing' ) ) )
					->setLabel( __( 'Icons Spacing', 'kenta' ) )
					->enableResponsive()
					->asyncCss( $defaults['selector'], [ '--kenta-social-icons-spacing' => 'value' ] )
					->setMin( 0 )
					->setMax( 100 )
					->setDefaultUnit( 'px' )
					->setDefaultValue( $defaults['icon-spacing'] )
				,
				( new Separator() ),
				( new Radio( $this->getSocialControlId( 'icons_color_type' ) ) )
					->setLabel( __( 'Icons Color', 'kenta' ) )
					->buttonsGroupView()
					->selectiveRefresh( ...$render_callback )
					->setDefaultValue( $defaults['icons-color-type'] )
					->setChoices( [
						'custom'   => __( 'Custom', 'kenta' ),
						'official' => __( 'Official', 'kenta' ),
					] )
				,
			];

			return apply_filters( 'kenta_socials_element_content_controls', $controls, $this->getSocialControlId( '' ), $defaults );
		}

		/**
		 * @param array $defaults
		 *
		 * @return array
		 */
		protected function getSocialStyleControls( $defaults = [] ) {
			$controls = [
				( new Condition() )
					->setCondition( [ $this->getSocialControlId( 'icons_color_type' ) => 'custom' ] )
					->setControls( [
						( new ColorPicker( $this->getSocialControlId( 'icons_color' ) ) )
							->setLabel( __( 'Icons Color', 'kenta' ) )
							->addColor( 'initial', __( 'Initial', 'kenta' ), $defaults['icons-color-initial'] )
							->addColor( 'hover', __( 'Hover', 'kenta' ), $defaults['icons-color-hover'] )
							->asyncColors( $defaults['selector'] . ' .kenta-social-link', [
								'initial' => '--kenta-social-icon-initial-color',
								'hover'   => '--kenta-social-icon-hover-color',
							] )
						,
						( new Separator() ),
					] )
				,
			];

			return apply_filters( 'kenta_socials_element_style_controls', $controls, $this->getSocialControlId( '' ), $defaults );
		}
	}
}
