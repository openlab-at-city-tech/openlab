/**
 * JavaScript code for the ColorPicker component.
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
	ColorPicker as WpColorPicker,
	ComboboxControl,
	Popover,
} from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import { useScreenSettings } from '../context/screen-settings';
import cssProperties from '../data/options';

const colorProperties = Object.entries( cssProperties ).filter( ( [ , propertyData ] ) => ( 'color' === propertyData.type ) );

/**
 * Returns the ColorPicker component's JSX markup.
 *
 * @param {Object}   props             Function parameters.
 * @param {string}   props.cssProperty Custom CSS property for this ColorPicker.
 * @param {string}   props.color       Current color value of the custom CSS property.
 * @param {Function} props.onChange    Callback for color value changes.
 * @param {Function} props.onClose     Callback for closing the Popover.
 * @return {Object} ColorPicker component.
 */
const ColorPicker = ( { cssProperty, color, onChange, onClose } ) => {
	const { currentStyle } = useScreenSettings();
	const currentColorData = colorProperties.filter( ( [ optionCssProperty, ] ) => ( optionCssProperty === cssProperty ) )[0][1];
	const currentColor = ( 'border' === currentColorData.category ) ? currentColorData.name : sprintf( __( '%1$s %2$s color', 'tablepress' ), currentColorData.name, currentColorData.category );

	const comboboxOptions = colorProperties.filter( ( [ optionCssProperty, ] ) => {
		// Don't show itself.
		if ( optionCssProperty === cssProperty ) {
			return false;
		}

		let value = currentStyle[ optionCssProperty ];

		// Don't show entries that would create a circle reference.
		while ( value.startsWith( 'var(' ) ) {
			const referencedCssProperty = ( ( value.match( /var\(([-a-z]+)\)/ ) )?.[1] ).trim();
			if ( referencedCssProperty === cssProperty ) {
				return false;
			}
			value = currentStyle[ referencedCssProperty ];
		}

		return true;
	} ).map( ( [ optionCssProperty, propertyData ] ) => {
		// Reformat object to structure that ComboboxControl expects.

		// Get HEX color value from CSS property.
		let colorHex = currentStyle[ optionCssProperty ];
		while ( colorHex.startsWith( 'var(' ) ) {
			const colorName = ( ( colorHex.match( /var\(([-a-z]+)\)/ ) )?.[1] ).trim();
			colorHex = currentStyle[ colorName ];
		}

		let label = propertyData.name;
		if ( 'border' !== propertyData.category ) {
			label += ' ' + sprintf( __( '(%s color)', 'tablepress' ), propertyData.category )
		}

		return {
			label,
			value: optionCssProperty,
			itemColor: colorHex,
		};
	} )

	// Get color name from CSS property value.
	const comboboxValue = color.startsWith( 'var(' ) ? ( ( color.match( /var\(([-a-z]+)\)/ ) )?.[1] ).trim() : null;

	return (
		<Popover
			onClose={ onClose }
		>
			<div style={ {
				display: 'flex',
				width: '500px',
			} }>
				<div style={ {
					width: '50%',
				} }>
					<WpColorPicker
						color={ color }
						copyFormat='hex'
						onChange={ ( newColor ) => onChange( cssProperty, newColor ) }
					/>
				</div>
				<div style={ {
					width: '50%',
					padding: '20px',
				} }>
					<ComboboxControl
						label={ __( 'Reference an existing color', 'tablepress' ) }
						help={
							__( 'Choose your desired color in the color picker on the left.', 'tablepress' )
							+ ' '
							+ __( 'Alternatively, select an existing color above, to create a reference to it.', 'tablepress' )
							+ ' '
							+ sprintf( __( 'When that color is changed, the currently edited %s will automatically adapt as well.', 'tablepress' ), currentColor )
						}
						options={ comboboxOptions }
						value={ comboboxValue }
						onChange={ ( newColor ) => onChange( cssProperty, newColor ) }
						__experimentalRenderItem={ ( { item } ) => {
							const { label, itemColor } = item;

							const colorIndicator = (
							 	<span
									className="component-color-indicator"
									style={ {
										width: '14px',
										height: '14px',
										background: itemColor,
										verticalAlign: 'bottom',
									} }
								/>
							);

							return (
								<div>
									<div style={ { marginBottom: '0.2rem' } }>{ label }</div>
									<small>
										{ __( 'Color:', 'tablepress' ) + ' ' }
										{ colorIndicator }
										{ ' ' + itemColor.toUpperCase() }
									</small>
								</div>
							);
						} }
					/>
				</div>
			</div>
		</Popover>
	);
}

export default ColorPicker;
