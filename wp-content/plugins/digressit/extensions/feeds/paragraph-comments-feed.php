<?php
/**
 * RSS2 Feed Template for displaying RSS2 Comments feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
//header('Content-Type: text/plain');
global $digressit_commentbrowser, $wp_rewrite, $matches, $wpdb, $wp_query;
//echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	<?php do_action('rss2_ns'); do_action('rss2_comments_ns'); ?>>
<channel>
	<title><?php
		if ( is_singular() )
			printf(ent2ncr(__('Comments on: %s')), get_the_title_rss());
		elseif ( is_search() )
			printf(ent2ncr(__('Comments for %s searching on %s')), get_bloginfo_rss( 'name' ), esc_attr($wp_query->query_vars['s']));
		else
			printf(ent2ncr(__('Comments for %s')), get_bloginfo_rss( 'name' ) . get_wp_title_rss());
	?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php (is_single()) ? the_permalink_rss() : bloginfo_rss("url") ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('r', get_lastcommentmodified('GMT')); ?></lastBuildDate>
	<?php the_generator( 'rss2' ); ?>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('commentsrss2_head'); ?>
<?php




list($post_identifier, $paragraph_number) = explode(',', $wp->query_vars['feed_parameter']);

$post = null;
if(is_numeric($post_identifier)){
	$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=".$post_identifier);
	$post = $post[0];
//	var_dump($post);	echo "using post id";	
}
else{
	$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_name='".basename($post_identifier)."'");
	$post = $post[0];
//	var_dump($post);
//	echo "using post name";	
}
//var_dump($paragraph_number);
if(!$post && is_numeric($paragraph_number)){
	//echo "nothing";
	return;
}

//var_dump($paragraph_number);
$approved_comments = get_approved_comments_for_paragraph($post->ID, (int)$paragraph_number);

//var_dump($approved_comments);


if ( $approved_comments ) : foreach ( $approved_comments as $comment ) : 

	$comment_post = get_post($comment->comment_post_ID);
	get_post_custom($comment_post->ID);
?>
	<item>
		<title><?php printf(ent2ncr(__('Comment on %1$s by %2$s')), $comment_post->post_title, $comment->comment_author); ?></title>
		<link><?php echo get_permalink($comment_post->ID) ?></link>
		<dc:creator><?php echo $comment->comment_author; ?></dc:creator>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $comment->comment_date_gmt, false); ?></pubDate>
		<guid isPermaLink="false"><?php echo $comment_post->guid; ?>#<?php echo $comment->comment_text_signature; ?></guid>
		<description><?php echo strip_tags($comment->comment_content) ?></description>
		<content:encoded><![CDATA[<?php echo $comment->comment_content; ?>
							<href="<?php echo $comment_post->guid; ?>#<?php echo $comment->comment_text_signature; ?>">Comment here</a>
							?>]]></content:encoded>
	</item>
<?php endforeach; endif; ?>
</channel>
</rss>
