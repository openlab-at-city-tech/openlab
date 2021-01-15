<?php

class B2S_Ship_Navbar {

    private $neworkName;
    private $networkTypeName;
    private $networkTypeData;
    private $networkKindName;
    private $authUrl;
    private $allowProfil;
    private $allowPage;
    private $allowGroup;
    private $oAuthPortal;

    public function __construct() {
        $this->neworkName = unserialize(B2S_PLUGIN_NETWORK);
        $this->networkTypeName = unserialize(B2S_PLUGIN_NETWORK_TYPE);
        $this->networkKindName = unserialize(B2S_PLUGIN_NETWORK_KIND);
        $hostUrl = (function_exists('rest_url')) ? rest_url() : get_site_url();
        $this->authUrl = B2S_PLUGIN_API_ENDPOINT_AUTH . '?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2) . '&unset=true&hostUrl='.$hostUrl;
        $this->allowProfil = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PROFILE);
        $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
        $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
        $this->oAuthPortal = unserialize(B2S_PLUGIN_NETWORK_OAUTH);
        $this->networkTypeData=array('profile','page', 'group');
    }

    public function getData() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getUserAuth', 'token' => B2S_PLUGIN_TOKEN,'version'=>B2S_PLUGIN_VERSION)));
        return array('mandanten' => isset($result->mandanten) ? $result->mandanten : '',
            'auth' => isset($result->auth) ? $result->auth : '',
            'portale' => isset($result->portale) ? $result->portale : '');
    }

    public function getSelectMandantHtml($data) {
        $select = '<select class="form-control b2s-network-details-mandant-select b2s-select">';
        $select .= '<option value="0" selected="selected">' . esc_html__('My Profile', 'blog2social') . '</option>';
        if (!empty($data)) {
            foreach ($data as $id => $name) {
                $select .= '<option value="' . esc_attr($id) . '">' . esc_html($name) . '</option>';
            }
        }
        $select .= '</select>';
        return $select;
    }

    public function getItemHtml($data, $draftData = array()) {
        $username = stripslashes($data->networkUserName);
        $b2sAuthUrl = $this->authUrl . '&portal_id=' . $data->networkId . '&transfer=' . (in_array($data->networkId, $this->oAuthPortal) ? 'oauth' : 'form' ) . '&mandant_id=' . $data->mandantId . '&version=3&affiliate_id='.B2S_Tools::getAffiliateId();
        
        $chooseData = isset($this->networkTypeData[$data->networkType]) ?  $this->networkTypeData[$data->networkType] : 'profile';
        
        $onclick = ($data->expiredDate != '0000-00-00' && $data->expiredDate <= date('Y-m-d')) ? ' onclick="wop(\'' . $b2sAuthUrl . '&choose='.$chooseData.'&update=' . $data->networkAuthId . '\', \'Blog2Social Network\'); return false;"' : '';

        $mandantIds = array();
        if(empty($draftData)){
            global $wpdb;
            $mandantCount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(mandant_id)FROM {$wpdb->prefix}b2s_user_network_settings  WHERE mandant_id =%d AND blog_user_id=%d ", $data->mandantId, B2S_PLUGIN_BLOG_USER_ID));
            $userSelected = $wpdb->get_results($wpdb->prepare("SELECT mandant_id FROM {$wpdb->prefix}b2s_user_network_settings WHERE blog_user_id =%d AND network_auth_id = %d", B2S_PLUGIN_BLOG_USER_ID, $data->networkAuthId));
            
            foreach ($userSelected as $key => $value) {
                $mandantIds[] = $value->mandant_id;
            }

            if ($mandantCount == 0) {
                $mandantIds[] = $data->mandantId;
            }
        } else {
            if (array_key_exists($data->networkAuthId, $draftData['b2s'])){
                $mandantIds[] = "0";
            }
        }
        //Bug: Converting json + PHP Extension
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            $username = (mb_strlen($username,'UTF-8') >= 29 ? (mb_substr($username, 0, 26,'UTF-8') . '...') : $username);
        }

        $content = '<li class="b2s-sidbar-wrapper-nav-li i" data-mandant-id=\'' . json_encode($mandantIds) . '\' data-mandant-default-id="' . esc_attr($data->mandantId) . '">';
        $content .= '<div class="b2s-network-select-btn ' . (($data->expiredDate != '0000-00-00' && $data->expiredDate <= date('Y-m-d')) ? 'b2s-network-select-btn-deactivate" ' . $onclick : '"') . ' data-instant-sharing="'.esc_attr((isset($data->instant_sharing) ? (int)$data->instant_sharing : 0)).'" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-network-type="' . esc_attr($data->networkType) . '" data-network-kind="' . esc_attr($data->networkKind) . '" data-network-id = "' . esc_attr($data->networkId) . '"  data-network-tos-group-id="'.esc_attr($data->networkTosGroupId).'" data-network-display-name="' . esc_attr(strtolower($data->networkUserName)) . '" ' . (in_array($data->networkId, array(1, 3, 15, 19, 17)) ? 'data-meta-type="og"' : (in_array($data->networkId, array(2)) ? 'data-meta-type="card"' : '')) . '>'; 
        $content .= '<div class="b2s-network-list">';
        $content .= '<div class="b2s-network-thumb">';
        $content .= '<img alt="" src="' . esc_url(plugins_url('/assets/images/portale/' . $data->networkId . '_flat.png', B2S_PLUGIN_FILE)) . '">';
        $content .= '</div>';
        $content .= '<div class="b2s-network-details">';
        $content .= '<h4>' . esc_html($username) . '</h4>';
        $content .= '<p>' .esc_html(($data->networkId == 19 && $data->networkType == 1 && isset($this->networkKindName[$data->networkKind]) ? $this->networkKindName[$data->networkKind] : (($data->networkId == 4) ? esc_html__('Blog', 'blog2social') : $this->networkTypeName[$data->networkType])) . ' | ' . $this->neworkName[$data->networkId]) . '</p>';
        $content .= '</div>';
        $content .= '<div class="b2s-network-status" data-network-auth-id="' . esc_attr($data->networkAuthId) . '">';
        $content .= '<span class="b2s-network-hide b2s-network-status-img glyphicon glyphicon-ok glyphicon-success"></span>';
        $content .= '<span class="b2s-network-status-no-img glyphicon glyphicon-danger glyphicon-ban-circle" data-network-auth-id="' . esc_attr($data->networkAuthId) . '" data-network-id="' . esc_attr($data->networkId) . '" style="display:none"></span>';
        $content .= ($data->expiredDate != '0000-00-00' && $data->expiredDate <= date('Y-m-d')) ? '<span class="b2s-network-status-expiredDate glyphicon glyphicon-danger glyphicon-refresh" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"></span>' : '';
        $content .= '<div style="display:none;" class="b2s-network-status-img-loading b2s-loader-impulse b2s-loader-impulse-sm" data-network-auth-id="' . esc_attr($data->networkAuthId) . '"></div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</li>';

        return $content;
    }

}
