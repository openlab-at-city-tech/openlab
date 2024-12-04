<?php


namespace Kubio\Theme\Components\FrontHeader;

use ColibriWP\Theme\View;

class TopBarSocialIcons extends \ColibriWP\Theme\Components\FrontHeader\TopBarSocialIcons {

	static $settings_prefix = 'front-header.social_icons.';



	// temporary because the kubio's social icons block returns some weird style classes
	public static function selectiveRefreshSelector() {
		return "[data-kubio-partial-refresh='top-bar-social-icons']";
	}

	public function renderTemplate() {
		View::partial(
			'front-header',
			'top-bar/social-icons',
			array(
				'component' => $this,
			)
		);

	}

	public function renderContent( $parameters = array() ) {
		?>
		<div data-kubio-partial-refresh='top-bar-social-icons'>
			<?php
			$this->renderTemplate();
			?>
		</div>
		<?php
	}
}
