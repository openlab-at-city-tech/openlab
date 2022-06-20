<?php if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) ) return; ?>
<section id="comments">
<?php
if ( have_comments() ) :
global $comments_by_type;
$comments_by_type = &separate_comments( $comments );
if ( ! empty($comments_by_type['comment']) ) :
?>
<section id="comments-list" class="comments">
<h3 class="comments-title"><?php comments_number(); ?></h3>
<?php if ( get_comment_pages_count() > 1 ) : ?>
<nav id="comments-nav-above" class="comments-navigation" role="navigation">
<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
</nav>
<?php endif; ?>
<ul>
<?php wp_list_comments('type=comment'); ?>
</ul>
<?php if ( get_comment_pages_count() > 1 ) : ?>
<nav id="comments-nav-below" class="comments-navigation" role="navigation">
<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
</nav>
<?php endif; ?>
</section>
<?php
endif;
if ( ! empty($comments_by_type['pings']) ) :
$ping_count = count($comments_by_type['pings']);
?>
<section id="trackbacks-list" class="comments">
<h3 class="comments-title"><?php echo '<span>'.$ping_count.'</span> '.($ping_count > 1 ? __( 'Trackbacks', 'blankslate' ) : __( 'Trackback', 'blankslate' ) ); ?></h3>
<ul>
<?php wp_list_comments('type=pings&callback=blankslate_custom_pings'); ?>
</ul>
</section>
<?php
endif;
endif;

if ( comments_open() ) {
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$required_attribute = ( $html5 ? ' required' : ' required="required"' );

	comment_form(
		[
			'comment_field'        => sprintf(
				'<p class="comment-form-comment">%s %s</p>',
				sprintf(
					'<label for="comment">%s</label>',
					_x( 'Comment', 'noun' )
				),
				'<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525"' . $required_attribute . '></textarea>'
			),
			'logged_in_as'         => sprintf(
				'<p class="logged-in-as">%s</p>',
				sprintf(
					/* translators: 1: Edit user link, 2: Accessibility text, 3: User name, 4: Logout URL. */
					__( '<a href="%1$s" aria-label="%2$s">Logged in as %3$s</a>. <a href="%4$s">Log out?</a>' ),
					get_edit_user_link(),
					/* translators: %s: User name. */
					esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.' ), $user_identity ) ),
					$user_identity,
					/** This filter is documented in wp-includes/link-template.php */
					wp_logout_url( apply_filters( 'the_permalink', get_permalink( get_queried_object_id() ), get_queried_object_id() ) )
				)
			),
		]
	);
}

?>
</section>
