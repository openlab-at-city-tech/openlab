import { ToggleControl } from '@wordpress/components';
import htmlHelp from './htmlHelp';

export default function ToggleField( { field, value, onChange, disabled } ) {
	return (
		<ToggleControl
			label={ htmlHelp( field.cb_label || field.label ) }
			help={ htmlHelp( field.desc ) }
			checked={ '1' === value || true === value }
			disabled={ disabled }
			onChange={ ( checked ) => onChange( checked ? '1' : '0' ) }
		/>
	);
}
