<?php

class B2S_Api_Get {

    public static function get($url = '', $timeout = 15) {
        if (empty($url)) {
            return false;
        }
        $args = array(
            'timeout' => $timeout,
            'redirection' => '5',
            'user-agent' => "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)",
        );

        return wp_remote_retrieve_body(wp_remote_get($url, $args));
    }

}
