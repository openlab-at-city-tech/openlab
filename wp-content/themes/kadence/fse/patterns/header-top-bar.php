<?php
/**
 * A header design: a dark top bar with a phone number and social links, above the logo and menu.
 *
 * The phone number is hidden on mobile, where the top bar keeps only the social links.
 * The menu is inline from 600px up and a button below that, which opens the
 * navigation-overlay template part, like the default header's menu.
 *
 * @package kadence
 */

use Kadence\FSE\Navigation;
use Kadence\Theme;

defined( 'ABSPATH' ) || exit;

$top_bar_attributes = [
	'metadata'        => [ 'name' => __( 'Top bar', 'kadence' ) ],
	'backgroundColor' => 'theme-palette3',
	'textColor'       => 'theme-palette9',
	'fontSize'        => 'small',
	'style'           => [
		'spacing'  => [
			'padding' => [
				'top'    => 'var:preset|spacing|xs',
				'right'  => 'var(--global-content-edge-padding)',
				'bottom' => 'var:preset|spacing|xs',
				'left'   => 'var(--global-content-edge-padding)',
			],
		],
		'elements' => [ 'link' => [ 'color' => [ 'text' => 'var:preset|color|theme-palette9' ] ] ],
	],
	'layout'          => [ 'type' => 'constrained' ],
];

$phone_attributes = [
	'metadata' => [ 'blockVisibility' => [ 'viewport' => [ 'mobile' => false ] ] ],
];

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
<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:group <?php echo serialize_block_attributes( $top_bar_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group has-theme-palette-9-color has-theme-palette-3-background-color has-text-color has-background has-link-color has-small-font-size" style="padding-top:var(--wp--preset--spacing--xs);padding-right:var(--global-content-edge-padding);padding-bottom:var(--wp--preset--spacing--xs);padding-left:var(--global-content-edge-padding)"><!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:paragraph <?php echo serialize_block_attributes( $phone_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<p><?php esc_html_e( 'Call us: +1 (555) 555-0100', 'kadence' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"size":"has-small-icon-size"} -->
<ul class="wp-block-social-links has-small-icon-size"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group <?php echo serialize_block_attributes( $header_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group has-theme-palette-9-background-color has-background" style="padding-top:0;padding-right:var(--global-content-edge-padding);padding-bottom:0;padding-left:var(--global-content-edge-padding)"><!-- wp:group {"style":{"dimensions":{"minHeight":"80px"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group" style="min-height:80px"><!-- wp:group <?php echo serialize_block_attributes( $branding_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> -->
<div class="wp-block-group"><!-- wp:site-logo {"width":200} /-->

<!-- wp:site-title {"level":0,"textColor":"theme-palette3","style":{"typography":{"fontSize":"26px","lineHeight":"1.2","fontWeight":"700"},"elements":{"link":{":hover":{"color":{"text":"var:preset|color|theme-palette3"}}}}}} /--></div>
<!-- /wp:group -->

<!-- wp:navigation <?php echo serialize_block_attributes( $menu_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
