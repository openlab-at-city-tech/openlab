<?php

/**
 * Category widget class
 *
 * @since  1.0
 */

class Typology_Category_Widget extends WP_Widget {

	var $defaults;

	function __construct() {
		$widget_ops = array( 'classname' => 'typology_category_widget', 'description' => esc_html__( 'Display your category list with this widget', 'typology' ) );
		$control_ops = array( 'id_base' => 'typology_category_widget' );
		parent::__construct( 'typology_category_widget', esc_html__( 'Typology Categories', 'typology' ), $widget_ops, $control_ops );

		$this->defaults = array(
			'title' => esc_html__( 'Categories', 'typology' ),
			'categories' => array(),
			'count' => 1
		);
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( !empty($title) ) {
			echo $before_title . $title . $after_title;
		}

		?>

		<ul>
		    <?php $cats = get_categories( array( 'include'	=> $instance['categories'])); ?>
		    <?php $cats = typology_sort_option_items( $cats,  $instance['categories']); ?>
		    <?php foreach($cats as $cat): ?>
			    	<li>
			    		<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class=""><?php echo esc_html( $cat->name ); ?></a>
			    		<?php if(!empty($instance['count'])): ?>
			    			<?php echo wp_kses_post( '('. $cat->count  . ')'); ?>
			    		<?php endif; ?>
			    	</li>
		    <?php endforeach; ?> 
		</ul>

		<?php
		echo $after_widget;
	}


	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['categories'] = !empty($new_instance['categories']) ? $new_instance['categories'] : array();
		$instance['count'] = isset($new_instance['count']) ? 1 : 0;
		$instance['type'] = $new_instance['type'];
		
		return $instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title', 'typology' ); ?>:</label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" type="text" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<?php $cats = get_categories( array( 'hide_empty' => false, 'number' => 0 ) ); ?>
		<?php $cats = typology_sort_option_items( $cats,  $instance['categories']); ?>

		<p>
		<label><?php esc_html_e( 'Choose (re-order) categories:', 'typology' ); ?></label><br/>
		<div class="typology-widget-content-sortable">
		<?php foreach ( $cats as $cat ) : ?>
		   	<?php $checked = in_array( $cat->term_id, $instance['categories'] ) ? 'checked' : ''; ?>
		   	<label><input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'categories' )); ?>[]" value="<?php echo esc_attr($cat->term_id); ?>" <?php echo esc_attr($checked); ?> /><?php echo esc_html( $cat->name );?></label>
		<?php endforeach; ?>
		</div>
		<small class="howto"><?php esc_html_e( 'Note: Leave empty to display all categories', 'typology' ); ?></small>
		</p>

		<p>
			<label><input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'count' )); ?>" value="1" <?php echo checked($instance['count'], 1, true); ?> /><?php esc_html_e( 'Show post count?', 'typology' ); ?></label>
		</p>

		<?php
	}

}

?>
