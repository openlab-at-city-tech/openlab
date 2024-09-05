/**
 * JavaScript code for the "Edit" screen integration of the DataTables SearchPanes feature.
 *
 * @package TablePress
 * @subpackage DataTables SearchPanes
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

add_filter( 'tablepress.optionsLoad', 'tp/datatables-searchpanes/load-option-value', option_name => {
	if ( 'datatables_searchpanes' !== option_name ) {
		return option_name;
	}

	if ( tp.table.options.datatables_searchpanes && (
		'' !== tp.table.options.datatables_searchpanes_columns.trim()
		) ) {
		$( '#tablepress-datatables_searchpanes-advanced-settings' ).open = true;
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-searchpanes/handle-options-check-dependencies', () => {
	const searchpanes_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head && tp.table.options.datatables_filter );
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	$( '#option-datatables_searchpanes' ).disabled = ! searchpanes_enabled || datatables_serverside_processing_enabled;
	$( '#option-datatables_searchpanes_columns' ).disabled = ! searchpanes_enabled || ! tp.table.options.datatables_searchpanes || datatables_serverside_processing_enabled;
	$( '#notice-datatables-searchpanes-requirements' ).style.display = searchpanes_enabled ? 'none' : 'block';
	$( '#notice-datatables-searchpanes-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-searchpanes/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-searchpanes' );
	return options_meta_boxes;
} );
