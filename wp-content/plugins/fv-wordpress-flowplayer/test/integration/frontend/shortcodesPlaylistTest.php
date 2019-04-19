<?php

require_once( dirname(__FILE__).'/../fv-player-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_ShortcodePlaylistTestCase extends FV_Player_UnitTestCase {
  
  public function setUp() {
    parent::setUp();
    
    $shortcode_body = 'src="https://cdn.site.com/video1.mp4" splash="https://cdn.site.com/video1.jpg" playlist="https://cdn.site.com/video2.mp4,https://cdn.site.com/video2.jpg;https://cdn.site.com/video3.mp4,https://cdn.site.com/video3.jpg" caption="Video 1;Video 2; Video 3" share="no" embed="false"';

    // create a post with playlist shortcode
    $this->playlist_default = $this->factory->post->create( array(      
      'post_content' => '[fvplayer '.$shortcode_body.']'
    ) );
    
    $this->playlist_vertical = $this->factory->post->create( array(      
      'post_content' => '[fvplayer '.$shortcode_body.' liststyle="vertical"]'
    ) );
    
    $this->playlist_tabs = $this->factory->post->create( array(      
      'post_content' => '[fvplayer '.$shortcode_body.' liststyle="tabs"]'
    ) );
    
    $this->playlist_prevnext = $this->factory->post->create( array(      
      'post_content' => '[fvplayer '.$shortcode_body.' liststyle="prevnext"]'
    ) );
    
    $this->playlist_slider = $this->factory->post->create( array(      
      'post_content' => '[fvplayer '.$shortcode_body.' liststyle="slider"]'
    ) );

  }

  public function testPlaylistStyleShortcode() {
    global $post;
    
    $post = get_post( $this->playlist_default );
    $output = apply_filters( 'the_content', $post->post_content );
    
    $sample = <<< HTML
    <div id="wpfp_10ecd1d835d0db002906d6666d27a916" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 100%; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
    	<div class="fp-ratio" style="padding-top: 56.25%"></div>
      <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
    </div>
    	<div class="fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal fp-playlist-has-captions" rel="wpfp_10ecd1d835d0db002906d6666d27a916">
    		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video1.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video1.jpg")'></div><h4><span>Video 1</span></h4></a>
    		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video2.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video2.jpg")'></div><h4><span>Video 2</span></h4></a>
    		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video3.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video3.jpg")'></div><h4><span> Video 3</span></h4></a>
    	</div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    
    $post = get_post( $this->playlist_vertical );
    $output = apply_filters( 'the_content', $post->post_content );
    
    $sample = <<< HTML
    <div class="fp-playlist-vertical-wrapper"><div id="wpfp_10ecd1d835d0db002906d6666d27a916" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 100%; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
    	<div class="fp-ratio" style="padding-top: 56.25%"></div>
      <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
    </div>
    	<div class="fp-playlist-external fv-playlist-design-2017 fp-playlist-vertical fp-playlist-has-captions" rel="wpfp_10ecd1d835d0db002906d6666d27a916">
    		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video1.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video1.jpg")'></div><h4><span>Video 1</span></h4></a>
    		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video2.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video2.jpg")'></div><h4><span>Video 2</span></h4></a>
    		<a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video3.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video3.jpg")'></div><h4><span> Video 3</span></h4></a>
    	</div>
    </div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    
    $post = get_post( $this->playlist_prevnext );
    $output = apply_filters( 'the_content', $post->post_content );

    $sample = <<< HTML
<div id="wpfp_10ecd1d835d0db002906d6666d27a916" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 100%; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
  <div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
<a class="fp-prev" title="prev"></a><a class="fp-next" title="next"></a></div>
  <div style="display: none" class="fp-playlist-external fv-playlist-design-2017 fp-playlist-has-captions" rel="wpfp_10ecd1d835d0db002906d6666d27a916">
    <a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video1.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video1.jpg")'></div><h4><span>Video 1</span></h4></a>
    <a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video2.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video2.jpg")'></div><h4><span>Video 2</span></h4></a>
    <a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video3.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video3.jpg")'></div><h4><span> Video 3</span></h4></a>
  </div>
HTML;

    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    
    $post = get_post( $this->playlist_slider );
    $output = apply_filters( 'the_content', $post->post_content );

    $sample = <<< HTML
<div id="wpfp_10ecd1d835d0db002906d6666d27a916" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 100%; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
  <div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
</div>
  <div class='fv-playlist-slider-wrapper'><div class="fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal fp-playlist-has-captions" rel="wpfp_10ecd1d835d0db002906d6666d27a916" style="width: 750px">
    <a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video1.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video1.jpg")'></div><h4><span>Video 1</span></h4></a>
    <a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video2.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video2.jpg")'></div><h4><span>Video 2</span></h4></a>
    <a href='#' onclick='return false' data-item='{"sources":[{"src":"https:\/\/cdn.site.com\/video3.mp4","type":"video\/mp4"}]}'><div style='background-image: url("https://cdn.site.com/video3.jpg")'></div><h4><span> Video 3</span></h4></a>
  </div>
</div>
HTML;

    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
  }
  
  public function testPlaylistTabsShortcode() {
    global $post;
    
    $post = get_post( $this->playlist_tabs );
    $output = apply_filters( 'the_content', $post->post_content );
    
    $sample = <<< HTML
<script>document.body.className += " fv_flowplayer_tabs_hide";</script><div class="fv_flowplayer_tabs tabs woocommerce-tabs" style="max-width: 640px"><div id="tabs-10-1" class="fv_flowplayer_tabs_content"><ul><li><a href="#tabs-10-1-0">Video 1</a></li><li><a href="#tabs-10-1-1">Video 2</a></li><li><a href="#tabs-10-1-2"> Video 3</a></li></ul><div class="fv_flowplayer_tabs_cl"></div><div id="tabs-10-1-0" class="fv_flowplayer_tabs_first"><div id="wpfp_5d697f461a6a69e41882ec0212d63d1f" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video1.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
</div><div id="tabs-10-1-1"><div id="wpfp_f31738e686c3bdae67dfd7e57dec3d8c" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video2.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video2.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
</div><div id="tabs-10-1-2"><div id="wpfp_0dfbb08c099beb557be57907b1c01eb2" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video3.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video3.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
</div><div class="fv_flowplayer_tabs_cl"></div><div class="fv_flowplayer_tabs_cr"></div></div></div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
        
    global $fv_fp;
    $this->assertTrue( $fv_fp->load_tabs );    
  }
  
  public function tearDown() {
    global $fv_fp, $FV_Player_lightbox;
    $fv_fp->load_tabs = false;
    $FV_Player_lightbox = new FV_Player_lightbox(); // reset the lightbox loading flag and footer lightboxed players HTML
    
    parent::tearDown();
  }

}
