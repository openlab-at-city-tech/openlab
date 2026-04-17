/* global openlabBlocksPostVisibility */

import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import './style.scss';

const PrePublicationPrivacy = () => {
	const { blogPublic, prePubShareOnlyWithGroup } = openlabBlocksPostVisibility;
	const blogPublicInt = parseInt( blogPublic );

	const { postVisibility } = useSelect( ( select ) => {
		const postMeta = select( 'core/editor' ).getEditedPostAttribute( 'meta' );
		const defaultVisibility = blogPublicInt >= 0 ? 'default' : 'members-only';

		return {
			postVisibility: postMeta.openlab_post_visibility || defaultVisibility,
		};
	}, [ blogPublicInt ] );

	const getBlogPublicMessage = () => {
		switch ( blogPublicInt ) {
			case 1:
				return __( 'You are publishing on a public site. It may be included in web search results.', 'wds-citytech' );
			case 0:
				return __( 'You are publishing on a public site. Search engines are asked not to include this site in web search results.', 'wds-citytech' );
			case -1:
				return __( 'You are publishing on an OpenLab only site. It is visible only to registered members of City Tech OpenLab.', 'wds-citytech' );
			case -2:
				return __( 'You are publishing on a private site. It is visible only to registered members of this site.', 'wds-citytech' );
			case -3:
				return __( 'You are publishing on a hidden site. It is visible only to site administrators.', 'wds-citytech' );
			default:
				return '';
		}
	};

	const getPostVisibilityMessage = () => {
		switch ( postVisibility ) {
			case 'default':
			default:
				return __( 'Everyone who can view this site can see this post. You can change the post visibility settings below.', 'wds-citytech' );
			case 'group-members-only':
				return prePubShareOnlyWithGroup;
			case 'members-only':
				return __( 'Only logged-in OpenLab members can see this post. This will override the Public visibility setting above. You can change the post visibility settings below.', 'wds-citytech' );
		}
	}

	return (
		<PluginPrePublishPanel
			className="openlab-pre-publication-privacy-panel"
			initialOpen={ true }
			title={ __( 'Visibility Status Alert', 'wds-citytech' ) }
		>
			<p>{ getBlogPublicMessage() }</p>
			<p>{ getPostVisibilityMessage() }</p>
		</PluginPrePublishPanel>
	);
};

registerPlugin( 'openlab-pre-publication-privacy', {
	render: PrePublicationPrivacy,
} );
