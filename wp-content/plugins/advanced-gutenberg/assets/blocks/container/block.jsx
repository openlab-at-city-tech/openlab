(function ( wpI18n, wpBlocks, wpBlockEditor ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Fragment } = wp.element;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InnerBlocks, InspectorControls } = wpBlockEditor;

    const containerBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path d="M3 5v14h19V5H3zm2 2h15v4H5V7zm0 10v-4h4v4H5zm6 0v-4h9v4h-9z"/>
        </svg>
    );

    registerBlockType( 'advgb/container', {
        title: __( 'Container' ),
        description: __( 'Block for containing other blocks.' ),
        icon: {
            src: containerBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'container' ), __( 'row' ), __( 'box' ) ],
        attributes: {},
        supports: {
            align: true,
            inserter: false,
        },
        transforms: {
            to: [
                {
                    type: 'block',
                    blocks: [ 'advgb/columns' ],
                    transform: ( attributes, innerBlocks ) => {
                        const columnBlock = createBlock(
                            'advgb/column',
                            {},
                            innerBlocks,
                        );

                        return createBlock(
                            'advgb/columns',
                            { columns: 1, className: attributes.className },
                            [ columnBlock ],
                        )
                    }
                }
            ]
        },
        edit: function () {
            return (
                <Fragment>
                    <InspectorControls>
                        <div style={ {
                            color: '#ff0000',
                            fontStyle: 'italic',
                            marginTop: 20,
                            padding: 10,
                            borderTop: '1px solid #ccc',
                        } }
                        >
                            { __(
                                'We will remove this block in the future release. ' +
                                'Please convert it to Columns Manager block to avoid unwanted error. ' +
                                'Columns Manager block has a lot of styles and improvements.'
                            ) }
                        </div>
                    </InspectorControls>
                    <div className="advgb-blocks-container">
                        <InnerBlocks />
                    </div>
                </Fragment>
            )
        },
        save: function () {
            return (
                <div className="advgb-blocks-container">
                    <InnerBlocks.Content />
                </div>
            );
        },
    } );
})( wp.i18n, wp.blocks, wp.blockEditor );