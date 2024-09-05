<?php
/**
 * TablePress DataTables Buttons.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables Buttons feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_Buttons {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Registers necessary plugin filter hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'tablepress_view_data', array( __CLASS__, 'add_edit_screen_elements' ), 10, 2 );
		}

		add_filter( 'tablepress_table_template', array( __CLASS__, 'add_option_to_table_template' ) );
		add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'add_shortcode_parameters' ) );
		add_filter( 'tablepress_table_js_options', array( __CLASS__, 'pass_render_options_to_js_options' ), 10, 3 );
		add_filter( 'tablepress_datatables_parameters', array( __CLASS__, 'set_datatables_parameters' ), 10, 4 );
		add_filter( 'tablepress_datatables_language_strings', array( __CLASS__, 'add_datatables_language_strings' ), 9, 2 ); // Run at priority 9 so that overriding is easier on default priority.

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_css_files' ) );
		}
	}

	/**
	 * Adds options related to DataTables Buttons to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_buttons'] = '';
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the "DataTables Buttons" feature.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data   Data for this screen.
	 * @param string               $action Action for this screen.
	 * @return array<string, mixed> Modified data for this screen.
	 */
	public static function add_edit_screen_elements( array $data, string $action ): array {
		if ( 'edit' === $action ) {
			// Add a meta box below the default meta boxes, by using the "low" priority.
			add_meta_box( 'tablepress_edit-datatables-buttons', __( 'Buttons', 'tablepress' ), array( __CLASS__, 'postbox_datatables_buttons' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_script( 'datatables-buttons', array( 'jquery-core', 'jquery-ui-sortable' ) );

			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds DataTables Buttons script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-datatables-buttons';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "DataTables Buttons" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_datatables_buttons( array $data, array $box ): void {
		$help_box_content = '<p>' . __( 'The “Buttons” module can add buttons for “Copy to Clipboard”, “Save to PDF”, “Save to Excel”, “Save to CSV”, a “Print view”, and “Column Visibility” above your tables, so that site visitors can perform those actions.', 'tablepress' ) . '</p>';
		$help_box_content .= '<p>' . __( 'Choose the desired buttons and drag them into the desired order:', 'tablepress' ) . ' ' . __( 'Use drag and drop with your mouse or double-click the buttons.', 'tablepress' ) . '</p>';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-datatables-buttons-requirements"><em><?php printf( __( 'This feature is only available when the “%1$s” and “%2$s” checkboxes in the “%3$s” and “%4$s” sections are checked.', 'tablepress' ), __( 'Table Head Row', 'tablepress' ), __( 'Enable Visitor Features', 'tablepress' ), __( 'Table Options', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
		<p id="notice-datatables-buttons-custom-commands"><em><?php printf( __( 'This feature is currently being controlled via the %1$s command in the “%2$s” text field in the “%3$s” section.', 'tablepress' ), '<code>"buttons"</code>', __( 'Custom Commands', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
		<p class="description"><?php _e( 'Choose the desired buttons and drag them into the desired order:', 'tablepress' ); ?></p>
		<div class="drag-box-section">
			<div class="drag-box-section-wrapper">
				<div class="drag-box-wrapper-label"><?php _e( 'Shown buttons:', 'tablepress' ); ?></div>
				<div id="datatables-buttons-drag-box-wrapper-active" class="drag-box-wrapper"></div>
			</div>
			<div class="drag-box-section-wrapper">
				<div class="drag-box-wrapper-label"><?php _e( 'Available buttons:', 'tablepress' ); ?></div>
				<div id="datatables-buttons-drag-box-wrapper-inactive" class="drag-box-wrapper"><div class="drag-box">
						<input type="hidden" value="colvis" id="option-datatables_buttons-colvis">
						<div><?php _e( 'Colvis', 'tablepress' ); ?></div>
					</div><div class="drag-box">
						<input type="hidden" value="copy" id="option-datatables_buttons-copy">
						<div><?php _e( 'Copy', 'tablepress' ); ?></div>
					</div><div class="drag-box">
						<input type="hidden" value="csv" id="option-datatables_buttons-csv">
						<div><?php _e( 'CSV', 'tablepress' ); ?></div>
					</div><div class="drag-box">
						<input type="hidden" value="excel" id="option-datatables_buttons-excel">
						<div><?php _e( 'Excel', 'tablepress' ); ?></div>
					</div><div class="drag-box">
						<input type="hidden" value="pdf" id="option-datatables_buttons-pdf">
						<div><?php _e( 'PDF', 'tablepress' ); ?></div>
					</div><div class="drag-box">
						<input type="hidden" value="print" id="option-datatables_buttons-print">
						<div><?php _e( 'Print', 'tablepress' ); ?></div>
					</div></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Adds parameters for the DataTables Buttons feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_buttons'] = null;
		return $default_atts;
	}

	/**
	 * Passes the DataTables Buttons configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		$js_options['datatables_buttons'] = strtolower( $render_options['datatables_buttons'] );

		// Remove invalid button names from the list.
		$js_options['datatables_buttons'] = explode( ',', $js_options['datatables_buttons'] );
		$js_options['datatables_buttons'] = array_map( 'trim', $js_options['datatables_buttons'] );
		foreach ( $js_options['datatables_buttons'] as $idx => $button ) {
			if ( ! in_array( $button, array( 'copy', 'csv', 'excel', 'pdf', 'print', 'colvis' ), true ) ) {
				unset( $js_options['datatables_buttons'][ $idx ] );
			}
		}

		// Bail out early if no button is to be shown.
		if ( 0 === count( $js_options['datatables_buttons'] ) ) {
			return $js_options;
		}

		$js_url = plugins_url( 'modules/js/datatables.buttons.min.js', TABLEPRESS__FILE__ );
		wp_enqueue_script( 'tablepress-datatables-buttons', $js_url, array( 'tablepress-datatables' ), TablePress::version, true );

		// If any of the export buttons is shown, we need the JS files.
		foreach ( array( 'copy', 'csv', 'excel', 'pdf' ) as $button ) {
			if ( in_array( $button, $js_options['datatables_buttons'], true ) ) {
				$js_url = plugins_url( 'modules/js/datatables.buttons.html5.min.js', TABLEPRESS__FILE__ );
				wp_enqueue_script( 'tablepress-datatables-buttons-html5', $js_url, array( 'tablepress-datatables-buttons' ), TablePress::version, true );
				break;
			}
		}

		// Add special JS files for special buttons.
		if ( in_array( 'print', $js_options['datatables_buttons'], true ) ) {
			$js_url = plugins_url( 'modules/js/datatables.buttons.print.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-buttons-print', $js_url, array( 'tablepress-datatables-buttons' ), TablePress::version, true );
		}
		if ( in_array( 'excel', $js_options['datatables_buttons'], true ) ) {
			$js_url = plugins_url( 'modules/js/datatables.buttons.jszip.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-buttons-jsmin', $js_url, array( 'tablepress-datatables-buttons' ), TablePress::version, true );
		}
		if ( in_array( 'pdf', $js_options['datatables_buttons'], true ) ) {
			$js_url = plugins_url( 'modules/js/datatables.buttons.pdfmake.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-buttons-pdfmake', $js_url, array( 'tablepress-datatables-buttons' ), TablePress::version, true );
		}
		if ( in_array( 'colvis', $js_options['datatables_buttons'], true ) ) {
			$js_url = plugins_url( 'modules/js/datatables.buttons.colvis.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-buttons-colvis', $js_url, array( 'tablepress-datatables-buttons' ), TablePress::version, true );
		}

		return $js_options;
	}

	/**
	 * Evaluates JS parameters and converts them to DataTables parameters.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $parameters DataTables parameters.
	 * @param string               $table_id   Table ID.
	 * @param string               $html_id    HTML ID of the table.
	 * @param array<string, mixed> $js_options JS options for DataTables.
	 * @return array<string, mixed> Extended DataTables parameters.
	 */
	public static function set_datatables_parameters( array $parameters, string $table_id, string $html_id, array $js_options ): array {
		// Bail out early if no button is to be shown.
		if ( 0 === count( $js_options['datatables_buttons'] ) ) {
			return $parameters;
		}

		// Prepend "B" to the "dom" value, if one is already set, otherwise use the default.
		if ( isset( $parameters['dom'] ) ) {
			$parameters['dom'] = str_replace( ':"', ':"B', $parameters['dom'] );
		} else {
			$parameters['dom'] = '"dom":"Blfrtip"';
		}

		// Construct the DataTables Buttons config parameter.
		foreach ( $js_options['datatables_buttons'] as &$button ) {
			$button = "\"{$button}\"";
		}
		unset( $button );
		$parameters['buttons'] = '"buttons":[' . implode( ',', $js_options['datatables_buttons'] ) . ']';

		return $parameters;
	}

	/**
	 * Adds strings that the module uses on the frontend to the DataTables language array.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, string|mixed[]> $datatables_strings The language strings for DataTables.
	 * @param string                        $datatables_locale  Current locale/language for the DataTables JS library.
	 * @return array<string, string|mixed[]> Extended array of strings for DataTables.
	 */
	public static function add_datatables_language_strings( array $datatables_strings, string $datatables_locale ): array {
		if ( 'en_US' === $datatables_locale ) {
			return $datatables_strings;
		}

		TablePress_Modules_Loader::load_language_file();

		$new_strings = array(
			'buttons' => array(
				'collection'    => _x( 'Collection', 'Buttons module', 'tablepress' ),
				'colvis'        => _x( 'Column visibility', 'Buttons module', 'tablepress' ),
				'colvisRestore' => _x( 'Restore visibility', 'Buttons module', 'tablepress' ),
				'copy'          => _x( 'Copy', 'Buttons module', 'tablepress' ),
				'copyKeys'      => _x( 'Press <i>ctrl</i> or <i>⌘</i> + <i>C</i> to copy the table data<br>to your system clipboard.<br><br>To cancel, click this message or press escape.', 'Buttons module', 'tablepress' ),
				'copySuccess'   => array(
					'1' => _x( 'Copied one row to clipboard', 'Buttons module', 'tablepress' ),
					'_' => _x( 'Copied %d rows to clipboard', 'Buttons module', 'tablepress' ),
				),
				'copyTitle'     => _x( 'Copy to clipboard', 'Buttons module', 'tablepress' ),
				'csv'           => _x( 'CSV', 'Buttons module', 'tablepress' ),
				'excel'         => _x( 'Excel', 'Buttons module', 'tablepress' ),
				'pageLength'    => array(
					'-1' => _x( 'Show all rows', 'Buttons module', 'tablepress' ),
					'_'  => _x( 'Show %d rows', 'Buttons module', 'tablepress' ),
				),
				'pdf'           => _x( 'PDF', 'Buttons module', 'tablepress' ),
				'print'         => _x( 'Print', 'Buttons module', 'tablepress' ),
			),
		);
		// Merge existing strings into the new strings, so that existing translations are not lost.
		$datatables_strings = array_replace_recursive( $new_strings, $datatables_strings );

		return $datatables_strings;
	}

	/**
	 * Enqueues CSS files for the DataTables Buttons module.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_css_files(): void {
		/** This filter is documented in modules/controllers/datatables-alphabetsearch.php */
		if ( ! apply_filters( 'tablepress_module_enqueue_css_files', true, self::$module['slug'] ) ) {
			return;
		}

		$css_url = plugins_url( 'modules/css/build/datatables.buttons.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-buttons', $css_url, array( 'tablepress-default' ), TablePress::version );
	}

} // class TablePress_Module_DataTables_Buttons
