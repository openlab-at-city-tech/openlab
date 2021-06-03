(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents, wpCompose ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings, InnerBlocks } = wpBlockEditor;
    const { Dashicon, Tooltip, PanelBody, RangeControl, SelectControl, Button } = wpComponents;
    const { withDispatch, select, dispatch } = wp.data;
    const { compose } = wpCompose;
    const { times } = lodash;

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

    const previewImageData = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAADzCAYAAACv4wv1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAACiRJREFUeNrs3c1rHOcBwOGZ2V3JkoJcArEbKCWE4NC4EEMwlPYQY3qoaeihkEtPOZkGcogvaS7ufxCoIZReeuglh+BDC8WXkgRaSopPwW1CoDkEO20gbgyOYq0l7e5032U3jEaz0n7Maker54FhJVmRJjP6zfvO7GgVRQAAAAAAAAAAcIzEs/ii9Xr90L4XHHFp/gOtVqu6offjjgUO5QQfPlZW9PEM4o73+fqih+Fxp/v92zTRxyVFftCjyGH00LOPaRmxxxNGng84zsUdHxA9sH/caS70dJrY4ykiLwo7LEn/44nYYeTIR10mij2eMvIkF3jv8aWXXjrxyiuvnFlbW/tWmqZJp9Pp/Tf9Rzj24jjuLVtbW82bN29++tprr/1vY2Oj0485/5h/e+zY4zEiLxrFk8xj8swzz9Rv3Ljxk1OnTl3ufv5z3f+Rk0Zx2GdIT9Nmu93+tBv5n956663fv/rqq3czYeeXiUf2cUIfFnktvP3666+vX7169XdLS0s/s/tgfN0Z739v3br1y/Pnz/+9+247E3h7yCgfnnNPSws9M5rnz8V7kZ85c6bxwQcf/LnRaPzI7oKpRvjNd95556eXLl36Zz/wdi72bPAjj+rJhFP8JDtt767YL7qR/9BugqnP3Veff/753547d+5EfyCt5Xqb6MJ2Ms46FCy17pFnpXtO/mvn4lCO7qD5vWvXrv0gTKZzoSdDOpw+9IL71nedo7/88svfTZLktN0D5Xnqqad+nIm8VhD5WOrjzizy5+mnT5/+tt0C5VpbW/t+P/DBOflgcE0z76ejfr1kxLgLIw9Lp9Op2y1Q/gw+N2UfOm3P3ak61Tl6/pbXby7EtdvtxD6BmUiGnKNP9IXGnbbver87orsIByVL0zTeZxQf++r7JKPxrm/mtlaYmaninjb0XSvRP/IAs4+96DR6JqHvubzfDd2ugNlFPtEIPu05uhEd5hf7xC/k4oo5HANTh27qDoc6dZ/ozjgjOhjRAaEDQgeEDggdEDogdEDoIHRA6IDQAaEDQgeEDggdEDoIHRA6IHRA6IDQAaEDQgeEDkK3CUDogNABoQNCB4QOCB0YVb2KK/X5559H9+7dO/yNUa9Hjz/+eLS+vr7n31qtVvTw4cMoTdNDX69GoxGdOHFi18c6nU7UbDZ7j4etVqtFKysrURzHChL6ZD755JPo7t27c/v+4QBz9uzZXbGHyL/66qu5rdPOzk4v6NXV1d774WBz//79uRx0BusTlpMnTyrI1H18W1tbc408O6PICiP5vGVnE2E7zSvygXa7HW1vbytI6JOFXgVhBM+ad1TZuKq4PggdEDogdEDogNCBvrpNwGELz2qE5+GTJImWl5dtECM6iyhEHu7q8zy8EX2PJ554IlpbWyv1a37xxRdT3aATRqOyR6Qw2m1ubk7034ZbU8veRsE87wrkmIUefoCL7kGf5w9wmHqG++MrMz2r2PpkhZtrBgewwY024aC2sbHRe3tpack0XugcdeFuvjBlH/axqh6ghA5jzjbCb7wNRvL8xbjwG3oIPfrwww8rt07hglJYqiLEM49f7x039LDNwroOft2VQ9j+NgEY0aF0YboepupeuELoLPj5elgwdQeEDggdEDoIfY6qcgtkVe/SCs87Wx8WIvSy72efxGOPPVa5A1C4F3zwdFRYn3k/NRWumrub7eio3ND19NNPR5999ln04MGDuXz/8AccHn300T2RPfLII3N7ldoww8j+AYcQWTgghjvM5vGKsIO73DwPLvSpfqjDr6RWTYg9LFWaNoeDDxy5qTsgdEDogNBB6MAiq/Rvr4U/FRyW/EsQQRWFpxvDs0bhGZGqPfVYydDDc8PhhQSr8tdVYVzhPoMqvXpOvYqRh1dn9Sd5OcrCzUxhNjqLl99eiHP0sIFEziIIM9KqzEqTKm4cWBQPHz4Uel4Yyedx7zbM8mda6AXn58AxmLoDQgeEDggdhA4IHRA6IHRA6IDQAaEDQgeh2wQgdEDogNABoQNCB4QOCB0QOggdEDpwtNSP4kovLy/3FhbbgwcP/Hmu4xx6kiS9P0/Lgk83u/tZ6KbugNABoYPQgUVyJK9oNZvN3gIY0QGhg6l7tY9OSRLVajV7r6JarVaUpqkNIfTphLviVlZW7L2K2tjYiHZ2dmwIU3dA6IDQAaEDOUfyYtzW1lbvyi7VZN8IvRSdTqe3AKbugNDB1L3SGo2GV5ipsHANxamV0Kdf6W7k7oyrrnAxTuim7oDQAaEDQgf2cmccpbNvhF4Kd8aBqTsgdBA6IHRA6IDQAaEDQgeEDggdhA4IHRA6IHRA6IDQAaEDQgeEDkIHhA4IHRA6IHRgYUOv1Wr2CAsljmOhF20UsbNIlpaWhF5kbW3NTwcLM5qvrKwIvUi9Xhc7CxH5+vp6lCTVSKySf2RxeXk5ajQaUbPZ9McUOXLC6WcYyatyfl7Z0HtTje6R0MgOCzp1B4QOCB0QOggdEDogdEDogNABoQNCB4QOQrcJQOiA0AGhA0IHhA4IHRA6IHQQOiB0QOiA0AGhA0IHhA4IHYQOCB0QOrC4ocdxbCvCbKS5x/zbRnQ4BgeAmYSeFryfNrtsdyjXzs7O/X1G9ZmP6Glm6XnvvfduT7sSwG537tx5v8wp/DRT917wb7755r3Nzc337Rooz/Xr1/+aG1Snir124JEgScLVtuySZB6TdrudPPnkk/969tlnfx7H8ZJdBFOP5n948cUXb3Tf7HSXdmbpZJZdM+tOp1PqOXr+CNNbLl++/O9bt25d7UZ/z26CyX355Zd/eeGFF35TEHTRMvKonowRefbtTuaxd9Q5f/78H999990rX3/99c00TbftMhjd1tbWfz7++ONrFy5c+NVHH33UzLY1bBQfx4FPgtfr9Sg3bU/6U/56Zmn0H2sXL148eeXKlefOnTt3aXV19TuZqT6Qs729ff/27dv/ePvtt//2xhtv3MnE3cotO/3HdlH8rVar9NDjfuj52OuZj9UyB4Vs6IKHvbPkPTPkgtBbBefrg8gPHOXrB31COFL0Y49y5wXZlYpzEaf92AefF4schgaf7Sl/AS5/Ea4zyfS9PsERKM6tXJyJPb/ySbT7ir1RHfYOmtmeBmG3ov2vtKfjnLPXx1ipuOAIFA85OiX9f09ysUdih4Mvbuem8EWxjzWqjxR6ZvqeZs4pBjFHBQeBpCByIzocPKIXXW0fGvlBF+EmnbrHBUeiqGCF4yGjucihOPZO7rG9T+Rj3/8+VnjdUT0fbDbkZMij0GG8UX2/x28+f9TRfNwRPTuqZx+z5+TZf+sIHUY+Tx+2dCYdxSce0QtG9mhIzAcFLngEvjf0KNrnVvPMeflMn14rWtk4t0LZEb0ocoHD8FF9WPTffM440/XSRtbMXXPRCFGLHA4e4Yf9GurEkZcWX0Hw4oaSpvXTBD7TEPcJHzgg9jLCBgAAAAAAZuX/AgwAYHi5yJYhRDUAAAAASUVORK5CYII=';

    /**
     * This allows for checking to see if the block needs to generate a new ID.
     */
    const advgbTabsUniqueIDs = [];

    class AdvTabsWrapper extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                viewport: 'desktop',
            };
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

        componentDidUpdate() {
            const { attributes, setAttributes } = this.props;
            const { isTransform } = attributes;

            if(isTransform) {
                setAttributes( {
                    isTransform: false
                } );
                this.props.updateTabActive( 0 );
            }
        }

        componentDidMount() {
            if ( ! this.props.attributes.uniqueID ) {
                this.props.setAttributes( {
                    uniqueID: '_' + this.props.clientId.substr( 2, 9 ),
                } );
                advgbTabsUniqueIDs.push( '_' + this.props.clientId.substr( 2, 9 ) );
            } else if ( advgbTabsUniqueIDs.includes( this.props.attributes.uniqueID ) ) {
                this.props.setAttributes( {
                    uniqueID: '_' + this.props.clientId.substr( 2, 9 ),
                } );
                advgbTabsUniqueIDs.push( '_' + this.props.clientId.substr( 2, 9 ) );
            } else {
                advgbTabsUniqueIDs.push( this.props.attributes.uniqueID );
            }
            if (!this.props.attributes.pid) {
                this.props.setAttributes( {
                    pid: `advgb-tabs-${this.props.clientId}`,
                } );
            }
            this.updateTabHeaders();
            this.props.resetOrder();
        }

        updateTabsAttr( attrs ) {
            const { setAttributes, clientId } = this.props;
            const { updateBlockAttributes } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);

            setAttributes( attrs );
            childBlocks.forEach( childBlockId => updateBlockAttributes( childBlockId, attrs ) );
            this.props.resetOrder();
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
            this.updateTabHeaders();
        }

        updateTabHeaders() {
            const { attributes, clientId } = this.props;
            const { tabHeaders } = attributes;
            const { updateBlockAttributes } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const { getBlockOrder } = !wp.blockEditor ? select( 'core/editor' ) : select( 'core/block-editor' );
            const childBlocks = getBlockOrder(clientId);

            childBlocks.forEach( childBlockId => updateBlockAttributes( childBlockId, {tabHeaders: tabHeaders} ) );
        }

        addTab() {
            const { attributes, setAttributes, clientId } = this.props;
            const { insertBlock } = !wp.blockEditor ? dispatch( 'core/editor' ) : dispatch( 'core/block-editor' );
            const tabItemBlock = createBlock('advgb/tab');

            insertBlock(tabItemBlock, attributes.tabHeaders.length, clientId);
            setAttributes( {
                tabHeaders: [
                    ...attributes.tabHeaders,
                    'Tab header'
                ]
            } );
            this.props.resetOrder();
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
            this.updateTabsAttr({tabActive: 0});
            this.props.resetOrder();
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
                isPreview,
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
                isPreview ?
                    <img alt={__('Advanced Tabs', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
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
                                    { label: __( 'None', 'advanced-gutenberg' ), value: 'none' },
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
                                    className={`advgb-tab ${tabActive === index ? 'advgb-tab-active' : ''}`}
                                    style={ {
                                        backgroundColor: headerBgColor,
                                        borderStyle: borderStyle,
                                        borderWidth: borderWidth + 'px',
                                        borderColor: borderColor,
                                        borderRadius: borderRadius + 'px',
                                    } }
                                >
                                    <a style={ { color: headerTextColor } }
                                       onClick={ () => {
                                           this.props.updateTabActive( index );
                                       } }
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
                        {activeTabBgColor && `#block-${clientId} li.advgb-tab.advgb-tab-active, #block-${clientId} li.advgb-tab.ui-tabs-active {
                                background-color: ${activeTabBgColor} !important;
                            }`}
                        {activeTabTextColor && `#block-${clientId} li.advgb-tab.advgb-tab-active a, #block-${clientId} li.advgb-tab.ui-tabs-active a {
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
        isPreview: {
            type: 'boolean',
            default: false,
        },
        uniqueID: {
            type: 'string',
            default: ''
        },
        isTransform: {
            type: 'boolean',
            default: false
        }
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
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            anchor: true
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
                    resetOrder() {
                        times( block.innerBlocks.length, n => {
                            updateBlockAttributes( block.innerBlocks[ n ].clientId, {
                                id: n,
                            } );
                        } );
                    },
                    updateTabActive(tabActive) {
                        updateBlockAttributes( block.clientId, {
                            tabActive: tabActive,
                        } );
                        times( block.innerBlocks.length, n => {
                            updateBlockAttributes( block.innerBlocks[ n ].clientId, {
                                tabActive: tabActive,
                            } );
                        } );
                        this.resetOrder();
                    },
                };

            }),
        )( AdvTabsWrapper ),
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
                pid
            } = attributes;
            const blockClass = [
                `advgb-tabs-wrapper`,
                `advgb-tab-${tabsStyleD}-desktop`,
                `advgb-tab-${tabsStyleT}-tablet`,
                `advgb-tab-${tabsStyleM}-mobile`,
                pid
            ].filter( Boolean ).join( ' ' );

            return (
                <div className={blockClass} data-tab-active={tabActiveFrontend}>
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
                                <a href={`#advgb-tabs-tab${index}`}
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
        deprecated: [
            {
                attributes: {
                    ...tabBlockAttrs,
                    isTransform: {
                        type: 'boolean',
                        default: false
                    }
                },
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
                        pid
                    } = attributes;
                    const blockClass = [
                        `advgb-tabs-wrapper`,
                        `advgb-tab-${tabsStyleD}-desktop`,
                        `advgb-tab-${tabsStyleT}-tablet`,
                        `advgb-tab-${tabsStyleM}-mobile`,
                        pid
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <div className={blockClass} data-tab-active={tabActiveFrontend}>
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
                                        <a href={`#advgb-tabs-tab${index}`}
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
                }
            },
            {
                attributes: {
                    ...tabBlockAttrs,
                    uniqueID: {
                        type: 'string',
                        default: ''
                    }
                },
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
            }
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components, wp.compose );