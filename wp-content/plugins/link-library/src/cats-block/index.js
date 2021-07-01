import edit from './edit';

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { dateI18n, format, __experimentalGetSettings } from '@wordpress/date';
import { setState } from '@wordpress/compose';

registerBlockType( 'link-library/cats-block', {
    title: 'Link Library Categories',
    icon: 'admin-links',
    category: 'link-library',
    attributes: {
        settings: {
            type: 'string',
            default: '1',
        },
        categorylistoverride:{
            type: 'array',
            default: [],
        },
        excludecategoryoverride:{
            type: 'array',
            default: [],
        },
        taglistoverride:{
            type: 'array',
            default: [],
        },
        targetlibrary: {
            type: 'string',
            default: '',
        },
        categorylistoverrideCSV:{
            type: 'string',
            default: '',
        },
        excludecategoryoverrideCSV:{
            type: 'string',
            default: '',
        },
        taglistoverrideCSV:{
            type: 'string',
            default: '',
        },
    },
    edit: edit,
    save() {
        // Rendering in PHP
        return null;
    },
} );