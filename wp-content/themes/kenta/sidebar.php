<?php
/**
 * The default primary sidebar.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Kenta
 */

use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( kenta_is_woo_shop() ) {
	$default_sidebar = apply_filters( 'kenta_filter_store_sidebar_id', 'store-sidebar', 'store' );
} else {
	$default_sidebar = apply_filters( 'kenta_filter_default_sidebar_id', 'primary-sidebar', 'primary' );
}

$attrs = [
	'class' => Utils::clsx( [
		'kenta-sidebar sidebar-primary shrink-0',
		'no-underline' => ! CZ::checked( 'kenta_global_sidebar_link-underline' ),
		'kenta-heading kenta-heading-' . CZ::get( 'kenta_global_sidebar_title-style' ),
	] ),
	'role'  => 'complementary',
];

if ( is_customize_preview() ) {
	$attrs['data-shortcut']          = 'border';
	$attrs['data-shortcut-location'] = 'kenta_global:kenta_global_sidebar_section';
}

?>

<?php if ( is_active_sidebar( $default_sidebar ) ): ?>
    <div <?php Utils::print_attribute_string( $attrs ); ?>>
		<?php dynamic_sidebar( $default_sidebar ); ?>
    </div>
<?php endif; ?>
