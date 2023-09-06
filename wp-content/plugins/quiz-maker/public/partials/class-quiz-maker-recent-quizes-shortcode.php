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
class Quiz_Maker_Recent_Quizes
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

        add_shortcode('ays_display_quizzes', array($this, 'ays_generate_display_quizes_method'));
    }

    public function recent_quiz_ids($quiz_attr){

        global $wpdb;

        $quiz_table = $wpdb->prefix.'aysquiz_quizes';

        $ays_recent_quiz_order_by = (isset($quiz_attr['orderby']) && sanitize_text_field($quiz_attr['orderby']) != '') ?sanitize_text_field($quiz_attr['orderby']) : "recent";
        $ays_recent_quiz_count = (isset($quiz_attr['count']) && intval($quiz_attr['count']) != '') ? intval($quiz_attr['count']) : "5";

        $last_quizes_sql = "SELECT id from {$quiz_table} WHERE published=1 ";

        switch ($ays_recent_quiz_order_by) {
            case 'recent':
                $last_quizes_sql .= "ORDER BY id DESC LIMIT ".$ays_recent_quiz_count;
                break;
            case 'random':
                $last_quizes_sql .= "ORDER BY RAND() LIMIT ".$ays_recent_quiz_count;
                break;
            default:
                $last_quizes_sql .= "ORDER BY id DESC LIMIT ".$ays_recent_quiz_count;
                break;
        }

        $last_quiz_ids = $wpdb->get_results($last_quizes_sql,'ARRAY_A');

        return $last_quiz_ids;
    }

    public function ays_generate_display_quizes_method($attr) {

        $recent_quiz_ids = $this->recent_quiz_ids($attr);

        $content = '<div class="ays_recent_quizes">';
        $quizzes = array();
        foreach ($recent_quiz_ids as $key => $last_quiz_id) {
            $quiz_id = (isset($last_quiz_id['id']) && intval($last_quiz_id['id']) != '') ? intval($last_quiz_id['id']) : '';
            $shortcode = '[ays_quiz id="'.$quiz_id.'"]';
            $quizzes[] = do_shortcode( $shortcode );
        }
        $content .= implode( '', $quizzes );

        $content .= '</div>';
        $content = Quiz_Maker_Data::ays_quiz_translate_content( $content );
        
        // echo $content;
        return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
    }
}
