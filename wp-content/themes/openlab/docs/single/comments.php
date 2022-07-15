<?php if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) ) return; ?>
<section id="comments">
<?php
if ( have_comments() ) :
    global $comments_by_type;
    $comments_by_type = &separate_comments( $comments );
    
    if ( ! empty($comments_by_type['comment']) ) :
?>
<section id="comments-list" class="comments">
    <div class="panel panel-default">
        <div class="comments-list-header">
            <h3 class="comments-title"><?php comments_number(); ?></h3>
        </div>
        <ul class="comments-list-body">
            <?php wp_list_comments( 'type=comment&callback=openlab_doc_list_comments_render' ); ?>
        </ul>
    </div>
</section>
    <?php endif;
endif;

if ( comments_open() ) {
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$required_attribute = ( $html5 ? ' required' : ' required="required"' );

    add_filter( 'comment_form_defaults', 'openlab_docs_comment_form' );
	comment_form(
		[
			'comment_field'             => sprintf(
				'<div class="comment-form-comment">%s</div>',
				'<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525"' . $required_attribute . '></textarea>'
			),
            'title_reply'               => sprintf(
                '<p class="comment-reply-text">%s</p> <p class="logged-in-as">%s</p>',
                'Leave a reply',
                sprintf(
					__( 'Logged in as <a href="%1$s" aria-label="%2$s">%3$s</a>. <a href="%4$s">Log out?</a>' ),
					get_edit_user_link(),
					esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.' ), $user_identity ) ),
					$user_identity,
					wp_logout_url( apply_filters( 'the_permalink', get_permalink( get_queried_object_id() ), get_queried_object_id() ) )
				)
            ),
			'logged_in_as'              => '', // Empty. For styling purposes it's moved to title_reply
            'submit_button'             => sprintf(
                '%s <button type="button" class="btn btn-default" id="openlab-cancel-doc-reply">%s</button>',
                '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
                'Cancel'
            )
		]
	);
    remove_filter( 'comment_form_defaults', 'openlab_docs_comment_form' );
}
?>
</section>