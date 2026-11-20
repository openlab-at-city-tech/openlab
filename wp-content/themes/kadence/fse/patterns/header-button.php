<?php
/**
 * A header design: the logo and site title, then the menu and a button on the right.
 *
 * The menu is inline from 600px up and a button below that, which opens the navigation-overlay
 * template part, like the default header's menu. The button is hidden on mobile, where the
 * row has no room for it next to the menu button.
 *
 * @package kadence
 */

use Kadence\FSE\Navigation;
use Kadence\Theme;

defined( 'ABSPATH' ) || exit;

$header_attributes = [
	'backgroundColor' => 'theme-palette9',
	'style'           => [
		'spacing' => [
			'padding' => [
				'top'    => '0',
				'right'  => 'var(--global-content-edge-padding)',
				'bottom' => '0',
				'left'   => 'var(--global-content-edge-padding)',
			],
		],
	],
	'layout'          => [ 'type' => 'constrained' ],
];

$branding_attributes = [
	'metadata' => [ 'name' => __( 'Branding', 'kadence' ) ],
	'layout'   => [
		'type'     => 'flex',
		'flexWrap' => 'nowrap',
	],
];

$actions_attributes = [
	'metadata' => [ 'name' => __( 'Menu and button', 'kadence' ) ],
	'layout'   => [
		'type'           => 'flex',
		'flexWrap'       => 'nowrap',
		'justifyContent' => 'right',
	],
];

$menu_attributes = [
	'ariaLabel'              => __( 'Primary', 'kadence' ),
	'overlayMenu'            => 'mobile',
	'overlay'                => 'navigation-overlay',
	'icon'                   => 'menu',
	'textColor'              => 'theme-palette5',
	'overlayBackgroundColor' => 'theme-palette3',
	'overlayTextColor'       => 'theme-palette8',
	'layout'                 => [
		'type'           => 'flex',
		'justifyContent' => 'right',
	],
];

$menu_ref = Theme::instance()->container()->get( Navigation::class )->get_ref( 'primary' );

if ( $menu_ref ) {
	$menu_attributes = [ 'ref' => $menu_ref ] + $menu_attributes;
}

// Fixed with no shrink, so a long menu wraps instead of squeezing the button.
$buttons_attributes = [
	'metadata' => [ 'blockVisibility' => [ 'viewport' => [ 'mobile' => false ] ] ],
	'style'    => [
		'layout' => [
			'selfStretch' => 'fixedNoShrink',
			'flexSize'    => 'auto',
		],
	],
];
?>
<!-- wp:group <?php echo serialize_block_attributes( $header_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group has-theme-palette-9-background-color has-background" style="padding-top:0;padding-right:var(--global-content-edge-padding);padding-bottom:0;padding-left:var(--global-content-edge-padding)"><!-- wp:group {"style":{"dimensions":{"minHeight":"80px"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group" style="min-height:80px"><!-- wp:group <?php echo serialize_block_attributes( $branding_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:site-logo {"width":200} /-->

<!-- wp:site-title {"level":0,"textColor":"theme-palette3","style":{"typography":{"fontSize":"26px","lineHeight":"1.2","fontWeight":"700"},"elements":{"link":{":hover":{"color":{"text":"var:preset|color|theme-palette3"}}}}}} /--></div>
<!-- /wp:group -->

<!-- wp:group <?php echo serialize_block_attributes( $actions_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:navigation <?php echo serialize_block_attributes( $menu_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /-->

<!-- wp:buttons <?php echo serialize_block_attributes( $buttons_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Get started', 'kadence' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
