(function() {
    tinymce.create('tinymce.plugins.Multipage', {
        init : function(ed, url) {
			//ed.addShortcut( 'alt+shift+s', '', 'subpage' );

            ed.addButton('subpage', {
                tooltip : ed.getLang('multipage_tinymce_plugin.new_subpage'),
                cmd : 'subpage',
				//shortcut : 'Alt+Shift+S',
                image : ''
            });
            ed.addCommand('subpage', function() {
				ed.windowManager.open( {
                    title: ed.getLang('multipage_tinymce_plugin.enter_subpage_title'),
                    body: [
                        {
                            type: 'textbox',
                            name: 'title',
                            label: 'Title',
                            value: ''
                        }
                    ],
                    onsubmit: function( e ) {
						// Check for title length
						if (typeof e.data.title != 'undefined' && e.data.title.length)
							shortcode = '[nextpage title="' + e.data.title + '"]<br />';
                       
						ed.execCommand('mceInsertContent', 0, shortcode);
                    }
                });
            });
			// Replace Read More/Next Page tags with images
			ed.on( 'BeforeSetContent', function( e ) {
				if ( e.content ) {
					if ( e.content.indexOf( '[nextpage title=\"' ) !== -1 ) {
						e.content = e.content.replace( /\[nextpage title=\"(.*?)\"\]/g, function( match, subtitle ) {
							return '<img src="' + tinymce.Env.transparentSrc + '" data-wp-more="subpage" class="wp-more-tag mce-wp-subpage" ' +
								'title="' + subtitle + '" data-mce-resize="false" data-mce-placeholder="1" />';
						});
					}
				}
			});

			// Replace images with tags
			ed.on( 'PostProcess', function( e ) {
				if ( e.get ) {
					e.content = e.content.replace(/<img[^>]+>/g, function( image ) {
						var match, subtitle = '';

						if ( image.indexOf( 'data-wp-more="subpage"' ) !== -1 ) {
							if ( match = image.match( /title="([^"]+)"/ ) ) {
								subtitle = match[1];
							}

							image = '[nextpage title="' + subtitle + '"]';
						}

						return image;
					});
				}
			});
        },
        // ... Hidden code
    });
    // Register plugin
    tinymce.PluginManager.add( 'multipage', tinymce.plugins.Multipage );
})();