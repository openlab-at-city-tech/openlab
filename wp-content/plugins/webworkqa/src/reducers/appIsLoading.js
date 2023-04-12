import { SET_APP_IS_LOADING } from '../actions/app'

export function appIsLoading( state = false, action ) {
	switch ( action.type ) {
		case SET_APP_IS_LOADING :
			const { appIsLoading } = action.payload
			return appIsLoading

		default:
			return state
	}
}
