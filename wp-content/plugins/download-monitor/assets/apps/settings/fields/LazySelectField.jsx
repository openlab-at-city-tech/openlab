import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { ComboboxControl } from '@wordpress/components';
import htmlHelp from './htmlHelp';

export default function LazySelectField( { field, value, onChange, disabled } ) {
	const [ options, setOptions ] = useState( [] );

	useEffect( () => {
		apiFetch( { path: '/download-monitor/v1/settings-pages' } ).then( setOptions );
	}, [] );

	return (
		<ComboboxControl
			label={ field.label }
			help={ htmlHelp( field.desc ) }
			value={ value ? String( value ) : '' }
			options={ options.map( ( option ) => ( { value: String( option.value ), label: option.label } ) ) }
			disabled={ disabled }
			onChange={ onChange }
			allowReset
		/>
	);
}
