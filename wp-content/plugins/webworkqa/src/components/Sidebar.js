import React, { Component } from 'react'
import SidebarFilterContainer from '../containers/SidebarFilterContainer'
import { __ } from '@wordpress/i18n';

export default class Sidebar extends Component {
	introText() {
		const { sidebarIntroText } = window.WWData

		return {
			__html: sidebarIntroText
		}
	}

	render() {
		return (
			<div className="ww-sidebar">
				<h3 className="ww-header">Explore Questions</h3>

				<div className="ww-sidebar-widget">
					<p dangerouslySetInnerHTML={this.introText()} />

					<ul className="ww-question-filters">
						<SidebarFilterContainer
							name={ __( 'Select Course', 'webworkqa' ) }
							type="dropdown"
							slug="course"
						/>
						<SidebarFilterContainer
							name={ __( 'Select Problem Set', 'webworkqa' ) }
							type="dropdown"
							slug="problemSet"
						/>
					</ul>
				</div>
			</div>
		)
	}
}
