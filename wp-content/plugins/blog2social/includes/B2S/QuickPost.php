<?php

/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
 */

class B2S_QuickPost {

    private $setPreFillText = array(0 => array(1 => 239, 2 => 255, 3 => 239, 6 => 300, 12 => 240, 17 => 442, 19 => 239, 36 => 200, 38 => 500, 39 => 2000, 42 => 1000, 43 => 279, 44 => 300, 45 => 255, 46 => 500), 1 => array(1 => 239, 3 => 239, 6 => 300, 12 => 240, 17 => 442, 19 => 5000, 42 => 1000), 2 => array(1 => 239, 17 => 442, 19 => 239));
    private $setPreFillTextLimit = array(0 => array(1 => 500, 2 => 254, 3 => 400, 6 => 400, 12 => 400, 19 => 400, 36 => 400, 38 => 500, 39 => 2000, 42 => 1000, 43 => 279, 44 => 400, 45 => 254 , 46 => 1000), 1 => array(1 => 400, 3 => 400, 6 => 400, 19 => 60000, 42 => 1000), 2 => array(1 => 400, 19 => 9000));
    private $content;
    private $title;
    private $optionalLink;
    private $url;
    private $template;

    public function __construct($content = '', $title = '', $optionalLink = '', $url = '') {
        $this->content = sanitize_textarea_field($content);
        $this->title = sanitize_text_field($title);
        $this->optionalLink = sanitize_text_field($optionalLink);
        $this->url = esc_url_raw($url);

    
        $this->template = ((defined('B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT')) ? unserialize(B2S_PLUGIN_NETWORK_SETTINGS_TEMPLATE_DEFAULT) : false);
    }

    public function prepareShareData($networkAuthId = 0, $networkId = 0, $networkType = 0, $postFormat = 0) {

        $content= $this->content;

        if ((int) $networkId > 0 && (int) $networkAuthId > 0) {
    
           $postData = array('content' => '', 'custom_title' => '', 'tags' => array(), 'network_auth_id' => (int) $networkAuthId);

            //PostFormat
            if (in_array($networkId, array(1, 2, 3, 12, 19, 24, 43, 44, 45))) {
                $postData['post_format'] = $postFormat;
            }
            
            //Content
            $limit = ((is_array($this->template) && isset($this->template[$networkId][$networkType]['short_text']['limit'])) ? $this->template[$networkId][$networkType]['short_text']['limit'] : (isset($this->setPreFillTextLimit[$networkType][$networkId]) ? (int) $this->setPreFillTextLimit[$networkType][$networkId] : false));
            
            $addOptionalLink=false;
      
            if(!empty($this->optionalLink)){

                if($limit == 0){
                    $addOptionalLink=true;
                }else{
                    $optionalLinkLength = strlen("\n\n" . $this->optionalLink);
                    if(!($optionalLinkLength>$limit)){
                        $limit= $limit-$optionalLinkLength;
                        $addOptionalLink=true;
                    }
                } 
            }

            $postData['content'] = (isset($this->setPreFillText[$networkType][$networkId])) ? B2S_Util::getExcerpt($content, (int) $this->setPreFillText[$networkType][$networkId], $limit) : $content;
           
            if($addOptionalLink){
                $postData['content'] = $postData['content']. "\n\n" . $this->optionalLink;
            }
           
            if ($networkId == 7 || $networkId == 9 || ($networkId == 8 && $networkType != 0) | ($networkId == 19 && $networkType != 0) || $networkId == 39 || $networkId == 36) {
                $postData['custom_title'] = $this->title;
            }
            if ($networkId == 15) {
                $postData['content'] = $this->title;
            }
            return $postData;
        }
        return false;
    }

    public function prepareShareDataFromTemplate($networkAuthId = 0, $networkId = 0, $networkType = 0, $postFormat = 0, $networkKind = 0) {
        
        $content= $this->content;

        if ((int) $networkId > 0 && (int) $networkAuthId > 0) {

            $postData = array('content' => '', 'custom_title' => '', 'tags' => array(), 'network_auth_id' => (int) $networkAuthId);
            //PostFormat
            if (in_array($networkId, array(1, 2, 3, 12, 19, 24, 43, 44, 45))) {
                $postData['post_format'] = $postFormat;
            }

            require_once(B2S_PLUGIN_DIR . 'includes/B2S/Ship/Item.php');
            
            $b2sItem = new B2S_Ship_Item(0,'en','','',0,false,array(),false,true);
            $emptyPostData = new stdClass();
            $emptyPostData->post_content = '';
            $emptyPostData->post_title = $this->title;
            $emptyPostData->post_author = B2S_PLUGIN_BLOG_USER_ID;
            $emptyPostData->post_type = '';
            $b2sItem->setPostData($emptyPostData);
            $b2sItem->setPostUrl(!empty($this->url) ? $this->url : $this->optionalLink);
            $networkData = array(
                'networkId' => $networkId,
                'networkType' => $networkType,
                'networkKind' => $networkKind
            );
            
            $message= $b2sItem->getMessagebyTemplate((object) $networkData, $content);

            // Get comment from template if allowed for this network
            if (defined('B2S_PLUGIN_USER_VERSION') && B2S_PLUGIN_USER_VERSION >= 2 && B2S_Tools::isCommentAllowed($networkId, $networkType)) {
                $commentValue = $b2sItem->getMessagebyTemplate((object) $networkData, $content, false, true);
                if (!empty($commentValue)) {
                    $postData['comment'] = $commentValue;
                }
            }


            //Add link if is within Limit
            if(!empty($this->optionalLink)){
                $limit = ((is_array($this->template) && isset($this->template[$networkId][$networkType]['short_text']['limit'])) ? $this->template[$networkId][$networkType]['short_text']['limit'] : (isset($this->setPreFillTextLimit[$networkType][$networkId]) ? (int) $this->setPreFillTextLimit[$networkType][$networkId] : false));
                if($limit==0){
                    $message .= "\n\n" . $this->optionalLink;
                }else
                {
                    if((strlen($message) + strlen("\n\n" . $this->optionalLink))< $limit){
                        $message .= "\n\n" . $this->optionalLink;
                    }
                }

                
            }

            $postData['content'] = $message;

            if ($networkId == 7 || $networkId == 9 || ($networkId == 8 && $networkType != 0) | ($networkId == 19 && $networkType != 0) || $networkId == 39 || $networkId == 36) {
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
