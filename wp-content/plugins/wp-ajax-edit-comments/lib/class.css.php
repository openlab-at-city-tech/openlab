<?php
/*
Printing these out to screen instead of via file is not an ideal solution, but probably the best one.
We attempted to store this locally to file, many it caused many issues with people with strict server permissions.  Earlier versions of the plugin had dynamic CSS, but this was a performance issue.
Recommend people just use a good caching plugin that minifies the inline CSS has needed.

*/
class AECCSS {
		public static function output_interface_css() {
			AECCSS::get_interface_css( true ); //echo out
		} //end output_interface_css
		public static function get_interface_css( $echo = false ) {
			global $aecomments;
			$content = '';
			$iconsize = AECCore::get_icon_size();
			$iconset = $aecomments->get_admin_option( 'icon_set' );
			$rtl = $aecomments->get_admin_option( 'use_rtl' );
			$icondisplay = $aecomments->get_admin_option( 'icon_display' );
			$plugins_url = $aecomments->get_plugin_url('');
			if ($rtl == 'true') {
				$prev = "right";
				$next = "left";
				$rtl_float = "right";
				$icon_margin = "left";
			} else {
				$prev = "left";
				$next = "right";
				$rtl_float = "left";
				$icon_margin = "right";
			}
			if ($iconsize == 16) {
				$fontSize = 12;
				$padding = 20;
			} else {
				$fontSize = 16;
				$padding = 30;
			}
$content .= "
* {\n
\tmargin:0;\n
\tpadding:0;\n
\tfont-weight: normal;\n
\tfont-family: \"Lucida Grande\",\"Lucida Sans Unicode\",Tahoma,Verdana,sans-serif;\n
}\n
img { border: 0; }\n
/* Comment Header */ \n
#comment-edit-header {\n
	\tbackground-color: #464646;\n
	\theight: 46px;\n
	\ttext-align: center;\n
}\n
#title {\n
	\tfont-family: Georgia, \"Times New Roman\", Times, serif;\n
	\tfont-size: 20px;\n
	\tpadding-top: 10px;\n
	\tcolor: #FFF;\n
}\n
#title a, #title a:hover {\n
	\tcolor: #FFF;\n
	\ttext-decoration: none;\n
}\n
#title a:hover { \n
	\ttext-decoration: underline;\n
}\n
/* Toggle Box Stuff - Conveniently lifted from WP Admin StyleSheet */\n
#comment-options {\n
	\tborder-color:#EBEBEB rgb(204, 204, 204) rgb(204, 204, 204) rgb(235, 235, 235);\n
}\n
#comment-options {\n
	\tmargin: 10px 8px 10px 20px;\n
	\tpadding:2px;\n
}\n
#comment-options h3 {\n
	\tfont-size: 14px;\n
	\tfont-weight: bold;\n
	\tpadding: 9px;\n
	\tcursor: pointer;\n
	\tbackground-color: #EAF3FA;\n
}\n
/*Expand icon*/\n
#edit_options {\n
	\tmargin-left: 10px;\n
}\n
.expand, .retract {\n
	\twidth: 32px;\n
    \theight: 32px;\n
    \tdisplay:block;\n
    \tfloat:left;\n
    \tmargin-left: 5px;\n
}\n
.expand {\n
	\tbackground-image: url('" . $plugins_url . "/images/full_screen.png');\n
}\n
.retract { \n
	\tbackground-image: url('" . $plugins_url . "/images/full_screen_close.png');\n
}\n
\n
/* Forms */\n
div .form {\n
	\tclear: both;\n";
if ($rtl == 'true') {
	$content .= "\tfloat:right;\n";
}
$content .= "
	\tmargin-left: 10px;\n
	\tmargin-top: 10px;\n
}\n
table.inputs {\n
	\tborder: 0;\n";
if ($rtl == 'true') {
	$content .= "\ttext-align:left;\n";
}
$content .= "
}\n	
table.inputs td {\n
	\tpadding: 5px 0;\n
}\n
.inputs {\n
	\tmargin-top: 5px;\n
	\tmargin-left: 5px;\n
}\n
.inputs label{\n
	\ttext-align: {$icon_margin};\n
	\tmargin-{$icon_margin}: 10px;\n
}\n
textarea, input { border: 1px solid #CCC; " . $rtl == 'true' ? "direction:rtl;" : '' . "}\n
textarea { position: relative; width: 98%; clear:left; " . $rtl == 'true' ? "float:right;" : '' . "}\n
#buttons div {\n
	\tfloat: {$rtl_float};\n
	\tmargin-right: 5px;\n
	\tmargin-top: 5px;\n
	\tmargin-bottom: 5px;\n
}\n
input.error,textarea.error {\n
	\tbackground-color: #FFEBE8;\n
	\tborder: 1px solid #CC0000;\n
}\n
/*Status */\n
#status, #close-option { \n
	\tdisplay: none;\n
}\n
#status {\n
	\tclear: both;\n
	\tmargin: 0 10px;\n
	\tpadding: 5px;\n
}\n
#status.error, #status.success {\n
	\tdisplay: block;\n
}\n
#status.error {\n
	\tbackground-color: #FFEBE8;\n
	\tborder: 1px solid #CC0000;\n
}\n
#status a, #status a:hover {\n
	\tcolor:#0000FF;\n
}\n
#status.success {\n
	\tbackground-color: #FFFFE0;\n
	\tborder: 1px solid #E6DB55;\n
}\n
a.next {\n
	\tdisplay: block;\n
	\tfont-size: {$fontSize}px;\n
	\tpadding: 2px 0px;\n
	\tpadding-{$next}: {$padding}px;\n
	\tfloat: {$next};\n
	\tmargin-left: 10px;\n
	\ttext-decoration: none;\n
}\n
a.previous {\n
	\tdisplay: block;\n
	\tfont-size: {$fontSize}px;\n
	\tpadding: 2px 0px;\n
	\tpadding-{$prev}: {$padding}px;\n
	\tfloat: {$prev};\n
	\tmargin-right: 10px;\n
	\ttext-decoration: none;\n
}\n
.previcon {\n
	\tdisplay:block;\n
    \tmargin-{$next}: 4px;\n
    \tfloat: {$prev};\n
	\twidth:{$iconsize}px;\n
    \theight:{$iconsize}px;\n
    \tbackground:url(" . $plugins_url . "/images/themes/{$iconset}/sprite.png) 0px 0px no-repeat;\n
}\n
.nexticon {\n
	\tdisplay:block;\n
    \tmargin-{$prev}: 4px;\n
    \tfloat: {$next};\n
	\twidth:{$iconsize}px;\n
    \theight:{$iconsize}px;\n
    \tbackground:url(" . $plugins_url . "/images/themes/{$iconset}/sprite.png) " . $iconsize*-1 . "px 0px no-repeat;\n
}\n
a.hidden, div.hidden, body.hidden {\n
	\tdisplay: none;\n
}\n
.loading { \n
	\tdisplay: block;\n
	\twidth: 35px;\n
	\theight: 35px;\n
	\tbackground: url(" . $plugins_url . "/images/loading.gif);\n
}\n
#post_buttons { \n
	\tclear: both;\n
}\n";
			ob_start();
			//For after the deadline and tabs
			$afterthedeadline = ($aecomments->get_admin_option( 'after_deadline_popups' ) == "true"  ? true : false);
			if ( $afterthedeadline ) {
				include( $aecomments->get_plugin_dir( '/css/atd/atd.css' ) );
			}
			include( $aecomments->get_plugin_dir( '/css/tabber.css' ) );
			include( $aecomments->get_plugin_dir( '/css/frontend.css' ) );
			$content  .= str_replace( 'images', $aecomments->get_plugin_url( '/css/images' ), ob_get_clean() ); //convert relative paths
			//Return content
			if ( $echo ) {	
				echo "<!--Ajax Edit Comments Styles-->\n";
				echo "<style type='text/css'>\n";
				echo $content;
				echo "\n</style>\n";
			} else {
				return $content;
			}	
		} //end get_interface_css
		public static function get_main_css( $echo = false ) {
			global $aecomments;
			$return_content = '';
			$iconsize = AECCore::get_icon_size();
			$iconset = $aecomments->get_admin_option( 'icon_set' );
			$rtl = $aecomments->get_admin_option( 'use_rtl' );
			$icondisplay = $aecomments->get_admin_option( 'icon_display' );
			if ($iconsize == 16) {
				$fontSize = 12;
				$paddingLeft = 5;
			} else {
				$fontSize = 16;
				$paddingLeft = 15;
			}
			if ($rtl == 'true') {
				$rtl_float = "right";
				$icon_margin = "left";
			} else {
				$rtl_float = "left";
				$icon_margin = "right";
			}
			$plugins_url = $aecomments->get_plugin_url('images');
//Left justified for formatting
$return_content .= "
.edit-comment-admin-links, .edit-comment-user-link {\n
\tdisplay: none;\n
}\n";
if ($icondisplay != "noicons") {
$return_content .= ".edit-comment-admin-links a, .ajax-edit-time-left, .aec-dropdown-container a{\n
\tdisplay: block;\n
\theight:  {$iconsize}px;\n
\tfont-size: {$fontSize}px;\n
\tpadding: 4px 0px;\n
\tpadding-{$rtl_float}: {$paddingLeft}px;\n
\tfloat: {$rtl_float};\n
\tmargin-{$rtl_float}: 10px;\n
\tfont-weight: bold;\n
}\n
.aec-icons {\n
\tdisplay: block;\n
\tclear: left;\n
\tfloat: {$rtl_float};\n
\twidth: {$iconsize}px;\n
\theight: {$iconsize}px;\n
\tmargin-{$icon_margin}: 4px;\n
\tmargin-bottom: 5px;\n
}\n
.aec_link_text, .aec_anon_text {\n
\tdisplay: block;\n
\tfloat: {$rtl_float};\n
\tmargin-{$icon_margin}: 6px;\n
}\n
.aec-icons {\n
\tmargin-{$icon_margin}: 10px;\n
}\n";
} //end if noicons
if ($icondisplay == "iconsonly") {
$return_content .= ".aec_link_text {\n
\tdisplay:none;\n
}\n
.aec-icons {\n
\tclear: none;\n
}\n";
} //end if iconsonly
$return_content .= "
.affiliate_message {\n
\tclear: {$rtl_float};\n
}\n
.affiliate_message a {\n
\tdisplay:inline;\n
\tfont:inherit;\n
\tpadding:0;\n
\tfloat:none;\n
\tmargin:0;\n
}\n
.edit-comment-admin-links-no-icon a, .ajax-edit-time-left-no-icon{\n
\tdisplay: inline;\n
\tfloat: none;\n
\tfont-size: 12px;\n
\tpadding: 2px 0px;\n
}\n
.ajax-edit-time-left {\n
\tpadding-left: 0;\n
}\n";
if ($icondisplay != "noicons") {
$return_content .= "
.row-actions {\n
\tclear: both;\n
}\n
.clearfix:after { /* from http://blue-anvil.com/archives/experiments-with-floats-whats-the-best-method-of-clearance*/ \n
\tcontent: \".\";\n
\tdisplay: block;\n
\theight: 0;\n
\tclear: both;\n
\tvisibility: hidden;\n
}\n
/* Begin dropdown */ \n
.aec-dropdownarrow { \n
\tposition: relative;\n
}\n
.aec-dropdown { display: none; padding-bottom:10px;}\n
.aec-dropdown-container {\n
\tposition: absolute;\n
\tdisplay: none;\n
\tbackground-color: #FFF;\n
\tpadding: 5px;\n
\tborder: 1px solid #CCCCCC;\n
\ttext-align: left;\n
\tz-index: 10;\n
}\n
.wp-admin .aec-dropdown-container {\n
\tbackground: #EEE;\n
}\n
.aec-dropdown-container a {\n
\tfloat: none;\n
\tmargin-left: 2px;\n
}\n
/* end dropdown */ \n
/* Begin Images */ \n
.aec-dropdownarrow span.aec-icons{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-2 . "px 0px no-repeat;\n
}\n
.aec-dropdownlink-less span.aec-icons{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-3 . "px 0px no-repeat;\n
}\n
span.blacklist-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-12 . "px 0px no-repeat;
}
span.email-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-8 . "px 0px no-repeat;\n
}\n
span.edit-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-4 . "px 0px no-repeat;\n
}\n
span.move-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-9 . "px 0px no-repeat;\n
}\n
span.moderate-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-11 . "px 0px no-repeat;\n
}\n
span.approve-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-5 . "px 0px no-repeat;\n
}\n
span.delete-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-6 . "px 0px no-repeat;\n
}\n
span.spam-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-10 . "px 0px no-repeat;\n
}\n
span.delink-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-7 . "px 0px no-repeat;\n
}\n
span.request-deletion-comment{\n
\tbackground:url({$plugins_url}/themes/{$iconset}/sprite.png) " . $iconsize*-6 . "px 0px no-repeat;\n
}\n
.edit-comment-admin-links a, .edit-comment-user-link a, .edit-comment-admin-links-no-icon a, .aec-dropdown-container a {\n
\ttext-decoration: none;\n
}\n";
} //End no icons
/* Errors */
$return_content .= "
li.ajax-delete, div.ajax-delete { background: #F33; } /* todo Doesn't work for .alt comments */ \n
li.ajax-approve, div.ajax-approve { background: #04cd33; } \n
li.ajax-unapprove, div.ajax-unapprove { background: #F96; } \n";
				
				ob_start();
				
				//After the deadline				
				if ($aecomments->get_admin_option( 'after_deadline_posts' ) == "true") {
					include( $aecomments->get_plugin_dir( '/css/atd/atd.css' ) );
				}
				include( $aecomments->get_plugin_dir( '/css/frontend.css' ) );
				$return_content  .= str_replace( 'images', $aecomments->get_plugin_url( '/css/images' ), ob_get_clean() ); //convert relative paths
				//Return content
			
				//Return content
				if ( $echo ) {	
					echo "<!--Ajax Edit Comments Styles-->\n";
					echo "<style type='text/css'>\n";
					echo $return_content;
					echo "\n</style>\n";
				} else {
					return $return_content;
				}
		} //end get_main_css
}