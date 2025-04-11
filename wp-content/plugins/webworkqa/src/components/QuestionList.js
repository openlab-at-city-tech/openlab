import React, { Component } from 'react'
import { __ } from '@wordpress/i18n';
import QuestionContainer from '../containers/QuestionContainer'

export default class QuestionList extends Component {
	render() {
		const { isLoading, questionsById } = this.props

		var styles = {
			ul: {
				listStyleType: 'none'
			}
		};
		let rows = []

		let rowKey
		let generated = {}
		questionsById.forEach(function(questionId) {
			if ( generated.hasOwnProperty( questionId ) ) {
				return
			}

			rows.push(
				<QuestionContainer
				  itemId={questionId}
				  key={questionId}
				/>
			);

			generated[ questionId ] = 1
		});

		return (
			<div className="ww-question-list">
				<h2 className="ww-header">{ __( 'Questions & Replies', 'webworkqa' ) }</h2>
				<p className="ww-question-gloss ww-qr-gloss">
					{ __( 'NOTE: values may be different than those presented in your problem.', 'webworkqa' ) }
				</p>
				<ul
					aria-atomic="false"
					aria-busy={isLoading}
					aria-live="polite"
				  style={styles.ul}
				>{rows}</ul>
			</div>
		);
	}
}
