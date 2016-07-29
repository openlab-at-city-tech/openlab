<?php

/*
 * Media-oriented functionality
 */

function openlab_get_home_slider() {
    global $post;
    $slider_mup = '';
    $slider_sr_mup = '';

    $slider_args = array(
        'post_type' => 'slider',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    $legacy = $post;
    $slider_query = new WP_Query($slider_args);

    if ($slider_query->have_posts()):
        $slider_mup = '<div class="camera_wrap clearfix" tabindex="-1" aria-hidden="true">';
        $slider_sr_mup = '<div class="camera_wrap_sr" role="widget"><h2 class="sr-only">Slideshow Content</h2><ul class="list-unstyled">';
        while ($slider_query->have_posts()) : $slider_query->the_post();
            //if the featured image is not set, slider will not be added
            if (get_post_thumbnail_id()) {

                $img_obj = wp_get_attachment_image_src(get_post_thumbnail_id(), 'front-page-slider');

                $slider_mup .= '<div data-alt="' . get_the_title() . '" data-src="' . $img_obj[0] . '"><div class="fadeIn camera_content"><h2 class="regular">' . get_the_title() . '</h2>' . get_the_content_with_formatting() . '</div></div>';
                $slider_sr_mup .= '<li class="sr-only sr-only-focusable camera_content" tabindex="0"><h2 class="regular">' . get_the_title() . '</h2>' . get_the_content_with_formatting() . '</li>';
            }
        endwhile;
        $slider_mup .= '</div>';
        $slider_sr_mup .= '</ul></div>';
    endif;

    $post = $legacy;

    return $slider_mup . $slider_sr_mup;
}

/**
 * Custom mysteryman
 * @return type
 */
function openlab_new_mysteryman() {
    return get_stylesheet_directory_uri() . '/images/default-avatar.jpg';
}

add_filter('bp_core_mysteryman_src', 'openlab_new_mysteryman', 2, 7);

/**
 * Custom default avatar
 * @param string $url
 * @param type $params
 * @return string
 */
function openlab_default_get_group_avatar( $url, $params ) {
	if ( strstr( $url, 'default-avatar' ) || strstr( $url, 'wavatar' ) || strstr( $url, 'mystery-group.png' ) ) {
		$url = get_stylesheet_directory_uri() . '/images/default-avatar.jpg';
	}

	return $url;
}

add_filter( 'bp_core_fetch_avatar_url', 'openlab_default_get_group_avatar', 10, 2 );

/**
 * WordPress adds dimensions to embedded images; this is totally not responsive WordPress
 * @param type $html
 * @return type
 */
function openlab_remove_thumbnail_dimensions($html) {

    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);

    return $html;
}

add_filter('post_thumbnail_html', 'openlab_remove_thumbnail_dimensions', 10);
add_filter('image_send_to_editor', 'openlab_remove_thumbnail_dimensions', 10);
add_filter('the_content', 'openlab_remove_thumbnail_dimensions', 10);

function openlab_activity_user_avatar() {
    global $activities_template;
    $current_activity_item = isset($activities_template->activity->current_comment) ? $activities_template->activity->current_comment : $activities_template->activity;
    $item_id = !empty($user_id) ? $user_id : $current_activity_item->user_id;
    $item_id = apply_filters('bp_get_activity_avatar_item_id', $item_id);

    return '<img class="img-responsive" src ="' . bp_core_fetch_avatar(array('item_id' => $item_id, 'object' => 'user', 'type' => 'full', 'html' => false)) . '" alt="' . bp_get_displayed_user_fullname() . '"/>';
}
