(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents, wpCompose ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, PanelColorSettings, InnerBlocks } = wpBlockEditor;
    const { RangeControl, PanelBody, BaseControl , SelectControl, ToggleControl, ToolbarGroup, ToolbarButton } = wpComponents;
    const { withDispatch, select, dispatch } = wp.data;
    const { compose } = wpCompose;
    const { times } = lodash;

    const HEADER_ICONS = {
        plus: (
            <Fragment>
                <path fill="none" d="M0,0h24v24H0V0z"/>
                <path d="M19,13h-6v6h-2v-6H5v-2h6V5h2v6h6V13z"/>
            </Fragment>
        ),
        plusCircle: (
            <Fragment>
                <path fill="none" d="M0,0h24v24H0V0z"/>
                <path d="M12,2C6.48,2,2,6.48,2,12s4.48,10,10,10s10-4.48,10-10S17.52,2,12,2z M17,13h-4v4h-2v-4H7v-2h4V7h2v4h4V13z"/>
            </Fragment>
        ),
        plusCircleOutline: (
            <Fragment>
                <path fill="none" d="M0,0h24v24H0V0z"/>
                <path d="M13,7h-2v4H7v2h4v4h2v-4h4v-2h-4V7z M12,2C6.48,2,2,6.48,2,12s4.48,10,10,10s10-4.48,10-10S17.52,2,12,2z M12,20 c-4.41,0-8-3.59-8-8s3.59-8,8-8s8,3.59,8,8S16.41,20,12,20z"/>
            </Fragment>
        ),
        plusBox: (
            <Fragment>
                <path fill="none" d="M0,0h24v24H0V0z"/>
                <path d="M19,3H5C3.89,3,3,3.9,3,5v14c0,1.1,0.89,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3z M19,19H5V5h14V19z"/>
                <polygon points="11,17 13,17 13,13 17,13 17,11 13,11 13,7 11,7 11,11 7,11 7,13 11,13"/>
            </Fragment>
        ),
        unfold: (
            <Fragment>
                <path fill="none" d="M0,0h24v24H0V0z"/>
                <path d="M12,5.83L15.17,9l1.41-1.41L12,3L7.41,7.59L8.83,9L12,5.83z M12,18.17L8.83,15l-1.41,1.41L12,21l4.59-4.59L15.17,15 L12,18.17z"/>
            </Fragment>
        ),
        threeDots: (
            <Fragment>
                <path fill="none" d="M0,0h24v24H0V0z"/>
                <path d="M6,10c-1.1,0-2,0.9-2,2s0.9,2,2,2s2-0.9,2-2S7.1,10,6,10z M18,10c-1.1,0-2,0.9-2,2s0.9,2,2,2s2-0.9,2-2S19.1,10,18,10z M12,10c-1.1,0-2,0.9-2,2s0.9,2,2,2s2-0.9,2-2S13.1,10,12,10z"/>
            </Fragment>
        ),
        arrowDown: (
            <Fragment>
                <path opacity="0.87" fill="none" d="M24,24H0L0,0l24,0V24z"/>
                <path d="M16.59,8.59L12,13.17L7.41,8.59L6,10l6,6l6-6L16.59,8.59z"/>
            </Fragment>
        )
    };

    const previewImageData = 'data:image/png;base64,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';

    class AccordionsEdit extends Component {
        constructor() {
            super( ...arguments );

            this.updateAccordionAttrs = this.updateAccordionAttrs.bind( this );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-accordions'];

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
            const { setAttributes, clientId } = this.props;
            setAttributes({
                rootBlockId: clientId
            });
            this.props.updateAccordionAttributes( {rootBlockId: clientId} );
        }

        componentDidUpdate() {
            const { clientId } = this.props;
            const { removeBlock } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);

            if (childBlocks.length < 1) {
                // No accordion left, we will remove this block
                // removeBlock(clientId);
            }
        }

        updateAccordionAttrs( attrs ) {
            const { setAttributes, clientId } = this.props;
            const { updateBlockAttributes } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);

            setAttributes( attrs );
            childBlocks.forEach( childBlockId => updateBlockAttributes( childBlockId, attrs ) );
        }

        resyncAccordions() {
            const { attributes, clientId } = this.props;
            const { updateBlockAttributes } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);

            childBlocks.forEach( childBlockId => updateBlockAttributes( childBlockId, attributes ) );
        }

        render() {
            const { attributes, setAttributes } = this.props;
            const {
                headerBgColor,
                headerTextColor,
                headerIcon,
                headerIconColor,
                bodyBgColor,
                bodyTextColor,
                borderStyle,
                borderWidth,
                borderColor,
                borderRadius,
                marginBottom,
                collapsedAll,
                isPreview,
            } = attributes;

            return (
                isPreview ?
                    <img alt={__('Advanced Accordion', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <BlockControls>
                        <ToolbarGroup>
                            <ToolbarButton
                                icon="update"
                                onClick={ () => this.resyncAccordions() }
                                label={__('Refresh', 'advanced-gutenberg')}
                            />
                        </ToolbarGroup>
                    </BlockControls>
                    <InspectorControls>
                        <PanelBody title={ __( 'Accordion Settings', 'advanced-gutenberg' ) }>
                            <RangeControl
                                label={ __( 'Bottom spacing', 'advanced-gutenberg' ) }
                                value={ marginBottom }
                                help={ __( 'Define space between each accordion (Frontend view only)', 'advanced-gutenberg' ) }
                                min={ 0 }
                                max={ 50 }
                                onChange={ ( value ) => this.updateAccordionAttrs( { marginBottom: value } ) }
                            />
                            <ToggleControl
                                label={ __( 'Initial Collapsed', 'advanced-gutenberg' ) }
                                help={ __( 'Make all accordions collapsed by default.', 'advanced-gutenberg' ) }
                                checked={ collapsedAll }
                                onChange={ () => {
                                    setAttributes( { collapsedAll: !collapsedAll } );
                                    this.props.updateAccordionAttributes( {collapsedAll: !collapsedAll} );
                                } }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Header Settings', 'advanced-gutenberg' ) }>
                            <BaseControl label={ __( 'Header Icon Style', 'advanced-gutenberg' ) }>
                                <div className="advgb-icon-items-wrapper">
                                    {Object.keys( HEADER_ICONS ).map( ( key, index ) => (
                                        <div className="advgb-icon-item" key={ index }>
                                                <span className={ key === headerIcon ? 'active' : '' }
                                                      onClick={ () => this.updateAccordionAttrs( { headerIcon: key } ) }>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                        { HEADER_ICONS[key] }
                                                    </svg>
                                                </span>
                                        </div>
                                    ) ) }
                                </div>
                            </BaseControl>
                            <PanelColorSettings
                                title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Background Color', 'advanced-gutenberg' ),
                                        value: headerBgColor,
                                        onChange: ( value ) => this.updateAccordionAttrs( { headerBgColor: value === undefined ? '#000' : value } ),
                                    },
                                    {
                                        label: __( 'Text Color', 'advanced-gutenberg' ),
                                        value: headerTextColor,
                                        onChange: ( value ) => this.updateAccordionAttrs( { headerTextColor: value === undefined ? '#eee' : value } ),
                                    },
                                    {
                                        label: __( 'Icon Color', 'advanced-gutenberg' ),
                                        value: headerIconColor,
                                        onChange: ( value ) => this.updateAccordionAttrs( { headerIconColor: value === undefined ? '#fff' : value } ),
                                    },
                                ] }
                            />
                        </PanelBody>
                        <PanelColorSettings
                            title={ __( 'Body Color Settings', 'advanced-gutenberg' ) }
                            initialOpen={ false }
                            colorSettings={ [
                                {
                                    label: __( 'Background Color', 'advanced-gutenberg' ),
                                    value: bodyBgColor,
                                    onChange: ( value ) => this.updateAccordionAttrs( { bodyBgColor: value } ),
                                },
                                {
                                    label: __( 'Text Color', 'advanced-gutenberg' ),
                                    value: bodyTextColor,
                                    onChange: ( value ) => this.updateAccordionAttrs( { bodyTextColor: value } ),
                                },
                            ] }
                        />
                        <PanelBody title={ __( 'Border Settings', 'advanced-gutenberg' ) } initialOpen={ false }>
                            <SelectControl
                                label={ __( 'Border Style', 'advanced-gutenberg' ) }
                                value={ borderStyle }
                                options={ [
                                    { label: __( 'Solid', 'advanced-gutenberg' ), value: 'solid' },
                                    { label: __( 'Dashed', 'advanced-gutenberg' ), value: 'dashed' },
                                    { label: __( 'Dotted', 'advanced-gutenberg' ), value: 'dotted' },
                                ] }
                                onChange={ ( value ) => this.updateAccordionAttrs( { borderStyle: value } ) }
                            />
                            <PanelColorSettings
                                title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Border Color', 'advanced-gutenberg' ),
                                        value: borderColor,
                                        onChange: ( value ) => this.updateAccordionAttrs( { borderColor: value } ),
                                    },
                                ] }
                            />
                            <RangeControl
                                label={ __( 'Border width', 'advanced-gutenberg' ) }
                                value={ borderWidth }
                                min={ 0 }
                                max={ 10 }
                                onChange={ ( value ) => this.updateAccordionAttrs( { borderWidth: value } ) }
                            />
                            <RangeControl
                                label={ __( 'Border radius', 'advanced-gutenberg' ) }
                                value={ borderRadius }
                                min={ 0 }
                                max={ 100 }
                                onChange={ ( value ) => this.updateAccordionAttrs( { borderRadius: value } ) }
                            />
                        </PanelBody>
                    </InspectorControls>
                    <div className="advgb-accordions-wrapper">
                        <InnerBlocks
                            template={ [ ['advgb/accordion-item'], ['advgb/accordion-item'] ] }
                            templateLock={ false }
                            allowedBlocks={ [ 'advgb/accordion-item' ] }
                        />
                    </div>
                </Fragment>
            )
        }
    }

    const accordionBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path d="M0 0h24v24H0z" fill="none"/>
            <path d="M2 20h20v-4H2v4zm2-3h2v2H4v-2zM2 4v4h20V4H2zm4 3H4V5h2v2zm-4 7h20v-4H2v4zm2-3h2v2H4v-2z"/>
        </svg>
    );

    const blockAttrs = {
        headerBgColor: {
            type: 'string',
            default: '#000',
        },
        headerTextColor: {
            type: 'string',
            default: '#eee',
        },
        headerIcon: {
            type: 'string',
            default: 'unfold',
        },
        headerIconColor: {
            type: 'string',
            default: '#fff',
        },
        bodyBgColor: {
            type: 'string',
        },
        bodyTextColor: {
            type: 'string',
        },
        borderStyle: {
            type: 'string',
            default: 'solid',
        },
        borderWidth: {
            type: 'number',
            default: 1,
        },
        borderColor: {
            type: 'string',
        },
        borderRadius: {
            type: 'number',
            default: 2,
        },
        marginBottom: {
            type: 'number',
            default: 15,
        },
        collapsedAll: {
            type: 'boolean',
            default: false,
        },
        changed: {
            type: 'boolean',
            default: false,
        },
        needUpdate: {
            type: 'boolean',
            default: true,
        },
        isPreview: {
            type: 'boolean',
            default: false,
        },
        rootBlockId: {
            type: 'string',
            default: ''
        }
    };

    registerBlockType( 'advgb/accordions', {
        title: __( 'Advanced Accordion', 'advanced-gutenberg' ),
        description: __( 'Easy to create an accordion for your post/page.', 'advanced-gutenberg' ),
        icon: {
            src: accordionBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'accordion', 'advanced-gutenberg' ), __( 'list', 'advanced-gutenberg' ), __( 'faq', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        edit: compose(
            withDispatch( (dispatch, { clientId }, { select }) => {
                const {
                    getBlock,
                } = select( 'core/block-editor' );
                const {
                    updateBlockAttributes,
                } = dispatch( 'core/block-editor' );
                const block = getBlock( clientId );
                return {
                    updateAccordionAttributes( attrs ) {
                        times( block.innerBlocks.length, n => {
                            updateBlockAttributes( block.innerBlocks[ n ].clientId, attrs );
                        } );
                    },
                };
            })
        )(AccordionsEdit),
        save: function ( { attributes } ) {
            const { collapsedAll } = attributes;

            return (
                <div className="advgb-accordion-wrapper" data-collapsed={ collapsedAll ? collapsedAll : undefined }>
                    <InnerBlocks.Content />
                </div>
            );
        },
        deprecated: [
            {
                attributes: {
                    ...blockAttrs,
                    borderWidth: {
                        type: 'number',
                        default: 0,
                    }
                },
                save: function ( { attributes } ) {
                    const { collapsedAll } = attributes;

                    return (
                        <div className="advgb-accordion-wrapper" data-collapsed={ collapsedAll ? collapsedAll : undefined }>
                            <InnerBlocks.Content />
                        </div>
                    );
                },
            },
            {
                attributes: {
                    ...blockAttrs,
                    rootBlockId: {
                        type: 'string',
                        default: ''
                    }
                },
                save: function ( { attributes } ) {
                    const { collapsedAll } = attributes;

                    return (
                        <div className="advgb-accordion-wrapper" data-collapsed={ collapsedAll ? collapsedAll : undefined }>
                            <InnerBlocks.Content />
                        </div>
                    );
                }
            }
        ]
    } )
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components, wp.compose );
