/**
 * JavaScript code for the "Edit" screen integration of the DataTables Buttons feature.
 *
 * @package TablePress
 * @subpackage DataTables Buttons
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/* globals tp, jQuery */

/**
 * WordPress dependencies.
 */
import { addAction as add_action, addFilter as add_filter } from '@wordpress/hooks';

/**
 * Internal dependencies.
 */
import { $ } from '../../../admin/js/common/functions';

const buttons_wrapper_active = $( '#datatables-buttons-drag-box-wrapper-active' );
const buttons_wrapper_inactive = $( '#datatables-buttons-drag-box-wrapper-inactive' );

/**
 * Sets the "datatables_buttons" table options according to the chosen boxes.
 */
const handle_drag_box_move = () => {
	const datatables_buttons = [ ...buttons_wrapper_active.querySelectorAll( ':scope input' ) ].map( ( field ) => field.value );
	tp.table.options.datatables_buttons = datatables_buttons.join( ',' );

	tp.helpers.options.check_dependencies();
	tp.helpers.unsaved_changes.set();
};

const handle_drag_box_dblclick = function ( event ) {
	if ( ! event.target ) {
		return;
	}

	const requirements_fulfilled = ( tp.table.options.use_datatables && tp.table.options.table_head );
	const datatables_buttons_in_custom_commands = tp.table.options.datatables_custom_commands.includes( '"buttons":' );
	if ( ! requirements_fulfilled || datatables_buttons_in_custom_commands ) {
		return;
	}

	const drag_box = event.target.closest( '.drag-box' );
	const drag_box_wrapper = drag_box.closest( '.drag-box-wrapper' );
	const target_drag_box_wrapper = ( drag_box_wrapper === buttons_wrapper_active ) ? buttons_wrapper_inactive : buttons_wrapper_active;
	target_drag_box_wrapper.appendChild( drag_box );

	handle_drag_box_move();
};
buttons_wrapper_active.addEventListener( 'dblclick', handle_drag_box_dblclick );
buttons_wrapper_inactive.addEventListener( 'dblclick', handle_drag_box_dblclick );

add_filter( 'tablepress.optionsLoad', 'tp/datatables-buttons/load-option-value', option_name => {
	if ( 'datatables_buttons' !== option_name ) {
		return option_name;
	}

	if ( '' !== tp.table.options.datatables_buttons ) {
		const datatables_buttons = tp.table.options.datatables_buttons.split( ',' );
		datatables_buttons.forEach( ( button ) => ( buttons_wrapper_active.appendChild( $( `#option-datatables_buttons-${ button }` ).parentNode ) ) );
	}

	return ''; // Skip the regular option loading for "datatables_buttons" by returning "".
} );

add_action( 'tablepress.optionsCheckDependencies', 'tp/datatables-buttons/handle-options-check-dependencies', () => {
	const datatables_buttons_in_custom_commands = tp.table.options.datatables_custom_commands.includes( '"buttons":' );
	const requirements_fulfilled = ( tp.table.options.use_datatables && tp.table.options.table_head );
	const buttons_enabled = ( requirements_fulfilled && ! datatables_buttons_in_custom_commands );

	buttons_wrapper_active.querySelectorAll( ':scope input' ).forEach( ( $field ) => ( $field.disabled = ! buttons_enabled ) );
	buttons_wrapper_inactive.querySelectorAll( ':scope input' ).forEach( ( $field ) => ( $field.disabled = ! buttons_enabled ) );

	$( '#notice-datatables-buttons-requirements' ).style.display = requirements_fulfilled ? 'none' : 'block';
	$( '#notice-datatables-buttons-custom-commands' ).style.display = ( requirements_fulfilled && datatables_buttons_in_custom_commands ) ? 'block' : 'none';

	jQuery( function( j$ ) {
		j$( buttons_wrapper_active ).sortable( 'option', 'disabled', ! buttons_enabled );
		j$( buttons_wrapper_inactive ).sortable( 'option', 'disabled', ! buttons_enabled );
	} );
} );

add_filter( 'tablepress.optionsMetaBoxes', 'tp/datatables-buttons/add-meta-box', options_meta_boxes => {
	options_meta_boxes.push( '#tablepress_edit-datatables-buttons' );
	return options_meta_boxes;
} );

/**
 * Make the list of DataTables Buttons sortable.
 */
jQuery( function( j$ ) {
	j$( buttons_wrapper_active ).sortable( {
		containment: '#tablepress_edit-datatables-buttons .drag-box-section',
		cursor: 'move',
		placeholder: 'drag-box-placeholder',
		connectWith: '#datatables-buttons-drag-box-wrapper-inactive',
		update: handle_drag_box_move,
	} );
	j$( buttons_wrapper_inactive ).sortable( {
		containment: '#tablepress_edit-datatables-buttons .drag-box-section',
		cursor: 'move',
		placeholder: 'drag-box-placeholder',
		connectWith: '#datatables-buttons-drag-box-wrapper-active',
	} );
} );
