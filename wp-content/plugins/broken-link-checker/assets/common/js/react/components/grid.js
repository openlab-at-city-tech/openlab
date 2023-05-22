import React from 'react';

export function Grid( props ) {
	let classes = '';
	let id = props.id !== undefined ? props.id : '';

	if ( props.classes !== undefined ) {
		classes = props.classes;
	}

	return (
		<div
			{... id ? {id} : {}}
			className={`sui-grid ${classes}`}
		>
			{props.children}
		</div>
	);
}

export function Row( props ) {
	let classes = '';

	if ( props.classes !== undefined ) {
		classes = props.classes;
	}

	return (
		<div className={`sui-row ${classes}`}>
			{props.children}
		</div>
	);
}

export function Column( props ) {
	let classes = '';

	if ( props.classes !== undefined ) {
		classes = props.classes;
	}

	return (
		<div className={`sui-col ${classes}`}>
			{props.children}
		</div>
	);
}