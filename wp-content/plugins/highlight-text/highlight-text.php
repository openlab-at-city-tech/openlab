<?php
/**
 * Plugin Name:       Highlight Text
 * Plugin URI:        https://github.com/Mamaduka/highlight
 * Description:       Text highligher for the Block Editor
 * Version:           1.2.0
 * Requires at least: 5.7
 * Requires PHP:      5.6
 * Author:            George Mamadashvili
 * Author URI:        https://mamaduka.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Mamaduka_Highlight
 */

namespace Mamaduka\Highlight;

/**
 * Register and enqueue block editor assets.
 *
 * @return void
 */
function enqueue_editor_assets() {
	$asset_filepath = __DIR__ . '/build/index.asset.php';
	$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : [
		'dependencies' => [],
		'version'      => false,
	];

	wp_enqueue_script(
		'mamaduka-highlight',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);

	wp_enqueue_style(
		'mamaduka-highlight-style',
		plugins_url( 'build/index.css', __FILE__ ),
		[],
		$asset_file['version']
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_editor_assets' );
