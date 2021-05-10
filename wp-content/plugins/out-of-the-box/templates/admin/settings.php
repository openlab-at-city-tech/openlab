<?php
$network_wide_authorization = $this->get_processor()->is_network_authorized();

function wp_roles_and_users_input($name, $selected = [])
{
    if (!is_array($selected)) {
        $selected = ['administrator'];
    }

    // Workaround: Add temporarily selected value to prevent an empty selection in Tagify when only user ID 0 is selected
    $selected[] = '_______PREVENT_EMPTY_______';

    // Create value for imput field
    $value = implode(', ', $selected);

    // Input Field
    echo "<input class='outofthebox-option-input-large outofthebox-tagify outofthebox-permissions-placeholders' type='text' name='{$name}' value='{$value}' placeholder='' />";
}

function create_color_boxes_table($colors, $settings)
{
    if (0 === count($colors)) {
        return '';
    }

    $table_html = '<table class="color-table">';

    foreach ($colors as $color_id => $color) {
        $value = isset($settings['colors'][$color_id]) ? sanitize_text_field($settings['colors'][$color_id]) : $color['default'];

        $table_html .= '<tr>';
        $table_html .= "<td>{$color['label']}</td>";
        $table_html .= "<td><input value='{$value}' data-default-color='{$color['default']}'  name='out_of_the_box_settings[colors][{$color_id}]' id='colors-{$color_id}' type='text'  class='wpcp-color-picker' data-alpha-enabled='true' ></td>";
        $table_html .= '</tr>';
    }

    $table_html .= '</table>';

    return $table_html;
}

function create_upload_button_for_custom_images($option)
{
    $field_value = $option['value'];
    $button_html = '<div class="upload_row">';

    $button_html .= '<div class="screenshot" id="'.$option['id'].'_image">'."\n";

    if ('' !== $field_value) {
        $button_html .= '<img src="'.$field_value.'" alt="" />'."\n";
        $button_html .= '<a href="javascript:void(0)" class="wpcp-upload-remove">'.esc_html__('Remove', 'wpcloudplugins').'</a>'."\n";
        $button_html .= '<a href="javascript:void(0)" class="upload-default">'.esc_html__('Default', 'wpcloudplugins').'</a>'."\n";
    }

    $button_html .= '</div>';

    $button_html .= '<input id="'.esc_attr($option['id']).'" class="upload outofthebox-option-input-large" type="text" name="'.esc_attr($option['name']).'" value="'.esc_attr($field_value).'" autocomplete="off" />';
    $button_html .= '<input class="wpcp-upload-button simple-button blue" type="button" value="'.esc_html__('Select Image', 'wpcloudplugins').'" title="'.esc_html__('Upload or select a file from the media library', 'wpcloudplugins').'" />';

    if ($field_value !== $option['default']) {
        $button_html .= '<input id="wpcp-default-image-button" class="wpcp-default-image-button simple-button" type="button" value="'.esc_html__('Default', 'wpcloudplugins').'" title="'.esc_html__('Fallback to the default value', 'wpcloudplugins').'"  data-default="'.$option['default'].'"/>';
    }

    $button_html .= '</div>'."\n";

    return $button_html;
}
?>

<div class="outofthebox admin-settings">
  <form id="outofthebox-options" method="post" action="options.php">
    <?php settings_fields('out_of_the_box_settings'); ?>

    <div class="wrap">
      <div class="outofthebox-header">
        <div class="outofthebox-logo"><a href="https://www.wpcloudplugins.com" target="_blank"><img src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/wpcp-logo-dark.svg" height="64" width="64"/></a></div>
        <div class="outofthebox-form-buttons"> <div id="save_settings" class="simple-button default save_settings" name="save_settings"><?php esc_html_e('Save Settings', 'wpcloudplugins'); ?>&nbsp;<div class='wpcp-spinner'></div></div></div>
        <div class="outofthebox-title"><?php esc_html_e('Settings', 'wpcloudplugins'); ?></div>
      </div>


      <div id="" class="outofthebox-panel outofthebox-panel-left">      
        <div class="outofthebox-nav-header"><?php esc_html_e('Settings', 'wpcloudplugins'); ?> <a href="<?php echo admin_url('update-core.php'); ?>">(Ver: <?php echo OUTOFTHEBOX_VERSION; ?>)</a></div>

        <ul class="outofthebox-nav-tabs">
          <li id="settings_general_tab" data-tab="settings_general" class="current"><a ><?php esc_html_e('General', 'wpcloudplugins'); ?></a></li>
          <?php
          if ($this->is_activated()) {
              ?>
              <li id="settings_layout_tab" data-tab="settings_layout" ><a ><?php esc_html_e('Layout', 'wpcloudplugins'); ?></a></li>
              <li id="settings_userfolders_tab" data-tab="settings_userfolders" ><a ><?php esc_html_e('Private Folders', 'wpcloudplugins'); ?></a></li>
              <li id="settings_advanced_tab" data-tab="settings_advanced" ><a ><?php esc_html_e('Advanced', 'wpcloudplugins'); ?></a></li>
              <li id="settings_integrations_tab" data-tab="settings_integrations" ><a><?php esc_html_e('Integrations', 'wpcloudplugins'); ?></a></li>
              <li id="settings_notifications_tab" data-tab="settings_notifications" ><a ><?php esc_html_e('Notifications', 'wpcloudplugins'); ?></a></li>
              <li id="settings_permissions_tab" data-tab="settings_permissions" ><a><?php esc_html_e('Permissions', 'wpcloudplugins'); ?></a></li>
              <li id="settings_stats_tab" data-tab="settings_stats" ><a><?php esc_html_e('Statistics', 'wpcloudplugins'); ?></a></li>
            <li id="settings_tools_tab" data-tab="settings_tools" ><a><?php esc_html_e('Tools', 'wpcloudplugins'); ?></a></li>              
              <?php
          }
          ?>

          <li id="settings_system_tab" data-tab="settings_system" ><a><?php esc_html_e('System information', 'wpcloudplugins'); ?></a></li>
          <li id="settings_help_tab" data-tab="settings_help" ><a><?php esc_html_e('Support', 'wpcloudplugins'); ?></a></li>

        </ul>

        <div class="outofthebox-nav-header" style="margin-top: 50px;"><?php esc_html_e('Other Cloud Plugins', 'wpcloudplugins'); ?></div>
        <ul class="outofthebox-nav-tabs">
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/L6yXj" target="_blank" style="color:#522058;">Google Drive <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/yDbyv" target="_blank" style="color:#522058;">OneDrive <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/M4B53" target="_blank" style="color:#522058;">Box <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
        </ul> 

        <div class="outofthebox-nav-footer">          <a href="https://www.wpcloudplugins.com/" target="_blank">
            <img alt="" height="auto" src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/wpcloudplugins-logo-dark.png">
          </a></div>
      </div>


      <div class="outofthebox-panel outofthebox-panel-right">

        <!-- General Tab -->
        <div id="settings_general" class="outofthebox-tab-panel current">

          <div class="outofthebox-tab-panel-header"><?php esc_html_e('General', 'wpcloudplugins'); ?></div>

          <?php if ($this->is_activated()) { ?>
              <div class="outofthebox-option-title"><?php esc_html_e('Accounts', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-accounts-list">
                <?php
                if (false === $this->get_processor()->is_network_authorized() || ($this->get_processor()->is_network_authorized() && true === is_network_admin())) {
                    $app = $this->get_app(); ?>
                    <div class='account account-new'>
                      <img class='account-image' src='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/dropbox_logo.png'/>
                      <div class='account-info-container'>
                        <div class='account-info'>
                          <div class='account-actions'>
                            <div id='add_dropbox_button' type='button' class='simple-button blue' data-url="<?php echo $app->get_auth_url(['force_reapprove' => 'true']); ?>" title="<?php esc_html_e('Add account', 'wpcloudplugins'); ?>"><i class='fas fa-plus-circle' aria-hidden='true'></i>&nbsp;<?php esc_html_e('Add account', 'wpcloudplugins'); ?></div>
                          </div>
                          <div class="account-info-name">
                            <?php esc_html_e('Add account', 'wpcloudplugins'); ?>
                          </div>
                          <span class="account-info-space"><?php esc_html_e('Link a new account to the plugin', 'wpcloudplugins'); ?></span>
                        </div>
                      </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class='account account-new'>
                      <img class='account-image' src='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/dropbox_logo.png'/>
                      <div class='account-info-container'>
                        <div class='account-info'>
                          <span class="account-info-space"><?php echo sprintf(wp_kses(__("The authorization is managed by the Network Admin via the <a href='%s'>Network Settings Page</a> of the plugin", 'wpcloudplugins'), ['a' => ['href' => []]]), network_admin_url('admin.php?page=OutoftheBox_network_settings')); ?>.</span>
                        </div>
                      </div>
                    </div>   
                    <?php
                }

                foreach ($this->get_main()->get_accounts()->list_accounts() as $account_id => $account) {
                    echo $this->get_plugin_authorization_box($account);
                }
                ?>
              </div>
              <?php
          }
          ?>
          <div class="outofthebox-option-title"><?php esc_html_e('Plugin License', 'wpcloudplugins'); ?></div>
          <?php
          echo $this->get_plugin_activated_box();
          ?>
        </div>
        <!-- End General Tab -->


        <!-- Layout Tab -->
        <div id="settings_layout"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Layout', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-accordion">

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Loading Spinner & Images', 'wpcloudplugins'); ?>         </div>
            <div>

              <div class="outofthebox-option-title"><?php esc_html_e('Select Loader Spinner', 'wpcloudplugins'); ?></div>
              <select type="text" name="out_of_the_box_settings[loaders][style]" id="loader_style">
                <option value="beat" <?php echo 'beat' === $this->settings['loaders']['style'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Beat', 'wpcloudplugins'); ?></option>
                <option value="spinner" <?php echo 'spinner' === $this->settings['loaders']['style'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Spinner', 'wpcloudplugins'); ?></option>
                <option value="custom" <?php echo 'custom' === $this->settings['loaders']['style'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Custom Image (selected below)', 'wpcloudplugins'); ?></option>
              </select>

              <div class="outofthebox-option-title"><?php esc_html_e('General Loader', 'wpcloudplugins'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['loading'], 'id' => 'loaders_loading', 'name' => 'out_of_the_box_settings[loaders][loading]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_loading.gif'];
              echo create_upload_button_for_custom_images($button);
              ?>
              <div class="outofthebox-option-title"><?php esc_html_e('Upload Loader', 'wpcloudplugins'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['upload'], 'id' => 'loaders_upload', 'name' => 'out_of_the_box_settings[loaders][upload]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_upload.gif'];
              echo create_upload_button_for_custom_images($button);
              ?>
              <div class="outofthebox-option-title"><?php esc_html_e('No Results', 'wpcloudplugins'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['no_results'], 'id' => 'loaders_no_results', 'name' => 'out_of_the_box_settings[loaders][no_results]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_no_results.png'];
              echo create_upload_button_for_custom_images($button);
              ?>
              <div class="outofthebox-option-title"><?php esc_html_e('Access Forbidden', 'wpcloudplugins'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['protected'], 'id' => 'loaders_protected', 'name' => 'out_of_the_box_settings[loaders][protected]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_protected.png'];
              echo create_upload_button_for_custom_images($button);
              ?>
              <div class="outofthebox-option-title"><?php esc_html_e('Error', 'wpcloudplugins'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['error'], 'id' => 'loaders_error', 'name' => 'out_of_the_box_settings[loaders][error]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_error.png'];
              echo create_upload_button_for_custom_images($button);
              ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Color Palette', 'wpcloudplugins'); ?></div>
            <div>

              <div class="outofthebox-option-title"><?php esc_html_e('Theme Style', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e('Select the general style of your theme', 'wpcloudplugins'); ?>.</div>
              <select name="skin_selectbox" id="wpcp_content_skin_selectbox" class="ddslickbox">
                <option value="dark" <?php echo 'dark' === $this->settings['colors']['style'] ? "selected='selected'" : ''; ?> data-imagesrc="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/skin-dark.png" data-description=""><?php esc_html_e('Dark', 'wpcloudplugins'); ?></option>
                <option value="light" <?php echo 'light' === $this->settings['colors']['style'] ? "selected='selected'" : ''; ?> data-imagesrc="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/skin-light.png" data-description=""><?php esc_html_e('Light', 'wpcloudplugins'); ?></option>
              </select>
              <input type="hidden" name="out_of_the_box_settings[colors][style]" id="wpcp_content_skin" value="<?php echo esc_attr($this->settings['colors']['style']); ?>">

              <?php
              $colors = [
                  'background' => [
                      'label' => esc_html__('Content Background Color', 'wpcloudplugins'),
                      'default' => '#f2f2f2',
                  ],
                  'accent' => [
                      'label' => esc_html__('Accent Color', 'wpcloudplugins'),
                      'default' => '#522058',
                  ],
                  'black' => [
                      'label' => esc_html__('Black', 'wpcloudplugins'),
                      'default' => '#222',
                  ],
                  'dark1' => [
                      'label' => esc_html__('Dark 1', 'wpcloudplugins'),
                      'default' => '#666666',
                  ],
                  'dark2' => [
                      'label' => esc_html__('Dark 2', 'wpcloudplugins'),
                      'default' => '#999999',
                  ],
                  'white' => [
                      'label' => esc_html__('White', 'wpcloudplugins'),
                      'default' => '#fff',
                  ],
                  'light1' => [
                      'label' => esc_html__('Light 1', 'wpcloudplugins'),
                      'default' => '#fcfcfc',
                  ],
                  'light2' => [
                      'label' => esc_html__('Light 2', 'wpcloudplugins'),
                      'default' => '#e8e8e8',
                  ],
              ];

              echo create_color_boxes_table($colors, $this->settings);
              ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Icons', 'wpcloudplugins'); ?></div>
            <div>

              <div class="outofthebox-option-title"><?php esc_html_e('Icon Set', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php _e(sprintf('Location to the icon set you want to use. When you want to use your own set, just make a copy of the default icon set folder (<code>%s</code>) and place it in the <code>wp-content/</code> folder', OUTOFTHEBOX_ROOTPATH.'/css/icons/'), 'wpcloudplugins'); ?>.</div>

              <div class="oftb-warning">
                <i><strong><?php esc_html_e('NOTICE', 'wpcloudplugins'); ?></strong>: <?php esc_html_e('Modifications to the default icons set will be lost during an update.', 'wpcloudplugins'); ?>.</i>
              </div>

              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[icon_set]" id="icon_set" value="<?php echo esc_attr($this->settings['icon_set']); ?>">  
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Lightbox', 'wpcloudplugins'); ?></div>
            <div>
              <div class="outofthebox-option-title"><?php esc_html_e('Lightbox Skin', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e('Select which skin you want to use for the Inline Preview', 'wpcloudplugins'); ?>.</div>
              <select name="wpcp_lightbox_skin_selectbox" id="wpcp_lightbox_skin_selectbox" class="ddslickbox">
                <?php
                foreach (new DirectoryIterator(OUTOFTHEBOX_ROOTDIR.'/vendors/iLightBox/') as $fileInfo) {
                    if ($fileInfo->isDir() && !$fileInfo->isDot() && (false !== strpos($fileInfo->getFilename(), 'skin'))) {
                        if (file_exists(OUTOFTHEBOX_ROOTDIR.'/vendors/iLightBox/'.$fileInfo->getFilename().'/skin.css')) {
                            $selected = '';
                            $skinname = str_replace('-skin', '', $fileInfo->getFilename());

                            if ($skinname === $this->settings['lightbox_skin']) {
                                $selected = 'selected="selected"';
                            }

                            $icon = file_exists(OUTOFTHEBOX_ROOTDIR.'/vendors/iLightBox/'.$fileInfo->getFilename().'/thumb.jpg') ? OUTOFTHEBOX_ROOTPATH.'/vendors/iLightBox/'.$fileInfo->getFilename().'/thumb.jpg' : '';
                            echo '<option value="'.$skinname.'" data-imagesrc="'.$icon.'" data-description="" '.$selected.'>'.$fileInfo->getFilename()."</option>\n";
                        }
                    }
                }
                ?>
              </select>
              <input type="hidden" name="out_of_the_box_settings[lightbox_skin]" id="wpcp_lightbox_skin" value="<?php echo esc_attr($this->settings['lightbox_skin']); ?>">


              <div class="outofthebox-option-title">Lightbox Scroll</div>
              <div class="outofthebox-option-description"><?php esc_html_e("Sets path for switching windows. Possible values are 'vertical' and 'horizontal' and the default is 'vertical", 'wpcloudplugins'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[lightbox_path]" id="lightbox_path">
                <option value="horizontal" <?php echo 'horizontal' === $this->settings['lightbox_path'] ? "selected='selected'" : ''; ?>>Horizontal</option>
                <option value="vertical" <?php echo 'vertical' === $this->settings['lightbox_path'] ? "selected='selected'" : ''; ?>>Vertical</option>
              </select>

              <div class="outofthebox-option-title">Lightbox <?php esc_html_e('Image Source', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e('Select the source of the images. Large thumbnails will load fast once created on your server, (raw) original files can take some time to load', 'wpcloudplugins'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[loadimages]" id="loadimages">
                <option value="thumbnail" <?php echo 'thumbnail' === $this->settings['loadimages'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Fast - Large preview thumbnails', 'wpcloudplugins'); ?></option>
                <option value="original" <?php echo 'original' === $this->settings['loadimages'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Slow - Show original files', 'wpcloudplugins'); ?></option>
              </select>

              <div class="outofthebox-option-title"><?php esc_html_e('Allow Mouse Click on Image', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[lightbox_rightclick]'/>
                  <input type="checkbox" name="out_of_the_box_settings[lightbox_rightclick]" id="lightbox_rightclick" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['lightbox_rightclick']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="lightbox_rightclick"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php esc_html_e('Should people be able to access the right click context menu to e.g. save the image?', 'wpcloudplugins'); ?>.</div>

              <div class="outofthebox-option-title"><?php esc_html_e('Header', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e('When should the header containing title and action-menu be shown', 'wpcloudplugins'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[lightbox_showheader]" id="lightbox_showheader">
                <option value="true" <?php echo 'true' === $this->settings['lightbox_showheader'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Always', 'wpcloudplugins'); ?></option>
                <option value="click" <?php echo 'click' === $this->settings['lightbox_showheader'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Show after clicking on the Lightbox', 'wpcloudplugins'); ?></option>
                <option value="mouseenter" <?php echo 'mouseenter' === $this->settings['lightbox_showheader'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Show when hovering over the Lightbox', 'wpcloudplugins'); ?></option>
                <option value="false" <?php echo 'false' === $this->settings['lightbox_showheader'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Never', 'wpcloudplugins'); ?></option>
              </select>  

              <div class="outofthebox-option-title"><?php esc_html_e('Caption/Description', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e('When should the description be shown in the Gallery Lightbox', 'wpcloudplugins'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[lightbox_showcaption]" id="lightbox_showcaption">
                <option value="true" <?php echo 'true' === $this->settings['lightbox_showcaption'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Always', 'wpcloudplugins'); ?></option>
                <option value="click" <?php echo 'click' === $this->settings['lightbox_showcaption'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Show after clicking on the Lightbox', 'wpcloudplugins'); ?></option>
                <option value="mouseenter" <?php echo 'mouseenter' === $this->settings['lightbox_showcaption'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Show when hovering over the Lightbox', 'wpcloudplugins'); ?></option>
                <option value="false" <?php echo 'false' === $this->settings['lightbox_showcaption'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Never', 'wpcloudplugins'); ?></option>
              </select>                  

            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Media Player', 'wpcloudplugins'); ?></div>
            <div> 
              <div class="outofthebox-option-description"><?php esc_html_e('Select which Media Player you want to use', 'wpcloudplugins'); ?>.</div>
              <select name="wpcp_mediaplayer_skin_selectbox" id="wpcp_mediaplayer_skin_selectbox" class="ddslickbox">
                <?php
                foreach (new DirectoryIterator(OUTOFTHEBOX_ROOTDIR.'/skins/') as $fileInfo) {
                    if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                        if (file_exists(OUTOFTHEBOX_ROOTDIR.'/skins/'.$fileInfo->getFilename().'/js/Player.js')) {
                            $selected = '';
                            if ($fileInfo->getFilename() === $this->settings['mediaplayer_skin']) {
                                $selected = 'selected="selected"';
                            }

                            $icon = file_exists(OUTOFTHEBOX_ROOTDIR.'/skins/'.$fileInfo->getFilename().'/Thumb.jpg') ? OUTOFTHEBOX_ROOTPATH.'/skins/'.$fileInfo->getFilename().'/Thumb.jpg' : '';
                            echo '<option value="'.$fileInfo->getFilename().'" data-imagesrc="'.$icon.'" data-description="" '.$selected.'>'.$fileInfo->getFilename()."</option>\n";
                        }
                    }
                }
                ?>
              </select>
              <input type="hidden" name="out_of_the_box_settings[mediaplayer_skin]" id="wpcp_mediaplayer_skin" value="<?php echo esc_attr($this->settings['mediaplayer_skin']); ?>">
              
              <br/><br/>
              <div class="outofthebox-option-title"><?php esc_html_e('Load native MediaElement.js library', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[mediaplayer_load_native_mediaelement]'/>
                  <input type="checkbox" name="out_of_the_box_settings[mediaplayer_load_native_mediaelement]" id="mediaplayer_load_native_mediaelement" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['mediaplayer_load_native_mediaelement']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="mediaplayer_load_native_mediaelement"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php esc_html_e('Is the layout of the Media Player all mixed up an does is not initiate properly? If that is the case, you might be encountering a conflict between media player librarieson your site. To resolve this, enable this setting to load the native MediaElement.js library.', 'wpcloudplugins'); ?></div>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Custom CSS', 'wpcloudplugins'); ?></div>
            <div>
              <div class="outofthebox-option-title"><?php esc_html_e('Custom CSS', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e("If you want to modify the looks of the plugin slightly, you can insert here your custom CSS. Don't edit the CSS files itself, because those modifications will be lost during an update.", 'wpcloudplugins'); ?>.</div>
              <textarea name="out_of_the_box_settings[custom_css]" id="custom_css" cols="" rows="10"><?php echo esc_attr($this->settings['custom_css']); ?></textarea>
            </div>
          </div>
        </div>
        <!-- End Layout Tab -->

        <!-- UserFolders Tab -->
        <div id="settings_userfolders"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Private Folders', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-accordion">

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Global settings Automatically linked Private Folders', 'wpcloudplugins'); ?> </div>
            <div>

              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: <?php esc_html_e('The following settings are only used for all shortcodes with automatically linked Private Folders', 'wpcloudplugins'); ?>. </i>
              </div>


              <div class="outofthebox-option-title"><?php esc_html_e('Create Private Folders on registration', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[userfolder_oncreation]'/>
                  <input type="checkbox" name="out_of_the_box_settings[userfolder_oncreation]" id="userfolder_oncreation" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['userfolder_oncreation']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="userfolder_oncreation"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php esc_html_e('Create a new Private Folders automatically after a new user has been created', 'wpcloudplugins'); ?>.</div>

              <div class="outofthebox-option-title"><?php esc_html_e('Create all Private Folders on first visit', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[userfolder_onfirstvisit]'/>
                  <input type="checkbox" name="out_of_the_box_settings[userfolder_onfirstvisit]" id="userfolder_onfirstvisit" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['userfolder_onfirstvisit']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="userfolder_onfirstvisit"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php esc_html_e('Create all Private Folders on first visit', 'wpcloudplugins'); ?>.</div>
              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: Creating User Folders takes around 1 sec per user, so it isn't recommended to create those on first visit when you have tons of users.</i>
              </div>


              <div class="outofthebox-option-title"><?php esc_html_e('Update Private Folders after profile update', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[userfolder_update]'/>
                  <input type="checkbox" name="out_of_the_box_settings[userfolder_update]" id="userfolder_update" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['userfolder_update']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="userfolder_update"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php esc_html_e('Update the folder name of the user after they have updated their profile', 'wpcloudplugins'); ?>.</div>

              <div class="outofthebox-option-title"><?php esc_html_e('Remove Private Folders after account removal', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[userfolder_remove]'/>
                  <input type="checkbox" name="out_of_the_box_settings[userfolder_remove]" id="userfolder_remove" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['userfolder_remove']) ? 'checked="checked"' : ''; ?> />
                  <label class="outofthebox-onoffswitch-label" for="userfolder_remove"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php esc_html_e('Try to remove Private Folders after they are deleted', 'wpcloudplugins'); ?>.</div>

              <div class="outofthebox-option-title"><?php esc_html_e('Name Template', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php echo esc_html__('Template name for automatically created Private Folders.', 'wpcloudplugins').' '.sprintf(esc_html__('Available placeholders: %s', 'wpcloudplugins'), '').'<code>%user_login%</code>, <code>%user_firstname%</code>, <code>%user_lastname%</code>, <code>%user_email%</code>, <code>%display_name%</code>, <code>%ID%</code>, <code>%user_role%</code>, <code>%jjjj-mm-dd%</code>, <code>%hh:mm%</code>, <code>%uniqueID%</code>'; ?>.</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[userfolder_name]" id="userfolder_name" value="<?php echo esc_attr($this->settings['userfolder_name']); ?>">

                          </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Global settings Manually linked Private Folders', 'wpcloudplugins'); ?> </div>
            <div>

              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: <?php echo sprintf(esc_html__('You can manually link users to their Private Folder via the %s[Link Private Folders]%s menu page', 'wpcloudplugins'), '<a href="'.admin_url('admin.php?page=OutoftheBox_settings_linkusers').'" target="_blank">', '</a>'); ?>. </i>
              </div>

              <div class="outofthebox-option-title"><?php esc_html_e('Access Forbidden notice', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e("Message that is displayed when an user is visiting a shortcode with the Private Folders feature set to 'Manual' mode while it doesn't have Private Folder linked to its account", 'wpcloudplugins'); ?>.</div>

              <?php
              ob_start();
              wp_editor($this->settings['userfolder_noaccess'], 'out_of_the_box_settings_userfolder_noaccess', [
                  'textarea_name' => 'out_of_the_box_settings[userfolder_noaccess]',
                  'teeny' => true,
                  'tinymce' => false,
                  'textarea_rows' => 15,
                  'media_buttons' => false,
              ]);
              echo ob_get_clean();
              ?>

            </div>

            <?php
            $main_account = $this->get_processor()->get_accounts()->get_primary_account();

            if (!empty($main_account)) {
                ?>
                <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Private Folders in WP Admin Dashboard', 'wpcloudplugins'); ?> </div>
                <div>

                  <div class="oftb-warning">
                    <i><strong>NOTICE</strong>: <?php esc_html_e('This setting only restrict access of the File Browsers in the Admin Dashboard (e.g. the ones in the Shortcode Builder and the File Browser menu). To enable Private Folders for your own Shortcodes, use the Shortcode Builder', 'wpcloudplugins'); ?>. </i>
                  </div>

                  <div class="outofthebox-option-description"><?php esc_html_e('Enables Private Folders in the Shortcode Builder and Back-End File Browser', 'wpcloudplugins'); ?>.</div>
                  <select type="text" name="out_of_the_box_settings[userfolder_backend]" id="userfolder_backend" data-div-toggle="private-folders-auto" data-div-toggle-value="auto">
                    <option value="No" <?php echo 'No' === $this->settings['userfolder_backend'] ? "selected='selected'" : ''; ?>>No</option>
                    <option value="manual" <?php echo 'manual' === $this->settings['userfolder_backend'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Yes, I link the users Manually', 'wpcloudplugins'); ?></option>
                    <option value="auto" <?php echo 'auto' === $this->settings['userfolder_backend'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Yes, let the plugin create the User Folders for me', 'wpcloudplugins'); ?></option>
                  </select>
                  <div class="outofthebox-suboptions private-folders-auto <?php echo ('auto' === ($this->settings['userfolder_backend'])) ? '' : 'hidden'; ?> ">
                    <div class="outofthebox-option-title"><?php esc_html_e('Root folder for Private Folders', 'wpcloudplugins'); ?></div>
                    <div class="outofthebox-option-description"><?php esc_html_e('Select in which folder the Private Folders should be created', 'wpcloudplugins'); ?>. <?php esc_html_e('Current selected folder', 'wpcloudplugins'); ?>:</div>
                    <?php
                    $private_auto_folder = $this->settings['userfolder_backend_auto_root'];

                if (empty($private_auto_folder)) {
                    $this->get_processor()->set_current_account($main_account);

                    $private_auto_folder = [
                        'account' => $main_account->get_id(),
                        'id' => '/',
                        'name' => '/',
                        'view_roles' => ['administrator'],
                    ];
                }

                if (!isset($private_auto_folder['account']) || empty($private_auto_folder['account'])) {
                    $private_auto_folder['account'] = $main_account->get_id();
                }

                $account = $this->get_processor()->get_accounts()->get_account_by_id($private_auto_folder['account']);
                if (null !== $account) {
                    $this->get_processor()->set_current_account($account);
                } ?>
                    <input class="outofthebox-option-input-large private-folders-auto-current" type="text" value="<?php echo $private_auto_folder['name']; ?>" disabled="disabled">
                    <input class="private-folders-auto-input-account" type='hidden' value='<?php echo $private_auto_folder['account']; ?>' name='out_of_the_box_settings[userfolder_backend_auto_root][account]'/>
                    <input class="private-folders-auto-input-id" type='hidden' value='<?php echo $private_auto_folder['id']; ?>' name='out_of_the_box_settings[userfolder_backend_auto_root][id]'/>
                    <input class="private-folders-auto-input-name" type='hidden' value='<?php echo $private_auto_folder['name']; ?>' name='out_of_the_box_settings[userfolder_backend_auto_root][name]'/>
                    <div id="wpcp_root_folder_button" type="button" class="button-primary private-folders-auto-button"><?php esc_html_e('Select Folder', 'wpcloudplugins'); ?>&nbsp;<div class='wpcp-spinner'></div></div>

                    <div id='oftb-embedded' style='clear:both;display:none'>
                      <?php
                      try {
                          echo $this->get_processor()->create_from_shortcode(
                              [
                                  'mode' => 'files',
                                  'singleaccount' => '0',
                                  'showfiles' => '1',
                                  'filesize' => '0',
                                  'filedate' => '0',
                                  'upload' => '0',
                                  'delete' => '0',
                                  'rename' => '0',
                                  'addfolder' => '0',
                                  'showbreadcrumb' => '1',
                                  'showfiles' => '0',
                                  'downloadrole' => 'none',
                                  'candownloadzip' => '0',
                                  'showsharelink' => '0',
                                  'mcepopup' => 'linktobackendglobal',
                                  'search' => '0', ]
                          );
                      } catch (\Exception $ex) {
                      } ?>
                    </div>

                    <br/><br/>
                    <div class="outofthebox-option-title"><?php esc_html_e('Full Access', 'wpcloudplugins'); ?></div>
                    <div class="outofthebox-option-description"><?php esc_html_e('By default only Administrator users will be able to navigate through all Private Folders', 'wpcloudplugins'); ?>. <?php esc_html_e('When you want other User Roles to be able do browse to the Private Folders as well, please check them below', 'wpcloudplugins'); ?>.</div>

                    <?php
                    $selected = (isset($private_auto_folder['view_roles'])) ? $private_auto_folder['view_roles'] : [];
                wp_roles_and_users_input('out_of_the_box_settings[userfolder_backend_auto_root][view_roles]', $selected); ?>
                  </div>
                </div>
                <?php
            }
            ?>
          </div>
        </div>
        <!-- End UserFolders Tab -->


        <!--  Advanced Tab -->
        <div id="settings_advanced"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Advanced', 'wpcloudplugins'); ?></div>

          <?php if (false === $network_wide_authorization) { ?>
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
          }
          ?>
          <div class="outofthebox-option-title"><?php esc_html_e('Load Javascripts on all pages', 'wpcloudplugins'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[always_load_scripts]'/>
              <input type="checkbox" name="out_of_the_box_settings[always_load_scripts]" id="always_load_scripts" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['always_load_scripts']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="always_load_scripts"></label>
            </div>
            <div class="outofthebox-option-description"><?php esc_html_e('By default the plugin will only load it scripts when the shortcode is present on the page. If you are dynamically loading content via AJAX calls and the plugin does not show up, please enable this setting', 'wpcloudplugins'); ?>.</div>
          </div>

          <div class="outofthebox-option-title"><?php esc_html_e('Enable Font Awesome Library v4 compatibility', 'wpcloudplugins'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[fontawesomev4_shim]'/>
              <input type="checkbox" name="out_of_the_box_settings[fontawesomev4_shim]" id="fontawesomev4_shim" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['fontawesomev4_shim']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="fontawesomev4_shim"></label>
            </div>
            <div class="outofthebox-option-description"><?php esc_html_e('If your theme is loading the old Font Awesome icon library (v4), it can cause conflict with the (v5) icons of this plugin. If you are having trouble with the icons, please enable this setting for backwards compatibility', 'wpcloudplugins'); ?>. <?php esc_html_e('To disable the Font Awesome library of this plugin completely, add this to your wp-config.php file', 'wpcloudplugins'); ?>: <code>define('WPCP_DISABLE_FONTAWESOME', true);</code></div>
          </div>               

          <div class="outofthebox-option-title"><?php esc_html_e('Enable Gzip compression', 'wpcloudplugins'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[gzipcompression]'/>
              <input type="checkbox" name="out_of_the_box_settings[gzipcompression]" id="gzipcompression" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['gzipcompression']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="gzipcompression"></label>
            </div>
          </div>
          <div class="outofthebox-option-description">Enables gzip-compression if the visitor's browser can handle it. This will increase the performance of the plugin if you are displaying large amounts of files and it reduces bandwidth usage as well. It uses the PHP ob_gzhandler() callback.</div>
          <div class="oftb-warning">
            <i><strong>NOTICE</strong>: Please use this setting with caution. Always test if the plugin still works on the Front-End as some servers are already configured to gzip content!</i>
          </div>

          <div class="outofthebox-option-title"><?php esc_html_e('Nonce Validation', 'wpcloudplugins'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[nonce_validation]'/>
              <input type="checkbox" name="out_of_the_box_settings[nonce_validation]" id="nonce_validation" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['nonce_validation']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="nonce_validation"></label>
            </div></div>
          <div class="outofthebox-option-description"><?php esc_html_e('The plugin uses, among others, the WordPress Nonce system to protect you against several types of attacks including CSRF. Disable this in case you are encountering a conflict with a plugin that alters this system', 'wpcloudplugins'); ?>. </div>
          <div class="oftb-warning">
            <i><strong>NOTICE</strong>: Please use this setting with caution! Only disable it when really necessary.</i>
          </div>

          <div class="outofthebox-option-title"><?php esc_html_e('Max Age Cache Request', 'wpcloudplugins'); ?></div>
          <div class="outofthebox-option-description"><?php esc_html_e('How long are the requests to view the plugin cached? Number is in minutes', 'wpcloudplugins'); ?>.</div>
          <input type="text" name="out_of_the_box_settings[request_cache_max_age]" id="request_cache_max_age" value="<?php echo esc_attr($this->settings['request_cache_max_age']); ?>" maxlength="3" size="3" >   <?php esc_html_e('Minutes'); ?>

          <div class="outofthebox-option-title"><?php esc_html_e('Download method', 'wpcloudplugins'); ?></div>
          <div class="outofthebox-option-description"><?php esc_html_e('Select the method that should be used to download your files. Default is to redirect the user to a temporarily url. If you want to use your server as a proxy just set it to Download via Server', 'wpcloudplugins'); ?>.</div>
          <select type="text" name="out_of_the_box_settings[download_method]" id="download_method">
            <option value="redirect" <?php echo 'redirect' === $this->settings['download_method'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Redirect to download url (fast)', 'wpcloudplugins'); ?></option>
            <option value="proxy" <?php echo 'proxy' === $this->settings['download_method'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Use your Server as proxy (slow)', 'wpcloudplugins'); ?></option>
          </select>   

          <div class="outofthebox-option-title"><?php esc_html_e('Delete settings on Uninstall', 'wpcloudplugins'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[uninstall_reset]'/>
              <input type="checkbox" name="out_of_the_box_settings[uninstall_reset]" id="uninstall_reset" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['uninstall_reset']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="uninstall_reset"></label>
            </div>
            </div>
          <div class="outofthebox-option-description"><?php esc_html_e('When you uninstall the plugin, what do you want to do with your settings? You can save them for next time, or wipe them back to factory settings.', 'wpcloudplugins'); ?>. </div>
          <div class="oftb-warning">
            <i><strong>NOTICE</strong>: <?php echo esc_html__('When you reset the settings, the plugin will not longer be linked to your accounts, but their authorization will not be revoked', 'wpcloudplugins').'. '.esc_html__('You can revoke the authorization via the General tab', 'wpcloudplugins').'.'; ?></a></i>
          </div>

        </div>
        <!-- End Advanced Tab -->

        <!-- Integrations Tab -->
        <div id="settings_integrations"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Integrations', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-accordion">
            <div class="outofthebox-accordion-title outofthebox-option-title">Social Sharing Buttons</div>
            <div>
              <div class="outofthebox-option-description"><?php esc_html_e('Select which sharing buttons should be accessible via the sharing dialogs of the plugin.', 'wpcloudplugins'); ?></div>

              <div class="shareon shareon-settings">
                <?php foreach ($this->settings['share_buttons'] as $button => $value) {
              $title = ucfirst($button);
              echo "<button type='button' class='shareon-setting-button {$button} shareon-{$value} ' title='{$title}'></button>";
              echo "<input type='hidden' value='{$value}' name='out_of_the_box_settings[share_buttons][{$button}]'/>";
          }
                ?>
              </div>
            </div>
          </div>



          <div class="outofthebox-accordion">
            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Shortlinks API', 'wpcloudplugins'); ?></div>

            <div>
              <div class="outofthebox-option-description"><?php esc_html_e('Select which Url Shortener Service you want to use', 'wpcloudplugins'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[shortlinks]" id="wpcp_shortlinks">
                <option value="None"  <?php echo 'None' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>None</option>
                <option value="Shorte.st"  <?php echo 'Shorte.st' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>Shorte.st</option>
                <option value="Rebrandly"  <?php echo 'Rebrandly' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>Rebrandly</option>
                <option value="Bit.ly"  <?php echo 'Bit.ly' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>Bit.ly</option>
              </select>   

              <div class="outofthebox-suboptions option shortest" <?php echo 'Shorte.st' !== $this->settings['shortlinks'] ? "style='display:none;'" : ''; ?>>
                <div class="outofthebox-option-description"><?php esc_html_e('Sign up for Shorte.st', 'wpcloudplugins'); ?> and <a href="https://shorte<?php echo '.st/tools/api'; ?>" target="_blank">grab your API token</a></div>

                <div class="outofthebox-option-title"><?php esc_html_e('API token', 'wpcloudplugins'); ?></div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[shortest_apikey]" id="shortest_apikey" value="<?php echo esc_attr($this->settings['shortest_apikey']); ?>">
              </div>

              <div class="outofthebox-suboptions option bitly" <?php echo 'Bit.ly' !== $this->settings['shortlinks'] ? "style='display:none;'" : ''; ?>>
                <div class="outofthebox-option-description"><a href="https://bitly.com/a/sign_up" target="_blank"><?php esc_html_e('Sign up for Bitly', 'wpcloudplugins'); ?></a> and <a href="http://bitly.com/a/your_api_key" target="_blank">generate an API key</a></div>

                <div class="outofthebox-option-title">Bitly Login</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[bitly_login]" id="bitly_login" value="<?php echo esc_attr($this->settings['bitly_login']); ?>">

                <div class="outofthebox-option-title">Bitly API key</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[bitly_apikey]" id="bitly_apikey" value="<?php echo esc_attr($this->settings['bitly_apikey']); ?>">
              </div> 

              <div class="outofthebox-suboptions option rebrandly" <?php echo 'Rebrandly' !== $this->settings['shortlinks'] ? "style='display:none;'" : ''; ?>>
                <div class="outofthebox-option-description"><a href="https://app.rebrandly.com/" target="_blank"><?php esc_html_e('Sign up for Rebrandly', 'wpcloudplugins'); ?></a> and <a href="https://app.rebrandly.com/account/api-keys" target="_blank">grab your API token</a></div>

                <div class="outofthebox-option-title">Rebrandly API key</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[rebrandly_apikey]" id="rebrandly_apikey" value="<?php echo esc_attr($this->settings['rebrandly_apikey']); ?>">

                <div class="outofthebox-option-title">Rebrandly Domain (optional)</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[rebrandly_domain]" id="rebrandly_domain" value="<?php echo esc_attr($this->settings['rebrandly_domain']); ?>">
              </div>
            </div>
          </div> 

          <div class="outofthebox-accordion">

            <div class="outofthebox-accordion-title outofthebox-option-title">ReCaptcha V3         </div>
            <div>

              <div class="outofthebox-option-description"><?php esc_html_e('reCAPTCHA protects you against spam and other types of automated abuse. With this reCAPTCHA (V3) integration module, you can block abusive downloads of your files by bots. Create your own credentials via the link below.', 'wpcloudplugins'); ?> <br/><br/><a href="https://www.google.com/recaptcha/admin" target="_blank">Manage your reCAPTCHA API keys</a></div>

              <div class="outofthebox-option-title"><?php esc_html_e('Site Key', 'wpcloudplugins'); ?></div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[recaptcha_sitekey]" id="recaptcha_sitekey" value="<?php echo esc_attr($this->settings['recaptcha_sitekey']); ?>">

              <div class="outofthebox-option-title"><?php esc_html_e('Secret Key', 'wpcloudplugins'); ?></div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[recaptcha_secret]" id="recaptcha_secret" value="<?php echo esc_attr($this->settings['recaptcha_secret']); ?>">
            </div>
          </div>

          <div class="outofthebox-accordion">

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Video Advertisements (IMA/VAST)', 'wpcloudplugins'); ?> </div>
            <div>
              <div class="outofthebox-option-description"><?php esc_html_e('The mediaplayer of the plugin supports VAST XML advertisments to offer monetization options for your videos. You can enable advertisments for the complete site and per Media Player shortcode. Currently, this plugin only supports Linear elements with MP4', 'wpcloudplugins'); ?>.</div>

              <div class="outofthebox-option-title"><?php echo 'VAST XML Tag Url'; ?></div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[mediaplayer_ads_tagurl]" id="mediaplayer_ads_tagurl" value="<?php echo esc_attr($this->settings['mediaplayer_ads_tagurl']); ?>" placeholder="<?php echo esc_html__('Leave empty to disable Ads', 'wpcloudplugins'); ?>" />

              <div class="oftb-warning">
                <i><strong><?php esc_html_e('NOTICE', 'wpcloudplugins'); ?></strong>: <?php esc_html_e('If you are unable to see the example VAST url below, please make sure you do not have an ad blocker enabled.', 'wpcloudplugins'); ?>.</i>
              </div>

              <a href="https://pubads.g.doubleclick.net/gampad/ads?sz=640x480&iu=/124319096/external/single_ad_samples&ciu_szs=300x250&impl=s&gdfp_req=1&env=vp&output=vast&unviewed_position_start=1&cust_params=deployment%3Ddevsite%26sample_ct%3Dskippablelinear&correlator=" rel="no-follow">Example Tag URL</a>

              <div class="outofthebox-option-title"><?php esc_html_e('Enable Skip Button', 'wpcloudplugins'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[mediaplayer_ads_skipable]'/>
                  <input type="checkbox" name="out_of_the_box_settings[mediaplayer_ads_skipable]" id="mediaplayer_ads_skipable" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['mediaplayer_ads_skipable']) ? 'checked="checked"' : ''; ?> data-div-toggle="ads_skipable"/>
                  <label class="outofthebox-onoffswitch-label" for="mediaplayer_ads_skipable"></label>
                </div>
              </div>

              <div class="outofthebox-suboptions ads_skipable <?php echo ('Yes' === $this->settings['mediaplayer_ads_skipable']) ? '' : 'hidden'; ?> ">
                <div class="outofthebox-option-title"><?php esc_html_e('Skip button visible after (seconds)', 'wpcloudplugins'); ?></div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[mediaplayer_ads_skipable_after]" id="mediaplayer_ads_skipable_after" value="<?php echo esc_attr($this->settings['mediaplayer_ads_skipable_after']); ?>" placeholder="5">
                <div class="outofthebox-option-description"><?php esc_html_e('Allow user to skip advertisment after after the following amount of seconds have elapsed', 'wpcloudplugins'); ?></div>
              </div>
            </div>
          </div>

        </div>  
        <!-- End Integrations info -->

        <!-- Notifications Tab -->
        <div id="settings_notifications"  class="outofthebox-tab-panel">

          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Notifications', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-accordion">
            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Download Notifications', 'wpcloudplugins'); ?>         </div>
            <div>
              <div class="outofthebox-option-title"><?php esc_html_e('Subject download notification', 'wpcloudplugins'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[download_template_subject]" id="download_template_subject" value="<?php echo esc_attr($this->settings['download_template_subject']); ?>">

              <div class="outofthebox-option-title"><?php esc_html_e('Subject zip notification', 'wpcloudplugins'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[download_template_subject_zip]" id="download_template_subject_zip" value="<?php echo esc_attr($this->settings['download_template_subject_zip']); ?>">

              <div class="outofthebox-option-title"><?php esc_html_e('Template download', 'wpcloudplugins'); ?> (HTML):</div>
              <?php
              ob_start();
              wp_editor($this->settings['download_template'], 'out_of_the_box_settings_download_template', [
                  'textarea_name' => 'out_of_the_box_settings[download_template]',
                  'teeny' => true,
                  'tinymce' => false,
                  'textarea_rows' => 15,
                  'media_buttons' => false,
              ]);
              echo ob_get_clean();
              ?>

              <br/>

              <div class="outofthebox-option-description"><?php echo sprintf(esc_html__('Available placeholders: %s', 'wpcloudplugins'), ''); ?>
                <code>%site_name%</code>, 
                <code>%number_of_files%</code>, 
                <code>%user_name%</code>, 
                <code>%user_email%</code>, 
                <code>%admin_email%</code>, 
                <code>%file_name%</code>, 
                <code>%file_size%</code>, 
                <code>%file_icon%</code>, 
                <code>%file_relative_path%</code>, 
                <code>%file_absolute_path%</code>, 
                <code>%file_cloud_preview_url%</code>, 
                <code>%file_cloud_shared_url%</code>, 
                <code>%file_download_url%</code>,
                <code>%folder_name%</code>,
                <code>%folder_relative_path%</code>,
                <code>%folder_absolute_path%</code>,
                <code>%folder_url%</code>,
                <code>%ip%</code>, 
                <code>%location%</code>, 
              </div>

            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Upload Notifications', 'wpcloudplugins'); ?>         </div>
            <div>  
              <div class="outofthebox-option-title"><?php esc_html_e('Subject upload notification', 'wpcloudplugins'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[upload_template_subject]" id="upload_template_subject" value="<?php echo esc_attr($this->settings['upload_template_subject']); ?>">

              <div class="outofthebox-option-title"><?php esc_html_e('Template upload', 'wpcloudplugins'); ?> (HTML):</div>
              <?php
              ob_start();
              wp_editor($this->settings['upload_template'], 'out_of_the_box_settings_upload_template', [
                  'textarea_name' => 'out_of_the_box_settings[upload_template]',
                  'teeny' => true,
                  'tinymce' => false,
                  'textarea_rows' => 15,
                  'media_buttons' => false,
              ]);
              echo ob_get_clean();
              ?>

              <br/>

              <div class="outofthebox-option-description"><?php echo sprintf(esc_html__('Available placeholders: %s', 'wpcloudplugins'), ''); ?>
                <code>%site_name%</code>, 
                <code>%number_of_files%</code>, 
                <code>%user_name%</code>, 
                <code>%user_email%</code>, 
                <code>%admin_email%</code>, 
                <code>%file_name%</code>, 
                <code>%file_size%</code>, 
                <code>%file_icon%</code>, 
                <code>%file_relative_path%</code>, 
                <code>%file_absolute_path%</code>, 

                <code>%file_cloud_preview_url%</code>, 
                <code>%file_cloud_shared_url%</code>, 
                <code>%file_download_url%</code>,
                <code>%folder_name%</code>,
                <code>%folder_relative_path%</code>,
                <code>%folder_absolute_path%</code>,
                <code>%folder_url%</code>,
                <code>%ip%</code>, 
                <code>%location%</code>, 
              </div>

            </div>


            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Delete Notifications', 'wpcloudplugins'); ?>         </div>
            <div>
              <div class="outofthebox-option-title"><?php esc_html_e('Subject delete notification', 'wpcloudplugins'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[delete_template_subject]" id="delete_template_subject" value="<?php echo esc_attr($this->settings['delete_template_subject']); ?>">

              <div class="outofthebox-option-title"><?php esc_html_e('Template deletion', 'wpcloudplugins'); ?> (HTML):</div>

              <?php
              ob_start();
              wp_editor($this->settings['delete_template'], 'out_of_the_box_settings_delete_template', [
                  'textarea_name' => 'out_of_the_box_settings[delete_template]',
                  'teeny' => true,
                  'tinymce' => false,
                  'textarea_rows' => 15,
                  'media_buttons' => false,
              ]);
              echo ob_get_clean();
              ?>

              <br/>

              <div class="outofthebox-option-description"><?php echo sprintf(esc_html__('Available placeholders: %s', 'wpcloudplugins'), ''); ?>
                <code>%site_name%</code>, 
                <code>%number_of_files%</code>, 
                <code>%user_name%</code>, 
                <code>%user_email%</code>, 
                <code>%admin_email%</code>, 
                <code>%file_name%</code>, 
                <code>%file_size%</code>, 
                <code>%file_icon%</code>, 
                <code>%file_relative_path%</code>, 
                <code>%file_absolute_path%</code>, 
                <code>%file_cloud_preview_url%</code>, 
                <code>%file_cloud_shared_url%</code>, 
                <code>%file_download_url%</code>,
                <code>%folder_name%</code>,
                <code>%folder_relative_path%</code>,
                <code>%folder_absolute_path%</code>,
                <code>%folder_url%</code>,
                <code>%ip%</code>, 
                <code>%location%</code>, 
              </div>

            </div>


            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Template File line in %filelist%', 'wpcloudplugins'); ?>         </div>
            <div>
              <div class="outofthebox-option-description"><?php esc_html_e('Template for File item in File List in the download/upload/delete template', 'wpcloudplugins'); ?> (HTML).</div>
              <?php
              ob_start();
              wp_editor($this->settings['filelist_template'], 'out_of_the_box_settings_filelist_template', [
                  'textarea_name' => 'out_of_the_box_settings[filelist_template]',
                  'teeny' => true,
                  'tinymce' => false,
                  'textarea_rows' => 15,
                  'media_buttons' => false,
              ]);
              echo ob_get_clean();
              ?>

              <br/>

              <div class="outofthebox-option-description"><?php echo sprintf(esc_html__('Available placeholders: %s', 'wpcloudplugins'), ''); ?>
                <code>%file_name%</code>, 
                <code>%file_size%</code>, 
                <code>%file_icon%</code>, 
                <code>%file_cloud_preview_url%</code>, 
                <code>%file_cloud_shared_url%</code>, 
                <code>%file_download_url%</code>,
                <code>%file_relative_path%</code>, 
                <code>%file_absolute_path%</code>, 
                <code>%folder_relative_path%</code>,
                <code>%folder_absolute_path%</code>,
                <code>%folder_url%</code>,
              </div>


            </div>
          </div>

          <div id="wpcp_reset_notifications_button" type="button" class="simple-button blue"><?php esc_html_e('Reset to default notifications', 'wpcloudplugins'); ?>&nbsp;<div class="wpcp-spinner"></div></div>

        </div>
        <!-- End Notifications Tab -->

        <!--  Permissions Tab -->
        <div id="settings_permissions"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Permissions', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-accordion">
            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Change Plugin Settings', 'wpcloudplugins'); ?>         </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_edit_settings]', $this->settings['permissions_edit_settings']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Link Users to Private Folders', 'wpcloudplugins'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_link_users]', $this->settings['permissions_link_users']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('See Reports', 'wpcloudplugins'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_see_dashboard]', $this->settings['permissions_see_dashboard']); ?>
            </div>   

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('See Back-End Filebrowser', 'wpcloudplugins'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_see_filebrowser]', $this->settings['permissions_see_filebrowser']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Add Plugin Shortcodes', 'wpcloudplugins'); ?>         </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_add_shortcodes]', $this->settings['permissions_add_shortcodes']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Add Direct links', 'wpcloudplugins'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_add_links]', $this->settings['permissions_add_links']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php esc_html_e('Embed Documents', 'wpcloudplugins'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_add_embedded]', $this->settings['permissions_add_embedded']); ?>
            </div>

          </div>
        </div>
        <!-- End Permissions Tab -->

        <!--  Statistics Tab -->
        <div id="settings_stats"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Statistics', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-option-title"><?php esc_html_e('Log Events', 'wpcloudplugins'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[log_events]'/>
              <input type="checkbox" name="out_of_the_box_settings[log_events]" id="log_events" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['log_events']) ? 'checked="checked"' : ''; ?> data-div-toggle="events_options"/>
              <label class="outofthebox-onoffswitch-label" for="log_events"></label>
            </div>
          </div>
          <div class="outofthebox-option-description"><?php esc_html_e('Register all plugin events', 'wpcloudplugins'); ?>.</div>

          <div class="outofthebox-suboptions events_options <?php echo ('Yes' === $this->settings['log_events']) ? '' : 'hidden'; ?> ">
            <div class="outofthebox-option-title"><?php esc_html_e('Summary Email', 'wpcloudplugins'); ?>
              <div class="outofthebox-onoffswitch">
                <input type='hidden' value='No' name='out_of_the_box_settings[event_summary]'/>
                <input type="checkbox" name="out_of_the_box_settings[event_summary]" id="event_summary" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['event_summary']) ? 'checked="checked"' : ''; ?> data-div-toggle="event_summary"/>
                <label class="outofthebox-onoffswitch-label" for="event_summary"></label>
              </div>
            </div>
            <div class="outofthebox-option-description"><?php esc_html_e('Email a summary of all the events that are logged with the plugin', 'wpcloudplugins'); ?>.</div>

            <div class="event_summary <?php echo ('Yes' === $this->settings['event_summary']) ? '' : 'hidden'; ?> ">

              <div class="outofthebox-option-title"><?php esc_html_e('Interval', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e('Please select the interval the summary needs to be send', 'wpcloudplugins'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[event_summary_period]" id="event_summary_period">
                <option value="daily"  <?php echo 'daily' === $this->settings['event_summary_period'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Every day', 'wpcloudplugins'); ?></option>
                <option value="weekly"  <?php echo 'weekly' === $this->settings['event_summary_period'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Weekly', 'wpcloudplugins'); ?></option>
                <option value="monthly"  <?php echo 'monthly' === $this->settings['event_summary_period'] ? "selected='selected'" : ''; ?>><?php esc_html_e('Monthly', 'wpcloudplugins'); ?></option>
              </select>   

              <div class="outofthebox-option-title"><?php esc_html_e('Recipients', 'wpcloudplugins'); ?></div>
              <div class="outofthebox-option-description"><?php esc_html_e('Send the summary to the following email address(es)', 'wpcloudplugins'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[event_summary_recipients]" id="event_summary_recipients" value="<?php echo esc_attr($this->settings['event_summary_recipients']); ?>" placeholder="<?php echo get_option('admin_email'); ?>">  
            </div>
          </div>

          <div class="outofthebox-option-title"><?php esc_html_e('Google Analytics', 'wpcloudplugins'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[google_analytics]'/>
              <input type="checkbox" name="out_of_the_box_settings[google_analytics]" id="google_analytics" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['google_analytics']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="google_analytics"></label>
            </div>
          </div>
          <div class="outofthebox-option-description"><?php esc_html_e('Would you like to see some statistics in Google Analytics?', 'wpcloudplugins'); ?>. <?php echo sprintf(esc_html__('If you enable this feature, please make sure you already added your %s Google Analytics web tracking %s code to your site.', 'wpcloudplugins'), "<a href='https://support.google.com/analytics/answer/1008080' target='_blank'>", '</a>'); ?>.</div>
        </div>
        <!-- End Statistics Tab -->

        <!-- System info Tab -->
        <div id="settings_system"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('System information', 'wpcloudplugins'); ?></div>
          <?php echo $this->get_system_information(); ?>
        </div>
        <!-- End System info -->

        <!-- Tools Tab -->
        <div id="settings_tools"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Tools', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-option-title"><?php esc_html_e('Cache', 'wpcloudplugins'); ?></div>
          <?php echo $this->get_plugin_reset_cache_box(); ?>

          <div class="outofthebox-option-title"><?php esc_html_e('Reset to Factory Settings', 'wpcloudplugins'); ?></div>
          <?php echo $this->get_plugin_reset_plugin_box(); ?>

        </div>  
        <!-- End Tools -->

        <!-- Help Tab -->
        <div id="settings_help"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php esc_html_e('Support', 'wpcloudplugins'); ?></div>

          <div class="outofthebox-option-title"><?php esc_html_e('Support & Documentation', 'wpcloudplugins'); ?></div>
          <div id="message">
            <p><?php esc_html_e('Check the documentation of the plugin in case you encounter any problems or are looking for support.', 'wpcloudplugins'); ?></p>
            <div id='wpcp_documentation_button' type='button' class='simple-button blue'><?php esc_html_e('Open Documentation', 'wpcloudplugins'); ?></div>
          </div>
        </div>  
      </div>
      <!-- End Help info -->
    </div>
  </form>
</div>