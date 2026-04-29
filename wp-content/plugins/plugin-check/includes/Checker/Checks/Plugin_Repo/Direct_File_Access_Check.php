<?php
/**
 * Class Direct_File_Access_Check.
 *
 * @package plugin-check
 */

namespace WordPress\Plugin_Check\Checker\Checks\Plugin_Repo;

use WordPress\Plugin_Check\Checker\Check_Categories;
use WordPress\Plugin_Check\Checker\Check_Result;
use WordPress\Plugin_Check\Checker\Checks\Abstract_File_Check;
use WordPress\Plugin_Check\Traits\Amend_Check_Result;
use WordPress\Plugin_Check\Traits\Find_Uninstall;
use WordPress\Plugin_Check\Traits\Stable_Check;

/**
 * Check for direct file access protection in PHP files.
 *
 * Files that only contain a PHP class the risk of something funky happening
 * when directly accessed is pretty small. For files that contain procedural code,
 * functions and function calls, the chance of security risks is a lot bigger.
 *
 * This check verifies that PHP files have proper guards to prevent direct access,
 * using checks like: if ( ! defined( 'ABSPATH' ) ) exit;
 *
 * @since 1.8.0
 */
class Direct_File_Access_Check extends Abstract_File_Check {

	use Amend_Check_Result;
	use Find_Uninstall;
	use Stable_Check;

	/**
	 * Gets the categories for the check.
	 *
	 * Every check must have at least one category.
	 *
	 * @since 1.8.0
	 *
	 * @return array The categories for the check.
	 */
	public function get_categories() {
		return array(
			Check_Categories::CATEGORY_SECURITY,
			Check_Categories::CATEGORY_PLUGIN_REPO,
		);
	}

	/**
	 * Amends the given result by running the check on the given list of files.
	 *
	 * @since 1.8.0
	 *
	 * @param Check_Result $result The check result to amend, including the plugin context to check.
	 * @param array        $files  List of absolute file paths.
	 */
	protected function check_files( Check_Result $result, array $files ) {
		// Only check PHP files.
		$php_files = self::filter_files_by_extension( $files, 'php' );

		$plugin_path = $result->plugin()->path();

		foreach ( $php_files as $file ) {
			// Skip uninstall.php files - they have their own check.
			if ( $this->is_uninstall_file( $file, $plugin_path ) ) {
				continue;
			}

			if ( ! $this->has_direct_access_protection( $file ) ) {
				if ( ! $this->is_valid_for_direct_access( $file ) ) {
					$this->add_result_error_for_file(
						$result,
						__( 'PHP file should prevent direct access. Add a check like: if ( ! defined( \'ABSPATH\' ) ) exit;', 'plugin-check' ),
						'missing_direct_file_access_protection',
						$file,
						0,
						0,
						'https://developer.wordpress.org/plugins/wordpress-org/common-issues/#direct-file-access',
						6
					);
				}
			}
		}
	}

	/**
	 * Removes PHP tag, comments, namespace and use statements from file contents.
	 *
	 * @since 1.8.0
	 *
	 * @param string $contents The file contents to clean.
	 * @return string Cleaned file contents.
	 */
	private function clean_file_contents( $contents ) {
		// Remove the opening PHP tag if present.
		$contents = preg_replace( '/^<\?php\s*/i', '', $contents );

		// Remove all comments.
		$contents = preg_replace( '/\/\*.*?\*\//s', '', $contents );
		$contents = preg_replace( '/\/\/.*$/m', '', $contents );
		$contents = preg_replace( '/#.*$/m', '', $contents );
		$contents = preg_replace( '/^\s*\*.*$/m', '', $contents );

		// Remove namespace and use statements (they don't execute code).
		$contents = preg_replace( '/namespace\s+[^{;]+(?:;|\{)/i', '', $contents );
		$contents = preg_replace( '/use\s+[^;]+;/i', '', $contents );

		return $contents;
	}

	/**
	 * Checks if a file has proper direct access protection.
	 *
	 * @since 1.8.0
	 *
	 * @param string $file The file path to check.
	 * @return bool True if the file has protection, false otherwise.
	 */
	private function has_direct_access_protection( $file ) {
		$contents = file_get_contents( $file );
		if ( false === $contents ) {
			return false;
		}

		// Remove the opening PHP tag if present.
		$contents = preg_replace( '/^<\?php\s*/i', '', $contents );

		// Get first 50 lines to check for guards.
		$lines       = explode( "\n", $contents );
		$first_lines = array_slice( $lines, 0, 50 );
		$beginning   = implode( "\n", $first_lines );

		// Clean up the content.
		$without_comments = preg_replace( '#/\*.*?\*/#s', '', $beginning );
		$without_comments = preg_replace( '#//.*$#m', '', $without_comments );
		$without_comments = preg_replace( '#^\s*\*\s.*$#m', '', $without_comments );
		$without_comments = preg_replace( '/\n\s*\n\s*\n/', "\n\n", $without_comments );
		$without_comments = trim( $without_comments );

		// Pattern 1: defined( 'ABSPATH' ) || exit; or, exit; .
		if ( preg_match( "/defined\s*\(\s*['\"]ABSPATH['\"]\s*\)\s*(?:\|\||or)\s*(?:exit|die)\s*(?:\([^)]*\))?\s*;/i", $without_comments ) ) {
			return true;
		}

		// Pattern 2: defined( 'WPINC' ) || exit; or, die();.
		if ( preg_match( "/defined\s*\(\s*['\"]WPINC['\"]\s*\)\s*(?:\|\||or)\s*(?:exit|die)\s*(?:\([^)]*\))?\s*;/i", $without_comments ) ) {
			return true;
		}

		// Pattern 3: if ( ! defined( 'ABSPATH' ) ) exit; or, exit;.
		if ( preg_match( "/if\s*\(\s*!\s*defined\s*\(\s*['\"]ABSPATH['\"]\s*\)\s*\)\s*(?:\{|exit|die)/i", $without_comments ) ) {
			return true;
		}

		// Pattern 4: if ( ! defined( 'WPINC' ) ) exit; {exit; or, die();}.
		if ( preg_match( "/if\s*\(\s*!\s*defined\s*\(\s*['\"]WPINC['\"]\s*\)\s*\)\s*(?:\{|exit|die)/i", $without_comments ) ) {
			return true;
		}

		// Pattern 5: if ( ! defined( 'ABSPATH' ) ) { die(); }, WPINC.
		if ( preg_match( "/if\s*\(\s*!\s*defined\s*\(\s*['\"](?:ABSPATH|WPINC)['\"]\s*\)\s*\)\s*\{[^}]*die\s*\(/i", $without_comments ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if a file is valid for direct access
	 *
	 * Files that only contain class/namespace definitions are generally safe for direct access.
	 * Files with procedural code (functions, hooks, defines) should always have guards.
	 *
	 * @since 1.8.0
	 *
	 * @param string $file The file path to check.
	 * @return bool True if the file is safe for direct access, false otherwise.
	 */
	private function is_valid_for_direct_access( $file ) {
		$contents = file_get_contents( $file );
		if ( false === $contents ) {
			return false;
		}

		$contents = $this->clean_file_contents( $contents );

		if ( $this->is_asset_file( $contents ) ) {
			return true;
		}

		if ( $this->has_procedural_code( $contents ) ) {
			return false;
		}

		if ( $this->has_only_safe_function_calls( $contents ) ) {
			return true;
		}

		if ( $this->has_only_class_definitions( $contents ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if file only contains return statements (asset files - safe).
	 *
	 * @since 1.8.0
	 *
	 * @param string $contents The cleaned file contents.
	 * @return bool True if file is an asset file, false otherwise.
	 */
	private function is_asset_file( $contents ) {
		$without_assignments  = preg_replace( '/\$[a-zA-Z_][a-zA-Z0-9_]*\s*=\s*[^;]+;/', '', $contents );
		$without_returns      = preg_replace( '/return\s+[^;]+;/', '', $without_assignments );
		$without_array_assign = preg_replace( '/\$[a-zA-Z_][a-zA-Z0-9_]*\s*=\s*array\s*\([^)]*\)\s*;/', '', $without_returns );
		$cleaned              = preg_replace( '/\s+/', ' ', trim( $without_array_assign ) );

		return empty( $cleaned ) || preg_match( '/^(<\?php)?\s*$/', $cleaned );
	}

	/**
	 * Checks if file contains procedural code that should have guards.
	 *
	 * @since 1.8.0
	 *
	 * @param string $contents The cleaned file contents.
	 * @return bool True if file has procedural code, false otherwise.
	 */
	private function has_procedural_code( $contents ) {
		if ( preg_match( '/\bdefine\s*\(/i', $contents ) ) {
			return true;
		}

		if ( preg_match( '/\badd_action\s*\(/i', $contents ) || preg_match( '/\badd_filter\s*\(/i', $contents ) ) {
			return true;
		}

		if ( preg_match( '/^\s*function\s+\w+\s*\(/im', $contents ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if file only contains safe function calls with return statements.
	 *
	 * @since 1.8.0
	 *
	 * @param string $contents The cleaned file contents.
	 * @return bool True if file has only safe function calls, false otherwise.
	 */
	private function has_only_safe_function_calls( $contents ) {
		$safe_if_count      = preg_match_all( '/if\s*\([^)]*(?:class_exists|function_exists|interface_exists|trait_exists|defined)\s*\(/i', $contents );
		$return_count       = preg_match_all( '/return\s*;/', $contents );
		$all_function_calls = preg_match_all( '/\b(?!class_exists|function_exists|interface_exists|trait_exists|defined|return|if|else|elseif|isset|empty|unset|array|list|echo|print)\w+\s*\(/i', $contents );

		return $safe_if_count > 0 && $return_count >= $safe_if_count && 0 === $all_function_calls;
	}

	/**
	 * Checks if file contains only class/interface/trait definitions.
	 *
	 * @since 1.8.0
	 *
	 * @param string $contents The cleaned file contents.
	 * @return bool True if file has only class definitions, false otherwise.
	 */
	private function has_only_class_definitions( $contents ) {
		return (bool) preg_match( '/(?:^|\s)(?:final\s+)?(?:abstract\s+)?(?:class|interface|trait)\s+\w+/i', $contents );
	}

	/**
	 * Gets the description for the check.
	 *
	 * Every check must have a short description explaining what the check does.
	 *
	 * @since 1.8.0
	 *
	 * @return string Description.
	 */
	public function get_description(): string {
		return __( 'Checks that PHP files have proper guards to prevent direct file access.', 'plugin-check' );
	}

	/**
	 * Gets the documentation URL for the check.
	 *
	 * Every check must have a URL with further information about the check.
	 *
	 * @since 1.8.0
	 *
	 * @return string The documentation URL.
	 */
	public function get_documentation_url(): string {
		return __( 'https://developer.wordpress.org/plugins/wordpress-org/common-issues/#direct-file-access', 'plugin-check' );
	}
}
