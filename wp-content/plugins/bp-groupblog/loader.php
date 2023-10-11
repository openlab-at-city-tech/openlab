<?php
/**
 * Plugin Name: BP Groupblog
 * Plugin URI: https://wordpress.org/plugins/bp-groupblog/
 * Description: Automates and links WPMU blogs groups controlled by the group creator.
 * Author: Rodney Blevins, Marius Ooms, Boone Gorges
 * Version: 1.9.3
 * License: (Groupblog: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
 * Network: true
 * Text Domain: bp-groupblog
 * Domain Path: /languages
 *
 * @package BP_Groupblog
 */

/**
 * Loads BuddyPress Groupblog only when BP is active
 *
 * @since 1.6
 */
function bp_groupblog_init() {
	// BP Groupblog requires multisite.
	if ( ! is_multisite() ) {
		return;
	}

	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

	require_once dirname( __FILE__ ) . '/bp-groupblog.php';
}
add_action( 'bp_include', 'bp_groupblog_init' );
