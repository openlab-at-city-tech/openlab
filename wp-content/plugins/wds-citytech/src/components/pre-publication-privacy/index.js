import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';

import './style.scss';

registerPlugin(
	'openlab-pre-publication-privacy',
	{
		render: () => {

			const { blogPublic } = window.openlabBlocks
			const blogPublicMessage = () => {
				switch ( blogPublic ) {
					case '1' :
					case '0' :
						return __( 'You are publishing on a public site.', 'wds-citytech' );
				}
			}

			const currentPostPostStatus = select( 'core/editor' ).getCurrentPost().status;
			console.log(currentPostPostStatus);

			return (

				<PluginPrePublishPanel
					className="openlab-pre-publication-privacy-panel"
					initialOpen={ true }
					title="Visibility Status Alert"
				>
					<ul>
						<li>{ blogPublicMessage() }</li>

						<li>
						</li>
					</ul>
				</PluginPrePublishPanel>
			)
		},
	}

)
