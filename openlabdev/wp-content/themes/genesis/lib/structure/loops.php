<?php
/**
 * Adds loop structures.
 *
 * @package Genesis
 */

add_action('genesis_loop', 'genesis_do_loop');
/**
 * Hook loops to the genesis_loop output hook so we can get
 * some front-end output. Pretty basic stuff.
 *
 * @since 1.1
 */
function genesis_do_loop() {

	if ( is_page_template('page_blog.php') ) {
		$include = genesis_get_option('blog_cat');
		$exclude = genesis_get_option('blog_cat_exclude') ? explode(',', str_replace(' ', '', genesis_get_option('blog_cat_exclude'))) : '';
		$paged = get_query_var('paged') ? get_query_var('paged') : 1;

		$cf = genesis_get_custom_field('query_args'); /** Easter Egg **/
		$args = array('cat' => $include, 'category__not_in' => $exclude, 'showposts' => genesis_get_option('blog_cat_num'), 'paged' => $paged);
		$query_args = wp_parse_args($cf, $args);

		genesis_custom_loop( $query_args );
	}
	else {
		genesis_standard_loop();
	}

}

/**
 * This is a standard loop, and is meant to be executed, without
 * modification, in most circumstances where content needs to be displayed.
 *
 * It outputs basic wrapping HTML, but uses hooks to do most of its
 * content output like Title, Content, Post information, and Comments.
 *
 * @since 1.1
 */
function genesis_standard_loop() {
	global $loop_counter;
	$loop_counter = 0;

	if ( have_posts() ) : while ( have_posts() ) : the_post(); // the loop

	do_action( 'genesis_before_post' );
?>
	<div <?php post_class(); ?>>

		<?php do_action( 'genesis_before_post_title' ); ?>
		<?php do_action( 'genesis_post_title' ); ?>
		<?php do_action( 'genesis_after_post_title' ); ?>

		<?php do_action( 'genesis_before_post_content' ); ?>
		<div class="entry-content">
			<?php do_action( 'genesis_post_content' ); ?>
		</div><!-- end .entry-content -->
		<?php do_action( 'genesis_after_post_content' ); ?>

	</div><!-- end .postclass -->
<?php

	do_action( 'genesis_after_post' );
	$loop_counter++;

	endwhile; /** end of one post **/
	do_action( 'genesis_after_endwhile' );

	else : /** if no posts exist **/
	do_action( 'genesis_loop_else' );
	endif; /** end loop **/
}

/**
 * This is a custom loop function, and is meant to be executed when a
 * custom query is needed. It accepts arguments in query_posts style
 * format to modify the custom WP_Query object.
 *
 * It outputs basic wrapping HTML, but uses hooks to do most of its
 * content output like Title, Content, Post information, and Comments.
 *
 * @since 1.1
 */
function genesis_custom_loop( $args = array() ) {
	global $wp_query, $more, $loop_counter;
	$loop_counter = 0;

	$defaults = array(); /** For forward compatibility **/
	$args = apply_filters('genesis_custom_loop_args', wp_parse_args($args, $defaults), $args, $defaults);

	/** save the original query **/
	$orig_query = $wp_query;

	$wp_query = new WP_Query($args);
	if ( $wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post();
	$more = 0;

	do_action( 'genesis_before_post' );
?>
	<div <?php post_class(); ?>>

		<?php do_action( 'genesis_before_post_title' ); ?>
		<?php do_action( 'genesis_post_title' ); ?>
		<?php do_action( 'genesis_after_post_title' ); ?>

		<?php do_action( 'genesis_before_post_content' ); ?>
		<div class="entry-content">
			<?php do_action( 'genesis_post_content' ); ?>
		</div><!-- end .entry-content -->
		<?php do_action( 'genesis_after_post_content' ); ?>

	</div><!-- end .postclass -->
	<?php

	do_action( 'genesis_after_post' );
	$loop_counter++;

	endwhile; /** end of one post **/
	do_action( 'genesis_after_endwhile' );

	else : /** if no posts exist **/
	do_action( 'genesis_loop_else' );
	endif; /** end loop **/

	/** restore original query **/
	$wp_query = $orig_query; wp_reset_query();
}

/**
 * Yet another custom loop function.
 * Outputs markup compatible with a Feature + Grid style layout.
 * All normal loop hooks present, except for genesis_post_content.
 *
 * @since 1.5
 */
function genesis_grid_loop( $args = array() ) {

	/** Global vars */
	global $_genesis_loop_args, $query_string;

	/** Parse args */
	$args = apply_filters( 'genesis_grid_loop_args', wp_parse_args( $args, array(
		'loop'					=> 'standard',
		'features'				=> 2,
		'features_on_all'		=> false,
		'feature_image_size'	=> 0,
		'feature_image_class'	=> 'alignleft post-image',
		'feature_content_limit'	=> 0,
		'grid_image_size'		=> 'thumbnail',
		'grid_image_class'		=> 'alignleft post-image',
		'grid_content_limit'	=> 0,
		'more'					=> g_ent( __('Read more&hellip;', 'genesis') ),
		'posts_per_page'		=> get_option('posts_per_page'),
		'paged'					=> get_query_var('paged') ? get_query_var('paged') : 1
	) ) );

	/** Error handler */
	if ( $args['posts_per_page'] < $args['features'] ) {
		trigger_error( sprintf( __( 'You are using invalid arguments with the %s function.', 'genesis' ), __FUNCTION__ ) );
		return;
	}

	/** Potentially remove features on page 2+ */
	if ( ! $args['features_on_all'] && $args['paged'] > 1 )
		$args['features'] = 0;

	/** Set global loop args */
	$_genesis_loop_args = wp_parse_args( $args, $query_string );

	/** Remove some unnecessary stuff from the grid loop */
	remove_action( 'genesis_before_post_title', 'genesis_do_post_format_image' );
	remove_action( 'genesis_post_content', 'genesis_do_post_image' );
	remove_action( 'genesis_post_content', 'genesis_do_post_content' );

	/** Custom loop output */
	add_filter( 'post_class', 'genesis_grid_loop_post_class' );
	add_action( 'genesis_post_content', 'genesis_grid_loop_content' );

	/** The loop */
	if ( $_genesis_loop_args['loop'] == 'custom' ) {
		genesis_custom_loop( $_genesis_loop_args );
	} else {
		query_posts( $_genesis_loop_args );
		genesis_standard_loop();
	}

	/** Reset loops */
	genesis_reset_loops();
	remove_filter( 'post_class', 'genesis_grid_loop_post_class' );
	remove_action( 'genesis_post_content', 'genesis_grid_loop_content' );

}

/**
 * This function filter the post class array to output custom classes
 * for the feature/grid layout, based on the grid loop args and the loop counter.
 *
 * @since 1.5
 */
function genesis_grid_loop_post_class( $classes ) {

	global $_genesis_loop_args, $loop_counter;

	$grid_classes = array();

	if ( $_genesis_loop_args['features'] && $loop_counter < $_genesis_loop_args['features'] ) {
		$grid_classes[] = 'genesis-feature';
		$grid_classes[] = sprintf( 'genesis-feature-%s', $loop_counter + 1 );
		$grid_classes[] = $loop_counter&1 ? 'genesis-feature-even' : 'genesis-feature-odd';
	}
	elseif ( $_genesis_loop_args['features']&1 ) {
		$grid_classes[] = 'genesis-grid';
		$grid_classes[] = sprintf( 'genesis-grid-%s', $loop_counter - $_genesis_loop_args['features'] + 1 );
		$grid_classes[] = $loop_counter&1 ? 'genesis-grid-odd' : 'genesis-grid-even';
	}
	else {
		$grid_classes[] = 'genesis-grid';
		$grid_classes[] = sprintf( 'genesis-grid-%s', $loop_counter - $_genesis_loop_args['features'] + 1 );
		$grid_classes[] = $loop_counter&1 ? 'genesis-grid-even' : 'genesis-grid-odd';
	}

	return array_merge( $classes, apply_filters( 'genesis_grid_loop_post_class', $grid_classes ) );

}

/**
 * This function outputs specially formatted content, based on the grid loop args.
 *
 * @since 1.5
 */
function genesis_grid_loop_content() {

	global $_genesis_loop_args;

	if ( in_array( 'genesis-feature', get_post_class() ) ) {
		if ( $_genesis_loop_args['feature_image_size'] ) {
			printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $_genesis_loop_args['feature_image_size'], 'attr' => array( 'class' => esc_attr( $_genesis_loop_args['feature_image_class'] ) ) ) ) );
		}

		if ( $_genesis_loop_args['feature_content_limit'] ) {
			the_content_limit( (int)$_genesis_loop_args['feature_content_limit'], esc_html( $_genesis_loop_args['more'] ) );
		}
		else {
			the_content( esc_html( $_genesis_loop_args['more'] ) );
		}
	}
	else {
		if ( $_genesis_loop_args['grid_image_size'] ) {
			printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $_genesis_loop_args['grid_image_size'], 'attr' => array( 'class' => esc_attr( $_genesis_loop_args['grid_image_class'] ) ) ) ) );
		}

		if ( $_genesis_loop_args['grid_content_limit'] ) {
			the_content_limit( (int)$_genesis_loop_args['grid_content_limit'], esc_html( $_genesis_loop_args['more'] ) );
		}
		else {
			the_excerpt();
			printf( '<a href="%s" class="more-link">%s</a>', get_permalink(), esc_html( $_genesis_loop_args['more'] ) );
		}
	}

}