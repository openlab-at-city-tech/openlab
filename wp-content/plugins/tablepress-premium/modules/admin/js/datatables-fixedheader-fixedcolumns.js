/**
 * JavaScript code for the "Edit" screen integration of the DataTables FixedHeader and FixedColumns feature.
 *
 * @package TablePress
 * @subpackage DataTables FixedHeader and FixedColumns
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

/**
 * Sets the "datatables_fixedheader" and "datatables_fixedcolumns" table options according to the chosen checkboxes.
 */
$( '#tablepress_edit-datatables-fixedheader-fixedcolumns' ).addEventListener( 'change', ( event ) => {
	if ( ! event.target || ! ( event.target instanceof HTMLInputElement ) || 'checkbox' !== event.target.type || ! event.target.classList.contains( 'control-input' ) ) {
		return;
	}

	const datatables_fixedheader_fixedcolumns_wrapper = event.target.closest( '.input-field-box-wrapper' );
	const datatables_fixedheader_fixedcolumns_checkboxes = datatables_fixedheader_fixedcolumns_wrapper.querySelectorAll( ':scope input:checked' );
	const option_name = datatables_fixedheader_fixedcolumns_wrapper.dataset.optionName;
	const datatables_fixedheader_fixedcolumns = [ ...datatables_fixedheader_fixedcolumns_checkboxes ].map( ( field ) => field.value );
	tp.table.options[ option_name ] = datatables_fixedheader_fixedcolumns.join( ',' );

	// Set FixedColumns on the left value according to the checkbox, as that internally supersedes the checkbox value.
	if ( tp.table.options.datatables_fixedcolumns.includes( 'left' ) && 0 === tp.table.options.datatables_fixedcolumns_left_columns ) {
		tp.table.options.datatables_fixedcolumns_left_columns = 1;
	} else if ( ! tp.table.options.datatables_fixedcolumns.includes( 'left' ) && 0 !== tp.table.options.datatables_fixedcolumns_left_columns ) {
		tp.table.options.datatables_fixedcolumns_left_columns = 0;
	}
	$( '#option-datatables_fixedcolumns_left_columns' ).value = tp.table.options.datatables_fixedcolumns_left_columns;

	// Set FixedColumns on the right value according to the checkbox, as that internally supersedes the checkbox value.
	if ( tp.table.options.datatables_fixedcolumns.includes( 'right' ) && 0 === tp.table.options.datatables_fixedcolumns_right_columns ) {
		tp.table.options.datatables_fixedcolumns_right_columns = 1;
	} else if ( ! tp.table.options.datatables_fixedcolumns.includes( 'right' ) && 0 !== tp.table.options.datatables_fixedcolumns_right_columns ) {
		tp.table.options.datatables_fixedcolumns_right_columns = 0;
	}
	$( '#option-datatables_fixedcolumns_right_columns' ).value = tp.table.options.datatables_fixedcolumns_right_columns;

	tp.helpers.options.check_dependencies();
	tp.helpers.unsaved_changes.set();
} );

add_filter( 'tablepress.optionsLoad', 'tp/datatables-fixedheader-fixedcolumns/load-option-value', option_name => {
	if ( 'datatables_fixedheader' !== option_name && 'datatables_fixedcolumns' !== option_name ) {
		return option_name;
	}

	if ( '' !== tp.table.options[ option_name ] ) {
		const datatables_fixedheader_fixedcolumns = tp.table.options[ option_name ].split( ',' );
		datatables_fixedheader_fixedcolumns.forEach( ( value ) => ( $( `#option-${ option_name }-${ value }` ).checked = true ) );
	}

	if ( tp.table.options.datatables_fixedheader.includes( 'top' ) && tp.table.options.datatables_fixedheader_offsettop > 0 ) {
		$( '#tablepress-datatables_fixedheader-advanced-settings' ).open = true;
	}

	if ( tp.table.options.datatables_fixedcolumns_left_columns > 1 || tp.table.options.datatables_fixedcolumns_right_columns > 1 || tp.table.options.datatables_scrollx_buttons ) {
		$( '#tablepress-datatables_fixedcolumns-advanced-settings' ).open = true;
	}

	return ''; // Skip the regular option loading for "datatables_fixedheader" and "datatables_fixedcolumns" by returning "".
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-fixedheader-fixedcolumns/handle-options-check-dependencies', () => {
	const fixedheader_fixedcolumns_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head );
	$( '#tablepress_edit-datatables-fixedheader-fixedcolumns' ).querySelectorAll( ':scope input' ).forEach( ( $field ) => ( $field.disabled = ! fixedheader_fixedcolumns_enabled ) );

	const fixedheader_bottom_enabled = ( fixedheader_fixedcolumns_enabled && tp.table.options.table_foot );
	const option_datatables_fixedheader_bottom = $( '#option-datatables_fixedheader-bottom' );
	option_datatables_fixedheader_bottom.disabled = ! fixedheader_bottom_enabled;
	option_datatables_fixedheader_bottom.nextElementSibling.title = fixedheader_fixedcolumns_enabled && ! fixedheader_bottom_enabled ? sprintf( __( 'This feature is only available when the “%1$s” checkbox in the “%2$s” section is checked.', 'tablepress' ), __( 'Table Foot Row', 'tablepress' ), __( 'Table Options', 'tablepress' ) ) : '';

	const option_datatables_fixedheader_offsettop = $( '#option-datatables_fixedheader_offsettop' );
	option_datatables_fixedheader_offsettop.disabled = ! fixedheader_fixedcolumns_enabled || ! tp.table.options.datatables_fixedheader.includes( 'top' );
	option_datatables_fixedheader_offsettop.parentNode.title = option_datatables_fixedheader_offsettop.disabled ? sprintf( __( 'This feature is only available when the “%1$s” checkbox is checked.', 'tablepress' ), __( 'Head Row', 'tablepress' ) ) : '';

	const option_datatables_fixedcolumns_left_columns = $( '#option-datatables_fixedcolumns_left_columns' );
	option_datatables_fixedcolumns_left_columns.disabled = ! fixedheader_fixedcolumns_enabled || ! tp.table.options.datatables_fixedcolumns.includes( 'left' );
	option_datatables_fixedcolumns_left_columns.parentNode.title = option_datatables_fixedcolumns_left_columns.disabled ? sprintf( __( 'This feature is only available when the “%1$s” checkbox is checked.', 'tablepress' ), __( 'First column', 'tablepress' ) ) : '';

	const option_datatables_fixedcolumns_right_columns = $( '#option-datatables_fixedcolumns_right_columns' );
	option_datatables_fixedcolumns_right_columns.disabled = ! fixedheader_fixedcolumns_enabled || ! tp.table.options.datatables_fixedcolumns.includes( 'right' );
	option_datatables_fixedcolumns_right_columns.parentNode.title = option_datatables_fixedcolumns_right_columns.disabled ? sprintf( __( 'This feature is only available when the “%1$s” checkbox is checked.', 'tablepress' ), __( 'Last column', 'tablepress' ) ) : '';

	$( '#option-datatables_scrollx_buttons' ).disabled = ! fixedheader_fixedcolumns_enabled || ! ( tp.table.options.datatables_fixedcolumns.includes( 'left' ) || tp.table.options.datatables_fixedcolumns.includes( 'right' ) || tp.table.options.datatables_scrollx );

	$( '#notice-datatables-fixedheader-fixedcolumns-requirements' ).style.display = fixedheader_fixedcolumns_enabled ? 'none' : 'block';
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-fixedheader-fixedcolumns/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-fixedheader-fixedcolumns' );
	return options_meta_boxes;
} );

add_filter( 'tablepress.optionsValidateFields', 'tp/datatables-advanced-loading/validate-fields', form_valid => {
	// The table header top offset value must be a non-negative number.
	if ( '' === tp.table.options.datatables_fixedheader_offsettop || isNaN( tp.table.options.datatables_fixedheader_offsettop ) || tp.table.options.datatables_fixedheader_offsettop < 0 ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Fixed Header Top Offset', 'tablepress' ) ) );
		const $field = $( '#option-datatables_fixedheader_offsettop' );
		$field.closest( 'details' ).open = true;
		$field.focus();
		$field.select();
		form_valid = false;
	}

	// The Fixed columns on the left value must be a non-negative number.
	if ( '' === tp.table.options.datatables_fixedcolumns_left_columns || isNaN( tp.table.options.datatables_fixedcolumns_left_columns ) || tp.table.options.datatables_fixedcolumns_left_columns < 0 ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Fixed Columns on the left', 'tablepress' ) ) );
		const $field = $( '#option-datatables_fixedcolumns_left_columns' );
		$field.closest( 'details' ).open = true;
		$field.focus();
		$field.select();
		form_valid = false;
	}

	// The Fixed columns on the right value must be a non-negative number.
	if ( '' === tp.table.options.datatables_fixedcolumns_right_columns || isNaN( tp.table.options.datatables_fixedcolumns_right_columns ) || tp.table.options.datatables_fixedcolumns_right_columns < 0 ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Fixed Columns on the right', 'tablepress' ) ) );
		const $field = $( '#option-datatables_fixedcolumns_right_columns' );
		$field.closest( 'details' ).open = true;
		$field.focus();
		$field.select();
		form_valid = false;
	}

	return form_valid;
} );
