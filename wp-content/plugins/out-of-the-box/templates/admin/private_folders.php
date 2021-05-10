<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Exit if no permission to link users
if (
  !(\TheLion\OutoftheBox\Helpers::check_user_role($this->get_main()->settings['permissions_link_users']))
) {
    exit();
}

?>
<div>
  <div class="outofthebox admin-settings">
    <div class="wrap">
      <div class="outofthebox-header">
        <div class="outofthebox-logo"><a href="https://www.wpcloudplugins.com" target="_blank"><img
              src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/wpcp-logo-dark.svg" height="64" width="64" /></a>
        </div>
        <div class="outofthebox-title"><?php esc_html_e('Link Private Folders', 'wpcloudplugins'); ?></div>
      </div>

      <div class="outofthebox-panel outofthebox-panel-full">
        <div>
          <form method="post">
            <input type="hidden" name="page" value="oftb_list_table" />
            <?php
          $users_list = new \TheLion\OutoftheBox\User_List_Table();
          $users_list->views();
          $users_list->prepare_items();
          $users_list->search_box('search', 'search_id');
          $users_list->display(); ?>
          </form>
        </div>
        <div id='oftb-embedded' style='clear:both;display:none'>
          <?php
        $processor = $this->_main->get_processor();

        echo $processor->create_from_shortcode(
            [
                'singleaccount' => '0',
                'mode' => 'files',
                'filelayout' => 'grid',
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
                'mcepopup' => 'linkto',
                'search' => '0',
            ]
        ); ?>
        </div>
      </div>
      <div class="footer"></div>
    </div>
  </div>
</div>