/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
const { Fragment } = wp.element;

export const RecommendedTab = () => {
	return (
		<Fragment>
			<p>{ __( 'This area is for Recommended Plugins.', 'kadence' ) }</p>
		</Fragment>
	);
};

export default RecommendedTab;