<?php
/**
 * Controls display of theme files within Theme Editor.
 *
 * @package Genesis
 * @todo Amend documentation in admin/editor.php
 */

add_action('admin_notices', 'genesis_theme_files_to_edit');
/**
 * Remove the Genesis theme files from the Theme Editor. Except when
 * Genesis is the current theme.
 *
 * @since 1.4
 * @uses and changes the $themes global variable.
 *
 * @returns nothing.
 */
function genesis_theme_files_to_edit() {
	global $themes, $theme, $current_screen;

	// Check to see if we are on the editor page.
	if ( 'theme-editor' == $current_screen->id ) {
		// Do not change anything if we are in the Genesis theme.
		if ( $theme != 'Genesis' ) {

			// Remove Genesis from the theme drop down list.
			unset($themes['Genesis']);

			// Remove the genesis files from the files lists.
			$themes[$theme]['Template Files']   = preg_grep('|/genesis/|', $themes[$theme]['Template Files'],   PREG_GREP_INVERT);
			$themes[$theme]['Stylesheet Files'] = preg_grep('|/genesis/|', $themes[$theme]['Stylesheet Files'], PREG_GREP_INVERT);
		}
	}
}