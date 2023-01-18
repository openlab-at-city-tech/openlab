<?php
/**
 * Custom Shortcodes - User dashboard.
 *
 * @package BadgeOS
 */

/**
 * Register the [badgeos_user_dashboard] shortcode.
 */
function badgeos_register_user_dashboard() {

	badgeos_register_shortcode(
		array(
			'name'            => __( 'User Dashboard', 'badgeos' ),
			'slug'            => 'badgeos_user_dashboard',
			'output_callback' => 'badgeos_user_dashboard_callback',
			'description'     => __( "Display list of User Points Achievements and Ranks.", 'badgeos' ),
		)
	);
}
add_action( 'init', 'badgeos_register_user_dashboard' );

/**
 * Single Achievement Shortcode.
 *
 * @param  array $atts Shortcode attributes.
 * @return string      HTML markup.
 */
function badgeos_user_dashboard_callback( $atts = array() ) {

	global $wpdb;

	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );

	/**
	 * Get the post id.
	 */
	$atts = shortcode_atts(
		array(
			'show_sharing_opt' => 'Yes',
			'achievement'      => 0,
			'user_id'          => 0,
			'award_id'         => 0,
		),
		$atts,
		'badgeos_evidence'
	);

		ob_start();
		wp_enqueue_style( 'badgeos-font-awesome' );
		wp_enqueue_style( 'badgeos-front' );

		wp_enqueue_script( 'badgeos-achievements' );
		$site_id 		 = get_current_blog_id();
		$current_user_ID = get_current_user_id();
		$all_users 		 = get_users();
		$colpan =  6;
		?>
	<div class="user-dashboard-main-wrapper">
		
		<div class="badgeos-user-dash-tabs">
			<span class="user-dash-tab" id="bdgo-pts"><i class="fa fa-star" aria-hidden="true"></i> Points</span>
			<span class="user-dash-tab" id="bdgo-achiev"><i class="fa fa-trophy" aria-hidden="true"></i> Achievements </span>
			<span class="user-dash-tab" id="bdgo-rnk"><i class="fa fa-shield" aria-hidden="true"></i> Ranks</span>
		</div>	 
		<div class="badge-os bdgo-rnk"> 
			<table class="badgeos-user-table form-table badgeos-rank-table badgeos-rank-revoke-table">
			<thead>
				<tr>
					<th><?php echo esc_html__( 'Rank Name', 'badgeos' ); ?></th>
					<th><?php echo esc_html__( 'Rank Type', 'badgeos' ); ?></th>
					<th><?php echo esc_html__( 'Points', 'badgeos' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$user_ranks = badgeos_get_user_ranks(
						array(
							'user_id'   => absint($current_user_ID ),
							'rank_type' => 'all',
						)
					);

					if ( $user_ranks ) {
						foreach ( $user_ranks as $rank ) {

							$point_type         = badgeos_points_type_display_title( $rank->credit_id );
							$default_point_type = ( ! empty( $badgeos_settings['default_point_type'] ) ) ? $badgeos_settings['default_point_type'] : '';
							if ( 0 === intval( $rank->rank_id ) ) {
								$point_type = badgeos_points_type_display_title( $default_point_type );
							}

							if ( empty( $point_type ) ) {
								$point_type = esc_html__( 'Points', 'badgeos' );
							}

							?>
							<tr class="<?php echo esc_attr( $rank->rank_type ); ?> ">
								<?php
								$ranks_image = badgeos_get_rank_image( $rank->rank_id, 50, 50 );
								?>
								<td class="badgeos-user-points-cell">
									<?php echo wp_kses_post( $ranks_image ); ?>
									<?php echo esc_html( $rank->rank_title ); ?>
								</td>
								<td><?php echo esc_html( $rank->rank_type ); ?></td>
								<td><?php echo esc_html( $rank->credit_amount . ' ' . $point_type ); ?></td>
							</tr>
							<?php
						}
					} else {
						
						?>
						<tr class="no-awarded-rank">
							<td colspan="<?php echo esc_attr( $colpan ); ?>">
							<span class="description">
								<?php echo esc_html__( 'No Awarded Ranks', 'badgeos' ); ?>
							</span>
							</td>
						</tr>
						<?php
					}
				?>
				</tbody>
				<tfoot>
				<tr>
					<th><?php echo esc_html__( 'Rank Name', 'badgeos' ); ?></th>
					<th><?php echo esc_html__( 'Rank Type', 'badgeos' ); ?></th>
					<th><?php echo esc_html__( 'Points', 'badgeos' ); 	 ?></th>
				</tr>
			</tfoot>
			</table> <?php
			$total_ranks = 0;
			if(sizeof(badgeos_get_ranks()) != 0){
				if(badgeos_get_ranks()[0]->post_type =='post'){
				$total_ranks = 0;
			}else{
				$total_ranks = sizeof(badgeos_get_ranks());
			}
		}
			?>
			<div class="badgeos-table-below-div">
				<div class="badgeos-all-points">
					<?php	echo esc_html__( 'Total Ranks ', 'badgeos' ) .esc_attr( $total_ranks) ; ?>
				</div>
				<div class="badgeos-user-earned">
					<?php	echo esc_html__( 'Your Ranks ', 'badgeos' ) .esc_attr( sizeof( $user_ranks ) ); ?>
				</div>
			</div>
		</div>
		<?php

	
			//end of user ranks
			// start of points table
			$credit_types = badgeos_get_point_types();
			?>
				<div class="badge-os badgeos-userd-points-table bdgo-pts" > 
						<table class="badgeos-user-table" >
							<tr>
								<th valign="left"><?php echo esc_html__( 'Point Type', 'badgeos' ) ; ?> </th>	
								<th valign="left"><?php echo esc_html__( 'In Circulation', 'badgeos' ) ; ?> </th>	
								<th valign="left"><?php echo esc_html__( 'Earned Points', 'badgeos' ) ; ?> </th>	
							</tr> <?php
			$all_points=0;
			$all_user_points=0;
			if ( is_array( $credit_types ) && ! empty( $credit_types ) ) {
				
				
				foreach ( $credit_types as $credit_type ) {
					$total_points = 0;
					foreach( $all_users as $user ) {
						$userID 	  = $user->ID;
						$total_points = $total_points + badgeos_get_points_by_type( $credit_type->ID, $userID );
					}
					$all_points 	  = $all_points + $total_points;
					$earned_credits   = badgeos_get_points_by_type( $credit_type->ID, $current_user_ID );
					$all_user_points  = $all_user_points + $earned_credits  ;
					$post_type_plural = badgeos_points_type_display_title( $credit_type->ID );
					$badge_image      = badgeos_get_point_image( $credit_type->ID, 50, 50 );
					$badge_image      = apply_filters( 'badgeos_profile_points_image', $badge_image, 'backend-profile', $credit_type );
					?>
							<tr>
								<td class="badgeos-user-points-cell" >
									<?php echo wp_kses_post( $badge_image ); ?>
									<span style="height:100%;"><?php echo esc_attr( $post_type_plural ); ?></span>
								</td>
								<td valign="left">
									<?php echo esc_attr( $total_points ); ?></span>
								</td>
								<td valign="left">
									<?php echo esc_attr( $earned_credits ); ?></span>
								</td>
							</tr>
					<?php
				}
			} else { ?>
							<td colspan="<?php echo esc_attr( $colpan ); ?>">
								<span class="description">
									<?php echo esc_html__( 'No points created', 'badgeos' ); ?>
								</span>
							</td>
			<?php }
			
			?>
							<tr>
								<th valign="left"><?php echo esc_html__( 'Point Type', 'badgeos' ) ; ?> </th>	
								<th valign="left"><?php echo esc_html__( 'In Circulation', 'badgeos' ) ; ?> </th>	
								<th valign="left"><?php echo esc_html__( 'Earned Points', 'badgeos' ) ; ?> </th>	
							</tr>
						</table>
						<div class="badgeos-table-below-div">
							<div class="badgeos-all-points">
								<?php echo esc_html__( 'Total Points ', 'badgeos' ).esc_attr( $all_points ) ; ?>
							</div>
							<div class="badgeos-user-earned">
								<?php echo esc_html__( 'Your Points ', 'badgeos' ).esc_attr( $all_user_points ) ; ?>
							</div>
						</div>
				</div> <?php
			//end of points table
			$badgeos_settings = ! empty( badgeos_utilities::get_option( 'badgeos_settings' ) ) ? badgeos_utilities::get_option( 'badgeos_settings' ) : array();
			$args = array(
				'user_id' 		   => $current_user_ID
				);
				$userAcheivement 		= badgeos_get_user_achievements( $args );
				$total_user_achievement = 0;
				$all_achevements		= array();
				$total_achevement_arr	= array();
				$achevement_img 		= array();
				foreach ( $userAcheivement as $achievement ){
					if ( trim( $badgeos_settings['achievement_step_post_type'] ) !== $achievement->post_type ) {
						if( !in_array( $achievement->achievement_title , $all_achevements ) ) {
							array_push( $all_achevements , $achievement->achievement_title );
						}
					}
				}
				foreach ( $all_achevements as $key ){
					$total_achevement_arr[$key] = 0;
				}
				
				foreach( $userAcheivement as $achievement ) {
					if ( trim( $badgeos_settings['achievement_step_post_type'] ) !== $achievement->post_type ) {
						foreach ( $all_achevements as $key ) {
							if( $key == $achievement->achievement_title ) {
								$total_achevement_arr[$key]=$total_achevement_arr[$key] +1;
								
								$badge_image = badgeos_get_achievement_post_thumbnail( $achievement->ID, array( 50, 50 ) );
								$achevement_img[$key] = apply_filters( 'badgeos_profile_achivement_image', $badge_image, $achievement, array( 50, 50 ) );
							}
						}
				}
				}
			?>
				<div class=" badge-os badgeos-user-dashboard bdgo-achiev">
				<table class="badgeos-user-table">
					<tr>
						<th><?php echo esc_html__( 'Achievements/Badges', 'badgeos' ) ; ?> </th>
						<th><?php echo esc_html__( 'Earned', 'badgeos' ) ; ?></th>
					</tr>
					<?php
					if ( is_array( $all_achevements ) && ! empty( $all_achevements ) ){
						foreach ($all_achevements as $key){ 
							$total_user_achievement = $total_user_achievement + $total_achevement_arr[$key];
						?>
						<tr>
							<td class="badgeos-user-points-cell">
								<?php echo wp_kses_post( $achevement_img[$key] ); ?>
								<?php echo esc_attr( $key ); ?>
							</td>
							<td><?php echo $total_achevement_arr[$key]; ?></td>
						</tr>
					<?php }  
				} else { ?>
					<td colspan="<?php echo esc_attr( $colpan ); ?>">
						<span class="description">
							<?php echo esc_html__( 'No Achievements', 'badgeos' ); ?>
						</span>
					</td>
				<?php
				} ?>

					<tr>
						<th><?php echo esc_html__( 'Achievements/Badges', 'badgeos' ) ; ?></th>
						<th><?php echo esc_html__( 'Earned', 'badgeos' ) ; ?></th>
					</tr>
					
				</table>
				<div class="badgeos-table-below-div">
					<div class="badgeos-all-points">
						<?php echo esc_html__( 'Total Achievement Types ', 'badgeos' ).esc_attr( sizeof( $all_achevements ) ); ; ?>
					</div>
					<div class="badgeos-user-earned">
						<?php echo esc_html__( 'Earned Achievements ', 'badgeos' ).esc_attr( $total_user_achievement ).esc_html__( ' Times', 'badgeos' );  ?>
					</div>
				</div>
				
			</div>
	</div>
		<?php

		$output = ob_get_clean();


	/**
	 * Return our rendered dashboard.
	 */
	return $output;
}
