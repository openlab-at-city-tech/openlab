<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Exit if no permission to link users
if (
  !(\TheLion\OutoftheBox\Helpers::check_user_role($this->get_main()->settings['permissions_link_users']))
) {
    die();
}

?>
<div>
  <div class="outofthebox admin-settings">

    <div class="outofthebox-header">
      <div class="outofthebox-logo"><a href="https://www.wpcloudplugins.com" target="_blank"><img src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/wpcp-logo-dark.svg" height="64" width="64" /></a></div>
      <div class="outofthebox-title"><?php _e('Link Private Folders','wpcloudplugins'); ?></div>
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

<script type="text/javascript">
  jQuery(function($) {
    /* Add Link to event*/
    $('.outofthebox .linkbutton').click(function() {
      $('.outofthebox .thickbox_opener').removeClass("thickbox_opener");
      $(this).parent().addClass("thickbox_opener");
      tb_show("(Re) link to folder", '#TB_inline?height=450&amp;width=800&amp;inlineId=oftb-embedded');
    });

    $('.outofthebox .unlinkbutton').click(function() {
      var curbutton = $(this),
        user_id = $(this).attr('data-user-id');

      $.ajax({
        type: "POST",
        url: OutoftheBox_vars.ajax_url,
        data: {
          action: 'outofthebox-unlinkusertofolder',
          userid: user_id,
          _ajax_nonce: OutoftheBox_vars.createlink_nonce
        },
        beforeSend: function() {
          curbutton.parent().find('.oftb-spinner').show();
        },
        success: function(response) {
          if (response === '1') {

            curbutton.addClass('hidden');
            curbutton.prev().removeClass('hidden');
            curbutton.parent().parent().find('.column-private_folder').text('');
          } else {
            location.reload(true);
          }
        },
        complete: function(reponse) {
          $('.oftb-spinner').hide();
        },
        dataType: 'text'
      });
    });
  });
</script>