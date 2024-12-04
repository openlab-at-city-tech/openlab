<?php

namespace Kubio\Theme\Components\Header;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class Logo extends \ColibriWP\Theme\Components\Header\Logo {
	protected static $settings_prefix = 'front-header.logo.';

	public static function selectiveRefreshSelector() {
		return "[data-kubio-partial-refresh='logo']";
	}

	protected static function getOptions() {
		static::doOptionsHooks();

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

				'alternate_logo'            => array(
					'default'   => Defaults::get( 'dark_logo', '' ),
					'transport' => 'refresh', // use refresh transport to properly load the classes
					'section'   => "{$prefix}section",
					'control'   => array(
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
								'setting'  => "{$prefix}props.layoutType",
								'operator' => '=',
								'value'    => 'image',
							),
						),
					),

				),

				"{$prefix}props.layoutType" => array(
					'default'   => Defaults::get( "{$prefix}props.layoutType" ),
					'transport' => 'refresh', // use refresh transport to properly load the classes
					'control'   => array(
						'label'       => Translations::get( 'layout_type' ),
						'focus_alias' => 'logo',
						'type'        => 'select',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
						'choices'     => array(
							'image' => Translations::escHtml( 'logo_image_only' ),
							'text'  => Translations::escHtml( 'site_title_text_only' ),
						),
					),
				),

				"{$prefix}.pen"             => array(
					'control' => array(
						'type'    => 'pen',
						'section' => "{$prefix}section",
					),
				),
			),
		);
	}

	protected static function doOptionsHooks() {
		Hooks::prefixed_add_action( 'rearrange_customizer_components', array( __CLASS__, 'rearrangeControls' ) );
	}

	public function renderContent( $parameters = array() ) {

		?>
		<div data-kubio-partial-refresh='logo'>
			<?php
			View::partial(
				'front-header',
				'logo',
				array_merge(
					array(
						'component' => $this,
					),
					$parameters
				)
			);
			?>
		</div>
		<?php

	}

	public function printTextLogo() {

		if ( $this->getLayoutType() == 'text' ) {
			echo get_bloginfo( 'name' );
		}
	}
}
