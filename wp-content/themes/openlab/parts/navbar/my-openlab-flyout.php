<?php
/**
 * Favorites flyout for main site nav.
 */

if ( ! is_user_logged_in() ) {
	return;
}

$user_unread_counts = openlab_get_user_unread_counts( bp_loggedin_user_id() );

$my_openlab_has_unread_class = openlab_user_has_unread_counts() ? 'has-unread-upper-dot' : '';

$my_activity_url = bp_loggedin_user_url( bp_members_get_path_chunks( [ 'my-activity' ] ) );

$left_chevron_svg  = '<svg width="10" height="17" viewBox="0 0 10 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 1.23077L0.999999 8.61539L9 16" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$right_chevron_svg = '<svg width="10" height="18" viewBox="0 0 10 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 17L9 9L0.999999 1" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

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
		'href' => openlab_get_user_portfolio_profile_url( bp_loggedin_user_id() ),
	];
} else {
	$root_panel['my-portfolio'] = [
		'text' => 'Create a Portfolio',
		'href' => openlab_get_portfolio_creation_url(),
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

$settings_submenu = array_merge(
	[
		'my-settings-submenu-heading' => [
			'text'  => 'My Settings',
			'class' => 'flyout-subnav-heading',
		]
	],
	openlab_my_settings_submenu_items()
);

$panels = [
	'root'             => $root_panel,
	'settings-submenu' => $settings_submenu,
	'activity-submenu' => array_merge(
		[
			'my-activity-submenu-heading' => [
				'text'  => 'My Activity',
				'class' => 'flyout-subnav-heading',
			],
		],
		openlab_my_activity_submenu_items(),
	),
	'friends-submenu'  => array_merge(
		[
			'my-friends-submenu-heading' => [
				'text'  => 'My Friends',
				'class' => 'flyout-subnav-heading',
			],
		],
		openlab_my_friends_submenu_items()
	),
	'messages-submenu' => array_merge(
		[
			'my-messages-submenu-heading' => [
				'text'  => 'My Messages',
				'class' => 'flyout-subnav-heading',
			],
		],
		openlab_my_messages_submenu_items()
	),
	'invitations-submenu' => array_merge(
		[
			'my-invitations-submenu-heading' => [
				'text'  => 'My Invitations',
				'class' => 'flyout-subnav-heading',
			],
		],
		openlab_my_invitations_submenu_items()
	),
];


?>

<div class="flyout-menu" id="my-openlab-flyout" data-default-panel="panel-root">

	<?php foreach ( $panels as $panel_id => $items ) : ?>
		<?php
		$panel_classes = [ 'drawer-panel' ];
		if ( 'root' === $panel_id ) {
			$panel_classes[] = 'drawer-panel-root';
		} else {
			$panel_classes[] = 'drawer-panel-submenu';
		}

		$panel_classes_string = implode( ' ', array_map( 'sanitize_html_class', $panel_classes ) );
		?>
		<div class="<?php echo esc_attr( $panel_classes_string ); ?>" id="panel-<?php echo esc_attr( $panel_id ); ?>" inert>
			<div class="flyout-heading">
				<?php if ( 'root' === $panel_id ) : ?>
					<a href="<?php echo esc_url( bp_loggedin_user_url() ); ?>">
						<?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?>
						<span>My OpenLab</span>
					</a>
				<?php else : ?>
					<button class="nav-item flyout-action-button flyout-subnav-back <?php echo esc_attr( $my_openlab_has_unread_class ); ?>" data-back="panel-root">
						<span class="chevron-left"><?php echo $left_chevron_svg; ?></span>

						<span class="icon-default"><?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?></span>

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
							<button class="nav-item has-submenu flyout-action-button flyout-submenu-toggle" data-target="panel-<?php echo esc_attr( $item['target'] ); ?>" aria-expanded="false">
								<span><?php echo esc_html( $item['text'] ); ?></span>
								<span class="right-chevron"><?php echo $right_chevron_svg; ?></span>
							</button>
						</li>
					<?php elseif ( ! empty( $item['href'] ) ) : ?>
						<li class="<?php echo esc_attr( $class_attr ); ?>">
							<a class="nav-item" href="<?php echo esc_url( $item['href'] ); ?>">
								<?php echo esc_html( $item['text'] ); ?>
							</a>
						</li>
					<?php else : ?>
						<li class="<?php echo esc_attr( $class_attr ); ?>">
							<span class="nav-item"><?php echo esc_html( $item['text'] ); ?></span>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>
