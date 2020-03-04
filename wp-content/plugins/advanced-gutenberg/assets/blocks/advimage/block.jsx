(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, RichText, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const { RangeControl, PanelBody, ToggleControl, SelectControl, TextControl, IconButton, Button, Toolbar, FocalPointPicker } = wpComponents;

    const previewImageData = 'data:image/png;base64,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';
    class AdvImage extends Component {
        constructor() {
            super(...arguments);
            this.state = {
                currentEdit: '',
            }
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-image'];

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
            const { blockIDX } = attributes;

            if (!blockIDX) {
                setAttributes({blockIDX: `advgb-img-${clientId}`});
            }
        }

        render() {
            const { currentEdit } = this.state;
            const { attributes, setAttributes, isSelected } = this.props;
            const {
                blockIDX, openOnClick, openUrl, linkInNewTab, imageUrl, imageID,
                title, titleColor, subtitle, subtitleColor, overlayColor, defaultOpacity,
                fullWidth, width, height, vAlign, hAlign, overlayOpacity, focalPoint,
                isPreview,
            } = attributes;
            const blockClassName = [
                'advgb-image-block',
                fullWidth && 'full-width',
                blockIDX,
            ].filter( Boolean ).join( ' ' );

            return (
                isPreview ?
                    <img alt={__('Advanced Image', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    {imageID && (
                        <BlockControls>
                            <Toolbar>
                                <MediaUpload
                                    allowedTypes={ ['image'] }
                                    value={ imageID }
                                    onSelect={ (image) => setAttributes( { imageUrl: image.url, imageID: image.id } ) }
                                    render={ ( { open } ) => (
                                        <IconButton
                                            className="components-toolbar__control"
                                            label={ __( 'Change image', 'advanced-gutenberg' ) }
                                            icon="edit"
                                            onClick={ open }
                                        />
                                    ) }
                                />
                                <IconButton
                                    className="components-toolbar__control"
                                    label={ __( 'Remove image', 'advanced-gutenberg' ) }
                                    icon="no"
                                    onClick={ () => setAttributes( { imageUrl: undefined, imageID: undefined } ) }
                                />
                            </Toolbar>
                        </BlockControls>
                    ) }
                    <InspectorControls>
                        <PanelBody title={ __( 'Advanced Image', 'advanced-gutenberg' ) }>
                            <SelectControl
                                label={ __( 'Action on click', 'advanced-gutenberg' ) }
                                value={ openOnClick }
                                options={ [
                                    { label: __( 'None', 'advanced-gutenberg' ), value: 'none' },
                                    { label: __( 'Open image in lightbox', 'advanced-gutenberg' ), value: 'lightbox' },
                                    { label: __( 'Open custom URL', 'advanced-gutenberg' ), value: 'url' },
                                ] }
                                onChange={ (value) => setAttributes( { openOnClick: value } ) }
                            />
                            {openOnClick === 'url' &&
                            <Fragment>
                                <TextControl
                                    label={ [
                                        __( 'Link URL', 'advanced-gutenberg' ),
                                        (openUrl && <a href={ openUrl || '#' } key="advgb_image_link_url" target="_blank" style={ { float: 'right' } }>
                                            { __( 'Preview', 'advanced-gutenberg' ) }
                                        </a>)
                                    ] }
                                    value={ openUrl }
                                    placeholder={ __( 'Enter URL…', 'advanced-gutenberg' ) }
                                    onChange={ ( text ) => setAttributes( { openUrl: text } ) }
                                />
                                <ToggleControl
                                    label={ __( 'Open link in new tab', 'advanced-gutenberg' ) }
                                    checked={ linkInNewTab }
                                    onChange={ () => setAttributes( { linkInNewTab: !linkInNewTab } ) }
                                />
                            </Fragment>
                            }
                            <PanelBody title={ __( 'Image Size', 'advanced-gutenberg' ) }>
                                <ToggleControl
                                    label={ __( 'Full width', 'advanced-gutenberg' ) }
                                    checked={ fullWidth }
                                    onChange={ () => setAttributes( { fullWidth: !fullWidth } ) }
                                />
                                <RangeControl
                                    label={ __( 'Height', 'advanced-gutenberg' ) }
                                    value={ height }
                                    min={ 100 }
                                    max={ 1000 }
                                    onChange={ (value) => setAttributes( { height: value } ) }
                                />
                                {!fullWidth &&
                                <RangeControl
                                    label={ __( 'Width', 'advanced-gutenberg' ) }
                                    value={ width }
                                    min={ 200 }
                                    max={ 1300 }
                                    onChange={ (value) => setAttributes( { width: value } ) }
                                />}
                                {imageUrl && (
                                    <FocalPointPicker
                                        label={ __( 'Focal Point Picker', 'advanced-gutenberg' ) }
                                        url={ imageUrl }
                                        value={ focalPoint }
                                        onChange={ ( value ) => setAttributes( { focalPoint: value } ) }
                                    />
                                ) }
                                <RangeControl
                                    label={ __( 'Overlay opacity default', 'advanced-gutenberg' ) }
                                    value={ defaultOpacity }
                                    min={ 0 }
                                    max={ 100 }
                                    onChange={ (value) => setAttributes( { defaultOpacity: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Overlay opacity hover', 'advanced-gutenberg' ) }
                                    value={ overlayOpacity }
                                    min={ 0 }
                                    max={ 100 }
                                    onChange={ (value) => setAttributes( { overlayOpacity: value } ) }
                                />
                            </PanelBody>
                            <PanelColorSettings
                                title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Title Color', 'advanced-gutenberg' ),
                                        value: titleColor,
                                        onChange: ( value ) => setAttributes( { titleColor: value === undefined ? '#fff' : value } ),
                                    },
                                    {
                                        label: __( 'Subtitle Color', 'advanced-gutenberg' ),
                                        value: subtitleColor,
                                        onChange: ( value ) => setAttributes( { subtitleColor: value === undefined ? '#fff' : value } ),
                                    },
                                    {
                                        label: __( 'Overlay Color', 'advanced-gutenberg' ),
                                        value: overlayColor,
                                        onChange: ( value ) => setAttributes( { overlayColor: value === undefined ? '#000' : value } ),
                                    },
                                ] }
                            />
                            <PanelBody title={ __( 'Text Alignment', 'advanced-gutenberg' ) } initialOpen={false}>
                                <SelectControl
                                    label={ __( 'Vertical Alignment', 'advanced-gutenberg' ) }
                                    value={vAlign}
                                    options={ [
                                        { label: __( 'Top', 'advanced-gutenberg' ), value: 'flex-start' },
                                        { label: __( 'Center', 'advanced-gutenberg' ), value: 'center' },
                                        { label: __( 'Bottom', 'advanced-gutenberg' ), value: 'flex-end' },
                                    ] }
                                    onChange={ (value) => setAttributes( { vAlign: value } ) }
                                />
                                <SelectControl
                                    label={ __( 'Horizontal Alignment', 'advanced-gutenberg' ) }
                                    value={hAlign}
                                    options={ [
                                        { label: __( 'Left', 'advanced-gutenberg' ), value: 'flex-start' },
                                        { label: __( 'Center', 'advanced-gutenberg' ), value: 'center' },
                                        { label: __( 'Right', 'advanced-gutenberg' ), value: 'flex-end' },
                                    ] }
                                    onChange={ (value) => setAttributes( { hAlign: value } ) }
                                />
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                    <div className={ blockClassName }
                         style={ {
                             backgroundImage: `url(${imageUrl || advgbBlocks.image_holder})`,
                             backgroundPosition: focalPoint ? `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` : undefined,
                             height: height,
                             width: width,
                             justifyContent: vAlign,
                             alignItems: hAlign,
                         } }
                    >
                        <span className="advgb-image-overlay"
                              style={ { backgroundColor: overlayColor, opacity: defaultOpacity/100 } }
                        />
                        {!imageID &&
                        <MediaUpload
                            allowedTypes={ ['image'] }
                            value={ imageID }
                            onSelect={ (image) => setAttributes( { imageUrl: image.url, imageID: image.id, focalPoint: {} } ) }
                            render={ ( { open } ) => (
                                <Button
                                    className="button button-large advgb-browse-image-btn"
                                    onClick={ open }
                                >
                                    { __( 'Open media library', 'advanced-gutenberg' ) }
                                </Button>
                            ) }
                        />
                        }
                        <RichText
                            tagName="h4"
                            className="advgb-image-title"
                            value={ title }
                            onChange={ (value) => setAttributes( { title: value.trim() } ) }
                            style={ { color: titleColor } }
                            isSelected={ isSelected && currentEdit === 'title' }
                            unstableOnFocus={ () => this.setState( { currentEdit: 'title' } ) }
                            unstableOnSplit={ () => null }
                            placeholder={ __( 'Enter title…', 'advanced-gutenberg' ) }
                        />
                        <RichText
                            tagName="p"
                            className="advgb-image-subtitle"
                            value={ subtitle }
                            onChange={ (value) => setAttributes( { subtitle: value.trim() } ) }
                            style={ { color: subtitleColor } }
                            isSelected={ isSelected && currentEdit === 'subtitle' }
                            unstableOnFocus={ () => this.setState( { currentEdit: 'subtitle' } ) }
                            unstableOnSplit={ () => null }
                            placeholder={ __( 'Enter subtitle…', 'advanced-gutenberg' ) }
                        />
                        <style>
                            {`.${blockIDX}.advgb-image-block:hover .advgb-image-overlay {opacity: ${overlayOpacity/100} !important;}`}
                        </style>
                    </div>
                </Fragment>
            );
        }
    }

    const advImageBlockIcon = (
        <svg height="20" viewBox="2 2 22 22" width="20" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0h24v24H0V0z" fill="none"/>
            <path d="M1 5h2v14H1zm4 0h2v14H5zm17 0H10c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1h12c.55 0 1-.45 1-1V6c0-.55-.45-1-1-1zM11 17l2.5-3.15L15.29 16l2.5-3.22L21 17H11z"/>
        </svg>
    );

    const blockAttrs = {
        blockIDX: {
            type: 'string',
        },
        openOnClick: {
            type: 'string',
            default: 'none',
        },
        linkInNewTab: {
            type: 'boolean',
            default: true,
        },
        openUrl: {
            type: 'string',
        },
        imageUrl: {
            type: 'string',
        },
        imageID: {
            type: 'number',
        },
        title: {
            type: 'string',
            default: __( 'Image title', 'advanced-gutenberg' ),
        },
        titleColor: {
            type: 'string',
            default: '#fff',
        },
        subtitle: {
            type: 'string',
            default: __( 'Your subtitle here', 'advanced-gutenberg' ),
        },
        subtitleColor: {
            type: 'string',
            default: '#fff'
        },
        overlayColor: {
            type: 'string',
            default: '#000'
        },
        fullWidth: {
            type: 'boolean',
            default: true,
        },
        width: {
            type: 'number',
            default: 500,
        },
        height: {
            type: 'number',
            default: 500,
        },
        vAlign: {
            type: 'string',
            default: 'center',
        },
        hAlign: {
            type: 'string',
            default: 'center',
        },
        overlayOpacity: {
            type: 'number',
            default: 20,
        },
        defaultOpacity: {
            type: 'number',
            default: 40,
        },
        focalPoint: {
            type: 'object',
        },
        changed: {
            type: 'boolean',
            default: false,
        },
        isPreview: {
            type: 'boolean',
            default: false,
        }
    };

    registerBlockType( 'advgb/image', {
        title: __( 'Advanced Image', 'advanced-gutenberg' ),
        description: __( 'Advanced image/photo block with more options and styles.', 'advanced-gutenberg' ),
        icon: {
            src: advImageBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'image', 'advanced-gutenberg' ), __( 'photo', 'advanced-gutenberg' ), __( 'box', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            align: true,
        },
        edit: AdvImage,
        save: ( { attributes } ) => {
            const {
                blockIDX,
                openOnClick,
                openUrl,
                linkInNewTab,
                imageUrl,
                title,
                titleColor,
                subtitle,
                subtitleColor,
                overlayColor,
                fullWidth,
                width,
                height,
                vAlign,
                hAlign,
                focalPoint,
            } = attributes;
            const linkURL = ( openOnClick === 'url' && !!openUrl ) ? openUrl : undefined;
            const blockClassName = [
                'advgb-image-block',
                fullWidth && 'full-width',
                openOnClick === 'lightbox' && !!imageUrl && 'advgb-lightbox',
                blockIDX,
            ].filter( Boolean ).join( ' ' );

            return (
                <div className={ blockClassName }
                     style={ {
                         backgroundImage: `url(${imageUrl})`,
                         backgroundPosition: focalPoint ? `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` : undefined,
                         height: height,
                         width: width,
                         justifyContent: vAlign,
                         alignItems: hAlign,
                     } }
                     data-image={ imageUrl }
                >
                    <a className="advgb-image-overlay"
                       style={ { backgroundColor: overlayColor } }
                       target={ linkInNewTab ? '_blank' : '_self' }
                       rel="noopener noreferrer"
                       href={ linkURL }
                    />
                    {title && (
                        <h4 className="advgb-image-title" style={ { color: titleColor } }>
                            {title}
                        </h4>
                    ) }
                    {subtitle && (
                        <p className="advgb-image-subtitle" style={ { color: subtitleColor } }>
                            {subtitle}
                        </p>
                    ) }
                </div>
            );
        },
        deprecated: [
            {
                attributes: {
                    ...blockAttrs,
                    overlayColor: {
                        type: 'string',
                        default: '#2196f3',
                    },
                    fullWidth: {
                        type: 'boolean',
                        default: false,
                    },
                },
                save: ( { attributes } ) => {
                    const {
                        blockIDX,
                        openOnClick,
                        openUrl,
                        linkInNewTab,
                        imageUrl,
                        title,
                        titleColor,
                        subtitle,
                        subtitleColor,
                        overlayColor,
                        fullWidth,
                        width,
                        height,
                        vAlign,
                        hAlign,
                        focalPoint,
                    } = attributes;
                    const linkURL = ( openOnClick === 'url' && !!openUrl ) ? openUrl : undefined;
                    const blockClassName = [
                        'advgb-image-block',
                        fullWidth && 'full-width',
                        openOnClick === 'lightbox' && !!imageUrl && 'advgb-lightbox',
                        blockIDX,
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <div className={ blockClassName }
                             style={ {
                                 backgroundImage: `url(${imageUrl})`,
                                 backgroundPosition: focalPoint ? `${ focalPoint.x * 100 }% ${ focalPoint.y * 100 }%` : undefined,
                                 height: height,
                                 width: width,
                                 justifyContent: vAlign,
                                 alignItems: hAlign,
                             } }
                             data-image={ imageUrl }
                        >
                            <a className="advgb-image-overlay"
                               style={ { backgroundColor: overlayColor } }
                               target={ linkInNewTab ? '_blank' : '_self' }
                               rel="noopener noreferrer"
                               href={ linkURL }
                            />
                            {title && (
                                <h4 className="advgb-image-title" style={ { color: titleColor } }>
                                    {title}
                                </h4>
                            ) }
                            {subtitle && (
                                <p className="advgb-image-subtitle" style={ { color: subtitleColor } }>
                                    {subtitle}
                                </p>
                            ) }
                        </div>
                    );
                }
            },
            {
                attributes: blockAttrs,
                save: ( { attributes } ) => {
                    const {
                        openOnClick,
                        openUrl,
                        linkInNewTab,
                        imageUrl,
                        title,
                        titleColor,
                        subtitle,
                        subtitleColor,
                        overlayColor,
                        fullWidth,
                        width,
                        height,
                        vAlign,
                        hAlign,
                    } = attributes;
                    const linkURL = ( openOnClick === 'url' && !!openUrl ) ? openUrl : undefined;
                    const blockClassName = [
                        'advgb-image-block',
                        fullWidth && 'full-width',
                        openOnClick === 'lightbox' && !!imageUrl && 'advgb-lightbox',
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <div className={ blockClassName }
                             style={ {
                                 backgroundImage: `url(${imageUrl})`,
                                 height: height,
                                 width: width,
                                 justifyContent: vAlign,
                                 alignItems: hAlign,
                             } }
                             data-image={ imageUrl }
                        >
                            <a className="advgb-image-overlay"
                               style={ { backgroundColor: overlayColor } }
                               target={ linkInNewTab ? '_blank' : '_self' }
                               rel="noopener noreferrer"
                               href={ linkURL }
                            />
                            {title && (
                                <h4 className="advgb-image-title" style={ { color: titleColor } }>
                                    {title}
                                </h4>
                            ) }
                            {subtitle && (
                                <p className="advgb-image-subtitle" style={ { color: subtitleColor } }>
                                    {subtitle}
                                </p>
                            ) }
                        </div>
                    );
                },
            }
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );