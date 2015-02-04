<?php
/*
Plugin Name: Grader
Description: Enables administrators to grade posts, and for users and administrators to view their grades through gradebook-like interface
Author: Michael Porter
Version: 1.0
*/

/*********************
** OPTION GET METHODS
**********************/

function grader_get_grade_color()
{
	return get_option('grader_grade_color',"blue");
}

function grader_get_comment_color()
{
	return get_option('grader_comment_color',"");
}

function grader_get_warning_msg()
{
	return get_option('grader_warning_msg','<em><font color="#A8A8A8">Only instructors and the post&#39;s author can see the grade for a post.</font></em>');
}

function grader_get_hidden_comment_text()
{
	return get_option('grader_hidden_comment_text',"<i>This is a grade.</i>");
}

function grader_get_comment_delim()
{
	return get_option('grader_comment_delim','.');
}

function grader_get_token()
{
	return get_option('grader_grade_token','@grade');
}

function grader_get_allow_edits()
{
	return get_option('grader_allow_edits',false);
}

/*********************
** HELPER FUNCTIONS
**********************/

/*********************
** Tests if string ($haystack) starts with another string ($needle)
** @return boolean
**********************/
function grader_starts_with($haystack, $needle)
{
	return (substr($haystack,0,strlen($needle))==$needle)?TRUE:FALSE;
}

/*********************
** Can current user grade assignments
** @return boolean
**********************/
function grader_is_grader($userdata)
{
	global $wpdb;

	$user = new WP_User( $userdata->ID );
	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			if ($role=='administrator' || $role=='editor') return (TRUE);
	}
	return (FALSE);
}

/*********************
** Is the current user the author of the current post
** @return boolean
**********************/
function grader_is_post_author()
{
	global $user_ID;
	global $post;

	return 	($post->post_author == $user_ID)?TRUE:FALSE;
}

/*********************
** Parses the comment text into a grade
** @return array with keys for grade and comment if $str starts with grade token.  Otherwise false.
**********************/
function grader_parse_grade($str)
{
	$token = grader_get_token();
	if (grader_starts_with($str, $token)) {
		$txt = substr($str,strlen($token));
		$comment_start = strpos($txt, grader_get_comment_delim());
		return array (
			'grade' => trim(($comment_start === false)?$txt:substr($txt,0,$comment_start)),
			'comment' => trim(($comment_start === false)?'':substr($txt,$comment_start+1)),
		);
	}
	return false;
}

/*********************
** Gets the grade from a given comment
** @param $comment - object of type $comment
** @param $comment_id - optional id of comment, will overwrite $comment value
** @return array (see grader_parse_grade) if comment is grade, otherwise false.
**********************/
function grader_get_grade($comment, $comment_id = -1)
{
	if ($comment_id != -1) $comment = get_comment($comment_id);
	$grade = grader_parse_grade($comment->comment_content);
	return ($grade === false)?$grade:$grade['grade'];
}

/*********************
** Gets the comment object that has a post's grade
** @param $post - post object
** @return comment object if post is graded, otherwise false.
**********************/
function grader_get_grade_comment($post)
{
	$grade_comment = get_post_meta($post->ID,'_grade_comment',true);
	return ($grade_comment=='')?false:$grade_comment;
}

/*********************
** Sets the grade on the current post
** @param $id - the id of the comment with the grde
** @return grade value if comment is grade, otherwise false.
**********************/
function grader_set_grade($id)
{
    global $current_user;
	global $post;

	if (!grader_is_grader($current_user->data)) return false;
	$grade = grader_get_grade(NULL, $id);
	if ($grade === false) return false;
	$grade_comment_id = grader_get_grade_comment($post);
	if ($grade_comment_id === false) {
		add_post_meta($post->ID, '_grade_comment', $id, TRUE);
	} else {
		wp_delete_comment($grade_comment_id);
		update_post_meta($post->ID, '_grade_comment', $id);
	}
	return $grade;
}

/*********************
** Removes any comments that are grades from the list of comments
** @param initial list of comments
** @return array of comments
**********************/
function grader_filter_comments($comments)
{
    global $current_user;

	if (grader_is_grader($current_user->data) || grader_is_post_author()) return $comments;
	foreach ($comments as $key=>$comment) {
		if (grader_is_grader(get_userdata($comment->user_id))) {
			$grade = grader_get_grade($comment);
			if (!($grade===false)) unset($comments[$key]);
		}
	}
	return $comments;
}

/**
 * Prevent grade from getting posted to the activity stream.
 *
 * This is a hack: we short-circuit BP by telling it that the current post type
 * should not have its comments recorded
 */
function grader_prevent_bp_activity( $comment_id ) {
        $comment = get_comment( $comment_id );

        $grade = grader_parse_grade( $comment->comment_content );
        if ( $grade ) {
                remove_action( 'comment_post', 'bp_blogs_record_comment', 10 );
                remove_action( 'edit_comment', 'bp_blogs_record_comment', 10, 2 );
        }
}
add_action( 'comment_post', 'grader_prevent_bp_activity', 0 );
add_action( 'edit_comment', 'grader_prevent_bp_activity', 0 );

function grader_filter_bp_activity( $has_activities ) {
	global $activities_template;

	if ( $has_activities ) {
		foreach ( $activities_template->activities as $akey => $a ) {
			$grade = grader_parse_grade( $a->content );

			// Just remove it. Don't bother checking permissions
			if ( $grade ) {
				unset( $activities_template->activities[ $akey ] );
				$activities_template->total_activity_count--;
				$activities_template->activity_count--;
			}
		}

		$activities_template->activities = array_values( $activities_template->activities );

		if ( ! $activities_template->activity_count ) {
			$has_activities = false;
		}
	}

	return $has_activities;
}
add_filter( 'bp_has_activities', 'grader_filter_bp_activity' );

/*********************
** FILTERS and ACTIONS
**********************/

add_filter('comment_text', 'grader_comment_text');
add_filter('comment_text_rss', 'grader_comment_text');
add_filter('comment_excerpt', 'grader_comment_text');
/*********************
** Edits a comment
** @return If the comment is a grade and the user can see function re-formats based on user options.  If the comment is a grade and the
** user cannot see returns hidden comment text.  If the comment is not a grade returns unformatted content.
**********************/
function grader_comment_text($content)
{

    global $current_user;
	global $comment;

	$grade = grader_parse_grade($content);
	$comment_author = get_userdata($comment->user_id);
	if ($grade===false || !grader_is_grader($comment_author)) return $content; // not a grade - not an issue
	If (grader_is_post_author() || grader_is_grader($current_user->data)) {
		return '<p><font color="'.grader_get_grade_color().'">GRADE:&nbsp;' . $grade['grade'] . '</font></p><p><font color="'.grader_get_comment_color().'">'.$grade['comment']."</p></font><p>".grader_get_warning_msg().'</p>';
	} else {
	    return grader_get_hidden_comment_text();
	}
}

/*********************
** Remove any grade comments the user should not see
**********************/
function grader_comments_array($comments)
{
	return grader_filter_comments($comments);
}
add_filter('comments_array','grader_comments_array');

/**
 * Filter any comments the user is not supposed to see
 *
 * grader_comments_array() handles this at the level of the comment
 * template. The current function goes directly into WP_Comment_Query
 */
function grader_filter_the_comments( $comments ) {
	return grader_filter_comments($comments);
}
add_filter( 'the_comments', 'grader_filter_the_comments' );

add_filter ('get_comments_number','grader_get_comments_number');
/*********************
** The number of comments from grader_comments_array
**********************/
function grader_get_comments_number($number)
{
	global $post;
	$comments = get_comments(array('post_id'=>($post->ID)));
	return sizeof(grader_filter_comments($comments));
}

add_action ('comment_post','grader_comment_post');
add_action ('edit_comment','grader_comment_post');
/*********************
** Updates grade
**********************/
function grader_comment_post($comment_id)
{
	grader_set_grade($comment_id);
}

add_action ('delete_comment','grader_delete_comment');
/*********************
** Delete grade and remove meta data
**********************/
function grader_delete_comment($id)
{
	$comment = get_comment($id);
	$grade = grader_get_grade($comment);
	if (!($grade === false)) {
		delete_post_meta($comment->comment_post_ID, '_grade_comment');
	}
}

add_filter('manage_posts_columns', 'grader_post_columns');
/*********************
** adds column to post admin panel
**********************/
function grader_post_columns($defaults) {
    $defaults['grade'] = __('Grade');
    return $defaults;
}

add_action('manage_posts_custom_column', 'grader_posts_custom_column');
/*********************
** Sets grade for column on post admin panel
**********************/
function grader_posts_custom_column($column_name)
{
	global $post;
	global $current_user;

	if ($column_name == 'grade') {
		if (grader_is_post_author() || grader_is_grader($current_user->data))
		{
			$comment_id = grader_get_grade_comment($post);
			if ($comment_id === false) return;
			print grader_get_grade(NULL,$comment_id);
		}
	}
}

add_filter('post_row_actions', 'grader_post_row_actions', 10, 2);
/*********************
** Add "Edit Grade" link for post row option, on post admin panel
**********************/
function grader_post_row_actions ($actions, $post)
{
	global $current_user;

	if (!grader_is_grader($current_user->data)) return $actions;
	$comment_id = grader_get_grade_comment($post);
	if ($comment_id === false) return $actions;
	$actions['edit-grade'] = '<a href="'.get_bloginfo('url').'/wp-admin/comment.php?action=editcomment&c='.$comment_id.'">Edit Grade</a>';
	return $actions;
}

add_filter ('user_has_cap','grader_user_has_cap',10,3);
/*********************
** Tests if the user can edit a post or comment.  Only admins can edit posts that are graded or grade comments.
** @return an array of capabilities.
**********************/
function grader_user_has_cap ($allcaps,$caps,$args)
{
	global $comment;
	global $post;
	global $current_user;

	if ($comment == NULL && $post==NULL) return $allcaps;
	if ( ! is_user_logged_in() ) {
		return $allcaps;
	}
	if (grader_is_grader($current_user->data)) return $allcaps;
	$grade_comment_id = grader_get_grade_comment($post);
	if ($grade_comment_id===false) return $allcaps;
	if ($comment !== NULL && $grade_comment_id !== $comment->comment_ID) return $allcaps;
	if ($comment == NULL && grader_get_allow_edits()==true) return $allcaps;
	$allcaps['edit_posts'] = 0;
	$allcaps['edit_published_posts'] = 0;
	$allcaps['delete_posts'] = 0;
	$allcaps['delete_published_posts'] = 0;
	return $allcaps;

}

/** OPTIONS */

add_action('admin_init', 'grader_admin_init' );
function grader_admin_init()
{
	register_setting('grader_options', 'grader_grade_token');
	register_setting('grader_options', 'grader_hidden_comment_text');
	register_setting('grader_options', 'grader_comment_delim');
	register_setting('grader_options', 'grader_warning_msg');
	register_setting('grader_options', 'grader_grade_color');
	register_setting('grader_options', 'grader_comment_color');
	register_setting('grader_options', 'grader_allow_edits');
}

add_action('admin_menu', 'grader_plugin_menu');
function grader_plugin_menu()
{
	add_options_page('Grader Options', 'Grader Options', 8, __FILE__, 'grader_plugin_options');
}

function grader_plugin_options()
{ ?>
	<div class="wrap">
	<h2>Grader</h2>

	<form method="post" action="options.php">
	<?php settings_fields('grader_options'); ?>
	<table class="form-table" width=100%>
        <tr valign="top">
            <th width=30% scope="row">Grade token</th>
            <td><input type="text" name="grader_grade_token" id="grader_grade_token" value="<?php echo grader_get_token();?>" /></td>
        </tr>
		<tr valing="top">
			<td colspan=2><em>The token is the text that at the beginning of the comment that signals that the comment is a grade.<font color="red">  Warning: If you change this value, posts with previous token will no longer be recognized as a grade.</em></td>
		</tr>
	    <tr valign="top">
            <th width=30% scope="row">Comment delimeter</th>
            <td><input type="text" name="grader_comment_delim" id="grader_comment_delim" value="<?php echo grader_get_comment_delim();?>" size="5" /></td>
        </tr>
		<tr valing="top">
			<td colspan=2><em>The comment Delimeter seperates the grade from the remainder of the comment.</em></td>
		</tr>

        <tr valign="top">
            <th width=30% scope="row">Blocked text</th>
            <td><input type="text" name="grader_hidden_comment_text" id="grader_hidden_comment_text" value="<?php echo grader_get_hidden_comment_text();?>" size="100" /></td>
        </tr>
		<tr valing="top">
			<td colspan=2><em>The blocked text replaces the grade comment for users that do not have sufficient privileges to view the grade.</em></td>
		</tr>

        <tr valign="top">
            <th width=30% scope="row">Warning message</th>
            <td><textarea type="text" name="grader_warning_msg" id="grader_warning_msg" rows=3 cols=75><?php echo grader_get_warning_msg();?></textarea></td>
        </tr>
		<tr valing="top">
			<td colspan=2><em>The warning message appears at the bottom of the grade comment and reminds users that their grades are private.</em></td>
		</tr>

	    <tr valign="top">
            <th width=30% scope="row">Grade color</th>
            <td><input type="text" name="grader_grade_color" id="grader_grade_color" value="<?php echo grader_get_grade_color();?>" size="25" /></td>
        </tr>

	    <tr valign="top">
            <th width=30% scope="row">Allow users to edit graded posts</th>
            <td><input type="checkbox" name="grader_allow_edits" id="grader_allow_edits" value="true" <?php echo grader_get_allow_edits()==true?'checked':''; ?> /></td>
        </tr>

		<tr valign="top">
			<td colspan=2><input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" class="button" /></td>
		</tr>
	</table>
	</form>
	</div>
<?php
}

