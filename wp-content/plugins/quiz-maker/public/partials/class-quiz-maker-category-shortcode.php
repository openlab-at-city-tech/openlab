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
class Quiz_Maker_Quiz_Category
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

        add_shortcode('ays_quiz_cat', array($this, 'ays_generate_quiz_categories_method'));
    }

    // Categories shortcode
    public function ays_generate_quiz_categories_method($attr){
        global $wpdb;

        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;

        if (is_null($id)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), '', $content);
        }

        $display = ( isset($attr['display']) && $attr['display'] != '' ) ? sanitize_text_field($attr['display']) : 'all';
        $count   = ( isset($attr['count']) && $attr['count'] != '' ) ? absint(sanitize_text_field($attr['count'])) : 5;
        $layout  = ( isset($attr['layout']) && $attr['layout'] != '' ) ? sanitize_text_field($attr['layout']) : 'list';

        $category = Quiz_Maker_Data::get_quiz_category_by_id($id);

        if (isset($category['published']) && $category['published'] == 0) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), '', $content);
        }

        $sql = "SELECT id FROM {$wpdb->prefix}aysquiz_quizes WHERE quiz_category_id = ". $id ." AND published=1 ";

        $content = "";
        $random_quiz_id = array();
        if ($display === 'random') {
            $sql .= " ORDER BY RAND() LIMIT ".$count;
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            $all_quiz_count = count($result);
            foreach ($result as $val) {
                $val = absint(intval($val['id']));
                $random_quiz_id[] = $val;
            }
        }else{
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            $all_quiz_count = count($result);
            foreach ($result as $val) {
                $val = absint(intval($val['id']));
                $random_quiz_id[] = $val;
            }
        }

        $conteiner_flex_class = '';
        if ($layout == 'grid') {
            $conteiner_flex_class = 'ays-quiz-category-container-flex';
        }

        $content .= "<h2 class='ays-quiz-category-title' style='text-align:center;'>
            <span style='font-size:3rem;'>". __( "Category", $this->plugin_name) .":</span>
            <em>". stripslashes($category['title']) ."</em>
        </h2>";

        if(isset($category['description']) && $category['description'] != ''){
            $content .= "<div class='ays-quiz-category-description'>". do_shortcode(stripslashes(wpautop($category['description']))) ."</div>";
        }

        $content .= "<div class='ays-quiz-category-container ". $conteiner_flex_class ."'>";
        foreach ($random_quiz_id as $quiz_id) {
            $content .= "<div class='ays-quiz-category-item'>";
            $shortcode = "[ays_quiz id='".$quiz_id."']";
            $content .= do_shortcode($shortcode);
            $content .= "</div>";
        }
        $content .= "</div>";
        // echo $content;
        return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
    }

}
