<?php
    $actions = $this->settings_obj;
    $loader_iamge = "<span class='display_none ays_quiz_loader_box'><img src='". AYS_QUIZ_ADMIN_URL ."/images/loaders/loading.gif'></span>";

    if( isset( $_REQUEST['ays_submit'] ) ){
        $actions->store_data($_REQUEST);
    }
    if(isset($_GET['ays_quiz_tab'])){
        $ays_quiz_tab = esc_attr( $_GET['ays_quiz_tab'] );
    }else{
        $ays_quiz_tab = 'tab1';
    }

    if(isset($_GET['action']) && $_GET['action'] == 'update_duration'){
        $actions->update_duration_data();
    }

    $data = $actions->get_data();
    $db_data = $actions->get_db_data();
    $options = ($actions->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes( $actions->ays_get_setting('options') ), true);

    $paypal_client_id = isset($data['paypal_client_id']) ? $data['paypal_client_id'] : '';
    $paypal_payment_terms = isset($data['payment_terms']) ? $data['payment_terms'] : 'lifetime';
    $data['extra_check'] = !isset( $data['extra_check'] ) ? 'off' : $data['extra_check'];
    $paypal_extra_check = isset( $data['extra_check'] ) && $data['extra_check'] == 'on' ? true : false;
    $paypal_subscribtion_duration = isset( $data['subscribtion_duration'] ) && $data['subscribtion_duration'] != '' ? absint( $data['subscribtion_duration'] ) : '';
    $paypal_subscribtion_duration_by = isset( $data['subscribtion_duration_by'] ) && $data['subscribtion_duration_by'] != '' ? $data['subscribtion_duration_by'] : 'day';

    // Stripe integration
    $stripe_res           = ($actions->ays_get_setting('stripe') === false) ? json_encode(array()) : $actions->ays_get_setting('stripe');
    $stripe               = json_decode($stripe_res, true);
    $stripe_secret_key    = isset($stripe['secret_key']) ? $stripe['secret_key'] : '';
    $stripe_api_key       = isset($stripe['api_key']) ? $stripe['api_key'] : '';
    $stripe_payment_terms = isset($stripe['payment_terms']) ? $stripe['payment_terms'] : 'lifetime' ;


    global $wp_roles;
    $ays_users_roles = $wp_roles->role_names;
    $user_roles = json_decode($actions->ays_get_setting('user_roles'), true);
    $mailchimp_res = ($actions->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $actions->ays_get_setting('mailchimp');
    $mailchimp = json_decode($mailchimp_res, true);
    $mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '' ;
    $mailchimp_api_key = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '' ;

    $monitor_res     = ($actions->ays_get_setting('monitor') === false) ? json_encode(array()) : $actions->ays_get_setting('monitor');
    $monitor         = json_decode($monitor_res, true);
    $monitor_client  = isset($monitor['client']) ? $monitor['client'] : '';
    $monitor_api_key = isset($monitor['apiKey']) ? $monitor['apiKey'] : '';

    $zapier_res  = ($actions->ays_get_setting('zapier') === false) ? json_encode(array()) : $actions->ays_get_setting('zapier');
    $zapier      = json_decode($zapier_res, true);
    $zapier_hook = isset($zapier['hook']) ? $zapier['hook'] : '';

    $active_camp_res     = ($actions->ays_get_setting('active_camp') === false) ? json_encode(array()) : $actions->ays_get_setting('active_camp');
    $active_camp         = json_decode($active_camp_res, true);
    $active_camp_url     = isset($active_camp['url']) ? $active_camp['url'] : '';
    $active_camp_api_key = isset($active_camp['apiKey']) ? $active_camp['apiKey'] : '';

    $slack_res    = ($actions->ays_get_setting('slack') === false) ? json_encode(array()) : $actions->ays_get_setting('slack');
    $slack        = json_decode($slack_res, true);
    $slack_client = isset($slack['client']) ? $slack['client'] : '';
    $slack_secret = isset($slack['secret']) ? $slack['secret'] : '';
    $slack_token = isset($slack['token']) ? $slack['token'] : '';
    $slack_oauth  = !empty($_GET['oauth']) && $_GET['oauth'] == 'slack';
    if ($slack_oauth) {
        $slack_temp_code = !empty($_GET['code']) ? $_GET['code'] : "";
        $slack_client    = !empty($_GET['state']) ? $_GET['state'] : "";
        $ays_quiz_tab    = 'tab2';
    }

    // Google sheets Xcho
    $google_res          = ($actions->ays_get_setting('google') === false) ? json_encode(array()) : $actions->ays_get_setting('google');
    $google_sheets       = json_decode($google_res, true);
    $google_client       = isset($google_sheets['client']) ? $google_sheets['client'] : '';
    $google_secret       = isset($google_sheets['secret']) ? $google_sheets['secret'] : '';
    $google_redirect_uri = isset($google_sheets['redirect_uri']) ? $google_sheets['redirect_uri'] : '';
    $google_token        = isset($google_sheets['token']) ? $google_sheets['token'] : '';
    $google_redirect_url = menu_page_url("quiz-maker-settings", false);

    $google_code  = !empty($_GET['code']) ? $_GET['code'] : "";
    $google_scope  = !empty($_GET['scope']) ? $_GET['scope'] : "";
    $google_code_check  = !empty($_GET['code']) && !isset($_GET['oauth']) ? true : false;

    if( $google_code && $google_scope ){
        $ays_quiz_tab = 'tab2';
    }

    if( isset( $_REQUEST['ays_disconnect_google_sheets'] ) ){
        $result = $actions->ays_update_setting('google', '');
        Quiz_Maker_Data::delete_quiz_sheet_ids();

        $url = menu_page_url("quiz-maker-settings", false);
        $url = add_query_arg( array(
            'ays_quiz_tab' => 'tab2',
            'status' => 'gdisconnected'
        ), $url );
        wp_redirect( $url );
        exit();
    }

    if( isset( $_REQUEST['ays_googleOAuth2'] ) ){

        // Google sheets
        $gclient_id = isset($_REQUEST['ays_google_client']) && $_REQUEST['ays_google_client'] != '' ? $_REQUEST['ays_google_client'] : '';
        $gclient_secret = isset($_REQUEST['ays_google_secret']) && $_REQUEST['ays_google_secret'] != '' ? $_REQUEST['ays_google_secret'] : '';
        $gredirect_url = isset($_REQUEST['ays_google_redirect']) && $_REQUEST['ays_google_redirect'] != '' ? $_REQUEST['ays_google_redirect'] : '';
        $google_sheets = array(
            'client' => $gclient_id,
            'secret' => $gclient_secret,
            'redirect_uri' => $gredirect_url,
        );
        $result = $actions->ays_update_setting('google', json_encode($google_sheets));

        $scopes = array(
            'https://www.googleapis.com/auth/spreadsheets',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/userinfo.email',
        );
        $glogin_url = 'https://accounts.google.com/o/oauth2/v2/auth?scope=' .
            urlencode( implode( ' ', $scopes ) ) .
            '&redirect_uri=' . urlencode( $gredirect_url ) . '&response_type=code&client_id=' . $gclient_id . '&access_type=offline&prompt=consent';

        wp_redirect( $glogin_url );
        exit();
    }

    $gerror_message = '';
    // Google passes a parameter 'code' in the Redirect Url
    if( $google_code && $google_scope ) {
        try {
            // Get the access token
            $gtokens = Quiz_Maker_Data::GetGoogleUserToken_RefreshToken($google_client, $google_redirect_url, $google_secret, $_GET['code']);

            // Access Token
            $gaccess_token = $gtokens['access_token'];

            // Get user information
            $google_user_info = Quiz_Maker_Data::GetGoogleUserProfileInfo( $gaccess_token );

            $google_sheets = array(
                'client' => $google_client,
                'secret' => $google_secret,
                'redirect_uri' => $google_redirect_uri,
                'token' => $gaccess_token,
                'refresh_token' => $gtokens['refresh_token'],
                'user_email' => $google_user_info['email'],
                'user_name' => $google_user_info['name'],
                'user_picture' => $google_user_info['picture'],
                'user_gid' => $google_user_info['id'],
            );

            $result = $actions->ays_update_setting('google', json_encode($google_sheets));
            $url = menu_page_url("quiz-maker-settings", false);
            $url = add_query_arg( array(
                'ays_quiz_tab' => 'tab2',
                'status' => 'gconnected'
            ), $url );
            wp_redirect( $url );
            exit();
        } catch(Exception $e) {
            $gerror_message = $e->getMessage();
        }
    }

    $google_res     = ($actions->ays_get_setting('google') === false) ? json_encode(array()) : $actions->ays_get_setting('google');
    $google_sheets  = json_decode($google_res, true);
    $google_email   = isset($google_sheets['user_email']) ? $google_sheets['user_email'] : '';
    $google_name    = isset($google_sheets['user_name']) ? $google_sheets['user_name'] : '';
    $google_picture = isset($google_sheets['user_picture']) ? $google_sheets['user_picture'] : '';
    $google_token   = isset($google_sheets['token']) ? $google_sheets['token'] : '';

    //Custom fields for shortcodes
    $custom_fields = Quiz_Maker_Data::get_custom_fields_for_shortcodes();

    //User page
    $user_page_custom_fields = isset($custom_fields['user_page']) && !empty($custom_fields['user_page']) ? $custom_fields['user_page'] : array();

    //User results
    $user_results_custom_fields = isset($custom_fields['user_results']) && !empty($custom_fields['user_results']) ? $custom_fields['user_results'] : array();

    //Quiz results
    $quiz_results_custom_fields = isset($custom_fields['quiz_results']) && !empty($custom_fields['quiz_results']) ? $custom_fields['quiz_results'] : array();

    // Individual Leaderboard
    $individual_leaderboard_custom_fields = isset($custom_fields['individual_leaderboard']) && !empty($custom_fields['individual_leaderboard']) ? $custom_fields['individual_leaderboard'] : array();

    // Leaderboard By Quiz Category
    $leaderboard_by_quiz_cat = isset($custom_fields['leaderboard_by_quiz_cat']) && !empty($custom_fields['leaderboard_by_quiz_cat']) ? $custom_fields['leaderboard_by_quiz_cat'] : array();


    // AV Leaderboard

    $leadboard_res = ($actions->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $actions->ays_get_setting('leaderboard');
    $leadboard = json_decode($leadboard_res, true);

    $ind_leadboard_count = isset($leadboard['individual']['count']) ? $leadboard['individual']['count'] : '5' ;
    $ind_leadboard_width = isset($leadboard['individual']['width']) ? $leadboard['individual']['width'] : '0' ;
    $ind_leadboard_orderby = isset($leadboard['individual']['orderby']) ? $leadboard['individual']['orderby'] : 'id' ;
    $ind_leadboard_sort = isset($leadboard['individual']['sort']) ? $leadboard['individual']['sort'] : 'avg' ;
    $ind_leadboard_color = isset($leadboard['individual']['color']) ? $leadboard['individual']['color'] : '#99BB5A' ;
    $ind_leadboard_suctom_css = (isset($leadboard['individual']['leadboard_custom_css']) && $leadboard['individual']['leadboard_custom_css'] != '') ? $leadboard['individual']['leadboard_custom_css'] : '';
    $ind_leadboard_points_display = (isset($leadboard['individual']['leadboard_points_display']) && $leadboard['individual']['leadboard_points_display'] != '') ? $leadboard['individual']['leadboard_points_display'] : 'without_max_point';

    // Enable pagination
    $leadboard['individual']['leadboard_enable_pagination'] = isset($leadboard['individual']['leadboard_enable_pagination']) ? sanitize_text_field( $leadboard['individual']['leadboard_enable_pagination'] ) : 'on';
    $leadboard_enable_pagination = (isset($leadboard['individual']['leadboard_enable_pagination']) && sanitize_text_field( $leadboard['individual']['leadboard_enable_pagination'] ) == "on") ? true : false;

    // Enable User Avatar
    $leadboard['individual']['leadboard_enable_user_avatar'] = isset($leadboard['individual']['leadboard_enable_user_avatar']) ? sanitize_text_field( $leadboard['individual']['leadboard_enable_user_avatar'] ) : 'off';
    $leadboard_enable_user_avatar = (isset($leadboard['individual']['leadboard_enable_user_avatar']) && sanitize_text_field( $leadboard['individual']['leadboard_enable_user_avatar'] ) == "on") ? true : false;

    $glob_leadboard_count = isset($leadboard['global']['count']) ? $leadboard['global']['count'] : '5' ;
    $glob_leadboard_width = isset($leadboard['global']['width']) ? $leadboard['global']['width'] : '0' ;
    $glob_leadboard_orderby = isset($leadboard['global']['orderby']) ? $leadboard['global']['orderby'] : 'id' ;
    $glob_leadboard_sort = isset($leadboard['global']['sort']) ? $leadboard['global']['sort'] : 'avg' ;
    $glob_leadboard_color = isset($leadboard['global']['color']) ? $leadboard['global']['color'] : '#99BB5A' ;
    $glob_leadboard_suctom_css = (isset($leadboard['global']['gleadboard_custom_css']) && $leadboard['global']['gleadboard_custom_css'] != '') ? $leadboard['global']['gleadboard_custom_css'] : '';

    // Enable pagination
    $leadboard['global']['leadboard_enable_pagination'] = isset($leadboard['global']['leadboard_enable_pagination']) ? sanitize_text_field( $leadboard['global']['leadboard_enable_pagination'] ) : 'on';
    $glob_leadboard_enable_pagination = (isset($leadboard['global']['leadboard_enable_pagination']) && sanitize_text_field( $leadboard['global']['leadboard_enable_pagination'] ) == "on") ? true : false;

    // Enable User Avatar
    $leadboard['global']['leadboard_enable_user_avatar'] = isset($leadboard['global']['leadboard_enable_user_avatar']) ? sanitize_text_field( $leadboard['global']['leadboard_enable_user_avatar'] ) : 'off';
    $glob_leadboard_enable_user_avatar = (isset($leadboard['global']['leadboard_enable_user_avatar']) && sanitize_text_field( $leadboard['global']['leadboard_enable_user_avatar'] ) == "on") ? true : false;

    //AV end

    $glob_quiz_cat_leadboard_count = isset($leadboard['global_quiz_cat']['count']) ? $leadboard['global_quiz_cat']['count'] : '5' ;
    $glob_quiz_cat_leadboard_width = isset($leadboard['global_quiz_cat']['width']) ? $leadboard['global_quiz_cat']['width'] : '0' ;
    $glob_quiz_cat_leadboard_orderby = isset($leadboard['global_quiz_cat']['orderby']) ? $leadboard['global_quiz_cat']['orderby'] : 'id' ;
    $glob_quiz_cat_leadboard_sort = isset($leadboard['global_quiz_cat']['sort']) ? $leadboard['global_quiz_cat']['sort'] : 'avg' ;
    $glob_quiz_cat_leadboard_color = isset($leadboard['global_quiz_cat']['color']) ? $leadboard['global_quiz_cat']['color'] : '#99BB5A' ;
    $glob_quiz_cat_leadboard_cuctom_css = (isset($leadboard['global_quiz_cat']['gleadboard_custom_css']) && $leadboard['global_quiz_cat']['gleadboard_custom_css'] != '') ? $leadboard['global_quiz_cat']['gleadboard_custom_css'] : '';

    // Enable pagination
    $leadboard['global_quiz_cat']['leadboard_enable_pagination'] = isset($leadboard['global_quiz_cat']['leadboard_enable_pagination']) ? sanitize_text_field( $leadboard['global_quiz_cat']['leadboard_enable_pagination'] ) : 'on';
    $glob_quiz_cat_leadboard_enable_pagination = (isset($leadboard['global_quiz_cat']['leadboard_enable_pagination']) && sanitize_text_field( $leadboard['global_quiz_cat']['leadboard_enable_pagination'] ) == "on") ? true : false;

    // Enable User Avatar
    $leadboard['global_quiz_cat']['leadboard_enable_user_avatar'] = isset($leadboard['global_quiz_cat']['leadboard_enable_user_avatar']) ? sanitize_text_field( $leadboard['global_quiz_cat']['leadboard_enable_user_avatar'] ) : 'off';
    $glob_quiz_cat_leadboard_enable_user_avatar = (isset($leadboard['global_quiz_cat']['leadboard_enable_user_avatar']) && sanitize_text_field( $leadboard['global_quiz_cat']['leadboard_enable_user_avatar'] ) == "on") ? true : false;

    $default_leadboard_columns = array(
        'pos' => 'pos',
        'name' => 'name',
        'score' => 'score',
        'duration' => 'duration',
        'points' => '',
    );

    $default_leadboard_column_names = array(
        "pos" => __( 'Pos.', $this->plugin_name ),
        "name" => __( 'Name', $this->plugin_name ),
        "score" => __( 'Score', $this->plugin_name ),
        "duration" => __( 'Duration', $this->plugin_name ),
        "points" => __( 'Points', $this->plugin_name ),
    );

    $ind_default_leadboard_columns = array(
        'pos' => 'pos',
        'name' => 'name',
        'score' => 'score',
        'duration' => 'duration',
        'points' => '',
    );

    $ind_default_leadboard_column_names = array(
        "pos" => __( 'Pos.', $this->plugin_name ),
        "name" => __( 'Name', $this->plugin_name ),
        "score" => __( 'Score', $this->plugin_name ),
        "duration" => __( 'Duration', $this->plugin_name ),
        "points" => __( 'Points', $this->plugin_name ),
    );
    
    if( !empty($individual_leaderboard_custom_fields) ){
        foreach ($individual_leaderboard_custom_fields as $custom_field_key => $custom_field) {
            $ind_default_leadboard_column_names[$custom_field_key] = $custom_field_key;
            $ind_default_leadboard_columns[$custom_field_key] = $custom_field_key;
        }
    }

    // Individual Leaderboard
    $leadboard['individual']['ind_leadboard_columns'] = ! isset( $leadboard['individual']['ind_leadboard_columns'] ) ? $ind_default_leadboard_columns : $leadboard['individual']['ind_leadboard_columns'];
    $ind_leadboard_columns = (isset( $leadboard['individual']['ind_leadboard_columns'] ) && !empty($leadboard['individual']['ind_leadboard_columns']) ) ? $leadboard['individual']['ind_leadboard_columns'] : array();
    $ind_leadboard_columns_order = (isset( $leadboard['individual']['ind_leadboard_columns_order'] ) && !empty($leadboard['individual']['ind_leadboard_columns_order']) ) ? $leadboard['individual']['ind_leadboard_columns_order'] : $ind_default_leadboard_columns;

    $ind_leadboard_columns_order_arr = $ind_leadboard_columns_order;

    foreach( $ind_default_leadboard_columns as $key => $value ){
        if( !isset( $ind_leadboard_columns[$key] ) ){
            $ind_leadboard_columns[$key] = '';
        }

        if( !isset( $ind_leadboard_columns_order[$key] ) ){
            $ind_leadboard_columns_order[$key] = $key;
        }

        if ( ! in_array( $key , $ind_leadboard_columns_order_arr) ) {
            $ind_leadboard_columns_order_arr[] = $key;
        }
    }

    foreach( $ind_leadboard_columns_order as $key => $value ){
        if( !isset( $ind_leadboard_columns[$key] ) ){
            if( isset( $ind_leadboard_columns[$value] ) ){
                $ind_leadboard_columns_order[$value] = $value;
            }
            unset( $ind_leadboard_columns_order[$key] );
        }
    }

    foreach ($ind_leadboard_columns_order_arr  as $key => $value) {
        if( isset( $ind_leadboard_columns_order[$value] ) ){
            $ind_leadboard_columns_order_arr[$value] = $value;
        }

        if ( is_int( $key ) ) {
            unset( $ind_leadboard_columns_order_arr[$key] );
        }
    }

    $ind_leadboard_columns_order = $ind_leadboard_columns_order_arr;

    // Global Leaderboard
    $leadboard['global']['glob_leadboard_columns'] = ! isset( $leadboard['global']['glob_leadboard_columns'] ) ? $default_leadboard_columns : $leadboard['global']['glob_leadboard_columns'];
    $glob_leadboard_columns = (isset( $leadboard['global']['glob_leadboard_columns'] ) && !empty($leadboard['global']['glob_leadboard_columns']) ) ? $leadboard['global']['glob_leadboard_columns'] : array();
    $glob_leadboard_columns_order = (isset( $leadboard['global']['glob_leadboard_columns_order'] ) && !empty($leadboard['global']['glob_leadboard_columns_order']) ) ? $leadboard['global']['glob_leadboard_columns_order'] : $default_leadboard_columns;

    $glob_quiz_cat_default_leadboard_columns = array(
        'pos' => 'pos',
        'name' => 'name',
        'score' => 'score',
        'duration' => 'duration',
        'points' => '',
    );

    $glob_quiz_cat_default_leadboard_column_names = array(
        "pos" => __( 'Pos.', $this->plugin_name ),
        "name" => __( 'Name', $this->plugin_name ),
        "score" => __( 'Score', $this->plugin_name ),
        "duration" => __( 'Duration', $this->plugin_name ),
        "points" => __( 'Points', $this->plugin_name ),
    );

    if( !empty($leaderboard_by_quiz_cat) ){
        foreach ($leaderboard_by_quiz_cat as $custom_field_key => $custom_field) {
            $glob_quiz_cat_default_leadboard_column_names[$custom_field_key] = $custom_field_key;
            $glob_quiz_cat_default_leadboard_columns[$custom_field_key] = $custom_field_key;
        }
    }

    //  Quiz Cat Leaderboard
    $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] = ! isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] ) ? $default_leadboard_columns : $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'];
    $glob_quiz_cat_leadboard_columns = (isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] ) && !empty($leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns']) ) ? $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] : array();
    $glob_quiz_cat_leadboard_columns_order = (isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns_order'] ) && !empty($leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns_order']) ) ? $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns_order'] : $glob_quiz_cat_default_leadboard_columns;

    $glob_quiz_cat_leadboard_columns_order_arr = $glob_quiz_cat_leadboard_columns_order;

    foreach( $glob_quiz_cat_default_leadboard_columns as $key => $value ){
        if( !isset( $glob_quiz_cat_leadboard_columns[$key] ) ){
            $glob_quiz_cat_leadboard_columns[$key] = '';
        }

        if( !isset( $glob_quiz_cat_leadboard_columns_order[$key] ) ){
            $glob_quiz_cat_leadboard_columns_order[$key] = $key;
        }

        if ( ! in_array( $key , $glob_quiz_cat_leadboard_columns_order_arr) ) {
            $glob_quiz_cat_leadboard_columns_order_arr[] = $key;
        }
    }

    foreach( $glob_quiz_cat_leadboard_columns_order as $key => $value ){
        if( !isset( $glob_quiz_cat_leadboard_columns[$key] ) ){
            if( isset( $glob_quiz_cat_leadboard_columns[$value] ) ){
                $glob_quiz_cat_leadboard_columns_order[$value] = $value;
            }
            unset( $glob_quiz_cat_leadboard_columns_order[$key] );
        }
    }

    foreach ($glob_quiz_cat_leadboard_columns_order_arr  as $key => $value) {
        if( isset( $glob_quiz_cat_leadboard_columns_order[$value] ) ){
            $glob_quiz_cat_leadboard_columns_order_arr[$value] = $value;
        }

        if ( is_int( $key ) ) {
            unset( $glob_quiz_cat_leadboard_columns_order_arr[$key] );
        }
    }

    $glob_quiz_cat_leadboard_columns_order = $glob_quiz_cat_leadboard_columns_order_arr;

    if( !empty($leaderboard_by_quiz_cat) ){
        foreach ($leaderboard_by_quiz_cat as $custom_field_key => $custom_field_value) {
            $glob_quiz_cat_default_leadboard_columns[$custom_field_key] = $custom_field_key;
            $glob_quiz_cat_leadboard_columns_order[$custom_field_key] = $custom_field_key;
        }
    }


    $quizzes = $actions->get_reports_titles();
    $empry_dur_count = $actions->get_empty_duration_rows_count();

    $question_types = array(
        "radio"             => __("Radio", $this->plugin_name),
        "checkbox"          => __("Checkbox( Multiple )", $this->plugin_name),
        "select"            => __("Dropdown", $this->plugin_name),
        "text"              => __("Text", $this->plugin_name),
        "short_text"        => __("Short Text", $this->plugin_name),
        "number"            => __("Number", $this->plugin_name),
        "date"              => __("Date", $this->plugin_name),
        "true_or_false"     => __("True/False", $this->plugin_name),
        "custom"            => __("Custom (Banner)", $this->plugin_name),
        "fill_in_blank"     => __("Fill in the blanks", $this->plugin_name),
        "matching"          => __("Matching", $this->plugin_name),
    );

    $question_types_icon_url = array(
        "radio"             => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-radio-type.svg",
        "checkbox"          => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-checkbox-type.svg",
        "select"            => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-dropdown-type.svg",
        "text"              => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-text-type.svg",
        "short_text"        => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-short-text-type.svg",
        "number"            => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-number-type.svg",
        "date"              => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-date-type.svg",
        "true_or_false"     => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-true-or-false-type.svg",
        "custom"            => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-custom-type.svg",
        "fill_in_blank"     => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-fill-in-blank-type.svg",
        "matching"          => AYS_QUIZ_ADMIN_URL ."/images/QuestionTypes/quiz-maker-matching-type.svg",
    );

    $options['question_default_type'] = !isset($options['question_default_type']) ? 'radio' : $options['question_default_type'];
    $question_default_type = isset($options['question_default_type']) ? $options['question_default_type'] : '';

    // Default Category
    $question_default_cat = isset($options['question_default_cat']) && $options['question_default_cat'] != '' ? absint(intval($options['question_default_cat'])) : 0;
    $question_categories = Quiz_Maker_Data::get_question_categories();


    $options['ays_show_result_report'] = !isset( $options['ays_show_result_report'] ) ? 'on' : $options['ays_show_result_report'];
//    $show_result_report = ( isset( $options['ays_show_result_report'] ) && $options['ays_show_result_report'] == 'on' ) ? 'on' : 'off';
//    $show_result_report = ( isset( $options['ays_show_result_report'] ) && $options['ays_show_result_report'] != 'on' ) ? 'off' : 'on';
    $ays_answer_default_count = isset($options['ays_answer_default_count']) ? $options['ays_answer_default_count'] : '3';

    if ( $question_default_type == 'true_or_false' ) {
        $ays_answer_default_count = 2;
    }

    $right_answer_sound = isset($options['right_answer_sound']) ? $options['right_answer_sound'] : '';
    $wrong_answer_sound = isset($options['wrong_answer_sound']) ? $options['wrong_answer_sound'] : '';

    $default_user_page_columns = array(
        'quiz_name' => 'quiz_name',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
        'duration' => 'duration',
        'score' => 'score',
        'points' => '',
        'download_certificate' => '',
        'details' => 'details',
    );

    if( !empty($user_page_custom_fields) ){
        foreach ($user_page_custom_fields as $custom_field => $value) {
            $default_user_page_columns[$custom_field] = $custom_field;
        }
    }

    $options['user_page_columns'] = ! isset( $options['user_page_columns'] ) ? $default_user_page_columns : $options['user_page_columns'];
    $user_page_columns = (isset( $options['user_page_columns'] ) && !empty($options['user_page_columns']) ) ? $options['user_page_columns'] : array();
    $user_page_columns_order = (isset( $options['user_page_columns_order'] ) && !empty($options['user_page_columns_order']) ) ? $options['user_page_columns_order'] : $default_user_page_columns;

    foreach( $default_user_page_columns as $key => $value ){
        if( !isset( $user_page_columns[$key] ) ){
            $user_page_columns[$key] = '';
        }

        if( !isset( $user_page_columns_order[$key] ) ){
            $user_page_columns_order[$key] = $key;
        }
    }

    foreach( $user_page_columns_order as $key => $value ){
        if( !isset( $user_page_columns[$key] ) ){
            if( isset( $user_page_columns[$value] ) ){
                $user_page_columns_order[$value] = $value;
            }
            unset( $user_page_columns_order[$key] );
        }
    }

    $default_user_page_column_names = array(
        "quiz_name" => __( 'Quiz name', $this->plugin_name ),
        "start_date" => __( 'Start date', $this->plugin_name ),
        "end_date" => __( 'End date', $this->plugin_name ),
        "duration" => __( 'Duration', $this->plugin_name ),
        "score" => __( 'Score', $this->plugin_name ),
        "details" => __( 'Details', $this->plugin_name ),
        "download_certificate" => __( 'Certificate', $this->plugin_name ),
        "points" => __( 'Points', $this->plugin_name ),
    );

    if( !empty($user_page_custom_fields) ){
        foreach ($user_page_custom_fields as $custom_field_key => $custom_field_value) {
            $default_user_page_column_names[$custom_field_key] = $custom_field_value;
        }
    }

    // Aro Buttons Text

    $buttons_texts_res      = ($actions->ays_get_setting('buttons_texts') === false) ? json_encode(array()) : $actions->ays_get_setting('buttons_texts');
    $buttons_texts          = json_decode( $buttons_texts_res, true, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );

    $start_button           = (isset($buttons_texts['start_button']) && $buttons_texts['start_button'] != '') ? esc_attr(stripslashes($buttons_texts['start_button'])) : 'Start' ;
    $next_button            = (isset($buttons_texts['next_button']) && $buttons_texts['next_button'] != '') ? esc_attr(stripslashes($buttons_texts['next_button'])) : 'Next' ;
    $previous_button        = (isset($buttons_texts['previous_button']) && $buttons_texts['previous_button'] != '') ? esc_attr(stripslashes($buttons_texts['previous_button'])) : 'Prev' ;
    $clear_button           = (isset($buttons_texts['clear_button']) && $buttons_texts['clear_button'] != '') ? esc_attr(stripslashes($buttons_texts['clear_button'])) : 'Clear' ;
    $finish_button          = (isset($buttons_texts['finish_button']) && $buttons_texts['finish_button'] != '') ? esc_attr(stripslashes($buttons_texts['finish_button'])) : 'Finish' ;
    $see_result_button      = (isset($buttons_texts['see_result_button']) && $buttons_texts['see_result_button'] != '') ? esc_attr(stripslashes($buttons_texts['see_result_button'])) : 'See Result' ;
    $restart_quiz_button    = (isset($buttons_texts['restart_quiz_button']) && $buttons_texts['restart_quiz_button'] != '') ? esc_attr(stripslashes($buttons_texts['restart_quiz_button'])) : 'Restart quiz' ;
    $send_feedback_button   = (isset($buttons_texts['send_feedback_button']) && $buttons_texts['send_feedback_button'] != '') ? esc_attr(stripslashes($buttons_texts['send_feedback_button'])) : 'Send feedback' ;
    $load_more_button       = (isset($buttons_texts['load_more_button']) && $buttons_texts['load_more_button'] != '') ? esc_attr(stripslashes($buttons_texts['load_more_button'])) : 'Load more' ;
    $exit_button            = (isset($buttons_texts['exit_button']) && $buttons_texts['exit_button'] != '') ? esc_attr(stripslashes($buttons_texts['exit_button'])) : 'Exit' ;
    $check_button           = (isset($buttons_texts['check_button']) && $buttons_texts['check_button'] != '') ? esc_attr(stripslashes($buttons_texts['check_button'])) : 'Check' ;
    $login_button           = (isset($buttons_texts['login_button']) && $buttons_texts['login_button'] != '') ? esc_attr(stripslashes($buttons_texts['login_button'])) : 'Log In' ;

    //Aro end

    //Questions title length
    $question_title_length = (isset($options['question_title_length']) && intval($options['question_title_length']) != 0) ? absint(intval($options['question_title_length'])) : 5;
    if($question_title_length == 0){
        $question_title_length = 5;
    }

    //Quizzes title length
    $quizzes_title_length = (isset($options['quizzes_title_length']) && intval($options['quizzes_title_length']) != 0) ? absint(intval($options['quizzes_title_length'])) : 5;
    if($quizzes_title_length == 0){
        $quizzes_title_length = 5;
    }

    //Results title length
    $results_title_length = (isset($options['results_title_length']) && intval($options['results_title_length']) != 0) ? absint(intval($options['results_title_length'])) : 5;
    if($results_title_length == 0){
        $results_title_length = 5;
    }

    // Question category title length
    $question_categories_title_length = (isset($options['question_categories_title_length']) && intval($options['question_categories_title_length']) != 0) ? absint(intval($options['question_categories_title_length'])) : 5;
    if($question_categories_title_length == 0){
        $question_categories_title_length = 5;
    }

    // Quiz category title length
    $quiz_categories_title_length = (isset($options['quiz_categories_title_length']) && intval($options['quiz_categories_title_length']) != 0) ? absint(intval($options['quiz_categories_title_length'])) : 5;

    // Reviews title length
    $quiz_reviews_title_length = (isset($options['quiz_reviews_title_length']) && intval($options['quiz_reviews_title_length']) != 0) ? absint(intval($options['quiz_reviews_title_length'])) : 5;

    // Do not store IP adressess
    $options['disable_user_ip'] = isset($options['disable_user_ip']) ? $options['disable_user_ip'] : 'off';
    $disable_user_ip = (isset($options['disable_user_ip']) && $options['disable_user_ip'] == "on") ? true : false;

    //default all results column
    $default_all_results_columns = array(
        'user_name'    => 'user_name',
        'quiz_name'    => 'quiz_name',
        'start_date'   => 'start_date',
        'end_date'     => 'end_date',
        'duration'     => 'duration',
        'score'        => 'score',
        'status'       => '',
        'user_email'   => '',
        // 'details' => 'details',
    );

    if( !empty($user_results_custom_fields) ){
        foreach ($user_results_custom_fields as $custom_field_key => $custom_field) {
            $default_all_results_columns[$custom_field_key] = $custom_field_key;
        }
    }

    $options['all_results_columns'] = ! isset( $options['all_results_columns'] ) ? $default_all_results_columns : $options['all_results_columns'];
    $all_results_columns = (isset( $options['all_results_columns'] ) && !empty($options['all_results_columns']) ) ? $options['all_results_columns'] : array();
    $all_results_columns_order = (isset( $options['all_results_columns_order'] ) && !empty($options['all_results_columns_order']) ) ? $options['all_results_columns_order'] : $default_all_results_columns;

    $all_results_columns_order_arr = $all_results_columns_order;

    foreach( $default_all_results_columns as $key => $value ){
        if( !isset( $all_results_columns[$key] ) ){
            $all_results_columns[$key] = '';
        }

        if( !isset( $all_results_columns_order[$key] ) ){
            $all_results_columns_order[$key] = $key;
        }

        if ( ! in_array( $key , $all_results_columns_order_arr) ) {
            $all_results_columns_order_arr[] = $key;
        }
    }

    foreach( $all_results_columns_order as $key => $value ){
        if( !isset( $all_results_columns[$key] ) ){
            if( isset( $all_results_columns[$value] ) ){
                $all_results_columns_order[$value] = $value;
            }
            unset( $all_results_columns_order[$key] );
        }
    }

    foreach ($all_results_columns_order_arr  as $key => $value) {
        if( isset( $all_results_columns_order[$value] ) ){
            $all_results_columns_order_arr[$value] = $value;
        }

        if ( is_int( $key ) ) {
            unset( $all_results_columns_order_arr[$key] );
        }
    }

    $all_results_columns_order = $all_results_columns_order_arr;

    $default_all_results_column_names = array(
        "user_name"  => __( 'User name', $this->plugin_name),
        "quiz_name"  => __( 'Quiz name', $this->plugin_name ),
        "start_date" => __( 'Start date',$this->plugin_name ),
        "end_date"   => __( 'End date',  $this->plugin_name ),
        "duration"   => __( 'Duration',  $this->plugin_name ),
        "score"      => __( 'Score',     $this->plugin_name ),
        "status"     => __( 'Status',    $this->plugin_name ),
        "user_email" => __( 'Email',    $this->plugin_name ),
        // "details" => __( 'Details', $this->plugin_name )
    );

    if( !empty($user_results_custom_fields) ){
        foreach ($user_results_custom_fields as $custom_field_key => $custom_field_value) {
            $default_all_results_column_names[$custom_field_key] = $custom_field_value;
        }
    }

    // Show publicly ( All Results )
    $options['all_results_show_publicly'] = isset($options['all_results_show_publicly']) ? $options['all_results_show_publicly'] : 'off';
    $all_results_show_publicly = (isset($options['all_results_show_publicly']) && $options['all_results_show_publicly'] == "on") ? true : false;

    // Show publicly ( Single Quiz Results )
    $options['quiz_all_results_show_publicly'] = isset($options['quiz_all_results_show_publicly']) ? $options['quiz_all_results_show_publicly'] : 'off';
    $quiz_all_results_show_publicly = (isset($options['quiz_all_results_show_publicly']) && $options['quiz_all_results_show_publicly'] == "on") ? true : false;

    // Keyword default count
    $keyword_default_max_value = (isset($options['keyword_default_max_value']) && $options['keyword_default_max_value'] != '') ? absint(intval($options['keyword_default_max_value'])) : 6;

    // Animation Top
    $quiz_animation_top = (isset($options['quiz_animation_top']) && $options['quiz_animation_top'] != '') ? absint(intval($options['quiz_animation_top'])) : 100 ;
    $options['quiz_enable_animation_top'] = isset($options['quiz_enable_animation_top']) ? $options['quiz_enable_animation_top'] : 'on';
    $quiz_enable_animation_top = (isset($options['quiz_enable_animation_top']) && $options['quiz_enable_animation_top'] == "on") ? true : false;

    // Default quiz all results column
    $default_quiz_all_results_columns = array(
        'user_name'    => 'user_name',
        'start_date'   => 'start_date',
        'end_date'     => 'end_date',
        'duration'     => 'duration',
        'score'        => 'score',
    );

    $default_quiz_all_results_column_names = array(
        "user_name"  => __( 'User name', $this->plugin_name ),
        "start_date" => __( 'Start date',$this->plugin_name ),
        "end_date"   => __( 'End date',  $this->plugin_name ),
        "duration"   => __( 'Duration',  $this->plugin_name ),
        "score"      => __( 'Score',     $this->plugin_name ),
    );

    if( !empty($quiz_results_custom_fields) ){
        foreach ($quiz_results_custom_fields as $custom_field_key => $custom_field_value) {
            $default_quiz_all_results_column_names[$custom_field_key] = $custom_field_value;
        }
    }

    $options['quiz_all_results_columns'] = ! isset( $options['quiz_all_results_columns'] ) ? $default_quiz_all_results_columns : $options['quiz_all_results_columns'];
    $quiz_all_results_columns = (isset( $options['quiz_all_results_columns'] ) && !empty($options['quiz_all_results_columns']) ) ? $options['quiz_all_results_columns'] : array();
    $quiz_all_results_columns_order = (isset( $options['quiz_all_results_columns_order'] ) && !empty($options['quiz_all_results_columns_order']) ) ? $options['quiz_all_results_columns_order'] : $default_quiz_all_results_columns;

    $quiz_all_results_columns_order_arr = $quiz_all_results_columns_order;

    foreach( $default_quiz_all_results_columns as $key => $value ){
        if( !isset( $quiz_all_results_columns[$key] ) ){
            $quiz_all_results_columns[$key] = '';
        }

        if( !isset( $quiz_all_results_columns_order[$key] ) ){
            $quiz_all_results_columns_order[$key] = $key;
        }

        if ( ! in_array( $key , $quiz_all_results_columns_order_arr) ) {
            $quiz_all_results_columns_order_arr[] = $key;
        }
    }

    foreach( $quiz_all_results_columns_order as $key => $value ){
        if( !isset( $quiz_all_results_columns[$key] ) ){
            if( isset( $quiz_all_results_columns[$value] ) ){
                $quiz_all_results_columns_order[$value] = $value;
            }
            unset( $quiz_all_results_columns_order[$key] );
        }
    }

    foreach ($quiz_all_results_columns_order_arr  as $key => $value) {
        if( isset( $quiz_all_results_columns_order[$value] ) ){
            $quiz_all_results_columns_order_arr[$value] = $value;
        }

        if ( is_int( $key ) ) {
            unset( $quiz_all_results_columns_order_arr[$key] );
        }
    }

    $quiz_all_results_columns_order = $quiz_all_results_columns_order_arr;

    if( !empty($quiz_results_custom_fields) ){
        foreach ($quiz_results_custom_fields as $custom_field_key => $custom_field_value) {
            $default_quiz_all_results_columns[$custom_field_key] = $custom_field_key;
            $quiz_all_results_columns_order[$custom_field_key] = $custom_field_key;
        }
    }

    // Enable question allow HTML
    $options['quiz_enable_question_allow_html'] = isset($options['quiz_enable_question_allow_html']) ? sanitize_text_field( $options['quiz_enable_question_allow_html'] ) : 'off';
    $quiz_enable_question_allow_html = (isset($options['quiz_enable_question_allow_html']) && sanitize_text_field( $options['quiz_enable_question_allow_html'] ) == "on") ? true : false;

    // Start button activation
    $options['enable_start_button_loader'] = isset($options['enable_start_button_loader']) ? sanitize_text_field( $options['enable_start_button_loader'] ) : 'off';
    $enable_start_button_loader = (isset($options['enable_start_button_loader']) && sanitize_text_field( $options['enable_start_button_loader'] ) == "on") ? true : false;

    // WP Editor height
    $quiz_wp_editor_height = (isset($options['quiz_wp_editor_height']) && $options['quiz_wp_editor_height'] != '' && $options['quiz_wp_editor_height'] != 0) ? absint( sanitize_text_field($options['quiz_wp_editor_height']) ) : 100;

    // Hide correct answer user page shortcode
    $hide_correct_answer = isset( $options['user_page_hide_answer'] ) && $options['user_page_hide_answer'] == "on" ? "checked" : "";

    // Textarea height (public)
    $quiz_textarea_height = (isset($options['quiz_textarea_height']) && $options['quiz_textarea_height'] != '' && $options['quiz_textarea_height'] != 0) ? absint( sanitize_text_field($options['quiz_textarea_height']) ) : 100;

    // User roles to change quiz
    $user_roles_to_change_quiz = (isset($options['user_roles_to_change_quiz']) && !empty( $options['user_roles_to_change_quiz'] ) ) ? $options['user_roles_to_change_quiz'] : array('administrator');

    // Show quiz button to Admins only
    $options['quiz_show_quiz_button_to_admin_only'] = isset($options['quiz_show_quiz_button_to_admin_only']) ? sanitize_text_field( $options['quiz_show_quiz_button_to_admin_only'] ) : 'off';
    $quiz_show_quiz_button_to_admin_only = (isset($options['quiz_show_quiz_button_to_admin_only']) && sanitize_text_field( $options['quiz_show_quiz_button_to_admin_only'] ) == "on") ? true : false;

    // Flash Card Width
    $quiz_flash_card_width = (isset( $options['quiz_flash_card_width'] ) && $options['quiz_flash_card_width'] != '') ? $options['quiz_flash_card_width'] : '';

    // Flash Card Color
    $quiz_flash_card_color = (isset( $options['quiz_flash_card_color'] ) && $options['quiz_flash_card_color'] != '') ? $options['quiz_flash_card_color'] : '#ffffff';

    // Flash Card Randomize
    $options['quiz_flash_card_randomize'] = (isset( $options['quiz_flash_card_randomize'] ) && $options['quiz_flash_card_randomize'] == 'on') ? sanitize_text_field( $options['quiz_flash_card_randomize'] ) : 'off';
    $quiz_flash_card_randomize = (isset( $options['quiz_flash_card_randomize'] ) && $options['quiz_flash_card_randomize'] == 'on') ? true : false;

    //Flash Card Enable Introduction Page
    $quiz_flash_card_enable_introduction = (isset( $options['quiz_flash_card_enable_introduction'] ) && $options['quiz_flash_card_enable_introduction'] == 'on') ? 'on' : 'off';
    $quiz_flash_card_introduction = (isset( $options['quiz_flash_card_introduction']) && $options['quiz_flash_card_introduction'] != '') ? esc_attr( htmlspecialchars_decode($options['quiz_flash_card_introduction']) ) : '';

    // Fields placeholders | Start

    $fields_placeholders_res      = ($actions->ays_get_setting('fields_placeholders') === false) ? json_encode(array()) : $actions->ays_get_setting('fields_placeholders');
    $fields_placeholders          = json_decode( $fields_placeholders_res, true, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );

    $quiz_fields_placeholder_name  = (isset($fields_placeholders['quiz_fields_placeholder_name']) && $fields_placeholders['quiz_fields_placeholder_name'] != '') ? stripslashes( esc_attr( $fields_placeholders['quiz_fields_placeholder_name'] ) ) : 'Name';

    $quiz_fields_placeholder_eamil = (isset($fields_placeholders['quiz_fields_placeholder_eamil']) && $fields_placeholders['quiz_fields_placeholder_eamil'] != '') ? stripslashes( esc_attr( $fields_placeholders['quiz_fields_placeholder_eamil'] ) ) : 'Email';

    $quiz_fields_placeholder_phone = (isset($fields_placeholders['quiz_fields_placeholder_phone']) && $fields_placeholders['quiz_fields_placeholder_phone'] != '') ? stripslashes( esc_attr( $fields_placeholders['quiz_fields_placeholder_phone'] ) ) : 'Phone Number';

    $quiz_fields_label_name  = (isset($fields_placeholders['quiz_fields_label_name']) && $fields_placeholders['quiz_fields_label_name'] != '') ? stripslashes( esc_attr( $fields_placeholders['quiz_fields_label_name'] ) ) : 'Name';

    $quiz_fields_label_eamil = (isset($fields_placeholders['quiz_fields_label_eamil']) && $fields_placeholders['quiz_fields_label_eamil'] != '') ? stripslashes( esc_attr( $fields_placeholders['quiz_fields_label_eamil'] ) ) : 'Email';

    $quiz_fields_label_phone = (isset($fields_placeholders['quiz_fields_label_phone']) && $fields_placeholders['quiz_fields_label_phone'] != '') ? stripslashes( esc_attr( $fields_placeholders['quiz_fields_label_phone'] ) ) : 'Phone Number';

    // Fields placeholders | End

    // Show Result Information | Start

    $options['ays_quiz_show_result_info_user_ip'] = isset($options['ays_quiz_show_result_info_user_ip']) ? sanitize_text_field( $options['ays_quiz_show_result_info_user_ip'] ) : 'on';
    $ays_quiz_show_result_info_user_ip = (isset($options['ays_quiz_show_result_info_user_ip']) && sanitize_text_field( $options['ays_quiz_show_result_info_user_ip'] ) == "on") ? true : false;
    
    $options['ays_quiz_show_result_info_user_id'] = isset($options['ays_quiz_show_result_info_user_id']) ? sanitize_text_field( $options['ays_quiz_show_result_info_user_id'] ) : 'on';
    $ays_quiz_show_result_info_user_id = (isset($options['ays_quiz_show_result_info_user_id']) && sanitize_text_field( $options['ays_quiz_show_result_info_user_id'] ) == "on") ? true : false;

    $options['ays_quiz_show_result_info_user'] = isset($options['ays_quiz_show_result_info_user']) ? sanitize_text_field( $options['ays_quiz_show_result_info_user'] ) : 'on';
    $ays_quiz_show_result_info_user = (isset($options['ays_quiz_show_result_info_user']) && sanitize_text_field( $options['ays_quiz_show_result_info_user'] ) == "on") ? true : false;

    $options['ays_quiz_show_result_info_admin_note'] = isset($options['ays_quiz_show_result_info_admin_note']) ? sanitize_text_field( $options['ays_quiz_show_result_info_admin_note'] ) : 'on';
    $ays_quiz_show_result_info_admin_note = (isset($options['ays_quiz_show_result_info_admin_note']) && sanitize_text_field( $options['ays_quiz_show_result_info_admin_note'] ) == "on") ? true : false;


    $options['ays_quiz_show_result_info_start_date'] = isset($options['ays_quiz_show_result_info_start_date']) ? sanitize_text_field( $options['ays_quiz_show_result_info_start_date'] ) : 'on';
    $ays_quiz_show_result_info_start_date = (isset($options['ays_quiz_show_result_info_start_date']) && sanitize_text_field( $options['ays_quiz_show_result_info_start_date'] ) == "on") ? true : false;
    
    $options['ays_quiz_show_result_info_duration'] = isset($options['ays_quiz_show_result_info_duration']) ? sanitize_text_field( $options['ays_quiz_show_result_info_duration'] ) : 'on';
    $ays_quiz_show_result_info_duration = (isset($options['ays_quiz_show_result_info_duration']) && sanitize_text_field( $options['ays_quiz_show_result_info_duration'] ) == "on") ? true : false;

    $options['ays_quiz_show_result_info_score'] = isset($options['ays_quiz_show_result_info_score']) ? sanitize_text_field( $options['ays_quiz_show_result_info_score'] ) : 'on';
    $ays_quiz_show_result_info_score = (isset($options['ays_quiz_show_result_info_score']) && sanitize_text_field( $options['ays_quiz_show_result_info_score'] ) == "on") ? true : false;

    $options['ays_quiz_show_result_info_rate'] = isset($options['ays_quiz_show_result_info_rate']) ? sanitize_text_field( $options['ays_quiz_show_result_info_rate'] ) : 'on';
    $ays_quiz_show_result_info_rate = (isset($options['ays_quiz_show_result_info_rate']) && sanitize_text_field( $options['ays_quiz_show_result_info_rate'] ) == "on") ? true : false;
    
    $options['ays_quiz_show_result_info_unique_code'] = isset($options['ays_quiz_show_result_info_unique_code']) ? sanitize_text_field( $options['ays_quiz_show_result_info_unique_code'] ) : 'on';
    $ays_quiz_show_result_info_unique_code = (isset($options['ays_quiz_show_result_info_unique_code']) && sanitize_text_field( $options['ays_quiz_show_result_info_unique_code'] ) == "on") ? true : false;

    $options['ays_quiz_show_result_info_keywords'] = isset($options['ays_quiz_show_result_info_keywords']) ? sanitize_text_field( $options['ays_quiz_show_result_info_keywords'] ) : 'on';
    $ays_quiz_show_result_info_keywords = (isset($options['ays_quiz_show_result_info_keywords']) && sanitize_text_field( $options['ays_quiz_show_result_info_keywords'] ) == "on") ? true : false;

    $options['ays_quiz_show_result_info_res_by_cats'] = isset($options['ays_quiz_show_result_info_res_by_cats']) ? sanitize_text_field( $options['ays_quiz_show_result_info_res_by_cats'] ) : 'on';
    $ays_quiz_show_result_info_res_by_cats = (isset($options['ays_quiz_show_result_info_res_by_cats']) && sanitize_text_field( $options['ays_quiz_show_result_info_res_by_cats'] ) == "on") ? true : false;
    
    $options['ays_quiz_show_result_info_coupon'] = isset($options['ays_quiz_show_result_info_coupon']) ? sanitize_text_field( $options['ays_quiz_show_result_info_coupon'] ) : 'on';
    $ays_quiz_show_result_info_coupon = (isset($options['ays_quiz_show_result_info_coupon']) && sanitize_text_field( $options['ays_quiz_show_result_info_coupon'] ) == "on") ? true : false;

    $options['ays_quiz_show_result_info_certificate'] = isset($options['ays_quiz_show_result_info_certificate']) ? sanitize_text_field( $options['ays_quiz_show_result_info_certificate'] ) : 'on';
    $ays_quiz_show_result_info_certificate = (isset($options['ays_quiz_show_result_info_certificate']) && sanitize_text_field( $options['ays_quiz_show_result_info_certificate'] ) == "on") ? true : false;

    // Show Result Information | End


    /*
    ==========================================
    Results settings start
    ==========================================
    */

    // Store all not finished results
    $options['store_all_not_finished_results'] = (isset( $options['store_all_not_finished_results'] ) && $options['store_all_not_finished_results'] == 'on') ? sanitize_text_field( $options['store_all_not_finished_results'] ) : 'off';
    $store_all_not_finished_results = (isset( $options['store_all_not_finished_results'] ) && $options['store_all_not_finished_results'] == 'on') ? true : false;

    /*
    ==========================================
    Results settings end
    ==========================================
    */

    // General CSS File
    $options['quiz_exclude_general_css'] = isset($options['quiz_exclude_general_css']) ? esc_attr( $options['quiz_exclude_general_css'] ) : 'off';
    $quiz_exclude_general_css = (isset($options['quiz_exclude_general_css']) && esc_attr( $options['quiz_exclude_general_css'] ) == "on") ? true : false;

    // Enable question answers
    $options['quiz_enable_question_answers'] = isset($options['quiz_enable_question_answers']) ? esc_attr( $options['quiz_enable_question_answers'] ) : 'off';
    $quiz_enable_question_answers = (isset($options['quiz_enable_question_answers']) && esc_attr( $options['quiz_enable_question_answers'] ) == "on") ? true : false;

    // Enable lazy loading attribute for images
    $options['quiz_enable_lazy_loading'] = isset($options['quiz_enable_lazy_loading']) ? esc_attr( $options['quiz_enable_lazy_loading'] ) : 'off';
    $quiz_enable_lazy_loading = (isset($options['quiz_enable_lazy_loading']) && esc_attr( $options['quiz_enable_lazy_loading'] ) == "on") ? true : false;

    // Default all orders column
    $default_all_orders_columns = array(
        'quiz_name'      => 'quiz_name',
        'payment_date'   => 'payment_date',
        'amount'         => 'amount',
        'type'           => 'type'
    );

    $default_all_orders_columns_names = array(
        "quiz_name"      => __( 'Quiz name', $this->plugin_name ),
        "payment_date"   => __( 'Payment date',$this->plugin_name ),
        "amount"         => __( 'Amount',  $this->plugin_name ),
        "type"           => __( 'Type',  $this->plugin_name )
    );

    $options['quiz_all_orders_columns'] = !isset( $options['quiz_all_orders_columns'] ) || empty($options['quiz_all_orders_columns']) ? $default_all_orders_columns : $options['quiz_all_orders_columns'];
    $quiz_all_orders_columns = (isset( $options['quiz_all_orders_columns'] ) && !empty($options['quiz_all_orders_columns']) ) ? $options['quiz_all_orders_columns'] : array();
    $quiz_all_orders_columns_order = (isset( $options['quiz_all_orders_columns_order'] ) && !empty($options['quiz_all_orders_columns_order']) ) ? $options['quiz_all_orders_columns_order'] : $default_all_orders_columns;

    // Show information form only once
    $options['quiz_show_information_form_only_once'] = (isset( $options['quiz_show_information_form_only_once'] ) && $options['quiz_show_information_form_only_once'] == 'on') ? sanitize_text_field( $options['quiz_show_information_form_only_once'] ) : 'off';
    $quiz_show_information_form_only_once = (isset( $options['quiz_show_information_form_only_once'] ) && $options['quiz_show_information_form_only_once'] == 'on') ? true : false;

    // Disable Quiz maker menu item notification
    $options['quiz_disable_quiz_menu_notification'] = isset($options['quiz_disable_quiz_menu_notification']) ? esc_attr( $options['quiz_disable_quiz_menu_notification'] ) : 'off';
    $quiz_disable_quiz_menu_notification = (isset($options['quiz_disable_quiz_menu_notification']) && esc_attr( $options['quiz_disable_quiz_menu_notification'] ) == "on") ? true : false;

    // Disable results menu item notification
    $options['quiz_disable_results_menu_notification'] = isset($options['quiz_disable_results_menu_notification']) ? esc_attr( $options['quiz_disable_results_menu_notification'] ) : 'off';
    $quiz_disable_results_menu_notification = (isset($options['quiz_disable_results_menu_notification']) && esc_attr( $options['quiz_disable_results_menu_notification'] ) == "on") ? true : false;

    // Enable custom login form redirect if user fail
    $options['quiz_enable_custom_login_form_redirect'] = (isset( $options['quiz_enable_custom_login_form_redirect'] ) && $options['quiz_enable_custom_login_form_redirect'] == 'on') ? sanitize_text_field( $options['quiz_enable_custom_login_form_redirect'] ) : 'off';
    $quiz_enable_custom_login_form_redirect = (isset( $options['quiz_enable_custom_login_form_redirect'] ) && $options['quiz_enable_custom_login_form_redirect'] == 'on') ? true : false;

    // Custom login form link
    $quiz_custom_login_form_redirect_link = (isset($options['quiz_custom_login_form_redirect_link']) && $options['quiz_custom_login_form_redirect_link'] != '') ? stripslashes( esc_url( $options['quiz_custom_login_form_redirect_link'] ) ) : '';

?>
<div class="wrap" style="position:relative;">
    <div class="container-fluid">
        <form method="post" class="ays-quiz-general-settings-form" id="ays-quiz-general-settings-form">
            <input type="hidden" name="ays_quiz_tab" value="<?php echo esc_attr($ays_quiz_tab); ?>">
            <div class="ays-quiz-heading-box">
                <div class="ays-quiz-wordpress-user-manual-box">
                    <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
                </div>
            </div>
            <h1 class="wp-heading-inline">
            <?php
                echo __('General Settings',$this->plugin_name);
            ?>
            </h1>
            <?php                
                if( isset( $_REQUEST['status'] ) ){
                    $actions->quiz_settings_notices($_REQUEST['status']);
                }
            ?>
            <hr/>
            <div class="form-group ays-settings-wrapper">
            <div>
                <div class="nav-tab-wrapper" style="position:sticky; top:35px;">
                    <a href="#tab1" data-tab="tab1" class="nav-tab <?php echo ($ays_quiz_tab == 'tab1') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("General", $this->plugin_name);?>
                    </a>
                    <a href="#tab2" data-tab="tab2" class="nav-tab <?php echo ($ays_quiz_tab == 'tab2') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Integrations", $this->plugin_name);?>
                    </a>
                    <a href="#tab3" data-tab="tab3" class="nav-tab <?php echo ($ays_quiz_tab == 'tab3') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Shortcodes", $this->plugin_name);?>
                    </a>
                    <a href="#tab7" data-tab="tab7" class="nav-tab <?php echo ($ays_quiz_tab == 'tab7') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Extra shortcodes", $this->plugin_name);?>
                    </a>
                    <a href="#tab4" data-tab="tab4" class="nav-tab <?php echo ($ays_quiz_tab == 'tab4') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Message variables", $this->plugin_name);?>
                    </a>
                    <a href="#tab5" data-tab="tab5" class="nav-tab <?php echo ($ays_quiz_tab == 'tab5') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Buttons Texts", $this->plugin_name);?>
                    </a>
                    <a href="#tab6" data-tab="tab6" class="nav-tab <?php echo ($ays_quiz_tab == 'tab6') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Fields texts", $this->plugin_name);?>
                    </a>
                    <a href="#tab8" data-tab="tab8" class="nav-tab <?php echo ($ays_quiz_tab == 'tab8') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Detailed Report Options", $this->plugin_name);?>
                    </a>
                </div>
            </div>
            <div class="ays-quiz-tabs-wrapper">
                <div id="tab1" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab1') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle"><?php echo __('General Settings',$this->plugin_name)?></p>
                    <hr/>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_globe"></i></strong>
                            <h5><?php echo __('Who will have permission to Quiz',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_user_roles">
                                    <?php echo __( "Select user role for giving access to Quiz menu", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Give permissions to see only their own quizzes to these user roles.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8 ays-quiz-user-roles">
                                <select name="ays_user_roles[]" id="ays_user_roles" multiple>
                                    <?php
                                        foreach($ays_users_roles as $role => $role_name){
                                            $selected = in_array($role, $user_roles) ? 'selected' : '';
                                            echo "<option ".$selected." value='".$role."'>".$role_name."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_user_roles_to_change_quiz">
                                    <?php echo __( "Select user role for giving access to change all Quiz data", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Give permissions to manage all quizzes and results to these user roles. Please add the given user roles to the above field as well.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8 ays-quiz-user-roles">
                                <select name="ays_user_roles_to_change_quiz[]" id="ays_user_roles_to_change_quiz" multiple>
                                    <?php
                                        foreach($ays_users_roles as $role => $role_name){
                                            $selected = in_array($role, $user_roles_to_change_quiz) ? 'selected' : '';
                                            echo "<option ".$selected." value='".$role."'>".$role_name."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <blockquote>
                            <?php echo __( "Control the access of the plugin from the dashboard and manage the capabilities of those user roles.", $this->plugin_name ); ?>
                            <br>
                            <?php echo __( "If you want to give a full control to the given user role, please add the role in both fields.", $this->plugin_name ); ?>
                        </blockquote>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_question_circle"></i></strong>
                            <h5><?php echo __('Default parameters for Quiz',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_questions_default_type">
                                    <?php echo __( "Questions default type", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose default question type which will be selected in the Add new question page.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <select id="ays-type" name="ays_question_default_type">
                                    <option></option>
                                    <?php
                                        foreach($question_types as $type => $label):
                                        $selected = $question_default_type == $type ? "selected" : "";
                                    ?>
                                    <option value="<?php echo $type; ?>" data-nkar="<?php echo $question_types_icon_url[ $type ]; ?>" <?php echo $selected; ?> ><?php echo $label; ?></option>
                                    <?php
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_answer_default_count">
                                    <?php echo __( "Answer default count", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can write the default answer count which will be showing in the Add new question page (this will work only with radio, checkbox, and dropdown types).',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_answer_default_count" id="ays_answer_default_count" min="2" class="ays-text-input" value="<?php echo $ays_answer_default_count; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_questions_default_cat">
                                    <?php echo __( "Questions default category", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose default question category which will be selected in the Add new question page.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <select id="ays-cat" name="ays_questions_default_cat">
                                     <option></option>
                                     <?php
                                        $cat = 0;
                                        foreach ($question_categories as $question_category) {
                                            $checked = (intval($question_category['id']) == $question_default_cat) ? "selected" : "";
                                            if ($cat == 0 && $question_default_cat == 0) {
                                                $checked = 'selected';
                                            }
                                            echo "<option value='" . $question_category['id'] . "' " . $checked . ">" . stripslashes($question_category['title']) . "</option>";
                                            $cat++;
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_questions_default_keyword">
                                    <?php echo __( "Keyword default count", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the default keyword count which will be selected while adding answers to your new question. It will apply to the previous questions and intervals as well.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_keyword_default_max_value" id="ays_keyword_default_max_value" class="ays-text-input" value="<?php echo $keyword_default_max_value; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_wp_editor_height">
                                    <?php echo __( "WP Editor height", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Give the default value to the height of the WP Editor. It will apply to all WP Editors within the plugin on the dashboard.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_quiz_wp_editor_height" id="ays_quiz_wp_editor_height" class="ays-text-input" value="<?php echo $quiz_wp_editor_height; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_textarea_height">
                                    <?php echo __( "Textarea height (public)", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Set the height of the textarea by entering a numeric value. It applies to Text question type textarea, Feedback texatarea and so on.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_quiz_textarea_height" id="ays_quiz_textarea_height" class="ays-text-input" value="<?php echo $quiz_textarea_height; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_enable_question_allow_html">
                                    <?php echo __( "Enable answers allow HTML for new question", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow implementing HTML coding in answer boxes while adding new question. This works only for Radio and Checkbox (Multiple) questions.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_enable_question_allow_html" name="ays_quiz_enable_question_allow_html" value="on" <?php echo $quiz_enable_question_allow_html ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_show_quiz_button_to_admin_only">
                                    <?php echo __( "Show quiz button to Admins only", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow only admins to see the Quiz Maker button within the WP Editor while adding/editing a new post/page.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_quiz_button_to_admin_only" name="ays_quiz_show_quiz_button_to_admin_only" value="on" <?php echo $quiz_show_quiz_button_to_admin_only ? 'checked' : ''; ?> />
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_user_ip"></i></strong>
                            <h5><?php echo __('Users IP addresses',$this->plugin_name)?></h5>
                        </legend>
                        <blockquote class="ays_warning">
                            <p style="margin:0;"><?php echo __( "If this option is enabled then the 'Limitation by IP' option will not work!", $this->plugin_name ); ?></p>
                        </blockquote>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_disable_user_ip">
                                    <?php echo __( "Do not store IP addresses", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, IP address of the users will not be stored in database. Note: If this option is enabled, then the `Limits user by IP` option will not work.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="ays-checkbox-input" id="ays_disable_user_ip" name="ays_disable_user_ip" value="on" <?php echo $disable_user_ip ? 'checked' : ''; ?> />
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_music"></i></strong>
                            <h5><?php echo __('Quiz Right/Wrong answers sounds',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_questions_default_type">
                                    <?php echo __( "Sounds for right/wrong answers", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This option will work with Enable correct answers option.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="ays_questions_default_type">
                                            <?php echo __( "Sounds for right answers", $this->plugin_name ); ?>
                                        </label>
                                        <div class="ays-bg-music-container">
                                            <a class="add-quiz-bg-music" href="javascript:void(0);"><?php echo __("Select sound", $this->plugin_name); ?></a>
                                            <audio controls src="<?php echo $right_answer_sound; ?>"></audio>
                                            <input type="hidden" name="ays_right_answer_sound" class="ays_quiz_bg_music" value="<?php echo $right_answer_sound; ?>">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="ays_questions_default_type">
                                            <?php echo __( "Sounds for wrong answers", $this->plugin_name ); ?>
                                        </label>
                                        <div class="ays-bg-music-container">
                                            <a class="add-quiz-bg-music" href="javascript:void(0);"><?php echo __("Select sound", $this->plugin_name); ?></a>
                                            <audio controls src="<?php echo $wrong_answer_sound; ?>"></audio>
                                            <input type="hidden" name="ays_wrong_answer_sound" class="ays_quiz_bg_music" value="<?php echo $wrong_answer_sound; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_text"></i></strong>
                            <h5><?php echo __('Excerpt words count in list tables',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_question_title_length">
                                    <?php echo __( "Questions list table", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Determine the length of the questions to be shown in the Questions List Table by putting your preferred count of words in the following field. (For example: if you put 10,  you will see the first 10 words of each question in the Questions page of your dashboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_question_title_length" id="ays_question_title_length" class="ays-text-input" value="<?php echo $question_title_length; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quizzes_title_length">
                                    <?php echo __( "Quizzes list table", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Determine the length of the quizzes to be shown in the Quizzes List Table by putting your preferred count of words in the following field. (For example: if you put 10,  you will see the first 10 words of each quiz in the Quizzes page of your dashboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_quizzes_title_length" id="ays_quizzes_title_length" class="ays-text-input" value="<?php echo $quizzes_title_length; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_results_title_length">
                                    <?php echo __( "Results list table", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Determine the length of the results to be shown in the Results List Table by putting your preferred count of words in the following field. (For example: if you put 10,  you will see the first 10 words of each result in the Results page of your dashboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_results_title_length" id="ays_results_title_length" class="ays-text-input" value="<?php echo $results_title_length; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_question_categories_title_length">
                                    <?php echo __( "Question categories list table", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Determine the length of the results to be shown in the Question categories List Table by putting your preferred count of words in the following field. (For example: if you put 10,  you will see the first 10 words of each result in the Results page of your dashboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_question_categories_title_length" id="ays_question_categories_title_length" class="ays-text-input" value="<?php echo $question_categories_title_length; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_categories_title_length">
                                    <?php echo __( "Quiz categories list table", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Determine the length of the results to be shown in the Quiz categories List Table by putting your preferred count of words in the following field. (For example: if you put 10,  you will see the first 10 words of each result in the Quiz categories page of your dashboard.', $this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_quiz_categories_title_length" id="ays_quiz_categories_title_length" class="ays-text-input" value="<?php echo $quiz_categories_title_length; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_reviews_title_length">
                                    <?php echo __( "Reviews list table", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Determine the length of the results to be shown in the Reviews List Table by putting your preferred count of words in the following field. (For example: if you put 10,  you will see the first 10 words of each result in the Reviews page of your dashboard.', $this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_quiz_reviews_title_length" id="ays_quiz_reviews_title_length" class="ays-text-input" value="<?php echo $quiz_reviews_title_length; ?>">
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_code"></i></strong>
                            <h5><?php echo __('Animation Top',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_enable_animation_top">
                                    <?php echo __( "Enable animation", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Enable animation of the scroll offset of the quiz container. It works when the quiz container is visible on the screen partly and the user starts the quiz and moves from one question to another.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" name="ays_quiz_enable_animation_top" id="ays_quiz_enable_animation_top" value="on" <?php echo $quiz_enable_animation_top ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_animation_top">
                                    <?php echo __( "Scroll offset(px)", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the scroll offset of the quiz container after the animation starts. It works when the quiz container is visible on the screen partly and the user starts the quiz and moves from one question to another.',$this->plugin_name);?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_quiz_animation_top" id="ays_quiz_animation_top" class="ays-text-input" value="<?php echo $quiz_animation_top; ?>">
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_file_code"></i></strong>
                            <h5><?php echo __('General CSS File',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_exclude_general_css">
                                    <?php echo __( "Exclude general CSS file from home page", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('If the option is enabled, then the quiz general CSS file will not be applied to the home page. Please note, that if you have inserted the quiz on the home page, then the option must be disabled so that the CSS File can normally work for that quiz..',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" name="ays_quiz_exclude_general_css" id="ays_quiz_exclude_general_css" value="on" <?php echo $quiz_exclude_general_css ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </fieldset> <!-- General CSS File -->
                    <hr>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/ays-quiz-loading-icon.svg" alt="" style="width: 30px;">
                            <h5><?php echo __('Lazy loading',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_enable_lazy_loading">
                                    <?php echo __( "Enable lazy loading attribute for images", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __('If you enable this option, the loading="lazy" attribute will be added to all the question and answer images, except of the first question and answer images. Note: The feature will not work for the Quiz image option. The default value for this option is set as "Off".',$this->plugin_name) ); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" name="ays_quiz_enable_lazy_loading" id="ays_quiz_enable_lazy_loading" value="on" <?php echo $quiz_enable_lazy_loading ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </fieldset> <!-- Lazy loading -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_bell"></i></strong>
                            <h5><?php echo __('Menu notifications',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_disable_quiz_menu_notification">
                                    <?php echo __( "Disable Quiz maker menu item notification", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __('Enable this option and the notifications will not be displayed in the Quiz Maker menu.',$this->plugin_name) ); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" name="ays_quiz_disable_quiz_menu_notification" id="ays_quiz_disable_quiz_menu_notification" value="on" <?php echo $quiz_disable_quiz_menu_notification ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_disable_results_menu_notification">
                                    <?php echo __( "Disable Results menu item notification", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __('Enable this option and the notifications will not be displayed in the Results menu.',$this->plugin_name) ); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" name="ays_quiz_disable_results_menu_notification" id="ays_quiz_disable_results_menu_notification" value="on" <?php echo $quiz_disable_results_menu_notification ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </fieldset> <!-- Menu notifications -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_spinner"></i></strong>
                            <h5><?php echo __('Start button activation',$this->plugin_name); ?></h5>
                        </legend>
                        <blockquote>
                            <?php echo __( 'Tick the checkbox if you would like to show loader and "Loading ..." text over the start button while the JavaScript of the given webpage loads. As soon as the webpage completes its loading, the start button will become active.', $this->plugin_name ); ?>
                        </blockquote>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_enable_start_button_loader">
                                    <?php echo __( "Enable Start button loader", $this->plugin_name ); ?>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="ays-checkbox-input" id="ays_enable_start_button_loader" name="ays_enable_start_button_loader" value="on" <?php echo $enable_start_button_loader ? 'checked' : ''; ?> />
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_list_alt"></i></strong>
                            <h5><?php echo __('Results settings',$this->plugin_name); ?></h5>
                        </legend>
                        <blockquote>
                            <?php echo __( 'All started, but not finished data of quizzes will be stored on the Not finished tab of the Results page.', $this->plugin_name ); ?>
                        </blockquote>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_store_all_not_finished_results">
                                    <?php echo __( "Store all not finished results", $this->plugin_name ); ?>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="ays-checkbox-input" id="ays_store_all_not_finished_results" name="ays_store_all_not_finished_results" value="on" <?php echo $store_all_not_finished_results ? 'checked' : ''; ?> />
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_code"></i></strong>
                            <h5><?php echo __('Information form settings',$this->plugin_name); ?></h5>
                        </legend>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_show_information_form_only_once">
                                    <?php echo __( "Show information form only once", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __('By enabling this option when the user has filled in the default email address in any of the quizzes once, the Information Form option will not be displayed for him/her for other quizzes, including that one as well, anymore.',$this->plugin_name) ); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_information_form_only_once" name="ays_quiz_show_information_form_only_once" value="on" <?php echo $quiz_show_information_form_only_once ? 'checked' : ''; ?> />
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_sign_in"></i></strong>
                            <h5><?php echo __('Quiz Login Form Settings',$this->plugin_name); ?></h5>
                        </legend>
                        <hr>
                        <div class="form-group row ays_toggle_parent">
                            <div class="col-sm-4">
                                <label for="ays_quiz_enable_custom_login_form_redirect">
                                    <?php echo __( "Enable Login Form Custom Redirection", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __('Enable this option to redirect users to your desired Login Form in case of filling an incorrect email address or password.',$this->plugin_name) ); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-1">
                                <input type="checkbox" class="ays-checkbox-input ays-enable-timer1 ays_toggle_checkbox" id="ays_quiz_enable_custom_login_form_redirect" name="ays_quiz_enable_custom_login_form_redirect" value="on" <?php echo $quiz_enable_custom_login_form_redirect ? 'checked' : ''; ?> />
                            </div>
                            <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $quiz_enable_custom_login_form_redirect ? '' : 'display_none'; ?>">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label class="form-check-label" for="ays_quiz_custom_login_form_redirect_link">
                                            <?php echo __('Custom login form link', $this->plugin_name); ?>
                                            <a class="ays_help" data-toggle="tooltip"
                                            title="<?php echo __('The URL for redirecting after writing an incorrect email address or password.', $this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="ays-enable-timerl ays-text-input" id="ays_quiz_custom_login_form_redirect_link" name="ays_quiz_custom_login_form_redirect_link" value="<?php echo $quiz_custom_login_form_redirect_link; ?>">
                                    </div>
                                </div>
                                <blockquote>
                                    <?php echo __( 'Note: If you leave the option empty,  the user will stay on the same page in case of a fail.', $this->plugin_name ); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_trash"></i></strong>
                            <h5><?php echo __('Erase Quiz data',$this->plugin_name)?></h5>
                        </legend>
                        <?php if( isset( $_GET['del_stat'] ) ): ?>
                        <blockquote style="border-color:#46b450;background: rgba(70, 180, 80, 0.2);">
                            <?php echo __("Results up to a ".$_GET['mcount']." month ago deleted successfully.", $this->plugin_name); ?>
                        </blockquote>
                        <hr>
                        <?php endif; ?>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_delete_results_by">
                                    <?php echo __( "Delete results older then 'X' the month", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify count of months and save changes. Attention! it will remove submissions older than specified months permanently.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_delete_results_by" id="ays_delete_results_by" class="ays-text-input">
                            </div>
                        </div>
                    </fieldset>
                    
                    <?php 

                        if ( is_multisite() ) {
                            ?>

                            <hr>
                            <fieldset>
                                <legend>
                                    <strong style="font-size:30px;"><i class="ays_fa ays_fa_code"></i></strong>
                                    <h5><?php echo __('Update Quiz Maker DB tables',$this->plugin_name)?></h5>
                                </legend>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button class="button button-primary ays-quiz-update-database" data-message="<?php echo __( "Are you sure you want to update DB?", $this->plugin_name ); ?>"><?php echo __("Update Database", $this->plugin_name); ?></button>
                                        <blockquote style="margin-top: 20px;">
                                            <?php echo sprintf( __( "%s Note: %s In case you correctly update the plugin to the latest version and notice that some of the previous results are missing, then,  most presumably the database hasn't been updated. 
                                                So, please make a %s DB backup %s for your safety (you can use %s UpdraftPlus %s ). %s
                                                Then, click on the %s Update Database button %s to update the tables manually․", $this->plugin_name ),
                                                "<strong>",
                                                "</strong>",
                                                "<strong>",
                                                "</strong>",
                                                "<a href='https://wordpress.org/plugins/updraftplus/' target ='_blank'>",
                                                "</a>",
                                                "</br>",
                                                "<strong>",
                                                "</strong>"
                                                 ); ?>
                                        </blockquote>
                                    </div>
                                </div>
                            </fieldset>

                            <?php
                        }

                     ?>
                </div>
                <div id="tab2" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab2') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle"><?php echo __('Integrations',$this->plugin_name)?></p>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/mailchimp_logo.png" alt="">
                            <h5><?php echo __('MailChimp',$this->plugin_name)?></h5>
                        </legend>
                        <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                            <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                                <a href="https://www.youtube.com/watch?v=joPQrsF0a60" target="_blank">
                                    <?php echo __("How to integrate MailChimp - video", $this->plugin_name); ?>
                                </a>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_mailchimp_username">
                                            <?php echo __('MailChimp Username',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" 
                                            class="ays-text-input" 
                                            id="ays_mailchimp_username" 
                                            name="ays_mailchimp_username"
                                            value="<?php echo $mailchimp_username; ?>"
                                        />
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_mailchimp_api_key">
                                            <?php echo __('MailChimp API Key',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" 
                                            class="ays-text-input" 
                                            id="ays_mailchimp_api_key" 
                                            name="ays_mailchimp_api_key"
                                            value="<?php echo $mailchimp_api_key; ?>"
                                        />
                                    </div>
                                </div>
                                <blockquote>
                                    <?php echo sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://us20.admin.mailchimp.com/account/api/", "Account Extras menu" ); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/paypal_logo.png" alt="">
                            <h5><?php echo __('PayPal',$this->plugin_name)?></h5>
                        </legend>
                        <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                            <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                                <a href="https://www.youtube.com/watch?v=IwT-2d9OE1g" target="_blank">
                                    <?php echo __("How to integrate PayPal - video", $this->plugin_name); ?>
                                </a>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_paypal_client_id">
                                            <?php echo __('Paypal Client ID',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" 
                                            class="ays-text-input" 
                                            id="ays_paypal_client_id" 
                                            name="ays_paypal_client_id"
                                            value="<?php echo $paypal_client_id; ?>"
                                        />
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>
                                            <?php echo __('Payment terms',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9 ays_toggle_parent">
                                        <label class="ays_quiz_loader" style="display:inline-block;">
                                            <input type="radio" name="ays_paypal_payment_terms" class="ays_toggle_radio" data-flag="false" value="lifetime" <?php echo $paypal_payment_terms == "lifetime" ? "checked" : ""; ?>/>
                                            <span><?php echo __('Lifetime payment',$this->plugin_name)?></span>
                                        </label>
                                        <label class="ays_quiz_loader" style="display:inline-block;">
                                            <input type="radio" name="ays_paypal_payment_terms" class="ays_toggle_radio" data-flag="true" data-toggle-class="ays_toggle_target_1" value="onetime" <?php echo $paypal_payment_terms == "onetime" ? "checked" : ""; ?>/>
                                            <span><?php echo __('Onetime payment',$this->plugin_name)?></span>
                                        </label>
                                        <label class="ays_quiz_loader" style="display:inline-block;">
                                            <input type="radio" name="ays_paypal_payment_terms" class="ays_toggle_radio" data-flag="true" data-toggle-class="ays_toggle_target_2" value="subscribtion" <?php echo $paypal_payment_terms == "subscribtion" ? "checked" : ""; ?>/>
                                            <span><?php echo __('Subscription',$this->plugin_name)?></span>
                                        </label>
                                        <a class="ays_help" style="font-size:15px;" data-toggle="tooltip" data-html="true"
                                            title="<?php
                                                echo __('Choose your preferred method.',$this->plugin_name) .
                                                "<ul style='list-style-type: circle;padding-left: 20px;'>".
                                                    "<li>". __('Lifetime  - By enabling this option, the user pays once at the beginning and gets access to the given quiz each time starting from that moment. It detects the user after the first attempt based on their WP user ID (designed for logged-in users).',$this->plugin_name) ."</li>".
                                                    "<li>". __('Onetime - By enabling this option, the user needs to pay each time separately for taking the quiz.',$this->plugin_name) ."</li>".
                                                    "<li>". __('Subscription - By enabling this option, the quiz will be available during your chosen period of time. You can set the Subscription duration by Day, Month or Year. This option is (designed for logged-in users).',$this->plugin_name) ."</li>".
                                                "</ul>";
                                            ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                        <hr>
                                        <div class="ays_toggle_target_1" style="<?php echo $paypal_payment_terms == "onetime" ? "display:block;" : "display:none;"; ?>">
                                            <label>
                                                <input type="checkbox" name="ays_paypal_extra_check" value="on" <?php echo $paypal_extra_check ? "checked" : ""; ?>/>
                                                <span><?php echo __('Turn on extra security check', $this->plugin_name); ?></span>
                                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('When the user pays for the quiz and starts passing it but leaves without finishing, he/she has to pay again every time he wants to pass it.',$this->plugin_name)?>">
                                                    <i class="ays_fa ays_fa_info_circle"></i>
                                                </a>
                                            </label>
                                        </div>
                                        <div class="ays_toggle_target_2" style="<?php echo $paypal_payment_terms == "subscribtion" ? "display:block;" : "display:none;"; ?>">
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label class="form-check-label" for="ays-subscribtion-duration"> <?php echo __('Subscription duration', $this->plugin_name); ?> </label>
                                                </div>
                                                <div class="col-sm-8 d-flex">
                                                    <input type="text" class="ays-text-input ays-text-input-short" id="ays-subscribtion-duration" name="ays-subscribtion-duration" value="<?php echo $paypal_subscribtion_duration; ?>" placeholder="30">
                                                    <select name="ays-subscribtion-duration-by" class="ays-text-input-short ml-3">
                                                        <option value="day" <?php echo $paypal_subscribtion_duration_by == 'day' ? 'selected' : ''; ?>><?php echo __( "Day", $this->plugin_name ); ?></option>
                                                        <option value="month" <?php echo $paypal_subscribtion_duration_by == 'month' ? 'selected' : ''; ?>><?php echo __( "Month", $this->plugin_name ); ?></option>
                                                        <option value="year" <?php echo $paypal_subscribtion_duration_by == 'year' ? 'selected' : ''; ?>><?php echo __( "Year", $this->plugin_name ); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <blockquote>
                                    <?php echo sprintf( __( "You can get your Client ID from ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://developer.paypal.com/developer/applications", "Developer Paypal" ); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/stripe_logo.png" alt="">
                            <h5><?php echo __('Stripe',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_stripe_api_key">
                                            Stripe <?php echo __('Publishable Key', $this->plugin_name); ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="ays-text-input" id="ays_stripe_api_key" name="ays_stripe_api_key" value="<?php echo $stripe_api_key; ?>" >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_stripe_secret_key">
                                            Stripe <?php echo __('Secret Key', $this->plugin_name); ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="ays-text-input" id="ays_stripe_secret_key" name="ays_stripe_secret_key" value="<?php echo $stripe_secret_key; ?>" >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>
                                            <?php echo __('Payment terms',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <label class="ays_quiz_loader" style="display:inline-block;">
                                            <input type="radio" name="ays_stripe_payment_terms" value="lifetime" <?php echo $stripe_payment_terms == "lifetime" ? "checked" : ""; ?>/>
                                            <span><?php echo __('Lifetime payment',$this->plugin_name)?></span>
                                        </label>
                                        <label class="ays_quiz_loader" style="display:inline-block;">
                                            <input type="radio" name="ays_stripe_payment_terms" value="onetime" <?php echo $stripe_payment_terms == "onetime" ? "checked" : ""; ?>/>
                                            <span><?php echo __('Onetime payment',$this->plugin_name)?></span>
                                        </label>
                                        <a class="ays_help" style="font-size:15px;" data-toggle="tooltip" data-html="true"
                                            title="<?php
                                                echo __('Choose your preferred method.',$this->plugin_name) .
                                                "<ul style='list-style-type: circle;padding-left: 20px;'>".
                                                    "<li>". __('Lifetime  - By enabling this option, the user pays once at the beginning and gets access to the given quiz each time starting from that moment. It detects the user after the first attempt based on their WP user ID (designed for logged-in users).',$this->plugin_name) ."</li>".
                                                    "<li>". __('Onetime - By enabling this option, the user needs to pay each time separately for taking the quiz.',$this->plugin_name) ."</li>".
                                                "</ul>";
                                            ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </div>
                                </div>
                                <blockquote>
                                    <?php echo __("You can get your Publishable and Secret keys on API Keys page on your Stripe dashboard.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/campaignmonitor_logo.png" alt="">
                            <h5><?php echo __('Campaign Monitor',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_monitor_client">
                                            Campaign Monitor <?= __('Client ID', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_monitor_client"
                                               name="ays_monitor_client"
                                               value="<?= $monitor_client; ?>"
                                        >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_monitor_api_key">
                                            Campaign Monitor <?= __('API Key', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_monitor_api_key"
                                               name="ays_monitor_api_key"
                                               value="<?= $monitor_api_key; ?>"
                                        >
                                    </div>
                                </div>
                                <blockquote>
                                    <?= __("You can get your API key and Client ID from your Account Settings page.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/zapier_logo.png" alt="">
                            <h5><?php echo __('Zapier',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_zapier_hook">
                                            <?= __('Zapier Webhook URL', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_zapier_hook"
                                               name="ays_zapier_hook"
                                               value="<?= $zapier_hook; ?>"
                                        >
                                    </div>
                                </div>
                                <blockquote>
                                    <?php echo sprintf( esc_attr( __("If you don't have any ZAP created, go <a href='%s' target='_blank'> here...</a>.", $this->plugin_name) ), "https://zapier.com/app/editor/"); ?>
                                </blockquote>
                                <blockquote>
                                    <?php echo __("We will send you all data from quiz information form with the “AysQuiz” key by POST method.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/activecampaign_logo.png" alt="">
                            <h5><?php echo __('ActiveCampaign',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_active_camp_url">
                                            <?= __('API Access URL', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_active_camp_url"
                                               name="ays_active_camp_url"
                                               value="<?= $active_camp_url; ?>"
                                        >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_active_camp_api_key">
                                            <?= __('API Access Key', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_active_camp_api_key"
                                               name="ays_active_camp_api_key"
                                               value="<?= $active_camp_api_key; ?>"
                                        >
                                    </div>
                                </div>
                                <blockquote>
                                    <?= __("Your API URL and Key can be found in your account on the My Settings page under the “Developer” tab.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/slack_logo.png" alt="">
                            <h5><?php echo __('Slack',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?php if (!$slack_oauth): ?>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <button id="slackInstructionsPopOver" type="button" class="btn btn-info"
                                                    title="<?= __("Slack Integration Setup Instructions", $this->plugin_name) ?>"><?= __("Instructions", $this->plugin_name) ?></button>
                                            <div class="d-none" id="slackInstructions">
                                                <p><?= sprintf(__("1. You will need to " . "<a href='%s' target='_blank'>%s</a>" . " new Slack App.", $this->plugin_name), "https://api.slack.com/apps?new_app=1", "create"); ?></p>
                                                <p><?= __("2. Complete Project creation for get App credentials.", $this->plugin_name) ?></p>
                                                <p><?= __("3. Next, go to the Features > OAuth & Permissions > Redirect URLs section.", $this->plugin_name) ?></p>
                                                <p><?= __("4. Click Add a new Redirect URL.", $this->plugin_name) ?></p>
                                                <p><?= __("5. In the shown input field, put this value below", $this->plugin_name) ?></p>
                                                <p>
                                                    <code><?= ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "&oauth=slack" ?></code>
                                                </p>
                                                <p><?= __("6. Then click the Add button.", $this->plugin_name) ?></p>
                                                <p><?= __("7. Then click the Save URLs button.", $this->plugin_name) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_slack_client">
                                            <?= __('App Client ID', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_slack_client"
                                               name="ays_slack_client"
                                               value="<?= $slack_client; ?>"
                                        >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_slack_oauth">
                                            <?= __('Slack Authorization', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?php if ($slack_oauth): ?>
                                            <span class="btn btn-success pointer-events-none">
                                                <?= __("Authorized", $this->plugin_name) ?></span>
                                        <?php else: ?>
                                            <button type="button" id="slackOAuth2"
                                                    class="btn btn-outline-secondary disabled">
                                                <?= __("Authorize", $this->plugin_name) ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_slack_secret">
                                            <?= __('App Client Secret', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_slack_secret"
                                               name="ays_slack_secret"
                                               value="<?= $slack_secret; ?>" <?= $slack_oauth ?: "readonly" ?>
                                        >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_slack_oauth">
                                            <?= __('App Access Token', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?php if ($slack_oauth): ?>
                                            <button type="button"
                                                    data-code="<?= !empty($slack_temp_code) ? $slack_temp_code : "" ?>"
                                                    id="slackOAuthGetToken"
                                                    data-success="<?= __("Access granted", $this->plugin_name) ?>"
                                                    class="btn btn-outline-secondary disabled"><?= __("Get it", $this->plugin_name) ?></button>
                                        <?php else: ?>
                                            <button type="button"
                                                    class="btn btn-outline-secondary disabled"><?= __("Need Authorization", $this->plugin_name) ?>
                                            </button>
                                        <?php endif; ?>
                                        <input type="hidden" id="ays_slack_token" name="ays_slack_token" value="<?= $slack_token; ?>">
                                    </div>
                                </div>
                                <blockquote>
                                    <?= __("You can get your App Client ID and Client Secret from your App’s Basic Information page.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <!-- _________________________GOOGLE SHEETS START____________________ -->
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/sheets_logo.png" alt="">
                            <h5><?php echo __('Google Sheets',$this->plugin_name)?></h5>
                        </legend>
                        <p style="color: red;"><?php echo $gerror_message; ?></p>
                        <?php if( $google_token ): ?>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <blockquote>
                                    <span style="margin:0;font-weight:normal;font-style:normal;"><?php
                                        echo sprintf(
                                            __( "You are connected to Google Sheets with %s (%s) account.", $this->plugin_name ),
                                            "<strong><em>" . $google_name . "</em></strong>",
                                            "<a href='mailto:" . $google_email . "'><strong><em>" . $google_email . "</em></strong></a>"
                                        );
                                    ?></span>
                                </blockquote>
                                <br>
                                <input type="submit" class="btn btn-outline-danger" name="ays_disconnect_google_sheets" value="<?php echo __( 'Disconnect', $this->plugin_name ); ?>">
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <button id="googleInstructionsPopOver" type="button" class="btn btn-info" data-original-title="Google Integration Setup Instructions" ><?php echo __('Instructions', $this->plugin_name); ?></button>
                                        <div class="d-none" id="googleInstructions">
                                            <ol style="font-size:15px;">
                                                <li><?php echo sprintf( __('Enable Your Google Sheet API from your %s Google Cloud Platform: %s', $this->plugin_name),
                                                        "<strong>",
                                                        "</strong>"
                                                    ); ?>
                                                    <a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a>.
                                                </li>
                                                <li><?php echo sprintf( __('Follow the instructions and create credentials in case you still do not have them. Here, you need to make sure you have got a project and it is active. %s If you don’t you will have to create a project. So, please follow these steps to %s create a project. %s', $this->plugin_name),
                                                        "<br>",
                                                        "<a href='https://console.cloud.google.com/apis/credentials' target='_blank'>",
                                                        "</a>"
                                                    ); ?>
                                                    <ul style="list-style-type: initial; padding-left: 40px;margin: 11px 0;">
                                                        <li>
                                                            <?php echo sprintf( __( "Click on the %s Create Project %s button. Write the %s Product Name. %s After this, click on the %s Create %s button.", $this->plugin_name),
                                                                "<strong>",
                                                                "</strong>",
                                                                "<strong>",
                                                                "</strong>",
                                                                "<strong>",
                                                                "</strong>"
                                                            ); ?>
                                                        </li>

                                                        <li>
                                                            <?php echo sprintf( __( "Select the project from the %s Search %s field. Click on the %s Configure Consent Screen %s button. %s By this, you can move to the %s OAuth consent screen %s tab. Click on the %s Create %s button.", $this->plugin_name),
                                                                "<strong>",
                                                                "</strong>",
                                                                "<strong>",
                                                                "</strong>",
                                                                "<br>",
                                                                "<strong>",
                                                                "</strong>",
                                                                "<strong>",
                                                                "</strong>"
                                                            ); ?>
                                                        </li>

                                                        <li>
                                                            <?php echo sprintf( __( "Fill in the required fields: %s App Name, User Support email, Email addresses. %s Then, click on the %s Save %s and %s Continue %s button.", $this->plugin_name),
                                                                "<strong>",
                                                                "</strong><br>",
                                                                "<strong>",
                                                                "</strong>",
                                                                "<strong>",
                                                                "</strong>"
                                                            ); ?>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <?php echo sprintf( __( "It is time to %s create Test Users. %s For that, click on the %s Add Users %s button and write the email address for the test user. %s Click on the %s Add %s button. You have successfully created a test user!", $this->plugin_name),
                                                        "<strong>",
                                                        "</strong><br>",
                                                        "<strong>",
                                                        "</strong>",
                                                        "<br>",
                                                        "<strong>",
                                                        "</strong>"
                                                    ); ?>
                                                </li>
                                                <li>
                                                    <?php echo sprintf( __( "Go back to the %s Credentials %s tab and click on the %s Create Credentials > OAuth client ID %s button. Fill in the %s Application type %s required field. %s Choose %s the application type as a %s Web application. %s", $this->plugin_name),
                                                        "<strong>",
                                                        "</strong>",
                                                        "<strong>",
                                                        "</strong>",
                                                        "<strong>",
                                                        "</strong>",
                                                        "<br><strong>",
                                                        "</strong>",
                                                        "<strong>",
                                                        "</strong>"
                                                    ); ?>
                                                </li>
                                                <li>
                                                    <?php echo sprintf( __( "Add the following link in the %s Authorized redirect URIs field. %s", $this->plugin_name),
                                                        "<strong>",
                                                        "</strong>"
                                                    ); ?>
                                                    <code data-toggle="tooltip" title="<?php echo __('Click for copy.', $this->plugin_name); ?>" style="padding: 5px;margin: 20px 0 10px;display: inline-block;position: relative;" onclick="selectElementContents(this)">
                                                        <?php echo $google_redirect_url?>
                                                        <i style="position: absolute;top: 5px;right: 5px;font-size: 20px;cursor: pointer;color: #000;font-weight: 900;" class="ays_fa ays_fa_clone_900"></i>
                                                    </code>
                                                </li>
                                                <li>
                                                    <?php echo sprintf( __( "Click on the %s Create %s button.", $this->plugin_name),
                                                        "<strong>",
                                                        "</strong>"
                                                    ); ?>
                                                </li>
                                                <li>
                                                    <?php echo sprintf( __( "After the successful authorization, %s copy Your Client ID %s and %s Your Client Secret %s and paste them into the corresponding fields.", $this->plugin_name),
                                                        "<strong>",
                                                        "</strong>",
                                                        "<strong>",
                                                        "</strong>"
                                                    ); ?>
                                                </li>
                                                <li>
                                                    <?php echo sprintf( __( "Click on the %s Connect %s button.", $this->plugin_name),
                                                        "<strong>",
                                                        "</strong>"
                                                    ); ?>
                                                </li>
                                                <li>
                                                    <?php echo sprintf( __( " %s Choose %s your  %s Google account. %s Make sure you have given access to the  %s See, edit, create, and delete  %s your Google Sheets spreadsheets by ticking the checkbox.", $this->plugin_name),
                                                        "<strong>",
                                                        "</strong>",
                                                        "<strong>",
                                                        "</strong>",
                                                        "<strong>",
                                                        "</strong>"
                                                    ); ?>
                                                </li>
                                            </ol>
                                            <p>
                                                <?php echo sprintf( __( "%s Note: %s The %s Google Sheets API extension %s must be activated for you. If this extension is not enabled for you, the Sheet will not be created. %s To activate it, please go to the %s Library section %s of the %s Google Console. %s Search %s 'Google Sheet API' %s and %s install %s the %s Google Sheets API %s extension. Then, %s click %s on the %s MANAGE button. %s", $this->plugin_name),
                                                    "<strong>",
                                                    "</strong>",
                                                    "<strong>",
                                                    "</strong>",
                                                    "<br>",
                                                    "<strong>",
                                                    "</strong>",

                                                    "<strong>",
                                                    "</strong><br>",
                                                    "<strong>",
                                                    "</strong>",

                                                    "<strong>",
                                                    "</strong>",
                                                    "<strong>",
                                                    "</strong>",
                                                    "<strong>",
                                                    "</strong>",
                                                    "<strong>",
                                                    "</strong>"
                                                ); ?>
                                            </p>
                                            <p style="font-size:16px;"><?php echo __('<b>Congratulations!</b> You are connected to the Google Sheets API and enabled the integration.', $this->plugin_name); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_google_client">
                                            <?= __('Google Client ID', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="ays-text-input" id="ays_google_client" name="ays_google_client" value="<?= $google_client; ?>" >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label for="ays_google_secret">
                                            <?= __('Google Client Secret', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="ays-text-input" id="ays_google_secret" name="ays_google_secret" value="">
                                        <input type="hidden" id="ays_google_redirect" name="ays_google_redirect" value="<?php echo $google_redirect_url; ?>">
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <button type="submit" name="googleOAuth2" id="googleOAuth2" class="btn btn-outline-info">
                                            <?= __("Connect", $this->plugin_name) ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </fieldset>
                    <!-- __________________________GOOGLE SHEETS END_____________________ -->
                    <hr/>
                    <?php
                        if(has_action('ays_qm_settings_page_integrations')){
                            do_action( 'ays_qm_settings_page_integrations' );
                        }
                    ?>
                </div>
                <div id="tab3" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab3') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle"><?php echo __('Shortcodes',$this->plugin_name)?></p>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Individual Leaderboard Settings',$this->plugin_name)?></h5>
                        </legend>
                        <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                            <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                                <a href="https://www.youtube.com/watch?v=trZEpGWm9GE" target="_blank">
                                    <?php echo __("How to add leadboard - video", $this->plugin_name); ?>
                                </a>
                            </div>
                        </div>
                        <blockquote>
                            <?php echo __( "It is designed for a particular quiz’s results.", $this->plugin_name ); ?>
                        </blockquote>
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_invidLead">
                                    <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and paste it to any post/page to see the list of the top user’s who passed this quiz.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_invidLead" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_leaderboard id="Your_Quiz_ID" from="Y-m-d H:i:s" to="Y-m-d H:i:s"]'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_count">
                                    <?php echo __('Users count',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('How many users’ results will be shown in the leaderboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"                 
                                    id="ays_leadboard_count" 
                                    name="ays_leadboard_count"
                                    value="<?php echo $ind_leadboard_count; ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_width">
                                    <?php echo __('Width',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of the Leaderboard box. For 100% leave it blank.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"                 
                                    id="ays_leadboard_width" 
                                    name="ays_leadboard_width"
                                    value="<?php echo $ind_leadboard_width; ?>"
                                />
                                <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Group users by',$this->plugin_name)?>
                                    <!-- <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Select the way for grouping the results. If you want to make Leaderboard for logged in users, then choose ID. It will collect results by WP user ID. If you want to make Leaderboard for guests, then you need to choose Email and enable Information Form and Email, Name options from quiz settings. It will group results by emails and display guests Names.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a> -->
                                    <a class="ays_help" data-toggle="tooltip" data-html="true"
                                        title="<?php
                                            echo __('Select the way for grouping the results:',$this->plugin_name) .
                                            "<ul style='list-style-type: circle;padding-left: 20px;'>".
                                                "<li>". esc_attr (__('ID: If you want to make Leaderboard for logged in users, then choose ID. It will collect results by WP user ID.',$this->plugin_name)) ."</li>".
                                                "<li>". esc_attr (__('Email: If you want to make Leaderboard for guests, then you need to choose Email and enable Information Form and Email, Name options from quiz settings. It will group results by emails and display guests Names.',$this->plugin_name)) ."</li>".
                                                "<li>". esc_attr( __("No grouping: If you don't want to group users, then choose No grouping. The user will only need to fill in information form to be on the leaderboard.",$this->plugin_name) ) ."</li>".
                                            "</ul>";
                                        ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_orderby" value="id" <?php echo $ind_leadboard_orderby == "id" ? "checked" : ""; ?> />
                                    <span><?php echo __( "ID", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_orderby" value="email" <?php echo $ind_leadboard_orderby == "email" ? "checked" : ""; ?> />
                                    <span><?php echo __( "Email", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_orderby" value="no_grouping" <?php echo $ind_leadboard_orderby == "no_grouping" ? "checked" : ""; ?> />
                                    <span><?php echo __( "No grouping", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Show user’s result',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the users’ Average or Maximum results in the leaderboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_sort" value="avg" <?php echo $ind_leadboard_sort == "avg" ? "checked" : ""; ?> />
                                    <span><?php echo __( "AVG", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_sort" value="max" <?php echo $ind_leadboard_sort == "max" ? "checked" : ""; ?> />
                                    <span><?php echo __( "MAX", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Show points',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Decide how to display the score. For instance, if you choose the correct answer count, the score will be shown in this format: 8/10.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_points_display" value="without_max_point" <?php echo $ind_leadboard_points_display == "without_max_point" ? "checked" : ""; ?> />
                                    <span><?php echo __( "Without maximum point", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_points_display" value="with_max_point" <?php echo $ind_leadboard_points_display == "with_max_point" ? "checked" : ""; ?> />
                                    <span><?php echo __( "With maximum point", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_enable_pagination">
                                    <?php echo __( "Enable pagination", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('When this option is enabled, the data on the leaderboard will be displayed with pages. You can sort the data by leaderboard columns.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_leadboard_enable_pagination" class="ays-checkbox-input" name="ays_leadboard_enable_pagination" value="on" <?php echo ( $leadboard_enable_pagination ) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_enable_user_avatar">
                                    <?php echo __( "Enable User Avatar", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('By enabling this option, you can display the user avatar on the Front-end. Note: The Name field (Information Form option) must be enabled so that this option can work for you. If the Name table column is disabled, but the User Avatar option is enabled, the avatar will not be displayed on the front end. The user avatar will be displayed next to the name of the user.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_leadboard_enable_user_avatar" class="ays-checkbox-input" name="ays_leadboard_enable_user_avatar" value="on" <?php echo ( $leadboard_enable_user_avatar ) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_color">
                                    <?php echo __('Color',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Top color of the leaderboard',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_leadboard_color" name="ays_leadboard_color" data-alpha="true" value="<?php echo $ind_leadboard_color; ?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_custom_css">
                                    <?php echo __('Custom CSS',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Field for entering your own CSS code',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <textarea class="ays-textarea" id="ays_leadboard_custom_css" name="ays_leadboard_custom_css" cols="30"
                                      rows="10" style="height: 80px;"><?php echo $ind_leadboard_suctom_css; ?></textarea>
                            </div>
                        </div> <!-- Custom leadboard CSS -->
                        <?php if($empry_dur_count > 0): ?>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Update duration field for old results',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This button needs to work only once. If you see 0 in the Duration column for some results, please click once to this button and it will regenerate duration for old results. It may happen if you update our plugin from the old version to the latest.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <a class="button" href="?page=<?php echo $_REQUEST['page']; ?>&action=update_duration&ays_quiz_tab=tab3"><?php echo __('Update duration old data', $this->plugin_name); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label>
                                    <?php echo __( "Leaderboard Columns", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can sort table columns and select which columns must display on the front-end.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                                <div class="ays-show-user-page-table-wrap">
                                    <ul class="ays-show-user-page-table">
                                        <?php
                                            foreach ($ind_leadboard_columns_order as $key => $val) {
                                                $checked = '';
                                                if(isset($ind_leadboard_columns[$val]) && $ind_leadboard_columns[$val] != ""){
                                                    $checked = 'checked';
                                                }
                                                if ($val == '') {
                                                   $checked = '';
                                                   $ind_default_leadboard_column_names[$val] = $key;
                                                   $val = $key;
                                                }

                                                if ( !isset( $ind_default_leadboard_column_names[$val] ) ) {
                                                    continue;
                                                }
                                                ?>
                                                <li class="ays-user-page-option-row ui-state-default">
                                                    <input type="hidden" value="<?php echo $val; ?>" name="ays_ind_leadboard_columns_order[]"/>
                                                    <input type="checkbox" id="ays_ilb_show_<?php echo $val; ?>" value="<?php echo $val; ?>" class="ays-checkbox-input" name="ays_ind_leadboard_columns[<?php echo $val; ?>]" <?php echo $checked; ?>/>
                                                    <label for="ays_ilb_show_<?php echo $val; ?>">
                                                        <?php echo $ind_default_leadboard_column_names[$val]; ?>
                                                    </label>
                                                </li>
                                                <?php
                                            }
                                         ?>
                                    </ul>
                               </div>
                            </div>
                        </div>
                        <hr>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul">
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sFrom%s', $this->plugin_name ) . ' - ' . __( 'Specify the start date to display the leaderboard (e.g. 2021-01-01 00:00:00)', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sTo%s', $this->plugin_name ) . ' - ' . __( 'Specify the end date to display the leaderboard
                                            (e.g. 2021-01-31 00:00:00)', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                            </ul>
                        </blockquote>
                    </fieldset> <!-- Individual Leaderboard Settings -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5 class="ays-subtitle"><?php echo __('Global Leaderboard Settings',$this->plugin_name)?></h5>
                        </legend>
                        <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                            <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                                <a href="https://www.youtube.com/watch?v=trZEpGWm9GE" target="_blank">
                                    <?php echo __("How to add leadboard - video", $this->plugin_name); ?>
                                </a>
                            </div>
                        </div>
                        <blockquote>
                            <?php echo __( "It is designed for all quizzes results.", $this->plugin_name ); ?>
                        </blockquote>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_globLead">
                                    <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and paste it to any post/page to see the list of the top user’s who passed any quiz.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_globLead" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_gleaderboard from="Y-m-d H:i:s" to="Y-m-d H:i:s"]'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_count">
                                    <?php echo __('Users count',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('How many users’ results will be shown in the leaderboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"                 
                                    id="ays_gleadboard_count" 
                                    name="ays_gleadboard_count"
                                    value="<?php echo $glob_leadboard_count; ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_width">
                                    <?php echo __('Width',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of the Leaderboard box. It accepts only numeric values. For 100% leave it blank.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"                 
                                    id="ays_gleadboard_width" 
                                    name="ays_gleadboard_width"
                                    value="<?php echo $glob_leadboard_width; ?>"
                                />
                                <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Group users by',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Select the way for grouping the results. If you want to make Leaderboard for logged in users, then choose ID. It will collect results by WP user ID. If you want to make Leaderboard for guests, then you need to choose Email and enable Information Form and Email, Name options from quiz settings. It will group results by emails and display guests Names.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_orderby" value="id" <?php echo $glob_leadboard_orderby == "id" ? "checked" : ""; ?> />
                                    <span><?php echo __( "ID", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_orderby" value="email" <?php echo $glob_leadboard_orderby == "email" ? "checked" : ""; ?> />
                                    <span><?php echo __( "Email", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Show user’s result',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the users’ Average, Maximum or Sum results in the leaderboard. SUM does not work with Score(table column)',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_sort" value="avg" <?php echo $glob_leadboard_sort == "avg" ? "checked" : ""; ?> />
                                    <span><?php echo __( "AVG", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_sort" value="max" <?php echo $glob_leadboard_sort == "max" ? "checked" : ""; ?> />
                                    <span><?php echo __( "MAX", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_sort" value="sum" <?php echo $glob_leadboard_sort == "sum" ? "checked" : ""; ?> />
                                    <span><?php echo __( "SUM", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_enable_pagination">
                                    <?php echo __( "Enable pagination", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('When this option is enabled, the data on the leaderboard will be displayed with pages. You can sort the data by leaderboard columns.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_gleadboard_enable_pagination" class="ays-checkbox-input" name="ays_gleadboard_enable_pagination" value="on" <?php echo ( $glob_leadboard_enable_pagination ) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_enable_user_avatar">
                                    <?php echo __( "Enable User Avatar", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('By enabling this option, you can display the user avatar on the Front-end. Note: The Name field (Information Form option) must be enabled so that this option can work for you. If the Name table column is disabled, but the User Avatar option is enabled, the avatar will not be displayed on the front end. The user avatar will be displayed next to the name of the user.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_gleadboard_enable_user_avatar" class="ays-checkbox-input" name="ays_gleadboard_enable_user_avatar" value="on" <?php echo ( $glob_leadboard_enable_user_avatar ) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_color">
                                    <?php echo __('Color',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Top color of the leaderboard',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_gleadboard_color" name="ays_gleadboard_color" data-alpha="true" value="<?php echo $glob_leadboard_color; ?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_custom_css">
                                    <?php echo __('Custom CSS',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Field for entering your own CSS code',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <textarea class="ays-textarea" id="ays_gleadboard_custom_css" name="ays_gleadboard_custom_css" cols="30"
                                      rows="10" style="height: 80px;"><?php echo $glob_leadboard_suctom_css; ?></textarea>
                            </div>
                        </div> <!-- Custom global leadboard CSS -->
                        <?php if($empry_dur_count > 0): ?>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Update duration field for old results',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This button needs to work only once. If you see 0 in the Duration column for some results, please click once to this button and it will regenerate duration for old results. It may happen if you update our plugin from the old version to the latest.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <a class="button" href="?page=<?php echo $_REQUEST['page']; ?>&action=update_duration&ays_quiz_tab=tab3"><?php echo __('Update duration old data', $this->plugin_name); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label>
                                    <?php echo __( "Leaderboard Columns", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can sort table columns and select which columns must display on the front-end.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                                <div class="ays-show-user-page-table-wrap">
                                    <ul class="ays-show-user-page-table">
                                        <?php
                                            foreach ($glob_leadboard_columns_order as $key => $val) {
                                                $checked = '';
                                                if(isset($glob_leadboard_columns[$val])){
                                                    $checked = 'checked';
                                                }
                                                if ($val == '') {
                                                   $checked = '';
                                                   $default_leadboard_column_names[$val] = $key;
                                                   $val = $key;
                                                }
                                                ?>
                                                <li class="ays-user-page-option-row ui-state-default">
                                                    <input type="hidden" value="<?php echo $val; ?>" name="ays_glob_leadboard_columns_order[]"/>
                                                    <input type="checkbox" id="ays_glb_show_<?php echo $val; ?>" value="<?php echo $val; ?>" class="ays-checkbox-input" name="ays_glob_leadboard_columns[<?php echo $val; ?>]" <?php echo $checked; ?>/>
                                                    <label for="ays_glb_show_<?php echo $val; ?>">
                                                        <?php echo $default_leadboard_column_names[$val]; ?>
                                                    </label>
                                                </li>
                                                <?php
                                            }
                                         ?>
                                    </ul>
                               </div>
                            </div>
                        </div>
                        <hr>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul">
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sFrom%s', $this->plugin_name ) . ' - ' . __( 'Specify the start date to display the leaderboard (e.g. 2021-01-01 00:00:00)', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sTo%s', $this->plugin_name ) . ' - ' . __( 'Specify the end date to display the leaderboard
                                            (e.g. 2021-01-31 00:00:00)', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                            </ul>
                        </blockquote>
                    </fieldset> <!-- Global Leaderboard Settings -->
                    <hr/>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5 class="ays-subtitle"><?php echo __('Leaderboard By Quiz Category Settings',$this->plugin_name)?></h5>
                        </legend>
                        <blockquote>
                            <?php echo __( "It is designed for a particular quiz category results.", $this->plugin_name ); ?>
                        </blockquote>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_globLead_cat">
                                    <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and paste it to any post/page to see the list of the top user’s who passed any quiz.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_globLead_cat" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_cat_gleaderboard id="Your_Quiz_Category_ID" from="Y-m-d H:i:s" to="Y-m-d H:i:s"]'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_quiz_cat_count">
                                    <?php echo __('Users count',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('How many users’ results will be shown in the leaderboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"
                                    id="ays_gleadboard_quiz_cat_count"
                                    name="ays_gleadboard_quiz_cat_count"
                                    value="<?php echo $glob_quiz_cat_leadboard_count; ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_quiz_cat_width">
                                    <?php echo __('Width',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of the Leaderboard box. It accepts only numeric values. For 100% leave it blank.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"
                                    id="ays_gleadboard_quiz_cat_width"
                                    name="ays_gleadboard_quiz_cat_width"
                                    value="<?php echo $glob_quiz_cat_leadboard_width; ?>"
                                />
                                <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Group users by',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Select the way for grouping the results. If you want to make Leaderboard for logged in users, then choose ID. It will collect results by WP user ID. If you want to make Leaderboard for guests, then you need to choose Email and enable Information Form and Email, Name options from quiz settings. It will group results by emails and display guests Names.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_quiz_cat_orderby" value="id" <?php echo $glob_quiz_cat_leadboard_orderby == "id" ? "checked" : ""; ?> />
                                    <span><?php echo __( "ID", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_quiz_cat_orderby" value="email" <?php echo $glob_quiz_cat_leadboard_orderby == "email" ? "checked" : ""; ?> />
                                    <span><?php echo __( "Email", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Show user’s result',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the users’ Average, Maximum or Sum results in the leaderboard. SUM does not work with Score(table column)',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_quiz_cat_sort" value="avg" <?php echo $glob_quiz_cat_leadboard_sort == "avg" ? "checked" : ""; ?> />
                                    <span><?php echo __( "AVG", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_quiz_cat_sort" value="max" <?php echo $glob_quiz_cat_leadboard_sort == "max" ? "checked" : ""; ?> />
                                    <span><?php echo __( "MAX", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_quiz_cat_sort" value="sum" <?php echo $glob_quiz_cat_leadboard_sort == "sum" ? "checked" : ""; ?> />
                                    <span><?php echo __( "SUM", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_quiz_cat_enable_pagination">
                                    <?php echo __( "Enable pagination", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('When this option is enabled, the data on the leaderboard will be displayed with pages. You can sort the data by leaderboard columns.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_gleadboard_quiz_cat_enable_pagination" class="ays-checkbox-input" name="ays_gleadboard_quiz_cat_enable_pagination" value="on" <?php echo ( $glob_quiz_cat_leadboard_enable_pagination ) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_quiz_cat_enable_user_avatar">
                                    <?php echo __( "Enable User Avatar", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('By enabling this option, you can display the user avatar on the Front-end. Note: The Name field (Information Form option) must be enabled so that this option can work for you. If the Name table column is disabled, but the User Avatar option is enabled, the avatar will not be displayed on the front end. The user avatar will be displayed next to the name of the user.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_gleadboard_quiz_cat_enable_user_avatar" class="ays-checkbox-input" name="ays_gleadboard_quiz_cat_enable_user_avatar" value="on" <?php echo ( $glob_quiz_cat_leadboard_enable_user_avatar ) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_quiz_cat_color">
                                    <?php echo __('Color',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Top color of the leaderboard',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_gleadboard_quiz_cat_color" name="ays_gleadboard_quiz_cat_color" data-alpha="true" value="<?php echo $glob_quiz_cat_leadboard_color; ?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_quiz_cat_custom_css">
                                    <?php echo __('Custom CSS',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Field for entering your own CSS code',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <textarea class="ays-textarea" id="ays_gleadboard_quiz_cat_custom_css" name="ays_gleadboard_quiz_cat_custom_css" cols="30"
                                      rows="10" style="height: 80px;"><?php echo $glob_quiz_cat_leadboard_cuctom_css; ?></textarea>
                            </div>
                        </div> <!-- Custom global leadboard CSS -->
                        <?php if($empry_dur_count > 0): ?>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Update duration field for old results',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This button needs to work only once. If you see 0 in the Duration column for some results, please click once to this button and it will regenerate duration for old results. It may happen if you update our plugin from the old version to the latest.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <a class="button" href="?page=<?php echo $_REQUEST['page']; ?>&action=update_duration&ays_quiz_tab=tab3"><?php echo __('Update duration old data', $this->plugin_name); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label>
                                    <?php echo __( "Leaderboard Columns", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can sort table columns and select which columns must display on the front-end.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                                <div class="ays-show-user-page-table-wrap">
                                    <ul class="ays-show-user-page-table">
                                        <?php
                                            foreach ($glob_quiz_cat_leadboard_columns_order as $key => $val) {
                                                $checked = '';
                                                if(isset($glob_quiz_cat_leadboard_columns[$val]) && $glob_quiz_cat_leadboard_columns[$val] != ""){
                                                    $checked = 'checked';
                                                }
                                                if ($val == '') {
                                                   $checked = '';
                                                   $glob_quiz_cat_default_leadboard_column_names[$val] = $key;
                                                   $val = $key;
                                                }

                                                if ( !isset( $glob_quiz_cat_default_leadboard_column_names[$val] ) ) {
                                                    continue;
                                                }
                                                ?>
                                                <li class="ays-user-page-option-row ui-state-default">
                                                    <input type="hidden" value="<?php echo $val; ?>" name="ays_glob_quiz_cat_leadboard_columns_order[]"/>
                                                    <input type="checkbox" id="ays_glb_quiz_cat_show_<?php echo $val; ?>" value="<?php echo $val; ?>" class="ays-checkbox-input" name="ays_glob_quiz_cat_leadboard_columns[<?php echo $val; ?>]" <?php echo $checked; ?>/>
                                                    <label for="ays_glb_quiz_cat_show_<?php echo $val; ?>">
                                                        <?php echo $glob_quiz_cat_default_leadboard_column_names[$val]; ?>
                                                    </label>
                                                </li>
                                                <?php
                                            }
                                         ?>
                                    </ul>
                               </div>
                            </div>
                        </div>
                        <hr>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul">
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sFrom%s', $this->plugin_name ) . ' - ' . __( 'Specify the start date to display the leaderboard (e.g. 2021-01-01 00:00:00)', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sTo%s', $this->plugin_name ) . ' - ' . __( 'Specify the end date to display the leaderboard
                                            (e.g. 2021-01-31 00:00:00)', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                            </ul>
                        </blockquote>
                    </fieldset> <!-- Leaderboard By Quiz Category Settings -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5 class="ays-subtitle"><?php echo __('User Leaderboard Position Settings',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_user_leaderboard_position">
                                    <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Copy the shortcode and paste it to any post/page to see the leaderboard position of the current user. It works with Individual Leaderboard shortcode options.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_user_leaderboard_position" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_user_leaderboard_position id="YOUR_QUIZ_ID"]'>
                            </div>
                        </div>
                    </fieldset> <!-- User Leaderboard Position Settings -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('User Page Settings',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_user_page">
                                    <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and insert it to any post to show the current user’s results history.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_user_page" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_user_page id="Your_Quiz_Category_ID"]'>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_quiz_hide_correct_answer_user_page">
                                    <?php echo __( "Hide correct answer", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Tick the checkbox if you want to hide the correct answers presented in the detailed report.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_quiz_hide_correct_answer_user_page" class="ays-checkbox-input" name="ays_quiz_hide_correct_answer_user_page" value="on" <?php echo $hide_correct_answer; ?>>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label>
                                    <?php echo __( "User Page results table columns", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can sort table columns and select which columns must display on the front-end.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                                <div class="ays-show-user-page-table-wrap">
                                    <ul class="ays-show-user-page-table">
                                        <?php
                                            foreach ($user_page_columns_order as $key => $val) {
                                                $checked = '';
                                                if(isset($user_page_columns[$key]) && $user_page_columns[$key] != ''){
                                                    $checked = 'checked';
                                                }

                                                $default_user_page_column_names_label = '';
                                                if( isset( $default_user_page_column_names[$val] ) && $default_user_page_column_names[$val] != '' ){
                                                    $default_user_page_column_names_label = $default_user_page_column_names[$val];
                                                }

                                                if( $default_user_page_column_names_label == '' ){
                                                    continue;
                                                }

                                                ?>
                                                <li class="ays-user-page-option-row ui-state-default">
                                                    <input type="hidden" value="<?php echo $key; ?>" name="ays_user_page_columns_order[<?php echo $key; ?>]"/>
                                                    <input type="checkbox" id="ays_show_<?php echo $key; ?>" value="<?php echo $key; ?>" class="ays-checkbox-input" name="ays_user_page_columns[<?php echo $key; ?>]" <?php echo $checked; ?>/>
                                                    <label for="ays_show_<?php echo $key; ?>">
                                                        <?php echo $default_user_page_column_names_label; ?>
                                                    </label>
                                                </li>
                                                <?php
                                            }
                                         ?>
                                    </ul>
                               </div>
                            </div>
                        </div>
                    </fieldset> <!-- User Page Settings -->
                    <hr/>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('All Results Settings',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_all_results">
                                    <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and insert it to any post to show all results.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_all_results" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_all_results id="Your_Category_ID"]'>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_all_results_show_publicly">
                                    <?php echo __( "Show to guests too", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the All results table to guests as well. By default, it is displayed only for logged-in users. If this option is disabled, then only the logged-in users will be able to see the table. Note: Despite the fact of showing the table to the guests, the table will contain only info of the logged-in users.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" class="ays-checkbox-input" id="ays_all_results_show_publicly" name="ays_all_results_show_publicly" value="on" <?php echo $all_results_show_publicly ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label>
                                    <?php echo __( "Table columns", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can sort table columns and select which columns must display on the front-end.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                                <div class="ays-show-user-page-table-wrap">
                                    <ul class="ays-show-user-page-table">
                                        <?php
                                            foreach ($all_results_columns_order as $key => $val) {
                                                $checked = '';
                                                if(isset($all_results_columns[$val]) && $all_results_columns[$val] != ''){
                                                    $checked = 'checked';
                                                }

                                                $default_all_results_column_names_label = '';
                                                if( isset( $default_all_results_column_names[$val] ) && $default_all_results_column_names[$val] != '' ){
                                                    $default_all_results_column_names_label = $default_all_results_column_names[$val];
                                                }

                                                if( $default_all_results_column_names_label == '' ){
                                                    continue;
                                                }

                                                ?>
                                                <li class="ays-user-page-option-row ui-state-default">
                                                    <input type="hidden" value="<?php echo $val; ?>" name="ays_all_results_columns_order[]"/>
                                                    <input type="checkbox" id="ays_show_result<?php echo $val; ?>" value="<?php echo $val; ?>" class="ays-checkbox-input" name="ays_all_results_columns[<?php echo $val; ?>]" <?php echo $checked; ?>/>
                                                    <label for="ays_show_result<?php echo $val; ?>">
                                                        <?php echo $default_all_results_column_names_label; ?>
                                                    </label>
                                                </li>
                                                <?php
                                            }
                                         ?>
                                    </ul>
                               </div>
                            </div>
                        </div>
                        <hr>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul" style="margin: 0;">
                                <li style="padding-bottom: 5px;">
                                    <?php
                                        echo sprintf(
                                            __( '%s ID %s', $this->plugin_name ) . ' - ' . esc_attr( __( "Enter the ID of the quiz category. Example: id='23'. Note: In case you don't insert the ID of the Quiz Category, all results of all the quizzes will be displayed on the Front-end.", $this->plugin_name ) ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                            </ul>
                        </blockquote>
                    </fieldset> <!-- All Results Settings -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Single Quiz Results Settings',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_all_results">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and insert it to any post to show quiz all results.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_all_results" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_all_results id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_all_results_show_publicly">
                                            <?php echo __( "Show to guests too", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the Single quiz results table to guests as well. By default, it is displayed only for logged-in users. If this option is disabled, then only the logged-in users will be able to see the table. Note: Despite the fact of showing the table to the guests, the table will contain only info of the logged-in users.',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_all_results_show_publicly" name="ays_quiz_all_results_show_publicly" value="on" <?php echo $quiz_all_results_show_publicly ? 'checked' : ''; ?> />
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>
                                            <?php echo __( "Table columns", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can sort table columns and select which columns must display on the front-end.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <div class="ays-show-user-page-table-wrap">
                                            <ul class="ays-show-user-page-table">
                                                <?php
                                                    foreach ($quiz_all_results_columns_order as $key => $val) {
                                                        $checked = '';
                                                        if(isset($quiz_all_results_columns[$val]) && $quiz_all_results_columns[$val] != ''){
                                                            $checked = 'checked';
                                                        }

                                                        if ($val == '') {
                                                           $checked = '';
                                                           $default_leadboard_column_names[$val] = $key;
                                                           $val = $key;
                                                        }

                                                        $default_quiz_all_results_column_names_label = '';
                                                        if( isset( $default_quiz_all_results_column_names[$val] ) && $default_quiz_all_results_column_names[$val] != '' ){
                                                            $default_quiz_all_results_column_names_label = $default_quiz_all_results_column_names[$val];
                                                        }

                                                        if( $default_quiz_all_results_column_names_label == '' ){
                                                            continue;
                                                        }

                                                        ?>
                                                        <li class="ays-user-page-option-row ui-state-default">
                                                            <input type="hidden" value="<?php echo $val; ?>" name="ays_quiz_all_results_columns_order[]"/>
                                                            <input type="checkbox" id="ays_show_quiz_result<?php echo $val; ?>" value="<?php echo $val; ?>" class="ays-checkbox-input" name="ays_quiz_all_results_columns[<?php echo $val; ?>]" <?php echo $checked; ?>/>
                                                            <label for="ays_show_quiz_result<?php echo $val; ?>">
                                                                <?php echo $default_quiz_all_results_column_names_label; ?>
                                                            </label>
                                                        </li>
                                                        <?php
                                                    }
                                                 ?>
                                            </ul>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset> <!-- Single Quiz Results Settings -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Display Quiz Bank(questions)',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_display_questions">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Paste the shortcode into any of your posts to show questions of a given quiz. Designed to show questions to students, earlier on, for preparing for the test.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_display_questions" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_display_questions by="quiz/category" id="N" orderby="ASC"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_enable_question_answers">
                                            <?php echo __( "Enable question answers", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, the answers of the questions will be displayed in a list on the Front-end.',$this->plugin_name);?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_enable_question_answers" name="ays_quiz_enable_question_answers" value="on" <?php echo $quiz_enable_question_answers ? 'checked' : ''; ?> />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul">
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%s By %s', $this->plugin_name ) . ' - ' . __( 'Choose the method of filtering. Example: by="category".', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                    <ul class='ays-quiz-general-settings-ul'>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s quiz %s', $this->plugin_name ) . ' - ' . __( 'If you set the method as Quiz, it will show all questions added in the given quiz.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s category %s', $this->plugin_name ) . ' - ' . __( 'If you set the method as Category, it will show all questions assigned to the given category.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%s ID %s', $this->plugin_name ) . ' - ' . __( 'Select the ID. Example: id="23".', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                    <ul class='ays-quiz-general-settings-ul'>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s quiz %s', $this->plugin_name ) . ' - ' . __( 'If you set the method as Quiz, please enter the ID of the given quiz.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s category %s', $this->plugin_name ) . ' - ' . __( 'If you set the method as Category, please enter the ID of the given category.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%s Orderby %s', $this->plugin_name ) . ' - ' . __( 'Choose the way of ordering the questions. Example: orderby="ASC".', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                    <ul class='ays-quiz-general-settings-ul'>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s ASC %s', $this->plugin_name ) . ' - ' . __( 'The earliest created questions will appear at top of the list. The order will be classified based on question ID (oldest to newest).', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s DESC %s', $this->plugin_name ) . ' - ' . __( 'The latest created questions will appear at top of the list. The order will be classified based on question ID (newest to oldest).', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s default %s', $this->plugin_name ) . ' - ' . __( 'The order will be classified based on the reordering you have done while adding the questions to the quiz. It will work only with the by="quiz" method. The by="category" method will show the same order as orderby="ASC".', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s random %s', $this->plugin_name ) . ' - ' . __( 'The questions will be displayed in random order every time the users refresh the page.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </blockquote>
                    </fieldset> <!-- Display Quiz Bank(questions) -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Quiz categories',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_categories">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Copy the following shortcode, configure it based on your preferences and paste it into the post/page. Put the ID of your preferred category,  choose the method of displaying (all/random) and specify the count of quizzes.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_categories" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_cat id="Your_Quiz_Category_ID" display="random" count="5" layout="list"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul">
                                <li style="padding-bottom: 5px;">
                                    <?php
                                        echo sprintf(
                                            __( '%s ID %s', $this->plugin_name ) . ' - ' . __( 'Enter the ID of the category. Example: id="23".', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%s Display %s', $this->plugin_name ) . ' - ' . __( 'Choose the method of displaying. Example: display="random" count="5".', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                    <ul class='ays-quiz-general-settings-ul'>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s All %s', $this->plugin_name ) . ' - ' . __( 'If you set the method as All, it will show all quizzes from the given category. In this case, it is not required to fill the %sCount%s attribute. You can either remove it or the system will ignore the value given to it.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>',
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s Random %s', $this->plugin_name ) . ' - ' . __( 'If you set the method as Random, please give a value to %s Count %s option too, and it will randomly display that given amount of quizzes from the given category.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>',
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%s Layout %s', $this->plugin_name ) . ' - ' . __( 'Choose the design of the layout. Example:layout=grid.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                            <ul class='ays-quiz-general-settings-ul'>
                                                <li>
                                                    <?php
                                                        echo sprintf(
                                                            __( '%s List %s', $this->plugin_name ) . ' - ' . __( 'Choose the design of the layout as list․', $this->plugin_name ),
                                                            '<b>',
                                                            '</b>'
                                                        );
                                                    ?>
                                                </li>
                                                <li>
                                                    <?php
                                                        echo sprintf(
                                                            __( '%s Grid %s', $this->plugin_name ) . ' - ' . __( 'Choose the design of the layout as grid․', $this->plugin_name ),
                                                            '<b>',
                                                            '</b>'
                                                        );
                                                    ?>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </blockquote>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_cat_title">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You need to insert Your Quiz Category ID in the shortcode. It will show the category title. If there is no quiz category available/unavailable with that particular Quiz Category ID, the shortcode will stay empty.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_cat_title" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_cat_title id="Your_Quiz_Category_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_cat_description">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You need to insert Your Quiz Category ID in the shortcode. It will show the category description. If there is no quiz category available/unavailable with that particular Quiz Category ID, the shortcode will stay empty.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_cat_description" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_cat_description id="Your_Quiz_Category_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset> <!-- Quiz categories -->
                    <hr/>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Question categories',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_question_categories_title">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You need to insert Your Quiz Question Category ID in the shortcode. It will show the category title. If there is no quiz question category available/unavailable with that particular Quiz Question Category ID, the shortcode will stay empty.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_question_categories_title" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_question_categories_title id="Your_Quiz_Question_Category_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_question_categories_description">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You need to insert Your Quiz Question Category ID in the shortcode. It will show the category description. If there is no quiz question category available/unavailable with that particular Quiz Question Category ID, the shortcode will stay empty.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_question_categories_description" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_question_categories_description id="Your_Quiz_Question_Category_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset> <!-- Question categories -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Flash Cards Settings',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                            <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                                <a href="https://www.youtube.com/watch?v=uBpzFjXyKC8" target="_blank">
                                    <?php echo __("How to create flashcards - video", $this->plugin_name); ?>
                                </a>
                            </div>
                        </div>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_flash_card">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Paste the shortcode into any of your posts/pages to create flashcards in a question-and-answer format. Each flashcard shows a question on one side and a correct answer on the other.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_flash_card" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_flash_card by="quiz/category" id="ID(s)"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_flash_card_width">
                                            <?php echo __( "Width", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of the Flash Card. It accepts only numeric values. For 100% leave it blank.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_flash_card_width" name="ays_quiz_flash_card_width" class="ays-text-input ays-quiz-flash-card-width" value='<?php echo $quiz_flash_card_width ;?>'>
                                        <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_flash_card_color">
                                            <?php echo __( "Background color", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The background color of the Flash Card.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_flash_card_color" name="ays_quiz_flash_card_color" data-alpha="true" value="<?php echo $quiz_flash_card_color; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12">
                                <div class="form-group row ays_toggle_parent">
                                    <div class="col-sm-4">
                                        <label for="ays_enable_fc_introduction">
                                            <?php echo __( "Introduction page", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Tick the checkbox to add a Start page to your Flashcards. You can customize the Start page and write your preferred texts in WP Editor.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="checkbox" class="ays-enable-fc-introduction ays_toggle_checkbox" id="ays_enable_fc_introduction" name="ays_enable_fc_introduction" <?php echo ($quiz_flash_card_enable_introduction == 'on') ? 'checked' : ''; ?>>
                                    </div>
                                    <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($quiz_flash_card_enable_introduction == 'on') ? '' : 'display_none' ; ?>">
                                    <?php
                                        $content = $quiz_flash_card_introduction;
                                        $editor_id = 'ays-fc-introduction';
                                        $settings = array(
                                            'editor_height' => $quiz_wp_editor_height,
                                            'textarea_name' => 'ays_quiz_flash_card_introduction',
                                            'editor_class' => 'ays-textarea',
                                            'media_buttons' => true
                                        );
                                        wp_editor($content, $editor_id, $settings);
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_flash_card_randomize">
                                            <?php echo __( "Randomize Flash Cards", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display the flashcard questions in random order.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="checkbox" id="ays_quiz_flash_card_randomize" name="ays_quiz_flash_card_randomize" class="ays-quiz-flash-card-randomize" value='on' <?php echo $quiz_flash_card_randomize ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul">
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sBy%s', $this->plugin_name ) . ' - ' . __( 'Choose the method of filtering. Example: by="quiz"', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                    <ul class='ays-quiz-general-settings-ul'>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%squiz%s', $this->plugin_name ) . ' - ' . __( ' If you set the method as Quiz, it will show all questions added in the given quiz.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%scategory%s', $this->plugin_name ) . ' - ' . __( 'If you set the method as Category, it will show all questions assigned to the given category.
                                                    ', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sID%s', $this->plugin_name ) . ' - ' . __( 'Select a single ID or multiple IDs. List multiple IDs by separating them with commas. Example id="13,23,33"', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                            </ul>
                        </blockquote>
                    </fieldset> <!-- Flash Cards Settings -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Recent Quizzes Settings',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_recent_quizes">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" data-html="true"
                                                title="<?php
                                                    echo __('Copy the following shortcode, configure it based on your preferences and paste it into the post.',$this->plugin_name) .
                                                    "<ul style='list-style-type: circle;padding-left: 20px;'>".
                                                        "<li>". __('Random - If you set the ordering method as random and gave a value to count option, then it will randomly display that given amount of quizzes from your created quizzes.',$this->plugin_name) ."</li>".
                                                        "<li>". __('Recent - If you set the ordering method as recent and gave a value to count option, then it will display that given amount of quizzes from your recently created quizzes.',$this->plugin_name) ."</li>".
                                                    "</ul>";
                                                ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_recent_quizes" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_display_quizzes orderby="random/recent" count="5"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset> <!-- Recent Quizzes Settings -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Most popular quiz',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_most_popular">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Designed to show the most popular quiz that is passed most commonly by users.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_most_popular" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_most_popular count="1"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset> <!-- Most popular quiz -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Display the sum of the quiz points',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_display_questions">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Copy the following shortcode and paste into any post.  Insert the IDs of the Quizzes to receive the sum of the quiz points.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_points_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_points_count id="Your_Quiz_ID(s)" mode="all/best"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul">
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sID%s', $this->plugin_name ) . ' - ' . __( 'Select the ID of the quiz. You can write more than one ID.', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                </li>
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( '%sMode%s', $this->plugin_name ) . ' - ' . __( 'Choose the way to sum the points. Example: mode="all".', $this->plugin_name ),
                                            '<b>',
                                            '</b>'
                                        );
                                    ?>
                                    <ul class='ays-quiz-general-settings-ul'>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%sAll%s', $this->plugin_name ) . ' - ' . __( "It will display the sum of all the user's points.", $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                        <li>
                                            <?php
                                                echo sprintf(
                                                    __( '%sBest%s', $this->plugin_name ) . ' - ' . __( ' It will display the sum of all the maximum points of the user.', $this->plugin_name ),
                                                    '<b>',
                                                    '</b>'
                                                );
                                            ?>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </blockquote>
                    </fieldset> <!-- Display the sum of the quiz points -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Show Quiz Orders',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_all_results">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and insert it into any post or page and display the Quiz Name, Payment Date, Amount and Type',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_all_results" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_paid_quizzes]'>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label>
                                            <?php echo __( "Table columns", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can sort table columns and select which columns must display on the front-end.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                        <div class="ays-show-user-page-table-wrap">
                                            <ul class="ays-show-user-page-table">
                                                <?php
                                                    foreach ($quiz_all_orders_columns_order as $key => $val) {
                                                        $checked = '';
                                                        if(isset($quiz_all_orders_columns[$val]) && $quiz_all_orders_columns[$val] != ''){
                                                            $checked = 'checked';
                                                        }

                                                        $default_all_orders_column_names_label = '';
                                                        if( isset( $default_all_orders_columns_names[$val] ) && $default_all_orders_columns_names[$val] != '' ){
                                                            $default_all_orders_column_names_label = $default_all_orders_columns_names[$val];
                                                        }

                                                        if( $default_all_orders_column_names_label == '' ){
                                                            continue;
                                                        }

                                                        ?>
                                                        <li class="ays-user-page-option-row ui-state-default">
                                                            <input type="hidden" value="<?php echo $val; ?>" name="ays_quiz_all_orders_columns_order[]"/>
                                                            <input type="checkbox" id="ays_show_order<?php echo $val; ?>" value="<?php echo $val; ?>" class="ays-checkbox-input" name="ays_quiz_all_orders_columns[<?php echo $val; ?>]" <?php echo $checked; ?>/>
                                                            <label for="ays_show_order<?php echo $val; ?>">
                                                                <?php echo $default_all_orders_column_names_label; ?>
                                                            </label>
                                                        </li>
                                                        <?php
                                                    }
                                                 ?>
                                            </ul>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset> <!-- Show Quiz Orders -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Quiz multilanguage',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_all_results">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Write your desired text in any WordPress language. It will be translated in the front-end. The languages must be included in the ISO 639-1 Code column.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_multilanugage_shortcode" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[:en]Hello[:es]Hola[:]'>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <blockquote>
                            <ul class="ays-quiz-general-settings-blockquote-ul">
                                <li>
                                    <?php
                                        echo sprintf(
                                            __( "In this shortcode you can add your desired text and its translation. The translated version of the text will be displayed in the front-end. The languages must be written in the %sLanguage Code%s", $this->plugin_name ),
                                            '<a href="https://www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank">',
                                            '</a>'
                                        );
                                    ?>
                                </li>
                            </ul>
                        </blockquote>
                    </fieldset> <!-- Quiz multilanguage -->
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Quiz intervals chart',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_interval_chart">
                                            <?php echo __( "Shortcode", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("You can copy the shortcode and paste it into your desired page/post to display a chart based on keywords on the Front-end. Don't forget to change YOUR_QUIZ_ID with the corresponding Quiz ID.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_interval_chart" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_interval_chart id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset> <!-- Quiz intervals chart -->
                    <hr>
                    <?php
                        if(has_action('ays_qm_settings_page_extra_shortcodes')){
                            do_action( 'ays_qm_settings_page_extra_shortcodes' );
                        }
                    ?>
                    <hr>
                    <?php
                        if(has_action('ays_qm_advanced_user_dashboard')){
                            do_action( 'ays_qm_advanced_user_dashboard' );
                        }
                    ?>
                </div>
                <div id="tab4" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab4') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle">
                        <?php echo __('Message variables',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" data-html="true" title="<p style='margin-bottom:3px;'><?php echo __( 'You can copy these variables and paste them in the following options from the quiz settings', $this->plugin_name ); ?>:</p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Result message', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Quiz pass message', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Quiz fail message', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Mail Message', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Certificate title', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Certificate body', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Interval message', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Email configuration', $this->plugin_name ); ?></p>
                            <p style='text-indent:30px;padding-left:10px;margin:0;'>* <?php echo __( 'From Name', $this->plugin_name ); ?></p>
                            <p style='text-indent:30px;padding-left:10px;margin:0;'>* <?php echo __( 'Subject', $this->plugin_name ); ?></p>
                            <p style='text-indent:30px;padding-left:10px;margin:0;'>* <?php echo __( 'Reply To Name', $this->plugin_name ); ?></p>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                    <blockquote>
                        <p><?php echo __( "You can copy these variables and paste them in the following options from the quiz settings", $this->plugin_name ); ?>:</p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Result message", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Quiz pass message", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Quiz fail message", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Mail Message", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Certificate title", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Certificate body", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Interval message", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Email configuration", $this->plugin_name ); ?></p>
                        <p style="text-indent:30px;margin:0;">* <?php echo __( "From Name", $this->plugin_name ); ?></p>
                        <p style="text-indent:30px;margin:0;">* <?php echo __( "Subject", $this->plugin_name ); ?></p>
                        <p style="text-indent:30px;margin:0;">* <?php echo __( "Reply To Name", $this->plugin_name ); ?></p>
                    </blockquote>
                    <hr>
                    <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                            <a href="https://www.youtube.com/watch?v=nzQEHzmUBc8" target="_blank">
                                <?php echo __("How message variables works - video", $this->plugin_name); ?>
                            </a>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_name%%"/>
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The name the user entered into information form", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_email%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The E-mail the user entered into information form", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_phone%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The phone the user entered into information form", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%quiz_name%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The title of the quiz", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%score%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The score of quiz which got the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_points%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The points of quiz which got the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_corrects_count%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The number of correct answers of the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%questions_count%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The number of questions that the user must pass.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%max_points%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "Maximum points which can get the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%current_date%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The date of the passing quiz", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%quiz_logo%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The quiz image which used for quiz start page", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%interval_message%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The message which must display on the result page depending from score", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%avg_score%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The average score of the quiz of all time", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%avg_rate%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The average rate of the quiz of all time", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_pass_time%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The time which spent that the user passed the quiz", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%quiz_time%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The time which must spend the user to the quiz", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%results_by_cats%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The score of the quiz by a question categories which got the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%results_by_tags%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The score the user got for the quiz by question tags.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%unique_code%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "You can use this unique code as an identifier. It is unique for every attempt.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%download_certificate%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "You can use this variable to allow users to download their certificate after quiz completion.", $this->plugin_name); ?> <?php echo __( "Note: You can easily translate the button text ('Download your certificate') of the message variable by using the Loco Translate plugin/Poedit app or any other Translation plugin.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%wrong_answers_count%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The number of wrong answers of the user.", $this->plugin_name) ." ". __( "(skipped questions are included)", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%only_wrong_answers_count%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The number of only wrong answers of the user.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%avg_score_by_category%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The average score by the question category of the given quiz of the given user.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%skipped_questions_count%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The count of unanswered questions of the user.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%answered_questions_count%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The count of answered questions of the user.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%score_by_answered_questions%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The score of those questions which the given user answered(%). Skipped or unanswered questions will not be included in the calculation.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <!-- ---- -->
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_first_name%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The user's first name that was filled in their WordPress site during registration.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_last_name%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The user's last name that was filled in their WordPress site during registration.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_nickname%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The user's nickname that was filled in their WordPress profile.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_display_name%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The user's display name that was filled in their WordPress profile.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%keyword_count_{keyword}%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The count of the selected keyword that the user answers during the quiz. For instance, %%keyword_count_A%%.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%keyword_percentage_{keyword}%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The percentage of the selected keyword that the user answers during the quiz. For instance, %%keyword_percentage_A%%.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%top_keywords_count_{count}%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "Top keywords of answers selected by the user during the quiz. Each keyword will be displayed with the count of selected keywords. For instance, %%top_keywords_count_3%%.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%top_keywords_percentage_{count}%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "Top keywords of answers selected by the user during the quiz. Each keyword will be displayed with the percentage of selected keywords. For instance, %%top_keywords_percentage_3%%.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%quiz_coupon%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "You can use this message variable for showing coupons to your users. This message variable won't work unless you enable the Enable quiz coupons option.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_wordpress_email%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The user's email that was filled in their WordPress profile.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_wordpress_roles%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The user's role(s) when logged-in. In case the user is not logged-in, the field will be empty.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_wordpress_website%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The user's website that was filled in their WordPress profile.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%quiz_creation_date%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The exact date/time of the quiz creation.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%current_quiz_author%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "It will show the author of the current quiz.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%current_quiz_page_link%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "Prints the webpage link where the current quiz is posted.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%current_user_ip%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo esc_attr( __( "Shows the current user's IP no matter whether they are a logged-in user or a guest. Please note, that this message variable will return empty, if 'Do not store IP addresses' is ticked from General Settings>General>Users IP addresses.", $this->plugin_name) ); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%current_quiz_author_email%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo esc_attr( __( "Shows the current quiz author's email that was filled in their WordPress profile.", $this->plugin_name) ); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%admin_email%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo esc_attr( __( "Shows the admin's email that was filled in their WordPress profile.", $this->plugin_name) ); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_keyword_point_{keyword}%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "It will display the total count of the keyword. For instance, %%user_keyword_point_A%%.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%max_point_keyword_{keyword}%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "It displays the maximum point of the keywords. For instance, %%max_point_keyword_A%%.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_keyword_percentage_{keyword}%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "It will display the percentage of the chosen keyword from the maximum.For instance, %%user_keyword_percentage_A%%.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%avg_user_points%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "It will display the average score of the user.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%avg_res_by_cats%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "It will display the average rate of the Question Category (for example: Copywriting: 2.7/5 ...).", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%personality_result_by_question_ids_{CatID_1,CatID_2,CatID_3,CatID_4}%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "It will display the Question Category title and the description. Moreover, it displays in which percentage you match the particular Question Category Keyword. The message variable is designed to create Myers Personality Test. For instance, %%personality_result_by_question_ids_3,5,16,2%%.", $this->plugin_name); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="tab5" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab5') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle">
                        <?php echo __('Buttons texts',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" data-html="true" title="<p style='margin-bottom:3px;'><?php echo __( 'If you make a change here, these words will not be translated!', $this->plugin_name ); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                    <blockquote>
                        <p>
                            <?php echo __( "You can change the buttons' texts and write the words you prefer for them.", $this->plugin_name ); ?>
                            <span class="ays-quiz-blockquote-span"><?php echo __( "Please note, that if you change the default texts, these words will not be translated with Translation plugins or the Poedit app.", $this->plugin_name ); ?></span>
                        </p>
                    </blockquote>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_start_button">
                                <?php echo __( "Start button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_start_button" name="ays_start_button" class="ays-text-input ays-text-input-short"  value='<?php echo $start_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_next_button">
                                <?php echo __( "Next button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_next_button" name="ays_next_button" class="ays-text-input ays-text-input-short"  value='<?php echo $next_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_previous_button">
                                <?php echo __( "Previous button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_previous_button" name="ays_previous_button" class="ays-text-input ays-text-input-short"  value='<?php echo $previous_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_clear_button">
                                <?php echo __( "Clear button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_clear_button" name="ays_clear_button" class="ays-text-input ays-text-input-short"  value='<?php echo $clear_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_finish_button">
                                <?php echo __( "Finish button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_finish_button" name="ays_finish_button" class="ays-text-input ays-text-input-short"  value='<?php echo $finish_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_see_result_button">
                                <?php echo __( "See Result button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_see_result_button" name="ays_see_result_button" class="ays-text-input ays-text-input-short"  value='<?php echo $see_result_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_restart_quiz_button">
                                <?php echo __( "Restart quiz button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_restart_quiz_button" name="ays_restart_quiz_button" class="ays-text-input ays-text-input-short"  value='<?php echo $restart_quiz_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_send_feedback_button">
                                <?php echo __( "Send feedback button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_send_feedback_button" name="ays_send_feedback_button" class="ays-text-input ays-text-input-short"  value='<?php echo $send_feedback_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_load_more_button">
                                <?php echo __( "Load more button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_load_more_button" name="ays_load_more_button" class="ays-text-input ays-text-input-short"  value='<?php echo $load_more_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_exit_button">
                                <?php echo __( "Exit button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_exit_button" name="ays_exit_button" class="ays-text-input ays-text-input-short"  value='<?php echo $exit_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_check_button">
                                <?php echo __( "Check button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_check_button" name="ays_check_button" class="ays-text-input ays-text-input-short"  value='<?php echo $check_button; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_login_button">
                                <?php echo __( "Log In button", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" id="ays_login_button" name="ays_login_button" class="ays-text-input ays-text-input-short"  value='<?php echo $login_button; ?>'>
                        </div>
                    </div>
                </div>
                <div id="tab6" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab6') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle">
                        <?php echo __('Fields texts',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" data-html="true" title="<p style='margin-bottom:3px;'><?php echo __( 'If you make a change here, these words will not be translated either.', $this->plugin_name ); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                    <blockquote>
                        <p>
                            <?php echo __( "With the help of this section, you can change the fields' placeholders and labels of the Information form. Find the available fields in the User data tab of your quizzes.", $this->plugin_name ); ?>
                            <span class="ays-quiz-blockquote-span"><?php echo __( "Please note, that if you change the default texts, these words will not be translated with Translation plugins or the Poedit app.", $this->plugin_name ); ?></span>
                        </p>

                    </blockquote>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-4">
                           <span><?php echo __( "Placeholders", $this->plugin_name ); ?></span>
                        </div>
                        <div class="col-sm-4">
                            <span><?php echo __( "Labels", $this->plugin_name ); ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="ays_quiz_fields_placeholder_name">
                                <?php echo __( "Name", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" id="ays_quiz_fields_placeholder_name" name="ays_quiz_fields_placeholder_name" class="ays-text-input ays-text-input-short"  value='<?php echo $quiz_fields_placeholder_name; ?>'>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" id="ays_quiz_fields_label_name" name="ays_quiz_fields_label_name" class="ays-text-input ays-text-input-short"  value='<?php echo $quiz_fields_label_name; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="ays_quiz_fields_placeholder_eamil">
                                <?php echo __( "Email", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" id="ays_quiz_fields_placeholder_eamil" name="ays_quiz_fields_placeholder_eamil" class="ays-text-input ays-text-input-short"  value='<?php echo $quiz_fields_placeholder_eamil; ?>'>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" id="ays_quiz_fields_label_eamil" name="ays_quiz_fields_label_eamil" class="ays-text-input ays-text-input-short"  value='<?php echo $quiz_fields_label_eamil; ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="ays_quiz_fields_placeholder_phone">
                                <?php echo __( "Phone", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" id="ays_quiz_fields_placeholder_phone" name="ays_quiz_fields_placeholder_phone" class="ays-text-input ays-text-input-short"  value='<?php echo $quiz_fields_placeholder_phone; ?>'>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" id="ays_quiz_fields_label_phone" name="ays_quiz_fields_label_phone" class="ays-text-input ays-text-input-short"  value='<?php echo $quiz_fields_label_phone; ?>'>
                        </div>
                    </div>
                </div>
                <div id="tab7" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab7') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle"><?php echo __('Shortcodes',$this->plugin_name)?></p>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Extra shortcodes',$this->plugin_name); ?></h5>
                        </legend>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_avg_score">
                                            <?php echo __( "Average score", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Copy the given shortcode and paste it in posts. Insert the Quiz ID  to see the average score of participants of that quiz.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_avg_score" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_avg_score id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_passed_users_count">
                                            <?php echo __( "Passed users count", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Copy the following shortcode and paste it in posts. Insert the Quiz ID to receive the number of participants of the quiz.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_passed_users_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_passed_users_count id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_passed_users_count_by_score">
                                            <?php echo __( "Passed users count by score", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Copy the following shortcode and paste it into posts. Insert the Quiz ID to receive the number of passed users of the quiz. The pass score has to be determined in the Quiz Settings.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_passed_users_count_by_score" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_passed_users_count_by_score id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_failed_users_count_by_score">
                                            <?php echo __( "Failed users count by score", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Copy the following shortcode and paste it into posts. Insert the Quiz ID to receive the number of failed users of the quiz. The pass score has to be determined in the Quiz Settings.',$this->plugin_name); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_failed_users_count_by_score" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_failed_users_count_by_score id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_unread_results_count">
                                            <?php echo __( "Show quiz unread results count", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("You need to insert Your Quiz ID in the shortcode. It will show the unread results count of the particular quiz. If there is no quiz available/found with that particular Quiz ID, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_unread_results_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_unread_results_count id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_read_results_count">
                                            <?php echo __( "Show quiz read results count", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("You need to insert Your Quiz ID in the shortcode. It will show the read results count of the particular quiz. If there is no quiz available/found with that particular Quiz ID, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_read_results_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_read_results_count id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_passed_quizzes_count">
                                            <?php echo __( "Quiz Takers count", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the number of passed quizzes of the current user. For instance, the current user has passed 20 quizzes. If the user is not logged in shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_passed_quizzes_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_passed_quizzes_count]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_all_passed_quizzes_count">
                                            <?php echo __( "Passed users count in total", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the total sum of how many times the particular user has passed all the quizzes. For instance, the current user has passed 20 quizzes 500 times in total. If the user is not logged in shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_all_passed_quizzes_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_all_passed_quizzes_count]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_first_name">
                                            <?php echo __( "Show User First Name", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the logged-in user's First Name. If the user is not logged-in, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_first_name" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_first_name]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_last_name">
                                            <?php echo __( "Show User Last Name", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the logged-in user's Last Name. If the user is not logged-in, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_last_name" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_last_name]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_nickname">
                                            <?php echo __( "Show User Nickname", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the logged-in user's Nickname. If the user is not logged-in, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_nickname" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_nickname]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_display_name">
                                            <?php echo __( "Show User Display name", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the logged-in user's Display name. If the user is not logged-in, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_display_name" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_display_name]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_email">
                                            <?php echo __( "Show User Email", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the logged-in user's Email. If the user is not logged-in, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_email" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_email]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_roles">
                                            <?php echo __( "Show user roles", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the logged-in user's role(s). If the user is not logged-in, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_roles" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_roles]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_website">
                                            <?php echo __( "Show user website", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Shows the logged-in user's website. If the user is not logged-in, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_website" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_website]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_user_duration">
                                            <?php echo __( "Show user quiz duration", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Put this shortcode on a page to show the total time the user spent to pass quizzes. It includes all the quizzes in the user history.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_user_duration" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_user_duration]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_creation_date">
                                            <?php echo __( "Show quiz creation date", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("You need to insert Your Quiz ID in the shortcode. It will show the creation date of the particular quiz. If there is no quiz available/found with that particular Quiz ID, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_creation_date" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_creation_date id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_current_author">
                                            <?php echo __( "Show current quiz author", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("You need to insert Your Quiz ID in the shortcode. It will show the current author of the particular quiz. If there is no quiz or questions available/found with that particular Quiz ID, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_current_author" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_current_author id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_questions_count">
                                            <?php echo __( "Show quiz questions count", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("You need to insert Your Quiz ID in the shortcode. It will show the questions count of the particular quiz. If there is no quiz available/found with that particular Quiz ID, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_questions_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_questions_count id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_category_title">
                                            <?php echo __( "Show quiz category title", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("You need to insert Your Quiz ID in the shortcode. It will show the cateogry title of the particular quiz. If there is no quiz available/found with that particular Quiz ID, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_category_title" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_category_title id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_category_description">
                                            <?php echo __( "Show quiz category description", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("You need to insert Your Quiz ID in the shortcode. It will show the cateogry description of the particular quiz. If there is no quiz available/found with that particular Quiz ID, the shortcode will be empty.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_category_description" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_category_description id="Your_Quiz_ID"]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_quizzes_count">
                                            <?php echo __( "Show quizzes count", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Put this shortcode on a page to show the total count of quizzes.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_quizzes_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_quizzes_count]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row" style="padding:0px;margin:0;">
                            <div class="col-sm-12" style="padding:20px;">
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label for="ays_quiz_categories_count">
                                            <?php echo __( "Show quiz categories count", $this->plugin_name ); ?>
                                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Put this shortcode on a page to show the total count of quiz categories.",$this->plugin_name) ); ?>">
                                                <i class="ays_fa ays_fa_info_circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ays_quiz_categories_count" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_categories_count]'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset> <!-- Extra shortcodes -->
                </div>
                <div id="tab8" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab8') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle">
                        <?php echo __('User Information',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose what user information you want to be displayed. Tick on the needed options for making them visible in the detailed result. Note that the information will be available in the exported version. So, even if the option is hided, it will be displayed in the exported PDF and XLSX files.', $this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_user_ip">
                                <?php echo __( "Show User IP", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_user_ip" name="ays_quiz_show_result_info_user_ip" value="on" <?php echo $ays_quiz_show_result_info_user_ip ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_user_id">
                                <?php echo __( "Show User ID", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_user_id" name="ays_quiz_show_result_info_user_id" value="on" <?php echo $ays_quiz_show_result_info_user_id ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_user">
                                <?php echo __( "Show User", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_user" name="ays_quiz_show_result_info_user" value="on" <?php echo $ays_quiz_show_result_info_user ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_admin_note">
                                <?php echo __( "Show Admin note", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_admin_note" name="ays_quiz_show_result_info_admin_note" value="on" <?php echo $ays_quiz_show_result_info_admin_note ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <hr>
                    <p class="ays-subtitle">
                        <?php echo __('Quiz Information',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose what Quiz information you want to be displayed. Tick on the needed options for making them visible in the detailed result. Note that the information will be available in the exported version. So, even if the option is hided, it will be displayed in the exported PDF and XLSX files.', $this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_start_date">
                                <?php echo __( "Show Start date", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_start_date" name="ays_quiz_show_result_info_start_date" value="on" <?php echo $ays_quiz_show_result_info_start_date ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_duration">
                                <?php echo __( "Show Duration", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_duration" name="ays_quiz_show_result_info_duration" value="on" <?php echo $ays_quiz_show_result_info_duration ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_score">
                                <?php echo __( "Show Score/Points", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_score" name="ays_quiz_show_result_info_score" value="on" <?php echo $ays_quiz_show_result_info_score ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_rate">
                                <?php echo __( "Show Rate", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_rate" name="ays_quiz_show_result_info_rate" value="on" <?php echo $ays_quiz_show_result_info_rate ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_unique_code">
                                <?php echo __( "Show Unique Code", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_unique_code" name="ays_quiz_show_result_info_unique_code" value="on" <?php echo $ays_quiz_show_result_info_unique_code ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_keywords">
                                <?php echo __( "Show Keywords", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_keywords" name="ays_quiz_show_result_info_keywords" value="on" <?php echo $ays_quiz_show_result_info_keywords ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_res_by_cats">
                                <?php echo __( "Show Results by Categories", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_res_by_cats" name="ays_quiz_show_result_info_res_by_cats" value="on" <?php echo $ays_quiz_show_result_info_res_by_cats ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_coupon">
                                <?php echo __( "Show Coupon", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_coupon" name="ays_quiz_show_result_info_coupon" value="on" <?php echo $ays_quiz_show_result_info_coupon ? 'checked' : ''; ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="ays_quiz_show_result_info_certificate">
                                <?php echo __( "Show Certificate", $this->plugin_name ); ?>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="checkbox" class="ays-checkbox-input" id="ays_quiz_show_result_info_certificate" name="ays_quiz_show_result_info_certificate" value="on" <?php echo $ays_quiz_show_result_info_certificate ? 'checked' : ''; ?> />
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <hr/>
            <div class="ays-settings-form-save-button-wrap">
            <?php
                wp_nonce_field('settings_action', 'settings_action');
                $other_attributes = array(
                    'title' => __('Ctrl + s', $this->plugin_name),
                    'data-toggle' => 'tooltip',
                    'data-delay'=> '{"show":"1000"}'
                );
                submit_button(__('Save changes', $this->plugin_name), 'primary ays-quiz-loader-banner', 'ays_submit', true, $other_attributes);
                echo $loader_iamge;
            ?>
            </div>
        </form>
    </div>
</div>
