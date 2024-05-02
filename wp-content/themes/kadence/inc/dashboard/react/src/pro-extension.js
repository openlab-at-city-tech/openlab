/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { withFilters } from '@wordpress/components';
const lockIcon = <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
<path d="M34 23h-2v-4c0-3.9-3.1-7-7-7s-7 3.1-7 7v4h-2v-4c0-5 4-9 9-9s9 4 9 9v4z"></path>
<path d="M33 40H17c-1.7 0-3-1.3-3-3V25c0-1.7 1.3-3 3-3h16c1.7 0 3 1.3 3 3v12c0 1.7-1.3 3-3 3zM17 24c-.6 0-1 .4-1 1v12c0 .6.4 1 1 1h16c.6 0 1-.4 1-1V25c0-.6-.4-1-1-1H17z"></path>
<circle cx="25" cy="28" r="2"></circle>
<path d="M25.5 28h-1l-1 6h3z"></path>
</svg>;
/**
 * Internal block libraries
 */
import map from 'lodash/map';

export const ProModules = () => {
	const proLinks = [
		{
			title: __( 'Header Addons', 'kadence' ),
			description: __( 'Adds 19 elements to the header builder.', 'kadence' ),
			setting: 'header_addon',
		},
		{
			title: __( 'Conditional Headers', 'kadence' ),
			description: __( 'Build Extra Headers to display conditionally.', 'kadence' ),
			setting: 'conditional_headers',
		},
		{
			title: __( 'Ultimate Menu', 'kadence' ),
			description: __( 'Adds menu options for mega menus, highlight tags, icons and more.', 'kadence' ),
			setting: 'mega_menu',
		},
		{
			title: __( 'Header/Footer Scripts', 'kadence' ),
			description: __( 'Adds Options into the customizer to add header and footer scripts', 'kadence' ),
			setting: 'scripts',
		},
		{
			title: __( 'Hooked Elements', 'kadence' ),
			description: __( 'Add content anywhere into your site conditionally.', 'kadence' ),
			setting: 'hooks',
		},
		{
			title: __( 'WooCommerce Addons', 'kadence' ),
			description: __( 'Adds new options into the customizer for WooCommerce stores.', 'kadence' ),
			setting: 'woocommerce',
		},
		{
			title: __( 'Infinite Scroll', 'kadence' ),
			description: __( 'Adds Infinite Scroll for archives.', 'kadence' ),
			setting: 'infinite_scroll',
		},
		{
			title: __( 'Color Palette Switch (Dark Mode)', 'kadence' ),
			description: __( 'Adds a color palette switch so you can create a "dark" mode for your website.', 'kadence' ),
			setting: 'dark_mode',
		},
		{
			title: __( 'Local Gravatars', 'kadence' ),
			description: __( 'Loads Gravatars from your servers to improve site performance.', 'kadence' ),
			setting: 'local_gravatars',
		},
		{
			title: __( 'Archive Custom Page Title Backgrounds', 'kadence' ),
			description: __( 'Allows you to assign a custom image for a taxonomy background.', 'kadence' ),
			setting: 'archive_custom',
		},
	];
	return (
		<>
			<h2 className="section-header">{ __( 'Do more with the Kadence Pro Addon', 'kadence' ) }</h2>
			<div className="two-col-grid">
				{ map( proLinks, ( link ) => {
					return (
						<div className="link-item locked-item">
							<span className="lock-icon">{ lockIcon }</span>
							<h4>{ link.title }</h4>
							<p>{ link.description }</p>
							<div className="link-item-foot">
								<a href={ `${kadenceDashboardParams.proURL}&utm_campaign=${ link.setting }` } target="_blank">
									{ __( 'Learn More', 'kadence') }
								</a>
							</div>
						</div>
					);
				} ) }
			</div>
		</>
	);
};

export default withFilters( 'kadence_theme_pro_modules' )( ProModules );