<?php
/**
 * @package Tako Movable Comments
 * @author Ren Aysha
 * @version 1.0.7
 */
/*
Plugin Name: Tako Movable Comments
Version: 1.0.7
Plugin URI: https://github.com/renettarenula/Tako/
Author: Ren Aysha
Author URI: http://twitter.com/RenettaRenula
Description: This plugin allows you to move comments from one post or page to another. You can also move comments across post types and custom post types. Just click on edit comments and move your comments through the <strong>Move Comments with Tako</strong> metabox.

Copyright (C) <2016> <Ren Aysha>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

define( 'TAKO_DIR', dirname( __FILE__ ) );

require TAKO_DIR . '/template.php';

class Tako
{
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/


	/**
	 * Initializes the plugin by adding meta box, filters, and JS files.
	 */
	public function __construct()
	{
		add_action( 'add_meta_boxes', array( &$this, 'tako_add_meta_box' ) );
		add_action( 'edit_comment', array( &$this, 'tako_save_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'tako_load_scripts') );
		add_action( 'wp_ajax_tako_chosen_post_type', array( &$this, 'tako_chosen_post_type_callback' ) );
		add_action( 'admin_footer-edit-comments.php', array( &$this, 'tako_bulk_action_for_comments' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'tako_bulk_action_script' ) );
		add_action( 'wp_ajax_tako_post_types', array( &$this, 'tako_post_types_callback' ) );
		add_action( 'wp_ajax_tako_move_bulk', array( &$this, 'tako_move_bulk_callback' ) );
	}

	/**
	 * Enqueue the CSS & JS file and ensure that it only loads in the edit comment page
	 */
	public function tako_load_scripts( $hook ) {
		if ( $hook == 'comment.php' || $hook == 'edit-comments.php' ) {
			wp_enqueue_script( 'tako_dropdown', plugins_url( 'js/tako-dropdown.js' , __FILE__ ) );
			wp_localize_script( 'tako_dropdown', 'tako_object', array( 'tako_ajax_nonce' => wp_create_nonce( 'tako-ajax-nonce' ) ) );
			// Chosen.js & css
			wp_enqueue_script( 'tako_chosen', plugins_url( 'js/tako-chosen.js', __FILE__ ) );
			wp_enqueue_style( 'tako_chosen_style', plugins_url( 'css/tako-chosen.css', __FILE__ ) );
		}
	}

	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

	/**
	 * This is needed in order to add a new meta box in the edit comment page
	 */
	public function tako_add_meta_box()
	{
		add_meta_box(
           	'tako_move_comments'
            ,__( 'Move Comments with Tako', 'tako_lang' )
            ,array( &$this, 'tako_meta_box' )
            ,'comment'
            ,'normal'
            ,'high'
        );
	}

	/**
	 * The callback for the meta box in order to print the HTML form of the Meta Box
	 * @param array $comment 	Getting the comment information for the current comment
	 */
	public function tako_meta_box( $comment )
	{
		wp_nonce_field( plugin_basename( __FILE__ ), 'tako_nonce' );

		$post_types = get_post_types( '', 'names' );
		$current_post = get_the_title( $comment->comment_post_ID );

		$html = '<div id="tako_current_comment" style="display:none;">';
		$html .= $comment->comment_post_ID;
		$html .= '</div>';

		$html .= '<ol>';
		$html .= '<li>' . __( 'This post currently belongs to a post titled ', 'tako_lang' ) . '<strong>' . $current_post . '</strong>' . '</li>';

		$html .= '<li>' . __( 'Choose the post type that you want to move this comment to ', 'tako_lang' );
		$html .= '<select name="tako_post_type" id="tako_post_type">';

		foreach( $post_types as $post_type ) {
			$html .= '<option value="' . $post_type . '" ' . selected( $post_type, get_post_type( $comment->comment_post_ID ), false ) . '>' . $post_type . '</option>';
		}

		$html .= '</select> &nbsp;';
		$html .= '<img src="' . admin_url('/images/wpspin_light.gif') . '"class="waiting" id="tako_spinner" style="display:none;" />';
		$html .= '</li>';

		$html .= '<li>' . __( 'Choose and search for the post title that you want to move this comment to ', 'tako_lang' );
		$html .= '<select name="tako_post" id="tako_post">';
		$html .= '</li>';

		echo $html;
	}

	/**
	 * The method that is responsible in ensuring that the new comment is saved
	 * @param string $comment_content 	Getting the comment information for the current comment
	 */
	public function tako_save_meta_box( $comment_content )
	{
		global $wpdb;

		$screen = get_current_screen();

		// For Quick Edit: if current screen is anything other than edit-comments (main page for editing comments), ignore nonce verification.
		if ( !wp_verify_nonce( $_POST['tako_nonce'], plugin_basename( __FILE__ ) ) && $screen->parent_base == 'edit-comments' )
			return;

		// For Front-end edit: return comment_content in order to ensure that non-administrator can edit comments using other AJAX edit comments plugins
		if ( !current_user_can( 'moderate_comments' ) )  {
			return $comment_content;
		}

		$comment_post_ID = (int) $_POST['tako_post'];
		$comment_ID = (int) $_POST['comment_ID'];

		// Retrieve the comment's current post ID so we can update count later
		$comment = get_comment($comment_ID);
		$old_post_ID = (int) $comment->comment_post_ID;

		// if post doesn't exist
		if ( !$this->tako_post_exist( $comment_post_ID ) )
			return $comment_content;

		$new = compact( 'comment_post_ID' );

		// if there are no nested comments, just move it
		// otherwise, get all the subcomments first and then move it
		if ( !$this->tako_nested_comments_exist( $comment_ID ) ) {
			$update = $wpdb->update( $wpdb->comments, $new, compact( 'comment_ID' ) );
		}
		else {
			$var = array_merge( $this->tako_get_subcomments( $this->get_direct_subcomments( $comment_ID ) ), compact( 'comment_ID' ) );
			$val = implode( ',', array_map( 'intval', $var ) );
			$wpdb->query( "UPDATE $wpdb->comments SET comment_post_ID = $comment_post_ID WHERE comment_ID IN ( $val )" );
		}

		// Update comment counts
		wp_update_comment_count( $old_post_ID );
		wp_update_comment_count( $comment_post_ID );

		return $comment_content;
	}

	/**
	 * The method that is responsible for getting all the nested comments under one comment.
	 * This method will check if there are subcomments available under each subcomments.
	 * @param array $comments	This is an array of comments. These comments are subcomments of the comment that the user wants to move
	 * @return array
	 */
	public function tako_get_subcomments( $comments )
	{
		global $wpdb;
		// implode the array; this is the current 'parent'
		$parents = implode( ',', array_map( 'intval', $comments ) );
		$nested = array(); // this will store all the subcomments

		do {
			// initializing the an array (or emptying the array)
			$subs = array();
			// get the subcomments under the parent
			$subcomments = $wpdb->get_results( "SELECT comment_ID FROM $wpdb->comments WHERE comment_parent IN ( $parents )" );
			// store the subcomment under $subs and $nested
			foreach( $subcomments as $subcomment ) {
				$subs[] = $subcomment->comment_ID;
				$nested[] = $subcomment->comment_ID;
			}
			// implode the array and assign it as parents
			$parents = implode( ',', array_map( 'intval', $subs ) );
		// loop will stop once $subs is empty
		} while( !empty( $subs ) );

		// merge all the subcomments with the initial parent comments
		$merge = array_merge( $comments, $nested );

		return $merge;
	}

	/**
	 * This method is responsible in checking whether nested comments is available
	 * @param int $comment_ID Comment ID of the comment chosen to be moved
	 * @return object
	 */
	public function tako_nested_comments_exist( $comment_ID )
	{
		$comments_args = array( 'parent' => $comment_ID );
		$comments = get_comments( $comments_args );

		return $comments;
	}

	/**
	 * Get the post object that the user had chosen to move the comments to
	 * @param int $comment_post_ID	The post ID that the user wants to move the comments to
	 * @return object
	 */
	public function tako_post_exist( $comment_post_ID )
	{
		return get_post( $comment_post_ID );
	}

	/**
	 * Get the direct subcomments of the comment that is chosen to be moved
	 * @param int $comment_ID Comment ID of the comment chosen to be moved
	 * @return array
	 */
	public function get_direct_subcomments( $comment_ID )
	{
		$comments_args = array( 'parent' => $comment_ID );
		$comments = get_comments( $comments_args );
		$comments_id = array();

		foreach( $comments as $comment ) {
			$comments_id[] = $comment->comment_ID;
		}

		return $comments_id;
	}

	/**
	* Add a bulk action for the plugin.
	*/

	public function tako_bulk_action_for_comments()
	{
		$template = new TakoTemplate(
			TAKO_DIR . '/views/script.view.php',
			array(
				'display' => __( 'Move Comments', 'tako_lang' )
			)
		);

		$template->render();
	}

	/**
	* Bulk action script load
	*/

	public function tako_bulk_action_script( $hook )
	{
		if ( 'edit-comments.php' != $hook )
			return;

		wp_enqueue_script( 'tako_handlebars', plugins_url( 'js/handlebars.js', __FILE__ ), '', '', true );
		wp_enqueue_style( 'tako_bulk_style', plugins_url( 'css/tako-bulk.css', __FILE__ ) );
	}

	/*--------------------------------------------*
	 * Ajax Callback
	 *--------------------------------------------*/

	/**
	 * Ajax callback for checking which post type is chosen and it will
	 * return JSON results of posts that are categorized under the chosen
	 * post type. JSON format - ID and title.
	 */
	public function tako_chosen_post_type_callback()
	{
		// check nonce
		if ( !isset( $_POST['tako_ajax_nonce'] ) || !wp_verify_nonce( $_POST['tako_ajax_nonce'], 'tako-ajax-nonce' ) )
			die( 'Permission Denied!' );

		$result = array();
		$post_type = $_POST['postype'];

		$args  = array( 'numberposts' => -1, 'post_type' => $post_type );
		$posts = get_posts( $args );

		foreach( $posts as $post ) {
			setup_postdata( $post );
			$result[] = array( 'ID' => $post->ID, 'title' => $post->post_title );
		}

		echo json_encode( $result );

		die();
	}

	/**
	* Ajax callback for passing a list of post type to bulk edit view
	*/

	public function tako_post_types_callback()
	{
		// check nonce
		if ( !isset( $_REQUEST[ 'tako_ajax_nonce' ] ) || !wp_verify_nonce( $_REQUEST[ 'tako_ajax_nonce' ], 'tako-ajax-nonce' ) )
			die( 'Permission Denied!' );

		$result = array();

		$post_types = get_post_types( '', 'names' );

		echo json_encode( $post_types );

		die();
	}

	/**
	* Ajax callback for moving bulk comments
	*/

	public function tako_move_bulk_callback()
	{
		global $wpdb;
		// check nonce
		if ( !isset( $_POST['tako_ajax_nonce'] ) || !wp_verify_nonce( $_POST['tako_ajax_nonce'], 'tako-ajax-nonce' ) )
			die( 'Permission Denied!' );

		$comment_ID = $the_posts = array();

		$comments = json_decode( stripslashes( $_POST[ 'comments' ] ) );
		$post_id = ( int ) $_POST[ 'post_id' ];
		$the_post = get_post( $post_id );

		foreach ( $comments as $comment ) {
			$the_comment_ID = ( int ) $comment;
			$comment_ID[] = $the_comment_ID;

			$the_comment = get_comment( $the_comment_ID );
			$the_posts[] = $the_comment->comment_post_ID;
		}

		$posts = array_unique( $the_posts );

		$nested = $this->tako_get_subcomments( $comment_ID );
		$comments_subs = implode( ',', array_map( 'intval', $nested ) );

		$results = $wpdb->query( "UPDATE $wpdb->comments SET comment_post_ID = $post_id WHERE comment_ID IN ( $comments_subs )" );

		// update number of comment for previous posts that the comments belong to
		foreach ( $posts as $post ) {
			wp_update_comment_count( $post );
		}

		// update number of comment for the post that comments are moved to
		wp_update_comment_count( $post_id );

		$success = array( 'comments' => $nested, 'post_id' => $post_id, 'title' => $the_post->post_title  );

		$success[ 'success' ] = ( $results != false ) ? 1 : 0;

		echo json_encode( $success );

		die();
	}
}
$tako = new Tako();
?>
