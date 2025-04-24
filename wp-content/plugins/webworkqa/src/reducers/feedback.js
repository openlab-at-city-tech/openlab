import { ADD_FEEDBACK_MESSAGE } from '../actions/app'

export function feedback( state = {}, action ) {
	switch ( action.type ) {
		case ADD_FEEDBACK_MESSAGE :
			const { itemId, type, text } = action.payload

			let itemFeedbacks = {}
			if ( state.hasOwnProperty( itemId ) ) {
				itemFeedbacks = Object.assign( {}, state[ itemId ] )
			}

			// Only one message per type?
			itemFeedbacks[ type ] = text

			return Object.assign( {}, state, {
				[ itemId ]: itemFeedbacks	
			} )

		default :
			return state
	}
}
