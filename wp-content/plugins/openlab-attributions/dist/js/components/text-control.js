/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import Help from './help';

function TextControl( {
	label,
	value,
	help,
	id,
	name,
	onChange,
	type = 'text',
	required = false,
	isInline = false,
	...props
} ) {
	const isRequired = required && ! value;

	return (
		<div className={ classnames( 'form-group', { inline: isInline } ) }>
			<label htmlFor={ id }>
				{ label } { ! isInline && <Help text={ help } /> }
			</label>
			<input
				id={ id }
				name={ name }
				className="form-control"
				type={ type }
				value={ value }
				onChange={ onChange }
				required={ isRequired }
				{ ...props }
			/>
			{ isRequired && (
				<p id={ id + '__help' } className="form-control__help">
					{ `Please add ${ label }.` }
				</p>
			) }
		</div>
	);
}

export default TextControl;
