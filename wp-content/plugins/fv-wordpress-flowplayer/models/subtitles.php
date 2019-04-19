<?php

class FV_Player_Subtitles {
  
  public function __construct() {
    add_filter('fv_player_item', array($this, 'add_subtitles'), 10, 3 );
    add_filter('fv_player_db_video_meta_save', array($this, 'parse_post_metadata'), 10, 3);
  }

  /**
   * Method used in WP filter. Receives video meta data array
   * as well as post data to extract subtitles from and returns
   * updated video meta array with subtitles formatted in a way
   * that can be stored in the database.
   *
   * @param array $video_meta     Existing video meta data to merge
   *                              new subtitle meta data into.
   * @param array $meta_post_data Relevant data from $_POST which include
   *                              all subtitle metadata.
   * @param int   $video_index    Index of the video currently being processed,
   *                              so we can retrieve the correct subtitles meta
   *                              data for it.
   *
   * @return array Returns an augmented array of the video meta data,
   *               adding subtitles meta data into it.
   */
  function parse_post_metadata($video_meta, $meta_post_data, $video_index) {
    if (empty($meta_post_data['subtitles'])) {
      // if we have no subtitles or video meta, just return what we received
      return $video_meta;
    }

    // prepare all options for this video
    foreach ( $meta_post_data['subtitles'][$video_index] as $subtitle_values ) {
      if ($subtitle_values['file']) {
        $m = array(
          'meta_key' => 'subtitles' . ($subtitle_values['code'] ? '_'.$subtitle_values['code'] : ''),
          'meta_value' => $subtitle_values['file']
        );

        // add ID, if present
        if (!empty($subtitle_values['id'])) {
          $m['id'] = $subtitle_values['id'];
        }

        $video_meta[] = $m;
      }
    }

    return $video_meta;
  }

  function add_subtitles( $aItem, $index ) {
    global $fv_fp;

    $aSubtitles = $fv_fp->get_subtitles($index);
    if( count($aSubtitles) == 0 ) return $aItem;
        
    $aLangs = flowplayer::get_languages();
    $countSubtitles = 0;
    $aOutput = array();

    foreach( $aSubtitles AS $key => $subtitles ) {
      if( $key == 'iw' ) $key = 'he';
      if( $key == 'in' ) $key = 'id';
      if( $key == 'jw' ) $key = 'jv';
      if( $key == 'mo' ) $key = 'ro';
      if( $key == 'sh' ) $key = 'sr';
      
      $objSubtitle = new stdClass;
      if( $key == 'subtitles' ) {                   
        $aLang = explode('-', get_bloginfo('language'));
        if( !empty($aLang[0]) ) $objSubtitle->srclang = $aLang[0];
        $sCode = $aLang[0];
        
        $sCaption = '';
        if( !empty($sCode) && $sCode == 'en' ) {
          $sCaption = 'English';
        
        } elseif( !empty($sCode) ) {
          $translations = get_site_transient( 'available_translations' );
          $sLangCode = str_replace( '-', '_', get_bloginfo('language') );
          if( $translations && isset($translations[$sLangCode]) && !empty($translations[$sLangCode]['native_name']) ) {
            $sCaption = $translations[$sLangCode]['native_name'];
          }
          
        }
        
        if( $sCaption ) {
          $objSubtitle->label = $sCaption;
        }
        
      } else {
        $objSubtitle->srclang = $key;
        $objSubtitle->label = $aLangs[strtoupper($key)];        
      }
      

      $objSubtitle->src = $subtitles;
      if( $countSubtitles == 0 && $fv_fp->_get_option('subtitleOn') ) {
        $objSubtitle->default = true;
      }      
      $aOutput[] = $objSubtitle;
      
      $countSubtitles++;
    }    
    
    if( count($aSubtitles) ) {
      $aItem['subtitles'] = $aOutput;
    }

    return $aItem;
  }

}

$FV_Player_Subtitles = new FV_Player_Subtitles();
