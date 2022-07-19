<?php

require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/ItemEdit.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');

class B2S_Calendar_Item {

    private $sched_date = null;
    private $publish_date = null;
    private $network_id = null;
    private $post_title = null;
    private $post_type = null;
    private $blog_user_id = null;
    private $network_display_name = null;
    private $b2s_id = null;
    private $b2s_sched_type = null;
    private $user_timezone = null;
    private $ship_item = null;
    private $network_type = null;
    private $network_auth_id = null;
    private $sched_data = null;
    private $image_url = null;
    private $post_format = null;
    private $sched_details_id = null;
    private $relay_primary_post_id = null;
    private $relay_primary_sched_date = null;
    private $post_for_relay = null;
    private $post_for_approve = null;
    private $relay_delay_min = null;
    private $publish_link = null;
    private $status = null;
    private $errorCode = null;
    private $errorText = null;
    private $multi_images = null;
    private $errorTextList = null;

    public function __construct(\StdClass $data = null) {
        $this->errorTextList = unserialize(B2S_PLUGIN_NETWORK_ERROR);
        if (isset($data)) {
            $this
                    ->setSchedData($data->sched_data)
                    ->setSchedDate($data->sched_date)
                    ->setNetworkId($data->network_id)
                    ->setPostTitle($data->post_title)
                    ->setPostType($data->post_type)
                    ->setBlogUserId($data->blog_user_id)
                    ->setNetworkDisplayName($data->network_display_name)
                    ->setUserTimezone($data->user_timezone)
                    ->setPostId($data->post_id)
                    ->setNetworkType($data->network_type)
                    ->setNetworkAuthId($data->network_auth_id)
                    ->setB2SId($data->b2s_id)
                    ->setB2SSchedType($data->b2s_sched_type)
                    ->setSchedDetailsId($data->sched_details_id)
                    ->setImageUrl($data->image_url)
                    ->setRelayPrimaryPostId($data->relay_primary_post_id)
                    ->setPostForRelay($data->post_for_relay)
                    ->setPostForApprove($data->post_for_approve)
                    ->setPublishLink($data->publish_link);

            if ($data->network_id == 1 || $data->network_id == 2 || $data->network_id == 3 || $data->network_id == 4 || $data->network_id == 12 || $data->network_id == 17 || $data->network_id == 19 || $data->network_id == 24) {
                $this->setPostFormat();
            }
            if ($data->network_id == 2 && isset($data->relay_primary_sched_date)) {
                $this->setRelayPrimarySchedDate($data->relay_primary_sched_date);
                $this->setRelayDelayMin($data->relay_delay_min);
            }
            if (isset($data->publish_date)) {
                $this->setPublishDate($data->publish_date);
            }
            if (isset($data->publish_error_code)) {
                $this->setStatus($data->publish_error_code);
                $this->setErrorCode($data->publish_error_code);
                $this->setErrorText($data->publish_error_code);
            }
            if (isset($data->sched_data)) {
                $this->setMultiImages($data->sched_data);
            }
        }
    }

    public function setPublishLink($value) {
        $this->publish_link = trim($value);
        return $this;
    }

    public function getPublishLink() {
        return $this->publish_link;
    }

    public function setPostForRelay($value) {
        $this->post_for_relay = (int) $value;
        return $this;
    }

    public function getPostForRelay() {
        return $this->post_for_relay;
    }

    public function setPostForApprove($value) {
        $this->post_for_approve = (int) $value;
        return $this;
    }

    public function getPostForApprove() {
        return $this->post_for_approve;
    }

    public function setRelayDelayMin($value) {
        $this->relay_delay_min = (int) $value;
        return $this;
    }

    public function getRelayDelayMin() {
        return $this->relay_delay_min;
    }

    public function setRelayPrimarySchedDate($value) {
        $this->relay_primary_sched_date = $value;
        return $this;
    }

    public function getRelayPrimarySchedDate() {
        return $this->relay_primary_sched_date;
    }

    public function setRelayPrimaryPostId($value) {
        $this->relay_primary_post_id = (int) $value;
        return $this;
    }

    public function getRelayPrimaryPostId() {
        return $this->relay_primary_post_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setSchedDate($value) {
        if (is_numeric($value) || is_null($value)) {
            $this->sched_date = $value;
        } else if (is_string($value) && $value != "0000-00-00 00:00:00") {
            $this->sched_date = strtotime($value);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getSchedDate() {
        return $this->sched_date;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setPublishDate($value) {
        if (is_numeric($value) || is_null($value)) {
            $this->publish_date = $value;
        } else if (is_string($value) && $value != "0000-00-00 00:00:00") {
            $this->publish_date = strtotime($value);
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getPublishDate() {
        return $this->publish_date;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setNetworkId($value) {
        if (is_numeric($value)) {
            $this->network_id = (int) $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getNetworkId() {
        return $this->network_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setSchedDetailsId($value) {
        if (is_numeric($value)) {
            $this->sched_details_id = (int) $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getSchedDetailsId() {
        return $this->sched_details_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setNetworkAuthId($value) {
        if (is_numeric($value)) {
            $this->network_auth_id = (int) $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getNetworkAuthId() {
        return $this->network_auth_id;
    }

    /**
     * @param string|array $value
     * @return $this
     */
    public function setSchedData($value) {
        if (is_string($value)) {
            $this->sched_data = unserialize($value);
            if (is_array($this->sched_data)) {
                //prepare Data
                foreach ($this->sched_data as $k => $v) {
                    if (!is_array($v)) {
                        $this->sched_data[$k] = stripslashes($v);
                    }
                }
            }
        } else if (is_array($value)) {
            $this->sched_data = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSchedData() {
        return $this->sched_data;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setNetworkType($value) {
        if (is_numeric($value)) {
            $this->network_type = (int) $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getNetworkType() {
        return $this->network_type;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setPostId($value) {
        if (is_numeric($value)) {
            $this->post_id = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getPostId() {
        return $this->post_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setB2SId($value) {
        if (is_numeric($value)) {
            $this->b2s_id = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getB2SId() {
        return $this->b2s_id;
    }
    
    /**
     * @param integer $value
     * @return $this
     */
    public function setB2SSchedType($value) {
        if (is_numeric($value)) {
            $this->b2s_sched_type = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getB2SSchedType() {
        return $this->b2s_sched_type;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPostTitle($value) {
        if (is_string($value)) {
            $this->post_title = B2S_Util::getTitleByLanguage($value, strtolower(substr(get_locale(), 0, 2)));
        }

        return $this;
    }

    public function setPostType($value) {
        if (is_string($value)) {
            $this->post_type = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPostTitle() {
        return $this->post_title;
    }

    /**
     * @return string
     */
    public function getPostType() {
        return $this->post_type;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setBlogUserId($value) {
        if (is_numeric($value)) {
            $this->blog_user_id = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getBlogUserId() {
        return $this->blog_user_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setUserTimezone($value) {
        if (is_numeric($value)) {
            $this->user_timezone = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getUserTimezone() {
        return $this->user_timezone;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setNetworkDisplayName($value) {
        if (is_string($value)) {
            $this->network_display_name = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkDisplayName() {
        return $this->network_display_name;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setImageUrl($value) {
        if (is_string($value)) {
            $this->image_url = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl() {
        return $this->image_url;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setPostFormat($value = null) {
        if ($value == null) {
            $sched_data = $this->getSchedData();
            if (is_array($sched_data)) {
                if (isset($sched_data['post_format'])) {
                    $this->post_format = (int) $sched_data['post_format'];
                }
            }
        } else {
            $this->post_format = $value;
        }
        return $this;
    }

    /**
     * @return integer
     */
    public function getPostFormat() {
        return $this->post_format;
    }

    /**
     * @return string
     */
    public function getAvatar() {
        $res = "";

        $user = get_user_by("id", $this->getBlogUserId());

        if ($user) {
            $res = get_avatar($user->user_email, 32);
        }

        return $res;
    }

    /**
     * @return string
     */
    public function getAuthor() {
        $res = "";

        $user = get_user_by("id", $this->getBlogUserId());

        if ($user) {
            $res = $user->display_name;
        }

        return $res;
    }

    public function setStatus($error = "") {
        if (!empty($error)) {
            $this->status = "error";
        } else {
            if ($this->sched_date == null && $this->publish_date != null) {
                $this->status = "published";
            } else {
                $this->status = "scheduled";
            }
        }
        return $this;
    }

    public function getStaus() {
        return $this->status;
    }
    
    public function setErrorCode($error = "") {
        $this->errorCode = $error;
        return $this;
    }

    public function getErrorCode() {
        return $this->errorCode;
    }
    
    public function setErrorText($error = "") {
        if (!empty($error) && isset($this->errorTextList[$error])) {
            if($this->network_id == 12 && $error == 'DEFAULT') {
                if($this->network_type == 0) {
                    $this->errorText = sprintf(__('The post cannot be published due to changes on the Instagram interface. More information in the <a href="%s" target="_blank">Instagram guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_error_private')));
                } else {
                    $this->errorText = sprintf(__('Your post could not be posted. More information in this <a href="%s" target="_blank">Instagram troubleshoot checklist</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_error_business')));
                }
            } else {
                $this->errorText = $this->errorTextList[$error];
            }
        } else {
            $this->errorText = "";
        }
        return $this;
    }

    public function getErrorText() {
        return $this->errorText;
    }
    
    public function setMultiImages($sched_data = "") {
        $multi_images = array();
        if (!empty($sched_data)) {
            $data = unserialize($sched_data);
            if(isset($data['multi_images'])) {
                $json_data = json_decode($data['multi_images'], true);
                if($json_data != false && !empty($json_data) && is_array($json_data)) {
                    $multi_images = $json_data;
                }
            }
        }
        $this->multi_images = $multi_images;
        return $this;
    }
    
    public function getMultiImages() {
        return $this->multi_images;
    }

    private function getColor() {
        $colors = ["#983b3b", "#79B232", "#983b7d", "#3b3b98", "#3b8e98", "#65983b", "#6b3b98", "#93983b", "#987d3b", "#985c3b"];
        $id = $this->getBlogUserId() % count($colors);
        return $colors[$id];
    }

    private function getNetworkName() {
        $names = unserialize(B2S_PLUGIN_NETWORK);
        if ($names[$this->getNetworkId()]) {
            return $names[$this->getNetworkId()];
        }

        return null;
    }

    /**
     * @return array
     */
    public function asCalendarArray() {

        return ["title" => $this->getPostTitle(),
            "post_type" => $this->getPostType(),
            "avatar" => $this->getAvatar(),
            "author" => $this->getAuthor(),
            "start" => (($this->getSchedDate() != null && (int) $this->getSchedDate() > 0) ? date("Y-m-d H:i:s", $this->getSchedDate()) : (($this->getPublishDate() != null && (int) $this->getPublishDate() > 0) ? date("Y-m-d H:i:s", $this->getPublishDate()) : date("Y-m-d H:i:s"))),
            "color" => $this->getColor(),
            "network_name" => $this->getNetworkName(),
            "network_id" => $this->getNetworkId(),
            "network_type" => $this->getNetworkType(),
            "network_auth_id" => $this->getNetworkAuthId(),
            "post_format" => $this->getPostFormat(),
            "relay_primary_post_id" => $this->getRelayPrimaryPostId(),
            "post_for_relay" => $this->getPostForRelay(),
            "post_for_approve" => $this->getPostForApprove(),
            "b2s_id" => $this->getB2SId(),
            "b2s_sched_type" => $this->getB2SSchedType(),
            "post_id" => $this->getPostId(),
            "user_timezone" => $this->getUserTimezone(),
            "profile" => $this->getNetworkDisplayName(),
            "status" => $this->getStaus(),
            "errorCode" => $this->getErrorCode(),
            "errorText" => $this->getErrorText(),
            "publish_link" => $this->getPublishLink()];
    }

    /**
     * @return B2S_Ship_Item
     */
    public function ship_item() {
        if (is_null($this->ship_item)) {
            $this->ship_item = new B2S_Calendar_ItemEdit($this->getPostId());

            $sched_data = $this->getSchedData();
            if (is_array($sched_data)) {
                if (!empty($sched_data['url'])) {
                    $this->ship_item->setPostUrl($sched_data['url']);
                }
                if (!empty($sched_data['custom_title'])) {
                    $this->ship_item->setTitle($sched_data['custom_title']);
                }
            }


            $this->ship_item->setB2SId($this->getB2SId());
        }

        return $this->ship_item;
    }

    public function getEditHtml($view = 'modal') {
        $itemData = array('networkAuthId' => $this->getNetworkAuthId(),
            'networkId' => $this->getNetworkId(),
            'network_display_name' => $this->getNetworkDisplayName(),
            'networkType' => $this->getNetworkType(),
            'image_url' => $this->getImageUrl(),
            'multi_images' => $this->getMultiImages(),
            'relay_primary_post_id' => $this->getRelayPrimaryPostId(),
            'post_for_relay' => $this->getPostForRelay(),
            'post_for_approve' => $this->getPostForApprove(),
            'post_format' => $this->getPostFormat(),
            'view' => $view,
            'networkTosGroupId' => '',
            'networkKind' => 0);
        
        return $this->ship_item()->getItemHtml((object) $itemData, false);
    }

}
