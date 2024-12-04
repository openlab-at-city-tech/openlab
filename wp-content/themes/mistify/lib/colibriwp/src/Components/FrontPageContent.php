<?php


namespace ColibriWP\Theme\Components;

class FrontPageContent extends PageContent {

	public function renderContent( $parameters = array() ) {
		?>
		<div class="page-content">
		  <?php
			while ( have_posts() ) :
				the_post();
				?>
			<div id="content"  class="content">
				<?php
				the_content();
				endwhile;
			?>
			</div>
			<?php
			get_template_part( 'comments-page' );
			?>
		</div>
		  <?php

	}

}
