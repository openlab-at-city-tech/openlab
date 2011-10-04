<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<?php get_header(); rewind_posts(); ?>
<div class="span-<?php modularity_sidebar_class(); ?>">

		<?php
		query_posts($query_string.'&posts_per_page=24');
		if (have_posts()) : ?>
			
	<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
		
	<h3 class="sub">	
	<?php if ( is_category() ) {
		/* If this is a category archive */
		single_cat_title();
		
	} elseif( is_tag() ) {
		/* If this is a tag archive */ 
		printf( __( 'Posts tagged &ldquo;<span>%1$s</span>&rdquo;', 'modularity' ), single_tag_title( '', false ) );
		
	 } elseif ( is_day() ) {
		/* If this is a daily archive */ 
		printf( __( 'Archive for <span>%1$s</span>', 'modularity' ), get_the_date() );
		
	} elseif (is_month()) {
		/* If this is a monthly archive */
		printf( __( 'Archive for <span>%1$s</span>', 'modularity' ), get_the_date('F, Y') );
	
	} elseif (is_year()) {
		/* If this is a yearly archive */ 
		printf( __( 'Archive for <span>%1$s</span>', 'modularity' ), get_the_date('Y') );
	
	} elseif (is_author()) {
		/* If this is an author archive */ 
		_e( 'Author Archive', 'modularity' );
	
	} elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
		/* If this is a paged archive */ 
		_e( 'Blog Archives', 'modularity' );
	
	} ?>
	</h3>
	
<div class="clear"></div>
<div class="content">
<?php while (have_posts()) : the_post(); ?>
<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink to %s', 'modularity' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title() ?></a></h2>
<?php the_post_thumbnail(); ?>
<?php the_content(); ?>
<div class="clear"></div>
<p class="postmetadata alt quiet">
	<?php
		$tag_list = get_the_tag_list( '| Tags: ', ', ' );
		printf( __( '%1$s | Categories: %2$s %3$s | ', 'modularity' ),
		get_the_time( get_option( 'date_format' ) ),
		get_the_category_list( ', ' ),
		$tag_list
	); ?>
	<?php comments_popup_link( __( 'Leave A Comment &#187;', 'modularity' ), __( '1 Comment &#187;', 'modularity' ), __( '% Comments &#187;', 'modularity' ) ); ?>
	<?php edit_post_link( __( ' Edit', 'modularity'), '| ', '' ); ?>
</p>
</div>
<hr />
<?php endwhile; ?>

<div class="clear"></div>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link( __( '&laquo; Older Entries', 'modularity' ) ); ?></div>
			<div class="alignright"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'modularity' ) ) ?></div>
		</div>
<div class="clear"></div>

	<?php else : ?>

		<h2 class="center"><?php _e( 'Not Found', 'modularity' ); ?></h2>
		<?php get_search_form(); ?>

	<?php endif; ?>
</div>
		</div>
<?php get_sidebar(); ?>

<!-- Begin Footer -->
<?php get_footer(); ?>