<?php
    class B2S_Stats {
        private $from = null;

        public function set_from($value) {
            if(is_string($value)) {
                $this->from = $value;
            }

            return $this;
        }

        public function get_from() {
            return $this->from ? date('Y-m-d',strtotime($this->from)): date('Y-m-d',strtotime("-1 WEEK"));
        }

        public function get_result() {
            global $wpdb;

            $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND '.$wpdb->prefix.'b2s_posts.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';

            $sql = "SELECT IFNULL(date1,date2) as _date, IFNULL(count_public,0) as count_public, IFNULL(count_sched,0) as count_sched FROM 
                    (
                    SELECT 
                        *
                    FROM
                        (SELECT 
                            DATE_FORMAT({$wpdb->prefix}b2s_posts.publish_date, '%Y-%m-%d') date1,
                                COUNT(*) as count_public
                        FROM
                            {$wpdb->prefix}b2s_posts
                        WHERE
                            {$wpdb->prefix}b2s_posts.publish_date >= '".$this->get_from()."'
                                && {$wpdb->prefix}b2s_posts.hide = 0
                                ".$addNotAdminPosts."
                        GROUP BY DATE_FORMAT({$wpdb->prefix}b2s_posts.publish_date, '%Y-%m-%d')) public
                            LEFT JOIN
                        (SELECT 
                            DATE_FORMAT({$wpdb->prefix}b2s_posts.sched_date, '%Y-%m-%d') date2,
                                COUNT(*) as count_sched
                        FROM
                            {$wpdb->prefix}b2s_posts
                        WHERE
                            {$wpdb->prefix}b2s_posts.publish_link = ''
                                && {$wpdb->prefix}b2s_posts.sched_date >= '".$this->get_from()."'
                                && {$wpdb->prefix}b2s_posts.hide = 0
                                ".$addNotAdminPosts."
                        GROUP BY DATE_FORMAT({$wpdb->prefix}b2s_posts.sched_date, '%Y-%m-%d')) sched ON sched.date2 = public.date1 
                    UNION SELECT 
                        *
                    FROM
                        (SELECT 
                            DATE_FORMAT({$wpdb->prefix}b2s_posts.publish_date, '%Y-%m-%d') date1,
                                COUNT(*) as count_public
                        FROM
                            {$wpdb->prefix}b2s_posts
                        WHERE
                            {$wpdb->prefix}b2s_posts.publish_date >= '".$this->get_from()."'
                                && {$wpdb->prefix}b2s_posts.hide = 0
                                ".$addNotAdminPosts."
                        GROUP BY DATE_FORMAT({$wpdb->prefix}b2s_posts.publish_date, '%Y-%m-%d')) public
                            RIGHT JOIN
                        (SELECT 
                            DATE_FORMAT({$wpdb->prefix}b2s_posts.sched_date, '%Y-%m-%d') date2,
                                COUNT(*) as count_sched
                        FROM
                            {$wpdb->prefix}b2s_posts
                        WHERE
                            {$wpdb->prefix}b2s_posts.publish_link = ''
                                && {$wpdb->prefix}b2s_posts.sched_date >= '".$this->get_from()."'
                                && {$wpdb->prefix}b2s_posts.hide = 0
                                ".$addNotAdminPosts."
                        GROUP BY DATE_FORMAT({$wpdb->prefix}b2s_posts.sched_date, '%Y-%m-%d')) sched ON sched.date2 = public.date1) as data";

            $items = $wpdb->get_results($sql);

            $res = array();

            foreach($items as $item) {
                $res[$item->_date] = array(intval($item->count_public),intval($item->count_sched));
            }
            return $res;
        }
    }