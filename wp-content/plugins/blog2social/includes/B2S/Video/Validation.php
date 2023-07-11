<?php

class B2S_Video_Validation {

    public $networkProperties;

    public function __construct() {
        $this->loadNetworkProperties();
    }

    public function loadNetworkProperties() {
        $checkUpdateOption = get_option('B2S_PLUGIN_UPDATE_TIME_NETWORK_PROPERTIES');
        $this->networkProperties = get_option('B2S_PLUGIN_DATA_NETWORK_PROPERTIES');
        if ($checkUpdateOption == false || $this->networkProperties == false || $checkUpdateOption < time()) {
            $properties = $this->getNetworkProperties();
            if ($properties !== false) {
                $this->networkProperties = $properties;
                update_option('B2S_PLUGIN_UPDATE_TIME_NETWORK_PROPERTIES', time() + 86400, false);
                update_option('B2S_PLUGIN_DATA_NETWORK_PROPERTIES', $this->networkProperties, false);
            }
        }
    }

    private function getNetworkProperties() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getNetworkProperties', 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));
        if (isset($result->result) && (int) $result->result == 1 && isset($result->data) && !empty($result->data)) {
            return $result->data;
        }
        return false;
    }

    public function isValidVideoForNetwork($postId = 0, $networkId = 0, $networkType = 0) {
        if (isset($this->networkProperties) && !empty($this->networkProperties) && is_array($this->networkProperties)) {
            if ((int) $postId != 0 && (int) $networkId != 0) {
                $videoFileForAnalyse = get_attached_file($postId);
                $video_meta = wp_read_video_metadata($videoFileForAnalyse);
                if (is_array($video_meta) && !empty($video_meta) && isset($video_meta['filesize']) && isset($video_meta['length']) && isset($video_meta['fileformat']) && !empty($video_meta['fileformat'])) {
                    foreach ($this->networkProperties as $key => $network) {
                        if ((int) $network->network_id == (int) $networkId && (int) $network->network_type == (int) $networkType) {
                            if (($video_meta['filesize'] / 1024) >= $network->video_max_size) {
                                $mfs = $network->video_max_size / 1024;
                                return array('result' => false, 'content' => sprintf(__('Your video is exceeding the maximum file size of %s Megabyte. Please compress your video file or select a video with a smaller file size.', 'blog2social'),sanitize_text_field($mfs)));
                            }
                            if ($video_meta['length'] >= $network->video_max_length) {
                                return array('result' => false, 'content' => sprintf(__('Your video is exceeding the maximum length. The maximum video length for this network is %s seconds. Please select a shorter video.', 'blog2social'), sanitize_text_field($network->video_max_length)));
                            }
                            if (strpos($network->video_format, strtolower($video_meta['fileformat'])) === false) {
                                return array('result' => false, 'content' => sprintf(__('Please check the file format of your video. This network only supports the following video formats: %s', 'blog2social'), sanitize_text_field($network->video_format)));
                            }
                            
                            if(($networkId == 1 && $networkType == 1) || $networkId == 12) {

                                $reelSupport = array(
                                    'reel_format' => $network->reel_support->reel_format,
                                    'reel_max_length' => $network->reel_support->reel_max_length,
                                    'reel_min_length' => $network->reel_support->reel_min_length,
                                    'reel_min_framerate' => $network->reel_support->reel_min_framerate,
                                    'reel_min_resolution_x' => $network->reel_support->reel_min_resolution_x,
                                    'reel_min_resolution_y' => $network->reel_support->reel_min_resolution_y,
                                    'reel_aspect_ratio' => $network->reel_support->reel_aspect_ratio_x . ':' . $network->reel_support->reel_aspect_ratio_y
                                );

                                if (strpos($reelSupport['reel_format'], strtolower($video_meta['fileformat'])) === false) {
                                    return array('result' => true, 'canReel' => array('result' => false, 'content' => sprintf(__('Please check the file type of your video. This network only supports the following video types: %s', 'blog2social'), sanitize_text_field($reelSupport['reel_format']))));
                                }
                                
                                if ($video_meta['length'] < $reelSupport['reel_min_length']) {
                                    return array('result' => true, 'canReel' => array('result' => false, 'content' => sprintf(__('Your video is below the minimum length. The minimum video length for this network is %s seconds. Please select a longer video.', 'blog2social'), sanitize_text_field($reelSupport['reel_min_length']))));
                                }

                                if ($video_meta['length'] >= $reelSupport['reel_max_length']) {
                                    return array('result' => true, 'canReel' => array('result' => false, 'content' => sprintf(__('Your video exceeds the maximum length. The maximum video length for this network is %s seconds. Please select a shorter video.', 'blog2social'), sanitize_text_field($reelSupport['reel_max_length']))));
                                }

                                $dataForReels = $this->getMetaDataForReels($videoFileForAnalyse);
                                
                                if($dataForReels === false) {
                                    return array('result' => true, 'canReel' => array('result' => false, 'content' => sprintf(__('Your video could not be posted, because your video format seems to be invalid. Please check your video file for errors and property rights. (Error Code: V002)', 'blog2social'))));
                                }

                                $returnRotate = $this->checkRotateForVideo($video_meta['width'], $video_meta['height'], $video_meta['fileformat'], $dataForReels['rotate']);
                                
                                if ($dataForReels['frame_rate'] < $reelSupport['reel_min_framerate']) {
                                    return array('result' => true, 'canReel' => array('result' => false, 'content' => sprintf(__('Your video frame rate is too low. The minimum frame rate is %s.', 'blog2social'), sanitize_text_field($reelSupport['reel_aspect_ratio']))));
                                }

                                if(strtolower($video_meta['fileformat'])){
                                    if($this->getAspectRatio($returnRotate['resolution_x'], $returnRotate['resolution_y']) != $reelSupport['reel_aspect_ratio']) {
                                        return array('result' => true, 'canReel' => array('result' => false, 'content' => sprintf(__('Your video does not have the correct %s aspect ratio.', 'blog2social'), sanitize_text_field($reelSupport['reel_aspect_ratio']))));
                                    }
                                }
                                if($returnRotate['resolution_x'] < $reelSupport['reel_min_resolution_x'] || $returnRotate['resolution_y'] < $reelSupport['reel_min_resolution_y']) {
                                    return array('result' => true, 'canReel' => array('result' => false, 'content' => sprintf(__('Your video resolution is too low. The minimum resolution for this network is %s x %s (%sp).', 'blog2social'), sanitize_text_field($reelSupport['reel_min_resolution_x']), sanitize_text_field($reelSupport['reel_min_resolution_y']), sanitize_text_field($reelSupport['reel_min_resolution_x']))));
                                }

                                return array('result' => true, 'canReel' => array('result' => true));
                            }

                            return array('result' => true);
                        }
                    }
                    return array('result' => false, 'content' => esc_html__('Your video could not be posted, because the server did not respond Please try again later! Contact our support team, if the failure should persist. (Error Code: V001)', 'blog2social'));
                }
                return array('result' => false, 'content' => esc_html__('Your video could not be posted, because your video format seems to be invalid. Please check your video file for errors and property rights. (Error Code: V002)', 'blog2social'));
            }
            return array('result' => false, 'content' => esc_html__('Your video could not be posted. Please try again! (Error Code: V003)', 'blog2social'));
        }
        return array('result' => false, 'content' => esc_html__('Your video could not be uploaded. Please check your video file for errors and try again! (Error Code: V004)', 'blog2social'));
    }

    private function checkRotateForVideo($resolution_x, $resolution_y, $fileformat, $videoRotate) {
        // mp4: if the rotate 90 is, change the resolution_x(width) vs resolution_y(height)
        if($resolution_y < $resolution_x && $fileformat == 'mp4' && $videoRotate === 90) {
            return array('resolution_y' => $resolution_x, 'resolution_x' => $resolution_y);
        }
        return array('resolution_y' => $resolution_y, 'resolution_x' => $resolution_x);
    }

    private function getAspectRatio($resolution_x, $resolution_y)
    {
        $calc = $resolution_y / $resolution_x;

        if(abs($calc - 16/9) == 0) {
            return '9:16';
        } else if(abs($calc - 5/3) == 0) {
            return '3:5';
        } else if(abs($calc - 4/3) == 0) {
            return '3:4';
        }
        return false;
    }

    public function getMetaDataForReels($file) {
        if (!file_exists($file)) {
            return false;
        }

        if(!class_exists('getID3')) {
            $pathToClass = ABSPATH . WPINC . '/ID3/getid3.php';
            if (!file_exists($pathToClass)) {
                return false;
            } else {
                require $pathToClass;
            }
        }

        $id3 = new getID3();
        $metadata = array();
        $data = $id3->analyze($file);

        $metadata['rotate'] = isset($data['video']['rotate']) && !empty($data['video']['rotate']) ? $data['video']['rotate'] : 0;
        $metadata['frame_rate'] = isset($data['video']['frame_rate']) && !empty($data['video']['frame_rate']) ? $data['video']['frame_rate'] : 0;

        return $metadata;
    }
    
}
