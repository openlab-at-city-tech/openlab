<?php

class B2S_Hook_Filter {

    function get_wp_user_post_author_display_name($wp_post_author_id = 0) {
        $user_data = get_userdata($wp_post_author_id);
        if ($user_data != false && !empty($user_data->display_name)) {
            $wp_display_name = apply_filters('b2s_filter_wp_user_post_author_display_name', $user_data->display_name, $wp_post_author_id);
            return $wp_display_name;
        }
        return '';
    }

    function get_wp_post_hashtag($post_id = 0, $post_type = '') {
        $keywords = wp_get_post_tags((int) $post_id);
        if (($keywords == false || empty($keywords))) {
            if (taxonomy_exists($post_type . '_tag')) {
                $keywords = wp_get_post_terms((int) $post_id, $post_type . '_tag');
            } elseif (taxonomy_exists($post_type . '-tag')) {
                $keywords = wp_get_post_terms((int) $post_id, $post_type . '-tag');
            }
        }
        $wp_hashtags = apply_filters('b2s_filter_wp_post_hashtag', $keywords, $post_id);
        return $wp_hashtags;
    }

    function get_wp_post_image($post_id = 0, $forceFeaturedImage = true, $postContent = '', $postUrl = '', $network = false, $postLang = 'en') {
        try {
            require_once(B2S_PLUGIN_DIR . 'includes/B2S/Hook/Filter.php');
            $images = B2S_Util::getImagesByPostId((int) $post_id, $forceFeaturedImage, $postContent, $postUrl, $network, $postLang);
            $wp_images = apply_filters('b2s_filter_wp_post_image', $images, $post_id);
            return $wp_images;
        } catch (Exception $ex) {
            if (function_exists('error_log')) {
                error_log('Blog2Social Wordpress Plugin Hook Filter FKT get_wp_post_image failed - Message: ' . $ex->getMessage());
            }
            return false;
        }
    }
    
    function get_posting_template_show_taxonomies($taxonomies = array()) {
        if(!defined('B2S_PLUGIN_USER_VERSION') || (defined('B2S_PLUGIN_USER_VERSION') && B2S_PLUGIN_USER_VERSION < 3)) {
            return $taxonomies;
        }
        $taxonomies_data = apply_filters('b2s_filter_posting_template_show_taxonomies', $taxonomies);
        return $taxonomies_data;
    }
    
    function get_posting_template_set_taxonomies($taxonomies = array(), $postId = 0) {
        if(!defined('B2S_PLUGIN_USER_VERSION') || (defined('B2S_PLUGIN_USER_VERSION') && B2S_PLUGIN_USER_VERSION < 3)) {
            return $taxonomies;
        }
        $taxonomies_data = apply_filters('b2s_filter_posting_template_set_taxonomies', $taxonomies, $postId);
        return $taxonomies_data;
    }

}
