<?php
/**
 * Plugin Name:       PDF Embedder
 * Plugin URI:        https://wp-pdf.com
 * Description:       Embed PDFs straight into your posts and pages, with flexible width and height. No third-party services required. Compatible with Gutenberg Editor WordPress
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           4.8.2
 * Author:            PDF Embedder
 * Author URI:        https://wp-pdf.com
 * Text Domain:       pdf-embedder
 * Domain Path:       assets/languages
 *
 *  PDF Embedder is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 2 of the License, or
 *  any later version.
 *
 *  PDF Embedder is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with PDF Embedder. If not, see <https://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 *
 * @since 4.7.0
 */
const PDFEMB_VERSION = '4.8.2';

/**
 * Plugin Folder Path.
 *
 * @since 4.7.0
 */
define( 'PDFEMB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin Folder URL.
 *
 * @since 4.7.0
 */
define( 'PDFEMB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin Root File.
 *
 * @since 4.7.0
 */
const PDFEMB_PLUGIN_FILE = __FILE__;

/**
 * Plugin requirements.
 * TODO: improve the logic behind this check.
 */
if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {

	include_once __DIR__ . '/requirements.php';

	// Do not process the plugin code further.
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

/*
 * Action Scheduler requires a special loading procedure.
 *
 * @since 4.8.0
 */
add_action(
	'plugins_loaded',
	static function() {
		$options = ( new \PDFEmbedder\Options() )->get();

		if ( \PDFEmbedder\Options::is_on( $options['usagetracking'] ) ) {
			require_once PDFEMB_PLUGIN_DIR . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
		}
	},
	-10
);

register_activation_hook( __FILE__, '\PDFEmbedder\Plugin::activated' );

/**
 * Load the plugin instance.
 *
 * @since 4.7.0
 */
function pdf_embedder(): \PDFEmbedder\Plugin {

	static $plugin;

	if ( empty( $plugin ) ) {
		$plugin = new \PDFEmbedder\Plugin();

		$plugin->hooks();
	}

	return $plugin;
}

add_action( 'plugins_loaded', 'pdf_embedder' );
