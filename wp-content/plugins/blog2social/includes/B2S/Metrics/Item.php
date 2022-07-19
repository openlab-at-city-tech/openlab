<?php

class B2S_Metrics_Item {
    
    public function __construct() {
        
    }
    
    public function getNetworkCount() {
        global $wpdb;
        $getNetworks = $wpdb->get_results("SELECT count(*) as networkCount, innerSelect.network_id FROM (SELECT network_id FROM {$wpdb->prefix}b2s_posts_insights as insights LEFT JOIN {$wpdb->prefix}b2s_posts_network_details as network ON insights.b2s_posts_network_details_id = network.id WHERE (network_id = 2) OR (network_id = 1 AND network_type = 1) GROUP BY b2s_posts_network_details_id) as innerSelect GROUP BY innerSelect.network_id");
        $networkCount = array(1 => 0, 2 => 0);
        if($getNetworks != null) {
            foreach($getNetworks as $key => $network) {
                if(isset($network->network_id) && (int) $network->network_id > 0) {
                    $networkCount[(int) $network->network_id] = $network->networkCount;
                }
            }
        }
        return $networkCount;
        
    }
    
    public function getInsightsData($filter_network_id = 0, $filter_dates = array()) {
        $todayDate = date('Y-m-d');
        $yesterdayDate = date("Y-m-d", time() - 60 * 60 * 24);
        
        $compareDate1 = date("Y-m-d", time() - 60 * 60 * 24 * 30);
        $compareDate2 = date("Y-m-d", time() - 60 * 60 * 24);
        
        if(is_array($filter_dates) && !empty($filter_dates)) {
            if(count($filter_dates) == 1) {
                $compareDate1 = date("Y-m-d", strtotime($filter_dates[0]) - 60 * 60 * 24);
                $compareDate2 = date("Y-m-d", strtotime($filter_dates[0]) - 60 * 60 * 24 * 2);
            } else if(count($filter_dates) == 2) {
                $compareDate1 = date("Y-m-d", strtotime($filter_dates[0]) - 60 * 60 * 24);
                $compareDate2 = date("Y-m-d", strtotime($filter_dates[1]));
            }
        }
        $date1 = date_create($compareDate1);
        $date2 = date_create($compareDate2);
        $compareDateDiff = date_diff($date1, $date2);

        $filterNetworks = '1,2';
        if($filter_network_id > 0) {
            $filterNetworks = (String) $filter_network_id;
        }
        
        global $wpdb;
        $sqlGetPostsToday = $wpdb->prepare("SELECT insights.insight FROM {$wpdb->prefix}b2s_network_insights as insights LEFT JOIN {$wpdb->prefix}b2s_posts_network_details as network ON insights.b2s_posts_network_details_id = network.id WHERE create_date LIKE %s AND network.network_id IN (".$filterNetworks.")", '%' . $todayDate . '%');
        $getPostsToday = $wpdb->get_results($sqlGetPostsToday);
        $sqlGetPostsYesterday = $wpdb->prepare("SELECT insights.insight FROM {$wpdb->prefix}b2s_network_insights as insights LEFT JOIN {$wpdb->prefix}b2s_posts_network_details as network ON insights.b2s_posts_network_details_id = network.id WHERE create_date LIKE %s AND network.network_id IN (".$filterNetworks.")", '%' . $yesterdayDate . '%');
        $getPostsYesterday = $wpdb->get_results($sqlGetPostsYesterday);
        
        $sqlGetPostsCompareDate1 = $wpdb->prepare("SELECT insights.insight FROM {$wpdb->prefix}b2s_network_insights as insights LEFT JOIN {$wpdb->prefix}b2s_posts_network_details as network ON insights.b2s_posts_network_details_id = network.id WHERE create_date LIKE %s AND network.network_id IN (".$filterNetworks.")", '%' . $compareDate1 . '%');
        $getPostsCompareDate1 = $wpdb->get_results($sqlGetPostsCompareDate1);
        $sqlGetPostsCompareDate2 = $wpdb->prepare("SELECT insights.insight FROM {$wpdb->prefix}b2s_network_insights as insights LEFT JOIN {$wpdb->prefix}b2s_posts_network_details as network ON insights.b2s_posts_network_details_id = network.id WHERE create_date LIKE %s AND network.network_id IN (".$filterNetworks.")", '%' . $compareDate2 . '%');
        $getPostsCompareDate2 = $wpdb->get_results($sqlGetPostsCompareDate2);
        
        $impressionsToday = 0;
        $engagementsToday = 0;
        $postCountToday = 0;
        if($getPostsToday != null) {
            foreach($getPostsToday as $key => $post) {
                if(isset($post->insight) && !empty($post->insight)) {
                    $postInsights = json_decode($post->insight, true);
                    if($postInsights !== false) {
                        $impressionsToday += $postInsights['impressions'];
                        $engagementsToday += $postInsights['likes'];
                        $engagementsToday += $postInsights['comments'];
                        $engagementsToday += $postInsights['reshares'];
                    }
                }
            }
        }
        
        $impressionsYesterday = 0;
        $engagementsYesterday = 0;
        $postCountYesterday = 0;
        if($getPostsYesterday != null) {
            foreach($getPostsYesterday as $key => $post) {
                if(isset($post->insight) && !empty($post->insight)) {
                    $postInsights = json_decode($post->insight, true);
                    if($postInsights !== false) {
                        $impressionsYesterday += $postInsights['impressions'];
                        $engagementsYesterday += $postInsights['likes'];
                        $engagementsYesterday += $postInsights['comments'];
                        $engagementsYesterday += $postInsights['reshares'];
                    }
                }
            }
        }
        
        $impressionsCompare1 = 0;
        $engagementsCompare1 = 0;
        if($getPostsCompareDate1 != null) {
            foreach($getPostsCompareDate1 as $key => $post) {
                if(isset($post->insight) && !empty($post->insight)) {
                    $postInsights = json_decode($post->insight, true);
                    if($postInsights !== false) {
                        $impressionsCompare1 += $postInsights['impressions'];
                        $engagementsCompare1 += $postInsights['likes'];
                        $engagementsCompare1 += $postInsights['comments'];
                        $engagementsCompare1 += $postInsights['reshares'];
                    }
                }
            }
        }
        
        $impressionsCompare2 = 0;
        $engagementsCompare2 = 0;
        if($getPostsCompareDate2 != null) {
            foreach($getPostsCompareDate2 as $key => $post) {
                if(isset($post->insight) && !empty($post->insight)) {
                    $postInsights = json_decode($post->insight, true);
                    if($postInsights !== false) {
                        $impressionsCompare2 += $postInsights['impressions'];
                        $engagementsCompare2 += $postInsights['likes'];
                        $engagementsCompare2 += $postInsights['comments'];
                        $engagementsCompare2 += $postInsights['reshares'];
                    }
                }
            }
        }
        $sqlGetPosts = "SELECT posts.id, insights.insight, insights.last_update, posts.post_id, posts.publish_date, b2s_favorites.blog_user_id as favorites_blog_user_id, insights.active "
        . "FROM {$wpdb->prefix}b2s_posts as posts "
        . "LEFT JOIN {$wpdb->prefix}b2s_posts_insights as insights ON posts.id = insights.b2s_posts_id ";
            $sqlGetPosts .="LEFT JOIN {$wpdb->prefix}b2s_posts_network_details as networkDetails ON posts.network_details_id = networkDetails.id ";
        $sqlGetPosts .= "LEFT JOIN ( SELECT post_id, blog_user_id FROM {$wpdb->prefix}b2s_posts_favorites WHERE blog_user_id = " . B2S_PLUGIN_BLOG_USER_ID . " ) as b2s_favorites ON posts.post_id = b2s_favorites.post_id "
        . "WHERE insights.id IS NOT NULL "
        . "AND posts.publish_date >= '". $compareDate1. " 00:00:00' "
        . "AND posts.publish_date <= '". ((isset($filter_dates) && !empty($filter_dates)) ? $compareDate2 : $todayDate). " 23:59:59' "
        . "AND posts.hide = 0 ";
            $sqlGetPosts .="AND networkDetails.network_id IN(" . $filterNetworks . ") ";
        
        $sqlGetPosts .= " ORDER BY insights.id DESC ";
        
        $getPosts = $wpdb->get_results($sqlGetPosts);
        
        $postsData = array();
        $doneB2sPostsIds = array();
        $postsTotal = 0;
        $postsCountCompare = 0;
        $impressionsTotal = 0;
        $engagementsTotal = 0;
        if($getPosts != null) {
            foreach($getPosts as $key => $post) {
                if(isset($post->id) && (int) $post->id > 0 && !in_array($post->id, $doneB2sPostsIds) && isset($post->post_id) && (int) $post->post_id > 0 && isset($post->insight) && !empty($post->insight)) {
                    $postInsights = json_decode($post->insight, true);
                    if($postInsights !== false && isset($postInsights['insights']) && !empty($postInsights['insights']) && isset($postInsights['insights']['data'])) {
                        $postImpressions = 0;
                        $postLikes = 0;
                        $postComments = 0;
                        $postReshares = 0;
                        $postCount = 0;
                        if(isset($postsData[$post->post_id])) {
                            $postImpressions = $postsData[$post->post_id]['impressions'];
                            $postLikes = $postsData[$post->post_id]['likes'];
                            $postComments = $postsData[$post->post_id]['comments'];
                            $postReshares = $postsData[$post->post_id]['reshares'];
                            $postCount = $postsData[$post->post_id]['count'];
                        }
                        $currentPostInsights = $postInsights['insights']['data'];
                        if(isset($currentPostInsights['likes'])) {
                            end($currentPostInsights['likes']);
                            $key = key($currentPostInsights['likes']);
                            $postImpressions += $currentPostInsights['impressions'][$key];
                            $postLikes += $currentPostInsights['likes'][$key];
                            $postComments += $currentPostInsights['comments'][$key];
                            $postReshares += $currentPostInsights['reshares'][$key];
                            $impressionsTotal += $currentPostInsights['impressions'][$key];
                            $engagementsTotal += $currentPostInsights['likes'][$key];
                            $engagementsTotal += $currentPostInsights['comments'][$key];
                            $engagementsTotal += $currentPostInsights['reshares'][$key];
                        }
                        $postCount++;
                        $postsTotal++;
                        $postsData[$post->post_id]['impressions'] = $postImpressions;
                        $postsData[$post->post_id]['likes'] = $postLikes;
                        $postsData[$post->post_id]['comments'] = $postComments;
                        $postsData[$post->post_id]['reshares'] = $postReshares;
                        $postsData[$post->post_id]['count'] = $postCount;

                        if(!isset($postsData[$post->post_id]['active'])) {
                            $postsData[$post->post_id]['active'] = (int) $post->active;
                        } else {
                            if((int) $post->active == 1) {
                                $postsData[$post->post_id]['active'] = 1;
                            }
                        }
                        $postsData[$post->post_id]['last_update'] = $post->last_update;
                        
                        if(substr($post->publish_date, 0, 10) == $todayDate) {
                            $postCountToday++;
                        }

                        if(substr($post->publish_date, 0, 10) == $yesterdayDate) {
                            $postCountYesterday++;
                        }

                        if(substr($post->publish_date, 0, 10) > $compareDate1 && substr($post->publish_date, 0, 10) <= $compareDate2) {
                            $postsCountCompare++;
                        }

                        $postsData[$post->post_id]['favorite'] = ((isset($post->favorites_blog_user_id) && $post->favorites_blog_user_id != NULL && $post->favorites_blog_user_id == B2S_PLUGIN_BLOG_USER_ID) ? true : false);
                    }
                    $doneB2sPostsIds[] = $post->id;
                }
                
            }
        }
        
        $postsHtml = '';
        foreach($postsData as $wpPostId => $insightsData) {
            $wpPostData = get_post($wpPostId);
            $lastPublish = $this->getLastPost($wpPostId);
            $userInfoName = get_the_author_meta('display_name', $lastPublish['user']);
            $postsHtml .= '<li class="list-group-item" data-active="'.(((int) $insightsData['active'] == 1) ? 1 : 0).'" data-favorites="'.(($insightsData['favorite'] == true) ? 1 : 0).'" data-impressions="'.$insightsData['impressions'].'" data-likes="'.$insightsData['likes'].'" data-comments="'.$insightsData['comments'].'" data-reshares="'.$insightsData['reshares'].'">
                            <div class="media">
                                <div class="media-body">
                                    <div style="width: 8%;display: inline-block;">'.esc_html(B2S_Util::getCustomDateFormat($wpPostData->post_date, substr(B2S_LANGUAGE, 0, 2))).'</div>
                                    <div style="width: 4%;display: inline-block;">' . (($insightsData['favorite'] == true) ?
                                        '<i class="glyphicon glyphicon-star pull-left b2sFavoriteStar" data-post-id="' . esc_attr($wpPostId) . '" data-is-favorite="1"></i>' :
                                        '<i class="glyphicon glyphicon-star-empty pull-left b2sFavoriteStar" data-post-id="' . esc_attr($wpPostId) . '" data-is-favorite="0"></i>'
                                        ) . '</div>
                                    <div style="width: 44%;display: inline-block;">
                                        <strong><a target="_blank" href="'.esc_url(get_permalink($wpPostId)).'">'.esc_html($wpPostData->post_title).'</a></strong>
                                        <p class="info hidden-xs"><a class="b2sGetB2SPostsByWpPost" data-post-id="'.esc_attr($wpPostId).'"><span class="b2s-publish-count" data-post-id="' . esc_attr($wpPostId) . '">'.esc_html($insightsData['count']).'</span> '.esc_html__('shared social media posts', 'blog2social').'</a> | ' . sprintf(esc_html__('latest share by %s', 'blog2social'), '<a href="' . esc_url(get_author_posts_url($lastPublish['user'])) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a>') . '</p>
                                    </div>
                                    <div style="width: 6%;display: inline-block;">
                                        <i class="glyphicon glyphicon-eye-open"></i> '.esc_html($insightsData['impressions']).'
                                    </div>
                                    <div style="width: 6%;display: inline-block;">
                                        <i class="glyphicon glyphicon-thumbs-up"></i> '.esc_html($insightsData['likes']).'
                                    </div>
                                    <div style="width: 6%;display: inline-block;">
                                        <i class="glyphicon glyphicon-refresh"></i> '.esc_html($insightsData['reshares']).'
                                    </div>
                                    <div style="width: 6%;display: inline-block;">
                                        <i class="glyphicon glyphicon-comment"></i> '.esc_html($insightsData['comments']).'
                                    </div>
                                    <div style="width: 17%;display: inline-block;">
                                        <span class="b2s-publish-btn" style="float: right; margin-top: 8px;">
                                            <a class="btn btn-success btn-sm publishPostBtn" href="admin.php?page=blog2social-ship&amp;postId='.esc_attr($wpPostId).'">'.esc_html__('Re-share this post', 'blog2social').'</a>
                                        </span>
                                        '.(((int) $insightsData['active'] == 0) ? '<br><span style="float: right;">last updated on ' . esc_html($insightsData['last_update']) . '</span>' : '').'
                                    </div>
                                    <div class="b2s-post-publish-area" data-post-id="'.esc_attr($wpPostId).'"></div>
                                </div>
                            </div>
                        </li>';
        }
        
        
        return array('general' => array(
            'impressionsTotal' => $impressionsTotal,
            'engagementsTotal' => $engagementsTotal,
            'postCountTotal' => $postsTotal,
            'impressionsToday' => ($impressionsToday >= $impressionsYesterday) ? $impressionsToday - $impressionsYesterday : 0,
            'engagementsToday' => ($engagementsToday >= $engagementsYesterday) ? $engagementsToday - $engagementsYesterday : 0,
            'postCountToday' => $postCountToday,
            'postCountCompare' => $postsCountCompare / $compareDateDiff->d,
            'impressionsCompare' => ($impressionsCompare2 - $impressionsCompare1) / $compareDateDiff->d,
            'engagementsCompare' => ($engagementsCompare2 - $engagementsCompare1) / $compareDateDiff->d
        ), 'posts' => $postsHtml);
        
    }
    
    private function getLastPost($post_id = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND `blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
            $order = " `publish_date` DESC";
            $addWhere = ' AND `publish_error_code` = "" ';
            $where = " `post_for_approve`= 0 AND (`sched_date`= '0000-00-00 00:00:00' OR (`sched_type` = 3 AND `publish_date` != '0000-00-00 00:00:00')) " . $addWhere;
            $fields = "publish_date";
            $sqlLast = "SELECT $fields, blog_user_id FROM `{$wpdb->prefix}b2s_posts` WHERE $where $addNotAdmin AND `hide` = 0 AND `post_id` = " . $post_id . " ORDER BY $order LIMIT 1";
            $result = $wpdb->get_results($sqlLast);
            if (!empty($result)) {
                $date = $result[0]->publish_date;
                $user = (isset($result[0]->blog_user_id) && (int) $result[0]->blog_user_id > 0) ? (int) $result[0]->blog_user_id : 0;
                return array('date' => $date, 'user' => $user);
            }
        }
        return array('date' => date('Y-m-d H:i:s'), 'user' => 0);
    }
    
}