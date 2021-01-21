(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents, wpCompose ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Fragment, Component } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { RichText, InnerBlocks, InspectorControls, PanelColorSettings } = wpBlockEditor;
    const { RangeControl, PanelBody, BaseControl , SelectControl, ToggleControl } = wpComponents;
    const { withDispatch, select } = wp.data;
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

    class AccordionItemEdit extends Component {
        constructor() {
            super( ...arguments );
        }

        componentWillMount() {
            const { attributes, setAttributes, clientId } = this.props;

            // Apply parent style if newly inserted
            if (attributes.changed !== true) {
                const { getBlockRootClientId, getBlockAttributes } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
                const rootBlockId = getBlockRootClientId( clientId );
                const rootBlockAttrs = getBlockAttributes( rootBlockId );

                if (rootBlockAttrs !== null && rootBlockAttrs.needUpdate !== false) {
                    Object.keys(rootBlockAttrs).map((attribute) => {
                        attributes[attribute] = rootBlockAttrs[attribute];
                    });

                    // Done applied, we will not do this again
                    setAttributes( { changed: true } );
                }
            }
        }

        render() {
            const { attributes, setAttributes } = this.props;
            const {
                header,
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
            } = attributes;

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Accordion Settings', 'advanced-gutenberg' ) }>
                            <RangeControl
                                label={ __( 'Bottom spacing', 'advanced-gutenberg' ) }
                                value={ marginBottom }
                                help={ __( 'Define space between each accordion (Frontend view only)', 'advanced-gutenberg' ) }
                                min={ 0 }
                                max={ 50 }
                                onChange={ ( value ) => this.props.updateRootBlockAttrs( { marginBottom: value } ) }
                            />
                            <ToggleControl
                                label={ __( 'Initial Collapsed', 'advanced-gutenberg' ) }
                                help={ __( 'Make all accordions collapsed by default.', 'advanced-gutenberg' ) }
                                checked={ collapsedAll }
                                onChange={ () => this.props.updateRootBlockAttrs({ collapsedAll: !collapsedAll })}
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Header Settings', 'advanced-gutenberg' ) }>
                            <BaseControl label={ __( 'Header Icon Style', 'advanced-gutenberg' ) }>
                                <div className="advgb-icon-items-wrapper">
                                    {Object.keys( HEADER_ICONS ).map( ( key, index ) => (
                                        <div className="advgb-icon-item" key={ index }>
                                                <span className={ key === headerIcon ? 'active' : '' }
                                                      onClick={ () => this.props.updateRootBlockAttrs( { headerIcon: key } ) }>
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
                                        onChange: ( value ) => this.props.updateRootBlockAttrs( { headerBgColor: value === undefined ? '#000' : value } ),
                                    },
                                    {
                                        label: __( 'Text Color', 'advanced-gutenberg' ),
                                        value: headerTextColor,
                                        onChange: ( value ) => this.props.updateRootBlockAttrs( { headerTextColor: value === undefined ? '#eee' : value } ),
                                    },
                                    {
                                        label: __( 'Icon Color', 'advanced-gutenberg' ),
                                        value: headerIconColor,
                                        onChange: ( value ) => this.props.updateRootBlockAttrs( { headerIconColor: value === undefined ? '#fff' : value } ),
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
                                    onChange: ( value ) => this.props.updateRootBlockAttrs( { bodyBgColor: value } ),
                                },
                                {
                                    label: __( 'Text Color', 'advanced-gutenberg' ),
                                    value: bodyTextColor,
                                    onChange: ( value ) => this.props.updateRootBlockAttrs( { bodyTextColor: value } ),
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
                                onChange={ ( value ) => this.props.updateRootBlockAttrs( { borderStyle: value } ) }
                            />
                            <PanelColorSettings
                                title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Border Color', 'advanced-gutenberg' ),
                                        value: borderColor,
                                        onChange: ( value ) => this.props.updateRootBlockAttrs( { borderColor: value } ),
                                    },
                                ] }
                            />
                            <RangeControl
                                label={ __( 'Border width', 'advanced-gutenberg' ) }
                                value={ borderWidth }
                                min={ 0 }
                                max={ 10 }
                                onChange={ ( value ) => this.props.updateRootBlockAttrs( { borderWidth: value } ) }
                            />
                            <RangeControl
                                label={ __( 'Border radius', 'advanced-gutenberg' ) }
                                value={ borderRadius }
                                min={ 0 }
                                max={ 100 }
                                onChange={ ( value ) => this.props.updateRootBlockAttrs( { borderRadius: value } ) }
                            />
                        </PanelBody>
                    </InspectorControls>
                    <div className="advgb-accordion-item">
                        <div className="advgb-accordion-header"
                             style={ {
                                 backgroundColor: headerBgColor,
                                 color: headerTextColor,
                                 borderStyle: borderStyle,
                                 borderWidth: borderWidth + 'px',
                                 borderColor: borderColor,
                                 borderRadius: borderRadius + 'px',
                             } }
                        >
                        <span className="advgb-accordion-header-icon">
                            <svg fill={ headerIconColor } xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                { HEADER_ICONS[headerIcon] }
                            </svg>
                        </span>
                            <RichText
                                tagName="h4"
                                value={ header }
                                onChange={ ( value ) => setAttributes( { header: value } ) }
                                unstableOnSplit={ () => null }
                                className="advgb-accordion-header-title"
                                placeholder={ __( 'Enter header…', 'advanced-gutenberg' ) }
                                style={ { color: 'inherit' } }
                            />
                        </div>
                        <div className="advgb-accordion-body"
                             style={ {
                                 backgroundColor: bodyBgColor,
                                 color: bodyTextColor,
                                 borderStyle: borderStyle,
                                 borderWidth: borderWidth + 'px',
                                 borderColor: borderColor,
                                 borderRadius: borderRadius + 'px',
                             } }
                        >
                            <InnerBlocks />
                        </div>
                    </div>
                </Fragment>
            )
        }
    }

    const accordionBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path fill="none" d="M0,0h24v24H0V0z"/>
            <rect x="3" y="17" width="18" height="2"/>
            <path d="M19,12v1H5v-1H19 M21,10H3v5h18V10L21,10z"/>
            <rect x="3" y="6" width="18" height="2"/>
        </svg>
    );

    registerBlockType( 'advgb/accordion-item', {
        title: __( 'Accordion Item', 'advanced-gutenberg' ),
        description: __( 'Easy to create an accordion for your post/page.', 'advanced-gutenberg' ),
        icon: {
            src: accordionBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        parent: [ 'advgb/accordions' ],
        category: 'advgb-category',
        keywords: [ __( 'accordion', 'advanced-gutenberg' ), __( 'list', 'advanced-gutenberg' ), __( 'faq', 'advanced-gutenberg' ) ],
        attributes: {
            header: {
                type: 'string',
                default: 'Header text',
            },
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
                default: 0,
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
            rootBlockId: {
                type: 'string',
                default: ''
            }
        },
        edit: compose([
            withDispatch( ( dispatch, { clientId }, { select } ) => {
                const {
                    getBlockRootClientId,
                    getBlocksByClientId
                } = select( 'core/block-editor' );
                const {
                    updateBlockAttributes,
                } = dispatch( 'core/block-editor' );
                const rootID = getBlockRootClientId( clientId );
                const accordionBlock = getBlocksByClientId( rootID );
                return {
                    updateRootBlockAttrs( attrs ) {
                        updateBlockAttributes( rootID, attrs);
                        times( accordionBlock[0].innerBlocks.length, n => {
                            updateBlockAttributes( accordionBlock[0].innerBlocks[ n ].clientId, attrs);
                        } );
                    },

                };
            } ),
        ]
        )(AccordionItemEdit),
        save: function ( { attributes } ) {
            const {
                header,
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
            } = attributes;

            return (
                <div className="advgb-accordion-item" style={ { marginBottom } }>
                    <div className="advgb-accordion-header"
                         style={ {
                             backgroundColor: headerBgColor,
                             color: headerTextColor,
                             borderStyle: borderStyle,
                             borderWidth: !!borderWidth ? borderWidth + 'px' : undefined,
                             borderColor: borderColor,
                             borderRadius: !!borderRadius ? borderRadius + 'px' : undefined,
                         } }
                    >
                        <span className="advgb-accordion-header-icon">
                            <svg fill={ headerIconColor } xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                { HEADER_ICONS[headerIcon] }
                            </svg>
                        </span>
                        <h4 className="advgb-accordion-header-title" style={ { color: 'inherit' } }>{ header }</h4>
                    </div>
                    <div className="advgb-accordion-body"
                         style={ {
                             backgroundColor: bodyBgColor,
                             color: bodyTextColor,
                             borderStyle: borderStyle + ' !important',
                             borderWidth: !!borderWidth ? borderWidth + 'px !important' : undefined,
                             borderColor: borderColor + ' !important',
                             borderTop: 'none !important',
                             borderRadius: !!borderRadius ? borderRadius + 'px !important' : undefined,
                         } }
                    >
                        <InnerBlocks.Content />
                    </div>
                </div>
            );
        },
        deprecated: [
            {
                attributes: {
                    header: {
                        type: 'string',
                        default: 'Header text',
                    },
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
                        default: 0,
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
                    rootBlockId: {
                        type: 'string',
                        default: ''
                    }
                },
                save: function ( { attributes } ) {
                    const {
                        header,
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
                    } = attributes;

                    return (
                        <div className="advgb-accordion-item" style={ { marginBottom } }>
                            <div className="advgb-accordion-header"
                                 style={ {
                                     backgroundColor: headerBgColor,
                                     color: headerTextColor,
                                     borderStyle: borderStyle,
                                     borderWidth: !!borderWidth ? borderWidth + 'px' : undefined,
                                     borderColor: borderColor,
                                     borderRadius: !!borderRadius ? borderRadius + 'px' : undefined,
                                 } }
                            >
                        <span className="advgb-accordion-header-icon">
                            <svg fill={ headerIconColor } xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                { HEADER_ICONS[headerIcon] }
                            </svg>
                        </span>
                                <h4 className="advgb-accordion-header-title" style={ { color: 'inherit' } }>{ header }</h4>
                            </div>
                            <div className="advgb-accordion-body"
                                 style={ {
                                     backgroundColor: bodyBgColor,
                                     color: bodyTextColor,
                                     borderStyle: borderStyle,
                                     borderWidth: !!borderWidth ? borderWidth + 'px' : undefined,
                                     borderColor: borderColor,
                                     borderRadius: !!borderRadius ? borderRadius + 'px' : undefined,
                                 } }
                            >
                                <InnerBlocks.Content />
                            </div>
                        </div>
                    );
                },
            }
        ]
    } )
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components, wp.compose );