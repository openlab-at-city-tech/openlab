import { connect } from 'react-redux'
import { setCollapsed, toggleEditing } from '../actions/app'
import { deleteResponse } from '../actions/responses'
import Response from '../components/Response'
import { attachmentShortcodeRegExp } from '../util/webwork-text-formatter'

const mapStateToProps = (state, ownProps) => {
	const { attachments, editing, questions, responses } = state
	const { responseId } = ownProps

	const response = responses.hasOwnProperty( responseId ) ? responses[ responseId ] : null

	const isEditing = editing.hasOwnProperty( responseId )

	let userCanEdit = false
	if ( null !== response ) {
		userCanEdit = window.WWData.user_is_admin || response.authorId == window.WWData.user_id
	}

	let atts = {}
	if ( null !== response ) {
		response.content.replace( attachmentShortcodeRegExp(), function( a, attId ) {
			if ( ! atts.hasOwnProperty( attId ) ) {
				atts[ attId ] = attachments[ attId ]
			}
			return a
		} )
	}

	let questionIsAnonymous = false
	if ( response ) {
		questionIsAnonymous = questions[ response.questionId ].isAnonymous
	}

	return {
		attachments: atts,
		isEditing,
		questionIsAnonymous,
		response,
		userCanEdit,
		userCanPostResponse: window.WWData.user_can_post_response > 0
	}
}

const mapDispatchToProps = (dispatch, ownProps) => {
	const { responseId } = ownProps

	return {
		onDeleteClick: () => {
			dispatch( deleteResponse( responseId ) )
		},

		onEditClick: () => {
			dispatch( toggleEditing( responseId ) )
			dispatch( setCollapsed( 'response-' + responseId + '-content', true ) )
		}
	}
}

const ResponseContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(Response)

export default ResponseContainer
