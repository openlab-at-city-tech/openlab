<?php
$page = isset($_GET['page']) ? '?page='.$_GET['page'] : '';
$location = network_admin_url('admin.php'.$page);
$admin_nonce = wp_create_nonce('outofthebox-admin-action');
$network_wide_authorization = $this->get_processor()->is_network_authorized();
?>

<div class="outofthebox admin-settings">
  <form id="outofthebox-options" method="post" action="<?php echo network_admin_url('edit.php?action='.$this->plugin_network_options_key); ?>">
    <?php settings_fields('out_of_the_box_settings'); ?>
    <input type="hidden" name="out_of_the_box_settings[dropbox_root_namespace_id]" id="dropbox_root_namespace_id" value="<?php echo @esc_attr($this->settings['dropbox_root_namespace_id']); ?>" >
    <input type="hidden" name="out_of_the_box_settings[dropbox_account_type]" id="dropbox_account_type" value="<?php echo @esc_attr($this->settings['dropbox_account_type']); ?>" >

    <div class="wrap">
      <div class="outofthebox-header">
                <div class="outofthebox-logo"><a href="https://www.wpcloudplugins.com" target="_blank"><img src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/wpcp-logo-dark.svg" height="64" width="64"/></a></div>
        <div class="outofthebox-form-buttons" style="<?php echo (false === is_plugin_active_for_network(OUTOFTHEBOX_SLUG)) ? 'display:none;' : ''; ?>"> <div id="save_settings" class="simple-button default save_settings" name="save_settings"><?php _e('Save Settings', 'wpcloudplugins'); ?>&nbsp;<div class='oftb-spinner'></div></div></div>
        <div class="outofthebox-title"><?php _e('Settings', 'wpcloudplugins'); ?></div>
      </div>


      <div id="" class="outofthebox-panel outofthebox-panel-left">      
                        <div class="outofthebox-nav-header"><?php _e('Settings', 'wpcloudplugins'); ?> <a href="<?php echo admin_url('update-core.php'); ?>">(Ver: <?php echo OUTOFTHEBOX_VERSION; ?>)</a></div>

        <ul class="outofthebox-nav-tabs">
          <li id="settings_general_tab" data-tab="settings_general" class="current"><a ><?php _e('General', 'wpcloudplugins'); ?></a></li>
          <?php if ($network_wide_authorization) { ?>
              <li id="settings_advanced_tab" data-tab="settings_advanced" ><a ><?php _e('Advanced', 'wpcloudplugins'); ?></a></li>
          <?php } ?>
          <li id="settings_system_tab" data-tab="settings_system" ><a><?php _e('System information', 'wpcloudplugins'); ?></a></li>
          <li id="settings_help_tab" data-tab="settings_help" ><a><?php _e('Support', 'wpcloudplugins'); ?></a></li>
        </ul>

        <div class="outofthebox-nav-header" style="margin-top: 50px;"><?php _e('Other Cloud Plugins', 'wpcloudplugins'); ?></div>
        <ul class="outofthebox-nav-tabs">
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/L6yXj" target="_blank" style="color:#522058;">Google Drive <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/yDbyv" target="_blank" style="color:#522058;">OneDrive <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/M4B53" target="_blank" style="color:#522058;">Box <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
        </ul> 

                            <div class="outofthebox-nav-footer">
          <a href="https://www.wpcloudplugins.com/" target="_blank">
            <img alt="" height="auto" src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/wpcloudplugins-logo-dark.png">
          </a>
        </div>
      </div>

      <div class="outofthebox-panel outofthebox-panel-right">

        <!-- General Tab -->
        <div id="settings_general" class="outofthebox-tab-panel current">

          <div class="outofthebox-tab-panel-header"><?php _e('General', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-option-title"><?php _e('Plugin License', 'wpcloudplugins'); ?></div>
          <?php
          echo $this->get_plugin_activated_box();
          ?>

          <?php if (is_plugin_active_for_network(OUTOFTHEBOX_SLUG)) { ?>
              <div class="outofthebox-option-title"><?php _e('Network Wide Authorization', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[network_wide]'/>
                  <input type="checkbox" name="out_of_the_box_settings[network_wide]" id="network_wide" class="outofthebox-onoffswitch-checkbox" <?php echo (empty($network_wide_authorization)) ? '' : 'checked="checked"'; ?> data-div-toggle="network_wide"/>
                  <label class="outofthebox-onoffswitch-label" for="network_wide"></label>
                </div>
              </div>


              <?php
              if ($network_wide_authorization) {
                  ?>
                  <div class="outofthebox-option-title"><?php _e('Accounts', 'wpcloudplugins'); ?></div>
                  <div class="outofthebox-accounts-list">
                    <?php
                    $app = $this->get_app(); ?>
                    <div class='account account-new'>
                      <img class='account-image' src='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/dropbox_logo.png'/>
                      <div class='account-info-container'>
                        <div class='account-info'>
                          <div class='account-actions'>
                            <div id='add_dropbox_button' type='button' class='simple-button blue' data-url="<?php echo $app->get_auth_url(['force_reapprove' => 'true']); ?>" title="<?php _e('Add account', 'wpcloudplugins'); ?>"><i class='fas fa-plus-circle' aria-hidden='true'></i>&nbsp;<?php _e('Add account', 'wpcloudplugins'); ?></div>
                          </div>
                          <div class="account-info-name">
                            <?php _e('Add account', 'wpcloudplugins'); ?>
                          </div>
                          <span class="account-info-space"><?php _e('Link a new account to the plugin', 'wpcloudplugins'); ?></span>
                        </div>
                      </div>
                    </div>
                    <?php
                    foreach ($this->get_main()->get_accounts()->list_accounts() as $account_id => $account) {
                        echo $this->get_plugin_authorization_box($account);
                    } ?>
                  </div>
                  <?php
              }
              ?>

              <?php
          }
          ?>

        </div>
        <!-- End General Tab -->


        <!--  Advanced Tab -->
        <?php if ($network_wide_authorization) { ?>

            <input type="hidden" name="out_of_the_box_settings[dropbox_app_token]" id="dropbox_app_token" value="<?php echo @esc_attr($this->settings['dropbox_app_token']); ?>" >

            <div id="settings_advanced"  class="outofthebox-tab-panel">
              <div class="outofthebox-tab-panel-header"><?php _e('Advanced', 'wpcloudplugins'); ?></div>

              <div class="outofthebox-option-title"><?php _e('"Lost Authorization" notification', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php _e('If the plugin somehow loses its authorization, a notification email will be send to the following email address', 'wpcloudplugins'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[lostauthorization_notification]" id="lostauthorization_notification" value="<?php echo esc_attr($this->settings['lostauthorization_notification']); ?>">

              <div class="outofthebox-option-title"><?php _e('Own App', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[dropbox_app_own]'/>
                  <input type="checkbox" name="out_of_the_box_settings[dropbox_app_own]" id="dropbox_app_own" class="outofthebox-onoffswitch-checkbox" <?php echo (empty($this->settings['dropbox_app_key']) || empty($this->settings['dropbox_app_secret'])) ? '' : 'checked="checked"'; ?> data-div-toggle="own-app"/>
                  <label class="outofthebox-onoffswitch-label" for="dropbox_app_own"></label>
                </div>
              </div>

              <div class="outofthebox-suboptions own-app <?php echo (empty($this->settings['dropbox_app_key']) || empty($this->settings['dropbox_app_secret'])) ? 'hidden' : ''; ?> ">
                <div class="outofthebox-option-description">
                  <strong>Using your own Dropbox App is <u>optional</u></strong>. For an easy setup you can just use the default App of the plugin itself by leaving the Key and Secret empty. The advantage of using your own app is limited. If you decided to create your own Dropbox App anyway, please enter your settings. In the <a href="http://goo.gl/dsT71e" target="_blank">documentation</a> you can find how you can create a Dropbox App.
                  <br/><br/>
                  <div class="oftb-warning">
                    <i><strong>NOTICE</strong>: If you encounter any issues when trying to use your own App, please fall back on the default App by disabling this setting.</i>
                  </div>
                </div>

                <div class="outofthebox-option-title">Dropbox App Key</div>
                <div class="outofthebox-option-description"><?php _e('<strong>Only</strong> if you want to use your own App, insert your App Key here', 'wpcloudplugins'); ?>.</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[dropbox_app_key]" id="dropbox_app_key" value="<?php echo esc_attr($this->settings['dropbox_app_key']); ?>" placeholder="<--- <?php _e('Leave empty for easy setup', 'wpcloudplugins'); ?> --->" >

                <div class="outofthebox-option-title">Dropbox App Secret</div>
                <div class="outofthebox-option-description"><?php _e('If you want to use your own App, insert your App Secret here', 'wpcloudplugins'); ?>.</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[dropbox_app_secret]" id="dropbox_app_secret" value="<?php echo esc_attr($this->settings['dropbox_app_secret']); ?>" placeholder="<--- <?php _e('Leave empty for easy setup', 'wpcloudplugins'); ?> --->" >   

                <div>
                  <div class="outofthebox-option-title">OAuth 2.0 Redirect URI</div>
                  <div class="outofthebox-option-description"><?php _e('Set the redirect URI in your application to the following', 'wpcloudplugins'); ?>:</div>
                  <code style="user-select:initial">
                    <?php
                    if ($this->get_app()->has_plugin_own_app()) {
                        echo $this->get_app()->get_redirect_uri();
                    } else {
                        _e('Enter Client Key and Secret, save settings and reload the page to see the Redirect URI you will need', 'wpcloudplugins');
                    }
                    ?>
                  </code>
                </div>

                <div class="outofthebox-option-title"><?php _e('Use App Folder', 'wpcloudplugins'); ?>
                  <div class="outofthebox-onoffswitch">
                    <input type='hidden' value='No' name='out_of_the_box_settings[use_app_folder]'/>
                    <input type="checkbox" name="out_of_the_box_settings[use_app_folder]" id="use_app_folder" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['use_app_folder']) ? 'checked="checked"' : ''; ?>/>
                    <label class="outofthebox-onoffswitch-label" for="use_app_folder"></label>
                  </div>
                </div>
                <div class="outofthebox-option-description"><?php _e('Is your App configured to only access the App specific folder', 'wpcloudplugins'); ?>?</div>

              </div>

              <?php
              //if (!empty($account_type) && $account_type === 'business') {
              ?>

              <div class="outofthebox-option-title"><?php _e('Business Accounts', 'wpcloudplugins'); ?> | <?php _e('Dropbox Team Folders', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[use_team_folders]'/>
                  <input type="checkbox" name="out_of_the_box_settings[use_team_folders]" id="use_team_folders" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['use_team_folders']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="use_team_folders"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php _e('Allows you to access your Dropbox Team Folders', 'wpcloudplugins'); ?>.</div>

              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: <?php _e('Please check your existing Shortcodes and Manually linked Private Folders when changing this setting. Your root folder will not longer be your Personal Folder when Team Folders are enabled', 'wpcloudplugins'); ?>.</i>
              </div>

              <?php
              //}
              ?>

            </div>
        <?php } ?>
        <!-- End Advanced Tab -->

        <!-- System info Tab -->
        <div id="settings_system"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('System information', 'wpcloudplugins'); ?></div>
          <?php echo $this->get_system_information(); ?>
        </div>
        <!-- End System info -->

        <!-- Help Tab -->
        <div id="settings_help"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('Support', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-option-title"><?php _e('Support & Documentation', 'wpcloudplugins'); ?></div>
          <div id="message">
            <p><?php _e('Check the documentation of the plugin in case you encounter any problems or are looking for support.', 'wpcloudplugins'); ?></p>
            <div id='documentation_button' type='button' class='simple-button blue'><?php _e('Open Documentation', 'wpcloudplugins'); ?></div>
          </div>
          <br/>
          <div class="outofthebox-option-title"><?php _e('Cache', 'wpcloudplugins'); ?></div>
          <?php echo $this->get_plugin_reset_cache_box(); ?>

        </div>  
      </div>
      <!-- End Help info -->
    </div>
  </form>
  <script type="text/javascript" >
      jQuery(document).ready(function ($) {

        $('#add_dropbox_button, .refresh_dropbox_button').click(function () {
          var $button = $(this);
          $button.addClass('disabled');
          $button.find('.oftb-spinner').fadeIn();
          $('#authorize_dropbox_options').fadeIn();
          popup = window.open($(this).attr('data-url'), "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,width=600,height=500");

          var i = sessionStorage.length;
          while (i--) {
            var key = sessionStorage.key(i);
            if (/CloudPlugin/.test(key)) {
              sessionStorage.removeItem(key);
            }
          }

        });

        $('.revoke_dropbox_button, .delete_dropbox_button').click(function () {
          $(this).addClass('disabled');
          $(this).find('.oftb-spinner').show();
          $.ajax({type: "POST",
            url: '<?php echo OUTOFTHEBOX_ADMIN_URL; ?>',
            data: {
              action: 'outofthebox-revoke',
              account_id: $(this).attr('data-account-id'),
              force: $(this).attr('data-force'),
              _ajax_nonce: '<?php echo $admin_nonce; ?>'
            },
            complete: function (response) {
              location.reload(true)
            },
            dataType: 'json'
          });
        });

        $('#resetDropbox_button').click(function () {
          var $button = $(this);
          $button.addClass('disabled');
          $button.find('.oftb-spinner').show();
          $.ajax({type: "POST",
            url: '<?php echo OUTOFTHEBOX_ADMIN_URL; ?>',
            data: {
              action: 'outofthebox-reset-cache',
              _ajax_nonce: '<?php echo $admin_nonce; ?>'
            },
            complete: function (response) {
              $button.removeClass('disabled');
              $button.find('.oftb-spinner').hide();
            },
            dataType: 'json'
          });

          var i = sessionStorage.length;
          while (i--) {
            var key = sessionStorage.key(i);
            if (/CloudPlugin/.test(key)) {
              sessionStorage.removeItem(key);
            }
          }

        });

        $('#updater_button').click(function () {

          if ($('#purcasecode.outofthebox-option-input-large').val()) {
            $('#outofthebox-options').submit();
            return;
          }

          popup = window.open('https://www.wpcloudplugins.com/updates/activate.php?init=1&client_url=<?php echo strtr(base64_encode($location), '+/=', '-_~'); ?>&plugin_id=<?php echo $this->plugin_id; ?>', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,width=900,height=700");
        });

        $('#check_updates_button').click(function () {
          window.location = '<?php echo admin_url('update-core.php'); ?>';
        });

        $('#purcasecode.outofthebox-option-input-large').focusout(function () {
          var purchase_code_regex = '^([a-z0-9]{8})-?([a-z0-9]{4})-?([a-z0-9]{4})-?([a-z0-9]{4})-?([a-z0-9]{12})$';
          if ($(this).val().match(purchase_code_regex)) {
            $(this).css('color', 'initial');
          } else {
            $(this).css('color', '#dc3232');
          }
        });
        $('#deactivate_license_button').click(function () {
          $('#purcase_code').val('');
          $('#outofthebox-options').submit();
        });

        $('#documentation_button').click(function () {
          popup = window.open('<?php echo OUTOFTHEBOX_ROOTPATH.'/_documentation/index.html'; ?>', "_blank");
        });

        $('#network_wide').click(function () {
          $('#save_settings').trigger('click');
        });

        $('#save_settings').click(function () {
          var $button = $(this);
          $button.addClass('disabled');
          $button.find('.oftb-spinner').fadeIn();

          var i = sessionStorage.length;
          while (i--) {
            var key = sessionStorage.key(i);
            if (/CloudPlugin/.test(key)) {
              sessionStorage.removeItem(key);
            }
          }

          $('#outofthebox-options').ajaxSubmit({
            success: function () {
              $button.removeClass('disabled');
              $button.find('.oftb-spinner').fadeOut();
              location.reload(true);
            },
            error: function () {
              $button.removeClass('disabled');
              $button.find('.oftb-spinner').fadeOut();
              location.reload(true);
            },
          });
          //setTimeout("$('#saveMessage').hide('slow');", 5000);
          return false;
        });
      }
      );


  </script>
</div>