<?php

// ShareThis
//
// Copyright (c) 2010 ShareThis, Inc.
// http://www.sharethis.com
//
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// *****************************************************************

/*
 Plugin Name: ShareThis
 Plugin URI: http://www.sharethis.com
 Description: Let your visitors share a post/page with others. Supports e-mail and posting to social bookmarking sites. <a href="options-general.php?page=sharethis.php">Configuration options are here</a>. Questions on configuration, etc.? Make sure to read the README.
 Version: 7.0.20
 Author: <a href="http://www.sharethis.com">The ShareThis Team</a>
 Author URI: http://www.sharethis.com
 */

load_plugin_textdomain('sharethis');

$_stversion=7.0;

function install_ShareThis(){
	$publisher_id = get_option('st_pubid'); //pub key value
	$widget = get_option('st_widget'); //entire script tag
	$newUser=false;

	if (get_option('st_version') == '') {
		update_option('st_version', '5x');
	}
	
	if(empty($publisher_id)){
		if(!empty($widget)){
			$newPkey=getKeyFromTag();
			if($newPkey==false){
				$newUser=true;
				update_option('st_pubid',trim(makePkey()));
			}else{
				update_option('st_pubid',$newPkey); //pkey found set old key
			}
		}else{
			$newUser=true;
			update_option('st_pubid',trim(makePkey()));
		}
	}
	
	if($widget==false || !preg_match('/stLight.options/',$widget)){
		$pkey2=get_option('st_pubid'); 
		if(empty($pkey2))
			$pkey2 = trim(makePkey());
			
		$widget ="<script charset=\"utf-8\" type=\"text/javascript\">var switchTo5x=true;</script>";
		$widget.="<script charset=\"utf-8\" type=\"text/javascript\" src=\"http://w.sharethis.com/button/buttons.js\"></script>";
		$widget.="<script type=\"text/javascript\">stLight.options({publisher:'$pkey2'});var st_type='wordpress".trim(get_bloginfo('version'))."';</script>";
		update_option('st_widget',$widget);
	}
	
	
	$st_sent=get_option('st_sent');
	$st_upgrade_five=get_option('st_upgrade_five');
	if(empty($st_sent)){
		update_option('st_sent','true');
		update_option('st_upgrade_five', '5x');
		$st_sent=get_option('st_sent'); //confirm if value has been set
		if(!(empty($st_sent))){
			sendWelcomeEmail($newUser);
		}
		$st_upgrade_five=get_option('st_upgrade_five');
	} else if (empty($st_upgrade_five)) {
		update_option('st_upgrade_five', '5x');
		$st_upgrade_five=get_option('st_upgrade_five'); //confirm if value has been set
		if(!(empty($st_upgrade_five))){
			sendUpgradeEmail();
		}
	}

	if (get_option('st_protocol') == '') {
		update_option('st_protocol', 'http');
	}
	if (get_option('st_pages_on_top') == '') {
		update_option('st_pages_on_top', '');
	}	
	if (get_option('st_posts_on_top') == '') {
		update_option('st_posts_on_top', '');
	}	

	$upgradeFromOld = false;
	checkForOldVersionOptions('widgetSecure', $upgradeFromOld);
	
	$upgradeFromOld = false;
	checkForOldVersionOptions('page', $upgradeFromOld);
	if (get_option('st_pages_on_bot') == '' && get_option('st_pages_on_top') == '' && !$upgradeFromOld) {
		update_option('st_pages_on_bot', 'bot');
	}	
	
	$upgradeFromOld = false;
	checkForOldVersionOptions('post', $upgradeFromOld);
	if (get_option('st_posts_on_bot') == '' && get_option('st_posts_on_top') == '' && !$upgradeFromOld) {
		update_option('st_posts_on_bot', 'bot');
	}	
}

function checkForOldVersionOptions($var, &$upgradeFromOld) {
	if($var == 'post') {
		if(get_option('st_add_to_content') == 'yes') {
			$upgradeFromOld = true;
			if(get_option('st_add_to_content1') == 'both') {
				update_option('st_posts_on_top', 'top');
				update_option('st_posts_on_bot', 'bot');
			 } else if(get_option('st_add_to_content1') == 'top') {
				update_option('st_posts_on_top', 'top');
			 } else {
				update_option('st_posts_on_bot', 'bot');
			 }
		} else if(get_option('st_add_to_content') == 'no') {
			$upgradeFromOld = true;
			update_option('st_posts_on_top', '');
			update_option('st_posts_on_bot', '');	
		}
	} else if($var == 'page') {
		if(get_option('st_add_to_page') == 'yes') {
			$upgradeFromOld = true;
			if(get_option('st_add_to_page2') == 'both') {
				update_option('st_pages_on_top', 'top');
				update_option('st_pages_on_bot', 'bot');
			 } else if(get_option('st_add_to_page2') == 'top') {
				update_option('st_pages_on_top', 'top');
			 } else {
				update_option('st_pages_on_bot', 'bot');
			 }
		} else if(get_option('st_add_to_page') == 'no') {
			$upgradeFromOld = true;
			update_option('st_pages_on_top', '');
			update_option('st_pages_on_bot', '');
			
			$pageIds = array();//st_get_page_ids();
			update_option('st_page', $pageIds);			
		}
	} else if($var == 'widgetSecure') {
		$upgradeFromOld = true;
		preg_match("/src=\\\(.*)\\\/", get_option('st_widget'), $m);
		$proto = preg_split('/:/', $m[1]);
		update_option('protocolType', str_replace('"', '', $proto[0]));	
	}
}

function uninstall_ShareThis()
{
	$st_options = array('st_current_type', 'st_pages_on_top', 'st_posts_on_top', 'st_pages_on_bot', 'st_posts_on_bot', 'st_post_excerpt', 'st_page','st_prompt','st_pubid','st_sent','st_services','st_hoverbar_services','st_pulldownbar_services', 'st_tags',	'st_upgrade_five','st_version','st_widget','st_username','st_pulldownlogo','copynshareSettings','protocolType', 'st_protocol');						
	foreach ($st_options as $option){
		delete_option($option);
	}
}

function getKeyFromTag(){
	$widget = get_option('st_widget');
	$pattern = "/publisher\=([^\&\"]*)/";
	preg_match($pattern, $widget, $matches);
	$pkey = $matches[1];
	if(empty($pkey)){
		return false;
	}
	else{
		return $pkey;
	}
}

function getNewTag($oldTag){
	$pattern = '/(http\:\/\/*.*)[(\')|(\")]/';
	preg_match($pattern, $oldTag, $matches);
	$url=$matches[1];

	$pattern = '/(type=)/';
	preg_match($pattern, $url, $matches);
	if(empty($matches)){
		$url.="&amp;type=wordpress".get_bloginfo('version');
	}

	$qs=parse_url($url);
	if($qs['query']){
		$qs=$qs['query'];
		$newUrl="http://w.sharethis.com/button/sharethis.js#$qs";
	}
	else{
		$newUrl=$url;
	}
	return $newTag='<script type="text/javascript" charset="utf-8" src="'.$newUrl.'"></script>';
}

function st_widget_head() {
	adding_st_filters();
	$widget = get_option('st_widget');
	if ($widget == '') {
	}
	else{
		//$widget = st_widget_add_wp_version($widget);
		$widget = st_widget_fix_domain($widget);
		$widget = preg_replace("/\&/", "&amp;", $widget);
	}

	print(stripslashes($widget));
}

function sendWelcomeEmail($newUser){
	$to=get_option('admin_email');
	$updatePage=get_option('siteurl');
	$updatePage.="/wp-admin/options-general.php?page=sharethis.php";

	$body = "The ShareThis plugin on your website has been activated on ".get_option('siteurl')."\n\n"
	."If you would like to customize the look of your widget, go to the ShareThis Options page in your WordPress administration area. $updatePage\n\n" 
	."Get more information on customization options at //support.sharethis.com/customer/portal/articles/446440-wordpress-integration" 
	."To get reporting on share data login to your account at //www.sharethis.com/account and choose options in the Analytics section\n\n"
    ."If you have any additional questions or need help please email us at support@sharethis.com\n\n--The ShareThis Team";

	$subject = "ShareThis WordPress Plugin";

	if(empty($to)){
		return false;
	}
	if($newUser){
		$subject = "ShareThis WordPress Plugin Activation";
		$body ="Thanks for installing the ShareThis plugin on your blog.\n\n" 
		."If you would like to customize the look of your widget, go to the ShareThis Options page in your WordPress administration area. $updatePage\n\n" 
		."Get more information on customization options at //support.sharethis.com/customer/portal/articles/446440-wordpress-integration\n\n" 		
		."If you have any additional questions or need help please email us at support@sharethis.com\n\n--The ShareThis Team";
	}
	$headers = "From: ShareThis Support <support@sharethis.com>\r\n" ."X-Mailer: php";
	update_option('st_sent','true');
	mail($to, $subject, $body, $headers);
}

function sendUpgradeEmail() {
	$to=get_option('admin_email');
	$updatePage=get_option('siteurl');
	$updatePage.="/wp-admin/options-general.php?page=sharethis.php";
	
	$body = "The ShareThis plugin on your website has been updated!\n\n"
	."If you would like to customize the look of your widget, go to the ShareThis Options page in your WordPress administration area. $updatePage\n\n" 
	."Get more information on customization options at //support.sharethis.com/customer/portal/articles/446440-wordpress-integration" 
	."To get reporting on share data login to your account at //www.sharethis.com/account and choose options in the Analytics section\n\n"
    ."If you have any additional questions or need help please email us at support@sharethis.com\n\n--The ShareThis Team";

	$subject = "ShareThis WordPress Plugin Updated";

	if(empty($to)){
		return false;
	}
	
	$headers = "From: ShareThis Support <support@sharethis.com>\r\n" ."X-Mailer: php";
	update_option('st_sent','true');
	mail($to, $subject, $body, $headers);
}

function st_link() {
	global $post;

	$sharethis = '<p><a href="//www.sharethis.com/item?&wp='
	.get_bloginfo('version').'&amp;publisher='
	.get_option('st_pubid').'&amp;title='
	.urlencode(get_the_title()).'&amp;url='
	.urlencode(get_permalink($post->ID)).'">ShareThis</a></p>';

	return $sharethis;
}

function sharethis_button() {
	echo st_makeEntries();
}

function st_remove_st_add_link($content) {
	remove_action('the_content', 'st_add_link');
	remove_action('the_content', 'st_add_widget');
	return $content;
}

// MODIFIES THE CONTENT OF THE PAGE
function st_add_widget($content) {
	if (!is_feed()) {
		return st_show_buttons($content);
	}
	
	return $content;
}

function st_show_buttons($content) {
	global $post;
	$postType = $post->post_type;
	
	if( !is_singular(array('post', 'page') ) &&  get_option('st_post_excerpt') == 'false') {
		return $content; // do not proceed - user has checked the option to hide buttons on excerpts		
	}
	
	$getTopOptions = get_option('st_'.$postType.'s_on_top');
	$getBotOptions = get_option('st_'.$postType.'s_on_bot');

	$selectedPage = get_option('st_page');
	if(empty($selectedPage)) $selectedPage = array();

	if(($post->post_type == 'page' && !in_array($post->ID , $selectedPage)) || $post->post_type == 'post') { 
		if ($getTopOptions == 'top' && $getBotOptions == 'bot') 
			return '<p class="no-break">'.st_makeEntries().'</p>'.$content.'<p>'.st_makeEntries().'</p>';	
		else if ($getTopOptions == 'top' && empty($getBotOptions))
			return '<p class="no-break">'.st_makeEntries().'</p>'.$content;
		else if(empty($getTopOptions) && $getBotOptions == 'bot')
			return $content.'<p class="no-break">'.st_makeEntries().'</p>';
	}
	
	return $content;
}

// 2006-06-02 Renamed function from st_add_st_link() to st_add_feed_link()
function st_add_feed_link($content) {
	if (is_feed()) {
		$content .= st_link();
	}

	return $content;
}

function adding_st_filters(){
	// 2006-06-02 Filters to Add Sharethis widget on content and/or link on RSS
	// 2006-06-02 Expected behavior is that the feed link will show up if an option is not 'no'
	if (get_option('st_add_to_content') != 'no' || get_option('st_add_to_page') != 'no') {
		add_filter('the_content', 'st_add_widget');
		
		// META GRAPH Plugin conflicts with Buttons Excerpts
		$current_plugins = get_option('active_plugins');		
		if( (!( (in_array('wp-open-graph/wp-open-graph.php', $current_plugins)) ||
			(in_array('wp-open-graph-meta/wp-open-graph-meta.php', $current_plugins)) ) )
			&&
			(!( (in_array('facebook/facebook.php', $current_plugins)) &&
			(in_array('wordpress-seo/wp-seo.php', $current_plugins)) ))
			) {
			// 2008-08-15 Excerpts don't play nice due to strip_tags().
			add_filter('get_the_excerpt', 'st_remove_st_add_link',9);
			add_filter('the_excerpt', 'st_add_widget');			
		}
		
	}
}

function st_widget_fix_domain($widget) {
	return preg_replace(
		"/\<script\s([^\>]*)src\=\"http\:\/\/sharethis/"
		, "<script $1src=\"http://w.sharethis"
		, $widget
		);
}

function st_widget_add_wp_version($widget) {
	preg_match("/([\&\?])wp\=([^\&\"]*)/", $widget, $matches);
	if ($matches[0] == "") {
		$widget = preg_replace("/\"\>\s*\<\/\s*script\s*\>/", "&wp=".get_bloginfo('version')."\"></script>", $widget);
		$widget = preg_replace("/widget\/\&wp\=/", "widget/?wp=", $widget);
	}
	else {
		$widget = preg_replace("/([\&\?])wp\=([^\&\"]*)/", "$1wp=".get_bloginfo('version'), $widget);
	}
	return $widget;
}


if (!function_exists('ak_can_update_options')) {
	function ak_can_update_options() {
		if (function_exists('current_user_can')) {
			if (current_user_can('manage_options')) {
				return true;
			}
		}
		else {
			global $user_level;
			get_currentuserinfo();
			if ($user_level >= 8) {
				return true;
			}
		}
		return false;
	}
}

function st_request_handler() {
	if (!empty($_REQUEST['st_action'])) {
		switch ($_REQUEST['st_action']) {
			case 'st_update_settings':
				
				if (function_exists('wp_verify_nonce')) {
					if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'st_nonce' ) ) {
						// This nonce is not valid.
						die( 'Security check' ); 
					}
				}
				if (ak_can_update_options()) {
					if($_POST['Edit'] == ""){
						$publisher_id=$_POST['st_pkey'];
						if($_POST['st_callesi'] == "0"){
							$cns_settings = $_POST['copynshareSettings'];
						}else{
							$cns_settings = "";
						}
						update_option('copynshareSettings', $cns_settings);
						
						if($_POST['st_user_name'] != "undefined"){
							update_option('st_username', $_POST['st_user_name']);
						}
						
						//update st_version to figure out which widget to use.
						if(!empty($_POST['st_version'])) {
							update_option('st_version', $_POST['st_version']);
							if (($_POST['st_version']) == '5x') {
								$st_switchTo5x = "true";
							} elseif (($_POST['st_version']) == '4x') {
								$st_switchTo5x = "false";
							}
						}
						
						if(empty($publisher_id)) $publisher_id = trim(makePkey());
						update_option('st_pubid', $publisher_id);
						update_option('st_widget', $_POST['st_widget']);//Fix for FB:13034
						
						if(!empty($_POST['st_tags'])){
							$tagsin=$_POST['st_tags'];
							$tagsin=preg_replace("/\\n|\\t/","", $tagsin);
							$tagsin=preg_replace("/\\\'/","'", $tagsin);
							$tagsin=preg_replace("/\"/","'", $tagsin);
							$tagsin=trim($tagsin);
							update_option('st_tags',$tagsin);
						}else{
							update_option('st_tags',' '); // in case of buttons not selected
						}
						
						if(!empty($_POST['protocolType'])) {
							update_option('protocolType', trim($_POST['protocolType']));
						}
						
						if(!empty($_POST['st_services'])){
							update_option('st_services', trim($_POST['st_services'],",") );
						}
						
						//Fix for FB:13034
						if(!empty($_POST['hoverbar']['services'])) {
							update_option('st_hoverbar_services', $_POST['hoverbar']['services'] );
						}
						
						//Fix for FB:13034
						if(!empty($_POST['pulldownbar']['services'])) {
							update_option('st_pulldownbar_services', $_POST['pulldownbar']['services'] );
						}
						if(!empty($_POST['pulldownbar']['logo'])) {
							update_option('st_pulldownlogo', $_POST['pulldownbar']['logo'] );
						}
						
						if(!empty($_POST['st_current_type'])){
							update_option('st_current_type', trim($_POST['st_current_type'],",") );
						}
						
						if(!empty($_POST['st_pages_on_top'])){
							update_option('st_pages_on_top', $_POST['st_pages_on_top'] );
						} else {
							update_option('st_pages_on_top', '' );
						}						
						
						if(!empty($_POST['st_posts_on_top'])){
							update_option('st_posts_on_top', $_POST['st_posts_on_top'] );
						} else {
							update_option('st_posts_on_top', '' );
						}						
						
						if(!empty($_POST['st_pages_on_bot'])){
							update_option('st_pages_on_bot', $_POST['st_pages_on_bot'] );
						} else {
							update_option('st_pages_on_bot', '' );
						}
						
						if(!empty($_POST['st_posts_on_bot'])){
							update_option('st_posts_on_bot', $_POST['st_posts_on_bot'] );
						} else {
							update_option('st_posts_on_bot', '' );
						}
						
						if($_POST['st_post_excerpt'] == 'true'){
							update_option('st_post_excerpt', $_POST['st_post_excerpt'] );
						} else {
							update_option('st_post_excerpt', 'false' );
						}
						
						$selPages = $_POST['st_page'];
						if((!empty($_POST['st_pages_on_top']) || !empty($_POST['st_pages_on_bot'])) && (!empty($selPages) && count($selPages) > 0)) {
							update_option('st_page', $selPages);
						} else {
							update_option('st_page', '');
						}
						
						die("SUCCESS");
					}
				}
				break;
		}
		die("FAILURE");
	}
}

function st_options_form() {
	$plugin_location=WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$publisher_id = get_option('st_pubid');
	$services = get_option('st_services');
	$tags = stripslashes(get_option('st_tags'));
	$tagsFromDb = stripslashes(get_option('st_tags'));
	$st_current_type=get_option('st_current_type');
	$st_current_type_from_db = get_option('st_current_type');
	$st_widget_version = get_option('st_version');
	$st_prompt = get_option('st_prompt');
	$st_username = get_option('st_username');
	$st_pulldownlogo = get_option('st_pulldownlogo');
	$st_hoverbarServices = get_option('st_hoverbar_services');
	$st_pulldownbarServices = get_option('st_pulldownbar_services');
	$cns_settings = get_option('copynshareSettings');
	$stProtocol = get_option('protocolType');
	$stPagesTop = get_option('st_pages_on_top');
	$stPagesBot = get_option('st_pages_on_bot');
	$stPostsTop = get_option('st_posts_on_top');
	$stPostsBot = get_option('st_posts_on_bot');
	$stPostExcerpt = get_option('st_post_excerpt');
	$freshInstalation = empty($services)?1:0;

	$checkPagesTop = '';
	$checkPagesBot = '';
	if($stPagesTop == 'top' && $stPagesBot == 'bot') {
		$checkPagesTop = 'checked="checked"';
		$checkPagesBot = 'checked="checked"';
	} else if(empty($stPagesTop) && $stPagesBot == 'bot') {
		$checkPagesTop = '';
		$checkPagesBot = 'checked="checked"';	
	} else if($stPagesTop == 'top' && empty($stPagesBot)) {
		$checkPagesTop = 'checked="checked"';
		$checkPagesBot = '';	
	}
	
	$checkPostsTop = '';
	$checkPostsBot = '';
	if($stPostsTop == 'top' && $stPostsBot == 'bot') {
		$checkPostsTop = 'checked="checked"';
		$checkPostsBot = 'checked="checked"';
	} else if(empty($stPostsTop) && $stPostsBot == 'bot') {
		$checkPostsTop = '';
		$checkPostsBot = 'checked="checked"';	
	} else if($stPostsTop == 'top' && empty($stPostsBot)) {
		$checkPostsTop = 'checked="checked"';
		$checkPostsBot = '';	
	}
	
	$checkPostExcerpt = '';
	if($stPostExcerpt == 'true') {
		$checkPostExcerpt = 'checked="checked"';		
	}else if($stPostExcerpt == 'false') {
		$checkPostExcerpt = '';		
	}else {
		// First installation - By default Checked
		$checkPostExcerpt = 'checked="checked"';
	}
	
	$isSecure = '';
	$isNonSecure = 'checked="checked"';
	if(!empty($stProtocol)) {
		if('https' == $stProtocol) {
			$isNonSecure = '';
			$isSecure = 'checked="checked"';
		} else {
			$isNonSecure = 'checked="checked"';
			$isSecure = '';		
		}
	} else {
		if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || $_SERVER['HTTPS'] == 'on') {
			$isNonSecure = '';
			$isSecure = 'checked="checked"';		
		} else {
			$isNonSecure = 'checked="checked"';
			$isSecure = '';	
		}
	}
	
	if(empty($st_username)){
		$st_username = "";
	}
	
	if(empty($st_pulldownlogo)){
		$st_pulldownlogo = "//sd.sharethis.com/disc/images/Logo_Area.png";
	}
	
	if(empty($pulldown_scrollpx))
		$pulldown_scrollpx = '';
		
	if(empty($st_current_type)){
		$st_current_type="_large";
		//$st_current_type_from_db="_large";
	}
	if(empty($services)){
		$services="facebook,twitter,linkedin,email,sharethis,fblike,plusone,pinterest";
	}
	if(empty($st_prompt)){
		//$services.=",instagram";
		update_option('st_prompt', 'true');
	}
	if(empty($tags)){
		$tagsFromDb = '';
		foreach(explode(',',$services) as $svc){
			$tags.="<span class='st_".$svc."_large' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='".$svc."'></span>";
			$tagsFromDb.="<span class='st_".$svc."_large' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='".$svc."'></span>";
		}
	}
	if(empty($st_widget_version)){
		$st_widget_version="5x";
	}
	if(empty($st_hoverbarServices)) {
		$st_hoverbarServices = '';
	}
	if(empty($st_pulldownbarServices)) {
		$st_pulldownbarServices = '';
	}
	if(empty($cns_settings)) {
		$cns_settings = '';
	}
	
	/* Retrieves widget version from the database */ 
	$widget5xSelected = "";
	$widget4xSelected = "";
	if($st_widget_version == "5x"){
		$widget5xSelected = "selected";
	}else if($st_widget_version == "4x"){
		$widget4xSelected = "selected";
	}

	$widgetTag = stripslashes(get_option('st_widget'));
	
	if(empty($publisher_id)){
		$toShow="";
		// Re-generate new random publisher key	
		$publisher_id=trim(makePkey());
	}
	else{
		$toShow = $widgetTag;
	}	

	/* Pulls the scrollpx value for the  pull down bar  */
	$a = preg_replace('~[\r\n]+~', '', $toShow);
	if (preg_match('/pulldownbar/',$a)) {
            $pattern = "/<script(.*?)<\/script>/";
            preg_match_all($pattern, $a, $matches);
            foreach($matches[1] as $k=>$v)
            {
                  if (preg_match('/pulldownbar/',$v)) {
                        preg_match("/\"scrollpx\":[\s\"\']{0,}(\d+)[\s\"\']{0,}/", $v, $matches);
                        $pulldown_scrollpx = $matches[1];
                        break;
                  }
            }
      }
	
	$wpVersion = trim(get_bloginfo('version'));
	$scriptProtocolCss = '';
	if(version_compare($wpVersion, '3.7.1', '<=')) {
		$scriptProtocolCss = "margin-right:7px;";
	} else {
		$scriptProtocolCss = '';
	}
	
	$nonceField = '';
	if (function_exists('wp_nonce_field')){ 
		$nonceField = wp_nonce_field('st_nonce');
	} 

	$stType = 'wordpress'.trim(get_bloginfo('version'));
	$sharethis_callesi = (preg_match('/doNotCopy/',$widgetTag))?0:1;
	print('	
		<link rel="stylesheet" type="text/css" href="'.$plugin_location.'css/st_wp_style.css"/>	
		<link rel="stylesheet" type="text/css" href="'.$plugin_location.'css/stlib_picker.css" />
		<script type="text/javascript">
			if (typeof(stlib) == "undefined") { var stlib = {}; }
			if (typeof(stlib_picker) == "undefined") { var stlib_picker = {}; }
			if (typeof(stlib_preview) == "undefined") { var stlib_preview = {}; }
			stlib.getButtonConfig = {
				dest : "website",
				style : "chickletStyle"
			}
			var st_button_state = 1;
		</script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
		<script type="text/javascript" src="http://w.sharethis.com/dynamic/stlib/allServices.js"></script>
		<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
		<script type="text/javascript" src="http://s.sharethis.com/loader.js"></script>
		<script type="text/javascript" src="'.$plugin_location.'libraries/get-buttons-new.js"></script>
		<link rel="stylesheet" type="text/css" href="http://w.sharethis.com/button/css/buttons.css"></link>
		<script type="text/javascript" src="'.$plugin_location.'libraries/stlib_picker.js"></script>
		<script type="text/javascript" src="'.$plugin_location.'libraries/stlib_preview.js"></script>
		<script type="text/javascript">

			  var _gaq = _gaq || [];
			  _gaq.push(["_setAccount", "UA-1645146-1"]);
			  _gaq.push(["_trackPageview"]);
			
			  (function() {
			    var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
			    ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
			    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
			  })();
		</script>
		
		<iframe id="settingSaved" name="settingSaved" width="0" height="0"></iframe>
		
			<div class="wrap">
				<div style="padding:10px;border:1px solid #aaa;background-color:#9fde33;text-align:center;display:none;" id="st_updated">Your options were successfully updated</div>
				<div id="showLoadingStatus" class="wp_st_showLoadingStatus">Loading please wait...</div>
				<div id="wp_st_outerContainer" style="width:1000px;">
				<div id="st_title" style="width: 100%; height: 38px;">
					<div class="wp_st_header_title">
						<label>Welcome to ShareThis for WordPress</label>
					</div>
					<div class="wp_st_userinfo">
						<div id="usernameContainer" style="display:none">You are logged in as : <span id="login_name"></span><span style="float:right;font-size:16px;cursor:pointer;" onclick="st_signOut(\''.trim(makePkey()).'\')">Sign out</span></div>
						<div id="pbukeyContainer" style="display:none">Your publisher key : <span id="login_key"></span></div>
					</div> 
				</div> 
				<form id="ak_sharethis" name="ak_sharethis" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post" >
					'.$nonceField.'
					<fieldset class="options">
						<div id="step1" class="wp_st_parentDiv">
							<div id="wp_st_header" class="wp_st_headerFooter">
								<div class="wp_st_left_navigator">&nbsp;
									<img class="wp_st_arrow wp_st_leftarrow" src="'.$plugin_location.'images/leftArrow.png" onclick="moveToPrevious(st_button_state)" style="display:none"/>
									<label class="wp_st_backText" onclick="moveToPrevious(st_button_state)" style="display:none">Back : </label>
									<label class="wp_st_backTitle" style="display:none" onclick="moveToPrevious(st_button_state)">Select Serivce</label>
								</div>
								<div class="wp_st_header_middle">
									<label id="wp_st_slideTitle">1. Choose Buttons and Options</label>
								</div>	
								<div class="wp_st_right_navigator">
									<label class="wp_st_nextText" onclick="moveToNext(st_button_state)">Next : </label>
									<label class="wp_st_nextTitle" onclick="moveToNext(st_button_state)">Select Services</label>
									<img id="st_rightarrow" class="wp_st_arrow wp_st_rightarrow" src="'.$plugin_location.'images/rightArrow.png" onclick="moveToNext(st_button_state)"/>
									<input type="submit" id="edit" value="Edit" name="Edit" class="wp_st_editButton"/>
								&nbsp;</div>
							</div>
							<div id="wp_st_mainbody">
								<div class="wp_st_centerContainer1">
										<div id="getchicklet" class="configChooser hcountStyleConfig vcountStyleConfig chickletStyleConfig wp_st_show">
											<div class="wp_st_previewDiv">
												<span>Preview (or Current Configuration):</span>
												<div id="barPreview1" class="wp_st_barPreview1">
													<div class="wp_st_bartext">
														<div class="wp_st_barPreviewHeader">Look to the side!</div>
														<div class="wp_st_barPreviewText">Preview your bar at the side of the page</div>
													</div>
												</div>
												<div id="preview" style="margin-top:30px;font-size:30px;"></div>
												<div id="errorMessage" style="margin-top:30px;font-size:30px;" class="wp_st_error_message"></div>
												<div id="barPreview2" class="wp_st_barPreview2">
													<div class="wp_st_bartext">
														<div class="wp_st_barPreviewHeader">Look to the side!</div>
														<div class="wp_st_barPreviewText">Preview your bar at the side of the page</div>
													</div>
												</div>
												<div id="barPreview3" class="wp_st_barPreview3">
													<div class="wp_st_bartext">
														<div class="wp_st_barPreviewHeader">Look Up!</div>
														<div class="wp_st_barPreviewText">Preview your bar at the top of the page</div>
													</div>
												</div>
											</div>
										</div>	
								</div>
								<hr id="wp_st_separator"/>
								
								<!-- STEP 1 -->
								<div id="st_step1" class="wp_st_centerContainer2">	
									
									<div id="wp_st_styleLinks" class="linksDiv">
										<h1 class="nonbars">Choose a button style:</h1>
										<h1 class="bars wp_st_show">Choose more options</h1>
										<div style="clear:both;"></div> 
										<div class="wp_st_widget5x">	
											<ul class="nonbars" style="padding-left:80px">
												<li class="wp_st_styleLink chickletStyle jqBtnStyle" id="chickletStyle"><div class="wp_st_hoverState2 chickletStyle"></div><div class="wp_st_hoverState chickletStyle">Prominent, yet minimalistic, the classic style of these buttons display sharing icons in 2 different sizes (16x16 &amp; 32x32).</div><img src="'.$plugin_location.'images/Button4.png" class="wp_st_chickletStyleButtonImg"/></li>
												<li class="wp_st_styleLink hcountStyle jqBtnStyle" id="hcountStyle"><div class="wp_st_hoverState2 hcountStyle"></div><div class="wp_st_hoverState hcountStyle">Sharing buttons with horizontal counters to publicly display the sharing activity for that piece of content.</div><img src="'.$plugin_location.'images/HORZ.png" class="wp_st_hcountStyleButtonImg"/></li>
												<li class="wp_st_styleLink vcountStyle jqBtnStyle" id="vcountStyle"><div class="wp_st_hoverState2 vcountStyle"></div><div class="wp_st_hoverState vcountStyle">Sharing buttons with vertical counters to publicly display the sharing activity for that piece of content.</div><img src="'.$plugin_location.'images/VERT.png" class="wp_st_vcountStyleButtonImg"/></li>
											</ul>
											<ul style="width:100px">
												<li style="border:0px" class="wp_st_inputBoxLI">
													<div id="selectSizeType" class="wp_st_selectSizeType">
														<div>Button Size :</div>
														<div><input type="radio" name="selectSize_type" value="16x16"/>  Small</div>
														<div><input checked="true" type="radio" name="selectSize_type" value="32x32"/>  Large</div>
													</div>
												</li>
											</ul>	
											
										</div>	
										<div class="wp_st_vseparator" style="height:478px; margin-top: -25px">
											<hr/>
										</div>
											
										<div class="wp_st_widget4x">	
											<ul class="bars wp_st_show" style="padding-left:80px">
												<li class="wp_st_styleLink jqBarStyle hoverbarStyle" id="hoverbarStyle"><div class="wp_st_hoverState2 hoverbarStyle"></div><div class="wp_st_hoverState hoverbarStyle">This bar can float either on the left side or the right side of the page to provide an always-visible view of the sharing tools.</div><img id="hoverBarImage" src="'.$plugin_location.'images/HOVER_Buttons.png" class="wp_st_hoverbarStyleButtonImg"/><img id="hoverbarLoadingImg" src="'.$plugin_location.'images/loading.gif" class="wp_st_loadingImage" style="display:none"/></li>
												<li class="wp_st_styleLink jqBarStyle pulldownStyle" id="pulldownStyle"><div class="wp_st_hoverState2 pulldownStyle"></div><div class="wp_st_hoverState pulldownStyle">This bar with sharing buttons is placed at the top of page, but appears only when the reader scrolls down.</div><img id="pullDownBarImage" src="'.$plugin_location.'images/PULLDOWN.png" class="wp_st_pulldownStyleButtonImg"/><img id="pulldownLoadingImg" src="'.$plugin_location.'images/loading.gif" class="wp_st_loadingImage" style="display:none"/></li>											
											</ul>
											<ul style="width:100px">
												<li style="border:0px" class="wp_st_inputBoxLI"><div class="btnDiv" >
													<div id="hoverbar_selectDock" class="wp_st_hoverbar_selectDock">
														<div>Docking Position :</div>
														<div><input type="radio" value="left" name="selectDock_type"/>  Left</div>
														<div><input checked="true" type="radio" value="right" name="selectDock_type"/>  Right</div>
													</div>
												</div>
												</li>
												
												<li class="wp_st_pulldownCustomization wp_st_inputBoxLI" style="border:0px" >
													<span id="st_configure_pulldown" style="display:none">&nbsp;&nbsp;Configure it!</span>
												</li>									
																							
											</ul>		
										</div>	
										<div style="clear:both;" class="bars wp_st_show"></div>
									</div>
								</div>
								
								
								<div id="st_pulldownConfig" class="wp_st_pulldownConfig" style="display:none;"> 
									<h3 style="margin-left:5px">Customize PullDownBar:</h3>
									<ul>
										<li>
											<div id="pulldown_selectDock" class="wp_st_pulldown_selectDock">
												<label style="margin-right:138px;">Logo URL:</label>
												<input class="wp_st_pulldown_optionsTextbox" id="pulldown_optionsTextbox_id" name="pulldown_optionsTextbox_id" type="textbox" value="" data-value=""/><span class="pulldown_previewButton">Update Preview</span>
											</div>
										</li>
										<li>
											<div id="pulldown_selectDock" class="wp_st_pulldown_selectDock">
												<span>
													<label style="margin-right:100px;">Scroll Height (px):</label><input style="width:10%;margin-bottom:0px;margin-left:5px" class="wp_st_pulldown_optionsTextbox" id="selectScrollHeight_id" name="selectScrollHeight_id" type="textbox" value="50" data-value=""/>
												</span>
											</div>
										</li>
									</ul>
								</div>
								
								<!-- STEP 2 -->
								<div id="st_step2" class="wp_st_centerContainer2" style="display:none;">
									<div style="height:230px;text-align: center;">
										<div id="mySPicker"></div> 
									</div>	
								</div>
								
								<!-- STEP 3 -->
								<div id="st_step3" class="wp_st_centerContainer2" style="display:none;">
									<div id="addOptDivSep" style="padding-bottom:10px"></div>
									<div id="addOptDiv" class="heading st_additional_option_heading">
										<span id="headingAddionalOptions" class="headingAddionalOptions_right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Additional Options</span>
									</div>								
									<div id="addOptions" style="height:250px;text-align: center;display:none;">
										<div id="st_widget5x" class="wp_st_widget5x">
											<div style="width:50%; float:left;margin-top:5px;">
												<img src="'.$plugin_location.'images/widget-5x.png"/>
											</div>
											<div style="width:48%; float:right;">
												<p id="st_5xwidget" class="wp_st_post_heading '.$widget5xSelected.'" style="width:86%">Multi Post</p>
												<p class="wp_st_text">Sharing takes place inside the widget, without taking users away from your site. Preferences are saved so your users can share to more than one service at the same time.</p>	
											</div>
										</div>
										<div class="wp_st_vseparator">
										<hr/>
										</div>
										<div id="st_widget4x" class="wp_st_widget4x">
											<div style="width:48%; float:left;position: relative">
												<img src="'.$plugin_location.'images/widget-4x.png"/>
											</div>
											<div style="width:48%; float:right">
												<p id="st_4xwidget" class="wp_st_post_heading '.$widget4xSelected.'" style="width:86%">Direct Post</p>
												<p class="wp_st_text">Your users will be redirected to Facebook, Twitter, etc when clicking on the corresponding buttons. The widget is opened when users click on "Email" and "ShareThis".</p>	
											</div>
										</div>
									</div>	
							</div>							
							<div id="st_splServiceContainer" class="wp_st_splServiceContainer">
								
							</div>
							<!-- STEP 4 -->	
							<div id="st_step4"  class="wp_st_centerContainer2" style="display:none;">
								<div style="height:118px;text-align: center;">
									<div id="" class="wp_st_widget5x">
										<div class="wp_st_copynshare_heading">
											<span id="wp_st_copynshare">Enable CopyNShare</span>
										</div>
										<div>
											<div class="wp_st_copynshare_text">
												<p class="">CopyNShare is the new ShareThis widget feature that enables you to track the shares that occur when a user copies and pastes your websites URL or content</p>
											</div>
											<div id="st_cns_settings" class="wp_st_copynshare_checkboxes">
												<input type="checkbox" class="cnsCheck wp_st_defaultCursor" id="donotcopy" name="donotcopy" value="true" ></input>
												<label for="donotcopy" class="cnsCheck" id="wp_st_donotcopy_label">&nbsp;Measure copy and shares of your website\'s content</label>
												<br />
												<br />
												<input type="checkbox" class="cnsCheck wp_st_defaultCursor" id="hashaddress" name="hashaddress" value="false" ></input>
												<label for="hashaddress" class="cnsCheck" id="wp_st_hashaddress_label">&nbsp;Measure copy and shares of your website\'s URLs</label>
											</div>
									  </div>
								</div>
							 </div>
							 <hr id="wp_st_separator">
							<div style="height:auto;text-align: center;margin-top:15px;">
									<div>
										<div class="wp_st_customizewidget_heading" style="padding-bottom:5px;">
											<span>Customize Widget Position</span>
										</div>
										<div class="wp_st_customizewidget_options">
											<div>
												<div style="float:left;font-size:15px;"> 
													<span>Pages</span>
												</div>
												<div style="margin-top: 5px;font-size:15px;margin-left:301px;">
													<span>Posts</span>
												</div>
											</div>
											<div style="margin-top:10px;">
												<div style="float:left;"> 
													<span style="cursor:auto;"><input id="st_pages_on_top" class="cnsCheck wp_st_defaultCursor" type="checkbox" value="top" name="st_pages_on_top" '.$checkPagesTop.'></span>
													<span>Show buttons on <strong style="font-family: sans-serif;font-weight:bold;">top of pages</strong></span>
												</div>
												<div style="margin-top: 5px"> 
													<span style="cursor:auto;margin-left:264px"><input id="st_posts_on_top" class="cnsCheck wp_st_defaultCursor" type="checkbox" value="top" name="st_posts_on_top" onclick="setPostExcerpt()" '.$checkPostsTop.'></span>
													<span>Show buttons on <strong style="font-family: sans-serif;font-weight:bold;">top of posts</strong></span>
												</div>												
											</div>
											<div>
												<div style="float:left;">
													<span style="cursor:auto;"><input id="st_pages_on_bot" class="cnsCheck wp_st_defaultCursor" type="checkbox" value="bot" name="st_pages_on_bot" '.$checkPagesBot.'></span>
													<span>Show buttons on <strong style="font-family: sans-serif;font-weight:bold;">bottom of pages</strong></span>
												</div>
												<div style="margin-top: 7px">
													<span style="cursor:auto;margin-left:264px;"><input id="st_posts_on_bot" class="cnsCheck wp_st_defaultCursor" type="checkbox" value="bot" name="st_posts_on_bot" onclick="setPostExcerpt()" '.$checkPostsBot.'></span>
													<span>Show buttons on <strong style="font-family: sans-serif;font-weight:bold;">bottom of posts</strong></span>
												</div>												
											</div>
									  </div>
									  <div style="margin-bottom:30px;"></div>
									  <div class="wp_st_customizewidget_heading" style="padding-bottom:5px;">
											<span>Customize Post Excerpts</span>
										</div>
									  <div>
										<div style="float:left;">
											<div style="margin-top: 7px">
													<span style="cursor:auto;margin-left:52px;"><input id="st_post_excerpt" class="cnsCheck wp_st_defaultCursor" type="checkbox" value="true" name="st_post_excerpt" '.$checkPostExcerpt.'></span>
													<span>Show buttons on <strong style="font-family: sans-serif;font-weight:bold;">Post Excerpt</strong></span>
												</div>	
										</div>
									   </div>		
									  <div style="margin-bottom:50px;"></div>
									  <div class="wp_st_customizewidget_heading">
											<span class="heading">
											<span id="headingimgPageList" class="headingimgPageList_right"></span>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Manage Page Exceptions
											</span>
									  </div>
									  <br/>
									  <div id="divPageList" style="display:none;">
										  <div class="wp_st_customizewidget_heading">
												<span style="font-size:12px;">Do <strong style="font-family: sans-serif;font-weight:bold;"><i>not</i></strong> show on</span>
										  </div>
											<div style="border: 1px solid #AAAAAA; overflow: auto; width: 43%; margin-left: 50px; height: 180px;margin-top:5px;">
												'.st_get_list_of_pages().'
											</div>
										</div>
									  
								</div>
							 </div>
						</div> 
							
							<!-- STEP 5 -->
							<div id="st_step5" class="wp_st_centerContainer2" style="display:none;">
								<div id="loginWindowDiv" class="wp_st_loginWindowDiv">
									<iframe id="loginFrame" width="644px" height="398px" frameborder="0" src="//www.sharethis.com/external-login?pluginType=newPlugins"></iframe>
									<div class="wp_st_login_message">You are successfully logged-in with ShareThis.</div>		
								</div>
							</div>
						
							<!-- STEP 6 -->							
							<div id="st_step6" class="wp_st_centerContainer2" style="display:none;">
								<div id="st_additional_options" class="wp_st_additional_options">
								
								</div>							
								<div style="margin-left: -367px;">
									<h1><span class="heading" style="font-size:18px">
										<span id="codeToggle" class="headingimg_right" style="left:249px;left:240px\9;"></span>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;View &amp edit code:
									</span></h1>
								</div>							
								<div id="codeDiv" style="display:none;">
									<div id="divScripTag" style="background:#ECECEC;display:inline-block;padding:5px;">
										<div style="float: left;color:#36759A;">Modify script tags<a style="padding-left:5px;" href="//support.sharethis.com/customer/portal/articles/464663-customize-functionality" title="Customize Functionality" target="_blank"><img src="'.$plugin_location.'images/QUESTION_Icon.png" /></a></div>
										<div style="float: left; margin-left: 288px;color:#36759A;margin-top:1px;"><input type="radio" name="protocolType" id="typehttp" value="http" '.$isNonSecure.' style="'.$scriptProtocolCss.'"/>http&nbsp;&nbsp;&nbsp;</div>
										<div style="float:left;color:#36759A;"><input type="radio" name="protocolType" id="typehttps" value="https" '.$isSecure.' style="'.$scriptProtocolCss.'" />https<a style="padding-left:5px;" href="//support.sharethis.com/customer/portal/articles/475097-ssl-support" title="SSL Support" target="_blank"><img src="'.$plugin_location.'images/QUESTION_Icon.png" /></a></div>
									</div>
									<div style="clear:both;"><textarea id="st_widget" name="st_widget" style="height: 150px; width: 525px;font-size:12px;">'.htmlspecialchars($toShow).'</textarea></div>
									<div>&nbsp;</div>
									<div id="divHtmlTag" style="background:#ECECEC;display:inline-block;padding:5px;width:517px;text-align:left;">
										<div style="float: left;color:#36759A;">Modify HTML tags<a style="padding-left:5px;" href="//support.sharethis.com/customer/portal/articles/475079-share-properties-and-sharing-custom-information#Properties_Tags" title="Share Properties and Sharing Custom Information" target="_blank"><img src="'.$plugin_location.'images/QUESTION_Icon.png" /></a></div>
									</div>									
									<div style="clear:both;"><textarea id="st_tags" name="st_tags" style="height: 150px; width: 525px;font-size:12px;">'.htmlspecialchars($tags).'</textarea></div>
								</div>							
							</div>
							
							<div id="lastStep" style="padding-bottom:0px;"><input type="submit" onclick="st_log();" id="wp_st_savebutton" value="SAVE"  name="submit_button" value="'.__('Update ShareThis Options', 'sharethis').'" style="display:none;"/></div>
							
							<div id="wp_st_footer" class="wp_st_headerFooter">
							<div class="wp_st_left_navigator" >&nbsp;
									<img class="wp_st_arrow wp_st_leftarrow " src="'.$plugin_location.'images/leftArrow.png" onclick="moveToPrevious(st_button_state)" style="display:none"/>
									<label class="wp_st_backText" onclick="moveToPrevious(st_button_state)" style="display:none">Back : </label>
									<label class="wp_st_backTitle" style="display:none" onclick="moveToPrevious(st_button_state)">Select Serivce</label>
								</div>
								<div class="wp_st_footer_middle">
									<div id="wp_st_stepfooter">Step 1 of 6</div>
									<div id="wp_st_navDots">
										<div class="wp_st_navSlideDot wp_st_slideSelected" id="navDotSlide1" value="1">&nbsp;</div>
										<div class="wp_st_navSlideDot" id="navDotSlide2" value="2">&nbsp;</div>
										<div class="wp_st_navSlideDot" id="navDotSlide3" value="3">&nbsp;</div>
										<div class="wp_st_navSlideDot" id="navDotSlide4" value="4">&nbsp;</div>
										<div class="wp_st_navSlideDot" id="navDotSlide5" value="5">&nbsp;</div>
										<div class="wp_st_navSlideDot" id="navDotSlide6" value="6">&nbsp;</div>
									</div> 
								</div>	
								<div class="wp_st_right_navigator" style="position:relative;right:6px;">
									<label class="wp_st_nextText" onclick="moveToNext(st_button_state)">Next : </label>
									<label class="wp_st_nextTitle" onclick="moveToNext(st_button_state)">Select Services</label>
									<img class="wp_st_arrow wp_st_rightarrow" src="'.$plugin_location.'images/rightArrow.png" onclick="moveToNext(st_button_state)"/>
								</div>
							</div>
						</div>
					</div>
						
						<script src="'.$plugin_location.'js/sharethis.js" type="text/javascript"></script>
					</fieldset>

					<input type="hidden" id="is_hoverbar_selected" value=""/>				
					<input type="hidden" id="is_copynshre_selected" value=""/>
					<input type="hidden" name="st_action" value="st_update_settings" />
					
					<input type="hidden" name="st_version" id="st_version" value="'.$st_widget_version.'"/>
					<input type="hidden" name="st_services" id="st_services" value="'.$services.'"/>
					<input type="hidden" name="st_current_type" id="st_current_type" value="'.$st_current_type.'"/>
					<input type="hidden" name="st_current_type_from_db" id="st_current_type_from_db" value="'.$st_current_type_from_db.'"/>
					<input type="hidden" name="st_tags_from_db" id="st_tags_from_db" value="'.htmlspecialchars($tagsFromDb).'"/>
					<input type="hidden" name="st_script_tags_from_db" id="st_script_tags_from_db" value="'.htmlspecialchars($toShow).'"/>
					
					<input type="hidden" name="st_type" id="st_type" value="'.$stType.'"/>
					<input type="hidden" name="st_pkey" id="st_pkey" value="'.htmlspecialchars($publisher_id).'"/>
					<input type="hidden" name="st_user_name" id="st_user_name" value="'.$st_username.'"/> 
					
					<input type="hidden" name="selectedBar" id="st_selected_bar" value=""/>
					<input type="hidden" name="hoverbar[position]" id="st_hoverbar_position" value=""/>
					<input type="hidden" name="hoverbar[services]" id="st_hoverbar_services" value="'.$st_hoverbarServices.'"/>
					
					<input type="hidden" name="pulldownbar[scrollpx]" id="st_pulldownbar_scrollpx" value="'.$pulldown_scrollpx.'"/>
					<input type="hidden" name="pulldownbar[logo]" id="st_pulldownbar_logo" value="'.$st_pulldownlogo.'"/>
					<input type="hidden" name="pulldownbar[services]" id="st_pulldownbar_services" value="'.$st_pulldownbarServices.'"/>
					
					
					<input type="hidden" name="copynshareSettings" id="copynshareSettings" value="'.$cns_settings.'"/>
					<input type="hidden" name="st_callesi" id="st_callesi" value="'.$sharethis_callesi.'" />
					<input type="hidden" id="freshInstalation" value="'.$freshInstalation.'"/>
				</form>
			</div>
		</div>	
	');
}

function st_get_page_ids() {
	$args = array(
		'sort_order' => 'DESC',
		'sort_column' => 'post_date',
		'hierarchical' => 1,
		'exclude' => '',
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'child_of' => 0,
		'parent' => -1,
		'exclude_tree' => '',
		'number' => '',
		'offset' => 0,
		'post_type' => 'page',
		'post_status' => 'publish'
	); 
	$pages = get_pages($args);
	
	$arrIds = array();
	foreach ( $pages as $page ) {
		if(!in_array($page->ID, $arrIds)) {
			$arrIds[] = $page->ID;
			getPageIdsRecursive($page, $arrIds);
		}
	}
	
	return $arrIds;
}

function getPageIdsRecursive($page, &$arrIds) {
	$pg = get_pages(array('child_of' => $page->ID));
	foreach ( $pg as $p ) {
		if(!in_array($p->ID, $arrIds)) {
			$arrIds[] = $p->ID;
			getPageIdsRecursive($p, $arrIds);
		}
	}
	return $arrIds;
}

function st_get_list_of_pages() {
	$option = '';
	$args = array(
		'sort_order' => 'DESC',
		'sort_column' => 'post_date',
		'hierarchical' => 1,
		'exclude' => '',
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'child_of' => 0,
		'parent' => -1,
		'exclude_tree' => '',
		'number' => '',
		'offset' => 0,
		'post_type' => 'page',
		'post_status' => 'publish'
	); 
	$pages = get_pages($args);
	
	$elemDisabled = '';
	$topPageIds = get_option('st_pages_on_top');
	$botPageIds = get_option('st_pages_on_bot');
	if(empty($topPageIds) && empty($botPageIds))
		$elemDisabled = 'disabled="disabled"';
		
	$selectedPages = get_option('st_page');
	$tempArr = array();
	foreach ( $pages as $page ) {
		if(!in_array($page->ID, $tempArr)) {
			$option .= '<div class="st_page_row" '.$elemDisabled.'>';
			if(!empty($selectedPages) && count($selectedPages) > 0 && in_array($page->ID, $selectedPages))
				$option .= '<span style="cursor:auto;" '.$elemDisabled.'><input '.$elemDisabled.' id="st_page'.$page->ID.'" checked="checked" class="cnsCheck wp_st_defaultCursor" type="checkbox" name="st_page[]" value="'.$page->ID.'"></span>';
			else
				$option .= '<span style="cursor:auto;" '.$elemDisabled.'><input '.$elemDisabled.' id="st_page'.$page->ID.'" class="cnsCheck wp_st_defaultCursor" type="checkbox" name="st_page[]" value="'.$page->ID.'"></span>';
			
			if(strlen($page->post_title) > 70)
				$option .= '<span '.$elemDisabled.'>'.substr($page->post_title,0,60).'......</span></div>';
			else
				$option .= '<span '.$elemDisabled.'>'.$page->post_title.'</span></div>';
				
			$option .= getPageRecursive($page, $tempArr, $selectedPages, $elemDisabled);
		}
  }
  
  return $option;
}

function getPageRecursive($page, &$tempArr, $selectedPages, $elemDisabled, $lvl = 0) {
	$option = '';
	$lvl++;
	$pg = get_pages(array('child_of' => $page->ID));
	foreach ( $pg as $p ) {
		if(!in_array($p->ID, $tempArr)) {
			$option .= '<div class="st_page_row" '.$elemDisabled.'>';
			if(!empty($selectedPages) && count($selectedPages) > 0 && in_array($p->ID, $selectedPages))
				$option .= '<span style="cursor:auto;" '.$elemDisabled.'><input '.$elemDisabled.' id="st_page'.$p->ID.'" checked="checked" class="cnsCheck wp_st_defaultCursor" type="checkbox" name="st_page[]" value="'.$p->ID.'"></span>';
			else
				$option .= '<span style="cursor:auto;" '.$elemDisabled.'><input '.$elemDisabled.' id="st_page'.$p->ID.'" class="cnsCheck wp_st_defaultCursor" type="checkbox" name="st_page[]" value="'.$p->ID.'"></span>';
						
			if(strlen($p->post_title) > 70)
				$option .= '<span '.$elemDisabled.'>'.str_repeat('&nbsp;',($lvl*3)). substr($p->post_title,0,60).'......</span></div>';
			else
				$option .= '<span '.$elemDisabled.'>'.str_repeat('&nbsp;',($lvl*3)). $p->post_title.'</span></div>';
				
			$tempArr[] = $p->ID;
			$option .= getPageRecursive($p, $tempArr, $selectedPages, $elemDisabled, $lvl);
		}
	}
	return $option;
}

function st_menu_items() {
	if (ak_can_update_options()) {
		add_options_page(
		__('ShareThis Options', 'sharethis')
		, __('ShareThis', 'sharethis')
		, 'manage_options'
		, basename(__FILE__)
		, 'st_options_form'
		);
	}
}

function st_makeEntries(){
	global $post;
	//$st_json='{"type":"vcount","services":"sharethis,facebook,twitter,email"}';
	$out="";
	$widget=get_option('st_widget');
	$tags=get_option('st_tags');
	if(!empty($widget)){
		if(preg_match('/buttons.js/',$widget)){
			if(!empty($tags)){
				$tags=preg_replace("/\\\'/","'", $tags);
				$tags=preg_replace("/<\?php the_permalink\(\); \?>/",get_permalink($post->ID), $tags);
				$tags=preg_replace("/<\?php the_title\(\); \?>/",strip_tags(get_the_title()), $tags);
				$tags=preg_replace("/{URL}/",get_permalink($post->ID), $tags);
				$tags=preg_replace("/{TITLE}/",strip_tags(get_the_title()), $tags);
			}else{
				$tags="<span class='st_sharethis' st_title='".strip_tags(get_the_title())."' st_url='".get_permalink($post->ID)."' displayText='ShareThis'></span>";
				$tags="<span class='st_facebook_buttons' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='Facebook'></span><span class='st_twitter_buttons' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='Twitter'></span><span class='st_email_buttons' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='Email'></span><span class='st_sharethis_buttons' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='ShareThis'></span><span class='st_fblike_buttons' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='Facebook Like'></span><span class='st_plusone_buttons' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='Google +1'></span><span class='st_pinterest _buttons' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='Pinterest'></span>";	
				$tags=preg_replace("/<\?php the_permalink\(\); \?>/",get_permalink($post->ID), $tags);
				$tags=preg_replace("/<\?php the_title\(\); \?>/",strip_tags(get_the_title()), $tags);		
			}
			$out=$tags;	
		}else{
			$out = '<script type="text/javascript">SHARETHIS.addEntry({ title: "'.strip_tags(get_the_title()).'", url: "'.get_permalink($post->ID).'" });</script>';
		}
	}
	return $out;
}


function makePkey(){
	return "wp.".sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),mt_rand( 0, 0x0fff ) | 0x4000,mt_rand( 0, 0x3fff ) | 0x8000,mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
	// return "paste-your-publisher-key-here";
}

function st_styles(){
	$widget=get_option('st_widget');	
	if(!empty($widget)){
		if(preg_match('/pulldownbar/',$widget)){
			$pulldownBarLogo = get_option('st_pulldownlogo');
			$custom_css = "
			.stpulldown-gradient
			{
				background: #E1E1E1;
				background: -moz-linear-gradient(top, #E1E1E1 0%, #A7A7A7 100%); /* firefox */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#E1E1E1), color-stop(100%,#A7A7A7)); /* webkit */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#E1E1E1', endColorstr='#A7A7A7',GradientType=0 ); /* ie */
				background: -o-linear-gradient(top, #E1E1E1 0%,#A7A7A7 100%); /* opera */
				color: #636363;
			}
			#stpulldown .stpulldown-logo
			{
				height: 40px;
				width: 300px;
				margin-left: 20px;
				margin-top: 5px;
				background:url('".$pulldownBarLogo."') no-repeat;
			}
			#stpulldown, #stpulldown *, .entry-content, .entry-content * {
				-webkit-box-sizing: content-box !important;
				-moz-box-sizing:    content-box !important;
				box-sizing:         content-box !important;
			}";
			echo "<style type='text/css'>";
			echo $custom_css;
			echo "\n</style>\n";
		}
		if(preg_match('/hoverbuttons/',$widget)){
			echo "<style type='text/css'>
					#sthoverbuttons #sthoverbuttonsMain, .stMainServices {
						-webkit-box-sizing: content-box !important;
						-moz-box-sizing:    content-box !important;
						box-sizing:         content-box !important;
					}
				</style>";
		}
		
		echo "<style type='text/css'>
					.no-break br {
						display: none !important;
					}
			</style>";
		
	}	
}

function st_load_custom_scripts() {
// To set plugin path for JS files
echo '<script type="text/javascript">

/* <![CDATA[ */
var st_script_vars = {"plugin_url":"'.plugin_dir_url( __FILE__ ).'"};
/* ]]> */
</script>';

}

add_action('wp_head', 'st_widget_head');
add_action('init', 'st_request_handler', 9999);
add_action('admin_menu', 'st_menu_items');
add_action( 'wp_enqueue_scripts', 'st_styles' ); 
add_action('admin_print_scripts', 'st_load_custom_scripts');
register_activation_hook( __FILE__, 'install_ShareThis');
register_uninstall_hook( __FILE__, 'uninstall_ShareThis');
?>