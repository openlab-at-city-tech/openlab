<?php
use PHPUnit\Framework\TestCase;

final class FV_Player_Controller extends PHPUnit_Framework_TestCase {

  private $fvPlayerProInstance;

  // playlist items
  private $playlist_items = array(
    0 =>
      array (
        'sources' =>
          array (
            0 =>
              array (
                'src' => 'https://youtu.be/7uY0Ab5HlZ0',
                'type' => 'video/youtube'
              )
          )
      ),
    1 =>
      array (
        'sources' =>
          array (
            0 =>
              array (
                'src' => 'https://www.youtube.com/watch?v=1XiHhpGUmQg',
                'type' => 'video/youtube'
              )
          )
      ),
    2 =>
      array (
        'sources' =>
          array (
            0 =>
              array (
                'src' => 'https://www.youtube.com/watch?v=Q1eR8pUM5iY',
                'type' => 'video/youtube'
              )
          )
      )
  );

  // ads
  private $adsMock = array(
    array(
      'videos' => array(
        'mp4' => 'https://www.youtube.com/watch?v=tPEE9ZwTmy0'
      ),
      'disabled' => '0',
      'name' => 'cat preroll',
      'click' => 'http://www.pobox.sk'
    ),
    array(
      'videos' => array(
        'mp4' => 'https://www.youtube.com/watch?v=bCGmUCDj4Nc'
      ),
      'disabled' => '1',
      'name' => 'kids postroll',
      'click' => 'http://www.foliovision.com'
    ),
    array(
      'videos' => array(
        'mp4' => 'https://www.youtube.com/watch?v=OsAVRDo9znQ'
      ),
      'disabled' => '0',
      'name' => 'funny whatroll',
      'click' => 'http://www.google.com'
    )
  );

  public function setUp() {
    // set an empty global return value to be used
    // in all the mocked global WordPress functions
    // like add_action() and the such
    global $testReturnValue;
    $testReturnValue = '';

    include_once "../../models/flowplayer.php";
    global $fv_fp;
    $fv_fp = new flowplayer();
    
    //  todo: fix in core perhaps
    $fv_fp->aAds = array();
    $fv_fp->aPopups = array();
    
    include_once "../../controller/frontend.php";
  }

  public function tearDown() {
    Mockery::close();
  }
  
  public function test_flowplayer_prepare_scripts_js_everywhere() {    
    global $fv_fp;
    $fv_fp->conf['js-everywhere'] = true;
    
    ob_start();
    flowplayer_prepare_scripts();
    $output = ob_get_clean();
    
    $expected = "Registering jquery-ui-tabs for ?ver=1.2.3.4 footer? 1
Registering fv_flowplayer for fv-wordpress-flowplayer/css/flowplayer.css?ver=1.2.3.4
Registering flowplayer for fv-wordpress-flowplayer/flowplayer/fv-flowplayer.min.js?ver=1.2.3.4 footer? 1
Registering flowplayer-hlsjs for fv-wordpress-flowplayer/flowplayer/hls.min.js?ver=1.2.3.4 footer? 1
Localizing flowplayer with fv_flowplayer_conf = Array
(
    [fullscreen] => 1
    [swf] => fv-wordpress-flowplayer/flowplayer/flowplayer.swf?ver=1.2.3.4
    [swfHls] => fv-wordpress-flowplayer/flowplayer/flowplayerhls.swf?ver=1.2.3.4
    [embed] => Array
        (
            [library] => fv-wordpress-flowplayer/flowplayer/fv-flowplayer.min.js
            [script] => fv-wordpress-flowplayer/flowplayer/embed.min.js
            [skin] => fv-wordpress-flowplayer/css/flowplayer.css
            [swf] => fv-wordpress-flowplayer/flowplayer/flowplayer.swf?ver=1.2.3.4
            [swfHls] => fv-wordpress-flowplayer/flowplayer/flowplayerhls.swf?ver=1.2.3.4
        )

    [speeds] => Array
        (
            [0] => 0.25
            [1] => 0.5
            [2] => 0.75
            [3] => 1
            [4] => 1.25
            [5] => 1.5
            [6] => 1.75
            [7] => 2
        )

    [video_hash_links] => 1
    [safety_resize] => 1
    [volume] => 0.7
    [sticky_video] => 
    [sticky_place] => right-bottom
    [sticky_width] => 380
    [script_hls_js] => fv-wordpress-flowplayer/flowplayer/hls.min.js?ver=1.2.3.4
    [script_dash_js] => fv-wordpress-flowplayer/flowplayer/flowplayer.dashjs.min.js?ver=1.2.3.4
    [script_dash_js_version] => 2.7
    [hlsjs] => Array
        (
            [startLevel] => -1
            [fragLoadingMaxRetry] => 3
            [levelLoadingMaxRetry] => 3
            [capLevelToPlayerSize] => 1
        )

)

Localizing flowplayer with fv_flowplayer_translations = Array
(
    [0] => 
    [1] => Video loading aborted
    [2] => Network error
    [3] => Video not properly encoded
    [4] => Video file not found
    [5] => Unsupported video
    [6] => Skin not found
    [7] => SWF file not found
    [8] => Subtitles not found
    [9] => Invalid RTMP URL
    [10] => Unsupported video format. Try installing Adobe Flash.
    [11] => Click to watch the video
    [12] => [This post contains video, click to play]
    [video_expired] => <h2>Video file expired.<br />Please reload the page and play it again.</h2>
    [unsupported_format] => <h2>Unsupported video format.<br />Please use a Flash compatible device.</h2>
    [mobile_browser_detected_1] => Mobile browser detected, serving low bandwidth video.
    [mobile_browser_detected_2] => Click here
    [mobile_browser_detected_3] => for full quality.
    [live_stream_failed] => <h2>Live stream load failed.</h2><h3>Please try again later, perhaps the stream is currently offline.</h3>
    [live_stream_failed_2] => <h2>Live stream load failed.</h2><h3>Please try again later, perhaps the stream is currently offline.</h3>
    [what_is_wrong] => Please tell us what is wrong :
    [full_sentence] => Please give us more information (a full sentence) so we can help you better
    [error_JSON] => Admin: Error parsing JSON
    [no_support_IE9] => Admin: Video checker doesn't support IE 9.
    [check_failed] => Admin: Check failed.
    [playlist_current] => Now Playing
    [video_issues] => Video Issues
    [video_reload] => Video loading has stalled, click to reload
    [link_copied] => Video Link Copied to Clipboard
    [embed_copied] => Embed Code Copied to Clipboard
    [subtitles_disabled] => Subtitles disabled
    [subtitles_switched] => Subtitles switched to 
    [warning_iphone_subs] => This video has subtitles, that are not supported on your device.
    [warning_unstable_android] => You are using an old Android device. If you experience issues with the video please use <a href=\"https://play.google.com/store/apps/details?id=org.mozilla.firefox\">Firefox</a>. <a target=\"_blank\" href=\"https://foliovision.com/2017/05/issues-with-vimeo-on-android\">Why?</a>
    [warning_samsungbrowser] => You are using the Samsung Browser which is an older and buggy version of Google Chrome. If you experience issues with the video please use <a href=\"https://www.mozilla.org/en-US/firefox/new/\">Firefox</a> or other modern browser.
    [warning_old_safari] => You are using an old Safari browser. If you experience issues with the video please use <a href=\"https://www.mozilla.org/en-US/firefox/new/\">Firefox</a> or other modern browser. <a target=\"_blank\" href=\"https://foliovision.com/2017/05/issues-with-vimeo-on-android\">Why?</a>
)

Localizing flowplayer with fv_fp_ajaxurl = https://site.com/wp//wp-admin/admin-ajax.php
Localizing flowplayer with fv_flowplayer_playlists = Array
(
)

";  
      
    /*$aOut = explode( "\n", preg_replace( '~\r\n~', "\n", $output) );  
    $aExpected = explode( "\n", preg_replace( '~\r\n~', "\n", $expected ) );
      
    foreach( $aOut AS $k => $v ) {
      $this->assertEquals( $v, $aExpected[$k] );
    }*/
    
    $this->assertEquals( preg_replace( '~\r\n~', "\n", $expected ), preg_replace( '~\r\n~', "\n", $output) );
  }

}
