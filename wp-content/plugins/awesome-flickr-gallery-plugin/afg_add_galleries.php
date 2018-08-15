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

    afg_get_sets_groups_galleries($photosets_map, $groups_map, $galleries_map, $user_id);
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
                $gallery['custom_size_square'] = isset($_POST['afg_custom_size_square'])?$_POST['afg_custom_size_square']:'false';
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

    $url=$_SERVER['REQUEST_URI'];
?>

            <form method='post' action='<?php echo $url ?>'>
               <div id="afg-wrap">
                   <?php echo afg_generate_version_line() ?>
                     <div id="afg-main-box">
                        <h3>Gallery Parameters</h3>
                        <table class='widefat afg-settings-box'>
                            <tr>
                                <th class="afg-label"></th>
                                <th class="afg-input"></th>
                                <th class="afg-help-bubble"></th>
                            </tr>
                           <tr>
                              <td>Gallery Name</td>
                              <td><input class='afg-input' maxlength='30' type='text' id='afg_add_gallery_name' name='afg_add_gallery_name' onblur='verifyBlank()' value='' />*</td>
                           </tr>
                           <tr>
                              <td>Gallery Description</td>
                              <td><input class='afg-input' maxlength='100' type='text' id='afg_add_gallery_descr' name='afg_add_gallery_descr' value="" /></td>
                           </tr>
                        </table>
<?php
    echo afg_generate_flickr_settings_table($photosets_map, $galleries_map, $groups_map);
    echo afg_generate_gallery_settings_table();
?>
                  <br />
                  <input type="submit" disabled='true' id="afg_save_changes" class="button-primary"
                  value="Add Gallery" />
              </div>


               <div id='afg-side-box'>
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
