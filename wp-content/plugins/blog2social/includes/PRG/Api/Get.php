<?php

class PRG_Api_Get {

    public static function get($url = '', $header = array("Content-Type:application/x-www-form-urlencoded"), $timeout = 30) {
        if (empty($url)) {
            return false;
        }

        $args = array(
            'timeout' => $timeout,
            'redirection' => '5',
            'user-agent' => "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)",
            'headers' => $header
        );
        return wp_remote_retrieve_body(wp_remote_get($url, $args));
    }

}
