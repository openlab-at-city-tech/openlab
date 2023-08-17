<?php

class B2S_Post_Item {

    protected $postData;
    protected $postTotal = 0;
    protected $postItem = '';
    protected $postPagination = '';
    protected $postPaginationLinks = 5;
    protected $searchAuthorId;
    protected $searchPostStatus;
    protected $searchPostShareStatus;
    protected $searchShowByDate;
    protected $searchShowByNetwork;
    protected $searchPublishDate;
    protected $searchSchedDate;
    protected $searchPostTitle;
    protected $searchPostCat;
    protected $searchPostType;
    protected $postCalendarSchedDates;
    protected $searchUserAuthId;
    protected $searchBlogPostId;
    protected $searchPostSharedById;
    protected $searchSharedAtDateStart;
    protected $searchSharedAtDateEnd;
    protected $userLang;
    protected $results_per_page = null;
    public $currentPage = 0;
    public $type;

    function __construct($type = 'all', $title = "", $authorId = 0, $postStatus = "", $shareStatus = "", $publishDate = '', $schedDate = '', $showByDate = '', $showByNetwork = 0, $userAuthId = 0, $blogPostId = 0, $currentPage = 0, $postCat = 0, $postType = "", $userLang = "en", $results_per_page = B2S_PLUGIN_POSTPERPAGE, $searchPostSharedById = 0, $searchSharedToNetwork = 0, $searchSharedAtDateStart = 0, $searchSharedAtDateEnd = 0) {
        $this->type = $type;
        $this->searchPostTitle = $title;
        $this->searchAuthorId = (int) $authorId;
        $this->searchPostStatus = $postStatus;
        $this->searchPostShareStatus = $shareStatus;
        $this->searchPublishDate = $publishDate;
        $this->searchSchedDate = $schedDate;
        $this->searchShowByDate = $showByDate;
        $this->searchShowByNetwork = (int) $showByNetwork;
        $this->searchUserAuthId = (int) $userAuthId;
        $this->searchBlogPostId = (int) $blogPostId;
        $this->currentPage = (int) $currentPage;
        $this->searchPostCat = (int) $postCat;
        $this->searchPostType = $postType;
        $this->userLang = $userLang; //Plugin: qTranslate
        $this->results_per_page = (int) $results_per_page;
        $this->searchPostSharedById = (int) $searchPostSharedById;
        $this->searchSharedToNetwork = (int) $searchSharedToNetwork;
        $this->searchSharedAtDateStart = $searchSharedAtDateStart;
        $this->searchSharedAtDateEnd = $searchSharedAtDateEnd;
    }

    protected function getData() {
        global $wpdb;

        $addSearchAuthorId = '';
        $addSearchPostTitle = '';
        $order = 'post_date';
        $sortType = 'DESC';
        $leftJoin = "";
        $leftJoin2 = "";
        $leftJoin3 = "";
        $leftJoin4 = "";
        $leftJoinWhere = "";

        if (!empty($this->searchPostTitle)) {
            $addSearchPostTitle = $wpdb->prepare(' AND posts.`post_title` LIKE %s', '%' . trim($this->searchPostTitle) . '%');
        }
        if ($this->searchAuthorId > 0) {
            $addSearchAuthorId = $wpdb->prepare(' AND posts.`post_author` = %d', $this->searchAuthorId);
        }

        if ($this->type != 'video') {
            if (!empty($this->searchPublishDate)) {
                $sortType = $this->searchPublishDate;
            }
            if (!empty($this->searchSchedDate)) {
                $sortType = $this->searchSchedDate;
            }
            if ($this->searchPostCat > 0) {
                $catIn = '(' . $this->searchPostCat;
                if (function_exists('get_categories')) {
                    $args = array('child_of' => $this->searchPostCat);
                    $subCategories = get_categories($args);
                    if (is_array($subCategories) && !empty($subCategories)) {
                        foreach ($subCategories as $subCat) {
                            if ((int) $subCat->term_taxonomy_id > 0) {
                                $catIn .= ',' . $subCat->term_taxonomy_id;
                            }
                        }
                    }
                }
                $catIn .= ')';
                if ($this->type == 'all') {
                    $leftJoin = "LEFT JOIN $wpdb->term_relationships ON posts.`ID` = $wpdb->term_relationships.`object_id`";
                } else {
                    $leftJoin = "LEFT JOIN $wpdb->term_relationships ON posts.`ID` = $wpdb->term_relationships.`object_id`";
                }
                $leftJoin2 = "LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_taxonomy.`term_taxonomy_id` = $wpdb->term_relationships.`term_taxonomy_id`";
                $leftJoinWhere = "AND  ($wpdb->term_taxonomy.`term_id` IN " . $catIn . " )";
            }

            if (!empty($this->searchPostStatus)) {
                $addSearchType = $wpdb->prepare(' posts.`post_status` = %s', $this->searchPostStatus);
            } else {
                //V5.0.0 include Content Curation (post_status = private)
                $addSearchType = " (posts.`post_status` = 'publish' OR posts.`post_status` = 'pending' " . (($this->type == 'draft-post' || $this->type == 'notice' || $this->type == 'publish' || $this->type == 'sched') ? " OR posts.`post_status` = 'inherit'" : "") . " OR posts.`post_status` = 'future' " . (($this->type != 'all') ? " OR posts.`post_status` = 'private'" : "") . ") ";
            }

            if (!empty($this->searchSharedToNetwork)) {
                $sharedToNetworkSelect = ", b2sNetwork.network_id  ";
                $sharedToNetworkJoin = "LEFT JOIN `{$wpdb->prefix}b2s_posts_network_details` b2sNetwork on a.network_details_id = b2sNetwork.id ";
                $sharedToNetworkWhere = " AND filter.network_id = " . $this->searchSharedToNetwork;
            } else {
                $sharedToNetworkSelect = " ";
                $sharedToNetworkJoin = " ";
                $sharedToNetworkWhere = " ";
            }

            if (!empty($this->searchPostShareStatus)) {
                $leftJoin4 = "LEFT JOIN `{$wpdb->prefix}b2s_posts` b2sStatus on posts.ID = b2sStatus.post_id";
                if ($this->searchPostShareStatus == 'shared') {
                    $leftJoinWhere .= ' AND b2sStatus.post_id IS NOT NULL AND b2sStatus.publish_date != "0000-00-00 00:00:00" AND  b2sStatus.publish_error_code = ""  AND b2sStatus.hide = 0';
                } else if ($this->searchPostShareStatus == 'scheduled') {
                    $leftJoinWhere .= ' AND b2sStatus.post_id IS NOT NULL AND b2sStatus.sched_date != "0000-00-00 00:00:00" AND b2sStatus.sched_date_utc > "' . gmdate("Y-m-d H:i:s") . '" AND  b2sStatus.post_for_relay=0 AND b2sStatus.post_for_approve = 0 AND b2sStatus.hide = 0';
                } else if ($this->searchPostShareStatus == 'autopost') {
                    $leftJoinWhere .= ' AND b2sStatus.post_id IS NOT NULL AND b2sStatus.sched_type = 3 AND  b2sStatus.post_for_relay=0 AND b2sStatus.post_for_approve = 0 AND b2sStatus.hide = 0';
                } else if ($this->searchPostShareStatus == 'repost') {
                    $leftJoinWhere .= ' AND b2sStatus.post_id IS NOT NULL AND b2sStatus.sched_type = 5 AND  b2sStatus.post_for_relay=0 AND b2sStatus.post_for_approve = 0 AND b2sStatus.hide = 0';
                } else {
                    //never
                    $leftJoin4 = "LEFT JOIN ( SELECT * FROM `{$wpdb->prefix}b2s_posts` WHERE hide = 0 ) as b2sStatus on posts.ID = b2sStatus.post_id";
                    $leftJoinWhere .= " AND b2sStatus.post_id IS NULL";
                }
            }

            if ($this->searchPostSharedById > 0 && $this->type != 'draft' && $this->type != 'draft-post') {
                if ($this->type == 'all' || $this->type == 'favorites') {
                    $leftJoin3 = "LEFT JOIN `{$wpdb->prefix}b2s_posts` b2s on posts.ID = b2s.post_id";
                    $leftJoinWhere .= " AND b2s.hide = 0 AND b2s.blog_user_id = " . $this->searchPostSharedById;
                } else {
                    $leftJoinWhere .= " AND filter.hide = 0 AND filter.blog_user_id = " . $this->searchPostSharedById;
                }
            }

            $postTypes = " ";
            if (!empty($this->searchPostType)) {
                $postTypes .= " posts.`post_type` LIKE '%" . $this->searchPostType . "%' ";
            } else {
                $post_types = get_post_types(array('public' => true));
                if (is_array($post_types) && !empty($post_types)) {
                    //V5.0.0 Add Content Curation manuelly because is not public
                    if ($this->type != 'all') {
                        $post_types['Content Curation'] = 'b2s_ex_post';
                    }
                    $postTypes .= " posts.`post_type` IN("; // AND
                    foreach ($post_types as $k => $v) {
                        if (!in_array($this->type, array('draft-post', 'notice', 'publish'))) {
                            if (($v == "attachment" && $this->type == "sched") || ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision')) {
                                $postTypes .= "'" . $v . "',";
                            }
                        } else {
                            if ($v != 'nav_menu_item' && $v != 'revision') {
                                $postTypes .= "'" . $v . "',";
                            }
                        }
                    }
                    $postTypes = rtrim($postTypes, ',');
                    $postTypes .= " ) ";
                } else {
                    $postTypes .= " (posts.`post_type` LIKE '%product%' OR posts.`post_type` LIKE '%book%' OR posts.`post_type` LIKE '%article%' OR posts.`post_type` LIKE '%job%' OR posts.`post_type` LIKE '%event%' OR posts.`post_type` = 'post' OR posts.`post_type` = 'page' OR posts.`post_type` = 'b2s_ex_post' " . ( ($this->type == 'draft-post' || $this->type == 'notice' || $this->type == 'publish') ? " OR posts.`post_type` = 'attachment'" : "") . " ) ";
                }
            }
        } else {
            //Video,Media Library
            $postTypes = " posts.`post_type` LIKE 'attachment' ";
        }

        $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND posts.`post_author` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
        $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND a.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';

        $wpdb->query("SET SQL_BIG_SELECTS = 1");
        if ($this->type == 'all') {
            $sqlPosts = "SELECT DISTINCT posts.`ID`, posts.`post_author`, posts.`post_date`, posts.`post_type`, posts.`post_status`, posts.`post_title`, b2s_drafts.blog_user_id as draft_blog_user_id, b2s_favorites.blog_user_id as favorites_blog_user_id
		FROM `$wpdb->posts` posts $leftJoin $leftJoin2 $leftJoin3 $leftJoin4
                LEFT JOIN ( SELECT post_id, blog_user_id FROM {$wpdb->prefix}b2s_posts_drafts WHERE blog_user_id = " . B2S_PLUGIN_BLOG_USER_ID . " AND save_origin = 0 ) as b2s_drafts ON posts.ID = b2s_drafts.post_id 
                LEFT JOIN ( SELECT post_id, blog_user_id FROM {$wpdb->prefix}b2s_posts_favorites WHERE blog_user_id = " . B2S_PLUGIN_BLOG_USER_ID . " ) as b2s_favorites ON posts.ID = b2s_favorites.post_id 
		WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle $addNotAdmin
                AND  $postTypes $leftJoinWhere
		ORDER BY `" . $order . "` " . $sortType . "
                LIMIT " . (($this->currentPage - 1) * $this->results_per_page) . "," . $this->results_per_page;

            $this->postData = $wpdb->get_results($sqlPosts);

            $sqlPostsTotal = "SELECT DISTINCT posts.`ID`
		FROM `$wpdb->posts` posts $leftJoin $leftJoin2 $leftJoin3 $leftJoin4
		WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle $addNotAdmin
                AND $postTypes $leftJoinWhere";
            $this->postTotal = count($wpdb->get_results($sqlPostsTotal));
        }
        if ($this->type == 'video') {
            $postMimeType = " AND posts.`post_mime_type` LIKE '%video%' ";
            $sqlPosts = "SELECT DISTINCT posts.`ID`, posts.`post_author`, posts.`post_date`, posts.`guid`, posts.`post_title`, posts.`post_mime_type`
		FROM `$wpdb->posts` posts 
                WHERE $postTypes $postMimeType $addSearchPostTitle $addSearchAuthorId $addNotAdmin
		ORDER BY `" . $order . "` " . $sortType . "
                LIMIT " . (($this->currentPage - 1) * $this->results_per_page) . "," . $this->results_per_page;

            $this->postData = $wpdb->get_results($sqlPosts);
            $sqlPostsTotal = "SELECT DISTINCT posts.`ID`
		FROM `$wpdb->posts` posts 
		WHERE $postTypes $postMimeType $addSearchPostTitle $addSearchAuthorId $addNotAdmin";
            $this->postTotal = count($wpdb->get_results($sqlPostsTotal));
        }
        if ($this->type == 'publish' || $this->type == 'notice' || $this->type == 'sched' || $this->type == 'approve') {
            //ExistsTable
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts'") == $wpdb->prefix . 'b2s_posts') {
                if ($this->type == 'approve') {
                    $addWhere = "";
                    $where = " a.`hide` = 0 AND a.`post_for_approve` = 1 AND (a.`publish_date` != '0000-00-00 00:00:00' OR a.`sched_date_utc` <= '" . gmdate('Y-m-d H:i:s') . "') $addNotAdminPosts GROUP BY a.`post_id` ORDER BY a.`sched_date` " . $sortType;
                    $orderBy = " ORDER BY filter.`sched_date` " . $sortType;
                    $addSearchBlogPostId = ((int) $this->searchBlogPostId != 0) ? " a.`post_id` = " . (int) $this->searchBlogPostId . " AND " : '';
                    $addSearchShowByDate = (!empty($this->searchShowByDate)) ? " (DATE_FORMAT(a.`publish_date`,'%Y-%m-%d') = '" . $this->searchShowByDate . "' OR DATE_FORMAT(a.`sched_date`,'%Y-%m-%d') = '" . $this->searchShowByDate . "') AND " : '';
                    $select = ' filter.`blog_user_id`, filter.`publish_date`, filter.`sched_date` ';
                    $selectInnerJoin = ' `sched_date` , `publish_date` ';
                } else {
                    $addWhere = ($this->type == 'notice') ? ' AND a.`publish_error_code` != "" ' : ' AND a.`publish_error_code` = "" ';
                    if($this->type == 'publish' || $this->type == 'notice'){
                        $where = " a.`hide` = 0 AND a.`post_for_approve`= 0 AND (a.`sched_date`= '0000-00-00 00:00:00' OR (a.`sched_type` = 3 AND a.`publish_date` != '0000-00-00 00:00:00')) $addWhere $addNotAdminPosts GROUP BY a.`post_id` ORDER BY a.`publish_date` " . $sortType;
                    } else {
                        $where = " a.`hide` = 0 
                        AND ((a.`sched_date_utc` != '0000-00-00 00:00:00' AND a.`post_for_approve` = 0) 
                            OR (a.`sched_date_utc` >= '" . gmdate('Y-m-d H:i:s') . "' AND a.`post_for_approve` = 1)) 
                        AND (a.`sched_type` != 3 OR (a.`sched_type` = 3 AND a.`publish_date` = '0000-00-00 00:00:00')) 
                        AND a.`publish_date`= '0000-00-00 00:00:00' $addNotAdminPosts 
                        GROUP BY a.`post_id` ORDER BY a.`sched_date` " . $sortType;
                    }
                    $orderBy = ($this->type == 'publish' || $this->type == 'notice') ? " ORDER BY filter.max_publish_date " . $sortType : " ORDER BY filter.`sched_date` " . $sortType;
                    $addSearchBlogPostId = ((int) $this->searchBlogPostId != 0) ? " a.`post_id` = " . (int) $this->searchBlogPostId . " AND " : '';
                    $addSearchShowByDate = (!empty($this->searchShowByDate)) ? (($this->type == 'publish' || $this->type == 'notice') ? " DATE_FORMAT(a.`publish_date`,'%Y-%m-%d') = '" . $this->searchShowByDate . "' AND " : " DATE_FORMAT(a.`sched_date`,'%Y-%m-%d') = '" . $this->searchShowByDate . "' AND ") : '';
                    $select = ($this->type == 'publish' || $this->type == 'notice') ? 'filter.`blog_user_id`, filter.max_publish_date' : 'filter.`blog_user_id`, filter.`sched_date`';
                    $selectInnerJoin = ($this->type == 'publish' || $this->type == 'notice') ? 'MAX(a.`publish_date`) as max_publish_date' : '`sched_date`';
                }
                $addInnerJoinLeftJoin = ((int) $this->searchUserAuthId != 0) ? ' LEFT JOIN ' . $wpdb->prefix . 'b2s_posts_network_details b ON b.`id` = a.`network_details_id` ' : '';
                $addInnnerJoinLeftJoinWhere = ((int) $this->searchUserAuthId != 0) ? ' b.`network_auth_id` =' . $this->searchUserAuthId . ' AND ' : '';

                $addInnerJoinLeftJoinNetwork = ((int) $this->searchShowByNetwork != 0 && empty($addInnerJoinLeftJoin)) ? ' LEFT JOIN ' . $wpdb->prefix . 'b2s_posts_network_details b ON b.`id` = a.`network_details_id` ' : '';
                $addInnnerJoinLeftJoinWhereNetwork = ((int) $this->searchShowByNetwork != 0) ? ' b.`network_id` =' . $this->searchShowByNetwork . ' AND ' : '';

                $video = '';
                if (!empty($this->searchPostType) && $this->searchPostType == 'attachment') {
                    $postTypes .= " AND posts.`post_mime_type` LIKE '%video%' ";
                }


                $sqlPosts = "SELECT DISTINCT posts.`ID`, posts.`post_author`,posts.`post_type`,posts.`post_title`, " . $select . ", filter.`id` 
                            FROM `$wpdb->posts` posts $leftJoin $leftJoin2
                                INNER JOIN(
                                        SELECT a.`id`,$selectInnerJoin, a.`blog_user_id`, a.`hide`, a.`post_id` $sharedToNetworkSelect
                                            FROM `{$wpdb->prefix}b2s_posts` a $addInnerJoinLeftJoin $addInnerJoinLeftJoinNetwork $sharedToNetworkJoin
                                                  WHERE $addInnnerJoinLeftJoinWhere $addInnnerJoinLeftJoinWhereNetwork $addSearchBlogPostId $addSearchShowByDate $where 
                                         ) filter
                                     ON posts.`ID` = filter.`post_id`
                             WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle AND $postTypes $video $sharedToNetworkWhere $leftJoinWhere $orderBy
                        LIMIT " . (($this->currentPage - 1) * $this->results_per_page) . "," . $this->results_per_page;
                $this->postData = $wpdb->get_results($sqlPosts);

                if ($this->type == 'publish' || $this->type == 'notice' || $this->type == 'approve') {
                    $sqlPostsTotal = "SELECT DISTINCT COUNT(posts.`ID`)
                            FROM `$wpdb->posts` posts $leftJoin $leftJoin2
                                INNER JOIN(
                                        SELECT a.`post_id`, a.`blog_user_id`, a.`hide`
                                            FROM `{$wpdb->prefix}b2s_posts` a
                                                 WHERE $addSearchShowByDate $where
                                         ) filter
                                     ON posts.`ID` = filter.`post_id`
                             WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle AND $postTypes $leftJoinWhere";
                    $this->postTotal = $wpdb->get_var($sqlPostsTotal);
                    //for Calender (mark Event)
                } else {
                    $where = " a.`hide` = 0 AND (a.`sched_type` != 3 OR (a.`sched_type` = 3 AND a.`publish_date` = '0000-00-00 00:00:00')) AND ((a.`sched_date_utc` != '0000-00-00 00:00:00' AND a.`post_for_approve` = 0)OR (a.`sched_date_utc` >= '" . gmdate('Y-m-d H:i:s') . "' AND a.`post_for_approve` = 1)) AND a.`publish_date`= '0000-00-00 00:00:00' $addNotAdminPosts  ORDER BY a.`sched_date` " . $sortType;
                    $sqlPostsTotal = "SELECT DISTINCT posts.`ID`, DATE_FORMAT(filter.`sched_date`,'%Y-%m-%d') AS sched
                            FROM `$wpdb->posts` posts $leftJoin $leftJoin2
                                INNER JOIN(
                                        SELECT a.`post_id`, a.`sched_date`, a.`blog_user_id`, a.`hide`
                                            FROM `{$wpdb->prefix}b2s_posts` a $addInnerJoinLeftJoin $addInnerJoinLeftJoinNetwork
                                                 WHERE $addInnnerJoinLeftJoinWhere $addInnnerJoinLeftJoinWhereNetwork $addSearchShowByDate $where 
                                         ) filter
                                     ON posts.`ID` = filter.`post_id`
                             WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle AND $postTypes $leftJoinWhere";

                    $schedResult = $wpdb->get_results($sqlPostsTotal);
                    if (is_array($schedResult) && !empty($schedResult)) {
                        $this->postCalendarSchedDates = array();
                        $postIds = array();
                        foreach ($schedResult as $k => $v) {
                            if (!in_array($v->ID, $postIds)) {
                                $postIds[] = $v->ID;
                            }
                            if (!in_array($v->sched, $this->postCalendarSchedDates)) {
                                $this->postCalendarSchedDates[] = $v->sched;
                            }
                        }
                        $this->postTotal = count($postIds);
                    }
                }
            }
        }

        if ($this->type == 'draft') {
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts'") == $wpdb->prefix . 'b2s_posts') {
                $sqlPosts = "SELECT posts.`ID`, posts.`post_author`, posts.`post_date`, posts.`post_type`, posts.`post_status`, posts.`post_title`, posts.`post_content`, posts.`guid`, postmeta.meta_value, posts_drafts.data AS draft_data  
		FROM `$wpdb->posts` posts $leftJoin $leftJoin2 
                LEFT JOIN {$wpdb->prefix}b2s_posts ON posts.ID = {$wpdb->prefix}b2s_posts.post_id 
                LEFT JOIN (SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = '_thumbnail_id') AS postmeta ON posts.ID = postmeta.post_id 
                LEFT JOIN (SELECT * FROM {$wpdb->prefix}b2s_posts_drafts WHERE save_origin = 1) AS posts_drafts ON posts.ID = posts_drafts.post_id 
		WHERE (posts.`post_type` LIKE '%b2s_ex_post%')
                AND {$wpdb->prefix}b2s_posts.post_id IS NULL AND (posts.`post_status` = 'private' OR posts.`post_status` = 'draft') 
                $addSearchAuthorId $addSearchPostTitle $addNotAdmin $leftJoinWhere 
		ORDER BY `" . $order . "` " . $sortType . "
                LIMIT " . (($this->currentPage - 1) * $this->results_per_page) . "," . $this->results_per_page;
                $this->postData = $wpdb->get_results($sqlPosts);

                $sqlPostsTotal = "SELECT COUNT(*)
		FROM `$wpdb->posts` posts $leftJoin $leftJoin2 
                LEFT JOIN {$wpdb->prefix}b2s_posts ON posts.ID = {$wpdb->prefix}b2s_posts.post_id 
		WHERE (posts.`post_type` LIKE '%b2s_ex_post%')
                AND {$wpdb->prefix}b2s_posts.post_id IS NULL AND (posts.`post_status` = 'private' OR posts.`post_status` = 'draft') 
                $addSearchAuthorId $addSearchPostTitle $addNotAdmin $leftJoinWhere";
                $this->postTotal = $wpdb->get_var($sqlPostsTotal);
            }
        }

        if ($this->type == 'draft-post') {
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts_drafts'") == $wpdb->prefix . 'b2s_posts_drafts') {
                if ($this->searchSharedAtDateStart != 0) {
                    $startDate = " AND {$wpdb->prefix}b2s_posts_drafts.`last_save_date` >= '" . date('Y-m-d H:i:s', strtotime($this->searchSharedAtDateStart)) . "' ";
                } else {
                    $startDate = "";
                }
                if ($this->searchSharedAtDateEnd != 0) {
                    $endDate = " AND {$wpdb->prefix}b2s_posts_drafts.`last_save_date` <= '" . date('Y-m-d H:i:s', strtotime($this->searchSharedAtDateEnd)) . "' ";
                } else {
                    $endDate = "";
                }

                $sqlPosts = "SELECT {$wpdb->prefix}b2s_posts_drafts.`ID` AS draft_id, posts.`ID`, {$wpdb->prefix}b2s_posts_drafts.`post_id`, {$wpdb->prefix}b2s_posts_drafts.`last_save_date`, {$wpdb->prefix}b2s_posts_drafts.`data`, posts.post_author, posts.post_type, posts.post_title 
		FROM `$wpdb->posts` posts LEFT JOIN {$wpdb->prefix}b2s_posts_drafts ON {$wpdb->prefix}b2s_posts_drafts.post_id = posts.ID $leftJoin $leftJoin2 $leftJoin3 $leftJoin4
                WHERE {$wpdb->prefix}b2s_posts_drafts.`blog_user_id` = " . B2S_PLUGIN_BLOG_USER_ID;
                if (!empty($this->searchPostType)) {
                    if ($this->searchPostType == 'b2s_ex_post') {
                        $sqlPosts .= " AND {$wpdb->prefix}b2s_posts_drafts.`save_origin` = 1 ";
                    } else {
                        $sqlPosts .= " AND {$wpdb->prefix}b2s_posts_drafts.`save_origin` = 0 "
                                . " AND $addSearchType ";
                    }
                }
                $video = '';
                if (!empty($this->searchPostType) && $this->searchPostType == 'attachment') {
                    $postTypes .= " AND posts.`post_mime_type` LIKE '%video%' ";
                }

                $sqlPosts .= " AND $postTypes $video
                $startDate $endDate 
                $addSearchAuthorId $addSearchPostTitle $addNotAdmin $leftJoinWhere 
		ORDER BY `last_save_date` " . $sortType . "
                LIMIT " . (($this->currentPage - 1) * $this->results_per_page) . "," . $this->results_per_page;
                $this->postData = $wpdb->get_results($sqlPosts);

                $sqlPostsTotal = "SELECT COUNT(*)
		FROM `$wpdb->posts` posts LEFT JOIN {$wpdb->prefix}b2s_posts_drafts ON {$wpdb->prefix}b2s_posts_drafts.post_id = posts.ID $leftJoin $leftJoin2 $leftJoin3 $leftJoin4
		WHERE {$wpdb->prefix}b2s_posts_drafts.`blog_user_id` = " . B2S_PLUGIN_BLOG_USER_ID;
                if (!empty($this->searchPostType)) {
                    if (!empty($this->searchPostType) && $this->searchPostType == 'b2s_ex_post') {
                        $sqlPosts .= " AND {$wpdb->prefix}b2s_posts_drafts.`save_origin` = 1 ";
                    } else {
                        $sqlPosts .= " AND {$wpdb->prefix}b2s_posts_drafts.`save_origin` = 0 "
                                . " AND $addSearchType ";
                    }
                }
                $sqlPosts .= " AND $postTypes $video
                $startDate $endDate 
                $addSearchAuthorId $addSearchPostTitle $addNotAdmin $leftJoinWhere";
                $this->postTotal = $wpdb->get_var($sqlPostsTotal);
            }
        }

        if ($this->type == 'favorites') {
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts_favorites'") == $wpdb->prefix . 'b2s_posts_favorites') {
                $sqlPosts = "SELECT DISTINCT {$wpdb->prefix}b2s_posts_favorites.`ID` AS favorite_id, posts.`ID`, {$wpdb->prefix}b2s_posts_favorites.`post_id`, posts.post_author, posts.post_type, posts.post_title, posts.post_date, posts.post_status, b2s_drafts.blog_user_id as draft_blog_user_id 
		FROM `$wpdb->posts` posts LEFT JOIN {$wpdb->prefix}b2s_posts_favorites ON {$wpdb->prefix}b2s_posts_favorites.post_id = posts.ID $leftJoin $leftJoin2 $leftJoin3 $leftJoin4
                LEFT JOIN ( SELECT post_id, blog_user_id FROM {$wpdb->prefix}b2s_posts_drafts WHERE blog_user_id = " . B2S_PLUGIN_BLOG_USER_ID . " AND save_origin = 0 ) as b2s_drafts ON posts.ID = b2s_drafts.post_id		
                WHERE {$wpdb->prefix}b2s_posts_favorites.`blog_user_id` = " . B2S_PLUGIN_BLOG_USER_ID . "  
                AND $postTypes 
                AND $addSearchType 
                $addSearchAuthorId $addSearchPostTitle $addNotAdmin $leftJoinWhere 
		ORDER BY `" . $order . "` " . $sortType . "
                LIMIT " . (($this->currentPage - 1) * $this->results_per_page) . "," . $this->results_per_page;
                $this->postData = $wpdb->get_results($sqlPosts);

                $sqlPostsTotal = "SELECT DISTINCT posts.`ID` 
		FROM `$wpdb->posts` posts LEFT JOIN {$wpdb->prefix}b2s_posts_favorites ON {$wpdb->prefix}b2s_posts_favorites.post_id = posts.ID $leftJoin $leftJoin2 $leftJoin3 $leftJoin4
		WHERE {$wpdb->prefix}b2s_posts_favorites.`blog_user_id` = " . B2S_PLUGIN_BLOG_USER_ID . " 
                AND $postTypes 
                AND $addSearchType 
                $addSearchAuthorId $addSearchPostTitle $addNotAdmin $leftJoinWhere";
                $this->postTotal = count($wpdb->get_results($sqlPostsTotal));
            }
        }

        if ($this->type == 'repost') {
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts'") == $wpdb->prefix . 'b2s_posts') {
                $sqlPosts = "SELECT DISTINCT b2s_posts.blog_user_id, posts.`ID`, posts.post_author, posts.post_type, posts.post_title 
		FROM `$wpdb->posts` posts LEFT JOIN {$wpdb->prefix}b2s_posts AS b2s_posts ON b2s_posts.post_id = posts.ID
                WHERE b2s_posts.`sched_type` = 5  
                AND b2s_posts.`hide` = '0' 
                AND b2s_posts.`publish_date` = '0000-00-00 00:00:00' 
                AND b2s_posts.`blog_user_id` = " . B2S_PLUGIN_BLOG_USER_ID . " 
		ORDER BY b2s_posts.`sched_date` ASC";
                $this->postData = $wpdb->get_results($sqlPosts);

                $sqlPostsTotal = "SELECT DISTINCT posts.`ID` 
		FROM `$wpdb->posts` posts LEFT JOIN {$wpdb->prefix}b2s_posts AS b2s_posts ON b2s_posts.post_id = posts.ID
                WHERE b2s_posts.`sched_type` = 5  
                AND b2s_posts.`hide` = '0' 
                AND b2s_posts.`publish_date` = '0000-00-00 00:00:00' 
                AND b2s_posts.`blog_user_id` = " . B2S_PLUGIN_BLOG_USER_ID;
                $this->postTotal = count($wpdb->get_results($sqlPostsTotal));
            }
        }
    }

    public function getItemHtml($selectSchedDate = "") {
        $this->getData();
        $postStatus = array('publish' => __('published', 'blog2social'), 'pending' => __('draft', 'blog2social'), 'future' => __('scheduled', 'blog2social'));

        if (empty($this->postData)) {
            if ($this->type == 'video') {
                $text = esc_html__('Upload your first video file now and share on your video networks', 'blog2social');
            } elseif ($this->type == 'draft-post') {
                $text = esc_html__('You have not saved any drafts.', 'blog2social');
            } elseif ($this->type == 'favorites') {
                $text = esc_html__('You have not saved any favorites.', 'blog2social');
            } elseif ($this->type == 'repost') {
                $text = esc_html__('You have no posts in your queue.', 'blog2social');
            } else {
                $text = esc_html__('You have not published or scheduled any posts.', 'blog2social');
            }
            return '<li class="list-group-item"><div class="media"><div class="media-body"></div><p>' . esc_html($text) . '</p></div></li>';
        }

        $videoAddonDetails = false;
        if ($this->type == 'video' && defined('B2S_PLUGIN_ADDON_VIDEO')) {
            if (!empty(B2S_PLUGIN_ADDON_VIDEO)) {
                $videoAddonDetails = B2S_PLUGIN_ADDON_VIDEO;
            }
        }
        foreach ($this->postData as $var) {
            $postType = 'post';
            if (strpos(strtolower($var->post_type), 'event') !== false) {
                $postType = 'event';
            }
            if (strpos(strtolower($var->post_type), 'job') !== false) {
                $postType = 'job';
            }
            if (strpos(strtolower($var->post_type), 'product') !== false) {
                $postType = 'product';
            }
            if (strpos(strtolower($var->post_type), 'attachment') !== false) {
                $postType = 'video';
            }

            //Plugin: qTranslate
            $postTitle = B2S_Util::getTitleByLanguage($var->post_title, $this->userLang);
            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                $postTitle = (mb_strlen(trim($postTitle), 'UTF-8') > 80 ? mb_substr($postTitle, 0, 77, 'UTF-8') . '...' : $postTitle);
            }

            //Content Curation
            $curated = (strtolower($var->post_type) == 'b2s_ex_post') ? ' - <strong>' . esc_html__('curated post', 'blog2social') . '</strong>' : '';

            if ($this->type == 'all') {
                $userInfoName = get_the_author_meta('display_name', $var->post_author);
                $lastPublish = $this->getLastPublish($var->ID);
                $lastPublish = ($lastPublish != false) ? ' | ' . __('last shared on social media', 'blog2social') . ' ' . B2S_Util::getCustomDateFormat($lastPublish, substr(B2S_LANGUAGE, 0, 2)) : '';

                $this->postItem .= '<li class="list-group-item">
                                <div class="media">
                                    ' . ((isset($var->favorites_blog_user_id) && $var->favorites_blog_user_id != NULL && $var->favorites_blog_user_id == B2S_PLUGIN_BLOG_USER_ID) ?
                        '<i class="glyphicon glyphicon-star pull-left b2sFavoriteStar" data-post-id="' . $var->ID . '" data-is-favorite="1"></i>' :
                        '<i class="glyphicon glyphicon-star-empty pull-left b2sFavoriteStar" data-post-id="' . $var->ID . '" data-is-favorite="0"></i>'
                        ) . '
                                    <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                        <div class="media-body">
                                                <strong><a target="_blank" href="' . esc_url(get_permalink($var->ID)) . '">' . esc_html($postTitle) . '</a></strong>
                                            <span class="pull-right b2s-publish-btn">
                                                <a class="btn btn-primary btn-sm publishPostBtn" href="admin.php?page=blog2social-ship&postId=' . esc_attr($var->ID) . (!empty($selectSchedDate) ? '&schedDate=' . $selectSchedDate : '') . '">' . esc_html__('Share on Social Media', 'blog2social') . '</a>
                                            </span>' .
                        ((isset($var->draft_blog_user_id) && $var->draft_blog_user_id != NULL && $var->draft_blog_user_id == B2S_PLUGIN_BLOG_USER_ID) ?
                        '<span class="pull-right b2s-publish-btn">
                                                    <a class="btn btn-default btn-sm loadDraftBtn" href="admin.php?page=blog2social-ship&postId=' . esc_attr($var->ID) . (!empty($selectSchedDate) ? '&schedDate=' . $selectSchedDate : '') . '&type=draft">' . esc_html__('load Draft', 'blog2social') . '</a>
                                                </span>' : '')
                        . '<p class="info hidden-xs">#' . esc_html($var->ID . ' | ' . __('Author', 'blog2social')) . ' <a href="' . esc_url(get_author_posts_url($var->post_author)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a> | ' . esc_html($postStatus[trim(strtolower($var->post_status))] . ' ' . __('on blog', 'blog2social')) . ': ' . esc_html(B2S_Util::getCustomDateFormat($var->post_date, substr(B2S_LANGUAGE, 0, 2)) . $lastPublish) . '</p>
                                        </div>
                                    </div>
                                </li>';
            }

            if ($this->type == 'video') {
                $countPublish = $this->getPostCount($var->ID);
                $lastPublish = ((int) $countPublish > 0) ? $this->getLastPost($var->ID) : '';
                $userInfoName = (!empty($lastPublish)) ? get_the_author_meta('display_name', $lastPublish['user']) : '';
                $videoMeta = wp_read_video_metadata(get_attached_file($var->ID));
                $videoUrl = wp_get_attachment_url($var->ID);

                $notice = '';
                $uploadDetails = '<button class="b2s-show-video-uploads btn btn-primary" disabled><i class="glyphicon glyphicon-ban-circle"></i> ' . esc_html__('Details', 'blog2social') . '</button>';
                $videoDetails = sprintf(esc_html__('uploaded by %s on %s', 'blog2social'), get_the_author_meta('display_name', $var->post_author), B2S_Util::getCustomDateFormat($var->post_date, substr(B2S_LANGUAGE, 0, 2)));
                $shareVideoBtn = '<button class="b2s-share-video-file btn btn-primary" disabled><i class="glyphicon glyphicon-ban-circle"></i> ' . esc_html__('Share on video networks', 'blog2social') . '</button>';

                if ($videoAddonDetails !== false) {
                    if ((int) $countPublish > 0) {
                        $videoDetails = '<a class="b2sDetailsPublishPostTriggerLink" href="#"><span class="b2s-publish-count" data-attachment-id="' . esc_attr($var->ID) . '">' . esc_html($countPublish) . '</span> ' . esc_html__('shared video posts', 'blog2social') . '</a>,  ' . sprintf(esc_html__('latest upload by %s on %s', 'blog2social'), '<a href="' . esc_url(get_author_posts_url($lastPublish['user'])) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a>', esc_html(B2S_Util::getCustomDateFormat($lastPublish['date'], substr(B2S_LANGUAGE, 0, 2)))) . ' | ' . $videoDetails;
                        $uploadDetails = '<button class="b2s-show-video-uploads btn btn-primary" data-file-url="' . esc_attr($videoUrl) . '" data-attachment-id="' . esc_attr($var->ID) . '"><i class="glyphicon glyphicon-chevron-down"></i> ' . esc_html__('Details', 'blog2social') . '</button>';
                    }
                    if (isset($videoAddonDetails['volume_open']) && ($videoAddonDetails['volume_open'] >= round($videoMeta['filesize'] / 1024))) {
                        $shareVideoBtn = '<a class="b2s-share-video-file btn btn-primary" href="admin.php?page=blog2social-ship&isVideo=1&postId=' . esc_attr($var->ID) . '" data-file-url="' . esc_attr($var->guid) . '" data-attachment-id="' . esc_attr($var->ID) . '">' . esc_html__('Share on video networks', 'blog2social') . '</a>';
                    } else {
                        $notice = '<span class="glyphicon glyphicon-warning-sign"></span> <b>' . esc_html__('Video size exceeds your data volume to share on networks', 'blog2social') . '</b></br>';
                    }
                }

                $this->postItem .= '<li class="list-group-item ' . (!empty($notice) ? 'b2s-label-danger-border-left' : '') . '" data-attachment-id="' . esc_attr($var->ID) . '">
                            <div class="media">
                                <img class="post-img-5 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/video-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                                <div class="media-body">
                                                    <div class="pull-left media-nav">
                                                            ' . $notice . '
                                                            <strong><a target="_blank" href="' . esc_url($var->guid) . '">' . $var->post_title . '</a></strong>
                                                            <span class="info hidden-xs">(' . esc_html__('Format', 'blog2social') . ': ' . esc_html($var->post_mime_type) . ', ' . esc_html__('Size', 'blog2social') . ': ' . esc_html(size_format($videoMeta['filesize'], 2)) . ', ' . esc_html__('Length', 'blog2social') . ':' . esc_html($videoMeta['length']) . esc_html__('s', 'blog2social') . ')</span>    
                                                        <span class="pull-right">
                                                        ' . $shareVideoBtn . ' ' . $uploadDetails . '
                                                        </span>
                                                        <p class="info hidden-xs">
                                                           ' . $videoDetails . ' 
                                                        </div>
                                                      <div class="pull-left">
                                                        <div class="b2s-post-video-upload-area" data-attachment-id="' . esc_attr($var->ID) . '"></div>
                                                    </div>
                                                </div>                                     
                            </div>
                        </li>';
            }

            if ($this->type == 'publish' || $this->type == 'notice') {
                $countPublish = $this->getPostCount($var->ID);
                $lastPublish = $this->getLastPost($var->ID);
                $userInfoName = get_the_author_meta('display_name', $lastPublish['user']);
                $addCurationFormat = '';
                $isVideo = '';
                $sharedText = esc_html__('shared social media posts', 'blog2social');
                if (strtolower($var->post_type) == 'b2s_ex_post') {
                    $guid = get_the_guid($var->ID);
                    $addCurationFormat = ((stripos($guid, 'b2s_ex_post') != false) ? '&b2sPostType=ex&postFormat=1' : '&b2sPostType=ex&postFormat=0');
                }
                if (strtolower($var->post_type) == 'attachment') {
                    $sharedText = esc_html__('shared video posts', 'blog2social');
                    $isVideo = '&isVideo=1';
                }

                $this->postItem .= '<li class="list-group-item">
                                        <div class="media">
                                            <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                                <div class="media-body">
                                                    <div class="pull-left media-nav">
                                                            <strong><a target="_blank" href="' . esc_url(get_permalink($var->ID)) . '">' . esc_html($postTitle) . '</a></strong>' . $curated . '
                                                        <span class="pull-right">
                                                        <a class="btn btn-primary hidden-xs btn-sm' . (($this->type == 'notice') ? ' b2s-repost-multi' : '') . '" href="admin.php?page=blog2social-ship&postId=' . esc_attr($var->ID) . $addCurationFormat . $isVideo . '" data-blog-post-id="' . esc_attr($var->ID) . '">' . esc_html__('Re-share this post', 'blog2social') . '</a>
                                                            <button type="button" class="btn btn-primary btn-sm b2sDetailsPublishPostBtn" data-search-date="' . esc_attr($this->searchShowByDate) . '" data-post-id="' . esc_attr($var->ID) . '"><i class="glyphicon glyphicon-chevron-down"></i> ' . esc_html__('Details', 'blog2social') . '</button>
                                                        </span>
                                                        <p class="info hidden-xs"><a class="b2sDetailsPublishPostTriggerLink" href="#"><span class="b2s-publish-count" data-post-id="' . esc_attr($var->ID) . '">' . esc_html($countPublish) . '</span> ' . $sharedText . '</a> | ' . sprintf(esc_html__('latest share by %s', 'blog2social'), '<a href="' . esc_url(get_author_posts_url($lastPublish['user'])) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a>') . ' ' . esc_html(B2S_Util::getCustomDateFormat($lastPublish['date'], substr(B2S_LANGUAGE, 0, 2))) . '</p>
                                                    </div>
                                                    <div class="pull-left">
                                                        <div class="b2s-post-publish-area" data-post-id="' . esc_attr($var->ID) . '"></div>
                                                    </div>
                                                </div>
                                         </div>
                                    </li>';
            }

            if ($this->type == 'sched') {
                $schedPublish = $this->getPostCount($var->ID);
                $nextSched = $this->getLastPost($var->ID);
                $userInfoName = get_the_author_meta('display_name', $nextSched['user']);

                $this->postItem .= '<li class="list-group-item">
                                        <div class="media">
                                            <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                                <div class="media-body">
                                                    <div class="pull-left media-head">
                                                            <strong><a target="_blank" href="' . esc_url(get_permalink($var->ID)) . '">' . esc_html($postTitle) . '</a></strong>' . $curated . '
                                                        <span class="pull-right">
                                                            <button type="button" class="btn btn-primary btn-sm b2sDetailsSchedPostBtn" data-search-network="' . esc_attr($this->searchShowByNetwork) . '" data-search-date="' . esc_attr($this->searchShowByDate) . '" data-post-id="' . esc_attr($var->ID) . '"><i class="glyphicon glyphicon-chevron-down"></i> ' . esc_html__('Details', 'blog2social') . '</button>
                                                        </span>
                                                        <p class="info hidden-xs"><a class="b2sDetailsSchedPostTriggerLink" href="#"><span class="b2s-sched-count" data-post-id="' . esc_attr($var->ID) . '">' . esc_html($schedPublish) . '</span> ' . esc_html__('scheduled social media posts', 'blog2social') . '</a> | ' . sprintf(esc_html__('next share by %s', 'blog2social'), '<a href="' . esc_url(get_author_posts_url($nextSched['user'])) . '">' . esc_html((!empty($userInfoName) ? esc_html($userInfoName) : '-')) . '</a>') . ' ' . esc_html(B2S_Util::getCustomDateFormat($nextSched['date'], substr(B2S_LANGUAGE, 0, 2))) . '</p>
                                                    </div>
                                                    <div class="pull-left">
                                                        <div class="b2s-post-sched-area" data-post-id="' . esc_attr($var->ID) . '"></div>
                                                 </div>
                                             </div>
                                         </div>
                                    </li>';
            }

            if ($this->type == 'approve') {
                $userInfoName = get_the_author_meta('display_name', $var->blog_user_id);
                $countApprove = $this->getPostCount($var->ID);
                $this->postItem .= '<li class="list-group-item">
                                        <div class="media">
                                            <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                                <div class="media-body">
                                                    <div class="pull-left media-head">
                                                            <strong><a target="_blank" href="' . esc_url(get_permalink($var->ID)) . '">' . esc_html($postTitle) . '</a></strong>
                                                        <span class="pull-right">
                                                            <button type="button" class="btn btn-primary btn-sm b2sDetailsApprovePostBtn" data-search-date="' . esc_attr($this->searchShowByDate) . '" data-post-id="' . esc_attr($var->ID) . '"><i class="glyphicon glyphicon-chevron-down"></i> ' . esc_html__('Details', 'blog2social') . '</button>
                                                        </span>
                                                        <p class="info hidden-xs"><a class="b2sDetailsApprovePostTriggerLink" href="#"><span class="b2s-approve-count" data-post-id="' . esc_attr($var->ID) . '">' . esc_html($countApprove) . '</span> ' . esc_html__('social media posts ready to be shared', 'blog2social') . '</a></p>
                                                    </div>
                                                    <div class="pull-left">
                                                        <div class="b2s-post-approve-area" data-post-id="' . esc_attr($var->ID) . '"></div>
                                                 </div>
                                             </div>
                                         </div>
                                    </li>';
            }

            if ($this->type == 'draft') {
                $browser = (get_post_meta($var->ID, "b2s_source", true) == "b2s_browser_extension") ? ' - <strong>' . esc_html__('via Browser-Extension', 'blog2social') . '</strong>' : '';
                $userInfoName = get_the_author_meta('display_name', $var->post_author);
                $lastPublish = $this->getLastPublish($var->ID);
                $lastPublish = ($lastPublish != false) ? ' | ' . __('last shared on social media', 'blog2social') . ' ' . B2S_Util::getCustomDateFormat($lastPublish, substr(B2S_LANGUAGE, 0, 2)) : '';
                $url = (!empty($browser)) ? get_post_meta($var->ID, "b2s_original_url", true) : $var->guid;
                $schedData = (!empty($var->draft_data)) ? http_build_query(unserialize($var->draft_data)) : '';

                $this->postItem .= '<li class="list-group-item b2s-list-cc-draft" data-blog-post-id="' . esc_attr($var->ID) . '">
                                <div class="media">
                                    <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                        <div class="media-body">
                                                <strong><a target="_blank" href="' . esc_url($url) . '">' . esc_html($postTitle) . '</a></strong>' . $browser . '
                                            <span class="pull-right padding-left-5 b2s-publish-btn">' .
                        (empty($url) || (stripos($url, '/b2s_ex_post/') != false) ?
                        '<a class="btn btn-primary btn-sm publishPostBtn" href="admin.php?page=blog2social-curation&postId=' . esc_attr($var->ID) . '&comment=' . urlencode($var->post_content) . (((int) $var->meta_value > 0) ? '&image_id=' . urlencode($var->meta_value) . '&image_url=' . urlencode(wp_get_attachment_url($var->meta_value)) : '') . '&' . $schedData . '">' . esc_html__('Share on Social Media', 'blog2social') . '</a>' :
                        '<a class="btn btn-primary btn-sm publishPostBtn" href="admin.php?page=blog2social-curation&postId=' . esc_attr($var->ID) . '&url=' . urlencode($url) . '&title=' . urlencode($var->post_title) . '&comment=' . urlencode($var->post_content) . '&' . $schedData . '">' . esc_html__('Share on Social Media', 'blog2social') . '</a>'
                        )
                        . '</span>
                                            <span class="pull-right">
                                                <button class="btn btn-default btn-sm deleteCcDraftBtn" data-blog-post-id="' . esc_attr($var->ID) . '"><span class="glyphicon glyphicon glyphicon-trash "></span> ' . esc_html__('delete', 'blog2social') . '</button>
                                            </span>
                                            <p class="info hidden-xs">#' . esc_html($var->ID . ' | ' . __('Author', 'blog2social')) . ' <a href="' . esc_url(get_author_posts_url($var->post_author)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a> | ' . esc_html__('saved', 'blog2social') . ': ' . esc_html(B2S_Util::getCustomDateFormat($var->post_date, substr(B2S_LANGUAGE, 0, 2)) . $lastPublish) . '</p>
                                        </div>
                                    </div>
                                </li>';
            }

            if ($this->type == 'draft-post') {
                $userInfoName = get_the_author_meta('display_name', $var->post_author);

                $this->postItem .= '<li class="list-group-item b2s-draft-list-entry" data-b2s-draft-id="' . esc_attr($var->draft_id) . '">
                                <div class="media">
                                    <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                    <div class="media-body">
                                            <strong><a target="_blank" href="' . esc_url(get_permalink($var->post_id)) . '">' . esc_html($postTitle) . '</a></strong>
                                        <span class="pull-right b2s-publish-btn">
                                            <a class="btn btn-primary btn-sm publishPostBtn" href="admin.php?page=blog2social-ship' . (($postType == 'video') ? '&isVideo=1' : '') . '&postId=' . esc_attr($var->ID) . '&type=draft">' . esc_html__('Share on Social Media', 'blog2social') . '</a>
                                        </span>
                                        <span class="pull-right">
                                            <a class="btn btn-default btn-sm deleteDraftBtn" data-b2s-draft-id="' . esc_attr($var->draft_id) . '">' . esc_html__('delete', 'blog2social') . '</a>
                                        </span>
                                        <p class="info hidden-xs">#' . esc_html($var->ID . ' | ' . __('Author', 'blog2social')) . ' <a href="' . esc_url(get_author_posts_url($var->post_author)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a> | ' . esc_html__('last saved', 'blog2social') . ': ' . esc_html(B2S_Util::getCustomDateFormat($var->last_save_date, substr(B2S_LANGUAGE, 0, 2))) . '</p>
                                    </div>
                                </div>
                            </li>';
            }

            if ($this->type == 'favorites') {
                $userInfoName = get_the_author_meta('display_name', $var->post_author);
                $lastPublish = $this->getLastPublish($var->ID);
                $lastPublish = ($lastPublish != false) ? ' | ' . __('last shared on social media', 'blog2social') . ' ' . B2S_Util::getCustomDateFormat($lastPublish, substr(B2S_LANGUAGE, 0, 2)) : '';

                $this->postItem .= '<li class="list-group-item b2s-favorite-list-entry" data-post-id="' . esc_attr($var->ID) . '">
                                <div class="media">
                                    <i class="glyphicon glyphicon-star pull-left b2sFavoriteStar" data-post-id="' . esc_attr($var->ID) . '" data-is-favorite="1"></i>
                                    <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                    <div class="media-body">
                                            <strong><a target="_blank" href="' . esc_url(get_permalink($var->post_id)) . '">' . esc_html($postTitle) . '</a></strong>
                                        <span class="pull-right b2s-publish-btn">
                                            <a class="btn btn-primary btn-sm publishPostBtn" href="admin.php?page=blog2social-ship&postId=' . esc_attr($var->ID) . '">' . esc_html__('Share on Social Media', 'blog2social') . '</a>
                                        </span>' .
                        ((isset($var->draft_blog_user_id) && $var->draft_blog_user_id != NULL && $var->draft_blog_user_id == B2S_PLUGIN_BLOG_USER_ID) ?
                        '<span class="pull-right b2s-publish-btn">
                                            <a class="btn btn-default btn-sm loadDraftBtn" href="admin.php?page=blog2social-ship&postId=' . esc_attr($var->ID) . (!empty($selectSchedDate) ? '&schedDate=' . $selectSchedDate : '') . '&type=draft">' . esc_html__('load Draft', 'blog2social') . '</a>
                                        </span>' : '')
                        . '<p class="info hidden-xs">#' . esc_html($var->ID . ' | ' . __('Author', 'blog2social')) . ' <a href="' . esc_url(get_author_posts_url($var->post_author)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a> | ' . esc_html($postStatus[trim(strtolower($var->post_status))] . ' ' . __('on blog', 'blog2social')) . ': ' . esc_html(B2S_Util::getCustomDateFormat($var->post_date, substr(B2S_LANGUAGE, 0, 2)) . $lastPublish) . '</p>
                                    </div>
                                </div>
                            </li>';
            }

            if ($this->type == 'repost') {
                $schedPublish = $this->getPostCount($var->ID);
                $nextSched = $this->getLastPost($var->ID);
                $userInfoName = get_the_author_meta('display_name', $nextSched['user']);

                $this->postItem .= '<li class="list-group-item" data-type="post">
                                        <div class="media">
                                            <input class="pull-left checkbox-item b2s-re-post-queue-checkbox" data-blog-post-id="' . esc_attr($var->ID) . '" name="selected-checkbox-item" value="' . esc_attr($var->ID) . '" type="checkbox">
                                            <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                            <div class="media-body">
                                                <div class="media-head">
                                                    <span class="pull-right">
                                                        <button type="button" class="btn btn-primary btn-sm b2sDetailsSchedPostBtn" data-post-id="' . esc_attr($var->ID) . '"><i class="glyphicon glyphicon-chevron-down"></i> ' . esc_html__('Details', 'blog2social') . '</button>
                                                    </span>
                                                    <strong><a target="_blank" href="' . esc_url(get_permalink($var->ID)) . '">' . esc_html($postTitle) . '</a></strong>' . $curated . '
                                                    <p class="info hidden-xs"><a data-post-id="' . esc_attr($var->ID) . '" class="b2sDetailsSchedPostTriggerLink" href="#"><span class="b2s-sched-count" data-post-id="' . esc_attr($var->ID) . '">' . esc_html($schedPublish) . '</span> ' . esc_html__('scheduled social media posts', 'blog2social') . '</a> | ' . sprintf(esc_html__('next share by %s', 'blog2social'), '<a href="' . esc_url(get_author_posts_url($nextSched['user'])) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a>') . ' ' . esc_html(B2S_Util::getCustomDateFormat($nextSched['date'], substr(B2S_LANGUAGE, 0, 2))) . '</p>
                                                </div>
                                                <div class="pull-left">
                                                    <div class="b2s-post-sched-area" data-post-id="' . esc_attr($var->ID) . '"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>';
            }
        }
        return html_entity_decode($this->postItem, ENT_COMPAT, 'UTF-8');
    }

    private function getPostCount($post_id = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND posts.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
            $addLeftJoin = ((int) $this->searchUserAuthId != 0) ? ' LEFT JOIN ' . $wpdb->prefix . 'b2s_posts_network_details details ON details.`id` = posts.`network_details_id` ' : '';
            $addLeftJoinWhere = ((int) $this->searchUserAuthId != 0) ? ' details.`network_auth_id` =' . $this->searchUserAuthId . ' AND ' : '';
            $addLeftJoinNetwork = ((int) $this->searchShowByNetwork != 0 && empty($addLeftJoin)) ? ' LEFT JOIN ' . $wpdb->prefix . 'b2s_posts_network_details details ON details.`id` = posts.`network_details_id` ' : '';
            $addLeftJoinWhereNetwork = ((int) $this->searchShowByNetwork != 0) ? ' details.`network_id` =' . $this->searchShowByNetwork . ' AND ' : '';

            if ($this->type == 'approve') {
                $addSearchShowByDate = (!empty($this->searchShowByDate)) ? " (DATE_FORMAT(posts.publish_date,'%Y-%m-%d') = '" . $this->searchShowByDate . "' OR DATE_FORMAT(posts.sched_date,'%Y-%m-%d') = '" . $this->searchShowByDate . "') AND " : '';
                $where = ' posts.`post_for_approve` = 1 AND (posts.`publish_date` != "0000-00-00 00:00:00" OR posts.`sched_date_utc` <= "' . gmdate('Y-m-d H:i:s') . '")';
            } else if ($this->type == 'repost') {
                $addSearchShowByDate = '';
                $where = ' posts.`publish_date` = "0000-00-00 00:00:00" AND posts.`sched_type` = 5 AND posts.`hide` = 0';
            } else {
                $addSearchShowByDate = (!empty($this->searchShowByDate)) ? (($this->type == 'publish' || $this->type == 'notice') ? " AND DATE_FORMAT(posts.publish_date,'%Y-%m-%d') = '" . $this->searchShowByDate . "' " : " AND DATE_FORMAT(posts.sched_date,'%Y-%m-%d') = '" . $this->searchShowByDate . "' ") : '';
                $addWhere = ($this->type == 'notice') ? ' AND posts.`publish_error_code` != "" ' : ' AND posts.`publish_error_code` = "" ';
                $addWhere .= ((int) $this->searchPostSharedById > 0) ? ' AND posts.`blog_user_id` = ' . $this->searchPostSharedById . ' ' : ' ';
                $where = ($this->type == 'publish' || $this->type == 'notice' || $this->type == 'video') ? " (posts.`sched_date` = '0000-00-00 00:00:00' OR (posts.`sched_type` = 3 AND posts.`publish_date` != '0000-00-00 00:00:00')) AND posts.`post_for_approve`= 0 " . $addWhere : " (posts.`sched_type` != 3 OR (posts.`sched_type` = 3 AND posts.`publish_date` = '0000-00-00 00:00:00')) AND posts.`publish_date` = '0000-00-00 00:00:00' AND ((posts.`sched_date_utc` != '0000-00-00 00:00:00' AND posts.`post_for_approve` = 0) OR (posts.`sched_date_utc` >= '" . gmdate('Y-m-d H:i:s') . "' AND posts.`post_for_approve` = 1)) ";
            }
            $sqlPostsTotal = "SELECT COUNT(posts.`post_id`) FROM `{$wpdb->prefix}b2s_posts` posts $addLeftJoin $addLeftJoinNetwork WHERE $addLeftJoinWhere $addLeftJoinWhereNetwork $where $addNotAdmin $addSearchShowByDate AND posts.`hide` = 0 AND posts.`post_id` = " . $post_id;
            return $wpdb->get_var($sqlPostsTotal);
        }
        return 0;
    }

    private function getLastPost($post_id = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(" AND `{$wpdb->prefix}b2s_posts`.`blog_user_id` = %d", B2S_PLUGIN_BLOG_USER_ID) : '';
            $order = ($this->type == 'publish' || $this->type == 'notice' || $this->type == 'video') ? " `publish_date` DESC" : " `sched_date` ASC ";
            $addWhere = ($this->type == 'notice') ? ' AND `publish_error_code` != "" ' : ' AND `publish_error_code` = "" ';
            $where = ($this->type == 'publish' || $this->type == 'notice' || $this->type == 'video') ? " `post_for_approve`= 0 AND (`sched_date`= '0000-00-00 00:00:00' OR (`sched_type` = 3 AND `publish_date` != '0000-00-00 00:00:00')) " . $addWhere : " (`sched_type` != 3 OR (`sched_type` = 3 AND `publish_date` = '0000-00-00 00:00:00')) AND ((`sched_date_utc` != '0000-00-00 00:00:00' AND `post_for_approve` = 0) OR (`sched_date_utc` >= '" . gmdate('Y-m-d H:i:s') . "' AND `post_for_approve` = 1)) AND `publish_date` = '0000-00-00 00:00:00'";
            $where .= ($this->type == 'repost') ? ' AND `sched_type` = 5 ' : '';
            $fields = ($this->type == 'publish' || $this->type == 'notice' || $this->type == 'video') ? "publish_date" : "sched_date";
            $sqlLast = "SELECT $fields, blog_user_id FROM `{$wpdb->prefix}b2s_posts` WHERE $where $addNotAdmin AND `hide` = 0 AND `post_id` = " . $post_id . " ORDER BY $order LIMIT 1";
            $result = $wpdb->get_results($sqlLast);
            if (!empty($result)) {
                $date = date('Y-m-d H:i:s');
                if (($this->type == 'publish' || $this->type == 'notice' || $this->type == 'video') && isset($result[0]->publish_date)) {
                    $date = $result[0]->publish_date;
                } elseif (isset($result[0]->sched_date)) {
                    $date = $result[0]->sched_date;
                }
                $user = (isset($result[0]->blog_user_id) && (int) $result[0]->blog_user_id > 0) ? (int) $result[0]->blog_user_id : 0;
                return array('date' => $date, 'user' => $user);
            }
        }
        return array('date' => date('Y-m-d H:i:s'), 'user' => 0);
    }

    private function getLastPublish($post_id = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(" AND `{$wpdb->prefix}b2s_posts`.`blog_user_id` = %d", B2S_PLUGIN_BLOG_USER_ID) : '';
            $order = "`publish_date` DESC";
            $where = "(`sched_date`= '0000-00-00 00:00:00' OR (`sched_type` = 3 AND `publish_date` != '0000-00-00 00:00:00')) ";
            $fields = "publish_date";
            $sqlLast = "SELECT $fields FROM `{$wpdb->prefix}b2s_posts` WHERE $where $addNotAdmin AND `hide` = 0 AND `post_for_approve`= 0 AND `post_id` = " . $post_id . " ORDER BY $order LIMIT 1";
            $result = $wpdb->get_results($sqlLast);
            if (!empty($result) && isset($result[0]->publish_date)) {
                return $result[0]->publish_date;
            }
        }
        return false;
    }

    public function getCalendarSchedDate() {
        if ((int) $this->postTotal > 0) {
            if (is_array($this->postCalendarSchedDates) && !empty($this->postCalendarSchedDates)) {
                return $this->postCalendarSchedDates;
            }
        }
        return 0;
    }

    public function getPaginationHtml() {
        if ((int) $this->postTotal > 0) {
            $last = ceil($this->postTotal / $this->results_per_page);
            $start = (($this->currentPage - $this->postPaginationLinks ) > 0 ) ? $this->currentPage - $this->postPaginationLinks : 1;
            $end = (( $this->currentPage + $this->postPaginationLinks ) < $last ) ? $this->currentPage + $this->postPaginationLinks : $last;
            $this->postPagination = '<ul class="pagination">';
            $class = ( $this->currentPage == 1 ) ? "disabled" : "";
            $linkpre = ( $this->currentPage == 1 ) ? $this->currentPage : ( $this->currentPage - 1);
            $this->postPagination .= '<li class="' . esc_attr($class) . '"><a class="b2s-pagination-btn" data-page="' . esc_attr($linkpre) . '" href="#">&laquo;</a></li>';
            if ($start > 1) {
                $this->postPagination .= '<li><a class="b2s-pagination-btn" data-page="1" href="#">1</a></li>';
                $this->postPagination .= '<li class="disabled"><span>...</span></li>';
            }
            for ($i = $start; $i <= $end; $i++) {
                $class = ( $this->currentPage == $i ) ? "active" : "";
                $this->postPagination .= '<li class="' . esc_attr($class) . '"><a class="b2s-pagination-btn" data-page="' . esc_attr($i) . '" href="#">' . esc_html($i) . '</a></li>';
            }
            if ($end < $last) {
                $this->postPagination .= '<li class="disabled"><span>...</span></li>';
                $this->postPagination .= '<li><a class="b2s-pagination-btn" data-page="' . esc_attr($last) . '" href="#">' . esc_html($last) . '</a></li>';
            }
            $class = ( $this->currentPage == $last ) ? "disabled" : "";
            $linkpast = ( $this->currentPage == $last ) ? $this->currentPage : ( $this->currentPage + 1 );
            $this->postPagination .= '<li class="' . esc_attr($class) . '"><a class="b2s-pagination-btn" data-page="' . esc_attr($linkpast) . '" href="#">&raquo;</a></li>';
            $this->postPagination .= '</ul>';
        }
        return $this->postPagination;
    }

    public function getPublishPostDataHtml($post_id = 0, $type = 'publish', $showByDate = '', $sharedByUser = 0, $sharedOnNetwork = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdminPosts = (!B2S_PLUGIN_ADMIN) ? (" AND `{$wpdb->prefix}b2s_posts`.blog_user_id =" . B2S_PLUGIN_BLOG_USER_ID) : '';
            $addSharedByUser = ($sharedByUser > 0) ? (" AND `{$wpdb->prefix}b2s_posts`.blog_user_id =" . $sharedByUser) : '';
            $addSharedOnNetwork = ($sharedOnNetwork > 0) ? (' AND network_id =' . $sharedOnNetwork) : '';
            $addSearchShowByDate = (!empty($showByDate)) ? " AND DATE_FORMAT(`{$wpdb->prefix}b2s_posts`.`publish_date`,'%%Y-%%m-%%d') = '" . $showByDate . "' " : '';
            $addWhere = ($type == 'notice') ? ' AND `' . $wpdb->prefix . 'b2s_posts`.`publish_error_code` != "" ' : ' AND `' . $wpdb->prefix . 'b2s_posts`.`publish_error_code` = "" ';
            $sqlData = $wpdb->prepare("SELECT `{$wpdb->prefix}b2s_posts`.`id`,`{$wpdb->prefix}b2s_posts`.`blog_user_id`, `sched_date`,`publish_date`,`publish_link`,`sched_type`,`publish_error_code`,`sched_details_id`,`hook_action`,`post_format`,`{$wpdb->prefix}b2s_posts_sched_details`.`sched_data`,`{$wpdb->prefix}b2s_posts_network_details`.`network_id`,`{$wpdb->prefix}b2s_posts_network_details`.`network_type`, `{$wpdb->prefix}b2s_posts_network_details`.`network_auth_id`, `{$wpdb->prefix}b2s_posts_network_details`.`network_display_name`, `{$wpdb->prefix}b2s_posts_insights`.`insight`, `{$wpdb->prefix}b2s_posts_insights`.`active` as insightsActive FROM `{$wpdb->prefix}b2s_posts` LEFT JOIN `{$wpdb->prefix}b2s_posts_network_details` ON `{$wpdb->prefix}b2s_posts`.`network_details_id` = `{$wpdb->prefix}b2s_posts_network_details`.`id` LEFT JOIN `{$wpdb->prefix}b2s_posts_sched_details` ON `{$wpdb->prefix}b2s_posts`.`sched_details_id` = `{$wpdb->prefix}b2s_posts_sched_details`.`id` LEFT JOIN `{$wpdb->prefix}b2s_posts_insights` ON `{$wpdb->prefix}b2s_posts`.`id` = `{$wpdb->prefix}b2s_posts_insights`.`b2s_posts_id` WHERE `{$wpdb->prefix}b2s_posts`.`hide` = 0 AND `{$wpdb->prefix}b2s_posts`.`post_for_approve`= 0  AND (`{$wpdb->prefix}b2s_posts`.`sched_date` = '0000-00-00 00:00:00' OR (`{$wpdb->prefix}b2s_posts`.`sched_type` = 3 AND `{$wpdb->prefix}b2s_posts`.`publish_date` != '0000-00-00 00:00:00')) $addWhere $addNotAdminPosts $addSharedByUser $addSharedOnNetwork $addSearchShowByDate AND `{$wpdb->prefix}b2s_posts`.`post_id` = %d ORDER BY `{$wpdb->prefix}b2s_posts`.`publish_date` DESC", $post_id);
            $result = $wpdb->get_results($sqlData);
            $specialPostingData = array(3 => esc_html__('Auto-Posting', 'blog2social'), 4 => esc_html__('Retweet', 'blog2social'), 5 => esc_html__('Re-Share', 'blog2social'));
            $metricsDoneB2SPostsIds = array();
            if (!empty($result) && is_array($result)) {
                $networkType = unserialize(B2S_PLUGIN_NETWORK_TYPE);
                $networkName = unserialize(B2S_PLUGIN_NETWORK);
                $networkErrorCode = unserialize(B2S_PLUGIN_NETWORK_ERROR);
                $content = '<div class="row"><div class="col-md-12"><ul class="list-group">';
                $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label"><input class="checkbox-all" data-blog-post-id="' . esc_attr($post_id) . '" name="selected-checkbox-all" value="" type="checkbox"> ' . esc_html__('select all', 'blog2social') . '</label></li>';
                foreach ($result as $var) {
                    if ($type == 'metrics') {
                        if (in_array($var->id, $metricsDoneB2SPostsIds)) {
                            continue;
                        }
                        if ($var->insight == null || empty($var->insight) || $var->insightsActive == 0) {
                            continue;
                        }
                        $postInsights = json_decode($var->insight, true);
                        if ($postInsights == false || !isset($postInsights['insights']) || empty($postInsights['insights'])) {
                            continue;
                        }
                        $metricsDoneB2SPostsIds[] = $var->id;
                    }

                    $addPostFormat = '';
                    $isVideo = '';
                    if (isset($var->post_format) && $var->post_format != null && (int) $var->post_format >= 0) {
                        $addPostFormat = esc_html__('post format', 'blog2social') . ': ';
                        if ((int) $var->post_format == 0) {
                            $addPostFormat .= esc_html__('Link Post', 'blog2social');
                        } else if ((int) $var->post_format == 1) {
                            $addPostFormat .= esc_html__('Image Post', 'blog2social');
                        } else {
                            $addPostFormat .= esc_html__('Video Post', 'blog2social');
                            $isVideo = '&isVideo=1';
                        }
                        $addPostFormat .= ' | ';
                    }


                    $specialPosting = (isset($var->sched_type) && isset($specialPostingData[$var->sched_type])) ? ' - <strong>' . esc_html($specialPostingData[$var->sched_type]) . '</strong>' : '';
                    $publishLink = (!empty($var->publish_link)) ? '<a target="_blank" href="' . esc_url($var->publish_link) . '">' . esc_html__('show', 'blog2social') . '</a> | ' : '';
                    $error = '';
                    if (!empty($var->publish_error_code)) {
                        $add = '';
                        //special case: reddit RATE_LIMIT
                        if ($var->network_id == 15 && $var->publish_error_code == 'RATE_LIMIT') {
                            $link = (strtolower(substr(B2S_LANGUAGE, 0, 2)) == 'de') ? 'https://www.blog2social.com/de/faq/content/9/115/de/reddit-du-hast-das-veroeffentlichungs_limit-mit-deinem-account-kurzzeitig-erreicht.html' : 'https://www.blog2social.com/en/faq/content/9/115/en/reddit-you-have-temporarily-reached-the-publication-limit-with-your-account.html';
                            $add = ' ' . esc_html__('Please see', 'blog2social') . ' <a target="_blank" href="' . esc_url($link) . '">' . esc_html__('FAQ', 'blog2social') . '</a>';
                        }
                        if ($var->network_id == 12 && $var->publish_error_code == 'DEFAULT') {
                            if ($var->network_type == 0) {
                                $networkError12 = sprintf(__('The post cannot be published due to changes on the Instagram interface. More information in the <a href="%s" target="_blank">Instagram guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_error_private')));
                            } else {
                                $networkError12 = sprintf(__('Your post could not be posted. More information in this <a href="%s" target="_blank">Instagram troubleshoot checklist</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_error_business')));
                            }
                            $error = '<span class="network-text-info text-danger hidden-xs"> <i class="glyphicon glyphicon-remove-circle glyphicon-danger"></i> ' . $networkError12 . $add . '</span>';
                        } else {
                            $errorCode = isset($networkErrorCode[trim($var->publish_error_code)]) ? $var->publish_error_code : 'DEFAULT';
                            $error = '<span class="network-text-info text-danger hidden-xs"> <i class="glyphicon glyphicon-remove-circle glyphicon-danger"></i> ' . $networkErrorCode[$errorCode] . $add . '</span>';
                        }
                    }
                    $publishDate = ($var->sched_date == "0000-00-00 00:00:00") ? B2S_Util::getCustomDateFormat($var->publish_date, substr(B2S_LANGUAGE, 0, 2)) : '';
                    $publishText = (empty($publishDate)) ? __('sharing in progress by %s', 'blog2social') : __('shared by %s', 'blog2social');
                    $userInfoName = get_the_author_meta('display_name', $var->blog_user_id);
                    $content .= ' <li class="list-group-item b2s-post-publish-area-li" data-post-id="' . esc_attr($var->id) . '">
                                    <div class="media">';

                    if (!empty($publishDate)) {
                        $content .= '<input class="checkboxes pull-left checkbox-item" data-blog-post-id="' . esc_attr($post_id) . '" name="selected-checkbox-item" value="' . esc_attr($var->id) . '" type="checkbox"' . (($type == 'notice') ? ' data-network-auth-id="' . $var->network_auth_id . '"' : '') . '>';
                    } else {
                        $content .= '<div class="checbox-item-empty"></div>';
                    }

                    if (!empty($var->publish_link)) {
                        $content .= '<a class="pull-left" target="_blank" href="' . esc_url($var->publish_link) . '"><img class="pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE)) . '" alt="posttype"></a>';
                    } else {
                        $content .= '<img class="pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE)) . '" alt="posttype">';
                    }

                    $content .= '<div class="media-body">
                                            <strong>' . esc_html($networkName[$var->network_id]) . '</strong> <span class="info">(' . esc_html($networkType[$var->network_type]) . esc_html((!empty($var->network_display_name) ? (': ' . $var->network_display_name) : '')) . ')</span> ' . $error . '
                                            <div class="info">' . $addPostFormat . sprintf(esc_html($publishText), '<a href="' . esc_url(get_author_posts_url($var->blog_user_id)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a>') . ' ' . esc_html($publishDate) . $specialPosting;

                    if ((B2S_PLUGIN_USER_VERSION >= 3 || (defined('B2S_PLUGIN_PERMISSION_INSIGHTS') && B2S_PLUGIN_PERMISSION_INSIGHTS == 1)) && (($var->network_id == 1 && $var->network_type == 1) || ($var->network_id == 2 && $var->network_type == 0)) && $var->insight !== null && !empty($var->insight)) {
                        $postInsights = json_decode($var->insight, true);
                        if ($postInsights !== false && isset($postInsights['insights']) && !empty($postInsights['insights']) && isset($postInsights['insights']['data'])) {
                            $currentPostInsights = $postInsights['insights']['data'];
                            if (!empty($currentPostInsights) && !empty($currentPostInsights['likes'])) {
                                end($currentPostInsights['likes']);
                                $key = key($currentPostInsights['likes']);
                                $content .= '<div class="pull-right">
                                <i class="glyphicon glyphicon-eye-open"></i><span class="b2s-insights-impressions-count" data-b2s-post-id="' . esc_attr($var->id) . '" style="margin-left: 4px; margin-right: 10px;">' . ((isset($currentPostInsights['impressions'][$key])) ? $currentPostInsights['impressions'][$key] : '0') . '</span>
                                        <i class="glyphicon glyphicon-thumbs-up"></i><span class="b2s-insights-likes-count" data-b2s-post-id="' . esc_attr($var->id) . '" style="margin-left: 4px; margin-right: 10px;">' . ((isset($currentPostInsights['likes'][$key])) ? $currentPostInsights['likes'][$key] : '0') . '</span>
                                        <i class="glyphicon glyphicon-refresh"></i><span class="b2s-insights-reshares-count" data-b2s-post-id="' . esc_attr($var->id) . '" style="margin-left: 4px; margin-right: 10px;">' . ((isset($currentPostInsights['reshares'][$key])) ? $currentPostInsights['reshares'][$key] : '0') . '</span>
                                        <i class="glyphicon glyphicon-comment"></i><span class="b2s-insights-linkclicks-count" data-b2s-post-id="' . esc_attr($var->id) . '" style="margin-left: 4px; margin-right: 10px;">' . ((isset($currentPostInsights['comments'][$key])) ? $currentPostInsights['comments'][$key] : '0') . '</span>';
                            } else {
                                $content .= '<div class="pull-right">
                                        <i class="glyphicon glyphicon-eye-open"></i><span class="b2s-insights-impressions-count" data-b2s-post-id="' . esc_attr($var->id) . '" style="margin-left: 4px; margin-right: 10px;">0</span>
                                        <i class="glyphicon glyphicon-thumbs-up"></i><span class="b2s-insights-likes-count" data-b2s-post-id="' . esc_attr($var->id) . '" style="margin-left: 4px; margin-right: 10px;">0</span>
                                        <i class="glyphicon glyphicon-refresh"></i><span class="b2s-insights-reshares-count" data-b2s-post-id="' . esc_attr($var->id) . '" style="margin-left: 4px; margin-right: 10px;">0</span>
                                        <i class="glyphicon glyphicon-comment"></i><span class="b2s-insights-linkclicks-count" data-b2s-post-id="' . esc_attr($var->id) . '" style="margin-left: 4px; margin-right: 10px;">0</span>';
                            }
                            if ($type !== 'metrics') {
                                $content .= '<a href="admin.php?page=blog2social-metrics" class="btn btn-success">Metrics Summary</a></div>';
                            }
                        }
                    }


                    $content .= '</div><p class="info">' . $publishLink;

                    if ((int) $var->hook_action == 0) {
                        $content .= (B2S_PLUGIN_USER_VERSION > 0) ? '<a href="#" class="b2s-post-publish-area-drop-btn" data-post-id="' . esc_attr($var->id) . '">' : '<a href="#" class="b2sPreFeatureModalBtn" data-title="' . esc_attr__('You want to delete a publish post entry?', 'blog2social') . '">';
                        $content .= esc_html__('delete from reporting', 'blog2social') . '</a> ';
                    }

                    if (!empty($error)) {
                        $content .= '| <a href="admin.php?page=blog2social-ship&postId=' . esc_attr($post_id) . '&network_auth_id=' . esc_attr($var->network_auth_id) . $isVideo . '">' . esc_html__('re-share', 'blog2social') . '</a>';
                    }

                    $content .= '</p>
                        </div>
                        </div>
                                </li>';
                }
                $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label-btn"><span class="glyphicon glyphicon glyphicon-trash "></span> ';
                $content .= B2S_PLUGIN_USER_VERSION > 0 ? '<a class="checkbox-post-publish-all-btn" data-blog-post-id="' . esc_attr($post_id) . '" href="#">' : '<a href="#" class="b2sPreFeatureModalBtn" data-title="' . esc_attr__('You want to delete a publish post entry?', 'blog2social') . '">';
                $content .= esc_html__('delete from reporting', 'blog2social') . '</a></label></li>';
                $content .= '</ul></div></div>';
                return $content;
            }
        }
        return false;
    }

    public function getApprovePostDataHtml($post_id = 0, $showByDate = '') {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdminPosts = (!B2S_PLUGIN_ADMIN) ? (" AND `{$wpdb->prefix}b2s_posts`.blog_user_id =" . B2S_PLUGIN_BLOG_USER_ID) : '';
            $addSearchShowByDate = (!empty($showByDate)) ? " AND (DATE_FORMAT(`{$wpdb->prefix}b2s_posts`.`sched_date`,'%%Y-%%m-%%d') = '" . $showByDate . "' OR DATE_FORMAT(`{$wpdb->prefix}b2s_posts`.`publish_date`,'%%Y-%%m-%%d') = '" . $showByDate . "') " : '';
            $sqlData = $wpdb->prepare("SELECT `{$wpdb->prefix}b2s_posts`.`id`, `{$wpdb->prefix}b2s_posts`.`post_id`, `{$wpdb->prefix}b2s_posts`.`blog_user_id`, `{$wpdb->prefix}b2s_posts`.`sched_date`,`{$wpdb->prefix}b2s_posts`.`publish_date`,`{$wpdb->prefix}b2s_posts_network_details`.`network_id`,`{$wpdb->prefix}b2s_posts_network_details`.`network_type`, `{$wpdb->prefix}b2s_posts_network_details`.`network_auth_id`, `{$wpdb->prefix}b2s_posts_network_details`.`network_display_name`, `{$wpdb->prefix}b2s_posts_sched_details`.`sched_data` FROM `{$wpdb->prefix}b2s_posts` LEFT JOIN `{$wpdb->prefix}b2s_posts_network_details` ON `{$wpdb->prefix}b2s_posts`.`network_details_id` = `{$wpdb->prefix}b2s_posts_network_details`.`id` LEFT JOIN `{$wpdb->prefix}b2s_posts_sched_details` ON `{$wpdb->prefix}b2s_posts`.`sched_details_id` = `{$wpdb->prefix}b2s_posts_sched_details`.`id` WHERE `{$wpdb->prefix}b2s_posts`.`hide` = 0 AND `{$wpdb->prefix}b2s_posts`.`post_for_approve` = 1 AND (`{$wpdb->prefix}b2s_posts`.`publish_date` != '0000-00-00 00:00:00' OR `{$wpdb->prefix}b2s_posts`.`sched_date_utc` <= '" . gmdate('Y-m-d H:i:s') . "') $addNotAdminPosts $addSearchShowByDate AND `{$wpdb->prefix}b2s_posts`.`post_id` = %d ORDER BY `{$wpdb->prefix}b2s_posts`.`sched_date` DESC,`{$wpdb->prefix}b2s_posts`.`publish_date` DESC", $post_id);
            $result = $wpdb->get_results($sqlData);
            if (!empty($result) && is_array($result)) {
                $networkType = unserialize(B2S_PLUGIN_NETWORK_TYPE);
                $networkName = unserialize(B2S_PLUGIN_NETWORK);
                $content = '<div class="row"><div class="col-md-12"><ul class="list-group">';
                $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label"><input class="checkbox-all" data-blog-post-id="' . esc_attr($post_id) . '" name="selected-checkbox-all" value="" type="checkbox"> ' . esc_html__('select all', 'blog2social') . '</label></li>';
                foreach ($result as $var) {
                    $approveDate = ($var->sched_date == "0000-00-00 00:00:00") ? B2S_Util::getCustomDateFormat($var->publish_date, substr(B2S_LANGUAGE, 0, 2)) : B2S_Util::getCustomDateFormat($var->sched_date, substr(B2S_LANGUAGE, 0, 2));
                    $approveText = __('is waiting to shared by %s', 'blog2social');
                    $userInfoName = get_the_author_meta('display_name', $var->blog_user_id);
                    $content .= ' <li class="list-group-item b2s-post-approve-area-li" data-post-id="' . esc_attr($var->id) . '">
                                    <div class="media">';
                    $content .= '<input class="checkboxes pull-left checkbox-item" data-blog-post-id="' . esc_attr($post_id) . '" name="selected-checkbox-item" value="' . esc_attr($var->id) . '" type="checkbox">';
                    $content .= '<img class="pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE)) . '" alt="posttype">';
                    $content .= '<div class="media-body">
                                            <strong>' . esc_html($networkName[$var->network_id]) . '</strong> 
                                            <p class="info">' . esc_html($networkType[$var->network_type]) . esc_html((!empty($var->network_display_name) ? (': ' . $var->network_display_name) : '')) . ' | ' . sprintf(esc_html($approveText), '<a href="' . esc_url(get_author_posts_url($var->blog_user_id)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a>') . ' ' . esc_html($approveDate) . '</p>
                                            <p class="info">';

                    $data = array(
                        'token' => B2S_PLUGIN_TOKEN,
                        'blog_post_id' => $post_id,
                        'internal_post_id' => $var->id,
                        'network_id' => $var->network_id,
                        'network_auth_id' => $var->network_auth_id,
                        'network_type' => $var->network_type,
                        'language' => substr(B2S_LANGUAGE, 0, 2)
                    );

                    if ($var->sched_data != null && !empty($var->sched_data)) {
                        $schedData = unserialize($var->sched_data);
                        $data['post_format'] = isset($schedData['post_format']) ? (int) $schedData['post_format'] : 0;
                        $data['image_url'] = isset($schedData['image_url']) ? $schedData['image_url'] : "";
                        $data['content'] = isset($schedData['content']) ? $schedData['content'] : "";
                        $data['url'] = isset($schedData['url']) ? $schedData['url'] : "";
                    } else {
                        $postData = get_post($var->post_id);
                        $data['url'] = (get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid);
                    }
                    $content .= ' <a href="#" class="btn btn-primary btn-xs" onclick="wopApprove(\'' . esc_attr($post_id) . '\',\'' . (($var->network_id == 10) ? esc_attr($var->id) : 0) . '\',\'' . B2S_PLUGIN_API_ENDPOINT . 'instant/share.php?data=' . B2S_Util::urlsafe_base64_encode(json_encode($data)) . '\', \'Blog2Social\'); return false;" target="_blank">' . esc_html__('share', 'blog2social') . '</a>';

                    $content . '</p>
                                        </div>
                                    </div>
                                </li>';
                }
                $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label-btn"><span class="glyphicon glyphicon glyphicon-trash "></span> ';
                $content .= B2S_PLUGIN_USER_VERSION > 0 ? '<a class="checkbox-post-approve-all-btn" data-blog-post-id="' . esc_attr($post_id) . '" href="#">' : '<a href="#" class="b2sPreFeatureModalBtn" data-title="' . esc_html__('You want to delete your Social Media post?', 'blog2social') . '">';
                $content .= esc_html__('delete', 'blog2social') . '</a></label></li>';
                $content .= '</ul></div></div>';
                return $content;
            }
        }
        return false;
    }

    public function getSchedPostDataHtml($post_id = 0, $showByDate = '', $showByNetwork = 0, $userAuthId = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND `' . $wpdb->prefix . 'b2s_posts`.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
            $addSearchShowByDate = (!empty($showByDate)) ? " AND DATE_FORMAT(`{$wpdb->prefix}b2s_posts`.`sched_date`,'%%Y-%%m-%%d') = '" . $showByDate . "' " : '';
            $addSearchShowByNetwork = ((int) $showByNetwork > 0) ? " AND `{$wpdb->prefix}b2s_posts_network_details`.`network_id` = '" . $showByNetwork . "' " : '';
            $addSearchUserAuthId = ($userAuthId != 0) ? " AND `{$wpdb->prefix}b2s_posts_network_details`.`network_auth_id` =" . $userAuthId . " " : '';

            $addSearchRepost = '';
            if ($this->type == 'repost') {
                $addSearchRepost = ' AND `' . $wpdb->prefix . 'b2s_posts`.`sched_type` = 5 ';
            }
            $select = "SELECT `{$wpdb->prefix}b2s_posts`.`id`, `{$wpdb->prefix}b2s_posts`.`post_id`,`blog_user_id`,`last_edit_blog_user_id`,`v2_id`, `post_format`,`sched_date`, `sched_date_utc`, `sched_type`, `relay_primary_post_id`, `{$wpdb->prefix}b2s_posts_network_details`.`network_id`,`{$wpdb->prefix}b2s_posts_network_details`.`network_auth_id`,`{$wpdb->prefix}b2s_posts_network_details`.`network_type`,`{$wpdb->prefix}b2s_posts_network_details`.`network_display_name`,`{$wpdb->prefix}b2s_posts_sched_details`.`sched_data` ";
            $from = "FROM `{$wpdb->prefix}b2s_posts` 
                LEFT JOIN `{$wpdb->prefix}b2s_posts_network_details` 
                    ON `{$wpdb->prefix}b2s_posts`.`network_details_id` = `{$wpdb->prefix}b2s_posts_network_details`.`id` 
                LEFT JOIN `{$wpdb->prefix}b2s_posts_sched_details` 
                    ON `{$wpdb->prefix}b2s_posts`.`sched_details_id` = `{$wpdb->prefix}b2s_posts_sched_details`.`id` ";
            $where = "WHERE `{$wpdb->prefix}b2s_posts`.`hide` = 0 
                AND (
                    (`{$wpdb->prefix}b2s_posts`.`sched_date_utc` != '0000-00-00 00:00:00' AND `{$wpdb->prefix}b2s_posts`.`post_for_approve` = 0) 
                    OR (`{$wpdb->prefix}b2s_posts`.`sched_date_utc` >= '" . gmdate('Y-m-d H:i:s') . "' AND `{$wpdb->prefix}b2s_posts`.`post_for_approve` = 1)) 
                AND (`{$wpdb->prefix}b2s_posts`.`sched_type` != 3 
                    OR (`{$wpdb->prefix}b2s_posts`.`sched_type` = 3 AND `{$wpdb->prefix}b2s_posts`.`publish_date` = '0000-00-00 00:00:00'))  
                AND `{$wpdb->prefix}b2s_posts`.`publish_date` = '0000-00-00 00:00:00' 
                $addNotAdminPosts $addSearchShowByDate $addSearchShowByNetwork $addSearchUserAuthId $addSearchRepost 
                AND `{$wpdb->prefix}b2s_posts`.`post_id` = %d ORDER BY `{$wpdb->prefix}b2s_posts`.`sched_date` ASC ";
            $sqlData = $wpdb->prepare($select . $from . $where, $post_id);
            $result = $wpdb->get_results($sqlData);
            $specialPostingData = array(3 => esc_html__('Auto-Posting', 'blog2social'), 4 => esc_html__('Retweet', 'blog2social'), 5 => esc_html__('Re-Share', 'blog2social'));
            if (!empty($result) && is_array($result)) {
                $networkType = unserialize(B2S_PLUGIN_NETWORK_TYPE);
                $networkName = unserialize(B2S_PLUGIN_NETWORK);
                $content = '<div class="row"><div class="col-md-12"><ul class="list-group">';
                if ($this->type != 'repost') {
                    $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label"><input class="checkbox-all" data-blog-post-id="' . esc_attr($post_id) . '" name="selected-checkbox-all" value="" type="checkbox"> ' . esc_html__('select all', 'blog2social') . '</label></li>';
                }
                $blogPostDate = strtotime(get_the_date('Y-m-d H:i:s', $post_id)) . '000';
                foreach ($result as $var) {
                    $specialPosting = (isset($var->sched_type) && isset($specialPostingData[$var->sched_type])) ? ' - <strong>' . esc_html($specialPostingData[$var->sched_type]) . '</strong>' : '';
                    $userInfoName = get_the_author_meta('display_name', $var->blog_user_id);
                    $content .= '<li class="list-group-item b2s-post-sched-area-li" data-post-id="' . esc_attr($var->id) . '">
                                    <div class="media">';
                    if ($this->type != 'repost') {
                        $content .= '<input class="checkboxes pull-left checkbox-item" data-blog-post-id="' . esc_attr($post_id) . '" name="selected-checkbox-item" value="' . esc_attr($var->id) . '" type="checkbox">';
                    }

                    $userInfoLastEditName = ((int) $var->last_edit_blog_user_id > 0 && (int) $var->last_edit_blog_user_id != (int) $var->blog_user_id) ? get_the_author_meta('display_name', $var->last_edit_blog_user_id) : '';
                    $lastEdit = (!empty($userInfoLastEditName)) ? ' | ' . sprintf(esc_html__('last modified by %s', 'blog2social'), '<a href="' . get_author_posts_url($var->last_edit_blog_user_id) . '">' . esc_html((!empty($userInfoLastEditName) ? $userInfoLastEditName : '-')) . '</a> | ') : '';

                    $schedInProcess = ($var->sched_date_utc <= gmdate('Y-m-d H:i:s')) ? ' <span class="glyphicon glyphicon-exclamation-sign glyphicon-info"></span> ' . esc_html__('is currently being processed by the network', 'blog2social') : '';
                    
                    $boardName = "";
                    if($var->network_id == 6){
                        $boards = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getBoards', 'token' => B2S_PLUGIN_TOKEN, 'networkType' => $networkType, 'networkAuthId' => $var->network_auth_id, 'selBoard' => null, 'networkId' => 6)));
                        $boards = explode("</option>",$boards->data);
                        $boardId = unserialize($var->sched_data)["board"];
                        foreach($boards as $b){
                            if($b!=""){
                                $boardInfo = explode('value="',$b)[1];    
                                $boardInfo = explode('">',$boardInfo);
                                if($boardId == $boardInfo[0]){
                                    $boardName = $boardInfo[1];
                                }
                            }
                        }
                    }
                    $name = ($boardName != "") ? ': '.$boardName : (!empty($var->network_display_name) ? (': ' . $var->network_display_name) : '' );
                    
                    $content .= '<img class="pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                            <div class="media-body">
                                                <strong>' . esc_html($networkName[$var->network_id]) . $schedInProcess . '</strong>
                                                <p class="info">' . esc_html($networkType[$var->network_type] . $name ) . ' | ' . sprintf(esc_html__('scheduled by %s', 'blog2social'), ' <a href="' . esc_url(get_author_posts_url($var->blog_user_id)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a>') . ' <span class="b2s-post-sched-area-sched-time" data-post-id="' . esc_attr($var->id) . '">' . $lastEdit . esc_html(B2S_Util::getCustomDateFormat($var->sched_date, substr(B2S_LANGUAGE, 0, 2))) . '</span> ' . $specialPosting . '</p>
                                                <p class="info">';

                    if ((int) $var->v2_id == 0 && empty($schedInProcess)) {
                        //data-blog-sched-date="' . $blogPostDate . '" data-b2s-sched-date="' . strtotime($var->sched_date) . '000"
                        if((B2S_PLUGIN_USER_VERSION > 0) && $var->post_format != 2){
                            $content .= ((B2S_PLUGIN_USER_VERSION > 0) ? ' <a href="#" class="b2s-post-edit-sched-btn" data-network-auth-id="' . esc_attr($var->network_auth_id) . '" data-network-type="' . esc_attr($var->network_type) . '" data-network-id="' . esc_attr($var->network_id) . '" data-post-id="' . esc_attr($var->post_id) . '" data-b2s-id="' . esc_attr($var->id) . '" data-relay-primary-post-id="' . esc_attr($var->relay_primary_post_id) . '" >' : ' <a href="#" class="b2sPreFeatureModalBtn" data-title="' . esc_attr__('You want to edit your scheduled post?', 'blog2social') . '">');
                            $content .= esc_html__('edit', 'blog2social') . '</a> ';
                            $content .= '|';
                        } else if (B2S_PLUGIN_USER_VERSION == 0){
                            $content = ' <a href="#" class="b2sPreFeatureModalBtn" data-title="' . esc_attr__('You want to edit your scheduled post?', 'blog2social') . '">';
                        }
                        
                    }
                    $content .= '<a href="#" class="b2s-post-sched-area-drop-btn" data-post-id="' . esc_attr($var->id) . '"> ' . esc_html__('delete', 'blog2social') . '</a> ';

                    $content .= '</p>
                                            </div>
                                    </div>
                                </li>';
                }
                if ($this->type != 'repost') {
                    $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label-btn"><span class="glyphicon glyphicon glyphicon-trash "></span> ';
                    $content .= '<a class="checkbox-post-sched-all-btn" data-blog-post-id="' . esc_attr($post_id) . '" href="#"> ' . esc_html__('delete scheduling', 'blog2social');
                    $content .= '</a></label></li>';
                }
                $content .= '</ul></div></div>';
                return $content;
            }
        }
        return false;
    }

}
