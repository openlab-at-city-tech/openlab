<?php
    /**
     * Enqueue front end and editor JavaScript
     */

    function ays_quiz_gutenberg_scripts() {
        global $current_screen;
        global $wp_version;
        $version1 = $wp_version;
        $operator = '>=';
        $version2 = '5.3';
        $versionCompare = ays_quiz_versionCompare($version1, $operator, $version2);

        if( ! $current_screen ){
            return null;
        }

        if( ! $current_screen->is_block_editor ){
            return null;
        }

        $blockPath = 'quiz-maker-block.js';
        
        wp_enqueue_script( "jquery-effects-core");
        wp_enqueue_script( AYS_QUIZ_NAME . '-block_select2js', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-select2.min.js', array('jquery'), AYS_QUIZ_VERSION, true);
        wp_enqueue_script( AYS_QUIZ_NAME . '-rating', AYS_QUIZ_PUBLIC_URL . '/js/rating.min.js', array('jquery'), AYS_QUIZ_VERSION, true);
        wp_enqueue_script( AYS_QUIZ_NAME . '-ajax-public', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-public-ajax.js', array('jquery'), AYS_QUIZ_VERSION, true);
        wp_enqueue_script( AYS_QUIZ_NAME, AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-public.js', array('jquery'), AYS_QUIZ_VERSION, true);
        wp_localize_script( AYS_QUIZ_NAME . '-ajax-public', 'quiz_maker_ajax_public', array('ajax_url' => admin_url('admin-ajax.php')));

        // Enqueue the bundled block JS file
        if ( $versionCompare ) {
            wp_enqueue_script(
                'quiz-maker-block-js',
                AYS_QUIZ_BASE_URL ."/quiz/quiz-maker-block-new.js",
                array( 'jquery', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
                AYS_QUIZ_VERSION, true //( AYS_QUIZ_BASE_URL . 'quiz-maker-block.js' )
            );
        } else {
            wp_enqueue_script(
                'quiz-maker-block-js',
                AYS_QUIZ_BASE_URL ."/quiz/". $blockPath,
                array( 'jquery', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
                AYS_QUIZ_VERSION, true //( AYS_QUIZ_BASE_URL . 'quiz-maker-block.js' )
            );
        }
        wp_localize_script('quiz-maker-block-js', 'ays_quiz_block_ajax', array(
            'aysDoShortCode' => admin_url( 'admin-ajax.php' ),
            'icons' => ays_quizmaker_get_block_icons(),
            'query_preview' => AYS_QUIZ_ADMIN_URL . 'imgages/themes/elegant_dark.JPG',
            'quiz_preview' => AYS_QUIZ_ADMIN_URL . 'imgages/themes/elegant_dark.JPG',
        ));
        
        wp_enqueue_style( AYS_QUIZ_NAME . '-block-font-awesome', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-font-awesome.min.css', array(), AYS_QUIZ_VERSION, 'all');
        wp_enqueue_style('ays-block-rating', AYS_QUIZ_PUBLIC_URL . '/css/rating.min.css', array(), AYS_QUIZ_VERSION, 'all');
        wp_enqueue_style('ays-block-animate', AYS_QUIZ_PUBLIC_URL . '/css/animate.css', array(), AYS_QUIZ_VERSION, 'all');
        wp_enqueue_style( AYS_QUIZ_NAME . '-block-select2', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-select2.min.css', array(), AYS_QUIZ_VERSION, 'all');
        wp_enqueue_style( AYS_QUIZ_NAME, AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-public.css', array(), AYS_QUIZ_VERSION, 'all');
        
        // Enqueue the bundled block CSS file
        if ( $versionCompare ) {
            wp_enqueue_style(
                'quiz-maker-block-css',
                AYS_QUIZ_BASE_URL ."/quiz/quiz-maker-block-new.css",
                array(),
                AYS_QUIZ_VERSION, 'all'
            );
        } else {
            wp_enqueue_style(
                'quiz-maker-block-css',
                AYS_QUIZ_BASE_URL ."/quiz/quiz-maker-block.css",
                array(),
                AYS_QUIZ_VERSION, 'all'
            );
        }
    }

    function ays_quiz_gutenberg_block_register() {
        
        global $wpdb;
        $block_name = 'quiz';
        $block_namespace = 'quiz-maker/' . $block_name;
        
        $current_user = get_current_user_id();
        $sql = "SELECT id, title FROM ". $wpdb->prefix . "aysquiz_quizes WHERE published = 1 ";
        if( ! Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ){
            $sql .= " AND author_id = ".$current_user." ";
        }

        $sql .= " ORDER BY id DESC ";

        $results = $wpdb->get_results($sql, "ARRAY_A");
        
        register_block_type(
            $block_namespace, 
            array(
                'render_callback'   => 'quizmaker_render_callback',                
                'editor_script'     => 'quiz-maker-block-js',  // The block script slug
                'style'             => 'quiz-maker-block-css',
                'attributes'	    => array(
                    'idner' => $results,
                    'metaFieldValue' => array(
                        'type'  => 'integer', 
                    ),
                    'shortcode' => array(
                        'type'  => 'string',				
                    ),
                    'className' => array(
                        'type'  => 'string',
                    ),
                    'openPopupId' => array(
                        'type'  => 'string',
                    ),
                ),
            )
        );
    }    

    function ays_quiz_versionCompare($version1, $operator, $version2) {

        $_fv = intval ( trim ( str_replace ( '.', '', $version1 ) ) );
        $_sv = intval ( trim ( str_replace ( '.', '', $version2 ) ) );

        if (strlen ( $_fv ) > strlen ( $_sv )) {
            $_sv = str_pad ( $_sv, strlen ( $_fv ), 0 );
        }

        if (strlen ( $_fv ) < strlen ( $_sv )) {
            $_fv = str_pad ( $_fv, strlen ( $_sv ), 0 );
        }

        return version_compare ( (string)$_fv, (string)$_sv, $operator );
    }

    function quizmaker_render_callback( $attributes ) {
        $ays_html = "<div class='ays-quiz-render-callback-box'></div>";

        if(isset($attributes["metaFieldValue"]) && $attributes["metaFieldValue"] === 0) {
            return $ays_html;
        }

        if(!empty($attributes["shortcode"])) {
            $ays_html = do_shortcode( $attributes["shortcode"] );
        }
        return $ays_html;
    }

    function quiz_maker_block_categories( $categories ) {
        $categories[] = array(
            'title' => __( 'Quiz Maker', AYS_QUIZ_NAME ),
            'slug'  => 'quiz-maker',
            'icon'  => 'quiz-maker'
        );

        return $categories;
    }


    /**
     * Quiz Maker block icons
     *
     * @since 21.7.2
     */
    function ays_quizmaker_get_block_icons() {

        $icons = array(
            'quiz_maker' =>
                '<svg width="24" height="24" viewBox="0 0 297 297" role="img" aria-hidden="true" focusable="false" style="transform: rotate(135deg);">
                    <path d="m293.98 118.57c-0.989-4.833-2.213-9.581-3.659-14.231-0.362-1.162-0.737-2.319-1.126-3.469-0.778-2.3-1.612-4.575-2.498-6.823-0.443-1.124-0.9-2.241-1.369-3.352-1.409-3.331-2.936-6.6-4.576-9.802-1.093-2.134-2.237-4.239-3.429-6.312-5.96-10.365-13.135-19.943-21.331-28.539-1.639-1.719-3.319-3.399-5.038-5.038-8.596-8.196-18.174-15.371-28.539-21.331-2.073-1.192-4.178-2.336-6.312-3.429-3.202-1.64-6.471-3.167-9.802-4.576-1.11-0.47-2.228-0.926-3.352-1.369-2.248-0.886-4.523-1.72-6.823-2.498-1.15-0.389-2.306-0.765-3.469-1.126-4.65-1.446-9.398-2.67-14.231-3.659-9.668-1.979-19.677-3.018-29.929-3.018s-20.261 1.039-29.928 3.017c-4.833 0.989-9.581 2.213-14.231 3.659-1.162 0.362-2.319 0.737-3.469 1.126-2.3 0.778-4.575 1.612-6.823 2.498-1.124 0.443-2.241 0.9-3.352 1.369-3.331 1.409-6.6 2.936-9.802 4.576-2.134 1.093-4.239 2.237-6.312 3.429-10.365 5.961-19.943 13.136-28.539 21.331-1.719 1.639-3.399 3.319-5.038 5.038-8.196 8.597-15.371 18.175-21.331 28.54-1.192 2.073-2.336 4.178-3.429 6.312-1.64 3.202-3.167 6.471-4.576 9.802-0.47 1.11-0.926 2.228-1.369 3.352-0.886 2.248-1.72 4.523-2.498 6.823-0.389 1.15-0.765 2.306-1.126 3.469-1.446 4.65-2.67 9.398-3.659 14.231-1.979 9.667-3.018 19.676-3.018 29.928 0 21.785 4.691 42.474 13.118 61.113 0.991 2.193 2.035 4.357 3.128 6.492 1.64 3.202 3.393 6.336 5.253 9.398 1.24 2.041 2.528 4.05 3.863 6.025 0.667 0.988 1.346 1.967 2.036 2.937 1.38 1.941 2.806 3.847 4.276 5.717s2.983 3.704 4.539 5.5 3.154 3.555 4.793 5.274 3.319 3.399 5.038 5.038c8.596 8.196 18.174 15.371 28.539 21.331 2.073 1.192 4.178 2.335 6.312 3.429 3.202 1.64 6.471 3.167 9.802 4.576 1.11 0.47 2.228 0.926 3.352 1.369 2.248 0.886 4.523 1.72 6.823 2.498 1.15 0.389 2.306 0.765 3.469 1.126 4.65 1.446 9.398 2.67 14.231 3.659 9.667 1.978 19.676 3.017 29.928 3.017s20.261-1.039 29.928-3.017c4.833-0.989 9.581-2.213 14.231-3.659 1.162-0.362 2.319-0.737 3.469-1.126 2.3-0.778 4.575-1.612 6.823-2.498 1.124-0.443 2.241-0.9 3.352-1.369 3.331-1.409 6.6-2.936 9.802-4.576 2.134-1.093 4.239-2.237 6.312-3.429 10.365-5.96 19.943-13.135 28.539-21.331 1.719-1.639 3.399-3.319 5.038-5.038s3.237-3.478 4.793-5.274 3.07-3.63 4.539-5.5c1.47-1.87 2.895-3.776 4.276-5.717 0.69-0.97 1.369-1.949 2.036-2.937 1.334-1.975 2.622-3.984 3.863-6.025 1.86-3.062 3.613-6.196 5.253-9.398 1.093-2.135 2.136-4.299 3.128-6.492 8.427-18.639 13.118-39.328 13.118-61.113 0-10.252-1.039-20.261-3.017-29.928zm-159.82-69.034c0-7.698 6.302-13.938 14-13.938s14 6.24 14 13.938v42.095c0 7.698-6.302 13.938-14 13.938s-14-6.24-14-13.938v-42.095zm109.32 102.7c-2.609 49.571-43.087 89.358-92.696 91.101-54.66 1.921-99.868-41.992-99.868-96.236 0-29.416 13.742-55.79 34.349-73.467 8.148-6.99 20.743-1.228 20.743 9.507 0 3.708-1.662 7.201-4.485 9.606-15.281 13.024-25.284 32.549-25.284 54.353 0 39.125 32.05 70.956 71.175 70.956s70.919-31.831 70.919-70.956c0-21.124-8.839-40.118-23.92-53.128-2.802-2.417-4.45-5.903-4.45-9.603 0-10.838 12.8-16.648 20.923-9.473 21.176 18.705 34.213 46.571 32.594 77.34z" fill="#e84c3d"></path>
                </svg>',
        );

        /**
         * Filter to register custom Quiz Maker block icons
         *
         * @since 21.7.2
         *
         * @param array $icons Icons already registered
         *
         * @return array
         */
        return apply_filters( 'ays_quizmaker_block_icons', $icons );
    }

    if(function_exists("register_block_type")){
        global $wp_version;

        $version1 = $wp_version;
        $operator = '>=';
        $version2 = '5.2';
        $version3 = '5.8';
        $versionCompare = ays_quiz_versionCompare($version1, $operator, $version2);
        $versionCompare2 = ays_quiz_versionCompare($version1, $operator, $version3);

        if ( $versionCompare ) {
            // Hook scripts function into block editor hook
            add_action( 'enqueue_block_editor_assets', 'ays_quiz_gutenberg_scripts' );
            add_action( 'init', 'ays_quiz_gutenberg_block_register' );
            if ( $versionCompare2 ) {                
                add_filter( 'block_categories_all', 'quiz_maker_block_categories', 10, 2 );
            } else {
                add_filter( 'block_categories', 'quiz_maker_block_categories', 10, 2 );
            }
        }
    }
