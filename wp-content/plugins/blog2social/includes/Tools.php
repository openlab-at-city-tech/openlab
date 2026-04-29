<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
 */

class B2S_Tools {

    public static function showNotice() {
        return (defined("B2S_PLUGIN_NOTICE") || !defined("B2S_PLUGIN_TOKEN")) ? true : false;
    }

    public static function getToken($data = array()) {
        return B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $data, 30);
    }

    public static function setUserDetails($blog_user_id = null, $blog_url = null, $email = null) {
        if (defined("B2S_PLUGIN_TOKEN")) {
            delete_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
            delete_option('B2S_PLUGIN_PRIVACY_POLICY_USER_ACCEPT_' . B2S_PLUGIN_BLOG_USER_ID);

            $currentDate = new DateTime("now", wp_timezone());
            $version = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getUserDetails',
                        'blog_user_id' => $blog_user_id,
                        'blog_url' => $blog_url,
                        'email' => $email,
                        'current_date' => $currentDate->format('Y-m-d'),
                        'token' => B2S_PLUGIN_TOKEN,
                        'version' => B2S_PLUGIN_VERSION), 30));

            $tokenInfo = array();
            $tokenInfo['B2S_PLUGIN_USER_VERSION'] = (isset($version->version) ? $version->version : 0);
            $tokenInfo['B2S_PLUGIN_VERSION'] = B2S_PLUGIN_VERSION;
            if (!defined("B2S_PLUGIN_USER_VERSION")) {
                define('B2S_PLUGIN_USER_VERSION', $tokenInfo['B2S_PLUGIN_USER_VERSION']);
            }

            if (isset($version->trial) && $version->trial != "") {
                $tokenInfo['B2S_PLUGIN_TRAIL_END'] = $version->trial;

                if (!defined("B2S_PLUGIN_TRAIL_END")) {
                    define('B2S_PLUGIN_TRAIL_END', $tokenInfo['B2S_PLUGIN_TRAIL_END']);
                }
            }
            if (isset($version->permission_insight)) {
                $tokenInfo['B2S_PLUGIN_PERMISSION_INSIGHTS'] = (int) $version->permission_insight;
                if (!defined("B2S_PLUGIN_PERMISSION_INSIGHTS")) {
                    define('B2S_PLUGIN_PERMISSION_INSIGHTS', $tokenInfo['B2S_PLUGIN_PERMISSION_INSIGHTS']);
                }
            }
            //has addon
            if (isset($version->addon->video)) {
                $tokenInfo['B2S_PLUGIN_ADDON_VIDEO'] = (array) $version->addon->video;
                if (!defined("B2S_PLUGIN_ADDON_VIDEO")) {
                    define('B2S_PLUGIN_ADDON_VIDEO', $tokenInfo['B2S_PLUGIN_ADDON_VIDEO']);
                }
            }
            if (isset($version->addon->app)) {
                $appQuantity = unserialize(B2S_PLUGIN_DEFAULT_USER_APP_QUANTITY);
                $quantity = isset($appQuantity[$tokenInfo['B2S_PLUGIN_USER_VERSION']]) ? $appQuantity[$tokenInfo['B2S_PLUGIN_USER_VERSION']] : 1;

                if (defined("B2S_PLUGIN_TRAIL_END")) {
                    $quantity = 1;
                }
                $network_quantities = array();
                foreach (unserialize(B2S_PLUGIN_USER_APP_NETWORKS) as $network) {
                    $network_quantities[$network] = $quantity;
                }
                foreach ($version->addon->app as $network_id => $entry) {
                    foreach ($entry as $individual_addon) {
                        if (isset($individual_addon->volume_total)) {
                            $network_quantities[$network_id] = (int) $network_quantities[$network_id] + (int) $individual_addon->volume_total;
                        }
                    }
                }

                if (!defined('B2S_PLUGIN_ALLOWED_USER_APPS')) {
                    define('B2S_PLUGIN_ALLOWED_USER_APPS', serialize($network_quantities));
                    $tokenInfo['B2S_PLUGIN_ALLOWED_USER_APPS'] = serialize($network_quantities);
                }
            }

            if (isset($version->licence_condition)) {
                $tokenInfo['B2S_PLUGIN_LICENCE_CONDITION'] = (array) $version->licence_condition;
            }

            if (isset($version->network_condition)) {
                $tokenInfo['B2S_PLUGIN_NETWORK_CONDITION'] = (array) $version->network_condition;
            }

            if (!isset($version->version)) {
                define('B2S_PLUGIN_NOTICE', 'CONNECTION');
            } else {
                $tokenInfo['B2S_PLUGIN_USER_VERSION_NEXT_REQUEST'] = time() + 3600;
                update_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID, $tokenInfo, false);
            }

            if (isset($version->show_privacy_policy) && !empty($version->show_privacy_policy)) {
                update_option('B2S_PLUGIN_PRIVACY_POLICY_USER_ACCEPT_' . B2S_PLUGIN_BLOG_USER_ID, $version->show_privacy_policy, false);
            }
        }
    }

    public static function checkUserBlogUrl() {
        $check = false;
        $blogUrl = get_option('home');
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare("SELECT token,state_url FROM {$wpdb->prefix}b2s_user WHERE blog_user_id = %d", B2S_PLUGIN_BLOG_USER_ID));
        if (is_array($result) && !empty($result) && isset($result[0]->token)) {
            if (isset($result[0]->state_url) && (int) $result[0]->state_url != 1) {
                $checkBlogUrl = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getBlogUrl', 'token' => $result[0]->token, 'blog_url' => strtolower($blogUrl), 'state_url' => (int) $result[0]->state_url)));
                if (isset($checkBlogUrl->result) && (int) $checkBlogUrl->result == 1) {
                    if (isset($checkBlogUrl->update) && (int) $checkBlogUrl->update == 1) {
                        $wpdb->update($wpdb->prefix . 'b2s_user', array('state_url' => "1"), array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID), array('%d'), array('%d'));
                    }
                    $check = true;
                }
            } else {
                $check = true;
            }
        }
        define("B2S_PLUGIN_NOTICE_SITE_URL", $check);
    }

    public static function getRandomBestTimeSettings() {
        $lang = substr(B2S_LANGUAGE, 0, 2);
        $defaultTimes = unserialize(B2S_PLUGIN_SCHED_DEFAULT_TIMES);
        $allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
        $allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
        $userTimes = array();
        if (is_array($defaultTimes) && !empty($defaultTimes)) {
            $slug = ($lang == 'en') ? 'h:i A' : 'H:i';
            foreach ($defaultTimes as $k => $v) {
                if (is_array($v) && !empty($v)) {
                    $endProfile = $v[1];
                    $getTimeForPage = in_array($k, $allowPage) ? true : false;
                    $getTimeForGroup = in_array($k, $allowGroup) ? true : false;
                    if ($getTimeForPage) {
                        $endProfile = wp_date("H:i", strtotime('-30 minutes', strtotime($endProfile . ':00')), new DateTimeZone(date_default_timezone_get()));   //-30min
                    }
                    if ($getTimeForGroup) {
                        $endProfile = wp_date("H:i", strtotime('-30 minutes', strtotime($endProfile . ':00')), new DateTimeZone(date_default_timezone_get()));   //-30min
                    }
                    $endProfile = (strpos($endProfile, ':') === false) ? $endProfile . ':00' : $endProfile;
                    $startProfle = (strpos($v[0], ':') === false) ? $v[0] . ':00' : $v[0];
                    $dateTime = wp_date('Y-m-d ' . B2S_Util::getRandomTime($startProfle, $endProfile) . ':00', null, new DateTimeZone(date_default_timezone_get()));
                    //Profile
                    $userTimes[$k][0] = wp_date($slug, strtotime($dateTime), new DateTimeZone(date_default_timezone_get()));
                    //Page
                    $dateTime = ($getTimeForPage) ? strtotime('+30 minutes', strtotime($dateTime)) : strtotime($dateTime);
                    $userTimes[$k][1] = ($getTimeForPage) ? wp_date($slug, $dateTime, new DateTimeZone(date_default_timezone_get())) : "";
                    //Group
                    $dateTime = strtotime('+30 minutes', $dateTime);
                    $userTimes[$k][2] = ($getTimeForGroup) ? wp_date($slug, $dateTime, new DateTimeZone(date_default_timezone_get())) : "";
                }
            }
        }
        return $userTimes;
    }

    public static function getSupportLink($type = 'howto', $add_slug = '') {
        $lang = substr(B2S_LANGUAGE, 0, 2);
        if ($type == 'howto') {
            return 'https://blog2social.com/docs/' . (($lang == 'en') ? 'blog2social-guide-step-by-step-en.pdf' : 'step-by-step-guide-zu-blog2social.pdf');
        }
        if ($type == 'faq') {
            return 'https://service.blog2social.com/support?url=' . get_option('home') . '&token=' . B2S_PLUGIN_TOKEN;
        }

        if ($type == 'faq_license_key') {
            return 'https://www.blog2social.com/en/faq/content/7/48/en/where-do-i-find-my-license-key.html';
        }

        if ($type == 'faq_direct') {
            return 'https://www.blog2social.com/' . (($lang == 'en') ? 'en' : 'de') . "/faq/";
        }
        if ($type == 'addon_video_trial') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/video-sharing/#trial' : 'https://www.blog2social.com/de/video-teilen/#trial';
        }
    
        if ($type == 'video_sharing_tiktok') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1204' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1201';
        }

        if ($type == 'trial') {
            return 'https://blog2social.com/' . (($lang == 'en') ? 'en/plugin/wordpress/premium-trial/' : 'de/plugin/wordpress/premium-testen/');
        }

        if ($type == 'upgrade_version') {
            $affiliateId = self::getAffiliateId();
            return 'https://b2s.li/wp-btn-premium/' . (((int) $affiliateId != 0) ? $affiliateId : 0) . '/' . ((!empty($add_slug)) ? $add_slug . '/' : '');
        }
     
        if ($type == 'term') {
            return 'https://www.blog2social.com/' . (($lang == 'en') ? 'en/terms' : 'de/agb');
        }
        if ($type == 'privacy_policy') {
            return 'https://www.blog2social.com/' . (($lang == 'en') ? 'en/privacy-policy' : 'de/datenschutz');
        }

        if ($type == 'ass_account') {
            return 'https://app.assistini.com/?screen=Plan';
        }

        if ($type == 'pinterest_app_tos_spam') {
            return 'https://developers.pinterest.com/docs/reference/spam/';
        }

        if ($type == 'dashboard-video-posting-addon-info') {
            return ($lang == 'en') ? 'https://en.blog2social.com/video-posting/' : 'https://de.blog2social.com/video-posting/';
        }

        if ($type == 'network_guide_re_sharer') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1165' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1162';
        }

        if ($type == 'userTimeSettings') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=5&id=32&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=5&id=43&artlang=de';
        }
        //TOS Twitter 032018
        //BTN: More information Twitter
        if ($type == 'network_tos_faq_032018') {
            return (($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/3/127/en/twitter-terms-of-service-update-february-2018-common-questions.html' : 'https://www.blog2social.com/de/faq/content/3/127/de/twitter-aenderung-der-allgemeinen-geschaeftsbedingungen-update-februar-2018-haeufig-gestellte-fragen.html');
        }
        //BTN: Learn more about this Twitter
        if ($type == 'network_tos_blog_032018') {
            return (($lang == 'en') ? 'https://www.blog2social.com/en/blog/how-new-twitter-rules-impact-your-social-media-marketing' : 'https://www.blog2social.com/de/blog/neue-twitter-regeln-social-media-marketing');
        }
        //Twitter own app since V7.2.0
        if ($type == 'deprecated_auth_network_2') {
            return $lang == 'en' ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1145' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1007';
        }
        //TOS Pinterest own app since V7.5.1
        if ($type == 'network_app_is_trial') {
            return $lang == 'en' ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1019' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1022';
        }
        //TOS Facebook 072018
        //BTN: read more  Facebook
        if ($type == 'network_tos_faq_news_072018') {
            return (($lang == 'en') ? 'https://www.blog2social.com/en/faq/news/39/en/version-491-_-facebook-profile-changes-_-introducing-facebook-instant-sharing.html' : 'https://www.blog2social.com/de/faq/news/35/de/version-491-_-facebook_profil_aenderungen-_-neue-funktion-facebook-instant-sharing.html');
        }
        //TOS Xing 082018
        //BTN: read more Xing
        if ($type == 'network_tos_blog_082018') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/3/137/en/how-to-successfully-post-to-xing-groups.html' : 'https://www.blog2social.com/de/faq/content/3/135/de/so-gelingt-ihnen-das-erfolgreiche-teilen-in-xing_gruppen.html';
        }
        //BTN: read more Xing
        if ($type == 'network_tos_blog_032019') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=146&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=145&artlang=de';
        }
        if ($type == 'system_requirements') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=1&id=58&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=1&id=63&artlang=de';
        }
        if ($type == 'hotlink_protection') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=80&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=84&artlang=de';
        }
        if ($type == 'faq_installation') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=1' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=1';
        }
        if ($type == 'faq_network') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=2' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=2';
        }
        if ($type == 'faq_sharing') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=3' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=3';
        }
        if ($type == 'faq_customize') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=4' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=4';
        }
        if ($type == 'faq_scheduling') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=5' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=5';
        }
        if ($type == 'faq_repoting') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=6' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=6';
        }
        if ($type == 'faq_licence') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=7' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=7';
        }
        if ($type == 'faq_security') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=8' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=8';
        }
        if ($type == 'faq_troubleshooting') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=9' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=9';
        }
        if ($type == 'faq_affiliate') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=10' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=10';
        }
        if ($type == 'faq_settings') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=11' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=11';
        }
        if ($type == 'faq_postformats') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/4/131/en/social-media-post-formats-_-the-differences-between-image-post-and-link-post.html' : 'https://www.blog2social.com/de/faq/content/4/131/de/social-media-postformate-_-die-unterschiede-zwischen-bild_beitraegen-und-link_beitraegen.html';
        }
        if($type == 'faq_first_comment') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1281' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1275';
        }
        if($type == 'faq_woocommerce_product_sharing') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1269' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1263';
        }
        if ($type == 'browser_extension') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/webapp/extension/' : 'https://www.blog2social.com/de/webapp/extension/';
        }
        if ($type == 'xing_auto_posting') {
            return ($lang == 'en') ? 'https://faq.xing.com/en/groups/code-conduct-group-members' : 'https://faq.xing.com/de/gruppen/verhaltenskodex-f%C3%BCr-gruppenmitglieder';
        }
        if ($type == 'system') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/1/58/en/system-requirements-for-installing-blog2social.html' : 'https://www.blog2social.com/de/faq/content/1/63/de/systemvoraussetzungen-fuer-die-installation-von-blog2social.html';
        }
        if ($type == 'share_error') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1205' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1202';
        }
        if ($type == 'instagram_without_text') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=154&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=152&artlang=de';
        }
        if ($type == 'auto_poster_m') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=72&artlang=en' : 'https://www.blog2social.com/de/faq/content/3/79/de/wie-kann-ich-meine-blogbeitraege-automatisiert-und-zeitgesteuert-auf-social-media-planen-social-media-auto_poster.html';
        }
        if ($type == 'auto_poster_a') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=116&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=116&artlang=de';
        }
        if ($type == 'open_graph_tags') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=103&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=106&artlang=de';
        }
        if ($type == 'twitter_cards') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/4/109/en/what-are-twitter-cards.html' : 'https://www.blog2social.com/de/faq/content/4/109/de/was-sind-twitter-cards.html';
        }
        if ($type == 'facebook_instant_sharing') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=135&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=136&artlang=de';
        }
        if ($type == 'xing_business') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=146&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=11&id=122&artlang=de';
        }
        if ($type == 'auto_post_manuell') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=72&artlang=en' : 'https://www.blog2social.com/de/faq/content/3/79/de/wie-kann-ich-meine-blogbeitraege-automatisiert-und-zeitgesteuert-auf-social-media-planen-social-media-auto_poster.html';
        }
        if ($type == 'auto_post_import') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&lang=en&cat=3&id=116&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=116&artlang=de';
        }
        if ($type == 'url_parameter') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=6&id=164&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=6&id=160&artlang=de';
        }
        if ($type == 'network_mandant') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=65&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=73&artlang=de';
        }
        if ($type == 'network_mandant_collection') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=65&artlang=en&highlight=collection' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=73&artlang=de&highlight=Netzwerkgruppierungen';
        }
        if ($type == 're_post') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=165&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=162&artlang=de';
        }
        if ($type == 'fb_page_auth') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=124&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=124&artlang=de';
        }
        if ($type == 'fb_group_auth') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=82&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=86&artlang=de';
        }
        if ($type == 'network_grouping') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=65&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=73&artlang=de';
        }
        if ($type == 'community') {
            return 'https://community.blog2social.com/';
        }
        if ($type == 'community_lostpw') {
            return 'https://community.blog2social.com/lostpw';
        }
        if ($type == 'license_key') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1062' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1069';
        }
        if ($type == 'auto_post_troubleshoot') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1187' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=186&artlang=de';
        }
        if ($type == 'auto_post_import_troubleshoot') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1188' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=186&artlang=de';
        }
        if ($type == 'auto_post_assign') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=72&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=79&artlang=de';
        }
        if ($type == 'xing_company_page_old_design') {
            return ($lang == 'en') ? 'https://community.xing.com/de/s/article/Ihr-Arbeitgeberprofil-im-neuen-Gewand-Steigen-Sie-jetzt-um' : 'https://community.xing.com/de/s/article/Ihr-Arbeitgeberprofil-im-neuen-Gewand-Steigen-Sie-jetzt-um';
        }
        if ($type == 'pinterest_shortener') {
            return ($lang == 'en') ? 'https://help.pinterest.com/en/article/fix-a-broken-link' : 'https://help.pinterest.com/de/article/fix-a-broken-link';
        }
        if ($type == 'content_error') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=182&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=176&artlang=de';
        }
        if ($type == 'troubleshoot_auth') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1181' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1175';
        }
        if ($type == 'debugger_support') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=148&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=147&artlang=de';
        }
        if ($type == 'troubleshooting_tool_support') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=147&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=146&artlang=de';
        }
        if ($type == 'cc_info_faq') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=161&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=158&artlang=de';
        }
        if ($type == 'allow_shortcodes') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=90&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=100&artlang=de';
        }
        if ($type == 'besttimes_blogpost') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/blog/best-times-to-post-on-social-media/' : 'https://www.blog2social.com/de/blog/infografik-die-besten-zeiten-fuer-social-media-beitraege/';
        }
        if ($type == 'besttimes_faq') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=5&id=32&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=5&id=43&artlang=de';
        }
        if ($type == 'cc_text_post_info') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=161&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=158&artlang=de';
        }
        if ($type == 'template_faq') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=152&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=150&artlang=de';
        }
        if ($type == 'instagram_auth_faq') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=19&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=22&artlang=de';
        }
        if ($type == 'instagram_business_auth_faq') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=183&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=180&artlang=de';
        }
        if ($type == 'url_shortener_faq') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=40&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=37&artlang=de';
        }
        if ($type == 'network_addon_faq') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=7&id=168&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=7&id=165&artlang=de';
        }
        if ($type == 'connection_guide') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=106&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=108&artlang=de';
        }
        if ($type == 'instagram_error_private') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=119&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=119&artlang=de';
        }
        if ($type == 'instagram_error_business') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=119&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=119&artlang=de';
        }
        if ($type == 'network_guide_link_1') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1175' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1174';
        }
        if ($type == 'network_guide_link_45') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1177' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1177';
        }
        if ($type == 'network_guide_link_3') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/?action=search&search=linkedin' : 'https://www.blog2social.com/de/faq/?action=search&search=linkedin';
        }
        if ($type == 'network_guide_link_4') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/2/16/en/how-do-i-connect-blog2social-with-tumblr.html' : 'https://www.blog2social.com/de/faq/content/2/19/de/wie-kann-ich-blog2social-mit-tumblr-verbinden.html';
        }
        if ($type == 'network_guide_link_6') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1178' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1179';
        }
        if ($type == 'network_guide_link_7') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/2/21/en/how-do-i-connect-blog2social-with-flickr.html' : 'https://www.blog2social.com/de/faq/content/2/24/de/wie-kann-ich-blog2social-mit-flickr-verbinden.html';
        }
        if ($type == 'network_guide_link_9') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/2/22/en/how-do-i-connect-blog2social-with-diigo.html' : 'https://www.blog2social.com/de/faq/content/2/25/de/wie-kann-ich-blog2social-mit-diigo-verbinden.html';
        }
        if ($type == 'network_guide_link_11') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/?action=search&search=medium' : 'https://www.blog2social.com/de/faq/?action=search&search=medium';
        }
        if ($type == 'network_guide_link_12') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1176' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1173';
        }
        if ($type == 'network_guide_link_14') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/2/18/en/how-do-i-connect-blog2social-with-torial.html' : 'https://www.blog2social.com/de/faq/content/2/21/de/wie-kann-ich-blog2social-mit-torial-verbinden.html';
        }
        if ($type == 'network_guide_link_15') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/2/81/en/how-do-i-connect-blog2social-with-reddit.html' : 'https://www.blog2social.com/de/faq/content/2/85/de/wie-kann-ich-blog2social-mit-reddit-verbinden.html';
        }
        if ($type == 'network_guide_link_16') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/2/110/en/how-to-automatically-re_publish-blog-posts-on-bloglovin%E2%80%99-.html' : 'https://www.blog2social.com/de/faq/content/2/113/de/blogbeitraege-auf-bloglovin%E2%80%99-veroeffentlichen-_-so-geht%E2%80%99s.html';
        }
        if ($type == 'network_guide_link_17') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=122&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=123&artlang=de';
        }
        if ($type == 'network_guide_link_18') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/?action=search&search=google+my+business' : 'https://www.blog2social.com/de/faq/?action=search&search=google+my+business';
        }
        if ($type == 'network_guide_link_19') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=146&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=16&artlang=de';
        }
        if ($type == 'network_guide_link_24') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=173&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=169&artlang=de';
        }
        if ($type == 'network_guide_link_25') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=194&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=190&artlang=de&highlight=blogger';
        }
        if ($type == 'network_guide_link_26') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=196&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=192&artlang=de';
        }
        if ($type == 'network_guide_link_27') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=197&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=193&artlang=de';
        }
        if ($type == 'network_guide_link_38') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1207' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1204';
        }
        if ($type == 'network_guide_link_39') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1208' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1205';
        }
        if ($type == 'network_guide_link_43') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1254' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1249';
        }
        if ($type == 'network_guide_link_44') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1251' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1246';
        }
        if ($type == 'network_guide_link_46') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1255' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1250';
        }
        if ($type == 'TOKEN') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1181' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1175';
        }
        if ($type == 'IMAGE') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1144' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1143';
        }
        if ($type == 'IMAGE_FOR_CURATION') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1144' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1143';
        }
        if ($type == 'IMAGE_NETWORK') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1144' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1143';
        }
        if ($type == 'NETWORK_12_NO_PERMISSION') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1195' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1194';
        }
        if ($type == 'NETWORK_12_ACCESS_RESTRICTED') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1195' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1194';
        }
        if ($type == 'NETWORK_12_SESSION_INVALID') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1181' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1175';
        }
        if ($type == 'NETWORK_12_RESOURCE_DOSE_NOT_EXIST') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1198' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1195';
        }
        if ($type == 'NETWORK_12_NOT_BUSINESS') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?solution_id=1185' : 'https://www.blog2social.com/de/faq/index.php?solution_id=1182';
        }
        if ($type == 'metrics_feedback') {
            return ($lang == 'de') ? 'https://docs.google.com/forms/d/e/1FAIpQLSeif2AifR7lbSwYchCg08HysfgLuhMCtktf1qrE75UVxJlpUQ/viewform?usp=sf_link' : 'https://docs.google.com/forms/d/e/1FAIpQLSetoOeysUKSKK15ZgbvOVIfTovM67MNzPyncL7n6OvEwlZp0A/viewform?usp=sf_link';
        }
        if ($type == 'video_upload_feedback') {
            return ($lang == 'de') ? 'https://docs.google.com/forms/d/e/1FAIpQLSdJu2p-GUgwcSBkylLu8ASEn9revOCXcW-18T7w0eGF8na55g/viewform' : 'https://docs.google.com/forms/d/e/1FAIpQLSfE6LTVmo6wkBSP7wMTVsk_GERhEm4MbnfQ9ohcl6CetlCyow/viewform';
        }
    
        if ($type == 'b2s_license_advice') {
            return ($lang == 'de') ? 'https://service.blog2social.com/de/question?o=faq' : 'https://service.blog2social.com/en/question?o=faq';
        }
        if ($type == 'b2s_reviews') {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/blog/testberichte/' : 'https://www.blog2social.com/en/blog/reviews/';
        }
        if ($type == 'autopost_checklist_wp') {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=79' : 'https://www.blog2social.com/en/faq/index.php?solution_id=1071';
        }
        if ($type == 'autopost_checklist_rss') {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=116' : 'https://www.blog2social.com/en/faq/index.php?solution_id=1115';
        }
        if ($type == 'yoast_warning_og_guide') {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/index.php?action=artikel&lang=de&cat=9&id=184&artlang=de' : 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=189&artlang=en';
        }
        if ($type == 'twitter_card_guide') {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=109&artlang=de' : 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=109&artlang=en';
        }
        if ($type == "twitter_faq") {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/index.php?solution_id=1007' : 'https://www.blog2social.com/en/faq/index.php?solution_id=1145';
        }
        if ($type == "pinterest_faq") {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/index.php?solution_id=1022' : 'https://www.blog2social.com/en/faq/index.php?solution_id=1019';
        }
        if ($type == "post_templates") {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/content/4/150/de/wie-kann-ich-die-beitragsvorlagen-fuer-meine-social_media_posts-nutzen.html?highlight=beitragsvorlagen' : 'https://www.blog2social.com/en/faq/content/4/152/en/how-to-use-post-templates-for-social-media-posts.html';
        }
        if($type == "post_templates_without_highlight") {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/content/4/150/de/wie-kann-ich-die-beitragsvorlagen-fuer-meine-social_media_posts-nutzen.html' : 'https://www.blog2social.com/en/faq/content/4/152/en/how-to-use-post-templates-for-social-media-posts.html';
        }
        if ($type == "addon_apps") {
            return 'https://service.blog2social.com/login?redirectUrl=/checkout?mode=addon&type=network_app&token=' . B2S_PLUGIN_TOKEN;
        }
        if ($type == "addon_post_volume") { 
            return 'https://service.blog2social.com/login?redirectUrl=/checkout?mode=addon&type=post_limit_yearly&token=' . B2S_PLUGIN_TOKEN;
        }
        if ($type == "addon_network_integration") {
            return 'https://service.blog2social.com/login?redirectUrl=/checkout?mode=addon&type=network_integration&token=' . B2S_PLUGIN_TOKEN;
        }
        if ($type == "addon_video") {
            return 'https://service.blog2social.com/login?redirectUrl=/checkout?mode=addon&type=video&token=' . B2S_PLUGIN_TOKEN;
        }
        if ($type == "addon_social_account") {
            return 'https://service.blog2social.com/login?redirectUrl=/checkout?mode=addon&type=network&token=' . B2S_PLUGIN_TOKEN;
        }
        if ($type == "addon_telegram") {
            return 'https://service.blog2social.com/login?redirectUrl=/checkout?mode=addon&type=network&token=' . B2S_PLUGIN_TOKEN;
        }
        if ($type == "addon_user_licence") {
            return 'https://service.blog2social.com/login?redirectUrl=/checkout?mode=addon&type=user&token=' . B2S_PLUGIN_TOKEN;
        }
        if ($type == "twitter_threads") {
            return ($lang == "de") ? 'https://www.blog2social.com/de/faq/index.php?solution_id=1149' : 'https://www.blog2social.com/en/faq/index.php?solution_id=1152';
        }
        if ($type == "custom_permalinks") {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/index.php?solution_id=1206' : 'https://www.blog2social.com/en/faq/index.php?solution_id=1209';
        }
    
        if($type == 'pricing_addon') {
            return ($lang == 'de') ? 'https://de.blog2social.com/preise/#addons' : 'https://en.blog2social.com/pricing/#addons';
        }
        if ($type == 'tiktok_music_confirmation') {
            return 'https://www.tiktok.com/legal/page/global/music-usage-confirmation/en';
        }
        if ($type == 'tiktok_branded_confirmation') {
            return 'https://www.tiktok.com/legal/page/global/bc-policy/en';
        }
        if ($type == 'assistini_website') {
            return ($lang == 'de') ? 'https://assistini.com/de/' : 'https://assistini.com/en/';
        }

        if($type == "faq_ai_templates") {
            return ($lang == 'de') ? 'https://www.blog2social.com/de/faq/index.php?solution_id=1276' : 'https://www.blog2social.com/en/faq/index.php?solution_id=1282';
        }
        return false;
    }

    public static function getAffiliateId() {
        return (defined("B2S_PLUGIN_AFFILIATE_ID")) ? B2S_PLUGIN_AFFILIATE_ID : 0;
    }

    public static function getTokenById($user_id = 0) {
        if ($user_id == 0) {
            $user_id = get_current_user_id();
        }
        $user = get_user_by('id', $user_id);
        global $wpdb;
        $userExist = $wpdb->get_row($wpdb->prepare("SELECT token FROM `{$wpdb->prefix}b2s_user` WHERE `blog_user_id` = %d", $user->data->ID));
        if (empty($userExist) || !isset($userExist->token)) {
            $postData = array('action' => 'getToken', 'blog_user_id' => $user->data->ID, 'blog_url' => get_option('home'), 'email' => $user->data->user_email, 'is_multisite' => is_multisite());
            $result = json_decode(B2S_Tools::getToken($postData));
            if (isset($result->result) && (int) $result->result == 1 && isset($result->token)) {
                $state_url = (isset($result->state_url)) ? (int) $result->state_url : 0;
                $wpdb->query($wpdb->prepare("INSERT INTO `{$wpdb->prefix}b2s_user` (`token`, `blog_user_id`,`register_date`,`state_url`) VALUES (%s,%d,%s,%d);", $result->token, (int) $user->data->ID, wp_date('Y-m-d H:i:s', null, new DateTimeZone(date_default_timezone_get())), $state_url));
                return $result->token;
            } else {
                return false;
            }
        } else {
            return $userExist->token;
        }
    }

    public static function searchUser($search = "", $selectId = 0) {
        $getUser = new WP_User_Query(array(
            'search' => '*' . esc_attr($search) . '*',
            'search_columns' => array(
                'display_name',
            ),
        ));
        $userResult = $getUser->get_results();
        $options = '<option value="0"></option>';
        if (!empty($userResult) && is_array($userResult)) {
            $b2sVersionType = unserialize(B2S_PLUGIN_VERSION_TYPE);
            foreach ($userResult as $k => $user) {
                if (isset($user->data->ID) && isset($user->data->display_name) && isset($user->data->user_email)) {
                    $userDetails = get_option('B2S_PLUGIN_USER_VERSION_' . $user->data->ID);
                    $ver = "";
                    if (isset($userDetails['B2S_PLUGIN_USER_VERSION']) && (int) $userDetails['B2S_PLUGIN_USER_VERSION'] > 0) {
                        $userVersion = $userDetails['B2S_PLUGIN_USER_VERSION'];
                        if (is_array($b2sVersionType) && isset($b2sVersionType[$userVersion]) && !empty($b2sVersionType[$userVersion])) {
                            $ver = ", " . esc_html__('Current license', 'blog2social') . ": " . esc_html($b2sVersionType[$userVersion]);
                        }
                    }
                    $options .= '<option value="' . esc_attr($user->data->ID) . '" ' . (($user->data->ID == $selectId) ? "selected" : "") . '>' . esc_html($user->data->display_name) . " (" . esc_html__('Email', 'blog2social') . ': ' . esc_html($user->data->user_email) . $ver . ')</option>';
                }
            }
        }
        return $options;
    }

    public static function getCountryListByNetwork($networkId = 6) {
        $countryList = array();
        if ($networkId == 6) { //Pinterest
            $countryList = array(
                'el' => array('name' => esc_html__('Greece', 'blog2social'), 'url' => 'https://gr.pinterest.com/'),
                'en-IN' => array('name' => esc_html__('India', 'blog2social'), 'url' => 'https://in.pinterest.com/'),
                'en' => array('name' => esc_html__('United States of America', 'blog2social'), 'url' => 'https://www.pinterest.com/'),
                'en-IE' => array('name' => esc_html__('Ireland', 'blog2social'), 'url' => 'https://www.pinterest.ie/'),
                'it' => array('name' => esc_html__('Italy', 'blog2social'), 'url' => 'https://www.pinterest.it/'),
                'en-CH' => array('name' => esc_html__('Switzerland', 'blog2social'), 'url' => 'https://www.pinterest.ch/'),
                'cs' => array('name' => esc_html__('Czechoslovakia', 'blog2social'), 'url' => 'https://cz.pinterest.com/'),
                'id' => array('name' => esc_html__('Indonesia', 'blog2social'), 'url' => 'https://id.pinterest.com/'),
                'es' => array('name' => esc_html__('Spain', 'blog2social'), 'url' => 'https://www.pinterest.es/'),
                'en-CA' => array('name' => esc_html__('Canada', 'blog2social'), 'url' => 'https://www.pinterest.ca/'),
                'en-GB' => array('name' => esc_html__('Great Britain', 'blog2social'), 'url' => 'https://www.pinterest.co.uk/'),
                'ru' => array('name' => esc_html__('Russia', 'blog2social'), 'url' => 'https://www.pinterest.ru/'),
                'nl' => array('name' => esc_html__('Netherlands', 'blog2social'), 'url' => 'https://nl.pinterest.com/'),
                'pt' => array('name' => esc_html__('Portugal', 'blog2social'), 'url' => 'https://br.pinterest.com/'),
                'no' => array('name' => esc_html__('Norway', 'blog2social'), 'url' => 'https://no.pinterest.com/'),
                'tr' => array('name' => esc_html__('Turkey', 'blog2social'), 'url' => 'https://tr.pinterest.com/'),
                'en-AU' => array('name' => esc_html__('Australia', 'blog2social'), 'url' => 'https://www.pinterest.com.au/'),
                'de-AT' => array('name' => esc_html__('Austria', 'blog2social'), 'url' => 'https://www.pinterest.at/'),
                'pl' => array('name' => esc_html__('Poland', 'blog2social'), 'url' => 'https://pl.pinterest.com/'),
                'fr' => array('name' => esc_html__('France', 'blog2social'), 'url' => 'https://www.pinterest.fr/'),
                'ro-RO' => array('name' => esc_html__('Romania', 'blog2social'), 'url' => 'https://ro.pinterest.com/'),
                'de' => array('name' => esc_html__('Germany', 'blog2social'), 'url' => 'https://www.pinterest.de/'),
                'da' => array('name' => esc_html__('Denmark', 'blog2social'), 'url' => 'https://www.pinterest.dk/'),
                'en-NZ' => array('name' => esc_html__('New Zealand', 'blog2social'), 'url' => 'https://www.pinterest.nz/'),
                'fi' => array('name' => esc_html__('Finland', 'blog2social'), 'url' => 'https://fi.pinterest.com/'),
                'hu' => array('name' => esc_html__('Hungary', 'blog2social'), 'url' => 'https://hu.pinterest.com/'),
                'ja' => array('name' => esc_html__('Japan', 'blog2social'), 'url' => 'https://www.pinterest.jp/'),
                'pt-PT' => array('name' => esc_html__('Portugal', 'blog2social'), 'url' => 'https://www.pinterest.pt/'),
                'es-AR' => array('name' => esc_html__('Argentina', 'blog2social'), 'url' => 'https://ar.pinterest.com/'),
                'ko' => array('name' => esc_html__('Korea', 'blog2social'), 'url' => 'https://www.pinterest.co.kr/'),
                'sv' => array('name' => esc_html__('Sweden', 'blog2social'), 'url' => 'https://www.pinterest.se/'),
                'es-MX' => array('name' => esc_html__('Mexico', 'blog2social'), 'url' => 'https://www.pinterest.com.mx/'),
                'sk' => array('name' => esc_html__('Slovakia', 'blog2social'), 'url' => 'https://sk.pinterest.com/'),
                'es-CL' => array('name' => esc_html__('Chile', 'blog2social'), 'url' => 'https://www.pinterest.cl/'),
                'es-CO' => array('name' => esc_html__('Colombia', 'blog2social'), 'url' => 'https://co.pinterest.com/'),
                'es-ZA' => array('name' => esc_html__('South Africa', 'blog2social'), 'url' => 'https://za.pinterest.com/'),
                'tl-PH' => array('name' => esc_html__('Philippines', 'blog2social'), 'url' => 'https://www.pinterest.ph/')
            );
            asort($countryList);
            $countryList = array_merge(array('' => array('name' => esc_html__('is determined automatically', 'blog2social'), 'url' => 'https://www.pinterest.com/')), $countryList);
        }
        return $countryList;
    }

    public static function getEmojiTranslationList() {
        return array(
            'search' => esc_html__('Search', 'blog2social'),
            'recents' => esc_html__('Recently Used', 'blog2social'),
            'smileys' => esc_html__('Smileys & People', 'blog2social'),
            'animals' => esc_html__('Animals & Nature', 'blog2social'),
            'food' => esc_html__('Food & Drink', 'blog2social'),
            'activities' => esc_html__('Activities', 'blog2social'),
            'travel' => esc_html__('Travel & Places', 'blog2social'),
            'objects' => esc_html__('Objects', 'blog2social'),
            'symbols' => esc_html__('Symbols', 'blog2social'),
            'flags' => esc_html__('Flags', 'blog2social'),
            'notFound' => esc_html__('No emojis found', 'blog2social')
        );
    }

    public static function getNoCacheData($blogUserId) {
        $default = array(
            1 => 0,
            3 => 1,
            19 => 1
        );
        if ((int) $blogUserId >= 1) {
            $changed = false;
            require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
            $options = new B2S_Options((int) $blogUserId);
            $linkNoCache = $options->_getOption("link_no_cache");
            if ($linkNoCache != false) {
                if (!is_array($linkNoCache)) {
                    $fb_linkNoCache = (((int) $linkNoCache > 0) ? 1 : 0);
                    $linkNoCache = $default;
                    $linkNoCache[1] = $fb_linkNoCache;
                    $changed = true;
                } else {
                    foreach ($default as $k => $v) {
                        if (!isset($linkNoCache[$k])) {
                            $linkNoCache[$k] = $v;
                            $changed = true;
                        }
                    }
                }
            } else {
                $linkNoCache = $default;
                $changed = true;
            }
            if ($changed) {
                $options->_setOption('link_no_cache', $linkNoCache);
            }
            return $linkNoCache;
        }
        return $default;
    }

    public static function extractKeywords($string) {
        $stopWords = array('i', 'a', 'about', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'com', 'de', 'en', 'for', 'from', 'how', 'in', 'is', 'it', 'la', 'of', 'on', 'or', 'that', 'the', 'this', 'to', 'was', 'what', 'when', 'where', 'who', 'will', 'with', 'und', 'the', 'www');

        $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
        $string = trim($string); // trim the string
        $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
        $string = strtolower($string); // make it lowercase

        preg_match_all('/\b.*?\b/i', $string, $matchWords);
        $matchWords = $matchWords[0];

        foreach ($matchWords as $key => $item) {
            if ($item == '' || in_array(strtolower($item), $stopWords) || strlen($item) <= 3) {
                unset($matchWords[$key]);
            }
        }
        $wordCountArr = array();
        if (is_array($matchWords)) {
            foreach ($matchWords as $key => $val) {
                $val = strtolower($val);
                $wordCountArr[] = $val;
            }
        }
        arsort($wordCountArr);
        $wordCountArr = array_slice($wordCountArr, 0, 10);
        return $wordCountArr;
    }

    public static function sanitize_array($array = array()) {
        if (is_array($array) && !empty($array)) {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    $value = self::sanitize_array($value);
                } else {
                    $value = sanitize_text_field($value);
                }
            }
        }
        return $array;
    }

    public static function sanitize_array_textarea($array = array()) {
        if (is_array($array) && !empty($array)) {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    $value = self::sanitize_array_textarea($value);
                } else {
                    $value = sanitize_textarea_field($value);
                }
            }
        }
        return $array;
    }

    public static function esc_html_array($array = array(), $kses = array()) {
        if (is_array($array) && !empty($array)) {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    $value = self::esc_html_array($value);
                } else {
                    $value = wp_kses($value, $kses);
                }
            }
        }
        return $array;
    }

    public static function hasUserMadePost($user_id) {

        global $wpdb;
        $posts = $wpdb->get_results($wpdb->prepare("SELECT id FROM {$wpdb->prefix}b2s_posts WHERE blog_user_id = %d", $user_id), ARRAY_A);
        if (isset($posts) && is_array($posts) && !empty($posts)) {
            return true;
        }
        return false;
    }

    public static function hasUserConnectedNetwork($user_id) {

        global $wpdb;
        $networks = $wpdb->get_results($wpdb->prepare("SELECT id FROM {$wpdb->prefix}b2s_posts_network_details WHERE owner_blog_user_id = %d", $user_id), ARRAY_A);
        if (isset($networks) && is_array($networks) && !empty($networks)) {
            return true;
        }
        return false;
    }

    public static function getPrePostDetails($client_user_network_id = 0) {
        $details = B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getPrePostDetails', 'token' => B2S_PLUGIN_TOKEN, 'plugin_version' => B2S_PLUGIN_VERSION, 'client_user_network_id' => $client_user_network_id));
        return $details;
    }

    public static function isCommentAllowed($networkId, $networkType) {

        if(!defined('B2S_PLUGIN_NETWORK_ALLOW_COMMENT')) {
            return false;
        }

        $allowComment = unserialize(B2S_PLUGIN_NETWORK_ALLOW_COMMENT);
        
        // If network ID is a key with an array value, only those types in the array are allowed
        if (isset($allowComment[$networkId]) && is_array($allowComment[$networkId])) {
            return in_array($networkType, $allowComment[$networkId]);
        }
        
        // If it's just a number in the array, all types are allowed
        if (in_array($networkId, $allowComment)) {
            return true;
        }
        
        return false;
    }

    public static function getAssistiniAllowEmoji($networkId) {
       $allowNoEmoji = array(9, 13, 14, 15, 16, 21, 35, 36, 37, 42); 
       return !in_array($networkId, $allowNoEmoji);
    }

    public static function getAssistiniAllowHashtags($networkId) {
        $allowHashtags = array(1, 2, 3, 6, 12, 17, 21, 37, 43, 45);
        return in_array($networkId, $allowHashtags);
    }

    public static function getAssistiniTemplateValues(){

        global $wpdb;

        $options = new B2S_Options((int) B2S_PLUGIN_BLOG_USER_ID);

        $storedTemplate = $options->_getOption('assistini_template_values');
        if (is_array($storedTemplate) && isset($storedTemplate['last_stored']) && isset($storedTemplate['values']) && defined('HOUR_IN_SECONDS') && (time() - (int) $storedTemplate['last_stored']) < HOUR_IN_SECONDS) {
            return $storedTemplate['values'];
        }

        if ($wpdb->get_var($wpdb->prepare("SELECT `id`, `access_token` FROM `{$wpdb->prefix}b2s_user_tool` WHERE `blog_user_id` = %d AND `tool_id` = 1", (int) B2S_PLUGIN_BLOG_USER_ID))) {
            $sqlResult = $wpdb->get_row($wpdb->prepare("SELECT `id`, `access_token` FROM `{$wpdb->prefix}b2s_user_tool` WHERE `blog_user_id` = %d AND `tool_id` = 1", (int) B2S_PLUGIN_BLOG_USER_ID));
            
            if (isset($sqlResult->id) && (int) $sqlResult->id > 0 && isset($sqlResult->access_token) && !empty($sqlResult->access_token)){

                $postData = array(
                    'action' => 'assGetTemplateValues', 
                    'access_token' => sanitize_text_field($sqlResult->access_token)
                );
        
                $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $postData), true);

                $storeTemplate= array(
                    'last_stored'=> time(),
                    'values' => $result
                );

                $options->_setOption('assistini_template_values', $storeTemplate);
                return $result;
            }
        }

        return false;
        
    }

    public static function getAssistiniPostGoal() {
        return array(
            'traffic' => __('Traffic', 'blog2social'),
            'awareness' => __('Awareness', 'blog2social'),
            'engagement' => __('Engagement', 'blog2social'),
            'conversion' => __('Conversion', 'blog2social'),
            'event' => __('Event', 'blog2social'),
            'recruiting' => __('Recruiting', 'blog2social'),
        );
    }

    public static function getAssistiniPointOfView() {
        return array(
            'neutral' => __('Neutral', 'blog2social'),
            '1st-person' => __('1st Person (I, We)', 'blog2social'),
            '2nd-person' => __('2nd Person (You, informal)', 'blog2social'),
            '2nd-person-she' => __('2nd Person (You, formal)', 'blog2social'),
            '3rd-person' => __('3rd Person (He, She, It, They)', 'blog2social')
        );
    }
    public static function getAssistiniLength() {
        return array(
            'short' => __('Short', 'blog2social'),
            'medium' => __('Medium', 'blog2social'),
            'long' => __('Long', 'blog2social')
        );
    }

    public static function getAssistiniAnswerInLanguage() {
        return array(
            'auto' => __('Auto', 'blog2social'),
            'en_GB' => __('English (United Kingdom)', 'blog2social'),
            'en_US' => __('English (United States)', 'blog2social'),
            'en_AU' => __('English (Australia)', 'blog2social'),
            'en_CA' => __('English (Canada)', 'blog2social'),
            'de_DE' => __('German', 'blog2social'),
            'de_AT' => __('German (Austria)', 'blog2social'),
            'de_CH' => __('German (Switzerland)', 'blog2social'),
            'fr_FR' => __('French', 'blog2social'),
            'fr_CA' => __('French (Canada)', 'blog2social'),
            'es_ES' => __('Spanish', 'blog2social'),
            'es_MX' => __('Spanish (Mexico)', 'blog2social'),
            'it_IT' => __('Italian', 'blog2social'),
            'pt_PT' => __('Portuguese', 'blog2social'),
            'pt_BR' => __('Portuguese (Brazil)', 'blog2social'),
            'zh_CN' => __('Chinese (Simplified)', 'blog2social'),
            'zh_TW' => __('Chinese (Traditional)', 'blog2social'),
            'ja_JP' => __('Japanese', 'blog2social'),
            'ko_KR' => __('Korean', 'blog2social'),
            'ru_RU' => __('Russian', 'blog2social'),
            'ar_SA' => __('Arabic', 'blog2social'),
            'ar_EG' => __('Arabic (Egypt)', 'blog2social'),
            'hi_IN' => __('Hindi', 'blog2social'),
            'nl_NL' => __('Dutch', 'blog2social'),
            'tr_TR' => __('Turkish', 'blog2social'),
            'pl_PL' => __('Polish', 'blog2social'),
            'sv_SE' => __('Swedish', 'blog2social'),
            'fi_FI' => __('Finnish', 'blog2social'),
            'da_DK' => __('Danish', 'blog2social'),
            'no_NO' => __('Norwegian Bokmål', 'blog2social'),
            'el_GR' => __('Greek', 'blog2social'),
            'cs_CZ' => __('Czech', 'blog2social'),
            'th_TH' => __('Thai', 'blog2social'),
            'he_IL' => __('Hebrew', 'blog2social'),
            'hu_HU' => __('Hungarian', 'blog2social'),
            'id_ID' => __('Indonesian', 'blog2social'),
            'ro_RO' => __('Romanian', 'blog2social'),
            'uk_UA' => __('Ukrainian', 'blog2social'),
            'vi_VN' => __('Vietnamese', 'blog2social'),
            'af_ZA' => __('Afrikaans', 'blog2social'),
            'az_AZ' => __('Azerbaijani', 'blog2social'),
            'be_BY' => __('Belarusian', 'blog2social'),
            'bn_BD' => __('Bengali', 'blog2social'),
            'bg_BG' => __('Bulgarian', 'blog2social'),
            'ca_ES' => __('Catalan', 'blog2social'),
            'et_EE' => __('Estonian', 'blog2social'),
            'is_IS' => __('Icelandic', 'blog2social'),
            'lv_LV' => __('Latvian', 'blog2social'),
            'lt_LT' => __('Lithuanian', 'blog2social'),
            'mk_MK' => __('Macedonian', 'blog2social'),
            'ms_MY' => __('Malay', 'blog2social'),
            'fa_IR' => __('Persian', 'blog2social'),
            'sk_SK' => __('Slovak', 'blog2social'),
            'sl_SI' => __('Slovenian', 'blog2social'),
            'cy_GB' => __('Welsh', 'blog2social'),
            'zu_ZA' => __('Zulu', 'blog2social'),
        );
    }
    

    public static function getAssistiniCtaType() {
        return array(
            'none' => __('None', 'blog2social'),
            'learn_more' => __('Learn more', 'blog2social'),
            'read_now' => __('Read now', 'blog2social'),
            'try_now' => __('Try now', 'blog2social'),
            'comment_question' => __('Comment question', 'blog2social'),
            'save_share' => __('Save or share', 'blog2social')
        );
    }

    public static function getAssistiniTextForm() {
        return array(
            'plain_text' => __('Plain Text', 'blog2social'),
            'bullet_points' => __('Bullet points', 'blog2social'),
            'short_paragraphs' => __('Short paragraphs', 'blog2social'),
            'question_at_end' => __('Question at the end', 'blog2social'),
            'hook_at_start' => __('Hook at the start', 'blog2social'),
            'storytelling' => __('Storytelling', 'blog2social'),
            'mini_cta_integrated' => __('Mini CTA integrated', 'blog2social'),
        );
    }

    public static function getAssistiniFormOfAddress() {
        return array(
            'you_informal' => __('You (informal)', 'blog2social'),
            'you_formal' => __('You (formal)', 'blog2social'),
            'no_direct_address' => __('No direct address', 'blog2social')
        );
    }

    public static function getAssistiniEmojis() {
        return array(
            'none' => __('None', 'blog2social'),
            'subtle' => __('Subtle', 'blog2social'),
            'normal' => __('Normal', 'blog2social')
        );
    }

    public static function getAssistiniWritingStyle() {
        return array(
            'neutral' => __('Neutral', 'blog2social'),
            'casual' => __('Casual', 'blog2social'),
            'professional' => __('Professional', 'blog2social'),
            'marketing_driven' => __('Marketing-driven', 'blog2social'),
            'editorial' => __('Editorial', 'blog2social')
        );
    }

    public static function getAssistiniGenerateHashtags() {
        return array(
            'none' => __('No hashtags', 'blog2social'),
            'from_ai' => __('From AI', 'blog2social')
        );
    }

    public static function getAssistiniTone() {
        return array(
            'neutral' => __('Neutral', 'blog2social'),
            'friendly' => __('Friendly', 'blog2social'),
            'helpful' => __('Helpful', 'blog2social'),
            'informative' => __('Informative', 'blog2social'),
            'aggressive' => __('Aggressive', 'blog2social'),
            'professional' => __('Professional', 'blog2social'),
            'formal' => __('Formal', 'blog2social'),
            'informal' => __('Informal', 'blog2social'),
            'conversational' => __('Conversational', 'blog2social'),
            'persuasive' => __('Persuasive', 'blog2social'),
            'witty' => __('Witty', 'blog2social'),
            'descriptive' => __('Descriptive', 'blog2social'),
            'expository' => __('Expository', 'blog2social'),
            'humorous' => __('Humorous', 'blog2social'),
            'inspirational' => __('Inspirational', 'blog2social'),
            'funny' => __('Funny', 'blog2social'),
            'poetic' => __('Poetic', 'blog2social'),
            'technical' => __('Technical', 'blog2social'),
            'argumentative' => __('Argumentative', 'blog2social'),
            'instructional' => __('Instructional', 'blog2social'),
            'sarcastic' => __('Sarcastic', 'blog2social'),
            'urgent' => __('Urgent', 'blog2social'),
            'optimistic' => __('Optimistic', 'blog2social')
        );
    }

    public static function getAiTemplateDefaults() {
        return array(
            'enabled' => 0,
            'content_goal_enabled' => 1,
            'tone_language_enabled' => 1,
            'hashtags_keywords_enabled' => 1,
            'content_length_output_enabled' => 1,
            'answer_in_language' => 'auto',
            'ai_instruction' => '',
            'post_goal' => 'traffic',
            'cta_type' => 'none',
            'tone' => 'neutral',
            'content_focus' => 50,
            'point_of_view' => 'neutral',
            'text_form' => 'plain_text',
            'form_of_address' => 'no_direct_address',
            'emojis' => 'normal',
            'writing_style' => 'neutral',
            'generate_hashtags' => 'from_ai',
            'hashtags_count' => 1,
            'use_keywords' => '',
            'keyword_strength' => 50,
            'text_length' => 'medium',
            'text_depth' => 50
        );
    }

    public static function getAiTemplateSchema($schema, $networkType, $networkId = 0) {
        $defaults = self::getAiTemplateDefaults();
        if (!isset($schema[$networkType]['ai_template']) || !is_array($schema[$networkType]['ai_template'])) {
            return $defaults;
        }

        return self::normalizeAiTemplateSettings($schema[$networkType]['ai_template'], $defaults, (int) $networkId, $networkType);
    }

    public static function normalizeAiTemplateSettings($template, $defaults = array(), $networkId = 0, $networkType = null) {
        if (!is_array($template)) {
            return is_array($defaults) && !empty($defaults) ? $defaults : self::getAiTemplateDefaults();
        }

        $normalized = array_merge((is_array($defaults) && !empty($defaults) ? $defaults : self::getAiTemplateDefaults()), $template);

        $answerLanguageOptions = array_keys(self::getAssistiniAnswerInLanguage());
        if (!isset($normalized['answer_in_language']) || !in_array($normalized['answer_in_language'], $answerLanguageOptions, true)) {
            $normalized['answer_in_language'] = 'auto';
        }

        $normalized['content_focus'] = max(1, min(100, (int) $normalized['content_focus']));
        $normalized['keyword_strength'] = max(1, min(100, (int) $normalized['keyword_strength']));
        $normalized['text_depth'] = max(1, min(100, (int) $normalized['text_depth']));
        $normalized['hashtags_count'] = max(0, (int) $normalized['hashtags_count']);
        $normalized['content_goal_enabled'] = ((isset($normalized['content_goal_enabled']) && (int) $normalized['content_goal_enabled'] === 0) ? 0 : 1);
        $normalized['tone_language_enabled'] = ((isset($normalized['tone_language_enabled']) && (int) $normalized['tone_language_enabled'] === 0) ? 0 : 1);
        $normalized['hashtags_keywords_enabled'] = ((isset($normalized['hashtags_keywords_enabled']) && (int) $normalized['hashtags_keywords_enabled'] === 0) ? 0 : 1);
        $normalized['content_length_output_enabled'] = ((isset($normalized['content_length_output_enabled']) && (int) $normalized['content_length_output_enabled'] === 0) ? 0 : 1);

        if ((int) $networkId > 0) {
            $defaultHashtagLimit = self::getAssistiniTemplateDefaultMaxKeywords();
            $resolvedFromValues = false;
            if ($networkType !== null) {
                $assistiniValues = self::getAssistiniTemplateValues();
                if (is_array($assistiniValues) && isset($assistiniValues['data']['networks'][$networkId]['network_type'][$networkType])) {
                    $networkTemplateValues = $assistiniValues['data']['networks'][$networkId]['network_type'][$networkType];
                    $resolvedFromValues = true;
                    if (!(isset($networkTemplateValues['allow_emojis']) && (int) $networkTemplateValues['allow_emojis'] === 1)) {
                        $normalized['emojis'] = 'none';
                    }
                    if (isset($networkTemplateValues['hashtags']) && (int) $networkTemplateValues['hashtags'] === 1) {
                        $hashTagLimit = isset($networkTemplateValues['hashtag_limit']) && (int) $networkTemplateValues['hashtag_limit'] > 0 ? (int) $networkTemplateValues['hashtag_limit'] : $defaultHashtagLimit;
                        $normalized['hashtags_count'] = max(0, min($hashTagLimit, (int) $normalized['hashtags_count']));
                    } else {
                        $normalized['generate_hashtags'] = 'none';
                        $normalized['hashtags_count'] = 0;
                    }
                }
            }
            //Backup Values if request failed
            if (!$resolvedFromValues) {
                if (!self::getAssistiniAllowEmoji((int) $networkId)) {
                    $normalized['emojis'] = 'none';
                }
                if (!self::getAssistiniAllowHashtags((int) $networkId)) {
                    $normalized['generate_hashtags'] = 'none';
                    $normalized['hashtags_count'] = 0;
                } else {
                    $normalized['hashtags_count'] = max(0, min($defaultHashtagLimit, (int) $normalized['hashtags_count']));
                }
            }
        }

        return $normalized;
    }

    public static function getAiTemplateDbSchema($blogUserId, $networkId, $typeId) {
        if ((int) $blogUserId <= 0 || (int) $networkId <= 0 || (int) $typeId < 0) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'b2s_ai_template';
        if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) != $table) {
            return false;
        }

        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT `payload` FROM `{$table}` WHERE `blog_user_id` = %d AND `network_id` = %d AND `type_id` = %d LIMIT 1",
            (int) $blogUserId,
            (int) $networkId,
            (int) $typeId
        ));

        if (!is_object($row) || !isset($row->payload) || empty($row->payload)) {
            return false;
        }

        $payload = json_decode((string) $row->payload, true);
        if (!is_array($payload) || empty($payload)) {
            return false;
        }

        return self::normalizeAiTemplateSettings($payload, self::getAiTemplateDefaults(), (int) $networkId, (int) $typeId);
    }

    public static function filterAiTemplateDataToSend($template) {
        
        if (!is_array($template) || empty($template)) {
            return array();
        }

        $filtered = $template;
        $groupFieldMap = array(
            'content_goal_enabled' => array('post_goal', 'cta_type', 'tone', 'content_focus', 'point_of_view'),
            'tone_language_enabled' => array('text_form', 'form_of_address', 'emojis', 'writing_style'),
            'hashtags_keywords_enabled' => array('generate_hashtags', 'hashtags_count', 'use_keywords', 'keyword_strength'),
            'content_length_output_enabled' => array('text_length', 'text_depth')
        );

        foreach ($groupFieldMap as $groupToggle => $groupFields) {
            if (isset($filtered[$groupToggle]) && (int) $filtered[$groupToggle] === 0) {
                foreach ($groupFields as $fieldKey) {
                    unset($filtered[$fieldKey]);
                }
            }
        }

        return $filtered;
    }

    public static function getAssistiniTemplateDefaultMaxKeywords() {
        return 30;
    }

    public static function getAiTemplateMaxPromptCharacters() {
        return 1000;
    }
}
