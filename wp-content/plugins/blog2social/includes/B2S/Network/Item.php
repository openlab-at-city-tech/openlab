<?php

/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
 */

class B2S_Network_Item {

    private $authurl;
    private $allowProfil;
    private $allowPage;
    private $allowGroup;
    private $modifyBoardAndGroup;
    private $networkKindName;
    private $oAuthPortal;
    private $mandantenId;
    private $bestTimeInfo;
    private $lang;
    private $options;
    private $userSchedData; // >5.1.0
    private $userSchedDataOld; // <5.1.0
    private $previewImage;
    private $addon_count;
    private $isVideoSupported;
    private $isSocialSupported;
    private $networkTypeName;
    private $networkTypeNameIndividual;

    public function __construct($load = true) {
        $this->mandantenId = array(-1, 0); //All,Default
        if ($load) {
            $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
            $this->userSchedData = $this->options->_getOption('auth_sched_time');
            if (!isset($this->userSchedData['time'])) {
                $this->userSchedDataOld = $this->getSchedDataByUser();
            }
            $hostUrl = (function_exists('rest_url')) ? rest_url() : get_site_url();
            $this->authurl = B2S_PLUGIN_API_ENDPOINT_AUTH . '?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2) . '&unset=true&hostUrl=' . $hostUrl;
            $this->allowProfil = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PROFILE);
            $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
            $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
            $this->oAuthPortal = unserialize(B2S_PLUGIN_NETWORK_OAUTH);
            $this->bestTimeInfo = unserialize(B2S_PLUGIN_SCHED_DEFAULT_TIMES_INFO);
            $this->modifyBoardAndGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_MODIFY_BOARD_AND_GROUP);
            $this->networkTypeName = unserialize(B2S_PLUGIN_NETWORK_TYPE);
            $this->networkTypeNameIndividual = unserialize(B2S_PLUGIN_NETWORK_TYPE_INDIVIDUAL);
            $this->networkKindName = unserialize(B2S_PLUGIN_NETWORK_KIND);
            $this->isVideoSupported = unserialize(B2S_PLUGIN_NETWORK_SUPPORT_VIDEO);
            $this->isSocialSupported = unserialize(B2S_PLUGIN_NETWORK_SUPPORT_SOCIAL);
            $this->lang = substr(B2S_LANGUAGE, 0, 2);
        }
        $this->previewImage = plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE);
    }

    public function getData() {

        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getUserAuth', 'view_mode' => 'all', 'auth_count' => true, 'addon_count' => true, 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));

        return array('mandanten' => isset($result->mandanten) ? $result->mandanten : '',
            'auth' => isset($result->auth) ? $result->auth : '',
            'auth_count' => isset($result->auth_count) ? $result->auth_count : false,
            'addon_count' => isset($result->addon_count) ? $result->addon_count : false,
            'portale' => isset($result->portale) ? $result->portale : '');
    }

    public function getCountSchedPostsByUserAuth($networkAuthId = 0) {
        global $wpdb;
        $countSched = $wpdb->get_results($wpdb->prepare("SELECT COUNT(b.id) AS count FROM {$wpdb->prefix}b2s_posts b LEFT JOIN {$wpdb->prefix}b2s_posts_network_details d ON (d.id = b.network_details_id) WHERE d.network_auth_id= %d AND b.hide = %d AND b.sched_date !=%s", $networkAuthId, 0, '0000-00-00 00:00:00'));
        if (is_array($countSched) && !empty($countSched) && isset($countSched[0]->count)) {
            if ((int) $countSched[0]->count > 0) {
                return (int) $countSched[0]->count;
            }
        }
        return false;
    }

    public function getSelectMandantHtml($data) {
        $select = '<select class="form-control b2s-network-mandant-select b2s-select">';
        $select .= '<optgroup label="' . esc_attr__("Default", "blog2social") . '"><option value="-1" selected="selected">' . esc_html__('Show all', 'blog2social') . '</option>';
        $select .= '<option value="0">' . esc_html__('My profile', 'blog2social') . '</option></optgroup>';
        if (!empty($data)) {
            $select .= '<optgroup id="b2s-network-select-more-client" label="' . esc_attr__("Your profiles:", "blog2social") . '">';
            foreach ($data as $id => $name) {
                $select .= '<option value="' . esc_attr($id) . '">' . esc_html(stripslashes($name)) . '</option>';
            }
            $select .= '</optgroup>';
        }
        $select .= '</select>';
        return $select;
    }

    public function getPortale($mandanten, $auth, $portale, $auth_count, $addon_count) {
        $convertAuthData = $this->convertAuthData($auth);

        foreach ($mandanten as $k => $v) {
            $this->mandantenId[] = $k;
        }

        $html = '<div class="col-md-12 b2s-network-details-container">';
        $html .= '<form id = "b2sSaveTimeSettings" method = "post">';
        $html .= '<input id = "action" type = "hidden" value = "b2s_save_user_time_settings" name = "action">';

        $this->addon_count = $addon_count;
        foreach ($this->mandantenId as $k => $mandant) {
            $html .= $this->getItemHtml($mandant, $mandanten, $convertAuthData, $portale, $auth_count);
        }
        $html .= '</form>';
        $html .= '</div>';

        return $html;
    }

    public function getItemHtml($mandant, $mandantenData, $convertAuthData, $portale, $auth_count) {

        $html = '<ul class="list-group b2s-network-details-container-list" data-mandant-id="' . esc_attr($mandant) . '" style="display:' . ($mandant > 0 ? "none" : "block" ) . '">';

        foreach ($portale as $k => $portal) {
            //$html .= "portalid: ".$portal;

            if (!isset($convertAuthData[$mandant][$portal->id]) || empty($convertAuthData[$mandant][$portal->id])) {
                $convertAuthData[$mandant][$portal->id] = array();
            }
            $auth_count = (array) $auth_count;
            $maxNetworkAccount = ($auth_count !== false && is_array($auth_count)) ? ((isset($auth_count[$portal->id])) ? $auth_count[$portal->id] : $auth_count[0]) : false;

            if (isset($this->addon_count) && is_object($this->addon_count) && isset($this->addon_count->{$portal->id}) && isset($this->addon_count->{$portal->id}->total) && (int) $this->addon_count->{$portal->id}->total > 0) {
                $maxNetworkAccount = (int) $maxNetworkAccount + (int) $this->addon_count->{$portal->id}->total;
            }
            $addonText = false;
            if (isset($this->addon_count->{$portal->id}->type) && !empty($this->addon_count->{$portal->id}->type)) {
                //Create Text
                $addonText = '(' . esc_html__('Available accounts', 'blog2social') . ': ' . (($auth_count !== false && is_array($auth_count)) ? ((isset($auth_count[$portal->id])) ? $auth_count[$portal->id] : $auth_count[0]) : 0);
                $additionalText = array(esc_html__('additional profiles', 'blog2social'), esc_html__('additional pages', 'blog2social'), esc_html__('additional groups', 'blog2social'));
                foreach ($this->addon_count->{$portal->id}->type as $type => $type_count) {
                    if (strlen($addonText) > 1) {
                        $addonText .= ' + ';
                    }
                    $addonText .= $additionalText[$type] . ': ' . $type_count;
                }
                $addonText .= ')';
            }

            if ($mandant == -1) { //all
                $html .= $this->getPortaleHtml($portal->id, $portal->name, $mandant, $mandantenData, $convertAuthData, $maxNetworkAccount, true, $addonText);
            } else {
                $html .= $this->getPortaleHtml($portal->id, $portal->name, $mandant, $mandantenData, $convertAuthData[$mandant][$portal->id], $maxNetworkAccount, false, $addonText);
            }
        }
        $html .= '</ul>';

        return $html;
    }

    private function getPortaleHtml($networkId, $networkName, $mandantId, $mandantenData, $networkData, $maxNetworkAccount = false, $showAllAuths = false, $addonText = false) {

        $containerMandantId = $mandantId;
        $mandantId = ($mandantId == -1) ? 0 : $mandantId;
        $sprache = substr(B2S_LANGUAGE, 0, 2);
        $isVideo = (in_array($networkId, $this->isVideoSupported)) ? 'isVideo' : '';
        $isSocial = (in_array($networkId, $this->isSocialSupported)) ? 'isSocial' : '';

        $addClass = (!empty($isVideo) && !empty($isSocial)) ? '' : (!empty($isVideo) ? $isVideo : (!empty($isSocial) ? $isSocial : ''));

        $html = '<li class="list-group-item ' . $addClass . '" data-network-id="' . esc_attr($networkId) . '">';

        $html .= '<div class="media">';

        if ($networkId != 8) {
            $html .= '<img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr($networkName) . '" src="' . esc_url(plugins_url('/assets/images/portale/' . $networkId . '_flat.png', B2S_PLUGIN_FILE)) . '">';
        } else {
            $html .= '<span class="pull-left hidden-xs b2s-img-network"></span>';
        }
        $html .= '<div class="media-body network">';

        $html .= '<h4>' . esc_html(ucfirst($networkName));

        $infoLink = B2S_Tools::getSupportLink('network_guide_link_' . $networkId);
        if ($infoLink !== false && !empty($infoLink)) {
            $html .= ' <a href="' . esc_url($infoLink) . '" target="_blank" class="b2s-info-btn">' . esc_html__('Guide', 'blog2social') . '</a>';
        }
        if ($maxNetworkAccount !== false) {
            if ($networkId == 18) {
                $html .= ' <a class="b2s-info-btn b2sInfoNetwork18Btn" href="#">Info</a>';
            }
        }
        if (isset($this->bestTimeInfo[$networkId]) && !empty($this->bestTimeInfo[$networkId]) && is_array($this->bestTimeInfo[$networkId]) && $networkId != 8) {
            $time = '';
            $slug = ($this->lang == 'de') ? __('Clock', 'blog2social') : '';
            foreach ($this->bestTimeInfo[$networkId] as $k => $v) {
                $time .= B2S_Util::getTimeByLang($v[0], $this->lang) . '-' . B2S_Util::getTimeByLang($v[1], $this->lang) . $slug . ', ';
            }
            $html .= '<span class="hidden-xs hidden-sm b2s-sched-manager-best-time-info">(' . esc_html__('Best times', 'blog2social') . ': ' . esc_html(substr($time, 0, -2)) . ')</span>';
        }

        $html .= '<span class="pull-right">';

        $b2sAuthUrl = $this->authurl . '&portal_id=' . $networkId . '&transfer=' . (in_array($networkId, $this->oAuthPortal) ? 'oauth' : 'form' ) . '&mandant_id=' . $mandantId . '&version=3&affiliate_id=' . B2S_Tools::getAffiliateId();

        if (in_array($networkId, $this->allowProfil)) {
            $name = $this->networkTypeName[0];
            if (isset($this->networkTypeNameIndividual[$networkId][0]) && !empty($this->networkTypeNameIndividual[$networkId][0])) {
                $name = $this->networkTypeNameIndividual[$networkId][0];
            }
            if ($networkId == 24 && B2S_PLUGIN_USER_VERSION < 1) {
                $html .= '<button href="#" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sBusinessFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("BUSINESS", "blog2social") . '</button>';
            } else if (($networkId == 38  || $networkId == 36)   && B2S_PLUGIN_USER_VERSION < 2) {

                    $html .= '<button href="#" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sProFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</button>';
          
            }else if( $networkId == 42 && B2S_PLUGIN_USER_VERSION < 2){

                    $html .= '<button href="#" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sProFeatureNetworksModal" data-title="' . esc_attr__('Expand Your Social Media Universe with HumHub!', 'blog2social') . '" data-type="network-humhub" >' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("ADDON", "blog2social") . '</button>';
            }
            else if (($networkId == 25 || $networkId == 26 || $networkId == 27 || $networkId == 39 || $networkId == 45 || $networkId == 46) && B2S_PLUGIN_USER_VERSION < 1) {
               
               if($networkId == 45 ){
                    $html .= '<button href="#" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sPreFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("ADDON", "blog2social") . '</button>';
            } else {
              
                $html .= '<button href="#" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sPreFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("SMART", "blog2social") . '</button>';
            }
            } else {
                $html .= ($networkId != 18 || (B2S_PLUGIN_USER_VERSION >= 2 && $networkId == 18)) ? '<button onclick="wop(\'' . esc_url($b2sAuthUrl) . '&plugin_version=' . B2S_PLUGIN_VERSION . '&choose=profile\', \'Blog2Social Network\'); return false;" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn" data-network-type="0">' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>' : '<button class="btn btn-' . esc_attr(str_replace(' ', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sProFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">+ ' . esc_html__('Profile', 'blog2social') . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</button>';
            }
        }
        if (in_array($networkId, $this->allowPage)) {
            $name = $this->networkTypeName[1];
            if (isset($this->networkTypeNameIndividual[$networkId][1]) && !empty($this->networkTypeNameIndividual[$networkId][1])) {
                $name = $this->networkTypeNameIndividual[$networkId][1];
            }
            if ($networkId == 42 && B2S_PLUGIN_USER_VERSION < 2) {
                $html .= '<button href="#" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sProFeatureNetworksModal" data-title="' . esc_attr__('Expand Your Social Media Universe with HumHub!', 'blog2social') . '" data-type="network-humhub">' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("ADDON", "blog2social") . '</button>';
            } else if ($networkId == 12) {
                $html .= (B2S_PLUGIN_USER_VERSION >= 1) ? '<button class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-network-add-instagram-business-info-btn" data-b2s-auth-url="' . esc_url($b2sAuthUrl) . '">' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>' : '<button href="#" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sPreFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a network profile?', 'blog2social') . '"  data-type="auth-network">+ ' . esc_html__('Business', 'blog2social') . ' <span class="label label-success">' . esc_html__("SMART", "blog2social") . '</button>';
            } else if ($networkId == 6 && B2S_PLUGIN_USER_VERSION == 0) {
                $html .= '<button href="#" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sPreFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(
                    // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("SMART", "blog2social") . '</button>';
            } else {
                $html .= (B2S_PLUGIN_USER_VERSION > 1 || (B2S_PLUGIN_USER_VERSION == 0 && $networkId == 1) || $networkId == 6 || (B2S_PLUGIN_USER_VERSION == 1 && $networkId == 1)) ? (($networkId == 1) ? '<button class="btn btn-' . preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName)) . ' btn-sm b2s-network-auth-btn b2s-network-add-page-info-btn" data-b2s-auth-url="' . $b2sAuthUrl . '">' . sprintf(
                     // translators: %s is a link
                    esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>' : '<button onclick="wop(\'' . $b2sAuthUrl . '&plugin_version=' . B2S_PLUGIN_VERSION . '&choose=page\', \'Blog2Social Network\'); return false;" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>') : '<button href="#" class="btn btn-' . preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName)) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sProFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a network page?', 'blog2social') . '"  data-type="auth-network">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</button>';
            }
        }
        if (in_array($networkId, $this->allowGroup)) {
            $name = $this->networkTypeName[2];
            if (isset($this->networkTypeNameIndividual[$networkId][2]) && !empty($this->networkTypeNameIndividual[$networkId][2])) {
                $name = $this->networkTypeNameIndividual[$networkId][2];
            }
            $html .= (B2S_PLUGIN_USER_VERSION > 1) ? (($networkId == 1) ? '<button class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn b2s-network-add-group-info-btn" data-b2s-auth-url="' . esc_url($b2sAuthUrl) . '">' . esc_html(sprintf(
                 // translators: %s is a link
                esc_html__('Connect %s', 'blog2social'), esc_html($name))) . '</button>' : '<button  onclick="wop(\'' . esc_url($b2sAuthUrl) . '&plugin_version=' . B2S_PLUGIN_VERSION . '&choose=group\', \'Blog2Social Network\'); return false;" class="btn btn-' . esc_attr(preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName))) . ' btn-sm b2s-network-auth-btn">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>') : '<button class="btn btn-' . preg_replace('/[^a-zA-Z0-9\']/', '', strtolower($networkName)) . ' btn-sm b2s-network-auth-btn b2s-btn-disabled b2sProFeatureNetworksModal" data-title="' . esc_attr__('You want to connect a social media group?', 'blog2social') . '" data-type="auth-network">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</span></button>';
        }
        if (array_key_exists($networkId, unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT))) {
            $html .= '<button onclick="return false;" class="btn btn-default btn-sm b2s-network-auth-btn b2s-edit-template-btn" data-network-id="' . esc_attr($networkId) . '"><i class="glyphicon glyphicon-pencil"></i> ' . esc_html__('Edit Post Template', 'blog2social') . '</button>';
        }
        if (in_array($networkId, unserialize(B2S_PLUGIN_USER_APP_NETWORKS))) {
            if ($networkId == 6) {
                $html .= ' <button class="btn btn-default btn-sm b2s-network-add-app-info-btn" data-network-id="'.$networkId.'"><i class="glyphicon glyphicon-pencil"></i> ' . esc_html__('Manage API Apps', 'blog2social') . '</button>';
            } else {
                $html .= ' <a class="btn btn-default btn-sm" href="admin.php?page=blog2social-user-apps"><i class="glyphicon glyphicon-pencil"></i> ' . esc_html__('Manage API Apps', 'blog2social') . '</a>';
            }
        }

        $html .= '</span></h4>';
        $html .= '<div class="clearfix"></div>';
        $html .= '<ul class="b2s-network-item-auth-list" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" ' . (($showAllAuths) ? 'data-network-count="true"' : '') . '>';

        //First Line
        $html .= '<li class="b2s-network-item-auth-list-li"  data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-view="' . esc_attr((($containerMandantId == -1) ? 'all' : 'selected')) . '">';
        $html .= '<span class="b2s-network-auth-count">' . esc_html__("Connections", "blog2social") . ' <span class="b2s-network-auth-count-current" ' . (($showAllAuths) ? 'data-network-count-trigger="true"' : '') . '  data-network-id="' . esc_attr($networkId) . '"></span>/' . esc_html($maxNetworkAccount) . '</span>';
        if ($addonText != false && !empty($addonText)) {
            $html .= '<span> ' . esc_html($addonText) . '</span>';
        }
        if (B2S_PLUGIN_USER_VERSION > 0) {
            $html .= '<span class="pull-right b2s-sched-manager-title hidden-xs"  data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '">' . esc_html__("Best Time Manager", "blog2social") . ' <a href="#" class="b2s-info-btn b2s-load-settings-sched-time-default-info b2sBestTimesInfoModal">' . esc_html__('Info', 'blog2social') . '</a></span>';
        }else
        {
            $html .= '<span class="pull-right b2s-sched-manager-title hidden-xs"  data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '">' . esc_html__("Best Time Manager", "blog2social") . ' <a href="#" class="b2s-info-btn b2s-load-settings-sched-time-default-info b2sPreFeatureBestTimesModal">' . esc_html__('Info', 'blog2social') . '</a></span>';
        }
        $html .= '</li>';

        if ($showAllAuths) {
            foreach ($this->mandantenId as $ka => $mandantAll) {
                $mandantName = isset($mandantenData->{$mandantAll}) ? ($mandantenData->{$mandantAll}) : esc_html__("My profile", "blog2social");
                if (isset($networkData[$mandantAll][$networkId]) && !empty($networkData[$mandantAll][$networkId])) {
                    $b2sAuthUrlTemp = $this->authurl . '&portal_id=' . $networkId . '&transfer=' . (in_array($networkId, $this->oAuthPortal) ? 'oauth' : 'form' ) . '&mandant_id=' . $mandantAll . '&version=3&affiliate_id=' . B2S_Tools::getAffiliateId();
                    $html .= $this->getAuthItemHtml($networkData[$mandantAll][$networkId], $mandantAll, $mandantName, $networkId, $b2sAuthUrlTemp, $containerMandantId, $sprache);
                }
            }
        } else {
            $html .= $this->getAuthItemHtml($networkData, $mandantId, "", $networkId, $b2sAuthUrl, $containerMandantId, $sprache);
        }

        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        return $html;
    }

    private function getAuthItemHtml($networkData = array(), $mandantId = 0, $mandantName = '', $networkId = 0, $b2sAuthUrl = '', $containerMandantId = 0, $sprache = 'en') {
        $isEdit = false;
        $html = '';
        $lastVisited = $this->options->_getOption('last_visited_network');
        $this->options->_setOption('last_visited_network',  wp_date('Y-m-d H:i:s', null, new DateTimeZone(date_default_timezone_get())));
        if (isset($networkData[0])) {
            foreach ($networkData[0] as $k => $v) {

                $isDeprecated = false;
                $notAllow = ($v['notAllow'] !== false) ? true : false;
                $isInterrupted = ($v['expiredDate'] != '0000-00-00' && $v['expiredDate'] <=  wp_date('Y-m-d', null, new DateTimeZone(date_default_timezone_get()))) ? true : false;
                $isApp = (isset($v['app_name']) && $v['app_name'] != '') ? true : false;

                $borderClass = '';
                if ($isDeprecated) {
                    $borderClass = 'b2s-label-info-border-left deprecated';
                }
                if ($notAllow) {
                    $borderClass = 'b2s-label-warning-border-left';
                }
                if ($isInterrupted) {
                    $borderClass = 'b2s-label-danger-border-left';
                }
                //special case twitter: if not user app and is interrupted as of 24/07/2024 re-auth with user app
                if ($networkId == 2 && !$isApp && $isInterrupted) {
                    $borderClass = 'b2s-label-danger-border-left';
                }

                $html .= '<li class="b2s-network-item-auth-list-li ' . $borderClass . '" data-network-type="0">';
                $html .= '<div class="pull-left">';
                if ($notAllow) {
                    $html .= '<div class="b2s-network-auth-list-info"><span class="glyphicon glyphicon-remove-circle"></span> ' . esc_html__('To reactivate this connection,', 'blog2social') . ' <a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '" target="_blank">' . esc_html__('please upgrade', 'blog2social') . '</a></div>';
                }
                if (($networkId != 2 && !$isApp) && $isInterrupted && !$notAllow) {
                    $html .= '<div class="b2s-network-auth-list-info" data-b2s-auth-info="isInterrupted"><span class="glyphicon glyphicon-remove-circle"></span> ' . esc_html__('Connection is interrupted since', 'blog2social') . ' ' . esc_html(($sprache == 'en' ? $v['expiredDate'] :  wp_date('d.m.Y', strtotime($v['expiredDate']), new DateTimeZone(date_default_timezone_get())))) . '</div>';
                }

                if ($networkId == 1) {
                    $html .= '<div class="b2s-network-auth-list-info b2s-network-video-not-supported"><span class="glyphicon glyphicon-ban-circle"></span> ' . esc_html__('This profile is not supported for video uploads by this network', 'blog2social') . '</div>';
                }

                if ($v['owner_blog_user_id'] !== false) {
                    $displayName = stripslashes(get_user_by('id', $v['owner_blog_user_id'])->display_name);
                    $addNewAssignMarker = '';
                    if (isset($v['assign_created']) && !empty($v['assign_created']) && $lastVisited != false && !empty($lastVisited)) {
                        if ($lastVisited <= $v['assign_created']) {
                            $addNewAssignMarker = '<span class="label label-success">' . esc_html__("New", "blog2social") . '</span> ';
                        }
                    }
                    $html .= '<div class="b2s-network-approved-from">' . $addNewAssignMarker . esc_html__("Assigned by", "blog2social") . ' ' . esc_html(((empty($displayName) || $displayName == false) ? esc_html__("Unknown username", "blog2social") : esc_html($displayName))) . ( (isset($v['assign_created']) && !empty($v['assign_created'])) ? ', ' . B2S_Util::getCustomDateFormat($v['assign_created'], substr(B2S_LANGUAGE, 0, 2)) : '');
                    if (!$isApp) {
                        $html .= '</div> ';
                    }
                }

                //display user app that was used for authorisation where applicable
                if ($isApp) {
                    if ($v['owner_blog_user_id'] !== false) {
                        $html .= ' | ';
                    } else {
                        $html .= '<div class="b2s-network-approved-from">';
                    }
                    if (isset($v['app_name']) && $v['app_name'] != '') {
                        $html .= '<span class="b2s-network-mandant-name">' . esc_html__("authorised with app:", 'blog2social') . " " . esc_html($v['app_name']) . '</span> ';
                        $html .= '</div> ';
                    }
                }


                $name = $this->networkTypeName[0];
                if (isset($this->networkTypeNameIndividual[$networkId][0]) && !empty($this->networkTypeNameIndividual[$networkId][0])) {
                    $name = $this->networkTypeNameIndividual[$networkId][0];
                }
                $html .= '<span class="b2s-network-item-auth-type">' . (($isDeprecated) ? '<span class="glyphicon glyphicon-exclamation-sign glyphicon-info"></span> ' : '') . esc_html($name) . '</span>: <span class="b2s-network-item-auth-user-name">' . esc_html(stripslashes($v['networkUserName'])) . '</span> ';

                if (!empty($mandantName)) {
                    $html .= '<span class="b2s-network-mandant-name">(' . esc_html($mandantName) . ')</span> ';
                }

                $html .= '</div>';

                $html .= '<div class="pull-right">';
                $html .= '<a class="b2s-network-item-auth-list-btn-delete b2s-add-padding-network-delete pull-right" data-network-type="0" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" href="#"><span class="glyphicon  glyphicon-trash glyphicon-grey"></span></a>';
                if (!$isDeprecated) {
                    if ($v['owner_blog_user_id'] == false) {
                        if (!$notAllow || ($notAllow && isset($this->addon_count->{$networkId}->type->{0}) && !empty($this->addon_count->{$networkId}->type->{0}))) { //only show if addon
                            $choose = ($networkId == 6) ? 'page' : 'profil';
                            $html .= '<a href="#" onclick="wop(\'' . $b2sAuthUrl . '&plugin_version=' . B2S_PLUGIN_VERSION . '&choose=' . $choose . '&update=' . $v['networkAuthId'] . (($notAllow) ? '&check=addon' : '') . '\', \'Blog2Social Network\'); return false;" class="b2s-network-auth-btn b2s-network-auth-update-btn b2s-add-padding-network-refresh pull-right" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '"><span class="glyphicon  glyphicon-refresh glyphicon-grey"></span></a>';
                        }
                    } else {
                        $html .= '<span class="b2s-add-padding-network-placeholder-btn pull-right"></span>';
                    }
                    if (!$notAllow) {
                        $html .= '<a href="#" class="pull-right b2s-network-auth-settings-btn b2s-add-padding-network-team pull-right" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="0" data-network-mandant-id="' . esc_attr($mandantId) . '" data-connection-owner="' . esc_attr((($v['owner_blog_user_id'] !== false) ? $v['owner_blog_user_id'] : '0')) . '"><span class="glyphicon glyphicon-cog glyphicon-grey"></span></a>';
                        if ($v['expiredDate'] == '0000-00-00' || $v['expiredDate'] >  wp_date('Y-m-d', null, new DateTimeZone(date_default_timezone_get()))) {
                            if (isset($this->modifyBoardAndGroup[$networkId])) {
                                if (in_array(0, $this->modifyBoardAndGroup[$networkId]['TYPE'])) {
                                    $html .= '<a href="#" class="pull-right b2s-modify-board-and-group-network-btn b2s-add-padding-network-edit" data-modal-title="' . esc_attr($this->modifyBoardAndGroup[$networkId]['TITLE']) . '" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="0"><span class="glyphicon glyphicon-pencil glyphicon-grey"></span></a>';
                                    $isEdit = true;
                                }
                            }
                        }
                    }
                }
                //Sched Manager since V 5.1.0
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-time-area pull-right ' . (!$isEdit ? 'b2s-sched-manager-add-padding' : '') . ' hidden-xs" style="' . (($notAllow) ? 'display:none;' : '') . '">
                        <input class="form-control b2s-box-sched-time-input b2s-settings-sched-item-input-time" type="text" value="' . esc_attr($this->getUserSchedTime($v['networkAuthId'], $networkId, 0, 'time')) . '" readonly="" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="0" data-network-container-mandant-id="' . esc_attr($containerMandantId) . '" name="b2s-user-sched-data[time][' . esc_attr($v['networkAuthId']) . ']">
                        </span>';
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-day-area pull-right hidden-xs" style="' . (($notAllow) ? 'display:none;' : '') . '"><span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-item-input-day-btn-minus" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '">-</span> <span class="b2s-text-middle">+</span> <input type="text" class="b2s-sched-manager-item-input-day" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="0"  data-network-container-mandant-id="' . esc_attr($containerMandantId) . '" name="b2s-user-sched-data[delay_day][' . esc_attr($v['networkAuthId']) . ']" value="' . esc_attr($this->getUserSchedTime($v['networkAuthId'], $networkId, 0, 'day')) . '" readonly> <span class="b2s-text-middle">' . esc_html__('Days', 'blog2social') . '</span> <span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-item-input-day-btn-plus" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '">+</span></span>';
                } else {
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-premium-area pull-right hidden-xs"><span class="label label-success"><a href="#" class="btn-label-premium b2sPreFeatureBestTimesModal">' . esc_html__('SMART', 'blog2social') . '</a></span></span>';
                }

                $html .= '</div>';

                $html .= '<div class="clearfix"></div></li>';
            }
        }
        if (isset($networkData[1])) {
            foreach ($networkData[1] as $k => $v) {

                $isDeprecated = false;
                $notAllow = ($v['notAllow'] !== false) ? true : false;
                $isInterrupted = ($v['expiredDate'] != '0000-00-00' && $v['expiredDate'] <=  wp_date('Y-m-d', null, new DateTimeZone(date_default_timezone_get()))) ? true : false;
                $isApp = (isset($v['app_name']) && $v['app_name'] != '') ? true : false;

                $html .= '<li class="b2s-network-item-auth-list-li ' . (($isDeprecated) ? 'b2s-label-info-border-left deprecated' : (($notAllow) ? 'b2s-label-warning-border-left' : (($isInterrupted) ? 'b2s-label-danger-border-left' : ''))) . '" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="1">';
                $html .= '<div class="pull-left">';

                if ($notAllow) {
                    $html .= '<div class="b2s-network-auth-list-info"><span class="glyphicon glyphicon-remove-circle"></span> ' . esc_html__('To reactivate this connection,', 'blog2social') . ' <a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '" target="_blank">' . esc_html__('please upgrade', 'blog2social') . '</a></div>';
                }
                if ($isInterrupted && !$notAllow) {
                    $html .= '<div class="b2s-network-auth-list-info" data-b2s-auth-info="isInterrupted">' . esc_html__('Connection is interrupted since', 'blog2social') . ' ' . esc_html(($sprache == 'en' ? $v['expiredDate'] :  wp_date('d.m.Y', strtotime($v['expiredDate']), new DateTimeZone(date_default_timezone_get())))) . '</div>';
                }
                if ($v['owner_blog_user_id'] !== false) {
                    $displayName = stripslashes(get_user_by('id', $v['owner_blog_user_id'])->display_name);
                    $addNewAssignMarker = '';
                    if (isset($v['assign_created']) && !empty($v['assign_created']) && $lastVisited != false && !empty($lastVisited)) {
                        if ($lastVisited <= $v['assign_created']) {
                            $addNewAssignMarker = '<span class="label label-success">' . esc_html__("New", "blog2social") . '</span> ';
                        }
                    }
                    $html .= '<div class="b2s-network-approved-from">' . $addNewAssignMarker . esc_html__("Assigned by", "blog2social") . ' ' . esc_html(((empty($displayName) || $displayName == false) ? __("Unknown username", "blog2social") : $displayName)) . ( (isset($v['assign_created']) && !empty($v['assign_created'])) ? ', ' . B2S_Util::getCustomDateFormat($v['assign_created'], substr(B2S_LANGUAGE, 0, 2)) : '');

                    if (!$isApp) {
                        $html .= '</div> ';
                    }
                }

                //display user app that was used for authorisation where applicable
                if ($isApp) {
                    if ($v['owner_blog_user_id'] !== false) {
                        $html .= ' | ';
                    } else {
                        $html .= '<div class="b2s-network-approved-from">';
                    }
                    if (isset($v['app_name']) && $v['app_name'] != '') {
                        $html .= '<span class="b2s-network-mandant-name">' . esc_html__("authorised with app:", 'blog2social') . " " . esc_html($v['app_name']) . '</span> ';
                        $html .= '</div> ';
                    }
                }


                $name = $this->networkTypeName[1];
                if (isset($this->networkTypeNameIndividual[$networkId][1]) && !empty($this->networkTypeNameIndividual[$networkId][1])) {
                    $name = $this->networkTypeNameIndividual[$networkId][1];
                }
                $html .= '<span class="b2s-network-item-auth-type">' . (($isDeprecated) ? '<span class="glyphicon glyphicon-exclamation-sign glyphicon-info"></span> ' : '') . ($networkId == 19 && isset($this->networkKindName[$v['networkKind']]) ? $this->networkKindName[$v['networkKind']] : $name) . '</span>: <span class="b2s-network-item-auth-user-name">' . esc_html(stripslashes($v['networkUserName'])) . '</span> ';

                if ($networkId == 19 && (int) $v['networkKind'] == 1) {// Xing Business Pages Info
                    $html .= '<input type="hidden" value="1" id="b2sHasXingBusinessPage">';
                }


                if (!empty($mandantName)) {
                    $html .= '<span class="b2s-network-mandant-name">(' . esc_html($mandantName) . ')</span> ';
                }

                $html .= '</div>';
                $html .= '<div class="pull-right">';
                $html .= '<a class="b2s-network-item-auth-list-btn-delete b2s-add-padding-network-delete pull-right" data-network-type="1" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" href="#"><span class="glyphicon  glyphicon-trash glyphicon-grey"></span></a>';
                if (!$isDeprecated) {
                    if (!$notAllow || ($notAllow && isset($this->addon_count->{$networkId}->type->{1}) && !empty($this->addon_count->{$networkId}->type->{1}))) { //only show if addon
                        if ($v['owner_blog_user_id'] == false) {
                            $html .= '<a href="#" onclick="wop(\'' . esc_url($b2sAuthUrl) . '&plugin_version=' . B2S_PLUGIN_VERSION . '&choose=page&update=' . esc_attr($v['networkAuthId']) . (($notAllow) ? '&check=addon' : '') . '\', \'Blog2Social Network\'); return false;" class="b2s-network-auth-btn b2s-network-auth-update-btn b2s-add-padding-network-refresh pull-right" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '"><span class="glyphicon  glyphicon-refresh glyphicon-grey"></span></a>';
                        } else {
                            $html .= '<span class="b2s-add-padding-network-placeholder-btn pull-right"></span>';
                        }
                    }
                    if (!$notAllow) {
                        $html .= '<a href="#" class="pull-right b2s-network-auth-settings-btn b2s-add-padding-network-team pull-right" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="1" data-network-mandant-id="' . esc_attr($mandantId) . '" data-connection-owner="' . esc_attr((($v['owner_blog_user_id'] !== false) ? $v['owner_blog_user_id'] : '0')) . '"><span class="glyphicon glyphicon-cog glyphicon-grey"></span></a>';
                        if ($v['expiredDate'] == '0000-00-00' || $v['expiredDate'] >  wp_date('Y-m-d', null, new DateTimeZone(date_default_timezone_get()))) {
                            if (isset($this->modifyBoardAndGroup[$networkId])) {
                                if (in_array(1, $this->modifyBoardAndGroup[$networkId]['TYPE'])) {
                                    $html .= '<a href="#" class="pull-right b2s-modify-board-and-group-network-btn b2s-add-padding-network-edit" data-modal-title="' . esc_attr($this->modifyBoardAndGroup[$networkId]['TITLE']) . '" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="1"><span class="glyphicon glyphicon-pencil glyphicon-grey"></span></a>';
                                    $isEdit = true;
                                }
                            }
                        }
                    }
                }

                //Sched Manager since V 5.1.0
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-time-area pull-right ' . (!$isEdit ? 'b2s-sched-manager-add-padding' : '') . ' hidden-xs" style="' . (($notAllow) ? 'display:none;' : '') . '">
                        <input class="form-control b2s-box-sched-time-input b2s-settings-sched-item-input-time" type="text" value="' . esc_attr($this->getUserSchedTime($v['networkAuthId'], $networkId, 1, 'time')) . '" readonly=""  data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="1" data-network-container-mandant-id="' . esc_attr($containerMandantId) . '" name="b2s-user-sched-data[time][' . esc_attr($v['networkAuthId']) . ']">
                        </span>';
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-day-area pull-right hidden-xs" style="' . (($notAllow) ? 'display:none;' : '') . '"><span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-item-input-day-btn-minus" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '">-</span> <span class="b2s-text-middle">+</span> <input type="text" class="b2s-sched-manager-item-input-day" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="1" data-network-container-mandant-id="' . esc_attr($containerMandantId) . '"  name="b2s-user-sched-data[delay_day][' . esc_attr($v['networkAuthId']) . ']" value="' . esc_attr($this->getUserSchedTime($v['networkAuthId'], $networkId, 1, 'day')) . '" readonly> <span class="b2s-text-middle">' . esc_html__('Days', 'blog2social') . '</span> <span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-item-input-day-btn-plus" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '">+</span></span>';
                } else {
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-premium-area pull-right hidden-xs"><span class="label label-success"><a href="#" class="btn-label-premium b2sPreFeatureBestTimesModal">' . esc_html__('SMART', 'blog2social') . '</a></span></span>';
                }

                $html .= '</div>';

                $html .= '<div class="clearfix"></div></li>';
            }
        }
        if (isset($networkData[2])) {
            foreach ($networkData[2] as $k => $v) {

                $isDeprecated = false;
                $notAllow = ($v['notAllow'] !== false) ? true : false;
                $isInterrupted = ($v['expiredDate'] != '0000-00-00' && $v['expiredDate'] <=  wp_date('Y-m-d', null, new DateTimeZone(date_default_timezone_get()))) ? true : false;

                $html .= '<li class="b2s-network-item-auth-list-li ' . (($isDeprecated) ? 'b2s-label-info-border-left deprecated' : (($notAllow) ? 'b2s-label-warning-border-left' : (($isInterrupted) ? 'b2s-label-danger-border-left' : ''))) . '" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="2">';

                $html .= '<div class="pull-left">';

                if ($notAllow) {
                    $html .= '<div class="b2s-network-auth-list-info"><span class="glyphicon glyphicon-remove-circle"></span> ' . esc_html__('To reactivate this connection,', 'blog2social') . ' <a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '" target="_blank">' . esc_html__('please upgrade', 'blog2social') . '</a></div>';
                }
                if ($isInterrupted && !$notAllow) {
                    $html .= '<div class="b2s-network-auth-list-info" data-b2s-auth-info="isInterrupted">' . esc_html__('Connection is interrupted since', 'blog2social') . ' ' . esc_html(($sprache == 'en' ? $v['expiredDate'] :  wp_date('d.m.Y', strtotime($v['expiredDate']), new DateTimeZone(date_default_timezone_get())))) . '</div>';
                }

                if ($networkId == 1) {
                    $html .= '<div class="b2s-network-auth-list-info b2s-network-video-not-supported"><span class="glyphicon glyphicon-ban-circle"></span> ' . esc_html__('This group is not supported for video uploads by this network', 'blog2social') . '</div>';
                }

                if ($v['owner_blog_user_id'] !== false) {
                    $displayName = stripslashes(get_user_by('id', $v['owner_blog_user_id'])->display_name);
                    $addNewAssignMarker = '';
                    if (isset($v['assign_created']) && !empty($v['assign_created']) && $lastVisited != false && !empty($lastVisited)) {
                        if ($lastVisited <= $v['assign_created']) {
                            $addNewAssignMarker = '<span class="label label-success">' . esc_html__("New", "blog2social") . '</span> ';
                        }
                    }
                    $html .= '<div class="b2s-network-approved-from">' . $addNewAssignMarker . esc_html__("Assigned by", "blog2social") . ' ' . esc_html(((empty($displayName) || $displayName == false) ? __("Unknown username", "blog2social") : $displayName)) . ( (isset($v['assign_created']) && !empty($v['assign_created'])) ? ', ' . B2S_Util::getCustomDateFormat($v['assign_created'], substr(B2S_LANGUAGE, 0, 2)) : '') . '</div> ';
                }
                $name = $this->networkTypeName[2];
                if (isset($this->networkTypeNameIndividual[$networkId][2]) && !empty($this->networkTypeNameIndividual[$networkId][2])) {
                    $name = $this->networkTypeNameIndividual[$networkId][2];
                }
                $html .= '<span class="b2s-network-item-auth-type">' . (($isDeprecated) ? '<span class="glyphicon glyphicon-exclamation-sign glyphicon-info"></span> ' : '') . esc_html($name) . '</span>: <span class="b2s-network-item-auth-user-name">' . esc_html(stripslashes($v['networkUserName'])) . '</span> ';

                if (!empty($mandantName)) {
                    $html .= '<span class="b2s-network-mandant-name">(' . esc_html($mandantName) . ')</span> ';
                }
                $html .= '</div>';
                $html .= '<div class="pull-right">';
                $html .= '<a class="b2s-network-item-auth-list-btn-delete b2s-add-padding-network-delete pull-right" data-network-type="2" data-network-id="' . esc_attr($networkId) . '" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" href="#"><span class="glyphicon  glyphicon-trash glyphicon-grey"></span></a>';
                if (!$isDeprecated) {
                    if (!$notAllow || ($notAllow && isset($this->addon_count->{$networkId}->type->{2}) && !empty($this->addon_count->{$networkId}->type->{2}))) { //only show if addon
                        if ($v['owner_blog_user_id'] == false) {
                            $html .= '<a href="#" onclick="wop(\'' . esc_url($b2sAuthUrl) . '&plugin_version=' . B2S_PLUGIN_VERSION . '&choose=group&update=' . esc_attr($v['networkAuthId']) . (($notAllow) ? '&check=addon' : '') . '\', \'Blog2Social Network\'); return false;" class="b2s-network-auth-btn b2s-network-auth-update-btn b2s-add-padding-network-refresh pull-right" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '"><span class="glyphicon  glyphicon-refresh glyphicon-grey"></span></a>';
                        } else {
                            $html .= '<span class="b2s-add-padding-network-placeholder-btn pull-right"></span>';
                        }
                    }
                    if (!$notAllow) {
                        $html .= '<a href="#" class="pull-right b2s-network-auth-settings-btn b2s-add-padding-network-team pull-right" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="2" data-network-mandant-id="' . esc_attr($mandantId) . '" data-connection-owner="' . esc_attr((($v['owner_blog_user_id'] !== false) ? $v['owner_blog_user_id'] : '0')) . '"><span class="glyphicon glyphicon-cog glyphicon-grey"></span></a>';
                        if ($v['expiredDate'] == '0000-00-00' || $v['expiredDate'] >  wp_date('Y-m-d', null, new DateTimeZone(date_default_timezone_get()))) {
                            if (isset($this->modifyBoardAndGroup[$networkId])) {
                                if (in_array(2, $this->modifyBoardAndGroup[$networkId]['TYPE'])) {
                                    $html .= '<a href="#" class="pull-right b2s-modify-board-and-group-network-btn b2s-add-padding-network-edit" data-modal-title="' . esc_attr($this->modifyBoardAndGroup[$networkId]['TITLE']) . '" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="2"><span class="glyphicon glyphicon-pencil glyphicon-grey"></span></a>';
                                    $isEdit = true;
                                }
                            }
                        }
                    }
                }

                //Sched Manager since V 5.1.0
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-time-area pull-right ' . (!$isEdit ? 'b2s-sched-manager-add-padding' : '') . ' hidden-xs" style="' . (($notAllow) ? 'display:none;' : '') . '">
                        <input class="form-control b2s-box-sched-time-input b2s-settings-sched-item-input-time" type="text" value="' . esc_attr($this->getUserSchedTime($v['networkAuthId'], $networkId, 2, 'time')) . '" readonly="" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="2" data-network-container-mandant-id="' . esc_attr($containerMandantId) . '" name="b2s-user-sched-data[time][' . esc_attr($v['networkAuthId']) . ']">
                        </span>';
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-day-area pull-right hidden-xs" style="' . (($notAllow) ? 'display:none;' : '') . '"><span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-item-input-day-btn-minus" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '">-</span> <span class="b2s-text-middle">+</span> <input type="text" class="b2s-sched-manager-item-input-day" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" data-network-mandant-id="' . esc_attr($mandantId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="2" data-network-container-mandant-id="' . esc_attr($containerMandantId) . '"  name="b2s-user-sched-data[delay_day][' . esc_attr($v['networkAuthId']) . ']" value="' . esc_attr($this->getUserSchedTime($v['networkAuthId'], $networkId, 2, 'day')) . '" readonly> <span class="b2s-text-middle">' . esc_html__('Days', 'blog2social') . '</span> <span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-item-input-day-btn-plus" data-network-auth-id="' . esc_attr($v['networkAuthId']) . '">+</span></span>';
                } else {
                    $html .= '<span data-network-auth-id="' . esc_attr($v['networkAuthId']) . '" class="b2s-sched-manager-premium-area pull-right hidden-xs"><span class="label label-success"><a href="#" class="btn-label-premium b2sPreFeatureBestTimesModal">' . esc_html__('SMART', 'blog2social') . '</a></span></span>';
                }

                $html .= '</div>';

                $html .= '<div class="clearfix"></div></li>';
            }
        }
        return $html;
    }

    private function convertAuthData($auth) {
        $convertAuth = array();
        foreach ($auth as $k => $value) {
            $convertAuth[$value->mandantId][$value->networkId][$value->networkType][] = array(
                'networkAuthId' => $value->networkAuthId,
                'networkUserName' => $value->networkUserName,
                'expiredDate' => $value->expiredDate,
                'networkKind' => $value->networkKind,
                'notAllow' => (isset($value->notAllow) ? $value->notAllow : false),
                'owner_blog_user_id' => (isset($value->owner_blog_user_id) ? $value->owner_blog_user_id : false),
                'assign_created' => (isset($value->assign_created) ? $value->assign_created : false),
                'app_name' => (isset($value->app_name) ? $value->app_name : ''),
            );
        }
        return $convertAuth;
    }

    //New >V5.1.0 Seeding
    private function getUserSchedTime($network_auth_id = 0, $network_id = 0, $network_type = 0, $type = 'time') { //type = time,day
        //new > v5.1.0
        if ($this->userSchedData !== false) {
            if (is_array($this->userSchedData) && isset($this->userSchedData['delay_day'][$network_auth_id]) && isset($this->userSchedData['time'][$network_auth_id]) && !empty($this->userSchedData['time'][$network_auth_id])) {
                if ($type == 'time') {
                    $slug = ($this->lang == 'en') ? 'h:i A' : 'H:i';
                    return  wp_date($slug, strtotime( wp_date('Y-m-d ' . $this->userSchedData['time'][$network_auth_id] . ':00', null, new DateTimeZone(date_default_timezone_get()))), new DateTimeZone(date_default_timezone_get()));
                }
                if ($type == 'day') {
                    return (int) $this->userSchedData['delay_day'][$network_auth_id];
                }
            }
        }
        //old < 5.1.0 load data
        if (!empty($this->userSchedDataOld) && is_array($this->userSchedDataOld)) {
            if ($type == 'time') {
                foreach ($this->userSchedDataOld as $k => $v) {
                    if ((int) $network_id == (int) $v->network_id && (int) $network_type == (int) $v->network_type) {
                        $slug = ($this->lang == 'en') ? 'h:i A' : 'H:i';
                        return  wp_date($slug, strtotime( wp_date('Y-m-d ' . $v->sched_time . ':00', null, new DateTimeZone(date_default_timezone_get()))), new DateTimeZone(date_default_timezone_get()));
                    }
                }
            }
        }
        if ($type == 'day') {
            return 0;
        }
        return null;
    }

    //Old < 5.1.0
    private function getSchedDataByUser() {
        global $wpdb;
        $saveSchedData = null;
        //if exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_post_sched_settings'") == $wpdb->prefix . 'b2s_post_sched_settings') {
            $saveSchedData = $wpdb->get_results($wpdb->prepare("SELECT network_id, network_type, sched_time FROM {$wpdb->prefix}b2s_post_sched_settings WHERE blog_user_id= %d", B2S_PLUGIN_BLOG_USER_ID));
        }
        return $saveSchedData;
    }

    public function getNetworkAuthAssignment($networkAuthId = 0, $networkId = 0, $networkType = 0) {
        global $wpdb;
        $blogUserTokenResult = $wpdb->get_results("SELECT token FROM `{$wpdb->prefix}b2s_user`");
        $blogUserToken = array();
        foreach ($blogUserTokenResult as $k => $row) {
            array_push($blogUserToken, $row->token);
        }
        $data = array('action' => 'getTeamAssignUserAuth', 'token' => B2S_PLUGIN_TOKEN, 'networkAuthId' => $networkAuthId, 'blogUser' => $blogUserToken);
        $networkAuthAssignment = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $data, 30), true);
        if (isset($networkAuthAssignment['result']) && $networkAuthAssignment['result'] !== false) {
            $doneIds = array();
            $assignList = '<ul class="b2s-network-item-auth-list" id="b2s-approved-user-list"><li class="b2s-network-item-auth-list-li b2s-bold">' . esc_html__('Connection currently assigned to', 'blog2social') . '</li>';
            if (isset($networkAuthAssignment['assignList']) && is_array($networkAuthAssignment['assignList']) && !empty($networkAuthAssignment['assignList'])) {
                $options = new B2S_Options((int) B2S_PLUGIN_BLOG_USER_ID);
                $optionUserTimeZone = $options->_getOption('user_time_zone');
                $userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
                $userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
                foreach ($networkAuthAssignment['assignList'] as $k => $listUser) {
                    array_push($doneIds, $listUser['assign_blog_user_id']);
                    if (get_userdata($listUser['assign_blog_user_id']) !== false) {
                        $current_user_date =  wp_date((strtolower(substr(B2S_LANGUAGE, 0, 2)) == 'de') ? 'd.m.Y' : 'Y-m-d', strtotime(B2S_Util::getUTCForDate($listUser['created_utc'], $userTimeZoneOffset)), new DateTimeZone(date_default_timezone_get()));
                        $displayName = stripslashes(get_user_by('id', $listUser['assign_blog_user_id'])->display_name);
                        $assignList .= '<li class="b2s-network-item-auth-list-li">';
                        $assignList .= '<div class="pull-left" style="padding-top: 5px;"><span>' . esc_html(((empty($displayName) || $displayName == false) ? __("Unknown username", "blog2social") : $displayName)) . '</span></div>';
                        $assignList .= '<div class="pull-right"><span style="margin-right: 10px;">' . esc_html($current_user_date) . '</span> <button class="b2s-network-item-auth-list-btn-delete btn btn-danger btn-sm" data-assign-network-auth-id="' . esc_attr($listUser['assign_network_auth_id']) . '" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-id="' . esc_attr($networkId) . '" data-network-type="' . esc_attr($networkType) . '" data-blog-user-id="' . esc_attr($listUser['assign_blog_user_id']) . '">' . esc_html__('delete', 'blog2social') . '</button></div>';
                        $assignList .= '<div class="clearfix"></div></li>';
                    }
                }
            }
            $assignList .= '</ul>';

            $select = '<select class="form-control b2s-select" id="b2s-select-assign-user">';
            if (isset($networkAuthAssignment['userList']) && !empty($networkAuthAssignment['userList'])) {
                foreach ($networkAuthAssignment['userList'] as $k => $listUser) {
                    if ((int) $listUser != B2S_PLUGIN_BLOG_USER_ID && !in_array($listUser, $doneIds)) {
                        array_push($doneIds, $listUser);
                        $userDetails = get_option('B2S_PLUGIN_USER_VERSION_' . $listUser);
                        if (isset($userDetails['B2S_PLUGIN_USER_VERSION']) && (int) $userDetails['B2S_PLUGIN_USER_VERSION'] == 3) {
                            $displayName = stripslashes(get_user_by('id', $listUser)->display_name);
                            if (!empty($displayName) && $displayName != false) {
                                $select .= '<option value="' . esc_attr($listUser) . '">' . esc_html($displayName) . '</option>';
                            }
                        }
                    }
                }
            }
            $select .= '</select>';

            return array('result' => true, 'userSelect' => $select, 'assignList' => $assignList);
        }
        return array('result' => false);
    }

    public function getUrlParameterSettings($networkAuthId, $networkId) {
        $html = '<div class="col-md-12 b2s-text-bold"><span>' . sprintf(
            // translators: %s is a link
            __('Define parameters that will be added to link posts on this network e.g. to create tracking links with UTM paramters. <a target="_blank" href="%s">More information</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('url_parameter'))) . '</span></div>';
        $html .= '<div class="b2s-col-name">';
        $html .= '<div class="col-md-5 b2s-url-parameter-legend-text">' . esc_html__('Name', 'blog2social') . '</div>';
        $html .= '<div class="col-md-5 b2s-url-parameter-legend-text">' . esc_html__('Value', 'blog2social') . '</div>';
        $html .= '</div>';

        $html .= '<ul class="b2s-url-parameter-list col-md-12">';

        global $wpdb;
        $dataString = $wpdb->get_var($wpdb->prepare("SELECT `data` FROM `{$wpdb->prefix}b2s_posts_network_details` WHERE `network_auth_id` = %d", (int) $networkAuthId));
        $counter = 0;
        if ($dataString !== NULL && !empty($dataString)) {
            $data = unserialize($dataString);
            if ($data != false && isset($data['url_parameter'][0]['querys']) && !empty($data['url_parameter'][0]['querys']) && is_array($data['url_parameter'][0]['querys'])) {
                foreach ($data['url_parameter'][0]['querys'] as $param => $value) {
                    $html .= '<li class="b2s-url-parameter-entry row">';
                    $html .= '<div class="col-md-5"><input class="form-control b2s-link-parameter-name" value="' . esc_attr(urldecode($param)) . '"></div>';
                    $html .= '<div class="col-md-5"><input class="form-control b2s-link-parameter-value" value="' . esc_attr(urldecode($value)) . '"></div>';
                    $html .= '<div class="col-md-1"><span aria-hidden="true" class="b2s-url-parameter-remove-btn text-danger">&times;</span></div>';
                    $html .= '</li>';
                    $counter++;
                }
            }
        }

        $html .= '</ul>';
        $html .= '<div class="col-md-12 padding-bottom-10"><button class="btn btn-sm btn-default b2s-url-parameter-add-btn" ' . (($counter >= 10) ? 'style="display:none;"' : '') . '>' . esc_html__('+ add Parameter', 'blog2social') . '</button></div>';
        $html .= '<div class="col-md-12"><input type="checkbox" class="b2s-url-parameter-for-all-network" id="b2s-url-parameter-for-all-network"><label for="b2s-url-parameter-for-all-network"> ' . sprintf(
             // translators: %s is network name
            esc_html__('Apply for all %s connections', 'blog2social'), unserialize(B2S_PLUGIN_NETWORK)[$networkId]) . '</label></div>';
        $html .= '<div class="col-md-6"><input type="checkbox" class="b2s-url-parameter-for-all" id="b2s-url-parameter-for-all"><label for="b2s-url-parameter-for-all"> ' . esc_html__('Apply for all connections', 'blog2social') . '</label></div>';
        $html .= '<div class="col-md-6"><button class="btn btn-sm btn-primary pull-right b2s-url-parameter-save-btn" data-network-auth-id="' . esc_attr($networkAuthId) . '" data-network-id="' . esc_attr($networkId) . '">' . esc_html__('save', 'blog2social') . '</button></div>';
        return $html;
    }

    public function getEditTemplateForm($networkId, $isFreeUser = false) {
        require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
        $options = new B2S_Options(get_current_user_id());
        $post_template = $options->_getOption("post_template");
        $assConnected = $this->isAssistiniConnected();
        $connectedLabel = __('Connected with Assistini AI', 'blog2social');
        $currentUser = wp_get_current_user();
        $userEmail = (isset($currentUser->user_email) && !empty($currentUser->user_email)) ? $currentUser->user_email : '';
        $authUrl = B2S_PLUGIN_API_ASS_ENDPOINT_AUTH . 'auth/assistini.php?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2);
        
        $defaultSchema = unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT)[$networkId];
       
        if (B2S_PLUGIN_USER_VERSION >= 1 && $post_template != false && isset($post_template[$networkId]) && !empty($post_template[$networkId])) {
            foreach ($defaultSchema as $type => $value) {
               
                if (!isset($post_template[$networkId][$type])) {
                    $post_template[$networkId][$type] = $value;
                }
                if ($networkId == 24 && $post_template[$networkId][$type]['format'] == false) { //new 6.8.2: format activate again | old: special Telegram disable PostFormat
                    $post_template[$networkId][$type]['format'] = 0;
                }
                
                // Ensure short_comment is merged from default template if missing
                if (isset($defaultSchema[$type]['short_comment']) && !isset($post_template[$networkId][$type]['short_comment'])) {
                    $post_template[$networkId][$type]['short_comment'] = $defaultSchema[$type]['short_comment'];
                }

                if (!isset($post_template[$networkId][$type]['ai_template']) || !is_array($post_template[$networkId][$type]['ai_template'])) {
                    $post_template[$networkId][$type]['ai_template'] = B2S_Tools::getAiTemplateDefaults();
                }

                if (($networkId == 1 || $networkId == 12) && !isset($post_template[$networkId][$type]['share_as_story'])) {
                    $post_template[$networkId][$type]['share_as_story'] = 0;
                }
                
            }

            $schema = $post_template[$networkId];
           

            if (count($schema) < count($defaultSchema)) {
             
                $schema = array_merge($schema, $defaultSchema);
            }

        } else {
            $schema = $defaultSchema;
        }

        //TODO: V7.0.0 Pinterest Profile deprecated, ignore profiles, remove in V.7.X.X
        if ($networkId == 6 && isset($defaultSchema[0])) {
            unset($defaultSchema[0]);
        }

        $html = '<div class="row">';
        $html .= '<div class="col-sm-12">';
        $html .= '<div class="alert alert-success b2s-edit-template-save-success" style="display: none;">' . esc_html__('Successfully saved', 'blog2social') . '</div>';
        $html .= '<div class="alert alert-success b2s-edit-template-save-failed" style="display: none;">' . esc_html__('Failed to save', 'blog2social') . '</div>';
        $html .= '<div class="alert alert-success b2s-edit-template-load-default-failed" style="display: none;">' . esc_html__('Failed to load the default template', 'blog2social') . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $post = wp_get_recent_posts(array(
            'numberposts' => 1,
            'post_status' => 'publish'
        ));
        if (isset($post[0]['ID']) && isset($post[0]['post_content']) && isset($post[0]['post_title']) && isset($post[0]['post_excerpt']) && isset($post[0]['post_author'])) {
            $post_tags = wp_get_post_tags($post[0]['ID']);
            $tags = '';
            if ($post_tags != false && !empty($post_tags)) {
                foreach ($post_tags as $tag) {
                    $tags .= '#' . $tag->name . ' ';
                }
                substr($tags, 0, -1);
            }
            $post_author = get_userdata($post[0]['post_author']);
            $author = '';
            if (isset($post_author->display_name) && !empty($post_author->display_name)) {
                $author = $post_author->display_name;
            }
            $hook_filter = new B2S_Hook_Filter();
            $images_urls = $hook_filter->get_wp_post_image((int) $post[0]['ID']);
            $image_url = ((!empty($images_urls) && isset(array_values($images_urls)[0][0])) ? array_values($images_urls)[0][0] : false);
            if ($image_url != false && !empty($image_url)) {
                $this->previewImage = $image_url;
            }
            $post_content = (function_exists('strip_shortcodes')) ? strip_shortcodes($post[0]['post_content']) : $post[0]['post_content'];
            $post_content = preg_replace('/<!--.*?-->/s', '', $post_content); // Strip WP block delimiter comments
            $post_excerpt = (function_exists('strip_shortcodes')) ? strip_shortcodes($post[0]['post_excerpt']) : $post[0]['post_excerpt'];
            $post_title = (function_exists('strip_shortcodes')) ? strip_shortcodes($post[0]['post_title']) : $post[0]['post_title'];
            $post_url = get_permalink($post[0]['ID']);
            $html .= '<input type="hidden" id="b2s_use_post" value="true">';
            $html .= '<input type="hidden" id="b2sPostId" value="' . esc_attr($post[0]['ID']) . '">';
            if(in_array($networkId, array(4, 11, 14))){
                $allowed_tags = array(
                    'p'   => array(),
                    'h1'  => array(),
                    'h2'  => array(),
                    'h3'  => array(),
                    'br'  => array(),
                    'i'   => array(),
                    'b'   => array(),
                    'a'   => array(
                        'href'   => true,
                        'title'  => true,
                        'target' => true,
                        'rel'    => true,
                    ),
                    'img' => array(
                        'src'    => true,
                        'alt'    => true,
                        'width'  => true,
                        'height' => true,
                        'class'  => true,
                    ),
                );
                $html .=  '<input type="hidden" id="b2s_post_content" value="' . esc_attr(wp_kses($post_content, $allowed_tags)) . '"><input type="hidden" id="b2s_post_content_raw" value="' . esc_attr($post_content) . '">';
            }else
            {
                $post_content = str_replace("</p>", "</p><br>", $post_content); //Add Linebreaks where normally paragraph breaks
                $html .= '<input type="hidden" id="b2s_post_content" value="' . esc_attr(wp_kses($post_content, array('br' => array(), 'p' => array()))) . '">';
            }
            $html .= '<input type="hidden" id="b2s_post_excerpt" value="' . esc_attr(wp_strip_all_tags($post_excerpt)) . '">';
            $html .= '<input type="hidden" id="b2s_post_title" value="' . esc_attr(wp_strip_all_tags($post_title)) . '">';
            $html .= '<input type="hidden" id="b2s_post_url" value="' . esc_url($post_url) . '">';
            $html .= '<input type="hidden" id="b2s_post_author" value="' . esc_attr($author) . '">';
            $html .= '<input type="hidden" id="b2s_post_keywords" value="' . esc_attr($tags) . '">';
        } else {
            $html .= '<input type="hidden" id="b2s_use_post" value="false">';
        }
        if (B2S_PLUGIN_USER_VERSION < 1) {
            $html .= '<div class="row">';
            $html .= '<div class="col-sm-12">';
            $html .= '<div class="alert alert-info">';
            $html .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html__('Upgrade to Blog2Social Smart or higher to customize your individual post templates that will automatically pre-format the structure of your social media posts. Select and define elements, such as title, excerpt, content, hashtags, and edit the post format. The “content” element is selected by default. Define custom post templates per social network and per profile, group & page. You can also add static content (such as individual hashtags or slogans) to your post templates.', 'blog2social');
            $html .= ' <a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '" class="b2s-bold b2s-text-underline">' . esc_html__('Upgrade to Blog2Social Smart', 'blog2social') . '</a>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-12">';
        $html .= '<div class="b2s-edit-template-mode-header">';
        $html .= '<div class="b2s-edit-template-mode-switch" role="group">';
        $html .= '<button type="button" class="b2s-edit-template-mode-btn b2s-edit-template-mode-btn-standard active" data-mode="standard">';
        $html .= '<i class="fa-regular fa-file-lines" style="margin-right:4px;"></i>';
        $html .= esc_html__('Standard', 'blog2social');
        if ($isFreeUser) {
            $html .= ' <span class="label label-success b2s-pro-badge-template">SMART</span>';
        }
        $html .= '</button>';
        $html .= '<button type="button" class="b2s-edit-template-mode-btn b2s-edit-template-mode-btn-ai" data-mode="ai">';
        $html .= '<i class="fa-solid fa-wand-magic-sparkles" style="margin-right:4px;"></i>';
        $html .= esc_html__('Assistini AI Template', 'blog2social');
        $html .= '</button>';
        $html .= '</div>';
        $html .= '<span class="b2s-ass-register-btn b2s-ass-connected btn-success-assistini-connected b2s-ai-ass-connected-indicator" style="display:' . ($assConnected ? 'inline-block' : 'none') . ';">' . esc_html($connectedLabel) . '</span>';
        $html .= '</div>';
        $html .= '<br><br>';
        $b2s_gai_disp_bold = __('displayed content', 'blog2social');
        /* translators: %1$s is bold text */
        $b2s_gai_disp_rest = __('This is the %1$s, such as a summary or an edited version of the blog post.', 'blog2social');
        $b2s_gai_disp_full = wp_kses(sprintf(esc_html($b2s_gai_disp_rest), sprintf('<strong>%s</strong>', esc_html($b2s_gai_disp_bold))), array('strong' => array()));

        $b2s_gai_orig_bold = __('original blog post', 'blog2social');
        /* translators: %1$s is bold text */
        $b2s_gai_orig_rest = __('This is the %1$s in full length with all original sections, paragraphs, and formatting.', 'blog2social');
        $b2s_gai_orig_full = wp_kses(sprintf(esc_html($b2s_gai_orig_rest), sprintf('<strong>%s</strong>', esc_html($b2s_gai_orig_bold))), array('strong' => array()));

        $html .= '<div class="tab-content clearfix">';

        $html .= '<div class="b2s-edit-template-ai-content-connect-gate b2s-ai-template-connect-gate" style="display:none; margin-bottom:20px;">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12 media-heading">';
        $html .= '<span class="b2s-edit-template-section-headline-first">' . esc_html__('Connect Assistini AI', 'blog2social') . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<div class="alert alert-info">';
        $html .= esc_html__('To use AI post templates, connect your account with Assistini AI. The setup follows three short steps.', 'blog2social');
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<div class="b2s-stepwizard b2s-ai-ass-stepwizard">';
        $html .= '<div class="b2s-stepwizard-row setup-panel">';
        $html .= '<div class="b2s-stepwizard-step b2s-ass-stepwizard-step">';
        $html .= '<a href="#" type="button" class="btn btn-danger b2s-ass-color b2s-stepwizard-btn-circle b2s-ai-ass-step-circle" data-step="1" disabled="disabled">1</a>';
        $html .= '<p>' . esc_html__('Enter email address', 'blog2social') . '</p>';
        $html .= '</div>';
        $html .= '<div class="b2s-stepwizard-step b2s-ass-stepwizard-step">';
        $html .= '<a href="#" type="button" class="btn btn-default b2s-stepwizard-btn-circle b2s-ai-ass-step-circle" data-step="2" disabled="disabled">2</a>';
        $html .= '<p>' . esc_html__('Verify email address', 'blog2social') . '</p>';
        $html .= '</div>';
        $html .= '<div class="b2s-stepwizard-step">';
        $html .= '<a href="#" type="button" class="btn btn-default b2s-stepwizard-btn-circle b2s-ai-ass-step-circle" data-step="3" disabled="disabled">3</a>';
        $html .= '<p>' . esc_html__('Ready to go!', 'blog2social') . '</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="row b2s-ai-ass-auth-step-1-content">';
        $html .= '<div class="col-md-12">';
        $html .= '<br>';
        $html .= '<br>';
        $html .= '<p><b>' . esc_html__('Use this email for verification', 'blog2social') . '</b></p>';
        $html .= '<div>';
        $html .= '<input type="radio" value="0" class="b2s-ai-ass-auth-email-option" id="b2s-ai-ass-auth-email-own[global]" data-auth-email="' . esc_attr($userEmail) . '" name="b2s-ai-ass-auth-email[global]" checked />';
        $html .= '<label for="b2s-ai-ass-auth-email-own[global]"> ' . esc_html($userEmail) . '</label>';
        $html .= '<br>';
        $html .= '<input type="radio" value="1" class="b2s-ai-ass-auth-email-option" id="b2s-ai-ass-auth-email-other[global]" name="b2s-ai-ass-auth-email[global]">';
        $html .= '<label for="b2s-ai-ass-auth-email-other[global]"> ' . esc_html__('Use different email for verification', 'blog2social') . '</label>';
        $html .= '</div>';
        $html .= '<div class="text-right">';
        $html .= '<button type="button" data-url="' . esc_url($authUrl) . '" data-auth-title="' . esc_attr__('Assistini Authorization', 'blog2social') . '" class="btn b2s-ass-btn b2s-ai-ass-auth-step1-btn">' . esc_html__('Send verification code', 'blog2social') . '</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="row b2s-ai-ass-auth-step-3-content" style="display:none;">';
        $html .= '<div class="col-md-12">';
        $html .= '<h4>' . esc_html__('Connection established', 'blog2social') . '</h4>';
        $html .= '<p>' . esc_html__('Congrats! You can now start using Assistini AI to optimize your social media posts.', 'blog2social') . '</p>';
        $html .= '<div class="text-right">';
        $html .= '<button type="button" class="btn b2s-ass-btn b2s-ai-ass-auth-step3-btn">' . esc_html__('Start now', 'blog2social') . '</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<hr>';
        $html .= '<br>';
        $html .= '</div>';

        $html .= '<div class="b2s-edit-template-ai-content b2s-global-ai-settings-section" style="display:none; position:relative;">';
        if (!$assConnected) {
            $html .= '<div id="b2s-global-ai-settings-not-connected-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:10;background:rgba(255,255,255,0.6);border-radius:8px;"></div>';
        }
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12 media-heading">';
        $html .= '<span class="b2s-edit-template-section-headline-first">' . esc_html__('Basic AI Settings', 'blog2social') . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<div class="b2s-pt-1">';
        $html .= '<div class="b2s-global-ai-settings-checkbox-row"><input type="checkbox" class="ai-template-form-element" id="b2s-global-ai-settings-checkbox-1" /> <label for="b2s-global-ai-settings-checkbox-1" class="b2s-global-ai-settings-checkbox-label">' . esc_html__('Apply standard templates', 'blog2social') . '</label></div>';
        $html .= '<div class="b2s-global-ai-settings-checkbox-row"><input type="checkbox" class="ai-template-form-element" id="b2s-global-ai-settings-checkbox-2" /> <label for="b2s-global-ai-settings-checkbox-2" class="b2s-global-ai-settings-checkbox-label">' . esc_html__('Exclude emojis', 'blog2social') . '</label></div>';
        $html .= '<div class="b2s-global-ai-settings-checkbox-row"><input type="checkbox" class="ai-template-form-element" id="b2s-global-ai-settings-checkbox-3" /> <label for="b2s-global-ai-settings-checkbox-3" class="b2s-global-ai-settings-checkbox-label">' . esc_html__('Generate Hashtags', 'blog2social') . '</label></div>';
        $html .= '<div id="b2s-global-ai-settings-checkbox-3-conditional-text" hidden>' . esc_html__('(is defined in post templates)', 'blog2social') . '</div>';
        $html .= '<div class="b2s-ai-template-enable-wrapper b2s-global-ai-settings-toggle-wrapper">';
        $html .= '<label class="b2s-ai-template-enable-switch" for="b2s-global-ai-settings-checkbox-4">';
        $html .= '<input class="b2s-global-ai-settings-checkbox-4 ai-template-form-element" type="checkbox" id="b2s-global-ai-settings-checkbox-4" checked>';
        $html .= '<span class="b2s-ai-template-enable-slider"></span>';
        $html .= '</label>';
        $html .= '<div>';
        $html .= '<span class="b2s-ai-template-enable-label toggle-label-b2s-global-ai-settings-checkbox-4-displayed-content" id="toggle-label-b2s-global-ai-settings-checkbox-4-displayed-content">' . esc_html__('Displayed content', 'blog2social') . '</span>';
        $html .= '<span class="b2s-ai-template-enable-label toggle-label-b2s-global-ai-settings-checkbox-4-original-content" id="toggle-label-b2s-global-ai-settings-checkbox-4-original-content" style="display:none;">' . esc_html__('Original Blog Post', 'blog2social') . '</span>';
        $html .= '<div class="b2s-global-ai-settings-checkbox-4-displayed-content-checked">' . $b2s_gai_disp_full . '</div>';
        $html .= '<div class="b2s-global-ai-settings-checkbox-4-original-content-checked" style="display:none;">' . $b2s_gai_orig_full . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        if (count($defaultSchema) > 1) {
            $html .= '<div class="b2s-tabs-nav-container">';
            $html .= '<div class="">';
            $html .= '<ul class="nav nav-pills">';
            $html .= '<li class="active"><a href="#b2s-template-profile" class="b2s-template-profile" data-toggle="tab">' . esc_html__('Profile', 'blog2social') . '</a></li>';
            if (isset($defaultSchema[1]) && !empty($defaultSchema[1]) && $networkId != 11) {
                $html .= '<li><a href="#b2s-template-page" class="b2s-template-page" data-toggle="tab">' . esc_html__('Page', 'blog2social') . '</a></li>';
            }
            if (isset($defaultSchema[2]) && !empty($defaultSchema[2])) {
                $html .= '<li><a href="#b2s-template-group" class="b2s-template-group" data-toggle="tab">' . esc_html__('Group', 'blog2social') . '</a></li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
            if ($networkId == 1 || $networkId == 3 || $networkId == 19) {
                $linkNoCache = B2S_Tools::getNoCacheData(B2S_PLUGIN_BLOG_USER_ID);
                $html .= '<div class="pull-right b2s-edit-template-no-cache-area"><input id="link-no-cache" type="checkbox" class="standard-template-form-element" ' . ((isset($linkNoCache[$networkId]) && $linkNoCache[$networkId] == 1) ? 'checked' : '') . ' name="no_cache"> <label for="link-no-cache">' . esc_html__('Activate Instant Caching', 'blog2social') . '</label> <a href="#" class="b2s-info-btn vertical-middle del-padding-left b2sInfoNoCacheBtn">' . esc_html__('Info', 'blog2social') . '</a></div>';
            }
        }

        if (isset($defaultSchema[0]) && !empty($defaultSchema[0])) {
            $html .= '<div class="tab-pane active b2s-template-tab-0" id="b2s-template-profile">';
            $html .= $this->getEditTemplateFormContent($networkId, 0, $schema, $isFreeUser);
            $html .= '</div>';
        }
        if (isset($defaultSchema[1]) && !empty($defaultSchema[1])) {
            $html .= '<div class="tab-pane  b2s-template-tab-1 ' . ((!isset($defaultSchema[0]) || empty($defaultSchema[0])) ? 'active' : '') . '" id="b2s-template-page">';
            $html .= $this->getEditTemplateFormContent($networkId, 1, $schema, $isFreeUser);
            $html .= '</div>';
        }
        if (isset($defaultSchema[2]) && !empty($defaultSchema[2])) {
            $html .= '<div class="tab-pane b2s-template-tab-2" id="b2s-template-group">';
            $html .= $this->getEditTemplateFormContent($networkId, 2, $schema, $isFreeUser);
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function getEditTemplateFormContent($networkId, $networkType, $schema, $isFreeUser = false) {
    
        $defaultTemplate = unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT);
        //V6.5.5 => Two different kinds of Xing Pages
        $multi_kind = false;
        if ($networkId == 19 && $networkType == 1) {
            $multi_kind = true;
            if (!isset($schema[$networkType]['short_text'][0]['limit'])) {
                if (isset($schema[$networkType]['short_text']['limit'])) {
                    $schema[$networkType]['short_text'] = array(0 => $schema[$networkType]['short_text'], 4 => $defaultTemplate[$networkId][$networkType]['short_text'][4]);
                }
            }
        }

        //V5.6.1
        $limit=0;
        if (!$multi_kind) {
            $limit = ((isset($defaultTemplate[$networkId][$networkType]['short_text']['limit'])) ? $defaultTemplate[$networkId][$networkType]['short_text']['limit'] : 0);
            if (!isset($schema[$networkType]['short_text']['excerpt_range_max']) && isset($defaultTemplate[$networkId][$networkType]['short_text']['excerpt_range_max'])) {
                $schema[$networkType]['short_text']['excerpt_range_max'] = $defaultTemplate[$networkId][$networkType]['short_text']['excerpt_range_max'];
            }
        }

      
        $aiTemplate = B2S_Tools::getAiTemplateSchema($schema, $networkType, (int) $networkId);
        $dbAiTemplate = B2S_Tools::getAiTemplateDbSchema((int) B2S_PLUGIN_BLOG_USER_ID, (int) $networkId, (int) $networkType);
        if (is_array($dbAiTemplate)) {
            $aiTemplate = array_merge($aiTemplate, $dbAiTemplate);
        }

        $content = '<div class="b2s-edit-template-standard-content" style="position:relative;">';
        if ($isFreeUser) {
            $content .= '<div class="b2s-standard-template-free-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:10;cursor:pointer;background:rgba(255,255,255,0.55);"></div>';
        }

        if ($schema[$networkType]['format'] !== false || $networkId == 4) { 
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12 media-heading">';
            $content .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Format', 'blog2social') . '</span> <a href="#" data-network-id="' . esc_attr($networkId) . '" class="b2s-info-btn del-padding-left b2sInfoFormatBtn">' . esc_html__('Info', 'blog2social') . '</a>';
            $content .= '<button class="pull-right btn btn-primary btn-xs b2s-edit-template-load-default" data-network-type="' . esc_attr($networkType) . '">' . esc_html__('Load default settings', 'blog2social') . '</button>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';

            //Tumblr Post Formats are different because of text/html
            if($networkId == 4 ){

                if ($schema[$networkType]['format'] == 0 || $schema[$networkType]['format'] == false) {

                    $content .= '<button class="btn btn-primary btn-sm b2s-edit-template-text-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html( __('Text', 'blog2social')) .'</button>';
                    $content .= '<button class="btn btn-light btn-sm b2s-edit-template-link-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Link', 'blog2social') : __('Image with frame', 'blog2social'))) . '</button>';
                    $content .= '<button class="btn btn-light btn-sm b2s-edit-template-image-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Image', 'blog2social') : __('Image cut out', 'blog2social'))) . '</button>';
                }
                
                if($schema[$networkType]['format'] == 1) {
                    
                    $content .= '<button class="btn btn-light btn-sm b2s-edit-template-text-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html( __('Text', 'blog2social')) .'</button>';
                    $content .= '<button class="btn btn-light btn-sm b2s-edit-template-link-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Link', 'blog2social') : __('Image with frame', 'blog2social'))) . '</button>';
                    $content .= '<button class="btn btn-primary btn-sm b2s-edit-template-image-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Image', 'blog2social') : __('Image cut out', 'blog2social'))) . '</button>';
                }

                if($schema[$networkType]['format'] == 3) {
                    $content .= '<button class="btn btn-light btn-sm b2s-edit-template-text-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html( __('Text', 'blog2social')) .'</button>';
                    $content .= '<button class="btn btn-primary btn-sm b2s-edit-template-link-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Link', 'blog2social') : __('Image with frame', 'blog2social'))) . '</button>';
                    $content .= '<button class="btn btn-light btn-sm b2s-edit-template-image-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Image', 'blog2social') : __('Image cut out', 'blog2social'))) . '</button>';
                }
            }else{
                if ($schema[$networkType]['format'] == 0) {

                    $content .= '<button class="btn btn-primary btn-sm b2s-edit-template-link-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Link', 'blog2social') : __('Image with frame', 'blog2social'))) . '</button>';
                    $content .= '<button class="btn btn-light btn-sm b2s-edit-template-image-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Image', 'blog2social') : __('Image cut out', 'blog2social'))) . '</button>';
            
                } else if($schema[$networkType]['format'] == 1) {

                    $content .= '<button class="btn btn-light btn-sm b2s-edit-template-link-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Link', 'blog2social') : __('Image with frame', 'blog2social'))) . '</button>';
                    $content .= '<button class="btn btn-primary btn-sm b2s-edit-template-image-post pull-left" data-network-id="'.esc_attr($networkId).'" data-network-type="' . esc_attr($networkType) . '">' . esc_html((($networkId != 12) ? __('Image', 'blog2social') : __('Image cut out', 'blog2social'))) . '</button>';
            
                }
            }
        
            $content .= '<input type="hidden" class="b2s-edit-template-post-format" value="' . esc_attr($schema[$networkType]['format']) . '" data-network-type="' . esc_attr($networkType) . '">';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<br>';
        }

        $content .= '<div class="edit-template-content">';

        $content .= '<div class="row">';
        $content .= '<div class="col-md-12 media-heading">';
        $content .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Content', 'blog2social') . '</span> <a href="#" class="b2s-info-btn del-padding-left b2sInfoContentBtn">' . esc_html__('Info', 'blog2social') . '</a>';
        if ($schema[$networkType]['format'] === false) {
            $content .= '<button class="pull-right btn btn-primary btn-xs b2s-edit-template-load-default" data-network-type="' . esc_attr($networkType) . '">' . esc_html__('Load default settings', 'blog2social') . '</button>';
        }
        $content .= '</div>';
        $content .= '</div>';
        if ($networkId == 12) {
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';
            $content .= '<div class="alert alert-warning b2s-edit-template-hashtag-warning" style="display:none;">' . esc_html__('Good to know: Instagram supports up to 30 hashtags in a post. The number recommended for best results is 5 hashtags. Make sure that your hashtags are thematically relevant to the content of your post.', 'blog2social') . '</div>';
            $content .= '</div>';
            $content .= '</div>';
        }
        $content .= '<div class="row">';
        $content .= '<div class="col-md-12">';

        $woocommerce_active = false;
        if (class_exists('WooCommerce') && function_exists('wc_get_product')) {
            $woocommerce_active = true;
        }
        $content .= '<div class="b2s-padding-bottom-5">'
                . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-title b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{TITLE}</button>'
                . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-excerpt b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{EXCERPT}</button>'
                . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-content b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{CONTENT}</button>'
                . ((isset($defaultTemplate[$networkId][$networkType]['disableKeywords']) && $defaultTemplate[$networkId][$networkType]['disableKeywords'] == true) ? '' : '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-keywords b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{KEYWORDS}</button>')
                . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-author b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{AUTHOR}</button>'
                . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-url b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{URL}</button>'
                . (($woocommerce_active) ? '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-price b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{PRICE}</button>' : '')
                . (($woocommerce_active) ? '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-regular-price b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{REGULAR_PRICE}</button>' : '')
                . (($woocommerce_active) ? '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-sale-price b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{SALE_PRICE}</button>' : '');


        $b2sHook = new B2S_Hook_Filter();
        $additionalTaxonomies = $b2sHook->get_posting_template_show_taxonomies();
        if (is_array($additionalTaxonomies) && !empty($additionalTaxonomies)) {
            foreach ($additionalTaxonomies as $k => $taxonomie) {
                $content .= '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-content-post-' . esc_html($taxonomie) . ' b2s-edit-template-content-post-item" data-network-type="' . esc_attr($networkType) . '">{' . esc_html($taxonomie) . '}</button>';
            }
        }

        $content .= '<button type="button" class="btn btn-primary btn-xs b2s-edit-template-content-clear-btn pull-right" data-network-type="' . esc_attr($networkType) . '">' . esc_html__('clear', 'blog2social') . '</button>'
                . '</div>';
        $content .= '<textarea class="b2s-edit-template-post-content standard-template-form-element" style="width: 100%;" data-network-type="' . esc_attr($networkType) . '" ' . ((B2S_PLUGIN_USER_VERSION < 1) ? 'readonly="true"' : '') . ' ' . (($limit > 0) ? 'maxlength="' . $limit . '"' : '') . '>' . esc_html(stripslashes($schema[$networkType]['content'])) . '</textarea>';
        $content .= '<input class="b2s-edit-template-content-selection-start" data-network-type="' . esc_attr($networkType) . '" type="hidden" value="0">';
        $content .= '<input class="b2s-edit-template-content-selection-end" data-network-type="' . esc_attr($networkType) . '" type="hidden" value="0">';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-12 b2s-edit-template-link-info">';
        if ($networkId != 1 && $networkId != 2 && $networkId != 3 && $networkId != 4 && $networkId != 12 &&  $networkId != 43 && $networkId != 44  && $networkId !=45 ) {
            if ($networkId == 11) {
                $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html__('The link will be transmitted as a canonical link, i.e. in the source code of your page, in order to refer to the original source of the content and to increase the reach from search engines like Google.', 'blog2social');
            } else if ($networkId == 39) {
                $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html__('The title of the post will serve as the link.', 'blog2social');
            } else {
                $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html__('The link will be added automatically at the end of the post.', 'blog2social');
            }
            $content .= '<br>';
        }

        if($networkId == 4){
            $content .= '<div '.($schema[$networkType]['format'] == 2 ? '':  ' style="display:none;"').' class="tumblr-link-post-notice">';
            $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html__('Tumblr shortens the description text of a link post after approximately 125 characters.', 'blog2social');
            $content .= '<br>';
            $content .= '</div>';
        }

        if (isset($defaultTemplate[$networkId][$networkType]['disableKeywords']) && $defaultTemplate[$networkId][$networkType]['disableKeywords'] == true) {
            $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html__('The network does not support hashtags.', 'blog2social');
            $content .= '<br>';
        }
        if (isset($defaultTemplate[$networkId][$networkType]['separateKeywords']) && $defaultTemplate[$networkId][$networkType]['separateKeywords'] == true) {
            $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html__('This social network displays the predefined hashtags as clickable tags at the end of your post.', 'blog2social');
            $content .= '<br>';
        }
        if ((int) $limit != 0) {
            $content .= '<input type="hidden" class="b2s-edit-template-limit" value="' . esc_attr($limit) . '">';
            $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html(__('Network limit', 'blog2social') . ': ' . esc_html($limit) . ' ' . __('characters', 'blog2social'));
        }
        $content .= '</div>';
        $content .= '</div>';
        if ($networkId == 1 || $networkId == 2 || $networkId == 3 || $networkId == 43 || $networkId == 45) {
            $content .= '<div class="row b2s-edit-template-enable-link-area" style="display:' . (($schema[$networkType]['format'] == 1) ? 'block' : 'none') . '" data-network-type="' . esc_attr($networkType) . '">';
            $content .= '<div class="col-md-12">';
            $content .= '<input class="b2s-edit-template-enable-link standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" type="checkbox" ' . ((isset($schema[$networkType]['addLink']) && $schema[$networkType]['addLink'] == false) ? '' : 'checked="checked"') . ' id="b2s-edit-template-enable-link[' . esc_attr($networkType) . ']"><label for="b2s-edit-template-enable-link[' . esc_attr($networkType) . ']"> ' . esc_html__('Add a link-URL to the end of my image post.', 'blog2social') . '</label>';
            $content .= '</div>';
            $content .= '</div>';
        }
        if ($networkId == 1 && $networkType == 1) {
            $content .= '<div class="row b2s-edit-template-share-as-story-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display:' . ((isset($schema[$networkType]['format']) && (int) $schema[$networkType]['format'] === 1) ? 'block' : 'none') . '">';
            $content .= '<div class="col-md-12">';
            $content .= '<input type="checkbox" class="b2s-edit-template-share-as-story b2s-post-item-option-share-as-story b2s-post-item-option-share-type standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="1" id="b2s-edit-template-share-as-story[' . esc_attr($networkType) . ']" ' . ((isset($schema[$networkType]['share_as_story']) && (int) $schema[$networkType]['share_as_story'] === 1) ? 'checked="checked"' : '') . '> ';
            $content .= '<label for="b2s-edit-template-share-as-story[' . esc_attr($networkType) . ']">' . esc_html__("Share as Story", "blog2social") . '</label> <a href="#" class="btn btn-link btn-sm b2s-info-share-as-story-modal-btn">' . esc_html__("Info", "blog2social") . '</a>';
            $content .= '</div>';
            $content .= '</div>';
        }
        if ($networkId == 2 || $networkId == 45) {
            $threadChecked = '';
            if (isset($schema[$networkType]['twitterThreads'])) {
                $threadChecked = $schema[$networkType]['twitterThreads'] ? 'checked="checked"' : '';
            }

            $content .= '<input type="checkbox" ' . $threadChecked . ' class="b2s-twitter-thread-template standard-template-form-element" id="b2s-twitter-thread[' . esc_attr($networkType) . ']" name="b2s-twitter-thread" data-network-type="' . esc_attr($networkType) . '" >';
            $content .= '<label for="b2s-twitter-thread[' . esc_attr($networkType) . ']"> ' . esc_html__('Use X threads for posts with more than 280 characters.', 'blog2social') . '</label>';
        }
        if ($networkId == 12) {
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';
            $content .= '<input class="b2s-edit-template-enable-link standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" type="checkbox" ' . ((isset($schema[$networkType]['addLink']) && $schema[$networkType]['addLink'] == false) ? '' : 'checked="checked"') . ' id="b2s-edit-template-enable-link"><label for="b2s-edit-template-enable-link"> ' . esc_html__('Add a link-URL to the end of my Instagram posts. (Please note, that Instagram does not turn link-URLs into clickable links)', 'blog2social') . '</label>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';
            $content .= '<input class="b2s-edit-template-shuffle-hashtags standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" type="checkbox" ' . ((isset($schema[$networkType]['shuffleHashtags']) && $schema[$networkType]['shuffleHashtags'] == true) ? 'checked="checked"' : '') . ' id="b2s-edit-template-shuffle-hashtags"><label for="b2s-edit-template-shuffle-hashtags"> ' . esc_html__('Hashtag shuffle (Hashtags have to be defined in the text field above)', 'blog2social') . '</label>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';
            $content .= '<input type="checkbox" class="b2s-edit-template-share-as-story b2s-post-item-option-share-as-story b2s-post-item-option-share-type standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="1" id="b2s-edit-template-share-as-story[' . esc_attr($networkType) . ']" ' . ((isset($schema[$networkType]['share_as_story']) && (int) $schema[$networkType]['share_as_story'] === 1) ? 'checked="checked"' : '') . '> ';
            $content .= '<label for="b2s-edit-template-share-as-story[' . esc_attr($networkType) . ']">' . esc_html__("Share as Story", "blog2social") . '</label> <a href="#" class="btn btn-link btn-sm b2s-info-share-as-story-modal-btn">' . esc_html__("Info", "blog2social") . '</a>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<br>';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';
            $content .= esc_html__('Frame colour:', 'blog2social') . ' <input id="b2s-edit-template-colorpicker" type="text" class="b2s-edit-template-colorpicker standard-template-form-element" value="' . ((isset($schema[$networkType]['frameColor']) && !empty($schema[$networkType]['frameColor'])) ? esc_attr($schema[$networkType]['frameColor']) : '#ffffff') . '">';
            $content .= '</div>';
            $content .= '</div>';
        }
        $content .= '<br>';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-12 media-heading">';
        $content .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Character limit', 'blog2social') . ' (CONTENT, EXCERPT)</span> <a href="#" class="b2s-info-btn del-padding-left b2sInfoCharacterLimitBtn">' . esc_html__('Info', 'blog2social') . '</a>';
        $content .= '</div>';
        $content .= '</div>';
        if (!function_exists('mb_strlen')) {
            $content .= '<div class="alert alert-warning">' . esc_html__('Missing PHP "mbstring" extension to use the character limit function. Please activate server-side the PHP "mbstring" extension in your "php.ini" file.', 'blog2social') . '</div>';
        }
        if (!$multi_kind) {
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';
            $content .= '<div class="form-group">';
            $content .= '<label class="col-sm-2 control-label b2s-edit-template-character-limit-label">{CONTENT}</label> <input type="number" class="b2s-edit-template-range standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr($schema[$networkType]['short_text']['range_max']) . '" min="1" max="' . esc_attr((($schema[$networkType]['short_text']['limit']) ? $schema[$networkType]['short_text']['limit'] : '')) . '" ' . ((B2S_PLUGIN_USER_VERSION < 1) ? 'readonly="true"' : '') . '>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';
            $content .= '<div class="form-group">';
            $content .= '<label class="col-sm-2 control-label b2s-edit-template-character-limit-label">{EXCERPT}</label> <input type="number" class="b2s-edit-template-excerpt-range standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr($schema[$networkType]['short_text']['excerpt_range_max']) . '" min="1" max="' . esc_attr((($schema[$networkType]['short_text']['limit']) ? $schema[$networkType]['short_text']['limit'] : '')) . '" ' . ((B2S_PLUGIN_USER_VERSION < 1) ? 'readonly="true"' : '') . '>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
            if (!in_array($networkId, array(4, 11, 14, 16, 25))) { //V6.7 don't show recommended length for HTML Networks
                $content .= '<div class="row">';
                $content .= '<div class="col-md-12 b2s-edit-template-link-info">';
                $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html(__('recommended length', 'blog2social') . ': ' . $defaultTemplate[$networkId][$networkType]['short_text']['range_min'] . ' ' . __('characters', 'blog2social') . (((int) $limit != 0) ? '; ' . __('Network limit', 'blog2social') . ': ' . $limit . ' ' . __('characters', 'blog2social') : ''));
                $content .= '</div>';
                $content .= '</div>';
            }
            $content .= '<hr>';
            $content .= '<br>';
        } else {
            $networkKindName = unserialize(B2S_PLUGIN_NETWORK_KIND);
            foreach ($schema[$networkType]['short_text'] as $kind_id => $short_text) {
                $deprecated = ((isset($defaultTemplate[$networkId][$networkType]['short_text'][$kind_id]['deprecated_date']) && $defaultTemplate[$networkId][$networkType]['short_text'][$kind_id]['deprecated_date'] <  wp_date('Y-m-d', null, new DateTimeZone(date_default_timezone_get()))) ? true : false);
                if ($deprecated) {
                    $content .= '<div style="display: none;">';
                }
                $content .= '<div class="row">';
                $content .= '<div class="col-md-12">';
                $content .= '<div class="form-group">';
                $content .= '<br><span class="b2s-bold">' . ((isset($networkKindName[$kind_id])) ? esc_html($networkKindName[$kind_id]) : '') . '</span>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-12">';
                $content .= '<div class="form-group">';
                $content .= '<label class="col-sm-2 control-label b2s-edit-template-character-limit-label">{CONTENT}</label> <input type="number" class="b2s-edit-template-range standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" data-network-type-kind="' . esc_attr($kind_id) . '" value="' . esc_attr($short_text['range_max']) . '" min="1" max="' . esc_attr((($short_text['limit']) ? $short_text['limit'] : '')) . '" ' . ((B2S_PLUGIN_USER_VERSION < 1) ? 'readonly="true"' : '') . '>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-12">';
                $content .= '<div class="form-group">';
                $content .= '<label class="col-sm-2 control-label b2s-edit-template-character-limit-label">{EXCERPT}</label> <input type="number" class="b2s-edit-template-excerpt-range standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" data-network-type-kind="' . esc_attr($kind_id) . '" value="' . esc_attr($short_text['excerpt_range_max']) . '" min="1" max="' . esc_attr((($short_text['limit']) ? $short_text['limit'] : '')) . '" ' . ((B2S_PLUGIN_USER_VERSION < 1) ? 'readonly="true"' : '') . '>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-12 b2s-edit-template-link-info">';
                $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html(__('recommended length', 'blog2social') . ': ' . esc_html($defaultTemplate[$networkId][$networkType]['short_text'][$kind_id]['range_min']) . ' ' . esc_html__('characters', 'blog2social') . (((int) $short_text['limit'] != 0) ? '; ' . __('Network limit', 'blog2social') . ': ' . $short_text['limit'] . ' ' . __('characters', 'blog2social') : ''));
                $content .= '</div>';
                $content .= '</div>';
                if ($deprecated) {
                    $content .= '</div>';
                }
            }
            $content .= '<hr>';
            $content .= '<br>';
        }
        $content .= '<input type="hidden" name="b2s-edit-template-multi-kind" class="b2s-edit-template-multi-kind" data-network-type="' . esc_attr($networkType) . '" value="' . (($multi_kind) ? 1 : 0) . '">';

        // Add default comment text field for networks that support comments (before preview)
        if(B2S_Tools::isCommentAllowed($networkId, $networkType) ){
            if(defined('B2S_PLUGIN_USER_VERSION') && B2S_PLUGIN_USER_VERSION >= 2){

            
            $CommentValue = (isset($schema[$networkType]['comment']) && !empty($schema[$networkType]['comment'])) ? esc_textarea($schema[$networkType]['comment']) : '';
            $commentLimit = ((isset($defaultTemplate[$networkId][$networkType]['short_comment']['limit'])) ? $defaultTemplate[$networkId][$networkType]['short_comment']['limit'] : 0);
            
            $content .= '<div class="b2s-comment-settings">';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12 media-heading">';
            $content .= '<span class="b2s-edit-template-section-headline">' . esc_html__('First Comment', 'blog2social') . '</span>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12">';
            $content .= '<label><i class="glyphicon glyphicon-comment"></i> ' . esc_html__('Define a first comment that will be pre-filled when posting', 'blog2social') . '</label>';
            $content .= '<div class="b2s-padding-bottom-5">'
                    . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-title b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{TITLE}</button>'
                    . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-excerpt b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{EXCERPT}</button>'
                    . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-content b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{CONTENT}</button>'
                    . ((isset($defaultTemplate[$networkId][$networkType]['disableKeywords']) && $defaultTemplate[$networkId][$networkType]['disableKeywords'] == true) ? '' : '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-keywords b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{KEYWORDS}</button>')
                    . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-author b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{AUTHOR}</button>'
                    . '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-url b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{URL}</button>'
                    . (($woocommerce_active) ? '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-price b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{PRICE}</button>' : '')
                    . (($woocommerce_active) ? '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-regular-price b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{REGULAR_PRICE}</button>' : '')
                    . (($woocommerce_active) ? '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-sale-price b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{SALE_PRICE}</button>' : '');
            
            if (is_array($additionalTaxonomies) && !empty($additionalTaxonomies)) {
                foreach ($additionalTaxonomies as $k => $taxonomie) {
                    $content .= '<button type="button" draggable="true" class="btn btn-primary btn-xs b2s-edit-template-comment-post-' . esc_html($taxonomie) . ' b2s-edit-template-comment-post-item" data-network-type="' . esc_attr($networkType) . '">{' . esc_html($taxonomie) . '}</button>';
                }
            }
            
            $content .= '<button type="button" class="btn btn-primary btn-xs b2s-edit-template-comment-clear-btn pull-right" data-network-type="' . esc_attr($networkType) . '">' . esc_html__('clear', 'blog2social') . '</button>'
                    . '</div>';
            $content .= '<textarea class="form-control b2s-edit-template-comment standard-template-form-element" rows="3" data-network-type="' . esc_attr($networkType) . '" placeholder="' . esc_attr__('Enter first comment...', 'blog2social') . '" ' . (($commentLimit > 0) ? 'maxlength="' . $commentLimit . '"' : '') . ' name="b2s-edit-template-comment[' . esc_attr($networkType) . ']">' . $CommentValue . '</textarea>';
            $content .= '<input class="b2s-edit-template-comment-selection-start" data-network-type="' . esc_attr($networkType) . '" type="hidden" value="0">';
            $content .= '<input class="b2s-edit-template-comment-selection-end" data-network-type="' . esc_attr($networkType) . '" type="hidden" value="0">';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<br>';
            $content .= '<div class="row">';
            $content .= '<div class="col-md-12 media-heading">';
            $content .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Character limit', 'blog2social') . ' (CONTENT, EXCERPT)</span>';
            $content .= '</div>';
            $content .= '</div>';
            if (!function_exists('mb_strlen')) {
                $content .= '<div class="alert alert-warning">' . esc_html__('Missing PHP "mbstring" extension to use the character limit function. Please activate server-side the PHP "mbstring" extension in your "php.ini" file.', 'blog2social') . '</div>';
            }
            if (isset($schema[$networkType]['short_comment'])) {
                $content .= '<div class="row">';
                $content .= '<div class="col-md-12">';
                $content .= '<div class="form-group">';
                $content .= '<label class="col-sm-2 control-label b2s-edit-template-character-limit-label">{CONTENT}</label> <input type="number" class="b2s-edit-template-range-comment standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr($schema[$networkType]['short_comment']['range_max']) . '" min="1" max="' . esc_attr((($schema[$networkType]['short_comment']['limit']) ? $schema[$networkType]['short_comment']['limit'] : '')) . '" ' . ((B2S_PLUGIN_USER_VERSION < 1) ? 'readonly="true"' : '') . '>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-12">';
                $content .= '<div class="form-group">';
                $content .= '<label class="col-sm-2 control-label b2s-edit-template-character-limit-label">{EXCERPT}</label> <input type="number" class="b2s-edit-template-excerpt-range-comment standard-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr($schema[$networkType]['short_comment']['excerpt_range_max']) . '" min="1" max="' . esc_attr((($schema[$networkType]['short_comment']['limit']) ? $schema[$networkType]['short_comment']['limit'] : '')) . '" ' . ((B2S_PLUGIN_USER_VERSION < 1) ? 'readonly="true"' : '') . '>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '<div class="row">';
                $content .= '<div class="col-md-12 b2s-edit-template-link-info">';
                $content .= '<i class="glyphicon glyphicon-info-sign"></i> ' . esc_html(__('recommended length', 'blog2social') . ': ' . $defaultTemplate[$networkId][$networkType]['short_comment']['range_min'] . ' ' . __('characters', 'blog2social') . (((int) $commentLimit != 0) ? '; ' . __('Comment character limit', 'blog2social') . ': ' . $commentLimit . ' ' . __('characters', 'blog2social') : ''));
                $content .= '</div>';
                $content .= '</div>';
            }
            if ((int) $commentLimit != 0) {
                $content .= '<input type="hidden" class="b2s-edit-template-comment-limit" value="' . esc_attr($commentLimit) . '">';
            }
            $content .= '<hr>';
            $content .= '<br>';
            $content .= '</div>';
            }else{
                //Show dummy toggle for first comment for non-PRO users
                $content .= '<div class="b2s-toggle-comment-wrapper">';
                $content .= '<label class="b2s-toggle-switch b2sProFeatureAddCommentModal">';
                $content .= '<input type="checkbox" class="b2s-toggle-comment"disabled="">';
                $content .= '<span class="b2s-toggle-slider"></span></label><span class="b2s-toggle-label" style="margin-right: 3px;">'.esc_html__("First comment", "blog2social").'</span>';
                $content .= '<span class="label label-success"><a class="btn-label-premium b2sProFeatureAddCommentModal">' . esc_html__("PRO", "blog2social") .'</a></span>';
                $content .=  '<a class"b2s-toggle-comment-info" href="'.B2S_Tools::getSupportLink('faq_first_comment') . ' "class="btn btn-xs hidden-xs">'.esc_html__("Info", "blog2social").'</a>';
                $content .= '</div>';
            }
        }

        $content .= $this->networkPreview($networkId, $networkType, $schema);

        $content .= '</div>';
        $content .= '</div>';
        $assConnected = $this->isAssistiniConnected();
        $content .= '<div class="b2s-edit-template-ai-content" style="display:none;">';
        if (!$assConnected || $isFreeUser) {
            $content .= '<div style="position:relative;">';
            $content .= $this->getAiTemplateFormContent($networkId, $networkType, $aiTemplate, $isFreeUser);
            if ($isFreeUser) {
                $content .= '<div class="b2s-ai-template-free-overlay"></div>';
            }
            if (!$assConnected) {
                $content .= '<div class="b2s-ai-template-not-connected-overlay"></div>';
            }
            $content .= '</div>';
        } else {
            $content .= $this->getAiTemplateFormContent($networkId, $networkType, $aiTemplate);
        }
        $content .= '</div>';

        if($networkId == 36){
            
            $privacyValue = 'PUBLIC_TO_EVERYONE';
            $allowComment = false;
            $promotionOwnBrand = false;
            $promotionThirdParty = false;
            $toggleOn = false;

            if(isset($schema[0]['share_settings']['status_privacy']) && isset($schema[0]['share_settings']['allow_comment']) && isset($schema[0]['share_settings']['promotion_option_organic']) && isset($schema[0]['share_settings']['promotion_option_branded']) ){
                
                $privacyValue = $schema[0]['share_settings']['status_privacy'];
                $allowComment =  $schema[0]['share_settings']['allow_comment'] === 'true';
                $promotionOwnBrand =   $schema[0]['share_settings']['promotion_option_organic'] === 'true';
                $promotionThirdParty =   $schema[0]['share_settings']['promotion_option_branded']=== 'true';

                $toggleOn = ($promotionOwnBrand || $promotionThirdParty)? true : false;
              
            }

            /*
            $prepostDetails = json_decode(B2S_Tools::getPrePostDetails($networkAuthId));
            
            if(isset($prepostDetails->prepostsettings->data) && !empty($prepostDetails->prepostsettings->data) && isset($prepostDetails->prepostsettings->error->code) && !empty($prepostDetails->prepostsettings->error->code)){
                
                $error = $prepostDetails->prepostsettings->error->code;
            }
            */

            $content .= '<div class="tiktok-share-settings" style="display:none;">';

            $privacySettings = array(
                            "PUBLIC_TO_EVERYONE" => array("value" => "PUBLIC_TO_EVERYONE", "label" => esc_html__("Public to everyone", "blog2social")),
                            "FOLLOWER_OF_CREATOR" => array("value" => "FOLLOWER_OF_CREATOR", "label" => esc_html__("Followers of creator", "blog2social")),
                            "MUTUAL_FOLLOW_FRIENDS" => array("value" => "MUTUAL_FOLLOW_FRIENDS", "label" => esc_html__("Mutual follow friends", "blog2social")),
                            "SELF_ONLY" => array("value" => "SELF_ONLY", "label" => esc_html__("Self only", "blog2social")),
                        );

            $content .= '<label>'.esc_html__("Who can view the post?", "blog2social").'</label>';
            $content .= '<select class="form-control b2s-select-area b2s-tiktok-status_privacy" data-network-id="' . esc_attr($networkId) . '" id="b2s[' . esc_attr($networkId) . '][status_privacy]" name="b2s[' . esc_attr($networkId) . '][status_privacy]">';

            foreach($privacySettings as $option){
                if(isset($option['value']) && isset($option["label"])){
                    $content .= '<option value="'.esc_attr($option['value']).'" '. ($option['value'] == $privacyValue ? " selected " :""). ' >' .$option["label"]. '</option>';
                }
            }

            $content.= '</select>';

            $content .= '<label>'.esc_html__("Allow users to", "blog2social").'</label>';
            $content.= '<div class="b2s-tiktok-allow-options b2s-tiktok-menu">';
            $content.= '<input   '.($allowComment ? "checked" : "").'      type="checkbox" name="b2s[' . esc_attr($networkId) . '][allow_comment]" id="b2s[' . esc_attr($networkId) . '][b2sTiktokAllowComment]" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" >';
            $content .= '<label> ' . esc_html__('Comment', 'blog2social') . '</label>';           
            $content .= '</div>';

            $content .= '<label>'.esc_html__("Disclose video/photo content",  "blog2social").'</label>';
            $content .= '<div class="b2s-tiktok-promotion b2s-tiktok-menu">';
            $content .= '<div>
                        <div class="toggle btn btn-xs btn-primary off" data-toggle="toggle" style="width: 90px; height: 22px; float:left;"  name="b2s[' . esc_attr($networkId) . '][b2s-tiktok-disclose-toggle]"  data-network-id="' . esc_attr($networkId) . '">
                        <input data-size="mini" data-toggle="toggle" data-width="90" data-height="22" data-onstyle="primary" data-on="ON" data-off="OFF" checked=""  name="b2s[' . esc_attr($networkId) . '][b2s-tiktok-disclose-input]" class="" data-area-type="manuell" value="1" type="checkbox">
                        <div class="toggle-group">
                        <label class="btn btn-primary btn-xs toggle-on" style="line-height: 14px;">ON</label>
                        <label class="btn btn-default btn-xs active toggle-off" style="line-height: 14px;">OFF</label>
                        <span class="toggle-handle btn btn-default btn-xs"></span>
                        </div>
                        </div>
                        </div>
                    <div style="clear: both;"></div>
                    <div hidden id="b2s[' . esc_attr($networkId) . '][b2s-tiktok-toggle-on]">'.($toggleOn ? '1' : '0').'</div>';

            $content .= '<div class="b2s-tiktok-disclose-info"  data-network-id="' . esc_attr($networkId) . '">';
            $content .= '<label> ' . esc_html__("Turn on to disclose that this video/photo promotes goods or services in exchange for something of value. Your video/photo could promote yourself, a third party or both.", 'blog2social') . '</label>';
            $content .= '</div>';
            $content .= '</div>';

            $content .= '<div class="clearfix"></div><div class="alert alert-info b2s-tiktok-promotional-note"   id="b2s[' . esc_attr($networkId) . '][b2sPromotional]" style="display:none;">' . esc_html__("Your photo/video will be labeled as 'Promotional content'.", "blog2social") .'</div>';
            $content.= '<div class="clearfix"></div><div class="alert alert-info b2s-tiktok-paid-partnership-note"  id="b2s[' . esc_attr($networkId) . '][b2sPaidPartnership]" style="display:none;">' . esc_html__("Your photo/video will be labeled as 'Paid partnership'.", "blog2social") .'</div>';

            $content .= '<div class="b2s-tiktok-branded-private-notice" style="display:none;" data-network-id="' . esc_attr($networkId) . '">';
            $content .= '<label> ' . esc_html__("Branded content can't be self-only", 'blog2social') . '</label>';
            $content .= '</div>';

            $content .= '<div class="b2s-tiktok-promotion-options b2s-tiktok-menu b2s-margin-bottom-10" style="display:none;" data-network-id="' . esc_attr($networkId) . '">';
            $content .= '<div>';

            $content .= '<div class="b2s-margin-bottom-10">';
            $content .= '<input '.($promotionOwnBrand ? "checked" : "").'  type="checkbox" value="off"  class="b2s-tiktok-promotion-option" name="b2s[' . esc_attr($networkId) . '][promotion_option_organic]" id="b2s[' . esc_attr($networkId) . '][b2sTiktokPromotionOwnBrand]" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '" >';
            $content .= '<label> ' . esc_html__('Your brand', 'blog2social') . '</label>';
            $content .= '<br><label class="b2s-own-promotional-content"> ' . esc_html__("You are promoting yourself or your own business. This video will be classified as Brand Organic.", 'blog2social') . '</label>';              
            $content .= '</div>';

            $content .= '<div class="b2s-margin-bottom-10">';
            $content .= ' <input '.($promotionThirdParty ? "checked" : "").'  type="checkbox" value="off"  class="b2s-tiktok-promotion-option" name="b2s[' . esc_attr($networkId) . '][promotion_option_branded]" id="b2s[' . esc_attr($networkId) . '][b2sTiktokPromotionThirdParty]" data-network-count="-1" data-network-id="' . esc_attr($networkId) . '">';
            $content .= '<label> ' . esc_html__('Branded Content', 'blog2social') . '</label>';
            $content .= '<br><label class="b2s-both-promotional-content"> ' . esc_html__("You are promoting another brand or a third party. This video will be classified as Branded Content.", 'blog2social') . '</label>';
            $content .= '<br>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';

            $content .= '<div class="b2s-tiktok-menu">';
        
            $content  .= '<div class="tiktok-music-confirmation"  data-network-id="' . esc_attr($networkId) . '">';
            // translators: %s is a link
            $content  .= sprintf(__('By posting, you agree to <a href="%s" target="_blank">TikTok\'s Music Usage Confirmation.</a>', "blog2social"), esc_url(B2S_Tools::getSupportLink('tiktok_music_confirmation')));
            $content  .= '</div>';
            
            $content  .= '<div class="tiktok-music-brand-confirmation" style="display:none;" data-network-id="' . esc_attr($networkId) . '">';
            // translators: %1$s, %2$s is a link
            $content .= sprintf(__('By posting, you agree to <a href="%1$s" target="_blank">Tiktok\'s Branded Content Policy</a> and <a href="%2$s" target="_blank">Music Usage Confirmation.</a>', "blog2social"), esc_url(B2S_Tools::getSupportLink('tiktok_branded_confirmation')),esc_url(B2S_Tools::getSupportLink('tiktok_music_confirmation')));
            $content .= '</div>';

            $content .= '<input type="hidden" class="b2s-tiktok-self-only-disabled-text" value="'.esc_html__("Self only (Branded content videos cannot be set to private)", "blog2social").'">';
            $content  .= '<input type="hidden" class="b2s-tiktok-self-only-text" value="'.esc_html__("Self only", "blog2social").'">';
            $content .= '<input type="hidden" class="b2s-tiktok-no-promotion-selected" value="'.esc_html__("You need to indicate if your TikTok content promotes yourself, a third party, or both.", "blog2social").'">';
            $content  .= '</div>';
        
            $content .= '</div>';

        }



        return $content;
    }

    private function getAiTemplateFormContent($networkId, $networkType, $aiTemplate, $isFreeUser = false) {

        //Set enabled
        $enabled = isset($aiTemplate['enabled']) && (int) $aiTemplate['enabled'] === 1;

        //Section Enabled/Toggles
        $contentGoalEnabled = !isset($aiTemplate['content_goal_enabled']) || (int) $aiTemplate['content_goal_enabled'] === 1;
        $toneLanguageEnabled = !isset($aiTemplate['tone_language_enabled']) || (int) $aiTemplate['tone_language_enabled'] === 1;
        $hashtagsKeywordsEnabled = !isset($aiTemplate['hashtags_keywords_enabled']) || (int) $aiTemplate['hashtags_keywords_enabled'] === 1;
        $contentLengthOutputEnabled = !isset($aiTemplate['content_length_output_enabled']) || (int) $aiTemplate['content_length_output_enabled'] === 1;
        
        //Values
        $defaultHashtagLimit= B2S_Tools::getAssistiniTemplateDefaultMaxKeywords($networkId);
        $values= B2S_Tools::getAssistiniTemplateValues();
        $maxPromptCharacters = B2S_Tools::getAiTemplateMaxPromptCharacters();
        $networkTemplateValues= isset($values["data"]["networks"][$networkId]["network_type"][$networkType]) ? $values["data"]["networks"][$networkId]["network_type"][$networkType] : array();
        $allowHashtags= isset($networkTemplateValues['hashtags']) && (int) $networkTemplateValues['hashtags'] === 1 ? $networkTemplateValues['hashtags'] : 0;
        $allowEmojis= isset($networkTemplateValues['allow_emojis']) && (int) $networkTemplateValues['allow_emojis'] === 1 ? $networkTemplateValues['allow_emojis'] : 0;
        $hashTagLimit =  ($allowHashtags == 1)? (isset($networkTemplateValues['hashtag_limit']) && (int) $networkTemplateValues['hashtag_limit'] > 0 ? (int) $networkTemplateValues['hashtag_limit'] : $defaultHashtagLimit) : 0;
        $networkName = isset(unserialize(B2S_PLUGIN_NETWORK)[$networkId]) ? unserialize(B2S_PLUGIN_NETWORK)[$networkId] : '';
        $hashtagsEnabled = $allowHashtags && (isset($aiTemplate['generate_hashtags']) ? $aiTemplate['generate_hashtags'] === 'from_ai' : true);
        $assConnected = $this->isAssistiniConnected();
        
        //Get Options for select fields
        $answerLanguages = B2S_Tools::getAssistiniAnswerInLanguage();
        $postGoals = B2S_Tools::getAssistiniPostGoal();
        $ctaTypes = B2S_Tools::getAssistiniCtaType();
        $pointOfViews = B2S_Tools::getAssistiniPointOfView();
        $textForms = B2S_Tools::getAssistiniTextForm();
        $formOfAddresses = B2S_Tools::getAssistiniFormOfAddress();
        $emojiOptions = B2S_Tools::getAssistiniEmojis();
        $writingStyles = B2S_Tools::getAssistiniWritingStyle();
        $generateHashtagOptions = B2S_Tools::getAssistiniGenerateHashtags();
        $lengths = B2S_Tools::getAssistiniLength();
        $tones = B2S_Tools::getAssistiniTone();

        //For loading default Settings in template
        $aiDefaultSettings = B2S_Tools::normalizeAiTemplateSettings(B2S_Tools::getAiTemplateDefaults(), array(), (int) $networkId);

        $content = '';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-12 media-heading">';
        $content .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Advanced AI Settings', 'blog2social') . '</span>' . ($isFreeUser ? ' <span class="label label-success b2s-ai-template-smart-badge">SMART</span>' : '') . ' <a href="#" class="b2s-info-btn del-padding-left b2sInfoContentBtn">' . esc_html__('Info', 'blog2social') . '</a>';
        $content .= '<button class="pull-right btn btn-primary btn-xs b2s-edit-template-load-default-ai" data-network-type="' . esc_attr($networkType) . '">' . esc_html__('Load default settings', 'blog2social') . '</button>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="b2s-tabs-nav-top-sentinel"></div>';
        $content .= '<div class="b2s-ai-template-config" style="display:' . ($assConnected ? 'block' : 'none') . ';">';

        $content .= '<div class="row">';
        $content .= '<div class="col-md-12">';
        $content .= '<div class="b2s-ai-template-enable-wrapper">';
        $content .= '<label class="b2s-ai-template-enable-switch" for="b2s-ai-template-enabled[' . esc_attr($networkType) . ']">';
        $content .= '<input type="checkbox" class="b2s-ai-template-enabled ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" id="b2s-ai-template-enabled[' . esc_attr($networkType) . ']" ' . ($enabled ? 'checked="checked"' : '') . '>';
        $content .= '<span class="b2s-ai-template-enable-slider"></span>';
        $content .= '</label>';
        $content .= '<span class="b2s-ai-template-enable-label">' . esc_html__('Define individual AI template rules', 'blog2social') . '</span>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="row b2s-ai-template-disabled-info" data-network-type="' . esc_attr($networkType) . '" style="display:' . ($enabled ? 'none' : 'block') . ';">';
        $content .= '<div class="col-md-12">';
        $content .= '<div class="alert alert-info">' . esc_html__('By default, Assistini uses your current post content and standard network rules. Activate custom AI template rules if you want to guide the AI output for this network.', 'blog2social') . '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="b2s-ai-template-settings ' . ($enabled ? '' : 'b2s-ai-template-settings-disabled') . '" data-network-type="' . esc_attr($networkType) . '" data-ai-defaults="' . esc_attr(wp_json_encode($aiDefaultSettings)) . '" style="display:block;">';

        $content .= '<input type="hidden" class="b2s-ai-template-network-name" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr($networkName) . '">';

        $content .= '<div class="row">';
        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Answer language', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-answer-language ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($answerLanguages as $languageKey => $languageLabel) {
            $content .= '<option value="' . esc_attr($languageKey) . '" ' . selected($aiTemplate['answer_in_language'], $languageKey, false) . '>' . esc_html($languageLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<br>';

        $content .= '<div class="row">';
        $content .= '<div class="col-md-12">';
        $content .= '<label>' . esc_html__('AI instruction (Prompt)', 'blog2social') . '</label>';
        
        $content .= '<textarea class="form-control b2s-ai-template-ai-instruction ai-template-form-element" rows="3" maxlength="' . esc_attr($maxPromptCharacters) . '" data-network-type="' . esc_attr($networkType) . '" placeholder="' . esc_attr__('e.g. Focus on practical value for small businesses, ask a question at the end, keep the tone friendly.', 'blog2social') . '">' . esc_textarea($aiTemplate['ai_instruction']) . '</textarea>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<br>';

        $content .= '<div class="b2s-ai-template-enable-wrapper b2s-ai-template-group-enable-wrapper">';
        $content .= '<label class="b2s-ai-template-enable-switch" for="b2s-ai-template-content-goal-enabled[' . esc_attr($networkType) . ']">';
        $content .= '<input type="checkbox" class="b2s-ai-template-content-goal-enabled ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" id="b2s-ai-template-content-goal-enabled[' . esc_attr($networkType) . ']" ' . ($contentGoalEnabled ? 'checked="checked"' : '') . '>';
        $content .= '<span class="b2s-ai-template-enable-slider"></span>';
        $content .= '</label>';
        $content .= '<span class="b2s-ai-template-enable-label">' . esc_html__('Content & goal', 'blog2social') . '</span>';
        $content .= '</div>';

        $content .= '<div class="b2s-ai-template-group">';

        $content .= '<div class="b2s-ai-template-group-fields b2s-ai-template-content-goal-fields ' . ($contentGoalEnabled ? '' : 'b2s-ai-template-group-fields-disabled') . '" data-network-type="' . esc_attr($networkType) . '">';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Post goal', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-post-goal ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($postGoals as $goalKey => $goalLabel) {
            $content .= '<option value="' . esc_attr($goalKey) . '" ' . selected($aiTemplate['post_goal'], $goalKey, false) . '>' . esc_html($goalLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';

        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('CTA type', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-cta-type ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($ctaTypes as $ctaTypeKey => $ctaTypeLabel) {
            $content .= '<option value="' . esc_attr($ctaTypeKey) . '" ' . selected($aiTemplate['cta_type'], $ctaTypeKey, false) . '>' . esc_html($ctaTypeLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';

        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Tone', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-tone ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($tones as $toneKey => $toneLabel) {
            $content .= '<option value="' . esc_attr($toneKey) . '" ' . selected($aiTemplate['tone'], $toneKey, false) . '>' . esc_html($toneLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="row">';
        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Content focus', 'blog2social') . '</label>';
        $content .= '<input type="range" min="1" max="100" class="form-control b2s-ai-template-content-focus ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr((int) $aiTemplate['content_focus']) . '">';
        $content .= '</div>';

        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Point of view', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-point-of-view ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($pointOfViews as $pointOfViewKey => $pointOfViewLabel) {
            $content .= '<option value="' . esc_attr($pointOfViewKey) . '" ' . selected($aiTemplate['point_of_view'], $pointOfViewKey, false) . '>' . esc_html($pointOfViewLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<br>';

        $content .= '<div class="b2s-ai-template-enable-wrapper b2s-ai-template-group-enable-wrapper">';
        $content .= '<label class="b2s-ai-template-enable-switch" for="b2s-ai-template-tone-language-enabled[' . esc_attr($networkType) . ']">';
        $content .= '<input type="checkbox" class="b2s-ai-template-tone-language-enabled ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" id="b2s-ai-template-tone-language-enabled[' . esc_attr($networkType) . ']" ' . ($toneLanguageEnabled ? 'checked="checked"' : '') . '>';
        $content .= '<span class="b2s-ai-template-enable-slider"></span>';
        $content .= '</label>';
        $content .= '<span class="b2s-ai-template-enable-label">' . esc_html__('Tone & language', 'blog2social') . '</span>';
        $content .= '</div>';

        $content .= '<div class="b2s-ai-template-group">';
        $content .= '<div class="b2s-ai-template-group-fields b2s-ai-template-tone-language-fields ' . ($toneLanguageEnabled ? '' : 'b2s-ai-template-group-fields-disabled') . '" data-network-type="' . esc_attr($networkType) . '">';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Form of address', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-form-of-address ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($formOfAddresses as $formOfAddressKey => $formOfAddressLabel) {
            $content .= '<option value="' . esc_attr($formOfAddressKey) . '" ' . selected($aiTemplate['form_of_address'], $formOfAddressKey, false) . '>' . esc_html($formOfAddressLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';

        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Text form', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-text-form ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($textForms as $textFormKey => $textFormLabel) {
            $content .= '<option value="' . esc_attr($textFormKey) . '" ' . selected($aiTemplate['text_form'], $textFormKey, false) . '>' . esc_html($textFormLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';

        if ($allowEmojis) {
            $content .= '<div class="col-md-4">';
            $content .= '<label>' . esc_html__('Emojis', 'blog2social') . '</label>';
            $content .= '<select class="form-control b2s-ai-template-emojis ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
            foreach ($emojiOptions as $emojiKey => $emojiLabel) {
                $content .= '<option value="' . esc_attr($emojiKey) . '" ' . selected($aiTemplate['emojis'], $emojiKey, false) . '>' . esc_html($emojiLabel) . '</option>';
            }
            $content .= '</select>';
            $content .= '</div>';
        }

        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Writing style', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-writing-style ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($writingStyles as $writingStyleKey => $writingStyleLabel) {
            $content .= '<option value="' . esc_attr($writingStyleKey) . '" ' . selected($aiTemplate['writing_style'], $writingStyleKey, false) . '>' . esc_html($writingStyleLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<br>';

        $content .= '<div class="b2s-ai-template-enable-wrapper b2s-ai-template-group-enable-wrapper">';
        $content .= '<label class="b2s-ai-template-enable-switch" for="b2s-ai-template-hashtags-keywords-enabled[' . esc_attr($networkType) . ']">';
        $content .= '<input type="checkbox" class="b2s-ai-template-hashtags-keywords-enabled ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" id="b2s-ai-template-hashtags-keywords-enabled[' . esc_attr($networkType) . ']" ' . ($hashtagsKeywordsEnabled ? 'checked="checked"' : '') . '>';
        $content .= '<span class="b2s-ai-template-enable-slider"></span>';
        $content .= '</label>';
        $content .= '<span class="b2s-ai-template-enable-label">' . esc_html__('Hashtags & Keywords', 'blog2social') . '</span>';
        $content .= '</div>';

        $content .= '<div class="b2s-ai-template-group">';
        $content .= '<div class="b2s-ai-template-group-fields b2s-ai-template-hashtags-keywords-fields ' . ($hashtagsKeywordsEnabled ? '' : 'b2s-ai-template-group-fields-disabled') . '" data-network-type="' . esc_attr($networkType) . '">';
        $content .= '<div class="row">';

        if ($allowHashtags) {
            $content .= '<div class="col-md-4">';
            $content .= '<label>' . esc_html__('Generate hashtags', 'blog2social') . '</label>';
            $content .= '<select class="form-control b2s-ai-template-generate-hashtags ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
            foreach ($generateHashtagOptions as $generateHashtagKey => $generateHashtagLabel) {
                $content .= '<option value="' . esc_attr($generateHashtagKey) . '" ' . selected($aiTemplate['generate_hashtags'], $generateHashtagKey, false) . '>' . esc_html($generateHashtagLabel) . '</option>';
            }
            $content .= '</select>';
            $content .= '</div>';

            $content .= '<div class="col-md-4">';
            $content .= '<label>' . esc_html__('Hashtags count', 'blog2social') . '</label>';
            $content .= '<input type="number" min="0" max="' . esc_attr($hashTagLimit) . '" class="form-control b2s-ai-template-hashtags-count ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr((int) $aiTemplate['hashtags_count']) . '" ' . ($hashtagsEnabled ? '' : 'disabled="disabled"') . '>';
            $content .= '</div>';
        }

        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Keywords', 'blog2social') . '</label>';
        $content .= '<div><input type="checkbox" class="b2s-ai-template-enforce-keywords ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" id="b2s-ai-template-enforce-keywords[' . esc_attr($networkType) . ']" ' . (!empty($aiTemplate['use_keywords']) ? 'checked="checked"' : '') . '> <label for="b2s-ai-template-enforce-keywords[' . esc_attr($networkType) . ']">' . esc_html__('Enforce keywords', 'blog2social') . '</label></div>';
        $content .= '<input type="text" class="form-control b2s-ai-template-use-keywords ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr($aiTemplate['use_keywords']) . '" placeholder="' . esc_attr__('keyword1, keyword2', 'blog2social') . '" ' . (empty($aiTemplate['use_keywords']) ? 'style="display:none;"' : '') . '>';
        $content .= '</div>';

        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Keyword strength', 'blog2social') . '</label>';
        $content .= '<input type="range" min="1" max="100" class="form-control b2s-ai-template-keyword-strength ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr((int) $aiTemplate['keyword_strength']) . '">';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '</div>';
        $content .= '</div>';
        $content .= '<br>';

        $content .= '<div class="b2s-ai-template-enable-wrapper b2s-ai-template-group-enable-wrapper">';
        $content .= '<label class="b2s-ai-template-enable-switch" for="b2s-ai-template-content-length-output-enabled[' . esc_attr($networkType) . ']">';
        $content .= '<input type="checkbox" class="b2s-ai-template-content-length-output-enabled ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" id="b2s-ai-template-content-length-output-enabled[' . esc_attr($networkType) . ']" ' . ($contentLengthOutputEnabled ? 'checked="checked"' : '') . '>';
        $content .= '<span class="b2s-ai-template-enable-slider"></span>';
        $content .= '</label>';
        $content .= '<span class="b2s-ai-template-enable-label">' . esc_html__('Content Length & Output', 'blog2social') . '</span>';
        $content .= '</div>';

        $content .= '<div class="b2s-ai-template-group">';
        $content .= '<div class="b2s-ai-template-group-fields b2s-ai-template-content-length-output-fields ' . ($contentLengthOutputEnabled ? '' : 'b2s-ai-template-group-fields-disabled') . '" data-network-type="' . esc_attr($networkType) . '">';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Text length', 'blog2social') . '</label>';
        $content .= '<select class="form-control b2s-ai-template-text-length ai-template-form-element" data-network-type="' . esc_attr($networkType) . '">';
        foreach ($lengths as $lengthKey => $lengthLabel) {
            $content .= '<option value="' . esc_attr($lengthKey) . '" ' . selected($aiTemplate['text_length'], $lengthKey, false) . '>' . esc_html($lengthLabel) . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';

        $content .= '<div class="col-md-4">';
        $content .= '<label>' . esc_html__('Text depth', 'blog2social') . '</label>';
        $content .= '<input type="range" min="1" max="100" class="form-control b2s-ai-template-text-depth ai-template-form-element" data-network-type="' . esc_attr($networkType) . '" value="' . esc_attr((int) $aiTemplate['text_depth']) . '">';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<br>';

        $content .= '<div class="row">';
        $content .= '<div class="col-md-12">';
        $content .= '<button type="button" class="btn btn-primary btn-sm b2s-ai-template-preview-btn" data-network-type="' . esc_attr($networkType) . '">' . esc_html__('Generate preview text', 'blog2social') . '</button>';
        $content .= '<div class="b2s-ai-template-preview" data-network-type="' . esc_attr($networkType) . '"'
            . ' data-text-generating="' . esc_attr__('Generating preview text...', 'blog2social') . '"'
            . ' data-text-generated="' . esc_attr__('Generated text', 'blog2social') . '"'
            . ' data-text-left="' . esc_attr__('Left Words', 'blog2social') . '"'
            . ' data-text-hashtags="' . esc_attr__('Hashtags', 'blog2social') . '"'
            . ' data-text-no-context="' . esc_attr__('No post context found for AI preview.', 'blog2social') . '"'
            . ' data-text-no-network="' . esc_attr__('Please add template content before generating an AI preview.', 'blog2social') . '"'
            . ' data-text-parse-error="' . esc_attr__('AI preview response could not be parsed.', 'blog2social') . '"'
            . ' data-text-security-fail="' . esc_attr__('Security check failed. Please reload and try again.', 'blog2social') . '"'
            . ' data-text-gen-fail="' . esc_attr__('AI preview could not be generated.', 'blog2social') . '"'
            . ' data-text-request-fail="' . esc_attr__('Request failed while generating AI preview.', 'blog2social') . '"'
            . '>'
            . '<div class="b2s-ai-preview-loader">'
            . '<span class="spinner is-active"></span>'
            . '<span class="b2s-ai-preview-loader-text"></span>'
            . '</div>'
            . '<div class="alert alert-info b2s-ai-preview-result">'
            . '<span class="b2s-ai-template-preview-title"></span>'
            . '<div class="b2s-ai-template-preview-text"></div>'
            . '<div class="b2s-ai-template-preview-meta"></div>'
            . '<div class="b2s-ai-template-preview-hashtags"></div>'
            . '</div>'
            . '<div class="alert alert-danger b2s-ai-preview-message"></div>'
            . '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="row"' . ($isFreeUser ? ' style="position:relative;z-index:200;"' : '') . '>';
        $content .= '<div class="col-md-12">';
        $content .= '<button type="button" class="btn btn-primary btn-sm b2s-edit-template-save-ai-btn pull-right" data-network-type="' . esc_attr($networkType) . '">' . esc_html__('save AI template', 'blog2social') . '</button>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '</div>';
        $content .= '<hr>';
        $content .= '<br>';

        return $content;
    }

    private function isAssistiniConnected() {
        global $wpdb;
        $sqlResult = $wpdb->get_row($wpdb->prepare("SELECT `id`, `access_token` FROM `{$wpdb->prefix}b2s_user_tool` WHERE `blog_user_id` = %d AND `tool_id` = 1", (int) B2S_PLUGIN_BLOG_USER_ID));
        return (is_object($sqlResult) && isset($sqlResult->id) && (int) $sqlResult->id > 0 && isset($sqlResult->access_token) && !empty($sqlResult->access_token));
    }

    private function networkPreview($networkId, $networkType, $schema) {
        $domain = get_home_url();
        $title = get_bloginfo('title');
        $desc = get_bloginfo('description');
        $preview = '';
        $preview .= '<div style="width: 80%; margin: 0 auto;">';
        switch ($networkId) {
            case '1':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-1">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-1" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-1">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-1">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-1">';
                $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-1" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-link-meta-box-1">';
                $preview .= '<span>' . esc_html($domain) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-title">' . esc_html($title) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-1">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-1">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-1" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-1" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-1.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 10px; padding-top: 10px; border-top: 1px solid #dadde1;">';
                $preview .= '<div style="display: flex; align-items: flex-start; gap: 8px;">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-1" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 32px; height: 32px; flex-shrink: 0;">';
                $preview .= '<div style="background-color: #f0f2f5; border-radius: 18px; padding: 8px 12px; flex: 1;">';
                $preview .= '<span style="font-weight: 600; font-size: 13px;">Blog2Social</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '" style="font-size: 13px;">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '2':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-2">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-2" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 b2s-edit-template-preview-2">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-2">Blog2Social</span> <span class="b2s-edit-template-preview-profile-handle-2">@blog2social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-2" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $blogOptions = new B2S_Options(0, 'B2S_PLUGIN_GENERAL_OPTIONS');
                if ($blogOptions->_getOption('card_default_type') == 0) {
                    $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-2">';
                    $preview .= '<div class="col-sm-3 b2s-edit-template-preview-link-meta-box-image-2">';
                    $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-2" src="' . esc_url($this->previewImage) . '">';
                    $preview .= '</div>';
                    $preview .= '<div class="col-sm-9" style="padding-top: 12px;">';
                    $preview .= '<span>' . esc_html($title) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-2">' . esc_html($desc) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-2">' . esc_html($domain) . '</span>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                } else {
                    $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-2">';
                    $preview .= '<div class="col-sm-12 b2s-edit-template-preview-link-meta-box-image-2-big">';
                    $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-2-big" src="' . esc_url($this->previewImage) . '">';
                    $preview .= '</div>';
                    $preview .= '<div class="col-sm-12">';
                    $preview .= '<span>' . esc_html($title) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-2">' . esc_html($desc) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-2">' . esc_html($domain) . '</span>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                }
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-2" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-2" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-2" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-2.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '3':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-3">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-3" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-3">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-3">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-3">';
                $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-3" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-3">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-title-3">' . esc_html($title) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-3">' . esc_html($domain) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-3">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-3">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-3" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-3" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-3.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e0e0e0;">';
                $preview .= '<div style="display: flex; align-items: flex-start; gap: 8px;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="flex: 1;">';
                $preview .= '<div><span class="b2s-edit-template-preview-profile-name-3" style="margin-left: 0;">Blog2Social</span></div>';
                $preview .= '<div class="b2s-edit-template-preview-content-3"><span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span></div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '4':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-4">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<img alt="Avatar" class="S8Dii RoN4R tPU70" loading="eager" sizes="44px" src="'. esc_url(plugins_url('/assets/images/settings/cone_closed_64.png', B2S_PLUGIN_FILE)) .'" style="height: 44px; width: 44px;  border-radius: 100px;">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-4">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';     
                $preview .= '<div class="b2s-edit-template-text-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<h3 class="b2s-edit-template-text-preview-tumblr-title col-sm-12"></h3>';
                $preview .= '<img style="margin-top: 5px; margin-bottom: 5px;" class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-4 col-sm-12" src="' . esc_url($this->previewImage) . '">';
                $preview .= '<div style="padding-top: 0px !important;"  class="col-sm-12 b2s-edit-template-preview-content-4">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '<div style="margin-top: 5px;" class="b2s-edit-template-text-preview-tumblr-hashtags col-sm-12"></div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 3) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div style="padding-top: 0px !important;" class="col-sm-12 b2s-edit-template-preview-content-4">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-4">';
                $preview .= '<img style="margin-top: 5px; margin-bottom: 5px;" class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-4" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-4">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-4">' . esc_html($domain) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div style="margin-top: 5px;" class="b2s-edit-template-text-preview-tumblr-hashtags"></div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-4">';
                $preview .= '<img style="margin-top: 5px; margin-bottom: 5px;" class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-4" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '<div style="padding-top: 0px !important;" class="col-sm-12 b2s-edit-template-preview-content-4">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div style="margin-top: 5px;" class="b2s-edit-template-text-preview-tumblr-hashtags"></div>';
                $preview .= '<div class="row">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-4" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-3.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '12':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-12">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-12" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 b2s-edit-template-preview-profile-name-12">';
                $preview .= '<span>Blog2Social</span>';
                $preview .= '<span class="pull-right b2s-edit-template-preview-dots-12">...</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-12">';
                $preview .= '<img class="b2s-edit-template-preview-image-12 b2s-edit-template-link-preview b2s-edit-template-image-frame" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . ' src="' . esc_url($this->previewImage) . '">';
                $preview .= '<img class="b2s-edit-template-preview-image-12 b2s-edit-template-image-preview b2s-edit-template-image-cut" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . ' src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-12" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-12.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content-profile-name-12">Blog2Social</span><span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-12" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #dbdbdb; display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . ';">';
                $preview .= '<div class="col-sm-1" style="padding-right: 0;">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-12" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 32px; height: 32px;">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-11">';
                $preview .= '<span class="b2s-edit-template-preview-content-profile-name-12">Blog2Social</span> ';
                $preview .= '<span class="b2s-edit-template-preview-comment b2s-edit-template-preview-content-12" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '19':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-19">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-19" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-19">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (($schema[$networkType]['format'] !== false && (int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row b2s-edit-template-preview-header-19">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-19">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-4 b2s-edit-template-preview-image-border-19">';
                $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-19" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-link-meta-box-19">';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-title-19">' . esc_html($title) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-19">' . esc_html($desc) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-19">' . esc_html($domain) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (($schema[$networkType]['format'] === false || (int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-19">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-19">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-19" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-19" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-19.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '6':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-1">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 offset-sm-1 b2s-edit-template-preview-border b2s-edit-template-preview-border-6">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-6">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-6" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-6">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-6" data-network-type="' . esc_attr($networkType) . '">TITLE</p>';
                $preview .= '<p class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '<div class="row" style="margin-top: 40px;">';
                $preview .= '<div class="col-sm-2 b2s-pr-0">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-6" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 b2s-pl-0">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-6">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '17':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-17">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-17" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-17">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-17">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class=" col-md-12 b2s-link-preview-17">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-17">';
                $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-1" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-link-meta-box-17">';
                $preview .= '<span class="b2s-edit-template-preview-link-title">' . esc_html($title) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-link-17">' . esc_html($domain) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-17">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-17">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-17" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-17" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-17.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 10px; padding-top: 10px; border-top: 1px solid #dce1e7;">';
                $preview .= '<div style="display: flex; align-items: flex-start; gap: 8px;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="background-color: #f2f3f5; border-radius: 12px; padding: 8px 12px; flex: 1; font-size: 13px;">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-17">Blog2Social</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '16':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-16">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-16" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-16">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-16" data-network-type="' . esc_attr($networkType) . '">TITLE</p>';
                $preview .= '<p class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '7':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-7">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-7" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-7">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-7">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-7" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-7">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-7" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-7.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e0e0e0;">';
                $preview .= '<div style="display: flex; align-items: flex-start; gap: 8px;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="flex: 1; font-size: 13px;">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-7" style="margin-left: 0;">Blog2Social</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '9':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-9">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-title b2s-edit-template-preview-title-9" data-network-type="' . esc_attr($networkType) . '">TITLE</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link b2s-edit-template-preview-link-9">' . esc_html($domain) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-9" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '4':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-4">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-4" data-network-type="' . esc_attr($networkType) . '">TITLE</p>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-4" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '<p class="b2s-edit-template-preview-link b2s-edit-template-preview-link-4">' . esc_html($domain) . '</p>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-4" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-4.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '14':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-14" data-network-type="' . esc_attr($networkType) . '">TITLE</p><br>';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-14" src="' . esc_url($this->previewImage) . '"><br>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-14" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '11':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-11" data-network-type="' . esc_attr($networkType) . '">TITLE</p><br>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-11" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-11" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '18':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-18">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-18" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-18">Blog2Social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-17" src="' . esc_url($this->previewImage) . '">';
                $preview .= '<p class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '15':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-15" data-network-type="' . esc_attr($networkType) . '">TITLE</p>';
                $preview .= '<p class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">CONTENT</p>';
                $preview .= '<a class="b2s-edit-template-preview-link b2s-edit-template-preview-link-15" data-network-type="' . esc_attr($networkType) . '">' . $domain . '</a>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 8px; padding: 8px; background-color: #f6f7f8; border-radius: 4px; border-left: 3px solid #ff4500;">';
                $preview .= '<div style="display: flex; align-items: flex-start; gap: 6px;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="flex: 1; font-size: 12px;">';
                $preview .= '<span style="font-weight: 600;">u/blog2social</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '24':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-24">';
                $preview .= '<div class="row" style="padding-right: 10px;">';
                $preview .= '<div class="col-sm-1">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-24" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 col-sm-push-1">';
                $preview .= '<div class="b2s-edit-template-link-preview b2s-edit-template-preview-inner-border-24" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-24">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '<br><br><span>' . esc_html($domain) . '</span><br>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-11 b2s-edit-template-preview-link-meta-box-24">';
                $preview .= '<span class="b2s-edit-template-preview-link-title">' . esc_html($title) . '</span>';
                $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-24" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-24">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-24" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-image-border-24">';
                $preview .= '<div class="b2s-edit-template-preview-inner-border-24">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';

                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '25':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-11" data-network-type="' . esc_attr($networkType) . '">TITLE</p><br>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-11" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-11" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '26':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-4">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-11" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-11" data-network-type="' . esc_attr($networkType) . '">TITLE</p><br>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-11" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '27':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-9">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-11" data-network-type="' . esc_attr($networkType) . '">TITLE</p>';
                $preview .= '<span>' . esc_html($domain) . '</span><br>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-11" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-27" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-27.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-3">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-27" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '36':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8">';
                $preview .= '<div class="b2s-edit-template-preview-frame-36">';
                $preview .= '<div class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-36" style="background-image: url(\'' . esc_url($this->previewImage) . '\');"></div>';
                $preview .= '<div class="b2s-edit-template-preview-badge-36">&#x2665;</div>';
                $preview .= '<div class="b2s-edit-template-preview-top-36">';
                $preview .= '<span>' . esc_html__('Following', 'blog2social') . '</span>';
                $preview .= '<span>' . esc_html__('For You', 'blog2social') . '</span>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-actions-36">';
                $preview .= '<div class="b2s-edit-template-preview-avatar-wrap-36">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" class="b2s-edit-template-preview-avatar-img-36">';
                $preview .= '<div class="b2s-edit-template-preview-avatar-badge-36">+</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-item-36">';
                $preview .= '<div class="b2s-edit-template-preview-action-icon-lg-36">&#x2665;</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-label-36">0</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-item-36">';
                $preview .= '<div class="b2s-edit-template-preview-action-icon-md-36">&#x1F4AC;</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-label-36">0</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-item-36">';
                $preview .= '<div class="b2s-edit-template-preview-action-icon-sm-36">&#x27A4;</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-label-36">Share</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-overlay-36">';
                $preview .= '<div class="b2s-edit-template-preview-user-row-36">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" class="b2s-edit-template-preview-user-img-36">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-36">@blog2social</span>';
                $preview .= '</div>';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-36" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '<div class="b2s-edit-template-preview-music-36">&#x266A; Photo Mode</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '37':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-lg b2s-edit-template-preview-box-37">';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-37" data-network-type="' . esc_attr($networkType) . '">TITLE</p>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-37">' . "Linked content" . '</p>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-37" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '<div class="b2s-edit-template-preview-content b2s-edit-template-preview-tags-37">' . "Tag1, Tag2" . '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-37" data-network-type="' . esc_attr($networkType) . '">TITLE</p>';
                $preview .= '<div class="col-sm-3">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-37" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '<p class="b2s-edit-template-preview-content b2s-edit-template-preview-content-37" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</p>';
                $preview .= '<div class="b2s-edit-template-preview-content b2s-edit-template-preview-tags-37">' . "Tag1, Tag2" . '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '38':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-38">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-2" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 b2s-edit-template-preview-38">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-38">Blog2Social</span><br>';
                $preview .= ' <span class="b2s-edit-template-preview-profile-handle-38">@blog2social@mas.to</span>';

                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-38">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-38" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';

                $blogOptions = new B2S_Options(0, 'B2S_PLUGIN_GENERAL_OPTIONS');
                if ($blogOptions->_getOption('card_default_type') == 0) {
                    $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-38">';
                    $preview .= '<div class="col-sm-3 b2s-edit-template-preview-link-meta-box-image-38">';
                    $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-38" src="' . esc_url($this->previewImage) . '">';
                    $preview .= '</div>';
                    $preview .= '<div class="col-sm-9" style="padding-top: 12px;">';
                    $preview .= '<span class="b2s-edit-template-preview-link-title-38">' . esc_html($title) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-38">' . esc_html($desc) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-38">' . esc_html($domain) . '</span>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                } else {
                    $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-38">';
                    $preview .= '<div class="col-sm-12 b2s-edit-template-preview-link-meta-box-image-38-big">';
                    $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image38-big" src="' . esc_url($this->previewImage) . '">';
                    $preview .= '</div>';
                    $preview .= '<div class="col-sm-12">';
                    $preview .= '<span>' . esc_html($title) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-38">' . esc_html($desc) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-38">' . esc_html($domain) . '</span>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                }
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-content-38">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-38" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-38" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-38" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-38.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e0e0e0;">';
                $preview .= '<div style="display: flex; align-items: flex-start; gap: 8px;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="flex: 1;">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-38">Blog2Social</span> <span class="b2s-edit-template-preview-profile-handle-38">@blog2social@mas.to</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '39':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-box-39">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-1 b2s-edit-template-preview-margin-39" style="margin-right:15px">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10">';
                $preview .= '<div class="b2s-edit-template-preview-title b2s-edit-template-preview-title-account-39">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-4">Blog2Social</div>';
                $preview .= '<div class="col-sm-1 b2s-edit-template-bot-39">Bot</div>';
                $preview .= '<div class="col-sm-6 b2s-edit-template-timestamp-39"> Today at 9:25 AM</div>';
                $preview .= '</div>';
                $preview .= '</div><br>';
                $preview .= '<div class="b2s-edit-template-preview-box-inner-39">';
                $preview .= '<p class="b2s-edit-template-preview-title b2s-edit-template-preview-title-39" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($title)) . '</p><br>';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-2" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-title-39" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper b2s-edit-template-preview-box-39" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 4px; padding: 4px 0;">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-1 b2s-edit-template-preview-margin-39" style="margin-right:15px">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10" style="margin-left: 20px;">';
                $preview .= '<div class="b2s-edit-template-preview-title b2s-edit-template-preview-title-account-39">Blog2Social <span class="b2s-edit-template-bot-39">Bot</span></div>';
                $preview .= '<span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '" style="font-size: 13px; color: #e0e1e5;">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '42':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8">';
                $preview .= '<div class="b2s-edit-template-preview-border-42">';
                $preview .= '<div class="b2s-edit-template-preview-header-42">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-42" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '<div>';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-42">Blog2Social</span>';
                $preview .= '<span class="b2s-edit-template-preview-profile-meta-42">' . esc_html($domain) . ' &bull; ' . esc_html__('just now', 'blog2social') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-content-42">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-image-border-42">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-42" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-actions-42">';
                $preview .= '<div class="b2s-edit-template-preview-action-btn-42">&#x1F44D; ' . esc_html__('Like', 'blog2social') . '</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-btn-42">&#x1F4AC; ' . esc_html__('Comment', 'blog2social') . '</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-btn-42">&#x21BA; ' . esc_html__('Share', 'blog2social') . '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '43':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-43">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-43" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 b2s-edit-template-preview-43">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-43">Blog2Social</span> <span class="b2s-edit-template-preview-profile-handle-43">@blog2social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-43" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-43">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-link-meta-box-image-43-big">';
                $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-43-big" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span>' . esc_html($title) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-43">' . esc_html($desc) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-43">' . esc_html($domain) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-43" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-43" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-43" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-43.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e0e0e0;">';
                $preview .= '<div style="display: flex; align-items: flex-start; gap: 8px; overflow: hidden;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="flex: 1; min-width: 0;">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-43">Blog2Social</span> <span class="b2s-edit-template-preview-profile-handle-43">@blog2social</span> <div class="b2s-edit-template-preview-comment b2s-edit-template-preview-content-43" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '44':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-44">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-44" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 b2s-edit-template-preview-44">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-44">Blog2Social</span> <span class="b2s-edit-template-preview-profile-handle-44">@blog2social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-44" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-44">';
                $preview .= '<div class="col-sm-12 b2s-edit-template-preview-link-meta-box-image-44-big">';
                $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-44-big" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span>' . esc_html($title) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-44">' . esc_html($desc) . '</span><br>';
                $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-44">' . esc_html($domain) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-44" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-44" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-44" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-44.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'block') . '; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e0e0e0;">';
                $preview .= '<div style="display: flex; align-items: flex-start; gap: 8px;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="flex: 1;">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-44">Blog2Social</span> <span class="b2s-edit-template-preview-profile-handle-44">@blog2social</span>';
                $preview .= '<div class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '45':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8 b2s-edit-template-preview-border b2s-edit-template-preview-border-2">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-45" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-10 b2s-edit-template-preview-45">';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-45" style="font-weight: 600;">Blog2Social</span> <span class="b2s-edit-template-preview-profile-handle-45">@blog2social</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-link-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 0) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-45" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $blogOptions = new B2S_Options(0, 'B2S_PLUGIN_GENERAL_OPTIONS');
                if ($blogOptions->_getOption('card_default_type') == 0) {
                    $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-45">';
                    $preview .= '<div class="col-sm-3 b2s-edit-template-preview-link-meta-box-image-45">';
                    $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-45" src="' . esc_url($this->previewImage) . '">';
                    $preview .= '</div>';
                    $preview .= '<div class="col-sm-9" style="padding-top: 12px;">';
                    $preview .= '<span>' . esc_html($title) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-45">' . esc_html($desc) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-45">' . esc_html($domain) . '</span>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                } else {
                    $preview .= '<div class="row b2s-edit-template-preview-link-meta-box-45">';
                    $preview .= '<div class="col-sm-12 b2s-edit-template-preview-link-meta-box-image-45-big">';
                    $preview .= '<img class="b2s-edit-template-preview-link-image b2s-edit-template-preview-link-image-45-big" src="' . esc_url($this->previewImage) . '">';
                    $preview .= '</div>';
                    $preview .= '<div class="col-sm-12">';
                    $preview .= '<span>' . esc_html($title) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-desc-45">' . esc_html($desc) . '</span><br>';
                    $preview .= '<span class="b2s-edit-template-preview-link-meta-box-domain-45">' . esc_html($domain) . '</span>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                    $preview .= '</div>';
                }
                $preview .= '<div class="b2s-edit-template-image-preview" data-network-type="' . esc_attr($networkType) . '" ' . (((int) $schema[$networkType]['format'] == 1) ? '' : 'style="display: none;"') . '>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<span class="b2s-edit-template-preview-content b2s-edit-template-preview-content-45" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-image-image b2s-edit-template-preview-image-image-45" src="' . esc_url($this->previewImage) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-12">';
                $preview .= '<img class="b2s-edit-template-preview-like-icons-45" src="' . esc_url(plugins_url('/assets/images/settings/like-icons-45.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'flex') . '; align-items: flex-start; gap: 6px; margin-top: 8px; padding: 8px 12px; background-color: #f7f9f9; border-radius: 8px;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="flex: 1; font-size: 13px;">';
                $preview .= '<span style="font-weight: 600; color: #0f1419;">Blog2Social</span> ';
                $preview .= '<span style="color: #536471;">@blog2social</span> ';
                $preview .= '<span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '" style="color: #0f1419;">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            case '46':
                $preview .= '<div class="row">';
                $preview .= '<div class="col-sm-2">';
                $preview .= '<span class="b2s-edit-template-section-headline">' . esc_html__('Preview', 'blog2social') . ':</span>';
                $preview .= '</div>';
                $preview .= '<div class="col-sm-8">';
                $preview .= '<div class="b2s-edit-template-preview-border-46">';
                $preview .= '<div class="b2s-edit-template-preview-header-46">';
                $preview .= '<img class="b2s-edit-template-preview-profile-img-46" src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '">';
                $preview .= '<div>';
                $preview .= '<span class="b2s-edit-template-preview-profile-name-46">Blog2Social</span>';
                $preview .= '<span class="b2s-edit-template-preview-profile-meta-46">' . esc_html__('just now', 'blog2social') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-content-46">';
                $preview .= '<span class="b2s-edit-template-preview-content" data-network-type="' . esc_attr($networkType) . '">' . preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['content'])) . '</span>';
                $preview .= '</div>';
                $preview .= '<hr class="b2s-edit-template-preview-divider-46">';
                $preview .= '<div class="b2s-edit-template-preview-actions-46">';
                $preview .= '<div class="b2s-edit-template-preview-action-btn-46">&#x2665; ' . esc_html__('Like', 'blog2social') . '</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-btn-46">&#x1F4AC; ' . esc_html__('Comment', 'blog2social') . '</div>';
                $preview .= '<div class="b2s-edit-template-preview-action-btn-46">&#x27A4; ' . esc_html__('Share', 'blog2social') . '</div>';
                $preview .= '</div>';
                $preview .= '<div class="b2s-edit-template-preview-comment-wrapper" data-network-type="' . esc_attr($networkType) . '" style="display: ' . (empty($schema[$networkType]['comment']) ? 'none' : 'flex') . '; align-items: flex-start; gap: 6px; margin-top: 8px; padding: 8px 12px; background-color: #f7f9f9; border-radius: 8px;">';
                $preview .= '<img src="' . esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)) . '" style="width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;">';
                $preview .= '<div style="flex: 1; font-size: 13px;">';
                $preview .= '<span style="font-weight: 600;">Blog2Social</span> ';
                $preview .= '<span class="b2s-edit-template-preview-comment" data-network-type="' . esc_attr($networkType) . '">' . (!empty($schema[$networkType]['comment']) ? preg_replace("/\n/", "<br>", esc_html($schema[$networkType]['comment'])) : '') . '</span>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                $preview .= '</div>';
                break;
            default:
                break;
        }
        $preview .= '</div>';
        return $preview;
    }

}
