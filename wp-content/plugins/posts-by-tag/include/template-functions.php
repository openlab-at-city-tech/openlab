<?php
/**
 * All publicly exposed template functions
 *
 * @since 3.1
 * @author
 * @package Posts By Tag
 * @subpackage Template Functions
 */

/**
 * Template function to display posts by tags
 *
 *          If you want the plugin to automatically pick up tags from the current post,
 *          then set either one of the following options to true and leave tags empty
 *          - tag_from_post
 *          - tag_from_post_slug
 *          - tag_from_post_custom_field
 *       - int number Number of posts to show
 *       - bool tag_from_post Whether to get tags from current post's tags. Default is FALSE
 *       - bool tag_from_post_slug Whether to get tags from current post's slug. Default is FALSE
 *       - bool tag_from_post_custom_field Whether to get tags from current post's custom field. Default is FALSE
 *       - bool exclude Whether to exclude the tags specified. Default is FALSE
 *       - bool excerpt - Whether to display excerpts or not
 *       - bool excerpt_filter - Whether to enable or disable excerpt filter
 *       - bool thumbnail - Whether to display thumbnail or not
 *       - string|array thumbnail_size - Size of the thumbnail image. Refer to http://codex.wordpress.org/Function_Reference/get_the_post_thumbnail#Thumbnail_Sizes
 *       - set order_by (title, date, rand) defaults to 'date'
 *       - set order (asc, desc) defaults to 'desc'
 *       - bool author - Whether to show the author name or not
 *       - bool date - Whether to show the post date or not
 *       - bool content - Whether to display content or not
 *       - bool content_filter - Whether to enable or disable content filter
 *       - bool exclude_current_post Whether to exclude the current post/page. Default is FALSE
 *       - bool tag_links Whether to display tag links at the end
 *       - string link_target the value to the target attribute of each links that needs to be added
 *
 * @param string  $tags    (optional) List of tags from where the posts should be retrieved.
 * @param array   $options (optional) An array which has the following values
 * @return string $output The posts HTML content
 */
function posts_by_tag( $tags = '', $options = array() ) {
	$output = get_posts_by_tag( $tags, $options );

	if ( $options['tag_links'] && ! $option['exclude'] ) {
		$output .= Posts_By_Tag_Util::get_tag_more_links( $tags );
	}

	echo $output;
}


/**
 * Helper function for @link posts_by_tag
 *
 * @link posts_by_tag for information about parameters
 *
 * @see posts_by_tag
 * @param unknown $tags    (optional)
 * @param unknown $options (optional)
 * @return unknown
 */
function get_posts_by_tag( $tags = '', $options = array() ) {
	return Posts_By_Tag_Util::get_posts_by_tag( $tags, $options );
}
?>
