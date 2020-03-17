(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, PanelColorSettings, AlignmentToolbar, InnerBlocks } = wpBlockEditor;
    const { PanelBody, RangeControl, BaseControl, SelectControl } = wpComponents;
    const { select } = wp.data;

    const columnsBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
            <path d="M10 18h5V5h-5v13zm-6 0h5V5H4v13zM16 5v13h5V5h-5z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    );
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
    const MARGIN_PADDING_CONTROLS = [
        {label:'Top', icon: 'arrow-up-alt2'},
        {label:'Right', icon: 'arrow-right-alt2'},
        {label:'Bottom', icon: 'arrow-down-alt2'},
        {label:'Left', icon: 'arrow-left-alt2'},
    ];

    class AdvColumnEdit extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                tabSelected: 'desktop',
            };
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-column'];

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
                setAttributes( { colId: 'advgb-col-' + clientId, } )
            }
        }

        static jsUcfirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        render() {
            const { tabSelected } = this.state;
            const { attributes, setAttributes, clientId, className } = this.props;
            const {
                width,
                borderColor, borderStyle, borderWidth, borderRadius,
                textAlign, textAlignM,
                marginTop, marginRight, marginBottom, marginLeft,
                marginTopM, marginRightM, marginBottomM, marginLeftM,
                paddingTop, paddingRight, paddingBottom, paddingLeft,
                paddingTopM, paddingRightM, paddingBottomM, paddingLeftM,
            } = attributes;
            const { getBlockOrder, getBlockRootClientId, getBlockAttributes } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const hasChildBlocks = getBlockOrder( clientId ).length > 0;
            const rootBlockId = getBlockRootClientId( clientId );
            const rootChildBlocks = getBlockOrder(rootBlockId).filter( blockId => blockId !== clientId );
            let avaiWidth = 100;
            rootChildBlocks.map( ( blockId ) => {
                const width = getBlockAttributes(blockId).width || 0;
                avaiWidth -= parseInt(width);
            } );

            const blockClasses = [
                'advgb-column',
                className,
            ].filter( Boolean ).join( ' ' );

            let deviceLetter = '';
            if (tabSelected === 'mobile') deviceLetter = 'M';

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Column Settings', 'advanced-gutenberg' ) }>
                            <RangeControl
                                label={ [
                                    __( 'Width (%)', 'advanced-gutenberg' ),
                                    <span key="width" style={ { color: '#555d66', marginLeft: 10 } }>{ __( 'Available: ', 'advanced-gutenberg' ) + avaiWidth + '%' }</span>
                                ] }
                                help={ __( 'Set to 0 = auto. This will override predefine layout styles. Recommend for experience users!', 'advanced-gutenberg' ) }
                                value={ width }
                                min={ 0 }
                                max={ avaiWidth }
                                onChange={ (value) => setAttributes( { width: value } ) }
                            />
                            <PanelBody title={ __( 'Border Settings', 'advanced-gutenberg' ) }>
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
                                                    onChange: ( value ) => setAttributes( { borderColor: value } ),
                                                },
                                            ] }
                                        />
                                        <RangeControl
                                            label={ __( 'Border width', 'advanced-gutenberg' ) }
                                            value={ borderWidth || '' }
                                            onChange={ ( value ) => setAttributes( { borderWidth: value } ) }
                                            min={ 0 }
                                            max={ 20 }
                                        />
                                        <RangeControl
                                            label={ __( 'Border radius (px)', 'advanced-gutenberg' ) }
                                            value={ borderRadius || '' }
                                            onChange={ ( value ) => setAttributes( { borderRadius: value } ) }
                                            min={ 0 }
                                            max={ 100 }
                                        />
                                    </Fragment>
                                ) }
                            </PanelBody>

                            <div className="advgb-columns-responsive-items"
                                 style={ { borderTop: '2px solid #aaa', paddingTop: 10 } }
                            >
                                {['desktop', 'mobile'].map( (device, index) => {
                                    const itemClasses = [
                                        "advgb-columns-responsive-item",
                                        tabSelected === device && 'is-selected',
                                    ].filter( Boolean ).join( ' ' );

                                    return (
                                        <div className={ itemClasses }
                                             key={ index }
                                             onClick={ () => this.setState( { tabSelected: device } ) }
                                        >
                                            {device}
                                        </div>
                                    )
                                } ) }
                            </div>
                            <BaseControl
                                label={ AdvColumnEdit.jsUcfirst(tabSelected) + __( ' text alignment', 'advanced-gutenberg' ) }
                            >
                                <AlignmentToolbar
                                    isCollapsed={ false }
                                    value={ attributes[ 'textAlign' + deviceLetter ] }
                                    onChange={ (align) => setAttributes( { ['textAlign' + deviceLetter]: align } ) }
                                />
                            </BaseControl>
                            <PanelBody title={ tabSelected !== 'desktop' ? AdvColumnEdit.jsUcfirst(tabSelected) + __(' Padding', 'advanced-gutenberg') : __('Padding', 'advanced-gutenberg') }
                                       initialOpen={false}
                            >
                                <div className="advgb-controls-title">{ __( 'Unit (px)', 'advanced-gutenberg' ) }</div>
                                {MARGIN_PADDING_CONTROLS.map((pos, idx) => (
                                    <RangeControl
                                        key={ idx }
                                        beforeIcon={ pos.icon }
                                        value={ attributes['padding' + pos.label + deviceLetter] || '' }
                                        min={ 0 }
                                        max={ 50 }
                                        onChange={ (value) => setAttributes( { ['padding' + pos.label + deviceLetter]: value } ) }
                                    />
                                ) ) }
                            </PanelBody>
                            <PanelBody title={ tabSelected !== 'desktop' ? AdvColumnEdit.jsUcfirst(tabSelected) + __(' Margin', 'advanced-gutenberg') : __('Margin', 'advanced-gutenberg') }
                                       initialOpen={false}
                            >
                                <div className="advgb-controls-title">{ __( 'Unit (px)', 'advanced-gutenberg' ) }</div>
                                {MARGIN_PADDING_CONTROLS.map((pos, idx) => (
                                    <RangeControl
                                        key={ idx }
                                        beforeIcon={ pos.icon }
                                        value={ attributes['margin' + pos.label + deviceLetter] || '' }
                                        min={ 0 }
                                        max={ 50 }
                                        onChange={ (value) => setAttributes( { ['margin' + pos.label + deviceLetter]: value } ) }
                                    />
                                ) ) }
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                    <div className={ blockClasses }>
                        <div className="advgb-column-inner"
                             style={ {
                                 borderStyle, borderColor, borderWidth, borderRadius,
                             } }
                        >
                            <InnerBlocks
                                templateLock={ false }
                                renderAppender={ (
                                    hasChildBlocks ?
                                        undefined :
                                        () => <InnerBlocks.ButtonBlockAppender />
                                ) }
                            />
                        </div>
                    </div>
                    <style>
                        {`#block-${clientId} .advgb-column > .advgb-column-inner {
                            text-align: ${textAlign};
                            margin-top: ${marginTop}px;
                            margin-right: ${marginRight}px;
                            margin-bottom: ${marginBottom}px;
                            margin-left: ${marginLeft}px;
                            padding-top: ${paddingTop}px;
                            padding-right: ${paddingRight}px;
                            padding-bottom: ${paddingBottom}px;
                            padding-left: ${paddingLeft}px;
                        }
                        @media screen and (max-width: 767px) {
                            #block-${clientId} .advgb-column > .advgb-column-inner {
                                text-align: ${textAlignM};
                                margin-top: ${marginTopM}px;
                                margin-right: ${marginRightM}px;
                                margin-bottom: ${marginBottomM}px;
                                margin-left: ${marginLeftM}px;
                                padding-top: ${paddingTopM}px;
                                padding-right: ${paddingRightM}px;
                                padding-bottom: ${paddingBottomM}px;
                                padding-left: ${paddingLeftM}px;
                            }
                        }
                        ${width ?
                            `#block-${clientId} {flex-basis: ${width}%;}` : ''}`
                        }
                    </style>
                </Fragment>
            )
        }
    }

    const blockAttrs = {
        width: {
            type: 'number',
        },
        columnClasses: {
            type: 'string',
        },
        colId: {
            type: 'string',
        },
        borderStyle: {
            type: 'string',
            default: 'none',
        },
        borderColor: {
            type: 'string',
        },
        borderWidth: {
            type: 'number',
            default: 1,
        },
        borderRadius: {
            type: 'number',
        },
        textAlign: {
            type: 'string',
        },
        textAlignM: {
            type: 'string',
        },
        marginTop: {
            type: 'number',
        },
        marginTopM: {
            type: 'number',
        },
        marginRight: {
            type: 'number',
        },
        marginRightM: {
            type: 'number',
        },
        marginBottom: {
            type: 'number',
        },
        marginBottomM: {
            type: 'number',
        },
        marginLeft: {
            type: 'number',
        },
        marginLeftM: {
            type: 'number',
        },
        paddingTop: {
            type: 'number',
        },
        paddingTopM: {
            type: 'number',
        },
        paddingRight: {
            type: 'number',
        },
        paddingRightM: {
            type: 'number',
        },
        paddingBottom: {
            type: 'number',
        },
        paddingBottomM: {
            type: 'number',
        },
        paddingLeft: {
            type: 'number',
        },
        paddingLeftM: {
            type: 'number',
        },
        changed: {
            type: 'boolean',
            default: false,
        }
    };

    registerBlockType( 'advgb/column', {
        title: __( 'Adv. Column', 'advanced-gutenberg' ),
        parent: [ 'advgb/columns' ],
        description: __( 'Column in row.', 'advanced-gutenberg' ),
        icon: {
            src: columnsBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'columns', 'advanced-gutenberg' ), __( 'row', 'advanced-gutenberg' ), __( 'layout', 'advanced-gutenberg' ) ],
        supports: {
            inserter: false,
            reusable: false,
            html: false,
        },
        attributes: blockAttrs,
        edit: AdvColumnEdit,
        save: function ( { attributes } ) {
            const {
                width,
                columnClasses, colId,
                borderColor, borderStyle, borderWidth, borderRadius,
            } = attributes;

            const blockClasses = [
                'advgb-column',
                columnClasses,
            ].filter( Boolean ).join( ' ' );

            return (
                <div className={ blockClasses }
                     id={ colId }
                     style={ {
                         flex: width ? 'none' : undefined,
                     } }
                >
                    <div className="advgb-column-inner"
                         style={ { borderStyle, borderColor, borderWidth, borderRadius, } }
                    >
                        <InnerBlocks.Content />
                    </div>
                </div>
            );
        },
        deprecated: [
            {
                attributes: blockAttrs,
                save: function ( { attributes } ) {
                    const {
                        width,
                        columnClasses, colId,
                        borderColor, borderStyle, borderWidth, borderRadius,
                    } = attributes;

                    const blockClasses = [
                        'advgb-column',
                        columnClasses,
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <div className={ blockClasses }
                             id={ colId }
                             style={ {
                                 width: width ? width + '%' : undefined,
                                 flex: width ? 'none' : undefined,
                             } }
                        >
                            <div className="advgb-column-inner"
                                 style={ { borderStyle, borderColor, borderWidth, borderRadius, } }
                            >
                                <InnerBlocks.Content />
                            </div>
                        </div>
                    );
                },
            },
            {
                attributes: blockAttrs,
                save: function ( { attributes } ) {
                    const {
                        width,
                        columnClasses, colId,
                        borderColor, borderStyle, borderWidth, borderRadius,
                    } = attributes;

                    const blockClasses = [
                        'advgb-column',
                        'column',
                        columnClasses,
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <div className={ blockClasses }
                             id={ colId }
                             style={ {
                                 width: width ? width + '%' : undefined,
                                 flex: width ? 'none' : undefined,
                             } }
                        >
                            <div className="advgb-column-inner"
                                 style={ { borderStyle, borderColor, borderWidth, borderRadius, } }
                            >
                                <InnerBlocks.Content />
                            </div>
                        </div>
                    );
                },
            }
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );