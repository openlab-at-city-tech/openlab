<?php defined( 'ABSPATH' ) || exit;

return apply_filters(
	'su/data/addons',
	array(
		array(
			'id'          => 'bundle',
			'slug'        => 'add-ons-bundle',
			'title'       => __( 'Add-ons Bundle', 'shortcodes-ultimate' ),
			'description' => __( 'Three-in-one, best price, simple', 'shortcodes-ultimate' ),
			'permalink'   => 'https://getshortcodes.com/add-ons/add-ons-bundle/',
			'is_bundle'   => true,
		),
		array(
			'id'          => 'extra',
			'slug'        => 'additional-shortcodes',
			'title'       => __( 'Extra Shortcodes', 'shortcodes-ultimate' ),
			'description' => __( 'A set of 15 additional shortcodes', 'shortcodes-ultimate' ),
			'permalink'   => 'https://getshortcodes.com/add-ons/extra-shortcodes/',
		),
		array(
			'id'          => 'maker',
			'slug'        => 'shortcode-creator',
			'title'       => __( 'Shortcode Creator', 'shortcodes-ultimate' ),
			'description' => __( 'Create your own shortcodes', 'shortcodes-ultimate' ),
			'permalink'   => 'https://getshortcodes.com/add-ons/shortcode-creator/',
		),
		array(
			'id'          => 'skins',
			'slug'        => 'additional-skins',
			'title'       => __( 'Additional Skins', 'shortcodes-ultimate' ),
			'description' => __( 'Add more style to your shortcodes', 'shortcodes-ultimate' ),
			'permalink'   => 'https://getshortcodes.com/add-ons/additional-skins/',
		),
	)
);
