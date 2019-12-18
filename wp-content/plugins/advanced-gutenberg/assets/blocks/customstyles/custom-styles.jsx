(function ( wpI18n, wpHooks, wpBlocks, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { addFilter } = wpHooks;
    const { __ } = wpI18n;
    const { hasBlockSupport } = wpBlocks;
    const { InspectorControls } = wpBlockEditor;
    const { SelectControl } = wpComponents;

    // Register custom styles to blocks attributes
    addFilter( 'blocks.registerBlockType', 'advgb/registerCustomStyleClass', function ( settings ) {
        if (settings.name === 'core/paragraph') {
            settings.attributes = Object.assign( settings.attributes, {
                customStyle: {
                    type: 'string'
                },
                identifyColor: {
                    type: 'string'
                }
            } );
        }

        return settings;
    } );

    // Add option to return to default style
    if (typeof advgbBlocks.customStyles !== 'undefined' && advgbBlocks.customStyles) {
        advgbBlocks.customStyles.unshift( {
            id: 0,
            label: __( 'Paragraph', 'advanced-gutenberg' ),
            value: '',
            identifyColor: ''
        } );
    }

    // Add option to select custom styles for paragraph blocks
    addFilter( 'editor.BlockEdit', 'advgb/customStyles', function ( BlockEdit ) {
        return ( props ) => {
            return ( [
                <BlockEdit key="block-edit-custom-class-name" {...props} />,
                props.isSelected && props.name === "core/paragraph" &&
                <InspectorControls key="advgb-custom-controls">
                    <SelectControl
                        label={ [
                            __( 'Custom styles', 'advanced-gutenberg' ),
                            <span className={'components-panel__color-area'}
                                  key="customstyle-identify"
                                  style={ {
                                      background: props.attributes.identifyColor,
                                      verticalAlign: 'text-bottom',
                                      borderRadius: '50%',
                                      border: 'none',
                                      width: '16px',
                                      height: '16px',
                                      display: 'inline-block',
                                      marginLeft: '10px',
                                  } } />
                        ] }
                        help={__( 'This option let you add custom style for current paragraph. (Front-end only!)', 'advanced-gutenberg' )}
                        value={props.attributes.customStyle}
                        options={advgbBlocks.customStyles.map( ( cstyle, index ) => {
                            if (cstyle.title) advgbBlocks.customStyles[ index ].label = cstyle.title;
                            if (cstyle.name) advgbBlocks.customStyles[ index ].value = cstyle.name;

                            return cstyle;
                        } )}
                        onChange={( cstyle ) => {
                            const { identifyColor } = advgbBlocks.customStyles.filter( ( style ) => style.value === cstyle )[0];
                            props.setAttributes( {
                                customStyle: cstyle,
                                identifyColor: identifyColor,
                                backgroundColor: undefined,
                                textColor: undefined,
                                fontSize: undefined,
                            } );
                        }}
                    />
                </InspectorControls>
            ] )
        }
    } );

    // Apply custom styles on front-end
    addFilter( 'blocks.getSaveContent.extraProps', 'advgb/loadFrontendCustomStyles', function ( extraProps, blockType, attributes ) {
        if (hasBlockSupport( blockType, 'customStyle', true ) && attributes.customStyle) {
            if (typeof extraProps.className === 'undefined') {
                extraProps.className = attributes.customStyle;
            } else {
                extraProps.className += ' ' + attributes.customStyle;
                extraProps.className = extraProps.className.trim();
            }
        }

        return extraProps;
    } );
})( wp.i18n, wp.hooks, wp.blocks, wp.blockEditor, wp.components );