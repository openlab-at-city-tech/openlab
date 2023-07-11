<?php

class Ajax_Get {

    static private $instance = null;

    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('wp_ajax_b2s_ship_item', array($this, 'getShipItem'));
        add_action('wp_ajax_b2s_sort_data', array($this, 'getSortData'));
        add_action('wp_ajax_b2s_get_sched_posts_by_user_auth', array($this, 'getSchedPostsByUserAuth'));
        add_action('wp_ajax_b2s_get_network_board_and_group', array($this, 'getNetworkBoardAndGroup'));
        add_action('wp_ajax_b2s_publish_post_data', array($this, 'getPublishPostData'));
        add_action('wp_ajax_b2s_sched_post_data', array($this, 'getSchedPostData'));
        add_action('wp_ajax_b2s_approve_post_data', array($this, 'getApprovePostData'));
        add_action('wp_ajax_b2s_ship_navbar_item', array($this, 'getNavbarItem'));
        add_action('wp_ajax_b2s_scrape_url', array($this, 'scrapeUrl'));
        add_action('wp_ajax_b2s_get_settings_sched_time_default', array($this, 'getSettingsSchedTimeDefault'));
        add_action('wp_ajax_b2s_get_settings_sched_time_user', array($this, 'getUserTimeSettings'));
        add_action('wp_ajax_b2s_ship_item_full_text', array($this, 'getShipItemFullText'));
        add_action('wp_ajax_b2s_ship_item_reload_url', array($this, 'getShipItemReloadUrl'));
        add_action('wp_ajax_b2s_get_faq_entries', array($this, 'getFaqEntries'));
        add_action('wp_ajax_b2s_get_calendar_events', array($this, 'getCalendarEvents'));
        add_action('wp_ajax_b2s_get_post_edit_modal', array($this, 'getPostEditModal'));
        add_action('wp_ajax_b2s_get_calendar_filter_network_auth', array($this, 'getCalendarFilterNetworkAuth'));
        add_action('wp_ajax_b2s_get_image_modal', array($this, 'getImageModal'));
        add_action('wp_ajax_b2s_get_multi_widget_content', array($this, 'getMultiWidgetContent'));
        add_action('wp_ajax_b2s_get_stats', array($this, 'getStats'));
        add_action('wp_ajax_b2s_get_blog_post_status', array($this, 'getBlogPostStatus'));
        add_action('wp_ajax_b2s_support_systemrequirements', array($this, 'b2sSupportSystemRequirements'));
        add_action('wp_ajax_b2s_search_user', array($this, 'searchUser'));
        add_action('wp_ajax_b2s_get_select_mandant_user', array($this, 'getSelectMandantUser'));
        add_action('wp_ajax_b2s_get_edit_template', array($this, 'getEditTemplateForm'));
        add_action('wp_ajax_b2s_check_draft_exists', array($this, 'checkDraftExists'));
        add_action('wp_ajax_b2s_get_curation_ship_details', array($this, 'getCurationShipDetails'));
        add_action('wp_ajax_b2s_get_network_auth_settings', array($this, 'getNetworkAuthSettings'));
        add_action('wp_ajax_b2s_update_post_box', array($this, 'updatePostBox'));
        add_action('wp_ajax_b2s_get_image_caption', array($this, 'getImageCaption'));
        add_action('wp_ajax_b2s_load_insights', array($this, 'loadInsights'));
        add_action('wp_ajax_b2s_get_video_upload_data', array($this, 'getVideoUploadData'));
    }

    public function getBlogPostStatus() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            $status = '';
            if (isset($_GET['post_id'])) {
                $status = ((int) $_GET['post_id'] > 0) ? get_post_status((int) $_GET['post_id']) : '';
            }
            echo json_encode($status);
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function scrapeUrl() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['url']) && !empty($_POST['url'])) {
                $data = B2S_Util::scrapeUrl(esc_url_raw(wp_unslash($_POST['url'])));
                $scrapeError = ($data !== false) ? false : true;
                require_once (B2S_PLUGIN_DIR . 'includes/B2S/Curation/View.php');
                $curation = new B2S_Curation_View();
                $preview = $curation->getCurationPreviewHtml(esc_url_raw(wp_unslash($_POST['url'])), $data);
                if (!empty($preview)) {
                    if (isset($_POST['loadSettings']) && filter_var(wp_unslash($_POST['loadSettings']), FILTER_VALIDATE_BOOLEAN)) {
                        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getProfileUserAuth', 'token' => B2S_PLUGIN_TOKEN)));
                        if (isset($result->result) && (int) $result->result == 1 && isset($result->data) && !empty($result->data) && isset($result->data->mandant) && isset($result->data->auth) && !empty($result->data->mandant) && !empty($result->data->auth)) {
                            /*
                             * since V7.0 Remove Video Networks
                             */
                            $isVideoNetwork = unserialize(B2S_PLUGIN_NETWORK_SUPPORT_VIDEO);
                            foreach ($result->data->auth as $a => $auth) {
                                foreach ($auth as $u => $item) {
                                    if (in_array($item->networkId, $isVideoNetwork)) {
                                        if (!in_array($item->networkId, array(1, 2, 6, 12, 38, 39))) {
                                            unset($result->data->auth->{$a[$u]});
                                        }
                                    }
                                }
                            }
                            echo json_encode(array('result' => true, 'preview' => $preview, 'scrapeError' => $scrapeError, 'settings' => $curation->getShippingDetails($result->data->mandant, $result->data->auth)));
                            wp_die();
                        }
                        echo json_encode(array('result' => false, 'preview' => $preview, 'scrapeError' => $scrapeError, 'error' => 'NO_AUTH'));
                        wp_die();
                    } else {
                        echo json_encode(array('result' => true, 'preview' => $preview, 'scrapeError' => $scrapeError));
                        wp_die();
                    }
                }
            }
            echo json_encode(array('result' => false, 'preview' => '', 'scrapeError' => false, 'error' => 'NO_PREVIEW'));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getSortData() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
            require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
            /* Sort */
            $b2sType = isset($_POST['b2sType']) ? trim(sanitize_text_field(wp_unslash($_POST['b2sType']))) : "";
            $b2sPagination = isset($_POST['b2sPagination']) ? (int) $_POST['b2sPagination'] : 1;
            $b2sShowPagination = !isset($_POST['b2sShowPagination']) || (int) $_POST['b2sShowPagination'] == 1;
            $b2sSortPostTitle = isset($_POST['b2sSortPostTitle']) ? trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostTitle']))) : "";
            $b2sSortPostAuthor = (isset($_POST['b2sSortPostAuthor']) && (int) $_POST['b2sSortPostAuthor'] > 0) ? (int) $_POST['b2sSortPostAuthor'] : 0;
            $b2sSortPostSchedDate = isset($_POST['b2sSortPostSchedDate']) ? (in_array(trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostSchedDate']))), array('desc', 'asc')) ? trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostSchedDate']))) : '') : '';
            $b2sSortPostPublishDate = isset($_POST['b2sSortPostPublishDate']) ? (in_array(trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostPublishDate']))), array('desc', 'asc')) ? trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostPublishDate']))) : '') : '';
            $b2sSortPostStatus = isset($_POST['b2sSortPostStatus']) ? (in_array(trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostStatus']))), array('publish', 'future', 'pending')) ? trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostStatus']))) : '') : '';
            $b2sSortPostShareStatus = isset($_POST['b2sSortPostShareStatus']) ? (in_array(trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostShareStatus']))), array('never', 'shared', 'scheduled', 'autopost', 'repost')) ? trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostShareStatus']))) : '') : '';
            $b2sShowByDate = isset($_POST['b2sShowByDate']) ? (preg_match("#^[0-9\-.\]]+$#", trim(sanitize_text_field(wp_unslash($_POST['b2sShowByDate'])))) ? trim(sanitize_text_field(wp_unslash($_POST['b2sShowByDate']))) : '') : ''; //YYYY-mm-dd
            $b2sShowByNetwork = isset($_POST['b2sShowByNetwork']) ? (int) $_POST['b2sShowByNetwork'] : 0;
            $b2sUserAuthId = isset($_POST['b2sUserAuthId']) ? (int) $_POST['b2sUserAuthId'] : 0;
            $b2sPostBlogId = isset($_POST['b2sPostBlogId']) ? (int) $_POST['b2sPostBlogId'] : 0;
            $b2sSortPostCat = isset($_POST['b2sSortPostCat']) ? (int) $_POST['b2sSortPostCat'] : 0;
            $b2sSortPostType = (isset($_POST['b2sSortPostType']) && !empty($_POST['b2sSortPostType'])) ? trim(sanitize_text_field(wp_unslash($_POST['b2sSortPostType']))) : "";
            $b2sSelectSchedDate = isset($_POST['b2sSchedDate']) ? (preg_match("#^[0-9\-.\]]+$#", trim(sanitize_text_field(wp_unslash($_POST['b2sSchedDate'])))) ? trim(sanitize_text_field(wp_unslash($_POST['b2sSchedDate']))) : '') : '';
            $b2sUserLang = isset($_POST['b2sUserLang']) ? trim(sanitize_text_field(wp_unslash($_POST['b2sUserLang']))) : strtolower(substr(B2S_LANGUAGE, 0, 2));
            $b2sResultsPerPage = (isset($_POST['b2sPostsPerPage']) && (int) $_POST['b2sPostsPerPage'] > 0) ? (int) $_POST['b2sPostsPerPage'] : B2S_PLUGIN_POSTPERPAGE;
            $b2sSortPostSharedBy = (isset($_POST['b2sSortPostSharedBy']) && (int) $_POST['b2sSortPostSharedBy'] > 0) ? (int) $_POST['b2sSortPostSharedBy'] : 0;
            $b2sSortSharedToNetwork = (isset($_POST['b2sSortSharedToNetwork']) && (int) $_POST['b2sSortSharedToNetwork'] > 0) ? (int) $_POST['b2sSortSharedToNetwork'] : 0;
            $b2sSortSharedAtDateStart = (isset($_POST['b2sSortSharedAtDateStart']) && (int) $_POST['b2sSortSharedAtDateStart'] > 0) ? (int) $_POST['b2sSortSharedAtDateStart'] : 0;
            $b2sSortSharedAtDateEnd = (isset($_POST['b2sSortSharedAtDateEnd']) && (int) $_POST['b2sSortSharedAtDateEnd'] > 0) ? (int) $_POST['b2sSortSharedAtDateEnd'] : 0;

            require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
            $options = new B2S_Options((int) B2S_PLUGIN_BLOG_USER_ID);
            $optionPostFilters = $options->_getOption('post_filters');
            $optionPostFilters['searchPostTitle'] = $b2sSortPostTitle;
            $optionPostFilters['searchAuthorId'] = $b2sSortPostAuthor;
            $optionPostFilters['searchPostStatus'] = $b2sSortPostStatus;
            $optionPostFilters['searchPostShareStatus'] = $b2sSortPostShareStatus;
            $optionPostFilters['searchSchedDate'] = $b2sSortPostSchedDate;
            $optionPostFilters['searchPostCat'] = $b2sSortPostCat;
            $optionPostFilters['searchPostType'] = $b2sSortPostType;
            $optionPostFilters['postsPerPage'] = $b2sResultsPerPage;
            $optionPostFilters['searchPostSharedById'] = $b2sSortPostSharedBy;
            $optionPostFilters['searchSharedToNetwork'] = $b2sSortSharedToNetwork;
            $optionPostFilters['searchSharedAtDateStart'] = $b2sSortSharedAtDateStart;
            $optionPostFilters['searchSharedAtDateEnd'] = $b2sSortSharedAtDateEnd;
            $options->_setOption('post_filters', $optionPostFilters);

            if (!empty($b2sType) && in_array($b2sType, array('all', 'sched', 'publish', 'notice', 'approve', 'draft', 'draft-post', 'favorites', 'video'))) {
                $postItem = new B2S_Post_Item($b2sType, $b2sSortPostTitle, $b2sSortPostAuthor, $b2sSortPostStatus, $b2sSortPostShareStatus, $b2sSortPostPublishDate, $b2sSortPostSchedDate, $b2sShowByDate, $b2sShowByNetwork, $b2sUserAuthId, $b2sPostBlogId, $b2sPagination, $b2sSortPostCat, $b2sSortPostType, $b2sUserLang, $b2sResultsPerPage, $b2sSortPostSharedBy, $b2sSortSharedToNetwork, $b2sSortSharedAtDateStart, $b2sSortSharedAtDateEnd);
                $result = array('result' => true, 'content' => $postItem->getItemHtml($b2sSelectSchedDate), 'schedDates' => json_encode($postItem->getCalendarSchedDate()));
                if ($b2sShowPagination) {
                    $result['pagination'] = $postItem->getPaginationHtml();
                }
                echo json_encode($result);
                wp_die();
            }
            echo json_encode(array('result' => false, 'content' => '', 'schedDates' => 0, 'pagination' => ''));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getNetworkBoardAndGroup() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['networkId']) && !empty($_POST['networkId']) && isset($_POST['networkAuthId']) && isset($_POST['networkType']) && !empty($_POST['networkAuthId'])) {
                $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getNetworkBoardAndGroup', 'token' => B2S_PLUGIN_TOKEN, 'networkType' => (int) $_POST['networkType'], 'networkAuthId' => (int) $_POST['networkAuthId'], 'networkId' => (int) $_POST['networkId'], 'lang' => substr(B2S_LANGUAGE, 0, 2))));
                if (is_object($result) && !empty($result) && isset($result->data) && !empty($result->data) && isset($result->result) && (int) $result->result == 1) {
                    require_once B2S_PLUGIN_DIR . 'includes/Form.php';
                    echo json_encode(array('result' => true, 'content' => B2S_Form::getNetworkBoardAndGroupHtml($result->data, (int) $_POST['networkId'])));
                    wp_die();
                }
            }
            echo json_encode(array('result' => false, 'content' => ''));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getFaqEntries() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getFaqEntries', 'lang' => substr(B2S_LANGUAGE, 0, 2), 'token' => B2S_PLUGIN_TOKEN)));
            if (isset($result->result) && isset($result->content) && !empty($result->content)) {
                echo json_encode(array('result' => true, 'content' => B2S_Notice::getFaqEntriesHtml($result->content)));
                wp_die();
            }
            echo json_encode(array('result' => false, 'content' => ''));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getShipItemFullText() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['postId']) && (int) $_POST['postId'] > 0 && isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0) {
                $userLang = isset($_POST['userLang']) ? trim(sanitize_text_field($_POST['userLang'])) : strtolower(substr(B2S_LANGUAGE, 0, 2));
                $data = get_post((int) $_POST['postId']);
                if (isset($data->post_content)) {
                    $postUrl = (get_permalink($data->ID) !== false) ? get_permalink($data->ID) : $data->guid;
                    $content = trim(B2S_Util::prepareContent($data->ID, $data->post_content, $postUrl, '', false, $userLang));
                    $networkId = isset($_POST['networkId']) ? (int) $_POST['networkId'] : 0;
                    echo json_encode(array('result' => true, 'text' => trim(sanitize_textarea_field($content)), 'networkAuthId' => (int) $_POST['networkAuthId'], 'networkId' => $networkId));
                    wp_die();
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getShipItem() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['postId']) && (int) $_POST['postId'] > 0 && isset($_POST['networkId']) && (int) (int) $_POST['networkId'] > 0 && isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0) {
                //TOS XING Group
                if ((int) $_POST['networkId'] == 19 && isset($_POST['networkTosGroupId']) && !empty($_POST['networkTosGroupId'])) {
                    $options = new B2S_Options(0, 'B2S_PLUGIN_TOS_XING_GROUP_CROSSPOSTING');
                    if ($options->existsValueByKey((int) $_POST['postId'], sanitize_text_field($_POST['networkTosGroupId']))) {
                        echo json_encode(array('result' => false, 'reason' => 'tos_xing_group_exists', 'networkAuthId' => (int) $_POST['networkAuthId']));
                        wp_die();
                    }
                }

                //Check IsValidVideoForNetwork
                $isVideoMode = false;
                if (isset($_POST['isVideo']) && (int) $_POST['isVideo'] == 1) {
                    require_once B2S_PLUGIN_DIR . 'includes/B2S/Video/Validation.php';
                    $validVideo = new B2S_Video_Validation();
                    $isValid = $validVideo->isValidVideoForNetwork((int) $_POST['postId'], (int) $_POST['networkId'], (int) $_POST['networkType']);
                    if (is_array($isValid) && isset($isValid['result']) && $isValid['result'] !== false) {
                        $isVideoMode = true;
                    } else {
                        echo json_encode(array('result' => false, 'reason' => 'invalid_video', 'content' => (isset($isValid['content']) ? $isValid['content'] : ''), 'networkId' => (int) $_POST['networkId'], 'networkAuthId' => (int) $_POST['networkAuthId']));
                        wp_die();
                    }

                    // NOTE check if it's a reel
                    if(isset($isValid['canReel']['result']) && !empty($isValid['canReel']['result']) && $isValid['canReel']['result'] === true) {
                        $canReel = array('result' => true);
                    } else {
                        $canReel = array('result' => false, 'content' => $isValid['canReel']['content']);
                    }
                }

                $userLang = isset($_POST['userLang']) ? trim(sanitize_text_field($_POST['userLang'])) : strtolower(substr(B2S_LANGUAGE, 0, 2));
                $relayCount = isset($_POST['relayCount']) ? (int) $_POST['relayCount'] : 0;
                require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Item.php';
                $itemData = array('networkAuthId' => (int) $_POST['networkAuthId'],
                    'networkId' => (int) $_POST['networkId'],
                    'networkKind' => (int) $_POST['networkKind'],
                    'networkTosGroupId' => ((isset($_POST['networkTosGroupId']) && !empty($_POST['networkTosGroupId'])) ? trim(sanitize_text_field($_POST['networkTosGroupId'])) : ''),
                    'instantSharing' => (isset($_POST['instantSharing']) ? (int) $_POST['instantSharing'] : 0),
                    'network_display_name' => sanitize_text_field($_POST['networkDisplayName']),
                    'networkType' => (int) $_POST['networkType']);
                $selSchedDate = (isset($_POST['selSchedDate']) && !empty($_POST['selSchedDate'])) ? (preg_match("#^[0-9\-.\]:\s]+$#", trim(sanitize_text_field($_POST['selSchedDate']))) ? trim(sanitize_text_field($_POST['selSchedDate'])) : "") : "";   //routing from calendar
                $b2sPostType = (isset($_POST['b2sPostType']) && $_POST['b2sPostType'] == 'ex') ? 'ex' : "";    //Content Curation

                $b2sDraftData = array();
                if (isset($_POST['b2sIsDraft']) && (int) $_POST['b2sIsDraft'] == 1) {
                    global $wpdb;
                    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts_drafts'") == $wpdb->prefix . 'b2s_posts_drafts') {
                        $sql = $wpdb->prepare("SELECT data FROM `{$wpdb->prefix}b2s_posts_drafts` WHERE `blog_user_id` = %d AND `post_id` = %d AND `save_origin` = %d", (int) B2S_PLUGIN_BLOG_USER_ID, (int) $_POST['postId'], 0);
                        $sqlResult = $wpdb->get_row($sql);
                        $drafts = (isset($sqlResult->data) && !empty($sqlResult->data)) ? unserialize($sqlResult->data) : false;
                        if ($drafts !== false && isset($drafts['b2s']) && !empty($drafts['b2s']) && array_key_exists(sanitize_text_field($_POST['networkAuthId']), $drafts['b2s'])) {
                            $b2sDraftData = $drafts['b2s'][sanitize_text_field($_POST['networkAuthId'])];
                            if (!empty($b2sDraftData) && is_array($b2sDraftData)) {
                                foreach ($b2sDraftData as $key => $value) {
                                    if (!is_array($value)) {
                                        $b2sDraftData[$key] = stripslashes($value);
                                    }
                                }
                            }
                        }
                    }
                }

                $item = new B2S_Ship_Item((int) $_POST['postId'], $userLang, $selSchedDate, $b2sPostType, $relayCount, $isVideoMode, $canReel);
                echo json_encode(array('result' => true, 'networkAuthId' => (int) $_POST['networkAuthId'], 'networkType' => (int) $_POST['networkType'], 'networkId' => (int) $_POST['networkId'], 'content' => $item->getItemHtml((object) $itemData, true, $b2sDraftData), 'draft' => !empty($b2sDraftData), 'draftActions' => $b2sDraftData));
            } else {
                echo json_encode(array('result' => false));
            }
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getPublishPostData() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['postId']) && (int) $_POST['postId'] > 0) {
                require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
                require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
                $postData = new B2S_Post_Item();
                $showByDate = isset($_POST['showByDate']) ? (preg_match("#^[0-9\-.\]]+$#", trim(sanitize_text_field($_POST['showByDate']))) ? trim(sanitize_text_field($_POST['showByDate'])) : "") : "";
                $type = (isset($_POST['type']) && in_array($_POST['type'], array('publish', 'notice', 'metrics'))) ? sanitize_text_field($_POST['type']) : 'publish';
                $sharedByUser = (isset($_POST['sharedByUser']) && (int) $_POST['sharedByUser'] > 0) ? (int) $_POST['sharedByUser'] : 0;
                $sharedOnNetwork = (isset($_POST['sharedOnNetwork']) && (int) $_POST['sharedOnNetwork'] > 0) ? (int) $_POST['sharedOnNetwork'] : 0;
                $result = $postData->getPublishPostDataHtml((int) $_POST['postId'], $type, $showByDate, $sharedByUser, $sharedOnNetwork);
                if ($result !== false) {
                    echo json_encode(array('result' => true, 'postId' => (int) $_POST['postId'], 'content' => $result));
                    wp_die();
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getVideoUploadData() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['attachment_id']) && (int) $_POST['attachment_id'] > 0) {
                require_once (B2S_PLUGIN_DIR . 'includes/B2S/Video/Item.php');
                $video = new B2S_Video_Item();
                $result = $video->getVideoUploadDataHtml((int) $_POST['attachment_id']);
                if ($result !== false) {
                    echo json_encode(array('result' => true, 'attachment_id' => (int) $_POST['attachment_id'], 'content' => $result));
                    wp_die();
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getApprovePostData() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['postId']) && (int) $_POST['postId'] > 0) {
                require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
                require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
                $postData = new B2S_Post_Item();
                $showByDate = isset($_POST['showByDate']) ? (preg_match("#^[0-9\-.\]]+$#", trim(sanitize_text_field(wp_unslash($_POST['showByDate'])))) ? trim(sanitize_text_field(wp_unslash($_POST['showByDate']))) : "") : "";
                $result = $postData->getApprovePostDataHtml((int) $_POST['postId'], $showByDate);
                if ($result !== false) {
                    echo json_encode(array('result' => true, 'postId' => (int) $_POST['postId'], 'content' => $result));
                    wp_die();
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getSchedPostsByUserAuth() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0) {
                require_once (B2S_PLUGIN_DIR . 'includes/B2S/Network/Item.php');
                $networkData = new B2S_Network_Item(false);
                global $wpdb;
                $blogUserTokenResult = $wpdb->get_results("SELECT token FROM `{$wpdb->prefix}b2s_user`");
                $blogUserToken = array();
                foreach ($blogUserTokenResult as $k => $row) {
                    array_push($blogUserToken, $row->token);
                }
                $data = array('action' => 'getTeamAssignUserAuth', 'token' => B2S_PLUGIN_TOKEN, 'networkAuthId' => (int) $_POST['networkAuthId'], 'blogUser' => $blogUserToken);
                $networkAuthAssignment = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $data, 30), true);
                $count = $networkData->getCountSchedPostsByUserAuth((int) $_POST['networkAuthId']);
                if (isset($networkAuthAssignment['result']) && $networkAuthAssignment['result'] !== false && isset($networkAuthAssignment['assignList']) && is_array($networkAuthAssignment['assignList'])) {
                    $assignCount = 0;
                    $assignList = array();
                    foreach ($networkAuthAssignment['assignList'] as $k => $v) {
                        $assignList[$v['assign_blog_user_id']] = (int) $v['assign_network_auth_id'];
                        $authCount = $networkData->getCountSchedPostsByUserAuth((int) $v['assign_network_auth_id']);
                        if ($authCount !== false) {
                            $assignCount += $authCount;
                        }
                    }
                    echo json_encode(array('result' => true, 'count' => ($count !== false) ? $count : 0, 'assignCount' => $assignCount, 'assignListCount' => count($networkAuthAssignment['assignList']), 'assignList' => json_encode($assignList)));
                    wp_die();
                } else {
                    if ($count !== false) {
                        echo json_encode(array('result' => true, 'count' => $count));
                        wp_die();
                    }
                }
            }
            echo json_encode(array('result' => false, 'count' => 0));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getSchedPostData() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['postId']) && (int) $_POST['postId'] > 0) {
                require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
                require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
                $postData = new B2S_Post_Item(((isset($_POST['type']) && sanitize_text_field(wp_unslash($_POST['type'])) == 'repost') ? 'repost' : 'all'));
                $showByDate = isset($_POST['showByDate']) ? (preg_match("#^[0-9\-.\]]+$#", trim(sanitize_text_field(wp_unslash($_POST['showByDate'])))) ? trim(sanitize_text_field(wp_unslash($_POST['showByDate']))) : "") : "";
                $showByNetwork = (isset($_POST['showByNetwork']) && (int) $_POST['showByNetwork'] > 0) ? (int) $_POST['showByNetwork'] : 0;
                $userAuthId = (isset($_POST['userAuthId']) && (int) $_POST['userAuthId'] > 0) ? (int) $_POST['userAuthId'] : 0;
                $result = $postData->getSchedPostDataHtml((int) $_POST['postId'], $showByDate, (int) $showByNetwork, (int) $userAuthId);
                if ($result !== false) {
                    echo json_encode(array('result' => true, 'postId' => (int) $_POST['postId'], 'content' => $result));
                    wp_die();
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getNavbarItem() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['networkId']) && isset($_POST['networkAuthId']) && isset($_POST['networkType']) && isset($_POST['displayName']) && isset($_POST['mandandId']) && isset($_POST['displayName']) && !empty($_POST['displayName'])) {
                require_once (B2S_PLUGIN_DIR . '/includes/B2S/Ship/Navbar.php');
                global $wpdb;
                $networkDetailsIdSelect = $wpdb->get_col($wpdb->prepare("SELECT postNetworkDetails.id FROM {$wpdb->prefix}b2s_posts_network_details AS postNetworkDetails WHERE postNetworkDetails.network_auth_id = %s", sanitize_text_field(wp_unslash($_POST['networkAuthId']))));
                if (!isset($networkDetailsIdSelect[0])) {
                    $wpdb->insert($wpdb->prefix . 'b2s_posts_network_details', array(
                        'network_id' => (int) $_POST['networkId'],
                        'network_type' => (int) $_POST['networkType'],
                        'network_auth_id' => (int) $_POST['networkAuthId'],
                        'network_display_name' => sanitize_text_field(wp_unslash($_POST['displayName']))), array('%d', '%d', '%d', '%s'));
                }
                $mandantCount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(mandant_id)FROM {$wpdb->prefix}b2s_user_network_settings  WHERE mandant_id =%d AND blog_user_id=%d ", (int) $_POST['mandandId'], B2S_PLUGIN_BLOG_USER_ID));
                if ($mandantCount > 0) {
                    $wpdb->insert($wpdb->prefix . 'b2s_user_network_settings', array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID, 'mandant_id' => (int) $_POST['mandandId'], 'network_auth_id' => (int) $_POST['networkAuthId']), array('%d', '%d', '%d'));
                }
                $data = array(
                    'networkId' => (isset($_POST['networkId']) && (int) $_POST['networkId'] > 0) ? (int) $_POST['networkId'] : 0,
                    'networkAuthId' => (isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0) ? (int) $_POST['networkAuthId'] : 0,
                    'networkType' => (isset($_POST['networkType']) && (int) $_POST['networkType'] > 0) ? (int) $_POST['networkType'] : 0,
                    'networkUserName' => isset($_POST['displayName']) ? sanitize_text_field($_POST['displayName']) : '',
                    'mandantId' => (isset($_POST['mandandId']) && (int) $_POST['mandandId'] > 0) ? (int) $_POST['mandandId'] : 0,
                    'instant_sharing' => (isset($_POST['instant_sharing']) && (int) $_POST['instant_sharing'] == 1) ? (int) $_POST['instant_sharing'] : 0,
                    'networkKind' => (isset($_POST['networkKind']) && (int) $_POST['networkKind'] > 0) ? (int) $_POST['networkKind'] : 0,
                    'networkTosGroupId' => (isset($_POST['networkTosGroupId']) && !empty($_POST['networkTosGroupId'])) ? trim(sanitize_text_field($_POST['networkTosGroupId'])) : '',
                    'expiredDate' => date('Y-m-d', strtotime('+3 days')));

                $navbar = new B2S_Ship_Navbar();
                $isVideo = (isset($_POST['isVideo']) && (int) $_POST['isVideo'] == 1) ? true : false;
                echo json_encode(array('result' => true, 'networkAuthId' => (int) $_POST['networkAuthId'], 'content' => $navbar->getItemHtml((object) $data, array(), $isVideo)));
                wp_die();
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getSettingsSchedTimeDefault() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            $userTimes = B2S_Tools::getRandomBestTimeSettings();
            if (!empty($userTimes) && is_array($userTimes)) {
                echo json_encode(array('result' => true, 'times' => $userTimes));
                wp_die();
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    //NEW V5.1.0
    public function getUserTimeSettings() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            $lang = substr(B2S_LANGUAGE, 0, 2);
            $options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
            $userSchedData = $options->_getOption('auth_sched_time');
            if (isset($userSchedData['time'])) {
                if (is_array($userSchedData) && isset($userSchedData['delay_day']) && isset($userSchedData['time']) && is_array($userSchedData['time'])) {
                    foreach ($userSchedData['time'] as $k => $v) {
                        $slug = ($lang == 'en') ? 'h:i A' : 'H:i';
                        $userSchedData['time'][$k] = date($slug, strtotime(date('Y-m-d ' . $v . ':00')));
                    }
                    echo json_encode(array('result' => true, 'type' => 'new', 'delay_day' => $userSchedData['delay_day'], 'times' => $userSchedData['time']));
                    wp_die();
                }
                //load old setttings  >5.1.0
            } else {
                global $wpdb;
                //if exists
                if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_post_sched_settings'") == $wpdb->prefix . 'b2s_post_sched_settings') {
                    $userTimes = array();
                    $saveSchedData = $wpdb->get_results($wpdb->prepare("SELECT network_id, network_type, sched_time FROM {$wpdb->prefix}b2s_post_sched_settings WHERE blog_user_id= %d", B2S_PLUGIN_BLOG_USER_ID));
                    if (!empty($saveSchedData) && is_array($saveSchedData)) {
                        foreach ($saveSchedData as $k => $v) {
                            $slug = ($lang == 'en') ? 'h:i A' : 'H:i';
                            $userTimes[$v->network_id][$v->network_type] = date($slug, strtotime(date('Y-m-d ' . $v->sched_time . ':00')));
                        }
                        if (!empty($userTimes)) {
                            echo json_encode(array('result' => true, 'type' => 'old', 'times' => $userTimes));
                            wp_die();
                        }
                    }
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getShipItemReloadUrl() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_POST['networkId']) && (int) $_POST['networkId'] > 0 && isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0 && isset($_POST['url']) && !empty($_POST['url'])) {
                if (isset($_POST['postId']) && (int) $_POST['postId'] > 0 && isset($_POST['defaultUrl']) && esc_url_raw(wp_unslash($_POST['defaultUrl'])) == esc_url_raw(wp_unslash($_POST['url'])) && (!isset($_POST['postType']) || sanitize_text_field(wp_unslash($_POST['postType'])) != 'ex')) {
                    $postData = get_post((int) $_POST['postId']);
                    if ($postData->post_status != 'publish') {
                        $postUrl = (get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid);
                        $metaInfo = array('title' => B2S_Util::getExcerpt(B2S_Util::remove4byte($postData->post_title), 50) . ' - ' . get_option('blogname'), 'description' => B2S_Util::getExcerpt(B2S_Util::prepareContent($postData->ID, $postData->post_content, $postUrl, false, false), 150));
                    } else {
                        $metaInfo = B2S_Util::getMetaTags((int) sanitize_text_field(wp_unslash($_POST['postId'])), esc_url_raw(wp_unslash($_POST['url'])), (int) sanitize_text_field(wp_unslash($_POST['networkId'])));
                    }
                } else {
                    $metaInfo = B2S_Util::getMetaTags(0, esc_url_raw(wp_unslash($_POST['url'])), (int) sanitize_text_field(wp_unslash($_POST['networkId'])));
                }
                echo json_encode(array('result' => true, 'networkId' => (int) $_POST['networkId'], 'networkAuthId' => (int) $_POST['networkAuthId'], 'title' => isset($metaInfo['title']) ? (function_exists('htmlspecialchars_decode') ? htmlspecialchars_decode($metaInfo['title']) : $metaInfo['title']) : '', 'description' => isset($metaInfo['description']) ? (function_exists('htmlspecialchars_decode') ? htmlspecialchars_decode($metaInfo['description']) : $metaInfo['description']) : '', 'image' => isset($metaInfo['image']) ? $metaInfo['image'] : ''));
                wp_die();
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getCalendarEvents() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Filter.php');
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php');
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php');
            require_once (B2S_PLUGIN_DIR . 'includes/Util.php');

            //Filter Network
            $network_id = (isset($_GET['filter_network']) && (int) $_GET['filter_network'] >= 1) ? (int) $_GET['filter_network'] : 0; // 0=all
            //Filter Network Details
            $network_details_id = (isset($_GET['filter_network_auth']) && (int) $_GET['filter_network_auth'] >= 1) ? (int) $_GET['filter_network_auth'] : 0; // 0=all
            //Filter Status
            $status = (isset($_GET['filter_status']) && (int) $_GET['filter_status'] >= 0) ? (int) $_GET['filter_status'] : 0; // 0=all,1=publish, 2=scheduled

            if (isset($_GET['start']) && isset($_GET['end']) && preg_match("#^[0-9\-.\]]+$#", sanitize_text_field(wp_unslash($_GET['start']))) && preg_match("#^[0-9\-.\]]+$#", sanitize_text_field(wp_unslash($_GET['end'])))) {
                $calendar = B2S_Calendar_Filter::getByTimespam(sanitize_text_field(wp_unslash($_GET['start'])) . " 00:00:00", sanitize_text_field(wp_unslash($_GET['end'])) . " 23:59:59", $network_id, $network_details_id, $status);
            } else {
                $calendar = B2S_Calendar_Filter::getAll($network_id, $network_details_id);
            }
            echo json_encode($calendar->asCalendarArray());
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getCalendarFilterNetworkAuth() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Filter.php');
            $network_id = (isset($_POST['network_id']) && (int) $_POST['network_id'] >= 1) ? (int) $_POST['network_id'] : 0; // 0=all
            if ($network_id != 0) {
                $result = B2S_Calendar_Filter::getFilterNetworkAuthHtml($network_id);
                if ($result !== false) {
                    echo json_encode(array('result' => true, 'content' => $result));
                    wp_die();
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getPostEditModal() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Filter.php');
            if (isset($_POST['id']) && (int) $_POST['id'] > 0) {
                $item = B2S_Calendar_Filter::getById((int) $_POST['id']);
                if ($item != null) {
                    $lock_user_id = get_option("B2S_PLUGIN_CALENDAR_BLOCKED_" . (int) $_POST['id']);
                    if (!$lock_user_id) {
                        update_option("B2S_PLUGIN_CALENDAR_BLOCKED_" . (int) $_POST['id'], B2S_PLUGIN_BLOG_USER_ID, false);
                        $options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
                        $block_old = $options->_getOption("B2S_PLUGIN_USER_CALENDAR_BLOCKED");

                        if ($block_old) {
                            delete_option("B2S_PLUGIN_CALENDAR_BLOCKED_" . $block_old);
                        }
                        $options->_setOption("B2S_PLUGIN_USER_CALENDAR_BLOCKED", (int) $_POST['id']);
                    }
                    if ($lock_user_id) {
                        $lock_user = get_userdata($lock_user_id);
                    }
                    include (B2S_PLUGIN_DIR . 'views/b2s/partials/post-edit-modal.php');
                    wp_die();
                }
            }
            echo "0";
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getImageModal() {
        if (current_user_can('read') && isset($_POST['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php');
            if (isset($_POST['id']) && (int) $_POST['id'] > 0) {
                $postData = get_post((int) $_POST['id']);
                if (isset($postData->ID)) {
                    $postUrl = (get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid);
                    include (B2S_PLUGIN_DIR . 'views/b2s/partials/calendar-image-modal.php');
                    wp_die();
                }
            }
            echo "0";
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getMultiWidgetContent() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            $option = get_option("B2S_MULTI_WIDGET");
            if ($option !== false) {
                if (is_array($option) && isset($option['timestamp']) && isset($option['content']) && !empty($option['content']) && $option['timestamp'] > date('Y-m-d H:i:s', strtotime("-1 hours"))) {
                    die($option['content']);
                }
            }
            $content = B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getNews', 'version' => B2S_PLUGIN_VERSION, 'lang' => strtolower(substr(get_locale(), 0, 2)), 'token' => B2S_PLUGIN_TOKEN));
            update_option("B2S_MULTI_WIDGET", array("timestamp" => date("Y-m-d H:i:s"), "content" => $content), false);
            echo B2S_Tools::esc_html_array($content, array(
                'div' => array(
                    'class' => array(),
                    'style' => array()
                ),
                'img' => array(
                    'src' => array(),
                    'alt' => array(),
                    'style' => array()
                ),
                'p' => array(
                    'style' => array()
                ),
                'a' => array(
                    'href' => array(),
                    'target' => array(),
                    'class' => array(),
                    'title' => array()
                )
            ));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getStats() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Stats.php');
            $stats = new B2S_Stats();
            if (isset($_GET['from']) && !empty($_GET['from']) && preg_match("#^[0-9\-.\]]+$#", sanitize_text_field(wp_unslash($_GET['from'])))) {
                $stats->set_from(sanitize_text_field(wp_unslash($_GET['from'])));
            }
            echo json_encode($stats->get_result());
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function b2sSupportSystemRequirements() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (!current_user_can('administrator')) {
                echo json_encode(array('result' => false, 'error' => 'admin'));
                wp_die();
            }
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Support/Check/System.php');
            $heartbeat_status = (isset($_GET['heartbeat_status']) && sanitize_text_field(wp_unslash($_GET['heartbeat_status'])) == 'false') ? false : true;
            $support = new B2S_Support_Check_System($heartbeat_status);
            $htmlData = $support->htmlData();
            $blogData = $support->blogData();
            if (empty($htmlData) || empty($blogData)) {
                $result = array('result' => false);
            } else {
                $result = array('result' => true, 'htmlData' => $htmlData, "blogData" => $blogData);
            }
            echo json_encode($result);
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function searchUser() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_GET['search_user']) && !empty($_GET['search_user'])) {
                $options = B2S_Tools::searchUser(sanitize_text_field($_GET['search_user']));
                echo json_encode(array('result' => true, 'options' => $options));
                wp_die();
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getSelectMandantUser() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_GET['owner']) && (int) $_GET['owner'] > 0) {
                $owner = stripslashes(get_user_by('id', (int) $_GET['owner'])->display_name);
                $owner = (empty($owner) || $owner == false) ? esc_html("Unknown username", "blog2social") : esc_html($owner);
                echo json_encode(array('result' => true, 'ownerName' => $owner));
                wp_die();
            } else {
                $networkAuthId = (isset($_GET['networkAuthId']) && (int) $_GET['networkAuthId'] > 0) ? (int) $_GET['networkAuthId'] : 0;
                $networkId = (isset($_GET['networkId']) && (int) $_GET['networkId'] > 0) ? (int) $_GET['networkId'] : 0;
                $networkType = (isset($_GET['networkType']) && (int) $_GET['networkType'] >= 0) ? (int) $_GET['networkType'] : 0;
                if ($networkAuthId > 0 && $networkId > 0) {
                    require_once (B2S_PLUGIN_DIR . 'includes/B2S/Network/Item.php');
                    $networkItem = new B2S_Network_Item();
                    $networkAuthAssignment = $networkItem->getNetworkAuthAssignment($networkAuthId, $networkId, $networkType);
                    if (isset($networkAuthAssignment['result']) && $networkAuthAssignment['result'] !== false) {
                        if (isset($networkAuthAssignment['assignList']) && isset($networkAuthAssignment['userSelect'])) {
                            echo json_encode(array('result' => true, 'userSelect' => $networkAuthAssignment['userSelect'], 'assignList' => $networkAuthAssignment['assignList']));
                            wp_die();
                        }
                    }
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getEditTemplateForm() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_GET['networkId']) && (int) $_GET['networkId'] > 0) {
                require_once (B2S_PLUGIN_DIR . 'includes/B2S/Network/Item.php');
                $networkItem = new B2S_Network_Item(false);

                $content = $networkItem->getEditTemplateForm((int) $_GET['networkId']);

                echo json_encode(array('result' => true, 'content' => $content));
                wp_die();
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function checkDraftExists() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_GET['postId']) && (int) $_GET['postId'] > 0) {
                global $wpdb;
                if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts_drafts'") == $wpdb->prefix . 'b2s_posts_drafts') {
                    $sqlCheckDraft = $wpdb->prepare("SELECT `id` FROM `{$wpdb->prefix}b2s_posts_drafts` WHERE `blog_user_id` = %d AND `post_id` = %d AND `save_origin` = %d", B2S_PLUGIN_BLOG_USER_ID, (int) $_GET['postId'], 0);
                    $draftEntry = $wpdb->get_var($sqlCheckDraft);
                    if ($draftEntry !== NULL && (int) $draftEntry > 0) {
                        echo json_encode(array('result' => true));
                        wp_die();
                    }
                }
            }
            echo json_encode(array('result' => false));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getCurationShipDetails() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Curation/View.php');
            $curation = new B2S_Curation_View();
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getProfileUserAuth', 'token' => B2S_PLUGIN_TOKEN)));
            if (isset($result->result) && (int) $result->result == 1 && isset($result->data) && !empty($result->data) && isset($result->data->mandant) && isset($result->data->auth) && !empty($result->data->mandant) && !empty($result->data->auth)) {
                /*
                 * since V7.0 Remove Video Networks
                 */
                $isVideoNetwork = unserialize(B2S_PLUGIN_NETWORK_SUPPORT_VIDEO);
                foreach ($result->data->auth as $a => $auth) {
                    foreach ($auth as $u => $item) {
                        if (in_array($item->networkId, $isVideoNetwork)) {
                            if (!in_array($item->networkId, array(1, 2, 6, 12, 38, 39))) {
                                unset($result->data->auth->{$a[$u]});
                            }
                        }
                    }
                }
                echo json_encode(array('result' => true, 'settings' => $curation->getShippingDetails($result->data->mandant, $result->data->auth)));
                wp_die();
            }
            echo json_encode(array('result' => false, 'preview' => $preview, 'scrapeError' => $scrapeError, 'error' => 'NO_AUTH'));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getNetworkAuthSettings() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            $result = array();
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Network/Item.php');
            $networkItem = new B2S_Network_Item();
            if (isset($_GET['owner']) && (int) $_GET['owner'] > 0) {
                $owner = stripslashes(get_user_by('id', (int) $_GET['owner'])->display_name);
                $owner = (empty($owner) || $owner == false) ? esc_html("Unknown username", "blog2social") : esc_html($owner);
                $result['ownerName'] = $owner;
            } else {
                $networkAuthId = (isset($_GET['networkAuthId']) && (int) $_GET['networkAuthId'] > 0) ? (int) $_GET['networkAuthId'] : 0;
                $networkId = (isset($_GET['networkId']) && (int) $_GET['networkId'] > 0) ? (int) $_GET['networkId'] : 0;
                $networkType = (isset($_GET['networkType']) && (int) $_GET['networkType'] >= 0) ? (int) $_GET['networkType'] : 0;
                if ($networkAuthId > 0 && $networkId > 0) {
                    $networkAuthAssignment = $networkItem->getNetworkAuthAssignment($networkAuthId, $networkId, $networkType);
                    if (isset($networkAuthAssignment['result']) && $networkAuthAssignment['result'] !== false) {
                        if (isset($networkAuthAssignment['assignList']) && isset($networkAuthAssignment['userSelect'])) {
                            $result['userSelect'] = $networkAuthAssignment['userSelect'];
                            $result['assignList'] = $networkAuthAssignment['assignList'];
                        }
                    }
                }
            }

            if (isset($_GET['networkAuthId']) && (int) $_GET['networkAuthId'] > 0) {
                $result['urlParameter'] = $networkItem->getUrlParameterSettings((int) $_GET['networkAuthId'], (int) $_GET['networkId']);
            }

            echo json_encode(array('result' => true, 'data' => json_encode($result)));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function updatePostBox() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_GET['post_id']) && (int) $_GET['post_id'] > 0) {
                require_once (B2S_PLUGIN_DIR . 'includes/B2S/PostBox.php');
                $postBox = new B2S_PostBox();
                $updateInfo = $postBox->updateInfo((int) $_GET['post_id']);
                echo json_encode(array('result' => true, 'active' => $updateInfo['active'], 'lastPostDate' => $updateInfo['lastPostDate'], 'shareCount' => $updateInfo['shareCount']));
                wp_die();
            }
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function getImageCaption() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            if (isset($_GET['image_id']) && (int) $_GET['image_id'] > 0) {
                $image = get_post((int) $_GET['image_id']);
                $caption = (($image->post_content != false && $image->post_content != '') ? $image->post_content : '');
                echo json_encode(array('result' => true, 'caption' => $caption));
                wp_die();
            }
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

    public function loadInsights() {
        if (current_user_can('read') && isset($_GET['b2s_security_nonce']) && (int) wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['b2s_security_nonce'])), 'b2s_security_nonce') > 0) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Metrics/Item.php');
            $metrics = new B2S_Metrics_Item();
            $filterNetwork = ((isset($_GET['filter_network']) && sanitize_text_field(wp_unslash($_GET['filter_network'])) !== 'all' && (int) $_GET['filter_network'] > 0) ? (int) $_GET['filter_network'] : 0);
            $filterDates = ((isset($_GET['filter_dates']) && is_array($_GET['filter_dates']) && !empty($_GET['filter_dates'])) ? B2S_Tools::sanitize_array($_GET['filter_dates']) : array());
            $data = $metrics->getInsightsData($filterNetwork, $filterDates);
            echo json_encode(array('result' => true, 'data' => $data));
            wp_die();
        } else {
            echo json_encode(array('result' => false, 'error' => 'nonce'));
            wp_die();
        }
    }

}
