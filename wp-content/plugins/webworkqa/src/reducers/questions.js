import { RECEIVE_QUESTION, RECEIVE_QUESTIONS, REMOVE_QUESTION, RESET_QUESTIONS } from '../actions/questions'
import { RECEIVE_RESPONSE_ID_FOR_MAP } from '../actions/responses'

export function questions( state = {}, action ) {
	switch ( action.type ) {
		case RECEIVE_QUESTION :
			return Object.assign( {}, state, {
				[action.payload.questionId]: action.payload
			} )

		case RECEIVE_QUESTIONS :
			return Object.assign( {}, state, action.payload )

		case REMOVE_QUESTION :
			let newQuestions = Object.assign( {}, state )
			delete( newQuestions[ action.payload.questionId ] )
			return newQuestions

		case RESET_QUESTIONS :
			return {}

		case RECEIVE_RESPONSE_ID_FOR_MAP :
			let newQuestion = Object.assign( {}, state[ action.payload.questionId ] )
			newQuestion.responseCount++

			return Object.assign( {}, state, {
				[ action.payload.questionId ]: newQuestion
			} )

		default :
			return state
	}
}
