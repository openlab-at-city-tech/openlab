/**
 * JavaScript code for the "Edit" screen integration of the DataTables SearchBuilder feature.
 *
 * @package TablePress
 * @subpackage DataTables SearchBuilder
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

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-searchbuilder/handle-options-check-dependencies', () => {
	const searchbuilder_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head && tp.table.options.datatables_filter );
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	$( '#option-datatables_searchbuilder' ).disabled = ! searchbuilder_enabled || datatables_serverside_processing_enabled;
	$( '#notice-datatables-searchbuilder-requirements' ).style.display = searchbuilder_enabled ? 'none' : 'block';
	$( '#notice-datatables-searchbuilder-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-searchbuilder/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-searchbuilder' );
	return options_meta_boxes;
} );
