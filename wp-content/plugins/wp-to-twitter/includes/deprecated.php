<?php
/**
 * Metabox rendering functions XPoster
 *
 * @category Metabox
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv3
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

/**
 * Provide aliases for changed function names if plug-ins or themes are calling XPoster functions in custom code. Deprecated.
 *
 * @param string $url Query url.
 * @param string $method Method.
 * @param string $body Body.
 * @param string $headers Headers.
 * @param string $return_type Return data.
 *
 * @return data.
 */
function jd_fetch_url( $url, $method = 'GET', $body = '', $headers = '', $return_type = 'body' ) {
	return wpt_fetch_url( $url, $method, $body, $headers, $return_type );
}

/**
 * Alias for remote_json. Deprecated.
 *
 * @param string $url Query url.
 * @param array  $query_args Arguments sent to remote query.
 *
 * @return remote JSON.
 */
function jd_remote_json( $url, $query_args = true ) {
	return wpt_remote_json( $url, $query_args );
}

/**
 * Deprecated - alias for `wpt_post_update`.
 *
 * @param int     $post_ID Post ID.
 * @param string  $type Publishing context: instant, future, xmlrpc.
 * @param object  $post Post object.
 * @param boolean $updated True if updated, false if inserted.
 * @param object  $post_before The post prior to this update, or null for new posts.
 *
 * @return int $post_ID
 */
function wpt_tweet( $post_ID, $type = 'instant', $post = null, $updated = null, $post_before = null ) {
	return wpt_post_update( $post_ID, $type, $post, $updated, $post_before );
}

/**
 * Send a status update for a new link.
 *
 * @param int $link_id Link ID.
 *
 * @return twit link.
 */
function jd_twit_link( $link_id ) {
	return wpt_post_update_link( $link_id );
}

/**
 * Get post data.
 *
 * @param int $post_ID Post ID.
 *
 * @return Array post data.
 */
function jd_post_info( $post_ID ) {
	return wpt_post_info( $post_ID );
}

/**
 * Sent post tweet. Deprecated; replaced by wpt_tweet.
 *
 * @param int    $post_ID Post ID.
 * @param string $type Type of post.
 *
 * @return tweet
 */
function jd_twit( $post_ID, $type = 'instant' ) {
	return wpt_post_update( $post_ID, $type );
}

/**
 * Update oauth settings.
 *
 * @param mixed boolean/int   $auth Author ID.
 * @param mixed boolean/array $post POST data.
 *
 * @return update.
 */
function jd_update_oauth_settings( $auth = false, $post = false ) {
	return wpt_update_oauth_settings( $auth, $post );
}

/**
 * Deprecated 11/30/2024. Aliases `wpt_truncate_status`.
 *
 * @param string  $update Status update text.
 * @param array   $post Post data.
 * @param int     $post_ID Post ID.
 * @param boolean $repost Is this a repost.
 * @param boolean $ref X.com author Reference.
 *
 * @return string New text.
 */
function jd_truncate_tweet( $update, $post, $post_ID, $repost = false, $ref = false ) {
	return wpt_truncate_status( $update, $post, $post_ID, $repost, $ref );
}
