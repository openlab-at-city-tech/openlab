/**
 * JavaScript code for the "Edit" screen integration of the DataTables SearchHighlight feature.
 *
 * @package TablePress
 * @subpackage DataTables SearchHighlight
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

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-searchhighlight/handle-options-check-dependencies', () => {
	const searchhighlight_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head && tp.table.options.datatables_filter );
	$( '#option-datatables_searchhighlight' ).disabled = ! searchhighlight_enabled;
	$( '#notice-datatables-searchhighlight-requirements' ).style.display = searchhighlight_enabled ? 'none' : 'block';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-searchhighlight/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-searchhighlight' );
	return options_meta_boxes;
} );
