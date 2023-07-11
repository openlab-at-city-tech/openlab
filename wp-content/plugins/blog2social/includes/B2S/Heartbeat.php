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
            if (isset($data['b2s_heartbeat_action']) && ($data['b2s_heartbeat_action'] == 'b2s_auto_posting' || $data['b2s_heartbeat_action'] == 'b2s_repost')) {
                $this->postSchedToServer();
            } else if (isset($data['b2s_heartbeat_action']) && $data['b2s_heartbeat_action'] == 'b2s_delete_sched_post') {
                $this->deleteUserSchedPost();
            } else if (isset($data['b2s_heartbeat_action']) && $data['b2s_heartbeat_action'] == 'b2s_metrics') {
                $this->updateInsights();
            } else if (isset($data['b2s_heartbeat_action']) && $data['b2s_heartbeat_action'] == 'b2s_video_upload') {
                $this->uploadVideo();
                $this->getVideoResultfromServer();
            } else {
                $this->postSchedToServer();
                $this->deleteUserSchedPost();
                $this->updateUserSchedTimePost();
                $this->updateUserSchedPost();
                $this->deleteUserPublishPost();
                $this->getSchedResultFromServer();
                $this->uploadVideo();
                $this->getVideoResultFromServer();
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

    public function updateSchedTimePost() {
        $this->updateUserSchedTimePost();
    }

    private function postSchedToServer() {
        global $wpdb;
        $sendData = array();
        $sql = "SELECT post.id,post.post_id,post.blog_user_id,post.user_timezone,post.sched_type,post.sched_date,post.sched_date_utc,post.relay_primary_post_id,post.post_for_relay,schedDetails.sched_data, schedDetails.image_url,network.network_id, network.network_type,network.network_auth_id,user.token "
                . "FROM {$wpdb->prefix}b2s_posts AS post "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_network_details AS network on post.network_details_id = network.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details AS schedDetails on post.sched_details_id = schedDetails.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_user AS user on post.blog_user_id = user.blog_user_id "
                . "WHERE sched_date !='0000-00-00 00:00:00' AND sched_date_utc !='0000-00-00 00:00:00' AND post.hook_action= %d AND post.hide=%d AND post.post_for_approve= %d LIMIT 100";
        $postData = $wpdb->get_results($wpdb->prepare($sql, 1, 0, 0), ARRAY_A);

        foreach ($postData as $k => $value) {
            $data = array('hook_action' => '0');
            $where = array('id' => $value['id']);
            $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
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
                        $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
                    }
                    if (isset($result->fail) && isset($result->fail->{$value['id']})) {
                        $failData = $wpdb->get_results($wpdb->prepare("SELECT id,sched_details_id,network_details_id FROM {$wpdb->prefix}b2s_posts WHERE id= %d", $value['id']), ARRAY_A);
                        if (isset($failData[0]) && (int) $failData[0]['id'] > 0) {
                            $wpdb->delete($wpdb->prefix . 'b2s_posts', array('id' => $failData[0]['id']), array('%d'));
                            if (isset($failData[0]['sched_details_id']) && (int) $failData[0]['sched_details_id'] > 0) {
                                $wpdb->delete($wpdb->prefix . 'b2s_posts_sched_details', array('id' => $failData[0]['sched_details_id']), array('%d'));
                            }
                            if (isset($failData[0]['sched_details_id']) && (int) $failData[0]['network_details_id'] > 0) {
                                $wpdb->delete($wpdb->prefix . 'b2s_posts_network_details', array('id' => $failData[0]['network_details_id']), array('%d'));
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
                . "LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE posts.sched_date_utc != %s AND posts.sched_date_utc <= %s AND posts.hide=%d AND posts.post_for_approve = %d LIMIT 500"; //AND posts.publish_date = %s
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
                                            $wpdb->delete($wpdb->prefix . 'b2s_posts', array('id' => $vdv2->id), array('%d'));
                                        }
                                        if ((int) $vdv2->network_details_id > 0) {
                                            $wpdb->delete($wpdb->prefix . 'b2s_posts_network_details', array('id' => $vdv2->network_details_id), array('%d'));
                                        }
                                        if ((int) $vdv2->sched_details_id > 0) {
                                            $wpdb->delete($wpdb->prefix . 'b2s_posts_sched_details', array('id' => $vdv2->sched_details_id), array('%d'));
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
                                            $wpdb->insert($wpdb->prefix . 'b2s_posts_network_details', $networkDetails, array('%d', '%d', '%d', '%s'));
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
                                            $wpdb->insert($wpdb->prefix . 'b2s_posts', $b2sPost, array('%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d'));

                                            if (isset($vpv2['external_post_id']) && !empty($vpv2['external_post_id'])) {
                                                $insightData = array(
                                                    'network_post_id' => $vpv2['external_post_id'],
                                                    'insight' => '',
                                                    'blog_user_id' => $blog_user_id,
                                                    'b2s_posts_id' => (int) $post_id,
                                                    'b2s_posts_network_details_id' => $networkDetailsId,
                                                    'last_update' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -1 day')),
                                                    'active' => 1
                                                );
                                                $wpdb->insert($wpdb->prefix . 'b2s_posts_insights', $insightData, array('%s', '%s', '%d', '%d', '%d', '%s', '%d'));
                                            }


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
                                $wpdb->update($wpdb->prefix . 'b2s_posts', $updateData, array('id' => $v->id, 'v2_id' => $v->v2_id), array('%s', '%s', '%s', '%s', '%s', '%d'), array('%d', '%d'));
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
                            $wpdb->update($wpdb->prefix . 'b2s_posts', $updateData, array('id' => $v->id), array('%s', '%s', '%s', '%s', '%s', '%d', '%d'), array('%d'));

                            if (isset($v->external_post_id) && !empty($v->external_post_id)) {
                                $netowkDetailsData = $wpdb->get_results($wpdb->prepare("SELECT network_details_id, blog_user_id FROM {$wpdb->prefix}b2s_posts WHERE id= %d", $v->id), ARRAY_A);
                                if (isset($netowkDetailsData[0]) && isset($netowkDetailsData[0]['network_details_id']) && (int) $netowkDetailsData[0]['network_details_id'] > 0 && isset($netowkDetailsData[0]['blog_user_id']) && (int) $netowkDetailsData[0]['blog_user_id'] > 0) {
                                    $insightData = array(
                                        'network_post_id' => $v->external_post_id,
                                        'insight' => '',
                                        'blog_user_id' => (int) $netowkDetailsData[0]['blog_user_id'],
                                        'b2s_posts_id' => (int) $v->id,
                                        'b2s_posts_network_details_id' => (int) $netowkDetailsData[0]['network_details_id'],
                                        'last_update' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -1 day')),
                                        'active' => 1
                                    );
                                    $wpdb->insert($wpdb->prefix . 'b2s_posts_insights', $insightData, array('%s', '%s', '%d', '%d', '%d', '%s', '%d'));
                                }
                            }
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
                . "LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE hook_action = %d LIMIT 100";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 2), ARRAY_A);

        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
            }
            $tempData = array('action' => 'updateUserSchedTimePost', 'data' => serialize($sendData));
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            foreach ($sendData as $k => $value) {
                //is failed, try again later
                $id = $value['id'];
                if (!isset($result->content) || !isset($result->content->{$id})) {
                    $data = array('hook_action' => '2');
                    $where = array('id' => $id);
                    $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
                }
            }
        }
    }

    private function updateUserSchedPost() {
        global $wpdb;
        $sql = "SELECT posts.id, posts.sched_date, posts.sched_date_utc,schedDetails.sched_data, schedDetails.image_url,user.token FROM {$wpdb->prefix}b2s_posts as posts "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details AS schedDetails on posts.sched_details_id = schedDetails.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE hook_action = %d AND post_for_approve = %d LIMIT 100";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 5, 0), ARRAY_A);

        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
            }
            $tempData = array('action' => 'updateUserSchedPost', 'data' => serialize($sendData));
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            foreach ($sendData as $k => $value) {
                //is failed, try again later
                $id = $value['id'];
                if (!isset($result->content) || !isset($result->content->{$id})) {
                    $data = array('hook_action' => '5');
                    $where = array('id' => $id);
                    $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
                }
            }
        }
    }

    private function deleteUserSchedPost() {
        global $wpdb;
        $sql = "SELECT posts.id, posts.v2_id, user.token, posts.post_format, posts.upload_video_token FROM {$wpdb->prefix}b2s_posts as posts LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE hook_action = %d AND  post_for_approve = %d LIMIT 500";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 3, 0), ARRAY_A);
        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
            }
            $tempData = array('action' => 'deleteUserSchedPost', 'data' => serialize($sendData));
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            foreach ($sendData as $k => $value) {
                //is failed, try again later
                $id = $value['id'];
                if (!isset($result->content) || !isset($result->content->{$id})) {
                    $data = array('hook_action' => '3');
                    $where = array('id' => $id);
                    $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
                }
            }
        }
    }

    private function deleteUserPublishPost() {
        global $wpdb;
        $sql = "SELECT posts.id, user.token, posts.sched_details_id, posts.hide FROM {$wpdb->prefix}b2s_posts as posts LEFT JOIN {$wpdb->prefix}b2s_user AS user on posts.blog_user_id = user.blog_user_id WHERE hook_action = %d AND post_for_approve = %d LIMIT 500";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 4, 0), ARRAY_A);
        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));
            }
            $tempData = array('action' => 'deleteUserPublishPost', 'data' => serialize($sendData));
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 90));
            foreach ($sendData as $k => $value) {
                //is failed, try again later
                $id = $value['id'];
                if (!isset($result->content) || !isset($result->content->{$id})) {
                    $data = array('hook_action' => '4');
                    $where = array('id' => $id);
                    $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%d'));

                //if not failed and delete flag hide = 2 is set, delete completely
                } else if(isset($value['hide']) && (int) $value['hide'] == 2){
                    if(isset($value['id']) && (int) $value['id'] > 0){
                        $wpdb->delete($wpdb->prefix . 'b2s_posts', array('id' => $value['id']), array('%d'));
                        if( isset($value["sched_details_id"]) && (int) $value["sched_details_id"] > 0){
                            $wpdb->delete($wpdb->prefix . 'b2s_posts_sched_details', array('id' => $value['sched_details_id']), array('%d'));
                        }
                    }
                }
            }
        }
    }

    private function updateInsights() {
        global $wpdb;
        $sql = "SELECT user.token, insights.network_post_id, insights.insight, network_details.network_auth_id, network_details.network_id, network_details.network_type FROM {$wpdb->prefix}b2s_posts_insights as insights LEFT JOIN {$wpdb->prefix}b2s_user AS user on insights.blog_user_id = user.blog_user_id LEFT JOIN {$wpdb->prefix}b2s_posts_network_details AS network_details on insights.b2s_posts_network_details_id = network_details.id WHERE insights.active = %d AND insights.last_update < %s LIMIT 50";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 1, date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -1 day'))), ARRAY_A);
        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            $tempData = array('action' => 'updateInsights', 'data' => $sendData);
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $tempData, 45), true);
            if (isset($result['data']) && !empty($result['data'])) {
                foreach ($result['data'] as $k => $value) {
                    $insights = $value;
                    if ($insights !== false && is_array($insights) && !empty($insights) && isset($insights['extern_post_id']) && !empty($insights['extern_post_id'])) {
                        if (isset($insights['insights']) && !empty($insights['insights'])) {
                            $sql = "SELECT insights.insight, insights.b2s_posts_network_details_id, networkDetails.network_auth_id, networkDetails.id as b2sNetworkDetailsId FROM {$wpdb->prefix}b2s_posts_insights as insights LEFT JOIN {$wpdb->prefix}b2s_posts_network_details as networkDetails ON insights.b2s_posts_network_details_id = networkDetails.id WHERE insights.network_post_id = %s";
                            $externPostData = $wpdb->get_results($wpdb->prepare($sql, $insights['extern_post_id']), ARRAY_A);
                            if (strlen($externPostData[0]['insight']) > $insights) {
                                continue; // no new insights data
                            }
                            $wpdb->update($wpdb->prefix . 'b2s_posts_insights', array('insight' => json_encode($insights), 'last_update' => date('Y-m-d H:i:s')), array('network_post_id' => $insights['extern_post_id']), array('%s', '%s'), array('%s'));

                            if (is_array($externPostData) && !empty($externPostData) && isset($externPostData[0])) {
                                //update & compare
                                $currentInsights = json_decode($externPostData[0]['insight'], true);
                                if (isset($currentInsights['insights']['data']['likes']) && !empty($currentInsights['insights']['data']['likes'])) {
                                    if (isset($insights['insights']['data']['likes']) && !empty($insights['insights']['data']['likes'])) {
                                        foreach ($insights['insights']['data']['likes'] as $newDate => $newCount) {
                                            $dataForDateExist = false;
                                            $dataForDateIsSame = false;
                                            $newTotalData = array(
                                                'likes' => $newCount,
                                                'comments' => ((isset($insights['insights']['data']['comments'][$newDate])) ? (int) $insights['insights']['data']['comments'][$newDate] : 0),
                                                'reshares' => ((isset($insights['insights']['data']['reshares'][$newDate])) ? (int) $insights['insights']['data']['reshares'][$newDate] : 0),
                                                'impressions' => ((isset($insights['insights']['data']['impressions'][$newDate])) ? (int) $insights['insights']['data']['impressions'][$newDate] : 0)
                                            );
                                            foreach ($currentInsights['insights']['data']['likes'] as $currDate => $currCount) {
                                                if (substr($newDate, 0, 10) == substr($currDate, 0, 10)) {
                                                    $dataForDateExist = true;
                                                    if ($newCount == $currCount) {
                                                        $dataForDateIsSame = true;
                                                    }
                                                }
                                            }

                                            if (!$dataForDateExist || ($dataForDateExist && !$dataForDateIsSame)) {
                                                $sql = "SELECT insight FROM {$wpdb->prefix}b2s_posts_insights WHERE b2s_posts_network_details_id = %d";
                                                $postInsightsData = $wpdb->get_results($wpdb->prepare($sql, (int) $externPostData[0]['b2sNetworkDetailsId']), ARRAY_A);

                                                $totalData = array('likes' => 0, 'comments' => 0, 'reshares' => 0, 'impressions' => 0);
                                                if (is_array($postInsightsData) && !empty($postInsightsData)) {
                                                    foreach ($postInsightsData as $entry) {
                                                        if (isset($entry['insight']) && !empty($entry['insight'])) {
                                                            $entryData = json_decode($entry['insight'], true);
                                                            if ($entryData !== false && is_array($entryData) && !empty($entryData) && isset($entryData['insights']['data']['likes']) && is_array($entryData['insights']['data']['likes']) && !empty($entryData['insights']['data']['likes'])) {
                                                                end($entryData['insights']['data']['likes']);
                                                                $entryKey = key($entryData['insights']['data']['likes']);
                                                                $totalData['likes'] += $entryData['insights']['data']['likes'][$entryKey];
                                                                $totalData['comments'] += $entryData['insights']['data']['comments'][$entryKey];
                                                                $totalData['reshares'] += $entryData['insights']['data']['reshares'][$entryKey];
                                                                $totalData['impressions'] += $entryData['insights']['data']['impressions'][$entryKey];
                                                            }
                                                        }
                                                    }
                                                }
                                                $sql = "SELECT id, insight FROM {$wpdb->prefix}b2s_network_insights WHERE b2s_posts_network_details_id = %d AND create_date = %s";
                                                $networkInsightsData = $wpdb->get_results($wpdb->prepare($sql, (int) $externPostData[0]['b2sNetworkDetailsId'], substr($newDate, 0, 10)), ARRAY_A);

                                                if (is_array($networkInsightsData) && !empty($networkInsightsData) && isset($networkInsightsData[0])) {
                                                    //update
                                                    $wpdb->update($wpdb->prefix . 'b2s_network_insights', array('insight' => json_encode($totalData)), array('id' => $networkInsightsData[0]['id']), array('%s'), array('%d'));
                                                } else {
                                                    //insert
                                                    $wpdb->insert($wpdb->prefix . 'b2s_network_insights', array(
                                                        'b2s_posts_network_details_id' => $externPostData[0]['b2sNetworkDetailsId'],
                                                        'create_date' => substr($newDate, 0, 10),
                                                        'insight' => json_encode($totalData)
                                                            ), array('%d', '%s', '%s'));
                                                }
                                            } else {
                                                $sql = "SELECT id, insight FROM {$wpdb->prefix}b2s_network_insights WHERE b2s_posts_network_details_id = %d AND create_date = %s";
                                                $networkInsightsData = $wpdb->get_results($wpdb->prepare($sql, (int) $externPostData[0]['b2sNetworkDetailsId'], substr($newDate, 0, 10)), ARRAY_A);
                                                if (is_array($networkInsightsData) && !empty($networkInsightsData) && isset($networkInsightsData[0])) {
                                                    //update
                                                    $wpdb->update($wpdb->prefix . 'b2s_network_insights', array('insight' => json_encode($newTotalData)), array('id' => $networkInsightsData[0]['id']), array('%s'), array('%d'));
                                                } else {
                                                    //insert
                                                    $wpdb->insert($wpdb->prefix . 'b2s_network_insights', array(
                                                        'b2s_posts_network_details_id' => $externPostData[0]['b2sNetworkDetailsId'],
                                                        'create_date' => substr($newDate, 0, 10),
                                                        'insight' => json_encode($newTotalData)
                                                            ), array('%d', '%s', '%s'));
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                //new entry
                                if (isset($insights['data']['insights']['likes']) && !empty($insights['data']['insights']['likes'])) {
                                    foreach ($insights['data']['insights']['likes'] as $newDate => $newCount) {
                                        $sql = "SELECT insight FROM {$wpdb->prefix}b2s_posts_insights WHERE b2s_posts_network_details_id = %d";
                                        $postInsightsData = $wpdb->get_results($wpdb->prepare($sql, (int) $externPostData['b2s_posts_network_details_id']), ARRAY_A);

                                        $totalData = array('likes' => 0, 'comments' => 0, 'reshares' => 0, 'impressions' => 0);
                                        if (is_array($postInsightsData) && !empty($postInsightsData)) {
                                            foreach ($postInsightsData as $entry) {
                                                if (isset($entry['insight']) && !empty($entry['insight'])) {
                                                    $entryData = json_decode($entry['insight'], true);
                                                    if ($entryData !== false && is_array($entryData) && !empty($entryData)) {
                                                        end($entryData['insights']['data']['likes']);
                                                        $entryKey = key($entryData['insights']['data']['likes']);
                                                        $totalData['likes'] += $entryData['insights']['data']['likes'][$entryKey];
                                                        $totalData['comments'] += $entryData['insights']['data']['comments'][$entryKey];
                                                        $totalData['reshares'] += $entryData['insights']['data']['reshares'][$entryKey];
                                                        $totalData['impressions'] += $entryData['insights']['data']['impressions'][$entryKey];
                                                    }
                                                }
                                            }
                                        }

                                        $sql = "SELECT id, insight FROM {$wpdb->prefix}b2s_network_insights WHERE b2s_posts_network_details_id = %d AND create_date = %s";
                                        $networkInsightsData = $wpdb->get_results($wpdb->prepare($sql, (int) $externPostData['b2s_posts_network_details_id'], substr($newDate, 0, 10)), ARRAY_A);

                                        if (is_array($networkInsightsData) && !empty($networkInsightsData) && isset($networkInsightsData[0])) {
                                            //update
                                            $wpdb->update($wpdb->prefix . 'b2s_network_insights', array('insight' => json_encode($totalData)), array('id' => $networkInsightsData[0]['id']), array('%s'), array('%d'));
                                        } else {
                                            //insert
                                            $wpdb->insert($wpdb->prefix . 'b2s_network_insights', array(
                                                'b2s_posts_network_details_id' => $externPostData['b2s_posts_network_details_id'],
                                                'create_date' => substr($newDate, 0, 10),
                                                'insight' => json_encode($totalData)
                                                    ), array('%d', '%s', '%s'));
                                        }
                                    }
                                }
                            }
                            //is deactivated   
                        } else {
                            $wpdb->update($wpdb->prefix . 'b2s_posts_insights', array('last_update' => date('Y-m-d H:i:s'), 'active' => 0), array('network_post_id' => $insights['extern_post_id']), array('%s', '%d'), array('%s'));
                        }
                    }
                }
            }
        }
    }

    private function uploadVideo() {
        global $wpdb;
        $sql = "SELECT post_id,upload_video_token FROM {$wpdb->prefix}b2s_posts WHERE hook_action = %d ORDER BY id ASC LIMIT 1";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 6), ARRAY_A);
        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            if (isset($sendData[0]['post_id']) && (int) $sendData[0]['post_id'] > 0 && isset($sendData[0]['upload_video_token']) && $sendData[0]['upload_video_token'] != '') {
                require_once (B2S_PLUGIN_DIR . '/includes/B2S/Video/Upload.php');
                $upload = new B2S_Video_Upload();
                $result = $upload->uploadVideo($sendData[0]['post_id'], $sendData[0]['upload_video_token']);
                if (is_array($result) && !empty($result) && isset($result['upload'])) {
                    if ($result['upload'] !== false) {
                        $data = array('hook_action' => 7);
                        $where = array('publish_error_code' => '', 'upload_video_token' => $sendData[0]['upload_video_token']);
                        $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d'), array('%s', '%s'));
                    } else {
                        $data = array('hook_action' => 0, 'publish_error_code' => ((isset($result['error_code']) && !empty($result['error_code'])) ? $result['error_code'] : 'VIDEO_UPLOAD'));
                        $where = array('publish_error_code' => '', 'upload_video_token' => $sendData[0]['upload_video_token']);
                        $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d', '%s'), array('%s', '%s'));
                    }
                }
            }
        }
    }

    private function getVideoResultfromServer() {
        global $wpdb;
        $sql = "SELECT DISTINCT upload_video_token FROM {$wpdb->prefix}b2s_posts WHERE hook_action = %d ORDER BY id ASC LIMIT 15";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 7), ARRAY_A);
        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            $videoToken = array();
            foreach ($sendData as $k => $value) {
                array_push($videoToken, trim($value['upload_video_token']));
            }
            if (!empty($videoToken)) {
                $args = array(
                    'method' => 'POST',
                    'body' => array(
                        'video_token' => $videoToken,
                    ),
                    'timeout' => 20,
                    'redirection' => '5',
                    'user-agent' => "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)",
                );
                $result = wp_remote_retrieve_body(wp_remote_post(B2S_PLUGIN_API_VIDEO_UPLOAD_ENDPOINT . 'video/check', $args));
                if (!empty($result)) {
                    $result = json_decode($result, true);
                    if (is_array($result) && !empty($result)) {
                        foreach ($result as $token => $resl) {
                            if (!empty($token) && is_array($resl) && !empty($resl)) {
                                foreach ($resl as $k => $res) {
                                    if (isset($res['state']) && (int) $res['state'] == 0) {
                                        if (isset($res['publish_url']) && isset($res['post_id']) && (int) $res['post_id'] > 0) {
                                            //Update
                                            $data = array('hook_action' => 0, 'publish_link' => $res['publish_url'], 'publish_error_code' => '');
                                            $where = array('id' => (int) $res['post_id'], 'upload_video_token' => $token);
                                            $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d', '%s', '%s'), array('%d', '%s'));
                                        }
                                    } else if (isset($res['state']) && (int) $res['state'] == 1) {
                                        if (isset($res['b2s_error_code']) && isset($res['post_id']) && (int) $res['post_id'] > 0) {
                                            //Update
                                            $data = array('hook_action' => 0, 'publish_link' => '', 'publish_error_code' => $res['b2s_error_code']);
                                            $where = array('id' => (int) $res['post_id'], 'upload_video_token' => $token);
                                            $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d', '%s', '%s'), array('%d', '%s'));
                                        }
                                    } else if (!isset($res['state']) && !isset($res['post_id']) && isset($res['b2s_error_code']) && !empty($res['b2s_error_code'])) {
                                        //Update
                                        $data = array('hook_action' => 0, 'publish_link' => '', 'publish_error_code' => $res['b2s_error_code']);
                                        $where = array('publish_error_code' => '', 'publish_link' => '', 'upload_video_token' => $token);
                                        $wpdb->update($wpdb->prefix . 'b2s_posts', $data, $where, array('%d', '%s', '%s'), array('%s', '%s', '%s'));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function updateVideoStatus() {
        global $wpdb;
        $sql = "SELECT videos.id, videos.hook_action, videos.video_token, sched_details.sched_data FROM {$wpdb->prefix}b2s_posts_videos as videos LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details AS sched_details on videos.sched_details_id = sched_details.id WHERE hook_action > %d LIMIT 5";
        $sendData = $wpdb->get_results($wpdb->prepare($sql, 0), ARRAY_A);
        if (is_array($sendData) && !empty($sendData) && isset($sendData[0])) {
            require_once (B2S_PLUGIN_DIR . '/includes/B2S/Video/Post.php');
            $videoPost = new B2S_Video_Post();
            foreach ($sendData as $k => $value) {
                $data = array('hook_action' => '0');
                $where = array('id' => $value['id']);
                $wpdb->update($wpdb->prefix . 'b2s_posts_videos', $data, $where, array('%d'), array('%d'));
                if ($value['hook_action'] == 1) {
                    //check upload complete
                    $status = $videoPost->getVideoStatus($value['video_token']);
                    if ($status !== false && (int) $status == 2) {
                        //send data to publish
                        $sched_data = unserialize($value['sched_data']);
                        $publish = $videoPost->publishVideo($value['video_token'], $sched_data['auth_id'], $sched_data['title'], $sched_data['content']);

                        if ($publish['success'] == true && !empty($publish['link'])) {
                            $data = array('hook_action' => '0', 'publish_link' => $publish['link'], 'publish_date' => date('Y-m-d H:i:s'));
                            $where = array('id' => $value['id']);
                            $wpdb->update($wpdb->prefix . 'b2s_posts_videos', $data, $where, array('%d'), array('%d'));
                        }

                        $data = array('hook_action' => '2');
                        $where = array('id' => $value['id']);
                        $wpdb->update($wpdb->prefix . 'b2s_posts_videos', $data, $where, array('%d'), array('%d'));
                    } else {
                        $data = array('hook_action' => '1');
                        $where = array('id' => $value['id']);
                        $wpdb->update($wpdb->prefix . 'b2s_posts_videos', $data, $where, array('%d'), array('%d'));
                    }
                }
                if ($value['hook_action'] == 2) {
                    //get publish status

                    $data = array('hook_action' => '0');
                    $where = array('id' => $value['id']);
                    $wpdb->update($wpdb->prefix . 'b2s_posts_videos', $data, $where, array('%d'), array('%d'));
                }
            }
        }
    }

}
