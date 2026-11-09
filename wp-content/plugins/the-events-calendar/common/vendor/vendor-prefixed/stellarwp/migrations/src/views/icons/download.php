<?php
/**
 * View: Download Icon.
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

$svg_classes = [ 'stellarwp-migration-icon__download' ];

if ( ! empty( $classes ) ) {
	$svg_classes = array_merge( $svg_classes, $classes );
}

if ( empty( $label ) ) {
	$label = __( 'Download icon', 'stellarwp-migrations' );
}

$template->template(
	'icons/icon/start',
	[
		'classes' => $svg_classes,
		'height'  => 12,
		'label'   => $label,
		'width'   => 12,
	],
);
?>

<path d="M11.4004 7.2002C11.7317 7.2002 12.0009 7.46848 12.001 7.7998V10.2002C12.0009 10.6774 11.811 11.1352 11.4736 11.4727C11.1361 11.8102 10.6776 12 10.2002 12H1.80078C1.32338 12 0.864916 11.8102 0.527344 11.4727C0.189922 11.1352 5.96703e-05 10.6774 0 10.2002V7.7998C6.34184e-05 7.46848 0.269248 7.2002 0.600586 7.2002C0.931802 7.20034 1.20013 7.46857 1.2002 7.7998V10.2002C1.20025 10.3592 1.2636 10.5116 1.37598 10.624C1.4885 10.7365 1.64165 10.7998 1.80078 10.7998H10.2002C10.3593 10.7998 10.5125 10.7365 10.625 10.624C10.7373 10.5116 10.8007 10.3591 10.8008 10.2002V7.7998C10.8008 7.46853 11.0691 7.20027 11.4004 7.2002Z" fill="currentColor"/>
<path d="M6 0C6.33124 0 6.60037 0.268417 6.60059 0.599609V6.35059L8.57617 4.37598C8.81044 4.1417 9.19047 4.14179 9.4248 4.37598C9.65869 4.6102 9.65867 4.98939 9.4248 5.22363L6.4248 8.22461C6.39598 8.25341 6.36327 8.27829 6.3291 8.30078C6.27503 8.33633 6.21588 8.35973 6.15527 8.37598C6.14227 8.3795 6.12956 8.38508 6.11621 8.3877C6.03949 8.40272 5.96049 8.40282 5.88379 8.3877C5.86746 8.38445 5.85176 8.37855 5.83594 8.37402C5.77756 8.35735 5.72008 8.33452 5.66797 8.2998C5.63499 8.27781 5.60411 8.25252 5.57617 8.22461L2.57617 5.22363C2.34217 4.98937 2.34215 4.61023 2.57617 4.37598C2.81045 4.1417 3.19047 4.14179 3.4248 4.37598L5.40039 6.35156V0.599609C5.40061 0.268592 5.669 0.000283275 6 0Z" fill="currentColor"/>

<?php
$template->template( 'icons/icon/end' );
