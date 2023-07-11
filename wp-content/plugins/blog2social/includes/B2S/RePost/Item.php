<?php

class B2S_RePost_Item {

    private $options;
    private $postTypesData;
    private $postCategoriesData;
    private $postAuthorData;

    public function __construct() {
        $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
        $this->postTypesData = get_post_types(array('public' => true));
        $this->postCategoriesData = get_categories();
        $this->postAuthorData = get_users();
    }

    public function getRePostOptionsHtml() {

        $isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? false : true;
        $limit = unserialize(B2S_PLUGIN_RE_POST_LIMIT);

        $content = '';
        $content .= '<h3 class="b2s-re-post-h3">' . esc_html__('Re-share your blog content automatically on your social media channels.', 'blog2social') . ((!$isPremium) ? ' <span class="label label-success">' . esc_html__('SMART', 'blog2social') . '</span>' : '') . '</h3>';
        $content .= '<div class="col-md-12 b2s-re-post-settings-header">';
        $content .= '<i class="glyphicon glyphicon-cog b2s-icon-size"></i><span class="b2s-re-post-headline"> ' . esc_html__('Settings', 'blog2social') . '</span><span class="b2s-re-post-headline"><i class="glyphicon glyphicon-chevron-up b2s-re-post-settings-toggle b2s-icon-size"></i></span>';
        $content .= '</div>';
        $content .= '<div class="col-md-12 b2s-re-post-settings-area">';
        $content .= '<form id="b2s-re-post-settings" class="b2s-pb-10 ' . ((!$isPremium) ? 'b2s-btn-disabled' : '') . '">';
        $content .= '<div class="row">';
        //Post Settings
        $content .= '<div class="col-md-12 col-lg-6">';
        $content .= '<h4>' . esc_html__('Which content should be shared?', 'blog2social') . '</h4>';
        $content .= '<div class="alert alert-info b2s-re-post-limit-info" style="display:none;"> <a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '" target="_blank">' . esc_html__('Upgrade', 'blog2social') . '</a> ' . esc_html__('your Blog2Social license to extend the quota for the number of posts in your queue.', 'blog2social') . '</div>';

        $content .= '<span>' . esc_html__('Number of posts', 'blog2social') . ' </span>';
        $content .= '<select name="b2s-re-post-limit" class="b2s-re-post-limit">';
        for ($i = 5; $i <= 100; $i = $i + 5) {
            $content .= '<option value="' . $i . '" data-limit="' . (($i <= $limit[B2S_PLUGIN_USER_VERSION]) ? '1' : '0') . '">' . $i . '</option>';
        }
        $content .= '</select>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<input type="radio" id="b2s-re-post-settings-option-1" name="b2s-re-post-settings-option" class="b2s-re-post-settings-option" checked value="0"><label for="b2s-re-post-settings-option-1" class="b2s-bold"> ' . esc_html__('share oldest posts first', 'blog2social') . '</label><br>';
        $content .= '<input type="radio" id="b2s-re-post-settings-option-2" name="b2s-re-post-settings-option" class="b2s-re-post-settings-option" value="1"><label for="b2s-re-post-settings-option-2" class="b2s-bold"> ' . esc_html__('customize', 'blog2social') . '</label><br>';
        $content .= '<div class="col-md-12 b2s-re-post-settings-customize-area">';
        $content .= $this->getChosenPostTypesData();
        $content .= '<br>';
        $content .= $this->getDateData();
        $content .= $this->getChosenPostCategoriesData();
        $content .= '<br>';
        $content .= $this->getChosenPostAuthorData();
        $content .= '<br>';
        $content .= '<input type="checkbox" name="b2s-re-post-favorites-active" id="b2s-re-post-favorites-active" value="1">';
        $content .= '<label for="b2s-re-post-favorites-active"> ' . sprintf(__('include <a href="%s" target="_blank">favorites posts</a> only', 'blog2social'), 'admin.php?page=blog2social-favorites') . ' </label>';
        $content .= '<br>';
        $content .= '<input type="checkbox" name="b2s-re-post-images-active" id="b2s-re-post-images-active" value="1">';
        $content .= '<label for="b2s-re-post-images-active"> ' . esc_html__('include posts with images only', 'blog2social') . ' </label>';
        $content .= '<br>';
        $content .= '<input type="checkbox" name="b2s-re-post-already-planed-active" id="b2s-re-post-already-planed-active" value="1">';
        $content .= '<label for="b2s-re-post-already-planed-active"> ' . sprintf(esc_html__('only posts that have been shared no more than %s times', 'blog2social'), '<input type="number" name="b2s-re-post-already-planed-count" class="b2s-re-post-number-input" value="1" min="1" max="50">') . ' </label>';
        $content .= '</div>';
        $content .= '</div>';

        //Time Settings
        $content .= '<div class="col-md-12 col-lg-6">';
        $content .= '<h4>' . esc_html__('When should your content be shared?', 'blog2social') . '</h4>';
        $content .= '<input type="radio" class="b2s-re-post-share-option" id="b2s-re-post-share-option-0" name="b2s-re-post-share-option" checked value="0">';
        $content .= '<div class="b2s-re-post-share-option-area">';
        $content .= '<label for="b2s-re-post-share-option-0"><span>' . esc_html__('Post every', 'blog2social') . ' </span><input type="number" name="b2s-re-post-day-0" class="b2s-re-post-number-input" value="1" min="1" max="30"><span> ' . esc_html__('days at', 'blog2social') . '   </span><input name="b2s-re-post-input-time-0" class="b2s-re-post-input-time form-control"></label>';
        $content .= '</div>';
        $content .= '<br>';
        $content .= '<input type="radio" style="display:none;">';
        $content .= '<div class="b2s-re-post-share-option-area b2s-mt-10 b2s-ml-22">';
        $content .= '<span>' . esc_html__('on', 'blog2social') . '   </span>';
        $content .= '<input id="b2s-re-post-weekday-mo" name="b2s-re-post-weekday-1" type="checkbox" class="form-control b2s-re-post-weekday" value="1" checked><label for="b2s-re-post-weekday-mo" class="b2s-re-post-weekday-label"> ' . esc_html__('Mon', 'blog2social') . '</label>'; //MO
        $content .= '<input id="b2s-re-post-weekday-di" name="b2s-re-post-weekday-2" type="checkbox" class="form-control b2s-re-post-weekday" value="1" checked><label for="b2s-re-post-weekday-di" class="b2s-re-post-weekday-label"> ' . esc_html__('Tue', 'blog2social') . '</label>'; //Di
        $content .= '<input id="b2s-re-post-weekday-mi" name="b2s-re-post-weekday-3" type="checkbox" class="form-control b2s-re-post-weekday" value="1" checked><label for="b2s-re-post-weekday-mi" class="b2s-re-post-weekday-label"> ' . esc_html__('Wed', 'blog2social') . '</label>'; //Mi
        $content .= '<input id="b2s-re-post-weekday-do" name="b2s-re-post-weekday-4" type="checkbox" class="form-control b2s-re-post-weekday" value="1" checked><label for="b2s-re-post-weekday-do" class="b2s-re-post-weekday-label"> ' . esc_html__('Thu', 'blog2social') . '</label>'; //Do
        $content .= '<input id="b2s-re-post-weekday-fr" name="b2s-re-post-weekday-5" type="checkbox" class="form-control b2s-re-post-weekday" value="1" checked><label for="b2s-re-post-weekday-fr" class="b2s-re-post-weekday-label"> ' . esc_html__('Fri', 'blog2social') . '</label>'; //Fr
        $content .= '<input id="b2s-re-post-weekday-sa" name="b2s-re-post-weekday-6" type="checkbox" class="form-control b2s-re-post-weekday" value="1" checked><label for="b2s-re-post-weekday-sa" class="b2s-re-post-weekday-label"> ' . esc_html__('Sat', 'blog2social') . '</label>'; //Sa
        $content .= '<input id="b2s-re-post-weekday-so" name="b2s-re-post-weekday-0" type="checkbox" class="form-control b2s-re-post-weekday" value="1" checked><label for="b2s-re-post-weekday-so" class="b2s-re-post-weekday-label"> ' . esc_html__('Sun', 'blog2social') . '</label>'; //So
        $content .= '</div>';
        $content .= '<br>';
        $content .= '<input type="radio" class="b2s-re-post-share-option" id="b2s-re-post-share-option-1" name="b2s-re-post-share-option" value="1">';
        $content .= '<div class="b2s-re-post-share-option-area b2s-mt-12">';
        $content .= '<label for="b2s-re-post-share-option-1"><span>' . esc_html__('Post every', 'blog2social') . ' </span><input type="number" name="b2s-re-post-day-1" class="b2s-re-post-number-input" value="1" min="1" max="10">';
        $content .= '<select class="b2s-re-post-weekday-select" name="b2s-re-post-weekday-select">';
        $content .= '<option value="monday">' . esc_html__('Monday', 'blog2social') . '</option>';
        $content .= '<option value="tuesday">' . esc_html__('Tuesday', 'blog2social') . '</option>';
        $content .= '<option value="wednesday">' . esc_html__('Wednesday', 'blog2social') . '</option>';
        $content .= '<option value="thursday">' . esc_html__('Thursday', 'blog2social') . '</option>';
        $content .= '<option value="friday">' . esc_html__('Friday', 'blog2social') . '</option>';
        $content .= '<option value="saturday">' . esc_html__('Saturday', 'blog2social') . '</option>';
        $content .= '<option value="sunday">' . esc_html__('Sunday', 'blog2social') . '</option>';
        $content .= '</select>';
        $content .= '<span> ' . esc_html__('at', 'blog2social') . '   </span><input name="b2s-re-post-input-time-1" class="b2s-re-post-input-time form-control">';
        $content .= '</label>';
        $content .= '</div>';
        $content .= '<br>';
        $content .= '<div class="b2s-mt-12">';
        $content .= '<input type="checkbox" name="b2s-re-post-best-times-active" id="b2s-re-post-best-times-active" value="1">';
        $content .= '<label class="b2s-re-post-share-option-area" for="b2s-re-post-best-times-active">' . esc_html__('at my best times', 'blog2social') . ' </label>';
        $content .= '</div>';
        $content .= '<br>';
        $content .= '<br>';
        $content .= '<br>';

        //Network Settings
        $content .= '<h4>' . esc_html__('Where should your content be shared?', 'blog2social') . '</h4>';
        $content .= $this->getMandantSelect();
        $content .= '<input type="button" class="btn btn-primary pull-right ' . ((!$isPremium) ? 'b2s-re-post-submit-premium' : 'b2s-re-post-submit-btn') . '" value="' . esc_html__('Add to queue', 'blog2social') . '">';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<input type="hidden" id="b2sUserLang" name="b2s-user-lang" value="' . esc_attr(strtolower(substr(get_locale(), 0, 2))) . '">';
        $content .= '</form>';
        $content .= '</div>';

        return $content;
    }

    public function getRePostQueueHtml() {
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
        $postItem = new B2S_Post_Item('repost');
        $postItem->currentPage = 1;
        $limit = unserialize(B2S_PLUGIN_RE_POST_LIMIT);
        $needMoreBtn = (B2S_PLUGIN_USER_VERSION <= 2) ? '<a class="b2s-info-btn" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '" target="_blank">' . esc_html__('Need more?', 'blog2social') . '</a>' : '';
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
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getProfileUserAuth', 'token' => B2S_PLUGIN_TOKEN)));
        if (isset($result->result) && (int) $result->result == 1 && isset($result->data) && !empty($result->data) && isset($result->data->mandant) && isset($result->data->auth) && !empty($result->data->mandant) && !empty($result->data->auth)) {
            /*
             * since V7.0 Remove Video Networks
             */
            $isVideoNetwork = unserialize(B2S_PLUGIN_NETWORK_SUPPORT_VIDEO);
            foreach ($result->data->auth as $a => $auth) {
                foreach ($auth as $u => $item) {
                    if (in_array($item->networkId, $isVideoNetwork)) {
                        if (!in_array($item->networkId, array(1, 2, 6, 12, 38, 39))) {
                            unset($result->data->auth->$u);                        }
                    }
                }
            }
            $mandant = $result->data->mandant;
            $auth = $result->data->auth;
            $authContent = '';
            $content = '<div class="row"><div class="col-md-6 b2s-re-post-profile"><label for="b2s-re-post-profil-dropdown">' . esc_html__('Select network collection:', 'blog2social') . '</label><a class="b2s-network-info-modal-btn pull-right" href="#">' . esc_html__('Info', 'blog2social') . '</a>
                <select class="b2s-w-100" id="b2s-re-post-profil-dropdown" name="b2s-re-post-profil-dropdown">';
            foreach ($mandant as $k => $m) {
                $content .= '<option value="' . esc_attr($m->id) . '" ' . (((int) $m->id == (int) $mandantId) ? 'selected' : '') . '>' . esc_html((($m->id == 0) ? __($m->name, 'blog2social') : $m->name)) . '</option>';
                $profilData = (isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0])) ? json_encode($auth->{$m->id}) : '';
                $authContent .= "<input type='hidden' name='b2s-re-post-profil-data-" . esc_attr($m->id) . "' id='b2s-re-post-profil-data-" . esc_attr($m->id) . "' value='" . base64_encode($profilData) . "'/>";
            }
            $content .= '</select><div class="pull-right hidden-sm hidden-xs"><a href="' . esc_url(get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-network') . '" target="_blank">' . esc_html__('Network settings', 'blog2social') . '</a></div></div>';
            $content .= $authContent;

            //TOS Twitter 032018 - none multiple Accounts - User select once
            $content .= '<div class="col-md-6 b2s-re-post-twitter-profile"><label for="b2s-re-post-profil-dropdown-twitter">' . esc_html__('Select Twitter profile:', 'blog2social') . '</label> <select class="b2s-w-100" id="b2s-re-post-profil-dropdown-twitter" name="b2s-re-post-profil-dropdown-twitter">';
            foreach ($mandant as $k => $m) {
                if ((isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0]))) {
                    foreach ($auth->{$m->id} as $key => $value) {
                        if ($value->networkId == 2) {
                            $content .= '<option data-mandant-id="' . esc_attr($m->id) . '" value="' . esc_attr($value->networkAuthId) . '" ' . (((int) $value->networkAuthId == (int) $twitterId) ? 'selected' : '') . '>' . esc_html($value->networkUserName) . '</option>';
                        }
                    }
                }
            }
            $content .= '</select><div class="pull-right hidden-sm hidden-xs"><a href="#" class="b2sTwitterInfoModalBtn">' . esc_html__('Info', 'blog2social') . '</a></div></div></div>';
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
        $html .= '<label for="b2s-re-post-date-active"> ' . esc_html__('Date', 'blog2social') . ' </label>';
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

    private function getChosenPostAuthorData() {

        $html = '';
        if (is_array($this->postAuthorData) && !empty($this->postAuthorData)) {
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
        }
        return $html;
    }

}
