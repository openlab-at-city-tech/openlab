<?php

class B2S_AutoPost_Item {

    private $options;
    private $postTypesData;
    private $postCategoriesData;
    private $networkAuthData = array();
    private $networkAutoPostData;
    private $networkAuthCount = false;

    public function __construct() {
        $this->getSettings();
        $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
        $this->postTypesData = get_post_types(array('public' => true));
        $this->postCategoriesData = get_categories(array('public' => true));
        $this->postTaxonomiesData = get_taxonomies(array('public' => true));
    }

    private function getSettings() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getSettings', 'portal_view_mode' => true, 'portal_auth_count' => true, 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));
        if (is_object($result) && isset($result->result) && (int) $result->result == 1 && isset($result->portale) && is_array($result->portale)) {
            $this->networkAuthCount = isset($result->portal_auth_count) ? $result->portal_auth_count : false;
            $this->networkAuthData = isset($result->portal_auth) ? $result->portal_auth : array();
            $this->networkAutoPostData = isset($result->portal_auto_post) ? $result->portal_auto_post : array();
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

        $content = '';
        $content .= '<input type="hidden" class="b2s-autopost-m-show-modal" value="' . ((isset($optionAutoPost['active'])) ? '0' : '1') . '">';
        $content .= '<input type="hidden" class="b2s-autopost-a-show-modal" value="' . ((isset($optionAutoPostImport['active'])) ? '0' : '1') . '">';
        $content .= '<div class="panel panel-group b2s-auto-post-own-general-warning"><div class="panel-body">';
        $content .= '<span class="glyphicon glyphicon-exclamation-sign glyphicon-warning"></span> ' . sprintf(__('Posts for Facebook Profiles will be shown on your "Site & Blog Content" navigation bar in the "Instant Sharing" tab. To share the post on your Facebook Profile just click on the "Share" button next to your post. More information in the <a href="%s" target="_blank">Instant Sharing guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('facebook_instant_sharing')));
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<h4 class="b2s-auto-post-header">' . esc_html__('Autoposter', 'blog2social') . '</h4><a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('auto_post_manuell')) . '">Info</a>';

        if (isset($optionAutoPost['assignBy']) && (int) $optionAutoPost['assignBy'] > 0) {
            $content .= '<div class="panel panel-group b2s-auto-post-own-general-warning"><div class="panel-body">';
            $content .= '<span class="glyphicon glyphicon-exclamation-sign glyphicon-warning"></span>' . esc_html__('The settings for the Auto-Poster were configured for you by a WordPress admin.', 'blog2social') . '  ';
            $content .= '<a href="#" id="b2s-auto-post-assign-by-disconnect">' . esc_html__('Disconnect', 'blog2social') . '</a>';
            $content .= '</div>';
            $content .= '</div>';
        } else {
            $content .= '<p class="b2s-bold">' . esc_html__('Set up your autoposter to automatically share your new or updated posts, pages and custom post types on your social media channels.', 'blog2social') . '</p>';
            $content .= '<form id = "b2s-user-network-settings-auto-post-own" method = "post">';
            $content .= '<div class="' . (!empty($isPremium) ? 'b2s-btn-disabled' : '') . '">';
            $content .= '<input data-size="mini" data-toggle="toggle" data-width="90" data-height="22" data-onstyle="primary" data-on="ON" data-off="OFF" ' . (($autoPostActive) ? 'checked' : '') . '  name="b2s-manuell-auto-post" class="b2s-auto-post-area-toggle" data-area-type="manuell" value="1" type="checkbox">';
            $content .= '</div>';
            $content .= '<div class="b2s-auto-post-area" data-area-type="manuell"' . (($autoPostActive) ? '' : ' style="display:none;"') . '>';
            $content .= '<br>';
            $content .= '<div class="' . (!empty($isPremium) ? 'b2s-btn-disabled' : '') . '">';
            $content .= '<div class="alert alert-danger b2s-auto-post-error" data-error-reason="no-auth-in-mandant" style="display:none;">' . esc_html__('There are no social network accounts assigned to your selected network collection. Please assign at least one social network account or select another network collection.', 'blog2social') . '<a href="' . esc_url(((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-network') . '" target="_bank">' . esc_html__('Network settings', 'blog2social') . '</a></div>';
            $content .= '<p class="b2s-bold">' . esc_html__('Select your preferred network collection for autoposting. This collection defines the social media accounts on which the autoposter will share your social media posts automatically.', 'blog2social') . '</p>';
            $content .= $this->getMandantSelect((isset($optionAutoPost['profile']) ? $optionAutoPost['profile'] : 0), (isset($optionAutoPost['twitter']) ? $optionAutoPost['twitter'] : 0));
            $content .= '</div>';
            $content .= '<br>';
            $content .= '<div class="alert alert-danger b2s-auto-post-error" data-error-reason="no-post-type" style="display:none;">' . esc_html__('Please select a post type', 'blog2social') . '</div>';
            $content .= '<div class="row ' . (!empty($isPremium) ? 'b2s-btn-disabled' : '') . '">';
            $content .= '<div class="col-xs-12 col-md-2">';
            $content .= '<label class="b2s-auto-post-publish-label">' . esc_html__('new posts', 'blog2social') . '</label>';
            $content .= '<br><small><button class="btn btn-link btn-xs hidden-xs b2s-post-type-select-btn" data-post-type="publish" data-select-toogle-state="0" data-select-toogle-name="' . esc_attr__('Unselect all', 'blog2social') . '">' . esc_html__('Select all', 'blog2social') . '</button></small>';
            $content .= '</div>';
            $content .= '<div class="col-xs-12 col-md-6">';
            $content .= $this->getPostTypesHtml($optionAutoPost);
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<br>';
            $content .= '<div class="row ' . (!empty($isPremium) ? 'b2s-btn-disabled' : '') . '">';
            $content .= '<div class="col-md-12"><div class="panel panel-group b2s-auto-post-own-update-warning" style="display: none;"><div class="panel-body"><span class="glyphicon glyphicon-exclamation-sign glyphicon-warning"></span> ' . esc_html__('By enabling this feature your previously published social media posts will be sent again to your selected social media channels as soon as the post is updated.', 'blog2social') . '</div></div></div>';
            $content .= '<div class"clearfix"></div>';
            $content .= '<div class="col-xs-12 col-md-2">';
            $content .= '<label class="b2s-auto-post-update-label">' . esc_html__('updated posts', 'blog2social') . '</label>';
            $content .= '<br><small><button class="btn btn-link btn-xs hidden-xs b2s-post-type-select-btn" data-post-type="update" data-select-toogle-state="0" data-select-toogle-name="' . esc_html__('Unselect all', 'blog2social') . '">' . esc_html__('Select all', 'blog2social') . '</button></small>';
            $content .= '</div>';
            $content .= '<div class="col-xs-12 col-md-6">';
            $content .= $this->getPostTypesHtml($optionAutoPost, 'update');
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<br>';
            $content .= '<div class="row ' . (!empty($isPremium) ? 'b2s-btn-disabled' : '') . '">';
            $content .= '<div class="col-md-12">';
            $content .= '<input id="b2s-auto-post-best-times" class="b2s-auto-post-best-times" name="b2s-auto-post-best-times" type="checkbox" value="1" ' . ((isset($optionAutoPost['best_times']) && (int) $optionAutoPost['best_times'] == 1) ? 'checked' : '') . '><label for="b2s-auto-post-best-times"> ' . esc_html__('Apply best times', 'blog2social') . '</label> <a href="#" class="b2sAutoPostBestTimesInfoModalBtn">Info</a>';
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
                    $content .= '<br>';
                    $content .= '<div class="row">';
                    $content .= '<div class="col-md-12">';
                    $content .= '<span class="b2s-bold">' . esc_html__('Transfer Auto-Poster settings to other users', 'blog2social') . ' </span>';
                    $content .= '<a class="b2sInfoAssignAutoPostBtn" href="#">' . esc_html__('Info', 'blog2social') . '</a><br>';
                    $content .= '<select name="b2s-auto-post-assign-user-data[]" multiple="" data-placeholder="Select User" class="b2s-auto-post-assign-user">';
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
        }

        $content .= '<br>';
        $content .= '<hr>';
        $content .= '<h4 class="b2s-auto-post-header">' . esc_html__('Autoposter for Imported Posts', 'blog2social') . '</h4><a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('auto_post_import')) . '">Info</a>';
        $content .= '<p class="b2s-bold">' . esc_html__('Set up your autoposter to automatically share your imported posts, pages and custom post types on your social media channels.', 'blog2social') . '</p>';
        $content .= '<p>' . esc_html__('Your current license:', 'blog2social') . '<span class="b2s-key-name"> ' . esc_html($versionType[B2S_PLUGIN_USER_VERSION]) . '</span> ';
        if (B2S_PLUGIN_USER_VERSION == 0) {
            $content .= '<br>' . esc_html__('Immediate Cross-Posting across all networks: Share an unlimited number of posts', 'blog2social') . '<br>';
            $content .= esc_html__('Scheduled Auto-Posting', 'blog2social') . ': <a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '" target="_blank">' . esc_html__('Upgrade', 'blog2social') . '</a>';
        } else {
            $content .= '(' . esc_html__('share up to', 'blog2social') . ' ' . esc_html($limit[B2S_PLUGIN_USER_VERSION]) . ((B2S_PLUGIN_USER_VERSION >= 2) ? ' ' . esc_html__('posts per day', 'blog2social') : '') . ') ';
            $content .= '<a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '" target="_blank">' . esc_html__('Upgrade', 'blog2social') . '</a>';
        }

        $content .= '</p>';
        $content .= '<br>';
        $content .= '<div class="' . (!empty($isPremium) ? 'b2s-btn-disabled' : '') . '">';
        $content .= '<input data-size="mini" data-toggle="toggle" data-width="90" data-height="22" data-onstyle="primary" data-on="ON" data-off="OFF" ' . (($autoPostImportActive) ? 'checked' : '') . '  name="b2s-import-auto-post" class="b2s-auto-post-area-toggle" data-area-type="import" value="1" type="checkbox">';
        $content .= '<div class="b2s-auto-post-area" data-area-type="import"' . (($autoPostImportActive) ? '' : ' style="display:none;"') . '>';
        $content .= '<br><br>';
        $content .= '<div class="alert alert-danger b2s-auto-post-error" data-error-reason="import-no-auth" style="display:none;">' . esc_html__('Please select a social media network', 'blog2social') . '</div>';
        $content .= '<p class="b2s-bold">' . esc_html__('Available networks to select your auto-post connecitons:', 'blog2social') . '</p>';
        $content .= '<div class="b2s-network-tos-auto-post-import-warning"><div class="alert alert-danger">' . esc_html__('In accordance with the new Twitter TOS, one Twitter account can be selected as your primary Twitter account for auto-posting.', 'blog2social') . ' <a href="' . esc_url(B2S_Tools::getSupportLink('network_tos_faq_032018')) . '" target="_blank">' . esc_html__('More information', 'blog2social') . '</a></div></div>';
        $content .= $this->getNetworkAutoPostData($optionAutoPostImport);
        $content .= '<p class="b2s-bold">' . esc_html__('Select to auto-post immediately after publishing or with a delay', 'blog2social') . '</p>';
        $content .= '<input id="b2s-import-auto-post-time-now" name="b2s-import-auto-post-time-state" ' . (((isset($optionAutoPostImport['ship_state']) && (int) $optionAutoPostImport['ship_state'] == 0) || !isset($optionAutoPostImport['ship_state'])) ? 'checked' : '') . ' value="0" type="radio"><label for="b2s-import-auto-post-time-now">' . esc_html__('immediately', 'blog2social') . '</label><br>';
        $content .= '<input id="b2s-import-auto-post-time-delay" name="b2s-import-auto-post-time-state" value="1" ' . ((isset($optionAutoPostImport['ship_state']) && (int) $optionAutoPostImport['ship_state'] == 1) ? 'checked' : '') . ' type="radio"><label for="b2s-import-auto-post-time-delay">' . esc_html__('publish with a delay of', 'blog2social');
        $content .= ' <input type="number" maxlength="2" max="10" min="1" class="b2s-input-text-size-45" name="b2s-import-auto-post-time-data" value="' . esc_attr((isset($optionAutoPostImport['ship_delay_time']) ? $optionAutoPostImport['ship_delay_time'] : 1)) . '" placeholder="1" > (1-10) ' . esc_html__('minutes', 'blog2social') . '</label>';
        $content .= '<br>';
        $content .= $this->getChosenPostTypesData($optionAutoPostImport);
        $content .= '</div>';
        $content .= '<input type="hidden" name="action" value="b2s_auto_post_settings">';
        $content .= '</div>';
        $content .= '</form>';
        if (B2S_PLUGIN_USER_VERSION > 0) {
            $content .= '<button class="pull-right btn btn-primary btn-sm" id="b2s-auto-post-settings-btn" type="submit">';
        } else {
            $content .= '<button class="pull-right btn btn-primary btn-sm b2s-btn-disabled b2s-save-settings-pro-info b2sInfoAutoPosterMModalBtn">';
        }
        $content .= esc_html__('Save', 'blog2social') . '</button>';

        return $content;
    }

    private function getMandantSelect($mandantId = 0, $twitterId = 0) {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getProfileUserAuth', 'token' => B2S_PLUGIN_TOKEN)));
        if (isset($result->result) && (int) $result->result == 1 && isset($result->data) && !empty($result->data) && isset($result->data->mandant) && isset($result->data->auth) && !empty($result->data->mandant) && !empty($result->data->auth)) {
            $mandant = $result->data->mandant;
            $auth = $result->data->auth;
            $authContent = '';
            $content = '<div class="row"><div class="col-md-3 b2s-auto-post-profile"><label for="b2s-auto-post-profil-dropdown">' . esc_html__('Select network collection:', 'blog2social') . '</label>
                <select class="b2s-w-100" id="b2s-auto-post-profil-dropdown" name="b2s-auto-post-profil-dropdown">';
            foreach ($mandant as $k => $m) {
                $content .= '<option value="' . esc_attr($m->id) . '" ' . (((int) $m->id == (int) $mandantId) ? 'selected' : '') . '>' . esc_html((($m->id == 0) ? __($m->name, 'blog2social') : $m->name)) . '</option>';
                $profilData = (isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0])) ? json_encode($auth->{$m->id}) : '';
                $authContent .= "<input type='hidden' id='b2s-auto-post-profil-data-" . esc_attr($m->id) . "' value='" . base64_encode($profilData) . "'/>";
            }
            $content .= '</select><div class="pull-right"><a href="' . esc_url(get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-network') . '" target="_blank">' . esc_html__('Network settings', 'blog2social') . '</a></div></div>';
            $content .= $authContent;

            //TOS Twitter 032018 - none multiple Accounts - User select once
            $content .= '<div class="col-md-3 b2s-auto-post-twitter-profile"><label for="b2s-auto-post-profil-dropdown-twitter">' . esc_html__('Select Twitter profile:', 'blog2social') . '</label> <select class="b2s-w-100" id="b2s-auto-post-profil-dropdown-twitter" name="b2s-auto-post-profil-dropdown-twitter">';
            $selectedTwitterAuthId = 0;
            foreach ($mandant as $k => $m) {
                if ((isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0]))) {
                    foreach ($auth->{$m->id} as $key => $value) {
                        if ($value->networkId == 2) {
                            $content .= '<option data-mandant-id="' . esc_attr($m->id) . '" value="' . esc_attr($value->networkAuthId) . '" ' . (((int) $value->networkAuthId == (int) $twitterId) ? 'selected' : '') . '>' . esc_html($value->networkUserName) . '</option>';
                            if ((int) $value->networkAuthId == (int) $twitterId) {
                                $selectedTwitterAuthId = (int) $value->networkAuthId;
                            }
                        }
                    }
                }
            }
            $content .= '</select><div class="pull-right"><a href="#" class="b2sTwitterInfoModalBtn">' . esc_html__('Info', 'blog2social') . '</a></div>'
//                    . $this->getRelayBtnHtml($selectedTwitterAuthId, 2)
                    . '</div></div>';
            return $content;
        }
    }

    private function getPostTypesHtml($selected = array(), $type = 'publish') {
        $content = '';
        $selected = (is_array($selected) && isset($selected[$type])) ? $selected[$type] : array();
        if (is_array($this->postTypesData) && !empty($this->postTypesData)) {
            foreach ($this->postTypesData as $k => $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $selItem = (in_array($v, $selected)) ? 'checked' : '';
                    $content .= ' <div class="b2s-post-type-list"><input id="b2s-post-type-item-' . esc_attr($type) . '-' . esc_attr($v) . '" class="b2s-post-type-item-' . esc_attr($type) . '" value="' . esc_attr($v) . '" name="b2s-settings-auto-post-' . esc_attr($type) . '[]" type="checkbox" ' . $selItem . '><label for="b2s-post-type-item-' . esc_attr($type) . '-' . esc_attr($v) . '"> ' . esc_html($v) . '</label></div>';
                }
            }
        }
        return $content;
    }

    private function getNetworkAutoPostData($data = array()) {
        $html = '';
        if (!empty($this->networkAutoPostData)) {
            $selected = (isset($data['network_auth_id']) && is_array($data['network_auth_id'])) ? $data['network_auth_id'] : array();
            $networkName = unserialize(B2S_PLUGIN_NETWORK);
            $html .= '<ul class="list-group b2s-network-details-container-list">';
            foreach ($this->networkAutoPostData as $k => $v) {
                if ($v == 18 && B2S_PLUGIN_USER_VERSION <= 1) {
                    continue;
                }
                $maxNetworkAccount = ($this->networkAuthCount !== false && is_array($this->networkAuthCount)) ? ((isset($this->networkAuthCount[$v])) ? $this->networkAuthCount[$v] : $this->networkAuthCount[0]) : false;
                $html .= '<li class="list-group-item">';
                $html .= '<div class="media">';
                $html .= '<img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr($networkName[$v]) . '" src="' . esc_url(plugins_url('/assets/images/portale/' . $v . '_flat.png', B2S_PLUGIN_FILE)) . '">';
                $html .= '<div class="media-body network">';
                $html .= '<h4>' . esc_html(ucfirst($networkName[$v]));
                if ($maxNetworkAccount !== false) {
                    $html .= ' <span class="b2s-network-auth-count">(' . esc_html__("Connections", "blog2social") . ' <span class="b2s-network-auth-count-current" data-network-count-trigger="true" data-network-id="' . esc_attr($v) . '"></span>/' . esc_html($maxNetworkAccount) . ')</span>';
                }
                $html .= ' <a href="admin.php?page=blog2social-network" class="b2s-info-btn">' . esc_html__('add/change connection', 'blog2social') . '</a>';
                $html .= '</h4>';
                $html .= '<ul class="b2s-network-item-auth-list" data-network-id="' . esc_attr($v) . '" data-network-count="true" >';
                if (!empty($this->networkAuthData)) {
                    foreach ($this->networkAuthData as $i => $t) {
                        if ($v == $t->networkId) {
                            $html .= '<li class="b2s-network-item-auth-list-li" data-network-auth-id="' . esc_attr($t->networkAuthId) . '"  data-network-id="' . esc_attr($t->networkId) . '" data-network-type="0">';
                            $networkType = ((int) $t->networkType == 0 ) ? __('Profile', 'blog2social') : __('Page', 'blog2social');
                            if ($t->notAllow !== false) {
                                $html .= '<span class="glyphicon glyphicon-remove-circle glyphicon-danger"></span> <span class="not-allow">' . esc_html($networkType) . ': ' . esc_html(stripslashes($t->networkUserName)) . '</span> ';
                            } else {
                                $selItem = (in_array($t->networkAuthId, $selected)) ? 'checked' : '';
                                $html .= '<input id="b2s-import-auto-post-network-auth-id-' . esc_attr($t->networkAuthId) . '" class="b2s-network-tos-check" data-network-id="' . esc_attr($t->networkId) . '" ' . $selItem . ' value="' . esc_attr($t->networkAuthId) . '" name="b2s-import-auto-post-network-auth-id[]" type="checkbox"> <label for="b2s-import-auto-post-network-auth-id-' . esc_attr($t->networkAuthId) . '">' . esc_html($networkType) . ': ' . esc_html(stripslashes($t->networkUserName)) . '</label>';
                            }
                            $html .= '</li>';
                        }
                    }
                }

                $html .= '</ul>';
                $html .= '</div>';
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
            $html .= '<br>';
            $html .= '<p><b><input value="1"  ' . ((isset($data['post_filter']) && (int) $data['post_filter'] == 1) ? 'checked' : '') . ' name="b2s-import-auto-post-filter" type="checkbox"> ' . esc_html__('Filter Posts (Only posts that meet the following criteria will be autoposted)', 'blog2social') . '</b></p>';
            $html .= '<p>' . esc_html__('Post Types', 'blog2social');
            $html .= ' <input id="b2s-import-auto-post-type-state-include" name="b2s-import-auto-post-type-state" value="0" ' . (((isset($data['post_type_state']) && (int) $data['post_type_state'] == 0) || !isset($data['post_type_state'])) ? 'checked' : '') . ' type="radio"><label class="padding-bottom-3" for="b2s-import-auto-post-type-state-include">' . esc_html__('Include (Post only...)', 'blog2social') . '</label> ';
            $html .= '<input id="b2s-import-auto-post-type-state-exclude" name="b2s-import-auto-post-type-state" value="1" ' . ((isset($data['post_type_state']) && (int) $data['post_type_state'] == 1) ? 'checked' : '') . ' type="radio"><label class="padding-bottom-3" for="b2s-import-auto-post-type-state-exclude">' . esc_html__('Exclude (Do no post ...)', 'blog2social') . '</label>';
            $html .= '</p>';
            $html .= '<select name="b2s-import-auto-post-type-data[]" data-placeholder="' . esc_html__('Select Post Types', 'blog2social') . '" class="b2s-import-auto-post-type" multiple>';

            $selected = (isset($data['post_type']) && is_array($data['post_type'])) ? $data['post_type'] : array();

            foreach ($this->postTypesData as $k => $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $selItem = (in_array($v, $selected)) ? 'selected' : '';
                    $html .= '<option ' . $selItem . ' value="' . esc_attr($v) . '">' . esc_html($v) . '</option>';
                }
            }

            $html .= '</select>';

            //Categories
            $html .= '<br>';
            $html .= '<br>';
            $html .= '<p>' . esc_html__('Post Categories', 'blog2social');
            $html .= ' <input id="b2s-import-auto-post-categories-state-include" name="b2s-import-auto-post-categories-state" value="0" ' . (((isset($data['post_categories_state']) && (int) $data['post_categories_state'] == 0) || !isset($data['post_categories_state'])) ? 'checked' : '') . ' type="radio"><label class="padding-bottom-3" for="b2s-import-auto-post-categories-state-include">' . esc_html__('Include (Post only...)', 'blog2social') . '</label> ';
            $html .= '<input id="b2s-import-auto-post-categories-state-exclude" name="b2s-import-auto-post-categories-state" value="1" ' . ((isset($data['post_categories_state']) && (int) $data['post_categories_state'] == 1) ? 'checked' : '') . ' type="radio"><label class="padding-bottom-3" for="b2s-import-auto-post-categories-state-exclude">' . esc_html__('Exclude (Do no post ...)', 'blog2social') . '</label>';
            $html .= '</p>';
            $html .= '<select name="b2s-import-auto-post-categories-data[]" data-placeholder="' . esc_html__('Select Categories', 'blog2social') . '" class="b2s-import-auto-post-categories" multiple>';

            $catSelected = (isset($data['post_categories']) && is_array($data['post_categories'])) ? $data['post_categories'] : array();

            foreach ($this->postCategoriesData as $k => $v) {
                $selItem = (in_array($v->term_id, $catSelected)) ? 'selected' : '';
                $html .= '<option ' . $selItem . ' value="' . esc_attr($v->term_id) . '">' . esc_html($v->name) . '</option>';
            }

            $html .= '</select>';

            //Custom Taxonomies
            $html .= '<br>';
            $html .= '<br>';
            $html .= '<p>' . esc_html__('Custom taxonomies', 'blog2social');
            $html .= ' <input id="b2s-import-auto-post-taxonomies-state-include" name="b2s-import-auto-post-taxonomies-state" value="0" ' . (((isset($data['post_taxonomies_state']) && (int) $data['post_taxonomies_state'] == 0) || !isset($data['post_taxonomies_state'])) ? 'checked' : '') . ' type="radio"><label class="padding-bottom-3" for="b2s-import-auto-post-taxonomies-state-include">' . esc_html__('Include (Post only...)', 'blog2social') . '</label> ';
            $html .= '<input id="b2s-import-auto-post-taxonomies-state-exclude" name="b2s-import-auto-post-taxonomies-state" value="1" ' . ((isset($data['post_taxonomies_state']) && (int) $data['post_taxonomies_state'] == 1) ? 'checked' : '') . ' type="radio"><label class="padding-bottom-3" for="b2s-import-auto-post-taxonomies-state-exclude">' . esc_html__('Exclude (Do no post ...)', 'blog2social') . '</label>';
            $html .= '</p>';
            $html .= '<select name="b2s-import-auto-post-taxonomies-data[]" data-placeholder="' . esc_html__('Select Taxonomies', 'blog2social') . '" class="b2s-import-auto-post-taxonomies" multiple>';

            $catSelected = (isset($data['post_categories']) && is_array($data['post_categories'])) ? $data['post_categories'] : array();

            $customTaxonomies = array();
            foreach ($this->postTaxonomiesData as $tax) {
                if (!in_array($tax, array('category', 'post_tag'))) {
                    $terms = get_terms(array(
                        'taxonomy' => $tax
                    ));
                    foreach ($terms as $term) {
                        $customTaxonomies[] = $term;
                    }
                }
            }
            foreach ($customTaxonomies as $k => $v) {
                $selItem = (in_array($v->term_id, $catSelected)) ? 'selected' : '';
                $html .= '<option ' . $selItem . ' value="' . esc_attr($v->term_id) . '">' . esc_html($v->name) . '</option>';
            }

            $html .= '</select>';
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
