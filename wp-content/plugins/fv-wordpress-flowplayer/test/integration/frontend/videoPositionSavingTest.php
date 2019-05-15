<?php

require_once( dirname(__FILE__).'/../fv-player-ajax-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_videoPositionSavingTestCase extends FV_Player_Ajax_UnitTestCase {

  var $postID = -1;
  var $userID = -1;

  protected $backupGlobals = false;

  public function setUp() {
    parent::setUp();

    // create a post with playlist shortcode
    $this->postID = $this->factory->post->create( array(
      'post_title' => 'Playlist with Ads',
      'post_content' => '[fvplayer src="https://cdn.site.com/1.mp4" playlist="https://cdn.site.com/2.mp4;https://cdn.site.com/3.mp4" saveposition="yes"]'
    ) );

    global $fv_fp;

    include_once "../../../fv-wordpress-flowplayer/models/flowplayer.php";
    include_once "../../../fv-wordpress-flowplayer/models/flowplayer-frontend.php";
    $fv_fp = new flowplayer_frontend();

    // add new user and create last saved position metadata for this new user
    $this->userID = $this->factory->user->create(array(
      'role' => 'admin'
    ));

    /*add_user_meta($this->userID, 'fv_wp_flowplayer_position_watch?v=1XiHhpGUmQg', '12');
    var_export(get_user_meta($this->userID, 'fv_wp_flowplayer_position_watch?v=1XiHhpGUmQg', true ));*/

  }

  public function testNoSaveForNotLoggedInUsers() {
    // is anybody listening out there?
    $this->assertTrue( has_action('wp_ajax_fv_wp_flowplayer_video_position_save') );

    // Spoof the nonce in the POST superglobal
    //$_POST['_wpnonce'] = wp_create_nonce( 'anything-here-if-needed' );

    // set up POST data for video resume times
    $_POST['action'] = 'fv_wp_flowplayer_video_position_save';
    $_POST['videoTimes'] = array(
      array(
        'name' => 'https://cdn.site.com/2.mp4',
        'position' => 12
      )
    );

    // call the AJAX which
    try {
      $this->_handleAjax( 'fv_wp_flowplayer_video_position_save' );
    } catch ( WPAjaxDieContinueException $e ) {
      $response = json_decode( $this->_last_response );
      $this->assertInternalType( 'object', $response );
      $this->assertObjectHasAttribute( 'success', $response );
      $this->assertFalse( $response->success );
    }

    // check for clear playlist HTML without last player position data items
    $post = get_post( $this->postID );
    $output = apply_filters( 'the_content', $post->post_content );

    $expect = "<div id=\"some-test-hash\" class=\"flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy\" style=\"max-width: 100%; \" data-ratio=\"0.5625\" data-save-position=\"yes\">
	<div class=\"fp-ratio\" style=\"padding-top: 56.25%\"></div>
  <div class=\"fp-ui\"><div class=\"fp-play fp-visible\"><a class=\"fp-icon fp-playbtn\"></a></div></div>
<div class='fvp-share-bar'><ul class=\"fvp-sharing\">
    <li><a class=\"sharing-facebook\" href=\"https://www.facebook.com/sharer/sharer.php?u=\" target=\"_blank\"></a></li>
    <li><a class=\"sharing-twitter\" href=\"https://twitter.com/home?status=Test+Blog+\" target=\"_blank\"></a></li>
    <li><a class=\"sharing-google\" href=\"https://plus.google.com/share?url=\" target=\"_blank\"></a></li>
    <li><a class=\"sharing-email\" href=\"mailto:?body=Check%20out%20the%20amazing%20video%20here%3A%20\" target=\"_blank\"></a></li></ul><div><label><a class=\"embed-code-toggle\" href=\"#\"><strong>Embed</strong></a></label></div><div class=\"embed-code\"><label>Copy and paste this HTML code into your webpage to embed.</label><textarea></textarea></div></div>
</div>
	<div class=\"fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal\" rel=\"some-test-hash\">
		<a href='#' onclick='return false' data-item='{\"sources\":[{\"src\":\"https:\/\/cdn.site.com\/1.mp4\",\"type\":\"video\/mp4\"}]}'><div></div></a>
		<a href='#' onclick='return false' data-item='{\"sources\":[{\"src\":\"https:\/\/cdn.site.com\/2.mp4\",\"type\":\"video\/mp4\"}]}'><div></div></a>
		<a href='#' onclick='return false' data-item='{\"sources\":[{\"src\":\"https:\/\/cdn.site.com\/3.mp4\",\"type\":\"video\/mp4\"}]}'><div></div></a>
	</div>

";
    
    $this->assertEquals( $this->fix_newlines($expect), $this->fix_newlines($output) );
  }

  public function testSaveAndPlaylistHTMLForLoggedInUsers() {
    // is anybody listening out there?
    $this->assertTrue( has_action('wp_ajax_fv_wp_flowplayer_video_position_save') );
    
    // Spoof the nonce in the POST superglobal
    //$_POST['_wpnonce'] = wp_create_nonce( 'anything-here-if-needed' );

    // set this user as the active one
    global $current_user;
    $restore_user = $current_user;
    wp_set_current_user($this->userID);

    // set up POST data for video resume times
    $_POST['action'] = 'fv_wp_flowplayer_video_position_save';
    $_POST['videoTimes'] = array(
      array(
        'name' => 'https://cdn.site.com/2.mp4',
        'position' => 12
      )
    );

    // call the AJAX which
    try {
      $this->_handleAjax( 'fv_wp_flowplayer_video_position_save' );
    } catch ( WPAjaxDieContinueException $e ) {
      $response = json_decode( $this->_last_response );
      $this->assertInternalType( 'object', $response );
      $this->assertObjectHasAttribute( 'success', $response );
      $this->assertTrue( $response->success );
    }

    // check if metadata was saved correctly
    $this->assertEquals(12, get_user_meta($this->userID, 'fv_wp_flowplayer_position_2', true ));

    // check that the playlist HTML is being generated correctly, with the last player position taken into consideration
    $post = get_post( $this->postID );
    $output = apply_filters( 'the_content', $post->post_content );

    $expect = "<div id=\"some-test-hash\" class=\"flowplayer no-brand is-splash no-svg is-paused skin-slim fp-slim fp-edgy\" style=\"max-width: 100%; \" data-ratio=\"0.5625\" data-save-position=\"yes\">
	<div class=\"fp-ratio\" style=\"padding-top: 56.25%\"></div>
  <div class=\"fp-ui\"><div class=\"fp-play fp-visible\"><a class=\"fp-icon fp-playbtn\"></a></div></div>
<div class='fvp-share-bar'><ul class=\"fvp-sharing\">
    <li><a class=\"sharing-facebook\" href=\"https://www.facebook.com/sharer/sharer.php?u=\" target=\"_blank\"></a></li>
    <li><a class=\"sharing-twitter\" href=\"https://twitter.com/home?status=Test+Blog+\" target=\"_blank\"></a></li>
    <li><a class=\"sharing-google\" href=\"https://plus.google.com/share?url=\" target=\"_blank\"></a></li>
    <li><a class=\"sharing-email\" href=\"mailto:?body=Check%20out%20the%20amazing%20video%20here%3A%20\" target=\"_blank\"></a></li></ul><div><label><a class=\"embed-code-toggle\" href=\"#\"><strong>Embed</strong></a></label></div><div class=\"embed-code\"><label>Copy and paste this HTML code into your webpage to embed.</label><textarea></textarea></div></div>
</div>
	<div class=\"fp-playlist-external fv-playlist-design-2017 fp-playlist-horizontal\" rel=\"some-test-hash\">
		<a href='#' onclick='return false' data-item='{\"sources\":[{\"src\":\"https:\/\/cdn.site.com\/1.mp4\",\"type\":\"video\/mp4\"}]}'><div></div></a>
		<a href='#' onclick='return false' data-item='{\"sources\":[{\"src\":\"https:\/\/cdn.site.com\/2.mp4\",\"type\":\"video\/mp4\",\"position\":\"12\"}]}'><div></div></a>
		<a href='#' onclick='return false' data-item='{\"sources\":[{\"src\":\"https:\/\/cdn.site.com\/3.mp4\",\"type\":\"video\/mp4\"}]}'><div></div></a>
	</div>

";
    
    $this->assertEquals( $this->fix_newlines($expect), $this->fix_newlines($output) );
    
    $current_user;
  }

}