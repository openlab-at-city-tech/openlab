import { connect } from 'react-redux'
import QuestionIndex from '../components/QuestionIndex'
import { getCurrentHash } from '../util/webwork-url-parser'

const mapStateToProps = ( state, ownProps ) => {
	const { routing } = state
	const currentHash = getCurrentHash( routing )

	return {
		isLoading: state.appIsLoading,
		isResultsPage: currentHash.length > 0
	}
}

const QuestionIndexContainer = connect(
	mapStateToProps
)(QuestionIndex)

export default QuestionIndexContainer
