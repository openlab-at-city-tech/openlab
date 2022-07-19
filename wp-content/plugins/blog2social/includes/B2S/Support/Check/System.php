<?php

class B2S_Support_Check_System {

    private $systemData = array();
    private $pluginData = array();

    public function __construct($heartbeat_status = true) {
        $this->systemData['PLUGINVERSION'] = array("system" => $this->getB2sPluginVersion(), "req" => $this->getB2sNeededVersion(), "type" => "version", "name" => "Blog2Social Version", "link" => get_option('home') . ((substr(get_option('home'), -1, 1) == '/') ? '' : '/') . 'wp-admin/plugins.php');
        $this->systemData['WORDPRESSVERSION'] = array("system" => $this->getWordpressVersion(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_WORDPRESSVERSION, "type" => "version", "name" => "Wordpress Version", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->systemData['HEARTBEAT'] = array("system" => $heartbeat_status, "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_HEARTBEAT, "type" => "active", "name" => "Wordpress Heartbeat", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->systemData['PHPVERSION'] = array("system" => $this->getPhpVersion(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_PHPVERSION, "type" => "version", "name" => "PHP Version", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->systemData['PHPCURL'] = array("system" => $this->getPhpCurl(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_PHPCURL, "type" => "active", "name" => "PHP Curl", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->systemData['PHPMBSTRING'] = array("system" => $this->getPhpMbstring(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_PHPMBSTRING, "type" => "active", "name" => "PHP mbstring", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->systemData['PHPDOM'] = array("system" => $this->getPhpDom(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_PHPDOM, "type" => "active", "name" => "PHP Dom", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->systemData['MYSQLVERSION'] = array("system" => $this->getMysqlVersion(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_MYSQLVERSION, "type" => "version", "name" => "MySql Version", "link" => B2S_Tools::getSupportLink("system_requirements"));
        //deprecated since V 6.2.0 - not more used
        //$this->systemData['DATABASERIGHTS'] = array("system" => $this->getDatabaseRights(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_DATABASERIGHTS, "type" => "active", "name" => "Database Rights", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->systemData['HOTLINKPROTECTION'] = array("system" => $this->getHotlinkProtection(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_HOTLINKPROTECTION, "type" => "active", "name" => "Hotlink Protection (disabled)", "link" => B2S_Tools::getSupportLink("hotlink_protection"));
        //deprecated since V 6.8.0 - not more used
        //$this->systemData['WPJSON'] = array("system" => $this->getWpJson(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_WPJSON, "type" => "active", "name" => "REST API / WP-JSON", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->systemData['OPENSSL'] = array("system" => $this->getOpenssl(), "req" => B2S_PLUGIN_SYSTEMREQUIREMENT_OPENSSL, "type" => "active", "name" => "OpenSSL", "link" => B2S_Tools::getSupportLink("system_requirements"));
        $this->checkSystemData();
        $this->getPluginData();
    }

    private function checkSystemData() {
        foreach ($this->systemData as $key => $value) {
            if ($value["type"] == "version") {
                $this->systemData[$key]["success"] = ((version_compare($value["req"], $value["system"], '<=')) ? true : false);
            } else {
                $this->systemData[$key]["success"] = (($value["system"] == $value["req"]) ? true : false);
            }
        }
    }

    public function htmlData() {
        $systemHtml = '';
        foreach ($this->systemData as $key => $value) {
            $systemHtml .= '<div class="row">
                            <div class="col-sm-4 b2s-text-bold"">
                                ' . esc_html__($value["name"], "blog2social") . '
                            </div>
                            <div class="col-sm-3 b2s-debug-req">' .
                    (($value["type"] == "version") ?
                            ((isset($value["req"]) && !empty($value["req"])) ?
                                    $value["req"] . ' ' . esc_html__("or higher", "blog2social") :
                                    ''
                            ) :
                            (($value["req"]) ?
                                    '<i class="glyphicon glyphicon-ok glyphicon-success"></i>' :
                                    '<i class="glyphicon glyphicon-remove glyphicon-danger"></i>'
                            )
                    )
                    . '</div>
                            <div class="col-sm-2 b2s-debug-user">' .
                    (($value["type"] == "version") ?
                            $value["system"] :
                            (($value["system"]) ?
                                    '<i class="glyphicon glyphicon-ok glyphicon-success"></i>' :
                                    '<i class="glyphicon glyphicon-remove glyphicon-danger"></i>'
                            )
                    )
                    . '</div>
                            <div class="col-sm-1 b2s-debug-warn">' . (($value["success"]) ? '' : '<i class="glyphicon glyphicon-warning-sign glyphicon-warning pull-right"></i>') . '</div>
                            <div class="col-sm-2">' .
                    ((isset($value["link"]) && !empty($value["link"])) ?
                            '<a href="' . esc_url($value["link"]) . '" target="_blank" class="pull-right margin-right-15 ' . (($value["success"]) ? 'b2s-support-link-not-active' : '') . '">' . esc_html__("resolve conflict", "blog2social") . '</a>' :
                            '')
                    . '</div>
                        </div>
                        <br>
                        <hr>';
        }
        $pluginHtml = '';
        foreach ($this->pluginData as $key => $value) {
            if ($value["warning"]) {
                $pluginHtml .= '<div class="b2s-plugin-warn-row"><br>
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-7">' . esc_html($value["name"]) . '</div>
                        <div class="col-sm-1">
                            <i class="glyphicon glyphicon-warning-sign glyphicon-warning pull-right"></i>
                        </div>
                        <div class="col-sm-2">
                            <a href="' . esc_url(B2S_Tools::getSupportLink("system_requirements")) . '" target="_blank" class="pull-right margin-right-15">' . esc_html__("resolve conflict", "blog2social") . '</a>
                        </div>
                    </div>
                </div>';
            }
        }
        if (!empty($pluginHtml)) {
            $pluginHtml = '<div class="row"><div class="col-sm-10 b2s-text-bold">' . esc_html__("Plugin Warnings:", "blog2social") . '</div></div>' . $pluginHtml;
        }
        return $systemHtml . $pluginHtml;
    }

    public function blogData() {
        $path = $this->getBlogPath();
        $url = $this->getBlogUrl();
        $options = $this->getUserOptions();
        $version = $this->getUserVersion();
        $theme = get_template();
        $blogData = array('blogUrl' => $url, 'blogUserId' => B2S_PLUGIN_BLOG_USER_ID, 'options' => $options, 'WP_MEMORY_LIMIT' => WP_MEMORY_LIMIT, "max_execution_time" => ini_get('max_execution_time'), 'version' => $version, 'theme' => $theme);
        return array("systemData" => $this->systemData, "blogData" => $blogData, "pluginData" => $this->pluginData);
    }

    private function getWordpressVersion() {
        return get_bloginfo('version');
    }

    private function getPhpVersion() {
        return phpversion();
    }

    private function getB2sPluginVersion() {
        $b2sLastVersion = get_option('b2s_plugin_version');
        return ($b2sLastVersion !== false) ? B2S_Util::getVersion(B2S_PLUGIN_VERSION) : '';
    }

    private function getB2sNeededVersion() {
        $args = array(
            'timeout' => '15',
            'redirection' => '5',
            'user-agent' => "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)"
        );
        $result = wp_remote_retrieve_body(wp_remote_get(B2S_PLUGIN_API_ENDPOINT . 'update.txt', $args));
        $currentVersion = explode('#', $result);
        return (isset($currentVersion[0])) ? B2S_Util::getVersion($currentVersion[0]) : "";
    }

    private function getMysqlVersion() {
        global $wpdb;
        return $wpdb->db_version();
    }

    //Since V6.2.0 - disabled check database rights  - is checked in install prozess
    /*private function getDatabaseRights() {
        global $wpdb;
        $rights = $wpdb->get_var("SHOW GRANTS");
        if (isset($rights) && !empty($rights) && (strpos($rights, 'ALL PRIVILEGES') != false || (strpos($rights, 'CREATE') != false && strpos($rights, 'ALTER') != false))) {
            return true;
        } else {
            $tables_count = $wpdb->get_var("SELECT count(*) FROM information_schema.tables WHERE table_name LIKE '{$wpdb->prefix}b2s_%%'");
            if (isset($tables_count) && !empty($tables_count) && $tables_count >= 8) {
                return true;
            }
        }
        return false;
    }*/

    //Since V6.6.0.5 - disabled php heartbeat check  - is checked in javascript
    /*private function getHeartbeat() {
        return wp_script_is('heartbeat', 'registered');
    }*/
    
    private function getPhpCurl() {
        return function_exists('curl_version');
    }
    
    private function getPhpMbstring() {
        return function_exists('mb_strlen');
    }

    private function getPhpDom() {
        return class_exists('DOMDocument');
    }

    private function getHotlinkProtection() {
        $image_uri = B2S_PLUGIN_DIR . '/assets/images/b2s/b2s_logo.png';
        $image_url = plugins_url('/assets/images/b2s/b2s_logo.png', B2S_PLUGIN_FILE);
        if (file_exists($image_uri)) {
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array(
                        "action" => 'checkSysReq',
                        'type' => 'hotLink',
                        'image' => $image_url
                            ), 15), true);
            if (isset($result['result']) && $result['result'] !== false && isset($result['hotLink']) && $result['hotLink'] == false) {
                return true;
            }
        }
        return false;
    }

    private function getBlogPath() {
        return get_home_path();
    }

    private function getBlogUrl() {
        return get_site_url();
    }

    private function getUserOptions() {
        $options = get_option('B2S_PLUGIN_OPTIONS_' . B2S_PLUGIN_BLOG_USER_ID);
        if (isset($options["auto_post_import"]["network_auth_id"])) {
            unset($options["auto_post_import"]["network_auth_id"]);
        }
        if (isset($options["auth_sched_time"])) {
            unset($options["auth_sched_time"]);
        }
        return $options;
    }

    private function getUserVersion() {
        return get_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
    }

    private function getPluginData() {
        $this->pluginData = array();
        $pluginList = get_plugins();
        if (isset($pluginList) && is_array($pluginList) && !empty($pluginList)) {
            foreach ($pluginList as $key => $value) {
                if (isset($value['Name']) && !empty($value['Name'])) {
                    if (preg_match("/(" . implode("|", unserialize(B2S_PLUGIN_SYSTEMREQUIREMENT_PLUGINWARNING_WORDS)) . ")/i", $value['Name'])) {
                        array_push($this->pluginData, array('name' => $value['Name'], 'warning' => true));
                    } else {
                        array_push($this->pluginData, array('name' => $value['Name'], 'warning' => false));
                    }
                }
            }
        }
    }
    //deprecated since V 6.8.0
    private function getWpJson() {
        $base_url = get_site_url();
        $url = (substr($base_url, -1) == "/") ? $base_url."wp-json" : $base_url."/wp-json";
        $result = json_decode(B2S_Api_Get::get($url), true);
        if(isset($result["url"]) && !empty($result["url"]) && $result["url"] == $base_url){
            return true;
        } else {
            return false;
        }
    }
    
    private function getOpenssl() {
        return function_exists("openssl_private_decrypt");
    }

}
