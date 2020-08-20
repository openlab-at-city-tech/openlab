<?php
$page = isset($_GET['page']) ? '?page='.$_GET['page'] : '';
$location = get_admin_url(null, 'admin.php'.$page);
$admin_nonce = wp_create_nonce('outofthebox-admin-action');
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
        $table_html .= "<td><input value='{$value}' data-default-color='{$color['default']}'  name='out_of_the_box_settings[colors][{$color_id}]' id='colors-{$color_id}' type='text'  class='outofthebox-color-picker' data-alpha='true' ></td>";
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
        $button_html .= '<a href="javascript:void(0)" class="upload-remove">'.__('Remove Media', 'outofthebox').'</a>'."\n";
        $button_html .= '<a href="javascript:void(0)" class="upload-default">'.__('Default', 'outofthebox').'</a>'."\n";
    }

    $button_html .= '</div>';

    $button_html .= '<input id="'.esc_attr($option['id']).'" class="upload outofthebox-option-input-large" type="text" name="'.esc_attr($option['name']).'" value="'.esc_attr($field_value).'" autocomplete="off" />';
    $button_html .= '<input id="upload_image_button" class="upload_button simple-button blue" type="button" value="'.__('Select Image', 'outofthebox').'" title="'.__('Upload or select a file from the media library', 'outofthebox').'" />';

    if ($field_value !== $option['default']) {
        $button_html .= '<input id="default_image_button" class="default_image_button simple-button" type="button" value="'.__('Default', 'outofthebox').'" title="'.__('Fallback to the default value', 'outofthebox').'"  data-default="'.$option['default'].'"/>';
    }

    $button_html .= '</div>'."\n";

    return $button_html;
}
?>

<div class="outofthebox admin-settings">
  <form id="outofthebox-options" method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields('out_of_the_box_settings'); ?>
    <input type="hidden" name="action" value="update">

    <div class="wrap">
      <div class="outofthebox-header">
        <div class="outofthebox-logo"><img src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/logo64x64.png" height="64" width="64"/></div>
        <div class="outofthebox-form-buttons"> <div id="save_settings" class="simple-button default save_settings" name="save_settings"><?php _e('Save Settings', 'outofthebox'); ?>&nbsp;<div class='oftb-spinner'></div></div></div>
        <div class="outofthebox-title">Out-of-the-Box <?php _e('Settings', 'outofthebox'); ?></div>
      </div>


      <div id="" class="outofthebox-panel outofthebox-panel-left">      
        <div class="outofthebox-nav-header"><?php _e('Settings', 'outofthebox'); ?></div>

        <ul class="outofthebox-nav-tabs">
          <li id="settings_general_tab" data-tab="settings_general" class="current"><a ><?php _e('General', 'outofthebox'); ?></a></li>
          <?php
          if ($this->is_activated()) {
              ?>
              <li id="settings_layout_tab" data-tab="settings_layout" ><a ><?php _e('Layout', 'outofthebox'); ?></a></li>
              <li id="settings_userfolders_tab" data-tab="settings_userfolders" ><a ><?php _e('Private Folders', 'outofthebox'); ?></a></li>
              <li id="settings_advanced_tab" data-tab="settings_advanced" ><a ><?php _e('Advanced', 'outofthebox'); ?></a></li>
              <li id="settings_integrations_tab" data-tab="settings_integrations" ><a><?php _e('Integrations', 'outofthebox'); ?></a></li>
              <li id="settings_notifications_tab" data-tab="settings_notifications" ><a ><?php _e('Notifications', 'outofthebox'); ?></a></li>
              <li id="settings_permissions_tab" data-tab="settings_permissions" ><a><?php _e('Permissions', 'outofthebox'); ?></a></li>
              <li id="settings_stats_tab" data-tab="settings_stats" ><a><?php _e('Statistics', 'outofthebox'); ?></a></li>
              <li id="settings_system_tab" data-tab="settings_system" ><a><?php _e('System information', 'outofthebox'); ?></a></li>
              <?php
          }
          ?>
          <li id="settings_help_tab" data-tab="settings_help" ><a><?php _e('Support', 'outofthebox'); ?></a></li>

        </ul>

        <div class="outofthebox-nav-header" style="margin-top: 50px;"><?php _e('Other Cloud Plugins', 'outofthebox'); ?></div>
        <ul class="outofthebox-nav-tabs">
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/c/1260925/275988/4415?u=https%3A%2F%2Fcodecanyon.net%2Fitem%2Fuseyourdrive-google-drive-plugin-for-wordpress%2F6219776" target="_blank" style="color:#0078d7;">Google Drive <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/c/1260925/275988/4415?u=https%3A%2F%2Fcodecanyon.net%2Fitem%2Fshareonedrive-onedrive-plugin-for-wordpress%2F11453104%3Fref%3D_DeLeeuw_" target="_blank" style="color:#0078d7;">OneDrive <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
          <li id="settings_help_tab" data-tab="settings_help"><a href="https://1.envato.market/c/1260925/275988/4415?u=https%3A%2F%2Fcodecanyon.net%2Fitem%2Fletsbox-box-plugin-for-wordpress%2F8204640" target="_blank" style="color:#0078d7;">Box <i class="fas fa-external-link-square-alt" aria-hidden="true"></i></a></li>
        </ul> 

        <div class="outofthebox-nav-footer"><a href="<?php echo admin_url('update-core.php'); ?>"><?php _e('Version', 'outofthebox'); ?>: <?php echo OUTOFTHEBOX_VERSION; ?></a></div>
      </div>


      <div class="outofthebox-panel outofthebox-panel-right">

        <!-- General Tab -->
        <div id="settings_general" class="outofthebox-tab-panel current">

          <div class="outofthebox-tab-panel-header"><?php _e('General', 'outofthebox'); ?></div>

          <?php if ($this->is_activated()) { ?>
              <div class="outofthebox-option-title"><?php _e('Accounts', 'outofthebox'); ?></div>
              <div class="outofthebox-accounts-list">
                <?php
                if (false === $this->get_processor()->is_network_authorized() || ($this->get_processor()->is_network_authorized() && true === is_network_admin())) {
                    $app = $this->get_app(); ?>
                    <div class='account account-new'>
                      <img class='account-image' src='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/dropbox_logo.png'/>
                      <div class='account-info-container'>
                        <div class='account-info'>
                          <div class='account-actions'>
                            <div id='add_dropbox_button' type='button' class='simple-button blue' data-url="<?php echo $app->get_auth_url(['force_reapprove' => 'true']); ?>" title="<?php _e('Add account', 'outofthebox'); ?>"><i class='fas fa-plus-circle' aria-hidden='true'></i>&nbsp;<?php _e('Add account', 'outofthebox'); ?></div>
                          </div>
                          <div class="account-info-name">
                            <?php _e('Add account', 'outofthebox'); ?>
                          </div>
                          <span class="account-info-space"><?php _e('Link a new account to the plugin', 'outofthebox'); ?></span>
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
                          <span class="account-info-space"><?php echo sprintf(__("The authorization is managed by the Network Admin via the <a href='%s'>Network Settings Page</a> of the plugin", 'outofthebox'), network_admin_url('admin.php?page=OutoftheBox_network_settings')); ?>.</span>
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
          <div class="outofthebox-option-title"><?php _e('Plugin License', 'outofthebox'); ?></div>
          <?php
          echo $this->get_plugin_activated_box();
          ?>
        </div>
        <!-- End General Tab -->


        <!-- Layout Tab -->
        <div id="settings_layout"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('Layout', 'outofthebox'); ?></div>

          <div class="outofthebox-accordion">

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Loading Spinner & Images', 'outofthebox'); ?>         </div>
            <div>

              <div class="outofthebox-option-title"><?php _e('Select Loader Spinner', 'outofthebox'); ?></div>
              <select type="text" name="out_of_the_box_settings[loaders][style]" id="loader_style">
                <option value="beat" <?php echo 'beat' === $this->settings['loaders']['style'] ? "selected='selected'" : ''; ?>><?php _e('Beat', 'outofthebox'); ?></option>
                <option value="spinner" <?php echo 'spinner' === $this->settings['loaders']['style'] ? "selected='selected'" : ''; ?>><?php _e('Spinner', 'outofthebox'); ?></option>
                <option value="custom" <?php echo 'custom' === $this->settings['loaders']['style'] ? "selected='selected'" : ''; ?>><?php _e('Custom Image (selected below)', 'outofthebox'); ?></option>
              </select>

              <div class="outofthebox-option-title"><?php _e('General Loader', 'outofthebox'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['loading'], 'id' => 'loaders_loading', 'name' => 'out_of_the_box_settings[loaders][loading]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_loading.gif'];
              echo create_upload_button_for_custom_images($button);
              ?>
              <div class="outofthebox-option-title"><?php _e('Upload Loader', 'outofthebox'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['upload'], 'id' => 'loaders_upload', 'name' => 'out_of_the_box_settings[loaders][upload]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_upload.gif'];
              echo create_upload_button_for_custom_images($button);
              ?>
              <div class="outofthebox-option-title"><?php _e('No Results', 'outofthebox'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['no_results'], 'id' => 'loaders_no_results', 'name' => 'out_of_the_box_settings[loaders][no_results]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_no_results.png'];
              echo create_upload_button_for_custom_images($button);
              ?>
              <div class="outofthebox-option-title"><?php _e('Access Forbidden Image', 'outofthebox'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['protected'], 'id' => 'loaders_protected', 'name' => 'out_of_the_box_settings[loaders][protected]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_protected.png'];
              echo create_upload_button_for_custom_images($button);
              ?>
              <div class="outofthebox-option-title"><?php _e('Error Image', 'outofthebox'); ?></div>
              <?php
              $button = ['value' => $this->settings['loaders']['error'], 'id' => 'loaders_error', 'name' => 'out_of_the_box_settings[loaders][error]', 'default' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_error.png'];
              echo create_upload_button_for_custom_images($button);
              ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Color Palette', 'outofthebox'); ?></div>
            <div>

              <div class="outofthebox-option-title"><?php _e('Content Skin', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e('Select the general content skin', 'outofthebox'); ?>.</div>
              <select name="skin_selectbox" id="content_skin_selectbox" class="ddslickbox">
                <option value="dark" <?php echo 'dark' === $this->settings['colors']['style'] ? "selected='selected'" : ''; ?> data-imagesrc="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/skin-dark.png" data-description=""><?php _e('Dark', 'outofthebox'); ?></option>
                <option value="light" <?php echo 'light' === $this->settings['colors']['style'] ? "selected='selected'" : ''; ?> data-imagesrc="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/skin-light.png" data-description=""><?php _e('Light', 'outofthebox'); ?></option>
              </select>
              <input type="hidden" name="out_of_the_box_settings[colors][style]" id="content_skin" value="<?php echo esc_attr($this->settings['colors']['style']); ?>">

              <?php
              $colors = [
                  'background' => [
                      'label' => __('Content Background Color', 'outofthebox'),
                      'default' => '#f2f2f2',
                  ],
                  'accent' => [
                      'label' => __('Accent Color', 'outofthebox'),
                      'default' => '#29ADE2',
                  ],
                  'black' => [
                      'label' => __('Black', 'outofthebox'),
                      'default' => '#222',
                  ],
                  'dark1' => [
                      'label' => __('Dark 1', 'outofthebox'),
                      'default' => '#666666',
                  ],
                  'dark2' => [
                      'label' => __('Dark 2', 'outofthebox'),
                      'default' => '#999999',
                  ],
                  'white' => [
                      'label' => __('White', 'outofthebox'),
                      'default' => '#fff',
                  ],
                  'light1' => [
                      'label' => __('Light 1', 'outofthebox'),
                      'default' => '#fcfcfc',
                  ],
                  'light2' => [
                      'label' => __('Light 2', 'outofthebox'),
                      'default' => '#e8e8e8',
                  ],
              ];

              echo create_color_boxes_table($colors, $this->settings);
              ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Icons', 'outofthebox'); ?></div>
            <div>

              <div class="outofthebox-option-title"><?php _e('Icon Set', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e(sprintf('Location to the icon set you want to use. When you want to use your own set, just make a copy of the default icon set folder (<code>%s</code>) and place it in the <code>wp-content/</code> folder', OUTOFTHEBOX_ROOTPATH.'/css/icons/'), 'outofthebox'); ?>.</div>

              <div class="oftb-warning">
                <i><strong><?php _e('NOTICE', 'outofthebox'); ?></strong>: <?php _e('Modifications to the default icons set will be lost during an update.', 'outofthebox'); ?>.</i>
              </div>

              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[icon_set]" id="icon_set" value="<?php echo esc_attr($this->settings['icon_set']); ?>">  
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Lightbox', 'outofthebox'); ?></div>
            <div>
              <div class="outofthebox-option-title"><?php _e('Lightbox Skin', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e('Select which skin you want to use for the lightbox', 'outofthebox'); ?>.</div>
              <select name="lightbox_skin_selectbox" id="lightbox_skin_selectbox" class="ddslickbox">
                <?php
                foreach (new DirectoryIterator(OUTOFTHEBOX_ROOTDIR.'/includes/iLightBox/') as $fileInfo) {
                    if ($fileInfo->isDir() && !$fileInfo->isDot() && (false !== strpos($fileInfo->getFilename(), 'skin'))) {
                        if (file_exists(OUTOFTHEBOX_ROOTDIR.'/includes/iLightBox/'.$fileInfo->getFilename().'/skin.css')) {
                            $selected = '';
                            $skinname = str_replace('-skin', '', $fileInfo->getFilename());

                            if ($skinname === $this->settings['lightbox_skin']) {
                                $selected = 'selected="selected"';
                            }

                            $icon = file_exists(OUTOFTHEBOX_ROOTDIR.'/includes/iLightBox/'.$fileInfo->getFilename().'/thumb.jpg') ? OUTOFTHEBOX_ROOTPATH.'/includes/iLightBox/'.$fileInfo->getFilename().'/thumb.jpg' : '';
                            echo '<option value="'.$skinname.'" data-imagesrc="'.$icon.'" data-description="" '.$selected.'>'.$fileInfo->getFilename()."</option>\n";
                        }
                    }
                }
                ?>
              </select>
              <input type="hidden" name="out_of_the_box_settings[lightbox_skin]" id="lightbox_skin" value="<?php echo esc_attr($this->settings['lightbox_skin']); ?>">


              <div class="outofthebox-option-title"><?php _e('Lightbox Scroll', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e("Sets path for switching windows. Possible values are 'vertical' and 'horizontal' and the default is 'vertical", 'outofthebox'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[lightbox_path]" id="lightbox_path">
                <option value="horizontal" <?php echo 'horizontal' === $this->settings['lightbox_path'] ? "selected='selected'" : ''; ?>>Horizontal</option>
                <option value="vertical" <?php echo 'vertical' === $this->settings['lightbox_path'] ? "selected='selected'" : ''; ?>>Vertical</option>
              </select>

              <div class="outofthebox-option-title"><?php _e('Lightbox Image Source', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e('Select the source of the images. Large thumbnails will load fast once created on your server, (raw) original files can take some time to load', 'outofthebox'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[loadimages]" id="loadimages">
                <option value="thumbnail" <?php echo 'thumbnail' === $this->settings['loadimages'] ? "selected='selected'" : ''; ?>><?php _e('Fast - Large preview thumbnails', 'outofthebox'); ?></option>
                <option value="original" <?php echo 'original' === $this->settings['loadimages'] ? "selected='selected'" : ''; ?>><?php _e('Slow - Show orginal files', 'outofthebox'); ?></option>
              </select>

              <div class="outofthebox-option-title"><?php _e('Allow Mouse Click on Image', 'outofthebox'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[lightbox_rightclick]'/>
                  <input type="checkbox" name="out_of_the_box_settings[lightbox_rightclick]" id="lightbox_rightclick" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['lightbox_rightclick']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="lightbox_rightclick"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php _e('Should people be able to access the right click context menu to e.g. save the image?', 'outofthebox'); ?>.</div>

              <div class="outofthebox-option-title"><?php _e('Lightbox Caption', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e('Choose when the caption containing the title and (if available) description are shown', 'outofthebox'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[lightbox_showcaption]" id="lightbox_showcaption">
                <option value="click" <?php echo 'click' === $this->settings['lightbox_showcaption'] ? "selected='selected'" : ''; ?>><?php _e('Show caption after clicking on the Lightbox', 'outofthebox'); ?></option>
                <option value="mouseenter" <?php echo 'mouseenter' === $this->settings['lightbox_showcaption'] ? "selected='selected'" : ''; ?>><?php _e('Show caption when Lightbox opens', 'outofthebox'); ?></option>
              </select>              



            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Media Player', 'outofthebox'); ?></div>
            <div> 
              <div class="outofthebox-option-description"><?php _e('Select which Media Player you want to use', 'outofthebox'); ?>.</div>
              <select name="mediaplayer_skin_selectbox" id="mediaplayer_skin_selectbox" class="ddslickbox">
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
              <input type="hidden" name="out_of_the_box_settings[mediaplayer_skin]" id="mediaplayer_skin" value="<?php echo esc_attr($this->settings['mediaplayer_skin']); ?>">

            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Custom CSS', 'outofthebox'); ?></div>
            <div>
              <div class="outofthebox-option-title"><?php _e('Custom CSS', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e("If you want to modify the looks of the plugin slightly, you can insert here your custom CSS. Don't edit the CSS files itself, because those modifications will be lost during an update.", 'outofthebox'); ?>.</div>
              <textarea name="out_of_the_box_settings[custom_css]" id="custom_css" cols="" rows="10"><?php echo esc_attr($this->settings['custom_css']); ?></textarea>
            </div>
          </div>
        </div>
        <!-- End Layout Tab -->

        <!-- UserFolders Tab -->
        <div id="settings_userfolders"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('Private Folders', 'outofthebox'); ?></div>

          <div class="outofthebox-accordion">

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Global settings Automatically linked Private Folders', 'outofthebox'); ?> </div>
            <div>

              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: <?php _e('The following settings are only used for all shortcodes with automatically linked Private Folders', 'outofthebox'); ?>. </i>
              </div>


              <div class="outofthebox-option-title"><?php _e('Create Private Folders on registration', 'outofthebox'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[userfolder_oncreation]'/>
                  <input type="checkbox" name="out_of_the_box_settings[userfolder_oncreation]" id="userfolder_oncreation" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['userfolder_oncreation']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="userfolder_oncreation"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php _e('Create a new Private Folders automatically after a new user has been created', 'outofthebox'); ?>.</div>

              <div class="outofthebox-option-title"><?php _e('Create all Private Folders on first visit', 'outofthebox'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[userfolder_onfirstvisit]'/>
                  <input type="checkbox" name="out_of_the_box_settings[userfolder_onfirstvisit]" id="userfolder_onfirstvisit" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['userfolder_onfirstvisit']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="userfolder_onfirstvisit"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php _e('Create all Private Folders on first visit', 'outofthebox'); ?>.</div>
              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: Creating User Folders takes around 1 sec per user, so it isn't recommended to create those on first visit when you have tons of users.</i>
              </div>


              <div class="outofthebox-option-title"><?php _e('Update Private Folders after profile update', 'outofthebox'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[userfolder_update]'/>
                  <input type="checkbox" name="out_of_the_box_settings[userfolder_update]" id="userfolder_update" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['userfolder_update']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="userfolder_update"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php _e('Update the folder name of the user after they have updated their profile', 'outofthebox'); ?>.</div>

              <div class="outofthebox-option-title"><?php _e('Remove Private Folders after account removal', 'outofthebox'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[userfolder_remove]'/>
                  <input type="checkbox" name="out_of_the_box_settings[userfolder_remove]" id="userfolder_remove" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['userfolder_remove']) ? 'checked="checked"' : ''; ?> />
                  <label class="outofthebox-onoffswitch-label" for="userfolder_remove"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php _e('Try to remove Private Folders after they are deleted', 'outofthebox'); ?>.</div>

              <div class="outofthebox-option-title"><?php _e('Name Template', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e('Template name for automatically created Private Folders. You can use <code>%user_login%</code>, <code>%user_email%</code>, <code>%display_name%</code>, <code>%ID%</code>, <code>%user_role%</code>, <code>%jjjj-mm-dd%</code>', 'outofthebox'); ?>.</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[userfolder_name]" id="userfolder_name" value="<?php echo esc_attr($this->settings['userfolder_name']); ?>">

                          </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Global settings Manually linked Private Folders', 'outofthebox'); ?> </div>
            <div>

              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: <?php echo sprintf(__('You can manually link users to their Private Folder via the %s[Link Private Folders]%s menu page', 'outofthebox'), '<a href="'.admin_url('admin.php?page=OutoftheBox_settings_linkusers').'" target="_blank">', '</a>'); ?>. </i>
              </div>

              <div class="outofthebox-option-title"><?php _e('No Access message', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e("Message that is displayed when an user is visiting a shortcode with the Private Folders feature set to 'Manual' mode while it doesn't have Private Folder linked to its account", 'outofthebox'); ?>.</div>

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
                <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Private Folders in WP Admin Dashboard', 'outofthebox'); ?> </div>
                <div>

                  <div class="oftb-warning">
                    <i><strong>NOTICE</strong>: <?php _e('This setting only restrict access of the File Browsers in the Admin Dashboard (e.g. the ones in the Shortcode Builder and the File Browser menu). To enable Private Folders for your own Shortcodes, use the Shortcode Builder', 'outofthebox'); ?>. </i>
                  </div>

                  <div class="outofthebox-option-description"><?php _e('Enables Private Folders in the Shortcode Builder and Back-End File Browser', 'outofthebox'); ?>.</div>
                  <select type="text" name="out_of_the_box_settings[userfolder_backend]" id="userfolder_backend" data-div-toggle="private-folders-auto" data-div-toggle-value="auto">
                    <option value="No" <?php echo 'No' === $this->settings['userfolder_backend'] ? "selected='selected'" : ''; ?>>No</option>
                    <option value="manual" <?php echo 'manual' === $this->settings['userfolder_backend'] ? "selected='selected'" : ''; ?>><?php _e('Yes, I link the users Manually', 'outofthebox'); ?></option>
                    <option value="auto" <?php echo 'auto' === $this->settings['userfolder_backend'] ? "selected='selected'" : ''; ?>><?php _e('Yes, let the plugin create the User Folders for me', 'outofthebox'); ?></option>
                  </select>
                  <div class="outofthebox-suboptions private-folders-auto <?php echo ('auto' === ($this->settings['userfolder_backend'])) ? '' : 'hidden'; ?> ">
                    <div class="outofthebox-option-title"><?php _e('Root folder for Private Folders', 'outofthebox'); ?></div>
                    <div class="outofthebox-option-description"><?php _e('Select in which folder the Private Folders should be created', 'outofthebox'); ?>. <?php _e('Current selected folder', 'outofthebox'); ?>:</div>
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
                    <div id="root_folder_button" type="button" class="button-primary private-folders-auto-button"><?php _e('Select Folder', 'outofthebox'); ?>&nbsp;<div class='oftb-spinner'></div></div>

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
                                  'showcolumnnames' => '0',
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
                    <div class="outofthebox-option-title"><?php _e('Full Access', 'outofthebox'); ?></div>
                    <div class="outofthebox-option-description"><?php _e('By default only Administrator users will be able to navigate through all Private Folders', 'outofthebox'); ?>. <?php _e('When you want other User Roles to be able do browse to the Private Folders as well, please check them below', 'outofthebox'); ?>.</div>

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
          <div class="outofthebox-tab-panel-header"><?php _e('Advanced', 'outofthebox'); ?></div>

          <?php if (false === $network_wide_authorization) { ?>
              <div class="outofthebox-option-title"><?php _e('"Lost Authorization" notification', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e('If the plugin somehow loses its authorization, a notification email will be send to the following email address', 'outofthebox'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[lostauthorization_notification]" id="lostauthorization_notification" value="<?php echo esc_attr($this->settings['lostauthorization_notification']); ?>">

              <div class="outofthebox-option-title"><?php _e('Own Dropbox App', 'outofthebox'); ?>
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
                    <i><strong>NOTICE</strong>: If you encounter any issues when trying to use your own App with Out-of-the-Box, please fall back on the default App by disabling this setting.</i>
                  </div>
                </div>

                <div class="outofthebox-option-title"><?php _e('Dropbox App Key', 'outofthebox'); ?></div>
                <div class="outofthebox-option-description"><?php _e('<strong>Only</strong> if you want to use your own App, insert your Dropbox App Key here', 'outofthebox'); ?>.</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[dropbox_app_key]" id="dropbox_app_key" value="<?php echo esc_attr($this->settings['dropbox_app_key']); ?>" placeholder="<--- <?php _e('Leave empty for easy setup', 'outofthebox'); ?> --->" >

                <div class="outofthebox-option-title"><?php _e('Dropbox App Secret', 'outofthebox'); ?></div>
                <div class="outofthebox-option-description"><?php _e('If you want to use your own App, insert your Dropbox App Secret here', 'outofthebox'); ?>.</div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[dropbox_app_secret]" id="dropbox_app_secret" value="<?php echo esc_attr($this->settings['dropbox_app_secret']); ?>" placeholder="<--- <?php _e('Leave empty for easy setup', 'outofthebox'); ?> --->" >   

                <div>
                  <div class="outofthebox-option-title"><?php _e('OAuth 2.0 Redirect URI', 'outofthebox'); ?></div>
                  <div class="outofthebox-option-description"><?php _e('Set the redirect URI in your application to the following', 'outofthebox'); ?>:</div>
                  <code style="user-select:initial">
                    <?php
                    if ($this->get_app()->has_plugin_own_app()) {
                        echo $this->get_app()->get_redirect_uri();
                    } else {
                        _e('Enter Client Key and Secret, save settings and reload the page to see the Redirect URI you will need', 'outofthebox');
                    }
                    ?>
                  </code>
                </div>
              </div>

              <?php
              //if (!empty($account_type) && $account_type === 'business') {
              ?>

              <div class="outofthebox-option-title"><?php _e('Business Accounts', 'outofthebox'); ?> | <?php _e('Dropbox Team Folders', 'outofthebox'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[use_team_folders]'/>
                  <input type="checkbox" name="out_of_the_box_settings[use_team_folders]" id="use_team_folders" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['use_team_folders']) ? 'checked="checked"' : ''; ?>/>
                  <label class="outofthebox-onoffswitch-label" for="use_team_folders"></label>
                </div>
              </div>
              <div class="outofthebox-option-description"><?php _e('Allows you to access your Dropbox Team Folders', 'outofthebox'); ?>.</div>

              <div class="oftb-warning">
                <i><strong>NOTICE</strong>: <?php _e('Please check your existing Shortcodes and Manually linked Private Folders when changing this setting. Your Dropbox root folder will not longer be your Personal Folder when Team Folders are enabled', 'outofthebox'); ?>.</i>
              </div>

              <?php
              //}
          }
          ?>
          <div class="outofthebox-option-title"><?php _e('Load Javascripts on all pages', 'outofthebox'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[always_load_scripts]'/>
              <input type="checkbox" name="out_of_the_box_settings[always_load_scripts]" id="always_load_scripts" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['always_load_scripts']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="always_load_scripts"></label>
            </div>
            <div class="outofthebox-option-description"><?php _e('By default the plugin will only load it scripts when the shortcode is present on the page. If you are dynamically loading content via AJAX calls and the plugin does not show up, please enable this setting', 'outofthebox'); ?>.</div>
          </div>

          <div class="outofthebox-option-title"><?php _e('Enable Font Awesome Library v4 compatibility', 'outofthebox'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[fontawesomev4_shim]'/>
              <input type="checkbox" name="out_of_the_box_settings[fontawesomev4_shim]" id="fontawesomev4_shim" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['fontawesomev4_shim']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="fontawesomev4_shim"></label>
            </div>
            <div class="outofthebox-option-description"><?php _e('If your theme is loading the old Font Awesome icon library (v4), it can cause conflict with the (v5) icons of this plugin. If you are having trouble with the icons, please enable this setting for backwards compatibility', 'outofthebox'); ?>. <?php _e('To disable the Font Awesome library of this plugin completely, add this to your wp-config.php file', 'outofthebox'); ?>: <code>define('WPCP_DISABLE_FONTAWESOME', true);</code></div>
          </div>               

          <div class="outofthebox-option-title"><?php _e('Enable Gzip compression', 'outofthebox'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[gzipcompression]'/>
              <input type="checkbox" name="out_of_the_box_settings[gzipcompression]" id="gzipcompression" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['gzipcompression']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="gzipcompression"></label>
            </div>
          </div>
          <div class="outofthebox-option-description">Enables gzip-compression if the visitor's browser can handle it. This will increase the performance of the plugin if you are displaying large amounts of files and it reduces bandwidth usage as well. It uses the PHP <code>ob_gzhandler()</code> callback.</div>
          <div class="oftb-warning">
            <i><strong>NOTICE</strong>: Please use this setting with caution. Always test if the plugin still works on the Front-End as some servers are already configured to gzip content!</i>
          </div>

          <div class="outofthebox-option-title"><?php _e('Nonce Validation', 'outofthebox'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[nonce_validation]'/>
              <input type="checkbox" name="out_of_the_box_settings[nonce_validation]" id="nonce_validation" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['nonce_validation']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="nonce_validation"></label>
            </div></div>
          <div class="outofthebox-option-description"><?php _e('The plugin uses, among others, the WordPress Nonce system to protect you against several types of attacks including CSRF. Disable this in case you are encountering a conflict with a plugin that alters this system', 'outofthebox'); ?>. </div>
          <div class="oftb-warning">
            <i><strong>NOTICE</strong>: Please use this setting with caution! Only disable it when really necessary.</i>
          </div>

          <div class="outofthebox-option-title"><?php _e('Max Age Cache Request', 'outofthebox'); ?></div>
          <div class="outofthebox-option-description"><?php _e('How long are the requests to view the plugin cached? Number is in minutes', 'outofthebox'); ?>.</div>
          <input type="text" name="out_of_the_box_settings[request_cache_max_age]" id="request_cache_max_age" value="<?php echo esc_attr($this->settings['request_cache_max_age']); ?>" maxlength="3" size="3" >   <?php _e('Minutes'); ?>

          <div class="outofthebox-option-title"><?php _e('Download method', 'outofthebox'); ?></div>
          <div class="outofthebox-option-description"><?php _e('Select the method that should be used to download your files. Default is to redirect the user to a temporarily url on the Dropbox Server. If you want to use your server as a proxy to the Dropbox Server just set it to Download via Server', 'outofthebox'); ?>.</div>
          <select type="text" name="out_of_the_box_settings[download_method]" id="download_method">
            <option value="redirect" <?php echo 'redirect' === $this->settings['download_method'] ? "selected='selected'" : ''; ?>><?php _e('Redirect to download url (fast)', 'outofthebox'); ?></option>
            <option value="proxy" <?php echo 'proxy' === $this->settings['download_method'] ? "selected='selected'" : ''; ?>><?php _e('Use your Server as proxy (slow)', 'outofthebox'); ?></option>
          </select>   

          <div class="outofthebox-option-title"><?php _e('Shortlinks API', 'outofthebox'); ?></div>
          <div class="outofthebox-option-description"><?php _e('Select which Url Shortener Service you want to use', 'outofthebox'); ?>.</div>
          <select type="text" name="out_of_the_box_settings[shortlinks]" id="shortlinks">
            <option value="None"  <?php echo 'None' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>None</option>
            <option value="Dropbox"  <?php echo 'Dropbox' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>Dropbox Urlshortener</option>
            <option value="Shorte.st"  <?php echo 'Shorte.st' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>Shorte.st</option>
            <option value="Rebrandly"  <?php echo 'Rebrandly' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>Rebrandly</option>
            <option value="Bit.ly"  <?php echo 'Bit.ly' === $this->settings['shortlinks'] ? "selected='selected'" : ''; ?>>Bit.ly</option>
          </select>   

          <div class="outofthebox-suboptions option shortest" <?php echo 'Shorte.st' !== $this->settings['shortlinks'] ? "style='display:none;'" : ''; ?>>
            <div class="outofthebox-option-description"><?php _e('Sign up for Shorte.st', 'outofthebox'); ?> and <a href="https://shorte<?php echo '.st/tools/api'; ?>" target="_blank">grab your API token</a></div>

            <div class="outofthebox-option-title"><?php _e('API token', 'outofthebox'); ?></div>
            <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[shortest_apikey]" id="shortest_apikey" value="<?php echo esc_attr($this->settings['shortest_apikey']); ?>">
          </div>

          <div class="outofthebox-suboptions option bitly" <?php echo 'Bit.ly' !== $this->settings['shortlinks'] ? "style='display:none;'" : ''; ?>>
            <div class="outofthebox-option-description"><a href="https://bitly.com/a/sign_up" target="_blank"><?php _e('Sign up for Bitly', 'outofthebox'); ?></a> and <a href="http://bitly.com/a/your_api_key" target="_blank">generate an API key</a></div>

            <div class="outofthebox-option-title"><?php _e('Bitly login', 'outofthebox'); ?></div>
            <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[bitly_login]" id="bitly_login" value="<?php echo esc_attr($this->settings['bitly_login']); ?>">

            <div class="outofthebox-option-title"><?php _e('Bitly apiKey', 'outofthebox'); ?></div>
            <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[bitly_apikey]" id="bitly_apikey" value="<?php echo esc_attr($this->settings['bitly_apikey']); ?>">
          </div> 

          <div class="outofthebox-suboptions option rebrandly" <?php echo 'Rebrandly' !== $this->settings['shortlinks'] ? "style='display:none;'" : ''; ?>>
            <div class="outofthebox-option-description"><a href="https://app.rebrandly.com/" target="_blank"><?php _e('Sign up for Rebrandly', 'outofthebox'); ?></a> and <a href="https://app.rebrandly.com/account/api-keys" target="_blank">grab your API token</a></div>

            <div class="outofthebox-option-title"><?php _e('Rebrandly apiKey', 'outofthebox'); ?></div>
            <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[rebrandly_apikey]" id="rebrandly_apikey" value="<?php echo esc_attr($this->settings['rebrandly_apikey']); ?>">

            <div class="outofthebox-option-title"><?php _e('Rebrandly Domain (optional)', 'outofthebox'); ?></div>
            <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[rebrandly_domain]" id="rebrandly_domain" value="<?php echo esc_attr($this->settings['rebrandly_domain']); ?>">

          </div> 


        </div>
        <!-- End Advanced Tab -->

        <!-- Integrations Tab -->
        <div id="settings_integrations"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('Integrations', 'outofthebox'); ?></div>

          <div class="outofthebox-accordion">

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('ReCaptcha V3', 'outofthebox'); ?>         </div>
            <div>

              <div class="outofthebox-option-description"><?php _e('reCAPTCHA protects you against spam and other types of automated abuse. With this reCAPTCHA (V3) integration module, you can block abusive downloads of your files by bots. Create your own credentials via the link below.', 'outofthebox'); ?> <br/><br/><a href="https://www.google.com/recaptcha/admin" target="_blank">Manage your reCAPTCHA API keys</a></div>

              <div class="outofthebox-option-title"><?php _e('Site Key', 'outofthebox'); ?></div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[recaptcha_sitekey]" id="recaptcha_sitekey" value="<?php echo esc_attr($this->settings['recaptcha_sitekey']); ?>">

              <div class="outofthebox-option-title"><?php _e('Secret Key', 'outofthebox'); ?></div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[recaptcha_secret]" id="recaptcha_secret" value="<?php echo esc_attr($this->settings['recaptcha_secret']); ?>">
            </div>
          </div>

          <div class="outofthebox-accordion">

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Video Advertisements (IMA/VAST)', 'outofthebox'); ?> </div>
            <div>
              <div class="outofthebox-option-description"><?php _e('The mediaplayer of the plugin supports VAST XML advertisments to offer monetization options for your videos. You can enable advertisments for the complete site and per Media Player shortcode. Currently, this plugin only supports Linear elements with MP4', 'outofthebox'); ?>.</div>

              <div class="outofthebox-option-title"><?php echo 'VAST XML Tag Url'; ?></div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[mediaplayer_ads_tagurl]" id="mediaplayer_ads_tagurl" value="<?php echo esc_attr($this->settings['mediaplayer_ads_tagurl']); ?>" placeholder="<?php echo __('Leave empty to disable Ads', 'outofthebox'); ?>" />

              <div class="oftb-warning">
                <i><strong><?php _e('NOTICE', 'outofthebox'); ?></strong>: <?php _e('If you are unable to see the example VAST url below, please make sure you do not have an ad blocker enabled.', 'outofthebox'); ?>.</i>
              </div>

              <a href="https://pubads.g.doubleclick.net/gampad/ads?sz=640x480&iu=/124319096/external/single_ad_samples&ciu_szs=300x250&impl=s&gdfp_req=1&env=vp&output=vast&unviewed_position_start=1&cust_params=deployment%3Ddevsite%26sample_ct%3Dskippablelinear&correlator=" rel="no-follow">Example Tag URL</a>

              <div class="outofthebox-option-title"><?php _e('Enable Skip Button', 'outofthebox'); ?>
                <div class="outofthebox-onoffswitch">
                  <input type='hidden' value='No' name='out_of_the_box_settings[mediaplayer_ads_skipable]'/>
                  <input type="checkbox" name="out_of_the_box_settings[mediaplayer_ads_skipable]" id="mediaplayer_ads_skipable" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['mediaplayer_ads_skipable']) ? 'checked="checked"' : ''; ?> data-div-toggle="ads_skipable"/>
                  <label class="outofthebox-onoffswitch-label" for="mediaplayer_ads_skipable"></label>
                </div>
              </div>

              <div class="outofthebox-suboptions ads_skipable <?php echo ('Yes' === $this->settings['mediaplayer_ads_skipable']) ? '' : 'hidden'; ?> ">
                <div class="outofthebox-option-title"><?php _e('Skip button visible after (seconds)', 'outofthebox'); ?></div>
                <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[mediaplayer_ads_skipable_after]" id="mediaplayer_ads_skipable_after" value="<?php echo esc_attr($this->settings['mediaplayer_ads_skipable_after']); ?>" placeholder="5">
                <div class="outofthebox-option-description"><?php _e('Allow user to skip advertisment after after the following amount of seconds have elapsed', 'outofthebox'); ?></div>
              </div>
            </div>
          </div>

        </div>  
        <!-- End Integrations info -->

        <!-- Notifications Tab -->
        <div id="settings_notifications"  class="outofthebox-tab-panel">

          <div class="outofthebox-tab-panel-header"><?php _e('Notifications', 'outofthebox'); ?></div>

          <div class="outofthebox-accordion">
            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Download Notifications', 'outofthebox'); ?>         </div>
            <div>
              <div class="outofthebox-option-title"><?php _e('Subject download notification', 'outofthebox'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[download_template_subject]" id="download_template_subject" value="<?php echo esc_attr($this->settings['download_template_subject']); ?>">

              <div class="outofthebox-option-title"><?php _e('Subject zip notification', 'outofthebox'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[download_template_subject_zip]" id="download_template_subject_zip" value="<?php echo esc_attr($this->settings['download_template_subject_zip']); ?>">

              <div class="outofthebox-option-title"><?php _e('Template download', 'outofthebox'); ?> (HTML):</div>
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

              <div class="outofthebox-option-description"><?php _e('Available placeholders', 'outofthebox'); ?>: 
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
                <code>%file_url%</code>, 
                <code>%folder_name%</code>,
                <code>%folder_relative_path%</code>,
                <code>%folder_absolute_path%</code>,
                <code>%folder_url%</code>,
                <code>%ip%</code>, 
                <code>%location%</code>, 
              </div>

            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Upload Notifications', 'outofthebox'); ?>         </div>
            <div>  
              <div class="outofthebox-option-title"><?php _e('Subject upload notification', 'outofthebox'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[upload_template_subject]" id="upload_template_subject" value="<?php echo esc_attr($this->settings['upload_template_subject']); ?>">

              <div class="outofthebox-option-title"><?php _e('Template upload', 'outofthebox'); ?> (HTML):</div>
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

              <div class="outofthebox-option-description"><?php _e('Available placeholders', 'outofthebox'); ?>: 
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
                <code>%file_url%</code>, 
                <code>%folder_name%</code>,
                <code>%folder_relative_path%</code>,
                <code>%folder_absolute_path%</code>,
                <code>%folder_url%</code>,
                <code>%ip%</code>, 
                <code>%location%</code>, 
              </div>

            </div>


            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Delete Notifications', 'outofthebox'); ?>         </div>
            <div>
              <div class="outofthebox-option-title"><?php _e('Subject delete notification', 'outofthebox'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[delete_template_subject]" id="delete_template_subject" value="<?php echo esc_attr($this->settings['delete_template_subject']); ?>">

              <div class="outofthebox-option-title"><?php _e('Template deletion', 'outofthebox'); ?> (HTML):</div>

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

              <div class="outofthebox-option-description"><?php _e('Available placeholders', 'outofthebox'); ?>: 
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
                <code>%file_url%</code>, 
                <code>%folder_name%</code>,
                <code>%folder_relative_path%</code>,
                <code>%folder_absolute_path%</code>,
                <code>%folder_url%</code>,
                <code>%ip%</code>, 
                <code>%location%</code>, 
              </div>

            </div>


            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Template File line in %filelist%', 'outofthebox'); ?>         </div>
            <div>
              <div class="outofthebox-option-description"><?php _e('Template for File item in File List in the download/upload/delete template', 'outofthebox'); ?> (HTML).</div>
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

              <div class="outofthebox-option-description"><?php _e('Available placeholders', 'outofthebox'); ?>: 
                <code>%file_name%</code>, 
                <code>%file_size%</code>, 
                <code>%file_icon%</code>, 
                <code>%file_url%</code>, 
                <code>%file_relative_path%</code>, 
                <code>%file_absolute_path%</code>, 
                <code>%folder_relative_path%</code>,
                <code>%folder_absolute_path%</code>,
                <code>%folder_url%</code>,
              </div>


            </div>
          </div>

          <div id="reset_notifications" type="button" class="simple-button blue"><?php _e('Reset to default notifications', 'outofthebox'); ?>&nbsp;<div class="oftb-spinner"></div></div>

        </div>
        <!-- End Notifications Tab -->

        <!--  Permissions Tab -->
        <div id="settings_permissions"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('Permissions', 'outofthebox'); ?></div>

          <div class="outofthebox-accordion">
            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Change Plugin Settings', 'outofthebox'); ?>         </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_edit_settings]', $this->settings['permissions_edit_settings']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Link Users to Private Folders', 'outofthebox'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_link_users]', $this->settings['permissions_link_users']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('See Reports', 'outofthebox'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_see_dashboard]', $this->settings['permissions_see_dashboard']); ?>
            </div>   

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('See Back-End Filebrowser', 'outofthebox'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_see_filebrowser]', $this->settings['permissions_see_filebrowser']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Add Plugin Shortcodes', 'outofthebox'); ?>         </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_add_shortcodes]', $this->settings['permissions_add_shortcodes']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Add Direct Links', 'outofthebox'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_add_links]', $this->settings['permissions_add_links']); ?>
            </div>

            <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Embed Documents', 'outofthebox'); ?>        </div>
            <div>
              <?php wp_roles_and_users_input('out_of_the_box_settings[permissions_add_embedded]', $this->settings['permissions_add_embedded']); ?>
            </div>

          </div>
        </div>
        <!-- End Permissions Tab -->

        <!--  Statistics Tab -->
        <div id="settings_stats"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('Statistics', 'outofthebox'); ?></div>

          <div class="outofthebox-option-title"><?php _e('Log Events', 'outofthebox'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[log_events]'/>
              <input type="checkbox" name="out_of_the_box_settings[log_events]" id="log_events" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['log_events']) ? 'checked="checked"' : ''; ?> data-div-toggle="events_options"/>
              <label class="outofthebox-onoffswitch-label" for="log_events"></label>
            </div>
          </div>
          <div class="outofthebox-option-description"><?php _e('Register all plugin events', 'outofthebox'); ?>.</div>

          <div class="outofthebox-suboptions events_options <?php echo ('Yes' === $this->settings['log_events']) ? '' : 'hidden'; ?> ">
            <div class="outofthebox-option-title"><?php _e('Summary Email', 'outofthebox'); ?>
              <div class="outofthebox-onoffswitch">
                <input type='hidden' value='No' name='out_of_the_box_settings[event_summary]'/>
                <input type="checkbox" name="out_of_the_box_settings[event_summary]" id="event_summary" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['event_summary']) ? 'checked="checked"' : ''; ?> data-div-toggle="event_summary"/>
                <label class="outofthebox-onoffswitch-label" for="event_summary"></label>
              </div>
            </div>
            <div class="outofthebox-option-description"><?php _e('Email a summary of all the events that are logged with the plugin', 'outofthebox'); ?>.</div>

            <div class="event_summary <?php echo ('Yes' === $this->settings['event_summary']) ? '' : 'hidden'; ?> ">

              <div class="outofthebox-option-title"><?php _e('Interval', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e('Please select the interval the summary needs to be send', 'outofthebox'); ?>.</div>
              <select type="text" name="out_of_the_box_settings[event_summary_period]" id="shortlinks">
                <option value="daily"  <?php echo 'daily' === $this->settings['event_summary_period'] ? "selected='selected'" : ''; ?>><?php _e('Every day', 'outofthebox'); ?></option>
                <option value="weekly"  <?php echo 'weekly' === $this->settings['event_summary_period'] ? "selected='selected'" : ''; ?>><?php _e('Weekly', 'outofthebox'); ?></option>
                <option value="monthly"  <?php echo 'monthly' === $this->settings['event_summary_period'] ? "selected='selected'" : ''; ?>><?php _e('Monthly', 'outofthebox'); ?></option>
              </select>   

              <div class="outofthebox-option-title"><?php _e('Recipients', 'outofthebox'); ?></div>
              <div class="outofthebox-option-description"><?php _e('Send the summary to the following email address(es)', 'outofthebox'); ?>:</div>
              <input class="outofthebox-option-input-large" type="text" name="out_of_the_box_settings[event_summary_recipients]" id="lostauthorization_notification" value="<?php echo esc_attr($this->settings['event_summary_recipients']); ?>" placeholder="<?php echo get_option('admin_email'); ?>">  
            </div>
          </div>

          <div class="outofthebox-option-title"><?php _e('Google Analytics', 'outofthebox'); ?>
            <div class="outofthebox-onoffswitch">
              <input type='hidden' value='No' name='out_of_the_box_settings[google_analytics]'/>
              <input type="checkbox" name="out_of_the_box_settings[google_analytics]" id="google_analytics" class="outofthebox-onoffswitch-checkbox" <?php echo ('Yes' === $this->settings['google_analytics']) ? 'checked="checked"' : ''; ?> />
              <label class="outofthebox-onoffswitch-label" for="google_analytics"></label>
            </div>
          </div>
          <div class="outofthebox-option-description"><?php _e('Would you like to see some statistics about your files? Out-of-the-Box can send all download/upload events to Google Analytics', 'outofthebox'); ?>. <?php echo sprintf(__('If you enable this feature, please make sure you already added your %s Google Analytics web tracking %s code to your site.', 'outofthebox'), "<a href='https://support.google.com/analytics/answer/1008080' target='_blank'>", '</a>'); ?>.</div>
        </div>
        <!-- End Statistics Tab -->

        <!-- System info Tab -->
        <div id="settings_system"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('System information', 'outofthebox'); ?></div>
          <?php echo $this->get_system_information(); ?>
        </div>
        <!-- End System info -->

        <!-- Help Tab -->
        <div id="settings_help"  class="outofthebox-tab-panel">
          <div class="outofthebox-tab-panel-header"><?php _e('Support', 'outofthebox'); ?></div>

          <div class="outofthebox-option-title"><?php _e('Support & Documentation', 'outofthebox'); ?></div>
          <div id="message">
            <p><?php _e('Check the documentation of the plugin in case you encounter any problems or are looking for support.', 'outofthebox'); ?></p>
            <div id='documentation_button' type='button' class='simple-button blue'><?php _e('Open Documentation', 'outofthebox'); ?></div>
          </div>
          <br/>
          <div class="outofthebox-option-title"><?php _e('Reset Cache', 'outofthebox'); ?></div>
          <?php echo $this->get_plugin_reset_box(); ?>

        </div>  
      </div>
      <!-- End Help info -->
    </div>
  </form>
  <script type="text/javascript" >
      var whitelist = <?php echo json_encode(TheLion\OutoftheBox\Helpers::get_all_users_and_roles()); ?>; /* Build Whitelist for permission selection */

      jQuery(document).ready(function ($) {
        var media_library;

        $('.outofthebox-color-picker').wpColorPicker();

        $('#content_skin_selectbox').ddslick({
          width: '598px',
          background: '#f4f4f4',
          onSelected: function (item) {
            $("#content_skin").val($('#content_skin_selectbox').data('ddslick').selectedData.value);
          }
        });
        $('#lightbox_skin_selectbox').ddslick({
          width: '598px',
          imagePosition: "right",
          background: '#f4f4f4',
          onSelected: function (item) {
            $("#lightbox_skin").val($('#lightbox_skin_selectbox').data('ddslick').selectedData.value);
          }
        });
        $('#mediaplayer_skin_selectbox').ddslick({
          width: '598px',
          imagePosition: "right",
          background: '#f4f4f4',
          onSelected: function (item) {
            $("#mediaplayer_skin").val($('#mediaplayer_skin_selectbox').data('ddslick').selectedData.value);
          }
        });

        $('.upload_button').click(function () {
          var input_field = $(this).prev("input").attr("id");
          media_library = wp.media.frames.file_frame = wp.media({
            title: '<?php echo __('Select your image', 'outofthebox'); ?>',
            button: {
              text: '<?php echo __('Use this Image', 'outofthebox'); ?>'
            },
            multiple: false
          });
          media_library.on("select", function () {
            var attachment = media_library.state().get('selection').first().toJSON();

            var mime = attachment.mime;
            var regex = /^image\/(?:jpe?g|png|gif|svg)$/i;
            var is_image = mime.match(regex)

            if (is_image) {
              $("#" + input_field).val(attachment.url);
              $("#" + input_field).trigger('change');
            }

            $('.upload-remove').click(function () {
              $(this).hide();
              $(this).parent().parent().find(".upload").val('');
              $(this).parent().parent().find(".screenshot").slideUp();
            })
          })
          media_library.open()
        });

        $('.upload-remove').click(function () {
          $(this).hide();
          $(this).parent().parent().find(".upload").val('');
          $(this).parent().parent().find(".screenshot").slideUp();
        })

        $('.default_image_button').click(function () {
          $(this).parent().find(".upload").val($(this).attr('data-default'));
          $('input.upload').trigger('change');
        });

        $('input.upload').change(function () {
          var img = '<img src="' + $(this).val() + '" />'
          img += '<a href="javascript:void(0)" class="upload-remove">' + '<?php echo __('Remove Media', 'outofthebox'); ?>' + "</a>";
          $(this).parent().find(".screenshot").slideDown().html(img);

          var default_button = $(this).parent().find(".default_image_button");
          default_button.hide();
          if ($(this).val() !== default_button.attr('data-default')) {
            default_button.fadeIn();
          }
        });

        $('#shortlinks').on('change', function () {
          $('.option.bitly, .option.shortest, .option.rebrandly').hide();
          if ($(this).val() == 'Bit.ly') {
            $('.option.bitly').show();
          }
          if ($(this).val() == 'Shorte.st') {
            $('.option.shortest').show();
          }
          if ($(this).val() == 'Rebrandly') {
            $('.option.rebrandly').show();
          }

        });

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

        $('#root_folder_button').click(function () {
          var $button = $(this);
          $(this).parent().addClass("thickbox_opener");
          $button.addClass('disabled');
          $button.find('.oftb-spinner').show();
          tb_show("Select Folder", '#TB_inline?height=450&amp;width=800&amp;inlineId=oftb-embedded');
        });

        $('#reset_notifications').click(function () {
          var $button = $(this);
          $button.addClass('disabled');
          $button.find('.oftb-spinner').fadeIn();
          $('#settings_notifications input[type="text"], #settings_notifications textarea').val('');
          $('#outofthebox-options').submit();
        });

        $('#documentation_button').click(function () {
          popup = window.open('<?php echo plugins_url('_documentation/index.html', dirname(__FILE__)); ?>', "_blank");
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

              if (location.hash === '#settings_advanced' || location.hash === '#settings_notifications') {
                location.reload(true);
              }

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
      });


  </script>
</div>