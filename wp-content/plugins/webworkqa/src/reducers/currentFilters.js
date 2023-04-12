import { RESET_CURRENT_FILTERS, SET_FILTER_TOGGLE, SET_SORT_ORDERBY } from '../actions/app'
import { LOCATION_CHANGE } from 'react-router-redux'
import { getCurrentView } from '../util/webwork-url-parser'

const initialState = {
	course: false,
	problemSet: false,

	orderby: 'post_date',
	order: 'DESC'
}

export function currentFilters( state = initialState, action ) {
	switch ( action.type ) {
		case LOCATION_CHANGE :
			const currentView = getCurrentView( {
				locationBeforeTransitions: action.payload
			} )

			for ( var i in state ) {
				if ( currentView.hasOwnProperty( i ) ) {
					state[ i ] = currentView[ i ]
				} else {
					state[ i ] = initialState[ i ]
				}
			}

			return state

		case SET_FILTER_TOGGLE :
			const { slug, value } = action.payload

			return Object.assign( {}, state, {
				[ slug ]: value,
			} )

		case SET_SORT_ORDERBY :
			const { orderby, order } = action.payload

			return Object.assign( {}, state, {
				orderby,
				order
			} )

		case RESET_CURRENT_FILTERS :
			return {}

		default :
			return state
	}
}

