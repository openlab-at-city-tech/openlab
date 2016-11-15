<?php
/*
   Plugin Name: Awesome Flickr Gallery
   Plugin URI: http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/
   Description: Awesome Flickr Gallery is a simple, fast and light plugin to create a gallery of your Flickr photos on your WordPress enabled website.  This plugin aims at providing a simple yet customizable way to create stunning Flickr gallery.
   Version: 3.5.5
   Author: Ronak Gandhi
   Author URI: http://www.ronakg.com
   License: GPL2

   Copyright 2014 Ronak Gandhi (email : ronak.gandhi@ronakg.com)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2, as
   published by the Free Software Foundation.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once('afgFlickr/afgFlickr.php');
include_once('afg_admin_settings.php');
include_once('afg_libs.php');

function afg_enqueue_cbox_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('afg_colorbox_script', BASE_URL . "/colorbox/jquery.colorbox-min.js" , array('jquery'));
    wp_enqueue_script('afg_colorbox_js', BASE_URL . "/colorbox/mycolorbox.js" , array('jquery'));
}

function afg_enqueue_swipebox_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('afg_swipebox_script', BASE_URL . "/swipebox/js/jquery.swipebox.min.js" , array('jquery'));
    wp_enqueue_script('afg_swipebox_js', BASE_URL . "/swipebox/myswipebox.js" , array('jquery'));
}

function afg_enqueue_cbox_styles() {
    wp_enqueue_style('afg_colorbox_css', BASE_URL . "/colorbox/colorbox.css");
}

function afg_enqueue_swipebox_styles() {
    wp_enqueue_style('afg_swipebox_css', BASE_URL . "/swipebox/css/swipebox.min.css");
}

function afg_enqueue_styles() {
    wp_enqueue_style('afg_css', BASE_URL . "/afg.css");
}

$enable_colorbox = get_option('afg_slideshow_option') == 'colorbox';
$enable_swipebox = get_option('afg_slideshow_option') == 'swipebox';

if (!is_admin()) {
    global $enable_colorbox, $enable_swipebox;
    /* Short code to load Awesome Flickr Gallery plugin.  Detects the word
     * [AFG_gallery] in posts or pages and loads the gallery.
     */
    add_shortcode('AFG_gallery', 'afg_display_gallery');
    add_filter('widget_text', 'do_shortcode', 11);

    $galleries = get_option('afg_galleries');
    foreach ($galleries as $gallery) {
        if ($gallery['slideshow_option'] == 'colorbox') {
            $enable_colorbox = true;
            break;
        }

        if ($gallery['slideshow_option'] == 'swipebox') {
            $enable_swipebox = true;
            break;
        }
    }

    if ($enable_colorbox) {
        add_action('wp_print_scripts', 'afg_enqueue_cbox_scripts');
        add_action('wp_print_styles', 'afg_enqueue_cbox_styles');
    }

    if ($enable_swipebox) {
        add_action('wp_print_scripts', 'afg_enqueue_swipebox_scripts');
        add_action('wp_print_styles', 'afg_enqueue_swipebox_styles');
    }

    add_action('wp_print_styles', 'afg_enqueue_styles');
}

add_action('wp_head', 'add_afg_headers');

function add_afg_headers() {
    echo "<style type=\"text/css\">" . get_option('afg_custom_css') . "</style>";
}

function afg_return_error_code($rsp) {
    return $rsp['message'];
}

/* Main function that loads the gallery. */
function afg_display_gallery($atts) {
    global $size_heading_map, $afg_text_color_map, $pf;

    if (!get_option('afg_pagination')) update_option('afg_pagination', 'on');

    extract( shortcode_atts( array(
        'id' => '0',
    ), $atts ) );

    $cur_page = 1;
    $cur_page_url = afg_get_cur_url();

    preg_match("/afg{$id}_page_id=(?P<page_id>\d+)/", $cur_page_url, $matches);

    if ($matches) {
        $cur_page = ($matches['page_id']);
        $match_pos = strpos($cur_page_url, "afg{$id}_page_id=$cur_page") - 1;
        $cur_page_url = substr($cur_page_url, 0, $match_pos);
        if(function_exists('qtrans_convertURL')) {
            $cur_page_url = qtrans_convertURL($cur_page_url);
        }
    }

    if (strpos($cur_page_url,'?') === false) $url_separator = '?';
    else $url_separator = '&';

    $galleries = get_option('afg_galleries');
    if (!isset($galleries) || array_key_exists($id, $galleries) == false) {
        return afg_error("Gallery ID {$id} has been either deleted or not configured.");
    }

    $gallery = $galleries[$id];

    $api_key = get_option('afg_api_key');
    $user_id = get_option('afg_user_id');
    $disable_slideshow = (get_afg_option($gallery, 'slideshow_option') == 'disable');
    $slideshow_option = get_afg_option($gallery, 'slideshow_option');

    $per_page = get_afg_option($gallery, 'per_page');
    $sort_order = get_afg_option($gallery, 'sort_order');
    $photo_size = get_afg_option($gallery, 'photo_size');
    $photo_title = get_afg_option($gallery, 'captions');
    $photo_descr = get_afg_option($gallery, 'descr');
    $bg_color = get_afg_option($gallery, 'bg_color');
    $columns = get_afg_option($gallery, 'columns');
    $credit_note = get_afg_option($gallery, 'credit_note');
    $gallery_width = get_afg_option($gallery, 'width');
    $pagination = get_afg_option($gallery, 'pagination');
    $cache_refresh_interval = get_afg_option($gallery, 'cache_refresh_interval');

    if ($photo_size == 'custom') {
        $custom_size = get_afg_option($gallery, 'custom_size');
        $custom_size_square = get_afg_option($gallery, 'custom_size_square');

        if ($custom_size <= 70) $photo_size = '_s';
        else if ($custom_size <= 90) $photo_size = '_t';
        else if ($custom_size <= 220) $photo_size = '_m';
        else if ($custom_size <= 500) $photo_size = 'NULL';
    }
    else {
        $custom_size = 0;
        $custom_size_square = 'false';
    }

    $photoset_id = NULL;
    $gallery_id = NULL;
    $group_id = NULL;
    $tags = NULL;
    $popular = false;

    if (!isset($gallery['photo_source'])) $gallery['photo_source'] = 'photostream';

    if ($gallery['photo_source'] == 'photoset') $photoset_id = $gallery['photoset_id'];
    else if ($gallery['photo_source'] == 'gallery') $gallery_id = $gallery['gallery_id'];
    else if ($gallery['photo_source'] == 'group') $group_id = $gallery['group_id'];
    else if ($gallery['photo_source'] == 'tags') $tags = $gallery['tags'];
    else if ($gallery['photo_source'] == 'popular') $popular = true;

    $disp_gallery = "<!-- Awesome Flickr Gallery Start -->";
    $disp_gallery .= "<!--" .
        " - Version - " . VERSION .
        " - User ID - " . $user_id .
        " - Photoset ID - " . (isset($photoset_id)? $photoset_id: '') .
        " - Gallery ID - " . (isset($gallery_id)? $gallery_id: '') .
        " - Group ID - " . (isset($group_id)? $group_id: '') .
        " - Tags - " . (isset($tags)? $tags: '') .
        " - Popular - " . (isset($popular)? $popular: '') .
        " - Per Page - " . $per_page .
        " - Sort Order - " . $sort_order .
        " - Photo Size - " . $photo_size .
        " - Custom Size - " . $custom_size .
        " - Square - " . $custom_size_square .
        " - Captions - " . $photo_title .
        " - Description - " . $photo_descr .
        " - Columns - " . $columns .
        " - Credit Note - " . $credit_note .
        " - Background Color - " . $bg_color .
        " - Width - " . $gallery_width .
        " - Pagination - " . $pagination .
        " - Slideshow - " . $slideshow_option .
        " - Disable slideshow? - " . $disable_slideshow .
        "-->";

    $extras = 'url_l, description, date_upload, date_taken, owner_name';

    if (!DEBUG) {
	    $photos = get_transient('afg_id_' . $id);
    }

    if ($photos === false) {
        $photos = array();

        if (isset($photoset_id) && $photoset_id) {
            $rsp_obj = $pf->photosets_getInfo($photoset_id);
            if ($pf->error_code) return afg_error($pf->error_msg);
            $total_photos = $rsp_obj['photos'];
        }
        else if (isset($gallery_id) && $gallery_id) {
            $rsp_obj = $pf->galleries_getInfo($gallery_id);
            if ($pf->error_code) return afg_error($pf->error_msg);
            $total_photos = $rsp_obj['gallery']['count_photos']['_content'];
        }
        else if (isset($group_id) && $group_id) {
            $rsp_obj = $pf->groups_pools_getPhotos($group_id, NULL, NULL, NULL, NULL, 1, 1);
            if ($pf->error_code) return afg_error($pf->error_msg);
            $total_photos = $rsp_obj['photos']['total'];
            if ($total_photos > 500) $total_photos = 500;
            }
        else if (isset($tags) && $tags) {
            $rsp_obj = $pf->photos_search(array('user_id'=>$user_id, 'tags'=>$tags, 'extras'=>$extras, 'per_page'=>1));
            if ($pf->error_code) return afg_error($pf->error_msg);
            $total_photos = $rsp_obj['photos']['total'];
        }
        else if (isset($popular) && $popular) {
            $rsp_obj = $pf->photos_search(array('user_id'=>$user_id, 'sort'=>'interestingness-desc', 'extras'=>$extras, 'per_page'=>1));
            if ($pf->error_code) return afg_error($pf->error_msg);
            $total_photos = $rsp_obj['photos']['total'];
            if ($total_photos > 500) $total_photos = 500;
        }
        else {
            $rsp_obj = $pf->people_getInfo($user_id);
            if ($pf->error_code) return afg_error($pf->error_msg);
            $total_photos = $rsp_obj['photos']['count']['_content'];
        }

        for($i=1; $i<($total_photos/500)+1; $i++) {
            $flickr_api = 'photos';
            if ($photoset_id) {
                $flickr_api = 'photoset';
                $rsp_obj_total = $pf->photosets_getPhotos($photoset_id, $extras, NULL, 500, $i);
                if ($pf->error_code) return afg_error($pf->error_msg);
            }
            else if ($gallery_id) {
                $rsp_obj_total = $pf->galleries_getPhotos($gallery_id, $extras, 500, $i);
                if ($pf->error_code) return afg_error($pf->error_msg);
            }
            else if ($group_id) {
                $rsp_obj_total = $pf->groups_pools_getPhotos($group_id, NULL, NULL, NULL, $extras, 500, $i);
                if ($pf->error_code) return afg_error($pf->error_msg);
            }
            else if ($tags) {
                $rsp_obj_total = $pf->photos_search(array('user_id'=>$user_id, 'tags'=>$tags, 'extras'=>$extras, 'per_page'=>500, 'page'=>$i));
                if ($pf->error_code) return afg_error($pf->error_msg);
            }
            else if ($popular) {
                $rsp_obj_total = $pf->photos_search(array('user_id'=>$user_id, 'sort'=>'interestingness-desc', 'extras'=>$extras, 'per_page'=>500, 'page'=>$i));
                if ($pf->error_code) return afg_error($pf->error_msg);
            }
            else {
                if (get_option('afg_flickr_token')) $rsp_obj_total = $pf->people_getPhotos($user_id, array('extras' => $extras, 'per_page' => 500, 'page' => $i));
                else $rsp_obj_total = $pf->people_getPublicPhotos($user_id, NULL, $extras, 500, $i);
                if ($pf->error_code) return afg_error($pf->error_msg);
            }
            $photos = array_merge($photos, $rsp_obj_total[$flickr_api]['photo']);
        }
        if (!DEBUG)
            set_transient('afg_id_' . $id, $photos, afg_get_cache_refresh_interval_secs($cache_refresh_interval));
    }
    else {
        $total_photos = count($photos);
    }

    if (($total_photos % $per_page) == 0) {
        $total_pages = (int)($total_photos / $per_page);
    }
    else {
        $total_pages = (int)($total_photos / $per_page) + 1;
    }

    if ($gallery_width == 'auto') $gallery_width = 100;
    $text_color = isset($afg_text_color_map[$bg_color])? $afg_text_color_map[$bg_color]: '';
    $disp_gallery .= "<div class='afg-gallery custom-gallery-{$id}' id='afg-{$id}' style='background-color:{$bg_color}; width:$gallery_width%; color:{$text_color}; border-color:{$bg_color};'>";

    $disp_gallery .= "<div class='afg-table' style='width:100%'>";

    $photo_count = 1;
    $column_width = (int)($gallery_width/$columns);

    if (!$popular && $sort_order != 'flickr') {
        if ($sort_order == 'random')
            shuffle($photos);
        else
            usort($photos, $sort_order);
    }

    if ($disable_slideshow) {
        $class = '';
        $rel = '';
        $click_event = '';
    }
    else {
        if ($slideshow_option == 'colorbox') {
            $class = "class='afgcolorbox'";
            $rel = "rel='example4{$id}'";
            $click_event = "";
        }
        else if ($slideshow_option == 'swipebox') {
            $class = "class='swipebox'";
            $rel = '';
            $click_event = "";
        }
        else if ($slideshow_option == 'flickr') {
            $class = "";
            $rel = "";
            $click_event = "target='_blank'";
        }
    }

    if ($photo_size == '_s') {
        $photo_width = "width='75'";
        $photo_height = "height='75'";
    }
    else {
        $photo_width = '';
        $photo_height = '';
    }

    $cur_col = 0;
    foreach($photos as $pid => $photo) {
        $p_title = esc_attr($photo['title']);
        $p_description = esc_attr($photo['description']['_content']);

        $p_description = preg_replace("/\n/", "<br />", $p_description);

        $photo_url = afg_get_photo_url($photo['farm'], $photo['server'],
            $photo['id'], $photo['secret'], $photo_size);

        if ($slideshow_option != 'none') {
            if (isset($photo['url_l'])? $photo['url_l']: '') {
                $photo_page_url = $photo['url_l'];
            }
            else {
                $photo_page_url = afg_get_photo_url($photo['farm'], $photo['server'],
                    $photo['id'], $photo['secret'], '_z');
            }

            if ($photoset_id)
                $photo['owner'] = $user_id;

            $photo_title_text = $p_title;
            $photo_title_text .= ' • <a style="font-size:0.8em;" href="http://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'] . '/" target="_blank">View on Flickr</a>';

            $photo_title_text = esc_attr($photo_title_text);

            if ($slideshow_option == 'flickr') {
                $photo_page_url = "https://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'];
            }
        }

        if ($cur_col % $columns == 0) $disp_gallery .= "<div class='afg-row'>";

        if ( ($photo_count <= $per_page * $cur_page) && ($photo_count > $per_page * ($cur_page - 1)) ) {
            $disp_gallery .= "<div class='afg-cell' style='width:${column_width}%;'>";

            $pid_len = strlen($photo['id']);

            if ($slideshow_option != 'none') {
                if (isset($rel))
                    $disp_gallery .= "<a $class $rel $click_event href='{$photo_page_url}' title='{$photo['title']}'>";
                else
                    $disp_gallery .= "<a $class $click_event href='{$photo_page_url}' title='{$photo['title']}'>";
            }

            if ($custom_size) {
                $timthumb_script = BASE_URL . "/afg_img_rsz.php?src=";
                if($photo['width_l'] > $photo['height_l']) {
                    $timthumb_params = "&q=100&w=$custom_size";
                    if ($custom_size_square == 'true')  $timthumb_params .= "&h=$custom_size";
                }
                else {
                    $timthumb_params = "&q=100&h=$custom_size";
                    if ($custom_size_square == 'true')  $timthumb_params .= "&w=$custom_size";
                }

            }
            else {
                $timthumb_script = "";
                $timthumb_params = "";
            }

            $disp_gallery .= "<img class='afg-img' title='{$photo['title']}' src='{$timthumb_script}{$photo_url}{$timthumb_params}' alt='{$photo_title_text}'/>";

            if ($slideshow_option != 'none')
                $disp_gallery .= "</a>";

            if ($size_heading_map[$photo_size] && $photo_title == 'on') {
                if ($group_id || $gallery_id)
                    $owner_title = "- by <a href='https://www.flickr.com/photos/{$photo['owner']}/' target='_blank'>{$photo['ownername']}</a>";
                else
                    $owner_title = '';

                $disp_gallery .= "<div class='afg-title' style='font-size:{$size_heading_map[$photo_size]}'>{$p_title} $owner_title</div>";
            }

            if($photo_descr == 'on' && $photo_size != '_s' && $photo_size != '_t') {
                $disp_gallery .= "<div class='afg-description'>" .
                    $photo['description']['_content'] . "</div>";
            }

            $cur_col += 1;
            $disp_gallery .= '</div>';
        }
        else {
            if ($pagination == 'on' && $slideshow_option != 'none') {
                $photo_url = '';
                $photo_src_text = "";
                $disp_gallery .= "<a style='display:none' $class $rel $click_event href='$photo_page_url'" .
                    " title='{$photo['title']}'>" .
                    " <img class='afg-img' alt='{$photo_title_text}' $photo_src_text width='75' height='75'></a> ";
            }
        }
        if ($cur_col % $columns == 0) $disp_gallery .= '</div>';
        $photo_count += 1;
    }

    if ($cur_col % $columns != 0) $disp_gallery .= '</div>';
    $disp_gallery .= '</div>';

    // Pagination
    if ($pagination == 'on' && $total_pages > 1) {
        $disp_gallery .= "<div class='afg-pagination'>";
        $disp_gallery .= "<br /><br />";
        if ($cur_page == 1) {
            $disp_gallery .="<font class='afg-page'>&nbsp;&#171; prev&nbsp;</font>&nbsp;&nbsp;&nbsp;&nbsp;";
            $disp_gallery .="<font class='afg-cur-page'> 1 </font>&nbsp;";
        }
        else {
            $prev_page = $cur_page - 1;
            $disp_gallery .= "<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=$prev_page#afg-{$id}' title='Prev Page'>&nbsp;&#171; prev </a>&nbsp;&nbsp;&nbsp;&nbsp;";
            $disp_gallery .= "<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=1#afg-{$id}' title='Page 1'> 1 </a>&nbsp;";
        }
        if ($cur_page - 2 > 2) {
            $start_page = $cur_page - 2;
            $end_page = $cur_page + 2;
            $disp_gallery .= " ... ";
        }
        else {
            $start_page = 2;
            $end_page = 6;
        }
        for ($count = $start_page; $count <= $end_page; $count += 1) {
            if ($count > $total_pages) break;
            if ($cur_page == $count)
                $disp_gallery .= "<font class='afg-cur-page'>&nbsp;{$count}&nbsp;</font>&nbsp;";
            else
                $disp_gallery .= "<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id={$count}#afg-{$id}' title='Page {$count}'>&nbsp;{$count} </a>&nbsp;";
        }

        if ($count < $total_pages) $disp_gallery .= " ... ";
        if ($count <= $total_pages)
            $disp_gallery .= "<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id={$total_pages}#afg-{$id}' title='Page {$total_pages}'>&nbsp;{$total_pages} </a>&nbsp;";
        if ($cur_page == $total_pages) $disp_gallery .= "&nbsp;&nbsp;&nbsp;<font class='afg-page'>&nbsp;next &#187;&nbsp;</font>";
        else {
            $next_page = $cur_page + 1;
            $disp_gallery .= "&nbsp;&nbsp;&nbsp;<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=$next_page#afg-{$id}' title='Next Page'> next &#187; </a>&nbsp;";
        }
        $disp_gallery .= "<br />({$total_photos} Photos)";
        $disp_gallery .= "</div>";
    }
    if ($credit_note == 'on') {
        $disp_gallery .= "<br />";
        $disp_gallery .= "<div class='afg-credit'>Powered by " .
            "<a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin'" .
            "title='Awesome Flickr Gallery by Ronak Gandhi'/>AFG</a>";
        $disp_gallery .= "</div>";
    }
    $disp_gallery .= "</div>";
    $disp_gallery .= "<!-- Awesome Flickr Gallery End -->";
    return $disp_gallery;
}
?>
