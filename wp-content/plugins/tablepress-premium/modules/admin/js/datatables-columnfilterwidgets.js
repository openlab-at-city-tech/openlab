/**
 * JavaScript code for the "Edit" screen integration of the DataTables ColumnFilterWidgets feature.
 *
 * @package TablePress
 * @subpackage DataTables ColumnFilterWidgets
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/* globals tp */

/**
 * WordPress dependencies.
 */
import { addAction as add_action, addFilter as add_filter } from '@wordpress/hooks';

/**
 * Internal dependencies.
 */
import { $ } from '../../../admin/js/common/functions';

add_filter( 'tablepress.optionsLoad', 'tp/datatables-columnfilterwidgets/load-option-value', option_name => {
	if ( 'datatables_columnfilterwidgets' !== option_name ) {
		return option_name;
	}

	if ( tp.table.options.datatables_columnfilterwidgets && (
		'' !== tp.table.options.datatables_columnfilterwidgets_columns.trim() ||
		'' !== tp.table.options.datatables_columnfilterwidgets_exclude_columns.trim() ||
		'' !== tp.table.options.datatables_columnfilterwidgets_separator.trim() ||
		'' !== tp.table.options.datatables_columnfilterwidgets_max_selections.trim() ||
		tp.table.options.datatables_columnfilterwidgets_group_terms
		) ) {
		$( '#tablepress-datatables_columnfilterwidgets-advanced-settings' ).open = true;
	}

	// Hide the "Excluded Columns" field if it is empty, as a soft-deprecation in favor of the "Columns" field.
	if ( '' === tp.table.options.datatables_columnfilterwidgets_exclude_columns.trim() ) {
		$( '#option-datatables_columnfilterwidgets_exclude_columns' ).parentNode.parentNode.style.display = 'none';
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-columnfilterwidgets/handle-options-check-dependencies', () => {
	const columnfilterwidgets_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head && tp.table.options.datatables_filter );
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	const columnfilterwidgets_parameters_disabled = ! columnfilterwidgets_enabled || ! tp.table.options.datatables_columnfilterwidgets || datatables_serverside_processing_enabled;
	$( '#option-datatables_columnfilterwidgets' ).disabled = ! columnfilterwidgets_enabled || datatables_serverside_processing_enabled;
	$( '#option-datatables_columnfilterwidgets_columns' ).disabled = columnfilterwidgets_parameters_disabled;
	$( '#option-datatables_columnfilterwidgets_exclude_columns' ).disabled = columnfilterwidgets_parameters_disabled;
	$( '#option-datatables_columnfilterwidgets_separator' ).disabled = columnfilterwidgets_parameters_disabled;
	$( '#option-datatables_columnfilterwidgets_max_selections' ).disabled = columnfilterwidgets_parameters_disabled;
	$( '#option-datatables_columnfilterwidgets_group_terms' ).disabled = columnfilterwidgets_parameters_disabled;
	$( '#notice-datatables-columnfilterwidgets-requirements' ).style.display = columnfilterwidgets_enabled ? 'none' : 'block';
	$( '#notice-datatables-columnfilterwidgets-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-columnfilterwidgets/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-columnfilterwidgets' );
	return options_meta_boxes;
} );
