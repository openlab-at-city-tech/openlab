(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, PanelColorSettings, InnerBlocks } = wpBlockEditor;
    const { PanelBody, RangeControl, SelectControl, ToggleControl, Tooltip, Toolbar } = wpComponents;
    const { times } = lodash;
    const { dispatch, select } = wp.data;

    const COLUMNS_LAYOUTS = [
        { columns: 1, layout: '100', icon: '100', title: __( 'One', 'advanced-gutenberg' ) },
        { columns: 2, layout: '12-12', icon: '12-12', title: __( 'Two: 1/2 - 1/2', 'advanced-gutenberg' ) },
        { columns: 2, layout: '23-13', icon: '23-13', title: __( 'Two: 2/3 - 1/3', 'advanced-gutenberg' ) },
        { columns: 2, layout: '13-23', icon: '13-23', title: __( 'Two: 1/3 - 2/3', 'advanced-gutenberg' ) },
        { columns: 2, layout: '14-34', icon: '14-34', title: __( 'Two: 1/4 - 3/4', 'advanced-gutenberg' ) },
        { columns: 2, layout: '34-14', icon: '34-14', title: __( 'Two: 3/4 - 1/4', 'advanced-gutenberg' ) },
        { columns: 2, layout: '15-45', icon: '15-45', title: __( 'Two: 1/5 - 4/5', 'advanced-gutenberg' ) },
        { columns: 2, layout: '45-15', icon: '45-15', title: __( 'Two: 4/5 - 1/5', 'advanced-gutenberg' ) },
        { columns: 3, layout: '13-13-13', icon: '13-13-13', title: __( 'Three: 1/3 - 1/3 - 1/3', 'advanced-gutenberg' ) },
        { columns: 3, layout: '12-14-14', icon: '12-14-14', title: __( 'Three: 1/2 - 1/4 - 1/4', 'advanced-gutenberg' ) },
        { columns: 3, layout: '14-14-12', icon: '14-14-12', title: __( 'Three: 1/4 - 1/4 - 1/2', 'advanced-gutenberg' ) },
        { columns: 3, layout: '14-12-14', icon: '14-12-14', title: __( 'Three: 1/4 - 1/2 - 1/4', 'advanced-gutenberg' ) },
        { columns: 3, layout: '15-35-15', icon: '15-35-15', title: __( 'Three: 1/5 - 3/5 - 1/5', 'advanced-gutenberg' ) },
        { columns: 3, layout: '35-15-15', icon: '35-15-15', title: __( 'Three: 3/5 - 1/5 - 1/5', 'advanced-gutenberg' ) },
        { columns: 3, layout: '15-15-35', icon: '15-15-35', title: __( 'Three: 1/5 - 1/5 - 3/5', 'advanced-gutenberg' ) },
        { columns: 3, layout: '16-46-16', icon: '16-46-16', title: __( 'Three: 1/6 - 4/6 - 1/6', 'advanced-gutenberg' ) },
        { columns: 4, layout: '14-14-14-14', icon: '14-14-14-14', title: __( 'Four: 1/4 - 1/4 - 1/4 - 1/4', 'advanced-gutenberg' ) },
        { columns: 4, layout: '36-16-16-16', icon: '36-16-16-16', title: __( 'Four: 3/6 - 1/6 - 1/6 - 1/6', 'advanced-gutenberg' ) },
        { columns: 4, layout: '16-16-16-36', icon: '16-16-16-36', title: __( 'Four: 1/6 - 1/6 - 1/6 - 3/6', 'advanced-gutenberg' ) },
        { columns: 4, layout: '15-15-15-25', icon: '15-15-15-25', title: __( 'Four: 1/5 - 1/5 - 1/5 - 2/5', 'advanced-gutenberg' ) },
        { columns: 4, layout: '25-15-15-15', icon: '25-15-15-15', title: __( 'Four: 2/5 - 1/5 - 1/5 - 1/5', 'advanced-gutenberg' ) },
        { columns: 5, layout: 'five', icon: '15-15-15-15-15', title: __( 'Five', 'advanced-gutenberg' ) },
        { columns: 6, layout: 'six', icon: '16-16-16-16-16-16', title: __( 'Six', 'advanced-gutenberg' ) },
    ];
    const COLUMNS_LAYOUTS_RESPONSIVE = [
        { columns: 3, layout: '1-12-12', icon: '100-12-12', title: __( 'Three: 100 - 1/2 - 1/2', 'advanced-gutenberg' ) },
        { columns: 3, layout: '12-12-1', icon: '12-12-100', title: __( 'Three: 1/2 - 1/2 - 100', 'advanced-gutenberg' ) },
        { columns: 4, layout: '12x4', icon: '12-12-12-12', title: __( 'Four: Two Columns', 'advanced-gutenberg' ) },
        { columns: 6, layout: '12x6', icon: '12-12-12-12', title: __( 'Six: Two Columns', 'advanced-gutenberg' ) },
        { columns: 6, layout: '13x6', icon: '13-13-13-13-13-13', title: __( 'Six: Three Columns', 'advanced-gutenberg' ) },
    ];
    const COLUMNS_LAYOUTS_STACKED = {
        columns: 1, layout: 'stacked', icon: 'stacked', title: __( 'Stacked', 'advanced-gutenberg' )
    };
    const GUTTER_OPTIONS = [
        {label: __( 'None', 'advanced-gutenberg' ), value: 0},
        {label: '10px', value: 10},
        {label: '20px', value: 20},
        {label: '30px', value: 30},
        {label: '40px', value: 40},
        {label: '50px', value: 50},
        {label: '70px', value: 70},
        {label: '90px', value: 90},
    ];

    const previewImageData = 'data:image/png;base64,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';

    class AdvColumnsEdit extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                tabSelected: 'desktop',
            }
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-columns'];

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
                setAttributes( { colId: 'advgb-cols-' + clientId, } )
            }
        }

        componentDidUpdate( prevProps ) {
            const {
                columnsLayout: prevLayout,
                columnsLayoutT: prevLayoutT,
                columnsLayoutM: prevLayoutM,
            } = prevProps.attributes;
            const { attributes, clientId } = this.props;
            const { columns, columnsLayout, columnsLayoutT, columnsLayoutM } = attributes;
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const { updateBlockAttributes } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);
            let shouldUpdate = false;
            let classes = times( 6, () => [] );

            const extraClassD = !!columnsLayoutT ? '-desktop' : '-tablet';
            const extraClassT = '-tablet';
            const extraClassM = '-mobile';

            if (prevLayout !== columnsLayout
                || prevLayoutT !== columnsLayoutT
                || prevLayoutM !== columnsLayoutM
            ) {
                shouldUpdate = true;
                classes = AdvColumnsEdit.prepareColumnClass(columnsLayout, extraClassD, classes);
                classes = AdvColumnsEdit.prepareColumnClass(columnsLayoutT, extraClassT, classes);
                classes = AdvColumnsEdit.prepareColumnClass(columnsLayoutM, extraClassM, classes);
            }

            if (shouldUpdate) {
                classes = classes.map((cls) => cls.filter( Boolean ).join( ' ' ));
                classes.map(
                    ( cls, idx ) =>
                        (!!childBlocks[idx]) && updateBlockAttributes( childBlocks[idx], { columnClasses: cls, width: 0 } )
                );
            }
        }

        static prepareColumnClass(layout, extraClass, classObj) {
            switch (layout) {
                case '12-12':
                    for ( let i = 0; i < 2; i++) {
                        classObj[i].push('advgb-is-half' + extraClass);
                    }
                    break;
                case '13-13-13':
                    for ( let i = 0; i < 3; i++) {
                        classObj[i].push('advgb-is-one-third' + extraClass);
                    }
                    break;
                case '14-14-14-14':
                    for ( let i = 0; i < 4; i++) {
                        classObj[i].push('advgb-is-one-quarter' + extraClass);
                    }
                    break;
                case 'five':
                    for ( let i = 0; i < 5; i++) {
                        classObj[i].push('advgb-is-one-fifth' + extraClass);
                    }
                    break;
                case 'six':
                    for ( let i = 0; i < 6; i++) {
                        classObj[i].push('advgb-is-2' + extraClass);
                    }
                    break;
                case '23-13':
                    classObj[0].push('advgb-is-two-thirds' + extraClass);
                    classObj[1].push('advgb-is-one-third' + extraClass);
                    break;
                case '13-23':
                    classObj[0].push('advgb-is-one-third' + extraClass);
                    classObj[1].push('advgb-is-two-thirds' + extraClass);
                    break;
                case '34-14':
                    classObj[0].push('advgb-is-three-quarters' + extraClass);
                    classObj[1].push('advgb-is-one-quarter' + extraClass);
                    break;
                case '14-34':
                    classObj[0].push('advgb-is-one-quarter' + extraClass);
                    classObj[1].push('advgb-is-three-quarters' + extraClass);
                    break;
                case '45-15':
                    classObj[0].push('advgb-is-four-fifths' + extraClass);
                    classObj[1].push('advgb-is-one-fifth' + extraClass);
                    break;
                case '15-45':
                    classObj[0].push('advgb-is-one-fifth' + extraClass);
                    classObj[1].push('advgb-is-four-fifths' + extraClass);
                    break;
                case '12-14-14':
                    classObj[0].push('advgb-is-half' + extraClass);
                    classObj[1].push('advgb-is-one-quarter' + extraClass);
                    classObj[2].push('advgb-is-one-quarter' + extraClass);
                    break;
                case '14-14-12':
                    classObj[0].push('advgb-is-one-quarter' + extraClass);
                    classObj[1].push('advgb-is-one-quarter' + extraClass);
                    classObj[2].push('advgb-is-half' + extraClass);
                    break;
                case '14-12-14':
                    classObj[0].push('advgb-is-one-quarter' + extraClass);
                    classObj[1].push('advgb-is-half' + extraClass);
                    classObj[2].push('advgb-is-one-quarter' + extraClass);
                    break;
                case '15-35-15':
                    classObj[0].push('advgb-is-one-fifth' + extraClass);
                    classObj[1].push('advgb-is-three-fifths' + extraClass);
                    classObj[2].push('advgb-is-one-fifth' + extraClass);
                    break;
                case '35-15-15':
                    classObj[0].push('advgb-is-three-fifths' + extraClass);
                    classObj[1].push('advgb-is-one-fifth' + extraClass);
                    classObj[2].push('advgb-is-one-fifth' + extraClass);
                    break;
                case '15-15-35':
                    classObj[0].push('advgb-is-one-fifth' + extraClass);
                    classObj[1].push('advgb-is-one-fifth' + extraClass);
                    classObj[2].push('advgb-is-three-fifths' + extraClass);
                    break;
                case '16-46-16':
                    classObj[0].push('advgb-is-2' + extraClass);
                    classObj[1].push('advgb-is-8' + extraClass);
                    classObj[2].push('advgb-is-2' + extraClass);
                    break;
                case '1-12-12':
                    classObj[0].push('advgb-is-full' + extraClass);
                    classObj[1].push('advgb-is-half' + extraClass);
                    classObj[2].push('advgb-is-half' + extraClass);
                    break;
                case '12-12-1':
                    classObj[0].push('advgb-is-half' + extraClass);
                    classObj[1].push('advgb-is-half' + extraClass);
                    classObj[2].push('advgb-is-full' + extraClass);
                    break;
                case '36-16-16-16':
                    classObj[0].push('advgb-is-half' + extraClass);
                    classObj[1].push('advgb-is-2' + extraClass);
                    classObj[2].push('advgb-is-2' + extraClass);
                    classObj[3].push('advgb-is-2' + extraClass);
                    break;
                case '16-16-16-36':
                    classObj[0].push('advgb-is-2' + extraClass);
                    classObj[1].push('advgb-is-2' + extraClass);
                    classObj[2].push('advgb-is-2' + extraClass);
                    classObj[3].push('advgb-is-half' + extraClass);
                    break;
                case '25-15-15-15':
                    classObj[0].push('advgb-is-two-fifths' + extraClass);
                    classObj[1].push('advgb-is-one-fifth' + extraClass);
                    classObj[2].push('advgb-is-one-fifth' + extraClass);
                    classObj[3].push('advgb-is-one-fifth' + extraClass);
                    break;
                case '15-15-15-25':
                    classObj[0].push('advgb-is-one-fifth' + extraClass);
                    classObj[1].push('advgb-is-one-fifth' + extraClass);
                    classObj[2].push('advgb-is-one-fifth' + extraClass);
                    classObj[3].push('advgb-is-two-fifths' + extraClass);
                    break;
                case '12x4':
                    for ( let i = 0; i < 4; i++) {
                        classObj[i].push('advgb-is-half' + extraClass);
                    }
                    break;
                case '12x6':
                    for ( let i = 0; i < 6; i++) {
                        classObj[i].push('advgb-is-half' + extraClass);
                    }
                    break;
                case '13x6':
                    for ( let i = 0; i < 6; i++) {
                        classObj[i].push('advgb-is-one-third' + extraClass);
                    }
                    break;
                case 'stacked':
                    for ( let i = 0; i < 6; i++) {
                        classObj[i].push('advgb-is-full' + extraClass);
                    }
                    break;
                default:
                    break;
            }

            return classObj;
        }

        static jsUcfirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        render() {
            const { attributes, setAttributes, clientId, className } = this.props;
            const { tabSelected } = this.state;
            const {
                columns,
                columnsLayout, columnsLayoutT, columnsLayoutM,
                marginUnit,
                marginTop, marginRight, marginBottom, marginLeft,
                marginTopM, marginRightM, marginBottomM, marginLeftM,
                paddingUnit,
                paddingTop, paddingRight, paddingBottom, paddingLeft,
                paddingTopM, paddingRightM, paddingBottomM, paddingLeftM,
                vAlign,
                gutter,
                collapsedGutter,
                collapsedRtl,
                columnsWrapped,
                contentMaxWidth,
                contentMaxWidthUnit,
                contentMinHeight,
                contentMinHeightUnit,
                contentMaxHeight,
                contentMaxHeightUnit,
                wrapperTag,
                isPreview,
            } = attributes;

            const blockClasses = [
                'advgb-columns',
                className,
                vAlign && `columns-valign-${vAlign}`,
                columns && `advgb-columns-${columns}`,
                columnsLayout && `layout-${columnsLayout}`,
                columnsLayoutT && `tbl-layout-${columnsLayoutT}`,
                columnsLayoutM && `mbl-layout-${columnsLayoutM}`,
                !!gutter && `gutter-${gutter}`,
                !!collapsedGutter && `vgutter-${collapsedGutter}`,
                collapsedRtl && 'order-rtl',
                columnsWrapped && 'columns-wrapped',
            ].filter( Boolean ).join( ' ' );

            if (!columns) {
                return (
                    isPreview ?
                        <img alt={__('Columns Manager', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                        :
                    <div className="advgb-columns-select-wrapper">
                        <div className="advgb-columns-select-title">
                            { __( 'Pickup a columns layout', 'advanced-gutenberg' ) }
                        </div>
                        <div className="advgb-columns-select-layout">
                            {COLUMNS_LAYOUTS.map( (layout, index) => {
                                return (
                                    <Tooltip text={ layout.title } key={ index }>
                                        <div className="advgb-columns-layout"
                                             onClick={ () => setAttributes( {
                                                 columns: layout.columns,
                                                 columnsLayout: layout.layout
                                             } ) }
                                        >
                                            <img src={advgbBlocks.pluginUrl + '/assets/blocks/columns/icons/' + layout.icon + '.png'}
                                                 alt={ layout.layout }
                                            />
                                        </div>
                                    </Tooltip>
                                )
                            } ) }
                        </div>
                    </div>
                )
            }

            const COLUMNS_LAYOUTS_FILTERED = COLUMNS_LAYOUTS.filter( (item) => item.columns === columns );
            const COLUMNS_LAYOUTS_RESPONSIVE_FILTERED = COLUMNS_LAYOUTS_RESPONSIVE.filter( (item) => item.columns === columns );
            COLUMNS_LAYOUTS_RESPONSIVE_FILTERED.push( COLUMNS_LAYOUTS_STACKED );
            const VERT_ALIGNMENT_CONTROLS = [
                {
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M8 11h3v10h2V11h3l-4-4-4 4zM4 3v2h16V3H4z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    title: __( 'Vertical Align Top', 'advanced-gutenberg' ),
                    isActive: vAlign === 'top',
                    onClick: () => setAttributes( { vAlign: 'top' } )
                },
                {
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M8 19h3v4h2v-4h3l-4-4-4 4zm8-14h-3V1h-2v4H8l4 4 4-4zM4 11v2h16v-2H4z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    title: __( 'Vertical Align Middle', 'advanced-gutenberg' ),
                    isActive: vAlign === 'middle',
                    onClick: () => setAttributes( { vAlign: 'middle' } )
                },
                {
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M16 13h-3V3h-2v10H8l4 4 4-4zM4 19v2h16v-2H4z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    title: __( 'Vertical Align Bottom', 'advanced-gutenberg' ),
                    isActive: vAlign === 'bottom',
                    onClick: () => setAttributes( { vAlign: 'bottom' } )
                },
                {
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 12 32">
                            <polygon points="8,20 8,26 12,26 6,32 0,26 4,26 4,20"/>
                            <polygon points="4,12 4,6 0,6 6,0 12,6 8,6 8,12"/>
                        </svg>
                    ),
                    title: __( 'Inner Columns Full Height', 'advanced-gutenberg' ),
                    isActive: vAlign === 'full',
                    onClick: () => setAttributes( { vAlign: 'full' } )
                },
            ];
            const MARGIN_PADDING_CONTROLS = [
                {label:'Top', icon: 'arrow-up-alt2'},
                {label:'Right', icon: 'arrow-right-alt2'},
                {label:'Bottom', icon: 'arrow-down-alt2'},
                {label:'Left', icon: 'arrow-left-alt2'},
            ];

            let deviceLetter = '';
            if (tabSelected === 'tablet') deviceLetter = 'T';
            if (tabSelected === 'mobile') deviceLetter = 'M';

            return (
                isPreview ?
                    <img alt={__('Columns Manager ', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <BlockControls>
                        <Toolbar controls={ VERT_ALIGNMENT_CONTROLS } />
                    </BlockControls>
                    <InspectorControls>
                        <PanelBody title={ __( 'Columns Settings', 'advanced-gutenberg' ) }>
                            <PanelBody title={ __( 'Responsive Settings', 'advanced-gutenberg' ) }>
                                <div className="advgb-columns-responsive-items">
                                    {['desktop', 'tablet', 'mobile'].map( (device, index) => {
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
                                <div className="advgb-columns-select-layout on-inspector">
                                    {COLUMNS_LAYOUTS_FILTERED.map( (layout, index) => {
                                        const layoutClasses = [
                                            'advgb-columns-layout',
                                            tabSelected === 'desktop' && layout.layout === columnsLayout && 'is-selected',
                                            tabSelected === 'tablet' && layout.layout === columnsLayoutT && 'is-selected',
                                            tabSelected === 'mobile' && layout.layout === columnsLayoutM && 'is-selected',
                                        ].filter( Boolean ).join( ' ' );

                                        return (
                                            <Tooltip text={ layout.title } key={ index }>
                                                <div className={ layoutClasses }
                                                     onClick={ () => {
                                                         setAttributes( {
                                                             ['columnsLayout' + deviceLetter]: layout.layout
                                                         } );
                                                         this.setState( { random: Math.random() } );
                                                     } }
                                                >
                                                    <img src={advgbBlocks.pluginUrl + '/assets/blocks/columns/icons/' + layout.icon + '.png'}
                                                         alt={ layout.layout }
                                                    />
                                                </div>
                                            </Tooltip>
                                        )
                                    } ) }
                                    {tabSelected !== 'desktop' && COLUMNS_LAYOUTS_RESPONSIVE_FILTERED.map( (layout, index) => {
                                        const layoutClasses = [
                                            'advgb-columns-layout',
                                            tabSelected === 'tablet' && layout.layout === columnsLayoutT && 'is-selected',
                                            tabSelected === 'mobile' && layout.layout === columnsLayoutM && 'is-selected',
                                        ].filter( Boolean ).join( ' ' );

                                        return (
                                            <Tooltip text={ layout.title } key={ index }>
                                                <div className={ layoutClasses }
                                                     onClick={ () => {
                                                         setAttributes( {
                                                             ['columnsLayout' + deviceLetter]: layout.layout
                                                         } );
                                                         this.setState( { random: Math.random() } );
                                                     } }
                                                >
                                                    <img src={advgbBlocks.pluginUrl + '/assets/blocks/columns/icons/' + layout.icon + '.png'}
                                                         alt={ layout.layout }
                                                    />
                                                </div>
                                            </Tooltip>
                                        )
                                    } ) }
                                </div>
                                {tabSelected === 'desktop' && (
                                    <SelectControl
                                        label={ __( 'Space between columns', 'advanced-gutenberg' ) }
                                        value={ gutter }
                                        options={ GUTTER_OPTIONS }
                                        onChange={ (value) => setAttributes( { gutter: parseInt(value) } ) }
                                    />
                                ) }
                                {tabSelected === 'mobile' && columnsLayoutM === 'stacked' && (
                                    <Fragment>
                                        <SelectControl
                                            label={ __( 'Vertical space when collapsed', 'advanced-gutenberg' ) }
                                            value={ collapsedGutter }
                                            options={ GUTTER_OPTIONS }
                                            onChange={ (value) => setAttributes( { collapsedGutter: parseInt(value) } ) }
                                        />
                                        <ToggleControl
                                            label={ __( 'Collapsed Order RTL', 'advanced-gutenberg' ) }
                                            checked={ collapsedRtl }
                                            onChange={ () => setAttributes( { collapsedRtl: !collapsedRtl } ) }
                                        />
                                    </Fragment>
                                ) }
                                <PanelBody title={ tabSelected !== 'desktop' ? AdvColumnsEdit.jsUcfirst(tabSelected) + __(' Padding', 'advanced-gutenberg') : __('Padding', 'advanced-gutenberg') }
                                           initialOpen={false}
                                >
                                    <div className="advgb-controls-title">
                                        <span>{ __( 'Unit', 'advanced-gutenberg' ) }</span>
                                        <div className="advgb-unit-wrapper" key="unit">
                                            { ['px', 'em', 'vh', '%'].map( (unit, idx) => (
                                                <span className={`advgb-unit ${paddingUnit === unit ? 'selected' : ''}`} key={idx}
                                                      onClick={ () => setAttributes( { paddingUnit: unit } ) }
                                                >
                                                    {unit}
                                                </span>
                                            ) ) }
                                        </div>
                                    </div>
                                    {MARGIN_PADDING_CONTROLS.map((pos, idx) => (
                                        <RangeControl
                                            key={ idx }
                                            beforeIcon={ pos.icon }
                                            value={ attributes['padding' + pos.label + deviceLetter] || 0 }
                                            min={ 0 }
                                            max={ 50 }
                                            onChange={ (value) => setAttributes( { ['padding' + pos.label + deviceLetter]: value } ) }
                                        />
                                    ) ) }
                                </PanelBody>
                                <PanelBody title={ tabSelected !== 'desktop' ? AdvColumnsEdit.jsUcfirst(tabSelected) + __(' Margin', 'advanced-gutenberg') : __('Margin', 'advanced-gutenberg') }
                                           initialOpen={false}
                                >
                                    <div className="advgb-controls-title">
                                        <span>{ __( 'Unit', 'advanced-gutenberg' ) }</span>
                                        <div className="advgb-unit-wrapper" key="unit">
                                            { ['px', 'em', 'vh', '%'].map( (unit, idx) => (
                                                <span className={`advgb-unit ${marginUnit === unit ? 'selected' : ''}`} key={idx}
                                                      onClick={ () => setAttributes( { marginUnit: unit } ) }
                                                >
                                                    {unit}
                                                </span>
                                            ) ) }
                                        </div>
                                    </div>
                                    {MARGIN_PADDING_CONTROLS.map((pos, idx) => (
                                        <RangeControl
                                            key={ idx }
                                            beforeIcon={ pos.icon }
                                            value={ attributes['margin' + pos.label + deviceLetter] || 0 }
                                            min={ 0 }
                                            max={ 50 }
                                            onChange={ (value) => setAttributes( { ['margin' + pos.label + deviceLetter]: value } ) }
                                        />
                                    ) ) }
                                </PanelBody>
                            </PanelBody>
                            <PanelBody title={ __( 'Row Settings', 'advanced-gutenberg' ) } initialOpen={ false }>
                                <ToggleControl
                                    label={ __( 'Columns Wrapped', 'advanced-gutenberg' ) }
                                    help={ __( 'If your columns is overflown, it will be separated to a new line (eg: Use this with Columns Spacing).', 'advanced-gutenberg' ) }
                                    checked={ columnsWrapped }
                                    onChange={ () => setAttributes( { columnsWrapped: !columnsWrapped } ) }
                                />
                                <SelectControl
                                    label={ __( 'Wrapper Tag', 'advanced-gutenberg' ) }
                                    value={ wrapperTag }
                                    options={ [
                                        { label: 'Div', value: 'div' },
                                        { label: 'Header', value: 'header' },
                                        { label: 'Section', value: 'section' },
                                        { label: 'Main', value: 'main' },
                                        { label: 'Article', value: 'article' },
                                        { label: 'Aside', value: 'aside' },
                                        { label: 'Footer', value: 'footer' },
                                    ] }
                                    onChange={ (value) => setAttributes( { wrapperTag: value } ) }
                                />
                                <RangeControl
                                    label={ [
                                        __( 'Content Max Width', 'advanced-gutenberg' ),
                                        <div className="advgb-unit-wrapper" key="unit">
                                            { ['px', 'vw', '%'].map( (unit, idx) => (
                                                <span className={`advgb-unit ${contentMaxWidthUnit === unit ? 'selected' : ''}`} key={idx}
                                                      onClick={ () => setAttributes( { contentMaxWidthUnit: unit } ) }
                                                >
                                                    {unit}
                                                </span>
                                            ) ) }
                                        </div>
                                    ] }
                                    value={ contentMaxWidth }
                                    min={ 0 }
                                    max={ contentMaxWidthUnit === 'px' ? 2000 : 100 }
                                    onChange={ (value) => setAttributes( { contentMaxWidth: value } ) }
                                />
                                <RangeControl
                                    label={ [
                                        __( 'Content Min Height', 'advanced-gutenberg' ),
                                        <div className="advgb-unit-wrapper" key="unit">
                                            { ['px', 'vw', 'vh'].map( (unit, idx) => (
                                                <span className={`advgb-unit ${contentMinHeightUnit === unit ? 'selected' : ''}`} key={idx}
                                                      onClick={ () => setAttributes( { contentMinHeightUnit: unit } ) }
                                                >
                                                    {unit}
                                                </span>
                                            ) ) }
                                        </div>
                                    ] }
                                    value={ contentMinHeight }
                                    min={ 0 }
                                    max={ contentMinHeightUnit === 'px' ? 2000 : 200 }
                                    onChange={ (value) => setAttributes( { contentMinHeight: value } ) }
                                />
                                <RangeControl
                                    label={ [
                                        __( 'Content Max Height', 'advanced-gutenberg' ),
                                        <div className="advgb-unit-wrapper" key="unit">
                                            { ['px', 'vw', 'vh'].map( (unit, idx) => (
                                                <span className={`advgb-unit ${contentMaxHeightUnit === unit ? 'selected' : ''}`} key={idx}
                                                      onClick={ () => setAttributes( { contentMaxHeightUnit: unit } ) }
                                                >
                                                    {unit}
                                                </span>
                                            ) ) }
                                        </div>
                                    ] }
                                    value={ contentMaxHeight }
                                    min={ 0 }
                                    max={ contentMaxHeightUnit === 'px' ? 2000 : 200 }
                                    onChange={ (value) => setAttributes( { contentMaxHeight: value } ) }
                                />
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                    <span className="advgb-columns-spacing">&nbsp;</span>
                    <div className="advgb-columns-wrapper">
                        <div className={ blockClasses }
                             style={ {
                                 maxWidth: !!contentMaxWidth ? `${contentMaxWidth}${contentMaxWidthUnit}` : undefined,
                                 minHeight: !!contentMinHeight ? `${contentMinHeight}${contentMinHeightUnit}` : undefined,
                                 maxHeight: !!contentMaxHeight ? `${contentMaxHeight}${contentMaxHeightUnit}` : undefined,
                             } }
                        >
                            <InnerBlocks
                                template={ times( parseInt(columns), () => [ 'advgb/column' ] ) }
                                templateLock="all"
                                allowedBlocks={ [ 'advgb/column' ] }
                                random={ this.state.random }
                            />
                        </div>
                    </div>
                    <style>
                        {`#block-${clientId} .advgb-columns-wrapper .advgb-columns {
                            margin-top: ${marginTop + marginUnit};
                            margin-right: ${marginRight + marginUnit};
                            margin-bottom: ${marginBottom + marginUnit};
                            margin-left: ${marginLeft + marginUnit};
                            padding-top: ${paddingTop + paddingUnit};
                            padding-right: ${paddingRight + paddingUnit};
                            padding-bottom: ${paddingBottom + paddingUnit};
                            padding-left: ${paddingLeft + paddingUnit};
                        }
                        @media screen and (max-width: 767px) {
                            #block-${clientId} .advgb-columns-wrapper .advgb-columns {
                                margin-top: ${marginTopM + marginUnit};
                                margin-right: ${marginRightM + marginUnit};
                                margin-bottom: ${marginBottomM + marginUnit};
                                margin-left: ${marginLeftM + marginUnit};
                                padding-top: ${paddingTopM + paddingUnit};
                                padding-right: ${paddingRightM + paddingUnit};
                                padding-bottom: ${paddingBottomM + paddingUnit};
                                padding-left: ${paddingLeftM + paddingUnit};
                            }
                        }`}
                    </style>
                </Fragment>
            )
        }
    }

    const blockAttrs = {
        columns: {
            type: 'number',
        },
        columnsLayout: {
            type: 'string',
        },
        columnsLayoutT: {
            type: 'string',
        },
        columnsLayoutM: {
            type: 'string',
            default: 'stacked',
        },
        marginTop: {
            type: 'number',
        },
        marginTopT: {
            type: 'number',
        },
        marginTopM: {
            type: 'number',
        },
        marginRight: {
            type: 'number',
        },
        marginRightT: {
            type: 'number',
        },
        marginRightM: {
            type: 'number',
        },
        marginBottom: {
            type: 'number',
        },
        marginBottomT: {
            type: 'number',
        },
        marginBottomM: {
            type: 'number',
        },
        marginLeft: {
            type: 'number',
        },
        marginLeftT: {
            type: 'number',
        },
        marginLeftM: {
            type: 'number',
        },
        marginUnit: {
            type: 'string',
            default: 'px',
        },
        paddingTop: {
            type: 'number',
        },
        paddingTopT: {
            type: 'number',
        },
        paddingTopM: {
            type: 'number',
        },
        paddingRight: {
            type: 'number',
        },
        paddingRightT: {
            type: 'number',
        },
        paddingRightM: {
            type: 'number',
        },
        paddingBottom: {
            type: 'number',
        },
        paddingBottomT: {
            type: 'number',
        },
        paddingBottomM: {
            type: 'number',
        },
        paddingLeft: {
            type: 'number',
        },
        paddingLeftT: {
            type: 'number',
        },
        paddingLeftM: {
            type: 'number',
        },
        paddingUnit: {
            type: 'string',
            default: 'px',
        },
        gutter: {
            type: 'number',
            default: 0,
        },
        collapsedGutter: {
            type: 'number',
            default: 10,
        },
        collapsedRtl: {
            type: 'boolean',
            default: false,
        },
        vAlign: {
            type: 'string',
        },
        columnsWrapped: {
            type: 'boolean',
            default: false,
        },
        contentMaxWidth: {
            type: 'number',
        },
        contentMaxWidthUnit: {
            type: 'string',
            default: 'px',
        },
        contentMinHeight: {
            type: 'number',
        },
        contentMinHeightUnit: {
            type: 'string',
            default: 'px',
        },
        contentMaxHeight: {
            type: 'number',
        },
        contentMaxHeightUnit: {
            type: 'string',
            default: 'px',
        },
        wrapperTag: {
            type: 'string',
            default: 'div',
        },
        colId: {
            type: 'string',
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

    registerBlockType( 'advgb/columns', {
        title: __( 'Columns Manager', 'advanced-gutenberg' ),
        description: __( 'Row layout with columns you decided.', 'advanced-gutenberg' ),
        icon: {
            src: 'layout',
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'columns', 'advanced-gutenberg' ), __( 'row', 'advanced-gutenberg' ), __( 'layout', 'advanced-gutenberg' ) ],
        supports: {
            align: [ 'wide', 'full' ],
            html: false,
        },
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        edit: AdvColumnsEdit,
        save: function ( { attributes } ) {
            const {
                columns,
                columnsLayout, columnsLayoutT, columnsLayoutM,
                vAlign,
                gutter,
                collapsedGutter,
                collapsedRtl,
                columnsWrapped,
                contentMaxWidth,
                contentMaxWidthUnit,
                contentMinHeight,
                contentMinHeightUnit,
                contentMaxHeight,
                contentMaxHeightUnit,
                wrapperTag,
                colId,
            } = attributes;
            const Tag = wrapperTag;

            const blockClasses = [
                'advgb-columns',
                'advgb-columns-row',
                'advgb-is-mobile',
                vAlign && `columns-valign-${vAlign}`,
                columns && `advgb-columns-${columns}`,
                columnsLayout && `layout-${columnsLayout}`,
                columnsLayoutT && `tbl-layout-${columnsLayoutT}`,
                columnsLayoutM && `mbl-layout-${columnsLayoutM}`,
                !!gutter && `gutter-${gutter}`,
                !!collapsedGutter && `vgutter-${collapsedGutter}`,
                collapsedRtl && 'order-rtl',
                columnsWrapped && 'columns-wrapped',
            ].filter( Boolean ).join( ' ' );

            return (
                <Tag className="advgb-columns-wrapper" id={ colId }>
                    <div className="advgb-columns-container">
                        <div className={ blockClasses }
                             style={ {
                                 maxWidth: !!contentMaxWidth ? `${contentMaxWidth}${contentMaxWidthUnit}` : undefined,
                                 minHeight: !!contentMinHeight ? `${contentMinHeight}${contentMinHeightUnit}` : undefined,
                                 maxHeight: !!contentMaxHeight ? `${contentMaxHeight}${contentMaxHeightUnit}` : undefined,
                             } }
                        >
                            <InnerBlocks.Content />
                        </div>
                    </div>
                </Tag>
            );
        },
        deprecated: [
            {
                attributes: blockAttrs,
                save: function ( { attributes } ) {
                    const {
                        columns,
                        columnsLayout, columnsLayoutT, columnsLayoutM,
                        vAlign,
                        gutter,
                        collapsedGutter,
                        collapsedRtl,
                        columnsWrapped,
                        contentMaxWidth,
                        contentMaxWidthUnit,
                        contentMinHeight,
                        contentMinHeightUnit,
                        contentMaxHeight,
                        contentMaxHeightUnit,
                        wrapperTag,
                        colId,
                    } = attributes;
                    const Tag = wrapperTag;

                    const blockClasses = [
                        'advgb-columns',
                        'advgb-is-mobile',
                        vAlign && `columns-valign-${vAlign}`,
                        columns && `advgb-columns-${columns}`,
                        columnsLayout && `layout-${columnsLayout}`,
                        columnsLayoutT && `tbl-layout-${columnsLayoutT}`,
                        columnsLayoutM && `mbl-layout-${columnsLayoutM}`,
                        !!gutter && `gutter-${gutter}`,
                        !!collapsedGutter && `vgutter-${collapsedGutter}`,
                        collapsedRtl && 'order-rtl',
                        columnsWrapped && 'columns-wrapped',
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <Tag className="advgb-columns-wrapper">
                            <div className={ blockClasses } id={ colId }
                                 style={ {
                                     maxWidth: !!contentMaxWidth ? `${contentMaxWidth}${contentMaxWidthUnit}` : undefined,
                                     minHeight: !!contentMinHeight ? `${contentMinHeight}${contentMinHeightUnit}` : undefined,
                                     maxHeight: !!contentMaxHeight ? `${contentMaxHeight}${contentMaxHeightUnit}` : undefined,
                                 } }
                            >
                                <InnerBlocks.Content />
                            </div>
                        </Tag>
                    );
                }
            },
            {
                attributes: blockAttrs,
                save: function ( { attributes } ) {
                    const {
                        columns,
                        columnsLayout, columnsLayoutT, columnsLayoutM,
                        vAlign,
                        gutter,
                        collapsedGutter,
                        collapsedRtl,
                        columnsWrapped,
                        contentMaxWidth,
                        contentMaxWidthUnit,
                        contentMinHeight,
                        contentMinHeightUnit,
                        wrapperTag,
                        colId,
                    } = attributes;
                    const Tag = wrapperTag;

                    const blockClasses = [
                        'advgb-columns',
                        'columns is-mobile',
                        vAlign && `columns-valign-${vAlign}`,
                        columns && `advgb-columns-${columns}`,
                        columnsLayout && `layout-${columnsLayout}`,
                        columnsLayoutT && `tbl-layout-${columnsLayoutT}`,
                        columnsLayoutM && `mbl-layout-${columnsLayoutM}`,
                        !!gutter && `gutter-${gutter}`,
                        !!collapsedGutter && `vgutter-${collapsedGutter}`,
                        collapsedRtl && 'order-rtl',
                        columnsWrapped && 'columns-wrapped',
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <Tag className="advgb-columns-wrapper">
                            <div className={ blockClasses } id={ colId }
                                 style={ {
                                     maxWidth: !!contentMaxWidth ? `${contentMaxWidth}${contentMaxWidthUnit}` : undefined,
                                     minHeight: !!contentMinHeight ? `${contentMinHeight}${contentMinHeightUnit}` : undefined,
                                 } }
                            >
                                <InnerBlocks.Content />
                            </div>
                        </Tag>
                    );
                },
            }
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );