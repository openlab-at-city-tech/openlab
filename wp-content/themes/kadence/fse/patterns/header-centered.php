<?php
/**
 * A header design: the logo and site title centered, with the menu centered below them.
 *
 * The menu is inline from 600px up and a button below that, which opens the navigation-overlay
 * template part, like the default header's menu.
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
				'top'    => 'var:preset|spacing|sm',
				'right'  => 'var(--global-content-edge-padding)',
				'bottom' => 'var:preset|spacing|sm',
				'left'   => 'var(--global-content-edge-padding)',
			],
		],
	],
	'layout'          => [ 'type' => 'constrained' ],
];

$stack_attributes = [
	'layout' => [
		'type'           => 'flex',
		'orientation'    => 'vertical',
		'justifyContent' => 'center',
	],
];

$branding_attributes = [
	'metadata' => [ 'name' => __( 'Branding', 'kadence' ) ],
	'layout'   => [
		'type'           => 'flex',
		'flexWrap'       => 'nowrap',
		'justifyContent' => 'center',
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
		'justifyContent' => 'center',
	],
];

$menu_ref = Theme::instance()->container()->get( Navigation::class )->get_ref( 'primary' );

if ( $menu_ref ) {
	$menu_attributes = [ 'ref' => $menu_ref ] + $menu_attributes;
}
?>
<!-- wp:group <?php echo serialize_block_attributes( $header_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group has-theme-palette-9-background-color has-background" style="padding-top:var(--wp--preset--spacing--sm);padding-right:var(--global-content-edge-padding);padding-bottom:var(--wp--preset--spacing--sm);padding-left:var(--global-content-edge-padding)"><!-- wp:group <?php echo serialize_block_attributes( $stack_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:group <?php echo serialize_block_attributes( $branding_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:site-logo {"width":200} /-->

<!-- wp:site-title {"level":0,"textColor":"theme-palette3","style":{"typography":{"fontSize":"26px","lineHeight":"1.2","fontWeight":"700"},"elements":{"link":{":hover":{"color":{"text":"var:preset|color|theme-palette3"}}}}}} /--></div>
<!-- /wp:group -->

<!-- wp:navigation <?php echo serialize_block_attributes( $menu_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
