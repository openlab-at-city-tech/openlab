(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const { RangeControl, ToggleControl, PanelBody, Tooltip } = wpComponents;
    const { times } = lodash;

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
            const { attributes, clientId } = this.props;
            const { sliderView } = attributes;

            if (sliderView) {
                jQuery(`#block-${clientId} .advgb-testimonial.slider-view`).slick({
                    infinite: true,
                    centerMode: true,
                    centerPadding: '40px',
                    slidesToShow: 3,
                });
            }
        }

        componentWillUpdate(nextProps) {
            const { sliderView: nextView, columns: nextColumns } = nextProps.attributes;
            const { attributes, clientId } = this.props;
            const { sliderView, columns } = attributes;

            if (nextView !== sliderView || nextColumns !== columns) {
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
            const { sliderView: prevView, columns: prevColumns } = prevProps.attributes;
            const { attributes, clientId } = this.props;
            const { sliderView, columns } = attributes;

            if (sliderView !== prevView || columns !== prevColumns) {
                if (sliderView) {
                    jQuery(`#block-${clientId} .advgb-testimonial.slider-view`).slick({
                        infinite: true,
                        centerMode: true,
                        centerPadding: '40px',
                        slidesToShow: 3,
                    });
                }
            }
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

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Testimonial Settings' ) }>
                            <ToggleControl
                                label={ __( 'Slider view' ) }
                                checked={ sliderView }
                                onChange={ () => setAttributes( { sliderView: !sliderView } ) }
                            />
                            <RangeControl
                                label={ __( 'Columns' ) }
                                help={ __( 'Columns range in Normal view is 1-3, and in Slider view is 4-10.' ) }
                                min={ minCols }
                                max={ maxCols }
                                value={ columns }
                                onChange={ (value) => setAttributes( { columns: value } ) }
                            />
                            <PanelBody title={ __( 'Avatar' ) } initialOpen={ false }>
                                <PanelColorSettings
                                    title={ __( 'Avatar Colors' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Background Color' ),
                                            value: avatarColor,
                                            onChange: ( value ) => setAttributes( { avatarColor: value } ),
                                        },
                                        {
                                            label: __( 'Border Color' ),
                                            value: avatarBorderColor,
                                            onChange: ( value ) => setAttributes( { avatarBorderColor: value } ),
                                        },
                                    ] }
                                />
                                <RangeControl
                                    label={ __( 'Border Radius (%)' ) }
                                    min={ 0 }
                                    max={ 50 }
                                    value={ avatarBorderRadius }
                                    onChange={ (value) => setAttributes( { avatarBorderRadius: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Border Width' ) }
                                    min={ 0 }
                                    max={ 5 }
                                    value={ avatarBorderWidth }
                                    onChange={ (value) => setAttributes( { avatarBorderWidth: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Avatar Size' ) }
                                    min={ 50 }
                                    max={ 130 }
                                    value={ avatarSize }
                                    onChange={ (value) => setAttributes( { avatarSize: value } ) }
                                />
                            </PanelBody>
                            <PanelColorSettings
                                title={ __( 'Text Colors' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Name Color' ),
                                        value: nameColor,
                                        onChange: ( value ) => setAttributes( { nameColor: value } ),
                                    },
                                    {
                                        label: __( 'Position Color' ),
                                        value: positionColor,
                                        onChange: ( value ) => setAttributes( { positionColor: value } ),
                                    },
                                    {
                                        label: __( 'Description Color' ),
                                        value: descColor,
                                        onChange: ( value ) => setAttributes( { descColor: value } ),
                                    },
                                ] }
                            />
                        </PanelBody>
                    </InspectorControls>
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
                                                <Tooltip text={ __( 'Click to change avatar' ) }>
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
                                                <Tooltip text={ __( 'Remove avatar' ) }>
                                                <span className="dashicons dashicons-no advgb-testimonial-avatar-clear"
                                                      onClick={ () => this.updateItems(idx, { avatarUrl: undefined, avatarID: undefined } ) }
                                                />
                                                </Tooltip>
                                            </div>
                                        ) }
                                    />
                                    <RichText
                                        tagName="h4"
                                        className="advgb-testimonial-name"
                                        value={ item.name }
                                        isSelected={ isSelected && currentEdit === 'name' + idx }
                                        unstableOnFocus={ () => this.setState( { currentEdit: 'name' + idx } ) }
                                        onChange={ (value) => this.updateItems(idx, { name: value } ) }
                                        style={ { color: nameColor } }
                                        placeholder={ __( 'Text…' ) }
                                    />
                                    <RichText
                                        tagName="p"
                                        className="advgb-testimonial-position"
                                        value={ item.position }
                                        isSelected={ isSelected && currentEdit === 'pos' + idx }
                                        unstableOnFocus={ () => this.setState( { currentEdit: 'pos' + idx } ) }
                                        onChange={ (value) => this.updateItems(idx, { position: value } ) }
                                        style={ { color: positionColor } }
                                        placeholder={ __( 'Text…' ) }
                                    />
                                    <RichText
                                        tagName="p"
                                        className="advgb-testimonial-desc"
                                        value={ item.desc }
                                        isSelected={ isSelected && currentEdit === 'desc' + idx }
                                        unstableOnFocus={ () => this.setState( { currentEdit: 'desc' + idx } ) }
                                        onChange={ (value) => this.updateItems(idx, { desc: value } ) }
                                        style={ { color: descColor } }
                                        placeholder={ __( 'Text…' ) }
                                    />
                                </div>
                        ) } ) }
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

    const blockAttrsOld = {
        avatarUrl: {
            type: 'string',
            default: advgbBlocks.avatarHolder,
        },
        avatarID: {
            type: 'number',
        },
        avatarUrl2: {
            type: 'string',
            default: advgbBlocks.avatarHolder,
        },
        avatarID2: {
            type: 'number',
        },
        avatarUrl3: {
            type: 'string',
            default: advgbBlocks.avatarHolder,
        },
        avatarID3: {
            type: 'number',
        },
        avatarUrl4: {
            type: 'string',
            default: advgbBlocks.avatarHolder,
        },
        avatarID4: {
            type: 'number',
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
            default: 70,
        },
        name: {
            type: 'string',
            default: __( 'Person Name' ),
        },
        name2: {
            type: 'string',
            default: __( 'Person Name' ),
        },
        name3: {
            type: 'string',
            default: __( 'Person Name' ),
        },
        name4: {
            type: 'string',
            default: __( 'Person Name' ),
        },
        nameColor: {
            type: 'string',
        },
        position: {
            type: 'string',
            default: __( 'Job Position' ),
        },
        position2: {
            type: 'string',
            default: __( 'Job Position' ),
        },
        position3: {
            type: 'string',
            default: __( 'Job Position' ),
        },
        position4: {
            type: 'string',
            default: __( 'Job Position' ),
        },
        positionColor: {
            type: 'string'
        },
        desc: {
            type: 'string',
            default: __( 'A little description about this person will show up here.' ),
        },
        desc2: {
            type: 'string',
            default: __( 'A little description about this person will show up here.' ),
        },
        desc3: {
            type: 'string',
            default: __( 'A little description about this person will show up here.' ),
        },
        desc4: {
            type: 'string',
            default: __( 'A little description about this person will show up here.' ),
        },
        descColor: {
            type: 'string',
        },
        columns: {
            type: 'number',
            default: 1,
        },
        changed: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType( 'advgb/testimonial', {
        title: __( 'Testimonial' ),
        description: __( 'Block for creating personal or team/group information.' ),
        icon: {
            src: testimonialBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'testimonial' ), __( 'personal' ), __( 'about' ) ],
        attributes: {
            ...blockAttrsOld,
            items: {
                type: 'array',
                default: times(10, () => ( {
                    avatarUrl: advgbBlocks.avatarHolder,
                    avatarID: undefined,
                    name: __( 'Person Name' ),
                    position: __( 'Job Position' ),
                    desc: __( 'A little description about this person will show up here.' ),
                } ) ),
            },
            sliderView: {
                type: 'boolean',
                default: false,
            },
        },
        edit: AdvTestimonial,
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
        },
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );