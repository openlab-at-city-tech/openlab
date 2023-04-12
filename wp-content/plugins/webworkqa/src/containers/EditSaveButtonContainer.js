import { connect } from 'react-redux'
import EditSaveButton from '../components/EditSaveButton'
import { setTextareaValue } from '../actions/app'
import { updateQuestion } from '../actions/questions'
import { updateResponse } from '../actions/responses'

const mapStateToProps = (state, ownProps) => {
	const { formData } = state
	const { fieldId, fieldType } = ownProps

	const key = fieldType + '-' + fieldId
	const isPending = formData.hasOwnProperty( key ) && formData[ key ].isPending

	return {
		isPending
	}
}

const mapDispatchToProps = (dispatch, ownProps) => {
	const { fieldId, fieldType } = ownProps

	const key = fieldType + '-' + fieldId

	return {
		onClick: () => {
			dispatch( setTextareaValue( key, 'isPending', true ) )

			switch ( fieldType ) {
				case 'question' :
					dispatch( updateQuestion( fieldId ) )
				break

				case 'response' :
					dispatch( updateResponse( fieldId ) )
				break
			}
		}
	}
}

const EditSaveButtonContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(EditSaveButton)

export default EditSaveButtonContainer
