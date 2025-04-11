import { SET_SCORE, SET_SCORES_BULK } from '../actions/scores'

export function scores( state = {}, action ) {
	let itemId = 0;

	switch ( action.type ) {
		case SET_SCORE :
			itemId = action.payload.itemId
			return Object.assign( {}, state, {
				[itemId]: action.payload.score
			} )

		case SET_SCORES_BULK :
			return Object.assign( {}, state, action.payload )

		default :
			return state
	}
}

