<?php


namespace Kubio\Theme\Components\FrontHeader;

use ColibriWP\Theme\Components\FrontHeader\ButtonsGroup;
use ColibriWP\Theme\View;

class Buttons extends ButtonsGroup {

	protected static $settings_prefix = 'front-header.buttons.';

	public static function selectiveRefreshSelector() {
		return "[data-kubio-partial-refresh='buttons']";
	}

	public function renderContent( $parameters = array() ) {

		if ( $this->mod( static::$settings_prefix . 'show', true ) ) {  ?>
	<div data-kubio-partial-refresh='buttons'>
				<?php
				View::partial(
					'front-header',
					'buttons',
					array(
						'component' => $this,
					)
				);
				?>
	  </div>
			<?php
		}
	}

	public function renderTemplate() {

	}
}
