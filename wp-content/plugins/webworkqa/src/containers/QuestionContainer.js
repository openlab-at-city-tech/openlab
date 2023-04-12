import { connect } from 'react-redux'
import Question from '../components/Question'
import { setCollapsed, toggleEditing } from '../actions/app'
import { deleteQuestion, setScrolledTo } from '../actions/questions'
import { getCurrentView } from '../util/webwork-url-parser'
import { attachmentShortcodeRegExp } from '../util/webwork-text-formatter'

const mapStateToProps = (state, ownProps) => {
	const {
		attachments, collapsed, editing, feedback, formData, initialLoadComplete,
		questions, responseFormPending, responseIdMap, responses, routing
	} = state

	const { itemId } = ownProps

	const currentView = getCurrentView( routing )

	const isCollapsed = collapsed.hasOwnProperty( itemId )
	const isProblemSummaryCollapsed = collapsed.hasOwnProperty( itemId + '-problem' )

	const question = questions[itemId]
	const responseIds = responseIdMap.hasOwnProperty( itemId ) ? responseIdMap[itemId] : []

	const isSingleProblem = currentView.hasOwnProperty( 'problemId' )
	const isQuestionAnchor = currentView.hasOwnProperty( 'questionId' )
	const isCurrentQuestion = ( isSingleProblem && isQuestionAnchor && currentView.questionId == itemId )

	const isEditing = editing.hasOwnProperty( itemId )

	const isPending = formData.hasOwnProperty( 'question-' + itemId ) && formData[ 'question-' + itemId ].isPending
	const responseIsPending = responseFormPending.hasOwnProperty( itemId ) && responseFormPending[ itemId ]

	const routeBase = window.WWData.route_base
	const questionLink = '/'
		+ routeBase + '#:problemId='
		+ question.problemId + ':questionId='
		+ itemId

	const userCanEdit = WWData.user_is_admin || question.authorId == WWData.user_id

	let atts = {}, textAtts
	const texts = [
		question.content,
		question.tried
	]
	for ( let i in texts ) {
		texts[i].replace( attachmentShortcodeRegExp(), function( a, attId ) {
			if ( ! atts.hasOwnProperty( attId ) ) {
				atts[ attId ] = attachments[ attId ]
			}
			return a
		} )
	}

	return {
		attachments: atts,
		feedback: feedback.hasOwnProperty( itemId ) ? feedback[ itemId ] : {},
		initialLoadComplete,
		isCollapsed,
		isEditing,
		isPending,
		isProblemSummaryCollapsed,
		isCurrentQuestion,
		isSingleProblem,
		question,
		questionLink,
		responseIds,
		responseIsPending,
		responses,
		userCanEdit,
		userCanPostResponse: window.WWData.user_can_post_response > 0,
		userCanSubscribe: window.WWData.user_can_post_response > 0
	}
}

const mapDispatchToProps = (dispatch, ownProps) => {
	const { itemId } = ownProps

	return {
		onAccordionClick: () => {
			dispatch( setCollapsed( itemId ) )
		},

		onDeleteClick: () => {
			dispatch( deleteQuestion( itemId ) )
		},

		onEditClick: () => {
			dispatch( toggleEditing( itemId ) )
			dispatch( setCollapsed( 'question-' + itemId + '-content', true ) )
			dispatch( setCollapsed( 'question-' + itemId + '-tried', true ) )
		},

		onProblemSummaryClick: ( event ) => {
			// Don't close for clickable elements.
			const clickable = {
				SELECT: 1,
				OPTION: 1,
				A: 1,
			}

			if ( clickable.hasOwnProperty( event.target.tagName ) ) {
				return
			}

			dispatch( setCollapsed( itemId + '-problem' ) )
		},

		onRespondClick: () => {
			dispatch( setCollapsed( 'responseForm-' + itemId, false ) )
		},

		onWaypointEnter: () => {
			dispatch( setScrolledTo( itemId ) )
		}
	}
}

const QuestionContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(Question)

export default QuestionContainer
