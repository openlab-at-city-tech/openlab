import { TOGGLE_EDITING } from '../actions/app'

export function editing( state = {}, action ) {
	switch ( action.type ) {
		case TOGGLE_EDITING :
			const { itemId, value } = action.payload

			let updateValue = value
			if ( 'undefined' === typeof updateValue ) {
				updateValue = ! state.hasOwnProperty( itemId )
			}

			let newState = Object.assign( {}, state )

			if ( updateValue ) {
				newState[ itemId ] = 1
			} else {
				delete newState[ itemId ]
			}

			return newState

		default :
			return state
	}
}
