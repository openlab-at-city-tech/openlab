(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents, wpData, lodash, wpHtmlEntities, wpDate ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls } = wpBlockEditor;
    const { PanelBody, RangeControl, ToggleControl, TextControl, QueryControls, Spinner, Toolbar, Placeholder, IconButton } = wpComponents;
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
            } = attributes;

            const inspectorControls = (
                <InspectorControls>
                    <PanelBody title={ __( 'Block Settings' ) }>
                        <QueryControls
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
                            label={ __( 'Columns' ) }
                            value={ columns }
                            min={ 1 }
                            max={ 4 }
                            onChange={ (value) => setAttributes( { columns: value } ) }
                        />
                        }
                        <ToggleControl
                            label={ __( 'Display Featured Image' ) }
                            checked={ displayFeaturedImage }
                            onChange={ () => setAttributes( { displayFeaturedImage: !displayFeaturedImage } ) }
                        />
                        <ToggleControl
                            label={ __( 'Display Post Author' ) }
                            checked={ displayAuthor }
                            onChange={ () => setAttributes( { displayAuthor: !displayAuthor } ) }
                        />
                        <ToggleControl
                            label={ __( 'Display Post Date' ) }
                            checked={ displayDate }
                            onChange={ () => setAttributes( { displayDate: !displayDate } ) }
                        />
                        <ToggleControl
                            label={ __( 'Display Read More Link' ) }
                            checked={ displayReadMore }
                            onChange={ () => setAttributes( { displayReadMore: !displayReadMore } ) }
                        />
                        {displayReadMore &&
                            <TextControl
                                label={ __('Read more text') }
                                value={ readMoreLbl }
                                onChange={ (value) => setAttributes( { readMoreLbl: value } ) }
                            />
                        }
                        <ToggleControl
                            label={ __( 'Display Post Excerpt' ) }
                            checked={ displayExcerpt }
                            onChange={ () => setAttributes( { displayExcerpt: !displayExcerpt } ) }
                        />
                        {displayExcerpt &&
                            <ToggleControl
                                label={ __( 'First Post text as Excerpt' ) }
                                help={ __( 'Display some part of first text found in post as excerpt.' ) }
                                checked={ postTextAsExcerpt }
                                onChange={ () => setAttributes( { postTextAsExcerpt: !postTextAsExcerpt } ) }
                            />
                        }
                        {displayExcerpt && postTextAsExcerpt &&
                            <RangeControl
                                label={ __( 'Post Text Excerpt length' ) }
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
                    <Fragment>
                        { inspectorControls }
                        <Placeholder
                            icon={ advRecentPostsBlockIcon }
                            label={ __( 'ADVGB Recent Posts Block' ) }
                        >
                            { ! Array.isArray( recentPosts ) ?
                                <Spinner /> :
                                __( 'No posts found! Try to change category or publish posts.' )
                            }
                        </Placeholder>
                    </Fragment>
                )
            }

            const postViewControls = [
                {
                    icon: 'grid-view',
                    title: __( 'Grid View' ),
                    onClick: () => setAttributes( { postView: 'grid' } ),
                    isActive: postView === 'grid',
                },
                {
                    icon: 'list-view',
                    title: __( 'List View' ),
                    onClick: () => setAttributes( { postView: 'list' } ),
                    isActive: postView === 'list',
                },
                {
                    icon: 'slides',
                    title: __( 'Slider View' ),
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
                <Fragment>
                    { inspectorControls }
                    <BlockControls>
                        <Toolbar controls={ postViewControls } />
                        <Toolbar>
                            <IconButton
                                label={ __( 'Refresh' ) }
                                icon="update"
                                onClick={ () => setAttributes( { myToken: Math.floor(Math.random() * Math.floor(999)) } ) }
                            />
                        </Toolbar>
                    </BlockControls>
                    <div className={ blockClassName }>
                        {this.state.updating && <div className="advgb-recent-posts-loading" />}
                        <div className="advgb-recent-posts">
                            {recentPosts.map( ( post, index ) => (
                                <article key={ index } className="advgb-recent-post" >
                                    {displayFeaturedImage && (
                                        <div className="advgb-post-thumbnail">
                                            <a href={ post.link } target="_blank">
                                                <img src={ post.featured_img ? post.featured_img : advgbBlocks.post_thumb } alt={ __( 'Post Image' ) } />
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
                                                    <a href={ post.link } target="_blank">{ readMoreLbl ? readMoreLbl : __( 'Read More' ) }</a>
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
        title: __( 'Recent Posts' ),
        description: __( 'Display your recent posts in slider or grid view with beautiful styles.' ),
        icon: {
            src: advRecentPostsBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'latest posts' ), __( 'posts slide' ), __( 'posts grid' ) ],
        supports: {
            html: false,
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