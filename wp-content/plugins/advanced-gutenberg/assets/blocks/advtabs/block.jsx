(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings, InnerBlocks } = wpBlockEditor;
    const { Dashicon, Tooltip, PanelBody, RangeControl, SelectControl, Button } = wpComponents;
    const { dispatch, select } = wp.data;

    const svgPath = (
        <Fragment>
            <path fill="currentColor" d="M491.2,474.55a1.93,1.93,0,0,1,0-2.84Z"/>
            <path fill="none" stroke="currentColor" strokeMiterlimit="10" strokeWidth="7.43"
                  d="M248.71,475.78H34.46c-10.59,0-11.37-.8-11.37-11.22q0-206.92,0-413.84c0-4.89-.2-9.78-.08-14.66.21-8.43,3.91-12,12.18-12,41.14,0,82.29.08,123.44-.12,4.38,0,6.31,1.52,8,5.29,14.32,31.05,28.91,62,43.22,93,1.7,3.69,3.65,5,7.74,5q122.26-.18,244.52-.09c12.73,0,12.73,0,12.73,12.59V463.22c0,12.14-.4,12.56-12.39,12.56Z"/>
            <path fill="currentColor"
                  d="M257,24.15c23,0,46,.11,69-.09,4.67,0,6.33,1.24,6.29,6.12q-.33,37.57,0,75.17c0,4.52-1.39,6-5.93,6q-49.17-.24-98.34,0c-4.29,0-6.49-1.5-8.24-5.32C208,80.21,196,54.53,184.06,28.81c-2.08-4.48-2-4.64,3-4.65Q222.05,24.13,257,24.15Z"/>
            <path fill="currentColor"
                  d="M411.32,111.22c-18.92,0-37.85-.12-56.77.08-4.59,0-6.39-1.09-6.35-6q.33-37.6,0-75.21c0-4.53,1.41-6,5.95-6,36.43.15,72.85.07,109.27.1,6.91,0,9.69,2.29,11.09,9.08a27.87,27.87,0,0,1,.53,5.62q0,33.12,0,66.23c0,6.05-.13,6.13-6.05,6.13Z"/>
        </Fragment>
    );

    const tabHorizontalIcon = (
        <svg color="#5954d6" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 500 500" style={{backgroundColor: "#fff"}}>
            {svgPath}
        </svg>
    );

    const tabVerticalIcon = (
        <svg color="#5954d6" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 500 500" style={{backgroundColor: "#fff"}} transform="rotate(-90) scale(-1, 1)">
            {svgPath}
        </svg>
    );

    const TABS_STYLES = [
        {name: 'horz', label: __('Horizontal', 'advanced-gutenberg'), icon: tabHorizontalIcon},
        {name: 'vert', label: __('Vertical', 'advanced-gutenberg'), icon: tabVerticalIcon},
    ];

    class AdvTabsWrapper extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                viewport: 'desktop',
            }
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-adv-tabs'];

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
            if (!this.props.attributes.pid) {
                this.props.setAttributes( { pid: `advgb-tabs-${this.props.clientId}` } );
            }
        }

        updateTabsAttr( attrs ) {
            const { setAttributes, clientId } = this.props;
            const { updateBlockAttributes } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);

            setAttributes( attrs );
            childBlocks.forEach( childBlockId => updateBlockAttributes( childBlockId, attrs ) );
        }

        updateTabsHeader(header, index) {
            const { attributes, setAttributes, clientId } = this.props;
            const { tabHeaders } = attributes;
            const { updateBlockAttributes } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);

            let newHeaders = tabHeaders.map( ( item, idx ) => {
                if ( index === idx ) {
                    item = header;
                }
                return item;
            } );

            setAttributes( { tabHeaders: newHeaders} );
            updateBlockAttributes(childBlocks[index], {header: header});
        }

        addTab() {
            const { attributes, setAttributes, clientId } = this.props;
            const { insertBlock } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const tabItemBlock = createBlock('advgb/tab');

            insertBlock(tabItemBlock, attributes.tabHeaders.length, clientId);
            setAttributes( {
                tabHeaders: [
                    ...attributes.tabHeaders,
                    __('Tab header', 'advanced-gutenberg')
                ]
            } )
        }

        removeTab(index) {
            const { attributes, setAttributes, clientId } = this.props;
            const { removeBlock } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);

            removeBlock(childBlocks[index], false);
            setAttributes( {
                tabHeaders: attributes.tabHeaders.filter( (vl, idx) => idx !== index )
            } );
            this.updateTabsAttr({tabActive: 0})
        }

        render() {
            const { attributes, setAttributes, clientId } = this.props;
            const { viewport } = this.state;
            const {
                tabHeaders,
                tabActive,
                tabActiveFrontend,
                tabsStyleD,
                tabsStyleT,
                tabsStyleM,
                headerBgColor,
                headerTextColor,
                bodyBgColor,
                bodyTextColor,
                borderStyle,
                borderWidth,
                borderColor,
                borderRadius,
                pid,
                activeTabBgColor,
                activeTabTextColor,
            } = attributes;
            const blockClass = [
                `advgb-tabs-wrapper`,
                `advgb-tab-${tabsStyleD}-desktop`,
                `advgb-tab-${tabsStyleT}-tablet`,
                `advgb-tab-${tabsStyleM}-mobile`,
            ].filter( Boolean ).join( ' ' );

            let deviceLetter = 'D';
            if (viewport === 'tablet') deviceLetter = 'T';
            if (viewport === 'mobile') deviceLetter = 'M';

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Tabs Style', 'advanced-gutenberg' ) }>
                            <div className="advgb-columns-responsive-items">
                                {['desktop', 'tablet', 'mobile'].map( (device, index) => {
                                    const itemClasses = [
                                        "advgb-columns-responsive-item",
                                        viewport === device && 'is-selected',
                                    ].filter( Boolean ).join( ' ' );

                                    return (
                                        <div className={ itemClasses }
                                             key={ index }
                                             onClick={ () => this.setState( { viewport: device } ) }
                                        >
                                            {device}
                                        </div>
                                    )
                                } ) }
                            </div>
                            <div className="advgb-tabs-styles">
                                {TABS_STYLES.map((style, index) => (
                                    <Tooltip key={index} text={style.label}>
                                        <Button className="advgb-tabs-style"
                                                isToggled={ style.name === attributes[`tabsStyle${deviceLetter}`] }
                                                onClick={ () => setAttributes( { [`tabsStyle${deviceLetter}`]: style.name } ) }
                                        >
                                            {style.icon}
                                        </Button>
                                    </Tooltip>
                                ))}
                                {viewport === 'mobile' && (
                                    <Tooltip text={ __( 'Stacked', 'advanced-gutenberg' ) }>
                                        <Button className="advgb-tabs-style"
                                                isToggled={ tabsStyleM === 'stack' }
                                                onClick={ () => setAttributes( { tabsStyleM: 'stack' } ) }
                                        >
                                            <svg color="#5954d6" width="50px" height="50px" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg" style={{backgroundColor: "#fff"}}>
                                                <path fill="currentColor" d="M24.2480469,18.5H1.75C1.3359375,18.5,1,18.8359375,1,19.25v5C1,24.6640625,1.3359375,25,1.75,25   h22.4980469c0.4140625,0,0.75-0.3359375,0.75-0.75v-5C24.9980469,18.8359375,24.6621094,18.5,24.2480469,18.5z M23.4980469,23.5   H2.5V20h20.9980469V23.5z"/>
                                                <path fill="currentColor" d="M24.25,9.75H1.75C1.3359375,9.75,1,10.0859375,1,10.5v5c0,0.4140625,0.3359375,0.75,0.75,0.75h22.5   c0.4140625,0,0.75-0.3359375,0.75-0.75v-5C25,10.0859375,24.6640625,9.75,24.25,9.75z M23.5,14.75h-21v-3.5h21V14.75z"/>
                                                <path fill="currentColor" d="M1.75,7.5h22.4980469c0.4140625,0,0.75-0.3359375,0.75-0.75v-5c0-0.4140625-0.3359375-0.75-0.75-0.75H1.75   C1.3359375,1,1,1.3359375,1,1.75v5C1,7.1640625,1.3359375,7.5,1.75,7.5z M2.5,2.5h20.9980469V6H2.5V2.5z"/>
                                            </svg>
                                        </Button>
                                    </Tooltip>
                                )}
                            </div>
                        </PanelBody>
                        <PanelBody title={ __( 'Tabs Settings', 'advanced-gutenberg' ) }>
                            <SelectControl
                                label={ __( 'Initial Open Tab', 'advanced-gutenberg' ) }
                                value={ tabActiveFrontend }
                                options={ tabHeaders.map((tab, index) => {
                                    return {value: index, label: tab};
                                } ) }
                                onChange={ (value) => setAttributes( { tabActiveFrontend: parseInt(value) } ) }
                            />
                        </PanelBody>
                        <PanelColorSettings
                            title={ __( 'Tab Colors', 'advanced-gutenberg' ) }
                            initialOpen={ false }
                            colorSettings={ [
                                {
                                    label: __( 'Background Color', 'advanced-gutenberg' ),
                                    value: headerBgColor,
                                    onChange: ( value ) => setAttributes( { headerBgColor: value === undefined ? '#e0e0e0' : value } ),
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
                    <div className={blockClass} style={ { border: 'none' } }>
                        <ul className="advgb-tabs-panel">
                            {tabHeaders.map( ( item, index ) => (
                                <li key={ index }
                                    className={`advgb-tab ${tabActive === index && 'ui-tabs-active'}`}
                                    style={ {
                                        backgroundColor: headerBgColor,
                                        borderStyle: borderStyle,
                                        borderWidth: borderWidth + 'px',
                                        borderColor: borderColor,
                                        borderRadius: borderRadius + 'px',
                                    } }
                                >
                                    <a style={ { color: headerTextColor } }
                                       onClick={ () => this.updateTabsAttr( {tabActive: index} ) }
                                    >
                                        <RichText
                                            tagName="p"
                                            value={ item }
                                            onChange={ ( value ) => this.updateTabsHeader(value, index) }
                                            unstableOnSplit={ () => null }
                                            placeholder={ __( 'Title…', 'advanced-gutenberg' ) }
                                        />
                                    </a>
                                    {tabHeaders.length > 1 && (
                                        <Tooltip text={ __( 'Remove tab', 'advanced-gutenberg' ) }>
                                            <span className="advgb-tab-remove"
                                                  onClick={ () => this.removeTab(index) }
                                            >
                                                <Dashicon icon="no"/>
                                            </span>
                                        </Tooltip>
                                    )}
                                </li>
                            ) ) }
                            <li className="advgb-tab advgb-add-tab"
                                style={ {
                                    borderRadius: borderRadius + 'px',
                                    borderWidth: borderWidth + 'px',
                                } }
                            >
                                <Tooltip text={ __( 'Add tab', 'advanced-gutenberg' ) }>
                                    <span onClick={ () => this.addTab() }>
                                        <Dashicon icon="plus"/>
                                    </span>
                                </Tooltip>
                            </li>
                        </ul>
                        <div className="advgb-tab-body-wrapper"
                             style={ {
                                 backgroundColor: bodyBgColor,
                                 color: bodyTextColor,
                                 borderStyle: borderStyle,
                                 borderWidth: borderWidth + 'px',
                                 borderColor: borderColor,
                                 borderRadius: borderRadius + 'px',
                             } }
                        >
                            <InnerBlocks
                                template={ [ ['advgb/tab'], ['advgb/tab'], ['advgb/tab']] }
                                templateLock={false}
                                allowedBlocks={ [ 'advgb/tab' ] }
                            />
                        </div>
                    </div>
                    {!!pid &&
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
        tabHeaders: {
            type: 'array',
            default: [
                __( 'Tab 1', 'advanced-gutenberg' ),
                __( 'Tab 2', 'advanced-gutenberg' ),
                __( 'Tab 3', 'advanced-gutenberg' ),
            ]
        },
        tabActive: {
            type: 'number',
            default: 0,
        },
        tabActiveFrontend: {
            type: 'number',
            default: 0,
        },
        tabsStyleD: {
            type: 'string',
            default: 'horz'
        },
        tabsStyleT: {
            type: 'string',
            default: 'vert'
        },
        tabsStyleM: {
            type: 'string',
            default: 'stack'
        },
        headerBgColor: {
            type: 'string',
            default: '#e0e0e0',
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
            default: 10,
        },
        pid: {
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

    registerBlockType( 'advgb/adv-tabs', {
        title: __( 'Advanced Tabs', 'advanced-gutenberg' ),
        description: __( 'Create your own tabs never easy like this.', 'advanced-gutenberg' ),
        icon: {
            src: tabsBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: "advgb-category",
        keywords: [ __( 'tabs', 'advanced-gutenberg' ), __( 'cards', 'advanced-gutenberg' ) ],
        attributes: tabBlockAttrs,
        edit: AdvTabsWrapper,
        save: function ( { attributes } ) {
            const {
                tabHeaders,
                tabActiveFrontend,
                tabsStyleD,
                tabsStyleT,
                tabsStyleM,
                headerBgColor,
                headerTextColor,
                bodyBgColor,
                bodyTextColor,
                borderStyle,
                borderWidth,
                borderColor,
                borderRadius,
                pid,
            } = attributes;
            const blockClass = [
                `advgb-tabs-wrapper`,
                `advgb-tab-${tabsStyleD}-desktop`,
                `advgb-tab-${tabsStyleT}-tablet`,
                `advgb-tab-${tabsStyleM}-mobile`,
            ].filter( Boolean ).join( ' ' );

            return (
                <div id={pid} className={blockClass} data-tab-active={tabActiveFrontend}>
                    <ul className="advgb-tabs-panel">
                        {tabHeaders.map( ( header, index ) => (
                            <li key={ index } className="advgb-tab"
                                style={ {
                                    backgroundColor: headerBgColor,
                                    borderStyle: borderStyle,
                                    borderWidth: borderWidth + 'px',
                                    borderColor: borderColor,
                                    borderRadius: borderRadius + 'px',
                                } }
                            >
                                <a href={`#${pid}-${index}`}
                                   style={ { color: headerTextColor } }
                                >
                                    <span>{header}</span>
                                </a>
                            </li>
                        ) ) }
                    </ul>
                    <div className="advgb-tab-body-wrapper"
                         style={ {
                             backgroundColor: bodyBgColor,
                             color: bodyTextColor,
                             borderStyle: borderStyle,
                             borderWidth: borderWidth + 'px',
                             borderColor: borderColor,
                             borderRadius: borderRadius + 'px',
                         } }
                    >
                        <InnerBlocks.Content />
                    </div>
                </div>
            );
        },
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );