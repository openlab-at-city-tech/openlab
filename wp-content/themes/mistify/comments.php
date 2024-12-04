<?php
if ( post_password_required() ) :
	return;
endif;
global $kubio_comments_data;

?>

<div id="comments" class="post-comments">
	<h4 class="comments-title">
		<span class="comments-number">
			<?php
			comments_number(
				__( 'No Responses', 'mistify' ),
				__( 'One Response', 'mistify' ),
				__( '% Responses', 'mistify' )
			);
			?>
		</span>
	</h4>

	<ol class="comment-list">
		<?php
		wp_list_comments(
			array(
				'avatar_size' => '40',
				'format'      => 'html5',
			)
		);
		?>
	</ol>

	<?php
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		?>
		<div class="h-row">
			<div class="h-col h-col-auto">
				<div class="prev-posts">
					<?php
					previous_comments_link(
						sprintf(
							'&#xab; %s',
							__(
								'Older Comments',
								'mistify'
							)
						)
					);
					?>
				</div>
			</div>
			<div class="h-col"></div>
			<div class="h-col h-col-auto">
				<div class="next-posts">
					<?php
					next_comments_link(
						sprintf(
							'%s &#xbb;',
							__(
								'Newer Comments',
								'mistify'
							)
						)
					);
					?>
				</div>
			</div>
		</div>
		<?php
	endif;
	?>

	<?php
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'mistify' ); ?></p>
		<?php
	endif;
	?>

</div>
