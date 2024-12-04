<?php

namespace Kubio\Theme\Components;

use ColibriWP\Theme\View;

class PageNotFound extends \ColibriWP\Theme\Components\PageNotFound {

	public function renderContent( $parameters = array() ) {    ?>
		<div id="content">
			<?php
			View::partial(
				'content',
				'404',
				array(
					'component' => $this,
				)
			);
			?>
		</div> 
		<?php
	}
}
