<?php
/**
 * Class Plugin_Content_Check.
 *
 * @package plugin-check
 */

namespace WordPress\Plugin_Check\Checker\Checks\Plugin_Repo;

use Exception;
use WordPress\Plugin_Check\Checker\Check_Categories;
use WordPress\Plugin_Check\Checker\Check_Result;
use WordPress\Plugin_Check\Checker\Checks\Abstract_File_Check;
use WordPress\Plugin_Check\Traits\Amend_Check_Result;
use WordPress\Plugin_Check\Traits\Mode_Aware;
use WordPress\Plugin_Check\Traits\Stable_Check;

/**
 * Check to detect PHP code obfuscation.
 *
 * @since 1.0.0
 */
class Plugin_Content_Check extends Abstract_File_Check {

	use Amend_Check_Result;
	use Mode_Aware;
	use Stable_Check;

	/**
	 * Gets the categories for the check.
	 *
	 * Every check must have at least one category.
	 *
	 * @since 1.0.0
	 *
	 * @return array The categories for the check.
	 */
	public function get_categories() {
		return array( Check_Categories::CATEGORY_PLUGIN_REPO );
	}

	/**
	 * Amends the given result by running the check on the given list of files.
	 *
	 * @since 1.0.0
	 *
	 * @param Check_Result $result The check result to amend, including the plugin context to check.
	 * @param array        $files  List of absolute file paths.
	 *
	 * @throws Exception Thrown when the check fails with a critical error (unrelated to any errors detected as part of
	 *                   the check).
	 */
	protected function check_files( Check_Result $result, array $files ) {
		$php_files        = self::filter_files_by_extension( $files, 'php' );
		$block_json_files = self::filter_files_by_regex( $files, '/(?:^|\/)block\.json$/' );

		$this->look_for_five_star_reviews( $result, $php_files );
		$this->look_for_incompatible_block_api_versions( $result, $block_json_files );
	}

	/**
	 * Looks for five star reviews and amends the given result with an error if found.
	 *
	 * @since 1.7.0
	 *
	 * @param Check_Result $result    The check result to amend, including the plugin context to check.
	 * @param array        $php_files List of absolute PHP file paths.
	 */
	protected function look_for_five_star_reviews( Check_Result $result, array $php_files ) {
		$files = self::files_preg_match_all( '/(?:https?:\/\/)?(?:wordpress\.org|wp\.org)\/.*reviews\/\?filter=5/', $php_files ); // phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText
		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				$this->add_result_error_for_file(
					$result,
					__( 'Linking directly to 5 stars reviews is not allowed.', 'plugin-check' ),
					'five_star_reviews_detected',
					$file['file'],
					$file['line'],
					$file['column'],
					'https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/',
					7
				);
			}
		}
	}

	/**
	 * Looks for block.json files with an apiVersion lower than 3.
	 *
	 * @since 1.9.0
	 *
	 * @param Check_Result $result           The check result to amend, including the plugin context to check.
	 * @param array        $block_json_files List of absolute block.json file paths.
	 */
	protected function look_for_incompatible_block_api_versions( Check_Result $result, array $block_json_files ) {
		foreach ( $block_json_files as $file ) {
			$this->validate_block_api_version( $result, $file );
		}
	}

	/**
	 * Validates apiVersion in a block.json file.
	 *
	 * @since 1.9.0
	 *
	 * @param Check_Result $result The check result to amend.
	 * @param string       $file   Absolute path to block.json.
	 */
	private function validate_block_api_version( Check_Result $result, string $file ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $file );
		if ( false === $contents ) {
			return;
		}

		$decoded = json_decode( $contents, true );
		if ( ! is_array( $decoded ) ) {
			$this->add_block_api_version_result( $result, $file );
			return;
		}

		$api_version = $decoded['apiVersion'] ?? null;
		if ( is_numeric( $api_version ) && intval( $api_version ) >= 3 ) {
			return;
		}

		$this->add_block_api_version_result( $result, $file );
	}

	/**
	 * Adds a result for an incompatible block apiVersion.
	 *
	 * @since 1.9.0
	 *
	 * @param Check_Result $result The check result to amend.
	 * @param string       $file   Absolute path to block.json.
	 */
	private function add_block_api_version_result( Check_Result $result, string $file ) {
		$is_error = true;
		$severity = $this->is_update_mode( $result ) ? 5 : 7;

		$this->add_result_message_for_file(
			$result,
			$is_error,
			__( 'Editor blocks must define "apiVersion" 3 or higher in block.json for WordPress 7.0+ iframe editor compatibility.', 'plugin-check' ),
			'block_api_version_too_low',
			$file,
			0,
			0,
			'https://developer.wordpress.org/block-editor/reference-guides/block-api/block-api-versions/block-migration-for-iframe-editor-compatibility/',
			$severity
		);
	}

	/**
	 * Gets the description for the check.
	 *
	 * Every check must have a short description explaining what the check does.
	 *
	 * @since 1.7.0
	 *
	 * @return string Description.
	 */
	public function get_description(): string {
		return __( 'Detects content that does not comply with the WordPress.org plugin guidelines.', 'plugin-check' );
	}

	/**
	 * Gets the documentation URL for the check.
	 *
	 * Every check must have a URL with further information about the check.
	 *
	 * @since 1.7.0
	 *
	 * @return string The documentation URL.
	 */
	public function get_documentation_url(): string {
		return __( 'https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/', 'plugin-check' );
	}
}
