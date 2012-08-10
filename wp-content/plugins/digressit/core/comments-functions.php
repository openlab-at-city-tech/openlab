<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments-functions.php' == basename($_SERVER['SCRIPT_FILENAME'])):
	die (':)');
endif;

add_action('init', 'commentbrowser_flush_rewrite_rules' );
add_filter('query_vars', 'commentbrowser_query_vars' );
add_action('generate_rewrite_rules', 'commentbrowser_add_rewrite_rules' );
add_action('template_redirect', 'commentbrowser_template_redirect' );
//add_action('wp_print_styles', 'comments_wp_print_styles');
//add_action('wp_print_scripts', 'comments_wp_print_scripts' );
add_action('public_ajax_function', 'add_comment_ajax');

add_action('widgets_init', create_function('', 'return register_widget("CommentBrowserLinks");'));

add_action('add_commentbrowser', 'commentbrowser_comments_by_section');
add_action('add_commentbrowser', 'commentbrowser_comments_by_user'); //DEPRECATED
add_action('add_commentbrowser', 'commentbrowser_comments_by_contributor');
add_action('add_commentbrowser', 'commentbrowser_general_comments');



// Flush your rewrite rules if you want pretty permalinks
function commentbrowser_flush_rewrite_rules() {
    global $wp_rewrite;

    $wp_rewrite->flush_rules();
}


// Create some extra variables to accept when passed through the url
function commentbrowser_query_vars( $query_vars ) {
    $myvars = array('commentbrowser_function', 'commentbrowser_params');
    $query_vars = array_merge( $query_vars, $myvars );
    return $query_vars;
}




// Create a rewrite rule if you want pretty permalinks
function commentbrowser_add_rewrite_rules( $wp_rewrite ) {


	$wp_rewrite->add_rewrite_tag( "%commentbrowser_function%", "(general-comments|comments-by-user|comments-by-contributor|comments-by-section|comments-by-tag)", "commentbrowser_function=" );
	$wp_rewrite->add_rewrite_tag( "%commentbrowser_params%", "(.+?)", "commentbrowser_params=" );

	$urls = array('%commentbrowser_function%', '%commentbrowser_function%/%commentbrowser_params%');
	foreach( $urls as $url ) {
		$rule = $wp_rewrite->generate_rewrite_rules($url, EP_NONE, false, false, false, false, false);
		$wp_rewrite->rules = array_merge( $rule, $wp_rewrite->rules );
	}
	return $wp_rewrite;
}

// Let's echo out the content we are looking to dynamically grab before we load any template files
function commentbrowser_template_redirect() {
    global $wp, $wpdb, $current_user, $current_browser_section, $is_commentbrowser;

	//var_dump($wp->query_vars);
	$commentbrowser_function = "commentbrowser_" . str_replace('-','_',$wp->query_vars['commentbrowser_function']);
	$commentbrowser_params =  $wp->query_vars['commentbrowser_params'];
	
	if( has_action('add_commentbrowser', $commentbrowser_function) && function_exists($commentbrowser_function)) :
		$is_commentbrowser = true;
		if(file_exists(DIGRESSIT_THEMES_DIR .get_current_theme(). '/comments-browser.php')){
			
		}
		else{
			include(DIGRESSIT_THEMES_DIR . '/digressit-default/comments-browser.php');
		}
		exit;
	endif;
	
}






/*
function comments_wp_print_scripts(){		
	if(is_single()):
		wp_enqueue_script('digressit.comments', get_digressit_media_uri('js/digressit.comments.js'), 'jquery', false, true );
	endif;
}
*/






function add_comment_ajax($request_params){
	//extract($request_params);
	global $wpdb, $current_user, $blog_id;

	$time = current_time('mysql', $gmt = get_option('gmt_offset')); 
	$time_gmt = current_time('mysql', $gmt = 0); 
	
	$display_name = isset($current_user->display_name) ? $current_user->display_name : $request_params['display_name'];
	$user_email = isset($request_params['user_email']) ? $request_params['user_email'] : $current_user->user_email;
	$user_ID = isset($current_user->ID) ? $current_user->ID : '';

//	var_dump($display_name);
	
	$comment_moderation = get_option('comment_moderation');
	
	
	$data = array(
	    'comment_post_ID' => $request_params['comment_post_ID'],
	    'comment_author' => $display_name,
	    'comment_author_email' => $user_email,
	    'comment_content' => $request_params['comment'],
	    'comment_parent' => $request_params['comment_parent'],
	    'user_id' => $user_ID,
	    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
	    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
	    'comment_date' => $time,
	    'comment_date_gmt' => $time_gmt,
	    'comment_approved' => (($comment_moderation) ? 0 : 1), //TODO: we kinda have to approve automatically. because we don't have a way to notify user of approval yet
	);
	
	
	

	if(strlen($display_name) < 2){
		die(json_encode(array('status' => 0, "message" => 'Please enter a valid name.')));				
	}

	if(!is_email($user_email)){
		die(json_encode(array('status' => 0, "message" => 'Not a valid email.')));				
	}

	if(strlen($request_params['comment']) < 2){
		die(json_encode(array('status' => 0, "message" => 'Your comment is too short.')));				
	}
	
	/*
	if(digressit_live_spam_check_comment( $data )){
		die(json_encode(array('status' => 0, "message" => 'Your comment looks like spam. You might want to try again with out links')));						
	}
	*/


	
	if($wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) as comment_exists FROM $wpdb->comments WHERE comment_author_email = $user_email AND $comment_content = %s " , $user_email, $request_params['comment']) ) > 0){
		die(json_encode(array('status' => 0, "message" => 'This comment already exists')));		
	}

	
	
	
	$comment_ID = wp_insert_comment($data);					
	
	$request_params['comment_ID'] = $comment_ID;
	
	
	//TODO: we are moving away from the extra column
	$result = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->comments SET comment_text_signature = %s WHERE comment_ID = %d", $request_params['selected_paragraph_number'], $comment_ID) );




	//TODO: FOR FUTURE VERSIONS we will just use comment meta
	add_metadata('post', $request_params['comment_post_ID'], 'comment_text_signature', $request_params['selected_paragraph_number'], true);

	add_metadata('comment', $comment_ID, 'paragraph', $request_params['selected_paragraph_number'], true) ;

	$comment_date = date('m/d/y');
	
	$message['comment_ID'] = $comment_ID;
	$message['comment_parent'] = $request_params['comment_parent'];
	$message['comment_date'] = $comment_date;
	$message['comment_author'] = $display_name;
	
	$status = 1;

	//an extra hook
	do_action('add_comment_ajax_metadata', $request_params);
	
	$commentcount = count(get_approved_comments($request_params['comment_post_ID']));
	delete_metadata('post', $request_params['comment_post_ID'], 'comment_count');
	add_metadata('post', $request_params['comment_post_ID'], 'comment_count', $commentcount, true);
	$message['comment_count'] = $commentcount;
	$message['paragraph_comment_count'] = count(get_approved_comments_for_paragraph($request_params['comment_post_ID'],  $request_params['selected_paragraph_number']));


	ob_start();
	$data = get_comment($comment_ID);	
	$data->blog_id = $blog_id;
	$data->ajax_call = $request_params['comment_parent'] ? true : false;
	
	$data->comment_text_signature = $request_params['selected_paragraph_number'];
	call_user_func(get_digressit_comments_function(), $data, null, null);
	$message['comment_response'] = ob_get_contents();
	ob_end_clean();
	
	
	die(json_encode(array('status' => $status, "message" => $message)));
	
	
	
}

function standard_digressit_comment_parser($comment, $args, $depth) {
 	global $current_page_template, $blog_id, $current_user; 

	$GLOBALS['comment'] = $comment; 	
	$classes = null;
	?>
	<?php $current_blog_id = is_single() ? $blog_id : $comment->blog_id; ?>
	<?php $paragraphnumber = is_numeric($comment->comment_text_signature) ? $comment->comment_text_signature : 0; ?>
	<?php $force_depth = $comment->ajax_call ? " depth-2 " : ''; ?>
	<?php $classes .= " paragraph-".$paragraphnumber." " .$force_depth; ?>
		
	<div <?php comment_class($classes); ?> id="comment-<?php echo $current_blog_id ?>-<?php comment_ID() ?>">
		<div id="div-comment-<?php echo $current_blog_id; ?>-<?php comment_ID(); ?>" class="comment-body">
			
			<div class="comment-header">
				
				<div class="comment-author vcard">

					<?php echo get_avatar( $comment, 15 ); ?>


					<?php

					if($comment->user_id){
						$comment_user = get_userdata($comment->user_id); 
						$profile_url = get_bloginfo('home')."/comments-by-contributor/" . $comment_user->user_login;
						echo "<a href='$profile_url'>$comment_user->display_name</a>";
					}
					else{
						$profile_url = get_bloginfo('home')."/comments-by-contributor/" . $comment->comment_author;						
						echo "<a href='$profile_url'>$comment->comment_author</a>";						
					}
					?>
					

				</div>
				
				<div class="comment-meta">
					
					<?php if(is_single()):  ?>
					<?php global $blog_id; ?>
						<span class="comment-blog-id" value="<?php echo $blog_id; ?>"></span>
					<?php else: ?>
						<span class="comment-blog-id" value="<?php echo $comment->blog_id; ?>"></span>
					<?php endif; ?>
					<span class="comment-id" value="<?php comment_ID(); ?>"></span>
					<span class="comment-parent" value="<?php echo $comment->comment_parent; ?>"></span>
					<span class="comment-paragraph-number" value="<?php echo $comment->comment_text_signature; ?>"></span>


					<span class="comment-date"><a href="<?php get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $current_blog_id ?>-<?php comment_ID() ?>"><?php comment_date('n/j/Y'); ?></a></span>
					

					
					<div class="comment-goto">
						<a href="<?php echo get_permalink($comment->comment_post_ID); ?>#<?php echo $comment->comment_text_signature; ?>">GO TO TEXT</a>
					</div>


					<?php do_action('digressit_custom_meta_data'); ?>

										
				</div>
			</div>
			<div class="comment-text">
				
				<?php 
				if ($comment->comment_approved == '0'): ?>
					<p><i>This comment is awaiting moderation.</i></p><?php
				else:
					comment_text();
				endif;
				
				?>						
				
			</div>
			
			
			<?php if(($depth < get_option('thread_comments_depth') || is_null($comment->comment_parent)) && (is_user_logged_in() || !get_option('comment_registration')) && is_single()): ?>
			<div class="comment-reply comment-hover small-button" value="<?php comment_ID(); ?>">reply</div>
			<?php endif; ?>

			<?php do_action('digressit_custom_comment_footer'); ?>

			<div class="comment-respond">
			</div>
			
		</div>
	</div>
	

	<?php
}


function digressit_comment_form(){
global $blog_id;
?>

<?php if(function_exists('display_recaptcha')):?>
<form method="post" action="<?php bloginfo('url') ?>/wp-comments-post.php" id="add-comment">
<?php else: ?>
<form method="post" action="/" id="add-comment">
<?php endif;?>

	<?php if(!is_user_logged_in()): ?>


		<?php if(function_exists('display_recaptcha')):?>
			<p><input type="text" class="comment-field-area" id="display_name"  name="author" value="Your Name" ><p>
			<p><input type="text" class="comment-field-area" id="user_email" name="email" value="Email"></p>

		<?php else: ?>

			<p><input type="text" class="comment-field-area" id="display_name"  name="display_name" value="Your Name" ><p>
			<p><input type="text" class="comment-field-area" id="user_email" name="user_email" value="Email"></p>

		<?php endif;?>
		
		
		
	<?php endif; ?>
	<div id="textarea-wrapper">
		<div class="left"></div>
		<div class="right">
		<textarea name="comment" class="comment-textarea comment-collapsed" id="comment">Click here add a new comment...</textarea>
		</div>
	</div>

	<input name="blog_id" type="hidden"  value="<?php echo $blog_id; ?>" />
	<input name="selected_paragraph_number" type="hidden" id="selected_paragraph_number"  value="0" />

	<div id="submit-wrapper">
		<div name="cancel-response" id="cancel-response" class="button link">Cancel</div>
		<?php if(function_exists('display_recaptcha')):?>
		<input type="submit" class="recaptcha-submit" name="submit" id="submit" value="submit">
		<?php else: ?>
		<div name="submit" id="submit-comment"  class="submit ajax"><div class="loading-bars"></div>Submit Comment</div>
		<?php endif; ?>
	</div>
	<?php comment_id_fields(); ?>
	<?php do_action('comment_form', $post->ID); ?>
	<?php do_action('digressit_after_comment_form'); ?>
</form>
<?php
}


function digressit_live_spam_check_comment( $comment ) {
	global $akismet_api_host, $akismet_api_port;
	
	if(function_exists('akismet_verify_key')){
	
		if(akismet_verify_key(akismet_get_key())){
			$comment['user_ip']    = $_SERVER['REMOTE_ADDR'];
			$comment['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$comment['referrer']   = $_SERVER['HTTP_REFERER'];
			$comment['blog']       = get_option('home');
			$comment['blog_lang']  = get_locale();
			$comment['blog_charset'] = get_option('blog_charset');
			$comment['permalink']  = get_permalink($comment['comment_post_ID']);
	
			$comment['user_role'] = akismet_get_user_roles($comment['user_ID']);

			$ignore = array( 'HTTP_COOKIE' );

			foreach ( $_SERVER as $key => $value )
				if ( !in_array( $key, $ignore ) && is_string($value) )
					$comment["$key"] = $value;

			$query_string = '';
			foreach ( $comment as $key => $data )
				$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';

			$response = akismet_http_post($query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
			if ( 'true' == $response[1] ) {
				return true;
			}
			return false;
		}
	}
	
	return false;
}

function get_comments_toolbar(){
	do_action('comments_toolbar');
}

function indexOf($needle, $haystack)
{
	if(($index =array_search($needle,$haystack)) !==false)
		return $index;
	else
		return -1;
}

function list_posts($args = array('number' => -1, 'category_name' => null ) )
{
	global $wp;

	//var_dump('numberposts='.$args['number']."&category_name=".$args['category_name']);
	//".$args['category_name']
	$myposts = get_posts('order=ASC&orderby=post_date&numberposts='.$args['number']."&category_name=".$args['category_name']);
	
	?>
	
	<ol class="navigation">
	<?php
	foreach($myposts as $post): ?>
	<?php
	$permalink = get_bloginfo('siteurl')."/".$wp->query_vars['commentbrowser_function'].'/'.$post->ID;
	?>
	<li><a href="<?php echo $permalink; ?>"><?php echo get_the_title($post->ID); ?> (<?php echo get_post_comment_count($post->ID, null, null, null); ?>)</a></li>
	<?php endforeach;
	?>
	</ol>
	<?php
}



function list_users()
{
	global $wp;
	$users = get_users_who_have_commented();



	?>
	<ol class="navigation">
	<?php
	foreach($users as $user) :
		$permalink = get_bloginfo('siteurl')."/".$wp->query_vars['commentbrowser_function'].'/'.$user->ID;
		?>
		<li>
			<?php

			if($user->user_id){
				$comment_user = get_userdata($user->ID); 
				$profile_url = get_bloginfo('home')."/comments-by-contributor/" . urlencode($user->user_login);
				echo "<a href='$profile_url'>$user->display_name ($user->comments_per_user)</a> ";						
			}
			else{
				$profile_url = get_bloginfo('home')."/comments-by-contributor/" . urlencode($user->comment_author);
				echo "<a href='$profile_url'>$user->comment_author ($user->comments_per_user)</a> ";						
			}
			?>			
		
		</li>
	<?php endforeach; 
	?>
	</ol>
	<?php

}




function list_general_comments()
{
	global $wp;
	

	$myposts = get_posts('order=ASC&orderby=post_date&numberposts=-1');
	
	?>
	
	<ol class="navigation">
	<?php
	foreach($myposts as $post): ?>
	<?php
	$permalink = get_bloginfo('siteurl')."/".$wp->query_vars['commentbrowser_function'].'/'.$post->ID;
	?>
	<li><a href="<?php echo $permalink; ?>"><?php echo get_the_title($post->ID); ?> (<?php echo count(get_approved_general_comments($post->ID)); ?>)</a></li>
	<?php endforeach;
	?>
	</ol>
	<?php
}




//the following were imported from CP1.4
function get_approved_general_comments($id){
	$approved_comments = get_approved_comments($id);
	
	$general_comments = null;

	foreach($approved_comments as $comment){
		if(!$comment->comment_text_signature){
			$general_comments[] = $comment;
		}
	}
	
	return $general_comments;

}

function getCommentCount($title)
{
	global $wpdb;
	$title = strip_tags($title);
	$title = addslashes($title);
	$sql = "SELECT * FROM $wpdb->comments, $wpdb->posts WHERE comment_post_ID=ID AND post_status='publish' AND comment_approved='1' AND post_name = '$title' AND post_status='publish'";
	$result = $wpdb->get_results($sql);
	return ( count($result) );
}

/* REDO THIS FUNCTION */
$comments_for_counting = null;
function get_post_comment_count($post_ID, $metatag = null, $metavalue = null, $only_approved = 1){
	global $wpdb, $comments_for_counting;
	
	if($only_approved == 1){
		$only_approved_sql = " AND c.comment_approved = '1' ";
	}
	else{
		$only_approved_sql = " AND (c.comment_approved = '1' OR c.comment_approved = '1' )";		
	}
	//echo "postid" . $post_ID;
	$sql = "SELECT * FROM $wpdb->comments c 
			WHERE c.comment_post_ID = $post_ID 
			$only_approved_sql
			AND c.comment_type = ''";	

	$comments_for_counting = $wpdb->get_results($sql);		

	//var_dump($sql);
	$count= 0;

	if($metatag){
		foreach($comments_for_counting as $c){
			$value = get_metadata('comment', $c->comment_ID, $metatag, true);
			if((int)$metavalue == (int)$value){
				$count++;					
			}
		}
		return $count;
	}
	
	return count($comments_for_counting);
	
}
function getCommentCountByCategory($cat)
{
	global $wpdb;
	$cat = strip_tags($cat);
	$cat = addslashes($cat);
	$sql = "SELECT * FROM $wpdb->posts, $wpdb->post2cat, $wpdb->categories  WHERE category_nicename = '$cat' AND cat_ID = category_id AND post_id = ID";


	$commentCategories = $wpdb->get_results($sql);
	$count = 0;
	foreach($commentCategories as $c)
	{
		$count += $c->comment_count;
	}
	
	return $count;
	
}



function get_users_who_have_commented()
{
	global $wpdb;
	$sql = "SELECT *, COUNT( * ) AS comments_per_user  FROM $wpdb->users u RIGHT JOIN $wpdb->comments c ON u.ID=c.user_id
					LEFT JOIN $wpdb->posts p ON p.ID=c.comment_post_ID
					WHERE c.comment_approved = 1 
					AND p.post_status = 'publish'
					AND c.comment_type = ''
					GROUP BY c.comment_author
					ORDER BY c.comment_author";
	
	
	//echo $sql;
	$results = $wpdb->get_results($sql);
	
	//var_dump($results);

	return $results;
}



function get_comments_from_user($id){
 	global $wpdb;	

     $id = urldecode($id);

	$results = null;
	if(is_numeric($id)){
		$sql = "SELECT c.*, u.*, p.post_name, p.post_title FROM $wpdb->comments c, $wpdb->users u, $wpdb->posts p  WHERE p.post_status='publish' AND c.user_id = u.ID AND u.ID=$id AND c.comment_post_ID = p.ID ORDER BY comment_ID DESC";
		$results = $wpdb->get_results($sql);
		
	}

	if(count($results) == 0){
		$sql = "SELECT c.*, p.post_name, p.post_title FROM $wpdb->comments c, $wpdb->posts p  WHERE p.post_status='publish' AND c.comment_author = '$id' AND c.comment_post_ID = p.ID ORDER BY comment_ID DESC";
		//echo $sql;
		$results = $wpdb->get_results($sql);
		
	}




	return $results;
	
}



function getContributorsWhoHaveCommented(){
	global $wpdb;
	$sql = "SELECT * , COUNT( * ) AS comments_per_user FROM $wpdb->usermeta m, $wpdb->comments c, $wpdb->users u, $wpdb->posts p WHERE  p.ID = c.comment_post_ID AND u.ID = m.user_id  AND p.post_status='publish' AND c.user_id = u.ID GROUP BY u.ID ORDER BY u.user_login";
	return $wpdb->get_results($sql);              
}

function getParentPosts(){
	global $wpdb;
	$sql = "SELECT * FROM `$wpdb->posts` WHERE post_status='publish' AND post_parent='0' AND post_type = 'post'";
	$result = $wpdb->get_results($sql);
	return $result;
}

/* this might be useless */
function getAllCommentCount(){
	global $wpdb;
	$sql = "SELECT COUNT(*) as count FROM $wpdb->comments c, $wpdb->posts p WHERE c.comment_approved = '1' AND c.comment_post_ID=p.ID AND p.post_type='post' AND p.post_status='publish'";
	$result = $wpdb->get_var($sql);
	return $result;
}

function get_all_comments($only_approved = true){
	global $wpdb;
	
	if($only_approved){
		$clause = "AND comment_approved='1'";
	}
	else{
		$clause = '';
	}
	
	$sql = "SELECT * FROM $wpdb->comments WHERE comment_type = '' " . $clause;
	return $wpdb->get_results($sql);
			
}


function getRecentComments($limit = 5, $cleaned = false){
	global $wpdb;
	$sql = null;
	if($cleaned){
		$sql = "SELECT c.comment_ID,  c.comment_author, c.comment_date, c.comment_content, c.comment_parent, c.comment_post_ID, c.comment_text_signature, p.ID  FROM $wpdb->comments c, $wpdb->posts p WHERE comment_post_ID=ID AND post_status='publish' AND comment_approved='1' ORDER BY comment_date DESC LIMIT $limit ";			
	}
	else{
		$sql = "SELECT * FROM $wpdb->comments, $wpdb->posts WHERE comment_post_ID=ID AND post_status='publish' AND comment_approved='1' ORDER BY comment_date DESC LIMIT $limit ";
	}
	return $wpdb->get_results($sql);               
}


function get_approved_comments_for_paragraph($post_id, $paragraph){
	$approved_comments = get_approved_comments($post_id);		
	$filtered = null;
	foreach($approved_comments as $comment){
		if($comment->comment_text_signature == $paragraph){
			$filtered[] = $comment;
		}
	}
	return $filtered;
}



function mu_get_all_comments($user_id = null, $blog_id = null){
	
	$rule_list = null;
	if($blog_id){
		$rule['blog_id']  = $blog_id;
		$rule_list = $rule;
	}
	else{
		if(function_exists('get_blog_list')){
			$rule_list = get_blog_list ( 0, 'all' );
		}
	}
	
	$comments = array();
	foreach($rule_list as $rule){
		switch_to_blog( $rule['blog_id']);
		
		
		if($user_id){
			$current_comments= get_comments_from_user($user_id);				
		}
		else{
			$current_comments= get_all_comments();
		}
		
		$comments = array_merge($comments, $current_comments);
		restore_current_blog();
	}		
	
	return $comments;
}



function mu_get_comments_from_user($user_id){
	$rule_list = get_rules();
	
	//var_dump($rule_list);

	$comments = array();
	foreach($rule_list as $rule){
		switch_to_blog( $rule['blog_id']);
		$current_comments= get_comments_from_user($user_id);
		$comments = array_merge($comments, $current_comments);
		restore_current_blog();
	}		
	
	return $comments;
}




class CommentBrowserLinks extends WP_Widget {
	/** constructor */
	function CommentBrowserLinks() {
		parent::WP_Widget(false, $name = 'Comment Browser Links');	
	}

	function widget($args = array(), $defaults) {		
		extract( $args );
		$options = get_option('digressit');
		?>
		<h4>Comment Browser</h4>
		<ul>
			<li><a href="<?php bloginfo('url'); ?>/comments-by-section"><?php _e($options['comments_by_section_label'],'digressit'); ?></a></li>
			<li><a href="<?php bloginfo('url'); ?>/comments-by-contributor"><?php _e($options['comments_by_users_label'],'digressit'); ?></a></li>
			<li><a href="<?php bloginfo('url'); ?>/general-comments"><?php _e($options['general_comments_label'],'digressit'); ?></a></li>
			<?php do_action('add_commentbrowser_link'); ?>
		</ul>
		<?php
    }

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		return $instance;
	}



	/** @see WP_Widget::form */
	function form($instance) {				
		global $blog_id, $wpdb;
		return $instance;
	}

}




function commentbrowser_comments_by_section(){
	global $wp;
	$options = get_option('digressit');

	echo "<h3>".__($options['comments_by_section_label'],'digressit')."</h3>";
	echo "<div class='comment-count-in-book'>There are ".getAllCommentCount()." comments in this document</div>";
	list_posts();
	return isset($wp->query_vars['commentbrowser_params']) ? get_comments('post_id='.$wp->query_vars['commentbrowser_params']) : array();
}

function commentbrowser_comments_by_user(){
	commentbrowser_comments_by_contributor();
}

function commentbrowser_comments_by_contributor(){
	global $wp;
	//var_dump($wp->query_vars);
	$options = get_option('digressit');
	echo "<h3>".__($options['comments_by_users_label'],'digressit')."</h3>";
	
	echo "<div class='comment-count-in-book'>There are ".getAllCommentCount()." comments in this document</div>";
    if(is_numeric($wp->query_vars['commentbrowser_params'])) :
        $curauth = get_user_by('id', $wp->query_vars['commentbrowser_params']);
    else :
        $curauth = get_userdatabylogin(urldecode($wp->query_vars['commentbrowser_params']));
    endif;


    if(isset($curauth->ID)){
		$identifier = $curauth->ID;
	}
	else{
		$identifier = substr($wp->query_vars['commentbrowser_params'], 0 , 50); //lets limit the length of this string to limit funny url stuff.
	}
	
	//var_dump($identifier);

	list_users();

	return get_comments_from_user($identifier);
}
function commentbrowser_general_comments(){
	global $wp;
	$options = get_option('digressit');
	echo "<h3>".__($options['general_comments_label'],'digressit')."</h3>";	
	echo "<div class='comment-count-in-book'>There are ".getAllCommentCount()." comments in this document</div>";
	list_general_comments();
	return get_approved_general_comments($wp->query_vars['commentbrowser_params']);
}


?>