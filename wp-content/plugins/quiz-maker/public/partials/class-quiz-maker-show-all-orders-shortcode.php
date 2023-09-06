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
class Quiz_Maker_All_Orders
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


    protected $settings;

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

        add_shortcode('ays_quiz_paid_quizzes', array($this, 'ays_generate_quiz_all_orders_method'));

        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function enqueue_styles(){
        wp_enqueue_style($this->plugin_name . '-dataTable-min', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-dataTables.min.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name . '-datatable-min', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-datatable.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-all-orders-public', AYS_QUIZ_PUBLIC_URL . '/js/all-orders/all-orders-public.js', array('jquery'), $this->version, true);

        wp_localize_script( $this->plugin_name . '-datatable-min', 'quizLangDataTableObj', array(
            "sEmptyTable"           => __( "No data available in table", $this->plugin_name ),
            "sInfo"                 => __( "Showing _START_ to _END_ of _TOTAL_ entries", $this->plugin_name ),
            "sInfoEmpty"            => __( "Showing 0 to 0 of 0 entries", $this->plugin_name ),
            "sInfoFiltered"         => __( "(filtered from _MAX_ total entries)", $this->plugin_name ),
            // "sInfoPostFix":          => __( "", $this->plugin_name ),
            // "sInfoThousands":        => __( ",", $this->plugin_name ),
            "sLengthMenu"           => __( "Show _MENU_ entries", $this->plugin_name ),
            "sLoadingRecords"       => __( "Loading...", $this->plugin_name ),
            "sProcessing"           => __( "Processing...", $this->plugin_name ),
            "sSearch"               => __( "Search:", $this->plugin_name ),
            // "sUrl":                  => __( "", $this->plugin_name ),
            "sZeroRecords"          => __( "No matching records found", $this->plugin_name ),
            "sFirst"                => __( "First", $this->plugin_name ),
            "sLast"                 => __( "Last", $this->plugin_name ),
            "sNext"                 => __( "Next", $this->plugin_name ),
            "sPrevious"             => __( "Previous", $this->plugin_name ),
            "sSortAscending"        => __( ": activate to sort column ascending", $this->plugin_name ),
            "sSortDescending"       => __( ": activate to sort column descending", $this->plugin_name ),
        ) );
    }

    public function get_user_orders_info(){
        global $wpdb;

        $current_user = wp_get_current_user();
        $id = $current_user->ID;

        $orders_table = $wpdb->prefix . "aysquiz_orders";
        $quizes_table = $wpdb->prefix . "aysquiz_quizes";
        $sql = "SELECT o.quiz_id, o.amount, q.quiz_url, o.payment_date, o.type, q.title
                FROM $orders_table AS o
                LEFT JOIN $quizes_table AS q
                ON o.quiz_id = q.id
                WHERE o.status = 'finished' AND o.user_id = {$id}
                ORDER BY o.id DESC";
        $results = $wpdb->get_results($sql, "ARRAY_A");

        return $results;

    }

    public function ays_quiz_all_orders_html(){

        $quiz_settings = $this->settings;
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $quiz_set_option = json_decode(stripcslashes($quiz_settings_options), true);

        $results = $this->get_user_orders_info();

        $default_quiz_all_orders_columns = array(
            'quiz_name'       => 'quiz_name',
            'payment_date'    => 'payment_date',
            'amount'          => 'amount',
            'type'            => 'type'
        );

        $default_quiz_all_orders_column_names = array(
            "quiz_name"       => __( "Quiz Name", $this->plugin_name ),
            "payment_date"    => __( "Payment Date", $this->plugin_name ),
            "amount"          => __( "Amount", $this->plugin_name ),
            "type"            => __( "Type", $this->plugin_name) ,
        );

        $quiz_all_orders_columns = (isset( $quiz_set_option['quiz_all_orders_columns'] ) && !empty($quiz_set_option['quiz_all_orders_columns']) ) ? $quiz_set_option['quiz_all_orders_columns'] : $default_quiz_all_orders_columns;
        $quiz_all_orders_columns_order = (isset( $quiz_set_option['quiz_all_orders_columns_order'] ) && !empty($quiz_set_option['quiz_all_orders_columns_order']) ) ? $quiz_set_option['quiz_all_orders_columns_order'] : $default_quiz_all_orders_columns;

        $ays_default_header_value = array(
            "quiz_name"       => "<th style='width:20%;'>" . __( "Quiz Name", $this->plugin_name ) . "</th>",
            "payment_date"    => "<th style='width:17%;' class='ays-quiz-all-orders-end-date-column'>" . __( "Payment Date", $this->plugin_name ) . "</th>",
            "amount"          => "<th style='width:13%;'>" . __( "Amount", $this->plugin_name ) . "</th>",
            "type"            => "<th style='width:13%;'>" . __( "Type", $this->plugin_name ) . "</th>"
        );

        if($results === null){
            $all_orders_html = "<p style='text-align: center;font-style:italic;'>" . __( "Only the logged-in users will be able to see the orders.", $this->plugin_name ) . "</p>";
            return $all_orders_html;
        }

        if( empty( $results ) ){
            $all_orders_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no orders yet.", $this->plugin_name ) . "</p>";
            return $all_orders_html;
        }

        $all_orders_html = "<div class='ays-quiz-all-orders-container'>
        <table id='ays-quiz-all-orders-page' class='ays-quiz-all-orders-page'>
        <thead>
        <tr>";

        foreach ($quiz_all_orders_columns_order as $key => $value) {
            if (isset($quiz_all_orders_columns[$value]) && isset( $ays_default_header_value[$value] )) {
                $all_orders_html .= $ays_default_header_value[$value];
            }
        }

        $all_orders_html .= "</tr></thead>";


        foreach($results as $key => $result){

            $payment_date = date_i18n('d M Y H:i:s', strtotime( $result['payment_date'] ) );
            $amount       = isset($result['amount']) ? stripslashes( esc_attr($result['amount']) ) : 0;
            $type         = isset($result['type']) ? stripslashes( esc_attr($result['type']) ) : '';
            $quiz_name    = isset($result['title']) ? stripslashes( esc_attr($result['title']) ) : '';
            $quiz_url     = isset($result['quiz_url']) ? esc_url($result['quiz_url']) : '';

            $payment_date_for_ordering = strtotime($result['payment_date']);

            $quiz_name_html = $quiz_name;
            if ( $quiz_url != "" || !empty( $quiz_url ) ) {
                $quiz_name_html = "<a class='ays-quiz-all-orders-title-href' href='".$quiz_url."' target='blank'>$quiz_name</a>";
            }

            $ays_default_html_order = array(
                "quiz_name"     => "<td data-title='". $default_quiz_all_orders_column_names['quiz_name'] ."'>". $quiz_name_html ."</td>",
                "payment_date"  => "<td data-title='". $default_quiz_all_orders_column_names['payment_date'] ."'data-order='". $payment_date_for_ordering ."'>". $payment_date ."</td>",
                "amount"        => "<td data-title='". $default_quiz_all_orders_column_names['amount'] ."'>". $amount ."</td>",
                "type"          => "<td data-title='". $default_quiz_all_orders_column_names['type'] ."'>". $type ."</td>",
            );

            $attribute_options = (isset($result['options']) && $result['options'] != '') ? json_decode( $result['options'], true ) : '';

            $all_orders_html .= "<tr>";
            foreach ($quiz_all_orders_columns_order as $key => $value) {
                if (isset($quiz_all_orders_columns[$value]) && isset( $ays_default_html_order[$value] )) {
                    $all_orders_html .= $ays_default_html_order[$value];
                }
            }
            $all_orders_html .= "</tr>";
        }

        $all_orders_html .= "</table>
            </div>";

        return $all_orders_html;
    }

    public function ays_generate_quiz_all_orders_method() {

        if (!is_user_logged_in()) {
            $quiz_all_orders_html = "<p style='text-align: center;font-style:italic;'>" . __( "You must log in to see your results.", $this->plugin_name ) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), '\n', $quiz_all_orders_html);
        }

        $this->enqueue_styles();
        $this->enqueue_scripts();
        $quiz_all_orders_html = $this->ays_quiz_all_orders_html();
        $quiz_all_orders_html = Quiz_Maker_Data::ays_quiz_translate_content( $quiz_all_orders_html );

        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_all_orders_html);
    }

}
