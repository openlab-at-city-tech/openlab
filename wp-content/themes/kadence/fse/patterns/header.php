<?php
/**
 * The header template part's content: the logo, site title and menu in one row.
 *
 * The menu is inline from 600px up and a button below that, which opens the navigation-overlay
 * template part. That's core's own collapse point for the Navigation block's Mobile setting,
 * which doesn't follow theme.json's breakpoints.
 *
 * The menu is the one at the Primary location, converted when the mode turned on.
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
?>
<!-- wp:group <?php echo serialize_block_attributes( $header_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group has-theme-palette-9-background-color has-background" style="padding-top:0;padding-right:var(--global-content-edge-padding);padding-bottom:0;padding-left:var(--global-content-edge-padding)"><!-- wp:group {"style":{"dimensions":{"minHeight":"80px"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group" style="min-height:80px"><!-- wp:group <?php echo serialize_block_attributes( $branding_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:site-logo {"width":200} /-->

<!-- wp:site-title {"level":0,"textColor":"theme-palette3","style":{"typography":{"fontSize":"26px","lineHeight":"1.2","fontWeight":"700"},"elements":{"link":{":hover":{"color":{"text":"var:preset|color|theme-palette3"}}}}}} /--></div>
<!-- /wp:group -->

<!-- wp:navigation <?php echo serialize_block_attributes( $menu_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
