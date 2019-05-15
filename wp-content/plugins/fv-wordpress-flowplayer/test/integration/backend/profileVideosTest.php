<?php

require_once( dirname(__FILE__).'/../fv-player-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_ProfileVideosTestCase extends FV_Player_UnitTestCase {
    
  public function testProfileScreen() {
    global $fv_fp;
    $fv_fp->conf['profile_videos_enable_bio'] = true;
    
    // add new user and create last saved position metadata for this new user
    $this->userID = $this->factory->user->create(array(
      'role' => 'admin'
    ));
    
    add_user_meta($this->userID, '_fv_player_user_video', '[fvplayer src="https://vimeo.com/255317467" playlist="https://vimeo.com/192934117" caption=";Talking about FV Player"]');
    add_user_meta($this->userID, '_fv_player_user_video', '[fvplayer src="https://vimeo.com/255370388"]');
    add_user_meta($this->userID, '_fv_player_user_video', '[fvplayer src="https://www.youtube.com/watch?v=6ZfuNTqbHE8"]]');
    
    $profileuser = get_user_to_edit($this->userID);
    
    ob_start();
    apply_filters( 'show_password_fields', true, $profileuser );
    $output = ob_get_clean();
        
    $one = $this->fix_newlines(file_get_contents(dirname(__FILE__).'/testProfileScreen.html')); // this contains user ID of '4'
    $two = explode("\n",$this->fix_newlines($output));
    foreach( explode("\n",$one) as $k => $v ) {
      
      /*if( $v != $two[$k]) {
        for($i=0;$i<strlen($two[$k]);$i++) {
          var_dump( $two[$k][$i].' '.ord($two[$k][$i]) );
        }
      }*/
      
      //$this->assertEquals( $v, $two[$k] );
    }
    
    $this->assertEquals( $this->fix_newlines(file_get_contents(dirname(__FILE__).'/testProfileScreen.html')), $this->fix_newlines($output) );
        
  }

}
