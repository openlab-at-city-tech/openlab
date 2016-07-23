<?php
/**
 * Displays a single attachment.
 *
 * @package P2
 */
?>
<?php get_header(); ?>

<div class="sleeve_main">

	<div id="main">

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>

		<div class="controls">
			<a href="#" id="togglecomments"><?php _e( 'Hide threads', 'p2' ); ?></a>
			<span class="sep">&nbsp;|&nbsp;</span>
			<a href="#directions" id="directions-keyboard"><?php  _e( 'Keyboard Shortcuts', 'p2' ); ?></a>
			<span class="single-action-links"><?php do_action( 'p2_action_links' ); ?></span>
		</div>

		<ul id="postlist">
			<li id="prologue-<?php the_ID(); ?>" <?php post_class(); ?>>

				<h2><a href="<?php echo get_permalink( $post->post_parent ); ?>" rev="attachment"><?php echo get_the_title( $post->post_parent ); ?></a> &raquo; <?php the_title(); ?></h2>
				<?php
				$author_posts_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
				$posts_by_title   = sprintf(
					__( 'Posts by %1$s ( @%2$s )', 'p2' ),
					get_the_author_meta( 'display_name' ),
					get_the_author_meta( 'user_nicename' )
				); ?>

				<a href="<?php echo esc_attr( $author_posts_url ); ?>" title="<?php echo esc_attr( $posts_by_title ); ?>" class="post-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), 48 ); ?>
				</a>
				<h4>
					<a href="<?php echo esc_attr( $author_posts_url ); ?>" title="<?php echo esc_attr( $posts_by_title ); ?>"><?php the_author(); ?></a>
					<span class="meta">
						<?php echo p2_date_time_with_microformat(); ?>
						<?php
							$metadata = wp_get_attachment_metadata();
							if ( wp_attachment_is_image() ) {
								echo " | "; // Pipe separator.
								printf( __( 'Full size is %s pixels', 'p2' ),
								sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
									wp_get_attachment_url(),
									esc_attr( __( 'Link to full-size image', 'p2' ) ),
										$metadata['width'],
										$metadata['height']
									)
								);
							}
						?>
						<span class="actions">
							<?php
							if ( comments_open() && ! post_password_required() ) {
									echo post_reply_link( array(
										'before'        => isset( $before_reply_link ) ? $before_reply_link : '',
										'after'         => '',
										'reply_text'    => __( 'Reply', 'p2' ),
										'add_below'     => 'comments'
									), get_the_ID() );
							}

							if ( current_user_can( 'edit_post', get_the_ID() ) ) : ?> | <a href="<?php echo ( get_edit_post_link( get_the_ID() ) ); ?>" rel="<?php the_ID(); ?>" title="<?php esc_attr_e( 'Edit', 'p2' ); ?>"><?php _e( 'Edit', 'p2' ); ?></a>
							<?php endif; ?>

							<?php do_action( 'p2_action_links' ); ?>
						</span>
					</span>
				</h4>

				<div id="content-<?php the_ID(); ?>" class="postcontent">
				<?php if ( wp_attachment_is_image() ) :
					$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
					foreach ( $attachments as $k => $attachment ) {
						if ( $attachment->ID == $post->ID )
							break;
					}
					$k++;
					// If there is more than 1 image attachment in a gallery
					if ( count( $attachments ) > 1 ) {
						if ( isset( $attachments[ $k ] ) )
							$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
						else
							$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
					} else {
						$next_attachment_url = wp_get_attachment_url();
					}
				?>

					<div class="attachment-image">
						<p><a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo wp_get_attachment_image( $post->ID, array( $content_width - 8, 700 ) ); ?></a></p>
						<div class="caption"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); ?></div>
						<div class="image-description"><?php if ( !empty($post->post_content) ) the_content(); ?></div>
					</div>

				<?php else : ?>

					<p><?php _e( 'View file:', 'p2' ); ?> <a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a></p>
					<?php the_content( __( '(More ...)' , 'p2' ) ); ?>
					<?php wp_link_pages(); ?>

				<?php endif; ?>
				</div><!-- /.postcontent -->

				<?php
					$comment_field = '<div class="form"><textarea id="comment" class="expand50-100" name="comment" cols="45" rows="3"></textarea></div> <label class="post-error" for="comment" id="commenttext_error"></label>';
					$comment_notes_before = '<p class="comment-notes">' . ( get_option( 'require_name_email' ) ? sprintf( ' ' . __( 'Required fields are marked %s', 'p2' ), '<span class="required">*</span>' ) : '' ) . '</p>';
					$p2_comment_args = array(
						'title_reply'           => __( 'Reply', 'p2' ),
						'comment_field'         => $comment_field,
						'comment_notes_before'  => $comment_notes_before,
						'comment_notes_after'   => '<span class="progress spinner-comment-new"></span>',
						'label_submit'          => __( 'Reply', 'p2' ),
						'id_submit'             => 'comment-submit',
				);
				?>

				<?php if ( get_comments_number() > 0 && ! post_password_required() ) : ?>
					<div class="discussion" style="display: none">
						<p>
							<?php p2_discussion_links(); ?>
							<a href="#" class="show-comments"><?php _e( 'Toggle Comments', 'p2' ); ?></a>
						</p>
					</div>
				<?php endif; ?>

				<div class="bottom-of-entry">&nbsp;</div>

				<?php if ( p2_is_ajax_request() ) : ?>
					<ul id="comments-<?php the_ID(); ?>" class="commentlist inlinecomments"></ul>
				<?php else :
					comments_template();
					$pc = 0;
					if ( p2_show_comment_form() && $pc == 0 && ! post_password_required() ) :
						$pc++; ?>
						<div class="respond-wrap" <?php echo ( ! is_singular() ) ? 'style="display: none; "' : ''; ?>>
							<?php comment_form( $p2_comment_args ); ?>
						</div><?php
					endif;
				endif; ?>
			</li>
		</ul>

		<?php endwhile; ?>
	<?php endif; ?>

		<div class="navigation attachment">
			<div class="alignleft"><?php previous_image_link(); ?></div>
			<div class="alignright"><?php next_image_link(); ?></div>
		</div>

	</div> <!-- #main -->

</div> <!-- sleeve -->

<?php get_footer(); ?>
