<?php
/*  FV Wordpress Flowplayer - HTML5 video player with Flash fallback    
    Copyright (C) 2013  Foliovision

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class FV_Player_Checker {
  
  
  var $is_cron = false;
  
  
  function __construct() {    
    add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) ); 
    add_action( 'fv_flowplayer_checker_event', array( $this, 'checker_cron' ) );
    add_action( 'init', array( $this, 'cron_init' ) );
  }
  

  
  public static function check_headers( $headers, $remotefilename, $random, $args = false ) {
    global $fv_fp;
    
    $args = wp_parse_args( $args, array( 'talk_bad_mime' => 'Video served with a bad mime type' , 'wrap'=>'p' ) );
  
    $sOutput = '';
  
    $video_errors = array();
  
    $bFatal = false;
    if( $headers && $headers['response']['code'] == '404' ) {
      $video_errors[] = 'File not found (HTTP 404)!';
      $bFatal = true;
    } else if( $headers && $headers['response']['code'] == '403' ) {
      $video_errors[] = 'Access to video forbidden (HTTP 403)!';
      $bFatal = true;
    } else if( $headers && $headers['response']['code'] != '200' && $headers['response']['code'] != '206' ) {
      $video_errors[] = 'Can\'t check the video (HTTP '.$headers['response']['code'].')!';
      $bFatal = true;
    } else {  
    
      if(
        ( !isset($headers['headers']['accept-ranges']) || $headers['headers']['accept-ranges'] != 'bytes' ) &&
        !isset($headers['headers']['content-range'])
      ) {
        $video_errors[] = 'Server does not support HTTP range requests! Please check <a href="http://foliovision.com/wordpress/plugins/fv-wordpress-flowplayer/faq#getting-error-about-range-requests">our FAQ</a>.';  
      }
    
      if(
        ( stripos( $remotefilename, '.mp4' ) !== FALSE && $headers['headers']['content-type'] != 'video/mp4' ) ||
        ( stripos( $remotefilename, '.m4v' ) !== FALSE && $headers['headers']['content-type'] != 'video/x-m4v' ) ||
        ( stripos( $remotefilename, '.webm' ) !== FALSE && $headers['headers']['content-type'] != 'video/webm' ) ||			
        ( stripos( $remotefilename, '.mov' ) !== FALSE && $headers['headers']['content-type'] != 'video/mp4' )
      ) {
        if( stripos( $remotefilename, '.mov' ) !== FALSE ) {
          $meta_note_addition = ' Firefox on Windows does not like MOV files with video/quicktime mime type.';
        } else if( stripos( $remotefilename, '.webm' ) !== FALSE ) {
          $meta_note_addition = ' Older Firefox versions don\'t like WEBM files with mime type other than video/webm.';
        } else {
          $meta_note_addition = ' Some web browsers may experience playback issues in HTML5 mode (Internet Explorer 9 - 10).';
          /*if( $fv_fp->conf['engine'] == 'default' ) {
            $meta_note_addition .= ' Currently you are using the "Default (mixed)" <a href="'.site_url().'/wp-admin/options-general.php?page=fvplayer">Preferred Flowplayer engine</a> setting, so IE will always use Flash and will play fine.';
          }*/
        } 
        
        $fix = '<div class="fix-meta-'.$random.'" style="display: none; ">
          <p>If the video is hosted on Amazon S3:</p>
          <blockquote>Using your Amazon AWS Management Console, you can go though your videos and find file content type under the "Metadata" tab in an object\'s "Properties" pane and fix it to "video/mp4" for MP4, "video/x-m4v" for M4V files, "video/mp4" for MOV files and "video/webm" for WEBM files.</blockquote>
          <p>If the video is hosted on your server, put this into your .htaccess:</p>
          <pre>AddType video/mp4             .mp4
    AddType video/webm            .webm
    AddType video/ogg             .ogv
    AddType application/x-mpegurl .m3u8
    AddType video/x-m4v           .m4v
    AddType video/mp4             .mov
    # hls transport stream segments:
    AddType video/mp2t            .ts</pre>
          <p>If you are using Microsoft IIS, you need to use the IIS manager. Check our <a href="http://foliovision.com/wordpress/plugins/fv-wordpress-flowplayer/faq#video-doesnt-play-internet-explorer" target="_blank">FAQ</a> for more info.</p>
        </div>';     
        
        $sOutput = ( $args['wrap'] ) ? '<'.$args['wrap'].'>' : '';
        $sOutput .= '<strong>Bad mime type</strong>: '.$args['talk_bad_mime'].' <tt>'.$headers['headers']['content-type'].'</tt>!'.$meta_note_addition.' (<a href="#" onclick="jQuery(\'.fix-meta-'.$random.'\').toggle(); return false">show fix</a>)';
        $sOutput .= ( $args['wrap'] ) ? '</'.$args['wrap'].'>' : '';
        $sOutput .= $fix;
        $video_errors[] = $sOutput;
      }
    }
  
    return array( $video_errors, (isset($headers['headers']['content-type'])) ? $headers['headers']['content-type'] : '', $bFatal );
  }
  
  
  
  
  public function check_mimetype( $URLs = false, $meta = false, $force_is_cron = false ) {

    add_action( 'http_api_curl', array( 'FV_Player_Checker', 'http_api_curl' ) );
    
    $tStart = microtime(true);
  
    global $fv_wp_flowplayer_ver, $fv_fp;
    
    if( !empty($meta) ) {
      extract( $meta, EXTR_SKIP );
    }
    
    if( defined('DOING_AJAX') && DOING_AJAX && isset( $_POST['media'] ) && stripos( $_SERVER['HTTP_REFERER'], home_url() ) === 0 ) {    
      $URLs = json_decode( stripslashes( trim($_POST['media']) ));
    }

    if( isset($URLs) ) {
      $all_sources = $URLs;

      foreach( $all_sources AS $source ) {
        if( preg_match( '!^rtmp://!', $source, $match ) ) {
          $found_rtmp = true;
        } else if( !isset($media) && !preg_match( '!\.(m3u8ALLOW|m3uALLOW|avi)$!', $source) ) {
          $media = $source;
        }
      }    
              
      //$random = rand( 0, 10000 );
      $random = (isset($_POST['hash'])) ? trim($_POST['hash']) : false;
      if( isset($media) ) {
        $remotefilename = $media;
        $remotefilename_encoded = flowplayer::get_encoded_url($remotefilename);

        $bValidFile = true;
        
        if ( ! class_exists( 'getID3' ) ) {
          require( ABSPATH . WPINC . '/ID3/getid3.php' );
        }    
        $getID3 = new getID3;     
        
        if( function_exists('curl_init') ) {
          $message = '<p>Analysis of <a class="bluelink" target="_blank" href="'.esc_attr($remotefilename_encoded).'">'.$remotefilename_encoded.'</a></p>';
  
          //	taken from: http://www.getid3.org/phpBB3/viewtopic.php?f=3&t=1141
          $upload_dir = wp_upload_dir();      
          $localtempfilename = trailingslashit( $upload_dir['basedir'] ).'fv_flowlayer_tmp_'.md5(rand(1,999)).'_'.basename( substr($remotefilename_encoded,0,32) );
  
          $out = @fopen( $localtempfilename,'wb' );
       
          if( $out ) {
            $aArgs = array( 'file' => $out );
            if( !$this->is_cron && !$force_is_cron ) {
              $aArgs['quick_check'] = apply_filters( 'fv_flowplayer_checker_timeout_quick', 2 );
            }
            list( $header, $sHTTPError ) = $this->http_request( $remotefilename_encoded, $aArgs );
            
            if( $sHTTPError ) {
              $bValidFile = false;
            }
            fclose($out);

            $headers = WP_Http::processHeaders( $header );			

            list( $aVideoErrors, $sContentType, $bFatal ) = $this->check_headers( $headers, $remotefilename, $random );
            if( $bFatal ) {
              $bValidFile = false;
            }
  
            if( $bValidFile ) {
              $ThisFileInfo = $getID3->analyze( $localtempfilename );
            }                        
          } 
          
          foreach( glob( trailingslashit($upload_dir['basedir']).'fv_flowlayer_tmp_*' ) AS $file ) {
            @unlink($file);
          }
        }
  
        
        /*
        Only check file length
        */
        
        if( (isset($meta_action) && $meta_action == 'check_time') || $force_is_cron ) {
          $time = false;
          if( isset($ThisFileInfo) && isset($ThisFileInfo['playtime_seconds']) ) {
            $time = $ThisFileInfo['playtime_seconds'];    	
          }
          
          if(preg_match('/.m3u8(\?.*)?$/i', $remotefilename_encoded)){
            
            remove_action( 'http_api_curl', array( 'FV_Player_Checker', 'http_api_curl' ) );
            
            $request = wp_remote_get($remotefilename_encoded);
            $response = wp_remote_retrieve_body( $request );
  
            $playlist = false;
            $duration = 0;
            $segments = false;
  
            if(!preg_match_all('/^[^#].*\.m3u8(\?.*)?$/im', $response,$playlist)){
              if(preg_match_all('/^#EXTINF:([0-9]+\.?[0-9]*)/im', $response,$segments)){
                foreach($segments[1] as $segment_item){
                  $duration += $segment_item;
                }  
              }
            }else{
              foreach($playlist[0] as $item){
                $item_url = preg_replace('/[^\/]*\.m3u8(\?.*)?/i', $item, $remotefilename_encoded);
                $request = wp_remote_get($item_url);
                $playlist_item = wp_remote_retrieve_body( $request );
                if(preg_match_all('/^#EXTINF:([0-9]+\.?[0-9]*)/im', $playlist_item,$segments)){
                  foreach($segments[1] as $segment_item){
                    $duration += $segment_item;
                  }  
                }
                if($duration > 0)
                  break;
              }
            }
  
            $time = $duration;
          }
          
          $time = apply_filters( 'fv_flowplayer_checker_time', $time, $remotefilename_encoded );
          $key = flowplayer::get_video_key($remotefilename_encoded);
          
          global $post;
          $fv_flowplayer_meta = array();
          if( !empty($post) ) {
            $fv_flowplayer_meta = get_post_meta( $post->ID, $key, true );
            if( !$fv_flowplayer_meta ) $fv_flowplayer_meta = array();
          }
         
          $fv_flowplayer_meta['duration'] = $time;
          $fv_flowplayer_meta['etag'] = isset($headers['headers']['etag']) ? $headers['headers']['etag'] : false;  //  todo: check!
          $fv_flowplayer_meta['date'] = time();
          $fv_flowplayer_meta['check_time'] = microtime(true) - $tStart;
  
          if( $time > 0 || $this->is_cron ) {
            if( !empty($post) ) {
              update_post_meta( $post->ID, $key, $fv_flowplayer_meta );
            }
            return $fv_flowplayer_meta;
          }
          
        }
        
      }	//	end isset($media) 
    }
  }
  
  
  
  
  function checker_cron() {

    // get all video IDs for which there is no duration meta_key    
    global $wpdb, $fv_fp;
    $aVideos = $wpdb->get_results( "SELECT id, src FROM `{$wpdb->prefix}fv_player_videos` as v left join ( select id_video from {$wpdb->prefix}fv_player_videometa WHERE meta_key = 'duration' ) as m ON v.id = m.id_video where m.id_video IS NULL ORDER BY id DESC" );
    
    if( $aVideos ) {
      foreach( $aVideos AS $objVideo ) {
        $id = $objVideo->id;
        $url = $objVideo->src;
        
        global $FV_Player_Db;
        $objVideo = new FV_Player_Db_Video( $id, array(), $FV_Player_Db );
        $last_check = $objVideo->getMetaValue('last_video_meta_check',true);
        
        if( $last_check && intval($last_check) + 3600 > time() ) {
          continue;
        }
        
        $meta_data = apply_filters('fv_player_meta_data', $url, false);
        if( $meta_data == false) {
          if( $secured_url = $fv_fp->get_video_src( $url, array( 'dynamic' => true ) ) ) {
            $url = $secured_url;
          }
          
          $meta_data['duration'] = $this->check_mimetype(array($url), false, true);
          $meta_data['duration'] = $meta_data['duration']['duration'];
          
        }

        if( !empty($meta_data['thumbnail']) ) {
          if( !$objVideo->getSplash() || $objVideo->getMetaValue('auto_splash',true) ) {
            $video_object = new FV_Player_Db_Video( $objVideo->getId(), array(), $FV_Player_Db );
            $video_object->link2db( $objVideo->getId() );
            $video_object->set( 'splash', $meta_data['thumbnail'] );
            $video_object->save();
          }
        }
        
        $objVideo->updateMetaValue('last_video_meta_check', time());
        
        if( $meta_data['duration'] ) {
          $objVideo->updateMetaValue( 'duration', $meta_data['duration'] );         
        }
        
      }
    }
    
    // legacy    
    if( !$aQueue = self::queue_get() ) return;
    $tStart = microtime(true);
    $this->is_cron = true;
    foreach( $aQueue AS $key => $item ) {
      if( microtime(true) - $tStart > apply_filters( 'fv_flowplayer_checker_cron_time', 20 ) ) {
        break;
      }
      global $post;
      $tmp = $post;
      $post = get_post($key);
      
      do_action( 'fv_flowplayer_checker_cron_post', $key );
      
      fv_wp_flowplayer_save_post($key);     
      $post = $tmp;
    }
    
  }
  
  
  
  
  function cron_init() {
    global $fv_fp;      
    if( isset($fv_fp->conf['db_duration']) && $fv_fp->conf['db_duration'] == 'true' ) {
      if ( !wp_next_scheduled( 'fv_flowplayer_checker_event' ) ) {
        wp_schedule_event( time(), '5minutes', 'fv_flowplayer_checker_event' );
      }
    } else if( wp_next_scheduled( 'fv_flowplayer_checker_event' ) ) {
      wp_clear_scheduled_hook( 'fv_flowplayer_checker_event' );
    }
  }

  
  
  
  function cron_schedules( $schedules ) {
    $schedules['5minutes'] = array(
      'interval' => 300,
      'display' => __('Every 5 minutes')
    );
    return $schedules;
  }
  
  
  
  
  public static function get_videos( $post_id ) {
    global $fv_fp;
    
    $objPost = get_post($post_id);
    if( $objPost ) {
      $content = $objPost->post_content;
      preg_match_all( '~\[(?:flowplayer|fvplayer).*?\]~', $content, $matches );
      
      $aMeta = get_post_custom($post_id);
      if( $aMeta && is_array($aMeta) && count($aMeta) > 0) {
        $meta_values = '';
        foreach( $aMeta AS $values ) {
          $meta_values .= implode( $values );
        }
        if( preg_match_all( '~\[(?:flowplayer|fvplayer).*?\]~', $meta_values, $meta_matches ) ) {
          $matches[0] = array_merge($matches[0], $meta_matches[0]);
        }
      }
      
    }
    
    $videos = array();
    if( isset($matches[0]) && count($matches[0]) ) {
      $aPlaylistItems = array();
      foreach( $matches[0] AS $shortcode ) {
        $aArgs = shortcode_parse_atts( rtrim($shortcode,']') );
        list( $playlist_items_external_html, $aPlaylistItems ) = $fv_fp->build_playlist( $aArgs, isset($aArgs['src']) ? $aArgs['src'] : false, false, false, false, false, true );
        
        if( count($aPlaylistItems) > 0 ) {
          foreach( $aPlaylistItems AS $aItem ) {
            if( isset($aItem['sources']) && isset($aItem['sources'][0]) && isset($aItem['sources'][0]['src']) ) {              
              $videos[] = $aItem['sources'][0]['src'];
            }
          }
        }      
      }
    }

    if( count($videos) > 0 ) {
      $videos = array_unique($videos);
    } else {
      $videos = false;
    }
    return $videos;
  }
  
  
  

  public static function http_api_curl( $handle ) {
    curl_setopt( $handle, CURLOPT_NOBODY, true );   //	don't include body in our wp_remote_head requests. We have to use GET instead of HEAD because of Amazon
  }
  
  
  
  
  public static function http_request( $sURL, $args ) {
    global $fv_wp_flowplayer_ver;
    
    $args = wp_parse_args( $args, array( 'file' => false, 'size' => 4194304, 'quick_check' => false ) );
    extract($args);
    
    $iTimeout = ($quick_check) ? $quick_check : 20;

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $sURL );    		
    curl_setopt( $ch, CURLOPT_RANGE, '0-'.$size );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    if( !@ini_get('open_basedir') ) {
      @curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    }
    curl_setopt( $ch, CURLOPT_HEADER, true );
    curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $iTimeout );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $iTimeout );
    curl_setopt( $ch, CURLOPT_USERAGENT, 'FV Flowplayer video checker/'.$fv_wp_flowplayer_ver);
    
    $data = curl_exec($ch);
      
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($data, 0, $header_size);
    $body = substr($data, $header_size);
  
    if ($file) {
      $size = strlen($body);
      for ($written = 0; $written < $size; $written += $fwrite) {
        $fwrite = fwrite($file, substr($body, $written ,1024*512));
        if ($fwrite == 0) {
          break;
        }
      }
    }
    $sError = ($ch == false) ? 'CURL Error: '.curl_error ( $ch) : false;
    if( curl_errno($ch) == 28 ) {
      $sError .= "Connection timeout, can't check the video.";
    } else if(!curl_errno($ch) ) {
      $aInfo = curl_getinfo($ch);
      if( $aInfo['total_time'] > $iTimeout*0.9 ) {
        $sError .= "Connection timeout, can't check the video.";
      }
    }
    curl_close($ch);
  
    return array( $header, $sError );
  }
  
  
  
  
  public static function queue_add( $post_id ) {
    $aQueue = get_option( 'fv_flowplayer_checker_queue' ) ? get_option( 'fv_flowplayer_checker_queue' ) : array();
    $aQueue[$post_id] = true;
    update_option( 'fv_flowplayer_checker_queue', $aQueue );
  }
  
  
  
  
  public static function queue_add_all() {
    global $wpdb;
    if( $aPosts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_content LIKE '%[fvplayer%' ORDER BY post_date DESC" ) ) {
      $aQueue = array();
      foreach( $aPosts AS $iPostId ) {
        $aQueue[$iPostId] = true;
      }
      update_option( 'fv_flowplayer_checker_queue', $aQueue );
    }
    
  }
  
  
  
  
  public static function queue_check( $post_id = false ) {
    global $post;
    $post_id = ( isset($post->ID) ) ? $post->ID : $post_id;
    $aQueue = get_option( 'fv_flowplayer_checker_queue' ) ? get_option( 'fv_flowplayer_checker_queue' ) : array();
    if( in_array($post_id,array_keys($aQueue)) ) {
      return true;
    }
    return false;
  }  
  
  
  
  
  public static function queue_get() {
    return get_option( 'fv_flowplayer_checker_queue' );
  }
  
  
  
  
  public static function queue_remove( $post_id ) {
    $aQueue = get_option( 'fv_flowplayer_checker_queue' ) ? get_option( 'fv_flowplayer_checker_queue' ) : array();
    if( isset($aQueue[$post_id]) ) {
      unset($aQueue[$post_id]);
    }
    update_option( 'fv_flowplayer_checker_queue', $aQueue );
  }    
  
  
  
  
}
