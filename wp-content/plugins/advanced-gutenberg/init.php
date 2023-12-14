<?php

defined( 'ABSPATH' ) || die;

require_once( plugin_dir_path( __FILE__ ) . '/incl/utilities-main.php' );
require_once( plugin_dir_path( __FILE__ ) . '/incl/block-settings-main.php' );
require_once( plugin_dir_path( __FILE__ ) . '/incl/block-controls-main.php' );
require_once( plugin_dir_path( __FILE__ ) . '/incl/advanced-gutenberg-main.php' );
new AdvancedGutenbergMain();

if ( ! function_exists( 'advg_language_domain_init' ) ) {
	/**
	 * Load language translations
	 *
	 * @return void
	 */
	function advg_language_domain_init() {
		// First, unload textdomain - Based on https://core.trac.wordpress.org/ticket/34213#comment:26
		unload_textdomain( 'advanced-gutenberg' );

		// Load override language file first if available from version 2.3.11 and older
		if ( file_exists( WP_LANG_DIR . '/plugins/' . 'advanced-gutenberg' . '-' . get_locale() . '.override.mo' ) ) {
			load_textdomain(
				'advanced-gutenberg',
				WP_LANG_DIR . '/plugins/' . 'advanced-gutenberg' . '-' . get_locale() . '.override.mo'
			);
		}

		// Call the core translations from plugins languages/ folder
		if ( file_exists( plugin_dir_path( __FILE__ ) . 'languages/' . 'advanced-gutenberg' . '-' . get_locale() . '.mo' ) ) {
			load_textdomain(
				'advanced-gutenberg',
				plugin_dir_path( __FILE__ ) . 'languages/' . 'advanced-gutenberg' . '-' . get_locale() . '.mo'
			);
		}

		wp_set_script_translations(
			'editor',
			'advanced-gutenberg',
			plugin_dir_path( __FILE__ ) . 'languages'
		);
	}
}
add_action( 'init', 'advg_language_domain_init' );

if ( ! function_exists( 'advg_check_legacy_widget_block_init' ) ) {
	/**
	 * Check if widget blocks exists in current user role through advgb_blocks_user_roles option,
	 * either in inactive_blocks or active_blocks array.
	 * https://github.com/publishpress/PublishPress-Blocks/issues/756#issuecomment-932358037
	 *
	 * @return void
	 * @since 3.1.4.2 - Added support for core/widget-group block
	 *
	 * This function can be used in future to add new blocks not available on widgets.php
	 *
	 * @since 2.11.0
	 */
	function advg_check_legacy_widget_block_init() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return false;
		}

		$widget_blocks = [
			'core/legacy-widget',
			'core/widget-group'
		];

		global $wp_version;
		global $pagenow;
		if ( ( $pagenow === 'widgets.php' || $pagenow === 'customize.php' ) && $wp_version >= 5.8 ) {
			$advgb_blocks_list       = get_option( 'advgb_blocks_list' ) && ! empty( get_option( 'advgb_blocks_list' ) ) ? get_option( 'advgb_blocks_list' ) : [];
			$advgb_blocks_user_roles = get_option( 'advgb_blocks_user_roles' ) && ! empty( get_option( 'advgb_blocks_user_roles' ) ) ? get_option( 'advgb_blocks_user_roles' ) : [];
			$current_user            = wp_get_current_user();
			$current_user_role       = $current_user->roles[0];

			if ( count( $advgb_blocks_list ) && count( $advgb_blocks_user_roles ) ) {
				foreach ( $widget_blocks as $item ) {
					if ( is_array( $advgb_blocks_user_roles[ $current_user_role ]['active_blocks'] )
					     && is_array( $advgb_blocks_user_roles[ $current_user_role ]['inactive_blocks'] )
					     && ! in_array( $item, $advgb_blocks_user_roles[ $current_user_role ]['active_blocks'] )
					     && ! in_array( $item, $advgb_blocks_user_roles[ $current_user_role ]['inactive_blocks'] )
					     && ! empty( $current_user_role )
					) {
						array_push(
							$advgb_blocks_user_roles[ $current_user_role ]['active_blocks'],
							$item
						);
						update_option( 'advgb_blocks_user_roles', $advgb_blocks_user_roles, false );
					}
				}
			}
		}
	}
}
add_action( 'admin_init', 'advg_check_legacy_widget_block_init' );

// @todo - Remove this in 4.0
if ( ! function_exists( 'advgb_some_specific_updates' ) ) {
	function advgb_some_specific_updates() {
		// Run the updates from here
		$advgb_current_version = get_option( 'advgb_version', '0.0.0' );
		global $wpdb;

		// Migrate to Block Access by User Roles
		if ( version_compare( $advgb_current_version, '2.10.2', 'lt' ) && ! get_option( 'advgb_blocks_user_roles' ) ) {
			// Migrate Block Access Profiles to Block Access by Roles
			global $wpdb;
			$profiles = $wpdb->get_results(
				'SELECT * FROM ' . $wpdb->prefix . 'posts
                WHERE post_type="advgb_profiles" AND post_status="publish" ORDER BY post_date_gmt DESC'
			);

			if ( ! empty( $profiles ) ) {
				// Let's extract the user roles associated to Block Access profiles (we can't get all the user roles with regular WP way)
				$user_role_accesses = array();
				foreach ( $profiles as $profile ) {
					$postID               = $profile->ID;
					$user_role_accesses[] = get_post_meta( $postID, 'roles_access', true );
				}

				$user_role_accesses = call_user_func_array( 'array_merge', $user_role_accesses );
				$user_role_accesses = array_unique( $user_role_accesses );

				// Find the most recent profile of each user role
				$blocks_by_role_access = array();
				foreach ( $user_role_accesses as $user_role_access ) {
					$profiles = $wpdb->get_results(
						'SELECT * FROM ' . $wpdb->prefix . 'posts
                        WHERE post_type="advgb_profiles" AND post_status="publish" ORDER BY post_date_gmt DESC'
					);

					if ( ! empty( $profiles ) ) {
						$centinel[ $user_role_access ] = false; // A boolean to get the first profile (newest) and skip the rest
						foreach ( $profiles as $profile ) {
							if ( $centinel[ $user_role_access ] === false ) {
								$postID       = $profile->ID;
								$roles_access = get_post_meta( $postID, 'roles_access', true );
								$blocks       = get_post_meta( $postID, 'blocks', true );

								if ( in_array( $user_role_access, $roles_access ) ) {
									$blocks_by_role_access[ $user_role_access ] = $blocks;
									$centinel[ $user_role_access ]              = true;
								}
							}
						}
					}
				}

				// Migrate Block Access by Profile to Block Access by Role
				if ( $blocks_by_role_access ) {
					update_option( 'advgb_blocks_user_roles', $blocks_by_role_access, false );
				}
			}
		}

		// Set version if needed
		if ( $advgb_current_version !== ADVANCED_GUTENBERG_VERSION ) {
			update_option( 'advgb_version', ADVANCED_GUTENBERG_VERSION );
		}
	}

	add_action( 'init', 'advgb_some_specific_updates' );
}