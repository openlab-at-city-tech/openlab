<?php

class FV_Player_UUP {  
  
  public function __construct() {
    add_action( 'init', array( $this, 'init') );
  }
  
  
  function init() {
    global $xoouserultra;
    if( !isset($xoouserultra) ) return;
    
    global $fv_fp;
    if( isset($fv_fp->conf['profile_videos_enable_bio']) && $fv_fp->conf['profile_videos_enable_bio'] == 'true' ) {      
      add_filter( 'the_content', array( $this, 'account' ), 999 );
      add_filter( 'the_content', array( $this, 'post_editor' ), 999 );    
      add_filter( 'the_content', array( $this, 'profile' ), 999 );
    }
  }
  
  
  function account( $content ) {
    global $post;
    
    if( !isset($post->post_content) || stripos($post->post_content,'[usersultra_my_account') === false || !isset($_GET['module']) || $_GET['module'] != 'videos' ) return $content;

    $objHTML = new DOMDocument();
    libxml_use_internal_errors(true);
    $objHTML->loadHTML($content);
    libxml_use_internal_errors(false);
    
    $objFinder = new DomXPath($objHTML);
    
    $objUploader = new FV_Player_Custom_Videos( array( 'id' => get_current_user_id(), 'type' => 'user' ) );
    
    $aNodes = $objFinder->query("//*[contains(@class, 'add-new-video')]");
    if( $aNodes ) {
      foreach ($aNodes as $objNode) {
          $objParent = $objNode->parentNode;
          while ($objParent->hasChildNodes()){
            $objParent->removeChild($objParent->childNodes->item(0));
          }
          
          $fragment = $objHTML->createDocumentFragment();
          $fragment->appendXML( $objUploader->get_form() );
          $objParent->appendChild( $fragment);          
      }
      
      $content = $objHTML->saveHTML();
    }
    
    return $content;
  }
  
  
  function post_editor( $content ) {
    global $post;
    
    if( !isset($post->post_content) || stripos($post->post_content,'[usersultra_my_account') === false || !isset($_GET['module']) || $_GET['module'] != 'posts' ) return $content;
    
    $args = array( 'meta' => 'videos', 'type' => 'post' );
    if( isset($_GET['act']) && $_GET['act'] == 'edit' && isset($_GET['post_id']) ) $args['id'] = intval($_GET['post_id']);
    
    $objUploader = new FV_Player_Custom_Videos( $args );
    $content = str_replace( '<p>Description:</p>', '<div class="field_row"><p>Post Videos:</p></div>'.$objUploader->get_form( array('limit' => 1, 'no_form' => true) ).'<p>Description:</p>', $content );
    
    return $content;
  }  
  

  function profile( $content ) {
    global $post;
    
    if( !isset($post->post_content) || stripos($post->post_content,'[usersultra_profile') === false ) return $content;
    
    $user_id = get_current_user_id();
    
    global $xoouserultra;
    if( isset($xoouserultra) && isset($xoouserultra->userpanel) && method_exists($xoouserultra->userpanel,'get_user_data_by_uri') ) {
      $current_user = $xoouserultra->userpanel->get_user_data_by_uri();
      
      if( isset($current_user->ID) ) {
        $user_id = $current_user->ID;				
      }
    }

    $objVideos = new FV_Player_Custom_Videos( array( 'id' => $user_id, 'type' => 'user' ) );
    if( !$objVideos->have_videos() ) return $content;
    
    $content = preg_replace( '~(<p class="cat"><a href="\?my_videos">VIDEOS</a></p>\s*?<p class="number")>\d+(</p>)~', '$1>'.count($objVideos->get_videos()).'$2', $content ); //  todo: would be better as a UUP filter 

    if( isset($_GET['my_videos']) ) {
      $objHTML = new DOMDocument();
      libxml_use_internal_errors(true);
      $objHTML->loadHTML($content);
      libxml_use_internal_errors(false);
      
      $objFinder = new DomXPath($objHTML);
      
      $aNodes = $objFinder->query("//*[contains(@class, 'videolist')]/ul");
      if( $aNodes ) {
        foreach ($aNodes as $objNode) {
            $objParent = $objNode->parentNode;
            
            $objChild = $objHTML->createElement('ul');
            $fragment = $objHTML->createDocumentFragment();
            $fragment->appendXML( $objVideos->get_html( array('wrapper' => 'li' ) ) );
            $objChild->appendChild( $fragment);
        
            $objParent->insertBefore($objChild, $objNode);
            $objParent->removeChild($objNode);
        }
        
        $content = $objHTML->saveHTML();
      }
      
    }
    
    return $content;
  }
  

}


$FV_Player_UUP = new FV_Player_UUP();
