<?php

require_once( dirname(__FILE__).'/../fv-player-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_EndActionsTest extends FV_Player_UnitTestCase {
  
  public function setUp() {
    parent::setUp();

    // create a post with playlist shortcode
    $this->post_id_testEndActions= $this->factory->post->create( array(
      'post_title' => 'End Action Test',
      'post_content' => '[fvplayer src="https://cdn.site.com/video.mp4"]'
    ) );
    
    // if we don't load something with a [fvplayer] shortcode in it it won't know to load CSS in header!
    global $post;
    $post = get_post( $this->post_id_testEndActions );
    $post->ID = 1234;
    
    // we remove header stuff which we don't want to test
    remove_action('wp_head', 'wp_generator');
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    add_filter( 'wp_resource_hints', '__return_empty_array' );
    wp_deregister_script( 'wp-embed' );
    
    // the test data
    update_option('fv_player_popups', array( 1 => array (
        'name' => '',
        'html' => '<a href="https://foliovision.com/2018/07/panamax"><img src="https://cdn.foliovision.com/images/2018/07/PanamaX-5-400x239.jpg" class="alignleft post-image entry-image lazyloaded " alt="PanamaX" itemprop="image" sizes="(max-width: 400px) 100vw, 400px" srcset="https://cdn.foliovision.com/images/2018/07/PanamaX-5-400x239.jpg 400w, https://cdn.foliovision.com/images/2018/07/PanamaX-5.jpg 1128w" width="400" height="239"></a>',
        'css' => '',
        'disabled' => '0',
      ) ) );
  }
  
  public function testEndActionsEmailCollection() {
    
    // triggering the default email list creation
    global $FV_Player_Email_Subscription;
    $FV_Player_Email_Subscription->init_options();   
        
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video.mp4" share="no" embed="false" popup="email-1"]' );     
      
    $sample = <<< HTML
<div id="wpfp_2534ca47632437a90737cb5c0e27b461" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; " data-ratio="0.5625">
  <div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    // is the email popup there?
    wp_deregister_script('flowplayer');
    flowplayer_prepare_scripts();
    
    global $wp_scripts;
    $this->assertTrue( stripos( $this->fix_newlines($wp_scripts->registered['flowplayer']->extra['data']), $this->fix_newlines('var fv_flowplayer_popup = {"wpfp_282c498132552aaa754164072eaaa0d0":{"html":"<div class=\"fv_player_popup fv_player_popup-1 wpfp_custom_popup_content\"><h3>Subscribe to list one<\/h3><p>Two good reasons to subscribe right now<\/p><form class=\"mailchimp-form  mailchimp-form-2\"><input type=\"hidden\" name=\"list\" value=\"1\" \/><input type=\"email\" placeholder=\"Email Address\" name=\"email\"\/><input type=\"text\" placeholder=\"First Name\" name=\"first_name\" required\/><input type=\"submit\" value=\"Subscribe\"\/><\/form><\/div>","pause":false}}') ) !== false );
    
    global $fv_fp;
    $fv_fp->aPopups = array();
  } 
  
  public function testEndActionsLoop() {
        
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video.mp4" share="no" embed="false" loop="true"]' );    
    
    $sample = <<< HTML
<div id="wpfp_2534ca47632437a90737cb5c0e27b461" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; " data-ratio="0.5625" data-fv_loop="1">
  <div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
  }    

  public function testEndActionsPopupNumber() {
        
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video.mp4" popup="1" share="no" embed="false"]' );    
    
    $sample = <<< HTML
<div id="wpfp_2534ca47632437a90737cb5c0e27b461" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; " data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
	<div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    // are the popups ready?
    wp_deregister_script('flowplayer');
    flowplayer_prepare_scripts();
    
    global $wp_scripts;
    $this->assertTrue( stripos( $this->fix_newlines($wp_scripts->registered['flowplayer']->extra['data']), $this->fix_newlines('var fv_flowplayer_popup = {"wpfp_40dd5c9f6426b9d96be06d43e9224af8":{"html":"<div class=\"fv_player_popup fv_player_popup-1 wpfp_custom_popup_content\"><a href=\"https:\/\/foliovision.com\/2018\/07\/panamax\"><img src=\"https:\/\/cdn.foliovision.com\/images\/2018\/07\/PanamaX-5-400x239.jpg\" class=\"alignleft post-image entry-image lazyloaded \" alt=\"PanamaX\" itemprop=\"image\" sizes=\"(max-width: 400px) 100vw, 400px\" srcset=\"https:\/\/cdn.foliovision.com\/images\/2018\/07\/PanamaX-5-400x239.jpg 400w, https:\/\/cdn.foliovision.com\/images\/2018\/07\/PanamaX-5.jpg 1128w\" width=\"400\" height=\"239\"><\/a><\/div>","pause":false}};') ) !== false );
    
    global $fv_fp;
    $fv_fp->aPopups = array();
  }
  
  public function testEndActionsPopupHTML() {
        
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video.mp4" share="no" embed="false" popup="'.addslashes('<a href="https://foliovision.com/2018/07/panamax"><img src="https://cdn.foliovision.com/images/2018/07/PanamaX-5-400x239.jpg" class="alignleft post-image entry-image lazyloaded " alt="PanamaX" itemprop="image" sizes="(max-width: 400px) 100vw, 400px" srcset="https://cdn.foliovision.com/images/2018/07/PanamaX-5-400x239.jpg 400w, https://cdn.foliovision.com/images/2018/07/PanamaX-5.jpg 1128w" width="400" height="239"></a>').'"]' );    
    
    $sample = <<< HTML
<div id="wpfp_2534ca47632437a90737cb5c0e27b461" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; " data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
	<div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    // are the popups ready?
    wp_deregister_script('flowplayer');
    flowplayer_prepare_scripts();
    
    global $wp_scripts;
    $this->assertTrue( stripos( $this->fix_newlines($wp_scripts->registered['flowplayer']->extra['data']), $this->fix_newlines('var fv_flowplayer_popup = {"wpfp_40dd5c9f6426b9d96be06d43e9224af8":{"html":"<div class=\"fv_player_popup fv_player_popup-1 wpfp_custom_popup_content\"><a href=\"https:\/\/foliovision.com\/2018\/07\/panamax\"><img src=\"https:\/\/cdn.foliovision.com\/images\/2018\/07\/PanamaX-5-400x239.jpg\" class=\"alignleft post-image entry-image lazyloaded \" alt=\"PanamaX\" itemprop=\"image\" sizes=\"(max-width: 400px) 100vw, 400px\" srcset=\"https:\/\/cdn.foliovision.com\/images\/2018\/07\/PanamaX-5-400x239.jpg 400w, https:\/\/cdn.foliovision.com\/images\/2018\/07\/PanamaX-5.jpg 1128w\" width=\"400\" height=\"239\"><\/a><\/div>","pause":false}};') ) !== false );
    
    global $fv_fp;
    $fv_fp->aPopups = array();
  }
  
  public function testEndActionsRedirect() {
        
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video.mp4" share="no" embed="false" redirect="https://foliovision.com"]' );    
    
    $sample = <<< HTML
<div id="wpfp_2534ca47632437a90737cb5c0e27b461" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; " data-ratio="0.5625" data-fv_redirect="https://foliovision.com">
  <div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
  }
  
  public function testEndActionsSplashEnd() {
        
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video.mp4" splash="https://cdn.site.com/video.jpg" share="no" embed="false" splashend="show"]' );    
    
    $sample = <<< HTML
<div id="wpfp_2534ca47632437a90737cb5c0e27b461" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video.jpg);" data-ratio="0.5625">
  <div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
  <div id="wpfp_ebf1dd081f973cd9a2b19499445705f2_custom_background" class="wpfp_custom_background" style="position: absolute; background: url('https://cdn.site.com/video.jpg') no-repeat center center; background-size: contain; width: 100%; height: 100%; z-index: 1;"></div>
</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
  } 
  
  public function tearDown() {
    delete_option('fv_player_popups');
  }

}
