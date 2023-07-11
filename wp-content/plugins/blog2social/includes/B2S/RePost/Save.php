<?php
class B2S_RePost_Save {

    private $title;
    private $contentHtml;
    private $postId;
    private $content;
    private $excerpt;
    private $url;
    private $imageUrl;
    private $keywords;
    private $blogUserId;
    private $userTimezone;
    private $setPreFillText;
    private $optionPostFormat;
    private $allowHashTag;
    private $bestTimes;
    private $userVersion;
    private $defaultPostData;
    private $b2sUserLang;
    private $notAllowNetwork;
    private $allowNetworkOnlyImage;
    private $tosCrossPosting;
    private $linkNoCache;
    private $allowHtml;
    private $default_template;

    function __construct($blogUserId = 0, $b2sUserLang = 'en', $userTimezone = 0, $optionPostFormat = array(), $allowHashTag = true, $bestTimes = array(), $userVersion = 0) {
        $this->userVersion = defined("B2S_PLUGIN_USER_VERSION") ? B2S_PLUGIN_USER_VERSION : (int) $userVersion;
        $this->blogUserId = $blogUserId;
        $this->userTimezone = $userTimezone;
        $this->optionPostFormat = $optionPostFormat;
        $this->allowHashTag = $allowHashTag;
        $this->b2sUserLang = $b2sUserLang;
        $this->bestTimes = (!empty($bestTimes)) ? $bestTimes : array();
        $this->setPreFillText = array(0 => array(6 => 300, 8 => 239, 10 => 442, 16 => 250, 17 => 442, 18 => 800, 20 => 300, 21 => 65000, 38 => 500, 39 => 2000), 1 => array(8 => 1200, 10 => 442, 17 => 442, 19 => 239), 2 => array(8 => 239, 10 => 442, 17 => 442, 19 => 239));
        $this->setPreFillTextLimit = array(0 => array(6 => 400, 8 => 400, 10 => 500, 18 => 1000, 20 => 400, 16 => false, 21 => 65535, 38 => 500, 39 => 2000), 1 => array(8 => 1200, 10 => 500, 19 => 400), 2 => array(8 => 400, 10 => 500, 19 => 9000));
        $this->notAllowNetwork = array(4, 11, 14, 16, 18);
        $this->allowHtml = array(4, 11, 14);
        $this->allowNetworkOnlyImage = array(6, 7, 12, 20, 21);
        $this->tosCrossPosting = unserialize(B2S_PLUGIN_NETWORK_CROSSPOSTING_LIMIT);
        $this->linkNoCache = B2S_Tools::getNoCacheData(B2S_PLUGIN_BLOG_USER_ID);
        $this->default_template = (defined('B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT')) ? unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT) : false;
    }

    public function setPostData($postId = 0, $title = '', $content = '', $excerpt = '', $url = '', $imageUrl = '', $keywords = '') {
        $this->postId = $postId;
        $this->title = $title;
        $this->content = B2S_Util::prepareContent($postId, $content, $url, false, true, $this->b2sUserLang);
        $this->excerpt = B2S_Util::prepareContent($postId, $excerpt, $url, false, true, $this->b2sUserLang);
        $this->contentHtml = B2S_Util::prepareContent($postId, $content, $url, '<p><h1><h2><br><i><b><a><img>', true, $this->b2sUserLang);
        $this->url = $url;
        $this->imageUrl = $imageUrl;
        $this->keywords = $keywords;
        $this->defaultPostData = array(
            'default_titel' => $title,
            'image_url' => ($imageUrl !== false) ? trim(urldecode($imageUrl)) : '',
            'lang' => $this->b2sUserLang,
            'no_cache' => 0, //default inactive , 1=active 0=not
            'board' => '',
            'group' => '',
            'url' => $url,
            'user_timezone' => $this->userTimezone
        );
    }

    public function generatePosts($startDate = '0000-00-00', $settings = array(), $networkData = array(), $twitter = '') {
        foreach ($networkData as $k => $value) {
            if (isset($value->networkAuthId) && (int) $value->networkAuthId > 0 && isset($value->networkId) && (int) $value->networkId > 0 && isset($value->networkType)) {
                //Filter: Image network
                if (in_array((int) $value->networkId, $this->allowNetworkOnlyImage) && empty($this->imageUrl)) {
                    continue;
                }
                //Filter: Blog network
                if (in_array((int) $value->networkId, $this->notAllowNetwork)) {
                    continue;
                }
                //Filter: TOS Crossposting ignore
                if (isset($this->tosCrossPosting[$value->networkId][$value->networkType])) {
                    continue;
                }
                //Filter: DeprecatedNetwork-8 31 march
                if ((int) $value->networkId == 8) {
                    continue;
                }
                $selectedTwitterProfile = (isset($twitter) && !empty($twitter)) ? (int) $twitter : '';
                if ((int) $value->networkId != 2 || ((int) $value->networkId == 2 && (empty($selectedTwitterProfile) || ((int) $selectedTwitterProfile == (int) $value->networkAuthId)))) {
                    $schedDate = $this->getPostDateTime($startDate, $settings, $value->networkAuthId);
                    $schedDateUtc = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($schedDate, ($this->userTimezone * -1))));
                    $shareApprove = (isset($value->instant_sharing) && (int) $value->instant_sharing == 1) ? 1 : 0;
                    $defaultPostData = $this->defaultPostData;
                    if ((int) $value->networkId == 1 || (int) $value->networkId == 3 || (int) $value->networkId == 19) {
                        if (is_array($this->linkNoCache) && isset($this->linkNoCache[$value->networkId]) && (int) $this->linkNoCache[$value->networkId] > 0) {
                            $defaultPostData['no_cache'] = $this->linkNoCache[$value->networkId];
                        }
                    }
                    $schedData = $this->prepareShareData($value->networkAuthId, $value->networkId, $value->networkType, ((isset($value->networkKind) && (int) $value->networkKind >= 0) ? $value->networkKind : 0));
                    if ($schedData !== false && is_array($schedData)) {
                        $schedData = array_merge($schedData, $defaultPostData);
                    }

                    if (((int) $value->networkId == 12) && isset($this->optionPostFormat[$value->networkId][$value->networkType]['addLink']) && $this->optionPostFormat[$value->networkId][$value->networkType]['addLink'] == false) {
                        $schedData['url'] = '';
                    } else if (((int) $value->networkId == 1 || (int) $value->networkId == 2 || (int) $value->networkId == 24) && isset($this->optionPostFormat[$value->networkId][$value->networkType]['format']) && (int) $this->optionPostFormat[$value->networkId][$value->networkType]['format'] == 1 && isset($this->optionPostFormat[$value->networkId][$value->networkType]['addLink']) && $this->optionPostFormat[$value->networkId][$value->networkType]['addLink'] == false) {
                        $schedData['url'] = '';
                    }

                    $this->saveShareData($schedData, $value->networkId, $value->networkType, $value->networkAuthId, $shareApprove, strip_tags($value->networkUserName), $schedDate, $schedDateUtc);
                }
            }
        }
    }

    public function getPostDateTime($startDate = '0000-00-00', $settings = array(), $networkAuthId = 0) {
        $date = new DateTime($startDate);
        if (!empty($this->bestTimes) && isset($this->bestTimes['delay_day'][$networkAuthId]) && isset($this->bestTimes['time'][$networkAuthId]) && !empty($this->bestTimes['time'][$networkAuthId])) {
            if ((int) $this->bestTimes['delay_day'][$networkAuthId] > 0) {
                $date->modify('+' . $this->bestTimes['delay_day'][$networkAuthId] . ' days');
            }
            $time = $this->getTime($this->bestTimes['time'][$networkAuthId]);
            $date->setTime($time['h'], $time['m']);
        } else if (isset($settings['time']) && !empty($settings['time'])) {
            $time = $this->getTime($settings['time']);
            $date->setTime($time['h'], $time['m']);
        }
        if (isset($settings['type']) && (int) $settings['type'] == 0 && isset($settings['weekday']) && is_array($settings['weekday']) && !empty($settings['weekday'])) {
            while (!$settings['weekday'][(int) $date->format('w')]) {
                $date->modify('+1 days');
            }
        }
        return $date->format("Y-m-d H:i:s");
    }

    private function getTime($time) {
        $output = array('h' => (int) substr($time, 0, 2), 'm' => (int) substr($time, 3, 2));
        if (substr($time, -2) == "PM") {
            $output['h'] += 12;
        }
        return $output;
    }

    public function prepareShareData($networkAuthId = 0, $networkId = 0, $networkType = 0, $networkKind = 0) {

        if ((int) $networkId > 0 && (int) $networkAuthId > 0) {
            $postData = array('content' => '', 'custom_title' => '', 'tags' => array(), 'network_auth_id' => (int) $networkAuthId);

            if ((int) $this->userVersion < 1 || $this->optionPostFormat == false || !isset($this->optionPostFormat[$networkId][$networkType])) {
                $tempOptionPostFormat = $this->default_template;
            } else {
                $tempOptionPostFormat = $this->optionPostFormat;
            }

            //V6.5.5 - Xing Pages => Two diferend kinds
            if ($networkId == 19 && $networkType == 1) {
                if (!isset($tempOptionPostFormat[$networkId][$networkType]['short_text'][0]['limit'])) {
                    if (isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['limit'])) {
                        if (isset($this->default_template[$networkId][$networkType]['short_text'][4])) {
                            $tempOptionPostFormat[$networkId][$networkType]['short_text'] = array(0 => $tempOptionPostFormat[$networkId][$networkType]['short_text'], 4 => $this->default_template[$networkId][$networkType]['short_text'][4]);
                        }
                    } else {
                        $tempOptionPostFormat = $this->default_template;
                    }
                }
                $content_min = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['range_min'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['range_min'] : 0;
                $content_max = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['range_max'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['range_max'] : 0;
                $excerpt_min = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['excerpt_range_min'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['excerpt_range_min'] : 0;
                $excerpt_max = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['excerpt_range_max'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['excerpt_range_max'] : 0;
                $limit = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['limit'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text'][$networkKind]['limit'] : 0;
            } else {
                $defaultSchema = $this->default_template;
                if (!isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_max']) && isset($defaultSchema[$networkId][$networkType]['short_text']['excerpt_range_max'])) {
                    $tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_max'] = $defaultSchema[$networkId][$networkType]['short_text']['excerpt_range_max'];
                }
                if (!isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_min']) && isset($defaultSchema[$networkId][$networkType]['short_text']['excerpt_range_min'])) {
                    $tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_min'] = $defaultSchema[$networkId][$networkType]['short_text']['excerpt_range_min'];
                }
                $content_min = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['range_min'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text']['range_min'] : 0;
                $content_max = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['range_max'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text']['range_max'] : 0;
                $excerpt_min = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_min'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_min'] : 0;
                $excerpt_max = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_max'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_max'] : 0;
                $limit = (isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['limit'])) ? $tempOptionPostFormat[$networkId][$networkType]['short_text']['limit'] : 0;
            }

            //PostFormat
            if (in_array($networkId, array(1, 2, 3, 12, 17, 19, 24))) {
                //Get: client settings
                if (isset($tempOptionPostFormat[$networkId][$networkType]['format']) && ((int) $tempOptionPostFormat[$networkId][$networkType]['format'] === 0 || (int) $tempOptionPostFormat[$networkId][$networkType]['format'] === 1)) {
                    $postData['post_format'] = (int) $tempOptionPostFormat[$networkId][$networkType]['format'];
                } else {
                    //Set: default settings
                    $defaultPostFormat = $this->default_template;
                    $postData['post_format'] = isset($defaultPostFormat[$networkId][$networkType]['format']) ? (int) $defaultPostFormat[$networkId][$networkType]['format'] : 0;
                }
            }
            //Special
            if (array_key_exists($networkId, $tempOptionPostFormat)) {
                if ($networkId == 12 && $this->imageUrl == false) {
                    return false;
                }
                $hook_filter = new B2S_Hook_Filter();

                $postData['content'] = $tempOptionPostFormat[$networkId][$networkType]['content'];

                $preContent = addcslashes(B2S_Util::getExcerpt($this->content, (int) $content_min, (int) $content_max), "\\$");
                $postData['content'] = preg_replace("/\{CONTENT\}/", $preContent, $postData['content']);

                $title = sanitize_text_field($this->title);
                $postData['content'] = preg_replace("/\{TITLE\}/", addcslashes($title, "\\$"), $postData['content']);

                $excerpt = (isset($this->excerpt) && !empty($this->excerpt)) ? addcslashes(B2S_Util::getExcerpt($this->excerpt, (int) $excerpt_min, (int) $excerpt_max), "\\$") : '';

                $postData['content'] = preg_replace("/\{EXCERPT\}/", $excerpt, $postData['content']);

                $hashtagcount = substr_count($postData['content'], '#');

                if (strpos($postData['content'], "{KEYWORDS}") !== false) {
                    if ($this->default_template != false && isset($this->default_template[$networkId][$networkType]['disableKeywords']) && $this->default_template[$networkId][$networkType]['disableKeywords'] == true) {
                        $postData['content'] = preg_replace("/\{KEYWORDS\}/", '', $postData['content']);
                    } else {
                        $hashtags = ($this->allowHashTag) ? $this->getHashTagsString("", (($networkId == 12) ? 30 - $hashtagcount : -1), ((isset($tempOptionPostFormat[$networkId][$networkType]['shuffleHashtags']) && $tempOptionPostFormat[$networkId][$networkType]['shuffleHashtags'] == true) ? true : false)) : '';
                        $postData['content'] = preg_replace("/\{KEYWORDS\}/", addcslashes($hashtags, "\\$"), $postData['content']);
                    }
                }

                $authorId = get_post_field('post_author', $this->postId);
                if (isset($authorId) && !empty($authorId) && (int) $authorId > 0) {
                    $author_name = $hook_filter->get_wp_user_post_author_display_name((int) $authorId);
                    $postData['content'] = stripslashes(preg_replace("/\{AUTHOR\}/", addcslashes($author_name, "\\$"), $postData['content']));
                } else {
                    $postData['content'] = preg_replace("/\{AUTHOR\}/", "", $postData['content']);
                }

                if (class_exists('WooCommerce') && function_exists('wc_get_product')) {
                    $wc_product = wc_get_product($this->postId);
                    if ($wc_product != false) {
                        $price = $wc_product->get_price();
                        if ($price != false && !empty($price)) {
                            $postData['content'] = stripslashes(preg_replace("/\{PRICE\}/", addcslashes($price, "\\$"), $postData['content']));
                        }
                    }
                }
                $postData['content'] = preg_replace("/\{PRICE\}/", "", $postData['content']);

                $taxonomieReplacements = $hook_filter->get_posting_template_set_taxonomies(array(), $this->postId);
                if (is_array($taxonomieReplacements) && !empty($taxonomieReplacements)) {
                    foreach ($taxonomieReplacements as $taxonomie => $replacement) {
                        $postData['content'] = preg_replace("/\{" . $taxonomie . "\}/", $replacement, $postData['content']);
                    }
                }

                if (in_array($networkId, $this->allowHtml)) {
                    $postData['content'] = preg_replace("/\\n/", "<br>", $postData['content']);

                    //Feature Image Html-Network
                    if (!empty($this->imageUrl)) {
                        $postData['content'] = '<img src="' . esc_url($this->imageUrl) . '" alt="' . esc_attr($title) . '"/><br>' . $postData['content'];
                    }
                }

                if (isset($limit) && (int) $limit > 0) {
                    if (!empty($this->url) && $networkId == 2) {
                        $limit = 254;
                    }
                    if (!empty($this->url) && $networkId == 38) {
                        $limit = 500-strlen($this->url);
                    }
                    $postData['content'] = B2S_Util::getExcerpt($postData['content'], 0, $limit);
                }
            } else {
                if ($networkId == 4) {
                    $postData['custom_title'] = strip_tags($this->title);
                    $postData['content'] = $this->contentHtml;
                    if ($this->allowHashTag) {
                        if (is_array($this->keywords) && !empty($this->keywords)) {
                            foreach ($this->keywords as $tag) {
                                $postData['tags'][] = str_replace(" ", "", $tag->name);
                            }
                        }
                    }
                }

                if ($networkId == 6 || $networkId == 20) {
                    if ($this->imageUrl !== false) {
                        $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], (int) $this->setPreFillTextLimit[$networkType][$networkId]) : $this->content;
                        if ($this->allowHashTag) {
                            $postData['content'] .= $this->getHashTagsString();
                        }
                        $postData['custom_title'] = strip_tags($this->title);
                    } else {
                        return false;
                    }
                }

                if ($networkId == 7) {
                    if ($this->imageUrl !== false) {
                        $postData['custom_title'] = strip_tags($this->title);
                    } else {
                        return false;
                    }
                }
                if ($networkId == 8) {
                    $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], (int) $this->setPreFillTextLimit[$networkType][$networkId]) : $this->content;
                    $postData['custom_title'] = strip_tags($this->title);
                }
                if ($networkId == 9 || $networkId == 16) {
                    $postData['custom_title'] = $this->title;
                    $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], (int) $this->setPreFillTextLimit[$networkType][$networkId]) : $this->content;
                    if ($this->allowHashTag) {
                        if (is_array($this->keywords) && !empty($this->keywords)) {
                            foreach ($this->keywords as $tag) {
                                $postData['tags'][] = str_replace(" ", "", $tag->name);
                            }
                        }
                    }
                }

                if ($networkId == 10 || $networkId == 17 || $networkId == 18) {
                    $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], (isset($this->setPreFillTextLimit[$networkType][$networkId]) ? (int) $this->setPreFillTextLimit[$networkType][$networkId] : false)) : $this->content;
                    if ($this->allowHashTag) {
                        $postData['content'] .= $this->getHashTagsString();
                    }
                }

                if ($networkId == 11 || $networkId == 14) {
                    $postData['custom_title'] = strip_tags($this->title);
                    $postData['content'] = $this->contentHtml;
                }

                if ($networkId == 11) {
                    if ($this->allowHashTag) {
                        if (is_array($this->keywords) && !empty($this->keywords)) {
                            foreach ($this->keywords as $tag) {
                                $postData['tags'][] = str_replace(" ", "", $tag->name);
                            }
                        }
                    }
                }

                if ($networkId == 13 || $networkId == 15) {
                    $postData['content'] = strip_tags($this->title);
                }

                if ($networkId == 21) {
                    if ($this->imageUrl !== false) {
                        $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], (isset($this->setPreFillTextLimit[$networkType][$networkId]) ? (int) $this->setPreFillTextLimit[$networkType][$networkId] : false)) : $this->content;
                        $postData['custom_title'] = strip_tags($this->title);
                        if ($this->allowHashTag) {
                            if (is_array($this->keywords) && !empty($this->keywords)) {
                                foreach ($this->keywords as $tag) {
                                    $postData['tags'][] = str_replace(" ", "", $tag->name);
                                }
                            }
                        }
                    } else {
                        return false;
                    }
                }

                if ($networkId == 38) {
                    $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], (isset($this->setPreFillTextLimit[$networkType][$networkId]) ? (int) $this->setPreFillTextLimit[$networkType][$networkId] : false)) : $this->content;
                    $postData['custom_title'] = strip_tags($this->title);
                }
                if ($networkId == 39) { 
                    $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], (isset($this->setPreFillTextLimit[$networkType][$networkId]) ? (int) $this->setPreFillTextLimit[$networkType][$networkId] : false)) : $this->content;
                    $postData['custom_title'] = strip_tags($this->title);
                }
            }

            return $postData;
        }
        return false;
    }

    private function getHashTagsString($add = "\n\n", $limit = -1, $shuffle = false) {// limit = -1 => no limit
        if ($limit != 0) {
            $hashTags = '';
            if (is_array($this->keywords) && !empty($this->keywords)) {
                if ($shuffle) {
                    shuffle($this->keywords);
                }
                foreach ($this->keywords as $tag) {
                    $hashTags .= ' #' . str_replace(array(" ", "-", '"', "'", "!", "?", ",", ".", ";", ":"), "", $tag->name);
                }
            }
            if ($limit > 0) {
                $pos = 0;
                $temp_str = $hashTags;
                for ($i = 0; $i <= $limit; $i++) {
                    $pos = strpos($temp_str, '#');
                    $temp_str = substr($temp_str, $pos + 1);
                }
                if ($pos !== false) {
                    $pos = strpos($hashTags, $temp_str);
                    $hashTags = substr($hashTags, 0, $pos - 1);
                }
            }
            return (!empty($hashTags) ? (!empty($add) ? $add . trim($hashTags) : trim($hashTags)) : '');
        } else {
            return '';
        }
    }

    public function saveShareData($shareData = array(), $network_id = 0, $network_type = 0, $network_auth_id = 0, $shareApprove = 0, $network_display_name = '', $sched_date = '0000-00-00 00:00:00', $sched_date_utc = '0000-00-00 00:00:00') {

        global $wpdb;
        if ($this->userVersion >= 3) {
            $sqlGetData = $wpdb->prepare("SELECT `data` FROM `{$wpdb->prefix}b2s_posts_network_details` WHERE `network_auth_id` = %d", (int) $network_auth_id);
            $dataString = $wpdb->get_var($sqlGetData);
            if ($dataString !== NULL && !empty($dataString)) {
                $networkAuthData = unserialize($dataString);
                if (!empty($shareData['url']) && $networkAuthData != false && is_array($networkAuthData) && isset($networkAuthData['url_parameter'][0]['querys']) && !empty($networkAuthData['url_parameter'][0]['querys'])) {
                    $shareData['url'] = B2S_Util::addUrlParameter($shareData['url'], $networkAuthData['url_parameter'][0]['querys']);
                }
            }
        }

        if (isset($shareData['image_url']) && !empty($shareData['image_url']) && function_exists('wp_check_filetype') && defined('B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF')) {
            $image_data = wp_check_filetype($shareData['image_url']);
            $not_allow_gif = json_decode(B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF, true);
            if (isset($image_data['ext']) && $image_data['ext'] == 'gif' && is_array($not_allow_gif) && !empty($not_allow_gif) && in_array($network_id, $not_allow_gif)) {
                $shareData['image_url'] = '';
            }
        }

        $networkDetailsId = 0;
        $schedDetailsId = 0;
        $networkDetailsIdSelect = $wpdb->get_col($wpdb->prepare("SELECT postNetworkDetails.id FROM {$wpdb->prefix}b2s_posts_network_details AS postNetworkDetails WHERE postNetworkDetails.network_auth_id = %s", $network_auth_id));
        if (isset($networkDetailsIdSelect[0])) {
            $networkDetailsId = (int) $networkDetailsIdSelect[0];
        } else {
            $wpdb->insert($wpdb->prefix . 'b2s_posts_network_details', array(
                'network_id' => (int) $network_id,
                'network_type' => (int) $network_type,
                'network_auth_id' => (int) $network_auth_id,
                'network_display_name' => $network_display_name), array('%d', '%d', '%d', '%s'));
            $networkDetailsId = $wpdb->insert_id;
        }

        if ($networkDetailsId > 0) {
            //DeprecatedNetwork-8 31 march
            if ($network_id == 8 && $sched_date_utc >= '2019-03-30 23:59:59') {
                $wpdb->insert($wpdb->prefix . 'b2s_posts', array(
                    'post_id' => $this->postId,
                    'blog_user_id' => $this->blogUserId,
                    'user_timezone' => $this->userTimezone,
                    'publish_date' => date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate(gmdate('Y-m-d H:i:s'), $this->userTimezone * (-1)))),
                    'publish_error_code' => 'DEPRECATED_NETWORK_8',
                    'network_details_id' => $networkDetailsId), array('%d', '%d', '%s', '%s', '%s', '%d'));
            } else {
                $wpdb->insert($wpdb->prefix . 'b2s_posts_sched_details', array('sched_data' => serialize($shareData), 'image_url' => (isset($shareData['image_url']) ? $shareData['image_url'] : '')), array('%s', '%s'));
                $schedDetailsId = $wpdb->insert_id;
                $wpdb->insert($wpdb->prefix . 'b2s_posts', array(
                    'post_id' => $this->postId,
                    'blog_user_id' => $this->blogUserId,
                    'user_timezone' => $this->userTimezone,
                    'publish_date' => "0000-00-00 00:00:00",
                    'sched_details_id' => $schedDetailsId,
                    'sched_type' => 5, // Re-Posting
                    'sched_date' => $sched_date,
                    'sched_date_utc' => $sched_date_utc,
                    'network_details_id' => $networkDetailsId,
                    'post_for_approve' => (int) $shareApprove,
                    'hook_action' => (((int) $shareApprove == 0) ? 1 : 0),
                    'post_format' => (($shareData['post_format'] !== '') ? (((int) $shareData['post_format'] > 0) ? 1 : 0) : null)
                        ), array('%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%d'));
                B2S_Rating::trigger();
            }
        }
    }

    public function deletePostsByBlogPost($blogPostId = 0) {
        global $wpdb;
        $getSchedData = $wpdb->prepare("SELECT id as b2sPostId FROM {$wpdb->prefix}b2s_posts WHERE post_id = %d AND b.sched_type = %d AND b.publish_date = %s AND b.hide = %d", (int) $blogPostId, 5, "0000-00-00 00:00:00", 0);
        $schedDataResult = $wpdb->get_results($getSchedData);
        $delete_scheds = array();
        foreach ($schedDataResult as $k => $value) {
            array_push($delete_scheds, $value->b2sPostId);
        }
        if (!empty($delete_scheds)) {
            require_once (B2S_PLUGIN_DIR . '/includes/B2S/Post/Tools.php');
            B2S_Post_Tools::deleteUserSchedPost($delete_scheds);
            B2S_Heartbeat::getInstance()->deleteSchedPost();
        }
    }

}
