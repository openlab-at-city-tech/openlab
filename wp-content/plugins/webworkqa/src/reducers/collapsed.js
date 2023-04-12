import { SET_COLLAPSED, SET_COLLAPSED_BULK } from '../actions/app'

export function collapsed( state = {}, action ) {
	let newState

	switch ( action.type ) {
		case SET_COLLAPSED :
			const { itemId, value } = action.payload

			let doCollapse
			if ( null === value ) {
				doCollapse = ! state.hasOwnProperty( itemId )
			} else {
				doCollapse = value
			}

			if ( doCollapse ) {
				return Object.assign( {}, state, {
					[itemId]: '1'
				} )
			}

			newState = Object.assign( {}, state )
			delete newState[ itemId ]
			return newState

		case SET_COLLAPSED_BULK :
			newState = Object.assign( {}, state )
			const items = action.payload

			for ( let i = 0; i < items.length; i++ ) {
				if ( items[ i ].value ) {
					newState[ items[ i ].key ] = '1'
				} else if ( newState.hasOwnProperty( items[ i ].key ) ) {
					delete newState[ items[ i ].key ]
				}
			}

			return newState

		default :
			return state
	}
}

