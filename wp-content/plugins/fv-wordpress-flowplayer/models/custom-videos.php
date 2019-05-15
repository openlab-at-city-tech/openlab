<?php

class FV_Player_Custom_Videos {
  
  var $id;
  
  var $instance_id;
  
  public function __construct( $args ) {
    global $post;
    
    $args = wp_parse_args( $args, array(
                                        'id' => isset($post) && isset($post->ID) ? $post->ID : false,
                                        'meta' => '_fv_player_user_video',
                                        'type' => isset($post->ID) ? 'post' : 'user'
                                        ) );
    
    $this->id = $args['id'];
    $this->meta = $args['meta'];
    $this->type = $args['type'];
  }
  
  private function esc_shortcode( $arg ) {
    $arg = str_replace( array('[',']','"'), array('&#91;','&#93;','&quote;'), $arg );
    return $arg;
  }
  
  public function get_form( $args = array() ) {
    
    $args = wp_parse_args( $args, array( 'wrapper' => 'div', 'edit' => true, 'limit' => 1000, 'no_form' => false ) );
    
    $html = '';
    
    if( $args['wrapper'] != 'li' ) {
      $html .= '<div class="fv-player-custom-video-list">';
    }
    
    if( is_admin() ) {
      global $fv_fp;
      if( $this->have_videos() ) {
        global $FV_Player_Pro;
        if( isset($FV_Player_Pro) && $FV_Player_Pro ) {
          //  todo: there should be a better way than this
          add_filter( 'fv_flowplayer_splash', array( $FV_Player_Pro, 'get__cached_splash' ) );
          add_filter( 'fv_flowplayer_playlist_splash', array( $FV_Player_Pro, 'get__cached_splash' ), 10, 3 );      
          add_filter( 'fv_flowplayer_splash', array( $FV_Player_Pro, 'youtube_splash' ) );
          add_filter( 'fv_flowplayer_playlist_splash', array( $FV_Player_Pro, 'youtube_splash' ), 10, 3 );
      
          add_action('admin_footer', array( $FV_Player_Pro, 'styles' ) );
          add_action('admin_footer', array( $FV_Player_Pro, 'scripts' ) );  //  todo: not just for FV Player Pro
        }
      
        add_action('admin_footer','flowplayer_prepare_scripts');  
      }
      
      add_action('admin_footer', array( $this, 'shortcode_editor_load' ), 0 );    
    }
    
    if( !is_admin() && !$args['no_form'] ) $html .= "<form method='POST'>";
    
    $html .= $this->get_html( $args );
    
    if( !is_admin() ) {
      $html .= wp_nonce_field( 'fv-player-custom-videos-'.$this->meta.'-'.get_current_user_id(), 'fv-player-custom-videos-'.$this->meta.'-'.get_current_user_id(), true, false );
    }
    
    if( !is_admin() && !$args['no_form'] ) {      
      $html .= "<input type='hidden' name='action' value='fv-player-custom-videos-save' />";
      $html .= "<input type='submit' value='Save Videos' />";   
      $html .= "</form>";
    }
    
    if( $args['wrapper'] != 'li' ) {
      $html .= '</div>';
    }
    
    return $html;
  }
  
  public function get_html_part( $video, $edit = false ) {
    global $FV_Player_Custom_Videos_Master, $post;
    $args = !empty($FV_Player_Custom_Videos_Master->aMetaBoxes[$post->post_type]) ? $FV_Player_Custom_Videos_Master->aMetaBoxes[$post->post_type][$this->meta] : array( 'multiple' => true );
    
    //  exp: what matters here is .fv-player-editor-field and .fv-player-editor-button wrapped in  .fv-player-editor-wrapper and .fv-player-editor-preview
    if( $edit ) {
      $add_another = $args['multiple'] ? "<button class='button fv-player-editor-more' style='display:none'>Add Another Video</button>" : false;
      
      $html = "<div class='fv-player-editor-wrapper' data-key='fv-player-editor-field-".$this->meta."'>
          <div class='inside inside-child'>    
            <div class='fv-player-editor-preview'>".($video ? do_shortcode($video) : '')."</div>
            <input class='attachement-shortcode fv-player-editor-field' name='fv_player_videos[".$this->meta."][]' type='hidden' value='".esc_attr($video)."' />
            <div class='edit-video' ".(!$video ? 'style="display:none"' : '').">
              <button class='button fv-player-editor-button'>".$args['labels']['edit']."</button>
              <button class='button fv-player-editor-remove'>".$args['labels']['remove']."</button>
              $add_another
            </div>

            <div class='add-video' ".($video ? 'style="display:none"' : '').">
              <button class='button fv-player-editor-button'>Add Video</button>
            </div>
          </div>
        </div>";
    } else {
      $html = do_shortcode($video);      
    }
    return $html;
  }

  public function get_html( $args = array() ) {
    
    $args = wp_parse_args( $args, array( 'wrapper' => 'div', 'edit' => false, 'limit' => 1000, 'shortcode' => false ) );
    
    $html = '';
    $count = 0;
    if( $this->have_videos() ) {
      
      if( $args['wrapper'] ) $html .= '<'.$args['wrapper'].' class="fv-player-custom-video">';
      
      foreach( $this->get_videos() AS $video ) {
        $count++;
        $html .= $this->get_html_part($video, $args['edit']);
      }
      
      $html .= '<div style="clear: both"></div>'."\n";
      
      if( $args['wrapper'] ) $html .= '</'.$args['wrapper'].'>'."\n";
      
    } else if( $args['edit'] ) {
      $html .= '<'.$args['wrapper'].' class="fv-player-custom-video">';
        $html .= $this->get_html_part(false, true);        
        $html .= '<div style="clear: both"></div>'."\n";      
      $html .= '</'.$args['wrapper'].'>';      
    }
    
    $html .= "<input type='hidden' name='fv-player-custom-videos-entity-id[".$this->meta."]' value='".esc_attr($this->id)."' />";
    $html .= "<input type='hidden' name='fv-player-custom-videos-entity-type[".$this->meta."]' value='".esc_attr($this->type)."' />";

    return $html;
  }
  
  public function get_videos() {
    if( $this->type == 'user' ) {
      $aMeta = get_user_meta( $this->id, $this->meta );      
    } else if( $this->type == 'post' ) {
      $aMeta = get_post_meta( $this->id, $this->meta );
    }
    
    $aVideos = array();
    if( is_array($aMeta) && count($aMeta) > 0 ) {
      foreach( $aMeta AS $aVideo ) {
        if( is_array($aVideo) && isset($aVideo['url']) && isset($aVideo['title']) ) {
          $aVideos[] = '[fvplayer src="'.$this->esc_shortcode($aVideo['url']).'" caption="'.$this->esc_shortcode($aVideo['title']).'"]';
        } else if( is_string($aVideo) && stripos($aVideo,'[fvplayer ') === 0 ) {
          $aVideos[] = $aVideo;
        }
      }
    }
    
    return $aVideos;
  }  
  
  public function have_videos() {
    return count($this->get_videos()) ? true : false;
  }  
  
  function shortcode_editor_load() {
    if( !function_exists('fv_flowplayer_admin_select_popups') ) {
      fv_wp_flowplayer_edit_form_after_editor();
      fv_player_shortcode_editor_scripts_enqueue();   
    }
  }
  
  
}




class FV_Player_Custom_Videos_Master {
  
  var $aMetaBoxes = array();
  
  function __construct() {
    
    add_action( 'init', array( $this, 'save' ) ); //  saving of user profile, both front and back end    
    add_action( 'save_post', array( $this, 'save_post' ) );

    add_filter( 'show_password_fields', array( $this, 'user_profile' ), 10, 2 );
    add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 999, 2 );
    
    add_filter( 'the_content', array( $this, 'show' ) );  //  adding post videos after content automatically
    add_filter( 'get_the_author_description', array( $this, 'show_bio' ), 10, 2 );
    
    //  EDD
    add_action('edd_profile_editor_after_email', array($this, 'EDD_profile_editor'));
    add_action('edd_pre_update_user_profile', array($this, 'save'));
    
    //  bbPress
    add_action( 'bbp_template_after_user_profile', array( $this, 'bbpress_profile' ), 10 );
    add_filter( 'bbp_user_edit_after_about', array( $this, 'bbpress_edit' ), 10, 2 );
  }
  
  function add_meta_boxes() {
    global $post;
    if( !empty($this->aMetaBoxes[$post->post_type]) ) {
      foreach( $this->aMetaBoxes[$post->post_type] AS $meta_key => $args ) {
        global $FV_Player_Custom_Videos_form_instances;
        $id = 'fv_player_custom_videos-field_'.$meta_key;
        $FV_Player_Custom_Videos_form_instances[$id] = new FV_Player_Custom_Videos( array('id' => $post->ID, 'meta' => $args['meta_key'], 'type' => 'post' ) );
        add_meta_box( $id,
                    $args['name'],
                    array( $this, 'meta_box' ),
                    null,
                    'normal',
                    'high'
                    );
      }
    }
    
    //  todo: following code should not add the meta boxes added by the above again!
    
    global $fv_fp;
    if( isset($fv_fp->conf['profile_videos_enable_bio']) && $fv_fp->conf['profile_videos_enable_bio'] == 'true' ) {
      $aMeta = get_post_custom($post->ID);      
      if( $aMeta ) {
        foreach( $aMeta AS $key => $aMetas ) {
          $objVideos = new FV_Player_Custom_Videos( array('id' => $post->ID, 'meta' => $key, 'type' => 'post' ) );
          if( $objVideos->have_videos() ) {
            global $FV_Player_Custom_Videos_form_instances;
            $id = 'fv_player_custom_videos-field_'.$key;
            $FV_Player_Custom_Videos_form_instances[$id] = $objVideos;
            add_meta_box( $id,
                        ucfirst(str_replace( array('_','-'),' ',$key)),
                        array( $this, 'meta_box' ),
                        null,
                        'normal',
                        'high' );
          }
                      
        }
      }
    }
    
  }
  
  function bbpress_edit() {
    ?>
    </fieldset>
    
    <h2 class="entry-title"><?php _e( 'Videos', 'fv-wordpress-flowplayer' ); ?></h2>

    <fieldset class="bbp-form">
      
      <div>
        <?php
        $objVideos = new FV_Player_Custom_Videos(array( 'id' => bbp_get_displayed_user_field('ID'), 'type' => 'user' ));
        echo $objVideos->get_form( array('no_form' => true) );
        ?>
      </div>
  
    <?php
    
    if( !function_exists('is_plugin_active') ) include( ABSPATH . 'wp-admin/includes/plugin.php' );
    if( !function_exists('fv_wp_flowplayer_edit_form_after_editor') ) include( dirname( __FILE__ ) . '/../controller/editor.php' );
    
    fv_wp_flowplayer_edit_form_after_editor();
    fv_player_shortcode_editor_scripts_enqueue();
  }
  
  function bbpress_profile() {
    global $fv_fp;
    
    if( !isset($fv_fp->conf['profile_videos_enable_bio']) || $fv_fp->conf['profile_videos_enable_bio'] !== 'true' ) 
      return;
    
    $objVideos = new FV_Player_Custom_Videos(array( 'id' => bbp_get_displayed_user_field('ID'), 'type' => 'user' ));
    if( $objVideos->have_videos() ) : ?>
      <div id="bbp-user-profile" class="bbp-user-profile">
        <h2 class="entry-title"><?php _e( 'Videos', 'bbpress' ); ?></h2>
        <div class="bbp-user-section">
    
          <?php echo $objVideos->get_html(); ?>
    
        </div>
      </div><!-- #bbp-author-topics-started -->
    <?php endif;
  }
  
  function meta_box( $aPosts, $args ) {
    global $FV_Player_Custom_Videos_form_instances;
    $objVideos = $FV_Player_Custom_Videos_form_instances[$args['id']];
    echo $objVideos->get_form();
  }
  
  function register_metabox( $args ) {
    if( !isset($this->aMetaBoxes[$args['post_type']]) ) $this->aMetaBoxes[$args['post_type']] = array();
    
    $this->aMetaBoxes[$args['post_type']][$args['meta_key']] = $args;    
  }
  
  
  function save() {
    
    if( !isset($_POST['fv_player_videos']) || !isset($_POST['fv-player-custom-videos-entity-type']) || !isset($_POST['fv-player-custom-videos-entity-id']) ) {
      return;
    }
    
    
    
    //  todo: permission check!
    foreach( $_POST['fv_player_videos'] AS $meta => $videos ) {
      if( $_POST['fv-player-custom-videos-entity-type'][$meta] == 'user' ) {
        delete_user_meta( $_POST['fv-player-custom-videos-entity-id'][$meta], $meta );

        foreach( $videos AS $video ) {
          if( strlen($video) == 0 ) continue;
              
          add_user_meta( $_POST['fv-player-custom-videos-entity-id'][$meta], $meta, $video );
        }
      } 
      
    }
    
  }
  
  function save_post( $post_id ) {
    if( !isset($_POST['fv_player_videos']) || !isset($_POST['fv-player-custom-videos-entity-type']) || !isset($_POST['fv-player-custom-videos-entity-id']) ) {
      return;
    }
    
    //  todo: permission check!
    
    foreach( $_POST['fv_player_videos'] AS $meta => $value ) {
      if( $_POST['fv-player-custom-videos-entity-type'][$meta] == 'post' ) {
        delete_post_meta( $post_id, $meta );

        if( is_array($value) && count($value) > 0 ) {
          foreach( $value AS $k => $v ) {            
            if( strlen($v) == 0 ) continue;
            
            add_post_meta( $post_id, $meta, $v );
          }
        }
      } 
      
    }
    
  }
  
  function show( $content ) {
    global $post, $fv_fp;
    if( isset($fv_fp->conf['profile_videos_enable_bio']) && $fv_fp->conf['profile_videos_enable_bio'] == 'true' && isset($post->ID) ) {
      $aMeta = get_post_custom($post->ID);
      if( $aMeta ) {
        foreach( $aMeta AS $key => $aMetas ) {
          if( !empty($this->aMetaBoxes[$post->post_type][$key]) && $this->aMetaBoxes[$post->post_type][$key]['display'] ) {
            $objVideos = new FV_Player_Custom_Videos( array('id' => $post->ID, 'meta' => $key, 'type' => 'post' ) );
            if( $objVideos->have_videos() ) {
              $content .= $objVideos->get_html();
            }
          }
        }
      }
    }
    
    return $content;
  }
  
  function show_bio( $content, $user_id ) {
    global $fv_fp;
    if( !is_single() && isset($fv_fp->conf['profile_videos_enable_bio']) && $fv_fp->conf['profile_videos_enable_bio'] == 'true' ) {
      global $post;    
      $objVideos = new FV_Player_Custom_Videos( array('id' => $user_id, 'type' => 'user' ) );
      $html = $objVideos->get_html( array( 'wrapper' => false, 'shortcode' => array( 'width' => 272, 'height' => 153 ) ) );
      if( $html ) {
        $content .= $html."<div style='clear:both'></div>";
      }
    }
    return $content;
  }  
  
  function user_profile( $show_password_fields, $profileuser ) {
    global $fv_fp;
    if( isset($fv_fp->conf['profile_videos_enable_bio']) && $fv_fp->conf['profile_videos_enable_bio'] == 'true' ) {    
      if( $profileuser->ID > 0 ) {
        $objUploader = new FV_Player_Custom_Videos( array( 'id' => $profileuser->ID ) );
        ?>
        <tr class="user-videos">
          <th><?php _e( 'Videos', 'fv-wordpress-flowplayer' ); ?></th>
          <td>
            <?php
            
            echo $objUploader->get_form( array( 'wrapper' => 'div' ) );
            ?>
            <p class="description"><?php _e( 'You can put your Vimeo or YouTube links here.', 'fv-wordpress-flowplayer' ); ?> <abbr title="<?php _e( 'These show up as a part of the user bio. Licensed users get FV Player Pro which embeds these video types in FV Player interface without Vimeo or YouTube interface showing up.', 'fv-wordpress-flowplayer' ); ?>"><span class="dashicons dashicons-editor-help"></span></abbr></p>
          </td>
        </tr>
        <?php
      }
    }
    
    return $show_password_fields;
  }
  
  public function EDD_profile_editor(){ 
    global $fv_fp;
    
    if( !isset($fv_fp->conf['profile_videos_enable_bio']) || $fv_fp->conf['profile_videos_enable_bio'] !== 'true' ) 
      return;
    
    $user = new FV_Player_Custom_Videos(array( 'id' => get_current_user_id(), 'type' => 'user' ));
    ?>
        <p class="edd-profile-videos-label">
          <span for="edd_email"><?php _e( 'Profile Videos', 'fv-wordpress-flowplayer' ); ?></span>
            <?php echo $user->get_form(array('no_form' => true));?>
        </p>
    <?php
    
    if( !function_exists('is_plugin_active') ) include( ABSPATH . 'wp-admin/includes/plugin.php' );
    if( !function_exists('fv_wp_flowplayer_edit_form_after_editor') ) include( dirname( __FILE__ ) . '/../controller/editor.php' );
    
    fv_wp_flowplayer_edit_form_after_editor();
    fv_player_shortcode_editor_scripts_enqueue();
  }

}


global $FV_Player_Custom_Videos_Master;
$FV_Player_Custom_Videos_Master = new FV_Player_Custom_Videos_Master;




class FV_Player_MetaBox {
  
  function __construct( $args, $meta_key = false, $post_type = false, $display = false ) {
    if( is_string($args) ) {
      $args = array(
                    'name' => $args,
                    'meta_key' => $meta_key,
                    'post_type' => $post_type,
                    'display' => $display
                   );
    }
    
    $args = wp_parse_args( $args, array(
      'display' => false,
      'multiple' => true,
      'labels' => array(
        'edit' => 'Edit Video',
        'remove' => 'Remove Video'
      ) ) );
    
    global $FV_Player_Custom_Videos_Master;
    $FV_Player_Custom_Videos_Master->register_metabox($args);
  }
  
}
