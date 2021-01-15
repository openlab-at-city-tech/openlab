<?php

//by Install
class B2S_System {

    public function __construct() {
        
    }

    public function check($action = 'before') {
        $result = array();
        if ($action == 'before') {
            if (!$this->checkCurl()) {
                $result['curl'] = false;
            }
        }
        return empty($result) ? true : $result;
    }

    private function checkCurl() {
        return function_exists('curl_version');
    }

    public function getErrorMessage($errors, $removeBreakline = false) {
        $output = '';
        if (is_array($errors) && !empty($errors)) {
            foreach ($errors as $error => $status) {
                if (!$status && $error == 'curl') {
                    $output .= esc_html__('Blog2Social used cURL. cURL is not installed in your PHP installation on your server. Install cURL and activate Blog2Social again.', 'blog2social');
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= sprintf(__('<a href="%s" target="_blank">Please find more Information and help in our FAQ</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('system')));
                }
                if (!$status && $error == 'dbTable') {
                    $output .= esc_html__('Blog2Social does not seem to have permission to write in your WordPress database. Please assign Blog2Social the permission to write in the WordPress database. Please also make sure that your MySQL server runs on v5.5.3 or higher, or ask your server administrator to do it for you.', 'blog2social');
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= sprintf(__('<a href="%s" target="_blank">Please find more Information and help in our FAQ</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('system')));
                }
            }
        }
        return $output;
    }

    public function deactivatePlugin() {
        deactivate_plugins(B2S_PLUGIN_BASENAME);
    }

    //V5.7.0 White-Label-Solution
    public static function isblockedArea($area = '', $isAdmin = false, $general = false) {
        if (defined('B2S_PLUGIN_WHITE_LABEL')) {
            if (B2S_PLUGIN_WHITE_LABEL === true) {
                if ($general) {
                    return true;
                }
                if (defined('B2S_PLUGIN_WHITE_LABEL_BLOCKED_AREA')) {
                    $blocked = unserialize(B2S_PLUGIN_WHITE_LABEL_BLOCKED_AREA);
                    if (is_array($blocked) && !empty($blocked)) {
                        if (in_array(trim($area), $blocked)) {
                            if ((trim($area) == 'B2S_LICENSE_MODUL_EDIT' || trim($area) == 'B2S_MENU_ITEM_LICENSE') && $isAdmin) {
                                return false;
                            }
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    //V5.7.0 White-Label-Solution 
    public static function customizeArea() {
        if (defined('B2S_PLUGIN_WHITE_LABEL')) {
            if (B2S_PLUGIN_WHITE_LABEL === true) {
                if (defined('B2S_PLUGIN_WHITE_LABEL_LOGO')) {
                    if (!empty(B2S_PLUGIN_WHITE_LABEL_LOGO)) {
                        $file = get_home_path() . B2S_PLUGIN_WHITE_LABEL_LOGO;
                        if (file_exists($file)) {
                            return array('image_path' => get_home_url() . B2S_PLUGIN_WHITE_LABEL_LOGO);
                        }
                    }
                }
            }
        }
        return false;
    }

}
