(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const {
        RangeControl,
        PanelBody,
        ToggleControl,
        BaseControl,
        TextControl,
        SelectControl,
        Button,
        Dashicon,
        Spinner,
        Toolbar,
        ToolbarGroup,
        ToolbarButton,
        Disabled
    } = wpComponents;

    const PLAY_BUTTON_STYLE = {
        normal: [
            <path key="x" d="M8 5v14l11-7z"/>,
            <path key="y" d="M0 0h24v24H0z" fill="none"/>
        ],
        circleFill: [
            <path key="x" d="M0 0h24v24H0z" fill="none"/>,
            <path key="y" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
        ],
        circleOutline: [
            <path key="x" d="M0 0h24v24H0z" fill="none"/>,
            <path key="y" d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
        ],
        videoCam: [
            <path key="x" d="M0 0h24v24H0z" fill="none"/>,
            <path key="y" d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
        ],
        squareCurved: [
            <path key="x" d="M20 8H4V6h16v2zm-2-6H6v2h12V2zm4 10v8c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2v-8c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2zm-6 4l-6-3.27v6.53L16 16z"/>,
            <path key="y" fill="none" d="M0 0h24v24H0z"/>
        ],
        starSticker: [
            <path key="x" d="M0 0h24v24H0z" fill="none"/>,
            <path key="y" d="M20 12c0-1.1.9-2 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2v4c1.1 0 1.99.9 1.99 2s-.89 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2zm-4.42 4.8L12 14.5l-3.58 2.3 1.08-4.12-3.29-2.69 4.24-.25L12 5.8l1.54 3.95 4.24.25-3.29 2.69 1.09 4.11z"/>
        ],
    };

    const previewImageData = 'data:image/png;base64,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';

    class AdvVideo extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                fetching: false,
            };

            this.fetchVideoInfo = this.fetchVideoInfo.bind( this );
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-video'];

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

        fetchVideoInfo() {
            const { attributes, setAttributes } = this.props;
            const { videoID, poster, posterID } = attributes;
            let realID = videoID;

            if (!!videoID) {
                this.setState( { fetching: true } );

                let url = '';
                if (videoID.match( /^\d+$/g )) {
                    url = `https://vimeo.com/${videoID}`
                } else {
                    url = `https://www.youtube.com/watch?v=${videoID}`
                }

                if (videoID.indexOf( 'http' ) > -1) {
                    url = videoID;
                }

                if (videoID.match( /youtube.com/ )) {
                    realID = videoID.split( 'v=' );
                    realID = realID[1];
                } else if (videoID.match( /youtu.be|vimeo.com/ )) {
                    realID = videoID.split( '/' );
                    realID = realID[ realID.length - 1 ];
                }

                if (!realID) realID = '';

                if (realID.indexOf( '&' ) > -1)
                    realID = realID.substring( 0, realID.indexOf( '&' ) );

                wp.apiFetch( { path: wp.url.addQueryArgs(`/oembed/1.0/proxy?url=${ encodeURIComponent( url ) }`) } ).then(
                    (obj) => {
                        this.setState( { fetching: false } );
                        if (!!obj.title && !!obj.provider_name) {
                            setAttributes( {
                                videoTitle: obj.title,
                                poster: !!posterID ? poster : obj.thumbnail_url,
                            } );

                            switch (obj.provider_name) {
                                case 'YouTube':
                                    setAttributes( {
                                        videoSourceType: 'youtube',
                                        videoURL: `https://www.youtube.com/embed/${realID}?rel=0&wmode=transparent`,
                                    } );
                                    break;
                                case 'Vimeo':
                                    setAttributes( {
                                        videoSourceType: 'vimeo',
                                        videoURL: `https://player.vimeo.com/video/${realID}`,
                                    } );
                                    break;
                                default:
                                    break;
                            }
                        } else {
                            setAttributes( {
                                videoTitle: 'ADVGB_FAIL_TO_LOAD',
                                poster: '',
                            } );
                        }
                    }
                ).catch( ( error ) => {
                    this.setState( { fetching: false } );
                    setAttributes( {
                        videoTitle: 'ADVGB_FAIL_TO_LOAD',
                        poster: '',
                    } );
                } )
            }
        }

        loadLocalVideo(video, blockId) {
            this.props.setAttributes(
                {
                    videoURL: video.url,
                    videoID: video.id,
                    videoTitle: video.title,
                    videoSourceType: 'local',
                    openInLightbox: false,
                }
            );
            if(document.querySelector('#'+blockId+' video') != null) {
                document.querySelector( '#' + blockId + ' video' ).pause();
                document.querySelector( '#' + blockId + ' video' ).load();
            }
        }

        render() {
            const { isSelected, attributes, clientId,  setAttributes } = this.props;
            const {
                videoURL,
                videoID,
                videoSourceType,
                videoTitle,
                videoFullWidth,
                videoWidth,
                videoHeight,
                playButtonIcon,
                playButtonSize,
                playButtonColor,
                overlayColor,
                poster,
                posterID,
                openInLightbox,
                autoPlay,
                loop,
                muted,
                playback,
                playsinline,
                preload,
                isPreview,
            } = attributes;

            const blockClassName = [
                'advgb-video-block',
                !!openInLightbox && !!videoURL && 'advgb-video-lightbox',
            ].filter( Boolean ).join( ' ' );

            const videoWrapperClass = [
                'advgb-video-wrapper',
                !!videoFullWidth && 'full-width',
                !openInLightbox && 'no-lightbox',
            ].filter( Boolean ).join( ' ' );

            const blockId = 'advgb-video-' + clientId;

            const videoHostIcon = {
                youtube: (
                    <svg id="Social_Icons" version="1.1" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
                        <g id="_x34__stroke">
                            <g id="Youtube_1_">
                                <rect clipRule="evenodd" fill="none" height="128" width="128"/>
                                <path clipRule="evenodd" d="M126.72,38.224c0,0-1.252-8.883-5.088-12.794    c-4.868-5.136-10.324-5.16-12.824-5.458c-17.912-1.305-44.78-1.305-44.78-1.305h-0.056c0,0-26.868,0-44.78,1.305    c-2.504,0.298-7.956,0.322-12.828,5.458C2.528,29.342,1.28,38.224,1.28,38.224S0,48.658,0,59.087v9.781    c0,10.433,1.28,20.863,1.28,20.863s1.248,8.883,5.084,12.794c4.872,5.136,11.268,4.975,14.116,5.511    c10.24,0.991,43.52,1.297,43.52,1.297s26.896-0.04,44.808-1.345c2.5-0.302,7.956-0.326,12.824-5.462    c3.836-3.912,5.088-12.794,5.088-12.794S128,79.302,128,68.868v-9.781C128,48.658,126.72,38.224,126.72,38.224z M50.784,80.72    L50.78,44.501l34.584,18.172L50.784,80.72z" fill="#CE1312" fillRule="evenodd" id="Youtube"/>
                            </g>
                        </g>
                    </svg>
                ),
                vimeo: (
                    <svg height="25" viewBox="0 0 32 32" width="25" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <circle cx="16" cy="16" id="BG" r="16" fill="#5FCCFF"/>
                            <path d="M24,12.4c-0.1,1.6-1.2,3.7-3.3,6.4c-2.2,2.8-4,4.2-5.5,4.2        c-0.9,0-1.7-0.9-2.4-2.6c-0.4-1.6-0.9-3.2-1.3-4.7c-0.5-1.7-1-2.6-1.5-2.6c-0.1,0-0.5,0.3-1.3,0.8l-0.8-1        c0.8-0.7,1.6-1.4,2.3-2.1c1.1-0.9,1.8-1.4,2.4-1.4c1.2-0.1,2,0.7,2.3,2.5c0.3,2,0.5,3.2,0.6,3.7c0.4,1.6,0.8,2.4,1.2,2.4        c0.3,0,0.8-0.5,1.5-1.6c0.7-1.1,1-1.9,1.1-2.4c0.1-0.9-0.3-1.4-1.1-1.4c-0.4,0-0.8,0.1-1.2,0.3c0.8-2.6,2.3-3.8,4.5-3.7        C23.3,9.2,24.1,10.3,24,12.4" id="Vimeo" fill="#FFFFFF"/>
                        </g>
                    </svg>
                ),
                local: (
                    <svg height="25" id="Layer_1" version="1.1" viewBox="0 0 24 24" width="25" xmlns="http://www.w3.org/2000/svg">
                        <path clipRule="evenodd" d="M22.506,21v0.016L17,15.511V19c0,1.105-0.896,2-2,2h-1.5H3H2c-1.104,0-2-0.895-2-2  v-1l0,0V6l0,0V5c0-1.104,0.896-1.999,2-1.999h1l0,0h10.5l0,0H15c1.104,0,2,0.895,2,1.999v3.516l5.5-5.5V3.001  c0.828,0,1.5,0.671,1.5,1.499v15C24,20.327,23.331,20.996,22.506,21z" fillRule="evenodd"/>
                    </svg>
                ),
            };

            return (
                isPreview ?
                    <img alt={__('Advanced Video', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                    <Fragment>
                        { ( (!!poster && openInLightbox) || ( !openInLightbox && videoSourceType === 'local' ) ) &&
                        <BlockControls>
                            <ToolbarGroup>
                                <MediaUpload
                                    allowedTypes={ ["image"] }
                                    value={ posterID }
                                    onSelect={ (image) => setAttributes( { poster: image.url, posterID: image.id } ) }
                                    render={ ( { open } ) => (
                                        <ToolbarButton
                                            className="components-toolbar__control"
                                            label={ __( 'Change image preview', 'advanced-gutenberg' ) }
                                            icon="edit"
                                            onClick={ open }
                                        />
                                    ) }
                                />
                                <ToolbarButton
                                    className="components-toolbar__control"
                                    label={ __( 'Remove image preview', 'advanced-gutenberg' ) }
                                    icon="no"
                                    onClick={ () => setAttributes( { poster: undefined, posterID: undefined } ) }
                                />
                            </ToolbarGroup>
                        </BlockControls>
                        }
                        <InspectorControls>
                            <PanelBody title={ __( 'Advanced Video Settings', 'advanced-gutenberg' ) }>
                                <ToggleControl
                                    label={ __( 'Open video in light box', 'advanced-gutenberg' ) }
                                    help={ __( 'Lightbox offers additional display options.', 'advanced-gutenberg' ) }
                                    checked={ openInLightbox }
                                    onChange={ () => setAttributes( { openInLightbox: !openInLightbox } ) }
                                />

                                { videoSourceType === 'local' &&
                                <Fragment>
                                    <ToggleControl
                                        label={ __( 'Autoplay', 'advanced-gutenberg' ) }
                                        checked={ autoPlay }
                                        onChange={ () => setAttributes( { autoPlay: !autoPlay } ) }
                                    />

                                    <ToggleControl
                                        label={ __( 'Loop', 'advanced-gutenberg' ) }
                                        checked={ loop }
                                        onChange={ () => setAttributes( { loop: !loop } ) }
                                    />

                                    <ToggleControl
                                        label={ __( 'Muted', 'advanced-gutenberg' ) }
                                        checked={ muted }
                                        onChange={ () => setAttributes( { muted: !muted } ) }
                                    />

                                    <ToggleControl
                                        label={ __( 'Playback Controls', 'advanced-gutenberg' ) }
                                        checked={ playback }
                                        onChange={ () => setAttributes( { playback: !playback } ) }
                                    />

                                    <ToggleControl
                                        label={ __( 'Play inline', 'advanced-gutenberg' ) }
                                        checked={ playsinline }
                                        onChange={ () => setAttributes( { playsinline: !playsinline } ) }
                                    />

                                    <SelectControl
                                        label={ __( 'Video preloading', 'advanced-gutenberg' ) }
                                        value={ preload }
                                        options={ [
                                            { label: __( 'Auto', 'advanced-gutenberg' ), value: 'auto' },
                                            { label: __( 'Metadata', 'advanced-gutenberg' ), value: 'metadata' },
                                            { label: __( 'None', 'advanced-gutenberg' ), value: 'none' },
                                        ] }
                                        onChange={ ( value ) => setAttributes( { preload: value } ) }
                                    />
                                </Fragment>
                                }

                                <ToggleControl
                                    label={ __( 'Full width', 'advanced-gutenberg' ) }
                                    checked={ videoFullWidth }
                                    onChange={ () => setAttributes( { videoFullWidth: !videoFullWidth } ) }
                                />
                                {!videoFullWidth &&
                                <RangeControl
                                    label={ __( 'Video width', 'advanced-gutenberg' ) }
                                    value={ videoWidth }
                                    min={ 100 }
                                    max={ 1000 }
                                    onChange={ (value) => setAttributes( { videoWidth: value } ) }
                                />
                                }
                                <RangeControl
                                    label={ __( 'Video height', 'advanced-gutenberg' ) }
                                    value={ videoHeight }
                                    min={ 300 }
                                    max={ 700 }
                                    onChange={ (value) => setAttributes( { videoHeight: value } ) }
                                />
                                {!!openInLightbox &&
                                <Fragment>
                                    <PanelColorSettings
                                        title={ __( 'Color Settings', 'advanced-gutenberg' ) }
                                        initialOpen={ false }
                                        colorSettings={ [
                                            {
                                                label: __( 'Overlay Color', 'advanced-gutenberg' ),
                                                value: overlayColor,
                                                onChange: ( value ) => setAttributes( { overlayColor: value === undefined ? '#EEEEEE' : value } ),
                                            },
                                            {
                                                label: __( 'Play Button Color', 'advanced-gutenberg' ),
                                                value: playButtonColor,
                                                onChange: ( value ) => setAttributes( { playButtonColor: value === undefined ? '#fff' : value } ),
                                            },
                                        ] }
                                    />
                                    <PanelBody title={ __( 'Play Button', 'advanced-gutenberg' ) }>
                                        <BaseControl label={ __( 'Icon Style', 'advanced-gutenberg' ) }>
                                            <div className="advgb-icon-items-wrapper">
                                                {Object.keys( PLAY_BUTTON_STYLE ).map( ( key, index ) => (
                                                    <div className="advgb-icon-item" key={ index }>
                                                    <span className={ key === playButtonIcon ? 'active' : '' }
                                                          onClick={ () => setAttributes( { playButtonIcon: key } ) }>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                            { PLAY_BUTTON_STYLE[key] }
                                                        </svg>
                                                    </span>
                                                    </div>
                                                ) ) }
                                            </div>
                                        </BaseControl>
                                        <RangeControl
                                            label={ __( 'Play Button Size', 'advanced-gutenberg' ) }
                                            value={ playButtonSize }
                                            min={ 40 }
                                            max={ 200 }
                                            onChange={ (value) => setAttributes( { playButtonSize: value } ) }
                                        />
                                    </PanelBody>
                                </Fragment>
                                }
                            </PanelBody>
                        </InspectorControls>
                        <div className={ blockClassName } id={ blockId }>
                            {!!openInLightbox &&
                            <div className={ videoWrapperClass } style={ { backgroundColor: overlayColor,  width: videoWidth } }>
                                <div className="advgb-video-poster" style={ { backgroundImage: `url(${poster})` } }/>
                                <div className="advgb-button-wrapper" style={ { height: videoHeight } }>
                                    {!poster &&
                                    <MediaUpload
                                        allowedTypes={ ["image"] }
                                        onSelect={ (media) => setAttributes( { poster: media.url, posterID: media.id } ) }
                                        value={ posterID }
                                        render={ ( { open } ) => (
                                            <Button
                                                className="button button-large"
                                                onClick={ open }
                                            >
                                                { __( 'Select image preview', 'advanced-gutenberg' ) }
                                            </Button>
                                        ) }
                                    />
                                    }
                                    <div className="advgb-play-button" style={ { color: playButtonColor } }>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             width={ playButtonSize }
                                             height={ playButtonSize }
                                             viewBox="0 0 24 24"
                                        >
                                            {PLAY_BUTTON_STYLE[playButtonIcon]}
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            }
                            {!openInLightbox && (
                                <div className={ videoWrapperClass }>
                                    {( (videoSourceType === 'youtube' || videoSourceType === 'vimeo') &&
                                        <iframe src={videoURL}
                                                frameBorder="0"
                                                allowFullScreen
                                                style={ { width: videoWidth, height: videoHeight } }
                                        />
                                    )
                                    || (videoSourceType === 'local' && (
                                        <Disabled>
                                            <video width={videoWidth}
                                                   height={videoHeight}
                                                   poster={poster}
                                                   controls={playback}
                                                   muted={muted}
                                            >
                                                <source src={videoURL}/>
                                                { 'Your browser does not support HTML5 video.' }
                                            </video>
                                        </Disabled>
                                    ))
                                    || !videoSourceType && <div style={ { width: videoWidth, height: videoHeight } } />}
                                </div>
                            ) }
                            {isSelected &&
                            <div className="advgb-video-input-block">
                                <div className="advgb-video-input">
                                    <Dashicon className="advgb-video-link-icon" icon="admin-links" />
                                    <TextControl
                                        placeholder={ __( 'Youtube/Vimeo video URL/ID…', 'advanced-gutenberg' ) }
                                        value={ videoID }
                                        onChange={ (value) => {
                                            setAttributes( { videoID: value, videoURL: '', videoTitle: undefined, videoSourceType: '' } );
                                        } }
                                    />
                                    <Button
                                        className="button button-large"
                                        disabled={ !videoID || videoSourceType === 'local' }
                                        style={ { height: '31px' } }
                                        onClick={ this.fetchVideoInfo }
                                    >
                                        { __( 'Fetch video content', 'advanced-gutenberg' ) }
                                    </Button>
                                    <MediaUpload
                                        allowedTypes={ ["video"] }
                                        value={ videoID }
                                        onSelect={ (video) => this.loadLocalVideo(video, blockId) }
                                        render={ ( { open } ) => (
                                            <Button
                                                className="button button-large is-primary"
                                                onClick={ open }
                                                style={ {marginLeft: '5px'} }
                                            >
                                                { __( 'Load local video', 'advanced-gutenberg' ) }
                                            </Button>
                                        ) }
                                    />
                                </div>
                                <div className="advgb-current-video-desc"
                                     style={ { minWidth: '50%', margin: '10px auto', textAlign: 'center' } }
                                >
                                    <strong>{ __( 'Current Video', 'advanced-gutenberg' ) }:</strong>
                                    <span title={videoSourceType}
                                          style={ {
                                              width: '25px',
                                              height: '25px',
                                              margin: '-1px 5px 0',
                                              display: 'flex',
                                              alignItems: 'center',
                                              justifyContent: 'center'
                                          } }
                                    >
                                    {videoHostIcon[videoSourceType] || ( this.state.fetching && <Spinner /> ) }
                                </span>
                                    <span>
                                    {
                                        ( videoTitle === 'ADVGB_FAIL_TO_LOAD' && <strong style={ { color: 'red' } }>{ __( 'Wrong video URL/ID. Please try another.', 'advanced-gutenberg' ) }</strong> )
                                        || videoTitle
                                        || __( 'Not selected yet.', 'advanced-gutenberg' )
                                    }
                                </span>
                                </div>
                            </div>
                            }
                        </div>
                    </Fragment>
            )
        }
    }

    const advVideoBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path d="M0 0h24v24H0z" fill="none"/>
            <path d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
        </svg>
    );
    const blockAttrs = {
        videoURL: {
            type: 'string',
        },
        videoID: {
            type: 'string',
        },
        videoSourceType: {
            type: 'string',
        },
        videoTitle: {
            type: 'string',
        },
        videoFullWidth: {
            type: 'boolean',
            default: true,
        },
        videoWidth: {
            type: 'number',
        },
        videoHeight: {
            type: 'number',
            default: 450,
        },
        playButtonIcon: {
            type: 'string',
            default: 'normal'
        },
        playButtonSize: {
            type: 'number',
            default: 80,
        },
        playButtonColor: {
            type: 'string',
            default: '#fff',
        },
        overlayColor: {
            type: 'string',
            default: '#EEEEEE',
        },
        poster: {
            type: 'string',
        },
        posterID: {
            type: 'number',
        },
        openInLightbox: {
            type: 'boolean',
            default: true,
        },
        changed: {
            type: 'boolean',
            default: false,
        },
        autoPlay: {
            type: 'boolean',
            default: false
        },
        loop: {
            type: 'boolean',
            default: false
        },
        muted: {
            type: 'boolean',
            default: false
        },
        playback: {
            type: 'boolean',
            default: true
        },
        playsinline: {
            type: 'boolean',
            default: true
        },
        preload: {
            type: 'string',
            default: 'metadata'
        },
        isPreview: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType( 'advgb/video', {
        title: __( 'Advanced Video', 'advanced-gutenberg' ),
        description: __( 'Powerful block for insert and embed video.', 'advanced-gutenberg' ),
        icon: {
            src: advVideoBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [
            __( 'video', 'advanced-gutenberg' ),
            __( 'embed', 'advanced-gutenberg' ),
            __( 'media', 'advanced-gutenberg' )
        ],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            anchor: true
        },
        edit: AdvVideo,
        save: function ( { attributes } ) {
            const {
                videoURL,
                videoSourceType,
                videoTitle,
                videoFullWidth,
                videoWidth,
                videoHeight,
                playButtonIcon,
                playButtonSize,
                playButtonColor,
                overlayColor,
                poster,
                openInLightbox,
                autoPlay,
                loop,
                muted,
                playback,
                playsinline,
                preload
            } = attributes;

            const blockClassName = [
                'advgb-video-block',
                !!videoFullWidth && 'full-width',
                !!openInLightbox && !!videoURL && 'advgb-video-lightbox',
            ].filter( Boolean ).join( ' ' );

            const videoWrapperClass = [
                'advgb-video-wrapper',
                !!videoFullWidth && 'full-width',
                !openInLightbox && 'no-lightbox',
            ].filter( Boolean ).join( ' ' );

            let videoAttributes = [];
            if (loop) videoAttributes.push( 'loop' );
            if (muted) videoAttributes.push( 'muted' );
            if (autoPlay) videoAttributes.push( 'autoplay' );
            if (playback) videoAttributes.push( 'controls' );
            if (playsinline) videoAttributes.push( 'playsinline' );

            return (
                <div className={ blockClassName }
                     data-video={ videoURL }
                     data-source={ videoSourceType }
                     data-video-attr={videoAttributes.join(',')}
                     data-video-preload={preload}
                >
                    {!openInLightbox && (
                        <div className={ videoWrapperClass }>
                            {( (videoSourceType === 'youtube' || videoSourceType === 'vimeo') &&
                                <iframe src={videoURL}
                                        width={videoWidth}
                                        height={videoHeight}
                                        frameBorder="0"
                                        allowFullScreen
                                />
                            )
                            || (videoSourceType === 'local' &&
                                <video className={ videoFullWidth && 'full-width' }
                                       width={videoWidth}
                                       height={videoHeight}
                                       poster={poster}
                                       controls={playback}
                                       loop={loop}
                                       muted={muted}
                                       autoPlay={autoPlay}
                                       preload={preload}
                                       playsInline={playsinline}
                                >
                                    <source src={videoURL}/>
                                    { 'Your browser does not support HTML5 video.' }
                                </video>
                            )
                            || !videoSourceType && <div style={ { width: videoWidth, height: videoHeight } } />}
                        </div>
                    ) }
                    {!!openInLightbox &&
                    <div className={ videoWrapperClass } style={ { backgroundColor: overlayColor, width: videoWidth } }>
                        <div className="advgb-video-poster" style={ { backgroundImage: `url(${poster})` } }/>
                        <div className="advgb-button-wrapper" style={ { height: videoHeight } }>
                            <div className="advgb-play-button" style={ { color: playButtonColor } }>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     width={ playButtonSize }
                                     height={ playButtonSize }
                                     viewBox="0 0 24 24"
                                >
                                    {PLAY_BUTTON_STYLE[playButtonIcon]}
                                </svg>
                            </div>
                        </div>
                    </div>
                    }
                </div>
            );
        },
        deprecated: [
            {
                attributes: blockAttrs,
                save: function ( { attributes } ) {
                    const {
                        videoURL,
                        videoSourceType,
                        videoTitle,
                        videoFullWidth,
                        videoWidth,
                        videoHeight,
                        playButtonIcon,
                        playButtonSize,
                        playButtonColor,
                        overlayColor,
                        poster,
                        openInLightbox,
                    } = attributes;

                    const blockClassName = [
                        'advgb-video-block',
                        !!videoFullWidth && 'full-width',
                        !!openInLightbox && !!videoURL && 'advgb-video-lightbox',
                    ].filter( Boolean ).join( ' ' );

                    const videoWrapperClass = [
                        'advgb-video-wrapper',
                        !!videoFullWidth && 'full-width',
                        !openInLightbox && 'no-lightbox',
                    ].filter( Boolean ).join( ' ' );

                    return (
                        <div className={ blockClassName }
                             data-video={ videoURL }
                             data-source={ videoSourceType }
                        >
                            {!openInLightbox && (
                                <div className={ videoWrapperClass }>
                                    {( (videoSourceType === 'youtube' || videoSourceType === 'vimeo') &&
                                        <iframe src={videoURL}
                                                width={videoWidth}
                                                height={videoHeight}
                                                frameBorder="0"
                                                allowFullScreen
                                        />
                                    )
                                    || (videoSourceType === 'local' &&
                                        <video className={ videoFullWidth && 'full-width' }
                                               width={videoWidth}
                                               height={videoHeight}
                                               poster={poster}
                                               controls
                                        >
                                            <source src={videoURL}/>
                                            { 'Your browser does not support HTML5 video.' }
                                        </video>
                                    )
                                    || !videoSourceType && <div style={ { width: videoWidth, height: videoHeight } } />}
                                </div>
                            ) }
                            {!!openInLightbox &&
                            <div className={ videoWrapperClass } style={ { backgroundColor: overlayColor, width: videoWidth } }>
                                <div className="advgb-video-poster" style={ { backgroundImage: `url(${poster})` } }/>
                                <div className="advgb-button-wrapper" style={ { height: videoHeight } }>
                                    <div className="advgb-play-button" style={ { color: playButtonColor } }>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             width={ playButtonSize }
                                             height={ playButtonSize }
                                             viewBox="0 0 24 24"
                                        >
                                            {PLAY_BUTTON_STYLE[playButtonIcon]}
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            }
                        </div>
                    );
                },
            }]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );