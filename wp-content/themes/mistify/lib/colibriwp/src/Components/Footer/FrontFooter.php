<?php


namespace ColibriWP\Theme\Components\Footer;

use ColibriWP\Theme\AssetsManager;
use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;


class FrontFooter extends ComponentBase {

	protected static $settings_prefix = 'footer_post.footer.';
	protected static $selector        = '.page-footer';

	protected $background_component = null;

	public static function selectiveRefreshSelector() {
		return Defaults::get( static::$settings_prefix . 'selective_selector', false );
	}

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		$prefix = static::$settings_prefix;

		return array(
			'sections' => array(
				"{$prefix}section" => array(
					'title'  => Translations::get( 'title' ),
					'panel'  => 'footer_panel',
					'type'   => 'colibri_section',
					'hidden' => true,
				),
			),

			'settings' => array(

				"{$prefix}pen"                     => array(
					'control' => array(
						'type'    => 'pen',
						'section' => 'footer',
					),

				),

				"{$prefix}props.useFooterParallax" => array(
					'default'   => Defaults::get( "{$prefix}props.useFooterParallax" ),
					'transport' => 'refresh',
					'control'   => array(
						'focus_alias' => 'footer',
						'label'       => Translations::get( 'footer_parallax' ),
						'type'        => 'switch',
						'show_toggle' => true,
						'section'     => 'footer',
						'colibri_tab' => 'content',
					),
					'js_output' => array(

						array(
							'selector' => '.page-footer',
							'action'   => 'colibri-set-attr',
							'value'    => 'data-enabled',
						),

						array(
							'selector' => '.page-footer',
							'action'   => 'colibri-component-toggle',
							'value'    => 'footerParallax',
							'delay'    => 30,
						),

					),
				),
			),
		);
	}

	public function printParalaxJsToggle() {
		$prefix   = static::$settings_prefix;
		$parallax = $this->mod( "{$prefix}props.useFooterParallax", false );
		if ( $parallax === false || $parallax === '' ) {
			AssetsManager::addInlineScriptCallback(
				'kubio-theme',
				function () {
					?>
				<script type="text/javascript">
					jQuery(window).load(function () {
						var el = jQuery(".page-footer");
						var component = el.data()['fn.kubio.footerParallax'];
						if (component) {
							el.attr('data-enabled', 'false');
							component.stop();
						}
					});
				</script>
					<?php
				}
			);
		}

	}

	public function renderContent( $parameters = array() ) {
		View::partial(
			'front-footer',
			'footer',
			array(
				'component' => $this,
			)
		);
	}
}
