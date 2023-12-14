<?php
/**
 * BP Classic Core Functions.
 *
 * @package bp-classic\inc\core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add support for a top-level ("root") component.
 *
 * This function originally (pre-1.5) let plugins add support for pages in the
 * root of the install. These root level pages are now handled by actual
 * WordPress pages and this function is now a convenience for compatibility
 * with the new method.
 *
 * @since 1.0.0
 *
 * @param string $slug The slug of the component being added to the root list.
 */
function bp_core_add_root_component( $slug ) {
	$bp = buddypress();

	if ( empty( $bp->pages ) ) {
		$bp->pages = bp_core_get_directory_pages();
	}

	$match = false;

	// Check if the slug is registered in the $bp->pages global.
	foreach ( (array) $bp->pages as $key => $page ) {
		if ( $key === $slug || $page->slug === $slug ) {
			$match = true;
		}
	}

	// Maybe create the add_root array.
	if ( empty( $bp->add_root ) ) {
		$bp->add_root = array();
	}

	// If there was no match, add a page for this root component.
	if ( empty( $match ) ) {
		$add_root_items   = $bp->add_root;
		$add_root_items[] = $slug;
		$bp->add_root     = $add_root_items;
	}

	// Make sure that this component is registered as requiring a top-level directory.
	if ( isset( $bp->{$slug} ) ) {
		$bp->loaded_components[ $bp->{$slug}->slug ] = $bp->{$slug}->id;
		$bp->{$slug}->has_directory                  = true;
	}
}

/**
 * Return the domain for the root blog.
 *
 * Eg: http://example.com OR https://example.com
 *
 * @since 1.0.0
 *
 * @return string The domain URL for the blog.
 */
function bp_core_get_root_domain() {
	$domain = bp_rewrites_get_root_url();

	/**
	 * Filters the domain for the root blog.
	 *
	 * @since 1.0.0
	 *
	 * @param string $domain The domain URL for the blog.
	 */
	return apply_filters( 'bp_core_get_root_domain', $domain );
}

/**
 * Return the "root domain", the URL of the BP root blog.
 *
 * @since 1.0.0
 *
 * @return string URL of the BP root blog.
 */
function bp_get_root_domain() {
	$domain = bp_get_root_url();

	/**
	 *  Filters the "root domain", the URL of the BP root blog.
	 *
	 * @since 1.0.0
	 *
	 * @param string $domain URL of the BP root blog.
	 */
	return apply_filters( 'bp_get_root_domain', $domain );
}

/**
 * Output the "root domain", the URL of the BP root blog.
 *
 * @since 1.0.0
 */
function bp_root_domain() {
	bp_root_url();
}

/**
 * Analyze the URI and break it down into BuddyPress-usable chunks.
 *
 * BuddyPress can use complete custom friendly URIs without the user having to
 * add new rewrite rules. Custom components are able to use their own custom
 * URI structures with very little work.
 *
 * The URIs are broken down as follows:
 *   - http:// example.com / members / andy / [current_component] / [current_action] / [action_variables] / [action_variables] / ...
 *   - OUTSIDE ROOT: http:// example.com / sites / buddypress / members / andy / [current_component] / [current_action] / [action_variables] / [action_variables] / ...
 *
 * Example:
 *    - http://example.com/members/andy/profile/edit/group/5/
 *    - $bp->current_component: string 'xprofile'
 *    - $bp->current_action: string 'edit'
 *    - $bp->action_variables: array ['group', 5]
 *
 * @since 1.0.0
 */
function bp_core_set_uri_globals() {
	global $current_blog, $wp_rewrite;

	// Don't catch URIs on non-root blogs unless multiblog mode is on.
	if ( ! bp_is_root_blog() && ! bp_is_multiblog_mode() ) {
		return false;
	}

	$bp = buddypress();

	// Define local variables.
	$root_profile = false;
	$match        = false;
	$key_slugs    = array();
	$matches      = array();
	$uri_chunks   = array();

	// Fetch all the WP page names for each component.
	if ( empty( $bp->pages ) ) {
		$bp->pages = bp_core_get_directory_pages();
	}

	$request_uri = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}

	// Ajax or not?
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX || strpos( $request_uri, 'wp-load.php' ) ) {
		$path = bp_get_referer_path();
	} else {
		$path = $request_uri;
	}

	/**
	 * Filters the BuddyPress global URI path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Path to set.
	 */
	$path = apply_filters( 'bp_uri', $path );

	// Take GET variables off the URL to avoid problems.
	$path = strtok( $path, '?' );

	// Fetch current URI and explode each part separated by '/' into an array.
	$bp_uri = explode( '/', $path );

	// Loop and remove empties.
	foreach ( (array) $bp_uri as $key => $uri_chunk ) {
		if ( empty( $bp_uri[ $key ] ) ) {
			unset( $bp_uri[ $key ] );
		}
	}

	/*
	 * If running off blog other than root, any subdirectory names must be
	 * removed from $bp_uri. This includes two cases:
	 *
	 * 1. when WP is installed in a subdirectory,
	 * 2. when BP is running on secondary blog of a subdirectory
	 * multisite installation. Phew!
	 */
	if ( is_multisite() && ! is_subdomain_install() && ( bp_is_multiblog_mode() || 1 !== (int) bp_get_root_blog_id() ) ) {

		// Blow chunks.
		$chunks = explode( '/', $current_blog->path );

		// If chunks exist...
		if ( ! empty( $chunks ) ) {

			// ...loop through them...
			foreach ( $chunks as $key => $chunk ) {
				$bkey = array_search( $chunk, $bp_uri, true );

				// ...and unset offending keys.
				if ( false !== $bkey ) {
					unset( $bp_uri[ $bkey ] );
				}

				$bp_uri = array_values( $bp_uri );
			}
		}
	}

	// Get site path items.
	$paths = explode( '/', bp_core_get_site_path() );

	// Take empties off the end of path.
	if ( empty( $paths[ count( $paths ) - 1 ] ) ) {
		array_pop( $paths );
	}

	// Take empties off the start of path.
	if ( empty( $paths[0] ) ) {
		array_shift( $paths );
	}

	// Reset indexes.
	$bp_uri = array_values( $bp_uri );
	$paths  = array_values( $paths );

	// Unset URI indices if they intersect with the paths.
	foreach ( (array) $bp_uri as $key => $uri_chunk ) {
		if ( isset( $paths[ $key ] ) && $uri_chunk === $paths[ $key ] ) {
			unset( $bp_uri[ $key ] );
		}
	}

	// Reset the keys by merging with an empty array.
	$bp_uri = array_merge( array(), $bp_uri );

	/*
	 * If a component is set to the front page, force its name into $bp_uri
	 * so that $current_component is populated (unless a specific WP post is being requested
	 * via a URL parameter, usually signifying Preview mode).
	 */
	if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) && empty( $bp_uri ) && empty( $_GET['p'] ) && empty( $_GET['page_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$post = get_post( get_option( 'page_on_front' ) );
		if ( ! empty( $post ) ) {
			$bp_uri[0] = $post->post_name;
		}
	}

	// Keep the unfiltered URI safe.
	$bp->unfiltered_uri = $bp_uri;

	// Don't use $bp_unfiltered_uri, this is only for backpat with old plugins. Use $bp->unfiltered_uri.
	$GLOBALS['bp_unfiltered_uri'] = &$bp->unfiltered_uri;

	// Get slugs of pages into array.
	foreach ( (array) $bp->pages as $page_key => $bp_page ) {
		$key_slugs[ $page_key ] = trailingslashit( '/' . $bp_page->slug );
	}

	// Bail if keyslugs are empty, as BP is not setup correct.
	if ( empty( $key_slugs ) ) {
		return;
	}

	// Loop through page slugs and look for exact match to path.
	foreach ( $key_slugs as $key => $slug ) {
		if ( $slug === $path ) {
			$match      = $bp->pages->{$key};
			$match->key = $key;
			$matches[]  = 1;
			break;
		}
	}

	// No exact match, so look for partials.
	if ( empty( $match ) ) {

		// Loop through each page in the $bp->pages global.
		foreach ( (array) $bp->pages as $page_key => $bp_page ) {

			// Look for a match (check members first).
			if ( in_array( $bp_page->name, (array) $bp_uri, true ) ) {

				// Match found, now match the slug to make sure.
				$uri_chunks = explode( '/', $bp_page->slug );

				// Loop through uri_chunks.
				foreach ( (array) $uri_chunks as $key => $uri_chunk ) {

					// Make sure chunk is in the correct position.
					if ( ! empty( $bp_uri[ $key ] ) && ( $bp_uri[ $key ] === $uri_chunk ) ) {
						$matches[] = 1;

						// No match.
					} else {
						$matches[] = 0;
					}
				}

				// Have a match.
				if ( ! in_array( 0, (array) $matches, true ) ) {
					$match      = $bp_page;
					$match->key = $page_key;
					break;
				};

				// Unset matches.
				unset( $matches );
			}

			// Unset uri chunks.
			unset( $uri_chunks );
		}
	}

	// URLs with BP_ENABLE_ROOT_PROFILES enabled won't be caught above.
	if ( empty( $matches ) && bp_core_enable_root_profiles() && ! empty( $bp_uri[0] ) ) {

		// Switch field based on compat.
		$field = bp_is_username_compatibility_mode() ? 'login' : 'slug';

		/**
		 * Filter the portion of the URI that is the displayed user's slug.
		 *
		 * Eg. example.com/ADMIN (when root profiles is enabled)
		 *     example.com/members/ADMIN (when root profiles isn't enabled)
		 *
		 * ADMIN would be the displayed user's slug.
		 *
		 * @since 2.6.0
		 *
		 * @param string $member_slug
		 */
		$member_slug  = apply_filters( 'bp_core_set_uri_globals_member_slug', $bp_uri[0] );
		$root_profile = get_user_by( $field, $member_slug );

		// Make sure there's a user corresponding to $bp_uri[0].
		if ( ! empty( $bp->pages->members ) && $root_profile ) {

			// Force BP to recognize that this is a members page.
			$matches[]  = 1;
			$match      = $bp->pages->members;
			$match->key = 'members';
		}
	}

	// Search doesn't have an associated page, so we check for it separately.
	if ( isset( $_POST['search-terms'] ) && ! empty( $bp_uri[0] ) && ( bp_get_search_slug() === $bp_uri[0] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$matches[]   = 1;
		$match       = new stdClass();
		$match->key  = 'search';
		$match->slug = bp_get_search_slug();
	}

	// This is not a BuddyPress page, so just return.
	if ( empty( $matches ) ) {
		/**
		 * Fires when the the current page is not a BuddyPress one.
		 *
		 * @since 10.0.0
		 */
		do_action( 'is_not_buddypress' );
		return false;
	}

	$wp_rewrite->use_verbose_page_rules = false;

	// Find the offset. With $root_profile set, we fudge the offset down so later parsing works.
	$slug       = ! empty( $match ) ? explode( '/', $match->slug ) : '';
	$uri_offset = empty( $root_profile ) ? 0 : -1;

	// Rejig the offset.
	if ( ! empty( $slug ) && ( 1 < count( $slug ) ) ) {
		// Only offset if not on a root profile. Fixes issue when Members page is nested.
		if ( false === $root_profile ) {
			array_pop( $slug );
			$uri_offset = count( $slug );
		}
	}

	// Global the unfiltered offset to use in bp_core_load_template().
	// To avoid PHP warnings in bp_core_load_template(), it must always be >= 0.
	$bp->unfiltered_uri_offset = $uri_offset >= 0 ? $uri_offset : 0;

	// We have an exact match.
	if ( isset( $match->key ) ) {

		// Set current component to matched key.
		$bp->current_component = $match->key;

		// If members component, do more work to find the actual component.
		if ( 'members' === $match->key ) {

			$after_member_slug = false;
			if ( ! empty( $bp_uri[ $uri_offset + 1 ] ) ) {
				$after_member_slug = $bp_uri[ $uri_offset + 1 ];
			}

			// Are we viewing a specific user?
			if ( $after_member_slug ) {

				/** This filter is documented in bp-core/bp-core-catchuri.php */
				$after_member_slug = apply_filters( 'bp_core_set_uri_globals_member_slug', $after_member_slug );

				// If root profile, we've already queried for the user.
				if ( $root_profile instanceof WP_User ) {
					$bp->displayed_user->id = $root_profile->ID;

					// Switch the displayed_user based on compatibility mode.
				} elseif ( bp_is_username_compatibility_mode() ) {
					$bp->displayed_user->id = (int) bp_core_get_userid( urldecode( $after_member_slug ) );

				} else {
					$bp->displayed_user->id = (int) bp_core_get_userid_from_nicename( $after_member_slug );
				}
			}

			// Is this a member type directory?
			if ( ! bp_displayed_user_id() && bp_get_members_member_type_base() === $after_member_slug && ! empty( $bp_uri[ $uri_offset + 2 ] ) ) {
				$matched_types = bp_get_member_types(
					array(
						'has_directory'  => true,
						'directory_slug' => $bp_uri[ $uri_offset + 2 ],
					)
				);

				if ( ! empty( $matched_types ) ) {
					$bp->current_member_type = reset( $matched_types );
					unset( $bp_uri[ $uri_offset + 1 ] );
				}
			}

			// If the slug matches neither a member type nor a specific member, 404.
			if ( ! bp_displayed_user_id() && ! bp_get_current_member_type() && $after_member_slug ) {
				// Prevent components from loading their templates.
				$bp->current_component = '';
				bp_do_404();
				return;
			}

			// If the displayed user is marked as a spammer, 404 (unless logged-in user is a super admin).
			if ( bp_displayed_user_id() && bp_is_user_spammer( bp_displayed_user_id() ) ) {
				if ( bp_current_user_can( 'bp_moderate' ) ) {
					bp_core_add_message( __( 'This user has been marked as a spammer. Only site admins can view this profile.', 'bp-classic' ), 'warning' );
				} else {
					bp_do_404();
					return;
				}
			}

			// Bump the offset.
			if ( bp_displayed_user_id() ) {
				if ( isset( $bp_uri[ $uri_offset + 2 ] ) ) {
					$bp_uri                = array_merge( array(), array_slice( $bp_uri, $uri_offset + 2 ) );
					$bp->current_component = $bp_uri[0];

					// No component, so default will be picked later.
				} else {
					$bp_uri                = array_merge( array(), array_slice( $bp_uri, $uri_offset + 2 ) );
					$bp->current_component = '';
				}

				// Reset the offset.
				$uri_offset = 0;
			}
		}
	}

	// Determine the current action.
	$current_action = isset( $bp_uri[ $uri_offset + 1 ] ) ? $bp_uri[ $uri_offset + 1 ] : '';

	/*
	 * If a BuddyPress directory is set to the WP front page, URLs like example.com/members/?s=foo
	 * shouldn't interfere with blog searches.
	 */
	if ( empty( $current_action ) && ! empty( $_GET['s'] ) && 'page' === get_option( 'show_on_front' ) && ! empty( $match->id ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$page_on_front = (int) get_option( 'page_on_front' );
		if ( (int) $match->id === $page_on_front ) {
			$bp->current_component = '';
			return false;
		}
	}

	$bp->current_action = $current_action;

	// Slice the rest of the $bp_uri array and reset offset.
	$bp_uri     = array_slice( $bp_uri, $uri_offset + 2 );
	$uri_offset = 0;

	// Set the entire URI as the action variables, we will unset the current_component and action in a second.
	$bp->action_variables = $bp_uri;

	// Reset the keys by merging with an empty array.
	$bp->action_variables = array_merge( array(), $bp->action_variables );
}
add_action( 'bp_init', 'bp_core_set_uri_globals', 2 );

/**
 * Return the username for a user based on their user id.
 *
 * This function is sensitive to the BP_ENABLE_USERNAME_COMPATIBILITY_MODE,
 * so it will return the user_login or user_nicename as appropriate.
 *
 * @since 1.0.0
 *
 * @param int         $user_id       User ID to check.
 * @param string|bool $user_nicename Optional. user_nicename of user being checked.
 * @param string|bool $user_login    Optional. user_login of user being checked.
 * @return string The username of the matched user or an empty string if no user is found.
 */
function bp_core_get_username( $user_id = 0, $user_nicename = false, $user_login = false ) {
	if ( ! $user_id ) {
		$value = $user_nicename;
		$field = 'slug';

		if ( ! $user_nicename ) {
			$value = $user_login;
			$field = 'login';
		}

		$user = get_user_by( $field, $value );

		if ( $user instanceof WP_User ) {
			$user_id = (int) $user->ID;
		}
	}

	$username = bp_members_get_user_slug( $user_id );

	/**
	 * Filters the username based on originally provided user ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $username Username determined by user ID.
	 */
	return apply_filters( 'bp_core_get_username', $username );
}

/**
 * Return the domain for the passed user: e.g. http://example.com/members/andy/.
 *
 * @since 1.0.0
 *
 * @param int         $user_id       The ID of the user.
 * @param string|bool $user_nicename Optional. user_nicename of the user.
 * @param string|bool $user_login    Optional. user_login of the user.
 * @return string
 */
function bp_core_get_user_domain( $user_id = 0, $user_nicename = false, $user_login = false ) {
	if ( empty( $user_id ) ) {
		return;
	}

	$domain = bp_members_get_user_url( $user_id );

	// Don't use this filter.  Subject to removal in a future release.
	// Use the 'bp_core_get_user_domain' filter instead.
	$domain = apply_filters( 'bp_core_get_user_domain_pre_cache', $domain, $user_id, $user_nicename, $user_login );

	/**
	 * Filters the domain for the passed user.
	 *
	 * @since 1.0.0
	 *
	 * @param string $domain        Domain for the passed user.
	 * @param int    $user_id       ID of the passed user.
	 * @param string $user_nicename User nicename of the passed user.
	 * @param string $user_login    User login of the passed user.
	 */
	return apply_filters( 'bp_core_get_user_domain', $domain, $user_id, $user_nicename, $user_login );
}

/**
 * Determine whether BuddyPress should register the `themes` directory.
 *
 * @since 1.0.0
 *
 * @return boolean True if the `themes` directory should be registered.
 *                 False otherwise.
 */
function bp_do_register_theme_directory() {
	$register = false;

	/*
	 * If bp-default exists in another theme directory, bail.
	 * This ensures that the version of bp-default in the regular themes
	 * directory will always take precedence, as part of a migration away
	 * from the version packaged with BuddyPress.
	 */
	foreach ( array_values( (array) $GLOBALS['wp_theme_directories'] ) as $directory ) {
		if ( is_dir( $directory . '/bp-default' ) ) {
			return $register;
		}
	}

	// If the current theme is bp-default (or a bp-default child), BP should register its directory.
	$register = 'bp-default' === get_stylesheet() || 'bp-default' === get_template();

	// Legacy sites continue to have the theme registered.
	if ( empty( $register ) && ( 1 === (int) get_site_option( '_bp_retain_bp_default' ) ) ) {
		$register = true;
	}

	/**
	 * Filters whether BuddyPress should register the bp-themes directory.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $register If bp-themes should be registered.
	 */
	$register_theme = apply_filters( 'bp_do_register_theme_directory', $register );

	/*
	 * In case the BP Default theme was active in BuddyPress < 12.0.0, it's required
	 * to update template and eventually stylesheet root options to keep BP Default
	 * as the active theme safely.
	 */
	if ( $register_theme && false === strpos( get_option( 'template_root', '' ), '/plugins/bp-classic/themes' ) ) {
		$theme_root = str_replace( content_url(), '', bp_classic_get_themes_url() );

		if ( 'bp-default' === get_template() ) {
			update_option( 'template_root', $theme_root );
		}

		if ( 'bp-default' === get_stylesheet() ) {
			update_option( 'stylesheet_root', $theme_root );
		}
	}

	return $register_theme;
}

/**
 * Set up BuddyPress's legacy theme directory.
 *
 * BuddyPress is no more including BP Default. This plugin
 * is there to provide backward compatibility to BuddyPress
 * setups still using this deprecated theme.
 *
 * @since 1.1.0
 */
function bp_classic_register_themes_directory() {
	if ( ! bp_do_register_theme_directory() ) {
		return;
	}

	$bp = buddypress();

	$bp->old_themes_dir = bp_classic_get_themes_dir();
	$bp->old_themes_url = bp_classic_get_themes_url();

	register_theme_directory( $bp->old_themes_dir );
}
add_action( 'bp_register_theme_directory', 'bp_classic_register_themes_directory', 1 );
