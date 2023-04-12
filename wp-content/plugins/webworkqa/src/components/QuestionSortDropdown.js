import React, { Component } from 'react'
import Select from 'react-select'
import { __ } from '@wordpress/i18n';

export default class QuestionSortDropdown extends Component {
	render() {
		const { orderby, onSortChange } = this.props

		const options = [
			{ value: 'post_date', label: __( 'Most Recent', 'webworkqa' ) },
			{ value: 'response_count', label: __( 'Most Replies', 'webworkqa' ) },
			{ value: 'votes', label: __( 'Most Votes', 'webworkqa' ) }
		]

		return (
			<div className='question-sort'>
				<label
					className='screen-reader-text'
				  htmlFor='question-sort'
					id='question-sort-label'
				>{ __( 'Sort results by', 'webworkqa' ) }</label>

				<Select
					aria-labelledby='question-sort-label'
				  autoBlur={true}
				  id='question-sort'
				  name='question-sort'
				  value={orderby}
				  onChange={onSortChange}
				  options={options}
				  clearable={false}
				/>
			</div>
		)
	}
}
