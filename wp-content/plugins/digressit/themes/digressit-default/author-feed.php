<?php
/**
 * RSS2 Feed Template for displaying RSS2 Comments feed.
 *
 * @package WordPress
 */
require_once(ABSPATH . WPINC . '/registration.php');	
require_once(DIGRESSIT_CORE_DIR . '/core-functions.php');

global $wp_rewrite, $matches, $wp_query, $wp;

;
$user_id = username_exists( $_GET['user'] );

$userdata = get_userdata($user_id);

?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	<?php do_action('rss2_ns'); do_action('rss2_comments_ns'); ?>>
<channel>
	<title><?php echo 'Comments by: ' . $userdata->user_login; ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php (is_single()) ? the_permalink_rss() : bloginfo_rss("url") ?></link>
	<description><?php bloginfo('siteurl'); ?> comments</description>
	<lastBuildDate><?php echo mysql2date('r', get_lastcommentmodified('GMT')); ?></lastBuildDate>
	<?php the_generator( 'rss2' ); ?>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>

	<?php do_action('commentsrss2_head'); ?>
<?php


$comments_from_user = mu_get_comments_from_user($user_id);

if ( $comments_from_user ) : foreach ( $comments_from_user as $comment ) : 

	$comment_post = get_post($comment->comment_post_ID);
	get_post_custom($comment_post->ID);
?>
	<item>
		<title><?php printf(ent2ncr(__('Comment on %1$s by %2$s')), $comment_post->post_title, $comment->comment_author); ?></title>
		<link><?php echo get_permalink($comment_post->ID) ?>#<?php echo $comment->comment_text_signature; ?></link>
		<dc:creator><?php echo $comment->comment_author; ?></dc:creator>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $comment->comment_date_gmt, false); ?></pubDate>
		<guid isPermaLink="false"><?php echo get_permalink($comment_post->ID) ?>#<?php echo $comment->comment_text_signature; ?></guid>
		<description><?php echo strip_tags($comment->comment_content) ?></description>
		<content:encoded><![CDATA[<?php echo $comment->comment_content; ?>?>]]></content:encoded>
	</item>
<?php endforeach; endif; ?>
</channel>
</rss>
