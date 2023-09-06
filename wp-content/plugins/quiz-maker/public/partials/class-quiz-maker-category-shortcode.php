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

    private $html_class_prefix = 'ays-quiz-category-';
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

        add_shortcode('ays_quiz_cat', array($this, 'ays_generate_quiz_categories_method'));
        add_shortcode('ays_quiz_cat_title', array($this, 'ays_generate_quiz_categories_title_method'));
        add_shortcode('ays_quiz_cat_description', array($this, 'ays_generate_quiz_categories_description_method'));
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

        $category_title = (isset($category['title']) && $category['title'] != '') ? stripslashes($category['title']) : "";

        $content .= "<h2 class='ays-quiz-category-title' style='text-align:center;'>
            <span style='font-size:3rem;'>". __( "Category", $this->plugin_name) .":</span>
            <em>". $category_title ."</em>
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
        $content = Quiz_Maker_Data::ays_quiz_translate_content( $content );
        // echo $content;

        return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
    }

    /*
    ==========================================
        Show quiz category title | Start
    ==========================================
    */

    public function ays_generate_quiz_categories_title_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_category_title = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_category_title);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_category_title = $this->ays_generate_cat_title_html( $id );
        $quiz_category_title = Quiz_Maker_Data::ays_quiz_translate_content( $quiz_category_title );

        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_category_title);
    }

    public function ays_generate_cat_title_html( $id ) {

        $results = Quiz_Maker_Data::get_quiz_category_by_id($id);

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $category_title = (isset($results['title']) && $results['title'] != '') ? sanitize_text_field($results['title']) : "";

        if ( $category_title == "" ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."category-title' id='". $this->html_name_prefix ."category-title-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $category_title;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show quiz category title | End
    ==========================================
    */

    /*
    ==========================================
        Show quiz category description | Start
    ==========================================
    */

    public function ays_generate_quiz_categories_description_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_category_description = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_category_description);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_category_description = $this->ays_generate_cat_description_html( $id );
        $quiz_category_description = Quiz_Maker_Data::ays_quiz_translate_content( $quiz_category_description );

        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_category_description);
    }

    public function ays_generate_cat_description_html( $id ) {

        $results = Quiz_Maker_Data::get_quiz_category_by_id($id);

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $category_description = (isset($results['description']) && $results['description'] != '') ? Quiz_Maker_Data::ays_autoembed($results['description']) : "";

        if ( $category_description == "" ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<div class='". $this->html_name_prefix ."category-description' id='". $this->html_name_prefix ."category-description-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $category_description;
        $content_html[] = "</div>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show quiz category description | End
    ==========================================
    */
}
