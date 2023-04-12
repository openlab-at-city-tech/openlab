import React, { Component } from 'react';
import FormattedProblem from './FormattedProblem'
import { __, sprintf } from '@wordpress/i18n';

export default class ProblemSummary extends Component {
	render() {
		const { problemId, problem } = this.props

		if ( ! problem ) {
			return (
				<div className="ww-problem-summary"></div>
			)
		}

		const { content, contentSwappedUrl, libraryId, maths } = problem

		const itemId = problemId.split( '/' ).join( '-' )

		return (
			<div className="ww-problem-summary">
				<FormattedProblem
				  itemId={itemId}
				  content={content}
					contentSwappedUrl={contentSwappedUrl}
				  maths={maths}
				/>

				<div className="problem-library-id">
					<i
					  aria-hidden="true"
					  className="fa fa-folder-open problem-library-id-icon"
					></i>
					<div className="problem-library-id-text">
						{ sprintf( __( 'ProblemID: %s', 'webworkqa' ), libraryId ) }
					</div>
				</div>
			</div>
		);
	}
}
