<?php

class B2S_Video_Upload {

    private $maxServerPostSize = 10485760; //bytes, 10M
    private $maxDefaultPostSize = 5242880; //bytes, 5M
    private $maxClientPostSize;

    public function __construct() {
        
    }

    //in KB
    private function setMaxUploadChunkSize() {
        $pms = ini_get('post_max_size');
        if ($pms !== false && !empty($pms)) {
            $pms = B2S_Util::returnInByts($pms);
            if ($pms > $this->maxServerPostSize) {
                $this->maxClientPostSize = $this->maxServerPostSize;
            } else {
                $this->maxClientPostSize = $pms - 10240; //-10kb
            }
        } else {
            $this->maxClientPostSize = $this->maxDefaultPostSize;
        }
    }

    public function uploadVideo($video_post_id = 0, $videoToken = '') {
        $this->setMaxUploadChunkSize();
        $file = get_attached_file($video_post_id);
        if (file_exists($file)) {
            $filesize = filesize($file);
            if ($filesize !== false) {
                $file = fopen($file, 'r');
                if ($file !== false) {
                    global $wpdb;
                    $trys = 0;
                    $lastCall = false;
                    $pointer = 0; //pointer to the exact bit for the next chunk
                    $counter = 0;
                    $currentChunk = 1;
                    $maxCountChunk = ceil($filesize / $this->maxClientPostSize);

                    while ($counter != $maxCountChunk && $trys <= 1) {
                        fseek($file, $pointer);
                        $chunk = fread($file, $this->maxClientPostSize);

                        //upload chunks
                        $args = array(
                            'method' => 'POST',
                            'body' => array(
                                'video_token' => trim($videoToken),
                                'max_count_chunks' => (int) $maxCountChunk,
                                'current_chunk' => $currentChunk,
                                'chunk' => $chunk,
                            ),
                            'timeout' => 45,
                            'redirection' => '5',
                            'user-agent' => "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)",
                        );
                        
                        $resultChunk = wp_remote_retrieve_body(wp_remote_post(B2S_PLUGIN_API_VIDEO_UPLOAD_ENDPOINT . 'video/upload', $args));
                        if (!empty($resultChunk)) {
                            $resultChunk = json_decode($resultChunk, true);
                            if (isset($resultChunk['error']) && (int) $resultChunk['error'] == 0) {
                                $counter++;
                                $pointer += $this->maxClientPostSize;
                                $currentChunk += 1;
                                $trys = 0;
                                $lastCall = true;
                            } else {
                                if (isset($resultChunk['b2s_error_code']) && $resultChunk['b2s_error_code'] == 'VIDEO_TOKEN') {
                                    return array('upload' => false, 'error_code' => $resultChunk['b2s_error_code']);
                                } else {
                                    $trys++;
                                    $lastCall = false;
                                }
                            }
                            if (isset($resultChunk['networks']) && !empty($resultChunk['networks']) && is_array($resultChunk['networks'])) {
                                foreach ($resultChunk['networks'] as $k => $value) {
                                    if (isset($value['error']) && (int) $value['error'] == 1 && isset($value['post_id']) && (int) $value['post_id'] > 0 && isset($value['b2s_error_code']) && !empty($value['b2s_error_code'])) {
                                        $data = array('hook_action' => 0, 'publish_error_code' => $value['b2s_error_code']);
                                        $where = array('id' => $value['post_id']);
                                        $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d', '%s'), array('%d'));
                                    }
                                }
                                //check for stopping upload
                                $sql = "SELECT id FROM {$wpdb->prefix}b2s_posts WHERE hook_action = %d AND publish_error_code = %s AND upload_video_token = %s";
                                $res = $wpdb->get_results($wpdb->prepare($sql, 6, '', $videoToken), ARRAY_A);
                                if (empty($res)) {
                                    return array('upload' => false);
                                }
                            }
                        } else {
                            $trys++;
                            $lastCall = false;
                        }
                    }
                    //Upload finished
                    if ($lastCall) {
                        return array('upload' => true);
                    }
                    return array('upload' => false, 'error_code' => 'VIDEO_UPLOAD');
                }
            }
        }
        return array('upload' => false, 'error_code' => 'VIDEO_FILE');
    }

}
