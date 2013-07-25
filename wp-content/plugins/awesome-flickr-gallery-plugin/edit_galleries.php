<?php
include_once('afg_libs.php');
$default_gallery_id = 0;
$warning = false;

if ($_POST && isset($_POST['afg_edit_gallery_name'])) {
    global $default_gallery_id;
    global $warning;

    if ($_POST['afg_per_page_check']) $_POST['afg_per_page'] = '';
    else {
        if (!(ctype_digit($_POST['afg_per_page']) && (int)$_POST['afg_per_page'])) {
            $_POST['afg_per_page'] = '';
            $warning = true;
        }
    }


    $gallery = array(
        'name' => stripslashes($_POST['afg_edit_gallery_name']),
        'gallery_descr' => stripslashes($_POST['afg_edit_gallery_descr']),
        'photo_source' => $_POST['afg_photo_source_type'],
        'per_page' => afg_filter($_POST['afg_per_page']),
        'sort_order' => afg_filter($_POST['afg_sort_order']),
        'photo_size' => afg_filter($_POST['afg_photo_size']),
        'captions' => afg_filter($_POST['afg_captions']),
        'descr' => afg_filter($_POST['afg_descr']),
        'columns' => afg_filter($_POST['afg_columns']),
        'slideshow_option' => afg_filter($_POST['afg_slideshow_option']),
        'credit_note' => afg_filter($_POST['afg_credit_note']),
        'width' => afg_filter($_POST['afg_width']),
        'pagination' => afg_filter($_POST['afg_pagination']),
        'bg_color' => afg_filter($_POST['afg_bg_color']),
    );

    if ($_POST['afg_photo_source_type'] == 'photoset') $gallery['photoset_id'] = $_POST['afg_photosets_box'];
    else if ($_POST['afg_photo_source_type'] == 'gallery') $gallery['gallery_id'] = $_POST['afg_galleries_box'];
    else if ($_POST['afg_photo_source_type'] == 'group') $gallery['group_id'] = $_POST['afg_groups_box'];
    else if ($_POST['afg_photo_source_type'] == 'tags') $gallery['tags'] = $_POST['afg_tags'];

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

    $id = $_POST['afg_photo_gallery'];

    $galleries = get_option('afg_galleries');
    $galleries[$id] = $gallery;
    update_option('afg_galleries', $galleries);
    $default_gallery_id = $id;
}

function afg_edit_galleries_header() {
    $params = array(
        'api_key' => get_option('afg_api_key'),
        'user_id' => get_option('afg_user_id'),
        'default_per_page' => get_option('afg_per_page'),
        'galleries' => json_encode(get_option('afg_galleries')),
    );
    wp_enqueue_script('edit-galleries-script');
    wp_localize_script('edit-galleries-script', 'genparams', $params);
}

function afg_get_galleries($default='') {
    $galleries = get_option('afg_galleries');
    $gstr = "";
    foreach($galleries as $id => $ginfo) {
        if ($id) {
            if ($id == $default)
                $gstr .= "<option value=\"$id\" selected>$id - {$ginfo['name']}</option>";
            else
                $gstr .= "<option value=\"$id\">$id - {$ginfo['name']}</option>";
        }
    }
    return $gstr;
}

function afg_edit_galleries() {
    global $afg_photo_size_map, $afg_on_off_map,
        $afg_descr_map, $afg_columns_map, $afg_bg_color_map,
        $afg_photo_source_map, $default_gallery_id, $pf;

    $user_id = get_option('afg_user_id');

    $cur_page_url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    preg_match('/\&gallery_id=(?P<gallery_id>\d+)/', $cur_page_url, $matches);
    if ($matches && !$default_gallery_id) {
        $default_gallery_id = $matches['gallery_id'];
        $match_pos = strpos($cur_page_url, "&gallery_id=$default_gallery_id");
        $cur_page_url = substr($cur_page_url, 0, $match_pos);
    }

    $photosets_map = array();
    $rsp_obj = $pf->photosets_getList($user_id);
    if (!$pf->error_code) {
        foreach($rsp_obj['photoset'] as $photoset) {
            $photosets_map[$photoset['id']] = $photoset['title']['_content'];
        }
    }

    $galleries_map = array();
    $rsp_obj = $pf->galleries_getList($user_id);
    if (!$pf->error_code) {
        foreach($rsp_obj['galleries']['gallery'] as $gallery) {
            $galleries_map[$gallery['id']] = $gallery['title']['_content'];
        }
    }

    $groups_map = array();
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
    echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Edit Galleries | Awesome Flickr Gallery</h2>

<?php
        if ($_POST && $_POST['afg_edit_gallery_name']) {
            global $warning;
            if ($warning) {
                echo "<div class='updated'><p><strong>You entered invalid value for Per Page option.  It has been set to Default.</strong></p></div>";
                $warning = false;
            }
            echo "<div class='updated'><p><strong>Gallery updated successfully.</strong></p></div>";
        }
    echo afg_generate_version_line();
    $url=$_SERVER['REQUEST_URI'];
?>

         <form method='post' action='<?php echo $url ?>'>
            <div class="postbox-container" style="width:69%; margin-right:1%">

               <div id="poststuff">
                  <div class="postbox" style='box-shadow:0 0 2px'>
                     <h3>Saved Galleries</h3>
                     <table class='form-table'>
                        <tr valign='top'>
                           <th scope='row'>Select Gallery to Edit</th>
                           <td><select id='afg_photo_gallery' name='afg_photo_gallery' onchange='loadGallerySettings()'>
                                 <?php echo afg_get_galleries($default_gallery_id) ?>
                           </select></td>
                           <tr valign='top'>
                              <th scope='row'>Gallery Name</th>
                              <td><input maxlength='30' type='text' id='afg_edit_gallery_name' name='afg_edit_gallery_name' onblur='verifyEditBlank()' value="" /><font size='3' color='red'>*</font></td>
                           </tr>
                           <tr valign='top'>
                              <th scope='row'>Gallery Description</th>
                              <td><input maxlength='100' size='70%' type='text' id='afg_edit_gallery_descr' name='afg_edit_gallery_descr' value="" /></td>
                           </tr>
                        </table>
                  </div></div>

<?php
    echo afg_generate_flickr_settings_table($photosets_map, $galleries_map, $groups_map);
    echo afg_generate_gallery_settings_table();
    $gals = get_option('afg_galleries');
    if (sizeof($gals) == 1) $disable_submit = True;
    else $disable_submit = False;
?>

                  <input type="submit" id="afg_save_changes" class="button-primary"
                  <?php if ($disable_submit) echo "disabled='yes'"; ?>
                  value="Save Changes" />
                  <br /><br />
                  <div id="poststuff">
                     <div class="postbox" style='box-shadow:0 0 2px'>
                        <h3>Gallery Code</h3>
                        <table class='form-table'>
                           <tr valign='top'>
                              <td>
                                 <p id='afg_flickr_gallery_code'>[AFG_gallery]</p>
                              </td>
                           </tr>
                        </table>
                  </div></div>
               </div>
               <div class="postbox-container" style="width: 29%;">
<?php
    echo afg_box('Usage Instructions', 'Insert the Gallery Code in any of your posts of pages to display your Flickr Gallery.');
    echo afg_donate_box();
    echo afg_share_box();
?>
               </div>
            </form>
<?php
}
