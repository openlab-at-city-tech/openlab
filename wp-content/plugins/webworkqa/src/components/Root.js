import React from 'react'
import PropTypes from 'prop-types'
import { Provider } from 'react-redux'
import AppContainer from '../containers/AppContainer'

const Root = ({ store }) => (
	<Provider store={store}>
		<AppContainer />
	</Provider>
)

Root.propTypes = {
	store: PropTypes.object.isRequired
}

export default Root
