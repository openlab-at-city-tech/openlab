(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, PanelColorSettings } = wpBlockEditor;
    const { PanelBody, RangeControl, SelectControl, TextControl } = wpComponents;

    const contactBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0l-8 4.99L4 6h16zm0 12H4V8l8 5 8-5v10z"/>
        </svg>
    );

    const previewImageData = 'data:image/png;base64,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';

    class AdvContactForm extends Component {
        constructor() {
            super( ...arguments );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-contact-form'];

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
            const { attributes, setAttributes } = this.props;
            const {
                nameLabel,
                emailLabel,
                msgLabel,
                submitLabel,
                successLabel,
                alertLabel,
                bgColor,
                textColor,
                borderColor,
                borderStyle,
                borderRadius,
                submitColor,
                submitBgColor,
                submitRadius,
                submitPosition,
                isPreview
            } = attributes;

            return (
                isPreview ?
                    <img alt={__('Contact Form', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Form Settings', 'advanced-gutenberg' ) }>
                            {(typeof advgbBlocks !== 'undefined' && !parseInt(advgbBlocks.captchaEnabled)) && (
                                <PanelBody title={ __( 'Notice', 'advanced-gutenberg' ) }>
                                    <p style={ { fontStyle: 'italic' } }>
                                        { __( 'We strongly recommend to enable Google reCaptcha to avoid spam bot. You can enable it in Form Recaptcha in', 'advanced-gutenberg' ) }
                                        <a href={advgbBlocks.config_url + '#email-form'} target="_blank"> { __( 'settings', 'advanced-gutenberg' ) }.</a>
                                    </p>
                                </PanelBody>
                            ) }
                            <PanelBody title={ __( 'Email sender', 'advanced-gutenberg' ) } initialOpen={ false }>
                                <p style={ { fontStyle: 'italic' } }>
                                    { __('An email will be sent to the admin email (by default) whenever a contact form is submitted. You can change it in ', 'advanced-gutenberg') }
                                    <a href={advgbBlocks.config_url + '#settings'} target="_blank"> { __( 'settings', 'advanced-gutenberg' ) }.</a>
                                </p>
                            </PanelBody>
                            <PanelBody title={ __( 'Text Label', 'advanced-gutenberg' ) }>
                                <TextControl
                                    label={ __( 'Name input placeholder', 'advanced-gutenberg' ) }
                                    value={ nameLabel }
                                    onChange={ (value) => setAttributes( { nameLabel: value } ) }
                                />
                                <TextControl
                                    label={ __( 'Email input placeholder', 'advanced-gutenberg' ) }
                                    value={ emailLabel }
                                    onChange={ (value) => setAttributes( { emailLabel: value } ) }
                                />
                                <TextControl
                                    label={ __( 'Message input placeholder', 'advanced-gutenberg' ) }
                                    value={ msgLabel }
                                    onChange={ (value) => setAttributes( { msgLabel: value } ) }
                                />
                                <TextControl
                                    label={ __( 'Submit text', 'advanced-gutenberg' ) }
                                    value={ submitLabel }
                                    onChange={ (value) => setAttributes( { submitLabel: value } ) }
                                />
                                <TextControl
                                    label={ __( 'Empty field warning text', 'advanced-gutenberg' ) }
                                    value={ alertLabel }
                                    onChange={ (value) => setAttributes( { alertLabel: value } ) }
                                />
                                <TextControl
                                    label={ __( 'Submit success text', 'advanced-gutenberg' ) }
                                    value={ successLabel }
                                    onChange={ (value) => setAttributes( { successLabel: value } ) }
                                />
                            </PanelBody>
                            <PanelColorSettings
                                title={ __( 'Input Color', 'advanced-gutenberg' ) }
                                colorSettings={ [
                                    {
                                        label: __( 'Background color', 'advanced-gutenberg' ),
                                        value: bgColor,
                                        onChange: (value) => setAttributes( { bgColor: value } ),
                                    },
                                    {
                                        label: __( 'Text color', 'advanced-gutenberg' ),
                                        value: textColor,
                                        onChange: (value) => setAttributes( { textColor: value } ),
                                    },
                                ] }
                            />
                            <PanelBody title={ __( 'Border Settings', 'advanced-gutenberg' ) } initialOpen={ false }>
                                <PanelColorSettings
                                    title={ __( 'Border Color', 'advanced-gutenberg' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Border color', 'advanced-gutenberg' ),
                                            value: borderColor,
                                            onChange: (value) => setAttributes( { borderColor: value } ),
                                        },
                                    ] }
                                />
                                <SelectControl
                                    label={ __( 'Border Style', 'advanced-gutenberg' ) }
                                    value={ borderStyle }
                                    options={ [
                                        { label: __( 'Solid', 'advanced-gutenberg' ), value: 'solid' },
                                        { label: __( 'Dashed', 'advanced-gutenberg' ), value: 'dashed' },
                                        { label: __( 'Dotted', 'advanced-gutenberg' ), value: 'dotted' },
                                    ] }
                                    onChange={ (value) => setAttributes( { borderStyle: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Border radius (px)', 'advanced-gutenberg' ) }
                                    value={ borderRadius }
                                    onChange={ (value) => setAttributes( { borderRadius: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                            </PanelBody>
                            <PanelBody title={ __( 'Submit Button Settings', 'advanced-gutenberg' ) }>
                                <PanelColorSettings
                                    title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Border and Text', 'advanced-gutenberg' ),
                                            value: submitColor,
                                            onChange: (value) => setAttributes( { submitColor: value } ),
                                        },
                                        {
                                            label: __( 'Background', 'advanced-gutenberg' ),
                                            value: submitBgColor,
                                            onChange: (value) => setAttributes( { submitBgColor: value } ),
                                        },
                                    ] }
                                />
                                <RangeControl
                                    label={ __( 'Button border radius', 'advanced-gutenberg' ) }
                                    value={ submitRadius }
                                    onChange={ (value) => setAttributes( { submitRadius: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                                <SelectControl
                                    label={ __( 'Button position', 'advanced-gutenberg' ) }
                                    value={ submitPosition }
                                    options={ [
                                        { label: __( 'Center', 'advanced-gutenberg' ), value: 'center' },
                                        { label: __( 'Left', 'advanced-gutenberg' ), value: 'left' },
                                        { label: __( 'Right', 'advanced-gutenberg' ), value: 'right' },
                                    ] }
                                    onChange={ (value) => setAttributes( { submitPosition: value } ) }
                                />
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                    <div className="advgb-contact-form">
                        <div className="advgb-form-field advgb-form-field-half">
                            <input type="text" disabled={ true }
                                   className="advgb-form-input"
                                   value={ nameLabel ? nameLabel : 'Name' }
                                   style={ {
                                       backgroundColor: bgColor,
                                       color: textColor,
                                       borderColor: borderColor,
                                       borderStyle: borderStyle,
                                       borderRadius: borderRadius,
                                   } }
                            />
                        </div>
                        <div className="advgb-form-field advgb-form-field-half">
                            <input type="text" disabled={ true }
                                   className="advgb-form-input"
                                   value={ emailLabel ? emailLabel : 'Email address' }
                                   style={ {
                                       backgroundColor: bgColor,
                                       color: textColor,
                                       borderColor: borderColor,
                                       borderStyle: borderStyle,
                                       borderRadius: borderRadius,
                                   } }
                            />
                        </div>
                        <div className="advgb-form-field advgb-form-field-full">
                            <textarea className="advgb-form-input"
                                      disabled={ true }
                                      value={ msgLabel ? msgLabel : 'Message' }
                                      style={ {
                                          backgroundColor: bgColor,
                                          color: textColor,
                                          borderColor: borderColor,
                                          borderStyle: borderStyle,
                                          borderRadius: borderRadius,
                                      } }
                            />
                        </div>
                        <div className="advgb-form-submit-wrapper"
                             style={ { textAlign: submitPosition } }
                        >
                            <button className="advgb-form-submit"
                                    style={ {
                                        borderColor: submitColor,
                                        color: submitColor,
                                        backgroundColor: submitBgColor,
                                        borderRadius: submitRadius,
                                    } }
                            >
                                { submitLabel ? submitLabel : 'Submit' }
                            </button>
                        </div>
                    </div>
                </Fragment>
            )
        }
    }

    const contactBlockAttrs = {
        nameLabel: {
            type: 'string',
        },
        emailLabel: {
            type: 'string',
        },
        msgLabel: {
            type: 'string',
        },
        submitLabel: {
            type: 'string',
        },
        successLabel: {
            type: 'string',
        },
        alertLabel: {
            type: 'string',
        },
        bgColor: {
            type: 'string',
        },
        textColor: {
            type: 'string',
        },
        borderStyle: {
            type: 'string',
        },
        borderColor: {
            type: 'string',
        },
        borderRadius: {
            type: 'number',
        },
        submitColor: {
            type: 'string',
        },
        submitBgColor: {
            type: 'string',
        },
        submitRadius: {
            type: 'number',
        },
        submitPosition: {
            type: 'string',
            default: 'right',
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

    registerBlockType( 'advgb/contact-form', {
        title: __( 'Contact Form', 'advanced-gutenberg' ),
        description: __( 'Fastest way to create a contact form for your page.', 'advanced-gutenberg' ),
        icon: {
            src: contactBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'contact', 'advanced-gutenberg' ), __( 'form', 'advanced-gutenberg' ) ],
        attributes: contactBlockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            anchor: true
        },
        edit: AdvContactForm,
        save: function ( { attributes } ) {
            const {
                nameLabel,
                emailLabel,
                msgLabel,
                submitLabel,
                successLabel,
                alertLabel,
                bgColor,
                textColor,
                borderColor,
                borderStyle,
                borderRadius,
                submitColor,
                submitBgColor,
                submitRadius,
                submitPosition,
            } = attributes;

            return (
                <div className="advgb-contact-form">
                    <form method="POST">
                        <div className="advgb-form-field advgb-form-field-half">
                            <input type="text"
                                   className="advgb-form-input advgb-form-input-name"
                                   placeholder={ nameLabel ? nameLabel : 'Name' }
                                   name="contact_name"
                                   style={ {
                                       backgroundColor: bgColor,
                                       color: textColor,
                                       borderColor: borderColor,
                                       borderStyle: borderStyle,
                                       borderRadius: borderRadius,
                                   } }
                            />
                        </div>
                        <div className="advgb-form-field advgb-form-field-half">
                            <input type="email"
                                   className="advgb-form-input advgb-form-input-email"
                                   placeholder={ emailLabel ? emailLabel : 'Email address' }
                                   name="contact_email"
                                   style={ {
                                       backgroundColor: bgColor,
                                       color: textColor,
                                       borderColor: borderColor,
                                       borderStyle: borderStyle,
                                       borderRadius: borderRadius,
                                   } }
                            />
                        </div>
                        <div className="advgb-form-field advgb-form-field-full">
                            <textarea className="advgb-form-input advgb-form-input-msg"
                                      placeholder={ msgLabel ? msgLabel : 'Message' }
                                      name="contact_message"
                                      style={ {
                                          backgroundColor: bgColor,
                                          color: textColor,
                                          borderColor: borderColor,
                                          borderStyle: borderStyle,
                                          borderRadius: borderRadius,
                                      } }
                            />
                        </div>
                        <div className={`advgb-grecaptcha clearfix position-${submitPosition}`}/>
                        <div className="advgb-form-submit-wrapper"
                             style={ { textAlign: submitPosition } }
                        >
                            <button className="advgb-form-submit"
                                    type="submit"
                                    data-success={ successLabel ? successLabel : undefined }
                                    data-alert={ alertLabel ? alertLabel : undefined }
                                    style={ {
                                        borderColor: submitColor,
                                        color: submitColor,
                                        backgroundColor: submitBgColor,
                                        borderRadius: submitRadius,
                                    } }
                            >
                                { submitLabel ? submitLabel : 'Submit' }
                            </button>
                        </div>
                    </form>
                </div>
            );
        },
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );