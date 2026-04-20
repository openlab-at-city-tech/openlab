/* global openlabBlocksPostVisibility */

import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import './style.scss';

const WarningIcon = () => (
	<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
		<path
			fillRule="evenodd"
			clipRule="evenodd"
			d="M12.218 5.377a.25.25 0 0 0-.436 0l-7.29 12.96a.25.25 0 0 0 .218.373h14.58a.25.25 0 0 0 .218-.372l-7.29-12.96Zm-1.743-.735c.669-1.19 2.381-1.19 3.05 0l7.29 12.96a1.75 1.75 0 0 1-1.525 2.608H4.71a1.75 1.75 0 0 1-1.525-2.608l7.29-12.96ZM12.75 17.46h-1.5v-1.5h1.5v1.5Zm-1.5-3h1.5v-5h-1.5v5Z"
		/>
	</svg>
);

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
			icon={ <WarningIcon /> }
			initialOpen
			title={ __( 'Visibility Status Alert', 'wds-citytech' ) }
		>
			<p><strong>Site:</strong> { getBlogPublicMessage() }</p>
			<p><strong>Post:</strong> { getPostVisibilityMessage() }</p>
		</PluginPrePublishPanel>
	);
};

registerPlugin( 'openlab-pre-publication-privacy', {
	render: PrePublicationPrivacy,
} );
