import { actionTypes } from './reducer';

export const setActiveTab = ( value ) => ( {
	type: actionTypes.SET_ACTIVE_TAB,
	payload: value,
} );

export const setOptions = ( value ) => ( {
	type: actionTypes.SET_OPTIONS,
	payload: value,
} );
