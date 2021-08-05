( function( blocks, i18n, element, _ ) {
	var el = element.createElement,
		Component = element.Component,
		renderOutput;

	// https://stackoverflow.com/a/21976486
	isTrue = function( value ){
		if (typeof(value) === 'string'){
			value = value.trim().toLowerCase();
		}
		switch(value){
			case true:
			case "true":
			case 1:
			case "1":
			case "on":
			case "yes":
				return true;
			default:
				return false;
		}
	}

	validateLink = function( link ) {
		if ( ! link ) {
			return false;
		}

		if ( -1 === link.indexOf( '://docs.google.com' ) && -1 === link.indexOf( '://drive.google.com' ) ) {
			return false;
		}

		if ( link.length < 60 ) {
			return false;
		}

		return link;
	}

	getType = function( attr ) {
		var link = validateLink( attr.link ),
			type = '';

		if ( ! link ) {
			return false;
		}

		if ( -1 !== link.indexOf( '/document/' ) ) {
			type = 'doc';
		} else if ( -1 !== link.indexOf( '/presentation/' ) || -1 !== link.indexOf( '/present/' ) ) {
			type = 'presentation';
		} else if ( -1 !== link.indexOf( '/forms/' ) || -1 !== link.indexOf( 'form?formkey' ) ) {
			type = 'form';
		} else if ( -1 !== link.indexOf( '/spreadsheets/' ) || -1 !== link.indexOf( '/spreadsheet/' ) ) {
			type = 'spreadsheet';
		} else if ( attr.hasOwnProperty( 'type' ) && attr.type ) {
			type = attr.type;
		} else {
			type = 'other';
		}

		return type;
	}

	renderOutput = function( attr ) {
		var link = validateLink( attr.link ),
			invalid = false,
			width = '100%',
			height = '300',
			id = '',
			type = '',
			base = '',
			dl = '',
			output = [];

		if ( ! link ) {
			return '';
		}

		if ( attr.hasOwnProperty( 'width' ) && attr.width ) {
			width = attr.width;
		}

		if ( attr.hasOwnProperty( 'height' ) && attr.height ) {
			height = attr.height;
		}

		type = getType( attr );

		// add query args depending on doc type
		switch ( type ) {
			case 'doc' :
				base = 'document';
				if ( -1 !== link.indexOf( '/pub' ) && attr.hasOwnProperty( 'seamless' ) && isTrue( attr.seamless ) ) {
					if ( -1 !== link.indexOf( '?' ) ) {
						link += '&';
					} else {
						link += '?';
					}
					link += 'embedded=true';
				}

				break;

			case 'presentation' :
				var is_old_doc = false,
					size;

				if ( -1 !== link.indexOf( '/present/' ) || -1 !== link.indexOf( '?id=' ) ) {
					is_old_doc = true;
				}

				base = type;

				// alter the link so we're in embed mode (older docs)
				link = link.replace( '/view', '/embed' );

				// alter the link so we're in embed mode
				link = link.replace( 'pub?', 'embed?' );

				if ( attr.hasOwnProperty( 'size' ) && attr.size ) {
					size = attr.size;
				}

				// dimensions
				switch ( size ) {
					case 'medium' :
						width = 960;

						if ( is_old_doc ) {
							height = 749;
						} else {
							height = 559;
						}

						break;

					case 'large' :
						width = 1440;

						if ( is_old_doc ) {
							height = 1109;
						} else {
							height = 839;
						}

						break;

					case 'small' :
					default :
						width = 480;

						if ( is_old_doc ) {
							height = 389;
						} else {
							height = 299;
						}

						break;
				}

				break;

			case 'form' :
				// new form format
				if ( -1 !== link.indexOf( '/forms/' ) ) {
					if ( -1 !== link.indexOf( '?' ) ) {
						link += '&';
					} else {
						link += '?';
					}
					link += 'embedded=true';

				// older form format
				} else {
					link = link.replace( 'viewform?', 'embeddedform?' );
				}

				break;

			case 'spreadsheet' :
				base = 'spreadsheets';

				if ( -1 !== link.indexOf( '?' ) ) {
					link += '&';
				} else {
					link += '?';
				}
				link += 'widget=true';

				break;

			// http://webapps.stackexchange.com/a/84399
			case 'audio' :
			case 'other' :
				id = link.replace( 'https://drive.google.com/file/d/', '' );
				id = id.replace( '/view?usp=sharing', '' );

				link = dl = "https://docs.google.com/uc?export=open&id=" + id;
				break;
		}

		// set up link info
		if ( attr.hasOwnProperty( 'downloadlink' ) && isTrue( attr.downloadlink ) ) {
			switch ( type ) {
				case 'doc' :
				case 'presentation' :
				case 'spreadsheet' :
					id = link.replace( "https://docs.google.com/" + base + "/d/", '' );
					id = id.substring( 0, id.lastIndexOf( '/' ) );

					// ugh... URL formats are different!
					switch ( type ) {
						case 'doc' :
							dl = "https://docs.google.com/feeds/download/documents/export/Export?id=" + id + "&exportFormat=docx";
							break;
						case 'presentation' :
							dl = "https://docs.google.com/feeds/download/presentations/Export?id=" + id + "&exportFormat=pptx";
							break;
						case 'spreadsheet' :
							dl = "https://docs.google.com/spreadsheets/export?id=" + id + "&exportFormat=xlsx";
							break;
					}
					break;

				case 'audio' :
				case 'other' :
					dl = dl.replace( 'open', 'download' );
					break;
			}
		}

		// support "anyone with link" functionality
		if ( -1 !== link.indexOf( '/edit?usp=sharing' ) ) {
			link = link.replace( '/edit?usp=sharing', '/preview' );
			link = link.replace( '&widget=true', '' );
		} else if ( -1 !== link.indexOf( '/view?usp=sharing' ) ) {
			link = link.replace( '/view?usp=sharing', '/preview' );
		}

		// @todo do a better job here.
		if ( invalid ) {
			return '';

		// embed time!
		} else {
			// audio uses HTML5
			if ( 'audio' === type ) {
				output.push( el( 'audio', {
						key: 'gdoc-' + id,
						controls: true,
					},
						el( 'source', {
							src: link
						} ),
						el( 'p', {}, i18n.__( 'Your browser does not support HTML5 audio' ) )
				) );

			// Use iframe if we're not hiding it.
			} else if ( ! attr.hasOwnProperty( 'hideiframe' ) || false === isTrue( attr.hideiframe ) ) {
				output.push( el( 'iframe', {
					key: 'gdoc-' + id,
					className: 'gdocs_shortcode gdocs_' + type,
					src: link,
					width: width,
					height: height,
					marginWidth: 0,
					marginHeight: 0,
					frameBorder: 0,
					allowFullScreen: ''
				} ) );
			}


			// add download link if enabled
			if ( attr.hasOwnProperty( 'downloadlink' ) && isTrue( attr.downloadlink ) && 'form' !== type ) {
				output.push( el( 'p', {
						key: 'gdoc-download-' + id,
						className: 'gdoc-download gdoc-type-' + type,
					},
						el( 'span', {
							className: 'dashicons dashicons-download'
						} ),
						el( 'a', {
							href: dl
						}, i18n.__( 'Download' ) )
				) );
			}

			return output;
		}
	};

	i18n.setLocaleData( { '': {} }, 'gdrive' );

	blocks.registerBlockType( 'ray/google-drive', {
		title: i18n.__( 'Google Drive' ),
		icon:{foreground:"#555D66",src:Object(el)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},Object(el)("g",null,Object(el)("path",{d:"M4.433 22.396l4-6.929H24l-4 6.929H4.433zm3.566-6.929l-3.998 6.929L0 15.467 7.785 1.98l3.999 6.931-3.785 6.556zm15.784-.375h-7.999L7.999 1.605h8.002l7.785 13.486h-.003z"})))},
		category: 'embed',
		attributes: {
			link: {
				type: 'string'
			},
			height: {
				type: 'number'
			},
			width: {
				type: 'number'
			},
			downloadlink: {
				type: 'number'
			},
			type: {
				type: 'string'
			},
			seamless: {
				type: 'number'
			},
			size: {
				type: 'string'
			}
		},
		edit: function( props ) {
			var attr = props.attributes,
				sidebarControls,
				blockControls,
				extraField = '',
				downloadField = '',
				id, type;

			// Link added.
			if ( attr.link ) {
				id = attr.link;
				id = id.replace('://', '').replace(/\./g,'-').replace(/\//g,'-').replace(/&/g,'-');
				id = id.replace('https','').replace('http','').replace(/\?/g,'-').replace(/=/g,'-');

				type = getType( attr );

				if ( 'presentation' === type ) {
					extraField = el( wp.components.SelectControl, {
						className: 'components-panel__body is-opened gdrive-sidebar-item',
						label: i18n.__( 'Size' ),
						value: attr.size,
						options: [
							{ value: 'small',  label: i18n.__( 'Small - 480 x 299' ) },
							{ value: 'medium', label: i18n.__( 'Medium - 960 x 559' ) },
							{ value: 'large',  label: i18n.__( 'Large - 1440 x 839' ) },
						],
						onChange: function( newVal ) {
							props.setAttributes({
								size: newVal
							});
						},
					} );

				} else if ( 'doc' === type ) {
					extraField = el( wp.components.CheckboxControl, {
						className: 'components-panel__body is-opened gdrive-sidebar-item',
						label: i18n.__( 'Show Doc Header/Footer' ),
						checked: 1 === attr.seamless ? false : true,
						onChange: function( newVal ) {
							props.setAttributes({
								seamless: true === newVal ? 0 : 1
							});
						},
					} );
				} else if ( 'audio' === type || 'other' === type ) {
					extraField = el( wp.components.SelectControl, {
						className: 'components-panel__body is-opened gdrive-sidebar-item',
						label: i18n.__( 'Type (non-Google Doc only)' ),
						value: attr.type,
						options: [
							{ value: 'other', label: i18n.__( 'Other (Image, PDF, Microsoft Office, etc.)' ) },
							{ value: 'audio', label: i18n.__( 'Audio' ) },
						],
						onChange: function( newVal ) {
							props.setAttributes({
								type: newVal
							});
						},
					} );

				}

				if ( 'form' !== type ) {
					downloadField = el( wp.components.CheckboxControl, {
						label: i18n.__( 'Add Download Link?' ),
						className: 'components-panel__body is-opened gdrive-sidebar-item',
						checked: isTrue( attr.downloadlink ) ? true : false,
						onChange: function( newVal ) {
							props.setAttributes({
								downloadlink: true === newVal ? 1 : 0
							});
						},
					} );
				}

				// Sidebar controls.
				sidebarControls = el( wp.blockEditor.InspectorControls, { key: 'gdrive-controls-' + id },
					// Dimensions.
					el( 'div', {
						className: 'gdrive-sidebar-dimensions block-library-image__dimensions components-panel__body is-opened block-editor-image-size-control',
					},
						el( 'p', {
							className: 'block-library-image__dimensions__row gdrive-sidebar-dimensions-label'
						}, i18n.__( 'Dimensions' ) ),
						el( 'div', {
							className: 'block-library-image__dimensions__row block-editor-image-size-control__row',
						},
							el( wp.components.TextControl, {
								className: 'block-library-image__dimensions__width components-base-control block-editor-image-size-control__width',
								label: i18n.__( 'Width' ),
								value: attr.width,
								onChange: function( newVal ) {
									props.setAttributes({
										width: newVal.replace(/\D+/,'') * 1
									});
								},
					                } ),
							el( wp.components.TextControl, {
								className: 'block-library-image__dimensions__height components-base-control block-editor-image-size-control__height',
								label: i18n.__( 'Height' ),
								value: attr.height,
								onChange: function( newVal ) {
									props.setAttributes({
										height: newVal.replace(/\D+/,'') * 1
									});
								},
							} ),
						)
					),
					extraField,
					downloadField
				);

				// Block controls.
				blockControls = el( wp.blockEditor.BlockControls, { key: 'gdrive-block-controls-' + id,
					controls: [{
						icon: 'trash',
						title: i18n.__( 'Reset' ),
						onClick: function( event ) {
							// Reset variables to start fresh.
							props.setAttributes({
								link: '',
								type: '',
								width: '',
								height: '',
								size: '',
								seamless: '',
								downloadlink: ''
							});
						},
					}]
				} );

				return [
					renderOutput( attr ),
					sidebarControls,
					blockControls
				];
			}

			// No item selected.
			return (
				el( 'div', { className: props.className + ' is-large components-placeholder' },
					el( 'div', {
						className: 'components-placeholder__label',
					}, i18n.__( 'Google Drive' ) ),
					el( 'div', {
						className: 'components-placeholder__instructions',
					}, i18n.__( 'Enter a Google Drive link:' ) ),
					wp.element.createElement( GDriveURLInput, {
						//className: props.className,
						value: props.attributes.link,
						onChange: function( url ) {
							var settings = {
								link: url,
								width: rayGDriveProps.defaultWidth,
								height: rayGDriveProps.defaultHeight
							};

							// Set some defaults for specific types.
							if ( 'presentation' === getType( settings ) ) {
								settings.width = '';
								settings.height = '';
							}
							if ( 'doc' === getType( settings ) ) {
								settings.seamless = 1;
							}
							if ( 'form' === getType( settings ) ) {
								settings.height = 500;
							}

							props.setAttributes( settings );
						}
					} ),
					el( 'div', { className: 'components-placeholder__learn-more components-placeholder__fieldset' },
						el( wp.components.ExternalLink, {
							href: rayGDriveProps.helpUrl,
							children: i18n.__( 'Find out how to find your Google Drive link' )
						})
					)
				)
			);
		},
		save: function( props ) {
			return renderOutput( props.attributes );
		},
	} );

	// Custom URLInput component
	// Inspired by wp.editor.URLInput and https://gist.github.com/krambertech/76afec49d7508e89e028fce14894724c
	class GDriveURLInput extends Component {
		constructor(props) {
			super( props );

			this.onChange = this.onChange.bind( this );
			this.onKeyDown = this.onKeyDown.bind( this );

			this.state = {
				value: props.value
			};
		}

		componentDidMount() {
			this.timer = null;
		}

		componentWillUnmount() {
			clearTimeout(this.timer);
		}

		onChange( event ) {
			const inputValue = event.target.value;

			clearTimeout(this.timer);

			this.setState({ value: inputValue });

			this.timer = setTimeout(function() {
				if ( validateLink( inputValue ) ) {
					this.triggerChange();
				}
			}.bind(this), 2000);
		}

		onKeyDown(e) {
			// 13 = Enter
			if (e.keyCode === 13) {
				clearTimeout(this.timer);
				this.triggerChange();
			}
		}

		triggerChange() {
			const { value } = this.state;

			this.props.onChange(value);
		}

		render() {
			return (
				el( 'div', { className: 'components-placeholder__fieldset' },
					el( 'input', {
						type: 'url',
						'aria-label': i18n.__( 'URL' ),
						required: '',
						value: this.state.value,
						onChange: this.onChange,
						placeholder: i18n.__( 'Type or Paste URL. Hit Enter to submit.' ),
						className: 'components-placeholder__input',
						onKeyDown: this.onKeyDown
					})
				)
			);
		}
	}

} )(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element,
	window._,
);
