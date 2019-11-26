(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, PanelColorSettings, MediaUpload } = wpBlockEditor;
    const { RangeControl, PanelBody, ToggleControl, BaseControl, TextControl, Button, IconButton, Dashicon, Spinner, Toolbar } = wpComponents;

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

        render() {
            const { isSelected, attributes, setAttributes } = this.props;
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
                <Fragment>
                    { ( (!!poster && openInLightbox) || ( !openInLightbox && videoSourceType === 'local' ) ) &&
                    <BlockControls>
                        <Toolbar>
                            <MediaUpload
                                allowedTypes={ ["image"] }
                                value={ posterID }
                                onSelect={ (image) => setAttributes( { poster: image.url, posterID: image.id } ) }
                                render={ ( { open } ) => (
                                    <IconButton
                                        className="components-toolbar__control"
                                        label={ __( 'Change image preview' ) }
                                        icon="edit"
                                        onClick={ open }
                                    />
                                ) }
                            />
                            <IconButton
                                className="components-toolbar__control"
                                label={ __( 'Remove image preview' ) }
                                icon="no"
                                onClick={ () => setAttributes( { poster: undefined, posterID: undefined } ) }
                            />
                        </Toolbar>
                    </BlockControls>
                    }
                    <InspectorControls>
                        <PanelBody title={ __( 'Advanced Video Settings' ) }>
                            <ToggleControl
                                label={ __( 'Open video in light box' ) }
                                help={ __( 'Lightbox offers additional display options.' ) }
                                checked={ openInLightbox }
                                onChange={ () => setAttributes( { openInLightbox: !openInLightbox } ) }
                            />
                            <ToggleControl
                                label={ __( 'Full width' ) }
                                checked={ videoFullWidth }
                                onChange={ () => setAttributes( { videoFullWidth: !videoFullWidth } ) }
                            />
                            {!videoFullWidth &&
                            <RangeControl
                                label={ __( 'Video width' ) }
                                value={ videoWidth }
                                min={ 100 }
                                max={ 1000 }
                                onChange={ (value) => setAttributes( { videoWidth: value } ) }
                            />
                            }
                            <RangeControl
                                label={ __( 'Video height' ) }
                                value={ videoHeight }
                                min={ 300 }
                                max={ 700 }
                                onChange={ (value) => setAttributes( { videoHeight: value } ) }
                            />
                            {!!openInLightbox &&
                            <Fragment>
                                <PanelColorSettings
                                    title={ __( 'Color Settings' ) }
                                    initialOpen={ false }
                                    colorSettings={ [
                                        {
                                            label: __( 'Overlay Color' ),
                                            value: overlayColor,
                                            onChange: ( value ) => setAttributes( { overlayColor: value === undefined ? '#EEEEEE' : value } ),
                                        },
                                        {
                                            label: __( 'Play Button Color' ),
                                            value: playButtonColor,
                                            onChange: ( value ) => setAttributes( { playButtonColor: value === undefined ? '#fff' : value } ),
                                        },
                                    ] }
                                />
                                <PanelBody title={ __( 'Play Button' ) }>
                                    <BaseControl label={ __( 'Icon Style' ) }>
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
                                        label={ __( 'Play Button Size' ) }
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
                    <div className={ blockClassName }>
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
                                            { __( 'Select image preview' ) }
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
                                || (videoSourceType === 'local' &&
                                    <video width={videoWidth}
                                           height={videoHeight}
                                           poster={poster}
                                           controls
                                    >
                                        <source src={videoURL}/>
                                        { __( 'Your browser does not support HTML5 video.' ) }
                                    </video>
                                )
                                || !videoSourceType && <div style={ { width: videoWidth, height: videoHeight } } />}
                            </div>
                        ) }
                        {isSelected &&
                        <div className="advgb-video-input-block">
                            <div className="advgb-video-input">
                                <Dashicon className="advgb-video-link-icon" icon="admin-links" />
                                <TextControl
                                    placeholder={ __( 'Youtube/Vimeo video URL/ID…' ) }
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
                                    { __( 'Fetch' ) }
                                </Button>
                                <span style={ { margin: 'auto 10px' } }>{ __( 'or use' ) }</span>
                                <MediaUpload
                                    allowedTypes={ ["video"] }
                                    value={ videoID }
                                    onSelect={ (video) => setAttributes( { videoURL: video.url, videoID: video.id, videoTitle: video.title, videoSourceType: 'local' } ) }
                                    render={ ( { open } ) => (
                                        <Button
                                            className="button button-large is-primary"
                                            onClick={ open }
                                        >
                                            { __( 'Local video' ) }
                                        </Button>
                                    ) }
                                />
                            </div>
                            <div className="advgb-current-video-desc"
                                 style={ { minWidth: '50%', margin: '10px auto', textAlign: 'center' } }
                            >
                                <strong>{ __( 'Current Video' ) }:</strong>
                                <span title={videoSourceType}
                                      style={ {
                                          width: '25px',
                                          height: '25px',
                                          display: 'inline-block',
                                          verticalAlign: 'text-bottom',
                                          margin: 'auto 7px' } }
                                >
                                    {videoHostIcon[videoSourceType] || ( this.state.fetching && <Spinner /> ) }
                                </span>
                                <span>
                                    {
                                        ( videoTitle === 'ADVGB_FAIL_TO_LOAD' && <strong style={ { color: 'red' } }>{ __( 'Wrong video URL/ID. Please try another.' ) }</strong> )
                                        || videoTitle
                                        || __( 'Not selected yet.' )
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
    };

    registerBlockType( 'advgb/video', {
        title: __( 'Advanced Video' ),
        description: __( 'Powerful block for insert and embed video.' ),
        icon: {
            src: advVideoBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'video' ), __( 'embed' ), __( 'media' ) ],
        attributes: blockAttrs,
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
                                    { __( 'Your browser does not support HTML5 video.' ) }
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
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );