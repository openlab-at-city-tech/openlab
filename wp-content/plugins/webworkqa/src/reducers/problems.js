import { RECEIVE_PROBLEMS } from '../actions/problems'

export function problems( state = {}, action ) {
	switch ( action.type ) {
		case RECEIVE_PROBLEMS :
			return action.payload

		default :
			return state
	}
}
