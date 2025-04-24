import {
	RECEIVE_QUESTION_BY_ID, RECEIVE_QUESTIONS_BY_ID,
	RECEIVE_QUESTION_IDS,
	REMOVE_QUESTION,
	RESET_QUESTION_IDS
} from '../actions/questions'

export function questionsById( state = [], action ) {
	switch ( action.type ) {
		case RECEIVE_QUESTION_BY_ID :
			let newState = state
			newState.push( action.payload.questionId )
			return newState

		case RECEIVE_QUESTIONS_BY_ID :
		case RECEIVE_QUESTION_IDS :
			return state.concat( action.payload )

		case RESET_QUESTION_IDS :
			return []

		case REMOVE_QUESTION :
			let newQuestionIds = state.slice(0)

			const key = newQuestionIds.indexOf( action.payload.questionId )

			if ( key !== -1 ) {
				newQuestionIds.splice( key, 1 )
			}

			return newQuestionIds

		default :
			return state
	}
}

