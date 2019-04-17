<?php if ( post_password_required() ) { return; } ?>

<?php if ( comments_open() || get_comments_number() ) : ?>
    
    <div class="johannes-comments section-margin single-md-content">

        <div class="johannes-comment-form">
            <?php

                comment_form( array(
            	    'title_reply_before' => '<h5 id="reply-title" class="h2">',
            	    'title_reply'        => __johannes( 'leave_a_reply' ),
                    'label_submit' => __johannes( 'comment_submit' ),
                    'cancel_reply_link' => __johannes( 'comment_cancel_reply' ),
            	    'title_reply_after'  => '</h5>',
                    'comment_notes_before' => '',
                    'comment_notes_after' => '',
                    'comment_field' =>  '<p class="comment-form-comment"><label for="comment">' . __johannes( 'comment_text' ) .'</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">' .'</textarea></p>',
                ) );
            ?>
        </div>

        <?php if ( have_comments() ) : ?>
            <h5 id="comments" class="h2">
                <?php comments_number( __johannes( 'no_comments' ), __johannes( 'one_comment' ), __johannes( 'multiple_comments' ) ); ?>
            </h5>

            <ul class="comment-list">
                <?php $args = array(
                    'avatar_size' => 60,
                    'reply_text' => __johannes( 'comment_reply' ),
                    'format' => 'html5'
                ); ?>
                <?php wp_list_comments( $args ); ?>
            </ul>

            <?php paginate_comments_links( array(  'prev_text' => '<i class="johannes-icon johannes-icon-left"></i>', 'next_text' => '<i class="johannes-icon johannes-icon-right"></i>', 'type' => 'list' ) ); ?>
        <?php endif; ?>

    </div>

<?php endif; ?>