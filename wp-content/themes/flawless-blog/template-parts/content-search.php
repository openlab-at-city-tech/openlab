<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Flawless Blog
 */

$archive_category = get_theme_mod( 'flawless_blog_enable_archive_category', true );
$archive_author   = get_theme_mod( 'flawless_blog_enable_archive_author', true );
$archive_date     = get_theme_mod( 'flawless_blog_enable_archive_date', true );

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-item post-list">
		<div class="post-item-image">
			<?php flawless_blog_post_thumbnail(); ?>
			<div class="read-time-comment">
				<span class="reading-time">
					<i class="far fa-clock"></i>
					<?php
					echo flawless_blog_time_interval( get_the_content() );
					echo esc_html__( ' min read', 'flawless-blog' );
					?>
				</span>
				<span class="comment">
					<i class="far fa-comment"></i>
					<?php echo absint( get_comments_number( get_the_ID() ) ); ?>
				</span>
			</div>
		</div>
		<div class="post-item-content">
			<?php if ( $archive_category === true ) : ?>
				<div class="entry-cat">
					<?php the_category( '', '', get_the_ID() ); ?>
				</div>
			<?php endif; ?>
			<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif;
			if ( 'post' === get_post_type() ) :
				?>
				<ul class="entry-meta">
					<?php if ( $archive_author === true ) : ?>
						<li class="post-author"> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><i class="far fa-user"></i><?php echo esc_html( get_the_author() ); ?></a></li>
					<?php endif; ?>
					<?php if ( $archive_date === true ) : ?>
						<li class="post-date"><i class="far fa-calendar-alt"></i></span><?php echo esc_html( get_the_date() ); ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
			<div class="post-content">
				<?php echo esc_html( wp_trim_words( get_the_excerpt(), get_theme_mod( 'flawless_blog_excerpt_length', 30 ) ) ); ?>
			</div><!-- post-content -->
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
