<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/public
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Ays_Quiz_Maker_Intervals_Chart_Shortcodes_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    protected $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private $html_class_prefix = 'ays-quiz-intervals-chart-shortcodes-';
    private $html_name_prefix = 'ays-quiz-';
    private $name_prefix = 'ays_quiz_';
    private $unique_id;
    private $unique_id_in_class;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version){

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_shortcode('ays_quiz_interval_chart', array($this, 'ays_generate_most_popular_method'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name.'-apm-charts-core', AYS_QUIZ_ADMIN_URL . '/js/core.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name.'-apm-charts-main', AYS_QUIZ_ADMIN_URL . '/js/charts.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name.'-apm-charts-animated', AYS_QUIZ_ADMIN_URL . '/js/animated.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name.'-chart1', AYS_QUIZ_PUBLIC_URL . '/js/partials/quiz-maker-charts.js', array('jquery'), $this->version, true);

        wp_localize_script( $this->plugin_name.'-chart1', 'AysQuizQuestionChartObj', array(
            'completes'         => __( "Completes", $this->plugin_name ),
            'count'             => __( "Count", $this->plugin_name ),
            'interval'          => __( "Interval", $this->plugin_name ),
            'users'             => __( "Users", $this->plugin_name ),
            'category'          => __( "Category", $this->plugin_name ),
            'percent'           => __( "Percent", $this->plugin_name ),
            'users2'            => __( "users", $this->plugin_name ),
            'guest'             => __( "Guest", $this->plugin_name ),
            'loggedInUsers'     => __( "Logged in user", $this->plugin_name ),
            'keyword'           => __( "Keyword", $this->plugin_name ),
        ) );
    }

    /*
    ==========================================
        Intervals chart | Start
    ==========================================
    */

    public function ays_generate_most_popular_method( $attr ){

        $id = (isset($attr['id'])) ? absint(sanitize_text_field($attr['id'])) : null;

        if (is_null($id)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $id . "-" . $unique_id;

        $this->enqueue_scripts();

        $quiz_intervals_chart_html = $this->ays_quiz_intervals_chart_html( $id, $attr );
        $quiz_intervals_chart_html = Quiz_Maker_Data::ays_quiz_translate_content( $quiz_intervals_chart_html );
        
        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_intervals_chart_html);
    }

    public function ays_quiz_intervals_chart_html( $id, $attr ){

        $content_html = array();

        $keyword_data = Quiz_Each_Results_List_Table::ays_quiz_get_users_keywords_statistics( $id );

        if( empty( $keyword_data['keywords'] ) || empty( $keyword_data['keyword_percentage'] ) ){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $content_html[] = "<div class='ays-quiz-intervals-chart-container' id='ays-quiz-intervals-chart-container-". $this->unique_id_in_class ."'>";

        $content_html[] = '
        <div class="ays-quiz-intervals-chart-box">
           <div class="ays-quiz-intervals-chart-title" style="text-align:center;">'. __("Keyword statistics", $this->plugin_name) .'</div>
           <div id="chart_div_'. $id .'" class="ays_quiz_chart_div" data-id="'. $id .'"></div>
        </div>
        <script>
            if(typeof window.aysQuizIntervalsChartData === "undefined"){
                window.aysQuizIntervalsChartData = [];
            }
            window.aysQuizIntervalsChartData["'.$id.'"]  = "' . base64_encode(json_encode( $keyword_data )) . '";
        </script>
        <style>
        #ays-quiz-intervals-chart-container-'. $this->unique_id_in_class .' {
            margin: 20px auto;  
        }

        #ays-quiz-intervals-chart-container-'. $this->unique_id_in_class .' .ays_quiz_chart_div {
            width: 100%;
            height: 500px;
        }

        #ays-quiz-intervals-chart-container-'. $this->unique_id_in_class .' .ays-quiz-intervals-chart-title {
            text-align: center;
            font-size: 23px;
            font-weight: 400;
            margin: 0;
            padding: 0px 0 4px;
            line-height: 1.3;
        }

        #ays-quiz-intervals-chart-container-'. $this->unique_id_in_class .' svg > g > g:nth-child(2) > g:nth-child(2) > g > g:nth-child(3) {
            display: none !important;
        }

        </style>
        ';
        $content_html[] = "</div>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
       Intervals chart | End
    ==========================================
    */

}
