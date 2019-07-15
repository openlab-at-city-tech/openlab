<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // don't access directly
};

/**
 * Is this a fresh installation of Multipage?
 *
 * If there is no raw DB version, we infer that this is the first installation.
 *
 * @since 1.4
 *
 * @return bool True if this is a fresh install, otherwise false.
 */
function mpp_is_install() {
	return ! mpp_get_db_version_raw();
}

/**
 * Is this a Multipage update?
 *
 * Determined by comparing the registered Multipage version to the version
 * number stored in the database. If the registered version is greater, it's
 * an update.
 *
 * @since 1.4
 *
 * @return bool True if update, otherwise false.
 */
function mpp_is_update() {

	// Current DB version of this site (per site in a multisite network).
	$current_db   = get_option( '_mpp_db_version' );
	$current_live = mpp_get_db_version();

	// Compare versions (cast as int and bool to be safe).
	$is_update = (bool) ( (int) $current_db < (int) $current_live );

	// Return the product of version comparison.
	return $is_update;
}

/**
 * Determine whether Multipage is in the process of being deactivated.
 *
 * @since 1.4
 *
 * @param string $basename Multipage basename.
 * @return bool True if deactivating Multipage, false if not.
 */
function mpp_is_deactivation( $basename = '' ) {
	$mpp     = multipage();
	$action = false;

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' != $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' != $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail if not deactivating.
	if ( empty( $action ) || !in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being deactivated.
	if ( 'deactivate' == $action ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty.
	if ( empty( $basename ) && !empty( $mpp->basename ) ) {
		$basename = $mpp->basename;
	}

	// Bail if no basename.
	if ( empty( $basename ) ) {
		return false;
	}

	// Is bbPress being deactivated?
	return in_array( $basename, $plugins );
}

/**
 * Update the Multipage version stored in the database to the current version.
 *
 * @since 1.4
 */
function mpp_version_bump() {
	update_option( '_mpp_db_version', mpp_get_db_version() );
}

/**
 * Set up the Multipage updater.
 *
 * @since 1.4
 */
function mpp_setup_updater() {

	// Are we running an outdated version of Multipage?
	if ( ! mpp_is_update() ) {
		return;
	}

	mpp_version_updater();
}

/**
 * Initialize an update or installation of Multipage.
 *
 * Multipage's version updater looks at what the current database version is,
 * and runs whatever other code is needed - either the "update" or "install"
 * code.
 *
 * @since 1.4
 */
function mpp_version_updater() {

	// Get the raw database version.
	$raw_db_version = (int) mpp_get_db_version_raw();

	// Install Multipage.
	if ( mpp_is_install() ) {

		// Do everything you have to do installing Multipage.

	// Upgrades.
	} else {

		// Version 1.4.
		if ( $raw_db_version < 1000 ) {
			mpp_update_to_1_4();
		}
	}

	/* All done! *************************************************************/

	// Bump the version.
	mpp_version_bump();
}

/**
 * 1.4 update routine.
 *
 * - Add postmeta value.
 *
 * @since 1.4
 */
function mpp_update_to_1_4() {
	// Add multipage postmeta value to old multipage posts.
	mpp_add_post_multipage_meta();

	// Convert old settings schema.
	$old_settings = get_option( 'multipage' );

	if ( ! $old_settings )
		return;

	foreach ( $old_settings as $option_key => $option_value ) {
		if ( isset( $option_value ) && '' != $option_value ) {
			if ( $option_key == 'comments-oofp' && null == get_option( 'mpp-comments-on-page' ) )
				add_option( 'mpp-comments-on-page', 'first-page' );

			if ( $option_key == 'unhide-pagination' && null == get_option( 'mpp-disable-standard-pagination' ) ) {
				if ( $option_value == true ) {
					add_option( 'mpp-disable-standard-pagination', 0 );
				}
			}

			if ( $option_key == 'toc-oofp' && null == get_option( 'mpp-toc-only-on-the-first-page' ) )
				add_option( 'mpp-toc-only-on-the-first-page', $option_value );

			if ( $option_key == 'toc-position' && null == get_option( 'mpp-toc-position' ) )
				add_option( 'mpp-toc-position', $option_value );

			if ( $option_key == 'toc-page-labels' && null == get_option( 'mpp-toc-row-labels' ) ) {
				if ( $option_value == 'pages' ) {
					add_option( 'mpp-toc-row-labels', 'page' );
				} elseif ( $option_value == 'numbers' ) {
					add_option( 'mpp-toc-row-labels', 'number' );
				}
				elseif ( $option_value == 'hidden' ) {
					add_option( 'mpp-toc-row-labels', $option_value );
				}
			}

			if ( $option_key == 'toc-hide-header' && null == get_option( 'mpp-hide-toc-header' ) )
				add_option( 'mpp-hide-toc-header', $option_value );

			if ( $option_key == 'toc-comments-link' && null == get_option( 'mpp-comments-toc-link' ) )
				add_option( 'mpp-comments-toc-link', $option_value );

			if ( $option_key == 'rewrite-title-priority' && null == get_option( '_mpp-rewrite-title-priority' ) ) {
				if ( $option_value == 'highest' ) {
					add_option( '_mpp-rewrite-title-priority', '100' );
				}
				elseif ( $option_value == 'high' ) {
					add_option( '_mpp-rewrite-title-priority', '50' );
				}
				elseif ( $option_value == 'low' ) {
					add_option( '_mpp-rewrite-title-priority', '10' );
				}
				elseif ( $option_value == 'lowest' ) {
					add_option( '_mpp-rewrite-title-priority', '5' );
				}
			}

			if ( $option_key == 'rewrite-content-priority' && null == get_option( '_mpp-rewrite-content-priority' ) ) {
				if ( $option_value == 'highest' ) {
					add_option( '_mpp-rewrite-content-priority', '100' );
				}
				elseif ( $option_value == 'high' ) {
					add_option( '_mpp-rewrite-content-priority', '50' );
				}
				elseif ( $option_value == 'low' ) {
					add_option( '_mpp-rewrite-content-priority', '10' );
				}
				elseif ( $option_value == 'lowest' ) {
					add_option( '_mpp-rewrite-content-priority', '5' );
				}
			}

			if ( $option_key == 'disable-tinymce-buttons' && null == get_option( 'mpp-disable-tinymce-buttons' ) )
				add_option( 'mpp-disable-tinymce-buttons', $option_value );
		}
	}

	// Remove the old options.
	delete_option( 'multipage' );
}

/**
 * Update WP postsmeta with a new value for multipage posts that will contain all relevant infos about subpages.
 *
 * @since 1.4
 */
function mpp_add_post_multipage_meta( $update_option = true ) {
	global $wpdb;

	$where = $wpdb->prepare("post_content LIKE %s", '%[nextpage title=%');
	$mpp_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE $where AND post_status != 'auto-draft' AND post_status != 'draft' AND post_type != 'revision'" );

	foreach ( $mpp_ids as $mpp_id ) {
		$post = get_post( $mpp_id );
		$_mpp_data = MPP_Admin::multipage_return_array( $post->post_content );
		update_post_meta( $mpp_id, '_mpp_data', $_mpp_data );
	}
	
	if ( $update_option === true )
		update_option( '_mpp-postmeta-built', current_time( 'timestamp' ) );
}
