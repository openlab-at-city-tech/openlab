<?php

class B2S_Heartbeat {

    static private $instance = null;

    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function init($response, $data) {

        if (isset($data['b2s_heartbeat']) && $data['b2s_heartbeat'] == 'b2s_listener') {
            if (isset($data['b2s_heartbeat_action']) && $data['b2s_heartbeat_action'] == 'b2s_auto_posting') {
                $this->postSchedToServer();
            } if (isset($data['b2s_heartbeat_action']) && $data['b2s_heartbeat_action'] == 'b2s_delete_sched_post') {
                $this->deleteUserSchedPost();
            } else {
                $this->postSchedToServer();
                $this->deleteUserSchedPost();
                $this->updateUserSchedTimePost();
                $this->updateUserSchedPost();
                $this->deleteUserPublishPost();
                $this->getSchedResultFromServer();
            }
            $response['b2s-trigger'] = true;
        }
        return $response;
    }

    public function postToServer() {
        $this->postSchedToServer();
    }

    public function deleteSchedPost() {
        $this->deleteUserSchedPost();
    }
    
    public function updateSchedTimePost(){
        $this->updateUserSchedTimePost();
    } 
    
    private function postSchedToServer() {
        global $wpdb;
        $sendData = array();
        $sql = "SELECT post.id,post.post_id,post.blog_user_id,post.user_timezone,post.sched_date,post.sched_date_utc,post.relay_primary_post_id,post.post_for_relay,schedDetails.sched_data, schedDetails.image_url,network.network_id, network.network_type,network.network_auth_id,user.token "
                . "FROM {$wpdb->prefix}b2s_posts AS post "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_network_details AS network on post.network_details_id = network.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details AS schedDetails on post.sched_details_id = schedDetails.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_user AS user on post.blog_user_id = user.blog_user_id "
                . "WHERE sched_date !='0000-00-00 00:00:00' AND sched_date_utc !='0000-00-00 00:00:00' AND post.hook_action= %d AND post.hide=%d AND post.post_for_approve= %d ";
        $postData = $wpdb->get_results($wpdb->prepare($sql, 1, 0, 0), ARRAY_A);

        foreach ($postData as $k => $value) {
            $data = array('hook_action' => '0');
            $where = array('id' => $value['id']);
            $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
            $value['sched_data'] = ((isset($value['sched_data']) && !empty($value['sched_data'])) ? unserialize($value['sched_data']) : '');
            $value['image_url'] = ((isset($value['image_url']) && !empty($value['image_url'])) ? $value['image_url'] : '');
            $sendData[] = $value;
        }
        if (!empty($sendData) && is_array($sendData)) {
            $shipData = serialize($sendData);
            if (!empty($shipData)) {
                $data = array('action' => 'postSchedData', 'data' => $shipData);
                $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $data, 90));
                foreach ($postData as $k => $value) {
                    if (!isset($result->content) || !isset($result->content->{$value['id']})) {
                        $data = array('hook_action' => '1');
                        $where = array('id' => $value['id']);
                        $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
                    }
                    if (isset($result->fail) && isset($result->fail->{$value['id']})) {
                        $failData = $wpdb->get_results($wpdb->prepare("SELECT id,sched_details_id,network_details_id FROM {$wpdb->prefix}b2s_posts WHERE id= %d", $value['id']), ARRAY_A);
                        if (isset($failData[0]) && (int) $failData[0]['id'] > 0) {
                            $wpdb->delete($wpdb->prefix.'b2s_posts', array('id' => $failData[0]['id']), array('%d'));
                            if (isset($failData[0]['sched_details_id']) && (int) $failData[0]['sched_details_id'] > 0) {
                                $wpdb->delete($wpdb->prefix.'b2s_posts_sched_details', array('id' => $failData[0]['sched_details_id']), array('%d'));
                            }
                            if (isset($failData[0]['sched_details_id']) && (int) $failData[0]['network_details_id'] > 0) {
                                $wpdb->delete($wpdb->prefix.'b2s_posts_network_details', array('id' => $failData[0]['network_details_id']), array('%d'));
                            }
                        }
                    }
                }
            }
        }
    }

    private function getSchedResultFromServer() {

        $networkTypeAllow = array('profil', 'page', 'group');
        $networkTypeData = array('profil' => 0, 'page' => 1, 'group' => 2);
        global $wpdb;
        $sql = "SELECT posts.id, posts.user_timezone, posts.sched_date, posts.sched_date_utc, posts.v2_id, user.token FROM {$wpdb->prefix}b2s_posts as posts "
                . "LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE posts.sched_date_utc != %s AND posts.sched_date_utc <= %s AND posts.hide=%d AND posts.post_for_approve = %d"; //AND posts.publish_date = %s
        $select = $wpdb->prepare($sql, '0000-00-00 00:00:00', gmdate('Y-m-d H:i:s'), 0, 0); //,'0000-00-00 00:00:00'
        $sendData = $wpdb->get_results($select, ARRAY_A);

        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            $tempData = array('action' => 'getSchedData', 'data' => serialize($sendData));
            $schedResult = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            if ($schedResult->result == true) {
                foreach ($schedResult->content as $k => $v) {
                    //V2
                    if (isset($v->v2_id) && (int) $v->v2_id > 0) {
                        $publishTime = strtotime($v->publish_date);
                        if ($publishTime != false && isset($v->publishData)) {
                            $publishData = unserialize(stripslashes($v->publishData));
                            if (is_array($publishData) && !empty($publishData)) {
                                //DELETE
                                $post_id = 0;
                                $blog_user_id = 0;
                                $sql = "SELECT id,post_id,blog_user_id,network_details_id,sched_details_id FROM b2s_posts WHERE v2_id = %d";
                                $select = $wpdb->prepare($sql, $v->v2_id);
                                $deleteData = $wpdb->get_results($select);
                                if (is_array($deleteData) && !empty($deleteData)) {
                                    foreach ($deleteData as $kdv2 => $vdv2) {
                                        $post_id = $vdv2->post_id;
                                        $blog_user_id = $vdv2->blog_user_id;
                                        if ((int) $vdv2->id > 0) {
                                            $wpdb->delete($wpdb->prefix.'b2s_posts', array('id' => $vdv2->id), array('%d'));
                                        }
                                        if ((int) $vdv2->network_details_id > 0) {
                                            $wpdb->delete($wpdb->prefix.'b2s_posts_network_details', array('id' => $vdv2->network_details_id), array('%d'));
                                        }
                                        if ((int) $vdv2->sched_details_id > 0) {
                                            $wpdb->delete($wpdb->prefix.'b2s_posts_sched_details', array('id' => $vdv2->sched_details_id), array('%d'));
                                        }
                                    }

                                    foreach ($publishData as $kpv2 => $vpv2) {
                                        $networkDetailsId = 0;
                                        $schedDetailsId = 0;
                                        if (isset($vpv2['portal_id']) && !empty($vpv2['portal_id']) && isset($vpv2['type']) && in_array($vpv2['type'], $networkTypeAllow) && (int) $post_id > 0 && (int) $blog_user_id > 0) {
                                            //INSERT
                                            $networkDetails = array(
                                                'network_id' => $vpv2['portal_id'],
                                                'network_type' => $networkTypeData[$vpv2['type']],
                                                'network_auth_id' => 0,
                                                'network_display_name' => ''
                                            );
                                            $wpdb->insert($wpdb->prefix.'b2s_posts_network_details', $networkDetails, array('%d', '%d', '%d', '%s'));
                                            $networkDetailsId = $wpdb->insert_id;
                                            $timezone = get_option('gmt_offset');
                                            $b2sPost = array(
                                                'post_id' => $post_id,
                                                'blog_user_id' => $blog_user_id,
                                                'user_timezone' => $timezone,
                                                'sched_details_id' => $schedDetailsId,
                                                'sched_type' => '0',
                                                'sched_date' => '0000-00-00 00:00:00',
                                                'sched_date_utc' => '0000-00-00 00:00:00',
                                                'publish_date' => date('Y-m-d H:i:s', $publishTime),
                                                'publish_link' => isset($vpv2['publishUrl']) ? stripslashes($vpv2['publishUrl']) : '',
                                                'publish_error_code' => (!isset($vpv2['error']) || (int) $vpv2['error'] == 0) ? '' : 'DEFAULT',
                                                'network_details_id' => $networkDetailsId,
                                                'hook_action' => '0',
                                                'hide' => '0',
                                                'v2_id' => '0');
                                            $wpdb->insert($wpdb->prefix.'b2s_posts', $b2sPost, array('%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d'));
                                            B2S_Rating::trigger();
                                        }
                                    }
                                }
                            }
                        } else {
                            //Update (ERROR)
                            if (isset($v->publish_error_code) && isset($v->publish_link) && $publishTime != false && (int) $v->id > 0) {
                                $updateData = array(
                                    'sched_date' => '0000-00-00 00:00:00',
                                    'sched_date_utc' => '0000-00-00 00:00:00',
                                    'publish_date' => date('Y-m-d H:i:s', $publishTime),
                                    'publish_link' => sanitize_text_field($v->publish_link),
                                    'publish_error_code' => sanitize_text_field($v->publish_error_code),
                                    'hook_action' => 0);
                                $wpdb->update($wpdb->prefix.'b2s_posts', $updateData, array('id' => $v->id, 'v2_id' => $v->v2_id), array('%s', '%s', '%s', '%s', '%s', '%d'), array('%d', '%d'));
                            }
                        }
                    } else {
                        //V3   
                        $publishTime = strtotime($v->publish_date);
                        if ((int) $v->id > 0 && $publishTime != false) {
                            //since V4.9.1 - check by error is old scheduled instant sharing post
                            $shareApprove = (strtoupper($v->publish_error_code) == 'APPROVE') ? 1 : 0;

                            //Old since V.5.1.1
                            /* if (!empty($v->publish_error_code)) {
                              $sql = "SELECT details.network_id, details.network_type FROM b2s_posts as posts "
                              . "LEFT JOIN b2s_posts_network_details AS details on posts.network_details_id = details.id WHERE posts.id = %d";
                              $getNetworkDetails = $wpdb->get_results($wpdb->prepare($sql, $v->id), ARRAY_A);
                              if (is_array($getNetworkDetails) && !empty($getNetworkDetails) && isset($getNetworkDetails[0]['network_id']) && isset($getNetworkDetails[0]['network_type'])) {
                              $shareApproveNetworkData = unserialize(B2S_PLUGIN_NETWORK_SHARE_APPROVE);
                              if (isset($shareApproveNetworkData[(int) $getNetworkDetails[0]['network_type']]) && in_array((int) $getNetworkDetails[0]['network_id'], $shareApproveNetworkData[(int) $getNetworkDetails[0]['network_type']])) {
                              $shareApprove = 1;
                              }
                              }
                              } */

                            $updateData = array(
                                'sched_date' => '0000-00-00 00:00:00',
                                'sched_date_utc' => '0000-00-00 00:00:00',
                                'publish_date' => date('Y-m-d H:i:s', $publishTime),
                                'publish_link' => (($shareApprove == 0) ? sanitize_text_field($v->publish_link) : ''),
                                'publish_error_code' => (($shareApprove == 0) ? sanitize_text_field($v->publish_error_code) : ''),
                                'post_for_approve' => (int) $shareApprove,
                                'hook_action' => 0);
                            $wpdb->update($wpdb->prefix.'b2s_posts', $updateData, array('id' => $v->id), array('%s', '%s', '%s', '%s', '%s', '%d', '%d'), array('%d'));
                        }
                    }
                }
            }
        }
    }

    //since V.4.8.0 for relay posts 
    private function updateUserSchedTimePost() {
        global $wpdb;
        $sql = "SELECT posts.id, posts.sched_date, posts.sched_date_utc, user.token FROM {$wpdb->prefix}b2s_posts as posts "
                . "LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE hook_action = %d";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 2), ARRAY_A);

        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
            }
            $tempData = array('action' => 'updateUserSchedTimePost', 'data' => serialize($sendData));
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            foreach ($sendData as $k => $value) {
                //is failed, try again later
                $id = $value['id'];
                if (!isset($result->content) || !isset($result->content->{$id})) {
                    $data = array('hook_action' => '2');
                    $where = array('id' => $id);
                    $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
                }
            }
        }
    }

    private function updateUserSchedPost() {
        global $wpdb;
        $sql = "SELECT posts.id, posts.sched_date, posts.sched_date_utc,schedDetails.sched_data, schedDetails.image_url,user.token FROM {$wpdb->prefix}b2s_posts as posts "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details AS schedDetails on posts.sched_details_id = schedDetails.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE hook_action = %d AND post_for_approve = %d";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 5, 0), ARRAY_A);

        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
            }
            $tempData = array('action' => 'updateUserSchedPost', 'data' => serialize($sendData));
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            foreach ($sendData as $k => $value) {
                //is failed, try again later
                $id = $value['id'];
                if (!isset($result->content) || !isset($result->content->{$id})) {
                    $data = array('hook_action' => '5');
                    $where = array('id' => $id);
                    $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
                }
            }
        }
    }

    private function deleteUserSchedPost() {
        global $wpdb;
        $sql = "SELECT posts.id, posts.v2_id, user.token FROM {$wpdb->prefix}b2s_posts as posts LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE hook_action = %d AND  post_for_approve = %d";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 3, 0), ARRAY_A);
        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
            }
            $tempData = array('action' => 'deleteUserSchedPost', 'data' => serialize($sendData));
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            foreach ($sendData as $k => $value) {
                //is failed, try again later
                $id = $value['id'];
                if (!isset($result->content) || !isset($result->content->{$id})) {
                    $data = array('hook_action' => '3');
                    $where = array('id' => $id);
                    $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
                }
            }
        }
    }

    private function deleteUserPublishPost() {
        global $wpdb;
        $sql = "SELECT posts.id, user.token FROM {$wpdb->prefix}b2s_posts as posts LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE hook_action = %d AND post_for_approve = %d";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 4, 0), ARRAY_A);
        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
            }
            $tempData = array('action' => 'deleteUserPublishPost', 'data' => serialize($sendData));
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            foreach ($sendData as $k => $value) {
                //is failed, try again later
                $id = $value['id'];
                if (!isset($result->content) || !isset($result->content->{$id})) {
                    $data = array('hook_action' => '4');
                    $where = array('id' => $id);
                    $wpdb->update($wpdb->prefix.'b2s_posts', $data, $where, array('%d'), array('%d'));
                }
            }
        }
    }

}
