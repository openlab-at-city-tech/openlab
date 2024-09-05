/**
 * JavaScript code for the "Default Style Customizer Screen" settings context.
 *
 * @package TablePress
 * @subpackage Default Style Customizer Screen
 * @author Tobias Bäthge
 * @since 2.3.0
 */

/**
 * WordPress dependencies.
 */
import { createContext, useContext, useState } from 'react';

/**
 * Internal dependencies.
 */
import { importFromCustomCss } from '../functions/css';

const ScreenSettingsContext = createContext();

/**
 * Returns the ScreenSettingsProvider component's JSX markup.
 *
 * @param {Object} props          Function parameters.
 * @param {Object} props.children Children of the component.
 * @return {Object} ScreenSettingsProvider component.
 */
export const ScreenSettingsProvider = ( { children } ) => {
	const [ currentStyle, setCurrentStyle ] = useState( importFromCustomCss ); // `importFromCustomCss` is passed as an initializer function to only run once on the initial render.

	return (
		<ScreenSettingsContext.Provider
			value={ {
				currentStyle,
				setCurrentStyle,
			} }
		>
			{ children }
		</ScreenSettingsContext.Provider>
	);
};

/**
 * Returns the ScreenSettings context.
 *
 * @return {Object} ScreenSettings context.
 */
export const useScreenSettings = () => {
	return useContext( ScreenSettingsContext );
};
