(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InspectorControls, BlockControls, BlockAlignmentToolbar, RichText, PanelColorSettings } = wpBlockEditor;
    const { RangeControl, PanelBody, TextControl, ToggleControl, SelectControl, IconButton, Toolbar } = wpComponents;

    class AdvButton extends Component {
        constructor() {
            super( ...arguments );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-button'];

            // No override attributes of blocks inserted before
            if (attributes.changed !== true) {
                if (typeof currentBlockConfig === 'object' && currentBlockConfig !== null) {
                    Object.keys(currentBlockConfig).map((attribute) => {
                        if (typeof attributes[attribute] === 'boolean') {
                            attributes[attribute] = !!currentBlockConfig[attribute];
                        } else {
                            attributes[attribute] = currentBlockConfig[attribute];
                        }
                    });
                }

                // Finally set changed attribute to true, so we don't modify anything again
                setAttributes( { changed: true } );
            }
        }

        componentDidMount() {
            const { attributes, setAttributes, clientId } = this.props;


            if ( !attributes.id ) {
                setAttributes( { id: 'advgbbtn-' + clientId } );
            }
        }

        render() {
            const listBorderStyles = [
                { label: __( 'None' ), value: 'none' },
                { label: __( 'Solid' ), value: 'solid' },
                { label: __( 'Dotted' ), value: 'dotted' },
                { label: __( 'Dashed' ), value: 'dashed' },
                { label: __( 'Double' ), value: 'double' },
                { label: __( 'Groove' ), value: 'groove' },
                { label: __( 'Ridge' ), value: 'ridge' },
                { label: __( 'Inset' ), value: 'inset' },
                { label: __( 'Outset' ), value: 'outset' },
            ];
            const {
                attributes,
                setAttributes,
                isSelected,
                className,
                clientId: blockID,
            } = this.props;
            const {
                id,
                align,
                url,
                urlOpenNewTab,
                title,
                text,
                bgColor,
                textColor,
                textSize,
                paddingTop,
                paddingRight,
                paddingBottom,
                paddingLeft,
                borderWidth,
                borderColor,
                borderRadius,
                borderStyle,
                hoverTextColor,
                hoverBgColor,
                hoverShadowColor,
                hoverShadowH,
                hoverShadowV,
                hoverShadowBlur,
                hoverShadowSpread,
                transitionSpeed,
            } = attributes;

            return (
                <Fragment>
                    <BlockControls>
                        <BlockAlignmentToolbar value={ align } onChange={ ( align ) => setAttributes( { align: align } ) } />
                        <Toolbar>
                            <IconButton
                                label={ __( 'Refresh this button when it conflict with other buttons styles' ) }
                                icon="update"
                                className="components-toolbar__control"
                                onClick={ () => setAttributes( { id: 'advgbbutton-' + blockID } ) }
                            />
                        </Toolbar>
                    </BlockControls>
                    <span style={ { display: 'inline-block' } } >
                        <RichText
                            placeholder={ __( 'Add text…' ) }
                            value={ text }
                            onChange={ ( value ) => setAttributes( { text: value } ) }
                            formattingControls={ [ 'bold', 'italic', 'strikethrough' ] }
                            isSelected={ isSelected }
                            className={ `wp-block-advgb-button_link ${id}` }
                            keepPlaceholderOnFocus
                        />
                    </span>
                    <style>
                        {`.${id} {
                        font-size: ${textSize}px;
                        color: ${textColor};
                        background-color: ${bgColor};
                        padding: ${paddingTop}px ${paddingRight}px ${paddingBottom}px ${paddingLeft}px;
                        border-width: ${borderWidth}px;
                        border-color: ${borderColor};
                        border-radius: ${borderRadius}px;
                        border-style: ${borderStyle};
                    }
                    .${id}:hover {
                        color: ${hoverTextColor};
                        background-color: ${hoverBgColor};
                        box-shadow: ${hoverShadowH}px ${hoverShadowV}px ${hoverShadowBlur}px ${hoverShadowSpread}px ${hoverShadowColor};
                        transition: all ${transitionSpeed}s ease;
                    }`}
                    </style>
                    <InspectorControls>
                        <PanelBody title={ __( 'Button link' ) }>
                            <TextControl
                                label={ [
                                    __( 'Link URL' ),
                                    (url && <a href={ url || '#' } key="link_url" target="_blank" style={ { float: 'right' } }>
                                        { __( 'Preview' ) }
                                    </a>)
                                ] }
                                value={ url || '' }
                                placeholder={ __( 'Enter URL…' ) }
                                onChange={ ( text ) => setAttributes( { url: text } ) }
                            />
                            <ToggleControl
                                label={ __( 'Open in new tab' ) }
                                checked={ !!urlOpenNewTab }
                                onChange={ () => setAttributes( { urlOpenNewTab: !attributes.urlOpenNewTab } ) }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Text/Color' ) }>
                            <RangeControl
                                label={ __( 'Text size' ) }
                                value={ textSize || '' }
                                onChange={ ( size ) => setAttributes( { textSize: size } ) }
                                min={ 10 }
                                max={ 100 }
                                beforeIcon="editor-textcolor"
                                allowReset
                            />
                            <PanelColorSettings
                                title={ __( 'Color Settings' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Background Color' ),
                                        value: bgColor,
                                        onChange: ( value ) => setAttributes( { bgColor: value === undefined ? '#2196f3' : value } ),
                                    },
                                    {
                                        label: __( 'Text Color' ),
                                        value: textColor,
                                        onChange: ( value ) => setAttributes( { textColor: value === undefined ? '#fff' : value } ),
                                    },
                                ] }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Border' ) } initialOpen={ false } >
                            <RangeControl
                                label={ __( 'Border radius' ) }
                                value={ borderRadius || '' }
                                onChange={ ( value ) => setAttributes( { borderRadius: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <SelectControl
                                label={ __( 'Border style' ) }
                                value={ borderStyle }
                                options={ listBorderStyles }
                                onChange={ ( value ) => setAttributes( { borderStyle: value } ) }
                            />
                            {borderStyle !== 'none' && (
                                <Fragment>
                                    <PanelColorSettings
                                        title={ __( 'Border Color' ) }
                                        initialOpen={ false }
                                        colorSettings={ [
                                            {
                                                label: __( 'Border Color' ),
                                                value: borderColor,
                                                onChange: ( value ) => setAttributes( { borderColor: value === undefined ? '#2196f3' : value } ),
                                            },
                                        ] }
                                    />
                                    <RangeControl
                                        label={ __( 'Border width' ) }
                                        value={ borderWidth || '' }
                                        onChange={ ( value ) => setAttributes( { borderWidth: value } ) }
                                        min={ 0 }
                                        max={ 100 }
                                    />
                                </Fragment>
                            ) }
                        </PanelBody>
                        <PanelBody title={ __( 'Padding' ) } initialOpen={ false } >
                            <RangeControl
                                label={ __( 'Padding top' ) }
                                value={ paddingTop || '' }
                                onChange={ ( value ) => setAttributes( { paddingTop: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding right' ) }
                                value={ paddingRight || '' }
                                onChange={ ( value ) => setAttributes( { paddingRight: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding bottom' ) }
                                value={ paddingBottom || '' }
                                onChange={ ( value ) => setAttributes( { paddingBottom: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding left' ) }
                                value={ paddingLeft || '' }
                                onChange={ ( value ) => setAttributes( { paddingLeft: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Hover' ) } initialOpen={ false } >
                            <PanelColorSettings
                                title={ __( 'Color Settings' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Background Color' ),
                                        value: hoverBgColor,
                                        onChange: ( value ) => setAttributes( { hoverBgColor: value === undefined ? '#2196f3' : value } ),
                                    },
                                    {
                                        label: __( 'Text Color' ),
                                        value: hoverTextColor,
                                        onChange: ( value ) => setAttributes( { hoverTextColor: value === undefined ? '#fff' : value } ),
                                    },
                                    {
                                        label: __( 'Shadow Color' ),
                                        value: hoverShadowColor,
                                        onChange: ( value ) => setAttributes( { hoverShadowColor: value === undefined ? '#ccc' : value } ),
                                    },
                                ] }
                            />
                            <PanelBody title={ __( 'Shadow' ) } initialOpen={ false }  >
                                <RangeControl
                                    label={ __( 'Shadow H offset' ) }
                                    value={ hoverShadowH || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowH: value } ) }
                                    min={ -50 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow V offset' ) }
                                    value={ hoverShadowV || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowV: value } ) }
                                    min={ -50 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow blur' ) }
                                    value={ hoverShadowBlur || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowBlur: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow spread' ) }
                                    value={ hoverShadowSpread || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowSpread: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                            </PanelBody>
                            <RangeControl
                                label={ __('Transition speed (ms)') }
                                value={ transitionSpeed || '' }
                                onChange={ ( value ) => setAttributes( { transitionSpeed: value } ) }
                                min={ 0 }
                                max={ 3000 }
                            />
                        </PanelBody>
                    </InspectorControls>
                </Fragment>
            )
        }
    }

    const buttonBlockIcon = (
        <svg height="20" viewBox="2 2 22 22" width="20" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0h24v24H0V0z" fill="none"/>
            <path d="M5 14.5h14v-6H5v6zM11 .55V3.5h2V.55h-2zm8.04 2.5l-1.79 1.79 1.41 1.41 1.8-1.79-1.42-1.41zM13 22.45V19.5h-2v2.95h2zm7.45-3.91l-1.8-1.79-1.41 1.41 1.79 1.8 1.42-1.42zM3.55 4.46l1.79 1.79 1.41-1.41-1.79-1.79-1.41 1.41zm1.41 15.49l1.79-1.8-1.41-1.41-1.79 1.79 1.41 1.42z"/>
        </svg>
    );
    const blockAttrs = {
        id: {
            type: 'string',
        },
        url: {
            type: 'string',
        },
        urlOpenNewTab: {
            type: 'boolean',
            default: true,
        },
        title: {
            type: 'string',
        },
        text: {
            source: 'children',
            selector: 'a',
        },
        bgColor: {
            type: 'string',
            default: '#2196f3',
        },
        textColor: {
            type: 'string',
            default: '#fff',
        },
        textSize: {
            type: 'number',
            default: 18,
        },
        paddingTop: {
            type: 'number',
            default: 6,
        },
        paddingRight: {
            type: 'number',
            default: 12,
        },
        paddingBottom: {
            type: 'number',
            default: 6,
        },
        paddingLeft: {
            type: 'number',
            default: 12,
        },
        borderWidth: {
            type: 'number',
            default: 1,
        },
        borderColor: {
            type: 'string',
            default: '#2196f3'
        },
        borderStyle: {
            type: 'string',
            default: 'solid',
        },
        borderRadius: {
            type: 'number',
            default: 50
        },
        hoverTextColor: {
            type: 'string',
            default: '#fff'
        },
        hoverBgColor: {
            type: 'string',
            default: '#2196f3'
        },
        hoverShadowColor: {
            type: 'string',
            default: '#ccc'
        },
        hoverShadowH: {
            type: 'number',
            default: 3,
        },
        hoverShadowV: {
            type: 'number',
            default: 3,
        },
        hoverShadowBlur: {
            type: 'number',
            default: 1,
        },
        hoverShadowSpread: {
            type: 'number',
            default: 0,
        },
        transitionSpeed: {
            type: 'number',
            default: 200,
        },
        align: {
            type: 'string',
            default: 'none',
        },
        changed: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType( 'advgb/button', {
        title: __( 'Advanced Button' ),
        description: __( 'New button with more styles.' ),
        icon: {
            src: buttonBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __('button'), __('link') ],
        attributes: blockAttrs,
        transforms: {
            from: [
                {
                    type: 'block',
                    blocks: [ 'core/button' ],
                    transform: ( attributes ) => {
                        return createBlock( 'advgb/button', {
                            ...attributes,
                            bgColor: attributes.color,
                        } )
                    }
                }
            ],
            to: [
                {
                    type: 'block',
                    blocks: [ 'core/button' ],
                    transform: ( attributes ) => {
                        return createBlock( 'core/button', {
                            ...attributes,
                            color: attributes.bgColor,
                        } )
                    }
                }
            ]
        },
        edit: AdvButton,
        save: function ( { attributes } ) {
            const {
                id,
                align,
                url,
                urlOpenNewTab,
                title,
                text,
            } = attributes;

            return (
                <div className={ `align${align}` }>
                    <RichText.Content
                        tagName="a"
                        className={ `wp-block-advgb-button_link ${id}` }
                        href={ url || '#' }
                        title={ title }
                        target={ !urlOpenNewTab ? '_self' : '_blank' }
                        value={ text }
                        rel="noopener noreferrer"
                    />
                </div>
            );
        },
        getEditWrapperProps( attributes ) {
            const { align } = attributes;
            const props = { 'data-resized': true };

            if ( 'left' === align || 'right' === align || 'center' === align ) {
                props[ 'data-align' ] = align;
            }

            return props;
        },
        deprecated: [
            {
                attributes: {
                    ...blockAttrs,
                    transitionSpeed: {
                        type: 'number',
                        default: 0.2,
                    }
                },
                migrate: function( attributes ) {
                    const transitionSpeed = attributes.transitionSpeed * 1000;
                    return {
                        ...attributes,
                        transitionSpeed,
                    }
                },
                save: function ( { attributes } ) {
                    const {
                        id,
                        align,
                        url,
                        urlOpenNewTab,
                        title,
                        text,
                    } = attributes;

                    return (
                        <div className={ `align${align}` }>
                            <RichText.Content
                                tagName="a"
                                className={ `wp-block-advgb-button_link ${id}` }
                                href={ url || '#' }
                                title={ title }
                                target={ !urlOpenNewTab ? '_self' : '_blank' }
                                value={ text }
                                rel="noopener noreferrer"
                            />
                        </div>
                    );
                },
            },
        ],
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );