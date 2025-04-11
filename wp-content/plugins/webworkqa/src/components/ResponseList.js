import React, { Component } from 'react'
import ResponseContainer from '../containers/ResponseContainer'
import ResponseFormContainer from '../containers/ResponseFormContainer'
import { Element } from 'react-scroll'

export default class ResponseList extends Component {
	render() {
		const { isMyQuestion, isPending, questionId, responseIds } = this.props
		const responseScrollElementName = 'response-form-' + questionId

		var rows = [];
		this.props.responseIds.forEach( function(responseId) {
			rows.push(
				<ResponseContainer
				  key={responseId}
				  isMyQuestion={isMyQuestion}
				  responseId={responseId}
				/>
			);
		});

		if ( window.WWData.user_can_post_response ) {
			const key = 'response-form-' + questionId
			rows.push(
				<li className="response-form-li" key={key}>
					<Element name={responseScrollElementName}>
						<ResponseFormContainer
						  questionId={questionId}
						/>
					</Element>
				</li>
			)
		}

		return (
			<div className="ww-response-list">
				<ul
					aria-busy={isPending}
				  aria-live="polite"
				>{rows}</ul>
			</div>
		);
	}
}
