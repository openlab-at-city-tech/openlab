<?php

add_filter('genesis_pre_get_option_site_layout', 'cuny_404_layout');
function cuny_404_layout($opt) {
    $opt = 'full-width-content';
    return $opt;
}

remove_action( 'genesis_loop', 'genesis_do_loop' );

add_action( 'genesis_loop', 'cuny_404' );

function cuny_404() { ?>

	<div class="post hentry">

		<h1 class="entry-title">Page Not Found</h1>
		<div class="entry-content">
			<p>The page you requested could not be found. Please use the menu above to find the page you need.</p>

		</div><!-- end .entry-content -->

	</div><!-- end .postclass -->

<?php
}

genesis();