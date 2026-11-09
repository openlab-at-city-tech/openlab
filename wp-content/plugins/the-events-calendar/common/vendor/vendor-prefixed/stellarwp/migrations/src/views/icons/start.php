<?php
/**
 * View: Start Icon.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @var string[] $classes        Additional classes to add to the svg icon.
 * @var string   $label          The label for the icon.
 * @var bool     $is_aria_hidden Whether the icon is hidden from screen readers. Default false to show the icon.
 *
 * @package StellarWP\Migrations
 */

use TEC\Common\StellarWP\Migrations\Config;

$template = Config::get_template_engine();

$svg_classes = [ 'stellarwp-migration-icon__start' ];

if ( ! empty( $classes ) ) {
	$svg_classes = array_merge( $svg_classes, $classes );
}

if ( empty( $label ) ) {
	$label = __( 'Start icon', 'stellarwp-migrations' );
}

$template->template(
	'icons/icon/start',
	[
		'classes' => $svg_classes,
		'height'  => 16,
		'label'   => $label,
		'width'   => 16,
	],
);
?>

<path d="M12.6608 7.78802C12.8175 7.88594 12.8175 8.1141 12.6608 8.21202L5.3825 12.7609C5.21599 12.865 5 12.7453 5 12.5489V3.45108C5 3.25472 5.21599 3.13501 5.3825 3.23908L12.6608 7.78802Z" fill="currentColor" />

<?php
$template->template( 'icons/icon/end' );
