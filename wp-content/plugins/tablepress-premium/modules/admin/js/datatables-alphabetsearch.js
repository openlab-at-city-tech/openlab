/**
 * JavaScript code for the "Edit" screen integration of the DataTables AlphabetSearch feature.
 *
 * @package TablePress
 * @subpackage DataTables AlphabetSearch
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

add_filter( 'tablepress.optionsLoad', 'tp/datatables-alphabetsearch/load-option-value', option_name => {
	if ( 'datatables_alphabetsearch' !== option_name ) {
		return option_name;
	}

	if ( tp.table.options.datatables_alphabetsearch && (
		tp.table.options.datatables_alphabetsearch_column !== '1' || // 1 is the default Shortcode parameter value.
		tp.table.options.datatables_alphabetsearch_alphabet !== 'latin' ||
		tp.table.options.datatables_alphabetsearch_numbers ||
		! tp.table.options.datatables_alphabetsearch_letters ||
		tp.table.options.datatables_alphabetsearch_case_sensitive
		) ) {
		$( '#tablepress-datatables_alphabetsearch-advanced-settings' ).open = true;
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-alphabetsearch/handle-options-check-dependencies', () => {
	const alphabetsearch_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head && tp.table.options.datatables_filter );
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	$( '#option-datatables_alphabetsearch' ).disabled = ! alphabetsearch_enabled || datatables_serverside_processing_enabled;
	$( '#notice-datatables-alphabetsearch-requirements' ).style.display = alphabetsearch_enabled ? 'none' : 'block';
	$( '#option-datatables_alphabetsearch_column' ).disabled = ! alphabetsearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_alphabetsearch;
	$( '#option-datatables_alphabetsearch_alphabet-latin' ).disabled = ! alphabetsearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_alphabetsearch;
	$( '#option-datatables_alphabetsearch_alphabet-greek' ).disabled = ! alphabetsearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_alphabetsearch;
	$( '#option-datatables_alphabetsearch_numbers' ).disabled = ! alphabetsearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_alphabetsearch;
	$( '#option-datatables_alphabetsearch_letters' ).disabled = ! alphabetsearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_alphabetsearch;
	$( '#option-datatables_alphabetsearch_case_sensitive' ).disabled = ! alphabetsearch_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_alphabetsearch || ! tp.table.options.datatables_alphabetsearch_letters;
	$( '#notice-datatables-alphabetsearch-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-alphabetsearch/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-alphabetsearch' );
	return options_meta_boxes;
} );

add_filter( 'tablepress.optionsValidateFields', 'tp/datatables-alphabetsearch/validate-fields', form_valid => {
	// The "AlphabetSearch column" field must only contain numbers or letters and must not be empty.
	if ( tp.table.options.datatables_alphabetsearch && ( '' === tp.table.options.datatables_alphabetsearch_column.trim() || ( /[^A-Z0-9]/ ).test( tp.table.options.datatables_alphabetsearch_column ) ) ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'AlphabetSearch Column', 'tablepress' ) ) );
		const $field = $( '#option-datatables_alphabetsearch_column' );
		$field.closest( 'details' ).open = true;
		$field.focus();
		$field.select();
		form_valid = false;
	}

	return form_valid;
} );
