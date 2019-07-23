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
            } = attributes;

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody title={ __( 'Newsletter Settings' ) }>
                            {(typeof advgbBlocks !== 'undefined' && !parseInt(advgbBlocks.captchaEnabled)) && (
                                <PanelBody title={ __( 'Notice' ) }>
                                    <p style={ { fontStyle: 'italic' } }>
                                        { __( 'We strongly recommend to enable Google reCaptcha to avoid spam bot. You can enable it in Form Recaptcha in' ) }
                                        <a href={advgbBlocks.config_url + '#email-form'} target="_blank"> { __( 'settings' ) }.</a>
                                    </p>
                                </PanelBody>
                            ) }
                            <PanelBody title={ __( 'Form Settings' ) }>
                                <SelectControl
                                    label={ __( 'Form style' ) }
                                    value={ formStyle }
                                    options={ [
                                        { label: __( 'Default' ), value: 'default' },
                                        { label: __( 'Alternative' ), value: 'alt' },
                                    ] }
                                    onChange={ (value) => setAttributes( { formStyle: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Form width (px)' ) }
                                    value={ formWidth }
                                    onChange={ (value) => setAttributes( { formWidth: value } ) }
                                    min={ 200 }
                                    max={ 1000 }
                                />
                            </PanelBody>
                            <PanelBody title={ __( 'Text Label' ) }>
                                {formStyle === 'alt' && (
                                    <Fragment>
                                        <TextControl
                                            label={ __( 'First Name input placeholder' ) }
                                            value={ fnameLabel }
                                            onChange={ (value) => setAttributes( { fnameLabel: value } ) }
                                        />
                                        <TextControl
                                            label={ __( 'Last Name input placeholder' ) }
                                            value={ lnameLabel }
                                            onChange={ (value) => setAttributes( { lnameLabel: value } ) }
                                        />
                                    </Fragment>
                                ) }
                                <TextControl
                                    label={ __( 'Email input placeholder' ) }
                                    value={ emailLabel }
                                    onChange={ (value) => setAttributes( { emailLabel: value } ) }
                                />
                                <TextControl
                                    label={ __( 'Submit text' ) }
                                    value={ submitLabel }
                                    onChange={ (value) => setAttributes( { submitLabel: value } ) }
                                />
                                <TextControl
                                    label={ __( 'Empty field warning text' ) }
                                    value={ alertLabel }
                                    onChange={ (value) => setAttributes( { alertLabel: value } ) }
                                />
                                <TextControl
                                    label={ __( 'Submit success text' ) }
                                    value={ successLabel }
                                    onChange={ (value) => setAttributes( { successLabel: value } ) }
                                />
                            </PanelBody>
                            <PanelColorSettings
                                title={ __( 'Input Color' ) }
                                colorSettings={ [
                                    {
                                        label: __( 'Background color' ),
                                        value: bgColor,
                                        onChange: (value) => setAttributes( { bgColor: value } ),
                                    },
                                    {
                                        label: __( 'Text color' ),
                                        value: textColor,
                                        onChange: (value) => setAttributes( { textColor: value } ),
                                    },
                                ] }
                            />
                            <PanelBody title={ __( 'Border Settings' ) } initialOpen={ false }>
                                <PanelColorSettings
                                    title={ __( 'Border Color' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Border color' ),
                                            value: borderColor,
                                            onChange: (value) => setAttributes( { borderColor: value } ),
                                        },
                                    ] }
                                />
                                <SelectControl
                                    label={ __( 'Border Style' ) }
                                    value={ borderStyle }
                                    options={ [
                                        { label: __( 'Solid' ), value: 'solid' },
                                        { label: __( 'Dashed' ), value: 'dashed' },
                                        { label: __( 'Dotted' ), value: 'dotted' },
                                    ] }
                                    onChange={ (value) => setAttributes( { borderStyle: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Border radius (px)' ) }
                                    value={ borderRadius }
                                    onChange={ (value) => setAttributes( { borderRadius: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                            </PanelBody>
                            <PanelBody title={ __( 'Submit Button Settings' ) }>
                                <PanelColorSettings
                                    title={ __( 'Color Settings' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Border and Text' ),
                                            value: submitColor,
                                            onChange: (value) => setAttributes( { submitColor: value } ),
                                        },
                                        {
                                            label: __( 'Background' ),
                                            value: submitBgColor,
                                            onChange: (value) => setAttributes( { submitBgColor: value } ),
                                        },
                                    ] }
                                />
                                <RangeControl
                                    label={ __( 'Button border radius' ) }
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
                                       value={ emailLabel ? emailLabel : __( 'Email address' ) }
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
                                        { submitLabel ? submitLabel : __( 'Submit' ) }
                                    </button>
                                </div>
                            </div>
                        ) }

                        {formStyle === 'alt' && (
                            <Fragment>
                                <div className="advgb-form-field advgb-form-field-full">
                                    <input type="text" disabled={ true }
                                           className="advgb-form-input"
                                           value={ fnameLabel ? fnameLabel : __( 'First Name' ) }
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
                                           value={ lnameLabel ? lnameLabel : __( 'Last Name' ) }
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
                                           value={ emailLabel ? emailLabel : __( 'Email address' ) }
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
                                        { submitLabel ? submitLabel : __( 'Submit' ) }
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
        title: __( 'Newsletter' ),
        description: __( 'Fastest way to create a newsletter form for your page.' ),
        icon: {
            src: newsletterBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'newsletter' ), __( 'form' ), __( 'email' ) ],
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
            }
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
                                       placeholder={ emailLabel ? emailLabel : __( 'Email address' ) }
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
                                        { submitLabel ? submitLabel : __( 'Submit' ) }
                                    </button>
                                </div>
                            </div>
                        ) }

                        {formStyle === 'alt' && (
                            <Fragment>
                                <div className="advgb-form-field advgb-form-field-full">
                                    <input type="text"
                                           className="advgb-form-input advgb-form-input-fname"
                                           placeholder={ fnameLabel ? fnameLabel : __( 'First Name' ) }
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
                                           placeholder={ lnameLabel ? lnameLabel : __( 'Last Name' ) }
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
                                           placeholder={ emailLabel ? emailLabel : __( 'Email address' ) }
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
                                        { submitLabel ? submitLabel : __( 'Submit' ) }
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