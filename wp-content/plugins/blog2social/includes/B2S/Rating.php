<?php
    class B2S_Rating {
        public static function trigger() {
            global $wpdb;

            if(!get_option('B2S_HIDE_RATING')) {

                $count = $wpdb->get_var("SELECT COUNT(distinct post_id) FROM {$wpdb->prefix}b2s_posts");

                if(in_array($count, array(5,10,15,50,75,100,120,150)) || ($count > 150 && $count % 25 == 0)) {
                    update_option("B2S_SHOW_RATING",array("count" => $count),false);
                }
            }
        }

        public static function is_visible() {
            return !! get_option("B2S_SHOW_RATING");
        }

        public static function count() {
            $option = get_option("B2S_SHOW_RATING");

            return $option && isset($option['count']) ? $option['count'] : 0;
        }

        public static function hide($forever = false) {
            delete_option("B2S_SHOW_RATING");
            if($forever)
            {
                update_option("B2S_HIDE_RATING",true,false);
            }
        }
    }