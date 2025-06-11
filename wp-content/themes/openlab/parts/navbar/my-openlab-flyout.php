<?php
/**
 * Favorites flyout for main site nav.
 */

if ( ! is_user_logged_in() ) {
	return;
}

$user_unread_counts = openlab_get_user_unread_counts( bp_loggedin_user_id() );

$my_activity_url = bp_loggedin_user_url( bp_members_get_path_chunks( [ 'my-activity' ] ) );

$root_panel = [
	'my-settings' => [
		'text'   => 'My Settings',
		'href'   => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'settings' ] ) ),
		'target' => 'settings-submenu',
	],
	'my-activity' => [
		'text'   => 'My Activity',
		'href'   => $my_activity_url,
		'target' => 'activity-submenu',
	],
];

if ( openlab_user_has_portfolio( bp_loggedin_user_id() ) ) {
	$root_panel['my-portfolio'] = [
		'text' => 'My Portfolio',
		'href' => openlab_get_user_portfolio_url( bp_loggedin_user_id() ),
	];
}

$root_panel += [
	'my-courses' => [
		'text' => 'My Courses',
		'href' => home_url( '/courses/my-courses' ),
	],
	'my-projects' => [
		'text' => 'My Projects',
		'href' => home_url( '/projects/my-projects' ),
	],
	'my-clubs' => [
		'text' => 'My Clubs',
		'href' => home_url( '/clubs/my-clubs' ),
	],
	'my-friends' => [
		'text'   => 'My Friends',
		'class'  => $user_unread_counts['friend_requests'] ? 'has-unread' : '',
		'href'   => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'friends' ] ) ),
		'target' => 'friends-submenu',
	],
	'my-messages' => [
		'text'   => 'My Messages',
		'class'  => $user_unread_counts['messages'] ? 'has-unread' : '',
		'href'   => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'messages', 'inbox' ] ) ),
		'target' => 'messages-submenu',
	],
	'my-invitations' => [
		'text'   => 'My Invitations',
		'class'  => $user_unread_counts['group_invites'] ? 'has-unread' : '',
		'href'   => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites' ] ) ),
		'target' => 'invitations-submenu',
	],
	'my-dashboard' => [
		'text' => 'My Dashboard',
		'href' => openlab_get_my_dashboard_url( bp_loggedin_user_id() ),
	],
	'my-openlab-logout' => [
		'text'  => 'Sign Out',
		'href'  => wp_logout_url(),
		'class' => 'my-openlab-logout',
	],
];

$panels = [
	'root'             => $root_panel,
	'settings-submenu' => openlab_my_settings_submenu_items(),
	'activity-submenu' => [
		'all'       => [
			'text' => 'All',
			'href' => $my_activity_url,
		],
		'mine'      => [
			'text' => 'Mine',
			'href' => add_query_arg( 'type', 'mine', $my_activity_url ),
		],
		'favorites' => [
			'text' => 'Favorites',
			'href' => add_query_arg( 'type', 'favorites', $my_activity_url ),
		],
		'mentions'  => [
			'text' => '@Mentions',
			'href' => add_query_arg( 'type', 'mentions', $my_activity_url ),
		],
		'starred'   => [
			'text' => 'Starred',
			'href' => add_query_arg( 'type', 'starred', $my_activity_url ),
		],
	],
	'friends-submenu'  => [
		'friend-list'     => [
			'text' => 'Friend List',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'friends' ] ) ),
		],
		'friend-requests' => [
			'text' => 'Requests Received',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'friends', 'requests' ] ) ),
		],
	],
	'messages-submenu' => [
		'inbox'   => [
			'text' => 'Inbox',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'messages', 'inbox' ] ) ),
		],
		'sent'    => [
			'text' => 'Sent',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'messages', 'sentbox' ] ) ),
		],
		'compose' => [
			'text' => 'Compose',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'messages', 'compose' ] ) ),
		],
	],
	'invitations-submenu' => [
		'received-invitations' => [
			'text' => 'Invitations Received',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites' ] ) ),
		],
		'send-invitations'     => [
			'text' => 'Invite New Members',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites', 'invite-anyone' ] ) ),
		],
		'sent-invitations'     => [
			'text' => 'Sent Invitations',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites', 'invite-anyone', 'sent-invites' ] ) ),
		],
	],
];


?>

<div class="flyout-menu" id="my-openlab-flyout" role="menu" data-default-panel="panel-root">

	<?php foreach ( $panels as $panel_id => $items ) : ?>
		<div class="drawer-panel" id="panel-<?php echo esc_attr( $panel_id ); ?>" aria-hidden="true">
			<div class="flyout-heading">
				<?php if ( 'root' === $panel_id ) : ?>
					<?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?>
					<span>My OpenLab</span>
				<?php else : ?>
					<button class="nav-item flyout-action-button flyout-subnav-back" data-back="panel-root">
						<span>
							<svg width="32" height="31" viewBox="0 0 32 31" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M20 8.23077L12 15.6154L20 23" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</span>

						<?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?>

						<span class="back-button-text">Back to My OpenLab</span>
					</button>
				<?php endif; ?>
			</div>

			<ul class="drawer-list">
				<?php foreach ( $items as $item ) :
					$classes = [ 'drawer-item' ];
					if ( ! empty( $item['class'] ) ) {
						$classes[] = $item['class'];
					}

					$class_attr = implode( ' ', array_map( 'sanitize_html_class', $classes ) );

					if ( ! empty( $item['target'] ) ) :
						// Drawer target (not a link)
						?>
						<li class="<?php echo esc_attr( $class_attr ); ?>">
							<button class="nav-item has-submenu flyout-action-button flyout-submenu-toggle" data-target="panel-<?php echo esc_attr( $item['target'] ); ?>">
								<?php echo esc_html( $item['text'] ); ?>
							</button>
						</li>
					<?php elseif ( ! empty( $item['href'] ) ) : ?>
						<li class="<?php echo esc_attr( $class_attr ); ?>">
							<a class="nav-item" href="<?php echo esc_url( $item['href'] ); ?>">
								<?php echo esc_html( $item['text'] ); ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>

	<?php /*
	<ul class="flyout-menu-items">
		<?php foreach ( $links as $link ) : ?>
			<?php
			$has_children = ! empty( $link['children'] );

			$li_classes = [];
			if ( $has_children ) {
				$li_classes[] = 'has-children';
			}

			if ( ! empty( $link['class'] ) ) {
				$li_classes[] = $link['class'];
			}

			?>
			<li class="<?php echo esc_attr( implode( ' ', $li_classes ) ); ?>">
				<?php if ( $has_children ) : ?>
					<?php $submenu_id = sanitize_title( $link['text'] ) . '-submenu'; ?>
					<button class="flyout-submenu-toggle" aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr( $submenu_id ); ?>">
						<?php echo esc_html( $link['text'] ); ?>
					</button>
					<ul class="flyout-submenu" id="<?php echo esc_attr( $submenu_id ); ?>">
						<?php foreach ( $link['children'] as $child ) : ?>
							<li>
								<a href="<?php echo esc_url( $child['href'] ); ?>">
									<?php echo esc_html( $child['text'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<a href="<?php echo esc_url( $link['href'] ); ?>">
						<?php echo esc_html( $link['text'] ); ?>

						<?php if ( isset( $link['class'] ) && 'my-openlab-logout' === $link['class'] ) : ?>
							<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/log-out.png' ); ?>" alt="Logout Icon" class="logout-icon" aria-hidden="true" />
						<?php endif; ?>
					</a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	*/ ?>
</div>
