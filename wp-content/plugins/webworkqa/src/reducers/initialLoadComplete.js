import { SET_INITIAL_LOAD_COMPLETE } from '../actions/app'

export function initialLoadComplete( state = false, action ) {
	switch ( action.type ) {
		case SET_INITIAL_LOAD_COMPLETE :
			return action.payload

		default :
			return state
	}
}

