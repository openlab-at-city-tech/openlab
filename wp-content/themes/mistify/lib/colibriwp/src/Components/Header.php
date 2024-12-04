<?php


namespace ColibriWP\Theme\Components;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Theme;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;
use Exception;

class Header extends ComponentBase {

	protected static function getOptions() {
		$front_page_selector = '.header.header-front-page';

		return array(
			'settings' => array(),

			'sections' => array(
				'top_bar' => array(
					'title'    => Translations::get( 'top_bar_settings' ),
					'priority' => 0,
					'panel'    => 'header_panel',
					'type'     => 'colibri_section',
				),

				'nav_bar' => array(
					'title'    => Translations::get( 'nav_settings' ),
					'priority' => 0,
					'panel'    => 'header_panel',
					'type'     => 'colibri_section',
				),

				'hero'    => array(
					'title'    => Translations::get( 'hero_settings' ),
					'priority' => 0,
					'panel'    => 'header_panel',
					'type'     => 'colibri_section',
				),
			),

			'panels'   => array(
				'header_panel' => array(
					'priority'       => 1,
					'title'          => Translations::get( 'header_sections' ),
					'type'           => 'colibri_panel',
					'footer_buttons' => array(
						'change_header' => array(
							'label'         => Translations::get( 'change_header_design' ),
							'name'          => 'colibriwp_headers_panel',
							'classes'       => array( 'colibri-button-large', 'colibri-button-orange' ),
							'icon'          => 'dashicons-admin-customizer',
							'activate_when' => array(
								'selector' => $front_page_selector,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * @throws Exception
	 */
	public function renderContent( $parameters = array() ) {

		Hooks::prefixed_do_action( 'before_header' );
		$header_class = View::isFrontPage() ? 'header-front-page' : 'header-inner-page';
		View::printSkipToContent();
		?>
		<div class="header <?php echo $header_class; ?>">
		  <?php View::isFrontPage() ? $this->renderFrontPageFragment() : $this->renderInnerPageFragment(); ?>
		</div>
		<script type='text/javascript'>
			(function () {
				// forEach polyfill
				if (!NodeList.prototype.forEach) {
					NodeList.prototype.forEach = function (callback) {
						for (var i = 0; i < this.length; i++) {
							callback.call(this, this.item(i));
						}
					}
				}

				var navigation = document.querySelector('[data-colibri-navigation-overlap="true"], [data-kubio-component="navigation"][data-overlap="true"]');
				if (navigation) {
					var els = document
						.querySelectorAll('.h-navigation-padding');
					if (els.length) {
						els.forEach(function (item) {
							item.style.paddingTop = navigation.offsetHeight + "px";
						});
					}
				}
			})();
		</script>
		<?php
	}


	/**
	 * @throws Exception
	 */
	protected function renderFrontPageFragment() {

		// Theme::getInstance()->get( 'top-bar' )->render();
		Theme::getInstance()->get( 'front-nav-bar' )->render();
		Theme::getInstance()->get( 'front-hero' )->render();
	}

	protected function renderInnerPageFragment() {
		// Theme::getInstance()->get( 'top-bar' )->render();
		Theme::getInstance()->get( 'inner-nav-bar' )->render();
		Theme::getInstance()->get( 'inner-hero' )->render();
	}

	public function getRenderData() {
		return array(
			'mods' => $this->mods(),
		);
	}
}
