<?php
class WPBadgeDisplayWidget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'WPBadgeDisplayWidget',
			'WPBadgeDisplay Widget',
			array(
				'description' => __( 'Display Open Badges', 'wpbadgedisplay' ),
			)
		);
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'              => '',
			'openbadges_email'   => '',
			'openbadges_user_id' => '',
		) );

		$title = $instance['title'];
		?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wpbadgedisplay' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>

	<p><label for="openbadges_user_id"><?php _e( 'Email Account:', 'wpbadgedisplay' ); ?> <input class="widefat" id="openbadges_email" name="<?php echo $this->get_field_name( 'openbadges_email' ); ?>" type="text" value="<?php echo esc_attr( $instance['openbadges_email'] ); ?>" /></label></p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$defaults = array(
			'title'              => '',
			'openbadges_email'   => '',
			'openbadges_user_id' => '',
		);

		$args = wp_parse_args( (array) $new_instance, $defaults );

		$instance['title']              = sanitize_text_field( $args['title'] );
		$instance['openbadges_email']   = $args['openbadges_email'];
		$instance['openbadges_user_id'] = wpbadgedisplay_convert_email_to_openbadges_id( $instance['openbadges_email'] );

		return $instance;
	}

	function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$badgedata = wpbadgedisplay_get_public_backpack_contents( $instance['openbadges_user_id'], null );
		echo wpbadgedisplay_return_embed( $badgedata );
	}

	public static function register_widgets() {
		register_widget( 'WPBadgeDisplayWidget' );
	}

}
