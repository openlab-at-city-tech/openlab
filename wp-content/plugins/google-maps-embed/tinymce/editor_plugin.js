// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('cets_EmbedGmaps');
	
	tinymce.create('tinymce.plugins.cets_EmbedGmaps', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			
			
			// Register example button
			ed.addButton('cets_EmbedGmaps', {
				title : 'Embed a Google Map',
				image : url + '/cets_EmbedGmaps.png',
				onclick : function() {
					cets_GEButtonClick('cetsEmbedGmap');
				}
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('cets_EmbedGmaps', n.nodeName == 'IMG');
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
					longname  : 'cets_EmbedGmaps',
					author 	  : 'Deanna Schneider',
					authorurl : 'http://deannaschneider.wordpress.com',
					infourl   : 'http://deannaschneider.wordpress.com',
					version   : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('cets_EmbedGmaps', tinymce.plugins.cets_EmbedGmaps);
})();


