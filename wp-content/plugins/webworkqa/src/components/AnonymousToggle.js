import React, { Component } from 'react'
import { __ } from '@wordpress/i18n';

export default class AnonymousToggle extends Component {
	render() {
		const { onChange, isAnonymous } = this.props

		return (
			<div className="anonymous-toggle">
				<input
					checked={isAnonymous}
					id="anonymous-toggle-checkbox"
					onChange={onChange}
					type="checkbox"
					value="1"
				/>

				<label htmlFor="anonymous-toggle-checkbox">{ __( 'Post this question anonymously. Only your professor will see your name.', 'webworkqa' ) }</label>
			</div>
		)
	}
}
