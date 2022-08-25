import { __ } from '@wordpress/i18n';

export default function PluginAttribution() {
	return (
		<div className="attribution-plugin" dangerouslySetInnerHTML={ { __html: __( '&quot;OpenLab Attribution&quot; by the <a href="https://openlab.citytech.cuny.edu">City Tech OpenLab</a>, is licensed under <a href="https://creativecommons.org/licenses/by-nc-sa/3.0"> CC BY-NC-SA </a> / A derivative from <a href="https://www.openwa.org/attrib-builder">original work</a> by <a href="https://www.openwa.org">Open Washington</a>.', 'openlab-attributions' ) } }>
		</div>
	);
}
