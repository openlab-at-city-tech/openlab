/* global openlabBlocksPostVisibility */

import { VisuallyHidden } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { select, useDispatch, useSelect } from '@wordpress/data';

const PostSharingOptions = () => {
  const { blogPublic, shareOnlyWithGroup } = openlabBlocksPostVisibility;
  const { editPost } = useDispatch( 'core/editor' );

  const blogPublicInt = parseInt( blogPublic );

  const { postVisibility } = useSelect( ( selectObj ) => {
    const postMeta = selectObj( 'core/editor' ).getEditedPostAttribute( 'meta' );
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
      label: 'Site Members',
      info: shareOnlyWithGroup + ' ' + publicOverrideString,
    },
    {
      value: 'members-only',
      label: 'OpenLab members only',
      info: 'Only logged-in OpenLab members can see this post. ' + publicOverrideString,
    },
  ];

  if ( blogPublicInt >= 0 ) {
    visibilityOptions.push({
      value: 'default',
      label: 'Everyone',
      info: 'Everyone who can view this site can see this post.',
    });
  }

  return (
    <PluginDocumentSettingPanel
      name="post-sharing-options"
      title="More visibility options"
      className="post-sharing-options"
    >
      <fieldset className="editor-post-visibility__fieldset">
        <VisuallyHidden as="legend">Sharing</VisuallyHidden>

        <p>Control who can see this post.</p>

        { visibilityOptions.map( ( option ) => (
          <PostSharingChoice
            key={ option.value }
            instanceId="post-sharing-options"
            value={ option.value }
            label={ option.label }
            info={ option.info }
            onChange={ ( event ) => onChange( event.target.value ) }
            checked={ postVisibility === option.value }
          />
        )) }
      </fieldset>
    </PluginDocumentSettingPanel>
  );
};

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

const registerPostVisibility = () => {
  const post = select( 'core/editor' ).getCurrentPost();
  if ( post && post.id ) {
    registerPlugin(
      'post-sharing-options',
      { render: PostSharingOptions }
    );
  }
};

wp.domReady( registerPostVisibility );
