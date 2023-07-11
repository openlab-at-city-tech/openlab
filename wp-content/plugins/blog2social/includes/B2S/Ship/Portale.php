<?php

class B2S_Ship_Portale {

    private $authurl;
    private $allowProfil;
    private $allowPage;
    private $allowGroup;
    private $oAuthPortal;
    private $isVideoNetwork;

    public function __construct() {
        $hostUrl = (function_exists('rest_url')) ? rest_url() : get_site_url();
        $this->authurl = B2S_PLUGIN_API_ENDPOINT_AUTH . '?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2) . '&hostUrl=' . $hostUrl;
        $this->allowProfil = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PROFILE);
        $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
        $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
        $this->oAuthPortal = unserialize(B2S_PLUGIN_NETWORK_OAUTH);
        $this->networkTypeName = unserialize(B2S_PLUGIN_NETWORK_TYPE);
        $this->networkTypeNameIndividual = unserialize(B2S_PLUGIN_NETWORK_TYPE_INDIVIDUAL);
        $this->isVideoNetwork = unserialize(B2S_PLUGIN_NETWORK_SUPPORT_VIDEO);
    }

    public function getItemHtml($portale, $isVideoView = false) {
        $html = '<ul>';
        foreach ($portale as $k => $portal) {

            if ($isVideoView) {
                if (!in_array($portal->id, $this->isVideoNetwork)) {
                    continue;
                }
            }
            if (!$isVideoView && in_array($portal->id, $this->isVideoNetwork)) {
                if (!in_array($portal->id, array(1, 2, 3, 6, 12, 38, 39))) {
                    continue;
                }
            }

            $isDeprecated = ($portal->id == 8) ? true : false;
            if (!$isDeprecated) {
                $html .= '<li>';
                $html .= '<img class="b2s-network-list-add-thumb" alt="' . esc_attr($portal->name) . '" src="' . esc_url(plugins_url('/assets/images/portale/' . $portal->id . '_flat.png', B2S_PLUGIN_FILE)) . '">';
                $html .= '<span class="b2s-network-list-add-details">' . esc_html($portal->name) . '</span>';

                $b2sAuthUrl = $this->authurl . '&portal_id=' . $portal->id . '&transfer=' . (in_array($portal->id, $this->oAuthPortal) ? 'oauth' : 'form' ) . '&version=3&affiliate_id=' . B2S_Tools::getAffiliateId();
                if (in_array($portal->id, $this->allowGroup)) {
                    $name = $this->networkTypeName[2];
                    if (isset($this->networkTypeNameIndividual[$portal->id][2]) && !empty($this->networkTypeNameIndividual[$portal->id][2])) {
                        $name = $this->networkTypeNameIndividual[$portal->id][2];
                    }
                    $html .= (B2S_PLUGIN_USER_VERSION > 1) ? ('<button onclick="wop(\'' . esc_url($b2sAuthUrl) . '&choose=group\', \'Blog2Social Network\'); return false;" class="btn btn-' . str_replace(' ', '', strtolower($portal->name)) . ' btn-sm b2s-network-list-add-btn">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>') : '<button type="button" class="btn btn-' . str_replace(' ', '', strtolower($portal->name)) . ' btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sProFeatureModalBtn" data-type="auth-network" data-title="' . esc_html__('You want to connect a social media group?', 'blog2social') . '">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '<span class="label label-success">' . esc_html__("PRO", "blog2social") . '</a></button>';
                }
                if (in_array($portal->id, $this->allowPage)) {
                    $name = $this->networkTypeName[1];
                    if (isset($this->networkTypeNameIndividual[$portal->id][1]) && !empty($this->networkTypeNameIndividual[$portal->id][1])) {
                        $name = $this->networkTypeNameIndividual[$portal->id][1];
                    }
                    if ($portal->id == 12) {
                        $b2sSpecialAuthUrl = $this->authurl . '&portal_id=' . $portal->id . '&transfer=oauth&version=3&affiliate_id=' . B2S_Tools::getAffiliateId();
                        $html .= (B2S_PLUGIN_USER_VERSION >= 1) ? '<button class="btn btn-' . str_replace(' ', '', strtolower(esc_html($portal->name))) . ' btn-sm b2s-network-list-add-btn b2s-network-add-instagram-business-info-btn" data-b2s-auth-url="' . $b2sSpecialAuthUrl . '">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>' : '<button type="button" class="btn btn-' . str_replace(' ', '', strtolower($portal->name)) . ' btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sPreFeatureModalBtn" data-title="' . esc_html__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network" >' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("SMART", "blog2social") . '</a></button>';
                    } else {
                        $html .= (B2S_PLUGIN_USER_VERSION > 1 || (B2S_PLUGIN_USER_VERSION == 0 && ($portal->id == 1 ||$portal->id == 6)) || (B2S_PLUGIN_USER_VERSION == 1 && ($portal->id == 1 || $portal->id == 6))) ? ('<button onclick="wop(\'' . esc_url($b2sAuthUrl) . '&choose=page\', \'Blog2Social Network\'); return false;" class="btn btn-' . str_replace(' ', '', strtolower(esc_html($portal->name))) . ' btn-sm b2s-network-list-add-btn">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>') : '<button type="button" class="btn btn-' . str_replace(' ', '', strtolower($portal->name)) . ' btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled ' . ((B2S_PLUGIN_USER_VERSION == 0) ? 'b2sPreFeatureModalBtn' : 'b2sProFeatureModalBtn') . '" data-title="' . esc_html__('You want to connect a network page?', 'blog2social') . '" data-type="auth-network" >' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</a></button>';
                    }
                }
                if (in_array($portal->id, $this->allowProfil)) {
                    if ($isVideoView &&  ($portal->id == 1 || $portal->id == 3)) {
                        $html .= '';
                    } else {
                        $name = $this->networkTypeName[0];
                        if (isset($this->networkTypeNameIndividual[$portal->id][0]) && !empty($this->networkTypeNameIndividual[$portal->id][0])) {
                            $name = $this->networkTypeNameIndividual[$portal->id][0];
                        }
                        if ($portal->id == 6) {
                            $html .= '<a href="#" class="btn btn-' . str_replace(' ', '', strtolower(esc_attr($portal->name))) . ' btn-sm b2s-network-list-add-btn" data-auth-method="client">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</a>';
                        } else if ($portal->id == 24 && B2S_PLUGIN_USER_VERSION < 1) {
                            $html .= '<button type="button" class="btn btn-' . str_replace(' ', '', strtolower(esc_attr($portal->name))) . ' btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sBusinessFeatureModalBtn" data-title="' . esc_html__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("BUSINESS", "blog2social") . '</a></button>';
                        }else if ($portal->id == 38 && B2S_PLUGIN_USER_VERSION < 1) {
                            $html .= '<button type="button" class="btn btn-' . str_replace(' ', '', strtolower(esc_attr($portal->name))) . ' btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sProFeatureModalBtn" data-title="' . esc_html__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</a></button>';
                        } else if ($portal->id == 12) {
                            $html .= '<button class="btn btn-' . str_replace(' ', '', strtolower(esc_attr($portal->name))) . ' btn-sm b2s-network-list-add-btn b2s-network-add-instagram-info-btn" data-b2s-auth-url="' . $b2sAuthUrl . '">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>';
                        } else if (($portal->id == 25 || $portal->id == 26 || $portal->id == 27 || $portal->id == 39) && B2S_PLUGIN_USER_VERSION < 1) {
                            $html .= '<button type="button" class="btn btn-' . str_replace(' ', '', strtolower(esc_attr($portal->name))) . ' btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sPreFeatureModalBtn" data-title="' . esc_html__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("SMART", "blog2social") . '</a></button>';
                        } else {
                            $html .= ($portal->id != 18 || (B2S_PLUGIN_USER_VERSION >= 2 && $portal->id == 18)) ? ('<button onclick="wop(\'' . esc_url($b2sAuthUrl) . '&choose=profile\', \'Blog2Social Network\'); return false;" class="btn btn-' . str_replace(' ', '', strtolower(esc_attr($portal->name))) . ' btn-sm b2s-network-list-add-btn">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . '</button>') : '<button type="button" class="btn btn-' . str_replace(' ', '', strtolower($portal->name)) . ' btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sProFeatureModalBtn" data-title="' . esc_html__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">' . sprintf(esc_html__('Connect %s', 'blog2social'), esc_html($name)) . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</a></button>';
                        }
                    }
                }
                $html .= '</li>';
            }
        }
        $html .= '</ul>';
        return $html;
    }

}
