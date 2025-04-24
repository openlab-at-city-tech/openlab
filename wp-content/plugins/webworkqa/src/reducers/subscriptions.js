import { SET_SUBSCRIPTION, SET_SUBSCRIPTIONS } from '../actions/app'

export function subscriptions( state = {}, action ) {
	switch ( action.type ) {
		case SET_SUBSCRIPTION :
			const { itemId, value } = action.payload
			let newSubs = Object.assign( {}, state )

			if ( value ) {
				newSubs[ itemId ] = 1
			} else {
				delete newSubs[ itemId ]
			}

			return newSubs

		case SET_SUBSCRIPTIONS :
			return action.payload

		default :
			return state
	}
}
