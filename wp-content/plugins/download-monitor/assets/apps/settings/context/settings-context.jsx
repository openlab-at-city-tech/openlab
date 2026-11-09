import { createContext, useReducer } from '@wordpress/element';
import { reducer } from './reducer';
import { initialState } from './initial-state';

export const SettingsContext = createContext( initialState );

export const SettingsProvider = ( { children } ) => {
	const [ state, dispatch ] = useReducer( reducer, initialState );

	return (
		<SettingsContext.Provider value={ { state, dispatch } }>
			{ children }
		</SettingsContext.Provider>
	);
};
