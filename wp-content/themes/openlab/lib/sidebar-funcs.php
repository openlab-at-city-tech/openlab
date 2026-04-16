<?php

/**
 * Sidebar based functionality
 */

/**
 * Register the About pages mobile drawer.
 *
 * This registers a drawer that will be rendered in the unified drawer container
 * for About pages on mobile.
 */
function openlab_register_about_mobile_drawer() {
	openlab_register_drawer( 'about-mobile-drawer', 'openlab_render_about_mobile_drawer' );
}

/**
 * Render the About pages mobile drawer content.
 *
 * @return string Drawer HTML.
 */
function openlab_render_about_mobile_drawer() {
	// Get the About menu items.
	$menu_items = [];
	$menu_locations = get_nav_menu_locations();

	if ( isset( $menu_locations['aboutmenu'] ) ) {
		$menu = wp_get_nav_menu_object( $menu_locations['aboutmenu'] );
		if ( $menu ) {
			$nav_items = wp_get_nav_menu_items( $menu->term_id );
			if ( $nav_items ) {
				foreach ( $nav_items as $item ) {
					// Only include top-level items for now.
					if ( 0 === (int) $item->menu_item_parent ) {
						$menu_items[] = [
							'text' => $item->title,
							'href' => $item->url,
						];
					}
				}
			}
		}
	}

	if ( empty( $menu_items ) ) {
		return '';
	}

	return openlab_render_drawer( [
		'id'            => 'about-mobile-drawer',
		'default_panel' => 'about-mobile-panel',
		'panels'        => [
			[
				'id'      => 'about-mobile-panel',
				'heading' => 'About',
				'items'   => $menu_items,
				'is_root'    => true,
				'show_close' => true,
			],
		],
	] );
}

/**
 * Register the Member pages mobile drawer.
 *
 * This registers a drawer that will be rendered in the unified drawer container
 * for Member single pages on mobile.
 */
function openlab_register_member_mobile_drawer() {
	openlab_register_drawer( 'member-mobile-drawer', 'openlab_render_member_mobile_drawer' );
}

/**
 * Register the Group pages mobile drawer.
 *
 * This registers a drawer that will be rendered in the unified drawer container
 * for Group single pages on mobile.
 */
function openlab_register_group_mobile_drawer() {
	openlab_register_drawer( 'group-mobile-drawer', 'openlab_render_group_mobile_drawer' );
}

/**
 * Render the Group pages mobile drawer content.
 *
 * @return string Drawer HTML.
 */
function openlab_render_group_mobile_drawer() {
	global $bp;

	if ( ! bp_is_group() ) {
		return '';
	}

	$group_id = bp_get_current_group_id();
	$group_type_label = openlab_get_group_type_label( 'group_id=' . $group_id . '&case=upper' );
	$root_items = [];
	$panels = [];

	// Get the group site settings for site links.
	$group_site_settings = openlab_get_group_site_settings( $group_id );

	// Add site link if available.
	if ( ! empty( $group_site_settings['site_url'] ) && $group_site_settings['is_visible'] ) {
		$site_label = openlab_is_portfolio()
			? openlab_get_group_type_label( 'group_id=' . $group_id . '&case=upper' )
			: ucwords( groups_get_groupmeta( $group_id, 'wds_group_type' ) );

		$root_items[] = [
			'text'   => 'Visit ' . $site_label . ' Site <span class="fa fa-chevron-circle-right" aria-hidden="true"></span>',
			'href'   => trailingslashit( $group_site_settings['site_url'] ),
			'is_raw' => true,
		];

		// Add dashboard link if user has access.
		if ( $group_site_settings['is_local'] && ( $bp->is_item_admin || is_super_admin() || ( groups_is_user_member( bp_loggedin_user_id(), $group_id ) && current_user_can_for_blog( $group_site_settings['site_id'], 'edit_posts' ) ) ) ) {
			$root_items[] = [
				'text' => 'Site Dashboard',
				'href' => trailingslashit( $group_site_settings['site_url'] ) . 'wp-admin/',
			];
		}
	}

	// Get the BP options nav items (with submenus).
	$nav_items = openlab_get_group_nav_items();
	foreach ( $nav_items as $nav_item ) {
		// If the item has submenu_items, create a submenu panel and make the root item a toggle
		if ( ! empty( $nav_item['submenu_items'] ) ) {
			$submenu_id = 'group-mobile-' . sanitize_title( $nav_item['text'] ) . '-panel';

			// Add submenu panel
			$panels[] = [
				'id'          => $submenu_id,
				'heading'     => $nav_item['text'],
				'items'       => $nav_item['submenu_items'],
				'is_root'     => false,
				'back_target' => 'group-mobile-root-panel',
				'back_text'   => sprintf( 'Back to %s Profile', $group_type_label ),
			];

			// Root item becomes a submenu toggle
			$root_items[] = [
				'text'       => $nav_item['text'],
				'submenu'    => $submenu_id,
				'is_current' => $nav_item['is_current'],
			];
		} else {
			// Regular item without submenu
			$root_items[] = $nav_item;
		}
	}

	// Add mobile anchor links for Related Sites and Portfolios.
	$anchor_items = openlab_get_group_mobile_anchor_items();
	foreach ( $anchor_items as $anchor_item ) {
		$root_items[] = $anchor_item;
	}

	if ( empty( $root_items ) ) {
		return '';
	}

	// Build root panel
	$root_panel = [
		'id'         => 'group-mobile-root-panel',
		'heading'    => $group_type_label . ' Profile',
		'items'      => $root_items,
		'is_root'    => true,
		'show_close' => true,
	];

	// Prepend root panel to the list of panels
	array_unshift( $panels, $root_panel );

	$drawer_html = openlab_render_drawer( [
		'id'            => 'group-mobile-drawer',
		'default_panel' => 'group-mobile-root-panel',
		'panels'        => $panels,
	] );

	return $drawer_html;
}

/**
 * Get the group navigation items as a complete tree for mobile drawers.
 *
 * Unlike the desktop menu which relies on BuddyPress's dynamic nav registration,
 * this function builds a complete navigation tree based on group features,
 * independent of the current page context. This ensures all submenus are
 * available in the mobile drawer regardless of which group page the user is on.
 *
 * @return array Array of nav items suitable for drawer rendering, with 'submenu_items' for nested menus.
 */
function openlab_get_group_nav_items() {
	$items = [];

	$bp = buddypress();

	if ( ! bp_is_group() || empty( $bp->groups->current_group ) ) {
		return $items;
	}

	$group_id         = bp_get_current_group_id();
	$group            = groups_get_current_group();
	$current_action   = bp_current_action();
	$action_var       = bp_action_variable( 0 );
	$group_type_label = openlab_get_group_type_label( 'case=upper' );
	$group_url        = bp_get_group_url( $group );

	// 1. Profile (Home)
	$items[] = [
		'text'       => $group_type_label . ' Profile',
		'href'       => $group_url,
		'is_current' => ( $current_action === 'home' ),
	];

	// 2. Activity (always present)
	$items[] = [
		'text'          => __( 'Activity', 'flavor' ),
		'href'          => $group_url . 'activity/',
		'is_current'    => ( $current_action === 'activity' ),
		'submenu_items' => openlab_get_group_activity_submenu_items(),
	];

	// 3. Announcements (if enabled)
	if ( function_exists( 'openlab_is_announcements_enabled_for_group' ) && openlab_is_announcements_enabled_for_group( $group_id ) ) {
		$announcement_count = openlab_get_group_announcement_count( $group_id );
		$items[] = [
			'text'       => __( 'Announcements', 'flavor' ),
			'href'       => $group_url . 'announcements/',
			'is_current' => ( $current_action === 'announcements' ),
			'count'      => $announcement_count,
		];
	}

	// 4. Discussion / Forum (if enabled)
	if ( function_exists( 'openlab_is_forum_enabled_for_group' ) && openlab_is_forum_enabled_for_group( $group_id ) ) {
		$forum_count = openlab_get_group_forum_topic_count( $group_id );
		$items[] = [
			'text'          => __( 'Discussion', 'flavor' ),
			'href'          => $group_url . 'forum/',
			'is_current'    => ( $current_action === 'forum' ),
			'submenu_items' => openlab_get_group_forum_submenu_items(),
			'count'         => $forum_count,
		];
	}

	// 5. Docs (if enabled)
	if ( function_exists( 'openlab_is_docs_enabled_for_group' ) && openlab_is_docs_enabled_for_group( $group_id ) ) {
		$docs_count = openlab_get_group_docs_count( $group_id );
		$items[] = [
			'text'          => __( 'Docs', 'flavor' ),
			'href'          => $group_url . 'docs/',
			'is_current'    => ( $current_action === 'docs' ),
			'submenu_items' => openlab_get_group_docs_submenu_items(),
			'count'         => $docs_count,
		];
	}

	// 6. File Library (if enabled)
	if ( function_exists( 'openlab_is_files_enabled_for_group' ) && openlab_is_files_enabled_for_group( $group_id ) ) {
		$files_count = openlab_get_group_files_count( $group_id );
		$items[] = [
			'text'          => __( 'File Library', 'flavor' ),
			'href'          => $group_url . 'files/',
			'is_current'    => ( $current_action === 'files' ),
			'submenu_items' => openlab_get_group_files_submenu_items(),
			'count'         => $files_count,
		];
	}

	// 7. Calendar / Events (if enabled)
	if ( function_exists( 'openlab_is_calendar_enabled_for_group' ) && openlab_is_calendar_enabled_for_group( $group_id ) ) {
		$items[] = [
			'text'       => __( 'Calendar', 'flavor' ),
			'href'       => $group_url . 'events/',
			'is_current' => ( $current_action === 'events' ),
		];
	}

	// 8. Connections (if enabled)
	$connections_enabled = groups_get_groupmeta( $group_id, 'openlab_connections_enabled' );
	if ( $connections_enabled ) {
		$items[] = [
			'text'          => __( 'Connections', 'flavor' ),
			'href'          => $group_url . 'connections/',
			'is_current'    => ( $current_action === 'connections' ),
			'submenu_items' => openlab_get_group_connections_submenu_items(),
		];
	}

	// 9. Membership (always present - includes members, invites, notifications)
	$membership_actions = [ 'members', 'invite-anyone', 'notifications' ];
	$membership_vars    = [ 'manage-members', 'notifications', 'membership-requests' ];
	$membership_current = in_array( $current_action, $membership_actions, true )
		|| in_array( $action_var, $membership_vars, true );

	$membership_link = $bp->is_item_admin
		? $group_url . 'admin/manage-members'
		: $group_url . 'members/';

	$member_count = openlab_get_group_member_count( $group_id );
	$items[] = [
		'text'          => __( 'Membership', 'flavor' ),
		'href'          => $membership_link,
		'is_current'    => $membership_current,
		'submenu_items' => openlab_get_group_membership_submenu_items( $group ),
		'count'         => $member_count,
	];

	// 10. Settings (admin only, if BP thinks user is admin)
	if ( $bp->is_item_admin ) {
		$settings_current = ( $current_action === 'admin' && ! in_array( $action_var, [ 'manage-members', 'notifications', 'membership-requests' ], true ) );

		$items[] = [
			'text'       => __( 'Settings', 'flavor' ),
			'href'       => $group_url . 'admin/edit-details/',
			'is_current' => $settings_current,
		];
	}

	return $items;
}

/**
 * Render the group navigation for desktop sidebar.
 *
 * This replaces bp_get_options_nav() for groups, using the same data source
 * as the mobile drawer (openlab_get_group_nav_items) to ensure consistency.
 *
 * Outputs <li> elements for use inside a <ul class="sidebar-nav">.
 */
function openlab_render_group_desktop_nav() {
	$nav_items = openlab_get_group_nav_items();

	if ( empty( $nav_items ) ) {
		return;
	}

	$output = '';
	$i = 0;
	$total = count( $nav_items );

	foreach ( $nav_items as $item ) {
		$i++;
		$slug = sanitize_title( $item['text'] );

		// Build classes
		$classes = [ 'sidebar-nav-item', 'item-' . $slug ];

		if ( $i === 1 ) {
			$classes[] = 'first-item';
		}
		if ( $i === $total ) {
			$classes[] = 'last-item';
		}
		if ( ! empty( $item['is_current'] ) ) {
			$classes[] = 'current-menu-item';
		}

		$class_attr = implode( ' ', $classes );
		$href = esc_url( $item['href'] );
		$text = esc_html( $item['text'] );

		// Add count badge if present
		$count_badge = '';
		if ( ! empty( $item['count'] ) && $item['count'] > 0 ) {
			$count_badge = sprintf(
				'<span class="mol-count pull-right count-%d gray">%s</span>',
				intval( $item['count'] ),
				esc_html( number_format_i18n( $item['count'] ) )
			);
		}

		$output .= sprintf(
			'<li class="%s"><a href="%s">%s%s</a></li>',
			esc_attr( $class_attr ),
			$href,
			$text,
			$count_badge
		);
	}

	echo $output;
}

/**
 * Get the count of announcements for a group.
 *
 * @param int $group_id Group ID.
 * @return int Count of announcements.
 */
function openlab_get_group_announcement_count( $group_id ) {
	$count_query = new WP_Query( [
		'post_type'      => 'openlab_announcement',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_query'     => [
			[
				'key'   => 'openlab_announcement_group_id',
				'value' => $group_id,
			]
		],
		'fields' => 'ids',
	] );

	return $count_query->found_posts;
}

/**
 * Get the count of forum topics for a group.
 *
 * @param int $group_id Group ID.
 * @return int Count of topics.
 */
function openlab_get_group_forum_topic_count( $group_id ) {
	if ( ! function_exists( 'bbp_get_group_forum_ids' ) ) {
		return 0;
	}

	$forum_ids = bbp_get_group_forum_ids( $group_id );
	if ( empty( $forum_ids ) ) {
		return 0;
	}

	$topic_ids = get_posts( [
		'post_type'      => bbp_get_topic_post_type(),
		'post_parent'    => $forum_ids[0],
		'fields'         => 'ids',
		'posts_per_page' => -1,
	] );

	return count( $topic_ids );
}

/**
 * Get the count of docs for a group.
 *
 * @param int $group_id Group ID.
 * @return int Count of docs.
 */
function openlab_get_group_docs_count( $group_id ) {
	if ( ! class_exists( 'BP_Docs_Query' ) ) {
		return 0;
	}

	$query_builder = new BP_Docs_Query( [
		'group_id' => $group_id,
		'doc_slug' => '',
	] );

	$the_wp_query = $query_builder->get_wp_query();

	return ! empty( $the_wp_query->found_posts ) ? $the_wp_query->found_posts : 0;
}

/**
 * Get the count of files for a group.
 *
 * @param int $group_id Group ID.
 * @return int Count of files.
 */
function openlab_get_group_files_count( $group_id ) {
	if ( ! class_exists( 'BP_Group_Documents' ) ) {
		return 0;
	}

	return BP_Group_Documents::get_total( $group_id );
}

/**
 * Get the count of members for a group.
 *
 * @param int $group_id Group ID.
 * @return int Count of members.
 */
function openlab_get_group_member_count( $group_id ) {
	$total_mem = (int) bp_core_number_format( groups_get_groupmeta( $group_id, 'total_member_count' ) );

	// Subtract private users if current user can't see them.
	if ( ! current_user_can( 'bp_moderate' ) && ! groups_is_user_member( bp_loggedin_user_id(), $group_id ) ) {
		$private_users = openlab_get_group_private_users( $group_id );
		$total_mem -= count( $private_users );
	}

	return max( 0, $total_mem );
}

/**
 * Get the Membership submenu items for a group.
 *
 * This centralizes the logic from openlab_group_membership_tabs() so it can
 * be used by both the desktop menu and mobile drawer.
 *
 * @param object $group The group object.
 * @return array Array of submenu items.
 */
function openlab_get_group_membership_submenu_items( $group = null ) {
	global $bp;

	if ( ! $group ) {
		$group = groups_get_current_group();
	}

	if ( ! $group ) {
		return [];
	}

	$items = [];
	$current_action = bp_current_action();
	$action_var = bp_action_variable( 0 );
	$group_url = bp_get_group_url( $group );

	// Membership list link - differs for admins vs regular members
	if ( $bp->is_item_admin ) {
		$items[] = [
			'text'       => __( 'Membership', 'flavor' ),
			'href'       => $group_url . 'admin/manage-members',
			'is_current' => ( $action_var === 'manage-members' ),
		];

		// Member Requests (private groups only)
		if ( $group->status === 'private' ) {
			$items[] = [
				'text'       => __( 'Member Requests', 'flavor' ),
				'href'       => $group_url . 'admin/membership-requests',
				'is_current' => ( $action_var === 'membership-requests' ),
			];
		}
	} else {
		$items[] = [
			'text'       => __( 'Membership', 'flavor' ),
			'href'       => $group_url . 'members',
			'is_current' => ( $current_action === 'members' ),
		];
	}

	// Invite New Members (members with invite access)
	if ( bp_group_is_member() && function_exists( 'invite_anyone_access_test' ) && invite_anyone_access_test() && openlab_is_admin_truly_member() ) {
		$items[] = [
			'text'       => __( 'Invite New Members', 'flavor' ),
			'href'       => $group_url . 'invite-anyone',
			'is_current' => ( $current_action === 'invite-anyone' ),
		];
	}

	// Email Members (admins only)
	if ( $bp->is_item_admin ) {
		$items[] = [
			'text'       => __( 'Email Members', 'flavor' ),
			'href'       => $group_url . 'admin/notifications',
			'is_current' => ( $current_action === 'admin' && $action_var === 'notifications' ),
		];
	}

	// Your Email Options (members only)
	if ( bp_group_is_member() && openlab_is_admin_truly_member() ) {
		$items[] = [
			'text'       => __( 'Your Email Options', 'flavor' ),
			'href'       => $group_url . 'notifications',
			'is_current' => ( $current_action === 'notifications' ),
		];
	}

	return $items;
}

/**
 * Get the Activity submenu items for a group.
 *
 * @return array Array of submenu items.
 */
function openlab_get_group_activity_submenu_items() {
	$items    = [];
	$base_url = bp_get_group_permalink( groups_get_current_group() ) . 'activity';

	$current_type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';

	$items[] = [
		'text'       => __( 'All', 'flavor' ),
		'href'       => $base_url,
		'is_current' => empty( $current_type ),
	];

	$items[] = [
		'text'       => __( 'Mine', 'flavor' ),
		'href'       => $base_url . '?type=mine',
		'is_current' => ( $current_type === 'mine' ),
	];

	$items[] = [
		'text'       => __( '@Mentions', 'flavor' ),
		'href'       => $base_url . '?type=mentions',
		'is_current' => ( $current_type === 'mentions' ),
	];

	$items[] = [
		'text'       => __( 'Starred', 'flavor' ),
		'href'       => $base_url . '?type=starred',
		'is_current' => ( $current_type === 'starred' ),
	];

	$items[] = [
		'text'       => __( 'Connections', 'flavor' ),
		'href'       => $base_url . '?type=connections',
		'is_current' => ( $current_type === 'connections' ),
	];

	return $items;
}

/**
 * Get the Discussion (Forum) submenu items for a group.
 *
 * @return array Array of submenu items.
 */
function openlab_get_group_forum_submenu_items() {
	$items    = [];
	$base_url = bp_get_group_permalink( groups_get_current_group() ) . 'forum';

	$items[] = [
		'text'       => __( 'All Topics', 'flavor' ),
		'href'       => $base_url,
		'is_current' => ( bp_current_action() === 'forum' && ! bp_is_action_variable( 'topic', 0 ) ),
	];

	if ( is_user_logged_in() ) {
		$items[] = [
			'text'       => __( 'New Topic', 'flavor' ),
			'href'       => $base_url . '#new-post',
			'is_current' => false,
		];
	}

	// If viewing a specific topic, add it to the submenu
	if ( bp_is_action_variable( 'topic', 0 ) && function_exists( 'bbpress' ) ) {
		$bbp       = bbpress();
		$forum_ids = bbp_get_group_forum_ids( bp_get_current_group_id() );
		$forum_id  = array_shift( $forum_ids );

		$bbp->current_forum_id = $forum_id;
		bbp_set_query_name( 'bbp_single_forum' );

		bbp_has_topics( [
			'name'           => bp_action_variable( 1 ),
			'posts_per_page' => 1,
			'show_stickies'  => false,
		] );

		if ( bbp_topics() ) {
			bbp_the_topic();
			$items[] = [
				'text'       => bbp_get_topic_title(),
				'href'       => bbp_get_topic_permalink(),
				'is_current' => true,
			];
		}
	}

	return $items;
}

/**
 * Get the Docs submenu items for a group.
 *
 * @return array Array of submenu items.
 */
function openlab_get_group_docs_submenu_items() {
	$items = [];

	$group    = groups_get_current_group();
	$base_url = bp_get_group_permalink( $group ) . 'docs';

	$current_view = function_exists( 'bp_docs_current_view' ) ? bp_docs_current_view() : '';

	$items[] = [
		'text'       => __( 'All Docs', 'flavor' ),
		'href'       => $base_url,
		'is_current' => ( $current_view === 'list' || empty( $current_view ) ),
	];

	if ( is_user_logged_in() && current_user_can( 'bp_docs_create' ) ) {
		$items[] = [
			'text'       => __( 'New Doc', 'flavor' ),
			'href'       => $base_url . '/create',
			'is_current' => ( $current_view === 'create' ),
		];
	}

	// If viewing a specific doc, add it to the submenu
	if ( function_exists( 'bp_docs_get_current_doc' ) ) {
		$current_doc = bp_docs_get_current_doc();
		if ( $current_doc ) {
			$items[] = [
				'text'       => $current_doc->post_title,
				'href'       => trailingslashit( $base_url ) . $current_doc->post_name,
				'is_current' => true,
			];
		}
	}

	return $items;
}

/**
 * Get the File Library submenu items for a group.
 *
 * @return array Array of submenu items.
 */
function openlab_get_group_files_submenu_items() {
	$items    = [];
	$base_url = bp_get_group_permalink( groups_get_current_group() ) . 'files';

	$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';

	$items[] = [
		'text'       => __( 'All Files', 'flavor' ),
		'href'       => $base_url,
		'is_current' => ( $action !== 'add_new_file' ),
	];

	$user_can_upload = current_user_can( 'bp_moderate' ) || groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() );
	if ( $user_can_upload ) {
		$items[] = [
			'text'       => __( 'Add New File', 'flavor' ),
			'href'       => $base_url . '?action=add_new_file',
			'is_current' => ( $action === 'add_new_file' ),
		];
	}

	return $items;
}

/**
 * Get the Connections submenu items for a group.
 *
 * @return array Array of submenu items.
 */
function openlab_get_group_connections_submenu_items() {
	$items    = [];
	$base_url = bp_get_group_permalink( groups_get_current_group() ) . 'connections';

	$current_subpage = bp_action_variable( 0 );

	$items[] = [
		'text'       => __( 'Connected Groups', 'flavor' ),
		'href'       => $base_url,
		'is_current' => empty( $current_subpage ),
	];

	$items[] = [
		'text'       => __( 'Make a Connection', 'flavor' ),
		'href'       => $base_url . '/new/',
		'is_current' => ( $current_subpage === 'new' ),
	];

	$items[] = [
		'text'       => __( 'Invitations', 'flavor' ),
		'href'       => $base_url . '/invitations/',
		'is_current' => ( $current_subpage === 'invitations' ),
	];

	return $items;
}

/**
 * Get the group mobile anchor link items as an array.
 *
 * @return array Array of anchor link items suitable for drawer rendering.
 */
function openlab_get_group_mobile_anchor_items() {
	$items = [];

	$group_id = bp_get_current_group_id();

	// Non-public groups shouldn't show this to non-members.
	$group = groups_get_current_group();
	if ( 'public' !== $group->status && empty( $group->user_has_access ) ) {
		return $items;
	}

	// Related Sites link.
	if ( groups_get_groupmeta( $group_id, 'openlab_related_links_list_enable' ) ) {
		$related_links = openlab_get_group_related_links( $group_id );
		if ( ! empty( $related_links ) ) {
			$items[] = [
				'text' => 'Related Sites',
				'href' => '#group-related-links-sidebar-widget',
			];
		}
	}

	// Portfolios link.
	if ( openlab_portfolio_list_enabled_for_group() ) {
		$portfolio_data = openlab_get_group_member_portfolios( $group_id );
		if ( ! empty( $portfolio_data ) ) {
			$items[] = [
				'text' => 'Portfolios',
				'href' => '#group-member-portfolio-sidebar-widget',
			];
		}
	}

	return $items;
}

/**
 * Register the Archive pages mobile drawer.
 *
 * This registers a drawer that will be rendered in the unified drawer container
 * for archive pages (groups, people, search, resources) on mobile.
 *
 * @param string $drawer_id Unique ID for the drawer.
 * @param string $title     Title for the drawer panel.
 */
function openlab_register_archive_mobile_drawer( $drawer_id, $title ) {
	// Store title in a static variable for the render callback.
	static $drawer_titles = [];
	$drawer_titles[ $drawer_id ] = $title;

	openlab_register_drawer( $drawer_id, function() use ( $drawer_id, $title ) {
		return openlab_render_archive_mobile_drawer( $drawer_id, $title );
	} );
}

/**
 * Render the Archive pages mobile drawer content.
 *
 * @param string $drawer_id Unique ID for the drawer.
 * @param string $title     Title for the drawer panel.
 * @return string Drawer HTML.
 */
function openlab_render_archive_mobile_drawer( $drawer_id, $title ) {
	// Capture the sidebar content.
	ob_start();
	get_sidebar( 'group-archive' );
	$sidebar_content = ob_get_clean();

	if ( empty( $sidebar_content ) ) {
		return '';
	}

	return openlab_render_drawer( [
		'id'            => $drawer_id,
		'default_panel' => $drawer_id . '-panel',
		'panels'        => [
			[
				'id'         => $drawer_id . '-panel',
				'heading'    => $title,
				'content'    => $sidebar_content,
				'is_root'    => true,
				'show_close' => true,
			],
		],
	] );
}

/**
 * Get member navigation items.
 *
 * Single source of truth for member nav, used by both desktop and mobile renderers.
 *
 * @return array Nav items array with structure:
 *               [
 *                   'text'          => string,
 *                   'href'          => string,
 *                   'is_current'    => bool,
 *                   'count'         => int (optional),
 *                   'submenu_items' => array (optional),
 *                   'is_anchor'     => bool (optional, for in-page links),
 *                   'icon'          => string (optional, e.g., 'fa fa-chevron-circle-right'),
 *               ]
 */
function openlab_get_member_nav_items() {
	$dud = bp_displayed_user_domain();
	if ( ! $dud ) {
		$dud = bp_loggedin_user_domain();
	}

	$items = [];

	$portfolio_label = openlab_get_portfolio_label(
		[
			'user_id' => bp_displayed_user_id(),
			'case'    => 'upper',
		]
	);

	$is_own_profile = is_user_logged_in() && openlab_is_my_profile();

	// Get unread counts for visual indicators (no count badges in labels).
	$user_unread_counts = is_user_logged_in() ? openlab_get_user_unread_counts( bp_loggedin_user_id() ) : [];

	if ( $is_own_profile ) {
		// === OWN PROFILE NAV ===
		// Matches My OpenLab flyout structure exactly (minus Sign Out).

		// My Settings (with submenu) - first, per flyout
		$is_settings = bp_is_user_settings() || bp_is_user_change_avatar() || bp_is_user_profile_edit();
		$items[] = [
			'slug'          => 'my-settings',
			'text'          => 'My Settings',
			'href'          => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'settings' ] ) ),
			'is_current'    => $is_settings,
			'submenu_items' => array_values( openlab_my_settings_submenu_items() ),
		];

		// My Activity (with submenu)
		$items[] = [
			'slug'          => 'my-activity',
			'text'          => 'My Activity',
			'href'          => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'my-activity' ] ) ),
			'is_current'    => bp_is_current_component( 'my-activity' ),
			'submenu_items' => array_values( openlab_my_activity_submenu_items() ),
		];

		// My Portfolio - links to actual portfolio, not anchor
		if ( openlab_user_has_portfolio( bp_loggedin_user_id() ) ) {
			$items[] = [
				'slug' => 'my-portfolio',
				'text' => 'My Portfolio',
				'href' => openlab_get_user_portfolio_profile_url( bp_loggedin_user_id() ),
			];
		} else {
			$items[] = [
				'slug' => 'my-portfolio',
				'text' => 'Create a Portfolio',
				'href' => openlab_get_portfolio_creation_url(),
			];
		}

		// My Courses
		$items[] = [
			'slug'       => 'my-courses',
			'text'       => 'My Courses',
			'href'       => home_url( '/my-courses' ),
			'is_current' => is_page( 'my-courses' ) || openlab_is_create_group( 'course' ),
		];

		// My Projects
		$items[] = [
			'slug'       => 'my-projects',
			'text'       => 'My Projects',
			'href'       => home_url( '/my-projects' ),
			'is_current' => is_page( 'my-projects' ) || openlab_is_create_group( 'project' ),
		];

		// My Clubs
		$items[] = [
			'slug'       => 'my-clubs',
			'text'       => 'My Clubs',
			'href'       => home_url( '/my-clubs' ),
			'is_current' => is_page( 'my-clubs' ) || openlab_is_create_group( 'club' ),
		];

		// My Friends (with submenu, has-unread class instead of count)
		if ( bp_is_active( 'friends' ) ) {
			$items[] = [
				'slug'          => 'my-friends',
				'text'          => 'My Friends',
				'href'          => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'friends' ] ) ),
				'is_current'    => bp_is_user_friends(),
				'class'         => ! empty( $user_unread_counts['friend_requests'] ) ? 'has-unread' : '',
				'submenu_items' => array_values( openlab_my_friends_submenu_items() ),
			];
		}

		// My Messages (with submenu, has-unread class instead of count)
		if ( bp_is_active( 'messages' ) ) {
			$items[] = [
				'slug'          => 'my-messages',
				'text'          => 'My Messages',
				'href'          => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'messages', 'inbox' ] ) ),
				'is_current'    => bp_is_user_messages(),
				'class'         => ! empty( $user_unread_counts['messages'] ) ? 'has-unread' : '',
				'submenu_items' => array_values( openlab_my_messages_submenu_items() ),
			];
		}

		// My Invitations (with submenu, has-unread class instead of count)
		if ( bp_is_active( 'groups' ) ) {
			$items[] = [
				'slug'          => 'my-invitations',
				'text'          => 'My Invitations',
				'href'          => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites' ] ) ),
				'is_current'    => bp_is_current_action( 'invites' ) || bp_is_current_action( 'sent-invites' ) || bp_is_current_component( 'invite-anyone' ),
				'class'         => ! empty( $user_unread_counts['group_invites'] ) ? 'has-unread' : '',
				'submenu_items' => array_values( openlab_my_invitations_submenu_items() ),
			];
		}

		// My Dashboard - no chevron icon
		$items[] = [
			'slug' => 'my-dashboard',
			'text' => 'My Dashboard',
			'href' => openlab_get_my_dashboard_url( bp_loggedin_user_id() ),
		];
	} else {
		// === OTHER USER'S PROFILE NAV ===

		// Profile
		$items[] = [
			'slug'       => 'profile',
			'text'       => 'Profile',
			'href'       => $dud . '/',
			'is_current' => bp_is_user_activity(),
		];

		// Portfolio link (if visible)
		$has_portfolio = openlab_user_has_portfolio( bp_displayed_user_id() );
		$portfolio_visible = $has_portfolio && (
			! openlab_group_is_hidden( openlab_get_user_portfolio_id() )
			|| groups_is_user_member( bp_loggedin_user_id(), openlab_get_user_portfolio_id() )
		);

		if ( $portfolio_visible ) {
			$items[] = [
				'slug'      => 'portfolio',
				'text'      => $portfolio_label,
				'href'      => '#portfolio-sidebar-inline-widget',
				'is_anchor' => true,
			];
		}

		$current_group_view = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';

		// Courses
		$items[] = [
			'slug'       => 'courses',
			'text'       => 'Courses',
			'href'       => $dud . bp_get_groups_slug() . '/?type=course',
			'is_current' => bp_is_user_groups() && 'course' === $current_group_view,
		];

		// Projects
		$items[] = [
			'slug'       => 'projects',
			'text'       => 'Projects',
			'href'       => $dud . bp_get_groups_slug() . '/?type=project',
			'is_current' => bp_is_user_groups() && 'project' === $current_group_view,
		];

		// Clubs
		$items[] = [
			'slug'       => 'clubs',
			'text'       => 'Clubs',
			'href'       => $dud . bp_get_groups_slug() . '/?type=club',
			'is_current' => bp_is_user_groups() && 'club' === $current_group_view,
		];

		// Friends
		$items[] = [
			'slug'       => 'friends',
			'text'       => 'Friends',
			'href'       => $dud . bp_get_friends_slug() . '/',
			'is_current' => bp_is_user_friends(),
		];
	}

	return $items;
}

/**
 * Render the Member pages desktop navigation.
 *
 * Uses openlab_get_member_nav_items() as data source, with desktop-specific adjustments:
 * - Prepends 'My Profile' link (own profile only)
 * - Skips 'My Portfolio' (desktop has separate widget)
 * - Shows count badges for Friends/Messages/Invitations
 * - Shows chevron icon on Dashboard
 *
 * @param bool $show_submenus Whether to show submenu items for current section. Default true.
 */
function openlab_render_member_desktop_nav( $show_submenus = true ) {
	$nav_items = openlab_get_member_nav_items();

	if ( empty( $nav_items ) ) {
		return;
	}

	$output = '';
	$is_own_profile = is_user_logged_in() && openlab_is_my_profile();

	// Desktop-only: Prepend 'My Profile' link for own profile
	if ( $is_own_profile ) {
		$dud = bp_displayed_user_domain() ? bp_displayed_user_domain() : bp_loggedin_user_domain();
		$is_profile_current = bp_is_user_activity() && ! bp_is_current_component( 'my-activity' );
		$profile_classes = [ 'sq-bullet', 'mol-profile', 'my-profile' ];
		if ( $is_profile_current ) {
			$profile_classes[] = 'selected-page';
		}

		$output .= sprintf(
			'<li class="%s"><a href="%s">My Profile</a></li>',
			esc_attr( implode( ' ', $profile_classes ) ),
			esc_url( $dud )
		);
	}

	// Desktop-only: Get counts for badges
	$desktop_counts = [];
	if ( $is_own_profile ) {
		if ( bp_is_active( 'friends' ) ) {
			$request_ids = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
			$desktop_counts['my-friends'] = intval( count( (array) $request_ids ) );
		}
		if ( bp_is_active( 'messages' ) ) {
			$desktop_counts['my-messages'] = bp_get_total_unread_messages_count();
		}
		if ( bp_is_active( 'groups' ) ) {
			$invites = groups_get_invites_for_user();
			$desktop_counts['my-invitations'] = isset( $invites['total'] ) ? (int) $invites['total'] : 0;
		}
	}

	foreach ( $nav_items as $item ) {
		$slug = isset( $item['slug'] ) ? $item['slug'] : sanitize_title( $item['text'] );

		// Desktop-only: Skip 'My Portfolio' (has separate widget on desktop)
		if ( $slug === 'my-portfolio' && $is_own_profile ) {
			continue;
		}

		// Build classes
		$classes = [ 'sq-bullet', 'mol-' . str_replace( 'my-', '', $slug ) ];

		// Add slug-based class
		$classes[] = $slug;

		// Add current state
		if ( ! empty( $item['is_current'] ) ) {
			$classes[] = 'selected-page';
		}

		// Special handling for anchor links (portfolio widget links)
		if ( ! empty( $item['is_anchor'] ) ) {
			$classes[] = 'visible-xs';
			$classes[] = 'mobile-anchor-link';
		}

		// Add custom class if present (e.g., 'has-unread')
		if ( ! empty( $item['class'] ) ) {
			$classes[] = $item['class'];
		}

		$class_attr = implode( ' ', array_filter( $classes ) );
		$href = esc_url( $item['href'] );
		$text = esc_html( $item['text'] );

		// Desktop-only: Add count badge for specific items
		$count_badge = '';
		if ( isset( $desktop_counts[ $slug ] ) && $desktop_counts[ $slug ] > 0 ) {
			$count_badge = openlab_get_menu_count_mup( $desktop_counts[ $slug ] );
		}

		// Desktop-only: Add chevron icon for Dashboard
		$icon = '';
		if ( $slug === 'my-dashboard' ) {
			$icon = ' <span class="fa fa-chevron-circle-right" aria-hidden="true"></span>';
		}

		// Start li element
		$output .= sprintf(
			'<li class="%s"><a href="%s">%s%s%s</a>',
			esc_attr( $class_attr ),
			$href,
			$text,
			$count_badge,
			$icon
		);

		// Add submenu if present and item is current section and show_submenus is true
		if ( $show_submenus && ! empty( $item['submenu_items'] ) && ! empty( $item['is_current'] ) ) {
			$output .= '<ul class="sidebar-submenu">';
			foreach ( $item['submenu_items'] as $sub ) {
				$sub_classes = [];
				if ( ! empty( $sub['is_current'] ) ) {
					$sub_classes[] = 'selected-page';
				}
				if ( ! empty( $sub['class'] ) ) {
					$sub_classes[] = $sub['class'];
				}
				$sub_class_attr = ! empty( $sub_classes ) ? ' class="' . esc_attr( implode( ' ', $sub_classes ) ) . '"' : '';

				$output .= sprintf(
					'<li%s><a href="%s">%s</a></li>',
					$sub_class_attr,
					esc_url( $sub['href'] ),
					esc_html( $sub['text'] )
				);
			}
			$output .= '</ul>';
		}

		$output .= '</li>';
	}

	echo $output;
}

/**
 * Render the Member pages mobile drawer content.
 *
 * Uses openlab_get_member_nav_items() as the single source of truth.
 *
 * @return string Drawer HTML.
 */
function openlab_render_member_mobile_drawer() {
	$nav_items = openlab_get_member_nav_items();

	if ( empty( $nav_items ) ) {
		return '';
	}

	$panels = [];
	$root_items = [];

	// Process each nav item from the unified data source
	foreach ( $nav_items as $item ) {
		$slug = isset( $item['slug'] ) ? $item['slug'] : sanitize_title( $item['text'] );
		$panel_id = 'member-' . $slug . '-panel';

		// Build root item
		$root_item = [
			'text'       => $item['text'],
			'href'       => $item['href'],
			'is_current' => isset( $item['is_current'] ) ? $item['is_current'] : false,
		];

		// Add class if present (e.g., 'has-unread')
		if ( ! empty( $item['class'] ) ) {
			$root_item['class'] = $item['class'];
		}

		// If item has submenu, link to panel
		if ( ! empty( $item['submenu_items'] ) ) {
			$root_item['submenu'] = $panel_id;

			// Build submenu panel
			$submenu_items = [];
			foreach ( $item['submenu_items'] as $sub ) {
				$sub_item = [
					'text'       => $sub['text'],
					'href'       => $sub['href'],
					'is_current' => isset( $sub['is_current'] ) ? $sub['is_current'] : false,
				];
				if ( ! empty( $sub['class'] ) ) {
					$sub_item['class'] = $sub['class'];
				}
				$submenu_items[] = $sub_item;
			}

			$panels[] = [
				'id'          => $panel_id,
				'heading'     => $item['text'],
				'items'       => $submenu_items,
				'back_target' => 'member-mobile-root-panel',
				'back_text'   => sprintf( 'Back to Profile', $group_type_label ),
			];
		}

		$root_items[] = $root_item;
	}

	// Build the root panel
	$root_panel = [
		'id'         => 'member-mobile-root-panel',
		'heading'    => openlab_is_my_profile() ? 'My Profile' : bp_get_displayed_user_fullname(),
		'items'      => $root_items,
		'is_root'    => true,
		'show_close' => true,
	];

	// Prepend root panel to the list
	array_unshift( $panels, $root_panel );

	return openlab_render_drawer( [
		'id'            => 'member-mobile-drawer',
		'default_panel' => 'member-mobile-root-panel',
		'panels'        => $panels,
	] );
}

function openlab_bp_sidebar($type, $mobile_dropdown = false, $extra_classes = '') {
	$classes = [
		'sidebar',
		'col-sm-6',
		'col-xs-24',
		'groups' === $type ? 'pull-right' : '',
		$mobile_dropdown ? 'mobile-dropdown' : '',
		'type-' . $type,
	];

	$classes_string = implode( ' ', array_filter( $classes ) );

    echo '<div id="sidebar" role="complementary" class="' . esc_attr( $classes_string ) . ' ' . esc_attr( $extra_classes ) . '"><div class="sidebar-wrapper">';

    switch ($type) {
        case 'actions':
            openlab_group_sidebar();
            break;
        case 'members':
            bp_get_template_part('members/single/sidebar');
            break;
        case 'groups':
            get_sidebar('group-archive');
            break;
        case 'about':
            $args = array(
                'theme_location' => 'aboutmenu',
                'container' => 'div',
                'container_id' => 'about-menu',
                'menu_class' => 'sidebar-nav clearfix'
            );
            echo '<h2 class="sidebar-title hidden-xs">About</h2>';
            echo '<div class="sidebar-block hidden-xs">';
            wp_nav_menu($args);
            echo '</div>';

			echo '<h2 class="sidebar-title">Learn More</h2>';
            echo '<div class="sidebar-block sidebar-block-learnmore">';
			openlab_learnmore_sidebar();
            echo '</div>';

            break;
        case 'help':
            get_sidebar('help');
            break;
        default:
            get_sidebar();
    }

    echo '</div></div>';
}

/**
 * Mobile sidebar - for when a piece of the sidebar needs to appear above the content in the mobile space.
 *
 * @param string $type Sidebar type. 'members', 'about'.
 */
function openlab_bp_mobile_sidebar($type) {

    switch ($type) {
        case 'members':
            echo '<div id="sidebar-mobile" class="sidebar group-single-item mobile-dropdown clearfix" aria-hidden="true">';
            openlab_member_sidebar_menu(true);
            echo '</div>';
            break;
        case 'about':
            echo '<div id="sidebar-mobile" class="sidebar clearfix mobile-dropdown" aria-hidden="true">';
            $args = array(
                'theme_location' => 'aboutmenu',
                'container' => 'div',
                'container_id' => 'about-mobile-menu',
                'menu_class' => 'sidebar-nav clearfix'
            );
            echo '<div class="sidebar-block">';
            wp_nav_menu($args);
            echo '</div>';
            echo '</div>';
            break;
    }
}

/**
 * Output the sidebar content for a single group
 */
function openlab_group_sidebar($mobile = false) {

    if (bp_has_groups()) : while (bp_groups()) : bp_the_group();
		$group_site_settings = openlab_get_group_site_settings( bp_get_group_id() );

		$widget_wrapper_class = 'sidebar-widget sidebar-widget-wrapper';
		if ( ! empty( $group_site_settings['site_url'] ) && $group_site_settings['is_visible'] ) {
			$widget_wrapper_class .= ' group-has-site';
		}

            ?>
            <div class="<?php echo esc_attr( $widget_wrapper_class ); ?>" id="portfolio-sidebar-widget">
                <div class="wrapper-block">
                    <?php openlab_bp_group_site_pages(); ?>
                </div>
                <div id="sidebar-menu-wrapper" class="sidebar-menu-wrapper wrapper-block">
                    <div id="item-buttons" class="profile-nav sidebar-block clearfix">
                        <ul class="sidebar-nav clearfix">
                            <?php openlab_render_group_desktop_nav(); ?>
                            <?php echo openlab_get_group_profile_mobile_anchor_links(); ?>
                        </ul>
                    </div><!-- #item-buttons -->
                </div>
                <?php do_action('bp_group_options_nav') ?>
            </div>
            <?php
        endwhile;
    endif;
}

/**
 * 'Learn More' sidebar for About pages.
 */
function openlab_learnmore_sidebar() {
	?>
	<div class="learn-more-sidebar">
		<p>Get updates on the <a href="https://openlab.citytech.cuny.edu/openroad/">Open Road</a></p>
		<p>Follow our student bloggers on <a href="https://openlab.citytech.cuny.edu/the-buzz/">The Buzz</a></p>
		<p>Join the conversation about <a href="https://openlab.citytech.cuny.edu/openpedagogyopenlab/">Open Pedagogy</a></p>
	</div>
	<?php
}

/**
 * Member pages sidebar - modularized for easier parsing of mobile menus.
 *
 * Uses openlab_get_member_nav_items() as the single source of truth.
 *
 * @param bool $mobile Whether to render the mobile menu. Default false.
 */
function openlab_member_sidebar_menu( $mobile = false ) {
	$classes = $mobile ? 'visible-xs' : 'hidden-xs';
	$is_own_profile = is_user_logged_in() && openlab_is_my_profile();
	$id_suffix = $mobile ? '-mobile' : '';

	?>
	<div id="item-buttons<?php echo esc_attr( $id_suffix ); ?>" class="mol-menu sidebar-block <?php echo esc_attr( $classes ); ?>">
		<ul class="sidebar-nav clearfix">
			<?php openlab_render_member_desktop_nav( true ); ?>
		</ul>
	</div>
	<?php
}
/**
 * Echoes 'selected-page' class if the current page is the one passed in.
 *
 * Works like `selected()` etc from WP.
 *
 * @param mixed $selected The value to check against.
 * @param mixed $current  Optional. The current value. Defaults to true.
 * @param bool  $display  Whether to display the class. Defaults to true.
 * @return string The class if the values match, empty string otherwise.
 */
function openlab_selected_page_class( $selected, $current = true, $display = true) {
	$result = '';
	if ( (string) $selected === (string) $current ) {
		$result = ' selected-page ';
	}

	if ( $display ) {
		echo esc_html( $result );
	}

	return $result;
}

/**
 * Member pages sidebar blocks (portfolio link) - modularized for easier parsing of mobile menus
 */
function openlab_members_sidebar_blocks($mobile_hide = false) {
	static $counter = 0;

	++$counter;

	$portfolio_label = openlab_get_portfolio_label(
		[
			'user_id' => bp_displayed_user_id(),
			'case'    => 'upper',
		]
	);

    $block_classes = '';

    if ($mobile_hide) {
        $block_classes = ' hidden-xs';
    }

    if (
		( openlab_user_has_portfolio( bp_displayed_user_id() ) && ( ! openlab_group_is_hidden(openlab_get_user_portfolio_id() ) && openlab_show_portfolio_link_on_user_profile() )
		||
		openlab_is_my_profile() && openlab_user_has_portfolio( bp_displayed_user_id() )
		||
		groups_is_user_member(bp_loggedin_user_id(), openlab_get_user_portfolio_id()) )
	) : ?>

        <?php if (!$mobile_hide): ?>
            <?php if (is_user_logged_in() && openlab_is_my_profile()): ?>
                <h2 class="sidebar-header top-sidebar-header visible-xs">My <?php echo esc_html( $portfolio_label ); ?></h2>
            <?php else: ?>
                <h2 class="sidebar-header top-sidebar-header visible-xs">Member <?php echo esc_html( $portfolio_label ); ?></h2>
            <?php endif; ?>
        <?php endif; ?>

        <?php /* Abstract the displayed user id, so that this function works properly on my-* pages */ ?>
        <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id() ?>

        <div class="sidebar-block<?php echo $block_classes ?>">

            <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">

                <li class="portfolio-site-link bold">
                    <a class="bold no-deco a-grey" href="<?php openlab_user_portfolio_url() ?>">
                        <?php echo (is_user_logged_in() && openlab_is_my_profile() ? 'My ' : 'Visit '); ?>
                        <?php openlab_portfolio_label('user_id=' . $displayed_user_id . '&case=upper'); ?> Site <span class="fa fa-chevron-circle-right" aria-hidden="true"></span>
                    </a>
                </li>

                <li class="portfolio-dashboard-link">
                    <a href="<?php openlab_user_portfolio_profile_url() ?>">Profile</a>
                    <?php if (openlab_is_my_profile() && openlab_user_portfolio_site_is_local()) : ?>
                        | <a class="portfolio-dashboard-link" href="<?php openlab_user_portfolio_url() ?>/wp-admin">Dashboard</a>
                    <?php endif ?>
                </li>
            </ul>
        </div>

    <?php elseif (openlab_is_my_profile() && !bp_is_group_create()) : ?>
        <?php /* Don't show the 'Create a Portfolio' link during group (ie Portfolio) creation */ ?>
        <div class="sidebar-widget" id="portfolio-sidebar-widget">

            <?php if (is_user_logged_in() && openlab_is_my_profile()): ?>
                <h2 class="sidebar-header top-sidebar-header visible-xs">My <?php echo esc_html( $portfolio_label ); ?></h2>
            <?php endif; ?>

            <div class="sidebar-block<?php echo $block_classes ?>">
                <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
                    <li>
                        <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id(); ?>
                        <a class="bold" href="<?php openlab_portfolio_creation_url() ?>">+ Create <?php openlab_portfolio_label('leading_a=1&case=upper&user_id=' . $displayed_user_id) ?></a>
                    </li>
                </ul>
            </div>
        </div>

        <?php
    endif;
}

/**
 * Get the current filter value out of GET parameters.
 */
function openlab_get_current_filter( $param ) {
	$value = '';

	switch ( $param ) {
		case 'school' :
			if ( isset( $_GET['school'] ) ) {
				$value_raw           = wp_unslash( $_GET['school'] );
				$schools_and_offices = array_merge( openlab_get_school_list(), openlab_get_office_list() );

				if ( 'school_all' === $value_raw ) {
					$value = 'school_all';
				} elseif ( isset( $schools_and_offices[ $value_raw ] ) ) {
					$value = $value_raw;
				}
			}
		break;

		case 'group_types' :
			$value = isset( $_GET['group_types'] ) ? wp_unslash( $_GET['group_types'] ) : [];
		break;

		case 'member_type' :
			if ( isset( $_GET['member_type'] ) ) {
				$valid_user_types = openlab_valid_user_types();

				$user_types    = array_merge( array_keys( $valid_user_types ), [ 'user_type_all' ] );
				$user_type_raw = $_GET['member_type'];
				if ( in_array( $user_type_raw, $user_types ) ) {
					$value = $user_type_raw;
				}
			}
		break;

		case 'order' :
			$whitelist = [ 'alphabetical', 'newest', 'active' ];
			$value     =  isset( $_GET['order'] ) && in_array( $_GET['order'], $whitelist, true ) ? $_GET['order'] : 'active';
		break;

		case 'open' :
			$value = ! empty( $_GET['is_open'] );
		break;

		case 'cloneable' :
			$value = ! empty( $_GET['is_cloneable'] );
		break;

		case 'badges' :
			$value = isset( $_GET['badges'] ) ? array_map( 'intval', $_GET['badges'] ) : [];
		break;

		case 'sort' :
			$valid = [ 'newest', 'alphabetical', 'active' ];
			if ( isset( $_GET['sort'] ) && in_array( $_GET['sort'], $valid, true ) ) {
				$value = $_GET['sort'];
			} else {
				$value = 'active';
			}
		break;

		case 'group-types' :
			$value = [];
			if ( ! empty( $_GET['group-types'] ) && is_array( $_GET['group-types'] ) ) {
				$value = array_filter(
					wp_unslash( $_GET['group-types'] ),
					function( $group_type ) {
						return in_array( $group_type, openlab_group_types(), true );
					}
				);
			}
		break;

		default :
			$value = isset( $_GET[ $param ] ) ? wp_unslash( $_GET[ $param ] ) : '';
		break;
	}

	return $value;
}
