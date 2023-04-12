import { connect } from 'react-redux'
import AnonymousToggle from '../components/AnonymousToggle'
import { toggleAnonymous } from '../actions/questions'

const mapStateToProps = ( state, ownProps ) => {
	const { formData } = state

	let isAnonymous = true
	if ( formData.hasOwnProperty( 'question-form' ) ) {  
		isAnonymous = formData['question-form'].isAnonymous
	}

	return {
		isAnonymous
	}
}

const mapDispatchToProps = ( dispatch, ownProps ) => {
	return {
		onChange: ( event ) => {
			dispatch( toggleAnonymous() ) 
		}
	}
}

const AnonymousToggleContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(AnonymousToggle)

export default AnonymousToggleContainer
