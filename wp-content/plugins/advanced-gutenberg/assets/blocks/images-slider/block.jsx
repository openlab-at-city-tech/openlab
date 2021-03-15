(function (wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const {__} = wpI18n;
    const {Component, Fragment} = wpElement;
    const {registerBlockType} = wpBlocks;
    const {InspectorControls, PanelColorSettings, MediaUpload} = wpBlockEditor;
    const {PanelBody, RangeControl, ToggleControl, SelectControl, TextControl, TextareaControl, Button, Placeholder, Tooltip} = wpComponents;
    const $ = jQuery;

    const imageSliderBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22" className="dashicon">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path
                d="M20 4h-3.17L15 2H9L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM9.88 4h4.24l1.83 2H20v12H4V6h4.05"/>
            <path d="M15 11H9V8.5L5.5 12 9 15.5V13h6v2.5l3.5-3.5L15 8.5z"/>
        </svg>
    );

    const previewImageData = 'data:image/png;base64,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';

    class AdvImageSlider extends Component {
        constructor() {
            super(...arguments);
            this.state = {
                currentSelected: 0,
                imageLoaded: false,
            };

            this.initSlider = this.initSlider.bind(this);
        }

        componentWillMount() {
            const {attributes, setAttributes} = this.props;
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
                setAttributes({changed: true});
            }
        }

        componentDidMount() {
            const {attributes} = this.props;

            if (attributes.images.length) {
                this.initSlider();
            }
        }

        componentWillUpdate(nextProps) {
            const {clientId, attributes} = this.props;
            const {images} = attributes;
            const {images: nextImages} = nextProps.attributes;

            if (images.length !== nextImages.length) {
                $(`#block-${clientId} .advgb-images-slider.slick-initialized`).slick('unslick');
                $(`#block-${clientId} .advgb-image-slider-item`)
                    .removeAttr('tabindex')
                    .removeAttr('role')
                    .removeAttr('aria-describedby');
            }
        }

        componentDidUpdate(prevProps) {
            const {attributes, clientId} = this.props;
            const {images} = attributes;
            const {images: prevImages} = prevProps.attributes;

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

                this.setState({imageLoaded: null})
            }
        }

        initSlider() {
            const {clientId} = this.props;

            $(`#block-${clientId} .advgb-images-slider:not(.slick-initialized)`).slick({
                dots: true,
                adaptiveHeight: true,
            });

            $(`#block-${clientId} .advgb-images-slider`).on('afterChange', (e, s, currentSlide) => {
                if (this.state.currentSelected !== currentSlide) {
                    this.setState({currentSelected: currentSlide});
                }
            });
        }

        moveImage(currentIndex, newIndex) {
            const {setAttributes, attributes} = this.props;
            const {images} = attributes;

            const image = images[currentIndex];
            setAttributes({
                images: [
                    ...images.filter((img, idx) => idx !== currentIndex).slice(0, newIndex),
                    image,
                    ...images.filter((img, idx) => idx !== currentIndex).slice(newIndex),
                ]
            });
        }

        updateImagesData(data) {
            const {currentSelected} = this.state;
            if (typeof currentSelected !== 'number') {
                return null;
            }

            const {attributes, setAttributes} = this.props;
            const {images} = attributes;

            const newImages = images.map((image, index) => {
                if (index === currentSelected) {
                    image = {...image, ...data};
                }

                return image;
            });

            setAttributes({images: newImages});
        }

        render() {
            const {attributes, setAttributes, isSelected, clientId} = this.props;
            const {currentSelected, imageLoaded} = this.state;
            const {
                images,
                actionOnClick,
                fullWidth,
                autoHeight,
                width,
                height,
                alwaysShowOverlay,
                rtl,
                hoverColor,
                titleColor,
                textColor,
                hAlign,
                vAlign,
                isPreview,
            } = attributes;
            if (images.length === 0) {
                return (
                    isPreview ?
                        <img alt={__('Images Slider', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                        :
                        <Placeholder
                            icon={imageSliderBlockIcon}
                            label={__('Image Slider Block', 'advanced-gutenberg')}
                            instructions={__('No images selected. Adding images to start using this block.', 'advanced-gutenberg')}
                        >
                            <MediaUpload
                                allowedTypes={['image']}
                                value={null}
                                multiple
                                onSelect={(image) => {
                                    const imgInsert = image.map((img) => ({
                                        url: img.url,
                                        id: img.id,
                                    }));

                                    setAttributes({
                                        images: [
                                            ...images,
                                            ...imgInsert,
                                        ]
                                    })
                                }}
                                render={({open}) => (
                                    <Button className="button button-large button-primary" onClick={open}>
                                        {__('Add images', 'advanced-gutenberg')}
                                    </Button>
                                )}
                            />
                        </Placeholder>
                )
            }

            const blockClass = [
                'advgb-images-slider-block',
                imageLoaded === false && 'advgb-ajax-loading',
            ].filter(Boolean).join(' ');

            return (
                isPreview ?
                    <img alt={__('Images Slider', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                    <Fragment>
                        <InspectorControls>
                            <PanelBody title={__('Image Settings', 'advanced-gutenberg')}>
                                <SelectControl
                                    label={__('Action on click', 'advanced-gutenberg')}
                                    value={actionOnClick}
                                    options={[
                                        {label: __('None', 'advanced-gutenberg'), value: ''},
                                        {label: __('Open image in lightbox', 'advanced-gutenberg'), value: 'lightbox'},
                                        {label: __('Open custom link', 'advanced-gutenberg'), value: 'link'},
                                    ]}
                                    onChange={(value) => setAttributes({actionOnClick: value})}
                                />
                                <ToggleControl
                                    label={__('Full width', 'advanced-gutenberg')}
                                    checked={fullWidth}
                                    onChange={() => setAttributes({fullWidth: !fullWidth})}
                                />
                                <ToggleControl
                                    label={__('Auto height', 'advanced-gutenberg')}
                                    checked={autoHeight}
                                    onChange={() => setAttributes({autoHeight: !autoHeight})}
                                />
                                {!fullWidth && (
                                    <RangeControl
                                        label={__('Width', 'advanced-gutenberg')}
                                        value={width}
                                        onChange={(value) => setAttributes({width: value})}
                                        min={200}
                                        max={1300}
                                    />
                                )}
                                {!autoHeight && (
                                    <RangeControl
                                        label={__('Height', 'advanced-gutenberg')}
                                        value={height}
                                        onChange={(value) => setAttributes({height: value})}
                                        min={100}
                                        max={1000}
                                    />
                                )}
                                <ToggleControl
                                    label={__('Always show overlay', 'advanced-gutenberg')}
                                    checked={alwaysShowOverlay}
                                    onChange={() => setAttributes({alwaysShowOverlay: !alwaysShowOverlay})}
                                />
                                <ToggleControl
                                    label={__('Enable RTL', 'advanced-gutenberg')}
                                    checked={rtl}
                                    onChange={() => setAttributes({rtl: !rtl})}
                                />
                            </PanelBody>
                            <PanelColorSettings
                                title={__('Color Settings', 'advanced-gutenberg')}
                                colorSettings={[
                                    {
                                        label: __('Hover Color', 'advanced-gutenberg'),
                                        value: hoverColor,
                                        onChange: (value) => setAttributes({hoverColor: value}),
                                    },
                                    {
                                        label: __('Title Color', 'advanced-gutenberg'),
                                        value: titleColor,
                                        onChange: (value) => setAttributes({titleColor: value}),
                                    },
                                    {
                                        label: __('Text Color', 'advanced-gutenberg'),
                                        value: textColor,
                                        onChange: (value) => setAttributes({textColor: value}),
                                    },
                                ]}
                            />
                            <PanelBody title={__('Text Alignment', 'advanced-gutenberg')} initialOpen={false}>
                                <SelectControl
                                    label={__('Vertical Alignment', 'advanced-gutenberg')}
                                    value={vAlign}
                                    options={[
                                        {label: __('Top', 'advanced-gutenberg'), value: 'flex-start'},
                                        {label: __('Center', 'advanced-gutenberg'), value: 'center'},
                                        {label: __('Bottom', 'advanced-gutenberg'), value: 'flex-end'},
                                    ]}
                                    onChange={(value) => setAttributes({vAlign: value})}
                                />
                                <SelectControl
                                    label={__('Horizontal Alignment', 'advanced-gutenberg')}
                                    value={hAlign}
                                    options={[
                                        {label: __('Left', 'advanced-gutenberg'), value: 'flex-start'},
                                        {label: __('Center', 'advanced-gutenberg'), value: 'center'},
                                        {label: __('Right', 'advanced-gutenberg'), value: 'flex-end'},
                                    ]}
                                    onChange={(value) => setAttributes({hAlign: value})}
                                />
                            </PanelBody>
                        </InspectorControls>
                        <div className={blockClass}>
                            <div className="advgb-images-slider">
                                {images.map((image, index) => (
                                    <div className="advgb-image-slider-item" key={index}>
                                        <img src={image.url}
                                             className="advgb-image-slider-img"
                                             alt={'Slider image'}
                                             style={{
                                                 width: fullWidth ? '100%' : width,
                                                 height: autoHeight ? 'auto' : height,
                                             }}
                                             onLoad={() => {
                                                 if (index === 0) {
                                                     if (this.state.imageLoaded === false) {
                                                         this.setState({imageLoaded: true})
                                                     }
                                                 }
                                             }}
                                             onError={() => {
                                                 if (index === 0) {
                                                     if (this.state.imageLoaded === false) {
                                                         this.setState({imageLoaded: true})
                                                     }
                                                 }
                                             }}
                                        />
                                        <div className="advgb-image-slider-item-info"
                                             style={{
                                                 justifyContent: vAlign,
                                                 alignItems: hAlign,
                                             }}
                                        >
                                            {(actionOnClick !== '' || alwaysShowOverlay) && (
                                                <span className="advgb-image-slider-overlay"
                                                      style={{
                                                          backgroundColor: hoverColor,
                                                          opacity: alwaysShowOverlay ? 0.5 : undefined,
                                                      }}
                                                />)}
                                            {image.title && (
                                                <h4 className="advgb-image-slider-title"
                                                    style={{color: titleColor}}
                                                >
                                                    {image.title}
                                                </h4>
                                            )}
                                            {image.text && (
                                                <p className="advgb-image-slider-text"
                                                   style={{color: textColor}}
                                                >
                                                    {image.text}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                            {isSelected && (
                                <div className="advgb-image-slider-controls">
                                    <div className="advgb-image-slider-control">
                                        <TextControl
                                            label={__('Title', 'advanced-gutenberg')}
                                            value={images[currentSelected] ? images[currentSelected].title || '' : ''}
                                            onChange={(value) => this.updateImagesData({title: value || ''})}
                                        />
                                    </div>
                                    <div className="advgb-image-slider-control">
                                        <TextareaControl
                                            label={__('Text', 'advanced-gutenberg')}
                                            value={images[currentSelected] ? images[currentSelected].text || '' : ''}
                                            onChange={(value) => this.updateImagesData({text: value || ''})}
                                        />
                                    </div>
                                    {actionOnClick === 'link' && (
                                        <div className="advgb-image-slider-control">
                                            <TextControl
                                                label={__('Link', 'advanced-gutenberg')}
                                                value={images[currentSelected] ? images[currentSelected].link || '' : ''}
                                                onChange={(value) => this.updateImagesData({link: value || ''})}
                                            />
                                        </div>
                                    )}
                                    <div className="advgb-image-slider-image-list">
                                        {images.map((image, index) => (
                                            <div className="advgb-image-slider-image-list-item" key={index}>
                                                {index > 0 && (
                                                    <Tooltip text={__('Move Left', 'advanced-gutenberg')}>
                                                <span className="advgb-move-arrow advgb-move-left"
                                                      onClick={() => this.moveImage(index, index - 1)}
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                         viewBox="0 0 24 24">
                                                        <path fill="none" d="M0 0h24v24H0V0z"/>
                                                        <path
                                                            d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/>
                                                    </svg>
                                                </span>
                                                    </Tooltip>
                                                )}
                                                <img src={image.url}
                                                     className="advgb-image-slider-image-list-img"
                                                     alt={__('Image', 'advanced-gutenberg')}
                                                     onClick={() => {
                                                         $(`#block-${clientId} .advgb-images-slider`).slick('slickGoTo', index, false);
                                                         this.setState({currentSelected: index})
                                                     }}
                                                />
                                                {index + 1 < images.length && (
                                                    <Tooltip text={__('Move Right', 'advanced-gutenberg')}>
                                                <span className="advgb-move-arrow advgb-move-right"
                                                      onClick={() => this.moveImage(index, index + 1)}
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                         viewBox="0 0 24 24">
                                                        <path fill="none" d="M0 0h24v24H0V0z"/>
                                                        <path
                                                            d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                                                    </svg>
                                                </span>
                                                    </Tooltip>
                                                )}
                                                <Tooltip text={__('Remove image', 'advanced-gutenberg')}>
                                                    <Button
                                                        className="advgb-image-slider-image-list-item-remove"
                                                        icon="no"
                                                        onClick={() => {
                                                            if (index === currentSelected) this.setState({currentSelected: null});
                                                            setAttributes({images: images.filter((img, idx) => idx !== index)})
                                                        }}
                                                    />
                                                </Tooltip>
                                            </div>
                                        ))}
                                        <div className="advgb-image-slider-add-item">
                                            <MediaUpload
                                                allowedTypes={['image']}
                                                value={currentSelected}
                                                multiple
                                                onSelect={(imgs) => setAttributes({
                                                    images: [...images, ...imgs.map((img) => lodash.pick(img, 'id', 'url'))],
                                                })}
                                                render={({open}) => (
                                                    <Button
                                                        label={__('Add image', 'advanced-gutenberg')}
                                                        icon="plus"
                                                        onClick={open}
                                                    />
                                                )}
                                            />
                                        </div>
                                    </div>
                                </div>
                            )}
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
            default: '',
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
        rtl: {
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
        },
        isPreview: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType('advgb/images-slider', {
        title: __('Images Slider', 'advanced-gutenberg'),
        description: __('Display your images in a slider.', 'advanced-gutenberg'),
        icon: {
            src: imageSliderBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [__('slide', 'advanced-gutenberg'), __('gallery', 'advanced-gutenberg'), __('photos', 'advanced-gutenberg')],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            anchor: true
        },
        edit: AdvImageSlider,
        save: function ({attributes}) {
            const {
                images,
                actionOnClick,
                fullWidth,
                autoHeight,
                width,
                height,
                alwaysShowOverlay,
                rtl,
                hoverColor,
                titleColor,
                textColor,
                hAlign,
                vAlign,
            } = attributes;
            const blockClassName = [
                'advgb-images-slider-block',
                actionOnClick === 'lightbox' && 'advgb-images-slider-lightbox',
            ].filter(Boolean).join(' ');

            return (
                <div className={blockClassName}>
                    <div className="advgb-images-slider" data-slick={`{"rtl": ${rtl}}`}>
                        {images.map((image, index) => (
                            <div className="advgb-image-slider-item" key={index}>
                                <img src={image.url}
                                     className="advgb-image-slider-img"
                                     alt={'Slider image'}
                                     style={{
                                         width: fullWidth ? '100%' : width,
                                         height: autoHeight ? 'auto' : height,
                                     }}
                                />
                                <div className="advgb-image-slider-item-info"
                                     style={{
                                         justifyContent: vAlign,
                                         alignItems: hAlign,
                                     }}
                                >
                                    {(actionOnClick !== '' || alwaysShowOverlay) && (
                                        <a className="advgb-image-slider-overlay"
                                           target={actionOnClick !== '' ? '_blank' : false}
                                           rel="noopener noreferrer"
                                           href={(actionOnClick === 'link' && !!image.link) ? image.link : '#'}
                                           style={{
                                               backgroundColor: hoverColor,
                                               opacity: alwaysShowOverlay ? 0.5 : undefined,
                                           }}
                                        />)}
                                    {image.title && (
                                        <h4 className="advgb-image-slider-title"
                                            style={{color: titleColor}}
                                        >
                                            {image.title}
                                        </h4>
                                    )}
                                    {image.text && (
                                        <p className="advgb-image-slider-text"
                                           style={{color: textColor}}
                                        >
                                            {image.text}
                                        </p>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            );
        },
        deprecated: [
            {
                attributes: blockAttrs,
                save: function ({attributes}) {
                    const {
                        images,
                        actionOnClick,
                        fullWidth,
                        autoHeight,
                        width,
                        height,
                        alwaysShowOverlay,
                        rtl,
                        hoverColor,
                        titleColor,
                        textColor,
                        hAlign,
                        vAlign,
                    } = attributes;
                    const blockClassName = [
                        'advgb-images-slider-block',
                        actionOnClick === 'lightbox' && 'advgb-images-slider-lightbox',
                    ].filter(Boolean).join(' ');

                    return (
                        <div className={blockClassName}>
                            <div className="advgb-images-slider" data-slick={`{"rtl": ${rtl}}`}>
                                {images.map((image, index) => (
                                    <div className="advgb-image-slider-item" key={index}>
                                        <img src={image.url}
                                             className="advgb-image-slider-img"
                                             alt={'Slider image'}
                                             style={{
                                                 width: fullWidth ? '100%' : width,
                                                 height: autoHeight ? 'auto' : height,
                                             }}
                                        />
                                        <div className="advgb-image-slider-item-info"
                                             style={{
                                                 justifyContent: vAlign,
                                                 alignItems: hAlign,
                                             }}
                                        >
                                            <a className="advgb-image-slider-overlay"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               href={(actionOnClick === 'link' && !!image.link) ? image.link : '#'}
                                               style={{
                                                   backgroundColor: hoverColor,
                                                   opacity: alwaysShowOverlay ? 0.5 : undefined,
                                               }}
                                            />
                                            {image.title && (
                                                <h4 className="advgb-image-slider-title"
                                                    style={{color: titleColor}}
                                                >
                                                    {image.title}
                                                </h4>
                                            )}
                                            {image.text && (
                                                <p className="advgb-image-slider-text"
                                                   style={{color: textColor}}
                                                >
                                                    {image.text}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    );
                },
            },
            {
                attributes: blockAttrs,
                save: function ({attributes}) {
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
                    ].filter(Boolean).join(' ');

                    return (
                        <div className={blockClassName}>
                            <div className="advgb-images-slider">
                                {images.map((image, index) => (
                                    <div className="advgb-image-slider-item" key={index}>
                                        <img src={image.url}
                                             className="advgb-image-slider-img"
                                             alt={'Slider image'}
                                             style={{
                                                 width: fullWidth ? '100%' : width,
                                                 height: autoHeight ? 'auto' : height,
                                             }}
                                        />
                                        <div className="advgb-image-slider-item-info"
                                             style={{
                                                 justifyContent: vAlign,
                                                 alignItems: hAlign,
                                             }}
                                        >
                                            <a className="advgb-image-slider-overlay"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               href={(actionOnClick === 'link' && !!image.link) ? image.link : '#'}
                                               style={{
                                                   backgroundColor: hoverColor,
                                                   opacity: alwaysShowOverlay ? 0.5 : undefined,
                                               }}
                                            />
                                            {image.title && (
                                                <h4 className="advgb-image-slider-title"
                                                    style={{color: titleColor}}
                                                >
                                                    {image.title}
                                                </h4>
                                            )}
                                            {image.text && (
                                                <p className="advgb-image-slider-text"
                                                   style={{color: textColor}}
                                                >
                                                    {image.text}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    );
                },
            },
            {
                attributes: blockAttrs,
                save: function ({attributes}) {
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
                    ].filter(Boolean).join(' ');

                    return (
                        <div className={blockClassName}>
                            <div className="advgb-images-slider">
                                {images.map((image, index) => (
                                    <div className="advgb-image-slider-item" key={index}>
                                        <img src={image.url}
                                             className="advgb-image-slider-img"
                                             alt={'Slider image'}
                                             style={{
                                                 width: fullWidth ? '100%' : width,
                                                 height: autoHeight ? 'auto' : height,
                                             }}
                                        />
                                        <div className="advgb-image-slider-item-info"
                                             style={{
                                                 justifyContent: vAlign,
                                                 alignItems: hAlign,
                                             }}
                                        >
                                            <a className="advgb-image-slider-overlay"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               href={(actionOnClick === 'link' && !!image.link) ? image.link : undefined}
                                               style={{
                                                   backgroundColor: hoverColor,
                                                   opacity: alwaysShowOverlay ? 0.5 : undefined,
                                               }}
                                            />
                                            <h4 className="advgb-image-slider-title"
                                                style={{color: titleColor}}
                                            >
                                                {image.title}
                                            </h4>
                                            <p className="advgb-image-slider-text"
                                               style={{color: textColor}}
                                            >
                                                {image.text}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    );
                },
            }
        ]
    });
})(wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components);