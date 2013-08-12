<?php

class wpdm_topdownloads_widget extends WP_Widget {
    /** constructor */
    function wpdm_topdownloads_widget() {
        parent::WP_Widget(true, 'WPDM Top Downlaods');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $sdc = $instance['sdc'];
        $nop = $instance['nop2']>0?$instance['nop2']:5;
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; 
                echo "<ul>";        
                wpdm_top_packages($nop,$sdc);
                echo "</ul>";
               echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['sdc'] = strip_tags($new_instance['sdc']);
    $instance['nop2'] = strip_tags($new_instance['nop2']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $sdc = esc_attr($instance['sdc']);
        $nop = esc_attr($instance['nop2']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('nop1'); ?>"><?php _e('Number of packages to show:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('nop2'); ?>" name="<?php echo $this->get_field_name('nop2'); ?>" type="text" value="<?php echo $nop; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('sdc'); ?>"><?php _e('Show Download Count:'); ?>
          <input id="<?php echo $this->get_field_id('sdc'); ?>" name="<?php echo $this->get_field_name('sdc'); ?>" type="checkbox" value="1" <?php echo $sdc?'checked=checked':''; ?> />
          </label> 
        </p>
        <?php 
    }

} 


class wpdm_newpacks_widget extends WP_Widget {
    /** constructor */
    function wpdm_newpacks_widget() {
        parent::WP_Widget(false, 'WPDM New Packages');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $sdc = $instance['sdc'];
        $nop = $instance['nop1']>0?$instance['nop1']:5;
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; 
                echo "<ul>";        
                wpdm_new_packages($nop,$sdc);
                echo "</ul>";
               echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['sdc'] = strip_tags($new_instance['sdc']);
    $instance['nop1'] = strip_tags($new_instance['nop1']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $sdc = esc_attr($instance['sdc']);
        $nop = esc_attr($instance['nop1']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('nop1'); ?>"><?php _e('Number of packages to show:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('nop1'); ?>" name="<?php echo $this->get_field_name('nop1'); ?>" type="text" value="<?php echo $nop; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('sdc'); ?>"><?php _e('Show Download Count:'); ?>
          <input id="<?php echo $this->get_field_id('sdc'); ?>" name="<?php echo $this->get_field_name('sdc'); ?>" type="checkbox" value="1" <?php echo $sdc?'checked=checked':''; ?> />
          </label> 
        </p>
        <?php 
    }

} 

function wpdm_register_widgets() {
    register_widget( 'wpdm_newpacks_widget' );
}

add_action('widgets_init', 'wpdm_register_widgets');

?>