<?php

class PRG_Post_Item {

    protected $postData;
    protected $postTotal;
    protected $postItem = '';
    protected $postPagination = '';
    protected $postPaginationLinks = 5;
    protected $searchAuthorId;
    protected $searchPostType;
    protected $searchPublishDate;
    protected $searchSchedDate;
    protected $searchPostTitle;
    protected $userLang;
    public $currentPage = 0;
    public $type;

    function __construct($type = 'all', $title = "", $authorId = 0, $postType = "", $postStatus = "", $currentPage = 0, $userLang = 'en') {
        $this->type = $type;
        $this->searchPostTitle = $title;
        $this->searchAuthorId = (int) $authorId;
        $this->searchPostType = $postType;
        $this->searchPostStatus = $postStatus;
        $this->currentPage = $currentPage;
        $this->userLang = $userLang; //Plugin: qTranslate
    }

    protected function getData() {
        global $wpdb;
        $addSearchAuthorId = '';
        $addSearchPostTitle = '';
        $order = 'ID';
        $sortType = 'DESC';
        if (!empty($this->searchPostTitle)) {
            $addSearchPostTitle = $wpdb->prepare(' AND `post_title` LIKE %s', '%' . trim($this->searchPostTitle) . '%');
        }
        if (!empty($this->searchAuthorId)) {
            $addSearchAuthorId = $wpdb->prepare(' AND `post_author` = %d', $this->searchAuthorId);
        }
        if (!empty($this->searchPostStatus)) {
            $addSearchType = $wpdb->prepare(' `post_status` = %s', $this->searchPostStatus);
        } else {
            $addSearchType = " (`post_status` = 'publish' OR `post_status` = 'future') ";
        }

        $postTypes = " ";
        if (!empty($this->searchPostType)) {
            $postTypes .= " `post_type` LIKE '%" . $this->searchPostType . "%' ";
        } else {
            $post_types = get_post_types(array('public' => true));
            if (is_array($post_types) && !empty($post_types)) {
                $postTypes .= " `post_type` IN("; // AND
                foreach ($post_types as $k => $v) {
                    if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                        $postTypes .= "'" . $v . "',";
                    }
                }
                $postTypes = rtrim($postTypes, ',');
                $postTypes .= " ) ";
            } else {
                $postTypes .= " (`post_type` LIKE '%product%' OR `post_type` LIKE '%book%' OR `post_type` LIKE '%article%' OR `post_type` LIKE '%job%' OR `post_type` LIKE '%event%' OR `post_type` = 'post' OR `post_type` = 'page') ";
            }
        }

        $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND `post_author` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';

        if ($this->type == 'all') {
            $sqlPosts = "SELECT `$wpdb->posts`.`ID`,`post_author`,`post_date`,`post_type`,`post_status`, `post_title` 
		FROM `$wpdb->posts`
		WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle $addNotAdmin
                AND $postTypes
		ORDER BY `" . $order . "` " . $sortType . " 
                LIMIT " . (($this->currentPage - 1) * B2S_PLUGIN_POSTPERPAGE) . "," . B2S_PLUGIN_POSTPERPAGE;
            $this->postData = $wpdb->get_results($sqlPosts);
            $sqlPostsTotal = "SELECT COUNT(*)
		FROM `$wpdb->posts`
		WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle $addNotAdmin
                AND $postTypes";
            $this->postTotal = $wpdb->get_var($sqlPostsTotal);
        }
    }

    public function getItemHtml() {
        $this->getData();
        $postStatus = array('publish' => __('published', 'blog2social'), 'pending' => __('draft', 'blog2social'), 'future' => __('scheduled', 'blog2social'));

        if (empty($this->postData)) {
            $text = esc_html__('You have no posts published or scheduled', 'blog2social');
            return '<li class="list-group-item"><div class="media"><div class="media-body"></div>' . $text . '</div></li>';
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

            if ($this->type == 'all') {
                //Plugin: qTranslate
                $postTitle = B2S_Util::getTitleByLanguage($var->post_title, $this->userLang);
                if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                    $postTitle = (mb_strlen(trim($postTitle), 'UTF-8') > 100 ? mb_substr($postTitle, 0, 97, 'UTF-8') . '...' : $postTitle);
                }

                $userInfoName = get_the_author_meta('display_name', $var->post_author);
                $this->postItem .= '<li class="list-group-item">
                                <div class="media">
                                    <img class="post-img-10 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/prg/' . $postType . '-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                        <div class="media-body">
                                                <strong><a target="_blank" href="' . esc_url(get_permalink($var->ID)) . '">' . esc_html($postTitle) . '</a></strong>
                                            <span class="pull-right b2s-publish-btn">
                                                <a href="admin.php?page=prg-login&postId=' . $var->ID . '" class="btn btn-primary btn-sm">' . esc_html__('Publish on PR-Gateway', 'blog2social') . '</a>
                                            </span>
                                            <p class="info hidden-xs">#' . esc_html($var->ID) . ' | ' . esc_html__('Author', 'blog2social') . ' <a href="' . esc_url(get_author_posts_url($var->post_author)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a> | ' . esc_html($postStatus[trim(strtolower($var->post_status))]) . ' ' . esc_html__('on Blog', 'blog2social') . ' ' . esc_html(B2S_Util::getCustomDateFormat($var->post_date, substr(B2S_LANGUAGE, 0, 2))) . '</p>
                                        </div>
                                    </div>
                                </li>';
            }
        }

        return $this->postItem;
    }

    public function getPaginationHtml($page = 'prg-post') {
        if ((int) $this->postTotal > 0) {
            $sort = '';
            $last = ceil($this->postTotal / B2S_PLUGIN_POSTPERPAGE);
            $start = (($this->currentPage - $this->postPaginationLinks ) > 0 ) ? $this->currentPage - $this->postPaginationLinks : 1;
            $end = (( $this->currentPage + $this->postPaginationLinks ) < $last ) ? $this->currentPage + $this->postPaginationLinks : $last;
            $page = 'page=' . $page;
            $searchParams = '&b2sSortPostType=' . $this->searchPostType . '&b2sSortPostTitle=' . $this->searchPostTitle . '&b2sSortPostAuthor=' . $this->searchAuthorId;
            $this->postPagination = '<ul class="pagination">';
            $class = ( $this->currentPage == 1 ) ? "disabled" : "";
            $linkpre = ( $this->currentPage == 1 ) ? '#' : ('?' . $page . $searchParams . '&b2sPage=' . ( $this->currentPage - 1 ));
            $this->postPagination .= '<li class="' . esc_attr($class) . '"><a href="' . $linkpre . '">&laquo;</a></li>';
            if ($start > 1) {
                $this->postPagination .= '<li><a href="?' . $page . $searchParams . '&b2sPage=1">1</a></li>';
                $this->postPagination .= '<li class="disabled"><span>...</span></li>';
            }
            for ($i = $start; $i <= $end; $i++) {
                $class = ( $this->currentPage == $i ) ? "active" : "";
                $this->postPagination .= '<li class="' . esc_attr($class) . '"><a href="?' . $page . $searchParams . '&b2sPage=' . $i . '">' . esc_html($i) . '</a></li>';
            }
            if ($end < $last) {
                $this->postPagination .= '<li class="disabled"><span>...</span></li>';
                $this->postPagination .= '<li><a href="?' . $page . $searchParams . '&b2sPage=' . $last . '">' . esc_html($last) . '</a></li>';
            }
            $class = ( $this->currentPage == $last ) ? "disabled" : "";
            $linkpast = ( $this->currentPage == $last ) ? '#' : ('?' . $page . $searchParams . '&b2sPage=' . ( $this->currentPage + 1 ));
            $this->postPagination .= '<li class="' . esc_attr($class) . '"><a href="' . $linkpast . '">&raquo;</a></li>';
            $this->postPagination .= '</ul>';
        }
        return $this->postPagination;
    }

}
