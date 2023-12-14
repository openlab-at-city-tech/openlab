<?php

/**
 * Polyfills for URL-related functions introduced in BP 12.0 for the Core component.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'bp_rewrites_get_slug' ) ) {
	/**
	 * In BP 12.0.0, it's used to get a customized slug.
	 *
	 * Here it simply returns the default slug as customizing URL slugs is not supported by BP versions < 12.0.
	 *
	 * @param string $component_id The BuddyPress component's ID.
	 * @param string $rewrite_id   The screen rewrite ID, used to find the custom slugs.
	 * @param string $default_slug The screen default slug, used as a fallback.
	 * @return string The slug to use for the screen belonging to the requested component.
	 */
	function bp_rewrites_get_slug( $component_id = '', $rewrite_id = '', $default_slug = '' ) {
		return $default_slug;
	}
}

if ( ! function_exists( 'bp_rewrites_get_url' ) ) {
	/**
	 * Gets a BuddyPress URL.
	 *
	 * @param array $args {
	 *      Optional. An array of arguments.
	 *
	 *      @type string $component_id                The BuddyPress component ID. Defaults ''.
	 *      @type string $directory_type              Whether it's an object type URL. Defaults ''.
	 *                                                Accepts '' (no object type), 'members' or 'groups'.
	 *      @type string $single_item                 The BuddyPress single item's URL chunk. Defaults ''.
	 *                                                Eg: the member's user nicename for Members or the group's slug for Groups.
	 *      @type string $single_item_component       The BuddyPress single item's component URL chunk. Defaults ''.
	 *                                                Eg: the member's Activity page.
	 *      @type string $single_item_action          The BuddyPress single item's action URL chunk. Defaults ''.
	 *                                                Eg: the member's Activity mentions page.
	 *      @type array $single_item_action_variables The list of BuddyPress single item's action variable URL chunks. Defaults [].
	 * }
	 * @return string The BuddyPress URL.
	 */
	function bp_rewrites_get_url( $args = array() ) {
		$url = trailingslashit( bp_get_root_domain() );

		$r = bp_parse_args(
			$args,
			array(
				'component_id'                 => '',
				'directory_type'               => '',
				'single_item'                  => '',
				'single_item_component'        => '',
				'single_item_action'           => '',
				'single_item_action_variables' => array(),
			)
		);

		$chunks = array();

		if ( $r['component_id'] && bp_is_active( $r['component_id'] ) ) {
			$bp = buddypress();

			if ( isset( $bp->pages->{ $r['component_id'] }->id ) ) {
				$url = get_permalink( $bp->pages->{ $r['component_id'] }->id );

				if ( $r['single_item'] ) {
					$chunks[] = $r['single_item'];

					if ( 'members' === $r['component_id'] && bp_core_enable_root_profiles() ) {
						$url = trailingslashit( bp_get_root_domain() );
					}

					if ( $r['single_item_component'] && 'members' === $r['component_id'] ) {
						$chunks[] = $r['single_item_component'];
					}

					if ( $r['single_item_action'] ) {
						$chunks[] = $r['single_item_action'];
					}

					if ( $r['single_item_action_variables'] && is_array( $r['single_item_action_variables'] ) ) {
						$chunks = array_merge( $chunks, $r['single_item_action_variables'] );
					}

				} elseif ( $r['directory_type'] ) {
					if ( 'members' === $r['component_id'] ) {
						$chunks[] = bp_get_members_member_type_base();
					} elseif ( 'groups' === $r['component_id'] ) {
						$chunks[] = bp_get_groups_group_type_base();
					}

					$chunks[] = $r['directory_type'];

				} elseif ( isset( $r['member_register'] ) && 'members' === $r['component_id'] ) {
					return bp_get_signup_page();

				} elseif ( isset( $r['member_activate'] ) && 'members' === $r['component_id'] ) {
					return bp_get_activation_page();

				} elseif ( isset( $r['create_single_item'] ) ) {
					$chunks[] = 'create';

					if ( isset( $r['create_single_item_variables'] ) && is_array( $r['create_single_item_variables'] ) ) {
						$chunks = array_merge( $chunks, $r['create_single_item_variables'] );
					}
				}
			}
		}

		if ( $chunks ) {
			$url .= join( '/', $chunks ) . '/';
		}

		return $url;
	}
}

if ( ! function_exists( 'bp_get_root_url' ) ) {
	/**
	 * Gets the root URL of the site.
	 *
	 * @return string
	 */
	function bp_get_root_url() {
		return bp_get_root_domain();
	}
}
