/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import { default as Help } from './icon-button-help';

function TextControl( {
	label,
	value,
	help,
	id,
	name,
	className = '',
	onChange,
	type = 'text',
	...props
} ) {
	const isInline = className === 'inline';

	return (
		<div className={ classnames( 'form-group', className ) }>
			<label htmlFor={ id }>
				{ label } { ! isInline && <Help text={ help } /> }
			</label>
			<input
				className="form-control"
				type={ type }
				id={ id }
				name={ name }
				value={ value }
				onChange={ onChange }
				{ ...props }
			/>
		</div>
	);
}

export default TextControl;
