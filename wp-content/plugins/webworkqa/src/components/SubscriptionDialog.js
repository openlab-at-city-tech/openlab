import React, { Component } from 'react'
import { __ } from '@wordpress/i18n';

export default class SubscriptionDialog extends Component {
	constructor() {
		super()
		this.state = { isHover: false }
	}

	onMouseover() {
		this.setState( { isHover: true } )
	}

	onMouseout() {
		this.setState( { isHover: false } )
	}


	render() {
		const { isSubscribed, onClick } = this.props
		const { isHover } = this.state

		let buttonText
		if ( isSubscribed ) {
			buttonText = isHover ? __( 'Unfollow', 'webworkqa' ) : __( 'Following', 'webworkqa' )
		} else {
			buttonText = __( 'Follow', 'webworkqa' )
		}

		let iconClass = 'hover-notice-parent subscription-dialog '
		let screenReaderText = ''
		if ( isSubscribed ) {
			iconClass += 'subscribed'
			screenReaderText = __( 'End email notifications.', 'webworkqa' )
		} else {
			iconClass += 'unsubscribed'
			screenReaderText = __( 'Notify me of future replies to this question.', 'webworkqa' )
		}

		return (
			<button
				aria-label={screenReaderText}
				className={iconClass}
				onClick={onClick}
				onMouseEnter={this.onMouseover.bind(this)}
				onMouseLeave={this.onMouseout.bind(this)}
			>
				<span className="screen-reader-text">{ screenReaderText }</span>
				{buttonText}
				<i className="fa"></i>
				<div aria-hidden="true" className="hover-notice subscription-notice">
					{screenReaderText}
				</div>
			</button>
		)
	}
}
