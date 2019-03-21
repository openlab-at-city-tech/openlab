<?php
    $commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );

    $comment_form_args = array(
        'title_reply' => '',
        'label_submit' => __typology( 'comment_submit' ),
        'cancel_reply_link' => __typology( 'comment_cancel_reply' ),
        'comment_notes_before' => '',
        'comment_notes_after' => '',
        'comment_field' =>  '<p class="comment-form-comment"><label for="comment">' . __typology( 'comment_text' ) .'</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">' .'</textarea></p>'
    );

    comment_form( $comment_form_args );

?>