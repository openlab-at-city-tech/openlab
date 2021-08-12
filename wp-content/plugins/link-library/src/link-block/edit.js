import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';
import { SelectControl, 
    Toolbar,
    Button,
    Tooltip,
    PanelBody,
    PanelRow,
    FormToggle,
    ToggleControl,
    ToolbarGroup,
    Disabled, 
    RadioControl,
    RangeControl,
    TextControl,
    CheckboxControl,
    FontSizePicker } from '@wordpress/components';

    import {
        RichText,
        AlignmentToolbar,
        BlockControls,
        BlockAlignmentToolbar,
        InspectorControls,
        InnerBlocks,
        withColors,
        PanelColorSettings,
        getColorClassName
    } from '@wordpress/block-editor'
    ;
import { withSelect, widthDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';

const {
    withState
} = wp.compose;

const linkOrderOptions = [
    { label: 'Select an option to override', value: '' },
    { label: 'Name', value: 'name' },
    { label: 'Link ID', value: 'id' },
    { label: 'Random', value: 'random' },
    { label: 'Updated Date', value: 'date' },
    { label: 'Publication Date', value: 'pubdate' },
    { label: 'Link Visits', value: 'hits' },
    { label: 'User Votes', value: 'uservotes' },
    { label: 'Simple Custom Post Order plugin', value: 'scpo' },
 ];

 const linkDirectionOptions = [
    { label: 'Select an option to override', value: '' },
    { label: 'Ascending', value: 'ASC' },
    { label: 'Descending', value: 'DESC' },
 ];


const settingsidOptions = [
 ];

 wp.apiFetch({path: "/link-library/v1/settingslist"}).then(posts => {
    jQuery.each( posts, function( key, val ) {
        settingsidOptions.push( {label: 'Library #' + key + ': ' + val, value: key} );
    });
}).catch( 

)

const categoryOptions = [
];

wp.apiFetch({path: "/wp/v2/link_library_category?per_page=100"}).then(posts => {
    jQuery.each( posts, function( key, val ) {
        categoryOptions.push({label: val.name, value: val.id});
    });
}).catch( 

)

const tagOptions = [
];

wp.apiFetch({path: "/wp/v2/link_library_tags?per_page=100"}).then(posts => {
    jQuery.each( posts, function( key, val ) {
        tagOptions.push({label: val.name, value: val.id});
    });
}).catch( 

)

const edit = props => {
    const {attributes: { settings, linkorderoverride, linkdirectionoverride, categorylistoverride, excludecategoryoverride, taglistoverride, maxlinksoverride, notesoverride, descoverride, rssoverride, categorylistoverrideCSV, excludecategoryoverrideCSV, taglistoverrideCSV }, className, setAttributes } = props;

    const setSettingsID = settings => {
        props.setAttributes( { settings } );
    };

    const setLinkOrder = linkorderoverride => {
        props.setAttributes( { linkorderoverride } );
    };

    const setLinkDirection = linkdirectionoverride => {
        props.setAttributes( { linkdirectionoverride } );
    };

    const setOverrideCategories = categorylistoverride => {
        props.setAttributes( { categorylistoverride } );
    };

    const setExcludeOverrideCategories = excludecategoryoverride => {
        props.setAttributes( { excludecategoryoverride } );
    };

    const setTagOverride = taglistoverride => {
        props.setAttributes( { taglistoverride } );
    };

    const setNotesOverride = notesoverride => {
        props.setAttributes( { notesoverride } );
    }

    const setDescOverride = descoverride => {
        props.setAttributes( { descoverride } );
    }

    const setRSSOverride = rssoverride => {
        props.setAttributes( { rssoverride } );
    }

    const setCategoryOverrideArrayCSV = categorylistoverrideCSV => {
        props.setAttributes( { categorylistoverrideCSV } );
    }

    const setExcludeCategoryOverrideArrayCSV = excludecategoryoverrideCSV => {
        props.setAttributes( { excludecategoryoverrideCSV } );
    }

    const setTagOverrideArrayCSV = taglistoverrideCSV => {
        props.setAttributes( { taglistoverrideCSV } );
    }

    const setMaxLinksOverride = maxlinksoverride => {
        props.setAttributes( { maxlinksoverride } );
    }

    const inspectorControls = (
        <InspectorControls key="inspector">
            <PanelBody>
                <PanelRow>
                    <SelectControl
                        label="Library Configuration"
                        value={ settings }
                        options= { settingsidOptions }
                        onChange = { setSettingsID }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody title={ __( 'Configuration overrides' )} initialOpen={ false }>
                <PanelRow>
                    <SelectControl
                        label="Link Order Override"
                        value={ linkorderoverride }
                        options= { linkOrderOptions }
                        onChange = { setLinkOrder }
                    />
                </PanelRow>
                <PanelRow>
                    <SelectControl
                        label="Library Configuration"
                        value={ linkdirectionoverride }
                        options= { linkDirectionOptions }
                        onChange = { setLinkDirection }
                    />
                </PanelRow>
                <PanelRow>
                    <span className="link-library-cat-override">
                    <SelectControl
                        multiple
                        label = "Override Categories to display"
                        help = "Select one or more categories to override library category list. Ctrl-Click to select multiple items or deselect an item."
                        value = { categorylistoverride }
                        options = { categoryOptions }
                        onChange = { setOverrideCategories }
                    />
                    </span>
                </PanelRow>
                <PanelRow>
                    <span className="link-library-cat-exclude-override">
                    <SelectControl
                        multiple
                        label = "Override Categories to be excluded"
                        help = "Select one or more categories to override excluded category list. Ctrl-Click to select multiple items or deselect an item."
                        value = { excludecategoryoverride }
                        options = { categoryOptions }
                        onChange = { setExcludeOverrideCategories }
                    />
                    </span>
                </PanelRow>
                <PanelRow>
                    <span className="link-library-tag-override">
                    <SelectControl
                        multiple
                        label = "Override Tags to display"
                        help = "Select one or more tags to override library tag list. Ctrl-Click to select multiple items or deselect an item."
                        value = { taglistoverride }
                        options = { tagOptions }
                        onChange = { setTagOverride }
                    />
                    </span>
                </PanelRow>
                <TextControl
                        label = "Max number of links to display"
                        value = { props.attributes.maxlinksoverride }
                        onChange = { setMaxLinksOverride }
                    />
                <PanelRow>
                    <CheckboxControl
                        label="Force Notes Display"
                        checked ={ props.attributes.notesoverride }
                        onChange = { setNotesOverride }
                    />
                </PanelRow>
                <PanelRow>
                    <CheckboxControl
                        label="Force Description Display"
                        checked ={ props.attributes.descoverride }
                        onChange = { setDescOverride }
                    />
                </PanelRow>
                <PanelRow>
                    <CheckboxControl
                        label="Force RSS Display"
                        checked ={ props.attributes.rssoverride }
                        onChange = { setRSSOverride }
                    />
                </PanelRow>
            </PanelBody>
            <PanelBody title={ __( 'CSV category list overrides' )} initialOpen={ false }>
            <PanelRow>
                    <TextControl
                        label = "Comma-separated list of category IDs to display"
                        value = { props.attributes.categorylistoverrideCSV }
                        onChange = { setCategoryOverrideArrayCSV }
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label = "Comma-separated list of category IDs to exclude"
                        value = { props.attributes.excludecategoryoverrideCSV }
                        onChange = { setExcludeCategoryOverrideArrayCSV }
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label = "Comma-separated list of tag IDs to display"
                        value = { props.attributes.taglistoverrideCSV }
                        onChange = { setTagOverrideArrayCSV }
                    />
                </PanelRow>
            </PanelBody>
        </InspectorControls>
    );
    return [
        <div className={ props.className } key="returneddata">
            <div className="ll-block-warning">Warning: Some Link Library features like Pagination, AJAX category switching and Masonry layout won't work as you build your page in the Block Editor but will work correctly when viewed on your site.</div>
            <ServerSideRender
                block="link-library/link-block"
                attributes = {props.attributes}
            />
            { inspectorControls }
        </div>
    ];
};

export default edit;
