<?php

class OpenLab_Support_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'openlab_help',
			'OpenLab Help',
			array(
				'description' => '',
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		echo $args['before_title'];
		echo esc_html( $instance['title'] );
		echo $args['after_title'];

		echo openlab_render_block( 'openlab-support' );

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$r = array_merge(
			[
				'title' => 'OpenLab Help',
			],
			$instance
		);

		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
			<input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" value="<?php echo esc_attr( $r['title'] ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = [
			'title' => isset( $new_instance['title'] ) ? $new_instance['title'] : '',
		];

		return $instance;
	}
}
