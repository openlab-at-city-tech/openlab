(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings } = wpBlockEditor;
    const { RangeControl, PanelBody, TextControl, FormToggle } = wpComponents;

    const previewImageData = 'data:image/png;base64,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';

    class AdvCountUp extends Component {
        constructor() {
            super(...arguments);
            this.state = {
                currentEdit: '',
            };
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-count-up'];

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

        render() {
            const { currentEdit } = this.state;
            const { attributes, setAttributes, isSelected } = this.props;
            const {
                headerText,
                headerText2,
                headerText3,
                headerTextColor,
                countUpNumber,
                countUpNumber2,
                countUpNumber3,
                countUpNumberColor,
                countUpNumberSize,
                countUpSymbol,
                countUpSymbol2,
                countUpSymbol3,
                countUpSymbolAfter,
                countUpSymbolAfter2,
                countUpSymbolAfter3,
                descText,
                descText2,
                descText3,
                descTextColor,
                columns,
                isPreview,
            } = attributes;

            return (
                isPreview ?
                    <img alt={__('Count Up', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Count Up Settings', 'advanced-gutenberg' ) }>
                            <PanelColorSettings
                                title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Header Color', 'advanced-gutenberg' ),
                                        value: headerTextColor,
                                        onChange: ( value ) => setAttributes( { headerTextColor: value } ),
                                    },
                                    {
                                        label: __( 'Count Up Color', 'advanced-gutenberg' ),
                                        value: countUpNumberColor,
                                        onChange: ( value ) => setAttributes( { countUpNumberColor: value } ),
                                    },
                                    {
                                        label: __( 'Description Color', 'advanced-gutenberg' ),
                                        value: descTextColor,
                                        onChange: ( value ) => setAttributes( { descTextColor: value } ),
                                    },
                                ] }
                            />
                            <RangeControl
                                label={ __( 'Columns', 'advanced-gutenberg' ) }
                                min={ 1 }
                                max={ 3 }
                                value={ columns }
                                onChange={ (value) => setAttributes( { columns: value } ) }
                            />
                            <RangeControl
                                label={ __( 'Counter Number Size', 'advanced-gutenberg' ) }
                                min={ 10 }
                                max={ 100 }
                                value={ countUpNumberSize }
                                onChange={ (value) => setAttributes( { countUpNumberSize: value } ) }
                            />
                            <div>{ __( 'Counter Up Symbol', 'advanced-gutenberg' ) }</div>
                            {
                                <div className="advgb-col-3">
                                    <TextControl
                                        value={ countUpSymbol }
                                        onChange={ (value) => setAttributes( { countUpSymbol: value } ) }
                                    />
                                    <FormToggle
                                        checked={ countUpSymbolAfter }
                                        onChange={ () => setAttributes( { countUpSymbolAfter: !countUpSymbolAfter } ) }
                                        title={ !!countUpSymbolAfter ? __( 'After', 'advanced-gutenberg' ) : __( 'Before', 'advanced-gutenberg' ) }
                                    />
                                </div>
                            }
                            {parseInt(columns) > 1 &&
                            <div className="advgb-col-3">
                                <TextControl
                                    value={ countUpSymbol2 }
                                    onChange={ (value) => setAttributes( { countUpSymbol2: value } ) }
                                />
                                <FormToggle
                                    checked={ countUpSymbolAfter2 }
                                    onChange={ () => setAttributes( { countUpSymbolAfter2: !countUpSymbolAfter2 } ) }
                                    title={ !!countUpSymbolAfter2 ? __( 'After', 'advanced-gutenberg' ) : __( 'Before', 'advanced-gutenberg' ) }
                                />
                            </div>
                            }
                            {parseInt(columns) > 2 &&
                            <div className="advgb-col-3">
                                <TextControl
                                    value={ countUpSymbol3 }
                                    onChange={ (value) => setAttributes( { countUpSymbol3: value } ) }
                                />
                                <FormToggle
                                    checked={ countUpSymbolAfter3 }
                                    onChange={ () => setAttributes( { countUpSymbolAfter3: !countUpSymbolAfter3 } ) }
                                    title={ !!countUpSymbolAfter3 ? __( 'After', 'advanced-gutenberg' ) : __( 'Before', 'advanced-gutenberg' ) }
                                />
                            </div>
                            }
                            <p className={'components-base-control__help'} style={ { clear: 'both' } }>
                                { __( 'Use toggle buttons above to define symbol placement before/after the number (toggle on is after).', 'advanced-gutenberg' ) }
                            </p>
                        </PanelBody>
                    </InspectorControls>
                    <div className={`advgb-count-up advgb-column-${columns}`} style={ { display: 'flex' } }>
                        <div className="advgb-count-up-columns-one" style={ { textAlign: 'center' } }>
                            <RichText
                                tagName="h4"
                                value={ headerText }
                                onChange={ (value) => setAttributes( { headerText: value } ) }
                                isSelected={ isSelected && currentEdit === 'header' }
                                unstableOnFocus={ () => this.setState( { currentEdit: 'header' } ) }
                                style={ { color: headerTextColor } }
                                placeholder={ __( 'Enter text…', 'advanced-gutenberg' ) }
                                className="advgb-count-up-header"
                            />
                            <div className="advgb-counter">
                                {countUpSymbol && !countUpSymbolAfter && (
                                    <span className="advgb-counter-symbol"
                                          style={ {
                                              fontSize: countUpNumberSize,
                                              color: countUpNumberColor,
                                          } }
                                    >
                                        { countUpSymbol }
                                    </span>
                                ) }
                                <RichText
                                    tagName="div"
                                    value={ countUpNumber }
                                    onChange={ (value) => setAttributes( { countUpNumber: value } ) }
                                    isSelected={ isSelected && currentEdit === 'countUp' }
                                    unstableOnFocus={ () => this.setState( { currentEdit: 'countUp' } ) }
                                    style={ { fontSize: countUpNumberSize + 'px', color: countUpNumberColor } }
                                    className="advgb-counter-number"
                                />
                                {countUpSymbol && countUpSymbolAfter && (
                                    <span className="advgb-counter-symbol"
                                          style={ {
                                              fontSize: countUpNumberSize,
                                              color: countUpNumberColor,
                                          } }
                                    >
                                        { countUpSymbol }
                                    </span>
                                ) }
                            </div>
                            <RichText
                                tagName="p"
                                value={ descText }
                                onChange={ (value) => setAttributes( { descText: value } ) }
                                isSelected={ isSelected && currentEdit === 'desc' }
                                unstableOnFocus={ () => this.setState( { currentEdit: 'desc' } ) }
                                style={ { color: descTextColor } }
                                placeholder={ __( 'Enter text…', 'advanced-gutenberg' ) }
                                className="advgb-count-up-desc"
                            />
                        </div>
                        <div className="advgb-count-up-columns-two" style={ { textAlign: 'center' } }>
                            <RichText
                                tagName="h4"
                                value={ headerText2 }
                                onChange={ (value) => setAttributes( { headerText2: value } ) }
                                isSelected={ isSelected && currentEdit === 'header2' }
                                unstableOnFocus={ () => this.setState( { currentEdit: 'header2' } ) }
                                style={ { color: headerTextColor } }
                                placeholder={ __( 'Enter text…', 'advanced-gutenberg' ) }
                                className="advgb-count-up-header"
                            />
                            <div className="advgb-counter">
                                {countUpSymbol2 && !countUpSymbolAfter2 && (
                                    <span className="advgb-counter-symbol"
                                          style={ {
                                              fontSize: countUpNumberSize,
                                              color: countUpNumberColor,
                                          } }
                                    >
                                        { countUpSymbol2 }
                                    </span>
                                ) }
                                <RichText
                                    tagName="div"
                                    value={ countUpNumber2 }
                                    onChange={ (value) => setAttributes( { countUpNumber2: value } ) }
                                    isSelected={ isSelected && currentEdit === 'countUp2' }
                                    unstableOnFocus={ () => this.setState( { currentEdit: 'countUp2' } ) }
                                    style={ { fontSize: countUpNumberSize + 'px', color: countUpNumberColor } }
                                    className="advgb-counter-number"
                                />
                                {countUpSymbol2 && countUpSymbolAfter2 && (
                                    <span className="advgb-counter-symbol"
                                          style={ {
                                              fontSize: countUpNumberSize,
                                              color: countUpNumberColor,
                                          } }
                                    >
                                        { countUpSymbol2 }
                                    </span>
                                ) }
                            </div>
                            <RichText
                                tagName="p"
                                value={ descText2 }
                                onChange={ (value) => setAttributes( { descText2: value } ) }
                                isSelected={ isSelected && currentEdit === 'desc2' }
                                unstableOnFocus={ () => this.setState( { currentEdit: 'desc2' } ) }
                                style={ { color: descTextColor } }
                                placeholder={ __( 'Enter text…', 'advanced-gutenberg' ) }
                                className="advgb-count-up-desc"
                            />
                        </div>
                        <div className="advgb-count-up-columns-three" style={ { textAlign: 'center' } }>
                            <RichText
                                tagName="h4"
                                value={ headerText3 }
                                onChange={ (value) => setAttributes( { headerText3: value } ) }
                                isSelected={ isSelected && currentEdit === 'header3' }
                                unstableOnFocus={ () => this.setState( { currentEdit: 'header3' } ) }
                                style={ { color: headerTextColor } }
                                placeholder={ __( 'Enter text…', 'advanced-gutenberg' ) }
                                className="advgb-count-up-header"
                            />
                            <div className="advgb-counter">
                                {countUpSymbol3 && !countUpSymbolAfter3 && (
                                    <span className="advgb-counter-symbol"
                                          style={ {
                                              fontSize: countUpNumberSize,
                                              color: countUpNumberColor,
                                          } }
                                    >
                                        { countUpSymbol3 }
                                    </span>
                                ) }
                                <RichText
                                    tagName="div"
                                    value={ countUpNumber3 }
                                    onChange={ (value) => setAttributes( { countUpNumber3: value } ) }
                                    isSelected={ isSelected && currentEdit === 'countUp3' }
                                    unstableOnFocus={ () => this.setState( { currentEdit: 'countUp3' } ) }
                                    style={ { fontSize: countUpNumberSize + 'px', color: countUpNumberColor } }
                                    className="advgb-counter-number"
                                />
                                {countUpSymbol3 && countUpSymbolAfter3 && (
                                    <span className="advgb-counter-symbol"
                                          style={ {
                                              fontSize: countUpNumberSize,
                                              color: countUpNumberColor,
                                          } }
                                    >
                                        { countUpSymbol3 }
                                    </span>
                                ) }
                            </div>
                            <RichText
                                tagName="p"
                                value={ descText3 }
                                onChange={ (value) => setAttributes( { descText3: value } ) }
                                isSelected={ isSelected && currentEdit === 'desc3' }
                                unstableOnFocus={ () => this.setState( { currentEdit: 'desc3' } ) }
                                style={ { color: descTextColor } }
                                placeholder={ __( 'Enter text…', 'advanced-gutenberg' ) }
                                className="advgb-count-up-desc"
                            />
                        </div>
                    </div>
                </Fragment>
            )
        }
    }

    function AdvCountUpSave( { attributes } ) {
        const {
            headerText,
            headerText2,
            headerText3,
            headerTextColor,
            countUpNumber,
            countUpNumber2,
            countUpNumber3,
            countUpNumberColor,
            countUpNumberSize,
            countUpSymbol,
            countUpSymbol2,
            countUpSymbol3,
            countUpSymbolAfter,
            countUpSymbolAfter2,
            countUpSymbolAfter3,
            descText,
            descText2,
            descText3,
            descTextColor,
            columns,
        } = attributes;

        const countSymbolElm = countUpSymbol ? <span className="advgb-counter-symbol">{ countUpSymbol }</span> : '';
        const countSymbolElm2 = countUpSymbol2 ? <span className="advgb-counter-symbol">{ countUpSymbol2 }</span> : '';
        const countSymbolElm3 = countUpSymbol3 ? <span className="advgb-counter-symbol">{ countUpSymbol3 }</span> : '';

        return (
            <div className="advgb-count-up" style={ { display: 'flex' } }>
                <div className="advgb-count-up-columns-one" style={ { textAlign: 'center' } }>
                    <h4 className="advgb-count-up-header" style={ { color: headerTextColor } }>
                        { headerText }
                    </h4>
                    <div className="advgb-counter"
                         style={ { color: countUpNumberColor, fontSize: countUpNumberSize + 'px' } }
                    >
                        {!countUpSymbolAfter && countSymbolElm}
                        <span className="advgb-counter-number">{ countUpNumber }</span>
                        {!!countUpSymbolAfter && countSymbolElm}
                    </div>
                    <p className="advgb-count-up-desc" style={ { color: descTextColor } }>
                        { descText }
                    </p>
                </div>
                {parseInt(columns) > 1 && (
                    <div className="advgb-count-up-columns-two" style={ { textAlign: 'center' } }>
                        <h4 className="advgb-count-up-header" style={ { color: headerTextColor } }>
                            { headerText2 }
                        </h4>
                        <div className="advgb-counter"
                             style={ { color: countUpNumberColor, fontSize: countUpNumberSize + 'px' } }
                        >
                            {!countUpSymbolAfter2 && countSymbolElm2}
                            <span className="advgb-counter-number">{ countUpNumber2 }</span>
                            {!!countUpSymbolAfter2 && countSymbolElm2}
                        </div>
                        <p className="advgb-count-up-desc" style={ { color: descTextColor } }>
                            { descText2 }
                        </p>
                    </div>
                ) }
                {parseInt(columns) > 2 && (
                    <div className="advgb-count-up-columns-three" style={ { textAlign: 'center' } }>
                        <h4 className="advgb-count-up-header" style={ { color: headerTextColor } }>
                            { headerText3 }
                        </h4>
                        <div className="advgb-counter"
                             style={ { color: countUpNumberColor, fontSize: countUpNumberSize + 'px' } }
                        >
                            {!countUpSymbolAfter3 && countSymbolElm3}
                            <span className="advgb-counter-number">{ countUpNumber3 }</span>
                            {!!countUpSymbolAfter3 && countSymbolElm3}
                        </div>
                        <p className="advgb-count-up-desc" style={ { color: descTextColor } }>
                            { descText3 }
                        </p>
                    </div>
                ) }
            </div>
        );
    }

    const countUpBlockIcon = (
        <svg height="20" viewBox="2 2 22 22" width="20" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0h24v24H0zm0 0h24v24H0z" fill="none"/>
            <path d="M16.05 16.29l2.86-3.07c.38-.39.72-.79 1.04-1.18.32-.39.59-.78.82-1.17.23-.39.41-.78.54-1.17.13-.39.19-.79.19-1.18 0-.53-.09-1.02-.27-1.46-.18-.44-.44-.81-.78-1.11-.34-.31-.77-.54-1.26-.71-.51-.16-1.08-.24-1.72-.24-.69 0-1.31.11-1.85.32-.54.21-1 .51-1.36.88-.37.37-.65.8-.84 1.3-.18.47-.27.97-.28 1.5h2.14c.01-.31.05-.6.13-.87.09-.29.23-.54.4-.75.18-.21.41-.37.68-.49.27-.12.6-.18.96-.18.31 0 .58.05.81.15.23.1.43.25.59.43.16.18.28.4.37.65.08.25.13.52.13.81 0 .22-.03.43-.08.65-.06.22-.15.45-.29.7-.14.25-.32.53-.56.83-.23.3-.52.65-.88 1.03l-4.17 4.55V18H22v-1.71h-5.95zM8 7H6v4H2v2h4v4h2v-4h4v-2H8V7z"/>
        </svg>
    );

    registerBlockType( 'advgb/count-up', {
        title: __( 'Count Up', 'advanced-gutenberg' ),
        description: __( 'Make a block with animate counting numbers.', 'advanced-gutenberg' ),
        icon: {
            src: countUpBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'numbers', 'advanced-gutenberg' ), __( 'count', 'advanced-gutenberg' ), __( 'increase', 'advanced-gutenberg' ) ],
        attributes: {
            headerText: {
                type: 'string',
                default: 'Header text',
            },
            headerText2: {
                type: 'string',
                default: 'Header text',
            },
            headerText3: {
                type: 'string',
                default: 'Header text',
            },
            headerTextColor: {
                type: 'string',
            },
            countUpNumber: {
                type: 'string',
                default: '56789'
            },
            countUpNumber2: {
                type: 'string',
                default: '56789'
            },
            countUpNumber3: {
                type: 'string',
                default: '56789'
            },
            countUpNumberColor: {
                type: 'string',
            },
            countUpNumberSize: {
                type: 'number',
                default: 55,
            },
            countUpSymbol: {
                type: 'string',
            },
            countUpSymbol2: {
                type: 'string',
            },
            countUpSymbol3: {
                type: 'string',
            },
            countUpSymbolAfter: {
                type: 'boolean',
                default: false,
            },
            countUpSymbolAfter2: {
                type: 'boolean',
                default: false,
            },
            countUpSymbolAfter3: {
                type: 'boolean',
                default: false,
            },
            descText: {
                type: 'string',
                default: 'and description',
            },
            descText2: {
                type: 'string',
                default: 'and description',
            },
            descText3: {
                type: 'string',
                default: 'and description',
            },
            descTextColor: {
                type: 'string',
            },
            columns: {
                type: 'number',
                default: 1,
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
        edit: AdvCountUp,
        save: AdvCountUpSave,
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );