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
	wp_enqueue_script( 'openlab-usage-reporting', OLUR_PLUGIN_URL . 'assets/js/openlab-usage-reporting.js', array( 'jquery-ui-datepicker' ) );
}

/**
 * Markup for the admin panel.
 */
function olur_admin_panel() {

	?>
	<div class="wrap">
		<h2><?php _e( 'OpenLab Usage Reporting', 'openlab-usage-reporting' ) ?></h2>

		<p><?php _e( 'Select a date range to generate a usage report.', 'openlab-usage-reporting' ) ?></p>

		<div class="olur-dates">

		<form method="post" action="">
			<label for="olur-start"><?php _e( 'Start: ', 'openlab-usage-reporting' ) ?></label> <input type="text" class="olur-datepicker" name="olur-start" id="olur-start"><br />
			<label for="olur-end"><?php _e( 'End: ', 'openlab-usage-reporting' ) ?></label> <input type="text" class="olur-datepicker" name="olur-end" id="olur-end">

			<?php wp_nonce_field( 'olur-generate' ) ?>
			<?php submit_button( 'Generate Report' ) ?>
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
