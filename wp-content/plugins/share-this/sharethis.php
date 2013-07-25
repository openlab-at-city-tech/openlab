<?php

// ShareThis
//
// Copyright (c) 2010 ShareThis, Inc.
// http://sharethis.com
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
 Plugin URI: http://sharethis.com
 Description: Let your visitors share a post/page with others. Supports e-mail and posting to social bookmarking sites. <a href="options-general.php?page=sharethis.php">Configuration options are here</a>. Questions on configuration, etc.? Make sure to read the README.
 Version: 7.0.3
 Author: <a href="http://www.sharethis.com">Kalpak Shah@ShareThis</a>
 Author URI: http://sharethis.com
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

	if (get_option('st_add_to_content') == '') {
		update_option('st_add_to_content', 'yes');
	}
	if (get_option('st_add_to_page') == '') {
		update_option('st_add_to_page', 'yes');
	}
}

function uninstall_ShareThis()
{
	$st_options = array('st_add_to_content','st_add_to_page','st_current_type',
						'st_prompt','st_pubid','st_sent','st_services',
						'st_tags','st_upgrade_five','st_version','st_widget','st_username','st_pulldownlogo');
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

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	install_ShareThis();
}

function st_widget_head() {
	$widget = get_option('st_widget');
	if ($widget == '') {
	}
	else{
		//$widget = st_widget_add_wp_version($widget);
		$widget = st_widget_fix_domain($widget);
		$widget = preg_replace("/\&/", "&amp;", $widget);
	}

	print($widget);
}

function sendWelcomeEmail($newUser){
	$to=get_option('admin_email');
	$updatePage=get_option('siteurl');
	$updatePage.="/wp-admin/options-general.php?page=sharethis.php";

	$body = "The ShareThis plugin on your website has been activated on ".get_option('siteurl')."\n\n"
	."If you would like to customize the look of your widget, go to the ShareThis Options page in your WordPress administration area. $updatePage\n\n" 
	."Get more information on customization options at http://help.sharethis.com/integration/wordpress." 
	."To get reporting on share data login to your account at http://sharethis.com/account and choose options in the Analytics section\n\n"
    ."If you have any additional questions or need help please email us at support@sharethis.com\n\n--The ShareThis Team";

	$subject = "ShareThis WordPress Plugin";

	if(empty($to)){
		return false;
	}
	if($newUser){
		$subject = "ShareThis WordPress Plugin Activation";
		$body ="Thanks for installing the ShareThis plugin on your blog.\n\n" 
		."If you would like to customize the look of your widget, go to the ShareThis Options page in your WordPress administration area. $updatePage\n\n" 
		."Get more information on customization options at http://help.sharethis.com/integration/wordpress.\n\n" 		
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
	."Get more information on customization options at http://help.sharethis.com/integration/wordpress." 
	."To get reporting on share data login to your account at http://sharethis.com/account and choose options in the Analytics section\n\n"
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

	$sharethis = '<p><a href="http://sharethis.com/item?&wp='
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
	if ((is_page() && get_option('st_add_to_page') != 'no') || (!is_page() && get_option('st_add_to_content') != 'no')) {
		if (!is_feed()) {
			return $content.'<p>'.st_makeEntries().'</p>';
		}
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

// 2006-06-02 Filters to Add Sharethis widget on content and/or link on RSS
// 2006-06-02 Expected behavior is that the feed link will show up if an option is not 'no'
if (get_option('st_add_to_content') != 'no' || get_option('st_add_to_page') != 'no') {
	add_filter('the_content', 'st_add_widget');

	// 2008-08-15 Excerpts don't play nice due to strip_tags().
	add_filter('get_the_excerpt', 'st_remove_st_add_link',9);
	add_filter('the_excerpt', 'st_add_widget');
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
				if (ak_can_update_options()) {
					if($_POST['Edit'] == ""){
						$publisher_id=$_POST['st_pkey'];
						if($_POST['st_callesi'] == "0"){
							$cns_settings = $_POST['copynshareSettings'];
						}else{
							$cns_settings = "";
						}
						//print_r ($_POST);
						//var_dump($cns_settings);
						/* updates username in database*/ 
						if($_POST['st_user_name'] != "" && $_POST['st_user_name'] != "undefined"){
							update_option('st_username', $_POST['st_user_name']);
						}
						
						//update st_version to figure out which widget to use.
						if(!empty($_POST['st_version'])) {
							update_option('st_version', $_POST['st_version']);
							if (($_POST['st_version']) == '5x') {
								$st_switchTo5x = true;
							} elseif (($_POST['st_version']) == '4x') {
								$st_switchTo5x = false;
							}
						}
						
						$widgetTemp = "<script charset=\"utf-8\" type=\"text/javascript\">var switchTo5x={$st_switchTo5x};</script>";
						
						$widgetTemp.="<script charset=\"utf-8\" type=\"text/javascript\" src=\"http://w.sharethis.com/button/buttons.js\"></script>";
						
						$widgetTemp.="<script charset=\"utf-8\" type=\"text/javascript\">stLight.options({publisher:\"$publisher_id\" $cns_settings});var st_type='wordpress".trim(get_bloginfo('version'))."';</script>";
						
						if($_POST['selectedBar'] == "hoverbarStyle" || $_POST['selectedBar'] == "pulldownStyle" || $_POST['sharenowSelected'] == "true"){
							$widgetTemp.="<script charset=\"utf-8\" type=\"text/javascript\" src=\"http://s.sharethis.com/loader.js\"></script>";
						}
						
						if($_POST['selectedBar'] == "hoverbarStyle"){
							$st_hoverbar_services = $_POST['hoverbar']['services'];
							$st_hoverbar_services = '"'.str_replace(',','","',$st_hoverbar_services).'"';									
							
							$widgetTemp.="<script charset=\"utf-8\" type=\"text/javascript\">var options={ \"publisher\":\"".$publisher_id."\", \"position\": \"".$_POST['hoverbar']['position']."\", \"chicklets_params\": {\"twitter\":{\"st_via\":\"".$_POST['twitter']['via']."\" }, \"instagram\" :{\"st_username\":\"".$_POST['instagram']['username']."\" } }, \"chicklets\": { \"items\": [".$st_hoverbar_services."] } }; var st_hover_widget = new sharethis.widgets.hoverbuttons(options);</script>";
							
						}else if($_POST['selectedBar'] == "pulldownStyle"){
							$st_pulldown_services = $_POST['pulldownbar']['services'];
							$st_pulldown_services = '"'.str_replace(',','","',$st_pulldown_services).'"';
				
							$widgetTemp.="<script charset=\"utf-8\" type=\"text/javascript\">var options={ \"publisher\": \"".$publisher_id."\", \"scrollpx\": ".$_POST['pulldownbar']['scrollpx'].", \"ad\": { \"visible\": false}, \"chicklets\": { \"items\": [".$st_pulldown_services."]}};var st_pulldown_widget = new sharethis.widgets.pulldownbar(options); </script>";
							update_option('st_pulldownlogo', $_POST['pulldownbar']['logo']);
						}
						
						if($_POST['sharenowSelected'] == "true"){
							$widgetTemp.="<script charset=\"utf-8\" type=\"text/javascript\">var options={ \"service\": \"facebook\", \"timer\": { \"countdown\": 30, \"interval\": 10, \"enable\": false}, \"frictionlessShare\": false, \"style\": \"".$_POST['sharenow']['theme']."\", publisher:\"".$publisher_id."\"};var st_service_widget = new sharethis.widgets.serviceWidget(options);</script>";
						}
						
						// note: do not convert & to &amp; or append WP version here
						$widget = st_widget_fix_domain($widgetTemp);
						update_option('st_pubid', $publisher_id);
						update_option('st_widget', $widget);
						
						if(!empty($_POST['st_tags'])){
							$tagsin=$_POST['st_tags'];
							$tagsin=preg_replace("/\\n|\\t/","", $tagsin);
							$tagsin=preg_replace("/\\\'/","'", $tagsin);
							$tagsin=trim($tagsin);
							update_option('st_tags',$tagsin);
						}else{
							update_option('st_tags',' '); // in case of buttons not selected
						}
						
						if(!empty($_POST['st_services'])){
							update_option('st_services', trim($_POST['st_services'],",") );
						}
							
						if(!empty($_POST['st_current_type'])){
							update_option('st_current_type', trim($_POST['st_current_type'],",") );
						}
						$options = array(
							'st_add_to_content'
							, 'st_add_to_page'
							);
							foreach ($options as $option) {
								if (isset($_POST[$option]) && in_array($_POST[$option], array('yes', 'no'))) {
									update_option($option, $_POST[$option]);
								}
							}
								
							//header('Location: '.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=sharethis.php&updated=true');
							//$blog_title = get_bloginfo('wpurl');
							//$blog_title .= "/wp-admin/options-general.php?page=sharethis.php"; 
							//header('refresh:0;url='.$blog_title);
							//die();
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
	$tags = get_option('st_tags');
	$st_current_type=get_option('st_current_type');
	$st_widget_version = get_option('st_version');
	$st_prompt = get_option('st_prompt');
	$st_username = get_option('st_username');
	$st_pulldownlogo = get_option('st_pulldownlogo');
	
	$freshInstalation = empty($services)?1:0;
	
	if(empty($st_username)){
		$st_username = "";
	}
	
	if(empty($st_pulldownlogo)){
		$st_pulldownlogo = "http://sd.sharethis.com/disc/images/Logo_Area.png";
	}
	
	if(empty($st_current_type)){
		$st_current_type="_large";
	}
	if(empty($services)){
		$services="facebook,twitter,linkedin,email,sharethis,fblike,plusone,pinterest";
	}
	if(empty($st_prompt)){
		//$services.=",instagram";
		update_option('st_prompt', 'true');
	}
	if(empty($tags)){
		foreach(explode(',',$services) as $svc){
			$tags.="<span class='st_".$svc."_large' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='".$svc."'></span>";
		}
	}
	if(empty($st_widget_version)){
		$st_widget_version="5x";
	}
	
	/* Retrives widget version from the database */ 
	$widget5xSelected = "";
	$widget4xSelected = "";
	if($st_widget_version == "5x"){
		$widget5xSelected = "selected";
	}else if($st_widget_version == "4x"){
		$widget4xSelected = "selected";
	}

	if(get_option('st_add_to_content') != 'no'){
		$st_add_to_contentYes = ' selected="selected" ';
		$st_add_to_contentNo = "";
	}else{
		$st_add_to_contentYes = "";
		$st_add_to_contentNo = ' selected="selected" ';
	}
	
	if(get_option('st_add_to_page') != 'no') {
		$st_add_to_pageYes = ' selected="selected" ';
		$st_add_to_pageNo = "";
	}else{
		$st_add_to_pageYes = "";
		$st_add_to_pageNo = ' selected="selected" ';
	}
	$widgetTag = get_option('st_widget');
	
	if(empty($publisher_id)){
		$toShow="";
		// Re-generate new random publisher key	
		$publisher_id=trim(makePkey());
	}
	else{
		$toShow = $widgetTag;
	}	
	
	/* Pulls the theme ID for the sharenow feature*/
	if (preg_match('/serviceWidget/',$toShow)) {
            $pattern = "/<script(.*?)<\/script>/";
            preg_match_all($pattern, $toShow, $matches);
            foreach($matches[1] as $k=>$v)
            {
                  if (preg_match('/serviceWidget/',$v)) {
                        preg_match("/style(.*):[\s\"\']{0,}(\d)[\s\"\']{0,}/", $v, $matches);
                        $sharenow_style = $matches[2];
                        break;
                  }
            }
      }

	/* Pulls the scrollpx value for the  pull down bar  */
	if (preg_match('/pulldownbar/',$toShow)) {
            $pattern = "/<script(.*?)<\/script>/";
            preg_match_all($pattern, $toShow, $matches);
            foreach($matches[1] as $k=>$v)
            {
                  if (preg_match('/pulldownbar/',$v)) {
                        preg_match("/scrollpx(.*):[\s\"\']{0,}(\d+)[\s\"\']{0,}/", $v, $matches);
                        $pulldown_scrollpx = $matches[2];
                        break;
                  }
            }
      }
	  
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
		var st_button_state = 1;</script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
		<script type="text/javascript" src="http://w.sharethis.com/dynamic/stlib/allServices.js"></script>
		<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
		<script type="text/javascript" src="http://s.sharethis.com/loader.js"></script>
		<script type="text/javascript" src="http://sharethis.com/js/new/json2.js"></script>
		<script type="text/javascript" src="http://sharethis.com/js/new/jquery.autocomplete.js"></script>
		<script type="text/javascript" src="http://sharethis.com/js/new/jquery.colorbox.js"></script>
		<script type="text/javascript" src="http://sharethis.com/js/new/get-buttons-new.js"></script>
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
				<div id="showLoadingStatus" class="wp_st_showLoadingStatus">Loading...</div>
				<div id="wp_st_outerContainer" style="width:1000px;">
				<div id="st_title" style="width: 100%; height: 38px;">
					<div class="wp_st_header_title">
						<label>Welcome to ShareThis for WordPress</label>
					</div>	
					<div class="wp_st_userinfo">
						<div id="usernameContainer" style="display:none">You are logged in as : <span id="login_name"></span></div>
						<div id="pbukeyContainer" style="display:none">Your publisher key : <span id="login_key"></span></div>
					</div> 
				</div> 
				<form id="ak_sharethis" name="ak_sharethis" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post" >
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
												<div id="preview" style="margin-top:30px;"></div>
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
														<div>Chicklet Size :</div>
														<div><input type="radio" name="selectSize_type" value="16x16"/>  Small</div>
														<div><input checked="true" type="radio" name="selectSize_type" value="32x32"/>  Large</div>
													</div>
												</li>
											</ul>	
											
										</div>	
											<div class="wp_st_vseparator" style="height:500px; margin-top: -31px">
												<hr/>
											</div>
											
										<div class="wp_st_widget4x">	
											<ul class="bars wp_st_show" style="padding-left:80px">
												<li class="wp_st_styleLink jqBarStyle hoverbarStyle" id="hoverbarStyle"><div class="wp_st_hoverState2 hoverbarStyle"></div><div class="wp_st_hoverState hoverbarStyle">This bar can float either on the left side or the right side of the page to provide an always-visible view of the sharing tools.</div><img id="hoverBarImage" src="'.$plugin_location.'images/HOVER_Buttons.png" class="wp_st_hoverbarStyleButtonImg"/><img id="hoverbarLoadingImg" src="'.$plugin_location.'images/loading.gif" class="wp_st_loadingImage" style="display:none"/></li>
												<li class="wp_st_styleLink jqBarStyle pulldownStyle" id="pulldownStyle"><div class="wp_st_hoverState2 pulldownStyle"></div><div class="wp_st_hoverState pulldownStyle">This bar with sharing buttons is placed at the top of page, but appears only when the reader scrolls down.</div><img id="pullDownBarImage" src="'.$plugin_location.'images/PULLDOWN.png" class="wp_st_pulldownStyleButtonImg"/><img id="pulldownLoadingImg" src="'.$plugin_location.'images/loading.gif" class="wp_st_loadingImage" style="display:none"/></li>
												<li class="wp_st_styleLink jqShareNow fbStyle" id="fbStyle"><div class="wp_st_hoverState2 fbStyle"></div><div class="wp_st_hoverState fbStyle">ShareNow allows any publisher to leverage Facebook frictionless sharing without having to create their own solution.</div><img id="shareNowImage" src="'.$plugin_location.'images/ShareNow_Button.png" class="wp_st_sharebarStyleButtonImg"/><img id="sharenowLoadingImg" src="'.$plugin_location.'images/loading.gif" class="wp_st_loadingImage" style="display:none"/></li>
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
												
												<li class="wp_st_shareNowCustomization wp_st_inputBoxLI" style="border:0px" >
													<span id="st_customize_sharenow" style="display:none;position:relative;top:5px;">&nbsp;&nbsp;Customize it!</span>
												</li>
												
											</ul>		
										</div>	
											<div style="clear:both;" class="bars wp_st_show"></div>
										</div>
								</div>
								
								<div id="wp_st_slidingContainer" style="display:none;"> 
									 <h3 style="margin-left:5px">Customize ShareNow:</h3>
									 <ul id="themeList" class="wp_st_subOptions">
										<li data-value="3" class="wp_st_sharenowImg" id="st_sharenowImg3">
											<a><img class="widgetIconSelected" id="opt_theme3" src="'.$plugin_location.'images/fbtheme_3.png"/></a>
										</li>
										<li data-value="4" class="wp_st_sharenowImg" id="st_sharenowImg4">
											<a><img class="widgetIconSelected" id="opt_theme4" src="'.$plugin_location.'images/fbtheme_4.png"/></a>
										</li>
										<li data-value="5" class="wp_st_sharenowImg" id="st_sharenowImg5">
											<a><img class="widgetIconSelected" id="opt_theme5" src="'.$plugin_location.'images/fbtheme_5.png"/></a>
										</li>
										<li data-value="6" class="wp_st_sharenowImg" id="st_sharenowImg6">
											<a><img class="widgetIconSelected" id="opt_theme6" src="'.$plugin_location.'images/fbtheme_6.png"/></a>
										</li>
										<li data-value="7" class="wp_st_sharenowImg" id="st_sharenowImg7">
											<a><img class="widgetIconSelected" id="opt_theme7" src="'.$plugin_location.'images/fbtheme_7.png"/></a>
										</li>
									</ul>
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
										</div></li>
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
									<div style="height:250px;text-align: center;">
										<div id="st_widget5x" class="wp_st_widget5x">
											<div style="width:48%; float:left">
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
								<div style="height:175px;text-align: center;">
									<div id="" class="wp_st_widget5x">
										<div class="wp_st_copynshare_heading">
											<span id="wp_st_copynshare">Enable CopyNShare</span>
										</div>
										<div>
											<div class="wp_st_copynshare_image">
												<img src="'.$plugin_location.'images/copynshare.jpg"/>
											</div>
											<div class="wp_st_copynshare_text">
												<p class="">CopyNShare is the new ShareThis widget feature that enables you to track the shares that occur when a user copies and pastes your websites URL or content</p>
											</div>
											<div id="st_cns_settings" class="wp_st_copynshare_checkboxes">
												<input type="checkbox" class="cnsCheck wp_st_defaultCursor" id="donotcopy" name="donotcopy" value="true" ></input>
												<label for="donotcopy" class="cnsCheck wp_st_defaultCursor" id="wp_st_donotcopy_label">&nbsp;Measure copy and shares of your website\'s content</label>
												<br />
												<br />
												<input type="checkbox" class="cnsCheck wp_st_defaultCursor" id="hashaddress" name="hashaddress" value="false" ></input>
												<label for="hashaddress" class="cnsCheck wp_st_defaultCursor" id="wp_st_hashaddress_label">&nbsp;Measure copy and shares of your website\'s URLs</label>
											</div>
									  </div>
								</div>
							 </div>
							 
							<div style="height:175px;text-align: center;">
									<div>
										<div class="wp_st_customizewidget_heading">
											<span>Customize Widget Position</span>
										</div>
										<div class="wp_st_customizewidget_options">
											<div style="margin-top: 5px"> 
												<span style="cursor:auto;">Automatically add ShareThis to your posts?</span>
												<span style="margin-left: 10px"><select name="st_add_to_content" id="st_add_to_content">
													<option value="yes"'.$st_add_to_contentYes.'>Yes</option>
													<option value="no"'.$st_add_to_contentNo.'>No</option>
												</select></span>
											</div>
											<div style="margin-top: 7px">
												<span style="cursor:auto;position: relative; left: 2px;">Automatically add ShareThis to your pages?</span>
												<span style="margin-left: 10px;position:relative;left:-2px;"><select name="st_add_to_page" id="st_add_to_page">
													<option value="yes"'.$st_add_to_pageYes.'>Yes</option>
													<option value="no"'.$st_add_to_pageNo.'>No</option>
												</select></span>
											</div>
									  </div>
								</div>
							 </div>
						</div> 
							
							<!-- STEP 5 -->
							<div id="st_step5" class="wp_st_centerContainer2" style="display:none;">
								<div id="loginWindowDiv" class="wp_st_loginWindowDiv">
									<iframe id="loginFrame" width="644px" height="398px" frameborder="0" src="http://sharethis.com/external-login?pluginType=newPlugins"></iframe>
									<div class="wp_st_login_message">You are successfully logged-in with ShareThis.</div>		
								</div>
							</div>
						
							<!-- STEP 6 -->
							<div id="st_step6" class="wp_st_centerContainer2" style="display:none;">
								<div id="st_additional_options" class="wp_st_additional_options">
								
								</div>	
							</div>
							
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
						<div><input type="submit" onclick="st_log();" id="wp_st_savebutton" value="SAVE"  name="submit_button" value="'.__('Update ShareThis Options', 'sharethis').'" style="display:none;"/>
						</div>
						
						<script src="'.$plugin_location.'js/sharethis.js" type="text/javascript"></script>
					</fieldset>

					<input type="hidden" id="is_hoverbar_selected" value=""/>
					<input type="hidden" id="is_sharenow_selected" value=""/>
					<input type="hidden" id="is_copynshre_selected" value=""/>
					
					<input type="hidden" name="st_action" value="st_update_settings" />
					<input type="hidden" name="st_version" id="st_version" value="'.$st_widget_version.'"/>
					<input type="hidden" name="st_services" id="st_services" value="'.$services.'"/>
					<input type="hidden" name="st_current_type" id="st_current_type" value="'.$st_current_type.'"/>
					<input type="hidden" name="st_widget" id="st_widget" value="'.htmlspecialchars($toShow).'"/>
					<input type="hidden" name="st_tags" id="st_tags" value="'.htmlspecialchars($tags).'"/>
					<input type="hidden" name="st_pkey" id="st_pkey" value="'.htmlspecialchars($publisher_id).'"/>
					<input type="hidden" name="st_user_name" id="st_user_name" value="'.$st_username.'"/> 
					
					<input type="hidden" name="selectedBar" id="st_selected_bar" value=""/>
					<input type="hidden" name="hoverbar[position]" id="st_hoverbar_position" value=""/>
					<input type="hidden" name="hoverbar[services]" id="st_hoverbar_services" value=""/>
					
					<input type="hidden" name="pulldownbar[scrollpx]" id="st_pulldownbar_scrollpx" value="'.$pulldown_scrollpx.'"/>
					<input type="hidden" name="pulldownbar[logo]" id="st_pulldownbar_logo" value="'.$st_pulldownlogo.'"/>
					<input type="hidden" name="pulldownbar[services]" id="st_pulldownbar_services" value=""/>
					
					<input type="hidden" name="sharenowSelected" id="st_sharenow_selected" value="false"/>
					<input type="hidden" name="sharenow[theme]" id="st_sharenow_theme" value="'.$sharenow_style.'"/>
					
					<input type="hidden" name="copynshareSettings" id="copynshareSettings" value=""/>
					<input type="hidden" name="st_callesi" id="st_callesi" value="'.$sharethis_callesi.'" />
					<input type="hidden" id="freshInstalation" value="'.$freshInstalation.'"/>
				</form>
			</div>
		</div>	
	');
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
		}";
	echo "<style type='text/css'>";
	echo $custom_css;
	echo "\n</style>\n";
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
register_uninstall_hook( __FILE__, 'uninstall_ShareThis');
?>