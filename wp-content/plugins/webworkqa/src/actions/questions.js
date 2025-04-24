import fetch from 'isomorphic-fetch'
import {
	addFeedbackMessage,
	receiveAttachments, receiveFilterOptions,
	setAppIsLoading, setInitialLoadComplete,
	setCollapsed, setCollapsedBulk, setSubscription,
	setTextareaValue, setTextareaValues,
	toggleEditing
} from './app'
import { setScoresBulk } from './scores'

export function fetchQuestionIndexList( append ) {
	return (dispatch, getState) => {
		const { rest_api_endpoint, rest_api_nonce } = window.WWData
		let endpoint = rest_api_endpoint + 'questions/'

		const { currentFilters, queryString, questionsById } = getState()

		let filters = standardizeFiltersForEndpoint( currentFilters )

		if ( ! append ) {
			dispatch( resetQuestionIds() )
			dispatch( resetQuestions() )
			dispatch( setInitialLoadComplete( false ) )
			filters.offset = 0
		} else {
			filters.offset = questionsById.length
		}

		filters.maxResults = 5

		let qs = ''
		for ( var filterName in filters ) {
			if ( ! filters.hasOwnProperty( filterName ) ) {
				continue
			}

			if ( '' != qs ) {
				qs += '&'
			}

			qs += encodeURIComponent( filterName )
				+ '=' + encodeURIComponent( filters[ filterName ] )
		}

		if ( '' != qs ) {
			endpoint += '?' + qs
		}

		dispatch( setAppIsLoading( true ) )

		return fetch( endpoint,
		{
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'text/plain',
				'X-WP-Nonce': rest_api_nonce
			},
		} )
		.then( response => response.json() )
		.then( json => {
			dispatch( receiveAttachments( json.attachments ) )
			dispatch( receiveQuestions( json.questions ) )
			dispatch( receiveQuestionIds( json.questionIds ) )

			let toCollapse = []
			let scores = {}
			let thisQuestionId
			for ( var i = 0; i < json.questionIds.length; i++ ) {
				thisQuestionId = json.questionIds[ i ]
				toCollapse.push( {
					key: thisQuestionId + '-problem',
					value: '1'
				} )

				scores[ thisQuestionId ] = json.questions[ thisQuestionId ].voteCount
			}
			dispatch( setCollapsedBulk( toCollapse ) )
			dispatch( setScoresBulk( scores ) )

			const defaultFormContent = {
				isPending: false,
				content: '',
				tried: ''
			}

			let newFormData = {}
			let newFormContent
			for ( var j in json.questions ) {
				newFormContent = Object.assign( {}, defaultFormContent )

				newFormContent.content = json.questions[ j ].content
				newFormContent.tried = json.questions[ j ].tried

				newFormData[ 'question-' + j ] = newFormContent
			}

			dispatch( setTextareaValues( newFormData ) )

			dispatch( setAppIsLoading( false ) )
			dispatch( setInitialLoadComplete( true ) )
		} )
	}
}

function standardizeFiltersForEndpoint( filters ) {
	let s = {}

	for ( var filterName in filters ) {
		if ( ! filters.hasOwnProperty( filterName ) ) {
			continue
		}

		switch ( filterName ) {
			case 'problemSet' :
			case 'course' :
			case 'section' :
				if ( filters[ filterName ] ) {
					s[ filterName ] = filters[ filterName ]
				}
			break

			default :
				s[ filterName ] = filters[ filterName ]
			break;
		}
	}

	return s
}

export const RECEIVE_QUESTION_IDS = 'RECEIVE_QUESTION_IDS';
const receiveQuestionIds = (questionIds) => {
	return {
		type: RECEIVE_QUESTION_IDS,
		payload: questionIds
	}
}

export const RECEIVE_QUESTION = 'RECEIVE_QUESTION'
export const receiveQuestion = (question) => {
	return {
		type: RECEIVE_QUESTION,
		payload: question
	}
}

export const RECEIVE_QUESTIONS = 'RECEIVE_QUESTIONS'
export const receiveQuestions = (questions) => {
	return {
		type: RECEIVE_QUESTIONS,
		payload: questions
	}
}

export const RESET_QUESTIONS = 'RESET_QUESTIONS'
export const resetQuestions = () => {
	return {
		type: RESET_QUESTIONS,
		payload: {}
	}
}

export const RECEIVE_QUESTION_BY_ID = 'RECEIVE_QUESTION_BY_ID'
const receiveQuestionById = (questionId) => {
	return {
		type: RECEIVE_QUESTION_BY_ID,
		payload: {
			questionId
		}
	}
}

export const RECEIVE_QUESTIONS_BY_ID = 'RECEIVE_QUESTIONS_BY_ID'
export const receiveQuestionsById = (questionsById) => {
	return {
		type: RECEIVE_QUESTIONS_BY_ID,
		payload: questionsById
	}
}

export const RESET_QUESTION_IDS = 'RESET_QUESTION_IDS'
export const resetQuestionIds = () => {
	return {
		type: RESET_QUESTION_IDS,
		payload: {}
	}
}

export const REMOVE_QUESTION = 'REMOVE_QUESTION'
export const removeQuestion = (questionId) => {
	return {
		type: REMOVE_QUESTION,
		payload: {
			questionId
		}
	}
}

export const SET_QUESTION_PENDING = 'SET_QUESTION_PENDING'
export const setQuestionPending = ( isPending ) => {
	return {
		type: SET_QUESTION_PENDING,
		payload: {
			isPending
		}
	}
}

export const SET_TRIED_IS_EMPTY = 'SET_TRIED_IS_EMPTY'
export const setTriedIsEmpty = ( isEmpty ) => {
	return {
		type: SET_TRIED_IS_EMPTY,
		payload: {
			isEmpty
		}
	}
}

export const TOGGLE_ANONYMOUS = 'TOGGLE_ANONYMOUS'
export const toggleAnonymous = () => {
	return {
		type: TOGGLE_ANONYMOUS,
		payload: {}
	}
}

export const TOGGLE_INCOMPLETE = 'TOGGLE_INCOMPLETE'
export const setIncomplete = ( questionId, isIncomplete ) => {
	return {
		type: TOGGLE_INCOMPLETE,
		payload: {
			incompleteQuestionId: questionId,
			isIncomplete
		}
	}
}

export function setScrolledTo( itemId ) {
	return ( dispatch, getState ) => {
		const { questionsById } = getState()

		let pos = null
		for ( let i = 0; i < questionsById.length; i++ ) {
			if ( questionsById[ i ] === itemId ) {
				pos = i
			}
		}

		if ( pos === ( questionsById.length - 1 ) ) {
			dispatch( fetchQuestionIndexList( true ) )
		}
	}
}

export function sendQuestion( problemId ) {
	return ( dispatch, getState ) => {
		const { client_name, page_base, rest_api_endpoint, rest_api_nonce } = window.WWData
		let endpoint = rest_api_endpoint + 'questions/'

		const { queryString, formData } = getState()
		const { post_data_key } = queryString

		if ( post_data_key ) {
			endpoint += '?post_data_key=' + post_data_key
		}

		const questionForm = formData['question-form']

		let attachmentIds = []
		for ( let attachmentId in questionForm.attachments ) {
			attachmentIds.push( attachmentId )
		}

		return fetch( endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			},
			body: JSON.stringify({
				problem_id: problemId,
				client_name: client_name,
				client_url: page_base,
				content: questionForm.content,
				problem_text: window.WWData.problem_text,
				tried: questionForm.tried,
				attachments: attachmentIds,
				isAnonymous: questionForm.isAnonymous
			})
		} )
		.then( response => response.json() )
		.then( json => {
			// If there's a 'message', then it's an error.
			dispatch( setQuestionPending( false ) )
			if ( json.hasOwnProperty( 'message' ) ) {
				return
			}
			dispatch( receiveQuestion( json ) )
			dispatch( receiveQuestionById( json.questionId ) )
			dispatch( setTextareaValue( 'question-form', 'content', '' ) )
			dispatch( setTextareaValue( 'question-form', 'tried', '' ) )
			dispatch( setCollapsed( 'response-' + json.questionId + '-content', true ) )
			dispatch( setSubscription( json.questionId, true ) )

			dispatch( addFeedbackMessage( {
				itemId: json.questionId,
				type: 'success',
				text: 'Your question has been posted!\nYou will receive an email notification when your question receives a response.'
			} ) )

			// Remove the post_data_key param from the window location.
			if ( false !== window.location.search.indexOf( 'post_data_key' ) ) {
				const cleanSearch = window.location.search.replace( /&?post_data_key=[^&]+/, '' )

				let newPath = window.location.toString()
				if ( 0 == cleanSearch.length || '?' == cleanSearch ) {
					// Remove altogether.
					newPath = newPath.replace( window.location.search, '' )
				} else {
					newPath = newPath.replace( window.location.search, cleanSearch )
				}

				window.history.replaceState( {}, 'Title', newPath )
			}

			// todo - handle errors
		} )
	}
}

export function updateQuestion( questionId ) {
	return ( dispatch, getState ) => {
		const { formData } = getState()
		const { client_name, page_base, rest_api_endpoint, rest_api_nonce } = window.WWData

		let endpoint = rest_api_endpoint + 'questions/' + questionId

		const questionData = formData[ 'question-' + questionId ]

		return fetch( endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			},
			body: JSON.stringify({
				content: questionData.content,
				tried: questionData.tried
			})
		} )
		.then( response => response.json() )
		.then( json => {
			dispatch( setTextareaValue( 'question-' + questionId, 'isPending', false ) )
			dispatch( toggleEditing( questionId, false ) )
			dispatch( receiveQuestion( json ) )
		} )
	}
}

export function deleteQuestion( questionId ) {
	return ( dispatch, getState ) => {
		const { rest_api_endpoint, rest_api_nonce } = window.WWData
		const endpoint = rest_api_endpoint + 'questions/' + questionId

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
			dispatch( removeQuestion( questionId ) )
		} )
	}
}
