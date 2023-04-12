import { connect } from 'react-redux'
import QuestionForm from '../components/QuestionForm'
import { sendQuestion, setQuestionPending, setTriedIsEmpty } from '../actions/questions'
import { setCollapsed } from '../actions/app'

const mapStateToProps = (state, ownProps) => {
	const { collapsed, formData, questionsById, triedIsEmpty } = state

	let questionForm = {
		content: '',
		tried: '',
		isPending: false
	}

	if ( formData.hasOwnProperty( 'question-form' ) ) {
		questionForm = Object.assign( {}, questionForm, formData['question-form'] )
	}

	const { content, tried, isPending } = questionForm

	const problemText = window.WWData.problem_text
	const isCollapsed = collapsed.hasOwnProperty( 'questionForm' )

	const problemHasQuestions = questionsById.length > 0

	return {
		content,
		isCollapsed,
		isPending,
		problemHasQuestions,
		problemText,
		tried,
		triedIsEmpty
	}
}

const mapDispatchToProps = (dispatch, ownProps) => {
	return {
		onAccordionClick: () => {
			dispatch( setCollapsed( 'questionForm' ) )
		},

		onQuestionFormSubmit: ( e, content, tried, problemText, triedIsEmpty ) => {
			e.preventDefault()

			// Only disable submit if the notice isn't yet showing.
			if ( 0 === tried.length && ! triedIsEmpty ) {
				dispatch( setTriedIsEmpty( true ) )
			} else {
				dispatch( setTriedIsEmpty( false ) )
				dispatch( setQuestionPending( true ) )
				dispatch( sendQuestion( ownProps.problemId ) )
			}
		}
	}
}

const QuestionFormContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(QuestionForm)

export default QuestionFormContainer
