import { SET_TEXTAREA_VALUE, SET_TEXTAREA_VALUES, ADD_ATTACHMENT_TO_ITEM } from '../actions/app'
import { RECEIVE_QUESTION, SET_QUESTION_PENDING, TOGGLE_ANONYMOUS, TOGGLE_INCOMPLETE } from '../actions/questions'

export function formData( state = {}, action ) {
	switch ( action.type ) {
		case ADD_ATTACHMENT_TO_ITEM :
			const { formId, attData } = action.payload
			const attFieldName = action.payload.fieldName

			const attId = attData.id

			let newFieldForAttachment = Object.assign( {}, state[ formId ] )
			let fieldValue = newFieldForAttachment.hasOwnProperty( attFieldName ) ? newFieldForAttachment[ attFieldName ] : ''
			const shortcode = '[attachment id="' + attId + '"]'

			if ( fieldValue.length > 0 ) {
				fieldValue += "\n\n"
			}

			fieldValue += shortcode + "\n\n"
			newFieldForAttachment[ attFieldName ] = fieldValue

			let newStateForAttachment = Object.assign( {}, state )
			newStateForAttachment[ formId ] = newFieldForAttachment

			return newStateForAttachment

		case SET_TEXTAREA_VALUE :
			const { fieldId, fieldName, value } = action.payload

			let newField = Object.assign( {}, state[ fieldId ] )
			newField[ fieldName ] = value

			let newState = Object.assign( {}, state )
			newState[ fieldId ] = newField

			return newState

		case SET_TEXTAREA_VALUES :
			const { values } = action.payload
			return Object.assign( {}, state, values )

		case SET_QUESTION_PENDING :
			let newQuestionForPending = Object.assign( {}, state['question-form'] )
			newQuestionForPending.isPending = action.payload.isPending

			return Object.assign( {}, state, {
				['question-form']: newQuestionForPending
			} )

		case RECEIVE_QUESTION :
			const newQuestion = {
				attachments: {},
				content: action.payload.content,
				tried: action.payload.tried
			}
			return Object.assign( {}, state, {
				[ 'question-' + action.payload.questionId ]: newQuestion
			} )

		case TOGGLE_ANONYMOUS :
			let newAnonQuestion = Object.assign( {}, state['question-form'] )
			newAnonQuestion.isAnonymous = ! newAnonQuestion.isAnonymous

			return Object.assign( {}, state, {
				['question-form']: newAnonQuestion
			} )

		case TOGGLE_INCOMPLETE :
			const { incompleteQuestionId, isIncomplete } = action.payload
			let newIncompleteQuestion = Object.assign( {}, state['question-' + incompleteQuestionId]  )
			newIncompleteQuestion.isIncomplete = isIncomplete

			return Object.assign( {}, state, {
				['question-' + incompleteQuestionId]: newIncompleteQuestion
			} )

		default :
			return state
	}
}
