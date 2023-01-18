<?php
/**
 * Ranks Widget
 *
 * @package badgeos/includes/widgets
 */

/**
 * Class earned_user_ranks_widget.
 */
class Earned_User_Ranks_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'earned_user_ranks_class',
			'description' => __( 'Displays ranks earned by the logged in user', 'badgeos' ),
		);
		parent::__construct( 'earned_user_ranks_widget', __( 'BadgeOS Earned User Ranks', 'badgeos' ), $widget_ops );
	}

	/**
	 * Form
	 *
	 * @param array $instance args.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'          => __( 'My Ranks', 'badgeos' ),
			'set_point_type' => '',
			'point_total'    => '',
			'set_ranks'      => array(),
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title              = $instance['title'];
		$number             = isset( $instance['number'] ) ? $instance['number'] : '';
		$point_total        = $instance['point_total'];
		$set_point_type     = ( isset( $instance['total_points_type'] ) ) ? $instance['total_points_type'] : '';
		$set_ranks          = ( isset( $instance['set_ranks'] ) ) ? (array) $instance['set_ranks'] : array();
		$remove_title_field = ( isset( $instance['remove_title_field'] ) ) ? $instance['remove_title_field'] : '';
		$remove_thumb_field = ( isset( $instance['remove_thumb_field'] ) ) ? $instance['remove_thumb_field'] : '';
		$rank_section_title = ( isset( $instance['rank_section_title'] ) ) ? $instance['rank_section_title'] : '';
		?>

		<p><label><input type="checkbox" id="<?php echo esc_attr( $this->get_field_name( 'point_total' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'point_total' ) ); ?>" <?php checked( $point_total, 'on' ); ?> /> <?php esc_attr_e( 'Display user\'s total points', 'badgeos' ); ?></label></p>
		<p>
			<?php
			// Get all credit types.
			$point_types = badgeos_get_point_types();
			if ( is_array( $point_types ) && ! empty( $point_types ) ) {
				?>
				<select id="total_points_type" name="<?php echo esc_attr( $this->get_field_name( 'total_points_type' ) ); ?>" class="widget-total-points-type">
					<option value=""><?php esc_attr_e( 'Select Points Type', 'badgeos' ); ?></option>
					<?php

					foreach ( $point_types as $key => $point_type ) {
						?>
								<option value="<?php echo esc_attr( $point_type->ID ); ?>" <?php echo esc_html( selected( $set_point_type, $point_type->ID ) ); ?> ><?php echo esc_html( $point_type->post_title ); ?></option>
							<?php
					}
					?>
				</select>
				<br />
				<span class="tool-hint"><?php esc_attr_e( 'Total points of selected type will be displayed on frontend.', 'badgeos' ); ?></span>
			<?php } ?>
		</p>
		<p><label><?php esc_attr_e( 'Title', 'badgeos' ); ?>: <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"  type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>
			<p><label><?php esc_attr_e( 'Number to display (0 = all)', 'badgeos' ); ?>: <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>"  type="text" value="<?php echo absint( $number ); ?>" /></label></p>
			<p><label><?php esc_attr_e( 'Hide following fields:', 'badgeos' ); ?></label></p>
		<p>
					<label for="remove_title_field">
						<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'remove_title_field' ) ); ?>" id="<?php echo esc_attr( $this->get_field_name( 'remove_title_field' ) ); ?>" <?php echo 'on' === $remove_title_field ? esc_attr( 'checked' ) : ''; ?> />
						<?php esc_attr_e( 'Title Field', 'badgeos' ); ?>
					</label><br />
					<label for="remove_thumb_field">
						<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'remove_thumb_field' ) ); ?>" id="<?php echo esc_attr( $this->get_field_name( 'remove_thumb_field' ) ); ?>" <?php echo 'on' === $remove_thumb_field ? esc_attr( 'checked' ) : ''; ?> />
						<?php esc_attr_e( 'Thumbnail Field', 'badgeos' ); ?>
					</label><br />
				</p>
		<p><label><?php esc_attr_e( 'Display only the following User Rank Types:', 'badgeos' ); ?></label></p>
		<p>
			<?php
				// Get all registered achievements.
				$ranks = badgeos_get_rank_types_slugs_detailed();

				// Loop through all registered achievements.
			foreach ( $ranks as $rank_slug => $rank ) {

				// If rank displaying exists in the saved array it is enabled for display.
				$checked = checked( in_array( $rank_slug, $set_ranks, true ), true, false );

				echo esc_html(
					'<label for="' . esc_attr( $this->get_field_name( 'set_ranks' ) ) . '_' . esc_attr( $rank_slug ) . '">'
					. '<input type="checkbox" name="' . esc_attr( $this->get_field_name( 'set_ranks' ) ) . '[]" id="' . esc_attr( $this->get_field_name( 'set_ranks' ) ) . '_' . esc_attr( $rank_slug ) . '" value="' . esc_attr( $rank_slug ) . '" ' . $checked . ' />'
					. ' ' . esc_html( ucfirst( $rank['plural_name'] ) )
					. '</label><br />'
				);

			}
			?>
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

		if ( ! isset( $new_instance['set_ranks'] ) || empty( $new_instance['set_ranks'] ) ) {
			$new_instance['set_ranks'] = array();
		}
		$instance['title']             = sanitize_text_field( $new_instance['title'] );
		$instance['number']            = absint( $new_instance['number'] );
		$instance['set_ranks']         = array_map( 'sanitize_text_field', $new_instance['set_ranks'] );
		$instance['total_points_type'] = sanitize_text_field( $new_instance['total_points_type'] );
		$instance['point_total']       = ( ! empty( $new_instance['point_total'] ) ) ? sanitize_text_field( $new_instance['point_total'] ) : '';

		$instance['remove_title_field'] = ( ! empty( $new_instance['remove_title_field'] ) ) ? sanitize_text_field( $new_instance['remove_title_field'] ) : '';
		$instance['remove_thumb_field'] = ( ! empty( $new_instance['remove_thumb_field'] ) ) ? sanitize_text_field( $new_instance['remove_thumb_field'] ) : '';

		$instance['rank_section_title'] = ( ! empty( $new_instance['rank_section_title'] ) ) ? sanitize_text_field( $new_instance['rank_section_title'] ) : '';

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

		$remove_title_field = isset( $instance['remove_title_field'] ) ? $instance['remove_title_field'] : '';
		$remove_thumb_field = isset( $instance['remove_thumb_field'] ) ? $instance['remove_thumb_field'] : '';
		$rank_section_title = ( isset( $instance['rank_section_title'] ) ) ? $instance['rank_section_title'] : '';

		$show_title = true;
		if ( 'on' === $remove_title_field ) {
			$show_title = false;
		}

		$show_thumb = true;
		if ( 'on' === $remove_thumb_field ) {
			$show_thumb = false;
		}

		if ( ! empty( $title ) ) {
			echo esc_html( $args['before_title'] . $title . $args['after_title'] );
		};

		// User must be logged in to view earned badges and points.
		if ( is_user_logged_in() ) {
			wp_enqueue_style( 'badgeos-widget' );

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

			if ( isset( $instance['set_ranks'] ) && is_array( $instance['set_ranks'] ) && count( $instance['set_ranks'] ) > 0 ) {
				$user_ranks = badgeos_get_user_ranks( array( 'rank_type' => $instance['set_ranks'] ) );
				if ( isset( $user_ranks ) && count( $user_ranks ) > 0 ) {
					?>
					<p class="badgeos-user-ranks-main">
					<?php if ( ! empty( $rank_section_title ) ) { ?>
						<h3><?php echo esc_html( $rank_section_title ); ?></h3>
					<?php } ?>
					<?php
					echo '<ul class="widget-ranks-listing">';
					$rank_width = '50';
					if ( isset( $badgeos_settings['badgeos_rank_global_image_width'] ) && intval( $badgeos_settings['badgeos_rank_global_image_width'] ) > 0 ) {
						$rank_width = intval( $badgeos_settings['badgeos_rank_global_image_width'] );
					}

					$rank_height = '50';
					if ( isset( $badgeos_settings['badgeos_rank_global_image_height'] ) && intval( $badgeos_settings['badgeos_rank_global_image_height'] ) > 0 ) {
						$rank_height = intval( $badgeos_settings['badgeos_rank_global_image_height'] );
					}

					$number_to_show = isset( $instance['number'] ) ? absint( $instance['number'] ) : '';
					$thecount       = 0;
					foreach ( $user_ranks as $rank ) {

						$img                       = badgeos_get_rank_image( $rank->rank_id, $rank_width, $rank_height );
									$img_permalink = 'javascript:;';
						if ( ! function_exists( 'post_exists' ) ) {
							require_once ABSPATH . 'wp-admin/includes/post.php';
						}
						if ( post_exists( get_the_title( $rank->rank_id ) ) ) {
							$img_permalink = get_permalink( $rank->rank_id );
						}

									$thumb      = $img ? '<a class="badgeos-item-thumb" href="' . $img_permalink . '">' . $img . '</a>' : '';
									$class      = 'widget-badgeos-item-title';
									$item_class = $thumb ? ' has-thumb' : '';

									$rank_title = get_the_title( $rank->rank_id );
						if ( empty( $rank_title ) ) {
							$rank_title = '<a class="widget-badgeos-item-title ' . esc_attr( $class ) . '" href="javascript:;">' . esc_html( $rank->rank_title ) . '</a>';
						} else {
							$permalink  = get_permalink( $rank->rank_id );
							$rank_title = '<a class="widget-badgeos-item-title ' . esc_attr( $class ) . '" href="' . esc_url( $permalink ) . '">' . esc_html( $rank_title ) . '</a>';
						}

									echo '<li id="widget-rank-listing-item-' . absint( $rank->rank_id ) . '" class="widget-rank-listing-item' . esc_attr( $item_class ) . '">';
						if ( $show_thumb ) {
							echo esc_html( $thumb );
						}

						if ( $show_title ) {
							echo esc_html( $rank_title );
						}

									do_action( 'badgeos_widget_ranks_listing', $rank );
									echo '</li>';

									$thecount++;

						if ( $thecount === $number_to_show && 0 !== $number_to_show ) {
							break;
						}
					}
								echo '</ul>';
					?>
						</p>
					<?php
				}
			}
		} else {
			// user is not logged in so display a message.
			echo esc_html( '<p>' . esc_attresc_attr_e( 'You must be logged in to view earned ranks and points', 'badgeos' ) . '</p> )' );

		}

		if ( array_key_exists( 'after_widget', $args ) ) {
			echo esc_html( $args['after_widget'] );
		}

	}

}
