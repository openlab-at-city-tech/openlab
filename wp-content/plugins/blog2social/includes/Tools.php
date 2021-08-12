<?php

class B2S_Tools {

    public static function showNotice() {
        return (defined("B2S_PLUGIN_NOTICE") || !defined("B2S_PLUGIN_TOKEN")) ? true : false;
    }

    public static function getToken($data = array()) {
        return B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $data, 30);
    }

    public static function setUserDetails() {
        if (defined("B2S_PLUGIN_TOKEN")) {
            delete_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
            delete_option('B2S_PLUGIN_PRIVACY_POLICY_USER_ACCEPT_' . B2S_PLUGIN_BLOG_USER_ID);
            $version = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getUserDetails', 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION), 30));
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
        $sql = "SELECT token,state_url FROM {$wpdb->prefix}b2s_user WHERE blog_user_id = %d";
        $result = $wpdb->get_results($wpdb->prepare($sql, B2S_PLUGIN_BLOG_USER_ID));
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
                        $endProfile = date("H:i", strtotime('-30 minutes', strtotime($endProfile . ':00')));   //-30min
                    }
                    if ($getTimeForGroup) {
                        $endProfile = date("H:i", strtotime('-30 minutes', strtotime($endProfile . ':00')));   //-30min
                    }
                    $endProfile = (strpos($endProfile, ':') === false) ? $endProfile . ':00' : $endProfile;
                    $startProfle = (strpos($v[0], ':') === false) ? $v[0] . ':00' : $v[0];
                    $dateTime = date('Y-m-d ' . B2S_Util::getRandomTime($startProfle, $endProfile) . ':00');
                    //Profile
                    $userTimes[$k][0] = date($slug, strtotime($dateTime));
                    //Page
                    $dateTime = ($getTimeForPage) ? strtotime('+30 minutes', strtotime($dateTime)) : strtotime($dateTime);
                    $userTimes[$k][1] = ($getTimeForPage) ? date($slug, $dateTime) : "";
                    //Group
                    $dateTime = strtotime('+30 minutes', $dateTime);
                    $userTimes[$k][2] = ($getTimeForGroup) ? date($slug, $dateTime) : "";
                }
            }
        }
        return $userTimes;
    }

    public static function getSupportLink($type = 'howto') {
        $lang = substr(B2S_LANGUAGE, 0, 2);
        if ($type == 'howto') {
            return 'https://blog2social.com/docs/' . (($lang == 'en') ? 'blog2social-guide-step-by-step-en.pdf' : 'step-by-step-guide-zu-blog2social.pdf');
        }
        if ($type == 'faq') {
            return 'https://service.blog2social.com/support?url=' . get_option('home') . '&token=' . B2S_PLUGIN_TOKEN;
        }
        if ($type == 'faq_direct') {
            return 'https://www.blog2social.com/' . (($lang == 'en') ? 'en' : 'de') . "/faq/";
        }
        if ($type == 'affiliate') {
            $affiliateId = self::getAffiliateId();
            return 'https://service.blog2social.com/' . (((int) $affiliateId != 0) ? '?aid=' . $affiliateId : '');
        }
        if ($type == 'feature') {
            return 'https://blog2social.com/' . (($lang == 'en') ? 'en/plugin/wordpress/premium-trial/' : 'de/plugin/wordpress/premium-testen/');
        }
        if ($type == 'trial') {
            return 'https://service.blog2social.com/' . (($lang == 'en') ? 'en/trial' : 'de/trial');
        }
        if ($type == 'contact') {
            return 'https://service.blog2social.com/' . (($lang == 'en') ? 'en/trial' : 'de/trial');
        }
        if ($type == 'term') {
            return 'https://www.blog2social.com/' . (($lang == 'en') ? 'en/terms' : 'de/agb');
        }
        if ($type == 'privacy_policy') {
            return 'https://www.blog2social.com/' . (($lang == 'en') ? 'en/privacy-policy' : 'de/datenschutz');
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
        if ($type == 'faq_licence') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=7' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=7';
        }
        if ($type == 'faq_troubleshooting') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=9' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=9';
        }
        if ($type == 'faq_settings') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=show&cat=11' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=11';
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
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/category/9/troubleshooting-for-error-messages.html' : 'https://www.blog2social.com/de/faq/index.php?action=show&cat=9';
        }
        if ($type == 'instagram_without_text') {
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=154&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=152&artlang=de';
        }
        if($type == 'auto_poster_m'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=72&artlang=en' : 'https://www.blog2social.com/de/faq/content/3/79/de/wie-kann-ich-meine-blogbeitraege-automatisiert-und-zeitgesteuert-auf-social-media-planen-social-media-auto_poster.html';
        }
        if($type == 'auto_poster_a'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=116&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=116&artlang=de';
        }
        if($type == 'open_graph_tags'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=103&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=106&artlang=de';
        }
        if($type == 'twitter_cards'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/content/4/109/en/what-are-twitter-cards.html' : 'https://www.blog2social.com/de/faq/content/4/109/de/was-sind-twitter-cards.html';
        }
        if($type == 'facebook_instant_sharing'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=135&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=136&artlang=de';
        }    
        if($type == 'xing_business'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=146&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=11&id=122&artlang=de';
        }
        if($type == 'auto_post_manuell'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=72&artlang=en' : 'https://www.blog2social.com/de/faq/content/3/79/de/wie-kann-ich-meine-blogbeitraege-automatisiert-und-zeitgesteuert-auf-social-media-planen-social-media-auto_poster.html';
        }
        if($type == 'auto_post_import'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&lang=en&cat=3&id=116&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=116&artlang=de';
        }
        if($type == 'url_parameter'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=6&id=164&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=6&id=160&artlang=de';
        }
        if($type == 'network_mandant'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=65&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=73&artlang=de';
        }
        if($type == 'network_mandant_collection'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=65&artlang=en&highlight=collection' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=73&artlang=de&highlight=Netzwerkgruppierungen';
        }
        if($type == 're_post'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=165&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=162&artlang=de';
        }
        if($type == 'fb_page_auth'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=124&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=124&artlang=de';
        }
        if($type == 'fb_group_auth'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=82&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=86&artlang=de';
        }
        if($type == 'network_grouping'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=65&artlang=en&highlight=profile' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=73&artlang=de&highlight=profil';
        }
        if($type == 'community'){
            return 'https://community.blog2social.com/';
        }
        if($type == 'community_lostpw'){
            return 'https://community.blog2social.com/lostpw';
        }
        if($type == 'auto_post_assign'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=72&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=79&artlang=de';
        }
        if($type == 'xing_company_page_old_design'){
            return ($lang == 'en') ? 'https://community.xing.com/de/s/article/Ihr-Arbeitgeberprofil-im-neuen-Gewand-Steigen-Sie-jetzt-um' : 'https://community.xing.com/de/s/article/Ihr-Arbeitgeberprofil-im-neuen-Gewand-Steigen-Sie-jetzt-um';
        }
        if($type == 'pinterest_shortener'){
            return ($lang == 'en') ? 'https://help.pinterest.com/en/article/fix-a-broken-link' : 'https://help.pinterest.com/de/article/fix-a-broken-link';
        }
        if($type == 'content_error'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=182&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=176&artlang=de';
        }
        if($type == 'troubleshoot_auth'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=119&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=119&artlang=de';
        }
        if($type == 'debugger_support'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=148&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=147&artlang=de';
        }
        if($type == 'troubleshooting_tool_support'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=9&id=147&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=9&id=146&artlang=de';
        }
        if($type == 'cc_info_faq'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=161&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=158&artlang=de';
        }
        if($type == 'allow_shortcodes'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=90&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=100&artlang=de';
        }
        if($type == 'besttimes_blogpost'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/blog/best-times-to-post-on-social-media/' : 'https://www.blog2social.com/de/blog/infografik-die-besten-zeiten-fuer-social-media-beitraege/';
        }
        if($type == 'besttimes_faq'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=5&id=32&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=5&id=43&artlang=de';
        }
        if($type == 'cc_text_post_info'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=3&id=161&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=3&id=158&artlang=de';
        }
        if($type == 'template_faq'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=152&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=150&artlang=de';
        }
        if($type == 'instagram_auth_faq'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=19&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=22&artlang=de';
        }
        if($type == 'instagram_business_auth_faq'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=2&id=183&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=2&id=180&artlang=de';
        }
        if($type == 'url_shortener_faq'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=4&id=40&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=4&id=37&artlang=de';
        }
        if($type == 'network_addon_faq'){
            return ($lang == 'en') ? 'https://www.blog2social.com/en/faq/index.php?action=artikel&cat=7&id=168&artlang=en' : 'https://www.blog2social.com/de/faq/index.php?action=artikel&cat=7&id=165&artlang=de';
        }
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
        $sql = $wpdb->prepare("SELECT token FROM `{$wpdb->prefix}b2s_user` WHERE `blog_user_id` = %d", $user->data->ID);
        $userExist = $wpdb->get_row($sql);
        if (empty($userExist) || !isset($userExist->token)) {
            $postData = array('action' => 'getToken', 'blog_user_id' => $user->data->ID, 'blog_url' => get_option('home'), 'email' => $user->data->user_email, 'is_multisite' => is_multisite());
            $result = json_decode(B2S_Tools::getToken($postData));
            if (isset($result->result) && (int) $result->result == 1 && isset($result->token)) {
                $state_url = (isset($result->state_url)) ? (int) $result->state_url : 0;
                $sqlInsertToken = $wpdb->prepare("INSERT INTO `{$wpdb->prefix}b2s_user` (`token`, `blog_user_id`,`register_date`,`state_url`) VALUES (%s,%d,%s,%d);", $result->token, (int) $user->data->ID, date('Y-m-d H:i:s'), $state_url);
                $wpdb->query($sqlInsertToken);
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
                        if(is_array($b2sVersionType) && isset($b2sVersionType[$userVersion]) && !empty($b2sVersionType[$userVersion])){
                            $ver = " (Blog2Social " . esc_html__('License', 'blog2social') . ": " . $b2sVersionType[$userVersion] . ")";
                        }
                    }
                    $options .= '<option value="' . $user->data->ID . '" ' . (($user->data->ID == $selectId) ? "selected" : "") . '>' . esc_attr($user->data->display_name) . " (" . esc_attr($user->data->user_email) . ")" . $ver . '</option>';
                }
            }
        }
        return $options;
    }

    
    public static function getCountryListByNetwork($networkId = 6){
        $countryList = array();
        if($networkId == 6) { //Pinterest
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
        if((int) $blogUserId >= 1) {
            $changed = false;
            require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
            $options = new B2S_Options((int) $blogUserId);
            $linkNoCache = $options->_getOption("link_no_cache");
            if ($linkNoCache != false) {
                if(!is_array($linkNoCache)) {
                    $fb_linkNoCache = (((int) $linkNoCache > 0) ? 1 : 0);
                    $linkNoCache = $default;
                    $linkNoCache[1] = $fb_linkNoCache;
                    $changed = true;
                } else {
                    foreach ($default as $k => $v) {
                        if(!isset($linkNoCache[$k])) {
                            $linkNoCache[$k] = $v;
                            $changed = true;
                        }
                    }
                }
            } else {
                $linkNoCache = $default;
                $changed = true;
            }
            if($changed) {
                $options->_setOption('link_no_cache', $linkNoCache);
            }
            return $linkNoCache;
        }
        return $default;
    }
    
    public static function extractKeywords($string){
        $stopWords = array('i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www');

        $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
        $string = trim($string); // trim the string
        $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
        $string = strtolower($string); // make it lowercase

        preg_match_all('/\b.*?\b/i', $string, $matchWords);
        $matchWords = $matchWords[0];

        foreach ( $matchWords as $key=>$item ) {
            if ( $item == '' || in_array(strtolower($item), $stopWords) || strlen($item) <= 3 ) {
                unset($matchWords[$key]);
            }
        }
        $wordCountArr = array();
        if ( is_array($matchWords) ) {
            foreach ( $matchWords as $key => $val ) {
                $val = strtolower($val);
                $wordCountArr[] = $val;
            }
        }
        arsort($wordCountArr);
        $wordCountArr = array_slice($wordCountArr, 0, 10);
        return $wordCountArr;
    }
    
}
