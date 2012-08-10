<?php
/**
 * RSS2 Feed Template for displaying RSS2 Comments feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
//header('Content-Type: text/plain');
global $digressit_commentbrowser, $wp_rewrite, $matches, $wp_query, $wpdb;
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	<?php do_action('rss2_ns'); do_action('rss2_comments_ns'); ?>>
<channel>
	<title><?php echo get_the_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php (is_single()) ? the_permalink_rss() : bloginfo_rss("url") ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('r', get_lastcommentmodified('GMT')); ?></lastBuildDate>
	<?php the_generator( 'rss2' ); ?>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('commentsrss2_head'); ?>
<?php




/* FIXME: I can't find a way to get the user var from GET this is a temp hack */
//preg_match('#paragraphlevel/(.+)#', $_SERVER['REQUEST_URI'], $gets);


//var_dump($gets);
if($post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=".$wp->query_vars['feed_parameter'] )){
//	echo "using post id";
}
elseif($post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_name='".basename($wp->query_vars['feed_parameter'])."'")){
//	echo "using post name";	
}


//var_dump($post);
$post = $post[0];

$paragraphs = digressit_paragraphs($post->post_content);





if ( $paragraphs ) : foreach ( $paragraphs as $key => $paragraph ) : 

$userdata = get_userdata($post->post_author);;
?>
	<item>
		<title><?php echo $post->post_title; ?></title>
		<link><?php echo get_permalink($post->ID) ?></link>
		<dc:creator><?php echo strlen($userdata->display_name) ? $userdata->display_name : $userdata->user_login; ?></dc:creator>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $post->post_date_gmt, false); ?></pubDate>
		<guid isPermaLink="false"><?php echo get_permalink($post->ID) ?>#<?php echo $key; ?></guid>
		<description><![CDATA[<?php echo $paragraph; ?>]]></description>
		<content:encoded><![CDATA[<?php echo $paragraph; ?>]]></content:encoded>
	</item>
<?php endforeach; endif; ?>
</channel>
</rss>
