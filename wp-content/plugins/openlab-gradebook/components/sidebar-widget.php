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

        if (!is_user_logged_in()) {
            return false;
        }
        
        extract($args);

        $title = apply_filters('widget_title', $instance['title']);
        $title = !empty($title) ? esc_html($title) : 'Link to OpenLab Gradebook';
        $message = esc_html($instance['message']);
        $url = esc_url(admin_url('admin.php?page=oplb_gradebook#courses'));

        ob_start();
        include(plugin_dir_path(__FILE__) . 'parts/widgets/sidebar-widget-frontend.php');
        $frontend = ob_get_clean();

        echo $frontend;
    }

    public function form($instance) {

        $title = !empty($instance['title']) ? esc_attr($instance['title']) : 'Link to OpenLab Gradebook';
        $message = !empty($instance['message']) ? esc_attr($instance['message']) : '';

        ob_start();
        include(plugin_dir_path(__FILE__) . 'parts/widgets/sidebar-widget-form.php');
        $form = ob_get_clean();

        echo $form;
    }

    public function update($new_instance, $old_instance) {

        $instance = $old_instance;

        $instance['title'] = esc_html(strip_tags($new_instance['title']));
        $instance['message'] = esc_html(strip_tags($new_instance['message']));

        return $instance;
    }

}

/* Register the OpenLab GradeBook Widget */
add_action('widgets_init', function() {
    register_widget('OPLB_Gradebook_Widget');
});
