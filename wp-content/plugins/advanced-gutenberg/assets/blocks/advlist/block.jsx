(function (wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const {__} = wpI18n;
    const {Component, Fragment} = wpElement;
    const {registerBlockType, createBlock} = wpBlocks;
    const {InspectorControls, RichText, ColorPalette, BlockControls} = wpBlockEditor;
    const {BaseControl, RangeControl, PanelBody, Dashicon, ToolbarGroup, ToolbarButton} = wpComponents;

    var parse = require('html-react-parser');

    const previewImageData = 'data:image/png;base64,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';

    class AdvList extends Component {
        constructor() {
            super(...arguments);
        }

        componentWillMount() {
            const {attributes, setAttributes} = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-list'];

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
                setAttributes({changed: true});
            }
        }

        componentDidMount() {
            const {attributes, setAttributes, clientId} = this.props;

            if (typeof attributes.values[0] !== 'undefined') {
                if (typeof attributes.values[0] === 'string' && attributes.values[0] !== '') {
                    setAttributes({
                        values: parse(attributes.values[0])
                    })
                }
            }
            if (!attributes.id) {
                setAttributes({
                    id: 'advgblist-' + clientId
                })
            }
        }

        render() {
            const listIcons = [
                {label: __('None', 'advanced-gutenberg'), value: ''},
                {label: __('Pushpin', 'advanced-gutenberg'), value: 'admin-post'},
                {label: __('Configuration', 'advanced-gutenberg'), value: 'admin-generic'},
                {label: __('Flag', 'advanced-gutenberg'), value: 'flag'},
                {label: __('Star', 'advanced-gutenberg'), value: 'star-filled'},
                {label: __('Checkmark', 'advanced-gutenberg'), value: 'yes'},
                {label: __('Minus', 'advanced-gutenberg'), value: 'minus'},
                {label: __('Plus', 'advanced-gutenberg'), value: 'plus'},
                {label: __('Play', 'advanced-gutenberg'), value: 'controls-play'},
                {label: __('Arrow right', 'advanced-gutenberg'), value: 'arrow-right-alt'},
                {label: __('X Cross', 'advanced-gutenberg'), value: 'dismiss'},
                {label: __('Warning', 'advanced-gutenberg'), value: 'warning'},
                {label: __('Help', 'advanced-gutenberg'), value: 'editor-help'},
                {label: __('Info', 'advanced-gutenberg'), value: 'info'},
                {label: __('Circle', 'advanced-gutenberg'), value: 'marker'},
            ];
            const {
                attributes,
                isSelected,
                insertBlocksAfter,
                mergeBlocks,
                setAttributes,
                onReplace,
                className,
                clientId: blockID,
            } = this.props;
            const {
                id,
                values,
                icon,
                iconSize,
                iconColor,
                margin,
                padding,
                lineHeight,
                fontSize,
                isPreview,
            } = attributes;
            const listClassName = [
                className,
                id,
                icon && 'advgb-list',
                icon && `advgb-list-${icon}`
            ].filter(Boolean).join(' ');
            const size = typeof iconSize != 'undefined' ? parseInt(iconSize) : 16;
            const marg = typeof margin != 'undefined' ? parseInt(margin) : 2;
            const padd = typeof padding != 'undefined' ? parseInt(padding) * 2 : 4;
            return (
                isPreview ?
                    <img alt={__('Advanced List', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                    <Fragment>
                        <BlockControls>
                            <ToolbarGroup>
                                <ToolbarButton
                                    label={__('Refresh this list when it conflict with other lists styles', 'advanced-gutenberg')}
                                    icon="update"
                                    onClick={() => setAttributes({id: 'advgblist-' + blockID})}
                                />
                            </ToolbarGroup>
                        </BlockControls>
                        <InspectorControls>
                            <PanelBody title={__('Text Settings', 'advanced-gutenberg')} initialOpen={false}>
                                <RangeControl
                                    label={__('Text size', 'advanced-gutenberg')}
                                    value={fontSize || ''}
                                    onChange={(size) => setAttributes({fontSize: size})}
                                    min={10}
                                    max={100}
                                    beforeIcon="editor-textcolor"
                                    allowReset
                                />
                            </PanelBody>
                            <PanelBody title={__('Icon Settings', 'advanced-gutenberg')}>
                                <BaseControl label={__('List icon', 'advanced-gutenberg')}>
                                    <div className="advgb-icon-items-wrapper">
                                        {listIcons.map((item, index) => (
                                            <div className="advgb-icon-item h20" key={index}>
                                            <span onClick={() => setAttributes({icon: item.value})}
                                                  className={[
                                                      item.value === icon && 'active',
                                                      item.value === '' && 'remove-icon',
                                                  ].filter(Boolean).join(' ')}
                                            >
                                                <Dashicon icon={item.value}/>
                                            </span>
                                            </div>
                                        ))}
                                    </div>
                                </BaseControl>
                                {icon && (
                                    <Fragment>
                                        <PanelBody
                                            title={[
                                                __('Icon color', 'advanced-gutenberg'),
                                                <span key="advgb-list-icon-color"
                                                      className={`dashicons dashicons-${icon}`}
                                                      style={{color: iconColor, marginLeft: '10px'}}/>
                                            ]}
                                            initialOpen={false}
                                        >
                                            <ColorPalette
                                                value={iconColor}
                                                onChange={(value) => setAttributes({iconColor: value === undefined ? '#000' : value})}
                                            />
                                        </PanelBody>
                                        <RangeControl
                                            label={__('Icon size', 'advanced-gutenberg')}
                                            value={iconSize || ''}
                                            onChange={(size) => setAttributes({iconSize: size})}
                                            min={10}
                                            max={100}
                                            allowReset
                                        />
                                        <RangeControl
                                            label={__('Line height', 'advanced-gutenberg')}
                                            value={lineHeight || ''}
                                            onChange={(size) => setAttributes({lineHeight: size})}
                                            min={0}
                                            max={100}
                                            allowReset
                                        />
                                        <RangeControl
                                            label={__('Margin', 'advanced-gutenberg')}
                                            value={margin || ''}
                                            onChange={(size) => setAttributes({margin: size})}
                                            min={0}
                                            max={100}
                                            allowReset
                                        />
                                        <RangeControl
                                            label={__('Padding', 'advanced-gutenberg')}
                                            value={padding || ''}
                                            onChange={(size) => setAttributes({padding: size})}
                                            min={0}
                                            max={100}
                                            allowReset
                                        />
                                    </Fragment>
                                )}
                            </PanelBody>
                        </InspectorControls>
                        <RichText
                            multiline="li"
                            tagName="ul"
                            onChange={(value) => setAttributes({values: value})}
                            value={values}
                            className={listClassName}
                            placeholder={__('Write advanced list…', 'advanced-gutenberg')}
                            onMerge={mergeBlocks}
                            unstableOnSplit={
                                insertBlocksAfter ?
                                    (before, after, ...blocks) => {
                                        if (!blocks.length) {
                                            blocks.push(createBlock('core/paragraph'));
                                        }

                                        if (after.length) {
                                            blocks.push(createBlock('advgb/list', {
                                                ...attributes,
                                                values: after,
                                                id: undefined,
                                            }));
                                        }

                                        setAttributes({values: before});
                                        insertBlocksAfter(blocks);
                                    } :
                                    undefined
                            }
                            onRemove={() => onReplace([])}
                            isSelected={isSelected}
                        />
                        <div>
                            <style>
                                {`.${id} li { font-size: ${fontSize}px; margin-left: ${size + padd}px }`}
                            </style>
                            {icon &&
                            <style>
                                {`.${id} li:before {
                                font-size: ${iconSize}px;
                                color: ${iconColor};
                                line-height: ${lineHeight}px;
                                margin: ${margin}px;
                                padding: ${padding}px;
                                margin-left: -${size + padd + marg}px
                            }`}
                            </style>
                            }
                        </div>
                    </Fragment>
            )
        }
    }

    const listBlockIcon = (
        <svg height="20" viewBox="2 2 22 22" width="20" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    );

    const listBlockAttrs = {
        id: {
            type: 'string'
        },
        icon: {
            type: 'string'
        },
        iconSize: {
            type: 'number',
            default: 16,
        },
        iconColor: {
            type: 'string',
            default: '#000',
        },
        fontSize: {
            type: 'number',
            default: 16,
        },
        lineHeight: {
            type: 'number',
            default: 18,
        },
        margin: {
            type: 'number',
            default: 2,
        },
        padding: {
            type: 'number',
            default: 2,
        },
        values: {
            type: 'array',
            source: 'children',
            selector: 'ul',
            default: [],
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

    registerBlockType('advgb/list', {
        title: __('Advanced List', 'advanced-gutenberg'),
        description: __('List block with custom icons and styles.', 'advanced-gutenberg'),
        icon: {
            src: listBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [__('list', 'advanced-gutenberg'), __('icon', 'advanced-gutenberg')],
        attributes: listBlockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        transforms: {
            from: [
                {
                    type: 'block',
                    blocks: ['core/list'],
                    transform: ({values}) => {
                        return createBlock('advgb/list', {
                            values: parse(values),
                            icon: 'controls-play',
                            iconColor: '#ff0000',
                        })
                    }
                }
            ],
            to: [
                {
                    type: 'block',
                    blocks: ['core/list'],
                    transform: ({values}) => {
                        return createBlock('core/list', {
                            nodeName: 'UL',
                            values: values,
                        })
                    }
                }
            ]
        },
        merge(attributes, attributesToMerge) {
            const valuesToMerge = attributesToMerge.values || [];

            // Standard text-like block attribute.
            if (attributesToMerge.content) {
                valuesToMerge.push(attributesToMerge.content);
            }

            return {
                ...attributes,
                values: [
                    ...attributes.values,
                    ...valuesToMerge,
                ],
            };
        },
        supports: {
            anchor: true,
        },
        edit: AdvList,
        save: function ({attributes}) {
            const {
                id,
                values,
                icon,
            } = attributes;
            const listClassName = [
                id,
                icon && 'advgb-list',
                icon && `advgb-list-${icon}`
            ].filter(Boolean).join(' ');

            return <div>
                <ul className={listClassName}>
                    {values}
                </ul>
            </div>
        },
    });
})(wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components);