<?php

class FV_Player_video_intelligence_Installer {

  var $notice = false;
  var $notice_status = false;

  function __construct() {
    add_action( 'admin_menu', array( $this, 'start' ), 8 ) ;
    add_action( 'admin_init', array( $this, 'settings_register' ) ) ;
    add_action( 'admin_notices', array( $this, 'show_notice' ) );
    add_action( 'fv_player_admin_settings_tabs', array( $this, 'settings_tab' ) );
    add_action( 'wp_ajax_fv-player-vi-add', array( $this, 'settings_remove' ) );
    add_action( 'wp_ajax_fv-player-vi-remove', array( $this, 'settings_remove' ) );
  }
  
  function screen_account() {
    global $fv_fp; 
    
    $jwt = $fv_fp->_get_option(array('addon-video-intelligence', 'jwt'));
    wp_nonce_field('fv_player_vi_install','nonce_fv_player_vi_install');
    ?>
        
        <table class="form-table2" style="margin: 5px; ">
          <tbody>
            <?php
            $data = explode( '.', $jwt );
            $data = !empty($data[1]) ? json_decode( base64_decode($data[1]) ) : false;

            if( $jwt && $data && !empty($data->exp) && $data->exp > time() ) : ?>            
              <tr>
                <td class="first"></td>
                <td>
                  <p>We found an existing video intelligence token. Click below to install FV Player video intelligence plugin.</p>
                    <input type="submit" name="fv_player_vi_install" value="<?php _e('Install', 'fv-wordpress-flowplayer'); ?>" class="button-primary">
                    <input type="submit" name="fv_player_vi_reset" value="<?php _e('Reset', 'fv-wordpress-flowplayer'); ?>" class="button">
                </td>
              </tr>
            <?php endif; ?>
            <?php if( !$jwt || empty($data->exp) || $data->exp < time() ) : ?>
              <tr>
                <td class="first"><label for="vi_login"><?php _e('Login', 'fv-wordpress-flowplayer'); ?>:</label></td>
                <td>
                  <p class="description">
                    <input type="text" name="vi_login" id="vi_login" class="medium" />
                  </p>
                </td>
              </tr>
              <tr>
                <td><label for="vi_pass"><?php _e('Password', 'fv-wordpress-flowplayer'); ?>:</label></td>
                <td>
                  <p class="description">
                    <input type="password" name="vi_pass" id="vi_pass" class="medium" />
                  </p>
                </td>
              </tr>
              <tr>
                <td>
                </td>
                <td>
                  <input type="submit" name="fv_player_vi_install" value="<?php _e('Sign in', 'fv-wordpress-flowplayer'); ?>" class="button-primary">
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <p><a href="mailto:support@vi.ai?Subject=Issues%20with%20account%20activation%20for%20<?php echo urlencode(home_url()); ?>">I'm having issues with the account activation</a></p>
                </td>
              </tr>              
            <?php endif; ?>
          </tbody>
        </table>                
        


      <?php
  }  

  function screen_ad() {
    global $fv_fp; 
    $current_user = wp_get_current_user();
    
    if( $fv_fp->_get_option('hide-tab-video-intelligence') && !class_exists('FV_Player_Video_Intelligence') ) : ?>
      <style>
      a[href$=postbox-container-tab_video_intelligence] { display: none }
      #fv_flowplayer_video_intelligence { display: none }
      #fv_flowplayer_video_intelligence_account { display: none }
      #fv_flowplayer_video_intelligence_revival { display: block }
      </style>
    <?php else : ?>
      <style>
      #fv_flowplayer_video_intelligence_revival { display: none }
      </style>
    <?php endif;
    
    $jwt = $fv_fp->_get_option(array('addon-video-intelligence', 'jwt'));
    wp_nonce_field('fv_player_vi_install','nonce_fv_player_vi_install');
    ?>        
        <table class="form-table2" style="margin: 5px; ">
          <tbody>
            <tr>
              <td class="first">
                <img src="<?php echo flowplayer::get_plugin_url(); ?>/images/vi-logo.svg" alt="video intelligence logo" />
            	<a href="https://vi.ai/publisher-video-monetization/?aid=foliovision&email=<?php echo $current_user->user_email; ?>&url=<?php echo home_url(); ?>&invtype=3#publisher_signup" target="_blank" class="button vi-register">Learn More</a>
              </td>
              <td>
                <p>Video content and video advertising – powered by <strong>video intelligence</strong></p>
                <p>Advertisers pay more for video advertising when it's matched with video content. This new video player will insert both on your page. It increases time on site, and commands a higher CPM than display advertising.</p>
                <p>You'll see video content that is matched to your sites keywords straight away. A few days after activation you'll begin to receive revenue from advertising served before this video content.</p>
                <ul>
                  <li>The set up takes only a few minutes</li>
                  <li>Up to 10x higher CPM than traditional display advertising</li>
                  <li>Users spend longer on your site thanks to professional video content</li>
                </ul>                
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                  <p>By clicking sign up you agree to send your current domain, email and affiliate ID to video intelligence.</p>                  
                  <a href="https://vi.ai/publisher-video-monetization/?aid=foliovision&email=<?php echo $current_user->user_email; ?>&url=<?php echo home_url(); ?>&invtype=3#publisher_signup" target="_blank" class="button vi-register">Learn More or Create an Account</a>
              </td>
            </tr>
          </tbody>
        </table>

      <?php
  }
  
  function settings_hide() { 
    ?>
    <input id="fv-player-vi-remove" type="checkbox"> <label for="fv-player-vi-remove"><?php _e('Hide the vi Ads tab', 'fv-wordpress-flowplayer'); ?></label>
    <script>
    jQuery( function($) {
      $('#fv-player-vi-remove').click( function() {
        $.post(ajaxurl, {action:'fv-player-vi-remove'}, function() {
          $('#fv-player-vi-give-back').prop('checked',false);
          $('[href=#postbox-container-tab_video_intelligence]').hide();
          $('#fv_flowplayer_video_intelligence').hide();
          $('[href=#postbox-container-tab_video_ads]').click();
          $('#fv_flowplayer_video_intelligence_revival').show();
        });
        
      });
    });
    </script>
    <?php
  }  

  function settings_register() {
    if( !class_exists('FV_Player_Video_Intelligence') ) {
      add_meta_box( 'fv_flowplayer_video_intelligence', __('video intelligence', 'fv-wordpress-flowplayer'), array( $this, 'screen_ad' ), 'fv_flowplayer_settings_video_intelligence', 'normal' );
      add_meta_box( 'fv_flowplayer_video_intelligence_account', __('Account', 'fv-wordpress-flowplayer'), array( $this, 'screen_account' ), 'fv_flowplayer_settings_video_intelligence', 'normal' );
      add_meta_box( 'fv_flowplayer_video_intelligence_hide', __('Hide vi Ads', 'fv-wordpress-flowplayer'), array( $this, 'settings_hide' ), 'fv_flowplayer_settings_video_intelligence', 'normal' );
      add_meta_box( 'fv_flowplayer_video_intelligence_revival', __('Free video intelligence ads', 'fv-wordpress-flowplayer'), array( $this, 'settings_revival' ), 'fv_flowplayer_settings_video_ads', 'normal', 'low' );
    }
  }
  
  function settings_remove() {
    if( current_user_can('manage_options') ) {
      global $fv_fp;
      $aNew = $fv_fp->conf;
      $aNew['hide-tab-video-intelligence'] = $_POST['action'] == 'fv-player-vi-remove';
      $fv_fp->_set_conf( $aNew );
      die();
    }
  }
  
  function settings_revival() {    
    ?>
    <input id="fv-player-vi-give-back" type="checkbox"> <label for="fv-player-vi-give-back"><?php _e('Show the vi Ads tab again', 'fv-wordpress-flowplayer'); ?></label></a>
    <script>
    jQuery( function($) {
      $('#fv-player-vi-give-back').click( function() {
        $.post(ajaxurl, {action:'fv-player-vi-add'}, function() {
          $('#fv-player-vi-remove').prop('checked',false);
          $('[href=#postbox-container-tab_video_intelligence]').show();
          $('#fv_flowplayer_video_intelligence').show();
          $('#fv_flowplayer_video_intelligence_account').show();
          $('[href=#postbox-container-tab_video_intelligence]').click();
          $('#fv_flowplayer_video_intelligence_revival').hide();
        });
      });
    });
    </script>
    <?php
  }
  
  function settings_tab( $tabs ) {
    $tabs[] = array('id' => 'fv_flowplayer_settings_video_intelligence',	'hash' => 'tab_video_intelligence',	'name' => __('vi Ads', 'fv-player-vi') );
    return $tabs;
  }

  function show_notice() {
    if( $this->notice_status ) {
      echo "<div class='".$this->notice_status."'><p>".$this->notice."</p></div>\n";
    }
  }

  function start() {
    $should_install = false;
    
    if( current_user_can('install_plugins') && !empty($_POST['vi_login']) && !empty($_POST['vi_pass']) && !empty($_POST['fv_player_vi_install']) ) {
      check_admin_referer( 'fv_player_vi_install', 'nonce_fv_player_vi_install' );
      
      remove_action('admin_init', 'fv_player_settings_save', 9);

      $request = wp_remote_get( 'https://dashboard-api.vidint.net/v1/api/widget/settings' );
      if( is_wp_error($request) ) {
        $this->notice_status = 'error';
        $this->notice = "Can't connect to dashboard-api.vidint.net (1)!";
        return;
      }

      $body = wp_remote_retrieve_body( $request );

      $data = json_decode( $body );

      if( !$data || empty($data->data) || empty($data->data->loginAPI) ) {
        $this->notice_status = 'error';
        $this->notice = "Can't parse settings URLs!";
        return;
      }


      $request = wp_remote_post( $data->data->loginAPI, array(
        'headers'   => array('Content-Type' => 'application/json;charset=UTF-8'),
        'body'      => json_encode(array( 'email' => $_POST['vi_login'], 'password' => $_POST['vi_pass'] )),
        'method'    => 'POST'
      ));

      if( is_wp_error($request) ) {
        $this->notice_status = 'error';
        $this->notice = "Can't connect to dashboard-api.vidint.net (2)!";
        return;
      }

      $body = wp_remote_retrieve_body( $request );

      $data = json_decode( $body );

      if( !$data || empty($data->status) || $data->status != 'ok' ) {
        $this->notice_status = 'error';
        $this->notice = 'Error logging in to video intelligence account. Please double check that you have filled in the video intelligence signup form and confirmed the account by clicking the link in confirmation email.';
        return;
      }

      global $fv_fp;
      $aNew = $fv_fp->conf;
      $aNew['addon-video-intelligence'] = array( 'jwt' => $data->data, 'time' => time() );
      $fv_fp->_set_conf( $aNew );

      $this->notice_status = 'updated';
      $this->notice = 'video intelligence login successful!';

      //  attempt plugin auto install!
      $should_install = true;
    }

    else if( current_user_can('install_plugins') && !empty($_REQUEST['fv_player_vi_install']) && wp_verify_nonce( $_REQUEST['nonce_fv_player_vi_install'], 'fv_player_vi_install') ) {
      $should_install = true;
    }
    
    else if( current_user_can('install_plugins') && !empty($_POST['fv_player_vi_reset']) ) {
      check_admin_referer( 'fv_player_vi_install', 'nonce_fv_player_vi_install' );
      global $fv_fp;      
      $fv_fp->conf['addon-video-intelligence'] = array();
      $fv_fp->_set_conf( $fv_fp->conf );
      $this->notice_status = 'updated';
      $this->notice = 'video intelligence login reset!';
    }

    if( $should_install ) {
      $result = FV_Wordpress_Flowplayer_Plugin_Private::install_plugin(
        "FV Player video intelligence",
        "fv-player-video-intelligence",
        "fv-player-video-intelligence.php",
        "https://foliovision.com/plugins/public/fv-player-video-intelligence.zip",
        admin_url('options-general.php?page=fvplayer&fv_player_vi_install=1#postbox-container-tab_video_intelligence'),
        'fv_wordpress_flowplayer_deferred_notices',
        'fv_player_vi_install'
      );
    }
  }
}

new FV_Player_video_intelligence_Installer;
