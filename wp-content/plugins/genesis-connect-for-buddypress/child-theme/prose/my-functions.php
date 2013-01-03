<?php
/*
 * this file customizes Prose for BuddyPress
 */
class GConnect_Prose_CSS {
	var $settings = null;
	var $maybe_css = '
.internal-page #inner {
	width:920px;
	padding:15px 20px 20px;
	border:1px solid #E4E4E4;
	margin:20px auto 0;
}
.bp-content-top .widget {
	margin: 0 0 10px 0;
	padding: 4px;

}
#content ul.item-list li {
	background: #F4F4F4 !important;
	width: 98%;
	padding: 3px 0 3px 3px;
	margin: 5px 0 5px 0;
}
#content .bp-content-top .widget {
	padding: 10px;
	border:0px;
}
#header .widget {
     background:transparent;
     border: none;
}
.activity-list div.activity-meta a, .activity-list .activity-content a {
border:0px;
background:transparent;
}
.sidebar-sidebar-content #content .padder h3,
	.sidebar-content-sidebar #content .padder h3,
	.content-sidebar-sidebar #content .padder h3 {
		font-size: 14px;
}
';
	var $buttons = '#search-submit, #sidebar-me a.button, #aw-whats-new-submit, #ac-form-submit, #groups_search_submit, #forums_search_submit, #members_search_submit, .dir-search input, #profile-group-edit-submit, #send, div.ac-reply-content input, .internal-page #content .padder a.button, 	.internal-page #content .padder .submit input[type=button], input[type=submit], .button, a.button, .dir-form h3 a { -moz-border-radius: 0px; -webkit-border-radius: 0px; -khtml-border-radius: 0px; background: %s; color: %s; font-size: %spx; font-family: %s; text-transform: %s; text-decoration: none; }
#search-submit:hover, #sidebar-me a.button:hover, #aw-whats-new-submit:hover, #ac-form-submit:hover, #groups_search_submit:hover, #forums_search_submit:hover, #members_search_submit:hover, .dir-search input:hover, #profile-group-edit-submit:hover, #send:hover, div.ac-reply-content input:hover, .internal-page #content .padder a.button:hover, 	.internal-page #content .padder .submit input[type=button]:hover, input[type=submit]:hover, .button:hover, .dir-form h3 a:hover { background: %s; }
';
	var $bpnav = '#bpnav { background-color: %s; font-family: %s; font-size: %spx; border: %spx %s %s; width: 938px; text-transform: uppercase; }
#bpnav ul { width: 936px; float: left; border: %spx %s %s; }
#bpnav li a { background-color: %s; color: %s; text-decoration: %s; }
#bpnav li a:hover, #bpnav li a:active, #bpnav .current_page_item a, #bpnav .current-menu-item a { background-color: %s; color: %s; text-decoration: %s; }
#bpnav li li a, #bpnav li li a:link, #bpnav li li a:visited { background-color: %s; color: %s; }
#bpnav li li a:hover, #bpnav li li a:active { background-color: %s; color: %s; }
';
	var $inputs = '#wrap #content .dir-search label input, #search-terms, #search-which, #whats-new-post-in, #activity-filter-select select, .ac-input, .dir-search label input, .item-list-tabs ul li.filter select, #field_ids, #message_content, #subject, #send-notice, #send-to-input, #pass1, #pass2 { background-color: %s; color: %s; border: %spx %s %s; font-family: %s; font-style: %s; font-size: %spx; text-transform: none; text-shadow: none; }
';
	var $widgets = '#content .bp-content-top .widget h4 { color: %s; text-transform: %s; font-size: %spx; font-family: %s; font-weight: %s; margin: 0 0 5px 0; padding: 0 0 5px 15px; background: transparent; border-bottom: %spx %s %s; }
#content .bp-content-top .widget { padding: 10px; border:0px; }
';
	var $css = '/* This file is overwritten by the Prose Design Settings - use your custom stylesheet to customize */
#wp-admin-bar { margin-left: -10px; width: 960px; } #wrap { width: 940px; }
#content div.pagination-links a { background: transparent; }
.sidebar-sidebar-content #item-header span.activity, .sidebar-content-sidebar #item-header span.activity, .content-sidebar-sidebar #item-header span.activity { display: inline-block; }
.sidebar-sidebar-content .dir-search label input, .sidebar-content-sidebar .dir-search label input, .content-sidebar-sidebar .dir-search label input { width: 10em; }
div.generic-button { padding: 2px 4px; background: transparent; }
.directory ul.item-list li { padding: 10px 0; }
.dir-form h3 a { padding:2px; }
#content .padder #item-body a.accept, #content .padder #item-body a.reject { line-height: 1em; margin-right: 4px; }
#content #group-create-tabs, #content #group-create-tabs ul li span { background: #F7F7F7; }
#content #group-create-tabs ul li a { background: #FFFFFF; }
';
	function GConnect_Prose_CSS() {
		$this->__construct();
	}
	function  __construct() {
		add_action( 'update_option', array( &$this, 'update_option' ), 10, 3 );
		add_action( 'genesis_meta', array( &$this, 'genesis_meta' ), 1 );
		add_action( 'gconnect_load_template', 'prose_add_stylesheets' );
	}
	function update_option( $option, $old, $new ) {
		if( $option == PROSE_SETTINGS_FIELD ) {
			if ( function_exists( 'prose_get_custom_stylesheet_path' ) )
				$handle = @fopen( prose_get_custom_stylesheet_path( 'dir' ) . 'buddypress.css', 'w');
			else
				$handle = @fopen( prose_get_stylesheet_location( 'dir' ) . 'buddypress.css', 'w');
			@fwrite( $handle, $this->css );

			$background_color = ( 'hex' == $new['button_background_color_select'] ? $new['button_background_color'] : $new['button_background_color_select'] );
			$background_hover = ( 'hex' == $new['button_background_hover_color_select'] ? $new['button_background_hover_color'] : $new['button_background_hover_color_select'] );
			@fwrite($handle, sprintf( $this->buttons, $background_color, $new['button_font_color'], $new['button_font_size'], $new['button_font_family']
				, $new['button_text_transform'], $background_hover ) );

			$nav_color = ( 'hex' == $new['secondary_nav_background_color_select'] ? $new['secondary_nav_background_color'] : $new['secondary_nav_background_color_select'] );
			$nav_back_color = ( 'hex' == $new['secondary_nav_link_background_select'] ? $new['secondary_nav_link_background'] : $new['secondary_nav_link_background_select'] );
			$nav_back_hover = ( 'hex' == $new['secondary_nav_link_hover_background_select'] ? $new['secondary_nav_link_hover_background'] : $new['secondary_nav_link_hover_background_select'] );
			@fwrite($handle, sprintf( $this->bpnav, $nav_color, $new['secondary_nav_font_family'], $new['secondary_nav_font_size']
				, $new['secondary_nav_border'], $new['secondary_nav_border_style'], $new['secondary_nav_border_color']
				, $new['secondary_nav_inner_border'], $new['secondary_nav_inner_border_style'], $new['secondary_nav_inner_border_color']
				, $nav_back_color, $new['secondary_nav_link_color'], $new['secondary_nav_link_decoration']
				, $nav_back_hover, $new['secondary_nav_link_hover'], $new['secondary_nav_link_hover_decoration']
				, $nav_back_hover, $new['secondary_nav_link_hover']
				, $nav_back_hover, $new['secondary_nav_link_hover'] ) );

			$input_back_color = ( 'hex' == $new['input_background_color_select'] ? $new['input_background_color'] : $new['input_background_color_select'] );
			@fwrite($handle, sprintf( $this->inputs, $input_back_color, $new['input_font_color']
				, $new['input_border'], $new['input_border_style'], $new['input_border_color']
				, $new['input_font_family'], $new['input_font_style'], $new['input_font_size'] ) );

			@fwrite($handle, sprintf( $this->widgets, $new['sidebar_headline_font_color'], $new['sidebar_headline_text_transform']
				, $new['sidebar_headline_font_size'], $new['sidebar_headline_font_family'], $new['sidebar_headline_font_weight']
				, $new['sidebar_headline_border'], $new['sidebar_headline_border_style'], $new['sidebar_headline_border_color'] ) );

			@fclose($handle);
		}
	}
	function genesis_meta() {
		global $gconnect_theme;
		if( empty( $gconnect_theme->front ) )
			return;
			
			if ( function_exists( 'prose_get_stylesheet_location' ) )
				$gconnect_theme->front->set_addon( prose_get_stylesheet_location( 'dir' ), prose_get_stylesheet_location( 'url' ) );
			else
				$gconnect_theme->front->set_addon( get_stylesheet_directory() . '/css', get_stylesheet_directory_uri() . '/css' );
	}
}
new GConnect_Prose_CSS();