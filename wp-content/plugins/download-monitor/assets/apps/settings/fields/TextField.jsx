import { TextControl } from '@wordpress/components';
import htmlHelp from './htmlHelp';

export default function TextField( { field, value, onChange, disabled } ) {
	return (
		<TextControl
			label={ field.label }
			help={ htmlHelp( field.desc ) }
			placeholder={ field.placeholder }
			value={ value || '' }
			disabled={ disabled }
			onChange={ onChange }
		/>
	);
}
