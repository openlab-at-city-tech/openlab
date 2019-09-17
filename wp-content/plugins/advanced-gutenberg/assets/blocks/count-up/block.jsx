(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings } = wpBlockEditor;
    const { RangeControl, PanelBody, TextControl, FormToggle } = wpComponents;

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
            } = attributes;

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Count Up Settings' ) }>
                            <PanelColorSettings
                                title={ __( 'Color Settings' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Header Color' ),
                                        value: headerTextColor,
                                        onChange: ( value ) => setAttributes( { headerTextColor: value } ),
                                    },
                                    {
                                        label: __( 'Count Up Color' ),
                                        value: countUpNumberColor,
                                        onChange: ( value ) => setAttributes( { countUpNumberColor: value } ),
                                    },
                                    {
                                        label: __( 'Description Color' ),
                                        value: descTextColor,
                                        onChange: ( value ) => setAttributes( { descTextColor: value } ),
                                    },
                                ] }
                            />
                            <RangeControl
                                label={ __( 'Columns' ) }
                                min={ 1 }
                                max={ 3 }
                                value={ columns }
                                onChange={ (value) => setAttributes( { columns: value } ) }
                            />
                            <RangeControl
                                label={ __( 'Counter Number Size' ) }
                                min={ 10 }
                                max={ 100 }
                                value={ countUpNumberSize }
                                onChange={ (value) => setAttributes( { countUpNumberSize: value } ) }
                            />
                            <div>{ __( 'Counter Up Symbol' ) }</div>
                            {
                                <div className="advgb-col-3">
                                    <TextControl
                                        value={ countUpSymbol }
                                        onChange={ (value) => setAttributes( { countUpSymbol: value } ) }
                                    />
                                    <FormToggle
                                        checked={ countUpSymbolAfter }
                                        onChange={ () => setAttributes( { countUpSymbolAfter: !countUpSymbolAfter } ) }
                                        title={ !!countUpSymbolAfter ? __( 'After' ) : __( 'Before' ) }
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
                                    title={ !!countUpSymbolAfter2 ? __( 'After' ) : __( 'Before' ) }
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
                                    title={ !!countUpSymbolAfter3 ? __( 'After' ) : __( 'Before' ) }
                                />
                            </div>
                            }
                            <p className={'components-base-control__help'} style={ { clear: 'both' } }>
                                { __( 'Use toggle buttons above to define symbol placement before/after the number (toggle on is after).' ) }
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
                                placeholder={ __( 'Enter text…' ) }
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
                                placeholder={ __( 'Enter text…' ) }
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
                                placeholder={ __( 'Enter text…' ) }
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
                                placeholder={ __( 'Enter text…' ) }
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
                                placeholder={ __( 'Enter text…' ) }
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
                                placeholder={ __( 'Enter text…' ) }
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
        title: __( 'Count Up' ),
        description: __( 'Make a block with animate counting numbers.' ),
        icon: {
            src: countUpBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'numbers' ), __( 'count' ), __( 'increase' ) ],
        attributes: {
            headerText: {
                type: 'string',
                default: __( 'Header text' ),
            },
            headerText2: {
                type: 'string',
                default: __( 'Header text' ),
            },
            headerText3: {
                type: 'string',
                default: __( 'Header text' ),
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
                default: __( 'and description' ),
            },
            descText2: {
                type: 'string',
                default: __( 'and description' ),
            },
            descText3: {
                type: 'string',
                default: __( 'and description' ),
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
        },
        edit: AdvCountUp,
        save: AdvCountUpSave,
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );