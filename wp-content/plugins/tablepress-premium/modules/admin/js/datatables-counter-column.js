/**
 * JavaScript code for the "Edit" screen integration of the DataTables Counter Column feature.
 *
 * @package TablePress
 * @subpackage DataTables Counter Column
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

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-counter-column/handle-options-check-dependencies', () => {
	const counter_column_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head );
	$( '#option-datatables_counter_column' ).disabled = ! counter_column_enabled;
	$( '#notice-datatables-counter-column-requirements' ).style.display = counter_column_enabled ? 'none' : 'block';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-counter-column/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-counter-column' );
	return options_meta_boxes;
} );
