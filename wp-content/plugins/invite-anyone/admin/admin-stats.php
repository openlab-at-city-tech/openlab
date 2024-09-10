<?php

/**
 * The Stats class for Invite Anyone
 *
 * @package Invite Anyone
 * @since 0.9
 */
class Invite_Anyone_Stats {
	public $time_periods;
	public $params;
	public $posts;
	public $date_sql;

	/**
	 * PHP 4 constructor
	 *
	 * @package Invite Anyone
	 * @since 0.9
	 */
	public function invite_anyone_stats() {
		$this->__construct();
	}

	/**
	 * PHP 5 constructor
	 *
	 * @package Invite Anyone
	 * @since 0.9
	 */
	public function __construct() {
		$this->setup_time_periods();
		$this->setup_get_params();
		$this->get_posts();
	}

	public function setup_time_periods() {
		// $time_periods are in seconds
		$this->time_periods = apply_filters(
			'invite_anyone_stats_time_periods',
			array(
				60 * 60 * 24          => array(
					'name' => __( '24 Hours', 'invite-anyone' ),
				),
				60 * 60 * 24 * 3      => array(
					'name' => __( '3 Days', 'invite-anyone' ),
				),
				60 * 60 * 24 * 7      => array(
					'name' => __( '1 Week', 'invite-anyone' ),
				),
				60 * 60 * 24 * 28     => array(
					'name' => __( '4 Weeks', 'invite-anyone' ),
				),
				60 * 60 * 24 * 30 * 3 => array(
					'name' => __( '3 Months', 'invite-anyone' ),
				),
				0                     => array(
					'name' => __( 'All Time', 'invite-anyone' ),
				),
			)
		);
	}
	/**
	 * Gets the stats params out of the $_GET global
	 *
	 * @package Invite Anyone
	 * @since 0.9
	 */
	public function setup_get_params() {
		$params = array();

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['user_id'] ) ) {
			$params['user_id'] = (int) $_REQUEST['user_id'];
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$this->params = $params;
	}

	/**
	 * Gets posts, based on the params provided
	 *
	 * @package Invite Anyone
	 * @since 0.9
	 */
	public function get_posts() {
		global $wpdb;

		// $posts is a multidimensional array, containing all different time periods
		$posts = array();

		foreach ( $this->time_periods as $tp => $period ) {
			$invite = new Invite_Anyone_Invitation();

			// Will be populated out of $this->params. Defaults to none?
			$args = array(
				'posts_per_page' => '-1',
				'status'         => 'pending,draft,future,publish,trash',
			);

			// Create the date filter
			if ( $tp ) {
				$since          = time() - $tp;
				$this->date_sql = $wpdb->prepare( ' AND post_date > %s', gmdate( 'Y-m-d H:i:s', $since ) );

				add_filter( 'posts_where_paged', array( $this, 'where_filter' ) );
			}

			$invites = $invite->get( $args );

			// Remove the filter
			if ( $tp ) {
				remove_filter( 'posts_where_paged', array( $this, 'where_filter' ) );
			}

			$period['total_count']       = 0;
			$period['accepted_count']    = 0;
			$period['total_count_cs']    = 0;
			$period['accepted_count_cs'] = 0;
			$period['unique_emails']     = 0;
			$period['unique_inviters']   = 0;

			$period['unique_emails']   = array();
			$period['unique_inviters'] = array();

			if ( $invites->have_posts() ) {
				while ( $invites->have_posts() ) {
					$invites->the_post();

					// Increase the total count
					++$period['total_count'];

					$author_key = get_the_author_meta( 'ID' );

					// If it's a new sender, add them to $unique_inviters
					if ( ! isset( $period['unique_inviters'][ $author_key ] ) ) {
						$period['unique_inviters'][ $author_key ] = array(
							'overall'     => array(
								'sent'     => 0,
								'accepted' => 0,
							),
							'cloudsponge' => array(
								'sent'     => 0,
								'accepted' => 0,

							),
						);
					}

					// Bump the inviter's count
					++$period['unique_inviters'][ $author_key ]['overall']['sent'];

					// Is it accepted?
					$accepted = get_post_meta( get_the_ID(), 'bp_ia_accepted', true );

					if ( $accepted ) {
						// Total accepted count
						++$period['accepted_count'];

						// Author's accepted count
						++$period['unique_inviters'][ $author_key ]['overall']['accepted'];
					}

					// Is it a CloudSponge invite?
					$is_cloudsponge = get_post_meta( get_the_ID(), 'bp_ia_is_cloudsponge', true );

					if ( __( 'Yes', 'invite-anyone' ) === $is_cloudsponge ) {

						++$period['total_count_cs'];

						// Author count
						++$period['unique_inviters'][ $author_key ]['cloudsponge']['sent'];

						if ( $accepted ) {
							// Total accepted count
							++$period['accepted_count_cs'];

							// Author's accepted count
							++$period['unique_inviters'][ $author_key ]['cloudsponge']['accepted'];
						}
					}
				}
			}

			// With all the data tallied, we can come up with some percentages

			// Overall acceptance rate
			if ( $period['total_count'] ) {
				$period['acceptance_rate']  = round( ( $period['accepted_count'] / $period['total_count'] ) * 100 );
				$period['acceptance_rate'] .= '%';
			} else {
				$period['acceptance_rate'] = __( 'n/a', 'invite-anyone' );
			}

			// CS percentage
			if ( $period['total_count'] ) {
				$period['cs_percentage']  = round( ( $period['total_count_cs'] / $period['total_count'] ) * 100 );
				$period['cs_percentage'] .= '%';
			} else {
				$period['cs_percentage'] = __( 'n/a', 'invite-anyone' );
			}

			// CS acceptance rate
			if ( $period['total_count_cs'] ) {
				$period['acceptance_rate_cs']  = round( ( $period['accepted_count_cs'] / $period['total_count_cs'] ) * 100 );
				$period['acceptance_rate_cs'] .= '%';
			} else {
				$period['acceptance_rate_cs'] = __( 'n/a', 'invite-anyone' );
			}

			// Find the most active user
			$leader_user_id_pct = 0;
			$leader_val_pct     = 0;

			$leader_user_id_num = 0;
			$leader_val_num     = 0;

			$leader_user_id_pct_cs = 0;
			$leader_val_pct_cs     = 0;

			$leader_user_id_num_cs = 0;
			$leader_val_num_cs     = 0;

			foreach ( $period['unique_inviters'] as $user_id => $u ) {
				// Overall
				if ( $u['overall']['sent'] ) {
					if ( $u['overall']['sent'] >= $leader_val_num ) {
						$leader_user_id_num = $user_id;
						$leader_val_num     = $u['overall']['sent'];
					}

					if ( ( $u['overall']['accepted'] / $u['overall']['sent'] ) >= $leader_val_pct ) {
						$leader_user_id_pct = $user_id;
						$leader_val_pct     = $u['overall']['accepted'] / $u['overall']['sent'] * 100;
					}
				}

				// CloudSponge
				if ( $u['cloudsponge']['sent'] ) {
					if ( $u['cloudsponge']['sent'] >= $leader_val_num_cs ) {
						$leader_user_id_num_cs = $user_id;
						$leader_val_num_cs     = $u['cloudsponge']['sent'];
					}

					if ( ( $u['cloudsponge']['accepted'] / $u['cloudsponge']['sent'] ) >= $leader_val_pct_cs ) {
						$leader_user_id_pct_cs = $user_id;
						$leader_val_pct_cs     = $u['cloudsponge']['accepted'] / $u['cloudsponge']['sent'] * 100;
					}
				}
			}

			$period['top_users']['top_user_num'] = array(
				'user_id' => $leader_user_id_num ? $leader_user_id_num : false,
				'sent'    => $leader_val_num ? $leader_val_num : false,
			);

			$period['top_users']['top_user_pct'] = array(
				'user_id'  => $leader_user_id_pct ? $leader_user_id_pct : false,
				'accepted' => $leader_val_pct ? round( $leader_val_pct ) . '%' : '-',
			);

			$period['top_users']['top_user_num_cs'] = array(
				'user_id' => $leader_user_id_num_cs ? $leader_user_id_num_cs : false,
				'sent'    => $leader_val_num_cs ? $leader_val_num_cs : false,
			);

			$period['top_users']['top_user_pct_cs'] = array(
				'user_id'  => $leader_user_id_pct_cs ? $leader_user_id_pct_cs : false,
				'accepted' => $leader_val_pct_cs ? round( $leader_val_pct_cs ) . '%' : '-',
			);

			// Fetch userlinks
			foreach ( $period['top_users'] as $key => $top_user ) {
				$link                                     = bp_core_get_userlink( $top_user['user_id'] );
				$period['top_users'][ $key ]['user_link'] = $link;
			}

			$this->time_periods[ $tp ] = $period;
		}
	}

	/**
	 * Add the date_sql filter to the where clause of the post query
	 *
	 * @package Invite Anyone
	 * @since 0.9
	 *
	 * @param str $where Where clause from WP_Query
	 * @param str $where Where clause with date_sql appended
	 */
	public function where_filter( $where ) {
		$where .= $this->date_sql;
		return $where;
	}

	/**
	 * Displays the admin panel markup
	 *
	 * @package Invite Anyone
	 * @since 0.9
	 */
	public function display() {
		?>

		<table class="widefat ia-stats">
			<thead><tr>
				<th scope="col" class="in-the-last">
					<?php esc_html_e( 'In the last...', 'invite-anyone' ); ?>
				</th>

				<th scope="col">
					<?php esc_html_e( 'Total Sent', 'invite-anyone' ); ?>
				</th>

				<th scope="col">
					<?php esc_html_e( 'Total Accepted', 'invite-anyone' ); ?>
				</th>

				<th scope="col">
					<?php esc_html_e( 'Acceptance Rate', 'invite-anyone' ); ?>
				</th>

				<th scope="col" class="top-inviter">
					<?php esc_html_e( 'Top Inviter (by #)', 'invite-anyone' ); ?>
				</th>

				<th scope="col" class="top-inviter">
					<?php esc_html_e( 'Top Inviter (by % accepted)', 'invite-anyone' ); ?>
				</th>

				<?php if ( defined( 'INVITE_ANYONE_CS_ENABLED' ) && INVITE_ANYONE_CS_ENABLED ) : ?>
					<th scope="col">
						<?php esc_html_e( 'Total Sent (CloudSponge)', 'invite-anyone' ); ?>
					</th>

					<th scope="col">
						<?php esc_html_e( 'Total Accepted (CloudSponge)', 'invite-anyone' ); ?>
					</th>

					<th scope="col">
						<?php esc_html_e( 'Acceptance Rate (CloudSponge)', 'invite-anyone' ); ?>
					</th>

					<th scope="col">
						<?php esc_html_e( 'CloudSponge Usage', 'invite-anyone' ); ?>
					</th>

					<th scope="col" class="top-inviter">
						<?php esc_html_e( 'Top Inviter (by #) (CloudSponge)', 'invite-anyone' ); ?>
					</th>

					<th scope="col" class="top-inviter">
						<?php esc_html_e( 'Top Inviter (by % accepted) (CloudSponge)', 'invite-anyone' ); ?>
					</th>
				<?php endif ?>

			</tr></thead>

			<tbody>

			<?php foreach ( $this->time_periods as $tp => $period ) : ?>

				<tr>
					<th scope="row">
						<?php echo esc_html( $period['name'] ); ?>
					</th>

					<td>
						<?php echo esc_html( $period['total_count'] ); ?>
					</td>

					<td>
						<?php echo esc_html( $period['accepted_count'] ); ?>
					</td>

					<td>
						<?php echo esc_html( $period['acceptance_rate'] ); ?>
					</td>

					<td>
						<?php // translators: number of items sent ?>
						<?php echo wp_kses_post( $period['top_users']['top_user_num']['user_link'] ); ?> <span class="description"><?php echo esc_html( sprintf( __( '(%d sent)', 'invite-anyone' ), $period['top_users']['top_user_num']['sent'] ) ); ?></span>
					</td>

					<td>
						<?php // translators: percentage of items accepted ?>
						<?php echo wp_kses_post( $period['top_users']['top_user_pct']['user_link'] ); ?> <span class="description"><?php echo esc_html( sprintf( __( '(%s accepted)', 'invite-anyone' ), $period['top_users']['top_user_pct']['accepted'] ) ); ?></span>
					</td>

					<?php if ( defined( 'INVITE_ANYONE_CS_ENABLED' ) && INVITE_ANYONE_CS_ENABLED ) : ?>
						<td>
							<?php echo esc_html( $period['total_count_cs'] ); ?>
						</td>

						<td>
							<?php echo esc_html( $period['accepted_count_cs'] ); ?>
						</td>

						<td>
							<?php echo esc_html( $period['acceptance_rate_cs'] ); ?>
						</td>

						<td>
							<?php echo esc_html( $period['cs_percentage'] ); ?>
						</td>

						<td>
							<?php // translators: number of items sent ?>
							<?php echo wp_kses_post( $period['top_users']['top_user_num_cs']['user_link'] ); ?> <span class="description"><?php echo esc_html( sprintf( __( '(%d sent)', 'invite-anyone' ), $period['top_users']['top_user_num_cs']['sent'] ) ); ?></span>
						</td>

						<td>
							<?php // translators: number of items accepted ?>
							<?php echo wp_kses_post( $period['top_users']['top_user_pct_cs']['user_link'] ); ?> <span class="description"><?php echo esc_html( sprintf( __( '(%s accepted)', 'invite-anyone' ), $period['top_users']['top_user_pct_cs']['accepted'] ) ); ?></span>
						</td>
					<?php endif ?>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>

		<?php if ( defined( 'INVITE_ANYONE_CS_ENABLED' ) && INVITE_ANYONE_CS_ENABLED ) : ?>
			<p class="description"><strong>Note:</strong> CloudSponge data has only been recorded since Invite Anyone v0.9.</p>
		<?php endif ?>
		<?php
	}
}

?>
