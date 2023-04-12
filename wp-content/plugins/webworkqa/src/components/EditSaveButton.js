import React, { Component } from 'react'
import { __ } from '@wordpress/i18n';

export default class EditSaveButton extends Component {
	render() {
		const { isPending, onClick } = this.props

		let buttonText = __( 'Save', 'webworkqa' )
		if ( isPending ) {
			buttonText = __( 'Saving...', 'webworkqa' )
		}

		return (
			<button
				className="button edit-save-button"
				onClick={onClick}
				type="submit"
			>
				{buttonText}
			</button>
		)
	}
}
