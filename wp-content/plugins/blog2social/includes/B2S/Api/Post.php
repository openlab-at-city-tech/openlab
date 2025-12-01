<?php

class B2S_Api_Post {

    public static function post($url = '', $post = array(), $timeout = 60) {
        if (empty($url) || empty($post)) {
            return false;
        }
        $wpVersion = get_bloginfo('version');
        $pluginVersion =  implode('.', str_split((string) B2S_PLUGIN_VERSION));
        $ua = sprintf(
                'Blog2SocialBot/1.0 (WP/%s; Plugin/%s; +https://en.blog2social.com/bot-info; bot@blog2social.com)',
                $wpVersion,
                $pluginVersion
        );

        $args = array(
            'method' => 'POST',
            'body' => $post,
            'timeout' => $timeout,
            'redirection' => '5',
            'user-agent' => $ua);

        return wp_remote_retrieve_body(wp_remote_post($url . 'post.php', $args));
    }
}
