<?php

/*
 * Optional sidebar widget that displays link to OpenLab Gradebook
 */

class OPLB_Gradebook_Widget extends WP_Widget {

    public function __construct() {
        
        parent::__construct(
                'oplb_gradebook_widget', __('OpenLab Gradebook Widget', 'oplb_gradebook'), array(
            'classname' => 'oplb_gradebook_widget',
            'description' => __('This widget will display a direct link to the OpenLab Gradebook interface. Will not display for logged out users.', 'oplb_gradebook')
                )
        );

        load_plugin_textdomain('oplb_gradebook', false, basename(dirname(__FILE__)) . '/languages');
    }

    public function widget($args, $instance) {
        global $wpdb;

        if (!is_user_logged_in()) {
            return false;
        }

        if(!$this->show_user_widget()){
            return false;
        }
        
        extract($args);

        $title = apply_filters('widget_title', $instance['title']);
        $title = !empty($title) ? esc_html($title) : 'Link to OpenLab Gradebook';
        $message = esc_html($instance['link_text']);
        $message = !empty($message) ? esc_html($message) : 'OpenLab Gradebook';
        $url = esc_url(admin_url('admin.php?page=oplb_gradebook#courses'));

        ob_start();
        include(plugin_dir_path(__FILE__) . 'parts/widgets/sidebar-widget-frontend.php');
        $frontend = ob_get_clean();

        echo $frontend;
    }

    public function form($instance) {
        
        if(!$this->show_user_widget()){
            return false;
        }
        
        $title = !empty($instance['title']) ? esc_attr($instance['title']) : 'Link to OpenLab Gradebook';
        $message = !empty($instance['link_text']) ? esc_attr($instance['link_text']) : 'OpenLab Gradebook';

        ob_start();
        include(plugin_dir_path(__FILE__) . 'parts/widgets/sidebar-widget-form.php');
        $form = ob_get_clean();

        echo $form;
    }

    public function update($new_instance, $old_instance) {

        $instance = $old_instance;

        $instance['title'] = esc_html(strip_tags($new_instance['title']));
        $instance['link_text'] = esc_html(strip_tags($new_instance['link_text']));

        return $instance;
    }
    
    private function show_user_widget(){
        
        return apply_filters('oplb_gradebook_show_user_widget', true);
        
    }

}

/* Register the OpenLab GradeBook Widget */
add_action('widgets_init', function() {
    register_widget('OPLB_Gradebook_Widget');
});
