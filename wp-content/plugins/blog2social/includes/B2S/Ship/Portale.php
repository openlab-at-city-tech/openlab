<?php

class B2S_Ship_Portale {

    private $authurl;
    private $allowProfil;
    private $allowPage;
    private $allowGroup;
    private $oAuthPortal;

    public function __construct() {
        $hostUrl = (function_exists('rest_url')) ? rest_url() : get_site_url();
        $this->authurl = B2S_PLUGIN_API_ENDPOINT_AUTH . '?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2) . '&hostUrl=' . $hostUrl;
        $this->allowProfil = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PROFILE);
        $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
        $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
        $this->oAuthPortal = unserialize(B2S_PLUGIN_NETWORK_OAUTH);
    }

    public function getItemHtml($portale) {
        $html = '<ul>';
        foreach ($portale as $k => $portal) {
            $isDeprecated = ($portal->id == 8) ? true : false;
            if (!$isDeprecated) {
                $html .= '<li>';
                $html .= '<img class="b2s-network-list-add-thumb" alt="' . esc_attr($portal->name) . '" src="' . esc_url(plugins_url('/assets/images/portale/' . $portal->id . '_flat.png', B2S_PLUGIN_FILE)) . '">';
                $html .= '<span class="b2s-network-list-add-details">' . esc_html($portal->name) . '</span>';

                $b2sAuthUrl = $this->authurl . '&portal_id=' . $portal->id . '&transfer=' . (in_array($portal->id, $this->oAuthPortal) ? 'oauth' : 'form' ) . '&version=3&affiliate_id=' . B2S_Tools::getAffiliateId();
                if (in_array($portal->id, $this->allowGroup)) {
                    $name = ($portal->id == 11) ? esc_html__('Publication', 'blog2social') : esc_html__('Group', 'blog2social');
                    $html .= (B2S_PLUGIN_USER_VERSION > 1) ? ('<button onclick="wop(\'' . $b2sAuthUrl . '&choose=group\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-list-add-btn">+ ' . $name . '</button>') : '<button type="button" class="btn btn-primary btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sProFeatureModalBtn" data-type="auth-network" data-title="' . esc_html__('You want to connect a social media group?', 'blog2social') . '">+ ' . esc_html__('Group', 'blog2social') . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</a></button>';
                }
                if (in_array($portal->id, $this->allowPage)) {
                    $html .= (B2S_PLUGIN_USER_VERSION > 1 || (B2S_PLUGIN_USER_VERSION == 0 && $portal->id == 1) || (B2S_PLUGIN_USER_VERSION == 1 && ($portal->id == 1 || $portal->id == 10))) ? ('<button onclick="wop(\'' . $b2sAuthUrl . '&choose=page\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-list-add-btn">+ ' . esc_html__('Page', 'blog2social') . '</button>') : '<button type="button" class="btn btn-primary btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled ' . ((B2S_PLUGIN_USER_VERSION == 0) ? 'b2sPreFeatureModalBtn' : 'b2sProFeatureModalBtn') . '" data-title="' . esc_html__('You want to connect a network page?', 'blog2social') . '" data-type="auth-network" >+ ' . esc_html__('Page', 'blog2social') . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</a></button>';
                }
                if (in_array($portal->id, $this->allowProfil)) {
                    $name = ($portal->id == 4) ? esc_html__('Blog', 'blog2social') : esc_html__('Profile', 'blog2social');
                    if($portal->id == 6) {
                        $html .= '<a href="#" class="btn btn-primary btn-sm b2s-network-list-add-btn" data-auth-method="client">+ ' . esc_html__('Profile', 'blog2social') . '</a>';
                    } else {
                        if($portal->id == 24 && B2S_PLUGIN_USER_VERSION < 1) {
                            $html .= '<button type="button" class="btn btn-primary btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sBusinessFeatureModalBtn" data-title="' . esc_html__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">+ ' . esc_html__('Profile', 'blog2social') . ' <span class="label label-success">' . esc_html__("BUSINESS", "blog2social") . '</a></button>';
                        } else {
                            $html .= ($portal->id != 18 || (B2S_PLUGIN_USER_VERSION >= 2 && $portal->id == 18)) ? ('<a href="#" onclick="wop(\'' . $b2sAuthUrl . '&choose=profile\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-list-add-btn">+ ' . esc_html($name) . '</a>') : '<button type="button" class="btn btn-primary btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled b2sProFeatureModalBtn" data-title="' . esc_html__('You want to connect a network profile?', 'blog2social') . '" data-type="auth-network">+ ' . esc_html__('Profile', 'blog2social') . ' <span class="label label-success">' . esc_html__("PRO", "blog2social") . '</a></button>';
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
