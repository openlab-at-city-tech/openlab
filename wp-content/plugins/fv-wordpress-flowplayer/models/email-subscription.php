<?php

class FV_Player_Email_Subscription {

  public function __construct() {
    add_action( 'admin_init', array($this, 'init_options') );
  
    add_action( 'admin_init', array($this, 'admin__add_meta_boxes') );
    add_filter( 'fv_flowplayer_popup_html', array($this, 'popup_html') );
    add_filter( 'fv_player_conf_defaults', array($this, 'conf_defaults') );
    add_filter( 'fv_flowplayer_settings_save', array($this, 'fv_flowplayer_settings_save'), 10, 2 );
    add_action( 'wp_ajax_nopriv_fv_wp_flowplayer_email_signup', array($this, 'email_signup') );
    add_action( 'wp_ajax_fv_wp_flowplayer_email_signup', array($this, 'email_signup') );
    add_filter( 'fv_player_admin_popups_defaults', array($this,'fv_player_admin_popups_defaults') );
    
    add_action( 'wp_ajax_fv_player_email_subscription_save', array($this, 'save_settings') );

    if( !empty($_GET['fv-email-export']) && !empty($_GET['page']) && $_GET['page'] === 'fvplayer'){
      add_action('admin_init', array( $this, 'csv_export' ) );
    }

    if( !empty($_GET['fv-email-export-screen']) && !empty($_GET['page']) && $_GET['page'] === 'fvplayer'){
      add_action('in_admin_header',array($this,'admin_export_screen'));
    }
    
    add_filter( 'fv_flowplayer_attributes', array( $this, 'popup_preview' ), 10, 3 );

  }

  /*
  * SETTINGS
  */

  public function conf_defaults($conf) {
    $conf += array(
      'mailchimp_api' => '',
      'mailchimp_list' => '',
      'mailchimp_label' => 'Subscribe for updates',
    );
    return $conf;
  }
  
  public function init_options() {
    if( !get_option('fv_player_email_lists') ) {
      update_option('fv_player_email_lists', array( 1 => array('first_name' => true,
                                                   'last_name' => false,
                                                   'integration' => false,
                                                   'title' => 'Subscribe to list one',
                                                   'description' => 'Two good reasons to subscribe right now',
                                                   'disabled' => false
                                                   ) ) );
    }
  }

  public function admin__add_meta_boxes() {
    add_meta_box('fv_flowplayer_email_lists', __('Email Popups', 'fv-wordpress-flowplayer'), array($this, 'settings_box_lists'), 'fv_flowplayer_settings_actions', 'normal');
    add_meta_box('fv_flowplayer_email_integration', __('Email Integration', 'fv-wordpress-flowplayer'), array($this, 'settings_box_integration'), 'fv_flowplayer_settings_actions', 'normal');
  }

  public function fv_flowplayer_settings_save($param1,$param2){

    if(isset($_POST['email_lists'])){
      $aOptions = array();
      unset($aOptions['#fv_popup_dummy_key#']);

      foreach( $_POST['email_lists'] AS $key => $value ) {
        $key = intval($key);
        $aOptions[$key]['first_name'] = stripslashes($value['first_name']);
        $aOptions[$key]['last_name'] = stripslashes($value['last_name']);
        $aOptions[$key]['integration'] = isset($value['integration']) ? stripslashes($value['integration']) : false;
        $aOptions[$key]['title'] = stripslashes($value['title']);
        $aOptions[$key]['description'] = stripslashes($value['description']);

      }
      update_option('fv_player_email_lists',$aOptions);
    }

    return $param1;
  }

  public function fv_player_admin_popups_defaults($aData){
    $aPopupData = get_option('fv_player_email_lists');
    unset($aPopupData['#fv_list_dummy_key#']);

    if( is_array($aPopupData) ) {
      foreach( $aPopupData AS $key => $aPopupAd ) {
        $aData['email-' . $key] = $aPopupAd;
      }
    }

    return $aData;
  }
  
  public function settings_box_integration () {
    global $fv_fp;
    ?>
    <p><?php _e('Enter your service API key and then assign it to a list which you create above.', 'fv-wordpress-flowplayer'); ?></p>
    <?php if( version_compare(phpversion(),'5.3.0') >= 0 ) : ?>
      <table class="form-table2" style="margin: 5px; ">
        <tr>
          <td style="width: 250px"><label for="mailchimp_api"><?php _e('Mailchimp API key', 'fv-wordpress-flowplayer'); ?>:</label></td>
          <td>
            <p class="description">
              <input type="text" name="mailchimp_api" id="mailchimp_api" value="<?php echo esc_attr($fv_fp->_get_option('mailchimp_api')); ?>" />
            </p>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input type="submit" name="fv-wp-flowplayer-submit" class="button-primary" value="<?php _e('Save All Changes', 'fv-wordpress-flowplayer'); ?>" />
          </td>
        </tr>        
      </table>
    <?php else : ?>
      <p><?php _e('Please upgrade to PHP 5.3 or above to use the Mailchimp integration.', 'fv-wordpress-flowplayer'); ?></p>
    <?php endif;
  }

  public function settings_box_lists () {
    global $fv_fp;
    
    $aListData = get_option('fv_player_email_lists');
    if( empty($aListData) ) {
      $aListData = array( 1 => array() );
    }
    if(!isset($aListData['#fv_list_dummy_key#'])){
      $aListData =  array( '#fv_list_dummy_key#' => array() ) + $aListData ;
    }
    $aMailchimpLists = $this->get_mailchimp_lists();
    ?>
    <p><?php _e('Lists defined here can be used for subscription box for each video or for Default Popup above.', 'fv-wordpress-flowplayer'); ?></p>
    <table class="form-table2" style="margin: 5px; ">
      <tr>
        <td>
          <table id="fv-player-email_lists-settings">
            <thead>
            <tr>
              <td>ID</td>
              <td style="width: 40%"><?php _e('Properties', 'fv-wordpress-flowplayer'); ?></td>
              <?php if( !empty($aMailchimpLists['result']) ) : ?>
                <td><?php _e('Target List', 'fv-wordpress-flowplayer'); ?></td>
              <?php endif; ?>
              <td><?php _e('Export', 'fv-wordpress-flowplayer'); ?></td>
              <td><?php _e('Options', 'fv-wordpress-flowplayer'); ?></td>
              <td><?php _e('Status', 'fv-wordpress-flowplayer'); ?></td>
              <td></td>
            </tr>
            </thead>
            <tbody>
            <?php

            foreach ($aListData AS $key => $aList) {
              $mailchimpOptions = '';

              foreach($aMailchimpLists['result'] as $mailchimpId => $list){
                if(!$list)
                  continue;
                $use = true;
                foreach($list['fields'] as $field){

                  if( $field['required'] && ($field['tag'] === "FNAME" && ( empty($aList['first_name']) || !$aList['first_name'] ) || $field['tag'] === "LNAME" && ( empty($aList['last_name']) || !$aList['last_name'] ) ) ){
                    $use = false;
                    break;
                  }

                }
                if($use){
                  $mailchimpOptions .= '<option value="mailchimp-' . $list['id'] . '" ' . ( isset($aList['integration']) && 'mailchimp-' . $list['id'] === $aList['integration']?"selected":"" ) . '>' . $list['name'] . '</option>';
                }
              }
              
              if( $aMailchimpLists && $mailchimpOptions ) {
                $mailchimp_no_option = 'None';
              } else if( $aMailchimpLists && !$mailchimpOptions ) {
                $mailchimp_no_option = 'No matching list found';
              }

              ?>
              <tr class='data' id="fv-player-list-item-<?php echo $key; ?>"<?php echo $key === '#fv_list_dummy_key#' ? 'style="display:none"' : ''; ?>>
                <td class='id'><?php echo $key ; ?></td>
                <td>
                  <table>
                    <tr>
                      <td style="width:16%"><label>Header</label></td>
                      <td><input type='text' name='email_lists[<?php echo $key; ?>][title]' value='<?php echo isset($aList['title']) ? esc_attr($aList['title']) : ''; ?>' /></td>
                    </tr>
                    <tr>
                      <td><label>Message</label></td>
                      <td><input type='text' name='email_lists[<?php echo $key; ?>][description]' value='<?php echo isset($aList['description']) ? esc_attr($aList['description']) : ''; ?>' /></td>
                    </tr>
                  </table>
                </td>                
                <?php if( !empty($aMailchimpLists['result']) ) : ?>
                  <td>                  
                    <select name="email_lists[<?php echo $key; ?>][integration]" title="E-mail list">
                      <option value=""><?php echo $mailchimp_no_option; ?></option>
                      <?php echo $mailchimpOptions ;?>
                    </select>
                    <br />&nbsp;
                  </td>
                <?php endif; ?>
                <td>
                  <a class='fv-player-list-export' href='<?php echo admin_url('options-general.php?page=fvplayer&fv-email-export='.$key ); ?>' target="_blank" ><?php _e('Download CSV', 'fv-wordpress-flowplayer'); ?></a>
                  <br />
                  <a class='fv-player-list-export' href='<?php echo admin_url('options-general.php?page=fvplayer&fv-email-export-screen='.$key); ?>' target="_blank" ><?php _e('View list', 'fv-wordpress-flowplayer'); ?></a>
                </td>
                <td>
                    <input type='hidden' name='email_lists[<?php echo $key; ?>][first_name]' value='0' />
                    <input id='list-first-name-<?php echo $key; ?>' title="first name" type='checkbox' name='email_lists[<?php echo $key; ?>][first_name]' value='1' <?php echo (isset($aList['first_name']) && $aList['first_name'] ? 'checked="checked"' : ''); ?> />
                    <label for='list-first-name-<?php echo $key; ?>'>First Name</label>
                    <br />
                    <input type='hidden' name='email_lists[<?php echo $key; ?>][last_name]' value='0' />
                    <input id='list-last-name-<?php echo $key; ?>' title="last name" type='checkbox' name='email_lists[<?php echo $key; ?>][last_name]' value='1' <?php echo (isset($aList['last_name']) && $aList['last_name'] ? 'checked="checked"' : ''); ?> />
                    <label for='list-last-name-<?php echo $key; ?>'>Last Name</label>
                </td>
                <td>
                  <input type='hidden' name='email_lists[<?php echo $key; ?>][disabled]' value='0' />
                  <input id='ListAdDisabled-<?php echo $key; ?>' type='checkbox' title="disable" name='email_lists[<?php echo $key; ?>][disabled]' value='1' <?php echo (isset($aList['disabled']) && $aList['disabled'] ? 'checked="checked"' : ''); ?> />
                  <label for='ListAdDisabled-<?php echo $key; ?>'>Disable</label>
                  <br />
                  <a class='fv-player-list-remove' href=''><?php _e('Remove', 'fv-wordpress-flowplayer'); ?></a>
                </td>
                <td>
                  <input type="button" style="visibility: hidden" class="fv_player_email_list_save button" value="Save & Preview" />
                </td>
              </tr>
              <?php
            }
            ?>
            </tbody>
          </table>
        </td>
      </tr>
      <tr>
        <td>          
          <input type="button" value="<?php _e('Add More Lists', 'fv-wordpress-flowplayer'); ?>" class="button" id="fv-player-email_lists-add" />
        </td>
      </tr>
    </table>

    <script>
      jQuery('#fv-player-email_lists-add').click( function() {
        var fv_player_list_index  = (parseInt( jQuery('#fv-player-email_lists-settings tr.data:last .id').html()  ) || 0 ) + 1;
        jQuery('#fv-player-email_lists-settings').append(jQuery('#fv-player-email_lists-settings tr.data:first').prop('outerHTML').replace(/#fv_list_dummy_key#/gi,fv_player_list_index + ""));
        jQuery('#fv-player-list-item-' + fv_player_list_index).show();
        return false;
      } );

      jQuery(document).on('click','.fv-player-list-remove', false, function() {
        if( confirm('Are you sure you want to remove the list?') ){
          jQuery(this).parents('.data').remove();
          if(jQuery('#fv-player-email_lists-settings .data').length === 1) {
            jQuery('#fv-player-email_lists-add').trigger('click');
          }
        }
        return false;
      } );
      
      jQuery(document).on('keydown change', '#fv-player-email_lists-settings', function(e) {
        var row = jQuery(e.target).parents('[id^="fv-player-list-item-"]');
        row.find('.fv_player_email_list_save').css('visibility','visible');
      });
      jQuery(document).on('click', '#fv-player-email_lists-settings input[type=checkbox]', function(e) {
        var row = jQuery(e.target).parents('[id^="fv-player-list-item-"]');
        row.find('.fv_player_email_list_save').css('visibility','visible');
      });
      
      jQuery(document).on('click', '.fv_player_email_list_save', function() {
        var button = jQuery(this);
        var row = button.parents('[id^="fv-player-list-item-"]');
        var aInputs = row.find('input, select');
        var key = row.attr('id').replace(/fv-player-list-item-/,'');
        
        fv_player_open_preview_window(null,720,480);
        
        button.prop('disabled',true);
        jQuery.ajax( {
          type: "POST",
          url: ajaxurl,
          data: aInputs.serialize()+'&key='+key+'&action=fv_player_email_subscription_save&_wpnonce=<?php echo wp_create_nonce('fv_player_email_subscription_save'); ?>',
          success: function(response) {
            button.css('visibility','hidden');
            button.prop('disabled', false);
            
            row.replaceWith( jQuery('#'+row.attr('id'),response) );
            
            var shortcode = '<?php echo '[fvplayer src="https://player.vimeo.com/external/196881410.hd.mp4?s=24645ecff21ff60079fc5b7715a97c00f90c6a18&profile_id=174&oauth2_token_id=3501005" splash="https://i.vimeocdn.com/video/609485450_1280.jpg" preroll="no" postroll="no" subtitles="'.flowplayer::get_plugin_url().'/images/test-subtitles.vtt" end_popup_preview="true" popup="email-#key#" caption="'.__("This is how the popup will appear at the end of a video",'fv-wordpress-flowplayer').'"]'; ?>';
            shortcode = shortcode.replace(/#key#/,key);
            
            var url = '<?php echo home_url(); ?>?fv_player_embed=<?php echo wp_create_nonce( "fv-player-preview-".get_current_user_id() ); ?>&fv_player_preview=' + b64EncodeUnicode(shortcode);
            fv_player_open_preview_window(url);
          },
          error: function() {
            button.val('Error saving!');
          }
        } );
      });
    </script>
    <?php
  }

  /*
   * GENEREATE HTML
   */
  public function popup_html($popup) {
    if ($popup === 'email-no'){
      return '';
    }

    if(strpos($popup,'email-') !== 0)
    {
      return $popup ;
    }

    $id = array_reverse(explode('-',$popup));
    $id = $id[0];
    $aLists = get_option('fv_player_email_lists',array());
    $list = isset($aLists[$id]) ? $aLists[$id] : array('disabled' => '1');
    
    if( empty($list['title']) || isset($list['disabled']) && $list['disabled'] === '1'){
      return '';
    }
    $popupItems = '';
    $count = 1;
    foreach($list as $key => $field){
      if(($key === 'first_name' || $key === 'last_name') && $field == "1"){
        $count++;
        $aName = explode('_',$key);
        foreach($aName as $nameKey => $val){
          $aName[$nameKey] = ucfirst($aName[$nameKey]);
        }

        $sName = implode(' ',$aName);
        $popupItems .= '<input type="text" placeholder="' . $sName . '" name="' . $key . '" required/>';
      }

    }
    $popup = '';
    if( !empty($list['title']) ) $popup .= '<h3>'.$list['title'].'</h3>';
    if( !empty($list['description']) ) $popup .= '<p>'.$list['description'].'</p>';
    $popup .= '<form class="mailchimp-form  mailchimp-form-' . $count . '">'
      . '<input type="hidden" name="list" value="' . $id . '" />'
      . '<input type="email" placeholder="' . __('Email Address', 'fv-wordpress-flowplayer') . '" name="email"/>'
      . $popupItems . '<input type="submit" value="' . __('Subscribe', 'fv-wordpress-flowplayer') . '"/></form>';
    return $popup;
  }

  /*
   * API CALL
   */
  private function get_mailchimp_lists() {
    if(version_compare(phpversion(),'5.3.0','<')){
      return array('error' => 'PHP 5.3 or above required.', 'result' => false);
    }
    
    global $fv_fp;
    $aLists = array();

    if (!$fv_fp->_get_option('mailchimp_api')) {
      update_option('fv_player_mailchimp_lists', $aLists);
      return array('error' => 'No API key found.  ', 'result' => $aLists);
    }

    $aLists = get_option('fv_player_mailchimp_lists', array());
    $sTimeout = !$aLists || count($aLists) == 0 ? 60 : 3600;
    
    if( get_option('fv_player_mailchimp_time', 0 ) + $sTimeout > time() && !isset($_GET['fv_refresh_mailchimp']) ) return array('error' => false, 'result' => $aLists);

    require_once dirname(__FILE__) . '/../includes/mailchimp-api/src/MailChimp.php';
    require_once dirname(__FILE__) . '/email-subscription-mailchimp.php';
    
    $result = fv_player_mailchimp_result();
    $error = fv_player_mailchimp_last_error();
    if ($error || !$result) {
      update_option('fv_player_mailchimp_time', time() - 50 * 60);
      update_option('fv_player_mailchimp_lists', $aLists);
      return array('error' => $error, 'result' => $aLists);
    }
    $aLists  = array();
    foreach ($result['lists'] as $list) {
      $item = array(
        'id' => $list['id'],
        'name' => $list['name'],
        'fields' => array()
      );


      foreach ($list['_links'] as $link) {
        if ($link['rel'] === 'merge-fields') {
          $mergeFields = fv_player_mailchimp_get($list['id']);
          foreach ($mergeFields['merge_fields'] as $field) {
            $item['fields'][] = array(
              'tag' => $field['tag'],
              'name' => $field['name'],
              'required' => $field['required'],
            );
          }
          break;
        }
      }
      $aLists[$list['id']] = $item;
    }

    update_option('fv_player_mailchimp_time', time() );
    update_option('fv_player_mailchimp_lists', $aLists);
    return array('error' => false, 'result' => $aLists);
  }

  private function  mailchimp_signup($list_id, $data){
    global $fv_fp;
    require_once dirname(__FILE__) . '/../includes/mailchimp-api/src/MailChimp.php';
    require_once dirname(__FILE__) . '/email-subscription-mailchimp.php';
    
    $merge_fields = array();

    if(isset($data['first_name'])){
      $merge_fields['FNAME'] = $data['first_name'];
    }

    if(isset($data['last_name'])){
      $merge_fields['LNAME'] = $data['last_name'];
    }

    $result_data = fv_player_mailchimp_post($list_id, $data['email'], $merge_fields);

    $result = array(
      'status' => 'OK',
      'text' => __('Thank You for subscribing.', 'fv-wordpress-flowplayer'),
      'error_log' => false,
    );


    if ($result_data['status'] === 400) {
      if ($result_data['title'] === 'Member Exists') {
        $result = array(
          'status' => 'ERROR',
          'text' => __('Email Address already subscribed.', 'fv-wordpress-flowplayer'),
          'error_log' => $result_data,
        );
      } elseif ($result_data['title'] === 'Invalid Resource') {
        $result = array(
          'status' => 'ERROR',
          'text' => __('Email Address not valid', 'fv-wordpress-flowplayer'),
          'error_log' => $result_data
        );
      } else {
        $result = array(
          'status' => 'ERROR',
          'text' => 'Unknown Error 1. ',
          'error_log'=> $result_data,
        );
      }
    }elseif($result_data['status'] !== 'subscribed'){
      $result = array(
        'status' => 'ERROR',
        'text' => 'Unknown Error 2.',
        'error_log'=> $result_data,
      );
    }
    return $result;
  }

  public function email_signup() {
    $data = $_POST;
    $list_id = isset($data['list']) ? $data['list'] : 0;
    unset($data['list']);
    $aLists = get_option('fv_player_email_lists');

    $list = isset($aLists[$list_id]) ? $aLists[$list_id] : array();

    global $wpdb;
    $table_name = $wpdb->prefix . 'fv_player_emails';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
      $sql = "CREATE TABLE `$table_name` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `email` TEXT NULL,
        `first_name` TEXT NULL,
        `last_name` TEXT NULL,
        `id_list` INT(11) NOT NULL,
        `date` DATETIME NULL DEFAULT NULL,
        `data` TEXT NULL,
        `integration` TEXT NULL,
        `integration_nice` TEXT NULL,
        `status` TEXT NULL,
        `error` TEXT NULL,
        PRIMARY KEY (`id`)
      )" . $wpdb->get_charset_collate() . ";";
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta($sql);
    }

    $result = array(
      'status' => 'OK',
      'text' => __('Thank You for subscribing.', 'fv-wordpress-flowplayer'));

    $integration_nice = '';

    if(!empty($list['integration'])){
      $aLists = get_option('fv_player_mailchimp_lists', array());
      $integration_nice = $aLists[str_replace('mailchimp-','',$list['integration'])]['name'];
      $result = $this->mailchimp_signup(str_replace('mailchimp-','',$list['integration']),$data);
    }
    if(empty($data['email']) || filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)===false){
      $result['status'] = 'ERROR';
      $result['text'] = __('Malformed Email Address.', 'fv-wordpress-flowplayer');
    };

    $count = $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'fv_player_emails WHERE email="' . addslashes($data['email']) . '" AND id_list = "'. addslashes($list_id) .'"' );


    if(intval($count) === 0){
      $wpdb->insert($table_name, array(
        'email' => $data['email'],
        'data' => serialize($data),
        'id_list'=>$list_id,
        'date'=>date("Y-m-d H:i:s"),
        'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
        'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
        'integration' => $list['integration'],
        'integration_nice' => $integration_nice,
        'status' => $result['status'],
        'error' => $result['status'] === 'ERROR' ? serialize( $result['error_log'] ) : '',
      ));
      
    }elseif($result['status'] === 'OK'){
      $result = array(
        'status' => 'ERROR',
        'text' => __('Email Address already subscribed.', 'fv-wordpress-flowplayer'),
      );
      
    }else{
      $wpdb->insert($table_name, array(
        'email' => $data['email'],
        'data' => serialize($data),
        'id_list'=>$list_id,
        'date'=>date("Y-m-d H:i:s"),
        'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
        'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
        'integration' => $list['integration'],
        'integration_nice' => $integration_nice,
        'status' => $result['status'],
        'error' => $result['status'] === 'ERROR' ? serialize( $result['error_log'] ) : '',
      ));
    }

    unset($result['error_log']);
    die(json_encode($result));
  }

  function csv_export(){
    $list_id = $_GET['fv-email-export'];
    $aLists = get_option('fv_player_email_lists');
    $list = $aLists[$list_id];
    $filename = 'export-lists-' . (empty($list->title) ? $list_id : $list->title) . '-' . date('Y-m-d') . '.csv';

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    global $wpdb;
    $results = $wpdb->get_results('SELECT `email`, `first_name`, `last_name`, `date`, `integration`, `integration_nice`, `status`, `error` FROM `' . $wpdb->prefix . 'fv_player_emails` WHERE `id_list` = "' . intval($list_id) . '"');

    echo 'email,first_name,last_name,date,integration,status,error'."\n";
    if( $results ) {
      foreach ($results as $row){
        if(!empty($row->integration)){
          $row->integration .= ': '.$row->integration_nice;
        }
        unset($row->integration_nice);
  
        if(!empty($row->error)){
          $tmp = unserialize($row->error);
          $row->error =  $tmp['title'];
        }
  
  
        echo '"' . implode('","',str_replace('"','',(array)$row)) . "\"\n";
      }
    }
    die;
  }


  public function admin_export_screen(){

    $list_id = $_GET['fv-email-export-screen'];

    global $wpdb;
    $results = $wpdb->get_results('SELECT `email`, `first_name`, `last_name`, `date`, `integration`, `integration_nice`, `status`, `error` FROM `' . $wpdb->prefix . 'fv_player_emails` WHERE `id_list` = "' . intval($list_id) . '" LIMIT 10');

    ?>
    <style>
      #adminmenumain { display: none }
      #wpcontent { margin-left: 0 }
    </style>
    
    <table class="wp-list-table widefat fixed striped posts">
      <thead>
      <tr>
        <th scope="col" class="manage-column">E-mail</th>
        <th scope="col" class="manage-column">First Name</th>
        <th scope="col" class="manage-column">Last Name</th>
        <th scope="col" class="manage-column">Date</th>
        <th scope="col" class="manage-column">Integration</th>
        <th scope="col" class="manage-column">Status</th>
        <th scope="col" class="manage-column">Error</th>
      </tr>
      </thead>
      <tbody>
    <?php
    foreach ($results as $row){
      echo '<tr>';
      foreach ($row as $key => $item) {
        if($key === 'integration' && !empty($item)){
          $item .= ': ' . $row->integration_nice;
        }elseif($key === 'integration_nice'){
          continue;
        }elseif($key === 'error'){
          $item = '';
          if( !empty($item) ) {
            $tmp = unserialize($item);
            $item = $tmp['title'];
          }
        }
        echo '<td>' . $item . '</td>';
      }
      echo '</tr>';
    }
    ?>
      </tbody>
    </table>
    <p>
      <a class='fv-player-list-export button' href='<?php echo admin_url('options-general.php?page=fvplayer&fv-email-export='.intval($list_id));?>' target="_blank" ><?php _e('Download CSV', 'fv-wordpress-flowplayer'); ?></a>
    </p>

  <?php

    die();
  }
  
  
  public function save_settings() {
    check_ajax_referer('fv_player_email_subscription_save');
    
    $aLists = get_option('fv_player_email_lists',array());
    $key = intval($_POST['key']);
    
    if( !isset($_POST['email_lists'][$key]) ) {
      header('HTTP/1.0 403 Forbidden');
      die();
    }
    
    $aLists[$key] = $_POST['email_lists'][$key];
    foreach ($aLists as $index => $values) {
      foreach ($values as $key => $value) {
        $aLists[$index][$key] = stripslashes($value);
      }
    }
    update_option('fv_player_email_lists',$aLists);
    
    fv_player_admin_page();
  }
  
  
  public function popup_preview( $aAttributes ) {
    global $fv_fp;
    $aArgs = func_get_args();
    if( isset($aArgs[2]->aCurArgs['end_popup_preview']) && $aArgs[2]->aCurArgs['end_popup_preview'] ) {
      $aAttributes['data-end_popup_preview'] = true;
    }    
    return $aAttributes;
  }

}

global $FV_Player_Email_Subscription;
$FV_Player_Email_Subscription = new FV_Player_Email_Subscription();
