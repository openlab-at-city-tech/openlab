<?php
/**
 * The navigation-overlay template part's content: the close button and the menu the header's button opens.
 *
 * The menu is the one at the Mobile location, or the Primary one when Mobile has none,
 * converted when the mode turned on.
 *
 * @package kadence
 */

use Kadence\FSE\Navigation;
use Kadence\Theme;

defined( 'ABSPATH' ) || exit;

$menu_attributes = [
	'overlayMenu'       => 'never',
	'showSubmenuIcon'   => false,
	'submenuVisibility' => 'always',
	'style'             => [ 'typography' => [ 'fontSize' => '14px' ] ],
	'layout'            => [
		'type'        => 'flex',
		'orientation' => 'vertical',
	],
];

$menu_ref = Theme::instance()->container()->get( Navigation::class )->get_ref( 'mobile' );

if ( $menu_ref ) {
	$menu_attributes = [ 'ref' => $menu_ref ] + $menu_attributes;
}
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|sm","right":"var:preset|spacing|sm","bottom":"var:preset|spacing|sm","left":"var:preset|spacing|sm"}},"dimensions":{"minHeight":"100vh"},"elements":{"link":{"color":{"text":"var:preset|color|theme-palette8"}}},"color":{"background":"#090c10"}},"textColor":"theme-palette8","layout":{"type":"default"}} -->
<div class="wp-block-group has-theme-palette-8-color has-text-color has-background has-link-color" style="background-color:#090c10;min-height:100vh;padding-top:var(--wp--preset--spacing--sm);padding-right:var(--wp--preset--spacing--sm);padding-bottom:var(--wp--preset--spacing--sm);padding-left:var(--wp--preset--spacing--sm)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:navigation-overlay-close {"style":{"color":{"background":"transparent"}},"textColor":"theme-palette8"} /--></div>
<!-- /wp:group -->

<!-- wp:navigation <?php echo serialize_block_attributes( $menu_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /--></div>
<!-- /wp:group -->
