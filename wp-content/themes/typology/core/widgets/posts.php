<?php

class Typology_Posts_Widget extends WP_Widget {

	var $defaults;

	function __construct() {
		$widget_ops = array( 'classname' => 'typology_posts_widget', 'description' => esc_html__( 'Display your posts with this widget', 'typology' ) );
		$control_ops = array( 'id_base' => 'typology_posts_widget' );
		parent::__construct( 'typology_posts_widget', esc_html__( 'Typology Posts', 'typology' ), $widget_ops, $control_ops );

		$this->defaults = array(
			'title' => esc_html__( 'Posts', 'typology' ),
			'style' => 'list',
			'numposts' => 5,
			'category' => array(),
			'auto_detect' => 0,
			'orderby' => 0,
			'meta' => array( 'date' ),
			'manual' => array(),
			'tag' => array(),
		);
	}


	function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'] );

		
		if ( !empty( $title ) ) {
			$title =  $before_title . $title . $after_title;
			echo $title;
		}
	
		//print_r($instance);

		$q_args = array(
			'post_type'=> 'post',
			'posts_per_page' => $instance['numposts'],
			'ignore_sticky_posts' => 1,
			'orderby' => $instance['orderby']
		);


		if ( !empty( $instance['manual'] ) && !empty( $instance['manual'][0] ) ) {
			$q_args['posts_per_page'] = absint( count( $instance['manual'] ) );
			$q_args['orderby'] =  'post__in';
			$q_args['post__in'] =  $instance['manual'];
			$q_args['post_type'] = array_keys( get_post_types( array( 'public' => true ) ) );

		} else {

			if ( !empty( $instance['auto_detect'] ) && is_single() ) {

				$cats = get_the_category();

				if ( !empty( $cats ) ) {
					foreach ( $cats as $k => $cat ) {
						$q_args['category__in'][] = $cat->term_id;
					}
				}

			} else {

				if ( !empty( $instance['category'] ) ) {
					$q_args['category__in'] = $instance['category'];
				}
			}

			if ( !empty( $instance['tag'] ) ) {
				$q_args['tag__in'] = $instance['tag'];
			}

			if($q_args['orderby'] == 'title'){
				$q_args['order'] = 'ASC';
			}


		}

		$widget_posts = new WP_Query( $q_args );

		if ( $widget_posts->have_posts() ): ?>

			<div class="typology-posts-widget-container style-<?php echo esc_attr( $instance['style'] ); ?>">
				
				<?php while ( $widget_posts->have_posts() ) : $widget_posts->the_post(); ?>
					
					<article <?php post_class(); ?>>

						<?php call_user_func( array($this, 'display_'.$instance['style']) ); ?>

		                <?php the_title( sprintf( '<h6><a href="%s">', esc_url( get_permalink() ) ), '</a></h6>' );  ?>
		                
		                <?php if(!empty($instance['meta']) && $meta = typology_get_meta_data( $instance['meta'] ) ) : ?>
		                	<div class="entry-meta"><?php echo $meta; ?></div>
		            	<?php endif; ?>



					</article>

				<?php endwhile; ?>

			</div>

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

		<?php
		echo $after_widget;
	}

	function display_thumbnail(){ ?>
		<div class="entry-image">
		      <a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
		</div>
		<?php
	}

	function display_timeline(){ ?>
       <?php $date = the_date('d', '', '', false ); ?>
        <div class="post-date">
            <?php if( !empty( $date  ) ) : ?>
                <span class="post-date-day"><?php echo get_the_date( 'd' ); ?></span><span class="post-date-month"><?php echo get_the_date( 'M' ); ?></span>
            <?php endif; ?>
        </div>
	<?php
	}

	function display_list(){ ?>

	<?php
	}


	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['style'] = $new_instance['style'];
		$instance['orderby'] = $new_instance['orderby'];
		$instance['category'] = $new_instance['category'];
		$instance['numposts'] = absint( $new_instance['numposts'] );
		$instance['auto_detect'] = isset( $new_instance['auto_detect'] ) ? 1 : 0;
		$instance['meta'] = !empty($new_instance['meta']) ? $new_instance['meta'] : array();
		$instance['manual'] = !empty( $new_instance['manual'] ) ? explode( ",", $new_instance['manual'] ) : array();
		$instance['tag'] = typology_get_tax_term_id_by_name( $new_instance['tag'] );
		
		return $instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title', 'typology' ); ?>:</label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" type="text" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
	  		<?php $this->widget_style( $this, $instance['style'] ); ?>
		</p>

		<p>
	   	 	<label for="<?php echo esc_attr($this->get_field_id( 'numposts' )); ?>"><?php esc_html_e( 'Number of posts to show', 'typology' ); ?>:</label>
		 	<input id="<?php echo esc_attr($this->get_field_id( 'numposts' )); ?>" type="text" name="<?php echo esc_attr($this->get_field_name( 'numposts' )); ?>" value="<?php echo absint( $instance['numposts'] ); ?>" class="small-text" />
	  	</p>

		<p>
	  		<?php $this->widget_meta( $this, $instance['meta'] ); ?>
		</p>

	  	<p>
	  	 <?php $this->widget_orderby( $this, $instance['orderby'] ); ?>
	    </p>

	   <p>
	   	 <label for="<?php echo esc_attr($this->get_field_id( 'manual' )); ?>"><?php esc_html_e( 'Or choose manually', 'typology' ); ?>:</label>
		 <input id="<?php echo esc_attr($this->get_field_id( 'manual' )); ?>" type="text" name="<?php echo esc_attr($this->get_field_name( 'manual' )); ?>" value="<?php echo esc_attr(implode( ",", $instance['manual'] )); ?>" class="widefat" />
		 <small class="howto"><?php esc_html_e( 'Specify post ids separated by comma if you want to select only those posts. i.e. 213,32,12,45 Note: you can also choose pages as well as custom post types', 'typology' ); ?></small>
	   </p>

	  <p>
	  	<?php $this->widget_tax( $this, 'category', $instance['category'] ); ?>
	  </p>

	  
	  <p>
		<input id="<?php echo esc_attr($this->get_field_id( 'auto_detect' )); ?>" type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'auto_detect' )); ?>" value="1" <?php checked( 1, $instance['auto_detect'] ); ?>/>
		<label for="<?php echo esc_attr($this->get_field_id( 'auto_detect' )); ?>"><?php esc_html_e( 'Auto detect category', 'typology' ); ?></label>
		<small class="howto"><?php esc_html_e( 'If sidebar is used on single post template, display posts from current post category ', 'typology' ); ?></small>
	  </p>

	   <p>
	   	 <label for="<?php echo esc_attr($this->get_field_id( 'tag' )); ?>"><?php esc_html_e( 'Tagged with', 'typology' ); ?>:</label>
		 <input id="<?php echo esc_attr($this->get_field_id( 'tag' )); ?>" type="text" name="<?php echo esc_attr($this->get_field_name( 'tag' )); ?>" value="<?php echo esc_attr(typology_get_tax_term_name_by_id($instance['tag'])); ?>" class="widefat" />
		 <small class="howto"><?php esc_html_e( 'Specify one or more tags separated by comma. i.e. life, cooking, funny moments', 'typology' ); ?></small>
	   </p>
	

	<?php
	}


	function widget_orderby( $widget_instance = false, $orderby = false ) {

		$orders = typology_get_post_order_opts();

		if ( !empty( $widget_instance ) ) { ?>
				<label for="<?php echo esc_attr($widget_instance->get_field_id( 'orderby' )); ?>"><?php esc_html_e( 'Order by:', 'typology' ); ?></label>
				<select id="<?php echo esc_attr($widget_instance->get_field_id( 'orderby' )); ?>" name="<?php echo esc_attr($widget_instance->get_field_name( 'orderby' )); ?>" class="widefat">
					<?php foreach ( $orders as $key => $order ) { ?>
						<option value="<?php echo esc_attr($key); ?>" <?php selected( $orderby, $key );?>><?php echo esc_html($order); ?></option>
					<?php } ?>
				</select>
		<?php }
	}



	function widget_tax( $widget_instance, $taxonomy, $selected_taxonomy = false ) {
		if ( !empty( $widget_instance ) && !empty( $taxonomy ) ) {
			$categories = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
?>
				<label for="<?php echo esc_attr($widget_instance->get_field_id( 'category' )); ?>"><?php esc_html_e( 'Choose from:', 'typology' ); ?></label><br/>
					<?php foreach ( $categories as $category ) { ?>
						<input type="checkbox" name="<?php echo esc_attr($widget_instance->get_field_name( 'category' )); ?>[]" value="<?php echo esc_attr($category->term_id); ?>" <?php echo in_array( $category->term_id, (array)$selected_taxonomy ) ? 'checked': ''?> /> <?php echo esc_html($category->name); ?><br/>
					<?php } ?>
		<?php }
	}

	function widget_style( $widget_instance = false, $current = false ) {

		$styles = array(
			'list' => esc_html__( 'Simple list', 'typology' ),
			'timeline' => esc_html__( 'Timeline', 'typology' ),
			'thumbnail' => esc_html__( 'List with thumbnails', 'typology' )
		);

		if ( !empty( $widget_instance ) ) { ?>
				<label for="<?php echo esc_attr($widget_instance->get_field_id( 'style' )); ?>"><?php esc_html_e( 'Posts style:', 'typology' ); ?></label>
				<select id="<?php echo esc_attr($widget_instance->get_field_id( 'style' )); ?>" name="<?php echo esc_attr($widget_instance->get_field_name( 'style' )); ?>" class="widefat">
					<?php foreach ( $styles as $id => $title ) { ?>
						<option value="<?php echo esc_attr($id); ?>" <?php selected( $current, $id );?>><?php echo $title; ?></option>
					<?php } ?>
				</select>
		<?php }
	}

	function widget_meta( $widget_instance = false, $current = false ) {

		$meta = typology_get_meta_opts();

		if ( !empty( $widget_instance ) ) : ?>
				<label for="<?php echo esc_attr($widget_instance->get_field_id( 'meta' )); ?>"><?php esc_html_e( 'Display meta data:', 'typology' ); ?></label><br/>
				<?php foreach ( $meta as $id => $title ) : ?>
				<?php $checked = in_array($id, $current ) ? 'checked="checked"' : ''; ?>
				<input type="checkbox" id="<?php echo esc_attr($widget_instance->get_field_id( 'meta' )); ?>" name="<?php echo esc_attr($widget_instance->get_field_name( 'meta' )); ?>[]" value="<?php echo esc_attr($id); ?>" <?php echo $checked; ?>> <?php echo esc_html($title); ?><br/>
				<?php endforeach; ?>
		<?php endif; ?>
	<?php }

}

?>
