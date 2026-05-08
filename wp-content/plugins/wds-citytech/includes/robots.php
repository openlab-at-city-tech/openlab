<?php

/**
 * Tools related to robots.txt.
 *
 * @package cbox-openlab-core
 */

namespace CBOX\OL\Robots;

const GROUP_AI_ROBOTS_CACHE_KEY = 'cboxol_group_ai_robots_directives';
const SITE_AI_ROBOTS_CACHE_KEY  = 'cboxol_site_ai_robots_directives';

/**
 * Adds AI-specific directives to robots.txt.
 *
 * @since 1.8.0
 */
function add_ai_robots_directives( $data ) {
	if ( is_multisite() && ! is_subdomain_install() ) {
		if ( ! cbox_is_main_site() ) {
			return $data;
		}

		// If the block option is enabled on the main site, no further action is necessary.
		// The site-level directive will ipso facto apply to the entire network.
		if ( is_block_ai_robots_enabled() ) {
			return $data . build_ai_robots_directives( [ '/' ] );
		}

		if ( supports_site_ai_robots_meta() ) {
			$directives = get_site_transient( SITE_AI_ROBOTS_CACHE_KEY );
		} else {
			$directives = false;
		}

		if ( false === $directives ) {
			$directives = build_site_ai_robots_directives();

			if ( supports_site_ai_robots_meta() ) {
				set_site_transient( SITE_AI_ROBOTS_CACHE_KEY, $directives, DAY_IN_SECONDS );
			}
		}

		if ( '' === $directives ) {
			return $data;
		}

		return $data . $directives;
	}

	if ( ! is_block_ai_robots_enabled() ) {
		return $data;
	}

	// Only subdomain networks will fall through to this point.
	return $data . build_ai_robots_directives( [ '/' ] );
}
add_filter( 'robots_txt', __NAMESPACE__ . '\\add_ai_robots_directives' );

/**
 * Builds AI-specific directives for a list of paths.
 *
 * @since 1.8.0
 *
 * @param array $paths Paths to disallow.
 * @return string
 */
function build_ai_robots_directives( array $paths ) {
	$paths = array_filter(
		array_map(
			function( $path ) {
				$path = (string) $path;

				if ( '' === $path ) {
					return '';
				}

				return '/' === $path ? '/' : trailingslashit( $path );
			},
			$paths
		)
	);

	$paths = array_values( array_unique( $paths ) );

	if ( empty( $paths ) ) {
		return '';
	}

	$agents_data = require __DIR__ . '/ai-crawlers/knownagents-user-agents.php';

	if ( empty( $agents_data['user_agents'] ) ) {
		return '';
	}

	$data = "\n";

	foreach ( $agents_data['user_agents'] as $agent ) {
		$data .= "User-agent: {$agent}\n";

		foreach ( $paths as $path ) {
			$data .= "Disallow: {$path}\n";
		}

		$data .= "\n";
	}

	return $data;
}

/**
 * Adds group-related AI-specific directives to robots.txt on the primary site.
 *
 * @since 1.8.0
 *
 * @param string $data Existing robots.txt contents.
 * @return string
 */
function add_group_ai_robots_directives( $data ) {
	if ( ! cbox_is_main_site() ) {
		return $data;
	}

	// If the block option is enabled on the main site, no further action is necessary.
	// The blog-level directive will ipso facto apply to all groups.
	if ( is_block_ai_robots_enabled() ) {
		return $data;
	}

	$directives = get_site_transient( GROUP_AI_ROBOTS_CACHE_KEY );

	if ( false === $directives ) {
		$directives = build_group_ai_robots_directives();
		set_site_transient( GROUP_AI_ROBOTS_CACHE_KEY, $directives, DAY_IN_SECONDS );
	}

	if ( '' === $directives ) {
		return $data;
	}

	return $data . $directives;
}
add_filter( 'robots_txt', __NAMESPACE__ . '\\add_group_ai_robots_directives' );

/**
 * Builds group-related AI-specific directives for robots.txt.
 *
 * @since 1.8.0
 *
 * @return string
 */
function build_group_ai_robots_directives() {
	$groups = groups_get_groups(
		[
			'meta_query' => [
				[
					'key'   => 'cboxol_block_ai_robots',
					'value' => '1',
				],
			],
			'per_page'   => -1,
			'fields'     => 'all',
		]
	);

	if ( empty( $groups['groups'] ) ) {
		return '';
	}

	$group_paths = [];

	foreach ( $groups['groups'] as $group ) {
		$url  = bp_get_group_url( $group );
		$path = wp_parse_url( $url, PHP_URL_PATH );

		if ( empty( $path ) ) {
			continue;
		}

		$group_paths[] = trailingslashit( $path );
	}

	$group_paths = array_values( array_unique( $group_paths ) );

	if ( empty( $group_paths ) ) {
		return '';
	}

	return build_ai_robots_directives( $group_paths );
}

/**
 * Builds site-related AI-specific directives for robots.txt on the primary site.
 *
 * @since 1.8.0
 *
 * @return string
 */
function build_site_ai_robots_directives() {
	$site_paths = [];

	if ( supports_site_ai_robots_meta() ) {
		$sites = get_sites(
			[
				'meta_key'   => 'cboxol_block_ai_robots',
				'meta_value' => '1',
				'number'     => 0,
			]
		);

		foreach ( $sites as $site ) {
			$path = wp_parse_url( get_home_url( $site->blog_id ), PHP_URL_PATH );

			if ( empty( $path ) ) {
				continue;
			}

			$site_paths[] = trailingslashit( $path );
		}
	} else {
		$site_ids = get_sites(
			[
				'fields' => 'ids',
				'number' => 0,
			]
		);

		foreach ( $site_ids as $site_id ) {
			if ( ! is_block_ai_robots_enabled( $site_id ) ) {
				continue;
			}

			$path = wp_parse_url( get_home_url( $site_id ), PHP_URL_PATH );

			if ( empty( $path ) ) {
				continue;
			}

			$site_paths[] = trailingslashit( $path );
		}
	}

	if ( empty( $site_paths ) ) {
		return '';
	}

	return build_ai_robots_directives( $site_paths );
}

/**
 * Invalidates group AI robots cache when group setting changes.
 *
 * @since 1.8.0
 *
 * @param int    $group_id   Group ID.
 * @param string $meta_key   Meta key.
 * @param mixed  $meta_value Meta value.
 * @return void
 */
function maybe_invalidate_group_ai_robots_cache( $meta_id, $group_id, $meta_key, $meta_value ) {
	unset( $meta_id, $group_id, $meta_value );

	if ( 'cboxol_block_ai_robots' !== $meta_key ) {
		return;
	}

	clear_group_ai_robots_directives_cache();
}
add_action( 'updated_group_meta', __NAMESPACE__ . '\\maybe_invalidate_group_ai_robots_cache', 10, 4 );
add_action( 'deleted_group_meta', __NAMESPACE__ . '\\maybe_invalidate_group_ai_robots_cache', 10, 4 );

/**
 * Clears cached group AI robots directives.
 *
 * @since 1.8.0
 *
 * @return void
 */
function clear_group_ai_robots_directives_cache() {
	/*
	 * BuddyPress filters the 'query' hook to replace the meta ID column name
	 * in group meta queries. Because the current callback is hooked during
	 * the process of updating/deleting group meta, the filter is still in place,
	 * which causes the `delete_site_transient()` call to fail since it relies
	 * on a direct query to the options table.
	 */
	$has_bp_filter_metaid_column_name = has_filter( 'query', 'bp_filter_metaid_column_name' );
	if ( $has_bp_filter_metaid_column_name ) {
		remove_filter( 'query', 'bp_filter_metaid_column_name' );
	}

	delete_site_transient( GROUP_AI_ROBOTS_CACHE_KEY );

	if ( $has_bp_filter_metaid_column_name ) {
		add_filter( 'query', 'bp_filter_metaid_column_name' );
	}
}

/**
 * Invalidates site AI robots cache when site setting changes.
 *
 * @since 1.8.0
 *
 * @param int|array $meta_ids   Meta ID or list of meta IDs.
 * @param int       $site_id    Site ID.
 * @param string    $meta_key   Meta key.
 * @param mixed     $meta_value Meta value.
 * @return void
 */
function maybe_invalidate_site_ai_robots_cache( $meta_ids, $site_id, $meta_key, $meta_value ) {
	unset( $meta_ids, $site_id, $meta_value );

	if ( 'cboxol_block_ai_robots' !== $meta_key ) {
		return;
	}

	clear_site_ai_robots_directives_cache();
}

if ( supports_site_ai_robots_meta() ) {
	add_action( 'added_blog_meta', __NAMESPACE__ . '\\maybe_invalidate_site_ai_robots_cache', 10, 4 );
	add_action( 'updated_blog_meta', __NAMESPACE__ . '\\maybe_invalidate_site_ai_robots_cache', 10, 4 );
	add_action( 'deleted_blog_meta', __NAMESPACE__ . '\\maybe_invalidate_site_ai_robots_cache', 10, 4 );
}

/**
 * Clears cached site AI robots directives.
 *
 * @since 1.8.0
 *
 * @return void
 */
function clear_site_ai_robots_directives_cache() {
	delete_site_transient( SITE_AI_ROBOTS_CACHE_KEY );
}

/**
 * Is the option to block AI crawlers enabled for a given site?
 *
 * @since 1.8.0
 *
 * @param int|null $site_id Optional site ID to check. Defaults to current site.
 * @return bool True if the option is enabled, false otherwise.
 */
function is_block_ai_robots_enabled( $site_id = null ) {
	$site_id = $site_id ? (int) $site_id : get_current_blog_id();

	if ( supports_site_ai_robots_meta() ) {
		return (bool) get_site_meta( $site_id, 'cboxol_block_ai_robots', true );
	}

	return (bool) get_blog_option( $site_id, 'cboxol_block_ai_robots', false );
}

/**
 * Stores the AI crawler block setting for a given site.
 *
 * @since 1.8.0
 *
 * @param int  $site_id Site ID.
 * @param bool $enabled Whether AI crawler blocking is enabled.
 * @return void
 */
function set_block_ai_robots_enabled( $site_id, $enabled ) {
	$site_id = (int) $site_id;
	$enabled = (bool) $enabled;

	if ( ! $site_id ) {
		return;
	}

	if ( supports_site_ai_robots_meta() ) {
		if ( $enabled ) {
			update_site_meta( $site_id, 'cboxol_block_ai_robots', 1 );
		} else {
			delete_site_meta( $site_id, 'cboxol_block_ai_robots' );
		}

		return;
	}

	if ( $enabled ) {
		update_blog_option( $site_id, 'cboxol_block_ai_robots', 1 );
	} else {
		delete_blog_option( $site_id, 'cboxol_block_ai_robots' );
	}
}

/**
 * Determines whether site meta can store the AI crawler setting.
 *
 * @since 1.8.0
 *
 * @return bool
 */
function supports_site_ai_robots_meta() {
	return function_exists( 'get_site_meta' ) && function_exists( 'update_site_meta' ) && function_exists( 'delete_site_meta' );
}

/**
 * Is the option to block AI crawlers enabled for the BP root site?
 *
 * @since 1.8.0
 *
 * @return bool True if the option is enabled, false otherwise.
 */
function is_block_ai_robots_enabled_on_root_site() {
	return is_block_ai_robots_enabled( cbox_get_main_site_id() );
}

/**
 * Is the option to block AI crawlers enabled for a given group?
 *
 * @since 1.8.0
 *
 * @param int|null $group_id Optional group ID to check. Defaults to current group.
 * @return bool True if the option is enabled, false otherwise.
 */
function is_block_ai_robots_enabled_for_group( $group_id = null ) {
	$group_id = $group_id ? (int) $group_id : bp_get_current_group_id();
	if ( ! $group_id ) {
		return false;
	}

	$group_option = groups_get_groupmeta( $group_id, 'cboxol_block_ai_robots', true );
	return (bool) $group_option;
}

/**
 * Saves AI crawler setting on the Reading Settings screen.
 *
 * @since 1.8.0
 *
 * @return void
 */
function save_ai_robots_setting() {
	global $pagenow;

	if ( 'options.php' !== $pagenow ) {
		return;
	}

	if ( empty( $_POST['option_page'] ) || 'reading' !== $_POST['option_page'] ) {
		return;
	}

	if ( ! isset( $_POST['cboxol_block_ai_robots'] ) ) {
		return;
	}

	check_admin_referer( 'reading-options' );

	$value = sanitize_ai_robots_setting( wp_unslash( $_POST['cboxol_block_ai_robots'] ) );
	set_block_ai_robots_enabled( get_current_blog_id(), (bool) $value );
}
add_action( 'admin_init', __NAMESPACE__ . '\\save_ai_robots_setting' );

/**
 * Sanitizes AI crawler checkbox setting.
 *
 * @since 1.8.0
 *
 * @param mixed $value Raw submitted value.
 * @return int
 */
function sanitize_ai_robots_setting( $value ) {
	return empty( $value ) ? 0 : 1;
}

/**
 * Adds checkbox to Reading settings.
 *
 * @since 1.8.0
 *
 * @return void
 */
function add_ai_robots_checkbox() {
	$checked = is_block_ai_robots_enabled();
	ai_robots_checkbox_markup( $checked );

	?>
	<style>
		.block-ai-crawlers-wrapper {
			margin-top: 1em;
		}

		.block-ai-crawlers-wrapper label {
			font-weight: bold;
		}
	</style>
	<?php
}
add_action( 'blog_privacy_selector', __NAMESPACE__ . '\\add_ai_robots_checkbox', 50 );

/**
 * Markup for checkbox to block AI crawlers.
 *
 * @since 1.8.0
 *
 * @param bool $checked Whether the checkbox should be checked.
 * @return void
 */
function ai_robots_checkbox_markup( $checked ) {
	?>

	<div class="block-ai-crawlers-wrapper">
		<input type="hidden" name="cboxol_block_ai_robots" value="0" />
		<label for="block-ai-crawlers">
			<input type="checkbox" name="cboxol_block_ai_robots" id="block-ai-crawlers" value="1" <?php checked( $checked, true ); ?> />
			<?php esc_html_e( 'Ask AI crawlers not to access this site.', 'cbox-openlab-core' ); ?>
		</label>

		<p class="description group-settings-note italics note">
			<?php esc_html_e( 'Note: This option will NOT block access to the site. It is up to AI crawlers to honor your request.', 'cbox-openlab-core' ); ?>
		</p>
	</div>

	<?php
}
