import edit from './edit';

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { dateI18n, format, __experimentalGetSettings } from '@wordpress/date';
import { setState } from '@wordpress/compose';

registerBlockType( 'link-library/link-block', {
    title: 'Link Library Links',
    icon: 'admin-links',
    category: 'link-library',
    attributes: {
        settings: {
            type: 'string',
            default: '1',
        },
        linkorderoverride: {
            type: 'string',
            default: '',
        },
        linkdirectionoverride: {
            type: 'string',
            default: '',
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
        maxlinksoverride:{
            type: 'string',
            default: '',
        },
        notesoverride:{
            type: 'boolean',
            default: false,
        },
        descoverride:{
            type: 'boolean',
            default: false,
        },
        rssoverride:{
            type: 'boolean',
            default: false,
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