import React from "react";
import NGGFeaturedImage from './components/ngg-post-thumbnail.jsx';

const {select}   = wp.data;
const {Fragment} = wp.element

function setFeaturedImageDisplay(OriginalComponent) {
    return (props) => {
        const meta = select('core/editor').getCurrentPostAttribute('meta');
        return (
            <Fragment>
                <OriginalComponent {...props}/>
                <NGGFeaturedImage {...props} meta={meta}/>
            </Fragment>
        );
    }
}

wp.hooks.addFilter('editor.PostFeaturedImage', 'imagely/featured-image-display', setFeaturedImageDisplay);