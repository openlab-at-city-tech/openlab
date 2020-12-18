(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const { RangeControl, ToggleControl, SelectControl, PanelBody, Tooltip } = wpComponents;
    const { times } = lodash;

    const PREV_ARROW = (
        <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24">
            <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/>
            <path fill="none" d="M0 0h24v24H0V0z"/>
        </svg>
    );

    const NEXT_ARROW = (
        <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24">
            <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
            <path fill="none" d="M0 0h24v24H0V0z"/>
        </svg>
    );

    const previewImageData = 'data:image/png;base64,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';

    class AdvTestimonial extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                currentEdit: '',
            }
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-testimonial'];

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
            const { pid, sliderView, sliderColumn, sliderCenterMode, sliderPauseOnHover, sliderInfiniteLoop,
                sliderDotsShown, sliderSpeed, sliderArrowShown, sliderItemsToScroll,
            } = attributes;

            if (!pid) {
                setAttributes( { pid: `advgb-testimonial-${clientId}` } );
            }

            if (sliderView) {
                jQuery(`#block-${clientId} .advgb-testimonial.slider-view`).slick({
                    infinite: sliderInfiniteLoop,
                    centerMode: sliderCenterMode,
                    slidesToShow: sliderColumn,
                    slidesToScroll: Math.min(sliderItemsToScroll, sliderColumn),
                    pauseOnHover: sliderPauseOnHover,
                    dots: sliderDotsShown,
                    arrows: sliderArrowShown,
                    speed: sliderSpeed,
                    prevArrow: jQuery(`#block-${clientId} .advgb-slider-prev`),
                    nextArrow: jQuery(`#block-${clientId} .advgb-slider-next`),
                });
            }
        }

        componentWillUpdate(nextProps) {
            const { attributes, clientId } = this.props;
            const { sliderView } = attributes;

            if (this.sliderNeedReload(nextProps.attributes, this.props.attributes)) {
                if (sliderView) {
                    jQuery(`#block-${clientId} .advgb-testimonial.slick-initialized`).slick('unslick');
                    jQuery(`#block-${clientId} .advgb-testimonial`)
                        .removeAttr('tabindex')
                        .removeAttr('role')
                        .removeAttr('aria-describedby');
                }
            }
        }

        componentDidUpdate(prevProps) {
            const { attributes, clientId } = this.props;
            const { sliderView, sliderColumn, sliderCenterMode, sliderPauseOnHover, sliderInfiniteLoop,
                sliderDotsShown, sliderSpeed, sliderArrowShown, sliderItemsToScroll,
            } = attributes;
            const needReload = this.sliderNeedReload(prevProps.attributes, this.props.attributes);
            const needUpdate = this.sliderNeedUpdate(prevProps.attributes, this.props.attributes);
            const slider = jQuery(`#block-${clientId} .advgb-testimonial.slider-view`);
            const prevElm = jQuery(`#block-${clientId} .advgb-slider-prev`);
            const nextElm = jQuery(`#block-${clientId} .advgb-slider-next`);

            if (needReload) {
                if (sliderView) {
                    slider.slick({
                        infinite: sliderInfiniteLoop,
                        centerMode: sliderCenterMode,
                        slidesToShow: sliderColumn,
                        slidesToScroll: Math.min(sliderItemsToScroll, sliderColumn),
                        pauseOnHover: sliderPauseOnHover,
                        dots: sliderDotsShown,
                        arrows: sliderArrowShown,
                        speed: sliderSpeed,
                        prevArrow: prevElm,
                        nextArrow: nextElm,
                    });
                }
            }

            if (needUpdate && sliderView) {
                slider.slick('slickSetOption', 'slidesToShow', sliderColumn);
                slider.slick('slickSetOption', 'slidesToScroll', sliderItemsToScroll);
                slider.slick('slickSetOption', 'centerMode', sliderCenterMode);
                slider.slick('slickSetOption', 'pauseOnHover', sliderPauseOnHover);
                slider.slick('slickSetOption', 'infinite', sliderInfiniteLoop);
                slider.slick('slickSetOption', 'dots', sliderDotsShown);
                slider.slick('slickSetOption', 'arrows', sliderArrowShown);
                slider.slick('slickSetOption', 'speed', sliderSpeed);
                slider.slick('slickSetOption', 'prevArrow', prevElm);
                slider.slick('slickSetOption', 'nextArrow', nextElm, true);
            }
        }

        sliderNeedReload(pa, ca) {
            const checkReload = ['sliderView', 'columns', 'avatarPosition', 'sliderCenterMode'];
            let reload = false;

            for (let checkProp of checkReload) {
                if (pa[checkProp] !== ca[checkProp]) {
                    reload = true;
                    break;
                }
            }

            return reload;
        }

        sliderNeedUpdate(pa, ca) {
            const checkUpdate = [
                'sliderColumn', 'sliderItemsToScroll', 'sliderPauseOnHover', 'sliderAutoPlay', 'sliderInfiniteLoop',
                'sliderDotsShown', 'sliderSpeed', 'sliderAutoPlaySpeed', 'sliderArrowShown',
            ];
            let update = false;

            for (let checkItem of checkUpdate) {
                if (pa[checkItem] !== ca[checkItem]) {
                    update = true;
                    break;
                }
            }

            return update;
        }

        updateItems(idx, data) {
            const { attributes, setAttributes } = this.props;
            const { items } = attributes;

            const newItems = items.map( (item, index) => {
                if (idx === index) item = { ...item, ...data };

                return item;
            } );

            setAttributes( { items: newItems } );
        }

        render() {
            const { currentEdit } = this.state;
            const { attributes, setAttributes, isSelected } = this.props;
            const {
                pid, items, sliderView, avatarColor, avatarBorderRadius, avatarBorderWidth, avatarBorderColor, avatarSize,
                nameColor, positionColor, descColor, columns, sliderColumn, sliderItemsToScroll, sliderCenterMode, sliderPauseOnHover,
                sliderAutoPlay, sliderInfiniteLoop, sliderDotsShown, sliderDotsColor, sliderSpeed, sliderAutoPlaySpeed,
                sliderArrowShown, sliderArrowSize, sliderArrowBorderSize, sliderArrowBorderRadius, sliderArrowColor, avatarPosition,
                isPreview
            } = attributes;

            const blockClass = [
                'advgb-testimonial',
                sliderView && 'slider-view',
                `advgb-avatar-${avatarPosition}`
            ].filter( Boolean ).join( ' ' );

            const maxCols  = sliderView ? 10 : 3;
            const minCols = sliderView ? 4 : 1;
            let i = 0;
            let validCols = columns;
            if (columns < 1) {
                validCols = 1;
            } else if (columns > 3 && !sliderView) {
                validCols = 3;
                setAttributes( { columns: 3 } );
            } else if (columns < 4 && sliderView) {
                validCols = 4;
                setAttributes( { columns: 4 } );
            } else if (columns > 10) {
                validCols = 10;
                setAttributes( { columns: 10 } );
            } else if (columns === '' || !columns) {
                validCols = sliderView ? 4 : 1;
            }

            const arrowStyle = {
                color: sliderArrowColor,
                borderColor: sliderArrowColor,
                borderWidth: sliderArrowBorderSize,
                borderRadius: sliderArrowBorderRadius ? `${sliderArrowBorderRadius}%` : undefined,
                width: sliderArrowSize,
            };

            return (
                isPreview ?
                    <img alt={__('Testimonial', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Testimonial Settings', 'advanced-gutenberg' ) }>
                            <ToggleControl
                                label={ __( 'Slider view', 'advanced-gutenberg' ) }
                                checked={ sliderView }
                                onChange={ () => setAttributes( { sliderView: !sliderView } ) }
                            />
                            <RangeControl
                                label={ !sliderView ? __( 'Columns', 'advanced-gutenberg' ) : __( 'Number of items', 'advanced-gutenberg' ) }
                                help={ __( 'Range in Normal view is 1-3, and in Slider view is 4-10.', 'advanced-gutenberg' ) }
                                min={ minCols }
                                max={ maxCols }
                                value={ columns }
                                onChange={ (value) => setAttributes( { columns: value } ) }
                            />
                            {sliderView && (
                            <Fragment>
                                <RangeControl
                                    label={ __( 'Items to show', 'advanced-gutenberg' ) }
                                    min={ 1 }
                                    max={ columns }
                                    value={ sliderColumn }
                                    onChange={ (value) => setAttributes( { sliderColumn: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Items to scroll', 'advanced-gutenberg' ) }
                                    min={ 1 }
                                    max={ sliderColumn }
                                    value={ sliderItemsToScroll }
                                    onChange={ (value) => setAttributes( { sliderItemsToScroll: value } ) }
                                />
                                <PanelBody title={ __( 'Slider Settings', 'advanced-gutenberg' ) } initialOpen={ false }>
                                    <ToggleControl
                                        label={ __( 'Center mode', 'advanced-gutenberg' ) }
                                        checked={ sliderCenterMode }
                                        onChange={ () => setAttributes( { sliderCenterMode: !sliderCenterMode } ) }
                                    />
                                    <ToggleControl
                                        label={ __( 'Pause on hover', 'advanced-gutenberg' ) }
                                        checked={ sliderPauseOnHover }
                                        onChange={ () => setAttributes( { sliderPauseOnHover: !sliderPauseOnHover } ) }
                                    />
                                    <ToggleControl
                                        label={ __( 'Auto play', 'advanced-gutenberg' ) }
                                        checked={ sliderAutoPlay }
                                        onChange={ () => setAttributes( { sliderAutoPlay: !sliderAutoPlay } ) }
                                    />
                                    {sliderAutoPlay && (
                                        <RangeControl
                                            label={ __( 'Autoplay speed (ms)', 'advanced-gutenberg' ) }
                                            min={ 0 }
                                            max={ 10000 }
                                            value={ sliderAutoPlaySpeed }
                                            onChange={ (value) => setAttributes( { sliderAutoPlaySpeed: value } ) }
                                        />
                                    )}
                                    <ToggleControl
                                        label={ __( 'Infinite Loop', 'advanced-gutenberg' ) }
                                        checked={ sliderInfiniteLoop }
                                        onChange={ () => setAttributes( { sliderInfiniteLoop: !sliderInfiniteLoop } ) }
                                    />
                                    <RangeControl
                                        label={ __( 'Transition speed (ms)', 'advanced-gutenberg' ) }
                                        min={ 0 }
                                        max={ 5000 }
                                        value={ sliderSpeed }
                                        onChange={ (value) => setAttributes( { sliderSpeed: value } ) }
                                    />
                                    <ToggleControl
                                        label={ __( 'Show dots', 'advanced-gutenberg' ) }
                                        checked={ sliderDotsShown }
                                        onChange={ () => setAttributes( { sliderDotsShown: !sliderDotsShown } ) }
                                    />
                                    <ToggleControl
                                        label={ __( 'Show arrows', 'advanced-gutenberg' ) }
                                        checked={ sliderArrowShown }
                                        onChange={ () => setAttributes( { sliderArrowShown: !sliderArrowShown } ) }
                                    />
                                    {sliderArrowShown && (
                                    <Fragment>
                                        <RangeControl
                                            label={ __( 'Arrow size', 'advanced-gutenberg' ) }
                                            min={ 40 }
                                            max={ 150 }
                                            value={ sliderArrowSize }
                                            onChange={ (value) => setAttributes( { sliderArrowSize: value } ) }
                                        />
                                        <RangeControl
                                            label={ __( 'Arrow border size', 'advanced-gutenberg' ) }
                                            min={ 0 }
                                            max={ 15 }
                                            value={ sliderArrowBorderSize }
                                            onChange={ (value) => setAttributes( { sliderArrowBorderSize: value } ) }
                                        />
                                        <RangeControl
                                            label={ __( 'Arrow border radius (%)', 'advanced-gutenberg' ) }
                                            min={ 0 }
                                            max={ 100 }
                                            value={ sliderArrowBorderRadius }
                                            onChange={ (value) => setAttributes( { sliderArrowBorderRadius: value } ) }
                                        />
                                    </Fragment>
                                    )}
                                </PanelBody>
                                <PanelColorSettings
                                    title={ __( 'Slider Colors', 'advanced-gutenberg' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Arrow and Border Color', 'advanced-gutenberg' ),
                                            value: sliderArrowColor,
                                            onChange: ( value ) => setAttributes( { sliderArrowColor: value } ),
                                        },
                                        {
                                            label: __( 'Dots Color', 'advanced-gutenberg' ),
                                            value: sliderDotsColor,
                                            onChange: ( value ) => setAttributes( { sliderDotsColor: value } ),
                                        },
                                    ] }
                                />
                            </Fragment>
                            )}
                            <PanelBody title={ __( 'Avatar', 'advanced-gutenberg' ) } initialOpen={ false }>
                                <PanelColorSettings
                                    title={ __( 'Avatar Colors', 'advanced-gutenberg' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Background Color', 'advanced-gutenberg' ),
                                            value: avatarColor,
                                            onChange: ( value ) => setAttributes( { avatarColor: value } ),
                                        },
                                        {
                                            label: __( 'Border Color', 'advanced-gutenberg' ),
                                            value: avatarBorderColor,
                                            onChange: ( value ) => setAttributes( { avatarBorderColor: value } ),
                                        },
                                    ] }
                                />
                                <RangeControl
                                    label={ __( 'Border Radius (%)', 'advanced-gutenberg' ) }
                                    min={ 0 }
                                    max={ 50 }
                                    value={ avatarBorderRadius }
                                    onChange={ (value) => setAttributes( { avatarBorderRadius: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Border Width', 'advanced-gutenberg' ) }
                                    min={ 0 }
                                    max={ 5 }
                                    value={ avatarBorderWidth }
                                    onChange={ (value) => setAttributes( { avatarBorderWidth: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Avatar Size', 'advanced-gutenberg' ) }
                                    min={ 50 }
                                    max={ 130 }
                                    value={ avatarSize }
                                    onChange={ (value) => setAttributes( { avatarSize: value } ) }
                                />
                                <SelectControl
                                    label={ __( 'Avatar Position', 'advanced-gutenberg' ) }
                                    value={ avatarPosition }
                                    options={ [
                                        {label: __( 'Top', 'advanced-gutenberg' ), value: 'top'},
                                        {label: __( 'Bottom', 'advanced-gutenberg' ), value: 'bottom'},
                                        {label: __( 'Left', 'advanced-gutenberg' ), value: 'left'},
                                        {label: __( 'right', 'advanced-gutenberg' ), value: 'right'},
                                    ] }
                                    onChange={ (value) => setAttributes( { avatarPosition: value } ) }
                                />
                            </PanelBody>
                            <PanelColorSettings
                                title={ __( 'Text Colors', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Name Color', 'advanced-gutenberg' ),
                                        value: nameColor,
                                        onChange: ( value ) => setAttributes( { nameColor: value } ),
                                    },
                                    {
                                        label: __( 'Position Color', 'advanced-gutenberg' ),
                                        value: positionColor,
                                        onChange: ( value ) => setAttributes( { positionColor: value } ),
                                    },
                                    {
                                        label: __( 'Description Color', 'advanced-gutenberg' ),
                                        value: descColor,
                                        onChange: ( value ) => setAttributes( { descColor: value } ),
                                    },
                                ] }
                            />
                        </PanelBody>
                    </InspectorControls>
                    <div className="advgb-testimonial-wrapper" id={pid}>
                        <div className={ blockClass }>
                            {items.map( (item, idx) => {
                                i++;
                                if (i > validCols) return false;
                                return (
                                    <div className="advgb-testimonial-item" key={idx}>
                                        <MediaUpload
                                            allowedTypes={ ["image"] }
                                            onSelect={ (media) => this.updateItems(idx, {
                                                avatarUrl: media.sizes.thumbnail ? media.sizes.thumbnail.url : media.sizes.full.url,
                                                avatarID: media.id
                                            } ) }
                                            value={ item.avatarID }
                                            render={ ( { open } ) => (
                                                <div className="advgb-testimonial-avatar-group">
                                                    <Tooltip text={ __( 'Click to change avatar', 'advanced-gutenberg' ) }>
                                                        <div className="advgb-testimonial-avatar"
                                                             onClick={ open }
                                                             style={ {
                                                                 backgroundImage: `url(${item.avatarUrl ? item.avatarUrl : advgbBlocks.avatarHolder})`,
                                                                 backgroundColor: avatarColor,
                                                                 borderRadius: avatarBorderRadius + '%',
                                                                 borderWidth: avatarBorderWidth + 'px',
                                                                 borderColor: avatarBorderColor,
                                                                 width: avatarSize + 'px',
                                                                 height: avatarSize + 'px',
                                                             } }
                                                        />
                                                    </Tooltip>
                                                    <Tooltip text={ __( 'Remove avatar', 'advanced-gutenberg' ) }>
                                                        <span className="dashicons dashicons-no advgb-testimonial-avatar-clear"
                                                              onClick={ () => this.updateItems(idx, { avatarUrl: undefined, avatarID: undefined } ) }
                                                        />
                                                    </Tooltip>
                                                </div>
                                            ) }
                                        />
                                        <div className="advgb-testimonial-info">
                                            <RichText
                                                tagName="h4"
                                                className="advgb-testimonial-name"
                                                value={ item.name }
                                                isSelected={ isSelected && currentEdit === 'name' + idx }
                                                unstableOnFocus={ () => this.setState( { currentEdit: 'name' + idx } ) }
                                                onChange={ (value) => this.updateItems(idx, { name: value } ) }
                                                style={ { color: nameColor } }
                                                placeholder={ __( 'Text…', 'advanced-gutenberg' ) }
                                            />
                                            <RichText
                                                tagName="p"
                                                className="advgb-testimonial-position"
                                                value={ item.position }
                                                isSelected={ isSelected && currentEdit === 'pos' + idx }
                                                unstableOnFocus={ () => this.setState( { currentEdit: 'pos' + idx } ) }
                                                onChange={ (value) => this.updateItems(idx, { position: value } ) }
                                                style={ { color: positionColor } }
                                                placeholder={ __( 'Text…', 'advanced-gutenberg' ) }
                                            />
                                            <RichText
                                                tagName="p"
                                                className="advgb-testimonial-desc"
                                                value={ item.desc }
                                                isSelected={ isSelected && currentEdit === 'desc' + idx }
                                                unstableOnFocus={ () => this.setState( { currentEdit: 'desc' + idx } ) }
                                                onChange={ (value) => this.updateItems(idx, { desc: value } ) }
                                                style={ { color: descColor } }
                                                placeholder={ __( 'Text…', 'advanced-gutenberg' ) }
                                            />
                                        </div>
                                    </div>
                                ) } ) }
                        </div>
                        {sliderView && (
                        <Fragment>
                            {sliderArrowShown && (
                                <Fragment>
                                    <button className="advgb-slider-arrow advgb-slider-prev"
                                            style={ arrowStyle }
                                    >
                                        {PREV_ARROW}
                                    </button>
                                    <button className="advgb-slider-arrow advgb-slider-next"
                                            style={ arrowStyle }
                                    >
                                        {NEXT_ARROW}
                                    </button>
                                </Fragment>
                            )}
                            <style>
                                {`#${pid} .slick-dots li.slick-active button:before {color: ${sliderDotsColor}}`}
                                {`#${pid} .slick-dots li button:before {color: ${sliderDotsColor}}`}
                            </style>
                        </Fragment>
                        )}
                    </div>
                </Fragment>
            )
        }
    }

    const testimonialBlockIcon = (
        <svg height="20" viewBox="2 2 22 22" width="20" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h4l3 3 3-3h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 3.3c1.49 0 2.7 1.21 2.7 2.7 0 1.49-1.21 2.7-2.7 2.7-1.49 0-2.7-1.21-2.7-2.7 0-1.49 1.21-2.7 2.7-2.7zM18 16H6v-.9c0-2 4-3.1 6-3.1s6 1.1 6 3.1v.9z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    );

    const blockAttrs = {
        items: {
            type: 'array',
            default: times(10, () => ( {
                avatarUrl: advgbBlocks.avatarHolder,
                avatarID: undefined,
                name: 'Person Name',
                position: 'Job Position',
                desc: 'A little description about this person will show up here.',
            } ) ),
        },
        pid: {
            type: 'string',
        },
        sliderView: {
            type: 'boolean',
            default: false,
        },
        avatarColor: {
            type: 'string',
        },
        avatarBorderRadius: {
            type: 'number',
            default: 50,
        },
        avatarBorderWidth: {
            type: 'number',
        },
        avatarBorderColor: {
            type: 'string',
        },
        avatarSize: {
            type: 'number',
            default: 120,
        },
        avatarPosition: {
            type: 'string',
            default: 'top',
        },
        nameColor: {
            type: 'string',
        },
        positionColor: {
            type: 'string'
        },
        descColor: {
            type: 'string',
        },
        columns: {
            type: 'number',
            default: 1,
        },
        sliderColumn: {
            type: 'number',
            default: 1,
        },
        sliderItemsToScroll: {
            type: 'number',
            default: 1,
        },
        sliderCenterMode: {
            type: 'boolean',
            default: false,
        },
        sliderPauseOnHover: {
            type: 'boolean',
            default: true,
        },
        sliderAutoPlay: {
            type: 'boolean',
            default: true,
        },
        sliderAutoPlaySpeed: {
            type: 'number',
            default: 2500,
        },
        sliderInfiniteLoop: {
            type: 'boolean',
            default: true,
        },
        sliderDotsShown: {
            type: 'boolean',
            default: true,
        },
        sliderDotsColor: {
            type: 'string',
        },
        sliderArrowShown: {
            type: 'boolean',
            default: true,
        },
        sliderArrowSize: {
            type: 'number',
            default: 50,
        },
        sliderArrowBorderSize: {
            type: 'number',
        },
        sliderArrowBorderRadius: {
            type: 'number',
        },
        sliderArrowColor: {
            type: 'string',
        },
        sliderSpeed: {
            type: 'number',
            default: 500,
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

    registerBlockType( 'advgb/testimonial', {
        title: __( 'Testimonial', 'advanced-gutenberg' ),
        description: __( 'Block for creating personal or team/group information.', 'advanced-gutenberg' ),
        icon: {
            src: testimonialBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'testimonial', 'advanced-gutenberg' ), __( 'personal', 'advanced-gutenberg' ), __( 'about', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        edit: AdvTestimonial,
        save: function ( { attributes } ) {
            const {
                pid, items, sliderView, avatarColor, avatarBorderRadius, avatarBorderWidth, avatarBorderColor, avatarSize,
                nameColor, positionColor, descColor, columns, sliderColumn, sliderItemsToScroll, sliderCenterMode, sliderPauseOnHover,
                sliderAutoPlay, sliderInfiniteLoop, sliderDotsShown, avatarPosition, sliderSpeed, sliderAutoPlaySpeed,
                sliderArrowShown, sliderArrowSize, sliderArrowBorderSize, sliderArrowBorderRadius, sliderArrowColor,
            } = attributes;

            const blockClass = [
                'advgb-testimonial',
                sliderView && 'slider-view',
                `advgb-avatar-${avatarPosition}`
            ].filter( Boolean ).join( ' ' );

            let i = 0;
            let validCols = columns;
            if (columns < 1) {
                validCols = 1;
            } else if (columns > 3 && !sliderView) {
                validCols = 3;
            } else if (columns < 4 && sliderView) {
                validCols = 4;
            } else if (columns > 10) {
                validCols = 10;
            }

            const arrowStyle = {
                color: sliderArrowColor,
                borderColor: sliderArrowColor,
                borderWidth: sliderArrowBorderSize,
                borderRadius: sliderArrowBorderRadius ? `${sliderArrowBorderRadius}%` : undefined,
                width: sliderArrowSize,
            };

            return (
                <div className="advgb-testimonial-wrapper" id={pid}
                     data-col={ sliderView ? sliderColumn : undefined }
                     data-scroll={ sliderView ? sliderItemsToScroll : undefined }
                     data-pause={ sliderView ? sliderPauseOnHover : undefined }
                     data-autoplay={ sliderView ? sliderAutoPlay : undefined }
                     data-apspeed={ sliderView ? sliderAutoPlaySpeed : undefined }
                     data-loop={ sliderView ? sliderInfiniteLoop : undefined }
                     data-dots={ sliderView ? sliderDotsShown : undefined }
                     data-speed={ sliderView ? sliderSpeed : undefined }
                     data-arrows={ sliderView ? sliderArrowShown : undefined }
                     data-center={ sliderView ? sliderCenterMode : undefined }
                >
                    <div className={ blockClass }>
                        {items.map( (item, idx) => {
                            i++;
                            if (i > validCols) return false;
                            return (
                                <div className="advgb-testimonial-item" key={idx}>
                                    <div className="advgb-testimonial-avatar-group">
                                        <div className="advgb-testimonial-avatar"
                                             style={ {
                                                 backgroundImage: `url(${item.avatarUrl ? item.avatarUrl : advgbBlocks.avatarHolder})`,
                                                 backgroundColor: avatarColor,
                                                 borderRadius: avatarBorderRadius + '%',
                                                 borderWidth: avatarBorderWidth + 'px',
                                                 borderColor: avatarBorderColor,
                                                 width: avatarSize + 'px',
                                                 height: avatarSize + 'px',
                                             } }
                                        />
                                    </div>
                                    <div className="advgb-testimonial-info">
                                        <h4 className="advgb-testimonial-name"
                                            style={ { color: nameColor } }
                                        >
                                            { item.name }
                                        </h4>
                                        <p className="advgb-testimonial-position"
                                           style={ { color: positionColor } }
                                        >
                                            { item.position }
                                        </p>
                                        <p className="advgb-testimonial-desc"
                                           style={ { color: descColor } }
                                        >
                                            { item.desc }
                                        </p>
                                    </div>
                                </div>
                            ) } ) }
                    </div>
                    {sliderView && (
                        <Fragment>
                            <button className="advgb-slider-arrow advgb-slider-prev"
                                    style={ arrowStyle }
                            >
                                {PREV_ARROW}
                            </button>
                            <button className="advgb-slider-arrow advgb-slider-next"
                                    style={ arrowStyle }
                            >
                                {NEXT_ARROW}
                            </button>
                        </Fragment>
                    )}
                </div>
            );
        },
        deprecated: [
            {
                attributes: {
                    ...blockAttrs,
                    avatarSize: {
                        type: 'number',
                        default: 70
                    }
                },
                save: function ( { attributes } ) {
                    const {
                        items,
                        sliderView,
                        avatarColor,
                        avatarBorderRadius,
                        avatarBorderWidth,
                        avatarBorderColor,
                        avatarSize,
                        nameColor,
                        positionColor,
                        descColor,
                        columns,
                    } = attributes;

                    const blockClass = [
                        'advgb-testimonial',
                        sliderView && 'slider-view',
                    ].filter( Boolean ).join( ' ' );

                    let i = 0;
                    let validCols = columns;
                    if (columns < 1) {
                        validCols = 1;
                    } else if (columns > 3 && !sliderView) {
                        validCols = 3;
                    } else if (columns < 4 && sliderView) {
                        validCols = 4;
                    } else if (columns > 10) {
                        validCols = 10;
                    }

                    return (
                        <div className={ blockClass }>
                            {items.map( (item, idx) => {
                                i++;
                                if (i > validCols) return false;
                                return (
                                    <div className="advgb-testimonial-item" key={idx}>
                                        <div className="advgb-testimonial-avatar-group">
                                            <div className="advgb-testimonial-avatar"
                                                 style={ {
                                                     backgroundImage: `url(${item.avatarUrl ? item.avatarUrl : advgbBlocks.avatarHolder})`,
                                                     backgroundColor: avatarColor,
                                                     borderRadius: avatarBorderRadius + '%',
                                                     borderWidth: avatarBorderWidth + 'px',
                                                     borderColor: avatarBorderColor,
                                                     width: avatarSize + 'px',
                                                     height: avatarSize + 'px',
                                                 } }
                                            />
                                        </div>
                                        <h4 className="advgb-testimonial-name"
                                            style={ { color: nameColor } }
                                        >
                                            { item.name }
                                        </h4>
                                        <p className="advgb-testimonial-position"
                                           style={ { color: positionColor } }
                                        >
                                            { item.position }
                                        </p>
                                        <p className="advgb-testimonial-desc"
                                           style={ { color: descColor } }
                                        >
                                            { item.desc }
                                        </p>
                                    </div>
                                ) } ) }
                        </div>
                    );
                }
            }
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );