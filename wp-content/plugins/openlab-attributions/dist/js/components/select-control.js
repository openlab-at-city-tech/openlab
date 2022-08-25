/**
 * External dependencies
 */
import classnames from 'classnames';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Help from './help';

function SelectControl( {
	label,
	value,
	help,
	id,
	name,
	onChange,
	options = [],
	required = false,
	isInline = false,
} ) {
	const isRequired = required && ! value;

	// Disable reason: onBlur resets the value.
	/* eslint-disable jsx-a11y/no-onchange */
	return (
		<div className={ classnames( 'form-group', { inline: isInline } ) }>
			<label htmlFor={ id }>
				{ label } { ! isInline && <Help text={ help } /> }
			</label>
			<select
				id={ id }
				name={ name }
				className="form-control"
				value={ value }
				onChange={ onChange }
				required={ isRequired }
			>
				<option key="0" value="">
					Choose...
				</option>
				{ options.map( ( option, index ) => (
					<option
						key={ `${ option.label }-${ option.value }-${ index }` }
						value={ option.value }
					>
						{ option.label }
					</option>
				) ) }
			</select>
			{ isRequired && (
				<p id={ id + '__help' } className="form-control__help">
					{ sprintf( __( 'Please add %s', 'openlab-attributions' ), label ) }
				</p>
			) }
		</div>
	);
}

export default SelectControl;
