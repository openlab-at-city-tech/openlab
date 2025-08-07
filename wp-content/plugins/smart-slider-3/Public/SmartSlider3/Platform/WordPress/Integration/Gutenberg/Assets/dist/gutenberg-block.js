(function (blocks, element, components, blockEditor) {
    var el = element.createElement,
        smartSliderIcon = wp.element.createElement('svg',
            {
                width: 20,
                height: 20
            },
            wp.element.createElement('path',
                {
                    d: "M10.25 2.04l9.33 4.41c.26.1.42.34.42.6 0 .26-.18.5-.45.58l-9.33 3.77a.67.67 0 0 1-.44 0L2 8.28v4.8c0 .5-.45.92-1 .92s-1-.42-1-.92V7.05c0-.26.16-.5.42-.6l9.34-4.4a.7.7 0 0 1 .49 0zm-.25 11c.34 0 2.04-.62 5.11-1.84.89-.34.89-.34.89.84v3.46c0 .3 0 .5-.45.68C12.52 17.39 10.66 18 10 18c-.66 0-2.52-.6-5.55-1.82C4 16 4 15.8 4 15.5V12c0-1.15 0-1.15.89-.81 3.07 1.22 4.77 1.84 5.11 1.84z"
                }
            )
        ),
        SelectSlider = function (props) {
            _N2.SelectSlider(n2_('Select Slider'), function (id, alias) {
                return props.setAttributes({
                    slider: alias || id
                });
            });
        },
        EditSlider = function (attributes) {
            window.open(window.gutenberg_smartslider3.slider_edit_url + attributes.slider, '_blank')
        };

    blocks.registerBlockType('nextend/smartslider3', {
        title: 'Smart Slider 3',
        description: n2_('Insert a slider into your content'),
        icon: smartSliderIcon,
        category: 'common',
        attributes: {
            preview: false,
            slider: {
                type: 'string'
            }
        },
        example: {
            attributes: {
                preview: true
            },
        },
        edit: function (props) {
            var attributes = props.attributes;

            return (
                el('div', null,
                    attributes.preview ? el('img', {
                            width: 500,
                            height: 250,
                            src: _N2._imageHelper.fixed('$ss3-admin$/images/ss3gutenbergblock.png')
                        }) :
                        attributes.slider ? el('div', {className: props.className},
                                el(element.RawHTML, null, window.gutenberg_smartslider3.template.replace(/\{\{\{slider\}\}\}/g, attributes.slider)),
                                blockEditor.BlockControls && components.ToolbarGroup && el(blockEditor.BlockControls, null,
                                    el(components.ToolbarGroup, {className: 'wp-block-nextend-smartslider3--toolbar-group'},
                                        el(components.ToolbarButton, {
                                            icon: 'insert',
                                            label: n2_('Select Slider'),
                                            onClick: function () {
                                                SelectSlider(props)
                                            },
                                            className: 'wp-block-nextend-smartslider3--toolbar-icon'
                                        }),
                                        el(components.ToolbarButton, {
                                            icon: 'edit-large',
                                            label: n2_('Edit Slider'),
                                            onClick: function () {
                                                EditSlider(attributes)
                                            },
                                            className: 'wp-block-nextend-smartslider3--toolbar-icon'
                                        }),
                                    ))) :
                            el('div', {className: props.className + ' components-placeholder is-large'},
                                el('div', {className: 'components-placeholder__label'},
                                    el('span', {className: 'block-editor-block-icon'},
                                        smartSliderIcon
                                    ),
                                    'Smart Slider'
                                ),
                                el('div', {className: 'components-placeholder__instructions'},
                                    n2_('Select the slider you want to insert.')
                                ),
                                el('div', {className: 'components-placeholder__fieldset'},
                                    el('button', {
                                            className: 'components-button is-primary is-button is-large',
                                            type: 'button',
                                            onClick: function () {
                                                SelectSlider(props)
                                            }
                                        },
                                        n2_('Select Slider')
                                    ),
                                ),
                                blockEditor.BlockControls && components.ToolbarGroup && el(blockEditor.BlockControls, null,
                                    el(components.ToolbarGroup, {className: 'wp-block-nextend-smartslider3--toolbar-group'},
                                        el(components.ToolbarButton, {
                                            icon: 'insert',
                                            label: n2_('Select Slider'),
                                            onClick: function () {
                                                SelectSlider(props)
                                            },
                                            className: 'wp-block-nextend-smartslider3--toolbar-icon'
                                        })
                                    )
                                )
                            ),
                    el(blockEditor.InspectorControls, null,
                        el(components.PanelBody, {
                                title: n2_('Slider')
                            },
                            el(
                                'div',
                                {className: 'wp-block-nextend-smartslider3__button-container'},
                                el('button', {
                                        className: 'components-button is-primary is-button is-large',
                                        type: 'button',
                                        onClick: function () {
                                            SelectSlider(props)
                                        }
                                    },
                                    n2_('Select Slider')
                                ),
                                attributes.slider ?
                                    el('button', {
                                            className: 'components-button is-secondary is-button is-large',
                                            type: 'button',
                                            onClick: function () {
                                                EditSlider(attributes)
                                            }
                                        },
                                        n2_('Edit')
                                    ) : null
                            ))
                    )
                )
            );
        },
        save: function (props) {
            var attributes = props.attributes;

            if (attributes.slider) {

                return el('div', {className: props.className}, '[smartslider3 slider="' + attributes.slider + '"]');
            }

            return null;
        },
        transforms: {
            from: [
                {
                    type: 'block',
                    blocks: ['core/legacy-widget'],
                    isMatch: ({idBase, instance}) => {
                        if (!instance?.raw) {
                            // Can't transform if raw instance is not shown in REST API.
                            return false;
                        }
                        return idBase === 'smartslider3';
                    },
                    transform: ({instance}) => {
                        return blocks.createBlock('nextend/smartslider3', {
                            slider: instance.raw.slider,
                        });
                    },
                },
            ]
        },
        deprecated: [
            {
                attributes: {
                    slider: {
                        type: 'string'
                    }
                },
                save: function (props) {
                    var attributes = props.attributes;
                    return (
                        attributes.slider && el('div', {className: props.className + ' gutenberg-smartslider3'}, '[smartslider3 slider="' + attributes.slider + '"]')
                    );
                }
            }
        ]
    });

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor || window.wp.editor
);