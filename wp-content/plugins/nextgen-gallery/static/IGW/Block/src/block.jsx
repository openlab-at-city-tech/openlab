import NGGEditor from './edit.jsx'
import icons     from './icons.min';
import React     from 'react';

const { __ } 				= wp.i18n
const { RawHTML } 	        = wp.element
const { registerBlockType } = wp.blocks

// Register our block
registerBlockType('imagely/nextgen-gallery', {

    title: __('NextGEN Gallery'),

    description: __('A block for adding NextGEN Galleries.'),

    icon: icons.nextgen,

    category: 'common',

    attributes: {
        content: {
            type: 'string',
            source: 'html',
        },
    },

    supports: {
        className: false,
        customClassName: false,
    },

    edit({attributes, setAttributes}) {
        return <NGGEditor content={attributes.content}
                          onInsertGallery={(shortcode) => {
                              setAttributes({content: shortcode});
                          }}/>
    },

    save({ attributes }) {
        return attributes.content;
    }
});