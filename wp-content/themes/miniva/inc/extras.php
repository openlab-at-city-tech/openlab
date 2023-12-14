<?php
/**
 * Miniva extra functions and definitions
 *
 * @package Miniva
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function miniva_after_setup() {
	/**
	 * Add custom image sizes
	 */
	set_post_thumbnail_size( 640, 360, true );
	add_image_size( 'miniva-small', 250, 250, true );
	add_image_size( 'miniva-medium', 500, 310, true );
	add_image_size( 'miniva-large', 1040, 500, true );
	add_image_size( 'miniva-large-nocrop', 1040 );
	add_image_size( 'miniva-post-nocrop', 640 );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	add_theme_support( 'align-wide' );

	add_theme_support( 'responsive-embeds' );

	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Red', 'miniva' ),
				'slug'  => 'red',
				'color' => '#e2574c',
			),
			array(
				'name'  => __( 'Orange', 'miniva' ),
				'slug'  => 'orange',
				'color' => '#ffbc49',
			),
			array(
				'name'  => __( 'Green', 'miniva' ),
				'slug'  => 'green',
				'color' => '#00d084',
			),
			array(
				'name'  => __( 'Blue', 'miniva' ),
				'slug'  => 'blue',
				'color' => '#0693e3',
			),
			array(
				'name'  => __( 'Black', 'miniva' ),
				'slug'  => 'black',
				'color' => '#272727',
			),
			array(
				'name'  => __( 'Gray', 'miniva' ),
				'slug'  => 'gray',
				'color' => '#767676',
			),
			array(
				'name'  => __( 'Light Gray', 'miniva' ),
				'slug'  => 'light-gray',
				'color' => '#eee',
			),
		)
	);
}
add_action( 'after_setup_theme', 'miniva_after_setup' );

/**
 * Enqueue extra script.
 */
function miniva_enqueue_scripts() {
	if ( miniva_is_amp() ) {
		return;
	}

	wp_register_script( 'miniva-functions', get_template_directory_uri() . '/js/functions.js', array(), MINIVA_VERSION, true );
	wp_localize_script(
		'miniva-functions',
		'miniva',
		array(
			'fluidvids'     => apply_filters( 'miniva_fluidvids', true ),
			'expand_text'   => esc_html__( 'expand sub menu', 'miniva' ),
			'collapse_text' => esc_html__( 'collapse sub menu', 'miniva' ),
		)
	);
	wp_enqueue_script( 'miniva-functions' );
}
add_action( 'wp_enqueue_scripts', 'miniva_enqueue_scripts' );

/**
 * Register extra widget areas.
 */
function miniva_extra_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Widgetized Page', 'miniva' ),
			'id'            => 'widgetized-page',
			'description'   => esc_html__( 'This widget area is located below the main content in widgetized page template.', 'miniva' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	$footer_col = miniva_get_footer_col();
	for ( $i = 1; $i <= $footer_col; $i++ ) {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer', 'miniva' ) . ' ' . $i,
				'id'            => 'footer-' . $i,
				'description'   => esc_html__( 'Footer widget area', 'miniva' ) . ' #' . $i,
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'miniva_extra_widgets_init' );

/**
 * Modify the excerpt length
 *
 * @param  string $length Number of words.
 * @return integer
 */
function miniva_excerpt_length( $length ) {
	$excerpt_length = absint( get_theme_mod( 'excerpt_length', 20 ) );
	if ( empty( $excerpt_length ) ) {
		return 20;
	}
	return $excerpt_length;
}
add_filter( 'excerpt_length', 'miniva_excerpt_length' );

/**
 * Change the [...] string in the excerpt
 *
 * @param  string $more more string.
 * @return string
 */
function miniva_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'miniva_excerpt_more' );

/**
 * Modifies tag cloud widget arguments to have all tags in the widget same font size.
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array A new modified arguments.
 */
function miniva_widget_tag_cloud_args( $args ) {
	$args['largest']  = 0.8;
	$args['smallest'] = 0.8;
	$args['unit']     = 'em';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'miniva_widget_tag_cloud_args' );

/**
 * Container open.
 *
 * @param string $class container class.
 * @param string $id    container id.
 */
function miniva_container_open( $class = '', $id = '' ) {
	if ( empty( $class ) ) {
		$class = 'container';
	}
	echo '<div class="' . esc_attr( $class ) . '"';
	if ( ! empty( $id ) ) {
		echo ' id="' . esc_attr( $id ) . '"';
	}
	echo '>';
}

/**
 * Container close.
 */
function miniva_container_close() {
	echo '</div>';
}

/**
 * Modify the search form to include a search icon
 *
 * @return string search form
 */
function miniva_search_form() {
	$form = '<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
		<label>
			<svg aria-hidden="true" width="16" height="16" class="icon"><use xlink:href="#search" /></svg>
			<span class="screen-reader-text">' . _x( 'Search for:', 'label', 'miniva' ) . '</span>
			<input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search &hellip;', 'placeholder', 'miniva' ) . '" value="' . get_search_query() . '" name="s">
		</label>
		<button type="submit" class="search-submit">
			<svg aria-hidden="true" width="16" height="16" class="icon"><use xlink:href="#search" /></svg>
			<span class="screen-reader-text">' . esc_html_x( 'Search', 'submit button', 'miniva' ) . '</span>
		</button>
	</form>';
	return $form;
}
add_filter( 'get_search_form', 'miniva_search_form' );

/**
 * Display breadcrumb navigation with Breadcrumb NavXT
 */
function miniva_breadcrumb() {
	if ( ! function_exists( 'bcn_display' ) ) {
		return;
	}
	if ( is_front_page() ) {
		return;
	}
	?>
	<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
		<?php bcn_display(); ?>
	</div>
	<?php
}
add_action( 'miniva_content_start', 'miniva_breadcrumb' );

/**
 * Insert welcome text.
 */
function miniva_welcome_text() {
	if ( ! is_front_page() ) {
		return;
	}
	$welcome_text = get_theme_mod( 'welcome_text' );
	if ( trim( $welcome_text ) === '' ) {
		return;
	}
	miniva_container_open( 'welcome-text' );
	miniva_container_open( 'container' );
	echo nl2br( esc_html( $welcome_text ) );
	miniva_container_close();
	miniva_container_close();
}
add_action( 'miniva_header_after', 'miniva_welcome_text', 11 );

/**
 * Insert footer site info.
 */
function miniva_site_info() {
	echo esc_html__( 'Powered by', 'miniva' );
	?>
	<a href="<?php echo esc_url( __( 'https://tajam.id/miniva/', 'miniva' ) ); ?>">
		<?php esc_html_e( 'Miniva WordPress Theme', 'miniva' ); ?>
	</a>
	<?php
}
add_action( 'miniva_site_info', 'miniva_site_info' );

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 */
function miniva_javascript_detection() {
	if ( miniva_is_amp() ) {
		return;
	}
	echo "<script>(function(H){H.className = H.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'miniva_javascript_detection', 0 );
