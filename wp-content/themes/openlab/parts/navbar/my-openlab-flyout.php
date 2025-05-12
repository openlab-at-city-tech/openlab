<?php
/**
 * Favorites flyout for main site nav.
 */

if ( ! is_user_logged_in() ) {
	return;
}

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
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'notifications' ] ) ),
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
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'activity' ] ) ),
			],
			[
				'text' => 'Mine',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'activity', 'just-me' ] ) ),
			],
			[
				'text' => 'Favorites',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'activity', 'favorites' ] ) ),
			],
			[
				'text' => '@Mentions',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'activity', 'mentions' ] ) ),
			],
			[
				'text' => 'Starred',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'activity', 'starred' ] ) ),
			],
		],
	],
	[
		'text' => 'My Portfolio',
		'href' => home_url( '/portfolio' ),
	],
	[
		'text' => 'My Courses',
		'href' => home_url( '/courses/my-courses' ),
	],
	[
		'text' => 'My Projects',
		'href' => home_url( '/projects/my-projects' ),
	],
	[
		'text' => 'My Clubs',
		'href' => home_url( '/clubs/my-clubs' ),
	],
	[
		'text'     => 'My Friends',
		'children' => [
			[
				'text' => 'Requests Received',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'friends', 'requests' ] ) ),
			],
		],
	],
	[
		'text'     => 'My Messages',
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
	],
	[
		'text'     => 'My Invitations',
		'children' => [
			[
				'text' => 'Invitations Received',
				'href' => bp_loggedin_user_url( bp_members_get_path_chunks( [ 'invites' ] ) ),
			],
			[
				'text' => 'Invite New Members',
				'href' => home_url( '/invite' ),
			],
			[
				'text' => 'Sent Invitations',
				'href' => home_url( '/invite/sent' ),
			],
		],
	],
	[
		'text' => 'My Dashboard',
		'href' => admin_url(),
	],
	[
		'text' => 'Sign Out',
		'href' => wp_logout_url(),
	],
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
			$li_class     = $has_children ? 'has-children' : '';
			?>
			<li class="<?php echo esc_attr( $li_class ); ?>">
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
					</a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
