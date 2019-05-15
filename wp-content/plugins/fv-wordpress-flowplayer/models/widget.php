<?php

if( class_exists('WP_Widget') ) :

class FV_Player_Widget extends WP_Widget {

  public function __construct() {

    add_action('widgets_init', array($this, 'widget_init'));

    $widget_ops = array('classname' => 'widget_fvplayer', 'description' => __('FV Player widget.'));
    $control_ops = array('width' => 400, 'height' => 350);
    parent::__construct('widget_fvplayer', __('FV Player'), $widget_ops, $control_ops);
  }

  function widget_init() {
    register_widget('FV_Player_widget');
    add_action('admin_footer', array($this, 'formFooter'), 0 );
  }

  /**
   * Outputs the content for the current Text widget instance.
   *
   * @since 2.8.0
   * @access public
   *
   * @param array $args     Display arguments including 'before_title', 'after_title',
   *                        'before_widget', and 'after_widget'.
   * @param array $instance Settings for the current Text widget instance.
   */
  public function widget($args, $instance) {

    /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

    $widget_text = !empty($instance['text']) ? $instance['text'] : '';

    /**
     * Filter the content of the Text widget.
     *
     * @since 2.3.0
     * @since 4.4.0 Added the `$this` parameter.
     *
     * @param string         $widget_text The widget content.
     * @param array          $instance    Array of settings for the current widget.
     * @param WP_Widget_Text $this        Current Text widget instance.
     */
    $text = apply_filters('widget_text', $widget_text, $instance, $this);

    echo $args['before_widget'];
    if (!empty($title)) {
      echo $args['before_title'] . $title . $args['after_title'];
    }
    ?>
    <div class="textwidget"><?php echo!empty($instance['filter']) ? wpautop($text) : $text; ?></div>
    <?php
    echo $args['after_widget'];
  }

  /**
   * Handles updating settings for the current Text widget instance.
   *
   * @since 2.8.0
   * @access public
   *
   * @param array $new_instance New settings for this instance as input by the user via
   *                            WP_Widget::form().
   * @param array $old_instance Old settings for this instance.
   * @return array Settings to save or bool false to cancel saving.
   */
  public function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = sanitize_text_field($new_instance['title']);
    if (current_user_can('unfiltered_html'))
      $instance['text'] = $new_instance['text'];
    else
      $instance['text'] = wp_kses_post(stripslashes($new_instance['text']));
    $instance['filter'] = !empty($new_instance['filter']);
    return $instance;
  }

  /**
   * Outputs the Text widget settings form.
   *
   * @since 2.8.0
   * @access public
   *
   * @param array $instance Current settings.
   */
  public function form($instance) {
    add_action('admin_head', 'wp_enqueue_media');    
    
    $instance = wp_parse_args((array) $instance, array('title' => '', 'text' => ''));
    $filter = isset($instance['filter']) ? $instance['filter'] : 0;
    $title = sanitize_text_field($instance['title']);

    //var_dump($this->number);
    ?><p><label for = "<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>

      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

    <p>
      <style>
        .wp-customizer .fv-wordpress-flowplayer-button { display: none; }
      </style>
      <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Player'); ?>:</label>&nbsp;&nbsp;
      <input type="button" id="widget-widget_fvplayer-<?php echo $this->number; ?>-savewidget" class="button button-primary left fv-wordpress-flowplayer-button"  data-number="<?php echo $this->number; ?>" value="<?php _e( strlen( trim($instance['text']) ) ? 'Edit' : 'Add' ); ?>">    

      <textarea class="widefat" rows="5" cols="5" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_textarea($instance['text']); ?></textarea>
    </p>
    <?php
  }

  function formFooter() {
    if( function_exists('get_current_screen') ) {
      $objScreen = get_current_screen();
      if( $objScreen && $objScreen->base != 'widgets' ) return;
    }
    
    fv_wp_flowplayer_edit_form_after_editor();
    fv_player_shortcode_editor_scripts_enqueue();
  }

}

$FV_Player_Widget = new FV_Player_Widget();

endif;
