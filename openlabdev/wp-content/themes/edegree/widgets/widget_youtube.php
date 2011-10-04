<?php 

function widget_youtube_icon() { ?>
	
    <a href="<?php echo get_option('tbf2_icon_youtube')?>" target="_blank" rel="nofollow"><img src="<?php bloginfo('template_url'); ?>/images/icon-watch-me.png" alt="Watch me... on YouTube" /></a>

<?php
}

function widget_youtubeIcon($args) {
	extract($args);
	echo $before_widget;
	widget_youtube_icon();
	echo $after_widget; 
}

$tbf2_icon_youtube = get_option('tbf2_icon_youtube');
if (!empty($tbf2_icon_youtube)) {
	if (function_exists('wp_register_sidebar_widget')) {
		wp_register_sidebar_widget('tbf2_youtube_icon',__('YouTube Button/Link'), 'widget_youtubeIcon');
	}
}

?>