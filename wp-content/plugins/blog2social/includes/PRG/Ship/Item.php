<?php

class PRG_Ship_Item {

    private $userData;

    public function __construct() {
        
    }

    public function getMandant() {
        global $wpdb;
        $sqlUserData = $wpdb->prepare("SELECT * FROM `{$wpdb->prefix}b2s_user_contact` WHERE `blog_user_id` = %d", B2S_PLUGIN_BLOG_USER_ID);
        $this->userData = $wpdb->get_row($sqlUserData);
        return $this->userData;
    }

    public function getCountryHtml() {
        $countries = simplexml_load_string(PRG_Api_Get::get(B2S_PLUGIN_PRG_API_ENDPOINT . 'get.php?action=getCountry'));
        $prgKeyName = 'titel_' . substr(B2S_LANGUAGE, 0, 2);
        $content = '';
        foreach ($countries as $val) {
            $content .= '<option value="' . esc_attr($val->tag) . '"';
            if (isset($this->userData->land_presse) && !empty($this->userData->land_presse)) {
                if ($val->tag == $this->userData->land_presse) {
                    $content .= ' selected="selected"';
                }
            } else {
                //default
                if ($val->tag == "US") {
                    $content .= ' selected="selected"';
                }
            }
            $content .= '>' . esc_html($val->$prgKeyName) . '</option>' . PHP_EOL;
        }
        return $content;
    }

    public function getCategoryHtml() {
        $cats = simplexml_load_string(PRG_Api_Get::get(B2S_PLUGIN_PRG_API_ENDPOINT . 'get.php?action=getCategory'));
        $prgKeyName = 'titel_' . substr(B2S_LANGUAGE, 0, 2);
        $content = '';
        foreach ($cats as $val) {
            $content .= '<option value="' . esc_attr($val->id) . '">' . esc_html($val->$prgKeyName) . '</option>' . PHP_EOL;
        }
        return $content;
    }

}
