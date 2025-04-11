import { connect } from 'react-redux'
import App from '../components/App'
import { fetchAppData } from '../actions/app'

const mapStateToProps = (state, ownProps) => {
	const { appIsLoading, initialLoadComplete, routing } = state

	return {
		appIsLoading,
		initialLoadComplete,
		routing
	}
}

const mapDispatchToProps = (dispatch, ownProps) => {
	return {
		onInit: () => {
			dispatch( fetchAppData() )
		}
	}
}

const AppContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(App)

export default AppContainer
