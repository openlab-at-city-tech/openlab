jQuery(document).ready(function($) {
	
	tinymce.create( 'tinymce.plugins.wpsdc_plugin', 
	{
		init : function( editor, url ) 
		{
			// register command for when button is clicked
			editor.addCommand( 'wpsdc_insert_shortcode', function() 
			{
				var selected = tinyMCE.activeEditor.selection.getContent();
				var content;

                if( selected !== ' ' || selected !== null ) {
                    // if text is selected when button is clicked, wrap shortcode around it
                    content =  '[dropcap]' + selected + '[/dropcap]';
                } else {
                	// add shortcode without the selected text
                	content = '[dropcap][/dropcap]';
                }

                tinymce.execCommand('mceInsertContent', false, content);
			});

			// register button, trigger above command if the button is clicked
			editor.addButton( 'wpsdc_button', 
			{
				title : 'Insert Drop Cap',
				cmd : 'wpsdc_insert_shortcode',
				icon : 'dropcap',
			});
		}
	});

	// Register TinyMCE plugin
	tinymce.PluginManager.add( 'wpsdc_button', tinymce.plugins.wpsdc_plugin);
});