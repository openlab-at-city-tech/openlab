import latinize from "latinize";

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents, wpData, wpHooks ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, getBlockContent, createBlock } = wpBlocks;
    const { BlockControls, InspectorControls, InspectorAdvancedControls, PanelColorSettings, BlockAlignmentToolbar } = wpBlockEditor;
    const { ToolbarButton, Placeholder, Button, ToolbarGroup, ToggleControl, TextControl, PanelBody } = wpComponents;
    const { select, dispatch } = wpData;
    const { addFilter } = wpHooks;

    const summaryBlockIcon = (
        <svg height="20" viewBox="2 2 22 22" width="20" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    );

    const previewImageData = 'data:image/png;base64,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';

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
                const allBlocks = select( 'core/block-editor' ).getBlocks();
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
            this.latinise = this.latinise.bind(this);
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

        latinise(str) {
            let lettersArr = str.split('');
            let result = [];
            lettersArr.map(letter => {
                if(typeof latinMap[letter] === "undefined") {
                    result.push(letter);
                } else {
                    result.push(latinMap[letter]);
                }

            });
            return result.join('');
        }

        updateSummary() {
            let headingDatas = [];
            let headingBlocks = [];
            const allBlocks = select( 'core/block-editor' ).getBlocks();
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
                    let lowerHead = unescape(thisHead[ 'content' ].toLowerCase());
                    let headId = lowerHead.replace(/[!@#$%^&*()\/\\,?":{}|<>]/g, "");
                    headId = headId.replace(/(amp;)+/g, "");
                    headId = latinize(headId).replace(/\./g, ' ').replace(/[^\w ]+/g,'').replace(/ +/g,'-');
                    headId = headId + '-' + heading.clientId;

                    thisHead[ 'clientId' ] = heading.clientId;
                    if (heading.attributes.anchor) {
                        thisHead[ 'anchor' ] = heading.attributes.anchor;
                    } else {
                        // Generate a random anchor for headings without it
                        thisHead[ 'anchor' ] = headId;
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
            const { headings, loadMinimized, anchorColor, align, headerTitle, isPreview } = attributes;

            // No heading blocks
            let summaryContent = (
                isPreview ?
                    <img alt={__('Summary', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
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
                const { selectBlock } = dispatch( 'core/block-editor' );
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
                            <ToolbarGroup>
                                <ToolbarButton className={'components-icon-button components-toolbar__control'}
                                            icon={'update'}
                                            label={__( 'Update Summary', 'advanced-gutenberg' )}
                                            onClick={this.updateSummary}
                                />
                            </ToolbarGroup>
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
        isPreview: {
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
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            multiple: false,
            anchor: true
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
            {
                attributes: blockAttrs,
                save: ( { attributes } ) => {
                    
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
                },
            }
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components, wp.data, wp.hooks );
