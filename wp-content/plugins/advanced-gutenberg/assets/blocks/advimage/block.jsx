(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, RichText, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const { RangeControl, PanelBody, ToggleControl, SelectControl, TextControl, IconButton, Button, Toolbar } = wpComponents;

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

        render() {
            const { currentEdit } = this.state;
            const { attributes, setAttributes, isSelected } = this.props;
            const {
                openOnClick,
                openUrl,
                linkInNewTab,
                imageUrl,
                imageID,
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
            const blockClassName = [
                'advgb-image-block',
                fullWidth && 'full-width',
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
                                            label={ __( 'Change image' ) }
                                            icon="edit"
                                            onClick={ open }
                                        />
                                    ) }
                                />
                                <IconButton
                                    className="components-toolbar__control"
                                    label={ __( 'Remove image' ) }
                                    icon="no"
                                    onClick={ () => setAttributes( { imageUrl: undefined, imageID: undefined } ) }
                                />
                            </Toolbar>
                        </BlockControls>
                    ) }
                    <InspectorControls>
                        <PanelBody title={ __( 'Advanced Image' ) }>
                            <SelectControl
                                label={ __( 'Action on click' ) }
                                value={ openOnClick }
                                options={ [
                                    { label: __( 'None' ), value: 'none' },
                                    { label: __( 'Open image in lightbox' ), value: 'lightbox' },
                                    { label: __( 'Open custom URL' ), value: 'url' },
                                ] }
                                onChange={ (value) => setAttributes( { openOnClick: value } ) }
                            />
                            {openOnClick === 'url' &&
                            <Fragment>
                                <TextControl
                                    label={ [
                                        __( 'Link URL' ),
                                        (openUrl && <a href={ openUrl || '#' } key="advgb_image_link_url" target="_blank" style={ { float: 'right' } }>
                                            { __( 'Preview' ) }
                                        </a>)
                                    ] }
                                    value={ openUrl }
                                    placeholder={ __( 'Enter URL…' ) }
                                    onChange={ ( text ) => setAttributes( { openUrl: text } ) }
                                />
                                <ToggleControl
                                    label={ __( 'Open link in new tab' ) }
                                    checked={ linkInNewTab }
                                    onChange={ () => setAttributes( { linkInNewTab: !linkInNewTab } ) }
                                />
                            </Fragment>
                            }
                            <PanelBody title={ __( 'Image Size' ) }>
                                <ToggleControl
                                    label={ __( 'Full width' ) }
                                    checked={ fullWidth }
                                    onChange={ () => setAttributes( { fullWidth: !fullWidth } ) }
                                />
                                <RangeControl
                                    label={ __( 'Height' ) }
                                    value={ height }
                                    min={ 100 }
                                    max={ 1000 }
                                    onChange={ (value) => setAttributes( { height: value } ) }
                                />
                                {!fullWidth &&
                                <RangeControl
                                    label={ __( 'Width' ) }
                                    value={ width }
                                    min={ 200 }
                                    max={ 1300 }
                                    onChange={ (value) => setAttributes( { width: value } ) }
                                />
                                }
                            </PanelBody>
                            <PanelColorSettings
                                title={ __( 'Color Settings' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Title Color' ),
                                        value: titleColor,
                                        onChange: ( value ) => setAttributes( { titleColor: value === undefined ? '#fff' : value } ),
                                    },
                                    {
                                        label: __( 'Subtitle Color' ),
                                        value: subtitleColor,
                                        onChange: ( value ) => setAttributes( { subtitleColor: value === undefined ? '#fff' : value } ),
                                    },
                                    {
                                        label: __( 'Overlay Color' ),
                                        value: overlayColor,
                                        onChange: ( value ) => setAttributes( { overlayColor: value === undefined ? '#2196f3' : value } ),
                                    },
                                ] }
                            />
                            <PanelBody title={ __( 'Text Alignment' ) } initialOpen={false}>
                                <SelectControl
                                    label={ __( 'Vertical Alignment' ) }
                                    value={vAlign}
                                    options={ [
                                        { label: __( 'Top' ), value: 'flex-start' },
                                        { label: __( 'Center' ), value: 'center' },
                                        { label: __( 'Bottom' ), value: 'flex-end' },
                                    ] }
                                    onChange={ (value) => setAttributes( { vAlign: value } ) }
                                />
                                <SelectControl
                                    label={ __( 'Horizontal Alignment' ) }
                                    value={hAlign}
                                    options={ [
                                        { label: __( 'Left' ), value: 'flex-start' },
                                        { label: __( 'Center' ), value: 'center' },
                                        { label: __( 'Right' ), value: 'flex-end' },
                                    ] }
                                    onChange={ (value) => setAttributes( { hAlign: value } ) }
                                />
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                    <div className={ blockClassName }
                         style={ {
                             backgroundImage: `url( ${imageUrl})`,
                             height: height,
                             width: width,
                             justifyContent: vAlign,
                             alignItems: hAlign,
                         } }
                    >
                    <span className="advgb-image-overlay"
                          style={ { backgroundColor: overlayColor } }
                    />
                        {!imageID &&
                        <MediaUpload
                            allowedTypes={ ['image'] }
                            value={ imageID }
                            onSelect={ (image) => setAttributes( { imageUrl: image.url, imageID: image.id } ) }
                            render={ ( { open } ) => (
                                <Button
                                    className="button button-large"
                                    onClick={ open }
                                >
                                    { __( 'Choose image' ) }
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
                            placeholder={ __( 'Enter title…' ) }
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
                            placeholder={ __( 'Enter subtitle…' ) }
                        />
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
            default: __( 'Image title' ),
        },
        titleColor: {
            type: 'string',
            default: '#fff',
        },
        subtitle: {
            type: 'string',
            default: __( 'Your subtitle here' ),
        },
        subtitleColor: {
            type: 'string',
            default: '#fff'
        },
        overlayColor: {
            type: 'string',
            default: '#2196f3'
        },
        fullWidth: {
            type: 'boolean',
            default: false,
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
        changed: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType( 'advgb/image', {
        title: __( 'Advanced Image' ),
        description: __( 'Advanced image/photo block with more options and styles.' ),
        icon: {
            src: advImageBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'image' ), __( 'photo' ), __( 'box' ) ],
        attributes: blockAttrs,
        edit: AdvImage,
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
        deprecated: [
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
                                 backgroundImage: `url( ${imageUrl})`,
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
                            <h4 className="advgb-image-title" style={ { color: titleColor } }>
                                {title}
                            </h4>
                            <p className="advgb-image-subtitle" style={ { color: subtitleColor } }>
                                {subtitle}
                            </p>
                        </div>
                    );
                },
            }
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );