<?php 
if ( is_page() || is_page_template() ) {
	
	$related_pages_position 		= esc_attr(get_theme_mod( 'theme-dynamic-menu-position', 0 ));

	if ( $related_pages_position == 1 ) {
		get_template_part('related-pages');
	}

}

if (is_active_sidebar('sidebar')) {
	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar') ) : ?> <?php endif;
} ?>