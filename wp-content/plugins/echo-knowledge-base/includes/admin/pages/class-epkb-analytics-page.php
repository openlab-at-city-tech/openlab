<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display analytics
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Analytics_Page {

	private $kb_config;

	public function __construct() {
		add_action( 'wp_ajax_epkb_toggle_article_views_counter', array( __CLASS__, 'ajax_toggle_article_views_counter' ) );
		add_action( 'wp_ajax_nopriv_epkb_toggle_article_views_counter', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_get_filtered_analytics', array( __CLASS__, 'ajax_get_filtered_analytics' ) );
		add_action( 'wp_ajax_nopriv_epkb_get_filtered_analytics', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Display the standalone Analytics page
	 */
	public function display_plugin_analytics_page() {

		EPKB_Core_Utilities::display_missing_css_message();

		// Get analytics sections
		$analytics_sections = $this->get_compact_sections();

		if ( empty( $analytics_sections ) || ! is_array( $analytics_sections ) ) {
			echo '<div class="wrap">';
			echo '<div class="epkb-analytics-empty-state">' . esc_html__( 'Analytics data is currently unavailable.', 'echo-knowledge-base' ) . '</div>';
			echo '</div>';
			return;
		}

		$section_keys = array_keys( $analytics_sections );
		$active_key = isset( $section_keys[0] ) ? $section_keys[0] : '';

		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// Start the page output
		echo '<div class="wrap" id="epkb-admin-analytics-page-wrap">'; ?>

		<h1></h1> <!-- This is here for WP admin consistency -->

		<div class="epkb-wrap">
			<div class="epkb-analytics-page-container" data-kb-id="<?php echo esc_attr( $kb_id ); ?>">
				<?php
				// Show demo data indicator if using demo data
				if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
					echo '<div class="epkb-analytics-demo-indicator">';
					echo '<div class="epkb-analytics-demo-header">';
					echo '<span class="epkb-analytics-demo-badge">' . esc_html__( 'DEMO DATA', 'echo-knowledge-base' ) . '</span>';
					echo '<span class="epkb-analytics-demo-message">' . esc_html__( 'Showing example analytics data. Real data will appear once you have articles and user activity.', 'echo-knowledge-base' ) . '</span>';
					echo '</div>';

					// Show add-on requirements
					$addon_notes = array();

					// Check if Article Rating add-on is needed
					if ( ! EPKB_Utilities::is_article_rating_enabled() ) {
						$addon_notes[] = sprintf(
							'<a href="%s" target="_blank">%s</a> %s',
							'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/',
							esc_html__( 'Article Rating and Feedback add-on', 'echo-knowledge-base' ),
							esc_html__( 'required for real rating analytics', 'echo-knowledge-base' )
						);
					}

					// Check if Advanced Search add-on is needed
					if ( ! EPKB_Utilities::is_advanced_search_enabled() ) {
						$addon_notes[] = sprintf(
							'<a href="%s" target="_blank">%s</a> %s',
							'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/',
							esc_html__( 'Advanced Search add-on', 'echo-knowledge-base' ),
							esc_html__( 'required for advanced search analytics', 'echo-knowledge-base' )
						);
					}

					if ( ! empty( $addon_notes ) ) {
						echo '<div class="epkb-analytics-demo-addons">';
						echo '<ul>';
						foreach ( $addon_notes as $note ) {
							echo '<li>' . $note . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						echo '</ul>';
						echo '</div>';
					}

					echo '</div>';
				}
				?>
				<div class="epkb-ai-custom-tabs" role="tablist">
					<div class="epkb-ai-tabs-nav">
						<div class="epkb-ai-tabs-regular">
							<?php foreach ( $analytics_sections as $slug => $section ) :
								$title = isset( $section['title'] ) ? $section['title'] : '';
								$button_classes = 'epkb-analytics-tab-button epkb-ai-tab-button';
								if ( $slug === $active_key ) {
									$button_classes .= ' is-active';
								}
								?>
								<button type="button" class="<?php echo esc_attr( $button_classes ); ?>" data-analytics-tab="<?php echo esc_attr( $slug ); ?>" role="tab" aria-selected="<?php echo $slug === $active_key ? 'true' : 'false'; ?>">
									<span><?php echo esc_html( $title ); ?></span>
								</button>
							<?php endforeach; ?>
						</div>
						<?php echo $this->get_date_range_filter_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
				<div class="epkb-analytics-tab-panels">
					<?php foreach ( $analytics_sections as $slug => $section ) :
						$panel_classes = 'epkb-analytics-tab-panel';
						if ( $slug === $active_key ) {
							$panel_classes .= ' is-active';
						}
						$content = isset( $section['content'] ) ? $section['content'] : '';
						?>
						<div class="<?php echo esc_attr( $panel_classes ); ?>" data-analytics-panel="<?php echo esc_attr( $slug ); ?>">
							<div class="epkb-analytics-tab-panel__inner">
								<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>		<?php

		echo '</div>';
	}

	/**
	 * Get HTML for date range filter
	 *
	 * @return string
	 */
	private function get_date_range_filter_html() {

		ob_start();
		?>
		<div class="epkb-analytics-date-range-filter">
			<div class="epkb-analytics-date-range-filter__inner">
				<div class="epkb-analytics-date-range-filter__quick-buttons">
					<button type="button" class="epkb-analytics-date-range-quick-btn is-active" data-preset="all-time">
						<?php echo esc_html__( 'All Time', 'echo-knowledge-base' ); ?>
					</button>
					<button type="button" class="epkb-analytics-date-range-quick-btn" data-preset="last-week">
						<?php echo esc_html__( 'Last Week', 'echo-knowledge-base' ); ?>
					</button>
					<button type="button" class="epkb-analytics-date-range-quick-btn" data-preset="last-month">
						<?php echo esc_html__( 'Last Month', 'echo-knowledge-base' ); ?>
					</button>
					<button type="button" class="epkb-analytics-date-range-quick-btn" data-preset="last-3-months">
						<?php echo esc_html__( 'Last 3 Months', 'echo-knowledge-base' ); ?>
					</button>
				</div>
				<select class="epkb-analytics-date-range-preset" id="epkb-analytics-date-range-preset">
					<option value="" selected disabled><?php echo esc_html__( 'Choose Range', 'echo-knowledge-base' ); ?></option>
					<option value="today"><?php echo esc_html__( 'Today', 'echo-knowledge-base' ); ?></option>
					<option value="yesterday"><?php echo esc_html__( 'Yesterday', 'echo-knowledge-base' ); ?></option>
					<option value="this-week"><?php echo esc_html__( 'This Week', 'echo-knowledge-base' ); ?></option>
					<option value="this-month"><?php echo esc_html__( 'This Month', 'echo-knowledge-base' ); ?></option>
					<option value="last-6-months"><?php echo esc_html__( 'Last 6 Months', 'echo-knowledge-base' ); ?></option>
					<option value="this-year"><?php echo esc_html__( 'This Year', 'echo-knowledge-base' ); ?></option>
					<option value="last-year"><?php echo esc_html__( 'Last Year', 'echo-knowledge-base' ); ?></option>
					<option value="custom"><?php echo esc_html__( 'Custom Range', 'echo-knowledge-base' ); ?></option>
				</select>
				<div class="epkb-analytics-date-range-custom" style="display: none;">
					<label>
						<span class="screen-reader-text"><?php echo esc_html__( 'Start Date', 'echo-knowledge-base' ); ?></span>
						<input type="date" class="epkb-analytics-date-start" id="epkb-analytics-date-start" />
					</label>
					<span class="epkb-analytics-date-separator"><?php echo esc_html__( 'to', 'echo-knowledge-base' ); ?></span>
					<label>
						<span class="screen-reader-text"><?php echo esc_html__( 'End Date', 'echo-knowledge-base' ); ?></span>
						<input type="date" class="epkb-analytics-date-end" id="epkb-analytics-date-end" />
					</label>
					<button type="button" class="epkb-analytics-date-range-apply epkb-primary-btn" id="epkb-analytics-date-range-apply">
						<?php echo esc_html__( 'Apply', 'echo-knowledge-base' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Calculate date range based on preset
	 *
	 * @param string $preset The date range preset (today, yesterday, this-week, etc.)
	 * @param string $start_date Optional custom start date (Y-m-d format)
	 * @param string $end_date Optional custom end date (Y-m-d format)
	 * @return array Array with 'start' and 'end' timestamps, or null for all-time
	 */
	public static function calculate_date_range( $preset, $start_date = '', $end_date = '' ) {

		// Return null for all-time (no filtering)
		if ( $preset === 'all-time' ) {
			return null;
		}

		// Handle custom range
		if ( $preset === 'custom' && ! empty( $start_date ) && ! empty( $end_date ) ) {
			$start_timestamp = strtotime( $start_date . ' 00:00:00' );
			$end_timestamp = strtotime( $end_date . ' 23:59:59' );
			if ( $start_timestamp && $end_timestamp ) {
				return array(
					'start' => $start_timestamp,
					'end' => $end_timestamp,
				);
			}
			return null;
		}

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return null;
		}

		switch ( $preset ) {
			case 'today':
				$start = clone $now;
				$start->setTime( 0, 0, 0 );
				$end = clone $now;
				$end->setTime( 23, 59, 59 );
				break;

			case 'yesterday':
				$start = clone $now;
				$start->modify( '-1 day' );
				$start->setTime( 0, 0, 0 );
				$end = clone $now;
				$end->modify( '-1 day' );
				$end->setTime( 23, 59, 59 );
				break;

			case 'this-week':
				$start = clone $now;
				$day_of_week = (int) $start->format( 'w' ); // 0 (Sunday) through 6 (Saturday)
				$start->modify( '-' . $day_of_week . ' days' );
				$start->setTime( 0, 0, 0 );
				// End yesterday to exclude today
				$end = clone $now;
				$end->modify( '-1 day' );
				$end->setTime( 23, 59, 59 );
				break;

			case 'last-week':
				$start = clone $now;
				$day_of_week = (int) $start->format( 'w' );
				$start->modify( '-' . ( $day_of_week + 7 ) . ' days' );
				$start->setTime( 0, 0, 0 );
				$end = clone $start;
				$end->modify( '+6 days' );
				$end->setTime( 23, 59, 59 );
				break;

			case 'this-month':
				$start = clone $now;
				$start->modify( 'first day of this month' );
				$start->setTime( 0, 0, 0 );
				// End yesterday to exclude today
				$end = clone $now;
				$end->modify( '-1 day' );
				$end->setTime( 23, 59, 59 );
				break;

			case 'last-month':
				$start = clone $now;
				$start->modify( 'first day of last month' );
				$start->setTime( 0, 0, 0 );
				$end = clone $now;
				$end->modify( 'last day of last month' );
				$end->setTime( 23, 59, 59 );
				break;

			case 'last-3-months':
				$start = clone $now;
				$start->modify( '-3 months' );
				$start->setTime( 0, 0, 0 );
				// End yesterday to exclude today
				$end = clone $now;
				$end->modify( '-1 day' );
				$end->setTime( 23, 59, 59 );
				break;

			case 'last-6-months':
				$start = clone $now;
				$start->modify( '-6 months' );
				$start->setTime( 0, 0, 0 );
				// End yesterday to exclude today
				$end = clone $now;
				$end->modify( '-1 day' );
				$end->setTime( 23, 59, 59 );
				break;

			case 'this-year':
				$start = clone $now;
				$start->modify( 'first day of January ' . $now->format( 'Y' ) );
				$start->setTime( 0, 0, 0 );
				// End yesterday to exclude today
				$end = clone $now;
				$end->modify( '-1 day' );
				$end->setTime( 23, 59, 59 );
				break;

			case 'last-year':
				$last_year = $now->format( 'Y' ) - 1;
				$start = EPKB_Utilities::create_datetime( 'first day of January ' . $last_year );
				$end = EPKB_Utilities::create_datetime( 'last day of December ' . $last_year );
				if ( $start === null || $end === null ) {
					return null;
				}
				$start->setTime( 0, 0, 0 );
				$end->setTime( 23, 59, 59 );
				break;

			default:
				return null;
		}

		return array(
			'start' => $start->getTimestamp(),
			'end' => $end->getTimestamp(),
		);
	}

	/**
	 * Get article views for a specific date range
	 *
	 * @param int $article_id The article post ID
	 * @param array|null $date_range Array with 'start' and 'end' timestamps, or null for all-time
	 * @return int The number of views within the date range
	 */
	public static function get_article_views_in_range( $article_id, $date_range = null ) {

		// If no date range specified, return all-time views
		if ( empty( $date_range ) ) {
			return (int) EPKB_Utilities::get_postmeta( $article_id, 'epkb-article-views', 0 );
		}

		$start_timestamp = $date_range['start'];
		$end_timestamp = $date_range['end'];

		$start_date = EPKB_Utilities::create_datetime( '@' . $start_timestamp );
		$end_date = EPKB_Utilities::create_datetime( '@' . $end_timestamp );
		if ( $start_date === null || $end_date === null ) {
			return 0;
		}

		$timezone = wp_timezone();
		$start_date->setTimezone( $timezone );
		$end_date->setTimezone( $timezone );

		$start_year = (int) $start_date->format( 'Y' );
		$end_year = (int) $end_date->format( 'Y' );
		$start_week = (int) $start_date->format( 'W' );
		$end_week = (int) $end_date->format( 'W' );

		$total_views = 0;

		// Iterate through each year in the range
		for ( $year = $start_year; $year <= $end_year; $year++ ) {
			$year_meta = EPKB_Utilities::get_postmeta( $article_id, 'epkb-article-views-' . $year, array() );
			if ( ! is_array( $year_meta ) || empty( $year_meta ) ) {
				continue;
			}

			// Determine week range for this year
			if ( $year === $start_year && $year === $end_year ) {
				// Same year - use both start and end week
				$week_start = $start_week;
				$week_end = $end_week;
			} elseif ( $year === $start_year ) {
				// First year - from start week to end of year
				$week_start = $start_week;
				$week_end = 53; // Max weeks in a year
			} elseif ( $year === $end_year ) {
				// Last year - from start of year to end week
				$week_start = 1;
				$week_end = $end_week;
			} else {
				// Middle years - all weeks
				$week_start = 1;
				$week_end = 53;
			}

			// Sum up views for weeks in range
			for ( $week = $week_start; $week <= $week_end; $week++ ) {
				$week_key = str_pad( $week, 2, '0', STR_PAD_LEFT );
				if ( isset( $year_meta[ $week_key ] ) && is_numeric( $year_meta[ $week_key ] ) ) {
					$total_views += (int) $year_meta[ $week_key ];
				}
			}
		}

		return $total_views;
	}

	/**
	 * Get weekly views data for all articles
	 *
	 * @param int $kb_id
	 * @param int $weeks_back Number of weeks to go back (default: 12)
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array of weekly data with 'week_label' and 'total_views'
	 */
	private function get_weekly_views_data( $kb_id, $weeks_back = 12, $date_range = null ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_weekly_views_data( $weeks_back );
		}

		$good_post_status = EPKB_Utilities::is_amag_on() ? 'private' : 'publish';
		$all_articles = get_posts( [
			'post_type'      => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status'    => $good_post_status,
			'posts_per_page' => -1,
		] );

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}

		// If date range is provided, calculate weeks within that range
		if ( ! empty( $date_range ) ) {
			$start_date = EPKB_Utilities::create_datetime( '@' . $date_range['start'] );
			$end_date = EPKB_Utilities::create_datetime( '@' . $date_range['end'] );

			if ( $start_date === null || $end_date === null ) {
				return array();
			}

			$timezone = wp_timezone();
			$start_date->setTimezone( $timezone );
			$end_date->setTimezone( $timezone );

			// Calculate weeks within the date range
			$weekly_data = array();
			$current_week = clone $start_date;
			$current_week->modify( 'this week' ); // Start of the week

			while ( $current_week <= $end_date ) {
				$year = (int) $current_week->format( 'Y' );
				$week = $current_week->format( 'W' );

				$weekly_data[] = array(
					'year'        => $year,
					'week'        => $week,
					'week_label'  => $current_week->format( 'M j, Y' ),
					'total_views' => 0,
				);

				$current_week->modify( '+1 week' );
			}
		} else {
			// Use the default weeks_back behavior
			// Exclude current week (start from week 1 instead of week 0)
			$weekly_data = array();

			// Initialize data for each week, excluding the current week
			for ( $i = $weeks_back; $i >= 1; $i-- ) {
				$week_date = clone $now;
				$week_date->modify( "-{$i} weeks" );
				$year = (int) $week_date->format( 'Y' );
				$week = $week_date->format( 'W' );

				$weekly_data[] = array(
					'year'        => $year,
					'week'        => $week,
					'week_label'  => $week_date->format( 'M j, Y' ),
					'total_views' => 0,
				);
			}
		}

		// Sum up views for all articles
		foreach ( $all_articles as $article ) {
			foreach ( $weekly_data as &$week_data ) {
				$year_meta = EPKB_Utilities::get_postmeta( $article->ID, 'epkb-article-views-' . $week_data['year'], array() );
				if ( isset( $year_meta[ $week_data['week'] ] ) && is_numeric( $year_meta[ $week_data['week'] ] ) ) {
					$week_data['total_views'] += (int) $year_meta[ $week_data['week'] ];
				}
			}
		}

		return $weekly_data;
	}

	/**
	 * Get weekly searches data for all searches (requires Advanced Search add-on)
	 *
	 * @param int $kb_id
	 * @param int $weeks_back Number of weeks to go back (default: 12)
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array of weekly data with 'week_label' and 'total_searches'
	 */
	private function get_weekly_searches_data( $kb_id, $weeks_back = 12, $date_range = null ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_weekly_searches_data( $weeks_back );
		}

		// Check if Advanced Search is available
		if ( ! class_exists( 'ASEA_Search_DB' ) ) { /* @disregard PREFIX */
			return array();
		}

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}

		// If date range is provided, calculate weeks within that range
		if ( ! empty( $date_range ) ) {
			$start_date = EPKB_Utilities::create_datetime( '@' . $date_range['start'] );
			$end_date = EPKB_Utilities::create_datetime( '@' . $date_range['end'] );

			if ( $start_date === null || $end_date === null ) {
				return array();
			}

			$timezone = wp_timezone();
			$start_date->setTimezone( $timezone );
			$end_date->setTimezone( $timezone );

			$weekly_data = array();
			$current_week = clone $start_date;
			$current_week->modify( 'this week' ); // Start of the week

			while ( $current_week <= $end_date ) {
				// Get start and end of this week
				$week_start = clone $current_week;
				$week_start->setTime( 0, 0, 0 );

				$week_end = clone $week_start;
				$week_end->modify( '+6 days' );
				$week_end->setTime( 23, 59, 59 );

				// Get search count for this week
				$search_db = new ASEA_Search_DB();   /* @disregard PREFIX */
				$search_count = $search_db->get_search_count(
					$kb_id,
					$week_start->format( 'Y-m-d H:i:s' ),
					$week_end->format( 'Y-m-d H:i:s' )
				);

				$weekly_data[] = array(
					'week_label'     => $current_week->format( 'M j, Y' ),
					'total_searches' => $search_count ? $search_count : 0,
				);

				$current_week->modify( '+1 week' );
			}
		} else {
			// Use the default weeks_back behavior
			// Exclude current week (start from week 1 instead of week 0)
			$weekly_data = array();

			// Initialize data for each week, excluding the current week
			for ( $i = $weeks_back; $i >= 1; $i-- ) {
				$week_date = clone $now;
				$week_date->modify( "-{$i} weeks" );

				// Get start and end of this week
				$week_start = clone $week_date;
				$day_of_week = (int) $week_start->format( 'w' );
				$week_start->modify( '-' . $day_of_week . ' days' );
				$week_start->setTime( 0, 0, 0 );

				$week_end = clone $week_start;
				$week_end->modify( '+6 days' );
				$week_end->setTime( 23, 59, 59 );

				// Get search count for this week
				$search_db = new ASEA_Search_DB();   /* @disregard PREFIX */
				$search_count = $search_db->get_search_count(
					$kb_id,
					$week_start->format( 'Y-m-d H:i:s' ),
					$week_end->format( 'Y-m-d H:i:s' )
				);

				$weekly_data[] = array(
					'week_label'     => $week_date->format( 'M j, Y' ),
					'total_searches' => $search_count ? $search_count : 0,
				);
			}
		}

		return $weekly_data;
	}

	/**
	 * Get weekly ratings data (requires Article Rating add-on)
	 *
	 * @param int $kb_id
	 * @param int $weeks_back Number of weeks to go back (default: 12)
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array of weekly data with 'week_label', 'positive_ratings', and 'negative_ratings'
	 */
	private function get_weekly_ratings_data( $kb_id, $weeks_back = 12, $date_range = null ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_weekly_ratings_data( $weeks_back );
		}

		// Check if Article Rating add-on is available
		if ( ! EPKB_Utilities::is_article_rating_enabled() ) {
			return array();
		}

		global $wpdb;
		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}
		$table_name = $wpdb->prefix . 'epkb_article_ratings';

		// If date range is provided, calculate weeks within that range
		if ( ! empty( $date_range ) ) {
			$start_date = EPKB_Utilities::create_datetime( '@' . $date_range['start'] );
			$end_date = EPKB_Utilities::create_datetime( '@' . $date_range['end'] );

			if ( $start_date === null || $end_date === null ) {
				return array();
			}

			$timezone = wp_timezone();
			$start_date->setTimezone( $timezone );
			$end_date->setTimezone( $timezone );

			$weekly_data = array();
			$current_week = clone $start_date;
			$current_week->modify( 'this week' ); // Start of the week

			while ( $current_week <= $end_date ) {
				// Get start and end of this week
				$week_start = clone $current_week;
				$week_start->setTime( 0, 0, 0 );

				$week_end = clone $week_start;
				$week_end->modify( '+6 days' );
				$week_end->setTime( 23, 59, 59 );

				// Get ratings count for this week
				$start_date_str = $week_start->format( 'Y-m-d H:i:s' );
				$end_date_str = $week_end->format( 'Y-m-d H:i:s' );

				// Count positive ratings (rating_value >= 3)
				$positive_count = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name
					WHERE kb_id = %d
					AND rating_date >= %s
					AND rating_date <= %s
					AND rating_value >= 3",
					$kb_id,
					$start_date_str,
					$end_date_str
				) );

				// Count negative ratings (rating_value < 3)
				$negative_count = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name
					WHERE kb_id = %d
					AND rating_date >= %s
					AND rating_date <= %s
					AND rating_value < 3",
					$kb_id,
					$start_date_str,
					$end_date_str
				) );

				$weekly_data[] = array(
					'week_label'       => $current_week->format( 'M j, Y' ),
					'positive_ratings' => $positive_count ? (int) $positive_count : 0,
					'negative_ratings' => $negative_count ? (int) $negative_count : 0,
				);

				$current_week->modify( '+1 week' );
			}
		} else {
			// Use the default weeks_back behavior
			// Exclude current week (start from week 1 instead of week 0)
			$weekly_data = array();

			// Initialize data for each week, excluding the current week
			for ( $i = $weeks_back; $i >= 1; $i-- ) {
				$week_date = clone $now;
				$week_date->modify( "-{$i} weeks" );

				// Get start and end of this week
				$week_start = clone $week_date;
				$day_of_week = (int) $week_start->format( 'w' );
				$week_start->modify( '-' . $day_of_week . ' days' );
				$week_start->setTime( 0, 0, 0 );

				$week_end = clone $week_start;
				$week_end->modify( '+6 days' );
				$week_end->setTime( 23, 59, 59 );

				// Get ratings count for this week
				$start_date_str = $week_start->format( 'Y-m-d H:i:s' );
				$end_date_str = $week_end->format( 'Y-m-d H:i:s' );

				// Count positive ratings (rating_value >= 3)
				$positive_count = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name
					WHERE kb_id = %d
					AND rating_date >= %s
					AND rating_date <= %s
					AND rating_value >= 3",
					$kb_id,
					$start_date_str,
					$end_date_str
				) );

				// Count negative ratings (rating_value < 3)
				$negative_count = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name
					WHERE kb_id = %d
					AND rating_date >= %s
					AND rating_date <= %s
					AND rating_value < 3",
					$kb_id,
					$start_date_str,
					$end_date_str
				) );

				$weekly_data[] = array(
					'week_label'       => $week_date->format( 'M j, Y' ),
					'positive_ratings' => $positive_count ? (int) $positive_count : 0,
					'negative_ratings' => $negative_count ? (int) $negative_count : 0,
				);
			}
		}

		return $weekly_data;
	}

	/**
	 * Get period comparison data (this period vs previous period)
	 *
	 * @param int $kb_id
	 * @param string $period 'week', 'month', or 'year'
	 * @return array Array with 'current_period', 'previous_period', 'change_percent'
	 */
	private function get_period_comparison_data( $kb_id, $period = 'month' ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_period_comparison_data( $period );
		}

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}

		// Calculate date ranges
		if ( $period === 'week' ) {
			$current_day_of_month = (int) $now->format( 'd' );

			// If today is the 1st, compare last 2 complete weeks
			if ( $current_day_of_month === 1 ) {
				$day_of_week = (int) $now->format( 'N' ); // 1 (Monday) through 7 (Sunday)
				$days_since_monday = $day_of_week - 1;

				// Start of current week
				$current_week_start = clone $now;
				$current_week_start->modify( '-' . $days_since_monday . ' days' );
				$current_week_start->setTime( 0, 0, 0 );

				// Last week (complete): 7 days ending just before current week
				$current_end = clone $current_week_start;
				$current_end->modify( '-1 second' );

				$current_start = clone $current_week_start;
				$current_start->modify( '-7 days' );

				// Week before last (complete): 7 days ending just before last week
				$previous_end = clone $current_start;
				$previous_end->modify( '-1 second' );

				$previous_start = clone $current_start;
				$previous_start->modify( '-7 days' );

				$current_label = 'Last Week';
				$previous_label = 'Week Before Last';
			} else {
				// This week (Monday-based) - NOT including today, and NOT including days from previous month
				$day_of_week = (int) $now->format( 'N' ); // 1 (Monday) through 7 (Sunday)
				$days_since_monday = $day_of_week - 1; // 0 if Monday, 6 if Sunday

				$week_start = clone $now;
				$week_start->modify( '-' . $days_since_monday . ' days' );
				$week_start->setTime( 0, 0, 0 );

				// Start of current month
				$month_start = clone $now;
				$month_start->modify( 'first day of this month' );
				$month_start->setTime( 0, 0, 0 );

				// Current period starts at the later of: week start or month start
				$current_start = $week_start < $month_start ? $month_start : $week_start;

				// End is yesterday (excluding today)
				$current_end = clone $now;
				$current_end->modify( '-1 day' );
				$current_end->setTime( 23, 59, 59 );

				// Calculate number of complete days in current period
				$current_days = $current_start->diff( $current_end )->days + 1;

				// Last week - same number of complete days, ending on the same day of week as yesterday
				$previous_end = clone $current_end;
				$previous_end->modify( '-7 days' );

				$previous_start = clone $previous_end;
				$previous_start->modify( '-' . ( $current_days - 1 ) . ' days' );
				$previous_start->setTime( 0, 0, 0 );

				// Generate labels with day range if less than 7 days
				if ( $current_days < 7 ) {
					$current_start_day = $current_start->format( 'D' );
					$current_end_day = $current_end->format( 'D' );
					$current_label = 'This Week (' . $current_start_day . '-' . $current_end_day . ')';
					$previous_label = 'Last Week (' . $current_start_day . '-' . $current_end_day . ')';
				} else {
					$current_label = 'This Week';
					$previous_label = 'Last Week';
				}
			}
		} elseif ( $period === 'month' ) {
			$current_day_of_month = (int) $now->format( 'd' );

			// If today is the 1st, compare last 2 complete months
			if ( $current_day_of_month === 1 ) {
				// Last month (complete)
				$current_start = clone $now;
				$current_start->modify( 'first day of last month' );
				$current_start->setTime( 0, 0, 0 );

				$current_end = clone $now;
				$current_end->modify( 'last day of last month' );
				$current_end->setTime( 23, 59, 59 );

				// Month before last (complete)
				$previous_start = clone $now;
				$previous_start->modify( 'first day of last month' );
				$previous_start->modify( '-1 month' );
				$previous_start->setTime( 0, 0, 0 );

				$previous_end = clone $now;
				$previous_end->modify( 'last day of last month' );
				$previous_end->modify( '-1 month' );
				$previous_end->setTime( 23, 59, 59 );

				$current_label = 'Last Month';
				$previous_label = 'Month Before Last';
			} else {
				// This month - NOT including today
				$current_start = clone $now;
				$current_start->modify( 'first day of this month' );
				$current_start->setTime( 0, 0, 0 );

				// End is yesterday (excluding today)
				$current_end = clone $now;
				$current_end->modify( '-1 day' );
				$current_end->setTime( 23, 59, 59 );

				// Last month - same number of complete days (if today is 15th, compare 1st-14th of both months)
				$previous_start = clone $now;
				$previous_start->modify( 'first day of last month' );
				$previous_start->setTime( 0, 0, 0 );

				$previous_end = clone $previous_start;
				$previous_end->modify( '+' . ( $current_day_of_month - 2 ) . ' days' );
				$previous_end->setTime( 23, 59, 59 );

				$current_label = 'This Month';
				$previous_label = 'Last Month';
			}
		} else {
			// This year - NOT including today
			$current_start = clone $now;
			$current_start->modify( 'first day of January ' . $now->format( 'Y' ) );
			$current_start->setTime( 0, 0, 0 );

			// End is yesterday (excluding today)
			$current_end = clone $now;
			$current_end->modify( '-1 day' );
			$current_end->setTime( 23, 59, 59 );

			// Last year - same number of complete days
			$current_day_of_year = (int) $now->format( 'z' ); // Day of year (0-indexed: Jan 1 = 0)
			$last_year = $now->format( 'Y' ) - 1;
			$previous_start = EPKB_Utilities::create_datetime( 'first day of January ' . $last_year );
			if ( $previous_start === null ) {
				return array();
			}
			$previous_start->setTime( 0, 0, 0 );

			$previous_end = clone $previous_start;
			$previous_end->modify( '+' . ( $current_day_of_year - 1 ) . ' days' );
			$previous_end->setTime( 23, 59, 59 );

			$current_label = 'This Year';
			$previous_label = 'Last Year';
		}

		$good_post_status = EPKB_Utilities::is_amag_on() ? 'private' : 'publish';
		$all_articles = get_posts( [
			'post_type'      => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status'    => $good_post_status,
			'posts_per_page' => -1,
		] );

		$current_views = 0;
		$previous_views = 0;

		foreach ( $all_articles as $article ) {
			$current_views += self::get_article_views_in_range( $article->ID, array(
				'start' => $current_start->getTimestamp(),
				'end'   => $current_end->getTimestamp(),
			) );

			$previous_views += self::get_article_views_in_range( $article->ID, array(
				'start' => $previous_start->getTimestamp(),
				'end'   => $previous_end->getTimestamp(),
			) );
		}

		// Calculate percentage change
		$change_percent = 0;
		if ( $previous_views > 0 ) {
			$change_percent = ( ( $current_views - $previous_views ) / $previous_views ) * 100;
		} elseif ( $current_views > 0 ) {
			$change_percent = 100;
		}

		return array(
			'current_period'    => array(
				'label' => $current_label,
				'views' => $current_views,
			),
			'previous_period'   => array(
				'label' => $previous_label,
				'views' => $previous_views,
			),
			'change_percent'    => round( $change_percent, 1 ),
			'is_positive'       => $change_percent >= 0,
		);
	}

	/**
	 * Get day-of-week pattern data for heatmap
	 *
	 * @param int $kb_id
	 * @param int $weeks_back Number of weeks to analyze (default: 12)
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array with day-of-week averages
	 */
	private function get_day_of_week_data( $kb_id, $weeks_back = 12, $date_range = null ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_day_of_week_data();
		}

		$good_post_status = EPKB_Utilities::is_amag_on() ? 'private' : 'publish';
		$all_articles = get_posts( [
			'post_type'      => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status'    => $good_post_status,
			'posts_per_page' => -1,
		] );

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}

		// Initialize day counts starting with Monday (0=Monday through 6=Sunday)
		$day_data = array(
			0 => array( 'label' => 'Monday', 'total_views' => 0, 'week_count' => 0 ),
			1 => array( 'label' => 'Tuesday', 'total_views' => 0, 'week_count' => 0 ),
			2 => array( 'label' => 'Wednesday', 'total_views' => 0, 'week_count' => 0 ),
			3 => array( 'label' => 'Thursday', 'total_views' => 0, 'week_count' => 0 ),
			4 => array( 'label' => 'Friday', 'total_views' => 0, 'week_count' => 0 ),
			5 => array( 'label' => 'Saturday', 'total_views' => 0, 'week_count' => 0 ),
			6 => array( 'label' => 'Sunday', 'total_views' => 0, 'week_count' => 0 ),
		);

		// If date range is provided, calculate weeks within that range
		if ( ! empty( $date_range ) ) {
			$start_date = EPKB_Utilities::create_datetime( '@' . $date_range['start'] );
			$end_date = EPKB_Utilities::create_datetime( '@' . $date_range['end'] );

			if ( $start_date === null || $end_date === null ) {
				return array();
			}

			$timezone = wp_timezone();
			$start_date->setTimezone( $timezone );
			$end_date->setTimezone( $timezone );

			$current_week = clone $start_date;
			$current_week->modify( 'this week' ); // Start of the week

			while ( $current_week <= $end_date ) {
				$year = (int) $current_week->format( 'Y' );
				$week = (int) $current_week->format( 'W' );

				$week_views = 0;
				foreach ( $all_articles as $article ) {
					$year_meta = EPKB_Utilities::get_postmeta( $article->ID, 'epkb-article-views-' . $year, array() );
					if ( isset( $year_meta[ $week ] ) && is_numeric( $year_meta[ $week ] ) ) {
						$week_views += (int) $year_meta[ $week ];
					}
				}

				// Distribute views across the week (simplified approximation)
				$avg_daily_views = $week_views / 7;
				for ( $day = 0; $day < 7; $day++ ) {
					$day_data[ $day ]['total_views'] += $avg_daily_views;
					$day_data[ $day ]['week_count']++;
				}

				$current_week->modify( '+1 week' );
			}
		} else {
			// Use the default weeks_back behavior
			// For simplicity, we'll approximate by dividing weekly views by 7
			// A more accurate implementation would require day-level tracking
			for ( $i = $weeks_back - 1; $i >= 0; $i-- ) {
				$week_date = clone $now;
				$week_date->modify( "-{$i} weeks" );
				$year = (int) $week_date->format( 'Y' );
				$week = (int) $week_date->format( 'W' );

				$week_views = 0;
				foreach ( $all_articles as $article ) {
					$year_meta = EPKB_Utilities::get_postmeta( $article->ID, 'epkb-article-views-' . $year, array() );
					if ( isset( $year_meta[ $week ] ) && is_numeric( $year_meta[ $week ] ) ) {
						$week_views += (int) $year_meta[ $week ];
					}
				}

				// Distribute views across the week (simplified approximation)
				$avg_daily_views = $week_views / 7;
				for ( $day = 0; $day < 7; $day++ ) {
					$day_data[ $day ]['total_views'] += $avg_daily_views;
					$day_data[ $day ]['week_count']++;
				}
			}
		}

		// Calculate averages
		foreach ( $day_data as $day => &$data ) {
			$data['avg_views'] = $data['week_count'] > 0 ? round( $data['total_views'] / $data['week_count'], 1 ) : 0;
		}

		return array_values( $day_data );
	}

	/**
	 * Get article engagement distribution data - shows how views are spread across articles
	 *
	 * @param int $kb_id
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array with distribution segments
	 */
	private function get_article_engagement_distribution_data( $kb_id, $date_range = null ) {

		$good_post_status = EPKB_Utilities::is_amag_on() ? 'private' : 'publish';
		$all_articles = get_posts( [
			'post_type'      => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status'    => $good_post_status,
			'posts_per_page' => -1,
		] );

		// Use demo data if no articles or very few articles
		if ( empty( $all_articles ) || count( $all_articles ) < 5 ) {
			return EPKB_KB_Demo_Data::get_demo_engagement_distribution_data();
		}

		// Initialize distribution buckets
		$distribution = array(
			'zero'       => array( 'label' => '0 Views', 'count' => 0 ),
			'very_low'   => array( 'label' => '1-10 Views', 'count' => 0 ),
			'low'        => array( 'label' => '11-50 Views', 'count' => 0 ),
			'medium'     => array( 'label' => '51-100 Views', 'count' => 0 ),
			'high'       => array( 'label' => '101-500 Views', 'count' => 0 ),
			'very_high'  => array( 'label' => '500+ Views', 'count' => 0 ),
		);

		// Categorize each article based on its view count
		foreach ( $all_articles as $article ) {
			$views = self::get_article_views_in_range( $article->ID, $date_range );

			if ( $views === 0 ) {
				$distribution['zero']['count']++;
			} elseif ( $views <= 10 ) {
				$distribution['very_low']['count']++;
			} elseif ( $views <= 50 ) {
				$distribution['low']['count']++;
			} elseif ( $views <= 100 ) {
				$distribution['medium']['count']++;
			} elseif ( $views <= 500 ) {
				$distribution['high']['count']++;
			} else {
				$distribution['very_high']['count']++;
			}
		}

		// Remove empty segments and format for chart
		$chart_data = array();
		foreach ( $distribution as $segment ) {
			if ( $segment['count'] > 0 ) {
				$chart_data[] = $segment;
			}
		}

		// If we only have 1-2 segments, the chart isn't very useful - show demo data instead
		if ( count( $chart_data ) < 3 ) {
			return EPKB_KB_Demo_Data::get_demo_engagement_distribution_data();
		}

		return $chart_data;
	}

	/**
	 * Get growth rate metrics
	 *
	 * @param int $kb_id
	 * @return array Array with growth metrics
	 */
	private function get_growth_rate_data( $kb_id ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_growth_rate_data();
		}

		$weekly_comparison = $this->get_period_comparison_data( $kb_id, 'week' );
		$monthly_comparison = $this->get_period_comparison_data( $kb_id, 'month' );
		$yearly_comparison = $this->get_period_comparison_data( $kb_id, 'year' );

		return array(
			'weekly'  => $weekly_comparison,
			'monthly' => $monthly_comparison,
			'yearly'  => $yearly_comparison,
		);
	}

	/**
	 * Get articles with zero engagement (no views) in the selected period
	 *
	 * @param int $kb_id
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array of articles with zero views
	 */
	private function get_zero_engagement_articles( $kb_id, $date_range = null ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_zero_engagement_articles();
		}

		$good_post_status = EPKB_Utilities::is_amag_on() ? 'private' : 'publish';
		$all_articles = get_posts( [
			'post_type'      => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status'    => $good_post_status,
			'posts_per_page' => -1,
		] );

		$zero_engagement_articles = array();

		foreach ( $all_articles as $article ) {
			$views = self::get_article_views_in_range( $article->ID, $date_range );

			if ( $views === 0 ) {
				$post_title = empty( $article->post_title ) ? '<unknown>' : $article->post_title;
				$link = get_permalink( $article->ID );
				$link = empty( $link ) || is_wp_error( $link ) ? '' : $link;

				$zero_engagement_articles[] = array(
					'title' => '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $post_title ) . '</a>',
					'views' => 0,
				);
			}
		}

		return $zero_engagement_articles;
	}

	/**
	 * Detect outlier articles (significantly above or below average)
	 *
	 * @param int $kb_id
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array with 'high_performers' and 'low_performers'
	 */
	private function get_outlier_articles( $kb_id, $date_range = null ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_outlier_articles();
		}

		$good_post_status = EPKB_Utilities::is_amag_on() ? 'private' : 'publish';
		$all_articles = get_posts( [
			'post_type'      => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status'    => $good_post_status,
			'posts_per_page' => -1,
		] );

		$articles_data = array();
		$total_views = 0;

		// Collect views for all articles
		foreach ( $all_articles as $article ) {
			$views = self::get_article_views_in_range( $article->ID, $date_range );
			$total_views += $views;

			$post_title = empty( $article->post_title ) ? '<unknown>' : $article->post_title;
			$link = get_permalink( $article->ID );
			$link = empty( $link ) || is_wp_error( $link ) ? '' : $link;

			$articles_data[] = array(
				'id'     => $article->ID,
				'title'  => '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $post_title ) . '</a>',
				'views'  => $views,
			);
		}

		// Return empty if no articles
		if ( empty( $articles_data ) ) {
			return array(
				'high_performers' => array(),
				'low_performers'  => array(),
			);
		}

		// Calculate mean and standard deviation
		$count = count( $articles_data );
		$mean = $total_views / $count;

		$variance = 0;
		foreach ( $articles_data as $article ) {
			$variance += pow( $article['views'] - $mean, 2 );
		}
		$std_dev = sqrt( $variance / $count );

		// Identify outliers (more than 2 standard deviations from mean)
		$high_performers = array();
		$low_performers = array();

		foreach ( $articles_data as $article ) {
			$z_score = $std_dev > 0 ? ( $article['views'] - $mean ) / $std_dev : 0;

			if ( $z_score > 2 ) {
				$high_performers[] = array(
					'title'   => $article['title'],
					'views'   => $article['views'],
					'z_score' => round( $z_score, 2 ),
				);
			} elseif ( $z_score < -1 && $article['views'] > 0 ) {
				// Only include articles with some views as low performers
				$low_performers[] = array(
					'title'   => $article['title'],
					'views'   => $article['views'],
					'z_score' => round( $z_score, 2 ),
				);
			}
		}

		// Sort by z-score
		usort( $high_performers, function( $a, $b ) {
			return $b['z_score'] <=> $a['z_score'];
		} );

		usort( $low_performers, function( $a, $b ) {
			return $a['z_score'] <=> $b['z_score'];
		} );

		// Limit to top 50 each
		$high_performers = array_slice( $high_performers, 0, 50 );
		$low_performers = array_slice( $low_performers, 0, 50 );

		return array(
			'high_performers' => $high_performers,
			'low_performers'  => $low_performers,
			'mean'            => round( $mean, 1 ),
			'std_dev'         => round( $std_dev, 1 ),
		);
	}

	/**
	 * Get most improved articles (biggest gainers in selected period vs previous period)
	 *
	 * @param int $kb_id
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array of articles with improvement data
	 */
	private function get_most_improved_articles( $kb_id, $date_range = null ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			return EPKB_KB_Demo_Data::get_demo_most_improved_articles();
		}

		// If no date range, compare last month vs previous month
		if ( empty( $date_range ) ) {
			$now = EPKB_Utilities::create_datetime();
			if ( $now === null ) {
				return array();
			}

			// Last month
			$current_start = clone $now;
			$current_start->modify( 'first day of last month' );
			$current_start->setTime( 0, 0, 0 );
			$current_end = clone $now;
			$current_end->modify( 'last day of last month' );
			$current_end->setTime( 23, 59, 59 );

			$date_range = array(
				'start' => $current_start->getTimestamp(),
				'end'   => $current_end->getTimestamp(),
			);
		}

		// Calculate previous period (same duration as current period)
		$duration = $date_range['end'] - $date_range['start'];
		$previous_range = array(
			'start' => $date_range['start'] - $duration,
			'end'   => $date_range['start'] - 1,
		);

		$good_post_status = EPKB_Utilities::is_amag_on() ? 'private' : 'publish';
		$all_articles = get_posts( [
			'post_type'      => EPKB_KB_Handler::get_post_type( $kb_id ),
			'post_status'    => $good_post_status,
			'posts_per_page' => -1,
		] );

		$improved_articles = array();

		foreach ( $all_articles as $article ) {
			$current_views = self::get_article_views_in_range( $article->ID, $date_range );
			$previous_views = self::get_article_views_in_range( $article->ID, $previous_range );

			// Calculate improvement (absolute and percentage)
			$absolute_change = $current_views - $previous_views;
			$percent_change = 0;

			if ( $previous_views > 0 ) {
				$percent_change = ( $absolute_change / $previous_views ) * 100;
			} elseif ( $current_views > 0 ) {
				$percent_change = 100;
			}

			// Only include articles with positive improvement
			if ( $absolute_change > 0 ) {
				$post_title = empty( $article->post_title ) ? '<unknown>' : $article->post_title;
				$link = get_permalink( $article->ID );
				$link = empty( $link ) || is_wp_error( $link ) ? '' : $link;

				$improved_articles[] = array(
					'title'           => '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $post_title ) . '</a>',
					'current_views'   => $current_views,
					'previous_views'  => $previous_views,
					'absolute_change' => $absolute_change,
					'percent_change'  => round( $percent_change, 1 ),
				);
			}
		}

		// Sort by absolute change (biggest gainers first)
		usort( $improved_articles, function( $a, $b ) {
			return $b['absolute_change'] - $a['absolute_change'];
		} );

		// Limit to top 100
		return array_slice( $improved_articles, 0, 100 );
	}

	/**
	 * Provide simplified analytics sections for embedding on the Content Analysis page.
	 *
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array[]
	 */
	public function get_compact_sections( $date_range = null ) {

		if ( empty( $this->kb_config ) ) {
			$this->kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
		}

		$views = $this->get_regular_views_config( $date_range );

		$sections = array(
			'article-views' => array(
				'title'   => esc_html__( 'Article Views', 'echo-knowledge-base' ),
				'content' => '',
			),
			'rating' => array(
				'title'   => esc_html__( 'Article Ratings', 'echo-knowledge-base' ),
				'content' => '',
			),
			'all-data' => array(
				'title'   => esc_html__( 'All Search Data', 'echo-knowledge-base' ),
				'content' => '',
			),
			'kb-search' => array(
				'title'   => esc_html__( 'KB Searches', 'echo-knowledge-base' ),
				'content' => '',
			),
			'search-shortcode' => array(
				'title'   => esc_html__( 'Search Shortcode', 'echo-knowledge-base' ),
				'content' => '',
			),
			'widgets' => array(
				'title'   => esc_html__( 'Search Widgets', 'echo-knowledge-base' ),
				'content' => '',
			),
			'time-based-analytics' => array(
				'title'   => esc_html__( 'Time-Based Analytics', 'echo-knowledge-base' ),
				'content' => '',
			),
		);
		$article_views_enabled = $this->kb_config['article_views_counter_enable'] === 'on';
		$kb_id = (int) $this->kb_config['id'];


		foreach ( $views as $view ) {

			if ( empty( $view['list_key'] ) ) {
				continue;
			}

			$view_html = $this->get_view_boxes_html( $view );
			if ( empty( $view_html ) ) {
				continue;
			}

			switch ( $view['list_key'] ) {
				case 'all-data':
					$sections['all-data']['content'] .= $view_html;
					break;
				case 'kb-search':
					$sections['kb-search']['content'] .= $view_html;
					break;
				case 'search-shortcode':
					$sections['search-shortcode']['content'] .= $view_html;
					break;
				case 'widgets':
					$sections['widgets']['content'] .= $view_html;
					break;
				case 'kb-article-views':
					$sections['article-views']['content'] .= $view_html;
					break;
				case 'rating-data':
					$sections['rating']['content'] .= $view_html;
					break;
				default:
					break;
			}
		}

		// Empty state for individual search sections
		if ( empty( $sections['all-data']['content'] ) ) {
			unset( $sections['all-data'] );
		}
		if ( empty( $sections['kb-search']['content'] ) ) {
			unset( $sections['kb-search'] );
		}
		if ( empty( $sections['search-shortcode']['content'] ) ) {
			unset( $sections['search-shortcode'] );
		}
		if ( empty( $sections['widgets']['content'] ) ) {
			unset( $sections['widgets'] );
		}

		if ( empty( $sections['article-views']['content'] ) ) {
			if ( $article_views_enabled ) {
				$message = $date_range
					? esc_html__( 'No article views data available for the selected date range.', 'echo-knowledge-base' )
					: esc_html__( 'No article views data available.', 'echo-knowledge-base' );
				$sections['article-views']['content'] = $this->get_empty_state_box( $message );
			} else {
				$sections['article-views']['content'] = $this->get_article_views_disabled_notice( $kb_id );
			}
		}

		// Only show empty state if EPRF is enabled but has no data
		// If EPRF is not enabled, the ad box is shown instead (added as a view above)
		if ( empty( $sections['rating']['content'] ) && EPKB_Utilities::is_article_rating_enabled() ) {
			$message = $date_range
				? esc_html__( 'Ratings data is unavailable for the selected date range.', 'echo-knowledge-base' )
				: esc_html__( 'Ratings data is unavailable.', 'echo-knowledge-base' );
			$sections['rating']['content'] = $this->get_empty_state_box( $message );
		}

		// Populate Time-Based Analytics section
		$sections['time-based-analytics']['content'] = $this->get_time_based_analytics_html( $date_range );

		// Allow plugins to add/modify analytics sections
		$sections = apply_filters( 'epkb_analytics_sections', $sections, $date_range );

		return $sections;
	}

	/**
	 * Output markup that encourages enabling the article views counter.
	 *
	 * @param int $kb_id Knowledge base ID.
	 * @return string
	 */
	private function get_article_views_disabled_notice( $kb_id ) {

		ob_start();	?>

		<div class="epkb-analytics-empty-card epkb-analytics-article-views-disabled"
			 data-kb-id="<?php echo esc_attr( $kb_id ); ?>"
			 data-enabling-message="<?php echo esc_attr( esc_html__( 'Enabling article views counter...', 'echo-knowledge-base' ) ); ?>"
			 data-success-message="<?php echo esc_attr( esc_html__( 'Article views counter enabled. Reloading...', 'echo-knowledge-base' ) ); ?>"
			 data-error-message="<?php echo esc_attr( esc_html__( 'Unable to update article views counter. Please try again.', 'echo-knowledge-base' ) ); ?>">
			<h3><?php echo esc_html__( 'Article view tracking is off.', 'echo-knowledge-base' ); ?></h3>
			<p><?php echo esc_html__( 'Enable the Article Views counter to start collecting data and unlock engagement insights.', 'echo-knowledge-base' ); ?></p>
			<div class="epkb-article-views-toggle">
				<label class="epkb-article-views-toggle__label">
					<span class="epkb-article-views-toggle__text"><?php echo esc_html__( 'Enable Article Views Counter', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-article-views-toggle__control">
						<input type="checkbox" class="epkb-article-views-toggle__input" />
						<span class="epkb-article-views-toggle__slider" aria-hidden="true"></span>
					</span>
				</label>
				<p class="epkb-article-views-toggle__hint"><?php echo esc_html__( 'Turn on tracking to populate analytics with article view data.', 'echo-knowledge-base' ); ?></p>
				<div class="epkb-article-views-toggle__status" aria-live="polite"></div>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Build combined HTML for a view's boxes.
	 *
	 * @param array $view View configuration array.
	 * @return string
	 */
	private function get_view_boxes_html( $view ) {

		ob_start();

		// Process direct boxes_list
		if ( ! empty( $view['boxes_list'] ) && is_array( $view['boxes_list'] ) ) {
			foreach ( $view['boxes_list'] as $box ) {
				if ( empty( $box['html'] ) ) {
					continue;
				}

				// Output the HTML directly - it already includes the card structure
				echo $box['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		// Process secondary_tabs (for Advanced Search)
		if ( ! empty( $view['secondary_tabs'] ) && is_array( $view['secondary_tabs'] ) ) {
			foreach ( $view['secondary_tabs'] as $secondary_tab ) {
				if ( empty( $secondary_tab['boxes_list'] ) || ! is_array( $secondary_tab['boxes_list'] ) ) {
					continue;
				}

				foreach ( $secondary_tab['boxes_list'] as $box ) {
					if ( empty( $box['html'] ) ) {
						continue;
					}

					// Output the HTML directly - it already includes the card structure
					echo $box['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}

		return ob_get_clean();
	}

	/**
	 * Simple empty state container used when analytics data is missing.
	 *
	 * @param string $title       Empty state title.
	 * @param string $description Optional description.
	 * @return string
	 */
	private function get_empty_state_box( $title, $description = '' ) {

		ob_start();
		?>
		<div class="epkb-analytics-empty-card">
			<h3><?php echo esc_html( $title ); ?></h3>
			<?php if ( ! empty( $description ) ) : ?>
				<p><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Show KB core statistics
	 *
	 * @param $kb_id
	 */
	private function display_core_content_analytics( $kb_id ) {

		$all_kb_terms      = EPKB_Core_Utilities::get_kb_categories_unfiltered( $kb_id );
		$nof_kb_categories = $all_kb_terms === null ? 'unknown' : count( $all_kb_terms );
		$nof_kb_articles   = EPKB_Articles_DB::get_count_of_all_kb_articles( $kb_id );  ?>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php esc_html_e( 'Categories', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo esc_html( $nof_kb_categories ); ?></div>
				<div class="widget-desc"><?php esc_html_e( 'Categories help you to organize articles into groups and hierarchies.', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="widget-toggle"><?php
				$url = admin_url('edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) .'&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ));  ?>
				<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php esc_html_e( 'View Categories', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php esc_html_e( 'Articles', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo esc_html( $nof_kb_articles ); ?></div>
				<div class="widget-desc"><?php esc_html_e( 'Article belongs to one or more categories or sub-categories.', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="widget-toggle">
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id )) ); ?>" target="_blank"><?php esc_html_e( 'View Articles', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>	<?php
	}

	/**
	 * Show KB core statistics
	 *
	 * @param int $kb_id
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 */
	private function display_article_views_analytics( $kb_id, $date_range = null ) {

		// Check if we should use demo data
		$is_demo_data = EPKB_KB_Demo_Data::is_demo_data( $kb_id );

		if ( $is_demo_data ) {
			// Use demo data for article views
			$demo_articles = EPKB_KB_Demo_Data::get_demo_most_viewed_articles( 100 );
			$articles_with_views = array();
			foreach ( $demo_articles as $demo_article ) {
				$articles_with_views[] = array(
					'title' => esc_html( $demo_article['title'] ),
					'views' => $demo_article['views'],
				);
			}
		} else {
			$good_post_status = EPKB_Utilities::is_amag_on() ? 'private' :'publish';

			// Get all articles first
			$all_articles = get_posts( [
				'post_type'      => EPKB_KB_Handler::get_post_type( $kb_id ),
				'post_status'    => $good_post_status,
				'posts_per_page' => -1, // Get all articles
			] );

			// Calculate views for each article within date range
			$articles_with_views = array();
			foreach ( $all_articles as $post ) {
				if ( $post->post_status != $good_post_status ) {
					continue;
				}

				$views = self::get_article_views_in_range( $post->ID, $date_range );

				// Only include articles with views
				if ( $views > 0 ) {
					$post_title = empty( $post->post_title ) ? '<unknown>' : $post->post_title;
					$link = get_permalink( $post->ID );
					$link = empty( $link ) || is_wp_error( $link ) ? '' : $link;

					$articles_with_views[] = array(
						'title' => '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $post_title ) . '</a>',
						'views' => $views,
					);
				}
			}
		}

		// If no articles have views, return early - empty state will be shown by get_compact_sections()
		if ( empty( $articles_with_views ) ) {
			return;
		}

		// Sort by views
		usort( $articles_with_views, function( $a, $b ) {
			return $b['views'] - $a['views'];
		} );

		// MOST RATED ARTICLES (top 100)
		$most_rated_articles_data = array();
		$most_viewed = array_slice( $articles_with_views, 0, 100 );
		foreach ( $most_viewed as $article ) {
			$most_rated_articles_data[] = array( $article['title'], $article['views'] );
		}

		// LEAST RATED ARTICLES (bottom 100)
		$least_rated_articles_data = array();
		$least_viewed = array_slice( array_reverse( $articles_with_views ), 0, 100 );
		foreach ( $least_viewed as $article ) {
			$least_rated_articles_data[] = array( $article['title'], $article['views'] );
		}

		// Start two-column grid wrapper with ID for JavaScript
		echo '<div id="epkb-article-views-data-content" class="epkb-time-based-analytics-container">';
		echo '<div class="epkb-time-based-analytics-row">';
		echo '<div class="epkb-time-based-analytics-column">';

		$this->pie_chart_data_box(
			__( 'Most Frequently Viewed Articles', 'echo-knowledge-base' ),
			__( 'Top 100 articles with the highest number of views', 'echo-knowledge-base' ),
			$most_rated_articles_data,
			'epkb-popular-articles',
			'No articles were viewed.'
		);

		echo '</div>';
		echo '<div class="epkb-time-based-analytics-column">';

		$this->pie_chart_data_box(
			__( 'Least Frequently Viewed Articles', 'echo-knowledge-base' ),
			__( 'Bottom 100 articles with the lowest number of views', 'echo-knowledge-base' ),
			$least_rated_articles_data,
			'epkb-not-popular-articles',
			'No articles were viewed.'
		);

		echo '</div>';
		echo '</div>';

		// OUTLIER DETECTION + ZERO ENGAGEMENT
		$outlier_data = $this->get_outlier_articles( $kb_id, $date_range );
		$zero_engagement_articles = $this->get_zero_engagement_articles( $kb_id, $date_range );
		$most_improved_articles = $this->get_most_improved_articles( $kb_id, $date_range );

		// Row with Most Improved and Zero Engagement
		if ( ! empty( $most_improved_articles ) || ! empty( $zero_engagement_articles ) ) {
			echo '<div class="epkb-time-based-analytics-row">';

			// Most Improved Articles (left)
			if ( ! empty( $most_improved_articles ) ) {
				echo '<div class="epkb-time-based-analytics-column">';

				$this->improvement_data_box(
					__( 'Most Improved Articles', 'echo-knowledge-base' ),
					__( 'Articles with the biggest gains compared to the previous period', 'echo-knowledge-base' ),
					$most_improved_articles,
					'epkb-most-improved-articles'
				);

				echo '</div>';
			}

			// Zero Engagement Articles (right)
			if ( ! empty( $zero_engagement_articles ) ) {
				echo '<div class="epkb-time-based-analytics-column">';

				$this->list_data_box(
					__( 'Zero Engagement Articles', 'echo-knowledge-base' ),
					__( 'Articles with no views in the selected period', 'echo-knowledge-base' ),
					$zero_engagement_articles,
					'epkb-zero-engagement-articles'
				);

				echo '</div>';
			}

			echo '</div>';
		}

		// Low Performers in separate row if exists
		if ( ! empty( $outlier_data['low_performers'] ) ) {
			echo '<div class="epkb-time-based-analytics-row">';
			echo '<div class="epkb-time-based-analytics-column epkb-time-based-analytics-column--full">';

			$low_performers_display = array();
			foreach ( $outlier_data['low_performers'] as $article ) {
				$low_performers_display[] = array( $article['title'], $article['views'] );
			}

			$this->pie_chart_data_box(
				__( 'Low Performing Articles', 'echo-knowledge-base' ),
				sprintf(
					// translators: %1$d is the mean value, %2$d is the standard deviation
					__( 'Articles performing significantly below average (Mean: %1$d, Std Dev: %2$d)', 'echo-knowledge-base' ),
					$outlier_data['mean'],
					$outlier_data['std_dev']
				),
				$low_performers_display,
				'epkb-low-performers',
				'No low performing outliers detected.'
			);

			echo '</div>';
			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Show KB core statistics
	 *
	 * @param $kb_id
	 */
	private function display_core_search_data( $kb_id ) {

		// Check if we should use demo data
		if ( EPKB_KB_Demo_Data::is_demo_data( $kb_id ) ) {
			$demo_stats = EPKB_KB_Demo_Data::get_demo_search_statistics();
			$user_search_total = isset( $demo_stats['total_searches'][1] ) ? $demo_stats['total_searches'][1] : 0;
			$user_search_not_found_count = isset( $demo_stats['total_no_results_searches'][1] ) ? $demo_stats['total_no_results_searches'][1] : 0;
			$user_search_found_count = $user_search_total - $user_search_not_found_count;
		} else {
			$user_search_found_count = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_hit_search_counter', 0 );
			$user_search_not_found_count = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_miss_search_counter', 0 );
			$user_search_total = $user_search_found_count + $user_search_not_found_count;
		}   ?>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php esc_html_e( 'Searches with Articles Found', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo esc_html( $user_search_found_count ); ?></div>
			</div>
			<div><?php esc_html_e( 'Are you interested in searched-for keywords?', 'echo-knowledge-base' ); ?></div>
			<br>
			<a href="https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/" target="_blank">Learn More</a>
		</div>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php esc_html_e( 'Searches with No Results', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo esc_html( $user_search_not_found_count ); ?></div>
			</div>
			<div><?php esc_html_e( 'Do you need to know what keywords were not found?', 'echo-knowledge-base' ); ?></div>
			<br>
			<a href="https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/" target="_blank">Learn More</a>

		</div>

		<div class="overview-info-widget overview-info-widget__content-center">
			<div class="widget-header"><h4><?php esc_html_e( 'Articles Found Success Rate', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo empty($user_search_total) ? 'N/A' : number_format( 100 * $user_search_found_count / $user_search_total, 0 ) . '%'; ?></div>
			</div>
		</div>  <?php
	}

	/**
	 * Calculate the appropriate time period text and trend type based on date range
	 *
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array Array with 'period' and 'trend_type' keys
	 */
	private function get_time_period_info( $date_range = null ) {

		// Default to 12 weeks if no date range
		if ( empty( $date_range ) ) {
			return array(
				'period' => __( '12 weeks', 'echo-knowledge-base' ),
				'trend_type' => __( 'weekly', 'echo-knowledge-base' )
			);
		}

		$start_date = EPKB_Utilities::create_datetime( '@' . $date_range['start'] );
		$end_date = EPKB_Utilities::create_datetime( '@' . $date_range['end'] );

		if ( $start_date === null || $end_date === null ) {
			return array(
				'period' => __( '12 weeks', 'echo-knowledge-base' ),
				'trend_type' => __( 'weekly', 'echo-knowledge-base' )
			);
		}

		$timezone = wp_timezone();
		$start_date->setTimezone( $timezone );
		$end_date->setTimezone( $timezone );

		// Calculate the difference in days
		$diff_days = $start_date->diff( $end_date )->days;

		// Handle single day
		if ( $diff_days <= 1 ) {
			return array(
				'period' => __( '1 day', 'echo-knowledge-base' ),
				'trend_type' => __( 'daily', 'echo-knowledge-base' )
			);
		}

		// Handle multiple days but less than a week
		if ( $diff_days < 7 ) {
			return array(
				'period' => ( $diff_days + 1 ) . ' ' . esc_html__( 'days', 'echo-knowledge-base' ),
				'trend_type' => __( 'daily', 'echo-knowledge-base' )
			);
		}

		// Calculate weeks within the date range
		$current_week = clone $start_date;
		$current_week->modify( 'this week' ); // Start of the week
		$week_count = 0;

		while ( $current_week <= $end_date ) {
			$week_count++;
			$current_week->modify( '+1 week' );
		}

		// Return appropriate text based on week count
		if ( $week_count <= 1 ) {
			return array(
				'period' => __( '1 week', 'echo-knowledge-base' ),
				'trend_type' => __( 'weekly', 'echo-knowledge-base' )
			);
		} else {
			return array(
				'period' => $week_count . ' ' . esc_html__( 'weeks', 'echo-knowledge-base' ),
				'trend_type' => __( 'weekly', 'echo-knowledge-base' )
			);
		}
	}

	/**
	 * Get HTML for Time-Based Analytics tab
	 *
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return string
	 */
	private function get_time_based_analytics_html( $date_range = null ) {

		$kb_id = $this->kb_config['id'];

		// Check if article views are enabled
		if ( $this->kb_config['article_views_counter_enable'] !== 'on' ) {
			return $this->get_article_views_disabled_notice( $kb_id );
		}

		// Get analytics data
		$weekly_data = $this->get_weekly_views_data( $kb_id, 12, $date_range );
		$growth_data = $this->get_growth_rate_data( $kb_id );
		$engagement_distribution_data = $this->get_article_engagement_distribution_data( $kb_id, $date_range );
		$weekly_searches_data = $this->get_weekly_searches_data( $kb_id, 12, $date_range );
		$weekly_ratings_data = $this->get_weekly_ratings_data( $kb_id, 12, $date_range );

		// Get dynamic time period info
		$time_period_info = $this->get_time_period_info( $date_range );

		// Check if there's any data
		$has_data = false;
		foreach ( $weekly_data as $week ) {
			if ( $week['total_views'] > 0 ) {
				$has_data = true;
				break;
			}
		}

		if ( ! $has_data ) {
			return $this->get_empty_state_box( esc_html__( 'No time-based analytics data available yet.', 'echo-knowledge-base' ), esc_html__( 'Start collecting article views to see trends over time.', 'echo-knowledge-base' ) );
		}

		ob_start();
		?>
		<div class="epkb-time-based-analytics-container">
			<div class="epkb-time-based-analytics-row">
				<!-- Left Column -->
				<div class="epkb-time-based-analytics-column">
					<!-- Views Over Time Chart -->
					<div class="epkb-analytics-card epkb-time-chart-card">
						<div class="epkb-analytics-card__header">
							<h3><?php echo esc_html__( 'Views Over Time', 'echo-knowledge-base' ); ?></h3>
							<?php // translators: %1$s is the trend type (e.g., "Weekly"), %2$s is the time period (e.g., "4 weeks") ?>
							<p class="epkb-analytics-card__desc"><?php printf( esc_html__( '%1$s trend of article views over the last %2$s', 'echo-knowledge-base' ), esc_html( ucfirst( $time_period_info['trend_type'] ) ), esc_html( $time_period_info['period'] ) ); ?></p>
						</div>
						<div class="epkb-analytics-card__body">
							<canvas id="epkb-time-chart" style="height: 300px;" data-weekly-data="<?php echo esc_attr( wp_json_encode( $weekly_data ) ); ?>"></canvas>
						</div>
					</div>
				</div>

				<!-- Right Column -->
				<div class="epkb-time-based-analytics-column">
					<!-- Article Engagement Distribution -->
					<div class="epkb-analytics-card epkb-engagement-distribution-card">
						<div class="epkb-analytics-card__header">
							<h3><?php echo esc_html__( 'Article Engagement Distribution', 'echo-knowledge-base' ); ?></h3>
							<p class="epkb-analytics-card__desc"><?php echo esc_html__( 'How views are distributed across your articles', 'echo-knowledge-base' ); ?></p>
						</div>
						<div class="epkb-analytics-card__body">
							<canvas id="epkb-engagement-distribution-chart" style="height: 300px;" data-distribution="<?php echo esc_attr( wp_json_encode( $engagement_distribution_data ) ); ?>"></canvas>
						</div>
					</div>
				</div>
			</div>

			<?php if ( ! empty( $weekly_ratings_data ) || ! empty( $weekly_searches_data ) ) : ?>
				<!-- Ratings and Searches Row -->
				<div class="epkb-time-based-analytics-row">
					<?php if ( ! empty( $weekly_ratings_data ) ) : ?>
						<div class="epkb-time-based-analytics-column">
							<!-- Ratings Over Time Chart -->
							<div class="epkb-analytics-card epkb-ratings-chart-card">
								<div class="epkb-analytics-card__header">
									<h3><?php echo esc_html__( 'Articles Rating Over Time', 'echo-knowledge-base' ); ?></h3>
									<?php // translators: %1$s is the trend type (e.g., "Weekly"), %2$s is the time period (e.g., "4 weeks") ?>
									<p class="epkb-analytics-card__desc"><?php printf( esc_html__( '%1$s trend of positive and negative feedback over the last %2$s', 'echo-knowledge-base' ), esc_html( ucfirst( $time_period_info['trend_type'] ) ), esc_html( $time_period_info['period'] ) ); ?></p>
								</div>
								<div class="epkb-analytics-card__body">
									<canvas id="epkb-ratings-chart" style="height: 300px;" data-ratings-data="<?php echo esc_attr( wp_json_encode( $weekly_ratings_data ) ); ?>"></canvas>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $weekly_searches_data ) ) : ?>
						<div class="epkb-time-based-analytics-column">
							<!-- Searches Over Time Chart -->
							<div class="epkb-analytics-card epkb-searches-chart-card">
								<div class="epkb-analytics-card__header">
									<h3><?php echo esc_html__( 'Searches Over Time', 'echo-knowledge-base' ); ?></h3>
									<?php // translators: %1$s is the trend type (e.g., "Weekly"), %2$s is the time period (e.g., "4 weeks") ?>
									<p class="epkb-analytics-card__desc"><?php printf( esc_html__( '%1$s trend of searches over the last %2$s', 'echo-knowledge-base' ), esc_html( ucfirst( $time_period_info['trend_type'] ) ), esc_html( $time_period_info['period'] ) ); ?></p>
								</div>
								<div class="epkb-analytics-card__body">
									<canvas id="epkb-searches-chart" style="height: 300px;" data-searches-data="<?php echo esc_attr( wp_json_encode( $weekly_searches_data ) ); ?>"></canvas>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<!-- Growth Rate Metrics (Full Width) -->
			<div class="epkb-time-based-analytics-row">
				<div class="epkb-time-based-analytics-column epkb-time-based-analytics-column--full">
					<div class="epkb-analytics-card epkb-growth-metrics-card">
						<div class="epkb-analytics-card__header">
							<h3><?php echo esc_html__( 'Views Growth Rate Metrics', 'echo-knowledge-base' ); ?></h3>
							<p class="epkb-analytics-card__desc"><?php echo esc_html__( 'Period-over-period comparison of article views', 'echo-knowledge-base' ); ?></p>
						</div>
						<div class="epkb-analytics-card__body">
							<div class="epkb-growth-metrics">
								<!-- Weekly Growth -->
								<div class="epkb-growth-metric">
									<div class="epkb-growth-metric__label"><?php echo esc_html__( 'Weekly Growth', 'echo-knowledge-base' ); ?></div>
									<div class="epkb-growth-metric__comparison">
										<div class="epkb-growth-metric__period">
											<span class="epkb-growth-metric__period-label"><?php echo esc_html( $growth_data['weekly']['previous_period']['label'] ); ?></span>
											<span class="epkb-growth-metric__period-value"><?php echo esc_html( number_format_i18n( $growth_data['weekly']['previous_period']['views'] ) ); ?></span>
										</div>
										<div class="epkb-growth-metric__vs">vs</div>
										<div class="epkb-growth-metric__period">
											<span class="epkb-growth-metric__period-label"><?php echo esc_html( $growth_data['weekly']['current_period']['label'] ); ?></span>
											<span class="epkb-growth-metric__period-value"><?php echo esc_html( number_format_i18n( $growth_data['weekly']['current_period']['views'] ) ); ?></span>
										</div>
									</div>
									<div class="epkb-growth-metric__change <?php echo $growth_data['weekly']['is_positive'] ? 'epkb-growth-positive' : 'epkb-growth-negative'; ?>">
										<span class="epkb-growth-metric__arrow"><?php echo $growth_data['weekly']['is_positive'] ? '▲' : '▼'; ?></span>
										<span class="epkb-growth-metric__percent"><?php echo esc_html( abs( $growth_data['weekly']['change_percent'] ) . '%' ); ?></span>
									</div>
								</div>

								<!-- Monthly Growth -->
								<div class="epkb-growth-metric">
									<div class="epkb-growth-metric__label"><?php echo esc_html__( 'Monthly Growth', 'echo-knowledge-base' ); ?></div>
									<div class="epkb-growth-metric__comparison">
										<div class="epkb-growth-metric__period">
											<span class="epkb-growth-metric__period-label"><?php echo esc_html( $growth_data['monthly']['previous_period']['label'] ); ?></span>
											<span class="epkb-growth-metric__period-value"><?php echo esc_html( number_format_i18n( $growth_data['monthly']['previous_period']['views'] ) ); ?></span>
										</div>
										<div class="epkb-growth-metric__vs">vs</div>
										<div class="epkb-growth-metric__period">
											<span class="epkb-growth-metric__period-label"><?php echo esc_html( $growth_data['monthly']['current_period']['label'] ); ?></span>
											<span class="epkb-growth-metric__period-value"><?php echo esc_html( number_format_i18n( $growth_data['monthly']['current_period']['views'] ) ); ?></span>
										</div>
									</div>
									<div class="epkb-growth-metric__change <?php echo $growth_data['monthly']['is_positive'] ? 'epkb-growth-positive' : 'epkb-growth-negative'; ?>">
										<span class="epkb-growth-metric__arrow"><?php echo $growth_data['monthly']['is_positive'] ? '▲' : '▼'; ?></span>
										<span class="epkb-growth-metric__percent"><?php echo esc_html( abs( $growth_data['monthly']['change_percent'] ) . '%' ); ?></span>
									</div>
								</div>

								<!-- Yearly Growth -->
								<div class="epkb-growth-metric">
									<div class="epkb-growth-metric__label"><?php echo esc_html__( 'Yearly Growth', 'echo-knowledge-base' ); ?></div>
									<div class="epkb-growth-metric__comparison">
										<div class="epkb-growth-metric__period">
											<span class="epkb-growth-metric__period-label"><?php echo esc_html( $growth_data['yearly']['previous_period']['label'] ); ?></span>
											<span class="epkb-growth-metric__period-value"><?php echo esc_html( number_format_i18n( $growth_data['yearly']['previous_period']['views'] ) ); ?></span>
										</div>
										<div class="epkb-growth-metric__vs">vs</div>
										<div class="epkb-growth-metric__period">
											<span class="epkb-growth-metric__period-label"><?php echo esc_html( $growth_data['yearly']['current_period']['label'] ); ?></span>
											<span class="epkb-growth-metric__period-value"><?php echo esc_html( number_format_i18n( $growth_data['yearly']['current_period']['views'] ) ); ?></span>
										</div>
									</div>
									<div class="epkb-growth-metric__change <?php echo $growth_data['yearly']['is_positive'] ? 'epkb-growth-positive' : 'epkb-growth-negative'; ?>">
										<span class="epkb-growth-metric__arrow"><?php echo $growth_data['yearly']['is_positive'] ? '▲' : '▼'; ?></span>
										<span class="epkb-growth-metric__percent"><?php echo esc_html( abs( $growth_data['yearly']['change_percent'] ) . '%' ); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get HTML for KB Stats box
	 *
	 * @return false|string
	 */
	private function get_kb_stats_box_html() {

		ob_start();     ?>

		<div class="eckb-config-content epkb-active-content" id="epkb-statistics-data-content">
			<div class="epkb-config-content-wrapper">
				<?php $this->display_core_content_analytics( $this->kb_config['id'] ); ?>
			</div>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get HTML for KB Stats box
	 *
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return false|string
	 */
	private function get_kb_views_box_html( $date_range = null ) {

		// Check if there's any data before generating wrapper HTML
		ob_start();
		$this->display_article_views_analytics( $this->kb_config['id'], $date_range );
		$content = ob_get_clean();

		// If no content, return empty string so empty state message shows
		if ( empty( trim( $content ) ) ) {
			return '';
		}

		// Return content directly without wrapper (cards are already wrapped)
		return $content;
	}

	/**
	 * Get HTML for Search Data box
	 *
	 * @return false|string
	 */
	private function get_search_data_box_html() {

		ob_start();     ?>

		<div class="eckb-config-content" id="epkb-search-data-content">
			<div class="epkb-config-content-wrapper">
				<?php $this->display_core_search_data( $this->kb_config['id'] ); ?>
			</div>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get HTML for Advanced Search add-on ad box
	 *
	 * @return false|string
	 */
	private static function get_asea_addon_ad_box_html() {

		return EPKB_HTML_Forms::advertisement_ad_box( array(
			'icon'              => 'epkbfa-linode',
			'title'             => __( 'Advanced Search Add-on', 'echo-knowledge-base' ),
			'img_url'           => 'https://www.echoknowledgebase.com/wp-content/uploads/2025/10/advanced-search-features.jpg',
			'desc'              => __( "Enhance users' search experience with advanced features and powerful customization options.", 'echo-knowledge-base' ),
			'list'              => array(
				__( 'Advanced search filters for categories, tags, and more', 'echo-knowledge-base' ),
				__( 'Search shortcuts for faster navigation', 'echo-knowledge-base' ),
				__( 'Customizable search box design and placement', 'echo-knowledge-base' )
			),
			'btn_text'          => __( 'Buy Now', 'echo-knowledge-base' ),
			'btn_url'           => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/',
			'btn_color'         => 'green',

			'more_info_text'    => __( 'More Information', 'echo-knowledge-base' ),
			'more_info_url'     => 'https://www.echoknowledgebase.com/documentation/advanced-search-overview/',
			'more_info_color'   => 'orange',
			'box_type'			=> 'new-feature',
			'return_html'       => true,
		) );
	}

	/**
	 * Get HTML for Article Rating and Feedback add-on ad box
	 *
	 * @return false|string
	 */
	private static function get_eprf_addon_ad_box_html() {

		return EPKB_HTML_Forms::advertisement_ad_box( array(
			'icon'              => 'epkbfa-thumbs-up',
			'title'             => __( 'Article Rating and Feedback Add-on', 'echo-knowledge-base' ),
			'img_url'           => 'https://www.echoknowledgebase.com/wp-content/uploads/2025/10/article-rating-feature.jpg',
			'desc'              => __( "Collect valuable user ratings and feedback to understand article quality and user satisfaction.", 'echo-knowledge-base' ),
			'list'              => array(
				__( 'Star ratings, thumbs up/down, and custom rating types', 'echo-knowledge-base' ),
				__( 'Collect detailed feedback and user comments', 'echo-knowledge-base' ),
				__( 'Customizable rating forms and display options', 'echo-knowledge-base' ),
				__( 'Email notifications for new feedback submissions', 'echo-knowledge-base' )
			),
			'btn_text'          => __( 'Buy Now', 'echo-knowledge-base' ),
			'btn_url'           => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/',
			'btn_color'         => 'green',

			'more_info_text'    => __( 'More Information', 'echo-knowledge-base' ),
			'more_info_url'     => 'https://www.echoknowledgebase.com/documentation/article-rating-feedback-overview/',
			'more_info_color'   => 'orange',
			'box_type'			=> 'new-feature',
			'return_html'       => true,
		) );
	}

	/**
	 * Get configuration array for regular views
	 *
	 * @param array|null $date_range Optional date range array with 'start' and 'end' timestamps
	 * @return array
	 */
	private function get_regular_views_config( $date_range = null ) {

		$views = [];

		/**
		 * View: Article Views Stats
		 */
		if ( $this->kb_config['article_views_counter_enable'] == 'on' ) {

			$views[] = [

				// Shared
				'active'                      => false,
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( [ 'admin_eckb_access_search_analytics_read' ] ),
				'list_key'                    => 'kb-article-views',

				// Top Panel Item
				'label_text'                  => esc_html__( 'KB Article Views', 'echo-knowledge-base' ),
				'icon_class'                  => 'epkbfa epkbfa-signal',

				// Boxes List
				'boxes_list'                  => array(

					// Box: KB Stats
					array(
						'html' => $this->get_kb_views_box_html( $date_range ),
					),
				),
			];
		}

		/**
		 * View: Search Data
		 */
		if ( ! EPKB_Utilities::is_advanced_search_enabled() ) {

			$views[] = [

				// Shared
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ),
				'list_key' => 'all-data',

				// Top Panel Item
				'label_text' => esc_html__( 'All Search Data', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-search',

				// Boxes List
				'boxes_list' => array(

					// Box: Search Data
					array(
						'html' => $this->get_search_data_box_html(),
					),

					// Box: Advanced Search Analytics Description
					array(
						'html' => '<div class="epkb-asea-analytics-teaser">' .
						          '<div class="epkb-asea-analytics-teaser__icon">' .
						          '<span class="epkbfa epkbfa-bar-chart"></span>' .
						          '</div>' .
						          '<div class="epkb-asea-analytics-teaser__content">' .
						          '<h3>' . esc_html__( 'Advanced Search Analytics Available', 'echo-knowledge-base' ) . '</h3>' .
						          '<p>' . esc_html__( 'Get detailed insights into search behavior with the Advanced Search add-on:', 'echo-knowledge-base' ) . '</p>' .
						          '<ul>' .
						          '<li><span class="epkbfa epkbfa-check-circle"></span> ' . esc_html__( 'Track most popular search keywords', 'echo-knowledge-base' ) . '</li>' .
						          '<li><span class="epkbfa epkbfa-check-circle"></span> ' . esc_html__( 'Identify searches with no results', 'echo-knowledge-base' ) . '</li>' .
						          '<li><span class="epkbfa epkbfa-check-circle"></span> ' . esc_html__( 'View search trends over time', 'echo-knowledge-base' ) . '</li>' .
						          '</ul>' .
						          '</div>' .
						          '</div>',
					),

					// Box: Advanced Search add-on ad
					array(
						'class' => 'epkb-admin__boxes-list__box__search-data__asea-ad',
						'html' => self::get_asea_addon_ad_box_html(),
					),
				),
			];
		}

		/**
		 * View: Rating Data
		 */
		if ( ! EPKB_Utilities::is_article_rating_enabled() ) {

			$views[] = [

				// Shared
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ),
				'list_key' => 'rating-data',

				// Top Panel Item
				'label_text' => esc_html__( 'Article Ratings', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-thumbs-up',

				// Boxes List
				'boxes_list' => array(

					// Box: Article Rating Analytics Description
					array(
						'html' => '<div class="epkb-eprf-analytics-teaser">' .
						          '<div class="epkb-eprf-analytics-teaser__icon">' .
						          '<span class="epkbfa epkbfa-star"></span>' .
						          '</div>' .
						          '<div class="epkb-eprf-analytics-teaser__content">' .
						          '<h3>' . esc_html__( 'Article Rating Analytics Available', 'echo-knowledge-base' ) . '</h3>' .
						          '<p>' . esc_html__( 'Understand article quality and user satisfaction with the Article Rating and Feedback add-on:', 'echo-knowledge-base' ) . '</p>' .
						          '<ul>' .
						          '<li><span class="epkbfa epkbfa-check-circle"></span> ' . esc_html__( 'Analyze most and best rated articles', 'echo-knowledge-base' ) . '</li>' .
						          '<li><span class="epkbfa epkbfa-check-circle"></span> ' . esc_html__( 'Identify articles needing improvement', 'echo-knowledge-base' ) . '</li>' .
						          '<li><span class="epkbfa epkbfa-check-circle"></span> ' . esc_html__( 'Track rating trends over time', 'echo-knowledge-base' ) . '</li>' .
						          '</ul>' .
						          '</div>' .
						          '</div>',
					),

					// Box: Article Rating add-on ad
					array(
						'class' => 'epkb-admin__boxes-list__box__rating-data__eprf-ad',
						'html' => self::get_eprf_addon_ad_box_html(),
					),
				),
			];
		}

		$add_on_views = apply_filters( 'eckb_admin_analytics_page_views', [], $this->kb_config, $date_range );
		if ( empty( $add_on_views ) || ! is_array( $add_on_views ) ) {
			$add_on_views = [];
		}

		// Set minimum required capability for search analytics data passed from add-ons
		foreach ( $add_on_views as $view_index => $view ) {

			// Apply for certain add-ons only
			if ( ! isset( $view['list_key'] ) || ! in_array( $view['list_key'], ['all-data', 'kb-search', 'search-shortcode', 'widgets', 'rating-data'] ) ) {
				continue;
			}

			// Access for View
			$add_on_views[$view_index]['minimum_required_capability'] = EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] );

			// Access for Boxes
			if ( isset( $view['boxes_list'] ) && is_array( $view['boxes_list'] ) ) {
				foreach ( $view['boxes_list'] as $box_index => $box ) {
					if ( ! current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) ) {
						$add_on_views[$view_index]['boxes_list'][$box_index]['class'] = isset( $box['class'] ) ? $box['class'] . ' epkb-admin__limit-access-control' : ' epkb-admin__limit-access-control';
					}
				}
			}

			// Access for Secondary Views
			if ( isset( $view['secondary_tabs'] ) && is_array( $view['secondary_tabs'] ) ) {
				foreach ( $view['secondary_tabs'] as $secondary_view_index => $secondary_view ) {

					// Access for Secondary Boxes
					if ( isset( $secondary_view['boxes_list'] ) && is_array( $secondary_view['boxes_list'] ) ) {
						foreach ( $secondary_view['boxes_list'] as $secondary_box_index => $secondary_box ) {
							if ( ! current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) ) {
								$add_on_views[$view_index]['secondary_tabs'][$secondary_view_index]['boxes_list'][$secondary_box_index]['class'] =
																isset( $secondary_box['class'] ) ? $secondary_box['class'] . ' epkb-admin__limit-access-control' : ' epkb-admin__limit-access-control';
							}
						}
					}
				}
			}
		}

		$views = array_merge( $views, $add_on_views );

		/**
		 * View: KB Stats
		 */
		$views[] = [

			// Shared
			'active' => true,
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ),
			'list_key' => 'kb-stats',

			// Top Panel Item
			'label_text' => esc_html__( 'KB Stats', 'echo-knowledge-base' ),
			'icon_class' => 'ep_font_icon_data_report',

			// Boxes List
			'boxes_list' => array(

				// Box: KB Stats
				array(
					'html' => $this->get_kb_stats_box_html(),
				),
			),
		];

		return $views;
	}

	/**
	 * Displays a Pie Chart Box with a list on the left and a pie chart on the right.
	 * The Chart is created using Chart.js and called in from our admin-plugins.js file then targets the container ID.
	 *
	 * @param  string $title Top Title of the card.
	 * @param  string $description Description of the card.
	 * @param  array $data Multidimensional array containing a list of Words and their counts.
	 * @param  string $id The id of the container and chart id. JS is used to target it to create the chart.
	 * @param string $empty_message
	 */
	private function pie_chart_data_box( $title, $description, $data, $id, $empty_message='' ) {   ?>

		<div class="epkb-analytics-card epkb-pie-chart-card">
			<div class="epkb-analytics-card__header">
				<h3><?php echo esc_html( $title ); ?></h3>
				<p class="epkb-analytics-card__desc"><?php echo esc_html( $description ); ?></p>
			</div>
			<div class="epkb-analytics-card__body">
				<section class="epkb-pie-chart-container" id="<?php echo esc_attr( $id ); ?>">

			<!-- Body ------------------->
			<div class="epkb-pie-chart-body">
				<div class="epkb-pie-chart-left-col">
					<ul class="epkb-pie-data-list">			<?php
						$item_count = 0;
						if ( empty( $data ) ) {
							echo esc_html( $empty_message );
						} else {
							foreach ( $data as $word ) {    ?>
								<li class="<?php echo ++$item_count <= 10 ? 'epkb-first-10' : 'epkb-after-10'; ?>">
									<span class="epkb-circle epkbfa epkbfa-circle"></span>
									<span class="epkb-pie-chart-word"><?php echo wp_kses_post( stripslashes( $word[0] ) ); ?></span>
									<span class="epkb-pie-chart-count"><?php echo esc_html( $word[1] ); ?></span>
								</li>                <?php
							}
						} ?>
					</ul> <?php

					// More button
					if ( $item_count > 10 ) {   ?>
						<a class="epkb-pie-chart__more-button epkb-primary-btn">
							<span class="epkb-pie-chart__more-button__more-text"><?php esc_html_e( 'More', 'echo-knowledge-base' ); ?></span>
							<span class="epkb-pie-chart__more-button__less-text epkb-hidden"><?php esc_html_e( 'Less', 'echo-knowledge-base' ); ?></span>
						</a>    <?php
					}   ?>
				</div>
				<div class="epkb-pie-chart-right-col">
					<div id="epkb-pie-chart" style="height: 225px">
						<canvas id="<?php echo esc_attr( $id ); ?>-chart"></canvas>
					</div>
				</div>
			</div>
		</section>
			</div>
		</div>	<?php
	}

	/**
	 * Display a simple list of articles
	 *
	 * @param string $title Box title
	 * @param string $description Box description
	 * @param array $articles Array of articles with 'title' and 'views' keys
	 * @param string $id Container ID
	 */
	private function list_data_box( $title, $description, $articles, $id ) {   ?>

		<div class="epkb-analytics-card epkb-list-card">
			<div class="epkb-analytics-card__header">
				<h3><?php echo esc_html( $title ); ?></h3>
				<p class="epkb-analytics-card__desc"><?php echo esc_html( $description ); ?></p>
			</div>
			<div class="epkb-analytics-card__body">
				<div class="epkb-list-container" id="<?php echo esc_attr( $id ); ?>">
					<div class="epkb-article-count"><?php echo esc_html__( 'Total', 'echo-knowledge-base' ) . ': ' . count( $articles ) . ' ' . esc_html__( 'articles', 'echo-knowledge-base' ); ?></div>
					<ul class="epkb-article-list">			<?php
						$count = 0;
						foreach ( $articles as $article ) {
							$count++;
							$class = $count <= 20 ? 'epkb-first-20' : 'epkb-after-20';    ?>
							<li class="<?php echo esc_attr( $class ); ?>">
								<span class="epkb-article-number"><?php echo esc_html( $count ); ?>.</span>
								<span class="epkb-article-title"><?php echo wp_kses_post( stripslashes( $article['title'] ) ); ?></span>
							</li>                <?php
						} ?>
					</ul> <?php

					// Show more/less button
					if ( count( $articles ) > 20 ) {   ?>
						<a class="epkb-article-list__more-button epkb-primary-btn">
							<span class="epkb-article-list__more-button__more-text"><?php esc_html_e( 'Show More', 'echo-knowledge-base' ); ?></span>
							<span class="epkb-article-list__more-button__less-text epkb-hidden"><?php esc_html_e( 'Show Less', 'echo-knowledge-base' ); ?></span>
						</a>    <?php
					}   ?>
				</div>
			</div>
		</div>	<?php
	}

	/**
	 * Display improvement data for articles
	 *
	 * @param string $title Box title
	 * @param string $description Box description
	 * @param array $articles Array of articles with improvement data
	 * @param string $id Container ID
	 */
	private function improvement_data_box( $title, $description, $articles, $id ) {   ?>

		<div class="epkb-analytics-card epkb-improvement-card">
			<div class="epkb-analytics-card__header">
				<h3><?php echo esc_html( $title ); ?></h3>
				<p class="epkb-analytics-card__desc"><?php echo esc_html( $description ); ?></p>
			</div>
			<div class="epkb-analytics-card__body">
				<div class="epkb-improvement-container" id="<?php echo esc_attr( $id ); ?>">
					<div class="epkb-article-count"><?php echo esc_html__( 'Total', 'echo-knowledge-base' ) . ': ' . count( $articles ) . ' ' . esc_html__( 'articles', 'echo-knowledge-base' ); ?></div>
					<ul class="epkb-improvement-list">			<?php
						$count = 0;
						foreach ( $articles as $article ) {
							$count++;
							$class = $count <= 20 ? 'epkb-first-20' : 'epkb-after-20';    ?>
							<li class="<?php echo esc_attr( $class ); ?>">
								<div class="epkb-improvement-item">
									<div class="epkb-improvement-item__header">
										<span class="epkb-article-number"><?php echo esc_html( $count ); ?>.</span>
										<span class="epkb-article-title"><?php echo wp_kses_post( stripslashes( $article['title'] ) ); ?></span>
									</div>
									<div class="epkb-improvement-item__stats">
										<span class="epkb-stat">
											<span class="epkb-stat-label"><?php esc_html_e( 'Previous:', 'echo-knowledge-base' ); ?></span>
											<span class="epkb-stat-value"><?php echo esc_html( number_format_i18n( $article['previous_views'] ) ); ?></span>
										</span>
										<span class="epkb-stat-separator">→</span>
										<span class="epkb-stat">
											<span class="epkb-stat-label"><?php esc_html_e( 'Current:', 'echo-knowledge-base' ); ?></span>
											<span class="epkb-stat-value"><?php echo esc_html( number_format_i18n( $article['current_views'] ) ); ?></span>
										</span>
										<span class="epkb-improvement-change">
											<span class="epkb-change-absolute">+<?php echo esc_html( number_format_i18n( $article['absolute_change'] ) ); ?></span>
											<span class="epkb-change-percent">(+<?php echo esc_html( $article['percent_change'] ); ?>%)</span>
										</span>
									</div>
								</div>
							</li>                <?php
						} ?>
					</ul> <?php

					// Show more/less button
					if ( count( $articles ) > 20 ) {   ?>
						<a class="epkb-article-list__more-button epkb-primary-btn">
							<span class="epkb-article-list__more-button__more-text"><?php esc_html_e( 'Show More', 'echo-knowledge-base' ); ?></span>
							<span class="epkb-article-list__more-button__less-text epkb-hidden"><?php esc_html_e( 'Show Less', 'echo-knowledge-base' ); ?></span>
						</a>    <?php
					}   ?>
				</div>
			</div>
		</div>	<?php
	}

	/**
	 * Enable article views counter for a knowledge base via AJAX.
	 */
	public static function ajax_toggle_article_views_counter() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

		$kb_id = (int) EPKB_Utilities::post( 'kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Invalid knowledge base.', 'echo-knowledge-base' ),
			) );
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		if ( is_wp_error( $kb_config ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Unable to load knowledge base configuration.', 'echo-knowledge-base' ),
			) );
		}

		if ( isset( $kb_config['article_views_counter_enable'] ) && $kb_config['article_views_counter_enable'] === 'on' ) {
			wp_send_json_success( array(
				'message' => esc_html__( 'Article views counter is already enabled.', 'echo-knowledge-base' ),
			) );
		}

		$kb_config['article_views_counter_enable'] = 'on';

		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $kb_config );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Unable to update article views counter.', 'echo-knowledge-base' ),
			) );
		}

		wp_send_json_success( array(
			'message' => esc_html__( 'Article views counter enabled.', 'echo-knowledge-base' ),
		) );
	}

	/**
	 * AJAX handler to get filtered analytics data based on date range
	 */
	public static function ajax_get_filtered_analytics() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_search_analytics_read' );

		$kb_id = (int) EPKB_Utilities::post( 'kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Invalid knowledge base.', 'echo-knowledge-base' ),
			) );
		}

		$preset = EPKB_Utilities::post( 'preset', 'all-time' );
		$start_date = EPKB_Utilities::post( 'start_date', '' );
		$end_date = EPKB_Utilities::post( 'end_date', '' );

		// Calculate date range
		$date_range = self::calculate_date_range( $preset, $start_date, $end_date );

		// Get analytics page instance
		$analytics_page = new self();
		$analytics_page->kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		if ( is_wp_error( $analytics_page->kb_config ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Unable to load knowledge base configuration.', 'echo-knowledge-base' ),
			) );
		}

		// Get all sections with date range applied
		$sections = $analytics_page->get_compact_sections( $date_range );

		// Generate HTML for each section
		$sections_html = array();
		foreach ( $sections as $slug => $section ) {
			$sections_html[ $slug ] = isset( $section['content'] ) ? $section['content'] : '';
		}

		wp_send_json_success( array(
			'sections' => $sections_html,
		) );
	}
}
