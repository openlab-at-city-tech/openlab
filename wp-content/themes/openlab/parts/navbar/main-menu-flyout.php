<?php
/**
 * Main Menu flyout for main site nav.
 */

$my_openlab_has_unread_class = openlab_user_has_unread_counts() ? 'has-unread-upper-dot' : '';

$all_nav_links = [];

if ( is_user_logged_in() ) {
	$all_nav_links = [
		'my-openlab' => [
			'text'  => 'My OpenLab',
			'url'   => bp_loggedin_user_url(),
			'class' => 'my-openlab-main-menu-link ' . $my_openlab_has_unread_class,
		],
	];
}

$all_nav_links = array_merge( $all_nav_links, openlab_get_global_nav_links() );

if ( is_user_logged_in() ) {
	$all_nav_links['my-openlab-logout'] = [
		'text'  => 'Sign Out',
		'url'   => wp_logout_url(),
		'class' => 'my-openlab-logout',
	];
}

?>

<div class="flyout-menu" id="main-menu-flyout" role="menu" data-default-panel="main-menu-root">
	<div class="drawer-panel-submenu" id="main-menu-root">
		<div class="flyout-heading">
			<a href="<?php echo esc_url( home_url() ); ?>">
				<span>OpenLab</span>
			</a>
		</div>
		<ul class="drawer-list">
			<?php foreach ( $all_nav_links as $link_key => $link ) : ?>
				<?php
				$li_classes = [ 'drawer-item' ];
				if ( isset( $link['class'] ) ) {
					$custom_li_classes = explode( ' ', $link['class'] );
					$li_classes = array_merge( $li_classes, $custom_li_classes );
				}

				$li_class = implode( ' ', array_map( 'sanitize_html_class', $li_classes ) );
				?>

				<li class="<?php echo esc_attr( $li_class ); ?>">
					<a href="<?php echo esc_url( $link['url'] ); ?>" class="flyout-menu-link">
						<?php if ( 'my-openlab' === $link_key ) : ?>
							<span class="flyout-menu-icon icon-default">
								<?php get_template_part( 'parts/navbar/my-openlab-icon' ); ?>
							</span>
						<?php endif; ?>

						<span>
							<?php echo esc_html( $link['text'] ); ?>
						</span>
					</a>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
