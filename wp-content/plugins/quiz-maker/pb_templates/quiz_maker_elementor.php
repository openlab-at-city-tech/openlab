<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Widget_Quiz_Maker_Elementor extends Widget_Base {
    public function get_name() {
        return 'quiz-maker';
    }
    public function get_title() {
        return __( 'Quiz Maker', 'quiz-maker' );
    }
    public function get_icon() {
        // Icon name from the Elementor font file, as per http://dtbaker.net/web-development/creating-your-own-custom-elementor-widgets/
        return 'fa fa-power-off ays_fa_power_off_quiz';
    }
	public function get_categories() {
		return array( 'general' );
	}
    protected function _register_controls() {
        $this->start_controls_section(
            'section_quiz_maker',
            array(
                'label' => esc_html__( 'Quiz Maker', 'quiz-maker' ),
            )
        );

        $this->add_control(
            'quiz_title',
            array(
                'label' => __( 'Quiz Title', 'quiz-maker' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'title' => __( 'Enter the quiz title', 'quiz-maker' ),
                'placeholder' => __( 'Enter the quiz title', 'quiz-maker' ),
            )
        );
        $this->add_control(
            'quiz_title_alignment',
            array(
                'label' => __( 'Title Alignment', 'quiz-maker' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => array(
                    'left'      => 'Left',
                    'right'     => 'Right',
                    'center'    => 'Center'
                )
            )
        );
        $this->add_control(
            'quiz_selector',
            array(
                'label' => __( 'Select Quiz', 'quiz-maker' ),
                'type' => Controls_Manager::SELECT,
                'default' => $this->get_default_quiz(),
                'options' => $this->get_active_quizzes()
            )
        );

        $this->end_controls_section();
    }
    protected function render( $instance = array() ) {
        $settings = $this->get_settings_for_display();
        echo ( isset( $settings['quiz_title'] ) && ! empty( $settings['quiz_title'] ) ) ? "<h2 style='text-align: {$settings['quiz_title_alignment']}'>{$settings['quiz_title']}</h2>" : "";
        echo do_shortcode("[ays_quiz id={$settings['quiz_selector']}]");
    }

    public function get_active_quizzes(){
        global $wpdb;
        $quizes_table = $wpdb->prefix . 'aysquiz_quizes';
        $sql = "SELECT id,title FROM {$quizes_table} WHERE published=1;";
        $results = $wpdb->get_results( $sql, ARRAY_A );
        $options = array();
        foreach ( $results as $result ){
            $options[$result['id']] = $result['title'];
        }
        return $options;
    }

    public function get_default_quiz(){
        global $wpdb;
        $quizes_table = $wpdb->prefix . 'aysquiz_quizes';
        $sql = "SELECT id FROM {$quizes_table} WHERE published=1 limit 1;";
        $id = $wpdb->get_var( $sql );

        return intval($id);
    }

    protected function content_template() {}
    public function render_plain_content( $instance = array() ) {}
}