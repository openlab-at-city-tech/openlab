<?php

/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
 */

class B2S_RePost_Item {

    private $options;
    private $postTypesData;
    private $postCategoriesData;
    private $postAuthorData;
    private $postTagsData;
    private $authData;
    private $schedLimit = null;

    public function __construct() {
        $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
        $this->postTypesData = get_post_types(array('public' => true));
        $this->postCategoriesData = get_categories();
        $this->postAuthorData = get_users();
        $this->postTagsData = get_tags(array('hide_empty' => false));
    }

    public function getAuthData() {
        $currentDate = new DateTime("now", wp_timezone());
        $this->authData = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getProfileUserAuth', 'current_date' => $currentDate->format('Y-m-d'), 'update_licence' => 1, 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));

        if (isset($this->authData->licence_condition)) {
            //update
            $versionDetails = get_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
            if ($versionDetails !== false && is_array($versionDetails) && !empty($versionDetails)) {
                $versionDetails['B2S_PLUGIN_LICENCE_CONDITION'] = (array) $this->authData->licence_condition;
                if (isset($result->network_condition)) {
                    $versionDetails['B2S_PLUGIN_NETWORK_CONDITION'] = (array) $result->network_condition;
                }
                update_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID, $versionDetails, false);

                if (isset($this->authData->licence_condition->open_sched_post_quota) && B2S_PLUGIN_USER_VERSION > 0) {
                    if ((int) $this->authData->licence_condition->open_sched_post_quota > 0) {
                        $this->schedLimit = (int) $this->authData->licence_condition->open_sched_post_quota;
                    } else {
                        $this->schedLimit = 0;
                    }
                }
            }
        }
    }

    public function getRePostOptionsHtml() {

        $isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? false : true;
        $showSchedLimitInfo = ($isPremium && $this->schedLimit <= 0) ? "" : "b2s-info-display-none";
        $limit = unserialize(B2S_PLUGIN_RE_POST_LIMIT);

        $content = '';

        // Premium / sched-limit alert
        $content .= '<div id="b2s-licence-condition" class="alert alert-danger ' . $showSchedLimitInfo . '">';
        $content .= '<span class="b2s-text-bold">' . esc_html__("You've reached your posting limit!", "blog2social") . '</span><br>';
        $content .= esc_html__('To increase your limit and enjoy more features, consider upgrading.', 'blog2social');
        $content .= ' <a target="_blank" class="b2s-text-bold" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '">' . esc_html__('Upgrade', 'blog2social') . '</a>';
        $content .= '</div>';

        $content .= '<form id="b2s-re-post-settings" class="' . ((!$isPremium) ? 'b2s-btn-disabled' : '') . '">';
        $content .= '<div class="row">';

        // =============================================================
        // LEFT COLUMN — Content Filter (8/12)
        // =============================================================
        $content .= '<div class="col-md-8">';
        $content .= '<div class="panel panel-default b2s-rp-new-card">';

        // Card header
        $content .= '<div class="panel-heading b2s-rp-new-card-header">';
        $content .= '<div class="b2s-rp-new-card-title-row">';
        $content .= '<div class="b2s-rp-new-card-title">';
        $content .= '<span class="dashicons dashicons-filter b2s-rp-new-icon-primary"></span>';
        $content .= '<span class="b2s-rp-new-heading">' . esc_html__('Which content should be shared?', 'blog2social') . '</span>';
        $content .= '<a target="_blank" style="margin-top: 1px;" href="' . esc_url(B2S_Tools::getSupportLink('network_guide_re_sharer')) . '">Info</a>';
        $content .= '</div>';
        $content .= '<span id="b2s-rp-new-filter-badge" class="label label-default b2s-rp-new-filter-badge b2s-info-display-none"></span>';
        $content .= '</div>';
        $content .= '<p class="b2s-rp-new-card-desc">' . esc_html__('Choose which posts should be shared automatically', 'blog2social') . '</p>';
        $content .= '</div>';

        // Card body
        $content .= '<div class="panel-body">';

        // --- Post count + sort order ---
        $content .= '<div class="row">';

        $postCount = array();
        for($i=1; $i<=100; $i++){
            if($i%5==0){
                $postCount[]=$i;
            }
        }
        
        $content .= '<div class="col-sm-6 form-group">';
        $content .= '<label for="b2s-rp-new-post-count">' . esc_html__('Number of posts', 'blog2social') . '</label>';
        $content .= '<div class="alert alert-info b2s-re-post-limit-info" style="display:none;">';
        $content .= '<a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '" target="_blank">' . esc_html__('Upgrade', 'blog2social') . '</a> ';
        $content .= esc_html__('your Blog2Social license to extend the quota for the number of posts in your queue.', 'blog2social');
        $content .= '</div>';
        $content .= '<select id="b2s-rp-new-post-count" name="b2s-re-post-limit" class="form-control b2s-re-post-limit">';
        foreach ($postCount as $count) {
            $content .= '<option value="' . $count . '" data-limit="' . (($count <= $limit[B2S_PLUGIN_USER_VERSION]) ? '1' : '0') . '">' . $count . ' ' . esc_html__('Posts', 'blog2social') . '</option>';
        }
        $content .= '</select>';
        $content .= '</div>';

        $content .= '<div class="col-sm-6 form-group">';
        $content .= '<label for="b2s-rp-new-sort-order">' . esc_html__('Sort order', 'blog2social') . '</label>';
        $content .= '<select id="b2s-rp-new-sort-order" name="b2s-rp-new-sort" class="form-control">';
        $content .= '<option value="0">' . esc_html__('Oldest first', 'blog2social') . '</option>';
        $content .= '<option value="1">' . esc_html__('Newest first', 'blog2social') . '</option>';
        $content .= '<option value="2">' . esc_html__('Random', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '</div>';

        $content .= '</div>'; // .row

        // --- Advanced Filters collapsible ---
        $content .= '<a class="b2s-rp-new-collapse-trigger" data-toggle="collapse" aria-expanded="true">';
        $content .= '<span>' . esc_html__('Advanced Filter', 'blog2social') . '</span>';
        $content .= '<span class="glyphicon glyphicon-chevron-down b2s-rp-new-chevron"></span>';
        $content .= '</a>';
        $content .= '<input hidden style="display:none;" type="radio" id="b2s-re-post-settings-option-1" name="b2s-re-post-settings-option" class="b2s-re-post-settings-option" checked value="1">';


        $content .= '<div id="b2s-rp-new-advanced-filters" class="collapse in">';
        $content .= '<div class="b2s-rp-new-filter-list">';

        // Content Types
        if (is_array($this->postTypesData) && !empty($this->postTypesData)) {
            $content .= '<div class="b2s-rp-new-filter-row">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-media-document b2s-rp-new-icon-muted"></span><span>' . esc_html__('Content Types', 'blog2social') . '</span></div>';
            $content .= '<select name="b2s-re-post-type-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-rp-new-type-input">';
            $content .= '<option value="all">' . esc_html__('All types', 'blog2social') . '</option>';
            $content .= '<option value="0">' . esc_html__('Include selected only', 'blog2social') . '</option>';
            $content .= '<option value="1">' . esc_html__('Exclude selected', 'blog2social') . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '<div class="b2s-rp-new-filter-input b2s-info-display-none" id="b2s-rp-new-type-input">';
            $content .= '<select name="b2s-re-post-type-data[]" data-placeholder="' . esc_attr__('Select post types…', 'blog2social') . '" class="b2s-re-post-type form-control" multiple>';
            foreach ($this->postTypesData as $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $content .= '<option value="' . esc_attr($v) . '">' . esc_html($v) . '</option>';
                }
            }
            $content .= '</select>';
            $content .= '</div>';
            $content .= '</div>';
        }

        // Publication Date
        $content .= '<div class="b2s-rp-new-filter-row">';
        $content .= '<div class="b2s-rp-new-filter-row-head">';
        $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-calendar-alt b2s-rp-new-icon-muted"></span><span>' . esc_html__('Publication Date', 'blog2social') . '</span></div>';
        $content .= '<select name="b2s-re-post-date-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-rp-new-date-input">';
        $content .= '<option value="all">' . esc_html__('Any date', 'blog2social') . '</option>';
        $content .= '<option value="range">' . esc_html__('Choose date range', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div class="b2s-rp-new-filter-input b2s-info-display-none" id="b2s-rp-new-date-input">';
        $content .= '<div class="row">';
        $content .= '<div class="col-sm-6"><label class="b2s-rp-new-small-label">' . esc_html__('Start date', 'blog2social') . '</label>';
        $content .= '<input type="text" placeholder="' . esc_attr__('Start date', 'blog2social') . '" class="b2s-re-post-date-start form-control" name="b2s-re-post-date-start"></div>';
        $content .= '<div class="col-sm-6"><label class="b2s-rp-new-small-label">' . esc_html__('End date', 'blog2social') . '</label>';
        $content .= '<input type="text" placeholder="' . esc_attr__('End date', 'blog2social') . '" class="b2s-re-post-date-end form-control" name="b2s-re-post-date-end"></div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        // Categories
        if (is_array($this->postCategoriesData) && !empty($this->postCategoriesData)) {
            $content .= '<div class="b2s-rp-new-filter-row">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-category b2s-rp-new-icon-muted"></span><span>' . esc_html__('Categories', 'blog2social') . '</span></div>';
            $content .= '<select name="b2s-re-post-categories-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-rp-new-categories-input">';
            $content .= '<option value="all">' . esc_html__('All categories', 'blog2social') . '</option>';
            $content .= '<option value="0">' . esc_html__('Include selected only', 'blog2social') . '</option>';
            $content .= '<option value="1">' . esc_html__('Exclude selected', 'blog2social') . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '<div class="b2s-rp-new-filter-input b2s-info-display-none" id="b2s-rp-new-categories-input">';
            $content .= '<select name="b2s-re-post-categories-data[]" data-placeholder="' . esc_attr__('Select categories…', 'blog2social') . '" class="b2s-re-post-categories form-control" multiple>';
            foreach ($this->postCategoriesData as $cat) {
                $content .= '<option value="' . esc_attr($cat->term_taxonomy_id) . '">' . esc_html($cat->name) . '</option>';
            }
            $content .= '</select>';
            $content .= '</div>';
            $content .= '</div>';
        }

        // Tags
        if (is_array($this->postTagsData) && !empty($this->postTagsData)) {
            $content .= '<div class="b2s-rp-new-filter-row">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-tag b2s-rp-new-icon-muted"></span><span>' . esc_html__('Tags', 'blog2social') . '</span></div>';
            $content .= '<select name="b2s-re-post-tags-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-rp-new-tags-input">';
            $content .= '<option value="all">' . esc_html__('All tags', 'blog2social') . '</option>';
            $content .= '<option value="0">' . esc_html__('Include selected only', 'blog2social') . '</option>';
            $content .= '<option value="1">' . esc_html__('Exclude selected', 'blog2social') . '</option>';
            $content .= '</select>';
            $content .= '</div>';
            $content .= '<div class="b2s-rp-new-filter-input b2s-info-display-none" id="b2s-rp-new-tags-input">';
            $content .= '<select name="b2s-re-post-tags-data[]" data-placeholder="' . esc_attr__('Select tags…', 'blog2social') . '" class="b2s-re-post-tags form-control" multiple>';
            foreach ($this->postTagsData as $tag) {
                $content .= '<option value="' . esc_attr($tag->term_taxonomy_id) . '">' . esc_html($tag->name) . '</option>';
            }
            $content .= '</select>';
            $content .= '</div>';
            $content .= '</div>';
        }

        // Authors
        if (is_array($this->postAuthorData) && !empty($this->postAuthorData)) {
            $content .= '<div class="b2s-rp-new-filter-row">';
            $content .= '<div class="b2s-rp-new-filter-row-head">';
            $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-admin-users b2s-rp-new-icon-muted"></span><span>' . esc_html__('Authors', 'blog2social') . '</span></div>';
            if (defined('B2S_PLUGIN_ADMIN') && B2S_PLUGIN_ADMIN) {
                $content .= '<select name="b2s-re-post-author-state" class="form-control b2s-rp-new-filter-state-select b2s-rp-new-filter-toggle" data-target="b2s-rp-new-author-input">';
                $content .= '<option value="all">' . esc_html__('All authors', 'blog2social') . '</option>';
                $content .= '<option value="0">' . esc_html__('Include selected only', 'blog2social') . '</option>';
                $content .= '<option value="1">' . esc_html__('Exclude selected', 'blog2social') . '</option>';
                $content .= '</select>';
                $content .= '</div>';
                $content .= '<div class="b2s-rp-new-filter-input b2s-info-display-none" id="b2s-rp-new-author-input">';
                $content .= '<select name="b2s-re-post-author-data[]" data-placeholder="' . esc_attr__('Select authors…', 'blog2social') . '" class="b2s-re-post-author form-control" multiple>';
                foreach ($this->postAuthorData as $user) {
                    $authorName = $user->display_name;
                    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                        $authorName = mb_strlen($user->display_name, 'UTF-8') > 27 ? mb_substr($user->display_name, 0, 27, 'UTF-8') . '...' : $authorName;
                    }
                    $content .= '<option value="' . esc_attr($user->ID) . '">' . esc_html($authorName) . '</option>';
                }
                $content .= '</select>';
                $content .= '</div>';
            } else {
                $content .= '<input type="hidden" name="b2s-re-post-author-state" value="all">';
                $content .= '<span class="b2s-rp-new-author-notice">' . esc_html__('Only your own WordPress posts are available for resharing.', 'blog2social') . '</span>';
                $content .= ' <a href="#" class="b2s-re-post-author-info-modal-btn">' . esc_html__('Info', 'blog2social') . '</a>';
                $content .= '</div>';
            }
            $content .= '</div>';
        }

        // AI-generated flags
        $content .= '<div class="b2s-rp-new-filter-row">';
        $content .= '<div class="b2s-rp-new-filter-row-head">';
        $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-format-image b2s-rp-new-icon-muted"></span><span>' . esc_html__('Image is AI generated', 'blog2social') . '</span></div>';
        $content .= '<select name="b2s-re-post-image-ai-generated" class="form-control b2s-rp-new-filter-state-select">';
        $content .= '<option value="0">' . esc_html__('Off', 'blog2social') . '</option>';
        $content .= '<option value="1">' . esc_html__('On', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="b2s-rp-new-filter-row">';
        $content .= '<div class="b2s-rp-new-filter-row-head">';
        $content .= '<div class="b2s-rp-new-filter-label"><span class="dashicons dashicons-editor-textcolor b2s-rp-new-icon-muted"></span><span>' . esc_html__('Text is AI generated', 'blog2social') . '</span></div>';
        $content .= '<select name="b2s-re-post-text-ai-generated" class="form-control b2s-rp-new-filter-state-select">';
        $content .= '<option value="0">' . esc_html__('Off', 'blog2social') . '</option>';
        $content .= '<option value="1">' . esc_html__('On', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '</div>';

        // Additional options
        $content .= '<div class="b2s-rp-new-extras">';

        // Favorites only
        $content .= '<div class="b2s-rp-new-toggle-row">';
        $content .= '<div class="b2s-rp-new-toggle-label">';
        $content .= '<span class="dashicons dashicons-heart b2s-rp-new-icon-muted"></span>';
        $content .= '<label for="b2s-re-post-favorites-active" class="b2s-rp-new-label-normal">';
        // translators: %s is a link
        $content .= sprintf(__('Include <a href="%s" target="_blank">favorite posts</a> only', 'blog2social'), esc_url('admin.php?page=blog2social-favorites'));
        $content .= '</label>';
        $content .= '</div>';
        $content .= '<label class="b2s-rp-new-switch"><input type="checkbox" name="b2s-re-post-favorites-active" id="b2s-re-post-favorites-active" value="1"><span class="b2s-rp-new-slider"></span></label>';
        $content .= '</div>';

        // Images only
        $content .= '<div class="b2s-rp-new-toggle-row">';
        $content .= '<div class="b2s-rp-new-toggle-label">';
        $content .= '<span class="dashicons dashicons-format-image b2s-rp-new-icon-muted"></span>';
        $content .= '<label for="b2s-re-post-images-active" class="b2s-rp-new-label-normal">' . esc_html__('Posts with images only', 'blog2social') . '</label>';
        $content .= '</div>';
        $content .= '<label class="b2s-rp-new-switch"><input type="checkbox" name="b2s-re-post-images-active" id="b2s-re-post-images-active" value="1"><span class="b2s-rp-new-slider"></span></label>';
        $content .= '</div>';

        // Max shares
        $content .= '<div class="b2s-rp-new-toggle-row">';
        $content .= '<div class="b2s-rp-new-toggle-label">';
        $content .= '<label class="b2s-rp-new-switch" style="margin-right:8px;"><input type="checkbox" name="b2s-re-post-already-planed-active" id="b2s-re-post-already-planed-active" value="1"><span class="b2s-rp-new-slider"></span></label>';
        $content .= '<label for="b2s-re-post-already-planed-active" class="b2s-rp-new-label-normal">' . esc_html__('Posts shared max.', 'blog2social') . '</label>';
        $content .= '<input type="number" name="b2s-re-post-already-planed-count" class="form-control b2s-rp-new-inline-number" value="1" min="1" max="50" style="margin:0 6px;">';
        $content .= '<span class="b2s-rp-new-muted">' . esc_html__('times', 'blog2social') . '</span>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '</div>'; // .b2s-rp-new-extras

        $content .= '</div>'; // .b2s-rp-new-filter-list
        $content .= '</div>'; // #b2s-rp-new-advanced-filters

        $content .= '</div>'; // .panel-body
        $content .= '</div>'; // .panel (content filter)
        $content .= '</div>'; // .col-md-8

        $content .= '<div class="col-md-4">';

        // --- Schedule Panel ---
        $content .= '<div class="panel panel-default b2s-rp-new-card">';
        $content .= '<div class="panel-heading b2s-rp-new-card-header">';
        $content .= '<div class="b2s-rp-new-card-title">';
        $content .= '<span class="dashicons dashicons-clock b2s-rp-new-icon-primary"></span>';
        $content .= '<span class="b2s-rp-new-heading">' . esc_html__('When?', 'blog2social') . '</span>';
        $content .= '</div>';
        $content .= '<p class="b2s-rp-new-card-desc">' . esc_html__('Set when your posts should be shared', 'blog2social') . '</p>';
        $content .= '</div>';
        $content .= '<div class="panel-body">';

        // Daily schedule option
        $content .= '<div class="b2s-rp-new-sched-option b2s-rp-new-sched-active" id="b2s-rp-new-sched-daily-box">';
        $content .= '<div class="b2s-rp-new-sched-header">';
        $content .= '<input type="radio" class="b2s-re-post-share-option" id="b2s-rp-new-sched-0" name="b2s-re-post-share-option" checked value="0">';
        $content .= '<label for="b2s-rp-new-sched-0" class="b2s-rp-new-sched-label">' . esc_html__('Post daily', 'blog2social') . '</label>';
        $content .= '</div>';
        $content .= '<div id="b2s-rp-new-sched-daily-body" class="b2s-rp-new-sched-body">';
        $content .= '<div class="b2s-rp-new-inline-row">';
        $content .= '<span class="b2s-rp-new-muted">' . esc_html__('Every', 'blog2social') . '</span>';
        $content .= '<input type="number" name="b2s-re-post-day-0" class="form-control b2s-rp-new-inline-number" value="1" min="1" max="30">';
        $content .= '<span class="b2s-rp-new-muted">' . esc_html__('days at', 'blog2social') . '</span>';
        $content .= '<input name="b2s-re-post-input-time-0" class="b2s-re-post-input-time form-control b2s-rp-new-time-input">';
        $content .= '</div>';
        // Weekday toggle buttons
        $content .= '<div class="b2s-rp-new-weekday-row">';
        $weekdays = array(
            array('val' => '1', 'label' => esc_html__('Mo', 'blog2social'), 'name' => 'b2s-re-post-weekday-1'),
            array('val' => '2', 'label' => esc_html__('Tu', 'blog2social'), 'name' => 'b2s-re-post-weekday-2'),
            array('val' => '3', 'label' => esc_html__('We', 'blog2social'), 'name' => 'b2s-re-post-weekday-3'),
            array('val' => '4', 'label' => esc_html__('Th', 'blog2social'), 'name' => 'b2s-re-post-weekday-4'),
            array('val' => '5', 'label' => esc_html__('Fr', 'blog2social'), 'name' => 'b2s-re-post-weekday-5'),
            array('val' => '6', 'label' => esc_html__('Sa', 'blog2social'), 'name' => 'b2s-re-post-weekday-6'),
            array('val' => '0', 'label' => esc_html__('Su', 'blog2social'), 'name' => 'b2s-re-post-weekday-0'),
        );
        foreach ($weekdays as $day) {
            $content .= '<button type="button" class="b2s-rp-new-weekday-btn b2s-rp-new-weekday-on">';
            $content .= '<span>' . $day['label'] . '</span>';
            $content .= '<input type="checkbox" name="' . esc_attr($day['name']) . '" value="1" checked class="b2s-re-post-weekday b2s-rp-new-weekday-hidden">';
            $content .= '</button>';
        }
        $content .= '</div>'; // .b2s-rp-new-weekday-row
        $content .= '</div>'; // #b2s-rp-new-sched-daily-body
        $content .= '</div>'; // .b2s-rp-new-sched-option (daily)

        // Weekly schedule option
        $content .= '<div class="b2s-rp-new-sched-option" id="b2s-rp-new-sched-weekly-box">';
        $content .= '<div class="b2s-rp-new-sched-header">';
        $content .= '<input type="radio" class="b2s-re-post-share-option" id="b2s-rp-new-sched-1" name="b2s-re-post-share-option" value="1">';
        $content .= '<label for="b2s-rp-new-sched-1" class="b2s-rp-new-sched-label">' . esc_html__('Post weekly', 'blog2social') . '</label>';
        $content .= '</div>';
        $content .= '<div id="b2s-rp-new-sched-weekly-body" class="b2s-rp-new-sched-body" style="display:none;">';
        $content .= '<div class="b2s-rp-new-inline-row">';
        $content .= '<span class="b2s-rp-new-muted">' . esc_html__('Every', 'blog2social') . '</span>';
        $content .= '<input type="number" name="b2s-re-post-day-1" class="form-control b2s-rp-new-inline-number" value="1" min="1" max="10">';
        $content .= '<select class="b2s-re-post-weekday-select form-control b2s-rp-new-select-sm" name="b2s-re-post-weekday-select">';
        $content .= '<option value="monday">' . esc_html__('Monday', 'blog2social') . '</option>';
        $content .= '<option value="tuesday">' . esc_html__('Tuesday', 'blog2social') . '</option>';
        $content .= '<option value="wednesday">' . esc_html__('Wednesday', 'blog2social') . '</option>';
        $content .= '<option value="thursday">' . esc_html__('Thursday', 'blog2social') . '</option>';
        $content .= '<option value="friday">' . esc_html__('Friday', 'blog2social') . '</option>';
        $content .= '<option value="saturday">' . esc_html__('Saturday', 'blog2social') . '</option>';
        $content .= '<option value="sunday">' . esc_html__('Sunday', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '<span class="b2s-rp-new-muted">' . esc_html__('at', 'blog2social') . '</span>';
        $content .= '<input name="b2s-re-post-input-time-1" class="b2s-re-post-input-time form-control b2s-rp-new-time-input">';
        $content .= '</div>';
        $content .= '</div>'; // #b2s-rp-new-sched-weekly-body
        $content .= '</div>'; // .b2s-rp-new-sched-option (weekly)

        // Best times toggle
        $content .= '<div class="b2s-rp-new-toggle-row b2s-rp-new-best-times">';
        $content .= '<div class="b2s-rp-new-toggle-label">';
        $content .= '<span class="dashicons dashicons-star-filled b2s-rp-new-icon-primary"></span>';
        $content .= '<label for="b2s-re-post-best-times-active" class="b2s-rp-new-label-normal">' . esc_html__('at my best times', 'blog2social') . '</label>';
        $content .= '</div>';
        $content .= '<label class="b2s-rp-new-switch"><input type="checkbox" name="b2s-re-post-best-times-active" id="b2s-re-post-best-times-active" value="1"><span class="b2s-rp-new-slider"></span></label>';
        $content .= '</div>';

        $content .= '</div>'; // .panel-body
        $content .= '</div>'; // .panel (schedule)

        // --- Destination Panel ---
        $content .= '<div class="panel panel-default b2s-rp-new-card">';
        $content .= '<div class="panel-heading b2s-rp-new-card-header">';
        $content .= '<div class="b2s-rp-new-card-title">';
        $content .= '<span class="dashicons dashicons-location b2s-rp-new-icon-primary"></span>';
        $content .= '<span class="b2s-rp-new-heading">' . esc_html__('Where?', 'blog2social') . '</span>';
        $content .= '</div>';
        $content .= '<p class="b2s-rp-new-card-desc">' . esc_html__('Choose the networks and profiles for your posts', 'blog2social') . '</p>';
        $content .= '</div>';
        $content .= '<div class="panel-body">';
        $content .= $this->getMandantSelect();
        $content .= '</div>';
        $content .= '</div>'; // .panel (destination)

        // Submit button
        $submitDisabled = ($isPremium && $this->schedLimit <= 0) ? 'disabled="disabled"' : '';
        $submitExtraClass = (!$isPremium) ? 'b2s-re-post-submit-premium' : (($this->schedLimit > 0) ? 'b2s-re-post-submit-btn' : '');
        $content .= '<button type="button" class="btn btn-primary btn-block b2s-rp-new-submit-btn ' . esc_attr($submitExtraClass) . '" ' . $submitDisabled . '>';
        $content .= esc_html__('Add to queue', 'blog2social');
        $content .= '</button>';

        $content .= '</div>'; // .col-md-4
        $content .= '</div>'; // .row

        $content .= '<input type="hidden" id="b2sUserLang" name="b2s-user-lang" value="' . esc_attr(strtolower(substr(get_locale(), 0, 2))) . '">';
        $content .= '</form>';

        return $content;
    }

    public function getRePostQueueHtml() {
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
        $postItem = new B2S_Post_Item('repost');
        $postItem->currentPage = 1;
        $limit = unserialize(B2S_PLUGIN_RE_POST_LIMIT);
        $needMoreBtn = (B2S_PLUGIN_USER_VERSION <= 2) ? '<a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '" target="_blank">' . esc_html__('Need more?', 'blog2social') . '</a>' : '';
        $content = '';
        $content .= '<div class="col-md-12 b2s-re-post-queue-header">';
        $content .= '<i class="glyphicon glyphicon-random b2s-icon-size"></i><span class="b2s-re-post-headline"> ' . esc_html__('Queue', 'blog2social') . '</span>';
        $content .= '<span class="b2s-re-post-headline pull-right"><span class="b2s-re-post-queue-count"></span>/' . $limit[B2S_PLUGIN_USER_VERSION] . ' ' . esc_html__('Posts', 'blog2social') . ' ' . $needMoreBtn . '</span>';
        $content .= '</div>';
        $content .= '<div class="col-md-12 b2s-re-post-queue-top-area">';
        $content .= '<div class="col-md-5">';
        $content .= '<div class="b2s-re-post-queue-delete-area">';
        $content .= '<button type="button" class="btn btn-primary btn-xs b2s-re-post-select-all">' . esc_html__('select all', 'blog2social') . '</button> ';
        $content .= '<button type="button" class="btn btn-danger btn-xs b2s-re-post-delete-checked" style="display:none;"><i class="glyphicon glyphicon-trash"></i> ' . esc_html__('delete selected posts', 'blog2social') . '</button>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="col-md-7">';
        $content .= '<button type="button" class="btn btn-primary btn-xs b2s-re-post-show-list-btn">' . esc_html__('List', 'blog2social') . '</button>';
        $content .= '<button type="button" class="btn btn-primary btn-xs b2s-re-post-show-calender-btn">' . esc_html__('Calendar', 'blog2social') . '</button>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="b2s-re-post-queue-area">';
        $content .= '<ul>' . $postItem->getItemHtml() . '</ul>';
        $content .= '</div>';
        $content .= '<div class="b2s-re-post-calender-area" style="display:none;">';
        $content .= '<div id="b2s_calendar"></div>';
        $content .= '</div>';
        return $content;
    }

    private function getMandantSelect($mandantId = 0, $twitterId = 0) {
        if (isset($this->authData) && !empty($this->authData) && isset($this->authData->result) && (int) $this->authData->result == 1 && isset($this->authData->data) && !empty($this->authData->data) && isset($this->authData->data->mandant) && isset($this->authData->data->auth) && !empty($this->authData->data->mandant) && !empty($this->authData->data->auth)) {

            /*
             * since V7.0 Remove Video Networks
             */
            if (!empty($this->authData->data->auth)) {
                $isVideoNetwork = unserialize(B2S_PLUGIN_NETWORK_SUPPORT_VIDEO);
                foreach ($this->authData->data->auth as $a => $auth) {
                    foreach ($auth as $u => $item) {
                        if (in_array($item->networkId, $isVideoNetwork)) {
                            // if (!in_array($item->networkId, array(1, 2, 3, 6, 7, 12, 38, 39))) {
                            if (!in_array($item->networkId, unserialize(B2S_PLUGIN_NETWORK_SUPPORT_SOCIAL))) {
                                if (isset($a[$u])) {
                                    unset($this->authData->data->auth->{$a[$u]});
                                }
                            }
                        }
                    }
                }
            }
            $mandant = $this->authData->data->mandant;
            $auth = $this->authData->data->auth;
            $authContent = '';

            $content = '<div id="b2s-network-group-wrap" >';
            $content .= '<div class="b2s-curation-network-select-wrap">';
            $content .= '<i class="glyphicon glyphicon-user b2s-curation-network-select-glyphicon"></i>';
            $content .= '<select class="b2s-w-100" id="b2s-re-post-profil-dropdown" name="b2s-re-post-profil-dropdown">';

            foreach ($mandant as $k => $m) {
                $content .= '<option value="' . esc_attr($m->id) . '" ' . (((int) $m->id == (int) $mandantId) ? 'selected' : '') . '>' . esc_html((($m->id == 0) ? __("My Profile", 'blog2social') : $m->name)) . '</option>';
                $profilData = (isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0])) ? json_encode($auth->{$m->id}) : '';
                $authContent .= "<input type='hidden' name='b2s-re-post-profil-data-" . esc_attr($m->id) . "' id='b2s-re-post-profil-data-" . esc_attr($m->id) . "' value='" . base64_encode($profilData) . "'/>";
            }
            $content .= '</select>';
            $content .= '<i class="glyphicon glyphicon-chevron-down b2s-compose-settings-toggle-icon select-chevron"></i>';
            $content .= '</div>';
            $content .= '<a class="b2s-preview-network-info-link b2s-network-info-modal-btn" href="#" style="vertical-align: middle;"><i class="glyphicon glyphicon-question-sign"></i></a>';
            $content .= '</div>';

            $content .= $authContent;

            //TOS Twitter 032018 - none multiple Accounts - User select once
            //$content .= '<div class="col-md-6 b2s-re-post-twitter-profile"><label for="b2s-re-post-profil-dropdown-twitter">' . esc_html__('X profile:', 'blog2social') . '</label> <a href="#" class="b2sTwitterInfoModalBtn">' . esc_html__('Info', 'blog2social') . '</a>';
            
            $content .= '<div id="b2s-network-group-wrap-twitter" >';
            $content .= '<div class="b2s-curation-network-select-wrap">';
            $content .= '<img class="hidden-xs b2s-img-network-x-icon" alt="' . esc_attr('X') . '" src="' . esc_url(plugins_url('/assets/images/portale/45_flat.png', B2S_PLUGIN_FILE)) . '">';

            $content .= '<select class="b2s-w-100" id="b2s-re-post-profil-dropdown-twitter" name="b2s-re-post-profil-dropdown-twitter">';
            foreach ($mandant as $k => $m) {
                if ((isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0]))) {
                    foreach ($auth->{$m->id} as $key => $value) {
                        if ($value->networkId == 2 || $value->networkId == 45) {
                            $content .= '<option data-mandant-id="' . esc_attr($m->id) . '" value="' . esc_attr($value->networkAuthId) . '" ' . (((int) $value->networkAuthId == (int) $twitterId) ? 'selected' : '') . '>' . esc_html($value->networkUserName) . '</option>';
                        }
                    }
                }
            }

            $content .= '</select>';
            $content .= '<i class="glyphicon glyphicon-chevron-down b2s-compose-settings-toggle-icon select-chevron "></i>';
            $content .= '</div>';
            $content .= '<a class="b2s-preview-network-info-link b2sTwitterInfoModalBtn" href="#" style="vertical-align: middle;"><i class="glyphicon glyphicon-question-sign"></i></a>';
            $content .= '</div>';
            $content .= '<div class="pull-right hidden-sm hidden-xs"><a href="' . esc_url(get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-network') . '" target="_blank">' . esc_html__('Network settings', 'blog2social') . '</a></div>';
            $content .= '<div class="pull-right hidden-sm hidden-xs"></div>';
            return $content;
        }
    }

    private function getChosenPostTypesData() {

        $html = '';
        if (is_array($this->postTypesData) && !empty($this->postTypesData)) {
            $html .= '<input type="checkbox" name="b2s-re-post-type-active" class="b2s-re-post-type-active" id="b2s-re-post-type-active" value="1">';
            $html .= '<label for="b2s-re-post-type-active"> ' . esc_html__('Post Types', 'blog2social') . ' </label>';
            $html .= '<input id="b2s-re-post-type-state-include" name="b2s-re-post-type-state" value="0" checked type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-type-state-include">' . esc_html__('Include (Post only...)', 'blog2social') . '</label> ';
            $html .= '<input id="b2s-re-post-type-state-exclude" name="b2s-re-post-type-state" value="1" type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-type-state-exclude">' . esc_html__('Exclude (Do no post ...)', 'blog2social') . '</label>';
            $html .= '<select name="b2s-re-post-type-data[]" data-placeholder="Select Post Types" class="b2s-re-post-type" multiple>';

            foreach ($this->postTypesData as $k => $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $html .= '<option value="' . esc_attr($v) . '">' . esc_html($v) . '</option>';
                }
            }

            $html .= '</select>';
        }
        return $html;
    }

    private function getDateData() {

        $html = '';
        $html .= '<input type="checkbox" name="b2s-re-post-date-active" class="b2s-re-post-date-active" id="b2s-re-post-date-active" value="1">';
        $html .= '<label for="b2s-re-post-date-active"> ' . esc_html__('Publication Date', 'blog2social') . ' </label>';
        $html .= '<input id="b2s-re-post-date-state-include" name="b2s-re-post-date-state" value="0" checked type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-date-state-include">' . esc_html__('Include (Post only...)', 'blog2social') . '</label> ';
        $html .= '<input id="b2s-re-post-date-state-exclude" name="b2s-re-post-date-state" value="1" type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-date-state-exclude">' . esc_html__('Exclude (Do no post ...)', 'blog2social') . '</label>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<input type="text" placeholder="' . esc_attr__('Startdate', 'blog2social') . '" class="b2s-re-post-date-start form-control" name="b2s-re-post-date-start">';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<input type="text" placeholder="' . esc_attr__('Enddate', 'blog2social') . '" class="b2s-re-post-date-end form-control" name="b2s-re-post-date-end">';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private function getChosenPostCategoriesData() {

        $html = '';
        if (is_array($this->postCategoriesData) && !empty($this->postCategoriesData)) {
            $html .= '<input type="checkbox" name="b2s-re-post-categories-active" class="b2s-re-post-categories-active" id="b2s-re-post-categories-active" value="1">';
            $html .= '<label for="b2s-re-post-categories-active"> ' . esc_html__('Categories', 'blog2social') . ' </label>';
            $html .= '<input id="b2s-re-post-categories-state-include" name="b2s-re-post-categories-state" value="0" checked type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-categories-state-include">' . esc_html__('Include (Post only...)', 'blog2social') . '</label> ';
            $html .= '<input id="b2s-re-post-categories-state-exclude" name="b2s-re-post-categories-state" value="1" type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-categories-state-exclude">' . esc_html__('Exclude (Do no post ...)', 'blog2social') . '</label>';
            $html .= '<select name="b2s-re-post-categories-data[]" data-placeholder="Select Post Categories" class="b2s-re-post-categories" multiple>';

            foreach ($this->postCategoriesData as $cat) {
                $html .= '<option value="' . esc_attr($cat->term_taxonomy_id) . '">' . esc_html($cat->name) . '</option>';
            }

            $html .= '</select>';
        }
        return $html;
    }

    private function getChosenPostTagsData() {

        $html = '';
        if (is_array($this->postTagsData) && !empty($this->postTagsData)) {
            $html .= '<input type="checkbox" name="b2s-re-post-tags-active" class="b2s-re-post-tags-active" id="b2s-re-post-tags-active" value="1">';
            $html .= '<label for="b2s-re-post-tags-active"> ' . esc_html__('Tags', 'blog2social') . ' </label>';
            $html .= '<input id="b2s-re-post-tags-state-include" name="b2s-re-post-tags-state" value="0" checked type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-tags-state-include">' . esc_html__('Include (Post only...)', 'blog2social') . '</label> ';
            $html .= '<input id="b2s-re-post-tags-state-exclude" name="b2s-re-post-tags-state" value="1" type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-tags-state-exclude">' . esc_html__('Exclude (Do no post ...)', 'blog2social') . '</label>';
            $html .= '<select name="b2s-re-post-tags-data[]" data-placeholder="Select Post Tags" class="b2s-re-post-tags" multiple>';

            foreach ($this->postTagsData as $cat) {
                $html .= '<option value="' . esc_attr($cat->term_taxonomy_id) . '">' . esc_html($cat->name) . '</option>';
            }

            $html .= '</select>';
        }
        return $html;
    }

    private function getChosenPostAuthorData() {

        $html = '';
        if (is_array($this->postAuthorData) && !empty($this->postAuthorData)) {
            if (current_user_can('edit_others_posts')) {
                $html .= '<input type="checkbox" name="b2s-re-post-author-active" class="b2s-re-post-author-active" id="b2s-re-post-author-active" value="1">';
                $html .= '<label for="b2s-re-post-author-active"> ' . esc_html__('Authors', 'blog2social') . ' </label>';
                $html .= '<input id="b2s-re-post-author-state-include" name="b2s-re-post-author-state" value="0" checked type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-author-state-include">' . esc_html__('Include (Post only...)', 'blog2social') . '</label> ';
                $html .= '<input id="b2s-re-post-author-state-exclude" name="b2s-re-post-author-state" value="1" type="radio" class="b2s-re-post-state"><label class="padding-bottom-3" for="b2s-re-post-author-state-exclude">' . esc_html__('Exclude (Do no post ...)', 'blog2social') . '</label>';
                $html .= '<select name="b2s-re-post-author-data[]" data-placeholder="Select Post Author" class="b2s-re-post-author" multiple>';
                foreach ($this->postAuthorData as $var) {
                    $autorName = $var->display_name;
                    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                        $autorName = mb_strlen($var->display_name, 'UTF-8') > 27 ? mb_substr($var->display_name, 0, 27, 'UTF-8') . '...' : $autorName;
                    }
                    $html .= '<option value="' . esc_attr($var->ID) . '">' . esc_html($autorName) . '</option>';
                }
                $html .= '</select>';
            } else {
                $html .= '<span class="b2s-re-post-author-notice">' . esc_html__('Only your own WordPress posts are available for resharing.', 'blog2social') . '</span>';
                $html .= ' <a href="#" class="b2s-re-post-author-info-modal-btn">' . esc_html__('Info', 'blog2social') . '</a>';
            }
        }
        return $html;
    }
}
