<?php
/**
 * View: Rollback Icon.
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

$svg_classes = [ 'stellarwp-migration-icon__rollback' ];

if ( ! empty( $classes ) ) {
	$svg_classes = array_merge( $svg_classes, $classes );
}

if ( empty( $label ) ) {
	$label = __( 'Rollback icon', 'stellarwp-migrations' );
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

<path d="M8.32875 3.42651C10.854 3.42681 12.9012 5.47471 12.9014 7.99995C12.9011 10.5252 10.854 12.5731 8.32875 12.5734C6.77358 12.5734 5.39914 11.7957 4.57412 10.6121C4.34137 10.2777 4.4236 9.81786 4.75781 9.58484C5.09221 9.3518 5.55194 9.43418 5.78502 9.76854C6.34585 10.573 7.27669 11.0972 8.32875 11.0972C10.0387 11.0969 11.425 9.70992 11.4252 7.99995C11.425 6.28997 10.0387 4.90379 8.32875 4.90349C7.47072 4.90349 6.98243 5.09218 6.59147 5.39691C6.43591 5.51822 6.2884 5.66289 6.13759 5.8368L7.21999 6.92002C7.61853 7.31856 7.33631 7.99995 6.77269 7.99995H3.73307C3.38368 7.99995 3.09962 7.71671 3.09961 7.36732V4.32687C3.09968 3.7633 3.78185 3.48105 4.18036 3.87957L5.09308 4.79229C5.27826 4.58622 5.47275 4.39721 5.68453 4.23214C6.38803 3.68388 7.21666 3.42651 8.32875 3.42651Z" fill="currentColor" />

<?php
$template->template( 'icons/icon/end' );
