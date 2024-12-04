<?php

namespace ColibriWP\Theme\Components\Header;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;
use WP_Customize_Manager;
use WP_Customize_Setting;


class Logo extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.logo.';

	public static function rearrangeControls( $wp_customize ) {

		$prefix = static::$settings_prefix;

		$controls       = array( 'blogname', 'custom_logo' );
		$priority_start = 20;

		foreach ( $controls as $index => $control ) {
			/** @var WP_Customize_Manager $wp_customize */
			$instance = $wp_customize->get_control( $control );

			if ( $instance ) {
				$instance->section             = "{$prefix}section";
				$instance->json['colibri_tab'] = 'content';
				$instance->priority            = ( $priority_start + $index * 5 );

				$active_rule_value = 'text';

				if ( $control == 'custom_logo' ) {
					$active_rule_value = 'image';

				}

				/** @var WP_Customize_Setting $setting */
				$setting = $instance->setting;
				// $setting->transport             = 'postMessage';
				$instance->json['active_rules'] = array(
					array(
						'setting'  => "{$prefix}props.layoutType",
						'operator' => '=',
						'value'    => $active_rule_value,
					),
				);
			}

			if ( $wp_customize->selective_refresh ) {
				$id      = static::selectiveRefreshSelector();
				$partial = $wp_customize->selective_refresh->get_partial( Utils::slugify( $id ) );

				if ( $partial ) {
					$partial->settings = array_merge(
						$partial->settings,
						$controls
					);
				}
			}
		}
	}

	public static function selectiveRefreshSelector() {
		$selector = Defaults::get( static::$settings_prefix . 'selective_selector', false );

		return $selector;
	}

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		Hooks::prefixed_add_action( 'rearrange_customizer_components', array( __CLASS__, 'rearrangeControls' ) );

		$prefix = static::$settings_prefix;

		$custom_logo_args = get_theme_support( 'custom-logo' );

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'logo' ),
					'panel'  => 'header_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => array(

				'alternate_logo'                    => array(
					'default' => Defaults::get( 'dark_logo', '' ),
					'control' => array(
						'label'        => Translations::escHtml( 'alternate_logo_image' ),
						'type'         => 'cropped_image',
						'section'      => "{$prefix}section",
						'priority'     => 35,
						'colibri_tab'  => 'content',

						'height'       => Utils::pathGet( $custom_logo_args, '0.height', false ),
						'width'        => Utils::pathGet( $custom_logo_args, '0.width', false ),
						'flex_height'  => Utils::pathGet( $custom_logo_args, '0.flex-height', false ),
						'flex_width'   => Utils::pathGet( $custom_logo_args, '0.flex-width', false ),

						'active_rules' => array(
							array(
								'setting'  => "{$prefix}props.heroSection.layout",
								'operator' => '=',
								'value'    => 'image',
							),
						),
					),

				),

				"{$prefix}props.heroSection.layout" => array(
					'default' => Defaults::get( "{$prefix}props.heroSection.layout" ),
					'control' => array(
						'label'       => Translations::get( 'layout_type' ),
						'focus_alias' => 'logo',
						'type'        => 'select',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
						'choices'     => array(
							'image' => Translations::escHtml( 'logo_image_only' ),
							'text'  => Translations::escHtml( 'site_title_text_only' ), /*
							'image_text_v' => Translations::escHtml( "image_with_text_below" ),
							'image_text_h'    => Translations::escHtml( "image_with_text_right" ),
							'text_image_v'    => Translations::escHtml( "image_with_text_above" ),
							'text_image_h'    => Translations::escHtml( "image_with_text_left" ),*/
						),
					),
				),
			),
		);
	}

	public function getPenPosition() {
		return static::PEN_ON_RIGHT;
	}

	public function renderContent( $parameters = array() ) {
		View::partial(
			'front-header',
			'logo',
			array(
				'component' => $this,
			)
		);
	}

	public function printTextLogo() {

		if ( $this->getLayoutType() == 'text' ) {
			echo sprintf(
				'<a class="text-logo" data-type="group" data-dynamic-mod="true" href="%1$s">%2$s</a>',
				$this->getHomeurl(),
				get_bloginfo( 'name' )
			);
		}
	}

	public function getLayoutType() {
		$prefix = static::$settings_prefix;

		return $this->mod( "{$prefix}props.layoutType" );
	}

	public function getHomeUrl() {
		return esc_url( home_url( '/' ) );
	}

	public function printImageLogo( $class = '' ) {

		$class = $class ? "{$class}-image" : '';

		if ( $this->getLayoutType() == 'image' ) : ?>
			<a href="<?php echo $this->getHomeUrl(); ?>" class="d-flex align-items-center">
				<img src="<?php echo $this->customLogoUrl(); ?>"
					 class="h-logo__image h-logo__image_h logo-image <?php echo esc_attr( $class ); ?>"/>
				<img src="<?php echo $this->alternateLogoUrl(); ?>"
					 class="h-logo__alt-image h-logo__alt-image_h logo-alt-image <?php echo esc_attr( $class ); ?>"/>
			</a>
				<?php
		  endif;
	}

	public function customLogoUrl() {
		$custom_logo_id = get_theme_mod( 'custom_logo', - 1 );

		if ( $custom_logo_id == - 1 || empty( $custom_logo_id ) ) {
			return get_template_directory_uri() . '/resources/images/logo.png';
		}

		return esc_url( wp_get_attachment_image_url( $custom_logo_id, 'full' ) );
	}

	public function alternateLogoUrl() {
		$alternate_logo_id = get_theme_mod( 'alternate_logo', - 1 );

		if ( $alternate_logo_id == - 1 || empty( $alternate_logo_id ) ) {
			return $this->customLogoUrl();
		}
		if ( is_numeric( $alternate_logo_id ) ) {
			return esc_url( wp_get_attachment_image_url( $alternate_logo_id, 'full' ) );
		} else {
			return esc_url( $alternate_logo_id );
		}
	}
}
