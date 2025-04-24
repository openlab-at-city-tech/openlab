import { connect } from 'react-redux'
import ResponseForm from '../components/ResponseForm'
import { sendResponse, setResponsePending } from '../actions/responses'
import { setCollapsed } from '../actions/app'
import { setIncomplete } from '../actions/questions'

const mapStateToProps = (state, ownProps) => {
	const { collapsed, formData, responseFormPending } = state
	const { questionId } = ownProps

	const isPending = responseFormPending.hasOwnProperty( questionId ) && responseFormPending[ questionId ]

	const collapsedIndex = 'responseForm-' + ownProps.questionId
	const isCollapsed = collapsed.hasOwnProperty( collapsedIndex )
	const responseData = formData.hasOwnProperty( 'response-' + questionId ) ? formData[ 'response-' + questionId ] : ''

	let responseText = ''
	if ( 'undefined' !== typeof responseData && responseData.hasOwnProperty( 'content' ) ) {
		responseText = responseData.content
	}

	const showIncompleteToggle = window.WWData.user_is_admin
	const { isIncomplete } = formData.hasOwnProperty( 'question-' + questionId ) ? formData['question-' + questionId] : false
	const incompleteText = window.WWData.incompleteQuestionText

	return {
		incompleteText,
		isCollapsed,
		isIncomplete,
		isPending,
		responseText,
		showIncompleteToggle
	}
}

const mapDispatchToProps = (dispatch, ownProps) => {
	return {
		onAccordionClick: () => {
			dispatch( setCollapsed( 'responseForm-' + ownProps.questionId ) )
		},

		onIncompleteToggleChange: ( isIncomplete ) => {
			dispatch( setIncomplete( ownProps.questionId, isIncomplete ) )
		},

		onResponseFormSubmit: ( e, responseText ) => {
			e.preventDefault()
			dispatch( setResponsePending( ownProps.questionId, true ) )
			dispatch( sendResponse( ownProps.questionId, responseText ) )
			dispatch( setIncomplete( ownProps.questionId, false ) )
		}
	}
}

const ResponseFormContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(ResponseForm)

export default ResponseFormContainer
