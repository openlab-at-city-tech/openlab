<?php

class B2S_Network_UserApp {

    private $networks;
    private $allowedAppsPerNetwork;

    public function __construct() {
        $this->networks = unserialize(B2S_PLUGIN_NETWORK);
        $this->allowedAppsPerNetwork = unserialize(B2S_PLUGIN_ALLOWED_USER_APPS);
    }

    public function getData() {
        $data = array('action' => 'getUserAppList', 'token' => B2S_PLUGIN_TOKEN, 'plugin_version' => B2S_PLUGIN_VERSION);
        $apps = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $data, 30), true);
        $appsByNetwork = array();
        $supportedNetworks = unserialize(B2S_PLUGIN_USER_APP_NETWORKS);

        foreach ($supportedNetworks as $network) {
            if (B2S_PLUGIN_USER_VERSION == 0 && $network == 6) { //Case Pinterest
                continue;
            }
            $appsByNetwork[$network] = array();
        }
        if (is_array($apps['app_list']) && !empty($apps['app_list'])) {
            foreach ($apps['app_list'] as $network => $app) {
                foreach ($app as $id => $data) {
                    $appsByNetwork[$network][$id] = array(
                        "name" => $data['name'],
                        'secret' => $data['secret'],
                        'key' => $data['key'],
                        'is_addon' => isset($data['is_addon']) ? $data['is_addon'] : false,
                        'is_addon_valid' => isset($data['is_addon_valid']) ? $data['is_addon_valid'] : false,
                    );
                }
            }
        }
        return $appsByNetwork;
    }

    public function getItemHtml($data = array()) {

        $html = "";
        $html .= '<div class="col-md-12 b2s-network-details-container">';
        $html .= '<ul class="list-group b2s-network-details-container-list">';

        foreach ($data as $networkId => $network_apps) {
            if (isset($this->allowedAppsPerNetwork[$networkId])) {
                $appCount = $this->getAppPerNetworkCount($network_apps);
                $html .= '<li class="list-group-item" data-network-id="' . esc_attr($networkId) . '">';
                $html .= '<div class="media">';
                $html .= '<img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr($this->networks[$networkId]) . '" src="' . esc_url(plugins_url('/assets/images/portale/' . $networkId . '_flat.png', B2S_PLUGIN_FILE)) . '">';
                $html .= '<div class="media-body network">';
                $html .= '<h4>' . esc_html($this->networks[$networkId]);
                $html .= '<span class="pull-right">';
                $disabled = ($appCount >= $this->allowedAppsPerNetwork[$networkId]) ? "disabled" : "";
                $html .= '<button onclick="return false;" ' . ' class="btn btn-default btn-sm b2s-network-auth-btn b2s-network-add-user-app-btn" data-network-id="' . esc_attr($networkId) . '"><span class="glyphicon glyphicon-plus glyphicon-grey"></span>' . esc_html__('Add App', 'blog2social') . '</button>';
                $html .= '</span>';
                $html .= '</h4>';
                $html .= '<div class="clearfix"></div>';
                $html .= '<ul class="b2s-network-item-auth-list" data-network-id="' . esc_attr($networkId) . '">';
                $html .= '<li class="b2s-network-item-auth-list-li" data-network-id="' . esc_attr($networkId) . '">';
                $html .= '<span class="b2s-network-auth-count">' . esc_html__("App", "blog2social") . ' ';
                $html .= '<span id="b2s-user-app-count-current" class="b2s-user-app-count-current" data-network-count-trigger="true" data-network-id="' . esc_attr($networkId) . '">' . esc_html($appCount) . '</span>/<span id="b2s-network-app-full-count" data-network-id="' . esc_attr($networkId) . '">' . esc_html($this->allowedAppsPerNetwork[$networkId]) . '</span></span>';
                $html .= '</li>';

                foreach ($network_apps as $id => $app) {

                    $border = "";
                    if ($app['is_addon'] && !$app['is_addon_valid']) {
                        $border = "b2s-label-danger-border-left";
                    }
                    $html .= '<li class="b2s-network-item-auth-list-li ' . $border . '" data-network-id="' . esc_attr($networkId) . '" data-app-id="' . esc_attr($id) . '">';
                    $html .= '<div class="pull-left">';
                    if ($app['is_addon']) {
                        $html .= '<div class="b2s-app-addon">';
                        if ($app['is_addon_valid']) {
                            $html .= "additional app";
                        } else {
                            $html .= "The license for your app addon has expired.";
                        }

                        $html .= '</div>';
                    }
                    $html .= '<span class="b2s-user-app-name" data-app-id="' . esc_attr($id) . '">' . esc_html($app['name']) . '</span>';
                    $html .= '</div>';
                    $html .= '<div class="pull-right">';
                    $html .= '<a class="b2s-btn-delete-app-button b2s-add-padding-network-delete pull-right" data-network-type="0" data-app-id="' . esc_attr($id) . '" data-app-name="' . esc_attr($app['name']) . '" data-network-id="' . esc_attr($networkId) . '" href="#">';
                    $html .= '<span class="glyphicon glyphicon-trash glyphicon-grey"></span>';
                    $html .= '</a>';
                    if (!$app['is_addon'] || $app['is_addon_valid']) {
                        $html .= '<a class="b2s-btn-edit-app-button b2s-add-padding-network-delete pull-right" data-network-type="0" data-app-id="' . esc_attr($id) . '" data-app-name="' . esc_attr($app['name']) . '" data-app-key="' . esc_attr($app['key']) . '" data-network-id="' . esc_attr($networkId) . '" data-app-secret="' . esc_attr($app['secret']) . '"  href="#">';
                        $html .= '<span class="glyphicon glyphicon-pencil glyphicon-grey"></span>';
                        $html .= '</a>';
                    } else {
                        $html .= '<a class="b2s-btn-edit-app-button b2s-add-padding-network-delete pull-right" disabled data-network-type="0" data-app-id="' . esc_attr($id) . '" data-app-name="' . esc_attr($app['name']) . '" data-app-key="' . esc_attr($app['key']) . '" data-network-id="' . esc_attr($networkId) . '" data-app-secret="' . esc_attr($app['secret']) . '"  href="#">';
                        $html .= '<span class="glyphicon glyphicon-pencil glyphicon-grey"></span>';
                        $html .= '</a>';
                    }
                    $html .= '</div>';
                    $html .= '<div class="clearfix"></div>';
                    $html .= '</li>';
                }

                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</li>';
            }
        }
        $html .= '</ul>';
        $html .= '</div>';
        return $html;
    }

    private function getAppPerNetworkCount($apps) {
        $count = 0;
        foreach ($apps as $app) {
            if (!$app['is_addon'] || $app['is_addon_valid']) {
                $count++;
            }
        }
        return $count;
    }

}
