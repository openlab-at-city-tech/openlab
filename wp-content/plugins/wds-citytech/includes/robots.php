<?php

/**
 * Tools related to robots.txt.
 *
 * @package cbox-openlab-core
 */

namespace CBOX\OL\Robots;

const GROUP_AI_ROBOTS_CACHE_KEY = 'cboxol_group_ai_robots_directives';

/**
 * Adds AI-specific directives to robots.txt.
 *
 * @since 1.8.0
 */
function add_ai_robots_directives( $data ) {
	if ( ! is_block_ai_robots_enabled() ) {
		return $data;
	}

	$agents_data = require __DIR__ . '/ai-crawlers/knownagents-user-agents.php';

	foreach ( $agents_data['user_agents'] as $agent ) {
		$data .= "\n";
		$data .= "User-agent: {$agent}\n";
		$data .= "Disallow: /\n";
	}

	return $data;
}
add_filter( 'robots_txt', __NAMESPACE__ . '\\add_ai_robots_directives' );

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
	$agents_data = require __DIR__ . '/ai-crawlers/knownagents-user-agents.php';

	if ( empty( $agents_data['user_agents'] ) ) {
		return '';
	}

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

	$data = "\n";

	foreach ( $agents_data['user_agents'] as $agent ) {
		$data .= "User-agent: {$agent}\n";

		foreach ( $group_paths as $group_path ) {
			$data .= "Disallow: {$group_path}\n";
		}

		$data .= "\n";
	}

	return $data;
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
function maybe_invalidate_group_ai_robots_cache( $group_id, $meta_key, $meta_value ) {
	unset( $group_id, $meta_value );

	if ( 'cboxol_block_ai_robots' !== $meta_key ) {
		return;
	}

	clear_group_ai_robots_directives_cache();
}
add_action( 'groups_update_groupmeta', __NAMESPACE__ . '\\maybe_invalidate_group_ai_robots_cache', 10, 3 );
add_action( 'groups_delete_groupmeta', __NAMESPACE__ . '\\maybe_invalidate_group_ai_robots_cache', 10, 3 );

/**
 * Clears cached group AI robots directives.
 *
 * @since 1.8.0
 *
 * @return void
 */
function clear_group_ai_robots_directives_cache() {
	delete_site_transient( GROUP_AI_ROBOTS_CACHE_KEY );
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
	return (bool) get_blog_option( $site_id, 'cboxol_block_ai_robots', false );
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
 * Registers setting for blocking AI crawlers.
 *
 * @since 1.8.0
 *
 * @return void
 */
function register_ai_robots_setting() {
	register_setting(
		'reading',
		'cboxol_block_ai_robots',
		[
			'type'              => 'boolean',
			'sanitize_callback' => __NAMESPACE__ . '\\sanitize_ai_robots_setting',
			'default'           => false,
		]
	);
}
add_action( 'admin_init', __NAMESPACE__ . '\\register_ai_robots_setting' );

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
