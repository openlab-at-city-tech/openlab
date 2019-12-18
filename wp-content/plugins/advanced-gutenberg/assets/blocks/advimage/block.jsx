(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, RichText, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const { RangeControl, PanelBody, ToggleControl, SelectControl, TextControl, IconButton, Button, Toolbar, FocalPointPicker } = wpComponents;

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
            } = attributes;
            const blockClassName = [
                'advgb-image-block',
                fullWidth && 'full-width',
                blockIDX,
            ].filter( Boolean ).join( ' ' );

            return (
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