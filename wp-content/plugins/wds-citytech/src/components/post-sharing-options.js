import { RadioControl, VisuallyHidden } from '@wordpress/components'
import { PluginDocumentSettingPanel } from '@wordpress/edit-post'
import { registerPlugin } from '@wordpress/plugins'
import { useDispatch, useSelect } from '@wordpress/data'

const PostSharingOptions = ({}) => {
	const { currentGroupTypeSiteLabel, shareOnlyWithGroup, siteIsPublic } = openlabBlocksPostVisibility

	if ( ! siteIsPublic ) {
		return null
	}

	const { editPost } = useDispatch( 'core/editor' )

	const onChange = ( value ) => {
		editPost( { meta: { 'openlab_post_visibility': value } } )
	}

	const { postVisibility } = useSelect( ( select ) => {
		const postMeta = select( 'core/editor' ).getEditedPostAttribute( 'meta' )

		return {
			postVisibility: postMeta['openlab_post_visibility'] || 'default'
		}
	} )

	return (
		<PluginDocumentSettingPanel
			name="post-sharing-options"
			title="Visibility Options"
			className="post-sharing-options"
		>
			<fieldset className="editor-post-visibility__fieldset">
				<VisuallyHidden as="legend">
					Sharing
				</VisuallyHidden>

				<p>{ 'Control the visibility of this post.' }</p>

				<PostSharingChoice
					instanceId="post-sharing-options"
					value="group-members-only"
					label={ currentGroupTypeSiteLabel }
					info={ shareOnlyWithGroup }
					onChange={ ( event ) => onChange( event.target.value ) }
					checked={ postVisibility === 'group-members-only' }
				/>

				<PostSharingChoice
					instanceId="post-sharing-options"
					value="members-only"
					label="OpenLab members only"
					info="Share only with logged in members of the OpenLab."
					onChange={ ( event ) => onChange( event.target.value ) }
					checked={	postVisibility === 'members-only' }
				/>

				<PostSharingChoice
					instanceId="post-sharing-options"
					value="default"
					label="Public"
					info="Share according to site visibility settings."
					onChange={ ( event ) => onChange( event.target.value ) }
					checked={ postVisibility === 'default' }
				/>
			</fieldset>
		</PluginDocumentSettingPanel>
	)
}

function PostSharingChoice( { instanceId, value, label, info, ...props } ) {
	return (
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
}

registerPlugin(
	'post-sharing-options',
	{ render: PostSharingOptions }
)
