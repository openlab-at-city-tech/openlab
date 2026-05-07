/* global openlabBlocksPostVisibility */

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { useSelect } from '@wordpress/data';

import PostSharingOptionsContent from './PostSharingOptionsContent';

const PostSharingOptions = () => (
	<PluginDocumentSettingPanel
		name="post-sharing-options"
		title="More visibility options"
		className="post-sharing-options"
	>
		<PostSharingOptionsContent />
	</PluginDocumentSettingPanel>
);

const OpenlabPostVisibilityPlugin = () => {
	const isSiteEditor = useSelect( ( select ) => {
		const editSite = select( 'core/edit-site' );
		return !! editSite;
	}, [] );

	const { blogPublic } = openlabBlocksPostVisibility;
	const blogPublicInt = parseInt( blogPublic );

	return blogPublicInt >= -1 && ! isSiteEditor && <PostSharingOptions />;
};

const registerPostVisibility = () => {
	registerPlugin( 'post-sharing-options', {
		render: OpenlabPostVisibilityPlugin,
		icon: 'visibility',
	} );
};

wp.domReady( registerPostVisibility );
