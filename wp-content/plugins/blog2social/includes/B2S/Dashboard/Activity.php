<?php

class B2S_Dashboard_Activity{
    
    public function __construct() {}

    public function getPublishItemHtml($postData = array()) {
        if (!is_array($postData) || empty($postData)) {
            return '';
        }
        $listContent = '';
        foreach ($postData as $post) {
            $listContent .= '<li><a href="admin.php?page=blog2social-publish&showPostId=' . (int)$post->ID . '">';
            // Date Header
            $listContent .= '<span class="b2s-font-bold b2s-color-black b2s-font-size-18">' . esc_html($post->formatted_date) . '</span><br>';
            // Title
            $listContent .= '<span class="b2s-font-bold">' . esc_html($post->post_title) . '</span><br>';
            // Subline
            $listContent .= '<span>' . esc_html($post->post_count) . ' ' . esc_html("shared social media posts", "blog2social") . ' | <span class="b2s-color-black">' . esc_html("last shared by", "blog2social") . ' ' . esc_html($post->blog_user_name) . '</span></span>';
            $listContent .= '</a></li>';
        }
        return $listContent;
    }
    
    public function getSchedItemHtml($postData = array()) {
        if (!is_array($postData) || empty($postData)) {
            return '';
        }
        $listContent = '';
        foreach ($postData as $post) {
            $listContent .= '<li><a href="admin.php?page=blog2social-sched&showPostId=' . (int)$post->ID . '">';
            // Date Header
            $listContent .= '<span class="b2s-font-bold b2s-color-black b2s-font-size-18">' . esc_html($post->formatted_date) . '</span><br>';
            // Title
            $listContent .= '<span class="b2s-font-bold">' . esc_html($post->post_title) . '</span><br>';
            // Subline
            $listContent .= '<span>' . esc_html($post->post_count) . ' ' . esc_html("scheduled social media posts", "blog2social") . ' | <span class="b2s-color-black">' . esc_html("next share by", "blog2social") . ' ' . esc_html($post->blog_user_name) . '</span></span>';
            $listContent .= '</a></li>';
        }
        return $listContent;
    }
}