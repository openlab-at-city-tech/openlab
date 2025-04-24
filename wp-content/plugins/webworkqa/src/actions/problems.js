import fetch from 'isomorphic-fetch'
import {
	receiveAttachments, receiveFilterOptions,
	setInitialLoadComplete, setAppIsLoading,
	setCollapsedBulk, setTextareaValues
} from './app'
import { receiveQuestions, receiveQuestionsById, resetQuestionIds } from './questions'
import { receiveResponseIdMap, setResponsesPendingBulk, receiveResponses } from './responses'
import { setScoresBulk } from './scores'
import { setVotesBulk } from './votes'

export const REQUEST_PROBLEM = 'REQUEST_PROBLEM';
const requestProblem = (problemId) => {
	return {
		type: REQUEST_PROBLEM,
		problemId
	}
}

export const RECEIVE_PROBLEM = 'RECEIVE_PROBLEM';
const receiveProblem = (problemId, problem) => {
	const { ID, title, content } = problem
	return {
		type: RECEIVE_PROBLEM,
		payload: {
			ID,
			title,
			content
		}
	}
}

export const REQUEST_PROBLEMS = 'REQUEST_PROBLEMS';
const requestProblems = (problems) => {
	return {
		type: REQUEST_PROBLEMS,
		payload: problems
	}
}

export const RECEIVE_PROBLEMS = 'RECEIVE_PROBLEMS'
const receiveProblems = (problems) => {
	return {
		type: RECEIVE_PROBLEMS,
		payload: problems
	}
}

export function fetchProblem( problemId ) {
	return (dispatch, getState) => {
		// Reset a bunch of stuff.
		// Could work around this with a better-structured state (store all data per-problem)
		dispatch( setInitialLoadComplete( false ) )
		dispatch( receiveProblems( {} ) )
		dispatch( resetQuestionIds() )
		dispatch( receiveResponseIdMap( {} ) )

		const { page_base, rest_api_endpoint, rest_api_nonce } = window.WWData

		let endpoint = rest_api_endpoint + 'problems/?problem_id=' + problemId

		const { currentFilters, queryString } = getState()
		const { post_data_key } = queryString

		if ( post_data_key ) {
			endpoint += '&post_data_key=' + post_data_key
		}

		endpoint += '&orderby=' + currentFilters.orderby
		endpoint += '&client_url=' + page_base

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
			const {
				attachments, problems, questions, questionsById,
				responseIdMap, responses, scores, votes,
				filterOptions
			} = json

			// No problems? No problem. This will be a 404.
			if ( 0 !== problems.length ) {
				let score = 0;
				let vote = 0;

				dispatch( receiveProblems( problems ) )
				dispatch( receiveQuestions( questions ) )
				dispatch( receiveAttachments( attachments ) )

				// Set "pending" status for response forms.
				let pending = {}
				questionsById.forEach( questionId => {
					pending[questionId] = false
				} )
				dispatch( setResponsesPendingBulk( pending ) )

				dispatch( receiveQuestionsById( questionsById ) )

				// @todo Collapsing should probably happen in componentDidMount or something
				let toCollapse = []
				for ( var i = 0; i < questionsById.length; i++ ) {
					toCollapse.push( {
						key: questionsById[ i ] + '-problem',
						value: true
					} )

					// Response previews.
					toCollapse.push( {
						key: 'response-' + questionsById[ i ] + '-content',
						value: true
					} )
				}

				// Question form field and response previews
				toCollapse.push( {
					key: 'question-form-content',
					value: true
				} )
				toCollapse.push( {
					key: 'question-form-tried',
					value: true
				} )

				dispatch( setCollapsedBulk( toCollapse ) )

				dispatch( receiveResponseIdMap( responseIdMap ) )
				dispatch( receiveResponses( responses ) )

				dispatch( setScoresBulk( scores ) )
				dispatch( setVotesBulk( votes ) )

				const defaultFormContent = {
					isPending: false,
					content: '',
					tried: '',
					isAnonymous: false,
				}

				let newFormData = {}
				let newFormContent
				for ( var j in json.questions ) {
					newFormContent = Object.assign( {}, defaultFormContent )
					newFormContent.attachments = {}

					newFormContent.content = json.questions[ j ].content
					newFormContent.tried = json.questions[ j ].tried
					newFormContent.isAnonymous = json.questions[ j ].isAnonymous

					newFormContent.isIncomplete = false

					newFormData[ 'question-' + j ] = newFormContent
				}

				for ( var k in json.responses ) {
					newFormContent = Object.assign( {}, defaultFormContent )
					newFormContent.attachments = {}

					newFormContent.content = json.responses[ k ].content

					newFormData[ 'response-' + k ] = newFormContent
				}

				newFormData['question-form'] = Object.assign( {}, defaultFormContent )
				newFormData['question-form'].attachments = {}
				newFormData['question-form'].isAnonymous = false

				dispatch( setTextareaValues( newFormData ) )
			}

			dispatch( receiveFilterOptions( filterOptions ) )

			dispatch( setInitialLoadComplete( true ) )
			dispatch( setAppIsLoading( false ) )
		} )
	}
}
