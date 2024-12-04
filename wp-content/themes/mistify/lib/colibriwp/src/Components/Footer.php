<?php


namespace ColibriWP\Theme\Components;

use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Theme;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class Footer extends ComponentBase {

	public static function selectiveRefreshSelector() {
		$footer_class = View::isFrontPage() ? 'footer-front-page' : 'footer-inner-page';

		return ".footer.{$footer_class}";
	}

	protected static function getOptions() {

		return array(
			'settings' => array(),
			'sections' => array(
				'footer' => array(
					'title'    => Translations::get( 'footer_settings' ),
					'priority' => 0,
					'panel'    => 'footer_panel',
					'type'     => 'colibri_section',

				),
			),

			'panels'   => array(
				'footer_panel' => array(
					'priority'       => 3,
					'title'          => Translations::get( 'footer_sections' ),
					'type'           => 'colibri_panel',
					'footer_buttons' => array(
						'change_header' => array(
							'label'   => Translations::get( 'change_footer_design' ),
							'name'    => 'colibriwp_footers_panel',
							'classes' => array( 'colibri-button-large', 'button-primary' ),
							'icon'    => 'dashicons-admin-customizer',
						),
					),
				),
			),
		);

	}

	public function renderContent( $parameters = array() ) {

		Hooks::prefixed_do_action( 'before_footer' );
		$footer_class = View::isFrontPage() ? 'footer-front-page' : 'footer-inner-page';

		?>
		<div class="footer <?php echo $footer_class; ?>" data-kubio-component="footer-parallax">
		  <?php Theme::getInstance()->get( 'front-footer' )->render(); ?>
		</div>
		<?php
	}
}
