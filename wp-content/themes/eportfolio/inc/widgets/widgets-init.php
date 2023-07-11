<?php 
/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function eportfolio_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Portfolio Template Widget Section', 'eportfolio' ),
		'id'            => 'portfolio-template-sidebar',
		'description'   => esc_html__( 'Add widgets here.', 'eportfolio' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Single Page/Post Sidebar', 'eportfolio' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'eportfolio' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

}
add_action( 'widgets_init', 'eportfolio_widgets_init' );

require get_template_directory() . '/inc/widgets/widget-base-class.php';
require get_template_directory() . '/inc/widgets/widgets.php';


