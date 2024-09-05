/**
 * JavaScript code for the ColorControl component.
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
	Button,
	ColorIndicator,
} from '@wordpress/components';
import { useState } from 'react';

/**
 * Internal dependencies.
 */
import ColorPicker from './color-picker';
import { useScreenSettings } from '../context/screen-settings';

/**
 * Returns the ColorControl component's JSX markup.
 *
 * @param {Object}   props             Function parameters.
 * @param {string}   props.cssProperty Custom CSS property for this ColorControl.
 * @param {string}   props.color       Current color value of the custom CSS property.
 * @param {string}   props.name        Readable name for the custom CSS property.
 * @param {Function} props.onChange    Callback for color value changes.
 * @return {Object} ColorControl component.
 */
const ColorControl = ( { cssProperty, color, name, onChange } ) => {
	const { currentStyle } = useScreenSettings();
	const [ isVisible, setIsVisible ] = useState( false );

	// Get HEX color value from CSS property.
	let colorHex = color;
	while ( colorHex.startsWith( 'var(' ) ) {
		const colorName = ( ( colorHex.match( /var\(([-a-z]+)\)/ ) )?.[1] ).trim();
		colorHex = currentStyle[ colorName ];
	}

	return (
		<div style={ {
			display: 'flex',
			height: '36px',
		} }>
			<div style={ {
				width: '50%',
				fontWeight: 'bold',
			} }>
				{ `${ name }:` }
			</div>
			<div>
				<ColorIndicator
					colorValue={ colorHex }
					onClick={ () => setIsVisible( ! isVisible ) }
				/>
				<Button
					variant="link"
					onClick={ () => setIsVisible( ! isVisible ) }
					style={ {
						verticalAlign: 'top',
						paddingLeft: '8px',
						paddingTop: '2px',
						color: '#000000',
						textDecoration: 'none',
					} }
				>
					{ colorHex.toUpperCase() }
				</Button>
				{ isVisible &&
					<ColorPicker
						cssProperty={ cssProperty }
						color={ color }
						onChange={ onChange }
						onClose={ () => setIsVisible( ! isVisible ) }
					/>
				}
			</div>
		</div>
	);
};

export default ColorControl;
