import {AdvColorControl} from "../0-adv-components/components.jsx";

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, RichText, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const { RangeControl, PanelBody, TextControl , SelectControl, ToggleControl, Tooltip, ToolbarGroup, ToolbarButton, Placeholder } = wpComponents;

    const userIcon = (
        <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    );
    const emailIcon = (
        <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10h5v-2h-5c-4.34 0-8-3.66-8-8s3.66-8 8-8 8 3.66 8 8v1.43c0 .79-.71 1.57-1.5 1.57s-1.5-.78-1.5-1.57V12c0-2.76-2.24-5-5-5s-5 2.24-5 5 2.24 5 5 5c1.38 0 2.64-.56 3.54-1.47.65.89 1.77 1.47 2.96 1.47 1.97 0 3.5-1.6 3.5-3.57V12c0-5.52-4.48-10-10-10zm0 13c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
            <path fill="none" d="M0 0h24v24H0z"/>
        </svg>
    );
    const passwordIcon = (
        <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <g fill="none">
                <path d="M0 0h24v24H0V0z"/>
                <path opacity=".87" d="M0 0h24v24H0V0z"/>
            </g>
            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
        </svg>
    );

    const previewImageData = 'data:image/png;base64,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';

    class LoginFormEdit extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                registerView: false,
            }
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-login-form'];

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

            // Change view to Register form regard to initial form option
            this.setState( { registerView: attributes.formType === 'register' } );
        }

        componentDidMount() {
            const { clientId, attributes, setAttributes } = this.props;
            const { submitButtonId } = attributes;

            if (!submitButtonId) {
                setAttributes( { submitButtonId: `advgb-submit-btn-${clientId}` } )
            }
        }

        render() {
            const { registerView } = this.state;
            const { attributes, setAttributes } = this.props;
            const {
                formType, formWidth, redirect, redirectLink, showLogo, showInputFieldIcon,
                showRegisterLink, showLostPasswordLink, logoImg, logoID, registerLogoImg, registerLogoID,
                logoWidth, welcomeText, loginLabel, loginText, passwordText, usernameLabel, userText,
                emailLabel, emailText, rememberMeText, loginSubmitLabel, registerSubmitLabel,
                registerText, registerLinkText, registerWelcome, backToLoginText, lostPasswordText,
                headerBgColor, bgColor, textColor, inputColor, borderColor, borderStyle, borderWidth,
                submitColor, submitBgColor, submitRadius, submitPosition, submitButtonId,
                submitHoverColor, submitHoverBgColor, submitHoverShadow, submitHoverShadowH, submitHoverShadowV,
                submitHoverShadowBlur, submitHoverShadowSpread, submitHoverOpacity, submitHoverTranSpeed,
                isPreview,
            } = attributes;

            const logoElm = (
                <MediaUpload
                    allowedTypes={ ["image"] }
                    onSelect={ (media) => setAttributes( {
                        logoImg: media.sizes.medium ? media.sizes.medium.url : media.sizes.full.url,
                        logoID: media.id
                    } ) }
                    value={ logoID }
                    render={ ( { open } ) => (
                        <div className="advgb-lores-form-logo-wrapper">
                            <Tooltip text={ __( 'Click to change logo', 'advanced-gutenberg' ) }>
                                <span style={ {
                                    display: 'block',
                                } }>
                                    <img className="advgb-lores-form-logo"
                                         onClick={ open }
                                         src={ logoImg }
                                         alt={ 'Site logo' }
                                         style={ {
                                             width: logoWidth ? logoWidth + 'px' : undefined,
                                             cursor: 'pointer',
                                         } }
                                    />
                                </span>
                            </Tooltip>
                        </div>
                    ) }
                />
            );

            const regLogoElm = (
                <MediaUpload
                    allowedTypes={ ["image"] }
                    onSelect={ (media) => setAttributes( {
                        registerLogoImg: media.sizes.medium ? media.sizes.medium.url : media.sizes.full.url,
                        registerLogoID: media.id
                    } ) }
                    value={ registerLogoID }
                    render={ ( { open } ) => (
                        <div className="advgb-lores-form-logo-wrapper">
                            <Tooltip text={ __( 'Click to change logo', 'advanced-gutenberg' ) }>
                                <span style={ {
                                    display: 'block',
                                } }>
                                    <img className="advgb-lores-form-logo"
                                         onClick={ open }
                                         src={ registerLogoImg }
                                         alt={ 'Site logo' }
                                         style={ {
                                             width: logoWidth ? logoWidth + 'px' : undefined,
                                             cursor: 'pointer',
                                         } }
                                    />
                                </span>
                            </Tooltip>
                        </div>
                    ) }
                />
            );

            const loginForm = (
                <div className="advgb-login-form-wrapper advgb-lores-form">
                    {!!showRegisterLink && (
                        <div className="advgb-register-link-wrapper advgb-header-navigation"
                             style={ { backgroundColor: headerBgColor } }
                        >
                            <RichText
                                tagName="span"
                                value={ registerText }
                                className="advgb-register-text"
                                onChange={ (value) => setAttributes( { registerText: value.trim() } ) }
                                style={ { color: textColor } }
                                onReplace={ () => null }
                                onSplit={ () => null }
                                placeholder={ __( 'Text…', 'advanced-gutenberg' ) }
                                keepPlaceholderOnFocus
                            />
                            <RichText
                                tagName="a"
                                value={ registerLinkText }
                                className="advgb-register-link"
                                onChange={ (value) => setAttributes( { registerLinkText: value.trim() } ) }
                                style={ { color: submitBgColor } }
                                onReplace={ () => null }
                                onSplit={ () => null }
                                placeholder={ __( 'Register…', 'advanced-gutenberg' ) }
                                keepPlaceholderOnFocus
                            />
                        </div>
                    ) }
                    <div className="advgb-login-form advgb-form-inner">
                        <div className="advgb-lores-form-header">
                            {!!showLogo && logoElm}
                            <RichText
                                tagName="h3"
                                value={ welcomeText }
                                className="advgb-lores-form-welcome"
                                onChange={ (value) => setAttributes( { welcomeText: value.trim() } ) }
                                style={ { color: textColor } }
                                placeholder={ __( 'Welcome text…', 'advanced-gutenberg' ) }
                                keepPlaceholderOnFocus
                            />
                        </div>
                        <div className="advgb-lores-field advgb-login-user">
                            <div className="advgb-lores-field-label">
                                <RichText
                                    tagName="label"
                                    value={ loginText }
                                    onChange={ (value) => setAttributes( { loginText: value.trim() } ) }
                                    style={ { color: textColor } }
                                    onReplace={ () => null }
                                    onSplit={ () => null }
                                    placeholder={ __( 'Username label…', 'advanced-gutenberg' ) }
                                    keepPlaceholderOnFocus
                                />
                            </div>
                            <div className="advgb-lores-field-input"
                                 style={ {
                                     backgroundColor: bgColor,
                                     color: inputColor,
                                     borderBottomColor: borderColor,
                                     borderStyle: borderStyle,
                                     borderWidth: borderWidth,
                                 } }
                            >
                                {!!showInputFieldIcon && (
                                    <span className="advgb-lores-input-icon"
                                          style={ { color: textColor } }
                                    >
                                        { emailIcon }
                                    </span>
                                ) }
                                <input type="text" disabled={ true }
                                       className="advgb-lores-input"
                                       style={ { color: inputColor } }
                                       value={ loginLabel ? loginLabel : 'user@email.com' }
                                />
                            </div>
                        </div>
                        <div className="advgb-lores-field advgb-login-password">
                            <div className="advgb-lores-field-label">
                                <RichText
                                    tagName="label"
                                    value={ passwordText }
                                    onChange={ (value) => setAttributes( { passwordText: value.trim() } ) }
                                    style={ { color: textColor } }
                                    onReplace={ () => null }
                                    onSplit={ () => null }
                                    placeholder={ __( 'Password label…', 'advanced-gutenberg' ) }
                                    keepPlaceholderOnFocus
                                />
                            </div>
                            <div className="advgb-lores-field-input"
                                 style={ {
                                     backgroundColor: bgColor,
                                     color: inputColor,
                                     borderBottomColor: borderColor,
                                     borderStyle: borderStyle,
                                     borderWidth: borderWidth,
                                 } }
                            >
                                {!!showInputFieldIcon && (
                                    <span className="advgb-lores-input-icon"
                                          style={ { color: textColor } }
                                    >
                                        { passwordIcon }
                                    </span>
                                ) }
                                <input type="password" disabled={ true }
                                       className="advgb-lores-input"
                                       style={ { color: inputColor } }
                                       value="password"
                                />
                            </div>
                        </div>
                        <div className={`advgb-lores-field advgb-lores-submit-wrapper advgb-submit-align-${submitPosition}`}>
                            <label htmlFor="rememberme" className="remember-me-label">
                                <input type="checkbox"
                                       checked={ true }
                                       className="advgb-lores-checkbox"
                                       style={ { color: submitBgColor } }
                                />
                                <div className="remember-me-switch" style={ { color: submitBgColor } }>
                                    <span>
                                        <RichText
                                            tagName="span"
                                            value={ rememberMeText }
                                            onChange={ (value) => setAttributes( { passwordText: value.trim() } ) }
                                            style={ { color: textColor } }
                                            onReplace={ () => null }
                                            onSplit={ () => null }
                                            placeholder={ __( 'Remember me…', 'advanced-gutenberg' ) }
                                            keepPlaceholderOnFocus
                                        />
                                    </span>
                                </div>
                            </label>
                            <div className="advgb-lores-submit advgb-login-submit">
                                <span className={`advgb-lores-submit-button ${submitButtonId}`}
                                      style={ {
                                          borderColor: submitColor,
                                          color: submitColor,
                                          backgroundColor: submitBgColor,
                                          borderRadius: submitRadius,
                                      } }
                                >
                                    <RichText
                                        tagName="span"
                                        value={ loginSubmitLabel }
                                        onChange={ (value) => setAttributes( { loginSubmitLabel: value.trim() } ) }
                                        onReplace={ () => null }
                                        onSplit={ () => null }
                                        placeholder={ __( 'Login…', 'advanced-gutenberg' ) }
                                        keepPlaceholderOnFocus
                                    />
                                </span>
                            </div>
                        </div>
                        {!!showLostPasswordLink && (
                            <div className="advgb-lores-field advgb-lost-password-field">
                                <div className="advgb-lost-password">
                                    <RichText
                                        tagName="a"
                                        value={ lostPasswordText }
                                        className="advgb-lost-password-link"
                                        onChange={ (value) => setAttributes( { lostPasswordText: value.trim() } ) }
                                        style={ { color: submitBgColor } }
                                        onReplace={ () => null }
                                        onSplit={ () => null }
                                        placeholder={ __( 'Lost password…', 'advanced-gutenberg' ) }
                                        keepPlaceholderOnFocus
                                    />
                                </div>
                            </div>
                        ) }
                    </div>
                </div>
            );

            const registerForm = (
                <div className="advgb-register-form-wrapper advgb-lores-form">
                    {!!showRegisterLink && (
                        <div className="advgb-header-navigation advgb-back-to-login"
                             style={ { backgroundColor: headerBgColor } }
                        >
                            <div className="advgb-back-to-login-link"
                                 style={ { color: submitBgColor } }
                            >
                                <RichText
                                    tagName="span"
                                    value={ backToLoginText }
                                    className="advgb-register-text"
                                    onChange={ (value) => setAttributes( { backToLoginText: value.trim() } ) }
                                    style={ { color: submitBgColor } }
                                    onReplace={ () => null }
                                    onSplit={ () => null }
                                    placeholder={ __( 'Back…', 'advanced-gutenberg' ) }
                                    keepPlaceholderOnFocus
                                />
                            </div>
                        </div>
                    ) }
                    <div className="advgb-register-form advgb-form-inner">
                        <div className="advgb-lores-form-header">
                            {!!showLogo && regLogoElm}
                            <RichText
                                tagName="h3"
                                value={ registerWelcome }
                                className="advgb-lores-form-welcome"
                                onChange={ (value) => setAttributes( { registerWelcome: value.trim() } ) }
                                style={ { color: textColor } }
                                placeholder={ __( 'Register…', 'advanced-gutenberg' ) }
                                keepPlaceholderOnFocus
                            />
                        </div>
                        <div className="advgb-lores-field advgb-register-username">
                            <div className="advgb-lores-field-label">
                                <RichText
                                    tagName="label"
                                    value={ userText }
                                    onChange={ (value) => setAttributes( { userText: value.trim() } ) }
                                    style={ { color: textColor } }
                                    onReplace={ () => null }
                                    onSplit={ () => null }
                                    placeholder={ __( 'Username label…', 'advanced-gutenberg' ) }
                                    keepPlaceholderOnFocus
                                />
                            </div>
                            <div className="advgb-lores-field-input"
                                 style={ {
                                     backgroundColor: bgColor,
                                     color: inputColor,
                                     borderBottomColor: borderColor,
                                     borderStyle: borderStyle,
                                     borderWidth: borderWidth,
                                 } }
                            >
                                {!!showInputFieldIcon && (
                                    <span className="advgb-lores-input-icon"
                                          style={ { color: textColor } }
                                    >
                                        { userIcon }
                                    </span>
                                ) }
                                <input type="text" disabled={ true }
                                       className="advgb-lores-input"
                                       style={ { color: inputColor } }
                                       value={ usernameLabel ? usernameLabel : 'username' }
                                />
                            </div>
                        </div>
                        <div className="advgb-lores-field advgb-register-email">
                            <div className="advgb-lores-field-label">
                                <RichText
                                    tagName="label"
                                    value={ emailText }
                                    onChange={ (value) => setAttributes( { emailText: value.trim() } ) }
                                    style={ { color: textColor } }
                                    onReplace={ () => null }
                                    onSplit={ () => null }
                                    placeholder={ __( 'Email label…', 'advanced-gutenberg' ) }
                                    keepPlaceholderOnFocus
                                />
                            </div>
                            <div className="advgb-lores-field-input"
                                 style={ {
                                     backgroundColor: bgColor,
                                     color: inputColor,
                                     borderBottomColor: borderColor,
                                     borderStyle: borderStyle,
                                     borderWidth: borderWidth,
                                 } }
                            >
                                {!!showInputFieldIcon && (
                                    <span className="advgb-lores-input-icon"
                                          style={ { color: textColor } }
                                    >
                                        { emailIcon }
                                    </span>
                                ) }
                                <input type="text" disabled={ true }
                                       className="advgb-lores-input"
                                       style={ { color: inputColor } }
                                       value={ emailLabel ? emailLabel : 'user@email.com' }
                                />
                            </div>
                        </div>
                        <div className={`advgb-lores-field advgb-lores-submit-wrapper advgb-submit-align-${submitPosition}`}>
                            <div className="advgb-lores-submit advgb-register-submit">
                                <span className={`advgb-lores-submit-button ${submitButtonId}`}
                                      style={ {
                                          borderColor: submitColor,
                                          color: submitColor,
                                          backgroundColor: submitBgColor,
                                          borderRadius: submitRadius,
                                      } }
                                >
                                    <RichText
                                        tagName="span"
                                        value={ registerSubmitLabel }
                                        onChange={ (value) => setAttributes( { registerSubmitLabel: value.trim() } ) }
                                        onReplace={ () => null }
                                        onSplit={ () => null }
                                        placeholder={ __( 'Register…', 'advanced-gutenberg' ) }
                                        keepPlaceholderOnFocus
                                    />
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            );

            return (
                isPreview ?
                    <img alt={__('Login/Register Form', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                <Fragment>
                    <BlockControls>
                        <ToolbarGroup label={ __( 'Options', 'advanced-gutenberg' ) }>
                            <ToolbarButton
                                icon="image-flip-horizontal"
                                label={ __( 'Switch View', 'advanced-gutenberg' ) }
                                onClick={ () => this.setState( { registerView: !registerView } ) }
                            />
                        </ToolbarGroup>
                    </BlockControls>
                    <InspectorControls>
                        <PanelBody title={ __( 'Form State', 'advanced-gutenberg' ) }>
                            <SelectControl
                                label={ __( 'Initial Form', 'advanced-gutenberg' ) }
                                help={ __( 'Form that show on load.', 'advanced-gutenberg' ) }
                                value={ formType }
                                options={ [
                                    { label: __( 'Login', 'advanced-gutenberg' ), value: 'login' },
                                    { label: __( 'Register', 'advanced-gutenberg' ), value: 'register' },
                                ] }
                                onChange={ (value) => {
                                    setAttributes( { formType: value } );
                                    this.setState( { registerView: value === 'register' } );
                                } }
                            />
                            <SelectControl
                                label={ __( 'Redirect After Login', 'advanced-gutenberg' ) }
                                value={ redirect }
                                options={ [
                                    { label: __( 'Home', 'advanced-gutenberg' ), value: 'home' },
                                    { label: __( 'Dashboard', 'advanced-gutenberg' ), value: 'dashboard' },
                                    { label: __( 'Custom', 'advanced-gutenberg' ), value: 'custom' },
                                ] }
                                onChange={ (value) => setAttributes( { redirect: value } ) }
                            />
                            {redirect === 'custom' && (
                                <TextControl
                                    label={ __( 'Custom redirect link', 'advanced-gutenberg' ) }
                                    value={ redirectLink }
                                    onChange={ (value) => setAttributes( { redirectLink: value } ) }
                                />
                            ) }
                            <RangeControl
                                label={ __( 'Form Width (px)', 'advanced-gutenberg' ) }
                                value={ formWidth }
                                onChange={ ( value ) => setAttributes( { formWidth: value } ) }
                                min={ 300 }
                                max={ 1500 }
                            />
                            <ToggleControl
                                label={ __( 'Show Logo', 'advanced-gutenberg' ) }
                                checked={ !!showLogo }
                                onChange={ () => setAttributes( { showLogo: !showLogo } ) }
                            />
                            {!!showLogo && (
                                <RangeControl
                                    label={ __( 'Logo Width (px)', 'advanced-gutenberg' ) }
                                    value={ logoWidth }
                                    onChange={ ( value ) => setAttributes( { logoWidth: value } ) }
                                    min={ 100 }
                                    max={ 1500 }
                                />
                            ) }
                            <ToggleControl
                                label={ __( 'Show input field icon', 'advanced-gutenberg' ) }
                                checked={ !!showInputFieldIcon }
                                onChange={ () => setAttributes( { showInputFieldIcon: !showInputFieldIcon } ) }
                            />
                            <ToggleControl
                                label={ __( 'Show register/header link', 'advanced-gutenberg' ) }
                                checked={ !!showRegisterLink }
                                onChange={ () => setAttributes( { showRegisterLink: !showRegisterLink } ) }
                            />
                            {!!showRegisterLink && (
                                <PanelColorSettings
                                    title={ __( 'Header Color', 'advanced-gutenberg' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Header color', 'advanced-gutenberg' ),
                                            value: headerBgColor,
                                            onChange: (value) => setAttributes( { headerBgColor: value } ),
                                        },
                                    ] }
                                />
                            ) }
                            <ToggleControl
                                label={ __( 'Show lost password link', 'advanced-gutenberg' ) }
                                checked={ !!showLostPasswordLink }
                                onChange={ () => setAttributes( { showLostPasswordLink: !showLostPasswordLink } ) }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Input placeholder', 'advanced-gutenberg' ) } initialOpen={ false }>
                            <TextControl
                                label={ __( 'Login input placeholder', 'advanced-gutenberg' ) }
                                value={ loginLabel }
                                onChange={ (value) => setAttributes( { loginLabel: value } ) }
                            />
                            <TextControl
                                label={ __( 'Username input placeholder', 'advanced-gutenberg' ) }
                                help={ __( 'Use in register form', 'advanced-gutenberg' ) }
                                value={ usernameLabel }
                                onChange={ (value) => setAttributes( { usernameLabel: value } ) }
                            />
                            <TextControl
                                label={ __( 'Email input placeholder', 'advanced-gutenberg' ) }
                                help={ __( 'Use in register form', 'advanced-gutenberg' ) }
                                value={ emailLabel }
                                onChange={ (value) => setAttributes( { emailLabel: value } ) }
                            />
                        </PanelBody>
                        <PanelColorSettings
                            title={ __( 'Text/Input Color', 'advanced-gutenberg' ) }
                            initialOpen={ false }
                            colorSettings={ [
                                {
                                    label: __( 'Input background color', 'advanced-gutenberg' ),
                                    value: bgColor,
                                    onChange: (value) => setAttributes( { bgColor: value } ),
                                },
                                {
                                    label: __( 'Input color', 'advanced-gutenberg' ),
                                    value: inputColor,
                                    onChange: (value) => setAttributes( { inputColor: value } ),
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
                                label={ __( 'Border width', 'advanced-gutenberg' ) }
                                value={ borderWidth }
                                onChange={ (value) => setAttributes( { borderWidth: value } ) }
                                min={ 0 }
                                max={ 10 }
                            />
                        </PanelBody>
                        <PanelBody title={ __( 'Submit Button Settings', 'advanced-gutenberg' ) }>
                            <AdvColorControl
                                label={ __('Border and Text', 'advanced-gutenberg') }
                                value={ submitColor }
                                onChange={ (value) => setAttributes( { submitColor: value } ) }
                            />
                            <AdvColorControl
                                label={ __('Background', 'advanced-gutenberg') }
                                value={ submitBgColor }
                                onChange={ (value) => setAttributes({submitBgColor: value}) }
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
                        <PanelBody title={ __( 'Submit Button Hover', 'advanced-gutenberg' ) } initialOpen={false}>
                            <PanelColorSettings
                                title={ __( 'Hover Colors', 'advanced-gutenberg' ) }
                                initialOpen={ false }
                                colorSettings={ [
                                    {
                                        label: __( 'Background color', 'advanced-gutenberg' ),
                                        value: submitHoverBgColor,
                                        onChange: (value) => setAttributes( { submitHoverBgColor: value } ),
                                    },
                                    {
                                        label: __( 'Text color', 'advanced-gutenberg' ),
                                        value: submitHoverColor,
                                        onChange: (value) => setAttributes( { submitHoverColor: value } ),
                                    },
                                    {
                                        label: __( 'Shadow color', 'advanced-gutenberg' ),
                                        value: submitHoverShadow,
                                        onChange: (value) => setAttributes( { submitHoverShadow: value } ),
                                    },
                                ] }
                            />
                            <PanelBody title={ __( 'Shadow', 'advanced-gutenberg' ) } initialOpen={false}>
                                <RangeControl
                                    label={ __('Opacity (%)', 'advanced-gutenberg') }
                                    value={ submitHoverOpacity }
                                    onChange={ ( value ) => setAttributes( { submitHoverOpacity: value } ) }
                                    min={ 0 }
                                    max={ 100 }
                                />
                                <RangeControl
                                    label={ __('Transition speed (ms)', 'advanced-gutenberg') }
                                    value={ submitHoverTranSpeed || '' }
                                    onChange={ ( value ) => setAttributes( { submitHoverTranSpeed: value } ) }
                                    min={ 0 }
                                    max={ 3000 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow H offset', 'advanced-gutenberg' ) }
                                    value={ submitHoverShadowH || '' }
                                    onChange={ ( value ) => setAttributes( { submitHoverShadowH: value } ) }
                                    min={ -50 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow V offset', 'advanced-gutenberg' ) }
                                    value={ submitHoverShadowV || '' }
                                    onChange={ ( value ) => setAttributes( { submitHoverShadowV: value } ) }
                                    min={ -50 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow blur', 'advanced-gutenberg' ) }
                                    value={ submitHoverShadowBlur || '' }
                                    onChange={ ( value ) => setAttributes( { submitHoverShadowBlur: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                                <RangeControl
                                    label={ __( 'Shadow spread', 'advanced-gutenberg' ) }
                                    value={ submitHoverShadowSpread || '' }
                                    onChange={ ( value ) => setAttributes( { submitHoverShadowSpread: value } ) }
                                    min={ 0 }
                                    max={ 50 }
                                />
                            </PanelBody>
                        </PanelBody>
                        {(typeof advgbBlocks !== 'undefined' && !parseInt(advgbBlocks.captchaEnabled)) && (
                            <PanelBody title={ __( 'Notice', 'advanced-gutenberg' ) }>
                                <p style={ { fontStyle: 'italic', color: '#ff8800' } }>
                                    { __( 'We strongly recommend to enable Google reCaptcha to avoid spam bot. You can enable it in Form Recaptcha in', 'advanced-gutenberg' ) }
                                    <a href={advgbBlocks.config_url + '#email-form'} target="_blank"> { __( 'settings', 'advanced-gutenberg' ) }.</a>
                                </p>
                            </PanelBody>
                        ) }
                    </InspectorControls>
                    <div className="advgb-lores-form-wrapper" style={ { width: formWidth } }>
                        {!registerView
                            ? loginForm
                            : (typeof advgbBlocks !== 'undefined' && !parseInt(advgbBlocks.registerEnabled))
                                ? (
                                    <Placeholder
                                        icon={userIcon}
                                        label={ __( 'Registration Form', 'advanced-gutenberg' ) }
                                        instructions={ __( 'Registration for your website is currently disabled, enable it in WordPress General settings to use registration form', 'advanced-gutenberg' ) }
                                    />
                                )
                                : registerForm
                        }
                        <style>
                            {`.${submitButtonId}:hover {
                                color: ${submitHoverColor} !important;
                                background-color: ${submitHoverBgColor} !important;
                                box-shadow: ${submitHoverShadowH}px ${submitHoverShadowV}px ${submitHoverShadowBlur}px ${submitHoverShadowSpread}px ${submitHoverShadow};
                                transition: all ${submitHoverTranSpeed}s ease;
                                opacity: ${submitHoverOpacity/100}
                            }`}
                        </style>
                    </div>
                </Fragment>
            )
        }
    }

    const loginFormBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path fill="none" d="M0 0h24v24H0V0z"/>
            <path d="M11 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zM5 18c.2-.63 2.57-1.68 4.96-1.94l2.04-2c-.39-.04-.68-.06-1-.06-2.67 0-8 1.34-8 4v2h9l-2-2H5zm15.6-5.5l-5.13 5.17-2.07-2.08L12 17l3.47 3.5L22 13.91z"/>
        </svg>
    );

    const blockAttrs = {
        formType: {
            type: 'string',
            default: 'login',
        },
        formWidth: {
            type: 'number',
            default: 500,
        },
        redirect: {
            type: 'string',
            default: 'home',
        },
        redirectLink: {
            type: 'string',
        },
        showLogo: {
            type: 'boolean',
            default: true,
        },
        showInputFieldIcon: {
            type: 'boolean',
            default: true,
        },
        showRegisterLink: {
            type: 'boolean',
            default: true,
        },
        showLostPasswordLink: {
            type: 'boolean',
            default: true,
        },
        logoImg: {
            type: 'string',
            default: advgbBlocks.login_logo,
        },
        logoID: {
            type: 'number',
        },
        registerLogoImg: {
            type: 'string',
            default: advgbBlocks.reg_logo,
        },
        registerLogoID: {
            type: 'number',
        },
        logoWidth: {
            type: 'number',
            default: 150,
        },
        welcomeText: {
            type: 'string',
            default: 'Welcome back',
        },
        loginLabel: {
            type: 'string',
        },
        loginText: {
            type: 'string',
            default: 'Username or Email',
        },
        passwordText: {
            type: 'string',
            default: 'Password',
        },
        usernameLabel: {
            type: 'string',
        },
        userText: {
            type: 'string',
            default: 'Username',
        },
        emailLabel: {
            type: 'string',
        },
        emailText: {
            type: 'string',
            default: 'Email',
        },
        rememberMeText: {
            type: 'string',
            default: 'Remember me',
        },
        loginSubmitLabel: {
            type: 'string',
            default: 'LOGIN',
        },
        registerSubmitLabel: {
            type: 'string',
            default: 'REGISTER',
        },
        registerText: {
            type: 'string',
            default: "Don't have an account?",
        },
        registerLinkText: {
            type: 'string',
            default: 'Register now',
        },
        registerWelcome: {
            type: 'string',
            default: 'Register new account',
        },
        backToLoginText: {
            type: 'string',
            default: 'Login',
        },
        lostPasswordText: {
            type: 'string',
            default: 'Lost your password?',
        },
        headerBgColor: {
            type: 'string',
        },
        bgColor: {
            type: 'string',
        },
        textColor: {
            type: 'string',
        },
        inputColor: {
            type: 'string',
        },
        borderStyle: {
            type: 'string',
        },
        borderColor: {
            type: 'string',
        },
        borderWidth: {
            type: 'number',
        },
        submitButtonId : {
            type: 'string',
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
        submitHoverColor: {
            type: 'string',
        },
        submitHoverBgColor: {
            type: 'string',
        },
        submitHoverShadow: {
            type: 'string',
        },
        submitHoverShadowH: {
            type: 'number',
            default: 1,
        },
        submitHoverShadowV: {
            type: 'number',
            default: 1,
        },
        submitHoverShadowBlur: {
            type: 'number',
            default: 12,
        },
        submitHoverShadowSpread: {
            type: 'number',
            default: 0,
        },
        submitHoverOpacity: {
            type: 'number',
            default: 100,
        },
        submitHoverTranSpeed: {
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

    registerBlockType( 'advgb/login-form', {
        title: __( 'Login/Register Form', 'advanced-gutenberg' ),
        description: __( 'Create a login form for your post/page.', 'advanced-gutenberg' ),
        icon: {
            src: loginFormBlockIcon,
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
            anchor: true
        },
        edit: LoginFormEdit,
        save: function ( { attributes } ) {
            const {
                formType, formWidth, redirect, redirectLink, showLogo, showInputFieldIcon,
                showRegisterLink, showLostPasswordLink, logoImg, registerLogoImg, logoWidth,
                welcomeText, loginLabel, loginText, passwordText, usernameLabel, userText,
                emailLabel, emailText, rememberMeText, loginSubmitLabel, registerSubmitLabel, registerText,
                registerLinkText, registerWelcome, backToLoginText, lostPasswordText,
                headerBgColor, bgColor, textColor, inputColor, borderColor, borderStyle, borderWidth,
                submitColor, submitBgColor, submitRadius, submitPosition, submitButtonId,
            } = attributes;

            const logoElmSave = (
                <div className="advgb-lores-form-logo-wrapper">
                    <span style={ {display: 'block',} }>
                        <img className="advgb-lores-form-logo"
                             src={ logoImg }
                             alt={ 'Site logo' }
                             style={ {
                                 width: logoWidth ? logoWidth + 'px' : undefined,
                                 cursor: 'pointer',
                             } }
                        />
                    </span>
                </div>
            );

            const regLogoElmSave = (
                <div className="advgb-lores-form-logo-wrapper">
                    <span style={ {display: 'block',} }>
                        <img className="advgb-lores-form-logo"
                             src={ registerLogoImg }
                             alt={ 'Site logo' }
                             style={ {
                                 width: logoWidth ? logoWidth + 'px' : undefined,
                                 cursor: 'pointer',
                             } }
                        />
                    </span>
                </div>
            );

            const loginFormSave = (
                <div className="advgb-login-form-wrapper advgb-lores-form"
                     style={ {
                         display: formType === 'login' ? 'block' : 'none',
                     } }
                >
                    {!!showRegisterLink && (
                        <div className="advgb-register-link-wrapper advgb-header-navigation"
                             style={ { backgroundColor: headerBgColor } }
                        >
                            <span className="advgb-register-text"
                                  style={ { color: textColor } }
                            >
                                { registerText }
                            </span>
                            <a href="#"
                               className="advgb-register-link"
                               style={ { color: submitBgColor } }
                            >
                                { registerLinkText }
                            </a>
                        </div>
                    ) }
                    <form action="" className="advgb-form-login" method="post">
                        <div className="advgb-login-form advgb-form-inner">
                            <div className="advgb-lores-form-header">
                                {!!showLogo && logoElmSave}
                                <h3 className="advgb-lores-form-welcome"
                                    style={ { color: textColor } }
                                >
                                    { welcomeText }
                                </h3>
                            </div>
                            <div className="advgb-lores-field advgb-login-user"
                                 style={ { borderColor: textColor } }
                            >
                                <div className="advgb-lores-field-label">
                                    <label htmlFor="advgb-login-user" style={ { color: textColor } }>
                                        { loginText }
                                    </label>
                                </div>
                                <div className="advgb-lores-field-input"
                                     style={ {
                                         backgroundColor: bgColor,
                                         color: inputColor,
                                         borderBottomColor: borderColor,
                                         borderStyle: borderStyle,
                                         borderWidth: borderWidth,
                                     } }
                                >
                                    {!!showInputFieldIcon && (
                                        <span className="advgb-lores-input-icon"
                                              style={ { color: textColor } }
                                        >
                                            { emailIcon }
                                        </span>
                                    ) }
                                    <input type="text"
                                           id="advgb-login-user"
                                           className="advgb-lores-input"
                                           name="log"
                                           style={ { color: inputColor } }
                                           placeholder={ loginLabel ? loginLabel : 'user@email.com' }
                                    />
                                </div>
                            </div>
                            <div className="advgb-lores-field advgb-login-password"
                                 style={ { borderColor: textColor } }
                            >
                                <div className="advgb-lores-field-label">
                                    <label htmlFor="advgb-login-password" style={ { color: textColor } }>
                                        { passwordText }
                                    </label>
                                </div>
                                <div className="advgb-lores-field-input"
                                     style={ {
                                         backgroundColor: bgColor,
                                         color: inputColor,
                                         borderBottomColor: borderColor,
                                         borderStyle: borderStyle,
                                         borderWidth: borderWidth,
                                     } }
                                >
                                    {!!showInputFieldIcon && (
                                        <span className="advgb-lores-input-icon"
                                              style={ { color: textColor } }
                                        >
                                            { passwordIcon }
                                        </span>
                                    ) }
                                    <input type="password"
                                           id="advgb-login-password"
                                           className="advgb-lores-input"
                                           name="pwd"
                                           style={ { color: inputColor } }
                                           placeholder="password"
                                    />
                                </div>
                            </div>
                            <div className={`advgb-grecaptcha clearfix position-${submitPosition}`}/>
                            <div className={`advgb-lores-field advgb-lores-submit-wrapper advgb-submit-align-${submitPosition}`}>
                                <label htmlFor="rememberme" className="remember-me-label">
                                    <input type="checkbox"
                                           value="forever"
                                           id="rememberme"
                                           name="rememberme"
                                           className="advgb-lores-checkbox"
                                    />
                                    <div style={ { color: submitBgColor } } className="remember-me-switch">
                                        <span style={ { color: textColor } }>{ rememberMeText }</span>
                                    </div>
                                </label>
                                <div className="advgb-lores-submit advgb-login-submit">
                                    <button className={`advgb-lores-submit-button ${submitButtonId}`}
                                            type="submit"
                                            name="wp-submit"
                                            style={ {
                                                borderColor: submitColor,
                                                color: submitColor,
                                                backgroundColor: submitBgColor,
                                                borderRadius: submitRadius,
                                            } }
                                    >
                                        { loginSubmitLabel }
                                    </button>
                                    <input type="hidden" name="redirect_to" data-redirect={redirect} className="redirect_to" value={redirectLink} />
                                    <input type="hidden" name="testcookie" value="1" />
                                    <input type="hidden" name="advgb_login_form" value="1" />
                                </div>
                            </div>
                            {!!showLostPasswordLink && (
                                <div className="advgb-lores-field advgb-lost-password-field">
                                    <div className="advgb-lost-password">
                                        <a href="#"
                                           className="advgb-lost-password-link"
                                           style={ { color: submitBgColor } }
                                        >
                                            { lostPasswordText }
                                        </a>
                                    </div>
                                </div>
                            ) }
                        </div>
                    </form>
                </div>
            );

            const registerFormSave = (
                <div className="advgb-register-form-wrapper advgb-lores-form"
                     style={ {
                         display: formType === 'register' ? 'block' : 'none',
                     } }
                >
                    {!!showRegisterLink && (
                        <div className="advgb-header-navigation advgb-back-to-login"
                             style={ { backgroundColor: headerBgColor } }
                        >
                            <div className="advgb-back-to-login-link"
                                 style={ { color: submitBgColor } }
                            >
                                <span className="advgb-register-text"
                                      style={ { color: submitBgColor } }
                                >
                                    { backToLoginText }
                                </span>
                            </div>
                        </div>
                    ) }
                    <form action="" className="advgb-form-register" method="post">
                        <div className="advgb-register-form advgb-form-inner">
                            <div className="advgb-lores-form-header">
                                {!!showLogo && regLogoElmSave}
                                <h3 className="advgb-lores-form-welcome"
                                    style={ { color: textColor } }
                                >
                                    { registerWelcome }
                                </h3>
                            </div>
                            <div className="advgb-lores-field advgb-register-username"
                                 style={ { borderColor: textColor } }
                            >
                                <div className="advgb-lores-field-label">
                                    <label htmlFor="advgb-register-username" style={ { color: textColor } }>
                                        { userText }
                                    </label>
                                </div>
                                <div className="advgb-lores-field-input"
                                     style={ {
                                         backgroundColor: bgColor,
                                         color: inputColor,
                                         borderBottomColor: borderColor,
                                         borderStyle: borderStyle,
                                         borderWidth: borderWidth,
                                     } }
                                >
                                    {!!showInputFieldIcon && (
                                        <span className="advgb-lores-input-icon"
                                              style={ { color: textColor } }
                                        >
                                            { userIcon }
                                        </span>
                                    ) }
                                    <input type="text"
                                           id="advgb-register-username"
                                           className="advgb-lores-input"
                                           name="user_login"
                                           style={ { color: inputColor } }
                                           placeholder={ usernameLabel ? usernameLabel : 'username' }
                                    />
                                </div>
                            </div>
                            <div className="advgb-lores-field advgb-register-email"
                                 style={ { borderColor: textColor } }
                            >
                                <div className="advgb-lores-field-label">
                                    <label htmlFor="advgb-register-email" style={ { color: textColor } }>
                                        { emailText }
                                    </label>
                                </div>
                                <div className="advgb-lores-field-input"
                                     style={ {
                                         backgroundColor: bgColor,
                                         color: inputColor,
                                         borderBottomColor: borderColor,
                                         borderStyle: borderStyle,
                                         borderWidth: borderWidth,
                                     } }
                                >
                                    {!!showInputFieldIcon && (
                                        <span className="advgb-lores-input-icon"
                                              style={ { color: textColor } }
                                        >
                                            { emailIcon }
                                        </span>
                                    ) }
                                    <input type="email"
                                           id="advgb-register-email"
                                           className="advgb-lores-input"
                                           name="user_email"
                                           style={ { color: inputColor } }
                                           placeholder={ emailLabel ? emailLabel : 'user@email.com' }
                                    />
                                </div>
                            </div>
                            <div className={`advgb-grecaptcha clearfix position-${submitPosition}`}/>
                            <div className={`advgb-lores-field advgb-lores-submit-wrapper advgb-submit-align-${submitPosition}`}>
                                <div className="advgb-lores-submit advgb-register-submit">
                                    <button className={`advgb-lores-submit-button ${submitButtonId}`}
                                            type="submit"
                                            name="wp-submit"
                                            style={ {
                                                borderColor: submitColor,
                                                color: submitColor,
                                                backgroundColor: submitBgColor,
                                                borderRadius: submitRadius,
                                            } }
                                    >
                                        { registerSubmitLabel }
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            );

            return (
                <div className="advgb-lores-form-wrapper" style={ { width: formWidth } }>
                    { loginFormSave }
                    { registerFormSave }
                </div>
            );
        }
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );