import { connect } from 'react-redux'
import PreviewableField from '../components/PreviewableField'
import { setCollapsed, setTextareaValue } from '../actions/app'
import { attachmentShortcodeRegExp } from '../util/webwork-text-formatter'

const mapStateToProps = ( state, ownProps ) => {
	const { attachments, collapsed, formData } = state
	const { fieldId, fieldName } = ownProps

	let value = ''
	let isPending = false
	if ( formData.hasOwnProperty( fieldId ) ) {
		value = formData[ fieldId ][ fieldName ]
		isPending = formData[ fieldId ].isPending
	}

	let isIncomplete = false
	if ( 0 === fieldId.indexOf( 'response-' ) ) {
		const questionId = fieldId.substr( 9 )
		if ( formData.hasOwnProperty( 'question-' + questionId ) ) {
			isIncomplete = formData['question-' + questionId].isIncomplete
		}
	}

	const isPreviewVisible = ! collapsed.hasOwnProperty( fieldId + '-' + fieldName ) || isIncomplete

	const incompleteText = window.WWData.incompleteQuestionText

	let atts = {}
	value.replace( attachmentShortcodeRegExp(), function( a, attId ) {
		if ( ! atts.hasOwnProperty( attId ) ) {
			atts[ attId ] = attachments[ attId ]
		}
		return a
	} )

	return {
		attachments: atts,
		incompleteText,
		isIncomplete,
		isPending,
		isPreviewVisible,
		value
	}
}

const mapDispatchToProps = ( dispatch, ownProps ) => {
	return {
		onPreviewToggleClick: () => {
			const collapsedKey = ownProps.fieldId + '-' + ownProps.fieldName
			dispatch( setCollapsed( collapsedKey ) )
		},

		onTextareaChange: ( event ) => {
			dispatch( setTextareaValue( ownProps.fieldId, ownProps.fieldName, event.target.value ) )
		}
	}
}

const PreviewableFieldContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(PreviewableField)

export default PreviewableFieldContainer
