<?php
/**
 * Favorites flyout for main site nav.
 */

if ( ! is_user_logged_in() ) {
	return;
}

$links = [
	[
		'text' => 'My OpenLab',
		'href' => home_url( 'my-openlab' ),
	],
	[
		'text' => 'My Groups',
		'href' => home_url( 'my-groups' ),
	],
	[
		'text' => 'My Courses',
		'href' => home_url( 'my-courses' ),
	],
];

?>

<div class="flyout-menu" id="my-openlab-flyout" role="menu">
	<div class="flyout-heading">
		<i class="fa fa-bookmark-o" aria-hidden="true"></i>
		<span>My OpenLab</span>
	</div>
	<ul class="flyout-menu-items">
		<?php foreach ( $links as $link ) : ?>
			<li>
				<a href="<?php echo esc_attr( $link['href'] ); ?>">
					<?php echo esc_html( $link['text'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
