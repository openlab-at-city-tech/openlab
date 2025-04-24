import { SET_TRIED_IS_EMPTY } from '../actions/questions'

export function triedIsEmpty( state = false, action ) {
	switch ( action.type ) {
		case SET_TRIED_IS_EMPTY :
			const { isEmpty } = action.payload
			return isEmpty

		default:
			return state
	}
}
