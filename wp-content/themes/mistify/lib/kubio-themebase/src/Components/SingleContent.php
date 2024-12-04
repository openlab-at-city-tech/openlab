<?php

namespace Kubio\Theme\Components;

use ColibriWP\Theme\View;

class SingleContent extends \ColibriWP\Theme\Components\MainContent {

	public function wrapPostContent( $content ) {
		return "<div class='kubio-post-content entry-content'>{$content}</div>";
	}

	public function renderContent( $parameters = array() ) {

		add_filter( 'the_content', array( $this, 'wrapPostContent' ), 100 );

		?>
		<div id="content">
			<?php
			if ( have_posts() ) :
				View::partial(
					'content',
					'single',
					array(
						'component' => $this,
					)
				);

		else :
			View::partial(
				'content',
				'404',
				array(
					'component' => $this,
				)
			);
		endif;
		?>
		</div> 
		<?php

		remove_filter( 'the_content', array( $this, 'wrapPostContent' ), 100 );
	}

}
