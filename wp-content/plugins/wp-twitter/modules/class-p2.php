<?php
/* Widget_profile
*------------------------------------------------------------*/
define( 'PLUGIN_P2', $this->hook . '-'.$this->_p2 );
define( 'PLUGIN_NAME', $this->pluginname );

class FDX_Widget_profile extends WP_Widget {

	function __construct() {
		$widget_options = array('classname' => 'widget_wp_twitter_fdx_profile', 'description' => __('Display Twitter Profile Widget', 'wp-twitter') );
		parent::__construct('fdxprofile',PLUGIN_NAME. ' - '.__('Profile Widget', 'wp-twitter'), $widget_options);
	}

	function widget($args) {
	extract($args);
	$wp_twitter_fdx_widget_title1 = get_option('wp_twitter_fdx_widget_title');
	echo $before_widget;
	echo $before_title . $wp_twitter_fdx_widget_title1 . $after_title;
    echo WP_Twitter::wp_twitter_fdx_profile();
    echo $after_widget;
	}

    function form() {
    echo __('Please go to', 'wp-twitter').': <b><a href="'. admin_url('admin.php?page='.PLUGIN_P2).'">'. PLUGIN_NAME . ' | Widgets</a></b> '. __('for options.', 'wp-twitter');
	}
}//end


/* Widget_search
*------------------------------------------------------------*/
class FDX_Widget_search extends WP_Widget {

	function __construct() {
		$widget_options = array('classname' => 'widget_wp_twitter_fdx_search', 'description' => __('Display Twitter Search Widget', 'wp-twitter') );
		parent::__construct('fdxsearch', PLUGIN_NAME. ' - '.__('Search Widget', 'wp-twitter'), $widget_options);
	}

	function widget($args) {
	extract($args);
	$wp_twitter_fdx_widget_title1 = get_option('wp_twitter_fdx_search_widget_sidebar_title');
	echo $before_widget;
	echo $before_title . $wp_twitter_fdx_widget_title1 . $after_title;
    echo WP_Twitter::wp_twitter_fdx_search();
    echo $after_widget;
	}

    function form() {
    echo __('Please go to', 'wp-twitter').': <b><a href="'. admin_url('admin.php?page='.PLUGIN_P2).'">'.PLUGIN_NAME . ' | Widgets</a></b> '. __('for options.', 'wp-twitter');
	}
}




