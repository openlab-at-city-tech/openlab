/**
 * JavaScript code for the "Edit" screen integration of the Row Highlighting feature.
 *
 * @package TablePress
 * @subpackage Row Highlighting
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

add_filter( 'tablepress.optionsLoad', 'tp/row-highlighting/load-option-value', option_name => {
	if ( 'row_highlight' !== option_name ) {
		return option_name;
	}

	if ( '' !== tp.table.options.row_highlight.trim() && (
		! tp.table.options.row_highlight_full_cell_match ||
		tp.table.options.row_highlight_case_sensitive ||
		'' !== tp.table.options.row_highlight_columns.trim() ||
		'' !== tp.table.options.row_highlight_rows.trim()
		) ) {
		$( '#tablepress-row_highlight-advanced-settings' ).open = true;
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/row-highlighting/handle-options-check-dependencies', () => {
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	$( '#notice-row-highlighting-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
	$( '#option-row_highlight' ).disabled = datatables_serverside_processing_enabled;
	const highlight_parameters_disabled = ( '' === tp.table.options.row_highlight.trim() ) || datatables_serverside_processing_enabled;
	$( '#option-row_highlight_full_cell_match' ).disabled = highlight_parameters_disabled;
	$( '#option-row_highlight_case_sensitive' ).disabled = highlight_parameters_disabled;
	$( '#option-row_highlight_columns' ).disabled = highlight_parameters_disabled;
	$( '#option-row_highlight_rows' ).disabled = highlight_parameters_disabled;
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/row-highlighting/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-row-highlighting' );
	return options_meta_boxes;
} );
