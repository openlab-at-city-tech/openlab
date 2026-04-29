<?php
/**
 * Class External_Admin_Menu_Links_Check.
 *
 * @package plugin-check
 */

namespace WordPress\Plugin_Check\Checker\Checks\Plugin_Repo;

use Exception;
use WordPress\Plugin_Check\Checker\Check_Categories;
use WordPress\Plugin_Check\Checker\Check_Result;
use WordPress\Plugin_Check\Checker\Checks\Abstract_File_Check;
use WordPress\Plugin_Check\Traits\Amend_Check_Result;
use WordPress\Plugin_Check\Traits\Stable_Check;

/**
 * Check to detect external URLs used in WordPress admin menu functions.
 *
 * This check detects when plugins use external URLs (starting with http://, https://, or //)
 * as the menu slug parameter in WordPress admin menu functions. This practice violates
 * WordPress.org Plugin Directory Guideline #11 which prohibits hijacking the admin experience.
 *
 * @since 1.9.0
 */
class External_Admin_Menu_Links_Check extends Abstract_File_Check {

	use Amend_Check_Result;
	use Stable_Check;

	/**
	 * List of top-level admin menu functions to check.
	 *
	 * Only add_menu_page is checked as it adds to the top-level admin menu.
	 * External URLs in submenus (add_submenu_page, add_options_page, etc.) are
	 * acceptable for documentation, support links, and other resources.
	 *
	 * @since 1.9.0
	 * @var array
	 */
	protected $menu_functions = array(
		'add_menu_page',
	);

	/**
	 * Gets the categories for the check.
	 *
	 * Every check must have at least one category.
	 *
	 * @since 1.9.0
	 *
	 * @return array The categories for the check.
	 */
	public function get_categories() {
		return array( Check_Categories::CATEGORY_PLUGIN_REPO );
	}

	/**
	 * Amends the given result by running the check on the given list of files.
	 *
	 * @since 1.9.0
	 *
	 * @param Check_Result $result The check result to amend, including the plugin context to check.
	 * @param array        $files  List of absolute file paths.
	 *
	 * @throws Exception Thrown when the check fails with a critical error (unrelated to any errors detected as part of
	 *                   the check).
	 */
	protected function check_files( Check_Result $result, array $files ) {
		$php_files = self::filter_files_by_extension( $files, 'php' );

		$this->look_for_external_menu_links( $result, $php_files );
	}

	/**
	 * Looks for external URLs in admin menu functions and amends the given result with an error if found.
	 *
	 * @since 1.9.0
	 *
	 * @param Check_Result $result    The check result to amend, including the plugin context to check.
	 * @param array        $php_files List of absolute PHP file paths.
	 */
	protected function look_for_external_menu_links( Check_Result $result, array $php_files ) {
		// Build regex pattern for all menu functions.
		$functions_pattern = implode( '|', array_map( 'preg_quote', $this->menu_functions ) );

		// Pattern to match menu function calls with external URLs in the 4th parameter.
		// This regex matches:
		// - Function name from the list.
		// - Opening parenthesis.
		// - First 3 parameters (non-greedy, can be strings with single/double quotes or variables).
		// - 4th parameter containing http://, https://, or // at the start of a string.
		$pattern = '/\b(' . $functions_pattern . ')\s*\(\s*' .
			// First parameter.
			'(?:[^,]+)\s*,\s*' .
			// Second parameter.
			'(?:[^,]+)\s*,\s*' .
			// Third parameter.
			'(?:[^,]+)\s*,\s*' .
			// Fourth parameter - look for external URL (http://, https://, or //).
			'[\'"](?:https?:)?\/\/[^\'"]+[\'"]/i';

		$files = self::files_preg_match_all( $pattern, $php_files );

		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				$this->add_result_error_for_file(
					$result,
					__(
						'<strong>External URL used in top-level admin menu.</strong><br>Plugins should not use add_menu_page() for external links as it disrupts the WordPress navigation experience.',
						'plugin-check'
					),
					'external_admin_menu_link',
					$file['file'],
					$file['line'],
					$file['column'],
					'https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#11-plugins-should-not-hijack-the-admin',
					7
				);
			}
		}
	}

	/**
	 * Gets the description for the check.
	 *
	 * Every check must have a short description explaining what the check does.
	 *
	 * @since 1.9.0
	 *
	 * @return string Description.
	 */
	public function get_description(): string {
		return __( 'Detects external URLs used in top-level WordPress admin menu, which disrupts the expected user experience.', 'plugin-check' );
	}

	/**
	 * Gets the documentation URL for the check.
	 *
	 * Every check must have a URL with further information about the check.
	 *
	 * @since 1.9.0
	 *
	 * @return string The documentation URL.
	 */
	public function get_documentation_url(): string {
		return __( 'https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#11-plugins-should-not-hijack-the-admin', 'plugin-check' );
	}
}
