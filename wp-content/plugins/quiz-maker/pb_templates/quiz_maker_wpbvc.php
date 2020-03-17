<?php
/*
Element Description: VC Quiz Maker
*/
if( class_exists( 'WPBakeryShortCode' ) ) {
    // Element Class
    class vcQuizMaker extends WPBakeryShortCode {

        function __construct() {
            add_action( 'init', array( $this, 'vc_quizmaker_mapping' ) );
            add_shortcode( 'vc_quizmaker', array( $this, 'vc_quizmaker_html' ) );
        }

        public function vc_quizmaker_mapping() {
            // Stop all if VC is not enabled
            if ( !defined( 'WPB_VC_VERSION' ) ) {
                return;
            }

            // Map the block with vc_map()
            vc_map(
                array(
                    'name' => __('Quiz Maker', 'text-domain'),
                    'base' => 'vc_quizmaker',
                    'description' => __('The Best Quiz Maker Ever', 'text-domain'),
                    'category' => __('Quiz Maker by AYS', 'text-domain'),
                    'icon' => AYS_QUIZ_ADMIN_URL . '/images/icons/icon-128x128.png',
                    'params' => array(
                        array(
                            'type' => 'dropdown',
                            'holder' => 'div',
                            'class' => 'quiz_vc_select',
                            'heading' => __( 'Quiz Maker', 'text-domain' ),
                            'param_name' => 'quiz',
                            'value' => $this->get_active_quizzes(),
                            'description' => __( 'Please select your quiz from dropdown', 'text-domain' ),
                            'admin_label' => true,
                            'group' => 'Quiz Maker'
                        )
                    )
                )
            );
        }

        public function vc_quizmaker_html( $atts ) {
            // Params extraction
            extract(
                shortcode_atts(
                    array(
                        'quiz'   => null
                    ),
                    $atts
                )
            );
            // Fill $html var with data

            // Fill $html var with data
            $html = do_shortcode("[ays_quiz id={$quiz}]");

            return $html;
        }

        public function get_active_quizzes(){
            global $wpdb;
            $quizes_table = $wpdb->prefix . 'aysquiz_quizes';
            $sql = "SELECT id,title FROM {$quizes_table} WHERE published=1;";
            $results = $wpdb->get_results( $sql, ARRAY_A );
            $options = array();
            $options['Select Quiz'] = '';
            foreach ( $results as $result ){
                $options[$result['title']] = intval( $result['id'] );
            }

            return $options;
        }
    }

    new vcQuizMaker();
}