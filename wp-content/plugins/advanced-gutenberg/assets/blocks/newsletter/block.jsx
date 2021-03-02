(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, PanelColorSettings } = wpBlockEditor;
    const { PanelBody, RangeControl, SelectControl, TextControl } = wpComponents;

    const newsletterBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path fill-opacity=".9" d="M12 1.95c-5.52 0-10 4.48-10 10s4.48 10 10 10h5v-2h-5c-4.34 0-8-3.66-8-8s3.66-8 8-8 8 3.66 8 8v1.43c0 .79-.71 1.57-1.5 1.57s-1.5-.78-1.5-1.57v-1.43c0-2.76-2.24-5-5-5s-5 2.24-5 5 2.24 5 5 5c1.38 0 2.64-.56 3.54-1.47.65.89 1.77 1.47 2.96 1.47 1.97 0 3.5-1.6 3.5-3.57v-1.43c0-5.52-4.48-10-10-10zm0 13c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
        </svg>
    );

    const previewImageData = 'data:image/png;base64,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';

    class AdvNewsletter extends Component {
        constructor() {
            super( ...arguments );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-newsletter'];

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
                formStyle,
                formWidth,
                fnameLabel,
                lnameLabel,
                emailLabel,
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
                isPreview
            } = attributes;

            return (
                isPreview ?
                    <img alt={__('Newsletter', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Newsletter Settings', 'advanced-gutenberg' ) }>
                            {(typeof advgbBlocks !== 'undefined' && !parseInt(advgbBlocks.captchaEnabled)) && (
                                <PanelBody title={ __( 'Notice', 'advanced-gutenberg' ) }>
                                    <p style={ { fontStyle: 'italic' } }>
                                        { __( 'We strongly recommend to enable Google reCaptcha to avoid spam bot. You can enable it in Form Recaptcha in', 'advanced-gutenberg' ) }
                                        <a href={advgbBlocks.config_url + '#email-form'} target="_blank"> { __( 'settings', 'advanced-gutenberg' ) }.</a>
                                    </p>
                                </PanelBody>
                            ) }
                            <PanelBody title={ __( 'Form Settings', 'advanced-gutenberg' ) }>
                                <SelectControl
                                    label={ __( 'Form style', 'advanced-gutenberg' ) }
                                    value={ formStyle }
                                    options={ [
                                        { label: __( 'Default', 'advanced-gutenberg' ), value: 'default' },
                                        { label: __( 'Alternative', 'advanced-gutenberg' ), value: 'alt' },
                                    ] }
                                    onChange={ (value) => setAttributes( { formStyle: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Form width (px)', 'advanced-gutenberg' ) }
                                    value={ formWidth }
                                    onChange={ (value) => setAttributes( { formWidth: value } ) }
                                    min={ 200 }
                                    max={ 1000 }
                                />
                            </PanelBody>
                            <PanelBody title={ __( 'Text Label', 'advanced-gutenberg' ) }>
                                {formStyle === 'alt' && (
                                    <Fragment>
                                        <TextControl
                                            label={ __( 'First Name input placeholder', 'advanced-gutenberg' ) }
                                            value={ fnameLabel }
                                            onChange={ (value) => setAttributes( { fnameLabel: value } ) }
                                        />
                                        <TextControl
                                            label={ __( 'Last Name input placeholder', 'advanced-gutenberg' ) }
                                            value={ lnameLabel }
                                            onChange={ (value) => setAttributes( { lnameLabel: value } ) }
                                        />
                                    </Fragment>
                                ) }
                                <TextControl
                                    label={ __( 'Email input placeholder', 'advanced-gutenberg' ) }
                                    value={ emailLabel }
                                    onChange={ (value) => setAttributes( { emailLabel: value } ) }
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
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                    <div className="advgb-newsletter-wrapper">
                        <div className={ `advgb-newsletter clearfix style-${formStyle}` } style={ { maxWidth: formWidth } }>
                        {formStyle === 'default' && (
                            <div className="advgb-form-field">
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
                                <div className="advgb-form-submit-wrapper">
                                    <button className="advgb-form-submit"
                                            type="button"
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
                        ) }

                        {formStyle === 'alt' && (
                            <Fragment>
                                <div className="advgb-form-field advgb-form-field-full">
                                    <input type="text" disabled={ true }
                                           className="advgb-form-input"
                                           value={ fnameLabel ? fnameLabel : __( 'First Name', 'advanced-gutenberg' ) }
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
                                    <input type="text" disabled={ true }
                                           className="advgb-form-input"
                                           value={ lnameLabel ? lnameLabel : __( 'Last Name', 'advanced-gutenberg' ) }
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
                                <div className="advgb-form-submit-wrapper">
                                    <button className="advgb-form-submit"
                                            type="button"
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
                            </Fragment>
                        ) }
                        </div>
                    </div>
                </Fragment>
            )
        }
    }

    registerBlockType( 'advgb/newsletter', {
        title: __( 'Newsletter', 'advanced-gutenberg' ),
        description: __( 'Fastest way to create a newsletter form for your page.', 'advanced-gutenberg' ),
        icon: {
            src: newsletterBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'newsletter', 'advanced-gutenberg' ), __( 'form', 'advanced-gutenberg' ), __( 'email', 'advanced-gutenberg' ) ],
        attributes: {
            formStyle: {
                type: 'string',
                default: 'default',
            },
            formWidth: {
                type: 'number',
                default: 400,
            },
            fnameLabel: {
                type: 'string',
            },
            lnameLabel: {
                type: 'string',
            },
            emailLabel: {
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
        edit: AdvNewsletter,
        save: function ( { attributes } ) {
            const {
                formStyle,
                formWidth,
                fnameLabel,
                lnameLabel,
                emailLabel,
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
            } = attributes;

            return (
                <div className={`advgb-newsletter clearfix style-${formStyle}`} style={ { maxWidth: formWidth } }>
                    <form method="POST" className="clearfix">
                        {formStyle === 'default' && (
                            <div className="advgb-form-field">
                                <input type="email"
                                       className="advgb-form-input advgb-form-input-email"
                                       placeholder={ emailLabel ? emailLabel : 'Email address' }
                                       style={ {
                                           backgroundColor: bgColor,
                                           color: textColor,
                                           borderColor: borderColor,
                                           borderStyle: borderStyle,
                                           borderRadius: borderRadius,
                                       } }
                                />
                                <div className="advgb-form-submit-wrapper">
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
                            </div>
                        ) }

                        {formStyle === 'alt' && (
                            <Fragment>
                                <div className="advgb-form-field advgb-form-field-full">
                                    <input type="text"
                                           className="advgb-form-input advgb-form-input-fname"
                                           placeholder={ fnameLabel ? fnameLabel : __( 'First Name', 'advanced-gutenberg' ) }
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
                                    <input type="text"
                                           className="advgb-form-input advgb-form-input-lname"
                                           placeholder={ lnameLabel ? lnameLabel : __( 'Last Name', 'advanced-gutenberg' ) }
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
                                    <input type="email"
                                           className="advgb-form-input advgb-form-input-email"
                                           placeholder={ emailLabel ? emailLabel : 'Email address' }
                                           style={ {
                                               backgroundColor: bgColor,
                                               color: textColor,
                                               borderColor: borderColor,
                                               borderStyle: borderStyle,
                                               borderRadius: borderRadius,
                                           } }
                                    />
                                </div>
                                <div className="advgb-form-submit-wrapper">
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
                            </Fragment>
                        ) }
                        <div className="advgb-grecaptcha clearfix"/>
                    </form>
                </div>
            );
        }
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );