import { connect } from 'react-redux'
import QuestionSortDropdown from '../components/QuestionSortDropdown'
import { fetchQuestionIndexList } from '../actions/questions'
import { fetchProblem } from '../actions/problems'
import { processOrderbyChange } from '../actions/app'

const mapStateToProps = ( state, ownProps ) => {
	const { currentFilters } = state

	let orderby
	if ( currentFilters ) {
		orderby = currentFilters.orderby
	}

	return {
		orderby
	}
}

const mapDispatchToProps = ( dispatch, ownProps ) => {
	const onSortChange = ( change ) => {
		const { value } = change
		const { problemId } = ownProps

		dispatch( processOrderbyChange( value, problemId ) )
	}

	return {
		onSortChange
	}
}

const QuestionSortDropdownContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(QuestionSortDropdown)

export default QuestionSortDropdownContainer
