<?php
    /**
     * Enqueue front end and editor JavaScript
     */

    function ays_quiz_gutenberg_scripts() {
        $blockPath = 'quiz-maker-block.js';
        
        wp_enqueue_script( "jquery-effects-core");
        wp_enqueue_script( 'ays_block_select2js', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array('jquery'), AYS_QUIZ_VERSION, true);
        wp_enqueue_script( AYS_QUIZ_NAME . '-rating', AYS_QUIZ_PUBLIC_URL . '/js/rating.min.js', array('jquery'), AYS_QUIZ_VERSION, true);
        wp_enqueue_script( AYS_QUIZ_NAME . '-ajax-public', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-public-ajax.js', array('jquery'), AYS_QUIZ_VERSION, true);
        wp_enqueue_script( AYS_QUIZ_NAME, AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-public.js', array('jquery'), AYS_QUIZ_VERSION, true);
        wp_localize_script( AYS_QUIZ_NAME . '-ajax-public', 'quiz_maker_ajax_public', array('ajax_url' => admin_url('admin-ajax.php')));

        // Enqueue the bundled block JS file
        wp_enqueue_script(
            'quiz-maker-block-js',
            AYS_QUIZ_BASE_URL ."/quiz/". $blockPath,
            array( 'jquery', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
            AYS_QUIZ_VERSION, true //( AYS_QUIZ_BASE_URL . 'quiz-maker-block.js' )
        );
        wp_localize_script('ays-gutenberg-block-js', 'ays_quiz_block_ajax', array('aysDoShortCode' => admin_url( 'admin-ajax.php' )));
        
        wp_enqueue_style('ays-block-font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), AYS_QUIZ_VERSION, 'all');
        wp_enqueue_style('ays-block-rating', AYS_QUIZ_PUBLIC_URL . '/css/rating.min.css', array(), AYS_QUIZ_VERSION, 'all');
        wp_enqueue_style('ays-block-animate', AYS_QUIZ_PUBLIC_URL . '/css/animate.css', array(), AYS_QUIZ_VERSION, 'all');
        wp_enqueue_style('ays-block-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', array(), AYS_QUIZ_VERSION, 'all');
        wp_enqueue_style( AYS_QUIZ_NAME, AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-public.css', array(), AYS_QUIZ_VERSION, 'all');
        
        // Enqueue the bundled block CSS file
        wp_enqueue_style(
            'quiz-maker-block-css',
            AYS_QUIZ_BASE_URL ."/quiz/quiz-maker-block.css",
            array(),
            AYS_QUIZ_VERSION, 'all'
        );
    }

    function ays_quiz_gutenberg_block_register() {
        
        global $wpdb;
        $block_name = 'quiz';
        $block_namespace = 'quiz-maker/' . $block_name;
        
        $current_user = get_current_user_id();
        $sql = "SELECT * FROM ". $wpdb->prefix . "aysquiz_quizes WHERE published = 1";
        if( ! Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ){
            $sql .= " AND author_id = ".$current_user." ";
        }
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

        return version_compare ( ( string ) $_fv, ( string ) $_sv, $operator );
    }

    function quizmaker_render_callback( $attributes ) {
        $ays_html = "<p style='text-align:center;'>" . __('Please select quiz') . "</p>";
        if(!empty($attributes["shortcode"])) {
            $ays_html = do_shortcode( $attributes["shortcode"] );
        }
        return $ays_html;
    }

    if(function_exists("register_block_type")){
        global $wp_version;

        $version1 = $wp_version;
        $operator = '>=';
        $version2 = '5.2';
        $versionCompare = ays_quiz_versionCompare($version1, $operator, $version2);

        if ( $versionCompare ) {
            // Hook scripts function into block editor hook
            add_action( 'enqueue_block_editor_assets', 'ays_quiz_gutenberg_scripts' );
            add_action( 'init', 'ays_quiz_gutenberg_block_register' );
        }
    }
