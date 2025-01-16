import React from 'react';
import { createInterpolateElement } from '@wordpress/element';

require( '@wpmudev/shared-ui/dist/js/_src/dropdowns' );

export default function Dropdown( props ) {
	const id = props.id !== undefined ? props.id : 'blc-dropdown';
	const classes = props.classes !== undefined ? props.classes : '';
	const imageClass = props.imageClass !== undefined ? props.imageClass : '';

	const label = props.label !== undefined ? props.label : '';
	const orientation =
		props.orientation !== undefined && 'right' === props.orientation
			? props.orientation
			: 'left';
	const dropdownData =
		props.dropdownData !== undefined ? props.dropdownData : '';

	/**
	 * The Drodown Item element.
	 * @param {*} item
	 * @return Object The dropdown item element.
	 */
	const dropdownElement = ( item ) => {
		let itemTag = 'span';
		const itemType = item.type !== 'undefined' ? item.type : 'plain';
		const callback = item.callback !== 'undefined' ? item.callback : '';
		// eslint-disable-next-line no-shadow
		const props = {
			className: item.classes !== undefined ? item.classes : '',
		};

		let content = item.content !== undefined ? item.content : '';

		if ( item.icon !== undefined ) {
			content = (
				<>
					<ItemIcon icon={ item.icon } /> { content }{ ' ' }
				</>
			);
		}

		switch ( itemType ) {
			case 'link':
				itemTag = 'a';
				props.href = item.href !== 'undefined' ? item.href : '';
				props.target = item.target !== 'undefined' ? item.target : '';
				break;
			case 'button':
				itemTag = 'button';
				props.onClick = callback;
				break;
			default:
				// eslint-disable-next-line no-unused-vars
				itemTag = 'span';
		}

		return React.createElement( itemTag, props, content );
	};

	const ItemIcon = ( { icon } ) => {
		return <i className={ icon }></i>;
	};

	/**
	 * Return the component
	 */
	return (
		<div
			{ ...( id ? { id } : {} ) }
			className={ `sui-dropdown sui-dropdown-${ orientation } ${ classes }` }
		>
			<button
				aria-expanded="false"
				className="sui-button-icon sui-dropdown-anchor"
				aria-label={ label }
			>
				{props.triggerComponent}
			</button>
			<ul>
				{ dropdownData.length > 0 &&
					dropdownData.map( ( dropdownItem, i ) => {
						return [
							<li
								key={ `${ dropdownItem.id }-title` }
								id={ `${ dropdownItem.id }-title` }
							>
								{ dropdownElement( dropdownItem ) }
							</li>,
						];
					} ) }
			</ul>
		</div>
	);
}
