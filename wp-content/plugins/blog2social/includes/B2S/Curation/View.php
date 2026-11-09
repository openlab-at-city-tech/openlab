<?php

/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
 */

class B2S_Curation_View {

    public function __construct() {
        
    }

    public function getCurationPreviewHtml($url = '', $data = array()) {

        $image = plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE);
        $externalImage = false;
        if (isset($data['og_image'])) {
            $image = $data['og_image'];
            $externalImage = true;
        }
        $title = (isset($data['og_title']) &&!empty(trim($data['og_title']))) ? $data['og_title'] : ((isset($data['default_title']) && !empty(trim($data['default_title']))) ? $data['default_title'] : '');
        $desc = (isset($data['og_description']) &&!empty(trim($data['og_description']))) ? $data['og_description'] : ((isset($data['default_description']) && !empty(trim($data['default_description']))) ? $data['default_description'] : '');

        $html = '';
        $html .='<div class="row">';
        $html .='<div class="b2s-post-item-details-item-message-area">';

        $html .='<input type="hidden" id="b2s-post-curation-comment" name="comment"  value="'.esc_attr($desc).'">';
        $html .='</div>';
        $html .='</div>';
        $html .='</br>';
        $html .='<div class="row">';
        $html .='<div class="panel panel-default" style="display: none;">'; //Since new preview, url preview is hidden
        $html .='<div class="panel-body">';
        $html .='<div class="col-xs-12 col-sm-5 col-lg-3">';
        $html .='<img src="' . esc_url($image) . '" class="center-block img-responsive" style="display: block;">';
        $html .='<div class="clearfix"></div>';
        $html .='</div>';
        $html .='<div class="col-xs-12 b2s-post-original-area col-sm-7 col-lg-9">';
        $html .='<p class="b2s-post-item-details-preview-title">' . esc_html($title) . '</p>';
        $html .='<input type="hidden" id="b2s-post-curation-preview-title" class="form-control" value="' . esc_attr(addslashes($title)) . '" placeholder="'.esc_attr__('Title', 'blog2social').'">';
        $html .='<span class="b2s-post-item-details-preview-desc">' . esc_html($desc) . '</span>';
        $html .='<br>';
        $html .='<span class="b2s-post-item-details-preview-url"><a href="' . esc_url($url) . '" target="_blank" class="b2s-post-item-details-preview-url-link btn btn-link del-padding-left b2s-break-word">' . esc_url($url) . '</a></span>';
        $html .='<input type="hidden" id="b2s-post-curation-url" name="url" value="' . esc_url($url) . '">';
        $html .='<span class="glyphicon glyphicon-pencil b2s-btn-change-url-preview"></span>';
        $html .='</div>';
        $html .='</div>';
        $html .='</div>';
        $html .='</div>';
        $html .='</div>';
        return $html;
    }

    public function getShippingDetails($mandant = array(), $auth = array(), $isVideo = false) {
        //Opt: CustomDatePicker
        $dateFormat = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? 'dd.mm.yyyy' : 'yyyy-mm-dd';
        $timeFormat = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? 'hh:ii' : 'hh:ii aa';
        $isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? ' [' . esc_html__("SMART", "blog2social") . ']' : '';

        $authContent = '';
        $content = '<br>';
        $content .='<div class="row">';
        $content .='<div class="col-xs-12 col-sm-6 col-lg-4">';
        $content .='<label for="b2s-curation-ship-type">' . esc_html__('Share your post', 'blog2social') . '</label>';
        $content .='<select style="width:100%;" id="b2s-post-curation-ship-type" class="b2s-select" data-user-version="' . B2S_PLUGIN_USER_VERSION . '" name="ship_type">';
        $content .='<option value="0">' . esc_html__('immediately', 'blog2social') . '</option>';
        $content .='<option value="1">' . esc_html__('at scheduled times', 'blog2social') . ' ' . $isPremium . '</option>';
        $content .= '</select>';
        $content .='</div>';
        $content .='<div class="col-xs-12 col-sm-6 col-lg-4" style="display:none;">';
        $content .='<label for="b2s-curation-profile-select">' . esc_html__('Select network collection:', 'blog2social') . '</label><a class="pull-right b2s-network-info-modal-btn" href="#">' . esc_html__('Info', 'blog2social') . '</a>';
        $content .='<select style="width:100%;" id="b2s-post-curation-profile-select" class="b2s-curation-select" name="profile_select">';
        foreach ($mandant as $k => $m) {
            $content .= '<option value="' . esc_attr($m->id) . '">' . esc_html($m->name) . '</option>';
            $profilData = (isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0])) ? json_encode($auth->{$m->id}) : '';
            $authContent .= "<input type='hidden' id='b2s-post-curation-profile-data-" . esc_attr($m->id) . "' name='profile_data_" . esc_attr($m->id) . "' value='" . base64_encode($profilData) . "'/>";
        }
        $content .= '</select>';
        $content .='</div>';
        
        $content .= $authContent;

        //TOS Twitter 032018 - none multiple Accounts - User select once
        $twitterContent = '';
        foreach ($mandant as $k => $m) {
            if ((isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0]))) {
                foreach ($auth->{$m->id} as $key => $value) {
                    if ($value->networkId == 2 || $value->networkId == 45) {
                        $twitterContent .= '<option data-mandant-id="' . esc_attr($m->id) . '" value="' . esc_attr($value->networkAuthId) . '">' . esc_html($value->networkUserName) . '</option>';
                    }
                }
            }
        }
        if (!empty($twitterContent)) {

            $content .='<div class="b2s-curation-twitter-area" id="b2s-network-group-wrap-twitter">';
            $content .= '<img class="hidden-xs b2s-img-network-x-icon" alt="' . esc_attr('X') . '" src="' . esc_url(plugins_url('/assets/images/portale/45_flat.png', B2S_PLUGIN_FILE)) . '">';
            $content .='<select style="width:100%;" id="b2s-post-curation-twitter-select" class="form-control b2s-preview-network-group-select" name="twitter_select">';
            $content .=$twitterContent;
            $content .= '</select>';
            $content .= '<i class="glyphicon glyphicon-chevron-down b2s-compose-settings-toggle-icon select-chevron "></i>';
            $content .='</div>';
        }
        if ($isVideo) {
            $content .= '<div class="col-xs-12 col-sm-6 col-lg-4 b2s-video-network-col">';
            $content .= '<label for="b2s-curation-preview-profile-select">' . esc_html__('Select network collection:', 'blog2social') . '</label>';
            $content .= '<select style="width:100%;" id="b2s-curation-preview-profile-select" class="form-control b2s-preview-network-group-select' . ($isVideo ? '-video' : '') . '" name="preview_profile_select">';
            foreach ($mandant as $k => $m) {
                $content .= '<option value="' . esc_attr($m->id) . '">' . esc_html($m->name) . '</option>';
            }
            $content .= '</select>';
            $content .= '</div>';
        }
        $content .='</div>';
        $content .='<div class="b2s-curation-post-form-apply-post-templates" style="display:none;">';
        $content .='<input class="b2s-curation-post-form-apply-post-templates-checkbox" value="1" name="apply_post_templates" type="checkbox"><label>' . esc_html__('Apply Post Templates', 'blog2social') . '</label>';
        $content .='</div>';
        $content .='<br>';
        $content .='<div class="row">';
        $content .='<div class="b2s-post-curation-ship-date-area">';
        $content .='<input type="hidden" name="ship_date" id="b2s-post-curation-ship-date" class="b2s-post-curation-ship-date" disabled="disabled" data-language="' . esc_attr(substr(B2S_LANGUAGE, 0, 2)) . '" data-date-format="' . esc_attr($dateFormat) . '" data-time-format="' . esc_attr($timeFormat) . '">';
        $content .='<div class="b2s-ship-date-row">';
        $content .='<div class="col-xs-7 del-padding-right" style="width: 150px;">';
        $content .='<label for="b2s-post-curation-ship-date-date">' . esc_html__('Date', 'blog2social') . '</label>';
        $content .='<input type="text" placeholder="' . esc_html__('Date', 'blog2social') . '" id="b2s-post-curation-ship-date-date" class="b2s-post-curation-ship-date form-control b2s-input" disabled="disabled" readonly>';
        $content .='</div>';
        $content .='<div class="col-xs-5" style="width:150px;">';
        $content .='<label for="b2s-post-curation-ship-date-time">' . esc_html__('Time', 'blog2social') . '</label>';
        $content .='<input type="text" placeholder="' . esc_html__('Time', 'blog2social') . '" id="b2s-post-curation-ship-date-time" class="b2s-post-curation-ship-date form-control b2s-input" disabled="disabled" readonly>';
        $content .='</div>';
        $content .='<button type="button" class="btn btn-default btn-sm b2s-show-calendar-btn" style="align-self:flex-end;margin-bottom:13px;"><i class="glyphicon glyphicon-calendar"></i> ' . esc_html__('Show calendar', 'blog2social') . '</button>';
        $content .='</div>';
        $content .='</div>';
        $content .='</div>';
        
        $content .= '<br>';
        $content .='<hr>';
        $content .='<input type="hidden" class="b2s-post-curation-action" name="action" value="">';
        $content .='</div>';

        return $content;
    }

    public function getResultListHtml($data = array()) {
        $networkName = unserialize(B2S_PLUGIN_NETWORK);
        $networkTypeName = unserialize(B2S_PLUGIN_NETWORK_TYPE);
        $html = '';
        foreach ($data as $k => $v) {
            $html.='<div class="b2s-post-item">
                <div class="panel panel-group">
                <div class="panel-body">
                <div class="b2s-post-item-area">
                <div class="b2s-post-item-thumb hidden-xs">
                <img alt="" class="img-responsive b2s-post-item-network-image" src="' . esc_url(plugins_url('/assets/images/portale/' . $v['networkId'] . '_flat.png', B2S_PLUGIN_FILE)) . '">
                </div>
                <div class="b2s-post-item-details">
                <h4 class="pull-left b2s-post-item-details-network-display-name">' . esc_html($v['networkDisplayName']) . '</h4>
                <div class="clearfix"></div>
                <p class="pull-left">' . esc_html($networkTypeName[$v['networkType']]) . ' | ' . esc_html($networkName[$v['networkId']]) . '</p>
                <div class="b2s-post-item-details-message-result" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '">' . $v['html'] . '</div>
                </div>
                </div>
                </div>
                </div>
                </div>';
        }
        return $html;
    }

}
