(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings } = wpBlockEditor;
    const { Dashicon, Tooltip, PanelBody, RangeControl, SelectControl } = wpComponents;

    class AdvTabsBlock extends Component {
        constructor() {
            super( ...arguments );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-tabs'];

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
            setTimeout(() => this.initTabs(), 100);
            if (!this.props.attributes.blockID) {
                this.props.setAttributes( { blockID: this.props.clientId } );
            }
        }

        componentDidUpdate( prevProps ) {
            const { tabItems: prevItems } = prevProps.attributes;
            const { tabItems } = this.props.attributes;

            if (prevItems !== tabItems) {
                this.initTabs( true );
            }

            if (tabItems.length === 0) {
                this.props.setAttributes( {
                    tabItems: [
                        {
                            header: 'Tab 1',
                            body: 'At least one tab must remaining, to remove block use "Remove Block" button from right menu.',
                        },
                    ],
                } );
            }
        }

        initTabs( refresh = false ) {
            if (typeof jQuery !== "undefined") {
                if (!refresh) {
                    jQuery(`#block-${this.props.clientId} .advgb-tabs-block`).tabs();
                } else {
                    jQuery(`#block-${this.props.clientId} .advgb-tabs-block`).tabs('refresh');
                }

                jQuery(`#block-${this.props.clientId} .advgb-tabs-block a`).on( 'keydown', function ( e ) {
                    e.stopPropagation();
                } )
            }
        }

        updateTabs( value, index ) {
            const { attributes, setAttributes } = this.props;
            const { tabItems } = attributes;

            let newItems = tabItems.map( ( item, thisIndex ) => {
                if ( index === thisIndex ) {
                    item = { ...item, ...value };
                }

                return item;
            } );

            setAttributes( { tabItems: newItems } );
        }

        render() {
            const { attributes, setAttributes, clientId } = this.props;
            const {
                tabItems,
                headerBgColor,
                headerTextColor,
                bodyBgColor,
                bodyTextColor,
                borderStyle,
                borderWidth,
                borderColor,
                borderRadius,
                blockID,
                activeTabBgColor,
                activeTabTextColor,
            } = attributes;

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelColorSettings
                            title={ __( 'Tab Colors', 'advanced-gutenberg' ) }
                            initialOpen={ false }
                            colorSettings={ [
                                {
                                    label: __( 'Background Color', 'advanced-gutenberg' ),
                                    value: headerBgColor,
                                    onChange: ( value ) => setAttributes( { headerBgColor: value === undefined ? '#000' : value } ),
                                },
                                {
                                    label: __( 'Text Color', 'advanced-gutenberg' ),
                                    value: headerTextColor,
                                    onChange: ( value ) => setAttributes( { headerTextColor: value === undefined ? '#fff' : value } ),
                                },
                                {
                                    label: __( 'Active Tab Background Color', 'advanced-gutenberg' ),
                                    value: activeTabBgColor,
                                    onChange: ( value ) => setAttributes( { activeTabBgColor: value } ),
                                },
                                {
                                    label: __( 'Active Tab Text Color', 'advanced-gutenberg' ),
                                    value: activeTabTextColor,
                                    onChange: ( value ) => setAttributes( { activeTabTextColor: value } ),
                                },
                            ] }
                        />
                        <PanelColorSettings
                            title={ __( 'Body Colors', 'advanced-gutenberg' ) }
                            initialOpen={ false }
                            colorSettings={ [
                                {
                                    label: __( 'Background Color', 'advanced-gutenberg' ),
                                    value: bodyBgColor,
                                    onChange: ( value ) => setAttributes( { bodyBgColor: value } ),
                                },
                                {
                                    label: __( 'Text Color', 'advanced-gutenberg' ),
                                    value: bodyTextColor,
                                    onChange: ( value ) => setAttributes( { bodyTextColor: value } ),
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
                                onChange={ ( value ) => setAttributes( { borderStyle: value } ) }
                            />
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
                                value={ borderWidth }
                                min={ 1 }
                                max={ 10 }
                                onChange={ ( value ) => setAttributes( { borderWidth: value } ) }
                            />
                            <RangeControl
                                label={ __( 'Border radius', 'advanced-gutenberg' ) }
                                value={ borderRadius }
                                min={ 0 }
                                max={ 100 }
                                onChange={ ( value ) => setAttributes( { borderRadius: value } ) }
                            />
                        </PanelBody>
                    </InspectorControls>
                    <div className="advgb-tabs-block" style={ { border: 'none' } }>
                        <ul className="advgb-tabs-panel">
                            {tabItems.map( ( item, index ) => (
                                <li key={ index }
                                    className="advgb-tab"
                                    style={ {
                                        backgroundColor: headerBgColor,
                                        borderStyle: borderStyle,
                                        borderWidth: borderWidth + 'px',
                                        borderColor: borderColor,
                                        borderRadius: borderRadius + 'px',
                                        margin: `-${borderWidth}px 0 -${borderWidth}px -${borderWidth}px`,
                                    } }
                                >
                                    <a href={`#advgb-tab-${blockID}-${index}`}
                                       style={ { color: headerTextColor } }
                                    >
                                        <RichText
                                            tagName="p"
                                            value={ item.header }
                                            onChange={ ( value ) => this.updateTabs( { header: value || '' }, index ) }
                                            unstableOnSplit={ () => null }
                                            placeholder={ __( 'Title…', 'advanced-gutenberg' ) }
                                        />
                                    </a>
                                    <Tooltip text={ __( 'Remove tab', 'advanced-gutenberg' ) }>
                                        <span className="advgb-tab-remove"
                                              onClick={ () => setAttributes( {
                                                  tabItems: tabItems.filter( (vl, idx) => idx !== index )
                                              } ) }
                                        >
                                            <Dashicon icon="no"/>
                                        </span>
                                    </Tooltip>
                                </li>
                            ) ) }
                            <li className="advgb-tab advgb-add-tab ui-state-default"
                                style={ {
                                    borderRadius: borderRadius + 'px',
                                    borderWidth: borderWidth + 'px',
                                    margin: `-${borderWidth}px 0 -${borderWidth}px -${borderWidth}px`,
                                } }
                            >
                                <Tooltip text={ __( 'Add tab', 'advanced-gutenberg' ) }>
                                    <span onClick={ () => setAttributes( {
                                        tabItems: [
                                            ...tabItems,
                                            { header: __( 'New Tab', 'advanced-gutenberg' ), body: __( 'Enter your content.', 'advanced-gutenberg' ) }
                                        ]
                                    } ) }>
                                        <Dashicon icon="plus-alt"/>
                                    </span>
                                </Tooltip>
                            </li>
                        </ul>
                        {tabItems.map( ( item, index ) => (
                            <div key={ index }
                                 id={`advgb-tab-${blockID}-${index}`}
                                 className="advgb-tab-body"
                                 style={ {
                                     backgroundColor: bodyBgColor,
                                     color: bodyTextColor,
                                     borderStyle: borderStyle,
                                     borderWidth: borderWidth + 'px',
                                     borderColor: borderColor,
                                     borderRadius: borderRadius + 'px',
                                 } }
                            >
                                <RichText
                                    tagName="p"
                                    value={ item.body }
                                    onChange={ ( value ) => this.updateTabs( { body: value }, index ) }
                                    placeholder={ __( 'Enter text…', 'advanced-gutenberg' ) }
                                />
                            </div>
                        ) ) }
                    </div>
                    {!!blockID &&
                        <style>
                            {activeTabBgColor && `#block-${clientId} li.advgb-tab.ui-tabs-active {
                                background-color: ${activeTabBgColor} !important;
                            }`}
                            {activeTabTextColor && `#block-${clientId} li.advgb-tab.ui-tabs-active a {
                                color: ${activeTabTextColor} !important;
                            }`}
                        </style>
                    }
                </Fragment>
            )
        }
    }

    const tabsBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
            <path fill="none" d="M0,0h24v24H0V0z"/>
            <path fill="none" d="M0,0h24v24H0V0z"/>
            <path d="M21,3H3C1.9,3,1,3.9,1,5v14c0,1.1,0.9,2,2,2h18c1.1,0,2-0.9,2-2V5C23,3.9,22.1,3,21,3z M21,19H3V5h10v4h8V19z"/>
        </svg>
    );

    const tabBlockAttrs = {
        tabItems: {
            type: "array",
            default: [
                {
                    header: __( 'Tab 1', 'advanced-gutenberg' ),
                    body: __( 'Filler text (also placeholder text or dummy text) is text that shares some characteristics of a real written text, but is random or otherwise generated.', 'advanced-gutenberg' )
                },
                {
                    header: __( 'Tab 2', 'advanced-gutenberg' ),
                    body: __( 'Filler text (also placeholder text or dummy text) is text that shares some characteristics of a real written text, but is random or otherwise generated.', 'advanced-gutenberg' )
                },
                {
                    header: __( 'Tab 3', 'advanced-gutenberg' ),
                    body: __( 'Filler text (also placeholder text or dummy text) is text that shares some characteristics of a real written text, but is random or otherwise generated.', 'advanced-gutenberg' )
                },
            ]
        },
        headerBgColor: {
            type: 'string',
            default: '#000',
        },
        headerTextColor: {
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
        blockID: {
            type: 'string',
        },
        activeTabBgColor: {
            type: 'string',
        },
        activeTabTextColor: {
            type: 'string',
        },
        changed: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType( 'advgb/tabs', {
        title: __( 'Tabs', 'advanced-gutenberg' ),
        description: __( 'Create your own tabs never easy like this.', 'advanced-gutenberg' ),
        icon: {
            src: tabsBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: "advgb-category",
        keywords: [ __( 'tabs', 'advanced-gutenberg' ), __( 'cards', 'advanced-gutenberg' ) ],
        attributes: tabBlockAttrs,
        supports: {
            inserter: false,
        },
        edit: AdvTabsBlock,
        save: function ( { attributes } ) {
            const {
                tabItems,
                headerBgColor,
                headerTextColor,
                bodyBgColor,
                bodyTextColor,
                borderStyle,
                borderWidth,
                borderColor,
                borderRadius,
                blockID,
                activeTabBgColor,
                activeTabTextColor,
            } = attributes;

            return (
                <div id={`advgb-tabs-${blockID}`} className="advgb-tabs-block" style={ { border: 'none' } }>
                    <ul className="advgb-tabs-panel">
                        {tabItems.map( ( item, index ) => (
                            <li key={ index } className="advgb-tab"
                                style={ {
                                    backgroundColor: headerBgColor,
                                    borderStyle: borderStyle,
                                    borderWidth: borderWidth + 'px',
                                    borderColor: borderColor,
                                    borderRadius: borderRadius + 'px',
                                    margin: `-${borderWidth}px 0 -${borderWidth}px -${borderWidth}px`,
                                } }
                            >
                                <a href={`#advgb-tab-${blockID}-${index}`}
                                   style={ { color: headerTextColor } }
                                >
                                    <RichText.Content tagName="span" value={ item.header }/>
                                </a>
                            </li>
                        ) ) }
                    </ul>
                    {tabItems.map( ( item, index ) => (
                        <div key={ index }
                             id={`advgb-tab-${blockID}-${index}`}
                             className="advgb-tab-body"
                             style={ {
                                 backgroundColor: bodyBgColor,
                                 color: bodyTextColor,
                                 borderStyle: borderStyle,
                                 borderWidth: borderWidth + 'px',
                                 borderColor: borderColor,
                                 borderRadius: borderRadius + 'px',
                             } }
                        >
                            <RichText.Content tagName="p" value={ item.body }/>
                        </div>
                    ) ) }
                    {!!blockID &&
                        <style>
                            {activeTabBgColor && `#advgb-tabs-${blockID} li.advgb-tab.ui-tabs-active {
                                background-color: ${activeTabBgColor} !important;
                            }
                            `}
                            {activeTabTextColor && `#advgb-tabs-${blockID} li.advgb-tab.ui-tabs-active a {
                                color: ${activeTabTextColor} !important;
                            }`}
                        </style>
                    }
                </div>
            );
        },
        transforms: {
            to: [
                {
                    type: 'block',
                    blocks: [ 'advgb/adv-tabs' ],
                    transform: ( attributes ) => {
                        const innerTabs = [];
                        const tabHeaders = [];

                        attributes.tabItems.map(item => {
                            const tabContent = createBlock(
                                'core/paragraph',
                                {content: item.body}
                            );

                            const tab = createBlock(
                                'advgb/tab',
                                { tabActive: 0 },
                                [ tabContent ]
                            );

                            tabHeaders.push(item.header);
                            innerTabs.push(tab);
                        });

                        attributes.tabItems =  undefined;
                        return createBlock(
                            'advgb/adv-tabs',
                            { ...attributes,
                                tabHeaders: tabHeaders,
                                pid: `advgb-tabs-${attributes.blockID}`,
                                changed: false,
                                isTransform: true,
                            },
                            innerTabs,
                        )
                    }
                }
            ]
        },
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );