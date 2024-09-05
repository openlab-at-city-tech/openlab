/**
 * JavaScript code for the StyleVariation component.
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
	ColorIndicator,
} from '@wordpress/components';

/**
 * Returns the StyleVariation component's JSX markup.
 *
 * @param {Object}   props               Function parameters.
 * @param {Object}   props.variation     Key of the style variation.
 * @param {Object}   props.variationName Name of the style variation.
 * @param {Object}   props.variationCss  CSS properties of the style variation.
 * @param {boolean}  props.checked       Whether the style variation is currently active/selected.
 * @param {Function} props.onChange      Callback for activation of the style variation.
 * @return {Object} StyleVariation component.
 */
const StyleVariation = ( { variation, variationName, variationCss, checked, onChange } ) => {
	return (
		<div className="style-variation">
			<input
				type="radio"
				name="style-variation"
				id={ `style-variation-${variation}` }
				checked={ checked }
				onChange={ onChange }
			/>
			{ /* eslint-disable-next-line jsx-a11y/label-has-associated-control */ }
			<label
				htmlFor={ `style-variation-${variation}` }
			>
				<div className="style-variation-content">
					<h3>
						{ variationName }
					</h3>
					<p>
						{
							[ '--head-bg-color', '--head-active-bg-color', '--odd-bg-color', '--hover-bg-color', '--border-color', '--text-color' ].map( ( color ) => {
								// Get HEX color value from CSS property.
								let colorHex = variationCss[ color ];
								while ( colorHex.startsWith( 'var(' ) ) {
									const colorName = ( ( colorHex.match( /var\(([-a-z]+)\)/ ) )?.[1] ).trim();
									colorHex = variationCss[ colorName ];
								}
								return <ColorIndicator
									key={ color }
									colorValue={ colorHex }
								/>;
							} )
						}
					</p>
				</div>
			</label>
		</div>
	);
};

export default StyleVariation;
