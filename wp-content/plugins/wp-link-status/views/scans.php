<?php

// Check WP constant
if (!defined('ABSPATH')) {
	die;
}

// Check dependencies
if (!class_exists('WP_List_Table')) {
	require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Scans class
 *
 * @package WP Link Status
 * @subpackage Views
 */
class WPLNST_Views_Scans extends WP_List_Table {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * External data
	 */
	private $results;


	/**
	 * Base URL for filters
	 */
	private $base_url;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Constructor
	 */
	public function __construct($results) {

		// Parent constructor
		parent::__construct();

		// Copy results
		$this->results = $results;

		// Base link for filters
		$this->base_url = esc_url(WPLNST_Core_Plugin::get_url_scans());
	}



	/**
	 * Prepare columns and data
	 */
	function prepare_items() {

		// Columns
		$this->setup_columns();

		// Data items
		$this->setup_items();

		// Pagination
		$this->setup_pagination();
	}



	/**
	 * Setup columns
	 */
	private function setup_columns() {

		// Initialize
		$hidden = array();
		$sortable = array();

		// Column headers
		$this->_column_headers = array($this->get_columns(), $hidden, $sortable);
	}



	/**
	 * Columns array
	 */
	public function get_columns(){

		// Checkbox column
		$columns = array();
		if (!$this->results->isolated) {
			$columns['cb'] = '<input type="checkbox" />';
		}

		// All columns
		return array_merge($columns, array(
			'wplnst-scans-name' 			=> __('Scan info', 		'wplnst'),
			'wplnst-scans-configuration' 	=> __('Configuration',	'wplnst'),
		));
	}



	/**
	 * Setup data items
	 */
	private function setup_items() {

		// Initialize
		$this->items = array();

		// Warning image
		$warning_img = '<img src="'.plugins_url('assets/images/scan-warning.png', WPLNST_FILE).'" width="16" height="16" border="0" style="margin-right: 5px;" title="'.__('Some critical values of this scan are not completed', 'wplnst').'">';

		// Populate data
		foreach ($this->results->rows as $scan) {

			// Initialize
			$timeinfo = false;
			$linksinfo = '';

			// Normalize identifiers and other data
			$item = array('ID' => $scan->id, 'hash' => $scan->hash, 'status' => $scan->status, 'ready' => $scan->ready);

			// Check processed posts
			$message = ('play' != $scan->status)? '' : __('Waiting...', 'wplnst');

			// Initialize
			$processed = array();
			$running_back = false;
			$class_completed = ('end' == $scan->status)? 'wplnst-scan-object-completed-end' : 'wplnst-scan-object-completed';

			if (isset($scan->trace['total_posts'])) {
				$running_back = empty($scan->trace['populated_posts']);
				$posts_index = empty($scan->trace['posts_index'])? 0 : number_format_i18n($scan->trace['posts_index']);
				$processed[] = empty($scan->trace['populated_posts'])? '<span class="wplnst-scan-object-info wplnst-scan-object-running">'.$posts_index.'/'.number_format_i18n($scan->trace['total_posts']).' '.__('entries', 'wplnst').'</span>' : '<span class="wplnst-scan-object-info '.$class_completed.'">'.number_format_i18n($scan->trace['total_posts']).' '.__('entries', 'wplnst').'</span>';
			}

			if (isset($scan->trace['total_comments'])) {
				$running = !$running_back && empty($scan->trace['populated_comments']);
				$running_back = $running? true : $running_back;
				$class_running = $running? 'wplnst-scan-object-running' : 'wplnst-scan-object-wait';
				$comments_index = empty($scan->trace['comments_index'])? 0 : number_format_i18n($scan->trace['comments_index']);
				$processed[] = empty($scan->trace['populated_comments'])? '<span class="wplnst-scan-object-info '.$class_running.'">'.$comments_index.'/'.number_format_i18n($scan->trace['total_comments']).' '.__('comments', 'wplnst').'</span>' : '<span class="wplnst-scan-object-info '.$class_completed.'">'.number_format_i18n($scan->trace['total_comments']).' '.__('comments', 'wplnst').'</span>';
			}

			if (isset($scan->trace['total_blogroll'])) {
				$running = !$running_back && empty($scan->trace['populated_blogroll']);
				$class_running = $running? 'wplnst-scan-object-running' : 'wplnst-scan-object-wait';
				$blogroll_index = empty($scan->trace['blogroll_index'])? 0 : number_format_i18n($scan->trace['blogroll_index']);
				$processed[] = empty($scan->trace['populated_blogroll'])? '<span class="wplnst-scan-object-info '.$class_running.'">'.$blogroll_index.'/'.number_format_i18n($scan->trace['total_blogroll']).' '.__('blogroll', 'wplnst').'</span>' : '<span class="wplnst-scan-object-info '.$class_completed.'">'.number_format_i18n($scan->trace['total_blogroll']).' '.__('blogroll', 'wplnst').'</span>';
			}

			// Check info
			if (!empty($processed)) {

				// Processed object types
				$message = implode('&nbsp;', $processed);

				// Check status
				if ('wait' != $scan->status) {


					/* Time info */

					// Start and local extra time
					$time_start = strtotime($scan->row->started_at.' UTC');
					$offset_time = get_option('gmt_offset') * HOUR_IN_SECONDS;

					// Current dates
					$today_date = gmdate('d/m/Y', time() + $offset_time);
					$yesterday_date = gmdate('d/m/Y', time() + $offset_time - 86400);

					// Local date and hour
					$start_date = gmdate('d/m/Y', $time_start + $offset_time);
					$start_hour = gmdate('H:i', $time_start + $offset_time);

					// Check today
					if ($start_date == $today_date) {

						// Today
						$started_at = sprintf(__('Today from %s', 'wplnst'), $start_hour);

					// Check yesterday
					} elseif ($start_date == $yesterday_date) {

						// Yesterday
						$started_at = sprintf(__('Yesterday at %s', 'wplnst'), $start_hour);

					// Other
					} else {

						// Date and hour
						$started_at = sprintf(__('%s at %s', 'wplnst'), $start_date, $start_hour);
					}

					// Start date
					$timeinfo = $started_at;

					// Retrieve time stopped
					$time_stopped = isset($scan->summary['time_stopped'])? (int) $scan->summary['time_stopped'] : 0;

					// Ended scan
					if ('end' == $scan->status) {

						// Finished date
						$time_end = $time_end_amount = strtotime($scan->row->finished_at.' UTC');

						// Local date and hour
						$end_date = gmdate('d/m/Y', $time_end + $offset_time);
						$end_hour = gmdate('H:i', $time_end + $offset_time);

						// Check today
						if ($end_date == $today_date) {

							// All today
							if ($start_date == $today_date) {

								// Started and finished today
								$timeinfo .= ' '.sprintf(__('to %s', 'wplnst'), $end_hour);

							// Date to today
							} else {

								// Started another date and finished today
								$timeinfo .= ' '.sprintf(__('to today at %s', 'wplnst'), $end_hour);
							}

						// Check yesterday
						} elseif ($end_date == $yesterday_date) {

							// All yesterday
							if ($start_date == $yesterday_date) {

								// Started and finished today
								$timeinfo .= ' '.sprintf(__('to %s', 'wplnst'), $end_hour);

							// Date to yesterday
							} else {

								// Started another date and finished today
								$timeinfo .= ' '.sprintf(__('to yesterday at %s', 'wplnst'), $end_hour);
							}

						// Check same day
						} elseif ($end_date == $start_date) {

							// Same day
							$timeinfo .= ' '.sprintf(__('until %s', 'wplnst'), $end_hour);

						// Before
						} else {

							// Different days
							$timeinfo .= ' '.sprintf(__('until %s at %s', 'wplnst'), $end_date, $end_hour);
						}

						// Time stopped correction
						if (!empty($time_stopped)) {
							$time_end_amount -= $time_stopped;
						}

						// Total time
						$timeinfo .= ' &#8212; '.human_time_diff($time_start, $time_end_amount);

					// Running
					} else {

						// Until now or stopped
						$time_end = ('stop' == $scan->status)? strtotime($scan->row->stopped_at.' UTC') : time();

						// Time stopped correction
						if (!empty($time_stopped)) {
							$time_end -= $time_stopped;
						}

						// Elapsed time
						$timeinfo .= ' &#8212; '.sprintf(__('Running time %s', 'wplnst'), human_time_diff($time_start, $time_end));
					}


					/* Links info */

					// Normalize
					$phases = array();
					foreach ($scan->summary as $key => $value) {
						if (0 === strpos($key, 'urls_phase_')) {
							$phase = explode('urls_phase_', $key);
							$phases[$phase[1]] = $value;
						}
					}

					// Check values
					if (!empty($phases)) {

						// Collect data
						$items = array();

						// All results
						$items[] = (isset($scan->summary['status_total']) && $scan->summary['status_total'] > 0)? sprintf(__('<strong>%s</strong> results', 'wplnst'), number_format_i18n((int) $scan->summary['status_total'])) : __('No results', 'wplnst');

						// Total processed
						if (!empty($phases['processed'])) {

							// Add uniques
							$items[] = sprintf(__('<strong>%s</strong> unique URLs', 'wplnst'), number_format_i18n((int) $phases['processed']));
						}

						// Enqueued URLs
						if ('end' != $scan->status) {
							$items[] = sprintf(__('%s enqueued', 'wplnst'), (isset($phases['wait'])? number_format_i18n((int) $phases['wait']) : '0'));
						}

						// Waiting
						if ('play' == $scan->status) {
							$items[] = sprintf(__('%s processing', 'wplnst'), (empty($phases['play'])? wplnst_get_nsetting('max_threads', $scan->threads->max) : (int) $phases['play']));
						}

						// End crawler
						if ('end' == $scan->status) {
							$items[] = sprintf(__('<strong>%s</strong> Request error', 'wplnst'), (isset($scan->summary['status_level_0'])? (int) number_format_i18n($scan->summary['status_level_0']) : '0'));
						}

						// Join items
						if (!empty($items)) {
							$linksinfo = implode('<span class="wplnst-split-char">&middot;</span>', $items);
						}
					}
				}

			// Only created
			} elseif ('wait' == $scan->status || 'queued' == $scan->status || 'stop' == $scan->status) {

				// Created date
				$time_created = strtotime($scan->row->created_at.' UTC');
				$offset_time = get_option('gmt_offset') * HOUR_IN_SECONDS;

				// Current dates
				$today_date = gmdate('d/m/Y', time() + $offset_time);
				$yesterday_date = gmdate('d/m/Y', time() + $offset_time - 86400);

				// Local date and hour
				$created_date = gmdate('d/m/Y', $time_created + $offset_time);
				$created_hour = gmdate('H:i', $time_created + $offset_time);

				// Check today
				if ($created_date == $today_date) {

					// Created today
					$timeinfo = sprintf(__('Created today at %s', 'wplnst'), $created_hour);

				// Yesterday
				} elseif ($created_date == $yesterday_date) {

					// Created yesterday
					$timeinfo = sprintf(__('Created yesterday at %s', 'wplnst'), $created_hour);

				// Any date
				} else {

					// Created date and hour
					$timeinfo = sprintf(__('Created %s at %s', 'wplnst'), $created_date, $created_hour);
				}
			}



			// Item name, possible warning and link to edit
			$statuses = WPLNST_Core_Types::get_scan_statuses();
			$item['wplnst-scans-name']  = '<strong class="wplnst-scan-name'.($scan->ready? '' : ' wplnst-scan-name-warning').'">'.($scan->ready? '' : $warning_img);
			if ('wait' != $scan->status) {
				$item['wplnst-scans-name'] .= '<span class="wplnst-scan-status wplnst-scan-status-'.esc_attr($scan->status).'">'.$statuses[$scan->status].'</span>&nbsp;';
			}
			$item['wplnst-scans-name'] .= '<a class="row-title" href="'.esc_url(WPLNST_Core_Plugin::get_url_scans_results($scan->id)).'">'.(empty($scan->name)? __('(no name)', 'wplnst') : esc_html($scan->name)).'</a></strong>';
			$item['wplnst-scans-name'] .= '<div class="wplnst-scan-status-line">'.$message.'</div>';

			// Check ready message
			if ('wait' == $scan->status && $scan->ready) {
				$item['wplnst-scans-name'] .= '<div class="wplnst-scan-ready-info">'.sprintf(__('Ready to <a href="%s">start the crawler</a>', 'wplnst'), esc_url(WPLNST_Core_Plugin::get_url_scans_crawler($scan->id, 'on', $scan->hash))).'</div>';
			}

			// Check datatime info
			if (!empty($timeinfo)) {
				$item['wplnst-scans-name'] .= '<div class="wplnst-scan-time-info">'.$timeinfo.'</div>';
			}

			// Check links info
			if (!empty($linksinfo)) {
				$item['wplnst-scans-name'] .= '<div class="wplnst-scan-links-info">'.$linksinfo.'</div>';
			}


			// Prepare configuration
			$item['wplnst-scans-configuration'] = '<table>';

			// Scan scope
			$link_types = (empty($scan->link_types_names)? '' : implode(', ', array_map('esc_html', $scan->link_types_names)));
			$item['wplnst-scans-configuration'] .= '<tr><td class="wplnst-scans-configuration-row"><strong>'.__('Scope', 'wplnst').'</strong></td><td>'.
			(empty($link_types)? '' : $link_types.', ').esc_html($scan->destination_type_name).', '.esc_html($scan->time_scope_name).', '.esc_html(__('Order by', 'wplnst')).' '.esc_html($scan->crawl_order_name).', '.($scan->redir_status? __('Check redirection status', 'wplnst') : __('Redirections not checked', 'wplnst')).($scan->malformed? ', '.__('Malformed', 'wplnst') : '').'</td></tr>';

			// Post types and status
			$item['wplnst-scans-configuration'] .= (empty($scan->post_types_names) && empty($scan->post_status_names))? '' :
			'<tr><td class="wplnst-scans-configuration-row"><strong>'.__('Post types', 'wplnst').'</strong></td><td>'.(empty($scan->post_types_names)? '' : implode(', ', array_map('esc_html', $scan->post_types_names))).(empty($scan->post_status_names)? '' : (empty($scan->post_types_names)? '' : ', ').__('Post status', 'wplnst').' '.implode(', ', array_map('esc_html', $scan->post_status_names))).'</td></tr>';

			// Links status
			$item['wplnst-scans-configuration'] .= empty($scan->links_status_names)? '' : '<tr><td class="wplnst-scans-configuration-row"><strong>'.__('Link status', 'wplnst').'</strong></td><td>'.implode(', ', array_map('esc_html', $scan->links_status_names)).'</td></tr>';

			// End configuration
			$item['wplnst-scans-configuration'] .= '</table>';

			// Add row
			$this->items[] = $item;
		}
	}



	/**
	 * Default method to display a column
	 *
	 * @param array  $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	protected function column_default($item, $column_name) {

		// Actions for name column
		if ('wplnst-scans-name' == $column_name) {

			// Initialize
			$actions = array();

			// Results link
			$actions['results'] = '<a href="'.esc_url(WPLNST_Core_Plugin::get_url_scans_results($item['ID'])).'">'.__('Show results', 'wplnst').'</a>';

			// Edit link
			$actions['edit'] = '<span class="edit"><a href="'.esc_url(WPLNST_Core_Plugin::get_url_scans_edit($item['ID'])).'">'.__('Edit scan', 'wplnst').'</a></span>';

			// Stop or start crawler
			if ('end' != $item['status'] && ($item['ready'] || 'play' == $item['status'])) {
				$actions['crawler'] = '<a href="'.esc_url(WPLNST_Core_Plugin::get_url_scans_crawler($item['ID'], ('play' == $item['status'] || 'queued' == $item['status'])? 'off' : 'on', $item['hash'])).'">'.(in_array($item['status'], array('wait', 'stop'))? __('Start crawler', 'wplnst') : (('queued' == $item['status'])? __('Unqueue crawling', 'wplnst')  : __('Stop crawler', 'wplnst'))).'</a>';
			}

			// Remove scan
			$actions['delete'] = '<span class="trash"><a href="'.esc_url(WPLNST_Core_Plugin::get_url_scans_delete($item['ID'], $item['hash'])).'" class="wplnst-scan-delete">'.esc_html(WPLNST_Admin::get_text('scan_delete')).'</a></span>';

			// Done
			return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions));
		}

		// Default column
		return $item[$column_name];
	}



	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'delete' => __('Delete', 'wplnst'),
		);
	}



	/**
	 * Handles the checkbox column output.
	 */
	protected function column_cb($item) {
		return $this->results->isolated? null : sprintf('<input type="checkbox" class="wplnst-ck-scan-id" value="%s" />', $item['ID']);
    }



	/**
	 * Set pagination arguments
	 */
	private function setup_pagination() {
		$this->set_pagination_args(array(
			'per_page' 		=> $this->results->isolated? 0 : (isset($this->results->per_page)? $this->results->per_page : 1),
			'total_items' 	=> isset($this->results->total_rows)?  $this->results->total_rows : count($this->results->rows),
			'total_pages'	=> $this->results->isolated? 0 : (isset($this->results->total_pages)? $this->results->total_pages : 1),
		));
	}



	/**
	 * Display the table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display() {

		// Wrapper form
		echo  '<form method="get" action="'.esc_url($this->base_url).'" id="wplnst-scans" data-href-delete="'.WPLNST_Core_Plugin::get_url_scans_delete('%scan_id%', 'bulk-scans-delete').'" data-confirm-delete="'.esc_attr(WPLNST_Admin::get_text('scan_delete_confirm')).'" data-confirm-delete-bulk="'.esc_attr__('Do you want to remove these scans?', 'wplnst').'">';

		// Check isolated classes
		$extra_classes = array();
		if ($this->results->isolated) {
			$extra_classes[] = 'wplnst-isolated-table';
		}

		// Check isolated display
		if (!$this->results->isolated) {
			$this->display_tablenav('top');
		}

		?><table class="wp-list-table <?php echo implode(' ', array_merge($this->get_table_classes(), $extra_classes)); ?>">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
			<?php if (!$this->results->isolated) : ?>
			<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
			</tfoot>
			<?php endif; ?>
			</table><?php

		// Check isolated display
		if (!$this->results->isolated) {
			$this->display_tablenav('bottom');
		}

		// Close form
		echo '</form>';
	}



	/**
	 * Generate the table navigation above or below the table
	 */
	protected function display_tablenav($which) {
		?><div class="tablenav <?php echo esc_attr($which); ?>">
			<div class="alignleft actions bulkactions wplnst-scans-bulkactions-<?php echo $which; ?>">
				<?php $this->bulk_actions($which); ?>
			</div>
			<?php $this->pagination($which); ?>
			<br class="clear" />
		</div><?php
	}



}