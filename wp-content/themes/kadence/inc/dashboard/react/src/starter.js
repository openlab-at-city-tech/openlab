/**
 * Activate a plugin
 *
 * @return void
 */
function kadence_starter_activatePlugin() {
	var data = new FormData();
	data.append( 'action', 'kadence_install_starter' );
	data.append( 'security', kadenceDashboardParams.ajax_nonce );
	data.append( 'status', kadenceDashboardParams.status );
	jQuery.ajax({
		method:      'POST',
		url:         kadenceDashboardParams.ajax_url,
		data:        data,
		contentType: false,
		processData: false,
	})
	.done( function( response, status, stately ) {
		if ( response.success ) {
			location.replace( kadenceDashboardParams.starterURL );
		}
	})
	.fail( function( error ) {
		console.log( error );
	});
}
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect, Fragment } from '@wordpress/element';
const { withFilters, TabPanel, Panel, PanelBody, PanelRow, Button, Spinner } = wp.components;
export const StarterTab = () => {
	const [ working, setWorking ] = useState( null );
	const handleClick = () => {
		setWorking( true );
		kadence_starter_activatePlugin();
	};
	return (
		<Fragment>
			<div className="kadence-desk-starter-inner" style={{ margin: '20px auto', textAlign:'center' }}>
				<h2>{ __( 'Starter Templates', 'kadence' ) }</h2>
				<p>{ __( 'Create and customize professionally designed websites in minutes. Simply choose your template, choose your colors, and import. Done!', 'kadence' ) }</p>
				<div className="image-container">
					<img width="772" height="250" alt={ __( 'Starter Templates', 'kadence' ) } src={ kadenceDashboardParams.starterImage } />
				</div>
				{ kadenceDashboardParams.starterTemplates && (
					<a
						className="kt-action-starter kadence-desk-button"
						href={ kadenceDashboardParams.starterURL }
					>
						{ kadenceDashboardParams.starterLabel }
					</a>
				) }
				{ ! kadenceDashboardParams.starterTemplates && (
					<Button 
						className="kt-action-starter kadence-desk-button"
						onClick={ () => handleClick() }
					>
						{ kadenceDashboardParams.starterLabel }
						{ working && (
							<Spinner />
						) }
					</Button>

				) }
			</div>
		</Fragment>
	);
};

export default withFilters( 'kadence_theme_starters' )( StarterTab );