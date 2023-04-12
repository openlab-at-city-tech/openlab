import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';

export default class Uploader extends Component {
	render() {
		const { onUploadClick } = this.props

		return (
			<button
					className="question-form-upload-button"
					onClick={function(e){
						e.preventDefault()
						onUploadClick()
					}}
				>{ __( 'Add Images', 'webworkqa' ) } <i className="fa fa-upload"></i>
			</button>
		)
	}
}
