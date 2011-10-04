<?php
/**
 * Controls output elements in comment sections.
 *
 * @package Genesis
 * @todo document the functions in structure/comments.php
 */

add_action( 'genesis_after_post', 'genesis_get_comments_template' );
/**
 * Output the comments at the end of posts/pages.
 * The checks are for 3rd party comment systems.
 *
 * @since 1.1
 * @uses genesis_get_option()
 */
function genesis_get_comments_template() {

	// Load comments only if we are on a page or post and only if comments or trackbacks are chosen
	if ( is_single() && ( genesis_get_option( 'trackbacks_posts' ) || genesis_get_option( 'comments_posts' ) ) )
		comments_template( '', true );
	elseif ( is_page() && ( genesis_get_option( 'trackbacks_pages' ) || genesis_get_option( 'comments_pages' ) ) )
		comments_template( '', true );

	return;

}

add_action( 'genesis_comments', 'genesis_do_comments' );
/**
 * Echo Genesis default comment structure. 
 * 
 * @since 1.1.2 
 * @uses genesis_get_option() 
 * 
 * @global unknown $post 
 * @global WP_Query $wp_query 
 * @return null Return null if on a page with Genesis pages comments off, or a post and Genesis posts comments off.
 */
function genesis_do_comments() {
	global $post, $wp_query;

	// Check
	if ( ( is_page() && ! genesis_get_option( 'comments_pages' ) ) || ( is_single() && ! genesis_get_option( 'comments_posts' ) ) )
		return;

	if ( have_comments() && ! empty( $wp_query->comments_by_type['comment'] ) ) : ?>

	<div id="comments">
		<?php echo apply_filters( 'genesis_title_comments', __( '<h3>Comments</h3>', 'genesis' ) ); ?>
		<ol class="comment-list">
			<?php do_action( 'genesis_list_comments' ); ?>
		</ol>
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
		</div>
	</div><!--end #comments-->

	<?php else : // this is displayed if there are no comments so far ?>

	<div id="comments">
		<?php if ( 'open' == $post->comment_status ) : ?>
		<!-- If comments are open, but there are no comments. -->
		<?php echo apply_filters( 'genesis_no_comments_text', '' ); ?>

		<?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<?php echo apply_filters( 'genesis_comments_closed_text', '' ); ?>

		<?php endif; // endif comments are open, but there are no comments ?>
	</div><!--end #comments-->

	<?php endif; // endif have comments ?>

<?php
}

add_action( 'genesis_pings', 'genesis_do_pings' );
/**
 * Echo Genesis default trackback structure. 
 * 
 * @since 1.1.2 
 * @uses genesis_get_option() 
 * 
 * @global unknown $post 
 * @global WP_Query $wp_query 
 * @return null Return null if on a page with Genesis pages trackbacks off, or a post and Genesis posts trackbacks off.
 */
function genesis_do_pings() {
	global $post, $wp_query;

	// Check
	if ( ( is_page() && ! genesis_get_option( 'trackbacks_pages' ) ) || ( is_single() && ! genesis_get_option( 'trackbacks_posts' ) ) )
		return;

	if ( have_comments() && !empty( $wp_query->comments_by_type['pings'] ) ) : // if have pings ?>

	<div id="pings">
		<?php echo apply_filters( 'genesis_title_pings', __( '<h3>Trackbacks</h3>', 'genesis' ) ); ?>

		<ol class="ping-list">
			<?php do_action( 'genesis_list_pings' ); ?>
		</ol>
	</div><!-- end #pings -->

	<?php else : // this is displayed if there are no pings ?>

		<?php echo apply_filters( 'genesis_no_pings_text', '' ); ?>

	<?php endif; // endif have pings ?>

<?php
}

add_action( 'genesis_list_comments', 'genesis_default_list_comments' );
/**
 * Outputs the comment list to the <code>genesis_comment_list()</code> hook.
 *
 * @since 1.0
 */
function genesis_default_list_comments() {

	$args = array(
		'type'			=> 'comment',
		'avatar_size'	=> 48,
		'callback'		=> 'genesis_comment_callback'
	);

	$args = apply_filters( 'genesis_comment_list_args', $args );

	wp_list_comments( $args );
}

add_action( 'genesis_list_pings', 'genesis_default_list_pings' );
/**
 * Outputs the ping list to the <code>genesis_ping_list()</code> hook.
 *
 * @since 1.0
 */
function genesis_default_list_pings() {
	$args = array(
		'type' => 'pings'
	);

	$args = apply_filters( 'genesis_ping_list_args', $args );

	wp_list_comments( $args );
}

/**
 * Comment callback for <code>genesis_default_comment_list()</code>.
 *
 * @since 1.0
 *
 * @param unknown $comment 
 * @param array $args 
 * @param integer $depth
 */
function genesis_comment_callback( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>

	<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">

		<?php do_action( 'genesis_before_comment' ); ?>

		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, $size = $args['avatar_size'] ); ?>
			<?php printf( __( '<cite class="fn">%s</cite> <span class="says">%s:</span>', 'genesis' ), get_comment_author_link(), apply_filters( 'comment_author_says_text', __( 'says', 'genesis' ) ) ); ?>
	 	</div><!-- end .comment-author -->

		<div class="comment-meta commentmetadata">
			<a href="<?php echo esc_attr( get_comment_link( $comment->comment_ID ) ); ?>"><?php printf( __( '%1$s at %2$s', 'genesis' ), get_comment_date(), get_comment_time() ); ?></a>
			<?php edit_comment_link( __( 'Edit', 'genesis' ), g_ent( '&bull; ' ), '' ); ?>
		</div><!-- end .comment-meta -->

		<div class="comment-content">
			<?php if ($comment->comment_approved == '0') : ?>
				<p class="alert"><?php echo apply_filters( 'genesis_comment_awaiting_moderation', __( 'Your comment is awaiting moderation.', 'genesis' ) ); ?></p>
			<?php endif; ?>

			<?php comment_text(); ?>
		</div><!-- end .comment-content -->

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>

		<?php do_action( 'genesis_after_comment' ); ?>

	<?php // no ending </li> tag because of comment threading
}

add_action( 'genesis_comment_form', 'genesis_do_comment_form' );
/**
 * Defines the comment form, hooked to <code>genesis_comments_form()</code>
 *
 * @since 1.0
 *
 * @global unknown $user_identity 
 * @global unknown $id 
 * @return null Returns null if Genesis disables comments for this page or post
 */
function genesis_do_comment_form() {
	global $user_identity, $id;

	// Check
	if ( ( is_page() && ! genesis_get_option( 'comments_pages' ) ) || ( is_single() && ! genesis_get_option( 'comments_posts' ) ) )
		return;

	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? ' aria-required="true"' : '' );

	$args = array(
		'fields' => array(
			'author' =>	'<p class="comment-form-author">' .
						'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" tabindex="1"' . $aria_req . ' />' .
						'<label for="author">' . __( 'Name', 'genesis' ) . '</label> ' .
						( $req ? '<span class="required">*</span>' : '' ) .
						'</p><!-- #form-section-author .form-section -->',

			'email' =>	'<p class="comment-form-email">' .
						'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" tabindex="2"' . $aria_req . ' />' .
						'<label for="email">' . __( 'Email', 'genesis' ) . '</label> ' .
						( $req ? '<span class="required">*</span>' : '' ) .
						'</p><!-- #form-section-email .form-section -->',

			'url' =>		'<p class="comment-form-url">' .
							'<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" tabindex="3" />' .
							'<label for="url">' . __( 'Website', 'genesis' ) . '</label>' .
							'</p><!-- #form-section-url .form-section -->'
		),

		'comment_field' =>	'<p class="comment-form-comment">' .
							'<textarea id="comment" name="comment" cols="45" rows="8" tabindex="4" aria-required="true"></textarea>' .
							'</p><!-- #form-section-comment .form-section -->',

		'title_reply' => __( 'Speak Your Mind', 'genesis' ),
		'comment_notes_before' => '',
		'comment_notes_after' => '',
	);

	comment_form( apply_filters( 'genesis_comment_form_args', $args, $user_identity, $id, $commenter, $req, $aria_req ), $id );
}