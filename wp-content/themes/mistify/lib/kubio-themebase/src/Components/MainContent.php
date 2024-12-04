<?php

namespace Kubio\Theme\Components;

use ColibriWP\Theme\View;

class MainContent extends \ColibriWP\Theme\Components\MainContent {
	public function renderContent( $parameters = array() ) {    ?>
		<div id="content">
			<?php
			if ( have_posts() ) {
				View::partial(
					'content',
					'index',
					array(
						'component' => $this,
					)
				);
			} else {
				View::partial(
					'content',
					'404',
					array(
						'component' => $this,
					)
				);
			}

			$this->doMasonry();
			?>
		</div> 
		<?php
	}

	public function doMasonry() {
		$value = $this->mod( 'blog_enable_masonry', false );
		if ( $value ) {
			wp_enqueue_script( 'jquery-masonry' );
		} else {
			?>
			<script>
				(function () {
					document.querySelector('.wp-block-kubio-query-loop').setAttribute('data-kubio-settings', '{}')
				})();
			</script>
			<?php
		}
	}
}
