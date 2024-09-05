<?php
/**
 * TablePress DataTables Column Filter.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables Column Filter feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_Column_Filter {
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
		add_filter( 'tablepress_table_render_options', array( __CLASS__, 'process_table_render_options' ), 10, 2 );
		add_filter( 'tablepress_table_js_options', array( __CLASS__, 'pass_render_options_to_js_options' ), 10, 3 );
		add_filter( 'tablepress_datatables_parameters', array( __CLASS__, 'set_datatables_parameters' ), 10, 4 );
	}

	/**
	 * Adds options related to DataTables Column Filter to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_column_filter'] = '';
		$table['options']['datatables_column_filter_position'] = 'table_head';
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the "DataTables Column Filter" feature.
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
			add_meta_box( 'tablepress_edit-datatables-column-filter', __( 'Individual Column Filtering', 'tablepress' ), array( __CLASS__, 'postbox_datatables_column_filter' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_style( 'datatables-column-filter', array( 'tablepress-modules-common' ) );
			TablePress_Modules_Helper::enqueue_script( 'datatables-column-filter' );

			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds DataTables Column Filter script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-datatables-column-filter';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "DataTables Column Filter" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_datatables_column_filter( array $data, array $box ): void {
		$help_box_content = '';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-datatables-column-filter-conflict-datatables-serverside-processing"><em><?php printf( __( 'This feature is only available when the “%1$s” feature is turned off.', 'tablepress' ), __( 'Server-side Processing', 'tablepress' ) ); ?></em></p>
		<table class="tablepress-postbox-table fixed">
			<tr>
				<th class="column-1 top-align" scope="row"><?php _e( 'Form Element', 'tablepress' ); ?>:</th>
				<td class="column-2">
					<div>
						<p class="description"><?php _e( 'Choose the desired form element for the individual column filters:', 'tablepress' ); ?></p>
						<p id="notice-datatables-column-filter-requirements"><em><?php printf( __( 'This feature is only available when the “%1$s”, “%2$s”, and “%3$s” checkboxes in the “%4$s” and “%5$s” sections are checked.', 'tablepress' ), __( 'Table Head Row', 'tablepress' ), __( 'Enable Visitor Features', 'tablepress' ), __( 'Search/Filtering', 'tablepress' ), __( 'Table Options', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
					</div>
					<div class="input-field-box">
						<input type="radio" name="datatables_column_filter" value="" id="option-datatables_column_filter-" class="control-input">
						<label for="option-datatables_column_filter-">
							<span class="box-title"><?php _e( 'Off', 'tablepress' ); ?></span>
							<p class="description"><?php _e( 'No individual column filtering. ', 'tablepress' ); ?></p>
						</label>
					</div><div class="input-field-box">
						<input type="radio" name="datatables_column_filter" value="input" id="option-datatables_column_filter-input" class="control-input">
						<label for="option-datatables_column_filter-input">
							<span class="box-title"><?php _e( 'Text field', 'tablepress' ); ?></span>
							<p class="description"><?php printf( __( 'Use %s fields.', 'tablepress' ), '<input type="text" class="mock-field" tabindex="-1" value="' . __( 'text input', 'tablepress' ) . '">' ); ?></p>
						</label>
					</div><div class="input-field-box">
						<input type="radio" name="datatables_column_filter" value="select" id="option-datatables_column_filter-select" class="control-input">
						<label for="option-datatables_column_filter-select">
							<span class="box-title"><?php _e( 'Drop-down', 'tablepress' ); ?></span>
							<p class="description"><?php printf( __( 'Use %s fields.', 'tablepress' ), '<select class="mock-field" tabindex="-1"><option>' . __( 'drop-down', 'tablepress' ) . '</option></select>' ); ?></p>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<th class="column-1 top-align" scope="row"><?php _e( 'Position', 'tablepress' ); ?>:</th>
				<td class="column-2">
					<label for="option-datatables_column_filter_position">
						<p class="description"><?php _e( 'Choose the desired position for the individual column filters.', 'tablepress' ); ?> <?php _e( 'The texts in the chosen cells will be used as placeholders for the search fields.', 'tablepress' ); ?></p>
						<select id="option-datatables_column_filter_position" name="datatables_column_filter_position">
							<option value="table_head"><?php _e( 'Table Head Row', 'tablepress' ); ?></option>
							<option value="table_foot" id="option-datatables_column_filter_position-table_foot"><?php _e( 'Table Foot Row', 'tablepress' ); ?></option>
						</select>
					</label>
					<p id="notice-datatables-column-filter-position-requirements" class="description"><em><?php printf( __( 'The “%1$s” option is only available when the “%2$s” checkbox in the “%3$s” section is checked.', 'tablepress' ), __( 'Table Foot Row', 'tablepress' ), __( 'Table Foot Row', 'tablepress' ), __( 'Table Options', 'tablepress' ) ); ?></em></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Adds parameters for the DataTables Column Filter feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_columnfilter'] = ''; // This parameter is deprecated and only kept for backward compatibility.
		$default_atts['datatables_column_filter'] = null;
		$default_atts['datatables_column_filter_position'] = null;
		return $default_atts;
	}

	/**
	 * Sets required render options based on the chosen settings for the Column Filter module.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $render_options Render Options.
	 * @param array<string, mixed> $table          Table.
	 * @return array<string, mixed> Modified Render Options.
	 */
	public static function process_table_render_options( array $render_options, array $table ): array {
		// If the deprecated datatables_columnfilter parameter is used, while the datatables_column_filter parameter is not, use that.
		if ( '' === $render_options['datatables_column_filter'] && '' !== $render_options['datatables_columnfilter'] ) {
			$render_options['datatables_column_filter'] = $render_options['datatables_columnfilter'];
		}

		if ( '' !== $render_options['datatables_column_filter'] && ! $render_options['datatables_filter'] ) {
			$render_options['datatables_column_filter'] = '';
		}

		if ( 'table_foot' === $render_options['datatables_column_filter_position'] && ! $render_options['table_foot'] ) {
			$render_options['datatables_column_filter_position'] = 'table_head';
		}

		return $render_options;
	}

	/**
	 * Passes the DataTables Column Filter configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		$js_options['datatables_column_filter'] = $render_options['datatables_column_filter'];
		$js_options['datatables_column_filter_position'] = $render_options['datatables_column_filter_position'];

		// Individual Column Filters is not supported with Server-side Processing.
		if ( isset( $render_options['datatables_serverside_processing'] ) && $render_options['datatables_serverside_processing'] ) {
			$js_options['datatables_column_filter'] = '';
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
		if ( '' === $js_options['datatables_column_filter'] ) {
			return $parameters;
		}

		if ( 'table_head' === $js_options['datatables_column_filter_position'] ) {
			$target_element = 'header';
		} else {
			$target_element = 'footer';
		}

		switch ( true ) {
			case ( true === $js_options['datatables_column_filter'] ): // For backward compatibility.
			case ( 'input' === $js_options['datatables_column_filter'] ):
				$init_complete = <<<JS
function () {
	this.api()
		.columns()
		.every( function () {
			const column = this;
			const target_element = column.{$target_element}();
			const input = $( '<input type="text">')
				.attr( 'placeholder', target_element.textContent )
				.appendTo( $( target_element ).empty() )
				.on( 'click', function ( event ) {
					event.stopPropagation();
				} )
				.on( 'keyup change clear', function () {
					if ( column.search() !== this.value ) {
						column.search( this.value ).draw();
					}
				} );
		} );
}
JS;
				break;

			case ( 'select' === $js_options['datatables_column_filter'] ):
				$init_complete = <<<JS
function () {
	this.api()
		.columns()
		.every( function () {
			const column = this;
			const target_element = column.{$target_element}();
			const select = $( '<select></select>' );

			$( '<option value=""></option>' )
				.text( target_element.textContent )
				.appendTo( select );

			column
				.data()
				.unique()
				.sort()
				.each( function ( value ) {
					const textarea = document.createElement( 'textarea' );
					textarea.innerHTML = value;
					$( '<option></option>' )
						.val( textarea.value )
						.text( textarea.value )
						.appendTo( select );
				} );

			select
				.appendTo( $( target_element ).empty() )
				.on( 'click', function ( event ) {
					event.stopPropagation();
				} )
				.on( 'change', function () {
					const value = $.fn.dataTable.util.escapeRegex( this.value );
					column.search( value ? '^' + value + '$' : '', true, false ).draw();
				} );
		} );
}
JS;
				break;

			default:
				// No valid value was passed, so bail.
				return $parameters;
				// break; // unreachable.
		}

		$parameters['initComplete'] = "\"initComplete\": {$init_complete}";
		return $parameters;
	}

} // class TablePress_Module_DataTables_Column_Filter
