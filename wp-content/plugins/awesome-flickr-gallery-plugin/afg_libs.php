<?php

define('BASE_URL', plugins_url() . '/' . basename(dirname(__FILE__)));
define('SITE_URL', get_option('siteurl'));
define('DEBUG', false);
define('VERSION', '3.3.4');

$afg_sort_order_map = array(
    'default' => 'Default',
    'flickr' => 'As per Flickr',
    'date_taken_cmp_newest' => 'By date taken (Newest first)',
    'date_taken_cmp_oldest' => 'By date taken (Oldest first)',
    'date_upload_cmp_newest' => 'By date uploaded (Newest first)',
    'date_upload_cmp_oldest' => 'By date uploaded (Oldest first)',
    'random' => 'Random',
);

$afg_slideshow_map = array(
    'default' => 'Default',
    'colorbox' => 'Colorbox',
    'disable' => 'No Slideshow',
    'flickr' => 'Link to Flickr Photo page',
    'none' => 'No Slideshow and No Link',
);

/* Map for photo titles displayed on the gallery. */
$size_heading_map = array(
    '_s' => '',
    '_t' => '0.9em',
    '_m' => '1em',
    'NULL' => '1.2em',
);

$afg_photo_source_map = array(
    'photostream' => 'Photostream',
    'gallery' => 'Gallery',
    'photoset' => 'Photoset',
    'group' => 'Group',
    'tags' => 'Tags',
    'popular' => 'My Popular Photos',
);

$afg_width_map = array(
    'default' => 'Default',
    'auto' => 'Automatic',
    '10' => '10 %',
    '20' => '20 %',
    '30' => '30 %',
    '40' => '40 %',
    '50' => '50 %',
    '60' => '60 %',
    '70' => '70 %',
    '80' => '80 %',
    '90' => '90 %',
);

$afg_photo_size_map = array(
    'default' => 'Default',
    '_s' => 'Square (Max 75px)',
    '_t' => 'Thumbnail (Max 100px)',
    '_m' => 'Small (Max 240px)',
    'NULL' => 'Medium (Max 500px)',
    'custom' => 'Custom',
);

$afg_on_off_map = array(
    'off' => 'Off  ',
    'on' => 'On  ',
    'default' => 'Default',
);

$afg_yes_no_map = array(
    'off' => 'Yes  ',
    'on' => 'No  ',
    'default' => 'Default',
);

$afg_descr_map = array(
    'off' => 'Off',
    'on' => 'On',
    'default' => 'Default',
);

$afg_columns_map = array(
    'default' => 'Default',
    '1' => '1  ',
    '2' => '2  ',
    '3' => '3  ',
    '4' => '4  ',
    '5' => '5  ',
    '6' => '6  ',
    '7' => '7  ',
    '8' => '8  ',
    '9' => '9  ',
    '10' => '10 ',
    '11' => '11 ',
    '12' => '12 ',
);

$afg_bg_color_map = array(
    'default' => 'Default',
    'Black' => 'Black',
    'White' => 'White',
    'Transparent' => 'Transparent',
);

$afg_text_color_map = array(
    'Black' => 'White',
    'White' => 'Black',
);

function create_afgFlickr_obj() {
    global $pf;
    unset($_SESSION['afgFlickr_auth_token']);
    $pf = new afgFlickr(get_option('afg_api_key'), get_option('afg_api_secret')? get_option('afg_api_secret'): NULL);
    $pf->setToken(get_option('afg_flickr_token'));
}

function afg_error() {
    global $pf;
    return "<h3>Awesome Flickr Gallery Error - $pf->error_msg</h3>";
}

function date_taken_cmp_newest($a, $b) {
    return $a['datetaken'] < $b['datetaken'];
}

function date_taken_cmp_oldest($a, $b) {
    return $a['datetaken'] > $b['datetaken'];
}

function date_upload_cmp_newest($a, $b) {
    return $a['dateupload'] < $b['dateupload'];
}

function date_upload_cmp_oldest($a, $b) {
    return $a['dateupload'] > $b['dateupload'];
}

function afg_fb_like_box() {
    return "<iframe src=\"//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fawesome.flickr.gallery&amp;width=300&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=false&amp;header=false&amp;appId=107783615948615\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:300px; height:170px;\" allowTransparency=\"true\"></iframe>";
}

function afg_share_box() {
    return "<div id=\"poststuff\">
        <div class=\"postbox\" style='width:300px; box-shadow:0 0 2px'>
        <h3>Follow Awesome Flickr Gallery</h3>"
        . afg_fb_like_box()
        . afg_gplus_box()
        . "</div></div>";
}

function afg_gplus_box() {
    return "<div class=\"g-plus\" data-href=\"https://plus.google.com/110562610836727777499\" data-size=\"badge\"></div>";
}

function delete_afg_caches() {
    $galleries = get_option('afg_galleries');
    foreach($galleries as $id => $ginfo) {
        delete_transient('afg_id_'. $id);
    }
}
function afg_get_photo_url($farm, $server, $pid, $secret, $size) {
    if ($size == 'NULL') {
        $size = '';
    }
    return "http://farm$farm.static.flickr.com/$server/{$pid}_$secret$size.jpg";
}

function afg_get_photo_page_url($pid, $uid) {
    return "http://www.flickr.com/photos/$uid/$pid";
}

function afg_generate_version_line() {
    if(isset($_POST['afg_dismis_ss_msg']) && $_POST['afg_dismis_ss_msg']) {
        update_option('afg_dismis_ss_msg', true);
    }

    $return_str = "" .
    " <h4 align=\"right\" style=\"margin-right:0.5%\">" .
       " &nbsp;Version: <b>" . VERSION . "</b> |" .
        " <a href=\"http://wordpress.org/extend/plugins/awesome-flickr-gallery-plugin/faq/\">FAQ</a> |" .
        " <a href=\"http://wordpress.org/extend/plugins/awesome-flickr-gallery-plugin/\">Rate this plugin</a> |" .
        " <a href=\"http://www.ronakg.com/discussions/\">Support Forums</a> |" .
        " <a href=\"http://wordpress.org/extend/plugins/awesome-flickr-gallery-plugin/changelog/\">Changelog</a> |" .
        " <a href=\"http://www.ronakg.com/photos/\">Live Demo</a>" .
    " </h4>";
    return $return_str;
}

function afg_generate_flickr_settings_table($photosets, $galleries, $groups) {
    global $afg_photo_source_map;
    $photosets = afg_generate_options($photosets, '', False);
    $galleries = afg_generate_options($galleries, '', False);
    $groups = afg_generate_options($groups, '', False);
    return "
    <div id=\"poststuff\">
<div class=\"postbox\" style='box-shadow:0 0 2px'>
    <h3>Flickr Settings</h3>
    <table class='form-table'>
        <tr valign='top'>
        <th scope='row'>Gallery Source</th>
        <td><select name='afg_photo_source_type' id='afg_photo_source_type' onchange='getPhotoSourceType()' >" . afg_generate_options($afg_photo_source_map, 'photostream', False) . "
        </select></td>
        </tr>
        <tr>
        <th id='afg_photo_source_label'></th>
        <td><select style='display:none' name='afg_photosets_box' id='afg_photosets_box'>$photosets
        </select>
        <select style='display:none' name='afg_galleries_box' id='afg_galleries_box'>$galleries
        </select>
        <select style='display:none' name='afg_groups_box' id='afg_groups_box'>$groups
        </select>
        <textarea rows='3' cols='30' name='afg_tags' id='afg_tags' style='display:none'></textarea>
        </td>
        <td id='afg_source_help' style='display:none'><font size='2'>Enter tags separated by comma. For example: <b>tag1, tag2, tag3, tag4</b><br />Photos matching any of the given tags will be displayed.</font></td>
        </tr>
    </table>
</div></div>";
}

function afg_generate_gallery_settings_table() {
    global $afg_photo_size_map, $afg_on_off_map, $afg_descr_map, 
        $afg_columns_map, $afg_bg_color_map, $afg_photo_source_map, 
        $afg_width_map, $afg_yes_no_map, $afg_sort_order_map, $afg_slideshow_map;
    
    if (get_option('afg_photo_size') == 'custom')
        $photo_size = '(Custom - ' . get_option('afg_custom_size') . 'px' . ((get_option('afg_custom_size_square') == 'true')? ' - Square)': ')');
    else
        $photo_size = $afg_photo_size_map[get_option('afg_photo_size')];

    return "
    <div id=\"poststuff\">
        <div class=\"postbox\" style='box-shadow:0 0 2px'>
        <h3>Gallery Settings</h3>
        <table class='form-table'>

        <tr valign='top'>
        <th scope='row'>Max Photos Per Page</th>
        <td style='width:28%'><input type='checkbox' name='afg_per_page_check' id='afg_per_page_check' onclick='showHidePerPage()' value='default' checked='' style='vertical-align:top'> Default </input><input name='afg_per_page' disabled='true' id='afg_per_page' type='text' size='3' maxlength='3' onblur='verifyBlank()' value='10'/> 
        </td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Sort order of Photos</th>
        <td><select name='afg_sort_order' id='afg_sort_order'>"
        . afg_generate_options($afg_sort_order_map, 'default', True, $afg_sort_order_map[get_option('afg_sort_order')]) . "
    </select>
            <td><font size='2'>Set the sort order of the photos as per your liking and forget about how photos are arranged on Flickr.</font></td>
            </td>
            </tr>

        <tr valign='top'>
        <th scope='row'>Size of Photos</th>
        <td><select name='afg_photo_size' id='afg_photo_size' onchange='customPhotoSize()'>
            " . afg_generate_options($afg_photo_size_map, 'default', True, $photo_size) . "
        </select></td>
        </tr>
        
        <tr valign='top' id='afg_custom_size_block' style='display:none'>
        <th>Custom Width</th>
        <td><input type='text' size='3' maxlength='3' name='afg_custom_size' id='afg_custom_size' onblur='verifyCustomSizeBlank()' value='100'><font color='red'>*</font> (in px)
        &nbsp;Square? <input type='checkbox' id='afg_custom_size_square' name='afg_custom_size_square' value='true'>
        </td>
        <td><font size='2'>Fill in the exact width for the photos (min 50, max 500).  Height of the photos will be adjusted
        accordingly to maintain aspect ratio of the photo. Enable <b>Square</b> to crop
        the photo to a square aspect ratio.</td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Photo Titles</th>
        <td><select name='afg_captions' id='afg_captions'>
            " . afg_generate_options($afg_on_off_map, 'default', True, $afg_on_off_map[get_option('afg_captions')]) . "
        </select></td>
        <td><font size='2'>Photo Title setting applies only to Thumbnail (and above) size photos.</font></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Photo Descriptions</th>
        <td><select name='afg_descr' id='afg_descr'>
            " . afg_generate_options($afg_descr_map, 'default', True, $afg_descr_map[get_option('afg_descr')]) . "
        </select></td>
        <td><font size='2'>Photo Description setting applies only to Small and Medium size photos.</td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Number of Columns</th>
        <td><select name='afg_columns' id='afg_columns'>
            " . afg_generate_options($afg_columns_map, 'default', True, $afg_columns_map[get_option('afg_columns')]) . "
        </select></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Slideshow Behavior</th>
        <td><select name='afg_slideshow_option' id='afg_slideshow_option'>
        " . afg_generate_options($afg_slideshow_map, 'default', True, $afg_slideshow_map[get_option('afg_slideshow_option')]) . "
    </select></td>
            <td><font size='2'></font></td>
            </tr>

        <tr valign='top'>
        <th scope='row'>Background Color</th>
        <td><select name='afg_bg_color' id='afg_bg_color'>
            " . afg_generate_options($afg_bg_color_map, 'default', True, $afg_bg_color_map[get_option('afg_bg_color')]) . "
        </select></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Gallery Width</th>
        <td><select name='afg_width' id='afg_width'>
        " . afg_generate_options($afg_width_map, 'default', True, $afg_width_map[get_option('afg_width')]) . "
        </select></td>
        <td><font size='2'>Width of the Gallery is relative to the width of the page where Gallery is being generated.  <i>Automatic</i> is 100% of page width.</font></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Disable Pagination?</th>
        <td><select name='afg_pagination' id='afg_pagination'>
        " . afg_generate_options($afg_yes_no_map, 'default', True, $afg_yes_no_map[get_option('afg_pagination')]) . "
        </select></td>
        <td><font size='2'>Useful when displaying gallery in a sidebar widget where you want only few recent photos.</td>
        </tr>

        <tr valign='top'>
        <th scope='row'>Add a Small Credit Note?</th>
        <td><select name='afg_credit_note' id='afg_credit_note'>
             " . afg_generate_options($afg_on_off_map, 'default', True, $afg_on_off_map[get_option('afg_credit_note')]) . "
             </select></td>
        <td><font size='2'>Credit Note will appear at the bottom of the gallery as - </font>
            Powered by
            <a href=\"http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin\"/>
            AFG</a></td>
        </tr>
    </table>
</div></div>";
}

function afg_generate_options($params, $selection, $show_default=False, $default_value=0) {
    $str = '';
    foreach($params as $key => $value) {
        if ($key == 'default' && !$show_default)
            continue;

        if ($selection == $key) {
            if ($selection == 'default') $value .= ' - ' . $default_value;
            $str .= "<option value=" . $key . " selected='selected'>" . $value . "</option>";
        }
        else
            $str .= "<option value=" . $key . ">" . $value . "</option>";
    }
    return $str;
}

function afg_filter($param) {
    if ($param == 'default') return "";
    else return $param;
}

function afg_box($title, $message) {
     return "
        <div id=\"poststuff\">
        <div class=\"postbox\" style='box-shadow:0 0 2px'>
        <h3>$title</h3>
        <table class='form-table'>
        <td>$message</td>
        </table>
        </div></div>
        ";
}

function afg_usage_box($code) {
    return "
        <div id=\"poststuff\">
        <div class=\"postbox\" style='box-shadow:0 0 2px'>
        <h3>Usage Instructions</h3>
        <table class='form-table'>
        <td>Just insert $code in any of the posts or page to display your Flickr gallery.</td>
        </table>
        </div></div>
        ";
}

function get_afg_option($gallery, $var) {
    if (isset($gallery[$var]) && $gallery[$var]) return $gallery[$var];
    else return get_option('afg_' . $var);
}

function afg_donate_box() {
    return "
        <div id=\"poststuff\">
        <div class=\"postbox\" style='box-shadow:0 0 2px'>
        <h3>Support this plugin</h3>
        <table class='form-table'>
        <td>It takes time and effort to keep releasing new versions of this plugin.  If you like it, consider donating a few bucks <b>(especially if you are using this plugin on a commercial website)</b> to keep receiving new features.
        </form><form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\"><div style=\"text-align:center\" class=\"paypal-donations\"><input type='hidden' name='item_name' value='Awesome Flickr Gallery'/><input type=\"hidden\" name=\"cmd\" value=\"_donations\" /><input type=\"hidden\" name=\"business\" value=\"2P32M6V34HDCQ\" /><input type=\"hidden\" name=\"currency_code\" value=\"USD\" /><input type=\"image\" src=\"" . BASE_URL . "/images/donate_small.png\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online.\" /><img alt=\"PayPal Donate\" src=\"https://www.paypal.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\" /><br /><b>All major credit cards are accepted too.</b></div></form>
        </td>
        </table>
        </div></div>";
}

function afg_reference_box() {
    $message = "Max Photos Per Page - <b>" . get_option('afg_per_page') . "</b>";
    $size = get_option('afg_photo_size');
    if ($size == '_s') $size = 'Square';
    else if ($size == '_t') $size = 'Thumbnail';
    else if ($size == '_m') $size = 'Small';
    else if ($size == 'NULL') $size = 'Medium';
    $message .= "<br />Size of Photos - <b>" . $size . "</b>";
    $message .= "<br />Photo Titles - <b>" . get_option('afg_captions') . "</b>";
    $message .= "<br />Photo Descriptions - <b>" . get_option('afg_descr') . "</b>";
    $message .= "<br />No of Columns - <b>" . get_option('afg_columns') . "</b>";
    $message .= "<br />Background Color - <b>" . get_option('afg_bg_color') . "</b>";
    $message .= "<br />Gallery Width - <b>" . ((get_option('afg_width') == 'auto')?"Automatic":get_option('afg_width') . "%") . "</b>";
    $message .= "<br />Pagination - <b>" . get_option('afg_pagination') . "</b>";
    $message .= "<br />Credit Note - <b>" . get_option('afg_credit_note') . "</b>";
    return afg_box('Default Settings for Reference', $message);
}

?>
