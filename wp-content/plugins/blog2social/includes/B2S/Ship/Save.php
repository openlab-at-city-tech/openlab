<?php

class B2S_Ship_Save {

    public $postData;
    public $postDataApprove;

    public function __construct() {
        $this->postData = array();
        $this->postDataApprove = array();  //Since V4.9.1 - Instant Sharing Facebook Profile
    }

    private function getNetworkDetailsId($network_id, $network_type, $network_auth_id, $network_display_name) {
        global $wpdb;

        //special case xing groups  contains network_display_name
        if ($network_id == 8 && $network_type == 2) {
            $networkDetailsIdSelect = $wpdb->get_col($wpdb->prepare("SELECT postNetworkDetails.id FROM {$wpdb->prefix}b2s_posts_network_details AS postNetworkDetails WHERE postNetworkDetails.network_auth_id = %s AND postNetworkDetails.network_display_name = %s", $network_auth_id, trim($network_display_name)));
        } else {
            $networkDetailsIdSelect = $wpdb->get_col($wpdb->prepare("SELECT postNetworkDetails.id FROM {$wpdb->prefix}b2s_posts_network_details AS postNetworkDetails WHERE postNetworkDetails.network_auth_id = %s", $network_auth_id));
        }
        if (isset($networkDetailsIdSelect[0])) {
            return (int) $networkDetailsIdSelect[0];
        } else {
            $wpdb->insert($wpdb->prefix . 'b2s_posts_network_details', array(
                'network_id' => (int) $network_id,
                'network_type' => (int) $network_type,
                'network_auth_id' => (int) $network_auth_id,
                'network_display_name' => $network_display_name), array('%d', '%d', '%d', '%s'));
            return $wpdb->insert_id;
        }
    }

    private function lookupNetworkDetailsId($network_auth_id) {
        global $wpdb;
        $networkDetailsIdSelect = $wpdb->get_col($wpdb->prepare("SELECT postNetworkDetails.id FROM {$wpdb->prefix}b2s_posts_network_details AS postNetworkDetails WHERE postNetworkDetails.network_auth_id = %s", $network_auth_id));
        if (isset($networkDetailsIdSelect[0])) {
            return (int) $networkDetailsIdSelect[0];
        }
        return 0;
    }

    public function savePublishDetails($data, $relayData = array(), $quickShare = false) {
        global $wpdb;
        $networkDetailsId = $this->getNetworkDetailsId($data['network_id'], $data['network_type'], $data['network_auth_id'], $data['network_display_name']);

        //Since V4.9.1 - Instant Share Facebook Profile
        $shareApprove = (isset($data['instant_sharing']) && (int) $data['instant_sharing'] == 1) ? 1 : 0;

        if (!empty($relayData) && is_array($relayData)) {
            $data['relay_data'] = $relayData;
            $data['post_for_relay'] = 1;
        }

        $postData = array(
            'post_id' => $data['post_id'],
            'blog_user_id' => $data['blog_user_id'],
            'user_timezone' => $data['user_timezone'],
            'publish_date' => $data['publish_date'],
            'post_for_relay' => ((isset($data['post_for_relay']) && (int) $data['post_for_relay'] == 1) ? 1 : 0),
            'post_for_approve' => $shareApprove,
            'network_details_id' => $networkDetailsId,
            'post_format' => ((isset($data['post_format']) && ($data['post_format'] != null || $data['post_format'] == 0)&& $data['post_format'] !== '') ? (((int) $data['post_format'] >= 1) ? (int) $data['post_format'] : 0) : NULL),
        );
        $wpdb->insert($wpdb->prefix . 'b2s_posts', $postData, array('%d', '%d', '%d', '%s', '%d', '%d', '%d'));
        B2S_Rating::trigger();

        //approve == 0  else postDataApprove

        $data['internal_post_id'] = $wpdb->insert_id;

        if ($shareApprove == 0) {
            $this->postData['token'] = $data['token'];
            $this->postData["blog_user_id"] = $data["blog_user_id"];
            $this->postData["post_id"] = $data["post_id"];
            $this->postData["default_titel"] = $data["default_titel"];
            $this->postData["is_video"] = $data["is_video"];
            $this->postData["video_upload_size"] = $data["video_upload_size"];
            $this->postData["no_cache"] = (int) $data["no_cache"];
            $this->postData["lang"] = $data["lang"];
            $this->postData['user_timezone'] = $data['user_timezone'];

            unset($data['token']);
            unset($data['blog_user_id']);
            unset($data['post_id']);
            unset($data['default_titel']);
            unset($data['is_video']);
            unset($data['video_upload_size']);
            unset($data['no_cache']);
            unset($data['lang']);
            unset($data['user_timezone']);
            unset($data['publish_date']);
            if (!$quickShare) {
                unset($data['network_type']);
                unset($data['network_display_name']);
            }

            $this->postData['post'][] = $data;
        } else {
            $this->postDataApprove['post'][] = array('internal_post_id' => $data['internal_post_id'],
                'network_id' => $data['network_id'],
                'network_auth_id' => $data['network_auth_id'],
                'network_type' => $data['network_type'],
                'network_display_name' => $data['network_display_name'],
                'post_format' => (isset($data['post_format']) ? (int) $data['post_format'] : 0),
                'image_url' => $data['image_url'],
                'content' => $data['content'],
                'url' => $data['url'],
                'share_as_reel' => $data['share_as_reel']
            );
        }
    }

    public function saveVideoDetails($data = array(), $schedData = array()) {
        global $wpdb;
        $networkDetailsId = $this->getNetworkDetailsId($data['network_id'], $data['network_type'], $data['network_auth_id'], $data['network_display_name']);
        $serializeData = $data;

        $shareApprove = (isset($data['instant_sharing']) && (int) $data['instant_sharing'] == 1) ? 1 : 0;

        unset($serializeData['network_type']);
        unset($serializeData['network_display_name']);
        unset($serializeData['post_id']);
        unset($serializeData['image']);
        unset($serializeData['token']);
        unset($serializeData['blog_user_id']);
        unset($serializeData['original_blog_user_id']);
        unset($serializeData['last_edit_blog_user_id']);

        //mode:scheduling
        if (($schedData['releaseSelect'] == 1) && is_array($schedData['date']) && isset($schedData['date'][0]) && !empty($schedData['date'][0]) && isset($schedData['time'][0]) && !empty($schedData['time'][0])) {
            foreach ($schedData['date'] as $key => $date) {
                if (isset($schedData['time'][$key]) && !empty($schedData['time'][$key])) {
                    //content
                    if (isset($schedData['sched_content'][$key]) && !empty($schedData['sched_content'][$key])) {
                        $serializeData['content'] = $schedData['sched_content'][$key];
                    }
                    //Update - calendar edit function
                    if (isset($data['sched_details_id'])) {
                        $wpdb->update($wpdb->prefix . 'b2s_posts_sched_details', array(
                            'sched_data' => serialize($serializeData)
                                ), array("id" => $data['sched_details_id']), array('%s', '%s', '%d'));
                        $schedDetailsId = $data['sched_details_id'];
                        //new entry insert
                    } else {
                        $wpdb->insert($wpdb->prefix . 'b2s_posts_sched_details', array('sched_data' => serialize($serializeData), 'image_url' => $data['image_url']), array('%s', '%s'));
                        $schedDetailsId = $wpdb->insert_id;
                    }

                    $sendTime = strtotime($date . ' ' . $schedData['time'][$key]);
                    $shipdays[] = array('sched_details_id' => $schedDetailsId, 'sched_date' => date('Y-m-d H:i:00', $sendTime), 'sched_date_utc' => date('Y-m-d H:i:00', strtotime(B2S_Util::getUTCForDate($date . ' ' . $schedData['time'][$key], $data['user_timezone'] * (-1)))));
                    if (isset($schedData['saveSetting']) && $schedData['saveSetting'] !== false) {
                        $this->saveUserTimeSettings(date('H:i', $sendTime), $data['network_auth_id']);
                    }
                }
            }

            $this->postData['token'] = $data['token'];
            $this->postData["default_titel"] = $data["default_titel"];
            $this->postData["is_video"] = $data["is_video"];
            $this->postData["video_upload_size"] = $data["video_upload_size"];
            $this->postData["no_cache"] = (int) $data["no_cache"];
            $this->postData["lang"] = $data["lang"];
            $this->postData["blog_user_id"] = $data["blog_user_id"];
            $this->postData["post_id"] = $data["post_id"];
            $this->postData['user_timezone'] = $data['user_timezone'];
            unset($data['token']);
            unset($data['default_titel']);
            unset($data['is_video']);
            unset($data['video_upload_size']);
            unset($data['no_cache']);
            unset($data['lang']);
            unset($data['publish_date']);

            foreach ($shipdays as $k => $date) {
                if (isset($data['b2s_id']) && $data['b2s_id'] > 0) {
                    $wpdb->update($wpdb->prefix . 'b2s_posts', array(
                        'post_id' => $data['post_id'],
                        'last_edit_blog_user_id' => $data['last_edit_blog_user_id'],
                        'user_timezone' => $data['user_timezone'],
                        'publish_date' => "0000-00-00 00:00:00",
                        'sched_details_id' => $date['sched_details_id'],
                        'sched_type' => 1,
                        'sched_date' => $date['sched_date'],
                        'sched_date_utc' => $date['sched_date_utc'],
                        'network_details_id' => $networkDetailsId,
                        'post_for_approve' => $shareApprove,
                        'post_format' => (($data['post_format'] !== '') ? (((int) $data['post_format'] > 0) ? (int) $data['post_format'] : 0) : null)
                            ), array("id" => $data['b2s_id']), array('%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%d'));
                } else {
                    $wpdb->insert($wpdb->prefix . 'b2s_posts', array(
                        'post_id' => $data['post_id'],
                        'blog_user_id' => $data['blog_user_id'],
                        'user_timezone' => $data['user_timezone'],
                        'publish_date' => "0000-00-00 00:00:00",
                        'sched_details_id' => $date['sched_details_id'],
                        'sched_type' => 1,
                        'sched_date' => $date['sched_date'],
                        'sched_date_utc' => $date['sched_date_utc'],
                        'network_details_id' => $networkDetailsId,
                        'post_for_approve' => $shareApprove,
                        'post_format' => ((isset($data['post_format']) && $data['post_format'] != null && $data['post_format'] !== '') ? (((int) $data['post_format'] >= 1) ? (int) $data['post_format'] : 0) : NULL),
                            ), array('%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%d', '%d'));

                    B2S_Rating::trigger();
                    $data['internal_post_id'] = $wpdb->insert_id;
                    $postData = array(
                        'post_id' => $data['post_id'],
                        'blog_user_id' => $data['blog_user_id'],
                        'user_timezone' => $data['user_timezone'],
                        'post_for_approve' => $shareApprove,
                        'network_details_id' => $networkDetailsId,
                        'post_format' => ((isset($data['post_format']) && $data['post_format'] != null && $data['post_format'] !== '') ? (((int) $data['post_format'] >= 1) ? (int) $data['post_format'] : 0) : NULL),
                    );
                    $date = array_merge(array("sched_content" => $schedData['sched_content'][$k]), $date);
                    unset($data['content']);
                    $this->postData['post'][] = array_merge($data, $date);
                }
            }

            unset($data['blog_user_id']);
            unset($data['post_id']);
            unset($data['user_timezone']);

            //mode:direct share
        } else {
            $postData = array(
                'post_id' => $data['post_id'],
                'blog_user_id' => $data['blog_user_id'],
                'user_timezone' => $data['user_timezone'],
                'publish_date' => $data['publish_date'],
                'post_for_approve' => $shareApprove,
                'network_details_id' => $networkDetailsId,
                'post_format' => ((isset($data['post_format']) && $data['post_format'] != null && $data['post_format'] !== '') ? (((int) $data['post_format'] >= 1) ? (int) $data['post_format'] : 0) : NULL),
            );
            $wpdb->insert($wpdb->prefix . 'b2s_posts', $postData, array('%d', '%d', '%d', '%s', '%d', '%d', '%d'));
            B2S_Rating::trigger();

            $data['internal_post_id'] = $wpdb->insert_id;

            if ($shareApprove == 0) {
                $this->postData['token'] = $data['token'];
                $this->postData["blog_user_id"] = $data["blog_user_id"];
                $this->postData["post_id"] = $data["post_id"];
                $this->postData["default_titel"] = $data["default_titel"];
                $this->postData["is_video"] = $data["is_video"];
                $this->postData["video_upload_size"] = $data["video_upload_size"];
                $this->postData["no_cache"] = (int) $data["no_cache"];
                $this->postData["lang"] = $data["lang"];
                $this->postData['user_timezone'] = $data['user_timezone'];

                unset($data['token']);
                unset($data['blog_user_id']);
                unset($data['post_id']);
                unset($data['default_titel']);
                unset($data['is_video']);
                unset($data['video_upload_size']);
                unset($data['no_cache']);
                unset($data['lang']);
                unset($data['user_timezone']);
                unset($data['publish_date']);

                $this->postData['post'][] = $data;
            } else {
                $this->postDataApprove['post'][] = array('internal_post_id' => $data['internal_post_id'],
                    'network_id' => $data['network_id'],
                    'network_auth_id' => $data['network_auth_id'],
                    'network_type' => $data['network_type'],
                    'network_display_name' => $data['network_display_name'],
                    'post_format' => (isset($data['post_format']) ? (int) $data['post_format'] : 0),
                    'image_url' => $data['image_url'],
                    'content' => $data['content'],
                    'url' => $data['url'],
                    'share_as_reel' => $data['share_as_reel']
                );
            }
        }
    }

    public function getShareApproveDetails($quickShare = false) {
        $content = array();
        foreach ($this->postDataApprove['post'] as $k => $v) {
            if (isset($v['internal_post_id']) && $v['internal_post_id'] > 0 && isset($v['network_auth_id']) && (int) $v['network_auth_id'] > 0 && isset($v['network_id']) && (int) $v['network_id'] > 0) {
                if (!$quickShare) {
                    $content[] = array('networkAuthId' => (int) $v['network_auth_id'], 'approve' => 1, 'html' => $this->getApproveItemHtml($v));
                } else {
                    $content[] = array('networkAuthId' => (int) $v['network_auth_id'], 'networkDisplayName' => $v['network_display_name'], 'networkId' => $v['network_id'], 'networkType' => $v['network_type'], 'approve' => 1, 'html' => $this->getApproveItemHtml($v));
                }
            }
        }
        return $content;
    }

    public function postPublish($quickShare = false) {
        global $wpdb;
        $content = array();
        $this->postData['action'] = 'sentToNetwork';
        $postData = $this->postData['post'];
        $this->postData['post'] = serialize($this->postData['post']);
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $this->postData, 90));
        $errorText = unserialize(B2S_PLUGIN_NETWORK_ERROR);
        $insertInsights = true;
        $requestSuccess = false;
        
        foreach ($postData as $k => $v) {
            $found = false;
            $networkId = (isset($v['network_id']) && (int) $v['network_id'] > 0) ? (int) $v['network_id'] : 0;
            if (isset($result->data) && is_array($result->data)) {
                foreach ($result->data as $key => $post) {
                    if (isset($post->internal_post_id) && (int) $post->internal_post_id > 0 && (int) $post->internal_post_id == (int) $v['internal_post_id']) {
                        $data = array('publish_link' => $post->publishUrl,
                            'publish_error_code' => (isset($post->error_code) ? $post->error_code : ''),
                            'upload_video_token' => (isset($post->video_token) ? $post->video_token : ''),
                            'hook_action' => ((isset($post->video_token) && !empty($post->video_token)) ? 6 : 0),
                        );
                        $where = array('id' => $post->internal_post_id);
                        $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%s', '%s', '%s', '%d'), array('%d'));
                        $errorCode = isset($post->error_code) ? $post->error_code : '';

                        //since V4.8.0 relay posts
                        $printDelayDates = array();
                        if (empty($errorCode) && isset($v['relay_data']) && !empty($v['relay_data']) && is_array($v['relay_data']) && isset($v['relay_data']['auth']) && isset($v['relay_data']['delay'])) {
                            $userTimeZone = (isset($this->postData['user_timezone'])) ? $this->postData['user_timezone'] : 0;
                            $sched_date = date('Y-m-d H:i:00', current_time('timestamp'));
                            $sched_date_utc = date('Y-m-d H:i:00', strtotime(B2S_Util::getUTCForDate($sched_date, $userTimeZone * (-1))));
                            $schedData = array('user_timezone' => $userTimeZone, 'sched_date' => $sched_date, 'sched_date_utc' => $sched_date_utc, 'post_id' => $this->postData['post_id'], 'blog_user_id' => $this->postData['blog_user_id']);
                            $printDelayDates = $this->saveRelayDetails((int) $v['internal_post_id'], $v['relay_data'], $schedData);
                            if (!$quickShare) {
                                $videoUploadType = isset($post->video_upload_type) ? (int) $post->video_upload_type : 0;
                                $content[] = array('networkAuthId' => $post->network_auth_id, 'html' => $this->getItemHtml($networkId, $errorCode, $post->publishUrl, $printDelayDates, true, $videoUploadType));
                            } else {
                                $content[] = array('networkAuthId' => $post->network_auth_id, 'networkDisplayName' => $v['network_display_name'], 'networkId' => $v['network_id'], 'networkType' => $v['network_type'], 'html' => $this->getItemHtml($networkId, $errorCode, $post->publishUrl, $printDelayDates, true));
                            }
                            //since V7.1.0 video Posts
                        } else if (empty($errorCode) && isset($v["post_format"]) && $v["post_format"] == 2 && isset($v["sched_date_utc"]) && !empty($v["sched_date_utc"])) {
                            $printDelayDates[] = $v["sched_date"];
                            $videoUploadType = isset($post->video_upload_type) ? (int) $post->video_upload_type : 0;
                            $valueInContent = false;
                            foreach ($content as $key => &$value) {
                                if ($value["networkAuthId"] == $post->network_auth_id) {
                                    if (!isset($value["html"])) {
                                        $value["html"] = $this->getItemHtml($networkId, $errorCode, $post->publishUrl, $printDelayDates, true, $videoUploadType);
                                    } else {
                                        $value["html"] .= $this->getItemHtml($networkId, $errorCode, $post->publishUrl, $printDelayDates, true, $videoUploadType);
                                    }
                                    $valueInContent = true;
                                    continue;
                                }
                            }
                            if (!$valueInContent) {
                                $content[] = array('networkAuthId' => $post->network_auth_id, 'html' => $this->getItemHtml($networkId, $errorCode, $post->publishUrl, $printDelayDates, true, $videoUploadType));
                            }
                        } else {
                            if (!$quickShare) {
                                $videoUploadType = isset($post->video_upload_type) ? (int) $post->video_upload_type : 0;
                                $content[] = array('networkAuthId' => $post->network_auth_id, 'html' => $this->getItemHtml($networkId, $errorCode, $post->publishUrl, $printDelayDates, true, $videoUploadType));
                            } else {
                                $content[] = array('networkAuthId' => $post->network_auth_id, 'networkDisplayName' => $v['network_display_name'], 'networkId' => $v['network_id'], 'networkType' => $v['network_type'], 'html' => $this->getItemHtml($networkId, $errorCode, $post->publishUrl, $printDelayDates, true));
                            }
                        }


                        $found = true;
                        $requestSuccess = true;
                    }
                    if ($insertInsights && isset($post->external_post_id) && !empty($post->external_post_id) && isset($post->insights) && !empty($post->insights)) {
                        $sql = "SELECT id FROM {$wpdb->prefix}b2s_posts_network_details WHERE network_auth_id = %d";
                        $postsNetworkDetailsId = $wpdb->get_results($wpdb->prepare($sql, $post->network_auth_id), ARRAY_A);
                        if (isset($postsNetworkDetailsId[0]['id']) && (int) $postsNetworkDetailsId[0]['id'] > 0) {
                            $insightData = array(
                                'network_post_id' => $post->external_post_id,
                                'insight' => json_encode($post->insights),
                                'blog_user_id' => B2S_PLUGIN_BLOG_USER_ID,
                                'b2s_posts_id' => (int) $post->internal_post_id,
                                'b2s_posts_network_details_id' => (int) $postsNetworkDetailsId[0]['id'],
                                'last_update' => date('Y-m-d H:i:s'),
                                'active' => 1
                            );
                            $wpdb->insert($wpdb->prefix . 'b2s_posts_insights', $insightData, array('%s', '%s', '%d', '%d', '%d', '%s', '%d'));
                        }
                    }
                }
                $insertInsights = false;
            }
            //DEFAULT ERROR
            if ($found == false) {
                $errorCode = (isset($result->data) && isset($errorText[$result->data])) ? sanitize_text_field(wp_unslash($result->data)) : 'DEFAULT';
                if (!$quickShare) {
                    $content[] = array('networkAuthId' => $v['network_auth_id'], 'html' => $this->getItemHtml($networkId, $errorCode, '', '', true));
                } else {
                    $content[] = array('networkAuthId' => $v['network_auth_id'], 'networkDisplayName' => $v['network_display_name'], 'networkId' => $v['network_id'], 'networkType' => $v['network_type'], 'html' => $this->getItemHtml($networkId, $errorCode, '', '', true));
                }
            }
        }

        if (!isset($post->error_code) || $post->error_code == "") {
            if (isset($this->postData['post_id']) && (int) $this->postData['post_id'] > 0 && isset($v['network_auth_id']) && (int) $v['network_auth_id'] > 0) {
                $prepare = $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}b2s_posts LEFT JOIN {$wpdb->prefix}b2s_posts_network_details ON {$wpdb->prefix}b2s_posts.network_details_id={$wpdb->prefix}b2s_posts_network_details.id SET {$wpdb->prefix}b2s_posts.hide = 1 WHERE {$wpdb->prefix}b2s_posts.post_id = %d AND {$wpdb->prefix}b2s_posts_network_details.network_auth_id = %d AND {$wpdb->prefix}b2s_posts.publish_error_code != ''",
                        array($this->postData['post_id'], $v['network_auth_id'])
                );
                $wpdb->query($prepare);
            }
        }

        if ($requestSuccess && isset($this->postData['is_video']) && $this->postData['is_video'] == 1 && isset($this->postData['video_upload_size']) && $this->postData['video_upload_size'] > 0) {
            $versionDetails = get_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
            $versionDetails['B2S_PLUGIN_ADDON_VIDEO']['volume_open'] -= (int) ($this->postData['video_upload_size'] / 1024);
            if ($versionDetails['B2S_PLUGIN_ADDON_VIDEO']['volume_open'] < 0) {
                $versionDetails['B2S_PLUGIN_ADDON_VIDEO']['volume_open'] = 0;
            }
            update_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID, $versionDetails, false);
        }
        return $content;
    }

    //save & print
    public function saveRelayDetails($relay_primary_post_id = 0, $relayData = array(), $schedData = array()) {
        global $wpdb;
        $printSchedDate = array();
        if ($relay_primary_post_id > 0) {
            foreach ($relayData['auth'] as $key => $auth) {
                if (isset($relayData['delay'][$key]) && !empty($relayData['delay'][$key])) {
                    $networkDetailsId = $this->lookupNetworkDetailsId($auth);
                    if ($networkDetailsId > 0) {
                        $sched_date = date('Y-m-d H:i:s', strtotime("+" . $relayData['delay'][$key] . " minutes", strtotime($schedData['sched_date'])));
                        $sched_date_utc = date('Y-m-d H:i:s', strtotime("+" . $relayData['delay'][$key] . " minutes", strtotime($schedData['sched_date_utc'])));

                        $wpdb->insert($wpdb->prefix . 'b2s_posts', array(
                            'post_id' => $schedData['post_id'],
                            'blog_user_id' => $schedData['blog_user_id'],
                            'user_timezone' => $schedData['user_timezone'],
                            'sched_type' => 4, // replay, retweet
                            'sched_date' => $sched_date,
                            'sched_date_utc' => $sched_date_utc,
                            'network_details_id' => $networkDetailsId,
                            'relay_primary_post_id' => $relay_primary_post_id,
                            'relay_delay_min' => (int) $relayData['delay'][$key],
                            'hook_action' => 1), array('%d', '%d', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d'));

                        $printSchedDate[] = array('date' => $sched_date, 'relay' => true);
                    }
                }
            }
        }
        return $printSchedDate;
    }

    public function saveSchedDetails($data, $schedData, $relayData = array()) {
        global $wpdb;

        $shipdays = array();
        $serializeData = $data;
        $networkDetailsId = $this->getNetworkDetailsId($data['network_id'], $data['network_type'], $data['network_auth_id'], $data['network_display_name']);

        //Since V4.9.1 - Instant Share Facebook Profile
        if (isset($serializeData['post_for_approve'])) {   //set by edit mode and $shareApproveNetworkData is empty
            $shareApprove = (int) $serializeData['post_for_approve'];
            unset($serializeData['post_for_approve']);
        } else {
            $shareApprove = (isset($data['instant_sharing']) && (int) $data['instant_sharing'] == 1) ? 1 : 0;
        }

        unset($serializeData['network_type']);
        unset($serializeData['network_display_name']);
        unset($serializeData['post_id']);
        unset($serializeData['image']);
        //insert mode
        unset($serializeData['token']);
        unset($serializeData['blog_user_id']);
        //update mode
        unset($serializeData['original_blog_user_id']);
        unset($serializeData['last_edit_blog_user_id']);

        $printSchedDate = array();
        //mode: once schedule
        if (($schedData['releaseSelect'] == 1 || $schedData['releaseSelect'] == 5) && is_array($schedData['date']) && isset($schedData['date'][0]) && !empty($schedData['date'][0]) && isset($schedData['time'][0]) && !empty($schedData['time'][0])) {
            foreach ($schedData['date'] as $key => $date) {
                if (isset($schedData['time'][$key]) && !empty($schedData['time'][$key])) {
                    //custom sched content
                    //image
                    if (isset($schedData['sched_image_url'][$key]) && !empty($schedData['sched_image_url'][$key])) {
                        $serializeData['image_url'] = $schedData['sched_image_url'][$key];
                        $data['image_url'] = $schedData['sched_image_url'][$key];
                    }


                    //Multi Image
                    $multi_images = array();
                    if (isset($schedData['sched_multi_image_1'][$key]) && !empty($schedData['sched_multi_image_1'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_1'][$key]);
                    }
                    if (isset($schedData['sched_multi_image_2'][$key]) && !empty($schedData['sched_multi_image_2'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_2'][$key]);
                    }
                    if (isset($schedData['sched_multi_image_3'][$key]) && !empty($schedData['sched_multi_image_3'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_3'][$key]);
                    }
                    if (isset($schedData['sched_multi_image_4'][$key]) && !empty($schedData['sched_multi_image_4'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_4'][$key]);
                    }
                    if (isset($schedData['sched_multi_image_5'][$key]) && !empty($schedData['sched_multi_image_5'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_5'][$key]);
                    }
                    if (isset($schedData['sched_multi_image_6'][$key]) && !empty($schedData['sched_multi_image_6'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_6'][$key]);
                    }
                    if (isset($schedData['sched_multi_image_7'][$key]) && !empty($schedData['sched_multi_image_7'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_7'][$key]);
                    }
                    if (isset($schedData['sched_multi_image_8'][$key]) && !empty($schedData['sched_multi_image_8'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_8'][$key]);
                    }
                    if (isset($schedData['sched_multi_image_9'][$key]) && !empty($schedData['sched_multi_image_9'][$key])) {
                        array_push($multi_images, $schedData['sched_multi_image_9'][$key]);
                    }
                    if (!empty($multi_images)) {
                        $serializeData['multi_images'] = json_encode($multi_images);
                        $data['multi_images'] = json_encode($multi_images);
                    }




                    //content
                    if (isset($schedData['sched_content'][$key]) && !empty($schedData['sched_content'][$key])) {
                        $serializeData['content'] = $schedData['sched_content'][$key];
                    }
                    //Update - calendar edit function
                    if (isset($data['sched_details_id'])) {
                        $wpdb->update($wpdb->prefix . 'b2s_posts_sched_details', array(
                            'sched_data' => serialize($serializeData),
                            'image_url' => $data['image_url']
                                ), array("id" => $data['sched_details_id']), array('%s', '%s', '%d'));
                        $schedDetailsId = $data['sched_details_id'];
                        //new entry insert
                    } else {
                        $wpdb->insert($wpdb->prefix . 'b2s_posts_sched_details', array('sched_data' => serialize($serializeData), 'image_url' => $data['image_url']), array('%s', '%s'));
                        $schedDetailsId = $wpdb->insert_id;
                    }

                    $sendTime = strtotime($date . ' ' . $schedData['time'][$key]);
                    $shipdays[] = array('sched_details_id' => $schedDetailsId, 'sched_date' => date('Y-m-d H:i:00', $sendTime), 'sched_date_utc' => date('Y-m-d H:i:00', strtotime(B2S_Util::getUTCForDate($date . ' ' . $schedData['time'][$key], $schedData['user_timezone'] * (-1)))));
                    $printSchedDate[] = array('date' => date('Y-m-d H:i:s', $sendTime));
                    if ($schedData['saveSetting']) {
                        $this->saveUserTimeSettings(date('H:i', $sendTime), $data['network_auth_id']);
                    }
                }
            }
        } else {
            //mode: recurrently schedule
            if (isset($schedData['interval_select']) && is_array($schedData['interval_select']) && isset($schedData['interval_select'][0])) {
                $dayOfWeeks = array(1 => 'mo', 2 => 'di', 3 => 'mi', 4 => 'do', 5 => 'fr', 6 => 'sa', 7 => 'so');

                //new entry insert
                $wpdb->insert($wpdb->prefix . 'b2s_posts_sched_details', array('sched_data' => serialize($serializeData), 'image_url' => $data['image_url']), array('%s', '%s'));
                $schedDetailsId = $wpdb->insert_id;

                foreach ($schedData['interval_select'] as $cycle => $mode) {
                    //interval:weekly
                    if ((int) $mode == 0) {
                        foreach ($dayOfWeeks as $dayNumber => $dayName) {
                            if (isset($schedData[$dayName][$cycle]) && $schedData[$dayName][$cycle] == 1) {
                                for ($weeks = 1; $weeks <= $schedData['weeks'][$cycle]; $weeks++) {
                                    $startTime = (isset($schedData['date'][$cycle]) && isset($schedData['time'][$cycle])) ? $schedData['date'][$cycle] : $data['publish_date'];
                                    $startDay = date('N', strtotime($startTime));
                                    $maxDaysSched = $schedData['weeks'][$cycle] * 7 + $startDay;
                                    if ($dayNumber < $startDay) {
                                        if ($schedData['weeks'][$cycle] == 1) {
                                            $sendDay = 7 - $startDay + $dayNumber;
                                        } else {
                                            $sendDay = 7 - $startDay + $dayNumber + (7 * ($weeks - 1));
                                        }
                                    } else if ($dayNumber == $startDay) {
                                        $sendDay = (7 * ($weeks - 1));
                                    } else {
                                        $sendDay = $dayNumber - $startDay + (7 * ($weeks - 1));
                                    }
                                    if ($schedData['weeks'][$cycle] == 1 || $sendDay <= $maxDaysSched) {
                                        $schedTime = date('Y-m-d', strtotime("+$sendDay days", strtotime($startTime)));
                                        $tempSchedDateTime = date('Y-m-d H:i:00', strtotime($schedTime . ' ' . $schedData['time'][$cycle]));
                                        $sched_date_utc = date('Y-m-d H:i:00', strtotime(B2S_Util::getUTCForDate($tempSchedDateTime, $schedData['user_timezone'] * (-1))));
                                        if ($tempSchedDateTime >= $data['publish_date']) {
                                            $shipdays[] = array('sched_date' => $tempSchedDateTime, 'sched_date_utc' => $sched_date_utc, 'sched_details_id' => $schedDetailsId);
                                            $printSchedDate[] = array('date' => $tempSchedDateTime);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //interval:monthly
                    if ((int) $mode == 1) {
                        if (isset($schedData['duration_month'][$cycle]) && isset($schedData['select_day'][$cycle]) && isset($schedData['date'][$cycle]) && isset($schedData['time'][$cycle])) {
                            $result = $this->createMonthlyIntervalDates($schedData['duration_month'][$cycle], $schedData['select_day'][$cycle], $schedData['date'][$cycle], $schedData['time'][$cycle]);
                            if (is_array($result) && !empty($result)) {
                                foreach ($result as $key => $date) { //Y-m-d none utc
                                    $sched_date_time = date('Y-m-d H:i:00', strtotime($date . ' ' . $schedData['time'][$cycle]));
                                    $sched_date_time_utc = date('Y-m-d H:i:00', strtotime(B2S_Util::getUTCForDate($sched_date_time, $schedData['user_timezone'] * (-1))));
                                    $shipdays[] = array('sched_date' => $sched_date_time, 'sched_date_utc' => $sched_date_time_utc, 'sched_details_id' => $schedDetailsId);
                                    $printSchedDate[] = array('date' => $sched_date_time);
                                }
                            }
                        }
                    }
                    //interval: own period
                    if ((int) $mode == 2) {
                        if (isset($schedData['duration_time'][$cycle]) && isset($schedData['select_timespan'][$cycle]) && isset($schedData['date'][$cycle]) && isset($schedData['time'][$cycle])) {
                            $result = $this->createCustomIntervalDates($schedData['duration_time'][$cycle], $schedData['select_timespan'][$cycle], $schedData['date'][$cycle]);
                            if (is_array($result) && !empty($result)) {
                                foreach ($result as $key => $date) { //Y-m-d none utc
                                    $sched_date_time = date('Y-m-d H:i:00', strtotime($date . ' ' . $schedData['time'][$cycle]));
                                    $sched_date_time_utc = date('Y-m-d H:i:00', strtotime(B2S_Util::getUTCForDate($sched_date_time, $schedData['user_timezone'] * (-1))));
                                    $shipdays[] = array('sched_date' => $sched_date_time, 'sched_date_utc' => $sched_date_time_utc, 'sched_details_id' => $schedDetailsId);
                                    $printSchedDate[] = array('date' => $sched_date_time);
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($shipdays as $k => $schedDate) {
            if (isset($data['b2s_id']) && $data['b2s_id'] > 0) {
                $wpdb->update($wpdb->prefix . 'b2s_posts', array(
                    'post_id' => $data['post_id'],
                    'last_edit_blog_user_id' => $data['last_edit_blog_user_id'],
                    'user_timezone' => $schedData['user_timezone'],
                    'publish_date' => "0000-00-00 00:00:00",
                    'sched_details_id' => $schedDate['sched_details_id'],
                    'sched_type' => $schedData['releaseSelect'],
                    'sched_date' => $schedDate['sched_date'],
                    'sched_date_utc' => $schedDate['sched_date_utc'],
                    'network_details_id' => $networkDetailsId,
                    'post_for_approve' => $shareApprove,
                    'hook_action' => (($shareApprove == 0) ? 5 : 0),
                    'post_format' => (($data['post_format'] !== '') ? (((int) $data['post_format'] > 0) ? (int) $data['post_format'] : 0) : null)
                        ), array("id" => $data['b2s_id']), array('%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%d'));
            } else {
                $wpdb->insert($wpdb->prefix . 'b2s_posts', array(
                    'post_id' => $data['post_id'],
                    'blog_user_id' => $data['blog_user_id'],
                    'user_timezone' => $schedData['user_timezone'],
                    'publish_date' => "0000-00-00 00:00:00",
                    'sched_details_id' => $schedDate['sched_details_id'],
                    'sched_type' => $schedData['releaseSelect'],
                    'sched_date' => $schedDate['sched_date'],
                    'sched_date_utc' => $schedDate['sched_date_utc'],
                    'network_details_id' => $networkDetailsId,
                    'post_for_relay' => ((!empty($relayData) && is_array($relayData)) ? 1 : 0),
                    'post_for_approve' => $shareApprove,
                    'hook_action' => (($shareApprove == 0) ? 1 : 0),
                    'post_format' => (($data['post_format'] !== '') ? (((int) $data['post_format'] > 0) ? 1 : 0) : null)
                        ), array('%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%d', '%d'));

                //since V4.8.0 relay posts
                if (!empty($relayData) && is_array($relayData)) {
                    $internal_post_id = $wpdb->insert_id;
                    $relaySchedData = array('user_timezone' => $schedData['user_timezone'], 'sched_date' => $schedDate['sched_date'], 'sched_date_utc' => $schedDate['sched_date_utc'], 'post_id' => $data['post_id'], 'blog_user_id' => ( isset($data['original_blog_user_id']) ? $data['original_blog_user_id'] : $data['blog_user_id'] ));  //update - insert
                    $relayResult = $this->saveRelayDetails((int) $internal_post_id, $relayData, $relaySchedData);
                    $printSchedDate = array_merge($printSchedDate, $relayResult);
                }

                B2S_Rating::trigger();
            }
        }

        return array('networkAuthId' => $data['network_auth_id'], 'html' => $this->getItemHtml($serializeData['network_id'], '', '', $printSchedDate));
    }

    public function getApproveItemHtml($data = array(), $info = true) {
        $html = "";
        $data['token'] = B2S_PLUGIN_TOKEN;
        $data['language'] = substr(B2S_LANGUAGE, 0, 2);
        if ($info) {
            if ($data['network_id'] == 1) {
                $html .= '<br><div class="alert alert-warning"><b>' . esc_html__('For sharing your posts on personal Facebook Profiles you can use Facebook Instant Sharing', 'blog2social') . '</b> (<a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('facebook_instant_sharing')) . '">' . esc_html__('Learn how it works', 'blog2social') . '</a>).';
                $html .= '<br><br>';
                $html .= '<b>' . esc_html__('This is how it works:', 'blog2social') . '</b><br>';
                $html .= esc_html__('-To share your post immediately, click the "Share" button next to your selected Facebook profile below.', 'blog2social') . '<br>';
                $html .= esc_html__('-For scheduled posts, Blog2Social will save your post and move it to the "Scheduled Posts" tab on your "Posts & Sharing" navigation bar. On your scheduled date and time, your post will move to the "Instant Sharing" tab and you can click on "Share" to post it to your Facebook Profile instantly.', 'blog2social');
                $html .= '</div>';
            }
            if ($data['network_id'] == 10) {
                $html .= '<br><div class="alert alert-warning"><b>' . esc_html__('For sharing your posts on Google+ you can now use Google+ Instant Sharing', 'blog2social') . '</b></a>).';
                $html .= '<br><br>';
                $html .= '<b>' . esc_html__('This is how it works:', 'blog2social') . '</b><br>';
                $html .= esc_html__('-To share your post immediately, click the "Share" button next to your selected Google+ account below.', 'blog2social') . '<br>';
                $html .= esc_html__('-For scheduled posts, Blog2Social will save your post and move it to the "Scheduled Posts" tab on your "Site & Blog Content" navigation bar. On your scheduled date and time, your post will move to the "Instant Sharing" tab and you can click on "Share" to post it to your account instantly.', 'blog2social') . '<br>';
                $html .= '<b>' . esc_html__('Please note: You post has to be marked as public to be posted in a group.', 'blog2social') . '</b>';
                $html .= '</div>';
            }
        }
        $approveLink = '<a href="#" class="btn btn-primary" onclick="wopApprove(\'' . esc_attr($data['network_auth_id']) . '\',\'' . esc_attr((($data['network_id'] == 10) ? $data['internal_post_id'] : 0)) . '\',\'' . B2S_PLUGIN_API_ENDPOINT . 'instant/share.php?data=' . B2S_Util::urlsafe_base64_encode(json_encode($data)) . '\', \'Blog2Social\'); return false;" target="_blank"><i class="glyphicon glyphicon-share"></i> ' . esc_html__('share', 'blog2social') . '</a>';
        $html .= '<span class="text-warning">' . $approveLink . ' (' . esc_html__('Please share your post now', 'blog2social') . ')</span><br>';
        return $html;
    }

    public function getItemHtml($network_id = 0, $error = "", $link = "", $schedDate = array(), $directPost = false, $videoUploadType = 0) {
        $html = "";
        if (empty($error)) {
            if ($directPost) {
                if ($videoUploadType > 0) {
                    if ($network_id == 36) { // mobile approvement
                        $html .= '<br><div class="alert alert-warning"><b>' . esc_html__('Your video will now be uploaded. After TikTok has processed your video, you can unlock it in your TikTok app.', 'blog2social') . '</b> (<a href="' . esc_url(B2S_Tools::getSupportLink('video_sharing_tiktok')) . '" target="_blank">' . esc_html__('Learn how it works', 'blog2social') . '</a>)</div>';
                    } else if (is_array($schedDate) && empty($schedDate)) {
                        $html .= '<br><div class="alert alert-info"><b>' . esc_html__('Your video is uploading.', 'blog2social') . '</b></div>';
                    }
                } else {
                    /* if ($network_id == 1 && empty($link)) { // NOTE fb reel ?
                      $html .= '<br><span class="text-success"><i class="glyphicon glyphicon-ok-circle"></i> ' . esc_html__('Your video will now be uploaded. Your video will be published after Facebook processing', 'blog2social');
                      } else { *///}
                    if(!isset($schedDate) || empty($schedDate)){
                        $html .= '<br><span class="text-success"><i class="glyphicon glyphicon-ok-circle"></i> ' . esc_html__('published', 'blog2social');
                        $html .= !empty($link) ? ': <a href="' . esc_url($link) . '" target="_blank">' . esc_html__('view social media post', 'blog2social') . '</a>' : '';    
                    }

                    $html .= '</span>';
                }
            }
            if (is_array($schedDate) && !empty($schedDate)) {
                $dateFormat = get_option('date_format');
                $timeFormat = get_option('time_format');
                sort($schedDate);
                foreach ($schedDate as $k => $v) {
                    if (is_array($v)) {
                        $date = $v['date'];
                    } else {
                        $date = $v;
                    }
                    $schedDateTime = date_i18n($dateFormat . ' ' . $timeFormat, strtotime($date));
                    $isRelay = (isset($v['relay'])) ? " - " . esc_html__('Retweet', 'blog2social') : '';
                    if ($videoUploadType > 0) {
                        
                    } else {
                        
                    }
                    $html .= '<br><span class="text-success"><i class="glyphicon glyphicon-time"></i> ' . esc_html__('scheduled on', 'blog2social') . ': ' . esc_html($schedDateTime) . $isRelay . '</span>';
                }
            }
        } else {
            $errorText = unserialize(B2S_PLUGIN_NETWORK_ERROR);
            $error = isset($errorText[$error]) ? $error : 'DEFAULT';
            $add = '';
            //special case: reddit RATE_LIMIT
            if ($network_id == 15 && $error == 'RATE_LIMIT') {
                $link = (strtolower(substr(B2S_LANGUAGE, 0, 2)) == 'de') ? 'https://www.blog2social.com/de/faq/content/9/115/de/reddit-du-hast-das-veroeffentlichungs_limit-mit-deinem-account-kurzzeitig-erreicht.html' : 'https://www.blog2social.com/en/faq/content/9/115/en/reddit-you-have-temporarily-reached-the-publication-limit-with-your-account.html';
                $add = ' ' . esc_html__('Please see', 'blog2social') . ' <a target="_blank" href="' . esc_url($link) . '">' . esc_html__('FAQ', 'blog2social') . '</a>';
            }

            if ($network_id == 12 && $error == 'DEFAULT') {
                $networkError12 = sprintf(__('Your post could not be posted. More information in this <a href="%s" target="_blank">Instagram troubleshoot checklist</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_error_business')));
                $html .= '<br><span class="text-danger"><i class="glyphicon glyphicon-remove-circle glyphicon-danger"></i> ' . $networkError12 . $add . '</span>';
            } else {
                $html .= '<br><span class="text-danger"><i class="glyphicon glyphicon-remove-circle glyphicon-danger"></i> ' . $errorText[$error] . $add . '</span>';
            }
        }
        return $html;
    }

    private function saveUserTimeSettings($schedTime = '', $networkAuthId = 0) {
        if ((int) $networkAuthId > 0) {
            $options = new B2S_Options(get_current_user_id());
            $userSchedData = $options->_getOption('auth_sched_time');
            if (isset($userSchedData['time']) && $userSchedData !== false) {
                //update
                if (is_array($userSchedData) && isset($userSchedData['delay_day']) && isset($userSchedData['time']) && is_array($userSchedData['time'])) {
                    $found = false;
                    foreach ($userSchedData['time'] as $k => $v) {
                        if ($k == $networkAuthId) {
                            $userSchedData['time'][$k] = $schedTime;
                            $found = true;
                        }
                    }
                    if (!$found) {
                        //add
                        $userSchedData['time'][$networkAuthId] = $schedTime;
                        $userSchedData['delay_day'][$networkAuthId] = 0;
                    }
                    $options->_setOption('auth_sched_time', array('delay_day' => $userSchedData['delay_day'], 'time' => $userSchedData['time']));
                }
            } else {
                //insert
                $options->_setOption('auth_sched_time', array('delay_day' => array($networkAuthId => 0), 'time' => array($networkAuthId => $schedTime)));
            }
        }
    }

    //monthly
    public function createMonthlyIntervalDates($duration_month = 0, $select_day = 0, $date = "", $time = "") {
        $dates = array();
        $startDateTime = strtotime($date . ' ' . $time);
        $allowEndofMonth = ((int) $select_day == 0) ? true : false;
        $select_day = $allowEndofMonth ? 31 : sprintf("%02d", $select_day);
        $selectDateTime = strtotime(date('Y-m', $startDateTime) . '-' . $select_day . ' ' . $time);

        $addMonth = ($selectDateTime < $startDateTime) ? 1 : 0;

        for ($i = 1; $i <= $duration_month; $i++) {
            $cDate = date('Y-m', strtotime(date('Y-m', $startDateTime) . " +" . $addMonth . " month"));
            if (checkdate((int) date('m', strtotime($cDate)), (int) $select_day, (int) date('Y', strtotime($cDate)))) {
                $dates[] = $cDate . "-" . $select_day;
            } else {
                //set last day of month
                if ($allowEndofMonth) {
                    $dates[] = date("Y-m-t", strtotime($cDate . "-01"));
                }
            }
            $addMonth++;
        }
        return $dates;
    }

    //own period
    public function createCustomIntervalDates($duration_time = 0, $select_timespan = 0, $date = "") {
        $dates = array();
        $dates[] = date('Y-m-d', strtotime($date));  //add start date
        $cTimespan = $select_timespan;
        for ($i = 1; $i < $duration_time; $i++) {
            $dates[] = date('Y-m-d', strtotime($date . " +" . $cTimespan . " day"));
            $cTimespan += $select_timespan;
        }
        return $dates;
    }

}
