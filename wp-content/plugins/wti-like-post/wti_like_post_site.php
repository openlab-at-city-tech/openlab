<?php
/**
 * Get the like output on site
 * @param array
 * @return string
 */
function GetWtiLikePost($arg = null) {
     global $wpdb;
     $post_id = get_the_ID();
     $wti_like_post = "";
     
     // Get the posts ids where we do not need to show like functionality
     $allowed_posts = explode(",", get_option('wti_like_post_allowed_posts'));
     $excluded_posts = explode(",", get_option('wti_like_post_excluded_posts'));
     $excluded_categories = get_option('wti_like_post_excluded_categories');
     $excluded_sections = get_option('wti_like_post_excluded_sections');
     
     if (empty($excluded_categories)) {
          $excluded_categories = array();
     }
     
     if (empty($excluded_sections)) {
          $excluded_sections = array();
     }
     
     $title_text = get_option('wti_like_post_title_text');
     $category = get_the_category();
     $excluded = false;
     
     // Checking for excluded section. if yes, then dont show the like/dislike option
     if ((in_array('home', $excluded_sections) && is_home()) || (in_array('archive', $excluded_sections) && is_archive())) {
          return;
     }
     
     // Checking for excluded categories
     foreach($category as $cat) {
          if (in_array($cat->cat_ID, $excluded_categories) && !in_array($post_id, $allowed_posts)) {
               $excluded = true;
          }
     }
     
     // If excluded category, then dont show the like/dislike option
     if ($excluded) {
          return;
     }
     
     // Check for title text. if empty then have the default value
     if (empty($title_text)) {
          $title_text_like = __('Like', 'wti-like-post');
          $title_text_unlike = __('Unlike', 'wti-like-post');
     } else {
          $title_text = explode('/', get_option('wti_like_post_title_text'));
          $title_text_like = $title_text[0];
          $title_text_unlike = isset( $title_text[1] ) ? $title_text[1] : '';
     }
     
     // Checking for excluded posts
     if (!in_array($post_id, $excluded_posts)) {
          // Get the nonce for security purpose and create the like and unlike urls
          $nonce = wp_create_nonce("wti_like_post_vote_nonce");
          $ajax_like_link = admin_url('admin-ajax.php?action=wti_like_post_process_vote&task=like&post_id=' . $post_id . '&nonce=' . $nonce);
          $ajax_unlike_link = admin_url('admin-ajax.php?action=wti_like_post_process_vote&task=unlike&post_id=' . $post_id . '&nonce=' . $nonce);
          
          $like_count = GetWtiLikeCount($post_id);
          $unlike_count = GetWtiUnlikeCount($post_id);
          $msg = GetWtiVotedMessage($post_id);
          $alignment = ("left" == get_option('wti_like_post_alignment')) ? 'align-left' : 'align-right';
          $show_dislike = get_option('wti_like_post_show_dislike');
          $style = (get_option('wti_like_post_voting_style') == "") ? 'style1' : get_option('wti_like_post_voting_style');
          
          $wti_like_post .= "<div class='watch-action'>";
          $wti_like_post .= "<div class='watch-position " . $alignment . "'>";
          
          $wti_like_post .= "<div class='action-like'>";
          $wti_like_post .= "<a class='lbg-" . $style . " like-" . $post_id . " jlk' href='javascript:void(0)' data-task='like' data-post_id='" . $post_id . "' data-nonce='" . $nonce . "' rel='nofollow'>";
          $wti_like_post .= "<img class='wti-pixel' src='" . plugins_url( 'images/pixel.gif' , __FILE__ ) . "' title='" . __($title_text_like, 'wti-like-post') . "' />";
          $wti_like_post .= "<span class='lc-" . $post_id . " lc'>" . $like_count . "</span>";
          $wti_like_post .= "</a></div>";
          
          if ($show_dislike) {
               $wti_like_post .= "<div class='action-unlike'>";
               $wti_like_post .= "<a class='unlbg-" . $style . " unlike-" . $post_id . " jlk' href='javascript:void(0)' data-task='unlike' data-post_id='" . $post_id . "' data-nonce='" . $nonce . "' rel='nofollow'>";
               $wti_like_post .= "<img class='wti-pixel' src='" . plugins_url( 'images/pixel.gif' , __FILE__ ) . "' title='" . __($title_text_unlike, 'wti-like-post') . "' />";
               $wti_like_post .= "<span class='unlc-" . $post_id . " unlc'>" . $unlike_count . "</span>";
               $wti_like_post .= "</a></div> ";
          }
          
          $wti_like_post .= "</div> ";
          $wti_like_post .= "<div class='status-" . $post_id . " status " . $alignment . "'>" . $msg . "</div>";
          $wti_like_post .= "</div><div class='wti-clear'></div>";
     }
     
     if ($arg == 'put') {
          return $wti_like_post;
     } else {
          echo $wti_like_post;
     }
}

/**
 * Show the like content
 * @param $content string
 * @param $param string
 * @return string
 */
function PutWtiLikePost($content) {
     $show_on_pages = false;
     
     if ((is_page() && get_option('wti_like_post_show_on_pages')) || (!is_page())) {
          $show_on_pages = true;
     }

     if (!is_feed() && $show_on_pages) {     
          $wti_like_post_content = GetWtiLikePost('put');
          $wti_like_post_position = get_option('wti_like_post_position');
          
          if ($wti_like_post_position == 'top') {
               $content = $wti_like_post_content . $content;
          } elseif ($wti_like_post_position == 'bottom') {
               $content = $content . $wti_like_post_content;
          } else {
               $content = $wti_like_post_content . $content . $wti_like_post_content;
          }
     }
     
     return $content;
}

add_filter('the_content', 'PutWtiLikePost');

/**
 * Get already voted message
 * @param $post_id integer
 * @param $ip string
 * @return string
 */
function GetWtiVotedMessage($post_id, $ip = null) {
     global $wpdb, $wti_ip_address;
     $wti_voted_message = '';
     $voting_period = get_option('wti_like_post_voting_period');
     
     if (null == $ip) {
          $ip = $wti_ip_address;
     }
     
     /*$query = $wpdb->prepare(
                    "SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wti_like_post
                    WHERE post_id = %d AND ip = %s",
                    $post_id, $ip
               );*/
     
     if ($voting_period != 0 && $voting_period != 'once') {
          // If there is restriction on revoting with voting period, check with voting time
          $last_voted_date = GetWtiLastDate($voting_period);
          //$query .= " AND date_time >= '$last_voted_date'";
          $query = $wpdb->prepare(
                         "SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wti_like_post
                         WHERE post_id = %d AND ip = %s AND date_time >= %s",
                         $post_id, $ip, $last_voted_date
                    );
     } else {
          $query = $wpdb->prepare(
                         "SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wti_like_post
                         WHERE post_id = %d AND ip = %s",
                         $post_id, $ip
                    );
     }

     $wti_has_voted = $wpdb->get_var($query);
     
     if ($wti_has_voted > 0) {
          $wti_voted_message = get_option('wti_like_post_voted_message');
     }
     
     return $wti_voted_message;
}

add_shortcode('most_liked_posts', 'WtiMostLikedPostsShortcode');

/**
 * Most liked posts shortcode
 * @param $args array
 * @return string
 */
function WtiMostLikedPostsShortcode($args) {
     global $wpdb;
     $most_liked_post = '';
     $where = '';
     
     if (isset($args['limit'])) {
          $limit = $args['limit'];
     } else {
          $limit = 10;
     }
     
     if (!empty($args['time']) && $args['time'] != 'all') {
          $last_date = GetWtiLastDate($args['time']);
          $where .= " AND date_time >= '$last_date'";
     }
     
     $posts = $wpdb->get_results(
                              "SELECT post_id, SUM(value) AS like_count, post_title
                              FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P 
                              WHERE L.post_id = P.ID AND post_status = 'publish' AND value > 0 $where
                              GROUP BY post_id ORDER BY like_count DESC, post_title ASC LIMIT $limit"
                         );
 
     if (count($posts) > 0) {
          $most_liked_post .= '<table class="most-liked-posts-table">';
          $most_liked_post .= '<tr>';
          $most_liked_post .= '<td>' . __('Title', 'wti-like-post') .'</td>';
          $most_liked_post .= '<td>' . __('Like Count', 'wti-like-post') .'</td>';
          $most_liked_post .= '</tr>';
       
          foreach ($posts as $post) {
               $post_title = stripslashes($post->post_title);
               $permalink = get_permalink($post->post_id);
               $like_count = $post->like_count;
               
               $most_liked_post .= '<tr>';
               $most_liked_post .= '<td><a href="' . $permalink . '" title="' . $post_title . '">' . $post_title . '</a></td>';
               $most_liked_post .= '<td>' . $like_count . '</td>';
               $most_liked_post .= '</tr>';
          }
       
          $most_liked_post .= '</table>';
     } else {
          $most_liked_post .= '<p>' . __('No posts liked yet.', 'wti-like-post') . '</p>';
     }
     
     return $most_liked_post;
}

add_shortcode('recently_liked_posts', 'WtiRecentlyLikedPostsShortcode');

/**
 * Get recently liked posts shortcode
 * @param $args array
 * @return string
 */
function WtiRecentlyLikedPostsShortcode($args) {
     global $wpdb;
     $recently_liked_post = '';
     $where = '';
     
     if ( isset( $args['limit'] ) ) {
          $limit = $args['limit'];
     } else {
          $limit = 10;
     }
     
     $show_excluded_posts = get_option('wti_like_post_show_on_widget');
	$excluded_posts = trim( get_option('wti_like_post_excluded_posts') );
     $excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
     
     if ( !$show_excluded_posts && !empty( $excluded_posts ) ) {
          $where = "AND post_id NOT IN (" . $excluded_posts . ")";
     }

     // Get the post IDs recently voted
     $recent_ids = $wpdb->get_col(
                              "SELECT DISTINCT(post_id) FROM `{$wpdb->prefix}wti_like_post`
                              WHERE value > 0 $where GROUP BY post_id ORDER BY MAX(date_time) DESC"
                         );

     if ( count( $recent_ids ) > 0 ) {
          $where = "AND post_id IN(" . implode(",", $recent_ids) . ")";
     
          // Getting the most liked posts
          $query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P 
                    WHERE L.post_id = P.ID AND post_status = 'publish' $where GROUP BY post_id
                    ORDER BY FIELD(post_id, " . implode(",", $recent_ids) . ") ASC LIMIT $limit";
     
          $posts = $wpdb->get_results($query);
     
          if ( count( $posts ) > 0 ) {
               $recently_liked_post .= '<table class="recently-liked-posts-table">';
               $recently_liked_post .= '<tr>';
               $recently_liked_post .= '<td>' . __('Title', 'wti-like-post') .'</td>';
               $recently_liked_post .= '</tr>';
            
               foreach ( $posts as $post ) {
                    $post_title = stripslashes($post->post_title);
                    $permalink = get_permalink($post->post_id);
                    
                    $recently_liked_post .= '<tr>';
                    $recently_liked_post .= '<td><a href="' . $permalink . '" title="' . $post_title . '">' . $post_title . '</a></td>';
                    $recently_liked_post .= '</tr>';
               }
            
               $recently_liked_post .= '</table>';
          }
     } else {
          $recently_liked_post .= '<p>' . __('No posts liked yet.', 'wti-like-post') . '</p>';
     }
     
     return $recently_liked_post;
}

/**
 * Add the javascript and css for the plugin
 * @param no-param
 */
function WtiLikePostEnqueueScripts() {
     // Load javascript file
     wp_register_script( 'wti_like_post_script', plugins_url( 'js/wti_like_post.js', __FILE__ ), array('jquery') );
     wp_localize_script( 'wti_like_post_script', 'wtilp', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

     wp_enqueue_script( 'jquery' );
     wp_enqueue_script( 'wti_like_post_script' );
     
     // Load css file
     wp_register_style( 'wti_like_post_style', plugins_url( 'css/wti_like_post.css', __FILE__ ) );
     wp_enqueue_style( 'wti_like_post_style' );
}