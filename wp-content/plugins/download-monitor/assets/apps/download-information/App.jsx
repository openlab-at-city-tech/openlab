import { applyFilters } from '@wordpress/hooks';
import CopyField from './components/CopyField';

export default function App() {
	const data = window.dlmDownloadInformation || {};
	const strings = data.strings || {};

	const baseFields = [
		{
			key: 'shortcode',
			render: () => (
				<CopyField
					label={ strings.shortcode }
					value={ data.shortcode }
					copyLabel={ strings.copy }
					copiedLabel={ strings.copied }
				/>
			),
		},
		{
			key: 'url',
			render: () => (
				<CopyField
					label={ strings.url }
					value={ data.url }
					copyLabel={ strings.copy }
					copiedLabel={ strings.copied }
				/>
			),
		},
	];

	const fields = applyFilters( 'dlm.downloadInformation.fields', baseFields, {
		downloadId: data.downloadId,
	} );

	return (
		<>
			{ fields.map( ( field ) => (
				<div key={ field.key } className="dlm-info-field">
					{ field.render() }
				</div>
			) ) }
		</>
	);
}
