<?php
/**
 * Automatic Periodic Table Import View.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Automatic Periodic Table Import View class.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Automatic_Periodic_Table_Import_View extends TablePress_Import_View {

	/**
	 * Sets up the view with data and do things that are specific for this view.
	 *
	 * @since 2.0.0
	 *
	 * @param string               $action Action for this view.
	 * @param array<string, mixed> $data   Data for this view.
	 */
	#[\Override]
	public function setup( /* string */ $action, array $data ) /* : void */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		parent::setup( $action, $data );

		TablePress_Modules_Helper::enqueue_style( 'automatic-periodic-table-import', array( 'wp-components' ) );
		TablePress_Modules_Helper::enqueue_script( 'automatic-periodic-table-import' );

		$this->add_meta_box( 'tables-auto-import', __( 'Automatic Periodic Table Import', 'tablepress' ), array( $this, 'postbox_auto_import' ), 'additional' );
	}

	/**
	 * Prints the content of the "Automatic Periodic Table Import" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 *
	 * @phpstan-ignore-next-line (PHPStan would like to see a type hint.)
	 */
	public function postbox_auto_import( /* array */ $data, /* array */ $box ) /* : void */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		$tables = $this->admin_page->convert_to_json_parse_output( $data['auto_import_tables'] );

		echo "<script>\n";
		echo "window.tp = window.tp || {};\n";
		echo "tp.automatic_periodic_table_import = {\n";
		echo "\ttables: {$tables},\n";
		echo "};\n";
		echo "</script>\n";

		echo '<div id="tablepress-automatic-periodic-table-import-screen"></div>';
	}

} // class TablePress_Automatic_Periodic_Table_Import_View
