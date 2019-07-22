( function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls } = wpBlockEditor;
    const { RangeControl, PanelBody, CheckboxControl, SelectControl, Spinner, Toolbar, Placeholder, Button } = wpComponents;
    const { addQueryArgs } = wp.url;

    let fetchingQueue = null;

    const advProductsBlockIcon = (
        <svg width="20" height="20" viewBox="0 0 24 24">
            <path fill="none" d="M0,0h24v24H0V0z"/>
            <path d="M15.55,13c0.75,0,1.41-0.41,1.75-1.03l3.58-6.49C21.25,4.82,20.77,4,20.01,4H5.21L4.27,2H1v2h2l3.6,7.59l-1.35,2.44 C4.52,15.37,5.48,17,7,17h12v-2H7l1.1-2H15.55z M6.16,6h12.15l-2.76,5H8.53L6.16,6z"/>
            <path d="M7,18c-1.1,0-1.99,0.9-1.99,2c0,1.1,0.89,2,1.99,2c1.1,0,2-0.9,2-2C9,18.9,8.1,18,7,18z"/>
            <path d="M17,18c-1.1,0-1.99,0.9-1.99,2c0,1.1,0.89,2,1.99,2c1.1,0,2-0.9,2-2C19,18.9,18.1,18,17,18z"/>
        </svg>
    );

    class AdvProductsEdit extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                categoriesList: [],
                productsList: [],
                loading: true,
                error: false,
            };

            this.fetchProducts = this.fetchProducts.bind(this);
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-woo-products'];

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

            this.fetchProducts();
        }

        componentWillUpdate( nextProps, nextState ) {
            const { clientId } = this.props;
            const $ = jQuery;

            if (this.checkAttrChanged(nextProps.attributes, this.props.attributes)) {
                $(`#block-${clientId} .advgb-products-wrapper.slick-initialized`).slick('unslick');
                $(`#block-${clientId} .advgb-product`)
                    .removeAttr('tabindex')
                    .removeAttr('role')
                    .removeAttr('aria-describedby');
            }
        }

        componentDidUpdate( prevProps ) {
            const { categoriesList } = this.state;
            const { attributes } = this.props;
            const { category } = attributes;

            if (category === 'selected' && categoriesList.length === 0) {
                wp.apiFetch( { path: addQueryArgs('/wc/v2/products/categories', {per_page:-1} ) } ).then(
                    (obj) => {
                        this.setState( { categoriesList: obj } );
                    }
                );
            }

            if (this.checkAttrChanged( prevProps.attributes, attributes )) {
                this.fetchProducts();
            }
        }

        checkAttrChanged( prevAttrs, curAttrs ) {
            const {
                viewType: prevView,
                category: prevCat,
                categories: prevCats,
                status: prevStatus,
                order: prevOrder,
                orderBy: prevOrderBy,
                numberOfProducts: prevLength
            } = prevAttrs;
            const { viewType, category, categories, status, order, orderBy, numberOfProducts } = curAttrs;

            return (
                category !== prevCat
                || categories !== prevCats
                || status !== prevStatus
                || order !== prevOrder
                || orderBy !== prevOrderBy
                || numberOfProducts !== prevLength
                || prevView !== viewType
            )
        }

        fetchProducts() {
            const self = this;
            const {
                viewType,
                category,
                categories,
                status,
                order,
                orderBy,
                numberOfProducts,
            } = this.props.attributes;

            const query = addQueryArgs(
                '/agwc/v1/products',
                {
                    order: order || undefined,
                    orderby: orderBy || undefined,
                    per_page: numberOfProducts,
                    category: category === 'selected' ? categories.join(',') : undefined,
                    featured: status === 'featured' ? 1 : undefined,
                    on_sale: status === 'on_sale' ? 1 : undefined,
                }
            );

            if (fetchingQueue) {
                clearTimeout(fetchingQueue);
            }

            if (this.state.error) {
                this.setState( { error: false } );
            }

            fetchingQueue = setTimeout(function () {
                if (!self.state.loading) {
                    self.setState( { loading: true } );
                }
                wp.apiFetch( { path: query } ).then( (obj) => {
                    self.setState( {
                        productsList: obj,
                        loading: false,
                    } )
                } ).catch( (error) => {
                    self.setState( {
                        loading: false,
                        error: true,
                    } )
                } ).then( () => {
                    if (viewType === 'slider') {
                        $(`#block-${self.props.clientId} .advgb-products-block.slider-view .advgb-products-wrapper:not(.slick-initialized)`).slick( {
                            dots: true,
                            adaptiveHeight: true,
                        } );
                    }
                } )
            }, 500 );
        }

        setCategories( catID, willAdd ) {
            const { attributes, setAttributes } = this.props;
            const { categories } = attributes;

            if (willAdd) {
                setAttributes( { categories: [ ...categories, catID ] } );
            } else {
                setAttributes( { categories: categories.filter( (cat) => cat !== catID ) } )
            }

            this.fetchProducts();
        }

        render() {
            const { categoriesList, productsList, loading, error } = this.state;
            const { attributes, setAttributes } = this.props;
            const {
                viewType,
                category,
                categories,
                status,
                order,
                orderBy,
                numberOfProducts,
                columns,
            } = attributes;

            const viewControls = [
                {
                    icon: 'grid-view',
                    title: __( 'Normal View' ),
                    onClick: () => setAttributes( { viewType: 'normal' } ),
                    isActive: viewType === 'normal',
                },
                {
                    icon: 'slides',
                    title: __( 'Slider View' ),
                    onClick: () => setAttributes( { viewType: 'slider' } ),
                    isActive: viewType === 'slider',
                },
            ];

            const blockClassName = [
                "advgb-products-block",
                viewType === 'slider' && 'slider-view',
            ].filter( Boolean ).join( ' ' );

            const blockWrapperClassName = [
                "advgb-products-wrapper",
                viewType === 'normal' && `columns-${columns}`,
            ].filter( Boolean ).join( ' ' );

            return (
                <Fragment>
                    <BlockControls>
                        <Toolbar controls={ viewControls } />
                    </BlockControls>
                    <InspectorControls>
                        <PanelBody title={ __( 'Products Settings' ) }>
                            <SelectControl
                                label={ __( 'Product Status' ) }
                                value={ status }
                                options={ [
                                    { label: __( 'All' ), value: '' },
                                    { label: __( 'Featured' ), value: 'featured' },
                                    { label: __( 'On Sale' ), value: 'on_sale' },
                                ] }
                                onChange={ ( value ) => setAttributes( { status: value } ) }
                            />
                            <SelectControl
                                label={ __( 'Category' ) }
                                value={ category }
                                options={ [
                                    { label: __( 'All' ), value: '' },
                                    { label: __( 'Selected' ), value: 'selected' },
                                ] }
                                onChange={ ( value ) => setAttributes( { category: value } ) }
                            />
                            {category === 'selected' &&
                                <div className="advgb-woo-categories-list">
                                    {categoriesList.map( (cat, index) => (
                                        <CheckboxControl
                                            key={ index }
                                            label={ [
                                                cat.name,
                                                <span key="cat-count" style={ { fontSize: 'small', color: '#999', marginLeft: 5 } }>
                                                    ({cat.count})
                                                </span>
                                            ] }
                                            checked={ jQuery.inArray(cat.id, categories) > -1 }
                                            onChange={ (checked) => this.setCategories( cat.id, checked ) }
                                        />
                                    ) ) }
                                </div>
                            }
                        </PanelBody>
                        <PanelBody title={ __( 'Layout Settings' ) }>
                            {viewType !== 'slider' &&
                                <RangeControl
                                    label={ __( 'Columns' ) }
                                    value={ columns }
                                    min={ 1 }
                                    max={ 4 }
                                    onChange={ ( value ) => setAttributes( { columns: value } ) }
                                />
                            }
                            <RangeControl
                                label={ __( 'Number of Products' ) }
                                value={ numberOfProducts }
                                min={ 1 }
                                max={ 48 }
                                onChange={ (value) => setAttributes( { numberOfProducts: value } ) }
                            />
                            <SelectControl
                                label={ __( 'Order' ) }
                                value={ `${orderBy}-${order}` }
                                options={ [
                                    { label: __( 'Newest to oldest' ), value: 'date-desc' },
                                    { label: __( 'Price: high to low' ), value: 'price-desc' },
                                    { label: __( 'Price: low to high' ), value: 'price-asc' },
                                    { label: __( 'Highest Rating first' ), value: 'rating-desc' },
                                    { label: __( 'Most sale first' ), value: 'popularity-desc' },
                                    { label: __( 'Title: Alphabetical' ), value: 'title-asc' },
                                    { label: __( 'Title: Alphabetical reversed' ), value: 'title-desc' },
                                ] }
                                onChange={ (value) => {
                                    const splitedVal = value.split('-');
                                    return setAttributes( {
                                        orderBy: splitedVal[0],
                                        order: splitedVal[1],
                                    } )
                                } }
                            />
                        </PanelBody>
                    </InspectorControls>
                    <div className={ blockClassName }>
                        {!error ? !loading ? productsList.length > 0 ?
                            <div className={ blockWrapperClassName }>
                                {productsList.map( (product, idx) => (
                                    <div key={idx} className="advgb-product">
                                        <div className="advgb-product-img">
                                            <img src={product.images.length ? product.images[0].src : undefined} alt={product.name} />
                                        </div>
                                        <div className="advgb-product-title">{ product.name }</div>
                                        <div className="advgb-product-price" dangerouslySetInnerHTML={ { __html: product.price_html } } />
                                        <div className="advgb-product-add-to-cart">
                                            <span>{ __( 'Add to cart' ) }</span>
                                        </div>
                                    </div>
                                ) ) }
                            </div>
                            : ( // When no products found
                                <div>{ __( 'No products found.' ) }</div>
                            )
                            : ( // When products is fetching
                                <div>
                                    <span>{ __( 'Loading' ) }</span>
                                    <Spinner/>
                                </div>
                            )
                            : ( // When error
                                <Placeholder
                                    icon={ advProductsBlockIcon }
                                    label={ __( 'ADVGB Woo Products Block' ) }
                                >
                                    <div style={ { marginBottom: 10 } }>
                                        { __( 'WooCommerce has not been detected, make sure WooCommerce is installed and activated.' ) }
                                    </div>
                                    <Button
                                        className="button button-large"
                                        onClick={ () => this.fetchProducts() }
                                    >
                                        { __( 'Try again' ) }
                                    </Button>
                                </Placeholder>
                            )
                        }
                    </div>
                </Fragment>
            )
        }
    }

    registerBlockType( 'advgb/woo-products', {
        title: __( 'Woo Products' ),
        description: __( 'Listing your products in a easy way.' ),
        icon: {
            src: advProductsBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'woo commerce' ), __( 'products list' ), __( 'price list' ) ],
        attributes: {
            viewType: {
                type: 'string',
                default: 'normal',
            },
            category: {
                type: 'string',
            },
            categories: {
                type: 'array',
                default: [],
            },
            status: {
                type: 'string',
            },
            order: {
                type: 'string',
                default: 'desc',
            },
            orderBy: {
                type: 'string',
                default: 'date',
            },
            numberOfProducts: {
                type: 'number',
                default: 6,
            },
            columns: {
                type: 'number',
                default: 3,
            },
            changed: {
                type: 'boolean',
                default: false,
            }
        },
        edit: AdvProductsEdit,
        save: function ( { attributes } ) {
            const {
                viewType,
                category,
                categories,
                status,
                order,
                orderBy,
                numberOfProducts,
                columns,
            } = attributes;

            const listCats = categories.join(',');
            const shortCode = [
                '[products',
                `limit="${numberOfProducts}"`,
                `columns="${columns}"`,
                `orderby="${orderBy}"`,
                `order="${order}"`,
                category === 'selected' && `category="${listCats}"`,
                status === 'featured' && 'featured="1"',
                status === 'on_sale' && 'on_sale="1"',
                ']',
            ].filter( Boolean ).join( ' ' );

            const blockClassName = [
                'advgb-woo-products',
                viewType === 'slider' && 'slider-view',
            ].filter( Boolean ).join( ' ' );

            return (
                <div className={ blockClassName }>
                    {shortCode}
                </div>
            );
        }
    } )
} )( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );