/**
 * JavaScript code for the LengthControl component.
 *
 * @package TablePress
 * @subpackage Default Style Customizer Screen
 * @author Tobias Bäthge
 * @since 2.2.0
 */

/**
 * WordPress dependencies.
 */
import {
	__experimentalUnitControl as UnitControl, // eslint-disable-line @wordpress/no-unsafe-wp-apis
} from '@wordpress/components';

/**
 * Returns the LengthControl component's JSX markup.
 *
 * @param {Object}   props             Function parameters.
 * @param {string}   props.cssProperty Custom CSS property for this LengthControl.
 * @param {string}   props.lengthValue Current length value of the custom CSS property.
 * @param {string}   props.name        Readable name for the custom CSS property.
 * @param {Function} props.onChange    Callback for color value changes.
 * @return {Object} LengthControl component.
 */
const LengthControl = ( { cssProperty, lengthValue, name, onChange } ) => {
	return (
		<UnitControl
			label={ name }
			value={ lengthValue }
			onChange={ ( newLength ) => onChange( cssProperty, newLength ) }
			style={ {
				width: '120px',
			} }
		/>
	);
};

export default LengthControl;
