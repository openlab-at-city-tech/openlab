import React, { Component } from 'react'

import { _n, sprintf } from '@wordpress/i18n';

export default class ProblemStats extends Component {
	render() {
		const { questionsById, responseIdMap, responses } = this.props

		const questionCount = questionsById.length

		let responseCount = 0
		for ( var questionId in responseIdMap ) {
			if ( responseIdMap.hasOwnProperty( questionId ) ) {
				responseCount += responseIdMap[questionId].length
			}
		}

		/* translators: Question count */
		const questionString = _n( '%s question', '%s questions', questionCount, 'webworkqa' );

		/* translators: Response count */
		const responseString = _n( '%s response', '%s responses', responseCount, 'webworkqa' );

		return (
			<div className="item-stats problem-stats">
				<span className="ww-subtitle-section">{sprintf(questionString, questionCount)}</span>
				<span className="ww-subtitle-sep">/</span>
				<span className="ww-subtitle-section">{sprintf(responseString, responseCount)}</span>
			</div>
		)
	}
}
