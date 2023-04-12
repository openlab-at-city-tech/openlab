import { RECEIVE_RESPONSE_ID_FOR_MAP, RECEIVE_RESPONSE_ID_MAP, REMOVE_RESPONSE } from '../actions/responses'
import { REMOVE_QUESTION } from '../actions/questions'

export function responseIdMap( state = {}, action ) {
	switch ( action.type ) {
		case RECEIVE_RESPONSE_ID_FOR_MAP :
			const { questionId, responseId } = action.payload

			let questionResponseIds = []
			if ( state.hasOwnProperty( questionId ) ) {
				// Clone the original array to avoid reference problems.
				questionResponseIds = state[questionId].slice(0)
				questionResponseIds.push( responseId )
			} else {
				questionResponseIds.push( responseId )
			}

			return Object.assign( {}, state, {
				[questionId]: questionResponseIds
			} )

		case RECEIVE_RESPONSE_ID_MAP :
			return action.payload

		case REMOVE_RESPONSE :
			let newQuestionResponseIds = state[ action.payload.questionId ].slice(0)
			const key = newQuestionResponseIds.indexOf( action.payload.responseId )

			if ( key !== -1 ) {
				newQuestionResponseIds.splice( key, 1 )
			}

			return Object.assign( {}, state, {
				[ action.payload.questionId ]: newQuestionResponseIds
			} )

		case REMOVE_QUESTION :
			let newQuestions = Object.assign( {}, state )
			delete( newQuestions[ action.payload.questionId ] )
			return newQuestions

		default :
			return state
	}
}
