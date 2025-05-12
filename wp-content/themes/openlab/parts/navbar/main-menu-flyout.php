<?php
/**
 * Main Menu flyout for main site nav.
 */

$user_unread_counts = openlab_get_user_unread_counts( bp_loggedin_user_id() );

$has_any_unread = (
	$user_unread_counts['messages'] > 0 ||
	$user_unread_counts['friend_requests'] > 0 ||
	$user_unread_counts['group_invites'] > 0
);

$my_openlab_has_unread_class = $has_any_unread ? 'has-unread' : '';

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

?>

<div class="flyout-menu" id="main-menu-flyout" role="menu">
	<div class="flyout-heading">
		<span>OpenLab</span>
	</div>
	<ul class="flyout-menu-items">
		<?php foreach ( $all_nav_links as $link_key => $link ) : ?>
			<?php $li_class = isset( $link['class'] ) ? $link['class'] : ''; ?>
			<li class="<?php echo esc_attr( $li_class ); ?>">
				<a href="<?php echo esc_url( $link['url'] ); ?>" class="flyout-menu-link">
					<?php if ( 'my-openlab' === $link_key ) : ?>
						<span class="flyout-menu-icon">
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
