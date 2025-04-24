import { connect } from 'react-redux'
import ScoreDialog from '../components/ScoreDialog'
import { clickVote } from '../actions/votes'
import { getCurrentView } from '../util/webwork-url-parser'

const mapStateToProps = (state, ownProps) => {
	const { collapsed, responseIdMap, scores, routing, votes } = state
	const { itemId } = ownProps

	const currentView = getCurrentView( routing )

	const isSingleProblem = currentView.hasOwnProperty( 'problemId' )
	const isCollapsed = collapsed.hasOwnProperty( itemId )

	const vote = votes.hasOwnProperty( itemId ) ? votes[ itemId ] : ''

	let score = scores.hasOwnProperty( itemId ) ? scores[ itemId ] : 0
	let responseId
	if ( isCollapsed && isSingleProblem ) {
		if ( responseIdMap.hasOwnProperty( itemId ) ) {
			for ( var k in responseIdMap[ itemId ] ) {
				responseId = responseIdMap[ itemId ][ k ]
				if ( scores.hasOwnProperty( responseId ) ) {
					score += scores[ responseId ]
				}

				if ( votes.hasOwnProperty( responseId ) && 'up' === votes[ responseId ] ) {
					score++
				}
			}
		}
	}

	return {
		isSingleProblem,
		score,
		userCanVote: window.WWData.user_can_vote,
		vote
	}
}

const mapDispatchToProps = (dispatch, ownProps) => {
	return {
		onVoteClick: ( itemId, voteType ) => {
			dispatch( clickVote( itemId, voteType, ownProps.itemType ) )
		}
	}
}

const ScoreDialogContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(ScoreDialog)

export default ScoreDialogContainer
