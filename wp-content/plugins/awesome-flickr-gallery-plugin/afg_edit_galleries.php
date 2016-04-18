<?php
include_once('afg_libs.php');
$default_gallery_id = 0;
$warning = false;

if (isset($_POST['afg_edit_gallery_name']) && $_POST['afg_edit_gallery_name']) {
    global $default_gallery_id;
    global $warning;

    if (isset($_POST['afg_per_page_check']) && $_POST['afg_per_page_check']) $_POST['afg_per_page'] = '';
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
            if (!is_dir(dirname(__FILE__) . "/cache")) {
                if (!wp_mkdir_p(dirname(__FILE__) . "/cache")) {
                    echo("<div class='updated'><p>Could not create directory - '" . dirname(__FILE__) . "/cache'. This is required for custom size photos to be displayed. Manually create this directory and set permissions for this directory as 777.</p></div>");
                }
            }
        }
        else {
            $gallery['custom_size'] = 100;
            echo "<div class='updated'><p><strong>You entered invalid value for Custom Width option.  It has been set to 100.</strong></p></div>";

        }
        $gallery['custom_size_square'] = isset($_POST['afg_custom_size_square'])? $_POST['afg_custom_size_square']:'false';
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
    $groups_map = array();
    $galleries_map = array();

    afg_get_sets_groups_galleries($photosets_map, $groups_map, $galleries_map, $user_id);
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
            <div id="afg-wrap">
                  <div id="afg-main-box">
                     <h3>Saved Galleries</h3>
                     <table class='widefat fixed afg-settings-box'>
                        <tr>
                            <th class="afg-label"></th>
                            <th class="afg-input"></th>
                            <th class="afg-help-bubble"></th>
                        </tr>
                         <tr>
                           <td>Select Gallery to Edit</td>
                           <td><select id='afg_photo_gallery' name='afg_photo_gallery' onchange='loadGallerySettings()'>
                                 <?php echo afg_get_galleries($default_gallery_id) ?>
                           </select></td>
                           <tr>
                              <td>Gallery Name</td>
                              <td><input class='afg-input' maxlength='30' type='text' id='afg_edit_gallery_name' name='afg_edit_gallery_name' onblur='verifyEditBlank()' value="" />*</td>
                           </tr>
                           <tr>
                              <td>Gallery Description</td>
                              <td><input class='afg-input' maxlength='100' type='text' id='afg_edit_gallery_descr' name='afg_edit_gallery_descr' value="" /></td>
                           </tr>
                        </table>
<?php
    echo afg_generate_flickr_settings_table($photosets_map, $galleries_map, $groups_map);
    echo afg_generate_gallery_settings_table();
    $gals = get_option('afg_galleries');
    if (sizeof($gals) == 1) $disable_submit = True;
    else $disable_submit = False;
?>

                  <input type="submit" id="afg_save_changes" class="button-primary"
                  <?php if ($disable_submit) echo "disabled='yes'"; ?>
                  value="Save Changes" style="margin-top: 15px"/>
                  <br /><br />
                </div>
               <div id="afg-side-box">
                   <h3>Gallery Code</h3>
                        <table class='widefat fixed afg-side-box'>
                           <tr valign='top'>
                              <td>
                                 <p id='afg_flickr_gallery_code'>[AFG_gallery]</p>
                              </td>
                           </tr>
                        </table>
<?php
    echo afg_box('Usage Instructions', 'Insert the Gallery Code in any of your posts or pages to display your Flickr Gallery.');
    echo afg_donate_box();
    echo afg_share_box();
?>
               </div>
              </div>
            </form>
<?php
}
