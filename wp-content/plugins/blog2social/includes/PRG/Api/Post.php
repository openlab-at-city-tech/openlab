<?php

class PRG_Api_Post {

    public static function post($url = '', $post = array(), $timeout = 30) {
        if (empty($url) || empty($post)) {
            return false;
        }
        $args = array(
            'method' => 'POST',
            'body' => $post,
            'timeout' => $timeout,
            'redirection' => '5',
            'user-agent' => "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)",
        );

        return wp_remote_retrieve_body(wp_remote_post($url, $args));
    }

}
