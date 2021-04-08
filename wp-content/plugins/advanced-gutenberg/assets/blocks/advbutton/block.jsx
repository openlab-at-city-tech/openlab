import {AdvColorControl} from "../0-adv-components/components.jsx";

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType, createBlock } = wpBlocks;
    const { InspectorControls, RichText, PanelColorSettings, URLInput } = wpBlockEditor;
    const { BaseControl, RangeControl, PanelBody, ToggleControl, SelectControl } = wpComponents;

    // Preview style images
    let previewImageData = '';
    const previewImageDataDefault = 'data:image/png;base64,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';
    const previewImageDataOutlined = 'data:image/png;base64,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';
    const previewImageDataSquaredOutline = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABoCAYAAADYQu11AAAE1ElEQVR4nO3d4VHrOhCG4T13bgO0EErYlHAoIZSQlEBKgBJICaSEnBJQCaSFlHDPiLuZ8YhVbDkOTLLvM8OfYBJs9FnSWjYCAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAmH5Nudeq+h/tCJhOSmmSjP7D3wS4ff9eaA8nHSkAAU06OqZHBwIg6EAABB0IgKADARB0IACCDgRA0IEACDoQAEEHAiDoQAAEHQiAoAMBEHQgAIIOBEDQgQAIOhAAQQcCIOhAAAQdCICgAwEQdCCASz0F9toMfeLmQUReRGSfUtrWNlLVnYj8Ll5+SCn9OfEzTyLyXLy8Tim92PdnIvJx5nH9k1J6UNVXEVme+V59+5P3X+1zZsW387FLIrJJKR2GfFjxPwP29vn7YhvvR/Mx637+2v6GoRD0NnfHMKpqbqiPZWOLzgL+bCGvWdjXs6pu7IQ2KPAmB/dNRObRj/dQDN3Hyw15p6p317oDU1PVHPBdT8hLucf/sBNEC1XVt2s6Pj+JoFfkf4XT/bLeIxVbzyYYAt8Emw48jdyXOztptpwgsoVNedCDoftAKaWkqmvrsboW3zHnsynCl/+AU5m7f87FT7zXSkRWznt5c/eTc3H7uWXlhJdPjNtjnaHz+y6ceoRY2O8bh/F5+J/3tzwJo4MevUGlwYceutvUxQttLrTNuyEXO2HZa3MrqnXV3qvPzk4gqCDo52vpfW7R0jnZ7W3UUGU9sLfNckTd486Kc6gg6A0qBaOTw9oAFs4uDprK2AjJO37ee5bKE6yOHA2EQNAHUtXc+F6LrfcRr8kWvAJadY2Bwwv6kKLcixP2pzMKgjeNYlxFsUDDkxvzqrFwdFMq8+J94zHximhD5tv5M3LB8b14/dlOHhTnOujRx9naNDP6/PxH2Tx/7fwOb9GLpCWCPs5xVdcH1d6fZRX8cqowcy6DhkbQK8oFM7Zopuw9QjeoyvLfWWPV3JuPNy0rTik9OkP1Y3Eu/BJlIehtar2HFeq6vMbV1/i971/D1MCbCw+pmh95VzLGzK8fK8W51tV2N4mgt/Ma4ZfryM42fQ1uqgb/3bwK+6DKt12u9Pa7pWr/yUYX3nX58HN1IegX410yWtbm87ZeuzwRHK5kWefG6Ulntpy2yo6Ft83gW1cd20pxLjyC3q6357WAlmHPPcu7rQv/lOeydseXt9DjKq7PWyi9cOUT23t500kOuL1W3icudsI4N6je9Co8rqMP1FnTXQa9dkPF2rnGm9/jta+3s6H/5uI7NZGU0sbuPCtvbFG7nXToirWHiS5ZruyzuSJiCHrFgAUzYj2Qu6bb7nZbVYanp+wnbPDfJq9tV9XDyJVpB3uIx1RTlYMV53bM0f/H0H283Cjnp54wk3s6W7019BLP1ntE0rVIKa1tf1sCm4/Rfd+tsCPUbpoJiR693brvmXFd1oDvbW4+c3q843PobuKeatvf+dTPjBvpWJwLf7PLlwcZnKMz3J30fYGAPrNkC7XOxtAdCICgAwEQdCAAgg4EQNCBAAg6EABBBwIg6EAABB0IgKADARB0IACCDgRA0IEACDoQAEEHAiDoQAAEHQjgUo+SGvJgRQDfhB4dAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABgPBH5CxnLf2zPqO7iAAAAAElFTkSuQmCC';
    const previewImageDataSquared = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABoCAYAAADYQu11AAAFXklEQVR4nO3c6ytsURzG8XUOUUSIXCNEFK/M//8XjFcUEUWuESKKyOmZ0z5nz7L22WuNfWjm9/3UlMbc9p71rPtsBwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJt+VHnU9Xr9nXIEVKdWq1WS0Z98J0DnI+iAAQQdMICgAwYQdMAAgg4YQNABAwg6YABBBwwg6IABBB0wgKADBhB0wACCDhhA0AEDCDpgAEEHDCDogAEEHTCAoAMGEHTAAIIOGEDQAQO6+ZKd29jYiHrc29ubu7y8dM/Pz+7m5qbwccvLy25gYKDpvv39fXd/f1/4nMnJSTc1NdV039nZmTs/P2/83dvb69bW1qI+Z5GHhwe3t7fn5ubm3Ojo6Kdeq+x4BgcHXX9/f+N9enp6mv53d3fnnp6e3NXVlXt9fY16v/x39PLy0jgOfQ9l1tfXm94/f04tIegJurq6/oRxfHzcHR4eRhU2SxTw6elp19fXV3jUQ0NDjZvO5fX1tTs9PY0OvCi4CwsLbmdnx/rpjkbXvUUqyGq5u7upKzMzMzNuaWnpnyH3qcVXT0UVRAq9x+Li4lcdWtujlBbY3Nxs+oe6obOzs02FWC3L2NiYya6g7zPDAfWUVEHs7u66x8fH6OepV6AhD+e/HEGPpAKoLqYKZJ4K21cUNA0R/MrHFYzds7F4kaOjo8bNFwpr2VhcVNmFQq5xuMbj+fOjzzsyMvJhPkJ0bre3t5O68Xodfb6UCsIiuu4JQgXeetddx68xuU9jb42h/UpQFZbuU+utSbU8teyh1yqjCkIVCIoR9E9KaX06kVpzBTRPAQ71GPLUAoceo55BauWp99fkHIoR9AShCSN1ky3T0MV3cXERdUbUQwqdv+Hh4dLnaqkzT3MnmgxEGEGPpHGlxrB5arliC3WnCs2w397eRh9tKOgxs/baz+CHXUuempzDR0zGFSjbRKNJJnU9LXfdQ+NiVX4p5yQ0iRYz3tZ7aKJwZWWl6X4m58Jo0VuQ7eyyPj7/bgqzdrr5NF63PknqI+gtyHZ1aXsls73fSzP4qnjztL/BXwa1jmqvQGjN2t+PrgKl3XFbW1vf/nm/Q2j7r86JWtPY3o42IvlStxUfHBy41dXVprF9Njmn1/L32ltEi56gqPXQRF1eqKCWdSX9JSrXJkt3GsL4YmbNM/6Pf4pes4x+dxCanEvZjtvJCHqiUCEMrSP7ygpcVQX+q/kVn0xMTER9Ci1Xho47ZdY+o8r1+Pj4w/2hCtQigv4fhHbQaSNI0XheQwK/IlDr1A4zx/qpqd+SqpfjL0X6dC5Cj9GOulZ7MvrpcGhyDgQ9WUzLq4D668NqWTSO1E6yjLrzGkeG9n1rnbgdKJT6DYBPFZuO11/XVsB1n/bn+2NnVRih10oRGl6Bybho2Z5uP+gKdKjlVYH113gVdv0CTrd/UddfLWW70GdVj8T/YYvu0y1UkYVoXbyKeQntb9D7Mgn3F0EvEHPVGbVARXu6FX6NGctC7cuuntJua/Q6DzofmgBLpedpMq2qoYrOnV5PS2yM0X8j6C1Sd73sCjNq6fR/jUVjWhd1OU9OTtr2qjX67JqfKLvCTF4rV5iJkVW08/Pzlb5uuyLoiTTZU3bNuDwVfK2za2yu8anf4mXXoeuUbZs6Dt2qvmZcK/Qd6ZzHDh062Y8qj61er79bP6FAlWq1WiUZZdYdMICgAwYQdMAAgg4YQNABAwg6YABBBwwg6IABBB0wgKADBhB0wACCDhhA0AEDCDpgAEEHDCDogAEEHTCAoAMGEHQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIDWOed+AS3cOPlbAUbbAAAAAElFTkSuQmCC';

    class AdvButton extends Component {
        constructor() {
            super( ...arguments );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-button'];

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
            const { attributes, setAttributes, clientId } = this.props;
            setAttributes( { id: 'advgbbtn-' + clientId } );
        }

        render() {
            const listBorderStyles = [
                { label: __( 'None', 'advanced-gutenberg' ), value: 'none' },
                { label: __( 'Solid', 'advanced-gutenberg' ), value: 'solid' },
                { label: __( 'Dotted', 'advanced-gutenberg' ), value: 'dotted' },
                { label: __( 'Dashed', 'advanced-gutenberg' ), value: 'dashed' },
                { label: __( 'Double', 'advanced-gutenberg' ), value: 'double' },
                { label: __( 'Groove', 'advanced-gutenberg' ), value: 'groove' },
                { label: __( 'Ridge', 'advanced-gutenberg' ), value: 'ridge' },
                { label: __( 'Inset', 'advanced-gutenberg' ), value: 'inset' },
                { label: __( 'Outset', 'advanced-gutenberg' ), value: 'outset' },
            ];
            const {
                attributes,
                setAttributes,
                isSelected,
                className,
                clientId: blockID,
            } = this.props;
            const {
                id, align, url, urlOpenNewTab, title, text, bgColor, textColor, textSize,
                marginTop, marginRight, marginBottom, marginLeft,
                paddingTop, paddingRight, paddingBottom, paddingLeft,
                borderWidth, borderColor, borderRadius, borderStyle,
                hoverTextColor, hoverBgColor, hoverShadowColor, hoverShadowH, hoverShadowV, hoverShadowBlur, hoverShadowSpread,
                hoverOpacity, transitionSpeed, isPreview
            } = attributes;

            const isStyleSquared        = className.indexOf('-squared') > -1;
            const isStyleOutlined       = className.indexOf('-outlined') > -1;
            const isStyleSquaredOutline = className.indexOf('-squared-outline') > -1;
            const hoverColorSettings = [
                {
                    label: __( 'Background Color', 'advanced-gutenberg' ),
                    value: hoverBgColor,
                    onChange: ( value ) => setAttributes( { hoverBgColor: value === undefined ? '#2196f3' : value } ),
                },
                {
                    label: __( 'Text Color', 'advanced-gutenberg' ),
                    value: hoverTextColor,
                    onChange: ( value ) => setAttributes( { hoverTextColor: value === undefined ? '#fff' : value } ),
                },
                {
                    label: __( 'Shadow Color', 'advanced-gutenberg' ),
                    value: hoverShadowColor,
                    onChange: ( value ) => setAttributes( { hoverShadowColor: value === undefined ? '#ccc' : value } ),
                },
            ];

            if (isStyleSquaredOutline) {
                hoverColorSettings.shift();
                previewImageData = previewImageDataSquaredOutline;
            } else if (isStyleOutlined) {
                hoverColorSettings.shift();
                previewImageData = previewImageDataOutlined;
            } else if (isStyleSquared) {
                previewImageData = previewImageDataSquared;
            } else {
                previewImageData = previewImageDataDefault;
            }

            return (
                isPreview ?
                    <img alt={__('Advanced Button', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                    <Fragment>
                    <span className={`${className} align${align}`}
                          style={ { display: 'inline-block' } }
                    >
                        <RichText
                            tagName="span"
                            placeholder={ __( 'Add text…', 'advanced-gutenberg' ) }
                            value={ text }
                            onChange={ ( value ) => setAttributes( { text: value } ) }
                            allowedFormats={ [ 'core/bold', 'core/italic', 'core/strikethrough' ] }
                            isSelected={ isSelected }
                            className={ `wp-block-advgb-button_link ${id}` }
                            keepPlaceholderOnFocus
                        />
                    </span>
                    <style>
                        {`.${id} {
                        font-size: ${textSize}px;
                        color: ${textColor} !important;
                        background-color: ${bgColor} !important;
                        margin: ${marginTop}px ${marginRight}px ${marginBottom}px ${marginLeft}px;
                        padding: ${paddingTop}px ${paddingRight}px ${paddingBottom}px ${paddingLeft}px;
                        border-width: ${borderWidth}px !important;
                        border-color: ${borderColor} !important;
                        border-radius: ${borderRadius}px !important;
                        border-style: ${borderStyle} ${borderStyle !== 'none' && '!important'};
                    }
                    .${id}:hover {
                        color: ${hoverTextColor} !important;
                        background-color: ${hoverBgColor} !important;
                        box-shadow: ${hoverShadowH}px ${hoverShadowV}px ${hoverShadowBlur}px ${hoverShadowSpread}px ${hoverShadowColor};
                        transition: all ${transitionSpeed}s ease;
                        opacity: ${hoverOpacity/100}
                    }`}
                    </style>
                    <InspectorControls>
                        <PanelBody title={ __( 'Button link', 'advanced-gutenberg' ) }>
                            <BaseControl
                                label={ [
                                    __( 'Link URL', 'advanced-gutenberg' ),
                                    (url && <a href={ url || '#' } key="link_url" target="_blank" style={ { float: 'right' } }>
                                        { __( 'Preview', 'advanced-gutenberg' ) }
                                    </a>)
                                ] }
                            >
                                <URLInput
                                    value={url}
                                    onChange={ (value) => setAttributes( { url: value } ) }
                                    autoFocus={false}
                                    isFullWidth
                                    hasBorder
                                />
                            </BaseControl>
                            <ToggleControl
                                label={ __( 'Open in new tab', 'advanced-gutenberg' ) }
                                checked={ !!urlOpenNewTab }
                                onChange={ () => setAttributes( { urlOpenNewTab: !attributes.urlOpenNewTab } ) }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Text/Color', 'advanced-gutenberg' ) }>
                            <RangeControl
                                label={ __( 'Text size', 'advanced-gutenberg' ) }
                                value={ textSize || '' }
                                onChange={ ( size ) => setAttributes( { textSize: size } ) }
                                min={ 10 }
                                max={ 100 }
                                beforeIcon="editor-textcolor"
                                allowReset
                            />
                            {!isStyleOutlined && (
                                <AdvColorControl
                                    label={ __('Background Color', 'advanced-gutenberg') }
                                    value={ bgColor }
                                    onChange={ (value) => setAttributes( { bgColor: value } ) }
                                />
                            )}
                            <AdvColorControl
                                label={ __('Text Color', 'advanced-gutenberg') }
                                value={ textColor }
                                onChange={ (value) => setAttributes( { textColor: value } ) }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Border', 'advanced-gutenberg' ) } initialOpen={ false } >
                            {!isStyleSquared && (
                                <RangeControl
                                    label={ __( 'Border radius', 'advanced-gutenberg' ) }
                                    value={ borderRadius || '' }
                                    onChange={ ( value ) => setAttributes( { borderRadius: value } ) }
                                    min={ 0 }
                                    max={ 100 }
                                />
                            ) }
                            <SelectControl
                                label={ __( 'Border style', 'advanced-gutenberg' ) }
                                value={ borderStyle }
                                options={ listBorderStyles }
                                onChange={ ( value ) => setAttributes( { borderStyle: value } ) }
                            />
                            {borderStyle !== 'none' && (
                                <Fragment>
                                    <PanelColorSettings
                                        title={ __( 'Border Color', 'advanced-gutenberg' ) }
                                        initialOpen={ false }
                                        colorSettings={ [
                                            {
                                                label: __( 'Border Color', 'advanced-gutenberg' ),
                                                value: borderColor,
                                                onChange: ( value ) => setAttributes( { borderColor: value === undefined ? '#2196f3' : value } ),
                                            },
                                        ] }
                                    />
                                    <RangeControl
                                        label={ __( 'Border width', 'advanced-gutenberg' ) }
                                        value={ borderWidth || '' }
                                        onChange={ ( value ) => setAttributes( { borderWidth: value } ) }
                                        min={ 0 }
                                        max={ 100 }
                                    />
                                </Fragment>
                            ) }
                        </PanelBody>
                        <PanelBody title={ __( 'Margin', 'advanced-gutenberg' ) } initialOpen={ false } >
                            <RangeControl
                                label={ __( 'Margin top', 'advanced-gutenberg' ) }
                                value={ marginTop || '' }
                                onChange={ ( value ) => setAttributes( { marginTop: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Margin right', 'advanced-gutenberg' ) }
                                value={ marginRight || '' }
                                onChange={ ( value ) => setAttributes( { marginRight: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Margin bottom', 'advanced-gutenberg' ) }
                                value={ marginBottom || '' }
                                onChange={ ( value ) => setAttributes( { marginBottom: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Margin left', 'advanced-gutenberg' ) }
                                value={ marginLeft || '' }
                                onChange={ ( value ) => setAttributes( { marginLeft: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Padding', 'advanced-gutenberg' ) } initialOpen={ false } >
                            <RangeControl
                                label={ __( 'Padding top', 'advanced-gutenberg' ) }
                                value={ paddingTop || '' }
                                onChange={ ( value ) => setAttributes( { paddingTop: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding right', 'advanced-gutenberg' ) }
                                value={ paddingRight || '' }
                                onChange={ ( value ) => setAttributes( { paddingRight: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding bottom', 'advanced-gutenberg' ) }
                                value={ paddingBottom || '' }
                                onChange={ ( value ) => setAttributes( { paddingBottom: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                            <RangeControl
                                label={ __( 'Padding left', 'advanced-gutenberg' ) }
                                value={ paddingLeft || '' }
                                onChange={ ( value ) => setAttributes( { paddingLeft: value } ) }
                                min={ 0 }
                                max={ 100 }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Hover', 'advanced-gutenberg' ) } initialOpen={ false } >
                            <PanelColorSettings
                                title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ hoverColorSettings }
                            />
                            <PanelBody title={ __( 'Shadow', 'advanced-gutenberg' ) } initialOpen={ false }  >
                                <RangeControl
                                    label={ __('Opacity (%)', 'advanced-gutenberg') }
                                    value={ hoverOpacity }
                                    onChange={ ( value ) => setAttributes( { hoverOpacity: value } ) }
                                    min={ 0 }
                                    max={ 100 }
                                />
                                <RangeControl
                                    label={ __('Transition speed (ms)', 'advanced-gutenberg') }
                                    value={ transitionSpeed || '' }
                                    onChange={ ( value ) => setAttributes( { transitionSpeed: value } ) }
                                    min={ 0 }
                                    max={ 3000 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow H offset', 'advanced-gutenberg' ) }
                                    value={ hoverShadowH || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowH: value } ) }
                                    min={ -50 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow V offset', 'advanced-gutenberg' ) }
                                    value={ hoverShadowV || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowV: value } ) }
                                    min={ -50 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow blur', 'advanced-gutenberg' ) }
                                    value={ hoverShadowBlur || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowBlur: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow spread', 'advanced-gutenberg' ) }
                                    value={ hoverShadowSpread || '' }
                                    onChange={ ( value ) => setAttributes( { hoverShadowSpread: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                </Fragment>
            )
        }
    }

    const buttonBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path d="M19 7H5c-1.1 0-2 .9-2 2v6c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm0 8H5V9h14v6z"/>
        </svg>
    );
    const blockAttrs = {
        id: {
            type: 'string',
        },
        url: {
            type: 'string',
        },
        urlOpenNewTab: {
            type: 'boolean',
            default: true,
        },
        title: {
            type: 'string',
        },
        text: {
            source: 'children',
            selector: 'a',
            default: 'PUSH THE BUTTON',
        },
        bgColor: {
            type: 'string',
        },
        textColor: {
            type: 'string',
        },
        textSize: {
            type: 'number',
            default: 18,
        },
        marginTop: {
            type: 'number',
            default: 0,
        },
        marginRight: {
            type: 'number',
            default: 0,
        },
        marginBottom: {
            type: 'number',
            default: 0,
        },
        marginLeft: {
            type: 'number',
            default: 0,
        },
        paddingTop: {
            type: 'number',
            default: 10,
        },
        paddingRight: {
            type: 'number',
            default: 30,
        },
        paddingBottom: {
            type: 'number',
            default: 10,
        },
        paddingLeft: {
            type: 'number',
            default: 30,
        },
        borderWidth: {
            type: 'number',
            default: 1,
        },
        borderColor: {
            type: 'string',
        },
        borderStyle: {
            type: 'string',
            default: 'none',
        },
        borderRadius: {
            type: 'number',
            default: 50
        },
        hoverTextColor: {
            type: 'string',
        },
        hoverBgColor: {
            type: 'string',
        },
        hoverShadowColor: {
            type: 'string',
            default: '#ccc'
        },
        hoverShadowH: {
            type: 'number',
            default: 1,
        },
        hoverShadowV: {
            type: 'number',
            default: 1,
        },
        hoverShadowBlur: {
            type: 'number',
            default: 12,
        },
        hoverShadowSpread: {
            type: 'number',
            default: 0,
        },
        hoverOpacity: {
            type: 'number',
            default: 100,
        },
        transitionSpeed: {
            type: 'number',
            default: 200,
        },
        align: {
            type: 'string',
            default: 'none',
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

    registerBlockType( 'advgb/button', {
        title: __( 'Advanced Button', 'advanced-gutenberg' ),
        description: __( 'New button with more styles.', 'advanced-gutenberg' ),
        icon: {
            src: buttonBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'button', 'advanced-gutenberg' ), __( 'link', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        transforms: {
            from: [
                {
                    type: 'block',
                    blocks: [ 'core/button' ],
                    transform: ( attributes ) => {
                        return createBlock( 'advgb/button', {
                            ...attributes,
                            bgColor: attributes.color,
                        } )
                    }
                }
            ],
            to: [
                {
                    type: 'block',
                    blocks: [ 'core/button' ],
                    transform: ( attributes ) => {
                        return createBlock( 'core/button', {
                            ...attributes,
                            color: attributes.bgColor,
                        } )
                    }
                }
            ]
        },
        styles: [
            { name: 'default', label: __( 'Default', 'advanced-gutenberg' ), isDefault: true },
            { name: 'outlined', label: __( 'Outlined', 'advanced-gutenberg' ) },
            { name: 'squared', label: __( 'Squared', 'advanced-gutenberg' ) },
            { name: 'squared-outline', label: __( 'Squared Outline', 'advanced-gutenberg' ) },
        ],
        supports: {
            anchor: true,
            align: ['right', 'left', 'center', 'full'],
        },
        edit: AdvButton,
        save: function ( { attributes } ) {
            const {
                id,
                align,
                url,
                urlOpenNewTab,
                title,
                text,
            } = attributes;

            return (
                <div className={ `align${align}` }>
                    <RichText.Content
                        tagName="a"
                        className={ `wp-block-advgb-button_link ${id}` }
                        href={ url || '#' }
                        title={ title }
                        target={ !urlOpenNewTab ? '_self' : '_blank' }
                        value={ text }
                        rel="noopener noreferrer"
                    />
                </div>
            );
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
                attributes: {
                    ...blockAttrs,
                    transitionSpeed: {
                        type: 'number',
                        default: 0.2,
                    }
                },
                migrate: function( attributes ) {
                    const transitionSpeed = attributes.transitionSpeed * 1000;
                    return {
                        ...attributes,
                        transitionSpeed,
                    }
                },
                save: function ( { attributes } ) {
                    const {
                        id,
                        align,
                        url,
                        urlOpenNewTab,
                        title,
                        text,
                    } = attributes;

                    return (
                        <div className={ `align${align}` }>
                            <RichText.Content
                                tagName="a"
                                className={ `wp-block-advgb-button_link ${id}` }
                                href={ url || '#' }
                                title={ title }
                                target={ !urlOpenNewTab ? '_self' : '_blank' }
                                value={ text }
                                rel="noopener noreferrer"
                            />
                        </div>
                    );
                },
            },
        ],
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );
