<?php

define('BASE_URL', plugins_url() . '/' . basename(dirname(__FILE__)));
define('SITE_URL', site_url());
define('DEBUG', false);
define('VERSION', '3.5.6');

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
    'swipebox' => 'Swipebox (Touch Enabled)',
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

$afg_cache_refresh_interval_map = array(
	'6h' => '6 Hours',
	'12h' => '12 Hours',
	'1d' => '1 Day',
	'3d' => '3 Days',
	'1w' => '1 Week',
);

function afg_get_cache_refresh_interval_secs ($interval)
{
	if ($interval == '6h') {
		return 6 * 60 * 60;
	}
	else if ($interval == '12h') {
		return 12 * 60 * 60;
	}
	else if ($interval == '1d') {
		return 24 * 60 * 60;
	}
	else if ($interval == '3d') {
		return 3 * 24 * 60 * 60;
	}
	else if ($interval == '1w') {
		return 7 * 24 * 60 * 60;
	}
}

function afg_get_sets_groups_galleries (&$photosets_map, &$groups_map, &$galleries_map, $user_id) {
    global $pf;

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

    asort($photosets_map);
    asort($groups_map);
    asort($galleries_map);
}

function afg_get_cur_url() {
    $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
    $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
    $port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
    $url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["HTTP_HOST"].$port.$_SERVER["REQUEST_URI"];
    return $url;
}

function create_afgFlickr_obj() {
    global $pf;
    unset($_SESSION['afgFlickr_auth_token']);
    $pf = new afgFlickr(get_option('afg_api_key'), get_option('afg_api_secret')? get_option('afg_api_secret'): NULL);
    $pf->setToken(get_option('afg_flickr_token'));
}

function afg_error($error_msg) {
    return "<h3>Awesome Flickr Gallery Error - $error_msg</h3>";
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
    return "<div><iframe src=\"//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fawesome.flickr.gallery&amp;width=300&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=false&amp;header=false&amp;appId=107783615948615\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:300px; height:258px;\" allowTransparency=\"true\"></iframe><div>";
}

function afg_share_box() {
    return "<div>
        <h3>Follow Awesome Flickr Gallery</h3>"
        . afg_gplus_box()
        . afg_fb_like_box()
        . "</div>";
}

function afg_gplus_box() {
    return "<!-- Place this tag where you want the widget to render. -->
<div class=\"g-page\" data-href=\"//plus.google.com/u/0/110562610836727777499\" data-showtagline=\"true\" data-rel=\"publisher\"></div>

<!-- Place this tag after the last widget tag. -->
<script type=\"text/javascript\">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>";
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
    return "https://farm$farm.static.flickr.com/$server/{$pid}_$secret$size.jpg";
}

function afg_get_photo_page_url($pid, $uid) {
    return "https://www.flickr.com/photos/$uid/$pid";
}

function afg_generate_version_line() {
    if(isset($_POST['afg_dismis_ss_msg']) && $_POST['afg_dismis_ss_msg']) {
        update_option('afg_dismis_ss_msg', true);
    }

    $return_str = "" .
    "<div><h4 align=\"right\" style=\"margin-right:0.5%\">" .
       " &nbsp;Version: <b>" . VERSION . "</b> |" .
        " <a href=\"http://wordpress.org/extend/plugins/awesome-flickr-gallery-plugin/faq/\">FAQ</a> |" .
        " <a href=\"http://wordpress.org/extend/plugins/awesome-flickr-gallery-plugin/\">Rate this plugin</a> |" .
        " <a href=\"http://www.ronakg.com/discussions/\">Support Forums</a> |" .
        " <a href=\"https://github.com/ronakg/Awesome-Flickr-Gallery/wiki/Changelog\">Changelog</a> |" .
        " <a href=\"http://www.ronakg.com/photos/\">Live Demo</a>" .
    " </h4></div>";
    return $return_str;
}

function afg_generate_flickr_settings_table($photosets, $galleries, $groups) {
    global $afg_photo_source_map;
    $photosets = afg_generate_options($photosets, '', False);
    $galleries = afg_generate_options($galleries, '', False);
    $groups = afg_generate_options($groups, '', False);
    return "
    <h3>Flickr Settings</h3>
    <table class='widefat afg-settings-box'>
        <tr>
            <th class='afg-label'></th>
            <th class='afg-input'></th>
            <th class='afg-help-bubble'></th>
        </tr>
        <tr>
        <td>Gallery Source</td>
        <td><select name='afg_photo_source_type' id='afg_photo_source_type' onchange='getPhotoSourceType()' >" . afg_generate_options($afg_photo_source_map, 'photostream', False) . "
        </select></td>
        </tr>
        <tr>
        <td id='afg_photo_source_label'></td>
        <td><select style='display:none' name='afg_photosets_box' id='afg_photosets_box'>$photosets
        </select>
        <select style='display:none' name='afg_galleries_box' id='afg_galleries_box'>$galleries
        </select>
        <select style='display:none' name='afg_groups_box' id='afg_groups_box'>$groups
        </select>
        <textarea rows='3' cols='30' name='afg_tags' id='afg_tags' style='display:none'></textarea>
        </td>
        <td id='afg_source_help' class='afg-help-bubble' style='display:none'>Enter tags separated by comma. For example: <b>tag1, tag2, tag3, tag4</b><br />Photos matching any of the given tags will be displayed.</td>
        </tr>
    </table>";
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
        <h3>Gallery Settings</h3>
        <table class='widefat fixed afg-settings-box'>
            <tr>
                <th class='afg-label'></th>
                <th class='afg-input'></th>
                <th class='afg-help-bubble'></th>
            </tr>
        <tr>
        <td>Max Photos Per Page</td>
        <td><div  style='display:inline; margin-right:10px'><input type='checkbox' name='afg_per_page_check' id='afg_per_page_check' onclick='showHidePerPage()' value='default' checked=''> Default </input></div><div  class='afg-small-input' style='display:inline-block'><input name='afg_per_page' disabled='true' id='afg_per_page' type='text' maxlength='3' onblur='verifyBlank()' value='10'/></div>
        </td>
        </tr>

        <tr>
        <td>Sort order of Photos</td>
        <td><select name='afg_sort_order' id='afg_sort_order'>"
        . afg_generate_options($afg_sort_order_map, 'default', True, $afg_sort_order_map[get_option('afg_sort_order')]) . "
    </select></td>
            <td class='afg-help'>Set the sort order of the photos as per your liking and forget about how photos are arranged on Flickr.</td>
            </tr>

        <tr>
        <td>Size of Photos</td>
        <td><select name='afg_photo_size' id='afg_photo_size' onchange='customPhotoSize()'>
            " . afg_generate_options($afg_photo_size_map, 'default', True, $photo_size) . "
        </select></td>
        </tr>

        <tr id='afg_custom_size_block' style='display:none'>
        <td>Custom Width</td>
        <td><input type='text' maxlength='3' name='afg_custom_size' id='afg_custom_size' onblur='verifyCustomSizeBlank()' value='100'>* (in px)
        &nbsp;Square? <input type='checkbox' id='afg_custom_size_square' name='afg_custom_size_square' value='true'>
        </td>
        <td class='afg-help'>Fill in the exact width for the photos (min 50, max 500).  Height of the photos will be adjusted
        accordingly to maintain aspect ratio of the photo. Enable <b>Square</b> to crop
        the photo to a square aspect ratio.<br />Warning: Custom photo sizes may not work with your webhost, please use built-in sizes, it's more reliable and faster too.</td>
        </tr>

        <tr>
        <td>Photo Titles</td>
        <td><select name='afg_captions' id='afg_captions'>
            " . afg_generate_options($afg_on_off_map, 'default', True, $afg_on_off_map[get_option('afg_captions')]) . "
        </select></td>
        <td class='afg-help'>Photo Title setting applies only to Thumbnail (and above) size photos.</td>
        </tr>

        <tr>
        <td>Photo Descriptions</td>
        <td><select name='afg_descr' id='afg_descr'>
            " . afg_generate_options($afg_descr_map, 'default', True, $afg_descr_map[get_option('afg_descr')]) . "
        </select></td>
        <td class='afg-help'>Photo Description setting applies only to Small and Medium size photos.</td>
        </tr>

        <tr>
        <td>Number of Columns</td>
        <td><select name='afg_columns' id='afg_columns'>
            " . afg_generate_options($afg_columns_map, 'default', True, $afg_columns_map[get_option('afg_columns')]) . "
        </select></td>
        </tr>

        <tr>
        <td>Slideshow Behavior</td>
        <td><select name='afg_slideshow_option' id='afg_slideshow_option'>
        " . afg_generate_options($afg_slideshow_map, 'default', True, $afg_slideshow_map[get_option('afg_slideshow_option')]) . "
    </select></td>
            </tr>

        <tr>
        <td>Background Color</td>
        <td><select name='afg_bg_color' id='afg_bg_color'>
            " . afg_generate_options($afg_bg_color_map, 'default', True, $afg_bg_color_map[get_option('afg_bg_color')]) . "
        </select></td>
        </tr>

        <tr>
        <td>Gallery Width</td>
        <td><select name='afg_width' id='afg_width'>
        " . afg_generate_options($afg_width_map, 'default', True, $afg_width_map[get_option('afg_width')]) . "
        </select></td>
        <td class='afg-help'>Width of the Gallery is relative to the width of the page where Gallery is being generated.  <i>Automatic</i> is 100% of page width.</td>
        </tr>

        <tr>
        <td>Disable Pagination?</td>
        <td><select name='afg_pagination' id='afg_pagination'>
        " . afg_generate_options($afg_yes_no_map, 'default', True, $afg_yes_no_map[get_option('afg_pagination')]) . "
        </select></td>
        <td class='afg-help'>Useful when displaying gallery in a sidebar widget where you want only few recent photos.</td>
        </tr>

        <tr>
        <td>Add a Small Credit Note?</td>
        <td><select name='afg_credit_note' id='afg_credit_note'>
             " . afg_generate_options($afg_on_off_map, 'default', True, $afg_on_off_map[get_option('afg_credit_note')]) . "
             </select></td>
        <td class='afg-help'>Credit Note will appear at the bottom of the gallery as - </font>
            Powered by
            <a href=\"http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin\"/>
            AFG</a></td>
        </tr>
    </table>";
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
        <table class='widefat fixed afg-side-box'>
        <h3>$title</h3>
        <tr><td>$message</td></tr>
        </table>
        ";
}

function afg_usage_box($code) {
    return "<table class='fixed widefat afg-side-box'>
        <h3>Usage Instructions</h3>
        <tr><td>Just insert $code in any of the posts or page to display your Flickr gallery.</td></tr>
        </table>";
}

function get_afg_option($gallery, $var) {
    if (isset($gallery[$var]) && $gallery[$var]) return $gallery[$var];
    else return get_option('afg_' . $var);
}

function afg_donate_box() {
    return "
        <h3>Support this plugin</h3>
        <table class='widefat fixed afg-side-box'>
        <td>It takes time and effort to keep releasing new versions of this plugin.  If you like it, consider donating a few bucks <b>(especially if you are using this plugin on a commercial website)</b> to keep receiving new features.
        </form>

        <br />
        <div style=\"text-align:center; margin-top:15px\"><form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_blank\">
<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
<input type=\"hidden\" name=\"encrypted\" value=\"-----BEGIN PKCS7-----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-----END PKCS7-----
\">
<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">
<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">
</div></form></div>
        </td>
        </table>";
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
