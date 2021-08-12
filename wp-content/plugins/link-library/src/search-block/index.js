import edit from './edit';

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { dateI18n, format, __experimentalGetSettings } from '@wordpress/date';
import { setState } from '@wordpress/compose';

registerBlockType( 'link-library/search-block', {
    title: 'Link Library Search Box',
    icon: 'admin-links',
    category: 'link-library',
    attributes: {
        settings: {
            type: 'string',
            default: '1',
        },
    },
    edit: edit,
    save() {
        // Rendering in PHP
        return null;
    },
} );