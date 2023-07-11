<?php

class B2S_QuickPost {

    private $setPreFillText = array(0 => array(1 => 239, 2 => 255, 3 => 239, 6 => 300, 12 => 240, 17 => 442, 19=> 239, 38 => 500, 39 => 2000), 1 => array(1 => 239, 3 => 239,6 => 300, 17 => 442, 19 => 239), 2 => array(1 => 239, 17 => 442, 19 => 239));
    private $setPreFillTextLimit = array(0 => array(1 => 500, 2 => 254, 3 => 400, 6 => 400, 12 => 400, 19 => 400, 38 => 500, 39 => 2000), 1 => array(1 => 400, 3 => 400,6 => 400, 19 => 400), 2 => array(1 => 400, 19 => 9000));
    private $content;
    private $title;
    private $template;

    public function __construct($content = '', $title = '') {
        $this->content = sanitize_textarea_field($content);
        $this->title = sanitize_text_field($title);
        $this->template = ((defined('B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT')) ? unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT) : false);
    }

    public function prepareShareData($networkAuthId = 0, $networkId = 0, $networkType = 0, $postFormat = 0) {
        if ((int) $networkId > 0 && (int) $networkAuthId > 0) {
            $postData = array('content' => '', 'custom_title' => '', 'tags' => array(), 'network_auth_id' => (int) $networkAuthId);
            //PostFormat
            if (in_array($networkId, array(1, 2, 3, 12, 19, 24))) {
                $postData['post_format'] = $postFormat;
            }
            //Content
            $limit = ((is_array($this->template) && isset($this->template[$networkId][$networkType]['short_text']['limit'])) ? $this->template[$networkId][$networkType]['short_text']['limit'] : (isset($this->setPreFillTextLimit[$networkType][$networkId]) ? (int) $this->setPreFillTextLimit[$networkType][$networkId] : false));
            $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($this->content, (int) $this->setPreFillText[$networkType][$networkId], $limit) : $this->content;
            if ($networkId == 7 || $networkId == 9 || ($networkId == 8 && $networkType != 0) | ($networkId == 19 && $networkType != 0) || $networkId == 39) {
                $postData['custom_title'] = $this->title;
            }
            if ($networkId == 15) {
                $postData['content'] = $this->title;
            }
            return $postData;
        }
        return false;
    }

}
