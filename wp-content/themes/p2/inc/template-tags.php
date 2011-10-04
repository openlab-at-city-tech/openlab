<?php

function p2_body_class( $classes ) {
	if ( is_tax( P2_MENTIONS_TAXONOMY ) )
		$classes[] = 'mentions';

	return $classes;
}
add_filter( 'body_class', 'p2_body_class' );

function p2_user_can_post() {
	global $user_ID;

	if ( current_user_can( 'publish_posts' ) || ( get_option( 'p2_allow_users_publish' ) && $user_ID ) )
		return true;

	return false;
}

function p2_show_comment_form() {
	global $post, $form_visible;

	$show = ( !isset( $form_visible ) || !$form_visible ) && 'open' == $post->comment_status;

	if ( $show )
		$form_visible = true;

	return $show;
}

function p2_is_ajax_request() {
	global $post_request_ajax;

	return ( $post_request_ajax ) ? $post_request_ajax : false;
}

function p2_posting_type() {
	echo p2_get_posting_type();
}
function p2_get_posting_type() {
	$p = isset( $_GET['p'] ) ? $_GET['p'] : 'status';
	return $p;
}

function p2_media_upload_form() {
	require( ABSPATH . '/wp-admin/includes/template.php' );
	media_upload_form();
?>
<?php
}

function p2_user_display_name() {
	echo p2_get_user_display_name();
}
	function p2_get_user_display_name() {
		$current_user = wp_get_current_user();

		return apply_filters( 'p2_get_user_display_name', isset( $current_user->first_name ) && $current_user->first_name ? $current_user->first_name : $current_user->display_name );
	}

function p2_discussion_links() {
	echo p2_get_discussion_links();
}
	function p2_get_discussion_links() {
		global $post;
		$content = '';
		$unique_commentors = array();

		$comments = get_comments( array( 'post_id' => $post->ID ) );

		foreach ( $comments as $comment )
			if ( '1' == $comment->comment_approved )
				$unique_commentors[$comment->comment_author_email] = $comment;

		$total_unique_commentors = count( $unique_commentors );

		$counter = 1;
		foreach ( $unique_commentors as $comment ) {
			if ( $counter > 3 )
				break;

			if ( 1 != $counter && $total_unique_commentors == $counter )
				$content .= __( ', and ', 'p2' );
			else if ( 1 != $counter )
				$content .= ', ';

			$content .= get_avatar( $comment, 16 ) . ' ' . get_comment_author_link( $comment->comment_ID );

			$counter++;
		}

		if ( $total_unique_commentors > 3 )
			if ( ( $total_unique_commentors - 3 ) != 1 )
				$content .= sprintf( __( ' and %s others are discussing.', 'p2' ), ( $total_unique_commentors - 3 ) );
			else
				$content .= __( ' and one other person are discussing.', 'p2' );
		else {
			if ( $total_unique_commentors == 1 )
				$content .= __( ' is discussing.', 'p2' );
			else
				$content .= __( ' are discussing.', 'p2' );
		}

		return $content;
	}

function p2_quote_content() {
	echo p2_get_quote_content();
}
	function p2_get_quote_content() {
		return apply_filters( 'p2_get_quote_content', get_the_content( __( '(More ...)' , 'p2' ) ) );
	}
	add_filter( 'p2_get_quote_content', 'p2_quote_filter_kses', 1 );
	add_filter( 'p2_get_quote_content', 'wptexturize' );
	add_filter( 'p2_get_quote_content', 'convert_smilies' );
	add_filter( 'p2_get_quote_content', 'convert_chars' );
	add_filter( 'p2_get_quote_content', 'prepend_attachment' );
	add_filter( 'p2_get_quote_content', 'make_clickable' );

	function p2_quote_filter_kses( $content ) {
		global $allowedtags;

		$quote_allowedtags = $allowedtags;
		$quote_allowedtags['cite'] = array();
		$quote_allowedtags['p'] = array();

		return wp_kses( $content, $quote_allowedtags );
	}

function p2_the_category() {
	echo p2_get_the_category();
}
	function p2_get_the_category() {
		$categories = get_the_category();
		$slug = ( isset( $categories[0] ) ) ? $categories[0]->slug : '';
		return apply_filters( 'p2_get_the_category', $slug );
	}

function p2_user_prompt() {
	echo p2_get_user_prompt();
}
	function p2_get_user_prompt() {
		$prompt = get_option( 'p2_prompt_text' );

		return apply_filters( 'p2_get_user_prompt', sprintf ( __( 'Hi, %s. %s', 'p2' ), esc_html( p2_get_user_display_name() ), ( $prompt != '' ) ? stripslashes( $prompt ) : __( 'Whatcha up to?', 'p2' ) ) );
	}

function p2_page_number() {
	echo p2_get_page_number();
}
	function p2_get_page_number() {
		global $paged;
		return apply_filters( 'p2_get_page_number', $paged );
	}

function p2_media_buttons() {
	// If we're using http and the admin is forced to https, bail.
	if ( ! is_ssl() && ( force_ssl_admin() || get_user_option( 'use_ssl' ) )  ) {
		return;
	}

	include_once( ABSPATH . '/wp-admin/includes/media.php' );
	ob_start();
	do_action( 'media_buttons' );

	// Replace any relative paths to media-upload.php
	echo preg_replace( '/([\'"])media-upload.php/', '${1}' . admin_url( 'media-upload.php' ), ob_get_clean() );
}

function p2_get_hide_sidebar() {
	return ( '' != get_option( 'p2_hide_sidebar' ) ) ? true : false;
}

function p2_archive_author() {
	echo p2_get_archive_author();
}

function p2_get_archive_author() {

	if ( get_query_var( 'author_name' ) )
	 		$curauth = get_userdatabylogin( get_query_var( 'author_name' ) );
	else
	 		$curauth = get_userdata( get_query_var( 'author' ) );

	if ( isset( $curauth->display_name ) )
		return apply_filters( 'p2_get_archive_author', $curauth->display_name );
}

function p2_author_feed_link() {
	echo p2_get_author_feed_link();
}
	function p2_get_author_feed_link() {

		if ( get_query_var( 'author_name' ) )
	   		$curauth = get_userdatabylogin( get_query_var( 'author_name' ) );
		else
	   		$curauth = get_userdata( get_query_var( 'author' ) );

		if ( isset( $curauth->ID ) )
			return apply_filters( 'p2_get_author_feed_link', get_author_feed_link( $curauth->ID ) );
	}

function p2_user_identity() {
	echo p2_get_user_identity();
}
	function p2_get_user_identity() {
		global $user_identity;
		return $user_identity;
	}

function p2_load_entry( $force_comments = true ) {
	global $withcomments;

	if ( $force_comments )
		$withcomments = true;

	get_template_part( 'entry' );
}

function p2_date_time_with_microformat( $type = 'post' ) {
	$d = 'comment' == $type ? 'get_comment_time' : 'get_post_time';
	return '<abbr title="'.$d( 'Y-m-d\TH:i:s\Z', true).'">'.sprintf( __( '%1$s <em>on</em> %2$s', 'p2' ),  $d(get_option( 'time_format' )), $d( get_option( 'date_format' ) ) ).'</abbr>';
}