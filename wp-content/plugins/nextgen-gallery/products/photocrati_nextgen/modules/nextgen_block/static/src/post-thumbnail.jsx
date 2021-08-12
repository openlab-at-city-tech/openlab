import React from "react";
import NGGFeaturedImage from './components/ngg-post-thumbnail.jsx';

const {select}   = wp.data;
const {Fragment} = wp.element

function setFeaturedImageDisplay(OriginalComponent) {
    return (props) => {
        const meta = select('core/editor').getCurrentPostAttribute('meta')
        const nggFeaturedImage = meta ? <NGGFeaturedImage {...props} meta={meta}/> : null
        return (
            <Fragment>
                <OriginalComponent {...props}/>
                {nggFeaturedImage}
            </Fragment>
        );
    }
}

wp.hooks.addFilter('editor.PostFeaturedImage', 'imagely/featured-image-display', setFeaturedImageDisplay);