/**
 * JavaScript code for the "Edit" screen integration of the DataTables Column Filter feature.
 *
 * @package TablePress
 * @subpackage DataTables Column Filter
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/* globals tp */

/**
 * WordPress dependencies.
 */
import { __, sprintf } from '@wordpress/i18n';
import { addAction as add_action, addFilter as add_filter } from '@wordpress/hooks';

/**
 * Internal dependencies.
 */
import { $ } from '../../../admin/js/common/functions';

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-column-filter/handle-options-check-dependencies', () => {
	const column_filter_enabled = ( tp.table.options.table_head && tp.table.options.use_datatables && tp.table.options.datatables_filter );
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	const $column_filter_notice = $( '#notice-datatables-column-filter-requirements' );
	$column_filter_notice.style.display = column_filter_enabled ? 'none' : 'block';
	document.querySelectorAll( '#option-datatables_column_filter-select,#option-datatables_column_filter-input' ).forEach( ( $field ) => {
		$field.disabled = ! column_filter_enabled || datatables_serverside_processing_enabled;
		$field.nextElementSibling.title = column_filter_enabled ? '' : $column_filter_notice.textContent;
	} );

	const column_filter_active = ( column_filter_enabled && '' !== tp.table.options.datatables_column_filter );
	$( '#option-datatables_column_filter_position' ).disabled = ! column_filter_active || datatables_serverside_processing_enabled;

	const column_filter_position_table_foot_available = ( tp.table.options.table_foot );
	const $column_filter_position_table_foot = $( '#option-datatables_column_filter_position-table_foot' );
	$column_filter_position_table_foot.disabled = ! column_filter_position_table_foot_available || datatables_serverside_processing_enabled;
	const $column_filter_position_notice = $( '#notice-datatables-column-filter-position-requirements' );
	$column_filter_position_table_foot.title = column_filter_position_table_foot_available ? '' : $column_filter_position_notice.textContent;
	$column_filter_position_notice.style.display = column_filter_position_table_foot_available ? 'none' : 'inline';

	$( '#notice-datatables-column-filter-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-column-filter/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-column-filter' );
	return options_meta_boxes;
} );

add_filter( 'tablepress.optionsValidateFields', 'tp/responsive-tables/validate-fields', form_valid => {
	// The Individual Column Filtering feature can not be used if DataTables is not used, if there's no Head Row, of if Filtering is turned off.
	if ( '' !== tp.table.options.datatables_column_filter && ! ( tp.table.options.use_datatables && tp.table.options.table_head && tp.table.options.datatables_filter ) ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Form element', 'tablepress' ) ) );
		const $field = $( '#option-datatables_column_filter-' );
		$field.focus();
		$field.select();
		form_valid = false;
	}

	// The "table_foot" position can not be selected if the Table Foot checkbox is unchecked.
	if ( 'table_foot' === tp.table.options.datatables_column_filter_position && '' !== tp.table.options.datatables_column_filter && ! tp.table.options.table_foot ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Position', 'tablepress' ) ) );
		const $field = $( '#option-datatables_column_filter_position' );
		$field.focus();
		$field.select();
		form_valid = false;
	}

	return form_valid;
} );
