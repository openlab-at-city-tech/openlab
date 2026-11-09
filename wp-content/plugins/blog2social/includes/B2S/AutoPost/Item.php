<?php

/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
 */

class B2S_AutoPost_Item {

    private $options;
    private $postTypesData;
    private $postCategoriesData;
    private $postTagsData;
    private $postTaxonomiesData;
    private $networkAuthData = array();
    private $networkAutoPostData;
    private $networkMandantData = array();
    private $networkAuthCount = false;
    private $schedLimit = null;

    public function __construct() {
        $this->getSettings();
        $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
        $this->postTypesData = get_post_types(array('public' => true));
        $this->postCategoriesData = get_categories(array('public' => true));
        $this->postTagsData = get_tags(array('hide_empty' => false));
        $this->postTaxonomiesData = get_taxonomies(array('public' => true));
    }

    private function getSettings() {
        $currentDate = new DateTime("now", wp_timezone());
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getSettings', 'portal_view_mode' => true, 'current_date' => $currentDate->format('Y-m-d'), 'update_licence' => 1, 'portal_auth_count' => true, 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));

        if (is_object($result) && isset($result->result) && (int) $result->result == 1 && isset($result->portale) && is_array($result->portale)) {
            $this->networkAuthCount = isset($result->portal_auth_count) ? $result->portal_auth_count : false;
            $this->networkAuthData = isset($result->portal_auth) ? $result->portal_auth : array();
            $this->networkAutoPostData = isset($result->portal_auto_post) ? $result->portal_auto_post : array();
            $this->networkMandantData = isset($result->mandantData) ? $result->mandantData : array();

            if (isset($result->licence_condition)) {
                //update
                $versionDetails = get_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
                if ($versionDetails !== false && is_array($versionDetails) && !empty($versionDetails)) {
                    $versionDetails['B2S_PLUGIN_LICENCE_CONDITION'] = (array) $result->licence_condition;
                    if (isset($result->network_condition)) {
                        $versionDetails['B2S_PLUGIN_NETWORK_CONDITION'] = (array) $result->network_condition;
                    }
                    update_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID, $versionDetails, false);

                    if (isset($result->licence_condition->open_sched_post_quota) && B2S_PLUGIN_USER_VERSION > 0) {
                        if ((int) $result->licence_condition->open_sched_post_quota > 0) {
                            $this->schedLimit = (int) $result->licence_condition->open_sched_post_quota;
                        } else {
                            $this->schedLimit = 0;
                        }
                    }
                }
            }
        }
    }

    public function getAutoPostingSettingsHtml() {

        $optionAutoPost = $this->options->_getOption('auto_post');
        $optionAutoPostImport = $this->options->_getOption('auto_post_import');

        $isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? ' <span class="label label-success label-sm">' . esc_html__("SMART", "blog2social") . '</span>' : '';
        $versionType = unserialize(B2S_PLUGIN_VERSION_TYPE);
        $limit = unserialize(B2S_PLUGIN_AUTO_POST_LIMIT);
        $autoPostActive = (isset($optionAutoPost['active'])) ? (((int) $optionAutoPost['active'] > 0) ? true : false) : (((isset($optionAutoPost['publish']) && !empty($optionAutoPost['publish'])) || (isset($optionAutoPost['update']) && !empty($optionAutoPost['update']))) ? true : false);
        $autoPostImportActive = (isset($optionAutoPostImport['active']) && (int) $optionAutoPostImport['active'] == 1) ? true : false;

        //Check if any post types allowed for auto-posting, if not, disable auto-posting so user knows it has no effect
        $types= array('publish', 'update');
        $typesEmpty = true;
        foreach ($types as $type) {
            $selectedItems = (is_array($optionAutoPost) && isset($optionAutoPost[$type])) ? $optionAutoPost[$type] : array();
            $stateSelected = isset($optionAutoPost[$type . '_state'])  ? $optionAutoPost[$type . '_state'] : '0';
            if ($stateSelected == '0' && empty($selectedItems)) {
                $stateSelected = 'none';
            }
            
            if($stateSelected != 'none') {
                $typesEmpty = false;
            }
        }
        if($typesEmpty) {
            $autoPostActive = false;
        }

        $content = '';
        $content .= '<input type="hidden" class="b2s-autopost-m-show-modal" value="' . ((isset($optionAutoPost['active'])) ? '0' : '1') . '">';
        $content .= '<input type="hidden" class="b2s-autopost-a-show-modal" value="' . ((isset($optionAutoPostImport['active'])) ? '0' : '1') . '">';
        $content .= '<div class="panel panel-group b2s-auto-post-own-general-warning"><div class="panel-body">';
       
        $content .= '<span class="glyphicon glyphicon-exclamation-sign glyphicon-warning"></span> ' . sprintf(
             // translators: %s is a link
            __('Posts for Facebook Profiles will be shown on your "Site & Blog Content" navigation bar in the "Instant Sharing" tab. To share the post on your Facebook Profile just click on the "Share" button next to your post. More information in the <a href="%s" target="_blank">Instant Sharing guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('facebook_instant_sharing')));
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<h4 class="b2s-auto-post-header">' . esc_html__('Auto-publish posts created in the WP Editor (your own content)', 'blog2social') . '</h4><a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('auto_post_manuell')) . '">Info</a>';

        if (isset($optionAutoPost['assignBy']) && (int) $optionAutoPost['assignBy'] > 0) {
            $content .= '<div class="panel panel-group b2s-auto-post-own-general-warning"><div class="panel-body">';
            $content .= '<span class="glyphicon glyphicon-exclamation-sign glyphicon-warning"></span>' . esc_html__('The settings for the Auto Poster were configured for you by a WordPress admin.', 'blog2social') . '  ';
            $content .= '<a href="#" id="b2s-auto-post-assign-by-disconnect">' . esc_html__('Disconnect', 'blog2social') . '</a>';
            $content .= '</div>';
            $content .= '</div>';
        } else {
            $content .= '<form id = "b2s-user-network-settings-auto-post-own" method = "post">';
            $content .= '<div class="' . (!empty($isPremium) ? 'b2s-btn-disabled b2sPreFeatureAutoPosterModal' : '') . '">';
            $content .= '<label class="b2s-ap-switch"><input ' . (($autoPostActive && empty($isPremium)) ? 'checked' : '') . ' name="b2s-manuell-auto-post" class="b2s-auto-post-area-toggle" data-area-type="manuell" value="1" type="checkbox"><span class="b2s-ap-slider"></span></label>';
            $content .= '</div>';
            $content .= '<div class="b2s-auto-post-area" data-area-type="manuell"' . (($autoPostActive && empty($isPremium)) ? '' : ' style="display:none;"') . '>';
            $content .= '<div id="b2s-ap-configure-manuell" class="b2s-ap-configure-body">';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-8">';
            $content .= '<div class="b2s-rp-autopost-manuell-standard-filters">';
            $content .= '<div class="alert alert-danger b2s-auto-post-error" data-error-reason="no-post-type" style="display:none;">' . esc_html__('Please select a post type', 'blog2social') . '</div>';
            $content .= $this->getPostTypesHtml($optionAutoPost, 'publish');
            $content .= '<div class="b2s-auto-post-own-update-warning" style="display: none;"><div class="alert alert-warning"><span class="glyphicon glyphicon-exclamation-sign"></span> ' . esc_html__('By enabling this feature your previously published social media posts will be sent again to your selected social media channels as soon as the post is updated.', 'blog2social') . '</div></div>';
            $content .= $this->getPostTypesHtml($optionAutoPost, 'update');
            $content .= '</div>';
            $delayState = isset($optionAutoPost['delay_state']) ? (int) $optionAutoPost['delay_state'] : 0;
            $content .= '<a class="b2s-rp-new-collapse-trigger" data-toggle="collapse" aria-expanded="true">';
            $content .= '<span>' . esc_html__('Advanced Filter', 'blog2social') . '</span>';
            $content .= '<span class="glyphicon glyphicon-chevron-down b2s-rp-new-chevron"></span>';
            $content .= '</a>';
            $content .= '<div id="b2s-ap-advanced-filters" style="display:block;" class="b2s-auto-post-area collapse">';
            $content .= '<div class="b2s-rp-new-filter-list">';
            $content .= $this->getChosenPostCategoriesData($optionAutoPost);
            $content .= $this->getChosenPostTagsData($optionAutoPost);
            $content .= '<div class="b2s-rp-new-filter-row">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-clock b2s-rp-new-icon-muted"></span><span>' . esc_html__('Publish Time', 'blog2social') . '</span></div>';
            $content .= '<select name="b2s-auto-post-delay-state" class="form-control b2s-rp-new-filter-state-select b2s-ap-delay-toggle" data-target="b2s-ap-delay-input">';
            $content .= '<option value="0"' . ($delayState == 0 ? ' selected' : '') . '>' . esc_html__('immediately', 'blog2social') . '</option>';
            $content .= '<option value="1"' . ($delayState == 1 ? ' selected' : '') . '>' . esc_html__('publish with a delay', 'blog2social') . '</option>';
            $content .= '<option value="2"' . ($delayState == 2 ? ' selected' : '') . '>' . esc_html__('at my best time settings', 'blog2social') . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '<div class="b2s-rp-new-filter-input' . ($delayState == 1 ? '' : ' b2s-info-display-none') . '" id="b2s-ap-delay-input">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-clock b2s-rp-new-icon-muted"></span><span>' . esc_html__('Delay (0-10 minutes)', 'blog2social') . ' <a href="#" class="b2sAutoPostBestTimesInfoModalBtn">Info</a></span></div>';
            $content .= '<input type="number" maxlength="2" max="10" min="0" class="form-control b2s-rp-new-filter-state-select" name="b2s-auto-post-delay-time" value="' . esc_attr((isset($optionAutoPost['delay']) ? $optionAutoPost['delay'] : 0)) . '" placeholder="1">';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';

            $selected1 = '';
            $selected2 = '';
            $selected3 = '';
            if (isset($optionAutoPost['echo'])) {
                if ((int) $optionAutoPost['echo'] > 0) {
                    $echoChecked = 'checked';
                    $echoShow = '';
                    $selected1 = $optionAutoPost['echo'] == 1 ? 'selected="selected"' : '';
                    $selected2 = $optionAutoPost['echo'] == 2 ? 'selected="selected"' : '';
                    $selected3 = $optionAutoPost['echo'] == 3 ? 'selected="selected"' : '';
                } else {
                    $echoChecked = '';
                    $echoShow = 'style="display:none;"';
                }
            } else {
                $echoChecked = '';
                $echoShow = 'style="display:none;"';
            }

            $echoStateVal = ($echoChecked === 'checked') ? 1 : 0;
            $content .= '<div class="b2s-rp-new-filter-row">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-update b2s-rp-new-icon-muted"></span><span>' . esc_html__('Re-post', 'blog2social') . '</span><a href="#" class="b2sAutoPostEchoSettingInfoModalBtn">Info</a></div>';
            $content .= '<select name="b2s-auto-post-echo-setting" id="b2s-auto-post-echo-setting" class="form-control b2s-rp-new-filter-state-select b2s-ap-echo-toggle" data-target="b2s-ap-echo-input">';
            $content .= '<option value="0"' . ($echoStateVal == 0 ? ' selected' : '') . '>' . esc_html__('Off', 'blog2social') . '</option>';
            $content .= '<option value="1"' . ($echoStateVal == 1 ? ' selected' : '') . '>' . esc_html__('On', 'blog2social') . '</option>';
            $content .= '</select>';
           
            $content .= '</div>';
            $content .= '<div class="b2s-rp-new-filter-input' . ($echoStateVal == 0 ? ' b2s-info-display-none' : '') . '" id="b2s-ap-echo-input">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-update b2s-rp-new-icon-muted"></span><span>' . esc_html__('Re-post after', 'blog2social') . '</span></div>';
            $content .= '<select class="form-control b2s-rp-new-filter-state-select" id="b2s-auto-post-echo-dropdown" name="b2s-auto-post-echo-dropdown">';
            $content .= '<option value="1" ' . $selected1 . '>' . esc_html__("1 Day", "blog2social") . '</option>';
            $content .= '<option value="2" ' . $selected2 . '>' . esc_html__("2 Days", "blog2social") . '</option>';
            $content .= '<option value="3" ' . $selected3 . '>' . esc_html__("Both 1 and 2 days (2 Re-posts)", "blog2social") . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';

            if (current_user_can('administrator')) {
                global $wpdb;

                $blogUserTokenResult = $wpdb->get_results("SELECT token FROM `{$wpdb->prefix}b2s_user`");
                $blogUserToken = array();
                foreach ($blogUserTokenResult as $k => $row) {
                    array_push($blogUserToken, $row->token);
                }
                $data = array('action' => 'getTeamAssignUserAuth', 'token' => B2S_PLUGIN_TOKEN, 'networkAuthId' => 0, 'blogUser' => $blogUserToken);
                $networkAuthAssignment = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $data, 30), true);
                if (isset($networkAuthAssignment['userList']) && !empty($networkAuthAssignment['userList']) && count($networkAuthAssignment['userList']) > 1) {
                    $doneIds = array();
                    $content .= '<div class="b2s-rp-new-filter-row">';
                    $content .= '<div class="b2s-rp-new-filter-row-head">';
                    $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-admin-users b2s-rp-new-icon-muted"></span><span>' . esc_html__('Transfer Auto Poster settings to other users', 'blog2social') . ' <a class="b2sInfoAssignAutoPostBtn" href="#">' . esc_html__('Info', 'blog2social') . '</a></span></div>';
                    $content .= '</div>';
                    $content .= '<div class="b2s-rp-new-filter-input">';
                    $content .= '<select name="b2s-auto-post-assign-user-data[]" multiple="" data-placeholder="Select User" class="b2s-auto-post-assign-user form-control">';
                    foreach ($networkAuthAssignment['userList'] as $k => $listUser) {
                        if ((int) $listUser != B2S_PLUGIN_BLOG_USER_ID && !in_array($listUser, $doneIds)) {
                            array_push($doneIds, $listUser);
                            $userDetails = get_option('B2S_PLUGIN_USER_VERSION_' . $listUser);
                            if (isset($userDetails['B2S_PLUGIN_USER_VERSION']) && (int) $userDetails['B2S_PLUGIN_USER_VERSION'] == 3) {
                                $displayName = stripslashes(get_user_by('id', $listUser)->display_name);
                                if (!empty($displayName) && $displayName != false) {
                                    $selected = '';
                                    if (isset($optionAutoPost['assignUser']) && !empty($optionAutoPost['assignUser']) && in_array($listUser, $optionAutoPost['assignUser'])) {
                                        $selected = 'selected="selected"';
                                    }
                                    $content .= '<option value="' . esc_attr($listUser) . '" ' . $selected . '>' . esc_html($displayName) . '</option>';
                                }
                            }
                        }
                    }
                    $content .= '</select>';
                    $content .= '</div>';
                    $content .= '</div>';
                }
            }
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<div class="col-md-4">';
            $content .= '<div class="panel panel-default b2s-rp-new-card" style="margin-top: 15px;">';
            $content .= '<div class="panel-heading b2s-rp-new-card-header">';
            $content .= '<div class="b2s-rp-new-card-title">';
            $content .= '<span class="dashicons dashicons-networking b2s-rp-new-icon-primary"></span>';
            $content .= '<span class="b2s-rp-new-heading">' . esc_html__('Networks', 'blog2social') . '</span>';
            $content .= '</div>';
            $content .= '<p class="b2s-rp-new-card-desc">' . esc_html__('Choose the networks and profiles for autoposting', 'blog2social') . '</p>';
            $content .= '</div>';
            $content .= '<div class="panel-body">';
            $content .= '<div class="' . (!empty($isPremium) ? 'b2s-btn-disabled' : '') . '">';
            $content .= '<div class="alert alert-danger b2s-auto-post-error" data-error-reason="no-auth-in-mandant" tabindex="-1" style="display:none;">' . esc_html__('There are no social network accounts assigned to your selected network collection. Please assign at least one social network account or select another network collection.', 'blog2social') . '<a href="' . esc_url(((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-network') . '" target="_blank">' . esc_html__('Network settings', 'blog2social') . '</a></div>';
            $content .= $this->getMandantSelect((isset($optionAutoPost['profile']) ? $optionAutoPost['profile'] : 0), (isset($optionAutoPost['twitter']) ? $optionAutoPost['twitter'] : 0));
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
        }

        $showSchedLimitInfo = (B2S_PLUGIN_USER_VERSION > 0 && $this->schedLimit <= 0) ? "" : "b2s-info-display-none";

        $content .= '</div>';
        $content .= '<br>';
        $content .= '<hr>';
        $content .= '</div>';
        $content .= '<h4 class="b2s-auto-post-header b2s-auto-post-header-margin-top">' . esc_html__('Auto-publish posts imported from RSS feeds or other plugins', 'blog2social') . '</h4><a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('auto_post_import')) . '">Info</a>';
        $content .= '<div id="b2s-licence-condition" class="alert alert-danger ' . $showSchedLimitInfo . '"><span class="b2s-text-bold">' . esc_html__("You've reached your posting limit!", "blog2social") . '</span><br>' . esc_html__('To increase your limit and enjoy more features, consider upgrading.', 'blog2social') . '<br><a target="_blank" class="b2s-text-bold" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '">' . esc_html__('Upgrade', 'blog2social') . '</a></div>';
        $content .= '<p>' . esc_html__('Your current license:', 'blog2social') . '<span class="b2s-key-name"> ' . esc_html($versionType[B2S_PLUGIN_USER_VERSION]) . '</span> ';
        if (B2S_PLUGIN_USER_VERSION == 0) {
            $content .= '<br>' . esc_html__('Immediate Cross-Posting across all networks: Share an unlimited number of posts', 'blog2social') . '<br>';
            $content .= esc_html__('Scheduled Auto-Posting', 'blog2social') . ': <a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '" target="_blank">' . esc_html__('Upgrade', 'blog2social') . '</a>';
        } else {
            $content .= '(' . esc_html__('share up to', 'blog2social') . ' ' . esc_html($limit[B2S_PLUGIN_USER_VERSION]) . ((B2S_PLUGIN_USER_VERSION >= 2) ? ' ' . esc_html__('posts per day', 'blog2social') : '') . ') ';
            $content .= '<a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '" target="_blank">' . esc_html__('Upgrade', 'blog2social') . '</a>';
        }
        $content .= '</p>';
        $content .= '<div class="b2s-auto-post-area-margin-top ' . (!empty($isPremium) ? 'b2s-btn-disabled b2sPreFeatureAutoPosterModal' : '') . '">';
        $content .= '<label class="b2s-ap-switch"><input ' . (($autoPostImportActive) ? 'checked' : '') . ' name="b2s-import-auto-post" class="b2s-auto-post-area-toggle" data-area-type="import" value="1" type="checkbox"><span class="b2s-ap-slider"></span></label>';
        $content .= '</div>';
        $content .= '<div class="b2s-auto-post-area" data-area-type="import"' . (($autoPostImportActive) ? '' : ' style="display:none;"') . '>';
        $content .= '<div id="b2s-ap-configure-import" class="b2s-ap-configure-body">';
        $content .= '<br>';
        $content .= '<div class="alert alert-danger b2s-auto-post-error" data-error-reason="import-no-auth" style="display:none;">' . esc_html__('Please select a social media network', 'blog2social') . '</div>';
        $content .= '<p class="b2s-bold">' . esc_html__('Available networks to select your auto-post connecitons:', 'blog2social') . '</p>';
        $content .= '<div class="b2s-network-tos-auto-post-import-warning"><div class="alert alert-danger">' . esc_html__('In accordance with the new X TOS, one X account can be selected as your primary X account for auto-posting.', 'blog2social') . ' <a href="' . esc_url(B2S_Tools::getSupportLink('network_tos_faq_032018')) . '" target="_blank">' . esc_html__('More information', 'blog2social') . '</a></div></div>';
        $content .= $this->getNetworkAutoPostData($optionAutoPostImport);
        $importShipState = isset($optionAutoPostImport['ship_state']) ? (int) $optionAutoPostImport['ship_state'] : 0;
        $importTemplateVal = (isset($optionAutoPost['import_template']) && (int) $optionAutoPost['import_template'] == 1) ? 1 : 0;
        $importImageAiGenerated = isset($optionAutoPostImport['image_is_ai_generated']) ? (int) $optionAutoPostImport['image_is_ai_generated'] : 0;
        $importTextAiGenerated = isset($optionAutoPostImport['text_is_ai_generated']) ? (int) $optionAutoPostImport['text_is_ai_generated'] : 0;
        $content .= '<a class="b2s-rp-new-collapse-trigger-import" data-toggle="collapse" aria-expanded="true">';
        $content .= '<span>' . esc_html__('Advanced Filter', 'blog2social') . '</span>';
        $content .= '<span class="glyphicon glyphicon-chevron-down b2s-rp-new-chevron"></span>';
        $content .= '</a>';
        $content .= '<div id="b2s-ap-import-advanced-filters" style="display:block;" class="b2s-auto-post-area collapse">';
        $content .= '<div class="b2s-rp-new-filter-list">';
        $content .= '<div class="b2s-rp-new-filter-row">';
        $content .= '<div class="b2s-rp-new-filter-row-head">';
        $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-clock b2s-rp-new-icon-muted"></span><span>' . esc_html__('Publish Time', 'blog2social') . '</span></div>';
        $content .= '<select name="b2s-import-auto-post-time-state" class="form-control b2s-rp-new-filter-state-select b2s-ap-delay-toggle" data-target="b2s-ap-import-delay-input">';
        $content .= '<option value="0"' . ($importShipState == 0 ? ' selected' : '') . '>' . esc_html__('immediately', 'blog2social') . '</option>';
        $content .= '<option value="1"' . ($importShipState == 1 ? ' selected' : '') . '>' . esc_html__('publish with a delay', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div class="b2s-rp-new-filter-input' . ($importShipState == 1 ? '' : ' b2s-info-display-none') . '" id="b2s-ap-import-delay-input">';
        $content .= '<div class="b2s-rp-new-filter-row-head">';
        $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-clock b2s-rp-new-icon-muted"></span><span>' . esc_html__('Delay (1-10 minutes)', 'blog2social') . '</span></div>';
        $content .= '<input type="number" maxlength="2" max="10" min="1" class="form-control b2s-rp-new-filter-state-select" name="b2s-import-auto-post-time-data" value="' . esc_attr((isset($optionAutoPostImport['ship_delay_time']) ? $optionAutoPostImport['ship_delay_time'] : 1)) . '" placeholder="1">';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="b2s-rp-new-filter-row">';
        $content .= '<div class="b2s-rp-new-filter-row-head">';
        $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-admin-page b2s-rp-new-icon-muted"></span><span>' . esc_html__('Post Templates', 'blog2social') . ' <a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('post_templates')) . '">' . esc_html__('Info', 'blog2social') . '</a></span></div>';
        $content .= '<select name="b2s-auto-post-import-template-setting" class="form-control b2s-rp-new-filter-state-select">';
        $content .= '<option value="0"' . ($importTemplateVal == 0 ? ' selected' : '') . '>' . esc_html__('Off', 'blog2social') . '</option>';
        $content .= '<option value="1"' . ($importTemplateVal == 1 ? ' selected' : '') . '>' . esc_html__('On', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="b2s-rp-new-filter-row">';
        $content .= '<div class="b2s-rp-new-filter-row-head">';
        $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-format-image b2s-rp-new-icon-muted"></span><span>' . esc_html__('Image is AI generated', 'blog2social') . '</span></div>';
        $content .= '<select name="b2s-import-auto-post-image-ai-generated" class="form-control b2s-rp-new-filter-state-select">';
        $content .= '<option value="0"' . ($importImageAiGenerated == 0 ? ' selected' : '') . '>' . esc_html__('Off', 'blog2social') . '</option>';
        $content .= '<option value="1"' . ($importImageAiGenerated == 1 ? ' selected' : '') . '>' . esc_html__('On', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="b2s-rp-new-filter-row">';
        $content .= '<div class="b2s-rp-new-filter-row-head">';
        $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-editor-textcolor b2s-rp-new-icon-muted"></span><span>' . esc_html__('Text is AI generated', 'blog2social') . '</span></div>';
        $content .= '<select name="b2s-import-auto-post-text-ai-generated" class="form-control b2s-rp-new-filter-state-select">';
        $content .= '<option value="0"' . ($importTextAiGenerated == 0 ? ' selected' : '') . '>' . esc_html__('Off', 'blog2social') . '</option>';
        $content .= '<option value="1"' . ($importTextAiGenerated == 1 ? ' selected' : '') . '>' . esc_html__('On', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= $this->getChosenPostTypesData($optionAutoPostImport);
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<input type="hidden" name="action" value="b2s_auto_post_settings">';

        $content .= '</form>';
        if (B2S_PLUGIN_USER_VERSION > 0) {
            $content .= '<button class="btn btn-primary btn-sm" id="b2s-auto-post-settings-btn" type="button">';
        } else {
            $content .= '<button class="btn btn-primary btn-sm b2s-btn-disabled b2s-save-settings-pro-info b2sInfoAutoPosterMModalBtn">';
        }
        $content .= esc_html__('Save', 'blog2social') . '</button>';

        return $content;
    }

    private function getMandantSelect($mandantId = 0, $twitterId = 0) {

        if (!empty($this->networkMandantData) && isset($this->networkMandantData->mandant) && isset($this->networkMandantData->auth)) {

            $mandant = $this->networkMandantData->mandant;
            $auth = $this->networkMandantData->auth;

            $authContent = '';

            $content = '<div id="b2s-network-group-wrap">';
            $content .= '<div class="b2s-curation-network-select-wrap">';
            $content .= '<i class="glyphicon glyphicon-user b2s-curation-network-select-glyphicon"></i>';
            $content .= '<select class="b2s-w-100 b2s-ap-profil-select" id="b2s-auto-post-profil-dropdown" name="b2s-auto-post-profil-dropdown">';
            foreach ($mandant as $k => $m) {
                $content .= '<option value="' . esc_attr($m->id) . '" ' . (((int) $m->id == (int) $mandantId) ? 'selected' : '') . '>' . esc_html((($m->id == 0) ? __("My Profile", 'blog2social') : $m->name)) . '</option>';
                $profilData = (isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0])) ? json_encode($auth->{$m->id}) : '';
                $authContent .= "<input type='hidden' id='b2s-auto-post-profil-data-" . esc_attr($m->id) . "' value='" . base64_encode($profilData) . "'/>";
            }
            $content .= '</select>';
            $content .= '<i class="glyphicon glyphicon-chevron-down b2s-compose-settings-toggle-icon select-chevron"></i>';
            $content .= '</div>';
            $content .= '<a class="b2s-preview-network-info-link" href="' . esc_url(get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-network') . '" target="_blank" style="vertical-align: middle;"><i class="glyphicon glyphicon-cog"></i></a>';
            $content .= '</div>';
            $content .= $authContent;

            //TOS Twitter 032018 - none multiple Accounts - User select once
            $content .= '<div id="b2s-network-group-wrap-twitter">';
            $content .= '<div class="b2s-curation-network-select-wrap">';
            $content .= '<img class="hidden-xs b2s-img-network-x-icon" alt="X" src="' . esc_url(plugins_url('/assets/images/portale/45_flat.png', B2S_PLUGIN_FILE)) . '">';
            $content .= '<select class="b2s-w-100 b2s-ap-profil-select" id="b2s-auto-post-profil-dropdown-twitter" name="b2s-auto-post-profil-dropdown-twitter">';
            $selectedTwitterAuthId = 0;
            foreach ($mandant as $k => $m) {
                if ((isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0]))) {
                    foreach ($auth->{$m->id} as $key => $value) {
                        if ($value->networkId == 2 || $value->networkId == 45) {
                            $content .= '<option data-mandant-id="' . esc_attr($m->id) . '" value="' . esc_attr($value->networkAuthId) . '" ' . (((int) $value->networkAuthId == (int) $twitterId) ? 'selected' : '') . '>' . esc_html($value->networkUserName) . '</option>';
                            if ((int) $value->networkAuthId == (int) $twitterId) {
                                $selectedTwitterAuthId = (int) $value->networkAuthId;
                            }
                        }
                    }
                }
            }
            $content .= '</select>';
            $content .= '<i class="glyphicon glyphicon-chevron-down b2s-compose-settings-toggle-icon select-chevron"></i>';
            $content .= '</div>';
            $content .= '<a class="b2s-preview-network-info-link b2sTwitterInfoModalBtn" href="#" style="vertical-align: middle;"><i class="glyphicon glyphicon-question-sign"></i></a>';
            $content .= '</div>';
            return $content;
        }
    }

    private function getPostTypesHtml($selected = array(), $type = 'publish') {
        $content = '';
        if (is_array($this->postTypesData) && !empty($this->postTypesData)) {

            $selectedItems = (is_array($selected) && isset($selected[$type])) ? $selected[$type] : array();
            $stateSelected = isset($selected[$type . '_state'])  ? $selected[$type . '_state'] : '0';

            if ($stateSelected == '0' && empty($selectedItems)) {
                $stateSelected = 'none';
            }

            //JM 07.09.2026 Default to all when not set to avoid empty choices
            if(!isset($selected[$type . '_state']) && $type === 'publish') {
                $stateSelected = 'all';
            }

            $icon = ($type === 'publish') ? 'dashicons-media-document' : 'dashicons-edit';
            $label = ($type === 'publish') ? esc_html__('New posts', 'blog2social') : esc_html__('Updated posts', 'blog2social');
            $inputId = 'b2s-ap-' . esc_attr($type) . '-types-input';
            $content .= '<div class="b2s-rp-new-filter-row">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons ' . $icon . ' b2s-rp-new-icon-muted"></span><span>' . $label . '</span></div>';
            $content .= '<select name="b2s-auto-post-' . esc_attr($type) . '-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="' . $inputId . '">';
            $content .= '<option value="none"' . ($stateSelected === 'none' ? ' selected' : '') . '>' . esc_html__('None', 'blog2social') . '</option>';
            $content .= '<option value="all"' . ($stateSelected === 'all' ? ' selected' : '') . '>' . esc_html__('All types', 'blog2social') . '</option>';
            $content .= '<option value="0"' . ($stateSelected == '0' ? ' selected' : '') . '>' . esc_html__('Include selected only', 'blog2social') . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '<div class="b2s-rp-new-filter-input' . (($stateSelected === 'all' || $stateSelected === 'none') ? ' b2s-info-display-none' : '') . '" id="' . $inputId . '">';
            $content .= '<select name="b2s-settings-auto-post-' . esc_attr($type) . '[]" data-placeholder="' . esc_attr__('Select post types...', 'blog2social') . '" class="b2s-auto-post-types-' . esc_attr($type) . ' form-control" multiple>';
            foreach ($this->postTypesData as $k => $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $sel = (in_array($v, $selectedItems)) ? ' selected' : '';
                    $content .= '<option value="' . esc_attr($v) . '"' . $sel . '>' . esc_html($v) . '</option>';
                }
            }
            $content .= '</select>';
            $content .= '</div>';
            $content .= '</div>';
        }
        return $content;
    }

    private function getNetworkAutoPostData($data = array()) {
        $html = '';
        if (!empty($this->networkAutoPostData)) {
            $selected = (isset($data['network_auth_id']) && is_array($data['network_auth_id'])) ? $data['network_auth_id'] : array();
            $networkName = unserialize(B2S_PLUGIN_NETWORK);
            $html .= '<ul class="list-group b2s-ap-network-details-container-list">';
            foreach ($this->networkAutoPostData as $k => $v) {
                if ($v == 18 && B2S_PLUGIN_USER_VERSION <= 1) {
                    continue;
                }
                $maxNetworkAccount = ($this->networkAuthCount !== false && is_array($this->networkAuthCount)) ? ((isset($this->networkAuthCount[$v])) ? $this->networkAuthCount[$v] : $this->networkAuthCount[0]) : false;
                $collapseId = 'b2s-ap-network-collapse-' . $v;
                $html .= '<li class="list-group-item-ap">';
                $html .= '<div class="b2s-rp-new-collapse-trigger-import b2s-ap-network-item-trigger" data-target="#' . esc_attr($collapseId) . '" aria-expanded="true">';
                $html .= '<img class="b2s-img-network b2s-ap-network-trigger-img" alt="' . esc_attr($networkName[$v]) . '" src="' . esc_url(plugins_url('/assets/images/portale/' . $v . '_flat.png', B2S_PLUGIN_FILE)) . '">';
                $html .= '<span>' . esc_html(ucfirst($networkName[$v])) . '</span>';
                if ($maxNetworkAccount !== false) {
                    $html .= '<span class="b2s-network-auth-count">(' . esc_html__("Connections", "blog2social") . ' <span class="b2s-network-auth-count-current" data-network-count-trigger="true" data-network-id="' . esc_attr($v) . '"></span>/' . esc_html($maxNetworkAccount) . ')</span>';
                }
                $html .= '<a href="admin.php?page=blog2social-network" class="b2s-info-btn">' . esc_html__('add/change connection', 'blog2social') . '</a>';
                $html .= '<span class="glyphicon glyphicon-chevron-down b2s-rp-new-chevron"></span>';
                $html .= '</div>';
                $html .= '<div id="' . esc_attr($collapseId) . '" style="display:block;" class="collapse">';
                $html .= '<select multiple class="b2s-network-item-auth-select form-control" name="b2s-import-auto-post-network-auth-id[]" data-network-id="' . esc_attr($v) . '" data-network-count="true" data-placeholder="' . esc_attr__('Select accounts...', 'blog2social') . '">';

                if (!empty($this->networkAuthData)) {
                    foreach ($this->networkAuthData as $i => $t) {
                        if ($v == $t->networkId) {
                            $networkType = ((int) $t->networkType == 0 ) ? __('Profile', 'blog2social') : __('Page', 'blog2social');
                            if ($t->notAllow !== false) {
                                $html .= '<option value="' . esc_attr($t->networkAuthId) . '" disabled>' . esc_html($networkType) . ': ' . esc_html(stripslashes($t->networkUserName)) . '</option>';
                            } else {
                                $selItem = (in_array($t->networkAuthId, $selected)) ? ' selected' : '';
                                $html .= '<option value="' . esc_attr($t->networkAuthId) . '"' . $selItem . '>' . esc_html($networkType) . ': ' . esc_html(stripslashes($t->networkUserName)) . '</option>';
                            }
                        }
                    }
                }

                $html .= '</select>';
                $html .= '</div>';
                $html .= '</li>';
            }

            $html .= '</ul>';
        }


        return $html;
    }

    private function getChosenPostTypesData($data = array()) {

        $html = '';

        if (is_array($this->postTypesData) && !empty($this->postTypesData)) {
            $filterVal = isset($data['post_filter']) ? (int) $data['post_filter'] : 0;
            $html .= '<div class="b2s-rp-new-filter-row">';
            $html .= '<div class="b2s-rp-new-filter-row-head">';
            $html .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-filter b2s-rp-new-icon-muted"></span><span>' . esc_html__('Filter Posts', 'blog2social') . '</span></div>';
            $html .= '<select name="b2s-import-auto-post-filter" class="form-control b2s-rp-new-filter-state-select b2s-ap-import-filter-toggle" data-target="b2s-ap-import-filter-rows">';
            $html .= '<option value="0"' . ($filterVal == 0 ? ' selected' : '') . '>' . esc_html__('Off', 'blog2social') . '</option>';
            $html .= '<option value="1"' . ($filterVal == 1 ? ' selected' : '') . '>' . esc_html__('On', 'blog2social') . '</option>';
            $html .= '</select>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div id="b2s-ap-import-filter-rows" class="b2s-rp-new-filter-rows-wrap' . ($filterVal != 1 ? ' b2s-info-display-none' : '') . '">';
            $typeStateVal = isset($data['post_type_state']) && (isset($data['post_type']) && !empty($data['post_type'])) ? $data['post_type_state'] : 'all';
            $html .= '<div class="b2s-rp-new-filter-row">';
            $html .= '<div class="b2s-rp-new-filter-row-head">';
            $html .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-media-document b2s-rp-new-icon-muted"></span><span>' . esc_html__('Post Types', 'blog2social') . '</span></div>';
            $html .= '<select name="b2s-import-auto-post-type-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-ap-import-type-input">';
            $html .= '<option value="all"' . ($typeStateVal === 'all' ? ' selected' : '') . '>' . esc_html__('All types', 'blog2social') . '</option>';
            $html .= '<option value="0"' . ($typeStateVal == '0' ? ' selected' : '') . '>' . esc_html__('Include selected only', 'blog2social') . '</option>';
            $html .= '<option value="1"' . ($typeStateVal == '1' ? ' selected' : '') . '>' . esc_html__('Exclude selected', 'blog2social') . '</option>';
            $html .= '</select>';
            $html .= '</div>';
            $html .= '<div class="b2s-rp-new-filter-input' . ($typeStateVal === 'all' ? ' b2s-info-display-none' : '') . '" id="b2s-ap-import-type-input">';
            $html .= '<select name="b2s-import-auto-post-type-data[]" data-placeholder="' . esc_attr__('Select post types...', 'blog2social') . '" class="b2s-import-auto-post-type form-control" multiple>';

            $selected = (isset($data['post_type']) && is_array($data['post_type'])) ? $data['post_type'] : array();
            foreach ($this->postTypesData as $k => $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $selItem = (in_array($v, $selected)) ? ' selected' : '';
                    $html .= '<option value="' . esc_attr($v) . '"' . $selItem . '>' . esc_html($v) . '</option>';
                }
            }
            $html .= '</select>';
            $html .= '</div>';
            $html .= '</div>';

            if (is_array($this->postCategoriesData) && !empty($this->postCategoriesData)) {

                $catStateVal = isset($data['post_categories_state']) && (isset($data['post_categories']) && !empty($data['post_categories'])) ? $data['post_categories_state'] : 'all';
                
                $html .= '<div class="b2s-rp-new-filter-row">';
                $html .= '<div class="b2s-rp-new-filter-row-head">';
                $html .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-category b2s-rp-new-icon-muted"></span><span>' . esc_html__('Post Categories', 'blog2social') . '</span></div>';
                $html .= '<select name="b2s-import-auto-post-categories-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-ap-import-categories-input">';
                $html .= '<option value="all"' . ($catStateVal === 'all' ? ' selected' : '') . '>' . esc_html__('All categories', 'blog2social') . '</option>';
                $html .= '<option value="0"' . ($catStateVal == '0' ? ' selected' : '') . '>' . esc_html__('Include selected only', 'blog2social') . '</option>';
                $html .= '<option value="1"' . ($catStateVal == '1' ? ' selected' : '') . '>' . esc_html__('Exclude selected', 'blog2social') . '</option>';
                $html .= '</select>';
                $html .= '</div>';
                $html .= '<div class="b2s-rp-new-filter-input' . ($catStateVal === 'all' ? ' b2s-info-display-none' : '') . '" id="b2s-ap-import-categories-input">';
                $html .= '<select name="b2s-import-auto-post-categories-data[]" data-placeholder="' . esc_attr__('Select Categories', 'blog2social') . '" class="b2s-import-auto-post-categories form-control" multiple>';

                $catSelected = (isset($data['post_categories']) && is_array($data['post_categories'])) ? $data['post_categories'] : array();
                foreach ($this->postCategoriesData as $k => $v) {
                    $selItem = (in_array($v->term_id, $catSelected)) ? ' selected' : '';
                    $html .= '<option value="' . esc_attr($v->term_id) . '"' . $selItem . '>' . esc_html($v->name) . '</option>';
                }
                $html .= '</select>';
                $html .= '</div>';
                $html .= '</div>';
            }

            $customTaxonomyNames = array();
            foreach ($this->postTaxonomiesData as $tax) {
                if (!in_array($tax, array('category', 'post_tag'))) {
                    $customTaxonomyNames[] = $tax;
                }
            }

            if (!empty($customTaxonomyNames)) {
                $customTaxonomies = array();
                foreach ($customTaxonomyNames as $tax) {
                    $terms = get_terms(array(
                        'taxonomy' => $tax
                    ));
                    if (!is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            $customTaxonomies[] = $term;
                        }
                    }
                }

                $taxStateVal = isset($data['post_taxonomies_state']) && !empty($customTaxonomies) ? $data['post_taxonomies_state'] : 'all';
                $html .= '<div class="b2s-rp-new-filter-row">';
                $html .= '<div class="b2s-rp-new-filter-row-head">';
                $html .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-tag b2s-rp-new-icon-muted"></span><span>' . esc_html__('Custom taxonomies', 'blog2social') . '</span></div>';
                $html .= '<select name="b2s-import-auto-post-taxonomies-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-ap-import-taxonomies-input">';
                $html .= '<option value="all"' . ($taxStateVal === 'all' ? ' selected' : '') . '>' . esc_html__('All taxonomies', 'blog2social') . '</option>';
                $html .= '<option value="0"' . ($taxStateVal == '0' ? ' selected' : '') . '>' . esc_html__('Include selected only', 'blog2social') . '</option>';
                $html .= '<option value="1"' . ($taxStateVal == '1' ? ' selected' : '') . '>' . esc_html__('Exclude selected', 'blog2social') . '</option>';
                $html .= '</select>';
                $html .= '</div>';
                $html .= '<div class="b2s-rp-new-filter-input' . ($taxStateVal === 'all' ? ' b2s-info-display-none' : '') . '" id="b2s-ap-import-taxonomies-input">';
                $html .= '<select name="b2s-import-auto-post-taxonomies-data[]" data-placeholder="' . esc_attr__('Select Taxonomies', 'blog2social') . '" class="b2s-import-auto-post-taxonomies form-control" multiple>';

                $taxSelected = (isset($data['post_taxonomies']) && is_array($data['post_taxonomies'])) ? $data['post_taxonomies'] : array();
                foreach ($customTaxonomies as $k => $v) {
                    $selItem = (in_array($v->term_id, $taxSelected)) ? ' selected' : '';
                    $html .= '<option value="' . esc_attr($v->term_id) . '"' . $selItem . '>' . esc_html($v->name) . '</option>';
                }
                $html .= '</select>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        return $html;
    }

    private function getChosenPostCategoriesData($data = array()) {

        $html = '';
        if (is_array($this->postCategoriesData) && !empty($this->postCategoriesData)) {
            $selected_categories = (isset($data['categories_data']) && is_array($data['categories_data'])) ? $data['categories_data'] : array();
            $stateSelected = isset($data['categories_state']) && count($selected_categories)>0 ? $data['categories_state'] : 'all';
            $html .= '<div class="b2s-rp-new-filter-row">';
            $html .= '<div class="b2s-rp-new-filter-row-head">';
            $html .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-category b2s-rp-new-icon-muted"></span><span>' . esc_html__('Categories', 'blog2social') . '</span></div>';
            $html .= '<select name="b2s-auto-post-categories-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-ap-categories-input">';
            $html .= '<option value="all"' . ($stateSelected === 'all' ? ' selected' : '') . '>' . esc_html__('All categories', 'blog2social') . '</option>';
            $html .= '<option value="0"' . ($stateSelected == '0' ? ' selected' : '') . '>' . esc_html__('Include selected only', 'blog2social') . '</option>';
            $html .= '<option value="1"' . ($stateSelected == '1' ? ' selected' : '') . '>' . esc_html__('Exclude selected', 'blog2social') . '</option>';
            $html .= '</select>';
            $html .= '</div>';
            $html .= '<div class="b2s-rp-new-filter-input' . ($stateSelected === 'all' ? ' b2s-info-display-none' : '') . '" id="b2s-ap-categories-input">';
            $html .= '<select name="b2s-auto-post-categories-data[]" data-placeholder="' . esc_attr__('Select Post Categories', 'blog2social') . '" class="b2s-auto-post-categories form-control" multiple>';

            foreach ($this->postCategoriesData as $cat) {
                $selected = in_array($cat->term_taxonomy_id, $selected_categories) ? ' selected' : '';
                $html .= '<option value="' . esc_attr($cat->term_taxonomy_id) . '"' . $selected . '>' . esc_html($cat->name) . '</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
            $html .= '</div>';
        }
        return $html;
    }

    private function getChosenPostTagsData($data = array()) {

        $html = '';
        if (is_array($this->postTagsData) && !empty($this->postTagsData)) {
            $selected_tags = (isset($data['tags_data']) && is_array($data['tags_data'])) ? $data['tags_data'] : array();
            $stateSelected = isset($data['tags_state']) && !empty($selected_tags)?  $data['tags_state'] : 'all';
            $html .= '<div class="b2s-rp-new-filter-row">';
            $html .= '<div class="b2s-rp-new-filter-row-head">';
            $html .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-tag b2s-rp-new-icon-muted"></span><span>' . esc_html__('Tags', 'blog2social') . '</span></div>';
            $html .= '<select name="b2s-auto-post-tags-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-ap-tags-input">';
            $html .= '<option value="all"' . ($stateSelected === 'all' ? ' selected' : '') . '>' . esc_html__('All tags', 'blog2social') . '</option>';
            $html .= '<option value="0"' . ($stateSelected == '0' ? ' selected' : '') . '>' . esc_html__('Include selected only', 'blog2social') . '</option>';
            $html .= '<option value="1"' . ($stateSelected == '1' ? ' selected' : '') . '>' . esc_html__('Exclude selected', 'blog2social') . '</option>';
            $html .= '</select>';
            $html .= '</div>';
            $html .= '<div class="b2s-rp-new-filter-input' . ($stateSelected === 'all' ? ' b2s-info-display-none' : '') . '" id="b2s-ap-tags-input">';
            $html .= '<select name="b2s-auto-post-tags-data[]" data-placeholder="' . esc_attr__('Select Post Tags', 'blog2social') . '" class="b2s-auto-post-tags form-control" multiple>';

            foreach ($this->postTagsData as $tag) {
                $selected = in_array($tag->term_taxonomy_id, $selected_tags) ? ' selected' : '';
                $html .= '<option value="' . esc_attr($tag->term_taxonomy_id) . '"' . $selected . '>' . esc_html($tag->name) . '</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
            $html .= '</div>';
        }
        return $html;
    }

    private function getRelayBtnHtml($networkAuthId, $networkId) {
        $relay = '<div class="form-group b2s-post-relay-area-select pull-left"><div class="checkbox checbox-switch switch-success"><label>';
        $relay .= '<input type="checkbox" class="b2s-post-item-details-relay form-control" data-user-version="' . esc_attr(B2S_PLUGIN_USER_VERSION) . '" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" name="b2s[' . esc_attr($networkAuthId) . '][post_relay]" value="1"/>';
        $relay .= '<span></span>';
        $relay .= esc_html__('Enable Retweets for all Tweets with the selected profile', 'blog2social') . ' <a href="#" class="btn-xs hidden-sm b2sInfoPostRelayModalBtn">' . esc_html__('Info', 'blog2social') . '</a>';
        $relay .= ' </label></div></div>';
        return $relay;
    }
}
