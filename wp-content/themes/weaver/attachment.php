<?php
/**
 * The template for displaying attachments.
 */

	get_header();
?>

	<?php // <div id="container" class="single-attachment container-attachment"> really - the one-column stuff makes this work right  ?>
	<div id="container" class="one-column container-attachment">
	<?php weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget'); ?>
	<?php weaver_put_wvr_widgetarea('postpages-widget-area','ttw-top-widget','ttw_hide_special_posts'); ?>
	    <div id="content" role="main">

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<?php if ( ! empty( $post->post_parent ) ) : ?>
		<p class="page-title"><a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php esc_attr( printf( __( 'Return to %s', WEAVER_TRANS ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery"><?php
			/* translators: %s - title of parent post */
		printf( __( '<span class="meta-nav">&larr;</span> %s', WEAVER_TRANS ), get_the_title( $post->post_parent ) );
		?></a></p>
	<?php endif; ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 id="attachment-title" class="entry-title"><?php weaver_post_title(); ?></h2>
			<?php
			$img_info = '';
			if (wp_attachment_is_image() ) {
			    $img_info = ' <span class="meta-sep meta-sep-bar">|</span> ';
			    if (weaver_getopt('ttw_post_icons')) {
				$img_info .= sprintf('&nbsp;&nbsp;<img src="%s/images/icons/fullscreen.png" style="position:relative; top:4px; padding-right:4px;" />',
				get_template_directory_uri());
			    }
			    $metadata = wp_get_attachment_metadata();
			    $msg = '<span class="attachment-size">' . __( 'Full size is %s pixels', WEAVER_TRANS) . '</span>';
			    if (weaver_getopt('ttw_hide_post_fill')) $msg = '<span class="attachment-size">[%s]</span>';
			    $img_info .=  sprintf( $msg,
				sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
				    wp_get_attachment_url(),
				    esc_attr( __('Link to full-size image', WEAVER_TRANS) ),
				    $metadata['width'],
				    $metadata['height'] ) );
			}
			weaver_posted_on('single');
			?>
			<div class="entry-content">
			    <div class="entry-attachment">
			    <?php if ( wp_attachment_is_image() ) :
				$next_attachment_url = wp_get_attachment_url();
			    ?>
				<p class="attachment"><a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
				$attachment_size = apply_filters( 'weaver_attachment_size', 900 );
				echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
				?></a></p>

				<div id="nav-below" class="navigation">
				<div class="nav-previous"><?php previous_image_link( false ); ?></div>
				<div class="nav-next"><?php next_image_link( false ); ?></div>
				</div><!-- #nav-below -->
			    <?php else : ?>
				<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
			    <?php endif; ?>
			    </div><!-- .entry-attachment -->
			<div class="entry-caption"><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?></div>

			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', WEAVER_TRANS ) );
				echo ("<div class=\"clear-cols\"></div>");
				wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', WEAVER_TRANS ), 'after' => '</div>' ) );
			?>
			</div><!-- .entry-content -->

			<div class="entry-utility">
				<?php weaver_posted_in('single'); ?>
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->

<?php if (weaver_getopt('ttw_allow_attachment_comments')) comments_template(); ?>

<?php endwhile; ?>

	    </div><!-- #content -->
	    <?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
	</div><!-- #container -->

<?php get_footer(); ?>
