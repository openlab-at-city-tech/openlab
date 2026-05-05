/* global openlabBlocksPostVisibility */

import { Fragment } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import PostSharingOptionsContent from '../post-sharing-options/PostSharingOptionsContent';

import './style.scss';

import WarningIcon from './warning-icon';

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
		if ( blogPublicInt <= -2 ) {
			return __( 'Everyone who can view this site can see this post.', 'wds-citytech' );
		}

		switch ( postVisibility ) {
			case 'default':
			default:
				return __( 'Everyone who can view this site can see this post. You can change the post visibility settings below.', 'wds-citytech' );
			case 'group-members-only':
				return prePubShareOnlyWithGroup;
			case 'members-only':
				return __( 'Only logged-in OpenLab members can see this post. This will override the site visibility setting. You can change the post visibility settings below.', 'wds-citytech' );
		}
	}

	const getVisibilityOptionsPanel = () => {
		if ( blogPublicInt >= -1 ) {
			return (
				<PluginPrePublishPanel
					className="openlab-pre-publication-visibility-panel"
					initialOpen
					title={ __( 'More visibility options', 'wds-citytech' ) }
					icon={ <WarningIcon /> }
				>
					<PostSharingOptionsContent instanceId="pre-publish-sharing-options" />
				</PluginPrePublishPanel>
			)
		}

		return null;
	};

	return (
		<Fragment>
			<PluginPrePublishPanel
				className="openlab-pre-publication-privacy-panel"
				icon={ <WarningIcon /> }
				initialOpen
				title={ __( 'Visibility Status Check', 'wds-citytech' ) }
			>
				<p><strong>Site:</strong> { getBlogPublicMessage() }</p>
				<p><strong>Post:</strong> { getPostVisibilityMessage() }</p>
			</PluginPrePublishPanel>
			{ getVisibilityOptionsPanel() }
		</Fragment>
	);
};

registerPlugin( 'openlab-pre-publication-privacy', {
	render: PrePublicationPrivacy,
} );
