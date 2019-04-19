<?php
/*  FV Wordpress Flowplayer - HTML5 video player with Flash fallback    
    Copyright (C) 2013  Foliovision

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 

add_action('wp_footer','flowplayer_prepare_scripts',9);
add_action('wp_footer','flowplayer_display_scripts',100);          
add_action('widget_text','do_shortcode');

add_filter( 'run_ngg_resource_manager', '__return_false' );


function fv_flowplayer_remove_bad_scripts() {  
  global $wp_scripts;
  if( isset($wp_scripts->registered['flowplayer']) && isset($wp_scripts->registered['flowplayer']->src) && stripos($wp_scripts->registered['flowplayer']->src, 'fv-wordpress-flowplayer') === false ) {
    wp_deregister_script( 'flowplayer' );
  }
}
add_action( 'wp_print_scripts', 'fv_flowplayer_remove_bad_scripts', 100 );

add_filter( 'run_ngg_resource_manager', '__return_false' ); //  Nextgen Gallery compatibility fix

function fv_flowplayer_ap_action_init(){
  // Localization
  load_plugin_textdomain('fv-wordpress-flowplayer', false, dirname(dirname(plugin_basename(__FILE__))) . "/languages");
}
add_action('init', 'fv_flowplayer_ap_action_init');

function fv_flowplayer_get_js_translations() {
  
  $sWhy = __(' <a target="_blank" href="https://foliovision.com/2017/05/issues-with-vimeo-on-android">Why?</a>','fv-wordpress-flowplayer');
  
  $aStrings = array(
  0 => '',
  1 => __('Video loading aborted', 'fv-wordpress-flowplayer'),
  2 => __('Network error', 'fv-wordpress-flowplayer'),
  3 => __('Video not properly encoded', 'fv-wordpress-flowplayer'),
  4 => __('Video file not found', 'fv-wordpress-flowplayer'),
  5 => __('Unsupported video', 'fv-wordpress-flowplayer'),
  6 => __('Skin not found', 'fv-wordpress-flowplayer'),
  7 => __('SWF file not found', 'fv-wordpress-flowplayer'),
  8 => __('Subtitles not found', 'fv-wordpress-flowplayer'),
  9 => __('Invalid RTMP URL', 'fv-wordpress-flowplayer'),
  10 => __('Unsupported video format. Try installing Adobe Flash.', 'fv-wordpress-flowplayer'),  
  11 => __('Click to watch the video', 'fv-wordpress-flowplayer'),
  12 => __('[This post contains video, click to play]', 'fv-wordpress-flowplayer'),
  'video_expired' => __('<h2>Video file expired.<br />Please reload the page and play it again.</h2>', 'fv-wordpress-flowplayer'),
  'unsupported_format' => __('<h2>Unsupported video format.<br />Please use a Flash compatible device.</h2>','fv-wordpress-flowplayer'),
  'mobile_browser_detected_1' => __('Mobile browser detected, serving low bandwidth video.','fv-wordpress-flowplayer'),
  'mobile_browser_detected_2' => __('Click here','fv-wordpress-flowplayer'),
  'mobile_browser_detected_3' => __('for full quality.','fv-wordpress-flowplayer'),
  'live_stream_failed' => __('<h2>Live stream load failed.</h2><h3>Please try again later, perhaps the stream is currently offline.</h3>','fv-wordpress-flowplayer'),
  'live_stream_failed_2' => __('<h2>Live stream load failed.</h2><h3>Please try again later, perhaps the stream is currently offline.</h3>','fv-wordpress-flowplayer'),
  'what_is_wrong' => __('Please tell us what is wrong :','fv-wordpress-flowplayer'),
  'full_sentence' => __('Please give us more information (a full sentence) so we can help you better','fv-wordpress-flowplayer'),
  'error_JSON' =>__('Admin: Error parsing JSON','fv-wordpress-flowplayer'),
  'no_support_IE9' =>__('Admin: Video checker doesn\'t support IE 9.','fv-wordpress-flowplayer'),
  'check_failed' =>__('Admin: Check failed.','fv-wordpress-flowplayer'),
  'playlist_current' =>__('Now Playing','fv-wordpress-flowplayer'),
  'video_issues' =>__('Video Issues','fv-wordpress-flowplayer'),
  'video_reload' =>__('Video loading has stalled, click to reload','fv-wordpress-flowplayer'),
  'link_copied' =>__('Video Link Copied to Clipboard','fv-wordpress-flowplayer'),
  'embed_copied' =>__('Embed Code Copied to Clipboard','fv-wordpress-flowplayer'),
  'subtitles_disabled' =>__('Subtitles disabled','fv-wordpress-flowplayer'),
  'subtitles_switched' =>__('Subtitles switched to ','fv-wordpress-flowplayer'),
  'warning_iphone_subs' => __('This video has subtitles, that are not supported on your device.','fv-wordpress-flowplayer'),
  'warning_unstable_android' => __('You are using an old Android device. If you experience issues with the video please use <a href="https://play.google.com/store/apps/details?id=org.mozilla.firefox">Firefox</a>.','fv-wordpress-flowplayer').$sWhy,
  'warning_samsungbrowser' => __('You are using the Samsung Browser which is an older and buggy version of Google Chrome. If you experience issues with the video please use <a href="https://www.mozilla.org/en-US/firefox/new/">Firefox</a> or other modern browser.','fv-wordpress-flowplayer'),
  'warning_old_safari' => __('You are using an old Safari browser. If you experience issues with the video please use <a href="https://www.mozilla.org/en-US/firefox/new/">Firefox</a> or other modern browser.','fv-wordpress-flowplayer').$sWhy,  
  );
  
  return $aStrings;
} 

/**
 * Replaces the flowplayer tags in post content by players and fills the $GLOBALS['fv_fp_scripts'] array.
 * @param string Content to be parsed
 * @return string Modified content string
 */
function flowplayer_content( $content ) {
  global $fv_fp;

  $content_matches = array();
  preg_match_all('/\[(flowplayer|fvplayer)\ [^\]]+\]/i', $content, $content_matches);
  
  // process all found tags
  foreach ($content_matches[0] as $tag) {
    $ntag = str_replace("\'",'&#039;',$tag);
    //search for URL
    preg_match("/src='([^']*?)'/i",$ntag,$tmp);
    if( $tmp[1] == NULL ) {
      preg_match_all("/src=([^,\s\]]*)/i",$ntag,$tmp);
      $media = $tmp[1][0];
    }
    else
      $media = $tmp[1]; 
    
    //strip the additional /videos/ from the beginning if present  
    preg_match('/(.*)\/videos\/(.*)/',$media,$matches);
    if ($matches[0] == NULL)
      $media = $media;
    else if ($matches[1] == NULL) {
      $media = $matches[2];
    }
    else {
      $media = $matches[2];
    }
    
    unset($arguments['src']);
    unset($arguments['src1']);
    unset($arguments['src2']);        
    unset($arguments['width']);
    unset($arguments['height']);
    unset($arguments['autoplay']);
    unset($arguments['splash']);
    unset($arguments['splashend']);
    unset($arguments['popup']);
    unset($arguments['controlbar']);
    unset($arguments['redirect']);
    unset($arguments['loop']);
    
    //width and heigth
    preg_match("/width=(\d*)/i",$ntag,$width);
    preg_match("/height=(\d*)/i",$ntag,$height);
    if( $width[1] != NULL)
      $arguments['width'] = $width[1];
    if( $height[1] != NULL)
      $arguments['height'] = $height[1];
      
    //search for redirect
    preg_match("/redirect='([^']*?)'/i",$ntag,$tmp);
    if ($tmp[1])
      $arguments['redirect'] = $tmp[1];
    
    //search for autoplay
    preg_match("/[\s]+autoplay([\s]|])+/i",$ntag,$tmp);
    if (isset($tmp[0])){
      $arguments['autoplay'] = true;
    }
    else {
      preg_match("/autoplay='([A-Za-z]*)'/i",$ntag,$tmp);
      if ( $tmp[1] == NULL )
        preg_match("/autoplay=([A-Za-z]*)/i",$ntag,$tmp);
      if (isset($tmp[1]))
        $arguments['autoplay'] = $tmp[1];
    }
    
    //search for popup in quotes
    preg_match("/popup='([^']*?)'/i",$ntag,$tmp);
    if ($tmp[1])
      $arguments['popup'] = $tmp[1];
    
    //search for loop
    preg_match("/[\s]+loop([\s]|])+/i",$ntag,$tmp);
    if (isset($tmp[0])){
      $arguments['loop'] = true;
    }
    else {
      preg_match("/loop='([A-Za-z]*)'/i",$ntag,$tmp);
      if ( $tmp[1] == NULL )
        preg_match("/loop=([A-Za-z]*)/i",$ntag,$tmp);
      if (isset($tmp[1]))
        $arguments['loop'] = $tmp[1];
    }
    
    //  search for splash image
    preg_match("/splash='([^']*?)'/i",$ntag,$tmp);   //quotes version
     if( $tmp[1] == NULL ) {
      preg_match_all("/splash=([^,\s\]]*)/i",$ntag,$tmp);  //non quotes version
      preg_match('/(.*)\/videos\/(.*)/i',$tmp[1][0],$matches);
       if ($matches[0] == NULL)
        $arguments['splash'] = $tmp[1][0];
       else if ($matches[1] == NULL) {
        $arguments['splash'] = $matches[2];//$tmp[1][0];
      }
       else {
        $arguments['splash'] = $matches[2];
      }
    }
    else {
      preg_match('/(.*)\/videos\/(.*)/',$tmp[1],$matches);
      if ($matches[0] == NULL)
        $arguments['splash'] = $tmp[1];
      elseif ($matches[1] == NULL)
        $arguments['splash'] = $matches[2];
      else
        $arguments['splash'] = $matches[2];//$tmp[1];
    }
    
    //  search for src1
    preg_match("/src1='([^']*?)'/i",$ntag,$tmp);   //quotes version
     if( $tmp[1] == NULL ) {
      preg_match_all("/src1=([^,\s\]]*)/i",$ntag,$tmp);  //non quotes version
      preg_match('/(.*)\/videos\/(.*)/i',$tmp[1][0],$matches);
       if ($matches[0] == NULL)
        $arguments['src1'] = $tmp[1][0];
       else if ($matches[1] == NULL) {
        $arguments['src1'] = $matches[2];//$tmp[1][0];
      }
       else {
        $arguments['src1'] = $matches[2];
      }
    }
    else {
      preg_match('/(.*)\/videos\/(.*)/',$tmp[1],$matches);
      if ($matches[0] == NULL)
        $arguments['src1'] = $tmp[1];
      elseif ($matches[1] == NULL)
        $arguments['src1'] = $matches[2];
      else
        $arguments['src1'] = $matches[2];//$tmp[1];
    }
    
    //  search for src1
    preg_match("/src2='([^']*?)'/i",$ntag,$tmp);   //quotes version
     if( $tmp[1] == NULL ) {
      preg_match_all("/src2=([^,\s\]]*)/i",$ntag,$tmp);  //non quotes version
      preg_match('/(.*)\/videos\/(.*)/i',$tmp[1][0],$matches);
       if ($matches[0] == NULL)
        $arguments['src2'] = $tmp[1][0];
       else if ($matches[1] == NULL) {
        $arguments['src2'] = $matches[2];//$tmp[1][0];
      }
       else {
        $arguments['src2'] = $matches[2];
      }
    }
    else {
      preg_match('/(.*)\/videos\/(.*)/',$tmp[1],$matches);
      if ($matches[0] == NULL)
        $arguments['src2'] = $tmp[1];
      elseif ($matches[1] == NULL)
        $arguments['src2'] = $matches[2];
      else
        $arguments['src2'] = $matches[2];//$tmp[1];
    }
    
    //search for splashend
    preg_match("/[\s]+splashend([\s]|])+/i",$ntag,$tmp);
    if (isset($tmp[0])){
      $arguments['splashend'] = true;
    }
    else {
      preg_match("/splashend='([A-Za-z]*)'/i",$ntag,$tmp);
      if ( $tmp[1] == NULL )
        preg_match("/splashend=([A-Za-z]*)/i",$ntag,$tmp);
      if (isset($tmp[1]))
        $arguments['splashend'] = $tmp[1];
    }
    
    //search for controlbar
    preg_match("/[\s]+controlbar([\s]|])+/i",$ntag,$tmp);
    if (isset($tmp[0])){
      $arguments['controlbar'] = true;
    }
    else {
      preg_match("/controlbar='([A-Za-z]*)'/i",$ntag,$tmp);
      if ( $tmp[1] == NULL )
        preg_match("/controlbar=([A-Za-z]*)/i",$ntag,$tmp);
      if (isset($tmp[1]))
        $arguments['controlbar'] = $tmp[1];
    }
    
    if (trim($media) != '') {
      // build new player
      $new_player = $fv_fp->build_min_player($media,$arguments);
      $content = str_replace($tag, $new_player['html'],$content);
      if (!empty($new_player['script'])) {
        $GLOBALS['fv_fp_scripts'] = $new_player['script'];
      }
    }
  }
  return $content;
}

function flowplayer_prepare_scripts() {
  global $fv_fp, $fv_wp_flowplayer_ver;
  
  //  don't load script in Optimize Press 2 preview
  if( flowplayer::is_special_editor() ) {
    return;  
  }

  if(
     isset($GLOBALS['fv_fp_scripts']) ||
     $fv_fp->_get_option('js-everywhere')  ||
     isset($_GET['fv_wp_flowplayer_check_template'])
  ){
    
    $aDependencies = array('jquery');
    if( $fv_fp->_get_option('js-everywhere') || $fv_fp->load_tabs ) {
      wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), $fv_wp_flowplayer_ver, true);
      $aDependencies[] = 'jquery-ui-tabs';
    }
    
    if( !$fv_fp->bCSSLoaded ) $fv_fp->css_enqueue(true);
    
    wp_enqueue_script( 'flowplayer', flowplayer::get_plugin_url().'/flowplayer/fv-flowplayer.min.js', $aDependencies, $fv_wp_flowplayer_ver, true );

    $sPluginUrl = preg_replace( '~^.*://~', '//', FV_FP_RELATIVE_PATH );
  
    $sCommercialKey = $fv_fp->_get_option('key') ? $fv_fp->_get_option('key') : '';
    $sLogo = $sCommercialKey && $fv_fp->_get_option('logo') ? $fv_fp->_get_option('logo') : '';
    
    $aConf = array( 'fullscreen' => true, 'swf' => $sPluginUrl.'/flowplayer/flowplayer.swf?ver='.$fv_wp_flowplayer_ver, 'swfHls' => $sPluginUrl.'/flowplayer/flowplayerhls.swf?ver='.$fv_wp_flowplayer_ver );
    
    if( $fv_fp->_get_option('rtmp-live-buffer') ) {
      $aConf['bufferTime'] = apply_filters( 'fv_player_rtmp_bufferTime', 3 );
    }

    if( $fv_fp->_get_option( array( 'integrations', 'embed_iframe') ) ) {
      $aConf['embed'] = false;
    } else {
      $aConf['embed'] = array( 'library' => $sPluginUrl.'/flowplayer/fv-flowplayer.min.js', 'script' => $sPluginUrl.'/flowplayer/embed.min.js', 'skin' => $sPluginUrl.'/css/flowplayer.css', 'swf' => $sPluginUrl.'/flowplayer/flowplayer.swf?ver='.$fv_wp_flowplayer_ver, 'swfHls' => $sPluginUrl.'/flowplayer/flowplayerhls.swf?ver='.$fv_wp_flowplayer_ver );
    }
   
    if( $fv_fp->_get_option('ui_speed_increment') == 0.25){
      $aConf['speeds'] = array( 0.25,0.5,0.75,1,1.25,1.5,1.75,2 );
    }elseif( $fv_fp->_get_option('ui_speed_increment') == 0.1){
      $aConf['speeds'] = array( 0.25,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1,1.1,1.2,1.3,1.4,1.5,1.6,1.7,1.8,1.9,2 );
    }elseif( $fv_fp->_get_option('ui_speed_increment') == 0.5){
      $aConf['speeds'] = array( 0.25,0.5,1,1.5,2 );
    }

    $aConf['video_hash_links'] = empty($fv_fp->aCurArgs['linking']) ? !$fv_fp->_get_option('disable_video_hash_links' ) : $fv_fp->aCurArgs['linking'] === 'true';
    
    if( $sCommercialKey ) $aConf['key'] = $sCommercialKey;
    if( apply_filters( 'fv_flowplayer_safety_resize', true) && !$fv_fp->_get_option('fixed_size') ) {
      $aConf['safety_resize'] = true;
    }
    if( $fv_fp->_get_option('cbox_compatibility') ) {
      $aConf['cbox_compatibility'] = true;
    }    
    if( current_user_can('manage_options') && !$fv_fp->_get_option('disable_videochecker') ) {
      $aConf['video_checker_site'] = home_url();
    }
    if( $sLogo ) $aConf['logo'] = $sLogo;
    $aConf['volume'] = floatval( $fv_fp->_get_option('volume') );
    if( $aConf['volume'] > 1 ) {
      $aConf['volume'] = 1;
    }
    
    if( $val = $fv_fp->_get_option('mobile_native_fullscreen') ) $aConf['mobile_native_fullscreen'] = $val;
    if( $val = $fv_fp->_get_option('mobile_force_fullscreen') ) $aConf['mobile_force_fullscreen'] = $val;
    if( $val = $fv_fp->_get_option('mobile_alternative_fullscreen') ) $aConf['mobile_alternative_fullscreen'] = $val;

    if ( $fv_fp->_get_option('video_position_save_enable') ) {
      $aConf['video_position_save_enable'] = $fv_fp->_get_option('video_position_save_enable');
    }

    if( is_user_logged_in() ) $aConf['is_logged_in'] = true;

    $aConf['sticky_video'] = $fv_fp->_get_option('sticky_video');
    $aConf['sticky_place'] = $fv_fp->_get_option('sticky_place');
    $aConf['sticky_width'] = $fv_fp->_get_option('sticky_width');
       
    global $post;
    if( $post && isset($post->ID) && $post->ID > 0 ) {
      if( get_post_meta($post->ID, 'fv_player_mobile_native_fullscreen', true) ) $aConf['mobile_native_fullscreen'] = true;
      if( get_post_meta($post->ID, 'fv_player_mobile_force_fullscreen', true) ) $aConf['mobile_force_fullscreen'] = true;
    }
    
    if( ( $fv_fp->_get_option('js-everywhere') || $fv_fp->load_hlsjs ) && $fv_fp->_get_option('hlsjs') ) {
      wp_enqueue_script( 'flowplayer-hlsjs', flowplayer::get_plugin_url().'/flowplayer/hls.min.js', array('flowplayer'), $fv_wp_flowplayer_ver, true );
    }
    $aConf['script_hls_js'] = flowplayer::get_plugin_url().'/flowplayer/hls.min.js?ver='.$fv_wp_flowplayer_ver;
        
    if( $fv_fp->load_dash ) {
      wp_enqueue_script( 'flowplayer-dash', flowplayer::get_plugin_url().'/flowplayer/flowplayer.dashjs.min.js', array('flowplayer'), $fv_wp_flowplayer_ver, true );
    }
    $aConf['script_dash_js'] = flowplayer::get_plugin_url().'/flowplayer/flowplayer.dashjs.min.js?ver='.$fv_wp_flowplayer_ver;
    $aConf['script_dash_js_version'] = '2.7';
        
    if( $fv_fp->_get_option('googleanalytics') ) {
      $aConf['analytics'] = $fv_fp->_get_option('googleanalytics');
    }
    
    $aConf['hlsjs'] = array(
      'startLevel' => -1,
      'fragLoadingMaxRetry' => 3,
      'levelLoadingMaxRetry' => 3,
      'capLevelToPlayerSize' => true
    );
    
    // The above HLS.js config doesn't work well on Chrome and Firefox, so we detect that in JS and use this config for it instead. Todo: make this a per-video thing
    if( class_exists('FV_Player_Pro_DaCast') ) {
      $aConf['dacast_hlsjs'] = array(        
        'autoLevelEnabled' => false // disable ABR. If you set startLevel or capLevelToPlayerSize it will be enabled again. So this way everybody on desktop gets top quality and they have to switch to lower each time.
      );
    }
    
    $aConf = apply_filters( 'fv_flowplayer_conf', $aConf );
    
    wp_localize_script( 'flowplayer', 'fv_flowplayer_conf', $aConf );
    if( current_user_can('manage_options') ) {
      wp_localize_script( 'flowplayer', 'fv_flowplayer_admin_input', array(true) );
      wp_localize_script( 'flowplayer', 'fv_flowplayer_admin_js_test', array(true) );
    }
    if( current_user_can('edit_posts') ) {
      wp_localize_script( 'flowplayer', 'fv_flowplayer_user_edit', array(true) );     
    }
    
    wp_localize_script( 'flowplayer', 'fv_flowplayer_translations', fv_flowplayer_get_js_translations());
    wp_localize_script( 'flowplayer', 'fv_fp_ajaxurl', site_url().'/wp-admin/admin-ajax.php' );
    wp_localize_script( 'flowplayer', 'fv_flowplayer_playlists', array() );   //  has to be defined for FV Player Pro 0.6.20 and such
    
    if( count($fv_fp->aAds) > 0 ) { //  todo: move into player
      wp_localize_script( 'flowplayer', 'fv_flowplayer_ad', $fv_fp->aAds ); 
    }
    if( count($fv_fp->aPopups) > 0 ) {  //  todo: move into player
      wp_localize_script( 'flowplayer', 'fv_flowplayer_popup', $fv_fp->aPopups );
    }    

    if( isset($GLOBALS['fv_fp_scripts']) && count($GLOBALS['fv_fp_scripts']) > 0 ) {
      foreach( $GLOBALS['fv_fp_scripts'] AS $sKey => $aScripts ) {
        wp_localize_script( 'flowplayer', $sKey.'_array', $aScripts );
      }
    }
    
  }
  
  global $FV_Player_lightbox;
  if( isset($FV_Player_lightbox) && ( $FV_Player_lightbox->bLoad || $fv_fp->_get_option('lightbox_images') || $fv_fp->_get_option('js-everywhere') ) ) {
    $aConf = array();
    $aConf['lightbox_images'] = $fv_fp->_get_option('lightbox_images');
    
    if( !$FV_Player_lightbox->bCSSLoaded ) $FV_Player_lightbox->css_enqueue(true);

    wp_enqueue_script( 'fv_player_lightbox', flowplayer::get_plugin_url().'/js/fancybox.js', 'jquery', $fv_wp_flowplayer_ver, true );
    wp_localize_script( 'fv_player_lightbox', 'fv_player_lightbox', $aConf );
    
  }
  
}

/**
 * Prints flowplayer javascript content to the bottom of the page.
 */
function flowplayer_display_scripts() {
  global $fv_fp;
  if( ( $fv_fp->_get_option('ui_repeat_button') || $fv_fp->_get_option('ui_no_picture_button') ) && file_exists(dirname( __FILE__ ) . '/../css/fvp-icon-sprite.svg') ) { //  todo: only include if it's going to be used!
    include_once(dirname( __FILE__ ) . '/../css/fvp-icon-sprite.svg');
  }
  
  if( $fv_fp->_get_option('ui_rewind_button') ) { //  todo: only include if it's going to be used!
    ?>
<svg style="position: absolute; width: 0; height: 0; overflow: hidden;" class="fvp-icon" xmlns="http://www.w3.org/2000/svg">
  <g id="fvp-rewind">
    <path d="M22.7 10.9c0 1.7-0.4 3.3-1.1 4.8 -0.7 1.5-1.8 2.8-3.2 3.8 -0.4 0.3-1.3-0.9-0.9-1.2 1.2-0.9 2.1-2 2.7-3.3 0.7-1.3 1-2.7 1-4.1 0-2.6-0.9-4.7-2.7-6.5 -1.8-1.8-4-2.7-6.5-2.7 -2.5 0-4.7 0.9-6.5 2.7 -1.8 1.8-2.7 4-2.7 6.5 0 2.4 0.8 4.5 2.5 6.3 1.7 1.8 3.7 2.7 6.1 2.9l-1.2-2c-0.2-0.3 0.9-1 1.1-0.7l2.3 3.7c0.2 0.3 0 0.6-0.2 0.7L9.5 23.8c-0.3 0.2-0.9-0.9-0.5-1.2l2.1-1.1c-2.7-0.2-5-1.4-6.9-3.4 -1.9-2-2.8-4.5-2.8-7.2 0-3 1.1-5.5 3.1-7.6C6.5 1.2 9 0.2 12 0.2c3 0 5.5 1.1 7.6 3.1C21.7 5.4 22.7 7.9 22.7 10.9z"  fill="#fff"/><path d="M8.1 15.1c-0.1 0-0.1 0-0.1-0.1V8C8 7.7 7.8 7.9 7.7 7.9L6.8 8.3C6.8 8.4 6.7 8.3 6.7 8.2L6.3 7.3C6.2 7.2 6.3 7.1 6.4 7.1l2.7-1.2c0.1 0 0.4 0 0.4 0.3v8.8c0 0.1 0 0.1-0.1 0.1H8.1z" fill="#fff"/><path d="M17.7 10.6c0 2.9-1.3 4.7-3.5 4.7 -2.2 0-3.5-1.8-3.5-4.7s1.3-4.7 3.5-4.7C16.4 5.9 17.7 7.7 17.7 10.6zM12.3 10.6c0 2.1 0.7 3.4 2 3.4 1.3 0 2-1.2 2-3.4 0-2.1-0.7-3.4-2-3.4C13 7.2 12.3 8.5 12.3 10.6z" fill="#fff"/>
  </g>
</svg>
    <?php
  }
  
  if( flowplayer::is_special_editor() ) {
    return;  
  }  

  if( is_user_logged_in() || isset($_GET['fv_wp_flowplayer_check_template']) ) {
    echo "\n<!--fv-flowplayer-footer-->\n\n";
  }
}

/**
 * This is the template tag. Use the standard Flowplayer shortcodes
 */
function flowplayer($shortcode) {
  echo apply_filters('the_content',$shortcode);
}


/*
Make sure our div won't be wrapped in any P tag and that html attributes don't break the shortcode
*/
function fv_flowplayer_the_content( $c ) {
  if( flowplayer::is_special_editor() ) {
    return $c;  
  }    
  
  $c = preg_replace( '!<p[^>]*?>(\[(?:fvplayer|flowplayer).*?[^\\\]\])</p>!', "\n".'$1'."\n", $c );
  $c = preg_replace_callback( '!\[(?:fvplayer|flowplayer).*?[^\\\]\]!', 'fv_flowplayer_shortfcode_fix_attrs', $c );
  return $c;
}
add_filter( 'the_content', 'fv_flowplayer_the_content', 0 );


function fv_flowplayer_shortfcode_fix_attrs( $aMatch ) {
  $aMatch[0] = preg_replace_callback( '!(?:ad|popup)="(.*?[^\\\])"!', 'fv_flowplayer_shortfcode_fix_attr', $aMatch[0] );
  return $aMatch[0];
}


function fv_flowplayer_shortfcode_fix_attr( $aMatch ) {
  $aMatch[0] = str_replace( $aMatch[1], '<!--fv_flowplayer_base64_encoded-->'.base64_encode($aMatch[1]), $aMatch[0] );
  return $aMatch[0];
}


/*
Handle attachment pages which contain videos
*/
function fv_flowplayer_attachment_page_video( $c ) {
  global $post;
  if( stripos($post->post_mime_type, 'video/') !== 0 && stripos($post->post_mime_type, 'audio/') !== 0 ) {
    return $c;
  }
  
  if( !$src = wp_get_attachment_url($post->ID) ) {
    return $c;
  }

  $meta = get_post_meta( $post->ID, '_wp_attachment_metadata', true );
  $size = (isset($meta['width']) && isset($meta['height']) && intval($meta['width'])>0 && intval($meta['height'])>0 ) ? ' width="'.intval($meta['width']).'" height="'.intval($meta['height']).'"' : false;
  
  $shortcode = '[fvplayer src="'.$src.'"'.$size.']';
  
  $c = preg_replace( '~<p class=.attachment.[\s\S]*?</p>~', $shortcode, $c );
  $c = preg_replace( '~<div[^>]*?class="[^"]*?wp-video[^"]*?"[^>]*?>[\s\S]*?<video.*?</video></div>~', $shortcode, $c );

  return $c;
}
add_filter( 'prepend_attachment', 'fv_flowplayer_attachment_page_video' );


function fv_player_caption( $caption ) {
  global $post, $authordata;
  $sAuthorInfo = ( $authordata ) ? sprintf( '<a href="%1$s" title="%2$s" rel="author">%3$s</a>', esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ), esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ), get_the_author() ) : false;
  $caption = str_replace(
                         array(
                               '%post_title%',
                               '%post_date%',
                               '%post_author%',
                               '%post_author_name%'
                               ),
                         array(
                               get_the_title(),
                               get_the_date(),
                               $sAuthorInfo,
                               get_the_author()
                              ),
                        $caption );
  return $caption;
}
add_filter( 'fv_player_caption', 'fv_player_caption' );


add_filter( 'comment_text', 'fv_player_comment_text', 0 );
add_filter( 'bp_get_activity_content_body', 'fv_player_comment_text', 6 );
add_filter( 'bbp_get_topic_content', 'fv_player_comment_text', 0 );
add_filter( 'bbp_get_reply_content', 'fv_player_comment_text', 0 );

function fv_player_comment_text( $comment_text ) {
  if( is_admin() ) return $comment_text;
  
  global $fv_fp;
  if( isset($fv_fp->conf['parse_comments']) && $fv_fp->conf['parse_comments'] == 'true' ) {
    add_filter('comment_text', 'do_shortcode');
    add_filter('bbp_get_topic_content', 'do_shortcode', 11);
    add_filter('bbp_get_reply_content', 'do_shortcode', 11);

    if( stripos($comment_text,'youtube.com') !== false || stripos($comment_text,'youtu.be') !== false ) {
      $pattern = '#(?:<iframe[^>]*?src=[\'"])?((?:https?://|//)?' # Optional URL scheme. Either http, or https, or protocol-relative.
               . '(?:www\.|m\.)?'      #  Optional www or m subdomain.
               . '(?:'                 #  Group host alternatives:
               .   'youtu\.be/'        #    Either youtu.be,
               .   '|youtube\.com/'    #    or youtube.com
               .     '(?:'             #    Group path alternatives:
               .       'embed/'        #      Either /embed/,
               .       '|v/'           #      or /v/,
               .       '|watch\?v='    #      or /watch?v=,
               .       '|watch\?.+&v=' #      or /watch?other_param&v=
               .     ')'               #    End path alternatives.
               . ')'                   #  End host alternatives.
               . '([\w-]{11})'         # 11 characters (Length of Youtube video ids).
               . '(?![\w-]))(?:.*?</iframe>)?#';         # Rejects if overlong id.
      $comment_text = preg_replace( $pattern, '[fvplayer src="$1"]', $comment_text );
    }

    if( stripos($comment_text,'vimeo.com') !== false ) {
      $pattern = '#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[/a-z]*/)*([0-9]{6,11})[?]?.*#';
      $comment_text = preg_replace( $pattern, '[fvplayer src="https://vimeo.com/$1"]', $comment_text );
    }
  }
  
  return $comment_text;
}
