(function ( wpI18n, wpBlocks, wpBlockEditor ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Fragment } = wp.element;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InnerBlocks, InspectorControls } = wpBlockEditor;
    const { PanelBody, SelectControl } = wp.components;

    const containerBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path d="M3 5v14h19V5H3zm2 2h15v4H5V7zm0 10v-4h4v4H5zm6 0v-4h9v4h-9z"/>
        </svg>
    );

    registerBlockType( 'advgb/container', {
        title: __( 'Container', 'advanced-gutenberg' ),
        description: __( 'Block for containing other blocks.', 'advanced-gutenberg' ),
        icon: {
            src: containerBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'container', 'advanced-gutenberg' ), __( 'row', 'advanced-gutenberg' ), __( 'box', 'advanced-gutenberg' ) ],
        attributes: {
            wrapperTag: {
                type: 'string',
                default: 'div',
            }
        },
        supports: {
            align: true,
            className: true,
        },
        transforms: {
            to: [
                {
                    type: 'block',
                    blocks: [ 'advgb/columns' ],
                    transform: ( attributes, innerBlocks ) => {
                        const { className, wrapperTag } = attributes;
                        const columnBlock = createBlock(
                            'advgb/column',
                            {},
                            innerBlocks,
                        );

                        return createBlock(
                            'advgb/columns',
                            { columns: 1, className, wrapperTag },
                            [ columnBlock ],
                        )
                    }
                }
            ]
        },
        edit: function ( props ) {
            const { attributes, setAttributes, className } = props;
            const { wrapperTag } = attributes;

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Container Settings', 'advanced-gutenberg' ) }>
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
                        </PanelBody>
                    </InspectorControls>
                    <div className={`advgb-blocks-container ${className}`}>
                        <InnerBlocks />
                    </div>
                </Fragment>
            )
        },
        save: function ( { attributes } ) {
            const { wrapperTag : Tag } = attributes;

            return (
                <Tag className="advgb-blocks-container">
                    <InnerBlocks.Content />
                </Tag>
            );
        },
    } );
})( wp.i18n, wp.blocks, wp.blockEditor );