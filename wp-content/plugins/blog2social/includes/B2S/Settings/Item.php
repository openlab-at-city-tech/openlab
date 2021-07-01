<?php

class B2S_Settings_Item {

    private $userSchedTimeData = array();
    private $networkData = array();
    private $settings = array();
    private $lang;
    private $allowPage;
    private $options;
    private $generalOptions;
    private $allowGroup;
    private $timeInfo;
    private $authUrl;

    public function __construct() {
        $this->getSettings();
        $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
        $this->generalOptions = new B2S_Options(0, 'B2S_PLUGIN_GENERAL_OPTIONS');
        $this->lang = substr(B2S_LANGUAGE, 0, 2);
        $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
        $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
        $this->timeInfo = unserialize(B2S_PLUGIN_SCHED_DEFAULT_TIMES_INFO);
        $this->authUrl = B2S_PLUGIN_API_ENDPOINT_AUTH_SHORTENER . '?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2);
    }

    private function getSettings() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getSettings', 'portal_view_mode' => true, 'portal_auth_count' => true, 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));
        if (is_object($result) && isset($result->result) && (int) $result->result == 1 && isset($result->portale) && is_array($result->portale)) {
            $this->networkData = $result->portale;
            if (isset($result->settings) && is_object($result->settings)) {
                $this->settings = $result->settings;
            }
        }
    }

    public function getGeneralSettingsHtml() {

        $isCheckedAllowShortcode = (get_option('B2S_PLUGIN_USER_ALLOW_SHORTCODE_' . B2S_PLUGIN_BLOG_USER_ID) !== false) ? 1 : 0;

        $optionUserTimeZone = $this->options->_getOption('user_time_zone');
        $legacyMode = $this->generalOptions->_getOption('legacy_mode');
        $isCheckedLegacyMode = ($legacyMode !== false && $legacyMode == 1) ? 1 : 0;  //default not active , 1=active 0=not active
        $userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
        $userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
        $userInfoName = get_the_author_meta('display_name', B2S_PLUGIN_BLOG_USER_ID);
        $isCheckedShortener = (isset($this->settings->shortener_state) && (int) $this->settings->shortener_state > 0) ? ((int) $this->settings->shortener_state - 1) : -1;
        
        $content = '';
        $content .='<h4>' . esc_html__('Account', 'blog2social') . '</h4>';
        $content .='<div class="form-inline">';
        $content .='<div class="col-xs-12 del-padding-left">';
        $content .='<label class="b2s-user-time-zone-label" for="b2s-user-time-zone">' . esc_html__('Personal Time Zone', 'blog2social') . '</label>';
        $content .=' <select id="b2s-user-time-zone" class="form-control b2s-select" name="b2s-user-time-zone">';
        $content .= B2S_Util::createTimezoneList($userTimeZone);
        $content .= '</select>';
        $content .= ' <a href="#" class="b2s-info-btn hidden-xs b2sInfoTimeZoneModalBtn">' . esc_html__('Info', 'Blog2Social') . '</a>';
        $content .='</div>';
        $content .='<br><div class="b2s-settings-time-zone-info">' . esc_html__('Timezone for Scheduling', 'blog2social') . ' (' . esc_html__('User', 'blog2social') . ': ' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . ') <code id="b2s-user-time">' . esc_html(B2S_Util::getLocalDate($userTimeZoneOffset, substr(B2S_LANGUAGE, 0, 2))) . '</code></span></div>';
        $content .='</div>';
        $content .='<div class="clearfix"></div>';
        $content .='<br>';
        $content .='<hr>';
        $content .='<h4>' . esc_html__('Content', 'blog2social') . '</h4>';
        $content .='<strong>' . esc_html__('Url Shortener', 'blog2social') . '</strong><br>';
        $content .='<div class="alert alert-warning">' . sprintf(__('You can use Bit.ly, Rebrandly or Sniply links to shorten the URL of your links and to track the performance of your links in your social networks. Activate one of the URL shorteners you like to use and link it to your account. Your social media posts will then be shared with your links of Bit.ly, Rebrandly or Sniply. You can then monitor the success of your posts in these accounts. Please note: Some networks do not allow shortlinks. Blog2Social will apply the regular URL for these social platforms. You find more information on the support of URL shortener by the different social platforms in the <a href="%s" target="_blank">link shortener guide</a>.', 'blog2social'), B2S_Tools::getSupportLink('url_shortener_faq')) . '</div>';
        $content .='<input type="radio" value="0" class="b2s-user-network-settings-short-url" id="b2s-user-network-settings-short-url-0" name="b2s-user-network-settings-short-url" ' . (($isCheckedShortener == -1) ? 'checked="checked"' : '') . ' data-provider-id="-1"/><label for="b2s-user-network-settings-short-url-0"> '.esc_html__('no URL Shortener', 'blog2social').'</label>';
        $content .= '<br>';
        $content .= '<input type="hidden" id="brandName" value="'.esc_html__('Brand', 'blog2social').'">';
        $content .= '<input type="hidden" id="campaignName" value="'.esc_html__('Call-to-Action', 'blog2social').'">';
        foreach (unserialize(B2S_PLUGIN_SHORTENER) as $id => $name) {
            $display_name = '';
            if(isset($this->settings->shortener_data) && is_array($this->settings->shortener_data) && !empty($this->settings->shortener_data)) {
                foreach ($this->settings->shortener_data as $shortenerObject) {
                    if(isset($shortenerObject->provider_id) && $shortenerObject->provider_id == $id && isset($shortenerObject->display_name)) {
                        $display_name = esc_html__('Account', 'blog2social') . ': ' . $shortenerObject->display_name;
                        if($shortenerObject->provider_id == 2) { //Sniply
                            $display_name_parts = explode('#SNIP#', $shortenerObject->display_name);
                            if(isset($display_name_parts[0]) && isset($display_name_parts[1])) {
                                $display_name = esc_html__('Brand', 'blog2social') . ': ' . esc_html($display_name_parts[0]) . ' | ' . esc_html__('Call-to-Action', 'blog2social') . ': ' . esc_html($display_name_parts[1]);
                            }
                        }
                    }
                }
            }
            $content .='<input type="radio" value="' . ($id+1) . '" class="b2s-user-network-settings-short-url" id="b2s-user-network-settings-short-url-'.($id+1).'" name="b2s-user-network-settings-short-url" ' . (($isCheckedShortener == $id) ? 'checked="checked"' : '') . ' data-provider-id="'.$id.'" /><label for="b2s-user-network-settings-short-url-'.($id+1).'"> <img class="b2s-shortener-image" alt="'.$name.'" src="' . plugins_url('/assets/images/settings/'. strtolower($name) .'.png', B2S_PLUGIN_FILE) . '"> ' . $name . '</label>';
            $content .='<span class="b2s-user-network-shortener-account-area" data-provider-id="'.$id.'">';
            $content .='<input type="hidden" class="b2s-user-network-shortener-state" data-provider-id="'.$id.'" value="' . ((!empty($display_name)) ? 1 : 0) . '"/>';
            $content .='<span class="b2s-user-network-shortener-connect" data-provider-id="'.$id.'" style="display:' . ((empty($display_name)) ? 'inline-block' : 'none') . ';" ><a href="#" class="b2s-shortener-account-connect-btn" data-provider-id="'.$id.'" onclick="wopShortener(\'' . $this->authUrl . '&provider_id='.$id.'\', \'Blog2Social Network\'); return false;"> ' . esc_html__('authorize', 'blog2social') . '</a> </span>';
            $content .=' <span class="b2s-user-network-shortener-account-detail" data-provider-id="'.$id.'" style="display:' . ((!empty($display_name)) ? 'inline-block' : 'none') . ';">(<span class="b2s-shortener-account-display-name" data-provider-id="'.$id.'">' . (!empty($display_name) ? esc_html($display_name) : '') . '</span> <a href="#" class="b2s-shortener-account-change-btn" data-provider-id="'.$id.'" onclick="wopShortener(\'' . $this->authUrl . '&provider_id='.$id.'\', \'Blog2Social Network\'); return false;">' . esc_html__('change', 'blog2social') . '</a> | <a href="#" class="b2s-shortener-account-delete-btn" data-provider-id="'.$id.'">' . esc_html__('delete', 'blog2social') . '</a>)</span>';
            $content .='</span>';
            $content .= '<br>';
        }
        
        $content .= '<br>';
        $content .= '<strong>' . esc_html__('Shortcodes', 'blog2social') . '</strong> <a href="#" class="b2s-info-btn del-padding-left b2sInfoAllowShortcodeModalBtn">' . esc_html__('Info', 'Blog2Social') . '</a> <br>';
        $content .= '<input type="checkbox" value="' . esc_attr($isCheckedAllowShortcode) . '" id="b2s-user-network-settings-allow-shortcode" ' . (($isCheckedAllowShortcode == 1) ? 'checked="checked"' : '') . ' /> ' . esc_html__('allow shortcodes in my post', 'blog2social');
        $content .='<br>';
        $content .='<br>';
        $content .='<hr>';
        $content .='<h4>' . esc_html__('System', 'blog2social') . '</h4>';
        $content .='<strong>' . esc_html__('This is a global feature for your blog, which can only be edited by users with admin rights.', 'blog2social') . '</strong><br>';
        $content .= '<input type="checkbox" value="' . (($isCheckedLegacyMode == 1) ? 0 : 1) . '" id="b2s-general-settings-legacy-mode" ' . (($isCheckedLegacyMode == 1) ? 'checked="checked"' : '') . ' /><label for="b2s-general-settings-legacy-mode"> ' . esc_html__('activate Legacy mode', 'blog2social') . ' <a href="#" class="b2s-info-btn del-padding-left b2sInfoLegacyModeBtn">' . esc_html__('Info', 'Blog2Social') . '</a></label>';
        return $content;
    }

    public function getSocialMetaDataHtml() {

        $og = $this->generalOptions->_getOption('og_active');
        $card = $this->generalOptions->_getOption('card_active');
        $oembed = $this->generalOptions->_getOption('oembed_active');
        //$user_meta_author_data = $this->options->_getOption('meta_author_data');
        $og_isChecked = ($og !== false && $og == 1) ? 0 : 1;
        $card_isChecked = ($card !== false && $card == 1) ? 0 : 1;
        $oembed_isChecked = ($oembed === false || $oembed == 1) ? 0 : 1;
        $selectCardType = $this->generalOptions->_getOption('card_default_type');
        $readonly = (B2S_PLUGIN_ADMIN) ? false : true;

        $content = '<div class="col-md-12">';
        if (B2S_PLUGIN_ADMIN) {
            $content .= '<a href="#" class="pull-right btn btn-primary btn-xs b2sClearSocialMetaTags">' . esc_html__('Reset all page and post meta data', 'blog2social') . '</a>';
        }
        $content .='<strong>' . esc_html__('This is a global feature for your blog, which can only be edited by users with admin rights.', 'blog2social') . '</strong>';
        $content .= '<br>';
        $content .='<div class="' . ( (B2S_PLUGIN_ADMIN) ? "" : "b2s-disabled-div") . '">';
        $content .='<h4>' . esc_html__('Meta Tags Settings for Posts and Pages', 'blog2social') . '</h4>';
        $content .= '<input type="checkbox" value="' . $og_isChecked . '" name="b2s_og_active" ' . (($readonly) ? 'disabled="true"' : "") . '  id="b2s_og_active" ' . (($og_isChecked == 0) ? 'checked="checked"' : '') . ' /><label for="b2s_og_active"> ' . esc_html__('Add Open Graph meta tags to your shared posts or pages, required by Facebook and other social networks to display your post or page image, title and description correctly.', 'blog2social', 'blog2social') . ' <a href="#" class="b2s-load-info-meta-tag-modal b2s-info-btn del-padding-left" data-meta-type="og" data-meta-origin="settings">' . esc_html__('Info', 'Blog2Social') . '</a></label>';
        $content .='<br>';
        $content .= '<input type="checkbox" value="' . $card_isChecked . '" name="b2s_card_active" ' . (($readonly) ? 'disabled="true"' : "") . ' id="b2s_card_active" ' . (($card_isChecked == 0) ? 'checked="checked"' : '') . ' /><label for="b2s_card_active"> ' . esc_html__('Add Twitter Card meta tags to your shared posts or pages, required by Twitter to display your post or page image, title and description correctly.', 'blog2social', 'blog2social') . ' <a href="#" class="b2s-load-info-meta-tag-modal b2s-info-btn del-padding-left" data-meta-type="card" data-meta-origin="settings">' . esc_html__('Info', 'Blog2Social') . '</a></label>';
        $content .='<br>';
        $content .= '<input type="checkbox" value="' . $oembed_isChecked . '" name="b2s_oembed_active" ' . (($readonly) ? 'disabled="true"' : "") . ' id="b2s_oembed_active" ' . (($oembed_isChecked == 0) ? 'checked="checked"' : '') . ' /><label for="b2s_oEmbed_active"> ' . esc_html__('Add oEmbed tags', 'blog2social', 'blog2social') . ' <a href="#" class="b2s-load-info-meta-tag-modal b2s-info-btn del-padding-left" data-meta-type="oEmbed" data-meta-origin="settings">' . esc_html__('Info', 'Blog2Social') . '</a></label>';
        $content .='</div>';
        $content .='<button class="btn btn-primary pull-right" type="submit" '.(B2S_PLUGIN_ADMIN ? '' : 'disabled="true"').'>'.esc_html__('save', 'blog2social').'</button>';
        $content .='<div class="clearfix"></div><hr>';

        /* $content .='<h4>' . __('Authorship Settings', 'blog2social');
          if (B2S_PLUGIN_USER_VERSION < 1) {
          $content .=' <span class="label label-success label-sm"><a href="#" class="btn-label-premium b2sPreFeatureModalBtn">' . __("PREMIUM", "blog2social") . '</a></span>';
          }
          $content .='</h4>';
          $content .='<div class="' . ( (B2S_PLUGIN_USER_VERSION >= 1) ? "" : "b2s-disabled-div") . '">';
          $content .='<p>' . __('Add authorship tags to your articles. When somesone shares your links on Facebook or Twitter, you will be automatically linked as the author.', 'blog2social') . '</p>';
          $content .='<div class="col-md-8">';
          $content .='<div class="form-group"><label for="b2s_og_article_author"><img alt="" class="b2s-post-item-network-image" src="' . plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE) . '"> <strong>' . __("Facebook author link", "blog2social") . ':</strong></label><input type="text" placeholder="' . __("Enter your Facebook link profile here (e.g. https://www.facebook.com/Blog2Social/)", "blog2social") . '" ' . ((B2S_PLUGIN_USER_VERSION >= 1) ? "" : "readonly") . ' value="' . (($user_meta_author_data !== false && isset($user_meta_author_data['og_article_author'])) ? $user_meta_author_data['og_article_author'] : "") . '" name="b2s_og_article_author" class="form-control" id="b2s_og_article_author"></div>';
          $content .='<div class="form-group"><label for="b2s_card_twitter_creator"><img alt="" class="b2s-post-item-network-image" src="' . plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE) . '"> <strong>' . __("Twitter Username", "blog2social") . ':</strong></label><input type="text" placeholder="' . __("Enter your Twitter Username here (e.g. @blog2social)", "blog2social") . '" ' . ((B2S_PLUGIN_USER_VERSION >= 1) ? "" : "readonly") . ' value="' . (($user_meta_author_data !== false && isset($user_meta_author_data['card_twitter_creator'])) ? $user_meta_author_data['card_twitter_creator'] : "") . '" name="b2s_card_twitter_creator" class="form-control" id="b2s_card_twitter_creator"></div>';
          $content .='</div>';
          $content .='</div>';
          $content .='<div class="clearfix"></div>';
          $content .='<hr>'; */

        
        $content .='<strong>' . esc_html__('This is a global feature for your blog, which can only be edited by users with admin rights.', 'blog2social') . '</strong>';
        $content .='<div class="' . ( (B2S_PLUGIN_ADMIN) ? "" : "b2s-disabled-div") . '">';
        $content .='<h4>' . esc_html__('Frontpage Settings', 'blog2social');
        if (B2S_PLUGIN_USER_VERSION >= 1) {
            $content .=' <a class="btn-link b2s-btn-link-txt" href="admin.php?page=blog2social-support#b2s-support-sharing-debugger">' . esc_html__("Check Settings with Sharing-Debugger", "blog2social") . '</a>';
        } else {
            if (B2S_PLUGIN_ADMIN) {
                $content .=' <span class="label label-success label-sm">' . esc_html__("SMART", "blog2social") . '</span>';
            }
        }
        $readonly = (B2S_PLUGIN_USER_VERSION >= 1) ? false : true;
        $content .='</h4>';
        $content .='<div class="' . ( (B2S_PLUGIN_USER_VERSION >= 1) ? "" : "b2s-disabled-div") . '">';
        $content .='<div><b>Open Graph</b></div>';
        $content .= '<p>' . esc_html__('Add the default Open Graph parameters for title, description and image you want Facebook to display, if you share the frontpage of your blog as link post (http://www.yourblog.com)', 'blog2social') . '</p>';
        $content .='<br>';
        $content .='<div class="col-md-8">';
        $content .='<div class="form-group"><label for="b2s_og_default_title"><strong>' . esc_html__("Title", "blog2social") . ':</strong></label><input type="text" ' . (($readonly) ? "readonly" : "") . ' value="' . esc_attr(( ($this->generalOptions->_getOption('og_default_title') !== false && !empty($this->generalOptions->_getOption('og_default_title'))) ? stripslashes($this->generalOptions->_getOption('og_default_title')) : get_bloginfo('name'))) . '" name="b2s_og_default_title" class="form-control" id="b2s_og_default_title"></div>';
        $content .='<div class="form-group"><label for="b2s_og_default_desc"><strong>' . esc_html__("Description", "blog2social") . ':</strong></label><input type="text" ' . (($readonly) ? "readonly" : "") . ' value="' . esc_attr(( ($this->generalOptions->_getOption('og_default_desc') !== false && !empty($this->generalOptions->_getOption('og_default_desc'))) ? stripslashes($this->generalOptions->_getOption('og_default_desc')) : get_bloginfo('description'))) . '" name="b2s_og_default_desc" class="form-control" id="b2s_og_default_desc"></div>';
        $content .='<div class="form-group"><label for="b2s_og_default_image"><strong>' . esc_html__("Image URL", "blog2social") . ':</strong></label>';
        if (!$readonly) {
            $content .='<button class="btn btn-link btn-xs b2s-upload-image pull-right" data-id="b2s_og_default_image">' . esc_html__("Image upload / Media Gallery", "blog2social") . '</button>';
        }
        $content .='<input type="text" ' . (($readonly) ? "readonly" : "") . ' value="' . esc_attr((($this->generalOptions->_getOption('og_default_image') !== false && !empty($this->generalOptions->_getOption('og_default_image'))) ? $this->generalOptions->_getOption('og_default_image') : '')) . '" name="b2s_og_default_image" class="form-control" id="b2s_og_default_image">';
        $content .='<span>' . esc_html__('Please note: Facebook supports images with a minimum dimension of 200x200 pixels and an aspect ratio of 1:1.', 'blog2social') . '</span>';
        $content .='</div>';
        $content .='</div>';
        $content .='<div class="clearfix"></div>';
        $content .='<br>';
        $content .='<div><b>Twitter Card</b></div>';
        $content .='<p>' . esc_html__('Add the default Twitter Card parameters for title, description and image you want Twitter to display, if you share the frontpage of your blog as link post (http://www.yourblog.com)', 'blog2social') . '</p>';
        $content .='<br>';
        $content .='<div class="col-md-8">';
        $content .='<div class="form-group"><label for="b2s_card_default_card_type"><strong>' . esc_html__("The default card type to use", "blog2social") . ':</strong></label>';
        $content .='<select class="form-control" name="b2s_card_default_type" ' . (($readonly) ? 'disabled="true"' : "") . '>';
        $content .='<option ' . (($selectCardType === false || $selectCardType == 0 || B2S_PLUGIN_USER_VERSION < 1) ? 'selected"' : '') . ' value="0">' . esc_html__('Summary', 'blog2social') . '</option>';
        $content .='<option ' . (($selectCardType !== false && $selectCardType == 1 && B2S_PLUGIN_USER_VERSION >= 1) ? 'selected' : '') . ' value="1">' . esc_html__('Summary with large image', 'blog2social') . ' ' . ((B2S_PLUGIN_USER_VERSION < 1) ? esc_html__('(SMART)', 'blog2social') : '') . '</option>';
        $content .='</select></div>';
        $content .='<div class="form-group"><label for="b2s_card_default_title"><strong>' . esc_html__("Title", "blog2social") . ':</strong></label><input type="text" ' . (($readonly) ? "readonly" : "") . ' value="' . esc_attr(( ($this->generalOptions->_getOption('card_default_title') !== false && !empty($this->generalOptions->_getOption('card_default_title'))) ? stripslashes($this->generalOptions->_getOption('card_default_title')) : get_bloginfo('name'))) . '" name="b2s_card_default_title" class="form-control" id="b2s_card_default_title"></div>';
        $content .='<div class="form-group"><label for="b2s_card_default_desc"><strong>' . esc_html__("Description", "blog2social") . ':</strong></label><input type="text" ' . (($readonly) ? "readonly" : "") . ' value="' . esc_attr(( ($this->generalOptions->_getOption('card_default_desc') !== false && !empty($this->generalOptions->_getOption('card_default_desc'))) ? stripslashes($this->generalOptions->_getOption('card_default_desc')) : get_bloginfo('description'))) . '" name="b2s_card_default_desc" class="form-control" id="b2s_card_default_desc"></div>';
        $content .='<div class="form-group"><label for="b2s_card_default_image"><strong>' . esc_html__("Image URL", "blog2social") . ':</strong></label> ';
        if (!$readonly) {
            $content .='<button class="btn btn-link btn-xs pull-right b2s-upload-image" data-id="b2s_card_default_image">' . esc_html__("Image upload / Media Gallery", "blog2social") . '</button>';
        }
        $content .='<input type="text" ' . (($readonly) ? "readonly" : "") . ' value="' . esc_attr((($this->generalOptions->_getOption('card_default_image') !== false && !empty($this->generalOptions->_getOption('card_default_image'))) ? $this->generalOptions->_getOption('card_default_image') : '')) . '" name="b2s_card_default_image" class="form-control" id="b2s_card_default_image">';
        $content .='<span>' . esc_html__('Please note: Twitter supports images with a minimum dimension of 144x144 pixels and a maximum dimension of 4096x4096 pixels and less than 5 BM. The image will be cropped to a square. Twitter supports JPG, PNG, WEBP and GIF formats.', 'blog2social') . '</span>';
        $content .='</div>';
        $content .='</div>';
        $content .='</div>';
        $content .='</div>';
        $content .='</div>';

        return $content;
    }

    public function getNetworkSettingsHtml() {
        $optionPostFormat = $this->options->_getOption('post_template');
        $defaultPostFormat = unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT);
        $content = '';
        $networkName = unserialize(B2S_PLUGIN_NETWORK);

        if (B2S_PLUGIN_USER_VERSION < 2) {
            $content .='<div class="alert alert-default">';
            $content .= '<b>' . esc_html__('Did you know?', 'blog2social') . '</b><br>';
            $content .= esc_html__('With Premium Pro, you can change the custom post format photo post or link post for each individual social media post and channel (profile, page, group).', 'blog2social') . ' <a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '">' . esc_html__('Upgrade to Premium Pro now.', 'blog2social') . '</a>';
            $content .='<hr></div>';
        }

        foreach (array(1, 2, 3, 12, 19, 17) as $n => $networkId) { //FB,TW,LI,IN
            $type = ($networkId == 1 || $networkId == 19 || $networkId == 17) ? array(0, 1, 2) : (($networkId == 3 || $networkId == 12) ? array(0, 1) : array(0));
            foreach ($type as $t => $typeId) { //Profile,Page,Group
                if($networkId == 17) {
                    $postFormat = 1;
                } else {
                    if (!isset($optionPostFormat[$networkId][$typeId]['format'])) {
                        $postFormat = $defaultPostFormat[$networkId][$typeId]['format'];
                    } else {
                        $postFormat = $optionPostFormat[$networkId][$typeId]['format'];
                    }
                }

                $post_format_0 = (((int) $postFormat == 0) ? 'b2s-settings-checked' : '' ); //LinkPost
                $post_format_1 = empty($post_format_0) ? 'b2s-settings-checked' : ''; //PhotoPost
                $postFormatType = ($networkId == 12) ? 'image' : 'post';

                $content .='<div class="b2s-user-network-settings-post-format-area col-md-12" data-post-format-type="' . esc_attr($postFormatType) . '" data-network-type="' . esc_attr($typeId) . '"  data-network-id="' . esc_attr($networkId) . '" data-network-title="' . esc_attr($networkName[$networkId]) . '" style="display:none;" >';
                $content .='<div class="col-md-6 col-xs-12">';
                $content .= '<b>1) ' . (($networkId == 12) ? esc_html__('Image with frame', 'blog2social') : esc_html__('Link Post', 'blog2social') . ' <span class="glyphicon glyphicon-link b2s-color-green"></span>' ) . '</b><br><br>';
                $content .= '<label><input type="radio" name="b2s-user-network-settings-post-format-' . $networkId . '" class="b2s-user-network-settings-post-format ' . esc_attr($post_format_0) . '" data-post-wp-type="" data-post-format-type="' . esc_attr($postFormatType) . '" data-network-type="' . esc_attr($typeId) . '" data-network-id="' . esc_attr($networkId) . '" data-post-format="0" value="0"/><img class="img-responsive b2s-display-inline" src="' . plugins_url('/assets/images/settings/b2s-post-format-' . $networkId . '-1-' . (($this->lang == 'de') ? $this->lang : 'en') . '.png', B2S_PLUGIN_FILE) . '">';
                $content .='</label>';
                $content .='<br><br>';
                if($networkId != 12) {
                    $content .= '<div class="alert alert-warning b2s-select-link-chang-image">'.esc_html__('The image will be changed', 'blog2social').'</div>';
                }
                if ($networkId == 12) {
                    $content .= esc_html__('Insert white frames to show the whole image in your timeline. All image information will be shown in your timeline.', 'blog2social');
                } else {
                    $content .= esc_html__('The link post format displays posts title, link address and the first one or two sentences of the post. The networks scan this information from your META or OpenGraph.  PLEASE NOTE: If you want your link posts to display the selected image from the Blog2Social preview editor, please make sure you have activated the Social Meta Tags for Facebook and Twitter in your Blog2Social settings. You find these settings in the tab "Social Meta Data". If you don\'t select a specific post image, some networks display the first image detected on your page. The image links to your blog post.', 'blog2social');
                }
                $content .='</div>';
                $content .='<div class="col-md-6 col-xs-12">';
                $content .= '<b>2) ' . (($networkId == 12) ? esc_html__('Image cut out', 'blog2social') : esc_html__('Image Post', 'blog2social') . ' <span class="glyphicon glyphicon-picture b2s-color-green"></span>' ) . '</b><br><br>';
                $content .= '<label><input type="radio" name="b2s-user-network-settings-post-format-' . $networkId . '" class="b2s-user-network-settings-post-format ' . esc_attr($post_format_1) . '" data-post-wp-type="" data-post-format-type="' . esc_attr($postFormatType) . '" data-network-type="' . esc_attr($typeId) . '" data-network-id="' . esc_attr($networkId) . '" data-post-format="1" value="1" /><img class="img-responsive b2s-display-inline" src="' . plugins_url('/assets/images/settings/b2s-post-format-' . $networkId . '-2-' . (($this->lang == 'de') ? $this->lang : 'en') . '.png', B2S_PLUGIN_FILE) . '">';
                $content .='</label>';
                $content .='<br><br>';
                if ($networkId == 12) {
                    $content .= esc_html__('The image preview will be cropped automatically to fit the default Instagram layout for your Instagram timeline. The image will be shown uncropped when opening the preview page for your Instagram post.', 'blog2social');
                } else {
                    $content .= esc_html__('A photo or image post displays the selected image in the one-page preview of Blog2Social and your comment above the image. The image links to the image view on your image gallery in the respective network. Blog2Social adds the link to your post in your comment. The main benefit of photo posts is that your image is uploaded to your personal image albums or gallery. In Facebook, you can edit the albums name with a description of your choice.', 'blog2social');
                }
                $content .='</div>';
                $content .='</div>';
            }
        }
        return $content;
    }

//view=ship
    public function setNetworkSettingsHtml() {
        $defaultTemplate = false;
        if(defined('B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT')) {
            $defaultTemplate = unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT);
        }
        $optionPostFormat = $this->options->_getOption('post_template');
        $content = "<input type='hidden' class='b2sNetworkSettingsPostFormatText' value='" . json_encode(array('post' => array(__('Link Post', 'blog2social'), __('Image Post', 'blog2social')), 'image' => array(__('Image with frame'), __('Image cut out')))) . "'/>";
        foreach (array(1, 2, 3, 12, 19, 15, 17) as $n => $networkId) { //FB,TW,LI,IN
            $postFormatType = ($networkId == 12) ? 'image' : 'post';
            $type = ($networkId == 1 || $networkId == 19 || $networkId == 17) ? array(0, 1, 2) : (($networkId == 3 || $networkId == 12) ? array(0, 1) : array(0));
            foreach ($type as $t => $typeId) { //Profile,Page,Group                
                if (!isset($optionPostFormat[$networkId][$typeId]['format']) || (int) $optionPostFormat[$networkId][$typeId]['format'] < 0 || (int) $optionPostFormat[$networkId][$typeId]['format'] > 1) { //DEFAULT
                    if(is_array($defaultTemplate) && isset($defaultTemplate[$networkId][$typeId]['format']) && $defaultTemplate[$networkId][$typeId]['format'] >= 0 && $defaultTemplate[$networkId][$typeId]['format'] <= 1) {
                        $value = $defaultTemplate[$networkId][$typeId]['format'];
                    } else {
                        $value = ($networkId == 2) ? 1 : 0;  //default see B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT
                    }
                } else {
                    $value = $optionPostFormat[$networkId][$typeId]['format'];
                }
                $content .= "<input type='hidden' class='b2sNetworkSettingsPostFormatCurrent' data-post-format-type='" . esc_attr($postFormatType) . "' data-network-id='" . esc_attr($networkId) . "' data-network-type='" . esc_attr($typeId) . "' value='" . (int) esc_attr($value) . "' />";
            }
        }
        return $content;
    }

}
