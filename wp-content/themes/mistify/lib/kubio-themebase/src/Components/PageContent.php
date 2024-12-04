<?php


namespace Kubio\Theme\Components;

use ColibriWP\Theme\View;

class PageContent extends \ColibriWP\Theme\Components\PageContent {

	public function wrapPostContent( $content ) {
		return "<div class='kubio-post-content entry-content'>{$content}</div>";
	}

	public function renderContent( $parameters = array() ) {

		add_filter( 'the_content', array( $this, 'wrapPostContent' ), 100 );

		?>
			<div id="content">
			<?php
			while ( have_posts() ) :
				the_post();
				View::partial( 'content', 'page' );
			endwhile;
			?>
			</div> 
		<?php

		remove_filter( 'the_content', array( $this, 'wrapPostContent' ), 100 );
	}

}
