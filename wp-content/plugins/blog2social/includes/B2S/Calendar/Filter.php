<?php

require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Item.php');

class B2S_Calendar_Filter {

    private $items = [];

    /**
     * @param $sql
     * @return B2S_Calendar_Filter
     */
    public static function getBySql($sql) {
        global $wpdb;

        $res = new B2S_Calendar_Filter();
        $items = $wpdb->get_results($sql);
        foreach ($items as $item) {
            if ($item->sched_date != "0000-00-00 00:00:00" && is_null($item->sched_data) && is_null($item->image_url) && (int) $item->relay_primary_post_id == 0) {
                continue;
            }

            //is relay post?
            if ((int) $item->relay_primary_post_id > 0) {
                //set sched_data & image_url    
                $resSchedData = self::getPrimaryPostSchedData($item->relay_primary_post_id);
                if (isset($resSchedData[0])) {
                    if (isset($resSchedData[0]->sched_data) && !empty($resSchedData[0]->sched_data) && isset($resSchedData[0]->image_url)) {
                        $item->sched_data = $resSchedData[0]->sched_data;
                        $item->image_url = $resSchedData[0]->image_url;
                    }
                    $item->relay_primary_sched_date = $resSchedData[0]->relay_primary_sched_date;
                    //relay post by share now post
                    if (isset($resSchedData[0]->sched_type) && (int) $resSchedData[0]->sched_type == 0) {
                        $item->relay_primary_sched_date = $resSchedData[0]->publish_date;
                    }
                }
            }

            $res->items[] = new B2S_Calendar_Item($item);
        }
        return $res;
    }

    public static function getPrimaryPostSchedData($id = 0) {
        global $wpdb;

        if (!is_numeric($id)) {
            return null;
        }
        $sql = "SELECT {$wpdb->prefix}b2s_posts.sched_type, {$wpdb->prefix}b2s_posts.publish_date, {$wpdb->prefix}b2s_posts.sched_date as relay_primary_sched_date,"
                . "{$wpdb->prefix}b2s_posts_sched_details.sched_data, "
                . "{$wpdb->prefix}b2s_posts_sched_details.image_url, "
                . "{$wpdb->prefix}b2s_posts.sched_details_id "
                . "FROM {$wpdb->prefix}b2s_posts "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details ON {$wpdb->prefix}b2s_posts.sched_details_id = {$wpdb->prefix}b2s_posts_sched_details.id "
                . "INNER JOIN " . $wpdb->posts . " post ON post.ID = {$wpdb->prefix}b2s_posts.post_id "
                . "WHERE {$wpdb->prefix}b2s_posts.id = %d ";

        $sql = $wpdb->prepare($sql, array($id));
        return $wpdb->get_results($sql);
    }

    /**
     * @return B2S_Calendar_Filter|null
     */
    public static function getAll($network_id = 0, $network_details_id = 0) { //0=all
        global $wpdb;
        $res = null;

        $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
        $addNetwork = ($network_id >= 1) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts_network_details.`network_id` = %d', $network_id) : '';
        $addNetworkDetails = ($network_details_id >= 1) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts.`network_details_id` = %d', $network_details_id) : '';
        $approvePosts = " AND (({$wpdb->prefix}b2s_posts.`sched_date_utc` != '0000-00-00 00:00:00' AND {$wpdb->prefix}b2s_posts.`post_for_approve` = 0)OR ({$wpdb->prefix}b2s_posts.`sched_date_utc` >= '" . gmdate('Y-m-d H:i:s') . "' AND {$wpdb->prefix}b2s_posts.`post_for_approve` = 1))";

        $sql = "SELECT {$wpdb->prefix}b2s_posts.sched_date, "
                . "{$wpdb->prefix}b2s_posts.blog_user_id, "
                . "{$wpdb->prefix}b2s_posts.id as b2s_id, "
                . "{$wpdb->prefix}b2s_posts.sched_type as b2s_sched_type, "
                . "{$wpdb->prefix}b2s_posts.user_timezone, "
                . "{$wpdb->prefix}b2s_posts.post_id, "
                . "{$wpdb->prefix}b2s_posts.publish_link, "
                . "{$wpdb->prefix}b2s_posts.relay_primary_post_id, "
                . "{$wpdb->prefix}b2s_posts.relay_delay_min, "
                . "{$wpdb->prefix}b2s_posts.post_for_relay, "
                . "{$wpdb->prefix}b2s_posts.post_for_approve, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_id, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_type, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_display_name, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_auth_id, "
                . "post.post_title, "
                . "post.post_type, "
                . "{$wpdb->prefix}b2s_posts_sched_details.sched_data, "
                . "{$wpdb->prefix}b2s_posts_sched_details.image_url, "
                . "{$wpdb->prefix}b2s_posts.sched_details_id "
                . "FROM {$wpdb->prefix}b2s_posts "
                . "INNER JOIN {$wpdb->prefix}b2s_posts_network_details ON {$wpdb->prefix}b2s_posts.network_details_id = {$wpdb->prefix}b2s_posts_network_details.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details ON {$wpdb->prefix}b2s_posts.sched_details_id = {$wpdb->prefix}b2s_posts_sched_details.id "
                . "INNER JOIN " . $wpdb->posts . " post ON post.ID = {$wpdb->prefix}b2s_posts.post_id "
                . "WHERE {$wpdb->prefix}b2s_posts.publish_link = '' "
                . "AND {$wpdb->prefix}b2s_posts.hide = 0 " . $addNotAdminPosts . $addNetwork . $addNetworkDetails . $approvePosts . " ORDER BY sched_date";


        $res = self::getBySql($sql);

        return $res;
    }

    public static function getFilterNetworkAuthHtml($network_id = 0) {
        global $wpdb;
        $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
        $addNetwork = ($network_id != 19) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts_network_details.`network_id` = %d', $network_id) : ' AND ('.$wpdb->prefix.'b2s_posts_network_details.`network_id` = ' . $network_id . ' OR '.$wpdb->prefix.'b2s_posts_network_details.`network_id` = 8)'; //combine XING old and new
        $approvePosts = " AND (({$wpdb->prefix}b2s_posts.`sched_date_utc` != '0000-00-00 00:00:00' AND {$wpdb->prefix}b2s_posts.`post_for_approve` = 0)OR ({$wpdb->prefix}b2s_posts.`sched_date_utc` >= '" . gmdate('Y-m-d H:i:s') . "' AND {$wpdb->prefix}b2s_posts.`post_for_approve` = 1))";

        $sql = "SELECT {$wpdb->prefix}b2s_posts_network_details.network_type, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_display_name, "
                . "{$wpdb->prefix}b2s_posts.network_details_id "
                . "FROM {$wpdb->prefix}b2s_posts "
                . "INNER JOIN {$wpdb->prefix}b2s_posts_network_details ON {$wpdb->prefix}b2s_posts.network_details_id = {$wpdb->prefix}b2s_posts_network_details.id "
                . "WHERE {$wpdb->prefix}b2s_posts.sched_date != '0000-00-00 00:00:00' AND  {$wpdb->prefix}b2s_posts.publish_error_code= '' "
                . "AND {$wpdb->prefix}b2s_posts.hide = '0' " . $addNotAdminPosts . $addNetwork . $approvePosts . " GROUP BY {$wpdb->prefix}b2s_posts.network_details_id";

        $result = $wpdb->get_results($sql);
        if (is_array($result) && !empty($result)) {
            $content = '<br>';
            $content .= '<select id="b2s-calendar-filter-network-auth-sel" class="form-control" name="b2s-calendar-filter-network-auth-sel">';
            $content .= '<option selected value="all">' . esc_html__("show all", "blog2social") . '</option>';
            $networkType = unserialize(B2S_PLUGIN_NETWORK_TYPE);
            foreach ($result as $k => $v) {
                $content .='<option value="' . esc_attr($v->network_details_id) . '">' . esc_html($networkType[$v->network_type]) . ': ' . esc_html(ucfirst($v->network_display_name)) . '</option>';
            }
            return $content;
        }

        return false;
    }

    /**
     * @return B2S_Calendar_Filter|null
     */
    public static function getByTimespam($start, $end, $network_id = 0, $network_details_id = 0, $filter = 2) { //0=all,1=publish,2=scheduled
        global $wpdb;
        $res = null;

        $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
        $addNetwork = ($network_id >= 1) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts_network_details.`network_id` = %d', $network_id) : '';
        $addNetworkDetails = ($network_details_id >= 1) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts.`network_details_id` = %d', $network_details_id) : '';
        $approvePosts = " AND (({$wpdb->prefix}b2s_posts.`sched_date_utc` != '0000-00-00 00:00:00' AND {$wpdb->prefix}b2s_posts.`post_for_approve` = 0) OR ({$wpdb->prefix}b2s_posts.`sched_date_utc` >= '" . gmdate('Y-m-d H:i:s') . "' AND {$wpdb->prefix}b2s_posts.`post_for_approve` = 1))";

        if ($filter == 1) {//published
            $where = "WHERE {$wpdb->prefix}b2s_posts.publish_date != '0000-00-00 00:00:00' "
                    . "AND publish_error_code = '' "
                    . "AND {$wpdb->prefix}b2s_posts.publish_date BETWEEN '" . date('Y-m-d H:i:s', strtotime($start)) . "' AND '" . date('Y-m-d H:i:s', strtotime($end)) . "' "
                    . "AND {$wpdb->prefix}b2s_posts.hide = 0 " . $addNotAdminPosts . $addNetwork . $addNetworkDetails . " ORDER BY publish_date";
        } elseif ($filter == 2) {//scheduled
            $where = "WHERE {$wpdb->prefix}b2s_posts.sched_date != '0000-00-00 00:00:00' "
                    . "AND {$wpdb->prefix}b2s_posts.sched_date BETWEEN '" . date('Y-m-d H:i:s', strtotime($start)) . "' AND '" . date('Y-m-d H:i:s', strtotime($end)) . "' "
                    . "AND {$wpdb->prefix}b2s_posts.hide = 0 " . $addNotAdminPosts . $addNetwork . $addNetworkDetails . $approvePosts . " ORDER BY sched_date";
        } elseif ($filter == 5) {//reposter
            $where = "WHERE {$wpdb->prefix}b2s_posts.sched_date BETWEEN '" . date('Y-m-d H:i:s', strtotime($start)) . "' AND '" . date('Y-m-d H:i:s', strtotime($end)) . "' "
                    . "AND {$wpdb->prefix}b2s_posts.sched_type = 5 "
                    . "AND {$wpdb->prefix}b2s_posts.hide = 0 " . $addNotAdminPosts . $addNetwork . $addNetworkDetails . $approvePosts . " ORDER BY sched_date";
        } else {//all
            $where = "WHERE {$wpdb->prefix}b2s_posts.hide = 0 "
                    . "AND (({$wpdb->prefix}b2s_posts.sched_date BETWEEN '" . date('Y-m-d H:i:s', strtotime($start)) . "' AND '" . date('Y-m-d H:i:s', strtotime($end)) . "')  "
                    . "OR ({$wpdb->prefix}b2s_posts.publish_date BETWEEN '" . date('Y-m-d H:i:s', strtotime($start)) . "' AND '" . date('Y-m-d H:i:s', strtotime($end)) . "')) "
                    . $addNotAdminPosts . $addNetwork . $addNetworkDetails . " ORDER BY publish_date, sched_date";
        }

        $sql = "SELECT {$wpdb->prefix}b2s_posts.sched_date, "
                . "{$wpdb->prefix}b2s_posts.publish_date, "
                . "{$wpdb->prefix}b2s_posts.publish_link, "
                . "{$wpdb->prefix}b2s_posts.blog_user_id, "
                . "{$wpdb->prefix}b2s_posts.id as b2s_id, "
                . "{$wpdb->prefix}b2s_posts.sched_type as b2s_sched_type, "
                . "{$wpdb->prefix}b2s_posts.user_timezone, "
                . "{$wpdb->prefix}b2s_posts.post_id, "
                . "{$wpdb->prefix}b2s_posts.relay_delay_min, "
                . "{$wpdb->prefix}b2s_posts.post_for_relay, "
                . "{$wpdb->prefix}b2s_posts.post_for_approve, "
                . "{$wpdb->prefix}b2s_posts.relay_primary_post_id, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_id, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_type, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_display_name, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_auth_id, "
                . "post.post_title, "
                . "post.post_type, "
                . "{$wpdb->prefix}b2s_posts_sched_details.sched_data, "
                . "{$wpdb->prefix}b2s_posts_sched_details.image_url, "
                . "{$wpdb->prefix}b2s_posts.sched_details_id, "
                . "{$wpdb->prefix}b2s_posts.publish_error_code "
                . "FROM {$wpdb->prefix}b2s_posts "
                . "INNER JOIN {$wpdb->prefix}b2s_posts_network_details ON {$wpdb->prefix}b2s_posts.network_details_id = {$wpdb->prefix}b2s_posts_network_details.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details ON {$wpdb->prefix}b2s_posts.sched_details_id = {$wpdb->prefix}b2s_posts_sched_details.id "
                . "INNER JOIN " . $wpdb->posts . " post ON post.ID = {$wpdb->prefix}b2s_posts.post_id "
                . $where;

        $res = self::getBySql($sql);
        return $res;
    }

    /**
     * @param $id
     * @return B2S_Calendar_Item|null
     */
    public static function getById($id) {
        global $wpdb;

        if (!is_numeric($id)) {
            return null;
        }

        $sql = "SELECT {$wpdb->prefix}b2s_posts.sched_date, "
                . "{$wpdb->prefix}b2s_posts.blog_user_id, "
                . "{$wpdb->prefix}b2s_posts.id as b2s_id, "
                . "{$wpdb->prefix}b2s_posts.sched_type as b2s_sched_type, "
                . "{$wpdb->prefix}b2s_posts.user_timezone, "
                . "{$wpdb->prefix}b2s_posts.post_id, "
                . "{$wpdb->prefix}b2s_posts.publish_link, "
                . "{$wpdb->prefix}b2s_posts.relay_primary_post_id, "
                . "{$wpdb->prefix}b2s_posts.relay_delay_min, "
                . "{$wpdb->prefix}b2s_posts.post_for_relay, "
                . "{$wpdb->prefix}b2s_posts.post_for_approve, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_id, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_type, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_display_name, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_auth_id, "
                . "post.post_title, "
                . "post.post_type, "
                . "{$wpdb->prefix}b2s_posts_sched_details.sched_data, "
                . "{$wpdb->prefix}b2s_posts_sched_details.image_url, "
                . "{$wpdb->prefix}b2s_posts.sched_details_id "
                . "FROM {$wpdb->prefix}b2s_posts "
                . "INNER JOIN {$wpdb->prefix}b2s_posts_network_details ON {$wpdb->prefix}b2s_posts.network_details_id = {$wpdb->prefix}b2s_posts_network_details.id "
                . "LEFT JOIN {$wpdb->prefix}b2s_posts_sched_details ON {$wpdb->prefix}b2s_posts.sched_details_id = {$wpdb->prefix}b2s_posts_sched_details.id "
                . "INNER JOIN " . $wpdb->posts . " post ON post.ID = {$wpdb->prefix}b2s_posts.post_id "
                . "WHERE {$wpdb->prefix}b2s_posts.id = %d "
                . "AND {$wpdb->prefix}b2s_posts.publish_link = '' "
                . "AND {$wpdb->prefix}b2s_posts.hide = 0 "
                . "ORDER BY sched_date";

        $sql = $wpdb->prepare($sql, array($id));

        $rows = self::getBySql($sql)->getItems();

        if (count($rows) > 0) {
            return $rows[0];
        }

        return null;
    }

    /**
     * @param $id
     * @return B2S_Calendar_Filter|null
     */
    public static function getByPostId($id) {
        global $wpdb;

        if (!is_numeric($id)) {
            return null;
        }

        $sql = "SELECT {$wpdb->prefix}b2s_posts.sched_date, "
                . "{$wpdb->prefix}b2s_posts.blog_user_id, "
                . "{$wpdb->prefix}b2s_posts.id as b2s_id, "
                . "{$wpdb->prefix}b2s_posts.sched_type as b2s_sched_type, "
                . "{$wpdb->prefix}b2s_posts.user_timezone, "
                . "{$wpdb->prefix}b2s_posts.post_id, "
                . "{$wpdb->prefix}b2s_posts.publish_link, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_id, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_type, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_display_name, "
                . "{$wpdb->prefix}b2s_posts_network_details.network_auth_id, "
                . "post.post_title, "
                . "post.post_type, "
                . "{$wpdb->prefix}b2s_posts_sched_details.sched_data, "
                . "{$wpdb->prefix}b2s_posts_sched_details.image_url, "
                . "{$wpdb->prefix}b2s_posts.sched_details_id "
                . "FROM {$wpdb->prefix}b2s_posts "
                . "INNER JOIN {$wpdb->prefix}b2s_posts_network_details ON {$wpdb->prefix}b2s_posts.network_details_id = {$wpdb->prefix}b2s_posts_network_details.id "
                . "INNER JOIN {$wpdb->prefix}b2s_posts_sched_details ON {$wpdb->prefix}b2s_posts.sched_details_id = {$wpdb->prefix}b2s_posts_sched_details.id "
                . "INNER JOIN " . $wpdb->posts . " post ON post.ID = {$wpdb->prefix}b2s_posts.post_id "
                . "WHERE {$wpdb->prefix}b2s_posts.post_id = %d "
                . "AND {$wpdb->prefix}b2s_posts.hide = 0 "
                . "ORDER BY sched_date";

        $sql = $wpdb->prepare($sql, array($id));

        return self::getBySql($sql);
    }

    /**
     * @return B2S_Calendar_Item[]
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @return array
     */
    public function asCalendarArray() {
        $res = [];

        foreach ($this->getItems() as $item) {
            $res[] = $item->asCalendarArray();
        }

        return $res;
    }

    public function getNetworkHtml() {
        $content = '';
        $deprecatedNetwork = 8;
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getNetwork', 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));
        if (is_object($result) && isset($result->result) && (int) $result->result == 1 && isset($result->portale) && is_array($result->portale)) {
            $content = '<label><input type="radio" class="b2s-calendar-filter-network-btn" checked name="b2s-calendar-filter-network-btn" value="all" /><span>all</span></label>';

            foreach ($result->portale as $k => $v) {
                if ($v->id == $deprecatedNetwork) {
                    continue;
                }
                $content .='<label><input type="radio" class="b2s-calendar-filter-network-btn" name="b2s-calendar-filter-network-btn" value="' . esc_attr($v->id) . '" /><span>';
                $content .='<img class="b2s-calendar-filter-img" alt="' . esc_attr($v->name) . '" src="' . esc_url(plugins_url('/assets/images/portale/' . $v->id . '_flat.png', B2S_PLUGIN_FILE)) . '">';
                $content .='</span></label>';
            }
        }
        return $content;
    }

}
