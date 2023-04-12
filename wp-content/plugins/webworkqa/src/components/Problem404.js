import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';

export default class Problem extends Component {
	render() {
		return (
			<div>
				{ __( 'No problem found with that ID.', 'webworkqa' ) }
			</div>
		)
	}
}
