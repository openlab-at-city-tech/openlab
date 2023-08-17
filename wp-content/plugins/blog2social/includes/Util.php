<?php

class B2S_Util {

    public static function urlsafe_base64_encode($data) {
//return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        return base64_encode($data);
    }

    public static function urlsafe_base64_decode($data) {
//return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - ( 3 + strlen($data)) % 4));
        return base64_decode($data);
    }

    public static function getUTCForDate($date, $userTimezone) {
        $utcTime = strtotime($date) + ($userTimezone * 3600);
        return date('Y-m-d H:i:s', $utcTime);
    }

    public static function getLocalDate($userTimezone, $lang = 'en') {
        $ident = ($lang == 'de') ? 'd.m.Y H:i' : 'Y/m/d g:i a';
        $localTime = strtotime(gmdate('Y-m-d H:i:s')) + ($userTimezone * 3600);
        return date($ident, $localTime);
    }

    public static function getbyIdentLocalDate($userTimezone, $ident = "Y-m-d H:i:s") {
        $localTime = strtotime(gmdate('Y-m-d H:i:s')) + ($userTimezone * 3600);
        return date($ident, $localTime);
    }

    public static function getCustomLocaleDateTime($userTimezone, $ident = "Y-m-d H:i:s") {
        $localTime = strtotime(gmdate('Y-m-d H:i:s')) + ($userTimezone * 3600);
        return date($ident, $localTime);
    }

    public static function getVersion($version = 000) {
        return substr(chunk_split($version, 1, '.'), 0, -1);
    }

    public static function convertKbToGb($kbytes) {
        return $fileSize = round($kbytes / 1024 / 1024, 3) . 'GB';
    }

    public static function getUsedPercentOfXy($open, $total) {
        $usedOf = (100-((100 / $total) * $open));
        return round($usedOf, 2);
    }

    public static function returnInByts($val = "") {
        if (!empty($val)) {
            $last = strtolower(mb_substr(trim($val), -1));
            switch ($last) {
                case 'g':
                    $val *= 1024 * 1024 * 1024;
                    break;
                case 'm':
                    $val *= 1024 * 1024;
                    break;
                case 'k':
                    $val *= 1024;
                    break;
                default:
                    break;
            }
        }
        return $val;
    }

    public static function getCustomDateFormat($dateTime = '0000-00-00 00:00:00', $lang = 'en', $time = true) {
        $options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
        $optionUserTimeFormat = $options->_getOption('user_time_format');
        if ($optionUserTimeFormat == false) {
            $optionUserTimeFormat = ($lang == 'de') ? 0 : 1;
        }
        if ($optionUserTimeFormat == 0) {
            $ident = 'd.m.Y ' . (($time) ? 'H:i' : '');
            return date($ident, strtotime($dateTime)) . (($time && $lang == 'de') ? ' ' . __('clock', 'blog2social') : '');
        } else {
            $ident = 'Y/m/d ' . (($time) ? 'g:i a' : '');
            return date($ident, strtotime($dateTime));
        }
    }

    public static function getTrialRemainingDays($trialEndDate = '', $timeZone = 'Europe/Berlin') {
        if (!empty($trialEndDate)) {
            $trailDateUtc = new DateTime($trialEndDate);
            $timeZone = empty($timeZone) ? 'Europe/Berlin' : $timeZone;
            $trailDateUtc->setTimezone(new DateTimeZone($timeZone));
            $isTrial = $trailDateUtc->format('Y-m-d H:i:s');

            $differTime = strtotime($isTrial) - time();
            if ((int) $differTime >= 0) {
                return (int) ($differTime / 86400);
            }
            return 0;
        }
        return false;
    }

    public static function scrapeUrl($url = '') {
        if (!empty($url)) {
            $param = array();
            libxml_use_internal_errors(true); // Yeah if you are so worried about using @ with warnings
            $hasHashtag = strpos($url, '#');
            if ($hasHashtag == false || $hasHashtag <= 0) {
                $url = $url . ((parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'no_cache=1');  //nocache
            } else {
                $subUrl = explode('#', $url);
                if (isset($subUrl[0]) && isset($subUrl[1])) {
                    $url = $subUrl[0] . ((parse_url($subUrl[0], PHP_URL_QUERY) ? '&' : '?') . 'no_cache=1');
                    for ($i = 1; $i < count($subUrl); $i++) {
                        $url .= '#' . $subUrl[$i];
                    }
                } else {
                    $url = $url . ((parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'no_cache=1');  //nocache
                }
            }
            $html = self::b2sFileGetContents($url, true);
            if (!empty($html) && $html !== false) {
//Search rist Parameter
                $data = self::b2sGetAllTags($html, 'all', false);
                if (is_array($data) && !empty($data)) {
                    return $data;
                }
            }
            return false;
        }
    }

    public static function getMetaTags($postId = 0, $postUrl = '', $network = 1) {
        $type = ($network == 2) ? 'twitter' : 'og';
        $search = ($network == 2) ? 'name' : 'property';
//GETSTOREEDDATA
        if ((int) $postId != 0) {
            $metaData = get_option('B2S_PLUGIN_POST_META_TAGES_' . strtoupper($type) . '_' . $postId);
            if ($metaData !== false && is_array($metaData)) {
                return $metaData;
            }
        }
//GETDATA
        $getTags = array('title', 'description', 'image');
        $param = array();
        libxml_use_internal_errors(true); // Yeah if you are so worried about using @ with warnings
        $postUrl = $postUrl . ((parse_url($postUrl, PHP_URL_QUERY) ? '&' : '?') . 'no_cache=1');  //nocache
        $html = self::b2sFileGetContents($postUrl);
        if (!empty($html) && $html !== false) {
//Search rist OG Parameter
            $temp = self::b2sGetAllTags($html, $type, false, $search);
            foreach ($getTags as $k => $v) {
                if (isset($temp[$v]) && !empty($temp[$v])) {
                    $param[$v] = $temp[$v];
                } else {
                    if ($v == 'title') {
                        if (function_exists('mb_convert_encoding')) {
                            $param[$v] = htmlspecialchars(self::b2sGetMetaTitle($html));
                        } else {
                            $param[$v] = self::b2sGetMetaTitle($html);
                        }
                    }
                    if ($v == 'description') {
                        if (function_exists('mb_convert_encoding')) {
                            $param[$v] = htmlspecialchars(self::b2sGetMetaDescription($html));
                        } else {
                            $param[$v] = self::b2sGetMetaDescription($html);
                        }
                    }
                }
            }
//STOREDATA
            if ((int) $postId != 0) {
                update_option('B2S_PLUGIN_POST_META_TAGES_' . strtoupper($type) . '_' . $postId, $param);
            }
            return $param;
        }
        return false;
    }

    private static function b2sFileGetContents($url, $extern = false) {
        $args = array(
            'timeout' => '15',
            'redirection' => '5',
            'user-agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:97.0) Gecko/20100101 Firefox/97.0"
        );
        $response = wp_safe_remote_get($url, $args);
        if (!is_wp_error($response)) {
            return wp_remote_retrieve_body($response);
        } else if ($extern) {
            $res = json_decode(B2S_Api_Get::get(B2S_PLUGIN_API_ENDPOINT . 'get.php?action=scrapeUrl&url=' . urlencode($url)));
            if (isset($res->data) && !empty($res->data)) {
                return $res->data;
            }
        }
        return false;
    }

    private static function b2sGetMetaDescription($html) {
//$res = get_meta_tags($url);
//return (isset($res['description']) ? self::cleanContent(strip_shortcodes($res['description'])) : '');
        $res = preg_match('#<meta +name *=[\"\']?description[\"\']?[^>]*content=[\"\']?(.*?)[\"\']? */?>#i', $html, $matches);
        return (isset($matches[1]) && !empty($matches[1])) ? trim(preg_replace('/\s+/', ' ', $matches[1])) : '';
    }

    private static function b2sGetMetaTitle($html) {
        $res = preg_match("/<title>(.*)<\/title>/siU", $html, $matches);
        return (isset($matches[1]) && !empty($matches[1])) ? trim(preg_replace('/\s+/', ' ', $matches[1])) : '';
    }

    private static function b2sGetAllTags($html, $type = 'og', $ignoreEncoding = false, $search = 'property') {
        $list = array();
        $doc = new DOMDocument();
        if (function_exists('mb_convert_encoding') && !$ignoreEncoding) {
            @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        } else {
            @$doc->loadHTML($html);
        }
        $metas = $doc->getElementsByTagName('meta');
        $title = $doc->getElementsByTagName("title");

        if ($type == 'all') {
            if ($title->length > 0) {
                if ($title->item(0)->nodeValue != "") {
                    $list['default_title'] = $title->item(0)->nodeValue;
                }
            }
        }
        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);
            if ($type != 'all') {
                if (($meta->getAttribute('property') == $type . ':title' || $meta->getAttribute('name') == $type . ':title') && !isset($list['title'])) {
                    $list['title'] = (function_exists('mb_convert_encoding') ? htmlspecialchars($meta->getAttribute('content')) : $meta->getAttribute('content'));
                }
                if (($meta->getAttribute('property') == $type . ':description' || $meta->getAttribute('name') == $type . ':description') && !isset($list['description'])) {
                    $desc = self::cleanContent(strip_shortcodes($meta->getAttribute('content')));
                    $list['description'] = (function_exists('mb_convert_encoding') ? htmlspecialchars($desc) : $desc);
                }
                if (($meta->getAttribute('property') == $type . ':image' || $meta->getAttribute('name') == $type . ':image') && !isset($list['image'])) {
                    $list['image'] = $meta->getAttribute('content');
                }
            } else {
                if ($meta->getAttribute('name') == 'description' && !isset($list['default_description'])) {
                    $list['default_description'] = (function_exists('mb_convert_encoding') ? htmlspecialchars($meta->getAttribute('content')) : $meta->getAttribute('content'));
                }
                if ($meta->getAttribute($search) == 'og:title' && !isset($list['og_title'])) {
                    $list['og_title'] = (function_exists('mb_convert_encoding') ? htmlspecialchars($meta->getAttribute('content')) : $meta->getAttribute('content'));
                }
                if ($meta->getAttribute($search) == 'og:description' && !isset($list['og_description'])) {
                    $desc = self::cleanContent(strip_shortcodes($meta->getAttribute('content')));
                    $list['og_description'] = (function_exists('mb_convert_encoding') ? htmlspecialchars($desc) : $desc);
                }
                if ($meta->getAttribute($search) == 'og:image' && !isset($list['og_image'])) {
                    $list['og_image'] = $meta->getAttribute('content');
                }
//Further
                /* if ($meta->getAttribute($search) == 'twitter:title' && !isset($list['twitter_title'])) {
                  $list['twitter_title'] = (function_exists('mb_convert_encoding') ? htmlspecialchars($meta->getAttribute('content')) : $meta->getAttribute('content'));
                  }
                  if ($meta->getAttribute($search) == 'twitter:description' && !isset($list['twitter_description'])) {
                  $desc = self::cleanContent(strip_shortcodes($meta->getAttribute('content')));
                  $list['twitter_description'] = (function_exists('mb_convert_encoding') ? htmlspecialchars($desc) : $desc);
                  }
                  if ($meta->getAttribute($search) == 'twitter:image' && !isset($list['twitter_image'])) {
                  $list['twitter_image'] = (function_exists('mb_convert_encoding') ? htmlspecialchars($meta->getAttribute('content')) : $meta->getAttribute('content'));
                  } */
            }
        }
        return $list;
    }

    public static function getImagesByPostId($postId = 0, $forceFeaturedImage = true, $postContent = '', $postUrl = '', $network = false, $postLang = 'en') {
        $matches = array();
        $homeUrl = get_site_url();
        $scheme = parse_url($homeUrl, PHP_URL_SCHEME);
        $attachment_id = get_post_thumbnail_id($postId);
        $featuredImage = wp_get_attachment_url($attachment_id);
        $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if ($forceFeaturedImage && $featuredImage != false && !empty($featuredImage)) {
            $ext = pathinfo(
                    parse_url($featuredImage, PHP_URL_PATH),
                    PATHINFO_EXTENSION
            );
            if (!in_array($ext, array('jpg', 'png', 'webp'))) {
                return array(0 => array(0 => $featuredImage, 1 => esc_attr($image_alt)));
            }
        }
        $content = stripcslashes(self::getFullContent($postId, $postContent, $postUrl, $postLang));
        if (!preg_match_all('%<img.*?src=[\"\'](.*?)[\"\'].*?>%', $content, $matches) && !$featuredImage) {
            return false;
        }
        array_unshift($matches[1], $featuredImage);
        $rtrnArray = array();
        if (isset($matches[1])) {
            foreach ($matches[1] as $key => $imgUrl) {
                if ($imgUrl == false) {
                    continue;
                }
//AllowedExtensions?
                if (!$network && !in_array(substr($imgUrl, strrpos($imgUrl, '.')), array('.jpg', '.png'))) {
                    continue;
                }
//isRelativ?
                if (!preg_match('/((http|https):\/\/|(www.))/', $imgUrl)) {
//StartWith //
                    if ((substr($imgUrl, 0, 2) == '//')) {
                        $imgUrl = (($scheme != NULL) ? $scheme : 'http') . ':' . $imgUrl;
                    } else {
//StartWith /
                        $imgUrl = (substr($imgUrl, 0, 1) != '/') ? '/' . $imgUrl : $imgUrl;
                        $imgUrl = str_replace('//', '/', $imgUrl);
                        $imgUrl = $homeUrl . $imgUrl;
                        if (strpos($imgUrl, 'http://') === false && strpos($imgUrl, 'https://') === false) {
                            $imgUrl = (($scheme != NULL) ? $scheme : 'http') . '://' . $imgUrl;
                        }
                    }
                }
                /* $file_headers = @get_headers($imgUrl);
                  if ((!is_array($file_headers)) || (is_array($file_headers) && !preg_match('/200/', $file_headers[0]))) {
                  continue;
                  } */
                $rtrnArray[$key][0] = urldecode($imgUrl);
            }
        }
        return $rtrnArray;
    }

    public static function clean_html($text = '', $tags = array('style', 'script', 'noscript')) {
        if (!empty($text)) {
            if (!empty($tags)) {
                if (is_array($tags)) {
                    foreach ($tags as $tag) {
                        $text = preg_replace("/<\s*$tag\b[^>]*>(.*?)<\s*\/\s*$tag\s*>/is", '', $text);
                    }
                } else if (is_string($tags)) {
                    $text = preg_replace("/<\s*$tags\b[^>]*>(.*?)<\s*\/\s*$tags\s*>/is", '', $text);
                }
            }
        }
        return $text;
    }

    public static function convertLiElements($postContent) {
        $postContent = preg_replace("/<([\s]*?)\/([\s]*?)li([\s]*?)>*?([^\n]?)*?<([\s]*?)li/", "</li>\n<li", $postContent);
        $postContent = preg_replace("/<([\s]*?)li*?([\s]*?)>/", "- ", $postContent);
        $postContent = preg_replace("/<([\s]*?)\/([\s]*?)li([\s]*?)>/", "", $postContent);
        return $postContent;
    }

    public static function prepareContent($postId = 0, $postContent = '', $postUrl = '', $allowHtml = '<p><h1><h2><br><i><b><a><img>', $allowEmoji = true, $postLang = 'en') {
        $homeUrl = get_site_url();
        $scheme = parse_url($homeUrl, PHP_URL_SCHEME);
        $postContent = html_entity_decode($postContent, ENT_COMPAT, 'UTF-8');
        $postContent = self::getFullContent($postId, $postContent, $postUrl, $postLang);
        $postContent = B2S_Util::clean_html($postContent);
        $postContent = B2S_Util::convertLiElements($postContent);
        $prepareContent = ($allowHtml !== false) ? self::cleanContent(self::cleanHtmlAttr(strip_shortcodes(self::cleanShortCodeByCaption($postContent)))) : self::cleanContent(strip_shortcodes($postContent));
        $prepareContent = ($allowEmoji !== false) ? $prepareContent : self::remove4byte($prepareContent);
//$prepareContent = preg_replace('/(?:[ \t]*(?:\n|\r\n?)){3,}/', "\n\n", $prepareContent);


        if ($allowHtml !== false) {
            $prepareContent = preg_replace("/(<[\/]*)strong(>)/", "$1b$2", $prepareContent);
            $prepareContent = preg_replace("/(<[\/]*)em(>)/", "$1i$2", $prepareContent);
            $tempContent = nl2br(preg_replace('/(?:[ \t]*(?:\n|\r\n?)){3,}/', "\n", trim(strip_tags($prepareContent, $allowHtml))));
            if (preg_match_all('%<img.*?src=[\"\'](.*?)[\"\'].*?/>%', $tempContent, $matches)) {
                foreach ($matches[1] as $key => $imgUrl) {
                    if ($imgUrl == false) {
                        continue;
                    }
//isRelativ?
                    if (!preg_match('/((http|https):\/\/|(www.))/', $imgUrl)) {
//StartWith //
                        if ((substr($imgUrl, 0, 2) == '//')) {
                            $tempImgUrl = (($scheme != NULL) ? $scheme : 'http') . ':' . $imgUrl;
                        } else {
//StartWith /
                            $tempImgUrl = (substr($imgUrl, 0, 1) != '/') ? '/' . $imgUrl : $imgUrl;
                            $tempImgUrl = str_replace('//', '/', $tempImgUrl);
                            $tempImgUrl = $homeUrl . $tempImgUrl;
                            if (strpos($tempImgUrl, 'http://') === false && strpos($imgUrl, 'https://') === false) {
                                $tempImgUrl = (($scheme != NULL) ? $scheme : 'http') . '://' . $tempImgUrl;
                            }
                        }
                        $tempContent = str_replace(trim($imgUrl), $tempImgUrl, $tempContent);
                    }
                }
            }
            return $tempContent;
        }
        return preg_replace('/(?:[ \t]*(?:\n|\r\n?)){3,}/', "\n\n", trim(strip_tags($prepareContent)));
    }

    public static function cleanHtmlAttr($postContent) {
        $postContent = preg_replace('/(<[^>]+) style=[\"\'].*?[\"\']/i', '$1', $postContent);
        $postContent = preg_replace('/(<[^>]+) class=[\"\'].*?[\"\']/i', '$1', $postContent);
        $postContent = preg_replace('/(<[^>]+) height=[\"\'].*?[\"\']/i', '$1', $postContent);
        $postContent = preg_replace('/(<[^>]+) width=[\"\'].*?[\"\']/i', '$1', $postContent);
        return preg_replace('/(<[^>]+) id=[\"\'].*?[\"\']/i', '$1', $postContent);
    }

    public static function cleanContent($postContent) {
        return preg_replace('/\[.*?(?=\])\]/s', '', $postContent);
    }

    public static function getFullContent($postId = 0, $postContent = '', $postUrl = '', $postLang = 'en') {
        $postLang = ($postLang === false) ? 'en' : trim(strtolower($postLang));
//isset settings allow shortcode
        if (get_option('B2S_PLUGIN_USER_ALLOW_SHORTCODE_' . get_current_user_id()) !== false) {
//check is shortcode in content
            if (preg_match('/\[(.*?)\]/s', $postContent)) {
//check has crawled content from frontend
                $dbContent = get_option('B2S_PLUGIN_POST_CONTENT_' . $postId);
                if ($dbContent !== false) {
                    return $dbContent;
                } else {
//crawl content from frontend
                    $postUrl = add_query_arg(array('b2s_get_full_content' => 1, 'no_cache' => 1, 'lang' => $postLang), $postUrl);
                    $wpB2sGetFullContent = wp_remote_get($postUrl, array('timeout' => 11)); //slot 11 seconds         
                    if (is_array($wpB2sGetFullContent) && !is_wp_error($wpB2sGetFullContent)) {
//get crwaled content from db - hide cache by get_options
                        global $wpdb;
                        $dbContent = $wpdb->get_var($wpdb->prepare("SELECT option_value FROM " . $wpdb->options . " WHERE option_name =%s ", 'B2S_PLUGIN_POST_CONTENT_' . $postId));
                        if ($dbContent !== NULL) {
                            return $dbContent;
                        }
                    }
                }
            }
        }
        return $postContent;
    }

//Emoji by Schedule + AllowNoNetwork
    public static function remove4byte($content) {
        if (function_exists('iconv')) {
            $content = iconv("utf-8", "utf-8//ignore", $content);
        }
        return trim(preg_replace('%(?:
         \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
        | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
    )%xs', '', $content));
    }

    public static function cleanShortCodeByCaption($postContent) {
        preg_match_all('#\s*\[caption[^]]*\].*?\[/caption\]\s*#is', $postContent, $matches);
        if (isset($matches[0]) && !empty($matches[0]) && is_array($matches[0])) {
            $temp = '';
            foreach ($matches[0] as $k => $v) {
                $temp = $v;
                if (preg_match('/< *img[^>]+\>/i', $v, $match)) {
                    $v = (isset($match[0])) ? str_replace($match[0], $match[0] . "\n\n", $v) : $v;
                    $t = preg_replace('#\s*\[/caption\]\s*#is', "\n\n", $v);
                    $new = preg_replace('#\s*\[caption[^]]*\]\s*#is', '', $t);
                    $postContent = str_replace($temp, "\n" . $new, $postContent);
                }
            }
        }
        return $postContent;
    }

    public static function getRandomTime($start, $ende) {
        $startparts = explode(':', $start);
        $startH = $startparts[0];
        $startMin = strlen($startparts[1]) == 1 ? '0' . $startparts[1] : $startparts[1];
        $endparts = explode(':', $ende);
        $endH = $endparts[0];
        $endMin = strlen($endparts[1]) == 1 ? '0' . $endparts[1] : $endparts[1];

        $rand = rand((int) ($startH . $startMin), (int) ($endH . $endMin));
        if ($rand == NULL) {
            return date('H:00');
        }
        if (strlen($rand) == 3) {
            $rand = '0' . $rand;
        }
        $hour = substr($rand, 0, 2);
        $miunte = substr($rand, 2, 2);
        $minute = $miunte > 50 ? '30' : '00';

        return $hour . ':' . $minute;
    }

    public static function getTimeByLang($time, $lang = 'de') {
        $time = substr('0' . $time, -2);
        $slug = ($lang == 'en') ? 'h:i a' : 'H:i';
        return date($slug, strtotime(date('Y-m-d ' . $time . ':00:00')));
    }

    public static function getExcerpt($text, $count = 400, $max = false, $add = false) {
//Bug: Converting json + PHP Extension
        if (function_exists('mb_strlen') && function_exists('mb_substr') && function_exists('mb_stripos') && function_exists('mb_strripos')) {
            if (mb_strlen($text, 'UTF-8') < $count) {
                return trim($text);
            }
            if ($max != false && mb_strlen($text, 'UTF-8') < $max) {
                return trim($text);
            }

            $stops = array('.', '?', '!', '#', '(');
            $min = $count;
            $cleanTruncateWord = true;
            $max = ($max !== false) ? ($max - $min) : ($min - 1);
            if (mb_strlen($text, 'UTF-8') < $max) {
                return trim($text);
            }

            $sub = mb_substr($text, $min, $max, 'UTF-8');
            $stopAt = '';
            $stopAtPos = 0;
            for ($i = 0; $i < count($stops); $i++) {
                if (mb_strripos($sub, $stops[$i]) > $stopAtPos) {
                    $stopAt = $stops[$i];
                    $stopAtPos = mb_strripos($sub, $stops[$i]);
                }
            }

            if (!empty($stopAt)) {
                if (count($subArray = explode($stopAt, $sub)) > 1) {
                    $cleanTruncateWord = false;
                    if (mb_substr($subArray[count($subArray) - 1], 0, 1) == ' ' || mb_substr($subArray[count($subArray) - 1], 0, 1) == "\n") { //empty first charcater in last explode - delete last explode
                        $subArray[count($subArray) - 1] = ' ';
                    }
                    if (mb_stripos($subArray[count($subArray) - 1], $stopAt) === false) { //delete last explode if no stops set
                        $subArray[count($subArray) - 1] = mb_substr($subArray[count($subArray) - 1], 0, mb_stripos($subArray[count($subArray) - 1], ' '));
                    }
                    $sub = implode($stopAt, $subArray);
                    $add = false;
                }
                if ($stopAt == '#') {
                    $sub = mb_substr($sub, 0, -1);
                }
            }

            if ($cleanTruncateWord) {
                $lastIndex = mb_strripos($sub, ' ');
                if ($lastIndex !== false) {
                    $sub = trim(mb_substr($sub, 0, $lastIndex));
                }
            }
            $text = trim(mb_substr($text, 0, $min, 'UTF-8') . $sub);
            return ($add) ? $text . "..." : $text;
        }

        return trim($text);
    }

//Plugin qTranslate [:en]Content[:de]Text[:]
    public static function getTitleByLanguage($title, $postLang = 'en') {
//$title = html_entity_decode($title, ENT_QUOTES | ENT_XML1);
        $postLang = ($postLang === false) ? 'en' : trim(strtolower($postLang));
        $regex = "#(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\]|\{:[a-z]{2}\}|\{:\})#ism";
        $blocks = preg_split($regex, $title, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (count($blocks) <= 1) {//no language is encoded in the $text, the most frequent case
            return strip_tags($title);
        }
        $result = array();
        $current_lang = false;
        foreach ($blocks as $block) {
// detect c-tags
            if (preg_match("#^<!--:([a-z]{2})-->$#ism", $block, $matches)) {
                $current_lang = $matches[1];
                continue;
// detect b-tags
            } elseif (preg_match("#^\[:([a-z]{2})\]$#ism", $block, $matches)) {
                $current_lang = $matches[1];
                continue;
// detect s-tags @since 3.3.6 swirly bracket encoding added
            } elseif (preg_match("#^\{:([a-z]{2})\}$#ism", $block, $matches)) {
                $current_lang = $matches[1];
                continue;
            }
            switch ($block) {
                case '[:]':
                case '{:}':
                case '<!--:-->':
                    $current_lang = false;
                    break;
                default:
// correctly categorize text block
                    if ($current_lang) {
                        if (!isset($result[$current_lang])) {
                            $result[$current_lang] = '';
                        }
                        $result[$current_lang] .= $block;
                        $found[$current_lang] = true;
                        $current_lang = false;
                    }
                    break;
            }
        }
        foreach ($result as $l => $text) {
            $result[$l] = trim(strip_tags($text));
        }

        if (!isset($found[$postLang])) {
            $locale = (!get_locale()) ? get_locale() : B2S_LANGUAGE;
            $postLang = substr($locale, 0, 2);
            if (!isset($found[$postLang])) {
                $postLang = current(array_keys($found));
            }
        }

        return strip_tags($result[$postLang]);
    }

    public static function createTimezoneList($selected = '', $region = 2047) { //DateTimeZone::ALL == 2047  >=PHP 5.5.3 constant not set
        $timezones = timezone_identifiers_list($region);
        if (!$timezones) {
            return false;
        }
        $optionHtmlList = '';
        $timezoneData = array();
        foreach ($timezones as $timezone) {
            $timezoneData[$timezone] = self::getOffsetToUtcByTimeZone($timezone);
            self::humanReadableOffset($timezoneData[$timezone]);
            $utcStr = '(UTC ' . self::humanReadableOffset($timezoneData[$timezone]) . ')';
            $timeZoneEntry = trim($utcStr) . ' ' . trim(preg_replace("/\_/", ' ', $timezone));
            $isSelected = ($timezone == $selected) ? 'selected' : '';
            $optionHtmlList .= '<option value="' . esc_attr($timezone) . '" data-offset="' . esc_attr($timezoneData[$timezone]) . '" ' . $isSelected . '>' . esc_html($timeZoneEntry) . '</option>';
        }
        return $optionHtmlList;
    }

    public static function humanReadableOffset($floatnbr = 0) {
        $result = '';
        $floatnbr = number_format($floatnbr, 2, '.', ' ');
        $sign = '';
        switch ($floatnbr) {

            case $floatnbr > 0.00:
                $sign = '+';
                break;

            case $floatnbr < 0.00:
                $sign = '-';
                break;

            case $floatnbr == 0.00:
                break;
        }

        $nbrSplit = explode('.', $floatnbr);
        $first = $nbrSplit[0];
        if ($first < 0) {
            $first = preg_replace('/-/', '', $first);
        }

        $first = str_pad($first, 2, '0', STR_PAD_LEFT);

        $second = $nbrSplit[1];
        if ($second > 0) {
            $second = $second / 100 * 60;
        }

        if ($floatnbr < 0.00) {
            $first = '-' . $first;
        } elseif ($floatnbr > 0.00) {
            $first = '+' . $first;
        } else {
            $first = ' ' . $first;
        }

        return $first . ':' . $second;
    }

    public static function getOffsetToUtcByTimeZone($userTimeZone = '', $firstDateTime = 'now') {

        if (empty($userTimeZone)) {
            $userTimeZone = date_default_timezone_get();
        }

        $this_tz = new DateTimeZone($userTimeZone);
        $now = new DateTime($firstDateTime, $this_tz);
        $offset = $this_tz->getOffset($now);

        return (float) $offset / 3600;
    }

    public static function addUrlParameter($url = '', $parameter = array()) {
        $add = '&';
        if (!parse_url($url, PHP_URL_QUERY)) {
            $add = '?';
        }
        foreach ($parameter as $key => $value) {
            $url .= $add . $key . '=' . $value;
            $add = '&';
        }
        return $url;
    }

}
