import React, { Component } from 'react'

import { __ } from '@wordpress/i18n';

import { getCurrentView } from '../util/webwork-url-parser'
import ProblemContainer from '../containers/ProblemContainer'
import QuestionIndexContainer from '../containers/QuestionIndexContainer'
import SidebarContainer from '../containers/SidebarContainer'

export default class App extends Component {
	componentWillMount() {
		// Initial app data setup.
		this.props.onInit()
	}

	componentDidMount() {
		document.body.classList.add('is-webwork-app');
	}

	render() {
		const { route_base } = window.WWData
		const { appIsLoading, initialLoadComplete, routing } = this.props

		const currentView = getCurrentView( routing )
		const { problemId } = currentView

		let rootElement = ''
		if ( problemId ) {
			rootElement = <ProblemContainer problemId={problemId} />
		} else {
			rootElement = <QuestionIndexContainer />
		}

		const wrapperClassName = appIsLoading && ! initialLoadComplete ? 'app-loading' : ''

		return (
			<div
			  className={wrapperClassName}
				role='application'
			>
				<div className="app-loading-throbber">
					{ __( 'Loading...', 'webworkqa' ) }
				</div>

				<div className="app-content">
					<div className="ww-main">
						{rootElement}
					</div>

					<SidebarContainer />
				</div>
			</div>
		)
	}
}
