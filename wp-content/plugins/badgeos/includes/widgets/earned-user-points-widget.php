<?php
/**
 * Points Widget
 *
 * @package badgeos/includes/widgets
 */

/**
 * Class earned_user_points_widget.
 *
 * @package badgeos
 */
class Earned_User_Points_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'earned_user_points_class',
			'description' => __( 'Displays Total points earned by the logged in user', 'badgeos' ),
		);
		parent::__construct( 'earned_user_points_widget', __( 'BadgeOS Earned User Points', 'badgeos' ), $widget_ops );
	}

	/**
	 * Form
	 *
	 * @param array $instance args.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'          => __( 'My Points', 'badgeos' ),
			'set_point_type' => '',
			'point_total'    => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title          = $instance['title'];
		$number         = isset( $instance['number'] ) ? $instance['number'] : '';
		$point_total    = $instance['point_total'];
		$set_point_type = ( isset( $instance['total_points_type'] ) ) ? $instance['total_points_type'] : '';
		?>
		<p><label><?php esc_attr_e( 'Title', 'badgeos' ); ?>: <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"  type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>
		<p><label><input type="checkbox" id="<?php echo esc_attr( $this->get_field_name( 'point_total' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'point_total' ) ); ?>" <?php checked( $point_total, 'on' ); ?> /> <?php esc_attr_e( 'Display user\'s total points', 'badgeos' ); ?></label></p>
			<p>
				<?php
				/**
				 * Get all credit types.
				 */
				$point_types = badgeos_get_point_types();
				if ( is_array( $point_types ) && ! empty( $point_types ) ) {
					?>
					<select id="total_points_type" name="<?php echo esc_attr( $this->get_field_name( 'total_points_type' ) ); ?>" class="widget-total-points-type">
						<option value=""><?php esc_attr_e( 'Select Points Type' ); ?></option>
						<?php

						foreach ( $point_types as $key => $point_type ) {
							?>
									<option value="<?php echo esc_attr( $point_type->ID ); ?>" <?php echo esc_attr( selected( $set_point_type, $point_type->ID ) ); ?> ><?php echo esc_html( $point_type->post_title ); ?></option>
								<?php
						}
						?>
					</select>
					<br />
					<span class="tool-hint"><?php esc_attr_e( 'Total points of selected type will be displayed on frontend.', 'badgeos' ); ?></span>
				<?php } ?>
			</p>
			<?php
	}

	/**
	 * Widget update.
	 *
	 * @param array $new_instance selected args.
	 * @param array $old_instance $args.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']             = sanitize_text_field( $new_instance['title'] );
		$instance['total_points_type'] = sanitize_text_field( $new_instance['total_points_type'] );
		$instance['point_total']       = ( ! empty( $new_instance['point_total'] ) ) ? sanitize_text_field( $new_instance['point_total'] ) : '';

		return $instance;
	}

	/**
	 * Widget
	 *
	 * @param array $args selected args.
	 * @param array $instance args.
	 */
	public function widget( $args, $instance ) {
		$args = array_map( 'sanitize_text_field', $args );

		if ( array_key_exists( 'before_widget', $args ) ) {
			echo esc_html( $args['before_widget'] );
		}

		$badgeos_settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
		$title            = apply_filters( 'widget_title', $instance['title'] );

		if ( ! empty( $title ) ) {
			echo esc_html( $args['before_title'] . $title . $args['after_title'] );
		};
		// user must be logged in to view earned badges and points.
		if ( is_user_logged_in() ) {

			// display user's points if widget option is enabled.
			if ( 'on' === $instance['point_total'] && ! empty( $instance['total_points_type'] ) ) {
				$earned_points = badgeos_get_points_by_type( $instance['total_points_type'], get_current_user_id() );
				$plural_name   = badgeos_utilities::get_post_meta( $instance['total_points_type'], '_point_plural_name', true );
				if ( ! empty( $plural_name ) ) {
					$point_title = $plural_name;
				} else {
					$point_title = get_the_title( $instance['total_points_type'] );
				}
				$badge_image = badgeos_get_point_image( $instance['total_points_type'], 50, 50 );
				$badge_image = apply_filters( 'badgeos_profile_points_image', $badge_image, 'front-widget', $instance['total_points_type'] );

				?>
				<div class="badgeos_earned_points_only">
					<table>
						<tr>
							<td width="100%">
								<div class="badgeos_earned_points_widget_image">
									<?php echo esc_html( $badge_image ); ?> 
								</div>
								<div class="badgeos_earned_points_widget">
									<div class="badgeos-earned-credit">
										<span><?php echo esc_html( number_format( $earned_points ) ); ?></span>
									</div>	
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="points_widget_title">
									<?php echo esc_html( $point_title ); ?>
								</div>
							</td>
						</tr>
					</table>
				</div> 
				<?php
			}
		} else {

			// user is not logged in so display a message.
			echo '<p>' . esc_attr_e( 'You must be logged in to view earned points', 'badgeos' ) . '</p>';

		}

		if ( array_key_exists( 'after_widget', $args ) ) {
			echo esc_html( $args['after_widget'] );
		}
	}

}

