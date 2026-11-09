import { TextareaControl } from '@wordpress/components';
import htmlHelp from './htmlHelp';

export default function TextareaField( { field, value, onChange, disabled } ) {
	return (
		<TextareaControl
			label={ field.label }
			help={ htmlHelp( field.desc ) }
			value={ value || '' }
			disabled={ disabled }
			onChange={ onChange }
		/>
	);
}
