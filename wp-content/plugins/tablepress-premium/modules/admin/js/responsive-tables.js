/**
 * JavaScript code for the "Edit" screen integration of the Responsive Tables feature.
 *
 * @package TablePress
 * @subpackage Responsive Tables
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

add_action( 'tablepress.optionsCheckDependencies', 'tp/responsive-tables/handle-options-check-dependencies', () => {
	const collapse_modal_enabled = ( tp.table.options.use_datatables && tp.table.options.table_head );
	const $collapse_modal_notice = $( '#notice-option-responsive-collapse-modal-requirements' );
	$collapse_modal_notice.style.display = collapse_modal_enabled ? 'none' : 'block';

	const $collapse_input_field = $( '#option-responsive-collapse' );
	$collapse_input_field.disabled = ! collapse_modal_enabled;
	$collapse_input_field.nextElementSibling.title = collapse_modal_enabled ? '' : $collapse_modal_notice.textContent;
	const $modal_input_field = $( '#option-responsive-modal' );
	$modal_input_field.disabled = ! collapse_modal_enabled;
	$modal_input_field.nextElementSibling.title = collapse_modal_enabled ? '' : $collapse_modal_notice.textContent;

	const flip_or_stack_selected = ( 'flip' === tp.table.options.responsive || 'stack' === tp.table.options.responsive );
	$( '#option-responsive_breakpoint' ).disabled = ! flip_or_stack_selected;
	$( '#description-option-responsive_breakpoint' ).style.display = ( flip_or_stack_selected || '' === tp.table.options.responsive ) ? 'none' : 'inline';

	const scroll_selected = ( 'scroll' === tp.table.options.responsive );
	$( '#option-responsive_scroll_buttons' ).disabled = ! scroll_selected;
	$( '#description-option-responsive_scroll_buttons' ).style.display = ( scroll_selected || '' === tp.table.options.responsive ) ? 'none' : 'inline';

} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/responsive-tables/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-responsive-tables' );
	return options_meta_boxes;
} );

add_filter( 'tablepress.optionsValidateFields', 'tp/responsive-tables/validate-fields', form_valid => {
	// The Collapse and Modal modes can not be selected if DataTables is not used or if there's no Head Row.
	if ( ( 'collapse' === tp.table.options.responsive || 'modal' === tp.table.options.responsive ) && ! ( tp.table.options.use_datatables && tp.table.options.table_head ) ) {
		window.alert( sprintf( __( 'The entered value in the “%1$s” field is invalid.', 'tablepress' ), __( 'Responsive Mode', 'tablepress' ) ) );
		const $field = $( '#option-responsive-' );
		$field.focus();
		$field.select();
		form_valid = false;
	}

	return form_valid;
} );
