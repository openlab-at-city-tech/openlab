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
        { columns: 1, layout: '100', icon: '100', title: __( 'One' ) },
        { columns: 2, layout: '12-12', icon: '12-12', title: __( 'Two: 1/2 - 1/2' ) },
        { columns: 2, layout: '23-13', icon: '23-13', title: __( 'Two: 2/3 - 1/3' ) },
        { columns: 2, layout: '13-23', icon: '13-23', title: __( 'Two: 1/3 - 2/3' ) },
        { columns: 2, layout: '14-34', icon: '14-34', title: __( 'Two: 1/4 - 3/4' ) },
        { columns: 2, layout: '34-14', icon: '34-14', title: __( 'Two: 3/4 - 1/4' ) },
        { columns: 2, layout: '15-45', icon: '15-45', title: __( 'Two: 1/5 - 4/5' ) },
        { columns: 2, layout: '45-15', icon: '45-15', title: __( 'Two: 4/5 - 1/5' ) },
        { columns: 3, layout: '13-13-13', icon: '13-13-13', title: __( 'Three: 1/3 - 1/3 - 1/3' ) },
        { columns: 3, layout: '12-14-14', icon: '12-14-14', title: __( 'Three: 1/2 - 1/4 - 1/4' ) },
        { columns: 3, layout: '14-14-12', icon: '14-14-12', title: __( 'Three: 1/4 - 1/4 - 1/2' ) },
        { columns: 3, layout: '14-12-14', icon: '14-12-14', title: __( 'Three: 1/4 - 1/2 - 1/4' ) },
        { columns: 3, layout: '15-35-15', icon: '15-35-15', title: __( 'Three: 1/5 - 3/5 - 1/5' ) },
        { columns: 3, layout: '35-15-15', icon: '35-15-15', title: __( 'Three: 3/5 - 1/5 - 1/5' ) },
        { columns: 3, layout: '15-15-35', icon: '15-15-35', title: __( 'Three: 1/5 - 1/5 - 3/5' ) },
        { columns: 3, layout: '16-46-16', icon: '16-46-16', title: __( 'Three: 1/6 - 4/6 - 1/6' ) },
        { columns: 4, layout: '14-14-14-14', icon: '14-14-14-14', title: __( 'Four: 1/4 - 1/4 - 1/4 - 1/4' ) },
        { columns: 4, layout: '36-16-16-16', icon: '36-16-16-16', title: __( 'Four: 3/6 - 1/6 - 1/6 - 1/6' ) },
        { columns: 4, layout: '16-16-16-36', icon: '16-16-16-36', title: __( 'Four: 1/6 - 1/6 - 1/6 - 3/6' ) },
        { columns: 4, layout: '15-15-15-25', icon: '15-15-15-25', title: __( 'Four: 1/5 - 1/5 - 1/5 - 2/5' ) },
        { columns: 4, layout: '25-15-15-15', icon: '25-15-15-15', title: __( 'Four: 2/5 - 1/5 - 1/5 - 1/5' ) },
        { columns: 5, layout: 'five', icon: '15-15-15-15-15', title: __( 'Five' ) },
        { columns: 6, layout: 'six', icon: '16-16-16-16-16-16', title: __( 'Six' ) },
    ];
    const COLUMNS_LAYOUTS_RESPONSIVE = [
        { columns: 3, layout: '1-12-12', icon: '100-12-12', title: __( 'Three: 100 - 1/2 - 1/2' ) },
        { columns: 3, layout: '12-12-1', icon: '12-12-100', title: __( 'Three: 1/2 - 1/2 - 100' ) },
        { columns: 4, layout: '12x4', icon: '12-12-12-12', title: __( 'Four: Two Columns' ) },
        { columns: 6, layout: '12x6', icon: '12-12-12-12', title: __( 'Six: Two Columns' ) },
        { columns: 6, layout: '13x6', icon: '13-13-13-13-13-13', title: __( 'Six: Three Columns' ) },
    ];
    const COLUMNS_LAYOUTS_STACKED = {
        columns: 1, layout: 'stacked', icon: 'stacked', title: __( 'Stacked' )
    };
    const GUTTER_OPTIONS = [
        {label: __( 'None' ), value: 0},
        {label: '10px', value: 10},
        {label: '20px', value: 20},
        {label: '30px', value: 30},
        {label: '40px', value: 40},
        {label: '50px', value: 50},
        {label: '70px', value: 70},
        {label: '90px', value: 90},
    ];

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
                wrapperTag,
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
                    <div className="advgb-columns-select-wrapper">
                        <div className="advgb-columns-select-title">
                            { __( 'Pickup a columns layout' ) }
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
                    title: __( 'Vertical Align Top' ),
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
                    title: __( 'Vertical Align Middle' ),
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
                    title: __( 'Vertical Align Bottom' ),
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
                    title: __( 'Inner Columns Full Height' ),
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
                <Fragment>
                    <BlockControls>
                        <Toolbar controls={ VERT_ALIGNMENT_CONTROLS } />
                    </BlockControls>
                    <InspectorControls>
                        <PanelBody title={ __( 'Columns Settings' ) }>
                            <PanelBody title={ __( 'Responsive Settings' ) }>
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
                                        label={ __( 'Space between columns' ) }
                                        value={ gutter }
                                        options={ GUTTER_OPTIONS }
                                        onChange={ (value) => setAttributes( { gutter: parseInt(value) } ) }
                                    />
                                ) }
                                {tabSelected === 'mobile' && columnsLayoutM === 'stacked' && (
                                    <Fragment>
                                        <SelectControl
                                            label={ __( 'Vertical space when collapsed' ) }
                                            value={ collapsedGutter }
                                            options={ GUTTER_OPTIONS }
                                            onChange={ (value) => setAttributes( { collapsedGutter: parseInt(value) } ) }
                                        />
                                        <ToggleControl
                                            label={ __( 'Collapsed Order RTL' ) }
                                            checked={ collapsedRtl }
                                            onChange={ () => setAttributes( { collapsedRtl: !collapsedRtl } ) }
                                        />
                                    </Fragment>
                                ) }
                                <PanelBody title={ tabSelected !== 'desktop' ? AdvColumnsEdit.jsUcfirst(tabSelected) + __(' Padding') : __('Padding') }
                                           initialOpen={false}
                                >
                                    <div className="advgb-controls-title">{ __( 'Unit (px)' ) }</div>
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
                                <PanelBody title={ tabSelected !== 'desktop' ? AdvColumnsEdit.jsUcfirst(tabSelected) + __(' Margin') : __('Margin') }
                                           initialOpen={false}
                                >
                                    <div className="advgb-controls-title">
                                        <span>{ __( 'Unit' ) }</span>
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
                            <PanelBody title={ __( 'Row Settings' ) } initialOpen={ false }>
                                <ToggleControl
                                    label={ __( 'Columns Wrapped' ) }
                                    help={ __( 'If your columns is overflown, it will be separated to a new line (eg: Use this with Columns Spacing).' ) }
                                    checked={ columnsWrapped }
                                    onChange={ () => setAttributes( { columnsWrapped: !columnsWrapped } ) }
                                />
                                <SelectControl
                                    label={ __( 'Wrapper Tag' ) }
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
                                        __( 'Content Max Width' ),
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
                                        __( 'Content Min Height' ),
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
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                    <div className="advgb-columns-wrapper">
                        <div className={ blockClasses }
                             style={ {
                                 maxWidth: !!contentMaxWidth ? `${contentMaxWidth}${contentMaxWidthUnit}` : undefined,
                                 minHeight: !!contentMinHeight ? `${contentMinHeight}${contentMinHeightUnit}` : undefined,
                             } }
                        >
                            <InnerBlocks
                                template={ times( parseInt(columns), () => [ 'advgb/column' ] ) }
                                templateLock="all"
                                allowdBlockType={ [ 'advgb/column' ] }
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
                            padding-top: ${paddingTop}px;
                            padding-right: ${paddingRight}px;
                            padding-bottom: ${paddingBottom}px;
                            padding-left: ${paddingLeft}px;
                        }
                        @media screen and (max-width: 767px) {
                            #block-${clientId} .advgb-columns-wrapper .advgb-columns {
                                margin-top: ${marginTopM + marginUnit};
                                margin-right: ${marginRightM + marginUnit};
                                margin-bottom: ${marginBottomM + marginUnit};
                                margin-left: ${marginLeftM + marginUnit};
                                padding-top: ${paddingTopM}px;
                                padding-right: ${paddingRightM}px;
                                padding-bottom: ${paddingBottomM}px;
                                padding-left: ${paddingLeftM}px;
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
        }
    };

    registerBlockType( 'advgb/columns', {
        title: __( 'Columns Manager' ),
        description: __( 'Row layout with columns you decided.' ),
        icon: {
            src: 'layout',
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'columns' ), __( 'row' ), __( 'layout' ) ],
        supports: {
            align: [ 'wide', 'full' ],
            html: false,
        },
        attributes: blockAttrs,
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
                         } }
                    >
                        <InnerBlocks.Content />
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