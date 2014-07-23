/* global tinymce */
(function () {
	tinymce.create( 'tinymce.plugins.Subscribe2Plugin', {
		init : function ( ed, url ) {
			var i = 0,
			pb = '<p><img src="' + url + '/../include/spacer.gif" class="mceSubscribe2 mceItemNoResize" /></p>',
			cls = 'mceSubscribe2',
			shortcode = '[subscribe2]',
			pbreplaced = [],
			pbRE = new RegExp(/(\[|<!--)subscribe2.*?(\]|-->)/g),
			replacer = function ( str ) {
				if ( -1 !== str.indexOf( 'class="mceSubscribe2' ) ) {
					str = pbreplaced[i];
				}
				return str;
			};

			// Register commands
			ed.addCommand( 'mceSubscribe2', function () {
				ed.execCommand( 'mceInsertContent', 0, pb );
			});

			// Register buttons
			ed.addButton( 'subscribe2', {
				title : 'Insert Subscribe2 Token',
				image : url + '/../include/s2_button.png',
				cmd : cls
			});

			// load the CSS and enable it on the right class
			ed.on('init', function () {
				ed.dom.loadCSS( url + '/css/content.css' );

				if ( ed.theme.onResolveName ) {
					ed.theme.onResolveName.add(function ( th, o ) {
						if ( o.node.nodeName === 'IMG' && ed.dom.hasClass( o.node, cls ) ) {
							o.name = 'subscribe2';
						}
					});
				}
			});

			// create an array of replaced shortcodes so we have additional parameters
			// then swap in the graphic
			ed.on('BeforeSetContent', function ( ed ) {
				pbreplaced = ed.content.match( pbRE );
				ed.content = ed.content.replace( pbRE, pb );
			});

			// swap back the array of shortcodes to preserve parameters
			// replace any other instances with the default shortcode
			ed.on('PostProcess', function ( ed ) {
				if ( ed.get ) {
					if ( null !== pbreplaced ) {
						for ( i = 0; i < pbreplaced.length; i++ ) {
							ed.content = ed.content.replace(/<img[^>]+>/, replacer );
						}
					}
					ed.content = ed.content.replace( /<img[^>]+>/g, function ( im ) {
						if ( -1 !== im.indexOf( 'class="mceSubscribe2' ) ) {
							im = shortcode;
						}
						return im;
					});
				}
			});
		},

		getInfo : function () {
			return {
				longname : 'Insert Subscribe2 Token',
				author : 'Matthew Robinson',
				authorurl : 'http://subscribe2.wordpress.com',
				infourl : 'http://subscribe2.wordpress.com',
				version : tinymce.majorVersion + '.' + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add( 'subscribe2', tinymce.plugins.Subscribe2Plugin );
})();