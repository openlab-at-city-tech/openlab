<?php

class B2S_AutoPost {

    private $title;
    private $contentHtml;
    private $postId;
    private $content;
    private $excerpt;
    private $url;
    private $imageUrl;
    private $keywords;
    private $blogPostData = array();
    private $myTimeSettings = array();
    private $current_user_date;
    private $setPreFillText;
    private $optionPostFormat;
    private $allowHashTag;
    private $userVersion;
    private $allowHtml = array(4, 11, 14, 25);
    private $default_template;

    function __construct($postId = 0, $blogPostData = array(), $current_user_date = '0000-00-00 00:00:00', $myTimeSettings = false, $title = '', $content = '', $excerpt = '', $url = '', $imageUrl = '', $keywords = '', $b2sPostLang = 'en', $optionPostFormat = array(), $allowHashTag = true, $userVersion = 0) {
        $this->postId = $postId;
        $this->blogPostData = $blogPostData;
        $this->current_user_date = $current_user_date;
        $this->myTimeSettings = $myTimeSettings;
        $this->title = $title;
        $this->content = B2S_Util::prepareContent($postId, $content, $url, false, true, $b2sPostLang);
        $this->excerpt = B2S_Util::prepareContent($postId, $excerpt, $url, false, true, $b2sPostLang);
        $this->contentHtml = B2S_Util::prepareContent($postId, $content, $url, '<p><h1><h2><br><i><b><a><img>', true, $b2sPostLang);
        $this->url = $url;
        $this->userVersion = defined("B2S_PLUGIN_USER_VERSION") ? B2S_PLUGIN_USER_VERSION : (int) $userVersion;
        $this->imageUrl = $imageUrl;
        $this->keywords = $keywords;
        $this->optionPostFormat = $optionPostFormat;
        $this->allowHashTag = $allowHashTag;
        $this->setPreFillText = array(0 => array(6 => 300, 16 => 250, 17 => 442, 18 => 800, 21 => 65000, 38 => 500, 39 => 2000), 1 => array(6 => 300, 17 => 442, 19 => 239), 2 => array(17 => 442, 19 => 239), 20 => 300);
        $this->setPreFillTextLimit = array(0 => array(6 => 400, 18 => 1000, 16 => false, 21 => 65535, 38 => 500, 39 => 2000), 1 => array(6 => 400, 19 => 400), 2 => array(19 => 9000));
        $this->default_template = (defined('B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT')) ? unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT) : false;
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
            if($networkId == 19 && $networkType == 1) {
                if(!isset($tempOptionPostFormat[$networkId][$networkType]['short_text'][0]['limit'])) {
                    if(isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['limit'])) {
                        if(isset($this->default_template[$networkId][$networkType]['short_text'][4])) {
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
                if (!isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_max']) && isset($this->default_template[$networkId][$networkType]['short_text']['excerpt_range_max'])) {
                    $tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_max'] = $this->default_template[$networkId][$networkType]['short_text']['excerpt_range_max'];
                }
                if (!isset($tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_min']) && isset($this->default_template[$networkId][$networkType]['short_text']['excerpt_range_min'])) {
                    $tempOptionPostFormat[$networkId][$networkType]['short_text']['excerpt_range_min'] = $this->default_template[$networkId][$networkType]['short_text']['excerpt_range_min'];
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
                    $postData['post_format'] = isset($this->default_template[$networkId][$networkType]['format']) ? (int) $this->default_template[$networkId][$networkType]['format'] : 0;
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
                    if($this->default_template != false && isset($this->default_template[$networkId][$networkType]['disableKeywords']) && $this->default_template[$networkId][$networkType]['disableKeywords'] == true) {
                        $postData['content'] = preg_replace("/\{KEYWORDS\}/",'', $postData['content']);
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
                    $postData['content'] = preg_replace("/\{AUTHOR\}/","", $postData['content']);
                }
                
                if (class_exists('WooCommerce') && function_exists('wc_get_product')) {
                    $wc_product = wc_get_product($this->postId);
                    if($wc_product != false) {
                        $price = $wc_product->get_price();
                        if($price != false && !empty($price)) {
                            $postData['content'] = stripslashes(preg_replace("/\{PRICE\}/", addcslashes($price, "\\$"), $postData['content']));
                        }
                    }
                }
                $postData['content'] = preg_replace("/\{PRICE\}/","", $postData['content']);
                
                $taxonomieReplacements = $hook_filter->get_posting_template_set_taxonomies(array(), $this->postId);
                if(is_array($taxonomieReplacements) && !empty($taxonomieReplacements)) {
                    foreach ($taxonomieReplacements as $taxonomie => $replacement) {
                        $postData['content'] = preg_replace("/\{".$taxonomie."\}/", $replacement, $postData['content']);
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
                    if(!empty($this->url) && $networkId == 2) {
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

                if ($networkId == 6) {
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

    public function saveShareData($shareData = array(), $network_id = 0, $network_type = 0, $network_auth_id = 0, $shareApprove = 0, $network_display_name = '') {

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

        $sched_type = $this->blogPostData['sched_type'];
        $sched_date = $this->blogPostData['sched_date'];
        $sched_date_utc = $this->blogPostData['sched_date_utc'];

        //Scheduling post once with user times 
        if ($sched_type == 2 && $this->myTimeSettings !== false && is_array($this->myTimeSettings) && isset($this->myTimeSettings['times']) && is_array($this->myTimeSettings['times']) && isset($this->myTimeSettings['type'])) {
            //V 5.1.0 Seeding
            //0=default(best time), 1= special per account (seeding), 2= per network (old)
            //Check My Time Setting in Past
            //new
            if ($this->myTimeSettings['type'] == 1) {
                if (isset($this->myTimeSettings['times']['delay_day'][$network_auth_id]) && isset($this->myTimeSettings['times']['time'][$network_auth_id]) && !empty($this->myTimeSettings['times']['time'][$network_auth_id])) {
                    $tempSchedDate = date('Y-m-d', strtotime($sched_date));
                    $networkSchedDate = date('Y-m-d H:i:00', strtotime($tempSchedDate . ' ' . $this->myTimeSettings['times']['time'][$network_auth_id]));
                    if ($this->myTimeSettings['times']['delay_day'][$network_auth_id] > 0) {
                        $sched_date = date('Y-m-d H:i:s', strtotime('+' . $this->myTimeSettings['times']['delay_day'][$network_auth_id] . ' days', strtotime($networkSchedDate)));
                        $sched_date_utc = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($sched_date, $this->blogPostData['user_timezone'] * (-1))));
                    } else {
                        if ($networkSchedDate >= $sched_date) {
                            //Scheduling
                            $sched_date = $networkSchedDate;
                            $sched_date_utc = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($sched_date, $this->blogPostData['user_timezone'] * (-1))));
                        } else {
                            //Scheduling on next Day by Past
                            $sched_date = date('Y-m-d H:i:s', strtotime('+1 days', strtotime($networkSchedDate)));
                            $sched_date_utc = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($sched_date, $this->blogPostData['user_timezone'] * (-1))));
                        }
                    }
                }
                //old  or default (best time)   
            } else {
                foreach ($this->myTimeSettings['times'] as $k => $v) {
                    if ($v->network_id == $network_id && $v->network_type == $network_type) {
                        if (isset($v->sched_time) && !empty($v->sched_time)) {
                            $tempSchedDate = date('Y-m-d', strtotime($sched_date));
                            $networkSchedDate = date('Y-m-d H:i:00', strtotime($tempSchedDate . ' ' . $v->sched_time));
                            if ($networkSchedDate >= $sched_date) {
                                //Scheduling
                                $sched_date = $networkSchedDate;
                                $sched_date_utc = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($sched_date, $this->blogPostData['user_timezone'] * (-1))));
                            } else {
                                //Scheduling on next Day by Past
                                $sched_date = date('Y-m-d H:i:s', strtotime('+1 days', strtotime($networkSchedDate)));
                                $sched_date_utc = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($sched_date, $this->blogPostData['user_timezone'] * (-1))));
                            }
                        }
                    }
                }
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
                    'blog_user_id' => $this->blogPostData['blog_user_id'],
                    'user_timezone' => $this->blogPostData['user_timezone'],
                    'publish_date' => date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate(gmdate('Y-m-d H:i:s'), $this->blogPostData['user_timezone'] * (-1)))),
                    'publish_error_code' => 'DEPRECATED_NETWORK_8',
                    'network_details_id' => $networkDetailsId), array('%d', '%d', '%s', '%s', '%s', '%d'));
            } else {
                $wpdb->insert($wpdb->prefix . 'b2s_posts_sched_details', array('sched_data' => serialize($shareData), 'image_url' => (isset($shareData['image_url']) ? $shareData['image_url'] : '')), array('%s', '%s'));
                $schedDetailsId = $wpdb->insert_id;
                $wpdb->insert($wpdb->prefix . 'b2s_posts', array(
                    'post_id' => $this->postId,
                    'blog_user_id' => $this->blogPostData['blog_user_id'],
                    'user_timezone' => $this->blogPostData['user_timezone'],
                    'publish_date' => (($sched_type == 3) ? $sched_date : "0000-00-00 00:00:00"), // selection for view publish / scheduled posts
                    'sched_details_id' => $schedDetailsId,
                    'sched_type' => 3, // Auto-Posting direkt or scheduled by wp post
                    'sched_date' => $sched_date,
                    'sched_date_utc' => $sched_date_utc,
                    'network_details_id' => $networkDetailsId,
                    'post_for_approve' => (int) $shareApprove,
                    'hook_action' => (((int) $shareApprove == 0) ? 1 : 0),
                    'post_format' => ((isset($shareData['post_format']) && $shareData['post_format'] !== '') ? (((int) $shareData['post_format'] > 0) ? 1 : 0) : null)
                        ), array('%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%d'));
                $insertId = $wpdb->insert_id;
                B2S_Rating::trigger();
                return $insertId;
            }
        }
        return false;
    }

}
