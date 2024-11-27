<?php

class B2S_Notice {

    public static function getProVersionNotice() {
        if (defined("B2S_PLUGIN_TOKEN")) {
            global $hook_suffix;
            if (in_array($hook_suffix, array('index.php', 'plugins.php'))) {
                if (B2S_PLUGIN_USER_VERSION == 0) {
                    global $wpdb;
                    $userResult = $wpdb->get_row($wpdb->prepare('SELECT feature,register_date FROM ' . $wpdb->prefix . 'b2s_user WHERE blog_user_id =%d', B2S_PLUGIN_BLOG_USER_ID));
                    if ($userResult->register_date == '0000-00-00 00:00:00') {
                        $wpdb->update('b2s_user', array('register_date' => date('Y-m-d H:i:s')), array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID), array('%s'), array('%d'));
                    } else if ($userResult->feature == 0 && strtotime($userResult->register_date) < strtotime('-6 days')) {
                        wp_enqueue_style('B2SNOTICECSS');
                        echo '<div class="updated b2s-notice-rate">
                            <strong>' . esc_html__("Rate it!", "blog2social") . '</strong>
                            <p>' . esc_html__("If you like Blog2Social, please give us a 5 star rating. If there is anything that does not work for you, please contact us!", "blog2social") . '
                                    <b><a href="https://wordpress.org/support/plugin/blog2social/reviews/" target="_bank">' . esc_html__('RATE BLOG2SOCIAL', 'blog2social') . '</a></b>
                                    <small><a href="' . esc_url(wp_nonce_url(add_query_arg('b2s_action', 'hide_notice'), 'b2s_notice_nonce', 'b2s_notice_nonce')) . '">(' . esc_html__('hide', 'blog2social') . ')</a></small>
                            </p>
                         </div>';
                    }
                }
            }
        }
    }

    public static function hideProVersionNotice() {
        if (isset($_GET['b2s_action']) && sanitize_text_field(wp_unslash($_GET['b2s_action'])) == 'hide_notice') {
            if (isset($_GET['b2s_notice_nonce']) && wp_verify_nonce(sanitize_key(wp_unslash($_GET['b2s_notice_nonce'])), 'b2s_notice_nonce')) {
                global $wpdb;
                $wpdb->update($wpdb->prefix . 'b2s_user', array('feature' => 1), array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID), array('%d'), array('%d'));
            } else {
                wp_die(esc_html__('Could not hide notice. Please refresh the page and retry.', 'blog2social'));
            }
        }
    }

    public static function getBlogEntries($lang = 'en') {
        return json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getBlogEntries', 'lang' => $lang, 'token' => B2S_PLUGIN_TOKEN)));
    }

    public static function getCalEvent($lang = 'en') {
        $content = '';
        $output = '';
        if (current_user_can('read')) {
            $option = get_option('B2S_CAL_EVENT_' . strtoupper($lang));
            if ($option !== false) {
                if (is_array($option) && isset($option['last_call_date']) && isset($option['content']) && !empty($option['content']) && $option['last_call_date'] == date('Y-m-d')) {
                    $content = $option['content'];
                }
            }
            if (empty($content)) {
                $data = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getCalEvent', 'lang' => $lang, 'currentDate' => date('Y-m-d'), 'token' => B2S_PLUGIN_TOKEN)));
                if (isset($data->result) && (int) $data->result == 1 && isset($data->content) && !empty($data->content)) {
                    $content = $data->content;
                }
                update_option('B2S_CAL_EVENT_' . strtoupper($lang), array("last_call_date" => date("Y-m-d"), "content" => $content), false);
            }
            if (!empty($content)) {
                $arr = array();
                foreach ($content as $key => $item) {
                    $arr[$item->start_date][$key] = $item;
                }
                ksort($arr, SORT_NUMERIC);
                $lc = new WP_Locale();
                if (!empty($arr)) {
                    $output .= '<div class="col-md-12"><div class="row"><hr><div class="b2s-sidebar-head"><div class="b2s-sidebar-head-text"><span class="glyphicon glyphicon-calendar glyphicon-success"></span> 
                            ' . esc_html__("Today's Content Calendar Tips", "blog2social") . '
                            </div></div></div>';
                    foreach ($arr as $c => $v) {
                        $isCurrentDate = (date('Y-m-d') == $c) ? 'row-striped' : '';
                        $output .= '<div class="row ' . $isCurrentDate . ' b2s-cal-padding-10">
                                    <div class="text-left">
                                    <div class="b2s-font-size-18"><span class="badge b2s-cal-badge b2s-cal-badge-today">' . date('d', strtotime($c)) . '</span> ' . $lc->get_month(date('n', strtotime($c))) . '</div>
                                    </div>';
                        $countItem = 0;
                        foreach ($v as $event) {
                            if ($countItem > 0) {
                                $output .= '<hr class="b2s-cal-hr">';
                            }
                            $output .= '<div class="text-left b2s-margin-left-5">
                                        <h4 class="b2s-cal-h4 b2s-margin-bottom-3">' . $event->event . '</h4>
                                        <ul class="list-inline b2s-margin-bottom-0">
                                        <li class="list-inline-item b2s-margin-bottom-0 b2s-font-italic b2s-font-size-11">' . $event->hashtag . '</li>
                                        </ul></div>';
                            $countItem++;
                        }
                        $output .= '</div>';
                    }
                    $output .= '<div class="clearfix"></div>
                      <div class="row"><a href="https://b2s.li/wp-plugin-social-media-calendar" class="btn btn-success btn-block" target="_blank">' . esc_html__(" Explore more tips and dates", "blog2social") . '</a></div>';
                    $output .= '</div>';
                }
            }
        }
        return $output;
    }

    public static function getFaqEntriesHtml($items = '') {
        $content = '';
        if (!empty($items)) {
            $content .= '<ol>';
            $content .= $items;
            $content .= '</ol>';
        }
        return $content;
    }

    public static function sytemNotice() {
        $b2sSytem = new B2S_System();
        $b2sCheck = $b2sSytem->check();
        if (is_array($b2sCheck) && !empty($b2sCheck)) {
            $output = '<div id="message" class="notice inline notice-warning notice-alt"><p>';
            $output .= $b2sSytem->getErrorMessage($b2sCheck, true);
            $output .= '</p></div>';
            echo wp_kses($output, array(
                'div' => array(
                    'id' => array(),
                    'class' => array(),
                ),
                'p' => array(),
                'br' => array(),
                'a' => array(
                    'href' => array(),
                    'target' => array(),
                    'class' => array(),
                ),
            ));
        }
    }

    public static function getPostStats($openPosts = 0, $maxPosts = 0) {
        $x = 100;
        if ($openPosts > 0) {
            $x = (int) ((($openPosts / $maxPosts) * $x));
        }

        $state = 'success';
        if ($x <= 50) {
            if ($x >= 25) {
                $state = 'warning';
            } else {
                $state = 'danger';
            }
        }

        $output = '
          <div class="progress b2s-margin-bottom-3 b2s-progress-height-10">
          <div class="progress-bar progress-bar-' . $state . '" style="width: ' . $x . '%;"></div>
          </div>';

        return $output;
    }

}
