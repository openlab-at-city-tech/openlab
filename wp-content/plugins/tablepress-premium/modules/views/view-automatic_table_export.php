<?php
/**
 * Automatic Table Export View.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Automatic Table Export View class.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Automatic_Table_Export_View extends TablePress_Export_View {

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

		TablePress_Modules_Helper::enqueue_style( 'automatic-table-export' );
		TablePress_Modules_Helper::enqueue_script( 'automatic-table-export' );

		$this->add_meta_box( 'tables-auto-export', __( 'Automatic Table Export', 'tablepress' ), array( $this, 'postbox_auto_export' ), 'additional' );
	}

	/**
	 * Prints the content of the "Automatic Export of Tables" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public function postbox_auto_export( array $data, array $box ): void {
		$script_data = array(
			'exportFormats'   => $this->admin_page->convert_to_json_parse_output( $data['export_formats'] ),
			'csvDelimiters'   => $this->admin_page->convert_to_json_parse_output( $data['csv_delimiters'] ),
			'active'          => $this->admin_page->convert_to_json_parse_output( $data['auto_export_active'] ),
			'path'            => $this->admin_page->convert_to_json_parse_output( $data['auto_export_path'] ),
			'selectedFormats' => $this->admin_page->convert_to_json_parse_output( $data['auto_export_formats'] ),
			'csvDelimiter'    => $this->admin_page->convert_to_json_parse_output( $data['auto_export_csv_delimiter'] ),
		);

		echo "<script>\n";
		echo "window.tp = window.tp || {};\n";
		echo "tp.automatic_table_export = {};\n";
		foreach ( $script_data as $variable => $value ) {
			echo "tp.automatic_table_export.{$variable} = {$value};\n";
		}
		echo "</script>\n";

		echo '<div id="tablepress-automatic-table-export-screen"></div>';
	}

} // class TablePress_Automatic_Table_Export_View
