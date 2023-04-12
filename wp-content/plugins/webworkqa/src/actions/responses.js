import fetch from 'isomorphic-fetch'
import { setCollapsed, setTextareaValue, toggleEditing } from './app'
import { receiveQuestion } from './questions'

export const RECEIVE_RESPONSE = 'RECEIVE_RESPONSE'
const receiveResponse = (response) => {
	return {
		type: RECEIVE_RESPONSE,
		payload: response
	}
}

export const RECEIVE_RESPONSES = 'RECEIVE_RESPONSES'
export const receiveResponses = (responses) => {
	return {
		type: RECEIVE_RESPONSES,
		payload: responses
	}
}

export const RECEIVE_RESPONSE_ID_MAP = 'RECEIVE_RESPONSE_ID_MAP'
export const receiveResponseIdMap = (responseIdMap) => {
	return {
		type: RECEIVE_RESPONSE_ID_MAP,
		payload: responseIdMap
	}
}

export const RECEIVE_RESPONSE_ID_FOR_MAP = 'RECEIVE_RESPONSE_ID_FOR_MAP'
export const receiveResponseIdForMap = (responseId, questionId) => {
	return {
		type: RECEIVE_RESPONSE_ID_FOR_MAP,
		payload: {
			responseId,
			questionId
		}
	}
}

export const REMOVE_RESPONSE = 'REMOVE_RESPONSE'
export const removeResponse = (responseId, questionId) => {
	return {
		type: REMOVE_RESPONSE,
		payload: {
			responseId,
			questionId
		}
	}
}

export const SET_RESPONSE_PENDING = 'SET_RESPONSE_PENDING'
export const setResponsePending = ( questionId, isPending ) => {
	return {
		type: SET_RESPONSE_PENDING,
		payload: {
			questionId,
			isPending
		}
	}
}

export const SET_RESPONSES_PENDING_BULK = 'SET_RESPONSES_PENDING_BULK'
export const setResponsesPendingBulk = ( pending ) => {
	return {
		type: SET_RESPONSES_PENDING_BULK,
		payload: pending
	}
}

export function sendResponse( questionId, value ) {
	return ( dispatch ) => {
		const { client_name, page_base, rest_api_endpoint, rest_api_nonce } = window.WWData
		const endpoint = rest_api_endpoint + 'responses/'

		return fetch( endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			},
			body: JSON.stringify({
				client_url: page_base,
				client_name: client_name,
				question_id: questionId,
				value: value
			})
		} )
		.then( response => response.json() )
		.then( json => {
			dispatch( receiveResponse( json ) )
			dispatch( receiveResponseIdForMap( json.responseId, questionId ) )
			dispatch( setResponsePending( questionId, false ) )
			dispatch( setTextareaValue( 'response-' + questionId, 'content', '' ) )
			dispatch( setTextareaValue( 'response-' + json.responseId, 'content', value ) )
			// todo - handle errors

		} )
	}
}

export function deleteResponse( responseId ) {
	return ( dispatch, getState ) => {
		const { rest_api_endpoint, rest_api_nonce } = window.WWData
		const endpoint = rest_api_endpoint + 'responses/' + responseId

		return fetch( endpoint, {
			method: 'DELETE',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			}
		} )
		.then( response => response.json() )
		.then( json => {
			const state = getState()

			const questionId = state.responses[ responseId ].questionId

			dispatch( removeResponse( responseId, questionId ) )
		} )
	}
}

export function clickAnswered( responseId, isAnswered ) {
	return ( dispatch ) => {
		dispatch( sendResponseAnswered( responseId, isAnswered ) )
		dispatch( setResponseAnswered( responseId, isAnswered ) )
	}
}

export function updateResponse( responseId ) {
	return ( dispatch, getState ) => {
		const { formData } = getState()
		const { client_name, page_base, rest_api_endpoint, rest_api_nonce } = window.WWData

		let endpoint = rest_api_endpoint + 'responses/' + responseId

		const responseData = formData[ 'response-' + responseId ]

		return fetch( endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			},
			body: JSON.stringify({
				content: responseData.content
			})
		} )
		.then( requestResponse => requestResponse.json() )
		.then( json => {
			dispatch( setTextareaValue( 'response-' + responseId, 'isPending', false ) )
			dispatch( toggleEditing( responseId, false ) )
			dispatch( receiveResponse( json ) )
		} )
	}
}
