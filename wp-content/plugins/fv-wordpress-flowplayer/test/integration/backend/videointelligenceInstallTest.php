<?php

require_once( dirname(__FILE__).'/../fv-player-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_videointelligenceInstallTestCase extends FV_Player_UnitTestCase {
  
  var $directory = WP_CONTENT_DIR.'/plugins/fv-player-video-intelligence';
  
  //  we need to convince it is showing the FV Player settings screen!
  public static function wpSetUpBeforeClass() {
    set_current_screen( 'settings_page_fvplayer' );
    
    parent::wpSetUpBeforeClass();    
    
    remove_action( 'admin_init', 'wp_admin_headers' );
    do_action( 'admin_init' );
  }  
  
  public function setUp() {
    parent::setUp();

    // add new user and create last saved position metadata for this new user
    $this->userID = $this->factory->user->create(array(
      'role' => 'admin'
    ));

  }  
  
  public function testInstall() {
    
    $this->assertTrue( defined('FV_PLAYER_VI_USER') && defined('FV_PLAYER_VI_PASS') );
    
    // we need to submit a valid vi Ads login with an admin user capable of installing plugins    
    global $current_user;
    wp_set_current_user($this->userID);
    $current_user->add_cap( 'install_plugins' );        
    
    $_POST['vi_login'] = FV_PLAYER_VI_USER;
    $_POST['vi_pass'] = FV_PLAYER_VI_PASS;
    $_POST['fv_player_vi_install'] = true;
    $_REQUEST['nonce_fv_player_vi_install'] = wp_create_nonce('fv_player_vi_install');  // notice the nonce goes into $_REQUEST
    
    $this->assertFalse( file_exists($this->directory) );
    
    do_action( 'admin_menu' );
    
    $this->assertEquals( "FV Player video intelligence extension installed successfully!", get_option('fv_wordpress_flowplayer_deferred_notices') );
    
    $this->assertFileExists( $this->directory );
    
    do_action( 'admin_menu' );

    $this->assertEquals( "FV Player video intelligence extension upgraded successfully!", get_option('fv_wordpress_flowplayer_deferred_notices') );
  }
  
  public function tearDown() {
    unset($_POST['vi_login']);
    unset($_POST['vi_pass']);
    unset($_POST['fv_player_vi_install']);
    unset($_REQUEST['nonce_fv_player_vi_install']);
    
    if( strlen($this->directory) > 10 ) {      
      $this->removeDirectory($this->directory);
    }
  }
  
  private function removeDirectory($path) {
   	$files = glob($path . '/*');
  	foreach ($files as $file) {
  		is_dir($file) ? $this->removeDirectory($file) : unlink($file);
  	}
  	rmdir($path);
   	return;
  }

}
