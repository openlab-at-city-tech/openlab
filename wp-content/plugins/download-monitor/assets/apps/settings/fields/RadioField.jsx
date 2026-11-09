import { RadioControl } from '@wordpress/components';
import htmlHelp from './htmlHelp';

export default function RadioField( { field, value, onChange, disabled } ) {
	const options = Object.entries( field.options || {} ).map( ( [ optValue, label ] ) => ( {
		value: optValue,
		label,
	} ) );

	return (
		<RadioControl
			label={ field.label }
			help={ htmlHelp( field.desc ) }
			selected={ value || field.std || '' }
			options={ options }
			disabled={ disabled }
			onChange={ onChange }
		/>
	);
}
