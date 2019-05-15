<?php

require_once( dirname(__FILE__).'/../fv-player-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_DBTest extends FV_Player_UnitTestCase {
  
  public function setUp() {
    parent::setUp();
        
    global $FV_Player_Db;
    $FV_Player_Db->import_player_data( false, false, json_decode( file_get_contents(dirname(__FILE__).'/player-data.json'), true) );

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
    
  }
  
  public function testDBShortcode() {
        
    $output = apply_filters( 'the_content', '[fvplayer id="1"]' );     
    
    $sample = <<< HTML
<div id="wpfp_034c92b7716ddbcf3a90a3a26440386e" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 100%; background-image: url(https://foliovision.com/video/burning-hula-hoop-girl-dominika.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
	<div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
	<div class="fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal fp-playlist-has-captions" rel="wpfp_034c92b7716ddbcf3a90a3a26440386e">
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/dominika-960-31.mp4","type":"video\/mp4"}],"id":1}'><div style='background-image: url("https://foliovision.com/video/burning-hula-hoop-girl-dominika.jpg")'></div><h4><span>Fire</span></h4></a>
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/Paypal-video-on-home-page.mp4","type":"video\/mp4"}],"id":2,"subtitles":[{"srclang":"en","label":"English","src":"https:\/\/foliovision.com\/videos\/paypal-splash.vtt"}]}'><div style='background-image: url("https://foliovision.com/videos/paypal-splash.jpg")'></div><h4><span>PayPal Background Video</span></h4></a>
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/Carly-Simon-Anticipation-1971.mp4","type":"video\/mp4"}],"id":3,"subtitles":[{"srclang":"en","label":"English","src":"https:\/\/foliovision.com\/images\/2014\/01\/carly-simon-1971-anticipation.vtt"}]}'><div style='background-image: url("https://foliovision.com/images/2014/01/carly-simon-1971-anticipation.png")'></div><h4><span>Carly Simon</span></h4></a>
	</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );    
  }
  
  public function testDBShortcodeWithSort() {
        
    $output = apply_filters( 'the_content', '[fvplayer id="1" sort="oldest"]' );
    
    $sample = <<< HTML
<div id="wpfp_034c92b7716ddbcf3a90a3a26440386e" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 100%; background-image: url(https://foliovision.com/video/burning-hula-hoop-girl-dominika.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
	<div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
	<div class="fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal fp-playlist-has-captions" rel="wpfp_034c92b7716ddbcf3a90a3a26440386e">
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/dominika-960-31.mp4","type":"video\/mp4"}],"id":1}'><div style='background-image: url("https://foliovision.com/video/burning-hula-hoop-girl-dominika.jpg")'></div><h4><span>Fire</span></h4></a>
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/Paypal-video-on-home-page.mp4","type":"video\/mp4"}],"id":2,"subtitles":[{"srclang":"en","label":"English","src":"https:\/\/foliovision.com\/videos\/paypal-splash.vtt"}]}'><div style='background-image: url("https://foliovision.com/videos/paypal-splash.jpg")'></div><h4><span>PayPal Background Video</span></h4></a>
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/Carly-Simon-Anticipation-1971.mp4","type":"video\/mp4"}],"id":3,"subtitles":[{"srclang":"en","label":"English","src":"https:\/\/foliovision.com\/images\/2014\/01\/carly-simon-1971-anticipation.vtt"}]}'><div style='background-image: url("https://foliovision.com/images/2014/01/carly-simon-1971-anticipation.png")'></div><h4><span>Carly Simon</span></h4></a>
	</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    $output = apply_filters( 'the_content', '[fvplayer id="1" sort="newest"]' );
    
    $sample = <<< HTML
<div id="wpfp_abbc39b8f78820ec7d8d7a8e34d43856" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 100%; background-image: url(https://foliovision.com/images/2014/01/carly-simon-1971-anticipation.png);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
	<div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
	<div class="fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal fp-playlist-has-captions" rel="wpfp_abbc39b8f78820ec7d8d7a8e34d43856">
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/Carly-Simon-Anticipation-1971.mp4","type":"video\/mp4"}],"subtitles":[{"srclang":"en","label":"English","src":"https:\/\/foliovision.com\/images\/2014\/01\/carly-simon-1971-anticipation.vtt"}]}'><div style='background-image: url("https://foliovision.com/images/2014/01/carly-simon-1971-anticipation.png")'></div><h4><span>Carly Simon</span></h4></a>
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/Paypal-video-on-home-page.mp4","type":"video\/mp4"}],"subtitles":[{"srclang":"en","label":"English","src":"https:\/\/foliovision.com\/videos\/paypal-splash.vtt"}]}'><div style='background-image: url("https://foliovision.com/videos/paypal-splash.jpg")'></div><h4><span>PayPal Background Video</span></h4></a>
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/dominika-960-31.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://foliovision.com/video/burning-hula-hoop-girl-dominika.jpg")'></div><h4><span>Fire</span></h4></a>
	</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    $output = apply_filters( 'the_content', '[fvplayer id="1" sort="title"]' );
    
    $sample = <<< HTML
<div id="wpfp_4836d78a28ea12e5df615a50be31878f" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 100%; background-image: url(https://foliovision.com/images/2014/01/carly-simon-1971-anticipation.png);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
	<div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
	<div class="fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal fp-playlist-has-captions" rel="wpfp_4836d78a28ea12e5df615a50be31878f">
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/Carly-Simon-Anticipation-1971.mp4","type":"video\/mp4"}],"subtitles":[{"srclang":"en","label":"English","src":"https:\/\/foliovision.com\/images\/2014\/01\/carly-simon-1971-anticipation.vtt"}]}'><div style='background-image: url("https://foliovision.com/images/2014/01/carly-simon-1971-anticipation.png")'></div><h4><span>Carly Simon</span></h4></a>
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/dominika-960-31.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://foliovision.com/video/burning-hula-hoop-girl-dominika.jpg")'></div><h4><span>Fire</span></h4></a>
		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/foliovision.com\/videos\/Paypal-video-on-home-page.mp4","type":"video\/mp4"}],"subtitles":[{"srclang":"en","label":"English","src":"https:\/\/foliovision.com\/videos\/paypal-splash.vtt"}]}'><div style='background-image: url("https://foliovision.com/videos/paypal-splash.jpg")'></div><h4><span>PayPal Background Video</span></h4></a>
	</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );      
  }
  
  public function testDBExport() {
    global $FV_Player_Db;        
    $output = json_encode( $FV_Player_Db->export_player_data(false,false,1), JSON_UNESCAPED_SLASHES );
    $this->assertEquals( file_get_contents(dirname(__FILE__).'/player-data.json'), $output );  
  }
  
  public function tearDown() {
    delete_option('fv_player_popups');
  }

}
