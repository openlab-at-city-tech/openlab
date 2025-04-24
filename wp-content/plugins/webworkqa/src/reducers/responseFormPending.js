import { SET_RESPONSE_PENDING, SET_RESPONSES_PENDING_BULK } from '../actions/responses'

export function responseFormPending( state = {}, action ) {
	switch ( action.type ) {
		case SET_RESPONSE_PENDING :
			const { questionId, isPending } = action.payload

			return Object.assign( {}, state, {
				[questionId]: isPending
			} )

		case SET_RESPONSES_PENDING_BULK :
			return action.payload

		default :
			return state
	}
}
