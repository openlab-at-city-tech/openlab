import {AdvColorControl} from "../0-adv-components/components.jsx";

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings } = wpBlockEditor;
    const { RangeControl, PanelBody, TextControl , ToggleControl } = wpComponents;

    const searchBlockIcon = (
        <svg fill="none" height="20" width="20" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <circle fill="none" cx="11" cy="11" r="8"/>
            <line x1="21" x2="16.65" y1="21" y2="16.65"/>
        </svg>
    );

    const SEARCH_ICONS = {
        icon1: searchBlockIcon,
        icon2: (
            <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="none" d="M0 0h24v24H0V0zm0 0h24v24H0V0z"/>
                <path d="M17.01 14h-.8l-.27-.27c.98-1.14 1.57-2.61 1.57-4.23 0-3.59-2.91-6.5-6.5-6.5s-6.5 3-6.5 6.5H2l3.84 4 4.16-4H6.51C6.51 7 8.53 5 11.01 5s4.5 2.01 4.5 4.5c0 2.48-2.02 4.5-4.5 4.5-.65 0-1.26-.14-1.82-.38L7.71 15.1c.97.57 2.09.9 3.3.9 1.61 0 3.08-.59 4.22-1.57l.27.27v.79l5.01 4.99L22 19l-4.99-5z"/>
            </svg>
        ),
        icon3: (
            <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none"/>
                <path d="M20.94 11c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
            </svg>
        ),
        icon4: (
            <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="none" d="M0 0h24v24H0V0z"/>
                <path d="M18 13v7H4V6h5.02c.05-.71.22-1.38.48-2H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-5l-2-2zm-1.5 5h-11l2.75-3.53 1.96 2.36 2.75-3.54zm2.8-9.11c.44-.7.7-1.51.7-2.39C20 4.01 17.99 2 15.5 2S11 4.01 11 6.5s2.01 4.5 4.49 4.5c.88 0 1.7-.26 2.39-.7L21 13.42 22.42 12 19.3 8.89zM15.5 9C14.12 9 13 7.88 13 6.5S14.12 4 15.5 4 18 5.12 18 6.5 16.88 9 15.5 9z"/>
                <path fill="none" d="M0 0h24v24H0z"/>
            </svg>
        ),
        icon5: (
            <svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                <path d="M0 0h24v24H0z" fill="none"/>
            </svg>
        ),
        icon6: (
            <svg width="20" height="20" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.9462891,1C9.4033203,1,4.8935547,5.5097656,4.8935547,11.0532227  c0,2.5022583,0.9248047,4.7885132,2.4428101,6.5498657l-6.1166382,6.1166382c-0.2929688,0.2929688-0.2929688,0.7675781,0,1.0605469  C1.3662109,24.9267578,1.5576172,25,1.75,25s0.3837891-0.0732422,0.5302734-0.2197266l6.1165771-6.1165771  c1.7612305,1.5180054,4.0474243,2.442749,6.5494385,2.442749C20.4902344,21.1064453,25,16.5966797,25,11.0532227  S20.4902344,1,14.9462891,1z M14.9462891,19.6064453c-4.7158203,0-8.5527344-3.8369141-8.5527344-8.5532227  S10.2304688,2.5,14.9462891,2.5C19.6630859,2.5,23.5,6.3369141,23.5,11.0532227S19.6630859,19.6064453,14.9462891,19.6064453z" fill="currentColor"/>
            </svg>
        )
    };

    const previewImageData = 'data:image/png;base64,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';

    class SearchBarEdit extends Component {
        constructor() {
            super( ...arguments );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-search-bar'];

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
            const { clientId, attributes, setAttributes } = this.props;
            const { searchBtnId } = attributes;

            if (!searchBtnId) {
                setAttributes( { searchBtnId: `advgb-search-btn-${clientId}` } )
            }
        }


        render() {
            const { attributes, setAttributes, className } = this.props;
            const {
                fullWidth, width, textColor, backgroundColor, searchIcon, searchIconOnRight,
                searchPlaceholder, searchButtonEnabled, searchButtonText, searchButtonTextColor,
                searchButtonBgColor, searchButtonRadius, searchButtonOnLeft, searchBtnId,
                searchBtnHoverColor, searchBtnHoverBgColor, searchBtnHoverShadow, searchBtnHoverShadowH, searchBtnHoverShadowV,
                searchBtnHoverShadowBlur, searchBtnHoverShadowSpread, searchBtnHoverOpacity, searchBtnHoverTranSpeed,
                isPreview,
            } = attributes;

            const searchBarIcon = (
                <span className="advgb-search-bar-icon">
                    {searchIcon ? SEARCH_ICONS[searchIcon] : searchBlockIcon}
                </span>
            );

            const searchBarButton = !searchButtonEnabled ? '' : (
                <div className="advgb-search-button-wrapper">
                    <span className="advgb-search-bar-button"
                          style={ {
                              color: searchButtonTextColor,
                              borderColor: searchButtonTextColor,
                              backgroundColor: searchButtonBgColor,
                              borderRadius: searchButtonRadius,
                          } }
                    >
                        <RichText
                            tagName="span"
                            value={ searchButtonText }
                            onChange={ (value) => setAttributes( { searchButtonText: value.trim() } ) }
                            onReplace={ () => null }
                            onSplit={ () => null }
                            placeholder={ 'Search' }
                            keepPlaceholderOnFocus
                        />
                    </span>
                </div>
            );

            return (
                isPreview ?
                    <img alt={__('Search Bar', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Search Bar State', 'advanced-gutenberg' ) }>
                            <ToggleControl
                                label={ __( 'Full width', 'advanced-gutenberg' ) }
                                checked={ fullWidth }
                                onChange={ () => setAttributes( { fullWidth: !fullWidth } ) }
                            />
                            {!fullWidth && (
                                <RangeControl
                                    label={ __( 'Width', 'advanced-gutenberg' ) }
                                    value={ width }
                                    onChange={ (value) => setAttributes( { width: value } ) }
                                    min={ 300 }
                                    max={ 2000 }
                                />
                            ) }
                        </PanelBody>
                        <PanelBody title={ __( 'Search Icon Settings', 'advanced-gutenberg' ) }>
                            <ToggleControl
                                label={ __( 'Search icon on the right', 'advanced-gutenberg' ) }
                                checked={ searchIconOnRight }
                                onChange={ () => setAttributes( { searchIconOnRight: !searchIconOnRight } ) }
                            />
                            <div className="advgb-icon-items">
                                <div className="advgb-icon-items-header">
                                    { __( 'Search icon', 'advanced-gutenberg' ) }
                                </div>
                                <div className="advgb-icon-items-wrapper">
                                    {Object.keys(SEARCH_ICONS).map((icon, idx) => (
                                        <div className="advgb-icon-item" key={idx}>
                                            <span className={ icon === searchIcon ? 'active' : '' }
                                                  onClick={ () => setAttributes( { searchIcon: icon } ) }
                                            >
                                                {SEARCH_ICONS[icon]}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </PanelBody>
                        <PanelBody title={ __( 'Search Input Settings', 'advanced-gutenberg' ) } initialOpen={false}>
                            <TextControl
                                label={ __( 'Search placeholder', 'advanced-gutenberg' ) }
                                value={ searchPlaceholder }
                                onChange={ (value) => setAttributes( { searchPlaceholder: value } ) }
                            />
                            <PanelColorSettings
                                title={ __( 'Input Color', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Background color', 'advanced-gutenberg' ),
                                        value: backgroundColor,
                                        onChange: (value) => setAttributes( { backgroundColor: value } ),
                                    },
                                    {
                                        label: __( 'Text color', 'advanced-gutenberg' ),
                                        value: textColor,
                                        onChange: (value) => setAttributes( { textColor: value } ),
                                    },
                                ] }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Search Button Settings', 'advanced-gutenberg' ) } initialOpen={false}>
                            <ToggleControl
                                label={ __( 'Show submit button', 'advanced-gutenberg' ) }
                                checked={ searchButtonEnabled }
                                onChange={ () => setAttributes( { searchButtonEnabled: !searchButtonEnabled } ) }
                            />
                            <ToggleControl
                                label={ __( 'Search button on the left', 'advanced-gutenberg' ) }
                                checked={ searchButtonOnLeft }
                                onChange={ () => setAttributes( { searchButtonOnLeft: !searchButtonOnLeft } ) }
                            />
                            {searchButtonEnabled && (
                                <Fragment>
                                    <AdvColorControl
                                        label={ __('Background color', 'advanced-gutenberg') }
                                        value={ searchButtonBgColor }
                                        onChange={ (value) => setAttributes( { searchButtonBgColor: value } ) }
                                    />
                                    <AdvColorControl
                                        label={ __('Text color', 'advanced-gutenberg') }
                                        value={ searchButtonTextColor }
                                        onChange={ (value) => setAttributes( { searchButtonTextColor: value } ) }
                                    />
                                    <RangeControl
                                        label={ __( 'Border radius (px)', 'advanced-gutenberg' ) }
                                        help={ __( 'Affect both input and button.', 'advanced-gutenberg' ) }
                                        value={ searchButtonRadius }
                                        onChange={ (value) => setAttributes( { searchButtonRadius: value } ) }
                                        min={ 0 }
                                        max={ 100 }
                                    />
                                    <PanelColorSettings
                                        title={ __( 'Hover Colors', 'advanced-gutenberg' ) }
                                        initialOpen={ false }
                                        colorSettings={ [
                                            {
                                                label: __( 'Background color', 'advanced-gutenberg' ),
                                                value: searchBtnHoverBgColor,
                                                onChange: (value) => setAttributes( { searchBtnHoverBgColor: value } ),
                                            },
                                            {
                                                label: __( 'Text color', 'advanced-gutenberg' ),
                                                value: searchBtnHoverColor,
                                                onChange: (value) => setAttributes( { searchBtnHoverColor: value } ),
                                            },
                                            {
                                                label: __( 'Shadow color', 'advanced-gutenberg' ),
                                                value: searchBtnHoverShadow,
                                                onChange: (value) => setAttributes( { searchBtnHoverShadow: value } ),
                                            },
                                        ] }
                                    />
                                    <PanelBody title={ __( 'Hover Shadow', 'advanced-gutenberg' ) } initialOpen={false}>
                                        <RangeControl
                                            label={ __('Opacity (%)', 'advanced-gutenberg') }
                                            value={ searchBtnHoverOpacity }
                                            onChange={ ( value ) => setAttributes( { searchBtnHoverOpacity: value } ) }
                                            min={ 0 }
                                            max={ 100 }
                                        />
                                        <RangeControl
                                            label={ __('Transition speed (ms)', 'advanced-gutenberg') }
                                            value={ searchBtnHoverTranSpeed || '' }
                                            onChange={ ( value ) => setAttributes( { searchBtnHoverTranSpeed: value } ) }
                                            min={ 0 }
                                            max={ 3000 }
                                        />
                                        <RangeControl
                                            label={ __( 'Shadow H offset', 'advanced-gutenberg' ) }
                                            value={ searchBtnHoverShadowH || '' }
                                            onChange={ ( value ) => setAttributes( { searchBtnHoverShadowH: value } ) }
                                            min={ -50 }
                                            max={ 50 }
                                        />
                                        <RangeControl
                                            label={ __( 'Shadow V offset', 'advanced-gutenberg' ) }
                                            value={ searchBtnHoverShadowV || '' }
                                            onChange={ ( value ) => setAttributes( { searchBtnHoverShadowV: value } ) }
                                            min={ -50 }
                                            max={ 50 }
                                        />
                                        <RangeControl
                                            label={ __( 'Shadow blur', 'advanced-gutenberg' ) }
                                            value={ searchBtnHoverShadowBlur || '' }
                                            onChange={ ( value ) => setAttributes( { searchBtnHoverShadowBlur: value } ) }
                                            min={ 0 }
                                            max={ 50 }
                                        />
                                        <RangeControl
                                            label={ __( 'Shadow spread', 'advanced-gutenberg' ) }
                                            value={ searchBtnHoverShadowSpread || '' }
                                            onChange={ ( value ) => setAttributes( { searchBtnHoverShadowSpread: value } ) }
                                            min={ 0 }
                                            max={ 50 }
                                        />
                                    </PanelBody>
                                </Fragment>
                            ) }
                        </PanelBody>
                    </InspectorControls>
                    <div className={`advgb-search-bar-wrapper ${className}`}>
                        <div className="advgb-search-bar-inner" style={ { width: fullWidth ? '100%' : width } }>
                            {searchButtonOnLeft && searchBarButton}
                            <div className="advgb-search-bar"
                                 style={ {
                                     backgroundColor: backgroundColor,
                                     color: textColor,
                                     borderRadius: searchButtonRadius,
                                 } }
                            >
                                {!searchIconOnRight && searchBarIcon}
                                <input type="text" disabled={true}
                                       className="advgb-search-bar-input"
                                       value={ searchPlaceholder ? searchPlaceholder : 'Type to search…' }
                                />
                                {searchIconOnRight && searchBarIcon}
                            </div>
                            {!searchButtonOnLeft && searchBarButton}
                        </div>
                        <style>
                            {`.${searchBtnId}:hover {
                                color: ${searchBtnHoverColor} !important;
                                background-color: ${searchBtnHoverBgColor} !important;
                                box-shadow: ${searchBtnHoverShadowH}px ${searchBtnHoverShadowV}px ${searchBtnHoverShadowBlur}px ${searchBtnHoverShadowSpread}px ${searchBtnHoverShadow};
                                transition: all ${searchBtnHoverTranSpeed}s ease;
                                opacity: ${searchBtnHoverOpacity/100}
                            }`}
                        </style>
                    </div>
                </Fragment>
            )
        }
    }

    const blockAttrs = {
        fullWidth: {
            type: 'boolean',
            default: false,
        },
        width: {
            type: 'number',
            default: 500,
        },
        textColor: {
            type: 'string',
        },
        backgroundColor: {
            type: 'string',
        },
        searchIcon: {
            type: 'string',
            default: 'icon1',
        },
        searchIconOnRight: {
            type: 'boolean',
        },
        searchPlaceholder: {
            type: 'string',
        },
        searchButtonEnabled: {
            type: 'boolean',
            default: true,
        },
        searchButtonText: {
            type: 'string',
            default: 'SEARCH',
        },
        searchButtonTextColor: {
            type: 'string',
        },
        searchButtonBgColor: {
            type: 'string',
        },
        searchButtonRadius: {
            type: 'number',
            default: 0,
        },
        searchButtonOnLeft: {
            type: 'boolean',
            default: false,
        },
        searchBtnId : {
            type: 'string',
        },
        searchBtnHoverColor: {
            type: 'string',
        },
        searchBtnHoverBgColor: {
            type: 'string',
        },
        searchBtnHoverShadow: {
            type: 'string',
        },
        searchBtnHoverShadowH: {
            type: 'number',
            default: 1,
        },
        searchBtnHoverShadowV: {
            type: 'number',
            default: 1,
        },
        searchBtnHoverShadowBlur: {
            type: 'number',
            default: 12,
        },
        searchBtnHoverShadowSpread: {
            type: 'number',
            default: 0,
        },
        searchBtnHoverOpacity: {
            type: 'number',
            default: 100,
        },
        searchBtnHoverTranSpeed: {
            type: 'number',
            default: 200,
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

    registerBlockType('advgb/search-bar', {
        title: __( 'Search Bar', 'advanced-gutenberg' ),
        description: __( 'Easy to create a search bar for your site.', 'advanced-gutenberg' ),
        icon: {
            src: searchBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'accordion', 'advanced-gutenberg' ), __( 'list', 'advanced-gutenberg' ), __( 'faq', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            align: true,
            anchor: true
        },
        styles: [
            { name: 'default', label: __( 'Default', 'advanced-gutenberg' ), isDefault: true },
            { name: 'classic', label: __( 'Classic', 'advanced-gutenberg' ) },
        ],
        edit: SearchBarEdit,
        save: function ( { attributes, className } ) {
            const {
                fullWidth, width, textColor, backgroundColor, searchIcon, searchIconOnRight,
                searchPlaceholder, searchButtonEnabled, searchButtonText, searchButtonTextColor,
                searchButtonBgColor, searchButtonRadius, searchButtonOnLeft, searchBtnId,
            } = attributes;

            const searchBarIcon = (
                <span className="advgb-search-bar-icon">
                    {searchIcon ? SEARCH_ICONS[searchIcon] : searchBlockIcon}
                </span>
            );

            const searchBarButton = !searchButtonEnabled ? '' : (
                <div className="advgb-search-button-wrapper">
                    <button
                        type="submit"
                        className={`advgb-search-bar-button ${searchBtnId}`}
                        style={ {
                            color: searchButtonTextColor,
                            borderColor: searchButtonTextColor,
                            backgroundColor: searchButtonBgColor,
                            borderRadius: searchButtonRadius,
                        } }
                    >
                        {searchButtonText}
                    </button>
                </div>
            );

            return (
                <div className={`advgb-search-bar-wrapper ${className}`}>
                    <form method="get"
                          action={advgbBlocks.home_url}
                          className="advgb-search-bar-form"
                          role="search"
                    >
                        <div className="advgb-search-bar-inner" style={ { width: fullWidth ? '100%' : width } }>
                            {searchButtonOnLeft && searchBarButton}
                            <div className="advgb-search-bar"
                                 style={ {
                                     backgroundColor: backgroundColor,
                                     color: textColor,
                                     borderRadius: searchButtonRadius,
                                 } }
                            >
                                {!searchIconOnRight && searchBarIcon}
                                <input type="text"
                                       className="advgb-search-bar-input"
                                       name="s"
                                       placeholder={ searchPlaceholder ? searchPlaceholder : 'Type to search…' }
                                />
                                {searchIconOnRight && searchBarIcon}
                            </div>
                            {!searchButtonOnLeft && searchBarButton}
                        </div>
                    </form>
                </div>
            );
        }
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );