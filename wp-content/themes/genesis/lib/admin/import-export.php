<?php
/**
 * Controls the Import/Export functions of the Genesis Framework.
 *
 * @package Genesis
 */

/**
 * This function controls the admin page for the Genesis Import/Export functionality.
 *
 * @since 1.4
 */
function genesis_import_export_admin() { ?>

	<div class="wrap">
		<?php screen_icon( 'tools' ); ?>
		<h2><?php _e( 'Genesis - Import/Export', 'genesis' ); ?></h2>

			<table class="form-table"><tbody>

				<tr>
					<th scope="row"><p><b><?php _e( 'Import Genesis Settings File', 'genesis' ); ?></b></p></th>
					<td>
						<p><?php _e( 'Upload the data file from your computer (.json) and we\'ll import your settings.', 'genesis' ); ?></p>
						<p><?php _e( 'Choose the file from your computer and click "Upload and Import"', 'genesis' ); ?></p>
						<p>
							<form enctype="multipart/form-data" method="post" action="<?php echo menu_page_url( 'genesis-import-export', 0 ); ?>">
								<?php wp_nonce_field( 'genesis-import' ); ?>
								<input type="hidden" name="genesis-import" value="1" />
								<label for="genesis-import-upload"><?php sprintf( __( 'Upload File: (Maximum Size: %s)', 'genesis' ), ini_get('post_max_size') ); ?></label>
								<input type="file" id="genesis-import-upload" name="genesis-import-upload" size="25" />
								<input type="submit" class="button" value="<?php _e( 'Upload file and import', 'genesis' ); ?>" />
							</form>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><p><b><?php _e( 'Export Genesis Settings File', 'genesis' ); ?></b></p></th>
					<td>
						<p><?php _e( 'When you click the button below, Genesis will generate a JSON file for you to save to your computer.', 'genesis' ); ?></p>
						<p><?php _e( 'Once you have saved the download file, you can use the import function on another site to import this data.', 'genesis' ); ?></p>
						<p>
							<form method="post" action="<?php echo menu_page_url( 'genesis-import-export', 0 ); ?>">
								<?php
								wp_nonce_field( 'genesis-export' );
								genesis_export_checkboxes();
								if ( genesis_get_export_options() ) {
								?>
								<input type="submit" class="button" value="<?php _e('Download Export File', 'genesis'); ?>" />
								<?php } ?>
							</form>
						</p>
					</td>
				</tr>

				<?php do_action( 'genesis_import_export_form' ); ?>

			</tbody></table>

	</div>

<?php }

add_action('admin_notices', 'genesis_import_export_notices');
/**
 * This is the notice that displays when you successfully import or export the
 * settings.
 *
 * @since 1.4
 */
function genesis_import_export_notices() {

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'genesis-import-export' )
		return;

	if ( isset( $_REQUEST['imported'] ) && $_REQUEST['imported'] == 'true' ) {
		echo '<div id="message" class="updated"><p><strong>'.__('Settings successfully imported!', 'genesis').'</strong></p></div>';
	}
	elseif ( isset($_REQUEST['error']) && $_REQUEST['error'] == 'true') {
		echo '<div id="message" class="error"><p><strong>'.__('There was a problem importing your settings. Please try again.', 'genesis').'</strong></p></div>';
	}

}

/**
 * Return array of export options and their arguments.
 *
 * Plugins and themes can hook into the genesis_export_options filter to add
 * their own settings to the exporter.
 *
 * @since 1.6
 *
 * @return array
 */
function genesis_get_export_options() {

	$options = array(
		'theme' => array( 'label' => __( 'Theme Settings', 'genesis' ), 'settings-field' => GENESIS_SETTINGS_FIELD ),
		'seo'   => array( 'label' => __( 'SEO Settings', 'genesis' ), 'settings-field' => GENESIS_SEO_SETTINGS_FIELD )
	);
	return (array) apply_filters( 'genesis_export_options', $options );

}

/**
 * Echo out the checkboxes for the export options.
 *
 * @since 1.6
 */
function genesis_export_checkboxes() {

	if( ! $options = genesis_get_export_options() ) {
		/** Not even the Genesis theme / seo export options were returned from the filter */
		printf( '<p><em>%s</em></p>', __( 'No export options available.', 'genesis' ) );
		return;
	}

	foreach ( $options as $name => $args ) {

		/** Ensure option item has an array key, and that label and settings-field appear populated */
		if ( is_int( $name ) || ! isset( $args['label'] ) || ! isset( $args['settings-field'] ) || '' === $args['label'] || '' === $args['settings-field'] )
			return;

		echo '<p><label><input id="genesis-export-' . esc_attr( $name ) . '" name="genesis-export[' . esc_attr( $name ) . ']" type="checkbox" value="1" /> ';
		echo esc_html( $args['label'] ) . '</label></p>' . "\n";
	}

}

add_action( 'admin_init', 'genesis_export' );
/**
 * Generate the export file, if requested, in JSON format.
 *
 * @since 1.4
 */
function genesis_export() {

	if ( ! isset($_REQUEST['page']) || $_REQUEST['page'] != 'genesis-import-export' )
		return;

	if ( empty( $_REQUEST['genesis-export'] ) )
		return;

	check_admin_referer( 'genesis-export' ); // Verify nonce

	/** hookable */
	do_action( 'genesis_export', $_REQUEST['genesis-export'] );

	$options = genesis_get_export_options();

	$settings = array();

	$prefix = array( 'genesis' );

	foreach ( $_REQUEST['genesis-export'] as $export => $value ) {
		$settings_field = $options[$export]['settings-field'];
		$settings[$settings_field] = get_option( $settings_field );
		$prefix[] = $export;
	}

	$prefix = join('-', $prefix);

	if ( ! $settings ) return;

    $output = json_encode( (array) $settings );

    header( 'Content-Description: File Transfer' );
    header( 'Cache-Control: public, must-revalidate' );
    header( 'Pragma: hack' );
    header( 'Content-Type: text/plain' );
    header( 'Content-Disposition: attachment; filename="' . $prefix . '-' . date( "Ymd-His" ) . '.json"' );
    header( 'Content-Length: ' . strlen( $output ) );
    echo $output;
    exit;

}

add_action( 'admin_init', 'genesis_import' );
/**
 * This function handles the import.
 *
 * @since 1.4
 */
function genesis_import() {

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'genesis-import-export' )
		return;

	if ( empty( $_REQUEST['genesis-import'] ) )
		return;

	check_admin_referer('genesis-import'); // Verify nonce

	/** hookable */
	do_action('genesis_import', $_REQUEST['genesis-import'], $_FILES['genesis-import-upload']);

	/** Extract file contents */
	$upload = file_get_contents($_FILES['genesis-import-upload']['tmp_name']);

	/** Decode the JSON */
	$options = json_decode( $upload, true );

	/** Check for errors */
	if ( !$options || $_FILES['genesis-import-upload']['error'] ) {
		genesis_admin_redirect( 'genesis-import-export', array( 'error' => 'true' ) );
		exit;
	}

	/** Cycle through data, import settings */
	foreach ( (array)$options as $key => $settings ) {
		update_option( $key, $settings );
	}

	/** Redirect, add success flag to the URI */
	genesis_admin_redirect( 'genesis-import-export', array( 'imported' => 'true' ) );
	exit;

}