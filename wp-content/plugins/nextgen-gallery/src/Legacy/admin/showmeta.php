<?php

// look up for the path.
require_once dirname( __DIR__ ) . '/ngg-config.php';

require_once NGGALLERY_ABSPATH . '/lib/meta.php';
require_once NGGALLERY_ABSPATH . '/lib/image.php';

if ( ! is_user_logged_in() ) {
	die( esc_html__( 'Cheatin&#8217; uh?', 'nggallery' ) );
}

if ( ! current_user_can( 'NextGEN Manage gallery' ) ) {
	die( esc_html__( 'Cheatin&#8217; uh?', 'nggallery' ) );
}

if ( ! isset( $_GET['id'] ) ) {
	die( esc_html( __( 'Permission denied', 'nggallery' ) ) );
}

if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ngg_meta_popup' ) ) {
	die( esc_html( __( 'Permission denied', 'nggallery' ) ) );
}

global $wpdb;

$id = (int) $_GET['id'];
// let's get the meta data'.

$meta     = new nggMeta( $id );
$dbdata   = $meta->get_saved_meta();
$exifdata = $meta->get_EXIF();
$iptcdata = $meta->get_IPTC();
$xmpdata  = $meta->get_XMP();
$class    = '';

?>
	<!-- META DATA -->
	<fieldset class="options nggallery">
	<h3><?php esc_html_e( 'Meta Data', 'nggallery' ); ?></h3>
	<?php if ( $dbdata ) { ?>
		<table id="the-list-x" width="100%" cellspacing="3" cellpadding="3">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Tag', 'nggallery' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Value', 'nggallery' ); ?></th>
				</tr>
			</thead>
		<?php
		foreach ( $dbdata as $key => $value ) {
			if ( in_array( $key, [ 'created_timestamp', 'timestamp' ] ) && is_numeric( $value ) ) {
				$value = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $value );
			}
			if ( is_array( $value ) ) {
				continue;
			}
			$class = ( $class == 'class="alternate"' ) ? '' : 'class="alternate"';
			echo '<tr ' . $class . '>
						<td style="width:230px">' . esc_html( $meta->i18n_name( $key ) ) . '</td>
						<td>' . esc_html( $value ) . '</td>
					</tr>';
		}
		?>
		</table>
		<?php
	} else {
		echo '<strong>' . esc_html__( 'No meta data saved', 'nggallery' ) . '</strong>';}
	?>
	</fieldset>

	<!-- EXIF DATA -->
	<?php if ( $exifdata ) { ?>
	<fieldset class="options nggallery">
	<h3><?php esc_html_e( 'EXIF Data', 'nggallery' ); ?></h3>
		<?php if ( $exifdata ) { ?>
		<table id="the-list-x" width="100%" cellspacing="3" cellpadding="3">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Tag', 'nggallery' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Value', 'nggallery' ); ?></th>
				</tr>
			</thead>
			<?php
			foreach ( $exifdata as $key => $value ) {
				if ( in_array( $key, [ 'created_timestamp', 'timestamp' ] ) && is_numeric( $value ) ) {
					$value = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $value );
				}
				if ( $key == 'created_date' ) {
					$value = date_i18n( get_option( 'date_format' ), strtotime( $value ) );
				}
				$class = ( $class == 'class="alternate"' ) ? '' : 'class="alternate"';
				echo '<tr ' . $class . '>
						<td style="width:230px">' . esc_html( $meta->i18n_name( $key ) ) . '</td>
						<td>' . esc_html( $value ) . '</td>
					</tr>';
			}
			?>
		</table>
			<?php
		} else {
			echo '<strong>' . esc_html__( 'No exif data', 'nggallery' ) . '</strong>';}
		?>
	</fieldset>
	<?php } ?>

	<!-- IPTC DATA -->
	<?php if ( $iptcdata ) { ?>
	<fieldset class="options nggallery">
	<h3><?php esc_html_e( 'IPTC Data', 'nggallery' ); ?></h3>
		<table id="the-list-x" width="100%" cellspacing="3" cellpadding="3">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Tag', 'nggallery' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Value', 'nggallery' ); ?></th>
				</tr>
			</thead>
		<?php
		foreach ( $iptcdata as $key => $value ) {
			if ( in_array( $key, [ 'created_timestamp', 'timestamp' ] ) && is_numeric( $value ) ) {
				$value = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $value );
			}
			$class = ( $class == 'class="alternate"' ) ? '' : 'class="alternate"';
			echo '<tr ' . $class . '>
						<td style="width:230px">' . esc_html( $meta->i18n_name( $key ) ) . '</td>
						<td>' . esc_html( $value ) . '</td>
					</tr>';
		}
		?>
		</table>
	</fieldset>
	<?php } ?>

	<!-- XMP DATA -->
	<?php if ( $xmpdata ) { ?>
	<fieldset class="options nggallery">
	<h3><?php esc_html_e( 'XMP Data', 'nggallery' ); ?></h3>
		<table id="the-list-x" width="100%" cellspacing="3" cellpadding="3">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Tag', 'nggallery' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Value', 'nggallery' ); ?></th>
				</tr>
			</thead>
		<?php
		foreach ( $xmpdata as $key => $value ) {
			if ( in_array( $key, [ 'created_timestamp', 'timestamp' ] ) && is_numeric( $value ) ) {
				$value = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $value );
			}
			$class = ( $class == 'class="alternate"' ) ? '' : 'class="alternate"';
			echo '<tr ' . $class . '>
						<td style="width:230px">' . esc_html( $meta->i18n_name( $key ) ) . '</td>
						<td>' . esc_html( $value ) . '</td>
					</tr>';
		}
		?>
		</table>
	</fieldset>
	<?php } ?>
