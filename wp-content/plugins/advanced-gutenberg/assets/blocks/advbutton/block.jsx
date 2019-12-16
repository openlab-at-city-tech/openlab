import {AdvColorControl} from "../0-adv-components/components.jsx";

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InspectorControls, BlockControls, BlockAlignmentToolbar, RichText, PanelColorSettings, URLInput } = wpBlockEditor;
    const { BaseControl, RangeControl, PanelBody, ToggleControl, SelectControl, IconButton, Toolbar } = wpComponents;

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
                hoverOpacity, transitionSpeed,
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