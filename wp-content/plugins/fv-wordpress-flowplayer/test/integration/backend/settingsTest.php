<?php

require_once( dirname(__FILE__).'/../fv-player-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_SettingsTestCase extends FV_Player_UnitTestCase {
  
  //  we need to convince it is showing the FV Player settings screen!
  public static function wpSetUpBeforeClass() {
    set_current_screen( 'settings_page_fvplayer' );
    
    parent::wpSetUpBeforeClass();
    
    remove_action( 'admin_init', 'wp_admin_headers' );
    do_action( 'admin_init' );
  }  
  
  public function testSettingsScreen() {
        
    ob_start();
    fv_player_admin_page();
    $output = ob_get_clean();
    
    $one = $this->fix_newlines(file_get_contents(dirname(__FILE__).'/testSettingsScreen.html'));    
    $two = explode("\n",$this->fix_newlines($output));
    foreach( explode("\n",$one) as $k => $v ) {
      
      /*if( $v != $two[$k]) {
        for($i=0;$i<strlen($two[$k]);$i++) {
          if( $v[$i] != $two[$k][$i]) {
            var_dump( $v[$i].' vs '.$two[$k][$i].' '.ord($two[$k][$i]) );
          }
        }
      }*/
      
      $this->assertEquals( $v, $two[$k] );
    }
    
    $this->assertEquals( $this->fix_newlines(file_get_contents(dirname(__FILE__).'/testSettingsScreen.html')), $this->fix_newlines($output) );
  }

}
