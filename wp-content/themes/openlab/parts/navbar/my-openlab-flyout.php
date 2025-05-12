<?php
/**
 * Favorites flyout for main site nav.
 */

if ( ! is_user_logged_in() ) {
	return;
}

$user_unread_counts = openlab_get_user_unread_counts( bp_loggedin_user_id() );

$my_activity_url = bp_loggedin_user_url( bp_members_get_path_chunks( [ 'my-activity' ] ) );

$links = [
	[
		'text'     => 'My Settings',
		'children' => [
			[
				'text' => 'Edit Profile',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'profile', 'edit' ] ) ),
			],
			[
				'text' => 'Account Info',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'settings' ] ) ),
			],
			[
				'text' => 'Notifications',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'settings', 'notifications' ] ) ),
			],
			[
				'text' => 'Privacy & Data',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'settings', 'data' ] ) ),
			],
			[
				'text' => 'Delete Account',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'settings', 'delete-account' ] ) ),
			],
		],
	],
	[
		'text'     => 'My Activity',
		'children' => [
			[
				'text' => 'All',
				'href' => $my_activity_url,
			],
			[
				'text' => 'Mine',
				'href' => add_query_arg( 'type', 'mine', $my_activity_url ),
			],
			[
				'text' => 'Favorites',
				'href' => add_query_arg( 'type', 'favorites', $my_activity_url ),
			],
			[
				'text' => '@Mentions',
				'href' => add_query_arg( 'type', 'mentions', $my_activity_url ),
			],
			[
				'text' => 'Starred',
				'href' => add_query_arg( 'type', 'starred', $my_activity_url ),
			],
		],
	],
];

if ( openlab_user_has_portfolio( bp_loggedin_user_id() ) ) {
	$links[] = [
		'text' => 'My Portfolio',
		'href' => openlab_get_user_portfolio_url( bp_loggedin_user_id() ),
	];
}

$links[] = [
	'text' => 'My Courses',
	'href' => home_url( '/courses/my-courses' ),
];

$links[] = [
	'text' => 'My Projects',
	'href' => home_url( '/projects/my-projects' ),
];

$links[] = [
	'text' => 'My Clubs',
	'href' => home_url( '/clubs/my-clubs' ),
];

$links[] = [
	'text'     => 'My Friends',
	'class'    => $user_unread_counts['friends'] ? 'has-unread' : '',
	'children' => [
		[
			'text' => 'Friend List',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'friends' ] ) ),
		],
		[
			'text' => 'Requests Received',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'friends', 'requests' ] ) ),
		],
	],
];

$links[] = [
	'text'     => 'My Messages',
	'class'    => $user_unread_counts['messages'] ? 'has-unread' : '',
	'children' => [
		[
			'text' => 'Inbox',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'messages', 'inbox' ] ) ),
		],
		[
			'text' => 'Sent',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'messages', 'sentbox' ] ) ),
		],
		[
			'text' => 'Compose',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'messages', 'compose' ] ) ),
		],
	],
];

$links[] = [
	'text'     => 'My Invitations',
	'class'    => $user_unread_counts['invites'] ? 'has-unread' : '',
	'children' => [
		[
			'text' => 'Invitations Received',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites' ] ) ),
		],
		[
			'text' => 'Invite New Members',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites', 'invite-anyone' ] ) ),
		],
		[
			'text' => 'Sent Invitations',
			'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites', 'invite-anyone', 'sent-invites' ] ) ),
		],
	],
];

$links[] = [
	'text' => 'My Dashboard',
	'href' => openlab_get_my_dashboard_url( bp_loggedin_user_id() ),
];

$links[] = [
	'class' => 'my-openlab-logout',
	'text'  => 'Sign Out',
	'href'  => wp_logout_url(),
];

?>

<div class="flyout-menu" id="my-openlab-flyout" role="menu">
	<div class="flyout-heading">
		<?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?>
		<span>My OpenLab</span>
	</div>
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

						<?php if ( 'my-openlab-logout' === $link['class'] ) : ?>
							<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/log-out.png' ); ?>" alt="Logout Icon" class="logout-icon" aria-hidden="true" />
						<?php endif; ?>
					</a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
