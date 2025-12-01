<?php

class B2S_QuickPost {

    private $setPreFillText = array(0 => array(1 => 239, 2 => 255, 3 => 239, 6 => 300, 12 => 240, 17 => 442, 19 => 239, 36 => 200, 38 => 500, 39 => 2000, 42 => 1000, 43 => 279, 44 => 300, 45 => 255, 46 => 500), 1 => array(1 => 239, 3 => 239, 6 => 300, 17 => 442, 19 => 5000, 42 => 1000), 2 => array(1 => 239, 17 => 442, 19 => 239));
    private $setPreFillTextLimit = array(0 => array(1 => 500, 2 => 254, 3 => 400, 6 => 400, 12 => 400, 19 => 400, 36 => 400, 38 => 500, 39 => 2000, 42 => 1000, 43 => 279, 44 => 400, 45 => 254 , 46 => 1000), 1 => array(1 => 400, 3 => 400, 6 => 400, 19 => 60000, 42 => 1000), 2 => array(1 => 400, 19 => 9000));
    private $content;
    private $title;
    private $template;

    public function __construct($content = '', $title = '') {
        $this->content = sanitize_textarea_field($content);
        $this->title = sanitize_text_field($title);
        $this->template = ((defined('B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT')) ? unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT) : false);
    }

    public function prepareShareData($networkAuthId = 0, $networkId = 0, $networkType = 0, $postFormat = 0) {
        if ((int) $networkId > 0 && (int) $networkAuthId > 0) {

            $postData = array('content' => '', 'custom_title' => '', 'tags' => array(), 'network_auth_id' => (int) $networkAuthId);

            //PostFormat
            if (in_array($networkId, array(1, 2, 3, 12, 19, 24, 43, 44, 45))) {
                $postData['post_format'] = $postFormat;
            }

            //Content
            $limit = ((is_array($this->template) && isset($this->template[$networkId][$networkType]['short_text']['limit'])) ? $this->template[$networkId][$networkType]['short_text']['limit'] : (isset($this->setPreFillTextLimit[$networkType][$networkId]) ? (int) $this->setPreFillTextLimit[$networkType][$networkId] : false));
            $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], $limit) : $this->content;
            if ($networkId == 7 || $networkId == 9 || ($networkId == 8 && $networkType != 0) | ($networkId == 19 && $networkType != 0) || $networkId == 39 || $networkId == 36) {
                $postData['custom_title'] = $this->title;
            }
            if ($networkId == 15) {
                $postData['content'] = $this->title;
            }
            return $postData;
        }
        return false;
    }

    public function prepareShareDataFromTemplate($networkAuthId = 0, $networkId = 0, $networkType = 0, $postFormat = 0, $networkKind = 0) {
        
        if ((int) $networkId > 0 && (int) $networkAuthId > 0) {

            $postData = array('content' => '', 'custom_title' => '', 'tags' => array(), 'network_auth_id' => (int) $networkAuthId);
            //PostFormat
            if (in_array($networkId, array(1, 2, 3, 12, 19, 24, 43, 44, 45))) {
                $postData['post_format'] = $postFormat;
            }

            require_once(B2S_PLUGIN_DIR . 'includes/B2S/Ship/Item.php');
            
            $b2sItem = new B2S_Ship_Item(0,'en','','',0,false,array(),false,true);
            $emptyPostData = new stdClass();
            $emptyPostData->post_content = '';
            $emptyPostData->post_type = '';
            $b2sItem->setPostData($emptyPostData);
            $networkData = array(
                'networkId' => $networkId,
                'networkType' => $networkType,
                'networkKind' => $networkKind
            );
            
            $message= $b2sItem->getMessagebyTemplate((object) $networkData, $this->content);

            $postData['content'] = $message;

            if ($networkId == 7 || $networkId == 9 || ($networkId == 8 && $networkType != 0) | ($networkId == 19 && $networkType != 0) || $networkId == 39 || $networkId == 36) {
                $postData['custom_title'] = $this->title;
            }
            if ($networkId == 15) {
                $postData['content'] = $this->title;
            }
            return $postData;
        }

        return false;
    }

     public function getMessagebyTemplate($data, $customText = '', $keepHashTags = false) {

        if (!isset($this->post_template) || empty($this->post_template)) {
            $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
            $this->post_template = $this->options->_getOption("post_template");
            if (B2S_PLUGIN_USER_VERSION < 1 || $this->post_template == false || !isset($this->post_template[$data->networkId][$data->networkType])) {
                $this->post_template = $this->default_template;
            }
        }

        if (isset($customText) && !empty($customText)) {
            $this->postData->post_content = $customText;
        }
        $post_template = $this->post_template[$data->networkId][$data->networkType];

//V6.5.5 - Xing Pages => Two diferend kinds
        if ($data->networkId == 19 && $data->networkType == 1 && (int) $data->networkKind >= 0) {
            if (!isset($post_template['short_text'][0]['limit'])) {
                if (isset($post_template['short_text']['limit'])) {
                    if (isset($this->default_template[$data->networkId][$data->networkType]['short_text'][4])) {
                        $post_template['short_text'] = array(0 => $post_template['short_text'], 4 => $this->default_template[$data->networkId][$data->networkType]['short_text'][4]);
                    }
                } else {
                    if (isset($this->default_template[$data->networkId][$data->networkType])) {
                        $post_template = $this->default_template[$data->networkId][$data->networkType];
                    }
                }
            }
            $content_min = (isset($post_template['short_text'][$data->networkKind]['range_min'])) ? $post_template['short_text'][$data->networkKind]['range_min'] : 0;
            $content_max = (isset($post_template['short_text'][$data->networkKind]['range_max'])) ? $post_template['short_text'][$data->networkKind]['range_max'] : 0;
            $excerpt_min = (isset($post_template['short_text'][$data->networkKind]['excerpt_range_min'])) ? $post_template['short_text'][$data->networkKind]['excerpt_range_min'] : 0;
            $excerpt_max = (isset($post_template['short_text'][$data->networkKind]['excerpt_range_max'])) ? $post_template['short_text'][$data->networkKind]['excerpt_range_max'] : 0;
            $limit = (isset($post_template['short_text'][$data->networkKind]['limit'])) ? $post_template['short_text'][$data->networkKind]['limit'] : 0;
        } else {
//V5.6.1
            if (!isset($post_template['short_text']['excerpt_range_max'])) {
                if (isset($this->default_template[$data->networkId][$data->networkType]['short_text']['excerpt_range_max'])) {
                    $post_template['short_text']['excerpt_range_max'] = $this->default_template[$data->networkId][$data->networkType]['short_text']['excerpt_range_max'];
                }
            }
            if (!isset($post_template['short_text']['excerpt_range_min'])) {
                if (isset($this->default_template[$data->networkId][$data->networkType]['short_text']['excerpt_range_min'])) {
                    $post_template['short_text']['excerpt_range_min'] = $this->default_template[$data->networkId][$data->networkType]['short_text']['excerpt_range_min'];
                }
            }
            $content_min = (isset($post_template['short_text']['range_min'])) ? $post_template['short_text']['range_min'] : 0;
            $content_max = (isset($post_template['short_text']['range_max'])) ? $post_template['short_text']['range_max'] : 0;
            $excerpt_min = (isset($post_template['short_text']['excerpt_range_min'])) ? $post_template['short_text']['excerpt_range_min'] : 0;
            $excerpt_max = (isset($post_template['short_text']['excerpt_range_max'])) ? $post_template['short_text']['excerpt_range_max'] : 0;
            $limit = (isset($post_template['short_text']['limit'])) ? $post_template['short_text']['limit'] : 0;
        }

        $message = $post_template['content'];

        //B2S CC
        if ($this->b2sPostType == 'ex' && isset($this->postData->post_content) && !empty($this->postData->post_content)) {
            $message = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang), (int) $content_min, (int) $content_max);

            //B2S Customize
        } else {
            if (isset($this->postData->post_content) && !empty($this->postData->post_content)) {
                $preContent = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, (in_array($data->networkId, $this->allowHtml) ? '<p><h1><h2><br><i><b><a><img>' : false), (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang), (int) $content_min, (int) $content_max);
                $message = preg_replace("/\{CONTENT\}/", addcslashes($preContent, "\\$"), $message);
            } else {
                $message = preg_replace("/\{CONTENT\}/", "", $message);
            }

            if (isset($this->postData->post_title) && !empty($this->postData->post_title)) {
                $title = in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang);
                $message = stripslashes(preg_replace("/\{TITLE\}/", addcslashes($title, "\\$"), $message));
            } else {
                $message = preg_replace("/\{TITLE\}/", "", $message);
            }

            if (isset($this->postData->post_excerpt) && !empty($this->postData->post_excerpt)) {
                $excerpt = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_excerpt, $this->postUrl, false, (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang), (int) $excerpt_min, (int) $excerpt_max);
                $message = stripslashes(preg_replace("/\{EXCERPT\}/", addcslashes($excerpt, "\\$"), $message));
            } else {
                $message = preg_replace("/\{EXCERPT\}/", "", $message);
            }

            if (strpos($message, "{KEYWORDS}") !== false) {

                if ($this->default_template != false && (isset($this->default_template[$data->networkId][$data->networkType]['disableKeywords']) && $this->default_template[$data->networkId][$data->networkType]['disableKeywords'] == true) || (isset($this->default_template[$data->networkId][$data->networkType]['separateKeywords']) && $this->default_template[$data->networkId][$data->networkType]['separateKeywords'] == true)) {
                    $message = stripslashes(preg_replace("/\{KEYWORDS\}/", '', $message));
                } else {
                    if (isset($data->custom_hashtags) && !empty($data->custom_hashtags)) {
                        $hashtags = $data->custom_hashtags;
                    } else {
                        $hashtags = $this->getHashTagsString("", ((isset($this->limitHashTagCharacter[$data->networkId])) ? $this->limitHashTagCharacter[$data->networkId] : 0), ((isset($post_template['shuffleHashtags']) && $post_template['shuffleHashtags'] == true) ? true : false));
                    }
                    $message = stripslashes(preg_replace("/\{KEYWORDS\}/", addcslashes($hashtags, "\\$"), $message));
                }
            }


            if (isset($this->postData->post_author) && (int) $this->postData->post_author > 0) {
                $author_name = $this->hook_filter->get_wp_user_post_author_display_name((int) $this->postData->post_author);
                $message = stripslashes(preg_replace("/\{AUTHOR\}/", addcslashes($author_name, "\\$"), $message));
            } else {
                $message = preg_replace("/\{AUTHOR\}/", "", $message);
            }

            if (class_exists('WooCommerce') && function_exists('wc_get_product')) {
                $wc_product = wc_get_product($this->postId);
                if ($wc_product != false) {
                    $price = $wc_product->get_price();
                    if ($price != false && !empty($price)) {
                        $message = stripslashes(preg_replace("/\{PRICE\}/", addcslashes($price, "\\$"), $message));
                    }
                }
            }
            $message = preg_replace("/\{PRICE\}/", "", $message);

            $taxonomieReplacements = $this->hook_filter->get_posting_template_set_taxonomies(array(), $this->postId);
            if (is_array($taxonomieReplacements) && !empty($taxonomieReplacements)) {
                foreach ($taxonomieReplacements as $taxonomie => $replacement) {
                    $message = preg_replace("/\{" . $taxonomie . "\}/", $replacement, $message);
                }
            }
        }

        if (in_array($data->networkId, $this->allowHtml)) {
            $message = preg_replace("/\\n/", "<br>", $message);
        }

        if (isset($limit) && (int) $limit > 0) {
            if (!empty($this->postUrl) && ($data->networkId == 2 || $data->networkId == 45)) {
                $limit = 254;
            }
            if (!empty($this->postUrl) && $data->networkId == 38) {
                $limit = 500 - strlen($this->postUrl);
            }
            if (!empty($this->postUrl) && $data->networkId == 44) {
                $limit = 500 - strlen($this->postUrl);
            }
            if (!empty($this->postUrl) && $data->networkId == 43 && isset($post_template['format']) && (int) $post_template['format'] == 1) {
                $limit = 300 - B2S_Util::getNetwork43UrlLength($this->postUrl);
            }

            if ($keepHashTags) {

                $message = B2S_Util::getExcerpt($message, 0, $limit, false, array('.', '?', '!', '('));

                if (!str_contains($message, '#')) {

                    if (isset($data->custom_hashtags) && !empty($data->custom_hashtags)) {
                        $hashtags = $data->custom_hashtags;
                    } else {
                        $hashtags = $this->getHashTagsString("", ((isset($this->limitHashTagCharacter[$data->networkId])) ? $this->limitHashTagCharacter[$data->networkId] : 0), ((isset($post_template['shuffleHashtags']) && $post_template['shuffleHashtags'] == true) ? true : false));
                    }

                    $hashLength = mb_strlen($hashtags);
                    $limit = $limit - $hashLength + 1;
                    $message = B2S_Util::getExcerpt($message, 0, $limit, false, array('.', '?', '!', '('));
                    $message = $message . " " . $hashtags;
                }
            } else {
                $message = B2S_Util::getExcerpt($message, 0, $limit);
            }
        }

        return $message;
    }











}
