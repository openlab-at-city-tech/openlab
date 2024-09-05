/**
 * JavaScript code for the "Edit" screen integration of the Cell Highlighting feature.
 *
 * @package TablePress
 * @subpackage Cell Highlighting
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

add_filter( 'tablepress.optionsLoad', 'tp/cell-highlighting/load-option-value', option_name => {
	if ( 'highlight' !== option_name ) {
		return option_name;
	}

	if ( '' !== tp.table.options.highlight.trim() && (
		tp.table.options.highlight_full_cell_match ||
		tp.table.options.highlight_case_sensitive ||
		'' !== tp.table.options.highlight_columns.trim()
		) ) {
		$( '#tablepress-highlight-advanced-settings' ).open = true;
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/cell-highlighting/handle-options-check-dependencies', () => {
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	$( '#notice-cell-highlighting-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
	$( '#option-highlight' ).disabled = datatables_serverside_processing_enabled;
	const highlight_parameters_disabled = ( '' === tp.table.options.highlight.trim() ) || datatables_serverside_processing_enabled;
	$( '#option-highlight_full_cell_match' ).disabled = highlight_parameters_disabled;
	$( '#option-highlight_case_sensitive' ).disabled = highlight_parameters_disabled;
	$( '#option-highlight_columns' ).disabled = highlight_parameters_disabled;
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/cell-highlighting/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-cell-highlighting' );
	return options_meta_boxes;
} );
