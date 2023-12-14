<?php

namespace Gravity_Forms\Gravity_Forms\Form_Display;

use Gravity_Forms\Gravity_Forms\Form_Display\Full_Screen\Full_Screen_Handler;
use Gravity_Forms\Gravity_Forms\Form_Display\Block_Styles\Block_Styles_Handler;
use Gravity_Forms\Gravity_Forms\GF_Service_Container;
use Gravity_Forms\Gravity_Forms\GF_Service_Provider;
use Gravity_Forms\Gravity_Forms\Query\GF_Query_Service_Provider;
use Gravity_Forms\Gravity_Forms\Query\JSON_Handlers\GF_Query_JSON_Handler;
use Gravity_Forms\Gravity_Forms\Query\JSON_Handlers\GF_String_JSON_Handler;
use \GFCommon;
use \GFForms;
use \GFFormDisplay;

/**
 * Class GF_Form_Display_Service_Provider
 *
 * Service provider for the Form_Display Service.
 *
 * @package Gravity_Forms\Gravity_Forms\Form_Display;
 */
class GF_Form_Display_Service_Provider extends GF_Service_Provider {

	const FULL_SCREEN_HANDLER   = 'full_screen_handler';
	const BLOCK_STYLES_HANDLER  = 'block_styles_handler';
	const BLOCK_STYLES_DEFAULTS = 'block_styles_defaults';

	/**
	 * Register services to the container.
	 *
	 * @since 2.7
	 *
	 * @param GF_Service_Container $container
	 */
	public function register( GF_Service_Container $container ) {
		require_once( plugin_dir_path( __FILE__ ) . '/full-screen/class-full-screen-handler.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/block-styles/views/class-form-view.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/block-styles/views/class-confirmation-view.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/block-styles/block-styles-handler.php' );

		$container->add( self::FULL_SCREEN_HANDLER, function() use ( $container ) {
			// Use string handler for now to avoid JSON query issues on old platforms.
			$handler = $container->get( GF_Query_Service_Provider::JSON_STRING_HANDLER );

			return new Full_Screen_Handler( $handler );
		});

		$container->add( self::BLOCK_STYLES_DEFAULTS, function() {
			return function( $form = array() ) {

				$form_style_settings = rgar( $form, 'styles' ) ? $form['styles'] : array();
				$form_styles         = GFFormDisplay::get_form_styles( $form_style_settings );

				return array(
					'theme'                        => get_option( 'rg_gforms_default_theme', 'gravity-theme' ),
					'inputSize'                    => rgar( $form_styles, 'inputSize' ) ? $form_styles['inputSize'] : 'md',
					'inputBorderRadius'            => rgar( $form_styles, 'inputBorderRadius' ) ? $form_styles['inputBorderRadius'] : 3,
					'inputBorderColor'             => rgar( $form_styles, 'inputBorderColor' ) ? $form_styles['inputBorderColor'] : '#686e77',
					'inputBackgroundColor'         => rgar( $form_styles, 'inputBackgroundColor' ) ? $form_styles['inputBackgroundColor'] : '#fff',
					'inputColor'                   => rgar( $form_styles, 'inputColor' ) ? $form_styles['inputColor'] : '#112337',
					// Setting this to empty allows us to set this to what the appropriate default
					// should be for the theme framework and CSS API. When empty, it defaults to:
					// buttonPrimaryBackgroundColor
					'inputPrimaryColor'            => rgar( $form_styles, 'inputPrimaryColor' ) ? $form_styles['inputPrimaryColor'] : '', // #204ce5
					'labelFontSize'                => rgar( $form_styles, 'labelFontSize' ) ? $form_styles['labelFontSize'] : 14,
					'labelColor'                   => rgar( $form_styles, 'labelColor' ) ? $form_styles['labelColor'] : '#112337',
					'descriptionFontSize'          => rgar( $form_styles, 'descriptionFontSize' ) ? $form_styles['descriptionFontSize'] : 13,
					'descriptionColor'             => rgar( $form_styles, 'descriptionColor' ) ? $form_styles['descriptionColor'] : '#585e6a',
					'buttonPrimaryBackgroundColor' => rgar( $form_styles, 'buttonPrimaryBackgroundColor' ) ? $form_styles['buttonPrimaryBackgroundColor'] : '#204ce5',
					'buttonPrimaryColor'           => rgar( $form_styles, 'buttonPrimaryColor' ) ? $form_styles['buttonPrimaryColor'] : '#fff',
				);
			};
		}, true );

		$container->add( self::BLOCK_STYLES_HANDLER, function() use ( $container ) {
			return new Block_Styles_Handler( $container->get( self::BLOCK_STYLES_DEFAULTS ) );
		});
	}

	/**
	 * Initialize any actions or hooks.
	 *
	 * @since
	 *
	 * @param GF_Service_Container $container
	 *
	 * @return void
	 */
	public function init( GF_Service_Container $container ) {
		add_filter( 'template_include', function ( $template ) use ( $container ) {
			return $container->get( self::FULL_SCREEN_HANDLER )->load_full_screen_template( $template );
		} );

		add_action( 'init', function () use ( $container ) {
			$container->get( self::BLOCK_STYLES_HANDLER )->handle();
		}, 0, 0 );

		add_action( 'gform_enqueue_scripts', array( $this, 'register_theme_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_theme_styles' ) );
	}

	public function register_theme_styles() {
		$base_url = GFCommon::get_base_url();
		$version  = GFForms::$version;
		$min      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		/**
		 * Allows users to disable all CSS files from being loaded on the Front End.
		 *
		 * @since 2.8
		 *
		 * @param boolean Whether to disable css.
		 */
		$disable_css = apply_filters( 'gform_disable_css', get_option( 'rg_gforms_disable_css' ) );

		if ( ! $disable_css ) {
			wp_register_style( 'gravity_forms_theme_reset', "{$base_url}/assets/css/dist/gravity-forms-theme-reset{$min}.css", array(), $version );
			wp_register_style( 'gravity_forms_theme_foundation', "{$base_url}/assets/css/dist/gravity-forms-theme-foundation{$min}.css", array(), $version );
			wp_register_style(
				'gravity_forms_theme_framework',
				"{$base_url}/assets/css/dist/gravity-forms-theme-framework{$min}.css",
				array(
					'gravity_forms_theme_reset',
					'gravity_forms_theme_foundation',
				),
				$version
			);
			wp_register_style( 'gravity_forms_orbital_theme', "{$base_url}/assets/css/dist/gravity-forms-orbital-theme{$min}.css", array( 'gravity_forms_theme_framework' ), $version );
		}
	}

}

