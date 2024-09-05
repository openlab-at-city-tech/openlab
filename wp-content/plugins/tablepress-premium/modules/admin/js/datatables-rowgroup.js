/**
 * JavaScript code for the "Edit" screen integration of the DataTables RowGroup feature.
 *
 * @package TablePress
 * @subpackage DataTables RowGroup
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

add_filter( 'tablepress.optionsLoad', 'tp/datatables-rowgroup/load-option-value', option_name => {
	if ( 'datatables_rowgroup' !== option_name ) {
		return option_name;
	}

	if ( tp.table.options.datatables_rowgroup && tp.table.options.datatables_rowgroup_datasrc !== '1' ) { // 1 is the default Shortcode parameter value.
		$( '#tablepress-datatables_rowgroup-advanced-settings' ).open = true;
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-rowgroup/handle-options-check-dependencies', () => {
	const rowgroup_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head );
	$( '#option-datatables_rowgroup' ).disabled = ! rowgroup_enabled;
	$( '#option-datatables_rowgroup_datasrc' ).disabled = ! rowgroup_enabled || ! tp.table.options.datatables_rowgroup;
	$( '#notice-datatables-rowgroup-requirements' ).style.display = rowgroup_enabled ? 'none' : 'block';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-rowgroup/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-rowgroup' );
	return options_meta_boxes;
} );

add_filter( 'tablepress.optionsValidateFields', 'tp/datatables-rowgroup/validate-fields', form_valid => {
	// The rowgroup datasrc field must not be empty.
	if ( tp.table.options.datatables_rowgroup && '' === tp.table.options.datatables_rowgroup_datasrc.trim() ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Row Grouping Column', 'tablepress' ) ) );
		const $field = $( '#option-datatables_rowgroup_datasrc' );
		$field.closest( 'details' ).open = true;
		$field.focus();
		$field.select();
		form_valid = false;
	}

	return form_valid;
} );
