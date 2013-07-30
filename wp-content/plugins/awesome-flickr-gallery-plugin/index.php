<?php
/*
   Plugin Name: Awesome Flickr Gallery
   Plugin URI: http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/
   Description: Awesome Flickr Gallery is a simple, fast and light plugin to create a gallery of your Flickr photos on your WordPress enabled website.  This plugin aims at providing a simple yet customizable way to create stunning Flickr gallery.
   Version: 3.3.4
   Author: Ronak Gandhi
   Author URI: http://www.ronakg.com
   License: GPL2

   Copyright 2011 Ronak Gandhi (email : ronak.gandhi@ronakg.com)

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

// TEMP: Enable update check on every request. Normally you don't need this! This is for testing only!                                                                    
//set_site_transient('update_plugins', null);

// TEMP: Show which variables are being requested when query plugin API
 /*
add_filter('plugins_api_result', 'afg_aaa_result', 10, 3);
function afg_aaa_result($res, $action, $args) {
    print_r($res);
    return $res;
}
  */

$api_url = 'http://www.ronakg.com/update_plugin/';
$plugin_slug = basename(dirname(__FILE__));
$plugin_slug_file = basename(__FILE__);

// Take over the update check
add_filter('pre_set_site_transient_update_plugins', 'afg_check_for_plugin_update');

function afg_check_for_plugin_update($checked_data) {
	global $api_url, $plugin_slug, $plugin_slug_file;
	
	if (empty($checked_data->checked))
		return $checked_data;
	
	$request_args = array(
        'slug' => $plugin_slug,
        'version' => $checked_data->checked[$plugin_slug .'/'. $plugin_slug_file],
        'package_type' => 'stable',
	);

	$request_string = afg_prepare_request('basic_check', $request_args);
	
	// Start checking for an update
	$raw_response = wp_remote_post($api_url, $request_string);
	
	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		$response = unserialize($raw_response['body']);
	
	if (is_object($response) && !empty($response)) // Feed the update data into WP updater
		$checked_data->response[$plugin_slug .'/'. $plugin_slug_file] = $response;
	
	return $checked_data;
}

// Take over the Plugin info screen
add_filter('plugins_api', 'my_plugin_api_call', 10, 3);

function my_plugin_api_call($def, $action, $args) {
	global $plugin_slug, $api_url, $plugin_slug_file;
	
	if ($args->slug != $plugin_slug)
		return false;
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[$plugin_slug .'/'. $plugin_slug_file];
	$args->version = $current_version;
	
	$request_string = afg_prepare_request($action, $args);
	
	$request = wp_remote_post($api_url, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);
		
		if ($res === false)
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
	}
	
	return $res;
}


function afg_prepare_request($action, $args) {
	global $wp_version;
	
	return array(
		'body' => array(
			'action' => $action, 
			'request' => serialize($args),
			'api-key' => md5(get_bloginfo('url'))
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	);	
}

require_once('afgFlickr/afgFlickr.php');
include_once('admin_settings.php');
include_once('afg_libs.php');

if (!is_admin()) {
    /* Short code to load Awesome Flickr Gallery plugin.  Detects the word
     * [AFG_gallery] in posts or pages and loads the gallery.
     */
    add_shortcode('AFG_gallery', 'afg_display_gallery');
    add_filter('widget_text', 'do_shortcode', 'SHORTCODE_PRIORITY');

    $enable_colorbox = false;

    $galleries = get_option('afg_galleries');
    foreach($galleries as $gallery) {
        if($gallery['slideshow_option'] == 'colorbox') $enable_colorbox = true;
    }

    add_action('wp_enqueue_scripts', 'enqueue_afg_scripts');
}

add_action('wp_head', 'add_afg_headers');

function enqueue_afg_scripts() {
    if (get_option('afg_slideshow_option') == 'colorbox' || $enable_colorbox) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('afg_colorbox_script', BASE_URL . "/colorbox/jquery.colorbox.js" , array('jquery'));
        wp_enqueue_script('afg_colorbox_js', BASE_URL . "/colorbox/mycolorbox.js" , array('jquery'));
        wp_enqueue_style('afg_colorbox_css', BASE_URL . "/colorbox/colorbox.css");
    }
}

function enqueue_afg_styles() {
    wp_enqueue_style('afg_css', BASE_URL . "/afg.css");
}

function add_afg_headers() {
    echo "<style type=\"text/css\">" . get_option('afg_custom_css') . "</style>";
}

function afg_return_error_code($rsp) {
    return $rsp['message'];
}

function upgrade_handler() {
    $galleries = get_option('afg_galleries');
    foreach($galleries as $gallery) {
        if($gallery['slideshow_option'] == 'highslide')
            $gallery['slideshow_option'] = 'colorbox';
    }
}

upgrade_handler();

/* Main function that loads the gallery. */
function afg_display_gallery($atts) {
    global $size_heading_map, $afg_text_color_map, $pf;

    if (!get_option('afg_pagination')) update_option('afg_pagination', 'on');

    extract( shortcode_atts( array(
        'id' => '0',
    ), $atts ) );

    $request_uri = $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'];
    
    if ($request_uri == '' || !$request_uri) $request_uri = $_SERVER['REQUEST_URI'];

    $cur_page = 1;
    $cur_page_url = ( isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS'] == 'on') || $_SERVER['HTTPS'] == '1') ) ? "https://".$_SERVER['SERVER_NAME'].$request_uri : "http://".$_SERVER['SERVER_NAME'].$request_uri;
    $cur_page_url = preg_replace("/hilbert-in.com/", "pkhs.nl", $cur_page_url);
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

    if (isset($gallery['photo_source']) && $gallery['photo_source'] == 'photoset') $photoset_id = $gallery['photoset_id'];
    else if (isset($gallery['photo_source']) && $gallery['photo_source'] == 'gallery') $gallery_id = $gallery['gallery_id'];
    else if (isset($gallery['photo_source']) && $gallery['photo_source'] == 'group') $group_id = $gallery['group_id'];
    else if (isset($gallery['photo_source']) && $gallery['photo_source'] == 'tags') $tags = $gallery['tags'];
    else if (isset($gallery['photo_source']) && $gallery['photo_source'] == 'popular') $popular = true;
    
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

    if (isset($photoset_id) && $photoset_id) {
        $rsp_obj = $pf->photosets_getInfo($photoset_id);
        if ($pf->error_code) return afg_error();
        $total_photos = $rsp_obj['photos'];
    }
    else if (isset($gallery_id) && $gallery_id) {
        $rsp_obj = $pf->galleries_getInfo($gallery_id);
        if ($pf->error_code) return afg_error();
        $total_photos = $rsp_obj['gallery']['count_photos']['_content'];
    }
    else if (isset($group_id) && $group_id) {
        $rsp_obj = $pf->groups_pools_getPhotos($group_id, NULL, NULL, NULL, NULL, 1, 1);
        if ($pf->error_code) return afg_error();
        $total_photos = $rsp_obj['photos']['total'];
        if ($total_photos > 500) $total_photos = 500;
        }
    else if (isset($tags) && $tags) {
        $rsp_obj = $pf->photos_search(array('user_id'=>$user_id, 'tags'=>$tags, 'extras'=>$extras, 'per_page'=>1));
        if ($pf->error_code) return afg_error();
        $total_photos = $rsp_obj['photos']['total'];
    }
    else if (isset($popular) && $popular) {
        $rsp_obj = $pf->photos_search(array('user_id'=>$user_id, 'sort'=>'interestingness-desc', 'extras'=>$extras, 'per_page'=>1));
        if ($pf->error_code) return afg_error();
        $total_photos = $rsp_obj['photos']['total'];
    }
    else {
        $rsp_obj = $pf->people_getInfo($user_id);
        if ($pf->error_code) return afg_error();
        $total_photos = $rsp_obj['photos']['count']['_content'];
    }

    $photos = get_transient('afg_id_' . $id);

    if ($photos == false || $total_photos != count($photos)) {
        $photos = array();
        for($i=1; $i<($total_photos/500)+1; $i++) {
            if ($photoset_id) {
                $flickr_api = 'photoset';
                $rsp_obj_total = $pf->photosets_getPhotos($photoset_id, $extras, NULL, 500, $i);
                if ($pf->error_code) return afg_error();
            }
            else if ($gallery_id) {
                $flickr_api = 'photos';
                $rsp_obj_total = $pf->galleries_getPhotos($gallery_id, $extras, 500, $i);
                if ($pf->error_code) return afg_error();
            }
            else if ($group_id) {
                $flickr_api = 'photos';
                $rsp_obj_total = $pf->groups_pools_getPhotos($group_id, NULL, NULL, NULL, $extras, 500, $i);
                if ($pf->error_code) return afg_error();
            }
            else if ($tags) {
                $flickr_api = 'photos';
                $rsp_obj_total = $pf->photos_search(array('user_id'=>$user_id, 'tags'=>$tags, 'extras'=>$extras, 'per_page'=>500, 'page'=>$i));
                if ($pf->error_code) return afg_error();
            }
            else if ($popular) {
                $flickr_api = 'photos';
                $rsp_obj_total = $pf->photos_search(array('user_id'=>$user_id, 'sort'=>'interestingness-desc', 'extras'=>$extras, 'per_page'=>500, 'page'=>$i));
                if ($pf->error_code) return afg_error();
            }
            else {
                $flickr_api = 'photos';
                if (get_option('afg_flickr_token')) $rsp_obj_total = $pf->people_getPhotos($user_id, array('extras' => $extras, 'per_page' => 500, 'page' => $i));
                else $rsp_obj_total = $pf->people_getPublicPhotos($user_id, NULL, $extras, 500, $i);
                if ($pf->error_code) return afg_error();
            }
            $photos = array_merge($photos, $rsp_obj_total[$flickr_api]['photo']);
        }
        set_transient('afg_id_' . $id, $photos, 60 * 60 * 24 * 3);
    }

    if (($total_photos % $per_page) == 0) $total_pages = (int)($total_photos / $per_page);
    else $total_pages = (int)($total_photos / $per_page) + 1;

    if ($gallery_width == 'auto') $gallery_width = 100;
    $text_color = isset($afg_text_color_map[$bg_color])? $afg_text_color_map[$bg_color]: '';
    $disp_gallery .= "<div class='afg-gallery custom-gallery-{$id}' style='background-color:{$bg_color}; width:$gallery_width%; color:{$text_color}; border-color:{$bg_color};'>";

    $disp_gallery .= "<div class='afg-table' style='width:100%'>";

    $photo_count = 1;
    $cur_col = 0;
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
                $photo_page_url = "http://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'];
            }
        }

        if ( ($photo_count <= $per_page * $cur_page) && ($photo_count > $per_page * ($cur_page - 1)) ) {
            if ($cur_col % $columns == 0) $disp_gallery .= "<div class='afg-row'>";
            $disp_gallery .= "<div class='afg-cell' style='width:${column_width}%;'>";

            $pid_len = strlen($photo['id']);

            if ($slideshow_option != 'none')
                $disp_gallery .= "<a $class $rel $click_event href='{$photo_page_url}' title='{$photo['title']}'>";

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

            $disp_gallery .= "<img class='afg-img' src='{$timthumb_script}{$photo_url}{$timthumb_params}' alt='{$photo_title_text}'/>";

            if ($slideshow_option != 'none')
                $disp_gallery .= "</a>";

            if ($size_heading_map[$photo_size] && $photo_title == 'on') {
                if ($group_id || $gallery_id)
                    $owner_title = "- by <a href='http://www.flickr.com/photos/{$photo['owner']}/' target='_blank'>{$photo['ownername']}</a>";
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
            if ($cur_col % $columns == 0) $disp_gallery .= '</div>';
        }
        else {
            if ($pagination == 'on' && $slideshow_option != 'none') {
                $disp_gallery .= "<a style='display:none' $class $rel $click_event href='$photo_page_url'" .
                    " title='{$photo_title_text}'>" .
                    " <img class='afg-img' alt='{$photo_title_text}' width='75' height='75'></a> ";
            }
        }
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
            $disp_gallery .= "<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=$prev_page' title='Prev Page'>&nbsp;&#171; prev </a>&nbsp;&nbsp;&nbsp;&nbsp;";
            $disp_gallery .= "<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=1' title='Page 1'> 1 </a>&nbsp;";
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
                $disp_gallery .= "<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id={$count}' title='Page {$count}'>&nbsp;{$count} </a>&nbsp;";
        }

        if ($count < $total_pages) $disp_gallery .= " ... ";
        if ($count <= $total_pages)
            $disp_gallery .= "<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id={$total_pages}' title='Page {$total_pages}'>&nbsp;{$total_pages} </a>&nbsp;";
        if ($cur_page == $total_pages) $disp_gallery .= "&nbsp;&nbsp;&nbsp;<font class='afg-page'>&nbsp;next &#187;&nbsp;</font>";
        else {
            $next_page = $cur_page + 1;
            $disp_gallery .= "&nbsp;&nbsp;&nbsp;<a class='afg-page' href='{$cur_page_url}{$url_separator}afg{$id}_page_id=$next_page' title='Next Page'> next &#187; </a>&nbsp;";
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
