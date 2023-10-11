<?php

class Quiz_Maker_Widget extends WP_Widget {

    public function __construct() {

        $widget_options = array(
            'classname' => 'quiz_maker',
            'description' => 'Quiz Maker Widget',
        );
        parent::__construct('quiz_maker','Quiz Maker Widget',$widget_options);
    }

    public function form( $instance ) {
        if ($instance) {
            $quiz_id = esc_attr($instance['quiz_maker_id']);
        } else {
            $quiz_id = 0;
        }
        global $wpdb;
        $quizes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aysquiz_quizes", 'ARRAY_A');
        ?>
        <p>
            <select class="widefat" id="<?php echo $this->get_field_id('quiz_id');?>" name="<?php echo $this->get_field_name('quiz_maker_id');?>">
                <option value="0" selected disabled>Select Quiz</option>
                <?php
                foreach ($quizes as $quiz) {?>
                    <option value="<?php echo $quiz['id'];?>" <?php echo $quiz['id'] == $quiz_id ? "selected" : "";?> >
                        <?php echo stripslashes( $quiz['title'] ); ?>
                    </option>
                <?php }
                ?>
            </select>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        // Fields
        $instance['quiz_maker_id'] = absint($new_instance['quiz_maker_id']);
        return $instance;
    }

    public function widget( $args, $instance ) {
        $quiz_id =  $instance['quiz_maker_id'];
        echo do_shortcode('[ays_quiz id='.$quiz_id.']');
    }

}
