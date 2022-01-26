// Version 1.0 - Initial version
// Version 1.0.1 - fix for useOnce deprecation, improve Transition from unsaved block and update 'edit' drop use of id
// Version 1.0.2 - fixed issue with transformation of text box size at default value
// Version 1.1 - eslinted and fixed bug in transformation of wrap attribute

( function( blocks, i18n, element, components, editor ) {
	var el              = element.createElement,
		TextControl     = components.TextControl,
		CheckboxControl = components.CheckboxControl,
		RadioControl    = components.RadioControl;

	function s2shortcode( props, control, newVal ) {
		var attributes = props.attributes || '';
		var hide       = '',
			id         = '',
			nojs       = '',
			antispam   = '',
			size       = '',
			wrap       = '',
			link       = '';

		// First we define the shortcode parameters from known Control values
		if ( 'subscribe' === attributes.hide ) {
			hide = ' hide="subscribe"';
		} else if ( 'unsubscribe' === attributes.hide ) {
			hide = ' hide="unsubscribe"';
		}
		if ( '' !== attributes.id && undefined !== attributes.id ) {
			id = ' id="' + attributes.id + '"';
		}
		if ( true === attributes.nojs ) {
			nojs = ' nojs="true"';
		}
		if ( true === attributes.antispam ) {
			antispam = ' antispam="true"';
		}
		if ( '' !== attributes.size && undefined !== attributes.size && '20' !== attributes.size ) {
			size = ' size="' + attributes.size + '"';
		}
		if ( true === attributes.wrap ) {
			wrap = ' wrap="false"';
		}
		if ( '' !== attributes.link && undefined !== attributes.link ) {
			link = ' link="' + attributes.link + '"';
		}

		// Second we amend parameter values based on recent input as values are asynchronous
		switch ( control ) {
			case 'hide':
				if ( 'none' === newVal ) {
					hide = '';
				} else if ( 'subscribe' === newVal ) {
					hide = ' hide="subscribe"';
				} else if ( 'unsubscribe' === newVal ) {
					hide = ' hide="unsubscribe"';
				}
				break;
			case 'id':
				if ( '' === newVal ) {
					id = '';
				} else {
					id = ' id="' + newVal + '"';
				}
				break;
			case 'nojs':
				if ( true === newVal ) {
					nojs = ' nojs="true"';
				} else if ( false === newVal ) {
					nojs = '';
				}
				break;
			case 'antispam':
				if ( true === newVal ) {
					antispam = ' antispam="true"';
				} else if ( false === newVal ) {
					antispam = '';
				}
				break;
			case 'size':
				if ( '20' === newVal ) {
					size = '';
				} else {
					size = ' size="' + newVal + '"';
				}
				break;
			case 'wrap':
				if ( true === newVal ) {
					wrap = ' wrap="false"';
				} else if ( false === newVal ) {
					wrap = '';
				}
				break;
			case 'link':
				if ( '' === newVal ) {
					link = '';
				} else {
					link = ' link="' + newVal + '"';
				}
				break;
			default:
				break;
		}

		// Now we construct and return our shortcode
		props.attributes.shortcode = '[subscribe2' + hide + id + nojs + antispam + size + wrap + link + ']';
		return props.attributes.shortcode;
	}

	blocks.registerBlockType(
		'subscribe2-html/shortcode',
		{
			title: i18n.__( 'Subscribe2 HTML', 'subscribe2' ),
			icon: 'email',
			category: 'widgets',
			keywords: [
				i18n.__( 'email', 'subscribe2' ),
				i18n.__( 'notification', 'subscribe2' )
			],
			supports: {
				customClassName: false,
				className: false,
				multiple: false,
				html: false
			},
			attributes: {
				shortcode: {
					type: 'text',
					selector: 'p'
				},
				hide: {
					type: 'string'
				},
				id: {
					type: 'string'
				},
				nojs: {
					type: 'boolean'
				},
				antispam: {
					type: 'boolean'
				},
				size: {
					type: 'number'
				},
				wrap: {
					type: 'boolean'
				},
				link: {
					type: 'string'
				}
			},
			transforms: {
				to: [
				{
					type: 'block',
					blocks: [ 'core/shortcode' ],
					transform: function( content ) {
						if ( undefined === content.shortcode || '' === content.shortcode ) {
							content.shortcode = '[subscribe2]';
						}
						return blocks.createBlock( 'core/shortcode', { text: content.shortcode } );
					}
				}
				],
				from: [
				{
					type: 'block',
					blocks: [ 'core/shortcode' ],
					transform: function( content ) {
						var shortcode, params, param, hide, id, nojs, antispam, size, wrap, link, i, l;
						if ( 'subscribe2' === content.text.substr( 1, 10 ) ) {
							shortcode = content.text;
							params    = content.text.replace( /^\[subscribe2|\]$/g, '' ).replace( /^\s+|\s+$/g, '' ).split( /['"]\s/g );
							l         = params.length;

							for ( i = 0; i < l; i++ ) {
								param = params[i].split( '=' );
								if ( 'hide' === param[0] ) {
									hide = param[1].replace( /['"]+/g, '' );
								}
								if ( 'id' === param[0] ) {
									id = param[1].replace( /['"]+/g, '' );
								}
								if ( 'nojs' === param[0] ) {
									nojs = 'true' === param[1].replace( /['"]+/g, '' );
								}
								if ( 'antispam' === param[0] ) {
									antispam = 'true' === param[1].replace( /['"]+/g, '' );
								}
								if ( 'size' === param[0] ) {
									size = param[1].replace( /['"]+/g, '' );
								}
								if ( 'wrap' === param[0] ) {
									wrap = 'false' === param[1].replace( /['"]+/g, '' );
								}
								if ( 'link' === param[0] ) {
									link = param[1].replace( /^['"]|['"]$/g, '' );
								}
							}

							return blocks.createBlock(
								'subscribe2-html/shortcode',
								{
									shortcode: shortcode,
									hide: hide,
									id: id,
									nojs: nojs,
									antispam: antispam,
									size: size,
									wrap: wrap,
									link: link
								}
							);
						}
					}
				},
				{
					type: 'shortcode',
					tag: 'subscribe2',
					attributes: {
						shortcode: {
							type: 'string',
							selector: 'p'
						},
						hide: {
							type: 'string',
							shortcode: function( content ) {
								return content.named.hide || 'none';
							}
						},
						id: {
							type: 'string',
							shortcode: function( content ) {
								return content.named.id || '';
							}
						},
						nojs: {
							type: 'boolean',
							shortcode: function( content ) {
								return content.named.nojs || false;
							}
						},
						antispam: {
							type: 'boolean',
							shortcode: function( content ) {
								return content.named.antispam || false;
							}
						},
						size: {
							type: 'number',
							shortcode: function( content ) {
								return content.named.size || '20';
							}
						},
						wrap: {
							type: 'boolean',
							shortcode: function( content ) {
								return content.named.wrap || false;
							}
						},
						link: {
							type: 'string',
							shortcode: function( content ) {
								return content.named.link || '';
							}
						}
					}
				}
				]
			},
			edit: function( props ) {
				var hide   = props.attributes.hide || 'none',
				id         = props.attributes.id || '',
				nojs       = props.attributes.nojs || false,
				antispam   = props.attributes.antispam || false,
				size       = props.attributes.size || '20',
				wrap       = props.attributes.wrap || false,
				link       = props.attributes.link || '',
				isSelected = props.isSelected;

				function onChangeHide( newHide ) {
					props.attributes.shortcode = s2shortcode( props, 'hide', newHide );
					props.setAttributes( { hide: newHide } );
				}
				function onChangeId( newId ) {
					props.attributes.shortcode = s2shortcode( props, 'id', newId );
					props.setAttributes( { id: newId } );
				}
				function onChangeNojs( newNojs ) {
					props.attributes.shortcode = s2shortcode( props, 'nojs', newNojs );
					props.setAttributes( { nojs: newNojs } );
				}
				function onChangeAntispam( newAntispam ) {
					props.attributes.shortcode = s2shortcode( props, 'antispam', newAntispam );
					props.setAttributes( { antispam: newAntispam } );
				}
				function onChangeSize( newSize ) {
					props.attributes.shortcode = s2shortcode( props, 'size', newSize );
					props.setAttributes( { size: newSize } );
				}
				function onChangeWrap( newWrap ) {
					props.attributes.shortcode = s2shortcode( props, 'wrap', newWrap );
					props.setAttributes( { wrap: newWrap } );
				}
				function onChangeLink( newLink ) {
					props.attributes.shortcode = s2shortcode( props, 'link', newLink );
					props.setAttributes( { link: newLink } );
				}

				return [
					isSelected && el(
						editor.InspectorControls,
						{ key: 'subscribe2-html/inspector' },
						el( 'h3', {}, i18n.__( 'Subscribe2 Shortcode Parameters', 'subscribe2' ) ),
						el(
							RadioControl,
							{
								label: i18n.__( 'Button Display Options', 'subscribe2' ),
								selected: hide,
								onChange: onChangeHide,
								options: [
									{ value: 'none', label: i18n.__( 'Show Both Buttons', 'subscribe2' ) },
									{ value: 'subscribe', label: i18n.__( 'Hide Subscribe Button', 'subscribe2' ) },
									{ value: 'unsubscribe', label: i18n.__( 'Hide Unsubscribe Button', 'subscribe2' ) }
								]
							}
						),
						el(
							TextControl,
							{
								type: 'number',
								label: i18n.__( 'Page ID', 'subscribe2' ),
								value: id,
								onChange: onChangeId
								}
						),
						el(
							CheckboxControl,
							{
								label: i18n.__( 'Disable Javascript', 'subscribe2' ),
								checked: nojs,
								onChange: onChangeNojs
								}
						),
						el(
							CheckboxControl,
							{
								label: i18n.__( 'Disable Simple Anti-Spam Measures', 'subscribe2' ),
								checked: antispam,
								onChange: onChangeAntispam
								}
						),
						el(
							TextControl,
							{
								type: 'number',
								label: i18n.__( 'Textbox size', 'subscribe2' ),
								value: size,
								onChange: onChangeSize
								}
						),
						el(
							CheckboxControl,
							{
								label: i18n.__( 'Disable wrapping of form buttons', 'subscribe2' ),
								checked: wrap,
								onChange: onChangeWrap
								}
						),
						el(
							TextControl,
							{
								type: 'string',
								label: i18n.__( 'Link Text', 'subscribe2' ),
								value: link,
								onChange: onChangeLink
								}
						)
					),
					el(
						'div',
						{
							key: 'subscribe2-html/block',
							style: { backgroundColor: '#ff0', color: '#000', padding: '2px', 'textAlign': 'center' }
							},
						i18n.__( 'Subscribe2 HTML Shortcode', 'subscribe2' )
					)
				];
			},
			save: function( props ) {
				return el( element.RawHTML, null, '<p>' + s2shortcode(props) + '</p>' );
			}
		}
	);
} (
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element,
	window.wp.components,
	window.wp.editor
) );
