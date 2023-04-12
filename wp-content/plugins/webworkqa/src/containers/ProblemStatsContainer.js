import { connect } from 'react-redux'
import ProblemStats from '../components/ProblemStats'

const mapStateToProps = ( state ) => {
	const { questionsById, responseIdMap, responses } = state

	return {
		questionsById,
		responseIdMap,
		responses
	}
}

const ProblemStatsContainer = connect(
	mapStateToProps
)(ProblemStats)

export default ProblemStatsContainer
