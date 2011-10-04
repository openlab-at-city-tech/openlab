<?php
	/* Comment form arguments and settings.
	----------------------------------------*/

	add_filter( 'comment_form_defaults', 'woo_comment_form_args' );

	function woo_comment_form_args( $args ) {
	
		// Get the current commenter's data.
		$commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );
		$required_text = sprintf( ' ' . __('Required fields are marked %s'), '<span class="required">*</span>' );
		$post_id = get_the_ID();
		
		// Get the display name of the current user, if there is one logged in.
		$user_identity = '';
		
		if ( is_user_logged_in() ) {
			global $current_user;
			$user_identity = $current_user->display_name;
		}
		
		$commenter_data_defaults = array(
										'comment_author' => '', 
										'comment_author_email' => '', 
										'comment_author_url' => ''
									    );
		
		$commenter_data = array();
		if ( array_key_exists( 'comment_author', $commenter ) ) { $commenter_data['comment_author'] = $commenter['comment_author']; } // End IF Statement
		if ( array_key_exists( 'comment_author_email', $commenter ) ) { $commenter_data['comment_author_email'] = $commenter['comment_author_email']; } // End IF Statement
		if ( array_key_exists( 'comment_author_url', $commenter ) ) { $commenter_data['comment_author_url'] = $commenter['comment_author_url']; } // End IF Statement
		
		// Make sure our defaults are loaded if no data is set.
		
		$commenter_data = array_merge( $commenter_data, $commenter_data_defaults );
		
		// Setup the comment fields for the form (note the opening and closing `.left` DIV tag).
		$fields =  array(
		
			'author' => '<p class="comment-form-author">' . '<label for="author" class="com">' . __( 'Name' ) . '' . ( $req ? ' <span class="required">*</span>' : '' ) . '</label>' . '<input id="author" class="txt input-text comtext" name="author" type="text" tabindex="1" value="' . esc_attr( $commenter_data['comment_author'] ) . '" size="22"' . $aria_req . ' />' . '</p>',
			            
			'email'  => '<p class="comment-form-email">' . ' <label for="email" class="com">' . __( 'E-mail' ) . '' . ( $req ? ' <span class="required">*</span>' : '' ) . '</label>' . '<input id="email" class="txt input-text comtext" name="email" type="text" tabindex="2" value="' . esc_attr(  $commenter_data['comment_author_email'] ) . '" size="22"' . $aria_req . ' />' . '</p>',
			            
			'url'    => '<p class="comment-form-url">' . ' <label for="url" class="com">' . __( 'Website' ) . '</label>' . '<input id="url" class="txt input-text comtext" name="url" type="text" tabindex="3" value="' . esc_attr( $commenter_data['comment_author_url'] ) . '" size="22" />' . '</p>',
	
		);
	
		// Setup the arguments to be passed to the comment form (note the opening and closing `.right` DIV tag, and the `.fix` DIV tag).
		$args = array(
		
			'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
			'comment_field'        => '<p class="comment-form-comment">' . '<label for="comment" class="com">' . _x( 'Comment', 'noun' ) . '</label>' . '<textarea id="comment" class="comtext" name="comment" cols="50" rows="10" tabindex="4" aria-required="true"></textarea></p>',
			'must_log_in'          => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
			'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
			'comment_notes_before' => '', 
			// 'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
			'comment_notes_after'  => '', 
			// 'comment_notes_after'  => '<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'title_reply'          => __( 'Post a Comment' ),
			'title_reply_to'       => __( 'Leave a Comment on %s' ),
			'cancel_reply_link'    => __( 'Click here to cancel reply' ),
			'label_submit'         => __( 'Submit Comment' )
	
		);

		return $args;

	} // End woo_comment_form_args()

// Fist full of comments
function custom_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
                 
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
    
    	<a name="comment-<?php comment_ID() ?>"></a>
  			
		<div class="commentcont" id="comment-<?php comment_ID(); ?>">
		<?php if( get_comment_type() == 'comment' ) { ?>
		<div class="fright"><?php the_commenter_avatar( $args ); ?></div>
		<?php } ?>
							
			<?php comment_text() ?>
				
				<p>
					<?php if ($comment->comment_approved == '0') { ?>
				
					<em><?php _e( 'Your comment is awaiting moderation', 'woothemes' ); ?>.</em>
					
					<?php } ?>
				</p>
				
		<cite>
		
		<?php _e( 'Posted by', 'woothemes' ); ?> <span class="commentauthor"><?php comment_author_link(); ?></span> | <a href="#comment-<?php comment_ID(); ?>" title=""><?php comment_date( get_option( 'date_format' ) ); ?>, <?php comment_time(); ?></a> <?php edit_comment_link( 'edit','| ','' ); ?>						
		
		</cite>
		
		</div>
		
		
		
		<div class="reply">
         <?php comment_reply_link( array_merge( $args, array( 'reply_text' => 'Reply to this comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
      </div>

		
<?php 
}

// PINGBACK / TRACKBACK OUTPUT
function list_pings($comment, $args, $depth) {

	$GLOBALS['comment'] = $comment; ?>
	
	<li id="comment-<?php comment_ID(); ?>">

		<span class="pingcontent"><?php comment_text(); ?></span>
		<div class="ping_meta">
			<span class="author"><?php comment_author_link(); ?></span> - 
			<span class="date"><?php echo get_comment_date( get_option( 'date_format' ) ); ?></span>
		</div>

<?php 
} 
		
function the_commenter_link() {
    $commenter = get_comment_author_link();
    if ( ereg( ']* class=[^>]+>', $commenter ) ) {$commenter = ereg_replace( '(]* class=[\'"]?)', '\\1url ' , $commenter );
    } else { $commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );}
    echo $commenter ;
}

function the_commenter_avatar($args) {
    $email = get_comment_author_email();
    $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( "$email",  $args['avatar_size']) );
    echo $avatar;
}

?>