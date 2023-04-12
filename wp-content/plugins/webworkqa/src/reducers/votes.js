import { SET_VOTE, SET_VOTES_BULK } from '../actions/votes'

export function votes( state = {}, action ) {
	let itemId = 0
	let voteType = ''

	switch ( action.type ) {
		case SET_VOTE :
			itemId = action.payload.itemId
			voteType = action.payload.voteType

			return Object.assign( {}, state, {
				[itemId]: voteType
			} )

		case SET_VOTES_BULK :
			return action.payload

		default :
			return state
	}
}
