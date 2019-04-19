<?php

require_once( dirname(__FILE__).'/../fv-player-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_ShortcodeLightboxTestCase extends FV_Player_UnitTestCase {
  
  var $shortcode_body = 'src="https://cdn.site.com/video1.mp4" splash="https://cdn.site.com/video1.jpg" playlist="https://cdn.site.com/video2.mp4,https://cdn.site.com/video2.jpg;https://cdn.site.com/video3.mp4,https://cdn.site.com/video3.jpg" caption="Video 1;Video 2;Video 3" share="no" embed="false"';

  public function testSimple() {
    
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video1.mp4" splash="https://cdn.site.com/video1.jpg" lightbox="true" share="no" embed="false"]' );
    
    $sample = <<< HTML
<div data-fancybox='gallery' data-options='{"touch":false,"thumb":"https:\/\/cdn.site.com\/video1.jpg"}' id='fv_flowplayer_5d2ac904592b20b5bf87a2a85df7ace7_lightbox_starter'  href='#wpfp_5d2ac904592b20b5bf87a2a85df7ace7' class='flowplayer lightbox-starter is-splash no-svg is-paused skin-slim fp-slim fp-edgy' style="max-width: 640px; max-height: 360px; background-image: url('https://cdn.site.com/video1.jpg')" data-ratio="0.5625"><div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div><div class="fp-ratio" style="padding-top: 56.25%"></div></div>
<div class='fv_player_lightbox_hidden' style='display: none'>
  <div id="some-test-hash" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video1.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
   <div class="fp-ratio" style="padding-top: 56.25%"></div>
   <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
   </div>
</div>
HTML;
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
  }
  
  
  public function testCaption() {
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video1.mp4" splash="https://cdn.site.com/video1.jpg" lightbox="true;Video 1" share="no" embed="false"]' );
    
    $sample = <<< HTML
<div data-fancybox='gallery' data-options='{"touch":false,"thumb":"https:\/\/cdn.site.com\/video1.jpg"}' id='fv_flowplayer_5d2ac904592b20b5bf87a2a85df7ace7_lightbox_starter' title='Video 1' href='#wpfp_5d2ac904592b20b5bf87a2a85df7ace7' class='flowplayer lightbox-starter is-splash no-svg is-paused skin-slim fp-slim fp-edgy' style="max-width: 640px; max-height: 360px; background-image: url('https://cdn.site.com/video1.jpg')" data-ratio="0.5625"><div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div><div class="fp-ratio" style="padding-top: 56.25%"></div></div>
<div class='fv_player_lightbox_hidden' style='display: none'>
  <div id="some-test-hash" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video1.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
   <div class="fp-ratio" style="padding-top: 56.25%"></div>
   <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
   </div>
</div>
HTML;
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
  }
  
  
  public function testCaptionAndDimensions() {    
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video1.mp4" splash="https://cdn.site.com/video1.jpg" lightbox="true;320;240;Video 1" share="no" embed="false"]' );
    $sample = <<< HTML
<div data-fancybox='gallery' data-options='{"touch":false,"thumb":"https:\/\/cdn.site.com\/video1.jpg"}' id='fv_flowplayer_f1f51bb87ed9702bd91ac63990cee57b_lightbox_starter' title='Video 1' href='#wpfp_f1f51bb87ed9702bd91ac63990cee57b' class='flowplayer lightbox-starter is-splash no-svg is-paused skin-slim fp-slim fp-edgy' style="max-width: 320px; max-height: 240px; background-image: url('https://cdn.site.com/video1.jpg')" data-ratio="0.75"><div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div><div class="fp-ratio" style="padding-top: 75%"></div></div>
<div class='fv_player_lightbox_hidden' style='display: none'>
  <div id="some-test-hash" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video1.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
   <div class="fp-ratio" style="padding-top: 56.25%"></div>
   <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>
   </div>
</div>
HTML;
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );      
  }
  
  
  public function testText() {    
    $output = apply_filters( 'the_content', '[fvplayer src="https://cdn.site.com/video1.mp4" splash="https://cdn.site.com/video1.jpg" caption="Video 1" lightbox="true;text" share="no" embed="false"]' );
    $sample = <<< HTML
<a data-fancybox='gallery' data-options='{"touch":false}' id='fv_flowplayer_2f9724515033ace3d660707b426f527c_lightbox_starter' title='Video 1' class='fv-player-lightbox-link' href="#" data-src='#wpfp_2f9724515033ace3d660707b426f527c'>Video 1</a>
HTML;
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    
    ob_start();
    do_action('wp_footer');
    $footer = ob_get_clean();
    
    $sample = <<< HTML
<div style='display: none'>
<div id="wpfp_11361d379334c11f2eaa75f6aacd8386" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video1.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy has-caption" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
<p class='fp-caption'>Video 1</p></div>
<!-- lightboxed players -->
HTML;
    
    $this->assertTrue( stripos( $this->fix_newlines($footer),$this->fix_newlines($sample) ) !== false );  //  is the lightboxed players in the footer?
    
    global $FV_Player_lightbox;
    $this->assertTrue( $FV_Player_lightbox->bLoad );  //  is the flag to load lightbox JS set?
  }

  
  public function testPlaylist() {
    $output = apply_filters( 'the_content', '[fvplayer '.$this->shortcode_body.' lightbox="true"]' );
    
    $sample = <<< HTML
<div data-fancybox='gallery' data-options='{"touch":false,"thumb":"https:\/\/cdn.site.com\/video1.jpg"}' id='fv_flowplayer_5d2ac904592b20b5bf87a2a85df7ace7_lightbox_starter'  href='#wpfp_5d2ac904592b20b5bf87a2a85df7ace7' class='flowplayer lightbox-starter is-splash no-svg is-paused skin-slim fp-slim fp-edgy' style="max-width: 640px; max-height: 360px; background-image: url('https://cdn.site.com/video1.jpg')" data-ratio="0.5625"><div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div><div class="fp-ratio" style="padding-top: 56.25%"></div></div>
<div class='fv_player_lightbox_hidden' style='display: none'>
<div id="wpfp_5d2ac904592b20b5bf87a2a85df7ace7" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video1.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy has-caption" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
<p class='fp-caption'>Video 1</p></div><div class='fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal fp-playlist-has-captions'><a id='fv_flowplayer_lightbox_placeholder' href='#' onclick='document.getElementById("fv_flowplayer_5d2ac904592b20b5bf87a2a85df7ace7_lightbox_starter").click(); return false'><div style="background-image: url('https://cdn.site.com/video1.jpg')"></div><h4><span>Video 1</span></h4></a><a data-fancybox='gallery' data-options='{"touch":false,"thumb":"https:\/\/cdn.site.com\/video2.jpg"}'  id='fv_flowplayer_lightbox_starter' class='fv-player-lightbox-link' href='#' data-src='#wpfp_e802b17ebbace952275cd50709bf549b'><div style="background-image: url('https://cdn.site.com/video2.jpg')"></div><h4><span>Video 2</span></h4></a><a data-fancybox='gallery' data-options='{"touch":false,"thumb":"https:\/\/cdn.site.com\/video3.jpg"}'  id='fv_flowplayer_lightbox_starter' class='fv-player-lightbox-link' href='#' data-src='#wpfp_2ffbd4e84c1ecf2e00db5edf98996de3'><div style="background-image: url('https://cdn.site.com/video3.jpg')"></div><h4><span>Video 3</span></h4></a></div><div class='fv_player_lightbox_hidden' style='display: none'>
<div id="wpfp_e802b17ebbace952275cd50709bf549b" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video2.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy has-caption" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video2.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
<p class='fp-caption'>Video 2</p></div><div class='fv_player_lightbox_hidden' style='display: none'>
<div id="wpfp_2ffbd4e84c1ecf2e00db5edf98996de3" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video3.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy has-caption" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video3.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
<p class='fp-caption'>Video 3</p></div>
HTML;
    
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    // setting liststyle shouldn't affect anything!
    $output = apply_filters( 'the_content', '[fvplayer '.$this->shortcode_body.' lightbox="true" liststyle="slider"]' );
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );  
  }
    
    
  public function testPlaylistText() {
    $output = apply_filters( 'the_content', '[fvplayer '.$this->shortcode_body.' lightbox="true;text"]' );
    $sample = <<< HTML
<ul><li><a data-fancybox='gallery' data-options='{"touch":false}' id='fv_flowplayer_b721d6e309a0b856f27cc5ffe3f64c19_lightbox_starter' title='Video 1' class='fv-player-lightbox-link' href="#" data-src='#wpfp_b721d6e309a0b856f27cc5ffe3f64c19'>Video 1</a></li><li><a data-fancybox='gallery' data-options='{"touch":false}' id='fv_flowplayer_lightbox_starter' title='Video 2' class='fv-player-lightbox-link' href='#' data-src='#wpfp_f7e1bf7ee8d12a2bf3bc4f148cdd718c'>Video 2</a></li><li><a data-fancybox='gallery' data-options='{"touch":false}' id='fv_flowplayer_lightbox_starter' title='Video 3' class='fv-player-lightbox-link' href='#' data-src='#wpfp_d0ecb746d43cfeca15296bd46c0dee3c'>Video 3</a></li></div></ul>
HTML;
    $this->assertEquals( $this->fix_newlines($sample), $this->fix_newlines($output) );
    
    
    ob_start();
    do_action('wp_footer');
    $footer = ob_get_clean();

    $sample = <<< HTML
<div style='display: none'>
<div id="wpfp_41dd59eae18defa2521bb63946885f69" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video1.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy has-caption" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video1.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
<p class='fp-caption'>Video 1</p></div>
<div style='display: none'>
<div id="wpfp_c189727d02321b2388ebc844c1fdc0c7" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video2.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy has-caption" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video2.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
<p class='fp-caption'>Video 2</p></div>
<div style='display: none'>
<div id="wpfp_51771d567d4dd88882f65b4b89fc81d6" data-item="{&quot;sources&quot;:[{&quot;src&quot;:&quot;https:\/\/cdn.site.com\/video3.mp4&quot;,&quot;type&quot;:&quot;video\/mp4&quot;}]}" class="flowplayer lightboxed no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy has-caption" data-embed="false" style="max-width: 640px; max-height: 360px; background-image: url(https://cdn.site.com/video3.jpg);" data-ratio="0.5625">
	<div class="fp-ratio" style="padding-top: 56.25%"></div>
  <div class="fp-ui"><div class="fp-play fp-visible"><a class="fp-icon fp-playbtn"></a></div></div>

</div>
<p class='fp-caption'>Video 3</p></div>
<!-- lightboxed players -->
HTML;
    
    $this->assertTrue( stripos( $this->fix_newlines($footer),$this->fix_newlines($sample) ) !== false );  //  are the lightboxed players in the footer?
    
    global $FV_Player_lightbox;
    $this->assertTrue( $FV_Player_lightbox->bLoad );  //  is the flag to load lightbox JS set?
  }

}
