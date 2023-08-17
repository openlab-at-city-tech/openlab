<?php

class B2S_Ship_Item {

    private $allowTitleProfile = array(6, 7, 9, 15, 16, 21, 25, 26, 37, 39);
    private $allowTitlePage = array(6);
    private $isInstantSharing = array(36);
    private $allowTitleGroup = array();
    private $setPostFormat = array(1, 2, 3, 12, 19, 17, 24);
    private $isCommentProfile = array(1, 3, 15, 17, 19);
    private $isCommentPage = array(1);
    private $isCommentGroup = array(1);
    private $allowTag = array(4, 7, 9, 11, 16, 32, 37);
    private $limitTag = array(11 => 5, 7 => 75); //networkId => Limit
    private $allowHtml = array(4, 11, 14, 25);
    private $showTitleProfile = array(4, 6, 9, 11, 14, 16, 21, 15, 25, 26, 27, 32, 35, 37, 39);
    private $showTitlePage = array(6, 8, 19 => array(1), 37); //Xing Business Page
    private $showTitleGroup = array(8, 11, 19);
    private $onlyImage = array(6, 7, 12, 21);
    private $allowNoImageProfile = array(9);
    private $allowNoCustomImageProfile = array();
    private $allowNoCustomImagePage = array();
    private $allowPrivacyStatus = array(32);
    private $allowNoEmoji = array(9, 13, 14, 15, 16, 21, 35, 37);
    private $allowNoImagePage = array(8);
    private $allowEditUrl = array(1, 2, 3, 4, 6, 7, 9, 11, 12, 14, 15, 16, 17, 18, 19, 21, 24, 25, 26, 27, 37, 38, 39);
    private $showBoards = array(6, 20);
    private $showRelay = array(2);
    private $showBoardsGroup = array(10);
    private $showGroups = array(15, 19);
    private $changeDisplayName = array(8);
    private $allowImageEditor = array(1, 2, 3, 6, 7, 12, 15, 18, 17, 19, 24, 26); // Cropper
    private $setShortTextProfile = array(1 => 239, 2 => 255, 3 => 239, 6 => 300, 9 => 200, 12 => 240, 16 => 250, 17 => 442, 18 => 800, 19 => 239, 21 => 500);
    private $setShortTextProfileLimit = array(1 => 400, 2 => 254, 3 => 400, 6 => 400, 9 => 200, 12 => 400, 18 => 1000, 21 => 600);
    private $setShortTextPage = array(1 => 239, 3 => 239, 6 => 300, 17 => 442, 19 => 239);
    private $setShortTextPageLimit = array(1 => 400, 3 => 400, 6 => 400, 19 => array(0 => 400, 1 => 2000));
    private $limitCharacterTitle = array(15 => array(0 => 300), 19 => array(1 => 150), 32 => array(0 => 100), 39 => array(0 => 256));
    private $setShortTextGroup = array(1 => 239, 17 => 442, 19 => 239);
    private $setShortTextGroupLimit = array(1 => 400);
    private $allowHashTags = array(1, 2, 3, 6, 12, 17, 21, 37);
    private $limitHashTagCharacter = array(21 => 36);
    private $limitCharacterProfile = array(1 => 500, 2 => 280, 3 => 3000, 6 => 495, 12 => 2000, 18 => 1500, 20 => 495, 21 => 65535, 38 => 500, 39 => 2000);
    private $showImageAreaProfile = array(6, 7, 12, 16, 18, 21, 26, 37, 38, 39);
    private $showImageAreaPage = array(6, 12);
    private $showImageAreaGroup = array();
    private $showMarketplace = array(19);
    private $limitCharacterPage = array(3 => 3000, 6 => 495, 12 => 2200, 19 => array(0 => 400, 1 => 2000, 4 => 1000));
    private $limitCharacterGroup = array(19 => 10000);
    private $requiredUrl = array(1, 3, 9, 19, 27);
    private $getText = array(1, 7, 12, 16, 17, 18, 21);
    private $allowSchedCustomizeContent = array(1, 2, 3, 6, 7, 9, 12, 15, 17, 18, 19, 21, 24);
    private $maxWeekTimeSelect = 52;
    private $networkTosProfile = array(2);
    private $networkTosGroup = array(19);
    private $maxMonthTimeSelect = 12;
    private $maxTimeSelect = 50;
    private $maxSchedCount = 3;
    private $selMarketplaceCategory = 3;
    private $selMarketplaceType = 1;
    private $selGroup = null;
    private $selBoard = null;
    private $setRelayCount = 0;
    private $maxDaySelect = 31;
    private $noScheduleRegularly = array(2, 4, 11, 14, 15, 18);
    private $noScheduleRegularlyPage = array(19);
    private $addNoMoreSchedPage = array(19);
    private $addNoMoreSchedGroup = array(19);
    private $defaultImage;
    private $postData;
    private $postUrl;
    private $postStatus;
    private $websiteName;
    private $postId;
    private $userLang;
    private $selSchedDate;
    private $viewMode;
    private $b2sPostType;
    private $options;
    private $post_template = array();
    private $hook_filter;
    private $default_template;
    private $isVideoMode;
    private $canReel; // NOTE $this->canReel['result'] = true
    private $videoScheduleNetworks = array(1, 2, 3, 6, 12, 32, 35); //NOTE Nur video Netzwerke der Video API
    
    public function __construct($postId, $userLang = 'en', $selSchedDate = "", $b2sPostType = "", $relayCount = 0, $isVideoMode = false, $canReel = array()) {
        $this->postId = $postId;
        $this->postData = get_post($this->postId);
        $this->postStatus = $this->postData->post_status;
        $this->websiteName = get_option('blogname');
        $this->b2sPostType = (!empty($b2sPostType) ? $b2sPostType : ( (isset($this->postData->post_type) && $this->postData->post_type == 'b2s_ex_post') ? 'ex' : ''));
        $this->postUrl = ($this->b2sPostType == 'ex') ? ((stripos($this->postData->guid, 'b2s_ex_post') != false) ? '' : $this->postData->guid) : (get_permalink($this->postData->ID) !== false ? get_permalink($this->postData->ID) : $this->postData->guid);
        $this->userLang = $userLang;
        $this->hook_filter = new B2S_Hook_Filter();
        $this->selSchedDate = $selSchedDate;
        $this->setRelayCount = $relayCount;
        $this->isVideoMode = $isVideoMode;
        $this->canReel = $canReel;
        $this->default_template = (defined('B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT')) ? unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT) : false;
    }

    protected function getPostId() {
        return $this->postId;
    }

    public function getItemHtml($data, $show_time = true, $draftData = array()) {

        $isDraft = (empty($draftData)) ? false : true;

        $this->viewMode = (isset($data->view) && !empty($data->view)) ? $data->view : null;  //normal or modal(Kalendar)
        //Override data values from edit modus
        $schedMetaData = ($this->viewMode == 'modal') ? $this->hook_sched_data(array()) : array();
        if (!empty($schedMetaData) && is_array($schedMetaData)) {
            $data->instantSharing = 0;
            if (isset($schedMetaData['network_kind'])) {
                $data->networkKind = $schedMetaData['network_kind'];
            }
            if (isset($schedMetaData['network_tos_group_id'])) {
                $data->networkTosGroupId = $schedMetaData['network_tos_group_id'];
            }
            if (isset($schedMetaData['marketplace_category'])) {
                $this->selMarketplaceCategory = (int) $schedMetaData['marketplace_category'];
            }
            if (isset($schedMetaData['marketplace_type'])) {
                $this->selMarketplaceType = (int) $schedMetaData['marketplace_type'];
            }
            if (isset($schedMetaData['group'])) {
                $this->selGroup = $schedMetaData['group'];
            }
            if (isset($schedMetaData['board'])) {
                $this->selBoard = $schedMetaData['board'];
            }
        }

        $networkName = unserialize(B2S_PLUGIN_NETWORK);
        $networkTypeName = unserialize(B2S_PLUGIN_NETWORK_TYPE);
        $networkTypeNameOverride = unserialize(B2S_PLUGIN_NETWORK_TYPE_INDIVIDUAL);
        $networkKindName = unserialize(B2S_PLUGIN_NETWORK_KIND);
        $limit = false;
        $limitValue = 0;
        $textareaLimitInfo = "";
        $textareaOnKeyUp = "";
        $this->defaultImage = plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE);
        if (B2S_PLUGIN_USER_VERSION >= 3) {
            global $wpdb;
            $sqlGetData = $wpdb->prepare("SELECT `data` FROM `{$wpdb->prefix}b2s_posts_network_details` WHERE `network_auth_id` = %d", (int) $data->networkAuthId);
            $dataString = $wpdb->get_var($sqlGetData);
            if ($dataString !== NULL && !empty($dataString)) {
                $networkAuthData = unserialize($dataString);
                if (!empty($this->postUrl) && $networkAuthData != false && is_array($networkAuthData) && isset($networkAuthData['url_parameter'][0]['querys']) && !empty($networkAuthData['url_parameter'][0]['querys'])) {
                    $this->postUrl = B2S_Util::addUrlParameter($this->postUrl, $networkAuthData['url_parameter'][0]['querys']);
                }
            }
        }

        if (!$this->isVideoMode) {
            $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
            $this->post_template = $this->options->_getOption("post_template");
            if (B2S_PLUGIN_USER_VERSION < 1 || $this->post_template == false || !isset($this->post_template[$data->networkId][$data->networkType])) {
                $this->post_template = $this->default_template;
            }
        }

//Settings
        switch ($data->networkType) {
            case '0':
//profil
                if (isset($this->limitCharacterProfile[$data->networkId]) && (int) $this->limitCharacterProfile[$data->networkId] > 0) {
                    $limitValue = $this->limitCharacterProfile[$data->networkId];
                    $limit = true;
                    if ($data->networkId == 2 && defined('B2S_PLUGIN_USER_VERSION') && B2S_PLUGIN_USER_VERSION >= 2) {
                        $limitValue = 0;
                        $limit = false;
                    }
                }
                $infoImage = (in_array($data->networkId, $this->allowNoImageProfile)) ? esc_html__('Network does not support image for profiles', 'blog2social') . '!' : '';
                $infoImage .= (in_array($data->networkId, $this->allowNoCustomImageProfile)) ? (!empty($infoImage) ? ' | ' : '') . esc_html__('Network defines image by link', 'blog2social') . '!' : '';
                $htmlTags = highlight_string("<p><br><i><b><a><img>", true);
                $infoImage .= (in_array($data->networkId, $this->allowHtml)) ? (!empty($infoImage) ? ' | ' : '') . esc_html__('Supported HTML tags', 'blog2social') . ': ' . $htmlTags : '';
                $infoImage .= (in_array($data->networkId, $this->allowNoEmoji)) ? (!empty($infoImage) ? ' | ' : '') . esc_html__('Network does not support emojis', 'blog2social') . '!' : '';
                $notAllowGif = ((defined('B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF')) ? json_decode(B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF, true) : false);
                $infoImage .= (is_array($notAllowGif) && in_array($data->networkId, $notAllowGif)) ? (!empty($infoImage) ? ' | ' : '') . esc_html__('Network does not support GIFs', 'blog2social') . '!' : '';

                $network_display_name = $data->network_display_name;
                $isRequiredTextarea = (in_array($data->networkId, $this->isCommentProfile)) ? '' : 'required="required"';

//ShortText
                if ($isDraft && isset($draftData['content'])) {
                    $message = $draftData['content'];
                } else {
                    if (array_key_exists($data->networkId, $this->post_template)) {
                        $message = $this->getMessagebyTemplate($data);
                        //Feature Image Html-Network
                        if (in_array($data->networkId, $this->allowHtml)) {
                            $featuredImage = wp_get_attachment_url(get_post_thumbnail_id($this->postId));
                            if ($featuredImage !== false) {
                                $title = in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang);
                                $message = '<img class="b2s-post-item-details-image-html-src" src="' . esc_url($featuredImage) . '" alt="' . esc_attr($title) . '"/><br>' . $message;
                            }
                        }
                    } else {
                        if (isset($this->setShortTextProfile[$data->networkId]) && (int) $this->setShortTextProfile[$data->networkId] > 0) {
                            $preContent = B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang);
                            $message = B2S_Util::getExcerpt($preContent, (int) $this->setShortTextProfile[$data->networkId], (isset($this->setShortTextProfileLimit[$data->networkId]) ? (int) $this->setShortTextProfileLimit[$data->networkId] : false));
                        } else {
                            $message = (in_array($data->networkId, $this->allowTitleProfile) ? (in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, (in_array($data->networkId, $this->allowHtml) ? '<p><h1><h2><br><i><b><a><img>' : false), (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang));
                        }

                        //Feature Image Html-Network
                        if (in_array($data->networkId, $this->allowHtml)) {
                            $featuredImage = wp_get_attachment_url(get_post_thumbnail_id($this->postId));
                            if ($featuredImage !== false) {
                                $title = in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang);
                                $message = '<img class="b2s-post-item-details-image-html-src" src="' . esc_url($featuredImage) . '" alt="' . esc_attr($title) . '"/><br>' . $message;
                            }
                        }

                        //Hashtags
                        if (in_array($data->networkId, $this->allowHashTags)) {
                            $add = ($data->networkId != 2) ? "\n\n" : "";
                            $message .= $this->getHashTagsString($add, ((isset($this->limitHashTagCharacter[$data->networkId])) ? $this->limitHashTagCharacter[$data->networkId] : 0));
                        }
                    }
                }
                $message = $this->hook_message($message);

                if ($this->b2sPostType == 'ex' && in_array($data->networkId, $this->showTitleProfile)) {
                    $messageParts = preg_split('/\n/', $message);
                    if (count($messageParts) > 1 && isset($messageParts[0]) && isset($messageParts[1])) {
                        $title = $messageParts[0];
                        unset($messageParts[0]);
                        $message = implode("\n", $messageParts);
                    }
                }

                $countCharacter = 0;
                if ($limit !== false) {
                    $infoCharacterCount = ($data->networkId != 2 && $data->networkId != 3 && $data->networkId != 19 && $data->networkId != 21) ? ' (' . esc_html__('Text only', 'blog2social') . ')' : '';
                    $textareaLimitInfo .= '<span class="b2s-post-item-countChar" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . (int) esc_html($countCharacter) . '</span>/' . esc_html($limitValue) . ' ' . esc_html__('characters', 'blog2social') . $infoCharacterCount . '</span>';
                    $textareaOnKeyUp = 'onkeyup="networkLimitAll(\'' . esc_attr($data->networkAuthId) . '\',\'' . esc_attr($data->networkId) . '\',\'' . esc_attr($limitValue) . '\');"';
                } else {
                    $textareaOnKeyUp = 'onkeyup="networkCount(\'' . esc_attr($data->networkAuthId) . '\');"';
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . (int) esc_html($countCharacter) . '</span> ' . esc_html__('characters', 'blog2social') . '</span>';
                    if ($data->networkId == 2) {
                        $textareaLimitInfo .= '<span class="b2s-post-item-show-thread-count" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"> - <span class="b2s-post-item-count-threads" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">1</span> ' . esc_html__('Threads', 'blog2social') . '</span>';
                    }
                }

                break;
            case '1':
//page
                if (isset($this->limitCharacterPage[$data->networkId]) && ((int) $this->limitCharacterPage[$data->networkId] > 0) || isset($this->limitCharacterPage[$data->networkId][$data->networkKind])) {
                    $limitValue = (isset($this->limitCharacterPage[$data->networkId][$data->networkKind])) ? $this->limitCharacterPage[$data->networkId][$data->networkKind] : $this->limitCharacterPage[$data->networkId];
                    $limit = true;
                }
                $infoImage = (in_array($data->networkId, $this->allowNoImagePage)) ? esc_html__('Network does not support image for pages', 'blog2social') . '!' : '';
                $infoImage .= (in_array($data->networkId, $this->allowNoEmoji)) ? (!empty($infoImage) ? ' | ' : '') . esc_html__('Network does not support emojis', 'blog2social') . '!' : '';
                $infoImage .= (in_array($data->networkId, $this->allowNoCustomImagePage)) ? (!empty($infoImage) ? ' | ' : '') . esc_html__('Network defines image by link', 'blog2social') . '!' : '';
                $notAllowGif = ((defined('B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF')) ? json_decode(B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF, true) : false);
                $infoImage .= (is_array($notAllowGif) && in_array($data->networkId, $notAllowGif)) ? (!empty($infoImage) ? ' | ' : '') . esc_html__('Network does not support GIFs', 'blog2social') . '!' : '';

//ShortText
                if ($isDraft && isset($draftData['content'])) {
                    $message = $draftData['content'];
                } else {
                    if (array_key_exists($data->networkId, $this->post_template)) {
                        $message = $this->getMessagebyTemplate($data);
                    } else {
                        if (isset($this->setShortTextPage[$data->networkId]) && (int) $this->setShortTextPage[$data->networkId] > 0) {
                            $message = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang), (isset($this->setShortTextPage[$data->networkId]) ? (int) $this->setShortTextPage[$data->networkId] : false), (isset($this->setShortTextPageLimit[$data->networkId]) ? (( is_array($this->setShortTextPageLimit[$data->networkId]) && isset($this->setShortTextPageLimit[$data->networkId][$data->networkKind])) ? (int) $this->setShortTextPageLimit[$data->networkId][$data->networkKind] : (int) $this->setShortTextPageLimit[$data->networkId] ) : false));
                        } else {
                            $message = (in_array($data->networkId, $this->allowTitlePage) ? (in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, (in_array($data->networkId, $this->allowHtml) ? '<p><h1><h2><br><i><b><a><img>' : false), (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang));
                        }

                        //Hashtags
                        if (in_array($data->networkId, $this->allowHashTags)) {
                            $message .= $this->getHashTagsString('', ((isset($this->limitHashTagCharacter[$data->networkId])) ? $this->limitHashTagCharacter[$data->networkId] : 0));
                        }
                    }
                }
                $message = $this->hook_message($message);

                if ($this->b2sPostType == 'ex' && in_array($data->networkId, $this->showTitlePage)) {
                    $messageParts = preg_split('/\n/', $message);
                    if (count($messageParts) > 1 && isset($messageParts[0]) && isset($messageParts[1])) {
                        $title = $messageParts[0];
                        unset($messageParts[0]);
                        $message = implode("\n", $messageParts);
                    }
                }

                $network_display_name = $data->network_display_name;
                $isRequiredTextarea = (in_array($data->networkId, $this->isCommentPage)) ? '' : 'required="required"';

                $countCharacter = 0;
                if ($limit !== false) {
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . (int) esc_html($countCharacter) . '</span>/' . esc_html($limitValue) . ' ' . esc_html__('characters', 'blog2social') . '</span>';
                    $textareaOnKeyUp = 'onkeyup="networkLimitAll(\'' . esc_attr($data->networkAuthId) . '\',\'' . esc_attr($data->networkId) . '\',\'' . esc_attr($limitValue) . '\');"';
                } else {
                    $textareaOnKeyUp = 'onkeyup="networkCount(\'' . esc_attr($data->networkAuthId) . '\');"';
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . (int) esc_html($countCharacter) . '</span> ' . esc_html__('characters', 'blog2social') . '</span>';
                }
                break;
            case'2':
//group
//ShortText
                if (isset($this->limitCharacterGroup[$data->networkId]) && ((int) $this->limitCharacterGroup[$data->networkId] > 0) || isset($this->limitCharacterGroup[$data->networkId][$data->networkKind])) {
                    $limitValue = (isset($this->limitCharacterGroup[$data->networkId][$data->networkKind])) ? $this->limitCharacterGroup[$data->networkId][$data->networkKind] : $this->limitCharacterGroup[$data->networkId];
                    $limit = true;
                }
                if ($isDraft && isset($draftData['content'])) {
                    $message = $draftData['content'];
                } else {
                    if (array_key_exists($data->networkId, $this->post_template)) {
                        $message = $this->getMessagebyTemplate($data);
                    } else {
                        if (isset($this->setShortTextGroup[$data->networkId]) && (int) $this->setShortTextGroup[$data->networkId] > 0) {
                            $message = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang), (isset($this->setShortTextGroup[$data->networkId]) ? (int) $this->setShortTextGroup[$data->networkId] : false), (isset($this->setShortTextGroupLimit[$data->networkId]) ? (int) $this->setShortTextGroupLimit[$data->networkId] : false));
                        } else {
                            $message = (in_array($data->networkId, $this->allowTitleGroup) ? (in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, (in_array($data->networkId, $this->allowHtml) ? '<p><h1><h2><br><i><b><a><img>' : false), (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang));
                        }
                        //Hashtags
                        if (in_array($data->networkId, $this->allowHashTags)) {
                            $message .= $this->getHashTagsString('', ((isset($this->limitHashTagCharacter[$data->networkId])) ? $this->limitHashTagCharacter[$data->networkId] : 0));
                        }
                    }
                }
                $message = $this->hook_message($message);

                if ($this->b2sPostType == 'ex' && in_array($data->networkId, $this->showTitleGroup)) {
                    $messageParts = preg_split('/\n/', $message);
                    if (count($messageParts) > 1 && isset($messageParts[0]) && isset($messageParts[1])) {
                        $title = $messageParts[0];
                        unset($messageParts[0]);
                        $message = implode("\n", $messageParts);
                    }
                }

                $network_display_name = $data->network_display_name;
                $isRequiredTextarea = (in_array($data->networkId, $this->isCommentGroup)) ? '' : 'required="required"';
                $countCharacter = 0;
                if ($limit !== false) {
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . (int) esc_html($countCharacter) . '</span>/' . esc_html($limitValue) . ' ' . esc_html__('characters', 'blog2social') . '</span>';
                    $textareaOnKeyUp = 'onkeyup="networkLimitAll(\'' . esc_attr($data->networkAuthId) . '\',\'' . esc_attr($data->networkId) . '\',\'' . esc_attr($limitValue) . '\');"';
                } else {
                    $textareaOnKeyUp = 'onkeyup="networkCount(\'' . esc_attr($data->networkAuthId) . '\');"';
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . (int) esc_html($countCharacter) . '</span> ' . esc_html__('characters', 'blog2social') . '</span>';
                }
                break;
        }


//Infotexte
        $messageInfo = (!empty($infoImage)) ? '<p class="b2s-post-item-message-info pull-left hidden-sm hidden-xs">' . $infoImage . '</p>' : '';
        $onlyimage = in_array($data->networkId, $this->onlyImage) ? 'b2sOnlyWithImage' : '';

        $content = '<div class="b2s-post-item ' . esc_attr($onlyimage) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-network-id="' . esc_attr($data->networkId) . '">';
        $content .= '<div class="panel panel-group" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">';
        $content .= '<div class="panel-body ' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'del-padding-left del-padding-right' : '') . ' ">';
        $content .= '<div class="b2s-post-item-area" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">';
        $content .= '<div class="b2s-post-item-thumb hidden-xs">';
        $content .= '<img alt="" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" class="img-responsive b2s-post-item-network-image" src="' . esc_url(plugins_url('/assets/images/portale/' . $data->networkId . '_flat.png', B2S_PLUGIN_FILE)) . '">';
        $content .= '</div>';
        $content .= '<div class="b2s-post-item-details">';
        //XING deprecated
        if ($data->networkId == 8) {
            $content .= '<div class="b2s-post-item-network-deprecated"><span class="glyphicon glyphicon-exclamation-sign glyphicon-info"></span> ' . esc_html__('Connection expires on 31 March 2019', 'blog2social') . '</div>';
        }
        // G+ deprecated
        if ($data->networkId == 10) {
            $content .= '<div class="b2s-post-item-network-deprecated"><span class="glyphicon glyphicon-exclamation-sign glyphicon-info"></span> ' . esc_html__('Connection expires on 2 April 2019', 'blog2social') . '</div>';
        }
        $content .= '<h4 class="pull-left b2s-post-item-details-network-display-name" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . esc_html(stripslashes($network_display_name)) . '</h4>';
        $content .= '<div class="clearfix"></div>';

        $networkTypeNamePrint = $networkTypeName[$data->networkType];
        if (isset($networkTypeNameOverride[$data->networkId][$data->networkType])) {
            $networkTypeNamePrint = $networkTypeNameOverride[$data->networkId][$data->networkType];
        }
        if ($data->networkId == 19 && $data->networkType == 1 && isset($networkKindName[$data->networkKind])) {
            $networkTypeNamePrint = $networkKindName[$data->networkKind];
        }

        $content .= '<p class="pull-left">' . esc_html($networkTypeNamePrint) . ' | ' . esc_html($networkName[$data->networkId]);

        $content .= '<div class="b2s-post-item-details-message-result" data-network-auth-id="' . $data->networkAuthId . '" style="display:none;"></div>';
        $content .= '<span class="hidden-xs b2s-post-item-details-message-info" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . $messageInfo . '</span></span>';

        $content .= '<div class="pull-right hidden-xs b2s-post-item-info-area">';

        if (!$this->isVideoMode) {
            if (in_array($data->networkId, $this->setPostFormat)) {
                $postFormatType = ($data->networkId == 12) ? 'image' : 'post';
                $addCSS = (B2S_PLUGIN_USER_VERSION == 0) ? 'b2s-btn-disabled' : '';
                $content .= '<button class="btn btn-xs btn-link b2s-post-ship-item-post-format ' . esc_attr($addCSS) . '" data-post-wp-type="' . esc_attr($this->b2sPostType) . '" data-post-format-type="' . esc_attr($postFormatType) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-network-type="' . esc_attr($data->networkType) . '" data-network-id="' . esc_attr($data->networkId) . '" >' . esc_html__('post format', 'blog2social') . ': <span class="b2s-post-ship-item-post-format-text" data-post-format-type="' . esc_attr($postFormatType) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-network-type="' . esc_attr($data->networkType) . '"  data-network-id="' . esc_attr($data->networkId) . '" ></span></button>';
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $content .= '<input type="hidden" class="b2s-post-item-details-post-format form-control" name="b2s[' . esc_attr($data->networkAuthId) . '][post_format]" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-network-id="' . esc_attr($data->networkId) . '" data-network-type="' . esc_attr($data->networkType) . '" value="0" />';
                } else {
                    if ($this->viewMode != 'modal') {
                        $content .= '<span class="label label-success"><a target="_blank" class="btn-label-premium b2s-btn-trigger-post-ship-item-post-format" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" href="#">SMART</a></span>';
                    }
                }
            }
        }

        if ($data->networkId == 4) {
            $content .= '<select class="b2s-post-item-details-post-format form-control input-sm" name="b2s[' . esc_attr($data->networkAuthId) . '][post_format]" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-network-id="' . esc_attr($data->networkId) . '" data-network-type="' . esc_attr($data->networkType) . '">';
            $content .= '<option value="0" ' . ((isset($data->post_format) && (int) $data->post_format >= 0) ? '' : 'selected="selected"') . '>' . esc_html__('Text Post', 'blog2social') . '</option>';
            $content .= '<option value="1" ' . ((isset($data->post_format) && (int) $data->post_format == 1) ? 'selected="selected"' : '') . '>' . esc_html__('Image Post', 'blog2social') . '</option>';
            $content .= '<option value="2" ' . ((isset($data->post_format) && (int) $data->post_format == 2) ? 'selected="selected"' : '') . '>' . esc_html__('Link Post', 'blog2social') . '</option>';
            $content .= '</select>';
        }

        if ($data->networkId == 15) {
            $content .= '<input type="hidden" class="b2s-post-item-details-post-format form-control" name="b2s[' . esc_attr($data->networkAuthId) . '][post_format]" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-network-id="' . esc_attr($data->networkId) . '" data-network-type="' . esc_attr($data->networkType) . '" value="0" />';
        }

        if (!in_array($data->networkId, $this->isInstantSharing)) {
            $content .= '<span class="b2s-post-tool-area" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">';
            $content .= (in_array($data->networkId, $this->setPostFormat) && !$this->isVideoMode) ? '  | ' : '';
            if (in_array($data->networkId, $this->getText)) {
                $content .= '<button class="btn btn-xs btn-link b2s-post-ship-item-full-text" data-network-id="' . esc_attr($data->networkId) . '" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" >' . esc_html__('Insert full-text', 'blog2social') . '</button> | ';
            }
            $content .= '<button class="btn btn-xs btn-link b2s-post-ship-item-message-delete" data-network-count="-1" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">' . esc_html__('Delete text', 'blog2social') . '</button> | ';
            $content .= $textareaLimitInfo . '</span>';
        }

        $content .= '</div></p>';

        //TOS Twitter 030218
        if ($data->networkType == 0 && in_array($data->networkId, $this->networkTosProfile)) {
            $content .= '<div class="b2s-unique-content" data-network-id="' . esc_attr($data->networkId) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"><div class="clearfix"></div><div class="alert b2s-unique-content-alert alert-warning">' . esc_html__('Please keep in mind that according to Twitter’s new TOS, users are no longer allowed to post identical or substantially similar content to multiple accounts or multiple duplicate updates on one account.', 'blog2social') . '<br><strong>' . esc_html__('Violating these rules can result in Twitter suspending your account. Always vary your Tweets with different comments, hashtags or handles to prevent duplicate posts.', 'blog2social') . '</strong> <a href="' . esc_url(B2S_Tools::getSupportLink('network_tos_blog_032018')) . '" target="_blank">' . esc_html__('Learn more about this', 'blog2social') . '</a></div></div>';
        }
        //TOS Xing Group 080218
        if ($data->networkType == 2 && in_array($data->networkId, $this->networkTosGroup)) {
            $content .= '<div class="b2s-content-info" data-network-id="' . esc_attr($data->networkId) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"><div class="clearfix"></div><div class="alert alert-warning">' . esc_html__('Please note: XING allows identical posts to be published only once within a group and no more than three times across different groups.', 'blog2social') . ' <a href="' . esc_url(B2S_Tools::getSupportLink('network_tos_blog_082018')) . '" target="_blank">' . esc_html__('Read more', 'blog2social') . '</a></div></div>';
        }
        if ($data->networkId == 32) {
            $content .= '<div class="b2s-content-info" data-network-id="' . esc_attr($data->networkId) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"><div class="clearfix"></div><div class="alert alert-info">' . esc_html__('Please note that for adding clickable links and hashtags to your YouTube video descriptions you need to have a verified account.', 'blog2social') . ' ' . esc_html__('You can ensure this by signing into YouTube and opening Video Manager > Partner Settings. Check if your account is verified', 'blog2social') . '!</div></div>';
        }
        if ($data->networkId == 12) {
            $content .= '<div class="b2s-content-info" data-network-id="' . esc_attr($data->networkId) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" style="display:none;"><div class="clearfix"></div><div class="alert alert-warning">' . esc_html__('Good to know: Instagram supports up to 30 hashtags in a post. The number recommended for best results is 5 hashtags. Make sure that your hashtags are thematically relevant to the content of your post.', 'blog2social') . '</div></div>';
            $content .= '<div class="b2s-unique-content" data-network-id="' . esc_attr($data->networkId) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"><div class="clearfix"></div><div class="alert b2s-unique-content-alert alert-warning" style="margin-bottom:10px !important;">' . esc_html__('Good to know: Instagram does not allow to publish identical or substantially similar content on one or more accounts. Vary your content by using other images, comments, hashtags or handles and provide your followers with more inspiring content.', 'blog2social') . '<br><strong>' . esc_html__('Violating these rules can result in suspending your account. Always vary your content with different images, comments, hashtags or handles to prevent duplicate posts.', 'blog2social') . '</strong></div></div>';
            $content .= '<input type="hidden" class="b2s-network-image-frame-color" name="b2s[' . esc_attr($data->networkAuthId) . '][frame_color]" data-network-id="' . esc_attr($data->networkId) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" value="' . ((isset($this->post_template[$data->networkId][$data->networkType]['frameColor']) && !empty($this->post_template[$data->networkId][$data->networkType]['frameColor'])) ? esc_html($this->post_template[$data->networkId][$data->networkType]['frameColor']) : '#ffffff') . '">';
        }

        $content .= '<div class="b2s-post-item-details-edit-area" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">';

        if (!in_array($data->networkId, $this->isInstantSharing)) {
            $content .= (in_array($data->networkId, $this->showBoards) || ($data->networkType == 2 && in_array($data->networkId, $this->showBoardsGroup))) ? $this->getBoardHtml($data->networkAuthId, $data->networkId, $data->networkType) : '';
            $content .= (in_array($data->networkId, $this->showGroups) && ($data->networkType == 2 || $data->networkId == 15)) ? $this->getGroupsHtml($data->networkAuthId, $data->networkId) : '';
            $content .= (in_array($data->networkId, $this->showMarketplace) && $data->networkType == 2) ? $this->getMarketplaceAreaHtml($data->networkAuthId, $data->networkId, $data->networkType, $data->networkKind) : '';
            $content .= ((in_array($data->networkId, $this->showTitleProfile) && $data->networkType == 0) || (((in_array($data->networkId, $this->showTitlePage) && !is_array($this->showTitlePage[$data->networkId]) ) || (is_array($this->showTitlePage[$data->networkId]) && in_array($data->networkKind, $this->showTitlePage[$data->networkId]))) && $data->networkType == 1) || (in_array($data->networkId, $this->showTitleGroup) && $data->networkType == 2)) ? $this->getTitleHtml($data->networkId, $data->networkAuthId, $data->networkKind, $data->networkType, ((isset($title) && !empty($title)) ? $title : $this->postData->post_title)) : '';
            $content .= $this->getCustomEditArea($data->networkId, $data->networkAuthId, $data->networkType, $message, $isRequiredTextarea, $textareaOnKeyUp, $limit, $limitValue, isset($data->image_url) ? $data->image_url : null, isset($data->multi_images) ? $data->multi_images : array(), isset($data->post_format) ? (int) $data->post_format : 0); //
            $content .= (in_array($data->networkId, $this->allowPrivacyStatus)) ? $this->getPrivacyStatusHtml($data->networkAuthId, $data->networkId) : '';
            $content .= (in_array($data->networkId, $this->allowTag) && ($data->networkType == 0 || $data->networkId == 11)) ? $this->getTagsHtml($data->networkId, $data->networkAuthId) : '';

            // NOTE Wird aufgerufen wenn kein video mode oder wenn video mode und erlaubte netzwerke
            if (!$this->isVideoMode || ($this->isVideoMode && (in_array($data->networkId, $this->videoScheduleNetworks)))) {
                //Calendar
                if (!(isset($this->viewMode) && $this->viewMode == 'modal')) {
                    $content .= '<div class="clearfix"></div>';
                    $content .= '<div class="b2s-calendar-filter-area col-xs-2 pull-right del-padding-right hide" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">';
                    $content .= '<select class="b2s-calendar-filter-network-sel form-control" name="b2s-calendar-filter-network-sel" data-last-sel="' . esc_attr($data->networkId) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"><option value="all">show all</option><option selected value="' . esc_attr($data->networkId) . '">' . esc_html($networkName[$data->networkId]) . '</option></select>';
                    $content .= '</div>';
                    // NOTE wird nur aufgerufen, wenn es sich um keinen Videopost handelt
                    if (in_array($data->networkId, $this->showRelay) && !$this->isVideoMode) {
                        $content .= $this->getRelayBtnHtml($data->networkAuthId, $data->networkId);
                    }
                    $content .= '<a href="#" class="b2s-toogle-calendar-btn btn btn-primary pull-right btn-xs hidden-xs" data-network-id="' . esc_attr($data->networkId) . '" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-toogle-text-btn="' . esc_attr__("hide calendar", "blog2social") . '">' . esc_html__("show calendar", "blog2social") . '</a>';
                    $content .= '<div class="clearfix"></div><div class="b2s-post-item-calendar-area hide hidden-xs" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"></div>';

                    if (in_array($data->networkId, $this->showRelay)) {
                        $content .= $this->getRelayContentHtml($data->networkAuthId, $data->networkId);
                    }
                }
                //Planning
                if ($show_time) {
                    $content .= $this->getShippingTimeHtml($data->networkAuthId, $data->networkType, $data->networkId, $data->networkType, $message, $isRequiredTextarea, $textareaOnKeyUp, $limit, $limitValue, isset($data->image_url) ? $data->image_url : null);
                }
            }
        } else {
            $content .= '<div class="clearfix"></div><div class="alert alert-info">' . esc_html__("Please note that video uploads need to be approved in the TikTok app.", "blog2social") . ' (<a href="' . esc_url(B2S_Tools::getSupportLink('video_sharing_tiktok')) . '" target="_blank">' . esc_html__('Learn how it works', 'blog2social') . '</a>)</div>';
        }

        $content .= '</div>';

        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<input type="hidden" class="form-control" name="b2s[' . esc_attr($data->networkAuthId) . '][network_id]" value="' . esc_attr($data->networkId) . '">';
        $content .= '<input type="hidden" class="form-control" name="b2s[' . esc_attr($data->networkAuthId) . '][network_type]" value="' . esc_attr($data->networkType) . '">';
        $content .= '<input type="hidden" class="form-control" name="b2s[' . esc_attr($data->networkAuthId) . '][instant_sharing]" value="' . esc_attr($data->instantSharing) . '">';
        $content .= '<input type="hidden" class="form-control" name="b2s[' . esc_attr($data->networkAuthId) . '][network_tos_group_id]" value="' . esc_attr($data->networkTosGroupId) . '">';
        $content .= '<input type="hidden" class="form-control networkKind" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" name="b2s[' . esc_attr($data->networkAuthId) . '][network_kind]" value="' . esc_attr($data->networkKind) . '">';
        $content .= '<input type="hidden" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" class="form-control b2s-post-ship-network-display-name" name="b2s[' . esc_attr($data->networkAuthId) . '][network_display_name]" value="' . esc_attr($data->network_display_name) . '">';
        $content .= '<input type="hidden" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" class="b2s-network-default-image" value="' . esc_url($this->defaultImage) . '">';

        $content .= '</div>';

        return $content;
    }

    public function getCustomEditArea($networkId, $networkAuthId, $networkType, $message, $isRequiredTextarea, $textareaOnKeyUp, $limit, $limitValue, $imageUrl = null, $multi_images = array(), $postFormat = 0) {

        $meta = array();
        if ($networkId == 1 || ($networkId == 8 && $networkType == 0) || $networkId == 19 || $networkId == 3 || $networkId == 4 || $networkId == 2 || $networkId == 15 || $networkId == 17 || $networkId == 24) {
            if (trim(strtolower($this->postStatus)) == 'publish' || $this->b2sPostType == 'ex') {
                //is calendar edit => scrape post url and not custom post url by override from edit function for meta tags!
                //$editPostUrl = $this->viewMode == 'modal') ? (get_permalink($this->postData->ID) !== false ? get_permalink($this->postData->ID) : $this->postData->guid) : $this->postUrl;
                $meta = B2S_Util::getMetaTags($this->postId, $this->postUrl, $networkId);
                //Case: no twitter image tag try og image tag
                if (($networkId == 2 && $networkId == 24) && !isset($meta['image'])) {
                    $meta = B2S_Util::getMetaTags($this->postId, $this->postUrl);
                }
            } else {
                $desc = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($networkId, $this->allowNoEmoji) ? false : true), $this->userLang), 250);
                if (empty($desc) && isset($this->postData->post_excerpt) && !empty($this->postData->post_excerpt)) {
                    $desc = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_excerpt, $this->postUrl, false, (in_array($networkId, $this->allowNoEmoji) ? false : true), $this->userLang), 250);
                }
                $meta = array('title' => B2S_Util::getExcerpt(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang), 250), 'description' => $desc);
            }

            //EDIT Function - Calendar
            $meta = (is_array($meta)) ? $meta : array();
            $meta = $this->hook_meta($meta);
            $imageUrl = $imageUrl ? $imageUrl : (isset($meta['image']) ? $meta['image'] : null);

            if ($networkId == 1) {
                $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea class="form-control fb-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '' . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';

                // NOTE Reel Checkbox name = share_as_reel
                if ($this->isVideoMode === true || $this->isVideoMode === 1) {
                    $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    if (isset($this->canReel['result']) && $this->canReel['result'] === false && isset($this->canReel['content'])) {
                        $edit .= '<div class="alert alert-warning warning-for-reel"> ' .$this->canReel['content']. '</div>';
                    }
                    $edit .= '<input type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][share_as_reel]" id="b2s[' . esc_attr($networkAuthId) . '][isReelCB]" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ((isset($this->canReel['result']) && $this->canReel['result'] === false) ? 'disabled' : '') . ' value="1">';
                    $edit .= '<label ' . ((isset($this->canReel['result']) && $this->canReel['result'] === false) ? 'class="dis-reel-cb"' : 'for="b2s[' . esc_attr($networkAuthId) . '][isReelCB]"') . '> ' . esc_html__('Share as Reel', 'blog2social') . '</label>';
                    $edit .= '</div>';
                }

                if (!$this->isVideoMode) {
                    $edit .= '<div class="row">';
                    $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                    $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img src="' . esc_url((isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage)) . '" class="fb-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';

                    if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                        $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                        $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                        $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                    }

                    $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                    $edit .= '<div class="clearfix"></div>';
                    $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-network-count="-1" data-meta-type="og" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                    $edit .= '</div>';
                    $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 col-sm-7 col-lg-9 b2s-post-original-area" data-network-auth-id="' . esc_attr($networkAuthId) . '"') . '>';
                    if (B2S_PLUGIN_USER_VERSION > 0) {
                        $edit .= '<button data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-meta-type="og" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . esc_html__('Info: Change Open Graph Meta tags image, title and description for this network', 'blog2social') . '</button>';
                    } else {
                        $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship" href="#"><span class="label label-success">SMART</span></a>';
                        $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship">' . esc_html__('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                    }
                    $edit .= '<input type="text" class="form-control og-url-title b2s-post-item-details-preview-title change-meta-tag og_title" placeholder="' . esc_attr__('OG Meta title', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_title]"  data-meta="og_title" data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '')) . '" />';
                    $edit .= '<input type="text" class="form-control og-url-desc b2s-post-item-details-preview-desc change-meta-tag og_desc" placeholder="' . esc_attr__('OG Meta description', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_desc]" data-meta="og_desc"  data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '')) . '" />';
                    $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, true, 'og-url-input', true, $imageUrl);
                    $edit .= '</div>';
                    $edit .= '</div>';
                    if ($networkType == 1 || $networkType == 2) {
                        $edit .= '<div class="col-sm-12 b2s-multi-image-area" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="-1">';
                        $edit .= '<div class="row b2s-margin-top-20">';
                        $edit .= '<div class="col-sm-3 text-center">';
                        if (B2S_PLUGIN_USER_VERSION > 1) {
                            $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? 'style="display:none;"' : '') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="1" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                            $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="1" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                            $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="1" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                            $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                            $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_1]" data-image-count="1" data-network-count="-1" data-network-auth-id="' . $networkAuthId . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '">';
                            $edit .= '</div>';
                            $edit .= '<div class="col-sm-3 text-center">';
                            $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0]) && (!isset($multi_images[1]) || empty($multi_images[1]))) ? '' : 'style="display:none;"') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="2" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                            $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="2" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                            $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? esc_url($multi_images[1]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="2" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                            $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="2">' . esc_html__('Change image', 'blog2social') . '</button>';
                            $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_2]" data-image-count="2" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '">';
                            $edit .= '</div>';
                            $edit .= '<div class="col-sm-3 text-center">';
                            $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1]) && (!isset($multi_images[2]) || empty($multi_images[2]))) ? '' : 'style="display:none;"') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="3" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                            $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="3" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                            $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? esc_url($multi_images[2]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="3" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                            $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="3">' . esc_html__('Change image', 'blog2social') . '</button>';
                            $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_3]" data-image-count="3" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? esc_url($multi_images[1]) : "")) . '">';
                        } else {
                            $edit .= '<a class="btn btn-primary btn-circle b2sProFeatureModalBtn" data-title="' . esc_html__('Do you want to post multiple images?', 'blog2social') . '" data-type="multi-image">+</a><span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span>';
                        }
                        $edit .= '</div>';
                        $edit .= '</div>';
                        $edit .= '</div>';
                    }
                }
            }

            if ($networkId == 2) {
                $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea class="form-control tw-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" unique="currency" name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                if (!$this->isVideoMode) {

                    $edit .= '<div class="row">';
                    $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                    $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img src="' . esc_url((isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage)) . '" class="tw-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';

                    if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                        $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                        $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                        $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                    }

                    $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                    $edit .= '<div class="clearfix"></div>';
                    $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="card" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                    $edit .= '</div>';
                    $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 col-sm-7 col-lg-9 b2s-post-original-area" data-network-auth-id="' . $networkAuthId . '"') . '>';
                    $edit .= '<div class="alert alert-warning margin-bottom-0 b2s-alert-twitter-card" data-network-auth-id="' . $networkAuthId . '">' . esc_html__('Please note: Twitter stores the Card parameters of a link for up to 7 days. Changes may not be immediately visible on Twitter.', 'blog2social') . '</div>';
                    if (B2S_PLUGIN_USER_VERSION > 0) {
                        $edit .= '<button data-network-auth-id="' . esc_attr($networkAuthId) . '" data-meta-type="card" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . esc_html__('Info: Change Card Meta tags image, title and description for this network', 'blog2social') . '</button>';
                    } else {
                        $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="card" data-meta-origin="ship" href="#"><span class="label label-success">SMART</span></a>';
                        $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="card" data-meta-origin="ship">' . esc_html__('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                    }
                    $edit .= '<input type="text" readonly class="form-control tw-url-title b2s-post-item-details-preview-title change-meta-tag card_title"  placeholder="' . esc_attr__('Card Meta title', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][card_title]"  data-meta="card_title" data-meta-type="card" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '')) . '" />';
                    $edit .= '<input type="text" readonly class="form-control tw-url-desc b2s-post-item-details-preview-desc change-meta-tag card_desc"  placeholder="' . esc_attr__('Card Meta description', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][card_desc]"  data-meta="card_desc" data-meta-type="card" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '')) . '" />';
                    $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, true, 'tw-url-input', true);
                    $edit .= '</div>';
                    $edit .= '</div>';

                    $edit .= '<div class="col-sm-12 b2s-multi-image-area" data-network-auth-id="' . $networkAuthId . '" data-network-count="-1">';
                    $edit .= '<div class="row b2s-margin-top-20">';
                    $edit .= '<div class="col-sm-3 text-center">';
                    if (B2S_PLUGIN_USER_VERSION > 1) {
                        $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? 'style="display:none;"' : '') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="1" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="1" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="1" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" data-image-count="1" ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_1]" data-image-count="1" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '">';
                        $edit .= '</div>';
                        $edit .= '<div class="col-sm-3 text-center">';
                        $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0]) && (!isset($multi_images[1]) || empty($multi_images[1]))) ? '' : 'style="display:none;"') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-network-count="-1" data-image-count="2" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="2" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? $multi_images[1] : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="2" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" data-image-count="2" ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_2]" data-image-count="2" data-network-count="-1" data-network-auth-id="' . $networkAuthId . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? esc_url($multi_images[1]) : "")) . '">';
                        $edit .= '</div>';
                        $edit .= '<div class="col-sm-3 text-center">';
                        $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1]) && (!isset($multi_images[2]) || empty($multi_images[2]))) ? '' : 'style="display:none;"') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-network-count="-1" data-image-count="3" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="3" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? esc_url($multi_images[2]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="3" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" data-image-count="3" ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_3]" data-image-count="3" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? esc_url($multi_images[2]) : "")) . '">';
                    } else {
                        $edit .= '<a class="btn btn-primary btn-circle b2sProFeatureModalBtn" data-title="' . esc_html__('Do you want to post multiple images?', 'blog2social') . '" data-type="multi-image">+</a><span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span>';
                    }
                    $edit .= '</div>';
                    $edit .= '</div>';
                    $edit .= '</div>';
                }
            }

            if ($networkId == 3) {
                $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea class="form-control linkedin-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '" >';
                
                if(!$this->isVideoMode){
                    $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img src="' . esc_url((isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage)) . '" class="linkedin-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';

                    if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                        $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                        $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                        $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                    }

                    $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                    $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="-1" data-meta-type="og" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                
                }
                $edit .= '</div>';

                if(!$this->isVideoMode){
                    $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 col-sm-7 col-lg-9 b2s-post-original-area" data-network-auth-id="' . esc_attr($networkAuthId) . '"') . '>';
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $edit .= '<button data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-meta-type="og" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . esc_html__('Info: Change Open Graph Meta tags image, title and description for this network', 'blog2social') . '</button>';
                } else {
                    $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship" href="#"><span class="label label-success">SMART</span></a>';
                    $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship">' . esc_html__('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                }
                $edit .= '<input type="text" class="form-control og-url-title b2s-post-item-details-preview-title change-meta-tag og_title" placeholder="' . esc_attr__('OG Meta title', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_title]"  data-meta="og_title" data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '')) . '" />';
                $edit .= '<input type="text" class="form-control og-url-desc b2s-post-item-details-preview-desc change-meta-tag og_desc" placeholder="' . esc_attr__('OG Meta description', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_desc]" data-meta="og_desc"  data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '')) . '" />';
                $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, true, 'og-url-input', true);
                $edit .= '</div>';
                $edit .= '</div>';
                }
                

                $edit .= '<div class="col-sm-12 b2s-multi-image-area" data-network-auth-id="' . $networkAuthId . '" data-network-count="-1">';
                $edit .= '<div class="row b2s-margin-top-20">';
                $edit .= '<div class="col-sm-3 text-center">';
                if (B2S_PLUGIN_USER_VERSION > 1) {
                    $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? 'style="display:none;"' : '') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="1" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                    $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="1" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="1" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" data-image-count="1" ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                    $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_1]" data-image-count="1" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '">';
                    $edit .= '</div>';
                    $edit .= '<div class="col-sm-3 text-center">';
                    $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0]) && (!isset($multi_images[1]) || empty($multi_images[1]))) ? '' : 'style="display:none;"') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-network-count="-1" data-image-count="2" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                    $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="2" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? esc_url($multi_images[1]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="2" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" data-image-count="2" ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                    $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_2]" data-image-count="2" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1])) ? esc_url($multi_images[1]) : "")) . '">';
                    $edit .= '</div>';
                    $edit .= '<div class="col-sm-3 text-center">';
                    $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[1]) && !empty($multi_images[1]) && (!isset($multi_images[2]) || empty($multi_images[2]))) ? '' : 'style="display:none;"') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-network-count="-1" data-image-count="3" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                    $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="3" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? esc_url($multi_images[2]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="3" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" data-image-count="3" ' . ((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                    $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_3]" data-image-count="3" data-network-count="-1" data-network-auth-id="' . $networkAuthId . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[2]) && !empty($multi_images[2])) ? $multi_images[2] : "")) . '">';
                } else {
                    $edit .= '<a class="btn btn-primary btn-circle b2sProFeatureModalBtn" data-title="' . esc_html__('Do you want to post multiple images?', 'blog2social') . '" data-type="multi-image">+</a><span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';
            }

            if ($networkId == 4) {
                $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea class="form-control tumblr-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '<div class="row">';
                $edit .= '<div class="b2s-format-area-tumblr-image ' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ((isset($postFormat) && (int) $postFormat == 1) ? '' : 'style="display:none;"') . '>';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url((isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage)) . '" class="tumblr-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';

                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="-1" data-meta-type="og" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="b2s-format-area-tumblr-link ' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 b2s-post-original-area" ') . ' data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ((isset($postFormat) && (int) $postFormat == 2) ? '' : 'style="display:none;"') . '>';
                $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, true, '');
                $edit .= '</div>';
                $edit .= '</div>';
            }

            if ($networkId == 19) {
                $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea class="form-control xing-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url((isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage)) . '" class="xing-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="-1" data-meta-type="og" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 col-sm-7 col-lg-9 b2s-post-original-area" data-network-auth-id="' . esc_attr($networkAuthId) . '"') . '>';
                $edit .= '<div class="alert alert-warning margin-bottom-0">' . esc_html__('Please note: XING stores the Open Graph parameters of a link for up to 7 days. Changes may not be immediately visible on XING.', 'blog2social') . '</div>';
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $edit .= '<button data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-meta-type="og" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . esc_html__('Info: Change Open Graph Meta tags image, title and description for this network', 'blog2social') . '</button>';
                } else {
                    $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship" href="#"><span class="label label-success">SMART</span></a>';
                    $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship">' . esc_html__('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                }
                $edit .= '<input type="text" class="form-control og-url-title b2s-post-item-details-preview-title change-meta-tag og_title" placeholder="' . esc_attr__('OG Meta title', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_title]"  data-meta="og_title" data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '')) . '" />';
                $edit .= '<input type="text" class="form-control og-url-desc b2s-post-item-details-preview-desc change-meta-tag og_desc" placeholder="' . esc_attr__('OG Meta description', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_desc]" data-meta="og_desc"  data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '')) . '" />';
                $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, true, 'og-url-input', true);
                $edit .= '</div>';
                $edit .= '</div>';
            }
            if (($networkId == 15)) {
                $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea class="form-control reddit-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url((isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage)) . '" class="reddit-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="-1" data-meta-type="og" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 col-sm-7 col-lg-9 b2s-post-original-area" data-network-auth-id="' . esc_attr($networkAuthId) . '"') . '>';
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $edit .= '<button data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-meta-type="og" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . esc_html__('Info: Change Open Graph Meta tags image, title and description for this network', 'blog2social') . '</button>';
                } else {
                    $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship" href="#"><span class="label label-success">SMART</span></a>';
                    $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship">' . esc_html__('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                }
                $edit .= '<input type="text" class="form-control og-url-title b2s-post-item-details-preview-title change-meta-tag og_title" placeholder="' . esc_attr__('OG Meta title', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_title]"  data-meta="og_title" data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '')) . '" />';
                $edit .= '<input type="text" class="form-control og-url-desc b2s-post-item-details-preview-desc change-meta-tag og_desc" placeholder="' . esc_attr__('OG Meta description', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_desc]" data-meta="og_desc"  data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '')) . '" />';
                $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, true, 'og-url-input', true);
                $edit .= '</div>';
                $edit .= '</div>';
            }
            if ($networkId == 17) {
                $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea class="form-control vk-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '" >';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url((isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage)) . '" class="b2s-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="-1" data-meta-type="og" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 col-sm-7 col-lg-9 b2s-post-original-area" data-network-auth-id="' . esc_attr($networkAuthId) . '"') . '>';
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $edit .= '<button data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-meta-type="og" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . esc_html__('Info: Change Open Graph Meta tags image, title and description for this network', 'blog2social') . '</button>';
                } else {
                    $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship" href="#"><span class="label label-success">SMART</span></a>';
                    $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship">' . esc_html__('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                }
                $edit .= '<input type="text" class="form-control og-url-title b2s-post-item-details-preview-title change-meta-tag og_title" placeholder="' . esc_attr__('OG Meta title', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_title]"  data-meta="og_title" data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '')) . '" />';
                $edit .= '<input type="text" class="form-control og-url-desc b2s-post-item-details-preview-desc change-meta-tag og_desc" placeholder="' . esc_attr__('OG Meta description', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][og_desc]" data-meta="og_desc"  data-meta-type="og" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '')) . '" />';
                $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, true, 'og-url-input', true);
                $edit .= '</div>';
                $edit .= '</div>';
            }
            if ($networkId == 24) {
                $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea class="form-control telegram-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url((isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage)) . '" class="b2s-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && $this->viewMode != 'modal') {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-network-count="-1" data-meta-type="card" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 col-sm-7 col-lg-9 b2s-post-original-area" data-network-auth-id="' . esc_attr($networkAuthId) . '"') . '>';
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $edit .= '<button data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-meta-type="card" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . esc_html__('Info: Change Card Meta tags image, title and description for this network', 'blog2social') . '</button>';
                } else {
                    $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="card" data-meta-origin="ship" href="#"><span class="label label-success">SMART</span></a>';
                    $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="card" data-meta-origin="ship">' . esc_html__('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                }
                $edit .= '<input type="text" readonly class="form-control tw-url-title b2s-post-item-details-preview-title change-meta-tag card_title"  placeholder="' . esc_attr__('Card Meta title', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][card_title]"  data-meta="card_title" data-meta-type="card" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '')) . '" />';
                $edit .= '<input type="text" readonly class="form-control tw-url-desc b2s-post-item-details-preview-desc change-meta-tag card_desc"  placeholder="' . esc_attr__('Card Meta description', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][card_desc]"  data-meta="card_desc" data-meta-type="card" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '')) . '" />';
                $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, true, 'tw-url-input', true);
                $edit .= '</div>';
                $edit .= '</div>';
            }
        } else {
            $edit = '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
            $edit .= '<textarea class="form-control b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '"  name="b2s[' . esc_attr($networkAuthId) . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . ' ' . (($networkId == 12) ? 'unique="currency"' : '') . '>' . esc_html($message) . '</textarea>';
            if (!in_array($networkId, $this->allowNoEmoji)) {
                $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
            }
            $edit .= '</div>';

//EDIT Function - Calendar
            $meta = $this->hook_meta(array());
            $imageUrl = $imageUrl ? $imageUrl : (isset($meta['image']) ? $meta['image'] : null);
            $edit .= $this->getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, false, '', false, $imageUrl);
            if ($networkId == 14) {  //FeatureImage Network Torial (Portfolio)
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-id="' . esc_attr($networkId) . '" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
            }
            if (!$this->isVideoMode) {
                if ($networkId == 12) {
                    $edit .= '<div class="col-sm-12 b2s-multi-image-area" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="-1">';
                    $edit .= '<div class="row b2s-margin-top-20">';

                    if (B2S_PLUGIN_USER_VERSION > 1) {
                        for ($i = 1; $i < 10; $i++) {
                            if (1 == $i) {
                                $edit .= '<div class="col-sm-' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? '2' : '1') . ' text-center">';
                                $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? 'style="display:none;"' : '') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                                $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-zoom-btn" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-zoom-in"></i></button>';
                                $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                                $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" data-image-count="' . esc_attr($i) . '" ' . ((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="' . esc_attr($i) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                                $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_' . esc_attr($i) . ']" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[0]) && !empty($multi_images[0])) ? esc_url($multi_images[0]) : "")) . '">';
                                $edit .= '</div>';
                            } else {
                                if ($i == 7 && isset($this->viewMode) && $this->viewMode == 'modal') {
                                    $edit .= '</div>';
                                    $edit .= '<div class="row b2s-margin-top-20">';
                                }
                                $edit .= '<div class="col-sm-' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? '2' : '1') . ' text-center">';
                                $edit .= '<a ' . ((!empty($multi_images) && isset($multi_images[$i - 2]) && !empty($multi_images[$i - 2]) && (!isset($multi_images[$i - 1]) || empty($multi_images[$i - 1]))) ? '' : 'style="display:none;"') . ' class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                                $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[$i - 1]) && !empty($multi_images[$i - 1])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                                $edit .= '<button ' . ((!empty($multi_images) && isset($multi_images[$i - 1]) && !empty($multi_images[$i - 1])) ? '' : 'style="display:none;"') . ' class="btn btn-primary btn-circle b2s-multi-image-zoom-btn" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-zoom-in"></i></button>';
                                $edit .= '<img ' . ((!empty($multi_images) && isset($multi_images[$i - 1]) && !empty($multi_images[$i - 1])) ? '' : 'style="display:none;"') . ' src="' . esc_attr(((!empty($multi_images) && isset($multi_images[$i - 1]) && !empty($multi_images[$i - 1])) ? esc_url($multi_images[$i - 1]) : "")) . '" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" data-image-count="' . esc_attr($i) . '" ' . ((!empty($multi_images) && isset($multi_images[$i - 1]) && !empty($multi_images[$i - 1])) ? '' : 'style="display:none;"') . ' data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="' . esc_attr($i) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                                $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][multi_image_' . esc_attr($i) . ']" data-image-count="' . esc_attr($i) . '" data-network-count="-1" data-network-auth-id="' . $networkAuthId . '" value="' . esc_attr(((!empty($multi_images) && isset($multi_images[$i - 1]) && !empty($multi_images[$i - 1])) ? esc_url($multi_images[$i - 1]) : "")) . '">';
                                $edit .= '</div>';
                            }
                        }
                    } else {
                        $edit .= '<div class="col-sm-3 text-center">';
                        $edit .= '<a class="btn btn-primary btn-circle b2sProFeatureModalBtn" data-title="' . esc_html__('Do you want to post multiple images?', 'blog2social') . '" data-type="multi-image">+</a><span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span>';
                        $edit .= '</div>';
                    }
                    $edit .= '</div>';
                    $edit .= '</div>';
                }
            } else if (($this->isVideoMode === true || $this->isVideoMode === 1) && $networkId == 12) {
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                if ($this->canReel['result'] === false) {
                    $edit .= '<div class="alert alert-warning warning-for-reel"> ' . esc_html__($this->canReel['content'], 'blog2social') . '</div>';
                }
                $edit .= '<input type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][share_as_reel]" id="b2s[' . esc_attr($networkAuthId) . '][isReelCB]" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($this->canReel['result'] === false ? 'disabled' : '') . ' value="1">';
                $edit .= '<label ' . ($this->canReel['result'] === false ? 'class="dis-reel-cb"' : 'for="b2s[' . esc_attr($networkAuthId) . '][isReelCB]"') . '> ' . esc_html__('Share as Reel', 'blog2social') . '</label>';
                $edit .= '</div>';
            }
        }
        return $edit;
    }

    public function getCustomEditSchedArea($schedCount = 0, $networkId = 0, $networkAuthId = 0, $networkType = 0, $message = '', $isRequiredTextarea = '', $textareaOnKeyUp = '', $limit = 0, $limitValue = 0, $infoArea = '', $imageUrl = null) {

        // NOTE Wird nur bei gelisteten Videonetzwerken aufgerufen
        if ($this->isVideoMode && in_array($networkId, $this->videoScheduleNetworks)) {
            $edit = '<div class="row"><br>';
            $edit .= '<div class="b2s-unique-content col-xs-12" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><div class="clearfix"></div><div class="alert b2s-unique-content-alert alert-warning">' . esc_html__('Please keep in mind that according to Twitter’s new TOS, users are no longer allowed to post identical or substantially similar content to multiple accounts or multiple duplicate updates on one account.', 'blog2social') . '<br><strong>' . esc_html__('Violating these rules can result in Twitter suspending your account. Always vary your Tweets with different comments, hashtags or handles to prevent duplicate posts.', 'blog2social') . '</strong> <a href="' . esc_url(B2S_Tools::getSupportLink('network_tos_blog_032018')) . '" target="_blank">' . esc_html__('Learn more about this', 'blog2social') . '</a></div><br></div>';
            $edit .= '<div class="col-xs-12 col-sm-7 col-lg-12">';
            $edit .= $infoArea;
            $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
            $edit .= '<textarea disabled="disabled" class="form-control tw-textarea-input b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" unique="currency" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '></textarea>';
            if (!in_array($networkId, $this->allowNoEmoji)) {
                $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
            }
            $edit .= '</div>';
            $edit .= '</div>';
            $edit .= '</div>';
        } else if ($networkId == 1 || $networkId == 19 || $networkId == 3 || $networkId == 2 || $networkId == 15 || $networkId == 17 || $networkId == 24) {
            if ($networkId == 1) {
                $edit = '<div class="row"><br>';
                $edit .= '<div class="col-xs-12 col-sm-5 col-lg-3">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url($this->defaultImage) . '" class="fb-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control"  data-network-count="' . esc_attr($schedCount) . '"  data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_image_url][' . esc_attr($schedCount) . ']">';
                $edit .= '<div class="clearfix"></div>';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="col-xs-12 col-sm-7 col-lg-9">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control fb-textarea-input b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_attr($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';

                if ($networkType == 1 || $networkType == 2) {
                    $edit .= '<div class="col-sm-12 b2s-multi-image-area" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedCount) . '">';
                    $edit .= '<div class="row b2s-margin-top-20">';
                    $edit .= '<div class="col-sm-3 text-center">';
                    if (B2S_PLUGIN_USER_VERSION > 1) {
                        $edit .= '<a class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_1][' . esc_attr($schedCount) . ']" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '</div>';
                        $edit .= '<div class="col-sm-3 text-center">';
                        $edit .= '<a style="display:none;" class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="2">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_2][' . esc_attr($schedCount) . ']" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '</div>';
                        $edit .= '<div class="col-sm-3 text-center">';
                        $edit .= '<a style="display:none;" class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="3">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_3][' . esc_attr($schedCount) . ']" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    } else {
                        $edit .= '<a class="btn btn-primary btn-circle b2sProFeatureModalBtn" data-title="' . esc_html__('Do you want to post multiple images?', 'blog2social') . '" data-type="multi-image">+</a><span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span>';
                    }
                    $edit .= '</div>';
                    $edit .= '</div>';
                    $edit .= '</div>';
                }
            }

            if ($networkId == 2) {
                $edit = '<div class="row"><br>';
                //TOS Twitter 032018
                $edit .= '<div class="b2s-unique-content col-xs-12" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><div class="clearfix"></div><div class="alert b2s-unique-content-alert alert-warning">' . esc_html__('Please keep in mind that according to Twitter’s new TOS, users are no longer allowed to post identical or substantially similar content to multiple accounts or multiple duplicate updates on one account.', 'blog2social') . '<br><strong>' . esc_html__('Violating these rules can result in Twitter suspending your account. Always vary your Tweets with different comments, hashtags or handles to prevent duplicate posts.', 'blog2social') . '</strong> <a href="' . esc_url(B2S_Tools::getSupportLink('network_tos_blog_032018')) . '" target="_blank">' . esc_html__('Learn more about this', 'blog2social') . '</a></div><br></div>';
                $edit .= '<div class="col-xs-12 col-sm-5 col-lg-3">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url($this->defaultImage) . '" class="tw-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_image_url][' . esc_attr($schedCount) . ']">';
                $edit .= '<div class="clearfix"></div>';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }


                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="card" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="col-xs-12 col-sm-7 col-lg-9">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control tw-textarea-input b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" unique="currency" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '></textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';

                $edit .= '<div class="col-sm-12 b2s-multi-image-area" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedCount . '">';
                $edit .= '<div class="row b2s-margin-top-20">';
                $edit .= '<div class="col-sm-3 text-center">';
                if (B2S_PLUGIN_USER_VERSION > 1) {
                    $edit .= '<a class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                    $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                    $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_1][' . esc_attr($schedCount) . ']" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '</div>';
                    $edit .= '<div class="col-sm-3 text-center">';
                    $edit .= '<a style="display:none;" class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                    $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="2">' . esc_html__('Change image', 'blog2social') . '</button>';
                    $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_2][' . esc_attr($schedCount) . ']" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '</div>';
                    $edit .= '<div class="col-sm-3 text-center">';
                    $edit .= '<a style="display:none;" class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                    $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                    $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="3">' . esc_html__('Change image', 'blog2social') . '</button>';
                    $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_3][' . esc_attr($schedCount) . ']" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                } else {
                    $edit .= '<a class="btn btn-primary btn-circle b2sProFeatureModalBtn" data-title="' . esc_html__('Do you want to post multiple images?', 'blog2social') . '" data-type="multi-image">+</a><span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';
            }

            if ($networkId == 3) {
                $edit = '<div class="row"><br>';
                $edit .= '<div class="col-xs-12 col-sm-5 col-lg-3">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url($this->defaultImage) . '" class="linkedin-url-image b2s-post-item-details-url-image center-block img-responsive"  data-network-count="' . esc_attr($schedCount) . '"  data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="' . esc_attr($schedCount) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_image_url][' . esc_attr($schedCount) . ']">';
                $edit .= '<div class="clearfix"></div>';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="col-xs-12 col-sm-7 col-lg-9">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control linkedin-textarea-input b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';

                if ($networkType == 0 || $networkType == 1) {
                    $edit .= '<div class="col-sm-12 b2s-multi-image-area" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedCount . '">';
                    $edit .= '<div class="row b2s-margin-top-20">';
                    $edit .= '<div class="col-sm-3 text-center">';
                    if (B2S_PLUGIN_USER_VERSION > 1) {
                        $edit .= '<a class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="1">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_1][' . esc_attr($schedCount) . ']" data-image-count="1" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '</div>';
                        $edit .= '<div class="col-sm-3 text-center">';
                        $edit .= '<a style="display:none;" class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="2">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_2][' . esc_attr($schedCount) . ']" data-image-count="2" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '</div>';
                        $edit .= '<div class="col-sm-3 text-center">';
                        $edit .= '<a style="display:none;" class="btn btn-success btn-circle b2s-add-multi-image" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                        $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                        $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                        $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="3">' . esc_html__('Change image', 'blog2social') . '</button>';
                        $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_3][' . esc_attr($schedCount) . ']" data-image-count="3" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    } else {
                        $edit .= '<a class="btn btn-primary btn-circle b2sProFeatureModalBtn" data-title="' . esc_html__('Do you want to post multiple images?', 'blog2social') . '" data-type="multi-image">+</a><span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span>';
                    }
                    $edit .= '</div>';
                    $edit .= '</div>';
                    $edit .= '</div>';
                }
            }

            if ($networkId == 19) {
                $edit = '<div class="row"><br>';
                $edit .= '<div class="col-xs-12 col-sm-5 col-lg-3">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url($this->defaultImage) . '" class="xing-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="' . esc_attr($schedCount) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_image_url][' . esc_attr($schedCount) . ']">';
                $edit .= '<div class="clearfix"></div>';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="col-xs-12 col-sm-7 col-lg-9">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control xing-textarea-input b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_html__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';
            }

            if ($networkId == 15) {
                $edit = '<div class="row"><br>';
                $edit .= '<div class="col-xs-12 col-sm-5 col-lg-3">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url($this->defaultImage) . '" class="reddit-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="' . esc_attr($schedCount) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_image_url][' . esc_attr($schedCount) . ']">';
                $edit .= '<div class="clearfix"></div>';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="col-xs-12 col-sm-7 col-lg-9">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control reddit-textarea-input b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_html__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';
            }

            if ($networkId == 17) {
                $edit = '<div class="row"><br>';
                $edit .= '<div class="col-xs-12 col-sm-5 col-lg-3">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url($this->defaultImage) . '" class="b2s-url-image b2s-post-item-details-url-image center-block img-responsive"  data-network-count="' . esc_attr($schedCount) . '"  data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="' . esc_attr($schedCount) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_image_url][' . esc_attr($schedCount) . ']">';
                $edit .= '<div class="clearfix"></div>';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="col-xs-12 col-sm-7 col-lg-9">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control vk-textarea-input b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';
            }
            if ($networkId == 24) {
                $edit = '<div class="row"><br>';
                $edit .= '<div class="col-xs-12 col-sm-5 col-lg-3">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url($this->defaultImage) . '" class="b2s-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control"  data-network-count="' . esc_attr($schedCount) . '"  data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_image_url][' . esc_attr($schedCount) . ']">';
                $edit .= '<div class="clearfix"></div>';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="card" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="col-xs-12 col-sm-7 col-lg-9">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control b2s-textarea-input b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . esc_attr($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
                $edit .= '</div>';
            }
        } else {
            $edit = '<div class="row"><br>';
            if ((in_array($networkId, $this->showImageAreaProfile) && $networkType == 0) || (in_array($networkId, $this->showImageAreaPage) && $networkType == 1) || (in_array($networkId, $this->showImageAreaGroup) && $networkType == 2)) {
                $edit .= '<div class="col-xs-12 col-sm-5 col-lg-3">';
                $edit .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                $edit .= '<img src="' . esc_url($this->defaultImage) . '" class="b2s-post-item-details-url-image center-block img-responsive b2s-image-border" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="' . esc_attr($schedCount) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr(($imageUrl ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_image_url][' . esc_attr($schedCount) . ']">';
                $edit .= '<div class="clearfix"></div>';

                if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                    $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                    $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                    $edit .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                }

                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" ' . ((in_array($networkId, json_decode(B2S_PLUGIN_NETWORK_META_TAGS, true)['og'])) ? 'data-meta-type="og"' : '') . ' data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="col-xs-12 col-sm-7 col-lg-9">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '"  name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . ']" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . ' ' . (($networkId == 12) ? 'unique="currency"' : '') . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
            } else {
                $edit .= '<div class="col-xs-12">';
                $edit .= $infoArea;
                $edit .= '<div class="b2s-post-item-details-item-message-area" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                $edit .= '<textarea disabled="disabled" class="form-control b2s-post-item-sched-customize-text b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-count="' . esc_attr($schedCount) . '" data-network-text-limit="' . esc_attr($limitValue) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" placeholder="' . esc_attr__('Write something about your post...', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][sched_content][' . esc_attr($schedCount) . '] ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . ' ' . (($networkId == 12) ? 'unique="currency"' : '') . '>' . esc_html($message) . '</textarea>';
                if (!in_array($networkId, $this->allowNoEmoji)) {
                    $edit .= '<button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><img src="' . esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)) . '"/></button>';
                }
                $edit .= '</div>';
                $edit .= '</div>';
            }
            $edit .= '</div>';

            if ($networkId == 12) {
                if (!$this->isVideoMode) {
                    $edit .= '<div class="col-sm-12 b2s-multi-image-area" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedCount) . '">';
                    $edit .= '<div class="row b2s-margin-top-20">';
                    if (B2S_PLUGIN_USER_VERSION > 1) {
                        for ($i = 1; $i < 10; $i++) {
                            $edit .= '<div class="col-sm-1 text-center">';
                            $edit .= '<a class="btn btn-success btn-circle b2s-add-multi-image" ' . (($i > 1) ? 'style="display:none;"' : '') . ' data-image-count="' . esc_attr($i) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-picture"></i></a>';
                            $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-remove-btn" data-image-count="' . esc_attr($i) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-trash"></i></button>';
                            $edit .= '<button style="display:none;" class="btn btn-primary btn-circle b2s-multi-image-zoom-btn" data-image-count="' . esc_attr($i) . '" data-network-count="' . $schedCount . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"><i class="glyphicon glyphicon-zoom-in"></i></button>';
                            $edit .= '<img style="display:none;" src="" class="b2s-image-border b2s-post-item-details-url-image-multi center-block img-responsive" data-image-count="' . esc_attr($i) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                            $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-multi-image-modal-open" style="display:none;" data-network-count="' . esc_attr($schedCount) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-image-count="' . esc_attr($i) . '">' . esc_html__('Change image', 'blog2social') . '</button>';
                            $edit .= '<input type="hidden" class="b2s-add-multi-image-hidden-field" name="b2s[' . esc_attr($networkAuthId) . '][sched_multi_image_' . esc_attr($i) . '][' . esc_attr($schedCount) . ']" data-image-count="' . esc_attr($i) . '" data-network-count="' . esc_attr($schedCount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                            $edit .= '</div>';
                        }
                    } else {
                        $edit .= '<div class="col-sm-3 text-center">';
                        $edit .= '<a class="btn btn-primary btn-circle b2sProFeatureModalBtn" data-title="' . esc_html__('Do you want to post multiple images?', 'blog2social') . '" data-type="multi-image">+</a><span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span>';
                        $edit .= '</div>';
                    }
                    $edit .= '</div>';
                    $edit .= '</div>';
                }
            }
        }
        return $edit;
    }

    private function getUrlHtml($networkId, $networkType, $networkAuthId, $limit, $limitValue, $hideInfo = false, $class = '', $refeshBtn = false, $imageUrl = null) {
        if (in_array($networkId, $this->allowEditUrl)) {
            $urlLimit = ($limit !== false) ? ' onkeyup="networkLimitAll(\'' . esc_attr($networkAuthId) . '\',\'' . esc_attr($networkId) . '\',\'' . esc_attr($limitValue) . '\');"' : 'onkeyup="networkCount(\'' . esc_attr($networkAuthId) . '\');"';
            $isRequiredClass = (in_array($networkId, $this->requiredUrl)) ? 'required_network_url' : '';
            $isRequiredText = (!empty($isRequiredClass)) ? '<small>(' . esc_html__('required', 'blog2social') . ')</small>' : '';

            $url = '';
            if (!$this->isVideoMode) {
                if ((in_array($networkId, $this->showImageAreaProfile) && $networkType == 0) || (in_array($networkId, $this->showImageAreaPage) && $networkType == 1) || (in_array($networkId, $this->showImageAreaGroup) && $networkType == 2)) {
                    $url .= '<br>';
                    $url .= '<div class="row">';
                    $url .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                    $url .= '<div>';
                    $url .= '<button class="btn btn-primary btn-circle b2s-image-remove-btn" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-trash"></i></button>';
                    $url .= '<img src="' . esc_url((($imageUrl != null) ? $imageUrl : $this->defaultImage)) . '" class="b2s-post-item-details-url-image center-block img-responsive b2s-image-border" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-image-change="1" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $url .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr((($imageUrl != null) ? $imageUrl : "")) . '" name="b2s[' . esc_attr($networkAuthId) . '][image_url]">';
                    $url .= '</div>';
                    $url .= '<div class="clearfix"></div>';

                    if (in_array($networkId, $this->allowImageEditor) && current_user_can('upload_files') && !isset($this->viewMode) || (isset($this->viewMode) && $this->viewMode != 'modal' )) {
                        $isVersionInfo = (B2S_PLUGIN_USER_VERSION < 1) ? 'disabled="true"' : '';
                        $versionInfoBtn = (!empty($isVersionInfo)) ? ' <span class="label label-success">PRO</span>' : '';
                        $url .= '<button ' . $isVersionInfo . ' class="cropper-open btn btn-sm btn-primary center-block" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" ' . ($imageUrl ? '' : 'style="display:none;"') . '>' . esc_html__('cut & rotate image', 'blog2social') . '' . $versionInfoBtn . '</button>';
                    }
                    $url .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-network-count="-1" ' . ((in_array($networkId, json_decode(B2S_PLUGIN_NETWORK_META_TAGS, true)['og'])) ? 'data-meta-type="og"' : '') . ((in_array($networkId, json_decode(B2S_PLUGIN_NETWORK_META_TAGS, true)['twitter'])) ? 'data-meta-type="card"' : '') . ' data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-post-id="' . esc_attr($this->postId) . '" data-image-url="' . esc_attr($imageUrl) . '">' . esc_html__('Change image', 'blog2social') . '</button></div>';
                    $url .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12"' : 'col-xs-12 col-sm-7 col-lg-9 b2s-post-original-area" data-network-auth-id="' . esc_attr($networkAuthId) . '"') . '>';
                }

                $url .= (!$hideInfo) ? '<div class="b2s-post-item-details-url-title hidden-xs">Link ' . $isRequiredText . '</div>' : '';

                if (($networkId == 12) && isset($this->post_template[$networkId][$networkType]['addLink']) && $this->post_template[$networkId][$networkType]['addLink'] == false) {
                    $urlValue = '';
                } else if (($networkId == 1 || $networkId == 2 || $networkId == 24) && isset($this->post_template[$networkId][$networkType]['format']) && (int) $this->post_template[$networkId][$networkType]['format'] == 1 && isset($this->post_template[$networkId][$networkType]['addLink']) && $this->post_template[$networkId][$networkType]['addLink'] == false) {
                    $urlValue = '';
                    $isRequiredClass = '';
                } else {
                    $urlValue = $this->postUrl;
                }

                if ($refeshBtn && (trim(strtolower($this->postStatus)) == 'publish' || $this->b2sPostType == 'ex')) {
                    $url .= '<div class="input-group"><input class="form-control ' . esc_attr($class) . ' b2s-post-item-details-item-url-input ' . $isRequiredClass . ' complete_network_url" dir="ltr" name="b2s[' . esc_attr($networkAuthId) . '][url]" ' . $urlLimit . ' placeholder="' . esc_attr__('Link', 'blog2social') . '" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="' . esc_attr($networkType) . '"  value="' . esc_attr($urlValue) . '" name="b2s[' . esc_attr($networkAuthId) . '][url]"/><span class="input-group-addon"><span class="glyphicon glyphicon-refresh b2s-post-item-details-preview-url-reload" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-id="' . esc_attr($networkId) . '" aria-hidden="true"></span></span></div>';
                } else {
                    $url .= '<input class="form-control ' . esc_attr($class) . ' b2s-post-item-details-item-url-input ' . $isRequiredClass . ' complete_network_url" dir="ltr" name="b2s[' . esc_attr($networkAuthId) . '][url]" ' . $urlLimit . ' placeholder="' . esc_attr__('Link', 'blog2social') . '" data-network-count="-1" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr($urlValue) . '" name="b2s[' . esc_attr($networkAuthId) . '][url]"/>';
                }
                if ((in_array($networkId, $this->showImageAreaProfile) && $networkType == 0) || (in_array($networkId, $this->showImageAreaPage) && $networkType == 1) || (in_array($networkId, $this->showImageAreaGroup) && $networkType == 2)) {
                    $url .= '</div>';
                    $url .= '</div>';
                    $url .= '<div class="col-xs-12"><br></div>';
                }
            } else {
                $url = '<input type="hidden" name="b2s[' . esc_attr($networkAuthId) . '][url]" value="' . esc_attr($this->postUrl) . '">';
            }
        }

        return $url;
    }

    protected function hook_message($message) {
        return $message;
    }

    protected function hook_meta(array $meta) {
        return $meta;
    }

    protected function hook_sched_data(array $schedData) {
        return $schedData;
    }

    private function getHashTagsString($add = "\n\n", $limit = 0, $shuffle = false) {
        $hashTagsData = $this->hook_filter->get_wp_post_hashtag((int) $this->postId, $this->postData->post_type);
        $hashTags = '';
        if (is_array($hashTagsData) && !empty($hashTagsData)) {
            if ($shuffle) {
                shuffle($hashTagsData);
            }
            foreach ($hashTagsData as $tag) {
                if ($limit > 0) {
                    if (strlen($tag->name) > $limit) {
                        continue;
                    }
                }
                $hashTags .= ' #' . str_replace(array(" ", "-", '"', "'", "!", "?", ",", ".", ";", ":"), "", (function_exists('htmlspecialchars_decode') ? htmlspecialchars_decode($tag->name) : $tag->name));
            }
        }
        return (!empty($hashTags) ? (!empty($add) ? $add . trim($hashTags) : trim($hashTags)) : '');
    }

    private function getBoardHtml($networkAuthId, $networkId, $networkType = 0) {
        $board = '';
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getBoards', 'token' => B2S_PLUGIN_TOKEN, 'networkType' => $networkType, 'networkAuthId' => $networkAuthId, 'selBoard' => $this->selBoard, 'networkId' => $networkId)));
        if (is_object($result) && !empty($result) && isset($result->data) && !empty($result->data) && isset($result->result) && (int) $result->result == 1) {
            $board = '<select class="form-control b2s-select-area" name="b2s[' . esc_attr($networkAuthId) . '][board]">';
            $board .= $result->data;
            $board .= '</select>';
        }
        return $board;
    }

    private function getPrivacyStatusHtml($networkAuthId, $networkId, $networkType = 0) {
        $status = '<div class="clearfix"></div><div class="form-group"><label class="b2s-select-area-label" for="b2s[' . esc_attr($networkAuthId) . '][status_privacy]">' . esc_html__('Status Privacy', 'blog2social') . '</label><select class="form-control b2s-select-area" id="b2s[' . esc_attr($networkAuthId) . '][status_privacy]" name="b2s[' . esc_attr($networkAuthId) . '][status_privacy]">';
        $status .= '<option value="public">' . esc_html__('Public', 'blog2social') . '</option>';
        $status .= '<option value="private">' . esc_html__('Private', 'blog2social') . '</option>';
        $status .= '<option value="Unlisted">' . esc_html__('Unlisted', 'blog2social') . '</option>';
        $status .= '</select></div>';
        return $status;
    }

    private function getGroupsHtml($networkAuthId, $networkId) {
        $group = '';
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getGroups', 'token' => B2S_PLUGIN_TOKEN, 'networkAuthId' => $networkAuthId, 'selGroup' => $this->selGroup, 'networkId' => $networkId, 'lang' => B2S_LANGUAGE)));
        $changeDisplayName = in_array($networkId, $this->changeDisplayName) ? 'true' : 'false';
        if (is_object($result) && !empty($result) && isset($result->data) && !empty($result->data) && isset($result->result) && (int) $result->result == 1) {
            $group = '<select class="form-control b2s-select-area b2s-post-item-details-item-group-select" data-change-network-display-name="' . esc_attr($changeDisplayName) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-id="' . esc_attr($networkId) . '" name="b2s[' . esc_attr($networkAuthId) . '][group]">';
            $group .= $result->data;
            $group .= '</select>';
        }
        return $group;
    }

    private function getMarketplaceAreaHtml($networkAuthId = 0, $networkId = 0, $networkType = 0, $networkKind = 0) {
        $marketplace = '<div class="marketplace_area" data-network-id="' . esc_attr($networkId) . '" data-network-type="' . esc_attr($networkType) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:' . (($networkKind == 3) ? 'block' : 'none') . ';">';
        $marketplace .= '<input type="radio" id="marketplace_category_1" class="form-control marketplace_category" ' . (($this->selMarketplaceCategory == 1) ? 'checked=""' : '') . ' name="b2s[' . esc_attr($networkAuthId) . '][marketplace_category]" value="1"> <label class="" for="marketplace_category_1">' . esc_html__('Jobs & Projects', 'blog2social') . '</label> ';
        $marketplace .= '<input type="radio" id="marketplace_category_2" class="form-control marketplace_category" ' . (($this->selMarketplaceCategory == 2) ? 'checked=""' : '') . ' name="b2s[' . esc_attr($networkAuthId) . '][marketplace_category]" value="2"> <label class="" for="marketplace_category_2">' . esc_html__('Events', 'blog2social') . '</label> ';
        $marketplace .= '<input type="radio" id="marketplace_category_3" class="form-control marketplace_category" ' . (($this->selMarketplaceCategory == 3) ? 'checked=""' : '') . ' name="b2s[' . esc_attr($networkAuthId) . '][marketplace_category]" value="3"> <label class="" for="marketplace_category_3">' . esc_html__('Classified Ads', 'blog2social') . '</label> ';
        $marketplace .= '<div class="clearfix"></div>';
        $marketplace .= '<input type="radio" id="marketplace_type_1" class="form-control marketplace_type" ' . (($this->selMarketplaceType == 1) ? 'checked=""' : '') . ' name="b2s[' . esc_attr($networkAuthId) . '][marketplace_type]" value="1"> <label class="" for="marketplace_type_1">' . esc_html__('Offer', 'blog2social') . '</label> ';
        $marketplace .= '<input type="radio" id="marketplace_type_2" class="form-control marketplace_type" ' . (($this->selMarketplaceType == 2) ? 'checked=""' : '') . ' name="b2s[' . esc_attr($networkAuthId) . '][marketplace_type]" value="2"> <label class="" for="marketplace_type_2">' . esc_html__('Request', 'blog2social') . '</label>';
        $marketplace .= '<div class="clearfix"></div><br/>';
        $marketplace .= '</div>';
        return $marketplace;

        //<input type="radio" id="type[0]-1-2" checked="" name="b2s['.$networkAuthId.'][marketplace_type]" value="1"> <label class="" for="type[0]-1-2">Profil</label>
    }

    private function getTitleHtml($networkId = 0, $networkdAutId = 0, $networkKind = 0, $networkType = 0, $title = '') {
        $title = in_array($networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($title, $this->userLang)) : B2S_Util::getTitleByLanguage($title, $this->userLang);
        $maxLength = ($networkType == 1 && isset($this->limitCharacterTitle[$networkId][$networkKind])) ? (int) $this->limitCharacterTitle[$networkId][$networkKind] : 254;
        return '<input type="text" name="b2s[' . esc_attr($networkdAutId) . '][custom_title]" class="form-control b2s-post-item-details-item-title-input" data-network-auth-id="' . esc_attr($networkdAutId) . '" placeholder="' . esc_attr__('The Headline...', 'blog2social') . '" required="required" maxlength="' . esc_attr($maxLength) . '" value="' . esc_attr($title) . '" />';
    }

    private function getTagsHtml($networkId, $networkAuthId, $allowTags = true) {
        $tags = '<div class="b2s-post-item-details-tag-area">';
        $info = '';
        if (isset($this->limitTag[$networkId])) {
            $tags .= '<input type="hidden" data-network-auth-id="' . esc_attr($networkAuthId) . '" class="b2s-post-item-details-tag-limit" value="' . (int) $this->limitTag[$networkId] . '" />';
            $info = '(' . sprintf(esc_html__('max. %s Tags', 'blog2social'), $this->limitTag[$networkId]) . ')';
        }
        $tags .= '<div class="b2s-post-item-details-tag-title"> ' . esc_html__('Hashtags', 'blog2social') . ' ' . $info . ' </div>';
        $tags .= '<div class="b2s-post-item-details-tag-input form-inline">';
        $posttags = $this->hook_filter->get_wp_post_hashtag((int) $this->postId, $this->postData->post_type);
        $countTags = 0;
        $limit = false;
        if ($posttags && $allowTags) {
            foreach ($posttags as $tag) {
                $name = str_replace(" ", "", $tag->name);
                $countTags += 1;
                if (isset($this->limitTag[$networkId]) && $countTags > $this->limitTag[$networkId]) {
                    $limit = true;
                    continue;
                }
                $tags .= '<input class="form-control b2s-post-item-details-tag-input-elem" name="b2s[' . esc_attr($networkAuthId) . '][tags][]" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="' . esc_attr($name) . '">';
            }
        } else {
            $tags .= '<input class="form-control b2s-post-item-details-tag-input-elem" name="b2s[' . esc_attr($networkAuthId) . '][tags][]" data-network-auth-id="' . esc_attr($networkAuthId) . '" value="">';
        }


        $showRemoveTagBtn = ($countTags >= 2) ? '' : 'display:none;';
        $showAddTagBtn = ($limit) ? 'display:none;' : '';
        $tags .= '<div class="form-control b2s-post-item-details-tag-add-div">';
        $tags .= '<span class="remove-tag-btn glyphicon glyphicon-minus" data-network-auth-id="' . esc_attr($networkAuthId) . '" style="' . $showRemoveTagBtn . '" onclick="removeTag(\'' . esc_attr($networkAuthId) . '\');" ></span>';
        $tags .= '<span class="ad-tag-btn glyphicon glyphicon-plus" data-network-auth-id="' . esc_attr($networkAuthId) . '" style="' . $showAddTagBtn . '" onclick="addTag(\'' . esc_attr($networkAuthId) . '\');" ></span>';
        $tags .= '</div>';
        $tags .= '</div>';
        $tags .= '</div>';

        return $tags;
    }

    private function getRelayBtnHtml($networkAuthId, $networkId) {
        $relay = '<div class="form-group b2s-post-relay-area-select pull-left"><div class="checkbox checbox-switch switch-success"><label>';
        $relay .= '<input type="checkbox" class="b2s-post-item-details-relay form-control" data-user-version="' . esc_attr(B2S_PLUGIN_USER_VERSION) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" name="b2s[' . esc_attr($networkAuthId) . '][post_relay]" value="1"/>';
        $relay .= '<span></span>';
        $relay .= esc_html__('Enable Retweets for all Tweets with the selected profile', 'blog2social') . ' <a href="#" class="btn-xs hidden-sm b2sInfoPostRelayModalBtn">' . esc_html__('Info', 'blog2social') . '</a>';
        $relay .= ' </label></div></div>';
        return $relay;
    }

    private function getRelayContentHtml($networkAuthId, $networkId) {
        $relay = '';
        if (B2S_PLUGIN_USER_VERSION > 0) {
            $relay .= '<div class="b2s-post-item-relay-area-details">';
            $relay .= '<ul class="list-group b2s-post-item-relay-area-details-ul" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;">';
            $relay .= '<li class="list-group-item">';

            for ($relaycount = 0; $relaycount < $this->setRelayCount; $relaycount++) {

                $relay .= '<div class="form-group b2s-post-item-relay-area-details-row" data-network-count="' . esc_attr($relaycount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none">';

                $relay .= $relaycount != 0 ? '<div class="clearfix"></div><hr class="b2s-hr-small">' : '';

                $relay .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-relay-area-label-account" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($relaycount) . '">' . esc_html__('Account', 'blog2social') . '</label>';
                $relay .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-relay-area-label-delay" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($relaycount) . '">' . esc_html__('Delay', 'blog2social') . '</label>';

                $relay .= '<div class="clearfix"></div>';

                $relay .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-relay-area-div-account" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($relaycount) . '">';
                $relay .= '<select name="b2s[' . esc_attr($networkAuthId) . '][post_relay_account][' . esc_attr($relaycount) . ']" class="form-control b2s-select b2s-post-item-details-relay-input-account" data-network-count="' . esc_attr($relaycount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;" disabled="disabled">';
                $relay .= '</select></div>';

                $relay .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-relay-area-div-delay" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($relaycount) . '">';
                $relay .= '<select name="b2s[' . esc_attr($networkAuthId) . '][post_relay_delay][' . esc_attr($relaycount) . ']" class="form-control b2s-select b2s-post-item-details-relay-input-delay" data-network-count="' . esc_attr($relaycount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;" disabled="disabled">';
                $relay .= '<option value="15">15' . esc_html__('min', 'blog2social') . '</option>';
                $relay .= '<option value="30">30' . esc_html__('min', 'blog2social') . '</option>';
                $relay .= '<option value="45">45' . esc_html__('min', 'blog2social') . '</option>';
                $relay .= '<option value="60">60' . esc_html__('min', 'blog2social') . '</option>';
                $relay .= '</select></div>';

                $relay .= '<div class="col-md-2 del-padding-left">';
                $relay .= ( $relaycount >= 1) ? '<button class="btn btn-link b2s-post-item-details-relay-input-hide"  data-network-count="' . esc_attr($relaycount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="1" style="display:none;">-' . esc_html__('delete', 'blog2social') . '</button>' : '';
                $relay .= $relaycount < $this->setRelayCount - 1 ? '<button class="btn btn-link b2s-post-item-details-relay-input-add"  data-network-count="' . esc_attr($relaycount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="1" style="display:none;">+' . esc_html__('Add Retweet', 'blog2social') . '</button>' : '';
                $relay .= '</div>';
                $relay .= '</div>';
                $relay .= '<div class="clearfix"></div>';
            }
            $relay .= '</li>';
            $relay .= '</ul>';
            $relay .= '</div>';
        }
        return $relay;
    }

    private function getShippingTimeHtml($networkAuthId, $networkTyp, $networkId, $networkType, $message, $isRequiredTextarea, $textareaOnKeyUp, $limit, $limitValue, $imageUrl = null) {

        $isSelectedSched = (B2S_PLUGIN_USER_VERSION > 0 && (trim(strtolower($this->postStatus)) == 'future' || !empty($this->selSchedDate))) ? 'selected="selected"' : '';
        $isSelectedNow = (empty($isSelectedSched)) ? 'selected="selected"' : '';
        $showSchedRegularly = (!($networkTyp == 2 || (in_array($networkId, $this->noScheduleRegularly)) || ($networkTyp == 1 && in_array($networkId, $this->noScheduleRegularlyPage)))) ? true : false;

        $shipping = '<br>';
        $shipping .= '<select name="b2s[' . esc_attr($networkAuthId) . '][releaseSelect]" data-user-version="' . esc_attr(B2S_PLUGIN_USER_VERSION) . '" data-network-type="' . esc_attr($networkTyp) . '" data-network-customize-content="' . (in_array($networkId, $this->allowSchedCustomizeContent) || ($this->isVideoMode && in_array($networkId, $this->videoScheduleNetworks)) ? 1 : 0) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" class="form-control b2s-select b2s-post-item-details-release-input-date-select ' . (B2S_PLUGIN_USER_VERSION == 0 ? 'b2s-post-item-details-release-input-date-select-reset' : '') . '" >';
        $shipping .= '<option value="0" ' . $isSelectedNow . '>' . esc_html__('Share Now', 'blog2social') . '</option>';

        $isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? ' [' . esc_html__("SMART", "blog2social") . ']' : '';

        $shipping .= (!$this->isVideoMode || ($this->isVideoMode && in_array($networkId, $this->videoScheduleNetworks))) ? '<option value="1" ' . $isSelectedSched . '>' . esc_html__('Schedule for specific dates', 'blog2social') . $isPremium . '</option>' : '';
        $shipping .= ($showSchedRegularly && !$this->isVideoMode) ? '<option value="2">' . esc_html__('Schedule Recurrent Post', 'blog2social') . $isPremium . '</option>' : '';
        $shipping .= '</select>';

        if (B2S_PLUGIN_USER_VERSION > 0) {
            $shipping .= '<div class="b2s-post-item-details-release-area-details">';
//TOS Twitter 032018
            $shipping .= '<div class="b2s-network-tos-sched-warning" data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display: none;"><div class="clearfix"></div><div class="alert b2s-network-tos-sched-alert alert-warning">' . esc_html__('Please keep in mind that according to Twitter’s new TOS, users are no longer allowed to post identical or substantially similar content to multiple accounts or multiple duplicate updates on one account.', 'blog2social') . '<br><strong>' . esc_html__('Violating these rules can result in Twitter suspending your account. Always vary your Tweets with different comments, hashtags or handles to prevent duplicate posts.', 'blog2social') . '</strong> <a href="' . esc_url(B2S_Tools::getSupportLink('network_tos_blog_032018')) . '" target="_blank">' . esc_html__('Learn more about this', 'blog2social') . '</a></div></div>';
            $shipping .= '<ul class="list-group b2s-post-item-details-release-area-details-ul" data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;">';
            $shipping .= '<li class="list-group-item">';

//Sched post
            $time = time();
            if (trim(strtolower($this->postStatus)) == 'future') {
                $time = strtotime($this->postData->post_date_gmt);
            }
//Routing form calendar
            if (!empty($this->selSchedDate)) {
                $time = strtotime($this->selSchedDate);
            }

            if (date('H') == '23' && date('i') >= 30) {
                $time = strtotime('+ 1 days');
            }

            $currentDate = (strtolower(substr(get_locale(), 0, 2)) == 'de') ? date('d.m.Y', $time) : date('Y-m-d', $time);
            $currentDay = date('d', $time);

            $maxSchedCount = ($networkId == 18) ? 1 : $this->maxSchedCount;
            for ($schedcount = 0; $schedcount < $maxSchedCount; $schedcount++) {
                $shipping .= '<div class="form-group b2s-post-item-details-release-area-details-row" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none">';

                $shipping .= $schedcount != 0 ? '<div class="clearfix"></div><hr class="b2s-hr-small">' : '';

                //deprecated Network
                if ($networkId == 8) {
                    $shipping .= '<div class="network-tos-deprecated-warning alert alert-danger"  style="display: none;" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '">' . esc_html__("Please note: Your account is connected via an old XING API that is no longer supported by XING after March 31. Please connect your XING profile, as well as your XING company pages (Employer branding profiles) and business pages with the new XING interface in the Blog2Social network settings. To do this, go to the Blog2Social Networks section and connect your XING accounts with the new XING.", "blog2social") . ' <a href="' . esc_url(B2S_Tools::getSupportLink('network_tos_blog_032019')) . '" target="_blank">' . esc_html__('Learn more', 'blog2social') . '</a></div>';
                }
                //deprecated Network
                if ($networkId == 10) {
                    $shipping .= '<div class="network-tos-deprecated-warning alert alert-danger"  style="display: none;" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '">' . esc_html__("Please note: Google will shut down Google+ for all private accounts (profiles, pages, groups) on 2nd April 2019. You can find further information and the next steps, including how to download your photos and other content here:", "blog2social") . ' <a href="https://support.google.com/plus/answer/9195133" target="_blank">https://support.google.com/plus/answer/9195133</a></div>';
                }

                $shipping .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-release-area-label-interval" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '">' . esc_html__('Repeats', 'blog2social') . '</label>';
                $shipping .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-release-area-label-duration" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '">' . esc_html__('Duration', 'blog2social') . '</label>';

//new since 4.5.0
                $shipping .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-release-area-label-duration-month" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" style="display:none;">' . esc_html__('Duration', 'blog2social') . '</label>';
                $shipping .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-release-area-label-duration-time" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" style="display:none;">' . esc_html__('Number of repeats', 'blog2social') . '</label>';
                $shipping .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-release-area-label-select-day" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" style="display:none;">' . esc_html__('Day of month', 'blog2social') . '</label>';
                $shipping .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-release-area-label-select-timespan" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" style="display:none;">' . esc_html__('Repeats every (days)', 'blog2social') . '</label>';

                $shipping .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-release-area-label-date" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '">' . esc_html__('Start date', 'blog2social') . '</label>';
                $shipping .= '<label class="hidden-sm hidden-xs col-md-2 del-padding-left b2s-post-item-details-release-area-label-time" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '">' . esc_html__('Time to publish', 'blog2social') . '</label>';
                $shipping .= '<label class="hidden-sm hidden-xs col-md-4 del-padding-left b2s-post-item-details-release-area-label-day" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '">' . esc_html__('Days', 'blog2social') . '</label>';

                $shipping .= '<div class="clearfix"></div>';

                if ($showSchedRegularly) {
                    $shipping .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-release-area-div-interval" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '"><select name="b2s[' . esc_attr($networkAuthId) . '][intervalSelect][' . esc_attr($schedcount) . ']" class="form-control b2s-select b2s-post-item-details-release-input-interval-select" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;" disabled="disabled">';
                    $shipping .= '<option value="0" selected="selected">' . esc_html__('weekly', 'blog2social') . '</option>';
                    $shipping .= '<option value="1">' . esc_html__('monthly', 'blog2social') . '</option>';
                    $shipping .= '<option value="2">' . esc_html__('own period', 'blog2social') . '</option>';
                    $shipping .= '</select></div>';

                    $shipping .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-release-area-div-duration" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '"><select name="b2s[' . esc_attr($networkAuthId) . '][weeks][' . esc_attr($schedcount) . ']" class="form-control b2s-select b2s-post-item-details-release-input-weeks" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;" disabled="disabled">';
                    $defaultWeek = isset($this->defaultScheduleTime[$networkId][$schedcount]['weeks']) ? $this->defaultScheduleTime[$networkId][$schedcount]['weeks'] : 1;
                    for ($i = 1; $i <= $this->maxWeekTimeSelect; $i++) {
                        $weekName = ($i == 1) ? __('Week', 'blog2social') : __('Weeks', 'blog2social');
                        $shipping .= '<option value="' . esc_attr($i) . '" ' . ($defaultWeek == $i ? 'selected="selected"' : '') . '>' . esc_html($i . ' ' . $weekName) . '</option>';
                    }
                    $shipping .= '</select></div>';

//new since 4.5.0
                    $shipping .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-release-area-div-duration-month" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" style="display:none;"><select name="b2s[' . esc_attr($networkAuthId) . '][duration_month][' . esc_attr($schedcount) . ']" class="form-control b2s-select b2s-post-item-details-release-input-months" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;" disabled="disabled">';
                    $defaultMonth = isset($this->defaultScheduleTime[$networkId][$schedcount]['month']) ? $this->defaultScheduleTime[$networkId][$schedcount]['month'] : 1;
                    for ($i = 1; $i <= $this->maxMonthTimeSelect; $i++) {
                        $monthName = ($i == 1) ? __('Month', 'blog2social') : __('Months', 'blog2social');
                        $shipping .= '<option value="' . esc_attr($i) . '" ' . ($defaultMonth == $i ? 'selected="selected"' : '') . '>' . esc_html($i . ' ' . $monthName) . '</option>';
                    }
                    $shipping .= '</select></div>';

//new since 4.5.0
                    $shipping .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-release-area-div-duration-time" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" style="display:none;"><select name="b2s[' . esc_attr($networkAuthId) . '][duration_time][' . esc_attr($schedcount) . ']" class="form-control b2s-select b2s-post-item-details-release-input-times" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;" disabled="disabled">';
                    $defaultTime = isset($this->defaultScheduleTime[$networkId][$schedcount]['time']) ? $this->defaultScheduleTime[$networkId][$schedcount]['time'] : 1;
                    for ($i = 1; $i <= $this->maxTimeSelect; $i++) {
                        $timeName = ""; //($i == 1) ? __('Time', 'blog2social') : __('Times', 'blog2social');
                        $shipping .= '<option value="' . esc_attr($i) . '" ' . ($defaultTime == $i ? 'selected="selected"' : '') . '>' . esc_html($i . ' ' . $timeName) . '</option>';
                    }
                    $shipping .= '</select></div>';

//new since 4.5.0
                    $shipping .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-release-area-label-select-day" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" style="display:none;"><select name="b2s[' . esc_attr($networkAuthId) . '][select_day][' . esc_attr($schedcount) . ']" class="form-control b2s-select b2s-post-item-details-release-input-select-day" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;" disabled="disabled">';
                    $defaultTime = isset($this->defaultScheduleTime[$networkId][$schedcount]['select_day']) ? $this->defaultScheduleTime[$networkId][$schedcount]['select_day'] : 1;
                    for ($i = 1; $i <= $this->maxDaySelect; $i++) {
                        $shipping .= '<option value="' . esc_attr($i) . '" ' . ($defaultTime == $i ? 'selected="selected"' : '') . '>' . esc_html($i) . '</option>';
                    }
                    $shipping .= '<option value="0">' . esc_html__("End Of Month", "blog2social") . '</option>';
                    $shipping .= '</select></div>';
                }

//new since 4.5.0
                $shipping .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-release-area-label-select-timespan" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" style="display:none;"><input type="number" min="1" max="100" placeholder="' . esc_html__('Timespan', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][select_timespan][' . esc_attr($schedcount) . ']" data-network-id="' . esc_attr($networkId) . '" data-network-type="' . esc_attr($networkTyp) . '" data-network-count="' . esc_attr($schedcount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"  class="b2s-post-item-details-release-input-select-timespan form-control" style="display:none;"  disabled="disabled" value="1"></div>';

                $shipping .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-release-area-label-date" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '"><input type="text" placeholder="' . esc_attr__('Date', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][date][' . esc_attr($schedcount) . ']" data-network-id="' . esc_attr($networkId) . '" data-network-type="' . esc_attr($networkTyp) . '" data-network-count="' . esc_attr($schedcount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"  class="b2s-post-item-details-release-input-date form-control" style="display:none;"  disabled="disabled" readonly value="' . esc_attr($currentDate) . '"></div>';
                $shipping .= '<div class="col-xs-12 col-sm-6 col-md-2 del-padding-left b2s-post-item-details-release-area-label-time" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '"><input type="text" placeholder="' . esc_attr__('Time', 'blog2social') . '" name="b2s[' . esc_attr($networkAuthId) . '][time][' . esc_attr($schedcount) . ']" data-network-id="' . esc_attr($networkId) . '" data-network-type="' . esc_attr($networkTyp) . '" data-network-count="' . esc_attr($schedcount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '"  class="b2s-post-item-details-release-input-time form-control" style="display:none;" disabled="disabled" readonly value=""></div>';
                $shipping .= '<div class="col-xs-12 col-sm-6 col-md-4 del-padding-left b2s-post-item-details-release-area-label-day" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '">';

                if ($showSchedRegularly) {
                    $shipping .= '<div class="b2s-post-item-details-release-input-daySelect" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '"  style="display:none;">';
                    $shipping .= '<input id="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-mo" type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][mo][' . $schedcount . ']" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-mo" value="1" disabled="disabled"><label for="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-mo" class="b2s-post-item-details-release-input-lable-day">' . esc_html__('Mon', 'blog2social') . '</label>'; //MO
                    $shipping .= '<input id="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-di" type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][di][' . $schedcount . ']" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-di" value="1" disabled="disabled"><label for="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-di" class="b2s-post-item-details-release-input-lable-day">' . esc_html__('Tue', 'blog2social') . '</label>'; //Di
                    $shipping .= '<input id="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-mi" type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][mi][' . $schedcount . ']" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-mi" value="1" disabled="disabled"><label for="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-mi" class="b2s-post-item-details-release-input-lable-day">' . esc_html__('Wed', 'blog2social') . '</label>'; //Mi
                    $shipping .= '<input id="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-do" type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][do][' . $schedcount . ']" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-do" value="1" disabled="disabled"><label for="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-do" class="b2s-post-item-details-release-input-lable-day">' . esc_html__('Thu', 'blog2social') . '</label>'; //Do
                    $shipping .= '<input id="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-fr" type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][fr][' . $schedcount . ']" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-fr" value="1" disabled="disabled"><label for="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-fr" class="b2s-post-item-details-release-input-lable-day">' . esc_html__('Fri', 'blog2social') . '</label>'; //Fr
                    $shipping .= '<input id="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-sa" type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][sa][' . $schedcount . ']" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-sa" value="1" disabled="disabled"><label for="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-sa" class="b2s-post-item-details-release-input-lable-day">' . esc_html__('Sat', 'blog2social') . '</label>'; //Sa
                    $shipping .= '<input id="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-so" type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][so][' . $schedcount . ']" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="' . esc_attr($schedcount) . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-so" value="1" disabled="disabled"><label for="b2s-' . esc_attr($networkAuthId) . '-' . esc_attr($schedcount) . '-so" class="b2s-post-item-details-release-input-lable-day">' . esc_html__('Sun', 'blog2social') . '</label>'; //So
                    $shipping .= '</div>';
                }
                $shipping .= '</div>';
                $shipping .= '<div class="col-md-2 del-padding-left">';
                if (!($networkTyp >= 1 && (in_array($networkId, $this->addNoMoreSchedPage) || in_array($networkId, $this->addNoMoreSchedGroup)))) {
                    $shipping .= ( $schedcount >= 1) ? '<button class="btn btn-link b2s-post-item-details-release-input-hide"  data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="1" style="display:none;">-' . esc_html__('delete', 'blog2social') . '</button>' : '';
                    $shipping .= $schedcount < $maxSchedCount - 1 ? '<button class="btn btn-link b2s-post-item-details-release-input-add" data-network-id="' . esc_attr($networkId) . '" data-network-type="' . esc_attr($networkTyp) . '" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-count="1" style="display:none;">+' . esc_html__('add another post', 'blog2social') . '</button>' : '';
                }
                $shipping .= '</div>';

//since 4.8.0 customize content
//Add 7.1.0  || (in_array($networkId, $this->videoScheduleNetworks) && $this->isVideoMode) for customize schedule videos
                if (in_array($networkId, $this->allowSchedCustomizeContent) || ($this->isVideoMode && in_array($networkId, $this->videoScheduleNetworks))) {
                    $countCharacter = 0;
                    if ($limit !== false) {
                        $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '">' . (int) esc_html($countCharacter) . '</span>/' . esc_html($limitValue) . ' ' . esc_html__('characters', 'blog2social') . '</span>';
                    } else {
                        $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '">' . (int) esc_html($countCharacter) . '</span> ' . esc_html__('characters', 'blog2social') . '</span>';
                    }

                    $edit = '<div class="pull-right hidden-xs b2s-post-item-info-area" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedcount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">';
                    $edit .= '<button class="btn btn-xs btn-link b2s-post-ship-item-copy-original-text" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedcount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" >' . esc_html__('Copy from original', 'blog2social') . '</button> | ';
                    if (in_array($networkId, $this->getText)) {
                        $edit .= '<button class="btn btn-xs btn-link b2s-post-ship-item-full-text" data-network-id="' . esc_attr($networkId) . '" data-network-count="' . esc_attr($schedcount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" >' . esc_html__('Insert full-text', 'blog2social') . '</button> | ';
                    }
                    $edit .= '<button class="btn btn-xs btn-link b2s-post-ship-item-message-delete" data-network-count="' . esc_attr($schedcount) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '">' . esc_html__('Delete text', 'blog2social') . '</button> | ';
                    $edit .= $textareaLimitInfo . '</div>';
                    $shipping .= '<div class="form-group b2s-post-item-details-release-customize-sched-area-details-row" data-network-count="' . esc_attr($schedcount) . '"  data-network-auth-id="' . esc_attr($networkAuthId) . '" style="display:none;">';
                    $shipping .= '<div class="clearfix"></div>';
                    $shipping .= $this->getCustomEditSchedArea($schedcount, $networkId, $networkAuthId, $networkType, $message, $isRequiredTextarea, $textareaOnKeyUp, $limit, $limitValue, $edit, $imageUrl);
                    $shipping .= '</div>';
                }

                $shipping .= '</div>';
            }
            $shipping .= '<div class="col-xs-12 del-padding-left">';
            $shipping .= '<label class="b2s-settings-time-zone-text"></label>';
            $shipping .= '<button class="btn btn-sm btn-link pull-right b2s-post-item-details-release-area-sched-for-all" data-network-auth-id="' . esc_attr($networkAuthId) . '">' . esc_html__('Apply Settings To All Networks', 'blog2social') . '</button>';
            $shipping .= '<label class="pull-right btn btn-link btn-sm b2s-post-item-details-release-save-settings-label" data-network-auth-id="' . esc_attr($networkAuthId) . '"><input class="b2s-post-item-details-release-save-settings" data-network-auth-id="' . esc_attr($networkAuthId) . '" type="checkbox" name="b2s[' . esc_attr($networkAuthId) . '][saveSchedSetting]" value="1" disabled="disabled"> ' . esc_html__('Save as best time for this network', 'blog2social') . '</label>';
            $shipping .= '</div><div class="clearfix"></div>';
            $shipping .= '</li>';
            $shipping .= '</ul>';
            $shipping .= '</div>';
        }
        return $shipping;
    }

    public function setPostUrl($value) {
        $this->postUrl = $value;
    }

    public function setTitle($value) {
        if ($this->postData) {
            $this->postData->post_title = $value;
        }
    }

    public function getMessagebyTemplate($data) {
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
//            $appendFirst = true;
//            if (strpos($message, '{CONTENT}') !== false) {
//                $message = preg_replace("/\{CONTENT\}/", addcslashes($preContent, "\\$"), $message);
//                $appendFirst = false;
//            }
//            $message = preg_replace(array("/\{TITLE\}/", "/\{EXCERPT\}/", "/\{KEYWORDS\}/", "/\{AUTHOR\}/"), "", $message);
//            if ($appendFirst) {
//                $message = $preContent . ' ' . $message;
//            }
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
                if ($this->default_template != false && isset($this->default_template[$data->networkId][$data->networkType]['disableKeywords']) && $this->default_template[$data->networkId][$data->networkType]['disableKeywords'] == true) {
                    $message = stripslashes(preg_replace("/\{KEYWORDS\}/", '', $message));
                } else {
                    $hashtags = $this->getHashTagsString("", ((isset($this->limitHashTagCharacter[$data->networkId])) ? $this->limitHashTagCharacter[$data->networkId] : 0), ((isset($post_template['shuffleHashtags']) && $post_template['shuffleHashtags'] == true) ? true : false));
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
            if (!empty($this->postUrl) && $data->networkId == 2) {
                $limit = 254;
            }
            $message = B2S_Util::getExcerpt($message, 0, $limit);
        }

        return $message;
    }

}
