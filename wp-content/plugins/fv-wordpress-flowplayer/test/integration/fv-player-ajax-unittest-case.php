<?php

abstract class FV_Player_Ajax_UnitTestCase extends WP_Ajax_UnitTestCase {
  
  protected $backupGlobals = false;
  
  public function setUp() {
    parent::setUp();
    
    global $fv_fp;
    $this->restore = $fv_fp->conf;
    
    //  somehow this got hooked in again after being removed in WP_Ajax_UnitTestCase::setUpBeforeClass() already
    remove_action( 'admin_init', '_maybe_update_core' );
    remove_action( 'admin_init', '_maybe_update_plugins' );
    remove_action( 'admin_init', '_maybe_update_themes' );    
  }  
  
  public function fix_newlines( $html ) {
    $html = preg_replace( '/"wpfp_[0-9a-z]+"/', '"some-test-hash"', $html);
    $html = preg_replace( '~<input type="hidden" id="([^"]*?)nonce" name="([^"]*?)nonce" value="([^"]*?)" />~', '<input type="hidden" id="$1nonce" name="$2nonce" value="XYZ" />', $html);
    $html = preg_replace( "~nonce: '([^']*?)'~", "nonce: 'XYZ'", $html);
    
    // testProfileScreen
    $html = preg_replace( '~fv_ytplayer_[a-z0-9]+~', 'fv_ytplayer_XYZ', $html);
    $html = preg_replace( '~fv_vimeo_[a-z0-9]+~', 'fv_vimeo_XYZ', $html);
    $html = preg_replace( '~<input type="hidden" id="fv-player-custom-videos-_fv_player_user_video-0" name="fv-player-custom-videos-_fv_player_user_video-0" value="[^"]*?" />~', '<input type="hidden" id="fv-player-custom-videos-_fv_player_user_video-0" name="fv-player-custom-videos-_fv_player_user_video-0" value="XYZ" />', $html);
    
    $html = preg_replace( '~convert_jwplayer=[a-z0-9]+~', 'convert_jwplayer=XYZ', $html);
    $html = preg_replace( '~_wpnonce=[a-z0-9]+~', '_wpnonce=XYZ', $html);
    
    $html = explode("\n",$html);
    foreach( $html AS $k => $v ) {
      if( trim($v) == '' ) unset($html[$k]);
    }
    $html = implode( "\n", array_map('trim',$html) );
    
    $html = preg_replace( '~\t~', '', $html );
    return $html;
  }

  // we need to set up PRO player with an appropriate key, or the PRO player won't work
  public static function wpSetUpBeforeClass() {
    global $fv_fp;

    // without this included, fv_wp_flowplayer_delete_extensions_transients() would not be found
    //include_once "../../../fv-wordpress-flowplayer/controller/backend.php";

    // include the flowplayer loader
    include_once "../../../fv-wordpress-flowplayer/flowplayer.php";

    // include the PRO plugin class, so it can intercept data saving
    // and update the ads structure as needed for saving
    //include_once "../../beta/fv-player-pro.class.php";

    // save initial settings
    //$fv_fp->_set_conf();
  }
  
  public function tearDown() {
    parent::tearDown();
    
    global $fv_fp;
    $fv_fp->conf = $this->restore;
  }  

}
