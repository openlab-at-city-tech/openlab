import AdvQueryControls from './query-controls.jsx';

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents, wpData, lodash, wpHtmlEntities, wpDate ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls } = wpBlockEditor;
    const { PanelBody, RangeControl, ToggleControl, TextControl, QueryControls, Spinner, ToolbarGroup, ToolbarButton, Placeholder } = wpComponents;
    const { withSelect } = wpData;
    const { pickBy, isUndefined } = lodash;
    const { decodeEntities } = wpHtmlEntities;
    const { dateI18n, __experimentalGetSettings } = wpDate;

    const advRecentPostsBlockIcon = (
        <svg width="20" height="20" viewBox="2 2 22 22">
            <path fill="none" d="M0,0h24v24H0V0z"/>
            <rect x="13" y="7.5" width="5" height="2"/>
            <rect x="13" y="14.5" width="5" height="2"/>
            <path d="M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3z M19,19H5V5h14V19z"/>
            <path d="M11,6H6v5h5V6z M10,10H7V7h3V10z"/>
            <path d="M11,13H6v5h5V13z M10,17H7v-3h3V17z"/>
        </svg>
    );

    const previewImageData = 'data:image/png;base64,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';

    let initSlider = null;

    class RecentPostsEdit extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                categoriesList: [],
                updating: false,
            }
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-recent-posts'];

            const categoriesListQuery = {
                per_page: -1,
                hide_empty: true,
            };

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

            wp.apiFetch( {
                path: wp.url.addQueryArgs( 'wp/v2/categories', categoriesListQuery ),
            } ).then( ( categoriesList ) => this.setState( { categoriesList: categoriesList } ) )
        }

        componentWillUpdate( nextProps ) {
            const { recentPosts: nextPosts } = nextProps;
            const { postView: nextView } = nextProps.attributes;
            const { attributes, clientId, recentPosts } = this.props;
            const $ = jQuery;

            if (nextView !== 'slider' || (nextPosts && recentPosts && nextPosts.length !== recentPosts.length) ) {
                $(`#block-${clientId} .advgb-recent-posts.slick-initialized`).slick('unslick');
                $(`#block-${clientId} .advgb-recent-post`)
                    .removeAttr('tabindex')
                    .removeAttr('role')
                    .removeAttr('aria-describedby');

                if (nextView === 'slider' && (nextPosts && recentPosts && nextPosts.length !== recentPosts.length)) {
                    if (!this.state.updating) {
                        this.setState( { updating: true } );
                    }
                }

                if (initSlider) {
                    clearTimeout(initSlider);
                }
            }
        }

        componentDidUpdate( prevProps ) {
            const that = this;
            const { attributes, clientId } = this.props;
            const { postView } = attributes;
            const $ = jQuery;

            if (postView === 'slider') {
                initSlider = setTimeout(function () {
                    $(`#block-${clientId} .advgb-recent-posts-block.slider-view .advgb-recent-posts:not(.slick-initialized)`).slick( {
                        dots: true,
                        adaptiveHeight: true,
                    } );

                    if (that.state.updating) {
                        that.setState( { updating: false } );
                    }
                }, 100 );
            } else {
                $(`#block-${clientId} .advgb-recent-posts.slick-initialized`).slick('unslick');
            }
        }

        static extractContent(html, length) {
            const span= document.createElement('span');
            span.innerHTML= html;

            // Remove script tag
            const scripts = span.getElementsByTagName('script');
            let j = scripts.length;
            while (j--) {
                scripts[j].parentNode.removeChild(scripts[j]);
            }

            // Remove style tag
            const styles = span.getElementsByTagName('style');
            let k = styles.length;
            while (k--) {
                styles[k].parentNode.removeChild(styles[k]);
            }

            const children= span.querySelectorAll('*');
            for(let i = 0 ; i < children.length ; i++) {
                if(children[i].textContent)
                    children[i].textContent += ' ';
                else
                    children[i].innerText += ' ';
            }

            let text = [span.textContent || span.innerText].toString().replace(/\s\s+/g,' ');
            text = text.slice(0, length).trim();

            if (text.length) text += '…' ;

            return text;
        };

        render() {
            const { categoriesList } = this.state;
            const { attributes, setAttributes, recentPosts } = this.props;
            const {
                postView,
                order,
                orderBy,
                category,
                numberOfPosts,
                columns,
                displayFeaturedImage,
                displayAuthor,
                displayDate,
                displayExcerpt,
                postTextAsExcerpt,
                postTextExcerptLength,
                displayReadMore,
                readMoreLbl,
                isPreview,
            } = attributes;

            const inspectorControls = (
                <InspectorControls>
                    <PanelBody title={ __( 'Block Settings', 'advanced-gutenberg' ) }>
                        <AdvQueryControls
                            { ...{ order, orderBy } }
                            categoriesList={ categoriesList }
                            selectedCategoryId={ category }
                            numberOfItems={ numberOfPosts }
                            onOrderChange={ ( value ) => setAttributes( { order: value } ) }
                            onOrderByChange={ ( value ) => setAttributes( { orderBy: value } ) }
                            onCategoryChange={ ( value ) => setAttributes( { category: value !== '' ? value : undefined } ) }
                            onNumberOfItemsChange={ (value) => setAttributes( { numberOfPosts: value } ) }
                        />
                        {postView === 'grid' &&
                        <RangeControl
                            label={ __( 'Columns', 'advanced-gutenberg' ) }
                            value={ columns }
                            min={ 1 }
                            max={ 4 }
                            onChange={ (value) => setAttributes( { columns: value } ) }
                        />
                        }
                        <ToggleControl
                            label={ __( 'Display Featured Image', 'advanced-gutenberg' ) }
                            checked={ displayFeaturedImage }
                            onChange={ () => setAttributes( { displayFeaturedImage: !displayFeaturedImage } ) }
                        />
                        <ToggleControl
                            label={ __( 'Display Post Author', 'advanced-gutenberg' ) }
                            checked={ displayAuthor }
                            onChange={ () => setAttributes( { displayAuthor: !displayAuthor } ) }
                        />
                        <ToggleControl
                            label={ __( 'Display Post Date', 'advanced-gutenberg' ) }
                            checked={ displayDate }
                            onChange={ () => setAttributes( { displayDate: !displayDate } ) }
                        />
                        <ToggleControl
                            label={ __( 'Display Read More Link', 'advanced-gutenberg' ) }
                            checked={ displayReadMore }
                            onChange={ () => setAttributes( { displayReadMore: !displayReadMore } ) }
                        />
                        {displayReadMore &&
                        <TextControl
                            label={ __('Read more text', 'advanced-gutenberg') }
                            value={ readMoreLbl }
                            onChange={ (value) => setAttributes( { readMoreLbl: value } ) }
                        />
                        }
                        <ToggleControl
                            label={ __( 'Display Post Excerpt', 'advanced-gutenberg' ) }
                            checked={ displayExcerpt }
                            onChange={ () => setAttributes( { displayExcerpt: !displayExcerpt } ) }
                        />
                        {displayExcerpt &&
                        <ToggleControl
                            label={ __( 'First Post text as Excerpt', 'advanced-gutenberg' ) }
                            help={ __( 'Display some part of first text found in post as excerpt.', 'advanced-gutenberg' ) }
                            checked={ postTextAsExcerpt }
                            onChange={ () => setAttributes( { postTextAsExcerpt: !postTextAsExcerpt } ) }
                        />
                        }
                        {displayExcerpt && postTextAsExcerpt &&
                        <RangeControl
                            label={ __( 'Post Text Excerpt length', 'advanced-gutenberg' ) }
                            min={ 50 }
                            max={ 300 }
                            value={ postTextExcerptLength }
                            onChange={ ( value ) => setAttributes( { postTextExcerptLength: value } ) }
                        />
                        }
                    </PanelBody>
                </InspectorControls>
            );

            const hasPosts = Array.isArray( recentPosts ) && recentPosts.length;

            // If no posts found we show this notice
            if (!hasPosts) {
                return (
                    isPreview ?
                        <img alt={__('Recent Posts', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                        :
                    <Fragment>
                        { inspectorControls }
                        <Placeholder
                            icon={ advRecentPostsBlockIcon }
                            label={ __( 'ADVGB Recent Posts Block', 'advanced-gutenberg' ) }
                        >
                            { ! Array.isArray( recentPosts ) ?
                                <Spinner /> :
                                __( 'No posts found! Try to change category or publish posts.', 'advanced-gutenberg' )
                            }
                        </Placeholder>
                    </Fragment>
                )
            }

            const postViewControls = [
                {
                    icon: 'grid-view',
                    title: __( 'Grid View', 'advanced-gutenberg' ),
                    onClick: () => setAttributes( { postView: 'grid' } ),
                    isActive: postView === 'grid',
                },
                {
                    icon: 'list-view',
                    title: __( 'List View', 'advanced-gutenberg' ),
                    onClick: () => setAttributes( { postView: 'list' } ),
                    isActive: postView === 'list',
                },
                {
                    icon: 'slides',
                    title: __( 'Slider View', 'advanced-gutenberg' ),
                    onClick: () => setAttributes( { postView: 'slider' } ),
                    isActive: postView === 'slider',
                },
            ];

            const blockClassName = [
                'advgb-recent-posts-block',
                this.state.updating && 'loading',
                postView === 'grid' && 'columns-' + columns,
                postView === 'grid' && 'grid-view',
                postView === 'list' && 'list-view',
                postView === 'slider' && 'slider-view',
            ].filter( Boolean ).join( ' ' );

            const dateFormat = __experimentalGetSettings().formats.date;

            return (
                isPreview ?
                    <img alt={__('Recent Posts', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    { inspectorControls }
                    <BlockControls>
                        <ToolbarGroup controls={ postViewControls } />
                        <ToolbarGroup>
                            <ToolbarButton
                                icon="update"
                                label={ __( 'Refresh', 'advanced-gutenberg' ) }
                                onClick={ () => setAttributes( { myToken: Math.floor(Math.random() * Math.floor(999)) } ) }
                            />
                        </ToolbarGroup>
                    </BlockControls>
                    <div className={ blockClassName }>
                        {this.state.updating && <div className="advgb-recent-posts-loading" />}
                        <div className="advgb-recent-posts">
                            {recentPosts.map( ( post, index ) => (
                                <article key={ index } className="advgb-recent-post" >
                                    {displayFeaturedImage && (
                                        <div className="advgb-post-thumbnail">
                                            <a href={ post.link } target="_blank">
                                                <img src={ post.featured_img ? post.featured_img : advgbBlocks.post_thumb } alt={ __( 'Post Image', 'advanced-gutenberg' ) } />
                                            </a>
                                        </div>
                                    ) }
                                    <div className="advgb-post-wrapper">
                                        <h2 className="advgb-post-title">
                                            <a href={ post.link } target="_blank">{ decodeEntities( post.title.rendered ) }</a>
                                        </h2>
                                        <div className="advgb-post-info">
                                            {displayAuthor && (
                                                <a href={ post.author_meta.author_link }
                                                   target="_blank"
                                                   className="advgb-post-author"
                                                >
                                                    { post.author_meta.display_name }
                                                </a>
                                            ) }
                                            {displayDate && (
                                                <span className="advgb-post-date" >
                                                { dateI18n( dateFormat, post.date_gmt ) }
                                            </span>
                                            ) }
                                        </div>
                                        <div className="advgb-post-content">
                                            {displayExcerpt && (
                                                <div className="advgb-post-excerpt"
                                                     dangerouslySetInnerHTML={ {
                                                         __html: postTextAsExcerpt ? RecentPostsEdit.extractContent(post.content.rendered, postTextExcerptLength) : post.excerpt.raw
                                                     } } />
                                            ) }
                                            {displayReadMore && (
                                                <div className="advgb-post-readmore">
                                                    <a href={ post.link } target="_blank">{ readMoreLbl ? readMoreLbl : __( 'Read More', 'advanced-gutenberg' ) }</a>
                                                </div>
                                            ) }
                                        </div>
                                    </div>
                                </article>
                            ) ) }
                        </div>
                    </div>
                </Fragment>
            )
        }
    }

    registerBlockType( 'advgb/recent-posts', {
        title: __( 'Recent Posts', 'advanced-gutenberg' ),
        description: __( 'Display your recent posts in slider or grid view with beautiful styles.', 'advanced-gutenberg' ),
        icon: {
            src: advRecentPostsBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'latest posts', 'advanced-gutenberg' ), __( 'posts slide', 'advanced-gutenberg' ), __( 'posts grid', 'advanced-gutenberg' ) ],
        supports: {
            html: false,
        },
        example: {
            attributes: {
                isPreview: true
            },
        },
        edit: withSelect( ( select, props ) => {
            const { getEntityRecords } = select( 'core' );
            const { category, order, orderBy, numberOfPosts, myToken } = props.attributes;

            const recentPostsQuery = pickBy( {
                categories: category,
                order,
                orderby: orderBy,
                per_page: numberOfPosts,
                token: myToken,
            }, ( value ) => !isUndefined( value ) );

            return {
                recentPosts: getEntityRecords( 'postType', 'post', recentPostsQuery ),
            }
        } )( RecentPostsEdit ),
        save: function () { // Render in PHP
            return null;
        },
    } )
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components, wp.data, lodash, wp.htmlEntities, wp.date );
