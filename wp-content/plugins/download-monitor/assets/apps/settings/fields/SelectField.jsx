import { decode } from 'he';
import { SelectControl } from '@wordpress/components';
import htmlHelp from './htmlHelp';

export default function SelectField( { field, value, onChange, disabled } ) {
	const options = Object.entries( field.options || {} ).map( ( [ optValue, label ] ) => ( {
		value: optValue,
		label: decode( label ),
	} ) );

	return (
		<SelectControl
			label={ field.label }
			help={ htmlHelp( field.desc ) }
			value={ value || '' }
			options={ options }
			disabled={ disabled }
			onChange={ onChange }
		/>
	);
}
