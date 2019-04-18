<?php

class ThemeRain_Recent_Projects extends WP_Widget {

	function __construct() {

		parent::__construct( 'themerain_recent_projects', 'ThemeRain Recent Projects', array( 'description' => 'The most recent projects.' ) );

	}

	function form( $instance ) {

		$title = empty( $instance['title'] ) ? 'Recent Projects' : esc_attr( $instance['title'] );
		$number = empty( $instance['number'] ) ? '3' : esc_attr( $instance['number'] );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">Number of projects to show:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3">
		</p>
		<?php

	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		return $instance;

	}

	function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );
		$number = $instance['number'];

		echo $args['before_widget'];
		if ( $title ) echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		echo '<ul>';
			$recent_projects_query = new WP_Query( array( 'post_type' => 'project', 'showposts' => $number ) );
			while ( $recent_projects_query->have_posts() ) : $recent_projects_query->the_post();
			?>
				<li><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a></li>
			<?php
			endwhile;
			wp_reset_postdata();
		echo '</ul>';
		echo $args['after_widget'];

	}

}

function register_themerain_recent_projects() {

    register_widget( 'ThemeRain_Recent_Projects' );

}
add_action( 'widgets_init', 'register_themerain_recent_projects' );