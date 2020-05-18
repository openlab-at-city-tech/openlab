import {AdvColorControl} from "../0-adv-components/components.jsx";

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InspectorControls, BlockControls, BlockAlignmentToolbar, RichText, PanelColorSettings, URLInput } = wpBlockEditor;
    const { BaseControl, RangeControl, PanelBody, ToggleControl, SelectControl, IconButton, Toolbar } = wpComponents;

    const previewImageData = 'data:image/png;base64,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';
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
                { label: __( 'None', 'advanced-gutenberg' ), value: 'none' },
                { label: __( 'Solid', 'advanced-gutenberg' ), value: 'solid' },
                { label: __( 'Dotted', 'advanced-gutenberg' ), value: 'dotted' },
                { label: __( 'Dashed', 'advanced-gutenberg' ), value: 'dashed' },
                { label: __( 'Double', 'advanced-gutenberg' ), value: 'double' },
                { label: __( 'Groove', 'advanced-gutenberg' ), value: 'groove' },
                { label: __( 'Ridge', 'advanced-gutenberg' ), value: 'ridge' },
                { label: __( 'Inset', 'advanced-gutenberg' ), value: 'inset' },
                { label: __( 'Outset', 'advanced-gutenberg' ), value: 'outset' },
            ];
            const {
                attributes,
                setAttributes,
                isSelected,
                className,
                clientId: blockID,
            } = this.props;
            const {
                id, align, url, urlOpenNewTab, title, text, bgColor, textColor, textSize,
                marginTop, marginRight, marginBottom, marginLeft,
                paddingTop, paddingRight, paddingBottom, paddingLeft,
                borderWidth, borderColor, borderRadius, borderStyle,
                hoverTextColor, hoverBgColor, hoverShadowColor, hoverShadowH, hoverShadowV, hoverShadowBlur, hoverShadowSpread,
                hoverOpacity, transitionSpeed, isPreview
            } = attributes;

            const isStyleSquared = className.indexOf('-squared') > -1;
            const isStyleOutlined = className.indexOf('-outline') > -1;
            const hoverColorSettings = [
                {
                    label: __( 'Background Color', 'advanced-gutenberg' ),
                    value: hoverBgColor,
                    onChange: ( value ) => setAttributes( { hoverBgColor: value === undefined ? '#2196f3' : value } ),
                },
                {
                    label: __( 'Text Color', 'advanced-gutenberg' ),
                    value: hoverTextColor,
                    onChange: ( value ) => setAttributes( { hoverTextColor: value === undefined ? '#fff' : value } ),
                },
                {
                    label: __( 'Shadow Color', 'advanced-gutenberg' ),
                    value: hoverShadowColor,
                    onChange: ( value ) => setAttributes( { hoverShadowColor: value === undefined ? '#ccc' : value } ),
                },
            ];

            if (isStyleOutlined) {
                hoverColorSettings.shift();
            }

            return (
                isPreview ?
                    <img alt={__('Advanced Button', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                    <Fragment>
                    <BlockControls>
                        <BlockAlignmentToolbar value={ align } onChange={ ( align ) => setAttributes( { align: align } ) } />
                        <Toolbar>
                            <IconButton
                                label={ __( 'Refresh this button when it conflict with other buttons styles', 'advanced-gutenberg' ) }
                                icon="update"
                                className="components-toolbar__control"
                                onClick={ () => setAttributes( { id: 'advgbbutton-' + blockID } ) }
                            />
                        </Toolbar>
                    </BlockControls>
                    <span className={`${className} align${align}`}
                          style={ { display: 'inline-block' } }
                    >
                        <RichText
                            tagName="span"
                            placeholder={ __( 'Add text…', 'advanced-gutenberg' ) }
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
                        color: ${textColor} !important;
                        background-color: ${bgColor} !important;
                        margin: ${marginTop}px ${marginRight}px ${marginBottom}px ${marginLeft}px;
                        padding: ${paddingTop}px ${paddingRight}px ${paddingBottom}px ${paddingLeft}px;
                        border-width: ${borderWidth}px;
                        border-color: ${borderColor} !important;
                        border-radius: ${borderRadius}px !important;
                        border-style: ${borderStyle} ${borderStyle !== 'none' && '!important'};
                    }
                    .${id}:hover {
                        color: ${hoverTextColor} !important;
                        background-color: ${hoverBgColor} !important;
                        box-shadow: ${hoverShadowH}px ${hoverShadowV}px ${hoverShadowBlur}px ${hoverShadowSpread}px ${hoverShadowColor};
                        transition: all ${transitionSpeed}s ease;
                        opacity: ${hoverOpacity/100}
                    }`}
                    </style>
                    <InspectorControls>
                        <PanelBody title={ __( 'Button link', 'advanced-gutenberg' ) }>
                            <BaseControl
                                label={ [
                                    __( 'Link URL', 'advanced-gutenberg' ),
                                    (url && <a href={ url || '#' } key="link_url" target="_blank" style={ { float: 'right' } }>
                                        { __( 'Preview', 'advanced-gutenberg' ) }
                                    </a>)
                                ] }
                            >
                                <URLInput
                                    value={url}
                                    onChange={ (value) => setAttributes( { url: value } ) }
                                    autoFocus={false}
                                    isFullWidth
                                    hasBorder
                                />
                            </BaseControl>
                            <ToggleControl
                                label={ __( 'Open in new tab', 'advanced-gutenberg' ) }
                                checked={ !!urlOpenNewTab }
                                onChange={ () => setAttributes( { urlOpenNewTab: !attributes.urlOpenNewTab } ) }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Text/Color', 'advanced-gutenberg' ) }>
                            <RangeControl
                                label={ __( 'Text size', 'advanced-gutenberg' ) }
                                value={ textSize || '' }
                                onChange={ ( size ) => setAttributes( { textSize: size } ) }
                                min={ 10 }
                                max={ 100 }
                                beforeIcon="editor-textcolor"
                                allowReset
                            />
                            {!isStyleOutlined && (
                                <AdvColorControl
                                    label={ __('Background Color', 'advanced-gutenberg') }
                                    value={ bgColor }
                                    onChange={ (value) => setAttributes( { bgColor: value } ) }
                                />
                            )}
                            <AdvColorControl
                                label={ __('Text Color', 'advanced-gutenberg') }
                                value={ textColor }
                                onChange={ (value) => setAttributes( { textColor: value } ) }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Border', 'advanced-gutenberg' ) } initialOpen={ false } >
                            {!isStyleSquared && (
                                <RangeControl
                                    label={ __( 'Border radius', 'advanced-gutenberg' ) }
                                    value={ borderRadius || '' }
                                    onChange={ ( value ) => setAttributes( { borderRadius: value } ) }
                                    min={ 0 }
                                    max={ 100 }
                                />
                            ) }
                            <SelectControl
                                label={ __( 'Border style', 'advanced-gutenberg' ) }
                                value={ borderStyle }
                                options={ listBorderStyles }
                                onChange={ ( value ) => setAttributes( { borderStyle: value } ) }
                            />
                            {borderStyle !== 'none' && (
                                <Fragment>
                                    <PanelColorSettings
                                        title={ __( 'Border Color', 'advanced-gutenberg' ) }
                                        initialOpen={ false }
                                        colorSettings={ [
                                            {
                                                label: __( 'Border Color', 'advanced-gutenberg' ),
                                                value: borderColor,
                                                onChange: ( value ) => setAttributes( { borderColor: value === undefined ? '#2196f3' : value } ),
                                            },
                                        ] }
                                    />
                                    <RangeControl
                                        label={ __( 'Border width', 'advanced-gutenberg' ) }
                                        value={ borderWidth || '' }
                                        onChange={ ( value ) => setAttributes( { borderWidth: value } ) }
                                        min={ 0 }
                                        max={ 100 }
                                    />
                                </Fragment>
                            ) }
                        </PanelBody>
                        <PanelBody title={ __( 'Margin', 'advanced-gutenberg' ) } initialOpen={ false } >
                            <RangeControl
                                label={ __( 'Margin top', 'advanced-gutenberg' ) }
                                value={ marginTop || '' }
                                onChange={ ( value ) => setAttributes( { marginTop: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Margin right', 'advanced-gutenberg' ) }
                                value={ marginRight || '' }
                                onChange={ ( value ) => setAttributes( { marginRight: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Margin bottom', 'advanced-gutenberg' ) }
                                value={ marginBottom || '' }
                                onChange={ ( value ) => setAttributes( { marginBottom: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Margin left', 'advanced-gutenberg' ) }
                                value={ marginLeft || '' }
                                onChange={ ( value ) => setAttributes( { marginLeft: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Padding', 'advanced-gutenberg' ) } initialOpen={ false } >
                            <RangeControl
                                label={ __( 'Padding top', 'advanced-gutenberg' ) }
                                value={ paddingTop || '' }
                                onChange={ ( value ) => setAttributes( { paddingTop: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding right', 'advanced-gutenberg' ) }
                                value={ paddingRight || '' }
                                onChange={ ( value ) => setAttributes( { paddingRight: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding bottom', 'advanced-gutenberg' ) }
                                value={ paddingBottom || '' }
                                onChange={ ( value ) => setAttributes( { paddingBottom: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding left', 'advanced-gutenberg' ) }
                                value={ paddingLeft || '' }
                                onChange={ ( value ) => setAttributes( { paddingLeft: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Hover', 'advanced-gutenberg' ) } initialOpen={ false } >
                            <PanelColorSettings
                                title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ hoverColorSettings }
                            />
                            <PanelBody title={ __( 'Shadow', 'advanced-gutenberg' ) } initialOpen={ false }  >
                                <RangeControl
                                    label={ __('Opacity (%)', 'advanced-gutenberg') }
                                    value={ hoverOpacity }
                                    onChange={ ( value ) => setAttributes( { hoverOpacity: value } ) }
                                    min={ 0 }
                                    max={ 100 }
                                />
                                <RangeControl
                                    label={ __('Transition speed (ms)', 'advanced-gutenberg') }
                                    value={ transitionSpeed || '' }
                                    onChange={ ( value ) => setAttributes( { transitionSpeed: value } ) }
                                    min={ 0 }
                                    max={ 3000 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow H offset', 'advanced-gutenberg' ) }
                                    value={ hoverShadowH || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowH: value } ) }
                                    min={ -50 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow V offset', 'advanced-gutenberg' ) }
                                    value={ hoverShadowV || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowV: value } ) }
                                    min={ -50 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow blur', 'advanced-gutenberg' ) }
                                    value={ hoverShadowBlur || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowBlur: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow spread', 'advanced-gutenberg' ) }
                                    value={ hoverShadowSpread || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowSpread: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                </Fragment>
            )
        }
    }

    const buttonBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path d="M19 7H5c-1.1 0-2 .9-2 2v6c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm0 8H5V9h14v6z"/>
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
            default: __( 'PUSH THE BUTTON', 'advanced-gutenberg' ),
        },
        bgColor: {
            type: 'string',
        },
        textColor: {
            type: 'string',
        },
        textSize: {
            type: 'number',
            default: 18,
        },
        marginTop: {
            type: 'number',
            default: 0,
        },
        marginRight: {
            type: 'number',
            default: 0,
        },
        marginBottom: {
            type: 'number',
            default: 0,
        },
        marginLeft: {
            type: 'number',
            default: 0,
        },
        paddingTop: {
            type: 'number',
            default: 10,
        },
        paddingRight: {
            type: 'number',
            default: 30,
        },
        paddingBottom: {
            type: 'number',
            default: 10,
        },
        paddingLeft: {
            type: 'number',
            default: 30,
        },
        borderWidth: {
            type: 'number',
            default: 1,
        },
        borderColor: {
            type: 'string',
        },
        borderStyle: {
            type: 'string',
            default: 'none',
        },
        borderRadius: {
            type: 'number',
            default: 50
        },
        hoverTextColor: {
            type: 'string',
        },
        hoverBgColor: {
            type: 'string',
        },
        hoverShadowColor: {
            type: 'string',
            default: '#ccc'
        },
        hoverShadowH: {
            type: 'number',
            default: 1,
        },
        hoverShadowV: {
            type: 'number',
            default: 1,
        },
        hoverShadowBlur: {
            type: 'number',
            default: 12,
        },
        hoverShadowSpread: {
            type: 'number',
            default: 0,
        },
        hoverOpacity: {
            type: 'number',
            default: 100,
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
        isPreview: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType( 'advgb/button', {
        title: __( 'Advanced Button', 'advanced-gutenberg' ),
        description: __( 'New button with more styles.', 'advanced-gutenberg' ),
        icon: {
            src: buttonBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'button', 'advanced-gutenberg' ), __( 'link', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
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
        styles: [
            { name: 'default', label: __( 'Default', 'advanced-gutenberg' ), isDefault: true },
            { name: 'outlined', label: __( 'Outlined', 'advanced-gutenberg' ) },
            { name: 'squared', label: __( 'Squared', 'advanced-gutenberg' ) },
            { name: 'squared-outline', label: __( 'Squared Outline', 'advanced-gutenberg' ) },
        ],
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