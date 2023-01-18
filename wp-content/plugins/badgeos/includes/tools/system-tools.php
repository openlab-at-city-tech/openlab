<?php
/**
 * System Tools
 *
 * @package badgeos
 * @subpackage Tools
 * @author LearningTimes, LLC
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 * @link https://badgeos.org
 */

global $wpdb;

/**
 * Get WordPress theme info
 */
$theme_data   = wp_get_theme();
$theme        = $theme_data->name . ' (' . $theme_data->version . ')';
$parent_theme = $theme_data->template;

if ( ! empty( $parent_theme ) ) {
	$parent_theme_data = wp_get_theme( $parent_theme );
	$parent_theme      = $parent_theme_data->name . ' (' . $parent_theme_data->version . ')';
}

/**
 * Retrieve current plugin information
 */
if ( ! function_exists( 'get_plugins' ) ) {
	include ABSPATH . '/wp-admin/includes/plugin.php';
}

$active_plugins = get_option( 'active_plugins', array() );
if ( ! is_array( $active_plugins ) ) {
	$active_plugins = array();
}

$active_plugins_output = array();
if ( isset( $active_plugins ) && count( $active_plugins ) > 0 ) {
	foreach ( get_plugins() as $plugin_path => $plugin ) {

		/**
		 * If the plugin isn't active, don't show it.
		 */
		if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
			continue;
		}

		$active_plugins_output[] = $plugin['Name'] . ' (' . $plugin['Version'] . ')';
	}
}

$points_types        = badgeos_get_point_types();
$points_types_output = array();

if ( ! empty( $points_types ) ) {
	foreach ( $points_types as $key => $item ) {
		$points_types_output[] = $item->post_title;
	}
}

if ( isset( $GLOBALS['badgeos']->achievement_types ) ) {
	$achievement_types = $GLOBALS['badgeos']->achievement_types;
} else {
	$achievement_types = array();
}

$achievement_types_output = array();

if ( ! empty( $achievement_types ) ) {
	foreach ( $achievement_types as $achievement_type_slug => $achievement_type ) {

		$achievement_types_output[] = $achievement_type['single_name'];
	}
}

/**
 * Get all rank types
 */
if ( isset( $GLOBALS['badgeos']->ranks_types ) ) {
	$rank_types = $GLOBALS['badgeos']->ranks_types;
} else {
	$rank_types = array();
}
$rank_types_output = array();

if ( ! empty( $rank_types ) ) {
	foreach ( $rank_types as $rank_type_slug => $rank_type ) {

		$rank_types_output[] = $rank_type['plural_name'];
	}
}

/**
 * Show symbol on tool page
 *
 * @param string $type string.
 */
function show_symbol_on_tool_page( $type ) {
	if ( 'success' === $type ) {
		echo '<div class="badgeos_success_symbol">&nbsp;</div>';
	} elseif ( 'failure' === $type ) {
		echo '<div class="badgeos_failure_symbol">&nbsp;</div>';
	}
}

/**
 * Return the hosting provider this site is using if possible
 *
 * Taken from Easy Digital Downloads
 *
 * @since 1.1.5
 *
 * @return mixed string $host if detected, false otherwise
 */
function badgeos_get_hosting_provider() {
	$host = false;

	if ( defined( 'WPE_APIKEY' ) ) {
		$host = 'WP Engine';
	} elseif ( defined( 'PAGELYBIN' ) ) {
		$host = 'Pagely';
	} elseif ( DB_HOST === 'localhost:/tmp/mysql5.sock' ) {
		$host = 'ICDSoft';
	} elseif ( 'mysqlv5' === DB_HOST ) {
		$host = 'NetworkSolutions';
	} elseif ( strpos( DB_HOST, 'ipagemysql.com' ) !== false ) {
		$host = 'iPage';
	} elseif ( strpos( DB_HOST, 'ipowermysql.com' ) !== false ) {
		$host = 'IPower';
	} elseif ( strpos( DB_HOST, '.gridserver.com' ) !== false ) {
		$host = 'MediaTemple Grid';
	} elseif ( strpos( DB_HOST, '.pair.com' ) !== false ) {
		$host = 'pair Networks';
	} elseif ( strpos( DB_HOST, '.stabletransit.com' ) !== false ) {
		$host = 'Rackspace Cloud';
	} elseif ( strpos( DB_HOST, '.sysfix.eu' ) !== false ) {
		$host = 'SysFix.eu Power Hosting';
	} elseif ( isset( $_SERVER['SERVER_NAME'] ) && strpos( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ), 'Flywheel' ) !== false ) {
		$host = 'Flywheel';
	} else {
		$host = 'DBH: ' . DB_HOST . ', SRV: ' . sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) );
	}

	return $host;
}

$badgeos_settings = badgeos_utilities::get_option( 'badgeos_settings' );

$logs        = wp_count_posts( 'badgeos-log-entry' );
$logs_exists = ( intval( $logs->publish ) > 0 ) ? true : false;

$minimum_role             = ( isset( $badgeos_settings['minimum_role'] ) ) ? $badgeos_settings['minimum_role'] : 'manage_options';
$debug_mode               = ( isset( $badgeos_settings['debug_mode'] ) && 'disabled' !== $badgeos_settings['debug_mode'] ) ? $badgeos_settings['debug_mode'] : 'disabled';
$log_entries              = ( isset( $badgeos_settings['log_entries'] ) && 'disabled' !== $badgeos_settings['log_entries'] ) ? $badgeos_settings['log_entries'] : 'disabled';
$ms_show_all_achievements = ( isset( $badgeos_settings['ms_show_all_achievements'] ) ) ? $badgeos_settings['ms_show_all_achievements'] : 'disabled';
$remove_data_on_uninstall = ( isset( $badgeos_settings['remove_data_on_uninstall'] ) && 'on' === $badgeos_settings['remove_data_on_uninstall'] ) ? $badgeos_settings['remove_data_on_uninstall'] : '';

?>
<div id="system-tabs">
	<div class="tab-title"><?php esc_attr_e( 'System Tools', 'badgeos' ); ?></div>
	<ul>
		<li>
			<a href="#server_info">
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-server" aria-hidden="true"></i>&nbsp;&nbsp;
				<?php esc_attr_e( 'Server Info', 'badgeos' ); ?>
			</a>
		</li>
		<li>
			<a href="#php_config">
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-cog" aria-hidden="true"></i>&nbsp;&nbsp;
				<?php esc_attr_e( 'PHP Configuration', 'badgeos' ); ?>
			</a>
		</li>
		<li>
			<a href="#wordpress_info">
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-wordpress" aria-hidden="true"></i>&nbsp;&nbsp;
				<?php esc_attr_e( 'WordPress Info', 'badgeos' ); ?>
			</a>
		</li>
		<li>
			<a href="#badgeos_info">
				&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-circle-o-notch" aria-hidden="true"></i>&nbsp;&nbsp;
				<?php esc_attr_e( 'BadgeOS Info', 'badgeos' ); ?>
			</a>
		</li>
	</ul>
	<div id="server_info">
		<div class="tab-title"><?php esc_attr_e( 'Server Info', 'badgeos' ); ?></div>
		<table cellspacing="0">
			<tbody>
			<tr>
				<th scope="row">
					<label for="hosting_provider">
						<?php esc_attr_e( 'Hosting Provider:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( badgeos_get_hosting_provider() ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="php_version">
						<?php esc_attr_e( 'PHP Version:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( phpversion() ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="msql_version">
						<?php esc_attr_e( 'MySQL Version:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( $wpdb->db_version() ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="web_server_info">
						<?php esc_attr_e( 'Webserver Info:', 'badgeos' ); ?>
					</label>
				</th>
				<td>
					<?php
					$server_name = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
					echo esc_html( $server_name );
					?>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div id="php_config">
		<div class="tab-title"><?php esc_attr_e( 'PHP Configuration', 'badgeos' ); ?></div>
		<table cellspacing="0">
			<tbody>
			<tr>
				<th scope="row">
					<label for="memory_limit">
						<?php esc_attr_e( 'Memory Limit:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( ini_get( 'memory_limit' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="time_limit">
						<?php esc_attr_e( 'Time Limit:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( ini_get( 'max_execution_time' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="upload_max_size">
						<?php esc_attr_e( 'Upload Max Size:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( ini_get( 'upload_max_filesize' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="post_max_size">
						<?php esc_attr_e( 'Post Max Size:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( ini_get( 'post_max_size' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="max_input_vars">
						<?php esc_attr_e( 'Max Input Vars:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( ini_get( 'max_input_vars' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row"><label for="display_errors">
						<?php esc_attr_e( 'Display Errors:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ); ?></td>
			</tr>
			</tbody>
		</table>
	</div>
	<div id="wordpress_info">
		<div class="tab-title"><?php esc_attr_e( 'WordPress Info', 'badgeos' ); ?></div>
		<table cellspacing="0">
			<tbody>
			<tr>
				<th scope="row"><label for="site_url">
						<?php esc_attr_e( 'Site URL:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( site_url() ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="home_url">
						<?php esc_attr_e( 'Home URL:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( home_url() ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="multisite">
						<?php esc_attr_e( 'Multisite', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( is_multisite() ? 'Yes' : 'No' ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="version">
						<?php esc_attr_e( 'Version', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="language">
						<?php esc_attr_e( 'Language:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( get_locale() ); ?></td>
			</tr>
			<tr>
				<th scope="row"><label for="permalink_structure">
						<?php esc_attr_e( 'Permalink Structure:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( badgeos_utilities::get_option( 'permalink_structure' ) ? badgeos_utilities::get_option( 'permalink_structure' ) : 'Default' ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="absolute_path">
						<?php esc_attr_e( 'Absolute Path:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( ABSPATH ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="debug">
						<?php esc_attr_e( 'Debug:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="memory_limit">
						<?php esc_attr_e( 'Memory Limit:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( WP_MEMORY_LIMIT ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="table_prefix">
						<?php esc_attr_e( 'Table Prefix:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( $wpdb->prefix ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="active_theme">
						<?php esc_attr_e( 'Active Theme:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( $theme ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="parent_theme">
						<?php esc_attr_e( 'Parent Theme:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( $parent_theme ); ?></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="active_plugins">
						<?php esc_attr_e( 'Active Plugins:', 'badgeos' ); ?>
					</label>
				</th>
				<td><?php echo esc_html( implode( ',  ', $active_plugins_output ) ); ?></td>
			</tr>
			</tbody>
		</table>
	</div>
	<div id="badgeos_info">
		<div class="tab-title"><?php esc_attr_e( 'BadgeOS Info', 'badgeos' ); ?></div>
		<table cellspacing="0">
			<tbody>
				<tr>
					<th scope="row">
						<label for="point_types">
							<?php esc_attr_e( 'Points Types:', 'badgeos' ); ?>
						</label>
					</th>
					<td><?php echo esc_html( implode( ', ', $points_types_output ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="achievement_types">
							<?php esc_attr_e( 'Achievement Types:', 'badgeos' ); ?>
						</label>
					</th>
					<td><?php echo esc_html( implode( ', ', $achievement_types_output ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="rank_types">
							<?php esc_attr_e( 'Rank Types:', 'badgeos' ); ?>
						</label>
					</th>
					<td><?php echo esc_html( implode( ', ', $rank_types_output ) ); ?></td>
				</tr>
				<tr>
					<th scope="row">
						<label for="min_role_to-administrate">
							<?php esc_attr_e( 'Min Role to Administer Plugin?', 'badgeos' ); ?>
						</label>
					</th>
					<td><?php echo esc_html( $minimum_role ); ?></td>
				</tr>
				<tr>
					<th scope="row">
						<label for="log_enabled">
							<?php esc_attr_e( 'Log Enabled: ', 'badgeos' ); ?>
						</label>
					</th>
					<td><?php echo esc_html( ( 'disabled' !== $log_entries ) ? show_symbol_on_tool_page( 'success' ) : show_symbol_on_tool_page( 'failure' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row">
						<label for="log_exists">
							<?php esc_attr_e( 'Log Exists: ', 'badgeos' ); ?>
						</label>
					</th>
					<td><?php echo esc_html( $logs_exists ? show_symbol_on_tool_page( 'success' ) : show_symbol_on_tool_page( 'failure' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row">
						<label for="debug_mode">
							<?php esc_attr_e( 'Debug Mode?', 'badgeos' ); ?>
						</label>
					</th>
					<td><?php echo esc_html( ( 'disabled' !== $debug_mode ) ? show_symbol_on_tool_page( 'success' ) : show_symbol_on_tool_page( 'failure' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row">
						<label for="delete_data_on_uninstall">
							<?php esc_attr_e( 'Delete Data on Uninstall? ', 'badgeos' ); ?>
						</label>
					</th>
					<td><?php echo esc_html( ( 'on' === $remove_data_on_uninstall ) ? show_symbol_on_tool_page( 'success' ) : show_symbol_on_tool_page( 'failure' ) ); ?></td>
				</tr>
				<?php do_action( 'badgeos_tools_badgeos_information', $badgeos_settings ); ?>
			</tbody>
		</table>
	</div>
</div>
