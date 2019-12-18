(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const { PanelBody, RangeControl, ToggleControl , SelectControl, TextControl, TextareaControl, IconButton, Button, Placeholder, Tooltip } = wpComponents;
    const $ = jQuery;

    const imageSliderBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22" className="dashicon">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path d="M20 4h-3.17L15 2H9L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM9.88 4h4.24l1.83 2H20v12H4V6h4.05"/>
            <path d="M15 11H9V8.5L5.5 12 9 15.5V13h6v2.5l3.5-3.5L15 8.5z"/>
        </svg>
    );

    class AdvImageSlider extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                currentSelected: 0,
                imageLoaded: false,
            };

            this.initSlider = this.initSlider.bind(this);
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-images-slider'];

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
            const { attributes } = this.props;

            if (attributes.images.length) {
                this.initSlider();
            }
        }

        componentWillUpdate( nextProps ) {
            const { clientId, attributes } = this.props;
            const { images } = attributes;
            const { images: nextImages } = nextProps.attributes;

            if ( images.length !== nextImages.length ) {
                $(`#block-${clientId} .advgb-images-slider.slick-initialized`).slick('unslick');
                $(`#block-${clientId} .advgb-image-slider-item`)
                    .removeAttr('tabindex')
                    .removeAttr('role')
                    .removeAttr('aria-describedby');
            }
        }

        componentDidUpdate( prevProps ) {
            const { attributes, clientId } = this.props;
            const { images } = attributes;
            const { images: prevImages } = prevProps.attributes;

            if (images.length !== prevImages.length) {
                if (images.length) {
                    this.initSlider();
                }
            }

            if (this.state.imageLoaded) {
                $(`#block-${clientId} .advgb-image-slider-image-list `)
                    .find('.advgb-image-slider-image-list-item:first-child')
                    .find('.advgb-image-slider-image-list-img')
                    .trigger('click');

                this.setState( { imageLoaded: null } )
            }
        }

        initSlider() {
            const { clientId } = this.props;

            $(`#block-${clientId} .advgb-images-slider:not(.slick-initialized)`).slick( {
                dots: true,
                adaptiveHeight: true,
            } );

            $(`#block-${clientId} .advgb-images-slider`).on('afterChange', (e, s, currentSlide) => {
                if (this.state.currentSelected !== currentSlide) {
                    this.setState( { currentSelected: currentSlide } );
                }
            } );
        }

        moveImage( currentIndex, newIndex ) {
            const { setAttributes, attributes } = this.props;
            const { images } = attributes;

            const image = images[currentIndex];
            setAttributes( {
                images: [
                    ...images.filter( (img, idx) => idx !== currentIndex ).slice(0, newIndex),
                    image,
                    ...images.filter( (img, idx) => idx !== currentIndex ).slice(newIndex),
                ]
            } );
        }

        updateImagesData(data) {
            const { currentSelected } = this.state;
            if (typeof currentSelected !== 'number') {
                return null;
            }

            const { attributes, setAttributes } = this.props;
            const { images } = attributes;

            const newImages = images.map( (image, index) => {
                if (index === currentSelected) {
                    image = { ...image, ...data };
                }

                return image;
            } );

            setAttributes( { images: newImages } );
        }

        render() {
            const { attributes, setAttributes, isSelected, clientId } = this.props;
            const { currentSelected, imageLoaded } = this.state;
            const {
                images,
                actionOnClick,
                fullWidth,
                autoHeight,
                width,
                height,
                alwaysShowOverlay,
                hoverColor,
                titleColor,
                textColor,
                hAlign,
                vAlign,
            } = attributes;

            if (images.length === 0) {
                return (
                    <Placeholder
                        icon={ imageSliderBlockIcon }
                        label={ __( 'Image Slider Block', 'advanced-gutenberg' ) }
                        instructions={ __( 'No images selected. Adding images to start using this block.', 'advanced-gutenberg' ) }
                    >
                        <MediaUpload
                            allowedTypes={ ['image'] }
                            value={ null }
                            multiple
                            onSelect={ (image) => {
                                const imgInsert = image.map( (img) => ( {
                                    url: img.url,
                                    id: img.id,
                                } ) );

                                setAttributes( {
                                    images: [
                                        ...images,
                                        ...imgInsert,
                                    ]
                                } )
                            } }
                            render={ ( { open } ) => (
                                <Button className="button button-large button-primary" onClick={ open }>
                                    { __( 'Add images', 'advanced-gutenberg' ) }
                                </Button>
                            ) }
                        />
                    </Placeholder>
                )
            }

            const blockClass = [
                'advgb-images-slider-block',
                imageLoaded === false && 'advgb-ajax-loading',
            ].filter( Boolean ).join(' ');

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Image Settings', 'advanced-gutenberg' ) }>
                            <SelectControl
                                label={ __( 'Action on click', 'advanced-gutenberg' ) }
                                value={ actionOnClick }
                                options={ [
                                    { label: __( 'None', 'advanced-gutenberg' ), value: '' },
                                    { label: __( 'Open image in lightbox', 'advanced-gutenberg' ), value: 'lightbox' },
                                    { label: __( 'Open custom link', 'advanced-gutenberg' ), value: 'link' },
                                ] }
                                onChange={ (value) => setAttributes( { actionOnClick: value } ) }
                            />
                            <ToggleControl
                                label={ __( 'Full width', 'advanced-gutenberg' ) }
                                checked={ fullWidth }
                                onChange={ () => setAttributes( { fullWidth: !fullWidth } ) }
                            />
                            <ToggleControl
                                label={ __( 'Auto height', 'advanced-gutenberg' ) }
                                checked={ autoHeight }
                                onChange={ () => setAttributes( { autoHeight: !autoHeight } ) }
                            />
                            {!fullWidth && (
                                <RangeControl
                                    label={ __( 'Width', 'advanced-gutenberg' ) }
                                    value={ width }
                                    onChange={ (value) => setAttributes( { width: value } ) }
                                    min={ 200 }
                                    max={ 1300 }
                                />
                            ) }
                            {!autoHeight && (
                                <RangeControl
                                    label={ __( 'Height', 'advanced-gutenberg' ) }
                                    value={ height }
                                    onChange={ (value) => setAttributes( { height: value } ) }
                                    min={ 100 }
                                    max={ 1000 }
                                />
                            ) }
                            <ToggleControl
                                label={ __( 'Always show overlay', 'advanced-gutenberg' ) }
                                checked=    { alwaysShowOverlay }
                                onChange={ () => setAttributes( { alwaysShowOverlay: !alwaysShowOverlay } ) }
                            />
                        </PanelBody>
                        <PanelColorSettings
                            title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                            colorSettings={ [
                                {
                                    label: __( 'Hover Color', 'advanced-gutenberg' ),
                                    value: hoverColor,
                                    onChange: ( value ) => setAttributes( { hoverColor: value } ),
                                },
                                {
                                    label: __( 'Title Color', 'advanced-gutenberg' ),
                                    value: titleColor,
                                    onChange: ( value ) => setAttributes( { titleColor: value } ),
                                },
                                {
                                    label: __( 'Text Color', 'advanced-gutenberg' ),
                                    value: textColor,
                                    onChange: ( value ) => setAttributes( { textColor: value } ),
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
                    </InspectorControls>
                    <div className={blockClass}>
                        <div className="advgb-images-slider">
                            {images.map( (image, index) => (
                                <div className="advgb-image-slider-item" key={index}>
                                    <img src={ image.url }
                                         className="advgb-image-slider-img"
                                         alt={ __( 'Slider image', 'advanced-gutenberg' ) }
                                         style={ {
                                             width: fullWidth ? '100%' : width,
                                             height: autoHeight ? 'auto' : height,
                                         } }
                                         onLoad={ () => {
                                             if (index === 0) {
                                                 if (this.state.imageLoaded === false) {
                                                     this.setState( { imageLoaded: true } )
                                                 }
                                             }
                                         } }
                                         onError={ () => {
                                             if (index === 0) {
                                                 if (this.state.imageLoaded === false) {
                                                     this.setState( { imageLoaded: true } )
                                                 }
                                             }
                                         } }
                                    />
                                    <div className="advgb-image-slider-item-info"
                                         style={ {
                                             justifyContent: vAlign,
                                             alignItems: hAlign,
                                         } }
                                    >
                                        <span className="advgb-image-slider-overlay"
                                              style={ {
                                                  backgroundColor: hoverColor,
                                                  opacity: alwaysShowOverlay ? 0.5 : undefined,
                                              } }
                                        />
                                        {image.title && (
                                            <h4 className="advgb-image-slider-title"
                                                style={ { color: titleColor } }
                                            >
                                                { image.title }
                                            </h4>
                                        ) }
                                        {image.text && (
                                            <p className="advgb-image-slider-text"
                                               style={ { color: textColor } }
                                            >
                                                { image.text }
                                            </p>
                                        ) }
                                    </div>
                                </div>
                            ) ) }
                        </div>
                        {isSelected && (
                        <div className="advgb-image-slider-controls">
                            <div className="advgb-image-slider-control">
                                <TextControl
                                    label={ __( 'Title', 'advanced-gutenberg' ) }
                                    value={ images[currentSelected] ? images[currentSelected].title || '' : '' }
                                    onChange={ (value) => this.updateImagesData( { title: value || '' } ) }
                                />
                            </div>
                            <div className="advgb-image-slider-control">
                                <TextareaControl
                                    label={ __( 'Text', 'advanced-gutenberg' ) }
                                    value={ images[currentSelected] ? images[currentSelected].text || '' : '' }
                                    onChange={ (value) => this.updateImagesData( { text: value || '' } ) }
                                />
                            </div>
                            {actionOnClick === 'link' && (
                                <div className="advgb-image-slider-control">
                                    <TextControl
                                        label={ __( 'Link', 'advanced-gutenberg' ) }
                                        value={ images[currentSelected] ? images[currentSelected].link || '' : '' }
                                        onChange={ (value) => this.updateImagesData( { link: value || '' } ) }
                                    />
                                </div>
                            ) }
                            <div className="advgb-image-slider-image-list">
                                {images.map( (image, index) => (
                                    <div className="advgb-image-slider-image-list-item" key={index}>
                                        {index > 0 && (
                                            <Tooltip text={ __( 'Move Left', 'advanced-gutenberg' ) }>
                                                <span className="advgb-move-arrow advgb-move-left"
                                                      onClick={ () => this.moveImage( index, index - 1 ) }
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                        <path fill="none" d="M0 0h24v24H0V0z"/>
                                                        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/>
                                                    </svg>
                                                </span>
                                            </Tooltip>
                                        ) }
                                        <img src={ image.url }
                                             className="advgb-image-slider-image-list-img"
                                             alt={ __( 'Image', 'advanced-gutenberg' ) }
                                             onClick={ () => {
                                                 $(`#block-${clientId} .advgb-images-slider`).slick('slickGoTo', index, false);
                                                 this.setState( { currentSelected: index } )
                                             } }
                                        />
                                        {index + 1 < images.length && (
                                            <Tooltip text={ __( 'Move Right', 'advanced-gutenberg' ) }>
                                                <span className="advgb-move-arrow advgb-move-right"
                                                      onClick={ () => this.moveImage( index, index + 1 ) }
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                        <path fill="none" d="M0 0h24v24H0V0z"/>
                                                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                                                    </svg>
                                                </span>
                                            </Tooltip>
                                        ) }
                                        <Tooltip text={ __( 'Remove image', 'advanced-gutenberg' ) }>
                                            <IconButton
                                                className="advgb-image-slider-image-list-item-remove"
                                                icon="no"
                                                onClick={ () => {
                                                    if (index === currentSelected) this.setState( { currentSelected: null } );
                                                    setAttributes( { images: images.filter( (img, idx) => idx !== index ) } )
                                                } }
                                            />
                                        </Tooltip>
                                    </div>
                                ) ) }
                                <div className="advgb-image-slider-add-item">
                                    <MediaUpload
                                        allowedTypes={ ['image'] }
                                        value={ currentSelected }
                                        multiple
                                        onSelect={ (imgs) => setAttributes( {
                                            images: [...images, ...imgs.map( (img) => lodash.pick( img, 'id', 'url' ) ) ],
                                        } ) }
                                        render={ ( { open } ) => (
                                            <IconButton
                                                label={ __( 'Add image', 'advanced-gutenberg' ) }
                                                icon="plus"
                                                onClick={ open }
                                            />
                                        ) }
                                    />
                                </div>
                            </div>
                        </div>
                        ) }
                    </div>
                </Fragment>
            )
        }
    }

    const blockAttrs = {
        images: {
            type: 'array',
            default: [], // [ { id: int, url, title, text, link: string } ]
        },
        actionOnClick: {
            type: 'string',
        },
        fullWidth: {
            type: 'boolean',
            default: true,
        },
        autoHeight: {
            type: 'boolean',
            default: true,
        },
        width: {
            type: 'number',
            default: 700,
        },
        height: {
            type: 'number',
            default: 500,
        },
        alwaysShowOverlay: {
            type: 'boolean',
            default: false,
        },
        hoverColor: {
            type: 'string',
        },
        titleColor: {
            type: 'string',
        },
        textColor: {
            type: 'string',
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
        }
    };

    registerBlockType( 'advgb/images-slider', {
        title: __( 'Images Slider', 'advanced-gutenberg' ),
        description: __( 'Display your images in a slider.', 'advanced-gutenberg' ),
        icon: {
            src: imageSliderBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'slide', 'advanced-gutenberg' ), __( 'gallery', 'advanced-gutenberg' ), __( 'photos', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        edit: AdvImageSlider,
        save: function ( { attributes } ) {
            const {
                images,
                actionOnClick,
                fullWidth,
                autoHeight,
                width,
                height,
                alwaysShowOverlay,
                hoverColor,
                titleColor,
                textColor,
                hAlign,
                vAlign,
            } = attributes;
            const blockClassName = [
                'advgb-images-slider-block',
                actionOnClick === 'lightbox' && 'advgb-images-slider-lightbox',
            ].filter( Boolean ).join( ' ' );

            return (
                <div className={ blockClassName }>
                    <div className="advgb-images-slider">
                        {images.map( (image, index) => (
                            <div className="advgb-image-slider-item" key={index}>
                                <img src={ image.url }
                                     className="advgb-image-slider-img"
                                     alt={ __( 'Slider image', 'advanced-gutenberg' ) }
                                     style={ {
                                         width: fullWidth ? '100%' : width,
                                         height: autoHeight ? 'auto' : height,
                                     } }
                                />
                                <div className="advgb-image-slider-item-info"
                                     style={ {
                                         justifyContent: vAlign,
                                         alignItems: hAlign,
                                     } }
                                >
                                    <a className="advgb-image-slider-overlay"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       href={ ( actionOnClick === 'link' && !!image.link ) ? image.link : undefined }
                                       style={ {
                                           backgroundColor: hoverColor,
                                           opacity: alwaysShowOverlay ? 0.5 : undefined,
                                       } }
                                    />
                                    {image.title && (
                                        <h4 className="advgb-image-slider-title"
                                            style={ { color: titleColor } }
                                        >
                                            { image.title }
                                        </h4>
                                    ) }
                                    {image.text && (
                                        <p className="advgb-image-slider-text"
                                           style={ { color: textColor } }
                                        >
                                            { image.text }
                                        </p>
                                    ) }
                                </div>
                            </div>
                        ) ) }
                    </div>
                </div>
            );
        },
        deprecated: [
            {
                attributes: blockAttrs,
                save: function ( { attributes } ) {
                    const {
                        images,
                        actionOnClick,
                        fullWidth,
                        autoHeight,
                        width,
                        height,
                        alwaysShowOverlay,
                        hoverColor,
                        titleColor,
                        textColor,
                        hAlign,
                        vAlign,
                    } = attributes;
                    const blockClassName = [
                        'advgb-images-slider-block',
                        actionOnClick === 'lightbox' && 'advgb-images-slider-lightbox',
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <div className={ blockClassName }>
                            <div className="advgb-images-slider">
                                {images.map( (image, index) => (
                                    <div className="advgb-image-slider-item" key={index}>
                                        <img src={ image.url }
                                             className="advgb-image-slider-img"
                                             alt={ __( 'Slider image', 'advanced-gutenberg' ) }
                                             style={ {
                                                 width: fullWidth ? '100%' : width,
                                                 height: autoHeight ? 'auto' : height,
                                             } }
                                        />
                                        <div className="advgb-image-slider-item-info"
                                             style={ {
                                                 justifyContent: vAlign,
                                                 alignItems: hAlign,
                                             } }
                                        >
                                            <a className="advgb-image-slider-overlay"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               href={ ( actionOnClick === 'link' && !!image.link ) ? image.link : undefined }
                                               style={ {
                                                   backgroundColor: hoverColor,
                                                   opacity: alwaysShowOverlay ? 0.5 : undefined,
                                               } }
                                            />
                                            <h4 className="advgb-image-slider-title"
                                                style={ { color: titleColor } }
                                            >
                                                { image.title }
                                            </h4>
                                            <p className="advgb-image-slider-text"
                                               style={ { color: textColor } }
                                            >
                                                { image.text }
                                            </p>
                                        </div>
                                    </div>
                                ) ) }
                            </div>
                        </div>
                    );
                },
            }
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );