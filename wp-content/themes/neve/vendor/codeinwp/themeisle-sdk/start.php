<?php
/**
 * File responsible for sdk files loading.
 *
 * @package     ThemeIsleSDK
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */

namespace ThemeisleSDK;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$products               = apply_filters( 'themeisle_sdk_products', array() );
$themeisle_library_path = dirname( __FILE__ );
$files_to_load          = [
	$themeisle_library_path . '/src/Loader.php',
	$themeisle_library_path . '/src/Product.php',

	$themeisle_library_path . '/src/Common/Abstract_module.php',
	$themeisle_library_path . '/src/Common/Module_factory.php',

	$themeisle_library_path . '/src/Modules/Script_loader.php',
	$themeisle_library_path . '/src/Modules/Dashboard_widget.php',
	$themeisle_library_path . '/src/Modules/Rollback.php',
	$themeisle_library_path . '/src/Modules/Uninstall_feedback.php',
	$themeisle_library_path . '/src/Modules/Licenser.php',
	$themeisle_library_path . '/src/Modules/Endpoint.php',
	$themeisle_library_path . '/src/Modules/Notification.php',
	$themeisle_library_path . '/src/Modules/Logger.php',
	$themeisle_library_path . '/src/Modules/Translate.php',
	$themeisle_library_path . '/src/Modules/Translations.php',
	$themeisle_library_path . '/src/Modules/Review.php',
	$themeisle_library_path . '/src/Modules/Recommendation.php',
	$themeisle_library_path . '/src/Modules/Promotions.php',
	$themeisle_library_path . '/src/Modules/Welcome.php',
	$themeisle_library_path . '/src/Modules/Compatibilities.php',
	$themeisle_library_path . '/src/Modules/About_us.php',
	$themeisle_library_path . '/src/Modules/Announcements.php',
	$themeisle_library_path . '/src/Modules/Featured_plugins.php',
	$themeisle_library_path . '/src/Modules/Float_widget.php',
];

$files_to_load = array_merge( $files_to_load, apply_filters( 'themeisle_sdk_required_files', [] ) );

foreach ( $files_to_load as $file ) {
	if ( is_file( $file ) ) {
		require_once $file;
	}
}
Loader::init();

foreach ( $products as $product ) {
	Loader::add_product( $product );
}
