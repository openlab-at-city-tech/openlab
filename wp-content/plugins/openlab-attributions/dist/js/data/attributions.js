/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';

// Pre-load initial state from server.
const initialState = window.attrMeta ? Object.values( window.attrMeta ) : [];

// Reducer
function reducer( state, action ) {
	switch ( action.type ) {
		case 'ADD_ATTRIBUTION':
			return state.concat( [ action.item ] );

		case 'UPDATE_ATTRIBUTION':
			return state.map( ( item ) =>
				item.id === action.item.id ? action.item : item
			);

		case 'REMOVE_ATTRIBUTION':
			return state.filter( ( item ) => item.id !== action.id );
	}

	return state;
}

// Actions.
const actions = {
	add( item ) {
		return {
			type: 'ADD_ATTRIBUTION',
			item,
		};
	},
	update( item ) {
		return {
			type: 'UPDATE_ATTRIBUTION',
			item,
		};
	},
	remove( id ) {
		return {
			type: 'REMOVE_ATTRIBUTION',
			id,
		};
	},
};

// Selectors
const selectors = {
	get( state ) {
		return state;
	},
};

registerStore( 'openlab/attributions', {
	reducer,
	actions,
	selectors,
	initialState,
} );
