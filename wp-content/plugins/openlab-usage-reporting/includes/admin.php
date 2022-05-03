<?php

/**
 * Create the menu item in the Network Admin.
 */
function olur_add_menu_item() {
	$plugin_page = add_menu_page(
		__( 'OpenLab Usage Reporting', 'openlab-usage-reporting' ),
		__( 'OpenLab Usage', 'openlab-usage-reporting' ),
		'manage_network_options',
		'openlab-usage-reporting',
		'olur_admin_panel'
	);

	add_action( 'admin_print_scripts-' . $plugin_page, 'olur_admin_assets' );
}
add_action( 'network_admin_menu', 'olur_add_menu_item' );

/**
 * Enqueue CSS and JS assets.
 */
function olur_admin_assets() {
	wp_enqueue_style( 'openlab-usage-reporting', OLUR_PLUGIN_URL . 'assets/css/openlab-usage-reporting.css' );
	wp_enqueue_script( 'openlab-usage-reporting', OLUR_PLUGIN_URL . 'assets/js/openlab-usage-reporting.js', array( 'jquery-ui-datepicker', 'jquery-ui-progressbar' ) );
}

/**
 * Markup for the admin panel.
 */
function olur_admin_panel() {
	$now = time();

	$default_start = date( 'm/d/Y', $now - YEAR_IN_SECONDS );
	$default_end   = date( 'm/d/Y', $now );

	?>
	<div class="wrap">
		<h2><?php _e( 'OpenLab Usage Reporting', 'openlab-usage-reporting' ) ?></h2>

		<p><?php _e( 'Select a date range to generate a usage report.', 'openlab-usage-reporting' ) ?></p>

		<div class="olur-dates">

		<form method="post" action="">
			<label for="olur-start"><?php _e( 'Start: ', 'openlab-usage-reporting' ) ?></label> <input type="text" class="olur-datepicker" name="olur-start" id="olur-start" value="<?php echo esc_attr( $default_start ); ?>"><br />
			<label for="olur-end"><?php _e( 'End: ', 'openlab-usage-reporting' ) ?></label> <input type="text" class="olur-datepicker" name="olur-end" id="olur-end" value="<?php echo esc_attr( $default_end ); ?>">

			<?php wp_nonce_field( 'olur-generate' ) ?>
			<?php submit_button( 'Generate Report' ) ?>
			<div id="progressbar"></div>
			<div id="progress-message">Generating report...</div>
		</form>

		</div>

		<h3><?php _e( 'Information', 'openlab-usage-reporting' ) ?></h3>

		<p><?php _e( 'Reports contain the following data.', 'openlab-usage-reporting' ) ?></p>

		<h4><?php _e( 'For each user type (student, faculty, staff, alumni, other):', 'openlab-usage-reporting' ) ?></h4>
		<ul>
			<li><?php _e( 'Total counts for the start and end date.', 'openlab-usage-reporting' ) ?></li>
			<li><?php _e( 'Number of users created during the period', 'openlab-usage-reporting' ) ?></li>
			<li><?php _e( 'Number of users deleted during the period', 'openlab-usage-reporting' ) ?></li>
		</ul>

		<p><?php _e( 'Note that it is possible for users to change their own user type in some cases (eg, Student to Alumni). The report reflects <strong>current data</strong>.', 'openlab-usage-reporting' ) ?></p>


		<h4><?php _e( 'For each group type (course, project, club, student ePortfolio, faculty Portfolio, staff Portfolio, each subdivided by privacy level):', 'openlab-usage-reporting' ) ?></h4>
		<ul>
			<li><?php _e( 'Total counts for the start and end date.', 'openlab-usage-reporting' ) ?></li>
			<li><?php _e( 'Number of groups created during the period', 'openlab-usage-reporting' ) ?></li>
			<li><?php _e( 'Number of groups deleted during the period', 'openlab-usage-reporting' ) ?></li>
		</ul>
	</div>
	<?php
}

/**
 * Catch submit requests and process.
 */
function olur_catch_generate_requests() {
	if ( empty( $_POST['submit'] ) ) {
		return;
	}

	if ( ! is_super_admin() ) {
		return;
	}

	if ( empty( $_POST['olur-start'] ) || empty( $_POST['olur-end'] ) || empty( $_POST['_wpnonce'] ) ) {
		return;
	}

	check_admin_referer( 'olur-generate' );

	$start = stripslashes( $_POST['olur-start'] );
	$end   = stripslashes( $_POST['olur-end'] );

	$start = date( 'Y-m-d H:i:s', strtotime( $start ) );
	$end   = date( 'Y-m-d H:i:s', strtotime( $end ) + ( 60 * 60 * 24 ) - 1 ); // Bump to remain inclusive

	olur_generate_report( $start, $end );
}
add_action( 'admin_init', 'olur_catch_generate_requests', 0 );

/**
 * AJAX callback for report batches.
 */
function olur_batch_ajax_callback() {
	if ( empty( $_POST['currentReportId'] ) || empty( $_POST['startDate'] ) || empty( $_POST['endDate'] ) ) {
		return;
	}

	$report_id  = wp_unslash( $_POST['currentReportId'] );
	$status_key = 'olur_report_status_' . $report_id;

	$all_callbacks = olur_report_callbacks();

	$report_status = get_option( $status_key );
	if ( ! $report_status ) {
		$report_status = $all_callbacks;
	}

	// Take the first available callback.
	foreach ( $report_status as $class => $callbacks ) {
		foreach ( $callbacks as $callback_index => $callback ) {
			$the_callback = $callback;
			break;
		}
		$the_class = $class;
		break;
	}

	$file_name   = 'openlab-usage-' . date( 'Y-m-d:H:i:s', $report_id ) . '.csv';
	$report_dir  = olur_report_directory();
	$report_path = $report_dir['dir'] . $file_name;

	$is_file_new = ! file_exists( $report_path );

	$fh = fopen( $report_path, 'a+' );

	if ( $is_file_new ) {
		fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );

		$header_row = array(
			0 => '',
			1 => 'Start #',
			2 => '# Created',
			4 => 'End #',
			5 => 'Actively Active',
			6 => 'Passively Active',
		);

		fputcsv( $fh, $header_row );
	}

	$start = date( 'Y-m-d H:i:s', strtotime( wp_unslash( $_POST['startDate'] ) ) );
	$end   = date( 'Y-m-d H:i:s', strtotime( wp_unslash( $_POST['endDate'] ) ) + ( 60 * 60 * 24 ) - 1 ); // Bump to remain inclusive

	$row = olur_generate_report_data_row( $the_class, $the_callback, $start, $end );

	if ( ! is_array( $row ) ) {
		$row = [];
	}

	fputcsv( $fh, $row );

	unset( $report_status[ $class ][ $callback_index ] );

	if ( empty( $report_status[ $class ] ) ) {
		unset( $report_status[ $class ] );

		// Print an empty row after a section.
		fputcsv( $fh, [] );
	}

	fclose( $fh );

	$all_callback_count       = olur_count_steps( $all_callbacks );
	$remaining_callback_count = olur_count_steps( $report_status );

	$data = [
		'file' => $report_dir['url'] . $file_name,
		'pct'  => 100 * ( ( $all_callback_count - $remaining_callback_count ) / $all_callback_count ),
		'more' => $remaining_callback_count > 0,
	];

	if ( ! $remaining_callback_count ) {
		delete_option( $status_key );
	} else {
		update_option( $status_key, $report_status );
	}

	wp_send_json_success( $data );
}
add_action( 'wp_ajax_olur_batch', 'olur_batch_ajax_callback' );

/**
 * Count the steps in a status array.
 */
function olur_count_steps( $status ) {
	$count = 0;

	foreach ( $status as $class => $callbacks ) {
		$count += count( $callbacks );
	}

	return $count;
}

/**
 * Gets the usage-reports directory path and URL.
 *
 * Creates if necessary.
 */
function olur_report_directory() {
	$upload_dir = wp_upload_dir();
	$report_dir = $upload_dir['basedir'] . '/usage-reports/';
	$report_url = $upload_dir['baseurl'] . '/usage-reports/';

	if ( ! file_exists( $report_dir ) ) {
		wp_mkdir_p( $report_dir );
	}

	return [
		'dir' => $report_dir,
		'url' => $report_url,
	];
}
