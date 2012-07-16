<?php get_header(); ?>

		<?php if (have_posts()) : ?>

 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>

		<div class="pagetitle">
		<?php
			if ( is_category() ) {
				printf( __( 'Archives for category: <span>%1$s</span>', 'wu-wei' ), single_cat_title( '', FALSE ) );
			} elseif ( is_tag() ) {
				printf( __( 'Archives for posts with tag: <span>%1$s</span>', 'wu-wei' ), single_tag_title( '', FALSE ) );
			} elseif ( is_day() ) {
				printf( __( 'Archives for the day of: <span>%1$s</span>', 'wu-wei' ), get_the_date( get_option( 'date_format' ) ) );
			} elseif ( is_month() ) {
				printf( __( 'Archives for the month of: <span>%1$s</span>', 'wu-wei' ), get_the_date( 'F, Y' ) );
			} elseif ( is_year() ) {
				printf( __( 'Archives for the year of: <span>%1$s</span>', 'wu-wei' ), get_the_date( 'Y' ) );
			} elseif (is_author()) {
				_e( 'Archives <span>For author</span>', 'wu-wei' );
			} elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
				_e( 'Blog Archives', 'wu-wei' );
			}
		?>
		</div>

			<div class="navigation">
				<div class="alignleft"><?php next_posts_link( __( '&laquo; Older Entries', 'wu-wei' ) ); ?></div>
				<div class="alignright"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'wu-wei' ) ); ?></div>
				<div class="clearboth"><!-- --></div>
			</div>

		<?php while (have_posts()) : the_post(); ?>

		<div <?php post_class(); ?>>

			<div class="post-info">

				<h1 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf( esc_attr__( 'Permalink to %s', 'wu-wei' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title(); ?></a></h1>
				<div class="timestamp"><?php the_time( get_option( 'date_format' ) ); ?> <!-- by <?php the_author(); ?> --> //</div> <?php if ( comments_open() ) : ?><div class="comment-bubble"><?php comments_popup_link( '0', '1', '%' ); ?></div><?php endif; ?>
				<div class="clearboth"><!-- --></div>

				<?php edit_post_link( __( 'Edit this entry', 'wu-wei' ), '<p>', '</p>' ); ?>

			</div>

			<div class="post-content">
				<?php the_content( __( 'Read the rest of this entry &raquo;', 'wu-wei' ) ); ?>

				<?php wp_link_pages( array('before' => '<p><strong>' . __( 'Pages:', 'wu-wei' ) . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>

			<div class="clearboth"><!-- --></div>
			
			<?php the_tags( '<div class="post-meta-data">' . __( 'Tags', 'wu-wei' ) . ' <span>', ', ', '</span></div>' ); ?>

			<div class="post-meta-data"><?php _e( 'Categories', 'wu-wei' ); ?> <span><?php the_category(', '); ?></span></div>			

		</div>

		<?php endwhile; ?>

			<div class="navigation">
				<div class="alignleft"><?php next_posts_link( __( '&laquo; Older Entries', 'wu-wei' ) ); ?></div>
				<div class="alignright"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'wu-wei' ) ); ?></div>
				<div class="clearboth"><!-- --></div>
			</div>

	<?php else :
		
		if ( is_category() ) { // If this is a category archive
			printf( __( '<h2 class="center">Sorry, but there aren&#8217;t any posts in the %1$s category yet.</h2>', 'wu-wei' ), single_cat_title('',false) );
		} else if ( is_date() ) { // If this is a date archive
			_e( '<h2>Sorry, but there aren&#8217;t any posts with this date.</h2>', 'wu-wei' );
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin( get_query_var('author_name') );
			printf( __( '<h2 class="center">Sorry, but there aren&#8217;t any posts by %1$s yet.</h2>', 'wu-wei' ), $userdata->display_name );
		} else {
			_e( '<h2 class="center">No posts found.</h2>', 'wu-wei' );
		}
		get_search_form();

	endif;
?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
