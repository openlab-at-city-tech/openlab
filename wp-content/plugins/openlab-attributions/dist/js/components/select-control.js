/**
 * Internal dependencies
 */
import { default as Help } from './icon-button-help';

function SelectControl( {
	label,
	value,
	help,
	id,
	name,
	onChange,
	options = [],
} ) {
	// Disable reason: onBlur resets the value.
	/* eslint-disable jsx-a11y/no-onchange */
	return (
		<div className="form-group">
			<label htmlFor={ id }>
				{ label } <Help text={ help } />
			</label>
			<select
				id={ id }
				name={ name }
				className="form-control"
				value={ value }
				onChange={ onChange }
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
		</div>
	);
}

export default SelectControl;
