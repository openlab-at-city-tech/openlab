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

const settingsidOptions = [
 ];

 wp.apiFetch({path: "/link-library/v1/settingslist"}).then(posts => {
    jQuery.each( posts, function( key, val ) {
        settingsidOptions.push( {label: 'Library #' + key + ': ' + val, value: key} );
    });
}).catch( 

)

const edit = props => {
    const {attributes: { settings }, className, setAttributes } = props;

    const setSettingsID = settings => {
        props.setAttributes( { settings } );
    };

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
        </InspectorControls>
    );
    return [
        <div className={ props.className } key="returneddata">
            <ServerSideRender
                block="link-library/search-block"
                attributes = {props.attributes}
            />
            { inspectorControls }

        </div>
    ];
};

export default edit;
