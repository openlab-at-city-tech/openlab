/* global openlabBlocksPostVisibility */

import { VisuallyHidden } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';

export const PostSharingChoice = ( { instanceId, value, label, info, ...props } ) => (
	<div className="editor-post-visibility__choice">
		<input
			type="radio"
			name={ `editor-post-visibility__setting-${ instanceId }` }
			value={ value }
			id={ `editor-post-${ value }-${ instanceId }` }
			aria-describedby={ `editor-post-${ value }-${ instanceId }-description` }
			className="editor-post-visibility__radio"
			{ ...props }
		/>
		<label
			htmlFor={ `editor-post-${ value }-${ instanceId }` }
			className="editor-post-visibility__label"
		>
			{ label }
		</label>
		<p
			id={ `editor-post-${ value }-${ instanceId }-description` }
			className="editor-post-visibility__info"
		>
			{ info }
		</p>
	</div>
);

const PostSharingOptionsContent = ( { instanceId = 'post-sharing-options' } ) => {
	const { blogPublic, shareOnlyWithGroup } = openlabBlocksPostVisibility;
	const { editPost } = useDispatch( 'core/editor' );

	const blogPublicInt = parseInt( blogPublic );

	const { postVisibility } = useSelect( ( select ) => {
		const postMeta = select( 'core/editor' ).getEditedPostAttribute( 'meta' );
		const defaultVisibility = blogPublicInt >= 0 ? 'default' : 'members-only';

		return {
			postVisibility: postMeta.openlab_post_visibility || defaultVisibility,
		};
	}, [ blogPublicInt ] );

	if ( blogPublicInt < -1 ) {
		return null;
	}

	const onChange = ( value ) => {
		editPost( { meta: { 'openlab_post_visibility': value } } );
	};

	const publicOverrideString = 'This will override the Public visibility setting above.';

	const visibilityOptions = [
		{
			value: 'group-members-only',
			label: 'Site members only',
			info: shareOnlyWithGroup + ' ' + publicOverrideString,
		},
		{
			value: 'members-only',
			label: 'OpenLab members only',
			info: 'Only logged-in OpenLab members can see this post. ' + publicOverrideString,
		},
	];

	if ( blogPublicInt >= 0 ) {
		visibilityOptions.push( {
			value: 'default',
			label: 'Everyone',
			info: 'Everyone who can view this site can see this post.',
		} );
	}

	return (
		<fieldset className="editor-post-visibility__fieldset">
			<VisuallyHidden as="legend">Sharing</VisuallyHidden>

			<p>Control who can see this post.</p>

			{ visibilityOptions.map( ( option ) => (
				<PostSharingChoice
					key={ option.value }
					instanceId={ instanceId }
					value={ option.value }
					label={ option.label }
					info={ option.info }
					onChange={ ( event ) => onChange( event.target.value ) }
					checked={ postVisibility === option.value }
				/>
			) ) }
		</fieldset>
	);
};

export default PostSharingOptionsContent;
