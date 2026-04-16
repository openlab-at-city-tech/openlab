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
	$group_name = bp_get_group_name();
	$root_items = [];

	// Get the group site settings for site links.
	$group_site_settings = openlab_get_group_site_settings( $group_id );
	$group_type = openlab_get_group_type( $group_id );

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

	// Get the BP options nav items.
	$nav_items = openlab_get_group_nav_items();
	foreach ( $nav_items as $nav_item ) {
		$root_items[] = $nav_item;
	}

	// Add mobile anchor links for Related Sites and Portfolios.
	$anchor_items = openlab_get_group_mobile_anchor_items();
	foreach ( $anchor_items as $anchor_item ) {
		$root_items[] = $anchor_item;
	}

	if ( empty( $root_items ) ) {
		return '';
	}

	return openlab_render_drawer( [
		'id'            => 'group-mobile-drawer',
		'default_panel' => 'group-mobile-root-panel',
		'panels'        => [
			[
				'id'         => 'group-mobile-root-panel',
				'heading'    => $group_name,
				'items'      => $root_items,
				'is_root'    => true,
				'show_close' => true,
			],
		],
	] );
}

/**
 * Get the BuddyPress group options nav items as an array.
 *
 * @return array Array of nav items suitable for drawer rendering.
 */
function openlab_get_group_nav_items() {
	$items = [];

	$bp = buddypress();

	if ( ! bp_is_group() || empty( $bp->groups->current_group ) ) {
		return $items;
	}

	$group_id = bp_get_current_group_id();
	$group = groups_get_current_group();
	$current_component = bp_current_component();
	$current_action = bp_current_action();

	// Get the group nav items from BuddyPress.
	$nav = $bp->groups->nav;

	if ( empty( $nav ) ) {
		return $items;
	}

	// Get secondary nav items for the group.
	$secondary_nav = $nav->get_secondary( [
		'parent_slug' => bp_get_current_group_slug(),
	] );

	if ( empty( $secondary_nav ) ) {
		return $items;
	}

	foreach ( $secondary_nav as $nav_item ) {
		// Skip items that shouldn't be shown (filtered out by theme).
		// Check if this item is typically hidden.
		$skip_slugs = [ 'invite-anyone', 'notifications', 'request-membership' ];
		if ( in_array( $nav_item->slug, $skip_slugs, true ) ) {
			continue;
		}

		// Determine if this is the current item.
		$is_current = ( $current_action === $nav_item->slug );

		$items[] = [
			'text'       => $nav_item->name,
			'href'       => $nav_item->link,
			'is_current' => $is_current,
		];
	}

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
 * Render the Member pages mobile drawer content.
 *
 * @return string Drawer HTML.
 */
function openlab_render_member_mobile_drawer() {
	if ( ! $dud = bp_displayed_user_domain() ) {
		$dud = bp_loggedin_user_domain();
	}

	$panels = [];
	$root_items = [];

	$portfolio_label = openlab_get_portfolio_label(
		[
			'user_id' => bp_displayed_user_id(),
			'case'    => 'upper',
		]
	);

	$is_activity = bp_is_my_profile() && bp_is_current_component( 'my-activity' );
	$is_settings = bp_is_user_settings() || bp_is_user_change_avatar() || bp_is_user_profile_edit();
	$is_friends  = bp_is_my_profile() && bp_is_friends_component();
	$is_messages = bp_is_my_profile() && bp_is_messages_component();
	$is_invites  = bp_is_my_profile() && ( bp_is_current_component( 'invite-anyone' ) || bp_is_groups_component() && ( bp_is_current_action( 'invites' ) || bp_is_current_action( 'sent-invites' ) || bp_is_current_action( 'invite-new-members' ) ) );

	if ( is_user_logged_in() && openlab_is_my_profile() ) {
		// My Profile
		$root_items[] = [
			'text'       => 'My Profile',
			'href'       => $dud,
			'is_current' => bp_is_user_activity(),
		];

		// My Activity (with submenu)
		$root_items[] = [
			'text'         => 'My Activity',
			'href'         => $dud . 'my-activity',
			'is_current'   => bp_is_current_component( 'my-activity' ),
			'submenu'      => 'member-activity-panel',
		];

		// Activity submenu panel
		$activity_items = [];
		foreach ( openlab_my_activity_submenu_items() as $item ) {
			$activity_items[] = [
				'text'       => $item['text'],
				'href'       => $item['href'],
				'is_current' => $item['is_current'],
			];
		}
		$panels[] = [
			'id'          => 'member-activity-panel',
			'heading'     => 'My Activity',
			'items'       => $activity_items,
			'back_target' => 'member-mobile-root-panel',
		];

		// My Settings (with submenu)
		$root_items[] = [
			'text'       => 'My Settings',
			'href'       => $dud . bp_get_settings_slug() . '/',
			'is_current' => $is_settings,
			'submenu'    => 'member-settings-panel',
		];

		// Settings submenu panel
		$settings_items = [];
		foreach ( openlab_my_settings_submenu_items() as $item ) {
			$settings_items[] = [
				'text'       => $item['text'],
				'href'       => $item['href'],
				'is_current' => $item['is_current'],
			];
		}
		$panels[] = [
			'id'          => 'member-settings-panel',
			'heading'     => 'My Settings',
			'items'       => $settings_items,
			'back_target' => 'member-mobile-root-panel',
		];

		// Portfolio anchor link
		if ( openlab_user_has_portfolio( bp_displayed_user_id() ) && ( ! openlab_group_is_hidden( openlab_get_user_portfolio_id() ) || openlab_is_my_profile() || groups_is_user_member( bp_loggedin_user_id(), openlab_get_user_portfolio_id() ) ) ) {
			$root_items[] = [
				'text' => 'My ' . $portfolio_label,
				'href' => '#portfolio-sidebar-inline-widget',
			];
		} else {
			$root_items[] = [
				'text' => 'Create ' . $portfolio_label,
				'href' => '#portfolio-sidebar-inline-widget',
			];
		}

		// My Courses, Projects, Clubs
		$root_items[] = [
			'text'       => 'My Courses',
			'href'       => bp_get_root_domain() . '/my-courses/',
			'is_current' => is_page( 'my-courses' ) || openlab_is_create_group( 'course' ),
		];

		$root_items[] = [
			'text'       => 'My Projects',
			'href'       => bp_get_root_domain() . '/my-projects/',
			'is_current' => is_page( 'my-projects' ) || openlab_is_create_group( 'project' ),
		];

		$root_items[] = [
			'text'       => 'My Clubs',
			'href'       => bp_get_root_domain() . '/my-clubs/',
			'is_current' => is_page( 'my-clubs' ) || openlab_is_create_group( 'club' ),
		];

		// My Friends (with submenu)
		if ( bp_is_active( 'friends' ) ) {
			$request_ids   = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
			$request_count = intval( count( (array) $request_ids ) );

			$root_items[] = [
				'text'       => 'My Friends' . openlab_get_menu_count_mup( $request_count ),
				'href'       => $dud . bp_get_friends_slug() . '/',
				'is_current' => bp_is_user_friends(),
				'submenu'    => 'member-friends-panel',
			];

			$friends_items = [];
			foreach ( openlab_my_friends_submenu_items() as $item ) {
				$friends_items[] = [
					'text'       => $item['text'],
					'href'       => $item['href'],
					'is_current' => $item['is_current'],
				];
			}
			$panels[] = [
				'id'          => 'member-friends-panel',
				'heading'     => 'My Friends',
				'items'       => $friends_items,
				'back_target' => 'member-mobile-root-panel',
			];
		}

		// My Messages (with submenu)
		if ( bp_is_active( 'messages' ) ) {
			$message_count = bp_get_total_unread_messages_count();

			$root_items[] = [
				'text'       => 'My Messages' . openlab_get_menu_count_mup( $message_count ),
				'href'       => $dud . bp_get_messages_slug() . '/inbox/',
				'is_current' => bp_is_user_messages(),
				'submenu'    => 'member-messages-panel',
			];

			$messages_items = [];
			foreach ( openlab_my_messages_submenu_items() as $item ) {
				$messages_items[] = [
					'text'       => $item['text'],
					'href'       => $item['href'],
					'is_current' => $item['is_current'],
				];
			}
			$panels[] = [
				'id'          => 'member-messages-panel',
				'heading'     => 'My Messages',
				'items'       => $messages_items,
				'back_target' => 'member-mobile-root-panel',
			];
		}

		// My Invitations (with submenu)
		if ( bp_is_active( 'groups' ) ) {
			$invites      = groups_get_invites_for_user();
			$invite_count = isset( $invites['total'] ) ? (int) $invites['total'] : 0;

			$root_items[] = [
				'text'       => 'My Invitations' . openlab_get_menu_count_mup( $invite_count ),
				'href'       => $dud . bp_get_groups_slug() . '/invites/',
				'is_current' => bp_is_current_action( 'invites' ) || bp_is_current_action( 'sent-invites' ) || bp_is_current_action( 'invite-new-members' ),
				'submenu'    => 'member-invitations-panel',
			];

			$invitations_items = [];
			foreach ( openlab_my_invitations_submenu_items() as $item ) {
				$invitations_items[] = [
					'text'       => $item['text'],
					'href'       => $item['href'],
					'is_current' => $item['is_current'],
				];
			}
			$panels[] = [
				'id'          => 'member-invitations-panel',
				'heading'     => 'My Invitations',
				'items'       => $invitations_items,
				'back_target' => 'member-mobile-root-panel',
			];
		}

		// My Dashboard
		$root_items[] = [
			'text'   => 'My Dashboard <span class="fa fa-chevron-circle-right" aria-hidden="true"></span>',
			'href'   => openlab_get_my_dashboard_url( bp_loggedin_user_id() ),
			'is_raw' => true,
		];
	} else {
		// Viewing someone else's profile
		$root_items[] = [
			'text'       => 'Profile',
			'href'       => $dud . '/',
			'is_current' => bp_is_user_activity(),
		];

		// Portfolio link
		if ( openlab_user_has_portfolio( bp_displayed_user_id() ) && ( ! openlab_group_is_hidden( openlab_get_user_portfolio_id() ) || openlab_is_my_profile() || groups_is_user_member( bp_loggedin_user_id(), openlab_get_user_portfolio_id() ) ) ) {
			$root_items[] = [
				'text' => $portfolio_label,
				'href' => '#portfolio-sidebar-inline-widget',
			];
		}

		$current_group_view = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';

		$root_items[] = [
			'text'       => 'Courses',
			'href'       => $dud . bp_get_groups_slug() . '/?type=course',
			'is_current' => bp_is_user_groups() && 'course' === $current_group_view,
		];

		$root_items[] = [
			'text'       => 'Projects',
			'href'       => $dud . bp_get_groups_slug() . '/?type=project',
			'is_current' => bp_is_user_groups() && 'project' === $current_group_view,
		];

		$root_items[] = [
			'text'       => 'Clubs',
			'href'       => $dud . bp_get_groups_slug() . '/?type=club',
			'is_current' => bp_is_user_groups() && 'club' === $current_group_view,
		];

		$root_items[] = [
			'text'       => 'Friends',
			'href'       => $dud . bp_get_friends_slug() . '/',
			'is_current' => bp_is_user_friends(),
		];
	}

	if ( empty( $root_items ) ) {
		return '';
	}

	// Build the root panel
	$root_panel = [
		'id'         => 'member-mobile-root-panel',
		'heading'    => openlab_is_my_profile() ? 'Menu' : bp_get_displayed_user_fullname(),
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
                            <?php bp_get_options_nav(); ?>
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
 * @param bool $mobile Whether to render the mobile menu. Default fals.
 */
function openlab_member_sidebar_menu( $mobile = false ) {

	if ( ! $dud = bp_displayed_user_domain() ) {
		$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}

	$classes = $mobile ? 'visible-xs' : 'hidden-xs';

	$portfolio_label = openlab_get_portfolio_label(
		[
			'user_id' => bp_displayed_user_id(),
			'case'    => 'upper',
		]
	);

	$is_activity = bp_is_my_profile() && bp_is_current_component( 'my-activity' );
	$is_settings = bp_is_user_settings() || bp_is_user_change_avatar() || bp_is_user_profile_edit();
	$is_friends  = bp_is_my_profile() && bp_is_friends_component();
	$is_messages = bp_is_my_profile() && bp_is_messages_component();
	$is_invites  = bp_is_my_profile() && ( bp_is_current_component( 'invite-anyone' ) || bp_is_groups_component() && ( bp_is_current_action( 'invites' ) || bp_is_current_action( 'sent-invites' ) || bp_is_current_action( 'invite-new-members' ) ) );

	if ( is_user_logged_in() && openlab_is_my_profile() ) :
		?>

		<div id="item-buttons<?php echo ( $mobile ? '-mobile' : '' ) ?>" class="mol-menu sidebar-block <?php echo $classes; ?>">

			<ul class="sidebar-nav clearfix">

				<li class="sq-bullet <?php openlab_selected_page_class( bp_is_user_activity() ); ?> mol-profile my-profile">
					<a href="<?php echo $dud ?>">My Profile</a>
				</li>

				<li class="sq-bullet <?php openlab_selected_page_class( bp_is_current_component( 'my-activity' ) ); ?> mol-profile my-activity">
					<a href="<?php echo $dud ?>my-activity">My Activity</a>

					<?php if ( $is_activity ) : ?>
						<ul class="sidebar-submenu">
							<?php $activity_submenu_items = openlab_my_activity_submenu_items(); ?>
							<?php foreach ( $activity_submenu_items as $item ) : ?>
								<li class="<?php openlab_selected_page_class( $item['is_current'] ); ?>">
									<a href="<?php echo esc_url( $item['href'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>

				<li class="sq-bullet <?php openlab_selected_page_class( $is_settings ); ?> mol-settings my-settings">
					<a href="<?php echo $dud . bp_get_settings_slug() ?>/">My Settings</a>

					<?php if ( $is_settings ) : ?>
						<ul class="sidebar-submenu">
							<?php $settings_submenu_items = openlab_my_settings_submenu_items(); ?>
							<?php foreach ( $settings_submenu_items as $item ) : ?>
								<li class="<?php openlab_selected_page_class( $item['is_current'] ); ?>">
									<a href="<?php echo esc_url( $item['href'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>

				<?php if ( openlab_user_has_portfolio( bp_displayed_user_id() ) && ( ! openlab_group_is_hidden( openlab_get_user_portfolio_id() ) || openlab_is_my_profile() || groups_is_user_member( bp_loggedin_user_id(), openlab_get_user_portfolio_id() ) ) ) : ?>
					<li id="portfolios-groups-li<?php echo ( $mobile ? '-mobile' : '' ) ?>" class="visible-xs mobile-anchor-link">
						<a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ( $mobile ? '-mobile' : '' ) ?>">My <?php echo esc_html( $portfolio_label ); ?></a>
					</li>
				<?php else : ?>
					<li id="portfolios-groups-li<?php echo ( $mobile ? '-mobile' : '' ) ?>" class="visible-xs mobile-anchor-link">
						<a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ( $mobile ? '-mobile' : '' ) ?>">Create <?php echo esc_html( $portfolio_label ); ?></a>
					</li>
				<?php endif; ?>

				<li class="sq-bullet <?php openlab_selected_page_class( is_page( 'my-courses' ) || openlab_is_create_group( 'course' ) ); ?> mol-courses my-courses"><a href="<?php echo bp_get_root_domain() ?>/my-courses/">My Courses</a></li>

				<li class="sq-bullet <?php openlab_selected_page_class( is_page( 'my-projects' ) || openlab_is_create_group( 'project' ) ); ?> mol-projects my-projects"><a href="<?php echo bp_get_root_domain() ?>/my-projects/">My Projects</a></li>

				<li class="sq-bullet <?php openlab_selected_page_class( is_page( 'my-clubs' ) || openlab_is_create_group( 'club' ) ); ?> mol-clubs my-clubs"><a href="<?php echo bp_get_root_domain() ?>/my-clubs/">My Clubs</a></li>

				<?php if ( bp_is_active( 'friends' ) ) : ?>
					<?php
					$request_ids   = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
					$request_count = intval( count( (array) $request_ids ) );
					?>
					<li class="sq-bullet <?php openlab_selected_page_class( bp_is_user_friends() ); ?> mol-friends my-friends">
						<a href="<?php echo $dud . bp_get_friends_slug() ?>/">My Friends <?php echo openlab_get_menu_count_mup( $request_count ); ?></a>

						<?php if ( $is_friends ) : ?>
							<ul class="sidebar-submenu">
								<?php $friends_submenu_items = openlab_my_friends_submenu_items(); ?>
								<?php foreach ( $friends_submenu_items as $item ) : ?>
									<li class="<?php openlab_selected_page_class( $item['is_current'] ); ?>">
										<a href="<?php echo esc_url( $item['href'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endif; ?>

				<?php if ( bp_is_active( 'messages' ) ) : ?>
					<?php $message_count = bp_get_total_unread_messages_count(); ?>
					<li class="sq-bullet <?php openlab_selected_page_class( bp_is_user_messages() ); ?> mol-messages my-messages">
						<a href="<?php echo $dud . bp_get_messages_slug() ?>/inbox/">My Messages <?php echo openlab_get_menu_count_mup( $message_count ); ?></a>

						<?php if ( $is_messages ) : ?>
							<ul class="sidebar-submenu">
								<?php $messages_submenu_items = openlab_my_messages_submenu_items(); ?>
								<?php foreach ( $messages_submenu_items as $item ) : ?>
									<li class="<?php openlab_selected_page_class( $item['is_current'] ); ?>">
										<a href="<?php echo esc_url( $item['href'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endif; ?>

				<?php if ( bp_is_active( 'groups' ) ) : ?>
					<?php
					$invites      = groups_get_invites_for_user();
					$invite_count = isset( $invites['total'] ) ? (int) $invites['total'] : 0;
					?>
					<li class="sq-bullet <?php openlab_selected_page_class( bp_is_current_action( 'invites' ) || bp_is_current_action( 'sent-invites' ) || bp_is_current_action( 'invite-new-members' ) ); ?> mol-invites my-invites">
						<a href="<?php echo $dud . bp_get_groups_slug() ?>/invites/">My Invitations <?php echo openlab_get_menu_count_mup( $invite_count ); ?></a>

						<?php if ( $is_invites ) : ?>
							<ul class="sidebar-submenu">
								<?php $invitations_submenu_items = openlab_my_invitations_submenu_items(); ?>
								<?php foreach ( $invitations_submenu_items as $item ) : ?>
									<li class="<?php openlab_selected_page_class( $item['is_current'] ); ?>">
										<a href="<?php echo esc_url( $item['href'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endif; ?>

				<li class="sq-bullet mol-dashboard my-dashboard">
					<a href="<?php echo esc_url( openlab_get_my_dashboard_url( bp_loggedin_user_id() ) ); ?>">My Dashboard <span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a>
				</li>

			</ul>

		</div>

	<?php else : ?>

		<div id="item-buttons<?php echo ( $mobile ? '-mobile' : '' ) ?>" class="mol-menu sidebar-block <?php echo $classes; ?>">

			<ul class="sidebar-nav clearfix">

				<li class="sq-bullet <?php openlab_selected_page_class( bp_is_user_activity() ); ?> mol-profile"><a href="<?php echo $dud ?>/">Profile</a></li>

				<?php if ( openlab_user_has_portfolio( bp_displayed_user_id() ) && ( ! openlab_group_is_hidden( openlab_get_user_portfolio_id() ) || openlab_is_my_profile() || groups_is_user_member( bp_loggedin_user_id(), openlab_get_user_portfolio_id() ) ) ) : ?>
					<li id="portfolios-groups-li<?php echo ( $mobile ? '-mobile' : '' ) ?>" class="visible-xs mobile-anchor-link">
						<a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ( $mobile ? '-mobile' : '' ) ?>"><?php echo esc_html( $portfolio_label ); ?></a>
					</li>
				<?php endif; ?>

				<?php $current_group_view = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : ''; ?>

				<li class="sq-bullet <?php openlab_selected_page_class( bp_is_user_groups() && 'course' === $current_group_view ); ?> mol-courses">
					<a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=course">Courses</a>
				</li>

				<li class="sq-bullet <?php openlab_selected_page_class( bp_is_user_groups() && 'project' === $current_group_view ); ?> mol-projects">
					<a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=project">Projects</a>
				</li>

				<li class="sq-bullet <?php openlab_selected_page_class( bp_is_user_groups() && 'club' === $current_group_view ); ?> mol-club">
					<a href="<?php echo $dud . bp_get_groups_slug() ?>/?type=club">Clubs</a>
				</li>

				<li class="sq-bullet <?php openlab_selected_page_class( bp_is_user_friends() ); ?> mol-friends">
					<a href="<?php echo $dud . bp_get_friends_slug() ?>/">Friends</a>
				</li>

			</ul>

		</div>

	<?php endif;
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
