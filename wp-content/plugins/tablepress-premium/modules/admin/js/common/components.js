/**
 * Common components for the Premium features of the TablePress/table block.
 *
 * @package TablePress
 * @subpackage Blocks
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/**
 * WordPress dependencies.
 */
import { CheckboxControl, RadioControl, SelectControl, TextControl } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import { shortcode_attrs_to_string } from '../../../../blocks/table/src/common/functions';

/**
 * Custom wrapper for CheckboxControls that already contains the logic for determining `checked` and `indeterminate` state from the table option value.
 *
 * @param {Object} props Component properties.
 * @return {Object} Component.
 */
export const TableOptionCheckboxControl = function( props ) {
	const {
		tableOption,
		shortcodeAttrs,
		setAttributes,
		...additionalProps
	} = props;

	return (
		<CheckboxControl
			checked={ 'string' === typeof shortcodeAttrs.named[ tableOption ] && 'true' === shortcodeAttrs.named[ tableOption ].toLowerCase() }
			indeterminate={ 'undefined' === typeof shortcodeAttrs.named[ tableOption ] }
			onChange={ ( tableOptionValue ) => {
				shortcodeAttrs.named[ tableOption ] = tableOptionValue ? 'true' : 'false';
				const parameters = shortcode_attrs_to_string( shortcodeAttrs );
				setAttributes( { parameters } );
			} }
			{ ...additionalProps }
		/>
	);
};

/**
 * Custom wrapper for RadioControls that already contains the logic for determining `selected` state from the table option value.
 *
 * @param {Object} props Component properties.
 * @return {Object} Component.
 */
export const TableOptionRadioControl = function( props ) {
	const {
		tableOption,
		shortcodeAttrs,
		setAttributes,
		...additionalProps
	} = props;

	return (
		<RadioControl
			selected={ shortcodeAttrs.named[ tableOption ] }
			onChange={ ( tableOptionValue ) => {
				shortcodeAttrs.named[ tableOption ] = tableOptionValue;
				const parameters = shortcode_attrs_to_string( shortcodeAttrs );
				setAttributes( { parameters } );
			} }
			{ ...additionalProps }
		/>
	);
};

/**
 * Custom wrapper for SelectControls that already contains the logic for determining the field value from the table option value.
 *
 * @param {Object} props Component properties.
 * @return {Object} Component.
 */
export const TableOptionSelectControl = function( props ) {
	const {
		tableOption,
		shortcodeAttrs,
		setAttributes,
		...additionalProps
	} = props;

	return (
		<SelectControl
			value={ 'undefined' !== typeof shortcodeAttrs.named[ tableOption ] ? shortcodeAttrs.named[ tableOption ].toLowerCase() : '' }
			onChange={ ( tableOptionValue ) => {
				if ( '' === tableOptionValue ) {
					delete shortcodeAttrs.named[ tableOption ];
				} else {
					shortcodeAttrs.named[ tableOption ] = tableOptionValue;
				}
				const parameters = shortcode_attrs_to_string( shortcodeAttrs );
				setAttributes( { parameters } );
			} }
			{ ...additionalProps }
		/>
	);
};

/**
 * Custom wrapper for TextControls that already contains the logic for determining the field value from the table option value.
 *
 * @param {Object} props Component properties.
 * @return {Object} Component.
 */
export const TableOptionTextControl = function( props ) {
	const {
		tableOption,
		shortcodeAttrs,
		setAttributes,
		...additionalProps
	} = props;

	return (
		<TextControl
			value={ shortcodeAttrs.named[ tableOption ] || tp.table.template[ tableOption ] }
			onChange={ ( tableOptionValue ) => {
				if ( tp.table.template[ tableOption ] === tableOptionValue ) {
					delete shortcodeAttrs.named[ tableOption ];
				} else {
					shortcodeAttrs.named[ tableOption ] = tableOptionValue;
				}
				const parameters = shortcode_attrs_to_string( shortcodeAttrs );
				setAttributes( { parameters } );
			} }
			{ ...additionalProps }
		/>
	);
};
