<?php
/**
 * Plugin Name: Highlighter Pro
 * Plugin URI: http://www.industrialthemes.com/plugins/
 * Description: Highlighter Pro is a medium.com inspired front-end text highlighter that also lets users add in-line notes to specific text selections. It comes with a robust stats engine that keeps track of top highlight per post (and all-time), and other stats like total highlights and mosted noted posts.
 * Version: 1.2
 * Author: Industrial Themes
 * Author URI: http://www.industrialthemes.com
 * License: GPL2
 */

// Grab the ReduxCore framework
require_once (dirname(__FILE__) . '/options/framework.php');

// Grab the plugin settings
require_once (dirname(__FILE__) . '/highlighter-config.php');

# load admin assets - this is for redux framework modifications
add_action( 'admin_enqueue_scripts', 'highlighter_enqueued_assets_admin' );
function highlighter_enqueued_assets_admin() {
	wp_enqueue_script( 'highlighter-admin-js', plugin_dir_url( __FILE__ ) . 'js/highlighter-admin.js', array( 'jquery' ), false, true );
	wp_enqueue_style( 'highlighter-admin-css', plugin_dir_url( __FILE__ ) . 'css/highlighter-admin.css', false, false, 'all');
}

# load front-end assets
add_action( 'wp_enqueue_scripts', 'highlighter_enqueued_assets' );
function highlighter_enqueued_assets() {
	wp_enqueue_script( 'rangy-core', plugin_dir_url( __FILE__ ) . 'js/rangy-core.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'rangy-classapplier', plugin_dir_url( __FILE__ ) . 'js/rangy-classapplier.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'rangy-highlighter', plugin_dir_url( __FILE__ ) . 'js/rangy-highlighter.js', array( 'jquery' ), false, true );
	
	wp_enqueue_style( 'highlighter-css', plugin_dir_url( __FILE__ ) . 'css/highlighter.css', false, false, 'all');
	wp_enqueue_style( 'highlighter-icons', plugin_dir_url( __FILE__ ) . 'options/assets/css/vendor/elusive-icons/elusive-icons.css', false, false, 'all');

	wp_enqueue_script('validate-script', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.js', array('jquery'), false, false); 

	wp_enqueue_script('ajax-request', plugin_dir_url( __FILE__ ) . 'js/highlighter.js', array( 'jquery' ), '', true);
	wp_localize_script( 'ajax-request', 'highlighterAjax', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => $_SERVER['REQUEST_URI'],
        'loadingmessage' => __('Sending user info, please wait...')
    ));
}

# add custom classes to the body
function highlighter_body_class($classes) {
	$options = get_option( 'highlighter_settings' );
    if($options['label_compact']) $classes[] = 'label-compact';
    return $classes;
}
add_filter('body_class', 'highlighter_body_class');

# add the custom css to the footer
add_action('wp_footer','highlighter_custom_css');
function highlighter_custom_css() {
	$userid = (string) get_current_user_id();
	if($userid) $userid = 'u' . $userid . 'id';
	$options = get_option( 'highlighter_settings' );
	# these options can't be output directly from redux and need to be custom implemented
	$highlight_color = $options['highlight_color'];
	if($highlight_color=='custom') $highlight_color = $options['highlight_color_custom'];
	#convert hex to rgb
	list($r, $g, $b) = sscanf($highlight_color, "#%02x%02x%02x");
	$highlight_color = $r.','.$g.','.$b;
	
	$highlighter_css = $options['highlighter_css'];
	$css = '';
	if($highlight_color) $css .= '
		body.highlights-ready .highlighted-text,
		body.highlights-ready .highlighted-text-comment,
		body.highlights-ready .highlighted-text[data-userid*="'.$userid.'"] {
			background-color:rgba('.$highlight_color.',.3);
		}
		body.highlights-ready .highlighted-text.active,
		body.highlights-ready .highlighter-stat-text a:hover .highlighted-text-comment,
		body.highlights-ready .highlighted-text.active[data-userid*="'.$userid.'"] {
			background-color:rgba('.$highlight_color.',.5);
		}
		.highlighter-shortcode .highlighter-stat-text a:hover {
			background:rgba('.$highlight_color.',.25);
		}
		.highlighter-stat:first-child,
		.highlighter-shortcode .highlighter-stat-text:first-child {
		    border-top:1px dashed rgba('.$highlight_color.',.7);
		}';

	# see if we're showing only user's highlights
	if($options['highlight_display']=='yours') $css .= '
		body.highlights-ready .highlighted-text {
			background:none;
			cursor:text;
		}';
	# see if we need to hide facebook/twitter buttons
	/* we are now completely ommitting these in the first place
	if(!$options['twitter_enabled']) $css .= '
		.btn-popup.shown.btn-twitter, .btn-twitter {
			display:none;
		}';
	if(!$options['facebook_enabled']) $css .= '
		.btn-popup.shown.btn-facebook, .btn-facebook {
			display:none;
		}';
	*/
	# see if we need a custom z-index for labels
	if($options['label_zindex']) $css .= '
		.highlighter-note {
			z-index:'.$options['label_zindex'].'
		}';
	
	# add the custom css from the plugin options last
	$css .= $highlighter_css;
	if(!empty($css)) echo '<style type="text/css">' . $css . '</style>';
}

# wrap content in div with php variables in data attributes
add_filter( 'the_content', 'highlighter_wrap_content', 10, 2 ); 
function highlighter_wrap_content( $content ) { 

	global $post;
	$postid = $post->ID;
	$timestamp = $post->post_modified;
	$posttitle = $post->post_title;
	$options = get_option( 'highlighter_settings' );
	$userid = (string) get_current_user_id();
	$login_url = wp_login_url(get_permalink());
	$permalink = get_permalink();
	$allow = false;

	if($userid) $userid = 'u' . $userid . 'id';

	$login_type = $options['login_type'] ? $options['login_type'] : 'ajax';

	// what parts of the site to allow highlighter functionality
	$types = !empty($options['highlighter_enable']) ? $options['highlighter_enable'] : array();
	$types_cpts = !empty($options['highlighter_cpts']) ? $options['highlighter_cpts'] : array();
	if(isset($options['highlighter_cpts_manual'])) {
		$val = preg_replace('/\s+/', '', $options['highlighter_cpts_manual']);
		if(strpos($val, ',') !== false) {
			$types_cpts_manual = explode(',', $val);
		} else {
			$types_cpts_manual = array($val);
		}
	}
	$types = array_merge($types, (array)$types_cpts, $types_cpts_manual);

	if(is_page() && in_array('page', $types)) $allow = true;
	if(is_archive() && in_array('archive', $types)) $allow = true;
	if(is_front_page() && in_array('home', $types)) $allow = true;
	if(is_home() && in_array('blog', $types)) $allow = true;
	if(is_singular(get_post_type()) && in_array(get_post_type(), $types)) $allow = true;

	// check to see if this post is individually or categorically disabled
	if(isset($options['category_disable'])) {
		if(!empty($options['category_disable'])) {
			if(has_category($options['category_disable'], $postid)) {
				$allow = false;
			}
		}
	}
	$disable = get_post_meta($postid, 'highlighter-disable', true);
	if($disable) $allow = false;

	// highlight display
	$highlightdisplay = $options['highlight_display'];
	$highlightcss = $highlightdisplay . '-only';

	// label display and placement
	$labeldisplay = !empty($options['label_display']) ? json_encode($options['label_display']) : '[]';
	$labeloffset = $options['label_offset'];
	$labelplacement = $options['label_placement'];

	// other variables to pass to js
	$twitterHighlights = !empty($options['twitter_highlights']) ? $options['twitter_highlights'] : true;
	$facebookHighlights = !empty($options['facebook_highlights']) ? $options['facebook_highlights'] : true;
	$viewingEnabled = !empty($options['viewing_enabled']) ? $options['viewing_enabled'] : true;
	$commentsView = !empty($options['comments_view']) ? $options['comments_view'] : 'dock';

	// store selectors in key value array
	$selectors = json_encode(array(
		'comment-list' => $options['selector_comment_list'],
		'respond' => $options['selector_respond'],
		'cancel-reply' => $options['selector_cancel_reply']
	));


	// i18n messages for use in js
	$msgAddHighlight = !empty($options['msg_add_highlight']) ? $options['msg_add_highlight'] : __( 'Highlight this selection?', 'highlighter');
	$msgRemoveHighlight = !empty($options['msg_remove_highlight']) ? $options['msg_remove_highlight'] : __( 'Remove your highlight?', 'highlighter' ); 
	$msgAddComment = !empty($options['msg_add_comment']) ? $options['msg_add_comment'] : __( 'Comment on this highlight?', 'highlighter');
	$msgAddNewComment = !empty($options['msg_add_new_comment']) ? $options['msg_add_new_comment'] : __( 'Comment on this selection?', 'highlighter');
	$msgHighlighted = !empty($options['msg_highlighted']) ? $options['msg_highlighted'] : __( 'You highlighted', 'highlighter' );
	$msgCommented = !empty($options['msg_commented']) ? $options['msg_commented'] : __( 'You highlighted and commented', 'highlighter');
	$msgFacebookConfirm = !empty($options['msg_facebook_confirm']) ? $options['msg_facebook_confirm'] : __( 'Post this to Facebook?', 'highlighter' );
	$msgTwitterConfirm = !empty($options['msg_twitter_confirm']) ? $options['msg_twitter_confirm'] : __( 'Tweet this?', 'highlighter' );
	$msgRedirectToPost = !empty($options['msg_redirect_to_post']) ? $options['msg_redirect_to_post'] : __( 'Go to this post?', 'highlighter');
	

	if($allow)
		$content = '<div class="highlighter-content highlighter-content-loading ' . $highlightcss . ' post-' . $postid . '" 
						data-postid="' . $postid . '" 
						data-timestamp="' . $timestamp . '" 
						data-userid="' . $userid . '" 
						data-logintype="' . $login_type . '" 
						data-loginurl="' . $login_url . '" 
						data-posttitle="' . $posttitle . '" 
						data-removehighlight="' . $msgRemoveHighlight . '" 
						data-facebookconfirm="' . $msgFacebookConfirm . '" 
						data-twitterconfirm="' . $msgTwitterConfirm . '" 
						data-permalink="' . $permalink . '" 
						data-msghighlighted="' . $msgHighlighted . '" 
						data-msgcommented="' . $msgCommented . '" 
						data-addhighlight="' . $msgAddHighlight . '" 
						data-addcomment="' . $msgAddComment . '"
						data-redirecttopost="' . $msgRedirectToPost . '" 
						data-addnewcomment="' . $msgAddNewComment . '" 
						data-labelplacement="' . $labelplacement . '" 
						data-labeloffset="' . $labeloffset . '" 
						data-labeldisplay=' . $labeldisplay . ' 
						data-highlightdisplay="' . $highlightdisplay . '" 
						data-twitterhighlights="' . $twitterHighlights . '" 
						data-facebookhighlights="' . $facebookHighlights . '" 
						data-viewingenabled="' . $viewingEnabled . '" 
						data-commentsview="' . $commentsView . '" 
						data-selectors=' . $selectors . ' 

					>' . $content . '</div>';

	return $content;

}

# add highlighter shield to page
add_action( 'wp_footer', 'highlighter_shield' );
function highlighter_shield() {
	$options = get_option( 'highlighter_settings' );
	?>

	<div class="highlighter-shield">
		<div class="highlighter-popup">
			<span class="highlighter-triangle"></span>
			<div class="btn-popup btn-remove-highlight"></div>
			<div class="btn-popup btn-view-highlight"></div>
			<div class="btn-popup btn-highlight-text"></div>
			<div class="btn-popup btn-comment"></div>
			<div class="btn-popup btn-comment btn-comment-link"></div>
			<?php if($options['twitter_enabled']) { ?><div class="btn-popup btn-twitter"></div><?php } ?>
			<?php if($options['facebook_enabled']) { ?><div class="btn-popup btn-facebook"></div><?php } ?>
			<div class="lbl-popup btn-view-highlight lbl-count"></div>
			<div class="lbl-popup btn-view-highlight lbl-you"></div>
		</div>
	</div>

	<?php 
}

# add highlighter popup to page
add_action( 'wp_footer', 'highlighter_popup' );
function highlighter_popup() {
	$options = get_option( 'highlighter_settings' );
	?>

	<div class="highlighter-popup">
		<span class="highlighter-triangle"></span>
		<div class="btn-popup btn-remove-highlight"></div>
		<div class="btn-popup btn-view-highlight"></div>
		<div class="btn-popup btn-highlight-text"></div>
		<div class="btn-popup btn-comment"></div>
		<div class="btn-popup btn-comment btn-comment-link"></div>
		<?php if($options['twitter_enabled']) { ?><div class="btn-popup btn-twitter"></div><?php } ?>
		<?php if($options['facebook_enabled']) { ?><div class="btn-popup btn-facebook"></div><?php } ?>
		<div class="lbl-popup btn-view-highlight lbl-count"></div>
		<div class="lbl-popup btn-view-highlight lbl-you"></div>
	</div>

	<?php 
}

# add comment form to page if enabled
add_action( 'wp_footer', 'highlighter_comment_form' );
function highlighter_comment_form() {
	
	$options = get_option( 'highlighter_settings' );
	$comments_enabled = $options['comments_enabled'] ? $options['comments_enabled'] : false;

	if($comments_enabled && is_single()) { 

		# get some user info
		$current_user = wp_get_current_user();
		$avatar = '';
		if ($current_user instanceof WP_User) {
		    $avatar = get_avatar( $current_user->user_email, 32 );
		    $name = $current_user->display_name;
		}

		# setup comment form
		$comment_args = array( 
			'id_form' => 'highlighter-comment-form',
			'title_reply' => '',
			'id_submit'   => 'highlighter-comment-submit',
			'label_submit' => __( 'Respond', 'highlighter' ),
			'submit_button' => '<div class="btn-confirm confirm-yes">
	            <input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />
	        </div>',
			'fields' => apply_filters( 'comment_form_default_fields', array(
					'author' => '', 
					'email'  => '',
					'url'    => '' ) ),
			'must_log_in' => '',
			'logged_in_as' => '',
		    'comment_field' => '<textarea id="highlighter-comment-textarea" name="comment"  aria-required="true"></textarea>',
		    'comment_notes_before' => '',
		    'comment_notes_after' => '',
		);

		?>
		
		<div class="highlighter-docked-panel highlighter-comments-wrapper">

			<div class="highlighter-docked-header"><?php _e( 'Add Comment', 'highlighter' ); ?></div>

			<div class="highlighter-comments-user">
				<?php echo $avatar; ?>
				<span class="highlighter-comments-name"><?php echo $name; ?></span>
			</div>

			<div class="highlighter-comment">

				<div class="highlighter-view-loading"><?php _e( 'Loading...', 'highlighter' ); ?></div>

				<?php // remove custom hooks
				if($options['hook_comment_form_top']) remove_action( 'comment_form_top', $options['hook_comment_form_top'] );
				if($options['hook_comment_form']) remove_action( 'comment_form', $options['hook_comment_form'] );

				comment_form($comment_args); 

				// re-add custom hooks
				if($options['hook_comment_form_top']) add_action( 'comment_form_top', $options['hook_comment_form_top'] );
				if($options['hook_comment_form']) add_action( 'comment_form', $options['hook_comment_form'] );
				?>


				<div class="btn-confirm confirm-no"><?php _e('Cancel', 'highlighter'); ?></div>

			</div>

		</div>

		
	<?php }

}


# add view panel if enabled
add_action( 'wp_footer', 'highlighter_view_panel' );
function highlighter_view_panel() {
	
	$options = get_option( 'highlighter_settings' );
	$comments_enabled = $options['comments_enabled'] ? $options['comments_enabled'] : false;
	$viewing_enabled = $options['viewing_enabled'] ? $options['viewing_enabled'] : false;

	if($viewing_enabled) { 

		?>
		
		<div class="highlighter-docked-panel highlighter-view-wrapper">

			<div class="highlighter-docked-header"><?php _e( 'Viewing Highlight', 'highlighter' ); ?></div>

			<div class="highlighter-view-notes-wrapper">

				<div class="highlighter-view-loading shown"><?php _e( 'Loading...', 'highlighter' ); ?></div>

				<div class="highlighter-view-notes">

					<div class="highlighter-view-note-wrapper">

						<div class="highlighter-view-note-user"></div>

						<div class="highlighter-view-note"></div>

					</div>

					<!-- more notes here -->

				</div>

				<div class="btn-confirm confirm-yes btn-highlight-text"><?php _e('Highlight', 'highlighter'); ?></div>

				<div class="btn-confirm confirm-no"><?php _e('Close', 'highlighter'); ?></div>

			</div>

		</div>

		
	<?php }

}

# add ajax login form to page if enabled
add_action( 'wp_footer', 'highlighter_login' );
function highlighter_login() {

	$out = '';

	$options = get_option( 'highlighter_settings' );
	$login_type = $options['login_type'] ? $options['login_type'] : 'ajax';
	$custom_message = isset($options['custom_message']) ? '<div class="custom-message">' . $options['custom_message'] . '</div>' : '';

	if (!is_user_logged_in() && $login_type=='ajax') {
		$out .= '
		<form id="login" class="ajax-auth" action="login" method="post">
		    <div class="title-text">Login</div>
		    <p class="status"></p>'
		    . wp_nonce_field('ajax-login-nonce', 'security', true, false) . $custom_message .
		    '<label for="username">Username</label>
		    <input id="username" type="text" class="required" name="username">
		    <label for="password">Password</label>
		    <input id="password" type="password" class="required" name="password">
		    <div class="cf">
		    <a id="pop_forgot" class="text-link" href="' . wp_lostpassword_url() . '">Forgot password?</a>
		    <input class="submit_button" type="submit" value="LOGIN">
		    </div>
		    <div class="intro-text"><a id="pop_signup" href="">New to site? Create an Account</a></div>
			<a class="login_close" href="">&times;</a>    
		</form>
		<form id="register" class="ajax-auth"  action="register" method="post">
		    <div class="title-text">Signup</div>
		    <p class="status"></p>'
		    . wp_nonce_field('ajax-register-nonce', 'signonsecurity', true, false) . $custom_message .        
		    '<label for="signonname">Username</label>
		    <input id="signonname" type="text" name="signonname" class="required">
		    <label for="email">Email</label>
		    <input id="email" type="text" class="required email" name="email">
		    <label for="signonpassword">Password</label>
		    <input id="signonpassword" type="password" class="required" name="signonpassword" >
		    <label for="password2">Confirm Password</label>
		    <input type="password" id="password2" class="required" name="password2">
		    <div class="cf">
		    <input class="submit_button" type="submit" value="SIGNUP">
		    </div>
		    <div class="intro-text"><a id="pop_login" href="">Already have an account? Login</a></div>
		    <a class="login_close" href="">&times;</a>    
		</form>

		<form id="forgot_password" class="ajax-auth" action="forgot_password" method="post">    
		    <div class="title-text">Forgot Password</div>
		    <p class="status"></p>'  
		    . wp_nonce_field('ajax-forgot-nonce', 'forgotsecurity', true, false) .  
		    '<label for="user_login">Username or E-mail</label>
		    <input id="user_login" type="text" class="required" name="user_login">
		     <input class="submit_button" type="submit" value="SUBMIT">
			<a class="login_close" href="">&times;</a>    
		</form>';
	}

	//return $out;

	echo $out;
}


# need to allow data attributes in spans for non-admin front-end post updating
function highlighter_filter_allowed_html($allowed, $context){
	if (is_array($context)) {
	    return $allowed;
	}
 
	if ($context === 'post') {
	    $allowed['span']['data-userid'] = true;
	    $allowed['span']['data-commentid'] = true;
	    $allowed['span']['data-userid-comment'] = true;
	}
 
	return $allowed;
}
add_filter('wp_kses_allowed_html', 'highlighter_filter_allowed_html', 10, 2);

# need to allow spans in comments for non-admin users
function highlighter_allow_tags_in_comments($data) {
	global $allowedtags;
	$allowedtags['span'] = array(
		'class'=>array(),
		'data-userid'=>array(),
		'data-commentid'=>array(),
		'data-userid-comment'=>array()
	);
	return $data;
}
add_filter('preprocess_comment','highlighter_allow_tags_in_comments', 10, 2);


# ajax

function ajax_check_content() {

	#setup vars
    $postid = empty($_POST['postid']) ? '' : $_POST['postid'];
    $timestamp = empty($_POST['timestamp']) ? '' : $_POST['timestamp'];

    #get latest edited timestamp
   	$content_post = get_post($postid);
	$new_timestamp = $content_post->post_modified;
	$new_content = $content_post->post_content;
	remove_filter( 'the_content', 'highlighter_wrap_content' );
	$new_content = apply_filters('the_content', $new_content);
	add_filter( 'the_content', 'highlighter_wrap_content', 10, 2 ); 
	$new_content = str_replace(']]>', ']]&gt;', $new_content);

	$modified = $timestamp == $new_timestamp ? false : true;

	$new_content = stripslashes($new_content);
    
	#generate the response
    $response = json_encode( array( 'modified' => $modified, 'timestamp' => $timestamp, 'new_timestamp' => $new_timestamp, 'new_content' => $new_content ) );

    #response output
    header( "Content-Type: application/json" );
    echo $response;

    exit;
}

function ajax_update_content() {

	#setup vars
    $postid = empty($_POST['postid']) ? '' : $_POST['postid'];
    $new_timestamp = empty($_POST['timestamp']) ? '' : $_POST['timestamp'];
    $new_content = empty($_POST['new_content']) ? '' : $_POST['new_content'];

    #get latest edited timestamp
   	$content_post = get_post($postid);
	$old_timestamp = $content_post->post_modified;
	$old_content = $content_post->post_content;
	remove_filter( 'the_content', 'highlighter_wrap_content' );
	$old_content = apply_filters('the_content', $old_content);
	add_filter( 'the_content', 'highlighter_wrap_content', 10, 2 ); 
	$old_content = str_replace(']]>', ']]&gt;', $old_content);

	$modified = $new_timestamp == $old_timestamp ? false : true;

	$new_content = $modified ? $old_content : $new_content;
	$new_timestamp = $modified ? $old_timestamp : $new_timestamp;

	$new_content = stripslashes($new_content);

    // Update post
	$update_post = array(
	  'ID'           => $postid,
	  'post_content' => $new_content
	);

	// Update the post into the database
	wp_update_post( $update_post );
	#get modified timestamp after update
   	$updated_post = get_post($postid);
	$updated_timestamp = $updated_post->post_modified;

	#generate the response
    $response = json_encode( array( 'new_content' => $new_content, 'new_timestamp' => $updated_timestamp) );

    #response output
    header( "Content-Type: application/json" );
    echo $response;

    exit;
}

// this gets a comment or just a highlight
function ajax_get_comment() {

	#setup vars
    $userids = empty($_POST['userids']) ? '' : $_POST['userids'];
    $userids_comment = empty($_POST['userids_comment']) ? '' : $_POST['userids_comment'];
    $commentid = empty($_POST['commentid']) ? '' : $_POST['commentid'];
    $highlight = empty($_POST['highlight']) ? '' : $_POST['highlight'];

    $comment = '';
    $user = '';
    $avatar = '';
    $name = '';
    $type = 'highlight';

    #find out which users have only highlighted
	$users = explode(',', str_replace(array('u','id'), '', $userids));
	$users_comment = explode(',', str_replace(array('u','id'), '', $userids_comment));
	$users_diff = array_diff($users, $users_comment);
	$total_diff = count($users_diff);
	$i = 0;
	#build out $name html if any users have only highlighted
	if($total_diff > 0) {
		foreach($users_diff as $user) { 
			$comment_user = get_userdata($user);
			if ($comment_user instanceof WP_User && $i < 5) {
			    $name .= $comment_user->display_name . ', ';
			    $i++;
			}
		}

		# get rid of last comma and space
		$name = substr($name, 0, -2);

		# figure out if there are more names than can be displayed
		$extra = $i < $total ? $total - $i : 0;
		$s = $extra===1 ? '' : 's';
		if($extra) $name .= ' and ' . $extra . ' other' . $s;
	}

    # build out the highlight and comments html
    if(!empty($commentid)) {

    	$type = 'comment';

    	# get comment ids into array and loop through each one
	    $commentids = explode(',', $commentid);
	    $total = count($commentids);
	    if($total === 1) $total = array($commentids);
	    foreach($commentids as $id) {
	    	$thecomment = get_comment($id);
    		$comment .= '<div class="highlight-comment-wrapper">
    						<div class="highlight-comment">'
	    					. $thecomment->comment_content . 
	    					'</div>
	    					<div class="highlight-comment-user">'
	    					. $thecomment->comment_author . 
	    					'</div>
	    				</div>';
	    }
    	

    } else {

    	$comment = '<span class="highlighted-text-comment">' . $highlight . '</span>';

	}

	#generate the response
    $response = json_encode( array( 'comment' => $comment, 'name' => $name, 'type' => $type ) );

    #response output
    header( "Content-Type: application/json" );
    echo $response;

    exit;
}


#ajax submit comment
function ajax_submit_comment(){
	/*
	 * @since 4.4.0
	 */
	$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );

	if ( is_wp_error( $comment ) ) {
		$error_data = intval( $comment->get_error_data() );
		if ( ! empty( $error_data ) ) {
			wp_die( '<p>' . $comment->get_error_message() . '</p>', __( 'Comment Submission Failure', 'highlighter' ), array( 'response' => $error_data, 'back_link' => true ) );
		} else {
			wp_die( 'Unknown error' );
		}
	}
 
	/*
	 * Set Cookies
	 */
	$user = wp_get_current_user();
	do_action('set_comment_cookies', $comment, $user);
 
	/*
	 * If you do not like this loop, pass the comment depth from JavaScript code
	 */
	$comment_depth = 1;
	$comment_parent = $comment->comment_parent;
	while( $comment_parent ){
		$comment_depth++;
		$parent_comment = get_comment( $comment_parent );
		$comment_parent = $parent_comment->comment_parent;
	}
 
 	/*
 	 * Set the globals, so our comment functions below will work correctly
 	 */
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $comment_depth;
 
	/*
	 * Here is the comment template, you can configure it for your website
	 * or you can try to find a ready function in your theme files
	 */

	/* this is actually not used yet, possible future plugin update */
	$comment_html = '<li ' . comment_class('', null, null, false ) . ' id="comment-' . get_comment_ID() . '">
		<article class="comment-body" id="div-comment-' . get_comment_ID() . '">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					' . get_avatar( $comment, 100 ) . '
					<b class="fn">' . get_comment_author_link() . '</b> <span class="says">' . __('says:', 'highlighter') . '</span>
				</div>
				<div class="comment-metadata">
					<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . sprintf('%1$s at %2$s', get_comment_date(),  get_comment_time() ) . '</a>';
 
					if( $edit_link = get_edit_comment_link() )
						$comment_html .= '<span class="edit-link"><a class="comment-edit-link" href="' . $edit_link . '">' . __('Edit', 'highlighter') . '</a></span>';
 
				$comment_html .= '</div>';
				if ( $comment->comment_approved == '0' )
					$comment_html .= '<p class="comment-awaiting-moderation">' . __('Your comment is awaiting moderation.', 'highlighter') . '</p>';
 
			$comment_html .= '</footer>
			<div class="comment-content">' . apply_filters( 'comment_text', get_comment_text( $comment ), $comment ) . '</div>
		</article>
	</li>';

	#generate the response
    $response = json_encode( array( 'comment_html' => $comment_html, 'comment_id' => $comment->comment_ID) );

    #response output
    header( "Content-Type: application/json" );
    echo $response;
 
	exit;
 
}



# ajax actions
add_action( 'wp_ajax_nopriv_ajax-check-content', 'ajax_check_content' );
add_action( 'wp_ajax_ajax-check-content', 'ajax_check_content' );
add_action( 'wp_ajax_nopriv_ajax-update-content', 'ajax_update_content' );
add_action( 'wp_ajax_ajax-update-content', 'ajax_update_content' );
add_action( 'wp_ajax_nopriv_ajax-get-comment', 'ajax_get_comment' );
add_action( 'wp_ajax_ajax-get-comment', 'ajax_get_comment' );
add_action( 'wp_ajax_ajaxcomments', 'ajax_submit_comment' );
add_action( 'wp_ajax_nopriv_ajaxcomments', 'ajax_submit_comment' );




#cron jobs
add_filter( 'cron_schedules', 'cron_add_schedules' );
add_action( 'wp', 'highlighter_setup_schedule' );
add_action( 'highlighter_scheduled_event', 'highlighter_scan' );

if(!function_exists('cron_add_schedules')) {
	function cron_add_schedules( $schedules ) {
		#Add more time periods to the available cron schedule options
		$schedules['everyfiveminutes'] = array(
			'interval' => 300,
			'display' => __( 'Every 5 Minutes', 'highlighter' )
		);
		$schedules['everyfifteenminutes'] = array(
			'interval' => 900,
			'display' => __( 'Every 15 Minutes', 'highlighter' )
		);
		$schedules['everythirtyminutes'] = array(
			'interval' => 1800,
			'display' => __( 'Every 30 Minutes', 'highlighter' )
		);
		$schedules['everyfortyfiveminutes'] = array(
			'interval' => 2700,
			'display' => __( 'Every 45 Minutes', 'highlighter' )
		);
		$schedules['everytwodays'] = array(
			'interval' => 172800,
			'display' => __( 'Every 2 Days', 'highlighter' )
		);
		$schedules['everythreedays'] = array(
			'interval' => 259200,
			'display' => __( 'Every 3 Days', 'highlighter' )
		);
		$schedules['everyfourdays'] = array(
			'interval' => 345600,
			'display' => __( 'Every 4 Days', 'highlighter' )
		);
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __( 'Weekly', 'highlighter' )
		);
		return $schedules;
	}
}
if(!function_exists('highlighter_setup_schedule')) {
	#setup cron schedule for the highlight scan
	function highlighter_setup_schedule() {
		$hook = 'highlighter_scheduled_event';
		$options = get_option( 'highlighter_settings' );
		$old = wp_get_schedule($hook);
		$new = $options['stats_schedule'];
		$timestamp = wp_next_scheduled($hook);
		//die('old=' . $old . '<br />new=' . $new);
		if (!$timestamp || $old != $new) {
			if($options['stats_enabled']) {
				wp_unschedule_event($timestamp, $hook);
				wp_schedule_event(time(), $new, $hook);
			}
		}
	}
}
if(!function_exists('highlighter_scan')) {
	#scan all posts for highlights and store totals
	function highlighter_scan() {
		
		$options = get_option( 'highlighter_settings' );
		$num = $options['stats_limit'] == 'limit' ? $options['stats_num'] : -1;

		$site_total_highlights = 0;
		$site_total_highlighters = 0;
		$site_total_commenters = 0;
		$site_top_comment = 0;
		$site_top_highlight = 0;
		$site_users_all = '';
	    $site_users_comment_all = '';
	    $site_top_highlight_text = '';
	    $site_top_highlight_link = '';
	    $site_top_comment_text = '';
	    $site_top_comment_link = '';

	    // determine which post types to scan
	    $types = isset($options['highlighter_enable']) ? $options['highlighter_enable'] : array();
		$types_cpts = isset($options['highlighter_cpts']) ? $options['highlighter_cpts'] : array();
		if(isset($options['highlighter_cpts_manual'])) {
			$val = preg_replace('/\s+/', '', $options['highlighter_cpts_manual']);
			if(strpos($val, ',') !== false) {
				$types_cpts_manual = explode(',', $val);
			} else {
				$types_cpts_manual = array($val);
			}
		}
		$types = array_merge($types, $types_cpts, $types_cpts_manual);
		$args = array( 
			'post_type'       => $types,
			'post_status'	  => 'publish',
			'posts_per_page'  => $num,
		);

		global $post;

		$posts = get_posts( $args );
		foreach($posts as $post) : setup_postdata($post);

			$content = get_the_content();
			$permalink = get_permalink();
			remove_filter( 'the_content', 'highlighter_wrap_content' );
			$content = apply_filters('the_content', $content);
			add_filter( 'the_content', 'highlighter_wrap_content', 10, 2 ); 

			$result = array();

		    $classname = "highlighted-text";
		    $domdocument = new DOMDocument();
		    $domdocument->loadHTML($content);
		    $a = new DOMXPath($domdocument);
		    $spans = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

		    // total highlights
		    $total_highlights = $spans->length;

		    // loop through highlights and get counts
		    $total_highlighters = 0;
		    $total_commenters = 0;
		    $top_highlight = 0;
		    $top_comment = 0;
		    $users_all = '';
		    $users_comment_all = '';
		    $top_highlight_text = '';
		    $top_comment_text = '';

		    foreach($spans as $span) {

		    	//highlights
		    	$userids = $span->getAttribute('data-userid'); 
		    	$users = str_replace(array('u','id'), '', $userids);

		    	if(strlen($users)) {
			    	$users_all .= $users . ',';
			    	if(count(explode(',', $users)) > $top_highlight) {
			    		$top_highlight = count(explode(',', $users));
			    		$top_highlight_text = $span->textContent;
			    	}
			    }

		    	//comments
		    	$userids_comment = $span->getAttribute('data-userid-comment'); 
		    	$users_comment = str_replace(array('u','id'), '', $userids_comment);

		    	if(strlen($users_comment)) {
			    	$users_comment_all .= $users_comment . ',';
			    	if(count(explode(',', $users_comment)) > $top_comment) {
			    		$top_comment = count(explode(',', $users_comment));
			    		$top_comment_text = $span->textContent;
			    	}
			    }

		    }

		    // get post totals
		    $users_all = rtrim($users_all,',');
		    if(strlen($users_all))
		    	$total_highlighters = count(array_unique(explode(',', $users_all)));

		    $users_comment_all = rtrim($users_comment_all,',');
		    if(strlen($users_comment_all))
		    	$total_commenters = count(array_unique(explode(',', $users_comment_all)));
		    
		    // perform the post meta updates
			update_post_meta( get_the_ID(), 'total_highlights', $total_highlights );
			update_post_meta( get_the_ID(), 'top_highlight', $top_highlight );
			update_post_meta( get_the_ID(), 'top_comment', $top_comment );
			update_post_meta( get_the_ID(), 'top_highlight_text', $top_highlight_text );
			update_post_meta( get_the_ID(), 'top_comment_text', $top_comment_text );
			update_post_meta( get_the_ID(), 'total_highlighters', $total_highlighters );
			update_post_meta( get_the_ID(), 'total_commenters', $total_commenters );

			// site totals
			$site_total_highlights += $total_highlights;
			if($top_highlight > $site_top_highlight) {
				$site_top_highlight = $top_highlight;
				$site_top_highlight_text = $top_highlight_text;
				$site_top_highlight_link = $permalink;
			}
			if($top_comment > $site_top_comment) {
				$site_top_comment = $top_comment;
				$site_top_comment_text = $top_comment_text;
				$site_top_comment_link = $permalink;
			}
			$site_users_all .= $users_all . ',';
			$site_users_comment_all .= $users_comment_all . ',';

		endforeach;
		
		wp_reset_query();

		// setup site totals
	    $site_users_all = rtrim($site_users_all,',');
	    if(strlen($site_users_all))
	    	$site_total_highlighters = count(array_unique(explode(',', $site_users_all)));

	    $site_users_comment_all = rtrim($site_users_comment_all,',');
	    if(strlen($site_users_comment_all))
	    	$site_total_commenters = count(array_unique(explode(',', $site_users_comment_all)));

	    // perform the site option updates
		update_option( 'highlighter_total_highlights', $site_total_highlights );
		update_option( 'highlighter_top_highlight', $site_top_highlight );
		update_option( 'highlighter_top_comment', $site_top_comment );
		update_option( 'highlighter_top_highlight_text', $site_top_highlight_text );
		update_option( 'highlighter_top_highlight_link', $site_top_highlight_link );
		update_option( 'highlighter_top_comment_text', $site_top_comment_text );
		update_option( 'highlighter_top_comment_link', $site_top_comment_link );
		update_option( 'highlighter_total_highlighters', $site_total_highlighters );
		update_option( 'highlighter_total_commenters', $site_total_commenters );

	}
}
// utility for manually running this cron job - don't use in production!
//add_action( 'wp_footer', 'highlighter_scan' );



# shortcodes
function highlighter_stats($atts) {
	$options = get_option( 'highlighter_settings' );
	$defaults = $options['stats_defaults'];
	
	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'show-total-highlights' => $defaults['total_highlights'],
		'show-total-highlighters' => $defaults['total_highlighters'],
		'show-total-commenters' => $defaults['total_commenters'],
		'show-top-highlight' => $defaults['top_highlight'],
		'show-top-comment' => $defaults['top_comment'],
		'show-top-highlight-text' => $defaults['top_highlight_text'],
		'show-top-comment-text' => $defaults['top_comment_text'],
		'toggled' => $options['stats_toggled'],
		'context' => $options['stats_context'],
	), $atts, 'highlighter-stats' );

	$out = '';
	$css = '';
	if($atts['context'] != 'site') {
		global $post;
		$postid = $post->ID;
	}
	if($atts['context']=='site') {
		$total_highlights = get_option( 'highlighter_total_highlights' );
		$top_highlight = get_option( 'highlighter_top_highlight' );
		$top_comment = get_option( 'highlighter_top_comment' );
		$total_highlighters = get_option( 'highlighter_total_highlighters' );
		$total_commenters = get_option( 'highlighter_total_commenters' );
		$top_highlight_text = get_option( 'highlighter_top_highlight_text' );
		$top_comment_text = get_option( 'highlighter_top_comment_text' );
		$css .= ' site';
		$tooltip_text = __('Site Stats', 'highlighter');
	} else {
		$total_highlights = get_post_meta( $postid, 'total_highlights', true );
		$top_highlight = get_post_meta( $postid, 'top_highlight', true );
		$top_comment = get_post_meta( $postid, 'top_comment', true );
		$total_highlighters = get_post_meta( $postid, 'total_highlighters', true );
		$total_commenters = get_post_meta( $postid, 'total_commenters', true );
		$top_highlight_text = get_post_meta( $postid, 'top_highlight_text', true );
		$top_comment_text = get_post_meta( $postid, 'top_comment_text', true );
		$css .= ' single';
		$tooltip_text = __('Highlighter Stats', 'highlighter');
	}

	$css .= $atts['toggled'] ? ' toggled' : '';

	# plurals
	$highlights = $total_highlights == 1 ? __('highlight', 'highlighter') : __('highlights', 'highlighter');
	$plural_highlighters = $total_highlighters == 1 ? __('person', 'highlighter') : __('people', 'highlighter');
	$plural_commenters = $total_commenters == 1 ? __('person', 'highlighter') : __('people', 'highlighter');
	$plural_top_highlight = $top_highlight == 1 ? __('person', 'highlighter') : __('people', 'highlighter');
	$plural_top_comment = $top_comment == 1 ? __('person', 'highlighter') : __('people', 'highlighter');

	# hide if all are 0
	$hide_all = false;
	if($total_highlights == 0 && $top_highlight == 0 && $top_comment == 0 && $total_highlighters == 0 && $total_commenters == 0) $hide_all = true;

	if(!$hide_all) {
		$out .= '
		<div class="highlighter-stats-wrapper' . $css . '">';
			if($atts['toggled']) {
				$out .= '
				<div class="highlighter-stats-toggle">
					<img src="' . plugin_dir_url( __FILE__ ) . 'img/stats-toggle.png" />

					<div class="stats-tooltip">' . $tooltip_text . '</div>
				</div>
				<div class="highlighter-stats">
					<div class="btn-confirm confirm-no">&times;</div>
				';
			} else {
				$out .= '
				<div class="highlighter-stats-title">
					<img src="' . plugin_dir_url( __FILE__ ) . 'img/stats-toggle.png" />

					' . $tooltip_text . '
				</div>
				<div class="highlighter-stats">';
			}
			if($atts['show-total-highlights'] && $total_highlights > 0) $out .= '
				<div class="highlighter-stat">
					<span class="stat-number">' . $total_highlights . '</span> total ' . $highlights . '
				</div>';
			if($atts['show-total-highlighters'] && $total_highlighters > 0) $out .= '
				<div class="highlighter-stat">
					<span class="stat-number">' . $total_highlighters . '</span> ' . $plural_highlighters . ' ' . __('highlighted', 'highlighter') . '
				</div>';
			if($atts['show-total-commenters'] && $total_commenters > 0) $out .= '
				<div class="highlighter-stat">
					<span class="stat-number">' . $total_commenters . '</span> ' . $plural_commenters . ' ' . __('added a note', 'highlighter') . '
				</div>';
			if($atts['show-top-highlight'] && $top_highlight > 0) $out .= '
				<div class="highlighter-stat">
					' . __('Top highlight: ','highlighter') . ' <span class="stat-number">' . $top_highlight . '</span> ' . $plural_top_highlight . '
				</div>';
			if($atts['show-top-highlight-text'] && !empty($top_highlight_text)) $out .= '
				<div class="highlighter-stat-text">
					<span class="highlighted-text-comment">' . $top_highlight_text . '</span> 
				</div>';
			if($atts['show-top-comment'] && $top_comment > 0) $out .= '
				<div class="highlighter-stat">
					' . __('Top note: ','highlighter') . ' <span class="stat-number">' . $top_comment . '</span> ' . $plural_top_comment . '
				</div>';
			if($atts['show-top-comment-text'] && !empty($top_comment_text)) $out .= '
				<div class="highlighter-stat-text">
					<span class="highlighted-text-comment">' . $top_comment_text . '</span> 
				</div>';
			$out .= '
			</div>
		</div>
		';
	}

	return $out;
}
add_shortcode( 'highlighter-stats', 'highlighter_stats' );

function highlighter_most_noted($atts) {
	$options = get_option( 'highlighter_settings' );	

	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'linked' => $options['most_noted_linked'],
		'title' => $options['most_noted_title'],
	), $atts, 'highlighter-most-noted' );

	$out = '';
	
	$top_comment_text = get_option( 'highlighter_top_comment_text' );
	$link = get_option( 'highlighter_top_comment_link' );

	# hide if no selection available
	$hide_all = false;
	if(empty($top_comment_text)) $hide_all = true;

	if(!$hide_all) {
		$out .= '
		<div class="highlighter-stats-wrapper site highlighter-most-noted">
			<div class="highlighter-stats-title">
				<img src="' . plugin_dir_url( __FILE__ ) . 'img/stats-toggle.png" />
				' . $atts['title'] . '
			</div>
			<div class="highlighter-stats">
				<div class="highlighter-stat-text">';
				if($atts['linked'] && !empty($link)) $out .= '<a href="' . $link . '">';
					$out .= '
					<span class="highlighted-text-comment">
						' . $top_comment_text . '
					</span>';
				if($atts['linked'] && !empty($link)) $out .= '</a>';
				$out .= '
				</div>
			</div>
		</div>
		';
	}

	return $out;
}
add_shortcode( 'highlighter-most-noted', 'highlighter_most_noted' );

function highlighter_most_highlighted($atts) {
	$options = get_option( 'highlighter_settings' );

	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'linked' => $options['most_highlighted_linked'],
		'title' => $options['most_highlighted_title'],
	), $atts, 'highlighter-most-highlighted' );

	$out = '';
	
	$top_highlight_text = get_option( 'highlighter_top_highlight_text' );
	$link = get_option( 'highlighter_top_highlight_link' );

	# hide if no selection available
	$hide_all = false;
	if(empty($top_highlight_text)) $hide_all = true;

	if(!$hide_all) {
		$out .= '
		<div class="highlighter-stats-wrapper site highlighter-most-noted">
			<div class="highlighter-stats-title">
				<img src="' . plugin_dir_url( __FILE__ ) . 'img/stats-toggle.png" />
				' . $atts['title'] . '
			</div>
			<div class="highlighter-stats">
				<div class="highlighter-stat-text">';
				if($atts['linked'] && !empty($link)) $out .= '<a href="' . $link . '">';
					$out .= '
					<span class="highlighted-text-comment">
						' . $top_highlight_text . '
					</span>';
				if($atts['linked'] && !empty($link)) $out .= '</a>';
				$out .= '
				</div>
			</div>
		</div>
		';
	}

	return $out;
}
add_shortcode( 'highlighter-most-highlighted', 'highlighter_most_highlighted' );

function highlighter_inked($atts) {

	$options = get_option( 'highlighter_settings' );
	$types = isset($options['inked_enable']) ? $options['inked_enable'] : array();
	$types_cpts = isset($options['inked_cpts']) ? $options['inked_cpts'] : array();
	$types = array_merge($types, $types_cpts);

	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'num' => $options['inked_num'],
		'title' => $options['inked_title'],
		'types' => $types,
		'counts' => $options['inked_counts']
	), $atts, 'highlighter-inked' );

	return highlighter_shortcode($atts, 'total_highlights');

}
add_shortcode( 'highlighter-inked', 'highlighter_inked' );

function highlighter_noteworthy($atts) {

	$options = get_option( 'highlighter_settings' );
	$types = isset($options['noteworthy_enable']) ? $options['noteworthy_enable'] : array();
	$types_cpts = isset($options['noteworthy_cpts']) ? $options['noteworthy_cpts'] : array();
	$types = array_merge($types, $types_cpts);

	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'num' => $options['noteworthy_num'],
		'title' => $options['noteworthy_title'],
		'types' => $types,
		'counts' => $options['noteworthy_counts']
	), $atts, 'highlighter-noteworthy' );

	return highlighter_shortcode($atts, 'total_commenters');

}
add_shortcode( 'highlighter-noteworthy', 'highlighter_noteworthy' );

function highlighter_trending($atts) {

	$options = get_option( 'highlighter_settings' );
	$types = isset($options['trending_enable']) ? $options['trending_enable'] : array();
	$types_cpts = isset($options['trending_cpts']) ? $options['trending_cpts'] : array();
	$types = array_merge($types, $types_cpts);

	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'num' => $options['trending_num'],
		'title' => $options['trending_title'],
		'types' => $types,
		'counts' => $options['trending_counts']
	), $atts, 'highlighter-trending' );

	return highlighter_shortcode($atts, 'total_highlighters');

}
add_shortcode( 'highlighter-trending', 'highlighter_trending' );

function highlighter_bold($atts) {

	$options = get_option( 'highlighter_settings' );
	$types = isset($options['bold_enable']) ? $options['bold_enable'] : array();
	$types_cpts = isset($options['bold_cpts']) ? $options['bold_cpts'] : array();
	$types = array_merge($types, $types_cpts);

	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'num' => $options['bold_num'],
		'title' => $options['bold_title'],
		'types' => $types,
		'counts' => $options['bold_counts']
	), $atts, 'highlighter-bold' );

	return highlighter_shortcode($atts, 'top_comment');

}
add_shortcode( 'highlighter-bold', 'highlighter_bold' );

function highlighter_memorable($atts) {

	$options = get_option( 'highlighter_settings' );
	$types = isset($options['memorable_enable']) ? $options['memorable_enable'] : array();
	$types_cpts = isset($options['memorable_cpts']) ? $options['memorable_cpts'] : array();
	$types = array_merge($types, $types_cpts);

	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'num' => $options['memorable_num'],
		'title' => $options['memorable_title'],
		'types' => $types,
		'counts' => $options['memorable_counts']
	), $atts, 'highlighter-memorable' );

	return highlighter_shortcode($atts, 'top_highlight');

}
add_shortcode( 'highlighter-memorable', 'highlighter_memorable' );


# function to return top x posts sorted by passed in custom meta field
function highlighter_shortcode($atts, $meta_key) {

	$out = '';
	$found = '';

	if(!isset($atts['types'])) $atts['types'] = explode(',', $atts['types']);
	
	// setup the query
	$args = array( 
		'post_type'       => $atts['types'],
		'post_status'	  => 'publish',
		'posts_per_page'  => $atts['num'],
		'orderby'   	  => 'meta_value_num',
		//'meta_key'  	  => $meta_key,
		'meta_query' => array(
		    array(
		        'key' => $meta_key,
		        'value'   => 0,
		        'compare' => '>'
		    )
		)
	);

	global $post;

	$posts = get_posts( $args );
	foreach($posts as $post) : setup_postdata($post);
		$count = get_post_meta( $post->ID, $meta_key, true );

		$found .= '
		<div class="highlighter-stat-text">';
			$found .= '<a href="' . get_the_permalink($post) . '">';
			if($atts['counts']) $found .= '<span class="count">' . $count . '</span>';
			$found .= $post->post_title . '</a>
		</div>';

	endforeach;
	
	wp_reset_query();

	if(!empty($found)) {
		$out .= '
		<div class="highlighter-stats-wrapper site highlighter-shortcode">
			<div class="highlighter-stats-title">
				<img src="' . plugin_dir_url( __FILE__ ) . 'img/stats-toggle.png" />
				' . $atts['title'] . '
			</div>
			<div class="highlighter-stats">
				' . $found . '
			</div>
		</div>
		';
	}

	return $out;
}


function highlighter_view($atts) {

	$options = get_option( 'highlighter_settings' );
	$userid = (string) get_current_user_id();

	# set defaults based on plugin options
	$atts = shortcode_atts( array(
		'userid' => $userid,
	), $atts, 'highlighter-view' );

	if($atts['userid']!=='all') $atts['userid'] = 'u' . $atts['userid'] . 'id';

	$out = '';
	$found = false;

	// determine which post types are active
    $types = isset($options['highlighter_enable']) ? $options['highlighter_enable'] : array();
	$types_cpts = isset($options['highlighter_cpts']) ? $options['highlighter_cpts'] : array();
	if(isset($options['highlighter_cpts_manual'])) {
		$val = preg_replace('/\s+/', '', $options['highlighter_cpts_manual']);
		if(strpos($val, ',') !== false) {
			$types_cpts_manual = explode(',', $val);
		} else {
			$types_cpts_manual = array($val);
		}
	}
	$types = array_merge($types, $types_cpts, $types_cpts_manual);
	$args = array( 
		'post_type'       => $types,
		'post_status'	  => 'publish',
		'numberposts'     => -1
	);

	global $post;

	$posts = get_posts( $args );
	if(!empty($posts)) {

		$out .= '<div class="highlighter-view-wrapper highlighter-shortcode">';

		foreach($posts as $post) : setup_postdata($post);

			$highlights = '';
			$content = get_the_content();
			$permalink = get_permalink(); 
			remove_filter( 'the_title', 'highlighter_stats_title', 10, 2);
			$title = get_the_title();
			add_filter( 'the_title', 'highlighter_stats_title', 10, 2);
			//remove_filter( 'the_content', 'highlighter_wrap_content' );
			//$content = apply_filters('the_content', $content); // this line was causing a timeout
			//add_filter( 'the_content', 'highlighter_wrap_content', 10, 2 ); 

			$result = array();

			libxml_use_internal_errors(true); // we don't care about invalid html
		    $classname = "highlighted-text";
		    $domdocument = new DOMDocument();
		    //$domdocument->loadHTML($content);
		    $domdocument->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		    $a = new DOMXPath($domdocument);

		    $spans = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

		    if(!empty($spans)) {

			    foreach($spans as $span) {

			    	$userids = $span->getAttribute('data-userid'); 
			    	
			    	if ($atts['userid'] == 'all' || strpos($userids, $atts['userid']) !== false)
			    		$highlights .= '
			    		<a href="' . $permalink . '" class="highlighter-view">
				    		' . $domdocument->saveHTML($span) . '
				    	</a>
				    	';
			    }

			    if(!empty($highlights)) {
				    $out .= '
		    		<div class="highlighter-view-post">
		    			<div class="highlighter-view-title">
		    				<a href="' . $permalink . '"><span>' . __('from','highlighter') . '</span> ' . $title . '</a>
		    			</div>
		    			' . $highlights . '
		    		</div>';
		    		$found = true;
				}

			}

		endforeach;

		wp_reset_query();

		if(!$found) $out .= '<span class="highlighter-nothing">' . __('No highlights found', 'highlighter') . '</span>';
		$out .= '</div>';

	}

	return $out;

}
add_shortcode( 'highlighter-view', 'highlighter_view' );

# enable shortcodes in text widgets
$options = get_option( 'highlighter_settings' );
if($options['enable_widget_shortcodes']) add_filter('widget_text','do_shortcode');


# calling shortcodes from filters 
add_action('loop_start','highlighter_stats_conditional_title');
function highlighter_stats_conditional_title($query){
	global $wp_query;
	if($query === $wp_query) {
		add_filter( 'the_title', 'highlighter_stats_title', 10, 2);
	} else {
		remove_filter( 'the_title', 'highlighter_stats_title', 10, 2);
	}
}
function highlighter_stats_title( $title, $post_id ) {
	$options = get_option( 'highlighter_settings' );
	$placement = $options['stats_placement'];
    global $post;
    if($post->ID == $post_id && is_single()) {
	    if($placement=='before_title') {
	    	$title = do_shortcode('[highlighter-stats]') . $title;
	    }elseif($placement=='after_title') {
	    	$title = $title . do_shortcode('[highlighter-stats]');
	    }
    }
    return $title;
}
add_filter( 'the_content', 'highlighter_stats_content', 10, 2);
function highlighter_stats_content( $content ) {
	$options = get_option( 'highlighter_settings' );
	$placement = $options['stats_placement'];
	if(is_single()) {
	    if($placement=='before_content') {
	    	$content = do_shortcode('[highlighter-stats]') . $content;
	    }elseif($placement=='after_content') {
	    	$content = $content . do_shortcode('[highlighter-stats]');
	    }
	}
    return $content;
}


/* BEGIN AJAX AUTH */

function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
  	// Call auth_user_login
	auth_user_login($_POST['username'], $_POST['password'], 'Login'); 
	
    die();
}

function ajax_register(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-register-nonce', 'security' );
		
    // Nonce is checked, get the POST data and sign user on
    $info = array();
  	$info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['first_name'] = $info['user_login'] = sanitize_user($_POST['username']) ;
    $info['user_pass'] = sanitize_text_field($_POST['password']);
	$info['user_email'] = sanitize_email( $_POST['email']);
	
	// Register the user
    $user_register = wp_insert_user( $info );
 	if ( is_wp_error($user_register) ){	
		$error  = $user_register->get_error_codes()	;
		
		if(in_array('empty_user_login', $error))
			echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login', 'highlighter'))));
		elseif(in_array('existing_user_login',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('This username is already registered.', 'highlighter')));
		elseif(in_array('existing_user_email',$error))
        echo json_encode(array('loggedin'=>false, 'message'=>__('This email address is already registered.', 'highlighter')));
    } else {
	  auth_user_login($info['nickname'], $info['user_pass'], 'Registration');       
    }

    die();
}

function auth_user_login($user_login, $password, $login)
{
	$info = array();
    $info['user_login'] = $user_login;
    $info['user_password'] = $password;
    $info['remember'] = true;
	
	$user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
		echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.', 'highlighter')));
    } else {
		wp_set_current_user($user_signon->ID); 
        echo json_encode(array('loggedin'=>true, 'message'=>__($login.' successful, redirecting...', 'highlighter')));
    }
	
	die();
}

function ajax_forgotPassword(){
	 
	// First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-forgot-nonce', 'security' );
	
	global $wpdb;
	
	$account = $_POST['user_login'];
	
	if( empty( $account ) ) {
		$error = __('Enter an username or e-mail address.', 'highlighter');
	} else {
		if(is_email( $account )) {
			if( email_exists($account) ) 
				$get_by = 'email';
			else	
				$error = __('There is no user registered with that email address.', 'highlighter');			
		}
		else if (validate_username( $account )) {
			if( username_exists($account) ) 
				$get_by = 'login';
			else	
				$error = __('There is no user registered with that username.', 'highlighter');				
		}
		else
			$error = __('Invalid username or e-mail address.', 'highlighter');		
	}	
	
	if(empty ($error)) {
		// lets generate our new password
		//$random_password = wp_generate_password( 12, false );
		$random_password = wp_generate_password();

			
		// Get user data by field and data, fields are id, slug, email and login
		$user = get_user_by( $get_by, $account );
			
		$update_user = wp_update_user( array ( 'ID' => $user->ID, 'user_pass' => $random_password ) );
			
		// if  update user return true then lets send user an email containing the new password
		if( $update_user ) {
			
			//$from = 'WRITE SENDER EMAIL ADDRESS HERE'; // Set whatever you want like mail@yourdomain.com
			
			if(!(isset($from) && is_email($from))) {		
				$sitename = strtolower( $_SERVER['SERVER_NAME'] );
				if ( substr( $sitename, 0, 4 ) == 'www.' ) {
					$sitename = substr( $sitename, 4 );					
				}
				$from = 'admin@'.$sitename; 
			}
			
			$to = $user->user_email;
			$subject = __('Your new password', 'highlighter');
			$sender = __('From: ', 'highlighter').get_option('name').' <'.$from.'>' . "\r\n";
			
			$message = __('Your new password is: ', 'highlighter').$random_password;
				
			$headers[] = 'MIME-Version: 1.0' . "\r\n";
			$headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers[] = "X-Mailer: PHP \r\n";
			$headers[] = $sender;
				
			$mail = wp_mail( $to, $subject, $message, $headers );
			if( $mail ) 
				$success = __('Check your email address for your new password.', 'highlighter');
			else
				$error = __('System is unable to send you mail containg your new password.', 'highlighter');						
		} else {
			$error = __('Oops! Something went wrong while updaing your account.', 'highlighter');
		}
	}
	
	if( ! empty( $error ) )
		//echo '<div class="error_login"><strong>ERROR:</strong> '. $error .'</div>';
		echo json_encode(array('loggedin'=>false, 'message'=>__($error)));
			
	if( ! empty( $success ) )
		//echo '<div class="updated"> '. $success .'</div>';
		echo json_encode(array('loggedin'=>false, 'message'=>__($success)));
				
	die();
}

// Enable the user with no privileges to run ajax_login() in AJAX
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
// Enable the user with no privileges to run ajax_register() in AJAX
add_action( 'wp_ajax_nopriv_ajaxregister', 'ajax_register' );
// Enable the user with no privileges to run ajax_forgotPassword() in AJAX
add_action( 'wp_ajax_nopriv_ajaxforgotpassword', 'ajax_forgotPassword' );


// add custom metaboxes
function highlighter_custom_meta() {
    $options = get_option( 'highlighter_settings' );

	// what parts of the site to allow highlighter functionality
	$types = !empty($options['highlighter_enable']) ? $options['highlighter_enable'] : array();
	$types_cpts = !empty($options['highlighter_cpts']) ? $options['highlighter_cpts'] : array();
	if(isset($options['highlighter_cpts_manual'])) {
		$val = preg_replace('/\s+/', '', $options['highlighter_cpts_manual']);
		if(strpos($val, ',') !== false) {
			$types_cpts_manual = explode(',', $val);
		} else {
			$types_cpts_manual = array($val);
		}
	}
	$types = array_merge($types, (array)$types_cpts, $types_cpts_manual);

	if(isset($types)) {
		if(is_array($types)) {
		    foreach ($types as $type) {
		        add_meta_box(
		            'highlighter_custom',           // Unique ID
		            'Highlighter Pro',  		// Box title
		            'highlighter_custom_html',  	// Content callback, must be of type callable
		            $type,                // Post type
		            'side',					// Context
		            'default'				// Priority
		        );
		    }
		}
	}
}
add_action('add_meta_boxes', 'highlighter_custom_meta');

function highlighter_custom_html($post) {
	wp_nonce_field( basename( __FILE__ ), 'highlighter_nonce' );
    $highlighter_stored_meta = get_post_meta( $post->ID );
    ?>
    <p>
    <label for="highlighter-disable">
        <input type="checkbox" name="highlighter-disable" id="highlighter-disable" value="yes" <?php if ( isset ( $highlighter_stored_meta['highlighter-disable'] ) ) checked( $highlighter_stored_meta['highlighter-disable'][0], 'yes' ); ?> />
        <?php _e( 'Disable', 'highlighter' )?>
    </label>
    </p>
    <?php
}

// save the custom meta
function highlighter_meta_save( $post_id ) {
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'highlighter_nonce' ] ) && wp_verify_nonce( $_POST[ 'highlighter_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
	if( isset( $_POST[ 'highlighter-disable' ] ) ) {
	    update_post_meta( $post_id, 'highlighter-disable', 'yes' );
	} else {
	    update_post_meta( $post_id, 'highlighter-disable', '' );
	}

}
add_action( 'save_post', 'highlighter_meta_save' );


?>