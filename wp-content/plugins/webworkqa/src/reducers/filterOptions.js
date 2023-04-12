import { RECEIVE_FILTER_OPTIONS } from '../actions/app'

export function filterOptions( state = window.WWData.filter_options, action ) {
	switch ( action.type ) {
		case RECEIVE_FILTER_OPTIONS :
			return action.payload

		default :
			return state
	}
}
