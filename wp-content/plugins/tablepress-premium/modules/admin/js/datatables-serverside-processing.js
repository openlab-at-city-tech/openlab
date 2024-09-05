/**
 * JavaScript code for the "Edit" screen integration of the DataTables Server-side Processing feature.
 *
 * @package TablePress
 * @subpackage DataTables Server-side Processing
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

add_filter( 'tablepress.optionsLoad', 'tp/datatables-serverside-processing/load-option-value', option_name => {
	if ( 'datatables_serverside_processing' !== option_name ) {
		return option_name;
	}

	if ( tp.table.options.datatables_serverside_processing && (
		tp.table.options.datatables_serverside_processing_cached_pages > 0 ||
		tp.table.options.datatables_serverside_processing_periodic_refresh > 0
		) ) {
		$( '#tablepress-datatables_serverside_processing-advanced-settings' ).open = true;
	}

	return option_name; // Return the option name, to use this function as an action hook.
} );

add_action( 'tablepress.optionsChange', 'tp/datatables-serverside-processing/handle-options-change', ( option_name /*, property, event */ ) => {
	// Prevent that "datatables_serverside_processing" and "datatables_advanced_loading" are enabled at the same time (e.g. when one of the modules is turned off).
	if ( 'datatables_serverside_processing' === option_name && tp.table.options.datatables_serverside_processing ) {
		const incompatible_features = [
			{
				name: 'datatables_advanced_loading',
				value: false,
			},
			{
				name: 'datatables_alphabetsearch',
				value: false,
			},
			{
				name: 'datatables_column_filter',
				value: '',
			},
			{
				name: 'datatables_columnfilterwidgets',
				value: false,
			},
			{
				name: 'datatables_fuzzysearch',
				value: false,
			},
			{
				name: 'datatables_searchbuilder',
				value: false,
			},
			{
				name: 'datatables_searchpanes',
				value: false,
			},
			{
				name: 'highlight',
				value: '',
			},
			{
				name: 'row_highlight',
				value: '',
			},
		];
		incompatible_features.forEach( feature => {
			tp.table.options[ feature.name ] = feature.value;
			const $checkbox = $( `#option-${ feature.name }` );
			if ( $checkbox ) {
				$checkbox.checked = false;
			}
		} );
	}
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-serverside-processing/handle-options-check-dependencies', () => {
	const serverside_processing_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head );
	const datatables_advanced_loading_enabled = tp.table.options.datatables_advanced_loading && $( '#option-datatables_advanced_loading' ); // The checkbox has to exist, as the module is turned off otherwise.
	$( '#option-datatables_serverside_processing' ).disabled = ! serverside_processing_enabled || datatables_advanced_loading_enabled;
	$( '#option-datatables_serverside_processing_cached_pages' ).disabled = ! serverside_processing_enabled || datatables_advanced_loading_enabled || ! tp.table.options.datatables_serverside_processing;
	$( '#option-datatables_serverside_processing_periodic_refresh' ).disabled = ! serverside_processing_enabled || datatables_advanced_loading_enabled || ! tp.table.options.datatables_serverside_processing;
	$( '#notice-datatables-serverside-processing-requirements' ).style.display = serverside_processing_enabled ? 'none' : 'block';
	$( '#notice-datatables-serverside-processing-conflict-datatables-advanced-loading' ).style.display = datatables_advanced_loading_enabled ? 'block' : 'none';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-serverside-processing/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-serverside-processing' );
	return options_meta_boxes;
} );

add_filter( 'tablepress.optionsValidateFields', 'tp/datatables-serverside-processing/validate-fields', form_valid => {
	// The cached pages entry value must be a number from 1 to 10.
	if ( tp.table.options.datatables_serverside_processing && ( isNaN( tp.table.options.datatables_serverside_processing_cached_pages ) || tp.table.options.datatables_serverside_processing_cached_pages < 0 || tp.table.options.datatables_serverside_processing_cached_pages > 10 ) ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Server-side Processing Cached Pages', 'tablepress' ) ) );
		const $field = $( '#option-datatables_serverside_processing_cached_pages' );
		$field.closest( 'details' ).open = true;
		$field.focus();
		$field.select();
		form_valid = false;
	}

	// The refresh interval entry value must be a non-negative number.
	if ( tp.table.options.datatables_serverside_processing && ( isNaN( tp.table.options.datatables_serverside_processing_periodic_refresh ) || tp.table.options.datatables_serverside_processing_periodic_refresh < 0 ) ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Server-side Processing Periodic Refresh', 'tablepress' ) ) );
		const $field = $( '#option-datatables_serverside_processing_periodic_refresh' );
		$field.closest( 'details' ).open = true;
		$field.focus();
		$field.select();
		form_valid = false;
	}

	return form_valid;
} );
