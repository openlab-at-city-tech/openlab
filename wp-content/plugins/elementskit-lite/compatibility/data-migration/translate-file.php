<?php

namespace ElementsKit_Lite\Compatibility\Data_Migration;

defined( 'ABSPATH' ) || exit;

class Translate_File {

	use \ElementsKit_Lite\Traits\Singleton;

	private $OK_Translate_File = 'ekit_translate_file_checked';

	public static function load_filesystem() {

		require_once ABSPATH . 'wp-admin/includes/file.php';

		WP_Filesystem();
	}

	public function init() {

		$option = get_option( $this->OK_Translate_File, 'no' );

		if ( $option == 'no' ) {

			self::load_filesystem();

			global $wp_filesystem;

			$the_dir = WP_LANG_DIR . '/plugins/';
			$files   = $wp_filesystem->dirlist( $the_dir );

			$o_dom = 'elementskit-';
			$n_dom = 'elementskit-lite-';

			$file_pattern    = 'elementskit-';
			$ln              = strlen( $file_pattern );
			$invalid_pattern = '/elementskit\-.+\-.+/';

			if ( ! empty( $files ) ) {
				foreach ( $files as $file ) {

					$nm = $file['name'];

					$dbg['cont']['pahase0'][] = 9;

					/**
					 * Checking if file name is started with elementskit-
					 */
					if ( substr( $nm, 0, $ln ) == $file_pattern ) {

						/**
						 * Checking if file name is like this pattern elementskit-blabla-
						 * If so we will not process it
						 *
						 */
						if ( preg_match( $invalid_pattern, $nm ) ) {

							continue;
						}

						/**
						 * Preparing a new name for the file
						 * and copying it i the same directory
						 *
						 */
						$new_name = str_replace( $o_dom, $n_dom, $nm );
						$wp_filesystem->copy( $the_dir . $nm, $the_dir . $new_name, true );
					}
				}
			}

			/**
			 * As we have copied all the files
			 * we do not want to run this every time
			 * so we are updating the flag in WordPress option
			 */

			update_option( $this->OK_Translate_File, 'yes' );

		}

		return true;
	}
}
