<p>
    <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title: (defaults to "")'); ?></label>
    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_html($title); ?>" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id('link_text')); ?>"><?php esc_html_e('Link Text (defaults to "OpenLab GradeBook")'); ?></label>
    <textarea class="widefat" rows="16" cols="20" id="<?php echo esc_attr($this->get_field_id('link_text')); ?>" name="<?php echo esc_attr($this->get_field_name('link_text')); ?>"><?php echo esc_html($message); ?></textarea>
</p>
