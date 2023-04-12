import React, { Component } from 'react'
import { connect } from 'react-redux'
import ReactTooltip from 'react-tooltip'
import { __, sprintf } from '@wordpress/i18n';

export default class ScoreDialog extends React.Component {
	constructor(props) {
		super(props)
	}

	render() {
		const { isSingleProblem, itemId, onVoteClick, userCanVote, vote } = this.props

		let score = this.props.score

		let scoreClass = 'ww-score'

		scoreClass += userCanVote && isSingleProblem ? ' can-vote' : ' cannot-vote'
		scoreClass += score > 0 ? ' has-votes' : ' has-no-votes'
		scoreClass += 'up' === vote ? ' liked-by-me' : ' not-liked-by-me'

		const scoreText = sprintf( __( 'Number of votes: %s', 'webworkqa' ), score )

		let iconClass = 'score-icon fa'
		let voteText
		if ( userCanVote && isSingleProblem ) {
			if ( 'up' === vote ) {
				voteText = __( 'Click to remove vote', 'webworkqa' )
			} else {
				voteText = __( 'Click to vote', 'webworkqa' )
			}
		} else {
			voteText = __( 'Join / login to like', 'webworkqa' )
		}

		const voteElementId = 'vote-element-' + itemId

		const iconElement = <i aria-hidden="true" className={iconClass}></i>

		const voteButtonIsDisabled = ! userCanVote || ! isSingleProblem

		let clickableText
		if ( isSingleProblem && userCanVote ) {
			clickableText = (
				<span>
					<span className="ww-score-value">{ __( 'This helped me', 'webworkqa' ) } ({score})</span>
					{iconElement}
				</span>
			)
		} else {
			clickableText = (
				<span>
					<span className="ww-score-value">
						{score}
						<span className="screen-reader-text">{scoreText}</span>
					</span>

					{iconElement}
				</span>
			)
		}

		const srElement = <span className="screen-reader-text">{voteText}</span>
		const voteElement = (
			<button
				aria-label={voteText}
				disabled={voteButtonIsDisabled}
				onClick={ (e) => {
					e.preventDefault()

					if ( ! voteButtonIsDisabled ) {
						onVoteClick( itemId, ( vote === 'up' ) ? '' : 'up' )
					}
				} }
			>
			{clickableText}
			<span className="screen-reader-text">{voteText}</span>
			</button>
		)

		let tooltip
		if ( ! userCanVote ) {
			tooltip = (
				<ReactTooltip
					id={voteElementId}
					type='info'
					className='login-tooltip'
				>{voteText}</ReactTooltip>
			)
		}

		return (
			<div className={scoreClass}>
				{/* span wrapper is to allow tooltip on disabled button */}
				<span data-tip data-for={voteElementId}>
					{voteElement}
				</span>
				{tooltip}
			</div>
		);
	}

	getClassName( mode ) {
		let className = 'ww-score-vote ww-score-' + mode;

		// @todo make this function pure
		if ( this.props.myvote == mode ) {
			className += ' ww-myvote';
		}

		return className;
	}
}
