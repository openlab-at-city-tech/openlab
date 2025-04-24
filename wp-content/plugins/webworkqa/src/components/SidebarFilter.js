import React, { Component } from 'react'
import Select from 'react-select'

export default class SidebarFilter extends Component {
	render() {
		const { slug, name } = this.props

		const contentContainerId = slug + '-content'
		const filterContent = this.getFilterContent()
		const filterId = 'filter-' + slug
		const labelId = filterId + '-label'

		return (
			<li>
				<div
					id={contentContainerId}
					className="filter-content"
				>
					<label 
					  id={labelId}
						htmlFor={filterId} 
						className="screen-reader-text"
					>{name}</label>
					
					{filterContent}
				</div>
			</li>
		)
	}

	getHeaderClassName() {
		const { type, value } = this.props

		let classNames = []

		switch ( type ) {
			case 'toggle' :
			case 'dropdown' :
				if ( value ) {
					classNames.push( 'toggle-enabled' )
				}
				break

			default: break;
		}

		return classNames.join( ' ' )
	}

	getFilterContent() {
		const {
			name, slug, value, options,
			onFilterChange
		} = this.props

		let optionElements = []
		if ( 'undefined' === typeof ( options ) || ! options.length ) {
			return optionElements
		}

		let selectData = []

		let option = {}
		for ( var i = 0; i < options.length; i++ ) {
			option = options[ i ]
			selectData.push( {
				value: option.value,
				label: option.name
			} )
		}

		const filterName = 'filter-' + slug
		const labelId = filterName + '-label'

		return (
			<Select
				autoBlur={true}
				aria-labelledby={labelId}
				id={filterName}
				name={filterName}
				value={value}
				onChange={onFilterChange}
				options={selectData}
				placeholder={name}
				clearable={false}
			/>
		)
	}
}
