(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents, wpData, wpHooks ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, getBlockContent, createBlock } = wpBlocks;
    const { BlockControls, InspectorControls, InspectorAdvancedControls, PanelColorSettings, BlockAlignmentToolbar } = wpBlockEditor;
    const { IconButton, Placeholder, Button, Toolbar, ToggleControl, TextControl, PanelBody } = wpComponents;
    const { select, dispatch } = wpData;
    const { addFilter } = wpHooks;

    const summaryBlockIcon = (
        <svg height="20" viewBox="2 2 22 22" width="20" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    );
    const summaryBlockTitle = __( 'Summary', 'advanced-gutenberg' );

    // Add button to insert summary inside table of contents component
    ( function () {
        jQuery( window ).on( 'load', function () {
            if (typeof dispatch( 'core/editor' ) === 'undefined') {
                return false;
            }

            const $ = jQuery;
            const { insertBlock } = dispatch( 'core/editor' );
            const summaryBlock = createBlock( 'advgb/summary' );

            $( '#editor' ).find( '.table-of-contents' ).click( function () {
                const allBlocks = select( 'core/editor' ).getBlocks();
                const summaryBlockExist = !!allBlocks.filter( ( block ) => ( block.name === 'advgb/summary' ) ).length;
                setTimeout( function () {
                    const summaryButton = $(
                        '<button class="button" style="position: absolute; bottom: 10px; right: 15px">'
                        + __( 'Insert Summary', 'advanced-gutenberg' ) +
                        '</button>'
                    );

                    $( '#editor' ).find( '.table-of-contents__popover' ).find( '.document-outline' )
                        .append( summaryButton );
                    summaryButton.unbind( 'click' ).click( function () {
                        insertBlock( summaryBlock, 0 );
                        $('.table-of-contents__popover').hide();
                    } );

                    if (summaryBlockExist) {
                        summaryButton.prop( 'disabled', true );
                    }
                }, 100 )
            } )
        } );
    } )();

    // Add notice for user to refresh summary if manually change heading anchor
    addFilter( 'editor.BlockEdit', 'advgb/addHeadingNotice', function ( BlockEdit ) {
        return ( props ) => {
            const { isSelected, name: blockType, attributes } = props;

            return ( [
                <BlockEdit key="block-edit-summary" {...props} />,
                isSelected && blockType === 'core/heading' && attributes.nodeName !== 'H1' &&
                <InspectorAdvancedControls key="advgb-summary-controls-hint">
                    <p style={{ color: 'red', fontStyle: 'italic' }}>
                        {__( 'After manually changing the anchor, remember to refresh summary block to make the links work!', 'advanced-gutenberg' )}
                    </p>
                </InspectorAdvancedControls>,
            ] )
        }
    } );

    class SummaryBlock extends Component {
        constructor() {
            super( ...arguments );
            this.updateSummary = this.updateSummary.bind( this );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-summary'];

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
            this.updateSummary();
        };


        /**
         * Function to get heading blocks from columns blocks
         *
         * @param block     array Columns block to get data
         * @param storeData array Data array to store heading blocks
         *
         * @returns array   array Heading blocks from block given
         */
        static getHeadingBlocksFromColumns( block, storeData )
        {
            if ( block.name === 'core/columns' || block.name === 'core/column' ) {
                block.innerBlocks.map(function ( bl ) {
                    SummaryBlock.getHeadingBlocksFromColumns( bl, storeData );
                    return bl;
                } )
            } else if ( block.name === 'core/heading' ) {
                storeData.push( block );
            }

            return storeData;
        }

        updateSummary() {
            let headingDatas = [];
            let headingBlocks = [];
            const allBlocks = select( 'core/editor' ).getBlocks();
            const filteredBlocks = allBlocks.filter( ( block ) => ( block.name === 'core/heading' || block.name === 'core/columns' ) );
            filteredBlocks.map(function ( block ) {
                if (block.name === 'core/columns') {
                    SummaryBlock.getHeadingBlocksFromColumns( block, headingBlocks );
                } else {
                    headingBlocks.push( block );
                }

                return block;
            });

            headingBlocks.map( ( heading ) => {
                let thisHead = {};
                thisHead[ 'level' ] = parseInt( heading.attributes.level );

                // We only get heading from h2
                if (thisHead[ 'level' ] > 1) {
                    thisHead[ 'level' ] -= 1;
                    thisHead[ 'content' ] = heading.attributes.content.length
                        ? getBlockContent( heading ).replace( /<(?:.|\n)*?>/gm, '' )
                        : '';
                    thisHead[ 'clientId' ] = heading.clientId;
                    if (heading.attributes.anchor) {
                        thisHead[ 'anchor' ] = heading.attributes.anchor;
                    } else {
                        // Generate a random anchor for headings without it
                        thisHead[ 'anchor' ] = 'advgb-toc-' + heading.clientId;
                        heading.attributes.anchor = thisHead[ 'anchor' ];
                    }

                    headingDatas.push( thisHead );
                }

                return heading;
            } );

            this.props.setAttributes( {
                headings: headingDatas
            } );
        }

        render() {
            const { attributes, isSelected, setAttributes } = this.props;
            const { headings, loadMinimized, anchorColor, align, headerTitle } = attributes;

            // No heading blocks
            let summaryContent = (
                <Placeholder
                    icon={summaryBlockIcon}
                    label={summaryBlockTitle}
                    instructions={__( 'Your current post/page has no headings. Try add some headings and update this block later', 'advanced-gutenberg' )}
                >
                    <Button onClick={this.updateSummary}
                            className={'button'}
                    >
                        {__( 'Update', 'advanced-gutenberg' )}
                    </Button>
                </Placeholder>
            );

            // Having heading blocks
            if (headings.length > 0) {
                const { selectBlock } = dispatch( 'core/editor' );
                summaryContent = (
                    <ul className="advgb-toc">
                        {headings.map( ( heading ) => {
                            return (
                                <li className={'toc-level-' + heading.level}
                                    style={{ marginLeft: heading.level * 20 }}
                                    key={heading.anchor}
                                >
                                    <a href={'#' + heading.anchor}
                                       onClick={() => selectBlock( heading.clientId )}
                                       style={ { color: anchorColor } }
                                    >
                                        {heading.content}
                                    </a>
                                </li>
                            )
                        } )}
                    </ul>
                )
            }

            return (
                <Fragment>
                    {!!headings.length && (
                        <BlockControls>
                            <BlockAlignmentToolbar value={ align } onChange={ ( align ) => setAttributes( { align: align } ) } />
                            <Toolbar>
                                <IconButton className={'components-icon-button components-toolbar__control'}
                                            icon={'update'}
                                            label={__( 'Update Summary', 'advanced-gutenberg' )}
                                            onClick={this.updateSummary}
                                />
                            </Toolbar>
                        </BlockControls>
                    ) }
                    <InspectorControls>
                        <PanelBody title={ __( 'Summary settings', 'advanced-gutenberg' ) } >
                            <ToggleControl
                                label={ __( 'Load minimized', 'advanced-gutenberg' ) }
                                checked={ !!loadMinimized }
                                onChange={ () => setAttributes( { loadMinimized: !loadMinimized, postTitle: select('core/editor').getEditedPostAttribute('title') } ) }
                            />
                            {loadMinimized &&
                            <TextControl
                                label={ __( 'Summary header title', 'advanced-gutenberg' ) }
                                value={ headerTitle || '' }
                                placeholder={ __( 'Enter header…', 'advanced-gutenberg' ) }
                                onChange={ (value) => setAttributes( { headerTitle: value } ) }
                            />
                            }
                            <PanelColorSettings
                                title={ __( 'Anchor Color', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Anchor Color', 'advanced-gutenberg' ),
                                        value: anchorColor,
                                        onChange: ( value ) => setAttributes( { anchorColor: value } ),
                                    },
                                ] }
                            />
                        </PanelBody>
                    </InspectorControls>
                    {summaryContent}
                </Fragment>
            )
        }
    }

    const blockAttrs = {
        headings: {
            type: 'array',
            default: [],
        },
        loadMinimized: {
            type: 'boolean',
            default: false,
        },
        anchorColor: {
            type: 'string',
        },
        align: {
            type: 'string',
            default: 'none',
        },
        postTitle: {
            type: 'string',
        },
        headerTitle: {
            type: 'string',
        },
        changed: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType( 'advgb/summary', {
        title: summaryBlockTitle,
        description: __( 'Show the table of content of current post/page.', 'advanced-gutenberg' ),
        icon: {
            src: summaryBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'summary', 'advanced-gutenberg' ), __( 'table of content', 'advanced-gutenberg' ), __( 'list', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        supports: {
            multiple: false,
        },
        edit: SummaryBlock,
        save: ( { attributes } ) => {
            const { headings, loadMinimized, anchorColor, align = 'none', postTitle, headerTitle } = attributes;
            // No heading blocks
            if (headings.length < 1) {
                return null;
            }

            let blockStyle = undefined;
            if (loadMinimized) blockStyle = { display: 'none' };

            const summary = (
                <ul className={`advgb-toc align${align}`} style={ blockStyle }>
                    {headings.map( ( heading, index ) => {
                        return (
                            <li className={'toc-level-' + heading.level}
                                key={`summary-save-${index}`}
                                style={{ marginLeft: heading.level * 20 }}
                            >
                                <a href={'#' + heading.anchor}
                                   style={ { color: anchorColor } }
                                >
                                    {heading.content}
                                </a>
                            </li>
                        )
                    } ) }
                </ul>
            );

            if ( loadMinimized ) {
                return (
                    <div className={`align${align}`}>
                        <div className={'advgb-toc-header collapsed'}>{ headerTitle || postTitle }</div>
                        {summary}
                    </div>
                );
            }

            return summary;
        },
        getEditWrapperProps( attributes ) {
            const { align } = attributes;
            const props = { 'data-resized': true };

            if ( 'left' === align || 'right' === align || 'center' === align ) {
                props[ 'data-align' ] = align;
            }

            return props;
        },
        deprecated: [
            {
                attributes: blockAttrs,
                save: function ( { attributes } ) {
                    const { headings, loadMinimized, anchorColor, align = 'none', postTitle, headerTitle } = attributes;
                    // No heading blocks
                    if (headings.length < 1) {
                        return null;
                    }

                    let blockStyle = undefined;
                    if (loadMinimized) blockStyle = { display: 'none' };

                    const summary = (
                        <ul className={`advgb-toc align${align}`} style={ blockStyle }>
                            {headings.map( ( heading, index ) => {
                                return (
                                    <li className={'toc-level-' + heading.level}
                                        key={`summary-save-${index}`}
                                        style={{ marginLeft: heading.level * 20 }}
                                    >
                                        <a href={'#' + heading.anchor}>{heading.content}</a>
                                    </li>
                                )
                            } ) }
                            { anchorColor &&
                            <style>
                                {`.advgb-toc li a {
                                    color: ${anchorColor};
                                }`}
                            </style>
                            }
                        </ul>
                    );

                    if ( loadMinimized ) {
                        return (
                            <div className={`align${align}`}>
                                <div className={'advgb-toc-header collapsed'}>{ headerTitle || postTitle }</div>
                                {summary}
                            </div>
                        );
                    }

                    return summary;
                },
            },
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components, wp.data, wp.hooks );