<?php

class B2S_Post_Tools {

    public static function updateUserSchedTimePost($post_id, $date, $time, $timezone) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}b2s_posts WHERE id =%d AND blog_user_id = %d AND publish_date = %s", (int) $post_id, (int) get_current_user_id(), "0000-00-00 00:00:00");
        $id = $wpdb->get_col($sql);
        if (isset($id[0]) && $id[0] == $post_id) {
            $insert_time = strtotime($date . ' ' . $time);
            if ($insert_time < time()) {
                $insert_time = time();
            }
            $insert_datetime_utc = B2S_Util::getUTCForDate(date('Y-m-d H:i:s', $insert_time), $timezone * (-1));
            $wpdb->update($wpdb->prefix.'b2s_posts', array('hook_action' => 2, 'sched_date' => date('Y-m-d H:i:s', $insert_time), 'sched_date_utc' => $insert_datetime_utc), array('id' => $post_id));
            return array('result' => true, 'postId' => $post_id, 'time' => B2S_Util::getCustomDateFormat(date('Y-m-d H:i:s', $insert_time), substr(B2S_LANGUAGE, 0, 2)));
        }
        return array('result' => false);
    }

    public static function deleteUserSchedPost($postIds = array()) {
        global $wpdb;
        $resultPostIds = array();
        $blogPostId = 0;
        $tosCrossPosting = unserialize(B2S_PLUGIN_NETWORK_CROSSPOSTING_LIMIT);

        foreach ($postIds as $v) {
            $sql = $wpdb->prepare("SELECT b.id,b.post_id,b.post_for_relay,b.post_for_approve,b.sched_details_id,d.network_id,d.network_type FROM {$wpdb->prefix}b2s_posts b LEFT JOIN {$wpdb->prefix}b2s_posts_network_details d ON (d.id = b.network_details_id) WHERE b.id =%d AND b.publish_date = %s", (int) $v, "0000-00-00 00:00:00");
            $row = $wpdb->get_row($sql);
            if (isset($row->id) && (int) $row->id == $v) {
                if ((int) $row->post_for_approve == 1) {
                    $wpdb->update($wpdb->prefix.'b2s_posts', array('hide' => 1), array('id' => $v));
                } else {
                    //TOS Crossposting delete entry
                    if ($row->network_id != null && $row->network_type != null && (int) $row->sched_details_id > 0) {
                        if (isset($tosCrossPosting[$row->network_id][$row->network_type])) {
                            //get network_tos_group_id form sched_data
                            $sql = $wpdb->prepare("SELECT sched_data FROM {$wpdb->prefix}b2s_posts_sched_details WHERE id =%d", (int) $row->sched_details_id);
                            $schedData = $wpdb->get_col($sql);
                            if (isset($schedData[0]) && !empty($schedData[0])) {
                                $schedData = unserialize($schedData[0]);
                                if ($schedData !== false && isset($schedData['network_tos_group_id']) && !empty($schedData['network_tos_group_id'])) {
                                    $options = new B2S_Options(0, 'B2S_PLUGIN_TOS_XING_GROUP_CROSSPOSTING');
                                    $options->deleteValueByKey($row->post_id, $schedData['network_tos_group_id']);
                                }
                            }
                        }
                    }
                    $wpdb->update($wpdb->prefix.'b2s_posts', array('hook_action' => 3, 'hide' => 1), array('id' => $v));
                }
                $resultPostIds[] = $v;
                $blogPostId = $row->post_id;

                //is post for relay
                if ((int) $row->post_for_relay == 1) {
                    $res = self::getAllRelayByPrimaryPostId($row->id);
                    if (is_array($res) && !empty($res)) {
                        foreach ($res as $item) {
                            if (isset($item->id) && (int) $item->id > 0) {
                                $wpdb->update($wpdb->prefix.'b2s_posts', array('hook_action' => 3, 'hide' => 1), array('id' => $item->id));
                                $resultPostIds[] = $item->id;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($resultPostIds) && is_array($resultPostIds)) {
            B2S_Heartbeat::getInstance()->deleteSchedPost();
            $resultPostIds = array_unique($resultPostIds);
            return array('result' => true, 'postId' => $resultPostIds, 'postCount' => count($resultPostIds), 'blogPostId' => $blogPostId);
        }

        return array('result' => false);
    }

    public static function getAllRelayByPrimaryPostId($primary_post_id = 0) {
        global $wpdb;
        $sqlData = $wpdb->prepare("SELECT `id`,`relay_delay_min` FROM `{$wpdb->prefix}b2s_posts` WHERE `hide` = 0 AND `sched_type` = 4  AND `{$wpdb->prefix}b2s_posts`.`publish_date` = '0000-00-00 00:00:00' AND `relay_primary_post_id` = %d ", $primary_post_id);
        return $wpdb->get_results($sqlData);
    }

    public static function deleteUserPublishPost($postIds = array()) {
        global $wpdb;
        $resultPostIds = array();
        $blogPostId = 0;
        $count = 0;
        foreach ($postIds as $v) {
            $sql = $wpdb->prepare("SELECT id,v2_id,post_id FROM {$wpdb->prefix}b2s_posts WHERE id =%d", (int) $v);
            $row = $wpdb->get_row($sql);
            if (isset($row->id) && (int) $row->id == $v) {
                $hook_action = (isset($row->v2_id) && (int) $row->v2_id > 0) ? 0 : 4; //oldItems
                $wpdb->update($wpdb->prefix.'b2s_posts', array('hook_action' => $hook_action, 'hide' => 1), array('id' => $v));
                $resultPostIds[] = $v;
                $blogPostId = $row->post_id;
                $count++;
            }
        }
        if (!empty($resultPostIds) && is_array($resultPostIds)) {
            return array('result' => true, 'postId' => $resultPostIds, 'postCount' => $count, 'blogPostId' => $blogPostId);
        }
        return array('result' => false);
    }

    public static function deleteUserApprovePost($postIds = array()) {
        global $wpdb;
        $resultPostIds = array();
        $blogPostId = 0;
        $count = 0;
        foreach ($postIds as $v) {
            $sql = $wpdb->prepare("SELECT id,v2_id,post_id FROM {$wpdb->prefix}b2s_posts WHERE id =%d", (int) $v);
            $row = $wpdb->get_row($sql);
            if (isset($row->id) && (int) $row->id == $v) {
                $hook_action = (isset($row->v2_id) && (int) $row->v2_id > 0) ? 0 : 4; //oldItems
                $wpdb->update($wpdb->prefix.'b2s_posts', array('hide' => 1), array('id' => $v));
                $resultPostIds[] = $v;
                $blogPostId = $row->post_id;
                $count++;
            }
        }
        if (!empty($resultPostIds) && is_array($resultPostIds)) {
            return array('result' => true, 'postId' => $resultPostIds, 'postCount' => $count, 'blogPostId' => $blogPostId);
        }
        return array('result' => false);
    }
    
    public static function countNewNotifications($userId = 0) {
        if($userId > 0) {
            require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
            $options = new B2S_Options($userId);
            $lastDate = $options->_getOption('lastNotificationUpdate');
            if($lastDate == false || empty($lastDate)) {
                $lastDate = '2021-01-04 12:00:00';
            }
            $optionUserTimeZone = $options->_getOption('user_time_zone');
            $userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
            $userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
            $lastDate = date('Y-m-d H:i:s', strtotime(B2S_Util::getUTCForDate($lastDate, $userTimeZoneOffset)));
            global $wpdb;
            $sql = $wpdb->prepare("SELECT count(id) AS noticeCount FROM {$wpdb->prefix}b2s_posts WHERE publish_date > '%s' AND publish_error_code != '' AND blog_user_id = %d", $lastDate, (int) $userId);
            $row = $wpdb->get_row($sql);
            if (isset($row->noticeCount)) {
                return (int) $row->noticeCount;
            }
        }
        return 0;
    }
    
    public static function countReadyForApprove($userId = 0) {
        if($userId > 0) {
            global $wpdb;
            $sql = $wpdb->prepare("SELECT count(id) AS approveCount FROM {$wpdb->prefix}b2s_posts WHERE hide = %d AND publish_date = '%s' AND sched_date_utc <= '%s' AND post_for_approve = %d AND blog_user_id = %d", 0, '0000-00-00 00:00:00', gmdate('Y-m-d H:i:s'), 1, (int) $userId);
            $row = $wpdb->get_row($sql);
            if (isset($row->approveCount)) {
                return (int) $row->approveCount;
            }
        }
        return 0;
    }

}
