<?php
include_once('afg_libs.php');

function afg_add_gallery() {
    global $afg_photo_size_map, $afg_on_off_map,
        $afg_descr_map, $afg_columns_map, $afg_bg_color_map,
        $afg_photo_source_map, $pf;

    $user_id = get_option('afg_user_id');

    $photosets_map = array();
    $groups_map = array();
    $galleries_map = array();
    $rsp_obj = $pf->photosets_getList($user_id);
    if (!$pf->error_code) {
        foreach($rsp_obj['photoset'] as $photoset) {
            $photosets_map[$photoset['id']] = $photoset['title']['_content'];
        }
    }

    $rsp_obj = $pf->galleries_getList($user_id);
    if (!$pf->error_code) {
        foreach($rsp_obj['galleries']['gallery'] as $gallery) {
            $galleries_map[$gallery['id']] = $gallery['title']['_content'];
        }
    }

    if (get_option('afg_flickr_token')) {
        $rsp_obj = $pf->groups_pools_getGroups();
        if (!$pf->error_code) {
            foreach($rsp_obj['group'] as $group) {
                $groups_map[$group['nsid']] = $group['name'];
            }
        }
    }
    else {
        $rsp_obj = $pf->people_getPublicGroups($user_id);
        if (!$pf->error_code) {
            foreach($rsp_obj as $group) {
                $groups_map[$group['nsid']] = $group['name'];
            }
        }
    }
    ?>

   <div class='wrap'>
   <h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
    echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Add Gallery | Awesome Flickr Gallery</h2>

<?php
        if ($_POST && $_POST['afg_add_gallery_name']) {
            if (isset($_POST['afg_per_page_check']) && $_POST['afg_per_page_check']) $_POST['afg_per_page'] = '';
            else {
                if (!(ctype_digit($_POST['afg_per_page']) && (int)$_POST['afg_per_page'])) {
                    $_POST['afg_per_page'] = '';
                    echo "<div class='updated'><p><strong>You entered invalid value for Per Page option.  It has been set to Default.</strong></p></div>";
                }
            }
            $gallery = array(
                'name' => $_POST['afg_add_gallery_name'],
                'gallery_descr' => $_POST['afg_add_gallery_descr'],
                'photo_source' => $_POST['afg_photo_source_type'],
                'per_page' => afg_filter($_POST['afg_per_page']),
                'sort_order' => afg_filter($_POST['afg_sort_order']),
                'photo_size' => afg_filter($_POST['afg_photo_size']),
                'captions' => afg_filter($_POST['afg_captions']),
                'descr' => afg_filter($_POST['afg_descr']),
                'columns' => afg_filter($_POST['afg_columns']),
                'slideshow_option' => afg_filter($_POST['afg_slideshow_option']),
                'credit_note' => afg_filter($_POST['afg_credit_note']),
                'bg_color' => afg_filter($_POST['afg_bg_color']),
                'width' => afg_filter($_POST['afg_width']),
                'pagination' => afg_filter($_POST['afg_pagination']),
            );

            if ($_POST['afg_photo_source_type'] == 'photoset')
                $gallery['photoset_id'] = $_POST['afg_photosets_box'];
            else if ($_POST['afg_photo_source_type'] == 'gallery')
                $gallery['gallery_id'] = $_POST['afg_galleries_box'];
            else if ($_POST['afg_photo_source_type'] == 'group')
                $gallery['group_id'] = $_POST['afg_groups_box'];
            else if ($_POST['afg_photo_source_type'] == 'tags')
                $gallery['tags'] = $_POST['afg_tags'];

            if ($gallery['photo_size'] == 'custom') {
                if (ctype_digit($_POST['afg_custom_size']) && (int)$_POST['afg_custom_size'] >= 50 && (int)$_POST['afg_custom_size'] <= 500) {
                    $gallery['custom_size'] = $_POST['afg_custom_size'];
                }
                else {
                    $gallery['custom_size'] = 100;
                    echo "<div class='updated'><p><strong>You entered invalid value for Custom Width option.  It has been set to 100.</strong></p></div>";

                }
                $gallery['custom_size_square'] = $_POST['afg_custom_size_square']?$_POST['afg_custom_size_square']:'false';
            }

            $galleries = get_option('afg_galleries');
            $galleries[] = $gallery;
            update_option('afg_galleries', $galleries);
            end($galleries);
            $id = key($galleries);
?>
            <div class="updated"><p><strong>
                  <?php echo "Gallery \"{$_POST['afg_add_gallery_name']}\" created successfully.  Shortcode for this gallery is </strong>[AFG_gallery id='$id']"; ?>
               </p></div>

<?php
        }

    echo afg_generate_version_line();
    $url=$_SERVER['REQUEST_URI'];
?>

            <form method='post' action='<?php echo $url ?>'>
               <div class="postbox-container" style="width:69%; margin-right:1%">
                  <div id="poststuff">
                     <div class="postbox" style='box-shadow:0 0 2px'>
                        <h3>Gallery Parameters</h3>
                        <table class='form-table'>
                           <tr valign='top'>
                              <th scope='row'>Gallery Name</th>
                              <td><input maxlength='30' type='text' id='afg_add_gallery_name' name='afg_add_gallery_name' onblur='verifyBlank()' value='' /><font size='3' color='red'>*</font></td>
                           </tr>
                           <tr valign='top'>
                              <th scope='row'>Gallery Description</th>
                              <td><input maxlength='100' size='70%' type='text' id='afg_add_gallery_descr' name='afg_add_gallery_descr'" value="" /></td>
                           </tr>
                        </table>
                  </div></div>
<?php
    echo afg_generate_flickr_settings_table($photosets_map, $galleries_map, $groups_map);
    echo afg_generate_gallery_settings_table();
?>
                  <input type="submit" disabled='true' id="afg_save_changes" class="button-primary"
                  value="Add Gallery" />
               </div>
               <div class="postbox-container" style="width: 29%;">
<?php
    $message = "<b>Gallery Description</b> - Provide a meaningful description of" .
        " your gallery for you to recognize it easily.<br /><br />" .
        " <b>Gallery Source</b> - Where do you want to fetch your photos from?" .
        " Your Flickr Photostream, a Photoset, a Gallery or a Group?<br /><br />" .
        " <b>What is <i>Default</i>?</b> - When you select <i>" .
        " Default</i> for a setting, it will be inherited from <a href=\"" .
        $_SERVER['PHP_SELF'] . "?page=afg_plugin_page\"><i>Default" .
        " Settings</i></a>.  The setting here is stored as reference to the" .
        " setting on Default Settings page, so if you change the <i>Default" .
        " Settings</i>, the setting for this specific gallery will also change.";
    echo afg_box('Help', $message);
    echo afg_donate_box();
    echo afg_share_box();
?>
               </div>
                </form>
<?php
}
