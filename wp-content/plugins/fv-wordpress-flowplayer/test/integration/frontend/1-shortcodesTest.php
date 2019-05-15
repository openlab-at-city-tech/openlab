<?php

require_once( dirname(__FILE__).'/../fv-player-unittest-case.php');

/**
 * Tests WordPress integration of playlists without any advertisements present
 * in the HTML markup.
 */
final class FV_Player_ShortcodeTestCase extends FV_Player_UnitTestCase {
  
  public function setUp() {
    parent::setUp();

    // create a post with playlist shortcode
    $this->post_id_SimpleShortcode = $this->factory->post->create( array(
      'post_title' => 'Simple Shortcode',
      'post_content' => '[fvplayer src="https://cdn.site.com/video.mp4"]'
    ) );

    /*global $fv_fp;

    include_once "../../../fv-wordpress-flowplayer/models/flowplayer.php";
    include_once "../../../fv-wordpress-flowplayer/models/flowplayer-frontend.php";
    $fv_fp = new flowplayer_frontend();

    include_once "../../beta/fv-player-pro.class.php";
    $this->fvPlayerProInstance = new FV_Player_Pro();*/
  }

  public function testSimpleShortcode() {
    global $post;
    $post = get_post( $this->post_id_SimpleShortcode );
    $post->ID = 1234;
    
    remove_action('wp_head', 'wp_generator');
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    add_filter( 'wp_resource_hints', '__return_empty_array' );    

    wp_deregister_script( 'wp-embed' );
        
    // note that you can only use wp_head() or wp_footer() once!
    ob_start();
    wp_head();
    echo apply_filters( 'the_content', $post->post_content );
    wp_footer();
    $output = ob_get_clean();
    
    $this->assertEquals( $this->fix_newlines(file_get_contents(dirname(__FILE__).'/testSimpleShortcode.html')), $this->fix_newlines($output) );
  }

}
