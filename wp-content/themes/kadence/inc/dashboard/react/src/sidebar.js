/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
const { Fragment } = wp.element;
const { withFilters, TabPanel, Panel, PanelBody, PanelRow, Button } = wp.components;
export const Sidebar = () => {
	return (
		<Fragment>
			<Panel className="community-section sidebar-section">
				<PanelBody
					opened={ true }
				>
					<h2>{ __( 'Web Creators Community', 'kadence' ) }</h2>
					<p>{ __( 'Join our community of fellow kadence users creating effective websites! Share your site, ask a question and help others.', 'kadence' ) }</p>
					<a href="https://www.facebook.com/groups/webcreatorcommunity" target="_blank" class="sidebar-link">{ __( 'Join our Facebook Group', 'kadence' ) }</a>
				</PanelBody>
			</Panel>
			<Panel className="support-section sidebar-section">
				<PanelBody
					opened={ true }
				>
					<h2>{ __( 'Support', 'kadence' ) }</h2>
					<p>{ __( 'Have a question, we are happy to help! Get in touch with our support team.', 'kadence' ) }</p>
					<a href="https://www.kadencewp.com/free-support/" target="_blank" class="sidebar-link">{ __( 'Submit a Ticket', 'kadence' ) }</a>
				</PanelBody>
			</Panel>
		</Fragment>
	);
};

export default withFilters( 'kadence_theme_sidebar' )( Sidebar );