import { fetchProblem } from './problems'
import { fetchQuestionIndexList } from './questions'

export const fetchAppData = () => {
	return ( dispatch ) => {
		const { rest_api_endpoint, rest_api_nonce } = window.WWData
		const endpoint = rest_api_endpoint + 'app-config/'

		return fetch( endpoint, {
			method: 'GET',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			}
		} )
		.then( response => response.json() )
		.then( json => {
			dispatch( setSubscriptions( json.subscriptions ) )
		} )
	}
}

export const SET_INITIAL_LOAD_COMPLETE = 'SET_INITAL_LOAD_COMPLETE'
export const setInitialLoadComplete = ( isInitialLoadComplete ) => {
	return {
		type: SET_INITIAL_LOAD_COMPLETE,
		payload: isInitialLoadComplete
	}
}

export const SET_APP_IS_LOADING = 'SET_APP_IS_LOADING'
export const setAppIsLoading = (appIsLoading) => {
	return {
		type: SET_APP_IS_LOADING,
		payload: {
			appIsLoading
		}
	}
}

export const TOGGLE_EDITING = 'TOGGLE_EDITING'
export const toggleEditing = ( itemId, value ) => {
	return {
		type: TOGGLE_EDITING,
		payload: {
			itemId,
			value
		}
	}
}

export const SET_COLLAPSED = 'SET_COLLAPSED'
export const setCollapsed = ( itemId, value = null ) => {
	return {
		type: SET_COLLAPSED,
		payload: {
			itemId,
			value
		}
	}
}

export const SET_COLLAPSED_BULK = 'SET_COLLAPSED_BULK'
export const setCollapsedBulk = ( c = [] ) => {
	return {
		type: SET_COLLAPSED_BULK,
		payload: c
	}
}

export function processFilterChange( slug, value ) {
	return ( dispatch ) => {
		dispatch( setFilterToggle( slug, value ) )
		dispatch( fetchQuestionIndexList( false ) )
	}
}

export const REBUILD_HASH = 'REBUILD_HASH'
export function rebuildHash() {
	return {
		type: REBUILD_HASH
	}
}

export const RESET_CURRENT_FILTERS = 'RESET_CURRENT_FILTERS'
export const resetCurrentFilters = () => {
	return {
		type: RESET_CURRENT_FILTERS,
		payload: {}
	}
}

export const SET_FILTER_TOGGLE = 'SET_FILTER_TOGGLE'
export const setFilterToggle = ( slug, value ) => {
	// Don't tell the Redux gods about this.
	if ( '' == value ) {
		value = false
	}

	return {
		type: SET_FILTER_TOGGLE,
		payload: {
			slug,
			value
		}
	}
}

export function processOrderbyChange( orderby, problemId ) {
	return ( dispatch ) => {
		dispatch( setSortOrderby( orderby ) )

		// This suggests that the handler should belong to the QuestionContainer
		// and ProblemContainer. Passing this param feels icky.
		if ( problemId ) {
			dispatch( fetchProblem( problemId ) )
		} else {
			dispatch( fetchQuestionIndexList( false ) )
		}
	}
}

export const SET_SORT_ORDERBY = 'SET_SORT_ORDERBY'
export const setSortOrderby = ( orderby ) => {
	const order = 'DESC'

	return {
		type: SET_SORT_ORDERBY,
		payload: {
			orderby,
			order
		}
	}
}

export const RECEIVE_FILTER_OPTIONS = 'RECEIVE_FILTER_OPTIONS'
export const receiveFilterOptions = ( filterOptions ) => {
	return {
		type: RECEIVE_FILTER_OPTIONS,
		payload: filterOptions
	}
}

export const SET_TEXTAREA_VALUE = 'SET_TEXTAREA_VALUE'
export const setTextareaValue = ( fieldId, fieldName, value ) => {
	return {
		type: SET_TEXTAREA_VALUE,
		payload: {
			fieldId,
			fieldName,
			value
		}
	}
}

export const SET_TEXTAREA_VALUES = 'SET_TEXTAREA_VALUES'
export const setTextareaValues = ( values ) => {
	return {
		type: SET_TEXTAREA_VALUES,
		payload: {
			values
		}
	}
}

export const ADD_FEEDBACK_MESSAGE = 'ADD_FEEDBACK_MESSAGE'
export const addFeedbackMessage = ( payload ) => {
	return {
		type: ADD_FEEDBACK_MESSAGE,
		payload
	}
}

export const toggleSubscription = ( itemId ) => {
	return ( dispatch, getState ) => {
		const state = getState()

		const isSubscribed = state.subscriptions.hasOwnProperty( itemId )

		dispatch( setSubscription( itemId, ! isSubscribed ) )

		if ( isSubscribed ) {
			dispatch( deleteSubscription( itemId ) )
		} else {
			dispatch( sendSubscription( itemId ) )

		}
	}
}

export const SET_SUBSCRIPTION = 'SET_SUBSCRIPTION'
export const setSubscription = ( itemId, value ) => {
	return {
		type: SET_SUBSCRIPTION,
		payload: {
			itemId,
			value
		}
	}
}

export const SET_SUBSCRIPTIONS = 'SET_SUBSCRIPTIONS'
export const setSubscriptions = ( values ) => {
	return {
		type: SET_SUBSCRIPTIONS,
		payload: values
	}
}

export const sendSubscription = ( itemId ) => {
	return ( dispatch, getState ) => {
		const { rest_api_endpoint, rest_api_nonce } = window.WWData
		const endpoint = rest_api_endpoint + 'subscriptions/'

		return fetch( endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			},
			body: JSON.stringify({
				itemId
			})
		} )
	}
}

export const deleteSubscription = ( itemId ) => {
	return ( dispatch, getState ) => {
		const { rest_api_endpoint, rest_api_nonce } = window.WWData
		const endpoint = rest_api_endpoint + 'subscriptions/' + itemId + '/'

		return fetch( endpoint, {
			method: 'DELETE',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			}
		} )
	}
}

export const ADD_ATTACHMENT = 'ADD_ATTACHMENT'
export const addAttachment = ( attData ) => {
	return {
		type: ADD_ATTACHMENT,
		payload: {
				attData
		}
	}
}

export const ADD_ATTACHMENT_TO_ITEM = 'ADD_ATTACHMENT_TO_ITEM'
export const addAttachmentToItem = ( formId, fieldName, attData ) => {
	return {
		type: ADD_ATTACHMENT_TO_ITEM,
		payload: {
				formId,
				fieldName,
				attData
		}
	}
}

export const RECEIVE_ATTACHMENTS = 'RECEIVE_ATTACHMENTS'
export const receiveAttachments = (attachments) => {
	return {
		type: RECEIVE_ATTACHMENTS,
		payload: attachments
	}
}
