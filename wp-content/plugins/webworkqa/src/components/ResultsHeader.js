import React, { Component } from 'react'
import { __ } from '@wordpress/i18n';

export default class ResultsHeader extends Component {
	render() {
		const { currentFilters } = this.props

		const displayFilters = [ 'course', 'problemSet' ]

		let active = []
		let filterName = ''
		for ( let i = 0; i < displayFilters.length; i++ ) {
			filterName = displayFilters[ i ]
			if ( currentFilters[ filterName ] ) {
				active.push( currentFilters[ filterName ] )
			}
		}

		let breadcrumbs = ''
		if ( active.length ) {
			const crumbs = active.join( ' / ' )
			breadcrumbs = (
				<div className="results-breadcrumbs">
					<span className="results-breadcrumbs-label">{ __( 'Filtered by:', 'webworkqa' ) }</span> {crumbs}
				</div>
			)
		}

		return (
			<div className="results-header">
				<h2 className="ww-header">{ __( 'Questions', 'webworkqa' ) }</h2>
				{breadcrumbs}
			</div>
		)
	}
}
