/**
 * JavaScript code for the "Edit" screen integration of the DataTables FuzzySearch feature.
 *
 * @package TablePress
 * @subpackage DataTables FuzzySearch
 * @author Tobias Bäthge
 * @since 2.4.0
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

add_filter( 'tablepress.optionsLoad', 'tp/datatables-fuzzysearch/load-option-value', option_name => {
	if ( 'datatables_fuzzysearch' !== option_name ) {
		return option_name;
	}

	// Expand the "Advanced Settings" section if a table option is different from the default.
	if ( tp.table.options.datatables_fuzzysearch && (
		0.5 !== tp.table.options.datatables_fuzzysearch_threshold ||
		! tp.table.options.datatables_fuzzysearch_togglesmart ||
		'' !== tp.table.options.datatables_fuzzysearch_rankcolumn
		) ) {
		$( '#tablepress-datatables_fuzzysearch-advanced-settings' ).open = true;
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-fuzzysearch/handle-options-check-dependencies', () => {
	const fuzzysearch_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head && tp.table.options.datatables_filter );
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	$( '#option-datatables_fuzzysearch' ).disabled = ! fuzzysearch_enabled || datatables_serverside_processing_enabled;
	$( '#option-datatables_fuzzysearch_threshold' ).disabled = ! fuzzysearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_fuzzysearch;
	$( '#option-datatables_fuzzysearch_togglesmart' ).disabled = ! fuzzysearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_fuzzysearch;
	$( '#option-datatables_fuzzysearch_rankcolumn' ).disabled = ! fuzzysearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_fuzzysearch;
	$( '#notice-datatables-fuzzysearch-requirements' ).style.display = fuzzysearch_enabled ? 'none' : 'block';
	$( '#notice-datatables-fuzzysearch-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-fuzzysearch/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-fuzzysearch' );
	return options_meta_boxes;
} );
