( function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls } = wpBlockEditor;
    const { RangeControl, PanelBody, CheckboxControl, SelectControl, Spinner, ToolbarGroup, Placeholder, Button } = wpComponents;
    const { addQueryArgs } = wp.url;

    let fetchingQueue = null;

    const previewImageData = 'data:image/png;base64,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';

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
                        jQuery(`#block-${self.props.clientId} .advgb-products-block.slider-view .advgb-products-wrapper:not(.slick-initialized)`).slick( {
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
                isPreview,
            } = attributes;

            const viewControls = [
                {
                    icon: 'grid-view',
                    title: __( 'Normal View', 'advanced-gutenberg' ),
                    onClick: () => setAttributes( { viewType: 'normal' } ),
                    isActive: viewType === 'normal',
                },
                {
                    icon: 'slides',
                    title: __( 'Slider View', 'advanced-gutenberg' ),
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
                isPreview ?
                    <img alt={__('Woo Products', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <BlockControls>
                        <ToolbarGroup controls={ viewControls } />
                    </BlockControls>
                    <InspectorControls>
                        <PanelBody title={ __( 'Products Settings', 'advanced-gutenberg' ) }>
                            <SelectControl
                                label={ __( 'Product Status', 'advanced-gutenberg' ) }
                                value={ status }
                                options={ [
                                    { label: __( 'All', 'advanced-gutenberg' ), value: '' },
                                    { label: __( 'Featured', 'advanced-gutenberg' ), value: 'featured' },
                                    { label: __( 'On Sale', 'advanced-gutenberg' ), value: 'on_sale' },
                                ] }
                                onChange={ ( value ) => setAttributes( { status: value } ) }
                            />
                            <SelectControl
                                label={ __( 'Category', 'advanced-gutenberg' ) }
                                value={ category }
                                options={ [
                                    { label: __( 'All', 'advanced-gutenberg' ), value: '' },
                                    { label: __( 'Selected', 'advanced-gutenberg' ), value: 'selected' },
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
                        <PanelBody title={ __( 'Layout Settings', 'advanced-gutenberg' ) }>
                            {viewType !== 'slider' &&
                                <RangeControl
                                    label={ __( 'Columns', 'advanced-gutenberg' ) }
                                    value={ columns }
                                    min={ 1 }
                                    max={ 4 }
                                    onChange={ ( value ) => setAttributes( { columns: value } ) }
                                />
                            }
                            <RangeControl
                                label={ __( 'Number of Products', 'advanced-gutenberg' ) }
                                value={ numberOfProducts }
                                min={ 1 }
                                max={ 48 }
                                onChange={ (value) => setAttributes( { numberOfProducts: value } ) }
                            />
                            <SelectControl
                                label={ __( 'Order', 'advanced-gutenberg' ) }
                                value={ `${orderBy}-${order}` }
                                options={ [
                                    { label: __( 'Newest to oldest', 'advanced-gutenberg' ), value: 'date-desc' },
                                    { label: __( 'Price: high to low', 'advanced-gutenberg' ), value: 'price-desc' },
                                    { label: __( 'Price: low to high', 'advanced-gutenberg' ), value: 'price-asc' },
                                    { label: __( 'Highest Rating first', 'advanced-gutenberg' ), value: 'rating-desc' },
                                    { label: __( 'Most sale first', 'advanced-gutenberg' ), value: 'popularity-desc' },
                                    { label: __( 'Title: Alphabetical', 'advanced-gutenberg' ), value: 'title-asc' },
                                    { label: __( 'Title: Alphabetical reversed', 'advanced-gutenberg' ), value: 'title-desc' },
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
                                            <span>{ __( 'Add to cart', 'advanced-gutenberg' ) }</span>
                                        </div>
                                    </div>
                                ) ) }
                            </div>
                            : ( // When no products found
                                <div>{ __( 'No products found.', 'advanced-gutenberg' ) }</div>
                            )
                            : ( // When products is fetching
                                <div>
                                    <span>{ __( 'Loading', 'advanced-gutenberg' ) }</span>
                                    <Spinner/>
                                </div>
                            )
                            : ( // When error
                                <Placeholder
                                    icon={ advProductsBlockIcon }
                                    label={ __( 'ADVGB Woo Products Block', 'advanced-gutenberg' ) }
                                >
                                    <div style={ { marginBottom: 10 } }>
                                        { __( 'WooCommerce has not been detected, make sure WooCommerce is installed and activated.', 'advanced-gutenberg' ) }
                                    </div>
                                    <Button
                                        className="button button-large"
                                        onClick={ () => this.fetchProducts() }
                                    >
                                        { __( 'Try again', 'advanced-gutenberg' ) }
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
        title: __( 'Woo Products', 'advanced-gutenberg' ),
        description: __( 'Listing your products in a easy way.', 'advanced-gutenberg' ),
        icon: {
            src: advProductsBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'woo commerce', 'advanced-gutenberg' ), __( 'products list', 'advanced-gutenberg' ), __( 'price list', 'advanced-gutenberg' ) ],
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
            },
            isPreview: {
                type: 'boolean',
                default: false,
            },
        },
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            anchor: true
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