<?php
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
        <div class="outofthebox-form-buttons" style="<?php echo (false === is_plugin_active_for_network(OUTOFTHEBOX_SLUG)) ? 'display:none;' : ''; ?>"> <div id="wpcp-save-settings-button" class="simple-button default"><?php esc_html_e('Save Settings', 'wpcloudplugins'); ?>&nbsp;<div class='wpcp-spinner'></div></div></div>
        <div class="outofthebox-title"><?php esc_html_e('Settings', 'wpcloudplugins'); ?></div>
      </div>


      <div id="" class="outofthebox-panel outofthebox-panel-left">      
                        <div class="outofthebox-nav-header"><?php esc_html_e('Settings', 'wpcloudplugins'); ?> <a href="<?php echo admin_url('update-core.php'); ?>">(Ver: <?php echo OUTOFTHEBOX_VERSION; ?>)</a></div>

        <ul class="outofthebox-nav-tabs">
          <li id="settings_general_tab" data-tab="settings_general" class="current"><a ><?php esc_html_e('General', 'wpcloudplugins'); ?></a></li>
          <?php if ($network_wide_authorization) { ?>
              <li id="settings_advanced_tab" data-tab="settings_advanced" ><a ><?php esc_html_e('Advanced', 'wpcloudplugins'); ?></a></li>
          <?php } ?>
          <li id="settings_system_tab" data-tab="settings_system" ><a><?php esc_html_e('System information', 'wpcloudplugins'); ?></a></li>
          <li id="settings_help_tab" data-tab="settings_help" ><a><?php esc_html_e('Support', 'wpcloudplugins'); ?></a></li>
        </ul>

        <div class="outofthebox-nav-header" style="margin-top: 50px;"><?php esc_html_e('Other Cloud Plugins', 'wpcloudplugins'); ?></div>
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

          <div class="outofthebox-tab-panel-header"><?php esc_html_e('General', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-option-title"><?php esc_html_e('Plugin License', 'wpcloudplugins'); ?></div>
          <?php
          echo $this->get_plugin_activated_box();
          ?>

          <?php if (is_plugin_active_for_network(OUTOFTHEBOX_SLUG)) { ?>
              <div class="outofthebox-option-title"><?php esc_html_e('Network Wide Authorization', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[network_wide]'/>
                  <input type="checkbox" name="out_of_the_box_settings[network_wide]" id="wpcp-network_wide-button" class="outofthebox-onoffswitch-checkbox" <?php echo (empty($network_wide_authorization)) ? '' : 'checked="checked"'; ?> data-div-toggle="network_wide"/>
                  <label class="outofthebox-onoffswitch-label" for="wpcp-network_wide-button"></label>
                </div>
              </div>


              <?php
              if ($network_wide_authorization) {
                  ?>
                  <div class="outofthebox-option-title"><?php esc_html_e('Accounts', 'wpcloudplugins'); ?></div>
                  <div class="outofthebox-accounts-list">
                    <?php
                    $app = $this->get_app(); ?>
                    <div class='account account-new'>
                      <img class='account-image' src='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/dropbox_logo.png'/>
                      <div class='account-info-container'>
                        <div class='account-info'>
                          <div class='account-actions'>
                            <div id='wpcp-add-account-button' type='button' class='simple-button blue' data-url="<?php echo $app->get_auth_url(['force_reapprove' => 'true']); ?>" title="<?php esc_html_e('Add account', 'wpcloudplugins'); ?>"><i class='fas fa-plus-circle' aria-hidden='true'></i>&nbsp;<?php esc_html_e('Add account', 'wpcloudplugins'); ?></div>
                          </div>
                          <div class="account-info-name">
                            <?php esc_html_e('Link a new account to the plugin', 'wpcloudplugins'); ?>
                          </div>
                          <span class="account-info-space"><a href="#" id="wpcp-read-privacy-policy"><i class="fas fa-shield-alt"></i> <?php esc_html_e('What happens with my data when I authorize the plugin?', 'wpcloudplugins'); ?></a></span>   
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
              <div class="outofthebox-tab-panel-header"><?php esc_html_e('Advanced', 'wpcloudplugins'); ?></div>

              <div class="outofthebox-option-title"><?php esc_html_e('"Lost Authorization" notification', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e('If the plugin somehow loses its authorization, a notification email will be send to the following email address', 'wpcloudplugins'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[lostauthorization_notification]" id="lostauthorization_notification" value="<?php echo esc_attr($this->settings['lostauthorization_notification']); ?>">

              <div class="outofthebox-option-title"><?php esc_html_e('Own App', 'wpcloudplugins'); ?>
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
                <div class="outofthebox-option-description"><?php esc_html_e('Only if you want to use your own App, insert your App Key here', 'wpcloudplugins'); ?>.</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[dropbox_app_key]" id="dropbox_app_key" value="<?php echo esc_attr($this->settings['dropbox_app_key']); ?>" placeholder="<--- <?php esc_html_e('Leave empty for easy setup', 'wpcloudplugins'); ?> --->" >

                <div class="outofthebox-option-title">Dropbox App Secret</div>
                <div class="outofthebox-option-description"><?php esc_html_e('If you want to use your own App, insert your App Secret here', 'wpcloudplugins'); ?>.</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[dropbox_app_secret]" id="dropbox_app_secret" value="<?php echo esc_attr($this->settings['dropbox_app_secret']); ?>" placeholder="<--- <?php esc_html_e('Leave empty for easy setup', 'wpcloudplugins'); ?> --->" >   

                <div>
                  <div class="outofthebox-option-title">OAuth 2.0 Redirect URI</div>
                  <div class="outofthebox-option-description"><?php esc_html_e('Set the redirect URI in your application to the following', 'wpcloudplugins'); ?>:</div>
                  <code style="user-select:initial">
                    <?php
                    if ($this->get_app()->has_plugin_own_app()) {
                        echo $this->get_app()->get_redirect_uri();
                    } else {
                        esc_html_e('Enter Client Key and Secret, save settings and reload the page to see the Redirect URI you will need', 'wpcloudplugins');
                    }
                    ?>
                  </code>
                </div>

                <div class="outofthebox-option-title"><?php esc_html_e('Use App Folder', 'wpcloudplugins'); ?>
                  <div class="outofthebox-onoffswitch">
                    <input type='hidden' value='No' name='out_of_the_box_settings[use_app_folder]'/>
                    <input type="checkbox" name="out_of_the_box_settings[use_app_folder]" id="use_app_folder" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['use_app_folder']) ? 'checked="checked"' : ''; ?>/>
                    <label class="outofthebox-onoffswitch-label" for="use_app_folder"></label>
                  </div>
                </div>
                <div class="outofthebox-option-description"><?php esc_html_e('Is your App configured to only access the App specific folder', 'wpcloudplugins'); ?>?</div>

              </div>

              <?php
              //if (!empty($account_type) && $account_type === 'business') {
              ?>

              <div class="outofthebox-option-title"><?php esc_html_e('Business Accounts', 'wpcloudplugins'); ?> | <?php esc_html_e('Dropbox Team Folders', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[use_team_folders]'/>
                  <input type="checkbox" name="out_of_the_box_settings[use_team_folders]" id="use_team_folders" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['use_team_folders']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="use_team_folders"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php esc_html_e('Allows you to access your Dropbox Team Folders', 'wpcloudplugins'); ?>.</div>

              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: <?php esc_html_e('Please check your existing Shortcodes and Manually linked Private Folders when changing this setting. Your root folder will not longer be your Personal Folder when Team Folders are enabled', 'wpcloudplugins'); ?>.</i>
              </div>

              <?php
              //}
              ?>

            </div>
        <?php } ?>
        <!-- End Advanced Tab -->

        <!-- System info Tab -->
        <div id="settings_system"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('System information', 'wpcloudplugins'); ?></div>
          <?php echo $this->get_system_information(); ?>
        </div>
        <!-- End System info -->

        <!-- Help Tab -->
        <div id="settings_help"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Support', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-option-title"><?php esc_html_e('Support & Documentation', 'wpcloudplugins'); ?></div>
          <div id="message">
            <p><?php esc_html_e('Check the documentation of the plugin in case you encounter any problems or are looking for support.', 'wpcloudplugins'); ?></p>
            <div id='wpcp-open-docs-button' type='button' class='simple-button blue'><?php esc_html_e('Open Documentation', 'wpcloudplugins'); ?></div>
          </div>
          <br/>
          <div class="outofthebox-option-title"><?php esc_html_e('Cache', 'wpcloudplugins'); ?></div>
          <?php echo $this->get_plugin_reset_cache_box(); ?>

        </div>  
      </div>
      <!-- End Help info -->
    </div>
  </form>

  <!-- End Privacy Policy -->
  <div id="wpcp-privacy-policy" style='clear:both;display:none'>  
    <div class="outofthebox outofthebox-tb-content">
      <div class="outofthebox-option-title"><?php esc_html_e('Requested scopes and justification', 'wpcloudplugins'); ?></div>
      <div class="outofthebox-option-description"> <?php echo sprintf(esc_html__('In order to display your content stored on %s, you have to authorize it with your %s account.', 'wpcloudplugins'), 'Dropbox', 'Dropbox'); ?> <?php _e('The authorization will ask you to grant the application the following scopes:', 'wpcloudplugins'); ?>

      <br/><br/>
      <table class="widefat">
        <thead>
          <tr>
            <th>Scope</th>
            <th>Reason</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><code>files.content.write</code></td>
            <td>Edit content of your Dropbox files and folders. Required to create/upload/copy/rename/delete content on your Dropbox</td>
          </thead>
          <tr>
            <td><code>files.content.read</code></td>
            <td>View content of your Dropbox files and folders. Used to display your content in the plugins modules, preview it and download the files.</td>
          </tr>
          <tr>
            <td><code>sharing.write</code></td>
            <td>View and manage your Dropbox sharing settings and collaborators. Required to create shared links or direct links to your files.</td>
          </tr>
          <tr>
            <td><code>account_info.read</code></td>
            <td><?php (esc_html_e('Allow the plugin to see your publicly available personal info, like email, name and profile picture. This information will only be displayed on this page for easy account identification.', 'wpcloudplugins')); ?></td>
          </tr>
        </tbody>
      </table>

      <br/>
      <div class="outofthebox-option-title"><?php esc_html_e('Information about the data', 'wpcloudplugins'); ?></div>
      The authorization tokens will be stored, encrypted, on this server and is not accessible by the developer or any third party. When you use the Application, all communications are strictly between your server and the cloud storage service servers. The communication is encrypted and the communication will not go through WP Cloud Plugins servers. We do not collect and do not have access to your personal data.
      
      <br/><br/>
      <i class="fas fa-shield-alt"></i> <?php echo sprintf(esc_html__('Read the full %sPrivacy Policy%s if you have any further privacy concerns.', 'wpcloudplugins'), '<a href="https://www.wpcloudplugins.com/privacy-policy/privacy-policy-out-of-the-box/">', '</a>'); ?></div>
    </div>
  </div>
  <!-- End Short Privacy Policy -->

</div>