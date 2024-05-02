/**
 * WordPress dependencies
 */
 import { __ } from '@wordpress/i18n';
const { Fragment } = wp.element;
const { withFilters } = wp.components;
const { TabPanel, Panel, PanelBody } = wp.components;
import ChangelogItem from './changelog-item';

export const ChangelogTab = () => {
	const tabs = [
		{
			name: 'kadence',
			title: __( 'Changelog', 'kadence' ),
			className: 'kadence-changelog-tab',
		},
		{
			name: 'pro',
			title: __( 'Pro Changelog', 'kadence' ),
			className: 'kadence-pro-changelog-tab',
		},
	];
	return (
		<Fragment>
			{ kadenceDashboardParams.changelog && (
				<Fragment>
					{ kadenceDashboardParams.proChangelog && kadenceDashboardParams.proChangelog.length && (
						<TabPanel className="kadence-dashboard-changelog-tab-panel"
							activeClass="active-tab"
							tabs={ tabs }>
							{
								( tab ) => {
									switch ( tab.name ) {
										case 'kadence':
											return (
												<Panel className="kadence-changelog-section tab-section">
													<PanelBody
														opened={ true }
													>
														{ kadenceDashboardParams.changelog.map( ( item, index ) => {
															return <ChangelogItem
																item={ item }
																index={ item }
															/>;
														} ) }
													</PanelBody>
												</Panel>
											);

										case 'pro':
											return (
												<Panel className="pro-changelog-section tab-section">
													<PanelBody
														opened={ true }
													>
														{ kadenceDashboardParams.proChangelog.map( ( item, index ) => {
															return <ChangelogItem
																item={ item }
																index={ item }
															/>;
														} ) }
													</PanelBody>
												</Panel>
											);
									}
								}
							}
						</TabPanel>
					) }
					{ ( '' == kadenceDashboardParams.proChangelog || ( Array.isArray( kadenceDashboardParams.proChangelog ) && ! kadenceDashboardParams.proChangelog.length ) ) && (
						<Fragment>
							{ kadenceDashboardParams.changelog.map( ( item, index ) => {
								return <ChangelogItem
									item={ item }
									index={ item }
								/>;
							} ) }
						</Fragment>
					) }
				</Fragment>
			) }
		</Fragment>
	);
};

export default withFilters( 'kadence_theme_changelog' )( ChangelogTab );