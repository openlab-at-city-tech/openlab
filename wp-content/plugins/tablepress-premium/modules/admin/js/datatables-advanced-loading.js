/**
 * JavaScript code for the "Edit" screen integration of the DataTables Advanced Loading feature.
 *
 * @package TablePress
 * @subpackage DataTables Advanced Loading
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

add_filter( 'tablepress.optionsLoad', 'tp/datatables-advanced-loading/load-option-value', option_name => {
	if ( 'datatables_advanced_loading' !== option_name ) {
		return option_name;
	}

	if ( tp.table.options.datatables_advanced_loading && tp.table.options.datatables_advanced_loading_html_rows !== 10 ) { // 10 is the default Shortcode parameter value.
		$( '#tablepress-datatables_advanced_loading-advanced-settings' ).open = true;
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsChange', 'tp/datatables-advanced-loading/handle-options-change', ( option_name /*, property, event */ ) => {
	// Prevent that "datatables_advanced_loading" and "datatables_serverside_processing" are enabled at the same time (e.g. when one of the modules is turned off).
	if ( 'datatables_advanced_loading' === option_name && tp.table.options.datatables_advanced_loading ) {
		tp.table.options.datatables_serverside_processing = false;
		const $checkbox = $( '#option-datatables_serverside_processing' );
		if ( $checkbox ) {
			$checkbox.checked = false;
		}
	}
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-advanced-loading/handle-options-check-dependencies', () => {
	const advanced_loading_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head );
	const datatables_serverside_processing_enabled = tp.table.options.datatables_serverside_processing && $( '#option-datatables_serverside_processing' ); // The checkbox has to exist, as the module is turned off otherwise.
	$( '#option-datatables_advanced_loading' ).disabled = ! advanced_loading_enabled || datatables_serverside_processing_enabled;
	$( '#option-datatables_advanced_loading_html_rows' ).disabled = ! advanced_loading_enabled || datatables_serverside_processing_enabled || ! tp.table.options.datatables_advanced_loading;
	$( '#notice-datatables-advanced-loading-requirements' ).style.display = advanced_loading_enabled ? 'none' : 'block';
	$( '#notice-datatables-advanced-loading-conflict-datatables-serverside-processing' ).style.display = datatables_serverside_processing_enabled ? 'block' : 'none';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-advanced-loading/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-advanced-loading' );
	return options_meta_boxes;
} );

add_filter( 'tablepress.optionsValidateFields', 'tp/datatables-advanced-loading/validate-fields', form_valid => {
	// The pagination entries value must be a positive number.
	if ( tp.table.options.datatables_advanced_loading && ( isNaN( tp.table.options.datatables_advanced_loading_html_rows ) || tp.table.options.datatables_advanced_loading_html_rows < 1 || tp.table.options.datatables_advanced_loading_html_rows > 9999 ) ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Advanced Loading HTML rows', 'tablepress' ) ) );
		const $field = $( '#option-datatables_advanced_loading_html_rows' );
		$field.closest( 'details' ).open = true;
		$field.focus();
		$field.select();
		form_valid = false;
	}

	return form_valid;
} );
