<?php

namespace ColibriWP\Theme\Components\Header;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;
use function is_customize_preview;

class TopBar extends ComponentBase {

	protected static $settings_prefix = 'header_front_page.navigation.';
	private $attrs                    = array();

	public function __construct( $attrs = array() ) {
	}

	protected static function getOptions() {
		$prefix  = static::$settings_prefix;
		$section = 'nav_bar';

		return array(
			'settings' => array(

				"{$prefix}props.showTopBar" => array(
					'default'    => Defaults::get( "{$prefix}props.showTopBar" ),
					'control'    => array(
						'label'       => Translations::get( 'show_top_bar' ),
						'type'        => 'switch',
						'section'     => $section,
						'colibri_tab' => 'content',
						'priority'    => 12,
					),
					'css_output' => array(
						array(
							'selector'    => static::selectiveRefreshSelector(),
							'property'    => 'display',
							'true_value'  => 'block',
							'false_value' => 'none',
						),
					),
				),
			),
		);
	}

	public static function selectiveRefreshSelector() {
		return "[data-selective-refresh='" . static::selectiveRefreshKey() . "']";
	}

	public function renderContent( $parameters = array() ) {

		$prefix = static::$settings_prefix;

		if ( ! $this->mod( "{$prefix}props.showTopBar" ) ) {
			if ( ! is_customize_preview() ) {

				return;
			}
		}

		if ( is_customize_preview() ) {
			?>
			<div data-selective-refresh="<?php echo static::selectiveRefreshKey(); ?>">
			<?php $this->makeView(); ?>
			</div>
			<?php
		} else {
			$this->makeView();
		}
	}

	public function makeView() {
		View::partial(
			'front-header',
			'top-bar',
			array(
				'component' => $this,
			)
		);
	}

}
