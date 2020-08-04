<?php
$settings = (array) get_option('out_of_the_box_settings');

if (
        !(\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_shortcodes'])) &&
        !(\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_links'])) &&
        !(\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_embedded']))
) {
    die();
}

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'default';
$standalone = isset($_REQUEST['standaloneshortcodebuilder']);

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

$this->load_scripts();
$this->load_styles();
$this->load_custom_css();

function OutoftheBox_remove_all_scripts()
{
    global $wp_scripts;
    $wp_scripts->queue = [];

    wp_enqueue_script('jquery-effects-fade');
    wp_enqueue_script('jquery-ui-accordion');
    wp_enqueue_script('jquery');
    wp_enqueue_script('OutoftheBox');
    wp_enqueue_script('OutoftheBox.tinymce');
}

function OutoftheBox_remove_all_styles()
{
    global $wp_styles;
    $wp_styles->queue = [];
    wp_enqueue_style('qtip');
    wp_enqueue_style('OutoftheBox.tinymce');
    wp_enqueue_style('OutoftheBox');
    wp_enqueue_style('Awesome-Font-5-css');
}

add_action('wp_print_scripts', 'OutoftheBox_remove_all_scripts', 1000);
add_action('wp_print_styles', 'OutoftheBox_remove_all_styles', 1000);

// Count number of openings for rating dialog
$counter = get_option('out_of_the_box_shortcode_opened', 0) + 1;
update_option('out_of_the_box_shortcode_opened', $counter);

// Initialize shortcode vars
$mode = (isset($_REQUEST['mode'])) ? $_REQUEST['mode'] : 'files';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
      <?php
      if ('default' === $type) {
          $title = __('Shortcode Builder', 'outofthebox');
          $mcepopup = 'shortcode';
      } elseif ('links' === $type) {
          $title = __('Insert direct Links', 'outofthebox');
          $mcepopup = 'links';
      } elseif ('embedded' === $type) {
          $title = __('Embed files', 'outofthebox');
          $mcepopup = 'embedded';
      } elseif ('gravityforms' === $type) {
          $title = __('Shortcode Builder', 'outofthebox');
          $mcepopup = 'shortcode';
      } elseif ('woocommerce' === $type) {
          $title = __('Shortcode Builder', 'outofthebox');
          $mcepopup = 'shortcode';
      } elseif ('contactforms7' === $type) {
          $title = __('Shortcode Builder', 'outofthebox');
          $mcepopup = 'shortcode';
      }
      ?></title>
    <?php if ('gravityforms' !== $type && 'contactforms7' !== $type && false === $standalone) { ?>
        <script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <?php } ?>

    <?php wp_print_scripts(); ?>
    <?php wp_print_styles(); ?>
  </head>

  <body class="outofthebox" data-mode="<?php echo $mode; ?>">
    <?php $this->ask_for_review(); ?>

    <form action="#">

      <div class="wrap">
        <div class="outofthebox-header">
          <div class="outofthebox-logo"><img src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/logo64x64.png" height="64" width="64"/></div>
          <div class="outofthebox-form-buttons">
            <?php if ('default' === $type) { ?>
                <?php if ($standalone) { ?>
                    <div id="get_shortcode" class="simple-button default get_shortcode" name="get_shortcode" title="<?php _e('Get raw Shortcode', 'outofthebox'); ?>"><?php _e('Create Shortcode', 'outofthebox'); ?><i class="fas fa-code" aria-hidden="true"></i></div>
                <?php } else { ?>
                    <div id="get_shortcode" class="simple-button default get_shortcode" name="get_shortcode" title="<?php _e('Get raw Shortcode', 'outofthebox'); ?>"><?php _e('Raw', 'outofthebox'); ?><i class="fas fa-code" aria-hidden="true"></i></div>
                    <div id="doinsert"  class="simple-button default insert_shortcode" name="insert"><?php _e('Insert Shortcode', 'outofthebox'); ?>&nbsp;<i class="fas fa-chevron-circle-right" aria-hidden="true"></i></div>                    
                <?php } ?>
            <?php } elseif ('links' === $type) { ?>
                <div id="doinsert" class="simple-button default insert_links" name="insert"  ><?php _e('Insert Links', 'outofthebox'); ?>&nbsp;<i class="fas fa-chevron-circle-right" aria-hidden="true"></i></div>
            <?php } elseif ('embedded' === $type) { ?>
                <div id="doinsert" class="simple-button default insert_embedded" name="insert" ><?php _e('Embed Files', 'outofthebox'); ?>&nbsp;<i class="fas fa-chevron-circle-right" aria-hidden="true"></i></div>
            <?php } elseif ('gravityforms' === $type) { ?>
                <div id="doinsert" class="simple-button default insert_shortcode_gf" name="insert"><?php _e('Insert Shortcode', 'outofthebox'); ?>&nbsp;<i class="fas fa-chevron-circle-right" aria-hidden="true"></i></div>
            <?php } elseif ('woocommerce' === $type) { ?>
                <div id="doinsert" class="simple-button default insert_shortcode_woocommerce" name="insert"><?php _e('Insert Shortcode', 'outofthebox'); ?>&nbsp;<i class="fas fa-chevron-circle-right" aria-hidden="true"></i></div>
            <?php } elseif ('contactforms7' === $type) { ?>
                <div id="doinsert" class="simple-button default insert_shortcode_cf" name="insert"><?php _e('Insert Shortcode', 'outofthebox'); ?>&nbsp;<i class="fas fa-chevron-circle-right" aria-hidden="true"></i></div>
            <?php } ?>
          </div>

          <div class="outofthebox-title"><?php echo $title; ?></div>

        </div>
        <?php
        if ('links' === $type || 'embedded' === $type) {
            echo '<div class="outofthebox-panel outofthebox-panel-full">';
            if ('embedded' === $type) {
                echo '<p>'.__('Please note that the embedded files need to be public (with link)', 'outofthebox').'</p>';
            }

            $atts = [
                'singleaccount' => '0',
                'dir' => '',
                'mode' => 'files',
                'filelayout' => 'grid',
                'showfiles' => '1',
                'upload' => '0',
                'delete' => '0',
                'rename' => '0',
                'addfolder' => '0',
                'showcolumnnames' => '0',
                'viewrole' => 'all',
                'candownloadzip' => '0',
                'showsharelink' => '0',
                'previewinline' => '0',
                'mcepopup' => $mcepopup,
                'includeext' => '*',
                '_random' => 'embed',
            ];

            $user_folder_backend = apply_filters('outofthebox_use_user_folder_backend', $this->settings['userfolder_backend']);

            if ('No' !== $user_folder_backend) {
                $atts['userfolders'] = $user_folder_backend;

                $private_root_folder = $this->settings['userfolder_backend_auto_root'];
                if ('auto' === $user_folder_backend && !empty($private_root_folder) && isset($private_root_folder['id'])) {
                    if (!isset($private_root_folder['account']) || empty($private_root_folder['account'])) {
                        $main_account = $this->get_processor()->get_accounts()->get_primary_account();
                        $atts['account'] = $main_account->get_id();
                    } else {
                        $atts['account'] = $private_root_folder['account'];
                    }

                    $atts['dir'] = $private_root_folder['id'];

                    if (!isset($private_root_folder['view_roles']) || empty($private_root_folder['view_roles'])) {
                        $private_root_folder['view_roles'] = ['none'];
                    }
                    $atts['viewuserfoldersrole'] = implode('|', $private_root_folder['view_roles']);
                }
            }

            echo $this->create_template($atts);
            echo '</div>'; ?>
            <?php
        } else {
            ?>

            <div id="" class="outofthebox-panel outofthebox-panel-left">
              <div class="outofthebox-nav-header"><?php _e('Shortcode Options', 'outofthebox'); ?></div>
              <ul class="outofthebox-nav-tabs">
                <li id="settings_general_tab" data-tab="settings_general" class="current"><a><span><?php _e('General', 'outofthebox'); ?></span></a></li>
                <li id="settings_folder_tab" data-tab="settings_folders"><a><span><?php _e('Folders', 'outofthebox'); ?></span></a></li>
                <li id="settings_layout_tab" data-tab="settings_layout"><a><span><?php _e('Layout', 'outofthebox'); ?></span></a></li>
                <li id="settings_sorting_tab" data-tab="settings_sorting"><a><span><?php _e('Sorting', 'outofthebox'); ?></span></a></li>
                <li id="settings_advanced_tab" data-tab="settings_advanced"><a><span><?php _e('Advanced', 'outofthebox'); ?></span></a></li>
                <li id="settings_exclusions_tab" data-tab="settings_exclusions"><a><span><?php _e('Exclusions', 'outofthebox'); ?></span></a></li>
                <li id="settings_upload_tab" data-tab="settings_upload"><a><span><?php _e('Upload Box', 'outofthebox'); ?></span></a></li>
                <li id="settings_notifications_tab" data-tab="settings_notifications"><a><span><?php _e('Notifications', 'outofthebox'); ?></span></a></li>
                <li id="settings_manipulation_tab" data-tab="settings_manipulation"><a><span><?php _e('File Manipulation', 'outofthebox'); ?></span></a></li>
                <li id="settings_permissions_tab" data-tab="settings_permissions" class=""><a><span><?php _e('User Permissions', 'outofthebox'); ?></span></a></li>
              </ul>
            </div>

            <div class="outofthebox-panel outofthebox-panel-right">

              <!-- General Tab -->
              <div id="settings_general" class="outofthebox-tab-panel current">

                <div class="outofthebox-tab-panel-header"><?php _e('General', 'outofthebox'); ?></div>

                <div class="outofthebox-option-title"><?php _e('Plugin Mode', 'outofthebox'); ?></div>
                <div class="outofthebox-option-description"><?php _e('Select how you want to use Out-of-the-Box in your post or page', 'outofthebox'); ?>:</div>
                <div class="outofthebox-option-radio">
                  <input type="radio" id="files" name="mode" <?php echo ('files' === $mode) ? 'checked="checked"' : ''; ?> value="files" class="mode"/>
                  <label for="files" class="outofthebox-option-radio-label"><?php _e('File browser', 'outofthebox'); ?></label>
                </div>
                <div class="outofthebox-option-radio">
                  <input type="radio" id="upload" name="mode" <?php echo ('upload' === $mode) ? 'checked="checked"' : ''; ?> value="upload" class="mode"/>
                  <label for="upload" class="outofthebox-option-radio-label"><?php _e('Upload Box', 'outofthebox'); ?></label>
                </div>
                <?php if ('gravityforms' !== $type && 'contactforms7' !== $type) { ?>
                    <div class="outofthebox-option-radio">
                      <input type="radio" id="gallery" name="mode" <?php echo ('gallery' === $mode) ? 'checked="checked"' : ''; ?> value="gallery" class="mode"/>
                      <label for="gallery" class="outofthebox-option-radio-label"><?php _e('Photo gallery', 'outofthebox'); ?> <small>(Images only)</small></label>
                    </div>
                    <div class="outofthebox-option-radio">
                      <input type="radio" id="audio" name="mode" <?php echo ('audio' === $mode) ? 'checked="checked"' : ''; ?> value="audio" class="mode"/>
                      <label for="audio" class="outofthebox-option-radio-label"><?php _e('Audio player', 'outofthebox'); ?> <small>(MP3, M4A, OGG, OGA, WAV)</small></label>
                    </div>
                    <div class="outofthebox-option-radio">
                      <input type="radio" id="video" name="mode" <?php echo ('video' === $mode) ? 'checked="checked"' : ''; ?> value="video" class="mode"/>
                      <label for="video" class="outofthebox-option-radio-label"><?php _e('Video player', 'outofthebox'); ?> <small>(MP4, M4V, OGG, OGV, WEBM, WEBMV)</small></label>
                    </div>
                    <div class="outofthebox-option-radio">
                      <input type="radio" id="search" name="mode" <?php echo ('search' === $mode) ? 'checked="checked"' : ''; ?> value="search" class="mode"/>
                      <label for="search" class="outofthebox-option-radio-label"><?php _e('Search Box', 'outofthebox'); ?></label>
                    </div>
                    <?php
                } else {
                    ?>
                    <br/>
                    <div class="oftb-updated">
                      <i><strong>TIP</strong>: <?php _e("Don't forget to check the Upload Permissions on the User Permissions tab", 'outofthebox'); ?>. <?php _e('By default, only logged-in users can upload files', 'outofthebox'); ?>.</i>
                    </div>
                    <?php
                } ?>

              </div>
              <!-- End General Tab -->
              <!-- User Folders Tab -->
              <div id="settings_folders" class="outofthebox-tab-panel">
                <div class="outofthebox-tab-panel-header"><?php _e('Folders', 'outofthebox'); ?></div>

                <div class="forfilebrowser forgallery">
                  <div class="outofthebox-option-title"><?php _e('Use single cloud account', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_singleaccount" id="OutoftheBox_singleaccount" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['singleaccount']) && '0' === $_REQUEST['singleaccount']) ? '' : 'checked="checked"'; ?> data-div-toggle='option-singleaccount'/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_singleaccount"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('Use a folder from one of the linked account. Disabling this option allows your users to navigate through the folders of all your linked accounts', 'outofthebox'); ?>
                  </div>
                </div>

                <div class="option-singleaccount <?php echo (isset($_REQUEST['singleaccount']) && '0' === $_REQUEST['singleaccount']) ? 'hidden' : ''; ?>">
                  <div class="outofthebox-option-title"><?php _e('Select start Folder', 'outofthebox'); ?></div>
                  <div class="outofthebox-option-description"><?php _e('Select which folder should be used as starting point, or in case the Smart Client Area is enabled should be used for the Private Folders', 'outofthebox'); ?>. <?php _e('Users will not be able to navigate outside this folder', 'outofthebox'); ?>.</div>
                  <div class="root-folder">
                    <?php
                    $atts = [
                        'singleaccount' => '0',
                        'mode' => 'files',
                        'maxheight' => '300px',
                        'filelayout' => 'list',
                        'showfiles' => '1',
                        'filesize' => '0',
                        'filedate' => '0',
                        'upload' => '0',
                        'delete' => '0',
                        'rename' => '0',
                        'addfolder' => '0',
                        'showbreadcrumb' => '1',
                        'showcolumnnames' => '0',
                        'search' => '0',
                        'roottext' => '',
                        'viewrole' => 'all',
                        'downloadrole' => 'none',
                        'candownloadzip' => '0',
                        'showsharelink' => '0',
                        'previewinline' => '0',
                        'mcepopup' => $mcepopup,
                    ];

            if (isset($_REQUEST['account'])) {
                $atts['startaccount'] = $_REQUEST['account'];
            }

            if (isset($_REQUEST['dir'])) {
                $atts['startpath'] = $_REQUEST['dir'];
            }

            $user_folder_backend = apply_filters('outofthebox_use_user_folder_backend', $this->settings['userfolder_backend']);

            if ('No' !== $user_folder_backend) {
                $atts['userfolders'] = $user_folder_backend;

                $private_root_folder = $this->settings['userfolder_backend_auto_root'];
                if ('auto' === $user_folder_backend && !empty($private_root_folder) && isset($private_root_folder['id'])) {
                    if (!isset($private_root_folder['account']) || empty($private_root_folder['account'])) {
                        $main_account = $this->get_processor()->get_accounts()->get_primary_account();
                        $atts['account'] = $main_account->get_id();
                    } else {
                        $atts['account'] = $private_root_folder['account'];
                    }

                    $atts['dir'] = $private_root_folder['id'];

                    if (!isset($private_root_folder['view_roles'])) {
                        $private_root_folder['view_roles'] = ['none'];
                    }
                    $atts['viewuserfoldersrole'] = implode('|', $private_root_folder['view_roles']);
                }
            }

            echo $this->create_template($atts); ?>
                  </div>

                  <br/>
                  <div class="outofthebox-tab-panel-header"><?php _e('Smart Client Area', 'outofthebox'); ?></div>

                  <div class="outofthebox-option-title"><?php _e('Use Private Folders', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_linkedfolders" id="OutoftheBox_linkedfolders" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['userfolders'])) ? 'checked="checked"' : ''; ?> data-div-toggle='option-userfolders'/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_linkedfolders"></label>
                    </div>
                  </div>

                  <div class="outofthebox-option-description">
                    <?php echo sprintf(__('The plugin can easily and securily share documents on your %s with your users/clients', 'outofthebox'), 'outofthebox'); ?>. 
                    <?php _e('This allows your clients to preview, download and manage their documents in their own private folder', 'outofthebox'); ?>.
                    <?php echo sprintf(__('Specific permissions can always be set via %s', 'outofthebox'), '<a href="#" onclick="jQuery(\'li[data-tab=settings_permissions]\').trigger(\'click\')">'.__('User Permissions', 'outofthebox').'</a>'); ?>. 

                    <?php _e('The Smart Client Area can be useful in some situations, for example', 'outofthebox'); ?>:
                    <ul>
                      <li><?php _e('You want to share documents with your clients privately', 'outofthebox'); ?></li>
                      <li><?php _e('You want your clients, users or guests upload files to their own folder', 'outofthebox'); ?></li>
                      <li><?php _e('You want to give your customers a private folder already filled with some files directly after they register', 'outofthebox'); ?></li>
                    </ul>
                  </div>

                  <div class="outofthebox-suboptions option-userfolders forfilebrowser foruploadbox forgallery <?php echo (isset($_REQUEST['userfolders'])) ? '' : 'hidden'; ?>">

                    <div class="outofthebox-option-title"><?php _e('Mode', 'outofthebox'); ?></div>
                    <div class="outofthebox-option-description"><?php _e('Do you want to link your users manually to their Private Folder or should the plugin handle this automatically for you', 'outofthebox'); ?>.</div>

                    <?php
                    $userfolders = 'auto';
            if (isset($_REQUEST['userfolders'])) {
                $userfolders = $_REQUEST['userfolders'];
            } ?>
                    <div class="outofthebox-option-radio">
                      <input type="radio" id="userfolders_method_manual" name="OutoftheBox_userfolders_method"<?php echo ('manual' === $userfolders) ? 'checked="checked"' : ''; ?> value="manual"/>
                      <label for="userfolders_method_manual" class="outofthebox-option-radio-label"><?php echo sprintf(__('I will link the users manually via %sthis page%s', 'outofthebox'), '<a href="'.admin_url('admin.php?page=OutoftheBox_settings_linkusers').'" target="_blank">', '</a>'); ?></label>
                    </div>
                    <div class="outofthebox-option-radio">
                      <input type="radio" id="userfolders_method_auto" name="OutoftheBox_userfolders_method" <?php echo ('auto' === $userfolders) ? 'checked="checked"' : ''; ?> value="auto"/>
                      <label for="userfolders_method_auto" class="outofthebox-option-radio-label"><?php _e('Let the plugin automatically manage the Private Folders for me in the folder I have selected above', 'outofthebox'); ?></label>
                    </div>

                    <div class="option-userfolders_auto">
                      <div class="outofthebox-option-title"><?php _e('Template Folder', 'outofthebox'); ?>
                        <div class="outofthebox-onoffswitch">
                          <input type="checkbox" name="OutoftheBox_userfolders_template" id="OutoftheBox_userfolders_template" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['usertemplatedir'])) ? 'checked="checked"' : ''; ?> data-div-toggle='userfolders-template-option'/>
                          <label class="outofthebox-onoffswitch-label" for="OutoftheBox_userfolders_template"></label>
                        </div>
                      </div>
                      <div class="outofthebox-option-description">
                        <?php _e('Newly created Private Folders can be prefilled with files from a template', 'outofthebox'); ?>. <?php _e('The content of the template folder selected will be copied to the user folder', 'outofthebox'); ?>.
                      </div>

                      <div class="outofthebox-suboptions userfolders-template-option <?php echo (isset($_REQUEST['usertemplatedir'])) ? '' : 'hidden'; ?>">
                        <div class="template-folder">
                          <?php
                          $user_folders = (('No' === $user_folder_backend) ? '0' : $this->settings['userfolder_backend']);

            $atts = [
                'singleaccount' => '0',
                'mode' => 'files',
                'filelayout' => 'list',
                'maxheight' => '300px',
                'showfiles' => '1',
                'filesize' => '0',
                'filedate' => '0',
                'upload' => '0',
                'delete' => '0',
                'rename' => '0',
                'addfolder' => '0',
                'showbreadcrumb' => '1',
                'showcolumnnames' => '0',
                'viewrole' => 'all',
                'downloadrole' => 'none',
                'candownloadzip' => '0',
                'showsharelink' => '0',
                'userfolders' => $user_folders,
                'mcepopup' => $mcepopup,
            ];

            if (isset($_REQUEST['usertemplatedir'])) {
                $atts['startpath'] = $_REQUEST['usertemplatedir'];
            }

            echo $this->create_template($atts); ?>
                        </div>
                      </div>

                      <div class="outofthebox-option-title"><?php _e('Full Access', 'outofthebox'); ?></div>
                      <div class="outofthebox-option-description"><?php _e('By default only Administrator users will be able to navigate through all Private Folders', 'outofthebox'); ?>. <?php _e('When you want other User Roles to be able do browse to the Private Folders as well, please check them below', 'outofthebox'); ?>.</div>

                      <?php
                      $selected = (isset($_REQUEST['viewuserfoldersrole'])) ? explode('|', $_REQUEST['viewuserfoldersrole']) : ['administrator'];
            wp_roles_and_users_input('OutoftheBox_view_user_folders_role', $selected); ?>


                      <div class="outofthebox-option-title"><?php _e('Quota', 'outofthebox'); ?></div>
                      <div class="outofthebox-option-description"><?php _e('Set maximum size of the User Folder (e.g. 10M, 100M, 1G). When the Upload function is enabled, the user will not be able to upload when the limit is reached', 'outofthebox'); ?>. <?php _e('Leave this field empty or set it to -1 for unlimited disk space', 'outofthebox'); ?>.</div>
                      <input type="text" name="OutoftheBox_maxuserfoldersize" id="OutoftheBox_maxuserfoldersize" placeholder="e.g. 10M, 100M, 1G" value="<?php echo (isset($_REQUEST['maxuserfoldersize'])) ? $_REQUEST['maxuserfoldersize'] : ''; ?>"/>
                    </div>
                  </div>
                </div>

              </div>
              <!-- End User Folders Tab -->

              <!-- Layout Tab -->
              <div id="settings_layout"  class="outofthebox-tab-panel">

                <div class="outofthebox-tab-panel-header"><?php _e('Layout', 'outofthebox'); ?></div>

                <div class="outofthebox-option-title"><?php _e('Plugin container width', 'outofthebox'); ?></div>
                <div class="outofthebox-option-description"><?php _e('Set max width for the Out-of-the-Box container', 'outofthebox'); ?>. <?php _e("You can use pixels or percentages, eg '360px', '480px', '70%'", 'outofthebox'); ?>. <?php echo __('Leave empty for default value', 'outofthebox'); ?>.</div>
                <input type="text" name="OutoftheBox_max_width" id="OutoftheBox_max_width" placeholder="100%" value="<?php echo (isset($_REQUEST['maxwidth'])) ? $_REQUEST['maxwidth'] : ''; ?>"/>


                <div class="forfilebrowser forgallery forsearch">
                  <div class="outofthebox-option-title"><?php _e('Plugin container height', 'outofthebox'); ?></div>
                  <div class="outofthebox-option-description"><?php _e('Set max height for the Out-of-the-Box container', 'outofthebox'); ?>. <?php _e("You can use pixels or percentages, eg '360px', '480px', '70%'", 'outofthebox'); ?>. <?php _e('Leave empty for default value', 'outofthebox'); ?>.</div>
                  <input type="text" name="OutoftheBox_max_height" id="OutoftheBox_max_height" placeholder="auto" value="<?php echo (isset($_REQUEST['maxheight'])) ? $_REQUEST['maxheight'] : ''; ?>"/>
                </div>

                <div class="outofthebox-option-title"><?php _e('Custom CSS Class', 'outofthebox'); ?></div>
                <div class="outofthebox-option-description"><?php _e('Add your own custom classes to the plugin container. Multiple classes can be added seperated by a whitespace', 'outofthebox'); ?>.</div>
                <input type="text" name="OutoftheBox_class" id="OutoftheBox_class" value="<?php echo (isset($_REQUEST['class'])) ? $_REQUEST['class'] : ''; ?>" autocomplete="off"/>


                <div class="foraudio forvideo">

                  <div class="outofthebox-option-title"><?php _e('Media Player', 'outofthebox'); ?></div>
                  <div>
                    <div class="outofthebox-option-description"><?php _e('Select which Media Player you want to use', 'outofthebox'); ?>.</div>
                    <select name="OutoftheBox_mediaplayer_skin_selectionbox" id="OutoftheBox_mediaplayer_skin_selectionbox" class="ddslickbox">
                      <?php
                      $default_player = $this->settings['mediaplayer_skin'];
            $selected_value = (isset($_REQUEST['mediaplayerskin'])) ? $_REQUEST['mediaplayerskin'] : $default_player;

            foreach (new DirectoryIterator(OUTOFTHEBOX_ROOTDIR.'/skins/') as $fileInfo) {
                if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                    if (file_exists(OUTOFTHEBOX_ROOTDIR.'/skins/'.$fileInfo->getFilename().'/js/Player.js')) {
                        $selected = ($fileInfo->getFilename() === $selected_value) ? 'selected="selected"' : '';
                        $icon = file_exists(OUTOFTHEBOX_ROOTDIR.'/skins/'.$fileInfo->getFilename().'/Thumb.jpg') ? OUTOFTHEBOX_ROOTPATH.'/skins/'.$fileInfo->getFilename().'/Thumb.jpg' : '';
                        echo '<option value="'.$fileInfo->getFilename().'" data-imagesrc="'.$icon.'" data-description="" '.$selected.'>'.$fileInfo->getFilename()."</option>\n";
                    }
                }
            } ?>
                    </select>
                    <input type="hidden" name="OutoftheBox_mediaplayer_skin" id="OutoftheBox_mediaplayer_skin" value="<?php echo $selected; ?>"/>
                    <input type="hidden" name="OutoftheBox_mediaplayer_default" id="OutoftheBox_mediaplayer_default" value="<?php echo $default_player; ?>"/>
                  </div>

                  <div class="outofthebox-option-title"><?php _e('Mediaplayer Buttons', 'outofthebox'); ?></div>
                  <div class="outofthebox-option-description"><?php _e('Set which buttons (if supported) should be visible in the mediaplayer', 'outofthebox'); ?>.</div>

                  <?php
                  $buttons = [
                      'prevtrack' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M76 480h24c6.6 0 12-5.4 12-12V285l219.5 187.6c20.6 17.2 52.5 2.8 52.5-24.6V64c0-27.4-31.9-41.8-52.5-24.6L112 228.1V44c0-6.6-5.4-12-12-12H76c-6.6 0-12 5.4-12 12v424c0 6.6 5.4 12 12 12zM336 98.5v315.1L149.3 256.5 336 98.5z"></path></svg>',
                      'playpause' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6zM48 453.5v-395c0-4.6 5.1-7.5 9.1-5.2l334.2 197.5c3.9 2.3 3.9 8 0 10.3L57.1 458.7c-4 2.3-9.1-.6-9.1-5.2z"></path></svg>',
                      'nexttrack' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M372 32h-24c-6.6 0-12 5.4-12 12v183L116.5 39.4C95.9 22.3 64 36.6 64 64v384c0 27.4 31.9 41.8 52.5 24.6L336 283.9V468c0 6.6 5.4 12 12 12h24c6.6 0 12-5.4 12-12V44c0-6.6-5.4-12-12-12zM112 413.5V98.4l186.7 157.1-186.7 158z"></path></svg>',
                      'volume' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 512"><path fill="currentColor" d="M394.23 100.85c-11.19-7.09-26.03-3.8-33.12 7.41s-3.78 26.03 7.41 33.12C408.27 166.6 432 209.44 432 256s-23.73 89.41-63.48 114.62c-11.19 7.09-14.5 21.92-7.41 33.12 6.51 10.28 21.12 15.03 33.12 7.41C447.94 377.09 480 319.09 480 256s-32.06-121.09-85.77-155.15zm-56 78.28c-11.58-6.33-26.19-2.16-32.61 9.45-6.39 11.61-2.16 26.2 9.45 32.61C327.98 228.28 336 241.63 336 256c0 14.37-8.02 27.72-20.92 34.81-11.61 6.41-15.84 21-9.45 32.61 6.43 11.66 21.05 15.8 32.61 9.45 28.23-15.55 45.77-45 45.77-76.87s-17.54-61.33-45.78-76.87zM231.81 64c-5.91 0-11.92 2.18-16.78 7.05L126.06 160H24c-13.26 0-24 10.74-24 24v144c0 13.25 10.74 24 24 24h102.06l88.97 88.95c4.87 4.87 10.88 7.05 16.78 7.05 12.33 0 24.19-9.52 24.19-24.02V88.02C256 73.51 244.13 64 231.81 64zM208 366.05L145.94 304H48v-96h97.94L208 145.95v220.1z"></path></svg>',
                      'current' => '<span>00:01</span>',
                      'duration' => '<span>- 59:59</span>',
                      'skipback' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M267.5 281.2l192 159.4c20.6 17.2 52.5 2.8 52.5-24.6V96c0-27.4-31.9-41.8-52.5-24.6L267.5 232c-15.3 12.8-15.3 36.4 0 49.2zM464 130.3V382L313 256.6l151-126.3zM11.5 281.2l192 159.4c20.6 17.2 52.5 2.8 52.5-24.6V96c0-27.4-31.9-41.8-52.5-24.6L11.5 232c-15.3 12.8-15.3 36.4 0 49.2zM208 130.3V382L57 256.6l151-126.3z"></path></svg>',
                      'jumpforward' => '<svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M244.5 230.8L52.5 71.4C31.9 54.3 0 68.6 0 96v320c0 27.4 31.9 41.8 52.5 24.6l192-160.6c15.3-12.8 15.3-36.4 0-49.2zM48 381.7V130.1l151 125.4L48 381.7zm452.5-150.9l-192-159.4C287.9 54.3 256 68.6 256 96v320c0 27.4 31.9 41.8 52.5 24.6l192-160.6c15.3-12.8 15.3-36.4 0-49.2zM304 381.7V130.1l151 125.4-151 126.2z"></path></svg>',
                      'speed' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M381.06 193.27l-75.76 97.4c-5.54-1.56-11.27-2.67-17.3-2.67-35.35 0-64 28.65-64 64 0 11.72 3.38 22.55 8.88 32h110.25c5.5-9.45 8.88-20.28 8.88-32 0-11.67-3.36-22.46-8.81-31.88l75.75-97.39c8.16-10.47 6.25-25.55-4.19-33.67-10.57-8.15-25.6-6.23-33.7 4.21zM288 32C128.94 32 0 160.94 0 320c0 52.8 14.25 102.26 39.06 144.8 5.61 9.62 16.3 15.2 27.44 15.2h443c11.14 0 21.83-5.58 27.44-15.2C561.75 422.26 576 372.8 576 320c0-159.06-128.94-288-288-288zm212.27 400H75.73C57.56 397.63 48 359.12 48 320 48 187.66 155.66 80 288 80s240 107.66 240 240c0 39.12-9.56 77.63-27.73 112z"></path></svg>',
                      'shuffle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M505 400l-79.2 72.9c-15.1 15.1-41.8 4.4-41.8-17v-40h-31c-3.3 0-6.5-1.4-8.8-3.9l-89.8-97.2 38.1-41.3 79.8 86.3H384v-48c0-21.4 26.7-32.1 41.8-17l79.2 71c9.3 9.6 9.3 24.8 0 34.2zM12 152h91.8l79.8 86.3 38.1-41.3-89.8-97.2c-2.3-2.5-5.5-3.9-8.8-3.9H12c-6.6 0-12 5.4-12 12v32c0 6.7 5.4 12.1 12 12.1zm493-41.9l-79.2-71C410.7 24 384 34.7 384 56v40h-31c-3.3 0-6.5 1.4-8.8 3.9L103.8 360H12c-6.6 0-12 5.4-12 12v32c0 6.6 5.4 12 12 12h111c3.3 0 6.5-1.4 8.8-3.9L372.2 152H384v48c0 21.4 26.7 32.1 41.8 17l79.2-73c9.3-9.4 9.3-24.6 0-33.9z"></path></svg>',
                      'loop' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M512 256c0 83.813-68.187 152-152 152H136.535l55.762 54.545c4.775 4.67 4.817 12.341.094 17.064l-16.877 16.877c-4.686 4.686-12.284 4.686-16.971 0l-104-104c-4.686-4.686-4.686-12.284 0-16.971l104-104c4.686-4.686 12.284-4.686 16.971 0l16.877 16.877c4.723 4.723 4.681 12.393-.094 17.064L136.535 360H360c57.346 0 104-46.654 104-104 0-19.452-5.372-37.671-14.706-53.258a11.991 11.991 0 0 1 1.804-14.644l17.392-17.392c5.362-5.362 14.316-4.484 18.491 1.847C502.788 196.521 512 225.203 512 256zM62.706 309.258C53.372 293.671 48 275.452 48 256c0-57.346 46.654-104 104-104h223.465l-55.762 54.545c-4.775 4.67-4.817 12.341-.094 17.064l16.877 16.877c4.686 4.686 12.284 4.686 16.971 0l104-104c4.686-4.686 4.686-12.284 0-16.971l-104-104c-4.686-4.686-12.284-4.686-16.971 0l-16.877 16.877c-4.723 4.723-4.681 12.393.094 17.064L375.465 104H152C68.187 104 0 172.187 0 256c0 30.797 9.212 59.479 25.019 83.447 4.175 6.331 13.129 7.209 18.491 1.847l17.392-17.392a11.991 11.991 0 0 0 1.804-14.644z"></path></svg>',
                      'fullscreen' => '<svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M0 180V56c0-13.3 10.7-24 24-24h124c6.6 0 12 5.4 12 12v24c0 6.6-5.4 12-12 12H48v100c0 6.6-5.4 12-12 12H12c-6.6 0-12-5.4-12-12zM288 44v24c0 6.6 5.4 12 12 12h100v100c0 6.6 5.4 12 12 12h24c6.6 0 12-5.4 12-12V56c0-13.3-10.7-24-24-24H300c-6.6 0-12 5.4-12 12zm148 276h-24c-6.6 0-12 5.4-12 12v100H300c-6.6 0-12 5.4-12 12v24c0 6.6 5.4 12 12 12h124c13.3 0 24-10.7 24-24V332c0-6.6-5.4-12-12-12zM160 468v-24c0-6.6-5.4-12-12-12H48V332c0-6.6-5.4-12-12-12H12c-6.6 0-12 5.4-12 12v124c0 13.3 10.7 24 24 24h124c6.6 0 12-5.4 12-12z"></path></svg>',
                      'airplay' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.9 13.9"><g id="airplay"><polygon fill="currentColor" points="0 0 16.9 0 16.9 10.4 13.2 10.4 11.9 8.9 15.4 8.9 15.4 1.6 1.5 1.6 1.5 8.9 5 8.9 3.6 10.4 0 10.4 0 0"/><polygon fill="currentColor"  points="2.7 13.9 8.4 7 14.2 13.9 2.7 13.9"/></g></svg>',
                      'chromecast' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.3 13.4"><path id="chromecast" fill="currentColor" d="M80.4,13v2.2h2.2A2.22,2.22,0,0,0,80.4,13Zm0-2.9v1.5a3.69,3.69,0,0,1,3.7,3.68s0,0,0,0h1.5a5.29,5.29,0,0,0-5.2-5.2h0ZM93.7,4.9H83.4V6.1a9.59,9.59,0,0,1,6.2,6.2h4.1V4.9h0ZM80.4,7.1V8.6a6.7,6.7,0,0,1,6.7,6.7h1.4a8.15,8.15,0,0,0-8.1-8.2h0ZM95.1,1.9H81.8a1.54,1.54,0,0,0-1.5,1.5V5.6h1.5V3.4H95.1V13.7H89.9v1.5h5.2a1.54,1.54,0,0,0,1.5-1.5V3.4A1.54,1.54,0,0,0,95.1,1.9Z" transform="translate(-80.3 -1.9)"/></svg>',
                  ];

            $selected = (isset($_REQUEST['mediabuttons'])) ? explode('|', $_REQUEST['mediabuttons']) : ['prevtrack', 'playpause', 'nexttrack', 'volume', 'current', 'duration', 'fullscreen'];

            foreach ($buttons as $button_value => $button_text) {
                if (in_array($button_value, $selected) || 'all' == $selected[0]) {
                    $checked = 'checked="checked"';
                } else {
                    $checked = '';
                }

                echo '<div class="outofthebox-option-checkbox outofthebox-option-checkbox-vertical-list media-buttons">';
                echo '<input class="simple" type="checkbox" name="OutoftheBox_media_buttons[]" value="'.$button_value.'" '.$checked.'/>';
                echo '<label for="OutoftheBox_media_buttons" class="outofthebox-option-checkbox-label">'.$button_text.'</label>';
                echo '</div>';
            } ?>

                  <div class="outofthebox-option-title"><?php _e('Show Playlist', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_showplaylist" id="OutoftheBox_showplaylist" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['hideplaylist']) && '1' === $_REQUEST['hideplaylist']) ? '' : 'checked="checked"'; ?> data-div-toggle="playlist-options">
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showplaylist"></label>
                    </div>
                  </div>   

                  <div class="outofthebox-suboptions playlist-options <?php echo (isset($_REQUEST['hideplaylist']) && '1' === $_REQUEST['hideplaylist']) ? 'hidden' : ''; ?>">
                    <div class="outofthebox-option-title"><?php _e('Playlist open on start', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_showplaylistonstart" id="OutoftheBox_showplaylistonstart" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showplaylistonstart']) && '0' === $_REQUEST['showplaylistonstart']) ? '' : 'checked="checked"'; ?>>
                          <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showplaylistonstart"></label>
                      </div>
                    </div>  

                    <div class="forvideo">
                      <div class="outofthebox-option-title"><?php _e('Playlist opens on top of player', 'outofthebox'); ?>
                        <div class="outofthebox-onoffswitch">
                          <input type="checkbox" name="OutoftheBox_showplaylistinline" id="OutoftheBox_showplaylistinline" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['playlistinline']) && '1' === $_REQUEST['playlistinline']) ? 'checked="checked"' : ''; ?>>
                            <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showplaylistinline"></label>
                        </div>
                      </div>  
                    </div>

                    <div class="outofthebox-option-title"><?php _e('Display thumbnails', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_playlistthumbnails" id="OutoftheBox_playlistthumbnails" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['playlistthumbnails']) && '0' === $_REQUEST['playlistthumbnails']) ? '' : 'checked="checked"'; ?>>
                          <label class="outofthebox-onoffswitch-label" for="OutoftheBox_playlistthumbnails"></label>
                      </div>
                    </div>   
                    <div class="outofthebox-option-description"><?php _e('Show thumbnails of your files in the Playlist', 'outofthebox'); ?>. <?php _e('The plugin show the thumbnail provided by the cloud server or you can use your own one', 'outofthebox'); ?>. <?php _e('If you want to use your own thumbnail, add a *.png or *.jpg file with the same name in the same folder. You can also add a cover with the name of the folder to show the cover for all files', 'outofthebox'); ?>. <?php _e('If no cover is available, a placeholder will be used', 'outofthebox'); ?>.</div>

                    <div class="outofthebox-option-title"><?php _e('Show last modified date', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_media_filedate" id="OutoftheBox_media_filedate" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['filedate']) && '0' === $_REQUEST['filedate']) ? '' : 'checked="checked"'; ?>/>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_media_filedate"></label>
                      </div>
                    </div>

                    <div class="outofthebox-option-title"><?php _e('Download Button', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_linktomedia" id="OutoftheBox_linktomedia" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['linktomedia']) && '1' === $_REQUEST['linktomedia']) ? 'checked="checked"' : ''; ?>>
                          <label class="outofthebox-onoffswitch-label" for="OutoftheBox_linktomedia"></label>
                      </div>
                    </div>   

                    <div class="outofthebox-option-title"><?php _e('Purchase Button', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_mediapurchase" id="OutoftheBox_mediapurchase" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['linktoshop']) && '1' === $_REQUEST['linktoshop']) ? 'checked="checked"' : ''; ?> data-div-toggle='webshop-options'>
                          <label class="outofthebox-onoffswitch-label" for="OutoftheBox_mediapurchase"></label>
                      </div>
                    </div>  

                    <div class="option webshop-options <?php echo (isset($_REQUEST['linktoshop'])) ? '' : 'hidden'; ?>">
                      <div class="outofthebox-option-title"><?php _e('Link to webshop', 'outofthebox'); ?></div>  
                      <input class="outofthebox-option-input-large" type="text" name="OutoftheBox_linktoshop" id="OutoftheBox_linktoshop" placeholder="https://www.yourwebshop.com/" value="<?php echo (isset($_REQUEST['linktoshop'])) ? $_REQUEST['linktoshop'] : ''; ?>"/>
                    </div>
                  </div>
                </div>














                <div class="forfilebrowser forsearch">
                  <div class="outofthebox-option-title"><?php _e('File Browser view', 'outofthebox'); ?></div>
                  <?php
                  $filelayout = (!isset($_REQUEST['filelayout'])) ? 'list' : $_REQUEST['filelayout']; ?>
                  <div class="outofthebox-option-radio">
                    <input type="radio" id="file_layout_grid" name="OutoftheBox_file_layout"  <?php echo ('grid' === $filelayout) ? 'checked="checked"' : ''; ?> value="grid" />
                    <label for="file_layout_grid" class="outofthebox-option-radio-label"><?php _e('Grid/Thumbnail View', 'outofthebox'); ?></label>
                  </div>
                  <div class="outofthebox-option-radio">
                    <input type="radio" id="file_layout_list" name="OutoftheBox_file_layout"  <?php echo ('list' === $filelayout) ? 'checked="checked"' : ''; ?> value="list" />
                    <label for="file_layout_list" class="outofthebox-option-radio-label"><?php _e('List View', 'outofthebox'); ?></label>
                  </div>
                </div>

                <div class=" forfilebrowser forgallery">
                  <div class="outofthebox-option-title"><?php _e('Show header', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_breadcrumb" id="OutoftheBox_breadcrumb" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showbreadcrumb']) && '0' === $_REQUEST['showbreadcrumb']) ? '' : 'checked="checked"'; ?> data-div-toggle="header-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_breadcrumb"></label>
                    </div>
                  </div>  

                  <div class="outofthebox-suboptions header-options <?php echo (isset($_REQUEST['showbreadcrumb']) && '0' === $_REQUEST['showbreadcrumb']) ? 'hidden' : ''; ?>">
                    <div class="outofthebox-option-title"><?php _e('Show refresh button', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_showrefreshbutton" id="OutoftheBox_showrefreshbutton" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showrefreshbutton']) && '0' === $_REQUEST['showrefreshbutton']) ? '' : 'checked="checked"'; ?>/>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showrefreshbutton"></label>
                      </div>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('Add a refresh button in the header so users can refresh the file list and pull changes', 'outofthebox'); ?></div>

                    <div class="outofthebox-option-title"><?php _e('Breadcrumb text for top folder', 'outofthebox'); ?></div>
                    <input type="text" name="OutoftheBox_roottext" id="OutoftheBox_roottext" placeholder="<?php _e('Start', 'outofthebox'); ?>" value="<?php echo (isset($_REQUEST['roottext'])) ? $_REQUEST['roottext'] : ''; ?>"/>
                  </div>
                </div>

                <div class=" forfilebrowser forsearch">
                  <div class="option forfilebrowser forsearch forlistonly">
                    <div class="outofthebox-option-title"><?php _e('Show columnnames', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_showcolumnnames" id="OutoftheBox_showcolumnnames" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showcolumnnames']) && '0' === $_REQUEST['showcolumnnames']) ? '' : 'checked="checked"'; ?> data-div-toggle="columnnames-options"/>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showcolumnnames"></label>
                      </div>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('Display the columnnames of the date and filesize in the List View of the File Browser', 'outofthebox'); ?></div>

                    <div class="outofthebox-suboptions columnnames-options">
                      <div class="option-filesize">
                        <div class="outofthebox-option-title"><?php _e('Show file size', 'outofthebox'); ?>
                          <div class="outofthebox-onoffswitch">
                            <input type="checkbox" name="OutoftheBox_filesize" id="OutoftheBox_filesize" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['filesize']) && '0' === $_REQUEST['filesize']) ? '' : 'checked="checked"'; ?>/>
                            <label class="outofthebox-onoffswitch-label" for="OutoftheBox_filesize"></label>
                          </div>
                        </div>
                        <div class="outofthebox-option-description"><?php _e('Display or Hide column with file sizes in List view', 'outofthebox'); ?></div>
                      </div>

                      <div class="option-filedate">
                        <div class="outofthebox-option-title"><?php _e('Show last modified date', 'outofthebox'); ?>
                          <div class="outofthebox-onoffswitch">
                            <input type="checkbox" name="OutoftheBox_filedate" id="OutoftheBox_filedate" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['filedate']) && '0' === $_REQUEST['filedate']) ? '' : 'checked="checked"'; ?>/>
                            <label class="outofthebox-onoffswitch-label" for="OutoftheBox_filedate"></label>
                          </div>
                        </div>
                        <div class="outofthebox-option-description"><?php _e('Display or Hide column with last modified date in List view', 'outofthebox'); ?></div>
                      </div>
                    </div>
                  </div>

                  <div class="option forfilebrowser forgallery forsearch">
                    <div class="outofthebox-option-title"><?php _e('Show file extension', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_showext" id="OutoftheBox_showext" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showext']) && '0' === $_REQUEST['showext']) ? '' : 'checked="checked"'; ?>/>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showext"></label>
                      </div>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('Display or Hide the file extensions', 'outofthebox'); ?></div>

                    <div class="outofthebox-option-title"><?php _e('Show files', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_showfiles" id="OutoftheBox_showfiles" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showfiles']) && '0' === $_REQUEST['showfiles']) ? '' : 'checked="checked"'; ?>/>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showfiles"></label>
                      </div>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('Display or Hide files', 'outofthebox'); ?></div>
                  </div>

                  <div class="outofthebox-option-title"><?php _e('Show folders', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_showfolders" id="OutoftheBox_showfolders" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showfolders']) && '0' === $_REQUEST['showfolders']) ? '' : 'checked="checked"'; ?>/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showfolders"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('Display or Hide child folders', 'outofthebox'); ?></div>

                  <div class="showfiles-options">
                    <div class="outofthebox-option-title"><?php _e('Amount of files', 'outofthebox'); ?>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('Number of files to show', 'outofthebox'); ?>. <?php _e('Can be used for instance to only show the last 5 updated documents', 'outofthebox'); ?>. <?php _e('Leave this field empty or set it to -1 for no limit', 'outofthebox'); ?></div>
                    <input type="text" name="OutoftheBox_maxfiles" id="OutoftheBox_maxfiles" placeholder="-1" value="<?php echo (isset($_REQUEST['maxfiles'])) ? $_REQUEST['maxfiles'] : ''; ?>"/>
                  </div>
                </div>

                <div class="option forgallery">
                  <div class="outofthebox-option-title"><?php _e('Show file names', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_showfilenames" id="OutoftheBox_showfilenames" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showfilenames']) && '1' === $_REQUEST['showfilenames']) ? 'checked="checked"' : ''; ?>/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showfilenames"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('Display or Hide the file names in the gallery', 'outofthebox'); ?></div>

                  <div class="outofthebox-option-title"><?php _e('Gallery row height', 'outofthebox'); ?></div>
                  <div class="outofthebox-option-description"><?php _e('The ideal height you want your grid rows to be', 'outofthebox'); ?>. <?php _e("It won't set it exactly to this as plugin adjusts the row height to get the correct width", 'outofthebox'); ?>. <?php _e('Leave empty for default value', 'outofthebox'); ?> (200px).</div>
                  <input type="text" name="OutoftheBox_targetHeight" id="OutoftheBox_targetHeight" placeholder="200" value="<?php echo (isset($_REQUEST['targetheight'])) ? $_REQUEST['targetheight'] : ''; ?>"/>

                  <div class="outofthebox-option-title"><?php _e('Number of images lazy loaded', 'outofthebox'); ?></div>
                  <div class="outofthebox-option-description"><?php _e('Number of images to be loaded each time', 'outofthebox'); ?>. <?php _e('Set to 0 to load all images at once', 'outofthebox'); ?>.</div>
                  <input type="text" name="OutoftheBox_maximage" id="OutoftheBox_maximage" placeholder="25" value="<?php echo (isset($_REQUEST['maximages'])) ? $_REQUEST['maximages'] : ''; ?>"/>

                  <div class="outofthebox-option-title"><?php _e('Show Folder Thumbnails in Gallery', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_folderthumbs" id="OutoftheBox_folderthumbs" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['folderthumbs']) && '0' === $_REQUEST['folderthumbs']) ? '' : 'checked="checked"'; ?> />
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_folderthumbs"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('Do you want to show thumbnails for the Folders in the gallery mode?', 'outofthebox'); ?> <?php _e('Please note, when enabled the loading performance can drop proportional to the number of folders present in the Gallery', 'outofthebox'); ?>.</div>

                  <div class="outofthebox-option-title"><?php _e('Crop Thumbnails', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_crop" id="OutoftheBox_crop" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['crop']) && '1' === $_REQUEST['crop']) ? 'checked="checked"' : ''; ?>/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_crop"></label>
                    </div>
                  </div>

                  <div class="outofthebox-option-title"><?php _e('Slideshow', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_slideshow" id="OutoftheBox_slideshow" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['slideshow']) && '1' === $_REQUEST['slideshow']) ? 'checked="checked"' : ''; ?> data-div-toggle="slideshow-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_slideshow"></label>
                    </div>
                  </div>

                  <div class="slideshow-options">                  
                    <div class="outofthebox-option-description"><?php _e('Enable or disable the Slideshow mode in the Lightbox', 'outofthebox'); ?></div>                  
                    <div class="outofthebox-option-title"><?php _e('Delay between cycles (ms)', 'outofthebox'); ?></div>
                    <div class="outofthebox-option-description"><?php _e('Delay between cycles in milliseconds, the default is 5000', 'outofthebox'); ?>.</div>
                    <input type="text" name="OutoftheBox_pausetime" id="OutoftheBox_pausetime" placeholder="5000" value="<?php echo (isset($_REQUEST['pausetime'])) ? $_REQUEST['pausetime'] : ''; ?>"/>
                  </div>
                </div>
              </div>
              <!-- End Layout Tab -->

              <!-- Sorting Tab -->
              <div id="settings_sorting"  class="outofthebox-tab-panel">

                <div class="outofthebox-tab-panel-header"><?php _e('Sorting', 'outofthebox'); ?></div>

                <div class="outofthebox-option-title"><?php _e('Sort field', 'outofthebox'); ?></div>
                <?php
                $sortfield = (!isset($_REQUEST['sortfield'])) ? 'name' : $_REQUEST['sortfield']; ?>
                <div class="outofthebox-option-radio">
                  <input type="radio" id="name" name="sort_field" <?php echo ('name' === $sortfield) ? 'checked="checked"' : ''; ?> value="name"/>
                  <label for="name" class="outofthebox-option-radio-label"><?php _e('Name', 'outofthebox'); ?></label>
                </div>
                <div class="outofthebox-option-radio">
                  <input type="radio" id="size" name="sort_field" <?php echo ('size' === $sortfield) ? 'checked="checked"' : ''; ?> value="size" />
                  <label for="size" class="outofthebox-option-radio-label"><?php _e('Size', 'outofthebox'); ?></label>
                </div>
                <div class="outofthebox-option-radio">
                  <input type="radio" id="modified" name="sort_field" <?php echo ('modified' === $sortfield) ? 'checked="checked"' : ''; ?> value="modified" />
                  <label for="modified" class="outofthebox-option-radio-label"><?php _e('Date modified', 'outofthebox'); ?></label>
                </div>
                <div class="outofthebox-option-radio">
                  <input type="radio" id="shuffle" name="sort_field" <?php echo ('shuffle' === $sortfield) ? 'checked="checked"' : ''; ?> value="shuffle" />
                  <label for="shuffle" class="outofthebox-option-radio-label"><?php _e('Shuffle/Random', 'outofthebox'); ?></label>
                </div>

                <div class="option-sort-field">
                  <div class="outofthebox-option-title"><?php _e('Sort order', 'outofthebox'); ?></div>

                  <?php
                  $sortorder = (isset($_REQUEST['sortorder']) && 'desc' === $_REQUEST['sortorder']) ? 'desc' : 'asc'; ?>
                  <div class="outofthebox-option-radio">
                    <input type="radio" id="asc" name="sort_order" <?php echo ('asc' === $sortorder) ? 'checked="checked"' : ''; ?> value="asc"/>
                    <label for="asc" class="outofthebox-option-radio-label"><?php _e('Ascending', 'outofthebox'); ?></label>
                  </div>
                  <div class="outofthebox-option-radio">
                    <input type="radio" id="desc" name="sort_order" <?php echo ('desc' === $sortorder) ? 'checked="checked"' : ''; ?> value="desc"/>
                    <label for="desc" class="outofthebox-option-radio-label"><?php _e('Descending', 'outofthebox'); ?></label>
                  </div>
                </div>
              </div>
              <!-- End Sorting Tab -->
              <!-- Advanced Tab -->
              <div id="settings_advanced"  class="outofthebox-tab-panel">
                <div class="outofthebox-tab-panel-header"><?php _e('Advanced', 'outofthebox'); ?></div>

                <div class="forfilebrowser forgallery forsearch">
                  <div class="outofthebox-option-title"><?php _e('Allow Preview', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_allow_preview" id="OutoftheBox_allow_preview" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['forcedownload']) && '1' === $_REQUEST['forcedownload']) ? '' : 'checked="checked"'; ?> data-div-toggle="preview-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_allow_preview"></label>
                    </div>
                  </div>


                  <div class="outofthebox-suboptions preview-options <?php echo (isset($_REQUEST['forcedownload']) && '1' === $_REQUEST['forcedownload']) ? 'hidden' : ''; ?>">
                    <div class="outofthebox-option-title"><?php _e('Inline Preview', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_previewinline" id="OutoftheBox_previewinline" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['previewinline']) && '0' === $_REQUEST['previewinline']) ? '' : 'checked="checked"'; ?> data-div-toggle="preview-options-inline"/>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_previewinline"></label>
                      </div>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('Open preview inside a lightbox or open in a new window', 'outofthebox'); ?></div>

                    <div class="outofthebox-suboptions preview-options-inline <?php echo (isset($_REQUEST['previewinline']) && '0' === $_REQUEST['previewinline']) ? 'hidden' : ''; ?>">

                      <div class="outofthebox-option-title"><?php _e('Enable Google pop out Button', 'outofthebox'); ?>
                        <div class="outofthebox-onoffswitch">
                          <input type="checkbox" name="OutoftheBox_canpopout" id="OutoftheBox_canpopout"  class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['canpopout']) && '1' === $_REQUEST['canpopout']) ? 'checked="checked"' : ''; ?>/>
                          <label class="outofthebox-onoffswitch-label" for="OutoftheBox_canpopout"></label>
                        </div>
                      </div>
                      <div class="outofthebox-option-description"><?php _e('Disables the Google Pop Out button which is visible in the inline preview for a couple of file formats', 'outofthebox'); ?>. </div>

                      <div class="outofthebox-option-title"><?php _e('Lightbox navigation', 'outofthebox'); ?>
                        <div class="outofthebox-onoffswitch">
                          <input type="checkbox" name="OutoftheBox_lightboxnavigation" id="OutoftheBox_lightboxnavigation"  class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['lightboxnavigation']) && '0' === $_REQUEST['lightboxnavigation']) ? '' : 'checked="checked"'; ?>/>
                          <label class="outofthebox-onoffswitch-label" for="OutoftheBox_lightboxnavigation"></label>
                        </div>
                      </div>
                      <div class="outofthebox-option-description"><?php _e('Navigate through your documents in the lightbox. Disable when each document should be shown individually without navigation arrows', 'outofthebox'); ?>. </div>

                    </div>
                  </div>

                  <div class="outofthebox-option-title"><?php _e('Allow Searching', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_search" id="OutoftheBox_search" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['search']) && '0' === $_REQUEST['search']) ? '' : 'checked="checked"'; ?> data-div-toggle="search-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_search"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('The search function allows your users to find files by filename and content (when files are indexed)', 'outofthebox'); ?></div>
                </div>

                <div class="option forfilebrowser foruploadbox forgallery forsearch">
                  <div class="option search-options <?php echo (isset($_REQUEST['search']) && '1' === $_REQUEST['search']) ? '' : 'hidden'; ?>">
                    <div class="outofthebox-option-title"><?php _e('Perform Full-Text search', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_search_field" id="OutoftheBox_search_field"  class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['searchcontents']) && '1' === $_REQUEST['searchcontents']) ? 'checked="checked"' : ''; ?>/>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_search_field"></label>
                      </div>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('Business Accounts only', 'outofthebox'); ?>. </div>
                  </div>
                </div>
                <div class=" forfilebrowser forsearch forgallery">
                  <?php
                  if (class_exists('ZipArchive')) {
                      ?>
                      <div class="outofthebox-option-title"><?php _e('Allow ZIP Download', 'outofthebox'); ?>
                        <div class="outofthebox-onoffswitch">
                          <input type="checkbox" name="OutoftheBox_candownloadzip" id="OutoftheBox_candownloadzip" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['candownloadzip']) && '1' === $_REQUEST['candownloadzip']) ? 'checked="checked"' : ''; ?>/>
                          <label class="outofthebox-onoffswitch-label" for="OutoftheBox_candownloadzip"></label>
                        </div>
                      </div>
                      <div class="outofthebox-option-description"><?php _e('Allow users to download multiple files at once', 'outofthebox'); ?></div>
                  <?php
                  } ?>
                </div>


                <div class="foraudio forvideo">
                  <div class="outofthebox-option-title"><?php _e('Auto Play', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_autoplay" id="OutoftheBox_autoplay" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['autoplay']) && '1' === $_REQUEST['autoplay']) ? 'checked="checked"' : ''; ?>>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_autoplay"></label>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('Autoplay is generally not recommended as it is seen as a negative user experience. It is also disabled in many browsers', 'outofthebox'); ?>.</div>
                  </div>
                </div>

                <div class="forvideo">
                  <div class="outofthebox-option-title"><?php _e('Enable Video Advertisements', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_media_ads" id="OutoftheBox_media_ads" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['ads']) && '1' === $_REQUEST['ads']) ? 'checked="checked"' : ''; ?> data-div-toggle="ads-options">
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_media_ads"></label>
                    </div>
                    <div class="outofthebox-option-description"><?php _e('The mediaplayer of the plugin supports VAST XML advertisments to offer monetization options for your videos. You can enable advertisments for the complete site on the Advanced tab of the plugin settings page and per shortcode. Currently, this plugin only supports Linear elements with MP4', 'outofthebox'); ?>.</div>
                  </div> 

                  <div class="outofthebox-suboptions ads-options <?php echo (isset($_REQUEST['ads']) && '1' === $_REQUEST['ads']) ? '' : 'hidden'; ?> ">
                    <div class="outofthebox-option-title"><?php echo 'VAST XML Tag Url'; ?></div>
                    <input type="text" name="OutoftheBox_media_ads" id="OutoftheBox_media_adstagurl" class="outofthebox-option-input-large" value="<?php echo (isset($_REQUEST['ads_tag_url'])) ? $_REQUEST['ads_tag_url'] : ''; ?>" placeholder="<?php echo $this->get_processor()->get_setting('mediaplayer_ads_tagurl'); ?>" />

                    <div class="oftb-warning">
                      <i><strong><?php _e('NOTICE', 'outofthebox'); ?></strong>: <?php _e('If you are unable to see the example VAST url below, please make sure you do not have an ad blocker enabled.', 'outofthebox'); ?>.</i>
                    </div>

                    <a href="https://pubads.g.doubleclick.net/gampad/ads?sz=640x480&iu=/124319096/external/single_ad_samples&ciu_szs=300x250&impl=s&gdfp_req=1&env=vp&output=vast&unviewed_position_start=1&cust_params=deployment%3Ddevsite%26sample_ct%3Dskippablelinear&correlator=" rel="no-follow">Example Tag URL</a>

                    <div class="outofthebox-option-title"><?php _e('Enable Skip Button', 'outofthebox'); ?>
                      <div class="outofthebox-onoffswitch">
                        <input type="checkbox" name="OutoftheBox_media_ads_skipable" id="OutoftheBox_media_ads_skipable" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['ads_skipable']) && '1' === $_REQUEST['ads_skipable']) ? 'checked="checked"' : ''; ?>data-div-toggle="ads_skipable"/>
                        <label class="outofthebox-onoffswitch-label" for="OutoftheBox_media_ads_skipable"></label>
                      </div>
                    </div>

                    <div class="outofthebox-suboptions ads_skipable <?php echo (isset($_REQUEST['ads_skipable']) && '0' === $_REQUEST['ads_skipable']) ? 'hidden' : ''; ?> ">
                      <div class="outofthebox-option-title"><?php _e('Skip button visible after (seconds)', 'outofthebox'); ?></div>
                      <input class="outofthebox-option-input-large" type="text" name="OutoftheBox_media_ads_skipable_after" id="OutoftheBox_media_ads_skipable_after" value="<?php echo (isset($_REQUEST['ads_skipable_after'])) ? $_REQUEST['ads_skipable_after'] : ''; ?>" placeholder="<?php echo $this->get_processor()->get_setting('mediaplayer_ads_skipable_after'); ?>">
                        <div class="outofthebox-option-description"><?php _e('Allow user to skip advertisment after after the following amount of seconds have elapsed', 'outofthebox'); ?></div>
                    </div>
                  </div>
                </div>

              </div>
              <!-- End Advanced Tab -->
              <!-- Exclusions Tab -->
              <div id="settings_exclusions"  class="outofthebox-tab-panel">
                <div class="outofthebox-tab-panel-header"><?php _e('Exclusions', 'outofthebox'); ?></div>

                <div class="outofthebox-option-title"><?php _e('Only show files with those extensions', 'outofthebox'); ?>:</div>
                <div class="outofthebox-option-description"><?php echo __('Add extensions separated with | e.g. (jpg|png|gif)', 'outofthebox').'. '.__('Leave empty to show all files', 'outofthebox'); ?>.</div>
                <input type="text" name="OutoftheBox_ext" id="OutoftheBox_ext" class="outofthebox-option-input-large" value="<?php echo (isset($_REQUEST['ext'])) ? $_REQUEST['ext'] : ''; ?>"/>

                <div class="outofthebox-option-title"><?php _e('Only show the following files or folders', 'outofthebox'); ?>:</div>
                <div class="outofthebox-option-description"><?php echo __('Add files or folders by name separated with | e.g. (file1.jpg|long folder name)', 'outofthebox'); ?>.</div>
                <input type="text" name="OutoftheBox_include" id="OutoftheBox_include" class="outofthebox-option-input-large" value="<?php echo (isset($_REQUEST['include'])) ? $_REQUEST['include'] : ''; ?>"/>

                <div class="outofthebox-option-title"><?php _e('Hide the following files or folders', 'outofthebox'); ?>:</div>
                <div class="outofthebox-option-description"><?php echo __('Add files or folders by name separated with | e.g. (file1.jpg|long folder name)', 'outofthebox'); ?>.</div>
                <input type="text" name="OutoftheBox_exclude" id="OutoftheBox_exclude"  class="outofthebox-option-input-large" value="<?php echo (isset($_REQUEST['exclude'])) ? $_REQUEST['exclude'] : ''; ?>"/>

                <div class="outofthebox-option-title"><?php _e('Show system files', 'outofthebox'); ?>
                  <div class="outofthebox-onoffswitch">
                    <input type="checkbox" name="OutoftheBox_showsystemfiles" id="OutoftheBox_showsystemfiles" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showsystemfiles']) && '1' === $_REQUEST['showsystemfiles']) ? 'checked="checked"' : ''; ?>/>
                    <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showsystemfiles"></label>
                  </div>
                </div>
                <div class="outofthebox-option-description"><?php _e('Display hidden system (dot) files (e.g. .DS_store, .config)', 'outofthebox'); ?></div>
              </div>
              <!-- End Exclusions Tab -->

              <!-- Upload Tab -->
              <div id="settings_upload"  class="outofthebox-tab-panel">

                <div class="outofthebox-tab-panel-header"><?php _e('Upload Box', 'outofthebox'); ?></div>

                <div class="outofthebox-option-title"><?php _e('Allow Upload', 'outofthebox'); ?>
                  <div class="outofthebox-onoffswitch">
                    <input type="checkbox" name="OutoftheBox_upload" id="OutoftheBox_upload" data-div-toggle="upload-options" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['upload']) && '1' === $_REQUEST['upload']) ? 'checked="checked"' : ''; ?>/>
                    <label class="outofthebox-onoffswitch-label" for="OutoftheBox_upload"></label>
                  </div>
                </div>
                <div class="outofthebox-option-description"><?php _e('Allow users to upload files', 'outofthebox'); ?>. <?php echo sprintf(__('You can select which Users Roles should be able to upload via %s', 'outofthebox'), '<a href="#" onclick="jQuery(\'li[data-tab=settings_permissions]\').trigger(\'click\')">'.__('User Permissions', 'outofthebox').'</a>'); ?>.</div>

                <div class="outofthebox-suboption upload-options <?php echo (isset($_REQUEST['upload']) && '1' === $_REQUEST['upload'] && in_array($mode, ['files', 'upload', 'gallery'])) ? '' : 'hidden'; ?>">

                  <div class="outofthebox-option-title"><?php _e('Allow folder upload', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_upload_folder" id="OutoftheBox_upload_folder"  class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['upload_folder']) && '0' === $_REQUEST['upload_folder']) ? '' : 'checked="checked"'; ?>/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_upload_folder"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('Adds an Add Folder button to the upload form if the browser supports it', 'outofthebox'); ?>. </div>

                  <div class="outofthebox-option-title"><?php _e('Overwrite existing files', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_overwrite" id="OutoftheBox_overwrite"  class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['overwrite']) && '1' === $_REQUEST['overwrite']) ? 'checked="checked"' : ''; ?>/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_overwrite"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('Overwrite already existing files or auto-rename the new uploaded files', 'outofthebox'); ?>. </div>

                  <div class="outofthebox-option-title"><?php _e('Restrict file extensions', 'outofthebox'); ?></div>
                  <div class="outofthebox-option-description"><?php echo __('Add extensions separated with | e.g. (jpg|png|gif)', 'outofthebox').' '.__('Leave empty for no restriction', 'outofthebox', 'outofthebox'); ?>.</div>
                  <input type="text" name="OutoftheBox_upload_ext" id="OutoftheBox_upload_ext" value="<?php echo (isset($_REQUEST['uploadext'])) ? $_REQUEST['uploadext'] : ''; ?>"/>

                  <div class="outofthebox-option-title"><?php _e('Max uploads per session', 'outofthebox'); ?></div>
                  <div class="outofthebox-option-description"><?php echo __('Number of maximum uploads per upload session', 'outofthebox').' '.__('Leave empty for no restriction', 'outofthebox'); ?>.</div>
                  <input type="text" name="OutoftheBox_maxnumberofuploads" id="OutoftheBox_maxnumberofuploads" placeholder="-1" value="<?php echo (isset($_REQUEST['maxnumberofuploads'])) ? $_REQUEST['maxnumberofuploads'] : ''; ?>"/>

                  <div class="outofthebox-option-title"><?php _e('Minimum file size', 'outofthebox'); ?></div>
                  <?php
                  // Convert bytes to MB when needed
                  $min_size_value = (isset($_REQUEST['minfilesize']) ? $_REQUEST['minfilesize'] : '');
            if (!empty($min_size_value) && ctype_digit($min_size_value)) {
                $min_size_value = \TheLion\OutoftheBox\Helpers::bytes_to_size_1024($min_size_value);
            } ?>
                  <div class="outofthebox-option-description"><?php _e('Min filesize (e.g. 1 MB) for uploading', 'outofthebox'); ?>. <?php echo __('Leave empty for no restriction', 'outofthebox'); ?>.</div>
                  <input type="text" name="OutoftheBox_minfilesize" id="OutoftheBox_minfilesize" value="<?php echo $min_size_value; ?>"/>

                  <div class="outofthebox-option-title"><?php _e('Maximum file size', 'outofthebox'); ?></div>
                  <?php
                  $max_size_bytes = min(\TheLion\OutoftheBox\Helpers::return_bytes(ini_get('post_max_size')), \TheLion\OutoftheBox\Helpers::return_bytes(ini_get('upload_max_filesize')));
            $max_size_string = \TheLion\OutoftheBox\Helpers::bytes_to_size_1024($max_size_bytes);

            // Convert bytes in version before 1.8 to MB
            $max_size_value = (isset($_REQUEST['maxfilesize']) ? $_REQUEST['maxfilesize'] : '');
            if (!empty($max_size_value) && ctype_digit($max_size_value)) {
                $max_size_value = \TheLion\OutoftheBox\Helpers::bytes_to_size_1024($max_size_value);
            } ?>
                  <div class="outofthebox-option-description"><?php _e('Max filesize for uploading', 'outofthebox'); ?>. <?php _e('Leave empty for server maximum', 'outofthebox'); ?> (<?php echo $max_size_string; ?>).</div>
                  <input type="text" name="OutoftheBox_maxfilesize" id="OutoftheBox_maxfilesize" placeholder="<?php echo $max_size_string; ?>" value="<?php echo $max_size_value; ?>"/>

                </div>
              </div>
              <!-- End Upload Tab -->

              <!-- Notifications Tab -->
              <div id="settings_notifications"  class="outofthebox-tab-panel">

                <div class="outofthebox-tab-panel-header"><?php _e('Notifications', 'outofthebox'); ?></div>

                <div class="outofthebox-option-title"><?php _e('Download email notification', 'outofthebox'); ?>
                  <div class="outofthebox-onoffswitch">
                    <input type="checkbox" name="OutoftheBox_notificationdownload" id="OutoftheBox_notificationdownload" class="outofthebox-onoffswitch-checkbox"  <?php echo (isset($_REQUEST['notificationdownload']) && '1' === $_REQUEST['notificationdownload']) ? 'checked="checked"' : ''; ?>/>
                    <label class="outofthebox-onoffswitch-label" for="OutoftheBox_notificationdownload"></label>
                  </div>
                </div>

                <div class="outofthebox-option-title"><?php _e('Upload email notification', 'outofthebox'); ?>
                  <div class="outofthebox-onoffswitch">
                    <input type="checkbox" name="OutoftheBox_notificationupload" id="OutoftheBox_notificationupload" class="outofthebox-onoffswitch-checkbox"  <?php echo (isset($_REQUEST['notificationupload']) && '1' === $_REQUEST['notificationupload']) ? 'checked="checked"' : ''; ?>/>
                    <label class="outofthebox-onoffswitch-label" for="OutoftheBox_notificationupload"></label>
                  </div>
                </div>
                <div class="outofthebox-option-title"><?php _e('Delete email notification', 'outofthebox'); ?>
                  <div class="outofthebox-onoffswitch">
                    <input type="checkbox" name="OutoftheBox_notificationdeletion" id="OutoftheBox_notificationdeletion" class="outofthebox-onoffswitch-checkbox"  <?php echo (isset($_REQUEST['notificationdeletion']) && '1' === $_REQUEST['notificationdeletion']) ? 'checked="checked"' : ''; ?>/>
                    <label class="outofthebox-onoffswitch-label" for="OutoftheBox_notificationdeletion"></label>
                  </div>
                </div>

                <div class="outofthebox-option-title"><?php _e('Recipients', 'outofthebox'); ?></div>
                <div class="outofthebox-option-description"><?php _e('On which email address would you like to receive the notification? You can use <code>%admin_email%</code> and <code>%user_email%</code> (user that executes the action) and <code>%linked_user_email%</code> (Private Folders owners)', 'outofthebox'); ?>.</div>
                <input type="text" name="OutoftheBox_notification_email" id="OutoftheBox_notification_email" class="outofthebox-option-input-large" placeholder="<?php echo get_option('admin_email'); ?>" value="<?php echo (isset($_REQUEST['notificationemail'])) ? $_REQUEST['notificationemail'] : ''; ?>" />

                <div class="oftb-warning">
                  <i><strong><?php _e('NOTICE', 'outofthebox'); ?></strong>: <?php echo sprintf(__('%s can be used to send notications to the owner(s) of the Private Folder', 'outofthebox'), '<code>%linked_user_email%</code>'); ?>. <?php echo sprintf(__('When using this placeholder in combination with automatically linked Private Folders, the %sName Template%s should contain %s', 'outofthebox'), '<a href="'.admin_url('admin.php?page=OutoftheBox_settings#settings_userfolders').'" target="_blank">', '</a>', '<code>%user_email%</code>'); ?>. <?php _e('I.e. the Private Folder name needs to contain the email address', 'outofthebox'); ?>.</i>
                </div>

                <div class="outofthebox-option-title"><?php _e('Skip notification of the user that executes the action', 'outofthebox'); ?>
                  <div class="outofthebox-onoffswitch">
                    <input type="checkbox" name="OutoftheBox_notification_skip_email_currentuser" id="OutoftheBox_notification_skip_email_currentuser" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['notification_skipemailcurrentuser']) && '1' === $_REQUEST['notification_skipemailcurrentuser']) ? 'checked="checked"' : ''; ?>/>
                    <label class="outofthebox-onoffswitch-label" for="OutoftheBox_notification_skip_email_currentuser"></label>
                  </div>
                </div>

              </div>
              <!-- End Notifications Tab -->

              <!-- Manipulation Tab -->
              <div id="settings_manipulation"  class="outofthebox-tab-panel">
                <div class="outofthebox-tab-panel-header"><?php _e('File Manipulation', 'outofthebox'); ?></div>

                <div class="option forfilebrowser forgallery">

                  <div class="outofthebox-option-title"><?php _e('Allow Linking', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_deeplink" id="OutoftheBox_deeplink" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['deeplink']) && '1' === $_REQUEST['deeplink']) ? 'checked="checked"' : ''; ?> data-div-toggle="deeplink-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_deeplink"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('Allow users to generate a link to a folder/file on your website. Only users with access to the shortcode and content will be able to open the link', 'outofthebox'); ?></div>

                  <div class="outofthebox-option-title"><?php _e('Allow Sharing', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_showsharelink" id="OutoftheBox_showsharelink" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['showsharelink']) && '1' === $_REQUEST['showsharelink']) ? 'checked="checked"' : ''; ?> data-div-toggle="sharing-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_showsharelink"></label>
                    </div>
                  </div>
                  <div class="outofthebox-option-description"><?php _e('Allow users to generate permanent shared links to the files', 'outofthebox'); ?></div>

                  <div class="outofthebox-option-title"><?php _e('Rename files and folders', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_rename" id="OutoftheBox_rename" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['rename']) && '1' === $_REQUEST['rename']) ? 'checked="checked"' : ''; ?> data-div-toggle="rename-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_rename"></label>
                    </div>
                  </div>

                  <div class="outofthebox-option-title"><?php _e('Move files and folders', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_move" id="OutoftheBox_move" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['move']) && '1' === $_REQUEST['move']) ? 'checked="checked"' : ''; ?> data-div-toggle="move-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_move"></label>
                    </div>
                  </div>

                  <div class="outofthebox-option-title"><?php _e('Delete files and folders', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_delete" id="OutoftheBox_delete" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['delete']) && '1' === $_REQUEST['delete']) ? 'checked="checked"' : ''; ?> data-div-toggle="delete-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_delete"></label>
                    </div>
                  </div>
                </div>

                <div class="option forfilebrowser forgallery">
                  <div class="outofthebox-option-title"><?php _e('Create new folders', 'outofthebox'); ?>
                    <div class="outofthebox-onoffswitch">
                      <input type="checkbox" name="OutoftheBox_addfolder" id="OutoftheBox_addfolder" class="outofthebox-onoffswitch-checkbox" <?php echo (isset($_REQUEST['addfolder']) && '1' === $_REQUEST['addfolder']) ? 'checked="checked"' : ''; ?> data-div-toggle="addfolder-options"/>
                      <label class="outofthebox-onoffswitch-label" for="OutoftheBox_addfolder"></label>
                    </div>
                  </div>
                </div>

                <br/><br/>

                <div class="outofthebox-option-description">
                  <?php echo sprintf(__('Select via %s which User Roles are able to perform the actions', 'outofthebox'), '<a href="#" onclick="jQuery(\'li[data-tab=settings_permissions]\').trigger(\'click\')">'.__('User Permissions', 'outofthebox').'</a>'); ?>.
                </div>

              </div>
              <!-- End Manipulation Tab -->
              <!-- Permissions Tab -->
              <div id="settings_permissions"  class="outofthebox-tab-panel">
                <div class="outofthebox-tab-panel-header"><?php _e('User Permissions', 'outofthebox'); ?></div>

                <div class="outofthebox-accordion">

                  <div class="option forfilebrowser foruploadbox forupload forgallery foraudio forvideo forsearch outofthebox-permissions-box">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can see the plugin', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['viewrole'])) ? explode('|', $_REQUEST['viewrole']) : ['administrator', 'author', 'contributor', 'editor', 'subscriber', 'pending', 'guest'];
            wp_roles_and_users_input('OutoftheBox_view_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option forfilebrowser forsearch outofthebox-permissions-box preview-options">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can preview', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['previewrole'])) ? explode('|', $_REQUEST['previewrole']) : ['all'];
            wp_roles_and_users_input('OutoftheBox_preview_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option forfilebrowser forupload forgallery foraudio forvideo forsearch outofthebox-permissions-box">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can download', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['downloadrole'])) ? explode('|', $_REQUEST['downloadrole']) : ['all'];
            wp_roles_and_users_input('OutoftheBox_download_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery forupload upload-options">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can upload', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['uploadrole'])) ? explode('|', $_REQUEST['uploadrole']) : ['administrator', 'author', 'contributor', 'editor', 'subscriber'];
            wp_roles_and_users_input('OutoftheBox_upload_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery forsearch deeplink-options ">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can link to content', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['deeplinkrole'])) ? explode('|', $_REQUEST['deeplinkrole']) : ['all'];
            wp_roles_and_users_input('OutoftheBox_deeplink_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery forsearch sharing-options ">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can share content', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['sharerole'])) ? explode('|', $_REQUEST['sharerole']) : ['all'];
            wp_roles_and_users_input('OutoftheBox_share_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery forsearch rename-options ">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can rename files', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['renamefilesrole'])) ? explode('|', $_REQUEST['renamefilesrole']) : ['administrator', 'author', 'contributor', 'editor'];
            wp_roles_and_users_input('OutoftheBox_renamefiles_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery forsearch rename-options ">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can rename folders', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['renamefoldersrole'])) ? explode('|', $_REQUEST['renamefoldersrole']) : ['administrator', 'author', 'contributor', 'editor'];
            wp_roles_and_users_input('OutoftheBox_renamefolders_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery forsearch move-options">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can move files and folders', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['moverole'])) ? explode('|', $_REQUEST['moverole']) : ['administrator', 'editor'];
            wp_roles_and_users_input('OutoftheBox_move_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery forsearch delete-options ">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can delete files', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['deletefilesrole'])) ? explode('|', $_REQUEST['deletefilesrole']) : ['administrator', 'author', 'contributor', 'editor'];
            wp_roles_and_users_input('OutoftheBox_deletefiles_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery forsearch delete-options ">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can delete folders', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['deletefoldersrole'])) ? explode('|', $_REQUEST['deletefoldersrole']) : ['administrator', 'author', 'contributor', 'editor'];
            wp_roles_and_users_input('OutoftheBox_deletefolders_role', $selected); ?>
                    </div>
                  </div>

                  <div class="option outofthebox-permissions-box forfilebrowser forgallery addfolder-options ">
                    <div class="outofthebox-accordion-title outofthebox-option-title"><?php _e('Who can create new folders', 'outofthebox'); ?></div>
                    <div>
                      <?php
                      $selected = (isset($_REQUEST['addfolderrole'])) ? explode('|', $_REQUEST['addfolderrole']) : ['administrator', 'author', 'contributor', 'editor'];
            wp_roles_and_users_input('OutoftheBox_addfolder_role', $selected); ?>
                    </div>
                  </div>

                </div>
              </div>
              <!-- End Permissions Tab -->

            </div>
            <?php
        }
        ?>

        <div class="footer">

        </div>
      </div>
    </form>
    <script type="text/javascript">
        var whitelist = <?php echo json_encode(TheLion\OutoftheBox\Helpers::get_all_users_and_roles()); ?>; /* Build Whitelist for permission selection */
    </script>
  </body>
</html>